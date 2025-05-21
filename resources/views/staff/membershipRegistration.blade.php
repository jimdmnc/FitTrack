@extends('layouts.app')

@section('content')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/themes/material_orange.css">
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<style>
    .glass-card {
        background: #1e1e1e;
        backdrop-filter: blur(10px);
        border-radius: 16px;
        box-shadow: 0 8px 32px rgba(31, 38, 135, 0.1);
        transition: all 0.3s ease;
    }
    .glass-card:hover {
        box-shadow: 0 12px 40px rgba(31, 38, 135, 0.15);
        transform: translateY(-5px);
    }
    .gradient-bg {
        background: #1e1e1e;
    }
    input[type="date"]::-webkit-calendar-picker-indicator {
        filter: invert(50%) sepia(90%) saturate(1000%) hue-rotate(330deg) brightness(100%) contrast(100%);
        cursor: pointer;
        padding: 0;
        margin: 0;
    }
    input[type="date"]::-moz-calendar-picker-indicator {
        filter: invert(50%) sepia(90%) saturate(1000%) hue-rotate(330deg) brightness(100%) contrast(100%);
        cursor: pointer;
    }
    input[type="date"]::-ms-clear {
        display: none;
    }
</style>

<div class="py-10 px-4 md:px-10">
    <div class="p-6">
        <div class="flex flex-col md:flex-row justify-between items-center">
            <div>
                <h1 class="text-2xl md:text-4xl pb-1 font-bold bg-clip-text text-transparent bg-gradient-to-r from-red-600 to-orange-600">
                    Membership Registration
                </h1>
                <p class="text-gray-200 mt-2">Complete the form below to register a new gym member</p>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="max-w-4xl mx-auto bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg mb-6 flex justify-between items-center" role="alert" x-data="{ show: true }" x-show="show">
            <div class="flex items-center">
                <svg class="h-5 w-5 text-green-500 mr-2" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                </svg>
                <span>{{ session('success') }} @if(session('generated_password')) Generated password: {{ session('generated_password') }} @endif</span>
            </div>
            <button type="button" @click="show = false" class="text-green-500 hover:text-green-700">
                <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                </svg>
            </button>
        </div>
    @endif

    @if($errors->any() || session('error'))
        <div class="max-w-4xl mx-auto bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg mb-6" role="alert" x-data="{ show: true }" x-show="show">
            <div class="flex justify-between items-center">
                <div class="flex items-center">
                    <svg class="h-5 w-5 text-red-500 mr-2" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                    </svg>
                    <span>
                        @if(session('error'))
                            {{ session('error') }}
                        @else
                            Please fix the following errors:
                        @endif
                    </span>
                </div>
                <button type="button" @click="show = false" class="text-red-500 hover:text-red-700">
                    <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                    </svg>
                </button>
            </div>
            @if($errors->any())
                <ul class="mt-2 list-disc list-inside text-sm">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            @endif
        </div>
    @endif

    <form id="registrationForm" action="{{ route('staff.membershipRegistration') }}" method="POST" class="bg-[#1e1e1e] rounded-xl shadow-lg overflow-hidden">
        @csrf
        <!-- Personal Information Section -->
        <div class="p-6 border-b border-[#121212] bg-gradient-to-br from-[#2c2c2c] to-[#1e1e1e]">
            <h2 class="text-xl font-semibold text-gray-200 flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mr-2 text-orange-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                </svg>
                Personal Information
            </h2>
            <p class="text-sm text-gray-200 mt-1 mb-4">Enter the member's personal details</p>
        </div>

        <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label for="first_name" class="block text-gray-200 font-medium mb-2">First Name <span class="text-red-500">*</span></label>
                <input type="text" id="first_name" name="first_name" class="bg-[#2c2c2c] text-gray-200 border-[#2c2c2c] w-full px-4 py-3 border rounded-lg focus:outline-none focus:ring-2 focus:ring-[#ff5722] focus:border-transparent" value="{{ old('first_name') }}" required aria-describedby="first_name_error">
                @error('first_name')
                    <span id="first_name_error" class="text-red-500 text-sm mt-1 block" aria-live="polite">{{ $message }}</span>
                @enderror
            </div>

            <div>
                <label for="last_name" class="block text-gray-200 font-medium mb-2">Last Name <span class="text-red-500">*</span></label>
                <input type="text" id="last_name" name="last_name" class="bg-[#2c2c2c] text-gray-200 border-[#2c2c2c] w-full px-4 py-3 border rounded-lg focus:outline-none focus:ring-2 focus:ring-[#ff5722] focus:border-transparent" value="{{ old('last_name') }}" required aria-describedby="last_name_error">
                @error('last_name')
                    <span id="last_name_error" class="text-red-500 text-sm mt-1 block" aria-live="polite">{{ $message }}</span>
                @enderror
            </div>

            <div class="relative">
                <label for="birthdate" class="block text-gray-200 font-medium mb-2">Birthdate <span class="text-red-500">*</span></label>
                <div class="relative">
                    <input type="date" id="birthdate" name="birthdate" class="bg-[#2c2c2c] text-gray-200 border-[#2c2c2c] w-full px-4 py-3 border rounded-lg focus:outline-none focus:ring-2 focus:ring-[#ff5722] focus:border-transparent" value="{{ old('birthdate') }}" max="{{ $maxBirthdate }}" required aria-describedby="birthdate_error">
                </div>
                <span class="text-xs text-gray-500 mt-1 block">Must be 16 years or older</span>
                @error('birthdate')
                    <span id="birthdate_error" class="text-red-500 text-sm mt-1 block" aria-live="polite">{{ $message }}</span>
                @enderror
            </div>

            <div>
                <label for="gender" class="block text-gray-200 font-medium mb-2">Gender <span class="text-red-500">*</span></label>
                <select id="gender" name="gender" class="bg-[#2c2c2c] text-gray-200 border-[#2c2c2c] w-full px-4 py-3 border rounded-lg focus:outline-none focus:ring-2 focus:ring-[#ff5722] focus:border-transparent" required aria-describedby="gender_error">
                    <option value="" selected disabled>Select Gender</option>
                    <option value="male" {{ old('gender') == 'male' ? 'selected' : '' }}>Male</option>
                    <option value="female" {{ old('gender') == 'female' ? 'selected' : '' }}>Female</option>
                    <option value="other" {{ old('gender') == 'other' ? 'selected' : '' }}>Other</option>
                </select>
                @error('gender')
                    <span id="gender_error" class="text-red-500 text-sm mt-1 block" aria-live="polite">{{ $message }}</span>
                @enderror
            </div>

            <div>
                <label for="phoneNumber" class="block text-gray-200 font-medium mb-2">Phone Number <span class="text-red-500">*</span></label>
                <div class="relative">
                    <input type="tel" id="phoneNumber" name="phone_number" class="bg-[#2c2c2c] text-gray-200 border-[#2c2c2c] w-full px-4 py-3 border rounded-lg focus:outline-none focus:ring-2 focus:ring-[#ff5722] focus:border-transparent" pattern="\d{11}" maxlength="11" placeholder="11-digit phone number (09*********)" value="{{ old('phone_number') }}" required oninput="this.value = this.value.replace(/\D/g, '')" aria-describedby="phone_number_error">
                    <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-[#ff5722]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                        </svg>
                    </div>
                </div>
                <span class="text-xs text-gray-500 mt-1 block">Format: 11 digits without spaces or dashes</span>
                @error('phone_number')
                    <span id="phone_number_error" class="text-red-500 text-sm mt-1 block" aria-live="polite">{{ $message }}</span>
                @enderror
            </div>

            <div>
                <label for="email" class="block text-gray-200 font-medium mb-2">Email Address <span class="text-red-500">*</span></label>
                <div class="relative">
                    <input type="email" id="email" name="email" class="bg-[#2c2c2c] text-gray-200 border-[#2c2c2c] w-full px-4 py-3 border rounded-lg focus:outline-none focus:ring-2 focus:ring-[#ff5722] focus:border-transparent" placeholder="example@email.com" value="{{ old('email') }}" required aria-describedby="email_error">
                    <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-[#ff5722]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                        </svg>
                    </div>
                </div>
                @error('email')
                    <span id="email_error" class="text-red-500 text-sm mt-1 block" aria-live="polite">{{ $message }}</span>
                @enderror
            </div>
        </div>

        <!-- Membership Section -->
        <div class="p-6 border-t border-b border-[#121212] bg-gradient-to-br from-[#2c2c2c] to-[#1e1e1e]">
            <h2 class="text-xl font-semibold text-gray-200 flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mr-2 text-[#ff5722]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z" />
                </svg>
                Membership Details
            </h2>
            <p class="text-sm text-gray-200 mt-1 mb-4">Select membership type and duration</p>
        </div>

        <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label for="membershipType" class="block text-gray-200 font-medium mb-2">Membership Type <span class="text-red-500">*</span></label>
                <select id="membershipType" name="membership_type" class="bg-[#2c2c2c] text-gray-200 border-[#2c2c2c] w-full px-4 py-3 border rounded-lg focus:outline-none focus:ring-2 focus:ring-[#ff5722] focus:border-transparent" required aria-describedby="membership_type_error">
                    <option value="" selected disabled>Select Membership Type</option>
                    <option value="custom" {{ old('membership_type') == 'custom' ? 'selected' : '' }}>Custom Days (₱{{ number_format($prices['session']->amount ?? 60, 2) }}/day)</option>
                    <option value="7" {{ old('membership_type') == '7' ? 'selected' : '' }}>Week (7 days, ₱{{ number_format($prices['weekly']->amount ?? 300, 2) }})</option>
                    <option value="30" {{ old('membership_type') == '30' ? 'selected' : '' }}>Month (30 days, ₱{{ number_format($prices['monthly']->amount ?? 850, 2) }})</option>
                    <option value="365" {{ old('membership_type') == '365' ? 'selected' : '' }}>Annual (365 days, ₱{{ number_format($prices['annual']->amount ?? 10000, 2) }})</option>
                </select>
                @error('membership_type')
                    <span id="membership_type_error" class="text-red-500 text-sm mt-1 block" aria-live="polite">{{ $message }}</span>
                @enderror
            </div>

            <div>
                <label for="payment_amount" class="block text-gray-200 font-medium mb-2">Payment Amount (₱)</label>
                <input type="text" id="payment_amount" name="payment_amount" class="w-full px-4 py-3 bg-[#3A3A3A] text-gray-200 border border-[#2c2c2c] rounded-lg cursor-default pointer-events-none select-none" readonly style="box-shadow: none;">
            </div>

            <div id="customDaysContainer" class="hidden">
                <label for="customDays" class="block text-gray-200 font-medium mb-2">Number of Days <span class="text-red-500">*</span></label>
                <input type="number" id="customDays" name="custom_days" min="1" max="365" class="bg-[#2c2c2c] text-gray-200 border-[#2c2c2c] w-full px-4 py-3 border rounded-lg focus:outline-none focus:ring-2 focus:ring-[#ff5722] focus:border-transparent" value="{{ old('custom_days') }}" aria-describedby="custom_days_error">
                @error('custom_days')
                    <span id="custom_days_error" class="text-red-500 text-sm mt-1 block" aria-live="polite">{{ $message }}</span>
                @enderror
            </div>

            <div class="start-date-container">
                <label for="startDate" class="block text-gray-200 font-medium mb-2">Start Date <span class="text-red-500">*</span></label>
                <div class="relative">
                    <input type="date" id="startDate" name="start_date" class="bg-[#2c2c2c] text-gray-200 border-[#2c2c2c] w-full px-4 py-3 border rounded-lg focus:outline-none focus:ring-2 focus:ring-[#ff5722] focus:border-transparent" value="{{ old('start_date') ?? date('Y-m-d') }}" required aria-describedby="start_date_error">
                </div>
                @error('start_date')
                    <span id="start_date_error" class="text-red-500 text-sm mt-1 block" aria-live="polite">{{ $message }}</span>
                @enderror
            </div>

            <div class="end-date-container">
                <label for="endDate" class="block text-gray-200 font-medium mb-2">Expiration Date</label>
                <div class="relative">
                    <input type="text" id="endDate" name="expiry_date" placeholder="Calculated automatically" class="bg-[#3A3A3A] text-gray-200 border-[#2c2c2c] w-full px-4 py-3 border rounded-lg cursor-default pointer-events-none select-none" readonly>
                    <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-[#ff5722]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                    </div>
                </div>
                <span class="text-xs text-gray-500 mt-1 block">Auto-calculated based on membership type</span>
            </div>

            <div class="rfid-container">
                <label for="uid" class="block text-gray-200 font-medium mb-2">RFID Card <span class="text-red-500">*</span></label>
                <div class="relative">
                    <input id="uid" name="uid" class="bg-[#3A3A3A] text-gray-200 border-[#2c2c2c] w-full pr-12 py-4 border rounded-lg cursor-default pointer-events-none select-none focus:ring-2 focus:ring-[#ff5722] focus:border-transparent transition-all" placeholder="Waiting for card tap..." readonly aria-describedby="uid_error">
                    <div class="absolute inset-y-0 right-3 flex items-center">
                        <div id="rfid-loading" class="animate-pulse flex items-center">
                            <span class="h-2 w-2 bg-[#ff5722] rounded-full mr-1"></span>
                            <span class="h-2 w-2 bg-[#ff5722] rounded-full mr-1 animate-pulse delay-100"></span>
                            <span class="h-2 w-2 bg-[#ff5722] rounded-full animate-pulse delay-200"></span>
                        </div>
                        <button id="clearRfidBtn" type="button" onclick="clearRfid()" class="ml-2 bg-red-600 text-white rounded-full w-6 h-6 flex items-center justify-center hover:bg-red-500 focus:outline-none focus:ring-2 focus:ring-red-500 transition-colors hidden" aria-label="Clear RFID input">
                            ×
                        </button>
                    </div>
                </div>
                <div id="rfid_status" class="mt-2 text-sm text-gray-500 flex items-center" aria-live="polite">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1 animate-pulse" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                    </svg>
                    Please Tap Your Card...
                </div>
                @error('uid')
                    <span id="uid_error" class="text-red-500 text-sm mt-1 block" aria-live="polite">{{ $message }}</span>
                @enderror
            </div>
        </div>

        <!-- Account Section -->
        <div class="p-6 border-t border-[#121212] bg-gradient-to-br from-[#2c2c2c] to-[#1e1e1e]">
            <h2 class="text-xl font-semibold text-gray-200 flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mr-2 text-[#ff5722]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                </svg>
                Account Setup
            </h2>
            <p class="text-sm text-gray-300 mt-1 mb-4">Auto-generated password details</p>
        </div>

        <div class="p-6">
            <div class="bg-[#ff5722] bg-opacity-10 p-4 rounded-lg border border-[#ff5722] mb-6">
                <div class="flex">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-[#ff5722] mr-2 flex-shrink-0" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                    </svg>
                    <div>
                        <h3 class="text-sm font-medium text-gray-300">Password Information</h3>
                        <p class="text-sm text-gray-300 mt-1">A password will be automatically generated based on the member's last name and birth date.</p>
                    </div>
                </div>
            </div>

            <div class="bg-[#1e1e1e] p-4 rounded-lg border border-[#121212] mb-6">
                <div class="flex justify-between items-center">
                    <label for="password" class="block text-gray-200 font-medium">Generated Password</label>
                    <span class="text-xs text-gray-300">Will be shown to the member</span>
                </div>
                <div class="relative mt-2">
                    <input type="text" id="password" class="bg-[#2c2c2c] text-gray-200 border-[#2c2c2c] w-full px-4 py-3 border rounded-lg cursor-default pointer-events-none select-none" readonly aria-live="polite" value="{{ old('generated_password') ?? session('generated_password') ?? '' }}">
                    <input type="hidden" id="generated_password" name="generated_password" value="{{ old('generated_password') ?? session('generated_password') ?? '' }}">
                    <div class="absolute inset-y-0 right-0 pr-3 flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-[#ff5722]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z" />
                        </svg>
                    </div>
                </div>
                @error('generated_password')
                    <span id="generated_password_error" class="text-red-500 text-sm mt-1 block" aria-live="polite">{{ $message }}</span>
                @enderror
            </div>
        </div>

        <!-- Submit Button -->
        <div class="p-6 bg-gradient-to-br from-[#2c2c2c] to-[#1e1e1e] border-[#121212] shadow-lg">
            <div class="flex flex-col md:flex-row space-y-4 md:space-y-0 md:space-x-4">
                <button type="button" class="w-full md:w-1/4 bg-gray-700 text-gray-200 py-3 px-6 rounded-xl transition duration-300 ease-in-out transform hover:translate-y-[-2px] hover:bg-gray-600 focus:outline-none focus:ring-4 focus:ring-gray-500 focus:ring-opacity-50 shadow-full">
                    <span class="flex items-center justify-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6l1-1M5 7h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v0a2 2 0 002 2z" />
                        </svg>
                        Clear Form
                    </span>
                </button>
                <button type="submit" class="w-full md:w-3/4 bg-[#ff5722] text-white py-3 px-6 rounded-xl transition duration-300 ease-in-out transform hover:translate-y-[-2px] hover:bg-opacity-80 focus:outline-none focus:ring-4 focus:ring-[#ff5722] focus:ring-opacity-50 shadow-lg flex items-center justify-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                    </svg>
                    Register New Member
                </button>
            </div>
        </div>
    </form>
</div>

<script>
document.addEventListener("DOMContentLoaded", function() {
    // Configuration constants
    const RFID_POLL_INTERVAL = 2000; // ms
    const FETCH_TIMEOUT = 5000; // ms
    const MAX_RETRIES = 3;
    let isFetching = false;
    let rfidPollInterval = null;

    // Birthdate Validation
    const birthdateInput = document.getElementById("birthdate");
    const maxBirthdate = "{{ $maxBirthdate }}";
    const today = "{{ $today }}";

    function validateBirthdate() {
        if (!birthdateInput) return;

        birthdateInput.setAttribute("max", maxBirthdate);

        birthdateInput.addEventListener("input", function() {
            const selectedDate = new Date(this.value);
            const maxDate = new Date(maxBirthdate);
            const todayDate = new Date(today);

            if (isNaN(selectedDate)) {
                this.setCustomValidity("Invalid date format");
                this.reportValidity();
                return;
            }

            if (selectedDate > maxDate) {
                this.setCustomValidity("Member must be at least 16 years old.");
                this.reportValidity();
            } else if (selectedDate >= todayDate) {
                this.setCustomValidity("Birthdate cannot be today or in the future.");
                this.reportValidity();
            } else {
                this.setCustomValidity("");
            }
            updatePassword();
        });
    }

    // Payment Amount Calculation
    const membershipType = document.getElementById("membershipType");
    const customDaysContainer = document.getElementById("customDaysContainer");
    const customDaysInput = document.getElementById("customDays");
    const paymentAmount = document.getElementById("payment_amount");

    const paymentRates = {
        "7": {{ $prices['weekly']->amount ?? 300 }},
        "30": {{ $prices['monthly']->amount ?? 850 }},
        "365": {{ $prices['annual']->amount ?? 10000 }},
        "custom": {{ $prices['session']->amount ?? 60 }}
    };

    function updatePaymentAmount() {
        if (!paymentAmount || !membershipType) return;

        if (membershipType.value === 'custom' && customDaysInput?.value) {
            const days = parseInt(customDaysInput.value) || 0;
            paymentAmount.value = (days > 0 ? days * paymentRates['custom'] : 0).toFixed(2);
        } else {
            paymentAmount.value = (paymentRates[membershipType.value] || 0).toFixed(2);
        }
    }

    function toggleCustomDays() {
        if (!membershipType || !customDaysContainer || !customDaysInput) return;

        if (membershipType.value === 'custom') {
            customDaysContainer.classList.remove('hidden');
            customDaysInput.setAttribute('required', 'required');
        } else {
            customDaysContainer.classList.add('hidden');
            customDaysInput.removeAttribute('required');
            customDaysInput.value = '';
        }
        updatePaymentAmount();
        updateEndDate();
    }

    function setupMembershipHandlers() {
        if (membershipType && paymentAmount) {
            membershipType.addEventListener("change", toggleCustomDays);
            if (customDaysInput) {
                customDaysInput.addEventListener("input", function() {
                    const value = parseInt(this.value) || 1;
                    this.value = Math.min(Math.max(value, 1), 365);
                    updatePaymentAmount();
                    updateEndDate();
                });
            }
            toggleCustomDays();
        }
    }

    // Expiry Date Calculation
    function updateEndDate() {
        const startDateInput = document.getElementById('startDate');
        const endDateInput = document.getElementById('endDate');

        if (!startDateInput || !endDateInput || !membershipType || !startDateInput.value) {
            if (endDateInput) endDateInput.value = '';
            return;
        }

        const startDate = new Date(startDateInput.value);
        if (isNaN(startDate)) {
            endDateInput.value = '';
            return;
        }

        let duration = membershipType.value === 'custom' && customDaysInput?.value 
            ? parseInt(customDaysInput.value) 
            : parseInt(membershipType.value);

        if (!isNaN(duration) && duration > 0) {
            startDate.setDate(startDate.getDate() + duration - 1);
            const day = String(startDate.getDate()).padStart(2, '0');
            const month = String(startDate.getMonth() + 1).padStart(2, '0');
            const year = startDate.getFullYear();
            endDateInput.value = `${day}/${month}/${year}`;
        } else {
            endDateInput.value = '';
        }
    }

    // Password Generation
    function updatePassword() {
        const lastNameInput = document.getElementById("last_name");
        const birthdateInput = document.getElementById("birthdate");
        const passwordField = document.getElementById("password");
        const hiddenPasswordField = document.getElementById("generated_password");

        if (!lastNameInput || !birthdateInput || !passwordField || !hiddenPasswordField) {
            return;
        }

        const lastNameValue = lastNameInput.value.trim();
        const birthdateValue = birthdateInput.value;

        if (!lastNameValue || !birthdateValue) {
            passwordField.value = '';
            hiddenPasswordField.value = '';
            passwordField.setAttribute('aria-label', 'Generated password is empty');
            return;
        }

        const sanitizedLastName = lastNameValue.replace(/[^a-zA-Z0-9]/g, '').toLowerCase();
        if (!sanitizedLastName) {
            passwordField.value = '';
            hiddenPasswordField.value = '';
            passwordField.setAttribute('aria-label', 'Generated password is empty due to invalid last name');
            return;
        }

        const selectedDate = new Date(birthdateValue);
        const maxDate = new Date(maxBirthdate);
        const todayDate = new Date(today);

        if (isNaN(selectedDate) || selectedDate > maxDate || selectedDate >= todayDate) {
            passwordField.value = '';
            hiddenPasswordField.value = '';
            passwordField.setAttribute('aria-label', 'Generated password is empty due to invalid birthdate');
            return;
        }

        const month = String(selectedDate.getMonth() + 1).padStart(2, '0');
        const day = String(selectedDate.getDate()).padStart(2, '0');
        const year = String(selectedDate.getFullYear());
        const generatedPassword = `${sanitizedLastName}${month}${day}${year}`;
        
        passwordField.value = generatedPassword;
        hiddenPasswordField.value = generatedPassword;
        passwordField.setAttribute('aria-label', `Generated password is ${generatedPassword}`);
    }

    // Form Handling
    function setupFormHandlers() {
        const form = document.getElementById('registrationForm');
        if (!form) return;

        const clearButton = form.querySelector('button[type="button"]');
        if (clearButton) {
            clearButton.addEventListener('click', function() {
                if (confirm('Are you sure you want to clear the form?')) {
                    form.reset();
                    ['endDate', 'password', 'generated_password'].forEach(id => {
                        const el = document.getElementById(id);
                        if (el) el.value = '';
                    });
                    updateRfidStatus('waiting', 'Please Tap Your Card...');
                    updatePaymentAmount();
                    updateEndDate();
                    toggleCustomDays();
                    toggleClearButton();
                }
            });
        }

        const lastNameInput = document.getElementById("last_name");
        const birthdateInput = document.getElementById("birthdate");
        const startDateEl = document.getElementById('startDate');
        const membershipTypeEl = document.getElementById('membershipType');

        if (lastNameInput) lastNameInput.addEventListener("input", updatePassword);
        if (birthdateInput) birthdateInput.addEventListener("input", updatePassword);
        if (startDateEl) startDateEl.addEventListener('change', updateEndDate);
        if (membershipTypeEl) membershipTypeEl.addEventListener('change', updateEndDate);

        updatePassword();
        updateEndDate();

        form.addEventListener('submit', function(e) {
            if (membershipType?.value === 'custom' && (!customDaysInput?.value || parseInt(customDaysInput.value) <= 0)) {
                e.preventDefault();
                alert('Please enter a valid number of days for custom membership');
                return;
            }

            const submitBtn = form.querySelector('button[type="submit"]');
            if (submitBtn) {
                submitBtn.disabled = true;
                submitBtn.innerHTML = `
                    <svg class="animate-spin -ml-1 mr-2 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    Processing...
                `;
            }
        });
    }

    // RFID Handling
    function updateRfidStatus(type, message) {
        const rfidStatus = document.getElementById('rfid_status');
        if (!rfidStatus) return;

        const icons = {
            success: `<svg class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>`,
            waiting: `<svg class="h-4 w-4 mr-1 animate-pulse" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
            </svg>`,
            error: `<svg class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
            </svg>`
        };

        const colors = {
            success: 'text-green-500',
            waiting: 'text-blue-500',
            error: 'text-red-500'
        };

        rfidStatus.innerHTML = `${icons[type] || ''} ${message}`;
        rfidStatus.className = `mt-2 text-sm ${colors[type] || 'text-gray-500'} flex items-center`;
    }

    async function fetchLatestUid(retryCount = 0) {
        if (isFetching) return;
        isFetching = true;

        const controller = new AbortController();
        const timeoutId = setTimeout(() => controller.abort(), FETCH_TIMEOUT);

        try {
            const response = await fetch('/api/rfid/latest', {
                signal: controller.signal,
                headers: {
                    'Accept': 'application/json'
                }
            });

            clearTimeout(timeoutId);

            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }

            const data = await response.json();
            const uidInput = document.getElementById('uid');

            if (data?.uid && uidInput) {
                uidInput.value = data.uid;
                updateRfidStatus('success', 'Card detected');
            } else {
                if (uidInput) uidInput.value = '';
                updateRfidStatus('waiting', 'Please Tap Your Card...');
            }
            toggleClearButton();
        } catch (error) {
            if (error.name === 'AbortError') {
                if (retryCount < MAX_RETRIES) {
                    await new Promise(resolve => setTimeout(resolve, 1000));
                    return fetchLatestUid(retryCount + 1);
                }
                updateRfidStatus('error', 'Request timed out. Please try again.');
            } else {
                console.error('RFID Fetch Error:', error);
                updateRfidStatus('error', 'Failed to fetch RFID. Please try again.');
            }
        } finally {
            isFetching = false;
        }
    }

    function clearRfid() {
    const uidInput = document.getElementById('uid');
    const uid = uidInput.value;

    if (!uid) {
        updateRfidStatus('error', 'No RFID to clear');
        return;
    }

    fetch(`/api/rfid/clear/${uid}`, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Accept': 'application/json',
        },
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            uidInput.value = '';
            updateRfidStatus('success', 'RFID cleared');
        } else {
            updateRfidStatus('error', data.message || 'Failed to clear RFID');
        }
        toggleClearButton();
    })
    .catch(error => {
        console.error(error);
        updateRfidStatus('error', 'Request failed');
    });
}

function toggleClearButton() {
    const uidInput = document.getElementById('uid');
    const clearBtn = document.getElementById('clearRfidBtn');

    if (uidInput && clearBtn) {
        if (uidInput.value.trim() !== '') {
            clearBtn.classList.remove('hidden');
        } else {
            clearBtn.classList.add('hidden');
        }
    }
}

    // Initialize
    function initialize() {
        validateBirthdate();
        setupMembershipHandlers();
        setupFormHandlers();

        // Handle session messages
        @if (session('success'))
            const uidInput = document.getElementById('uid');
            if (uidInput) uidInput.value = '';
            updateRfidStatus('success', 'Registration successful!');
        @endif

        @if (session('error'))
            updateRfidStatus('error', '{{ session('error') }}');
        @endif

        // Start RFID polling
        fetchLatestUid();
        rfidPollInterval = setInterval(fetchLatestUid, RFID_POLL_INTERVAL);

        // Cleanup
        window.addEventListener('beforeunload', () => {
            if (rfidPollInterval) {
                clearInterval(rfidPollInterval);
                rfidPollInterval = null;
            }
        });
    }

    initialize();
});
</script>
@endsection