

        @extends('layouts.app') <!-- Assuming you have a main layout file -->

@section('content')

            <section class="grid grid-cols-1 gap-4 pt-10">
                <div class="md:col-span-2 bg-white p-6  rounded-lg shadow-lg shadow-gray-400 border border-gray-200 transform hover:scale-105 transition duration-300">
                    <h2 class="font-bold text-lg sm:text-3xl text-gray-800">
                        <span class="text-indigo-700 drop-shadow-lg">Gym Members</span>
                    </h2>
                </div>
            </section>

            <section class="mt-6 border border-white rounded-lg p-4 bg-white text-gray-700">
                <div class="flex justify-between items-center">
                <div>
            <label>Member Status</label>
            <select  class="pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md">
                <option selected>Active Members</option>
                <option>Inactive Members</option>
            </select>
        </div>
            <div class="flex space-x-4">
              <div class="relative max-w-xs">
                    <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                        <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <input type="text" name="search" id="search" placeholder="Search members..." class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md leading-5 bg-white placeholder-gray-500 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                </div>
            </div>
                </div>
                
                <!-- Members Table -->
                        <!-- Members Table with Modern Design -->
        <div class="glass-card mt-5 ">
            
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead>
                        <tr class="bg-gray-100">
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Member</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Member ID</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Membership</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date Enrolled</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date Expiration</th>
                            <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="h-10 w-10 flex-shrink-0 mr-3">
                                        <div class="h-10 w-10 rounded-full bg-blue-100 flex items-center justify-center">
                                            <span class="text-blue-600 font-semibold">JD</span>
                                        </div>
                                    </div>
                                    <div>
                                        <div class="text-sm font-medium text-gray-900">John Doe</div>
                                        <div class="text-sm text-gray-500">john.doe@example.com</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-4 text-sm text-gray-500">RFGMEMID125</td>
                            <td class="px-4 py-4 whitespace-nowrap">
                                <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-purple-100 text-purple-800">Annual</span>
                            </td>
                            <td class="px-4 py-4 text-sm text-gray-500">Mar 7, 2025</td>
                            <td class="px-4 py-4 text-sm text-gray-500">Mar 7, 2026</td>
                            <td class="px-4 py-4 text-right text-sm">
                                <button class="text-indigo-600 hover:text-indigo-900 font-medium">View Details</button>
                            </td>
                        </tr>
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="h-10 w-10 flex-shrink-0 mr-3">
                                        <div class="h-10 w-10 rounded-full bg-green-100 flex items-center justify-center">
                                            <span class="text-green-600 font-semibold">AS</span>
                                        </div>
                                    </div>
                                    <div>
                                        <div class="text-sm font-medium text-gray-900">Alice Smith</div>
                                        <div class="text-sm text-gray-500">alice.smith@example.com</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-4 text-sm text-gray-500">RFGMEMID129</td>

                            <td class="px-4 py-4 whitespace-nowrap">
                                <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-amber-100 text-amber-800">Weekly</span>
                            </td>
                            <td class="px-4 py-4 text-sm text-gray-500">Mar 9, 2025</td>
                            <td class="px-4 py-4 text-sm text-gray-500">Mar 16, 2025</td>
                            <td class="px-4 py-4 text-right text-sm">
                                <button class="text-indigo-600 hover:text-indigo-900 font-medium">View Details</button>
                            </td>
                        </tr>
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="h-10 w-10 flex-shrink-0 mr-3">
                                        <div class="h-10 w-10 rounded-full bg-red-100 flex items-center justify-center">
                                            <span class="text-red-600 font-semibold">RJ</span>
                                        </div>
                                    </div>
                                    <div>
                                        <div class="text-sm font-medium text-gray-900">Robert Johnson</div>
                                        <div class="text-sm text-gray-500">robert.j@example.com</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-4 text-sm text-gray-500">RFGMEMID145</td>

                            <td class="px-4 py-4 whitespace-nowrap">
                                <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">Monthly</span>
                            </td>
                            <td class="px-4 py-4 text-sm text-gray-500">Mar 11, 2025</td>
                            <td class="px-4 py-4 text-sm text-gray-500">Apr 11, 2025</td>
                                <td class="px-4 py-4 text-right text-sm">
                                <button class="text-indigo-600 hover:text-indigo-900 font-medium">View Details</button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
                
                <!-- Pagination -->
            <div class="flex justify-between items-center mt-4">
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
            </section>
         @endsection