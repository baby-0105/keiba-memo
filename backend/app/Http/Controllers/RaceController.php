<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use GuzzleHttp\Client;
use Symfony\Component\DomCrawler\Crawler;
use App\Models\Race;
use App\Models\Horse;
use App\Models\RacetrackMaster;
use App\Models\HorseRace;

class RaceController extends Controller
{
    /**
     * レース一覧
     *
     * @return void
     */
    public function index()
    {
        $races = DB::table('races')
            ->select('id', 'name')
            ->where('is_display_to_index', true)
            ->get();

        return view('races.index', compact('races'));
    }

    /**
     * レース毎の出馬表
     *
     * @param int $id レースID
     * @return void
     */
    public function horsesByRace($id)
    {
        $race = DB::table('races')->where('id', $id)->first();

        if (!$race) {
            abort(404);
        }

        // 出走馬
        $horses = DB::table('horse_races')
            ->join('horses', 'horse_races.horse_id', '=', 'horses.id')
            ->where('horse_races.race_id', $race->id)
            ->select('horses.id', 'horses.name')
            ->get();

        $postRacesByHorses = [];

        $horseMemos = DB::table('horse_memos')->pluck('memo', 'horse_id');

        foreach ($horses as $horse) {
            // 各馬に紐ずく過去レースを取得
            $pastRaces = DB::table('horse_races')
                ->join('races', 'horse_races.race_id', '=', 'races.id')
                ->leftJoin('horse_races_memos', function ($join) use ($horse) {
                    $join->on('horse_races_memos.horse_id', '=', 'horse_races.horse_id')
                        ->on('horse_races_memos.race_id', '=', 'horse_races.race_id');
                })
                ->where('horse_races.horse_id', $horse->id)
                ->select('races.id', 'races.name', 'horse_races_memos.memo')
                ->get();

            $postRacesByHorses[] = [
                'id' => $horse->id,
                'name' => $horse->name,
                'past_races' => $pastRaces,
                'horse_memo' => $horseMemos[$horse->id] ?? null,
                'is_confirmed' => $horseRaces[$horse->id] ?? true,
            ];
        }

        return view('races.horsesByRace', [
            'raceName' => $race->name,
            'postRacesByHorses' => $postRacesByHorses
        ]);
    }

    /**
     * レースメモ編集
     *
     * @param Request $request
     * @return void
     */
    public function updateMemo(Request $request)
    {
        DB::table('horse_races_memos')->updateOrInsert(
            [
                'horse_id' => $request->horse_id,
                'race_id' => $request->race_id,
            ],
            ['memo' => $request->memo, 'updated_at' => now(), 'created_at' => now()]
        );

        return redirect()->back()->with('message', 'メモを保存しました');
    }

    /**
     * 馬メモ編集
     *
     * @param Request $request
     * @return void
     */
    public function updateHorseMemo(Request $request)
    {
        DB::table('horse_memos')->updateOrInsert(
            ['horse_id' => $request->horse_id],
            ['memo' => $request->memo, 'updated_at' => now(), 'created_at' => now()]
        );

        return redirect()->back()->with('message', '馬メモを保存しました');
    }

    /**
     * netkeibaからスクレイピング
     *
     * @param Request $request
     * @return void
     */
    public function scraping(Request $request)
    {
        $client = new Client(['headers' => [
            'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64)',
        ]]);

        DB::beginTransaction();

        try {
            $raceId = $request->race_id;
            $url = 'https://race.netkeiba.com/race/shutuba_past.html?race_id=' . $raceId . '&rf=shutuba_submenu';

            $response = $client->request('GET', $url);
            $html = $response->getBody()->getContents();
            $crawler = new Crawler($html);

            // レース名の取得
            $raceName = $crawler->filter('.RaceName')->text();

            // レース番号の取得
            $raceNumberRaw = $crawler->filter('.RaceNum')->text();
            $raceNumber = (int) filter_var($raceNumberRaw, FILTER_SANITIZE_NUMBER_INT);

            // レース情報保存
            $mainRace = Race::firstOrCreate([
                'start_date' => now()->toDateString(),
                'race_num' => $raceNumber,
                'name' => $raceName,
                'racetrack_master_id' => 1
            ], [
                'is_display_to_index' => 1,
            ]);

            // 競馬場マスターをキャッシュ（DBアクセス回数削減）
            $trackCache = RacetrackMaster::pluck('id', 'name')->toArray();

            // 出走馬の情報取得
            $crawler->filter('.Shutuba_Table .HorseList')->each(function (Crawler $node) use ($client, $mainRace, $trackCache) {
                // ノードが見つからない場合はスキップ
                if ($node->filter('.Horse_Info a')->count() === 0) return;

                try {
                    // 馬名とリンク
                    $horseName = trim($node->filter('.Horse_Info a')->text());
                    $horseLink = $node->filter('.Horse_Info a')->attr('href');
                    $horseId = basename($horseLink);

                    // horses 登録
                    $horse = Horse::firstOrCreate(['name' => $horseName]);

                    // horse_races 登録（現レース）
                    HorseRace::firstOrCreate(
                        ['horse_id' => $horse->id, 'race_id' => $mainRace->id],
                        ['is_entry_confirmed' => 0]
                    );

                    // 馬の詳細ページへアクセス
                    $horseUrl = 'https://db.netkeiba.com/horse/' . $horseId . '/';
                    $response = $client->request('GET', $horseUrl);
                    $html = $response->getBody()->getContents();
                    $horseCrawler = new Crawler($html);

                    // 過去レース一覧を取得
                    $horseCrawler->filter('table.db_h_race_results tbody tr')->each(function (Crawler $tr) use ($trackCache, $horse) {
                        try {
                            $dateText = trim($tr->filter('td')->eq(0)->text());
                            $raceDate = date('Y-m-d', strtotime($dateText));

                            $placeText = trim($tr->filter('td')->eq(1)->text());
                            preg_match('/\d*([^\d]+)\d*/u', $placeText, $matches);
                            $racetrack = $matches[1] ?? null;

                            if (!$racetrack) return;

                            // マスターテーブルに存在しない競馬場は新規登録し、キャッシュに追加
                            if (!isset($trackCache[$racetrack])) {
                                $track = RacetrackMaster::create(['name' => $racetrack]);
                                $trackCache[$racetrack] = $track->id;
                            }

                            $trackId = $trackCache[$racetrack];
                            $raceNumRaw = trim($tr->filter('td')->eq(3)->text());
                            $raceNum = filter_var($raceNumRaw, FILTER_VALIDATE_INT);
                            if ($raceNum === false) {
                                // レース番号を取得できない場合は0
                                $raceNum = 0;
                            }
                            $raceName = trim($tr->filter('td')->eq(4)->text());

                            // 過去レース 登録
                            // ※レース番号が取得できない場合にのみレース名での比較も行う
                            if ($raceNum === 0) {
                                // race_num = 0 の場合は name を条件に含める
                                $race = Race::firstOrCreate([
                                    'start_date' => $raceDate,
                                    'race_num' => $raceNum,
                                    'racetrack_master_id' => $trackId,
                                    'name' => $raceName,
                                ]);
                            } else {
                                // 通常パターン（race_numあり）
                                $race = Race::firstOrCreate([
                                    'start_date' => $raceDate,
                                    'race_num' => $raceNum,
                                    'racetrack_master_id' => $trackId,
                                ], [
                                    'name' => $raceName,
                                ]);
                            }

                            // horse_races 登録（過去）
                            HorseRace::firstOrCreate([
                                'horse_id' => $horse->id,
                                'race_id' => $race->id,
                            ]);
                        } catch (\Exception $e) {
                            DB::rollBack();
                            throw $e;
                        }
                    });

                } catch (\Exception $e) {
                    DB::rollBack();
                    throw $e;
                }
            });

            DB::commit();
            return redirect()->route('races.index')->with('message', 'スクレイピングが完了しました');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("[Error]スクレイピング処理もしくはその後のDB保存時: {$e->getMessage()}");
            echo "処理失敗";
        }
    }
}
