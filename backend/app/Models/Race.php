<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Race extends Model
{
    protected $fillable = [
        'name',
        'start_date',
        'racetrack_master_id',
        'race_num',
    ];

    public function racetrackMaster()
    {
        return $this->belongsTo(RacetrackMaster::class);
    }

    public function horses()
    {
        return $this->belongsToMany(Horse::class, 'horse_races');
    }
}
