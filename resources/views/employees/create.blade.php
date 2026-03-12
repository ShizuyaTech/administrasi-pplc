@extends('layouts.app')

@section('title', 'Tambah Karyawan')
@section('page-title', 'Tambah Data Karyawan')

@section('content')
<div class="max-w-3xl mx-auto">
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <form method="POST" action="{{ route('employees.store') }}">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <div>
                    <label for="nrp" class="block text-sm font-medium text-gray-700 mb-2">NRP <span class="text-red-500">*</span></label>
                    <input type="text" 
                           id="nrp" 
                           name="nrp" 
                           value="{{ old('nrp') }}" 
                           required 
                           placeholder="Nomor Registrasi Pegawai"
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 @error('nrp') border-red-500 @enderror">
                    @error('nrp')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-2">Nama Lengkap <span class="text-red-500">*</span></label>
                    <input type="text" 
                           id="name" 
                           name="name" 
                           value="{{ old('name') }}" 
                           required 
                           placeholder="Nama karyawan"
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 @error('name') border-red-500 @enderror">
                    @error('name')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <div>
                    <label for="section_id" class="block text-sm font-medium text-gray-700 mb-2">Seksi <span class="text-red-500">*</span></label>
                    <select id="section_id" 
                            name="section_id" 
                            required 
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 @error('section_id') border-red-500 @enderror">
                        <option value="">-- Pilih Seksi --</option>
                        @foreach($sections as $section)
                            <option value="{{ $section->id }}" {{ old('section_id') == $section->id ? 'selected' : '' }}>
                                {{ $section->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('section_id')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="position" class="block text-sm font-medium text-gray-700 mb-2">Jabatan <span class="text-red-500">*</span></label>
                    <input type="text" 
                           id="position" 
                           name="position" 
                           value="{{ old('position') }}" 
                           required 
                           placeholder="Contoh: Operator, Leader, dll"
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 @error('position') border-red-500 @enderror">
                    @error('position')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <div>
                    <label for="shift" class="block text-sm font-medium text-gray-700 mb-2">Shift <span class="text-red-500">*</span></label>
                    <select id="shift" 
                            name="shift" 
                            required 
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 @error('shift') border-red-500 @enderror">
                        <option value="">-- Pilih Shift --</option>
                        <option value="Shift A" {{ old('shift') == 'Shift A' ? 'selected' : '' }}>Shift A</option>
                        <option value="Shift B" {{ old('shift') == 'Shift B' ? 'selected' : '' }}>Shift B</option>
                        <option value="Non Shift" {{ old('shift', 'Non Shift') == 'Non Shift' ? 'selected' : '' }}>Non Shift</option>
                    </select>
                    @error('shift')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>

                    <select id="role_id" 
                            name="role_id" 
                            required 
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 @error('role_id') border-red-500 @enderror">
                        <option value="">-- Pilih Role --</option>
                        @foreach($roles as $role)
                            <option value="{{ $role->id }}" {{ old('role_id') == $role->id ? 'selected' : '' }}>
                                {{ $role->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('role_id')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                    <p class="mt-1 text-sm text-gray-500">Role menentukan izin akses karyawan dalam sistem</p>
                </div>
            </div>

            <div class="mb-6">
                <label class="flex items-center">
                    <input type="checkbox" 
                           name="is_active" 
                           value="1"
                           {{ old('is_active', true) ? 'checked' : '' }}
                           class="w-4 h-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500">
                    <span class="ml-2 text-sm text-gray-700">Karyawan Aktif</span>
                </label>
                <p class="mt-1 text-sm text-gray-500">Centang jika karyawan masih aktif bekerja</p>
            </div>

            <div class="flex justify-end space-x-3">
                <a href="{{ route('employees.index') }}" 
                   class="px-6 py-3 border border-gray-300 text-gray-700 font-medium rounded-lg hover:bg-gray-50">
                    Batal
                </a>
                <button type="submit" 
                        class="px-6 py-3 bg-indigo-600 hover:bg-indigo-700 text-white font-medium rounded-lg">
                    Simpan Data
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
