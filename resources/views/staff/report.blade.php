@extends('layouts.app')

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
    </style>

    <div class="mb-20">
        <div class="py-8">
            <h1 class="text-3xl pb-1 md:text-4xl font-bold bg-clip-text text-transparent bg-gradient-to-r from-red-600 to-orange-600" id="reportTitle">Members Report</h1>
            <p class="text-gray-300">View and analyze your data with ease</p>
        </div>
        <div x-data="reportFilter()" class="max-w-8xl mb-20 space-y-6">
            <!-- Header -->
            <div class="flex flex-col md:flex-row md:justify-between md:items-center gap-6 ml-8">            
                <!-- Enhanced Filter Section -->
                <div>
                    <div class="flex flex-col sm:flex-row gap-4 items-end">
                        <!-- Export Button -->
                        
                        <!-- Date Filter -->
                        <div class="w-full sm:w-auto">
                            <label class="block mb-1.5 text-sm font-medium text-gray-200">Time Period</label>
                            <div class="relative">
                                <select 
                                    id="dateFilter"
                                    class="appearance-none bg-[#1e1e1e] border border-[#666666] hover:border-[#ff5722] rounded-md pl-3 pr-10 py-2 text-gray-200 w-full focus:outline-none focus:ring-2 focus:ring-[#ff5722] focus:border-[#ff5722] transition-colors">
                                    <option value="">All Time</option>
                                    <option value="today">Today</option>
                                    <option value="yesterday">Yesterday</option>
                                    <option value="last7">Last 7 Days</option>
                                    <option value="last30">Last 30 Days</option>
                                    <option value="custom">Custom Range</option>
                                </select>
                            </div>
                        </div>
                        
                        <!-- Custom Date Range Picker -->
                        <div id="customRange" class="hidden flex items-center space-x-4">
                            <!-- Start Date -->
                            <div>
                                <label for="startDate" class="block text-sm font-medium text-gray-200">Start Date</label>
                                <input type="date" id="startDate" 
                                    class="mt-1 block w-full px-4 py-2 bg-[#1e1e1e] border border-[#666666] hover:border-[#ff5722] rounded-md text-gray-200 focus:outline-none focus:ring-2 focus:ring-[#ff5722] focus:border-[#ff5722] transition-colors"
                                    max="<?= date('Y-m-d') ?>"
                                    onchange="updateEndDatePicker()">
                            </div>

                            <!-- End Date -->
                            <div>
                                <label for="endDate" class="block text-sm font-medium text-gray-200">End Date</label>
                                <input type="date" id="endDate" 
                                    class="mt-1 block w-full px-4 py-2 bg-[#1e1e1e] border border-[#666666] hover:border-[#ff5722] rounded-md text-gray-200 focus:outline-none focus:ring-2 focus:ring-[#ff5722] focus:border-[#ff5722] transition-colors"
                                    max="<?= date('Y-m-d') ?>">
                            </div>
                        </div>

                        <div class="w-full sm:w-auto">
                            <label class="block mb-1.5 text-sm font-medium text-gray-200">Report Type</label>
                            <div class="relative">
                                <select 
                                    id="reportType" 
                                    class="appearance-none bg-[#1e1e1e] border border-[#666666] hover:border-[#ff5722] rounded-md pl-3 pr-10 py-2 text-gray-200 w-full focus:outline-none focus:ring-2 focus:ring-[#ff5722] focus:border-[#ff5722] transition-colors">
                                    <option value="members">Members Report</option>
                                    <option value="payments">Payments Report</option>
                                </select>
                            </div>
                        </div>
                        <div class="text-right">
                            <button type="button" id="exportButton" class="bg-[#ff5722] text-gray-200 px-4 py-2 rounded-md shadow hover:bg-opacity-80 hover:translate-y-[-2px] transition flex items-center gap-2">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 9V2h12v7m-9 0H6a2 2 0 00-2 2v5h16v-5a2 2 0 00-2-2h-3m-9 0v5m0 0v4h12v-4m-3 4v1m-6-1v1" />
                                </svg>
                                Print
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Members Report Table -->
            <div id="membersReport" class="overflow-hidden rounded-lg">
                <table class="min-w-full divide-y divide-black">
                    <thead class="bg-gradient-to-br from-[#2c2c2c] to-[#1e1e1e]">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-200 uppercase tracking-wider">#</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-200 uppercase tracking-wider">Member</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-200 uppercase tracking-wider">Membership</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-200 uppercase tracking-wider">Date</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-200 uppercase tracking-wider">Time In</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-200 uppercase tracking-wider">Time Out</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-200 uppercase tracking-wider">Contact</th>
                        </tr>
                    </thead>
                    <tbody class="bg-[#1e1e1e] divide-y divide-black" id="membersTableBody">
                        @if($attendances->count() > 0)
                            @foreach($attendances as $attendance)
                                <tr class="@if($loop->even) bg-[#1e1e1e] @else bg-[#1e1e1e] @endif" data-date="{{ $attendance->time_in ? $attendance->time_in->format('Y-m-d') : '' }}">
                                    <!-- # -->
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-200">{{ $loop->iteration }}</td>

                                    <!-- Member -->
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="ml-4">
                                            <div class="text-sm font-medium text-gray-200">{{ $attendance->user->first_name }} {{ $attendance->user->last_name }}</div>
                                        </div>
                                    </td>

                                    <!-- Membership -->
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-200">
                                        {{ $attendance->user->membership_type_name }}
                                    </td>

                                    <!-- Date -->
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-200">
                                        {{ $attendance->time_in ? $attendance->time_in->format('M d, Y') : 'N/A' }}
                                    </td>

                                    <!-- Time In -->
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-200">{{ $attendance->time_in ? $attendance->time_in->format('h:i A') : 'N/A' }}</div>
                                    </td>

                                    <!-- Time Out -->
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if($attendance->time_out)
                                            <div class="text-sm text-gray-200">{{ $attendance->time_out->format('h:i A') }}</div>
                                        @else
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                <span class="h-1.5 w-1.5 mr-1.5 rounded-full bg-green-400 animate-pulse"></span>
                                                In Session
                                            </span>
                                        @endif
                                    </td>

                                    <!-- Contact -->
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-200">
                                        <div class="text-sm text-gray-200">{{ $attendance->user->phone_number ?? 'N/A' }}</div>
                                    </td>
                                </tr>
                            @endforeach
                        @else
                            <tr>
                                <td colspan="7" class="px-6 py-12 text-center bg-[#1e1e1e]">
                                    <p class="text-gray-200 text-lg">No attendance records found</p>
                                </td>
                            </tr>
                        @endif
                        <!-- No results row for filtered results (hidden by default) -->
                        <tr id="membersNoResults" class="hidden">
                            <td colspan="7" class="px-6 py-12 text-center">
                                <p class="text-gray-200 text-lg">No records match your filter criteria</p>
                                <button onclick="resetFilters('members')" class="mt-2 text-[#ff5722] hover:text-white">Reset filters</button>
                            </td>
                        </tr>
                    </tbody>
                </table>
                
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
            <div id="paymentsReport" class="hidden overflow-hidden rounded-lg bg-gradient-to-br from-[#2c2c2c] to-[#1e1e1e]">
                <table class="min-w-full divide-y divide-black">
                    <thead>
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-200 uppercase tracking-wider">#</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-200 uppercase tracking-wider">Member</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-200 uppercase tracking-wider">Payment Date</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-200 uppercase tracking-wider">Amount</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-200 uppercase tracking-wider">Method</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-200 uppercase tracking-wider">Activation Date</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-200 uppercase tracking-wider">Expiry Date</th>
                        </tr>
                    </thead>
                    <tbody class="bg-[#1e1e1e] divide-y divide-black" id="paymentsTableBody">
                        @if($payments->count() > 0)
                            @foreach ($payments as $index => $payment)
                                <tr class="" data-date="{{ \Carbon\Carbon::parse($payment->payment_date)->format('Y-m-d') }}">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-200">{{ $index + 1 }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="text-sm font-medium text-gray-200">{{ $payment->user->first_name . ' ' . $payment->user->last_name }}</div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-300">{{ \Carbon\Carbon::parse($payment->payment_date)->format('m/d/Y') }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-300">₱{{ number_format($payment->amount, 2) }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2.5 py-1 inline-flex text-xs leading-5 font-semibold rounded-full 
                                            @if($payment->payment_method == 'cash') bg-green-900 text-green-200 
                                            @elseif($payment->payment_method == 'card') bg-blue-900 text-blue-200 
                                            @elseif($payment->payment_method == 'bank') bg-purple-900 text-purple-200 
                                            @else bg-gray-900 text-gray-200 @endif">
                                            {{ $payment->payment_method }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-300">{{ \Carbon\Carbon::parse($payment->user->start_date)->format('m/d/Y') }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-300">{{ \Carbon\Carbon::parse($payment->user->end_date)->format('m/d/Y') }}</td>
                                </tr>
                            @endforeach
                        @else
                            <tr>
                                <td colspan="7" class="px-6 py-12 text-center">
                                    <p class="text-gray-200 text-lg">No payment records found</p>
                                </td>
                            </tr>
                        @endif
                        <!-- No results row for filtered results (hidden by default) -->
                        <tr id="paymentsNoResults" class="hidden">
                            <td colspan="7" class="px-6 py-12 text-center">
                                <p class="text-gray-200 text-lg">No records match your filter criteria</p>
                                <button onclick="resetFilters('payments')" class="mt-2 text-[#ff5722] hover:text-gray-200">Reset filters</button>
                            </td>
                        </tr>
                    </tbody>
                </table>
                
                <!-- Pagination for Payments Report -->
                @if($payments->count() > 0)
                    <tfoot class="bg-[#1e1e1e]">
                        <tr>
                            <td colspan="7" class="px-6 py-4">
                                {{ $payments->appends([
                                    'type' => request('type', 'members'),
                                    'filter' => request('filter'),
                                    'start_date' => request('start_date'),
                                    'end_date' => request('end_date'),
                                    'per_page' => request('per_page', 10)
                                ])->links('vendor.pagination.default') }}
                            </td>
                        </tr>
                    </tfoot>
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

    // Update pagination links with current filters
    function updatePaginationLinks() {
        const type = reportTypeSelect.value;
        const filterValue = dateFilter.value;
        const startDateValue = startDate.value;
        const endDateValue = endDate.value;
        
        // Update all pagination links
        document.querySelectorAll('.pagination a').forEach(link => {
            const url = new URL(link.href);
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
            
            link.href = url.toString();
        });
    }

    // Initialize on page load
    initializeFromUrl();
    updatePaginationLinks();
    
    // Event listeners
    reportTypeSelect.addEventListener('change', function() {
        updateReportTypeDisplay();
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
            alert('Please select a valid report type');
            return;
        }

        // Validate custom date range
        if (selectedFilter === 'custom') {
            if (!startDateValue || !endDateValue) {
                alert('Please select both start and end dates');
                return;
            }

            if (new Date(startDateValue) > new Date(today)) {
                alert('Start date cannot be in the future');
                return;
            }

            if (new Date(endDateValue) > new Date(today)) {
                alert('End date cannot be in the future');
                return;
            }

            if (new Date(endDateValue) < new Date(startDateValue)) {
                alert('End date cannot be before start date');
                return;
            }
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