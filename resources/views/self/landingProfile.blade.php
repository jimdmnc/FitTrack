<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FitTrack - Gym Management System</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="icon" type="image/png" sizes="180x180" href="{{ asset('images/rockiesLogo.png') }}">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Montserrat:wght@600;700;800&display=swap');
        
        body {
            font-family: 'Montserrat', sans-serif;
        }

        /* Parallax section styles */
        .parallax-section {
            position: relative;
            height: 100vh;
            overflow: hidden;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .parallax-bg {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-image: url('{{ asset('images/welcomebgg.jpg') }}');
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
            z-index: -1;
        }

        /* Mobile optimization - disable fixed background on small screens */
        @media (max-width: 768px) {
            .parallax-bg {
                background-attachment: scroll;
            }
        }

        /* Custom content animation */
        .fade-in-up {
            animation: fadeInUp 1s ease-out forwards;
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Existing styles */
        .hero-section {
            background-image: url('{{ asset('images/welcomebg.jpg') }}');
            background-size: cover;
            background-position: center;
        }

        .in-here-section {
            background-image: url('{{ asset('images/welcomebgg.jpg') }}');
            background-size: cover;
            background-position: center;
        }

        .carousel-item {
            display: none;
        }
        
        .carousel-item.active {
            display: block;
        }
        
        .nav-link {
            position: relative;
        }
        
        .nav-link::after {
            content: '';
            position: absolute;
            bottom: -5px;
            left: 0;
            width: 0;
            height: 2px;
            background-color: #FF0000;
            transition: width 0.3s ease;
        }
        
        .nav-link:hover::after {
            width: 100%;
        }

        @keyframes slideFromRight {
            0% { transform: translateX(100%); opacity: 0; }
            100% { transform: translateX(0); opacity: 1; }
        }
    
        @keyframes slideFromLeft {
            0% { transform: translateX(-100%); opacity: 0; }
            100% { transform: translateX(0); opacity: 1; }
        }
    
        .slide-from-right {
            animation: slideFromRight 1.5s ease-out forwards;
        }
    
        .slide-from-left {
            animation: slideFromLeft 1.5s ease-out forwards;
        }
    
        .hide-images img {
            opacity: 0;
        }

        .workout-timer {
            background-color: rgba(0, 0, 0, 0.7);
            border-radius: 0.5rem;
            padding: 0.5rem 1rem;
            font-weight: bold;
            display: flex;
            align-items: center;
            margin-right: 1rem;
            border: 1px solid #ff5722;
        }
        
        .timer-icon {
            margin-right: 0.5rem;
            color: #FF5722;
        }
        
        .timer-text {
            color: white;
        }
        
        @keyframes pulse {
            0% { opacity: 1; }
            50% { opacity: 0.5; }
            100% { opacity: 1; }
        }
        
        .timer-active {
            animation: pulse 2s infinite;
        }

        /* Enhanced Profile Modal Styles */
        .profile-modal {
            transition: opacity 0.3s ease, visibility 0.3s ease;
        }

        .profile-modal-content {
            transform: translateX(100%);
            transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            box-shadow: -4px 0 15px rgba(0, 0, 0, 0.5);
            width: 380px;
        }

        .profile-modal.active .profile-modal-content {
            transform: translateX(0);
        }

        .profile-header {
            padding: 1.5rem;
        }

        .profile-avatar {
            width: 80px;
            height: 80px;
            border: 3px solid rgba(255, 255, 255, 0.2);
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.3);
            background-color: #2c2c2c;
        }

        .profile-info-item {
            border-bottom: 1px solid #2c2c2c;
            padding: 1rem;
            transition: background-color 0.2s ease;
        }

        .profile-info-item:hover {
            background-color: #252525;
        }

        @media (max-width: 640px) {
            .profile-modal-content {
                width: 100%;
            }
        }

        .announcement-card {
            position: relative;
            z-index: 1;
        }

        .announcement-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(135deg, rgba(255,255,255,0.03) 0%, rgba(255,255,255,0) 100%);
            z-index: -1;
            border-radius: inherit;
        }

        .announcement-card:hover::before {
            background: linear-gradient(135deg, rgba(255,255,255,0.05) 0%, rgba(255,255,255,0.01) 100%);
        }

        .announcements-grid .announcement-card {
            animation: fadeInUp 0.6s ease forwards;
            opacity: 0;
        }

        .announcements-grid .announcement-card:nth-child(1) { animation-delay: 0.1s; }
        .announcements-grid .announcement-card:nth-child(2) { animation-delay: 0.3s; }
        .announcements-grid .announcement-card:nth-child(3) { animation-delay: 0.5s; }
    </style>
</head>
<body data-timed-out="{{ session('timed_out') ? 'true' : 'false' }}" class="bg-gray-100">
    <!-- Navigation Bar -->
    <nav class="bg-black text-gray-200 py-3 px-4 md:px-6 sticky top-0 z-50">
        <div class="container mx-auto">
            <!-- Alerts for Success and Error messages -->
            @if(session('success'))
                <div class="alert-banner success-alert mb-2 p-3 bg-green-100 border-l-4 border-green-500 text-green-700 rounded">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                        </svg>
                        <span>{{ session('success') }}</span>
                    </div>
                </div>
            @endif

            @if(session('error'))
                <div class="alert-banner error-alert mb-2 p-3 bg-red-100 border-l-4 border-red-500 text-red-700 rounded">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                        </svg>
                        <span>{{ session('error') }}</span>
                    </div>
                </div>
            @endif

            <!-- Main Navigation Content -->
            <div class="flex justify-between items-center">
                <!-- Logo Image -->
                <div class="flex items-center">
                    <a href="{{ route('self.landing') }}" aria-label="FitTrack Homepage">
                        <img src="{{ asset('images/image.png') }}" alt="FitTrack Logo" class="h-10 w-10 sm:h-12 sm:w-12 md:h-16 md:w-16 rounded-full object-cover" loading="lazy">
                    </a>
                </div>
                @if(Auth::user()->role === 'userSession')
                    <!-- Workout Timer (Desktop) -->
                    @if(auth()->check() && auth()->user()->rfid_uid && isset($attendance) && !$attendance->time_out && !session('timed_out'))
                        <div class="workout-timer flex items-center bg-gray-800 px-3 py-1 rounded-full">
                            <i class="fas fa-stopwatch mr-2 text-red-400"></i>
                            <span class="timer-text text-sm md:text-base" id="workout-duration">00:00:00</span>
                        </div>
                    @endif
                    <!-- Time Out Button (Desktop and Mobile) -->
                    @if(!session('timed_out') && isset($attendance) && !$attendance->time_out)
                        <!-- Desktop Timeout Button -->
                        <button
                            id="timeout-button"
                            onclick="document.getElementById('timeout-modal').showModal()"
                            class="hidden md:inline-flex bg-red-600 text-gray-200 hover:bg-red-700 font-bold py-2 px-6 rounded-lg shadow-md transition duration-300 min-h-[44px]"
                        >
                            <i class="fas fa-sign-out-alt mr-2"></i> Time Out
                        </button>

                        <!-- Mobile Timeout Button -->
                        <button
                            onclick="document.getElementById('timeout-modal').showModal()"
                            class="inline-flex md:hidden items-center justify-center bg-red-600 hover:bg-red-700 text-white font-medium p-2 rounded-full text-sm transition duration-300 min-h-[44px] min-w-[44px]"
                        >
                            <i class="fas fa-sign-out-alt"></i>
                        </button>
                    @endif
                @endif

                <!-- Desktop Navigation Links -->
                <div class="hidden md:flex items-center space-x-4 lg:space-x-6">
                    <a href="{{ route('self.landingProfile') }}#home" class="nav-link font-medium hover:text-red-400 transition duration-300 text-sm lg:text-base">Home</a>
                    <a href="{{ route('self.landingProfile') }}#inhere" class="nav-link font-medium hover:text-red-400 transition duration-300 text-sm lg:text-base">In Here</a>
                    <a href="javascript:void(0)" onclick="showProfile()" class="nav-link font-medium hover:text-red-400 transition duration-300 text-sm lg:text-base">Profile</a>
                    
                    <!-- Action Buttons -->
                    <div class="flex items-center space-x-2">
                    @if(Auth::user()->role === 'userSession')

                        <button type="button" onclick="checkRenewalEligibility()"
                            class="bg-green-600 hover:bg-green-700 text-white font-medium py-2 px-3 rounded-full text-sm flex items-center transition duration-300 min-h-[44px]">
                            <i class="fas fa-sync-alt mr-1"></i> Renew
                        </button>
                        @endif

                        <form method="POST" action="{{ route('logout.custom') }}">
                            @csrf
                            <button type="submit"
                                class="bg-gray-700 hover:bg-gray-800 text-white font-medium py-2 px-3 rounded-full text-sm flex items-center transition duration-300 min-h-[44px]">
                                <i class="fas fa-door-open mr-1"></i> Sign Out
                            </button>
                        </form>
                    </div>
                </div>

                <!-- Mobile Menu Button -->
                <div class="md:hidden flex items-center space-x-3">
                    <button id="mobile-menu-button" class="text-gray-200 p-1 focus:outline-none bg-gray-800 rounded-md min-h-[44px] min-w-[44px]" aria-label="Toggle mobile menu" aria-expanded="false">
                        <i class="fas fa-bars text-xl"></i>
                    </button>
                </div>
            </div>

            <!-- Mobile Menu -->
            <div id="mobile-menu" class="md:hidden hidden fixed inset-0 bg-black bg-opacity-95 z-50 flex flex-col">
                <div class="container mx-auto px-4 py-8 flex flex-col h-full">
                    <div class="flex justify-end mb-6">
                        <button id="close-mobile-menu" class="text-gray-300 hover:text-white min-h-[44px] min-w-[44px]" aria-label="Close mobile menu">
                            <i class="fas fa-times text-2xl"></i>
                        </button>
                    </div>
                    
                    <div class="flex flex-col space-y-6 text-center flex-grow">
                        <a href="{{ route('self.landingProfile') }}#home" class="py-3 text-xl font-medium hover:text-red-400 transition duration-300">Home</a>
                        <a href="{{ route('self.landingProfile') }}#inhere" class="py-3 text-xl font-medium hover:text-red-400 transition duration-300">About Us</a>
                        <a href="javascript:void(0)" onclick="showProfile(); closeMobileMenu();" class="py-3 text-xl font-medium hover:text-red-400 transition duration-300">Profile</a>
                        
                        @if(Auth::user()->role === 'userSession')
                            @if(auth()->check() && auth()->user()->rfid_uid && isset($attendance) && !$attendance->time_out && !session('timed_out'))
                                <div class="flex justify-center items-center py-4">
                                    <div class="flex items-center bg-gray-800 px-4 py-2 rounded-lg">
                                        <i class="fas fa-stopwatch mr-3 text-red-400 text-lg"></i>
                                        <span id="mobile-workout-duration" class="text-lg font-medium">
                                            @if(isset($attendance))
                                                {{ gmdate('H:i:s', strtotime(now()) - strtotime($attendance->time_in)) }}
                                            @else
                                                00:00:00
                                            @endif
                                        </span>
                                    </div>
                                </div>
                            @endif
                        @endif
                    </div>
                    
                    <div class="grid grid-cols-2 gap-4 mt-6">
                        @if(Auth::user()->role === 'userSession')
                            <button type="button" onclick="checkRenewalEligibility(); closeMobileMenu();"
                                class="bg-green-600 hover:bg-green-700 text-white font-medium py-3 px-4 rounded-lg flex items-center justify-center transition duration-300 min-h-[44px]">
                                <i class="fas fa-sync-alt mr-2"></i> Renew
                            </button>
                        @endif
                        <form method="POST" action="{{ route('logout.custom') }}" class="w-full">
                            @csrf
                            <button type="submit"
                                class="w-full bg-gray-700 hover:bg-gray-800 text-white font-medium py-3 px-4 rounded-lg flex items-center justify-center transition duration-300 min-h-[44px]">
                                <i class="fas fa-door-open mr-2"></i> Sign Out
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </nav>
        
        <!-- Success Alert Modal -->
        @if(session('success'))
            <div class="fixed inset-0 flex items-center justify-center z-50 animate-fade-in" id="successAlert">
                <div class="absolute inset-0 bg-black bg-opacity-50" onclick="document.getElementById('successAlert').classList.add('hidden')"></div>
                <div class="bg-white rounded-lg shadow-xl w-full max-w-md mx-4 overflow-hidden transform transition-all animate-slide-up">
                    <div class="bg-green-500 h-2"></div>
                    <div class="p-5">
                        <div class="flex items-start space-x-4">
                            <div class="flex-shrink-0 bg-green-100 rounded-full p-2">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                            </div>
                            <div class="flex-1 pt-0.5">
                                <h3 class="text-lg font-medium text-gray-900">Success!</h3>
                                <p class="mt-1 text-gray-600">{{ session('success') }}</p>
                            </div>
                            <button type="button" class="text-gray-400 hover:text-gray-500" onclick="document.getElementById('successAlert').classList.add('hidden')">
                                <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>
                    </div>
                    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                        <button type="button" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-green-600 text-base font-medium text-gray-200 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 sm:ml-3 sm:w-auto sm:text-sm" onclick="document.getElementById('successAlert').classList.add('hidden')">
                            Got it
                        </button>
                    </div>
                </div>
            </div>
            <script>
                setTimeout(function() {
                    const alert = document.getElementById('successAlert');
                    if (alert) {
                        alert.classList.add('hidden');
                    }
                }, 5000);
            </script>
        @endif

        <!-- Error Alert Modal -->
        @if(session('error'))
            <div class="fixed inset-0 flex items-center justify-center z-50 animate-fade-in" id="errorAlert">
                <div class="absolute inset-0 bg-black bg-opacity-50" onclick="document.getElementById('errorAlert').classList.add('hidden')"></div>
                <div class="bg-white rounded-lg shadow-xl w-full max-w-md mx-4 overflow-hidden transform transition-all animate-slide-up">
                    <div class="bg-red-500 h-2"></div>
                    <div class="p-5">
                        <div class="flex items-start space-x-4">
                            <div class="flex-shrink-0 bg-red-100 rounded-full p-2">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                </svg>
                            </div>
                            <div class="flex-1 pt-0.5">
                                <h3 class="text-lg font-medium text-gray-900">Error</h3>
                                <p class="mt-1 text-gray-600">{{ session('error') }}</p>
                            </div>
                            <button type="button" class="text-gray-400 hover:text-gray-500" onclick="document.getElementById('errorAlert').classList.add('hidden')">
                                <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>
                    </div>
                    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                        <button type="button" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-gray-200 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:ml-3 sm:w-auto sm:text-sm" onclick="document.getElementById('errorAlert').classList.add('hidden')">
                            Close
                        </button>
                    </div>
                </div>
            </div>
            <script>
                setTimeout(function() {
                    const alert = document.getElementById('errorAlert');
                    if (alert) {
                        alert.classList.add('hidden');
                    }
                }, 5000);
            </script>
        @endif   

        @if(Auth::user()->role === 'userSession')
            <!-- Time Out Confirmation Modal -->
            <dialog id="timeout-modal" class="backdrop:bg-black backdrop:bg-opacity-50 bg-white rounded-lg p-6 max-w-md w-full">
                <div class="text-center">
                    <h3 class="text-xl font-bold mb-4">Confirm Time Out</h3>
                    <p class="mb-6">Are you sure you want to time out?</p>
                    <div class="flex justify-center gap-4">
                        @auth
                        <form id="timeout-form" action="{{ route('attendance.timeout') }}" method="POST">
                            @csrf
                            <input type="hidden" name="rfid_uid" value="{{ auth()->user()->rfid_uid }}">
                            <button type="submit" id="timeout-submit-btn" class="bg-red-600 text-gray-200 hover:bg-red-700 font-bold py-2 px-6 rounded-lg shadow-md transition duration-300">
                                <i class="fas fa-sign-out-alt mr-2"></i> Time Out
                            </button>
                        </form>
                        @endauth
                        <button onclick="document.getElementById('timeout-modal').close()" class="bg-gray-300 text-gray-700 hover:bg-gray-400 font-bold py-2 px-6 rounded-lg shadow-md transition duration-300">
                            Cancel
                        </button>
                    </div>
                </div>
            </dialog>
        @endif

        <!-- Hero Section with Announcements -->
        <section id="home" class="relative w-full h-screen overflow-hidden">
            <div class="absolute inset-0 bg-cover bg-center bg-no-repeat" 
                style="background-image: url('{{ asset('images/image1.png') }}'); transform: translateZ(0);" 
                id="parallax-bg">
            </div>
            <div class="absolute inset-0 bg-gradient-to-b from-black to-gray-900 opacity-90"></div>
            <div class="relative h-full flex items-center">
                <div class="container mx-auto px-6 z-10">
                    <div class="flex flex-col items-center">
                        <div class="text-center max-w-2xl mb-12">
                            <h1 class="text-4xl md:text-5xl lg:text-6xl font-extrabold text-gray-200 mb-6">
                                WELCOME, <span class="text-red-400">{{ Auth::user()->first_name }}!</span>
                            </h1>
                            <p class="text-sm md:text-xl text-gray-300 mb-8">
                                Stay updated with the latest announcements from Rockies Fitness
                            </p>
                        </div>
                        <!-- Temporary debugging -->
                        @if(isset($announcements))
                            <p class="text-gray-300 text-center mb-4">Debug: {{ $announcements->count() }} announcements found</p>
                        @else
                            <p class="text-gray-300 text-center mb-4">Debug: $announcements is undefined</p>
                        @endif
                        <div class="announcements-grid grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 max-w-5xl mx-auto">
                            @if(isset($announcements) && $announcements->isNotEmpty())
                                @foreach($announcements as $announcement)
                                    <div class="announcement-card bg-gray-800 bg-opacity-80 p-6 rounded-lg shadow-lg hover:shadow-xl transition-shadow duration-300">
                                        <h3 class="text-xl font-bold text-white mb-3">{{ $announcement->title }}</h3>
                                        <p class="text-gray-300 text-sm mb-4">{{ $announcement->content }}</p>
                                        <div class="flex justify-between items-center">
                                            <span class="text-xs text-gray-400">
                                                {{ \Carbon\Carbon::parse($announcement->schedule)->format('M d, Y H:i') }}
                                            </span>
                                            <span class="text-xs font-semibold px-2 py-1 rounded-full 
                                                {{ $announcement->type === 'Update' ? 'bg-blue-600' : ($announcement->type === 'Maintenance' ? 'bg-orange-600' : 'bg-green-600') }}">
                                                {{ $announcement->type }}
                                            </span>
                                        </div>
                                    </div>
                                @endforeach
                            @else
                                <p class="text-gray-300 text-center col-span-full">No announcements available at the moment.</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            <div class="absolute bottom-8 left-1/2 transform -translate-x-1/2 animate-bounce">
                <a href="#promotional">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-gray-200" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3" />
                    </svg>
                </a>
            </div>
        </section>


        <!-- Promotional Carousel -->
        <section class="py-16 bg-gray-900 text-gray-200" id="promotional">
            <div class="container mx-auto px-6">
                <h2 class="text-3xl font-bold text-center mb-12">TRANSFORMATION STORIES</h2>
                <div class="w-full max-w-5xl mx-auto mb-8">
                    <div class="relative w-full overflow-hidden rounded-lg shadow-xl">
                        <div id="gym-carousel" class="flex transition-transform duration-700 ease-in-out h-64 sm:h-80 md:h-96">
                            <div class="min-w-full relative">
                                <div class="absolute inset-0 flex">
                                    <div class="w-1/2 h-full overflow-hidden relative">
                                        <img src="{{ asset('images/before12.jpg') }}" class="absolute inset-0 w-full h-full object-cover" alt="Before transformation">
                                        <div class="absolute bottom-0 left-0 bg-black bg-opacity-70 text-gray-200 px-2 py-1 text-sm md:px-4 md:py-2 md:text-base">
                                            <span class="font-bold">BEFORE</span>
                                        </div>
                                    </div>
                                    <div class="w-1/2 h-full overflow-hidden relative">
                                        <img src="{{ asset('images/after12.jpg') }}" class="absolute inset-0 w-full h-full object-cover" alt="After transformation">
                                        <div class="absolute bottom-0 right-0 bg-orange-600 bg-opacity-90 text-gray-200 px-2 py-1 text-sm md:px-4 md:py-2 md:text-base">
                                            <span class="font-bold">AFTER</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="absolute bottom-0 left-0 right-0 bg-gradient-to-t from-black to-transparent p-3">
                                    <p class="text-gray-200 text-center font-semibold text-sm md:text-base">From chubby to muscular, Arloyd transformed through dedication and hard work, now confident and strong.</p>
                                </div>
                            </div>
                            <div class="min-w-full relative">
                                <div class="absolute inset-0 flex">
                                    <div class="w-1/2 h-full overflow-hidden relative">
                                        <img src="{{ asset('images/before22.jpg') }}" class="absolute inset-0 w-full h-full object-cover" alt="Before transformation">
                                        <div class="absolute bottom-0 left-0 bg-black bg-opacity-70 text-gray-200 px-2 py-1 text-sm md:px-4 md:py-2 md:text-base">
                                            <span class="font-bold">BEFORE</span>
                                        </div>
                                    </div>
                                    <div class="w-1/2 h-full overflow-hidden relative">
                                        <img src="{{ asset('images/after22.jpg') }}" class="absolute inset-0 w-full h-full object-cover" alt="After transformation">
                                        <div class="absolute bottom-0 right-0 bg-orange-600 bg-opacity-90 text-gray-200 px-2 py-1 text-sm md:px-4 md:py-2 md:text-base">
                                            <span class="font-bold">AFTER</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="absolute bottom-0 left-0 right-0 bg-gradient-to-t from-black to-transparent p-3">
                                    <p class="text-gray-200 text-center font-semibold text-sm md:text-base">Charles gained strength and defined muscle in just 8 months</p>
                                </div>
                            </div>
                            <div class="min-w-full relative">
                                <img src="{{ asset('images/welcomebggg.jpg') }}" class="absolute inset-0 w-full h-full object-cover" alt="State-of-the-art gym equipment">
                                <div class="absolute bottom-0 left-0 right-0 bg-gradient-to-t from-black to-transparent p-3">
                                    <p class="text-gray-200 text-center font-semibold text-sm md:text-base">Our state-of-the-art weight training section</p>
                                </div>
                            </div>
                            <div class="min-w-full relative">
                                <img src="{{ asset('images/cardio1.png') }}" class="absolute inset-0 w-full h-full object-cover" alt="Modern cardio equipment">
                                <div class="absolute bottom-0 left-0 right-0 bg-gradient-to-t from-black to-transparent p-3">
                                    <p class="text-gray-200 text-center font-semibold text-sm md:text-base">Spacious cardio area with premium equipment</p>
                                </div>
                            </div>
                            <div class="min-w-full relative">
                                <img src="{{ asset('images/healhtyenvirment.jpg') }}" class="absolute inset-0 w-full h-full object-cover" alt="Healthy environment with good people around">
                                <div class="absolute bottom-0 left-0 right-0 bg-gradient-to-t from-black to-transparent p-3">
                                    <p class="text-gray-200 text-center font-semibold text-sm md:text-base">Surround yourself with good people and embrace a healthy environment</p>
                                </div>
                            </div>
                        </div>
                        <button class="absolute left-1 top-1/2 transform -translate-y-1/2 bg-black bg-opacity-50 hover:bg-opacity-75 text-gray-200 p-1 md:p-2 rounded-full focus:outline-none transition" id="prev-btn">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 md:h-6 md:w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                            </svg>
                        </button>
                        <button class="absolute right-1 top-1/2 transform -translate-y-1/2 bg-black bg-opacity-50 hover:bg-opacity-75 text-gray-200 p-1 md:p-2 rounded-full focus:outline-none transition" id="next-btn">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 md:h-6 md:w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                            </svg>
                        </button>
                    </div>
                </div>
                <div class="flex justify-center mt-8 space-x-2">
                    <button class="w-2 h-2 md:w-3 md:h-3 rounded-full bg-white opacity-50 hover:opacity-100 focus:opacity-100 transition-opacity duration-300 carousel-dot active" data-index="0" aria-label="Slide 1"></button>
                    <button class="w-2 h-2 md:w-3 md:h-3 rounded-full bg-white opacity-50 hover:opacity-100 focus:opacity-100 transition-opacity duration-300 carousel-dot" data-index="1" aria-label="Slide 2"></button>
                    <button class="w-2 h-2 md:w-3 md:h-3 rounded-full bg-white opacity-50 hover:opacity-100 focus:opacity-100 transition-opacity duration-300 carousel-dot" data-index="2" aria-label="Slide 3"></button>
                    <button class="w-2 h-2 md:w-3 md:h-3 rounded-full bg-white opacity-50 hover:opacity-100 focus:opacity-100 transition-opacity duration-300 carousel-dot" data-index="3" aria-label="Slide 4"></button>
                    <button class="w-2 h-2 md:w-3 md:h-3 rounded-full bg-white opacity-50 hover:opacity-100 focus:opacity-100 transition-opacity duration-300 carousel-dot" data-index="4" aria-label="Slide 5"></button>
                </div>
            </div>
        </section>

   

        <!-- In Here Section -->
        <section id="inhere" class="in-here-section h-screen flex items-center justify-center relative" style="background-image: url('{{ asset('images/welcomebgg.jpg') }}'); background-size: cover; background-position: center;">
            <div class="absolute inset-0 bg-black bg-opacity-60"></div>
            <div class="container mx-auto px-6 z-10 text-center">
                <h2 class="text-5xl font-extrabold text-gray-200 mb-6">WELCOME TO THE GYM HUB</h2>
                <p class="text-xl text-gray-200 mb-8">Your fitness journey starts here. Access exclusive workouts, track your progress, and connect with our community.</p>
            </div>
        </section>

        <!-- Footer -->
        <footer class="bg-black text-gray-200 py-12">
            <div class="container mx-auto px-6">
                <div class="flex flex-col md:flex-row justify-between mb-8">
                    <div class="mb-8 md:mb-0">
                        <h3 class="text-2xl font-bold mb-4">FitTrack</h3>
                        <p class="text-gray-400 max-w-md">The ultimate gym management system to help you achieve your fitness goals faster and smarter.</p>
                    </div>
                    <div class="grid grid-cols-2 md:grid-cols-3 gap-8">
                        <div>
                            <h4 class="text-lg font-bold mb-4">Quick Links</h4>
                            <ul class="space-y-2">
                                <li><a href="#home" class="text-gray-400 hover:text-red-500 transition duration-300">Home</a></li>
                                <li><a href="#inhere" class="text-gray-400 hover:text-red-500 transition duration-300">In Here</a></li>
                            </ul>
                        </div>
                        <div>
                            <h4 class="text-lg font-bold mb-4">Support</h4>
                            <ul class="space-y-2">
                                <li><a href="#" class="text-gray-400 hover:text-red-500 transition duration-300">Help Center</a></li>
                                <li><a href="#" class="text-gray-400 hover:text-red-500 transition duration-300">Contact Us</a></li>
                                <li><a href="#" class="text-gray-400 hover:text-red-500 transition duration-300">FAQ</a></li>
                            </ul>
                        </div>
                        <div>
                            <h4 class="text-lg font-bold mb-4">Connect</h4>
                            <div class="flex space-x-4">
                                <a href="https://www.facebook.com/rockies.fitness" class="text-gray-400 hover:text-red-500 transition duration-300"><i class="fab fa-facebook-f text-xl"></i></a>
                                <a href="#" class="text-gray-400 hover:text-red-500 transition duration-300"><i class="fab fa-instagram text-xl"></i></a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="border-t border-gray-800 pt-8 text-center text-gray-500">
                    <p>© 2025 FitTrack Gym Management System. All rights reserved.</p>
                </div>
            </div>
        </footer>

        <!-- Profile Modal -->
        <div id="profile-modal" class="profile-modal fixed inset-0 z-50 hidden">
            <div class="absolute inset-0 bg-black bg-opacity-70" onclick="hideProfile()"></div>
            <div class="profile-modal-content absolute right-0 top-0 h-full bg-[#1e1e1e] text-gray-200">
                <div class="profile-header bg-red-600">
                    <div class="flex justify-between items-center mb-6">
                        <h3 class="text-2xl font-bold text-white">User Profile</h3>
                        <button onclick="hideProfile()" class="text-white hover:text-gray-300 transition-colors">
                            <i class="fas fa-times text-xl"></i>
                        </button>
                    </div>
                    <div class="text-center">
                        <img src="{{ asset('images/image.png') }}" alt="User Avatar" class="w-20 h-20 rounded-full mx-auto mb-4 profile-avatar">
                        <h2 class="text-xl font-semibold mt-4 text-white">{{ Auth::user()->first_name }} {{ Auth::user()->last_name }}</h2>
                        <p class="text-sm text-gray-300">{{ Auth::user()->email }}</p>
                    </div>
                </div>
                <div class="overflow-y-auto h-[calc(100vh-200px)] bg-[#1e1e1e]">
                    <div class="space-y-2 p-4">
                        <div class="profile-info-item">
                            <label class="block text-sm text-gray-400 mb-1">Phone Number</label>
                            <p class="font-medium text-gray-200">{{ Auth::user()->phone_number ?? 'Not provided' }}</p>
                        </div>
                        <div class="profile-info-item">
                            <label class="block text-sm text-gray-400 mb-1">Gender</label>
                            <p class="font-medium text-gray-200">{{ Auth::user()->gender ?? 'Not specified' }}</p>
                        </div>
                        <div class="profile-info-item">
                            <label class="block text-sm text-gray-400 mb-1">Member Since</label>
                            <p class="font-medium text-gray-200">{{ Auth::user()->created_at ? Auth::user()->created_at->format('M d, Y') : 'N/A' }}</p>
                        </div>
                        <div class="profile-info-item">
                            <label class="block text-sm text-gray-400 mb-1">Last Activity</label>
                            <p class="font-medium text-gray-200">{{ Auth::user()->last_login_at ? Auth::user()->last_login_at->diffForHumans() : 'N/A' }}</p>
                        </div>
                        <div class="profile-info-item">
                        <label class="block text-sm text-gray-400 mb-1">Issued Date</label>
            <p class="font-medium text-gray-200">
                @if(Auth::user()->start_date)
                    {{ Auth::user()->start_date->format('M d, Y') }}
                @else
                    Not specified
                @endif
            </p>
                        </div>
                        <div class="profile-info-item">
                            <label class="block text-sm text-gray-400 mb-1">Membership Status</label>
                            <p class="font-medium {{ Auth::user()->session_status === 'approved' ? 'text-green-600' : 'text-red-600' }}">
                                {{ ucfirst(Auth::user()->session_status) }}
                                @if(Auth::user()->session_status === 'approved' && Auth::user()->end_date)
                                    (Expires {{ \Carbon\Carbon::parse(Auth::user()->end_date)->format('M d, Y') }})
                                @elseif(Auth::user()->session_status === 'rejected' && Auth::user()->rejection_reason)
                                    - {{ Auth::user()->rejection_reason }}
                                @endif
                            </p>
                        </div>
                        @if(Auth::user()->session_status === 'pending')
                            <div class="profile-info-item">
                                <a href="{{ route('self.waiting') }}" class="text-blue-600 hover:text-blue-800">View Approval Status</a>
                            </div>
                        @elseif(in_array(Auth::user()->session_status, ['expired', 'rejected']))
                            <div class="profile-info-item">
                                <button onclick="checkRenewalEligibility()" class="bg-green-600 hover:bg-green-700 text-white font-medium py-2 px-4 rounded-lg w-full">
                                    Renew Membership
                                </button>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Session Renewal Modal -->
        @if(Auth::user()->role === 'userSession')
            <div id="renewModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-70 hidden">
                <div class="bg-[#1e1e1e] p-6 sm:p-8 rounded-lg shadow-xl w-full max-w-md transform transition-all border border-gray-700">
                    <div class="mb-6 text-center">
                        <h2 class="text-2xl font-bold text-white" style="font-family: 'Bebas Neue', sans-serif;">Session Renewal</h2>
                        <p class="text-gray-400 mt-1">Confirm your session membership details</p>
                    </div>
                    <div class="border-b border-gray-700 mb-6"></div>
                    <form id="renewForm" method="POST" action="{{ route('self.membership.renew') }}">
                        @csrf
                        <input type="hidden" name="rfid_uid" id="rfid_uid" value="{{ auth()->user()->rfid_uid }}">
                        <input type="hidden" name="membership_type" id="membership_type" value="session">
                        <input type="hidden" name="start_date" id="start_date" value="2025-05-18">
                        <input type="hidden" name="end_date" id="end_date" value="2025-05-18">
                        <input type="hidden" name="amount" id="amount" value="{{ $sessionPrice->amount ?? '0' }}">

                        <!-- Summary -->
                        <div class="bg-[#2a2a2a] p-5 rounded-lg mb-6">
                            <div class="space-y-3">
                                <div class="flex items-center">
                                    <span class="w-1/3 text-gray-400 text-sm">RFID UID</span>
                                    <span class="w-2/3 font-medium text-white">{{ auth()->user()->rfid_uid }}</span>
                                </div>
                                <div class="flex items-center">
                                    <span class="w-1/3 text-gray-400 text-sm">Type</span>
                                    <span id="summary_type" class="w-2/3 font-medium text-white">Session</span>
                                </div>
                                <div class="flex items-center">
                                    <span class="w-1/3 text-gray-400 text-sm">Period</span>
                                    <span id="summary_period" class="w-2/3 font-medium text-white">May 18, 2025</span>
                                </div>
                                <div class="flex items-center">
                                    <span class="w-1/3 text-gray-400 text-sm">Amount</span>
                                    <span id="summary_amount" class="w-2/3 font-medium text-white text-lg">₱{{ number_format($sessionPrice->amount ?? 0, 2) }}</span>
                                </div>
                            </div>
                            <!-- Validation Errors -->
                            <div id="form-errors" class="text-red-500 text-sm mt-2 hidden"></div>
                        </div>

                        <!-- Buttons -->
                        <div class="flex space-x-4">
                            <button type="button" onclick="closeRenewModal()" class="w-1/2 py-3 bg-gray-700 hover:bg-gray-600 text-white font-medium rounded-lg transition duration-200">
                                Cancel
                            </button>
                            <button type="submit" id="confirm_button" class="w-1/2 py-3 bg-orange-600 hover:bg-orange-700 text-white font-medium rounded-lg transition duration-200 flex items-center justify-center" {{ !$sessionPrice ? 'disabled' : '' }}>
                                Confirm Payment
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        @endif

        <script>
            document.addEventListener('DOMContentLoaded', function() {
                initNavigation();
                initProfile();
                initParallaxEffect();
                initCarousel();
                initSessionHandling();
                initPhoneAnimation();
                initTimeoutHandling();
                initModals();
                initRenewModal();
                initAttendanceCheck();
                @if(auth()->check() && auth()->user()->rfid_uid && isset($attendance) && !$attendance->time_out && !session('timed_out'))
                    initWorkoutTimer();
                @endif
            });

            function initRenewModal() {
                const renewForm = document.getElementById('renewForm');
                const confirmButton = document.getElementById('confirm_button');

                if (renewForm) {
                    renewForm.addEventListener('submit', async function(e) {
                        e.preventDefault();
                        confirmButton.disabled = true;
                        confirmButton.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> Processing...';

                        const formData = new FormData(renewForm);
                        
                        try {
                            const response = await fetch(renewForm.action, {
                                method: 'POST',
                                body: formData,
                                headers: {
                                    'X-Requested-With': 'XMLHttpRequest',
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                                }
                            });
                            
                            if (!response.ok) {
                                const data = await response.json();
                                throw new Error(data.message || 'Failed to renew membership');
                            }
                            
                            const data = await response.json();

                            if (data.success) {
                                closeRenewModal();
                                showNotification('Success', 'Membership renewed successfully!', 'success');
                                setTimeout(() => window.location.href = data.redirect || window.location.href, 2000);
                            } else {
                                throw new Error(data.message || 'Renewal failed');
                            }
                        } catch (error) {
                            console.error('Error renewing membership:', error);
                            showNotification('Error', error.message || 'Failed to renew membership. Please try again.', 'error');
                        } finally {
                            confirmButton.disabled = false;
                            confirmButton.innerHTML = 'Confirm Payment';
                        }
                    });
                }
            }

            function initAttendanceCheck() {
                let retryCount = 0;
                const maxRetries = 3;

                function checkAttendanceStatus() {
                    fetch('{{ route('self.checkAttendanceStatus') }}', {
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        }
                    })
                    .then(response => {
                        if (!response.ok) {
                            throw new Error('Network response was not ok');
                        }
                        return response.json();
                    })
                    .then(data => {
                        retryCount = 0; // Reset retry count on success
                        if (data.session_status === 'pending') {
                            window.location.href = '{{ route('self.waiting') }}';
                            return;
                        }
                        if (data.timedOut || (data.attendance && data.attendance.time_out)) {
                            if (typeof stopWorkoutTimer === 'function') stopWorkoutTimer();
                            const timeoutButtons = document.querySelectorAll('#timeout-button, [onclick*="timeout-modal"]');
                            timeoutButtons.forEach(button => {
                                if (button) button.style.display = 'none';
                            });
                            const timerElements = [
                                document.getElementById('workout-duration')?.parentElement,
                                document.getElementById('mobile-workout-duration')?.parentElement?.parentElement
                            ].filter(el => el);
                            timerElements.forEach(el => {
                                el.style.display = 'none';
                            });
                            document.body.dataset.timedOut = 'true';
                            const statusElement = document.querySelector('.profile-info-item:nth-last-child(2) p');
                            if (statusElement) {
                                statusElement.className = 'text-red-600 font-medium';
                                statusElement.textContent = 'Checked out';
                            }
                        }
                    })
                    .catch(error => {
                        console.error('Error checking attendance status:', error);
                        if (retryCount < maxRetries) {
                            retryCount++;
                            setTimeout(checkAttendanceStatus, 5000 * retryCount);
                        } else {
                            showNotification('Error', 'Failed to check attendance status. Please refresh the page.', 'error');
                        }
                    });
                }

                setInterval(checkAttendanceStatus, 60000);
                checkAttendanceStatus();
            }

            function initNavigation() {
                const mobileMenuButton = document.getElementById('mobile-menu-button');
                const mobileMenu = document.getElementById('mobile-menu');
                const closeMobileMenuButton = document.getElementById('close-mobile-menu');

                if (mobileMenuButton && mobileMenu) {
                    mobileMenuButton.addEventListener('click', () => {
                        mobileMenu.classList.remove('hidden');
                        mobileMenuButton.setAttribute('aria-expanded', 'true');
                    });
                }

                if (closeMobileMenuButton && mobileMenu) {
                    closeMobileMenuButton.addEventListener('click', () => {
                        closeMobileMenu();
                    });
                }

                mobileMenu?.querySelectorAll('a').forEach(link => {
                    link.addEventListener('click', () => {
                        closeMobileMenu();
                    });
                });
            }

            function closeMobileMenu() {
                const mobileMenu = document.getElementById('mobile-menu');
                const mobileMenuButton = document.getElementById('mobile-menu-button');
                if (mobileMenu) {
                    mobileMenu.classList.add('hidden');
                    if (mobileMenuButton) {
                        mobileMenuButton.setAttribute('aria-expanded', 'false');
                    }
                }
            }

            function initProfile() {
                const profileModal = document.getElementById('profile-modal');
                if (!profileModal) return;

                window.showProfile = function() {
                    profileModal.classList.remove('hidden');
                    setTimeout(() => {
                        profileModal.classList.add('active');
                        profileModal.classList.remove('opacity-0', 'invisible');
                    }, 10);
                };

                window.hideProfile = function() {
                    profileModal.classList.remove('active');
                    profileModal.classList.add('opacity-0', 'invisible');
                    setTimeout(() => {
                        profileModal.classList.add('hidden');
                    }, 300);
                };
            }

            function initParallaxEffect() {
                const parallaxBg = document.getElementById('parallax-bg');
                if (parallaxBg) {
                    const isMobile = 'ontouchstart' in window;
                    const parallaxSpeed = isMobile ? 0.2 : 0.5;
                    window.addEventListener('scroll', function() {
                        const scrolled = window.pageYOffset;
                        parallaxBg.style.transform = `translateY(${scrolled * parallaxSpeed}px) translateZ(0)`;
                    });
                }
            }

            function initCarousel() {
                const carousel = document.getElementById('gym-carousel');
                if (!carousel) return;
                const slides = carousel.children;
                const dots = document.querySelectorAll('.carousel-dot');
                const prevBtn = document.getElementById('prev-btn');
                const nextBtn = document.getElementById('next-btn');
                let currentIndex = 0;
                let intervalId;

                updateCarousel();
                startAutoSlide();

                if (prevBtn) {
                    prevBtn.addEventListener('click', function() {
                        currentIndex = (currentIndex - 1 + slides.length) % slides.length;
                        updateCarousel();
                        resetAutoSlide();
                    });
                }

                if (nextBtn) {
                    nextBtn.addEventListener('click', function() {
                        nextSlide();
                        resetAutoSlide();
                    });
                }

                dots.forEach((dot, index) => {
                    dot.addEventListener('click', function() {
                        currentIndex = parseInt(this.getAttribute('data-index') || index);
                        updateCarousel();
                        resetAutoSlide();
                    });
                });

                carousel.addEventListener('mouseenter', function() {
                    clearInterval(intervalId);
                });

                carousel.addEventListener('mouseleave', function() {
                    startAutoSlide();
                });

                function nextSlide() {
                    currentIndex = (currentIndex + 1) % slides.length;
                    updateCarousel();
                }

                function updateCarousel() {
                    carousel.style.transform = `translateX(-${currentIndex * 100}%)`;
                    dots.forEach((dot, index) => {
                        if (index === currentIndex) {
                            dot.classList.add('active', 'opacity-100');
                            dot.classList.remove('opacity-50');
                        } else {
                            dot.classList.remove('active', 'opacity-100');
                            dot.classList.add('opacity-50');
                        }
                    });
                }

                function startAutoSlide() {
                    intervalId = setInterval(nextSlide, 5000);
                }

                function resetAutoSlide() {
                    clearInterval(intervalId);
                    startAutoSlide();
                }
            }

            function initSessionHandling() {
                const timeoutButton = document.getElementById('timeout-button');
                if (timeoutButton) {
                    timeoutButton.addEventListener('click', function() {
                        document.getElementById('timeout-modal').showModal();
                    });
                }
            }

            function initTimeoutHandling() {
                const timeoutForm = document.getElementById('timeout-form');
                const timeoutSubmitBtn = document.getElementById('timeout-submit-btn');
                const timeoutModal = document.getElementById('timeout-modal');

                if (!timeoutForm) return;

                timeoutForm.addEventListener('submit', function(e) {
                    e.preventDefault();
                    if (timeoutSubmitBtn) {
                        timeoutSubmitBtn.disabled = true;
                        timeoutSubmitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> Processing...';
                    }

                    const formData = new FormData(this);
                    fetch(this.action, {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        }
                    })
                    .then(response => {
                        if (!response.ok) throw new Error('Network response was not ok');
                        return response.json();
                    })
                    .then(data => {
                        if (data.success) {
                            if (timeoutModal) timeoutModal.close();
                            if (typeof stopWorkoutTimer === 'function') stopWorkoutTimer();
                            const timeoutButtons = document.querySelectorAll('#timeout-button, [onclick*="timeout-modal"]');
                            timeoutButtons.forEach(button => {
                                if (button) button.style.display = 'none';
                            });
                            const timerElements = [
                                document.getElementById('workout-duration')?.parentElement,
                                document.getElementById('mobile-workout-duration')?.parentElement?.parentElement
                            ].filter(el => el);
                            timerElements.forEach(el => {
                                el.style.display = 'none';
                            });
                            showNotification('Success', 'You have successfully timed out.', 'success');
                            setTimeout(() => {
                                window.location.reload();
                            }, 2000);
                        } else {
                            showNotification('Error', data.message || 'Failed to time out. Please try again.', 'error');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        showNotification('Error', 'An error occurred while processing your request.', 'error');
                    })
                    .finally(() => {
                        if (timeoutSubmitBtn) {
                            timeoutSubmitBtn.disabled = false;
                            timeoutSubmitBtn.innerHTML = '<i class="fas fa-sign-out-alt mr-2"></i> Time Out';
                        }
                    });
                });
            }

            function initWorkoutTimer() {
                const timerElement = document.getElementById('workout-duration');
                const mobileTimerElement = document.getElementById('mobile-workout-duration');
                if (!timerElement && !mobileTimerElement) return;

                let startTime = @json(isset($attendance) && $attendance ? $attendance->time_in : null);
                startTime = startTime ? new Date(startTime).getTime() : null;
                let intervalId;

                function updateTimer() {
                    if (!startTime) {
                        if (timerElement) timerElement.textContent = '00:00:00';
                        if (mobileTimerElement) mobileTimerElement.textContent = '00:00:00';
                        return;
                    }

                    const now = new Date().getTime();
                    const distance = Math.floor((now - startTime) / 1000);
                    const hours = Math.floor(distance / 3600);
                    const minutes = Math.floor((distance % 3600) / 60);
                    const seconds = Math.floor(distance % 60);
                    const formattedTime = `${String(hours).padStart(2, '0')}:${String(minutes).padStart(2, '0')}:${String(seconds).padStart(2, '0')}`;

                    if (timerElement) timerElement.textContent = formattedTime;
                    if (mobileTimerElement) mobileTimerElement.textContent = formattedTime;
                }

                updateTimer();
                if (startTime) {
                    intervalId = setInterval(updateTimer, 1000);
                }

                window.stopWorkoutTimer = function() {
                    clearInterval(intervalId);
                };
            }

            function checkRenewalEligibility() {
                // First check if user is timed out
                const isTimedOut = document.body.dataset.timedOut === 'true';
                
                if (isTimedOut) {
                    openRenewModal();
                    return true;
                }
                
                // Check for active attendance
                fetch('/self/check-attendance-status', {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.attendance && !data.attendance.time_out && !data.timedOut) {
                        showNotification('Error', 'You must time out before renewing your membership.', 'error');
                    } else {
                        openRenewModal();
                    }
                })
                .catch(error => {
                    console.error('Error checking attendance:', error);
                    showNotification('Error', 'Unable to check attendance status. Please try again.', 'error');
                });
                
                return false;
            }

            function initPhoneAnimation() {
                const phone1 = document.getElementById('phone1');
                const phone2 = document.getElementById('phone2');
                const container = document.getElementById('phone-container');
                if (!phone1 || !phone2 || !container) return;
                runAnimation();
                window.runAnimation = runAnimation;
            }

            function runAnimation() {
                const phone1 = document.getElementById('phone1');
                const phone2 = document.getElementById('phone2');
                const container = document.getElementById('phone-container');
                if (!phone1 || !phone2 || !container) return;
                container.classList.add('hide-images');
                phone1.classList.remove('slide-from-right');
                phone2.classList.remove('slide-from-left');
                void phone1.offsetWidth;
                setTimeout(() => {
                    container.classList.remove('hide-images');
                    phone1.classList.add('slide-from-right');
                    phone2.classList.add('slide-from-left');
                }, 50);
            }

            function initModals() {
                const renewModal = document.getElementById('renewModal');
                if (!renewModal) return;
                const modalContent = renewModal.querySelector('div');

                renewModal.addEventListener('transitionend', function() {
                    if (renewModal.classList.contains('hidden')) {
                        modalContent.classList.remove('scale-100');
                        modalContent.classList.add('scale-95', 'opacity-0');
                    }
                });

                window.openRenewModal = function() {
                    renewModal.classList.remove('hidden');
                    setTimeout(() => {
                        modalContent.classList.remove('scale-95', 'opacity-0');
                        modalContent.classList.add('scale-100', 'opacity-100');
                    }, 10);
                };

                window.closeRenewModal = function() {
                    modalContent.classList.remove('scale-100', 'opacity-100');
                    modalContent.classList.add('scale-95', 'opacity-0');
                    setTimeout(() => {
                        renewModal.classList.add('hidden');
                    }, 300);
                };
            }

            function showNotification(title, message, type = 'info') {
                let notification = document.getElementById('notification');
                if (!notification) {
                    notification = document.createElement('div');
                    notification.id = 'notification';
                    notification.className = 'fixed top-4 right-4 p-4 rounded-lg shadow-lg z-50 transform transition-all duration-300 translate-x-full';
                    document.body.appendChild(notification);
                }

                const bgColor = type === 'success' ? 'bg-green-500' : 
                               type === 'error' ? 'bg-red-500' : 'bg-blue-500';

                notification.className = `fixed top-4 right-4 p-4 rounded-lg shadow-lg z-50 ${bgColor} text-white transform transition-all duration-300`;

                notification.innerHTML = `
                    <div class="flex items-center">
                        <div class="mr-3">
                            ${type === 'success' ? '<i class="fas fa-check-circle"></i>' : 
                            type === 'error' ? '<i class="fas fa-exclamation-circle"></i>' : 
                            '<i class="fas fa-info-circle"></i>'}
                        </div>
                        <div>
                            <h4 class="font-bold">${title}</h4>
                            <p>${message}</p>
                        </div>
                        <button onclick="this.parentElement.parentElement.classList.add('translate-x-full')" class="ml-4">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                `;

                setTimeout(() => {
                    notification.classList.remove('translate-x-full');
                }, 100);

                setTimeout(() => {
                    notification.classList.add('translate-x-full');
                }, 5000);
            }

            window.showNotification = showNotification;
        </script>
    </body>
</html>