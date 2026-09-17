<?php

namespace App\Http\Controllers\Api\Member;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Pilgrim;
use App\Models\PilgrimDocument;
use App\Models\Payment;
use Illuminate\Http\Request;

class BookingController extends Controller
{
    public function index(Request $request)
    {
        $bookings = Booking::where('user_id', $request->user()->id)
            ->with(['package', 'pilgrims.documents', 'payments'])
            ->orderByDesc('created_at')
            ->paginate(10);

        return response()->json(['success' => true, 'data' => $bookings]);
    }

    public function show(Request $request, Booking $booking)
    {
        if ($booking->user_id !== $request->user()->id) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        return response()->json([
            'success' => true,
            'data' => $booking->load(['package.itineraries', 'pilgrims.documents', 'payments']),
        ]);
    }

    public function updatePilgrim(Request $request, Pilgrim $pilgrim)
    {
        // Verify ownership
        if ($pilgrim->booking->user_id !== $request->user()->id) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
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
            'phone' => 'nullable|string|max:20',
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
            'emergency_contact_phone' => 'nullable|string|max:20',
            'emergency_contact_address' => 'nullable|string',
        ], [
            'nik.digits' => 'NIK harus 16 angka.',
            'family_card_number.digits' => 'No. Kartu Keluarga harus 16 angka.',
        ]);

        $pilgrim->update($validated);

        // Update booking document status to review if any docs exist
        $booking = $pilgrim->booking;
        if ($booking->document_status === 'incomplete') {
            $booking->update(['document_status' => 'review']);
        }

        return response()->json(['success' => true, 'message' => 'Data jamaah berhasil disimpan', 'data' => $pilgrim]);
    }

    public function uploadDocument(Request $request, Pilgrim $pilgrim)
    {
        if ($pilgrim->booking->user_id !== $request->user()->id) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $request->validate([
            'document_type' => 'required|in:ktp,kk,passport,photo,marriage_book,birth_certificate,vaccine,mahram,other',
            'file' => 'required|file|max:5120|mimes:jpg,jpeg,png,pdf',
        ]);

        $path = $request->file('file')->store('documents', 'public');

        // Update or create document
        $document = PilgrimDocument::updateOrCreate(
            ['pilgrim_id' => $pilgrim->id, 'document_type' => $request->document_type],
            [
                'file_path' => $path,
                'original_filename' => $request->file('file')->getClientOriginalName(),
                'status' => 'pending',
                'note' => null,
                'validated_by' => null,
                'validated_at' => null,
            ]
        );

        return response()->json(['success' => true, 'message' => 'Dokumen berhasil diupload', 'data' => $document]);
    }

    public function uploadPaymentProof(Request $request, $bookingId)
    {
        $booking = Booking::findOrFail($bookingId);

        if ($booking->user_id !== $request->user()->id) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $request->validate([
            'payment_type' => 'required|in:dp,installment,final',
            'amount' => 'required|numeric|min:1',
            'proof_file' => 'required|file|max:5120|mimes:jpg,jpeg,png,pdf',
            'bank_name' => 'nullable|string|max:100',
        ]);

        $path = $request->file('proof_file')->store('payment_proofs', 'public');

        $payment = Payment::create([
            'booking_id' => $booking->id,
            'payment_type' => $request->payment_type,
            'amount' => $request->amount,
            'method' => 'bank_transfer',
            'proof_file' => $path,
            'bank_name' => $request->bank_name,
            'status' => 'review',
        ]);

        return response()->json(['success' => true, 'message' => 'Bukti pembayaran berhasil diupload. Menunggu validasi admin.', 'data' => $payment]);
    }
}
