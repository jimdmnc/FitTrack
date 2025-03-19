<nav x-data="{ open: false }" class="bg-white border-b border-gray-200 sticky top-0 z-50">
    <header class="flex justify-between items-center w-full px-4 py-3 shadow-md bg-white transition-all duration-300">
        <!-- Left Section: Brand and Hamburger Menu -->
        <div class="flex items-center space-x-4">
            <!-- Hamburger Menu Button with Animation -->
            <button id="hamburger" class="p-2 rounded-full hover:bg-gray-100 text-gray-700 transition-all duration-300 relative group focus:outline-none focus:ring-2 focus:ring-blue-300 focus:ring-offset-2">
                <div class="w-6 h-6 relative flex flex-col justify-center items-center">
                    <!-- Hamburger lines with transition effects -->
                    <span id="line1" class="w-5 h-0.5 bg-current absolute transform transition-all duration-300 ease-in-out -translate-y-1.5 group-hover:bg-gray-900"></span>
                    <span id="line2" class="w-5 h-0.5 bg-current absolute transform transition-all duration-300 ease-in-out group-hover:bg-gray-900"></span>
                    <span id="line3" class="w-5 h-0.5 bg-current absolute transform transition-all duration-300 ease-in-out translate-y-1.5 group-hover:bg-gray-900"></span>
                </div>
                <span class="sr-only">Toggle menu</span>
            </button>
            
            <!-- Brand/Logo -->
            <div class="flex items-center">
                <div class="h-8 w-8 bg-blue-600 rounded-lg flex items-center justify-center">
                    <span class="text-white font-bold text-lg">FT</span>
                </div>
                <span class="ml-2 text-2xl font-bold text-gray-800">FitTrack</span>
            </div>
        </div>
        

        <!-- Center: User-Friendly Time Display -->
        <div class="hidden md:flex items-center space-x-1 text-lg font-semibold text-gray-800" id="current-time"></div>

        <!-- Right Section: Search, Feedback and Notifications -->
        <div class="flex items-center space-x-2">
            <!-- Search Button/Icon -->
            <button class="p-2 rounded-full text-gray-500 hover:text-gray-700 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-blue-300">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                </svg>
                <span class="sr-only">Search</span>
            </button>
            
            <!-- Feedback Button with Enhanced Design -->
            <button class="hidden sm:flex items-center px-3 py-1.5 text-sm font-medium text-gray-600 bg-gray-100 rounded-full hover:bg-blue-100 hover:text-blue-600 transition-all duration-200">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"></path>
                </svg>
                Feedback
            </button>

            <!-- Bell Icon for Notifications with Badge -->
            <div class="relative">
                <button class="p-2 rounded-full text-gray-500 hover:text-gray-700 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-blue-300" aria-label="Notifications">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                    </svg>
                </button>
                <!-- Notification Badge -->
                <span class="absolute top-0 right-0 inline-flex items-center justify-center px-1.5 py-0.5 text-xs font-bold leading-none text-white transform translate-x-1/4 -translate-y-1/4 bg-red-500 rounded-full">3</span>
            </div>
            
            <!-- User Profile -->
            <div class="relative ml-2">
                <button class="flex items-center focus:outline-none focus:ring-2 focus:ring-blue-300 rounded-full" id="user-menu-button" aria-expanded="false" aria-haspopup="true">
                    <span class="sr-only">Open user menu</span>
                    <div class="h-8 w-8 rounded-full bg-blue-500 flex items-center justify-center text-white">
                        <span class="text-sm font-medium">JD</span>
                    </div>
                </button>
            </div>
        </div>
    </header>
    
    <!-- Mobile Menu (Hidden by Default) -->
    <div id="mobile-menu" class="hidden px-2 pt-2 pb-3 space-y-1 bg-white border-t border-gray-200 shadow-lg">
        <a href="#" class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:bg-blue-50 hover:text-blue-600">Dashboard</a>
        <a href="#" class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:bg-blue-50 hover:text-blue-600">Members</a>
        <a href="#" class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:bg-blue-50 hover:text-blue-600">Reports</a>
        <a href="#" class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:bg-blue-50 hover:text-blue-600">Settings</a>
        <div class="pt-4 pb-3 border-t border-gray-200">
            <div class="flex items-center px-3">
                <div class="flex-shrink-0">
                    <div class="h-10 w-10 rounded-full bg-blue-500 flex items-center justify-center text-white">
                        <span class="text-sm font-medium">JD</span>
                    </div>
                </div>
                <div class="ml-3">
                    <div class="text-base font-medium text-gray-800">John Doe</div>
                    <div class="text-sm font-medium text-gray-500">john@example.com</div>
                </div>
            </div>
        </div>
    </div>
</nav>
<script>
    function updateTime() {
        const now = new Date();
        const options = { hour: 'numeric', minute: 'numeric', second: 'numeric', hour12: true };
        document.getElementById('current-time').textContent = new Intl.DateTimeFormat('en-US', options).format(now);
    }
    setInterval(updateTime, 1000);
    updateTime();
</script>
