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
        body {
            background-image: url('images/welcomebggg.jpg');
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
        }
        
        .glass-form {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
        
        .input-field {
            background: rgba(255, 255, 255, 0.15);
            border: 1px solid rgba(255, 255, 255, 0.3);
            color: white;
            backdrop-filter: blur(10px);
            transition: all 0.3s ease;
        }
        
        .input-field::placeholder {
            color: rgba(255, 255, 255, 0.7);
        }
        
        .input-field:focus {
            background: rgba(255, 255, 255, 0.2);
            border-color: #ff5722;
            box-shadow: 0 0 0 2px rgba(255, 87, 34, 0.3);
            outline: none;
        }
        
        .input-field.error {
            border-color: #ef4444;
            box-shadow: 0 0 0 2px rgba(239, 68, 68, 0.3);
        }
        
        .btn-login {
            background: linear-gradient(135deg, #ff5722 0%, #e64a19 100%);
            transition: all 0.3s ease;
        }
        
        .btn-login:hover {
            transform: translateY(-1px);
            box-shadow: 0 8px 25px rgba(255, 87, 34, 0.4);
        }
        
        .btn-login:disabled {
            opacity: 0.6;
            transform: none;
        }
        
        .error-message {
            color: #fca5a5;
            font-size: 0.875rem;
            margin-top: 0.5rem;
            opacity: 0;
            transform: translateY(-5px);
            transition: all 0.3s ease;
        }
        
        .error-message.show {
            opacity: 1;
            transform: translateY(0);
        }
        
        .alert-glass {
            background: rgba(255, 255, 255, 0.15);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
        
        .title-text {
            background: linear-gradient(135deg, #ffffff 0%, #ff5722 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        
        .password-toggle {
            position: absolute;
            right: 12px;
            top: 50%;
            transform: translateY(-50%);
            color: rgba(255, 255, 255, 0.7);
            cursor: pointer;
            transition: color 0.3s ease;
        }
        
        .password-toggle:hover {
            color: #ff5722;
        }
        
        .loading-spinner {
            display: none;
            width: 16px;
            height: 16px;
            border: 2px solid transparent;
            border-top: 2px solid currentColor;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }
        
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        
        .form-container {
            animation: fadeInUp 0.8s ease;
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
        
        .link-glass {
            color: rgba(255, 255, 255, 0.9);
            transition: all 0.3s ease;
        }
        
        .link-glass:hover {
            color: #ff5722;
            text-shadow: 0 0 10px rgba(255, 87, 34, 0.5);
        }
        
        .help-glass {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
    </style>
</head>
<body class="min-h-screen flex items-center justify-center p-4">
    <div class="w-full max-w-md">
        <!-- Title -->
        <div class="text-center mb-8">
            <h1 class="text-4xl md:text-5xl font-bold title-text mb-2">Login</h1>
            <p class="text-white text-opacity-80">Welcome back! Please sign in to your account</p>
        </div>

        <!-- Main Form -->
        <div class="form-container glass-form rounded-2xl shadow-2xl p-8">
            <!-- Alert Messages -->
            @if (session('success'))
                <div class="alert-glass text-white p-4 rounded-lg mb-6 text-sm flex items-center gap-3">
                    <i class="fas fa-check-circle text-green-400"></i>
                    <span>{{ session('success') }}</span>
                </div>
            @elseif(session('error'))
                <div class="alert-glass text-white p-4 rounded-lg mb-6 text-sm flex items-center gap-3">
                    <i class="fas fa-exclamation-circle text-red-400"></i>
                    <span>{{ session('error') }}</span>
                </div>
            @endif

            <form method="POST" action="{{ route('self.login.submit') }}" id="loginForm" novalidate>
                @csrf
                <div class="space-y-6">
                    <!-- Email Field -->
                    <div>
                        <label for="email" class="block text-white text-sm font-medium mb-2">
                            Email Address <span class="text-red-400">*</span>
                        </label>
                        <input type="email" 
                               class="input-field w-full px-4 py-3 rounded-lg text-base" 
                               id="email" 
                               name="email" 
                               placeholder="Enter your email address"
                               required
                               pattern="[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,}$"
                               value="{{ old('email') }}">
                        <div id="email_error" class="error-message">
                            Please enter a valid email address
                        </div>
                    </div>

                    <!-- Password Field -->
                    <div>
                        <label for="password" class="block text-white text-sm font-medium mb-2">
                            Password <span class="text-red-400">*</span>
                        </label>
                        <div class="relative">
                            <input type="password" 
                                class="input-field w-full px-4 py-3 pr-12 rounded-lg text-base" 
                                id="password" 
                                name="password" 
                                placeholder="Enter your password"
                                required
                                minlength="8">
                            <i class="password-toggle fas fa-eye absolute right-3 top-1/2 transform -translate-y-1/2 cursor-pointer hover:text-orange-400" id="togglePassword"></i>
                        </div>
                        <div id="password_error" class="error-message">
                            Password must be at least 8 characters
                        </div>
                            <!-- Password Hint -->
                        <div class="text-sm text-gray-300 mt-2">
                            <p class="font-medium">Password Hint:</p>
                            <p>Your password follows this format: <span class="font-mono">lastnameMMDDYYYY</span></p>
                            <p>Example: If your last name is Smith and birthdate is June 5, 1990 → <span class="font-mono">smith06051990</span></p>
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <button type="submit" 
                            class="btn-login w-full text-white py-3 rounded-lg font-semibold text-lg flex items-center justify-center gap-2">
                        <span class="button-text">Login</span>
                        <div class="loading-spinner"></div>
                    </button>
                </div>
            </form>

            <!-- Register Link -->
            <p class="text-center mt-6 text-white text-opacity-80">
                Don't have an account? 
                <a href="{{ route('self.registration') }}" class="link-glass font-semibold hover:underline">
                    Register here
                </a>
            </p>
        </div>

        <!-- Help Section -->
        <div class="mt-6 text-center">
            <a href="tel:+18005551234" 
               class="inline-flex items-center justify-center gap-3 help-glass rounded-lg px-4 py-3 link-glass">
                <i class="fas fa-phone-alt text-orange-400"></i>
                <span class="font-medium">Need help? Call us</span>
            </a>
        </div>
    </div>

    <script>
        document.getElementById('togglePassword').addEventListener('click', togglePassword);

        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('loginForm');
            const submitBtn = form.querySelector('button[type="submit"]');
            const buttonText = submitBtn.querySelector('.button-text');
            const loadingSpinner = submitBtn.querySelector('.loading-spinner');
            
            form.addEventListener('submit', function(e) {
                if (!validateForm()) {
                    e.preventDefault();
                } else {
                    showLoading();
                }
            });
            
            const inputs = form.querySelectorAll('input');
            inputs.forEach(input => {
                input.addEventListener('input', function() {
                    validateField(this);
                });
                
                input.addEventListener('blur', function() {
                    validateField(this);
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
                    errorMessage = 'Password must be at least 8 characters';
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
                errorElement.textContent = message;
                errorElement.classList.add('show');
            }
            
            function hideError(field, errorElement) {
                field.classList.remove('error');
                errorElement.classList.remove('show');
            }
            
            function showLoading() {
                submitBtn.disabled = true;
                buttonText.style.display = 'none';
                loadingSpinner.style.display = 'block';
            }
            
            function isValidEmail(email) {
                const emailPattern = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;
                return emailPattern.test(email);
            }
        });

        function togglePassword() {
    const passwordField = document.getElementById('password');
    const toggleIcon = document.getElementById('togglePassword');
    
    if (passwordField.type === 'password') {
        passwordField.type = 'text';
        toggleIcon.classList.remove('fa-eye');
        toggleIcon.classList.add('fa-eye-slash');
        toggleIcon.setAttribute('title', 'Hide password');
    } else {
        passwordField.type = 'password';
        toggleIcon.classList.remove('fa-eye-slash');
        toggleIcon.classList.add('fa-eye');
        toggleIcon.setAttribute('title', 'Show password');
    }
}

// Add event listener for the toggle
    </script>
</body>
</html>