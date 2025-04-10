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
                    time_out: {{ $attendance->time_out ? "'".$attendance->time_out->toISOString()."'" : 'null' }}',
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
