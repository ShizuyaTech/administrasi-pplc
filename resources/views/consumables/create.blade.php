@extends('layouts.app')

@section('title', 'Tambah Item Consumable')
@section('page-title', 'Tambah Item Consumable')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <form method="POST" action="{{ route('consumables.store') }}">
            @csrf

            @if(auth()->user()->isSuperAdmin())
            <div class="mb-6">
                <label for="section_id" class="block text-sm font-medium text-gray-700 mb-2">Seksi <span class="text-red-500">*</span></label>
                <select id="section_id" name="section_id" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 @error('section_id') border-red-500 @enderror">
                    <option value="">Pilih Seksi</option>
                    @foreach($sections as $section)
                        <option value="{{ $section->id }}" {{ old('section_id') == $section->id ? 'selected' : '' }}>{{ $section->name }}</option>
                    @endforeach
                </select>
                @error('section_id')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
            </div>
            @else
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">Seksi</label>
                <div class="px-4 py-3 bg-gray-50 border border-gray-300 rounded-lg text-gray-700">
                    {{ auth()->user()->section->name }}
                </div>
            </div>
            @endif

            <div class="mb-6">
                <label for="name" class="block text-sm font-medium text-gray-700 mb-2">Nama Item <span class="text-red-500">*</span></label>
                <input type="text" id="name" name="name" value="{{ old('name') }}" required placeholder="Misal: Kertas A4, Sarung Tangan, Lakban, dll" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 @error('name') border-red-500 @enderror">
                @error('name')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <div>
                    <label for="unit" class="block text-sm font-medium text-gray-700 mb-2">Satuan <span class="text-red-500">*</span></label>
                    <input type="text" id="unit" name="unit" value="{{ old('unit') }}" required placeholder="Misal: Rim, Pcs, Box, Roll" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 @error('unit') border-red-500 @enderror">
                    @error('unit')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>
                
                <div>
                    <label for="current_stock" class="block text-sm font-medium text-gray-700 mb-2">Stok Awal <span class="text-red-500">*</span></label>
                    <input type="number" id="current_stock" name="current_stock" value="{{ old('current_stock', 0) }}" required min="0" step="1" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 @error('current_stock') border-red-500 @enderror">
                    @error('current_stock')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>
            </div>

            <div class="mb-6">
                <label for="minimum_stock" class="block text-sm font-medium text-gray-700 mb-2">Minimum Stok (Alert) <span class="text-red-500">*</span></label>
                <input type="number" id="minimum_stock" name="minimum_stock" value="{{ old('minimum_stock', 10) }}" required min="0" step="1" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 @error('minimum_stock') border-red-500 @enderror">
                <p class="mt-1 text-sm text-gray-500">Alert akan muncul jika stok kurang dari atau sama dengan nilai ini</p>
                @error('minimum_stock')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
            </div>

            <div class="flex justify-end space-x-3">
                <a href="{{ route('consumables.index') }}" class="px-6 py-3 border border-gray-300 text-gray-700 font-medium rounded-lg hover:bg-gray-50">Batal</a>
                <button type="submit" class="px-6 py-3 bg-indigo-600 hover:bg-indigo-700 text-white font-medium rounded-lg">Simpan Item</button>
            </div>
        </form>
    </div>
</div>
@endsection
