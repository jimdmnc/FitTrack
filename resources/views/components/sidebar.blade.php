<div class="flex flex-col w-full h-full overflow-y-auto bg-[#121212]">
    <!-- Logo and Brand Header -->
    <div class="flex items-center p-2 sm:p-3 lg:p-4">
        <a href="{{ route('staff.dashboard') }}" class="flex items-center">
            <img src="{{ asset('images/rockiesLogo.jpg') }}" alt="Rockies Logo" class="h-8 w-8 sm:h-10 sm:w-10 lg:h-12 lg:w-12 p-1 sm:p-2 rounded-full">
            <div class="ml-1 sm:ml-2">
                <span class="text-sm sm:text-base lg:text-lg font-extrabold text-white" style="line-height: 1; font-family: 'Rajdhani', sans-serif;">
                    Rockies 
                    <span class="gradient-text bg-gradient-to-r from-red-600 to-orange-600 bg-clip-text text-transparent">Fitness</span>
                </span>
            </div>
        </a>
    </div>  

    <!-- Navigation Links -->
    <div class="flex flex-col flex-grow px-1 sm:px-2 lg:px-3 mt-2 sm:mt-3 lg:mt-4" :class="{'block': open, 'hidden': !open}">
        <nav class="flex-1 space-y-1">
            <span class="text-gray-400 uppercase text-xs font-semibold tracking-wider px-2 sm:px-3 py-1 sm:py-2 block">Main</span>

            <!-- Dashboard Link -->
            <a href="{{ route('staff.dashboard') }}" class="flex items-center px-2 sm:px-3 py-1.5 sm:py-2 text-xs sm:text-sm font-medium rounded-md group {{ Request::routeIs('staff.dashboard') ? 'bg-orange-700 text-white' : 'text-gray-400 hover:bg-orange-700 hover:text-white' }} transition-all duration-200 ease-in-out transform hover:translate-x-1">
                <svg class="w-4 h-4 sm:w-5 sm:h-5 mr-1.5 sm:mr-2 {{ Request::routeIs('staff.dashboard') ? 'text-white' : 'text-gray-400 group-hover:text-white' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                </svg>  
                <span>Dashboard</span>
            </a>

            <!-- Register Member Link -->
            <a href="{{ route('staff.membershipRegistration') }}" class="flex items-center px-2 sm:px-3 py-1.5 sm:py-2 text-xs sm:text-sm font-medium rounded-md group {{ Request::routeIs('staff.membershipRegistration') ? 'bg-orange-700 text-white' : 'text-gray-400 hover:bg-orange-700 hover:text-white' }} transition-all duration-200 ease-in-out transform hover:translate-x-1">
                <svg class="w-4 h-4 sm:w-5 sm:h-5 mr-1.5 sm:mr-2 {{ Request::routeIs('staff.membershipRegistration') ? 'text-white' : 'text-gray-400 group-hover:text-white' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                </svg>
                <span>Register Member</span>
            </a>
            <span class="text-gray-400 uppercase text-xxs font-semibold tracking-wider px-2 sm:px-3 py-1 sm:py-2 block mt-2 sm:mt-3">Members Management</span>

            <!-- Members Attendance Link -->
            <a href="{{ route('staff.attendance') }}" class="flex items-center px-2 sm:px-3 py-1.5 sm:py-2 text-xs sm:text-sm font-medium rounded-md group {{ Request::routeIs('staff.attendance') ? 'bg-orange-700 text-white' : 'text-gray-400 hover:bg-orange-700 hover:text-white' }} transition-all duration-200 ease-in-out transform hover:translate-x-1">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 sm:w-5 sm:h-5 mr-1.5 sm:mr-2 {{ Request::routeIs('staff.attendance') ? 'text-white' : 'text-gray-400 group-hover:text-white' }}" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M11.35 3.836c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 0 0 .75-.75 2.25 2.25 0 0 0-.1-.664m-5.8 0A2.251 2.251 0 0 1 13.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m8.9-4.414c.376.023.75.05 1.124.08 1.131.094 1.976 1.057 1.976 2.192V16.5A2.25 2.25 0 0 1 18 18.75h-2.25m-7.5-10.5H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V18.75m-7.5-10.5h6.375c.621 0 1.125.504 1.125 1.125v9.375m-8.25-3 1.5 1.5 3-3.75" />
                </svg>
                <span>Members Attendance</span>
            </a>

            <!-- Manage Approval Link -->
            <a href="{{ route('staff.manageApproval') }}"
                class="flex items-center px-2 sm:px-3 py-1.5 sm:py-2 text-xs sm:text-sm font-medium rounded-md group
                {{ Request::routeIs('staff.manageApproval') ? 'bg-orange-700 text-white' : 'text-gray-400 hover:bg-orange-700 hover:text-white' }}
                transition-all duration-200 ease-in-out transform hover:translate-x-1">
                <svg xmlns="http://www.w3.org/2000/svg"
                        class="w-4 h-4 sm:w-5 sm:h-5 mr-1.5 sm:mr-2"
                        fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m2.5-5.5h-11a2.5 2.5 0 0 0-2.5 2.5v11a2.5 2.5 0 0 0 2.5 2.5h11a2.5 2.5 0 0 0 2.5-2.5v-11a2.5 2.5 0 0 0-2.5-2.5z"/>
                </svg>
                Manage Approval
                @if($pendingApprovalCount > 0)
                    <span class="ml-2 inline-flex items-center justify-center px-2 py-0.5 text-xs font-bold leading-none text-white bg-red-600 rounded-full">
                        {{ $pendingApprovalCount }}
                    </span>
                @endif
            </a>

            <!-- Manage Staffs Link (Visible only to Super Admins) -->
            @if(auth()->user()->role === 'super_admin')
            <a href="{{ route('staff.manageStaffs') }}" class="flex items-center px-2 sm:px-3 py-1.5 sm:py-2 text-xs sm:text-sm font-medium rounded-md group {{ Request::routeIs('staff.manageStaffs') ? 'bg-orange-700 text-white' : 'text-gray-400 hover:bg-orange-700 hover:text-white' }} transition-all duration-200 ease-in-out transform hover:translate-x-1">
                <svg class="w-4 h-4 sm:w-5 sm:h-5 mr-1.5 sm:mr-2 {{ Request::routeIs('staff.manageStaffs') ? 'text-white' : 'text-gray-400 group-hover:text-white' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                </svg>
                <span>Manage Staffs</span>
            </a>
            @endif

            <!-- Member Status Link -->
            <a href="{{ route('staff.viewmembers') }}" class="flex items-center px-2 sm:px-3 py-1.5 sm:py-2 text-xs sm:text-sm font-medium rounded-md group {{ Request::routeIs('staff.viewmembers') ? 'bg-orange-700 text-white' : 'text-gray-400 hover:bg-orange-700 hover:text-white' }} transition-all duration-200 ease-in-out transform hover:translate-x-1">
                <svg class="w-4 h-4 sm:w-5 sm:h-5 mr-1.5 sm:mr-2 {{ Request::routeIs('staff.viewmembers') ? 'text-white' : 'text-gray-400 group-hover:text-white' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                </svg>
                <span>Member Status</span>
            </a>

            <!-- Payment Tracking Link -->
            <a href="{{ route('staff.paymentTracking') }}" class="flex items-center px-2 sm:px-3 py-1.5 sm:py-2 text-xs sm:text-sm font-medium rounded-md group {{ Request::routeIs('staff.paymentTracking') ? 'bg-orange-700 text-white' : 'text-gray-400 hover:bg-orange-700 hover:text-white' }} transition-all duration-200 ease-in-out transform hover:translate-x-1">
                <svg class="w-4 h-4 sm:w-5 sm:h-5 mr-1.5 sm:mr-2 {{ Request::routeIs('staff.paymentTracking') ? 'text-white' : 'text-gray-400 group-hover:text-white' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <span>Payment Tracking</span>
            </a>

            <!-- Reports Link -->
            <a href="{{ route('staff.report') }}" class="flex items-center px-2 sm:px-3 py-1.5 sm:py-2 text-xs sm:text-sm font-medium rounded-md group {{ request()->routeIs('staff.report') ? 'bg-orange-700 text-white' : 'text-gray-400 hover:bg-orange-700 hover:text-white' }} transition-all duration-200 ease-in-out transform hover:translate-x-1">
                <svg class="w-4 h-4 sm:w-5 sm:h-5 mr-1.5 sm:mr-2 {{ request()->routeIs('staff.report') ? 'text-white' : 'text-gray-400 group-hover:text-white' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                <span>Reports</span>
            </a>

            <!-- Profile Section -->
            <span class="text-gray-400 uppercase text-xs font-semibold tracking-wider px-2 sm:px-3 py-1 sm:py-2 block mt-2 sm:mt-3">Settings</span>

            <!-- Profile Dropdown -->
            <div x-data="{ open: false }" class="mt-1">
                <!-- Profile Button -->
                <button @click="open = !open" class="group flex items-center w-full px-2 sm:px-3 py-1.5 sm:py-2 text-xs sm:text-sm font-medium rounded-md {{ request()->routeIs('profile.edit') ? 'bg-orange-700 text-white' : 'text-gray-400 hover:bg-orange-700 hover:text-white' }} transition-all duration-200 ease-in-out transform hover:translate-x-1">
                    <!-- Profile Icon -->
                    <svg class="w-4 h-4 sm:w-5 sm:h-5 mr-1.5 sm:mr-2 {{ request()->routeIs('profile.edit') ? 'text-white' : 'text-gray-400 group-hover:text-white' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                    </svg>
                    <span>Account Settings</span>

                    <!-- Dropdown Arrow -->
                    <svg :class="{'rotate-90': open, 'rotate-0': !open}" class="w-3 h-3 sm:w-4 sm:h-4 ml-auto transform transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                    </svg>
                </button>

                <!-- Dropdown Menu -->
                <div x-show="open" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 transform -translate-y-2" x-transition:enter-end="opacity-100 transform translate-y-0" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 transform translate-y-0" x-transition:leave-end="opacity-0 transform -translate-y-2" class="pl-2 sm:pl-4 mt-1 space-y-1">
                    <!-- View Profile Link -->
                    <a href="{{ route('profile.edit') }}" class="group block text-xs sm:text-sm font-medium text-gray-400 rounded-md px-2 sm:px-3 py-1.5 sm:py-2 transition-all duration-200 ease-in-out hover:bg-orange-700 hover:text-white hover:translate-x-1 transform">
                        View Profile
                    </a>
                    <!-- Change Price list Link -->
                    <a href="{{ route('profile.pricelist') }}" class="group block text-xs sm:text-sm font-medium text-gray-400 rounded-md px-2 sm:px-3 py-1.5 sm:py-2 transition-all duration-200 ease-in-out hover:bg-orange-700 hover:text-white hover:translate-x-1 transform">
                        Membership Pricing
                    </a>
                    <!-- Logout Button -->
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="group w-full text-left flex items-center px-2 sm:px-3 py-1.5 sm:py-2 text-xs sm:text-sm font-medium text-gray-400 rounded-md transition-all duration-200 ease-in-out hover:bg-orange-700 hover:text-white hover:translate-x-1 transform">
                            Log Out
                        </button>
                    </form>
                </div>
            </div>
        </nav>
    </div>
</div>