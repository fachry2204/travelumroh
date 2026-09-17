<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Kwitansi {{ $payment->payment_number }}</title>
    <style>
        body { font-family: 'Helvetica', 'Arial', sans-serif; color: #333; line-height: 1.6; font-size: 14px; }
        .container { border: 2px solid #e2e8f0; padding: 40px; margin: 20px; border-radius: 8px; position: relative; }
        .watermark { position: absolute; top: 30%; left: 15%; opacity: 0.05; font-size: 80px; transform: rotate(-30deg); color: #000; font-weight: bold; }
        .header { text-align: center; margin-bottom: 40px; }
        .title { color: #075985; font-size: 28px; font-weight: bold; margin: 0; letter-spacing: 2px; }
        .company { color: #64748b; font-size: 14px; margin-top: 5px; }
        .content { margin-bottom: 40px; }
        .row { display: flex; margin-bottom: 15px; border-bottom: 1px dashed #cbd5e1; padding-bottom: 5px; }
        .label { width: 200px; font-weight: bold; color: #475569; display: inline-block; }
        .value { display: inline-block; font-size: 16px; }
        .amount-box { background: #f0f9ff; border: 1px solid #bae6fd; padding: 15px; text-align: center; margin-top: 30px; border-radius: 8px; }
        .amount-value { font-size: 24px; font-weight: bold; color: #0369a1; }
        .signatures { margin-top: 50px; width: 100%; }
        .signatures td { width: 50%; text-align: center; }
        .sign-area { height: 80px; }
    </style>
</head>
<body>
    <div class="container">
        @if($payment->status === 'approved')
            <div class="watermark">LUNAS / APPROVED</div>
        @endif

        <div class="header">
            <h1 class="title">KWITANSI PEMBAYARAN</h1>
            <div class="company">PT Travel Umroh Indonesia</div>
        </div>

        <div class="content">
            <div style="margin-bottom: 15px; border-bottom: 1px dashed #cbd5e1; padding-bottom: 5px;">
                <span class="label">No. Kwitansi</span>
                <span class="value">: <strong>{{ $payment->payment_number }}</strong></span>
            </div>
            <div style="margin-bottom: 15px; border-bottom: 1px dashed #cbd5e1; padding-bottom: 5px;">
                <span class="label">Telah terima dari</span>
                <span class="value">: {{ $payment->booking->user->name ?? 'Jamaah' }}</span>
            </div>
            <div style="margin-bottom: 15px; border-bottom: 1px dashed #cbd5e1; padding-bottom: 5px;">
                <span class="label">Untuk Pembayaran</span>
                <span class="value">: {{ strtoupper($payment->payment_type) }} - Pembayaran Paket {{ $payment->booking->package->name }} (Booking: {{ $payment->booking->booking_number }})</span>
            </div>
            <div style="margin-bottom: 15px; border-bottom: 1px dashed #cbd5e1; padding-bottom: 5px;">
                <span class="label">Metode Pembayaran</span>
                <span class="value">: {{ $payment->method }}</span>
            </div>
            <div style="margin-bottom: 15px; border-bottom: 1px dashed #cbd5e1; padding-bottom: 5px;">
                <span class="label">Tanggal Diterima</span>
                <span class="value">: {{ $payment->created_at->format('d F Y') }}</span>
            </div>
        </div>

        <div class="amount-box">
            <div style="color: #64748b; font-size: 14px; margin-bottom: 5px;">Jumlah Uang (Rp)</div>
            <div class="amount-value">Rp {{ number_format($payment->amount, 0, ',', '.') }}</div>
        </div>

        <table class="signatures">
            <tr>
                <td>
                    <p>Pembayar,</p>
                    <div class="sign-area"></div>
                    <p><strong>{{ $payment->booking->user->name ?? 'Jamaah' }}</strong></p>
                </td>
                <td>
                    <p>Penerima,</p>
                    <div class="sign-area"></div>
                    <p><strong>PT Travel Umroh Indonesia</strong></p>
                </td>
            </tr>
        </table>
    </div>
</body>
</html>
