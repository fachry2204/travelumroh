<?php

namespace App\Http\Controllers\Api\Member;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        $bookings = Booking::where('user_id', $user->id)
            ->with(['package', 'pilgrims'])
            ->orderByDesc('created_at')
            ->get();

        $stats = [
            'total_bookings' => $bookings->count(),
            'pending' => $bookings->where('booking_status', 'pending')->count(),
            'dp' => $bookings->where('booking_status', 'dp')->count(),
            'paid' => $bookings->where('booking_status', 'paid')->count(),
        ];

        return response()->json([
            'success' => true,
            'data' => [
                'user' => $user->load('profile'),
                'stats' => $stats,
                'recent_bookings' => $bookings->take(5),
            ],
        ]);
    }
}
