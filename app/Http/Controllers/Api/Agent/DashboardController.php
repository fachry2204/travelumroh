<?php

namespace App\Http\Controllers\Api\Agent;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Commission;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $agent = $user->agent;

        if (!$agent) {
            return response()->json(['success' => false, 'message' => 'Profil agen tidak ditemukan.'], 404);
        }

        $bookings = Booking::where('agent_id', $agent->id)->with(['package', 'pilgrims'])->get();
        $commissions = Commission::where('agent_id', $agent->id)->get();

        $stats = [
            'total_bookings' => $bookings->count(),
            'total_pilgrims' => $bookings->sum('total_pilgrims'),
            'pending_commissions' => $commissions->where('status', 'pending')->sum('commission_amount'),
            'approved_commissions' => $commissions->where('status', 'approved')->sum('commission_amount'),
            'paid_commissions' => $commissions->where('status', 'paid')->sum('commission_amount'),
            'referral_link' => url('/daftar?ref=' . $agent->referral_code),
            'referral_code' => $agent->referral_code,
            'agent_code' => $agent->agent_code,
        ];

        return response()->json([
            'success' => true,
            'data' => [
                'agent' => $agent->load('user', 'representative'),
                'stats' => $stats,
                'recent_bookings' => $bookings->take(5)->values(),
            ],
        ]);
    }

    public function bookings(Request $request)
    {
        $agent = $request->user()->agent;
        $bookings = Booking::where('agent_id', $agent->id)
            ->with(['package', 'pilgrims'])
            ->when($request->status, fn($q) => $q->where('booking_status', $request->status))
            ->orderByDesc('created_at')
            ->paginate(15);

        return response()->json(['success' => true, 'data' => $bookings]);
    }

    public function commissions(Request $request)
    {
        $agent = $request->user()->agent;
        $commissions = Commission::where('agent_id', $agent->id)
            ->with('booking.package')
            ->when($request->status, fn($q) => $q->where('status', $request->status))
            ->orderByDesc('created_at')
            ->paginate(15);

        return response()->json(['success' => true, 'data' => $commissions]);
    }
}
