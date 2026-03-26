@extends('layouts.app')

@section('title', 'Data Overtime')
@section('page-title', 'Data Overtime')

@section('content')
<div class="space-y-6">
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
                    @if(auth()->user()->isManager() && !auth()->user()->canManageAllSections())
                        <!-- Manager default: supervisor_approved -->
                        <option value="supervisor_approved" {{ request('status', 'supervisor_approved') == 'supervisor_approved' ? 'selected' : '' }}>Disetujui Supervisor</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="fully_approved" {{ request('status') == 'fully_approved' ? 'selected' : '' }}>Fully Approved</option>
                        <option value="rejected_by_supervisor" {{ request('status') == 'rejected_by_supervisor' ? 'selected' : '' }}>Rejected by Supervisor</option>
                        <option value="rejected_by_manager" {{ request('status') == 'rejected_by_manager' ? 'selected' : '' }}>Rejected by Manager</option>
                    @else
                        <!-- Others: all statuses -->
                        <option value="">Semua</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="supervisor_approved" {{ request('status') == 'supervisor_approved' ? 'selected' : '' }}>Disetujui Supervisor</option>
                        <option value="fully_approved" {{ request('status') == 'fully_approved' ? 'selected' : '' }}>Fully Approved</option>
                        <option value="rejected_by_supervisor" {{ request('status') == 'rejected_by_supervisor' ? 'selected' : '' }}>Rejected by Supervisor</option>
                        <option value="rejected_by_manager" {{ request('status') == 'rejected_by_manager' ? 'selected' : '' }}>Rejected by Manager</option>
                    @endif
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
        
        @if(!auth()->user()->isManager() || auth()->user()->canManageAllSections())
            <!-- Info for status filter -->
            <div class="mt-3 text-sm text-gray-600">
                <svg class="w-4 h-4 inline mr-1 text-gray-500" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                </svg>
                <span>Pilih <strong>"Semua"</strong> pada filter Status untuk menampilkan semua status overtime. Data akan dikelompokkan berdasarkan tanggal dan status.</span>
            </div>
        @endif
    </div>

    @if(auth()->user()->isManager() && !auth()->user()->canManageAllSections())
        <!-- Info Box for Manager -->
        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
            <div class="flex items-start">
                <svg class="w-5 h-5 text-blue-600 mt-0.5 mr-3" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                </svg>
                <div>
                    <h4 class="text-sm font-semibold text-blue-800">Mode Manager</h4>
                    <p class="text-sm text-blue-700 mt-1">
                        Default menampilkan overtime yang <strong>sudah disetujui Supervisor</strong> dari seksi yang Anda bawahi. 
                        Ubah filter status untuk melihat data lainnya.
                    </p>
                    <a href="{{ route('overtimes.approval.manager') }}" class="inline-block mt-2 text-sm font-medium text-blue-600 hover:text-blue-800">
                        → Ke Halaman Approval Manager
                    </a>
                </div>
            </div>
        </div>
    @endif

    <!-- Actions -->
    <div class="flex justify-between items-center">
        <h2 class="text-lg font-semibold text-gray-800">Daftar Overtime</h2>
        <div class="flex space-x-3">
            @if(auth()->user()->hasPermission('print-overtime-report'))
            <a href="{{ route('overtimes.pdf') }}" class="bg-purple-600 hover:bg-purple-700 text-white font-medium py-2 px-4 rounded-lg inline-flex items-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                </svg>
                Print PDF
            </a>
            @endif
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
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Dibuat Oleh</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Jumlah Karyawan</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Total Jam</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Tipe</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Status</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    @forelse($overtimeGroups as $group)
                    @php
                        $batchIdsJson = json_encode($group['batch_ids']);
                    @endphp
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            {{ $group['date']->format('d M Y') }}
                        </td>
                        <td class="px-6 py-4 text-sm">
                            <div class="flex flex-wrap gap-1">
                                @foreach($group['sections'] as $sectionName)
                                    <span class="px-2 py-1 bg-gray-100 text-gray-800 rounded text-xs font-medium">
                                        {{ $sectionName }}
                                    </span>
                                @endforeach
                            </div>
                        </td>
                        <td class="px-6 py-4 text-sm">
                            <div class="space-y-1">
                                @foreach($group['batches'] as $batch)
                                    <div class="flex items-center text-xs">
                                        <svg class="w-3 h-3 mr-1 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"></path>
                                        </svg>
                                        <span class="text-gray-700">{{ $batch['creator_name'] }}</span>
                                        <span class="text-gray-500 ml-1">({{ $batch['section_name'] }})</span>
                                    </div>
                                @endforeach
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-center text-sm">
                            <span class="px-3 py-1 bg-indigo-100 text-indigo-800 rounded-full font-semibold">
                                {{ $group['total_employees'] }} orang
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-semibold">
                            {{ number_format($group['total_hours'], 1) }} jam
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-center">
                            <span class="px-2 py-1 text-xs rounded {{ $group['type'] == 'regular' ? 'bg-blue-100 text-blue-800' : 'bg-purple-100 text-purple-800' }}">
                                {{ $group['type'] == 'regular' ? 'Harian' : 'Susulan' }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-center">
                            @if($group['status'] == 'pending')
                                <span class="px-2 py-1 text-xs rounded bg-yellow-100 text-yellow-800">Pending</span>
                            @elseif($group['status'] == 'supervisor_approved')
                                <span class="px-2 py-1 text-xs rounded bg-blue-100 text-blue-800">✓ Supervisor</span>
                            @elseif($group['status'] == 'fully_approved')
                                <span class="px-2 py-1 text-xs rounded bg-green-100 text-green-800">✓✓ Approved</span>
                            @elseif($group['status'] == 'rejected_by_supervisor')
                                <span class="px-2 py-1 text-xs rounded bg-red-100 text-red-800">✗ Supervisor</span>
                            @elseif($group['status'] == 'rejected_by_manager')
                                <span class="px-2 py-1 text-xs rounded bg-red-100 text-red-800">✗ Manager</span>
                            @else
                                <span class="px-2 py-1 text-xs rounded bg-gray-100 text-gray-800">{{ $group['status'] }}</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-center text-sm">
                            <!-- Detail Button - Link to correct approval page based on user role -->
                            @php
                                $approvalRoute = auth()->user()->isManager() && !auth()->user()->isSupervisor() && !auth()->user()->canManageAllSections()
                                    ? route('overtimes.approval.manager', ['date_from' => $group['date']->format('Y-m-d'), 'date_to' => $group['date']->format('Y-m-d')])
                                    : route('overtimes.approval.supervisor', ['date_from' => $group['date']->format('Y-m-d'), 'date_to' => $group['date']->format('Y-m-d')]);
                            @endphp
                            <a href="{{ $approvalRoute }}" 
                               class="text-indigo-600 hover:text-indigo-900 font-medium">
                                Detail & Actions ({{ count($group['batch_ids']) }} batch)
                            </a>
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
        @if($overtimeGroups->hasPages())
        <div class="px-6 py-4 border-t">{{ $overtimeGroups->links('pagination.custom') }}</div>
        @endif
    </div>
</div>
@endsection
