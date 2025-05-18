<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FitTrack - Rockies Fitness</title>
    <meta name="description" content="Gym management dashboard for tracking analytics and attendance">
    <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&display=swap" rel="stylesheet">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Expires" content="0">
    <link rel="icon" type="image/png" sizes="180x180" href="{{ asset('images/rockiesLogo.png') }}">
    @vite('resources/css/app.css')
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<style>
    ::-webkit-scrollbar {
        width: 2px;
        height: 8px;
    }
    ::-webkit-scrollbar-track {
        background: rgba(52, 52, 52, 0.8);
        border-radius: 10px;
    }
    ::-webkit-scrollbar-thumb {
        background: rgb(255, 81, 0);
        border-radius: 10px;
    }
    ::-webkit-scrollbar-thumb:hover {
        background: rgba(255, 153, 45, 0.8);
    }

    /* Sidebar and main content styles */
    #sidebar {
        width: 16rem; /* 256px */
        transition: transform 0.3s ease-in-out;
    }
    #main-content {
        margin-left: 16rem; /* Match sidebar width */
        transition: margin-left 0.3s ease-in-out;
    }
    @media (max-width: 767px) {
        #sidebar {
            width: 16rem;
            transform: translateX(-100%);
        }
        #sidebar.mobile-open {
            transform: translateX(0);
        }
        #main-content {
            margin-left: 0;
        }
    }
</style>
<body class="font-sans bg-[#121212] overflow-x-hidden">
    <div x-data="{ sidebarOpen: true }" 
         x-init="() => {
             Alpine.store('sidebarOpen', true);
             $watch('$store.sidebarOpen', value => {
                 sidebarOpen = value;
                 document.getElementById('sidebar').classList.toggle('mobile-open', value);
             });
         }" class="flex flex-col md:flex-row min-h-screen">
        <!-- Sidebar -->
        <div id="sidebar" 
            class="fixed inset-y-0 left-0 z-30 bg-gray-900 text-white overflow-y-auto transition-transform duration-300 ease-in-out"
            :class="{'translate-x-0': $store.sidebarOpen, '-translate-x-full': !$store.sidebarOpen}">
            @include('components.sidebar')
        </div>

        <!-- Mobile overlay -->
        <div id="sidebar-overlay" 
            class="fixed inset-0 bg-black opacity-50 z-20 transition-opacity duration-300 ease-in-out"
            :class="{'block': $store.sidebarOpen, 'hidden': !$store.sidebarOpen}"
            @click="$store.sidebarOpen = false"></div>

        <!-- Main Content -->
        <div id="main-content" 
             class="w-full will-change-transform">
            <div class="sticky top-0 z-10 bg-[#121212]">
                @include('layouts.navigation')
            </div>
            <div class="px-4 md:px-10 pb-20 md:pb-10">
                @yield('content')
            </div>
        </div>
    </div>

<script>
    // Add event listener for window resize to manage sidebar state
    window.addEventListener('resize', function() {
        if (window.innerWidth >= 768) {
            Alpine.store('sidebarOpen', true); // Keep sidebar open on desktop
        } else {
            Alpine.store('sidebarOpen', false); // Close sidebar on mobile
        }
    });
</script>
</body>
</html>