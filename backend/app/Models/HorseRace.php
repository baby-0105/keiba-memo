<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HorseRace extends Model
{
    protected $table = 'horse_races';
    protected $fillable = ['horse_id', 'race_id'];

    public function horse()
    {
        return $this->belongsTo(Horse::class);
    }

    public function race()
    {
        return $this->belongsTo(Race::class);
    }
}
