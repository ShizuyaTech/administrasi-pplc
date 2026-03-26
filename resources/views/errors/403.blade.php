<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>403 - Akses Ditolak</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50 min-h-screen flex items-center justify-center">
    <div class="text-center px-4">
        <!-- Icon -->
        <div class="flex justify-center mb-6">
            <div class="w-24 h-24 rounded-full bg-red-100 flex items-center justify-center">
                <svg class="w-12 h-12 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
                </svg>
            </div>
        </div>

        <!-- Code -->
        <h1 class="text-7xl font-extrabold text-gray-800 mb-2">403</h1>

        <!-- Title -->
        <h2 class="text-2xl font-semibold text-gray-700 mb-3">Akses Ditolak</h2>

        <!-- Message -->
        <p class="text-gray-500 mb-8 max-w-md mx-auto">
            Anda tidak memiliki izin untuk mengakses halaman ini.<br>
            Silakan hubungi administrator jika Anda merasa ini adalah kesalahan.
        </p>

        <!-- Buttons -->
        <div class="flex flex-col sm:flex-row gap-3 justify-center">
            <a href="{{ url()->previous() !== url()->current() ? url()->previous() : route('dashboard') }}"
               class="inline-flex items-center gap-2 px-5 py-2.5 bg-gray-200 hover:bg-gray-300 text-gray-700 font-medium rounded-lg transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
                Kembali
            </a>
            <a href="{{ route('dashboard') }}"
               class="inline-flex items-center gap-2 px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                </svg>
                Ke Dashboard
            </a>
        </div>

        <!-- User info hint -->
        @auth
        <p class="mt-8 text-sm text-gray-400">
            Login sebagai: <span class="font-medium text-gray-500">{{ auth()->user()->name }}</span>
            @if(auth()->user()->role)
                &bull; Role: <span class="font-medium text-gray-500">{{ auth()->user()->role->name }}</span>
            @endif
        </p>
        @endauth
    </div>
</body>
</html>
