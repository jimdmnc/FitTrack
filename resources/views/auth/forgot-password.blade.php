{{-- resources/views/auth/forgot-password.blade.php --}}
<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-full bg-cover bg-center bg-no-repeat bg-fixed" 
      style="background-image: linear-gradient(rgba(0, 0, 0, 0.65), rgba(0, 0, 0, 0.8)), 
             url('{{ asset('images/welcomebgg.jpg') }}');">

    <div class="min-h-screen flex items-center justify-center px-4 py-12">
        <div class="form-container glass-form rounded-2xl shadow-2xl p-8 max-w-md w-full">
            
            <!-- Logo / Title (optional) -->
            <div class="text-center mb-8">
                <h2 class="text-white text-3xl font-bold>Reset Password</h2>
                <p class="text-gray-300 text-sm mt-2">Enter your email and we'll send you a reset link</p>
            </div>

            <!-- Success Message -->
            @if (session('status'))
                <div class="alert-glass text-white p-4 rounded-lg mb-6 text-sm flex items-center gap-3">
                    <i class="fas fa-check-circle text-green-400"></i>
                    <span>{{ session('status') }}</span>
                </div>
            @endif

            <form method="POST" action="{{ route('password.email') }}" class="space-y-6">
                @csrf

                <!-- Email Address -->
                <div>
                    <label for="email" class="block text-white text-sm font-medium mb-2">
                        Email Address <span class="text-red-400">*</span>
                    </label>
                    <input id="email"
                           type="email"
                           name="email"
                           value="{{ old('email') }}"
                           required autofocus autocomplete="username"
                           class="input-field w-full px-4 py-3 rounded-lg text-base text-white placeholder-gray-400"
                           placeholder="you@example.com">

                    <!-- Laravel Error -->
                    @error('email')
                        <p class="text-red-400 text-xs mt-2">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Submit Button -->
                <button type="submit"
                        class="btn-login w-full text-white py-3.5 rounded-lg font-semibold text-lg 
                               flex items-center justify-center gap-3 hover:shadow-xl transform hover:scale-105 transition">
                    <span>Email Password Reset Link</span>
                    <i class="fas fa-paper-plane"></i>
                </button>
            </form>

            <!-- Back to Login -->
            <div class="text-center mt-8">
                <a href="{{ route('login') }}" 
                   class="text-orange-300 hover:text-orange-400 font-medium text-sm hover:underline transition">
                    Back to Login
                </a>
            </div>
        </div>
    </div>

</body>
</html>