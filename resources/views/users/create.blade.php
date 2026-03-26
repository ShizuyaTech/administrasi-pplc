@extends('layouts.app')

@section('title', 'Tambah User')
@section('page-title', 'Tambah User')

@section('content')
<div class="max-w-3xl mx-auto">
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <form action="{{ route('users.store') }}" method="POST">
            @csrf

            <!-- Employee Selection -->
            <div class="mb-6">
                <label for="employee_id" class="block text-sm font-medium text-gray-700 mb-2">
                    Pilih Karyawan <span class="text-red-500">*</span>
                </label>
                <select id="employee_id" 
                        name="employee_id" 
                        required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('employee_id') border-red-500 @enderror"
                        onchange="updateEmployeeInfo()">
                    <option value="">-- Pilih Karyawan --</option>
                    @foreach($employees as $employee)
                        <option value="{{ $employee->id }}" 
                                {{ old('employee_id') == $employee->id ? 'selected' : '' }}
                                data-name="{{ $employee->name }}"
                                data-nrp="{{ $employee->nrp }}"
                                data-section="{{ $employee->section->name }}"
                                data-position="{{ $employee->position }}"
                                data-shift="{{ $employee->shift }}"
                                data-role="{{ $employee->role?->name ?? '' }}">
                            {{ $employee->nrp }} - {{ $employee->name }} ({{ $employee->section->name }})
                        </option>
                    @endforeach
                </select>
                @error('employee_id')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
                <p class="mt-1 text-xs text-gray-500">Hanya karyawan aktif yang belum memiliki akun user yang ditampilkan</p>
            </div>

            <!-- Employee Info Display -->
            <div id="employeeInfo" class="mb-6 p-4 bg-gray-50 rounded-lg hidden">
                <h4 class="text-sm font-medium text-gray-700 mb-3">Informasi Karyawan:</h4>
                <div class="grid grid-cols-2 gap-4 text-sm">
                    <div>
                        <span class="text-gray-500">NRP:</span>
                        <span id="info-nrp" class="ml-2 font-medium text-gray-900"></span>
                    </div>
                    <div>
                        <span class="text-gray-500">Nama:</span>
                        <span id="info-name" class="ml-2 font-medium text-gray-900"></span>
                    </div>
                    <div>
                        <span class="text-gray-500">Seksi:</span>
                        <span id="info-section" class="ml-2 font-medium text-gray-900"></span>
                    </div>
                    <div>
                        <span class="text-gray-500">Jabatan:</span>
                        <span id="info-position" class="ml-2 font-medium text-gray-900"></span>
                    </div>
                    <div>
                        <span class="text-gray-500">Shift:</span>
                        <span id="info-shift" class="ml-2 font-medium text-gray-900"></span>
                    </div>
                    <div>
                        <span class="text-gray-500">Role:</span>
                        <span id="info-role" class="ml-2 font-medium text-gray-900"></span>
                    </div>
                </div>
            </div>

            <!-- Email -->
            <div class="mb-6">
                <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                    Email <span class="text-red-500">*</span>
                </label>
                <input type="email" 
                       id="email" 
                       name="email" 
                       value="{{ old('email') }}"
                       required
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('email') border-red-500 @enderror">
                @error('email')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Password -->
            <div class="mb-6">
                <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
                    Password <span class="text-red-500">*</span>
                </label>
                <input type="password" 
                       id="password" 
                       name="password" 
                       required
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('password') border-red-500 @enderror">
                @error('password')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
                <p class="mt-1 text-xs text-gray-500">Minimal 8 karakter</p>
            </div>

            <!-- Password Confirmation -->
            <div class="mb-6">
                <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-2">
                    Konfirmasi Password <span class="text-red-500">*</span>
                </label>
                <input type="password" 
                       id="password_confirmation" 
                       name="password_confirmation" 
                       required
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
            </div>

            <!-- Action Buttons -->
            <div class="flex justify-end space-x-3 pt-6 border-t border-gray-200">
                <a href="{{ route('users.index') }}" 
                   class="px-6 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition">
                    Batal
                </a>
                <button type="submit" 
                        class="px-6 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg transition">
                    Simpan
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
function updateEmployeeInfo() {
    const select = document.getElementById('employee_id');
    const option = select.options[select.selectedIndex];
    const infoDiv = document.getElementById('employeeInfo');
    
    if (option.value) {
        document.getElementById('info-nrp').textContent = option.dataset.nrp;
        document.getElementById('info-name').textContent = option.dataset.name;
        document.getElementById('info-section').textContent = option.dataset.section;
        document.getElementById('info-position').textContent = option.dataset.position;
        document.getElementById('info-shift').textContent = option.dataset.shift;
        document.getElementById('info-role').textContent = option.dataset.role;
        infoDiv.classList.remove('hidden');
    } else {
        infoDiv.classList.add('hidden');
    }
}

// Trigger on page load if there's old value
document.addEventListener('DOMContentLoaded', function() {
    const select = document.getElementById('employee_id');
    if (select.value) {
        updateEmployeeInfo();
    }
});
</script>
@endpush
@endsection
