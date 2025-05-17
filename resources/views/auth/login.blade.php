<x-guest-layout>
    <!-- Make sure the guest layout doesn't have padding/margin restrictions -->

    <!-- Background image with improved full coverage -->
    <div class="min-h-screen w-full flex flex-col items-center justify-center bg-cover bg-center bg-no-repeat fixed inset-0" style="background-image: url('{{ asset('images/loginbg.jpg') }}');">
        <!-- Gradient overlay for better readability and visual appeal -->
        <div class="absolute inset-0 bg-gradient-to-br from-black/70 to-black/40"></div>
        @if ($errors->any())
    <div class="alert alert-danger">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

        <!-- Main content container - modified to match reference image -->
        <div class="w-full max-w-sm bg-white/20 backdrop-blur-xl rounded-3xl overflow-hidden relative p-8 z-10 border border-white/30 my-8">
            <!-- Card content -->
            <div class="space-y-6">
                <!-- Login heading -->
                <div class="text-center">
                    <img src="{{ asset('images/rockiesLogo.jpg') }}" alt="logo" class="w-20 h-20 mx-auto rounded-full mb-4">
                    <div class="flex-shrink-0">
                        <span class="text-gray-100 text-2xl tracking-wider font-black" style="font-weight: 900;">ROCKIES <span class="bg-gradient-to-r from-red-600 to-orange-600 bg-clip-text text-transparent font-black">FITNESS</span></span>
                    </div>
                </div>
                <!-- Login Form -->
                <form method="POST" action="{{ route('login') }}" class="space-y-5">
                    @csrf
                    
                    <!-- Email Address -->
                    <div>
                        <div class="relative">
                            <input id="email" type="email" name="email" class="w-full bg-white/20 border border-white/30 text-white pl-4 pr-10 py-3 rounded-lg focus:outline-none focus:ring-1 focus:ring-orange-500 focus:border-orange-500 placeholder-gray-400" value="{{ old('email') }}" placeholder="User Name" required autofocus autocomplete="username">
                            <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                            </div>
                        </div>
                        <x-input-error :messages="$errors->get('email')" class="mt-2" />
                    </div>
                    
                    <!-- Password -->
                    <div>
                        <div class="relative">
                            <input id="password" type="password" name="password" class="w-full bg-white/20 border border-white/30 text-white pl-4 pr-10 py-3 rounded-lg focus:outline-none focus:ring-1 focus:ring-orange-500 focus:border-orange-500 placeholder-gray-400" placeholder="Password" required autocomplete="current-password">
                            <div class="absolute inset-y-0 right-0 pr-3 flex items-center">
                                <button type="button" class="text-white hover:text-gray-200 focus:outline-none" onclick="togglePasswordVisibility()">
                                    <svg id="eye-icon" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>
                                </button>
                            </div>
                        </div>
                        <x-input-error :messages="$errors->get('password')" class="mt-2" />
                    </div>
                    
                    <!-- Remember Me -->
                    <div class="flex items-center">
                        <div class="flex items-center h-5">
                            <input id="remember_me" name="remember" type="checkbox" class="w-4 h-4 rounded border-white/30 text-orange-500 shadow-sm focus:ring-orange-500">
                        </div>
                        <div class="ml-3 text-sm">
                            <label for="remember_me" class="text-white">Remember me</label>
                        </div>
                    </div>
                    
                    <!-- Sign in Button -->
                    <div class="grid place-items-center">
                        <button type="submit" class="bg-gradient-to-r from-red-500 to-orange-500 hover:from-red-600 hover:to-orange-600 hover:translate-y-[-2px] py-2 px-10 rounded-xl text-white font-medium inline-block btn-primary pulse text-center">
                            Login
                        </button>
                    </div>
                </form>
                
                <!-- Forgot Password -->
                <div class="text-center">
                    @if (Route::has('password.request'))
                        <a href="{{ route('password.request') }}" class="text-sm text-white hover:text-gray-200">Forgot Password?</a>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- JavaScript to toggle password visibility -->
    <script>
        function togglePasswordVisibility() {
            const passwordInput = document.getElementById('password');
            const eyeIcon = document.getElementById('eye-icon');

            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                eyeIcon.innerHTML = `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.542-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.542 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />`;
            } else {
                passwordInput.type = 'password';
                eyeIcon.innerHTML = `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                     <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />`;
            }
        }
    </script>
</x-guest-layout>