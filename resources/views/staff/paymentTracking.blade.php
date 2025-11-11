@extends('layouts.app')

@section('content')
<style>
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

        /* Mobile optimizations */
        @media (max-width: 640px) {
            .mobile-full-width {
                width: 100%;
            }
            
            .pagination-container {
                overflow-x: auto;
                padding-bottom: 1rem;
            }
            
            .pagination {
                display: flex;
                white-space: nowrap;
            }
        }
</style>
<div class="bg-[#121212] p-2">
    <!-- Header Section with Gradient Card -->
    <div class="py-8">
        <div class="mb-6">
            <h1 class="text-3xl pb-1 md:text-4xl font-bold bg-clip-text text-transparent bg-gradient-to-r from-red-600 to-orange-600">
                Payment Tracking
            </h1>
        </div>

        <!-- Payment Table Card -->
        <div class="p-4">
            <!-- Table Header with Search and Filter -->
            <div class="p-5">
                <div class="flex flex-col md:flex-row justify-between items-center gap-4">
                    <div class="relative w-full md:w-80">
                        <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                            <svg class="w-4 h-4 text-[#ff5722]" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m19 19-4-4m0-7A7 7 0 1 1 1 8a7 7 0 0 1 14 0Z"/>
                            </svg>
                        </div>
                        <input type="search" class="block w-full p-2.5 pl-10 text-sm text-gray-200 placeholder-gray-400 border border-[#666666] hover:border-[#ff5722] rounded-full bg-[#212121] focus:ring-[#ff5722] focus:border-[#ff5722]" placeholder="Search">
                        <!-- Clear Search Button -->
                        <a 
                            id="clearSearch" 
                            class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-200 hover:text-[#ff5722] transition-colors hidden cursor-pointer"
                            aria-label="Clear search">
                            <svg class="h-4 w-4 text-[#ff5722]" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </a>
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
            <div class="overflow-x-auto table-responsive">
                <table class="w-full table-auto">
                    <thead class="bg-gradient-to-br from-[#2c2c2c] to-[#1e1e1e] text-xs font-medium text-gray-200 uppercase tracking-wider border border-black divide-y divide-black">
                        <tr>
                            <th class="px-6 py-4 text-left">#</th>
                            <th class="px-6 py-4 text-left">Customer</th>
                            <!-- <th class="px-6 py-4 text-left">Membership</th> -->
                            <th class="px-6 py-4 text-left">Amount</th>
                            <th class="px-6 py-4 text-left">Method</th>
                            <th class="px-6 py-4 text-left">Activation</th>
                            <!-- <th class="px-6 py-4 text-left">Expiry</th> -->
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-black">
                        @if($payments->isEmpty())
                            <tr class="bg-[#1e1e1e]">
                                <td colspan="7" class="px-6 py-12 text-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 mx-auto text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                    </svg>
                                    <h3 class="mt-2 text-lg font-medium text-gray-300">No payment records found</h3>
                                    <p class="mt-1 text-sm text-gray-400">There are no payment records matching your criteria.</p>
                                </td>
                            </tr>
                        @else
                            @foreach ($payments as $payment)
                            <tr class="bg-[#1e1e1e]">
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-200">
                                    {{ ($payments->currentPage() - 1) * $payments->perPage() + $loop->iteration }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="text-sm font-medium text-gray-200">
                                            {{ optional($payment->user)->first_name . ' ' . optional($payment->user)->last_name ?? 'Unknown User' }}
                                        </div>
                                    </div>
                                </td>
                                <!-- <td class="px-6 py-4 whitespace-nowrap">
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
                                </td> -->
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
                                <!-- <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-200">
                                    @if(optional($payment->user)->end_date)
                                        <span class="@if(\Carbon\Carbon::parse($payment->user->end_date)->isPast()) text-red-500 @endif">
                                            {{ \Carbon\Carbon::parse($payment->user->end_date)->format('M d, Y') }}
                                        </span>
                                    @else
                                        <span class="text-gray-400">N/A</span>
                                    @endif
                                </td> -->
                            </tr>
                            @endforeach
                        @endif
                    </tbody>
                </table>
            </div>
            <div class="mt-4">
                {{ $payments->appends([
                    'search' => request('search'), 
                    'payment_method' => request('payment_method'), 
                    'time_filter' => request('time_filter')
                ])->links('vendor.pagination.default') }}
            </div>
        </div>
    </div>
</div>
<script>
    $(document).ready(function () {
    // Define the input and select elements
    const paymentMethodFilter = $('#paymentMethodFilter');
    const timeFilter = $('#timeFilter');
    const searchInput = $('input[type="search"]');
    const clearSearchButton = $('#clearSearch');
    
    // Debounce function to prevent too many AJAX requests while typing
    let debounceTimer;
    function debounce(func, timeout = 500) {
        clearTimeout(debounceTimer);
        debounceTimer = setTimeout(func, timeout);
    }
    
    // Listen for input in the search field with debounce
    searchInput.on('input', function () {
        debounce(fetchPayments);
        toggleClearButtonVisibility();
    });

    // Listen for changes in the filters
    paymentMethodFilter.add(timeFilter).on('change', function () {
        fetchPayments();
    });

    // Function to fetch payments based on search and filters
    function fetchPayments(url = null) {
        const search = searchInput.val();
        const paymentMethod = paymentMethodFilter.val();
        const time = timeFilter.val();
        
        // Use provided URL or default to the route
        const requestUrl = url || '{{ route("staff.paymentTracking") }}';

        // Show loading state in table
        $('tbody').html('<tr><td colspan="7" class="text-center py-8"><div class="flex justify-center items-center"><div class="animate-spin rounded-full h-12 w-12 border-t-2 border-b-2 border-orange-500"></div></div></td></tr>');
        
        // Prepare the parameters for the request
        const params = new URLSearchParams();
        if (search) params.append('search', search);
        if (paymentMethod) params.append('payment_method', paymentMethod);
        if (time) params.append('time_filter', time);

        // Construct the full URL with parameters
        const fullUrl = requestUrl + (requestUrl.includes('?') ? '&' : '?') + params.toString();
        
        // Send an AJAX request
        $.ajax({
            url: fullUrl,
            type: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            },
            success: function (data) {
                // Replace the table body with the new content
                $('tbody').html($(data).find('tbody').html());
                
                // Replace the pagination links
                $('.mt-4').html($(data).find('.mt-4').html());
                
                // Update browser URL without reload
                window.history.pushState({}, '', fullUrl);
            },
            error: function () {
                $('tbody').html('<tr><td colspan="7" class="text-center py-8 text-red-500">Error loading payments</td></tr>');
            }
        });
    }

    // Show/hide the clear search button based on input value
    function toggleClearButtonVisibility() {
        if (searchInput.val().trim() !== '') {
            clearSearchButton.removeClass('hidden');
        } else {
            clearSearchButton.addClass('hidden');
        }
    }

    // Clear search functionality
    clearSearchButton.on('click', function() {
        searchInput.val('');
        toggleClearButtonVisibility();
        fetchPayments();
    });

    // Set initial filter values from URL
    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.has("payment_method")) {
        paymentMethodFilter.val(urlParams.get("payment_method"));
    }
    if (urlParams.has("time_filter")) {
        timeFilter.val(urlParams.get("time_filter"));
    }
    if (urlParams.has("search")) {
        searchInput.val(urlParams.get("search"));
        toggleClearButtonVisibility();
    }

    // Handle pagination links with AJAX
    $(document).on('click', '.pagination a', function(e) {
        e.preventDefault();
        let url = $(this).attr('href');
        fetchPayments(url);
    });

    // Initial visibility check for clear button
    toggleClearButtonVisibility();
});
</script>
@endsection