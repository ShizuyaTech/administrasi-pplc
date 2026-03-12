@extends('layouts.app')

@section('title', 'Stock In / Out')
@section('page-title', 'Stock In / Out')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <form method="POST" action="{{ route('stock-movements.store') }}" x-data="{ consumableId: '{{ old('consumable_id') }}', consumables: {{ $consumables->toJson() }} }">
            @csrf

            <div class="mb-6">
                <label for="consumable_id" class="block text-sm font-medium text-gray-700 mb-2">Pilih Item <span class="text-red-500">*</span></label>
                <select id="consumable_id" name="consumable_id" required x-model="consumableId" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 @error('consumable_id') border-red-500 @enderror">
                    <option value="">-- Pilih Item --</option>
                    @foreach($consumables as $item)
                        <option value="{{ $item->id }}">{{ $item->name }} (Stok: {{ number_format($item->current_stock, 0, ',', '.') }} {{ $item->unit }})</option>
                    @endforeach
                </select>
                @error('consumable_id')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
            </div>

            <!-- Item Info Display -->
            <template x-if="consumableId">
                <div class="mb-6 p-4 bg-blue-50 rounded-lg border border-blue-200">
                    <template x-for="item in consumables" :key="item.id">
                        <div x-show="item.id == consumableId">
                            <div class="grid grid-cols-2 gap-4 text-sm">
                                <div>
                                    <span class="text-gray-600">Stok Saat Ini:</span>
                                    <span class="ml-2 font-bold text-lg" x-text="new Intl.NumberFormat('id-ID').format(item.current_stock) + ' ' + item.unit"></span>
                                </div>
                                <div>
                                    <span class="text-gray-600">Min. Stok:</span>
                                    <span class="ml-2 font-semibold" x-text="new Intl.NumberFormat('id-ID').format(item.minimum_stock)"></span>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>
            </template>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <div>
                    <label for="type" class="block text-sm font-medium text-gray-700 mb-2">Tipe Transaksi <span class="text-red-500">*</span></label>
                    <select id="type" name="type" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 @error('type') border-red-500 @enderror">
                        <option value="">-- Pilih Tipe --</option>
                        <option value="in" {{ old('type') == 'in' ? 'selected' : '' }}>Stock In (Masuk)</option>
                        <option value="out" {{ old('type') == 'out' ? 'selected' : '' }}>Stock Out (Keluar)</option>
                    </select>
                    @error('type')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>
                
                <div>
                    <label for="quantity" class="block text-sm font-medium text-gray-700 mb-2">Jumlah <span class="text-red-500">*</span></label>
                    <input type="number" id="quantity" name="quantity" value="{{ old('quantity') }}" required min="0.01" step="any" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 @error('quantity') border-red-500 @enderror">
                    @error('quantity')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>
            </div>

            <div class="mb-6">
                <label for="notes" class="block text-sm font-medium text-gray-700 mb-2">Catatan</label>
                <textarea id="notes" name="notes" rows="3" placeholder="Catatan opsional..." class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 @error('notes') border-red-500 @enderror">{{ old('notes') }}</textarea>
                @error('notes')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
            </div>

            <div class="flex justify-end space-x-3">
                <a href="{{ route('stock-movements.index') }}" class="px-6 py-3 border border-gray-300 text-gray-700 font-medium rounded-lg hover:bg-gray-50">Batal</a>
                <button type="submit" class="px-6 py-3 bg-green-600 hover:bg-green-700 text-white font-medium rounded-lg">Simpan Transaksi</button>
            </div>
        </form>
    </div>
</div>
@endsection
