@extends('layouts.app') 

@section('content')
 <!-- Dito tayo mag  front end langs -->



<p class="text-xl text-black">Payment  Tarcking</p>
    <section class="pt-10">
        <div class=" bg-white p-6 rounded-lg shadow-lg shadow-gray-400 border border-gray-200 transform hover:scale-105 transition duration-300">
            <h2 class="font-bold text-lg sm:text-3xl text-gray-800">
                <span class="text-indigo-700 drop-shadow-lg">Payment Tracking</span>
            </h2>
        </div>
    </section>

    <section class="max-w-6xl mx-auto bg-white p-6 shadow-lg rounded-lg mt-6">
            <div class="flex justify-between items-center mb-4">
                    <h2 class="text-2xl font-semibold">Payments</h2>
                    <div class="flex gap-2">
                        <button class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">Export</button>
                        <button class="border-2 text-gray-800 px-4 py-2 rounded-lg">+ New Payment</button>
                    </div>
                </div>
                <div class="grid grid-cols-5 gap-4 mb-4">
                    <div class="bg-gray-200 p-4 rounded-lg text-center flex-1 relative">
                        <p class="text-lg font-semibold">All Payments</p>
                        <p class="text-xl font-bold">$1,380</p>
                        <span class="border border-black text-gray-700 text-xs px-2 py-1 rounded-full">234 records</span>
                    </div>
                    <div class="bg-green-100 p-4 rounded-lg text-center flex-1 relative">
                        <p class="text-lg font-semibold">Succeeded</p>
                        <p class="text-xl font-bold text-green-600">$2,380</p>
                        <span class="border border-black  textgray-700 text-xs px-2 py-1 rounded-full">234 records</span>
                    </div>
                    <div class="bg-yellow-100 p-4 rounded-lg text-center flex-1 relative">
                        <p class="text-lg font-semibold">Pending</p>
                        <p class="text-xl font-bold text-yellow-600">$380</p>
                        <span class="border border-black 0 texgray-700 text-xs px-2 py-1 rounded-full">4 records</span>
                    </div>
                    <div class="bg-red-100 p-4 rounded-lg text-center flex-1 relative">
                        <p class="text-lg font-semibold">Failed</p>
                        <p class="text-xl font-bold text-red-600">$590</p>
                        <span class="border border-black ext-wgray-700 text-xs px-2 py-1 rounded-full">4 records</span>
                    </div>
                    <div class="bg-gray-300 p-4 rounded-lg text-center flex-1 relative">
                        <p class="text-lg font-semibold">Incomplete</p>
                        <p class="text-xl font-bold text-gray-700">$590</p>
                        <span class="border border-black text-gray-700 text-xs px-2 py-1 rounded-full">4 records</span>
                    </div>
                </div>

                <table class="w-full bg-white border rounded-lg overflow-hidden mt-14">
                    <thead class="bg-gray-100">
                        <tr class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                            <th class="p-3 text-left">Code</th>
                            <th class="p-3 text-left">Status</th>
                            <th class="p-3 text-left">Description</th>
                            <th class="p-3 text-left">Time</th>
                            <th class="p-3 text-left">Date</th>
                            <th class="p-3 text-left">Customer</th>
                            <th class="p-3 text-left">Amount</th>
                            <th class="p-3 text-left">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="px-4 py-4 text-sm text-gray-500">
                        <tr class="border-b hover:bg-gray-50">
                            <td class="p-3">#2935$</td>
                            <td class="p-3"><span class="px-2 py-1 bg-gray-300 rounded text-gray-700">Incomplete</span></td>
                            <td class="p-3">Payment for invoice</td>
                            <td class="p-3">03:09 AM</td>
                            <td class="p-3">Feb 15, 2023</td>
                            <td class="p-3">Ryan Young</td>
                            <td class="p-3">$89</td>
                            <td class="p-3"><button class=" text-blue-800 px-3 py-1 rounded-lg hover:text-gray-700">Details</button></td>
                        </tr>
                        <tr class="border-b hover:bg-gray-50">
                            <td class="p-3">#2935$</td>
                            <td class="p-3"><span class="px-2 py-1 bg-green-200 text-green-700 rounded">Succeeded</span></td>
                            <td class="p-3">Payment for invoice</td>
                            <td class="p-3">02:26 AM</td>
                            <td class="p-3">Feb 14, 2023</td>
                            <td class="p-3">Matthew Martinez</td>
                            <td class="p-3">$73</td>
                            <td class="p-3"><button class=" text-blue-800 px-3 py-1 rounded-lg hover:text-gray-700">Details</button></td>
                        </tr>
                        <tr class="border-b hover:bg-gray-50">
                            <td class="p-3">#2935$</td>
                            <td class="p-3"><span class="px-2 py-1 bg-red-200 text-red-700 rounded">Failed</span></td>
                            <td class="p-3">Interest</td>
                            <td class="p-3">01:21 PM</td>
                            <td class="p-3">Feb 14, 2023</td>
                            <td class="p-3">Layla Phillips</td>
                            <td class="p-3">$91</td>
                            <td class="p-3"><button class=" text-blue-800 px-3 py-1 rounded-lg hover:text-gray-700">Details</button></td>
                        </tr>
                    </tbody>
                </table>
                <div class="bg-white px-4 py-3 border-t border-gray-200 sm:px-6">
                    <!-- Pagination Placeholder -->
                    <div class="flex justify-between items-center">
                        <div class="text-sm text-gray-700">
                            Showing <span class="font-medium">1</span> to <span class="font-medium">1</span> of <span class="font-medium">1</span> results
                        </div>
                        <div class="flex space-x-2">
                            <button class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50">
                                Previous
                            </button>
                            <button class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50">
                                Next
                            </button>
                        </div>
                    </div>
                </div>
                </section>

@endsection