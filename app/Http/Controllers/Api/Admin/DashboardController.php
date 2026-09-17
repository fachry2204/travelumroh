<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Pilgrim;
use App\Models\Payment;
use App\Models\Commission;
use App\Models\Package;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'total_bookings' => Booking::count(),
            'total_pilgrims' => Pilgrim::count(),
            'pending_bookings' => Booking::where('booking_status', 'pending')->count(),
            'dp_bookings' => Booking::where('booking_status', 'dp')->count(),
            'paid_bookings' => Booking::where('booking_status', 'paid')->count(),
            'cancelled_bookings' => Booking::where('booking_status', 'cancelled')->count(),
            'incomplete_docs' => Booking::where('document_status', 'incomplete')->count(),
            'total_revenue' => Payment::where('status', 'approved')->sum('amount'),
            'pending_commissions' => Commission::where('status', 'pending')->sum('commission_amount'),
            'approved_commissions' => Commission::where('status', 'approved')->sum('commission_amount'),
            'active_packages' => Package::where('status', 'active')->count(),
        ];

        // Monthly bookings chart data (last 6 months)
        $monthlyBookings = Booking::selectRaw('MONTH(created_at) as month, YEAR(created_at) as year, COUNT(*) as total')
            ->whereDate('created_at', '>=', now()->subMonths(6))
            ->groupByRaw('YEAR(created_at), MONTH(created_at)')
            ->orderByRaw('YEAR(created_at), MONTH(created_at)')
            ->get();

        // Monthly revenue
        $monthlyRevenue = Payment::where('status', 'approved')
            ->selectRaw('MONTH(created_at) as month, YEAR(created_at) as year, SUM(amount) as total')
            ->whereDate('created_at', '>=', now()->subMonths(6))
            ->groupByRaw('YEAR(created_at), MONTH(created_at)')
            ->orderByRaw('YEAR(created_at), MONTH(created_at)')
            ->get();

        // Recent bookings
        $recentBookings = Booking::with(['package', 'pilgrims'])
            ->orderByDesc('created_at')
            ->limit(10)
            ->get();

        return response()->json([
            'success' => true,
            'data' => [
                'stats' => $stats,
                'monthly_bookings' => $monthlyBookings,
                'monthly_revenue' => $monthlyRevenue,
                'recent_bookings' => $recentBookings,
            ],
        ]);
    }
}
