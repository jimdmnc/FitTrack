<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FitTrack - Gym Dashboard</title>
    <meta name="description" content="Gym management dashboard for tracking analytics and attendance">
    
    <!-- Tailwind CSS -->
    @vite('resources/css/app.css')
    
    <!-- Alpine.js for interactions -->
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    
    <!-- Chart.js for graphs -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body class="font-sans">
    
<div class="flex h-screen min-w-[1024px]">
    <!-- Include Sidebar -->
    @include('components.sidebar')

    <!-- Main Content -->
    <div id="main-content" class="flex-1 transition-all duration-300 lg:ml-64">
        <div class="">
            @include('layouts.navigation')
        </div>
        <div class="px-4 md:px-20 bg-gray-100 h-full">
            @yield('content')
        </div>
    </div>
</div>

    
    <!-- JavaScript to initialize charts -->
    <script>
    document.addEventListener("DOMContentLoaded", function () {
        const sidebar = document.getElementById("sidebar");
        const hamburger = document.getElementById("hamburger");
        const mainContent = document.getElementById("main-content");

        let isSidebarOpen = true; // Default: Sidebar is visible on large screens

        if (sidebar && hamburger && mainContent) {
            hamburger.addEventListener("click", function () {
                sidebar.classList.toggle("-translate-x-full");
                isSidebarOpen = !isSidebarOpen;

                if (isSidebarOpen) {
    sidebar.style.transform = "translateX(0)"; // Slide in smoothly
    mainContent.classList.add("lg:ml-64"); // Shift content
} else {
    sidebar.style.transform = "translateX(-100%)"; // Slide out smoothly
    mainContent.classList.remove("lg:ml-64"); // Expand content
}


            });
        } else {
            console.error("Sidebar, Hamburger button, or Main Content not found!");
        }
    });
</script>




</body>
</html>