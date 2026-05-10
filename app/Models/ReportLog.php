<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReportLog extends Model
{
    protected $fillable = ['report_id', 'action', 'description', 'status'];

    public function report()
    {
        return $this->belongsTo(Report::class);
    }
}
