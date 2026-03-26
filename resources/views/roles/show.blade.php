@extends('layouts.app')

@section('title', 'Detail Role')
@section('page-title', 'Detail Role')

@section('content')
<div class="space-y-6">
    
    <!-- Back Button -->
    <div>
        <a href="{{ route('roles.index') }}" 
           class="inline-flex items-center text-sm text-gray-600 hover:text-gray-900">
            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            Kembali ke Daftar Role
        </a>
    </div>

    <!-- Role Info Card -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <div class="flex items-start justify-between mb-6">
            <div>
                <h2 class="text-2xl font-bold text-gray-800">{{ $role->name }}</h2>
                <p class="text-sm text-gray-500 mt-1">{{ $role->description ?? 'Tidak ada deskripsi' }}</p>
            </div>
            <div class="flex space-x-2">
                <a href="{{ route('roles.permissions', $role) }}" 
                   class="inline-flex items-center px-4 py-2 bg-green-600 hover:bg-green-700 text-white text-sm font-medium rounded-lg">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                    </svg>
                    Kelola Permissions
                </a>
                <a href="{{ route('roles.edit', $role) }}" 
                   class="inline-flex items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                    </svg>
                    Edit Role
                </a>
            </div>
        </div>

        <div class="grid grid-cols-3 gap-4 py-4 border-t border-gray-200">
            <div class="text-center">
                <div class="text-3xl font-bold text-indigo-600">{{ $role->permissions->count() }}</div>
                <div class="text-sm text-gray-500 mt-1">Permissions</div>
            </div>
            <div class="text-center">
                <div class="text-3xl font-bold text-green-600">{{ $role->users->count() }}</div>
                <div class="text-sm text-gray-500 mt-1">Users</div>
            </div>
            <div class="text-center">
                <div class="text-sm text-gray-600 mt-2">
                    Created: {{ $role->created_at->format('d M Y') }}
                </div>
                @if($role->updated_at != $role->created_at)
                <div class="text-xs text-gray-400">
                    Updated: {{ $role->updated_at->format('d M Y') }}
                </div>
                @endif
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Permissions List -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-800">Permissions ({{ $role->permissions->count() }})</h3>
            </div>
            <div class="p-6">
                @if($role->permissions->count() > 0)
                    @php
                        $groupedPermissions = $role->permissions->groupBy('group');
                    @endphp
                    
                    <div class="space-y-4">
                        @foreach($groupedPermissions as $group => $permissions)
                        <div>
                            <h4 class="text-sm font-semibold text-gray-700 mb-2 uppercase">
                                {{ $group ?? 'Other' }}
                            </h4>
                            <div class="space-y-1">
                                @foreach($permissions as $permission)
                                <div class="flex items-center space-x-2 text-sm">
                                    <svg class="w-4 h-4 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                    </svg>
                                    <span class="text-gray-700">{{ $permission->name }}</span>
                                </div>
                                @endforeach
                            </div>
                        </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-8">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                        </svg>
                        <p class="mt-2 text-sm text-gray-500">Tidak ada permissions</p>
                        <a href="{{ route('roles.permissions', $role) }}" 
                           class="mt-4 inline-flex items-center text-sm text-indigo-600 hover:text-indigo-800">
                            Tambahkan Permissions
                        </a>
                    </div>
                @endif
            </div>
        </div>

        <!-- Users List -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-800">Users dengan Role Ini ({{ $role->users->count() }})</h3>
            </div>
            <div class="p-6">
                @if($role->users->count() > 0)
                    <div class="space-y-3 max-h-96 overflow-y-auto">
                        @foreach($role->users as $user)
                        <div class="flex items-center space-x-3 p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition">
                            <div class="w-10 h-10 bg-indigo-600 rounded-full flex items-center justify-center flex-shrink-0">
                                <span class="text-sm font-medium text-white">
                                    {{ strtoupper(substr($user->name, 0, 1)) }}
                                </span>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-gray-900 truncate">
                                    {{ $user->name }}
                                </p>
                                <p class="text-xs text-gray-500 truncate">
                                    {{ $user->email }}
                                </p>
                                @if($user->section)
                                <p class="text-xs text-gray-400 truncate">
                                    {{ $user->section->name }}
                                </p>
                                @endif
                            </div>
                            @if(auth()->user()->hasPermission('view-users'))
                            <a href="{{ route('users.show', $user) }}" 
                               class="text-indigo-600 hover:text-indigo-800">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path>
                                </svg>
                            </a>
                            @endif
                        </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-8">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                        </svg>
                        <p class="mt-2 text-sm text-gray-500">Belum ada users dengan role ini</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Danger Zone -->
    @if($role->users->count() == 0)
    <div class="bg-white rounded-lg shadow-sm border border-red-200">
        <div class="px-6 py-4 border-b border-red-200 bg-red-50">
            <h3 class="text-lg font-semibold text-red-800">Danger Zone</h3>
        </div>
        <div class="p-6">
            <div class="flex items-start justify-between">
                <div>
                    <h4 class="text-sm font-medium text-gray-900">Hapus Role</h4>
                    <p class="text-sm text-gray-500 mt-1">
                        Setelah dihapus, role ini tidak dapat dikembalikan. Pastikan tidak ada users yang menggunakan role ini.
                    </p>
                </div>
                <form action="{{ route('roles.destroy', $role) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus role {{ $role->name }}? Tindakan ini tidak dapat dibatalkan.')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" 
                            class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white text-sm font-medium rounded-lg">
                        Hapus Role
                    </button>
                </form>
            </div>
        </div>
    </div>
    @endif

</div>
@endsection
