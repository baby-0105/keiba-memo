<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RacetrackMaster extends Model
{
    protected $fillable = ['name'];

    public function races()
    {
        return $this->hasMany(Race::class);
    }
}
