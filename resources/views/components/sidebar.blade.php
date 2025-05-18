<div class="flex flex-col w-64 md:w-64 h-full overflow-y-auto bg-[#121212] transition-all duration-300 ease-in-out will-change-transform"
     id="sidebar" x-data="{ dropdownOpen: false }">
    <!-- Logo and Brand Header -->
    <div class="flex items-center p-2 md:p-3">
        <a href="{{ route('staff.dashboard') }}" class="flex items-center w-full">
            <img src="{{ asset('images/rockiesLogo.jpg') }}" alt="Rockies Logo" class="h-8 w-8 p-1 rounded-full flex-shrink-0">
            <div class="ml-2 whitespace-nowrap sidebar-text">
                <span class="text-base font-extrabold text-white" style="font-family: 'Rajdhani', sans-serif;">
                    Rockies 
                    <span class="gradient-text bg-gradient-to-r from-red-600 to-orange-600 bg-clip-text text-transparent">Fitness</span>
                </span>
            </div>
        </a>
    </div>

    <!-- Navigation Links -->
    <div class="flex flex-col flex-grow px-2 mt-3">
        <nav class="flex-1 space-y-1">
            <span class="text-gray-400 uppercase text-xs font-semibold tracking-wider px-3 py-2 sidebar-text">Main</span>

            <!-- Dashboard Link -->
            <a href="{{ route('staff.dashboard') }}" 
               class="flex items-center px-3 py-2 text-sm font-medium rounded-md group/item 
                      {{ Request::routeIs('staff.dashboard') ? 'bg-orange-700 text-white' : 'text-gray-400 hover:bg-orange-700 hover:text-white' }} 
                      transition-all duration-200">
                <svg class="w-5 h-5 mr-2 flex-shrink-0 {{ Request::routeIs('staff.dashboard') ? 'text-white' : 'text-gray-400 group-hover/item:text-white' }}" 
                     fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                          d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                </svg>  
                <span class="whitespace-nowrap sidebar-text">Dashboard</span>
            </a>

            <!-- Manage Staffs Link (Visible only to Super Admins) -->
            @if(auth()->user()->role === 'super_admin')
            <a href="{{ route('staff.manageStaffs') }}" 
               class="flex items-center px-3 py-2 text-sm font-medium rounded-md group/item 
                      {{ Request::routeIs('staff.manageStaffs') ? 'bg-orange-700 text-white' : 'text-gray-400 hover:bg-orange-700 hover:text-white' }} 
                      transition-all duration-200">
                <svg class="w-5 h-5 mr-2 flex-shrink-0 {{ Request::routeIs('staff.manageStaffs') ? 'text-white' : 'text-gray-400 group-hover/item:text-white' }}" 
                     fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                          d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                </svg>
                <span class="whitespace-nowrap sidebar-text">Manage Staffs</span>
            </a>
            @endif

            <!-- Payment Tracking Link -->
            <a href="{{ route('staff.paymentTracking') }}" 
               class="flex items-center px-3 py-2 text-sm font-medium rounded-md group/item 
                      {{ Request::routeIs('staff.paymentTracking') ? 'bg-orange-700 text-white' : 'text-gray-400 hover:bg-orange-700 hover:text-white' }} 
                      transition-all duration-200">
                <svg class="w-5 h-5 mr-2 flex-shrink-0 {{ Request::routeIs('staff.paymentTracking') ? 'text-white' : 'text-gray-400 group-hover/item:text-white' }}" 
                     fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                          d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <span class="whitespace-nowrap sidebar-text">Payment Tracking</span>
            </a>

            <span class="text-gray-400 uppercase text-xs font-semibold tracking-wider px-3 py-2 sidebar-text">Members Management</span>

            <!-- Register Member Link -->
            <a href="{{ route('staff.membershipRegistration') }}" 
               class="flex items-center px-3 py-2 text-sm font-medium rounded-md group/item 
                      {{ Request::routeIs('staff.membershipRegistration') ? 'bg-orange-700 text-white' : 'text-gray-400 hover:bg-orange-700 hover:text-white' }} 
                      transition-all duration-200">
                <svg class="w-5 h-5 mr-2 flex-shrink-0 {{ Request::routeIs('staff.membershipRegistration') ? 'text-white' : 'text-gray-400 group-hover/item:text-white' }}" 
                     fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                          d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                </svg>
                <span class="whitespace-nowrap sidebar-text">Register Member</span>
            </a>

            <!-- Members Attendance Link -->
            <a href="{{ route('staff.attendance') }}" 
               class="flex items-center px-3 py-2 text-sm font-medium rounded-md group/item 
                      {{ Request::routeIs('staff.attendance') ? 'bg-orange-700 text-white' : 'text-gray-400 hover:bg-orange-700 hover:text-white' }} 
                      transition-all duration-200">
                <svg class="w-5 h-5 mr-2 flex-shrink-0 {{ Request::routeIs('staff.attendance') ? 'text-white' : 'text-gray-400 group-hover/item:text-white' }}" 
                     fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" 
                          d="M11.35 3.836c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 0 0 .75-.75 2.25 2.25 0 0 0-.1-.664m-5.8 0A2.251 2.251 0 0 1 13.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m8.9-4.414c.376.023.75.05 1.124.08 1.131.094 1.976 1.057 1.976 2.192V16.5A2.25 2.25 0 0 1 18 18.75h-2.25m-7.5-10.5H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V18.75m-7.5-10.5h6.375c.621 0 1.125.504 1.125 1.125v9.375m-8.25-3 1.5 1.5 3-3.75" />
                </svg>
                <span class="whitespace-nowrap sidebar-text">Members Attendance</span>
            </a>

            <!-- Manage Approval Link -->
            <a href="{{ route('staff.manageApproval') }}" 
               class="flex items-center px-3 py-2 text-sm font-medium rounded-md group/item 
                      {{ Request::routeIs('staff.manageApproval') ? 'bg-orange-700 text-white' : 'text-gray-400 hover:bg-orange-700 hover:text-white' }} 
                      transition-all duration-200">
                <svg class="w-5 h-5 mr-2 flex-shrink-0 {{ Request::routeIs('staff.manageApproval') ? 'text-white' : 'text-gray-400 group-hover/item:text-white' }}" 
                     fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M9 12l2 2 4-4m2.5-5.5h-11a2.5 2.5 0 0 0-2.5 2.5v11a2.5 2.5 0 0 0 2.5 2.5h11a2.5 2.5 0 0 0 2.5-2.5v-11a2.5 2.5 0 0 0-2.5-2.5z"/>
                </svg>
                <span class="whitespace-nowrap sidebar-text">Manage Approval</span>
                <span id="pending-approval-badge" 
                      class="ml-2 inline-flex items-center justify-center px-2 py-0.5 text-xs font-bold leading-none text-white bg-red-600 rounded-full {{ $pendingApprovalCount > 0 ? '' : 'hidden' }} sidebar-text">
                    {{ $pendingApprovalCount }}
                </span>
            </a>

            <!-- Member Status Link -->
            <a href="{{ route('staff.viewmembers') }}" 
               class="flex items-center px-3 py-2 text-sm font-medium rounded-md group/item 
                      {{ Request::routeIs('staff.viewmembers') ? 'bg-orange-700 text-white' : 'text-gray-400 hover:bg-orange-700 hover:text-white' }} 
                      transition-all duration-200">
                <svg class="w-5 h-5 mr-2 flex-shrink-0 {{ Request::routeIs('staff.viewmembers') ? 'text-white' : 'text-gray-400 group-hover/item:text-white' }}" 
                     fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                          d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                </svg>
                <span class="whitespace-nowrap sidebar-text">Member Status</span>
            </a>

            <!-- Reports Link -->
            <a href="{{ route('staff.report') }}" 
               class="flex items-center px-3 py-2 text-sm font-medium rounded-md group/item 
                      {{ Request::routeIs('staff.report') ? 'bg-orange-700 text-white' : 'text-gray-400 hover:bg-orange-700 hover:text-white' }} 
                      transition-all duration-200">
                <svg class="w-5 h-5 mr-2 flex-shrink-0 {{ Request::routeIs('staff.report') ? 'text-white' : 'text-gray-400 group-hover/item:text-white' }}" 
                     fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                          d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                <span class="whitespace-nowrap sidebar-text">Reports</span>
            </a>

            <!-- Profile Section -->
            <span class="text-gray-400 uppercase text-xs font-semibold tracking-wider px-3 py-2 sidebar-text">Settings</span>

            <!-- Profile Dropdown -->
            <div x-data="{ dropdownOpen: false }" class="mt-1">
                <button @click="dropdownOpen = !dropdownOpen" 
                        class="group/item flex items-center w-full px-3 py-2 text-sm font-medium rounded-md 
                            {{ request()->routeIs('profile.edit') ? 'bg-orange-700 text-white' : 'text-gray-400 hover:bg-orange-700 hover:text-white' }} 
                            transition-all duration-200">
                    <svg class="w-5 h-5 mr-2 flex-shrink-0 {{ request()->routeIs('profile.edit') ? 'text-white' : 'text-gray-400 group-hover/item:text-white' }}" 
                        fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                            d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                    </svg>
                    <span class="whitespace-nowrap sidebar-text">Account Settings</span>
                    
                    <!-- Always visible dropdown icon -->
                    <svg :class="{ 'rotate-90': dropdownOpen, 'rotate-0': !dropdownOpen }" 
                        class="w-4 h-4 ml-auto transform transition-transform duration-200 text-gray-400 group-hover/item:text-white sidebar-text" 
                        fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                    </svg>
                </button>

                <div x-show="dropdownOpen" 
                    x-transition:enter="transition ease-out duration-200" 
                    x-transition:enter-start="opacity-0 transform -translate-y-2" 
                    x-transition:enter-end="opacity-100 transform translate-y-0" 
                    x-transition:leave="transition ease-in duration-150" 
                    x-transition:leave-start="opacity-100 transform translate-y-0" 
                    x-transition:leave-end="opacity-0 transform -translate-y-2" 
                    class="pl-4 mt-1 space-y-1 sidebar-text">
                    <a href="{{ route('profile.edit') }}" 
                    class="group/item block text-sm font-medium text-gray-400 rounded-md px-3 py-2 transition-all duration-200 hover:bg-orange-700 hover:text-white">
                        View Profile
                    </a>
                    <a href="{{ route('profile.pricelist') }}" 
                    class="group/item block text-sm font-medium text-gray-400 rounded-md px-3 py-2 transition-all duration-200 hover:bg-orange-700 hover:text-white">
                        Membership Pricing
                    </a>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" 
                                class="group/item w-full text-left flex items-center px-3 py-2 text-sm font-medium text-gray-400 rounded-md transition-all duration-200 hover:bg-orange-700 hover:text-white">
                            Log Out
                        </button>
                    </form>
                </div>
            </div>
        </nav>
    </div>

    <!-- Sidebar Toggle Script -->
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize pending approval count feature
        initPendingApprovalCount();
        
        // Initialize sidebar text visibility
        initSidebarTextVisibility();
    });

    function initPendingApprovalCount() {
        const badge = document.getElementById('pending-approval-badge');
        if (!badge) return;

        async function fetchPendingApprovalCount() {
            try {
                const response = await fetch('{{ route('staff.pendingApprovalCount') }}', {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                });
                if (!response.ok) throw new Error('Network response was not ok');
                const data = await response.json();

                if (data.success) {
                    badge.textContent = data.count;
                    badge.classList.toggle('hidden', data.count === 0);
                } else {
                    console.error('Failed to fetch pending approval count:', data.message);
                }
            } catch (error) {
                console.error('Error fetching pending approval count:', error);
            }
        }

        fetchPendingApprovalCount();
        setInterval(fetchPendingApprovalCount, 30000);
    }

    function initSidebarTextVisibility() {
        const sidebar = document.getElementById('sidebar');
        const textElements = document.querySelectorAll('.sidebar-text');
        
        function updateTextVisibility() {
            const isCollapsed = sidebar.classList.contains('collapsed');
            textElements.forEach(element => {
                element.style.display = isCollapsed ? 'none' : 'block';
            });
        }

        // Initial update
        updateTextVisibility();

        // Observe class changes on sidebar
        const observer = new MutationObserver((mutations) => {
            mutations.forEach((mutation) => {
                if (mutation.attributeName === 'class') {
                    updateTextVisibility();
                }
            });
        });

        observer.observe(sidebar, { attributes: true });

        // Update on window resize
        window.addEventListener('resize', updateTextVisibility);
    }
    </script>
</div>