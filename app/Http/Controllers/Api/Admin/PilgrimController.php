<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pilgrim;
use Illuminate\Http\Request;

class PilgrimController extends Controller
{
    public function index(Request $request)
    {
        $pilgrims = Pilgrim::with(['booking.package', 'documents'])
            ->when($request->search, fn($q) => $q->where('full_name', 'like', '%' . $request->search . '%')
                ->orWhere('nik', 'like', '%' . $request->search . '%')
                ->orWhere('passport_number', 'like', '%' . $request->search . '%'))
            ->when($request->booking_id, fn($q) => $q->where('booking_id', $request->booking_id))
            ->orderByDesc('created_at')
            ->paginate($request->per_page ?? 15);

        return response()->json(['success' => true, 'data' => $pilgrims]);
    }

    public function show(Pilgrim $pilgrim)
    {
        return response()->json([
            'success' => true,
            'data' => $pilgrim->load(['booking.package', 'documents']),
        ]);
    }

    private function sanitizeInput(array $input): array
    {
        foreach ($input as $key => $val) {
            if ($val === '') {
                $input[$key] = null;
            }
        }
        return $input;
    }

    public function update(Request $request, Pilgrim $pilgrim)
    {
        $data = $this->sanitizeInput($request->all());

        $validated = \Illuminate\Support\Facades\Validator::make($data, [
            'full_name' => 'sometimes|string|max:200',
            'nik' => 'nullable|digits:16',
            'family_card_number' => 'nullable|digits:16',
            'birth_place' => 'nullable|string|max:100',
            'birth_date' => 'nullable|date',
            'gender' => 'nullable|in:male,female',
            'marital_status' => 'nullable|in:single,married,divorced,widowed',
            'job' => 'nullable|string|max:100',
            'education' => 'nullable|string|max:100',
            'address' => 'nullable|string',
            'province' => 'nullable|string|max:100',
            'city' => 'nullable|string|max:100',
            'district' => 'nullable|string|max:100',
            'village' => 'nullable|string|max:100',
            'postal_code' => 'nullable|string|max:10',
            'phone' => 'nullable|string|max:30',
            'email' => 'nullable|email',
            'passport_number' => 'nullable|string|max:30',
            'passport_issued_place' => 'nullable|string|max:100',
            'passport_issued_date' => 'nullable|date',
            'passport_expired_date' => 'nullable|date',
            'passport_name' => 'nullable|string|max:200',
            'blood_type' => 'nullable|in:A,B,AB,O',
            'medical_history' => 'nullable|string',
            'allergy' => 'nullable|string',
            'special_needs' => 'nullable|string',
            'emergency_contact_name' => 'nullable|string|max:200',
            'emergency_contact_relation' => 'nullable|string|max:100',
            'emergency_contact_phone' => 'nullable|string|max:30',
            'emergency_contact_address' => 'nullable|string',
            'room_type' => 'nullable|string|max:50',
            'room_number' => 'nullable|string|max:50',
            'bus_number' => 'nullable|string|max:50',
        ], [
            'nik.digits' => 'NIK harus 16 angka.',
            'family_card_number.digits' => 'No. Kartu Keluarga harus 16 angka.',
        ])->validate();

        $pilgrim->update($validated);

        return response()->json(['success' => true, 'message' => 'Data jamaah berhasil diupdate', 'data' => $pilgrim]);
    }

    public function store(Request $request)
    {
        $data = $this->sanitizeInput($request->all());

        $validated = \Illuminate\Support\Facades\Validator::make($data, [
            'booking_id' => 'required|exists:bookings,id',
            'full_name' => 'required|string|max:200',
            'nik' => 'nullable|digits:16',
            'family_card_number' => 'nullable|digits:16',
            'birth_place' => 'nullable|string|max:100',
            'birth_date' => 'nullable|date',
            'gender' => 'nullable|in:male,female',
            'marital_status' => 'nullable|in:single,married,divorced,widowed',
            'job' => 'nullable|string|max:100',
            'education' => 'nullable|string|max:100',
            'address' => 'nullable|string',
            'province' => 'nullable|string|max:100',
            'city' => 'nullable|string|max:100',
            'district' => 'nullable|string|max:100',
            'village' => 'nullable|string|max:100',
            'postal_code' => 'nullable|string|max:10',
            'phone' => 'nullable|string|max:30',
            'email' => 'nullable|email',
            'passport_number' => 'nullable|string|max:30',
            'passport_issued_place' => 'nullable|string|max:100',
            'passport_issued_date' => 'nullable|date',
            'passport_expired_date' => 'nullable|date',
            'passport_name' => 'nullable|string|max:200',
            'blood_type' => 'nullable|in:A,B,AB,O',
            'medical_history' => 'nullable|string',
            'allergy' => 'nullable|string',
            'special_needs' => 'nullable|string',
            'emergency_contact_name' => 'nullable|string|max:200',
            'emergency_contact_relation' => 'nullable|string|max:100',
            'emergency_contact_phone' => 'nullable|string|max:30',
            'emergency_contact_address' => 'nullable|string',
            'room_type' => 'nullable|string|max:50',
            'room_number' => 'nullable|string|max:50',
            'bus_number' => 'nullable|string|max:50',
        ], [
            'nik.digits' => 'NIK harus 16 angka.',
            'family_card_number.digits' => 'No. Kartu Keluarga harus 16 angka.',
        ])->validate();

        $booking = \App\Models\Booking::findOrFail($validated['booking_id']);
        if ($booking->pilgrims()->count() >= $booking->total_pilgrims) {
            return response()->json([
                'success' => false,
                'message' => "Jumlah jamaah telah mencapai batas kuota booking ({$booking->total_pilgrims} orang)."
            ], 422);
        }

        $pilgrim = Pilgrim::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Data jamaah berhasil ditambahkan',
            'data' => $pilgrim->load(['booking.package', 'documents']),
        ], 201);
    }

    public function destroy(Pilgrim $pilgrim)
    {
        $pilgrim->documents()->delete();
        $pilgrim->delete();

        return response()->json(['success' => true, 'message' => 'Data jamaah berhasil dihapus']);
    }
}
