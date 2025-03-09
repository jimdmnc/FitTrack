<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Members</title>
    
    <!-- Correct Tailwind CDN -->
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
        <!-- Sidebar -->
        <aside id="sidebar" class="w-25 lg:w-72 bg-gradient-to-b from-black via-purple-900 to-gray-900 text-white p-5 flex flex-col transition-all duration-300 overflow-hidden flex-shrink-0">
            <!-- <div class="flex items-center space-x-3 mb-6">
                <div class="">
                    <div class="sidebar-text hidden lg:block">
                        <h2>Rockies Fitness</h2>
                    </div>
                </div>
            </div> -->

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
                    <!-- <li class="flex items-center space-x-2 p-2 hover:bg-slate-400 hover:rounded-md hover:bg-opacity-15">
                        <span>🏋️</span>
                        <span class="sidebar-text hidden lg:block">Coaches</span>
                    </li> -->
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
        </aside>
        
        <!-- Main Content -->
        <main class="flex-1 p-4 md:p-8 bg-gray-200 transition-all duration-300 h-screen overflow-auto">
            <header class="flex justify-between items-center w-full p-4">
                <!-- Left Section: Logo and Gym Name -->
                <div class="flex items-center space-x-2">
                    <img src="images/rockiesLogo.jpg" alt="Logo" class="w-16 h-16 rounded-full">
                    <h1 id="gymName" class="text-xl md:text-2xl font-bold">ROCKIES FITNESS</h1>
                </div>

                <!-- Right Section: Profile/Notifications -->
                <div class="flex items-center space-x-4">
                    <div class="w-6 h-6 bg-gray-300 rounded-full"></div>
                </div>
            </header>
            <section class="grid grid-cols-1  gap-4 mt-6">
                <div class="md:col-span-2 bg-white p-6 rounded-lg shadow">
                    <h2 class="font-bold text-lg sm:text-3xl"><span class="text-indigo-700">Gym Members</span></h2>
                </div>
            </section>
        
            <section class="mt-6 border border-white rounded-lg p-4 bg-white text-gray-700">
                <div class="flex justify-between items-center">
                <div>
            <label>Member Status</label>
            <select class="ml-2 p-2 rounded bg-white text-gray-700 border-2 border-gray-400">
                <option selected>Active Members</option>
                <option>Inactive Members</option>
            </select>
        </div>
        <div class="flex space-x-4">
            <div>
                <label>Show Entities</label>
                <select class="ml-2 p-2 rounded bg-white text-gray-700 border-2 border-gray-400">
                    <option>10</option>
                    <option>25</option>
                    <option>50</option>
                </select>
            </div>
            <div>
                <input type="text" class="ml-2 p-2 rounded bg-white text-gray-700 border-2 border-gray-400" placeholder="Search">
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
                    <td class="p-2 border border-gray-500"><button class="p-1 bg-purple-500 rounded">Edit</button></td>
                </tr>
                <tr>
                    <td class="p-2 border border-gray-500">Carlos Roi Barretto</td>
                    <td class="p-2 border border-gray-500">SFM2301N2</td>
                    <td class="p-2 border border-gray-500">Jan 11</td>
                    <td class="p-2 border border-gray-500">Feb 11</td>
                    <td class="p-2 border border-gray-500"><button class="p-1 bg-purple-500 rounded text-white">Edit</button></td>
                </tr>
                <tr class="bg-gray-800 text-white">
                    <td class="p-2 border border-gray-500">Nomer Aguado</td>
                    <td class="p-2 border border-gray-500">SFM2301N3</td>
                    <td class="p-2 border border-gray-500">Jan 11</td>
                    <td class="p-2 border border-gray-500">Feb 11</td>
                    <td class="p-2 border border-gray-500"><button class="p-1 bg-purple-500 rounded">Edit</button></td>
                </tr>
                <tr>
                    <td class="p-2 border border-gray-500">Mc Joshua Delima</td>
                    <td class="p-2 border border-gray-500">SFM2301N4</td>
                    <td class="p-2 border border-gray-500">Jan 11</td>
                    <td class="p-2 border border-gray-500">Feb 11</td>
                    <td class="p-2 border border-gray-500"><button class="p-1 bg-purple-500 rounded text-white">Edit</button></td>
                </tr>
            </tbody>
                    </table>
                </div>
                
                <!-- Pagination -->
                <div class="flex justify-end mt-4 space-x-3">
                    <button class="p-2 rounded bg-gray-700 text-white">Previous</button>
                    <button class="p-2 rounded bg-gray-700 text-white">Next</button>
                </div>
            </section>
         </main>
    </div>
</body>
</html>