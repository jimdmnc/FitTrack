@if($members->isEmpty())
    <div class="mt-8 text-center">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 200 160" class="w-32 h-32 mx-auto mb-4 text-gray-300">
            <rect x="40" y="30" width="120" height="90" rx="8" fill="#f3f4f6"/>
            <path d="M60 50 H140" stroke="#d1d5db" stroke-width="2" stroke-linecap="round"/>
            <path d="M60 70 H140" stroke="#d1d5db" stroke-width="2" stroke-linecap="round"/>
            <path d="M60 90 H140" stroke="#d1d5db" stroke-width="2" stroke-linecap="round"/>
            <circle cx="110" cy="130" r="20" fill="#e5e7eb"/>
            <path d="M101 130 L119 130" stroke="#9ca3af" stroke-width="2" stroke-linecap="round"/>
            <path d="M110 121 L110 139" stroke="#9ca3af" stroke-width="2" stroke-linecap="round"/>
            <g opacity="0.6">
                <circle cx="70" cy="20" r="6" fill="#d1d5db"/>
                <circle cx="130" cy="140" r="4" fill="#d1d5db"/>
                <circle cx="180" cy="80" r="5" fill="#d1d5db"/>
                <circle cx="20" cy="100" r="3" fill="#d1d5db"/>
            </g>
        </svg>
        <p class="text-lg font-medium text-gray-500">No members found</p>
        <p class="text-sm text-gray-500">Please check your search criteria and try again.</p>
    </div>
@else
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-black">
            <thead>
                <tr class="bg-gradient-to-br from-[#2c2c2c] to-[#1e1e1e] rounded-lg">
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-200 uppercase tracking-wider">#</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-200 uppercase tracking-wider">Name</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-200 uppercase tracking-wider">Member ID</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-200 uppercase tracking-wider">Membership Type</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-200 uppercase tracking-wider">Registration Date</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-200 uppercase tracking-wider">Status</th>
                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-200 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-black">
                @foreach ($members as $member)
                <tr class="bg-[#1e1e1e] transition-colors member-table-row" data-status="{{ $member->member_status }}">
                    <td class="px-4 py-4 text-sm text-gray-200">{{ ($members->currentPage() - 1) * $members->perPage() + $loop->iteration }}</td>
                    <td class="px-4 py-4 whitespace-nowrap">
                        <div class="flex items-center">
                            <div class="h-10 w-10 flex-shrink-0 mr-3">
                                <div class="h-10 w-10 rounded-full bg-blue-100 flex items-center justify-center">
                                    <span class="text-gray-800 font-semibold">
                                        {{ strtoupper(substr($member->first_name, 0, 1)) . strtoupper(substr($member->last_name, 0, 1)) }}
                                    </span>
                                </div>
                            </div>
                            <div>
                                <div class="text-sm font-medium text-gray-200">{{ $member->first_name }} {{ $member->last_name }}</div>
                            </div>
                        </div>
                    </td>
                    <td class="px-4 py-4 text-sm text-gray-200">{{ $member->rfid_uid }}</td>
                    <td class="px-4 py-4 whitespace-nowrap">
                        <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full 
                            @if($member->getMembershipType() == 'Annual') bg-purple-900 text-purple-200
                            @elseif($member->getMembershipType() == 'Week') bg-green-900 text-green-200
                            @elseif($member->getMembershipType() == 'Month') bg-blue-900 text-blue-200
                            @elseif($member->getMembershipType() == 'Session') bg-yellow-900 text-yellow-200
                            @endif">
                            {{ $member->getMembershipType() }}
                        </span>
                    </td>
                    <td class="px-4 py-4 text-sm text-gray-200">{{ \Carbon\Carbon::parse($member->start_date)->format('M d, Y') }}</td>
                    <td class="px-4 py-4 whitespace-nowrap">
                        <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full 
                            {{ $member->member_status == 'active' ? ' text-green-400' : ' text-red-400' }}">
                            {{ $member->member_status }}
                        </span>
                    </td>
                    <td class="px-4 py-4 text-right text-sm">
                        @if($member->member_status == 'active')
                            <button onclick="openViewModal('{{ $member->rfid_uid }}', '{{ $member->first_name }} {{ $member->last_name }}', '{{ $member->getMembershipType() }}', '{{ \Carbon\Carbon::parse($member->start_date)->format('M d, Y') }}', '{{ $member->member_status }}')" class="inline-flex items-center px-3 py-1.5 bg-transparent hover:bg-[#ff5722] hover:translate-y-[-2px] text-gray-200 rounded-lg transition-all duration-200 font-medium text-sm border border-[#ff5722] shadow-sm" aria-label="View details for {{ $member->first_name }} {{ $member->last_name }}" title="View member details">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                                View
                            </button>
                        @elseif($member->member_status == 'expired')
                            <button onclick="openRenewModal('{{ $member->rfid_uid }}', '{{ $member->first_name }} {{ $member->last_name }}', '{{ $member->email }}', '{{ $member->phone_number }}', '{{ $member->end_date }}')" class="inline-flex items-center px-3 py-1.5 bg-transparent hover:bg-[#ff5722] hover:translate-y-[-2px] text-gray-200 rounded-lg transition-all duration-200 font-medium text-sm border border-[#ff5722] shadow-sm" aria-label="Renew membership for {{ $member->first_name }} {{ $member->last_name }}" title="Renew expired membership">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                </svg>
                                Renew
                            </button>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endif