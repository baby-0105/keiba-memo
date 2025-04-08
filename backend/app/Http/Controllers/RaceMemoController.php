<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RaceMemoController extends Controller
{
    public function index(Request $request)
    {
        $raceName = '大阪杯';
        $start_at = '2025-04-06 15:40:00';

        $race = DB::table('races')->where('name', $raceName)->where('start_at', $start_at)->first();
        if (!$race) {
            return response()->json([]);
        }

        // 出走馬
        $horses = DB::table('horse_races')
            ->join('horses', 'horse_races.horse_id', '=', 'horses.id')
            ->where('horse_races.race_id', $race->id)
            ->select('horses.id', 'horses.name')
            ->get();

        $result = [];

        foreach ($horses as $horse) {
            // 各馬に紐ずく過去レースを取得
            $pastRaces = DB::table('horse_races')
                ->join('races', 'horse_races.race_id', '=', 'races.id')
                ->leftJoin('horse_races_memos', function ($join) use ($horse) {
                    $join->on('horse_races_memos.horse_id', '=', 'horse_races.horse_id')
                        ->on('horse_races_memos.race_id', '=', 'horse_races.race_id');
                })
                ->where('horse_races.horse_id', $horse->id)
                ->select('races.id', 'races.name', 'races.start_at', 'horse_races_memos.memo')
                ->orderByDesc('races.start_at')
                ->get();

            $result[] = [
                'id' => $horse->id,
                'name' => $horse->name,
                'past_races' => $pastRaces,
            ];
        }

        return response()->json($result);
    }

    public function store(Request $request)
    {
        DB::table('horse_races_memos')->updateOrInsert(
            [
                'horse_id' => $request->input('horse_id'),
                'race_id' => $request->input('race_id'),
            ],
            [
                'memo' => $request->input('memo'),
                'updated_at' => now(),
                'created_at' => now(),
            ]
        );

        return response()->json(['message' => '保存しました']);
    }
}
