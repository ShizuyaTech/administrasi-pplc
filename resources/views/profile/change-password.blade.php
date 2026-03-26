@extends('layouts.app')

@section('title', 'Ganti Password')
@section('page-title', 'Ganti Password')

@section('content')
<div class="max-w-lg mx-auto">

    @if(session('success'))
    <div class="mb-6 p-4 bg-green-50 border border-green-200 rounded-lg flex items-center gap-3">
        <svg class="w-5 h-5 text-green-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
        </svg>
        <p class="text-sm text-green-700">{{ session('success') }}</p>
    </div>
    @endif

    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <div class="flex items-center gap-3 mb-6 pb-4 border-b border-gray-100">
            <div class="w-10 h-10 bg-indigo-100 rounded-full flex items-center justify-center">
                <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"></path>
                </svg>
            </div>
            <div>
                <h2 class="text-base font-semibold text-gray-900">Ganti Password</h2>
                <p class="text-sm text-gray-500">Pastikan password baru minimal 8 karakter</p>
            </div>
        </div>

        <form method="POST" action="{{ route('profile.password.update') }}">
            @csrf
            @method('PUT')

            <!-- Current Password -->
            <div class="mb-4">
                <label for="current_password" class="block text-sm font-medium text-gray-700 mb-1">Password Saat Ini</label>
                <input type="password"
                       id="current_password"
                       name="current_password"
                       autocomplete="current-password"
                       class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('current_password') border-red-500 @enderror">
                @error('current_password')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- New Password -->
            <div class="mb-4">
                <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Password Baru</label>
                <input type="password"
                       id="password"
                       name="password"
                       autocomplete="new-password"
                       class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('password') border-red-500 @enderror">
                @error('password')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Confirm Password -->
            <div class="mb-6">
                <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-1">Konfirmasi Password Baru</label>
                <input type="password"
                       id="password_confirmation"
                       name="password_confirmation"
                       autocomplete="new-password"
                       class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
            </div>

            <button type="submit"
                    class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-medium py-2.5 rounded-lg text-sm transition">
                Simpan Password Baru
            </button>
        </form>
    </div>
</div>
@endsection
