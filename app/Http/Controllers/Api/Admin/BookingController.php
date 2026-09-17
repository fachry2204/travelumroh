<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Package;
use App\Models\Pilgrim;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class BookingController extends Controller
{
    public function index(Request $request)
    {
        $bookings = Booking::with(['package', 'user', 'agent.user', 'pilgrims'])
            ->when($request->search, fn($q) => $q->where('booking_number', 'like', '%' . $request->search . '%')
                ->orWhereHas('user', fn($uq) => $uq->where('name', 'like', '%' . $request->search . '%'))
                ->orWhereHas('pilgrims', fn($pq) => $pq->where('full_name', 'like', '%' . $request->search . '%')))
            ->when($request->booking_status, fn($q) => $q->where('booking_status', $request->booking_status))
            ->when($request->document_status, fn($q) => $q->where('document_status', $request->document_status))
            ->when($request->package_id, fn($q) => $q->where('package_id', $request->package_id))
            ->orderByDesc('created_at')
            ->paginate($request->per_page ?? 15);

        return response()->json(['success' => true, 'data' => $bookings]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'package_id' => 'required|exists:packages,id',
            'user_id' => 'nullable|exists:users,id',
            'full_name' => 'required_without:user_id|nullable|string|max:200',
            'email' => 'required_without:user_id|nullable|email',
            'phone' => 'required_without:user_id|nullable|string|max:20',
            'room_type' => 'required|in:quad,triple,double',
            'total_pilgrims' => 'required|integer|min:1|max:20',
            'booking_status' => 'required|in:pending,dp,paid,cancelled',
            'document_status' => 'nullable|in:incomplete,review,complete',
            'visa_status' => 'nullable|in:not_submitted,process,issued',
            'notes' => 'nullable|string',
        ]);

        $package = Package::findOrFail($validated['package_id']);

        if ($package->remaining_seat < $validated['total_pilgrims']) {
            return response()->json([
                'success' => false,
                'message' => 'Sisa seat tidak mencukupi. Tersedia: ' . $package->remaining_seat . ' seat.',
            ], 422);
        }

        // Determine user
        $user = null;
        if (!empty($validated['user_id'])) {
            $user = User::find($validated['user_id']);
        } else {
            $user = User::where('email', $validated['email'])->orWhere('phone', $validated['phone'])->first();
            if (!$user) {
                $user = User::create([
                    'name' => $validated['full_name'],
                    'email' => $validated['email'],
                    'phone' => $validated['phone'],
                    'password' => Hash::make('password123'),
                    'role_type' => 'member',
                    'status' => 'active',
                ]);
            }
        }

        $priceMap = [
            'quad' => $package->price_quad,
            'triple' => $package->price_triple,
            'double' => $package->price_double,
        ];
        $totalAmount = ($priceMap[$validated['room_type']] ?? $package->price_quad) * $validated['total_pilgrims'];

        $booking = Booking::create([
            'package_id' => $package->id,
            'user_id' => $user?->id,
            'total_pilgrims' => $validated['total_pilgrims'],
            'room_type' => $validated['room_type'],
            'total_amount' => $totalAmount,
            'paid_amount' => 0,
            'outstanding_amount' => $totalAmount,
            'booking_status' => $validated['booking_status'],
            'document_status' => $validated['document_status'] ?? 'incomplete',
            'visa_status' => $validated['visa_status'] ?? 'not_submitted',
            'notes' => $validated['notes'] ?? null,
        ]);

        // Create primary pilgrim
        Pilgrim::create([
            'booking_id' => $booking->id,
            'full_name' => $user?->name ?? $validated['full_name'] ?? 'Jamaah 1',
            'phone' => $user?->phone ?? $validated['phone'] ?? null,
            'email' => $user?->email ?? $validated['email'] ?? null,
            'room_type' => $validated['room_type'],
        ]);

        $package->decrement('remaining_seat', $validated['total_pilgrims']);

        return response()->json([
            'success' => true,
            'message' => 'Booking berhasil dibuat',
            'data' => $booking->load(['package', 'user', 'pilgrims']),
        ], 201);
    }

    public function show(Booking $booking)
    {
        return response()->json([
            'success' => true,
            'data' => $booking->load(['package', 'user', 'agent.user', 'pilgrims.documents', 'payments', 'commission']),
        ]);
    }

    public function update(Request $request, Booking $booking)
    {
        $validated = $request->validate([
            'package_id' => 'sometimes|exists:packages,id',
            'room_type' => 'sometimes|in:quad,triple,double',
            'total_pilgrims' => 'sometimes|integer|min:1|max:20',
            'total_amount' => 'sometimes|numeric|min:0',
            'paid_amount' => 'sometimes|numeric|min:0',
            'booking_status' => 'sometimes|in:pending,dp,paid,cancelled',
            'document_status' => 'sometimes|in:incomplete,review,complete',
            'visa_status' => 'sometimes|in:not_submitted,process,issued',
            'notes' => 'nullable|string',
        ]);

        if (isset($validated['paid_amount']) || isset($validated['total_amount'])) {
            $total = $validated['total_amount'] ?? $booking->total_amount;
            $paid = $validated['paid_amount'] ?? $booking->paid_amount;
            $validated['outstanding_amount'] = max(0, $total - $paid);
        }

        $booking->update($validated);

        return response()->json(['success' => true, 'message' => 'Booking berhasil diupdate', 'data' => $booking->fresh(['package', 'user', 'pilgrims'])]);
    }

    public function destroy(Booking $booking)
    {
        if ($booking->package && $booking->booking_status !== 'cancelled') {
            $booking->package->increment('remaining_seat', $booking->total_pilgrims);
        }
        
        $booking->pilgrims()->delete();
        $booking->payments()->delete();
        $booking->delete();

        return response()->json(['success' => true, 'message' => 'Booking berhasil dihapus']);
    }
}
