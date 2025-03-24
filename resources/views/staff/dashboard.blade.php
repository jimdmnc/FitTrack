@extends('layouts.app') <!-- Assuming you have a main layout file -->

@section('content')
    <link href="https://cdnjs.cloudflare.com/ajax/libs/tailwindcss/2.2.19/tailwind.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.7.1/chart.min.js"></script>
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
        body {
            background-color: #f9fafb;
            background-image: radial-gradient(#e2e8f0 1px, transparent 1px);
            background-size: 20px 20px;
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
            background: rgba(240, 240, 250, 0.9);
            color: #4f46e5;
            cursor: pointer;
            transition: all 0.2s ease;
        }
        .chart-action-button:hover {
            background: rgba(228, 228, 250, 1);
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
            background: rgba(240, 240, 250, 0.9);
            color: #6b7280;
            cursor: pointer;
            transition: all 0.2s ease;
        }
        .period-button.active {
            background: #4f46e5;
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
            background: rgba(240, 240, 250, 0.9);
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
            background-color: rgba(248, 250, 252, 0.9);
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
    <style>
/* Improved styling for the chart components */
.glass-card {
    background: rgba(255, 255, 255, 0.8);
    backdrop-filter: blur(10px);
    border: 1px solid rgba(255, 255, 255, 0.3);
}

.period-button {
    cursor: pointer;
    transition: all 0.2s ease;
}

.period-button:hover:not(.active) {
    background-color: rgba(255, 255, 255, 0.5);
}

.period-button.active {
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
}

.chart-action-button {
    transition: all 0.2s ease;
    cursor: pointer;
}

.stat-card {
    transition: transform 0.2s ease;
}

.stat-card:hover {
    transform: translateY(-2px);
}
</style>

    <div class="container mx-auto py-8 px-4">
        <!-- Header Section with Modern Design -->
        <div class="mb-8">
        <div class="glass-card p-6 bg-gray-300">
            <div class="flex flex-col md:flex-row justify-between items-center">
                <div>
                    <h1 class="text-3xl md:text-4xl font-extrabold bg-clip-text text-transparent bg-gradient-to-r from-red-500 to-orange-500 hover:from-red-600 hover:to-orange-600">
                        Rockies Fitness Dashboard
                    </h1>
                    <p class="text-gray-700 mt-2">Track and analyze your gym's performance</p>
                </div>
                <div class="mt-4 md:mt-0 flex items-center gap-3">
                    <button class="px-4 py-2 bg-white border border-gray-200 hover:border-gray-300 rounded-lg shadow-sm flex items-center gap-2 text-sm font-medium transition-all">
                        <i class="fas fa-filter"></i> Filter
                    </button>
                    <div class="relative">
                        <button class="p-2 bg-white border border-gray-200 hover:border-gray-300 rounded-full shadow-sm text-gray-500 hover:text-indigo-600 transition-all">
                            <i class="fas fa-bell"></i>
                        </button>
                        <div class="absolute top-0 right-0 h-3 w-3 bg-red-500 rounded-full border-2 border-white"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
        
        <!-- Stats Cards -->
<div class="grid grid-cols-1 md:grid-cols-3 gap-2 mb-8">

    <!-- New Members Card -->
    <div class="glass-card p-3 hover:shadow-lg transition-shadow duration-300">
        <div class="flex justify-between items-start">
            <div class="w-2/3"> <!-- This sets the main content to take up 70% of the card -->
                <h3 class="text-gray-600 text-sm font-medium uppercase tracking-wider mb-2">
                    New <br>Members
                </h3>
                <div class="flex items-baseline">
                    <div class="text-3xl font-bold text-gray-900 mr-2">
                        {{ $newMembersData['currentWeekNewMembers'] }}
                    </div>
                    <span class="text-lg text-gray-700">members</span>
                </div>

                <!-- Status Indicator (Green for Increase, Red for Decrease) -->
                @php
                    $isIncrease = strpos($newMembersData['formattedPercentageChange'], '▲') !== false;
                @endphp
                <div class="mt-3 px-4 py-1 inline-flex items-center rounded-full 
                    {{ $isIncrease ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                    <i class="fas fa-arrow-up w-4 h-4 mr-1 text-green-700"></i>
                    <span class="text-sm font-medium">
                        {{ str_replace(['▲', '▼'], '', $newMembersData['formattedPercentageChange']) }}
                    </span>
                </div>
            </div>

            <!-- Icon on the Right Side (Takes up 30% width) -->
            <div class="w-1/3 flex items-center text-yellow-700">
                <i class="fas fa-user-plus text-7xl"></i> <!-- Adjust icon size if necessary -->
            </div>
        </div>
    </div>

    <!-- Today's Check-ins Card -->
    <div class="glass-card p-3 hover:shadow-lg transition-shadow duration-300">
        <div class="flex justify-between items-start">
            <div class="w-2/3">
                <h3 class="text-gray-600 text-sm font-medium uppercase tracking-wide mb-2">
                    Today's <br>Check-ins
                </h3>
                <div class="flex items-baseline">
                    <div class="text-3xl font-bold text-gray-900 mr-2">
                        {{ $todaysCheckInsData['todaysCheckIns'] }}
                    </div>
                    <span class="text-lg text-gray-700">members</span>
                </div>

                <!-- Status Indicator (Green for Increase, Red for Decrease) -->
                @php
                    $isIncrease = strpos($todaysCheckInsData['formattedPercentageChange'], 'Increase') !== false;
                @endphp
                <div class="mt-3 px-4 py-1 inline-flex items-center rounded-full 
                    {{ $isIncrease ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                    <i class="fas fa-{{ $isIncrease ? 'arrow-up' : 'arrow-down' }} w-4 h-4 mr-1"></i>
                    <span class="text-sm font-medium">
                        {{ str_replace(['Increase', 'Decrease'], '', $todaysCheckInsData['formattedPercentageChange']) }}
                    </span>
                </div>
            </div>

            <!-- Icon on the Right Side (Takes up 30% width) -->
            <div class="w-1/3 flex items-center text-green-900">
                <i class="fas fa-check-circle text-7xl"></i>
            </div>
        </div>
    </div>

    <!-- Soon to Expire Card -->
    <div class="glass-card p-3 hover:shadow-lg transition-shadow duration-300">
        <div class="flex justify-between items-start">
            <div class="w-2/3">
                <h3 class="text-gray-600 text-sm font-medium uppercase tracking-wide mb-2">Memberships <br> Expiring Soon</h3>
                <div class="flex items-baseline">
                    <div class="text-3xl font-bold text-gray-900 mr-2">{{ $expiringMemberships }}</div>
                    <span class="text-lg text-gray-700">members</span>
                </div>
                <a href="{{ route('staff.viewmembers') }}" class="mt-3 px-3 py-1.5 bg-blue-50 hover:bg-blue-100 text-blue-600 rounded-md inline-flex items-center group transition-colors duration-200">
                    <span class="text-sm font-medium">Manage Renewals</span>
                    <i class="fas fa-arrow-right ml-2 transform group-hover:translate-x-1 transition-transform duration-200"></i>
                </a>
            </div>

            <!-- Icon on the Right Side (Takes up 30% width) -->
            <div class="w-1/3 flex items-center text-red-900">
                <i class="fas fa-calendar-times text-7xl"></i>
            </div>

        </div>
    </div>
</div>


    <!-- Rearranged and Resized Dashboard Grid Charts Section -->
    <div class="dashboard-grid gap-4">

        <!-- Enhanced Check-ins Chart Card -->
        <div class="glass-card p-4 grid-col-span-8 chart-card space-y-4 rounded-xl shadow-sm" id="checkinsChartCard">
            <div class="flex justify-between items-center mb-2">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-800">Check-ins Overview</h3>
                        <p class="text-xs text-gray-500">Number of members visiting the gym</p>
                    </div>
                    <div class="flex space-x-2">
                        <button class="chart-action-button bg-white p-2 rounded-full shadow-sm hover:bg-gray-50" title="Download CSV">
                            <i class="fas fa-download text-sm text-gray-600"></i>
                        </button>
                        <button class="chart-action-button bg-white p-2 rounded-full shadow-sm hover:bg-gray-50 expand-checkins-btn" 
                            title="Expand">
                            <i class="fas fa-expand-alt text-sm text-gray-600"></i>
                        </button>

                    </div>
                </div>
                
                <!-- Improved period selector -->
                <div class="period-selector flex bg-gray-100 p-1 rounded-lg w-fit">
                    <button class="period-button active rounded-md px-3 py-1.5 text-sm font-medium transition-all" data-period="daily">Daily</button>
                    <button class="period-button rounded-md px-3 py-1.5 text-sm font-medium transition-all" data-period="weekly">Weekly</button>
                    <button class="period-button rounded-md px-3 py-1.5 text-sm font-medium transition-all" data-period="monthly">Monthly</button>
                    <button class="period-button rounded-md px-3 py-1.5 text-sm font-medium transition-all" data-period="yearly">Yearly</button>
                </div>
                
                <!-- Summary stats above chart -->
                <div class="stats-summary grid grid-cols-3 gap-4 mb-2">
                    <div class="stat-card bg-white p-3 rounded-lg shadow-sm">
                        <p class="text-xs text-gray-500">Total Check-ins</p>
                        <h4 class="text-xl font-bold text-gray-800" id="total-checkins">0</h4>
                    </div>
                    <div class="stat-card bg-white p-3 rounded-lg shadow-sm">
                        <p class="text-xs text-gray-500">Average</p>
                        <h4 class="text-xl font-bold text-gray-800" id="avg-checkins">0</h4>
                    </div>
                    <div class="stat-card bg-white p-3 rounded-lg shadow-sm">
                        <p class="text-xs text-gray-500">Peak Day</p>
                        <h4 class="text-xl font-bold text-gray-800" id="peak-checkins">0</h4>
                    </div>
                </div>
                
                <!-- Chart Container with loading indicator -->
                <div class="chart-container relative bg-white p-4 rounded-lg shadow-sm" id="checkinsChartContainer" style="height: 300px;">
                    <div id="chart-loading" class="absolute inset-0 flex items-center justify-center bg-white bg-opacity-80 z-10 hidden">
                        <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-500"></div>
                    </div>
                    <canvas id="checkins-chart"></canvas>
                </div>
                
                <!-- Legend -->
                <div class="flex items-center justify-center space-x-6 text-xs text-gray-500">
                    <div class="flex items-center">
                        <span class="inline-block w-3 h-3 mr-1 bg-blue-400 rounded-sm"></span>
                        <span>Check-ins</span>
                    </div>
                    <div class="flex items-center">
                        <span class="inline-block w-3 h-3 mr-1 border border-dashed border-gray-400 rounded-sm"></span>
                        <span>Previous Period</span>
                    </div>
                </div>
            </div>
            
            <!-- Right panels - resized and vertical -->
            <div class="grid-col-span-4 flex flex-col gap-4">
                <!-- Top right panel -->
                <div class="glass-card p-2 chart-card relative" id="chartCard">
                    <div class="flex justify-between items-center mb-3">
                        <div>
                            <h3 class="text-base font-semibold text-gray-800">Avg Time by Hour</h3>
                            <p class="text-xs text-gray-500">Peak hours</p>
                        </div>
                        <div class="chart-action-buttons space-x-1">
                            <div class="chart-action-button" title="Download CSV">
                                <i class="fas fa-download text-sm"></i>
                            </div>
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
                            <h3 class="text-base font-semibold text-gray-800">Subscribers</h3>
                            <p class="text-sm text-gray-500">Ongoing Memberships</p>
                        </div>
                        <div class="chart-action-buttons space-x-2 flex items-center">
                            <div class="chart-action-button cursor-pointer" title="Download CSV">
                                <i class="fas fa-download text-sm text-gray-700"></i>
                            </div>
                            <div class="chart-action-button expand-subscribers-btn cursor-pointer" title="Expand">
                                <i class="fas fa-expand-alt text-sm text-gray-700"></i>
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
        <h3 class="text-xl font-bold text-blue-900">Top Active Members</h3>
        <p class="text-sm text-gray-500 mt-1">Most frequent check-ins and their membership details</p>
    </div>

        <div class="flex items-center gap-3">
<!-- Search Form -->


        </div>
    </div>
    <div class="overflow-x-auto">
    <table class="min-w-full divide-y divide-gray-200">
        <thead>
            <tr class="bg-gray-50 rounded-lg">
                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">#</th> <!-- Added this column -->
                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Member ID</th>
                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Membership Type</th>
                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-200">
        @foreach ($topActiveMembers as $index => $member)
            <tr class="hover:bg-gray-50 transition-colors member-table-row">
                <td class="px-4 py-4 text-sm text-gray-500">{{ $loop->iteration }}</td> <!-- Added this cell -->
                <td class="px-4 py-4 text-sm text-gray-500">{{ $member->rfid_uid }}</td>
                <td class="px-4 py-4 whitespace-nowrap">
                    <div class="flex items-center">
                        <div class="h-10 w-10 flex-shrink-0 mr-3">
                            <div class="h-10 w-10 rounded-full bg-blue-100 flex items-center justify-center">
                                <span class="text-blue-600 font-semibold">
                                    {{ strtoupper(substr($member->first_name, 0, 1)) . strtoupper(substr($member->last_name, 0, 1)) }}
                                </span>
                            </div>
                        </div>
                        <div>
                            <div class="text-sm font-medium text-gray-900">{{ $member->first_name }} {{ $member->last_name }}</div>
                        </div>
                    </div>
                </td>
                <td class="px-4 py-4 whitespace-nowrap">
                    <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full 
                        @if($member->getMembershipType() == 'Annual') bg-purple-100 text-purple-800
                        @elseif($member->getMembershipType() == 'Week') bg-green-100 text-green-800
                        @elseif($member->getMembershipType() == 'Month') bg-blue-100 text-blue-800
                        @elseif($member->getMembershipType() == 'Session') bg-yellow-100 text-yellow-800
                        @endif">
                        {{ $member->getMembershipType() }}
                    </span>
                </td>
                <td class="px-4 py-4 whitespace-nowrap">
                    <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full 
                        {{ $member->member_status == 'active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                        {{ $member->member_status }}
                    </span>
                </td>
                <td class="px-4 py-4 text-right text-sm">
                    <button onclick="openViewModal('{{ $member->rfid_uid }}', '{{ $member->first_name }} {{ $member->last_name }}', '{{ $member->getMembershipType() }}', '{{ \Carbon\Carbon::parse($member->start_date)->format('M d, Y') }}', '{{ $member->member_status }}')"
                        class="text-indigo-600 hover:text-indigo-900 font-medium mr-2">View</button>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>


    </div>

    

</div>
        <!-- View Member Modal -->
        <div id="viewMemberModal" class="fixed inset-0 bg-gray-900 bg-opacity-70 flex justify-center items-center hidden z-50 transition-opacity duration-300">
            <div class="bg-white rounded-xl shadow-2xl w-full max-w-3xl p-6 transform transition-all duration-300 scale-95 opacity-0" id="viewModalContent">
                <!-- Modal Header -->
                <div class="flex justify-between items-center mb-6 border-b pb-3">
                    <h2 class="text-xl font-bold text-gray-800 flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mr-2 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                        Member Profile
                    </h2>
                    <button onclick="closeViewModal()" class="text-gray-500 hover:text-gray-700 hover:bg-gray-100 rounded-full p-1 transition-colors">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <!-- Horizontal ID Card Layout -->
                <div class="bg-white border-2 border-blue-500 rounded-lg overflow-hidden shadow-md">
                    <!-- Card Header -->
                    <div class="bg-blue-500 p-3 text-white">
                        <h3 class="font-bold text-center">MEMBER IDENTIFICATION</h3>
                    </div>
                    
                    <!-- Horizontal Layout Container -->
                    <div class="flex">
                        <!-- Left Column - Photo Area -->
                        <div class="w-1/4 p-4 flex flex-col items-center justify-center border-r">
                            <div class="w-32 h-32 bg-gray-100 border rounded-full flex items-center justify-center mb-3">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                            </div>
                            <!-- Status Badge -->
                            <span id="viewStatus" class="inline-block px-3 py-1 text-sm font-semibold rounded-full bg-green-100 text-green-800">Active</span>
                        </div>
                        
                        <!-- Middle Column - Primary Info -->
                        <div class="w-2/5 p-4 bg-white">
                            <!-- Name -->
                            <div class="mb-4">
                                <p class="text-xs text-gray-500 uppercase">Name</p>
                                <p class="font-bold text-gray-800 text-lg" id="viewMemberName">John Doe</p>
                            </div>
                            
                            <!-- Member ID -->
                            <div class="mb-4">
                                <p class="text-xs text-gray-500 uppercase">Member ID</p>
                                <p class="font-medium text-gray-800" id="viewMemberID">M12345678</p>
                            </div>
                            
                            <!-- Registration Date -->
                            <div class="flex items-center mb-4">
                                <div class="bg-yellow-100 p-2 rounded-full mr-3">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-yellow-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-500 uppercase">Registration Date</p>
                                    <p class="font-medium text-gray-800" id="viewStartDate">Jan 15, 2025</p>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Right Column - Membership Type & Barcode -->
                        <div class="w-1/3 p-4 bg-gray-50">
                            <!-- Membership Type -->
                            <div class="flex items-center mb-6">
                                <div class="bg-purple-100 p-2 rounded-full mr-3">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-purple-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-500 uppercase">Membership Type</p>
                                    <p class="font-medium text-gray-800" id="viewMembershipType">Premium</p>
                                </div>
                            </div>
                            
                            <!-- Barcode Area -->
                            <div class="mb-2">
                                <p class="text-xs text-gray-500 uppercase mb-1">Card ID</p>
                                <div class="bg-gray-100 h-12 flex items-center justify-center rounded">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-500 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 11c0 3.517-1.009 6.799-2.753 9.571m-3.44-2.04l.054-.09A13.916 13.916 0 008 11a4 4 0 118 0c0 1.017-.07 2.019-.203 3m-2.118 6.844A21.88 21.88 0 0015.171 17m3.839 1.132c.645-2.266.99-4.659.99-7.132A8 8 0 008 4.07M3 15.364c.64-1.319 1-2.8 1-4.364 0-1.457.39-2.823 1.07-4" />
                                    </svg>
                                    <span class="text-sm font-medium text-gray-600">ID: 123456789</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Footer -->
                    <div class="bg-blue-500 text-white text-center py-2 text-xs">
                        <p>Valid only with photo identification</p>
                    </div>
                </div>

                <!-- Modal Footer with Edit and Close Buttons -->
                <!-- <div class="flex justify-end mt-6">
                    <button onclick="openEditModal()" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg mr-2 transition-colors shadow-sm flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                        </svg>
                        Edit
                    </button>
                    <button onclick="closeViewModal()" class="bg-gray-100 hover:bg-gray-200 text-gray-800 px-4 py-2 rounded-lg transition-colors shadow-sm">Close</button>
                </div> -->
            </div>
        </div>

        <!-- Edit Member Modal -->
        <div id="editMemberModal" class="fixed inset-0 bg-gray-900 bg-opacity-70 flex justify-center items-center hidden z-50 transition-opacity duration-300">
            <div class="bg-white rounded-xl shadow-2xl w-full max-w-md p-6 transform transition-all duration-300 scale-95 opacity-0" id="editModalContent">
                <!-- Modal Header -->
                <div class="flex justify-between items-center mb-6 border-b pb-3">
                    <h2 class="text-xl font-bold text-gray-800 flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mr-2 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                        </svg>
                        Edit Member
                    </h2>
                    <button onclick="closeEditModal()" class="text-gray-500 hover:text-gray-700 hover:bg-gray-100 rounded-full p-1 transition-colors">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <!-- Edit Form -->
                <form id="editMemberForm">
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Member ID</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0" />
                                </svg>
                            </div>
                            <input type="text" id="editMemberID" class="w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg bg-gray-100 text-gray-500" readonly>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Name</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                            </div>
                            <input type="text" id="editMemberName" class="w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors">
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Membership Type</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                                </svg>
                            </div>
                            <select id="editMembershipType" class="w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors appearance-none">
                                <option value="Annual">Annual</option>
                                <option value="Month">Monthly</option>
                                <option value="Week">Weekly</option>
                                <option value="Session">Per Session</option>
                            </select>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                        <div class="flex items-center space-x-4">
                            <label class="flex items-center">
                                <input type="radio" name="status" value="active" class="h-5 w-5 text-blue-600 focus:ring-blue-500 cursor-pointer">
                                <div class="ml-2 flex items-center">
                                    <span class="inline-flex h-3 w-3 bg-green-500 rounded-full mr-1.5"></span>
                                    <span>Active</span>
                                </div>
                            </label>
                            <label class="flex items-center">
                                <input type="radio" name="status" value="expired" class="h-5 w-5 text-blue-600 focus:ring-blue-500 cursor-pointer">
                                <div class="ml-2 flex items-center">
                                    <span class="inline-flex h-3 w-3 bg-red-500 rounded-full mr-1.5"></span>
                                    <span>Expired</span>
                                </div>
                            </label>
                        </div>
                    </div>

                    <!-- Modal Footer with Save and Cancel Buttons -->
                    <div class="flex justify-end mt-6">
                        <button type="button" onclick="saveChanges()" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg mr-2 transition-colors shadow-sm flex items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            Save Changes
                        </button>
                        <button type="button" onclick="closeEditModal()" class="bg-gray-100 hover:bg-gray-200 text-gray-800 px-4 py-2 rounded-lg transition-colors shadow-sm">Cancel</button>
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
                card.style.background = "white";
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


















<script>


    // Open View Modal
    function openViewModal(name, memberID, status, membershipType, startDate) {
        // Set modal data
        document.getElementById('viewMemberName').textContent = name;
        document.getElementById('viewMemberID').textContent = memberID;
        document.getElementById('viewStatus').textContent = status;
        document.getElementById('viewMembershipType').textContent = membershipType;
        document.getElementById('viewStartDate').textContent = startDate;

        // Change status color based on status
        let statusBadge = document.getElementById('viewStatus');
        if (status.toLowerCase() === 'active') {
            statusBadge.className = "inline-block px-2 py-1 text-sm font-semibold rounded-full bg-green-100 text-green-800";
        } else {
            statusBadge.className = "inline-block px-2 py-1 text-sm font-semibold rounded-full bg-red-100 text-red-800";
        }

        // Show modal
        const modal = document.getElementById('viewMemberModal');
        const modalContent = document.getElementById('viewModalContent');
        
        modal.classList.remove('hidden'); // Make it visible
        setTimeout(() => {
            modalContent.classList.remove('scale-95', 'opacity-0'); // Animate opening
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


<!-- // Check INs data is correctly passed from PHP -->
<script>
    document.addEventListener("DOMContentLoaded", function() {
        // Get check-in data from Laravel
        var dailyCheckIns = @json($dailyCheckIns);
        var weeklyCheckIns = @json($weeklyCheckIns);
        var monthlyCheckIns = @json($monthlyCheckIns);
        var yearlyCheckIns = @json($yearlyCheckIns);
        
        // Function to extract labels and data counts from check-in data
        function getChartData(dataSet) {
            return {
                labels: dataSet.map(item => item.date),
                dataCounts: dataSet.map(item => item.count)
            };
        }
        
        // Function to calculate and display summary statistics
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
        
        // Show loading indicator
        function showLoading() {
            document.getElementById('chart-loading').classList.remove('hidden');
        }
        
        // Hide loading indicator
        function hideLoading() {
            document.getElementById('chart-loading').classList.add('hidden');
        }
        
        // Get previous period data (for comparison)
        function getPreviousPeriodData(currentData, period) {
            // This is a simplified implementation - in a real app, you'd need proper date calculations
            // to determine the previous period's data based on your database
            return currentData.map(item => Math.max(0, item * 0.8 + Math.random() * 10));
        }
        
        // Initial dataset (Daily Check-ins)
        var { labels, dataCounts } = getChartData(dailyCheckIns);
        updateSummaryStats(dailyCheckIns);
        
        // Chart configuration with improved styling and tooltip
        var ctx = document.getElementById("checkins-chart").getContext("2d");
        var myChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [
                    {
                        label: 'Current Period',
                        data: dataCounts,
                        backgroundColor: 'rgba(59, 130, 246, 0.7)',
                        borderColor: 'rgba(59, 130, 246, 1)',
                        borderWidth: 1,
                        borderRadius: 4,
                        barThickness: 'flex',
                        maxBarThickness: 25
                    },
                    {
                        label: 'Previous Period',
                        data: getPreviousPeriodData(dataCounts, 'daily'),
                        type: 'line',
                        fill: false,
                        borderColor: 'rgba(156, 163, 175, 0.7)',
                        borderDash: [5, 5],
                        pointBackgroundColor: 'rgba(156, 163, 175, 0.7)',
                        tension: 0.1
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
                    legend: {
                        display: false
                    },
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
                            label: function(context) {
                                return context.dataset.label + ': ' + context.parsed.y.toLocaleString() + ' check-ins';
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        grid: {
                            display: false
                        },
                        ticks: {
                            maxRotation: 0,
                            color: '#9CA3AF'
                        }
                    },
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: 'rgba(243, 244, 246, 1)'
                        },
                        ticks: {
                            stepSize: 5,
                            color: '#9CA3AF',
                            callback: function(value) {
                                return value.toLocaleString();
                            }
                        }
                    }
                },
                animation: {
                    duration: 500
                }
            }
        });
        
        // Enhanced period selector with loading simulation
        document.querySelectorAll(".period-button").forEach(button => {
            button.addEventListener("click", function() {
                // Remove active class from all buttons
                document.querySelectorAll(".period-button").forEach(btn => {
                    btn.classList.remove("active", "bg-white", "text-blue-600");
                });
                
                // Add active styling to clicked button
                this.classList.add("active", "bg-white", "text-blue-600");
                
                // Show loading indicator
                showLoading();
                
                // Get the selected period and update the chart (with delay to show loading effect)
                const period = this.dataset.period;
                setTimeout(() => {
                    let newData;
                    
                    switch (period) {
                        case "weekly":
                            newData = getChartData(weeklyCheckIns);
                            updateSummaryStats(weeklyCheckIns);
                            document.querySelector('h3').textContent = 'Weekly Check-ins';
                            break;
                        case "monthly":
                            newData = getChartData(monthlyCheckIns);
                            updateSummaryStats(monthlyCheckIns);
                            document.querySelector('h3').textContent = 'Monthly Check-ins';
                            break;
                        case "yearly":
                            newData = getChartData(yearlyCheckIns);
                            updateSummaryStats(yearlyCheckIns);
                            document.querySelector('h3').textContent = 'Yearly Check-ins';
                            break;
                        default:
                            newData = getChartData(dailyCheckIns);
                            updateSummaryStats(dailyCheckIns);
                            document.querySelector('h3').textContent = 'Daily Check-ins';
                    }
                    
                    // Update the chart with new data and a comparison line
                    myChart.data.labels = newData.labels;
                    myChart.data.datasets[0].data = newData.dataCounts;
                    myChart.data.datasets[1].data = getPreviousPeriodData(newData.dataCounts, period);
                    myChart.update();
                    
                    // Hide loading indicator
                    hideLoading();
                }, 500);
            });
        });
        
        // Add hover effects to action buttons
        document.querySelectorAll('.chart-action-button').forEach(button => {
            button.addEventListener('mouseenter', function() {
                this.querySelector('i').classList.add('text-blue-600');
            });
            
            button.addEventListener('mouseleave', function() {
                this.querySelector('i').classList.remove('text-blue-600');
            });
        });
    });
</script>

<!-- // Check if peakHours data is correctly passed from PHP -->
<script>
    const peakHours = @json($peakHours, JSON_PRETTY_PRINT);  // Debugging-friendly JSON format

    console.log("Peak Hours Data:", peakHours); // Debugging output in the console

    // Ensure the chart element exists before initializing
    const timeOfDayCanvas = document.getElementById('time-of-day-chart');
    if (timeOfDayCanvas) {
        const timeOfDayCtx = timeOfDayCanvas.getContext('2d');

        const timeOfDayChart = new Chart(timeOfDayCtx, {
            type: 'line',
            data: {
                labels: peakHours.labels || [],  // Ensure fallback to an empty array if undefined
                datasets: [{
                    label: 'Average Minutes',
                    data: peakHours.data || [],  // Ensure fallback to an empty array if undefined
                    fill: true,
                    backgroundColor: 'rgba(79, 70, 229, 0.2)',
                    borderColor: 'rgba(79, 70, 229, 1)',
                    tension: 0.4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                },
                plugins: {
                    legend: {
                        display: false // 🔴 Hides the legend since there's only one dataset
                    }
                }
            }
        });
    } else {
        console.error("Error: Canvas element 'time-of-day-chart' not found.");
    }
</script>


<!-- // Get the membership data from Laravel -->
<script>
        // Get the membership data from Laravel
        var membershipLabels = {!! json_encode($membershipData['labels']) !!};
        var membershipCounts = {!! json_encode($membershipData['data']) !!};

        // Render the Chart.js pie chart
        var ctx = document.getElementById('membershipChart').getContext('2d');
        var membershipChart = new Chart(ctx, {
            type: 'pie',
            data: {
                labels: membershipLabels,
                datasets: [{
                    label: 'Membership Count',
                    data: membershipCounts,
                    backgroundColor: ['#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0'],
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