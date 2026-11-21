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
        $baseDate = $this->last_recalibrate_date ? \Carbon\Carbon::parse($this->last_recalibrate_date) : $this->created_at;

        // Hitung selisih hari kalender (pukul 00:00) dari tanggal kalibrasi ke hari ini
        $daysElapsed = max(0, $baseDate->startOfDay()->diffInDays(now()->startOfDay(), false));

        return (int) round($baseKm + ($daysElapsed * $this->daily_average_km));
    }

    /**
     * Hitung sisa umur & status kesehatan masing-masing sparepart vital (5 Part)
     */
    public function getPartsHealthAttribute()
    {
        $currentKm = $this->current_odometer;
        $items = MaintenanceItem::where('vehicle_type', $this->vehicle_type ?? 'motor')->get();

        // Jika tabel kosong, fallback ambil semua item
        if ($items->isEmpty()) {
            $items = MaintenanceItem::all();
        }

        $logs = $this->serviceLogs()->orderBy('service_date', 'desc')->get();

        return $items->map(function ($item) use ($currentKm, $logs) {
            // Cari log servis terakhir untuk part ini
            $lastLog = $logs->firstWhere('maintenance_item_id', $item->id);

            // Jika pernah diservis, gunakan KM saat servis. Jika belum, gunakan Odometer awal pendaftaran
            $lastServiceKm = $lastLog ? $lastLog->odometer_at_service : $this->initial_odometer_km;

            $kmUsed = max(0, $currentKm - $lastServiceKm);
            $intervalKm = $item->interval_km > 0 ? $item->interval_km : 3000;

            // Sisa persentase umur (0% s/d 100%)
            $percentage = max(0, min(100, round((($intervalKm - $kmUsed) / $intervalKm) * 100)));

            // Tentukan status dan warna
            if ($percentage > 40) {
                $status = 'safe'; // Prima
                $color = 'text-emerald-500';
                $strokeColor = '#10B981';
            } elseif ($percentage >= 15) {
                $status = 'warning'; // Waspada
                $color = 'text-amber-500';
                $strokeColor = '#F59E0B';
            } else {
                $status = 'danger'; // Kritis
                $color = 'text-rose-500';
                $strokeColor = '#EF4444';
            }

            // Nama pendek untuk tampilan ringkas
            $shortName = match ($item->id) {
                1 => 'Oli Mesin',
                2 => 'Oli Gardan',
                3 => 'Busi',
                4 => 'Kampas Rem',
                5 => 'V-Belt',
                default => explode(' ', $item->item_name)[0]
            };

            return (object) [
                'id' => $item->id,
                'name' => $item->item_name,
                'short_name' => $shortName,
                'percentage' => (int) $percentage,
                'status' => $status,
                'color' => $color,
                'stroke_color' => $strokeColor,
                'km_remaining' => max(0, $intervalKm - $kmUsed),
                'interval_km' => $intervalKm,
            ];
        });
    }
}
