{{-- resources/views/auth/forgot-password.blade.php --}}
<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
</head>
<body class="h-full bg-cover bg-center bg-no-repeat bg-fixed font-sans" 
      style="background-image: linear-gradient(rgba(0,0,0,0.75), rgba(0,0,0,0.9)), 
             url('{{ asset('images/welcomebggg.jpg') }}');">

    <div class="min-h-screen flex items-center justify-center px-4 py-12">
        <div class="form-container glass-form rounded-2xl shadow-2xl p-8 max-w-md w-full backdrop-blur-xl">

            <!-- Title -->
            <div class="text-center mb-8">
                <h2 class="text-white text-3xl font-bold tracking-tight">Forgot Password?</h2>
                <p class="text-gray-300 text-sm mt-2">No worries — we'll email you a reset link</p>
            </div>

            <!-- Success Message -->
            @if (session('status'))
                <div class="alert-glass bg-green-500 bg-opacity-20 border border-green-400 text-white p-4 rounded-lg mb-6 text-sm flex items-center gap-3">
                    <i class="fas fa-check-circle text-green-400"></i>
                    <span>{{ session('status') }}</span>
                </div>
            @endif

            <!-- Form -->
            <form method="POST" action="{{ route('password.email') }}" class="space-y-6">
                @csrf

                <div>
                    <label for="email" class="block text-white text-sm font-medium mb-2">
                        Email Address <span class="text-red-400">*</span>
                    </label>

                    <input id="email"
                           type="email"
                           name="email"
                           value="{{ old('email') }}"
                           required
                           autofocus
                           autocomplete="username"
                           placeholder="Enter your email"
                           class="w-full px-4 py-3.5 rounded-lg text-white bg-white bg-opacity-10 
                                  border border-white border-opacity-30 placeholder-gray-400
                                  focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-orange-500
                                  transition duration-200 backdrop-blur-sm text-base">

                    @error('email')
                        <p class="text-red-400 text-xs mt-2 flex items-center gap-1">
                            <i class="fas fa-exclamation-triangle"></i> {{ $message }}
                        </p>
                    @enderror
                </div>

                <!-- HIGH-VISIBILITY BUTTON - Now impossible to miss! -->
                <button type="submit"
                        class="w-full bg-gradient-to-r from-orange-500 to-orange-600 hover:from-orange-600 hover:to-orange-700
                               text-white font-bold py-4 rounded-xl text-lg shadow-lg
                               transform hover:scale-105 active:scale-95 transition-all duration-200
                               flex items-center justify-center gap-3 tracking-wide uppercase">
                    <i class="fas fa-paper-plane"></i>
                    <span>Send Reset Link</span>
                </button>
            </form>

            <!-- Back to Login -->
            <div class="text-center mt-8">
                <a href="{{ route('login') }}" 
                   class="text-orange-400 hover:text-orange-300 font-medium text-sm hover:underline transition">
                    ← Back to Login
                </a>
            </div>
        </div>
    </div>
</body>
</html>