@extends('layouts.app')

@section('title', 'Edit Batch Overtime')
@section('page-title', 'Edit Batch Overtime')

@section('content')
<div class="space-y-6">
    <!-- Back Button -->
    <div>
        <a href="{{ route('overtimes.index') }}" class="inline-flex items-center text-indigo-600 hover:text-indigo-900">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
            </svg>
            Kembali ke Daftar Overtime
        </a>
    </div>

    @php
        $firstOvertime = $overtimes->first();
    @endphp

    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-6">Edit Batch Overtime</h3>

        <!-- Batch Info (Read Only) -->
        <div class="bg-gray-50 border border-gray-200 rounded-lg p-4 mb-6">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="text-sm text-gray-500">Tanggal</label>
                    <p class="font-semibold">{{ $firstOvertime->date->format('d F Y') }}</p>
                </div>
                <div>
                    <label class="text-sm text-gray-500">Seksi</label>
                    <p class="font-semibold">{{ $firstOvertime->section->name }}</p>
                </div>
                <div>
                    <label class="text-sm text-gray-500">Jumlah Karyawan</label>
                    <p class="font-semibold">{{ $overtimes->count() }} orang</p>
                </div>
            </div>
        </div>

        <form method="POST" action="{{ route('overtimes.batch.update', ['batchId' => $batchId]) }}" x-data="batchEditForm()">
            @csrf
            @method('PUT')

            <!-- Waktu Overtime (Sama untuk Semua) -->
            <div class="grid grid-cols-2 gap-6 mb-6 p-4 border border-gray-300 rounded-lg bg-blue-50">
                <div>
                    <label for="start_time" class="block text-sm font-medium text-gray-700 mb-2">
                        Jam Mulai <span class="text-red-500">*</span>
                    </label>
                    <input type="time" 
                           id="start_time" 
                           name="start_time" 
                           value="{{ old('start_time', substr($firstOvertime->start_time, 0, 5)) }}" 
                           required 
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 @error('start_time') border-red-500 @enderror">
                    @error('start_time')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label for="end_time" class="block text-sm font-medium text-gray-700 mb-2">
                        Jam Selesai <span class="text-red-500">*</span>
                    </label>
                    <input type="time" 
                           id="end_time" 
                           name="end_time" 
                           value="{{ old('end_time', substr($firstOvertime->end_time, 0, 5)) }}" 
                           required 
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 @error('end_time') border-red-500 @enderror">
                    @error('end_time')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>
            </div>

            <p class="text-sm text-blue-700 mb-4">
                <svg class="w-4 h-4 inline mr-1" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                </svg>
                Waktu mulai dan selesai akan berlaku untuk semua karyawan dalam batch ini
            </p>

            <!-- Employee List -->
            <div class="mb-6">
                <h4 class="text-md font-semibold text-gray-900 mb-4">Daftar Karyawan & Pekerjaan</h4>
                <div class="space-y-4">
                    @foreach($overtimes as $index => $overtime)
                    <div class="border border-gray-300 rounded-lg p-4 bg-gray-50">
                        <input type="hidden" name="employees[{{ $index }}][id]" value="{{ $overtime->id }}">
                        
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div class="flex items-center">
                                <div>
                                    <label class="block text-xs text-gray-500 mb-1">Karyawan #{{ $index + 1 }}</label>
                                    <p class="font-semibold text-gray-900">{{ $overtime->employee_name }}</p>
                                </div>
                            </div>
                            
                            <div class="md:col-span-2">
                                <label for="work_desc_{{ $index }}" class="block text-sm font-medium text-gray-700 mb-2">
                                    Deskripsi Pekerjaan <span class="text-red-500">*</span>
                                </label>
                                <textarea id="work_desc_{{ $index }}" 
                                          name="employees[{{ $index }}][work_description]" 
                                          required 
                                          rows="2"
                                          class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 @error("employees.$index.work_description") border-red-500 @enderror"
                                          placeholder="Contoh: Quality check produk, Setting mesin, dll">{{ old("employees.$index.work_description", $overtime->work_description) }}</textarea>
                                @error("employees.$index.work_description")
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex justify-end space-x-3 pt-4 border-t">
                <a href="{{ route('overtimes.index') }}" 
                   class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 font-medium">
                    Batal
                </a>
                <button type="submit" 
                        class="px-6 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 font-medium">
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function batchEditForm() {
    return {
        init() {
            // You can add client-side validation here if needed
        }
    }
}
</script>
@endsection
