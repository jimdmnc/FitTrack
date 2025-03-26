@extends('layouts.app')

@section('content')
<div class="py-8 sm:px-6 lg:px-4">
    <div class="mb-6">
    <h1 class="text-3xl pb-1 md:text-4xl font-bold bg-clip-text text-transparent bg-gradient-to-r from-red-600 to-orange-600">
                    Gym Member Attendance
                </h1>
        <p class="mt-1 ml-2 text-sm text-gray-300">Track member check-ins and check-outs</p>
    </div>

    <div class="bg-[#1E1E1E] shadow overflow-hidden rounded-lg">
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between px-6 py-4 border-b border-gray-800 gap-4">
<form method="GET" action="{{ route('staff.attendance.index') }}" class="w-full sm:w-auto">
    <div class="flex items-center space-x-2">
        <!-- Search Input with Icon -->
        <div class="flex w-full">
  <!-- Search Input Field -->
  <input 
    type="text" 
    name="search" 
    value="{{ request('search') }}" 
    placeholder="Search by name" 
    class="w-full px-4 py-2 border border-[#ff5722] text-gray-300 bg-[#1e1e1e] rounded-l-full focus:outline-none focus:ring-0"
    aria-label="Search members"
  >

  <!-- Search Button -->
  <button 
    type="submit" 
    class="px-6 py-2 bg-[#ff5722] text-white rounded-r-full hover:bg-[#e64a19] transition duration-300"
    aria-label="Search"
  >
    Search
  </button>
</div>

        <!-- Clear Button (Appears Only If Search is Active) -->
        @if(request('search'))
        <a 
            href="{{ route('staff.attendance.index') }}" 
            class="px-3 py-2 text-gray-600 hover:text-gray-800 bg-gray-300 rounded-md focus:outline-none transition duration-150 ease-in-out flex items-center"
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
                    <select id="date-filter" class="block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-[#ff5722] focus:border-[#ff5722] sm:text-sm rounded-md">
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
                <thead class="bg-[#2c2c2c]">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-gray-200 uppercase tracking-wider">Member</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-gray-200 uppercase tracking-wider">Membership</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-gray-200 uppercase tracking-wider">Check-in</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-gray-200 uppercase tracking-wider">Check-out</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-gray-200 uppercase tracking-wider">Duration</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-gray-200 uppercase tracking-wider">Actions</th>
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