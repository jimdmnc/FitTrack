<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Register for Session</title>
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
        .readonly-field {
            background-color: #2d2319;
            color: #ff8a65;
            border: 1px solid #3d2e22;
            opacity: 0.9;
            cursor: not-allowed;
        }
        .readonly-field-container {
            position: relative;
        }
        .readonly-icon {
            position: absolute;
            right: 12px;
            top: 50%;
            transform: translateY(-50%);
            color: #ff8a65;
            pointer-events: none;
        }
        input, select, button {
            min-height: 48px;
        }
        @media (min-width: 1024px) {
            input, select, button {
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
        <h2 class="text-2xl md:text-4xl lg:text-5xl pb-1 font-bold bg-clip-text text-transparent bg-gradient-to-r from-red-600 to-orange-600">Session Registration</h2>
    </div>
    <div class="header-bg text-gray-200 p-4 md:p-5 lg:p-6 rounded-t-lg shadow border-b border-black">
        <h2 class="text-xl md:text-2xl lg:text-3xl font-bold text-gray-100">Registration</h2>
    </div>
    
    <div class="regform-bg p-4 md:p-5 lg:p-6 rounded-b-lg shadow">
        @if (session('success'))
            <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-3 mb-4 text-sm">
                <i class="fas fa-check-circle mr-1"></i> {{ session('success') }}
            </div>
        @endif
        
        @if (session('error'))
            <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-3 mb-4 text-sm">
                <i class="fas fa-exclamation-circle mr-1"></i> {{ session('error') }}
            </div>
        @endif
        
        @if ($errors->any())
            <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-3 mb-4 text-sm">
                <i class="fas fa-exclamation-circle mr-1"></i> 
                <strong>Please fix the following errors:</strong>
                <ul class="mt-2 list-disc list-inside">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        
        <form method="POST" action="{{ route('self.registration.store') }}" id="registrationForm" novalidate>
            @csrf
            <div class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="first_name" class="block text-gray-200 font-medium text-sm lg:text-base mb-1 lg:mb-2">
                            First Name <span class="text-red-500">*</span>
                        </label>
                        <input type="text" 
                               class="w-full p-2 md:p-3 lg:p-4 rounded-lg text-base lg:text-lg orange-focus text-gray-200 @error('first_name') error-border @enderror" 
                               id="first_name" 
                               name="first_name" 
                               value="{{ old('first_name') }}"
                               placeholder="Your first name"
                               autocomplete="given-name"
                               required
                               minlength="2"
                               maxlength="50">
                        @error('first_name')
                            <div id="first_name_error" class="error-message">{{ $message }}</div>
                        @else
                            <div id="first_name_error" class="error-message hidden">First name must be between 2-50 characters and contain no numbers or special characters.</div>
                        @enderror
                    </div>
                    <div>
                        <label for="last_name" class="block text-gray-200 font-medium text-sm lg:text-base mb-1 lg:mb-2">
                            Last Name <span class="text-red-500">*</span>
                        </label>
                        <input type="text" 
                               class="w-full p-2 md:p-3 lg:p-4 rounded-lg text-base lg:text-lg orange-focus text-gray-200 @error('last_name') error-border @enderror" 
                               id="last_name" 
                               name="last_name" 
                               value="{{ old('last_name') }}"
                               placeholder="Your last name"
                               autocomplete="family-name"
                               required
                               minlength="2"
                               maxlength="50">
                        @error('last_name')
                            <div id="last_name_error" class="error-message">{{ $message }}</div>
                        @else
                            <div id="last_name_error" class="error-message hidden">Please enter a valid last name (2-50 characters)</div>
                        @enderror
                    </div>
                </div>
                
                <div>
                    <label for="email" class="block text-gray-200 font-medium text-sm lg:text-base mb-1 lg:mb-2">
                        Email Address <span class="text-red-500">*</span>
                    </label>
                    <input type="email" 
                           class="w-full p-2 md:p-3 rounded-lg text-base orange-focus text-gray-200 @error('email') error-border @enderror" 
                           id="email" 
                           name="email" 
                           value="{{ old('email') }}"
                           placeholder="Your email address"
                           required
                           pattern="[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,}$">
                    @error('email')
                        <div id="email_error" class="error-message">{{ $message }}</div>
                    @else
                        <div id="email_error" class="error-message hidden">Please enter a valid email address</div>
                    @enderror
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="gender" class="block text-gray-200 font-medium text-sm lg:text-base mb-1 lg:mb-2">Gender <span class="text-red-500">*</span></label>
                        <select name="gender" id="gender" 
                                class="w-full p-2 md:p-3 lg:p-4 rounded-lg text-base lg:text-lg orange-focus text-gray-200 @error('gender') error-border @enderror"
                                required>
                            <option value="">Select Gender</option>
                            <option value="male" {{ old('gender') == 'male' ? 'selected' : '' }}>Male</option>
                            <option value="female" {{ old('gender') == 'female' ? 'selected' : '' }}>Female</option>
                            <option value="other" {{ old('gender') == 'other' ? 'selected' : '' }}>Other</option>
                        </select>
                        @error('gender')
                            <div id="gender_error" class="error-message">{{ $message }}</div>
                        @else
                            <div id="gender_error" class="error-message hidden">Please select a gender</div>
                        @enderror
                    </div>
                    <div>
                        <label for="phone_number" class="block text-gray-200 font-medium text-sm lg:text-base mb-1 lg:mb-2">
                            Phone Number <span class="text-red-500">*</span>
                        </label>
                        <input type="tel" 
                               class="w-full p-2 md:p-3 rounded-lg text-base orange-focus text-gray-200 @error('phone_number') error-border @enderror" 
                               id="phone_number" 
                               name="phone_number" 
                               value="{{ old('phone_number') }}"
                               placeholder="09*********"
                               autocomplete="tel"
                               maxlength="11"
                               minlength="11"
                               oninput="this.value = this.value.replace(/[^0-9]/g, '')"
                               required>
                        @error('phone_number')
                            <div id="phone_error" class="error-message">{{ $message }}</div>
                        @else
                            <div id="phone_error" class="error-message hidden">Please enter a valid phone number</div>
                        @enderror
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="password" class="block text-gray-200 font-medium text-sm lg:text-base mb-1 lg:mb-2">
                            Password <span class="text-red-500">*</span>
                        </label>
                        <input type="password" 
                               class="w-full p-2 md:p-3 lg:p-4 rounded-lg text-base lg:text-lg orange-focus text-gray-200 @error('password') error-border @enderror" 
                               id="password" 
                               name="password" 
                               placeholder="Enter your password"
                               required
                               minlength="8">
                        @error('password')
                            <div id="password_error" class="error-message">{{ $message }}</div>
                        @else
                            <div id="password_error" class="error-message hidden">Password must be at least 8 characters</div>
                        @enderror
                    </div>
                    <div>
                        <label for="password_confirmation" class="block text-gray-200 font-medium text-sm lg:text-base mb-1 lg:mb-2">
                            Confirm Password <span class="text-red-500">*</span>
                        </label>
                        <input type="password" 
                               class="w-full p-2 md:p-3 lg:p-4 rounded-lg text-base lg:text-lg orange-focus text-gray-200 @error('password_confirmation') error-border @enderror" 
                               id="password_confirmation" 
                               name="password_confirmation" 
                               placeholder="Confirm your password"
                               required
                               minlength="8">
                        @error('password_confirmation')
                            <div id="password_confirmation_error" class="error-message">{{ $message }}</div>
                        @else
                            <div id="password_confirmation_error" class="error-message hidden">Passwords do not match</div>
                        @enderror
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="readonly-field-container">
                        <label for="membership_type" class="block text-gray-200 font-medium text-sm lg:text-base mb-1 lg:mb-2">
                            Membership Type
                        </label>
                        <div class="relative">
                            <select 
                                class="w-full p-2 md:p-3 lg:p-4 rounded-lg text-base lg:text-lg readonly-field" 
                                id="membership_type" 
                                name="membership_type" 
                                style="pointer-events: none; -webkit-appearance: none; -moz-appearance: none; text-indent: 1px; text-overflow: '';"
                                required>
                                <option value="1" selected>Session</option>
                            </select>
                            <i class="fas fa-lock readonly-icon text-sm"></i>
                        </div>
                    </div>
                    <div class="readonly-field-container">
                        <label for="amount" class="block text-gray-200 font-medium text-sm lg:text-base mb-1 lg:mb-2">
                            Amount
                        </label>
                        <div class="relative">
                            <input type="text" 
                                class="w-full p-2 md:p-3 lg:p-4 rounded-lg text-base lg:text-lg readonly-field" 
                                id="amount" 
                                name="amount" 
                                value="{{ $sessionPrice->amount ?? '0' }}"
                                style="pointer-events: none"
                                readonly>
                            <i class="fas fa-lock readonly-icon text-sm"></i>
                        </div>
                    </div>
                </div>
                
                <button type="submit" 
                        class="w-full orange-btn text-gray-200 p-2.5 lg:p-3 rounded-lg font-medium text-lg lg:text-xl mt-4 hover:bg-[#e64a19] focus:outline-none focus:ring-2 focus:ring-[#ff5722] focus:ring-offset-2 transition-colors">
                    Register Now
                </button>
            </div>
        </form>
        
        <p class="text-center text-xs lg:text-sm text-gray-400 mt-4">
            By registering, you agree to our Terms
        </p>
    </div>
    

</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('registrationForm');
    
    // Show server-side errors on page load
    const errorFields = form.querySelectorAll('.error-border');
    errorFields.forEach(field => {
        const errorElement = document.getElementById(`${field.id}_error`);
        if (errorElement && !errorElement.classList.contains('hidden')) {
            errorElement.classList.remove('hidden');
        }
    });
    
    form.addEventListener('submit', function(e) {
        if (!validateForm()) {
            e.preventDefault();
        }
    });
    
    const inputs = form.querySelectorAll('input, select');
    inputs.forEach(input => {
        input.addEventListener('input', function() {
            // Clear server-side error styling when user starts typing
            if (this.classList.contains('error-border')) {
                const errorElement = document.getElementById(`${this.id}_error`);
                if (errorElement && errorElement.textContent) {
                    // Only clear if it's a server-side error (not the default hidden message)
                    if (!errorElement.classList.contains('hidden')) {
                        this.classList.remove('error-border');
                        errorElement.classList.add('hidden');
                    }
                }
            }
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
        const inputs = form.querySelectorAll('input:not([readonly]), select:not([style*="pointer-events: none"])');
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
            case 'first_name':
            case 'last_name':
                if (field.value.trim()) {
                    if (field.value.length < 2 || field.value.length > 50) {
                        showError(field, errorElement, `Must be between 2-50 characters`);
                        return false;
                    }
                    if (/\d/.test(field.value)) {
                        showError(field, errorElement, `Numbers are not allowed`);
                        return false;
                    }
                    if (/[^a-zA-Z\s]/.test(field.value)) {
                        showError(field, errorElement, `Cannot contain special characters`);
                        return false;
                    }
                }
                break;
                
            case 'email':
                if (field.value.trim() && !isValidEmail(field.value)) {
                    showError(field, errorElement, 'Please enter a valid email address');
                    return false;
                }
                break;
                
            case 'phone_number':
                if (field.value.trim()) {
                    if (/[^0-9]/.test(field.value)) {
                        showError(field, errorElement, 'Phone number can only contain digits');
                        return false;
                    }
                    if (!/^09\d{9}$/.test(field.value)) {
                        showError(field, errorElement, 'Please enter a valid 11-digit phone number starting with 09');
                        return false;
                    }
                }
                break;
                
            case 'gender':
                if (!field.value) {
                    showError(field, errorElement, 'Please select your gender');
                    return false;
                }
                break;
                
            case 'password':
                if (field.value.trim()) {
                    if (field.value.length < 8) {
                        showError(field, errorElement, 'Password must be at least 8 characters');
                        return false;
                    }
                }
                break;
                
            case 'password_confirmation':
                if (field.value.trim()) {
                    const password = document.getElementById('password').value;
                    if (field.value !== password) {
                        showError(field, errorElement, 'Passwords do not match');
                        return false;
                    }
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
    
    function isValidPhone(phone) {
        const phonePattern = /^09\d{9}$/;
        return phonePattern.test(phone);
    }

    function focusFirstError() {
        const firstError = document.querySelector('.error-border');
        if (firstError) {
            firstError.focus();
        }
    }
});
</script>
</body>
</html>