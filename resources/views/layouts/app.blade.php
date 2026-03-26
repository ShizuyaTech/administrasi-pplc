<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Dashboard') - Administrasi PPLC IPPI</title>
    
    <!-- Favicon -->
    <link rel="icon" type="image/png" href="{{ asset('images/LOGO-IPPI.jpg') }}">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50">
    <div x-data="{ sidebarOpen: false }" class="flex h-screen overflow-hidden">
        <!-- Sidebar -->
        @include('components.sidebar')

        <!-- Main Content Area -->
        <div class="flex-1 flex flex-col overflow-hidden">
            <!-- Top Navigation Bar -->
            @include('components.navbar')

            <!-- Main Content -->
            <main class="flex-1 overflow-y-auto bg-gray-50 p-4 md:p-6 lg:p-8">
                @if(session('success'))
                    <div x-data="{ show: true }" 
                         x-show="show" 
                         x-init="setTimeout(() => show = false, 3000)"
                         class="mb-4 bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg">
                        {{ session('success') }}
                    </div>
                @endif

                @if(session('error'))
                    <div x-data="{ show: true }" 
                         x-show="show" 
                         x-init="setTimeout(() => show = false, 3000)"
                         class="mb-4 bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg">
                        {{ session('error') }}
                    </div>
                @endif

                @yield('content')
            </main>
        </div>
    </div>
    
    @stack('scripts')
    <script>
        // Detect browser timezone and store in cookie so server can use it
        (function() {
            try {
                var tz = Intl.DateTimeFormat().resolvedOptions().timeZone;
                if (tz) {
                    document.cookie = 'user_timezone=' + encodeURIComponent(tz) + '; path=/; max-age=86400; SameSite=Lax';
                }
            } catch(e) {}
        })();
    </script>
</body>
</html>
