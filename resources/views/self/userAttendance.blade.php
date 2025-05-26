<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Attendance Calendar | FitTrack</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap');
        
        body {
            font-family: 'Poppins', sans-serif;
        }
        
        .smooth-transition {
            transition: all 0.3s ease-in-out;
        }
        
        .gradient-text {
            background-clip: text;
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        
        .calendar-day:hover {
            transform: scale(1.05);
        }
        
        .attendance-dot {
            animation: pulse 2s infinite;
        }
        
        @keyframes pulse {
            0% { opacity: 0.7; }
            50% { opacity: 1; }
            100% { opacity: 0.7; }
        }
                /* Enhanced Profile Modal Styles */
                .profile-modal {
            transition: opacity 0.3s ease, visibility 0.3s ease;
        }

        .profile-modal-content {
            transform: translateX(100%);
            transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            box-shadow: -4px 0 15px rgba(0, 0, 0, 0.5);
            width: 380px;
        }

        .profile-modal.active .profile-modal-content {
            transform: translateX(0);
        }

        .profile-header {
            padding: 1.5rem;
        }

        .profile-avatar {
            width: 80px;
            height: 80px;
            border: 3px solid rgba(255, 255, 255, 0.2);
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.3);
            background-color: #2c2c2c;
        }

        .profile-info-item {
            border-bottom: 1px solid #2c2c2c;
            padding: 1rem;
            transition: background-color 0.2s ease;
        }

        .profile-info-item:hover {
            background-color: #252525;
        }

        @media (max-width: 640px) {
            .profile-modal-content {
                width: 80%;
            }
        }
    </style>
</head>
<body class="bg-gray-900 min-h-screen">

   <!-- Navigation Bar -->
   <nav class="bg-black text-gray-200 py-3 px-4 md:px-6 sticky top-0 z-50 shadow-lg">
        <div class="container mx-auto">
            <!-- Alerts for Success and Error messages -->
            @if(session('success'))
                <div class="alert-banner success-alert mb-2 p-3 bg-green-100 border-l-4 border-green-500 text-green-700 rounded flex items-center smooth-transition">
                    <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                    </svg>
                    <span>{{ session('success') }}</span>
                </div>
            @endif

            @if(session('error'))
                <div class="alert-banner error-alert mb-2 p-3 bg-red-100 border-l-4 border-red-500 text-red-700 rounded flex items-center smooth-transition">
                    <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                    </svg>
                    <span>{{ session('error') }}</span>
                </div>
            @endif

            <!-- Main Navigation Content -->
            <div class="flex justify-between items-center">
                <!-- Logo Image -->
                <div class="flex items-center">
                    <a href="{{ route('self.landing') }}" aria-label="FitTrack Homepage" class="hover:opacity-80 smooth-transition">
                        <img src="{{ asset('images/rockiesLogo.jpg') }}" alt="FitTrack Logo" class="h-10 w-10 sm:h-12 sm:w-12 md:h-16 md:w-16 rounded-full object-cover border-2 border-red-500 shadow-md" loading="lazy">
                    </a>
                </div>
                @if(Auth::user()->role === 'userSession')
                    <!-- Workout Timer (Desktop) -->
                    @if(auth()->check() && auth()->user()->rfid_uid && isset($attendance) && !$attendance->time_out && !session('timed_out'))
                        <div class="workout-timer flex items-center bg-gray-800 px-4 py-2 rounded-full shadow-md smooth-transition">
                            <i class="fas fa-stopwatch mr-2 text-red-400"></i>
                            <span class="timer-text text-sm md:text-base font-medium" id="workout-duration">00:00:00</span>
                        </div>
                    @endif
                    <!-- Time Out Button (Desktop and Mobile) -->
                    @if(!session('timed_out') && isset($attendance) && !$attendance->time_out)
                        <!-- Desktop Timeout Button -->
                        <button
                            id="timeout-button"
                            onclick="document.getElementById('timeout-modal').showModal()"
                            class="hidden md:inline-flex bg-red-600 hover:bg-red-700 text-gray-100 font-semibold py-2 px-6 rounded-lg shadow-md smooth-transition min-h-[44px] transform hover:scale-105"
                        >
                            <i class="fas fa-sign-out-alt mr-2"></i> Time Out
                        </button>

                        <!-- Mobile Timeout Button -->
                        <button
                            onclick="document.getElementById('timeout-modal').showModal()"
                            class="inline-flex md:hidden items-center justify-center bg-red-600 hover:bg-red-700 text-white font-medium p-3 rounded-full shadow-md smooth-transition min-h-[44px] min-w-[44px] transform hover:scale-105"
                        >
                            <i class="fas fa-sign-out-alt"></i>
                        </button>
                    @endif
                @endif

                <!-- Desktop Navigation Links -->
                <div class="hidden md:flex items-center space-x-4 lg:space-x-6">
                    <a href="{{ route('self.landingProfile') }}#home" class="nav-link font-medium hover:text-red-400 smooth-transition text-sm lg:text-base">Home</a>
                    <a href="{{ route('self.landingProfile') }}#inhere" class="nav-link font-medium hover:text-red-400 smooth-transition text-sm lg:text-base">In Here</a>
                    <a href="{{ route('self.userAttendance') }}" class="nav-link font-medium text-red-400 border-b-2 border-red-500 smooth-transition text-sm lg:text-base">Attendance</a>
                    <a href="javascript:void(0)" onclick="showProfile()" class="nav-link font-medium hover:text-red-400 smooth-transition text-sm lg:text-base">Profile</a>
                    
                    <!-- Action Buttons -->
                    <div class="flex items-center space-x-2">
                    @if(Auth::user()->role === 'userSession')

                        <button type="button" onclick="checkRenewalEligibility()"
                            class="bg-green-600 hover:bg-green-700 text-white font-medium py-2 px-4 rounded-full shadow-md smooth-transition text-sm flex items-center transform hover:scale-105 min-h-[44px]">
                            <i class="fas fa-sync-alt mr-2"></i> Renew
                        </button>
                        @endif

                        <form method="POST" action="{{ route('logout.custom') }}">
                            @csrf
                            <button type="submit"
                                class="bg-gray-700 hover:bg-gray-800 text-white font-medium py-2 px-4 rounded-full shadow-md smooth-transition text-sm flex items-center transform hover:scale-105 min-h-[44px]">
                                <i class="fas fa-door-open mr-2"></i> Sign Out
                            </button>
                        </form>
                    </div>
                </div>

                <!-- Mobile Menu Button -->
                <div class="md:hidden flex items-center space-x-3">
                    <button id="mobile-menu-button" class="text-gray-200 p-2 focus:outline-none bg-gray-800 rounded-full shadow smooth-transition min-h-[44px] min-w-[44px] hover:bg-gray-700" aria-label="Toggle mobile menu" aria-expanded="false">
                        <i class="fas fa-bars text-xl"></i>
                    </button>
                </div>
            </div>

            <!-- Mobile Menu -->
            <div id="mobile-menu" class="md:hidden hidden fixed inset-0 bg-black bg-opacity-95 z-50 flex flex-col">
                <div class="container mx-auto px-4 py-8 flex flex-col h-full">
                    <div class="flex justify-end mb-6">
                        <button id="close-mobile-menu" class="text-gray-300 hover:text-white min-h-[44px] min-w-[44px] smooth-transition" aria-label="Close mobile menu">
                            <i class="fas fa-times text-2xl"></i>
                        </button>
                    </div>
                    
                    <div class="flex flex-col space-y-6 text-center flex-grow">
                        <a href="{{ route('self.landingProfile') }}#home" class="py-3 text-xl font-medium hover:text-red-400 smooth-transition">Home</a>
                        <a href="{{ route('self.landingProfile') }}#inhere" class="py-3 text-xl font-medium hover:text-red-400 smooth-transition">About Us</a>
                        <a href="{{ route('self.userAttendance') }}" class="py-3 text-xl font-medium text-red-400 border-b-2 border-red-500 w-fit mx-auto px-2 smooth-transition">Attendance</a>
                        <a href="javascript:void(0)" onclick="showProfile(); closeMobileMenu();" class="py-3 text-xl font-medium hover:text-red-400 smooth-transition">Profile</a>
                        
                        @if(Auth::user()->role === 'userSession')
                            @if(auth()->check() && auth()->user()->rfid_uid && isset($attendance) && !$attendance->time_out && !session('timed_out'))
                                <div class="flex justify-center items-center py-4">
                                    <div class="flex items-center bg-gray-800 px-4 py-2 rounded-lg shadow-md">
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
                                class="bg-green-600 hover:bg-green-700 text-white font-medium py-3 px-4 rounded-lg flex items-center justify-center shadow-md smooth-transition transform hover:scale-105 min-h-[44px]">
                                <i class="fas fa-sync-alt mr-2"></i> Renew
                            </button>
                        @endif
                        <form method="POST" action="{{ route('logout.custom') }}" class="w-full">
                            @csrf
                            <button type="submit"
                                class="w-full bg-gray-700 hover:bg-gray-800 text-white font-medium py-3 px-4 rounded-lg flex items-center justify-center shadow-md smooth-transition transform hover:scale-105 min-h-[44px]">
                                <i class="fas fa-door-open mr-2"></i> Sign Out
                            </button>
                        </form>
                    </div>
                </div>
            </div>
    </nav>

    <div class="mx-auto px-4 py-8" x-data="{
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
        <div class="rounded-xl p-6 max-w-4xl mx-auto smooth-transition">
            <!-- Header Section -->
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6">
                <div>
                    <h2 class="text-3xl font-bold gradient-text bg-gradient-to-r from-red-500 to-orange-500 mb-2">Attendance History</h2>
                    <p class="text-gray-400">Track your gym visits</p>
                </div>
                <div class="mt-4 md:mt-0 flex items-center space-x-2">
                    <span class="hidden md:inline-block h-1 w-8 bg-red-500 rounded-full"></span>
                    <span class="text-sm text-gray-400">Select a date to view details</span>
                </div>
            </div>

<!-- Attendance Details Card -->
<div class="mb-6 bg-gray-800 rounded-lg p-4 border border-gray-700">
    <!-- User Info -->
    <div class="flex items-center space-x-4 mb-4">
        <div class="h-10 w-10 rounded-full bg-gradient-to-r from-red-500 to-orange-500 flex items-center justify-center text-white font-bold">
            <span x-text="selectedAttendance ? selectedAttendance.user.first_name.charAt(0) + selectedAttendance.user.last_name.charAt(0) : ''"></span>
        </div>
        <div>
            <h3 class="text-lg font-semibold text-gray-100" x-text="selectedAttendance ? selectedAttendance.user.first_name + ' ' + selectedAttendance.user.last_name : ''"></h3>
            <p class="text-sm text-gray-400" x-text="selectedAttendance ? selectedAttendance.user.membership_type : ''"></p>
        </div>
    </div>
    
    <!-- Details Grid -->
    <div class="grid grid-cols-2 gap-3">
        <div class="bg-gray-900 rounded p-3">
            <p class="text-xs text-gray-400 mb-1">DATE</p>
            <p class="text-sm font-medium text-gray-200" x-text="selectedDayDate"></p>
        </div>
        <div class="bg-gray-900 rounded p-3">
            <p class="text-xs text-gray-400 mb-1">DURATION</p>
            <p class="text-sm font-medium text-gray-200" x-text="selectedDayDuration"></p>
        </div>
        <div class="bg-gray-900 rounded p-3">
            <p class="text-xs text-gray-400 mb-1">CHECK-IN</p>
            <p class="text-sm font-medium text-gray-200" x-text="selectedDayCheckIn"></p>
        </div>
        <div class="bg-gray-900 rounded p-3">
            <p class="text-xs text-gray-400 mb-1">CHECK-OUT</p>
            <p class="text-sm font-medium text-gray-200" x-text="selectedDayCheckOut"></p>
        </div>
    </div>
</div>

            <!-- Calendar Section -->
            <div class="bg-gray-800 rounded-xl p-6 shadow-inner border border-gray-700">
                <!-- Calendar Navigation -->
                <div class="flex justify-between items-center mb-6">
                    <button @click="prevMonth" class="text-gray-400 hover:text-red-500 smooth-transition transform hover:scale-110">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                        </svg>
                    </button>
                    <h3 class="text-xl font-semibold text-gray-200" x-text="monthName() + ' ' + currentYear"></h3>
                    <button @click="nextMonth" class="text-gray-400 hover:text-red-500 smooth-transition transform hover:scale-110">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </button>
                </div>

                <!-- Calendar Grid -->
                <div class="grid grid-cols-7 gap-3 text-center">
                    <!-- Day headers -->
                    <template x-for="day in ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat']" :key="day">
                        <div class="text-sm text-gray-400 font-medium pb-2" x-text="day"></div>
                    </template>

                    <!-- Empty days from previous month -->
                    <template x-for="i in getFirstDayOfMonth()" :key="'empty-' + i">
                        <div class="p-2 text-sm text-gray-600"></div>
                    </template>

                    <!-- Days of current month -->
                    <template x-for="day in getDaysInMonth()" :key="'day-' + day">
                        <div class="relative flex flex-col items-center p-1">
                            <button 
                                @click="selectDay(day)" 
                                class="calendar-day text-sm rounded-full w-10 h-10 flex items-center justify-center smooth-transition focus:outline-none"
                                :class="{
                                    'text-gray-300 hover:bg-gray-700': !isSelectedDay(day),
                                    'text-white bg-gradient-to-r from-red-500 to-orange-500 shadow-md': isSelectedDay(day),
                                    'font-bold border-2 border-red-500': isToday(day) && !isSelectedDay(day)
                                }"
                                x-text="day"
                            ></button>
                            <!-- Attendance indicator dot -->
                            <div x-show="isAttendanceDay(day) && !isSelectedDay(day)" class="mt-1 w-2 h-2 bg-red-500 rounded-full attendance-dot"></div>
                        </div>
                    </template>
                </div>
            </div>
                 <!-- Profile Modal -->
        <div id="profile-modal" class="profile-modal fixed inset-0 z-50 hidden">
            <div class="absolute inset-0 bg-black bg-opacity-70" onclick="hideProfile()"></div>
            <div class="profile-modal-content absolute right-0 top-0 h-full bg-[#1e1e1e] text-gray-200">
                <div class="profile-header bg-red-600">
                    <div class="flex justify-between items-center mb-6">
                        <h3 class="text-2xl font-bold text-white">User Profile</h3>
                        <button onclick="hideProfile()" class="text-white hover:text-gray-300 transition-colors">
                            <i class="fas fa-times text-xl"></i>
                        </button>
                    </div>
                    <div class="text-center">
                        <img src="{{ asset('images/image.png') }}" alt="User Avatar" class="w-20 h-20 rounded-full mx-auto mb-4 profile-avatar">
                        <h2 class="text-xl font-semibold mt-4 text-white">{{ Auth::user()->first_name }} {{ Auth::user()->last_name }}</h2>
                        <p class="text-sm text-gray-300">{{ Auth::user()->email }}</p>
                    </div>
                </div>
                <div class="overflow-y-auto h-[calc(100vh-200px)] bg-[#1e1e1e]">
                    <div class="space-y-2 p-4">
                        <div class="profile-info-item">
                            <label class="block text-sm text-gray-400 mb-1">Phone Number</label>
                            <p class="font-medium text-gray-200">{{ Auth::user()->phone_number ?? 'Not provided' }}</p>
                        </div>
                        <div class="profile-info-item">
                            <label class="block text-sm text-gray-400 mb-1">Gender</label>
                            <p class="font-medium text-gray-200">{{ Auth::user()->gender ?? 'Not specified' }}</p>
                        </div>
                        <div class="profile-info-item">
                            <label class="block text-sm text-gray-400 mb-1">Member Since</label>
                            <p class="font-medium text-gray-200">{{ Auth::user()->created_at ? Auth::user()->created_at->format('M d, Y') : 'N/A' }}</p>
                        </div>
                        <div class="profile-info-item">
                            <label class="block text-sm text-gray-400 mb-1">Last Activity</label>
                            <p class="font-medium text-gray-200">{{ Auth::user()->last_login_at ? Auth::user()->last_login_at->diffForHumans() : 'N/A' }}</p>
                        </div>
                        <div class="profile-info-item">
                        <label class="block text-sm text-gray-400 mb-1">Issued Date</label>
            <p class="font-medium text-gray-200">
                @if(Auth::user()->start_date)
                    {{ \Carbon\Carbon::parse(Auth::user()->start_date)->format('M d, Y') }}
                @else
                    Not specified
                @endif
            </p>
                        </div>
                        <div class="profile-info-item">
                            <label class="block text-sm text-gray-400 mb-1">Membership Status</label>
                            <p class="font-medium {{ Auth::user()->session_status === 'approved' ? 'text-green-600' : 'text-red-600' }}">
                                {{ ucfirst(Auth::user()->session_status) }}
                                @if(Auth::user()->session_status === 'approved' && Auth::user()->end_date)
                                    (Expires {{ \Carbon\Carbon::parse(Auth::user()->end_date)->format('M d, Y') }})
                                @elseif(Auth::user()->session_status === 'rejected' && Auth::user()->rejection_reason)
                                    - {{ Auth::user()->rejection_reason }}
                                @endif
                            </p>
                        </div>
                        @if(Auth::user()->session_status === 'pending')
                            <div class="profile-info-item">
                                <a href="{{ route('self.waiting') }}" class="text-blue-600 hover:text-blue-800">View Approval Status</a>
                            </div>
                        @elseif(in_array(Auth::user()->session_status, ['expired', 'rejected']))
                            <div class="profile-info-item">
                                <button onclick="checkRenewalEligibility()" class="bg-green-600 hover:bg-green-700 text-white font-medium py-2 px-4 rounded-lg w-full">
                                    Renew Membership
                                </button>
                            </div>
                        @endif
                    </div>
                </div>
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