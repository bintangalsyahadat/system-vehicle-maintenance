<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class FonnteService
{
    protected ?string $token;
    protected string $baseUrl;

    public function __construct()
    {
        $this->token = config('services.fonnte.token');
        $this->baseUrl = rtrim((string) config('services.fonnte.base_url', 'https://api.fonnte.com'), '/');
    }

    /**
     * Check whether Fonnte API token is configured.
     */
    public function isConfigured(): bool
    {
        return !empty($this->token);
    }

    /**
     * Normalize Indonesian phone numbers to Fonnte target format (e.g., 6281234567890).
     */
    public function normalizePhone(?string $phone): ?string
    {
        if (empty($phone)) {
            return null;
        }

        $digits = preg_replace('/\D+/', '', $phone);
        if (empty($digits)) {
            return null;
        }

        if (str_starts_with($digits, '0')) {
            $digits = '62' . substr($digits, 1);
        } elseif (!str_starts_with($digits, '62')) {
            $digits = '62' . $digits;
        }

        return strlen($digits) >= 10 ? $digits : null;
    }

    /**
     * Send a WhatsApp message via Fonnte API.
     *
     * @return array{success: bool, message: string, response: array|null}
     */
    public function sendMessage(string $targetPhone, string $message): array
    {
        $normalized = $this->normalizePhone($targetPhone);
        if (!$normalized) {
            return [
                'success' => false,
                'message' => 'Nomor WhatsApp tidak valid. Gunakan format seperti 081234567890.',
                'response' => null,
            ];
        }

        if (!$this->isConfigured()) {
            Log::info('Fonnte token not configured; skipped WhatsApp send.', [
                'target' => $normalized,
            ]);

            return [
                'success' => false,
                'message' => 'FONNTE_TOKEN belum dikonfigurasi di .env server.',
                'response' => null,
            ];
        }

        try {
            $response = Http::withHeaders([
                'Authorization' => $this->token,
            ])
                ->asForm()
                ->timeout(15)
                ->post("{$this->baseUrl}/send", [
                    'target' => $normalized,
                    'message' => $message,
                    'countryCode' => '62',
                ]);

            $json = $response->json();
            $statusOk = $response->successful() && ($json['status'] ?? false) === true;

            if (!$statusOk) {
                Log::warning('Fonnte API returned failure', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);

                return [
                    'success' => false,
                    'message' => $json['reason'] ?? 'Gagal mengirim pesan WhatsApp melalui Fonnte.',
                    'response' => is_array($json) ? $json : null,
                ];
            }

            return [
                'success' => true,
                'message' => 'Notifikasi pengingat berhasil dikirim ke WhatsApp Anda.',
                'response' => is_array($json) ? $json : null,
            ];
        } catch (\Exception $e) {
            Log::error('FonnteService exception: ' . $e->getMessage());

            return [
                'success' => false,
                'message' => 'Gagal terhubung ke gateway WhatsApp Fonnte.',
                'response' => null,
            ];
        }
    }
}
