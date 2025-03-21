@extends('layouts.app')

@section('content')
<div class="py-6 sm:px-6 lg:px-4">
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Gym Member Attendance</h1>
        <p class="mt-1 text-sm text-gray-600">Track member check-ins and check-outs</p>
    </div>

    <div class="bg-white shadow overflow-hidden rounded-lg">
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between px-6 py-4 border-b border-gray-200 gap-4">
<form method="GET" action="{{ route('staff.attendance.index') }}" class="w-full sm:w-auto">
    <div class="flex items-center space-x-2">
        <!-- Search Input with Icon -->
        <div class="relative w-full">
            <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd" />
                </svg>
            </div>
            <input 
                type="text" 
                name="search" 
                value="{{ request('search') }}" 
                placeholder="Search by name" 
                class="block w-full pl-10 py-2 border border-gray-300 rounded-md leading-5 bg-white placeholder-gray-500 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                aria-label="Search members"
            >
        </div>

        <!-- Search Button (Outside Input Field) -->
        <button 
            type="submit" 
            class="px-3 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 focus:outline-none transition duration-150 ease-in-out flex items-center"
            aria-label="Search"
        >
            <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
            </svg>
        </button>

        <!-- Clear Button (Appears Only If Search is Active) -->
        @if(request('search'))
        <a 
            href="{{ route('staff.attendance.index') }}" 
            class="px-3 py-2 text-gray-600 hover:text-gray-800 bg-gray-200 rounded-md focus:outline-none transition duration-150 ease-in-out flex items-center"
        >
            <svg class="h-4 w-4 mr-1" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
            Clear
        </a>
        @endif
    </div>
</form>



            <div class="flex space-x-2 w-full sm:w-auto">
                <div class="w-full sm:w-auto">
                    <select id="date-filter" class="block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md">
                        <option value="today">Today</option>
                        <option value="yesterday">Yesterday</option>
                        <option value="thisWeek">This Week</option>
                        <option value="lastWeek">Last Week</option>
                        <option value="thisMonth">This Month</option>
                    </select>
                </div>

            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Member</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Membership</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Check-in</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Check-out</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Duration</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($attendances as $attendance)
                    <tr class="@if($loop->even) bg-gray-100 @else bg-white @endif hover:bg-indigo-50 transition-colors duration-150">
                        <!-- Member -->
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 h-10 w-10 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-600 font-medium">
                                    {{ substr($attendance->user->first_name, 0, 1) }}{{ substr($attendance->user->last_name, 0, 1) }}
                                </div>
                                <div class="ml-4">
                                    <div class="text-sm font-medium text-gray-900">{{ $attendance->user ? $attendance->user->first_name . ' ' . $attendance->user->last_name : 'Unknown' }}</div>
                                </div>
                            </div>
                        </td>
                        
                        <!-- Membership Type -->
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2.5 py-1 inline-flex text-xs leading-5 font-semibold rounded-full 
                                @if($attendance->user->getMembershipType() == 'Annual') bg-purple-100 text-purple-800
                                @elseif($attendance->user->getMembershipType() == 'Week') bg-green-100 text-green-800
                                @elseif($attendance->user->getMembershipType() == 'Month') bg-blue-100 text-blue-800
                                @elseif($attendance->user->getMembershipType() == 'Session') bg-yellow-100 text-yellow-800
                                @endif">
                                {{ $attendance->user->getMembershipType() }}
                            </span>
                        </td>
                        
                        <!-- Check-in Time -->
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">{{ $attendance->time_in->format('h:i A') }}</div>
                            <div class="text-xs text-gray-500">{{ $attendance->time_in->format('M d, Y') }}</div>
                        </td>
                        
                        <!-- Check-out Time -->
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($attendance->time_out)
                                <div class="text-sm text-gray-900">{{ $attendance->time_out->format('h:i A') }}</div>
                                <div class="text-xs text-gray-500">{{ $attendance->time_out->format('M d, Y') }}</div>
                            @else
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    <span class="h-1.5 w-1.5 mr-1.5 rounded-full bg-green-400 animate-pulse"></span>
                                    In Session
                                </span>
                            @endif
                        </td>
                        
                        <!-- Duration -->
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ $attendance->formatted_duration }}
                        </td>
                        
                        <!-- Actions -->
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <button class="text-indigo-600 hover:text-indigo-900 bg-indigo-50 hover:bg-indigo-100 px-3 py-1 rounded-md transition-colors duration-150">
                                Details
                            </button>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    <div class="mt-4">
        <!-- Custom Pagination Links -->
        {{ $attendances->links('vendor.pagination.default') }}
    </div>
    </div>
</div>
@endsection