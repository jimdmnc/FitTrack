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
            background-color: #1e1e1e;
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

   
<div class="mb-20">
    <div class="py-8">
        <h1 class="text-3xl pb-1 md:text-4xl font-bold bg-clip-text text-transparent bg-gradient-to-r from-red-600 to-orange-600" id="reportTitle">Members Report</h2>
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
                            <input type="date" id="startDate" class="mt-1 block w-full px-4 py-2 bg-[#1e1e1e] border border-[#666666] hover:border-[#ff5722] rounded-md text-gray-200">
                        </div>

                        <!-- End Date -->
                        <div>
                            <label for="endDate" class="block text-sm font-medium text-gray-200">End Date</label>
                            <input type="date" id="endDate" class="mt-1 block w-full px-4 py-2 bg-[#1e1e1e] border border-[#666666] hover:border-[#ff5722] rounded-md text-gray-200">
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
                    <div class="bg-[#1e1e1e]">
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
                    </div>
                </tbody>
            </table>
        </div>

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

                    @forelse ($payments as $index => $payment)
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
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center">
                                <p class="text-gray-200 text-lg">No payments found with the current filters</p>
                                <button wire:click="resetFilters" class="mt-2 text-[#ff5722] hover:text-blue-800">Reset filters</button>
                            </td>
                        </tr>
                    @endforelse
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
                            <p class="text-gray-500 text-lg">No records match your filter criteria</p>
                            <button onclick="resetFilters('payments')" class="mt-2 text-[#ff5722] hover:text-gray-200">Reset filters</button>
                        </td>
                    </tr>
                </tbody>
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
        const exportBtn = document.getElementById('exportReport');
        
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
            } else {
                customRange.classList.add('hidden');
                filterTables();
            }
        });
        
        // Apply date filter when custom dates change
        startDate.addEventListener('change', filterTables);
        endDate.addEventListener('change', filterTables);
        
        // Filter tables based on selected date range
        function filterTables() {
        const filterValue = document.getElementById('dateFilter').value;
        let start, end;
        
        // Set date range based on filter
        const today = new Date();
        today.setHours(0, 0, 0, 0);
        
        switch(filterValue) {
            case 'today':
                start = new Date(today);
                end = new Date(today);
                end.setHours(23, 59, 59, 999);
                break;
            case 'yesterday':
                start = new Date(today);
                start.setDate(start.getDate() - 1);
                end = new Date(today);
                end.setDate(end.getDate() - 1);
                end.setHours(23, 59, 59, 999);
                break;
            case 'last7':
                start = new Date(today);
                start.setDate(start.getDate() - 6);
                end = new Date(today);
                end.setHours(23, 59, 59, 999);
                break;
            case 'last30':
                start = new Date(today);
                start.setDate(start.getDate() - 29);
                end = new Date(today);
                end.setHours(23, 59, 59, 999);
                break;
            case 'custom':
                const startDate = document.getElementById('startDate').value;
                const endDate = document.getElementById('endDate').value;
                if (!startDate || !endDate) return;
                start = new Date(startDate);
                end = new Date(endDate);
                end.setHours(23, 59, 59, 999);
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
        
        // Filter members table
        let membersHasVisible = false;
        document.querySelectorAll('#membersTableBody tr[data-date]').forEach(row => {
            const rowDateStr = row.getAttribute('data-date');
            if (!rowDateStr) {
                row.style.display = 'none';
                return;
            }
            
            const rowDate = new Date(rowDateStr);
            const isVisible = (rowDate >= start && rowDate <= end);
            row.style.display = isVisible ? '' : 'none';
            if (isVisible) membersHasVisible = true;
        });
        
        // Show/hide no results message for members
        document.getElementById('membersNoResults').classList.toggle('hidden', membersHasVisible);
        
        // Filter payments table
        let paymentsHasVisible = false;
        document.querySelectorAll('#paymentsTableBody tr[data-date]').forEach(row => {
            const rowDateStr = row.getAttribute('data-date');
            if (!rowDateStr) {
                row.style.display = 'none';
                return;
            }
            
            const rowDate = new Date(rowDateStr);
            const isVisible = (rowDate >= start && rowDate <= end);
            row.style.display = isVisible ? '' : 'none';
            if (isVisible) paymentsHasVisible = true;
        });
        
        // Show/hide no results message for payments
        document.getElementById('paymentsNoResults').classList.toggle('hidden', paymentsHasVisible);
    }
    function resetFilters(tableType) {
        document.getElementById('dateFilter').value = '';
        if (tableType === 'members') {
            document.getElementById('membersNoResults').classList.add('hidden');
        } else {
            document.getElementById('paymentsNoResults').classList.add('hidden');
        }
        filterTables();
    }
    
        // Export functionality
        exportBtn.addEventListener('click', function() {
            alert('Export functionality would be implemented here');
            // You would typically gather the filtered data and send it to the server
            // to generate a CSV/Excel file, or use a client-side library to do it
        });
    });
</script>

<!-- export button logic -->
<script>
    document.getElementById('exportButton').addEventListener('click', function () {
        var selectedType = document.querySelector('input[name="reportType"]:checked')?.value || document.getElementById('reportType').value;
        var selectedDateFilter = document.getElementById('dateFilter').value;
        var startDate = document.getElementById('startDate').value; // Get the start date if available
        var endDate = document.getElementById('endDate').value; // Get the end date if available

        // Build the URL for the selected report type and filter
        var url = "{{ route('generate.report') }}?type=" + selectedType + "&date_filter=" + selectedDateFilter;

        // Include custom date range if available
        if (selectedDateFilter === 'custom' && startDate && endDate) {
            url += "&start_date=" + startDate + "&end_date=" + endDate;
        }

        if (selectedType === 'members' || selectedType === 'payments') {
            window.location.href = url; // Redirect with the constructed URL
        } else {
            alert('Please select a report type');
        }
    });
</script>





<script>
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










    <!-- <script>
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
    </script> -->
@endsection