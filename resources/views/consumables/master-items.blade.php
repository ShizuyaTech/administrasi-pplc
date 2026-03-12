@extends('layouts.app')

@section('title', 'Master List Item Consumable')
@section('page-title', 'Master List Item Consumable')

@section('content')
<div class="mb-6 flex items-center justify-between">
    <div>
        <a href="{{ route('consumables.index') }}" class="inline-flex items-center text-indigo-600 hover:text-indigo-900">
            <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            Kembali ke Stok Consumable
        </a>
        <p class="mt-2 text-sm text-gray-600">Daftar unique item consumable yang tersedia (tanpa duplikasi per seksi)</p>
    </div>
    
    @if(auth()->user()->hasPermission('create-consumable'))
    <a href="{{ route('consumables.create') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-medium rounded-lg">
        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
        </svg>
        Tambah Item Baru
    </a>
    @endif
</div>

<!-- Info Box -->
<div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
    <div class="flex">
        <svg class="w-5 h-5 text-blue-600 mr-3 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
        </svg>
        <div>
            <h3 class="text-sm font-medium text-blue-900 mb-1">Informasi Master List</h3>
            <p class="text-sm text-blue-800">Halaman ini menampilkan <strong>daftar unique item consumable</strong> dengan total stok dari semua seksi. Gunakan halaman ini untuk mengecek apakah item yang akan ditambahkan sudah ada atau belum, untuk menghindari duplikasi nama item.</p>
        </div>
    </div>
</div>

<!-- Search -->
<div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 mb-6">
    <form method="GET" action="{{ route('consumables.master-items') }}" class="flex gap-4">
        <div class="flex-1">
            <label for="search" class="block text-sm font-medium text-gray-700 mb-1">Cari Nama Item</label>
            <input type="text" id="search" name="search" value="{{ request('search') }}" placeholder="Cari nama item..." class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
        </div>
        
        <div class="flex items-end">
            <button type="submit" class="px-6 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-medium rounded-lg">
                Cari
            </button>
        </div>
        
        @if(request('search'))
        <div class="flex items-end">
            <a href="{{ route('consumables.master-items') }}" class="px-6 py-2 bg-gray-200 hover:bg-gray-300 text-gray-700 font-medium rounded-lg">
                Reset
            </a>
        </div>
        @endif
    </form>
</div>

<!-- Summary -->
<div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <div class="flex items-center">
            <div class="p-3 bg-indigo-100 rounded-lg">
                <svg class="w-8 h-8 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                </svg>
            </div>
            <div class="ml-4">
                <p class="text-sm text-gray-500">Total Unique Items</p>
                <p class="text-2xl font-bold text-gray-900">{{ $masterItems->total() }}</p>
            </div>
        </div>
    </div>
    
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <div class="flex items-center">
            <div class="p-3 bg-green-100 rounded-lg">
                <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
            </div>
            <div class="ml-4">
                <p class="text-sm text-gray-500">Showing</p>
                <p class="text-2xl font-bold text-gray-900">{{ $masterItems->count() }}</p>
            </div>
        </div>
    </div>
</div>

<!-- Table -->
<div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Item</th>
                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Satuan</th>
                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Total Stok<br><span class="text-xs font-normal normal-case">(Semua Seksi)</span></th>
                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Total Min. Stok</th>
                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Tersedia di<br><span class="text-xs font-normal normal-case">(Jumlah Seksi)</span></th>
                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($masterItems as $item)
                    <tr class="{{ $item->total_stock <= $item->total_minimum_stock ? 'bg-red-50' : '' }} hover:bg-gray-50">
                        <td class="px-6 py-4">
                            <div class="text-sm font-medium text-gray-900">{{ $item->name }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-center text-sm text-gray-600">
                            {{ $item->unit }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-center text-sm">
                            <span class="font-bold {{ $item->total_stock <= $item->total_minimum_stock ? 'text-red-700' : 'text-gray-900' }}">
                                {{ number_format($item->total_stock, 0, ',', '.') }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-center text-sm text-gray-500">
                            {{ number_format($item->total_minimum_stock, 0, ',', '.') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-center">
                            <span class="px-3 py-1 text-xs rounded-full bg-blue-100 text-blue-800 font-medium">
                                {{ $item->section_count }} Seksi
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-center">
                            @if($item->total_stock <= $item->total_minimum_stock)
                                <span class="px-2 py-1 text-xs rounded bg-red-100 text-red-800 font-medium">⚠️ Low Stock</span>
                            @elseif($item->total_stock <= $item->total_minimum_stock * 1.5)
                                <span class="px-2 py-1 text-xs rounded bg-yellow-100 text-yellow-800 font-medium">⚡ Warning</span>
                            @else
                                <span class="px-2 py-1 text-xs rounded bg-green-100 text-green-800 font-medium">✓ OK</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-8 text-center text-gray-500">
                            @if(request('search'))
                                <p class="text-lg font-medium mb-1">Tidak ada item yang cocok dengan pencarian "{{ request('search') }}"</p>
                                <a href="{{ route('consumables.master-items') }}" class="text-indigo-600 hover:text-indigo-900">Reset pencarian</a>
                            @else
                                Belum ada data consumable.
                            @endif
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    @if($masterItems->hasPages())
    <div class="px-6 py-4 border-t border-gray-200">
        {{ $masterItems->links('pagination.custom') }}
    </div>
    @endif
</div>

<!-- Legend -->
<div class="mt-6 bg-white rounded-lg shadow-sm border border-gray-200 p-4">
    <h3 class="text-sm font-semibold text-gray-700 mb-3">Keterangan:</h3>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm text-gray-600">
        <div class="flex items-start">
            <div class="flex-shrink-0 w-4 h-4 rounded bg-red-100 border border-red-300 mt-0.5 mr-2"></div>
            <div>
                <span class="font-medium">Background Merah:</span> Total stok ≤ minimum stok (low stock)
            </div>
        </div>
        <div class="flex items-start">
            <div class="flex-shrink-0 w-4 h-4 rounded-full bg-blue-100 border border-blue-300 mt-0.5 mr-2"></div>
            <div>
                <span class="font-medium">Badge Biru:</span> Jumlah seksi yang memiliki item tersebut
            </div>
        </div>
        <div class="flex items-start">
            <div class="flex-shrink-0 w-4 h-4 rounded bg-green-100 border border-green-300 mt-0.5 mr-2"></div>
            <div>
                <span class="font-medium">Status OK:</span> Total stok masih aman
            </div>
        </div>
    </div>
</div>
@endsection
