@extends('layouts.app')

@section('content')
<div class="py-10 px-4 md:px-12">
    <!-- Page Title -->
    <div class="mb-8 text-center">
        <h1 class="text-3xl font-bold text-gray-800">
            <span class="text-indigo-700">Membership Registration</span>
        </h1>
        <p class="text-gray-600 mt-2">Fill out the form below to register a new member</p>
    </div>

    <!-- Success Message -->
    @if(session('success'))
    <div class="max-w-4xl mx-auto bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-6" role="alert">
        <span class="block sm:inline">{{ session('success') }}</span>
        <button type="button" class="absolute top-0 bottom-0 right-0 px-4 py-3" onclick="this.parentElement.style.display='none';">
            <svg class="fill-current h-6 w-6 text-green-500" role="button" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                <title>Close</title>
                <path d="M14.348 14.849a1 1 0 0 1-1.414 0L10 11.414l-2.93 2.93a1 1 0 1 1-1.414-1.414l2.93-2.93-2.93-2.93a1 1 0 1 1 1.414-1.414l2.93 2.93 2.93-2.93a1 1 0 1 1 1.414 1.414l-2.93 2.93 2.93 2.93a1 1 0 0 1 0 1.414z"/>
            </svg>
        </button>
    </div>
    @endif

    <!-- Form Container -->
    <div class="max-w-4xl mx-auto">
        <form id="registrationForm" action="{{ route('staff.membershipRegistration') }}" method="POST" class="bg-white rounded-xl shadow-lg overflow-hidden">
            @csrf
            
            <!-- Form Sections -->
            <div class="p-6 border-b border-gray-200 bg-gray-50">
                <h2 class="text-xl font-semibold text-gray-800">Personal Information</h2>
            </div>
            
            <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Personal Info Section -->
                <div>
                    <label for="first_name" class="block text-gray-700 font-medium mb-2">First Name *</label>
                    <input type="text" id="first_name" name="first_name" class="w-full px-4 py-3 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent" value="{{ old('first_name') }}" required>
                    @error('first_name')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>

                <div>
                    <label for="last_name" class="block text-gray-700 font-medium mb-2">Last Name *</label>
                    <input type="text" id="last_name" name="last_name" class="w-full px-4 py-3 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent" value="{{ old('last_name') }}" required>
                    @error('last_name')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>



                <div>
                    <label for="phoneNumber" class="block text-gray-700 font-medium mb-2">Phone Number *</label>
                    <input type="tel" id="phoneNumber" name="phone_number" class="w-full px-4 py-3 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent" pattern="\d{11}" maxlength="11" value="{{ old('phone_number') }}" required oninput="this.value = this.value.replace(/\D/g, '')">
                    @error('phone_number')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>

                <div>
                    <label for="gender" class="block text-gray-700 font-medium mb-2">Gender *</label>
                    <select id="gender" name="gender" class="w-full px-4 py-3 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent" required>
                        <option value="" selected disabled>Select Gender</option>
                        <option value="male" {{ old('gender') == 'male' ? 'selected' : '' }}>Male</option>
                        <option value="female" {{ old('gender') == 'female' ? 'selected' : '' }}>Female</option>
                    </select>
                    @error('gender')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            <!-- Membership Section -->
            <div class="p-6 border-t border-b border-gray-200 bg-gray-50">
                <h2 class="text-xl font-semibold text-gray-800">Membership Details</h2>
            </div>
            
            <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="membershipType" class="block text-gray-700 font-medium mb-2">Membership Type *</label>
                    <select id="membershipType" name="membership_type" class="w-full px-4 py-3 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent" required>
                        <option value="" selected disabled>Select Membership Type</option>
                        <option value="1" {{ old('membership_type') == '1' ? 'selected' : '' }}>Session (1 day)</option>
                        <option value="7" {{ old('membership_type') == '7' ? 'selected' : '' }}>Weekly (7 days)</option>
                        <option value="30" {{ old('membership_type') == '30' ? 'selected' : '' }}>Monthly (30 days)</option>
                        <option value="365" {{ old('membership_type') == '365' ? 'selected' : '' }}>Annual (365 days)</option>
                    </select>
                    @error('membership_type')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>

                <div>
                    <label for="startDate" class="block text-gray-700 font-medium mb-2">Start Date *</label>
                    <input type="date" id="startDate" name="start_date" class="w-full px-4 py-3 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent" value="{{ old('start_date') }}" required>
                    @error('start_date')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>

                <div>
                    <label for="expiryDate" class="block text-gray-700 font-medium mb-2">Expiration Date</label>
                    <div class="relative">
                        <input type="text" id="expiryDate" name="expiry_date" placeholder="dd/mm/yyyy" class="w-full px-4 py-3 border border-gray-300 rounded-md bg-gray-100" readonly>
                        <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                        </div>
                    </div>
                </div>

                <div>
                    <label for="uid" class="block text-gray-700 font-medium mb-2">RFID Card *</label>
                    <div class="relative">
                        <input id="uid" name="uid" class="w-full px-4 py-3 border border-gray-300 rounded-md bg-gray-100" placeholder="Tap your RFID card on the reader" readonly />
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
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            <!-- Account Section -->
            <div class="p-6 border-t border-gray-200 bg-gray-50">
                <h2 class="text-xl font-semibold text-gray-800">Account Setup</h2>
            </div>
                <div class="p-6">
                    <label for="email" class="block text-gray-700 font-medium mb-2">Email Address *</label>
                    <input type="email" id="email" name="email" class="w-full px-4 py-3 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent" value="{{ old('email') }}" required>
                    @error('email')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>
            <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="password" class="block text-gray-700 font-medium mb-2">Password *</label>
                    <div class="relative">
                        <input type="password" id="password" name="password" class="w-full px-4 py-3 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent" required>
                    </div>
                    @error('password')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>

                <div>
                    <label for="password_confirmation" class="block text-gray-700 font-medium mb-2">Confirm Password *</label>
                    <div class="relative">
                        <input type="password" id="password_confirmation" name="password_confirmation" class="w-full px-4 py-3 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent" required>
                      
                    </div>
                </div>
            </div>

            <!-- Submit Button -->
            <div class="p-6 border-t border-gray-200">
                <button type="submit" class="w-full bg-indigo-600 text-white py-3 px-6 rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                    Register Member
                </button>
            </div>
        </form>
    </div>
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
@endsection