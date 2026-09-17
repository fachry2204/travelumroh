<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Invoice {{ $booking->booking_number }}</title>
    <style>
        body { font-family: 'Helvetica', 'Arial', sans-serif; color: #333; line-height: 1.5; font-size: 14px; }
        .header { text-align: center; border-bottom: 2px solid #38BDF8; padding-bottom: 20px; margin-bottom: 30px; }
        .title { color: #075985; font-size: 24px; font-weight: bold; margin: 0; }
        .info-table { width: 100%; margin-bottom: 30px; }
        .info-table td { padding: 5px 0; vertical-align: top; }
        .info-table .label { font-weight: bold; width: 150px; color: #64748b; }
        .table { width: 100%; border-collapse: collapse; margin-bottom: 30px; }
        .table th { background-color: #f0f9ff; color: #075985; padding: 12px; text-align: left; border-bottom: 2px solid #bae6fd; }
        .table td { padding: 12px; border-bottom: 1px solid #e2e8f0; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .total-row td { font-weight: bold; font-size: 16px; background-color: #f8fafc; }
        .footer { margin-top: 50px; text-align: center; font-size: 12px; color: #94a3b8; }
        .status { display: inline-block; padding: 4px 12px; border-radius: 4px; font-weight: bold; font-size: 12px; text-transform: uppercase; }
        .status.paid { background: #d1fae5; color: #065f46; }
        .status.pending { background: #fef3c7; color: #92400e; }
        .status.dp { background: #e0f2fe; color: #0369a1; }
    </style>
</head>
<body>
    <div class="header">
        <h1 class="title">INVOICE PEMBAYARAN</h1>
        <p style="margin: 5px 0; color: #64748b;">PT Travel Umroh Indonesia - Terpercaya & Amanah</p>
    </div>

    <table class="info-table">
        <tr>
            <td width="50%">
                <table width="100%">
                    <tr><td class="label">No. Invoice</td><td>: <strong>{{ $booking->booking_number }}</strong></td></tr>
                    <tr><td class="label">Tanggal</td><td>: {{ $booking->created_at->format('d M Y') }}</td></tr>
                    <tr>
                        <td class="label">Status</td>
                        <td>: <span class="status {{ strtolower($booking->booking_status) }}">{{ $booking->booking_status }}</span></td>
                    </tr>
                </table>
            </td>
            <td width="50%">
                <table width="100%">
                    <tr><td class="label">Nama Jamaah</td><td>: {{ $booking->user->name ?? 'N/A' }}</td></tr>
                    <tr><td class="label">Email</td><td>: {{ $booking->user->email ?? '-' }}</td></tr>
                    <tr><td class="label">No. HP</td><td>: {{ $booking->user->phone ?? '-' }}</td></tr>
                </table>
            </td>
        </tr>
    </table>

    <table class="table">
        <thead>
            <tr>
                <th>Deskripsi</th>
                <th class="text-center">Tipe Kamar</th>
                <th class="text-center">Jumlah Jamaah</th>
                <th class="text-right">Total</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>
                    <strong>Paket Umroh: {{ $booking->package->name }}</strong><br>
                    <span style="font-size: 12px; color: #64748b;">Tanggal Berangkat: {{ \Carbon\Carbon::parse($booking->package->departure_date)->format('d M Y') }}</span>
                </td>
                <td class="text-center" style="text-transform: capitalize;">{{ $booking->room_type }}</td>
                <td class="text-center">{{ $booking->total_pilgrims }} Orang</td>
                <td class="text-right">Rp {{ number_format($booking->total_amount, 0, ',', '.') }}</td>
            </tr>
            <tr class="total-row">
                <td colspan="3" class="text-right">Total Tagihan</td>
                <td class="text-right">Rp {{ number_format($booking->total_amount, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td colspan="3" class="text-right" style="color: #059669;">Sudah Dibayar</td>
                <td class="text-right" style="color: #059669;">- Rp {{ number_format($booking->paid_amount, 0, ',', '.') }}</td>
            </tr>
            <tr class="total-row">
                <td colspan="3" class="text-right" style="color: #dc2626;">Sisa Tagihan</td>
                <td class="text-right" style="color: #dc2626;">Rp {{ number_format($booking->total_amount - $booking->paid_amount, 0, ',', '.') }}</td>
            </tr>
        </tbody>
    </table>

    @if($booking->payments->count() > 0)
    <h3 style="color: #075985; font-size: 16px; border-bottom: 1px solid #e2e8f0; padding-bottom: 5px;">Riwayat Pembayaran</h3>
    <table class="table" style="font-size: 12px;">
        <thead>
            <tr>
                <th>Tanggal</th>
                <th>No. Transaksi</th>
                <th>Jenis Pembayaran</th>
                <th>Metode</th>
                <th>Status</th>
                <th class="text-right">Jumlah</th>
            </tr>
        </thead>
        <tbody>
            @foreach($booking->payments as $payment)
            <tr>
                <td>{{ $payment->created_at->format('d M Y') }}</td>
                <td>{{ $payment->payment_number }}</td>
                <td style="text-transform: capitalize;">{{ $payment->payment_type }}</td>
                <td>{{ $payment->method }}</td>
                <td>{{ $payment->status }}</td>
                <td class="text-right">Rp {{ number_format($payment->amount, 0, ',', '.') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @endif

    <div class="footer">
        <p>Terima kasih atas kepercayaan Anda menggunakan layanan PT Travel Umroh Indonesia.</p>
        <p>Dokumen ini dicetak otomatis dan sah tanpa tanda tangan.</p>
    </div>
</body>
</html>
