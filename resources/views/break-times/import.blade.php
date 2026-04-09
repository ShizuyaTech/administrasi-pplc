@extends('layouts.app')

@section('title', 'Import Jam Istirahat')
@section('page-title', 'Import Jam Istirahat dari Excel')

@section('content')
<div class="max-w-2xl mx-auto space-y-6">

    <div>
        <a href="{{ route('break-times.index') }}" class="inline-flex items-center text-sm text-gray-600 hover:text-gray-900">
            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
            Kembali ke Daftar Jam Istirahat
        </a>
    </div>

    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 flex items-start space-x-3">
        <svg class="w-5 h-5 text-blue-600 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        <div class="flex-1">
            <p class="text-sm font-medium text-blue-800">Panduan Import</p>
            <p class="text-sm text-blue-700 mt-1">
                Download template, isi data jam istirahat dengan format <strong>HH:MM</strong> (contoh: 12:00), lalu upload.
            </p>
            <a href="{{ route('break-times.import.template') }}"
               class="mt-2 inline-flex items-center text-sm font-medium text-blue-700 hover:text-blue-900">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                </svg>
                Download Template Excel
            </a>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <h3 class="text-base font-semibold text-gray-800 mb-4">Upload File Excel</h3>

        @if($errors->any())
        <div class="mb-4 p-4 bg-red-50 border border-red-200 rounded-lg">
            <p class="text-sm font-medium text-red-800 mb-1">Terjadi kesalahan:</p>
            <ul class="list-disc list-inside text-sm text-red-700 space-y-1">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <form method="POST" action="{{ route('break-times.import') }}" enctype="multipart/form-data">
            @csrf

            <div class="mb-5">
                <label for="file" class="block text-sm font-medium text-gray-700 mb-2">
                    File Excel (.xlsx / .xls) <span class="text-red-500">*</span>
                </label>
                <input type="file"
                       id="file"
                       name="file"
                       accept=".xlsx,.xls"
                       required
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 @error('file') border-red-500 @enderror">
                <p class="mt-1 text-xs text-gray-500">Maksimal 5 MB. Format: .xlsx atau .xls</p>
                @error('file')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
            </div>

            <div class="mb-5 overflow-x-auto">
                <p class="text-sm font-medium text-gray-700 mb-2">Format Kolom:</p>
                <table class="text-xs border border-gray-200 rounded w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-3 py-2 text-left border-r border-gray-200">Kolom</th>
                            <th class="px-3 py-2 text-left border-r border-gray-200">Isi</th>
                            <th class="px-3 py-2 text-left">Wajib</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        <tr><td class="px-3 py-1.5 border-r border-gray-200 font-mono">A</td><td class="px-3 py-1.5 border-r border-gray-200">Nama Jam Istirahat</td><td class="px-3 py-1.5 text-green-700">Ya</td></tr>
                        <tr><td class="px-3 py-1.5 border-r border-gray-200 font-mono">B</td><td class="px-3 py-1.5 border-r border-gray-200">Jam Mulai (format HH:MM)</td><td class="px-3 py-1.5 text-green-700">Ya</td></tr>
                        <tr><td class="px-3 py-1.5 border-r border-gray-200 font-mono">C</td><td class="px-3 py-1.5 border-r border-gray-200">Jam Selesai (format HH:MM)</td><td class="px-3 py-1.5 text-green-700">Ya</td></tr>
                        <tr><td class="px-3 py-1.5 border-r border-gray-200 font-mono">D</td><td class="px-3 py-1.5 border-r border-gray-200">Status Aktif (1 = Aktif, 0 = Nonaktif)</td><td class="px-3 py-1.5 text-gray-500">Opsional (default: 1)</td></tr>
                    </tbody>
                </table>
            </div>

            <div class="flex items-center justify-end space-x-3">
                <a href="{{ route('break-times.index') }}"
                   class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50">
                    Batal
                </a>
                <button type="submit"
                        class="px-5 py-2 text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 rounded-lg transition">
                    Upload & Import
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
