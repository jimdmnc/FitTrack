<nav x-data="{ open: false }" class="bg-white border-b border-gray-800 sticky top-0 z-50">
    <header class="flex justify-between items-center w-full px-4 md:px-10 py-3 shadow-md bg-[#121212] transition-all duration-300">
        <!-- Left Section: Hamburger Menu -->
        <div class="flex items-center space-x-4">
            <!-- Hamburger Menu Button -->
            <button id="hamburger" class="p-2 rounded-full text-[#FF5722] hover:text-gray-200 transition-all duration-300 relative" 
                    @click="$store.sidebarOpen = !$store.sidebarOpen">
                <div class="w-6 h-6 relative flex flex-col justify-center items-center">
                    <span id="line1" class="w-5 h-0.5 bg-current absolute transform transition-all duration-300 ease-in-out" 
                          :class="{'rotate-45 translate-y-0': $store.sidebarOpen, '-translate-y-1.5': !$store.sidebarOpen}"></span>
                    <span id="line2" class="w-5 h-0.5 bg-current absolute transform transition-all duration-300 ease-in-out" 
                          :class="{'opacity-0': $store.sidebarOpen, 'opacity-100': !$store.sidebarOpen}"></span>
                    <span id="line3" class="w-5 h-0.5 bg-current absolute transform transition-all duration-300 ease-in-out" 
                          :class="{'-rotate-45 translate-y-0': $store.sidebarOpen, 'translate-y-1.5': !$store.sidebarOpen}"></span>
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
            <span idcdd="time-text">Loading time...</span>
        </div>

        <!-- Right Section: Notifications -->
        <div class="flex items-center space-x-2">
            <!-- Bell Icon for Notifications with Badge -->
            <!-- Add notification button here if needed -->
        </div>
    </header>
</nav>

<script>
const TIME_API_ENDPOINTS = [
    'https://worldtimeapi.org/api/timezone/Asia/Manila',
    'https://timeapi.io/api/Time/current/zone?timeZone=Asia/Manila',
    'https://www.timeapi.io/api/Time/current/zone?timeZone=Asia/Manila'
];

async function fetchTimeFromAPI(endpoint) {
    try {
        const response = await fetch(endpoint, {
            cache: 'no-store',
            headers: { 'Accept': 'application/json' }
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
    if (timeElement) timeElement.textContent = formatTime(date);
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

document.addEventListener('DOMContentLoaded', () => {
    updateInternetTime();
    setInterval(updateInternetTime, 60000);
    const quickUpdateInterval = setInterval(updateInternetTime, 1000);
    setTimeout(() => clearInterval(quickUpdateInterval), 10000);
});
</script>