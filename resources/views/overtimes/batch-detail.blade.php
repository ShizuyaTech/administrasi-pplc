@extends('layouts.app')

@section('title', 'Detail Batch Overtime')
@section('page-title', 'Detail Batch Overtime')

@section('content')
<div class="space-y-6">
    <!-- Back Button -->
    <div>
        <a href="{{ route('overtimes.index') }}" class="inline-flex items-center text-indigo-600 hover:text-indigo-900">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
            </svg>
            Kembali ke Daftar Overtime
        </a>
    </div>

    @php
        $firstOvertime = $overtimes->first();
    @endphp

    <!-- Batch Info Card -->
    <div class="bg-white rounded-lg shadow-sm p-6 border border-gray-200">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Informasi Batch</h3>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label class="text-sm text-gray-500">Tanggal</label>
                <p class="font-semibold">{{ $firstOvertime->date->format('d F Y') }}</p>
            </div>
            <div>
                <label class="text-sm text-gray-500">Seksi</label>
                <p class="font-semibold">{{ $firstOvertime->section->name }}</p>
            </div>
            <div>
                <label class="text-sm text-gray-500">Waktu</label>
                <p class="font-semibold">{{ substr($firstOvertime->start_time, 0, 5) }} - {{ substr($firstOvertime->end_time, 0, 5) }}</p>
            </div>
            <div>
                <label class="text-sm text-gray-500">Tipe</label>
                <p>
                    <span class="px-2 py-1 text-xs rounded {{ $firstOvertime->type == 'regular' ? 'bg-blue-100 text-blue-800' : 'bg-purple-100 text-purple-800' }}">
                        {{ $firstOvertime->type == 'regular' ? 'Harian' : 'Susulan' }}
                    </span>
                </p>
            </div>
            <div>
                <label class="text-sm text-gray-500">Status</label>
                <p>
                    @if($firstOvertime->status == 'pending')
                        <span class="px-2 py-1 text-xs rounded bg-yellow-100 text-yellow-800">Pending</span>
                    @elseif($firstOvertime->status == 'approved')
                        <span class="px-2 py-1 text-xs rounded bg-green-100 text-green-800">Approved</span>
                    @else
                        <span class="px-2 py-1 text-xs rounded bg-red-100 text-red-800">Rejected</span>
                    @endif
                </p>
            </div>
            <div>
                <label class="text-sm text-gray-500">Jumlah Karyawan</label>
                <p class="font-semibold">{{ $overtimes->count() }} orang</p>
            </div>
            <div>
                <label class="text-sm text-gray-500">Dibuat Oleh</label>
                <p class="font-semibold">{{ $firstOvertime->creator->name }}</p>
            </div>
            <div>
                <label class="text-sm text-gray-500">Dibuat Pada</label>
                <p class="font-semibold">{{ toUserTime($firstOvertime->created_at, 'd M Y H:i') }}</p>
            </div>
            @if($firstOvertime->approver)
            <div>
                <label class="text-sm text-gray-500">Disetujui Oleh</label>
                <p class="font-semibold">{{ $firstOvertime->approver->name }}</p>
            </div>
            @endif
        </div>

        @if($firstOvertime->status == 'rejected' && $firstOvertime->rejection_reason)
        <div class="mt-4 p-4 bg-red-50 border border-red-200 rounded-lg">
            <label class="text-sm font-medium text-red-800">Alasan Reject:</label>
            <p class="text-red-700 mt-1">{{ $firstOvertime->rejection_reason }}</p>
        </div>
        @endif
    </div>

    <!-- Employee List -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
        <div class="px-6 py-4 border-b bg-gray-50">
            <h3 class="text-lg font-semibold text-gray-900">Daftar Karyawan ({{ $overtimes->count() }} orang)</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 border-b">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">No</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nama Karyawan</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Deskripsi Pekerjaan</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Jam</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    @foreach($overtimes as $index => $overtime)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $index + 1 }}</td>
                        <td class="px-6 py-4 text-sm font-medium text-gray-900">{{ $overtime->employee_name }}</td>
                        <td class="px-6 py-4 text-sm text-gray-700">{{ $overtime->work_description }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-center text-sm">
                            @php
                                $netHours = $overtime->getNetWorkingHours();
                                $breakDeductions = $overtime->getBreakTimeDeductionsInHours();
                            @endphp
                            <div class="font-semibold">{{ number_format($netHours, 1) }}</div>
                            @if($breakDeductions > 0)
                                <div class="text-xs text-gray-500 mt-0.5">
                                    (Gross: {{ number_format($overtime->getGrossWorkingHours(), 1) }} - Istirahat: {{ number_format($breakDeductions, 1) }})
                                </div>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot class="bg-gray-50 border-t">
                    <tr>
                        <td colspan="3" class="px-6 py-4 text-right text-sm font-bold text-gray-900">Total:</td>
                        <td class="px-6 py-4 text-center text-sm font-bold text-gray-900">
                            @php
                                $totalNetHours = $overtimes->sum(function($o) { return $o->getNetWorkingHours(); });
                            @endphp
                            {{ number_format($totalNetHours, 1) }} jam
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>
@endsection
