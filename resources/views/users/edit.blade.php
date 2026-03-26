@extends('layouts.app')

@section('title', 'Edit User')
@section('page-title', 'Edit User')

@section('content')
<div class="max-w-3xl mx-auto">
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6" x-data="{ 
        selectedRole: '{{ old('role_id', $user->role_id) }}',
        roles: @js($roles),
        isSupervisorOrManager: false,
        init() {
            this.checkRole();
        },
        checkRole() {
            const role = this.roles.find(r => r.id == this.selectedRole);
            if (role) {
                this.isSupervisorOrManager = role.name.toLowerCase().includes('supervisor') || 
                                            role.name.toLowerCase().includes('manager');
            } else {
                this.isSupervisorOrManager = false;
            }
        }
    }">
        <!-- Employee Info Display (Read-only) -->
        <div class="mb-6 p-4 bg-gray-50 rounded-lg">
            <h4 class="text-sm font-medium text-gray-700 mb-3">Informasi Karyawan:</h4>
            <div class="grid grid-cols-2 gap-4 text-sm">
                <div>
                    <span class="text-gray-500">NRP:</span>
                    <span class="ml-2 font-medium text-gray-900">{{ $user->employee->nrp }}</span>
                </div>
                <div>
                    <span class="text-gray-500">Nama:</span>
                    <span class="ml-2 font-medium text-gray-900">{{ $user->employee->name }}</span>
                </div>
                <div>
                    <span class="text-gray-500">Seksi:</span>
                    <span class="ml-2 font-medium text-gray-900">{{ $user->employee->section->name }}</span>
                </div>
                <div>
                    <span class="text-gray-500">Jabatan:</span>
                    <span class="ml-2 font-medium text-gray-900">{{ $user->employee->position }}</span>
                </div>
                <div>
                    <span class="text-gray-500">Shift:</span>
                    <span class="ml-2 font-medium text-gray-900">{{ $user->employee->shift }}</span>
                </div>
            </div>
            <p class="mt-2 text-xs text-gray-500">Data karyawan tidak dapat diubah di sini. Edit melalui Master Data Karyawan.</p>
        </div>

        <form action="{{ route('users.update', $user) }}" method="POST">
            @csrf
            @method('PUT')

            <!-- Role Selection -->
            <div class="mb-6">
                <label for="role_id" class="block text-sm font-medium text-gray-700 mb-2">
                    Role <span class="text-red-500">*</span>
                </label>
                <select id="role_id" 
                        name="role_id" 
                        required
                        x-model="selectedRole"
                        @change="checkRole()"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('role_id') border-red-500 @enderror">
                    <option value="">Pilih Role</option>
                    @foreach($roles as $role)
                        <option value="{{ $role->id }}" {{ old('role_id', $user->role_id) == $role->id ? 'selected' : '' }}>
                            {{ $role->name }}
                            @if($role->description)
                                - {{ $role->description }}
                            @endif
                        </option>
                    @endforeach
                </select>
                @error('role_id')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
                <p class="mt-1 text-xs text-gray-500">Role menentukan akses dan permission user di sistem.</p>
            </div>

            <!-- Email -->
            <div class="mb-6">
                <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                    Email <span class="text-red-500">*</span>
                </label>
                <input type="email" 
                       id="email" 
                       name="email" 
                       value="{{ old('email', $user->email) }}"
                       required
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('email') border-red-500 @enderror">
                @error('email')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Multiple Sections (for Supervisor/Manager) -->
            <div class="mb-6" x-show="isSupervisorOrManager" x-cloak>
                <label class="block text-sm font-medium text-gray-700 mb-3">
                    Seksi yang Dibawahi <span class="text-red-500">*</span>
                </label>
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-3">
                    <p class="text-sm text-blue-800">
                        ℹ️ <strong>Supervisor/Manager</strong> dapat membawahi lebih dari 1 seksi. 
                        Pilih seksi-seksi yang akan dikelola oleh user ini untuk approval overtime.
                    </p>
                </div>
                <div class="grid grid-cols-2 gap-3">
                    @foreach($sections as $section)
                        <label class="flex items-center p-3 border border-gray-300 rounded-lg hover:bg-gray-50 cursor-pointer">
                            <input type="checkbox" 
                                   name="section_ids[]" 
                                   value="{{ $section->id }}"
                                   {{ in_array($section->id, old('section_ids', $userSectionIds)) ? 'checked' : '' }}
                                   class="w-4 h-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500">
                            <span class="ml-3 text-sm text-gray-700">{{ $section->name }}</span>
                        </label>
                    @endforeach
                </div>
                <p class="mt-2 text-xs text-gray-500">
                    Seksi primary dari employee ({{ $user->employee->section->name }}) akan tetap ada. 
                    Pilih seksi tambahan di atas.
                </p>
            </div>

            <!-- Password (Optional) -->
            <div class="mb-6">
                <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
                    Password Baru
                </label>
                <input type="password" 
                       id="password" 
                       name="password" 
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('password') border-red-500 @enderror">
                @error('password')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
                <p class="mt-1 text-xs text-gray-500">Biarkan kosong jika tidak ingin mengubah password. Minimal 8 karakter.</p>
            </div>

            <!-- Password Confirmation -->
            <div class="mb-6">
                <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-2">
                    Konfirmasi Password Baru
                </label>
                <input type="password" 
                       id="password_confirmation" 
                       name="password_confirmation" 
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
                    Update
                </button>
            </div>
        </form>
    </div>
</div>

<style>
[x-cloak] { display: none !important; }
</style>

@endsection
