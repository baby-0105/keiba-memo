<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

// 対象レースの出走馬
class RaceHorseSeeder extends Seeder
{
    public function run()
    {
        // レース情報
        $raceId = DB::table('races')->insertGetId([
            'name' => '大阪杯',
            'start_at' => '2025-04-06 15:40:00',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // 馬一覧
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
