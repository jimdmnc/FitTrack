<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Rockies Fitness</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700|montserrat:700,800&display=swap" rel="stylesheet" />

        <!-- Styles -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        
        <style>
            .btn-primary {
                transition: all 0.3s ease;
            }
            
            .btn-primary:hover {
                transform: translateY(-2px);
                box-shadow: 0 8px 20px rgba(0, 0, 0, 0.25);
            }
            
            .gradient-text {
                background: linear-gradient(90deg, #FF6B6B, #FFA53B);
                -webkit-background-clip: text;
                background-clip: text;
                color: transparent;
                display: inline-block;
            }
            
            .hero-section {
                position: relative;
                height: calc(100vh - 4rem);
                overflow: hidden;
                background-size: cover;
                background-position: center;
                box-shadow: inset 0 0 100px rgba(0, 0, 0, 0.5);
            }
            
            .hero-overlay {
                background: linear-gradient(135deg, rgba(0, 0, 0, 0.85) 0%, rgba(0, 0, 0, 0.5) 100%);
            }
            
            .nav-glow {
                box-shadow: 0 4px 30px rgba(0, 0, 0, 0.5);
                backdrop-filter: blur(8px);
            }
            
            .text-container {
                animation: fadeIn 1s ease-out;
            }
            
            @keyframes fadeIn {
                from { opacity: 0; transform: translateY(20px); }
                to { opacity: 1; transform: translateY(0); }
            }
            
            .pulse {
                animation: pulse 2s infinite;
            }
            
            @keyframes pulse {
                0% { transform: scale(1); }
                50% { transform: scale(1.15); }
                100% { transform: scale(1); }
            }
        </style>
    </head>
    <body class="bg-gray-900 text-gray-100 font-sans antialiased">
        <div class="min-h-screen flex flex-col">
            <!-- Navigation Bar -->
            <header class="bg-gray-900 bg-opacity-80 nav-glow fixed w-full z-50">
                <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="flex justify-between h-16 items-center">
                        <div class="flex items-center space-x-4">
                            <img src="{{ asset('images/rockiesLogo.jpg') }}" alt="Rockies Fitness Logo" class="w-12 h-12 rounded-full border-2 border-gray-700">
                            <div class="flex-shrink-0">
                                <span class="text-gray-100 font-extrabold text-xl tracking-wider">ROCKIES <span class="gradient-text">FITNESS</span></span>
                            </div>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Hero Section with Background Image -->
            <div class="hero-section relative flex-grow flex items-center" style="background-image: url('{{ asset('images/welcomebggg.jpg') }}');">
                <!-- Overlay -->
                <div class="hero-overlay absolute inset-0"></div>
                
                <div class="max-w-6xl mx-auto w-full relative z-10 px-4 sm:px-6 lg:px-8 flex flex-col md:flex-row items-center">
                    <!-- Left Side - Empty space or additional element -->
                    <div class="w-full md:w-1/2 mb-10 md:mb-0">
                        <!-- Could add an image, animation, or leave empty -->
                    </div>

                    <!-- Right Side - Text and Button -->
                    <div class="w-full md:w-1/2 text-container px-4 md:px-8">
                        <h1 class="text-4xl sm:text-5xl lg:text-6xl font-bold mb-6 text-gray-100 leading-tight">
                            BUILD YOUR <span class="gradient-text">BODY</span><br>
                            <span class="text-3xl sm:text-4xl">TRANSFORM YOUR LIFE</span>
                        </h1>
                        <p class="text-lg text-gray-300 mb-8 max-w-lg">
                            Join Rockies Fitness and elevate your fitness journey with our expert trainers and premium equipment. Your transformation starts here.
                        </p>
                        <!-- Login and go to -->
                        <div class="flex flex-col sm:flex-row space-y-4 sm:space-y-0 sm:space-x-4">
                        @if (Route::has('login'))
                            @auth
                                <a href="{{ url('/staff/dashboard') }}" class="bg-gradient-to-r from-red-500 to-orange-500 hover:from-red-600 hover:to-orange-600 py-3 px-6 rounded-xl text-white font-medium inline-block btn-primary text-center">
                                    Go to Dashboard
                                </a>
                            @else
                                <a href="{{ route('login') }}" class="bg-gradient-to-r from-red-500 to-orange-500 hover:from-red-600 hover:to-orange-600 py-3 px-6 rounded-xl text-white font-medium inline-block btn-primary pulse text-center">
                                    Login Now
                                </a>
                            @endauth
                        @endif
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </body>
</html>