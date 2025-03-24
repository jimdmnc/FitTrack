@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Header Section with Gradient Card -->
    <div class="bg-white rounded-xl shadow-lg border border-gray-100 mb-8 overflow-hidden">
        <div class="bg-gradient-to-r from-blue-500 to-indigo-600 h-3"></div>
        <div class="p-6">
            <div class="flex flex-col md:flex-row justify-between items-center gap-4">
                <h1 class="text-2xl md:text-3xl font-bold text-gray-800">
                    Payment Tracking
                </h1>
        
            </div>
        </div>
    </div>

    <!-- Payment Table Card -->
    <div class="bg-white rounded-xl shadow-lg border border-gray-100 overflow-hidden">
        <!-- Table Header with Search and Filter -->
        <div class="p-5 border-b border-gray-100">
            <div class="flex flex-col md:flex-row justify-between items-center gap-4">
                <div class="relative w-full md:w-64">
                    <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                        <svg class="w-4 h-4 text-gray-500" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m19 19-4-4m0-7A7 7 0 1 1 1 8a7 7 0 0 1 14 0Z"/>
                        </svg>
                    </div>
                    <input type="search" class="block w-full p-2.5 pl-10 text-sm text-gray-900 border border-gray-200 rounded-lg bg-gray-50 focus:ring-blue-500 focus:border-blue-500" placeholder="Search payments...">
                </div>
                <div class="flex items-center gap-3">
                    <!-- Payment Method Filter -->
                    <select id="paymentMethodFilter" class="bg-gray-50 border border-gray-200 text-gray-700 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block p-2.5">
                        <option value="">All Methods</option>
                        <option value="cash">Cash</option>
                        <option value="gcash">Gcash</option>
                    </select>

                    <!-- Time Filter -->
                    <select id="timeFilter" class="bg-gray-50 border border-gray-200 text-gray-700 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block p-2.5">
                        <option value="">All Time</option>
                        <option value="today">Today</option>
                        <option value="week">This Week</option>
                        <option value="month">This Month</option>
                    </select>
                </div>
 


            </div>
        </div>

        <!-- Responsive Table -->
        <div class="overflow-x-auto">
            <table class="w-full table-auto">
                <thead class="bg-gray-50 text-xs font-medium text-gray-500 uppercase tracking-wider">
                    <tr>
                        <th class="px-6 py-4 text-left">#</th>
                        <th class="px-6 py-4 text-left">Customer</th>
                        <th class="px-6 py-4 text-left">Membership</th>
                        <th class="px-6 py-4 text-left">Amount</th>
                        <th class="px-6 py-4 text-left">Method</th>
                        <th class="px-6 py-4 text-left">Activation</th>
                        <th class="px-6 py-4 text-left">Expiry</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach ($payments as $payment)
                    <tr class="hover:bg-gray-50 transition-colors">
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-600">{{ $loop->iteration }}</td>
                    <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="h-8 w-8 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-500 font-medium mr-3">
                                    {{ substr(optional($payment->user)->first_name ?? 'U', 0, 1) }}
                                </div>
                                <div class="text-sm font-medium text-gray-700">
                                    {{ optional($payment->user)->first_name . ' ' . optional($payment->user)->last_name ?? 'Unknown User' }}
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2.5 py-1 text-xs font-medium rounded-full
                                @if(strtolower(optional($payment->user)->membership_type_name ?? '') == 'premium')
                                    bg-indigo-100 text-indigo-800
                                @elseif(strtolower(optional($payment->user)->membership_type_name ?? '') == 'basic')
                                    bg-blue-100 text-blue-800
                                @else
                                    bg-gray-100 text-gray-800
                                @endif
                            ">
                                {{ optional($payment->user)->membership_type_name ?? 'N/A' }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-700">₱{{ number_format($payment->amount, 2) }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                @if(strtolower($payment->payment_method) == 'cash')
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-green-500 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2z" />
                                    </svg>
                                @elseif(strtolower($payment->payment_method) == 'credit card')
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-blue-500 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                                    </svg>
                                @else
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-500 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                    </svg>
                                @endif
                                <span class="text-sm text-gray-600">{{ $payment->payment_method }}</span>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                            {{ \Carbon\Carbon::parse($payment->payment_date)->format('M d, Y') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                            @if(optional($payment->user)->end_date)
                                <span class="@if(\Carbon\Carbon::parse($payment->user->end_date)->isPast()) text-red-600 @endif">
                                    {{ \Carbon\Carbon::parse($payment->user->end_date)->format('M d, Y') }}
                                </span>
                            @else
                                <span class="text-gray-400">N/A</span>
                            @endif
                        </td>
            
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>


    </div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function () {
        const paymentMethodFilter = document.getElementById("paymentMethodFilter");
        const timeFilter = document.getElementById("timeFilter");

        function applyFilters() {
            let params = new URLSearchParams(window.location.search);
            if (paymentMethodFilter.value) {
                params.set("payment_method", paymentMethodFilter.value);
            } else {
                params.delete("payment_method");
            }
            if (timeFilter.value) {
                params.set("time_filter", timeFilter.value);
            } else {
                params.delete("time_filter");
            }
            window.location.search = params.toString(); // Reload page with new filters
        }

        paymentMethodFilter.addEventListener("change", applyFilters);
        timeFilter.addEventListener("change", applyFilters);

        // Set the selected values when the page loads
        const urlParams = new URLSearchParams(window.location.search);
        if (urlParams.has("payment_method")) {
            paymentMethodFilter.value = urlParams.get("payment_method");
        }
        if (urlParams.has("time_filter")) {
            timeFilter.value = urlParams.get("time_filter");
        }
    });
</script>

@endsection