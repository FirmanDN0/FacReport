<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Report extends Model
{
    protected $fillable = [
        'report_code', 'user_id', 'facility_name', 'room_id', 'building_id',
        'severity', 'description', 'photo', 'completion_photo', 'status', 'technician_id', 'technician_notes'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function room()
    {
        return $this->belongsTo(Room::class);
    }

    public function building()
    {
        return $this->belongsTo(Building::class);
    }

    public function technician()
    {
        return $this->belongsTo(Technician::class);
    }

    public function logs()
    {
        return $this->hasMany(ReportLog::class)->orderBy('created_at', 'asc');
    }

    public static function generateCode()
    {
        $lastReport = self::orderBy('id', 'desc')->first();
        $nextNumber = $lastReport ? ((int) substr($lastReport->report_code, 4)) + 1 : 1;
        return 'FLS-' . str_pad($nextNumber, 3, '0', STR_PAD_LEFT);
    }

    public function getStatusLabelAttribute()
    {
        return match ($this->status) {
            'menunggu' => 'Menunggu',
            'diproses' => 'Diproses',
            'selesai' => 'Selesai',
            'dibatalkan' => 'Dibatalkan',
            default => $this->status,
        };
    }

    public function getStatusColorAttribute()
    {
        return match ($this->status) {
            'menunggu' => 'warning',
            'diproses' => 'info',
            'selesai' => 'success',
            'dibatalkan' => 'danger',
            default => 'secondary',
        };
    }

    public function getSeverityLabelAttribute()
    {
        return match ($this->severity) {
            'ringan' => 'Ringan',
            'sedang' => 'Sedang',
            'berat' => 'Berat',
            default => $this->severity,
        };
    }

    public function getSeverityColorAttribute()
    {
        return match ($this->severity) {
            'ringan' => 'success',
            'sedang' => 'warning',
            'berat' => 'danger',
            default => 'secondary',
        };
    }
}
