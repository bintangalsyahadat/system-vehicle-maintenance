<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AiScanHistory extends Model
{
    use HasFactory;

    protected $fillable = [
        'vehicle_id',
        'image_url',
        'detected_indicator',
        'severity_level',
        'emergency_action',
        'estimated_cost_min',
        'estimated_cost_max',
        'is_resolved',
    ];

    protected $casts = [
        'is_resolved' => 'boolean',
        'estimated_cost_min' => 'decimal:2',
        'estimated_cost_max' => 'decimal:2',
    ];

    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }
}
