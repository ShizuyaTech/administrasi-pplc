<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Overtime - {{ ucfirst($type) }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: Arial, sans-serif;
            font-size: 11px;
            line-height: 1.4;
            color: #000;
            padding: 30px;
        }
        
        .header {
            text-align: center;
            margin-bottom: 25px;
            border-bottom: 3px solid #000;
            padding-bottom: 15px;
        }
        
        .header h1 {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 5px;
            text-transform: uppercase;
        }
        
        .header h2 {
            font-size: 14px;
            font-weight: normal;
            margin-bottom: 3px;
        }
        
        .period-info {
            margin-bottom: 20px;
            background-color: #f5f5f5;
            padding: 10px;
            border: 1px solid #ddd;
        }
        
        .period-info table {
            width: 100%;
        }
        
        .period-info td {
            padding: 3px 5px;
        }
        
        .period-info td:first-child {
            width: 30%;
            font-weight: bold;
        }
        
        .date-group {
            margin-bottom: 20px;
            page-break-inside: avoid;
        }
        
        .date-header {
            background-color: #333;
            color: white;
            padding: 8px;
            font-weight: bold;
            font-size: 12px;
            margin-bottom: 10px;
        }
        
        .type-section {
            margin-bottom: 15px;
        }
        
        .type-header {
            background-color: #666;
            color: white;
            padding: 6px;
            font-weight: bold;
            margin-bottom: 8px;
        }
        
        .batch-section {
            margin-bottom: 12px;
            border: 1px solid #ddd;
            padding: 10px;
            background-color: #fafafa;
        }
        
        .batch-info {
            margin-bottom: 8px;
            font-size: 10px;
        }
        
        .batch-info strong {
            display: inline-block;
            width: 120px;
        }
        
        table.overtime-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 5px;
        }
        
        table.overtime-table th,
        table.overtime-table td {
            border: 1px solid #333;
            padding: 5px;
            text-align: left;
        }
        
        table.overtime-table th {
            background-color: #e0e0e0;
            font-weight: bold;
            text-align: center;
            font-size: 10px;
        }
        
        table.overtime-table td {
            font-size: 10px;
        }
        
        table.overtime-table .text-center {
            text-align: center;
        }
        
        table.overtime-table .text-right {
            text-align: right;
        }
        
        .batch-total {
            text-align: right;
            font-weight: bold;
            margin-top: 5px;
            padding: 5px;
            background-color: #e8e8e8;
            border: 1px solid #ccc;
        }
        
        .grand-total {
            margin-top: 25px;
            padding: 15px;
            background-color: #f0f0f0;
            border: 2px solid #333;
            font-weight: bold;
            font-size: 13px;
        }
        
        .grand-total table {
            width: 100%;
        }
        
        .grand-total td:first-child {
            width: 70%;
            text-align: left;
        }
        
        .grand-total td:last-child {
            width: 30%;
            text-align: right;
        }
        
        .signatures {
            margin-top: 40px;
            page-break-inside: avoid;
        }
        
        .signature-row {
            display: table;
            width: 100%;
            margin-top: 20px;
        }
        
        .signature-box {
            display: table-cell;
            width: 48%;
            text-align: center;
            vertical-align: top;
        }
        
        .signature-box:first-child {
            margin-right: 4%;
        }
        
        .signature-title {
            font-weight: bold;
            margin-bottom: 10px;
            font-size: 12px;
        }
        
        .signature-image {
            min-height: 60px;
            border: 1px dashed #999;
            margin-bottom: 5px;
            display: flex;
            align-items: center;
            justify-content: center;
            background-color: #fafafa;
        }
        
        .signature-image img {
            max-height: 55px;
            max-width: 180px;
        }
        
        .signature-name {
            border-bottom: 1px solid #000;
            padding: 3px 0;
            margin: 0 20px 3px;
            font-weight: bold;
        }
        
        .signature-role {
            font-size: 10px;
            color: #666;
        }
        
        .footer {
            margin-top: 40px;
            text-align: center;
            font-size: 9px;
            color: #666;
            border-top: 1px solid #ddd;
            padding-top: 10px;
        }
        
        .status-badge {
            display: inline-block;
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 9px;
            font-weight: bold;
        }
        
        .status-approved {
            background-color: #d4edda;
            color: #155724;
        }
        
        .no-data {
            text-align: center;
            padding: 30px;
            color: #999;
            font-style: italic;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="header">
        <h1>Laporan Overtime</h1>
        <h2>{{ $company_name ?? 'PT. NAMA PERUSAHAAN' }}</h2>
        <div style="font-size: 12px; margin-top: 5px;">
            Tipe: <strong>{{ $type == 'regular' ? 'HARIAN' : 'SUSULAN' }}</strong>
        </div>
    </div>
    
    <!-- Period Info -->
    <div class="period-info">
        <table>
            <tr>
                <td>Periode</td>
                <td>: {{ $date_from }} s/d {{ $date_to }}</td>
            </tr>
            <tr>
                <td>Tanggal Generate</td>
                <td>: <span id="pdf-print-datetime">{{ userNow()->format('d F Y, H:i') }}</span></td>
            </tr>
            <tr>
                <td>Total Karyawan</td>
                <td>: {{ $total_employees }} orang</td>
            </tr>
            <tr>
                <td>Total Jam Overtime</td>
                <td>: {{ number_format($total_hours, 1) }} jam</td>
            </tr>
        </table>
    </div>
    
    @if(count($grouped) > 0)
        <!-- Grouped Overtimes -->
        @foreach($grouped as $dateKey => $dateGroup)
            <div class="date-group">
                <div class="date-header">
                    {{ $dateGroup['date']->format('d F Y (l)') }}
                </div>
                
                @foreach($dateGroup['types'] as $typeKey => $typeGroup)
                    <div class="type-section">
                        <div class="type-header">
                            {{ $typeKey == 'regular' ? 'OVERTIME HARIAN' : 'OVERTIME SUSULAN' }}
                        </div>
                        
                        @foreach($typeGroup['batches'] as $batchKey => $batch)
                            <div class="batch-section">
                                <!-- Batch Info -->
                                <div class="batch-info">
                                    <div><strong>Leader/Foreman:</strong> {{ $batch['creator']->name }} ({{ $batch['section']->name }})</div>
                                    <div><strong>Waktu Overtime:</strong> {{ substr($batch['start_time'], 0, 5) }} - {{ substr($batch['end_time'], 0, 5) }} WIB</div>
                                    <div><strong>Jumlah Karyawan:</strong> {{ $batch['overtimes']->count() }} orang</div>
                                </div>
                                
                                <!-- Employees Table -->
                                <table class="overtime-table">
                                    <thead>
                                        <tr>
                                            <th style="width: 5%;">No</th>
                                            <th style="width: 35%;">Nama Karyawan</th>
                                            <th style="width: 10%;">Jam</th>
                                            <th style="width: 50%;">Deskripsi Pekerjaan</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($batch['overtimes'] as $index => $overtime)
                                            <tr>
                                                <td class="text-center">{{ $index + 1 }}</td>
                                                <td>{{ $overtime->employee_name }}</td>
                                                <td class="text-center">{{ number_format($overtime->total_hours, 1) }}</td>
                                                <td>{{ $overtime->work_description }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                                
                                <!-- Batch Total -->
                                <div class="batch-total">
                                    Total: {{ $batch['overtimes']->count() }} karyawan, 
                                    {{ number_format($batch['overtimes']->sum('total_hours'), 1) }} jam
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endforeach
            </div>
        @endforeach
    @else
        <div class="no-data">
            Tidak ada data overtime untuk periode ini.
        </div>
    @endif
    
    <!-- Grand Total -->
    <div class="grand-total">
        <table>
            <tr>
                <td>TOTAL SELURUH KARYAWAN</td>
                <td><strong>{{ $total_employees }} ORANG</strong></td>
            </tr>
            <tr>
                <td>TOTAL JAM OVERTIME</td>
                <td><strong>{{ number_format($total_hours, 1) }} JAM</strong></td>
            </tr>
        </table>
    </div>
    
    <!-- Signatures -->
    <div class="signatures">
        <div class="signature-row">
            <!-- Supervisor Signature -->
            <div class="signature-box">
                <div class="signature-title">Disetujui Tahap 1 (Supervisor)</div>
                <div class="signature-image">
                    @if($supervisor && $supervisor->hasSignature())
                        <img src="{{ public_path('storage/' . $supervisor->signature_path) }}" alt="Signature">
                    @else
                        <span style="color: #999; font-size: 10px;">(Signature tidak tersedia)</span>
                    @endif
                </div>
                <div class="signature-name">{{ $supervisor ? $supervisor->name : '-' }}</div>
                <div class="signature-role">Supervisor</div>
            </div>
            
            <!-- Manager Signature -->
            <div class="signature-box">
                <div class="signature-title">Final Approval (Manager)</div>
                <div class="signature-image">
                    @if($manager && $manager->hasSignature())
                        <img src="{{ public_path('storage/' . $manager->signature_path) }}" alt="Signature">
                    @else
                        <span style="color: #999; font-size: 10px;">(Signature tidak tersedia)</span>
                    @endif
                </div>
                <div class="signature-name">{{ $manager ? $manager->name : '-' }}</div>
                <div class="signature-role">Manager</div>
            </div>
        </div>
    </div>
    
    <!-- Footer -->
    <div class="footer">
        Dokumen ini di-generate secara otomatis oleh sistem administrasi.<br>
        {{ $company_name ?? 'PT. NAMA PERUSAHAAN' }} &copy; {{ date('Y') }}
    </div>
</body>
</html>
