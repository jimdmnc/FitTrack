@extends('layouts.app')

@section('content')
<style>
    /* Ensure buttons are touch-friendly */
    .btn-touch {
        min-width: 44px;
        min-height: 44px;
        padding: 0.75rem 1.5rem;
    }
    /* Adjust pricing guide cards on smaller screens */
    @media (max-width: 640px) {
        .pricing-guide-card {
            padding: 0.75rem;
        }
        .pricing-guide-card p {
            font-size: 0.85rem;
        }
    }
    /* Responsive table styling */
    .responsive-table th,
    .responsive-table td {
        padding: 0.75rem 1rem;
    }
    @media (max-width: 640px) {
        .responsive-table thead {
            display: none; /* Hide headers on mobile */
        }
        .responsive-table tbody tr {
            display: block;
            border-bottom: 1px solid #2d2d2d;
            margin-bottom: 1rem;
            background: linear-gradient(to bottom right, #2c2c2c, #1e1e1e);
            border-radius: 0.5rem;
            padding: 0.5rem;
        }
        .responsive-table tbody td {
            display: flex;
            flex-direction: column;
            align-items: flex-start;
            padding: 0.5rem;
            font-size: 0.875rem;
            border: none;
        }
        .responsive-table tbody td:before {
            content: attr(data-label);
            font-weight: 600;
            color: #9ca3af;
            margin-bottom: 0.25rem;
            font-size: 0.85rem;
        }
        .responsive-table tbody td .flex.items-center {
            width: 100%;
            justify-content: space-between;
        }
        .responsive-table tbody td input {
            width: 100%;
        }
    }
    @media (min-width: 641px) {
        .responsive-table tbody tr {
            display: table-row;
        }
        .responsive-table tbody td {
            display: table-cell;
        }
        .responsive-table tbody td:before {
            content: none;
        }
    }
</style>

<div class="m-4 sm:m-6">
    <h1 class="text-2xl sm:text-3xl md:text-4xl font-bold bg-clip-text text-transparent bg-gradient-to-r from-red-600 to-orange-600 pb-2">Membership Pricing Management</h1>
    <p class="text-gray-400 text-sm sm:text-base">Complete the form below to update your gym membership pricing structure</p>
</div>

<div class="py-4 px-4 sm:px-6 md:px-8 max-w-6xl mx-auto">
    @if (session('success'))
        <div class="bg-green-900 border-l-4 border-green-500 text-green-200 p-4 mb-6 rounded shadow">
            {{ session('success') }}
        </div>
    @endif
    
    @if ($errors->any())
        <div class="bg-red-900 border-l-4 border-red-500 text-red-200 p-4 mb-6 rounded shadow">
            <p class="font-bold">Please correct the following errors:</p>
            <ul class="list-disc ml-5">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="bg-[#1e1e1e] rounded-lg shadow-md p-4 sm:p-6">
        <form method="POST" action="{{ route('profile.pricelist.update') }}">
            @csrf
            <div class="responsive-table w-full">
                <table class="w-full">
                    <thead class="bg-gradient-to-br from-[#2c2c2c] to-[#1e1e1e]">
                        <tr class="border-b border-gray-700">
                            <th class="text-left py-3 px-4 font-semibold text-gray-200">Membership Type</th>
                            <th class="text-left py-3 px-4 font-semibold text-gray-200">Amount (₱)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($prices as $price)
                            <tr class="border-b border-gray-800">
                                <td data-label="Membership Type" class="py-4 px-4">
                                    <div class="flex items-center">
                                        @if($price->type == 'session')
                                            <span class="mr-3 text-orange-500">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd" />
                                                </svg>
                                            </span>
                                        @elseif($price->type == 'weekly')
                                            <span class="mr-3 text-orange-500">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                                    <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd" />
                                                </svg>
                                            </span>
                                        @elseif($price->type == 'monthly')
                                            <span class="mr-3 text-orange-500">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                                    <path d="M5 4a2 2 0 012-2h6a2 2 0 012 2v14l-5-2.5L5 18V4z" />
                                                </svg>
                                            </span>
                                        @elseif($price->type == 'annual')
                                            <span class="mr-3 text-orange-500">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                                    <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd" />
                                                </svg>
                                            </span>
                                        @else
                                            <span class="mr-3 text-gray-500">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                                    <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h8a2 2 0 012 2v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4zm3 1h6v4H7V5zm8 8v2h1v1H4v-1h1v-2h1v2h1v-2h1v2h1v-2h1v2h1v-2h1v2h1v-2h1zm-3-5H8v1h4V8zm0 2H8v1h4v-1z" clip-rule="evenodd" />
                                                </svg>
                                            </span>
                                        @endif
                                        <span class="font-medium capitalize text-gray-300">{{ $price->type }}</span>
                                    </div>
                                </td>
                                <td data-label="Amount (₱)" class="py-4 px-4">
                                    <div class="flex items-center">
                                        <span class="text-gray-400 mr-2">₱</span>
                                        <input 
                                            type="number" 
                                            min="0" 
                                            step="0.01" 
                                            name="prices[{{ $price->id }}]" 
                                            value="{{ $price->amount }}" 
                                            class="bg-[#2c2c2c] border border-gray-700 rounded py-2 px-3 w-full sm:w-40 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent text-gray-200"
                                        >
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            <div class="mt-6 flex justify-end">
                <button type="submit" class="bg-orange-600 hover:bg-orange-700 hover:translate-y-[-2px] text-white py-2 px-6 rounded-md font-medium transition duration-200 flex items-center shadow-md btn-touch">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                    </svg>
                    Update Prices
                </button>
            </div>
        </form>
        
        <div class="mt-8 pt-6 border-t border-gray-700">
            <h2 class="text-lg font-semibold mb-4 text-gray-200">Pricing Guide</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="bg-[#2c2c2c] p-4 rounded-lg border border-gray-700 pricing-guide-card">
                    <div class="flex items-center mb-2">
                        <span class="text-orange-500 mr-2">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd" />
                            </svg>
                        </span>
                        <span class="font-medium text-gray-200">Session Pricing</span>
                    </div>
                    <p class="text-sm text-gray-400">For single visit or drop-in customers. This is the price for one gym session.</p>
                </div>
                
                <div class="bg-[#2c2c2c] p-4 rounded-lg border border-gray-700 pricing-guide-card">
                    <div class="flex items-center mb-2">
                        <span class="text-orange-500 mr-2">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd" />
                            </svg>
                        </span>
                        <span class="font-medium text-gray-200">Weekly Pricing</span>
                    </div>
                    <p class="text-sm text-gray-400">Short-term access for 7 consecutive days. Ideal for visitors or trial members.</p>
                </div>
                
                <div class="bg-[#2c2c2c] p-4 rounded-lg border border-gray-700 pricing-guide-card">
                    <div class="flex items-center mb-2">
                        <span class="text-orange-500 mr-2">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                <path d="M5 4a2 2 0 012-2h6a2 2 0 012 2v14l-5-2.5L5 18V4z" />
                            </svg>
                        </span>
                        <span class="font-medium text-gray-200">Monthly Pricing</span>
                    </div>
                    <p class="text-sm text-gray-400">Our most popular option. 30-day access with all standard benefits.</p>
                </div>
                
                <div class="bg-[#2c2c2c] p-4 rounded-lg border border-gray-700 pricing-guide-card">
                    <div class="flex items-center mb-2">
                        <span class="text-orange-500 mr-2">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd" />
                            </svg>
                        </span>
                        <span class="font-medium text-gray-200">Annual Pricing</span>
                    </div>
                    <p class="text-sm text-gray-400">Best value membership. 365-day access with premium benefits and significant savings.</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection