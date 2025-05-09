<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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

    public function scraping()
    {
        $client = new Client();
        $url = 'https://www.keibalab.jp/db/race/202505100811/umabashira.html?kind=yoko';

        $response = $client->request('GET', $url, [
            'headers' => [
                'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64)',
            ]
        ]);
        $html = $response->getBody()->getContents();
        $crawler = new Crawler($html);

        // 対象レース情報取得
        $raceName = $crawler->filter('.raceTitle')->text();
        $raceDateText = $crawler->filter('.raceaboutbox .fL.ml10 p')->text();
        $startDate = $this->formatDateFromText($raceDateText);
        $racePlace = $crawler->filter('ul.tabNav3 a.active')->text();
        $raceNumberText = $crawler->filter('div.icoRacedata.fL')->text();
        $raceNum = rtrim($raceNumberText, 'R');

        // 競馬場マスタID取得
        $racetrack = RacetrackMaster::where('name', $racePlace)->first();
        if (!$racetrack) return;

        // racesテーブルに重複がないかチェック
        $mainRace = Race::firstOrCreate([
            'start_date' => $startDate,
            'racetrack_master_id' => $racetrack->id,
            'race_num' => $raceNum,
        ], [
            'name' => $raceName,
            'is_display_to_index' => 1,
        ]);

        // 馬情報と過去レース情報取得
        $crawler->filter('.yokobashiraTable tr')->each(function (Crawler $trNode) use ($mainRace) {
            $horseNameNode = $trNode->filter('.bameiBox a');
            if ($horseNameNode->count() === 0) return;

            $horseName = trim($horseNameNode->text());

            // horsesテーブルへ挿入
            $horse = Horse::firstOrCreate(['name' => $horseName]);

            // horse_racesテーブルへ馬毎に出走予定レース情報挿入
            HorseRace::firstOrCreate([
                'horse_id' => $horse->id,
                'race_id' => $mainRace->id,
                'is_entry_confirmed' => 0, // 出走未確定
            ]);

            $trNode->filter('.zensoudayrace.tL')->each(function (Crawler $node) use ($horse) {
                $text = trim($node->text());
                $raceName = $node->filter('span')->count() > 0
                    ? trim($node->filter('span')->text())
                    : '不明';

                if (preg_match('/\d*([^\s]+\d+\s+\d+\/\d+\/\d+)/u', $text, $match) &&
                    preg_match('/([^\d]+)(\d+)\s+(\d{1,2})\/(\d{1,2})\/(\d{1,2})/', $match[1], $parts)) {

                    $keibajo = $parts[1];
                    $raceNo = $parts[2];
                    $year = (int)$parts[3] + 2000;
                    $month = (int)$parts[4];
                    $day = (int)$parts[5];
                    $raceDate = sprintf('%04d%02d%02d', $year, $month, $day);

                    $track = RacetrackMaster::where('name', $keibajo)->first();
                    if (!$track) return;

                    // racesテーブルへ挿入（過去レース）
                    $race = Race::firstOrCreate([
                        'start_date' => $raceDate,
                        'racetrack_master_id' => $track->id,
                        'race_num' => $raceNo
                    ], [
                        'name' => $raceName
                    ]);

                    // horse_racesテーブルへ挿入
                    HorseRace::firstOrCreate([
                        'horse_id' => $horse->id,
                        'race_id' => $race->id
                    ]);
                }
            });
        });
    }

    /**
     * レース開催日（テキスト）を yyyymmdd 形式に変換
     */
    private function formatDateFromText(string $text): ?string
    {
        if (preg_match('/(\d{4})\/(\d{1,2})\/(\d{1,2})/', $text, $matches)) {
            return sprintf('%04d%02d%02d', $matches[1], $matches[2], $matches[3]);
        }
        return null;
    }
}
