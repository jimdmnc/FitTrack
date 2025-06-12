<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Manual Attendance - FitTrack</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-900 text-gray-200 min-h-screen flex flex-col">
    <!-- Navigation Bar (Reused from provided code, adapted for this page) -->
    <nav class="bg-black text-gray-200 py-3 px-4 md:px-6 sticky top-0 z-50">
        <div class="container mx-auto">
            <div class="flex justify-between items-center">
                <!-- Logo -->
                <div class="flex items-center">
                    <a href="{{ route('self.landingProfile') }}" aria-label="FitTrack Homepage">
                        <img src="{{ asset('images/rockiesLogo.jpg') }}" alt="FitTrack Logo" class="h-10 w-10 sm:h-12 sm:w-12 md:h-16 md:w-16 rounded-full object-cover" loading="lazy">
                    </a>
                </div>

                <!-- Desktop Navigation Links -->
                <div class="hidden md:flex items-center space-x-4 lg:space-x-6">
                    <a href="{{ route('self.landingProfile') }}#home" class="nav-link font-medium hover:text-red-400 transition duration-300 text-sm lg:text-base">Home</a>
                    <a href="{{ route('self.landingProfile') }}#inhere" class="nav-link font-medium hover:text-red-400 transition duration-300 text-sm lg:text-base">In Here</a>
                    <a href="{{ route('self.userAttendance') }}" class="nav-link font-medium hover:text-red-400 transition duration-300 text-sm lg:text-base">Attendance</a>
                    <a href="javascript:void(0)" onclick="showProfile()" class="nav-link font-medium hover:text-red-400 transition duration-300 text-sm lg:text-base">Profile</a>
                    <a href="{{ route('self.manualAttendance') }}" class="nav-link font-medium text-yellow-400 transition duration-300 text-sm lg:text-base">Forgot RFID?</a>
                    <div class="flex items-center space-x-2">
                        @if(Auth::user()->role === 'userSession')
                            <button type="button" onclick="checkRenewalEligibility()" class="bg-green-600 hover:bg-green-700 text-white font-medium py-2 px-3 rounded-full text-sm flex items-center transition duration-300 min-h-[44px]">
                                <i class="fas fa-sync-alt mr-1"></i> Renew
                            </button>
                        @endif
                        <form method="POST" action="{{ route('logout.custom') }}">
                            @csrf
                            <button type="submit" class="bg-gray-700 hover:bg-gray-800 text-white font-medium py-2 px-3 rounded-full text-sm flex items-center transition duration-300 min-h-[44px]">
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
                        <a href="{{ route('self.userAttendance') }}" class="py-3 text-xl font-medium hover:text-red-400 transition duration-300">Attendance</a>
                        <a href="javascript:void(0)" onclick="showProfile(); closeMobileMenu();" class="py-3 text-xl font-medium hover:text-red-400 transition duration-300">Profile</a>
                        <a href="{{ route('self.manualAttendance') }}" class="py-3 text-xl font-medium text-yellow-400 transition duration-300">Forgot RFID?</a>
                    </div>
                    <div class="grid grid-cols-2 gap-4 mt-6">
                        @if(Auth::user()->role === 'userSession')
                            <button type="button" onclick="checkRenewalEligibility(); closeMobileMenu();" class="bg-green-600 hover:bg-green-700 text-white font-medium py-3 px-4 rounded-lg flex items-center justify-center transition duration-300 min-h-[44px]">
                                <i class="fas fa-sync-alt mr-2"></i> Renew
                            </button>
                        @endif
                        <form method="POST" action="{{ route('logout.custom') }}" class="w-full">
                            @csrf
                            <button type="submit" class="w-full bg-gray-700 hover:bg-gray-800 text-white font-medium py-3 px-4 rounded-lg flex items-center justify-center transition duration-300 min-h-[44px]">
                                <i class="fas fa-door-open mr-2"></i> Sign Out
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="container mx-auto px-4 py-8 flex-grow flex flex-col items-center justify-center">
        <h1 class="text-2xl md:text-3xl font-bold text-white mb-6">Manual Attendance</h1>

        <!-- Alert Messages -->
        <div id="alert-container" class="w-full max-w-md mb-4">
            @if(session('success'))
                <div class="alert-banner success-alert p-3 bg-green-100 border-l-4 border-green-500 text-green-700 rounded">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                        </svg>
                        <span>{{ session('success') }}</span>
                    </div>
                </div>
            @endif
            @if(session('error'))
                <div class="alert-banner error-alert p-3 bg-red-100 border-l-4 border-red-500 text-red-700 rounded">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9

a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                        </svg>
                        <span>{{ session('error') }}</span>
                    </div>
                </div>
            @endif
        </div>

        <!-- Timer and Buttons -->
        <div class="bg-gray-800 p-6 rounded-lg shadow-lg w-full max-w-md">
            <div class="flex justify-center items-center mb-6">
                <div class="flex items-center bg-gray-700 px-4 py-2 rounded-lg">
                    <i class="fas fa-stopwatch mr-3 text-red-400 text-lg"></i>
                    <span id="workout-duration" class="text-lg font-medium">
                        @if(isset($attendance) && !$attendance->time_out)
                            {{ gmdate('H:i:s', strtotime(now()) - strtotime($attendance->time_in)) }}
                        @else
                            00:00:00
                        @endif
                    </span>
                </div>
            </div>
            <div class="flex justify-center space-x-4">
                @if(!isset($attendance) || (isset($attendance) && $attendance->time_out))
                    <!-- Time-In Button -->
                    <form method="POST" action="{{ route('self.manualTimeIn') }}">
                        @csrf
                        <button type="submit" class="bg-green-600 hover:bg-green-700 text-white font-medium py-2 px-6 rounded-lg flex items-center transition duration-300 min-h-[44px]">
                            <i class="fas fa-sign-in-alt mr-2"></i> Time In
                        </button>
                    </form>
                @else
                    <!-- Time-Out Button -->
                    <form method="POST" action="{{ route('self.manualTimeOut') }}">
                        @csrf
                        <button type="submit" class="bg-red-600 hover:bg-red-700 text-white font-medium py-2 px-6 rounded-lg flex items-center transition duration-300 min-h-[44px]">
                            <i class="fas fa-sign-out-alt mr-2"></i> Time Out
                        </button>
                    </form>
                @endif
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-black text-gray-200 py-4 mt-auto">
        <div class="container mx-auto text-center">
            <p>&copy; {{ date('Y') }} FitTrack. All rights reserved.</p>
        </div>
    </footer>

    <!-- JavaScript for Timer and Mobile Menu -->
    <script>
        // Mobile Menu Toggle
        const mobileMenuButton = document.getElementById('mobile-menu-button');
        const mobileMenu = document.getElementById('mobile-menu');
        const closeMobileMenuButton = document.getElementById('close-mobile-menu');

        mobileMenuButton.addEventListener('click', () => {
            mobileMenu.classList.toggle('hidden');
            mobileMenuButton.setAttribute('aria-expanded', mobileMenu.classList.contains('hidden') ? 'false' : 'true');
        });

        closeMobileMenuButton.addEventListener('click', () => {
            mobileMenu.classList.add('hidden');
            mobileMenuButton.setAttribute('aria-expanded', 'false');
        });

        function closeMobileMenu() {
            mobileMenu.classList.add('hidden');
            mobileMenuButton.setAttribute('aria-expanded', 'false');
        }

        // Timer Logic
        let timerInterval;
        function startTimer(startTime) {
            clearInterval(timerInterval);
            const timerElement = document.getElementById('workout-duration');
            if (!startTime) {
                timerElement.textContent = '00 \

:00:00';
                return;
            }

            timerInterval = setInterval(() => {
                const now = new Date();
                const start = new Date(startTime);
                const diff = Math.floor((now - start) / 1000);
                const hours = Math.floor(diff / 3600).toString().padStart(2, '0');
                const minutes = Math.floor((diff % 3600) / 60).toString().padStart(2, '0');
                const seconds = (diff % 60).toString().padStart(2, '0');
                timerElement.textContent = `${hours}:${minutes}:${seconds}`;
            }, 1000);
        }

        // Initialize timer if there's an active session
        @if(isset($attendance) && !$attendance->time_out)
            startTimer('{{ $attendance->time_in }}');
        @endif

        // Handle alert dismissal after 5 seconds
        document.querySelectorAll('.alert-banner').forEach(alert => {
            setTimeout(() => {
                alert.style.opacity = '0';
                setTimeout(() => alert.remove(), 300);
            }, 5000);
        });
    </script>
</body>
</html>