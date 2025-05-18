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

    /* Custom styles for sidebar */
    #sidebar {
        width: 16rem; /* Default width: 256px */
        transition: width 0.3s ease-in-out, transform 0.3s ease-in-out;
    }
    #main-content {
        margin-left: 16rem; /* Default margin to account for open sidebar */
        transition: margin-left 0.3s ease-in-out;
    }
    #sidebar.collapsed {
        width: 4rem; /* Collapsed width: 64px */
    }
    #sidebar.collapsed ~ #main-content {
        margin-left: 4rem;
    }
    #sidebar.collapsed .flex.items-center {
        justify-content: center;
    }
    #sidebar.collapsed a.flex.items-center {
        justify-content: center;
    }
    #sidebar.collapsed button.flex.items-center {
        justify-content: center;
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
        #sidebar.collapsed {
            width: 0;
            transform: translateX(-100%);
        }
        #sidebar.collapsed ~ #main-content {
            margin-left: 0;
        }
        #sidebar.mobile-open .sidebar-text {
            display: block;
        }
    }
</style>
<body class="font-sans bg-[#121212] overflow-x-hidden">
    <div x-data="{ sidebarOpen: true }" 
         x-init="() => {
             Alpine.store('sidebarOpen', true);
             $watch('$store.sidebarOpen', value => {
                 sidebarOpen = value;
                 const sidebar = document.getElementById('sidebar');
                 sidebar.classList.toggle('collapsed', !value);
                 if (window.innerWidth < 768) {
                     sidebar.classList.toggle('mobile-open', value);
                 }
             });
         }" class="flex flex-col md:flex-row min-h-screen">
        <!-- Sidebar -->
        <div id="sidebar" 
            class="fixed inset-y-0 left-0 z-30 bg-gray-900 text-white overflow-y-auto transition-all duration-300 ease-in-out"
            :class="{'translate-x-0': $store.sidebarOpen, '-translate-x-full': !$store.sidebarOpen, 'md:translate-x-0': true}">
            @include('components.sidebar')
        </div>

        <!-- Mobile overlay -->
        <div id="sidebar-overlay" 
            class="fixed inset-0 bg-black opacity-50 z-20 md:hidden transition-opacity duration-300 ease-in-out"
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
    // Add event listener for window resize to handle sidebar state
    window.addEventListener('resize', function() {
        if (window.innerWidth >= 768) {
            Alpine.store('sidebarOpen', true);
        }
    });
</script>
</body>
</html>