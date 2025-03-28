@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 bg-[#121212] h-screen">
    <!-- Header Section with Gradient Card -->
    <div class="py-8">
        <div class="mb-6">
            <h1 class="text-3xl pb-1 md:text-4xl font-bold bg-clip-text text-transparent bg-gradient-to-r from-red-600 to-orange-600">
                        Payment Tracking
                        </h1>
        </div>


        <!-- Payment Table Card -->
        <div class=" ">
            <!-- Table Header with Search and Filter -->
            <div class="p-5">
                <div class="flex flex-col md:flex-row justify-between items-center gap-4">
                    <div class="relative w-full md:w-64">
                        <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                            <svg class="w-4 h-4 text-[#ff5722]" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m19 19-4-4m0-7A7 7 0 1 1 1 8a7 7 0 0 1 14 0Z"/>
                            </svg>
                        </div>
                        <input type="search" class="block w-full p-2.5 pl-10 text-sm text-gray-200 placeholder-gray-400 border border-[#666666] hover:border-[#ff5722] rounded-lg bg-[#212121] focus:ring-[#ff5722] focus:border-[#ff5722]" placeholder="Search payments...">
                    </div>
                    <div class="flex items-center gap-3">
                        <!-- Payment Method Filter -->
                        <select id="paymentMethodFilter" class="pr-8 bg-[#212121] border border-[#666666] hover:border-[#ff5722] text-gray-200 text-sm rounded-lg focus:ring-[#ff5722] focus:border-[#ff5722] block p-2.5">
                            <option value="">All Methods</option>
                            <option value="cash">Cash</option>
                            <option value="gcash">Gcash</option>
                        </select>

                        <!-- Time Filter -->
                        <select name="time_filter" id="timeFilter" class="pr-8 bg-[#212121] border border-[#666666] hover:border-[#ff5722] text-gray-200 text-sm rounded-lg focus:ring-[#ff5722] focus:border-[#ff5722] block p-2.5">
                            <option value="">All Time</option>
                            <option value="today" {{ request('time_filter') == 'today' ? 'selected' : '' }}>Today</option>
                            <option value="week" {{ request('time_filter') == 'week' ? 'selected' : '' }}>This Week</option>
                            <option value="month" {{ request('time_filter') == 'month' ? 'selected' : '' }}>This Month</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Responsive Table -->
            <div class="overflow-x-auto">
                <table class="w-full table-auto">
                    <thead class="bg-gradient-to-br from-[#2c2c2c] to-[#1e1e1e] text-xs font-medium text-gray-200 uppercase tracking-wider border border-black divide-y divide-black">
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
                    <tbody class="divide-y divide-black">
                        @foreach ($payments as $payment)
                        <tr class="bg-[#1e1e1e]">
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-200">{{ $loop->iteration }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="text-sm font-medium text-gray-200">
                                        {{ optional($payment->user)->first_name . ' ' . optional($payment->user)->last_name ?? 'Unknown User' }}
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2.5 py-1 text-xs font-medium rounded-full
                                    @if(strtolower(optional($payment->user)->membership_type_name ?? '') == 'annual')
                                        bg-purple-900 text-purple-200
                                    @elseif(strtolower(optional($payment->user)->membership_type_name ?? '') == 'week')
                                        bg-green-900 text-green-200
                                    @elseif(strtolower(optional($payment->user)->membership_type_name ?? '') == 'month')
                                        bg-blue-900 text-blue-200
                                    @elseif(strtolower(optional($payment->user)->membership_type_name ?? '') == 'session')
                                        bg-yellow-900 text-yellow-200
                                    @endif
                                ">
                                    {{ optional($payment->user)->membership_type_name ?? 'N/A' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-200">₱{{ number_format($payment->amount, 2) }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    @if(strtolower($payment->payment_method) == 'cash')
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-green-500 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2z" />
                                        </svg>
                                    @elseif(strtolower($payment->payment_method) == 'credit card')
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-yellow-500 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                                        </svg>
                                    @else
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-blue-500 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                        </svg>
                                    @endif
                                    <span class="text-sm text-gray-200">{{ $payment->payment_method }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-200">
                                {{ \Carbon\Carbon::parse($payment->payment_date)->format('M d, Y') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-200">
                                @if(optional($payment->user)->end_date)
                                    <span class="@if(\Carbon\Carbon::parse($payment->user->end_date)->isPast()) text-red-500 @endif">
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