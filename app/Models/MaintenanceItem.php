<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MaintenanceItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'vehicle_type',
        'item_name',
        'interval_km',
        'interval_months',
        'routine_cost',
        'neglect_cost',
        'neglect_impact_desc',
    ];

    public function serviceLogs()
    {
        return $this->hasMany(ServiceLog::class);
    }

    public function serviceReminders()
    {
        return $this->hasMany(ServiceReminder::class);
    }
}
