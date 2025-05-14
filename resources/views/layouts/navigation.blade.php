
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
            <span id="time-text">Loading time...</span>
        </div>

        <!-- Right Section: Search, Feedback and Notifications -->
        <div class="flex items-center space-x-2">
            
            <!-- Feedback Button with Enhanced Design -->
            <!-- <button class="hidden sm:flex items-center px-3 py-1.5 text-sm font-medium text-gray-200 bg-[#1E1E1E] rounded-full hover:bg-gray300-100 hover:text-[#FF5722] transition-all duration-200">
                <svg class="w-4 h-4 mr-1 text-[#FF5722]" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"></path>
                </svg>
                Feedback
            </button> -->

            <!-- Bell Icon for Notifications with Badge -->
            <!-- <div class="relative">
                <button class="p-2 rounded-full text-[#FF5722] hover:text-gray-200 focus:outline-none focus:ring-2 focus:ring-blue-300" aria-label="Notifications">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                    </svg>
                </button> -->
                <!-- Notification Badge -->
                <!-- <span class="absolute top-0 right-0 inline-flex items-center justify-center px-1.5 py-0.5 text-xs font-bold leading-none text-white transform translate-x-1/4 -translate-y-1/4 bg-red-500 rounded-full">3</span>
            </div> -->
            
            <!-- User Profile -->

        </div>
    </header>
        
</nav>
<script>
    // Using a reliable time API with multiple endpoints as fallback
const TIME_API_ENDPOINTS = [
    'https://worldtimeapi.org/api/timezone/Asia/Manila',
    'https://timeapi.io/api/Time/current/zone?timeZone=Asia/Manila',
    'https://www.timeapi.io/api/Time/current/zone?timeZone=Asia/Manila'
];

async function fetchTimeFromAPI(endpoint) {
    try {
        const response = await fetch(endpoint, {
            cache: 'no-store',
            headers: {
                'Accept': 'application/json'
            }
        });
        
        if (!response.ok) throw new Error(`API response not OK: ${response.status}`);
        
        const data = await response.json();
        return new Date(data.datetime || data.dateTime || data.currentDateTime);
    } catch (error) {
        console.error(`Failed to fetch from ${endpoint}:`, error);
        return null;
    }
}

async function getInternetTime() {
    // Try each API endpoint in sequence
    for (const endpoint of TIME_API_ENDPOINTS) {
        const date = await fetchTimeFromAPI(endpoint);
        if (date) return date;
    }
    
    throw new Error('All time APIs failed');
}

function formatTime(date) {
    const hours = date.getHours();
    const minutes = date.getMinutes().toString().padStart(2, '0');
    const ampm = hours >= 12 ? 'PM' : 'AM';
    const formattedHours = (hours % 12) || 12;
    
    return `${formattedHours}:${minutes} ${ampm}`;
}

function updateTimeDisplay(date) {
    const timeElement = document.getElementById('time-text');
    if (timeElement) {
        timeElement.textContent = formatTime(date);
    }
}

async function updateInternetTime() {
    try {
        const date = await getInternetTime();
        updateTimeDisplay(date);
    } catch (error) {
        console.error('Failed to get internet time:', error);
        const timeElement = document.getElementById('time-text');
        if (timeElement) {
            timeElement.textContent = 'Time unavailable';
            timeElement.classList.add('text-red-500');
        }
    }
}

// Initialize when page loads
document.addEventListener('DOMContentLoaded', () => {
    // First update
    updateInternetTime();
    
    // Update every minute (60000ms)
    setInterval(updateInternetTime, 60000);
    
    // Also update immediately every second for the first 10 seconds
    // to ensure we get a time display quickly
    const quickUpdateInterval = setInterval(updateInternetTime, 1000);
    setTimeout(() => clearInterval(quickUpdateInterval), 10000);
});
</script>   