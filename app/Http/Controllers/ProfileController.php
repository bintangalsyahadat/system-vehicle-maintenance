<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use App\Services\FonnteService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $user = $request->user();
        $user->fill($request->validated());

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        if ($user->isDirty('phone_whatsapp')) {
            $user->whatsapp_verified_at = null;
            $user->whatsapp_verification_code = null;
            $user->whatsapp_verification_expires_at = null;
            $user->whatsapp_notifications_enabled = false;
        }

        $user->save();

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    /**
     * Kirim kode OTP 6 digit ke WhatsApp user melalui Fonnte untuk verifikasi nomor.
     */
    public function sendWhatsAppOtp(Request $request, FonnteService $fonnte): RedirectResponse
    {
        $user = $request->user();

        if (empty($user->phone_whatsapp)) {
            return Redirect::route('profile.edit')
                ->with('wa_error', 'Isi dan simpan nomor WhatsApp terlebih dahulu sebelum meminta kode verifikasi.');
        }

        $otp = str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        $user->forceFill([
            'whatsapp_verification_code' => $otp,
            'whatsapp_verification_expires_at' => now()->addMinutes(10),
        ])->save();

        $message = "*SERVISA - Kode Verifikasi WhatsApp*\n"
            . "Halo " . (explode(' ', $user->name)[0] ?: 'Rider') . ",\n"
            . "Kode OTP verifikasi nomor WhatsApp Anda adalah: *{$otp}*\n\n"
            . "Kode berlaku selama 10 menit. Jangan bagikan kode ini kepada siapapun.";

        $result = $fonnte->sendMessage($user->phone_whatsapp, $message);

        if (!$result['success']) {
            return Redirect::route('profile.edit')->with('wa_error', $result['message']);
        }

        return Redirect::route('profile.edit')->with('status', 'wa-otp-sent');
    }

    /**
     * Verifikasi kode OTP WhatsApp yang dimasukkan user.
     */
    public function verifyWhatsAppOtp(Request $request): RedirectResponse
    {
        $request->validateWithBag('whatsappVerification', [
            'otp_code' => ['required', 'digits:6'],
        ]);

        $user = $request->user();

        if (
            empty($user->whatsapp_verification_code)
            || !$user->whatsapp_verification_expires_at
            || $user->whatsapp_verification_expires_at->isPast()
        ) {
            return Redirect::route('profile.edit')
                ->with('wa_error', 'Kode OTP sudah kedaluwarsa atau belum dikirim. Silakan kirim ulang kode OTP.');
        }

        if (!hash_equals($user->whatsapp_verification_code, (string) $request->input('otp_code'))) {
            return Redirect::route('profile.edit')
                ->with('wa_error', 'Kode OTP tidak sesuai. Periksa kembali pesan WhatsApp Anda.');
        }

        $user->forceFill([
            'whatsapp_verified_at' => now(),
            'whatsapp_notifications_enabled' => true,
            'whatsapp_verification_code' => null,
            'whatsapp_verification_expires_at' => null,
        ])->save();

        return Redirect::route('profile.edit')->with('status', 'wa-verified');
    }

    /**
     * Toggle aktif/nonaktif notifikasi pengingat WhatsApp.
     */
    public function toggleWhatsAppNotification(Request $request): RedirectResponse
    {
        $user = $request->user();

        if (empty($user->phone_whatsapp) || $user->whatsapp_verified_at === null) {
            return Redirect::route('profile.edit')
                ->with('wa_error', 'Verifikasi nomor WhatsApp terlebih dahulu sebelum mengaktifkan notifikasi.');
        }

        $user->update([
            'whatsapp_notifications_enabled' => !$user->whatsapp_notifications_enabled,
        ]);

        return Redirect::route('profile.edit')->with(
            'status',
            $user->whatsapp_notifications_enabled ? 'wa-notif-enabled' : 'wa-notif-disabled'
        );
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
