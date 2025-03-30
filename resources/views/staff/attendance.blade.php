@extends('layouts.app')

@section('content')
<style>
    [x-cloak] { display: none !important; }
</style>
<!-- Loading Indicator -->
<div id="loadingIndicator" class="hidden fixed top-0 left-0 w-full h-1 bg-[#ff5722] z-50">
    <div class="h-full bg-[#e64a19] animate-pulse"></div>
</div>
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

    <div class="">
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between px-6 py-4 border-b border-gray-800 gap-4">
            <form method="GET" action="{{ route('staff.attendance.index') }}" class="w-full sm:w-64 md:w-80">
                <div class="relative flex items-center -ml-4 mb-2">
                    <!-- Search Input -->
                    <input 
                        type="text" 
                        name="search" 
                        value="{{ request('search') }}" 
                        placeholder="Search by name" 
                        class="w-full bg-[#212121] border border-[#666666] hover:border-[#ff5722] rounded-full py-2 pl-9 pr-3 text-sm text-gray-200 placeholder-gray-400 focus:outline-none focus:ring-0 focus:border-[#ff5722]"
                        aria-label="Search members"
                    >
                    
                    <!-- Search Icon (Inside Input) -->
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="h-4 w-4 text-[#ff5722]" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    
                    <!-- Clear Button (Only When Search Active) -->
                    @if(request('search'))
                    <a 
                        href="{{ route('staff.attendance.index') }}" 
                        class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-200 hover:text-[#ff5722] transition duration-150 ease-in-out"
                        aria-label="Clear search"
                    >
                        <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </a>
                    @endif
                </div>
            </form>

            <div class="relative w-full sm:w-auto">
                <button id="select-btn" class="w-full px-6 py-2 text-gray-200 bg-[#212121] border border-[#666666] hover:border-[#ff5722] rounded-lg flex justify-between items-center">
                    <span id="selected-option">Today</span>
                    <svg class="ml-2 w-5 h-5 text-gray-200" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                    </svg>
                </button>

                <ul id="dropdown" class="hidden absolute left-0 w-full bg-[#212121] rounded-lg mt-2 overflow-hidden z-10">
                    <li class="px-1 py-1 text-gray-200 cursor-pointer hover:bg-[#ff5722]" data-value="today">Today</li>
                    <li class="px-1 py-1 text-gray-200 cursor-pointer hover:bg-[#ff5722]" data-value="yesterday">Yesterday</li>
                    <li class="px-1 py-1 text-gray-200 cursor-pointer hover:bg-[#ff5722]" data-value="thisWeek">This Week</li>
                    <li class="px-1 py-1 text-gray-200 cursor-pointer hover:bg-[#ff5722]" data-value="lastWeek">Last Week</li>
                    <li class="px-1 py-1 text-gray-200 cursor-pointer hover:bg-[#ff5722]" data-value="thisMonth">This Month</li>
                </ul>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-black">
                <thead class="bg-gradient-to-br from-[#2c2c2c] to-[#1e1e1e] border-b border-black">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-gray-200 uppercase tracking-wider">Member</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-gray-200 uppercase tracking-wider">Membership</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-gray-200 uppercase tracking-wider">Check-in</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-gray-200 uppercase tracking-wider">Check-out</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-gray-200 uppercase tracking-wider">Duration</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-gray-200 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-black">
                    @forelse($attendances as $attendance)
                    <tr class="@if($loop->even) bg-[#1e1e1e] @else bg-[#1e1e1e] @endif">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="ml-4">
                                    <div class="text-sm font-medium text-gray-200">{{ $attendance->user ? $attendance->user->first_name . ' ' . $attendance->user->last_name : 'Unknown' }}</div>
                                </div>
                            </div>
                        </td>
                        
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($attendance->user)
                            <span class="px-2.5 py-1 inline-flex text-xs leading-5 font-semibold rounded-full 
                                @if($attendance->user->getMembershipType() == 'Annual') bg-purple-900 text-purple-200
                                @elseif($attendance->user->getMembershipType() == 'Week') bg-green-900 text-green-200
                                @elseif($attendance->user->getMembershipType() == 'Month') bg-blue-900 text-blue-200
                                @elseif($attendance->user->getMembershipType() == 'Session') bg-yellow-900 text-yellow-200
                                @endif">
                                {{ $attendance->user->getMembershipType() }}
                            </span>
                            @else
                            <span class="px-2.5 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-700 text-gray-200">
                                Unknown
                            </span>
                            @endif
                        </td>
                        
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-200">{{ $attendance->time_in->format('h:i A') }}</div>
                            <div class="text-xs text-gray-400">{{ $attendance->time_in->format('M d, Y') }}</div>
                        </td>
                        
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($attendance->time_out)
                                <div class="text-sm text-gray-200">{{ $attendance->time_out->format('h:i A') }}</div>
                                <div class="text-xs text-gray-400">{{ $attendance->time_out->format('M d, Y') }}</div>
                            @else
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    <span class="h-1.5 w-1.5 mr-1.5 rounded-full bg-green-400 animate-pulse"></span>
                                    In Session
                                </span>
                            @endif
                        </td>
                        
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-200">
                            {{ $attendance->formatted_duration ?? 'N/A' }}
                        </td>
                        
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                        @if($attendance->user)
                        <button 
                            class="text-gray-200 hover:text-gray-200 hover:translate-y-[-2px] bg-transparent border border-[#ff5722] hover:bg-[#ff5722] px-3 py-1 rounded-md transition-colors duration-150"
                            @click="openModal({
                                user: {
                                    first_name: '{{ addslashes($attendance->user->first_name) }}',
                                    last_name: '{{ addslashes($attendance->user->last_name) }}',
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
                                time_out: {{ $attendance->time_out ? "'".$attendance->time_out->toISOString()."'" : 'null' }},
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
                        <div class="flex flex-col items-center justify-center py-8">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                            </svg>
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
        <div x-show="showModal" class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 z-50" @click.away="showModal = false">
            <div class="bg-[#1e1e1e] rounded-lg shadow-lg p-6 w-full max-w-md" @click.stop>
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-xl font-bold bg-clip-text text-transparent bg-gradient-to-r from-red-600 to-orange-600">Attendance Details</h2>
                    <button @click="showModal = false" class="p-2 text-gray-200 rounded-full hover:bg-[#ff5722] hover:scale-95 transition-transform">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
                
                <div class="mb-6">
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
                            <p class="text-gray-200" x-text="selectedDayDate || (selectedAttendance ? new Date(selectedAttendance.time_in).toLocaleDateString([], { month: 'short', day: 'numeric', year: 'numeric' }) : '')"></p>
                        </div>
                        <div class="mt-2">
                            <p class="text-sm text-gray-400">Duration</p>
                            <p class="text-gray-200" x-text="selectedDayDuration || (selectedAttendance ? selectedAttendance.formatted_duration : 'N/A')"></p>
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-2 gap-4 mb-4">
                        <div>
                            <p class="text-sm text-gray-400">Check-in</p>
                            <p class="text-gray-200" x-text="selectedDayCheckIn || (selectedAttendance ? new Date(selectedAttendance.time_in).toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'}) : 'N/A')"></p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-400">Check-out</p>
                            <p class="text-gray-200" x-text="selectedDayCheckOut || (selectedAttendance && selectedAttendance.time_out ? new Date(selectedAttendance.time_out).toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'}) : 'N/A')"></p>
                        </div>
                    </div>
                    
                    
                </div>

                <!-- Enhanced Calendar Section -->
                <div class="mt-4" x-data="{
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
                        $data.selectedDayCheckIn = null;
                        $data.selectedDayCheckOut = null;
                        $data.selectedDayDate = null;
                        $data.selectedDayDuration = null;
                    },
                    
                    nextMonth() {
                        if (this.currentMonth === 11) {
                            this.currentMonth = 0;
                            this.currentYear++;
                        } else {
                            this.currentMonth++;
                        }
                        this.selectedDay = null;
                        $data.selectedDayCheckIn = null;
                        $data.selectedDayCheckOut = null;
                        $data.selectedDayDate = null;
                        $data.selectedDayDuration = null;
                    },
                    
                    monthName() {
                        return new Date(this.currentYear, this.currentMonth).toLocaleString('default', { month: 'long' });
                    },
                    
                    isAttendanceDay(day) {
                        if (!$data.selectedAttendance?.user?.attendances) return false;
                        
                        const currentDate = new Date(this.currentYear, this.currentMonth, day);
                        
                        return $data.selectedAttendance.user.attendances.some(attendance => {
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
                        $data.selectedDayDate = selectedDate.toLocaleDateString([], { month: 'short', day: 'numeric', year: 'numeric' });
                        
                        // Reset values
                        $data.selectedDayCheckIn = 'N/A';
                        $data.selectedDayCheckOut = 'N/A';
                        $data.selectedDayDuration = 'N/A';
                        
                        if (!$data.selectedAttendance?.user?.attendances) {
                            return;
                        }
                        
                        const attendanceForDay = $data.selectedAttendance.user.attendances.find(attendance => {
                            const attendanceDate = new Date(attendance.time_in);
                            return attendanceDate.getFullYear() === selectedDate.getFullYear() &&
                                attendanceDate.getMonth() === selectedDate.getMonth() &&
                                attendanceDate.getDate() === selectedDate.getDate();
                        });
                        
                        if (attendanceForDay) {
                            $data.selectedDayCheckIn = new Date(attendanceForDay.time_in).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
                            $data.selectedDayCheckOut = attendanceForDay.time_out ? 
                                new Date(attendanceForDay.time_out).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' }) : 'N/A';
                            $data.selectedDayDuration = attendanceForDay.formatted_duration || 'N/A';
                        }
                    },
                    
                    isSelectedDay(day) {
                        return this.selectedDay === day;
                    }
                }" x-init="
                    $nextTick(() => {
                    console.log('Modal initialized', $data.selectedAttendance);
                        if ($data.selectedAttendance?.time_in) {
                            const date = new Date($data.selectedAttendance.time_in);
                            currentMonth = date.getMonth();
                            currentYear = date.getFullYear();
                            selectedDay = date.getDate();
                            loadDayAttendance(selectedDay);
                        }
                    })
                ">
                    <!-- Month Navigation -->
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
                    <div class="grid grid-cols-7 gap-1 text-center">
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
                                >
                                </button>
                                <!-- Dot indicator for attendance days -->
                                <div x-show="isAttendanceDay(day) && !isSelectedDay(day)" 
                                    class="mt-1 w-1.5 h-1.5 bg-[#ff5722] rounded-full"></div>
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
    document.addEventListener('DOMContentLoaded', function() {
        const selectBtn = document.getElementById('select-btn');
        const dropdown = document.getElementById('dropdown');
        const selectedOption = document.getElementById('selected-option');
        const currentFilter = new URLSearchParams(window.location.search).get('filter') || 'today';

        // Set initial selected option
        const initialOption = document.querySelector(`[data-value="${currentFilter}"]`);
        if (initialOption) {
            selectedOption.textContent = initialOption.textContent;
        }

        selectBtn.addEventListener('click', () => {
            dropdown.classList.toggle('hidden');
        });

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
                
                // Update URL
                window.location.href = `${url.pathname}?${searchParams.toString()}`;
            });
        });

        window.addEventListener('click', (e) => {
            if (!selectBtn.contains(e.target) && !dropdown.contains(e.target)) {
                dropdown.classList.add('hidden');
            }
        });
    }); 
</script>
@endsection