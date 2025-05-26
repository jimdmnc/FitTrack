<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Attendance Calendar</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body class="bg-gray-900 min-h-screen">

   <!-- Navigation Bar -->
   <nav class="bg-black text-gray-200 py-3 px-4 md:px-6 sticky top-0 z-50">
        <div class="container mx-auto">
            <!-- Alerts for Success and Error messages -->
            @if(session('success'))
                <div class="alert-banner success-alert mb-2 p-3 bg-green-100 border-l-4 border-green-500 text-green-700 rounded">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                        </svg>
                        <span>{{ session('success') }}</span>
                    </div>
                </div>
            @endif

            @if(session('error'))
                <div class="alert-banner error-alert mb-2 p-3 bg-red-100 border-l-4 border-red-500 text-red-700 rounded">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                        </svg>
                        <span>{{ session('error') }}</span>
                    </div>
                </div>
            @endif

            <!-- Main Navigation Content -->
            <div class="flex justify-between items-center">
                <!-- Logo Image -->
                <div class="flex items-center">
                    <a href="{{ route('self.landing') }}" aria-label="FitTrack Homepage">
                        <img src="{{ asset('images/rockiesLogo.jpg') }}" alt="FitTrack Logo" class="h-10 w-10 sm:h-12 sm:w-12 md:h-16 md:w-16 rounded-full object-cover" loading="lazy">
                    </a>
                </div>
                @if(Auth::user()->role === 'userSession')
                    <!-- Workout Timer (Desktop) -->
                    @if(auth()->check() && auth()->user()->rfid_uid && isset($attendance) && !$attendance->time_out && !session('timed_out'))
                        <div class="workout-timer flex items-center bg-gray-800 px-3 py-1 rounded-full">
                            <i class="fas fa-stopwatch mr-2 text-red-400"></i>
                            <span class="timer-text text-sm md:text-base" id="workout-duration">00:00:00</span>
                        </div>
                    @endif
                    <!-- Time Out Button (Desktop and Mobile) -->
                    @if(!session('timed_out') && isset($attendance) && !$attendance->time_out)
                        <!-- Desktop Timeout Button -->
                        <button
                            id="timeout-button"
                            onclick="document.getElementById('timeout-modal').showModal()"
                            class="hidden md:inline-flex bg-red-600 text-gray-200 hover:bg-red-700 font-bold py-2 px-6 rounded-lg shadow-md transition duration-300 min-h-[44px]"
                        >
                            <i class="fas fa-sign-out-alt mr-2"></i> Time Out
                        </button>

                        <!-- Mobile Timeout Button -->
                        <button
                            onclick="document.getElementById('timeout-modal').showModal()"
                            class="inline-flex md:hidden items-center justify-center bg-red-600 hover:bg-red-700 text-white font-medium p-2 rounded-full text-sm transition duration-300 min-h-[44px] min-w-[44px]"
                        >
                            <i class="fas fa-sign-out-alt"></i>
                        </button>
                    @endif
                @endif

                <!-- Desktop Navigation Links -->
                <div class="hidden md:flex items-center space-x-4 lg:space-x-6">
                    <a href="{{ route('self.landingProfile') }}#home" class="nav-link font-medium hover:text-red-400 transition duration-300 text-sm lg:text-base">Home</a>
                    <a href="{{ route('self.landingProfile') }}#inhere" class="nav-link font-medium hover:text-red-400 transition duration-300 text-sm lg:text-base">In Here</a>
                    <a href="{{ route('self.userAttendance') }}" class="nav-link font-medium hover:text-red-400 transition duration-300 text-sm lg:text-base">Attendance</a>
                    <a href="javascript:void(0)" onclick="showProfile()" class="nav-link font-medium hover:text-red-400 transition duration-300 text-sm lg:text-base">Profile</a>
                    
                    <!-- Action Buttons -->
                    <div class="flex items-center space-x-2">
                    @if(Auth::user()->role === 'userSession')

                        <button type="button" onclick="checkRenewalEligibility()"
                            class="bg-green-600 hover:bg-green-700 text-white font-medium py-2 px-3 rounded-full text-sm flex items-center transition duration-300 min-h-[44px]">
                            <i class="fas fa-sync-alt mr-1"></i> Renew
                        </button>
                        @endif

                        <form method="POST" action="{{ route('logout.custom') }}">
                            @csrf
                            <button type="submit"
                                class="bg-gray-700 hover:bg-gray-800 text-white font-medium py-2 px-3 rounded-full text-sm flex items-center transition duration-300 min-h-[44px]">
                                <i class="fas fa-door-open mr-1"></i> Sign Out
                            </button>
                        </form>
                    </div>
                </div>

                <!-- Mobile Menu Button -->
                <div class="md:hidden flex items-center space-x-3">
                    <button id="mobile-menu-button" class="text-gray-200 p-1 focus:outline-none bg-gray-800 rounded-md min-h-[44px] min-w-[44px]" aria-label="Toggle mobile menu" aria-expanded="false">
                        <i class="fas fa-bars text-xl"></i>
                    </button>
                </div>
            </div>

            <!-- Mobile Menu -->
            <div id="mobile-menu" class="md:hidden hidden fixed inset-0 bg-black bg-opacity-95 z-50 flex flex-col">
                <div class="container mx-auto px-4 py-8 flex flex-col h-full">
                    <div class="flex justify-end mb-6">
                        <button id="close-mobile-menu" class="text-gray-300 hover:text-white min-h-[44px] min-w-[44px]" aria-label="Close mobile menu">
                            <i class="fas fa-times text-2xl"></i>
                        </button>
                    </div>
                    
                    <div class="flex flex-col space-y-6 text-center flex-grow">
                        <a href="{{ route('self.landingProfile') }}#home" class="py-3 text-xl font-medium hover:text-red-400 transition duration-300">Home</a>
                        <a href="{{ route('self.landingProfile') }}#inhere" class="py-3 text-xl font-medium hover:text-red-400 transition duration-300">About Us</a>
                        <a href="{{ route('self.userAttendance') }}" class="py-3 text-xl font-medium hover:text-red-400 transition duration-300">Attendance</a>
                        <a href="javascript:void(0)" onclick="showProfile(); closeMobileMenu();" class="py-3 text-xl font-medium hover:text-red-400 transition duration-300">Profile</a>
                        
                        @if(Auth::user()->role === 'userSession')
                            @if(auth()->check() && auth()->user()->rfid_uid && isset($attendance) && !$attendance->time_out && !session('timed_out'))
                                <div class="flex justify-center items-center py-4">
                                    <div class="flex items-center bg-gray-800 px-4 py-2 rounded-lg">
                                        <i class="fas fa-stopwatch mr-3 text-red-400 text-lg"></i>
                                        <span id="mobile-workout-duration" class="text-lg font-medium">
                                            @if(isset($attendance))
                                                {{ gmdate('H:i:s', strtotime(now()) - strtotime($attendance->time_in)) }}
                                            @else
                                                00:00:00
                                            @endif
                                        </span>
                                    </div>
                                </div>
                            @endif
                        @endif
                    </div>
                    
                    <div class="grid grid-cols-2 gap-4 mt-6">
                        @if(Auth::user()->role === 'userSession')
                            <button type="button" onclick="checkRenewalEligibility(); closeMobileMenu();"
                                class="bg-green-600 hover:bg-green-700 text-white font-medium py-3 px-4 rounded-lg flex items-center justify-center transition duration-300 min-h-[44px]">
                                <i class="fas fa-sync-alt mr-2"></i> Renew
                            </button>
                        @endif
                        <form method="POST" action="{{ route('logout.custom') }}" class="w-full">
                            @csrf
                            <button type="submit"
                                class="w-full bg-gray-700 hover:bg-gray-800 text-white font-medium py-3 px-4 rounded-lg flex items-center justify-center transition duration-300 min-h-[44px]">
                                <i class="fas fa-door-open mr-2"></i> Sign Out
                            </button>
                        </form>
                    </div>
                </div>
            </div>
    </nav>
    <div class="container mx-auto px-4 py-8" x-data="{
        selectedAttendance: {
            user: {
                first_name: '{{ Auth::user()->first_name }}',
                last_name: '{{ Auth::user()->last_name }}',
                membership_type: '{{ Auth::user()->getMembershipType() }}',
                attendances: {{ json_encode(Auth::user()->attendances->map(function($a) {
                    return [
                        'time_in' => $a->time_in->toISOString(),
                        'time_out' => $a->time_out ? $a->time_out->toISOString() : null,
                        'formatted_duration' => $a->formatted_duration ?? 'N/A'
                    ];
                })) }}
            }
        },
        selectedDayDate: '',
        selectedDayCheckIn: 'N/A',
        selectedDayCheckOut: 'N/A',
        selectedDayDuration: 'N/A',
        currentMonth: new Date().getMonth(),
        currentYear: new Date().getFullYear(),
        today: new Date().getDate(),
        selectedDay: null,

        getDaysInMonth() {
            return new Date(this.currentYear, this.currentMonth + 1, 0).getDate();
        },

        getFirstDayOfMonth() {
            return new Date(this.currentYear, this.currentMonth, 1).getDay();
        },

        prevMonth() {
            if (this.currentMonth === 0) {
                this.currentMonth = 11;
                this.currentYear--;
            } else {
                this.currentMonth--;
            }
            this.selectedDay = null;
        },

        nextMonth() {
            if (this.currentMonth === 11) {
                this.currentMonth = 0;
                this.currentYear++;
            } else {
                this.currentMonth++;
            }
            this.selectedDay = null;
        },

        monthName() {
            return new Date(this.currentYear, this.currentMonth).toLocaleString('default', { month: 'long' });
        },

        isAttendanceDay(day) {
            if (!this.selectedAttendance || !this.selectedAttendance.user || !this.selectedAttendance.user.attendances) {
                return false;
            }
            const currentDate = new Date(this.currentYear, this.currentMonth, day);
            return this.selectedAttendance.user.attendances.some(attendance => {
                const attendanceDate = new Date(attendance.time_in);
                return attendanceDate.getFullYear() === currentDate.getFullYear() &&
                    attendanceDate.getMonth() === currentDate.getMonth() &&
                    attendanceDate.getDate() === currentDate.getDate();
            });
        },

        isToday(day) {
            const today = new Date();
            return day === today.getDate() &&
                this.currentMonth === today.getMonth() &&
                this.currentYear === today.getFullYear();
        },

        selectDay(day) {
            this.selectedDay = day;
            this.loadDayAttendance(day);
        },

        loadDayAttendance(day) {
            const selectedDate = new Date(this.currentYear, this.currentMonth, day);
            const formattedDate = selectedDate.toLocaleDateString([], { month: 'short', day: 'numeric', year: 'numeric' });
            
            let checkIn = 'N/A';
            let checkOut = 'N/A';
            let duration = 'N/A';
            
            if (this.selectedAttendance && this.selectedAttendance.user && this.selectedAttendance.user.attendances) {
                const attendanceForDay = this.selectedAttendance.user.attendances.find(attendance => {
                    const attendanceDate = new Date(attendance.time_in);
                    return attendanceDate.getFullYear() === selectedDate.getFullYear() &&
                        attendanceDate.getMonth() === selectedDate.getMonth() &&
                        attendanceDate.getDate() === selectedDate.getDate();
                });

                if (attendanceForDay) {
                    checkIn = new Date(attendanceForDay.time_in).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
                    checkOut = attendanceForDay.time_out ?
                        new Date(attendanceForDay.time_out).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' }) : 'N/A';
                    duration = attendanceForDay.formatted_duration || 'N/A';
                }
            }
            
            this.selectedDayDate = formattedDate;
            this.selectedDayCheckIn = checkIn;
            this.selectedDayCheckOut = checkOut;
            this.selectedDayDuration = duration;
        },

        isSelectedDay(day) {
            return this.selectedDay === day;
        }
    }" x-init="$nextTick(() => { selectDay(today); })">
        <div class="bg-[#1e1e1e] rounded-lg shadow-lg p-6 max-w-2xl mx-auto">
            <!-- Attendance Details -->
            <div class="mb-6">
                <h2 class="text-2xl font-bold bg-clip-text text-transparent bg-gradient-to-r from-red-600 to-orange-600 mb-4">Attendance Calendar</h2>
                <div class="flex items-center space-x-4 mb-4">
                    <div class="flex-shrink-0 h-12 w-12 rounded-full bg-gray-200 flex items-center justify-center text-gray-600 font-medium text-lg">
                        <span x-text="selectedAttendance ? selectedAttendance.user.first_name.charAt(0) + selectedAttendance.user.last_name.charAt(0) : ''"></span>
                    </div>
                    <div>
                        <h3 class="text-lg font-medium text-gray-200" x-text="selectedAttendance ? selectedAttendance.user.first_name + ' ' + selectedAttendance.user.last_name : ''"></h3>
                        <p class="text-sm text-gray-400" x-text="selectedAttendance ? selectedAttendance.user.membership_type : ''"></p>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4 mb-4">
                    <div>
                        <p class="text-sm text-gray-400">Date</p>
                        <p class="text-gray-200" x-text="selectedDayDate"></p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-400">Duration</p>
                        <p class="text-gray-200" x-text="selectedDayDuration"></p>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4 mb-4">
                    <div>
                        <p class="text-sm text-gray-400">Check-in</p>
                        <p class="text-gray-200" x-text="selectedDayCheckIn"></p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-400">Check-out</p>
                        <p class="text-gray-200" x-text="selectedDayCheckOut"></p>
                    </div>
                </div>
            </div>

            <!-- Calendar -->
            <div>
                <!-- Calendar Navigation -->
                <div class="flex justify-between items-center mb-4">
                    <button @click="prevMonth" class="text-gray-400 hover:text-[#ff5722]">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                        </svg>
                    </button>
                    <h3 class="text-lg font-medium text-gray-200" x-text="monthName() + ' ' + currentYear"></h3>
                    <button @click="nextMonth" class="text-gray-400 hover:text-[#ff5722]">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </button>
                </div>

                <!-- Calendar Grid -->
                <div class="grid grid-cols-7 gap-2 text-center text-sm">
                    <!-- Day headers -->
                    <template x-for="day in ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat']" :key="day">
                        <div class="text-sm text-gray-400 font-medium" x-text="day"></div>
                    </template>

                    <!-- Empty days from previous month -->
                    <template x-for="i in getFirstDayOfMonth()" :key="'empty-' + i">
                        <div class="p-2 text-sm text-gray-600"></div>
                    </template>

                    <!-- Days of current month -->
                    <template x-for="day in getDaysInMonth()" :key="'day-' + day">
                        <div class="relative flex flex-col items-center p-2">
                            <button 
                                @click="selectDay(day)" 
                                class="text-sm rounded-full w-8 h-8 flex items-center justify-center transition-colors focus:outline-none"
                                :class="{
                                    'text-gray-300 hover:bg-gray-700': !isSelectedDay(day),
                                    'text-white bg-[#ff5722]': isSelectedDay(day),
                                    'font-extrabold': isToday(day)
                                }"
                                x-text="day"
                            ></button>
                            <!-- Attendance indicator dot -->
                            <div x-show="isAttendanceDay(day) && !isSelectedDay(day)" class="mt-1 w-1.5 h-1.5 bg-[#ff5722] rounded-full"></div>
                        </div>
                    </template>
                </div>
            </div>
        </div>
    </div>
        <script>
 document.addEventListener('DOMContentLoaded', function() {
                initNavigation();
                initProfile();
               
            });

             function initNavigation() {
                const mobileMenuButton = document.getElementById('mobile-menu-button');
                const mobileMenu = document.getElementById('mobile-menu');
                const closeMobileMenuButton = document.getElementById('close-mobile-menu');

                if (mobileMenuButton && mobileMenu) {
                    mobileMenuButton.addEventListener('click', () => {
                        mobileMenu.classList.remove('hidden');
                        mobileMenuButton.setAttribute('aria-expanded', 'true');
                    });
                }

                if (closeMobileMenuButton && mobileMenu) {
                    closeMobileMenuButton.addEventListener('click', () => {
                        closeMobileMenu();
                    });
                }

                mobileMenu?.querySelectorAll('a').forEach(link => {
                    link.addEventListener('click', () => {
                        closeMobileMenu();
                    });
                });
            }

            function closeMobileMenu() {
                const mobileMenu = document.getElementById('mobile-menu');
                const mobileMenuButton = document.getElementById('mobile-menu-button');
                if (mobileMenu) {
                    mobileMenu.classList.add('hidden');
                    if (mobileMenuButton) {
                        mobileMenuButton.setAttribute('aria-expanded', 'false');
                    }
                }
            }

            function initProfile() {
                const profileModal = document.getElementById('profile-modal');
                if (!profileModal) return;

                window.showProfile = function() {
                    profileModal.classList.remove('hidden');
                    setTimeout(() => {
                        profileModal.classList.add('active');
                        profileModal.classList.remove('opacity-0', 'invisible');
                    }, 10);
                };

                window.hideProfile = function() {
                    profileModal.classList.remove('active');
                    profileModal.classList.add('opacity-0', 'invisible');
                    setTimeout(() => {
                        profileModal.classList.add('hidden');
                    }, 300);
                };
            }
       </script>
</body>
</html>