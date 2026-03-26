@extends('layouts.app')

@section('title', 'Tambah Section')
@section('page-title', 'Tambah Section')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="bg-white shadow-sm rounded-lg border border-gray-200">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-800">Form Tambah Section</h3>
        </div>

        <form action="{{ route('sections.store') }}" method="POST" class="p-6 space-y-6">
            @csrf

            <!-- Nama Section -->
            <div>
                <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                    Nama Section <span class="text-red-500">*</span>
                </label>
                <input type="text" 
                       name="name" 
                       id="name" 
                       value="{{ old('name') }}"
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('name') border-red-500 @enderror"
                       placeholder="Contoh: Material Control"
                       required>
                @error('name')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Code -->
            <div>
                <label for="code" class="block text-sm font-medium text-gray-700 mb-2">
                    Code (Opsional)
                </label>
                <input type="text" 
                       name="code" 
                       id="code" 
                       value="{{ old('code') }}"
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('code') border-red-500 @enderror"
                       placeholder="Contoh: MC">
                @error('code')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Description -->
            <div>
                <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
                    Deskripsi (Opsional)
                </label>
                <textarea name="description" 
                          id="description" 
                          rows="3"
                          class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('description') border-red-500 @enderror"
                          placeholder="Deskripsi section...">{{ old('description') }}</textarea>
                @error('description')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Action Buttons -->
            <div class="flex items-center justify-end space-x-3 pt-4 border-t border-gray-200">
                <a href="{{ route('sections.index') }}" 
                   class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition">
                    Batal
                </a>
                <button type="submit" 
                        class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition">
                    Simpan Section
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
