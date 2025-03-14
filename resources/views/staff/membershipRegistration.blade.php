@extends('layouts.app')

@section('content')
<!-- Main Content Area -->
<style>
    /* Toggle button styles */
    input:checked ~ .dot {
        transform: translateX(100%);
        background-color: #4F46E5;
    }
    
    input:checked ~ div {
        background-color: #C7D2FE;
    }
</style>
<section class=" grid grid-cols-1 gap-4 pt-10">
    <div class="flex justify-between md:col-span-2 bg-white p-6 rounded-lg shadow-lg shadow-gray-400 border border-gray-200 transform hover:scale-105 transition duration-300">
        <h2 class="font-bold text-lg sm:text-3xl text-gray-800">
            <span class="text-indigo-700 drop-shadow-lg">Add New Member</span>
        </h2>
        <!-- RFID Mode Toggle Button -->
    <label class="flex items-center cursor-pointer">
        <div class="relative">
            <input type="checkbox" id="rfidModeToggle" class="sr-only">
            <div class="w-12 h-6 bg-gray-300 rounded-full shadow-inner"></div>
            <div class="dot absolute w-6 h-6 bg-white rounded-full shadow -left-1 -top-1 transition"></div>
        </div>
        <div class="ml-3 text-gray-700 font-medium">
            <span id="modeLabel">Attendance Mode<br></span>
            <span class="ml-1 text-sm text-gray-500">(Toggle for Registration Mode)</span>
        </div>
    </label>
    </div>
    
</section>

<!-- Form Container -->
<div class="relative z-10 px-12 pb-8 mt-10">
    <!-- Success Message -->
@if(session('success'))
    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-6" role="alert">
        <span class="block sm:inline">{{ session('success') }}</span>
        <span class="absolute top-0 bottom-0 right-0 px-4 py-3">
            <svg class="fill-current h-6 w-6 text-green-500" role="button" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                <title>Close</title>
                <path d="M14.348 14.849a1 1 0 0 1-1.414 0L10 11.414l-2.93 2.93a1 1 0 1 1-1.414-1.414l2.93-2.93-2.93-2.93a1 1 0 1 1 1.414-1.414l2.93 2.93 2.93-2.93a1 1 0 1 1 1.414 1.414l-2.93 2.93 2.93 2.93a1 1 0 0 1 0 1.414z"/>
            </svg>
        </span>
    </div>
@endif
    <form action="{{ route('staff.membershipRegistration') }}" method="POST" class="grid grid-cols-1 lg:grid-cols-2 gap-6 bg-white p-8 rounded-xl shadow-lg">
        @csrf
        <!-- Left Column -->
        <div class="space-y-6">
            <!-- First Name -->
            <div>
                <input type="text" id="first_name" placeholder="First Name" name="first_name" class="w-full px-4 py-3 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-gray-500 focus:border-transparent" value="{{ old('first_name') }}" required>
                @error('first_name')
                    <span class="text-red-500 text-sm">{{ $message }}</span>
                @enderror
            </div>

            <!-- Email -->
            <div>
                <input type="email" id="email" placeholder="Email" name="email" class="w-full px-4 py-3 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-gray-500 focus:border-transparent" value="{{ old('email') }}" required>
                @error('email')
                    <span class="text-red-500 text-sm">{{ $message }}</span>
                @enderror
            </div>

            <!-- Phone Number -->
            <div>
                <input type="tel" id="phoneNumber" placeholder="Phone Number" name="phone_number" class="w-full px-4 py-3 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-gray-500 focus:border-transparent" pattern="\d{11}" maxlength="11" value="{{ old('phone_number') }}" required oninput="this.value = this.value.replace(/\D/g, '')">
                @error('phone_number')
                    <span class="text-red-500 text-sm">{{ $message }}</span>
                @enderror
            </div>

            <!-- Start Date -->
            <div>
                <label for="startDate" class="block text-gray-700 font-medium mb-2">Start Date *</label>
                <input type="date" id="startDate" name="start_date" class="w-full px-4 py-3 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-gray-500 focus:border-transparent" value="{{ old('start_date') }}" required>
                @error('start_date')
                    <span class="text-red-500 text-sm">{{ $message }}</span>
                @enderror
            </div>



                        <!-- Password -->
            <div>
                <input type="password" id="password" placeholder="Password" name="password" class="w-full px-4 py-3 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-gray-500 focus:border-transparent" required>
                @error('password')
                    <span class="text-red-500 text-sm">{{ $message }}</span>
                @enderror
            </div>

<!-- RFID UID -->
<div>
    <!-- <label for="rfid_uid" class="block text-gray-700 font-medium mb-2">RFID Card</label> -->
    <div class="flex flex-col">
        <x-text-input id="rfid_uid" 
            class="block w-full px-4 py-3 border border-gray-300 rounded-md bg-gray-100 focus:outline-none" 
            type="text" 
            name="rfid_uid" 
            :value="old('rfid_uid')" 
            placeholder="Tap your RFID card on the reader"
            readonly />
        <div id="rfid_status" class="mt-2 text-sm text-gray-500"></div>
    </div>
    @error('rfid_uid')
        <span class="text-red-500 text-sm">{{ $message }}</span>
    @enderror
</div>


        </div>

        <!-- Right Column -->
        <div class="space-y-6">
            <!-- Last Name -->
            <div>
                <input type="text" id="last_name" placeholder="Last Name" name="last_name" class="w-full px-4 py-3 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-gray-500 focus:border-transparent" value="{{ old('last_name') }}" required>
                @error('last_name')
                    <span class="text-red-500 text-sm">{{ $message }}</span>
                @enderror
            </div>

            <!-- Gender Dropdown -->
            <div>
                <select id="gender" name="gender" class="w-full px-4 py-3 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-gray-500 focus:border-transparent text-gray-700" required>
                    <option selected disabled>Select Gender</option>
                    <option value="male" {{ old('gender') == 'male' ? 'selected' : '' }}>Male</option>
                    <option value="female" {{ old('gender') == 'female' ? 'selected' : '' }}>Female</option>
                </select>
                @error('gender')
                    <span class="text-red-500 text-sm">{{ $message }}</span>
                @enderror
            </div>

            <!-- Membership Type -->
            <div>
                <select id="membershipType" name="membership_type" class="w-full px-4 py-3 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-gray-500 focus:border-transparent text-gray-700" required>
                    <option selected disabled>Select Membership Type</option>
                    <option value="1" {{ old('membership_type') == '1' ? 'selected' : '' }}>Session</option>
                    <option value="7" {{ old('membership_type') == '7' ? 'selected' : '' }}>Weekly</option>
                    <option value="30" {{ old('membership_type') == '30' ? 'selected' : '' }}>Monthly</option>
                    <option value="365" {{ old('membership_type') == '365' ? 'selected' : '' }}>Annual</option>
                </select>
                @error('membership_type')
                    <span class="text-red-500 text-sm">{{ $message }}</span>
                @enderror
            </div>

            <!-- Expiry Date -->
            <div>
                <label for="expiryDate" class="block text-gray-700 font-medium mb-2">Expiration Date</label>
                <input type="text" id="expiryDate" name="expiry_date" placeholder="dd/mm/yyyy" class="w-full px-4 py-3 border border-gray-300 rounded-md bg-gray-100 cursor-not-allowed" readonly>
            </div>



            <!-- Confirm Password -->
            <div>
                <input type="password" id="password_confirmation" placeholder="Confirm Password" name="password_confirmation" class="w-full px-4 py-3 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-gray-500 focus:border-transparent" required>
            </div>


            <!-- Submit Button -->
            <div class="">
                <button type="submit" class="w-full bg-gray-800 text-white py-3 rounded-md font-semibold hover:bg-gray-600 transition focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">Register</button>
            </div>
        </div>
    </form>
</div>

<!-- JavaScript for Expiry Date -->
<script>
    document.getElementById('membershipType').addEventListener('change', updateExpiryDate);
    document.getElementById('startDate').addEventListener('change', updateExpiryDate);

    function updateExpiryDate() {
        let membershipType = document.getElementById('membershipType').value;
        let startDateInput = document.getElementById('startDate').value;
        let expiryDateInput = document.getElementById('expiryDate');

        if (startDateInput && membershipType) {
            let startDate = new Date(startDateInput);

            switch (membershipType) {
                case '1': // Session (1 day)
                    startDate.setDate(startDate.getDate() + 1);
                    break;
                case '7': // Weekly (7 days)
                    startDate.setDate(startDate.getDate() + 7);
                    break;
                case '30': // Monthly (30 days)
                    startDate.setDate(startDate.getDate() + 30);
                    break;
                case '365': // Annual (365 days)
                    startDate.setDate(startDate.getDate() + 365);
                    break;
                default:
                    break;
            }

            let day = String(startDate.getDate()).padStart(2, '0');
            let month = String(startDate.getMonth() + 1).padStart(2, '0');
            let year = startDate.getFullYear();

            let formattedDate = `${day}/${month}/${year}`;
            expiryDateInput.value = formattedDate;
        } else {
            expiryDateInput.value = '';
        }
    }



    // <!-- Add this script section at the bottom of your form -->
    document.addEventListener('DOMContentLoaded', function() {
        let lastUid = '';
        let isRegistrationMode = false;
        const rfidInput = document.getElementById('rfid_uid');
        const rfidStatus = document.getElementById('rfid_status');
        const modeToggle = document.getElementById('rfidModeToggle');
        const modeLabel = document.getElementById('modeLabel');
        const rfidFieldLabel = document.getElementById('rfidFieldLabel');
        const rfidModeMessage = document.getElementById('rfidModeMessage');
        const modeUrl = '{{ route("latest.rfid") }}';
        
        // Function to update UI based on mode
        function updateModeUI(isRegMode) {
            if (isRegMode) {
                modeLabel.textContent = 'Registration Mode';
                rfidFieldLabel.textContent = 'RFID Card for Registration';
                rfidModeMessage.textContent = 'Card will be assigned to this member';
                document.querySelector('.dot').classList.add('translate-x-6');
            } else {
                modeLabel.textContent = 'Attendance Mode';
                rfidFieldLabel.textContent = 'RFID Card';
                rfidModeMessage.textContent = 'Card will be used for attendance tracking';
                document.querySelector('.dot').classList.remove('translate-x-6');
            }
        }
        
        // Toggle mode when button is clicked
        modeToggle.addEventListener('change', function() {
            isRegistrationMode = this.checked;
            updateModeUI(isRegistrationMode);
            
            // Clear the input field when switching modes
            rfidInput.value = '';
            lastUid = '';
            rfidStatus.textContent = 'Waiting for card...';
            rfidStatus.className = 'ml-3 text-sm text-gray-500';
        });
        
        // Function to check for new RFID UID
        function checkForNewRfid() {
            console.log('Fetching latest RFID, mode:', isRegistrationMode ? 'Registration' : 'Attendance');
            
            fetch(modeUrl)
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok: ' + response.status);
                    }
                    return response.json();
                })
                .then(data => {
                    console.log('Received data:', data);
                    
                    if (data.uid && data.uid !== lastUid) {
                        // Update the input field with the new UID
                        rfidInput.value = data.uid;
                        lastUid = data.uid;
                        
                        // Update status and show animation
                        if (isRegistrationMode) {
                            rfidStatus.textContent = 'Card registered!';
                        } else {
                            rfidStatus.textContent = 'Attendance recorded!';
                            
                            // For attendance mode, we could send an attendance record
                            // This is just a placeholder - you'd need to implement the actual endpoint
                            if (!isRegistrationMode) {
                                try {
                                    fetch('{{ route("rfid.store") }}', {
                                        method: 'POST',
                                        headers: {
                                            'Content-Type': 'application/json',
                                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                        },
                                        body: JSON.stringify({
                                            uid: data.uid,
                                            mode: 'attendance'
                                        })
                                    }).then(response => response.json())
                                      .then(result => console.log('Attendance result:', result));
                                } catch (e) {
                                    console.error('Error recording attendance:', e);
                                }
                            }
                        }
                        
                        rfidStatus.classList.remove('text-gray-500');
                        rfidStatus.classList.add('text-green-500', 'font-bold');
                        
                        // Reset status after 3 seconds
                        setTimeout(() => {
                            rfidStatus.textContent = 'Waiting for card...';
                            rfidStatus.classList.remove('text-green-500', 'font-bold');
                            rfidStatus.classList.add('text-gray-500');
                        }, 3000);
                    }
                })
                .catch(error => {
                    console.error('Error fetching RFID data:', error);
                    rfidStatus.textContent = 'Connection error: ' + error.message;
                    rfidStatus.classList.remove('text-gray-500', 'text-green-500');
                    rfidStatus.classList.add('text-red-500');
                });
        }
        
        // Check for new RFID every 2 seconds
        const intervalId = setInterval(checkForNewRfid, 2000);
        
        // Check once on page load
        checkForNewRfid();
        
        // Initialize UI based on default mode
        updateModeUI(isRegistrationMode);
        
        // Clean up interval when page is unloaded
        window.addEventListener('beforeunload', function() {
            clearInterval(intervalId);
        });
    });
</script>


@endsection