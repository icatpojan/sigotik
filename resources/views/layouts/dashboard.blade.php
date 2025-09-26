<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0" />
    <meta http-equiv="X-UA-Compatible" content="ie=edge" />
    <title>@yield('title', 'Dashboard') | Sigotik - Sistem Manajemen BBM Kapal</title>
    <link rel="icon" type="image/png" href="{{ asset('images/logo-kkp.png') }}">
    <!-- Tailwind CSS CDN as fallback -->
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="{{ asset('css/tailadmin.css') }}">
    <script defer src="{{ asset('js/tailadmin.js') }}"></script>
</head>
<body x-data="{
    page: 'dashboard',
    'loaded': false,
    'darkMode': false,
    'stickyMenu': false,
    'sidebarToggle': false,
    'scrollTop': false
}" x-init="
    darkMode = JSON.parse(localStorage.getItem('darkMode'));
    $watch('darkMode', value => localStorage.setItem('darkMode', JSON.stringify(value)));
    setTimeout(() => { loaded = true; }, 500);
" :class="{'dark bg-gray-900': darkMode === true}">

    <!-- ===== Preloader Start ===== -->
    <div x-show="!loaded" class="fixed inset-0 z-[99999] flex items-center justify-center bg-white dark:bg-gray-900" style="width: 100vw; height: 100vh; position: fixed; top: 0; left: 0; z-index: 999999;">
        <div class="flex flex-col items-center">
            <div class="mb-6">
                <img src="{{ asset('images/logo-kkp.png') }}" alt="Logo KKP" class="h-16 w-auto animate-pulse" />
            </div>
            <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Memuat dashboard...</p>
        </div>
    </div>
    <!-- ===== Preloader End ===== -->

    <!-- ===== Page Wrapper Start ===== -->
    <div x-show="loaded" class="flex h-screen overflow-hidden">
        <!-- ===== Sidebar Start ===== -->
        @include('components.sidebar')
        <!-- ===== Sidebar End ===== -->

        <!-- ===== Content Area Start ===== -->
        <div class="relative flex flex-col flex-1 overflow-x-hidden overflow-y-auto">
            <!-- Small Device Overlay Start -->
            <div x-show="sidebarToggle" class="fixed inset-0 z-50 bg-black bg-opacity-50 lg:hidden" @click="sidebarToggle = false"></div>
            <!-- Small Device Overlay End -->

            <!-- ===== Header Start ===== -->
            @include('components.header')
            <!-- ===== Header End ===== -->

            <!-- ===== Main Content Start ===== -->
            <main>
                <div class="p-4 mx-auto max-w-7xl md:p-6">
                    @yield('content')
                </div>
            </main>
            <!-- ===== Main Content End ===== -->
        </div>
        <!-- ===== Content Area End ===== -->
    </div>
    <!-- ===== Page Wrapper End ===== -->

    <!-- Modal Section -->
    @yield('modals')
</body>
</html>
