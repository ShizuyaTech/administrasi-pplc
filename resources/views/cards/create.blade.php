@extends('layouts.app')

@section('title', 'Tambah Kartu E-Money')
@section('page-title', 'Tambah Kartu E-Money')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <form method="POST" action="{{ route('cards.store') }}">
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
            <div class="mb-6 bg-indigo-50 border border-indigo-200 rounded-lg p-4">
                <p class="text-sm text-indigo-800"><strong>Seksi:</strong> {{ auth()->user()->section->name }}</p>
            </div>
            @endif

            <div class="mb-6">
                <label for="card_number" class="block text-sm font-medium text-gray-700 mb-2">Nomor Kartu <span class="text-red-500">*</span></label>
                <input type="text" id="card_number" name="card_number" value="{{ old('card_number') }}" required placeholder="Contoh: 1234-5678-9012" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 @error('card_number') border-red-500 @enderror">
                @error('card_number')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                <p class="mt-1 text-xs text-gray-500">Masukkan nomor kartu yang tertera</p>
            </div>

            <div class="mb-6">
                <label for="card_type" class="block text-sm font-medium text-gray-700 mb-2">Tipe Kartu <span class="text-red-500">*</span></label>
                <select id="card_type" name="card_type" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 @error('card_type') border-red-500 @enderror">
                    <option value="">Pilih Tipe Kartu</option>
                    <option value="flazz" {{ old('card_type') == 'flazz' ? 'selected' : '' }}>Flazz (BCA)</option>
                    <option value="brizzi" {{ old('card_type') == 'brizzi' ? 'selected' : '' }}>Brizzi (BRI)</option>
                    <option value="e-toll" {{ old('card_type') == 'e-toll' ? 'selected' : '' }}>E-Toll (Mandiri)</option>
                    <option value="other" {{ old('card_type') == 'other' ? 'selected' : '' }}>Lainnya</option>
                </select>
                @error('card_type')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
            </div>

            <div class="mb-6">
                <label for="current_balance" class="block text-sm font-medium text-gray-700 mb-2">Saldo Awal (Rp) <span class="text-red-500">*</span></label>
                <input type="number" id="current_balance" name="current_balance" value="{{ old('current_balance', 0) }}" required min="0" step="1" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 @error('current_balance') border-red-500 @enderror">
                @error('current_balance')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                <p class="mt-1 text-xs text-gray-500">Masukkan saldo saat kartu pertama kali ditambahkan</p>
            </div>

            <div class="mb-6">
                <label for="status" class="block text-sm font-medium text-gray-700 mb-2">Status <span class="text-red-500">*</span></label>
                <select id="status" name="status" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 @error('status') border-red-500 @enderror">
                    <option value="active" {{ old('status', 'active') == 'active' ? 'selected' : '' }}>Aktif</option>
                    <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>Tidak Aktif</option>
                </select>
                @error('status')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
            </div>

            <div class="mb-6">
                <label for="notes" class="block text-sm font-medium text-gray-700 mb-2">Catatan</label>
                <textarea id="notes" name="notes" rows="3" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 @error('notes') border-red-500 @enderror" placeholder="Catatan tambahan...">{{ old('notes') }}</textarea>
                @error('notes')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
            </div>

            <div class="flex justify-end space-x-3">
                <a href="{{ route('cards.index') }}" class="px-6 py-3 border border-gray-300 text-gray-700 font-medium rounded-lg hover:bg-gray-50">Batal</a>
                <button type="submit" class="px-6 py-3 bg-indigo-600 hover:bg-indigo-700 text-white font-medium rounded-lg">Simpan Kartu</button>
            </div>
        </form>
    </div>
</div>
@endsection
