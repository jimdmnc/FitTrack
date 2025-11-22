@extends('layouts.app')

@section('content')
<style>
    [x-cloak] { display: none !important; }
    <>
    /* Responsive table container */
    .table-responsive {
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }
        
        /* Custom scrollbar for tables */
        .table-responsive::-webkit-scrollbar {
            height: 8px;
        }
        .table-responsive::-webkit-scrollbar-track {
            background: #2d2d2d;
        }
        .table-responsive::-webkit-scrollbar-thumb {
            background-color: #ff5722;
            border-radius: 20px;
        }

        .pagination {
            display: flex;
            justify-content: center;
            align-items: center;
            list-style: none;
            padding: 0;
            margin: 1rem 0;
        }

        .pagination li {
            margin: 0 2px;
        }

        .pagination li a,
        .pagination li span {
            display: flex;
            align-items: center;
            justify-content: center;
            min-width: 32px;
            height: 32px;
            padding: 0 8px;
            color: #b9b9b9;
            background-color: #2d2d2d;
            border-radius: 6px;
            font-size: 0.875rem;
            text-decoration: none;
            transition: all 0.2s ease;
        }

        .pagination li.active span {
            background-color: #ff5722;
            color: #fff;
            font-weight: 600;
        }

        .pagination li a:hover {
            background-color: #3d3d3d;
            color: #fff;
        }

        .pagination li.disabled span {
            background-color: #202020;
            color: #666;
            cursor: not-allowed;
        }

        @media (max-width: 640px) {
            .pagination li a,
            .pagination li span {
                min-width: 28px;
                height: 28px;
                font-size: 0.75rem;
            }
        }

        
        /* Mobile optimizations */
        @media (max-width: 640px) {
            .mobile-full-width {
                width: 100%;
            }
            
            .pagination-container {
                overflow-x: auto;
                padding-bottom: 1rem;
            }
            
            .pagination {
                display: flex;
                white-space: nowrap;
            }
        }
    @keyframes spin {
        from { transform: rotate(0deg); }
        to { transform: rotate(360deg); }
    }
    .animate-spin {
        animation: spin 1s linear infinite;
    }
</style>
<div class="py-8 sm:px-6 lg:px-4" x-data="{
    showModal: false,
    selectedAttendance: null,
    selectedDayCheckIn: null,
    selectedDayCheckOut: null,
    selectedDayDate: null,
    selectedDayDuration: null,
    openModal(attendance) {
        this.selectedAttendance = attendance;
        this.showModal = true;
    }
}" x-cloak>
    <div class="mb-6">
        <h1 class="text-3xl pb-1 md:text-4xl font-bold bg-clip-text text-transparent bg-gradient-to-r from-red-600 to-orange-600">
            Gym Member Attendance
        </h1>
        <p class="mt-1 ml-2 text-sm text-gray-300">Track member check-ins and check-outs</p>
    </div>

    <div class="p-4">
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between px-3 sm:px-6 py-4 border-b border-gray-800 gap-4">

            <form method="GET" action="{{ route('staff.attendance.index') }}" class="w-full sm:w-64 md:w-80">
                <div class="relative flex items-center -ml-4 mb-2">
                    <!-- Search Input -->
                    <input 
                        type="text" 
                        name="search" 
                        value="{{ request('search') }}" 
                        placeholder="Search member" 
                        class="w-full bg-[#212121] border border-[#666666] hover:border-[#ff5722] rounded-full py-2 pl-9 pr-3 text-sm text-gray-200 placeholder-gray-400 focus:outline-none focus:ring-0 focus:border-[#ff5722]"
                        aria-label="Search members"
                    >
                    
                    <!-- Search Icon (Inside Input) -->
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="h-4 w-4 text-[#ff5722]" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd" />
                        </svg>
                    </div>

                    <!-- Clear Search Button -->
                    <a 
                    id="clearSearch" class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-200 hover:text-[#ff5722] transition-colors hidden cursor-pointer"
                    aria-label="Clear search">
                        <svg class="h-4 w-4 text-[#ff5722]" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </a>

                </div>
            </form>


            <div class="flex flex-nowrap gap-2 w-full justify-end">

                    <div class="relative w-full sm:w-auto">
                        <button id="select-btn" class="w-full px-6 py-2 text-gray-200 bg-[#212121] border border-[#666666] hover:border-[#ff5722] rounded-lg flex justify-between items-center">
                            <span id="selected-option">today</span>
                            <svg class="ml-2 w-5 h-5 text-gray-200" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>

                        <ul id="dropdown" class="hidden absolute left-0 w-full bg-[#212121] rounded-lg mt-2 overflow-hidden z-10">
                            <li class="px-1 py-1 text-gray-200 cursor-pointer hover:bg-[#ff5722]" data-value="all">All</li>
                            <li class="px-1 py-1 text-gray-200 cursor-pointer hover:bg-[#ff5722]" data-value="today">Today</li>
                            <li class="px-1 py-1 text-gray-200 cursor-pointer hover:bg-[#ff5722]" data-value="yesterday">Yesterday</li>
                            <li class="px-1 py-1 text-gray-200 cursor-pointer hover:bg-[#ff5722]" data-value="thisWeek">This Week</li>
                            <li class="px-1 py-1 text-gray-200 cursor-pointer hover:bg-[#ff5722]" data-value="lastWeek">Last Week</li>
                            <li class="px-1 py-1 text-gray-200 cursor-pointer hover:bg-[#ff5722]" data-value="thisMonth">This Month</li>
                        </ul>
                    </div>
                    <button id="refreshBtn" class="bg-[#212121] text-gray-200 border border-[#ff5722] hover:translate-y-[-2px] hover:bg-[#ff5722] px-4 py-2 rounded-md text-sm transition-colors flex items-center btn-touch w-full sm:w-auto">
                        <svg id="refreshIcon" xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                        </svg>
                        <span id="refreshText">Refresh</span>
                    </button>

            </div>
        </div>

        <div class="overflow-x-auto sm:rounded-lg table-responsive">
            <table class="w-full border-collapse divide-y divide-black">
                <thead class="bg-gradient-to-br from-[#2c2c2c] to-[#1e1e1e] border-b border-black">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-gray-200 uppercase tracking-wider">#</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-gray-200 uppercase tracking-wider">Member</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-gray-200 uppercase tracking-wider">Membership</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-gray-200 uppercase tracking-wider">Check-in</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-gray-200 uppercase tracking-wider">Check-out</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-gray-200 uppercase tracking-wider">Duration</th>
                        <th scope="col" class="px-6 py-3 text-center text-xs font-bold text-gray-200 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-black">
                    @forelse($attendances as $attendance)
                    <tr class="@if($loop->even) bg-[#1e1e1e] @else bg-[#1e1e1e] @endif">

                        <!-- # Column with Iteration Number -->
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-200">
                            {{ ($attendances->currentPage() - 1) * $attendances->perPage() + $loop->iteration }}
                        </td>

                        <!-- Member Column -->
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="ml-4">
                                    <div class="text-sm font-medium text-gray-200">{{ $attendance->user ? $attendance->user->first_name . ' ' . $attendance->user->last_name : 'Unknown' }}</div>
                                </div>
                            </div>
                        </td>
                        
                        <!-- Membership Column -->
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($attendance->user)
                                @include('components.membership-badge', ['type' => $attendance->user->membership_type, 'label' => $attendance->user->getMembershipType()])
                            @else
                                <span class="px-2.5 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-700 text-gray-200">
                                    Unknown
                                </span>
                            @endif
                        </td>
                        
                        <!-- Check-in Column -->
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-200">{{ $attendance->time_in->format('h:i A') }}</div>
                            <div class="text-xs text-gray-400">{{ $attendance->time_in->format('M d, Y') }}</div>
                        </td>
                        
                        <!-- Check-out Column -->
                        <td class="px-3 sm:px-6 py-4 whitespace-nowrap" data-label="Check-out">
                            @if($attendance->time_out)
                                <div class="text-sm text-gray-200">{{ $attendance->time_out->format('h:i A') }}</div>
                                <div class="text-xs text-gray-400">{{ $attendance->time_out->format('M d, Y') }}</div>
                            @elseif($attendance->time_in->startOfDay()->lt(\Carbon\Carbon::today()))
                                <!-- Past dates should show estimated checkout time -->
                                <div class="text-sm text-gray-200">9:00 PM</div>
                                <div class="text-xs text-gray-400">{{ $attendance->time_in->format('M d, Y') }}</div>
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-orange-100 text-orange-800">
                                    Auto Checkout
                                </span>
                            @else
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    <span class="h-1.5 w-1.5 mr-1.5 rounded-full bg-green-400 animate-pulse"></span>
                                    In Session
                                </span>
                            @endif
                        </td>
                        
                        <!-- Duration Column -->
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-200">
                            {{ $attendance->formatted_duration ?? 'N/A' }}
                        </td>
                        
                        <!-- Actions Column -->
                        <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                            @if($attendance->user)
                            <button 
                                class="text-gray-200 hover:text-gray-200 hover:translate-y-[-2px] bg-transparent border border-[#ff5722] hover:bg-[#ff5722] px-3 py-1 rounded-md transition-colors duration-150"
                                @click="openModal({
                                    user: {
                                        first_name: '{{ $attendance->user->first_name }}',
                                        last_name: '{{ $attendance->user->last_name }}',
                                        membership_type: '{{ $attendance->user->getMembershipType() }}',
                                        attendances: {{ json_encode($attendance->user->attendances->map(function($a) {
                                            return [
                                                'time_in' => $a->time_in->toISOString(),
                                                'time_out' => $a->time_out ? $a->time_out->toISOString() : null,
                                                'formatted_duration' => $a->formatted_duration ?? 'N/A'
                                            ];
                                        })) }}
                                    },
                                    time_in: '{{ $attendance->time_in->toISOString() }}',
                                    time_out: '{{ $attendance->time_out ? $attendance->time_out->toISOString() : 'null' }}',
                                    formatted_duration: '{{ $attendance->formatted_duration ?? 'N/A' }}'
                                })"
                            >
                                Details
                            </button>
                            @else
                            <span class="text-gray-400">N/A</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center">
                            <div class="flex flex-col items-center justify-center">
                                <h3 class="mt-4 text-lg font-medium text-gray-200">No attendance records found</h3>
                                <p class="mt-1 text-sm text-gray-400">There are no attendance records matching your criteria.</p>
                                @if(request('search') || request('filter'))
                                <a href="{{ route('staff.attendance.index') }}" class="mt-4 text-sm text-[#ff5722] hover:text-[#e64a19] transition-colors">
                                    Clear filters
                                </a>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Calendar Modal -->
        <div x-show="showModal" x-transition @click.away="showModal = false" class="fixed inset-0 flex items-center justify-center z-50 bg-black bg-opacity-50 p-4">
            <div class="bg-[#1e1e1e] rounded-lg shadow-lg p-4 sm:p-6 w-full max-w-md max-h-[90vh] overflow-y-auto" @click.stop>
                <div class="sticky top-0 z-20 bg-[#1e1e1e] -mx-4 sm:-mx-6 px-4 sm:px-6 py-3 border-b border-gray-800">
                    <div class="flex justify-between items-center">
                        <h2 class="text-xl font-bold bg-clip-text text-transparent bg-gradient-to-r from-red-600 to-orange-600">Attendance Details</h2>
                        <button @click="showModal = false" class="p-2 text-gray-200 rounded-full hover:bg-[#ff5722] hover:scale-95 transition-transform">
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                </div>

                <!-- Modal Details -->
                <div class="mb-6 pt-3">
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
                        <div class="mt-2">
                            <p class="text-sm text-gray-400">Date</p>
                            <p class="text-gray-200" x-text="selectedDayDate"></p>
                        </div>
                        <div class="mt-2">
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
                <div x-data="{
                    currentMonth: new Date().getMonth(),
                    currentYear: new Date().getFullYear(),
                    today: new Date().getDate(),
                    selectedDay: null,

                    // Get the number of days in the current month
                    getDaysInMonth() {
                        return new Date(this.currentYear, this.currentMonth + 1, 0).getDate();
                    },

                    // Get the first day of the current month (used for layout)
                    getFirstDayOfMonth() {
                        return new Date(this.currentYear, this.currentMonth, 1).getDay();
                    },

                    // Navigate to the previous month
                    prevMonth() {
                        if (this.currentMonth === 0) {
                            this.currentMonth = 11;
                            this.currentYear--;
                        } else {
                            this.currentMonth--;
                        }
                        this.selectedDay = null;
                    },

                    // Navigate to the next month
                    nextMonth() {
                        if (this.currentMonth === 11) {
                            this.currentMonth = 0;
                            this.currentYear++;
                        } else {
                            this.currentMonth++;
                        }
                        this.selectedDay = null;
                    },

                    // Get the name of the current month
                    monthName() {
                        return new Date(this.currentYear, this.currentMonth).toLocaleString('default', { month: 'long' });
                    },

                    // Check if the given day has attendance records
                    isAttendanceDay(day) {
                        // Make sure we have the right context
                        const mainData = Alpine.$data(this.$root);
                        if (!mainData.selectedAttendance || !mainData.selectedAttendance.user || !mainData.selectedAttendance.user.attendances) {
                            return false;
                        }
                        
                        const currentDate = new Date(this.currentYear, this.currentMonth, day);
                        return mainData.selectedAttendance.user.attendances.some(attendance => {
                            const attendanceDate = new Date(attendance.time_in);
                            return attendanceDate.getFullYear() === currentDate.getFullYear() &&
                                attendanceDate.getMonth() === currentDate.getMonth() &&
                                attendanceDate.getDate() === currentDate.getDate();
                        });
                    },

                    // Check if the given day is today
                    isToday(day) {
                        const today = new Date();
                        return day === today.getDate() &&
                            this.currentMonth === today.getMonth() &&
                            this.currentYear === today.getFullYear();
                    },

                    // Select a day and load its attendance data
                    selectDay(day) {
                        this.selectedDay = day;
                        this.loadDayAttendance(day);
                    },

                    // Load attendance data for the selected day
                    loadDayAttendance(day) {
                        const selectedDate = new Date(this.currentYear, this.currentMonth, day);
                        const formattedDate = selectedDate.toLocaleDateString([], { month: 'short', day: 'numeric', year: 'numeric' });
                        
                        // Access the parent component's data
                        const mainData = Alpine.$data(this.$root);
                        
                        // Default values
                        let checkIn = 'N/A';
                        let checkOut = 'N/A';
                        let duration = 'N/A';
                        
                        // Only proceed if we have valid data
                        if (mainData.selectedAttendance && mainData.selectedAttendance.user && mainData.selectedAttendance.user.attendances) {
                            const attendanceForDay = mainData.selectedAttendance.user.attendances.find(attendance => {
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
                        
                        // Update parent component state
                        mainData.selectedDayDate = formattedDate;
                        mainData.selectedDayCheckIn = checkIn;
                        mainData.selectedDayCheckOut = checkOut;
                        mainData.selectedDayDuration = duration;
                    },

                    // Check if the given day is selected
                    isSelectedDay(day) {
                        return this.selectedDay === day;
                    }
                    }" 
                    x-init="$nextTick(() => { 
                        // Initialize with today's day selected by default
                        selectDay(today);
                    })">
                        <!-- Calendar Navigation -->
                        <div class="flex justify-between items-center mb-2">
                            <button @click="prevMonth" class="text-gray-400 hover:text-[#ff5722]">
                                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                                </svg>
                            </button>
                            <h3 class="text-md font-medium text-gray-200" x-text="monthName() + ' ' + currentYear"></h3>
                            <button @click="nextMonth" class="text-gray-400 hover:text-[#ff5722]">
                                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                </svg>
                            </button>
                        </div>

                    <!-- Calendar Grid -->
                    <div class="grid grid-cols-7 gap-1 text-center text-xs sm:text-sm">
                        <!-- Day headers -->
                        <template x-for="day in ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat']" :key="day">
                            <div class="text-xs text-gray-400 font-medium" x-text="day"></div>
                        </template>

                        <!-- Empty days from previous month -->
                        <template x-for="i in getFirstDayOfMonth()" :key="'empty-' + i">
                            <div class="p-1 text-sm text-gray-600"></div>
                        </template>

                        <!-- Days of current month -->
                        <template x-for="day in getDaysInMonth()" :key="'day-' + day">
                            <div class="relative flex flex-col items-center p-1">
                                <button 
                                    @click="selectDay(day)" 
                                    class="text-sm rounded-full w-6 h-6 flex items-center justify-center transition-colors focus:outline-none"
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
        
        <div class="mt-4">
            {{ $attendances->appends(['search' => request('search'), 'filter' => request('filter')])->links('vendor.pagination.default') }}
        </div>
    </div>
</div>
<script>
    document.getElementById('refreshBtn').addEventListener('click', function() {
        const refreshBtn = this;
        const refreshIcon = document.getElementById('refreshIcon');
        const refreshText = document.getElementById('refreshText');
        
        // Disable button during refresh
        refreshBtn.disabled = true;
        
        // Add loading animation class
        refreshIcon.classList.add('animate-spin');
        
        // Change text to "Refreshing..."
        refreshText.textContent = 'Refreshing...';
        
        // Simulate refresh action (replace with your actual refresh logic)
        setTimeout(function() {
            // Your actual refresh code would go here
            location.reload()
            
            // For demonstration, we'll just simulate a refresh
            console.log('Refreshing data...');
            
            // After refresh completes:
            refreshIcon.classList.remove('animate-spin');
            refreshText.textContent = 'Refresh';
            refreshBtn.disabled = false;
            
            // Optional: Show success indicator
            const originalColor = refreshBtn.classList.contains('bg-[#212121]') ? '[#212121]' : '[#ff5722]';
            refreshBtn.classList.remove('bg-[#212121]', 'bg-[#ff5722]');
            refreshBtn.classList.add('bg-green-600');
            
            setTimeout(() => {
                refreshBtn.classList.remove('bg-green-600');
                refreshBtn.classList.add(`bg-${originalColor}`);
            }, 1000);
            
        }, 1500); // Simulate 1.5 second refresh time
    });
</script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
    // Initial setup
    initializeAttendancePage();

    // Also run it when Alpine.js is finished initializing
    document.addEventListener('alpine:init', initializeAttendancePage);
});

// Function to initialize all the attendance page functionality
function initializeAttendancePage() {
    const selectBtn = document.getElementById('select-btn');
    const dropdown = document.getElementById('dropdown');
    const selectedOption = document.getElementById('selected-option');
    const searchInput = document.querySelector('input[name="search"]');
    const searchForm = searchInput ? searchInput.closest('form') : null;
    const clearSearchButton = document.getElementById('clearSearch');
    const tableContainer = document.querySelector('.overflow-x-auto');
    const paginationContainer = document.querySelector('.mt-4');
    let searchTimeout;
    
    // Only proceed if we're on the attendance page
    if (!selectBtn || !tableContainer) return;

    // Set up the current filter based on URL parameters
    const urlParams = new URLSearchParams(window.location.search);
    const currentFilter = urlParams.get('filter') || 'today';

    // Set initial selected option
    const initialOption = document.querySelector(`[data-value="${currentFilter}"]`);
    if (initialOption) {
        selectedOption.textContent = initialOption.textContent;
    }

    // Set up dropdown toggle
    selectBtn.addEventListener('click', () => {
        dropdown.classList.toggle('hidden');
    });

    // Set up filter options
    dropdown.querySelectorAll('li').forEach(option => {
        option.addEventListener('click', () => {
            const filterValue = option.getAttribute('data-value');
            selectedOption.textContent = option.textContent;
            dropdown.classList.add('hidden');
            
            // Get current URL parameters
            const url = new URL(window.location.href);
            const searchParams = new URLSearchParams(url.search);
            
            // Update filter parameter
            searchParams.set('filter', filterValue);
            
            // Remove page parameter to go back to first page
            searchParams.delete('page');
            
            // Update URL and load new data
            window.history.pushState({}, '', `${url.pathname}?${searchParams.toString()}`);
            fetchAttendances();
        });
    });

    // Prevent the search form from submitting traditionally
    if (searchForm) {
        searchForm.addEventListener('submit', function(e) {
            e.preventDefault();
            updateSearchParam(searchInput.value);
            fetchAttendances();
        });
    }

    // Close dropdown when clicking outside
    window.addEventListener('click', (e) => {
        if (!selectBtn.contains(e.target) && !dropdown.contains(e.target)) {
            dropdown.classList.add('hidden');
        }
    });

    // Function to update the search parameter in URL
    function updateSearchParam(searchValue) {
        const url = new URL(window.location.href);
        const searchParams = new URLSearchParams(url.search);
        
        if (searchValue.trim()) {
            searchParams.set('search', searchValue.trim());
        } else {
            searchParams.delete('search');
        }
        
        // Reset to page 1 when search changes
        searchParams.delete('page');
        
        // Update URL without reloading
        window.history.pushState({}, '', `${url.pathname}?${searchParams.toString()}`);
    }

    window.fetchAttendances = function() {
        const params = new URLSearchParams(window.location.search);

        // Get current page number from the URL or default to 1
        const page = params.get('page') || 1;
        params.set('page', page);  // Add the current page to the request

        // Make sure search parameter is included from the input
        const searchValue = searchInput ? searchInput.value.trim() : '';
        if (searchValue) {
            params.set('search', searchValue);
        }

        // Build URL for AJAX request
        const fetchUrl = window.location.pathname + '?' + params.toString();

        // Show loading state while fetching
        tableContainer.innerHTML = '<div class="flex justify-center items-center h-32"><div class="animate-spin rounded-full h-12 w-12 border-t-2 border-b-2 border-orange-500"></div></div>';

        // Fetch data via AJAX
        fetch(fetchUrl, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(data => {
            console.log('AJAX attendance response:', data);
            if (tableContainer) {
                tableContainer.innerHTML = data.table;
            }

            if (paginationContainer && data.pagination) {
                paginationContainer.innerHTML = data.pagination;

                // Update the visible total count element. Prefer server-formatted value when available.
                const totalElem = paginationContainer.querySelector('.pagination-total-count') || document.querySelector('.pagination-total-count');
                if (totalElem) {
                    if (typeof data.total_formatted !== 'undefined') {
                        totalElem.textContent = data.total_formatted;
                    } else if (typeof data.total !== 'undefined') {
                        try {
                            totalElem.textContent = Number(data.total).toLocaleString();
                        } catch (e) {
                            totalElem.textContent = data.total;
                        }
                    } else {
                        // Fallback: read whatever the rendered pagination contains
                        const parsed = paginationContainer.querySelector('.pagination-total-count');
                        if (parsed) totalElem.textContent = parsed.textContent;
                    }
                }
            }

            // Reinitialize event listeners for the new content
            initializeModalButtons();
            attachPaginationListeners();
        })
        .catch(error => {
            console.error('Error:', error);
            if (tableContainer) {
                tableContainer.innerHTML = '<div class="text-center py-8 text-red-500">Error loading data. Please try again.</div>';
            }
        });
    };


    // Attach event listeners to pagination links
    function attachPaginationListeners() {
        const paginationLinks = document.querySelectorAll('.pagination a');
        paginationLinks.forEach(link => {
            link.addEventListener('click', function (e) {
                e.preventDefault();

                // Get the page URL from pagination link
                const pageUrl = new URL(this.href);
                const page = pageUrl.searchParams.get('page');

                // Update the URL in the address bar without reloading
                const url = new URL(window.location.href);
                url.searchParams.set('page', page);
                window.history.pushState({}, '', url.toString());

                // Fetch attendance data for the new page
                fetchAttendances();
            });
        });
    }

    // Function to initialize modal open buttons
    function initializeModalButtons() {
        const detailButtons = document.querySelectorAll('[x-on\\:click], [\\@click]');
        detailButtons.forEach(button => {
            const clickAttr = button.getAttribute('x-on:click') || button.getAttribute('@click');
            if (clickAttr && clickAttr.includes('openModal')) {
                button.addEventListener('click', function() {
                    try {
                        // For Alpine.js v3, we need to access the component differently
                        const alpineData = window.Alpine ? window.Alpine.raw(this) : null;
                        
                        // If we couldn't get Alpine data through Alpine.raw, use attribute parsing
                        if (!alpineData) {
                            const attendanceString = this.getAttribute('x-on:click') || this.getAttribute('@click');
                            if (attendanceString && attendanceString.includes('openModal')) {
                                // Extract the attendance data from the attribute
                                const match = attendanceString.match(/openModal\((.*)\)/);
                                if (match && match[1]) {
                                    try {
                                        // If you need to execute this data, you'd need a safe way to evaluate it
                                        console.log("Would parse and execute:", match[1]);
                                        // This is where you'd integrate with Alpine.js properly
                                    } catch (jsonError) {
                                        console.error("Error parsing attendance data:", jsonError);
                                    }
                                }
                            }
                        }
                    } catch (error) {
                        console.error("Error opening modal:", error);
                    }
                });
            }
        });
    }

    // Show/hide clear button based on search input
    const toggleClearButtonVisibility = () => {
        if (searchInput && searchInput.value.trim()) {
            clearSearchButton.classList.remove('hidden');
        } else {
            clearSearchButton.classList.add('hidden');
        }
    };

    // Debounce function for search input
    const debounce = (func, delay) => {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(func, delay);
    };

    // Listen for search input to trigger AJAX fetch with debounce
    if (searchInput) {
        searchInput.addEventListener('input', () => {
            toggleClearButtonVisibility();
            debounce(() => {
                updateSearchParam(searchInput.value);
                fetchAttendances();
            }, 500); // Trigger after 500ms of typing
        });
    }

    // Clear search button functionality
    if (clearSearchButton) {
        clearSearchButton.addEventListener('click', () => {
            if (searchInput) searchInput.value = ''; // Clear input
            clearSearchButton.classList.add('hidden'); // Hide button
            
            // Update URL to remove search parameter
            const url = new URL(window.location.href);
            const searchParams = new URLSearchParams(url.search);
            searchParams.delete('search');
            window.history.pushState({}, '', `${url.pathname}?${searchParams.toString()}`);

            fetchAttendances(); // Fetch data without search term
        });
    }

    // Initialize the clear button visibility on page load
    toggleClearButtonVisibility();
    
    // Initialize modal buttons
    initializeModalButtons();
    
    // Attach pagination listeners for initial page load
    attachPaginationListeners();
}
</script>
@endsection