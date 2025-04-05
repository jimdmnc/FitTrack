<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Register for Session</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
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
    </style>
</head>
<body class="body-bg">
<div class="p-4">
    <div class="my-4">
        <h2 class="text-2xl md:text-4xl pb-1 font-bold bg-clip-text text-transparent bg-gradient-to-r from-red-600 to-orange-600">Session Registration</h2>
    </div>
    <!-- Header with gradient -->
    <div class="header-bg text-white p-5 rounded-t-lg shadow border-b border-black">
        <h2 class="text-xl font-bold text-gray-100">Registration</h2>
    </div>
    
    <!-- Registration Form -->
    <div class="regform-bg p-5 rounded-b-lg shadow">
        <!-- Success and Error messages -->
        @if (session('success'))
            <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-3 mb-4 text-sm">
                <i class="fas fa-check-circle mr-1"></i> {{ session('success') }}
            </div>
        @elseif(session('error'))
            <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-3 mb-4 text-sm">
                <i class="fas fa-exclamation-circle mr-1"></i> {{ session('error') }}
            </div>
        @endif
        
        <form method="POST" action="{{ route('self.registration.store') }}" id="registrationForm" novalidate>
            @csrf
            <div class="space-y-4">
                <!-- First Name -->
                <div>
                    <label for="first_name" class="block text-gray-200 font-medium text-sm mb-1">
                        First Name <span class="text-red-500">*</span>
                    </label>
                    <input type="text" 
                           class="w-full p-3 rounded-lg text-base orange-focus text-gray-200" 
                           id="first_name" 
                           name="first_name" 
                           placeholder="Your first name"
                           autocomplete="given-name"
                           required
                           minlength="2"
                           maxlength="50">
                    <div id="first_name_error" class="error-message hidden">Please enter a valid first name (2-50 characters)</div>
                </div>
                
                <!-- Last Name -->
                <div>
                    <label for="last_name" class="block text-gray-200 font-medium text-sm mb-1">
                        Last Name <span class="text-red-500">*</span>
                    </label>
                    <input type="text" 
                           class="w-full p-3 rounded-lg text-base orange-focus text-gray-200" 
                           id="last_name" 
                           name="last_name" 
                           placeholder="Your last name"
                           autocomplete="family-name"
                           required
                           minlength="2"
                           maxlength="50">
                    <div id="last_name_error" class="error-message hidden">Please enter a valid last name (2-50 characters)</div>
                </div>
                
                <!-- Email -->
                <div>
                    <label for="email" class="block text-gray-200 font-medium text-sm mb-1">
                        Email Address <span class="text-red-500">*</span>
                    </label>
                    <input type="email" 
                           class="w-full p-3 rounded-lg text-base orange-focus text-gray-200" 
                           id="email" 
                           name="email" 
                           placeholder="Your email address"
                           required
                           pattern="[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,}$">
                    <div id="email_error" class="error-message hidden">Please enter a valid email address</div>
                </div>

                <!-- Gender -->
                <div>
                    <label for="gender" class="block text-gray-200 font-medium text-sm mb-1">Gender <span class="text-red-500">*</span></label>
                    <select name="gender" id="gender" 
                            class="w-full p-3 rounded-lg text-base orange-focus text-gray-200"
                            required>
                        <option value="">Select Gender</option>
                        <option value="male">Male</option>
                        <option value="female">Female</option>
                        <option value="other">Other</option>
                    </select>
                    <div id="gender_error" class="error-message hidden">Please select a gender</div>
                </div>

                <!-- Phone Number -->
                <div>
                    <label for="phone_number" class="block text-gray-200 font-medium text-sm mb-1">
                        Phone Number <span class="text-red-500">*</span>
                    </label>
                    <input type="tel" 
                           class="w-full p-3 rounded-lg text-base orange-focus text-gray-200" 
                           id="phone_number" 
                           name="phone_number" 
                           placeholder="09*********"
                           autocomplete="tel"
                           maxlength="11"
                           minlength="11"
                           pattern="^[\+]?[(]?[0-9]{3}[)]?[-\s\.]?[0-9]{3}[-\s\.]?[0-9]{4,6}$"
                           inputmode="tel" 
                           required>
                    <div id="phone_error" class="error-message hidden">Please enter a valid phone number</div>
                </div>
                
                <!-- Membership Type (Read-Only) -->
                <div>
                    <label for="membership_type" class="block text-gray-200 font-medium text-sm mb-1">
                        Membership Type
                    </label>
                    <select 
                        class="w-full p-3 rounded-lg text-base bg-gray-200 text-gray-200 orange-focus" 
                        id="membership_type" 
                        name="membership_type" 
                        style="pointer-events: none;"
                        required>
                        <option value="1" selected>Session</option>
                    </select>
                </div>
                
                <!-- Amount (Read-Only) -->
                <div>
                    <label for="amount" class="block text-gray-200 font-medium text-sm mb-1">
                        Amount
                    </label>
                    <input type="text" 
                           class="w-full p-3 rounded-lg text-base bg-gray-200 text-gray-200 orange-focus" 
                           id="amount" 
                           name="amount" 
                           value="60" 
                           readonly>
                </div>
                
                <!-- Submit Button -->
                <button type="submit" 
                        class="w-full orange-btn text-white p-2.5 rounded-lg font-medium text-lg mt-2 hover:bg-[#e64a19] focus:outline-none focus:ring-2 focus:ring-[#ff5722] focus:ring-offset-2 transition-colors">
                    Register Now
                </button>
            </div>
        </form>
        
        <!-- Bottom messaging -->
        <p class="text-center text-xs text-gray-400 mt-4">
            By registering, you agree to our Terms
        </p>
    </div>
    
    <!-- Help link -->
    <div class="mt-4 text-center">
        <a href="tel:+18005551234" class="text-sm font-medium flex items-center justify-center text-gray-200">
            <i class="orange-text fas fa-phone-alt mr-2"></i> Need help? Call us
        </a>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('registrationForm');
    
    // Validate on submit
    form.addEventListener('submit', function(e) {
        if (!validateForm()) {
            e.preventDefault();
        }
    });
    
    // Validate on input change
    const inputs = form.querySelectorAll('input, select');
    inputs.forEach(input => {
        input.addEventListener('input', function() {
            validateField(this);
        });
        input.addEventListener('blur', function() {
            validateField(this);
        });
    });
    
    function validateForm() {
        let isValid = true;
        const inputs = form.querySelectorAll('input, select');
        
        inputs.forEach(input => {
            if (!validateField(input)) {
                isValid = false;
            }
        });
        
        return isValid;
    }
    
    function validateField(field) {
        const errorElement = document.getElementById(`${field.id}_error`);
        
        if (field.required && !field.value.trim()) {
            showError(field, errorElement, `${field.labels[0].textContent.trim()} is required`);
            return false;
        }
        
        if (field.type === 'email' && !isValidEmail(field.value)) {
            showError(field, errorElement, 'Please enter a valid email address');
            return false;
        }
        
        if (field.id === 'phone_number' && !isValidPhone(field.value)) {
            showError(field, errorElement, 'Please enter a valid phone number');
            return false;
        }
        
        if (field.hasAttribute('minlength') && field.value.length < field.minLength) {
            showError(field, errorElement, `Must be at least ${field.minLength} characters`);
            return false;
        }
        
        if (field.hasAttribute('maxlength') && field.value.length > field.maxLength) {
            showError(field, errorElement, `Cannot exceed ${field.maxLength} characters`);
            return false;
        }
        
        if (field.hasAttribute('pattern') && !new RegExp(field.pattern).test(field.value)) {
            showError(field, errorElement, 'Invalid format');
            return false;
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
        return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
    }
    
    function isValidPhone(phone) {
        return /^[\+]?[(]?[0-9]{3}[)]?[-\s\.]?[0-9]{3}[-\s\.]?[0-9]{4,6}$/.test(phone);
    }
});
</script>
</body>
</html>