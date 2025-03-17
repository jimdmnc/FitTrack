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
                            Fitness Center Dashboard
                        </h1>
                        <p class="text-gray-500 mt-2">Track and analyze your gym's performance</p>
                    </div>
                    <div class="mt-4 md:mt-0 flex items-center gap-3">
                        <button class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg shadow-md flex items-center gap-2 text-sm font-medium transition-all btn-primary">
                            <i class="fas fa-download"></i> Export Data
                        </button>
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
            <div class="glass-card p-5">
                <div class="flex justify-between items-start">
                    <div>
                        <h3 class="text-gray-500 text-sm font-medium mb-1">Current Members</h3>
                        <div class="text-3xl font-bold text-gray-900 inline-block mr-1">326 </div>
                        <span class="text-lg font-semibold ">people</span>     
                        <div class="text-green-600 text-sm font-medium mt-1">
                            <i class="fas fa-arrow-up mr-1"></i> 12% vs last week
                        </div>
                    </div>
                    <div class="p-3 rounded-lg bg-blue-100 text-blue-600 stat-card-icon">
                        <i class="fas fa-users"></i>
                    </div>
                </div>
            </div>
            
            <div class="glass-card p-5">
                <div class="flex justify-between items-start">
                    <div>
                        <h3 class="text-gray-500 text-sm font-medium mb-1">New Members</h3>
                        <div class="text-3xl font-bold text-gray-900 inline-block mr-1">15</div>
                        <span class="text-lg font-semibold ">people</span>
                        <div class="text-green-600 text-sm font-medium mt-1">
                            <i class="fas fa-arrow-up mr-1"></i> 4% vs last week
                        </div>
                    </div>
                    <div class="p-3 rounded-lg bg-purple-100 text-purple-600 stat-card-icon">
                        <i class="fas fa-user-plus"></i>
                    </div>
                </div>
            </div>
            
            <div class="glass-card p-5">
                <div class="flex justify-between items-start">
                    <div>
                        <h3 class="text-gray-500 text-sm font-medium mb-1">Today's Walk-ins</h3>
                        <div class="text-3xl font-bold text-gray-900 inline-block mr-1">20</div>
                        <span class="text-lg font-semibold">people</span>
                        <div class="text-red-600 text-sm font-medium mt-1">
                            <i class="fas fa-arrow-down mr-1"></i> 2% vs yesterday
                        </div>
                    </div>
                    <div class="p-3 rounded-lg bg-amber-100 text-amber-600 stat-card-icon">
                        <i class="fas fa-walking"></i>
                    </div>
                </div>
            </div>
            
            <div class="glass-card p-5">
                <div class="flex justify-between items-start">
                    <div>
                        <h3 class="text-gray-500 text-sm font-medium mb-1">Today's Check-ins</h3>
                        <div class="text-3xl font-bold text-gray-900 inline-block mr-1">128</div>
                        <span class="text-lg font-semibold">people</span>
                        <div class="text-green-600 text-sm font-medium mt-1">
                            <i class="fas fa-arrow-up mr-1"></i> 3% vs yesterday
                        </div>
                    </div>
                    <div class="p-3 rounded-lg bg-green-100 text-green-600 stat-card-icon">
                        <i class="fas fa-check-circle"></i>
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
            <h3 class="text-lg font-semibold text-gray-800">Gym Members</h3>
            <p class="text-sm text-gray-500 mt-1">Recent activity and status</p>
        </div>
        <div class="flex items-center gap-3">
            <div class="relative">
                <input type="text" placeholder="Search members..." class="px-4 py-2 pl-10 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 w-64">
                <div class="absolute left-3 top-2.5 text-gray-400">
                    <i class="fas fa-search"></i>
                </div>
            </div>
            <button class="text-sm text-indigo-600 hover:text-indigo-800 font-medium flex items-center">
                View All <i class="fas fa-arrow-right ml-1"></i>
            </button>
        </div>
    </div>
    
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead>
                <tr class="bg-gray-50 rounded-lg">
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Member</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Membership</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Check-in</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Check-out</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Duration</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                <tr class="hover:bg-gray-50 transition-colors member-table-row">
                    <td class="px-4 py-4 whitespace-nowrap">
                        <div class="flex items-center">
                            <div class="h-10 w-10 flex-shrink-0 mr-3">
                                <div class="h-10 w-10 rounded-full bg-blue-100 flex items-center justify-center">
                                    <span class="text-blue-600 font-semibold">JD</span>
                                </div>
                            </div>
                            <div>
                                <div class="text-sm font-medium text-gray-900">John Doe</div>
                                <div class="text-sm text-gray-500">john.doe@example.com</div>
                            </div>
                        </div>
                    </td>
                    <td class="px-4 py-4 whitespace-nowrap">
                        <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-purple-100 text-purple-800">Annual</span>
                    </td>
                    <td class="px-4 py-4 text-sm text-gray-500">Mar 11, 2025 09:00 AM</td>
                    <td class="px-4 py-4 text-sm text-gray-500">Mar 11, 2025 11:05 AM</td>
                    <td class="px-4 py-4 text-sm text-gray-500">2h 5m</td>
                    <td class="px-4 py-4 whitespace-nowrap">
                        <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">Checked Out</span>
                    </td>
                    <td class="px-4 py-4 text-right text-sm">
                        <button class="text-indigo-600 hover:text-indigo-900 font-medium">View Details</button>
                    </td>
                </tr>
                <tr class="hover:bg-gray-50 transition-colors member-table-row">
                    <td class="px-4 py-4 whitespace-nowrap">
                        <div class="flex items-center">
                            <div class="h-10 w-10 flex-shrink-0 mr-3">
                                <div class="h-10 w-10 rounded-full bg-green-100 flex items-center justify-center">
                                    <span class="text-green-600 font-semibold">AS</span>
                                </div>
                            </div>
                            <div>
                                <div class="text-sm font-medium text-gray-900">Alice Smith</div>
                                <div class="text-sm text-gray-500">alice.smith@example.com</div>
                            </div>
                        </div>
                    </td>
                    <td class="px-4 py-4 whitespace-nowrap">
                        <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-amber-100 text-amber-800">Weekly</span>
                    </td>
                    <td class="px-4 py-4 text-sm text-gray-500">Mar 11, 2025 10:15 AM</td>
                    <td class="px-4 py-4 text-sm text-gray-500">-</td>
                    <td class="px-4 py-4 text-sm text-gray-500">1h 17m <span class="animate-pulse text-green-600">(ongoing)</span></td>
                    <td class="px-4 py-4 whitespace-nowrap">
                        <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Active</span>
                    </td>
                    <td class="px-4 py-4 text-right text-sm">
                        <button class="text-indigo-600 hover:text-indigo-900 font-medium">View Details</button>
                    </td>
                </tr>
                <tr class="hover:bg-gray-50 transition-colors member-table-row">
                    <td class="px-4 py-4 whitespace-nowrap">
                        <div class="flex items-center">
                            <div class="h-10 w-10 flex-shrink-0 mr-3">
                                <div class="h-10 w-10 rounded-full bg-red-100 flex items-center justify-center">
                                    <span class="text-red-600 font-semibold">RJ</span>
                                </div>
                            </div>
                            <div>
                                <div class="text-sm font-medium text-gray-900">Robert Johnson</div>
                                <div class="text-sm text-gray-500">robert.j@example.com</div>
                            </div>
                        </div>
                    </td>
                    <td class="px-4 py-4 whitespace-nowrap">
                        <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">Monthly</span>
                    </td>
                    <td class="px-4 py-4 text-sm text-gray-500">Mar 11, 2025 08:30 AM</td>
                    <td class="px-4 py-4 text-sm text-gray-500">Mar 11, 2025 09:45 AM</td>
                    <td class="px-4 py-4 text-sm text-gray-500">1h 15m</td>
                    <td class="px-4 py-4 whitespace-nowrap">
                        <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">Checked Out</span>
                    </td>
                    <td class="px-4 py-4 text-right text-sm">
                        <button class="text-indigo-600 hover:text-indigo-900 font-medium">View Details</button>
                    </td>
                </tr>
                <!-- Additional row -->
                <tr class="hover:bg-gray-50 transition-colors member-table-row">
                    <td class="px-4 py-4 whitespace-nowrap">
                        <div class="flex items-center">
                            <div class="h-10 w-10 flex-shrink-0 mr-3">
                                <div class="h-10 w-10 rounded-full bg-indigo-100 flex items-center justify-center">
                                    <span class="text-indigo-600 font-semibold">MP</span>
                                </div>
                            </div>
                            <div>
                                <div class="text-sm font-medium text-gray-900">Maria Parker</div>
                                <div class="text-sm text-gray-500">m.parker@example.com</div>
                            </div>
                        </div>
                    </td>
                    <td class="px-4 py-4 whitespace-nowrap">
                        <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-purple-100 text-purple-800">Annual</span>
                    </td>
                    <td class="px-4 py-4 text-sm text-gray-500">Mar 11, 2025 07:45 AM</td>
                    <td class="px-4 py-4 text-sm text-gray-500">-</td>
                    <td class="px-4 py-4 text-sm text-gray-500">3h 47m <span class="animate-pulse text-green-600">(ongoing)</span></td>
                    <td class="px-4 py-4 whitespace-nowrap">
                        <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Active</span>
                    </td>
                    <td class="px-4 py-4 text-right text-sm">
                        <button class="text-indigo-600 hover:text-indigo-900 font-medium">View Details</button>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
    
    <!-- Pagination -->
    <div class="mt-6 flex items-center justify-between">
        <div class="text-sm text-gray-500">
            Showing <span class="font-medium">1</span> to <span class="font-medium">4</span> of <span class="font-medium">326</span> members
        </div>
        <div class="flex items-center space-x-2">
            <button class="px-3 py-1 rounded border border-gray-200 text-gray-400">
                <i class="fas fa-chevron-left"></i>
            </button>
            <button class="px-3 py-1 rounded bg-indigo-600 text-white">1</button>
            <button class="px-3 py-1 rounded border border-gray-200 hover:bg-gray-50">2</button>
            <button class="px-3 py-1 rounded border border-gray-200 hover:bg-gray-50">3</button>
            <button class="px-3 py-1 rounded border border-gray-200 hover:bg-gray-50">...</button>
            <button class="px-3 py-1 rounded border border-gray-200 hover:bg-gray-50">82</button>
            <button class="px-3 py-1 rounded border border-gray-200 hover:bg-gray-50 text-gray-700">
                <i class="fas fa-chevron-right"></i>
            </button>
        </div>
    </div>
</div>
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