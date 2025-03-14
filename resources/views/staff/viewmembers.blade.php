

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
                <div class="mt-4 overflow-x-auto">
                    <table class="w-full border-collapse bg-white text-gray-600">
                        <thead>
                            <tr class="bg-white">
                                <th class="p-2 border border-gray-500">Name</th>
                                <th class="p-2 border border-gray-500">Member ID</th>
                                <th class="p-2 border border-gray-500">Date Enrolled</th>
                                <th class="p-2 border border-gray-500">Date Expiration</th>
                                <th class="p-2 border border-gray-500">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="text-center">
                <tr class="bg-gray-800 text-white">
                    <td class="p-2 border border-gray-500">Leniño Nequinto</td>
                    <td class="p-2 border border-gray-500">SFM2301N1</td>
                    <td class="p-2 border border-gray-500">Jan 11</td>
                    <td class="p-2 border border-gray-500">Feb 11</td>
                    <td class="p-2 border border-gray-500"><button class="p-2 px-4 border-rounded border-bg-white rounded-md border ">Edit</button></td>
                </tr>
                <tr>
                    <td class="p-2 border border-gray-500">Carlos Roi Barretto</td>
                    <td class="p-2 border border-gray-500">SFM2301N2</td>
                    <td class="p-2 border border-gray-500">Jan 11</td>
                    <td class="p-2 border border-gray-500">Feb 11</td>
                    <td class="p-2 border border-gray-500"><button class="p-2 px-4 border-rounded border-bg-gray-900 rounded-md border-2">Edit</button></td>
                </tr>
                <tr class="bg-gray-800 text-white">
                    <td class="p-2 border border-gray-500">Nomer Aguado</td>
                    <td class="p-2 border border-gray-500">SFM2301N3</td>
                    <td class="p-2 border border-gray-500">Jan 11</td>
                    <td class="p-2 border border-gray-500">Feb 11</td>
                    <td class="p-2 border border-gray-500"><button class="p-2 px-4 border-rounded border-bg-white rounded-md border">Edit</button></td>
                </tr>
                <tr>
                    <td class="p-2 border border-gray-500">Mc Joshua Delima</td>
                    <td class="p-2 border border-gray-500">SFM2301N4</td>
                    <td class="p-2 border border-gray-500">Jan 11</td>
                    <td class="p-2 border border-gray-500">Feb 11</td>
                    <td class="p-2 border border-gray-500"><button class="p-2 px-4 border-rounded border-bg-slate-900 rounded-md border-2">Edit</button></td>
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