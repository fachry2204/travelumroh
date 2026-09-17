<?php

namespace App\Http\Controllers\Api\Public;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Package;
use App\Models\Pilgrim;
use App\Models\Agent;
use App\Models\Commission;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Services\MpwaService;

class BookingController extends Controller
{
    public function store(Request $request)
    {
        if (!$request->has('full_name') && $request->has('contact_name')) {
            $request->merge([
                'full_name' => $request->input('contact_name'),
                'email' => $request->input('contact_email'),
                'phone' => $request->input('contact_phone'),
            ]);
        }

        $validated = $request->validate([
            'package_id' => 'required|exists:packages,id',
            'full_name' => 'required|string|max:200',
            'phone' => 'required|string|max:20',
            'email' => 'required|email',
            'room_type' => 'required|in:quad,triple,double',
            'total_pilgrims' => 'required|integer|min:1|max:20',
            'referral_code' => 'nullable|string|max:20',
            'notes' => 'nullable|string',
        ]);

        $package = Package::findOrFail($validated['package_id']);

        if ($package->remaining_seat < $validated['total_pilgrims']) {
            return response()->json([
                'success' => false,
                'message' => 'Sisa seat tidak mencukupi. Tersedia: ' . $package->remaining_seat . ' seat.',
            ], 422);
        }

        // Calculate total
        $priceMap = [
            'quad' => $package->price_quad,
            'triple' => $package->price_triple,
            'double' => $package->price_double,
        ];
        $pricePerPerson = $priceMap[$validated['room_type']];
        $totalAmount = $pricePerPerson * $validated['total_pilgrims'];

        // Find agent by referral code
        $agent = null;
        $source = 'direct';
        if (!empty($validated['referral_code'])) {
            $agent = Agent::where('referral_code', $validated['referral_code'])->where('status', 'active')->first();
            if ($agent) $source = 'agent';
        }

        // Create or get user
        $userId = null;
        $plainPassword = null;
        $user = null;
        if (auth()->check()) {
            $user = auth()->user();
            $userId = $user->id;
        } else {
            // Check if email or phone exists
            $user = User::where('email', $validated['email'])->orWhere('phone', $validated['phone'])->first();
            if (!$user) {
                $plainPassword = $request->password ?: Str::random(8);
                $user = User::create([
                    'name' => $validated['full_name'],
                    'email' => $validated['email'],
                    'phone' => $validated['phone'],
                    'password' => Hash::make($plainPassword),
                    'role_type' => 'member',
                    'status' => 'active',
                ]);
            }
            $userId = $user->id;
        }

        // Create booking
        $booking = Booking::create([
            'package_id' => $package->id,
            'user_id' => $userId,
            'agent_id' => $agent?->id,
            'representative_id' => $agent?->representative_id,
            'referral_code' => $agent ? $validated['referral_code'] : null,
            'source' => $source,
            'total_pilgrims' => $validated['total_pilgrims'],
            'room_type' => $validated['room_type'],
            'total_amount' => $totalAmount,
            'paid_amount' => 0,
            'outstanding_amount' => $totalAmount,
            'booking_status' => 'pending',
            'document_status' => 'incomplete',
            'notes' => $validated['notes'] ?? null,
        ]);

        // Create main pilgrim
        Pilgrim::create([
            'booking_id' => $booking->id,
            'full_name' => $validated['full_name'],
            'phone' => $validated['phone'],
            'email' => $validated['email'],
            'room_type' => $validated['room_type'],
        ]);

        // Decrease remaining seat
        $package->decrement('remaining_seat', $validated['total_pilgrims']);

        // Create commission if agent
        if ($agent) {
            $commissionAmount = $agent->commission_type === 'percentage'
                ? $totalAmount * ($agent->commission_value / 100)
                : $agent->commission_value * $validated['total_pilgrims'];

            Commission::create([
                'booking_id' => $booking->id,
                'agent_id' => $agent->id,
                'commission_type' => $agent->commission_type,
                'commission_value' => $agent->commission_value,
                'commission_amount' => $commissionAmount,
                'status' => 'pending',
            ]);
        }

        // --- WA NOTIFICATION ---
        try {
            $mpwa = app(MpwaService::class);
            $message = "Assalamu'alaikum *" . $validated['full_name'] . "*,\n\n";
            $message .= "Terima kasih telah mendaftar paket umroh *" . $package->name . "* di PT Travel Umroh Indonesia.\n\n";
            $message .= "Nomor Booking Anda: *" . $booking->booking_number . "*\n";
            $message .= "Total Tagihan: *Rp " . number_format($totalAmount, 0, ',', '.') . "*\n\n";
            
            if ($plainPassword) {
                $message .= "Silakan lengkapi dokumen dan lakukan pembayaran melalui Member Area dengan akun berikut:\n";
                $message .= "Email: " . $validated['email'] . "\n";
                $message .= "Password: *" . $plainPassword . "*\n\n";
            } else {
                $message .= "Silakan login ke Member Area untuk melengkapi dokumen dan pembayaran.\n\n";
            }
            
            $message .= "Harap segera melakukan pembayaran DP minimal *Rp " . number_format((float)$package->minimum_dp, 0, ',', '.') . "*.\n\n";
            $message .= "Wassalamu'alaikum wr. wb.";
            
            $mpwa->sendMessage($validated['phone'], $message);
        } catch (\Exception $e) {
            \Log::error("Failed to send WA on booking: " . $e->getMessage());
        }

        $token = null;
        if (!auth()->check() && $user) {
            $token = $user->createToken('auth_token')->plainTextToken;
        }

        return response()->json([
            'success' => true,
            'message' => 'Booking berhasil! Silakan lakukan pembayaran DP untuk mengkonfirmasi.',
            'data' => [
                'booking_number' => $booking->booking_number,
                'package' => $package->name,
                'total_amount' => $totalAmount,
                'minimum_dp' => $package->minimum_dp,
                'booking_id' => $booking->id,
                'booking' => $booking,
                'token' => $token,
                'user' => $user,
            ],
        ], 201);
    }
}
