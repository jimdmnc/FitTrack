@extends('layouts.app')

@section('content')


<div class="container mx-auto py-8">
    <div class="bg-white rounded-lg shadow-md p-6 max-w-md mx-auto">
        <h1 class="text-2xl font-bold mb-6">WiFi Settings</h1>
        
        @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                {{ session('success') }}
            </div>
        @endif
        
        <form method="POST" action="{{ route('wifi.update') }}">
            @csrf
            
            <div class="mb-4">
                <label for="ssid" class="block text-gray-700 text-sm font-bold mb-2">WiFi SSID</label>
                <input type="text" name="ssid" id="ssid" 
                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                    value="{{ $wifiSettings->ssid ?? '' }}" required>
            </div>
            
            <div class="mb-6">
                <label for="password" class="block text-gray-700 text-sm font-bold mb-2">WiFi Password</label>
                <input type="password" name="password" id="password" 
                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                    value="{{ $wifiSettings->password ?? '' }}" required>
            </div>
            
            <div class="flex items-center justify-between">
                <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                    Save Settings
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const wifiForm = document.getElementById('wifiForm');
        const ssidInput = document.getElementById('ssid');
        const passwordInput = document.getElementById('password');
        const togglePasswordBtn = document.getElementById('togglePassword');
        const scanButton = document.getElementById('scanButton');
        const networkList = document.getElementById('networkList');
        const networkListItems = document.getElementById('networkListItems');
        const successAlert = document.getElementById('successAlert');
        const errorAlert = document.getElementById('errorAlert');
        const errorMessage = document.getElementById('errorMessage');
        const ssidError = document.getElementById('ssidError');
        const passwordError = document.getElementById('passwordError');
        
        // Toggle password visibility
        togglePasswordBtn.addEventListener('click', function() {
            const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordInput.setAttribute('type', type);
            
            // Update icon based on password visibility
            if (type === 'text') {
                togglePasswordBtn.innerHTML = `
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                    </svg>
                `;
            } else {
                togglePasswordBtn.innerHTML = `
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                    </svg>
                `;
            }
        });
        
        // Form validation and submission
        wifiForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            // Reset error states
            ssidError.classList.add('hidden');
            passwordError.classList.add('hidden');
            successAlert.classList.add('hidden');
            errorAlert.classList.add('hidden');
            
            let isValid = true;
            
            // Validate SSID
            if (!ssidInput.value.trim()) {
                ssidError.classList.remove('hidden');
                isValid = false;
            }
            
            // Validate password (if provided, must be at least 8 characters)
            if (passwordInput.value && passwordInput.value.length < 8) {
                passwordError.classList.remove('hidden');
                isValid = false;
            }
            
            if (isValid) {
                // Simulate form submission
                const submitButton = wifiForm.querySelector('button[type="submit"]');
                submitButton.disabled = true;
                submitButton.innerHTML = `
                    <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white inline-block" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    Connecting...
                `;
                
                // In a real implementation, you would send the data to your server or API
                // This is a simulation for demonstration purposes
                setTimeout(function() {
                    submitButton.disabled = false;
                    submitButton.innerHTML = 'Connect';
                    
                    // Show success message (90% chance of success for demo)
                    if (Math.random() < 0.9) {
                        successAlert.classList.remove('hidden');
                        // Reset form on success
                        wifiForm.reset();
                    } else {
                        errorMessage.textContent = "Failed to connect to network. Please check your credentials and try again.";
                        errorAlert.classList.remove('hidden');
                    }
                }, 2000);
            }
        });
        
        // Network scanning functionality
   
    });
</script>

@endsection