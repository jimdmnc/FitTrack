<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Register for Session</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <style>
        input, select, textarea, button {
            font-size: 16px; /* Prevents zoom on iOS */
        }
    </style>
</head>
<body class="bg-gray-100">
<div class="p-4">
    <!-- Simple header -->
    <div class="bg-blue-600 text-white p-4 rounded-t-lg">
        <h2 class="text-xl font-bold text-center">Quick Registration</h2>
    </div>
    
    <!-- Registration Form -->
    <div class="bg-white p-5 rounded-b-lg shadow">
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
        
        <form method="POST" action="{{ route('self.registration.store') }}">
            @csrf
            <div class="space-y-4">
                <!-- First Name -->
                <div>
                    <label for="first_name" class="block text-gray-700 font-medium text-sm mb-1">
                        First Name <span class="text-red-500">*</span>
                    </label>
                    <input type="text" 
                           class="w-full p-4 border border-gray-300 rounded-lg text-base" 
                           id="first_name" 
                           name="first_name" 
                           placeholder="Your first name"
                           autocomplete="given-name"
                           required>
                </div>
                
                <!-- Last Name -->
                <div>
                    <label for="last_name" class="block text-gray-700 font-medium text-sm mb-1">
                        Last Name <span class="text-red-500">*</span>
                    </label>
                    <input type="text" 
                           class="w-full p-4 border border-gray-300 rounded-lg text-base" 
                           id="last_name" 
                           name="last_name" 
                           placeholder="Your last name"
                           autocomplete="family-name"
                           required>
                </div>
                <!-- Email -->
            <div>
                <label for="email" class="block text-gray-700 font-medium text-sm mb-1">
                    Email Address <span class="text-red-500">*</span>
                </label>
                <input type="email" 
                    class="w-full p-4 border border-gray-300 rounded-lg text-base" 
                    id="email" 
                    name="email" 
                    placeholder="Your email address"
                    required>
            </div>

                <div>
                    <label for="gender" class="block text-gray-700 font-medium text-sm mb-1">Gender</label>
                    <select name="gender" id="gender" class="w-full p-4 border border-gray-300 rounded-lg text-base">
                        <option value="male">Male</option>
                        <option value="female">Female</option>
                        <option value="other">Other</option>
                    </select>
                </div>

                <!-- Phone Number with mobile-specific keyboard -->
                <div>
                    <label for="phone_number" class="block text-gray-700 font-medium text-sm mb-1">
                        Phone Number <span class="text-red-500">*</span>
                    </label>
                    <input type="tel" 
                           class="w-full p-4 border border-gray-300 rounded-lg text-base" 
                           id="phone_number" 
                           name="phone_number" 
                           placeholder="(123) 456-7890"
                           autocomplete="tel"
                           pattern="[0-9() -]+" 
                           inputmode="tel" 
                           required>
                </div>
                
                <!-- Membership Type (Read-Only) -->
                <div>
                    <label for="membership_type" class="block text-gray-700 font-medium text-sm mb-1">
                        Membership Type
                    </label>
                    <select 
                        class="w-full p-4 border border-gray-300 rounded-lg text-base bg-gray-200 text-gray-600" 
                        id="membership_type" 
                        name="membership_type" 
                        style="pointer-events: none;"
                    >
                        <option value="1" selected>Session</option>  <!-- Displayed word with value=1 -->
                    </select>



                </div>
                
                <!-- Amount (Read-Only) -->
                <div>
                    <label for="amount" class="block text-gray-700 font-medium text-sm mb-1">
                        Amount
                    </label>
                    <input type="text" 
                           class="w-full p-4 border border-gray-300 rounded-lg text-base bg-gray-200 text-gray-600" 
                           id="amount" 
                           name="amount" 
                           value="60" 
                           readonly>
                </div>
                
                <!-- Large, touch-friendly button -->
                <button type="submit" 
                        class="w-full bg-blue-600 text-white p-5 rounded-lg font-medium text-lg mt-2 active:bg-blue-800 focus:outline-none">
                    Register Now
                </button>
            </div>
        </form>
        
        <!-- Bottom messaging -->
        <p class="text-center text-xs text-gray-500 mt-4">
            By registering, you agree to our Terms
        </p>
    </div>
    
    <!-- Help link -->
    <div class="mt-4 text-center">
        <a href="tel:+18005551234" class="text-blue-600 text-sm font-medium flex items-center justify-center">
            <i class="fas fa-phone-alt mr-2"></i> Need help? Call us
        </a>
    </div>
</div>
</body>
</html>
