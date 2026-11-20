<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ServiceLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'vehicle_id',
        'maintenance_item_id',
        'service_date',
        'odometer_at_service',
        'actual_cost',
        'workshop_name',
        'notes',
        'receipt_image_url',
    ];

    protected $casts = [
        'service_date' => 'date',
        'odometer_at_service' => 'integer',
        'actual_cost' => 'decimal:2',
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
