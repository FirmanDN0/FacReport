<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Building extends Model
{
    protected $fillable = ['name', 'code'];

    public function rooms()
    {
        return $this->hasMany(Room::class);
    }

    public function reports()
    {
        return $this->hasMany(Report::class);
    }
}
