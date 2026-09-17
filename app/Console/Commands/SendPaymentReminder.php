<?php

namespace App\Console\Commands;

use App\Models\Booking;
use App\Services\MpwaService;
use Illuminate\Console\Command;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class SendPaymentReminder extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:send-payment-reminder';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send WhatsApp payment reminders for pending/DP bookings older than 7 days.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting payment reminder check...');

        // Cari booking dengan status pending atau dp, tagihan belum lunas, 
        // dan sudah terlewat dari 7 hari sejak dibuat (sebagai contoh policy).
        // Bisa juga disesuaikan menjadi 7 hari setelah update terakhir dll.
        $targetDate = Carbon::now()->subDays(7)->format('Y-m-d');

        $bookings = Booking::with('user')
            ->whereIn('booking_status', ['pending', 'dp'])
            ->where('outstanding_amount', '>', 0)
            ->whereDate('created_at', '<=', $targetDate)
            ->get();

        if ($bookings->isEmpty()) {
            $this->info('No bookings require reminders today.');
            return;
        }

        $mpwa = app(MpwaService::class);
        $frontendUrl = config('app.frontend_url', 'http://localhost:5173');

        $count = 0;
        foreach ($bookings as $booking) {
            $user = $booking->user;
            if ($user && $user->phone) {
                try {
                    $message = "Assalamu'alaikum *" . $user->name . "*,\n\n";
                    $message .= "Kami ingin mengingatkan bahwa ada tagihan yang belum dilunasi untuk pendaftaran Umroh Anda dengan nomor booking *" . $booking->booking_number . "*.\n\n";
                    $message .= "Sisa Tagihan: *Rp " . number_format($booking->outstanding_amount, 0, ',', '.') . "*\n\n";
                    $message .= "Silakan lakukan pembayaran melalui tautan berikut:\n";
                    $message .= $frontendUrl . "/member/pembayaran\n\n";
                    $message .= "Abaikan pesan ini jika Anda sudah melakukan pembayaran. Terima kasih,\nPT Travel Umroh Indonesia.";

                    $mpwa->sendMessage($user->phone, $message);
                    $count++;
                    
                    Log::info("Sent payment reminder to {$user->phone} for booking {$booking->booking_number}");
                } catch (\Exception $e) {
                    Log::error("Failed to send reminder to {$user->phone}: " . $e->getMessage());
                }
            }
        }

        $this->info("Successfully sent {$count} reminders.");
    }
}
