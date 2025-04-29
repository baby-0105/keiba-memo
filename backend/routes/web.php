<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RaceController;

Route::get('/', function () {
    return view('app');
});

Route::get('/races', [RaceController::class, 'index'])->name('races.index');
Route::get('/races/{id}', [RaceController::class, 'horsesByRace'])->name('races.horsesByRace');
Route::get('/scraping', [RaceController::class, 'scraping'])->name('scraping');
Route::post('/memo/update', [RaceController::class, 'updateMemo'])->name('memo.update');
Route::post('/horse/memo/update', [RaceController::class, 'updateHorseMemo'])->name('horse.memo.update');
