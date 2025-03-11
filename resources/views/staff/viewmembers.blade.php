<!-- <!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Members</title>
    
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        function toggleSidebar() {
            const sidebar = document.getElementById("sidebar");
            const textElements = document.querySelectorAll(".sidebar-text");
            const main = document.getElementById("main");

            sidebar.classList.toggle("w-72");
            sidebar.classList.toggle("w-25");
            textElements.forEach(el => el.classList.toggle("hidden"));
        }
    </script>
</head>
<body class="h-screen w-full">
<div class="flex h-screen">
        <aside id="sidebar" class="w-25 lg:w-72 bg-gradient-to-b from-black via-purple-900 to-gray-900 text-white p-5 flex flex-col transition-all duration-300 overflow-hidden flex-shrink-0">

            <div class="flex items-center justify-center space-x-3 mb-6 mt-6">
                <div class=" justify-center ">
                    <div class="w-12 h-12 sm:w-24 sm:h-24 bg-yellow-500 rounded-full ">
                        <img src="images/jim.jpg" alt="staffdp" id="staffDp" class="w-12 h-12 sm:w-24 sm:h-24 rounded-full">
                    </div>
                </div>
            </div>

            <div class="flex items-center justify-center text-center space-x-3 mb-6">
                <div class="sidebar-text hidden lg:block">
                    <h2 id="staffName" class="text-lg font-bold">Jim Dominic Palabate</h2>
                        <p id="staffEmail" class="text-sm">jdmpalabate@gmail.com</p>
                </div>
            </div>
            <nav class="flex-1">
                <ul class="space-y-3">
                    <a href="../staff/dashboard.blade.php" class="">
                        <li class="flex items-center space-x-2 hover:bg-slate-400 hover:rounded-md hover:bg-opacity-15 p-2" >
                            <span>🏠</span>
                            <span class="sidebar-text hidden lg:block">Dashboard</span>
                        </li>
                    </a>
                    <div class="px-0.5"></div>
                    <a href="">
                        <li class="flex items-center space-x-2 p-2 hover:bg-slate-400 hover:rounded-md hover:bg-opacity-15">
                            <span>👤</span>
                            <span class="sidebar-text hidden lg:block">Admin Profile</span>
                        </li>
                    </a>
                    <div class="px-0.5"></div>
                    <a href="">
                        <li class="flex items-center space-x-2 p-2 hover:bg-slate-400 hover:rounded-md hover:bg-opacity-15">
                            <span>📝</span>
                            <span class="sidebar-text hidden lg:block">Registration</span>
                        </li>
                    </a>
                    <div class="px-0.5"></div>
                    <a href="">
                        <li class="flex items-center space-x-2 p-2 hover:bg-slate-400 hover:rounded-md hover:bg-opacity-15">
                            <span>📜</span>
                            <span class="sidebar-text hidden lg:block">Plan</span>
                        </li>
                    </a>
                    <div class="px-0.5"></div>
                    <a href="">
                        <li class="flex items-center space-x-2 p-2 hover:bg-slate-400 hover:rounded-md hover:bg-opacity-15">
                            <span>💳</span>
                            <span class="sidebar-text hidden lg:block">Payment</span>
                        </li>
                    </a>
                    <div class="px-0.5"></div>
                    <a href="">
                        <li class="flex items-center space-x-2 p-2 hover:bg-slate-400 hover:rounded-md hover:bg-opacity-15">
                            <span>📦</span>
                            <span class="sidebar-text hidden lg:block">Inventory</span>
                        </li>
                    </a>
                    <div class="px-0.5"></div>
                    <a href="">
                        <li class="flex items-center space-x-2 p-2  bg-indigo-700 rounded-md">
                            <span>👥</span>
                            <span class="sidebar-text hidden lg:block">View Members</span>
                        </li>
                    </a>
                    <div class="px-0.5"></div>
                    <a href="">
                        <li class="flex items-center space-x-2 p-2 hover:bg-slate-400 hover:rounded-md hover:bg-opacity-15">
                            <span>📊</span>
                            <span class="sidebar-text hidden lg:block">Report</span>
                        </li>
                    </a>
                </ul>
            </nav>
            <button class="mt-auto p-2 bg-transparent border rounded-md flex items-center space-x-2 hover:text-yellow-500 hover:bg-slate-400 hover:rounded-md hover:bg-opacity-15">
                <span>🚪</span>
                <span id="staffLogout" class="sidebar-text hidden lg:block">Logout</span>
            </button>
        </aside> -->

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
            <!-- <div>
                <label>Show Entities</label>
                <select  class="pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md">
                    <option>10</option>
                    <option>25</option>
                    <option>50</option>
                </select>
            </div> -->
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