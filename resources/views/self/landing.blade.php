
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FitTrack - Gym Management System</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="icon" type="image/png" sizes="180x180" href="{{ asset('images/rockiesLogo.png') }}">
    
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
            background-image: url('images/welcomebgg.jpg');
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

        /* Existing styles from your site */
        .hero-section {
            background-image: url('/api/placeholder/1920/1080');
            background-size: cover;
            background-position: center;
        }

        .in-here-section {
            background-image: url('/api/placeholder/1920/1080');
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
        0% {
            transform: translateX(100%);
            opacity: 0;
        }
        100% {
            transform: translateX(0);
            opacity: 1;
        }
    }
    
    @keyframes slideFromLeft {
        0% {
            transform: translateX(-100%);
            opacity: 0;
        }
        100% {
            transform: translateX(0);
            opacity: 1;
        }
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
        background: linear-gradient(135deg, #ff5722 0%, #ff7043 100%);
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

    @media (max-width: 640px) {
        .profile-modal-content {
            width: 100%;
        }
    }

    </style>
</head>
<body class="bg-gray-100">
    <!-- Navigation Bar -->
    <nav class="bg-black text-gray-200 py-2 px-6 sticky top-0 z-50">
        <div class="container mx-auto flex justify-between items-center">
            <!-- Logo Image -->
            <div class="flex items-center">
                <img src="images/image.png" alt="FitTrack Logo" class="h-20 w-20 rounded-full">
            </div>
            
            <!-- Navigation Links -->
            <div class="hidden md:flex space-x-8">
                <a href="#home" class="nav-link font-semibold hover:text-red-500 transition duration-300">Home</a>
                <a href="#tutorial" class="nav-link font-semibold hover:text-red-500 transition duration-300">Tutorial</a>
                <a href="#inhere" class="nav-link font-semibold hover:text-red-500 transition duration-300">In Here</a>
                <a href="#" onclick="showProfile()" class="nav-link font-semibold hover:text-red-500 transition duration-300">Profile</a>
            </div>

            <!-- Workout Duration Timer (New Element) -->
            @if(auth()->check() && auth()->user()->rfid_uid && !session('timed_out'))
            <div class="workout-timer" id="workout-timer">
                <i class="fas fa-stopwatch timer-icon timer-active"></i>
                <span class="timer-text" id="workout-duration">00:00:00</span>
            </div>
            @endif

            <!-- Time Out Button -->
            @if(auth()->check() && auth()->user()->rfid_uid && !session('timed_out'))
            <button onclick="document.getElementById('timeout-modal').showModal()" 
            class="bg-red-600 hover:bg-red-700 text-gray-200 font-bold py-2 px-3 text-sm rounded-full transition duration-300 flex items-center"
            id="timeout-button">
                <i class="fas fa-sign-out-alt mr-2"></i> TimeOut
            </button>
            @endif

            <!-- Mobile Menu Button -->
            <div class="md:hidden">
                <button id="mobile-menu-button" class="text-gray-200 focus:outline-none">
                    <i class="fas fa-bars text-2xl"></i>
                </button>
            </div>
        </div>

        <!-- Mobile Menu -->
        <div id="mobile-menu" class="md:hidden hidden bg-gray-900 mt-4 p-4 rounded-lg">
            <a href="#home" class="block py-2 text-center hover:bg-gray-800 rounded">Home</a>
            <a href="#tutorial" class="block py-2 text-center hover:bg-gray-800 rounded">Tutorial</a>
            <a href="#inhere" class="block py-2 text-center hover:bg-gray-800 rounded">In Here</a>
            <a href="#" onclick="showProfile()" class="nav-link font-semibold hover:text-red-500 transition duration-300">Profile</a>
            
            <!-- Mobile Workout Timer Display -->
            @if(auth()->check() && auth()->user()->rfid_uid && !session('timed_out'))
            <div class="flex justify-center items-center py-3 border-t border-gray-700 mt-2">
                <div class="flex items-center">
                    <i class="fas fa-stopwatch mr-2 text-red-500"></i>
                    <span id="mobile-workout-duration">00:00:00</span>
                </div>
            </div>
            @endif
        </div>
    </nav>
        
        <!-- Success Alert Modal -->
            @if(session('success'))
                    <div class="fixed inset-0 flex items-center justify-center z-50 animate-fade-in" id="successAlert">
                        <div class="absolute inset-0 bg-black bg-opacity-50" onclick="document.getElementById('successAlert').classList.add('hidden')"></div>
                        <div class="bg-white rounded-lg shadow-xl w-full max-w-md mx-4 overflow-hidden transform transition-all animate-slide-up">
                            <!-- Header with colored bar -->
                            <div class="bg-green-500 h-2"></div>
                            
                            <div class="p-5">
                                <!-- Success Icon and Message -->
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
                                    <!-- Close Button -->
                                    <button type="button" class="text-gray-400 hover:text-gray-500" onclick="document.getElementById('successAlert').classList.add('hidden')">
                                        <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                        </svg>
                                    </button>
                                </div>
                            </div>
                            
                            <!-- Action Button -->
                            <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                                <button type="button" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-green-600 text-base font-medium text-gray-200 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 sm:ml-3 sm:w-auto sm:text-sm" onclick="document.getElementById('successAlert').classList.add('hidden')">
                                    Got it
                                </button>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Auto-dismiss after 5 seconds -->
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
                        <!-- Header with colored bar -->
                        <div class="bg-red-500 h-2"></div>
                        
                        <div class="p-5">
                            <!-- Error Icon and Message -->
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
                                <!-- Close Button -->
                                <button type="button" class="text-gray-400 hover:text-gray-500" onclick="document.getElementById('errorAlert').classList.add('hidden')">
                                    <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </button>
                            </div>
                        </div>
                        
                        <!-- Action Button -->
                        <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                            <button type="button" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-gray-200 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:ml-3 sm:w-auto sm:text-sm" onclick="document.getElementById('errorAlert').classList.add('hidden')">
                                Close
                            </button>
                        </div>
                    </div>
                </div>
                
                <!-- Auto-dismiss after 5 seconds -->
                <script>
                    setTimeout(function() {
                        const alert = document.getElementById('errorAlert');
                        if (alert) {
                            alert.classList.add('hidden');
                        }
                    }, 5000);
                </script>
            @endif   
        <!-- Time Out Confirmation Modal -->
        <dialog id="timeout-modal" class="backdrop:bg-black backdrop:bg-opacity-50 bg-white rounded-lg p-6 max-w-md w-full">
            <div class="text-center">
                <h3 class="text-xl font-bold mb-4">Confirm Time Out</h3>
                <p class="mb-6">Are you sure you want to time out?</p>
                
                <div class="flex justify-center gap-4">
                    @auth
                        <form action="{{ url('/attendance/timeout') }}" method="POST">
                            @csrf
                            <input type="hidden" name="rfid_uid" value="{{ auth()->user()->rfid_uid }}">
                            <button type="submit" 
                                    class="bg-red-600 text-gray-200 hover:bg-red-700 font-bold py-2 px-6 rounded-lg shadow-md transition duration-300">
                                Yes, Time Out
                            </button>
                        </form>
                    @endauth
                    <button onclick="document.getElementById('timeout-modal').close()" 
                            class="bg-gray-300 text-gray-700 hover:bg-gray-400 font-bold py-2 px-6 rounded-lg shadow-md transition duration-300">
                        Cancel
                    </button>
                </div>
            </div>
        </dialog>

        
        <!-- Hero Section with Parallax Effect -->
        <section id="home" class="relative w-full h-screen overflow-hidden">
            <!-- Fixed Background Layer - This creates the parallax effect -->
            <div class="absolute inset-0 bg-cover bg-center bg-no-repeat" 
                style="background-image: url('{{ asset('images/image1.png') }}'); transform: translateZ(0);" 
                id="parallax-bg">
            </div>
            
            <!-- Gradient Overlay -->
            <div class="absolute inset-0 bg-gradient-to-b from-black to-gray-900 opacity-90"></div>
            
            <!-- Content Layer that scrolls normally -->
            <div class="relative h-full flex items-center">
                <div class="container mx-auto px-6 z-10">
                    <div class="flex flex-col items-center">
                        <!-- Hero Text Content -->
                        <div class="text-center max-w-2xl mb-12">
                            <!-- Heading -->
                            <h1 class="text-4xl md:text-5xl lg:text-6xl font-extrabold text-gray-200 mb-2">
                                WELCOME TO <span class="text-gray-200">ROCKIES <span class="gradient-text bg-gradient-to-r from-red-600 to-orange-600 bg-clip-text text-transparent">FITNESS </span></span>
                            </h1>
                            
                            <!-- Subtitle -->
                            <p class="text-sm md:text-2xl text-gray-300 mb-8">
                                Track your workouts, stay consistent, and achieve your fitness goals — all in one place.
                            </p>

                            <!-- App Store Buttons -->
                            <div class="flex flex-wrap justify-center gap-4 mb-6">
                                <a href="#tutorial" class="bg-red-600 hover:bg-red-700 text-gray-200 font-bold py-3 px-6 rounded-lg inline-flex items-center text-xs md:text-base transition duration-300 shadow-lg hover:scale-105 transform">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                                    </svg>
                                    Get Started
                                </a>
                                
                                <!-- Google Play Store Button -->
                                <a href="https://play.google.com/store/apps/details?id=com.FitTrack.fittrackapp&hl=en" 
                                class="bg-white hover:bg-gray-800 hover:text-gray-200 text-black font-bold py-3 px-6 rounded-lg inline-flex items-center text-xs md:text-base transition duration-300 shadow-lg hover:scale-105 transform"
                                target="_blank" rel="noopener noreferrer">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 512 512">
                                        <path fill="currentColor" d="M325.3 234.3L104.6 13l280.8 161.2-60.1 60.1zM47 0C34 6.8 25.3 19.2 25.3 35.3v441.3c0 16.1 8.7 28.5 21.7 35.3l256.6-256L47 0zm425.6 225.6l-58.9-34.1-65.7 64.5 65.7 64.5 60.1-34.1c18-14.3 18-46.5-1.2-60.8zM104.6 499l280.8-161.2-60.1-60.1L104.6 499z"/>
                                    </svg>
                                    Download App
                                </a>
                            </div>
                        </div>
                        
                        <!-- Two Phone Image Mockups -->
                        <div class="flex flex-row flex-wrap justify-center items-center hide-images" id="phone-container">
                            <img src="images/phone12.png" alt="Phone Mockup 1" class="w-40 md:w-50 transition-transform duration-500 hover:scale-105" id="phone2">
                            <img src="images/phone12.png" alt="Phone Mockup 2" class="w-40 md:w-50 transition-transform duration-500 hover:scale-105" id="phone1">
                        </div>

                    </div>
                </div>
            </div>
            
            <!-- Scroll Indicator -->
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
                
                    <!-- Bottom Content: Carousel -->
                    <div class="w-full max-w-5xl mx-auto mb-8">
                    <!-- Image Carousel Showcase -->
                    <div class="relative w-full overflow-hidden rounded-lg shadow-xl">
                        <!-- Main Carousel Container -->
                        <div id="gym-carousel" class="flex transition-transform duration-700 ease-in-out h-64 sm:h-80 md:h-96">
                            <!-- Before/After Transformation 1 -->
                            <div class="min-w-full relative">
                                <div class="absolute inset-0 flex">
                                    <!-- Before Image (Left Half) -->
                                    <div class="w-1/2 h-full overflow-hidden relative">
                                        <img src="/images/before12.jpg" class="absolute inset-0 w-full h-full object-cover" alt="Before transformation">
                                        <div class="absolute bottom-0 left-0 bg-black bg-opacity-70 text-gray-200 px-2 py-1 text-sm md:px-4 md:py-2 md:text-base">
                                            <span class="font-bold">BEFORE</span>
                                        </div>
                                    </div>
                                    <!-- After Image (Right Half) -->
                                    <div class="w-1/2 h-full overflow-hidden relative">
                                        <img src="/images/after12.jpg" class="absolute inset-0 w-full h-full object-cover" alt="After transformation">
                                        <div class="absolute bottom-0 right-0 bg-orange-600 bg-opacity-90 text-gray-200 px-2 py-1 text-sm md:px-4 md:py-2 md:text-base">
                                            <span class="font-bold">AFTER</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="absolute bottom-0 left-0 right-0 bg-gradient-to-t from-black to-transparent p-3">
                                    <p class="text-gray-200 text-center font-semibold text-sm md:text-base">From chubby to muscular, Daniel transformed through dedication and hard work, now confident and strong.
            </p>
                                </div>
                            </div>

                            <!-- Before/After Transformation 2 -->
                            <div class="min-w-full relative">
                                <div class="absolute inset-0 flex">
                                    <div class="w-1/2 h-full overflow-hidden relative">
                                        <img src="/images/before22.jpg" class="absolute inset-0 w-full h-full object-cover" alt="Before transformation">
                                        <div class="absolute bottom-0 left-0 bg-black bg-opacity-70 text-gray-200 px-2 py-1 text-sm md:px-4 md:py-2 md:text-base">
                                            <span class="font-bold">BEFORE</span>
                                        </div>
                                    </div>
                                    <div class="w-1/2 h-full overflow-hidden relative">
                                        <img src="/images/after22.jpg" class="absolute inset-0 w-full h-full object-cover" alt="After transformation">
                                        <div class="absolute bottom-0 right-0 bg-orange-600 bg-opacity-90 text-gray-200 px-2 py-1 text-sm md:px-4 md:py-2 md:text-base">
                                            <span class="font-bold">AFTER</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="absolute bottom-0 left-0 right-0 bg-gradient-to-t from-black to-transparent p-3">
                                    <p class="text-gray-200 text-center font-semibold text-sm md:text-base">Dan gained strength and defined muscle in just 8 months</p>
                                </div>
                            </div>

                            <!-- Gym Facility Image 1 -->
                            <div class="min-w-full relative">
                                <img src="/images/welcomebggg.jpg" class="absolute inset-0 w-full h-full object-cover" alt="State-of-the-art gym equipment">
                                <div class="absolute bottom-0 left-0 right-0 bg-gradient-to-t from-black to-transparent p-3">
                                    <p class="text-gray-200 text-center font-semibold text-sm md:text-base">Our state-of-the-art weight training section</p>
                                </div>
                            </div>

                            <!-- Gym Facility Image 2 -->
                            <div class="min-w-full relative">
                                <img src="/images/cardio1.png" class="absolute inset-0 w-full h-full object-cover" alt="Modern cardio equipment">
                                <div class="absolute bottom-0 left-0 right-0 bg-gradient-to-t from-black to-transparent p-3">
                                    <p class="text-gray-200 text-center font-semibold text-sm md:text-base">Spacious cardio area with premium equipment</p>
                                </div>
                            </div>

                            <!-- Healthy Environment with Good People Around -->
                            <div class="min-w-full relative">
                                <img src="/images/environment.png" class="absolute inset-0 w-full h-full object-cover" alt="Healthy environment with good people around">
                                <div class="absolute bottom-0 left-0 right-0 bg-gradient-to-t from-black to-transparent p-3">
                                    <p class="text-gray-200 text-center font-semibold text-sm md:text-base">Surround yourself with good people and embrace a healthy environment</p>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Navigation Controls -->
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
                        
                        <!-- Navigation Dots -->
                
                    </div>
                </div>
                
                <!-- Carousel Navigation -->
                <div class="flex justify-center mt-8 space-x-2">
                    <button class="w-2 h-2 md:w-3 md:h-3 rounded-full bg-white opacity-50 hover:opacity-100 focus:opacity-100 transition-opacity duration-300 carousel-dot active" data-index="0" aria-label="Slide 1"></button>
                    <button class="w-2 h-2 md:w-3 md:h-3 rounded-full bg-white opacity-50 hover:opacity-100 focus:opacity-100 transition-opacity duration-300 carousel-dot" data-index="1" aria-label="Slide 2"></button>
                    <button class="w-2 h-2 md:w-3 md:h-3 rounded-full bg-white opacity-50 hover:opacity-100 focus:opacity-100 transition-opacity duration-300 carousel-dot" data-index="2" aria-label="Slide 3"></button>
                    <button class="w-2 h-2 md:w-3 md:h-3 rounded-full bg-white opacity-50 hover:opacity-100 focus:opacity-100 transition-opacity duration-300 carousel-dot" data-index="3" aria-label="Slide 4"></button>
                    <button class="w-2 h-2 md:w-3 md:h-3 rounded-full bg-white opacity-50 hover:opacity-100 focus:opacity-100 transition-opacity duration-300 carousel-dot" data-index="4" aria-label="Slide 5"></button>
                </div>
        </section>

        <!-- Tutorial Section -->
        <section id="tutorial" class="py-16 bg-white">
            <div class="container mx-auto px-6">
                <h2 class="text-3xl font-bold text-center mb-12">HOW TO REGISTER FOR GYM SESSIONS</h2>
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                    <!-- Step 1 -->
                    <div class="bg-gray-100 p-8 rounded-lg shadow-lg text-center">
                        <div class="inline-block bg-red-600 text-gray-200 text-2xl font-bold w-12 h-12 rounded-full flex items-center justify-center mb-4">1</div>
                        <h3 class="text-xl font-bold mb-4">VISIT THE WEBSITE & FILL THE FORM</h3>
                        <p class="text-gray-700 mb-4">Go to the website, fill out the registration form, and submit it.</p>
                        <a href="{{ route('self.registration') }}" class="text-blue-600 hover:text-blue-800">Click here to register</a>
                        <img src="/images/welcomebg.jpg" alt="Visit Website" class="rounded-lg mx-auto mt-4">
                    </div>
                    
                    <!-- Step 2 -->
                    <div class="bg-gray-100 p-8 rounded-lg shadow-lg text-center">
                        <div class="inline-block bg-red-600 text-gray-200 text-2xl font-bold w-12 h-12 rounded-full flex items-center justify-center mb-4">2</div>
                        <h3 class="text-xl font-bold mb-4">GO TO THE GYM FOR PAYMENT & APPROVAL</h3>
                        <p class="text-gray-700 mb-4">Head to the gym for payment and approval by the staff. Once approved, the system will time in your visit.</p>
                        <img src="/images/welcomebgg.jpg" alt="Gym Payment" class="rounded-lg mx-auto">
                    </div>
                    
                    <!-- Step 3 -->
                    <div class="bg-gray-100 p-8 rounded-lg shadow-lg text-center">
                        <div class="inline-block bg-red-600 text-gray-200 text-2xl font-bold w-12 h-12 rounded-full flex items-center justify-center mb-4">3</div>
                        <h3 class="text-xl font-bold mb-4">ENJOY YOUR SESSION & TIME OUT</h3>
                        <p class="text-gray-700 mb-4">Enjoy your gym session. Once done, click the "Time Out" button to record your departure in the gym management system.</p>
                        <img src="/images/welcomebg.jpg" alt="Time Out" class="rounded-lg mx-auto">
                    </div>
                </div>
                

            </div>
        </section>


        <!-- In Here Section -->
        <section id="inhere" class="in-here-section h-screen flex items-center justify-center relative" style="background-image: url('images/welcomebgg.jpg'); background-size: cover; background-position: center;">
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
                                <li><a href="#" class="text-gray-400 hover:text-red-500 transition duration-300">Home</a></li>
                                <li><a href="#" class="text-gray-400 hover:text-red-500 transition duration-300">Tutorial</a></li>
                                <li><a href="#" class="text-gray-400 hover:text-red-500 transition duration-300">In Here</a></li>
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
                    <p>&copy; 2025 FitTrack Gym Management System. All rights reserved.</p>
                </div>
            </div>
        </footer>

<!-- Enhanced Profile Modal -->
<div id="profile-modal" class="profile-modal fixed inset-0 z-50 hidden">
    <div class="absolute inset-0 bg-black bg-opacity-70" onclick="hideProfile()"></div>
    <div class="profile-modal-content absolute right-0 top-0 h-full bg-[#1e1e1e] text-gray-200">
        <div class="profile-header">
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-2xl font-bold text-white">User Profile</h3>
                <button onclick="hideProfile()" class="text-white hover:text-gray-300 transition-colors">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
            
            <div class="text-center">
                
                <h2 class="text-xl font-semibold mt-4 text-white">
                    {{ Auth::user()->first_name . ' ' . Auth::user()->last_name ?? 'Guest User' }}
                </h2>
                <p class="text-sm text-gray-300">{{ Auth::user()->email ?? '' }}</p>
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
                    <p class="font-medium text-gray-200">
                        {{ Auth::user()->created_at ? Auth::user()->created_at->format('M d, Y') : 'N/A' }}
                    </p>
                </div>
                
                <div class="profile-info-item">
                    <label class="block text-sm text-gray-400 mb-1">Last Activity</label>
                    <p class="font-medium text-gray-200">
                        {{ Auth::user()->last_login_at ? Auth::user()->last_login_at->diffForHumans() : 'N/A' }}
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Update the JavaScript section -->
<script>
    function toggleProfile() {
        const modal = document.getElementById('profile-modal');
        modal.classList.toggle('opacity-0');
        modal.classList.toggle('invisible');
        modal.classList.toggle('active');
    }

    // Enhanced Mobile Menu Toggle
    function toggleMobileMenu() {
        const menu = document.getElementById('mobile-menu');
        menu.classList.toggle('hidden');
        menu.classList.toggle('animate-slideDown');
    }

    // Update DOMContentLoaded event listener
    document.addEventListener('DOMContentLoaded', function() {
        // Existing initializations...

        // Mobile menu button
        document.getElementById('mobile-menu-button').addEventListener('click', toggleMobileMenu);

        // Profile links in mobile menu
        document.querySelectorAll('[onclick="toggleProfile()"]').forEach(link => {
            link.addEventListener('click', () => {
                toggleProfile();
                toggleMobileMenu();
            });
        });
    });

    // Add smooth scroll behavior
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function(e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                target.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        });
    });
</script>

    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
<!-- Add this script at the end of your body tag -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Parallax Background Effect
            initParallaxEffect();
            
            // Mobile Menu Functionality
            initMobileMenu();
            
            // Carousel Functionality
            initCarousel();
            
            // Smooth scroll for navigation links
            initSmoothScroll();
            
            // Timeout Modal Functionality
            initTimeoutModal();

            // Initialize workout timer if user is logged in
            initWorkoutTimer()
        });

        // Workout Timer Functionality
        function initWorkoutTimer() {
            // Check if user is logged in and has an active session
            @if(auth()->check() && auth()->user()->rfid_uid && !session('timed_out'))
                // Get the user's check-in time from the database
                const checkInTime = new Date("{{ auth()->user()->attendances()->latest()->first()->created_at ?? now() }}");
                const timerElement = document.getElementById('workout-duration');
                const mobileTimerElement = document.getElementById('mobile-workout-duration');
                
                // Update timer every second
                setInterval(function() {
                    const now = new Date();
                    const duration = Math.floor((now - checkInTime) / 1000); // Duration in seconds
                    
                    // Calculate hours, minutes, seconds
                    const hours = Math.floor(duration / 3600);
                    const minutes = Math.floor((duration % 3600) / 60);
                    const seconds = duration % 60;
                    
                    // Format time as HH:MM:SS
                    const formattedTime = 
                        String(hours).padStart(2, '0') + ":" + 
                        String(minutes).padStart(2, '0') + ":" + 
                        String(seconds).padStart(2, '0');
                    
                    // Update timer display
                    if (timerElement) timerElement.textContent = formattedTime;
                    if (mobileTimerElement) mobileTimerElement.textContent = formattedTime;
                    
                    // Highlight duration if it's over 2 hours (optional feature)
                    if (hours >= 2) {
                        timerElement.classList.add('text-red-400');
                        if (mobileTimerElement) mobileTimerElement.classList.add('text-red-400');
                    }
                }, 1000);
            @endif
        }

        // Parallax Background Effect
        function initParallaxEffect() {
            const parallaxBg = document.getElementById('parallax-bg');
            
            if (parallaxBg) {
                // Check if device is mobile
                const isMobile = 'ontouchstart' in window;
                
                // Set the parallax speed based on device type
                const parallaxSpeed = isMobile ? 0.2 : 0.5;
                
                // Add scroll event listener
                window.addEventListener('scroll', function() {
                    // Get the current scroll position
                    const scrolled = window.pageYOffset;
                    
                    // Apply the parallax effect
                    parallaxBg.style.transform = `translateY(${scrolled * parallaxSpeed}px) translateZ(0)`;
                });
            }
        }

        // Mobile Menu Functionality
        function initMobileMenu() {
            const mobileMenuButton = document.getElementById('mobile-menu-button');
            
            if (mobileMenuButton) {
                mobileMenuButton.addEventListener('click', function() {
                    const mobileMenu = document.getElementById('mobile-menu');
                    mobileMenu.classList.toggle('hidden');
                });
            }
        }

        // Carousel Functionality
        function initCarousel() {
            const carousel = document.getElementById('gym-carousel');
            
            if (!carousel) return;
            
            const slides = carousel.children;
            const dots = document.querySelectorAll('.carousel-dot');
            const prevBtn = document.getElementById('prev-btn');
            const nextBtn = document.getElementById('next-btn');
            let currentIndex = 0;
            let intervalId;
            
            // Set initial position
            updateCarousel();
            
            // Auto-scroll every 5 seconds
            startAutoSlide();
            
            // Previous button
            if (prevBtn) {
                prevBtn.addEventListener('click', function() {
                    currentIndex = (currentIndex - 1 + slides.length) % slides.length;
                    updateCarousel();
                    resetAutoSlide();
                });
            }
            
            // Next button
            if (nextBtn) {
                nextBtn.addEventListener('click', function() {
                    nextSlide();
                    resetAutoSlide();
                });
            }
            
            // Dot navigation
            dots.forEach(dot => {
                dot.addEventListener('click', function() {
                    currentIndex = parseInt(this.getAttribute('data-index'));
                    updateCarousel();
                    resetAutoSlide();
                });
            });
            
            // Pause auto-scrolling when hovering over carousel
            carousel.addEventListener('mouseenter', function() {
                clearInterval(intervalId);
            });
            
            // Resume auto-scrolling when mouse leaves carousel
            carousel.addEventListener('mouseleave', function() {
                startAutoSlide();
            });
            
            function nextSlide() {
                currentIndex = (currentIndex + 1) % slides.length;
                updateCarousel();
            }
            
            function updateCarousel() {
                carousel.style.transform = `translateX(-${currentIndex * 100}%)`;
                
                // Update active dot
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

        // Smooth scroll for navigation links
        function initSmoothScroll() {
            document.querySelectorAll('a[href^="#"]').forEach(anchor => {
                anchor.addEventListener('click', function(e) {
                    e.preventDefault();
                    
                    const targetElement = document.querySelector(this.getAttribute('href'));
                    if (targetElement) {
                        targetElement.scrollIntoView({
                            behavior: 'smooth'
                        });
                    }
                    
                    // Close mobile menu if open
                    const mobileMenu = document.getElementById('mobile-menu');
                    if (mobileMenu && !mobileMenu.classList.contains('hidden')) {
                        mobileMenu.classList.add('hidden');
                    }
                });
            });
        }

        // Timeout Modal Functionality
        function initTimeoutModal() {
            // This was originally a PHP/Laravel snippet
            // We convert it to plain JavaScript
            const timeoutButton = document.getElementById('timeout-button');
            const sessionHasTimedOut = false; // This should be set by your app logic
            
            if (sessionHasTimedOut && timeoutButton) {
                timeoutButton.style.display = 'none';
            }
            
            const timeoutForm = document.querySelector('#timeout-modal form');
            if (timeoutForm) {
                timeoutForm.addEventListener('submit', function() {
                    if (timeoutButton) timeoutButton.style.display = 'none';
                });
            }
        }
        

    </script>

<script>
    // Function to trigger the animation
    function runAnimation() {
        const phone1 = document.getElementById('phone1');
        const phone2 = document.getElementById('phone2');
        const container = document.getElementById('phone-container');
        
        // Reset animation state
        container.classList.add('hide-images');
        phone1.classList.remove('slide-from-right');
        phone2.classList.remove('slide-from-left');
        
        // Force browser reflow to ensure animation can run again
        void phone1.offsetWidth;
        
        // Start animation after a brief delay
        setTimeout(() => {
            container.classList.remove('hide-images');
            phone1.classList.add('slide-from-right');
            phone2.classList.add('slide-from-left');
        }, 50);
    }
    
    // Run animation when the page loads
    document.addEventListener('DOMContentLoaded', runAnimation);
</script>
<!-- JavaScript for Profile Modal -->
<script>
    function showProfile() {
        const modal = document.getElementById('profile-modal');
        modal.classList.remove('hidden');
        // Add active class to trigger animation
        setTimeout(() => {
            modal.classList.add('active');
        }, 10); // Small delay to ensure transition works
    }

    function hideProfile() {
        const modal = document.getElementById('profile-modal');
        modal.classList.remove('active');
        // Add a delay before hiding to allow animation to complete
        setTimeout(() => {
            modal.classList.add('hidden');
        }, 300); // Match this with the CSS transition duration
    }

    // Function to toggle the profile modal
    function toggleProfile() {
        const modal = document.getElementById('profile-modal');
        if (modal.classList.contains('hidden')) {
            showProfile();
        } else {
            hideProfile();
        }
    }

    // Add event listener to close modal when clicking outside
    document.addEventListener('DOMContentLoaded', function() {
        const modalOverlay = document.querySelector('#profile-modal .absolute.inset-0');
        if (modalOverlay) {
            modalOverlay.addEventListener('click', hideProfile);
        }
    });
</script>
</body>
</html>





