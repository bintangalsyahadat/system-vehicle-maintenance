<?php

namespace App\Services;

use App\Models\AiScanHistory;
use App\Models\ServiceReminder;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class ReminderNotificationService
{
    /**
     * Build active in-app web notifications for all vehicles owned by a user.
     */
    public function getForUser(?User $user): Collection
    {
        if (!$user) {
            return collect();
        }

        $vehicles = $user->vehicles()->get();
        if ($vehicles->isEmpty()) {
            return collect();
        }

        $vehicleIds = $vehicles->pluck('id');
        $vehiclesById = $vehicles->keyBy('id');
        $notifications = collect();

        // 1. Scheduled reminders (due or within 7 days, not snoozed)
        $reminders = ServiceReminder::with('maintenanceItem')
            ->whereIn('vehicle_id', $vehicleIds)
            ->where('status', 'pending')
            ->where('is_snoozed', false)
            ->whereDate('scheduled_date', '<=', now()->addDays(7)->toDateString())
            ->orderBy('scheduled_date', 'asc')
            ->get();

        $remindedPartKeys = [];
        foreach ($reminders as $reminder) {
            $vehicle = $vehiclesById->get($reminder->vehicle_id);
            $partName = $reminder->maintenanceItem?->item_name ?? 'Komponen Servis';
            $dueDate = Carbon::parse($reminder->scheduled_date)->startOfDay();
            $daysDiff = now()->startOfDay()->diffInDays($dueDate, false);

            $remindedPartKeys[$reminder->vehicle_id . '_' . $reminder->maintenance_item_id] = true;

            if ($daysDiff < 0) {
                $timeText = 'Terlambat ' . abs($daysDiff) . ' hari';
                $level = 'critical';
            } elseif ($daysDiff === 0) {
                $timeText = 'Jadwal hari ini';
                $level = 'critical';
            } else {
                $timeText = $daysDiff . ' hari lagi (' . $dueDate->translatedFormat('d M') . ')';
                $level = 'warning';
            }

            $notifications->push((object) [
                'id' => 'reminder-' . $reminder->id,
                'reminder_id' => $reminder->id,
                'type' => 'reminder',
                'level' => $level,
                'title' => "Pengingat {$partName}",
                'message' => "{$vehicle?->brand} {$vehicle?->model_name} • {$timeText}",
                'time_label' => $dueDate->translatedFormat('d M Y'),
                'url' => route('dashboard'),
                'can_snooze' => true,
            ]);
        }

        // 2. Critical / Warning parts from real-time ODO Decay Engine
        foreach ($vehicles as $vehicle) {
            foreach ($vehicle->parts_health as $part) {
                $key = $vehicle->id . '_' . $part->id;
                if (isset($remindedPartKeys[$key]) || $part->status === 'safe') {
                    continue;
                }

                $isCritical = $part->status === 'danger';
                $notifications->push((object) [
                    'id' => "part-{$vehicle->id}-{$part->id}",
                    'reminder_id' => null,
                    'type' => 'part_health',
                    'level' => $isCritical ? 'critical' : 'warning',
                    'title' => ($isCritical ? 'Kritis: ' : 'Waspada: ') . $part->short_name,
                    'message' => "{$vehicle->brand} {$vehicle->model_name} • Sisa {$part->percentage}% (" . number_format($part->km_remaining, 0, ',', '.') . " km)",
                    'time_label' => $isCritical ? 'Segera Servis' : 'Perlu Perhatian',
                    'url' => route('dashboard'),
                    'can_snooze' => false,
                ]);
            }
        }

        // 3. Unresolved AI Vision warnings/critical scans
        $unresolvedScans = AiScanHistory::whereIn('vehicle_id', $vehicleIds)
            ->where('is_resolved', false)
            ->whereIn('severity_level', ['CRITICAL', 'WARNING'])
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        foreach ($unresolvedScans as $scan) {
            $vehicle = $vehiclesById->get($scan->vehicle_id);
            $notifications->push((object) [
                'id' => 'scan-' . $scan->id,
                'reminder_id' => null,
                'type' => 'ai_scan',
                'level' => $scan->severity_level === 'CRITICAL' ? 'critical' : 'warning',
                'title' => "Temuan AI: {$scan->detected_indicator}",
                'message' => "{$vehicle?->brand} {$vehicle?->model_name} • Belum ditandai selesai",
                'time_label' => $scan->created_at?->diffForHumans() ?? 'Baru saja',
                'url' => route('diagnosa'),
                'can_snooze' => false,
            ]);
        }

        return $notifications->sortBy(function ($item) {
            return $item->level === 'critical' ? 0 : 1;
        })->values();
    }

    /**
     * Format a clean WhatsApp reminder message for a user's urgent notifications.
     */
    public function buildWhatsAppMessage(User $user, Collection $notifications): string
    {
        $firstName = explode(' ', $user->name)[0] ?: 'Rider';
        $lines = [
            "*SERVISA - Pengingat Perawatan Motor*",
            "Halo {$firstName}, berikut ringkasan kondisi & jadwal servis kendaraan Anda:",
            "",
        ];

        foreach ($notifications->take(6) as $notif) {
            $prefix = $notif->level === 'critical' ? '[KRITIS]' : '[WASPADA]';
            $lines[] = "{$prefix} *{$notif->title}*";
            $lines[] = "   {$notif->message}";
        }

        if ($notifications->count() > 6) {
            $remaining = $notifications->count() - 6;
            $lines[] = "...dan {$remaining} pengingat lainnya.";
        }

        $lines[] = "";
        $lines[] = "Cek detail & catat servis di: " . route('dashboard');

        return implode("\n", $lines);
    }
}
