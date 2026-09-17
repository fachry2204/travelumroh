<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\Setting;

class MpwaService
{
    protected $url;
    protected $apiKey;

    public function __construct()
    {
        // Try getting from DB first, fallback to config
        $url = Setting::where('setting_key', 'mpwa_url')->value('setting_value');
        $apiKey = Setting::where('setting_key', 'mpwa_api_key')->value('setting_value');
        $this->url = $url ?? config('services.mpwa.url');
        $this->apiKey = $apiKey ?? config('services.mpwa.api_key');
    }

    /**
     * Mengirim pesan teks ke nomor WhatsApp.
     *
     * @param string $number Nomor tujuan (format: 08xx atau 628xx)
     * @param string $message Isi pesan
     * @return array|bool Response dari API atau false jika gagal
     */
    public function sendMessage($number, $message)
    {
        // Format nomor: pastikan menggunakan 628...
        $number = preg_replace('/[^0-9]/', '', $number);
        if (substr($number, 0, 1) === '0') {
            $number = '62' . substr($number, 1);
        }

        if (empty($this->url) || empty($this->apiKey)) {
            Log::warning("MPWA Not Configured: Tried to send message to $number: $message");
            return false;
        }

        try {
            $response = Http::post(rtrim($this->url, '/') . '/send-message', [
                'api_key' => $this->apiKey,
                'sender' => config('services.mpwa.sender', ''), // Opsional jika multi-sender
                'number' => $number,
                'message' => $message,
            ]);

            if ($response->successful()) {
                Log::info("MPWA Success: Sent message to $number");
                return $response->json();
            } else {
                Log::error("MPWA Failed: HTTP " . $response->status() . " " . $response->body());
                return false;
            }
        } catch (\Exception $e) {
            Log::error("MPWA Exception: " . $e->getMessage());
            return false;
        }
    }
}
