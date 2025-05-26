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
    <!-- Include Navigation -->
    @include('self.navigation')

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
</body>
</html>