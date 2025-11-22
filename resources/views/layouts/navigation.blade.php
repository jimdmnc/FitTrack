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
<div class="hidden md:flex items-center space-x-2 text-lg font-semibold text-gray-200 rounded-md bg-[#1E1E1E] px-4 py-2 shadow-sm" id="current-time">

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
            <!-- Member Expiration Notification -->
            <div x-data="{ open: false }" class="relative">
                <button 
                    @click="open = !open"
                    class="relative p-2 text-gray-200 hover:text-[#FF5722] transition-all duration-300 rounded-full hover:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-[#FF5722] focus:ring-offset-2 focus:ring-offset-[#121212]"
                    aria-label="Member expiration notifications"
                >
                    <!-- Bell Icon -->
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                    </svg>
                    
                    <!-- Badge with total count -->
                    @if(($expiringMembersCount ?? 0) > 0 || ($expiredMembersCount ?? 0) > 0)
                        <span class="absolute top-0 right-0 inline-flex items-center justify-center px-2 py-1 text-xs font-bold leading-none text-white transform translate-x-1/2 -translate-y-1/2 bg-red-600 rounded-full">
                            {{ ($expiringMembersCount ?? 0) + ($expiredMembersCount ?? 0) }}
                        </span>
                    @endif
                </button>

                <!-- Dropdown Menu -->
                <div 
                    x-show="open"
                    @click.away="open = false"
                    x-transition:enter="transition ease-out duration-100"
                    x-transition:enter-start="transform opacity-0 scale-95"
                    x-transition:enter-end="transform opacity-100 scale-100"
                    x-transition:leave="transition ease-in duration-75"
                    x-transition:leave-start="transform opacity-100 scale-100"
                    x-transition:leave-end="transform opacity-0 scale-95"
                    class="absolute right-0 mt-2 w-96 bg-[#1E1E1E] rounded-lg shadow-xl border border-gray-700 z-50"
                    style="display: none;"
                >
                    <div class="p-4 max-h-96 overflow-y-auto">
                        <h3 class="text-lg font-semibold text-gray-200 mb-3 border-b border-gray-700 pb-2">
                            Notifications
                        </h3>
                        
                        <!-- Expiring Members (7 days) -->
                        @if(($expiringMembersCount ?? 0) > 0)
                            <div class="mb-4">
                                <div class="flex items-center justify-between mb-3">
                                    <span class="text-sm font-medium text-yellow-400 flex items-center">
                                        <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                        </svg>
                                        Expiring Soon
                                    </span>
                                    <span class="text-sm font-bold text-yellow-400 bg-yellow-400/20 px-2 py-1 rounded">
                                        {{ $expiringMembersCount }}
                                    </span>
                                </div>
                                
                                <!-- List of Expiring Members -->
                                <div class="space-y-2 max-h-48 overflow-y-auto">
                                    @foreach($expiringMembers ?? [] as $member)
                                        @php
                                            $firstName = isset($member) && $member ? ($member->first_name ?? '') : '';
                                            $lastName = isset($member) && $member ? ($member->last_name ?? '') : '';
                                            $endDate = isset($member) && $member ? ($member->end_date ?? null) : null;
                                            $initials = strtoupper(substr($firstName, 0, 1) ?: '?') . strtoupper(substr($lastName, 0, 1) ?: '?');
                                        @endphp
                                        <div class="bg-gray-800/50 rounded-lg p-2 border-l-2 border-yellow-400">
                                            <div class="flex items-center justify-between">
                                                <div class="flex-1 min-w-0">
                                                    <div class="flex items-center space-x-2">
                                                        <div class="h-8 w-8 flex-shrink-0 rounded-full bg-yellow-400/20 flex items-center justify-center">
                                                            <span class="text-yellow-400 text-xs font-semibold">
                                                                {{ $initials }}
                                                            </span>
                                                        </div>
                                                        <div class="flex-1 min-w-0">
                                                            <p class="text-sm font-medium text-gray-200 truncate">
                                                                {{ $firstName }} {{ $lastName }}
                                                            </p>
                                                            <p class="text-xs text-gray-400">
                                                                Expires: {{ $endDate ? \Carbon\Carbon::parse($endDate)->format('M d, Y') : 'N/A' }}
                                                            </p>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        <!-- Expired Members -->
                        @if(($expiredMembersCount ?? 0) > 0)
                            <div>
                                <div class="flex items-center justify-between mb-3">
                                    <span class="text-sm font-medium text-red-400 flex items-center">
                                        <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                                        </svg>
                                        Expired Members
                                    </span>
                                    <span class="text-sm font-bold text-red-400 bg-red-400/20 px-2 py-1 rounded">
                                        {{ $expiredMembersCount }}
                                    </span>
                                </div>
                                
                                <!-- List of Expired Members -->
                                <div class="space-y-2 max-h-48 overflow-y-auto">
                                    @foreach($expiredMembers ?? [] as $member)
                                        @php
                                            $firstName = isset($member) && $member ? ($member->first_name ?? '') : '';
                                            $lastName = isset($member) && $member ? ($member->last_name ?? '') : '';
                                            $endDate = isset($member) && $member ? ($member->end_date ?? null) : null;
                                            $initials = strtoupper(substr($firstName, 0, 1) ?: '?') . strtoupper(substr($lastName, 0, 1) ?: '?');
                                        @endphp
                                        <div class="bg-gray-800/50 rounded-lg p-2 border-l-2 border-red-400">
                                            <div class="flex items-center justify-between">
                                                <div class="flex-1 min-w-0">
                                                    <div class="flex items-center space-x-2">
                                                        <div class="h-8 w-8 flex-shrink-0 rounded-full bg-red-400/20 flex items-center justify-center">
                                                            <span class="text-red-400 text-xs font-semibold">
                                                                {{ $initials }}
                                                            </span>
                                                        </div>
                                                        <div class="flex-1 min-w-0">
                                                            <p class="text-sm font-medium text-gray-200 truncate">
                                                                {{ $firstName }} {{ $lastName }}
                                                            </p>
                                                            <p class="text-xs text-gray-400">
                                                                Expired: {{ $endDate ? \Carbon\Carbon::parse($endDate)->format('M d, Y') : 'N/A' }}
                                                            </p>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        @if(($expiringMembersCount ?? 0) == 0 && ($expiredMembersCount ?? 0) == 0)
                            <div class="text-center py-4">
                                <p class="text-sm text-gray-400">No expiration alerts at this time</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
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