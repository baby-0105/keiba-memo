<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RacetrackMastersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('racetrack_masters')->insert([
            ['name' => '札幌'],
            ['name' => '函館'],
            ['name' => '福島'],
            ['name' => '新潟'],
            ['name' => '東京'],
            ['name' => '中山'],
            ['name' => '中京'],
            ['name' => '京都'],
            ['name' => '阪神'],
            ['name' => '小倉'],
        ]);
    }
}
