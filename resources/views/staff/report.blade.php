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
                                <input type="date" id="startDate" class="mt-1 block w-full px-4 py-2 bg-[#1e1e1e] border border-[#666666] hover:border-[#ff5722] rounded-md text-gray-200 focus:outline-none focus:ring-2 focus:ring-[#ff5722] focus:border-[#ff5722] transition-colors">
                            </div>

                            <!-- End Date -->
                            <div>
                                <label for="endDate" class="block text-sm font-medium text-gray-200">End Date</label>
                                <input type="date" id="endDate" class="mt-1 block w-full px-4 py-2 bg-[#1e1e1e] border border-[#666666] hover:border-[#ff5722] rounded-md text-gray-200 focus:outline-none focus:ring-2 focus:ring-[#ff5722] focus:border-[#ff5722] transition-colors">
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
                            <button type="button" id="exportButton" class="bg-[#ff5722] text-gray-200 px-4 py-2 rounded-md shadow hover:bg-opacity-80 transition hover:scale-95 flex items-center gap-2">
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
            <div id="membersReport" class="overflow-hidden rounded-lg bg-gradient-to-br from-[#2c2c2c] to-[#1e1e1e]">
                <table class="min-w-full divide-y divide-black">
                    <thead>
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
            </div>
            <!-- Members Report Table Footer -->
            @if($attendances->count() > 0)
                <tfoot class="">
                    <tr>
                        <td colspan="7" class="px-6 py-4">
                            {{ $attendances->onEachSide(1)->links('vendor.pagination.default') }}
                        </td>
                    </tr>
                </tfoot>
            @endif

            <!-- Payments Report Table -->
            <div id="paymentsReport" class="hidden overflow-hidden rounded-lg bg-gradient-to-br from-[#2c2c2c] to-[#1e1e1e]">
                <table class="w-full">
                    <thead class="bg-gradient-to-br from-[#2c2c2c] to-[#1e1e1e]">
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
                    <!-- Payments Report Table Footer -->
                @if($payments->count() > 0)
                    <tfoot class="bg-[#1e1e1e]">
                        <tr>
                            <td colspan="7" class="px-6 py-4">
                                {{ $payments->onEachSide(1)->links('vendor.pagination.default') }}
                            </td>
                        </tr>
                    </tfoot>
                @endif
                </table>
                
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
            
            // Toggle report type
            reportTypeSelect.addEventListener('change', function() {
                const type = this.value;
                reportTitle.textContent = type === 'members' ? 'Members Report' : 'Payments Report';
                
                if (type === 'members') {
                    membersReport.classList.remove('hidden');
                    paymentsReport.classList.add('hidden');
                } else {
                    membersReport.classList.add('hidden');
                    paymentsReport.classList.remove('hidden');
                }
            });
            
            // Toggle custom date range
            dateFilter.addEventListener('change', function() {
                if (this.value === 'custom') {
                    customRange.classList.remove('hidden');
                    
                    // Set max date to today for both inputs
                    const today = new Date().toISOString().split('T')[0];
                    startDate.max = today;
                    endDate.max = today;
                    
                    // Set default values to today if empty
                    if (!startDate.value) startDate.value = today;
                    if (!endDate.value) endDate.value = today;
                } else {
                    customRange.classList.add('hidden');
                    filterTables();
                }
            });

            // Validate start date
    startDate.addEventListener('change', function() {
        const startDateValue = this.value;
        const endDateValue = endDate.value;
        const today = new Date().toISOString().split('T')[0];

        // Ensure start date is not after today
        if (startDateValue > today) {
            this.value = today;
        }

        // If end date exists, ensure it's not before start date
        if (endDateValue) {
            if (endDateValue < startDateValue) {
                endDate.value = startDateValue;
            }
        } else {
            // If no end date, set it to start date
            endDate.value = startDateValue;
        }

        // Set the minimum end date to the start date
        endDate.min = startDateValue;
        filterTables();
    });

    // Validate end date
    endDate.addEventListener('change', function() {
        const startDateValue = startDate.value;
        const endDateValue = this.value;
        const today = new Date().toISOString().split('T')[0];

        // Ensure end date is not after today
        if (endDateValue > today) {
            this.value = today;
        }

        // Ensure end date is not before start date
        if (endDateValue < startDateValue) {
            this.value = startDateValue;
        }
        
        filterTables();
    });

            endDate.addEventListener('change', function() {
                const startDateValue = startDate.value;
                if (startDateValue && this.value < startDateValue) {
                    startDate.value = this.value;
                }
                filterTables();
            });
            
            // Apply date filter when custom dates change
            startDate.addEventListener('change', filterTables);
            endDate.addEventListener('change', filterTables);
            
            // Filter tables based on selected date range
            function filterTables() {
    const filterValue = dateFilter.value;
    let start, end;
    
    // Set date range based on filter - using UTC to avoid timezone issues
    const today = new Date();
    today.setUTCHours(0, 0, 0, 0);
    
    switch(filterValue) {
        case 'today':
            start = new Date(today);
            end = new Date(today);
            end.setUTCHours(23, 59, 59, 999);
            break;
        case 'yesterday':
            start = new Date(today);
            start.setUTCDate(start.getUTCDate() - 1);
            end = new Date(today);
            end.setUTCDate(end.getUTCDate() - 1);
            end.setUTCHours(23, 59, 59, 999);
            break;
        case 'last7':
            start = new Date(today);
            start.setUTCDate(start.getUTCDate() - 6);
            end = new Date(today);
            end.setUTCHours(23, 59, 59, 999);
            break;
        case 'last30':
            start = new Date(today);
            start.setUTCDate(start.getUTCDate() - 29);
            end = new Date(today);
            end.setUTCHours(23, 59, 59, 999);
            break;
        case 'custom':
            const startDateValue = startDate.value;
            const endDateValue = endDate.value;
            if (!startDateValue || !endDateValue) return;
            
            // Parse dates in UTC to avoid timezone issues
            start = new Date(startDateValue + 'T00:00:00Z');
            end = new Date(endDateValue + 'T23:59:59.999Z');
            
            // Validate dates
            if (isNaN(start.getTime()) || isNaN(end.getTime())) {
                console.error('Invalid date range');
                return;
            }
            break;
        default: // All time
            // Show all rows and hide no results message
            document.querySelectorAll('#membersTableBody tr[data-date]').forEach(row => {
                row.style.display = '';
            });
            document.querySelectorAll('#paymentsTableBody tr[data-date]').forEach(row => {
                row.style.display = '';
            });
            document.getElementById('membersNoResults').classList.add('hidden');
            document.getElementById('paymentsNoResults').classList.add('hidden');
            return;
    }
    
    // Filter tables
    filterTable('members', start, end);
    filterTable('payments', start, end);
}

function filterTable(tableType, start, end) {
    const tableBody = document.getElementById(`${tableType}TableBody`);
    const noResults = document.getElementById(`${tableType}NoResults`);
    let hasVisible = false;
    
    document.querySelectorAll(`#${tableType}TableBody tr[data-date]`).forEach(row => {
        const rowDateStr = row.getAttribute('data-date');
        if (!rowDateStr) {
            row.style.display = 'none';
            return;
        }
        
        // Parse date in UTC context
        const rowDate = new Date(rowDateStr + 'T00:00:00Z');
        if (isNaN(rowDate.getTime())) {
            row.style.display = 'none';
            return;
        }
        
        const isVisible = (rowDate >= start && rowDate <= end);
        row.style.display = isVisible ? '' : 'none';
        if (isVisible) hasVisible = true;
    });
    
    noResults.classList.toggle('hidden', hasVisible);
}

function resetFilters(tableType) {
    // Reset all filter controls
    document.getElementById('dateFilter').value = '';
    document.getElementById('startDate').value = '';
    document.getElementById('endDate').value = '';
    document.getElementById('customRange').classList.add('hidden');
    
    // Reset both tables
    filterTables();
    
    // Specific no results reset if needed
    if (tableType) {
        document.getElementById(`${tableType}NoResults`).classList.add('hidden');
    } else {
        document.getElementById('membersNoResults').classList.add('hidden');
        document.getElementById('paymentsNoResults').classList.add('hidden');
    }
}
            
            // In the export button click handler
            document.getElementById('exportButton').addEventListener('click', function() {
                const selectedType = document.getElementById('reportType').value;
                const selectedDateFilter = document.getElementById('dateFilter').value;
                const startDateValue = document.getElementById('startDate').value;
                const endDateValue = document.getElementById('endDate').value;

                // Build the URL for the selected report type and filter
                let url = "{{ route('generate.report') }}?type=" + selectedType + "&date_filter=" + selectedDateFilter;

                // Include custom date range if available
                if (selectedDateFilter === 'custom' && startDateValue && endDateValue) {
                    url += "&start_date=" + startDateValue + "&end_date=" + endDateValue;
                    
                    // Explicitly indicate we want to include the full end date
                    url += "&include_full_end_date=true";
                }

                if (selectedType === 'members' || selectedType === 'payments') {
                    window.location.href = url;
                } else {
                    alert('Please select a report type');
                }
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