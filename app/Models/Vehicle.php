<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Vehicle extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'vehicle_type',
        'brand',
        'model_name',
        'license_plate',
        'manufacture_year',
        'initial_odometer_km',
        'daily_average_km',
        'last_recalibrate_km',
        'last_recalibrate_date',
    ];

    protected $casts = [
        'last_recalibrate_date' => 'date',
        'daily_average_km' => 'float',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function serviceLogs()
    {
        return $this->hasMany(ServiceLog::class);
    }

    public function serviceReminders()
    {
        return $this->hasMany(ServiceReminder::class);
    }

    public function aiScanHistories()
    {
        return $this->hasMany(AiScanHistory::class);
    }

    /**
     * Hitung estimasi odometer real-time hari ini tanpa GPS (Decay Engine)
     */
    public function getCurrentOdometerAttribute()
    {
        $baseKm = $this->last_recalibrate_km ?: $this->initial_odometer_km;
        $baseDate = $this->last_recalibrate_date ?: $this->created_at->toDateString();

        $daysElapsed = now()->diffInDays(\Carbon\Carbon::parse($baseDate));
        return (int) round($baseKm + ($daysElapsed * $this->daily_average_km));
    }
}
