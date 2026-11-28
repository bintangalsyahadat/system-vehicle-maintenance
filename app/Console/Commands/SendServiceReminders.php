<?php

namespace App\Console\Commands;

use App\Models\ServiceReminder;
use App\Models\User;
use App\Services\FonnteService;
use App\Services\ReminderNotificationService;
use Illuminate\Console\Command;

class SendServiceReminders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'servisa:send-reminders';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Kirim notifikasi pengingat servis otomatis ke WhatsApp user via Fonnte';

    /**
     * Execute the console command.
     */
    public function handle(ReminderNotificationService $notificationService, FonnteService $fonnteService): int
    {
        if (!$fonnteService->isConfigured()) {
            $this->warn('FONNTE_TOKEN belum dikonfigurasi. Pengiriman WhatsApp dilewati.');
            return self::SUCCESS;
        }

        $users = User::whereNotNull('phone_whatsapp')
            ->where('phone_whatsapp', '!=', '')
            ->get();

        $sentCount = 0;

        foreach ($users as $user) {
            $notifications = $notificationService->getForUser($user);
            if ($notifications->isEmpty()) {
                continue;
            }

            // Hindari spam: cek apakah ada pengingat yang belum dinotifikasi dalam 24 jam terakhir
            $reminderIds = $notifications->pluck('reminder_id')->filter()->values();
            if ($reminderIds->isNotEmpty()) {
                $hasFreshReminder = ServiceReminder::whereIn('id', $reminderIds)
                    ->where(function ($q) {
                        $q->whereNull('last_notified_at')
                            ->orWhere('last_notified_at', '<=', now()->subHours(24));
                    })
                    ->exists();

                if (!$hasFreshReminder && $notifications->where('level', 'critical')->isEmpty()) {
                    continue;
                }
            }

            $message = $notificationService->buildWhatsAppMessage($user, $notifications);
            $result = $fonnteService->sendMessage($user->phone_whatsapp, $message);

            if ($result['success']) {
                $sentCount++;
                if ($reminderIds->isNotEmpty()) {
                    ServiceReminder::whereIn('id', $reminderIds)->update([
                        'last_notified_at' => now(),
                    ]);
                }
                $this->info("Terkirim ke {$user->name} ({$user->phone_whatsapp})");
            } else {
                $this->error("Gagal kirim ke {$user->name}: {$result['message']}");
            }
        }

        $this->info("Selesai. Total notifikasi WhatsApp terkirim: {$sentCount}");
        return self::SUCCESS;
    }
}
