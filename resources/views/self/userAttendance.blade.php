<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Attendance Calendar</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#ff5722',
                        secondary: '#ff8a65',
                        dark: '#121212',
                        darker: '#0a0a0a',
                        card: '#1e1e1e',
                        cardHover: '#252525'
                    },
                    animation: {
                        'fade-in': 'fadeIn 0.3s ease-in-out',
                        'slide-up': 'slideUp 0.3s cubic-bezier(0.16, 1, 0.3, 1)',
                        'pulse-soft': 'pulseSoft 2s infinite',
                        'bounce-soft': 'bounceSoft 0.6s ease-out'
                    },
                    keyframes: {
                        fadeIn: {
                            '0%': { opacity: '0' },
                            '100%': { opacity: '1' }
                        },
                        slideUp: {
                            '0%': { transform: 'translateY(10px)', opacity: '0' },
                            '100%': { transform: 'translateY(0)', opacity: '1' }
                        },
                        pulseSoft: {
                            '0%, 100%': { opacity: '1' },
                            '50%': { opacity: '0.7' }
                        },
                        bounceSoft: {
                            '0%': { transform: 'scale(0.95)' },
                            '50%': { transform: 'scale(1.02)' },
                            '100%': { transform: 'scale(1)' }
                        }
                    }
                }
            }
        }
    </script>
    <style>
        .gradient-bg {
            background: linear-gradient(135deg, #ff5722 0%, #ff8a65 100%);
        }
        .glass-effect {
            backdrop-filter: blur(10px);
            background: rgba(30, 30, 30, 0.9);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }
        .attendance-dot {
            position: relative;
        }
        .attendance-dot::after {
            content: '';
            position: absolute;
            bottom: -2px;
            left: 50%;
            transform: translateX(-50%);
            width: 4px;
            height: 4px;
            background: #ff5722;
            border-radius: 50%;
            animation: pulse 2s infinite;
        }
        @keyframes pulse {
            0%, 100% { opacity: 1; transform: translateX(-50%) scale(1); }
            50% { opacity: 0.7; transform: translateX(-50%) scale(1.2); }
        }
    </style>
</head>
<body class="bg-gradient-to-br from-darker via-dark to-darker min-h-screen">

   <!-- Navigation Bar -->
   <nav class="bg-black/90 backdrop-blur-md text-gray-200 py-2 px-3 sm:py-3 sm:px-4 sticky top-0 z-50 border-b border-gray-800">
        <div class="container mx-auto">
            <!-- Alerts for Success and Error messages -->
            <div class="alert-banner success-alert mb-2 p-3 bg-green-100 border-l-4 border-green-500 text-green-700 rounded hidden">
                <div class="flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                    </svg>
                    <span>Success message</span>
                </div>
            </div>

            <div class="alert-banner error-alert mb-2 p-3 bg-red-100 border-l-4 border-red-500 text-red-700 rounded hidden">
                <div class="flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                    </svg>
                    <span>Error message</span>
                </div>
            </div>

            <!-- Main Navigation Content -->
            <div class="flex justify-between items-center">
                <!-- Logo Image -->
                <div class="flex items-center">
                    <a href="#" aria-label="FitTrack Homepage" class="group">
                        <div class="h-8 w-8 sm:h-10 sm:w-10 md:h-12 md:w-12 rounded-full bg-gradient-to-br from-primary to-secondary flex items-center justify-center group-hover:scale-105 transition-transform duration-200">
                            <i class="fas fa-dumbbell text-white text-xs sm:text-sm md:text-base"></i>
                        </div>
                    </a>
                </div>

                <!-- Workout Timer (Mobile optimized) -->
                <div class="workout-timer flex items-center bg-gray-800/80 backdrop-blur-sm px-2 py-1 sm:px-3 sm:py-1 rounded-full border border-gray-700">
                    <i class="fas fa-stopwatch mr-1 sm:mr-2 text-primary text-xs sm:text-sm animate-pulse-soft"></i>
                    <span class="timer-text text-xs sm:text-sm font-mono" id="workout-duration">02:34:15</span>
                </div>

                <!-- Time Out Button (Mobile optimized) -->
                <button
                    id="timeout-button"
                    class="bg-red-600/90 hover:bg-red-700 text-white font-medium p-2 sm:p-2.5 rounded-full shadow-lg transition-all duration-300 min-h-[36px] min-w-[36px] sm:min-h-[40px] sm:min-w-[40px] hover:scale-105 active:scale-95"
                >
                    <i class="fas fa-sign-out-alt text-xs sm:text-sm"></i>
                </button>

                <!-- Mobile Menu Button -->
                <button id="mobile-menu-button" class="text-gray-200 p-2 focus:outline-none bg-gray-800/50 rounded-lg min-h-[36px] min-w-[36px] hover:bg-gray-700 transition-colors duration-200" aria-label="Toggle mobile menu" aria-expanded="false">
                    <i class="fas fa-bars text-sm"></i>
                </button>
            </div>

            <!-- Mobile Menu -->
            <div id="mobile-menu" class="hidden fixed inset-0 bg-black/95 backdrop-blur-md z-50 flex flex-col animate-fade-in">
                <div class="container mx-auto px-4 py-6 flex flex-col h-full">
                    <div class="flex justify-end mb-6">
                        <button id="close-mobile-menu" class="text-gray-300 hover:text-white p-2 hover:bg-gray-800 rounded-lg transition-colors duration-200" aria-label="Close mobile menu">
                            <i class="fas fa-times text-xl"></i>
                        </button>
                    </div>
                    
                    <div class="flex flex-col space-y-4 text-center flex-grow">
                        <a href="#" class="py-4 px-6 text-lg font-medium hover:text-primary transition-all duration-300 hover:bg-gray-800/50 rounded-xl">Home</a>
                        <a href="#" class="py-4 px-6 text-lg font-medium hover:text-primary transition-all duration-300 hover:bg-gray-800/50 rounded-xl">About Us</a>
                        <a href="#" class="py-4 px-6 text-lg font-medium hover:text-primary transition-all duration-300 hover:bg-gray-800/50 rounded-xl border-2 border-primary/30">Attendance</a>
                        <a href="#" class="py-4 px-6 text-lg font-medium hover:text-primary transition-all duration-300 hover:bg-gray-800/50 rounded-xl">Profile</a>
                        
                        <div class="flex justify-center items-center py-6">
                            <div class="flex items-center bg-gray-800/80 backdrop-blur-sm px-6 py-3 rounded-2xl border border-gray-700">
                                <i class="fas fa-stopwatch mr-3 text-primary text-lg animate-pulse-soft"></i>
                                <span class="text-xl font-mono font-medium">02:34:15</span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-2 gap-3 mt-6">
                        <button type="button" class="bg-green-600/90 hover:bg-green-700 text-white font-medium py-4 px-4 rounded-xl flex items-center justify-center transition-all duration-300 hover:scale-105 active:scale-95">
                            <i class="fas fa-sync-alt mr-2"></i> Renew
                        </button>
                        <button type="button" class="bg-gray-700/90 hover:bg-gray-800 text-white font-medium py-4 px-4 rounded-xl flex items-center justify-center transition-all duration-300 hover:scale-105 active:scale-95">
                            <i class="fas fa-door-open mr-2"></i> Sign Out
                        </button>
                    </div>
                </div>
            </div>
    </nav>

    <!-- Main Content -->
    <div class="container mx-auto px-3 py-4 sm:px-4 sm:py-6" x-data="{
        selectedAttendance: {
            user: {
                first_name: 'John',
                last_name: 'Doe',
                membership_type: 'Premium Member',
                attendances: [
                    {
                        time_in: '2024-01-15T08:30:00Z',
                        time_out: '2024-01-15T10:15:00Z',
                        formatted_duration: '1h 45m'
                    },
                    {
                        time_in: '2024-01-17T09:00:00Z',
                        time_out: '2024-01-17T11:30:00Z',
                        formatted_duration: '2h 30m'
                    },
                    {
                        time_in: '2024-01-20T07:45:00Z',
                        time_out: '2024-01-20T09:30:00Z',
                        formatted_duration: '1h 45m'
                    },
                    {
                        time_in: '2024-01-22T10:00:00Z',
                        time_out: '2024-01-22T12:15:00Z',
                        formatted_duration: '2h 15m'
                    },
                    {
                        time_in: '2024-01-25T08:15:00Z',
                        time_out: '2024-01-25T10:45:00Z',
                        formatted_duration: '2h 30m'
                    }
                ]
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
        
        <!-- User Info Card -->
        <div class="glass-effect rounded-2xl p-4 mb-4 animate-slide-up">
            <div class="flex items-center space-x-3">
                <div class="flex-shrink-0 h-12 w-12 rounded-2xl gradient-bg flex items-center justify-center text-white font-bold text-lg shadow-lg">
                    <span x-text="selectedAttendance ? selectedAttendance.user.first_name.charAt(0) + selectedAttendance.user.last_name.charAt(0) : ''"></span>
                </div>
                <div class="flex-grow">
                    <h3 class="text-base sm:text-lg font-semibold text-white" x-text="selectedAttendance ? selectedAttendance.user.first_name + ' ' + selectedAttendance.user.last_name : ''"></h3>
                    <div class="flex items-center space-x-2">
                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-primary/20 text-primary border border-primary/30">
                            <i class="fas fa-crown mr-1"></i>
                            <span x-text="selectedAttendance ? selectedAttendance.user.membership_type : ''"></span>
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Calendar Container -->
        <div class="glass-effect rounded-2xl overflow-hidden shadow-2xl animate-slide-up">
            <!-- Calendar Header -->
            <div class="gradient-bg p-4">
                <div class="flex justify-between items-center mb-4">
                    <button @click="prevMonth" class="text-white/80 hover:text-white p-2 hover:bg-white/10 rounded-xl transition-all duration-200 active:scale-95">
                        <i class="fas fa-chevron-left text-lg"></i>
                    </button>
                    <div class="text-center">
                        <h3 class="text-lg sm:text-xl font-bold text-white" x-text="monthName() + ' ' + currentYear"></h3>
                        <p class="text-white/80 text-sm">Tap a date to view details</p>
                    </div>
                    <button @click="nextMonth" class="text-white/80 hover:text-white p-2 hover:bg-white/10 rounded-xl transition-all duration-200 active:scale-95">
                        <i class="fas fa-chevron-right text-lg"></i>
                    </button>
                </div>

                <!-- Day Headers -->
                <div class="grid grid-cols-7 gap-1 mb-2">
                    <template x-for="day in ['S', 'M', 'T', 'W', 'T', 'F', 'S']" :key="day">
                        <div class="text-xs sm:text-sm text-white/70 font-medium text-center py-2" x-text="day"></div>
                    </template>
                </div>
            </div>

            <!-- Calendar Grid -->
            <div class="p-3 sm:p-4">
                <div class="grid grid-cols-7 gap-1 sm:gap-2">
                    <!-- Empty days from previous month -->
                    <template x-for="i in getFirstDayOfMonth()" :key="'empty-' + i">
                        <div class="aspect-square"></div>
                    </template>

                    <!-- Days of current month -->
                    <template x-for="day in getDaysInMonth()" :key="'day-' + day">
                        <div class="relative">
                            <button 
                                @click="selectDay(day)" 
                                class="w-full aspect-square text-xs sm:text-sm rounded-xl flex items-center justify-center transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-primary/50 relative overflow-hidden group"
                                :class="{
                                    'text-gray-400 hover:text-white hover:bg-gray-700/50': !isSelectedDay(day) && !isToday(day) && !isAttendanceDay(day),
                                    'text-white bg-gradient-to-br from-primary to-secondary shadow-lg scale-105': isSelectedDay(day),
                                    'font-bold text-white bg-gray-700 ring-2 ring-white/30': isToday(day) && !isSelectedDay(day),
                                    'text-white bg-gray-600/50 hover:bg-gray-600': isAttendanceDay(day) && !isSelectedDay(day) && !isToday(day)
                                }"
                            >
                                <span x-text="day" class="relative z-10"></span>
                                
                                <!-- Attendance indicator -->
                                <div x-show="isAttendanceDay(day) && !isSelectedDay(day)" 
                                     class="absolute bottom-1 left-1/2 transform -translate-x-1/2 w-1 h-1 bg-primary rounded-full animate-pulse-soft"></div>
                                
                                <!-- Today indicator ring -->
                                <div x-show="isToday(day)" 
                                     class="absolute inset-0 border-2 border-white/30 rounded-xl"></div>
                                
                                <!-- Hover effect -->
                                <div class="absolute inset-0 bg-white/5 opacity-0 group-hover:opacity-100 transition-opacity duration-200 rounded-xl"></div>
                            </button>
                        </div>
                    </template>
                </div>
            </div>
        </div>

        <!-- Selected Day Details -->
        <div class="mt-4 space-y-3 animate-slide-up" x-show="selectedDay">
            <!-- Date Header -->
            <div class="glass-effect rounded-2xl p-4">
                <div class="flex items-center justify-between">
                    <div>
                        <h4 class="text-lg font-semibold text-white" x-text="selectedDayDate"></h4>
                        <p class="text-gray-400 text-sm">Workout Details</p>
                    </div>
                    <div class="h-10 w-10 rounded-full bg-primary/20 flex items-center justify-center">
                        <i class="fas fa-dumbbell text-primary text-sm"></i>
                    </div>
                </div>
            </div>

            <!-- Stats Grid -->
            <div class="grid grid-cols-2 gap-3">
                <!-- Check-in -->
                <div class="glass-effect rounded-xl p-4 hover:bg-cardHover transition-colors duration-200">
                    <div class="flex items-center space-x-3">
                        <div class="h-8 w-8 rounded-lg bg-green-500/20 flex items-center justify-center">
                            <i class="fas fa-sign-in-alt text-green-400 text-xs"></i>
                        </div>
                        <div>
                            <p class="text-gray-400 text-xs">Check-in</p>
                            <p class="text-white font-semibold text-sm" x-text="selectedDayCheckIn"></p>
                        </div>
                    </div>
                </div>

                <!-- Check-out -->
                <div class="glass-effect rounded-xl p-4 hover:bg-cardHover transition-colors duration-200">
                    <div class="flex items-center space-x-3">
                        <div class="h-8 w-8 rounded-lg bg-red-500/20 flex items-center justify-center">
                            <i class="fas fa-sign-out-alt text-red-400 text-xs"></i>
                        </div>
                        <div>
                            <p class="text-gray-400 text-xs">Check-out</p>
                            <p class="text-white font-semibold text-sm" x-text="selectedDayCheckOut"></p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Duration -->
            <div class="glass-effect rounded-xl p-4 hover:bg-cardHover transition-colors duration-200">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-3">
                        <div class="h-10 w-10 rounded-xl gradient-bg flex items-center justify-center">
                            <i class="fas fa-clock text-white text-sm"></i>
                        </div>
                        <div>
                            <p class="text-gray-400 text-xs">Workout Duration</p>
                            <p class="text-white font-bold text-lg" x-text="selectedDayDuration"></p>
                        </div>
                    </div>
                    <div class="text-right">
                        <div class="w-16 h-2 bg-gray-700 rounded-full overflow-hidden">
                            <div class="h-full gradient-bg rounded-full" style="width: 75%"></div>
                        </div>
                        <p class="text-gray-400 text-xs mt-1">75% of goal</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Legend -->
        <div class="mt-6 glass-effect rounded-xl p-4">
            <h5 class="text-white font-medium text-sm mb-3 flex items-center">
                <i class="fas fa-info-circle text-primary mr-2"></i>
                Legend
            </h5>
            <div class="space-y-2">
                <div class="flex items-center space-x-3">
                    <div class="w-6 h-6 bg-gray-700 rounded-lg border-2 border-white/30 flex items-center justify-center">
                        <span class="text-white text-xs font-bold">15</span>
                    </div>
                    <span class="text-gray-300 text-sm">Today</span>
                </div>
                <div class="flex items-center space-x-3">
                    <div class="w-6 h-6 bg-gray-600/50 rounded-lg flex items-center justify-center relative">
                        <span class="text-white text-xs">20</span>
                        <div class="absolute bottom-0.5 left-1/2 transform -translate-x-1/2 w-1 h-1 bg-primary rounded-full"></div>
                    </div>
                    <span class="text-gray-300 text-sm">Attendance day</span>
                </div>
                <div class="flex items-center space-x-3">
                    <div class="w-6 h-6 gradient-bg rounded-lg flex items-center justify-center">
                        <span class="text-white text-xs font-bold">22</span>
                    </div>
                    <span class="text-gray-300 text-sm">Selected day</span>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            initNavigation();
            initProfile();
            
            // Simulate workout timer
            let startTime = Date.now() - (2 * 3600 + 34 * 60 + 15) * 1000; // 2:34:15 ago
            updateTimer();
            setInterval(updateTimer, 1000);
            
            function updateTimer() {
                const elapsed = Math.floor((Date.now() - startTime) / 1000);
                const hours = Math.floor(elapsed / 3600);
                const minutes = Math.floor((elapsed % 3600) / 60);
                const seconds = elapsed % 60;
                
                const timeString = `${hours.toString().padStart(2, '0')}:${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;
                
                const timerElements = document.querySelectorAll('#workout-duration, #mobile-workout-duration');
                timerElements.forEach(el => {
                    if (el) el.textContent = timeString;
                });
            }
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
                profileModal.classList.remove('