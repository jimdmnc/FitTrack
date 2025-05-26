<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Attendance Calendar</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');
        
        body {
            font-family: 'Inter', sans-serif;
        }
        
        .gradient-bg {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        
        .glass-effect {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
        
        .hover-lift {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        
        .hover-lift:hover {
            transform: translateY(-2px);
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.2), 0 10px 10px -5px rgba(0, 0, 0, 0.1);
        }
        
        .calendar-day {
            transition: all 0.2s ease-in-out;
        }
        
        .calendar-day:hover {
            transform: scale(1.1);
        }
        
        .pulse-dot {
            animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
        }
        
        @keyframes pulse {
            0%, 100% {
                opacity: 1;
            }
            50% {
                opacity: 0.5;
            }
        }
        
        .floating-animation {
            animation: float 6s ease-in-out infinite;
        }
        
        @keyframes float {
            0%, 100% {
                transform: translateY(0px);
            }
            50% {
                transform: translateY(-10px);
            }
        }
        
        .stat-card {
            background: linear-gradient(145deg, #2d3748, #1a202c);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }
        
        .calendar-grid {
            background: linear-gradient(145deg, #2d3748, #1a202c);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }
        
        .nav-button {
            background: linear-gradient(145deg, #4a5568, #2d3748);
            border: 1px solid rgba(255, 255, 255, 0.1);
            transition: all 0.3s ease;
        }
        
        .nav-button:hover {
            background: linear-gradient(145deg, #ff5722, #e64a19);
            transform: scale(1.05);
        }
        
        .selected-day {
            background: linear-gradient(145deg, #ff5722, #e64a19);
            box-shadow: 0 0 20px rgba(255, 87, 34, 0.4);
        }
        
        .today-indicator {
            position: relative;
        }
        
        .today-indicator::after {
            content: '';
            position: absolute;
            bottom: -2px;
            left: 50%;
            transform: translateX(-50%);
            width: 4px;
            height: 4px;
            background: #10b981;
            border-radius: 50%;
            animation: pulse 2s infinite;
        }
        
        .attendance-dot {
            background: linear-gradient(145deg, #ff5722, #e64a19);
            box-shadow: 0 0 10px rgba(255, 87, 34, 0.6);
        }
    </style>
</head>
<body class="bg-gradient-to-br from-gray-900 via-gray-800 to-gray-900 min-h-screen">

   <!-- Navigation Bar -->
   <nav class="bg-gradient-to-r from-black via-gray-900 to-black text-gray-200 py-3 px-4 md:px-6 sticky top-0 z-50 backdrop-blur-sm border-b border-gray-700">
        <div class="container mx-auto">
            <!-- Alerts for Success and Error messages -->
            @if(session('success'))
                <div class="alert-banner success-alert mb-2 p-3 bg-gradient-to-r from-green-500 to-emerald-500 text-white rounded-lg shadow-lg">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                        </svg>
                        <span>{{ session('success') }}</span>
                    </div>
                </div>
            @endif

            @if(session('error'))
                <div class="alert-banner error-alert mb-2 p-3 bg-gradient-to-r from-red-500 to-rose-500 text-white rounded-lg shadow-lg">
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
                    <a href="{{ route('self.landing') }}" aria-label="FitTrack Homepage" class="hover-lift">
                        <img src="{{ asset('images/rockiesLogo.jpg') }}" alt="FitTrack Logo" class="h-10 w-10 sm:h-12 sm:w-12 md:h-16 md:w-16 rounded-full object-cover shadow-lg border-2 border-gray-600" loading="lazy">
                    </a>
                </div>
                @if(Auth::user()->role === 'userSession')
                    <!-- Workout Timer (Desktop) -->
                    @if(auth()->check() && auth()->user()->rfid_uid && isset($attendance) && !$attendance->time_out && !session('timed_out'))
                        <div class="workout-timer flex items-center glass-effect px-4 py-2 rounded-full shadow-lg">
                            <i class="fas fa-stopwatch mr-2 text-red-400 pulse-dot"></i>
                            <span class="timer-text text-sm md:text-base font-medium" id="workout-duration">00:00:00</span>
                        </div>
                    @endif
                    <!-- Time Out Button (Desktop and Mobile) -->
                    @if(!session('timed_out') && isset($attendance) && !$attendance->time_out)
                        <!-- Desktop Timeout Button -->
                        <button
                            id="timeout-button"
                            onclick="document.getElementById('timeout-modal').showModal()"
                            class="hidden md:inline-flex bg-gradient-to-r from-red-600 to-red-700 text-white hover:from-red-700 hover:to-red-800 font-bold py-2 px-6 rounded-lg shadow-lg transition duration-300 min-h-[44px] hover-lift"
                        >
                            <i class="fas fa-sign-out-alt mr-2"></i> Time Out
                        </button>

                        <!-- Mobile Timeout Button -->
                        <button
                            onclick="document.getElementById('timeout-modal').showModal()"
                            class="inline-flex md:hidden items-center justify-center bg-gradient-to-r from-red-600 to-red-700 hover:from-red-700 hover:to-red-800 text-white font-medium p-2 rounded-full text-sm transition duration-300 min-h-[44px] min-w-[44px] shadow-lg hover-lift"
                        >
                            <i class="fas fa-sign-out-alt"></i>
                        </button>
                    @endif
                @endif

                <!-- Desktop Navigation Links -->
                <div class="hidden md:flex items-center space-x-4 lg:space-x-6">
                    <a href="{{ route('self.landingProfile') }}#home" class="nav-link font-medium hover:text-red-400 transition duration-300 text-sm lg:text-base relative group">
                        Home
                        <span class="absolute -bottom-1 left-0 w-0 h-0.5 bg-red-400 transition-all duration-300 group-hover:w-full"></span>
                    </a>
                    <a href="{{ route('self.landingProfile') }}#inhere" class="nav-link font-medium hover:text-red-400 transition duration-300 text-sm lg:text-base relative group">
                        In Here
                        <span class="absolute -bottom-1 left-0 w-0 h-0.5 bg-red-400 transition-all duration-300 group-hover:w-full"></span>
                    </a>
                    <a href="{{ route('self.userAttendance') }}" class="nav-link font-medium text-red-400 transition duration-300 text-sm lg:text-base relative">
                        Attendance
                        <span class="absolute -bottom-1 left-0 w-full h-0.5 bg-red-400"></span>
                    </a>
                    <a href="javascript:void(0)" onclick="showProfile()" class="nav-link font-medium hover:text-red-400 transition duration-300 text-sm lg:text-base relative group">
                        Profile
                        <span class="absolute -bottom-1 left-0 w-0 h-0.5 bg-red-400 transition-all duration-300 group-hover:w-full"></span>
                    </a>
                    
                    <!-- Action Buttons -->
                    <div class="flex items-center space-x-2">
                    @if(Auth::user()->role === 'userSession')
                        <button type="button" onclick="checkRenewalEligibility()"
                            class="bg-gradient-to-r from-green-600 to-green-700 hover:from-green-700 hover:to-green-800 text-white font-medium py-2 px-3 rounded-full text-sm flex items-center transition duration-300 min-h-[44px] shadow-lg hover-lift">
                            <i class="fas fa-sync-alt mr-1"></i> Renew
                        </button>
                        @endif

                        <form method="POST" action="{{ route('logout.custom') }}">
                            @csrf
                            <button type="submit"
                                class="bg-gradient-to-r from-gray-700 to-gray-800 hover:from-gray-800 hover:to-gray-900 text-white font-medium py-2 px-3 rounded-full text-sm flex items-center transition duration-300 min-h-[44px] shadow-lg hover-lift">
                                <i class="fas fa-door-open mr-1"></i> Sign Out
                            </button>
                        </form>
                    </div>
                </div>

                <!-- Mobile Menu Button -->
                <div class="md:hidden flex items-center space-x-3">
                    <button id="mobile-menu-button" class="text-gray-200 p-1 focus:outline-none glass-effect rounded-md min-h-[44px] min-w-[44px] hover-lift" aria-label="Toggle mobile menu" aria-expanded="false">
                        <i class="fas fa-bars text-xl"></i>
                    </button>
                </div>
            </div>

            <!-- Mobile Menu -->
            <div id="mobile-menu" class="md:hidden hidden fixed inset-0 bg-black bg-opacity-95 z-50 flex flex-col backdrop-blur-sm">
                <div class="container mx-auto px-4 py-8 flex flex-col h-full">
                    <div class="flex justify-end mb-6">
                        <button id="close-mobile-menu" class="text-gray-300 hover:text-white min-h-[44px] min-w-[44px] hover-lift" aria-label="Close mobile menu">
                            <i class="fas fa-times text-2xl"></i>
                        </button>
                    </div>
                    
                    <div class="flex flex-col space-y-6 text-center flex-grow">
                        <a href="{{ route('self.landingProfile') }}#home" class="py-3 text-xl font-medium hover:text-red-400 transition duration-300">Home</a>
                        <a href="{{ route('self.landingProfile') }}#inhere" class="py-3 text-xl font-medium hover:text-red-400 transition duration-300">About Us</a>
                        <a href="{{ route('self.userAttendance') }}" class="py-3 text-xl font-medium text-red-400 transition duration-300">Attendance</a>
                        <a href="javascript:void(0)" onclick="showProfile(); closeMobileMenu();" class="py-3 text-xl font-medium hover:text-red-400 transition duration-300">Profile</a>
                        
                        @if(Auth::user()->role === 'userSession')
                            @if(auth()->check() && auth()->user()->rfid_uid && isset($attendance) && !$attendance->time_out && !session('timed_out'))
                                <div class="flex justify-center items-center py-4">
                                    <div class="flex items-center glass-effect px-4 py-2 rounded-lg shadow-lg">
                                        <i class="fas fa-stopwatch mr-3 text-red-400 text-lg pulse-dot"></i>
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
                                class="bg-gradient-to-r from-green-600 to-green-700 hover:from-green-700 hover:to-green-800 text-white font-medium py-3 px-4 rounded-lg flex items-center justify-center transition duration-300 min-h-[44px] shadow-lg hover-lift">
                                <i class="fas fa-sync-alt mr-2"></i> Renew
                            </button>
                        @endif
                        <form method="POST" action="{{ route('logout.custom') }}" class="w-full">
                            @csrf
                            <button type="submit"
                                class="w-full bg-gradient-to-r from-gray-700 to-gray-800 hover:from-gray-800 hover:to-gray-900 text-white font-medium py-3 px-4 rounded-lg flex items-center justify-center transition duration-300 min-h-[44px] shadow-lg hover-lift">
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
        <!-- Enhanced Main Content -->
        <div class="max-w-6xl mx-auto">
            <!-- Header Section -->
            <div class="text-center mb-8 floating-animation">
                <h1 class="text-4xl md:text-5xl font-bold bg-gradient-to-r from-red-400 via-orange-500 to-yellow-500 bg-clip-text text-transparent mb-4">
                    <i class="fas fa-calendar-alt mr-3"></i>
                    Attendance Dashboard
                </h1>
                <p class="text-gray-400 text-lg">Track your fitness journey with style</p>
            </div>

            <div class="grid lg:grid-cols-3 gap-8">
                <!-- User Profile Card -->
                <div class="lg:col-span-1">
                    <div class="stat-card rounded-2xl shadow-2xl p-6 hover-lift">
                        <div class="text-center mb-6">
                            <div class="relative inline-block">
                                <div class="w-24 h-24 mx-auto rounded-full bg-gradient-to-r from-red-500 to-orange-500 flex items-center justify-center text-white font-bold text-2xl shadow-lg">
                                    <span x-text="selectedAttendance ? selectedAttendance.user.first_name.charAt(0) + selectedAttendance.user.last_name.charAt(0) : ''"></span>
                                </div>
                                <div class="absolute -bottom-2 -right-2 w-8 h-8 bg-green-500 rounded-full flex items-center justify-center shadow-lg">
                                    <i class="fas fa-check text-white text-sm"></i>
                                </div>
                            </div>
                            <h3 class="text-xl font-bold text-white mt-4" x-text="selectedAttendance ? selectedAttendance.user.first_name + ' ' + selectedAttendance.user.last_name : ''"></h3>
                            <p class="text-orange-400 font-medium" x-text="selectedAttendance ? selectedAttendance.user.membership_type : ''"></p>
                        </div>

                        <!-- Stats Cards -->
                        <div class="space-y-4">
                            <div class="bg-gradient-to-r from-blue-600 to-purple-600 rounded-lg p-4 text-center">
                                <i class="fas fa-calendar-day text-2xl mb-2"></i>
                                <p class="text-sm opacity-90">Selected Date</p>
                                <p class="font-bold text-lg" x-text="selectedDayDate"></p>
                            </div>
                            
                            <div class="bg-gradient-to-r from-green-600 to-teal-600 rounded-lg p-4 text-center">
                                <i class="fas fa-clock text-2xl mb-2"></i>
                                <p class="text-sm opacity-90">Duration</p>
                                <p class="font-bold text-lg" x-text="selectedDayDuration"></p>
                            </div>
                            
                            <div class="grid grid-cols-2 gap-3">
                                <div class="bg-gradient-to-r from-orange-500 to-red-500 rounded-lg p-3 text-center text-sm">
                                    <i class="fas fa-sign-in-alt mb-1"></i>
                                    <p class="opacity-90">Check-in</p>
                                    <p class="font-bold" x-text="selectedDayCheckIn"></p>
                                </div>
                                <div class="bg-gradient-to-r from-purple-500 to-pink-500 rounded-lg p-3 text-center text-sm">
                                    <i class="fas fa-sign-out-alt mb-1"></i>
                                    <p class="opacity-90">Check-out</p>
                                    <p class="font-bold" x-text="selectedDayCheckOut"></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Calendar Section -->
                <div class="lg:col-span-2">
                    <div class="calendar-grid rounded-2xl shadow-2xl p-6 hover-lift">
                        <!-- Calendar Header -->
                        <div class="flex justify-between items-center mb-6">
                            <button @click="prevMonth" class="nav-button p-3 rounded-full hover-lift">
                                <i class="fas fa-chevron-left text-lg"></i>
                            </button>
                            
                            <div class="text-center">
                                <h3 class="text-2xl font-bold text-white" x-text="monthName() + ' ' + currentYear"></h3>
                                <p class="text-gray-400 text-sm">Click on any date to view details</p>
                            </div>
                            
                            <button @click="nextMonth" class="nav-button p-3 rounded-full hover-lift">
                                <i class="fas fa-chevron-right text-lg"></i>
                            </button>
                        </div>

                        <!-- Legend -->
                        <div class="flex justify-center space-x-6 mb-6 text-sm">
                            <div class="flex items-center">
                                <div class="w-3 h-3 bg-green-500 rounded-full mr-2"></div>
                                <span class="text-gray-300">Today</span>
                            </div>
                            <div class="flex items-center">
                                <div class="w-3 h-3 bg-gradient-to-r from-red-500 to-orange-500 rounded-full mr-2"></div>
                                <span class="text-gray-300">Attendance</span>
                            </div>
                            <div class="flex items-center">
                                <div class="w-3 h-3 bg-gradient-to-r from-red-600 to-orange-600 rounded-full mr-2 shadow-lg"></div>
                                <span class="text-gray-300">Selected</span>
                            </div>
                        </div>

                        <!-- Calendar Grid -->
                        <div class="grid grid-cols-7 gap-3 text-center">
                            <!-- Day headers -->
                            <template x-for="day in ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat']" :key="day">
                                <div class="text-sm text-gray-400 font-semibold py-2 uppercase tracking-wider" x-text="day"></div>
                            </template>

                            <!-- Empty days from previous month -->
                            <template x-for="i in getFirstDayOfMonth()" :key="'empty-' + i">
                                <div class="p-2 text-sm text-gray-600"></div>
                            </template>

                            <!-- Days of current month -->
                            <template x-for="day in getDaysInMonth()" :key="'day-' + day">
                                <div class="relative flex flex-col items-center">
                                    <button 
                                        @click="selectDay(day)" 
                                        class="calendar-day text-sm rounded-full w-12 h-12 flex items-center justify-center font-medium focus:outline-none focus:ring-2 focus:ring-red-400 focus:ring-offset-2 focus:ring-offset-gray-800"
                                        :class="{
                                            'text-gray-300 hover:bg-gray-700 hover:text-white': !isSelectedDay(day) && !isToday(day),
                                            'selected-day text-white font-bold': isSelectedDay(day),
                                            'today-indicator text-white font-bold bg-green-600': isToday(day) && !isSelectedDay(day),
                                            'text-white font-bold': isToday(day)
                                        }"
                                        x-text="day"
                                    ></button>
                                    <!-- Attendance indicator dot -->
                                    <div 
                                        x-show="isAttendanceDay(day) && !isSelectedDay(day)" 
                                        class="mt-1 w-2 h-2 attendance-dot rounded-full pulse-dot"
                                    ></div>
                                </div>
                            </template>
                        </div>

                        <!-- Quick Stats at bottom -->
                        <div class="mt-8 pt-6 border-t border-gray-700">
                            <div class="grid grid-cols-3 gap-4 text-center">
                                <div class="bg-gradient-to-r from-blue-600 to-indigo-600 rounded-lg p-3">
                                    <i class="fas fa-calendar-week text-xl mb-1"></i>
                                    <p class="text-xs opacity-90">This Month</p>
                                    <p class="font-bold" x-text="selectedAttendance ? selectedAttendance.user.attendances.filter(a => {
                                        const date = new Date(a.time_in);
                                        return date.getMonth() === currentMonth && date.getFullYear() === currentYear;
                                    }).length : 0"></p>
                                </div>
                                <div class="bg-gradient-to-r from-purple-600 to-pink-600 rounded-lg p-3">
                                    <i class="fas fa-fire text-xl mb-1"></i>
                                    <p class="text-xs opacity-90">Total Sessions</p>
                                    <p class="font-bold" x-text="selectedAttendance ? selectedAttendance.user.attendances.length : 0"></p>
                                </div>
                                <div class="bg-gradient-to-r from-green-600 to-emerald-600 rounded-lg p-3">
                                    <i class="fas fa-trophy text-xl mb-1"></i>
                                    <p class="text-xs opacity-90">Active Days</p>
                                    <p class="font-bold" x-text="selectedAttendance ? new Set(selectedAttendance.user.attendances.map(a => new Date(a.time_in).toDateString())).size : 0"></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Additional Features Section -->
            <div class="mt-8 grid md:grid-cols-2 gap-6">
                <!-- Monthly Summary Card -->
                <div class="stat-card rounded-2xl shadow-2xl p-6 hover-lift">
                    <div class="flex items-center mb-4">
                        <div class="w-10 h-10 bg-gradient-to-r from-blue-500 to-purple-500 rounded-lg flex items-center justify-center mr-3">
                            <i class="fas fa-chart-line text-white"></i>
                        </div>
                        <h3 class="text-lg font-bold text-white">Monthly Summary</h3>
                    </div>
                    <div class="space-y-3">
                        <div class="flex justify-between items-center">
                            <span class="text-gray-300">Days Attended:</span>
                            <span class="font-bold text-blue-400" x-text="selectedAttendance ? selectedAttendance.user.attendances.filter(a => {
                                const date = new Date(a.time_in);
                                return date.getMonth() === currentMonth && date.getFullYear() === currentYear;
                            }).length : 0"></span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-gray-300">Average Duration:</span>
                            <span class="font-bold text-green-400">2h 15m</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-gray-300">Streak:</span>
                            <span class="font-bold text-orange-400">5 days</span>
                        </div>
                    </div>
                </div>

                <!-- Quick Actions Card -->
                <div class="stat-card rounded-2xl shadow-2xl p-6 hover-lift">
                    <div class="flex items-center mb-4">
                        <div class="w-10 h-10 bg-gradient-to-r from-green-500 to-teal-500 rounded-lg flex items-center justify-center mr-3">
                            <i class="fas fa-bolt text-white"></i>
                        </div>
                        <h3 class="text-lg font-bold text-white">Quick Actions</h3>
                    </div>
                    <div class="space-y-3">
                        <button class="w-full bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700 text-white font-medium py-3 px-4 rounded-lg transition duration-300 hover-lift">
                            <i class="fas fa-download mr-2"></i>
                            Export Data
                        </button>
                        <button class="w-full bg-gradient-to-r from-green-600 to-teal-600 hover:from-green-700 hover:to-teal-700 text-white font-medium py-3 px-4 rounded-lg transition duration-300 hover-lift">
                            <i class="fas fa-share-alt mr-2"></i>
                            Share Progress
                        </button>
                        <button class="w-full bg-gradient-to-r from-orange-600 to-red-600 hover:from-orange-700 hover:to-red-700 text-white font-medium py-3 px-4 rounded-lg transition duration-300 hover-lift">
                            <i class="fas fa-cog mr-2"></i>
                            Settings
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            initNavigation();
            initProfile();
            initAnimations();
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

        function initAnimations() {
            // Add stagger animation to calendar days
            const calendarDays = document.querySelectorAll('.calendar-day');
            calendarDays.forEach((day, index) => {
                day.style.animationDelay = `${index * 0.01}s`;
            });

            // Add intersection observer for scroll animations
            const observerOptions = {
                threshold: 0.1,
                rootMargin: '0px 0px -50px 0px'
            };

            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.style.transform = 'translateY(0)';
                        entry.target.style.opacity = '1';
                    }
                });
            }, observerOptions);

            // Observe all hover-lift elements
            document.querySelectorAll('.hover-lift').forEach(el => {
                el.style.transform = 'translateY(20px)';
                el.style.opacity = '0';
                el.style.transition = 'all 0.6s cubic-bezier(0.4, 0, 0.2, 1)';
                observer.observe(el);
            });
        }

        // Enhanced date selection with smooth transitions
        function enhancedSelectDay(day) {
            const selectedElements = document.querySelectorAll('.selected-day');
            selectedElements.forEach(el => {
                el.classList.add('animate-pulse');
                setTimeout(() => {
                    el.classList.remove('animate-pulse');
                }, 300);
            });
        }

        // Add subtle parallax effect to background
        window.addEventListener('scroll', () => {
            const scrolled = window.pageYOffset;
            const parallax = document.querySelector('body');
            const speed = scrolled * 0.5;
            parallax.style.backgroundPosition = `center ${speed}px`;
        });

        // Add smooth scroll behavior
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });
    </script>
</body>
</html>