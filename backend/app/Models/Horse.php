<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Horse extends Model
{
    protected $fillable = ['name'];

    public function races()
    {
        return $this->belongsToMany(Race::class, 'horse_races');
    }
}
