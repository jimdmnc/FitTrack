<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Login</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="icon" type="image/png" sizes="180x180" href="{{ asset('images/rockiesLogo.png') }}">
    <style>
        .gradient-bg {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        
        .glass-effect {
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }
        
        .input-group {
            position: relative;
        }
        
        .floating-label {
            position: absolute;
            left: 12px;
            top: 50%;
            transform: translateY(-50%);
            color: #9ca3af;
            transition: all 0.3s ease;
            pointer-events: none;
            font-size: 16px;
        }
        
        .input-field:focus + .floating-label,
        .input-field:not(:placeholder-shown) + .floating-label {
            top: -8px;
            left: 8px;
            font-size: 12px;
            color: #ff5722;
            background: #1f2937;
            padding: 0 4px;
            border-radius: 4px;
        }
        
        .input-field {
            background: rgba(31, 41, 55, 0.8);
            border: 2px solid rgba(75, 85, 99, 0.5);
            color: #f9fafb;
            transition: all 0.3s ease;
        }
        
        .input-field:focus {
            border-color: #ff5722;
            box-shadow: 0 0 0 3px rgba(255, 87, 34, 0.1);
            background: rgba(31, 41, 55, 0.9);
        }
        
        .input-field.error {
            border-color: #ef4444;
            box-shadow: 0 0 0 3px rgba(239, 68, 68, 0.1);
        }
        
        .btn-primary {
            background: linear-gradient(135deg, #ff5722 0%, #e64a19 100%);
            box-shadow: 0 4px 15px rgba(255, 87, 34, 0.3);
            transition: all 0.3s ease;
        }
        
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(255, 87, 34, 0.4);
        }
        
        .btn-primary:active {
            transform: translateY(0);
        }
        
        .btn-primary:disabled {
            opacity: 0.6;
            transform: none;
            box-shadow: 0 4px 15px rgba(255, 87, 34, 0.2);
        }
        
        .error-message {
            color: #f87171;
            font-size: 0.875rem;
            margin-top: 0.5rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            opacity: 0;
            transform: translateY(-10px);
            transition: all 0.3s ease;
        }
        
        .error-message.show {
            opacity: 1;
            transform: translateY(0);
        }
        
        .success-message {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            border: none;
            animation: slideDown 0.5s ease;
        }
        
        .error-alert {
            background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
            border: none;
            animation: slideDown 0.5s ease;
        }
        
        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .logo-container {
            animation: fadeInUp 0.8s ease;
        }
        
        .form-container {
            animation: fadeInUp 0.8s ease 0.2s both;
        }
        
        .help-container {
            animation: fadeInUp 0.8s ease 0.4s both;
        }
        
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .password-toggle {
            position: absolute;
            right: 12px;
            top: 50%;
            transform: translateY(-50%);
            color: #9ca3af;
            cursor: pointer;
            transition: color 0.3s ease;
            z-index: 10;
        }
        
        .password-toggle:hover {
            color: #ff5722;
        }
        
        .loading-spinner {
            display: none;
            width: 20px;
            height: 20px;
            border: 2px solid transparent;
            border-top: 2px solid currentColor;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }
        
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        
        .form-floating {
            background: rgba(17, 24, 39, 0.95);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(75, 85, 99, 0.3);
        }
        
        .welcome-text {
            background: linear-gradient(135deg, #ff5722 0%, #ff9800 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        
        .social-divider {
            position: relative;
            text-align: center;
            margin: 1.5rem 0;
        }
        
        .social-divider::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 0;
            right: 0;
            height: 1px;
            background: linear-gradient(90deg, transparent, rgba(75, 85, 99, 0.5), transparent);
        }
        
        .social-divider span {
            background: #111827;
            padding: 0 1rem;
            color: #9ca3af;
            font-size: 0.875rem;
        }
        
        .remember-me {
            position: relative;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            cursor: pointer;
        }
        
        .remember-me input[type="checkbox"] {
            opacity: 0;
            position: absolute;
        }
        
        .custom-checkbox {
            width: 20px;
            height: 20px;
            border: 2px solid #4b5563;
            border-radius: 4px;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s ease;
        }
        
        .remember-me input[type="checkbox"]:checked + .custom-checkbox {
            background: #ff5722;
            border-color: #ff5722;
        }
        
        .remember-me input[type="checkbox"]:checked + .custom-checkbox::after {
            content: '✓';
            color: white;
            font-size: 12px;
            font-weight: bold;
        }
    </style>
</head>
<body class="min-h-screen gradient-bg flex items-center justify-center p-4">
    <div class="w-full max-w-md">
        <!-- Logo/Title Section -->
        <div class="logo-container text-center mb-8">
            <div class="inline-flex items-center justify-center w-16 h-16 rounded-full glass-effect mb-4">
                <i class="fas fa-user-circle text-3xl text-orange-400"></i>
            </div>
            <h1 class="text-4xl font-bold welcome-text mb-2">Welcome Back</h1>
            <p class="text-gray-300 text-lg">Sign in to your account</p>
        </div>

        <!-- Main Form Container -->
        <div class="form-container form-floating rounded-2xl shadow-2xl p-8">
            <!-- Alert Messages -->
            @if (session('success'))
                <div class="success-message text-white p-4 rounded-lg mb-6 text-sm flex items-center gap-3">
                    <i class="fas fa-check-circle text-lg"></i>
                    <span>{{ session('success') }}</span>
                </div>
            @elseif(session('error'))
                <div class="error-alert text-white p-4 rounded-lg mb-6 text-sm flex items-center gap-3">
                    <i class="fas fa-exclamation-circle text-lg"></i>
                    <span>{{ session('error') }}</span>
                </div>
            @endif

            <form method="POST" action="{{ route('self.login.submit') }}" id="loginForm" novalidate>
                @csrf
                <div class="space-y-6">
                    <!-- Email Field -->
                    <div class="input-group">
                        <input type="email" 
                               class="input-field w-full px-4 py-4 rounded-lg text-base placeholder-transparent" 
                               id="email" 
                               name="email" 
                               placeholder="Email Address"
                               required
                               pattern="[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,}$"
                               value="{{ old('email') }}">
                        <label for="email" class="floating-label">
                            <i class="fas fa-envelope mr-2"></i>Email Address
                        </label>
                        <div id="email_error" class="error-message">
                            <i class="fas fa-exclamation-triangle"></i>
                            <span>Please enter a valid email address</span>
                        </div>
                    </div>

                    <!-- Password Field -->
                    <div class="input-group">
                        <input type="password" 
                               class="input-field w-full px-4 py-4 pr-12 rounded-lg text-base placeholder-transparent" 
                               id="password" 
                               name="password" 
                               placeholder="Password"
                               required
                               minlength="8">
                        <label for="password" class="floating-label">
                            <i class="fas fa-lock mr-2"></i>Password
                        </label>
                        <i class="password-toggle fas fa-eye" onclick="togglePassword()"></i>
                        <div id="password_error" class="error-message">
                            <i class="fas fa-exclamation-triangle"></i>
                            <span>Password must be at least 8 characters</span>
                        </div>
                    </div>

                    <!-- Remember Me & Forgot Password -->
                    <div class="flex items-center justify-between">
                        <label class="remember-me text-gray-300 text-sm">
                            <input type="checkbox" name="remember">
                            <div class="custom-checkbox"></div>
                            Remember me
                        </label>
                        <a href="#" class="text-sm text-orange-400 hover:text-orange-300 transition-colors hover:underline">
                            Forgot password?
                        </a>
                    </div>

                    <!-- Submit Button -->
                    <button type="submit" 
                            class="btn-primary w-full text-white py-4 rounded-lg font-semibold text-lg flex items-center justify-center gap-3 transition-all duration-300">
                        <span class="button-text">Sign In</span>
                        <div class="loading-spinner"></div>
                        <i class="fas fa-arrow-right button-icon"></i>
                    </button>
                </div>
            </form>

            <!-- Divider -->
            <div class="social-divider">
                <span>or continue with</span>
            </div>

            <!-- Social Login Buttons (Optional) -->
            <div class="grid grid-cols-2 gap-4 mb-6">
                <button class="flex items-center justify-center gap-2 py-3 px-4 bg-gray-700 hover:bg-gray-600 text-white rounded-lg transition-colors">
                    <i class="fab fa-google text-red-400"></i>
                    <span class="text-sm">Google</span>
                </button>
                <button class="flex items-center justify-center gap-2 py-3 px-4 bg-gray-700 hover:bg-gray-600 text-white rounded-lg transition-colors">
                    <i class="fab fa-facebook text-blue-400"></i>
                    <span class="text-sm">Facebook</span>
                </button>
            </div>

            <!-- Register Link -->
            <p class="text-center text-gray-400">
                Don't have an account? 
                <a href="{{ route('self.registration') }}" class="text-orange-400 hover:text-orange-300 font-semibold hover:underline transition-colors">
                    Create one now
                </a>
            </p>
        </div>

        <!-- Help Section -->
        <div class="help-container mt-8 text-center">
            <a href="tel:+18005551234" 
               class="inline-flex items-center justify-center gap-3 text-white hover:text-orange-300 transition-colors group">
                <div class="w-10 h-10 rounded-full glass-effect flex items-center justify-center group-hover:scale-110 transition-transform">
                    <i class="fas fa-phone-alt text-orange-400"></i>
                </div>
                <div class="text-left">
                    <div class="text-sm text-gray-400">Need help?</div>
                    <div class="font-semibold">Call Support</div>
                </div>
            </a>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('loginForm');
            const submitBtn = form.querySelector('button[type="submit"]');
            const buttonText = submitBtn.querySelector('.button-text');
            const buttonIcon = submitBtn.querySelector('.button-icon');
            const loadingSpinner = submitBtn.querySelector('.loading-spinner');
            
            form.addEventListener('submit', function(e) {
                if (!validateForm()) {
                    e.preventDefault();
                } else {
                    // Show loading state
                    showLoading();
                }
            });
            
            const inputs = form.querySelectorAll('input[type="email"], input[type="password"]');
            inputs.forEach(input => {
                input.addEventListener('input', function() {
                    validateField(this);
                    updateSubmitButton();
                });
                
                input.addEventListener('blur', function() {
                    validateField(this);
                    updateSubmitButton();
                });
                
                input.addEventListener('focus', function() {
                    this.classList.remove('error');
                });
            });

            function validateForm() {
                let isValid = true;
                inputs.forEach(input => {
                    if (!validateField(input)) {
                        isValid = false;
                    }
                });
                return isValid;
            }

            function validateField(field) {
                const errorElement = document.getElementById(`${field.id}_error`);
                if (!errorElement) return true;
                
                let isValid = true;
                let errorMessage = '';
                
                if (field.required && !field.value.trim()) {
                    isValid = false;
                    errorMessage = `Please enter your ${field.id === 'email' ? 'email address' : 'password'}`;
                } else if (field.id === 'email' && field.value.trim() && !isValidEmail(field.value)) {
                    isValid = false;
                    errorMessage = 'Please enter a valid email address';
                } else if (field.id === 'password' && field.value.trim() && field.value.length < 8) {
                    isValid = false;
                    errorMessage = 'Password must be at least 8 characters long';
                }
                
                if (!isValid) {
                    showError(field, errorElement, errorMessage);
                } else {
                    hideError(field, errorElement);
                }
                
                return isValid;
            }
            
            function showError(field, errorElement, message) {
                field.classList.add('error');
                errorElement.querySelector('span').textContent = message;
                errorElement.classList.add('show');
            }
            
            function hideError(field, errorElement) {
                field.classList.remove('error');
                errorElement.classList.remove('show');
            }
            
            function updateSubmitButton() {
                const isFormValid = validateForm();
                submitBtn.disabled = !isFormValid;
            }
            
            function showLoading() {
                submitBtn.disabled = true;
                buttonText.style.display = 'none';
                buttonIcon.style.display = 'none';
                loadingSpinner.style.display = 'block';
            }
            
            function isValidEmail(email) {
                const emailPattern = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;
                return emailPattern.test(email);
            }
        });

        function togglePassword() {
            const passwordField = document.getElementById('password');
            const toggleIcon = document.querySelector('.password-toggle');
            
            if (passwordField.type === 'password') {
                passwordField.type = 'text';
                toggleIcon.classList.remove('fa-eye');
                toggleIcon.classList.add('fa-eye-slash');
            } else {
                passwordField.type = 'password';
                toggleIcon.classList.remove('fa-eye-slash');
                toggleIcon.classList.add('fa-eye');
            }
        }
    </script>
</body>
</html>