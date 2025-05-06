@extends('layouts.app')

@section('content')

<div class="container mx-auto px-4 py-12 bg-[#121212] min-h-screen">
    <div class="mb-8">
        <h2 class="text-4xl font-bold bg-clip-text text-transparent bg-gradient-to-r from-red-600 to-orange-600 tracking-tight">Profile Overview</h2>
        <p class="text-gray-400 mt-2">Manage your personal information and security settings</p>
    </div>

    <!-- Consolidated Alerts Section -->
    @if (session('status') == 'verification-link-sent' || !$user->hasVerifiedEmail())
        <div class="mb-6 p-4 bg-green-600/20 border border-green-600/30 rounded-xl text-green-500">
            <div class="flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span>{{ __('A verification link has been sent to your email address. Please verify it before logging in.') }}</span>
            </div>
        </div>
    @endif
    @if (session('status') == 'profile-updated')
        <div class="mb-6 p-4 bg-green-600/20 border border-green-600/30 rounded-xl text-green-500">
            <div class="flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
                <span>{{ __('Your profile has been updated successfully!') }}</span>
            </div>
        </div>
    @endif

    <div class="max-w-6xl mx-auto space-y-8">
        <!-- Profile Card -->
        <div class="bg-[#1e1e1e] rounded-2xl shadow-xl overflow-hidden transition-transform hover:shadow-2xl">
            <!-- Profile Header -->
            <div class="bg-gradient-to-r from-[#2c2c2c] to-[#1e1e1e] p-6 md:p-8">
                <div class="flex flex-col md:flex-row justify-between items-start md:items-center space-y-4 md:space-y-0">
                    <div class="flex items-center space-x-4">
                        <!-- User Avatar with improved styling -->
                        <div class="h-16 w-16 rounded-full bg-gradient-to-br from-orange-500 to-red-600 flex items-center justify-center text-gray-200 text-2xl font-bold shadow-lg">
                            {{ substr($user->first_name ?? 'U', 0, 1) }}
                        </div>
                        <div>
                            <h2 class="text-3xl font-bold text-gray-200 tracking-tight">Profile</h2>
                            <p class="text-gray-400">Manage your profile information</p>
                        </div>
                    </div>
                    <button 
                        x-data=""
                        x-on:click.prevent="$dispatch('open-modal', 'edit-profile')"
                        class="bg-orange-600/20 hover:bg-orange-600/30 text-orange-500 font-semibold py-3 px-6 rounded-xl transition-all flex items-center space-x-3 border border-orange-600/20 hover:border-orange-600/40 hover:translate-y-[-2px]"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                        </svg>
                        <span>Edit Profile</span>
                    </button>
                </div>
            </div>

            <!-- Profile Details - Improved card layout -->
            <div class="p-6 md:p-8 bg-[#121212]">
                <div class="grid md:grid-cols-3 gap-6">
                    <div class="bg-[#1e1e1e] rounded-xl p-5 shadow-md transform hover:scale-105 transition-transform duration-300 border border-gray-800">
                        <div class="flex items-center space-x-2 mb-3">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-orange-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                            <p class="text-sm text-gray-400">Full Name</p>
                        </div>
                        <p class="text-sm font-semibold text-gray-200">{{ ($user->first_name ?? 'Not set') . ' ' . ($user->last_name ?? '') }}
                        </p>
                    </div>
                    <div class="bg-[#1e1e1e] rounded-xl p-5 shadow-md transform hover:scale-105 transition-transform duration-300 border border-gray-800">
                        <div class="flex items-center space-x-2 mb-3">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-orange-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                            </svg>
                            <p class="text-sm text-gray-400">Email Address</p>
                        </div>
                        <p class="text-sm font-semibold text-gray-200">{{ $user->email }}</p>
                    </div>
                    <div class="bg-[#1e1e1e] rounded-xl p-5 shadow-md transform hover:scale-105 transition-transform duration-300 border border-gray-800">
                        <div class="flex items-center space-x-2 mb-3">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-orange-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                            </svg>
                            <p class="text-sm text-gray-400">Phone Number</p>
                        </div>
                        <p class="text-sm font-semibold text-gray-200">
                            {{ $user->phone_number ?? 'Not provided' }}
                        </p>
                    </div>
                </div>
            </div>

            <!-- Account Footer with improved styling -->
            <div class="bg-[#1e1e1e] p-6 md:p-8 border-t border-gray-800">
                <div class="flex flex-col md:flex-row justify-between items-center space-y-4 md:space-y-0">
                    <div class="flex items-center space-x-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                        <p class="text-sm text-gray-400">
                            Account created: {{ $user->created_at->format('F d, Y') }}
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Security Card with improved styling -->
        <div class="bg-[#1e1e1e] rounded-2xl shadow-xl overflow-hidden transition-transform hover:shadow-2xl">
            <!-- Security Header -->
            <div class="bg-gradient-to-r from-[#2c2c2c] to-[#1e1e1e] p-6 md:p-8">
                <div class="flex flex-col md:flex-row justify-between items-start md:items-center space-y-4 md:space-y-0">
                    <div class="flex items-center space-x-4">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-orange-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                        </svg>
                        <div>
                            <h2 class="text-3xl font-bold text-gray-200 tracking-tight">Security</h2>
                            <p class="text-gray-400">Manage your password and account security</p>
                        </div>
                    </div>
                    <button 
                        x-data=""
                        x-on:click.prevent="$dispatch('open-modal', 'change-password')"
                        class="bg-orange-600/20 hover:bg-orange-600/30 text-orange-500 font-semibold py-3 px-6 rounded-xl transition-all flex items-center space-x-3 border border-orange-600/20 hover:border-orange-600/40 hover:translate-y-[-2px]"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z" />
                        </svg>
                        <span>Change Password</span>
                    </button>
                </div>
            </div>

            <!-- Security Info with improved styling -->
            <div class="p-6 md:p-8 bg-[#121212]">
                <div class="bg-[#1e1e1e] rounded-xl p-5 shadow-md border border-gray-800">
                    <div class="flex items-start space-x-4">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 text-orange-500/40 mt-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <div>
                            <h3 class="text-xl font-semibold text-gray-200 mb-3">Password Security Tips</h3>
                            <ul class="text-gray-400 space-y-3">
                                <li class="flex items-center space-x-3 p-2 bg-green-500/5 rounded-lg">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-green-500 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                    </svg>
                                    <span>Use a unique password you don't use elsewhere</span>
                                </li>
                                <li class="flex items-center space-x-3 p-2 bg-green-500/5 rounded-lg">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-green-500 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                    </svg>
                                    <span>Mix uppercase, lowercase, numbers and symbols</span>
                                </li>
                                <li class="flex items-center space-x-3 p-2 bg-green-500/5 rounded-lg">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-green-500 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                    </svg>
                                    <span>Use at least 12 characters for stronger security</span>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Edit Profile Modal with improved styling and validation -->
        <x-modal name="edit-profile" :show="$errors->updateProfile->isNotEmpty()" focusable>
            <div class="flex items-center justify-center min-h-full p-4">
                <form 
                    method="post" 
                    action="{{ route('profile.update') }}" 
                    class="bg-[#121212] rounded-2xl p-8 w-full md:p-12 max-w-xl shadow-2xl"
                >
                    @csrf
                    @method('patch')

                    <h2 class="text-3xl font-bold text-gray-200 mb-2">Edit Profile</h2>
                    <p class="text-gray-400 mb-6">Update your personal information</p>

                    <div class="space-y-6">
                        <!-- Separated First Name and Last Name fields -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <x-input-label for="first_name" :value="__('First Name')" class="text-gray-200 mb-2" />
                                <x-text-input
                                    id="first_name"
                                    name="first_name"
                                    type="text"
                                    class="w-full bg-[#1e1e1e] border-gray-700 text-gray-200 rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-transparent"
                                    :value="old('first_name', $user->first_name)"
                                    required
                                    autofocus
                                    placeholder="Enter your first name"
                                    minlength="2"
                                    maxlength="50"
                                    pattern="[A-Za-z\s]+"
                                    title="Only letters and spaces are allowed"
                                />
                                <x-input-error :messages="$errors->updateProfile->get('first_name')" class="mt-2" />
                                @error('first_name')
                                    <span class="text-red-500 text-sm">{{ $message }}</span>
                                @enderror
                            </div>

                            <div>
                                <x-input-label for="last_name" :value="__('Last Name')" class="text-gray-200 mb-2" />
                                <x-text-input
                                    id="last_name"
                                    name="last_name"
                                    type="text"
                                    class="w-full bg-[#1e1e1e] border-gray-700 text-gray-200 rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-transparent"
                                    :value="old('last_name', $user->last_name)"
                                    required
                                    placeholder="Enter your last name"
                                    minlength="2"
                                    maxlength="50"
                                    pattern="[A-Za-z\s]+"
                                    title="Only letters and spaces are allowed"
                                />
                                <x-input-error :messages="$errors->updateProfile->get('last_name')" class="mt-2" />
                                @error('last_name')
                                    <span class="text-red-500 text-sm">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div>
                            <x-input-label for="email" :value="__('Email Address')" class="text-gray-200 mb-2" />
                            <x-text-input
                                id="email"
                                name="email"
                                type="email"
                                class="w-full bg-[#1e1e1e] border-gray-700 text-gray-200 rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-transparent"
                                :value="old('email', $user->email)"
                                required
                                placeholder="your.email@example.com"
                                pattern="[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,}$"
                                title="Please enter a valid email address"
                            />
                            <x-input-error :messages="$errors->updateProfile->get('email')" class="mt-2" />
                            @error('email')
                                <span class="text-red-500 text-sm">{{ $message }}</span>
                            @enderror
                            <p class="text-xs text-gray-400 mt-2">Must be a valid email address</p>
                        </div>

                        <div>
                            <x-input-label for="phone_number" :value="__('Phone Number')" class="text-gray-200 mb-2" />
                            <x-text-input
                                id="phone_number"
                                name="phone_number"
                                type="tel"
                                class="w-full bg-[#1e1e1e] border-gray-700 text-gray-200 rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-transparent"
                                :value="old('phone_number', $user->phone_number)"
                                placeholder="09** **** *** (Optional)"
                                pattern="09[0-9]{9}"
                                title="Phone number must start with 09 and be 11 digits"
                                maxlength="11"
                            />
                            <x-input-error :messages="$errors->updateProfile->get('phone_number')" class="mt-2" />
                            @error('phone_number')
                                <span class="text-red-500 text-sm">{{ $message }}</span>
                            @enderror
                            <p class="text-xs text-gray-400 mt-2">Must start with 09 and be 11 digits in length</p>
                        </div>
                    </div>

                    <div class="mt-8 flex justify-end space-x-4">
                        <x-secondary-button x-on:click="$dispatch('close')" class="bg-gray-800 text-gray-400 hover:bg-[#2c2c2c] border-gray-700">
                            {{ __('Cancel') }}
                        </x-secondary-button>

                        <x-primary-button class="bg-orange-600 hover:bg-orange-700 text-white border-none">
                            {{ __('Save Changes') }}
                        </x-primary-button>
                    </div>
                </form>
            </div>
        </x-modal>

        <!-- Change Password Modal with improved styling -->
        <x-modal name="change-password" :show="$errors->updatePassword->isNotEmpty()" focusable>
            <form 
                method="post" 
                action="{{ route('password.update') }}" 
                class="bg-[#121212] rounded-2xl p-8 md:p-12 max-w-xl mx-auto shadow-2xl"
            >
                @csrf
                @method('put')

                <h2 class="text-3xl font-bold text-gray-200 mb-2">Change Password</h2>
                <p class="text-gray-400 mb-6">Ensure your account is using a secure password</p>

                <div class="space-y-6">
                    <div>
                        <x-input-label for="current_password" :value="__('Current Password')" class="text-gray-400 mb-2" />
                        <x-text-input
                            id="current_password"
                            name="current_password"
                            type="password"
                            class="w-full bg-[#1e1e1e] border-gray-700 text-gray-200 rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-transparent"
                            autocomplete="current-password"
                            placeholder="Enter your current password"
                        />
                        <x-input-error :messages="$errors->updatePassword->get('current_password')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="password" :value="__('New Password')" class="text-gray-400 mb-2" />
                        <x-text-input
                            id="password"
                            name="password"
                            type="password"
                            class="w-full bg-[#1e1e1e] border-gray-700 text-gray-200 rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-transparent"
                            autocomplete="new-password"
                            placeholder="Enter your new password"
                        />
                        <x-input-error :messages="$errors->updatePassword->get('password')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="password_confirmation" :value="__('Confirm Password')" class="text-gray-400 mb-2" />
                        <x-text-input
                            id="password_confirmation"
                            name="password_confirmation"
                            type="password"
                            class="w-full bg-[#1e1e1e] border-gray-700 text-gray-200 rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-transparent"
                            autocomplete="new-password"
                            placeholder="Confirm your new password"
                        />
                        <x-input-error :messages="$errors->updatePassword->get('password_confirmation')" class="mt-2" />
                    </div>

                    <div class="p-4 bg-[#1e1e1e] rounded-xl border border-orange-600/20">
                        <div class="flex items-center space-x-2 text-orange-500 mb-2">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <span class="font-medium">Password Strength</span>
                        </div>
                        <div class="text-sm text-gray-400">
                            Your password should be at least 8 characters and include uppercase letters, lowercase letters, numbers, and special characters.
                        </div>
                    </div>
                </div>

                <div class="mt-8 flex justify-end space-x-4">
                    <x-secondary-button x-on:click="$dispatch('close')" class="bg-[#1e1e1e] text-gray-400 hover:bg-[#2c2c2c] border-gray-700">
                        {{ __('Cancel') }}
                    </x-secondary-button>

                    <x-primary-button class="bg-orange-600 hover:bg-orange-700 text-gray-200 border-none">
                        {{ __('Change Password') }}
                    </x-primary-button>
                </div>
            </form>
        </x-modal>
    </div>
</div>
@endsection