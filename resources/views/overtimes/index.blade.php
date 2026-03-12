@extends('layouts.app')

@section('title', 'Data Overtime')
@section('page-title', 'Data Overtime')

@section('content')
<div class="space-y-6" x-data="{ showRejectModal: false, rejectBatchId: null }">
    <!-- Filter Card -->
    <div class="bg-white rounded-lg shadow-sm p-6 border border-gray-200">
        <form method="GET" class="grid grid-cols-1 md:grid-cols-5 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Dari</label>
                <input type="date" name="date_from" value="{{ request('date_from') }}"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Sampai</label>
                <input type="date" name="date_to" value="{{ request('date_to') }}"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                <select name="status" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
                    <option value="">Semua</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Approved</option>
                    <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Tipe</label>
                <select name="type" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
                    <option value="">Semua</option>
                    <option value="regular" {{ request('type') == 'regular' ? 'selected' : '' }}>Harian</option>
                    <option value="additional" {{ request('type') == 'additional' ? 'selected' : '' }}>Susulan</option>
                </select>
            </div>
            <div class="flex items-end">
                <button type="submit" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-medium py-2 px-4 rounded-lg">
                    Filter
                </button>
            </div>
        </form>
    </div>

    <!-- Actions -->
    <div class="flex justify-between items-center">
        <h2 class="text-lg font-semibold text-gray-800">Daftar Overtime</h2>
        <div class="flex space-x-3">
            @if(auth()->user()->hasPermission('export-overtimes'))
            <a href="{{ route('overtimes.export', request()->query()) }}" class="bg-green-600 hover:bg-green-700 text-white font-medium py-2 px-4 rounded-lg inline-flex items-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                Export CSV
            </a>
            @endif
            @if(auth()->user()->hasPermission('create-overtime'))
            <a href="{{ route('overtimes.create') }}" class="bg-indigo-600 hover:bg-indigo-700 text-white font-medium py-2 px-4 rounded-lg inline-flex items-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                Tambah Overtime
            </a>
            @endif
        </div>
    </div>

    <!-- Table -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 border-b">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tanggal</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Seksi</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Waktu</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Jumlah Karyawan</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Total Jam</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Tipe</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Status</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    @forelse($overtimeBatches as $batch)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                            {{ \Carbon\Carbon::parse($batch->date)->format('d M Y') }}
                        </td>
                        <td class="px-6 py-4 text-sm">
                            @php
                                $section = \App\Models\Section::find($batch->section_id);
                            @endphp
                            {{ $section ? $section->name : '-' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                            {{ substr($batch->start_time, 0, 5) }} - {{ substr($batch->end_time, 0, 5) }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-center text-sm">
                            <span class="px-3 py-1 bg-indigo-100 text-indigo-800 rounded-full font-semibold">
                                {{ $batch->employee_count }} orang
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-semibold">
                            {{ number_format($batch->total_hours, 1) }} jam
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-center">
                            <span class="px-2 py-1 text-xs rounded {{ $batch->type == 'regular' ? 'bg-blue-100 text-blue-800' : 'bg-purple-100 text-purple-800' }}">
                                {{ $batch->type == 'regular' ? 'Harian' : 'Susulan' }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-center">
                            @if($batch->status == 'pending')
                                <span class="px-2 py-1 text-xs rounded bg-yellow-100 text-yellow-800">Pending</span>
                            @elseif($batch->status == 'approved')
                                <span class="px-2 py-1 text-xs rounded bg-green-100 text-green-800">Approved</span>
                            @else
                                <span class="px-2 py-1 text-xs rounded bg-red-100 text-red-800">Rejected</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-center text-sm">
                            <div class="flex flex-col gap-2 items-center">
                                <!-- Detail Button -->
                                <a href="{{ route('overtimes.batch.detail', ['batchId' => $batch->batch_id]) }}" 
                                   class="text-indigo-600 hover:text-indigo-900 font-medium">
                                    Detail
                                </a>
                                
                                @if($batch->status == 'pending')
                                    <!-- Edit Button for Leader/Foreman/Admin -->
                                    @if(auth()->user()->canApproveOvertimes() || auth()->user()->isLeaderOrForeman() || auth()->user()->canManageAllSections())
                                        <a href="{{ route('overtimes.batch.edit', ['batchId' => $batch->batch_id]) }}" 
                                           class="text-yellow-600 hover:text-yellow-900 font-medium">
                                            Edit
                                        </a>
                                    @endif
                                    
                                    @if(auth()->user()->canApproveOvertimes())
                                        <div class="flex gap-2">
                                            <form action="{{ route('overtimes.batch.approve', ['batchId' => $batch->batch_id]) }}" method="POST" class="inline">
                                                @csrf
                                                <button type="submit" 
                                                        class="text-green-600 hover:text-green-900 font-medium"
                                                        onclick="return confirm('Approve batch overtime untuk {{ $batch->employee_count }} karyawan?')">
                                                    Approve
                                                </button>
                                            </form>
                                            <button @click="showRejectModal = true; rejectBatchId = '{{ $batch->batch_id }}'" 
                                                    class="text-red-600 hover:text-red-900 font-medium">
                                                Reject
                                            </button>
                                        </div>
                                    @endif
                                    @if(auth()->user()->hasPermission('delete-overtime'))
                                        <form action="{{ route('overtimes.batch.delete', ['batchId' => $batch->batch_id]) }}" method="POST" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" 
                                                    class="text-red-600 hover:text-red-900 font-medium"
                                                    onclick="return confirm('Hapus batch overtime untuk {{ $batch->employee_count }} karyawan?')">
                                                Hapus
                                            </button>
                                        </form>
                                    @endif
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="px-6 py-8 text-center text-gray-500">Belum ada data overtime</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($overtimeBatches->hasPages())
        <div class="px-6 py-4 border-t">{{ $overtimeBatches->links('pagination.custom') }}</div>
        @endif
    </div>

    <!-- Reject Modal -->
    <div x-show="showRejectModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center" style="display: none;">
        <div @click.away="showRejectModal = false" class="bg-white rounded-lg p-6 max-w-md w-full mx-4">
            <h3 class="text-lg font-semibold mb-4">Reject Batch Overtime</h3>
            <form :action="'/overtimes/batch/' + rejectBatchId + '/reject'" method="POST">
                @csrf
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Alasan Reject</label>
                    <textarea name="rejection_reason" rows="4" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500"></textarea>
                </div>
                <div class="flex justify-end space-x-3">
                    <button type="button" @click="showRejectModal = false" class="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50">Batal</button>
                    <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700">Reject</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
