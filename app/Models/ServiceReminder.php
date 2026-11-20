<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ServiceReminder extends Model
{
    use HasFactory;

    protected $fillable = [
        'vehicle_id',
        'maintenance_item_id',
        'scheduled_date',
        'custom_km',
        'is_snoozed',
        'status',
        'channel',
        'last_notified_at',
    ];

    protected $casts = [
        'scheduled_date' => 'date',
        'is_snoozed' => 'boolean',
        'last_notified_at' => 'datetime',
    ];

    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function maintenanceItem()
    {
        return $this->belongsTo(MaintenanceItem::class);
    }
}
