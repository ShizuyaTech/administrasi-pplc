@extends('layouts.app')

@section('title', 'Print Laporan Overtime')
@section('page-title', 'Print Laporan Overtime')

@section('content')
<div class="max-w-2xl mx-auto">
    
    <!-- Info Banner -->
    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
        <h3 class="text-md font-semibold text-blue-800 mb-2">ℹ️ Informasi Print Laporan</h3>
        <p class="text-sm text-blue-700">
            Generate laporan untuk overtime yang sudah <strong>fully approved</strong> (disetujui oleh Supervisor dan Manager). 
            Laporan akan menampilkan e-signature dari Supervisor dan Manager yang approve.
        </p>
        <p class="text-sm text-blue-700 mt-2">
            📌 <strong>Cara print/save PDF:</strong> Setelah klik tombol, halaman preview akan muncul. Klik tombol "Print / Save as PDF" atau tekan Ctrl+P, lalu pilih "Save as PDF" di printer options.
        </p>
    </div>

    @if(session('error'))
        <div class="bg-red-50 border border-red-200 rounded-lg p-4 mb-6">
            <p class="text-red-800">❌ {{ session('error') }}</p>
        </div>
    @endif

    <!-- Generator Form -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <h2 class="text-lg font-semibold text-gray-800 mb-6">�️ Generate Laporan Print</h2>
        
        <form method="GET" action="{{ route('overtimes.pdf.generate') }}" target="_blank">
            <div class="space-y-5">
                
                <!-- Date From -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Tanggal Dari <span class="text-red-500">*</span>
                    </label>
                    <input type="date" 
                           name="date_from"
                           value="{{ old('date_from', now()->startOfMonth()->format('Y-m-d')) }}"
                           required
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
                </div>

                <!-- Date To -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Tanggal Sampai <span class="text-red-500">*</span>
                    </label>
                    <input type="date" 
                           name="date_to"
                           value="{{ old('date_to', now()->endOfMonth()->format('Y-m-d')) }}"
                           required
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
                </div>

                <!-- Type -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Tipe Overtime <span class="text-red-500">*</span>
                    </label>
                    <select name="type" 
                            required
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
                        <option value="">-- Pilih Tipe --</option>
                        <option value="regular" {{ old('type') == 'regular' ? 'selected' : '' }}>
                            🕐 Overtime Harian
                        </option>
                        <option value="additional" {{ old('type') == 'additional' ? 'selected' : '' }}>
                            📝 Overtime Susulan
                        </option>
                    </select>
                </div>

                <!-- Buttons -->
                <div class="flex gap-3 pt-4">
                    <button type="submit" 
                            class="flex-1 bg-indigo-600 hover:bg-indigo-700 text-white font-medium py-3 px-6 rounded-lg flex items-center justify-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                        </svg>
                        Buka Halaman Print
                    </button>
                    <a href="{{ route('dashboard') }}" 
                       class="bg-gray-200 hover:bg-gray-300 text-gray-700 font-medium py-3 px-6 rounded-lg">
                        Batal
                    </a>
                </div>
            </div>
        </form>
    </div>

    <!-- Preview Info -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mt-6">
        <h3 class="text-md font-semibold text-gray-800 mb-3">📋 Isi Laporan</h3>
        <ul class="text-sm text-gray-600 space-y-2">
            <li class="flex items-start">
                <span class="text-green-500 mr-2">✓</span>
                <span>Header laporan dengan nama perusahaan dan tipe overtime</span>
            </li>
            <li class="flex items-start">
                <span class="text-green-500 mr-2">✓</span>
                <span>Tabel detail overtime per karyawan (tanggal, batch, nama, waktu, jam, deskripsi)</span>
            </li>
            <li class="flex items-start">
                <span class="text-green-500 mr-2">✓</span>
                <span>Summary total karyawan dan total jam overtime</span>
            </li>
            <li class="flex items-start">
                <span class="text-green-500 mr-2">✓</span>
                <span><strong>E-Signature Supervisor dan Manager</strong> (gambar tandatangan digital)</span>
            </li>
            <li class="flex items-start">
                <span class="text-green-500 mr-2">✓</span>
                <span>Tanggal approval dari masing-masing approver</span>
            </li>
        </ul>
    </div>
                <span>Informasi periode dan summary (total karyawan, total jam)</span>
            </li>
            <li class="flex items-start">
                <span class="text-green-500 mr-2">✓</span>
                <span>Data overtime dikelompokkan per tanggal, tipe, dan batch</span>
            </li>
            <li class="flex items-start">
                <span class="text-green-500 mr-2">✓</span>
                <span>Detail karyawan, jam overtime, dan deskripsi pekerjaan</span>
            </li>
            <li class="flex items-start">
                <span class="text-green-500 mr-2">✓</span>
                <span>Grand total seluruh karyawan dan jam overtime</span>
            </li>
            <li class="flex items-start">
                <span class="text-green-500 mr-2">✓</span>
                <span><strong>Signature Supervisor</strong> (tahap 1 approval)</span>
            </li>
            <li class="flex items-start">
                <span class="text-green-500 mr-2">✓</span>
                <span><strong>Signature Manager</strong> (final approval)</span>
            </li>
        </ul>
    </div>

    <!-- Requirements -->
    <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mt-6">
        <p class="text-sm text-yellow-800 font-medium mb-2">⚠️ Persyaratan:</p>
        <ul class="text-sm text-yellow-700 space-y-1 ml-4 list-disc">
            <li>Hanya overtime dengan status <strong>fully_approved</strong> yang masuk PDF</li>
            <li>Pastikan Supervisor dan Manager sudah upload signature di menu Profile → Signature</li>
            <li>Jika signature belum diupload, akan muncul placeholder "(Signature tidak tersedia)"</li>
        </ul>
    </div>

</div>
@endsection
