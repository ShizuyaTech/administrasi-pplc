@extends('layouts.app')

@section('title', 'Detail Consumable')
@section('page-title', 'Detail Consumable')

@section('content')
<div class="mb-6">
    <a href="{{ route('consumables.index') }}" class="inline-flex items-center text-indigo-600 hover:text-indigo-900">
        <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
        </svg>
        Kembali ke Daftar
    </a>
</div>

<!-- Item Information Card -->
<div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
    <div class="flex items-start justify-between mb-4">
        <h2 class="text-2xl font-bold text-gray-900">{{ $consumable->name }}</h2>
        @if($consumable->current_stock <= $consumable->minimum_stock)
            <span class="px-3 py-1 text-sm rounded bg-red-100 text-red-800 font-medium">⚠️ Low Stock</span>
        @elseif($consumable->current_stock <= $consumable->minimum_stock * 1.5)
            <span class="px-3 py-1 text-sm rounded bg-yellow-100 text-yellow-800 font-medium">⚡ Warning</span>
        @else
            <span class="px-3 py-1 text-sm rounded bg-green-100 text-green-800 font-medium">✓ Stock OK</span>
        @endif
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <div>
            <p class="text-sm text-gray-500 mb-1">Seksi</p>
            <p class="text-lg font-semibold text-gray-900">{{ $consumable->section->name }}</p>
        </div>
        
        <div>
            <p class="text-sm text-gray-500 mb-1">Satuan</p>
            <p class="text-lg font-semibold text-gray-900">{{ $consumable->unit }}</p>
        </div>
        
        <div>
            <p class="text-sm text-gray-500 mb-1">Stok Saat Ini</p>
            <p class="text-3xl font-bold {{ $consumable->current_stock <= $consumable->minimum_stock ? 'text-red-600' : 'text-green-600' }}">
                {{ number_format($consumable->current_stock, 0, ',', '.') }}
            </p>
        </div>
        
        <div>
            <p class="text-sm text-gray-500 mb-1">Minimum Stok</p>
            <p class="text-xl font-semibold text-gray-700">{{ number_format($consumable->minimum_stock, 0, ',', '.') }}</p>
        </div>
    </div>

    @if($consumable->description)
    <div class="mt-4 pt-4 border-t border-gray-200">
        <p class="text-sm text-gray-500 mb-1">Deskripsi</p>
        <p class="text-gray-700">{{ $consumable->description }}</p>
    </div>
    @endif

    <div class="mt-6 flex gap-3">
        @if(auth()->user()->hasPermission('edit-consumable'))
        <a href="{{ route('consumables.edit', $consumable) }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-medium rounded-lg">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
            </svg>
            Edit Item
        </a>
        @endif
        
        @if(auth()->user()->hasPermission('create-stock-movement'))
        <a href="{{ route('stock-movements.create', ['consumable_id' => $consumable->id]) }}" class="inline-flex items-center px-4 py-2 bg-green-600 hover:bg-green-700 text-white font-medium rounded-lg">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4"></path>
            </svg>
            Stock In/Out
        </a>
        @endif
    </div>
</div>

<!-- Stock Movement History -->
<div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
    <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
        <h3 class="text-lg font-semibold text-gray-900">Riwayat Stock Movement (10 Terakhir)</h3>
    </div>

    @if($consumable->stockMovements && $consumable->stockMovements->count() > 0)
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tipe</th>
                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Jumlah</th>
                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Stok Sebelum</th>
                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Stok Sesudah</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Keterangan</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Dicatat Oleh</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @foreach($consumable->stockMovements as $movement)
                <tr class="{{ $movement->type === 'in' ? 'bg-green-50' : 'bg-red-50' }}">
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                        {{ toUserTime($movement->created_at, 'd/m/Y H:i') }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                        @if($movement->type === 'in')
                            <span class="px-2 py-1 text-xs rounded bg-green-100 text-green-800 font-medium">Stock In</span>
                        @else
                            <span class="px-2 py-1 text-xs rounded bg-red-100 text-red-800 font-medium">Stock Out</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-semibold {{ $movement->type === 'in' ? 'text-green-700' : 'text-red-700' }}">
                        {{ $movement->type === 'in' ? '+' : '-' }}{{ number_format($movement->quantity, 0, ',', '.') }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-center text-sm text-gray-600">
                        {{ number_format($movement->stock_before, 0, ',', '.') }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium text-gray-900">
                        {{ number_format($movement->stock_after, 0, ',', '.') }}
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-700">
                        {{ $movement->notes ?? '-' }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                        {{ $movement->creator->name ?? 'N/A' }}
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    
    @if($consumable->stockMovements->count() >= 10)
    <div class="px-6 py-4 bg-gray-50 border-t border-gray-200">
        <p class="text-sm text-gray-600">
            <a href="{{ route('stock-movements.index', ['consumable_id' => $consumable->id]) }}" class="text-indigo-600 hover:text-indigo-900 font-medium">
                Lihat Semua Riwayat →
            </a>
        </p>
    </div>
    @endif
    @else
    <div class="px-6 py-8 text-center text-gray-500">
        <svg class="w-16 h-16 mx-auto mb-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
        </svg>
        <p class="text-lg font-medium mb-1">Belum ada riwayat stock movement</p>
        <p class="text-sm">Stock movement akan muncul setelah ada stock in/out</p>
    </div>
    @endif
</div>
@endsection
