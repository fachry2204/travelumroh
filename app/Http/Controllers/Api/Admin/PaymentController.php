<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\Booking;
use App\Services\MpwaService;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function index(Request $request)
    {
        $payments = Payment::with(['booking.package', 'booking.pilgrims', 'validator'])
            ->when($request->status, fn($q) => $q->where('status', $request->status))
            ->when($request->payment_type, fn($q) => $q->where('payment_type', $request->payment_type))
            ->when($request->booking_id, fn($q) => $q->where('booking_id', $request->booking_id))
            ->orderByDesc('created_at')
            ->paginate($request->per_page ?? 15);

        return response()->json(['success' => true, 'data' => $payments]);
    }

    public function show(Payment $payment)
    {
        return response()->json([
            'success' => true,
            'data' => $payment->load(['booking.package', 'booking.pilgrims']),
        ]);
    }

    public function validate(Request $request, Payment $payment)
    {
        $validated = $request->validate([
            'status' => 'required|in:approved,rejected',
            'note' => 'nullable|string',
        ]);

        $payment->update([
            'status' => $validated['status'],
            'note' => $validated['note'] ?? null,
            'validated_by' => auth()->id(),
            'validated_at' => now(),
        ]);

        $booking = $payment->booking;
        if ($validated['status'] === 'approved') {
            // Update booking amounts
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
                    $message .= "Pembayaran Anda sebesar *Rp " . number_format($payment->amount, 0, ',', '.') . "* telah *DITERIMA* dan divalidasi oleh Admin.\n\n";
                    $message .= "Status Booking: *" . strtoupper($booking->booking_status) . "*\n";
                    $message .= "Sisa Tagihan: *Rp " . number_format($booking->outstanding_amount, 0, ',', '.') . "*\n\n";
                    $message .= "Kwitansi dapat diunduh melalui menu Pembayaran di Member Area.\n\n";
                    $message .= "Terima kasih,\nPT Travel Umroh Indonesia.";
                    
                    $mpwa->sendMessage($user->phone, $message);
                }
            } catch (\Exception $e) {
                \Log::error("Failed to send WA on payment validation: " . $e->getMessage());
            }
        }

        return response()->json(['success' => true, 'message' => 'Pembayaran berhasil divalidasi', 'data' => $payment->load('booking')]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'booking_id' => 'required|exists:bookings,id',
            'payment_type' => 'required|in:dp,installment,final,refund',
            'amount' => 'required|numeric|min:1',
            'method' => 'required|in:bank_transfer,cash,payment_gateway',
            'bank_name' => 'nullable|string|max:100',
            'account_name' => 'nullable|string|max:200',
            'account_number' => 'nullable|string|max:50',
            'note' => 'nullable|string',
        ]);

        $payment = Payment::create(array_merge($validated, ['status' => 'approved', 'validated_by' => auth()->id(), 'validated_at' => now()]));

        // Update booking amounts
        $booking = Booking::find($validated['booking_id']);
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
                $message .= "Terdapat penambahan pembayaran sebesar *Rp " . number_format($payment->amount, 0, ',', '.') . "* pada booking Anda.\n\n";
                $message .= "Status Booking: *" . strtoupper($booking->booking_status) . "*\n";
                $message .= "Sisa Tagihan: *Rp " . number_format($booking->outstanding_amount, 0, ',', '.') . "*\n\n";
                $message .= "Kwitansi dapat diunduh melalui menu Pembayaran di Member Area.\n\n";
                $message .= "Terima kasih,\nPT Travel Umroh Indonesia.";
                
                $mpwa->sendMessage($user->phone, $message);
            }
        } catch (\Exception $e) {
            \Log::error("Failed to send WA on manual payment store: " . $e->getMessage());
        }

        return response()->json(['success' => true, 'message' => 'Pembayaran berhasil ditambahkan', 'data' => $payment], 201);
    }
}
