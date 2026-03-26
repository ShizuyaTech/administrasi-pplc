<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan {{ $type_label }} - {{ $date_from }} s/d {{ $date_to }}</title>
    <style>
        /* General Styles */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Arial', sans-serif;
            font-size: 11pt;
            line-height: 1.4;
            color: #000;
            background: #fff;
            padding: 20px;
        }
        
        /* Print Styles */
        @media print {
            body {
                padding: 0;
            }
            .no-print {
                display: none !important;
            }
            .page-break {
                page-break-after: always;
            }
            table {
                page-break-inside: auto;
            }
            tr {
                page-break-inside: avoid;
                page-break-after: auto;
            }
        }
        
        /* Header */
        .report-header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 3px solid #000;
            padding-bottom: 15px;
        }
        
        .company-name {
            font-size: 18pt;
            font-weight: bold;
            margin-bottom: 5px;
        }
        
        .report-title {
            font-size: 14pt;
            font-weight: bold;
            margin: 10px 0;
        }
        
        .report-period {
            font-size: 10pt;
            color: #333;
        }
        
        /* Report Info */
        .report-info {
            margin-bottom: 20px;
            font-size: 10pt;
        }
        
        .report-info table {
            width: 100%;
            border-collapse: collapse;
        }
        
        .report-info td {
            padding: 3px 0;
        }
        
        .report-info td:first-child {
            width: 150px;
            font-weight: bold;
        }
        
        /* Data Table */
        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            font-size: 9pt;
        }
        
        .data-table th,
        .data-table td {
            border: 1px solid #000;
            padding: 6px 8px;
            text-align: left;
        }
        
        .data-table th {
            background-color: #f0f0f0;
            font-weight: bold;
            text-align: center;
        }
        
        .data-table td.center {
            text-align: center;
        }
        
        .data-table td.right {
            text-align: right;
        }
        
        .data-table .group-header {
            background-color: #e8e8e8;
            font-weight: bold;
        }
        
        /* Summary */
        .summary-section {
            margin-top: 20px;
            margin-bottom: 30px;
        }
        
        .summary-box {
            display: inline-block;
            border: 2px solid #000;
            padding: 10px 20px;
            margin-right: 20px;
        }
        
        .summary-label {
            font-size: 9pt;
            color: #666;
        }
        
        .summary-value {
            font-size: 14pt;
            font-weight: bold;
        }
        
        /* Signatures */
        .signatures-section {
            margin-top: 40px;
            display: flex;
            justify-content: space-between;
        }
        
        .signature-box {
            width: 45%;
            text-align: center;
        }
        
        .signature-title {
            font-weight: bold;
            margin-bottom: 10px;
            font-size: 11pt;
        }
        
        .signature-image {
            height: 80px;
            margin: 15px 0;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .signature-image img {
            max-height: 80px;
            max-width: 200px;
        }
        
        .signature-name {
            font-weight: bold;
            border-top: 1px solid #000;
            padding-top: 5px;
            display: inline-block;
            min-width: 200px;
        }
        
        .signature-date {
            font-size: 9pt;
            color: #666;
            margin-top: 3px;
        }
        
        /* Print Button */
        .print-button {
            position: fixed;
            top: 20px;
            right: 20px;
            background-color: #4F46E5;
            color: white;
            border: none;
            padding: 12px 24px;
            font-size: 14px;
            font-weight: bold;
            border-radius: 8px;
            cursor: pointer;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            z-index: 1000;
        }
        
        .print-button:hover {
            background-color: #4338CA;
        }
        
        /* Notes */
        .notes-section {
            margin-top: 30px;
            font-size: 9pt;
            border-top: 1px solid #ccc;
            padding-top: 10px;
        }
    </style>
</head>
<body>
    
    <!-- Print Button (Hidden when printing) -->
    <button class="print-button no-print" onclick="window.print()">
        🖨️ Print / Save as PDF
    </button>
    
    <!-- Report Header -->
    <div class="report-header">
        {{-- <div class="company-name">{{ $company_name }}</div>
        <div class="report-title">LAPORAN {{ strtoupper($type_label) }}</div> --}}
        <div class="company-name">PT. IPPI - PPLC Dept.</div>
        <div class="report-title">SURAT PERINTAH LEMBUR</div>
        {{-- <div class="report-period">Periode: {{ $date_from }} s/d {{ $date_to }}</div> --}}
    </div>
    
    <!-- Report Info -->
    <div class="report-info">
        <table>
            <tr>
                <td>Tanggal Cetak</td>
                <td>: <span id="print-datetime">-</span></td>
            </tr>
            <tr>
                <td>Dicetak Oleh</td>
                <td>: {{ auth()->user()->name }}</td>
            </tr>
        </table>
    </div>
    
    <!-- Data Table -->
    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 3%;">No</th>
                <th style="width: 10%;">Tanggal</th>
                {{-- <th style="width: 12%;">Batch ID</th> --}}
                <th style="width: 20%;">Nama Karyawan</th>
                <th style="width: 15%;">Seksi</th>
                <th style="width: 10%;">Waktu</th>
                <th style="width: 8%;">Total Jam</th>
                <th style="width: 22%;">Deskripsi</th>
            </tr>
        </thead>
        <tbody>
            @php
                $no = 1;
                $currentDate = null;
                $currentBatchId = null;
            @endphp
            
            @foreach($overtimes as $overtime)
                @php
                    $dateChanged = $currentDate !== $overtime->date->format('Y-m-d');
                    $batchChanged = $currentBatchId !== $overtime->batch_id;
                    $currentDate = $overtime->date->format('Y-m-d');
                    $currentBatchId = $overtime->batch_id;
                @endphp
                
                <tr>
                    <td class="center">{{ $no++ }}</td>
                    <td class="center">
                        @if($dateChanged)
                            {{ $overtime->date->format('d M Y') }}
                        @endif
                    </td>
                    {{-- <td>
                        @if($batchChanged)
                            {{ $overtime->batch_id }}
                        @endif
                    </td> --}}
                    <td>{{ $overtime->employee_name }}</td>
                    <td>{{ $overtime->section->name }}</td>
                    <td class="center">{{ substr($overtime->start_time, 0, 5) }} - {{ substr($overtime->end_time, 0, 5) }}</td>
                    <td class="center">{{ number_format($overtime->total_hours, 1) }}</td>
                    <td>{{ $overtime->work_description }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
    
    <!-- Summary -->
    <div class="summary-section">
        <div class="summary-box">
            <div class="summary-label">Total Karyawan</div>
            <div class="summary-value">{{ $total_employees }}</div>
        </div>
        <div class="summary-box">
            <div class="summary-label">Total Jam Overtime</div>
            <div class="summary-value">{{ number_format($total_hours, 1) }} jam</div>
        </div>
    </div>
    
    <!-- Signatures -->
    <div class="signatures-section">
        <!-- Supervisor Signature -->
        <div class="signature-box">
            <div class="signature-title">Menugaskan</div>
            <div class="signature-image">
                @if($supervisor_signature)
                    <img src="{{ asset('storage/' . $supervisor_signature) }}" alt="Signature Supervisor">
                @else
                    <div style="color: #999; font-style: italic;">(Signature tidak tersedia)</div>
                @endif
            </div>
            <div class="signature-name">
                {{ $supervisor ? $supervisor->name : '-' }}
            </div>
            {{-- @if($supervisor)
                <div class="signature-date">
                    {{ $overtimes->first()->supervisor_approved_at ? $overtimes->first()->supervisor_approved_at->format('d M Y') : '-' }}
                </div>
            @endif --}}
        </div>
        
        <!-- Manager Signature -->
        <div class="signature-box">
            <div class="signature-title">Menyetujui</div>
            <div class="signature-image">
                @if($manager_signature)
                    <img src="{{ asset('storage/' . $manager_signature) }}" alt="Signature Manager">
                @else
                    <div style="color: #999; font-style: italic;">(Signature tidak tersedia)</div>
                @endif
            </div>
            <div class="signature-name">
                {{ $manager ? $manager->name : '-' }}
            </div>
            {{-- @if($manager)
                <div class="signature-date">
                    {{ $overtimes->first()->manager_approved_at ? $overtimes->first()->manager_approved_at->format('d M Y') : '-' }}
                </div>
            @endif --}}
        </div>

        <!-- Plant Head Signature -->
        <div class="signature-box">
            <div class="signature-title">Menyetujui</div>
            <div class="signature-image">
                {{-- @if($plant_head_signature)
                    <img src="{{ asset('storage/' . $plant_head_signature) }}" alt="Signature Plant Head">
                @else
                    <div style="color: #999; font-style: italic;">(Signature tidak tersedia)</div>
                @endif --}}
            </div>
            <div class="signature-name">
                <p>Octa Yuda P.</p>
            </div>
            {{-- @if($plant_head)
                <div class="signature-date">
                    {{ $overtimes->first()->plant_head_approved_at ? $overtimes->first()->plant_head_approved_at->format('d M Y') : '-' }}
                </div>
            @endif --}}
        </div>

        <!-- HRD Signature -->
        <div class="signature-box">
            <div class="signature-title">Diterima & Diperiksa</div>
            <div class="signature-image">
                {{-- @if($plant_head_signature)
                    <img src="{{ asset('storage/' . $plant_head_signature) }}" alt="Signature Plant Head">
                @else
                    <div style="color: #999; font-style: italic;">(Signature tidak tersedia)</div>
                @endif --}}
            </div>
            <div class="signature-name">
                <p>HRD</p>
            </div>
            {{-- @if($plant_head)
                <div class="signature-date">
                    {{ $overtimes->first()->plant_head_approved_at ? $overtimes->first()->plant_head_approved_at->format('d M Y') : '-' }}
                </div>
            @endif --}}
        </div>
    </div>
    
    <!-- Notes -->
    <div class="notes-section">
        <p><strong>Catatan:</strong></p>
        <ul style="margin-left: 20px; margin-top: 5px;">
            <li>Laporan ini di-generate secara otomatis dari sistem administrasi.</li>
            {{-- <li>Semua data overtime yang tercantum telah melalui 2 tahap approval (Supervisor, Manager, dan Plant Head).</li> --}}
            <li>E-Signature yang tertera adalah digital signature yang telah diupload oleh masing-masing approver.</li>
        </ul>
    </div>

    <script>
        // Set "Tanggal Cetak" using browser's actual local time & timezone
        (function () {
            try {
                var now  = new Date();
                var tz   = Intl.DateTimeFormat().resolvedOptions().timeZone;

                // Format: "26 Mar 2026, 15:30"
                var dateStr = now.toLocaleDateString('id-ID', {
                    day   : '2-digit',
                    month : 'short',
                    year  : 'numeric',
                    timeZone: tz
                });
                var timeStr = now.toLocaleTimeString('id-ID', {
                    hour   : '2-digit',
                    minute : '2-digit',
                    hour12 : false,
                    timeZone: tz
                });

                // Derive short timezone label (WIB / WITA / WIT / etc.)
                var tzLabel = tz;
                var tzMap = {
                    'Asia/Jakarta'   : 'WIB',
                    'Asia/Pontianak' : 'WIB',
                    'Asia/Makassar'  : 'WITA',
                    'Asia/Bali'      : 'WITA',
                    'Asia/Jayapura'  : 'WIT',
                };
                if (tzMap[tz]) {
                    tzLabel = tzMap[tz];
                } else {
                    // Generic offset fallback e.g. "UTC+9"
                    var offset = -now.getTimezoneOffset();
                    tzLabel = 'UTC' + (offset >= 0 ? '+' : '') + (offset / 60);
                }

                document.getElementById('print-datetime').textContent =
                    dateStr + ', ' + timeStr + ' ' + tzLabel;
            } catch (e) {
                document.getElementById('print-datetime').textContent =
                    new Date().toLocaleString('id-ID');
            }
        })();
    </script>
</body>
</html>
