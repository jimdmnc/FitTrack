@extends('layouts.app')

@section('content')
<style>
    [x-cloak] { display: none !important; }
</style>
<div class="py-8 sm:px-6 lg:px-4" x-data="{
    showModal: false,
    selectedAttendance: null,
    openModal(attendance) {
        if (typeof attendance === 'object') {
            this.selectedAttendance = attendance;
        } else {
            this.selectedAttendance = JSON.parse(attendance);
        }
        this.showModal = true;
    }
}" x-init="showModal = false" x-cloak>
    <div class="mb-6">
        <h1 class="text-3xl pb-1 md:text-4xl font-bold bg-clip-text text-transparent bg-gradient-to-r from-red-600 to-orange-600">
            Gym Member Attendance
        </h1>
        <p class="mt-1 ml-2 text-sm text-gray-300">Track member check-ins and check-outs</p>
    </div>

    <div class="">
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between px-6 py-4 border-b border-gray-800 gap-4">
            <form method="GET" action="{{ route('staff.attendance.index') }}" class="w-full sm:w-auto">
                <div class="flex items-center space-x-6">
                    <div class="flex w-full">
                        <input 
                            type="text" 
                            name="search" 
                            value="{{ request('search') }}" 
                            placeholder="Search by name" 
                            class="w-full px-4 py-2 border border-[#666666] hover:border-[#ff5722] text-gray-300 bg-[#212121] placeholder-gray-400 rounded-l-full focus:outline-none focus:ring-0 focus:border-[#ff5722]"
                            aria-label="Search members"
                        >
                        <button 
                            type="submit" 
                            class="px-6 py-2 bg-[#ff5722] text-white rounded-r-full hover:bg-[#e64a19] hover:scale-95 transition duration-300"
                            aria-label="Search"
                        >
                            Search
                        </button>
                    </div>

                    @if(request('search'))
                    <a 
                        href="{{ route('staff.attendance.index') }}" 
                        class="px-3 py-2 text-gray-200 bg-transparent hover:bg-[#ff5722] border border-[#666666] rounded-full focus:outline-none transition duration-150 ease-in-out flex items-center"
                    >
                        <svg class="h-4 w-4 mr-1" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                        Clear
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
                    <li class="px-1 py-1 text-gray-200 cursor-pointer hover:bg-[#ff5722]" data-value="all">All</li>
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
                    @foreach($attendances as $attendance)
                    <tr class="@if($loop->even) bg-[#1e1e1e] @else bg-[#1e1e1e] @endif">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="ml-4">
                                    <div class="text-sm font-medium text-gray-200">{{ $attendance->user ? $attendance->user->first_name . ' ' . $attendance->user->last_name : 'Unknown' }}</div>
                                </div>
                            </div>
                        </td>
                        
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2.5 py-1 inline-flex text-xs leading-5 font-semibold rounded-full 
                                @if($attendance->user->getMembershipType() == 'Annual') bg-purple-900 text-purple-200
                                @elseif($attendance->user->getMembershipType() == 'Week') bg-green-900 text-green-200
                                @elseif($attendance->user->getMembershipType() == 'Month') bg-blue-900 text-blue-200
                                @elseif($attendance->user->getMembershipType() == 'Session') bg-yellow-900 text-yellow-200
                                @endif">
                                {{ $attendance->user->getMembershipType() }}
                            </span>
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
                            {{ $attendance->formatted_duration }}
                        </td>
                        
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                        <button 
                            class="text-gray-200 hover:text-gray-200 hover:scale-95 bg-transparent border border-[#ff5722] hover:bg-[#ff5722] px-3 py-1 rounded-md transition-colors duration-150"
                            @click="openModal({
                                user: {
                                    first_name: '{{ $attendance->user->first_name }}',
                                    last_name: '{{ $attendance->user->last_name }}',
                                    getMembershipType: '{{ $attendance->user->getMembershipType() }}',
                                    all_attendances: [
                                        @foreach($attendance->user->attendances as $userAttendance)
                                        {
                                            time_in: '{{ $userAttendance->time_in->format('Y-m-d\TH:i:s.v\Z') }}',
                                            time_out: {{ $userAttendance->time_out ? "'".$userAttendance->time_out->format('Y-m-d\TH:i:s.v\Z')."'" : 'null' }},
                                        },
                                        @endforeach
                                    ]
                                },
                                time_in: '{{ $attendance->time_in->format('Y-m-d\TH:i:s.v\Z') }}',
                                time_out: {{ $attendance->time_out ? "'".$attendance->time_out->format('Y-m-d\TH:i:s.v\Z')."'" : 'null' }},
                                formatted_duration: '{{ $attendance->formatted_duration }}'
                            })"
                        >
                            Details
                        </button>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Calendar Modal -->
        <div x-show="showModal" class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 z-50" x-cloak @click.away="showModal = false">
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
                            <p class="text-sm text-gray-400" x-text="selectedAttendance ? selectedAttendance.user.getMembershipType : ''"></p>
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <p class="text-sm text-gray-400">Check-in</p>
                            <p class="text-gray-200" x-text="selectedAttendance ? new Date(selectedAttendance.time_in).toLocaleString('en-US', { timeZone: 'UTC', month: 'short', day: 'numeric', year: 'numeric', hour: '2-digit', minute: '2-digit' }) : ''"></p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-400">Check-out</p>
                            <p class="text-gray-200" x-text="selectedAttendance ? (selectedAttendance.time_out ? new Date(selectedAttendance.time_out).toLocaleString('en-US', { timeZone: 'UTC', month: 'short', day: 'numeric', year: 'numeric', hour: '2-digit', minute: '2-digit' }) : 'In Session') : ''"></p>
                        </div>
                    </div>
                </div>

                <!-- Calendar Section -->
                <div class="mt-4" x-data="{
                currentMonth: new Date().getUTCMonth(),
                currentYear: new Date().getUTCFullYear(),
                attendanceDay: null,
                attendanceMonth: null,
                attendanceYear: null,
                
                init() {
                    if (this.selectedAttendance?.time_in) {
                        const date = new Date(this.selectedAttendance.time_in);
                        this.currentMonth = date.getUTCMonth();
                        this.currentYear = date.getUTCFullYear();
                        this.attendanceDay = date.getUTCDate();
                        this.attendanceMonth = date.getUTCMonth();
                        this.attendanceYear = date.getUTCFullYear();
                    }
                },
                
                getDaysInMonth() {
                    return new Date(Date.UTC(this.currentYear, this.currentMonth + 1, 0)).getUTCDate();
                },
                
                getFirstDayOfMonth() {
                    return new Date(Date.UTC(this.currentYear, this.currentMonth, 1)).getUTCDay();
                },
                
                prevMonth() {
                    if (this.currentMonth === 0) {
                        this.currentMonth = 11;
                        this.currentYear--;
                    } else {
                        this.currentMonth--;
                    }
                },
                
                nextMonth() {
                    if (this.currentMonth === 11) {
                        this.currentMonth = 0;
                        this.currentYear++;
                    } else {
                        this.currentMonth++;
                    }
                },
                
                monthName() {
                    return new Date(Date.UTC(this.currentYear, this.currentMonth)).toLocaleString('default', { month: 'long' });
                },
                
                isAttendanceDay(day) {
                    if (!this.selectedAttendance?.user?.all_attendances) return false;
                    
                    const currentDate = new Date(Date.UTC(this.currentYear, this.currentMonth, day));
                    
                    return this.selectedAttendance.user.all_attendances.some(attendance => {
                        const attendanceDate = new Date(attendance.time_in);
                        return attendanceDate.getUTCFullYear() === currentDate.getUTCFullYear() &&
                            attendanceDate.getUTCMonth() === currentDate.getUTCMonth() &&
                            attendanceDate.getUTCDate() === currentDate.getUTCDate();
                    });
                },
                
                isCurrentAttendanceDay(day) {
                    if (!this.selectedAttendance?.time_in) return false;
                    const date = new Date(this.selectedAttendance.time_in);
                    return day === date.getUTCDate() && 
                        this.currentMonth === date.getUTCMonth() && 
                        this.currentYear === date.getUTCFullYear();
                }
            }">
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
                        <div class="text-sm rounded-full w-6 h-6 flex items-center justify-center transition-colors"
                            :class="{
                                'text-gray-300 hover:bg-gray-700': !isAttendanceDay(day),
                                'text-white': isAttendanceDay(day) && !isCurrentAttendanceDay(day),
                                'text-white font-extrabold': isCurrentAttendanceDay(day)
                            }"
                            x-text="day">
                </div>
                <!-- Dot indicator for all attendance days except current -->
                <div x-show="isAttendanceDay(day)" 
                    class="mt-1 w-1.5 h-1.5 bg-[#ff5722] rounded-full"></div>
            </div>
        </template>
    </div>
</div>
            </div>
        </div>
        <div class="mt-4">
            {{ $attendances->links('vendor.pagination.default') }}
        </div>
    </div>
</div>

<script>
    const selectBtn = document.getElementById('select-btn');
    const dropdown = document.getElementById('dropdown');
    const selectedOption = document.getElementById('selected-option');
    const currentFilter = new URLSearchParams(window.location.search).get('filter') || 'all';
    
    // Set initial selected option
    selectedOption.textContent = document.querySelector(`[data-value="${currentFilter}"]`).textContent;

    selectBtn.addEventListener('click', () => {
        dropdown.classList.toggle('hidden');
    });

    dropdown.querySelectorAll('li').forEach(option => {
        option.addEventListener('click', () => {
            const filterValue = option.getAttribute('data-value');
            selectedOption.textContent = option.textContent;
            dropdown.classList.add('hidden');
            
            // Update URL with the selected filter
            const url = new URL(window.location.href);
            url.searchParams.set('filter', filterValue);
            window.location.href = url.toString();
        });
    });

    window.addEventListener('click', (e) => {
        if (!selectBtn.contains(e.target) && !dropdown.contains(e.target)) {
            dropdown.classList.add('hidden');
        }
    });
</script>
@endsection