<div class="flex flex-col w-full h-full overflow-y-auto bg-gradient-to-b from-[#121212] to-[#1d1d1d] shadow-lg">
    <!-- Logo and Brand Header -->
    <div class="flex items-center p-4 border-b border-gray-800">
        <a href="{{ route('staff.dashboard') }}" class="flex items-center">
            <div class="h-12 w-12 rounded-full overflow-hidden bg-gradient-to-br from-orange-500 to-red-600 p-0.5">
                <img src="{{ asset('images/rockiesLogo.jpg') }}" alt="Rockies Logo" class="h-full w-full rounded-full object-cover">
            </div>
            <div class="ml-3">
                <span class="text-lg font-extrabold text-white" style="line-height: 1; font-family: 'Rajdhani', sans-serif;">
                    Rockies 
                    <span class="bg-gradient-to-r from-red-600 to-orange-600 bg-clip-text text-transparent">Fitness</span>
                </span>
            </div>
        </a>
    </div>  

    <!-- Navigation Links -->
    <div class="flex flex-col flex-grow p-3 space-y-6 mt-2" :class="{'block': open, 'hidden': !open}">
        <!-- Main Navigation -->
        <div x-data="{ mainOpen: true }" class="border-b border-gray-800 pb-4">
            <button @click="mainOpen = !mainOpen" class="flex items-center justify-between w-full px-3 py-2 text-gray-300 bg-gray-800 bg-opacity-30 rounded-md mb-3">
                <span class="text-gray-300 uppercase text-xs font-bold tracking-wider">Main Menu</span>
                <svg :class="{'rotate-180': mainOpen, 'rotate-0': !mainOpen}" class="w-4 h-4 transform transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                </svg>
            </button>

            <div x-show="mainOpen" x-transition class="space-y-1">
                <!-- Dashboard Link -->
                <a href="{{ route('staff.dashboard') }}" class="flex items-center px-3 py-2 text-sm font-medium rounded-md group {{ Request::routeIs('staff.dashboard') ? 'bg-orange-700 text-white shadow-md shadow-orange-900/30' : 'text-gray-400 hover:bg-orange-700 hover:text-white' }} transition-all duration-200 ease-in-out">
                    <svg class="w-5 h-5 mr-3 {{ Request::routeIs('staff.dashboard') ? 'text-orange-300' : 'text-gray-400 group-hover:text-white' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                    </svg>  
                    <span>Dashboard</span>
                </a>

                <!-- Register Member Link -->
                <a href="{{ route('staff.membershipRegistration') }}" class="flex items-center px-3 py-2 text-sm font-medium rounded-md group {{ Request::routeIs('staff.membershipRegistration') ? 'bg-orange-700 text-white shadow-md shadow-orange-900/30' : 'text-gray-400 hover:bg-orange-700 hover:text-white' }} transition-all duration-200 ease-in-out">
                    <svg class="w-5 h-5 mr-3 {{ Request::routeIs('staff.membershipRegistration') ? 'text-orange-300' : 'text-gray-400 group-hover:text-white' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                    </svg>
                    <span>Register Member</span>
                </a>
            </div>
        </div>

        <!-- Member Management Section -->
        <div x-data="{ memberOpen: true }" class="border-b border-gray-800 pb-4">
            <button @click="memberOpen = !memberOpen" class="flex items-center justify-between w-full px-3 py-2 text-gray-300 bg-gray-800 bg-opacity-30 rounded-md mb-3">
                <span class="text-gray-300 uppercase text-xs font-bold tracking-wider">Member Management</span>
                <svg :class="{'rotate-180': memberOpen, 'rotate-0': !memberOpen}" class="w-4 h-4 transform transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                </svg>
            </button>

            <div x-show="memberOpen" x-transition class="space-y-1">
                <!-- Members Attendance Link -->
                <a href="{{ route('staff.attendance') }}" class="flex items-center px-3 py-2 text-sm font-medium rounded-md group {{ Request::routeIs('staff.attendance') ? 'bg-orange-700 text-white shadow-md shadow-orange-900/30' : 'text-gray-400 hover:bg-orange-700 hover:text-white' }} transition-all duration-200 ease-in-out">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 mr-3 {{ Request::routeIs('staff.attendance') ? 'text-orange-300' : 'text-gray-400 group-hover:text-white' }}" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M11.35 3.836c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 0 0 .75-.75 2.25 2.25 0 0 0-.1-.664m-5.8 0A2.251 2.251 0 0 1 13.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m8.9-4.414c.376.023.75.05 1.124.08 1.131.094 1.976 1.057 1.976 2.192V16.5A2.25 2.25 0 0 1 18 18.75h-2.25m-7.5-10.5H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V18.75m-7.5-10.5h6.375c.621 0 1.125.504 1.125 1.125v9.375m-8.25-3 1.5 1.5 3-3.75" />
                    </svg>
                    <span>Members Attendance</span>
                </a>

                <!-- Member Status Link -->
                <a href="{{ route('staff.viewmembers') }}" class="flex items-center px-3 py-2 text-sm font-medium rounded-md group {{ Request::routeIs('staff.viewmembers') ? 'bg-orange-700 text-white shadow-md shadow-orange-900/30' : 'text-gray-400 hover:bg-orange-700 hover:text-white' }} transition-all duration-200 ease-in-out">
                    <svg class="w-5 h-5 mr-3 {{ Request::routeIs('staff.viewmembers') ? 'text-orange-300' : 'text-gray-400 group-hover:text-white' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                    <span>Member Status</span>
                </a>
                
                <!-- Manage Approval Link -->
                <a href="{{ route('staff.manageApproval') }}" class="flex items-center px-3 py-2 text-sm font-medium rounded-md group {{ Request::routeIs('staff.manageApproval') ? 'bg-orange-700 text-white shadow-md shadow-orange-900/30' : 'text-gray-400 hover:bg-orange-700 hover:text-white' }} transition-all duration-200 ease-in-out">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 mr-3 {{ Request::routeIs('staff.manageApproval') ? 'text-orange-300' : 'text-gray-400 group-hover:text-white' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m2.5-5.5h-11a2.5 2.5 0 0 0-2.5 2.5v11a2.5 2.5 0 0 0 2.5 2.5h11a2.5 2.5 0 0 0 2.5-2.5v-11a2.5 2.5 0 0 0-2.5-2.5z"/>
                    </svg>
                    <span>Manage Approval</span>
                    @if($pendingApprovalCount > 0)
                        <span class="ml-auto inline-flex items-center justify-center px-2 py-1 text-xs font-bold leading-none text-white bg-red-600 rounded-full">
                            {{ $pendingApprovalCount }}
                        </span>
                    @endif
                </a>
            </div>
        </div>

        <!-- Financial Section -->
        <div x-data="{ financeOpen: true }" class="border-b border-gray-800 pb-4">
            <button @click="financeOpen = !financeOpen" class="flex items-center justify-between w-full px-3 py-2 text-gray-300 bg-gray-800 bg-opacity-30 rounded-md mb-3">
                <span class="text-gray-300 uppercase text-xs font-bold tracking-wider">Financial</span>
                <svg :class="{'rotate-180': financeOpen, 'rotate-0': !financeOpen}" class="w-4 h-4 transform transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                </svg>
            </button>

            <div x-show="financeOpen" x-transition class="space-y-1">
                <!-- Payment Tracking Link -->
                <a href="{{ route('staff.paymentTracking') }}" class="flex items-center px-3 py-2 text-sm font-medium rounded-md group {{ Request::routeIs('staff.paymentTracking') ? 'bg-orange-700 text-white shadow-md shadow-orange-900/30' : 'text-gray-400 hover:bg-orange-700 hover:text-white' }} transition-all duration-200 ease-in-out">
                    <svg class="w-5 h-5 mr-3 {{ Request::routeIs('staff.paymentTracking') ? 'text-orange-300' : 'text-gray-400 group-hover:text-white' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <span>Payment Tracking</span>
                </a>

                <!-- Reports Link -->
                <a href="{{ route('staff.report') }}" class="flex items-center px-3 py-2 text-sm font-medium rounded-md group {{ request()->routeIs('staff.report') ? 'bg-orange-700 text-white shadow-md shadow-orange-900/30' : 'text-gray-400 hover:bg-orange-700 hover:text-white' }} transition-all duration-200 ease-in-out">
                    <svg class="w-5 h-5 mr-3 {{ request()->routeIs('staff.report') ? 'text-orange-300' : 'text-gray-400 group-hover:text-white' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    <span>Reports</span>
                </a>
            </div>
        </div>

        <!-- Admin Section (Conditionally Shown) -->
        @if(auth()->user()->role === 'super_admin')
        <div x-data="{ adminOpen: true }" class="border-b border-gray-800 pb-4">
            <button @click="adminOpen = !adminOpen" class="flex items-center justify-between w-full px-3 py-2 text-gray-300 bg-gray-800 bg-opacity-30 rounded-md mb-3">
                <span class="text-gray-300 uppercase text-xs font-bold tracking-wider">Administration</span>
                <svg :class="{'rotate-180': adminOpen, 'rotate-0': !adminOpen}" class="w-4 h-4 transform transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                </svg>
            </button>

            <div x-show="adminOpen" x-transition class="space-y-1">
                <!-- Manage Staffs Link -->
                <a href="{{ route('staff.manageStaffs') }}" class="flex items-center px-3 py-2 text-sm font-medium rounded-md group {{ Request::routeIs('staff.manageStaffs') ? 'bg-orange-700 text-white shadow-md shadow-orange-900/30' : 'text-gray-400 hover:bg-orange-700 hover:text-white' }} transition-all duration-200 ease-in-out">
                    <svg class="w-5 h-5 mr-3 {{ Request::routeIs('staff.manageStaffs') ? 'text-orange-300' : 'text-gray-400 group-hover:text-white' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                    </svg>
                    <span>Manage Staffs</span>
                </a>
            </div>
        </div>
        @endif

        <!-- Account Settings Section -->
        <div x-data="{ settingsOpen: false }" class="mt-auto">
            <button @click="settingsOpen = !settingsOpen" class="flex items-center justify-between w-full px-3 py-2 text-gray-300 bg-gray-800 bg-opacity-30 rounded-md mb-3">
                <span class="text-gray-300 uppercase text-xs font-bold tracking-wider">Account Settings</span>
                <svg :class="{'rotate-180': settingsOpen, 'rotate-0': !settingsOpen}" class="w-4 h-4 transform transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                </svg>
            </button>

            <div x-show="settingsOpen" x-transition class="space-y-1">
                <!-- View Profile Link -->
                <a href="{{ route('profile.edit') }}" class="flex items-center px-3 py-2 text-sm font-medium rounded-md group {{ request()->routeIs('profile.edit') ? 'bg-orange-700 text-white shadow-md shadow-orange-900/30' : 'text-gray-400 hover:bg-orange-700 hover:text-white' }} transition-all duration-200 ease-in-out">
                    <svg class="w-5 h-5 mr-3 {{ request()->routeIs('profile.edit') ? 'text-orange-300' : 'text-gray-400 group-hover:text-white' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                    </svg>
                    <span>View Profile</span>
                </a>

                <!-- Change Price list Link -->
                <a href="{{ route('profile.pricelist') }}" class="flex items-center px-3 py-2 text-sm font-medium rounded-md group {{ request()->routeIs('profile.pricelist') ? 'bg-orange-700 text-white shadow-md shadow-orange-900/30' : 'text-gray-400 hover:bg-orange-700 hover:text-white' }} transition-all duration-200 ease-in-out">
                    <svg class="w-5 h-5 mr-3 {{ request()->routeIs('profile.pricelist') ? 'text-orange-300' : 'text-gray-400 group-hover:text-white' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 11l5-5m0 0l5 5m-5-5v12"></path>
                    </svg>
                    <span>Membership Pricing</span>
                </a>

                <!-- Logout Button -->
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="w-full flex items-center px-3 py-2 text-sm font-medium text-gray-400 rounded-md transition-all duration-200 ease-in-out hover:bg-red-700 hover:text-white group">
                        <svg class="w-5 h-5 mr-3 text-gray-400 group-hover:text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                        </svg>
                        <span>Log Out</span>
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>