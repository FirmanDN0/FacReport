<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Room extends Model
{
    protected $fillable = ['name', 'building_id', 'floor'];

    public function building()
    {
        return $this->belongsTo(Building::class);
    }

    public function reports()
    {
        return $this->hasMany(Report::class);
    }
}
