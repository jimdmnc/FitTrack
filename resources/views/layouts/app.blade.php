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

    <!-- Custom Favicon -->
    <link rel="icon" type="image/png" sizes="180x180" href="{{ asset('images/rockiesLogo.png') }}">
    

    <!-- Tailwind CSS -->
    @vite('resources/css/app.css')

    <!-- Alpine.js for interactions -->
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <!-- Chart.js for graphs -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

</head>
<body class="font-sans bg-[#121212] overflow-x-hidden" x-data="{ sidebarOpen: window.innerWidth >= 768 }" x-init="() => {
    // Initialize sidebar state based on screen size
    window.addEventListener('resize', () => {
        sidebarOpen = window.innerWidth >= 768;
    });
}">
    
<div class="flex flex-col md:flex-row min-h-screen w-full">
    <!-- Sidebar -->
    <div id="sidebar" 
         class="fixed inset-y-0 left-0 z-30 w-64 bg-gray-900 text-white transition-transform duration-300 ease-in-out overflow-y-auto"
         :class="{'translate-x-0': sidebarOpen, '-translate-x-full': !sidebarOpen}">
        @include('components.sidebar')
    </div>

    <!-- Mobile overlay -->
    <div id="sidebar-overlay" 
         class="fixed inset-0 bg-black opacity-50 z-20 md:hidden transition-opacity duration-300 ease-in-out"
         :class="{'block': sidebarOpen, 'hidden': !sidebarOpen}"
         @click="sidebarOpen = false"></div>

    <!-- Main Content -->
    <div id="main-content" 
         class="w-full transition-all duration-300"
         :class="{'md:ml-64': sidebarOpen, 'ml-0': !sidebarOpen}">
        <div class="sticky top-0 z-10 bg-[#121212]">
            @include('layouts.navigation')
        </div>
        <div class="px-4 md:px-10 pb-20 md:pb-10">
            @yield('content')
        </div>
    </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", function () {
    const line1 = document.getElementById('line1');
    const line2 = document.getElementById('line2');
    const line3 = document.getElementById('line3');
    const hamburger = document.getElementById('hamburger');

    if (line1 && line2 && line3 && hamburger) {
        // Initial line positions
        line1.style.transform = 'translateY(-8px)';
        line3.style.transform = 'translateY(8px)';

        hamburger.addEventListener('click', function (e) {
            // Toggle sidebar state
            const alpineComponent = Alpine.$data(document.body);
            alpineComponent.sidebarOpen = !alpineComponent.sidebarOpen;
            
            // Animate hamburger
            if (alpineComponent.sidebarOpen) {
                line1.style.transform = 'translateY(0) rotate(45deg)';
                line2.style.opacity = '0';
                line3.style.transform = 'translateY(0) rotate(-45deg)';
            } else {
                line1.style.transform = 'translateY(-8px) rotate(0)';
                line2.style.opacity = '1';
                line3.style.transform = 'translateY(8px) rotate(0)';
            }

            // Ripple effect
            const circle = document.createElement('span');
            const diameter = Math.max(hamburger.clientWidth, hamburger.clientHeight);
            const radius = diameter / 2;

            circle.style.width = circle.style.height = `${diameter}px`;
            circle.style.left = `${e.clientX - hamburger.getBoundingClientRect().left - radius}px`;
            circle.style.top = `${e.clientY - hamburger.getBoundingClientRect().top - radius}px`;
            circle.classList.add('ripple');

            const ripple = hamburger.getElementsByClassName('ripple')[0];
            if (ripple) ripple.remove();

            hamburger.appendChild(circle);
        });

        // Hover effects
        hamburger.addEventListener('mouseenter', function () {
            const isOpen = Alpine.$data(document.body).sidebarOpen;
            if (!isOpen) {
                line1.style.transform = 'translateY(-6px)';
                line3.style.transform = 'translateY(6px)';
            }
        });

        hamburger.addEventListener('mouseleave', function () {
            const isOpen = Alpine.$data(document.body).sidebarOpen;
            if (!isOpen) {
                line1.style.transform = 'translateY(-8px)';
                line3.style.transform = 'translateY(8px)';
            }
        });
    }
});
</script>

</body>
</html>
