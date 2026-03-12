<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SPD - {{ $businessTrip->letter_number }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Times New Roman', Times, serif; font-size: 12pt; line-height: 1.6; padding: 2cm; }
        .header { text-align: center; margin-bottom: 30px; border-bottom: 3px solid #000; padding-bottom: 15px; }
        .header h1 { font-size: 18pt; font-weight: bold; margin-bottom: 5px; }
        .header h2 { font-size: 14pt; font-weight: bold; margin-bottom: 5px; }
        .header p { font-size: 11pt; }
        .letter-number { text-align: center; margin-bottom: 20px; font-weight: bold; text-decoration: underline; }
        .content { margin-bottom: 30px; }
        .content table { width: 100%; border-collapse: collapse; }
        .content table td { padding: 5px; vertical-align: top; }
        .content table td:first-child { width: 180px; }
        .content table td:nth-child(2) { width: 10px; }
        .signature { margin-top: 40px; display: flex; justify-content: space-between; }
        .signature-box { width: 45%; }
        .signature-box p { margin-bottom: 80px; }
        .signature-box .name { font-weight: bold; text-decoration: underline; }
        .footer { margin-top: 40px; border-top: 1px solid #000; padding-top: 10px; font-size: 10pt; color: #666; }
        @media print {
            body { padding: 1cm; }
            .no-print { display: none; }
            @page { size: A4; margin: 1cm; }
        }
        .print-button { position: fixed; top: 20px; right: 20px; padding: 10px 20px; background-color: #4F46E5; color: white; border: none; border-radius: 8px; cursor: pointer; font-size: 14px; }
        .print-button:hover { background-color: #4338CA; }
    </style>
</head>
<body>
    <button onclick="window.print()" class="print-button no-print">🖨️ Cetak SPD</button>

    <div class="header">
        <h1>PT. Inti Pantja Press Industri</h1>
        <h2>SURAT PERJALANAN DINAS</h2>
        <p>{{ $businessTrip->section->name }}</p>
    </div>

    <div class="letter-number">
        {{ $businessTrip->letter_number }}
    </div>

    <div class="content">
        <p style="margin-bottom: 15px;">Yang bertanda tangan di bawah ini:</p>
        <table>
            <tr>
                <td>Nama</td>
                <td>:</td>
                <td><strong>{{ $businessTrip->employee_name }}</strong></td>
            </tr>
            <tr>
                <td>Seksi/Bagian</td>
                <td>:</td>
                <td>{{ $businessTrip->section->name }}</td>
            </tr>
            <tr>
                <td>Tujuan Perjalanan</td>
                <td>:</td>
                <td><strong>{{ $businessTrip->destination }}</strong></td>
            </tr>
            <tr>
                <td>Tanggal Berangkat</td>
                <td>:</td>
                <td>{{ $businessTrip->departure_date->translatedFormat('d F Y') }}</td>
            </tr>
            <tr>
                <td>Tanggal Kembali</td>
                <td>:</td>
                <td>{{ $businessTrip->return_date->translatedFormat('d F Y') }}</td>
            </tr>
            <tr>
                <td>Lama Perjalanan</td>
                <td>:</td>
                <td>{{ $businessTrip->departure_date->diffInDays($businessTrip->return_date) + 1 }} hari</td>
            </tr>
            <tr>
                <td>Alat Transportasi</td>
                <td>:</td>
                <td>{{ $businessTrip->transport }}</td>
            </tr>
            @if($businessTrip->estimated_cost)
            <tr>
                <td>Estimasi Biaya</td>
                <td>:</td>
                <td>Rp {{ number_format($businessTrip->estimated_cost, 0, ',', '.') }}</td>
            </tr>
            @endif
            <tr>
                <td>Keperluan/Tujuan</td>
                <td>:</td>
                <td style="text-align: justify;">{{ $businessTrip->purpose }}</td>
            </tr>
        </table>
    </div>

    <div class="content">
        <p>Demikian surat perjalanan dinas ini dibuat untuk dapat dipergunakan sebagaimana mestinya.</p>
    </div>

    <div class="signature">
        <div class="signature-box">
            <p>Pemohon,</p>
            <p class="name">{{ $businessTrip->employee_name }}</p>
        </div>
        <div class="signature-box" style="text-align: right;">
            <p>Karawang, {{ now()->translatedFormat('d F Y') }}</p>
            <p>Mengetahui,<br>Leader {{ $businessTrip->section->name }}</p>
            <p class="name">{{ $businessTrip->approver ? $businessTrip->approver->name : '(................................)' }}</p>
        </div>
    </div>

    <div class="footer">
        <p>Status: 
            @if($businessTrip->status == 'draft')
                <strong>DRAFT</strong>
            @elseif($businessTrip->status == 'approved')
                <strong style="color: green;">APPROVED</strong>
            @elseif($businessTrip->status == 'completed')
                <strong style="color: blue;">COMPLETED</strong>
            @else
                <strong style="color: red;">CANCELLED</strong>
            @endif
        </p>
        <p style="margin-top: 5px; font-size: 9pt;">Dicetak pada: {{ now()->translatedFormat('d F Y H:i') }} | Oleh: {{ auth()->user()->name }}</p>
    </div>
</body>
</html>
