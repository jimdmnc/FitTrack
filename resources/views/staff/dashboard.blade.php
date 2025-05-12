@extends('layouts.app') <!-- Assuming you have a main layout file -->

@section('content')
    <link href="https://cdnjs.cloudflare.com/ajax/libs/tailwindcss/2.2.19/tailwind.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.7.1/chart.min.js"></script>
    <style>
        .glass-card {
            background: #1e1e1e;
            backdrop-filter: blur(10px);
            border-radius: 16px;
            transition: all 0.3s ease;
        } 
        .glass-card:hover { 
            box-shadow: 0 8px 20px rgba(31, 38, 135, 0.15);
            transform: translateY(-5px);
        }
        .glass-card1 {
            background: #1e1e1e;
            backdrop-filter: blur(10px);
            border-radius: 8px;
            border-left: 8px solid #EA580C;
            transition: all 0.3s ease;
        }
        .glass-card1:hover { 
            box-shadow: 0 8px 20px rgba(31, 38, 135, 0.15);
            transform: translateY(-5px);
        }
        .gradient-bg {
            background: linear-gradient(120deg, #a1c4fd 0%, #c2e9fb 100%);
        }
        body {
            background-color: #121212;
            color: white;
        }
        .chart-container {
            position: relative;
            height: 320px;
            width: 100%;
        }
        /* Modern dashboard grid layout */
        .dashboard-grid {
            display: grid;
            grid-template-columns: repeat(12, 1fr);
            gap: 24px;
            margin-bottom: 30px;
        }
        .grid-col-span-8 {
            grid-column: span 8;
        }
        .grid-col-span-4 {
            grid-column: span 4;
        }
        .grid-col-span-12 {
            grid-column: span 12;
        }
        .grid-col-span-6 {
            grid-column: span 6;
        }
        
        /* Chart interaction styles */
        .chart-action-buttons {
            display: flex;
            gap: 8px;
            opacity: 0;
            transition: opacity 0.3s ease;
        }
        .chart-card:hover .chart-action-buttons {
            opacity: 1;
        }
        .chart-action-button {
            padding: 6px;
            border-radius: 8px;
            background: #121212;
            color: #FF5722;
            cursor: pointer;
            transition: all 0.2s ease;
        }
        .chart-action-button:hover {
            background: #1e1e1e;
            transform: scale(1.05);
        }
        
        /* Chart tooltip customization */
        .period-selector {
            display: flex;
            gap: 8px;
            margin-bottom: 12px;
        }
        .period-button {
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 12px;
            background: #121212;
            color: white;
            cursor: pointer;
            transition: all 0.2s ease;
        }
        .period-button.active {
            background: #FF5722;
            color: white;
        }
        .period-button:hover:not(.active) {
            background: rgba(228, 228, 250, 1);
        }
        
        /* Chart legend customization */
        .custom-legend {
            display: flex;
            flex-wrap: wrap;
            gap: 16px;
            margin-top: 16px;
        }
        .legend-item {
            display: flex;
            align-items: center;
            gap: 8px;
            cursor: pointer;
            padding: 4px 8px;
            border-radius: 6px;
            transition: all 0.2s ease;
        }
        .legend-item:hover {
            background: rgba(35, 35, 36, 0.9);
        }
        .legend-color {
            width: 12px;
            height: 12px;
            border-radius: 3px;
        }
        .legend-text {
            font-size: 12px;
            color: #4b5563;
        }
        
        /* Responsive adjustments */
        @media (max-width: 1024px) {
            .dashboard-grid {
                grid-template-columns: 1fr;
            }
            .grid-col-span-8, .grid-col-span-4, .grid-col-span-6 {
                grid-column: span 1;
            }
        }
        
        /* Additional visual enhancements */
        .animate-pulse {
            animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
        }
        @keyframes pulse {
            0%, 100% {
                opacity: 1;
            }
            50% {
                opacity: 0.7;
            }
        }
        
        /* Enhanced stats card */
        .stat-card-icon {
            transition: all 0.3s ease;
        }
        .glass-card:hover .stat-card-icon {
            transform: scale(1.15);
        }
        
        /* Enhanced table */
        .member-table-row {
            transition: all 0.2s ease;
        }
        .member-table-row:hover {
            background-color: #121212;
            transform: translateX(4px);
        }
        
        /* Enhanced button effects */
        .btn-primary {
            background: linear-gradient(135deg, #4f46e5 0%, #4338ca 100%);
            border: none;
            transition: all 0.3s ease;
        }
        .btn-primary:hover {
            background: linear-gradient(135deg, #4338ca 0%, #3730a3 100%);
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(79, 70, 229, 0.2);
        }
        
        /* Scrollbar styles */
        ::-webkit-scrollbar {
            width: 8px;
            height: 8px;
        }
        ::-webkit-scrollbar-track {
            background: rgba(241, 245, 249, 0.8);
            border-radius: 10px;
        }
        ::-webkit-scrollbar-thumb {
            background: rgba(148, 163, 184, 0.5);
            border-radius: 10px;
        }
        ::-webkit-scrollbar-thumb:hover {
            background: rgba(148, 163, 184, 0.8);
        }


        
    </style>
    <div class="container mx-auto py-8 px-4">
        <!-- Header Section with Modern Design -->
        <div class="mb-8">
            <div class="flex flex-col md:flex-row justify-between items-center">
                <div>
                    <h1 class="text-3xl md:text-4xl font-bold bg-clip-text text-transparent bg-gradient-to-r from-red-600 to-orange-600">
                    Dashboard
                    </h1>
                    <p class="text-gray-200 mt-2">Track and analyze the gym's performance</p>
                </div>
        <!-- Right Section: Search, Feedback, Notifications, and Create Announcement -->
        <div class="flex items-center space-x-2">
            <button id="openModalBtn" class="bg-[#FF5722] text-white px-4 py-2 rounded-full hover:bg-[#e64a19] transition-all duration-300">
                Create Announcement
            </button>
        </div>
            </div>
        </div>
    </div>







    <div id="createAnnouncementModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 flex items-center justify-center hidden z-50">
    <div class="bg-white rounded-lg shadow-lg p-6 w-full max-w-md">
        <h2 class="text-xl font-bold mb-4 text-gray-800">Create Announcement</h2>
        <form action="{{ route('admin.announcements.store') }}" method="POST">
            @csrf
            <div class="mb-4">
                <label for="title" class="block text-sm font-medium text-gray-700">Title</label>
                <input type="text" name="title" id="title" class="w-full border rounded p-2 focus:ring-[#FF5722] focus:border-[#FF5722]" required>
            </div>
            <div class="mb-4">
                <label for="content" class="block text-sm font-medium text-gray-700">Content</label>
                <textarea name="content" id="content" class="w-full border rounded p-2 focus:ring-[#FF5722] focus:border-[#FF5722]" required></textarea>
            </div>
            <div class="mb-4">
                <label for="schedule" class="block text-sm font-medium text-gray-700">Schedule (Optional)</label>
                <input type="datetime-local" name="schedule" id="schedule" class="w-full border rounded p-2 focus:ring-[#FF5722] focus:border-[#FF5722]">
            </div>
            <div class="mb-4">
                <label for="type" class="block text-sm font-medium text-gray-700">Type</label>
                <select name="type" id="type" class="w-full border rounded p-2 focus:ring-[#FF5722] focus:border-[#FF5722]" required>
                    <option value="Maintenance">Maintenance</option>
                    <option value="Event">Event</option>
                    <option value="Update">Update</option>
                </select>
            </div>
            <div class="flex justify-end space-x-2">
                <button type="button" id="closeModalBtn" class="bg-gray-300 text-gray-700 px-4 py-2 rounded hover:bg-gray-400 transition">Cancel</button>
                <button type="submit" class="bg-[#FF5722] text-white px-4 py-2 rounded hover:bg-[#e64a19] transition">Save</button>
            </div>
        </form>
    </div>
</div>




        
        <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">

        <!-- New Members Card -->
            <div class="glass-card1 p-4">
                <div class="flex justify-between items-start">
                    <div class="w-2/3"> <!-- This sets the main content to take up 70% of the card -->
                        <h3 class="text-gray-200 text-sm font-medium uppercase tracking-wider mb-2">
                            New <br>Members
                        </h3>
                        <div class="flex items-baseline">
                            <div class="text-3xl font-bold text-gray-200 mr-2">
                                {{ $newMembersData['currentWeekNewMembers'] }}
                            </div>
                            <span class="text-lg text-gray-200">members</span>
                        </div>

                        <!-- Status Indicator (Green for Increase, Red for Decrease) -->
                        <div class="mt-3 px-4 py-1 inline-flex items-center rounded-full 
                            {{ $newMembersData['isIncrease'] ? 'text-green-400' : 'text-red-400' }}">
                            <i class="fas {{ $newMembersData['isIncrease'] ? 'fa-arrow-up' : 'fa-arrow-down' }} w-4 h-4 mr-1"></i>
                            <span class="text-sm font-medium">
                                {{ str_replace(['▲', '▼'], '', $newMembersData['formattedPercentageChange']) }}
                            </span>
                        </div>
                    </div>

                    <!-- Icon on the Right Side (Takes up 30% width) -->
                    <div class="w-1/3 flex items-center justify-center text-orange-500">
                        <i class="fas fa-user-plus text-5xl"></i> <!-- Adjust icon size if necessary -->
                    </div>
                </div>
            </div>

            <!-- Today's Check-ins Card -->
            <div class="glass-card1 p-3">
                <div class="flex justify-between items-start">
                    <div class="w-2/3">
                        <h3 class="text-gray-200 text-sm font-medium uppercase tracking-wide mb-2">
                            Today's <br>Check-ins
                        </h3>
                        <div class="flex items-baseline">
                            <div class="text-3xl font-bold text-gray-200 mr-2">
                                {{ $todaysCheckInsData['todaysCheckIns'] }}
                            </div>
                            <span class="text-lg text-gray-200">members</span>
                        </div>

                        <!-- Status Indicator (Green for Increase, Red for Decrease) -->
                        <div class="mt-3 px-4 py-1 inline-flex items-center rounded-full 
                            {{ $todaysCheckInsData['isIncrease'] ? 'text-green-400' : 'text-red-400' }}">
                            <i class="fas {{ $todaysCheckInsData['isIncrease'] ? 'fa-arrow-up' : 'fa-arrow-down' }} w-4 h-4 mr-1"></i>
                            <span class="text-sm font-medium">
                                {{ str_replace(['▲', '▼'], '', $todaysCheckInsData['formattedPercentageChange']) }}
                            </span>
                        </div>
                    </div>

                    <!-- Icon on the Right Side (Takes up 30% width) -->
                    <div class="w-1/3 flex items-center justify-center mr-4">
                        <i class="fas fa-check-circle text-5xl text-orange-500"></i>
                    </div>
                </div>
            </div>

            <!-- Soon to Expire Card -->
            <div class="glass-card1 p-3">
                <div class="flex justify-between items-start">
                    <div class="w-2/3">
                        <h3 class="text-gray-200 text-sm font-medium uppercase tracking-wide mb-2">Memberships <br> Expiring Soon</h3>
                        <div class="flex items-baseline">
                            <div class="text-3xl font-bold text-gray-200 mr-2">{{ $expiringMemberships }}</div>
                            <span class="text-lg text-gray-200">members</span>
                        </div>
                        <a href="{{ route('staff.viewmembers') }}" class="mt-3 px-3 py-1.5 text-[#FF5722] rounded-md inline-flex items-center group transition-all duration-200 hover: hover:text-orange-400 hover:translate-y-[-2px]">
                            <span class="text-sm font-medium">Manage Renewals</span>
                            <i class="fas fa-arrow-right ml-2 transform group-hover:translate-x-1 transition-transform duration-200"></i>
                        </a>
                    </div>

                    <!-- Icon on the Right Side (Takes up 30% width) -->
                    <div class="w-1/3 flex items-center justify-center ext-red-900">
                        <i class="fas fa-calendar-times text-5xl text-orange-500"></i>
                    </div>

                </div>
            </div>

    </div>


    <!-- Dashboard Grid Charts Section -->
    <div class="dashboard-grid grid md:grid-cols-12 gap-4">
        <!-- Enhanced Check-ins Chart Card -->
        <div class="glass-card p-4 col-span-12 md:col-span-8 chart-card space-y-4 rounded-xl" id="checkinsChartCard">
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-2">
                <div class="mb-2 sm:mb-0">
                    <h3 class="text-lg font-semibold text-gray-200" id="h3">Daily Check-ins</h3>
                    <p class="text-xs text-gray-200">Number of members visiting the gym</p>
                </div>
                <div class="flex space-x-2">
                    <button class="chart-action-button bg-white p-2 rounded-full shadow-sm hover:bg-gray-50 expand-checkins-btn" 
                        title="Expand">
                        <i class="fas fa-expand-alt text-sm"></i>
                    </button>
                </div>
            </div>
            
            <!-- Improved period selector -->
            <div class="period-selector flex flex-wrap p-1 rounded-lg w-fit">
                <button class="period-button active rounded-md px-3 py-1.5 text-sm font-medium transition-all" data-period="daily">Daily</button>
                <button class="period-button rounded-md px-3 py-1.5 text-sm font-medium transition-all" data-period="weekly">Weekly</button>
                <button class="period-button rounded-md px-3 py-1.5 text-sm font-medium transition-all" data-period="monthly">Monthly</button>
                <button class="period-button rounded-md px-3 py-1.5 text-sm font-medium transition-all" data-period="yearly">Yearly</button>
            </div>
            
            <!-- Summary stats above chart -->
            <div class="stats-summary grid grid-cols-1 sm:grid-cols-3 gap-4 mb-2">
                <div class="stat-card bg-[#1e1e1e] p-3">
                    <p class="text-xs text-gray-200">Total Check-ins</p>
                    <h4 class="text-xl font-bold text-gray-200" id="total-checkins">0</h4>
                </div>
                <div class="stat-card bg-[#1e1e1e] p-3">
                    <p class="text-xs text-gray-200">Average</p>
                    <h4 class="text-xl font-bold text-gray-200" id="avg-checkins">0</h4>
                </div>
                <div class="stat-card bg-[#1e1e1e] p-3">
                    <p class="text-xs text-gray-200">Peak Day</p>
                    <h4 class="text-xl font-bold text-gray-200" id="peak-checkins">0</h4>
                </div>
            </div>
            
            <!-- Chart Container with loading indicator -->
            <div class="chart-container relative bg-[#1e1e1e] p-4 rounded-lg shadow-sm" id="checkinsChartContainer" style="height: 300px;">
                <div id="chart-loading" class="absolute inset-0 flex items-center justify-center bg-white bg-opacity-80 z-10 hidden">
                    <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-[#FF5722]"></div>
                </div>
                <canvas id="checkins-chart"></canvas>
            </div>
            
            <!-- Legend -->
            <div class="flex items-center justify-center flex-wrap space-x-6 text-xs text-gray-200">
                <div class="flex items-center mt-2">
                    <span class="inline-block w-3 h-3 mr-1 bg-orange-400 rounded-sm"></span>
                    <span>Check-ins</span>
                </div>
                <div class="flex items-center mt-2">
                    <span class="inline-block w-3 h-3 mr-1 border border-dashed border-orange-200 rounded-sm"></span>
                    <span>Previous Period</span>
                </div>
            </div>
        </div>
        
        <!-- Right side panels container -->
        <div class="col-span-12 md:col-span-4 flex flex-col justify-between gap-4">
            <!-- Top right panel -->
            <div class="glass-card p-2 chart-card relative" id="chartCard">
                <div class="flex justify-between items-center mb-3">
                    <div>
                        <h3 class="text-base font-semibold text-gray-200">Peak hours</h3>
                        <p class="text-xs text-gray-200">Average Time by Hour</p>
                    </div>
                    <div class="chart-action-buttons space-x-1">
                        <div class="chart-action-button expand-peak-btn" title="Expand">
                            <i class="fas fa-expand-alt text-sm"></i>
                        </div>
                    </div>
                </div>

                <div class="chart-container transition-all duration-300 ease-in-out" id="chartContainer" style="height: 180px;">
                    <canvas id="time-of-day-chart"></canvas>
                </div>
            </div>
            
            <!-- Bottom right panel (Subscribers Chart) -->
            <div class="glass-card p-3 chart-card bg-white shadow-md rounded-lg relative" id="subscribersChartCard">
                <div class="flex justify-between items-center mb-4">
                    <div>
                        <h3 class="text-base font-semibold text-gray-200">Subscribers</h3>
                        <p class="text-sm text-gray-200">Ongoing Memberships</p>
                    </div>
                    <div class="chart-action-buttons space-x-2 flex items-center">
                        <div class="chart-action-button expand-subscribers-btn cursor-pointer" title="Expand">
                            <i class="fas fa-expand-alt text-sm"></i>
                        </div>
                    </div>
                </div>

                <div class="relative w-full transition-all duration-300 ease-in-out" id="subscribersChartContainer" style="height: 190px;">
                    <canvas id="membershipChart"></canvas>
                </div>
            </div>
        </div>
    </div>



<!-- Members Table with Modern Design - now placed below session distribution and wider -->
<div class="glass-card p-6 grid-col-span-8 mt-6">
    <div class="flex justify-between items-center mb-6">
    <div>
        <h3 class="text-xl font-bold text-gray-200">Top Active Members</h3>
        <p class="text-sm text-gray-200 mt-1">Most frequent check-ins and their membership details</p>
    </div>

        <div class="flex items-center gap-3">


        </div>
    </div>
    <div class="overflow-x-auto">
    <table class="min-w-full divide-y divide-gray-800">
        <thead>
            <tr class="bg-gradient-to-br from-[#2c2c2c] to-[#1e1e1e]">
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">#</th>
                <!-- <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Member ID</th> -->
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Name</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Membership Type</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Status</th>
                <th class="px-10 py-3 text-center text-xs font-medium text-gray-300 uppercase tracking-wider">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-800">
        @foreach ($topActiveMembers as $index => $member)
            <tr class="bg-[#2c2c2c] transition-colors duration-150">
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-300">{{ $loop->iteration }}</td>
                <!-- <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-300">{{ $member->rfid_uid }}</td> -->
                <td class="px-6 py-4 whitespace-nowrap">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 h-10 w-10 bg-gray-500 rounded-full flex items-center justify-center mr-3">
                            <span class="text-gray-100 font-medium">
                                {{ strtoupper(substr($member->first_name, 0, 1)) }}{{ strtoupper(substr($member->last_name, 0, 1)) }}
                            </span>
                        </div>
                        <div>
                            <div class="text-sm font-medium text-gray-200">{{ $member->first_name }} {{ $member->last_name }}</div>
                        </div>
                    </div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                    <span class="px-2.5 py-1 inline-flex text-xs leading-5 font-semibold rounded-full 
                        @if($member->getMembershipType() == 'Annual') bg-purple-900 text-purple-200
                        @elseif($member->getMembershipType() == 'Week') bg-green-900 text-green-200
                        @elseif($member->getMembershipType() == 'Month') bg-blue-900 text-blue-200
                        @elseif($member->getMembershipType() == 'Session') bg-yellow-900 text-yellow-200
                        @endif">
                        {{ $member->getMembershipType() }}
                    </span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                    <span class="px-2.5 py-1 inline-flex text-xs leading-5 font-semibold rounded-full 
                        {{ $member->member_status == 'active' ? ' text-green-500' : ' text-red-500' }}">
                        {{ $member->member_status }}
                    </span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                    <button 
                        onclick="openViewModal('{{ $member->rfid_uid }}', '{{ $member->first_name }} {{ $member->last_name }}', '{{ $member->getMembershipType() }}', '{{ \Carbon\Carbon::parse($member->start_date)->format('M d, Y') }}', '{{ $member->member_status }}')"
                        class="inline-flex items-center px-3 py-1.5 border border-[#ff5722] rounded-md text-gray-200 bg-transparent hover:bg-[#ff5722] hover:text-gray-200 hover:translate-y-[-2px] transition-colors duration-150"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                        </svg>
                        View
                    </button>
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
</div>
    

</div>

        <!-- View Member Modal -->
        <div id="viewMemberModal" class="fixed inset-0 bg-black bg-opacity-80 flex justify-center items-center hidden z-50 transition-opacity duration-300 overflow-y-auto px-4 py-6">
            <div class="bg-[#1e1e1e] rounded-2xl shadow-2xl w-full max-w-3xl p-4 sm:p-6 md:p-8 transform transition-all duration-300 scale-95 opacity-0 my-4" id="viewModalContent">
                <!-- Modal Header -->
                <div class="flex justify-between items-center mb-4 md:mb-6">
                    <h2 class="text-xl sm:text-2xl font-bold text-white flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 sm:h-7 sm:w-7 mr-2 sm:mr-3 text-orange-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                        Member Profile
                    </h2>
                    <button onclick="closeViewModal()" class="text-gray-300 hover:text-gray-200 hover:bg-[#ff5722] hover:scale-95 rounded-full p-2 transition-colors duration-200">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <!-- Modern Card Design -->
                <div class="bg-[#2c2c2c] rounded-xl overflow-hidden">
                    <!-- Card Header -->
                    <div class="bg-gradient-to-r from-[#2c2c2c] to-[#1e1e1e] py-3 px-4 sm:py-4 sm:px-6 rounded-t-xl shadow-lg">
                        <div class="flex justify-between items-center">
                            <h3 class="font-bold text-white text-base sm:text-lg tracking-wider">MEMBER IDENTIFICATION</h3>
                            <div class="px-2 py-1 rounded-full">
                                <span id="viewStatus" class="text-xs sm:text-sm font-semibold text-green-200">Active</span>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Modern Layout - Responsive stacking -->
                    <div class="flex flex-col lg:flex-row">
                        
                        <!-- Avatar Section - Full width on small screens -->
                        <div class="w-full lg:w-1/4 p-4 sm:p-6 flex flex-col items-center justify-center bg-[#2c2c2c] border-transparent">
                            <div class="w-24 h-24 sm:w-32 sm:h-32 bg-[#444444] rounded-full flex items-center justify-center border-2 border-orange-500 shadow-lg">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 sm:h-16 sm:w-16 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                            </div>
                            <div class="w-full text-center mt-3 sm:mt-4">
                                <p class="text-xs text-gray-400">Profile Image</p>
                            </div>
                        </div>
                        
                        <!-- Primary Info Section -->
                        <div class="w-full lg:w-2/5 p-4 sm:p-6 bg-[#1e1e1e] flex flex-col justify-between border-t border-b lg:border-t-0 lg:border-b-0 border-[#333333] lg:border-l lg:border-r">
                            <!-- Name -->
                            <div class="mb-3 sm:mb-5">
                                <p class="text-xs text-gray-400 uppercase tracking-wider mb-1">Name</p>
                                <p class="font-bold text-white text-lg sm:text-xl" id="viewMemberName">John Doe</p>
                            </div>
                            
                            <!-- Membership Type -->
                            <div class="mb-3 sm:mb-5">
                                <p class="text-xs text-gray-400 uppercase tracking-wider mb-1">Membership Type</p>
                                <div class="bg-orange-600 text-gray-200 inline-block px-3 py-1 rounded-lg text-sm">
                                    <p class="font-medium" id="viewMembershipType">Monthly</p>
                                </div>
                            </div>
                            
                            <!-- registration date -->
                            <div class="mb-2 sm:mb-5">
                                <p class="text-xs text-gray-400 uppercase tracking-wider">Issued Date</p>
                                <div class="flex items-center mt-2">
                                    <div class="bg-orange-500 bg-opacity-20 p-2 rounded-lg mr-3">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 sm:h-5 sm:w-5 text-orange-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                        </svg>
                                    </div>
                                    <div>
                                        <p class="font-medium text-gray-200" id="viewStartDate">Jan 1, 2025</p>
                                    </div>                           
                                </div>                      
                            </div>
                        </div>
                        
                        <!-- RFID Card Section -->
                        <div class="w-full lg:w-1/3 p-4 sm:p-6 bg-[#2c2c2c] flex flex-col justify-between">
                            <!-- RFID Card Area -->
                            <div class="mb-3 sm:mb-5">
                                <p class="text-xs text-gray-400 uppercase tracking-wider mb-2">RFID Card</p>
                                <div class="bg-[#1e1e1e] rounded-lg p-2 sm:p-3 shadow-inner">
                                    <div class="flex items-center mb-2">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 sm:h-6 sm:w-6 text-orange-400 mr-2" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                            <rect x="3" y="5" width="18" height="14" rx="2" ry="2" stroke-width="1.5" />
                                            <path d="M7 15a4 4 0 010-6" stroke-width="1.5" />
                                            <path d="M11 13a2 2 0 010-2" stroke-width="1.5" />
                                            <line x1="17" y1="9" x2="17" y2="9" stroke-width="2" stroke-linecap="round" />
                                            <line x1="17" y1="15" x2="17" y2="15" stroke-width="2" stroke-linecap="round" />
                                        </svg>
                                        <span class="text-xs sm:text-sm font-medium text-gray-300">RFID UID</span>
                                    </div>
                                    <div class="bg-[#121212] bg-opacity-50 p-2 rounded flex items-center justify-between">
                                        <span id="viewRfid" class="text-xs sm:text-sm font-medium text-gray-300 truncate pr-2">ID: 123456789</span>
                                        <div class="flex space-x-1">
                                            <div class="w-1 h-6 sm:h-8 bg-[#444444] rounded"></div>
                                            <div class="w-1 h-6 sm:h-8 bg-[#555555] rounded"></div>
                                            <div class="w-1 h-6 sm:h-8 bg-[#444444] rounded"></div>
                                            <div class="w-1 h-6 sm:h-8 bg-[#555555] rounded"></div>
                                            <div class="w-1 h-6 sm:h-8 bg-[#444444] rounded"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- expiration date -->
                            <div class="mb-2 sm:mb-5">
                                <p class="text-xs text-gray-400 uppercase tracking-wider">Expiration Date</p>
                                <div class="flex items-center mt-2">                           
                                    <div class="bg-orange-500 bg-opacity-20 p-2 rounded-lg mr-3">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 sm:h-5 sm:w-5 text-orange-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                        </svg>
                                    </div>
                                    <div>
                                        <p class="font-medium text-white" id="viewEndDate">Jan 15, 2025</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Footer -->
                    <div class="bg-gradient-to-r from-[#2c2c2c] to-[#1e1e1e] text-gray-400 border-t border-[#333333] py-2 sm:py-3 px-4 sm:px-6 flex justify-between items-center">
                        <p class="text-xs text-gray-300 mx-auto">Valid only upon registration</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Edit Member Modal -->
        <div id="editMemberModal" class="fixed inset-0 bg-gray-900 bg-opacity-70 flex justify-center items-center hidden z-50 transition-opacity duration-300 overflow-y-auto p-4">
            <div class="bg-white rounded-xl shadow-2xl w-full max-w-md p-4 sm:p-6 my-4 transform transition-all duration-300 scale-95 opacity-0" id="editModalContent">
                <!-- Modal Header -->
                <div class="flex justify-between items-center mb-4 sm:mb-6 border-b pb-3">
                    <h2 class="text-lg sm:text-xl font-bold text-gray-800 flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 sm:h-6 sm:w-6 mr-2 text-orange-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                        </svg>
                        Edit Member
                    </h2>
                    <button onclick="closeEditModal()" class="text-gray-500 hover:text-gray-700 hover:bg-gray-100 rounded-full p-1 transition-colors">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 sm:h-6 sm:w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <!-- Edit Form -->
                <form id="editMemberForm">
                    <div class="mb-3 sm:mb-4">
                        <label class="block text-xs sm:text-sm font-medium text-gray-700 mb-1">Member ID</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 sm:h-5 sm:w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0" />
                                </svg>
                            </div>
                            <input type="text" id="editMemberID" class="w-full pl-10 pr-3 py-2 text-sm sm:text-base border border-gray-300 rounded-lg bg-gray-100 text-gray-500" readonly>
                        </div>
                    </div>

                    <div class="mb-3 sm:mb-4">
                        <label class="block text-xs sm:text-sm font-medium text-gray-700 mb-1">Name</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 sm:h-5 sm:w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                            </div>
                            <input type="text" id="editMemberName" class="w-full pl-10 pr-3 py-2 text-sm sm:text-base border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-colors">
                        </div>
                    </div>

                    <div class="mb-3 sm:mb-4">
                        <label class="block text-xs sm:text-sm font-medium text-gray-700 mb-1">Membership Type</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 sm:h-5 sm:w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                                </svg>
                            </div>
                            <select id="editMembershipType" class="w-full pl-10 pr-8 py-2 text-sm sm:text-base border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-colors appearance-none">
                                <option value="Annual">Annual</option>
                                <option value="Month">Monthly</option>
                                <option value="Week">Weekly</option>
                                <option value="Session">Per Session</option>
                            </select>
                            <div class="absolute inset-y-0 right-0 flex items-center px-2 pointer-events-none">
                                <svg class="h-4 w-4 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3 sm:mb-4">
                        <label class="block text-xs sm:text-sm font-medium text-gray-700 mb-1">Status</label>
                        <div class="flex flex-col sm:flex-row sm:items-center gap-2 sm:gap-4">
                            <label class="flex items-center">
                                <input type="radio" name="status" value="active" class="h-4 w-4 sm:h-5 sm:w-5 text-orange-600 focus:ring-orange-500 cursor-pointer">
                                <div class="ml-2 flex items-center">
                                    <span class="inline-flex h-2 w-2 sm:h-3 sm:w-3 bg-green-500 rounded-full mr-1.5"></span>
                                    <span class="text-sm sm:text-base">Active</span>
                                </div>
                            </label>
                            <label class="flex items-center">
                                <input type="radio" name="status" value="expired" class="h-4 w-4 sm:h-5 sm:w-5 text-orange-600 focus:ring-orange-500 cursor-pointer">
                                <div class="ml-2 flex items-center">
                                    <span class="inline-flex h-2 w-2 sm:h-3 sm:w-3 bg-red-500 rounded-full mr-1.5"></span>
                                    <span class="text-sm sm:text-base">Expired</span>
                                </div>
                            </label>
                        </div>
                    </div>

                    <!-- Modal Footer with Save and Cancel Buttons -->
                    <div class="flex flex-col sm:flex-row sm:justify-end gap-2 mt-4 sm:mt-6">
                        <button type="button" onclick="saveChanges()" class="bg-orange-500 hover:bg-orange-600 text-white px-4 py-2 rounded-lg transition-colors shadow-sm flex items-center justify-center sm:justify-start order-2 sm:order-1 sm:mr-2">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            Save Changes
                        </button>
                        <button type="button" onclick="closeEditModal()" class="bg-gray-100 hover:bg-gray-200 text-gray-800 px-4 py-2 rounded-lg transition-colors shadow-sm order-1 sm:order-2">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
<script>
    document.addEventListener("DOMContentLoaded", function () {
          // Create a backdrop element
    const backdrop = document.createElement("div");
    backdrop.style.position = "fixed";
    backdrop.style.top = "0";
    backdrop.style.left = "0";
    backdrop.style.width = "100vw";
    backdrop.style.height = "100vh";
    backdrop.style.background = "rgba(0, 0, 0, 0.5)"; // 🔥 Semi-transparent black background
    backdrop.style.zIndex = "40"; // Behind the chart
    backdrop.style.display = "none"; // Initially hidden
    document.body.appendChild(backdrop);

        function toggleExpand(button, container, card, chart) {
            card.classList.toggle("expanded");

            if (card.classList.contains("expanded")) {
                backdrop.style.display = "block"; // Show backdrop

                card.style.position = "fixed";
                card.style.top = "50%";
                card.style.left = "50%";
                card.style.transform = "translate(-50%, -50%)";
                card.style.zIndex = "50";
                card.style.width = "100vw"; 
                card.style.height = "90vh"; 
                card.style.maxWidth = "1100px"; 
                card.style.maxHeight = "800px"; 
                card.style.background = "#1e1e1e";
                card.style.padding = "30px"; 
                card.style.borderRadius = "12px"; 
                card.style.overflow = "auto";  // 🔥 Make it scrollable when needed

                button.innerHTML = `<i class="fas fa-compress-alt text-sm"></i>`;

                // 🔥 Make the chart smaller inside the expanded card
                container.style.height = "400px";  // Change from 500px to 300px
                
                // Resize chart after expanding
                setTimeout(() => {
                    chart.resize();
                }, 500);
            } else {
                backdrop.style.display = "none"; // Hide backdrop when collapsed

                container.style.height = "190px"; // Reset height
                card.style = ""; // Reset styles
                button.innerHTML = `<i class="fas fa-expand-alt text-sm"></i>`;

                // Resize back when collapsed
                setTimeout(() => {
                    chart.resize();
                }, 300);
            }
        }
    // Close on backdrop click
    backdrop.addEventListener("click", function () {
        const expandedCard = document.querySelector(".expanded");
        if (expandedCard) {
            expandedCard.classList.remove("expanded");
            backdrop.style.display = "none"; // Hide backdrop
        }
    });
        // Get Chart.js instances
        const peakChart = Chart.getChart("time-of-day-chart"); 
        const subscribersChart = Chart.getChart("membershipChart"); 
        const checkinsChart = Chart.getChart("checkins-chart");  // Check-ins Chart


        // Expand Peak Hours Chart
        const expandPeakButton = document.querySelector(".expand-peak-btn");
        const peakChartContainer = document.getElementById("chartContainer");
        const peakChartCard = document.getElementById("chartCard");
        expandPeakButton.addEventListener("click", function () {
            toggleExpand(expandPeakButton, peakChartContainer, peakChartCard, peakChart);
        });

        // Expand Subscribers Chart
        const expandSubscribersButton = document.querySelector(".expand-subscribers-btn");
        const subscribersChartContainer = document.getElementById("subscribersChartContainer");
        const subscribersChartCard = document.getElementById("subscribersChartCard");
        expandSubscribersButton.addEventListener("click", function () {
            toggleExpand(expandSubscribersButton, subscribersChartContainer, subscribersChartCard, subscribersChart);
        });
        // Expand Check-ins Chart
        const expandCheckinsButton = document.querySelector(".expand-checkins-btn");
        const checkinsChartContainer = document.getElementById("checkinsChartContainer");
        const checkinsChartCard = document.getElementById("checkinsChartCard");
        expandCheckinsButton.addEventListener("click", function () {
            toggleExpand(expandCheckinsButton, checkinsChartContainer, checkinsChartCard, checkinsChart);
        });
    });

</script>

















<!-- =============================================Table============================================= -->
<script>


    // Open View Modal
    function openViewModal(rfid, name, membershipType, startDate, status) {
    // Set modal data
        document.getElementById('viewMemberName').textContent = name;
        document.getElementById('viewRfid').textContent = 'ID: ' + rfid;
        document.getElementById('viewMembershipType').textContent = membershipType;
        document.getElementById('viewStartDate').textContent = startDate;
        document.getElementById('viewStatus').textContent = status;

        // Change status color based on status
        let statusBadge = document.getElementById('viewStatus');
        if (status.toLowerCase() === 'active') {
            statusBadge.className = "text-sm font-semibold text-green-200";
            statusBadge.parentElement.className = "px-3 py-1 rounded-full bg-green-900";
        } else {
            statusBadge.className = "text-sm font-semibold text-red-200";
            statusBadge.parentElement.className = "px-3 py-1 rounded-full bg-red-900";
        }

        // Show modal
        const modal = document.getElementById('viewMemberModal');
        const modalContent = document.getElementById('viewModalContent');
        
        modal.classList.remove('hidden');
        setTimeout(() => {
            modalContent.classList.remove('scale-95', 'opacity-0');
            modalContent.classList.add('scale-100', 'opacity-100');
        }, 10);
    }

    // Function to close the modal
    function closeViewModal() {
        const modal = document.getElementById('viewMemberModal');
        const modalContent = document.getElementById('viewModalContent');

        // Animate closing
        modalContent.classList.remove('scale-100', 'opacity-100');
        modalContent.classList.add('scale-95', 'opacity-0');
        
        setTimeout(() => {
            modal.classList.add('hidden'); // Hide after animation
        }, 300);
    }

    // Close View Modal
    function closeViewModal() {
        const modal = document.getElementById('viewModalContent');
        modal.classList.remove('scale-100', 'opacity-100');
        modal.classList.add('scale-95', 'opacity-0');
        
        setTimeout(() => {
            document.getElementById("viewMemberModal").classList.add("hidden");

        }, 300);
    }

    // Open Edit Modal
    function openEditModal(memberID, name, membershipType, memberStatus) {

        // Set form values
        document.getElementById("editMemberID").value = memberID;
        document.getElementById("editMemberName").value = name;
        document.getElementById("editMembershipType").value = membershipType;

        // Ensure status value is lowercase for comparison
        let formattedStatus = memberStatus.trim().toLowerCase();

        // Set radio button based on status
        const radioButtons = document.getElementsByName('status');
        for (const radioButton of radioButtons) {
            if (radioButton.value.toLowerCase() === formattedStatus) {
                radioButton.checked = true;
                break;
            }
        }

        // Show modal with animation
        document.getElementById("editMemberModal").classList.remove("hidden");
        setTimeout(() => {
            document.getElementById('editModalContent').classList.remove('scale-95', 'opacity-0');
            document.getElementById('editModalContent').classList.add('scale-100', 'opacity-100');
        }, 10);
    }

    // Close Edit Modal
    function closeEditModal() {
        const modal = document.getElementById('editModalContent');
        modal.classList.remove('scale-100', 'opacity-100');
        modal.classList.add('scale-95', 'opacity-0');
        
        setTimeout(() => {
            document.getElementById("editMemberModal").classList.add("hidden");

        }, 300);
    }

    // Save Changes
    function saveChanges() {
        const memberId = document.getElementById('editMemberID').value;
        const memberName = document.getElementById('editMemberName').value;
        const membershipType = document.getElementById('editMembershipType').value;
        
        // Get selected status from radio buttons
        let status = '';
        const radioButtons = document.getElementsByName('status');
        for (const radioButton of radioButtons) {
            if (radioButton.checked) {
                status = radioButton.value;
                break;
            }
        }
        
        // Show success message
        const toast = document.createElement('div');
        toast.className = 'fixed bottom-4 right-4 bg-green-500 text-white px-4 py-2 rounded-lg shadow-lg flex items-center z-50';
        toast.innerHTML = `
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
            </svg>
            Changes saved successfully!
        `;
        document.body.appendChild(toast);
        
        // Remove toast after 3 seconds
        setTimeout(() => {
            toast.remove();
        }, 3000);
        
        // Close modal
        closeEditModal();
    }
</script>


<!-- ============================================= Check INs data is correctly passed from PHP============================================= -->
<script>
document.addEventListener("DOMContentLoaded", function () {
    // Get check-in data from Laravel
    var dailyCheckIns = @json($dailyCheckIns);
    var weeklyCheckIns = @json($weeklyCheckIns);
    var monthlyCheckIns = @json($monthlyCheckIns);
    var yearlyCheckIns = @json($yearlyCheckIns);

    function getChartData(dataSet) {
        return {
            labels: dataSet.map(item => item.date),
            dataCounts: dataSet.map(item => item.count)
        };
    }

    function updateSummaryStats(dataSet) {
        const counts = dataSet.map(item => item.count);
        const total = counts.reduce((sum, count) => sum + count, 0);
        const avg = Math.round(total / counts.length);
        const peak = Math.max(...counts);
        const peakDay = dataSet.find(item => item.count === peak)?.date || 'N/A';

        document.getElementById('total-checkins').textContent = total.toLocaleString();
        document.getElementById('avg-checkins').textContent = avg.toLocaleString();
        document.getElementById('peak-checkins').textContent = `${peak} (${peakDay})`;
    }

    function showLoading() {
        document.getElementById('chart-loading').classList.remove('hidden');
    }

    function hideLoading() {
        document.getElementById('chart-loading').classList.add('hidden');
    }

    // Initial dataset
    var { labels, dataCounts } = getChartData(dailyCheckIns);
    updateSummaryStats(dailyCheckIns);

    var myChart = new Chart(document.getElementById("checkins-chart").getContext("2d"), {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [
                {
                    label: 'Check-ins',
                    data: dataCounts,
                    backgroundColor: 'rgba(246, 174, 59, 0.8)',
                    borderColor: '#FF5722',
                    borderWidth: 2,
                    borderRadius: 4,
                    barThickness: 30,  // Fixed width in pixels
    // OR:
    maxBarThickness: 40,  // Maximum width
    minBarLength: 2  // Minimum length (for very small values)
                }
            ]
        },
        options: {
            
            responsive: true,
            maintainAspectRatio: false,
            interaction: {
                mode: 'index',
                intersect: false
            },
            plugins: {
                legend: { display: false },
                tooltip: {
                    backgroundColor: 'rgba(255, 255, 255, 0.9)',
                    titleColor: '#1F2937',
                    bodyColor: '#4B5563',
                    borderColor: '#E5E7EB',
                    borderWidth: 1,
                    padding: 12,
                    boxPadding: 6,
                    usePointStyle: true,
                    callbacks: {
                        label: function (context) {
                            return 'Check-ins: ' + context.parsed.y.toLocaleString();
                        }
                    }
                }
            },
            scales: {
                x: {
                    grid: { display: false },
                    ticks: {
                        maxRotation: 0,
                        color: '#9CA3AF'
                    },  barPercentage: 0.6,  // Controls bar width (0.6 = 60% of available space)
                    categoryPercentage: 0.8  // Controls space between categories (0.8 = 80% of available space)
                },
                y: {
                    beginAtZero: true,
                    grid: { color: '#FF5722' },
                    ticks: {
                        stepSize: 5,
                        color: '#9CA3AF',
                        callback: function (value) {
                            return value.toLocaleString();
                        }
                    }
                }
            },
            animation: { duration: 500 }
        }
    });

    // Switch chart by period
    document.querySelectorAll(".period-button").forEach(button => {
        button.addEventListener("click", function () {
            document.querySelectorAll(".period-button").forEach(btn => {
                btn.classList.remove("active", "bg-white", "text-orange-600");
            });
            this.classList.add("active", "bg-white", "text-orange-600");
            showLoading();

            const period = this.dataset.period;

            setTimeout(() => {
                let newData;
                
                switch (period) {
                    case "weekly":
                        newData = getChartData(weeklyCheckIns);
                        updateSummaryStats(weeklyCheckIns);
                        document.getElementById('h3').textContent = 'Weekly Check-ins';
                        break;
                    case "monthly":
                        newData = getChartData(monthlyCheckIns);
                        updateSummaryStats(monthlyCheckIns);
                        document.getElementById('h3').textContent = 'Monthly Check-ins';
                        break;
                    case "yearly":
                        newData = getChartData(yearlyCheckIns);
                        updateSummaryStats(yearlyCheckIns);
                        document.getElementById('h3').textContent = 'Yearly Check-ins';
                        break;
                    default:
                        newData = getChartData(dailyCheckIns);
                        updateSummaryStats(dailyCheckIns);
                        document.getElementById('h3').textContent = 'Daily Check-ins';
                }

                myChart.data.labels = newData.labels;
                myChart.data.datasets[0].data = newData.dataCounts;
                myChart.update();

                hideLoading();
            }, 500);
        });
    });

    document.querySelectorAll('.chart-action-button').forEach(button => {
        button.addEventListener('mouseenter', function () {
            this.querySelector('i').classList.add('text-orange-600');
        });

        button.addEventListener('mouseleave', function () {
            this.querySelector('i').classList.remove('text-orange-600');
        });
    });
});
</script>


<!-- ============================================= Check if peakHours data is correctly passed from PHP============================================= -->
<script>
    const peakHours = @json($peakHours, JSON_PRETTY_PRINT);

    console.log("Peak Hours Data:", peakHours);

    const timeOfDayCanvas = document.getElementById('time-of-day-chart');
    if (timeOfDayCanvas) {
        const timeOfDayCtx = timeOfDayCanvas.getContext('2d');

        const timeOfDayChart = new Chart(timeOfDayCtx, {
            type: 'line',
            data: {
                labels: peakHours.labels || [],
                datasets: [{
                    label: 'Number of Check-ins',
                    data: peakHours.data || [],
                    fill: true,
                    backgroundColor: 'rgba(255, 153, 0, 0.37)',
                    borderColor: '#FF5722',
                    tension: 0.4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'Check-in Count'
                        },
                        grid: {
                            color: 'rgba(255, 153, 0, 0.2)' // Orange grid lines Y-axis
                        }
                    },
                    x: {
                        title: {
                            display: true,
                            text: 'Time of Day'
                        },
                        grid: {
                            color: 'rgba(255, 153, 0, 0.2)' // Orange grid lines X-axis
                        }
                    }
                },
                plugins: {
                    legend: {
                        display: false
                    }
                }
            }
        });
    } else {
        console.error("Error: Canvas element 'time-of-day-chart' not found.");
    }
</script>



<!-- ============================================= Get the membership data from Laravel =============================================-->
<script>
        // Get the membership data from Laravel
    var membershipLabels = {!! json_encode($membershipData['labels']) !!};
    var membershipCounts = {!! json_encode($membershipData['data']) !!};

    // Replace "Unknown" with "Custom Date" in the labels array
    membershipLabels = membershipLabels.map(label => {
        return label === 'Unknown' ? 'Custom Day' : label;
    });

    // Render the Chart.js pie chart
    var ctx = document.getElementById('membershipChart').getContext('2d');
    var membershipChart = new Chart(ctx, {
        type: 'pie',
        data: {
            labels: membershipLabels,
            datasets: [{
                label: 'Membership Count',
                data: membershipCounts,
                backgroundColor: [
                    '#FFAD60',  // Soft orange (warm & inviting)
                    '#FF7043',  // Vibrant orange (attention-grabbing)
                    '#F57C00',  // Rich orange (strong but not harsh)
                    '#FFA726',  // Light orange (friendly & energetic)
                    '#D84315'   // Deep burnt orange (good contrast)
                ],
                borderColor: ['#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0'],
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,  
            maintainAspectRatio: false,  
            plugins: {
                legend: {
                    display: true,
                    position: 'top', // Moves legend below the chart
                    labels: {
                        boxWidth: 8, // Adjusts size of the color boxes
                        padding: 10,   // Adds spacing between labels
                    }
                }
            }
        }
    });
</script>


@endsection