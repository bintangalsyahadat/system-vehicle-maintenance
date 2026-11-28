<?php

namespace App\Http\Controllers;

use App\Models\ServiceReminder;
use App\Services\FonnteService;
use App\Services\ReminderNotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    /**
     * Snooze (tunda) sebuah pengingat servis dari panel notifikasi web.
     */
    public function snoozeReminder(Request $request, ServiceReminder $reminder)
    {
        $user = Auth::user();
        if (!$reminder->vehicle || $reminder->vehicle->user_id !== $user->id) {
            abort(403);
        }

        $reminder->update([
            'is_snoozed' => true,
        ]);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Pengingat servis berhasil ditunda.',
            ]);
        }

        return back()->with('success', 'Pengingat servis berhasil ditunda.');
    }

    /**
     * Kirim ringkasan pengingat servis aktif ke WhatsApp user melalui Fonnte.
     */
    public function sendWhatsApp(
        Request $request,
        ReminderNotificationService $notificationService,
        FonnteService $fonnteService
    ) {
        $user = Auth::user();

        if (empty($user->phone_whatsapp)) {
            $msg = 'Nomor WhatsApp Anda belum diisi. Silakan lengkapi di halaman Profil terlebih dahulu.';
            return $request->expectsJson()
                ? response()->json(['success' => false, 'message' => $msg], 422)
                : back()->with('error', $msg);
        }

        $notifications = $notificationService->getForUser($user);
        if ($notifications->isEmpty()) {
            $message = "*SERVISA - Status Kendaraan Prima*\nHalo " . (explode(' ', $user->name)[0] ?: 'Rider') . ", saat ini seluruh komponen motor Anda dalam kondisi aman dan belum ada jadwal servis mendesak.\n\nPantau terus di: " . route('dashboard');
        } else {
            $message = $notificationService->buildWhatsAppMessage($user, $notifications);
        }

        $result = $fonnteService->sendMessage($user->phone_whatsapp, $message);

        if ($result['success']) {
            $reminderIds = $notifications->pluck('reminder_id')->filter()->values();
            if ($reminderIds->isNotEmpty()) {
                ServiceReminder::whereIn('id', $reminderIds)->update([
                    'last_notified_at' => now(),
                ]);
            }
        }

        if ($request->expectsJson()) {
            return response()->json($result, $result['success'] ? 200 : 422);
        }

        return back()->with($result['success'] ? 'success' : 'error', $result['message']);
    }
}
