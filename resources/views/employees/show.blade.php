@extends('layouts.app')

@section('title', 'Detail Karyawan')
@section('page-title', 'Detail Data Karyawan')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
        <!-- Header -->
        <div class="bg-gradient-to-r from-indigo-600 to-indigo-700 px-6 py-8">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-2xl font-bold text-white">{{ $employee->name }}</h2>
                    <p class="text-indigo-100 mt-1">NRP: {{ $employee->nrp }}</p>
                </div>
                <div>
                    @if($employee->is_active)
                        <span class="px-4 py-2 bg-green-500 text-white text-sm font-semibold rounded-full">Aktif</span>
                    @else
                        <span class="px-4 py-2 bg-gray-500 text-white text-sm font-semibold rounded-full">Tidak Aktif</span>
                    @endif
                </div>
            </div>
        </div>

        <!-- Content -->
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <h3 class="text-sm font-medium text-gray-500 mb-2">Seksi</h3>
                    <p class="text-lg font-semibold text-gray-900">{{ $employee->section->name }}</p>
                </div>

                <div>
                    <h3 class="text-sm font-medium text-gray-500 mb-2">Jabatan</h3>
                    <p class="text-lg font-semibold text-gray-900">{{ $employee->position }}</p>
                </div>

                <div>
                    <h3 class="text-sm font-medium text-gray-500 mb-2">Shift</h3>
                    <span class="inline-block px-3 py-1 bg-blue-100 text-blue-800 text-sm font-medium rounded">
                        {{ $employee->shift }}
                    </span>
                </div>

                <div>
                    <h3 class="text-sm font-medium text-gray-500 mb-2">Role</h3>
                    <span class="inline-block px-3 py-1 bg-purple-100 text-purple-800 text-sm font-medium rounded">
                        {{ $employee->role->name }}
                    </span>
                </div>

                <div>
                    <h3 class="text-sm font-medium text-gray-500 mb-2">Status</h3>
                    @if($employee->is_active)
                        <span class="inline-block px-3 py-1 bg-green-100 text-green-800 text-sm font-medium rounded">
                            Aktif
                        </span>
                    @else
                        <span class="inline-block px-3 py-1 bg-gray-100 text-gray-800 text-sm font-medium rounded">
                            Tidak Aktif
                        </span>
                    @endif
                </div>

                <div>
                    <h3 class="text-sm font-medium text-gray-500 mb-2">Terdaftar Sejak</h3>
                    <p class="text-gray-900">{{ $employee->created_at->format('d M Y H:i') }}</p>
                </div>

                <div>
                    <h3 class="text-sm font-medium text-gray-500 mb-2">Terakhir Diperbarui</h3>
                    <p class="text-gray-900">{{ $employee->updated_at->format('d M Y H:i') }}</p>
                </div>
            </div>
        </div>

        <!-- Actions -->
        <div class="bg-gray-50 px-6 py-4 border-t border-gray-200 flex justify-between">
            <a href="{{ route('employees.index') }}" 
               class="px-6 py-2 border border-gray-300 text-gray-700 font-medium rounded-lg hover:bg-gray-100">
                Kembali
            </a>
            <div class="flex space-x-3">
                <a href="{{ route('employees.edit', $employee) }}" 
                   class="px-6 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-medium rounded-lg">
                    Edit Data
                </a>
                <form method="POST" 
                      action="{{ route('employees.destroy', $employee) }}" 
                      class="inline"
                      onsubmit="return confirm('Yakin ingin menghapus data karyawan ini?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" 
                            class="px-6 py-2 bg-red-600 hover:bg-red-700 text-white font-medium rounded-lg">
                        Hapus Data
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
