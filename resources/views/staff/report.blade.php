@extends('layouts.app')

@section('content')
@if(session('error'))
    <div id="flashMessage" class="fixed top-4 right-4 z-50 max-w-md p-4 mb-4 border-l-4 border-red-500 bg-red-900 text-red-100 rounded-lg shadow-lg" role="alert">
        <div class="flex items-center">
            <div class="flex-shrink-0 mr-3">
                <svg class="h-5 w-5 text-red-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                </svg>
            </div>
            <div>
                <p class="text-sm font-medium">{{ session('error') }}</p>
            </div>
        </div>
        <button type="button" class="absolute top-2 right-2 text-red-300 hover:text-white" onclick="this.parentElement.remove()">
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
        </button>
    </div>
    <script>
        setTimeout(function() {
            const flashMessage = document.getElementById('flashMessage');
            if (flashMessage) {
                flashMessage.style.opacity = '0';
                flashMessage.style.transform = 'translateY(-10px)';
                setTimeout(() => flashMessage.remove(), 300);
            }
        }, 5000);
    </script>
@endif

@if(session('success'))
    <div id="flashMessage" class="fixed top-4 right-4 z-50 max-w-md p-4 mb-4 border-l-4 border-green-500 bg-green-900 text-green-100 rounded-lg shadow-lg" role="alert">
        <div class="flex items-center">
            <div class="flex-shrink-0 mr-3">
                <svg class="h-5 w-5 text-green-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
            </div>
            <div>
                <p class="text-sm font-medium">{{ session('success') }}</p>
            </div>
        </div>
        <button type="button" class="absolute top-2 right-2 text-green-300 hover:text-white" onclick="this.parentElement.remove()">
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
        </button>
    </div>
    <script>
        setTimeout(function() {
            const flashMessage = document.getElementById('flashMessage');
            if (flashMessage) {
                flashMessage.style.opacity = '0';
                flashMessage.style.transform = 'translateY(-10px)';
                setTimeout(() => flashMessage.remove(), 300);
            }
        }, 5000);
    </script>
@endif

<style>
    #flashMessage {
        transition: opacity 0.3s ease, transform 0.3s ease;
    }
</style>
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
            background-color: #1e1e1e;
            background-image: none;
        }

        .chart-container {
            position: relative;
            height: 300px;
            width: 100%;
        }
        .hidden {
            display: none;
        }
        
        /* Responsive table container */
        .table-responsive {
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }
        
        /* Custom scrollbar for tables */
        .table-responsive::-webkit-scrollbar {
            height: 8px;
        }
        .table-responsive::-webkit-scrollbar-track {
            background: #2d2d2d;
        }
        .table-responsive::-webkit-scrollbar-thumb {
            background-color: #ff5722;
            border-radius: 20px;
        }

        /* Pagination Styles */
        .pagination {
            display: flex;
            justify-content: center;
            align-items: center;
            list-style: none;
            padding: 0;
            margin: 1rem 0;
        }

        .pagination li {
            margin: 0 2px;
        }

        .pagination li a,
        .pagination li span {
            display: flex;
            align-items: center;
            justify-content: center;
            min-width: 32px;
            height: 32px;
            padding: 0 8px;
            color: #b9b9b9;
            background-color: #2d2d2d;
            border-radius: 6px;
            font-size: 0.875rem;
            text-decoration: none;
            transition: all 0.2s ease;
        }

        .pagination li.active span {
            background-color: #ff5722;
            color: #fff;
            font-weight: 600;
        }

        .pagination li a:hover {
            background-color: #3d3d3d;
            color: #fff;
        }

        .pagination li.disabled span {
            background-color: #202020;
            color: #666;
            cursor: not-allowed;
        }

        @media (max-width: 640px) {
            .pagination li a,
            .pagination li span {
                min-width: 28px;
                height: 28px;
                font-size: 0.75rem;
            }
        }

    </style>

    <div class="mb-20 px-4 sm:px-6 md:px-8">
        <div class="py-6 md:py-8">
            <h1 class="text-2xl sm:text-3xl md:text-4xl font-bold bg-clip-text text-transparent bg-gradient-to-r from-red-600 to-orange-600" id="reportTitle">Members Report</h1>
            <p class="text-gray-200 text-sm sm:text-base">View and analyze your data with ease</p>
        </div>
        <div x-data="reportFilter()" class="max-w-8xl mb-20 space-y-6">
            <!-- Header -->
            <div class="flex flex-col space-y-4 sm:space-y-6 md:ml-0">            
                <!-- Enhanced Filter Section -->
                <div class="w-full">
                    <div class="flex flex-col sm:flex-row gap-4 flex-wrap">
                      <!-- Date Filter -->
                    <div class="w-full sm:w-auto">
                        <label class="block mb-1.5 text-sm font-medium text-gray-200">Time Period</label>
                        <div class="relative">
                            <select 
                                id="dateFilter"
                                class="appearance-none bg-[#1e1e1e] border border-[#666666] hover:border-[#ff5722] rounded-md pl-3 pr-10 py-2 text-gray-200 w-full focus:outline-none focus:ring-2 focus:ring-[#ff5722] focus:border-[#ff5722] transition-colors">
                                <option value="" data-all-time>All Time</option>
                                <option value="today">Today</option>
                                <option value="yesterday">Yesterday</option>
                                <option value="last7">Last 7 Days</option>
                                <option value="last30">Last 30 Days</option>
                                <option value="custom">Custom Range</option>
                            </select>
                        </div>
                    </div>
                        
                        <!-- Report Type -->
                        <div class="w-full sm:w-auto">
                            <label class="block mb-1.5 text-sm font-medium text-gray-200">Report Type</label>
                            <div class="relative">
                                <select 
                                    id="reportType" 
                                    class="appearance-none bg-[#1e1e1e] border border-[#666666] hover:border-[#ff5722] rounded-md pl-3 pr-10 py-2 text-gray-200 w-full focus:outline-none focus:ring-2 focus:ring-[#ff5722] focus:border-[#ff5722] transition-colors">
                                    <option value="members">Members Report</option>
                                    <option value="payments">Collection Report</option>
                                </select>
                            </div>
                        </div>
                        
                        <!-- Export Button -->
                        <div class="w-full sm:w-auto sm:self-end">
                            <button type="button" id="exportButton" class="w-full sm:w-auto bg-[#ff5722] text-gray-200 px-4 py-2 rounded-md shadow hover:bg-opacity-80 hover:translate-y-[-2px] transition flex items-center justify-center gap-2">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 9V2h12v7m-9 0H6a2 2 0 00-2 2v5h16v-5a2 2 0 00-2-2h-3m-9 0v5m0 0v4h12v-4m-3 4v1m-6-1v1" />
                                </svg>
                                Generate Report
                            </button>
                        </div>
                    </div>
                    
                    <!-- Custom Date Range Picker -->
                    <div id="customRange" class="hidden mt-4 flex flex-col sm:flex-row sm:items-center space-y-4 sm:space-y-0 sm:space-x-4">
                        <!-- Start Date -->
                        <div class="w-full sm:w-auto">
                            <label for="startDate" class="block text-sm font-medium text-gray-200">Start Date</label>
                            <input type="date" id="startDate" 
                                class="mt-1 block w-full px-4 py-2 bg-[#1e1e1e] border border-[#666666] hover:border-[#ff5722] rounded-md text-gray-200 focus:outline-none focus:ring-2 focus:ring-[#ff5722] focus:border-[#ff5722] transition-colors"
                                max="<?= date('Y-m-d') ?>"
                                onchange="updateEndDatePicker()">
                        </div>

                        <!-- End Date -->
                        <div class="w-full sm:w-auto">
                            <label for="endDate" class="block text-sm font-medium text-gray-200">End Date</label>
                            <input type="date" id="endDate" 
                                class="mt-1 block w-full px-4 py-2 bg-[#1e1e1e] border border-[#666666] hover:border-[#ff5722] rounded-md text-gray-200 focus:outline-none focus:ring-2 focus:ring-[#ff5722] focus:border-[#ff5722] transition-colors"
                                max="<?= date('Y-m-d') ?>">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Members Report Table -->
            <div id="membersReport" class="overflow-hidden rounded-lg">
                <div class="table-responsive">
                    <table class="min-w-full divide-y divide-black">
                        <thead class="bg-gradient-to-br from-[#2c2c2c] to-[#1e1e1e]">
                            <tr>
                                <th scope="col" class="px-4 sm:px-6 py-3 text-left text-xs font-medium text-gray-200 uppercase tracking-wider">#</th>
                                <th scope="col" class="px-4 sm:px-6 py-3 text-left text-xs font-medium text-gray-200 uppercase tracking-wider">Member</th>
                                <th scope="col" class="px-4 sm:px-6 py-3 text-left text-xs font-medium text-gray-200 uppercase tracking-wider">Membership</th>
                                <th scope="col" class="px-4 sm:px-6 py-3 text-left text-xs font-medium text-gray-200 uppercase tracking-wider">Date</th>
                                <th scope="col" class="px-4 sm:px-6 py-3 text-left text-xs font-medium text-gray-200 uppercase tracking-wider">Time In</th>
                                <th scope="col" class="px-4 sm:px-6 py-3 text-left text-xs font-medium text-gray-200 uppercase tracking-wider">Time Out</th>
                                <th scope="col" class="px-4 sm:px-6 py-3 text-left text-xs font-medium text-gray-200 uppercase tracking-wider">Contact</th>
                            </tr>
                        </thead>
                        <tbody class="bg-[#1e1e1e] divide-y divide-black" id="membersTableBody">
                            @if($attendances->count() > 0)
                                @foreach($attendances as $attendance)
                                    <tr class="@if($loop->even) bg-[#1e1e1e] @else bg-[#1e1e1e] @endif" data-date="{{ $attendance->time_in ? $attendance->time_in->format('Y-m-d') : '' }}">
                                        <!-- # -->
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-200">
                                            {{ ($attendances->currentPage() - 1) * $attendances->perPage() + $loop->iteration }}
                                        </td>

                                        <!-- Member -->
                                        <td class="px-4 sm:px-6 py-3 sm:py-4 whitespace-nowrap">
                                            <div class="ml-2 sm:ml-4">
                                                <div class="text-xs sm:text-sm font-medium text-gray-200">{{ $attendance->user->first_name }} {{ $attendance->user->last_name }}</div>
                                            </div>
                                        </td>

                                        <!-- Membership -->
                                        <td class="px-4 sm:px-6 py-3 sm:py-4 whitespace-nowrap text-xs sm:text-sm text-gray-200">
                                            {{ $attendance->user->membership_type_name }}
                                        </td>

                                        <!-- Date -->
                                        <td class="px-4 sm:px-6 py-3 sm:py-4 whitespace-nowrap text-xs sm:text-sm text-gray-200">
                                            {{ $attendance->time_in ? $attendance->time_in->format('M d, Y') : 'N/A' }}
                                        </td>

                                        <!-- Time In -->
                                        <td class="px-4 sm:px-6 py-3 sm:py-4 whitespace-nowrap">
                                            <div class="text-xs sm:text-sm text-gray-200">{{ $attendance->time_in ? $attendance->time_in->format('h:i A') : 'N/A' }}</div>
                                        </td>

                                        <!-- Time Out -->
                                        <td class="px-4 sm:px-6 py-3 sm:py-4 whitespace-nowrap">
                                            @if($attendance->time_out)
                                                <div class="text-xs sm:text-sm text-gray-200">{{ $attendance->time_out->format('h:i A') }}</div>
                                            @else
                                                <span class="inline-flex items-center px-1.5 sm:px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                    <span class="h-1.5 w-1.5 mr-1 sm:mr-1.5 rounded-full bg-green-400 animate-pulse"></span>
                                                    <span class="text-xs">In Session</span>
                                                </span>
                                            @endif
                                        </td>

                                        <!-- Contact -->
                                        <td class="px-4 sm:px-6 py-3 sm:py-4 whitespace-nowrap text-xs sm:text-sm text-gray-200">
                                            <div class="text-xs sm:text-sm text-gray-200">{{ $attendance->user->phone_number ?? 'N/A' }}</div>
                                        </td>
                                    </tr>
                                @endforeach
                            @else
                            <tr>
                                <td colspan="7" class="px-4 sm:px-6 py-8 sm:py-12 text-center bg-[#1e1e1e]">
                                    <div class="flex flex-col items-center justify-center">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 sm:h-12 w-8 sm:w-12 text-gray-500 mb-3 sm:mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                                        </svg>
                                        <p class="text-gray-200 text-base sm:text-lg font-medium">No attendance records found</p>
                                        <p class="text-gray-400 text-xs sm:text-sm mt-1">There are no records matching your criteria.</p>
                                    </div>
                                </td>
                            </tr>
                            @endif
                            <!-- No results row for filtered results (hidden by default) -->
                            <tr id="membersNoResults" class="hidden">
                                <td colspan="7" class="px-4 sm:px-6 py-8 sm:py-12 text-center">
                                    <p class="text-gray-200 text-base sm:text-lg">No records match your filter criteria</p>
                                    <button onclick="resetFilters('members')" class="mt-2 text-[#ff5722] hover:text-white">Reset filters</button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                
                <!-- Pagination for Members Report -->
                @if($attendances->count() > 0)
                    <div class="pagination-container">
                        {{ $attendances->appends([
                            'type' => request('type', 'members'),
                            'filter' => request('filter'),
                            'start_date' => request('start_date'),
                            'end_date' => request('end_date'),
                            'per_page' => request('per_page', 10)
                        ])->links('vendor.pagination.default') }}
                    </div>
                @endif
            </div>
            

            <!-- Payments Report Table -->
            <div id="paymentsReport" class="hidden overflow-hidden rounded-lg">
                <div class="table-responsive">
                    <table class="min-w-full divide-y divide-black">
                        <thead class="bg-gradient-to-br from-[#2c2c2c] to-[#1e1e1e]">
                            <tr>
                                <th scope="col" class="px-4 sm:px-6 py-3 text-left text-xs font-medium text-gray-200 uppercase tracking-wider">#</th>
                                <th scope="col" class="px-4 sm:px-6 py-3 text-left text-xs font-medium text-gray-200 uppercase tracking-wider">Member</th>
                                <th scope="col" class="px-4 sm:px-6 py-3 text-left text-xs font-medium text-gray-200 uppercase tracking-wider">Payment Date</th>
                                <th scope="col" class="px-4 sm:px-6 py-3 text-left text-xs font-medium text-gray-200 uppercase tracking-wider">Amount</th>
                                <th scope="col" class="px-4 sm:px-6 py-3 text-left text-xs font-medium text-gray-200 uppercase tracking-wider">Method</th>
                                <th scope="col" class="px-4 sm:px-6 py-3 text-left text-xs font-medium text-gray-200 uppercase tracking-wider">Activation Date</th>
                                <th scope="col" class="px-4 sm:px-6 py-3 text-left text-xs font-medium text-gray-200 uppercase tracking-wider">Expiry Date</th>
                            </tr>
                        </thead>
                        <tbody class="bg-[#1e1e1e] divide-y divide-black" id="paymentsTableBody">
                            @if($payments->count() > 0)
                                @foreach ($payments as $index => $payment)
                                    <tr class="" data-date="{{ \Carbon\Carbon::parse($payment->payment_date)->format('Y-m-d') }}">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-200">
                                            {{ ($payments->currentPage() - 1) * $payments->perPage() + $loop->iteration }}
                                        </td>
                                        <td class="px-4 sm:px-6 py-3 sm:py-4 whitespace-nowrap">
                                            <div class="flex items-center">
                                                <div class="text-xs sm:text-sm font-medium text-gray-200">{{ $payment->user->first_name . ' ' . $payment->user->last_name }}</div>
                                            </div>
                                        </td>
                                        <td class="px-4 sm:px-6 py-3 sm:py-4 whitespace-nowrap text-xs sm:text-sm text-gray-300">{{ \Carbon\Carbon::parse($payment->payment_date)->format('m/d/Y') }}</td>
                                        <td class="px-4 sm:px-6 py-3 sm:py-4 whitespace-nowrap">
                                            <div class="text-xs sm:text-sm font-medium text-gray-300">₱{{ number_format($payment->amount, 2) }}</div>
                                        </td>
                                        <td class="px-4 sm:px-6 py-3 sm:py-4 whitespace-nowrap">
                                            <span class="px-1.5 sm:px-2.5 py-0.5 sm:py-1 inline-flex text-xs leading-5 font-semibold rounded-full 
                                                @if($payment->payment_method == 'cash') bg-green-900 text-green-200 
                                                @elseif($payment->payment_method == 'card') bg-blue-900 text-blue-200 
                                                @elseif($payment->payment_method == 'bank') bg-purple-900 text-purple-200 
                                                @else bg-gray-900 text-gray-200 @endif">
                                                {{ $payment->payment_method }}
                                            </span>
                                        </td>
                                        <td class="px-4 sm:px-6 py-3 sm:py-4 whitespace-nowrap text-xs sm:text-sm text-gray-300">{{ \Carbon\Carbon::parse($payment->user->start_date)->format('m/d/Y') }}</td>
                                        <td class="px-4 sm:px-6 py-3 sm:py-4 whitespace-nowrap text-xs sm:text-sm text-gray-300">{{ \Carbon\Carbon::parse($payment->user->end_date)->format('m/d/Y') }}</td>
                                    </tr>
                                @endforeach
                            @else
                            <tr>
                                <td colspan="7" class="px-4 sm:px-6 py-8 sm:py-12 text-center bg-[#1e1e1e]">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 sm:h-12 w-8 sm:w-12 mx-auto text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                    </svg>
                                    <h3 class="mt-2 text-base sm:text-lg font-medium text-gray-300">No payment records found</h3>
                                    <p class="mt-1 text-xs sm:text-sm text-gray-400">There are no payment records matching your criteria.</p>
                                </td>
                            </tr>
                            @endif
                            <!-- No results row for filtered results (hidden by default) -->
                            <tr id="paymentsNoResults" class="hidden">
                                <td colspan="7" class="px-4 sm:px-6 py-8 sm:py-12 text-center">
                                    <p class="text-gray-200 text-base sm:text-lg">No records match your filter criteria</p>
                                    <button onclick="resetFilters('payments')" class="mt-2 text-[#ff5722] hover:text-gray-200">Reset filters</button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <!-- Pagination for Payments Report -->
                @if($payments->count() > 0)
                    <div class="pagination-container">
                        {{ $payments->appends([
                            'type' => request('type', 'members'),
                            'filter' => request('filter'),
                            'start_date' => request('start_date'),
                            'end_date' => request('end_date'),
                            'per_page' => request('per_page', 10)
                        ])->links('vendor.pagination.default') }}
                    </div>
                @endif
            </div>
            
        </div>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
    // DOM elements
    const reportTypeSelect = document.getElementById('reportType');
    const reportTitle = document.getElementById('reportTitle');
    const membersReport = document.getElementById('membersReport');
    const paymentsReport = document.getElementById('paymentsReport');
    const dateFilter = document.getElementById('dateFilter');
    const customRange = document.getElementById('customRange');
    const startDate = document.getElementById('startDate');
    const endDate = document.getElementById('endDate');
    const exportBtn = document.getElementById('exportButton');
    
    // Get current URL parameters
    const urlParams = new URLSearchParams(window.location.search);


// Add this function inside the DOMContentLoaded event listener, before the event listeners
function updateAllTimeOption() {
    const allTimeOption = dateFilter.querySelector('option[data-all-time]');
    const isPayments = reportTypeSelect.value === 'payments';
    allTimeOption.style.display = isPayments ? 'none' : ''; // Hide for Payments, show for Members
    if (isPayments && dateFilter.value === '') {
        dateFilter.value = 'today'; // Reset to "today" if "All Time" is selected
        reloadWithFilters();
    }
}


    // Initialize form values from URL parameters
    function initializeFromUrl() {
        // Set report type
        if (urlParams.has('type')) {
            reportTypeSelect.value = urlParams.get('type');
            updateReportTypeDisplay();
        }
        
        // Set date filter (using 'filter' parameter)
        if (urlParams.has('filter')) {
            dateFilter.value = urlParams.get('filter');
            
            if (urlParams.get('filter') === 'custom') {
                customRange.classList.remove('hidden');
                if (urlParams.has('start_date')) {
                    startDate.value = urlParams.get('start_date');
                }
                if (urlParams.has('end_date')) {
                    endDate.value = urlParams.get('end_date');
                }
                // Initialize end date constraints
                updateEndDateConstraints();
            }
        }
        updateAllTimeOption();
    }

    // Update the visible report type
    function updateReportTypeDisplay() {
        const type = reportTypeSelect.value;
        reportTitle.textContent = type === 'members' ? 'Members Report' : 'Payments Report';
        
        if (type === 'members') {
            membersReport.classList.remove('hidden');
            paymentsReport.classList.add('hidden');
        } else {
            membersReport.classList.add('hidden');
            paymentsReport.classList.remove('hidden');
        }
    }
    
    // Update URL with current filters (without reloading)
    function updateUrlWithFilters() {
        const url = new URL(window.location);
        
        // Always include type
        url.searchParams.set('type', reportTypeSelect.value);
        
        // Handle date filter
        const filterValue = dateFilter.value;
        if (filterValue) {
            url.searchParams.set('filter', filterValue);
            
            // For custom range, include dates
            if (filterValue === 'custom' && startDate.value && endDate.value) {
                url.searchParams.set('start_date', startDate.value);
                url.searchParams.set('end_date', endDate.value);
            } else {
                // Remove date params if not in custom mode
                url.searchParams.delete('start_date');
                url.searchParams.delete('end_date');
            }
        } else {
            // Remove filter if not set
            url.searchParams.delete('filter');
            url.searchParams.delete('start_date');
            url.searchParams.delete('end_date');
        }
        
        // Remove page parameter when filters change
        url.searchParams.delete('page');
        
        // Update URL without reloading
        window.history.replaceState(null, '', url.toString());
    }

    // Update end date constraints based on start date
    function updateEndDateConstraints() {
        const today = new Date().toISOString().split('T')[0];
        
        // Ensure start date isn't in the future
        if (startDate.value > today) {
            startDate.value = today;
        }
        
        // Set end date constraints
        endDate.min = startDate.value;
        endDate.max = today;
        
        // If current end date is invalid, adjust it
        if (endDate.value && (new Date(endDate.value) < new Date(startDate.value) || endDate.value > today)) {
            endDate.value = startDate.value;
        }
    }

    // Reload page with current filters
    function reloadWithFilters() {
        updateUrlWithFilters();
        const url = new URL(window.location);
        window.location.href = url.toString();
    }

    // Reset all filters
    function resetFilters(tableType) {
        // Reset all filter controls
        dateFilter.value = '';
        startDate.value = '';
        endDate.value = '';
        customRange.classList.add('hidden');

        // Reset URL
        const url = new URL(window.location.pathname);
        url.searchParams.set('type', reportTypeSelect.value);
        window.location.href = url.toString();
    }

    function updatePaginationLinks() {
        const type = reportTypeSelect.value;
        const filterValue = dateFilter.value;
        const startDateValue = startDate.value;
        const endDateValue = endDate.value;
        
        // Target only links within pagination container
        document.querySelectorAll('.pagination-container a').forEach(link => {
            try {
                if (!link.href || !link.href.includes('http')) return;
                
                const url = new URL(link.href);
                
                // Preserve existing page parameter
                const pageParam = url.searchParams.get('page');
                
                // Update parameters
                url.searchParams.set('type', type);
                
                if (filterValue) {
                    url.searchParams.set('filter', filterValue);
                } else {
                    url.searchParams.delete('filter');
                }
                
                if (filterValue === 'custom' && startDateValue && endDateValue) {
                    url.searchParams.set('start_date', startDateValue);
                    url.searchParams.set('end_date', endDateValue);
                } else {
                    url.searchParams.delete('start_date');
                    url.searchParams.delete('end_date');
                }
                
                // Restore page parameter if it existed
                if (pageParam) {
                    url.searchParams.set('page', pageParam);
                }
                
                // Update href without changing the element's structure
                link.href = url.toString();
            } catch (e) {
                console.error("Error updating pagination link:", e);
            }
        });
    }

    // Initialize on page load
    initializeFromUrl();
    updatePaginationLinks();
    
    // Event listeners
    reportTypeSelect.addEventListener('change', function() {
    updateReportTypeDisplay();
    updateAllTimeOption(); // Add this line
    reloadWithFilters();
});
    
    dateFilter.addEventListener('change', function() {
        if (this.value === 'custom') {
            customRange.classList.remove('hidden');
            const today = new Date().toISOString().split('T')[0];
            
            // Set max date to today for both inputs
            startDate.max = today;
            endDate.max = today;
            
            // Set default values if empty
            if (!startDate.value) startDate.value = today;
            if (!endDate.value) endDate.value = today;
            
            // Initialize end date constraints
            updateEndDateConstraints();
        } else {
            customRange.classList.add('hidden');
        }
        reloadWithFilters();
    });

    startDate.addEventListener('change', function() {
        updateEndDateConstraints();
        reloadWithFilters();
    });

    endDate.addEventListener('change', function() {
        // Just reload filters when end date changes
        reloadWithFilters();
    });

    // Export button handler
    exportBtn.addEventListener('click', function() {
        const selectedType = reportTypeSelect.value;
        const selectedFilter = dateFilter.value;
        const startDateValue = startDate.value;
        const endDateValue = endDate.value;
        const today = new Date().toISOString().split('T')[0];

        // Validate report type
        if (!['members', 'payments'].includes(selectedType)) {
            showAlert('Please select a valid report type', 'error');
            return;
        }

        // Prevent "All Time" filter for Payments
        if (selectedType === 'payments' && selectedFilter === '') {
            showAlert('All Time filter is not available for Collection report', 'error');
            return;
        }

        // Validate custom date range
        if (selectedFilter === 'custom') {
            if (!startDateValue || !endDateValue) {
                showAlert('Please select both start and end dates', 'error');
                return;
            }

            if (new Date(startDateValue) > new Date(today)) {
                showAlert('Start date cannot be in the future', 'error');
                return;
            }

            if (new Date(endDateValue) > new Date(today)) {
                showAlert('End date cannot be in the future', 'error');
                return;
            }

            if (new Date(endDateValue) < new Date(startDateValue)) {
                showAlert('End date cannot be before start date', 'error');
                return;
            }
        }

        // Check if there's data available based on the current filters
        const isDataAvailable = checkDataAvailability(selectedType);
        if (!isDataAvailable) {
            showAlert('No data available for the selected filters. Please adjust your filters and try again.', 'warning');
            return;
        }

        // Build the URL
        let url = new URL("{{ route('generate.report') }}");
        url.searchParams.append('type', selectedType);
        
        if (selectedFilter) {
            url.searchParams.append('filter', selectedFilter);
            
            if (selectedFilter === 'custom') {
                url.searchParams.append('start_date', startDateValue);
                url.searchParams.append('end_date', endDateValue);
            }
        }

        window.location.href = url.toString();
    });

    // Function to check if there's data available for the selected report type and filters
    function checkDataAvailability(reportType) {
        if (reportType === 'members') {
            // For members report, check if there are visible rows in the members table
            const visibleRows = Array.from(document.querySelectorAll('#membersTableBody tr:not(.hidden)'))
                .filter(row => !row.id.includes('NoResults'));
            return visibleRows.length > 0;
        } else if (reportType === 'payments') {
            // For payments report, check if there are visible rows in the payments table
            const visibleRows = Array.from(document.querySelectorAll('#paymentsTableBody tr:not(.hidden)'))
                .filter(row => !row.id.includes('NoResults'));
            return visibleRows.length > 0;
        }
        return false;
    }

    // Function to show alert messages to the user
    function showAlert(message, type = 'info') {
        // Create alert container if it doesn't exist
        let alertContainer = document.getElementById('alertContainer');
        if (!alertContainer) {
            alertContainer = document.createElement('div');
            alertContainer.id = 'alertContainer';
            alertContainer.className = 'fixed bottom-4 right-4 z-50 max-w-md';
            document.body.appendChild(alertContainer);
        }

        // Create the alert element
        const alertElement = document.createElement('div');
        alertElement.className = `mb-3 p-4 rounded-lg shadow-lg flex items-center justify-between transition-all transform translate-y-0 opacity-100 ${getAlertClasses(type)}`;
        
        // Set the icon based on alert type
        const icon = getAlertIcon(type);
        
        // Create alert content
        alertElement.innerHTML = `
            <div class="flex items-center">
                <div class="flex-shrink-0 mr-3">
                    ${icon}
                </div>
                <div>
                    <p class="text-sm font-medium">${message}</p>
                </div>
            </div>
            <button class="ml-4 text-gray-400 hover:text-gray-600 focus:outline-none" onclick="this.parentElement.remove()">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        `;
        
        // Add to container
        alertContainer.appendChild(alertElement);
        
        // Auto remove after delay
        setTimeout(() => {
            alertElement.classList.replace('translate-y-0', 'translate-y-2');
            alertElement.classList.replace('opacity-100', 'opacity-0');
            setTimeout(() => alertElement.remove(), 300);
        }, 5000);
    }

    // Check for flash messages and display them using the existing alert system
    @if(session('error'))
        showAlert("{{ session('error') }}", 'error');
    @endif
    
    @if(session('warning'))
        showAlert("{{ session('warning') }}", 'warning');
    @endif
    
    @if(session('success'))
        showAlert("{{ session('success') }}", 'success');
    @endif
    
    @if(session('info'))
        showAlert("{{ session('info') }}", 'info');
    @endif

    // Helper function to get the appropriate alert classes based on type
    function getAlertClasses(type) {
        switch (type) {
            case 'error':
                return 'bg-red-900 border-l-4 border-red-500 text-red-100';
            case 'success':
                return 'bg-green-900 border-l-4 border-green-500 text-green-100';
            case 'warning':
                return 'bg-yellow-900 border-l-4 border-yellow-500 text-yellow-100';
            case 'info':
            default:
                return 'bg-blue-900 border-l-4 border-blue-500 text-blue-100';
        }
    }

    // Helper function to get the appropriate alert icon based on type
    function getAlertIcon(type) {
        switch (type) {
            case 'error':
                return `<svg class="h-5 w-5 text-red-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>`;
            case 'success':
                return `<svg class="h-5 w-5 text-green-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>`;
            case 'warning':
                return `<svg class="h-5 w-5 text-yellow-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                </svg>`;
            case 'info':
            default:
                return `<svg class="h-5 w-5 text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>`;
        }
    }

    // Add CSS for animations
    const styleEl = document.createElement('style');
    styleEl.textContent = `
        #alertContainer > div {
            transition: transform 0.3s ease, opacity 0.3s ease;
        }
    `;
    document.head.appendChild(styleEl);
    // Reset filters button handler
    document.querySelectorAll('[onclick^="resetFilters"]').forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            const tableType = this.getAttribute('onclick').match(/'(\w+)'/)[1];
            resetFilters(tableType);
        });
    });
});

function reportFilter() {
    return {
        reportType: 'members', // Default report type

        // Function to toggle between members and payments
        toggleReportType(type) {
            this.reportType = type;
        },

        // Return members or payments based on the selected report type
        get data() {
            if (this.reportType === 'members') {
                return @json($attendances); // Load members data
            } else {
                return @json($payments); // Load payments data
            }
        },

        // Format time for display
        formatTime(timeStr) {
            return new Date(timeStr).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
        },

        // Format date for display
        formatDate(dateStr) {
            if (!dateStr || dateStr === '0000-00-00') return 'N/A';

            const parsedDate = new Date(dateStr);
            if (isNaN(parsedDate.getTime())) return 'N/A';

            return parsedDate.toLocaleDateString('en-US', {
                year: 'numeric',
                month: 'long',
                day: 'numeric'
            });
        },

        // Reset the report type to default
        resetReportType() {
            this.reportType = 'members';
        }
    }
}
</script>
@endsection