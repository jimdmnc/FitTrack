@extends('layouts.app')

@section('content')
<style>
 .glass-card {
            background: rgba(255, 255, 255, 0.95);
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
            background: linear-gradient(120deg, #a1c4fd 0%, #c2e9fb 100%);
        }

</style>
<div class="py-10 px-4 md:px-12">
    <!-- Page Title -->
            <div class="glass-card p-6">
                <div class="flex flex-col md:flex-row justify-between items-center">
                    <div>
                        <h1 class="text-2xl md:text-4xl font-extrabold bg-clip-text text-transparent bg-gradient-to-r from-blue-600 to-indigo-700">
                        Membership Registration

                        </h1>
                        <p class="text-gray-500 mt-2">Complete the form below to register a new gym member</p>
                    </div>
                    <div class="mt-4 md:mt-0 flex items-center gap-3">
      
                      
                    </div>
                </div>
            </div>



    <!-- Form Container -->
    <div class="max-w-8xl mx-auto mt-10">
        
        <!-- Success Message -->
    @if(session('success'))
        <div class="max-w-4xl mx-auto bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg mb-6 flex justify-between items-center" role="alert">
            <div class="flex items-center">
                <svg class="h-5 w-5 text-green-500 mr-2" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                </svg>
                <span>{{ session('success') }}</span>
            </div>
            <button type="button" class="text-green-500 hover:text-green-700" onclick="this.parentElement.style.display='none';">
                <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                </svg>
            </button>
        </div>
    @endif
        <form id="registrationForm" action="{{ route('staff.membershipRegistration') }}" method="POST" class="bg-white rounded-xl shadow-lg overflow-hidden">
            @csrf
            
  
            
            <!-- Personal Information Section -->
            <div class="p-6 border-b border-gray-200 bg-indigo-50">
                <h2 class="text-xl font-semibold text-gray-800 flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mr-2 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                    Personal Information
                </h2>
                <p class="text-sm text-gray-600 mt-1 mb-4">Enter the member's personal details</p>
            </div>
            
            <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="first_name" class="block text-gray-700 font-medium mb-2">First Name <span class="text-red-500">*</span></label>
                    <input type="text" id="first_name" name="first_name" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent" value="{{ old('first_name') }}" required>
                    @error('first_name')
                        <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span>
                    @enderror
                </div>

                <div>
                    <label for="last_name" class="block text-gray-700 font-medium mb-2">Last Name <span class="text-red-500">*</span></label>
                    <input type="text" id="last_name" name="last_name" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent" value="{{ old('last_name') }}" required>
                    @error('last_name')
                        <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span>
                    @enderror
                </div>

                <div>
                    <label for="birthdate" class="block text-gray-700 font-medium mb-2">Birthdate <span class="text-red-500">*</span></label>
                    <div class="relative">
                        <input type="date" id="birthdate" name="birthdate" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent" 
                            value="{{ old('birth_date') }}" required max="{{ date('Y-m-d') }}">

                    </div>
                    @error('birthdate')
                        <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span>
                    @enderror
                </div>

                <div>
                    <label for="gender" class="block text-gray-700 font-medium mb-2">Gender <span class="text-red-500">*</span></label>
                    <select id="gender" name="gender" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent" required>
                        <option value="" selected disabled>Select Gender</option>
                        <option value="male" {{ old('gender') == 'male' ? 'selected' : '' }}>Male</option>
                        <option value="female" {{ old('gender') == 'female' ? 'selected' : '' }}>Female</option>
                    </select>
                    @error('gender')
                        <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span>
                    @enderror
                </div>

                <div>
                    <label for="phoneNumber" class="block text-gray-700 font-medium mb-2">Phone Number <span class="text-red-500">*</span></label>
                    <div class="relative">
                        <input type="tel" id="phoneNumber" name="phone_number" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent" 
                            pattern="\d{11}" maxlength="11" placeholder="11-digit phone number" value="{{ old('phone_number') }}" required oninput="this.value = this.value.replace(/\D/g, '')">
                        <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                            </svg>
                        </div>
                    </div>
                    <span class="text-xs text-gray-500 mt-1 block">Format: 11 digits without spaces or dashes</span>
                    @error('phone_number')
                        <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span>
                    @enderror
                </div>

                <div>
                    <label for="email" class="block text-gray-700 font-medium mb-2">Email Address <span class="text-red-500">*</span></label>
                    <div class="relative">
                        <input type="email" id="email" name="email" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent" 
                            placeholder="example@email.com" value="{{ old('email') }}" required>
                        <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                            </svg>
                        </div>
                    </div>
                    @error('email')
                        <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            <!-- Membership Section -->
            <div class="p-6 border-t border-b border-gray-200 bg-indigo-50">
                <h2 class="text-xl font-semibold text-gray-800 flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mr-2 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z" />
                    </svg>
                    Membership Details
                </h2>
                <p class="text-sm text-gray-600 mt-1 mb-4">Select membership type and duration</p>
            </div>
            
            <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="membershipType" class="block text-gray-700 font-medium mb-2">Membership Type <span class="text-red-500">*</span></label>
                    <select id="membershipType" name="membership_type" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent" required>
                        <option value="" selected disabled>Select Membership Type</option>
                        <option value="1" {{ old('membership_type') == '1' ? 'selected' : '' }}>Session (1 day)</option>
                        <option value="7" {{ old('membership_type') == '7' ? 'selected' : '' }}>Weekly (7 days)</option>
                        <option value="30" {{ old('membership_type') == '30' ? 'selected' : '' }}>Monthly (30 days)</option>
                        <option value="365" {{ old('membership_type') == '365' ? 'selected' : '' }}>Annual (365 days)</option>
                    </select>
                    @error('membership_type')
                        <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span>
                    @enderror
                </div>

                <div>
                    <label for="startDate" class="block text-gray-700 font-medium mb-2">Start Date <span class="text-red-500">*</span></label>
                    <div class="relative">
                        <input type="date" id="startDate" name="start_date" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent" value="{{ old('start_date') ?? date('Y-m-d') }}" required>

                    </div>
                    @error('start_date')
                        <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span>
                    @enderror
                </div>

                <div>
                    <label for="endDate" class="block text-gray-700 font-medium mb-2">Expiration Date</label>
                    <div class="relative">
                        <input type="text" id="endDate" name="expiry_date" placeholder="Calculated automatically" class="w-full px-4 py-3 border border-gray-300 rounded-lg bg-gray-100" readonly>
                        <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                        </div>
                    </div>
                    <span class="text-xs text-gray-500 mt-1 block">Auto-calculated based on membership type</span>
                </div>

                <div>
                    <label for="uid" class="block text-gray-700 font-medium mb-2">RFID Card <span class="text-red-500">*</span></label>
                    <div class="relative">
                        <input id="uid" name="uid" class="w-full px-4 py-3 border border-gray-300 rounded-lg bg-gray-100" placeholder="Tap your RFID card on the reader" readonly />
                        <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 9a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v6m3-3H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                    </div>
                    <div id="rfid_status" class="mt-2 text-sm text-blue-500 flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1 animate-pulse" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                        </svg>
                        Please Tap Your Card...
                    </div>
                    @error('rfid_uid')
                        <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            <!-- Account Section -->
            <div class="p-6 border-t border-gray-200 bg-indigo-50">
                <h2 class="text-xl font-semibold text-gray-800 flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mr-2 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                    </svg>
                    Account Setup
                </h2>
                <p class="text-sm text-gray-600 mt-1 mb-4">Auto-generated password details</p>
            </div>
                
            <div class="p-6">
                <div class="bg-yellow-50 p-4 rounded-lg border border-yellow-200 mb-6">
                    <div class="flex">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-yellow-600 mr-2 flex-shrink-0" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                        </svg>
                        <div>
                            <h3 class="text-sm font-medium text-yellow-800">Password Information</h3>
                            <p class="text-sm text-yellow-700 mt-1">A password will be automatically generated based on the member's last name and birth date.</p>
                        </div>
                    </div>
                </div>
                
                <div class="bg-gray-100 p-4 rounded-lg border border-gray-200">
                    <div class="flex justify-between items-center">
                        <label for="password" class="block text-gray-700 font-medium">Generated Password</label>
                        <span class="text-xs text-gray-500">Will be shown to the member</span>
                    </div>
                    <div class="relative mt-2">
                        <input type="text" id="password" name="password" class="w-full px-4 py-3 border border-gray-300 rounded-lg bg-white focus:outline-none" readonly required>
                        <div class="absolute inset-y-0 right-0 pr-3 flex items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z" />
                            </svg>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Submit Button -->
            <div class="p-6 border-t border-gray-200 bg-gray-50">
                <div class="flex flex-col md:flex-row md:justify-between gap-4">
                    <button type="button" class="md:w-1/4 bg-gray-500 text-white py-3 px-6 rounded-lg hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2">
                        Clear Form
                    </button>
                    <button type="submit" class="md:w-3/4 bg-indigo-600 text-white py-3 px-6 rounded-lg hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 flex items-center justify-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                        </svg>
                        Register New Member
                    </button>
                </div>
            </div>
        </form>

    </div>
</div>



<!-- JavaScript for Expiry Date -->
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const form = document.getElementById('registrationForm');
        const clearButton = form.querySelector('button[type="button"]');
        
        // Clear form functionality
        clearButton.addEventListener('click', function() {
            if (confirm('Are you sure you want to clear the form?')) {
                form.reset();
                document.getElementById('endDate').value = '';
                document.getElementById('password').value = '';
                document.getElementById('rfid_status').innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1 animate-pulse" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" /></svg>Please Tap Your Card...';
                document.getElementById('rfid_status').className = 'mt-2 text-sm text-blue-500 flex items-center';
            }
        });
    });

    document.getElementById('membershipType').addEventListener('change', updateEndDate);
    document.getElementById('startDate').addEventListener('change', updateEndDate);

    function updateEndDate() {
        let membershipType = document.getElementById('membershipType').value;
        let startDateInput = document.getElementById('startDate').value;
        let endDateInput = document.getElementById('endDate');

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
            endDateInput.value = formattedDate;
        } else {
            endDateInput.value = '';
        }
    }
</script>
<script>
    // Function to fetch the latest RFID UID
function fetchLatestUid() {
    fetch('/rfid/latest')
        .then(response => response.json())
        .then(data => {
            console.log('Data received:', data); // Debugging
            if (data.uid) {
                document.getElementById('uid').value = data.uid;
                document.getElementById('rfid_status').textContent = 'Card detected.';
                document.getElementById('rfid_status').style.color = 'green';
            } else {
                console.log('No UID found'); // Debugging
                document.getElementById('rfid_status').textContent = 'Please Tap Your Card...';
                // document.getElementById('rfid_status').style.color = 'blue';
            }
        })
        .catch(error => {
            console.error('Error fetching UID:', error); // Debugging
            document.getElementById('rfid_status').textContent = 'Error fetching UID.';
            document.getElementById('rfid_status').style.color = 'red';
        });
}

    // Fetch the latest UID every 2 seconds (adjust interval as needed)
    setInterval(fetchLatestUid, 2000);

    // Fetch the latest UID immediately when the page loads
    fetchLatestUid();

    // Clear the UID input field after successful form submission
    @if (session('success'))
        document.getElementById('uid').value = ''; // Clear the UID input field
        document.getElementById('rfid_status').textContent = 'UID submitted successfully!';
        document.getElementById('rfid_status').style.color = 'green';
    @endif

    @if (session('error'))
        document.getElementById('rfid_status').textContent = 'Error submitting UID.';
        document.getElementById('rfid_status').style.color = 'red';
    @endif
</script>

<script>
    document.addEventListener("DOMContentLoaded", function () {
        // Automatically fill password when birthdate or last name is entered
        function updatePassword() {
            let lastName = document.getElementById("last_name").value.toLowerCase();
    let birthdate = this.value; // Format is YYYY-MM-DD

    if (lastName && birthdate) {
        let dateParts = birthdate.split("-"); // Split into [YYYY, MM, DD]
        let formattedPassword = `${lastName}${dateParts[1]}${dateParts[2]}${dateParts[0]}`; // lastNameMMDDYYYY
        document.getElementById("password").value = formattedPassword;
    }
        }

        // Attach event listeners
        document.querySelector("input[name='last_name']").addEventListener("input", updatePassword);
        document.querySelector("input[name='birthdate']").addEventListener("input", updatePassword);
    });
</script>
@endsection