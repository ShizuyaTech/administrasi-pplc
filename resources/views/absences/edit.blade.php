@extends('layouts.app')

@section('title', 'Edit Data Absensi')
@section('page-title', 'Edit Data Absensi')

@section('content')
<div class="max-w-3xl mx-auto">
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <form method="POST" action="{{ route('absences.update', $absence) }}">
            @csrf
            @method('PUT')

            <!-- Section (Super Admin Only) -->
            @if(auth()->user()->canManageAllSections())
            <div class="mb-6">
                <label for="section_id" class="block text-sm font-medium text-gray-700 mb-2">
                    Seksi <span class="text-red-500">*</span>
                </label>
                <select id="section_id" 
                        name="section_id" 
                        required
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('section_id') border-red-500 @enderror">
                    <option value="">Pilih Seksi</option>
                    @foreach($sections as $section)
                        <option value="{{ $section->id }}" {{ old('section_id', $absence->section_id) == $section->id ? 'selected' : '' }}>
                            {{ $section->name }}
                        </option>
                    @endforeach
                </select>
                @error('section_id')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
            @else
            <div class="mb-6 bg-indigo-50 border border-indigo-200 rounded-lg p-4">
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <p class="text-sm text-indigo-800">
                            <strong>Seksi:</strong> {{ $absence->section->name }}
                        </p>
                    </div>
                    <div>
                        <p class="text-sm text-indigo-800">
                            <strong>Shift:</strong> {{ $shift ?? '-' }}
                        </p>
                    </div>
                </div>
            </div>
            @endif

            <!-- Date -->
            <div class="mb-6">
                <label for="date" class="block text-sm font-medium text-gray-700 mb-2">
                    Tanggal <span class="text-red-500">*</span>
                </label>
                <input type="date" 
                       id="date" 
                       name="date" 
                       value="{{ old('date', $absence->date->format('Y-m-d')) }}" 
                       required
                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('date') border-red-500 @enderror">
                @error('date')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Attendance Numbers Grid -->
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
                <!-- Present -->
                <div>
                    <label for="present" class="block text-sm font-medium text-gray-700 mb-2">
                        Hadir <span class="text-red-500">*</span>
                    </label>
                    <input type="number" 
                           id="present" 
                           name="present" 
                           value="{{ old('present', $absence->present) }}" 
                           min="0"
                           required
                           onchange="calculateTotal()"
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('present') border-red-500 @enderror">
                    @error('present')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Sick -->
                <div>
                    <label for="sick" class="block text-sm font-medium text-gray-700 mb-2">
                        Sakit <span class="text-red-500">*</span>
                    </label>
                    <input type="number" 
                           id="sick" 
                           name="sick" 
                           value="{{ old('sick', $absence->sick) }}" 
                           min="0"
                           required
                           onchange="calculateTotal()"
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('sick') border-red-500 @enderror">
                    @error('sick')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Permission -->
                <div>
                    <label for="permission" class="block text-sm font-medium text-gray-700 mb-2">
                        Izin <span class="text-red-500">*</span>
                    </label>
                    <input type="number" 
                           id="permission" 
                           name="permission" 
                           value="{{ old('permission', $absence->permission) }}" 
                           min="0"
                           required
                           onchange="calculateTotal()"
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('permission') border-red-500 @enderror">
                    @error('permission')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Leave -->
                <div>
                    <label for="leave" class="block text-sm font-medium text-gray-700 mb-2">
                        Cuti <span class="text-red-500">*</span>
                    </label>
                    <input type="number" 
                           id="leave" 
                           name="leave" 
                           value="{{ old('leave', $absence->leave) }}" 
                           min="0"
                           required
                           onchange="calculateTotal()"
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('leave') border-red-500 @enderror">
                    @error('leave')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Total Members -->
            <div class="mb-6">
                <label for="total_members" class="block text-sm font-medium text-gray-700 mb-2">
                    Total Member <span class="text-red-500">*</span>
                </label>
                <input type="number" 
                       id="total_members" 
                       name="total_members" 
                       value="{{ old('total_members', $absence->total_members) }}" 
                       min="1"
                       required
                       readonly
                       class="w-full px-4 py-3 border border-gray-300 rounded-lg bg-gray-100 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('total_members') border-red-500 @enderror">
                <p class="mt-1 text-sm text-gray-500">Otomatis terhitung dari Hadir + Sakit + Izin + Cuti</p>
                @error('total_members')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Notes -->
            <div class="mb-6">
                <label for="notes" class="block text-sm font-medium text-gray-700 mb-2">
                    Keterangan (Opsional)
                </label>
                <textarea id="notes" 
                          name="notes" 
                          rows="4"
                          class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('notes') border-red-500 @enderror">{{ old('notes', $absence->notes) }}</textarea>
                @error('notes')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Action Buttons -->
            <div class="flex justify-end space-x-3">
                <a href="{{ route('absences.index') }}" 
                   class="px-6 py-3 border border-gray-300 text-gray-700 font-medium rounded-lg hover:bg-gray-50 transition">
                    Batal
                </a>
                <button type="submit" 
                        class="px-6 py-3 bg-indigo-600 hover:bg-indigo-700 text-white font-medium rounded-lg transition">
                    Update Data
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
function calculateTotal() {
    const present = parseInt(document.getElementById('present').value) || 0;
    const sick = parseInt(document.getElementById('sick').value) || 0;
    const permission = parseInt(document.getElementById('permission').value) || 0;
    const leave = parseInt(document.getElementById('leave').value) || 0;
    
    const total = present + sick + permission + leave;
    document.getElementById('total_members').value = total;
}

// Calculate on page load
document.addEventListener('DOMContentLoaded', function() {
    calculateTotal();
});
</script>
@endpush
@endsection
