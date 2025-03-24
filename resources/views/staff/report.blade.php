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

   

    <div x-data="reportFilter()" class="max-w-6xl mx-auto p-6 bg-white rounded-xl shadow-lg space-y-6">
        <!-- Header -->
        <div class="flex flex-col md:flex-row md:justify-between md:items-center gap-6">
            <div>
                <h2 class="text-3xl font-bold text-gray-800" x-text="reportType === 'members' ? 'Members Report' : 'Payments Report'"></h2>
                <p class="text-gray-500">View and analyze your data with ease</p>
            </div>
                    
                    <!-- Enhanced Filter Section -->
                    <div>
                        
                    <div class="flex flex-col sm:flex-row gap-4 items-end">
                    <!-- Report Type Selector -->
    <!-- Export Button -->
    <div class="text-right">
        <button class="bg-blue-600 text-white px-4 py-2 rounded-md shadow hover:bg-blue-700 transition" @click="exportReport">Export Report</button>
    </div>
                            
                        <!-- Date Filter -->
                        <div class="w-full sm:w-auto">
                                <label class="block mb-1.5 text-sm font-medium text-gray-700">Time Period</label>
                                <div class="relative">
                                    <select 
                                        x-model="dateFilter" 
                                        class="appearance-none bg-white border border-gray-300 rounded-md pl-3 pr-10 py-2.5 text-gray-700 w-full focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                                    >
                                        <option value="">All Time</option>
                                        <option value="today">Today</option>
                                        <option value="yesterday">Yesterday</option>
                                        <option value="last7">Last 7 Days</option>
                                        <option value="last30">Last 30 Days</option>
                                        <option value="custom">Custom Range</option>
                                    </select>
                                    <div class="absolute inset-y-0 right-0 flex items-center pr-2 pointer-events-none">
                                        <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                        </svg>
                                    </div>
                                </div>
                            </div>
                            
                <!-- Custom Date Range Picker -->
                <template x-if="dateFilter === 'custom'">
                                <div class="flex gap-3 w-full sm:w-auto">
                                    <div class="flex-1">
                                        <label class="block mb-1.5 text-sm font-medium text-gray-700">From</label>
                                        <input 
                                            type="date" 
                                            x-model="customDateFrom" 
                                            class="w-full border border-gray-300 rounded-md px-3 py-2.5 text-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                                        >
                                    </div>
                                    <div class="flex-1">
                                        <label class="block mb-1.5 text-sm font-medium text-gray-700">To</label>
                                        <input 
                                            type="date" 
                                            x-model="customDateTo" 
                                            class="w-full border border-gray-300 rounded-md px-3 py-2.5 text-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                                        >
                                    </div>
                                </div>
                </template>

                <div class="w-full sm:w-auto">
                    <label class="block mb-1.5 text-sm font-medium text-gray-700">Report Type</label>
                    <div class="relative">
                        <select 
                            x-model="reportType" 
                            class="appearance-none bg-white border border-gray-300 rounded-md pl-3 pr-10 py-2.5 text-gray-700 w-full focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                        >
                            <option value="members">Members Report</option>
                            <option value="payments">Payments Report</option>
                        </select>
                        <div class="absolute inset-y-0 right-0 flex items-center pr-2 pointer-events-none">
                            <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                            </svg>
                        </div>
                    </div>
                </div>
                
                
    

            </div>
        </div>
    </div>    

    <!-- Members Report Table -->
    <div x-show="reportType === 'members'" class="overflow-hidden rounded-lg border border-gray-200">
        <table class="w-full">
            <thead class="bg-gray-100">
                <tr>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">#</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Member</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Membership</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Time In</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Time Out</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Contact</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                <template x-for="(member, index) in filteredMembers" :key="index">
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500" x-text="index + 1"></td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="h-10 w-10 flex-shrink-0 mr-3 bg-blue-100 rounded-full flex items-center justify-center">
                                    <span class="text-blue-800 font-medium" x-text="member.first_name.charAt(0) + member.last_name.charAt(0)"></span>
                                </div>
                                <div>
                                    <div class="text-sm font-medium text-gray-900" x-text="member.first_name + ' ' + member.last_name"></div>
                                    <div class="text-sm text-gray-500" x-text="member.email || 'No email provided'"></div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap" 
                            x-data="{ 
                                membershipTypes: { '30': 'Monthly', '365': 'Annual', '7': 'Weekly', '1': 'Session' },
                                colors: {
                                    'Annual': 'bg-purple-100 text-purple-800',
                                    'Weekly': 'bg-green-100 text-green-800',
                                    'Monthly': 'bg-blue-100 text-blue-800',
                                    'Session': 'bg-yellow-100 text-yellow-800'
                                }
                            }">
                            <span class="px-2.5 py-1 inline-flex text-xs leading-5 font-semibold rounded-full"
                                :class="colors[membershipTypes[member.membership_type]] || 'bg-gray-100 text-gray-800'"
                                x-text="membershipTypes[member.membership_type] || 'No Type'">
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span 
                                class="px-2.5 py-1 inline-flex text-xs leading-5 font-semibold rounded-full"
                                :class="member.member_status === 'active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'"
                                x-text="member.member_status">
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500" x-text="member.attendance?.time_in ? formatTime(member.attendance.time_in) : 'N/A'"></td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500" x-text="member.attendance?.time_out ? formatTime(member.attendance.time_out) : 'N/A'"></td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500" x-text="member.phone_number || 'No contact'"></td>
                    </tr>
                </template>
                <!-- Empty State -->
                <tr x-show="filteredMembers.length === 0">
                    <td colspan="7" class="px-6 py-12 text-center">
                        <p class="text-gray-500 text-lg">No members found with the current filters</p>
                        <button @click="resetFilters" class="mt-2 text-blue-600 hover:text-blue-800">Reset filters</button>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

    <!-- Payments Report Table -->
    <div x-show="reportType === 'payments'" class="overflow-hidden rounded-lg border border-gray-200">
        <table class="w-full">
            <thead class="bg-gray-100">
                <tr>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">#</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Member</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Payment Date</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Method</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Activation Date</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Expiry Date</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                <template x-for="(payment, index) in filteredPayments" :key="index">
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500" x-text="index + 1"></td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="h-10 w-10 flex-shrink-0 mr-3 bg-green-100 rounded-full flex items-center justify-center">
                                    <span class="text-green-800 font-medium" x-text="payment.user?.first_name.charAt(0) + payment.user?.last_name.charAt(0)"></span>
                                </div>
                                <div class="text-sm font-medium text-gray-900" x-text="payment.user?.first_name + ' ' + payment.user?.last_name"></div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500" x-text="formatDate(payment.payment_date)"></td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900" x-text="'₱' + parseFloat(payment.amount).toFixed(2)"></div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2.5 py-1 inline-flex text-xs leading-5 font-semibold rounded-full"
                                :class="{
                                    'bg-green-100 text-green-800': payment.payment_method === 'cash',
                                    'bg-blue-100 text-blue-800': payment.payment_method === 'card',
                                    'bg-purple-100 text-purple-800': payment.payment_method === 'bank',
                                    'bg-gray-100 text-gray-800': !['cash', 'card', 'bank'].includes(payment.payment_method)
                                }"
                                x-text="payment.payment_method">
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500" x-text="formatDate(payment.user?.start_date)"></td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500" x-text="formatDate(payment.user?.end_date)"></td>
                    </tr>
                </template>
                <!-- Empty State -->
                <tr x-show="filteredPayments.length === 0">
                    <td colspan="7" class="px-6 py-12 text-center">
                        <p class="text-gray-500 text-lg">No payments found with the current filters</p>
                        <button @click="resetFilters" class="mt-2 text-blue-600 hover:text-blue-800">Reset filters</button>
                    </td>
                </tr>
            </tbody>
        </table>

            <!-- Export Button -->
    <div class="text-right">
        <button class="bg-blue-600 text-white px-4 py-2 rounded-md shadow hover:bg-blue-700 transition" @click="exportReport">Export Report</button>
    </div>
    </div>





    <script>
function reportFilter() {
    return {
        reportType: 'members',
        dateFilter: '',
        customDateFrom: '',
        customDateTo: '',
        statusFilter: '',
        paymentMethodFilter: '',
        members: @json($members),
        payments: @json($payments),

        get filteredMembers() {
            return this.members.filter(member => {
                // Status filter
                if (this.statusFilter && member.member_status !== this.statusFilter) {
                    return false;
                }

                // Date filter
                if (!this.passesDateFilter(member.created_at)) {
                    return false;
                }

                return true;
            });
        },

        get filteredPayments() {
            return this.payments.filter(payment => {
                // Payment method filter
                if (this.paymentMethodFilter && payment.payment_method !== this.paymentMethodFilter) {
                    return false;
                }

                // Date filter
                if (!this.passesDateFilter(payment.payment_date)) {
                    return false;
                }

                return true;
            });
        },

        passesDateFilter(itemDate) {
            if (!this.dateFilter && !this.customDateFrom && !this.customDateTo) return true; // No filter applied

            const now = new Date();
            const today = new Date(now.getFullYear(), now.getMonth(), now.getDate());
            const itemDateObj = new Date(itemDate);

            switch (this.dateFilter) {
                case 'today':
                    return itemDateObj >= today && itemDateObj < new Date(today.getTime() + 86400000);
                
                case 'yesterday':
                    const yesterday = new Date(today);
                    yesterday.setDate(yesterday.getDate() - 1);
                    return itemDateObj >= yesterday && itemDateObj < today;
                
                case 'last7':
                    const last7 = new Date(today);
                    last7.setDate(last7.getDate() - 7);
                    return itemDateObj >= last7 && itemDateObj < new Date(today.getTime() + 86400000);
                
                case 'last30':
                    const last30 = new Date(today);
                    last30.setDate(last30.getDate() - 30);
                    return itemDateObj >= last30 && itemDateObj < new Date(today.getTime() + 86400000);
                
                case 'custom':
                    if (!this.customDateFrom || !this.customDateTo) return true;
                    const fromDate = new Date(this.customDateFrom);
                    const toDate = new Date(this.customDateTo);
                    toDate.setDate(toDate.getDate() + 1); // Include the end date
                    return itemDateObj >= fromDate && itemDateObj < toDate;
                
                default:
                    return true;
            }
        },

        formatTime(timeStr) {
            return new Date(timeStr).toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'});
        },

        formatDate(dateStr) {
            if (!dateStr) return 'N/A';
            return new Date(dateStr).toLocaleDateString();
        },

        applyFilters() {
            console.log('Applying filters:', {
                reportType: this.reportType,
                dateFilter: this.dateFilter,
                customDateFrom: this.customDateFrom,
                customDateTo: this.customDateTo,
                statusFilter: this.statusFilter,
                paymentMethodFilter: this.paymentMethodFilter
            });
        },

        resetFilters() {
            this.dateFilter = '';
            this.customDateFrom = '';
            this.customDateTo = '';
            this.statusFilter = '';
            this.paymentMethodFilter = '';
            this.applyFilters();
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