<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot RFID - FitTrack</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="icon" type="image/png" sizes="180x180" href="{{ asset('images/rockiesLogo.png') }}">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Montserrat:wght@600;700;800&display=swap');
        
        body {
            font-family: 'Montserrat', sans-serif;
            background-color: #111827;
        }

        .form-container {
            background: linear-gradient(135deg, rgba(255,255,255,0.03) 0%, rgba(255,255,255,0) 100%);
            border: 1px solid rgba(255,255,255,0.1);
            backdrop-filter: blur(10px);
        }

        .fade-in {
            animation: fadeIn 0.5s ease-out forwards;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .btn-primary {
            background-color: #FF5722;
            transition: background-color 0.3s ease;
        }

        .btn-primary:hover {
            background-color: #E64A19;
        }

        .btn-secondary {
            background-color: #4B5563;
            transition: background-color 0.3s ease;
        }

        .btn-secondary:hover {
            background-color: #374151;
        }
    </style>
</head>
<body class="bg-gray-900 text-gray-200">
    <!-- Navigation Bar -->
    <nav class="bg-black text-gray-200 py-3 px-4 md:px-6 sticky top-0 z-50">
        <div class="container mx-auto flex justify-between items-center">
            <div class="flex items-center">
                <a href="{{ route('self.landingProfile') }}" aria-label="FitTrack Homepage">
                    <img src="{{ asset('images/rockiesLogo.png') }}" alt="FitTrack Logo" class="h-10 w-10 rounded-full object-cover" loading="lazy">
                </a>
            </div>
            <div class="hidden md:flex items-center space-x-4">
                <a href="{{ route('self.landingProfile') }}#home" class="font-medium hover:text-red-400 transition duration-300">Home</a>
                <a href="{{ route('self.landingProfile') }}#inhere" class="font-medium hover:text-red-400 transition duration-300">In Here</a>
                <a href="{{ route('self.userAttendance') }}" class="font-medium hover:text-red-400 transition duration-300">Attendance</a>
                <a href="javascript:void(0)" onclick="showProfile()" class="font-medium hover:text-red-400 transition duration-300">Profile</a>
                <a href="{{ route('self.forgotRfid') }}" class="font-medium text-yellow-400 transition duration-300">Forgot RFID?</a>
                <form method="POST" action="{{ route('logout.custom') }}">
                    @csrf
                    <button type="submit" class="bg-gray-700 hover:bg-gray-800 text-white font-medium py-2 px-3 rounded-full flex items-center transition duration-300">
                        <i class="fas fa-door-open mr-1"></i> Sign Out
                    </button>
                </form>
            </div>
            <div class="md:hidden">
                <button id="mobile-menu-button" class="text-gray-200 p-1 focus:outline-none bg-gray-800 rounded-md" aria-label="Toggle mobile menu">
                    <i class="fas fa-bars text-xl"></i>
                </button>
            </div>
        </div>
        <!-- Mobile Menu -->
        <div id="mobile-menu" class="md:hidden hidden fixed inset-0 bg-black bg-opacity-95 z-50 flex flex-col">
            <div class="container mx-auto px-4 py-8 flex flex-col h-full">
                <div class="flex justify-end mb-6">
                    <button id="close-mobile-menu" class="text-gray-300 hover:text-white" aria-label="Close mobile menu">
                        <i class="fas fa-times text-2xl"></i>
                    </button>
                </div>
                <div class="flex flex-col space-y-6 text-center flex-grow">
                    <a href="{{ route('self.landingProfile') }}#home" class="py-3 text-xl font-medium hover:text-red-400">Home</a>
                    <a href="{{ route('self.landingProfile') }}#inhere" class="py-3 text-xl font-medium hover:text-red-400">About Us</a>
                    <a href="{{ route('self.userAttendance') }}" class="py-3 text-xl font-medium hover:text-red-400">Attendance</a>
                    <a href="javascript:void(0)" onclick="showProfile(); closeMobileMenu();" class="py-3 text-xl font-medium hover:text-red-400">Profile</a>
                    <a href="{{ route('self.forgotRfid') }}" class="py-3 text-xl font-medium text-yellow-400">Forgot RFID?</a>
                    <form method="POST" action="{{ route('logout.custom') }}" class="w-full">
                        @csrf
                        <button type="submit" class="w-full bg-gray-700 hover:bg-gray-800 text-white font-medium py-3 px-4 rounded-lg flex items-center justify-center transition duration-300">
                            <i class="fas fa-door-open mr-2"></i> Sign Out
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <section class="py-16">
        <div class="container mx-auto px-6">
            <h1 class="text-4xl md:text-5xl font-extrabold text-center text-gray-200 mb-8">Forgot Your RFID Card?</h1>
            <p class="text-lg text-gray-400 text-center mb-12 max-w-2xl mx-auto">
                Verify your identity to manually record your time-in or time-out.
            </p>
            <div class="form-container max-w-md mx-auto p-6 rounded-lg shadow-xl fade-in">
                <form id="forgot-rfid-form" action="{{ route('self.manualAttendance') }}" method="POST">
                    @csrf
                    <div class="mb-6">
                        <label for="identifier" class="block text-sm font-medium text-gray-300 mb-2">Email or Phone Number</label>
                        <input type="text" id="identifier" name="identifier" class="w-full px-4 py-3 bg-gray-800 border border-gray-700 rounded-lg text-gray-200 focus:outline-none focus:ring-2 focus:ring-orange-500" placeholder="Enter email or phone number" required>
                        @error('identifier')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="flex space-x-4">
                        <button type="submit" name="action" value="time_in" class="w-1/2 py-3 btn-primary text-white font-medium rounded-lg flex items-center justify-center transition duration-300">
                            <i class="fas fa-sign-in-alt mr-2"></i> Time In
                        </button>
                        <button type="submit" name="action" value="time_out" class="w-1/2 py-3 btn-secondary text-white font-medium rounded-lg flex items-center justify-center transition duration-300">
                            <i class="fas fa-sign-out-alt mr-2"></i> Time Out
                        </button>
                    </div>
                </form>
                <div id="form-errors" class="text-red-500 text-sm mt-4 hidden"></div>
            </div>
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
                            <li><a href="{{ route('self.landingProfile') }}#home" class="text-gray-400 hover:text-red-500 transition duration-300">Home</a></li>
                            <li><a href="{{ route('self.landingProfile') }}#inhere" class="text-gray-400 hover:text-red-500 transition duration-300">In Here</a></li>
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

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            initNavigation();
            initFormSubmission();
        });

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

        function initFormSubmission() {
            const form = document.getElementById('forgot-rfid-form');
            if (!form) return;

            form.addEventListener('submit', async function(e) {
                e.preventDefault();
                const formData = new FormData(form);
                const submitButton = form.querySelector(`button[name="action"][value="${formData.get('action')}"]`);
                const errorDiv = document.getElementById('form-errors');

                submitButton.disabled = true;
                submitButton.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> Processing...';

                try {
                    const response = await fetch(form.action, {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        }
                    });

                    const data = await response.json();

                    if (!response.ok) {
                        throw new Error(data.message || 'Operation failed');
                    }

                    if (data.success) {
                        showNotification('Success', data.message, 'success');
                        setTimeout(() => {
                            window.location.href = '{{ route('self.landingProfile') }}';
                        }, 2000);
                    } else {
                        throw new Error(data.message || 'Operation failed');
                    }
                } catch (error) {
                    errorDiv.classList.remove('hidden');
                    errorDiv.textContent = error.message || 'An error occurred. Please try again.';
                    showNotification('Error', error.message || 'Operation failed', 'error');
                } finally {
                    submitButton.disabled = false;
                    submitButton.innerHTML = submitButton.value === 'time_in' 
                        ? '<i class="fas fa-sign-in-alt mr-2"></i> Time In' 
                        : '<i class="fas fa-sign-out-alt mr-2"></i> Time Out';
                }
            });
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
    </script>
</body>
</html>