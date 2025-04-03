<!-- resources/views/landing.blade.php -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="theme-color" content="#3B82F6">
    <title>App Landing Page</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Add these animations to your CSS file -->
<style>
    .animate-fade-in {
        animation: fadeIn 0.3s ease-in-out;
    }
    
    .animate-slide-up {
        animation: slideUp 0.3s ease-out;
    }
    
    @keyframes fadeIn {
        from { opacity: 0; }
        to { opacity: 1; }
    }
    
    @keyframes slideUp {
        from { transform: translateY(10px); opacity: 0; }
        to { transform: translateY(0); opacity: 1; }
    }
</style>
    <script>
        // Timeout functionality
        document.addEventListener('DOMContentLoaded', function() {
            const timeoutDuration = 60000; // 60 seconds timeout
            const timeoutMessage = document.getElementById('timeout-message');
            const mainContent = document.getElementById('main-content');
            
            setTimeout(function() {
                mainContent.classList.add('opacity-50');
                timeoutMessage.classList.remove('hidden');
            }, timeoutDuration);
            
            // Reset timeout on user interaction
            document.addEventListener('click', resetTimeout);
            document.addEventListener('touchstart', resetTimeout);
            document.addEventListener('keydown', resetTimeout);
            
            function resetTimeout() {
                mainContent.classList.remove('opacity-50');
                timeoutMessage.classList.add('hidden');
                
                // Reset the timeout
                clearTimeout(window.timeoutTimer);
                window.timeoutTimer = setTimeout(function() {
                    mainContent.classList.add('opacity-50');
                    timeoutMessage.classList.remove('hidden');
                }, timeoutDuration);
            }
            
            window.timeoutTimer = setTimeout(function() {}, timeoutDuration);
            
            // Mobile menu toggle
            const menuToggle = document.getElementById('menu-toggle');
            const mobileMenu = document.getElementById('mobile-menu');
            
            menuToggle.addEventListener('click', function() {
                mobileMenu.classList.toggle('hidden');
            });
        });
    </script>
</head>
<body class="bg-gray-50 font-sans text-base">
    <!-- Timeout Message -->
    <div id="timeout-message" class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-70 z-50 hidden">
        <div class="bg-white p-6 m-4 rounded-lg shadow-xl text-center">
            <h2 class="text-xl font-bold text-red-600 mb-3">Session Timeout</h2>
            <p class="mb-4">Your session has timed out due to inactivity.</p>
            <button onclick="location.reload()" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-6 rounded-lg w-full transition duration-300">
                Refresh Page
            </button>
        </div>
    </div>

    <!-- Main Content -->
    <div id="main-content" class="min-h-screen transition-opacity duration-300">
        <!-- Header -->
        <header class="bg-gradient-to-r from-gray-900 to-gray-700 text-white sticky top-0 z-40 shadow-md">
            <div class="container mx-auto px-4 py-3">
                <div class="flex justify-between items-center">
                    <div class="flex items-center">
                        <div class="text-xl font-bold">FitTrack</div>
                    </div>
                    
                    <!-- Mobile menu button -->
                    <button id="menu-toggle" class="md:hidden flex items-center p-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                    </button>
                    
                    <!-- Desktop navigation -->
                    <nav class="hidden md:block">
                        <ul class="flex space-x-6">
                            <li><a href="#features" class="hover:text-blue-200 transition">Features</a></li>
                            <li><a href="#download" class="hover:text-blue-200 transition">Download</a></li>
                            <li><a href="#contact" class="hover:text-blue-200 transition">Contact</a></li>
                        </ul>
                    </nav>
                </div>
                
                <!-- Mobile navigation menu -->
                <div id="mobile-menu" class="md:hidden hidden mt-3 pb-3">
                    <ul class="flex flex-col space-y-3">
                        <li><a href="#features" class="block py-2 hover:bg-blue-700 px-3 rounded">Features</a></li>
                        <li><a href="#download" class="block py-2 hover:bg-blue-700 px-3 rounded">Download</a></li>
                        <li><a href="#contact" class="block py-2 hover:bg-blue-700 px-3 rounded">Contact</a></li>
                    </ul>
                </div>
            </div>
        </header>
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
                        <button type="button" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-green-600 text-base font-medium text-white hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 sm:ml-3 sm:w-auto sm:text-sm" onclick="document.getElementById('successAlert').classList.add('hidden')">
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
                        <button type="button" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:ml-3 sm:w-auto sm:text-sm" onclick="document.getElementById('errorAlert').classList.add('hidden')">
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



<!-- Hero Section -->
<section class="relative min-h-screen bg-cover bg-center text-white" style="background-image: url('/images/image1.png');">
    <!-- Dark Overlay -->
    <div class="absolute inset-0 bg-black bg-opacity-70"></div>
    
    <!-- Main Content Container -->
    <div class="container mx-auto px-4 py-10 flex flex-col justify-between h-full relative">
        <!-- Top Content: Header, Paragraph, Button -->
        <div class="text-center mb-12 mt-40 ">
        <h1 class="text-4xl md:text-4xl font-bold mb-2 bg-clip-text text-transparent bg-gradient-to-r from-orange-500 via-yellow-500 to-red-500">
            Transform Your Life
        </h1>
            <p class="text-base md:text-lg mb-4 max-w-lg mx-auto">
                Join our fitness community for real results.
            </p>
    
            @if(auth()->check() && auth()->user()->rfid_uid)
    <!-- When user is logged in, show both buttons side by side -->
    <div class="flex flex-row justify-center gap-4">
        <a href="#membership" class="inline-block bg-transparent border-2 border-orange-500 text-orange-500 hover:bg-orange-500 hover:text-white font-bold py-3 px-6 rounded-lg text-base shadow-md transition duration-300">
            Get Started
        </a>
        
        <!-- Time Out Button with Modal Trigger -->
        <button onclick="document.getElementById('timeout-modal').showModal()" 
                class="inline-block bg-red-600 text-red-200 hover:bg-red-700 font-bold py-3 px-6 rounded-lg text-base shadow-md transition duration-300">
            Time Out
        </button>
    </div>
    
    <!-- Time Out Confirmation Modal -->
    <dialog id="timeout-modal" class="backdrop:bg-black backdrop:bg-opacity-50 bg-white rounded-lg p-6 max-w-md w-full">
        <div class="text-center">
            <h3 class="text-xl font-bold mb-4">Confirm Time Out</h3>
            <p class="mb-6">Are you sure you want to time out?</p>
            
            <div class="flex justify-center gap-4">
                <form action="{{ url('/attendance/timeout') }}" method="POST">
                    @csrf
                    <input type="hidden" name="rfid_uid" value="{{ auth()->user()->rfid_uid }}">
                    <button type="submit" 
                            class="bg-red-600 text-white hover:bg-red-700 font-bold py-2 px-6 rounded-lg shadow-md transition duration-300">
                        Yes, Time Out
                    </button>
                </form>
                <button onclick="document.getElementById('timeout-modal').close()" 
                        class="bg-gray-300 text-gray-700 hover:bg-gray-400 font-bold py-2 px-6 rounded-lg shadow-md transition duration-300">
                    Cancel
                </button>
            </div>
        </div>
    </dialog>
@else
    <!-- When user is not logged in, show only the wider Get Started button -->
    <div class="flex justify-center">
        <a href="#membership" class="inline-block bg-transparent border-2 border-orange-500 text-orange-500 hover:bg-orange-500 hover:text-white font-bold py-3 px-8 rounded-lg text-base shadow-md transition duration-300">
            Get Started
        </a>
    </div>
@endif
        </div>
        
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
                                <img src="/images/before1.png" class="absolute inset-0 w-full h-full object-cover" alt="Before transformation">
                                <div class="absolute bottom-0 left-0 bg-black bg-opacity-70 text-white px-2 py-1 text-sm md:px-4 md:py-2 md:text-base">
                                    <span class="font-bold">BEFORE</span>
                                </div>
                            </div>
                            <!-- After Image (Right Half) -->
                            <div class="w-1/2 h-full overflow-hidden relative">
                                <img src="/images/after1.png" class="absolute inset-0 w-full h-full object-cover" alt="After transformation">
                                <div class="absolute bottom-0 right-0 bg-orange-600 bg-opacity-90 text-white px-2 py-1 text-sm md:px-4 md:py-2 md:text-base">
                                    <span class="font-bold">AFTER</span>
                                </div>
                            </div>
                        </div>
                        <div class="absolute bottom-0 left-0 right-0 bg-gradient-to-t from-black to-transparent p-3">
                            <p class="text-white text-center font-semibold text-sm md:text-base">John lost 30lbs in 3 months with our personal training program</p>
                        </div>
                    </div>

                    <!-- Before/After Transformation 2 -->
                    <div class="min-w-full relative">
                        <div class="absolute inset-0 flex">
                            <div class="w-1/2 h-full overflow-hidden relative">
                                <img src="/images/before2.png" class="absolute inset-0 w-full h-full object-cover" alt="Before transformation">
                                <div class="absolute bottom-0 left-0 bg-black bg-opacity-70 text-white px-2 py-1 text-sm md:px-4 md:py-2 md:text-base">
                                    <span class="font-bold">BEFORE</span>
                                </div>
                            </div>
                            <div class="w-1/2 h-full overflow-hidden relative">
                                <img src="/images/after2.png" class="absolute inset-0 w-full h-full object-cover" alt="After transformation">
                                <div class="absolute bottom-0 right-0 bg-orange-600 bg-opacity-90 text-white px-2 py-1 text-sm md:px-4 md:py-2 md:text-base">
                                    <span class="font-bold">AFTER</span>
                                </div>
                            </div>
                        </div>
                        <div class="absolute bottom-0 left-0 right-0 bg-gradient-to-t from-black to-transparent p-3">
                            <p class="text-white text-center font-semibold text-sm md:text-base">Sarah gained strength and defined muscle in just 8 weeks</p>
                        </div>
                    </div>

                    <!-- Gym Facility Image 1 -->
                    <div class="min-w-full relative">
                        <img src="/images/welcomebggg.jpg" class="absolute inset-0 w-full h-full object-cover" alt="State-of-the-art gym equipment">
                        <div class="absolute bottom-0 left-0 right-0 bg-gradient-to-t from-black to-transparent p-3">
                            <p class="text-white text-center font-semibold text-sm md:text-base">Our state-of-the-art weight training section</p>
                        </div>
                    </div>

                    <!-- Gym Facility Image 2 -->
                    <div class="min-w-full relative">
                        <img src="/images/cardio1.png" class="absolute inset-0 w-full h-full object-cover" alt="Modern cardio equipment">
                        <div class="absolute bottom-0 left-0 right-0 bg-gradient-to-t from-black to-transparent p-3">
                            <p class="text-white text-center font-semibold text-sm md:text-base">Spacious cardio area with premium equipment</p>
                        </div>
                    </div>

                    <!-- Healthy Environment with Good People Around -->
                    <div class="min-w-full relative">
                        <img src="/images/environment.png" class="absolute inset-0 w-full h-full object-cover" alt="Healthy environment with good people around">
                        <div class="absolute bottom-0 left-0 right-0 bg-gradient-to-t from-black to-transparent p-3">
                            <p class="text-white text-center font-semibold text-sm md:text-base">Surround yourself with good people and embrace a healthy environment</p>
                        </div>
                    </div>
                </div>
                
                <!-- Navigation Controls -->
                <button class="absolute left-1 top-1/2 transform -translate-y-1/2 bg-black bg-opacity-50 hover:bg-opacity-75 text-white p-1 md:p-2 rounded-full focus:outline-none transition" id="prev-btn">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 md:h-6 md:w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                </button>
                <button class="absolute right-1 top-1/2 transform -translate-y-1/2 bg-black bg-opacity-50 hover:bg-opacity-75 text-white p-1 md:p-2 rounded-full focus:outline-none transition" id="next-btn">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 md:h-6 md:w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </button>
                
                <!-- Navigation Dots -->
                <div class="absolute -bottom-1 left-0 right-0 flex justify-center space-x-2 py-3 bg-gradient-to-t from-black to-transparent">
                    <button class="w-2 h-2 md:w-3 md:h-3 rounded-full bg-white opacity-50 hover:opacity-100 focus:opacity-100 transition-opacity duration-300 carousel-dot active" data-index="0" aria-label="Slide 1"></button>
                    <button class="w-2 h-2 md:w-3 md:h-3 rounded-full bg-white opacity-50 hover:opacity-100 focus:opacity-100 transition-opacity duration-300 carousel-dot" data-index="1" aria-label="Slide 2"></button>
                    <button class="w-2 h-2 md:w-3 md:h-3 rounded-full bg-white opacity-50 hover:opacity-100 focus:opacity-100 transition-opacity duration-300 carousel-dot" data-index="2" aria-label="Slide 3"></button>
                    <button class="w-2 h-2 md:w-3 md:h-3 rounded-full bg-white opacity-50 hover:opacity-100 focus:opacity-100 transition-opacity duration-300 carousel-dot" data-index="3" aria-label="Slide 4"></button>
                    <button class="w-2 h-2 md:w-3 md:h-3 rounded-full bg-white opacity-50 hover:opacity-100 focus:opacity-100 transition-opacity duration-300 carousel-dot" data-index="4" aria-label="Slide 5"></button>
                </div>
            </div>
        </div>
    </div>
</section>



        <!-- Features Section -->
        <section id="features" class="py-12 md:py-20 bg-white">
            <div class="container mx-auto px-4">
                <h2 class="text-2xl md:text-3xl font-bold text-center mb-10 md:mb-16 text-gray-800">Amazing Features</h2>
                
                <div class="grid gap-8 md:grid-cols-3">
                    <!-- Feature 1 -->
                    <div class="flex flex-col items-center text-center p-4">
                        <div class="bg-blue-100 p-4 rounded-full mb-4 md:mb-6">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                            </svg>
                        </div>
                        <h3 class="text-lg md:text-xl font-bold mb-2 text-gray-800">Lightning Fast</h3>
                        <p class="text-gray-600">Optimized performance to give you the speed you need.</p>
                    </div>
                    
                    <!-- Feature 2 -->
                    <div class="flex flex-col items-center text-center p-4">
                        <div class="bg-blue-100 p-4 rounded-full mb-4 md:mb-6">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                            </svg>
                        </div>
                        <h3 class="text-lg md:text-xl font-bold mb-2 text-gray-800">Secure & Reliable</h3>
                        <p class="text-gray-600">Your data is protected with the highest security standards.</p>
                    </div>
                    
                    <!-- Feature 3 -->
                    <div class="flex flex-col items-center text-center p-4">
                        <div class="bg-blue-100 p-4 rounded-full mb-4 md:mb-6">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>
                        </div>
                        <h3 class="text-lg md:text-xl font-bold mb-2 text-gray-800">User-Friendly</h3>
                        <p class="text-gray-600">Intuitive interface designed with the user in mind.</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Promotional Image Section -->
        <section class="py-12 bg-gray-100">
            <div class="container mx-auto px-4">
                <div class="bg-white rounded-xl shadow-lg overflow-hidden">
                    <div class="flex flex-col md:flex-row">
                        <div class="w-full md:w-1/2">
                            <img src="/api/placeholder/600/400" alt="Promotional Image" class="w-full h-64 md:h-full object-cover" />
                        </div>
                        <div class="w-full md:w-1/2 p-6 md:p-10 flex flex-col justify-center">
                            <h3 class="text-xl md:text-2xl font-bold mb-3 md:mb-4 text-gray-800">Transform Your Experience</h3>
                            <p class="text-gray-600 mb-6">Our app provides a seamless experience that helps you achieve more in less time. With powerful features and an intuitive interface, you'll wonder how you ever managed without it.</p>
                            <a href="#download" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-6 rounded-lg inline-block transition duration-300 text-center md:self-start">
                                Learn More
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Download Section -->
        <section id="download" class="py-12 md:py-20 bg-blue-600 text-white">
            <div class="container mx-auto px-4 text-center">
                <h2 class="text-2xl md:text-3xl font-bold mb-4 md:mb-8">Download Our App Now</h2>
                <p class="text-lg md:text-xl mb-8 md:mb-10 max-w-2xl mx-auto">Get started with our app today and experience the difference. Available for all major platforms.</p>
                
                <div class="flex justify-center">
                    <a href="#" class="w-full max-w-xs bg-white text-blue-700 hover:bg-blue-100 font-bold py-4 px-8 rounded-lg text-lg shadow-lg transition duration-300 flex items-center justify-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                        </svg>
                        Download App
                    </a>
                </div>
            </div>
        </section>

        <!-- Contact Section -->
        <section id="contact" class="py-12 md:py-20 bg-white">
            <div class="container mx-auto px-4">
                <h2 class="text-2xl md:text-3xl font-bold text-center mb-8 md:mb-12 text-gray-800">Get in Touch</h2>
                
                <div class="max-w-lg mx-auto">
                    <form class="space-y-4 md:space-y-6">
                        <div>
                            <label for="name" class="block text-gray-700 mb-2">Name</label>
                            <input type="text" id="name" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                        
                        <div>
                            <label for="email" class="block text-gray-700 mb-2">Email</label>
                            <input type="email" id="email" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                        
                        <div>
                            <label for="message" class="block text-gray-700 mb-2">Message</label>
                            <textarea id="message" rows="4" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"></textarea>
                        </div>
                        
                        <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-4 px-6 rounded-lg transition duration-300">
                            Send Message
                        </button>
                    </form>
                </div>
            </div>
        </section>

        <!-- Footer -->
        <footer class="bg-gray-800 text-white py-10">
            <div class="container mx-auto px-4">
                <div class="flex flex-col space-y-8 md:space-y-0 md:flex-row md:justify-between">
                    <div class="mb-6 md:mb-0">
                        <h3 class="text-xl font-bold mb-3">FitTrack</h3>
                        <p class="text-gray-400">Transform your digital experience</p>
                    </div>
                    
                    <div class="grid grid-cols-2 gap-8">
                        <div>
                            <h4 class="text-lg font-semibold mb-3">Links</h4>
                            <ul class="space-y-2">
                                <li><a href="#" class="text-gray-400 hover:text-white transition">About</a></li>
                                <li><a href="#" class="text-gray-400 hover:text-white transition">Team</a></li>
                                <li><a href="#" class="text-gray-400 hover:text-white transition">Blog</a></li>
                            </ul>
                        </div>
                        
                        <div>
                            <h4 class="text-lg font-semibold mb-3">Legal</h4>
                            <ul class="space-y-2">
                                <li><a href="#" class="text-gray-400 hover:text-white transition">Privacy</a></li>
                                <li><a href="#" class="text-gray-400 hover:text-white transition">Terms</a></li>
                                <li><a href="#" class="text-gray-400 hover:text-white transition">Security</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
                
                <div class="border-t border-gray-700 mt-8 pt-6 flex flex-col md:flex-row justify-between items-center">
                    <p class="text-gray-400 text-sm mb-4 md:mb-0">&copy; 2025 AppName. All rights reserved.</p>
                    <div class="flex space-x-4">
                        <a href="#" class="text-gray-400 hover:text-white transition">
                            <span class="sr-only">Facebook</span>
                            <svg class="h-6 w-6" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path fill-rule="evenodd" d="M22 12c0-5.523-4.477-10-10-10S2 6.477 2 12c0 4.991 3.657 9.128 8.438 9.878v-6.987h-2.54V12h2.54V9.797c0-2.506 1.492-3.89 3.777-3.89 1.094 0 2.238.195 2.238.195v2.46h-1.26c-1.243 0-1.63.771-1.63 1.562V12h2.773l-.443 2.89h-2.33v6.988C18.343 21.128 22 16.991 22 12z" clip-rule="evenodd" />
                            </svg>
                        </a>
                        <a href="#" class="text-gray-400 hover:text-white transition">
                            <span class="sr-only">Twitter</span>
                            <svg class="h-6 w-6" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path d="M8.29 20.251c7.547 0 11.675-6.253 11.675-11.675 0-.178 0-.355-.012-.53A8.348 8.348 0 0022 5.92a8.19 8.19 0 01-2.357.646 4.118 4.118 0 001.804-2.27 8.224 8.224 0 01-2.605.996 4.107 4.107 0 00-6.993 3.743 11.65 11.65 0 01-8.457-4.287 4.106 4.106 0 001.27 5.477A4.072 4.072 0 012.8 9.713v.052a4.105 4.105 0 003.292 4.022 4.095 4.095 0 01-1.853.07 4.108 4.108 0 003.834 2.85A8.233 8.233 0 012 18.407a11.616 11.616 0 006.29 1.84" />
                            </svg>
                        </a>
                        <a href="#" class="text-gray-400 hover:text-white transition">
                            <span class="sr-only">Instagram</span>
                            <svg class="h-6 w-6" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path fill-rule="evenodd" d="M12.315 2c2.43 0 2.784.013 3.808.06 1.064.049 1.791.218 2.427.465a4.902 4.902 0 011.772 1.153 4.902 4.902 0 011.153 1.772c.247.636.416 1.363.465 2.427.048 1.067.06 1.407.06 4.123v.08c0 2.643-.012 2.987-.06 4.043-.049 1.064-.218 1.791-.465 2.427a4.902 4.902 0 01-1.153 1.772 4.902 4.902 0 01-1.772 1.153c-.636.247-1.363.416-2.427.465-1.067.048-1.407.06-4.123.06h-.08c-2.643 0-2.987-.012-4.043-.06-1.064-.049-1.791-.218-2.427-.465a4.902 4.902 0 01-1.772-1.153 4.902 4.902 0 01-1.153-1.772c-.247-.636-.416-1.363-.465-2.427-.047-1.024-.06-1.379-.06-3.808v-.63c0-2.43.013-2.784.06-3.808.049-1.064.218-1.791.465-2.427a4.902 4.902 0 011.153-1.772A4.902 4.902 0 015.45 2.525c.636-.247 1.363-.416 2.427-.465C8.901 2.013 9.256 2 11.685 2h.63zm-.081 1.802h-.468c-2.456 0-2.784.011-3.807.058-.975.045-1.504.207-1.857.344-.467.182-.8.398-1.15.748-.35.35-.566.683-.748 1.15-.137.353-.3.882-.344 1.857-.047 1.023-.058 1.351-.058 3.807v.468c0 2.456.011 2.784.058 3.807.045.975.207 1.504.344 1.857.182.466.399.8.748 1.15.35.35.683.566 1.15.748.353.137.882.3 1.857.344 1.054.048 1.37.058 4.041.058h.08c2.597 0 2.917-.01 3.96-.058.976-.045 1.505-.207 1.858-.344.466-.182.8-.398 1.15-.748.35-.35.566-.683.748-1.15.137-.353.3-.882.344-1.857.048-1.055.058-1.37.058-4.041v-.08c0-2.597-.01-2.917-.058-3.96-.045-.976-.207-1.505-.344-1.858a3.097 3.097 0 00-.748-1.15 3.098 3.098 0 00-1.15-.748c-.353-.137-.882-.3-1.857-.344-1.023-.047-1.351-.058-3.807-.058zM12 6.865a5.135 5.135 0 110 10.27 5.135 5.135 0 010-10.27zm0 1.802a3.333 3.333 0 100 6.666 3.333 3.333 0 000-6.666zm5.338-3.205a1.2 1.2 0 110 2.4 1.2 1.2 0 010-2.4z" clip-rule="evenodd" />
                            </svg>
                        </a>
                    </div>
                </div>
            </div>
        </footer>
        
        <!-- Back to top button -->
        <a href="#" class="fixed bottom-4 right-4 bg-blue-600 hover:bg-blue-700 text-white p-2 rounded-full shadow-lg transition duration-300">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7" />
            </svg>
        </a>
    </div>
</body>
<!-- JavaScript for carousel functionality -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const carousel = document.getElementById('gym-carousel');
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
        prevBtn.addEventListener('click', function() {
            currentIndex = (currentIndex - 1 + slides.length) % slides.length;
            updateCarousel();
            resetAutoSlide();
        });
        
        // Next button
        nextBtn.addEventListener('click', function() {
            nextSlide();
            resetAutoSlide();
        });
        
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
                    dot.classList.add('active');
                    dot.classList.add('opacity-100');
                    dot.classList.remove('opacity-50');
                } else {
                    dot.classList.remove('active');
                    dot.classList.remove('opacity-100');
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
    });
</script>
</html>