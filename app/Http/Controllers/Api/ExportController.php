<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Payment;
use App\Models\Departure;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class ExportController extends Controller
{
    public function invoice(Booking $booking)
    {
        $booking->load(['user', 'package', 'payments']);
        
        $pdf = Pdf::loadView('pdf.invoice', compact('booking'));
        
        return $pdf->download('invoice-' . $booking->booking_number . '.pdf');
    }

    public function receipt(Payment $payment)
    {
        $payment->load(['booking.user', 'booking.package']);
        
        $pdf = Pdf::loadView('pdf.receipt', compact('payment'));
        
        return $pdf->download('kwitansi-' . $payment->payment_number . '.pdf');
    }

    public function manifest(Departure $departure)
    {
        $departure->load(['package', 'pilgrims.pilgrim']);
        
        $pdf = Pdf::loadView('pdf.manifest', compact('departure'))->setPaper('a4', 'landscape');
        
        return $pdf->download('manifest-' . $departure->group_code . '.pdf');
    }
}
