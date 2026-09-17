<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Manifest Keberangkatan {{ $departure->group_code }}</title>
    <style>
        body { font-family: 'Helvetica', 'Arial', sans-serif; color: #333; line-height: 1.5; font-size: 11px; }
        .header { text-align: center; border-bottom: 2px solid #000; padding-bottom: 10px; margin-bottom: 20px; }
        .title { font-size: 20px; font-weight: bold; margin: 0; text-transform: uppercase; }
        .info-table { width: 100%; margin-bottom: 20px; font-size: 12px; }
        .info-table td { padding: 3px 0; }
        .info-table .label { font-weight: bold; width: 120px; }
        .data-table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        .data-table th, .data-table td { border: 1px solid #000; padding: 6px; text-align: left; }
        .data-table th { background-color: #f3f4f6; font-weight: bold; text-align: center; }
        .text-center { text-align: center; }
        .footer { margin-top: 30px; text-align: right; font-size: 12px; }
    </style>
</head>
<body>
    <div class="header">
        <h1 class="title">MANIFEST JAMAAH UMROH</h1>
        <p style="margin: 5px 0;">PT Travel Umroh Indonesia</p>
    </div>

    <table class="info-table">
        <tr>
            <td width="50%">
                <table width="100%">
                    <tr><td class="label">Kode Grup</td><td>: <strong>{{ $departure->group_code }}</strong></td></tr>
                    <tr><td class="label">Paket</td><td>: {{ $departure->package->name }}</td></tr>
                    <tr><td class="label">Tanggal Keberangkatan</td><td>: {{ \Carbon\Carbon::parse($departure->departure_date)->format('d F Y') }}</td></tr>
                    <tr><td class="label">Tanggal Kepulangan</td><td>: {{ \Carbon\Carbon::parse($departure->return_date)->format('d F Y') }}</td></tr>
                </table>
            </td>
            <td width="50%">
                <table width="100%">
                    <tr><td class="label">Muthawif / Guide</td><td>: {{ $departure->guide_name ?? '-' }}</td></tr>
                    <tr><td class="label">Maskapai</td><td>: {{ $departure->airline ?? '-' }}</td></tr>
                    <tr><td class="label">Flight Pergi</td><td>: {{ $departure->flight_number_departure ?? '-' }}</td></tr>
                    <tr><td class="label">Flight Pulang</td><td>: {{ $departure->flight_number_return ?? '-' }}</td></tr>
                </table>
            </td>
        </tr>
    </table>

    <table class="data-table">
        <thead>
            <tr>
                <th width="3%">No</th>
                <th width="17%">Nama Sesuai Paspor</th>
                <th width="12%">No. Paspor</th>
                <th width="8%">L/P</th>
                <th width="12%">Tempat Lahir</th>
                <th width="10%">Tgl Lahir</th>
                <th width="8%">Kamar</th>
                <th width="8%">Bus</th>
                <th width="22%">Keterangan Khusus</th>
            </tr>
        </thead>
        <tbody>
            @forelse($departure->pilgrims as $index => $dp)
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td>{{ $dp->pilgrim->passport_name ?? $dp->pilgrim->full_name }}</td>
                <td class="text-center">{{ $dp->pilgrim->passport_number ?? '-' }}</td>
                <td class="text-center">{{ $dp->pilgrim->gender == 'male' ? 'L' : ($dp->pilgrim->gender == 'female' ? 'P' : '-') }}</td>
                <td>{{ $dp->pilgrim->birth_place ?? '-' }}</td>
                <td class="text-center">{{ $dp->pilgrim->birth_date ? \Carbon\Carbon::parse($dp->pilgrim->birth_date)->format('d/m/Y') : '-' }}</td>
                <td class="text-center">{{ $dp->room_number ?? '-' }}</td>
                <td class="text-center">{{ $dp->bus_number ?? '-' }}</td>
                <td>
                    @if($dp->pilgrim->medical_history || $dp->pilgrim->special_needs)
                        Medis: {{ $dp->pilgrim->medical_history }}. Kebutuhan: {{ $dp->pilgrim->special_needs }}.
                    @else
                        -
                    @endif
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="9" class="text-center">Belum ada jamaah dalam grup keberangkatan ini.</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        <p>Dicetak pada: {{ now()->format('d F Y H:i:s') }}</p>
    </div>
</body>
</html>
