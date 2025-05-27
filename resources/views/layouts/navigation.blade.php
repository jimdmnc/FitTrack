<style>
    /* 7-Segment Display Styling */
    .digit {
        position: relative;
        width: 24px;
        height: 40px;
        margin: 0 1px;
    }
    
    .segment-a, .segment-b, .segment-c, 
    .segment-d, .segment-e, .segment-f, 
    .segment-g {
        position: absolute;
        background-color: #FF5722;
        opacity: 0.1;
        transition: opacity 0.3s ease;
    }
    
    /* Horizontal segments (a, g, d) */
    .segment-a, .segment-g, .segment-d {
        height: 4px;
        width: 18px;
    }
    
    /* Vertical segments (b, c, e, f) */
    .segment-b, .segment-c, .segment-e, .segment-f {
        height: 16px;
        width: 4px;
    }
    
    /* Segment positions */
    .segment-a { top: 0; left: 3px; }
    .segment-b { top: 2px; right: 0; }
    .segment-c { bottom: 2px; right: 0; }
    .segment-d { bottom: 0; left: 3px; }
    .segment-e { bottom: 2px; left: 0; }
    .segment-f { top: 2px; left: 0; }
    .segment-g { top: 50%; left: 3px; transform: translateY(-50%); }
    
    /* Colon (:) */
    .colon {
        height: 40px;
        padding: 0 2px;
    }
</style>

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
       <!-- Replace the existing #current-time div with this: -->
<div class="hidden md:flex items-center space-x-2 text-lg font-semibold text-gray-200 rounded-full bg-[#1E1E1E] px-4 py-2 shadow-sm" id="current-time">

    <div id="digital-clock" class="flex items-center space-x-1">
        <!-- Hours -->
        <div class="digit">
            <div class="segment-a"></div>
            <div class="segment-b"></div>
            <div class="segment-c"></div>
            <div class="segment-d"></div>
            <div class="segment-e"></div>
            <div class="segment-f"></div>
            <div class="segment-g"></div>
        </div>
        <div class="digit">
            <div class="segment-a"></div>
            <div class="segment-b"></div>
            <div class="segment-c"></div>
            <div class="segment-d"></div>
            <div class="segment-e"></div>
            <div class="segment-f"></div>
            <div class="segment-g"></div>
        </div>
        
        <!-- Colon (:) -->
        <div class="colon flex flex-col justify-center items-center space-y-1">
            <div class="colon-dot bg-[#FF5722] w-2 h-2 rounded-full"></div>
            <div class="colon-dot bg-[#FF5722] w-2 h-2 rounded-full"></div>
        </div>
        
        <!-- Minutes -->
        <div class="digit">
            <div class="segment-a"></div>
            <div class="segment-b"></div>
            <div class="segment-c"></div>
            <div class="segment-d"></div>
            <div class="segment-e"></div>
            <div class="segment-f"></div>
            <div class="segment-g"></div>
        </div>
        <div class="digit">
            <div class="segment-a"></div>
            <div class="segment-b"></div>
            <div class="segment-c"></div>
            <div class="segment-d"></div>
            <div class="segment-e"></div>
            <div class="segment-f"></div>
            <div class="segment-g"></div>
        </div>
        
        <!-- AM/PM -->
        <div class="ampm text-[#FF5722] ml-1 text-sm font-mono"></div>
    </div>
</div>

        <!-- Right Section: Search, Feedback and Notifications -->
        <div class="flex items-center space-x-2">


        </div>
    </header>
        
</nav>
<script>
    // Define which segments are ON for each digit (0-9)
    const digitSegments = [
        [1, 1, 1, 1, 1, 1, 0], // 0
        [0, 1, 1, 0, 0, 0, 0], // 1
        [1, 1, 0, 1, 1, 0, 1], // 2
        [1, 1, 1, 1, 0, 0, 1], // 3
        [0, 1, 1, 0, 0, 1, 1], // 4
        [1, 0, 1, 1, 0, 1, 1], // 5
        [1, 0, 1, 1, 1, 1, 1], // 6
        [1, 1, 1, 0, 0, 0, 0], // 7
        [1, 1, 1, 1, 1, 1, 1], // 8
        [1, 1, 1, 1, 0, 1, 1]  // 9
    ];

    function updateDigit(digitElement, number) {
        const segments = digitElement.querySelectorAll('.segment-a, .segment-b, .segment-c, .segment-d, .segment-e, .segment-f, .segment-g');
        digitSegments[number].forEach((isOn, index) => {
            segments[index].style.opacity = isOn ? '1' : '0.1';
        });
    }

    function updateTime() {
        const now = new Date();
        const hours = now.getHours();
        const minutes = now.getMinutes();
        const ampm = hours >= 12 ? 'PM' : 'AM';
        const formattedHours = (hours % 12) || 12; // 12-hour format
        
        // Pad with leading zero (e.g., "05" instead of "5")
        const hourStr = formattedHours.toString().padStart(2, '0');
        const minuteStr = minutes.toString().padStart(2, '0');
        
        // Update each digit
        const digits = document.querySelectorAll('#digital-clock .digit');
        updateDigit(digits[0], parseInt(hourStr[0])); // First hour digit
        updateDigit(digits[1], parseInt(hourStr[1])); // Second hour digit
        updateDigit(digits[2], parseInt(minuteStr[0])); // First minute digit
        updateDigit(digits[3], parseInt(minuteStr[1])); // Second minute digit
        
        // Update AM/PM
        document.querySelector('.ampm').textContent = ampm;
    }

    // Initialize and update every second
    updateTime();
    setInterval(updateTime, 1000);
</script>