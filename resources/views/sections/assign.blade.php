@extends('layouts.app')

@section('title', 'Kelola Section Assignment')
@section('page-title', 'Kelola Section Assignment')

@section('content')
<div class="max-w-3xl mx-auto">
    <div class="bg-white shadow-sm rounded-lg border border-gray-200">
        <div class="px-6 py-4 border-b border-gray-200">
            <div class="flex items-center space-x-3">
                <div class="bg-indigo-100 rounded-full p-2">
                    <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                    </svg>
                </div>
                <div>
                    <h3 class="text-lg font-semibold text-gray-800">{{ $user->name }}</h3>
                    <p class="text-sm text-gray-600">{{ $user->email }} - {{ $user->role->name ?? 'No role' }}</p>
                </div>
            </div>
        </div>

        <form action="{{ route('sections.assign', $user->id) }}" method="POST" class="p-6 space-y-6">
            @csrf

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-3">
                    Pilih Section yang Ditugaskan <span class="text-red-500">*</span>
                </label>
                <p class="text-sm text-gray-600 mb-4">
                    Centang section yang dapat dikelola oleh {{ $user->name }}. 
                    Section yang dipilih akan menggantikan penugasan sebelumnya.
                </p>

                @error('sections')
                <div class="mb-4 bg-red-100 border-l-4 border-red-500 text-red-700 p-3 rounded">
                    <p class="text-sm">{{ $message }}</p>
                </div>
                @enderror

                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                    @foreach($sections as $section)
                    <label class="flex items-start p-4 border border-gray-200 rounded-lg hover:bg-gray-50 cursor-pointer transition">
                        <input type="checkbox" 
                               name="sections[]" 
                               value="{{ $section->id }}"
                               {{ $user->sections->contains($section->id) ? 'checked' : '' }}
                               class="mt-1 h-4 w-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500">
                        <div class="ml-3">
                            <p class="text-sm font-medium text-gray-900">{{ $section->name }}</p>
                            @if($section->code)
                            <p class="text-xs text-gray-500">Code: {{ $section->code }}</p>
                            @endif
                            @if($section->description)
                            <p class="text-xs text-gray-600 mt-1">{{ $section->description }}</p>
                            @endif
                        </div>
                    </label>
                    @endforeach
                </div>

                @if($sections->count() == 0)
                <div class="text-center py-8 text-gray-500">
                    <svg class="mx-auto h-12 w-12 text-gray-400 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path>
                    </svg>
                    <p>Belum ada section.</p>
                    <a href="{{ route('sections.create') }}" class="text-indigo-600 hover:text-indigo-800 mt-2 inline-block">
                        Tambahkan section terlebih dahulu
                    </a>
                </div>
                @endif
            </div>

            <!-- Current Assignments Info -->
            @if($user->sections->count() > 0)
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                <p class="text-sm font-medium text-blue-900 mb-2">✨ Current Assignments:</p>
                <div class="flex flex-wrap gap-2">
                    @foreach($user->sections as $section)
                    <span class="inline-flex items-center bg-blue-100 text-blue-800 px-2.5 py-0.5 rounded-full text-xs">
                        {{ $section->name }}
                    </span>
                    @endforeach
                </div>
            </div>
            @endif

            <!-- Action Buttons -->
            <div class="flex items-center justify-end space-x-3 pt-4 border-t border-gray-200">
                <a href="{{ route('sections.assignments') }}" 
                   class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition">
                    Batal
                </a>
                @if($sections->count() > 0)
                <button type="submit" 
                        class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition">
                    Simpan Penugasan
                </button>
                @endif
            </div>
        </form>
    </div>
</div>
@endsection
