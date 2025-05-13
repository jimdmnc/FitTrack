<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Login</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <link rel="icon" type="image/png" sizes="180x180" href="{{ asset('images/rockiesLogo.png') }}">
    <style>
        .header-bg {
            background: linear-gradient(to right, #2c2c2c, #1e1e1e);
        }
        .orange-btn {
            background-color: #ff5722;
        }
        .orange-btn:hover {
            background-color: #e64a19;
        }
        .orange-text {
            color: #ff5722;
        }
        .orange-focus {
            background-color: #2c2c2c;
        }
        .orange-focus:focus {
            border-color: #ff5722;
            outline: #ff5722;
        }
        .regform-bg {
            background: #1e1e1e;
        }
        .body-bg {
            background: #121212;
        }
        .error-message {
            color: #ef4444;
            font-size: 0.875rem;
            margin-top: 0.25rem;
        }
        .error-border {
            border-color: #ef4444;
        }
        .form-container {
            max-width: 100%;
            margin: 0 auto;
        }
        @media (min-width: 640px) {
            .form-container {
                max-width: 540px;
            }
        }
        @media (min-width: 768px) {
            .form-container {
                max-width: 640px;
            }
        }
        @media (min-width: 1024px) {
            .form-container {
                max-width: 768px;
            }
        }
        input, button {
            min-height: 48px;
        }
        @media (min-width: 1024px) {
            input, button {
                min-height: 56px;
            }
        }
        @media (max-width: 640px) {
            .p-5 {
                padding: 1rem;
            }
            .p-3 {
                padding: 0.75rem;
            }
        }
    </style>
</head>
<body class="body-bg min-h-screen flex flex-col">
<div class="p-4 w-full max-w-md lg:max-w-lg mx-auto">
    <div class="my-4 text-center">
        <h2 class="text-2xl md:text-4xl lg:text-5xl pb-1 font-bold bg-clip-text text-transparent bg-gradient-to-r from-red-600 to-orange-600">Login</h2>
    </div>
    <div class="header-bg text-gray-200 p-4 md:p-5 lg:p-6 rounded-t-lg shadow border-b border-black">
        <h2 class="text-xl md:text-2xl lg:text-3xl font-bold text-gray-100">User Login</h2>
    </div>
    
    <div class="regform-bg p-4 md:p-5 lg:p-6 rounded-b-lg shadow">
        @if (session('success'))
            <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-3 mb-4 text-sm">
                <i class="fas fa-check-circle mr-1"></i> {{ session('success') }}
            </div>
        @elseif(session('error'))
            <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-3 mb-4 text-sm">
                <i class="fas fa-exclamation-circle mr-1"></i> {{ session('error') }}
            </div>
        @endif
        
        <form method="POST" action="{{ route('self.login.submit') }}" id="loginForm" novalidate>
            @csrf
            <div class="space-y-4">
                <div>
                    <label for="email" class="block text-gray-200 font-medium text-sm lg:text-base mb-1 lg:mb-2">
                        Email Address <span class="text-red-500">*</span>
                    </label>
                    <input type="email" 
                           class="w-full p-2 md:p-3 rounded-lg text-base orange-focus text-gray-200" 
                           id="email" 
                           name="email" 
                           placeholder="Your email address"
                           required
                           pattern="[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,}$"
                           value="{{ old('email') }}">
                    <div id="email_error" class="error-message hidden">Please enter a valid email address</div>
                </div>

                <div>
                    <label for="password" class="block text-gray-200 font-medium text-sm lg:text-base mb-1 lg:mb-2">
                        Password <span class="text-red-500">*</span>
                    </label>
                    <input type="password" 
                           class="w-full p-2 md:p-3 rounded-lg text-base orange-focus text-gray-200" 
                           id="password" 
                           name="password" 
                           placeholder="Enter your password"
                           required
                           minlength="8">
                    <div id="password_error" class="error-message hidden">Password must be at least 8 characters</div>
                </div>
                
                <button type="submit" 
                        class="w-full orange-btn text-gray-200 p-2.5 lg:p-3 rounded-lg font-medium text-lg lg:text-xl mt-4 hover:bg-[#e64a19] focus:outline-none focus:ring-2 focus:ring-[#ff5722] focus:ring-offset-2 transition-colors">
                    Login
                </button>
            </div>
        </form>
        
        <p class="text-center text-xs lg:text-sm text-gray-400 mt-4">
            Don't have an account? <a href="{{ route('self.registration') }}" class="orange-text hover:underline">Register here</a>
        </p>
    </div>
    
    <div class="mt-4 text-center">
        <a href="tel:+18005551234" class="text-sm lg:text-base font-medium flex items-center justify-center text-gray-200">
            <i class="orange-text fas fa-phone-alt mr-2"></i> Need help? Call us
        </a>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('loginForm');
    
    form.addEventListener('submit', function(e) {
        if (!validateForm()) {
            e.preventDefault();
        }
    });
    
    const inputs = form.querySelectorAll('input');
    inputs.forEach(input => {
        input.addEventListener('input', function() {
            validateField(this);
        });
        input.addEventListener('keyup', function() {
            validateField(this);
        });
        input.addEventListener('blur', function() {
            validateField(this);
        });
    });

    function validateForm() {
        let isValid = true;
        const inputs = form.querySelectorAll('input');
        inputs.forEach(input => {
            if (!validateField(input)) {
                isValid = false;
            }
        });
        
        document.querySelector('button[type="submit"]').disabled = !isValid;
        return isValid;
    }

    function validateField(field) {
        const errorElement = document.getElementById(`${field.id}_error`);
        if (!errorElement) return true;
        
        if (field.required && !field.value.trim()) {
            showError(field, errorElement, `Please enter your ${field.labels[0].textContent.replace('*', '').trim().toLowerCase()}`);
            return false;
        }
        
        switch(field.id) {
            case 'email':
                if (field.value.trim() && !isValidEmail(field.value)) {
                    showError(field, errorElement, 'Please enter a valid email address');
                    return false;
                }
                break;
                
            case 'password':
                if (field.value.trim() && field.value.length < 8) {
                    showError(field, errorElement, 'Password must be at least 8 characters');
                    return false;
                }
                break;
        }
        
        hideError(field, errorElement);
        return true;
    }
    
    function showError(field, errorElement, message) {
        field.classList.add('error-border');
        errorElement.textContent = message;
        errorElement.classList.remove('hidden');
    }
    
    function hideError(field, errorElement) {
        field.classList.remove('error-border');
        errorElement.classList.add('hidden');
    }
    
    function isValidEmail(email) {
        const emailPattern = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;
        return emailPattern.test(email);
    }
});
</script>
</body>
</html>