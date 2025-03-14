<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FitTrack - Gym Dashboard</title>
    <meta name="description" content="Gym management dashboard for tracking analytics and attendance">
    <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>

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





    document.addEventListener('DOMContentLoaded', function() {
    const hamburger = document.getElementById('hamburger');
    const line1 = document.getElementById('line1');
    const line2 = document.getElementById('line2');
    const line3 = document.getElementById('line3');
    let isOpen = false;
    
    // Position the lines initially
    line1.style.transform = 'translateY(-8px)';
    line3.style.transform = 'translateY(8px)';
    
    // Add ripple effect on click
    hamburger.addEventListener('click', function(e) {
        // Toggle menu state
        isOpen = !isOpen;
        
        if (isOpen) {
            // Transform to X shape
            line1.style.transform = 'translateY(0) rotate(45deg)';
            line2.style.opacity = '0';
            line3.style.transform = 'translateY(0) rotate(-45deg)';
            hamburger.classList.remove('bg-blue-50');
            hamburger.classList.add('bg-blue-200');
        } else {
            // Return to hamburger shape
            line1.style.transform = 'translateY(-8px) rotate(0)';
            line2.style.opacity = '1';
            line3.style.transform = 'translateY(8px) rotate(0)';
            hamburger.classList.add('bg-blue-50');
            hamburger.classList.remove('bg-blue-200');
        }
        
        // Create ripple effect
        const circle = document.createElement('span');
        const diameter = Math.max(hamburger.clientWidth, hamburger.clientHeight);
        const radius = diameter / 2;
        
        circle.style.width = circle.style.height = `${diameter}px`;
        circle.style.left = `${e.clientX - hamburger.getBoundingClientRect().left - radius}px`;
        circle.style.top = `${e.clientY - hamburger.getBoundingClientRect().top - radius}px`;
        circle.classList.add('ripple');
        
        // Remove existing ripple
        const ripple = hamburger.getElementsByClassName('ripple')[0];
        if (ripple) {
            ripple.remove();
        }
        
        hamburger.appendChild(circle);
        
        // Toggle any menu or functionality here
        // For example: document.getElementById('menu').classList.toggle('hidden');
    });
    
    // Add hover effects
    hamburger.addEventListener('mouseenter', function() {
        if (!isOpen) {
            line1.style.transform = 'translateY(-6px)';
            line3.style.transform = 'translateY(6px)';
        }
    });
    
    hamburger.addEventListener('mouseleave', function() {
        if (!isOpen) {
            line1.style.transform = 'translateY(-8px)';
            line3.style.transform = 'translateY(8px)';
        }
    });
    
    // Add styles for ripple effect
    const style = document.createElement('style');
    style.textContent = `
        .ripple {
            position: absolute;
            background-color: rgba(255, 255, 255, 0.7);
            border-radius: 50%;
            transform: scale(0);
            animation: ripple 0.6s linear;
            pointer-events: none;
        }
        
        @keyframes ripple {
            to {
                transform: scale(2);
                opacity: 0;
            }
        }
    `;
    document.head.appendChild(style);
});
</script>




</body>
</html>