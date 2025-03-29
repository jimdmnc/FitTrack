@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-12 bg-[#121212] min-h-screen">
    <div class="mb-8">
        <h2 class="text-4xl font-bold bg-clip-text text-transparent bg-gradient-to-r from-red-600 to-orange-600 tracking-tight">Profile Overview</h2>
        <p class="text-gray-400 mt-2">Manage your personal information</p>
    </div>
    <div class="max-w-6xl mx-auto space-y-8">
        <div class="bg-[#1e1e1e] rounded-2xl shadow-xl overflow-hidden">
            {{-- Profile Header --}}
            <div class="bg-gradient-to-r from-[#2c2c2c] to-[#1e1e1e] p-6 md:p-8">
                <div class="flex flex-col md:flex-row justify-between items-center space-y-4 md:space-y-0">
                    <div>
                        <h2 class="text-3xl font-bold text-gray-200 tracking-tight">Profile</h2>
                    </div>
                    <button 
                        x-data=""
                        x-on:click.prevent="$dispatch('open-modal', 'edit-profile')"
                        class="bg-orange-600/20 hover:bg-orange-600/30 text-orange-500 font-semibold py-3 px-6 rounded-xl transition-all flex items-center space-x-3 border border-orange-600/20 hover:border-orange-600/40"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                        </svg>
                        <span>Edit Profile</span>
                    </button>
                </div>
            </div>

            {{-- Profile Details --}}
            <div class="p-6 md:p-8 bg-[#121212]">
                <div class="grid md:grid-cols-3 gap-6">
                    <div class="bg-[#1e1e1e] rounded-xl p-5 shadow-md">
                        <p class="text-sm text-gray-400 mb-2">Full Name</p>
                        <p class="text-xl font-semibold text-gray-200">{{ $user->name ?? 'Not set' }}</p>
                    </div>
                    <div class="bg-[#1e1e1e] rounded-xl p-5 shadow-md">
                        <p class="text-sm text-gray-400 mb-2">Email Address</p>
                        <p class="text-xl font-semibold text-gray-200">{{ $user->email }}</p>
                    </div>
                    <div class="bg-[#1e1e1e] rounded-xl p-5 shadow-md">
                        <p class="text-sm text-gray-400 mb-2">Phone Number</p>
                        <p class="text-xl font-semibold text-gray-200">
                            {{ $user->phone ?? 'Not provided' }}
                        </p>
                    </div>
                </div>
            </div>

            {{-- Account Footer --}}
            <div class="bg-[#1e1e1e] p-6 md:p-8 border-t border-gray-800">
                <div class="flex flex-col md:flex-row justify-between items-center space-y-4 md:space-y-0">
                    <p class="text-sm text-gray-400">
                        Account created: {{ $user->created_at->format('F d, Y') }}
                    </p>
                    <button 
                        x-data=""
                        x-on:click.prevent="$dispatch('open-modal', 'confirm-user-deletion')"
                        class="text-red-500 hover:text-red-400 font-medium flex items-center space-x-2 transition-colors"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                        <span>Delete Account</span>
                    </button>
                </div>
            </div>
        </div>

        {{-- Edit Profile Modal --}}
        <x-modal name="edit-profile" :show="$errors->updateProfile->isNotEmpty()" focusable>
            <form 
                method="post" 
                action="{{ route('profile.update') }}" 
                class="bg-[#121212] rounded-2xl p-8 md:p-12 max-w-xl mx-auto shadow-2xl"
            >
                @csrf
                @method('patch')

                <h2 class="text-3xl font-bold text-gray-200 mb-6">Edit Profile</h2>

                <div class="space-y-6">
                    <div>
                        <x-input-label for="name" :value="__('Full Name')" class="text-gray-400 mb-2" />
                        <x-text-input
                            id="name"
                            name="name"
                            type="text"
                            class="w-full bg-[#1e1e1e] border-none text-gray-200 rounded-xl focus:ring-2 focus:ring-orange-500"
                            :value="old('name', $user->name)"
                            required
                            autofocus
                        />
                        <x-input-error :messages="$errors->updateProfile->get('name')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="email" :value="__('Email Address')" class="text-gray-400 mb-2" />
                        <x-text-input
                            id="email"
                            name="email"
                            type="email"
                            class="w-full bg-[#1e1e1e] border-none text-gray-200 rounded-xl focus:ring-2 focus:ring-orange-500"
                            :value="old('email', $user->email)"
                            required
                        />
                        <x-input-error :messages="$errors->updateProfile->get('email')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="phone" :value="__('Phone Number')" class="text-gray-400 mb-2" />
                        <x-text-input
                            id="phone"
                            name="phone"
                            type="tel"
                            class="w-full bg-[#1e1e1e] border-none text-gray-200 rounded-xl focus:ring-2 focus:ring-orange-500"
                            :value="old('phone', $user->phone)"
                            placeholder="Optional"
                        />
                        <x-input-error :messages="$errors->updateProfile->get('phone')" class="mt-2" />
                    </div>
                </div>

                <div class="mt-8 flex justify-end space-x-4">
                    <x-secondary-button x-on:click="$dispatch('close')" class="bg-[#1e1e1e] text-gray-400 hover:bg-[#2c2c2c]">
                        {{ __('Cancel') }}
                    </x-secondary-button>

                    <x-primary-button class="bg-orange-600 hover:bg-orange-700 text-white">
                        {{ __('Save Changes') }}
                    </x-primary-button>
                </div>
            </form>
        </x-modal>
    </div>
</div>
@endsection