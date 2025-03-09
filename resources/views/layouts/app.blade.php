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
        <div class="flex flex-col flex-1 w-0 ml-64 ">
            <div class="">
                @include('layouts.navigation')
            </div>
            <div class="px-4 md:px-20 bg-gray-100 h-full">
                <!-- Header and Main Content -->
                @yield('content')
            </div>
        </div>
    </div>
    
    <!-- JavaScript to initialize charts -->

</body>
</html>