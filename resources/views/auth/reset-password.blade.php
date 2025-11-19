{{-- resources/views/auth/reset-password.blade.php --}}
<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body class="h-full bg-cover bg-center bg-no-repeat bg-fixed font-sans"
      style="background-image: linear-gradient(rgba(0,0,0,0.75), rgba(0,0,0,0.9)), 
             url('{{ asset('images/welcomebgg.jpg') }}');">

    <div class="min-h-screen flex items-center justify-center px-4 py-12">
        <div class="form-container glass-form rounded-2xl shadow-2xl p-8 max-w-md w-full backdrop-blur-xl">

            <!-- Title -->
            <div class="text-center mb-8">
                <h2 class="text-white text-3xl font-bold tracking-tight">Set New Password</h2>
                <p class="text-gray-300 text-sm mt-2">Choose a strong password you'll remember</p>
            </div>

            <!-- Success / Error from session -->
            @if (session('status'))
                <div class="alert-glass bg-green-500 bg-opacity-20 border border-green-400 text-white p-4 rounded-lg mb-6 text-sm flex items-center gap-3">
                    <i class="fas fa-check-circle text-green-400"></i>
                    <span>{{ session('status') }}</span>
                </div>
            @endif

            <form method="POST" action="{{ route('password.store') }}" class="space-y-6">
                @csrf

                <!-- Hidden Token -->
                <input type="hidden" name="token" value="{{ $request->route('token') }}">

                <!-- Email -->
                <div>
                    <label for="email" class="block text-white text-sm font-medium mb-2">
                        Email Address
                    </label>
                    <input id="email"
                           type="email"
                           name="email"
                           value="{{ old('email', $request->email) }}"
                           required autofocus autocomplete="username"
                           class="w-full px-4 py-3.5 rounded-lg text-white bg-white bg-opacity-10 
                                  border border-white border-opacity-30 placeholder-gray-400
                                  focus:outline-none focus:ring-2 focus:ring-orange-500 transition backdrop-blur-sm">
                    @error('email')
                        <p class="text-red-400 text-xs mt-2 flex items-center gap-1">
                            <i class="fas fa-exclamation-triangle"></i> {{ $message }}
                        </p>
                    @enderror
                </div>

                <!-- Password -->
                <div>
                    <label for="password" class="block text-white text-sm font-medium mb-2">
                        New Password <span class="text-red-400">*</span>
                    </label>
                    <div class="relative">
                        <input id="password"
                               type="password"
                               name="password"
                               required autocomplete="new-password"
                               class="w-full px-4 py-3.5 pr-12 rounded-lg text-white bg-white bg-opacity-10 
                                      border border-white border-opacity-30 placeholder-gray-400
                                      focus:outline-none focus:ring-2 focus:ring-orange-500 transition backdrop-blur-sm">
                        <i class="fas fa-eye password-toggle absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-orange-400 cursor-pointer transition" 
                           onclick="togglePassword('password')"></i>
                    </div>
                    @error('password')
                        <p class="text-red-400 text-xs mt-2 flex items-center gap-1">
                            <i class="fas fa-exclamation-triangle"></i> {{ $message }}
                        </p>
                    @enderror
                </div>

                <!-- Confirm Password -->
                <div>
                    <label for="password_confirmation" class="block text-white text-sm font-medium mb-2">
                        Confirm Password <span class="text-red-400">*</span>
                    </label>
                    <div class="relative">
                        <input id="password_confirmation"
                               type="password"
                               name="password_confirmation"
                               required autocomplete="new-password"
                               class="w-full px-4 py-3.5 pr-12 rounded-lg text-white bg-white bg-opacity-10 
                                      border border-white border-opacity-30 placeholder-gray-400
                                      focus:outline-none focus:ring-2 focus:ring-orange-500 transition backdrop-blur-sm">
                        <i class="fas fa-eye password-toggle absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-orange-400 cursor-pointer transition" 
                           onclick="togglePassword('password_confirmation')"></i>
                    </div>
                </div>

                <!-- Submit Button - Super Visible Orange Gradient -->
                <button type="submit"
                        class="w-full bg-gradient-to-r from-orange-500 to-orange-600 hover:from-orange-600 hover:to-orange-700
                               text-white font-bold py-4 rounded-xl text-lg shadow-lg
                               transform hover:scale-105 active:scale-95 transition-all duration-200
                               flex items-center justify-center gap-3 tracking-wide uppercase">
                    <i class="fas fa-key"></i>
                    <span>Reset Password</span>
                </button>
            </form>

            <!-- Back to Login -->
            <div class="text-center mt-8">
                <a href="{{ route('login') }}" 
                   class="text-orange-400 hover:text-orange-300 font-medium text-sm hover:underline transition">
                    Back to Login
                </a>
            </div>
        </div>
    </div>

    <!-- Simple JS for password toggle -->
    <script>
        function togglePassword(id) {
            const input = document.getElementById(id);
            const icon = input.nextElementSibling;
            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.replace('fa-eye', 'fa-eye-slash');
            } else {
                input.type = 'password';
                icon.classList.replace('fa-eye-slash', 'fa-eye');
            }
        }
    </script>
</body>
</html>