<nav x-data="{ open: false }" class="bg-white border-b border-gray-100">
<header class="flex justify-between items-center w-full p-4 shadow-lg shadow-gray-400 bg-white">
    <!-- Left Section: Hamburger Menu -->
    <div class="flex items-center">
        <!-- Hamburger Menu Button -->
        <button id="hamburger" class="p-2 rounded-lg  text-gray-600 hover:bg-gray-100  transition-all duration-300 relative">
                <div class="w-6 h-6 relative flex flex-col justify-center items-center">
                    <!-- Hamburger lines with transition effects -->
                    <span id="line1" class="w-5 h-0.5 bg-current absolute transform transition-all duration-300 ease-in-out"></span>
                    <span id="line2" class="w-5 h-0.5 bg-current absolute transform transition-all duration-300 ease-in-out"></span>
                    <span id="line3" class="w-5 h-0.5 bg-current absolute transform transition-all duration-300 ease-in-out"></span>
                </div>
                <span class="sr-only">Toggle menu</span>
        </button>
    </div>

    <!-- Right Section: Feedback and Bell Icon -->
    <div class="flex items-center space-x-4">
        <!-- Feedback Button -->
        <button class="px-4 py-2 text-sm font-medium text-gray-500  hover:text-lg transition-all duration-300 ease-in-out ">
            Feedback
        </button>

        <!-- Bell Icon for Notifications -->
        <button class="p-2 text-gray-500 hover:text-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
            </svg>
        </button>
    </div>
</header>
</nav>