<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\PilgrimDocument;
use App\Services\MpwaService;
use Illuminate\Http\Request;

class DocumentController extends Controller
{
    public function index(Request $request)
    {
        $documents = PilgrimDocument::with(['pilgrim.booking.package', 'validator'])
            ->when($request->status, fn($q) => $q->where('status', $request->status))
            ->when($request->document_type, fn($q) => $q->where('document_type', $request->document_type))
            ->orderByDesc('created_at')
            ->paginate($request->per_page ?? 15);

        return response()->json(['success' => true, 'data' => $documents]);
    }

    public function validate(Request $request, PilgrimDocument $document)
    {
        $validated = $request->validate([
            'status' => 'required|in:valid,rejected',
            'note' => 'nullable|string|required_if:status,rejected',
        ]);

        $document->update([
            'status' => $validated['status'],
            'note' => $validated['note'] ?? null,
            'validated_by' => auth()->id(),
            'validated_at' => now(),
        ]);

        // Check if all docs for pilgrim are valid
        $pilgrim = $document->pilgrim;
        $allValid = $pilgrim->documents()->where('status', '!=', 'valid')->doesntExist();

        if ($allValid && $pilgrim->documents()->exists()) {
            $pilgrim->booking->update(['document_status' => 'complete']);
        }

        // --- WA NOTIFICATION ---
        if ($validated['status'] === 'rejected') {
            try {
                $user = $pilgrim->booking->user;
                if ($user && $user->phone) {
                    $mpwa = app(MpwaService::class);
                    $docType = strtoupper(str_replace('_', ' ', $document->document_type));
                    $message = "Assalamu'alaikum *" . $user->name . "*,\n\n";
                    $message .= "Mohon maaf, dokumen *" . $docType . "* atas nama *" . $pilgrim->full_name . "* kami *TOLAK* karena alasan berikut:\n\n";
                    $message .= "_" . $validated['note'] . "_\n\n";
                    $message .= "Mohon segera mengunggah ulang dokumen yang benar melalui Member Area.\n\n";
                    $message .= "Terima kasih,\nPT Travel Umroh Indonesia.";
                    
                    $mpwa->sendMessage($user->phone, $message);
                }
            } catch (\Exception $e) {
                \Log::error("Failed to send WA on document rejection: " . $e->getMessage());
            }
        }

        return response()->json(['success' => true, 'message' => 'Dokumen berhasil divalidasi', 'data' => $document]);
    }

    public function uploadForPilgrim(Request $request, \App\Models\Pilgrim $pilgrim)
    {
        $request->validate([
            'document_type' => 'required|string|max:50',
            'file' => 'required|file|max:10240|mimes:jpg,jpeg,png,pdf',
        ]);

        $path = $request->file('file')->store('documents', 'public');

        $document = PilgrimDocument::updateOrCreate(
            ['pilgrim_id' => $pilgrim->id, 'document_type' => $request->document_type],
            [
                'file_path' => $path,
                'original_filename' => $request->file('file')->getClientOriginalName(),
                'status' => 'valid',
                'note' => null,
                'validated_by' => auth()->id(),
                'validated_at' => now(),
            ]
        );

        return response()->json(['success' => true, 'message' => 'Dokumen Siskopatuh berhasil diupload', 'data' => $document]);
    }
}
