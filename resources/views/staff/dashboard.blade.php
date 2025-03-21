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

    <div class="container mx-auto py-8 px-4">
        <!-- Header Section with Modern Design -->
        <div class="mb-8">
            <div class="glass-card p-6">
                <div class="flex flex-col md:flex-row justify-between items-center">
                    <div>
                        <h1 class="text-3xl md:text-4xl font-extrabold bg-clip-text text-transparent bg-gradient-to-r from-blue-600 to-indigo-700">
                            Rockies  Fitness Dashboard
                        </h1>
                        <p class="text-gray-500 mt-2">Track and analyze your gym's performance</p>
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
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
            <!-- Current Members Card -->
            <div class="glass-card p-5 hover:shadow-lg transition-shadow duration-300">
                <div class="flex justify-between items-start">
                    <div>
                        <h3 class="text-gray-600 text-sm font-medium uppercase tracking-wide mb-2">Current <br> Members</h3>
                        <div class="flex items-baseline">
                            <div class="text-3xl font-bold text-gray-900 mr-2">
                                {{ $activeMembersData['currentWeekActiveMembers'] }}
                            </div>
                            <span class="text-lg text-gray-700">members</span>
                        </div>
                        <div class="mt-3 px-4 py-1 inline-flex items-center rounded-full {{ $activeMembersData['formattedPercentageChange'][-1] === '▲' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                            <i class="fas {{ $activeMembersData['formattedPercentageChange'][-1] === '▲' ? 'fa-arrow-up' : 'fa-arrow-down' }} mr-1 text-xs"></i>
                            <span class="text-sm font-medium">{{ str_replace(['▲', '▼'], '', $activeMembersData['formattedPercentageChange']) }}</span>
                        </div>
                    </div>
                    <div class="p-3 rounded-full bg-blue-100 text-blue-600">
                        <i class="fas fa-users text-xl"></i>
                    </div>
                </div>
            </div>

            <!-- New Members Card -->
            <div class="glass-card p-5 hover:shadow-lg transition-shadow duration-300">
                <div class="flex justify-between items-start">
                    <div>
                        <h3 class="text-gray-600 text-sm font-medium uppercase tracking-wider mb-2">New <br>Members</h3>
                        <div class="flex items-baseline">
                            <div class="text-3xl font-bold text-gray-900 mr-2">
                                {{ $newMembersData['currentWeekNewMembers'] }}
                            </div>
                            <span class="text-lg text-gray-700">this week</span>
                        </div>
                        <div class="mt-3 px-4 py-1 inline-flex items-center rounded-full {{ $newMembersData['formattedPercentageChange'][-1] === '▲' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                            <i class="fas {{ $newMembersData['formattedPercentageChange'][-1] === '▲' ? 'fa-arrow-up' : 'fa-arrow-down' }} mr-1 text-xs"></i>
                            <span class="text-sm font-medium">{{ str_replace(['▲', '▼'], '', $newMembersData['formattedPercentageChange']) }}</span>
                        </div>
                    </div>
                    <div class="p-3 rounded-full bg-purple-100 text-purple-600">
                        <i class="fas fa-user-plus text-xl"></i>
                    </div>
                </div>
            </div>

            <!-- Today's Check-ins Card -->
            <div class="glass-card p-5 hover:shadow-lg transition-shadow duration-300">
                <div class="flex justify-between items-start">
                    <div>
                        <h3 class="text-gray-600 text-sm font-medium uppercase tracking-wide mb-2">Today's <br>Check-ins</h3>
                        <div class="flex items-baseline">
                            <div class="text-3xl font-bold text-gray-900 mr-2">
                                {{ $todaysCheckInsData['todaysCheckIns'] }}
                            </div>
                            <span class="text-lg text-gray-700">members</span>
                        </div>
                        <div class="mt-3 px-4 py-1 inline-flex items-center rounded-full {{ $todaysCheckInsData['formattedPercentageChange'][-1] === '▲' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                            <i class="fas {{ $todaysCheckInsData['formattedPercentageChange'][-1] === '▲' ? 'fa-arrow-up' : 'fa-arrow-down' }} mr-1 text-xs"></i>
                            <span class="text-sm font-medium">{{ str_replace(['▲', '▼'], '', $todaysCheckInsData['formattedPercentageChange']) }}</span>
                        </div>
                    </div>
                    <div class="p-3 rounded-full bg-amber-100 text-amber-600">
                        <i class="fas fa-check-circle text-xl"></i>
                    </div>
                </div>
            </div>

            <!-- Soon to Expire Card -->
            <div class="glass-card p-5 hover:shadow-lg transition-shadow duration-300">
                <div class="flex justify-between items-start">
                    <div>
                        <h3 class="text-gray-600 text-sm font-medium uppercase tracking-wide mb-2">Memberships <br> Expiring Soon</h3>
                        <div class="flex items-baseline">
                            <div class="text-3xl font-bold text-gray-900 mr-2">{{ $expiringMemberships }}</div>
                            <span class="text-lg text-gray-700">members</span>
                        </div>
                        <a href="#" class="mt-3 px-3 py-1.5 bg-blue-50 hover:bg-blue-100 text-blue-600 rounded-md inline-flex items-center group transition-colors duration-200">
                            <span class="text-sm font-medium">Manage Renewals</span>
                            <i class="fas fa-arrow-right ml-2 transform group-hover:translate-x-1 transition-transform duration-200"></i>
                        </a>
                    </div>
                    <div class="p-3 rounded-full bg-red-100 text-red-600">
                        <i class="fas fa-calendar-times text-xl"></i>
                    </div>
                </div>
            </div>
        </div>

            <!-- Rearranged and Resized Dashboard Grid Charts Section -->
    <div class="dashboard-grid gap-4">
        <!-- Large left panel - made narrower -->
        <div class="glass-card p-3 grid-col-span-8 chart-card space-y-9">
            <div class="flex justify-between items-center mb-4">
                <div>
                    <h3 class="text-base font-semibold text-gray-800">Session Duration</h3>
                    <p class="text-xs text-gray-500">Avg time spent by members</p>
                    
                </div>
                <div class="chart-action-buttons space-x-1">
                    <div class="chart-action-button" title="Download CSV">
                        <i class="fas fa-download text-sm"></i>
                    </div>
                    <div class="chart-action-button" title="Expand">
                        <i class="fas fa-expand-alt text-sm"></i>
                    </div>
                    <div class="chart-action-button" title="Settings">
                        <i class="fas fa-cog text-sm"></i>
                    </div>
                </div>
            </div>
            
            <div class="period-selector space-x-4 mb-3">
                <div class="period-button active text-xs px-2 py-1">Week</div>
                <div class="period-button text-xs px-2 py-1">Month</div>
                <div class="period-button text-xs px-2 py-1">Quarter</div>
                <div class="period-button text-xs px-2 py-1">Year</div>
            </div>
            
            <div class="chart-container" style="height: 220px;">
                <canvas id="duration-distribution-chart"></canvas>
            </div>
            
            <div class="custom-legend mt-3 p-2 grid grid-cols-3 space-x-3 text-xs"> <!-- gap-x-4 for columns, gap-y-2 for rows -->
        <div class="legend-item flex items-center space-x-2">
            <div class="legend-color" style="background-color: rgba(191, 219, 254, 0.9);"></div><span>0-30 min</span>
        </div>
        <div class="legend-item flex items-center space-x-2">
            <div class="legend-color" style="background-color: rgba(147, 197, 253, 0.9);"></div><span>31-60 min</span>
        </div>
        <div class="legend-item flex items-center space-x-2">
            <div class="legend-color" style="background-color: rgba(96, 165, 250, 0.9);"></div><span>61-90 min</span>
        </div>
        <div class="legend-item flex items-center space-x-2">
            <div class="legend-color" style="background-color: rgba(59, 130, 246, 0.9);"></div><span>91-120 min</span>
        </div>
        <div class="legend-item flex items-center space-x-2">
            <div class="legend-color" style="background-color: rgba(37, 99, 235, 0.9);"></div><span>120+ min</span>
        </div>
    </div>

        </div>
        
        <!-- Right panels - resized and vertical -->
        <div class="grid-col-span-4 flex flex-col gap-4">
            <!-- Top right panel -->
            <div class="glass-card p-2 chart-card">
                <div class="flex justify-between items-center mb-3">
                    <div>
                        <h3 class="text-base font-semibold text-gray-800">Avg Time by Hour</h3>
                        <p class="text-xs text-gray-500">Peak hours</p>
                    </div>
                    <div class="chart-action-buttons space-x-1">
                        <div class="chart-action-button" title="Download CSV"><i class="fas fa-download text-sm"></i></div>
                        <div class="chart-action-button" title="Expand"><i class="fas fa-expand-alt text-sm"></i></div>
                        <div class="chart-action-button" title="Settings"><i class="fas fa-cog text-sm"></i></div>
                    </div>
                </div>
                
                <div class="period-selector space-x-1 mb-2">
                    <div class="period-button active text-xs px-2 py-1">Today</div>
                    <div class="period-button text-xs px-2 py-1">Week</div>
                    <div class="period-button text-xs px-2 py-1">Month</div>
                </div>
                
                <div class="chart-container" style="height: 180px;">
                    <canvas id="time-of-day-chart"></canvas>
                </div>
            </div>
            
            <!-- Bottom right panel -->
            <div class="glass-card p-2 chart-card">
                <div class="flex justify-between items-center mb-3">
                    <div>
                        <h3 class="text-s font-semibold text-gray-800">Membership Distribution</h3>
                        <p class="text-xs text-gray-500">Active types</p>
                    </div>
                    <div class="chart-action-buttons space-x-1">
                        <div class="chart-action-button" title="Download CSV"><i class="fas fa-download text-sm"></i></div>
                        <div class="chart-action-button" title="Expand"><i class="fas fa-expand-alt text-sm"></i></div>
                        <div class="chart-action-button" title="Settings"><i class="fas fa-cog text-sm"></i></div>
                    </div>
                </div>
                
                <div class="chart-container" style="height: 190px;">
                    <canvas id="membership-chart"></canvas>
                </div>
            </div>
        </div>
    </div>


<!-- Members Table with Modern Design - now placed below session distribution and wider -->
<div class="glass-card p-6 grid-col-span-8 mt-6">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h3 class="text-xl font-bold text-blue-900">Recent Member Registrations</h3>
            <p class="text-sm text-gray-500 mt-1">Newly joined members and their status</p>
        </div>
        <div class="flex items-center gap-3">
<!-- Search Form -->
<form method="GET" action="{{ route('staff.dashboard') }}" class="w-full sm:w-auto">
    <div class="flex items-center space-x-2">
        <!-- Search Input with Icon -->
        <div class="relative w-full">
            <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd" />
                </svg>
            </div>
            <input 
                type="text" 
                name="search" 
                value="{{ $query }}" 
                placeholder="Search members..." 
                class="block w-full pl-10 py-2 border border-gray-300 rounded-md leading-5 bg-white placeholder-gray-500 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                aria-label="Search members"
            >
        </div>

        <!-- Search Button (Outside Input Field) -->
        <button 
            type="submit" 
            class="px-3 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 focus:outline-none transition duration-150 ease-in-out flex items-center"
            aria-label="Search"
        >
            <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
            </svg>
        </button>

        <!-- Clear Button (Appears Only If Search is Active) -->
        @if($query)
        <a 
            href="{{ route('staff.dashboard') }}" 
            class="px-3 py-2 text-gray-600 hover:text-gray-800 bg-gray-200 rounded-md focus:outline-none transition duration-150 ease-in-out flex items-center"
        >
            <svg class="h-4 w-4 mr-1" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
            Clear
        </a>
        @endif
    </div>
</form>


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
                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Registration Date</th>
                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-200">
            @foreach ($members as $member)
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
                <td class="px-4 py-4 text-sm text-gray-500">{{ \Carbon\Carbon::parse($member->start_date)->format('M d, Y') }}</td>
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
    <!-- Pagination Links -->
    <div class="mt-4">
        <!-- Custom Pagination Links -->
        {{ $members->links('vendor.pagination.default') }}
    </div>

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

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Chart.js global settings
            Chart.defaults.font.family = "'Inter', 'Helvetica', 'Arial', sans-serif";
            Chart.defaults.font.size = 12;
            Chart.defaults.color = '#64748b';
            Chart.defaults.plugins.tooltip.padding = 12;
            Chart.defaults.plugins.tooltip.cornerRadius = 8;
            Chart.defaults.plugins.tooltip.titleFont.size = 14;
            Chart.defaults.plugins.tooltip.titleFont.weight = 'bold';
            Chart.defaults.plugins.tooltip.titleColor = '#fff';
            Chart.defaults.plugins.tooltip.bodyColor = '#fff';
            Chart.defaults.plugins.tooltip.backgroundColor = 'rgba(17, 25, 40, 0.85)';
            Chart.defaults.plugins.tooltip.borderColor = 'rgba(255, 255, 255, 0.1)';
            Chart.defaults.plugins.tooltip.borderWidth = 1;
            Chart.defaults.plugins.tooltip.displayColors = false;
            Chart.defaults.plugins.legend.display = false;
            Chart.defaults.elements.line.tension = 0.4;
            Chart.defaults.elements.point.radius = 4;
            Chart.defaults.elements.point.hoverRadius = 6;

            // Enhanced animation options
            const animationOptions = {
                duration: 1000,
                easing: 'easeOutQuart',
                delay: (context) => context.dataIndex * 50
            };

            // Data for the charts
            const data = {
                // Session Duration Distribution
                durationDistribution: {
                    labels: ['0-30 min', '31-60 min', '61-90 min', '91-120 min', '120+ min'],
                    data: [32, 78, 96, 42, 19]
                },
                
                // Average Time by Hour of Day
                timeOfDay: {
                    labels: ['6am', '8am', '10am', '12pm', '2pm', '4pm', '6pm', '8pm', '10pm'],
                    data: [38, 55, 65, 70, 68, 75, 89, 62, 40]
                },
                
                // Membership Distribution
                membership: {
                    labels: ['Session', 'Weekly', 'Monthly', 'Annual'],
                    data: [145, 87, 35, 59]
                },
                
                // Weekly trends
                weeklyTrends: {
                    labels: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'],
                    current: [45, 59, 80, 81, 56, 55, 40],
                    previous: [35, 48, 65, 75, 50, 48, 30]
                }
            };

            // Initialize Session Duration Distribution chart with animations and gradient
            const durationDistributionCtx = document.getElementById('duration-distribution-chart').getContext('2d');
            
            // Create gradient for bars
            const barGradient1 = durationDistributionCtx.createLinearGradient(0, 0, 0, 400);
            barGradient1.addColorStop(0, 'rgba(96, 165, 250, 0.9)');
            barGradient1.addColorStop(1, 'rgba(59, 130, 246, 0.7)');
            
            const durationDistributionChart = new Chart(durationDistributionCtx, {
                type: 'bar',
                data: {
                    labels: data.durationDistribution.labels,
                    datasets: [{
                        label: 'Members',
                        data: data.durationDistribution.data,
                        backgroundColor: barGradient1,
                        borderColor: 'rgba(255, 255, 255, 0.6)',
                        borderWidth: 2,
                        borderRadius: 8,
                        maxBarThickness: 45,
                        hoverBackgroundColor: 'rgba(37, 99, 235, 0.9)'
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    animation: animationOptions,
                    plugins: {
                        tooltip: {
                            callbacks: {
                                title: function(tooltipItems) {
                                    return tooltipItems[0].label;
                                },
                                label: function(context) {
                                    const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                    return `${context.parsed.y} members (${Math.round(context.parsed.y / total * 100)}%)`;
                                }
                            }
                        }
                    },
                    scales: {
                        y: {
                            grid: {
                                drawBorder: false,
                                color: 'rgba(226, 232, 240, 0.5)'
                            },
                            ticks: {
                                padding: 10,
                                font: {
                                    size: 11
                                }
                            },
                            beginAtZero: true,
                            title: {
                                display: false
                            }
                        },
                        x: {
                            grid: {
                                display: false,
                                drawBorder: false
                            },
                            ticks: {
                                font: {
                                    size: 11
                                }
                            },
                            title: {
                                display: false
                            }
                        }
                    },
                    interaction: {
                        intersect: false,
                        mode: 'index'
                    }
                }
            });

            // Initialize Time of Day chart with enhanced animations
            const timeOfDayCtx = document.getElementById('time-of-day-chart').getContext('2d');
            
            // Create gradient for line area
            const lineGradient = timeOfDayCtx.createLinearGradient(0, 0, 0, 400);
            lineGradient.addColorStop(0, 'rgba(79, 70, 229, 0.2)');
            lineGradient.addColorStop(1, 'rgba(79, 70, 229, 0.0)');
            
            const timeOfDayChart = new Chart(timeOfDayCtx, {
                type: 'line',
                data: {
                    labels: data.timeOfDay.labels,
                    datasets: [{
                        label: 'Average Minutes',
                        data: data.timeOfDay.data,
                        fill: true,
                        backgroundColor: lineGradient,
                        borderColor: 'rgba(79, 70, 229, 1)',
                        tension: 0.4,
                        pointRadius: 4,
                        pointBackgroundColor: '#ffffff',
                        pointBorderColor: 'rgba(79, 70, 229, 1)',
                        pointBorderWidth: 2,
                        pointHoverRadius: 6,
                        pointHoverBackgroundColor: '#ffffff',
                        pointHoverBorderColor: 'rgba(79, 70, 229, 1)',
                        pointHoverBorderWidth: 3
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    animation: {
                        y: {
                            duration: 1000,
                            easing: 'easeOutQuart'
                        }
                    },
                    plugins: {
                        tooltip: {
                            callbacks: {
                                title: function(tooltipItems) {
                                    return tooltipItems[0].label;
                                },
                                label: function(context) {
                                    return `${context.parsed.y} minutes average workout time`;
                                }
                            }
                        }
                    },
                    scales: {
                        y: {
                            grid: {
                                drawBorder: false,
                                color: 'rgba(226, 232, 240, 0.5)'
                            },
                            ticks: {
                                padding: 10,
                                font: {
                                    size: 11
                                }
                            },
                            beginAtZero: true,
                            title: {
                                display: false
                            }
                        },
                        x: {
                            grid: {
                                display: false,
                                drawBorder: false
                            },
                            ticks: {
                                font: {
                                    size: 11
                                }
                            },
                            title: {
                                display: false
                            }
                        }
                    },
                    interaction: {
                        intersect: false,
                        mode: 'index'
                    }
                }
            });

            // Initialize Membership chart with enhanced interactions
            const membershipCtx = document.getElementById('membership-chart').getContext('2d');
            const membershipChart = new Chart(membershipCtx, {
                type: 'doughnut',
                data: {
                    labels: data.membership.labels,
                    datasets: [{
                        data: data.membership.data,
                        backgroundColor: [
                            'rgba(59, 130, 246, 0.8)',
                            'rgba(139, 92, 246, 0.8)',
                            'rgba(245, 158, 11, 0.8)',
                            'rgba(16, 185, 129, 0.8)'
                        ],
                        borderColor: 'white',
                        borderWidth: 3,
                        hoverOffset: 15,
                        borderRadius: 4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    cutout: '70%',
                    animation: {
                        animateRotate: true,
                        animateScale: true,
                        duration: 1200
                    },
                    plugins: {
                        tooltip: {
                            callbacks: {
                                title: function(tooltipItems) {
                                    return tooltipItems[0].label;
                                },
                                label: function(context) {
                                    const value = context.parsed;
                                    const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                    const percentage = Math.round((value / total) * 100);
                                    return `${value} members (${percentage}%)`;
                                }
                            }
                        },
                        legend: {
                            display: true,
                            position: 'bottom',
                            labels: {
                                padding: 20,
                                usePointStyle: true,
                                pointStyle: 'circle',
                                font: {
                                    size: 11
                                }
                            }
                        }
                    }
                }
            });
            
            // Initialize Weekly Trends Chart (new)
            const weeklyTrendsCtx = document.getElementById('weekly-trends-chart').getContext('2d');
            
            // Create gradients for weekly trends
            const trendGradient1 = weeklyTrendsCtx.createLinearGradient(0, 0, 0, 400);
            trendGradient1.addColorStop(0, 'rgba(16, 185, 129, 0.2)');
            trendGradient1.addColorStop(1, 'rgba(16, 185, 129, 0.0)');
            
            const trendGradient2 = weeklyTrendsCtx.createLinearGradient(0, 0, 0, 400);
            trendGradient2.addColorStop(0, 'rgba(59, 130, 246, 0.1)');
            trendGradient2.addColorStop(1, 'rgba(59, 130, 246, 0.0)');
            
            const weeklyTrendsChart = new Chart(weeklyTrendsCtx, {
                type: 'line',
                data: {
                    labels: data.weeklyTrends.labels,
                    datasets: [
                        {
                            label: 'This Week',
                            data: data.weeklyTrends.current,
                            fill: true,
                            backgroundColor: trendGradient1,
                            borderColor: 'rgba(16, 185, 129, 1)',
                            tension: 0.4,
                            pointRadius: 4,
                            pointBackgroundColor: '#ffffff',
                            pointBorderColor: 'rgba(16, 185, 129, 1)',
                            pointBorderWidth: 2,
                            pointHoverRadius: 6,
                            pointHoverBackgroundColor: '#ffffff',
                            pointHoverBorderColor: 'rgba(16, 185, 129, 1)',
                            pointHoverBorderWidth: 3
                        },
                        {
                            label: 'Last Week',
                            data: data.weeklyTrends.previous,
                            fill: true,
                            backgroundColor: trendGradient2,
                            borderColor: 'rgba(59, 130, 246, 1)',
                            tension: 0.4,
                            pointRadius: 4,
                            pointBackgroundColor: '#ffffff',
                            pointBorderColor: 'rgba(59, 130, 246, 1)',
                            pointBorderWidth: 2,
                            pointHoverRadius: 6,
                            pointHoverBackgroundColor: '#ffffff',
                            pointHoverBorderColor: 'rgba(59, 130, 246, 1)',
                            pointHoverBorderWidth: 3,
                            borderDash: [5, 5]
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    animation: {
                        y: {
                            duration: 1000,
                            easing: 'easeOutQuart',
                            delay: (context) => context.dataIndex * 50
                        }
                    },
                    plugins: {
                        tooltip: {
                            callbacks: {
                                title: function(tooltipItems) {
                                    return tooltipItems[0].label;
                                },
                                label: function(context) {
                                    const datasetLabel = context.dataset.label || '';
                                    return `${datasetLabel}: ${context.parsed.y} check-ins`;
                                }
                            }
                        },
                        legend: {
                            display: true,
                            position: 'bottom',
                            labels: {
                                padding: 20,
                                usePointStyle: true,
                                pointStyle: 'circle',
                                font: {
                                    size: 11
                                }
                            }
                        }
                    },
                    scales: {
                        y: {
                            grid: {
                                drawBorder: false,
                                color: 'rgba(226, 232, 240, 0.5)'
                            },
                            ticks: {
                                padding: 10,
                                font: {
                                    size: 11
                                }
                            },
                            beginAtZero: true,
                            title: {
                                display: false
                            }
                        },
                        x: {
                            grid: {
                                display: false,
                                drawBorder: false
                            },
                            ticks: {
                                font: {
                                    size: 11
                                }
                            },
                            title: {
                                display: false
                            }
                        }
                    },
                    interaction: {
                        intersect: false,
                        mode: 'index'
                    }
                }
            });
            
            // Add interactivity to period selectors
            document.querySelectorAll('.period-selector').forEach(selector => {
                const buttons = selector.querySelectorAll('.period-button');
                buttons.forEach(button => {
                    button.addEventListener('click', () => {
                        // Remove active class from all buttons in this selector
                        buttons.forEach(btn => btn.classList.remove('active'));
                        // Add active class to clicked button
                        button.classList.add('active');
                        
                        // Here you would typically update the chart data based on the selected period
                        // For demo purposes, we'll just add a subtle animation
                        const chartContainer = selector.closest('.chart-card').querySelector('.chart-container canvas');
                        const chart = Chart.getChart(chartContainer);
                        if (chart) {
                            // Animate the chart with random data fluctuations to simulate period change
                            const newData = chart.data.datasets[0].data.map(value => 
                                value * (0.9 + Math.random() * 0.2) // Fluctuate by ±10%
                            );
                            chart.data.datasets[0].data = newData;
                            chart.update();
                        }
                    });
                });
            });
            
            // Add interactivity to custom legend items
            document.querySelectorAll('.legend-item').forEach((item, index) => {
                item.addEventListener('click', () => {
                    const chart = Chart.getChart('duration-distribution-chart');
                    if (chart) {
                        // Toggle visibility of the dataset
                        const meta = chart.getDatasetMeta(0);
                        meta.data[index].hidden = !meta.data[index].hidden;
                        chart.update();
                        
                        // Visual feedback for toggle state
                        if (meta.data[index].hidden) {
                            item.style.opacity = '0.5';
                        } else {
                            item.style.opacity = '1';
                        }
                    }
                });
            });
            
            // Add interactivity to chart action buttons
            document.querySelectorAll('.chart-action-button').forEach(button => {
                button.addEventListener('click', () => {
                    // This would typically trigger specific actions
                    // For demo purposes, we'll just show a notification
                    alert(`Action: ${button.getAttribute('title')}`);
                });
            });
        });
    </script>
    
@endsection