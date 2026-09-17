<?php

namespace App\Http\Controllers\Api\Member;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Payment;
use App\Models\Setting;
use App\Services\MpwaService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class DuitkuController extends Controller
{
    public function createInvoice(Request $request)
    {
        $request->validate([
            'booking_id' => 'required|exists:bookings,id',
            'amount' => 'required|numeric|min:10000',
            'payment_type' => 'required|in:dp,installment,final',
        ]);

        $booking = Booking::with('user')->where('id', $request->booking_id)->where('user_id', auth()->id())->firstOrFail();
        
        $merchantCode = Setting::where('setting_key', 'duitku_merchant_code')->value('setting_value') ?? config('services.duitku.merchant_code');
        $merchantKey = Setting::where('setting_key', 'duitku_merchant_key')->value('setting_value') ?? config('services.duitku.merchant_key');
        $env = Setting::where('setting_key', 'duitku_env')->value('setting_value') ?? config('services.duitku.env');
        
        $paymentAmount = (int) $request->amount;
        $merchantOrderId = 'INV-' . time() . '-' . $booking->id;
        
        // Buat record payment dengan status pending
        $payment = Payment::create([
            'booking_id' => $booking->id,
            'payment_type' => $request->payment_type,
            'amount' => $paymentAmount,
            'method' => 'payment_gateway',
            'payment_number' => $merchantOrderId,
            'status' => 'pending',
            'note' => 'Menunggu pembayaran via Duitku',
        ]);

        $signature = md5($merchantCode . $merchantOrderId . $paymentAmount . $merchantKey);

        $params = [
            'merchantCode' => $merchantCode,
            'paymentAmount' => $paymentAmount,
            'merchantOrderId' => $merchantOrderId,
            'productDetails' => 'Pembayaran ' . strtoupper($request->payment_type) . ' Umroh - ' . $booking->booking_number,
            'additionalParam' => '',
            'merchantUserInfo' => '',
            'customerVaName' => $booking->user->name,
            'email' => $booking->user->email,
            'phoneNumber' => $booking->user->phone,
            'itemDetails' => [
                [
                    'name' => 'Pembayaran ' . strtoupper($request->payment_type) . ' Umroh - ' . $booking->booking_number,
                    'price' => $paymentAmount,
                    'quantity' => 1,
                ]
            ],
            'customerDetail' => [
                'firstName' => $booking->user->name,
                'lastName' => '',
                'email' => $booking->user->email,
                'phoneNumber' => $booking->user->phone,
            ],
            'callbackUrl' => url('/api/duitku/callback'),
            'returnUrl' => config('app.frontend_url', 'http://localhost:5173') . '/member/pembayaran',
            'signature' => $signature,
            'expiryPeriod' => 1440 // 24 jam
        ];

        $apiUrl = $env === 'sandbox' 
            ? 'https://sandbox.duitku.com/webapi/api/merchant/v2/inquiry' 
            : 'https://passport.duitku.com/webapi/api/merchant/v2/inquiry';
            
        try {
            $response = Http::post($apiUrl, $params);
            
            if ($response->successful()) {
                $result = $response->json();
                
                if (isset($result['paymentUrl'])) {
                    return response()->json([
                        'success' => true,
                        'paymentUrl' => $result['paymentUrl']
                    ]);
                }
                
                Log::error('Duitku Inquiry Error', ['response' => $result]);
            } else {
                Log::error('Duitku HTTP Error', ['status' => $response->status(), 'body' => $response->body()]);
            }
        } catch (\Exception $e) {
            Log::error('Duitku Exception: ' . $e->getMessage());
        }

        return response()->json([
            'success' => false,
            'message' => 'Gagal membuat invoice Duitku. Silakan coba lagi.'
        ], 500);
    }

    public function callback(Request $request)
    {
        $merchantCode = Setting::where('setting_key', 'duitku_merchant_code')->value('setting_value') ?? config('services.duitku.merchant_code');
        $merchantKey = Setting::where('setting_key', 'duitku_merchant_key')->value('setting_value') ?? config('services.duitku.merchant_key');

        $amount = $request->input('amount');
        $merchantOrderId = $request->input('merchantOrderId');
        $resultCode = $request->input('resultCode');
        $signature = $request->input('signature');

        // Verifikasi signature
        $validSignature = md5($merchantCode . $amount . $merchantOrderId . $merchantKey);

        if ($signature !== $validSignature) {
            return response()->json(['message' => 'Bad Signature'], 400);
        }

        $payment = Payment::where('payment_number', $merchantOrderId)->first();

        if (!$payment) {
            return response()->json(['message' => 'Payment not found'], 404);
        }

        if ($payment->status === 'approved') {
            return response()->json(['message' => 'Already processed'], 200);
        }

        if ($resultCode === '00') {
            // Success
            $payment->update([
                'status' => 'approved',
                'note' => 'Pembayaran berhasil via Duitku (' . $request->input('paymentCode', '') . ')',
            ]);

            // Update Booking Amount
            $booking = Booking::with('user')->find($payment->booking_id);
            $totalPaid = $booking->payments()->where('status', 'approved')->sum('amount');
            
            $booking->update([
                'paid_amount' => $totalPaid,
                'outstanding_amount' => max(0, $booking->total_amount - $totalPaid),
                'booking_status' => $totalPaid >= $booking->total_amount ? 'paid' : ($totalPaid > 0 ? 'dp' : 'pending'),
            ]);

            // --- WA NOTIFICATION ---
            try {
                $user = $booking->user;
                if ($user && $user->phone) {
                    $mpwa = app(MpwaService::class);
                    $message = "Assalamu'alaikum *" . $user->name . "*,\n\n";
                    $message .= "Pembayaran Anda sebesar *Rp " . number_format($payment->amount, 0, ',', '.') . "* melalui Duitku telah *BERHASIL*.\n\n";
                    $message .= "Status Booking: *" . strtoupper($booking->booking_status) . "*\n";
                    $message .= "Sisa Tagihan: *Rp " . number_format($booking->outstanding_amount, 0, ',', '.') . "*\n\n";
                    $message .= "Terima kasih,\nPT Travel Umroh Indonesia.";
                    
                    $mpwa->sendMessage($user->phone, $message);
                }
            } catch (\Exception $e) {
                Log::error("Failed to send WA on Duitku Callback: " . $e->getMessage());
            }

        } else if ($resultCode === '01') {
            // Failed
            $payment->update([
                'status' => 'rejected',
                'note' => 'Pembayaran gagal via Duitku',
            ]);
        }
        
        return response()->json(['message' => 'Callback processed'], 200);
    }
}
