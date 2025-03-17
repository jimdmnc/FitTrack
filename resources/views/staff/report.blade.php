@extends('layouts.app') <!-- Assuming you have a main layout file -->

@section('content')
    <link href="https://cdnjs.cloudflare.com/ajax/libs/tailwindcss/2.2.19/tailwind.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.7.1/chart.min.js"></script>
    <style>
        .glass-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 10px;
            box-shadow: 0 8px 32px rgba(31, 38, 135, 0.15);
        }
        .gradient-bg {
            background: linear-gradient(120deg, #a1c4fd 0%, #c2e9fb 100%);
        }
        .transition-all {
            transition: all 0.3s ease;
        }
        body {
            background-color: #f9fafb;
            background-image: radial-gradient(#e2e8f0 1px, transparent 1px);
            background-size: 20px 20px;
        }
        .chart-container {
            position: relative;
            height: 300px;
            width: 100%;
        }
    </style>

    <section class="pt-10 mb-8">
        <div class=" bg-white p-6 rounded-lg shadow-lg shadow-gray-400 border border-gray-200">
            <div class="flex flex-col md:flex-row justify-between items-center gap-y-4 md:gap-y-0">
                <h2 class="font-extrabold text-lg sm:text-3xl text-gray-800">
                    <span class="bg-clip-text text-transparent bg-gradient-to-r from-blue-600 to-indigo-700 leading-snug">Gym Activity Dashboard</span>
                </h2>
            <div class="flex flex-col md:flex-row items-center space-y-2 md:space-y-0 md:space-x-4"> 
                    <span class="bg-green-100 text-green-800 text-xs font-semibold px-4 py-1 rounded-full whitespace-nowrap"> 
                        <i class="fas fa-circle text-green-500 text-xs mr-1"></i> LIVE
                    </span>
                    <span class="text-xs md:text-sm text-gray-500 whitespace-nowrap"> 
                        Last updated: March 11, 2025 11:32 AM
                    </span>
                </div>
            </div>
        </div>
    </section>

        <!-- Search & Filter Section with Modern UI -->
        <div class="glass-card mb-8 p-4">
            <div class="flex flex-col md:flex-row items-center justify-between gap-4">
                <div class="relative w-full md:w-72">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="fas fa-search text-gray-400"></i>
                    </div>
                    <input type="text" placeholder="Search members..." class="pl-10 pr-4 py-2 w-full border-0 focus:ring-2 focus:ring-blue-500 rounded-lg shadow-sm" />
                </div>
                
                <div class="flex flex-wrap items-center gap-3 w-full md:w-auto">
                    <div class="relative">
                        <select id="date-filter" class="appearance-none pl-4 pr-10 py-2 border-0 bg-white rounded-lg shadow-sm focus:ring-2 focus:ring-blue-500">
                            <option value="today">Today</option>
                            <option value="thisWeek" selected>This Week</option>
                            <option value="thisMonth">This Month</option>
                            <option value="thisYear">This Year</option>
                            <option value="custom">Custom Range</option>
                        </select>
                        <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-400">
                            
                        </div>
                    </div>
                    
                    <button id="export-report" class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-blue-600 to-indigo-700 text-white rounded-lg shadow hover:from-blue-700 hover:to-indigo-800 transition-all">
                        <i class="fas fa-download mr-2"></i> Export
                    </button>
                </div>
            </div>
        </div>

        <!-- Stats Cards with Improved Design -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-5 mb-8">
            <div class="glass-card p-5 transition-all hover:shadow-lg hover:translate-y-[-5px]">
                <div class="flex justify-between items-start">
                    <div>
                        <h3 class="text-gray-500 text-sm font-medium mb-1">Active Members</h3>
                        <div class="text-3xl font-bold text-gray-900">267</div>
                        <div class="text-green-600 text-sm font-medium mt-1">
                            <i class="fas fa-arrow-up mr-1"></i> 12% vs last week
                        </div>
                    </div>
                    <div class="p-3 rounded-lg bg-blue-100 text-blue-600">
                        <i class="fas fa-users"></i>
                    </div>
                </div>
            </div>
            
            <div class="glass-card p-5 transition-all hover:shadow-lg hover:translate-y-[-5px]">
                <div class="flex justify-between items-start">
                    <div>
                        <h3 class="text-gray-500 text-sm font-medium mb-1">Avg Session</h3>
                        <div class="text-3xl font-bold text-gray-900">86 min</div>
                        <div class="text-green-600 text-sm font-medium mt-1">
                            <i class="fas fa-arrow-up mr-1"></i> 4% vs last week
                        </div>
                    </div>
                    <div class="p-3 rounded-lg bg-purple-100 text-purple-600">
                        <i class="fas fa-stopwatch"></i>
                    </div>
                </div>
            </div>
            
            <div class="glass-card p-5 transition-all hover:shadow-lg hover:translate-y-[-5px]">
                <div class="flex justify-between items-start">
                    <div>
                        <h3 class="text-gray-500 text-sm font-medium mb-1">Peak Hour</h3>
                        <div class="text-3xl font-bold text-gray-900">6-7 PM</div>
                        <div class="text-gray-500 text-sm font-medium mt-1">
                            <i class="fas fa-users mr-1"></i> 42 members
                        </div>
                    </div>
                    <div class="p-3 rounded-lg bg-amber-100 text-amber-600">
                        <i class="fas fa-clock"></i>
                    </div>
                </div>
            </div>
            
            <div class="glass-card p-5 transition-all hover:shadow-lg hover:translate-y-[-5px]">
                <div class="flex justify-between items-start">
                    <div>
                        <h3 class="text-gray-500 text-sm font-medium mb-1">Today's Check-ins</h3>
                        <div class="text-3xl font-bold text-gray-900">128</div>
                        <div class="text-red-600 text-sm font-medium mt-1">
                            <i class="fas fa-arrow-down mr-1"></i> 3% vs yesterday
                        </div>
                    </div>
                    <div class="p-3 rounded-lg bg-green-100 text-green-600">
                        <i class="fas fa-check-circle"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Charts Section with Modern Cards -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
            <div class="glass-card p-6 transition-all hover:shadow-lg">
                <div class="flex justify-between items-center mb-6">
                    <h3 class="text-lg font-semibold text-gray-800">Daily Check-ins by Hour</h3>
                    <button class="text-gray-400 hover:text-gray-600">
                        <i class="fas fa-ellipsis-h"></i>
                    </button>
                </div>
                <div class="chart-container">
                    <canvas id="hourly-checkins-chart"></canvas>
                </div>
            </div>
            
            <div class="glass-card p-6 transition-all hover:shadow-lg">
                <div class="flex justify-between items-center mb-6">
                    <h3 class="text-lg font-semibold text-gray-800">Avg Time by Day of Week</h3>
                    <button class="text-gray-400 hover:text-gray-600">
                        <i class="fas fa-ellipsis-h"></i>
                    </button>
                </div>
                <div class="chart-container">
                    <canvas id="time-spent-chart"></canvas>
                </div>
            </div>
            
            <div class="glass-card p-6 transition-all hover:shadow-lg">
                <div class="flex justify-between items-center mb-6">
                    <h3 class="text-lg font-semibold text-gray-800">Session Duration Distribution</h3>
                    <button class="text-gray-400 hover:text-gray-600">
                        <i class="fas fa-ellipsis-h"></i>
                    </button>
                </div>
                <div class="chart-container">
                    <canvas id="duration-distribution-chart"></canvas>
                </div>
            </div>
            
            <div class="glass-card p-6 transition-all hover:shadow-lg">
                <div class="flex justify-between items-center mb-6">
                    <h3 class="text-lg font-semibold text-gray-800">Membership Distribution</h3>
                    <button class="text-gray-400 hover:text-gray-600">
                        <i class="fas fa-ellipsis-h"></i>
                    </button>
                </div>
                <div class="chart-container">
                    <canvas id="membership-chart"></canvas>
                </div>
            </div>
        </div>

        <!-- Members Table with Modern Design -->
        <div class="glass-card p-6 transition-all hover:shadow-lg">
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-lg font-semibold text-gray-800">Recent Active Members</h3>
                <button class="text-sm text-blue-600 hover:text-blue-800 font-medium">
                    View All <i class="fas fa-arrow-right ml-1"></i>
                </button>
            </div>
            
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead>
                        <tr>
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
                        <tr class="hover:bg-gray-50">
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
                                <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-purple-100 text-purple-800">Premium</span>
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
                        <tr class="hover:bg-gray-50">
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
                                <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-amber-100 text-amber-800">Premium+</span>
                            </td>
                            <td class="px-4 py-4 text-sm text-gray-500">Mar 11, 2025 10:15 AM</td>
                            <td class="px-4 py-4 text-sm text-gray-500">-</td>
                            <td class="px-4 py-4 text-sm text-gray-500">1h 17m (ongoing)</td>
                            <td class="px-4 py-4 whitespace-nowrap">
                                <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Active</span>
                            </td>
                            <td class="px-4 py-4 text-right text-sm">
                                <button class="text-indigo-600 hover:text-indigo-900 font-medium">View Details</button>
                            </td>
                        </tr>
                        <tr class="hover:bg-gray-50">
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
                                <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">Standard</span>
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
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
    // Chart.js global settings
    Chart.defaults.font.family = "'Inter', 'Helvetica', 'Arial', sans-serif";
    Chart.defaults.font.size = 12;
    Chart.defaults.color = '#64748b';
    Chart.defaults.plugins.tooltip.padding = 10;
    Chart.defaults.plugins.tooltip.cornerRadius = 6;
    Chart.defaults.plugins.tooltip.titleFont.size = 13;
    Chart.defaults.plugins.tooltip.titleColor = '#fff';
    Chart.defaults.plugins.tooltip.bodyColor = '#fff';
    Chart.defaults.plugins.tooltip.backgroundColor = 'rgba(17, 25, 40, 0.8)';
    Chart.defaults.plugins.tooltip.borderColor = 'rgba(255, 255, 255, 0.1)';
    Chart.defaults.plugins.tooltip.borderWidth = 1;
    Chart.defaults.plugins.tooltip.displayColors = false;
    Chart.defaults.plugins.legend.display = false;

    // Chart instances
    let hourlyCheckinsChart;
    let timeSpentChart;
    let durationDistributionChart;
    let membershipChart;

    // Data sets for different time periods
    const chartData = {
        today: {
            hourlyCheckins: [5, 12, 19, 17, 14, 10, 16, 14, 11, 15, 24, 30, 38, 28, 17, 8],
            timeSpent: [65, 70, 60, 75, 85, 105, 90],
            durationDistribution: [28, 62, 85, 35, 15],
            membership: [125, 78, 29]
        },
        thisWeek: {
            hourlyCheckins: [8, 17, 25, 22, 16, 14, 21, 18, 13, 19, 27, 38, 42, 31, 22, 10],
            timeSpent: [72, 78, 68, 82, 94, 115, 102],
            durationDistribution: [32, 78, 96, 42, 19],
            membership: [145, 87, 35]
        },
        thisMonth: {
            hourlyCheckins: [10, 20, 29, 25, 19, 17, 24, 21, 16, 22, 31, 42, 47, 35, 25, 14],
            timeSpent: [75, 82, 70, 85, 98, 120, 110],
            durationDistribution: [40, 95, 120, 60, 25],
            membership: [175, 105, 45]
        },
        thisYear: {
            hourlyCheckins: [12, 25, 35, 30, 22, 20, 28, 25, 20, 26, 37, 48, 55, 42, 30, 18],
            timeSpent: [80, 87, 75, 90, 105, 130, 118],
            durationDistribution: [52, 110, 145, 75, 38],
            membership: [210, 130, 55]
        },
        custom: {
            hourlyCheckins: [7, 15, 23, 20, 15, 12, 18, 16, 12, 17, 25, 35, 40, 29, 20, 9],
            timeSpent: [70, 76, 65, 80, 92, 112, 100],
            durationDistribution: [30, 75, 90, 40, 18],
            membership: [140, 85, 32]
        }
    };

    // Stats data for different time periods
    const statsData = {
        today: {
            activeMembers: 198,
            activeChange: 8,
            avgSession: 78,
            avgSessionChange: 2,
            peakHour: "5-6 PM",
            peakMembers: 38,
            checkIns: 98,
            checkInsChange: -5
        },
        thisWeek: {
            activeMembers: 267,
            activeChange: 12,
            avgSession: 86,
            avgSessionChange: 4,
            peakHour: "6-7 PM",
            peakMembers: 42,
            checkIns: 128,
            checkInsChange: -3
        },
        thisMonth: {
            activeMembers: 320,
            activeChange: 15,
            avgSession: 92,
            avgSessionChange: 6,
            peakHour: "6-7 PM",
            peakMembers: 47,
            checkIns: 156,
            checkInsChange: 5
        },
        thisYear: {
            activeMembers: 395,
            activeChange: 22,
            avgSession: 98,
            avgSessionChange: 10,
            peakHour: "6-7 PM",
            peakMembers: 55,
            checkIns: 182,
            checkInsChange: 12
        },
        custom: {
            activeMembers: 258,
            activeChange: 10,
            avgSession: 84,
            avgSessionChange: 3,
            peakHour: "6-7 PM",
            peakMembers: 40,
            checkIns: 125,
            checkInsChange: -2
        }
    };

    // Initialize all charts with "This Week" data
    initializeCharts('thisWeek');
    
    // Date filter change event
    document.getElementById('date-filter').addEventListener('change', function() {
        // Update dashboard with selected period data
        updateDashboard(this.value);
    });

    // Function to initialize charts
    function initializeCharts(period) {
        // Get data for selected period
        const data = chartData[period];
        
        // Initialize hourly check-ins chart
        const hourlyCheckinsCtx = document.getElementById('hourly-checkins-chart').getContext('2d');
        hourlyCheckinsChart = new Chart(hourlyCheckinsCtx, {
            type: 'bar',
            data: {
                labels: ['6 AM', '7 AM', '8 AM', '9 AM', '10 AM', '11 AM', '12 PM', '1 PM', '2 PM', '3 PM', '4 PM', '5 PM', '6 PM', '7 PM', '8 PM', '9 PM'],
                datasets: [{
                    label: 'Check-ins',
                    data: data.hourlyCheckins,
                    backgroundColor: function(context) {
                        const index = context.dataIndex;
                        const value = context.dataset.data[index];
                        const maxValue = Math.max(...context.dataset.data);
                        const alpha = 0.5 + (value / maxValue) * 0.5;
                        return index >= 11 ? `rgba(79, 70, 229, ${alpha})` : `rgba(59, 130, 246, ${alpha})`;
                    },
                    borderColor: function(context) {
                        return context.dataIndex >= 11 ? 'rgba(79, 70, 229, 1)' : 'rgba(59, 130, 246, 1)';
                    },
                    borderWidth: 1,
                    borderRadius: 4,
                    maxBarThickness: 16
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    tooltip: {
                        callbacks: {
                            title: function(tooltipItems) {
                                return tooltipItems[0].label;
                            },
                            label: function(context) {
                                return `${context.parsed.y} members checked in`;
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        grid: {
                            drawBorder: false,
                            color: 'rgba(226, 232, 240, 0.7)'
                        },
                        ticks: {
                            padding: 10
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
                        title: {
                            display: false
                        }
                    }
                }
            }
        });

        // Initialize time spent chart
        const timeSpentCtx = document.getElementById('time-spent-chart').getContext('2d');
        timeSpentChart = new Chart(timeSpentCtx, {
            type: 'line',
            data: {
                labels: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'],
                datasets: [{
                    label: 'Average Minutes',
                    data: data.timeSpent,
                    fill: {
                        target: 'origin',
                        above: 'rgba(147, 51, 234, 0.1)'
                    },
                    backgroundColor: 'rgba(147, 51, 234, 0.7)',
                    borderColor: 'rgba(147, 51, 234, 1)',
                    tension: 0.4,
                    pointRadius: 6,
                    pointBackgroundColor: 'white',
                    pointBorderColor: 'rgba(147, 51, 234, 1)',
                    pointBorderWidth: 2,
                    pointHoverRadius: 8,
                    pointHoverBackgroundColor: 'white',
                    pointHoverBorderColor: 'rgba(147, 51, 234, 1)',
                    pointHoverBorderWidth: 3
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    tooltip: {
                        callbacks: {
                            title: function(tooltipItems) {
                                return tooltipItems[0].label;
                            },
                            label: function(context) {
                                return `${context.parsed.y} minutes average workout`;
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        grid: {
                            drawBorder: false,
                            color: 'rgba(226, 232, 240, 0.7)'
                        },
                        ticks: {
                            padding: 10
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
                        title: {
                            display: false
                        }
                    }
                }
            }
        });

        // Initialize duration distribution chart
        const durationDistributionCtx = document.getElementById('duration-distribution-chart').getContext('2d');
        durationDistributionChart = new Chart(durationDistributionCtx, {
            type: 'bar',
            data: {
                labels: ['0-30 min', '31-60 min', '61-90 min', '91-120 min', '120+ min'],
                datasets: [{
                    label: 'Members',
                    data: data.durationDistribution,
                    backgroundColor: [
                        'rgba(191, 219, 254, 0.9)',
                        'rgba(147, 197, 253, 0.9)',
                        'rgba(96, 165, 250, 0.9)',
                        'rgba(59, 130, 246, 0.9)',
                        'rgba(37, 99, 235, 0.9)'
                    ],
                    borderColor: 'rgba(255, 255, 255, 0.5)',
                    borderWidth: 2,
                    borderRadius: 8,
                    maxBarThickness: 40
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
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
                            color: 'rgba(226, 232, 240, 0.7)'
                        },
                        ticks: {
                            padding: 10
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
                        title: {
                            display: false
                        }
                    }
                }
            }
        });

        // Initialize membership chart
        const membershipCtx = document.getElementById('membership-chart').getContext('2d');
        membershipChart = new Chart(membershipCtx, {
            type: 'doughnut',
            data: {
                labels: ['Standard', 'Premium', 'Premium Plus'],
                datasets: [{
                    data: data.membership,
                    backgroundColor: [
                        'rgba(59, 130, 246, 0.8)',
                        'rgba(139, 92, 246, 0.8)',
                        'rgba(245, 158, 11, 0.8)'
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
                            pointStyle: 'circle'
                        }
                    }
                },
                animation: {
                    animateScale: true,
                    animateRotate: true
                }
            }
        });
    }

    // Function to update dashboard with new data
    function updateDashboard(period) {
        // Get data for selected period
        const data = chartData[period];
        const stats = statsData[period];
        
        // Update charts
        updateCharts(data);
        
        // Update stat cards
        updateStatCards(stats);
        
        // Show notification
        showNotification(period);
    }

    // Function to update charts with new data
    function updateCharts(data) {
        // Update hourly check-ins chart
        hourlyCheckinsChart.data.datasets[0].data = data.hourlyCheckins;
        hourlyCheckinsChart.update();
        
        // Update time spent chart
        timeSpentChart.data.datasets[0].data = data.timeSpent;
        timeSpentChart.update();
        
        // Update duration distribution chart
        durationDistributionChart.data.datasets[0].data = data.durationDistribution;
        durationDistributionChart.update();
        
        // Update membership chart
        membershipChart.data.datasets[0].data = data.membership;
        membershipChart.update();
    }

    // Function to update stat cards
    function updateStatCards(stats) {
        // Update active members
        document.querySelector('.glass-card:nth-child(1) .text-3xl').textContent = stats.activeMembers;
        const activeTrend = document.querySelector('.glass-card:nth-child(1) .text-green-600');
        activeTrend.innerHTML = `<i class="fas fa-arrow-up mr-1"></i> ${stats.activeChange}% vs last week`;
        
        // Update avg session
        document.querySelector('.glass-card:nth-child(2) .text-3xl').textContent = `${stats.avgSession} min`;
        const avgSessionTrend = document.querySelector('.glass-card:nth-child(2) .text-green-600');
        avgSessionTrend.innerHTML = `<i class="fas fa-arrow-up mr-1"></i> ${stats.avgSessionChange}% vs last week`;
        
        // Update peak hour
        document.querySelector('.glass-card:nth-child(3) .text-3xl').textContent = stats.peakHour;
        document.querySelector('.glass-card:nth-child(3) .text-gray-500').innerHTML = `<i class="fas fa-users mr-1"></i> ${stats.peakMembers} members`;
        
        // Update today's check-ins
        document.querySelector('.glass-card:nth-child(4) .text-3xl').textContent = stats.checkIns;
        const checkInsTrend = document.querySelector('.glass-card:nth-child(4) .text-red-600');
        
        if (stats.checkInsChange >= 0) {
            checkInsTrend.classList.remove('text-red-600');
            checkInsTrend.classList.add('text-green-600');
            checkInsTrend.innerHTML = `<i class="fas fa-arrow-up mr-1"></i> ${stats.checkInsChange}% vs yesterday`;
        } else {
            checkInsTrend.classList.remove('text-green-600');
            checkInsTrend.classList.add('text-red-600');
            checkInsTrend.innerHTML = `<i class="fas fa-arrow-down mr-1"></i> ${Math.abs(stats.checkInsChange)}% vs yesterday`;
        }
    }

    // Function to show notification
    function showNotification(period) {
        // Format period text for display
        let periodText;
        switch(period) {
            case 'today':
                periodText = 'Today';
                break;
            case 'thisWeek':
                periodText = 'This Week';
                break;
            case 'thisMonth':
                periodText = 'This Month';
                break;
            case 'thisYear':
                periodText = 'This Year';
                break;
            case 'custom':
                periodText = 'Custom Range';
                break;
            default:
                periodText = period;
        }
        
        // Create notification element
        const notification = document.createElement('div');
        notification.className = 'fixed top-4 right-4 bg-blue-600 text-white px-4 py-3 rounded-lg shadow-lg flex items-center z-50';
        notification.innerHTML = `
            <i class="fas fa-info-circle mr-2"></i>
            <span>Dashboard updated to show data for: ${periodText}</span>
        `;
        
        // Add animation styles
        notification.style.animation = 'fadeInDown 0.5s forwards';
        notification.style.opacity = '0';
        notification.style.transform = 'translateY(-20px)';
        
        // Add animation keyframes
        const style = document.createElement('style');
        style.innerHTML = `
            @keyframes fadeInDown {
                from {
                    opacity: 0;
                    transform: translateY(-20px);
                }
                to {
                    opacity: 1;
                    transform: translateY(0);
                }
            }
            @keyframes fadeOutUp {
                from {
                    opacity: 1;
                    transform: translateY(0);
                }
                to {
                    opacity: 0;
                    transform: translateY(-20px);
                }
            }
        `;
        document.head.appendChild(style);
        
        // Add notification to body
        document.body.appendChild(notification);
        
        // Remove notification after 3 seconds
        setTimeout(() => {
            notification.style.animation = 'fadeOutUp 0.5s forwards';
            setTimeout(() => {
                document.body.removeChild(notification);
            }, 500);
        }, 3000);
    }

    // Export report button
    document.getElementById('export-report').addEventListener('click', function() {
        const notification = document.createElement('div');
        notification.className = 'fixed top-4 right-4 bg-green-600 text-white px-4 py-3 rounded-lg shadow-lg flex items-center z-50';
        notification.style.animation = 'fadeInDown 0.5s forwards';
        notification.style.opacity = '0';
        notification.style.transform = 'translateY(-20px)';
        
        notification.innerHTML = `
            <i class="fas fa-check-circle mr-2"></i>
            <span>Report successfully exported! Check your downloads folder.</span>
        `;
        
        document.body.appendChild(notification);
        
        setTimeout(() => {
            notification.style.animation = 'fadeOutUp 0.5s forwards';
            setTimeout(() => {
                document.body.removeChild(notification);
            }, 500);
        }, 3000);
    });
});
</script>

@endsection