<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

// 馬ごとの過去レース
class HorseRacesSeeder extends Seeder
{
    public function run()
    {
        // 馬の一覧
        $horses = [
            'ボルドグフーシュ',
            'ホウオウビスケッツ',
            'ラヴェル',
            'ソールオリエンス',
            'ベラジオオペラ',
            'ジャスティンパレス',
            'ヨーホーレイク',
            'カラテ',
            'コスモキュランダ',
            'シックスペンス',
            'デシエルト',
            'ステレンボッシュ',
            'ロードデルレイ',
            'エコロヴァルツ',
            'アルナシーム',
        ];

        // 馬ごとのレース一覧（start_atはユニークにすること）
        $races = [
            'ボルドグフーシュ' => [
                ['name' => 'AJCC (GII)', 'start_at' => '2025-01-26 15:45:00'],
                ['name' => 'チャレンジC (GIII)', 'start_at' => '2024-11-30 15:35:00'],
                ['name' => '天皇賞（春）(GI)', 'start_at' => '2023-04-30 15:40:00'],
                ['name' => '阪神大賞典 (GII)', 'start_at' => '2023-03-19 15:35:00'],
                ['name' => '有馬記念 (GI)', 'start_at' => '2022-12-25 15:25:00'],
            ],
            'ホウオウビスケッツ' => [
                ['name' => '金鯱賞 (GII)', 'start_at' => '2025-03-16 15:35:00'],
                ['name' => '日刊中山金杯 (GIII)', 'start_at' => '2025-01-05 15:45:00'],
                ['name' => '天皇賞（秋）(GI)', 'start_at' => '2024-10-27 15:40:00'],
                ['name' => '毎日王冠 (GII)', 'start_at' => '2024-10-06 15:40:00'],
                ['name' => '函館記念 (GIII)', 'start_at' => '2024-07-14 15:25:00'],
            ],
            'ラヴェル' => [
                ['name' => '金鯱賞 (GII)', 'start_at' => '2025-03-16 15:35:00'],
                ['name' => 'チャレンジC (GIII)', 'start_at' => '2024-11-30 15:35:00'],
                ['name' => '秋華賞 (GI)', 'start_at' => '2024-10-13 15:40:00'],
                ['name' => 'エリザベス女王杯 (GI)', 'start_at' => '2024-11-10 15:40:00'],
                ['name' => 'オクトーバーS (L)', 'start_at' => '2024-10-14 15:45:00'],
            ],
            'ソールオリエンス' => [
                ['name' => '京都記念 (GII)', 'start_at' => '2025-02-16 15:40:00'],
                ['name' => 'ジャパンカップ (GI)', 'start_at' => '2024-11-24 15:40:00'],
                ['name' => '天皇賞（秋）(GI)', 'start_at' => '2024-10-27 15:40:00'],
                ['name' => '宝塚記念 (GI)', 'start_at' => '2024-06-23 15:40:00'],
                ['name' => '大阪杯 (GI)', 'start_at' => '2024-03-31 15:40:00'],
            ],
            'ベラジオオペラ' => [
                ['name' => '大阪杯 (GI)', 'start_at' => '2024-03-31 15:40:00'],
                ['name' => '宝塚記念 (GI)', 'start_at' => '2024-06-23 15:40:00'],
                ['name' => '天皇賞（秋）(GI)', 'start_at' => '2024-10-27 15:40:00'],
                ['name' => 'ジャパンカップ (GI)', 'start_at' => '2024-11-24 15:40:00'],
                ['name' => '京都記念 (GII)', 'start_at' => '2025-02-16 15:40:00'],
            ],
            'ジャスティンパレス' => [
                ['name' => '有馬記念 (GI)', 'start_at' => '2024-12-22 15:25:00'],
                ['name' => '天皇賞（秋）(GI)', 'start_at' => '2024-10-27 15:40:00'],
                ['name' => 'ジャパンカップ (GI)', 'start_at' => '2024-11-24 15:40:00'],
                ['name' => '宝塚記念 (GI)', 'start_at' => '2024-06-23 15:40:00'],
                ['name' => '大阪杯 (GI)', 'start_at' => '2024-03-31 15:40:00'],
            ],
            'ヨーホーレイク' => [
                ['name' => '京都記念 (GII)', 'start_at' => '2025-02-16 15:40:00'],
                ['name' => '宝塚記念 (GI)', 'start_at' => '2024-06-23 15:40:00'],
                ['name' => '天皇賞（秋）(GI)', 'start_at' => '2024-10-27 15:40:00'],
                ['name' => '大阪杯 (GI)', 'start_at' => '2024-03-31 15:40:00'],
                ['name' => '日経新春杯 (GII)', 'start_at' => '2024-01-19 15:35:00'],
            ],
            'カラテ' => [
                ['name' => '中山記念 (GII)', 'start_at' => '2025-03-02 15:45:00'],
                ['name' => 'AJCC (GII)', 'start_at' => '2025-01-26 15:45:00'],
                ['name' => '日刊中山金杯 (GIII)', 'start_at' => '2025-01-05 15:45:00'],
                ['name' => 'ジャパンカップ (GI)', 'start_at' => '2024-11-24 15:40:00'],
                ['name' => '毎日王冠 (GII)', 'start_at' => '2024-10-06 15:40:00'],
            ],
            'コスモキュランダ' => [
                ['name' => 'AJCC (GII)', 'start_at' => '2025-01-26 15:45:00'],
                ['name' => '中日新聞杯 (GIII)', 'start_at' => '2024-12-07 15:45:00'],
                ['name' => '菊花賞 (GI)', 'start_at' => '2024-10-20 15:40:00'],
                ['name' => 'セントライト記念 (GII)', 'start_at' => '2024-09-16 15:45:00'],
                ['name' => '東京優駿 (GI)', 'start_at' => '2024-05-26 15:40:00'],
            ],
            'シックスペンス' => [
                ['name' => '中山記念 (GII)', 'start_at' => '2025-03-02 15:45:00'],
                ['name' => '毎日王冠 (GII)', 'start_at' => '2024-10-06 15:40:00'],
                ['name' => 'プリンシパルS (OP)', 'start_at' => '2024-05-11 15:45:00'],
                ['name' => 'スプリングS (GII)', 'start_at' => '2024-03-17 15:45:00'],
                ['name' => 'グリーンC (1勝)', 'start_at' => '2024-02-10 15:45:00'],
            ],
            'デシエルト' => [
                ['name' => '京都記念 (GII)', 'start_at' => '2025-01-06 15:40:00'],
                ['name' => '中日新聞杯 (GIII)', 'start_at' => '2024-12-07 15:45:00'],
                ['name' => '中山金杯 (GIII)', 'start_at' => '2024-01-05 15:45:00'],
                ['name' => 'チャレンジC (GIII)', 'start_at' => '2023-12-02 15:35:00'],
                ['name' => 'エプソムC (GIII)', 'start_at' => '2023-06-11 15:45:00'],
            ],
            'ステレンボッシュ' => [
                ['name' => '桜花賞 (GI)', 'start_at' => '2024-04-07 15:40:00'],
                ['name' => '秋華賞 (GI)', 'start_at' => '2024-10-13 15:40:00'],
                ['name' => '香港ヴァーズ (GI)', 'start_at' => '2024-12-08 15:30:00'],
                ['name' => '紫苑S (GIII)', 'start_at' => '2024-09-07 15:45:00'],
                ['name' => 'オークス (GI)', 'start_at' => '2024-05-19 15:40:00'],
            ],
            'ロードデルレイ' => [
                ['name' => '日経新春杯 (GII)', 'start_at' => '2025-01-19 15:35:00'],
                ['name' => '中日新聞杯 (GIII)', 'start_at' => '2024-12-07 15:45:00'],
                ['name' => 'アンドロメダS (L)', 'start_at' => '2024-11-16 15:40:00'],
                ['name' => '鳴尾記念 (GIII)', 'start_at' => '2024-06-01 15:35:00'],
                ['name' => '白富士S (L)', 'start_at' => '2024-01-27 15:45:00'],
            ],
            'エコロヴァルツ' => [
                ['name' => '中山記念 (GII)', 'start_at' => '2025-03-02 15:45:00'],
                ['name' => 'ディセンバーS (L)', 'start_at' => '2024-12-15 15:40:00'],
                ['name' => '菊花賞 (GI)', 'start_at' => '2024-10-20 15:40:00'],
                ['name' => 'セントライト記念 (GII)', 'start_at' => '2024-09-16 15:45:00'],
                ['name' => '東京優駿 (GI)', 'start_at' => '2024-05-26 15:40:00'],
            ],
            'アルナシーム' => [
                ['name' => '中山記念 (GII)', 'start_at' => '2025-03-02 15:45:00'],
                ['name' => '日刊中山金杯 (GIII)', 'start_at' => '2025-01-05 15:45:00'],
                ['name' => 'マイルCS (GI)', 'start_at' => '2024-11-17 15:40:00'],
                ['name' => '富士S (GII)', 'start_at' => '2024-10-19 15:40:00'],
                ['name' => '中京記念 (GIII)', 'start_at' => '2024-07-21 15:35:00'],
            ],
        ];

        foreach ($horses as $horseName) {
            // 馬を取得し、なければ作成
            $horse = DB::table('horses')->where('name', $horseName)->first();
            if (!$horse) {
                $horseId = DB::table('horses')->insertGetId([
                    'name' => $horseName,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
                $horse = (object)['id' => $horseId];
            }

            foreach ($races[$horseName] as $raceData) {
                // レースの取得または作成（start_atで一意に判定）
                $race = DB::table('races')->where('start_at', $raceData['start_at'])->first();
                if (!$race) {
                    $raceId = DB::table('races')->insertGetId([
                        'name' => $raceData['name'],
                        'start_at' => $raceData['start_at'],
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                } else {
                    $raceId = $race->id;
                }

                // 出走情報の登録（重複防止）
                DB::table('horse_races')->insertOrIgnore([
                    'horse_id' => $horse->id,
                    'race_id' => $raceId,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }
}
