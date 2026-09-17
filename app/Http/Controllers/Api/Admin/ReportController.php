<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Commission;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function sales(Request $request)
    {
        $sales = Booking::with(['package'])
            ->selectRaw('DATE(created_at) as date, count(*) as total_bookings, sum(total_amount) as total_sales, sum(paid_amount) as total_paid')
            ->groupBy('date')
            ->orderByDesc('date')
            ->limit(30)
            ->get();

        return response()->json(['success' => true, 'data' => $sales]);
    }

    public function commissions(Request $request)
    {
        $commissions = Commission::with(['agent.user', 'booking'])
            ->orderByDesc('created_at')
            ->get();

        return response()->json(['success' => true, 'data' => $commissions]);
    }

    public function custom(Request $request)
    {
        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'type' => 'required|in:sales,commissions,pilgrims'
        ]);

        $start = $request->start_date . ' 00:00:00';
        $end = $request->end_date . ' 23:59:59';
        $type = $request->type;

        $data = [];

        if ($type === 'sales') {
            $data = Booking::with(['package', 'user'])
                ->whereBetween('created_at', [$start, $end])
                ->orderBy('created_at')
                ->get();
        } elseif ($type === 'commissions') {
            $data = Commission::with(['agent.user', 'booking.package'])
                ->whereBetween('created_at', [$start, $end])
                ->orderBy('created_at')
                ->get();
        } elseif ($type === 'pilgrims') {
            // Kita join ke bookings untuk filter tanggal booking
            $data = \App\Models\Pilgrim::with(['booking.package', 'booking.user'])
                ->whereHas('booking', function($q) use ($start, $end) {
                    $q->whereBetween('created_at', [$start, $end]);
                })
                ->get();
        }

        return response()->json(['success' => true, 'data' => $data]);
    }
}
