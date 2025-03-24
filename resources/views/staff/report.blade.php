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
        .hidden {
            display: none;
        }
    </style>

    <section class="pt-10 mb-8">
        <div class="bg-white p-6 rounded-lg shadow-lg shadow-gray-400 border border-gray-200">
            <div class="flex flex-col md:flex-row justify-between items-center gap-y-4 md:gap-y-0">
                <h2 class="font-extrabold text-lg sm:text-3xl text-gray-800">
                    <span class="bg-clip-text text-transparent bg-gradient-to-r from-blue-600 to-indigo-700 leading-snug">Reports</span>
                </h2>
                <div class="flex flex-col md:flex-row items-center space-y-2 md:space-y-0 md:space-x-4">
                    <span class="bg-green-100 text-green-800 text-xs font-semibold px-4 py-1 rounded-full whitespace-nowrap">
                        <i class="fas fa-circle text-green-500 text-xs mr-1"></i> LIVE
                    </span>
                    <span class="text-xs md:text-sm text-gray-500 whitespace-nowrap">
                        Last updated: {{ now()->format('F j, Y h:i A') }}
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
                    <select id="report-type" class="appearance-none pl-4 pr-10 py-2 border-0 bg-white rounded-lg shadow-sm focus:ring-2 focus:ring-blue-500">
                        <option value="finance">Payment Report</option>
                        <option value="members">Member Report</option>
                    </select>
                    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-400">
                        <i class="fas fa-chevron-down"></i>
                    </div>
                </div>

                <div class="relative">
                    <select id="date-filter" class="appearance-none pl-4 pr-10 py-2 border-0 bg-white rounded-lg shadow-sm focus:ring-2 focus:ring-blue-500">
                        <option value="today">Today</option>
                        <option value="thisWeek" selected>This Week</option>
                        <option value="thisMonth">This Month</option>
                        <option value="thisYear">This Year</option>
                        <option value="custom">Custom Range</option>
                    </select>
                    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-400">
                        <i class="fas fa-chevron-down"></i>
                    </div>
                </div>

                <!-- Custom Date Range Inputs (Hidden by Default) -->
                <div id="custom-date-range" class="hidden flex flex-wrap gap-3">
                    <input type="date" id="start-date" class="pl-4 pr-10 py-2 border-0 bg-white rounded-lg shadow-sm focus:ring-2 focus:ring-blue-500" />
                    <input type="date" id="end-date" class="pl-4 pr-10 py-2 border-0 bg-white rounded-lg shadow-sm focus:ring-2 focus:ring-blue-500" />
                </div>

                <button id="export-report" class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-blue-600 to-indigo-700 text-white rounded-lg shadow hover:from-blue-700 hover:to-indigo-800 transition-all">
                    <i class="fas fa-download mr-2"></i> Export
                </button>
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
                    <!-- Sample Data -->
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
                    <!-- Add more rows as needed -->
                </tbody>
            </table>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const dateFilter = document.getElementById('date-filter');
            const reportType = document.getElementById('report-type');
            const exportButton = document.getElementById('export-report');
            const customDateRange = document.getElementById('custom-date-range');
            const startDateInput = document.getElementById('start-date');
            const endDateInput = document.getElementById('end-date');

            // Show/hide custom date range inputs
            dateFilter.addEventListener('change', function () {
                if (this.value === 'custom') {
                    customDateRange.classList.remove('hidden');
                } else {
                    customDateRange.classList.add('hidden');
                }
            });

            // Handle report export
            exportButton.addEventListener('click', function () {
                const type = reportType.value; // 'finance' or 'members'
                const period = dateFilter.value; // 'today', 'thisWeek', etc.
                const startDate = startDateInput.value;
                const endDate = endDateInput.value;

                // Build the export URL
                let exportUrl = `/staff/reports/export?type=${type}&period=${period}`;
                if (period === 'custom' && startDate && endDate) {
                    exportUrl += `&start_date=${startDate}&end_date=${endDate}`;
                }

                // Redirect to the export endpoint
                window.location.href = exportUrl;
            });
        });
    </script>
@endsection