
@extends('layouts.app')

@section('content')
        
        <!-- Main Content Area -->
        <div class="w-150 relative overflow-hidden">
        <div class="absolute mt-40 w-screen h-32 bg-gradient-to-r from-gray-800 to-gray-500 rounded-3xl flex items-center"></div>
    
            
            <!-- Form Container -->
            <div class="relative z-10 pt-8 px-12 pb-8 mt-40  ">
            <h2 class="text-4xl font-bold text-gray-300 mb-20">Membership Registration</h2>

            <form action="{{ route('staff.membershipRegistration') }}" method="POST" class="grid grid-cols-1 lg:grid-cols-2 gap-6 bg-white p-8 rounded-xl shadow-lg">
    @csrf
    <!-- Left Column -->
    <div class="space-y-6">
        <!-- First Name -->
        <div>
            <input type="text" id="first_name" placeholder="First Name" name="first_name" class="w-full px-4 py-3 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-gray-500 focus:border-transparent" required>
        </div>

        <!-- Email -->
        <div>
            <input type="email" id="email" placeholder="Email" name="email" class="w-full px-4 py-3 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-gray-500 focus:border-transparent" required>
        </div>
        <!-- Phone Number -->
        <div>
            <input type="tel" id="phoneNumber" placeholder="Phone Number" name="phone_number" class="w-full px-4 py-3 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-gray-500 focus:border-transparent" pattern="\d{11}" maxlength="11" required oninput="this.value = this.value.replace(/\D/g, '')">
            <span id="phoneError" class="text-red-500 text-sm hidden">Phone number must be 11 digits.</span>
        </div>
        <!-- Start Date -->
        <div>
            <label for="startDate" class="block text-gray-700 font-medium mb-2">Start Date *</label>
            <input type="date" id="startDate" name="start_date" class="w-full px-4 py-3 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-gray-500 focus:border-transparent" required>
        </div>


                <!-- RFID UID -->
        <div>
            <input type="text" id="rfidUID" name="rfid_uid" placeholder="RFID UID" class="w-full px-4 py-3 border border-gray-300 rounded-md bg-gray-100 cursor-not-allowed" readonly>
        </div>
    </div>

    <!-- Right Column -->
    <div class="space-y-6">
        <!-- Last Name -->
        <div>
            <input type="text" id="last_name" placeholder="Last Name" name="last_name" class="w-full px-4 py-3 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-gray-500 focus:border-transparent" required>
        </div>

        <!-- Gender Dropdown -->
        <div>
            <select id="gender" name="gender" class="w-full px-4 py-3 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-gray-500 focus:border-transparent text-gray-700" required>
                <option selected disabled>Select Gender</option>
                <option value="male">Male</option>
                <option value="female">Female</option>
            </select>
        </div>

        <!-- Membership Type -->
        <div>
                <select id="membershipType" name="membership_type" class="w-full px-4 py-3 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-gray-500 focus:border-transparent text-gray-700" required>
                    <option selected disabled>Select Membership Type</option>
                    <option value="1">Session</option>
                    <option value="7">Weekly</option>
                    <option value="30">Monthly</option>
                    <option value="365">Annual</option>
                </select>
        </div>

        <!-- Expiry Date -->
        <div>
            <label for="expiryDate" class="block text-gray-700 font-medium mb-2">Expiration Date</label>
            <input type="text" id="expiryDate" name="expiry_date" placeholder="dd/mm/yyyy" class="w-full px-4 py-3 border border-gray-300 rounded-md bg-gray-100 cursor-not-allowed" readonly>
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
    // Get the selected membership type and start date
    let membershipType = document.getElementById('membershipType').value;
    let startDateInput = document.getElementById('startDate').value;
    let expiryDateInput = document.getElementById('expiryDate');

    // Check if both fields are filled
    if (startDateInput && membershipType) {
        let startDate = new Date(startDateInput); // Convert start date to a Date object

        // Calculate expiry date based on membership type
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

        // Format the expiry date as "dd/mm/yyyy"
        let day = String(startDate.getDate()).padStart(2, '0'); // Ensure 2 digits for day
        let month = String(startDate.getMonth() + 1).padStart(2, '0'); // Ensure 2 digits for month
        let year = startDate.getFullYear();

        let formattedDate = `${day}/${month}/${year}`; // Format as "dd/mm/yyyy"
        expiryDateInput.value = formattedDate; // Set the expiry date input value
    } else {
        expiryDateInput.value = ''; // Clear the expiry date if inputs are incomplete
    }
}
    </script>

    <!-- JavaScript for RFID UID Input -->
    <script>
        // Simulate RFID reader input (replace this with actual RFID reader integration)
        document.addEventListener('DOMContentLoaded', function () {
            const rfidInput = document.getElementById('rfidUID');

            // Simulate RFID card tap
            function simulateRFIDTap(uid) {
                rfidInput.value = uid;
            }

            // Example: Simulate a card tap after 3 seconds
            setTimeout(() => {
                simulateRFIDTap(''); 
            }, 3000);

            // For actual RFID integration, you would use a library or API provided by the RFID reader manufacturer.
            // Example:
            // rfidReader.on('data', function (uid) {
            //     rfidInput.value = uid;
            // });
        });
         // Function to fetch the RFID UID from the server
    function fetchRFIDUID() {
        fetch('/api/rfid-uid') // Create this endpoint in the next step
            .then(response => response.json())
            .then(data => {
                if (data.rfid_uid) {
                    // Update the RFID UID input field
                    document.getElementById('rfidUID').value = data.rfid_uid;
                }
            })
            .catch(error => console.error('Error fetching RFID UID:', error));
    }

    // Fetch the RFID UID every 2 seconds
    setInterval(fetchRFIDUID, 2000);
    </script>
 @endsection
