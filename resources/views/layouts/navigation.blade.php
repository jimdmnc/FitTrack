<nav x-data="{ open: false }" class="bg-white border-b border-gray-800 sticky top-0 z-50">
    <header class="flex justify-between items-center w-full px-10 py-3 shadow-md bg-[#121212] transition-all duration-300">
        <!-- Left Section: Brand and Hamburger Menu -->
        <div class="flex items-center space-x-4">
            <!-- Hamburger Menu Button with Animation -->
            <button id="hamburger" class="p-2 rounded-full text-[#FF5722] hover:text-gray-200 transition-all duration-300 relative" @click="open = !open">
                <div class="w-6 h-6 relative flex flex-col justify-center items-center">
                    <!-- Hamburger lines with transition effects -->
                    <span id="line1" class="w-5 h-0.5 bg-current absolute transform transition-all duration-300 ease-in-out" 
                          :class="{'rotate-45': open, '-translate-y-1.5': !open}"></span>
                    <span id="line2" class="w-5 h-0.5 bg-current absolute transform transition-all duration-300 ease-in-out" 
                          :class="{'opacity-0': open, 'opacity-100': !open}"></span>
                    <span id="line3" class="w-5 h-0.5 bg-current absolute transform transition-all duration-300 ease-in-out" 
                          :class="{'-rotate-45 translate-y-0': open, 'translate-y-1.5': !open}"></span>
                </div>
                <span class="sr-only">Toggle menu</span>
            </button>
        </div>

        <!-- Center: User-Friendly Time Display -->
        <div class="hidden md:flex items-center space-x-2 text-lg font-semibold text-gray-200 rounded-full bg-[#1E1E1E] px-4 py-2 shadow-sm" id="current-time">
            <span class="inline-flex">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-[#FF5722] w-6 h-6">
                    <circle cx="12" cy="12" r="10"></circle>
                    <polyline points="12 6 12 12 16 14"></polyline>
                </svg>
            </span>
            <span id="time-text">12:00 PM</span>
        </div>

        <!-- Right Section: Search, Feedback, Notifications, and Create Announcement -->
        <div class="flex items-center space-x-2">
            <button id="openModalBtn" class="bg-[#FF5722] text-white px-4 py-2 rounded-full hover:bg-[#e64a19] transition-all duration-300">
                Create Announcement
            </button>
        </div>
    </header>
</nav>
<script>
function updateTime() {
    const now = new Date();
    const hours = now.getHours();
    const minutes = now.getMinutes().toString().padStart(2, '0');
    const ampm = hours >= 12 ? 'PM' : 'AM';
    const formattedHours = (hours % 12) || 12; // Convert to 12-hour format
    
    document.getElementById('time-text').textContent = `${formattedHours}:${minutes} ${ampm}`;
}

// Update time immediately and then every second
updateTime();
setInterval(updateTime, 1000);
</script>