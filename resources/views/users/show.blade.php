@extends('layouts.app')

@section('title', 'Detail User')
@section('page-title', 'Detail User')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <!-- Header -->
        <div class="flex justify-between items-start mb-6">
            <div>
                <h2 class="text-2xl font-bold text-gray-900">{{ $user->employee->name }}</h2>
                <p class="text-gray-600 mt-1">{{ $user->email }}</p>
            </div>
            <div class="flex space-x-2">
                <a href="{{ route('users.edit', $user) }}" 
                   class="px-4 py-2 bg-yellow-500 hover:bg-yellow-600 text-white rounded-lg transition inline-flex items-center">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                    </svg>
                    Edit
                </a>
                <a href="{{ route('users.index') }}" 
                   class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition">
                    Kembali
                </a>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Account Information -->
            <div class="border border-gray-200 rounded-lg p-5">
                <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                    </svg>
                    Informasi Akun
                </h3>
                <div class="space-y-3">
                    <div>
                        <span class="block text-sm font-medium text-gray-500">Email</span>
                        <span class="block text-base text-gray-900 mt-1">{{ $user->email }}</span>
                    </div>
                    <div>
                        <span class="block text-sm font-medium text-gray-500">Terdaftar Sejak</span>
                        <span class="block text-base text-gray-900 mt-1">{{ $user->created_at->format('d F Y, H:i') }}</span>
                    </div>
                    <div>
                        <span class="block text-sm font-medium text-gray-500">Terakhir Diperbarui</span>
                        <span class="block text-base text-gray-900 mt-1">{{ $user->updated_at->format('d F Y, H:i') }}</span>
                    </div>
                </div>
            </div>

            <!-- Employee Information -->
            <div class="border border-gray-200 rounded-lg p-5">
                <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2"></path>
                    </svg>
                    Informasi Karyawan
                </h3>
                <div class="space-y-3">
                    <div>
                        <span class="block text-sm font-medium text-gray-500">NRP</span>
                        <span class="block text-base text-gray-900 mt-1">{{ $user->employee->nrp }}</span>
                    </div>
                    <div>
                        <span class="block text-sm font-medium text-gray-500">Nama Lengkap</span>
                        <span class="block text-base text-gray-900 mt-1">{{ $user->employee->name }}</span>
                    </div>
                    <div>
                        <span class="block text-sm font-medium text-gray-500">Seksi</span>
                        <span class="inline-block px-3 py-1 bg-blue-100 text-blue-800 text-sm font-medium rounded mt-1">
                            {{ $user->employee->section->name }}
                        </span>
                    </div>
                    <div>
                        <span class="block text-sm font-medium text-gray-500">Jabatan</span>
                        <span class="block text-base text-gray-900 mt-1">{{ $user->employee->position }}</span>
                    </div>
                    <div>
                        <span class="block text-sm font-medium text-gray-500">Shift</span>
                        <span class="inline-block px-3 py-1 bg-purple-100 text-purple-800 text-sm font-medium rounded mt-1">
                            {{ $user->employee->shift }}
                        </span>
                    </div>
                    <div>
                        <span class="block text-sm font-medium text-gray-500">Role</span>
                        <span class="inline-block px-3 py-1 
                            @if($user->employee->role->name === 'Super Admin') bg-purple-100 text-purple-800
                            @elseif($user->employee->role->name === 'Group Leader') bg-green-100 text-green-800
                            @elseif($user->employee->role->name === 'Foreman') bg-yellow-100 text-yellow-800
                            @else bg-gray-100 text-gray-800 @endif
                            text-sm font-medium rounded mt-1">
                            {{ $user->employee->role->name }}
                        </span>
                    </div>
                </div>

                <div class="mt-4 pt-4 border-t border-gray-200">
                    <a href="{{ route('employees.show', $user->employee) }}" 
                       class="text-sm text-indigo-600 hover:text-indigo-800 inline-flex items-center">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path>
                        </svg>
                        Lihat Detail Karyawan
                    </a>
                </div>
            </div>
        </div>

        <!-- Delete Button -->
        @if($user->id !== auth()->id())
        <div class="mt-6 pt-6 border-t border-gray-200">
            <form action="{{ route('users.destroy', $user) }}" 
                  method="POST" 
                  onsubmit="return confirm('Apakah Anda yakin ingin menghapus user ini? Tindakan ini tidak dapat dibatalkan.')">
                @csrf
                @method('DELETE')
                <button type="submit" 
                        class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg transition inline-flex items-center">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                    </svg>
                    Hapus User
                </button>
            </form>
        </div>
        @endif
    </div>
</div>
@endsection
