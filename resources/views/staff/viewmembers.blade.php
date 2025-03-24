

        @extends('layouts.app') <!-- Assuming you have a main layout file -->

@section('content')

    <section class="pt-10 mb-8">
        <div class=" bg-white p-6 rounded-lg shadow-lg shadow-gray-400 border border-gray-200">
            <div class="flex flex-col md:flex-row justify-between items-center gap-y-4 md:gap-y-0">
                <h2 class="font-extrabold text-lg sm:text-3xl text-gray-800">
                    <span class="bg-clip-text text-transparent bg-gradient-to-r from-blue-600 to-indigo-700 leading-snug">Gym Members</span>
                </h2>
            </div>
        </div>
    </section>

<section class="mt-6 border border-white rounded-lg p-4 bg-white text-gray-700">
        <div class="flex flex-col sm:flex-row justify-between items-center gap-4 mb-6">
            <!-- Filter Dropdown -->
            <div class="w-full sm:w-auto">
                <form action="{{ route('staff.viewmembers') }}" method="GET" class="inline-block w-full sm:w-auto">
                    <select 
                        name="status" 
                        onchange="this.form.submit()" 
                        class="w-full sm:w-auto appearance-none bg-white border border-gray-200 px-4 py-2 pr-8 rounded text-sm text-gray-700 focus:outline-none focus:ring-1 focus:ring-gray-300 focus:border-gray-300"
                        aria-label="Filter members by status"
                    >
                        <option value="all" {{ request('status') == 'all' ? 'selected' : '' }}>All Members</option>
                        <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active Members</option>
                        <option value="expired" {{ request('status') == 'expired' ? 'selected' : '' }}>Expired Members</option>
                    </select>
                </form>
            </div>

            <!-- Search Form -->
            <form method="GET" action="{{ route('staff.viewmembers') }}" class="w-full sm:w-64 md:w-80">
                <div class="relative flex items-center">
                    <!-- Search Input -->
                    <input 
                        type="text" 
                        name="search" 
                        value="{{ $query }}" 
                        placeholder="Search members" 
                        class="w-full border border-gray-200 rounded py-2 pl-9 pr-3 text-sm text-gray-700 placeholder-gray-400 focus:outline-none focus:ring-1 focus:ring-gray-300 focus:border-gray-300"
                        aria-label="Search members"
                    >
                    
                    <!-- Search Icon (Inside Input) -->
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="h-4 w-4 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    
                    <!-- Clear Button (Only When Search Active) -->
                    @if($query)
                    <a 
                        href="{{ route('staff.viewmembers') }}" 
                        class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-400 hover:text-gray-600"
                        aria-label="Clear search"
                    >
                        <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </a>
                    @endif
                </div>
            </form>
        </div>

        <div class="glass-card mt-5 ">
            <div class="overflow-x-auto">
                <!-- Success Message -->
@if(session('success'))
    <div class="max-w-4xl mx-auto bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg mb-6 flex justify-between items-center" role="alert">
        <div class="flex items-center">
            <svg class="h-5 w-5 text-green-500 mr-2" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
            </svg>
            <span>{{ session('success') }}</span>
        </div>
        <button type="button" class="text-green-500 hover:text-green-700" onclick="this.parentElement.style.display='none';">
            <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
            </svg>
        </button>
    </div>
@endif

<!-- Error Message -->
@if(session('error'))
    <div class="max-w-4xl mx-auto bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg mb-6 flex justify-between items-center" role="alert">
        <div class="flex items-center">
            <svg class="h-5 w-5 text-red-500 mr-2" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
            </svg>
            <span>{{ session('error') }}</span>
        </div>
        <button type="button" class="text-red-500 hover:text-red-700" onclick="this.parentElement.style.display='none';">
            <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
            </svg>
        </button>
    </div>
@endif
                <table class="min-w-full divide-y divide-gray-200">
                            <thead>
                                <tr class="bg-gray-50 rounded-lg">
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">#</th> <!-- Added this column -->
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Member ID</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Membership Type</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Registration Date</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                    <tbody class="divide-y divide-gray-200">
                        
                        @foreach ($members as $member)
                        
                        <tr class="hover:bg-gray-50 transition-colors member-table-row" data-status="{{ $member->member_status }}">
                            
                            <td class="px-4 py-4 text-sm text-gray-500">{{ $loop->iteration }}</td> 
                            <td class="px-4 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="h-10 w-10 flex-shrink-0 mr-3">
                                        <div class="h-10 w-10 rounded-full bg-blue-100 flex items-center justify-center">
                                            <span class="text-blue-600 font-semibold">
                                                {{ strtoupper(substr($member->first_name, 0, 1)) . strtoupper(substr($member->last_name, 0, 1)) }}
                                            </span>
                                        </div>
                                    </div>
                                    <div>
                                        <div class="text-sm font-medium text-gray-900">{{ $member->first_name }} {{ $member->last_name }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-4 text-sm text-gray-500">{{ $member->rfid_uid }}</td>

                            <td class="px-4 py-4 whitespace-nowrap">
                                <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full 
                                    @if($member->getMembershipType() == 'Annual') bg-purple-100 text-purple-800
                                    @elseif($member->getMembershipType() == 'Week') bg-green-100 text-green-800
                                    @elseif($member->getMembershipType() == 'Month') bg-blue-100 text-blue-800
                                    @elseif($member->getMembershipType() == 'Session') bg-yellow-100 text-yellow-800
                                    @endif">
                                    {{ $member->getMembershipType() }}
                                </span>
                            </td>
                            <td class="px-4 py-4 text-sm text-gray-500">{{ \Carbon\Carbon::parse($member->start_date)->format('M d, Y') }}</td>
                            <td class="px-4 py-4 whitespace-nowrap">
                                <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full 
                                    {{ $member->member_status == 'active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                    {{ $member->member_status }}
                                </span>
                            </td>
                        
                            <td class="px-4 py-4 text-right text-sm">
                                @if($member->member_status == 'active')
                                    <button 
                                        onclick="openViewModal('{{ $member->rfid_uid }}', '{{ $member->first_name }} {{ $member->last_name }}', '{{ $member->getMembershipType() }}', '{{ \Carbon\Carbon::parse($member->start_date)->format('M d, Y') }}', '{{ $member->member_status }}')"
                                        class="inline-flex items-center px-3 py-1.5 bg-indigo-50 hover:bg-indigo-100 text-indigo-600 hover:text-indigo-800 rounded-lg transition-all duration-200 font-medium text-sm border border-indigo-100 shadow-sm"
                                        aria-label="View details for {{ $member->first_name }} {{ $member->last_name }}"
                                        title="View member details"
                                    >
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        </svg>
                                        View
                                    </button>
                                @elseif($member->member_status == 'expired')
                                    <button 
                                        onclick="openRenewModal('{{ $member->rfid_uid }}', '{{ $member->first_name }} {{ $member->last_name }}', '{{ $member->email }}', '{{ $member->phone_number }}', '{{ $member->end_date }}')" 
                                        class="inline-flex items-center px-3 py-1.5 bg-green-50 hover:bg-green-100 text-green-600 hover:text-green-800 rounded-lg transition-all duration-200 font-medium text-sm border border-green-100 shadow-sm"
                                        aria-label="Renew membership for {{ $member->first_name }} {{ $member->last_name }}"
                                        title="Renew expired membership"
                                    >
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
                    <!-- no name found --> 
                    @if(isset($message) && $message) 
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
                            <p class="text-lg font-medium text-gray-500">{{ $message }}</p>
                            <p class="text-sm text-gray-500">Please check the name and try again.</p>
                        </div>
                    @endif
            </div>
                
            <!-- Pagination Links -->
            <div class="mt-4">
                <!-- Custom Pagination Links -->
                {{ $members->links('vendor.pagination.default') }}
            </div>
</section>






<!-- Renew Member Modal -->
<div id="renewMemberModal" class="fixed inset-0 bg-gray-900 bg-opacity-70 flex justify-center items-center hidden z-50 transition-opacity duration-300">
    <div class="bg-white rounded-xl shadow-2xl w-full max-w-2xl max-h-[90vh] overflow-y-auto transform transition-all duration-300 scale-95 opacity-0" id="editModalContent">
        <!-- Modal Header -->
        <div class="flex justify-between items-center p-4 border-b sticky top-0 bg-white z-10">
            <h2 class="text-lg font-bold text-gray-800 flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mr-2 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                </svg>
                Renew Membership
            </h2>
            <button onclick="closeRenewModal()" class="text-gray-500 hover:text-gray-700 hover:bg-gray-100 rounded-full p-1 transition-colors" aria-label="Close modal">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>




        <!-- Renew Form -->
        <form id="renewalForm" action="{{ route('renew.membership') }}" method="POST" class="p-6">
        @csrf

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1" for="editMemberID">Member ID</label>
                    <input type="text" name="rfid_uid" id="editMemberID" class="w-full px-3 py-2 border border-gray-300 rounded-lg bg-gray-100 text-gray-500 text-sm" readonly>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1" for="editMemberName">Name</label>
                    <input type="text" id="editMemberName" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1" for="membershipType">Membership Type</label>
                    <select id="membershipType" name="membership_type" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors appearance-none bg-white text-sm">
                    <option value="" selected disabled>Select Membership Type</option>
                        <option value="1" {{ old('membership_type') == '1' ? 'selected' : '' }}>Session (1 day)</option>
                        <option value="7" {{ old('membership_type') == '7' ? 'selected' : '' }}>Weekly (7 days)</option>
                        <option value="30" {{ old('membership_type') == '30' ? 'selected' : '' }}>Monthly (30 days)</option>
                        <option value="365" {{ old('membership_type') == '365' ? 'selected' : '' }}>Annual (365 days)</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1" for="startDate">Renewal Date</label>
                    <input type="date" id="startDate" name="start_date" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1" for="endDate">Expiration Date</label>
                    <input type="text" id="endDate" name="end_date" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors text-sm" readonly>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1" for="membershipFee">Base Fee ($)</label>
                    <input type="text" id="membershipFee" class="w-full px-3 py-2 border border-gray-300 rounded-lg bg-gray-100 text-gray-500 text-sm" readonly>
                </div>
            </div>
            
            <!-- Summary Box -->
            <div class="mt-4 bg-blue-50 p-4 rounded-lg border border-blue-100 flex items-start">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-blue-500 mt-0.5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <div class="ml-3 text-sm text-blue-800">
                    <span class="font-medium">Membership Summary:</span> Annual membership will be renewed. Total fee includes all applicable taxes.
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex justify-end space-x-2 mt-4 pt-3 border-t">
                <button type="submit" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-800 rounded-lg transition-colors text-sm">
                    Cancel
                </button>
                <button type="submit" class="px-5 py-2 bg-blue-500 hover:bg-blue-600 text-white rounded-lg transition-colors font-medium flex items-center text-sm">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    Complete Renewal
                </button>
            </div>
        </form>
    </div>
</div>
<!-- End Renew Member Modal -->
 
<!-- View Member Modal -->
        <div id="viewMemberModal" class="fixed inset-0 bg-gray-900 bg-opacity-70 flex justify-center items-center hidden z-50 transition-opacity duration-300">
            <div class="bg-white rounded-xl shadow-2xl w-full max-w-3xl p-6 transform transition-all duration-300 scale-95 opacity-0" id="viewModalContent">
                <!-- Modal Header -->
                <div class="flex justify-between items-center mb-6 border-b pb-3">
                    <h2 class="text-xl font-bold text-gray-800 flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mr-2 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                        Member Profile
                    </h2>
                    <button onclick="closeViewModal()" class="text-gray-500 hover:text-gray-700 hover:bg-gray-100 rounded-full p-1 transition-colors">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <!-- Horizontal ID Card Layout -->
                <div class="bg-white border-2 border-blue-500 rounded-lg overflow-hidden shadow-md">
                    <!-- Card Header -->
                    <div class="bg-blue-500 p-3 text-white">
                        <h3 class="font-bold text-center">MEMBER IDENTIFICATION</h3>
                    </div>
                    
                    <!-- Horizontal Layout Container -->
                    <div class="flex">
                        <!-- Left Column - Photo Area -->
                        <div class="w-1/4 p-4 flex flex-col items-center justify-center border-r">
                            <div class="w-32 h-32 bg-gray-100 border rounded-full flex items-center justify-center mb-3">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                            </div>
                            <!-- Status Badge -->
                            <span id="viewStatus" class="inline-block px-3 py-1 text-sm font-semibold rounded-full bg-green-100 text-green-800">Active</span>
                        </div>
                        
                        <!-- Middle Column - Primary Info -->
                        <div class="w-2/5 p-4 bg-white">
                            <!-- Name -->
                            <div class="mb-4">
                                <p class="text-xs text-gray-500 uppercase">Name</p>
                                <p class="font-bold text-gray-800 text-lg" id="viewMemberName">John Doe</p>
                            </div>
                            
                            <!-- Member ID -->
                            <div class="mb-4">
                                <p class="text-xs text-gray-500 uppercase">Member ID</p>
                                <p class="font-medium text-gray-800" id="viewMemberID">M12345678</p>
                            </div>
                            
                            <!-- Registration Date -->
                            <div class="flex items-center mb-4">
                                <div class="bg-yellow-100 p-2 rounded-full mr-3">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-yellow-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-500 uppercase">Registration Date</p>
                                    <p class="font-medium text-gray-800" id="viewStartDate">Jan 15, 2025</p>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Right Column - Membership Type & Barcode -->
                        <div class="w-1/3 p-4 bg-gray-50">
                            <!-- Membership Type -->
                            <div class="flex items-center mb-6">
                                <div class="bg-purple-100 p-2 rounded-full mr-3">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-purple-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-500 uppercase">Membership Type</p>
                                    <p class="font-medium text-gray-800" id="viewMembershipType">Premium</p>
                                </div>
                            </div>
                            
                            <!-- Barcode Area -->
                            <div class="mb-2">
                                <p class="text-xs text-gray-500 uppercase mb-1">Card ID</p>
                                <div class="bg-gray-100 h-12 flex items-center justify-center rounded">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-500 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 11c0 3.517-1.009 6.799-2.753 9.571m-3.44-2.04l.054-.09A13.916 13.916 0 008 11a4 4 0 118 0c0 1.017-.07 2.019-.203 3m-2.118 6.844A21.88 21.88 0 0015.171 17m3.839 1.132c.645-2.266.99-4.659.99-7.132A8 8 0 008 4.07M3 15.364c.64-1.319 1-2.8 1-4.364 0-1.457.39-2.823 1.07-4" />
                                    </svg>
                                    <span class="text-sm font-medium text-gray-600">ID: 123456789</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Footer -->
                    <div class="bg-blue-500 text-white text-center py-2 text-xs">
                        <p>Valid only with photo identification</p>
                    </div>
                </div>

                <!-- Modal Footer with Edit and Close Buttons -->
                <!-- <div class="flex justify-end mt-6">
                    <button onclick="openEditModal()" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg mr-2 transition-colors shadow-sm flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                        </svg>
                        Edit
                    </button>
                    <button onclick="closeViewModal()" class="bg-gray-100 hover:bg-gray-200 text-gray-800 px-4 py-2 rounded-lg transition-colors shadow-sm">Close</button>
                </div> -->
            </div>
        </div>

        
<script>
document.getElementById('membershipType').addEventListener('change', updateMembershipFee);
document.getElementById('startDate').addEventListener('change', updateExpirationDate);

function updateMembershipFee() {
    let membershipType = document.getElementById('membershipType').value;
    let membershipFeeInput = document.getElementById('membershipFee');

    // Define the membership fees
    let fees = {
        '1': 60,  // Session (1 day)
        '7': 300, // Weekly (7 days)
        '30': 1000, // Monthly (30 days)
        '365': 5000 // Annual (365 days)
    };

    membershipFeeInput.value = fees[membershipType] || 0;
}

function updateExpirationDate() {
    let membershipType = parseInt(document.getElementById('membershipType').value);
    let renewalDate = document.getElementById('startDate').value;
    let endDateInput = document.getElementById('endDate');

    if (renewalDate && membershipType) {
        let renewal = new Date(renewalDate);
        renewal.setDate(renewal.getDate() + membershipType);
        endDateInput.value = renewal.toISOString().split('T')[0];
    }
}



</script>







<script>
    document.getElementById('memberFilter').addEventListener('change', function () {
        const selectedStatus = this.value; // Get the selected value (active or expired)
        const rows = document.querySelectorAll('tbody tr[data-status]'); // Get all table rows

        rows.forEach(row => {
            const rowStatus = row.getAttribute('data-status'); // Get the row's status
            if (selectedStatus === 'all' || rowStatus === selectedStatus) {
                row.style.display = ''; // Show the row
            } else {
                row.style.display = 'none'; // Hide the row
            }
        });
    });



    
</script>     
<script>

    // Open View Modal
    function openViewModal(name, memberID, status, membershipType, startDate) {
        // Set modal data
        document.getElementById('viewMemberName').textContent = name;
        document.getElementById('viewMemberID').textContent = memberID;
        document.getElementById('viewStatus').textContent = status;
        document.getElementById('membershipType').textContent = membershipType;
        document.getElementById('startDate').textContent = startDate;

        // Change status color based on status
        let statusBadge = document.getElementById('viewStatus');
        if (status.toLowerCase() === 'active') {
            statusBadge.className = "inline-block px-2 py-1 text-sm font-semibold rounded-full bg-green-100 text-green-800";
        } else {
            statusBadge.className = "inline-block px-2 py-1 text-sm font-semibold rounded-full bg-red-100 text-red-800";
        }

        // Show modal
        const modal = document.getElementById('viewMemberModal');
        const modalContent = document.getElementById('viewModalContent');
        
        modal.classList.remove('hidden'); // Make it visible
        setTimeout(() => {
            modalContent.classList.remove('scale-95', 'opacity-0'); // Animate opening
            modalContent.classList.add('scale-100', 'opacity-100');
        }, 10);
    }

    // Function to close the modal
    function closeViewModal() {
        const modal = document.getElementById('viewMemberModal');
        const modalContent = document.getElementById('viewModalContent');

        // Animate closing
        modalContent.classList.remove('scale-100', 'opacity-100');
        modalContent.classList.add('scale-95', 'opacity-0');
        
        setTimeout(() => {
            modal.classList.add('hidden'); // Hide after animation
        }, 300);
    }

    // Close View Modal
    function closeViewModal() {
            const modal = document.getElementById('viewModalContent');
            modal.classList.remove('scale-100', 'opacity-100');
            modal.classList.add('scale-95', 'opacity-0');
            
            setTimeout(() => {
                document.getElementById("viewMemberModal").classList.add("hidden");

            }, 300);
    }

        // Open Renew Modal
        function openRenewModal(memberID, name, membershipType, memberStatus) {

            // Set form values
            document.getElementById("editMemberID").value = memberID;
            document.getElementById("editMemberName").value = name;
            document.getElementById("membershipType").value = membershipType;

            // Ensure status value is lowercase for comparison
            let formattedStatus = memberStatus.trim().toLowerCase();

            // Set radio button based on status
            const radioButtons = document.getElementsByName('status');
            for (const radioButton of radioButtons) {
                if (radioButton.value.toLowerCase() === formattedStatus) {
                    radioButton.checked = true;
                    break;
                }
            }

            // Show modal with animation
            document.getElementById("renewMemberModal").classList.remove("hidden");
            setTimeout(() => {
                document.getElementById('editModalContent').classList.remove('scale-95', 'opacity-0');
                document.getElementById('editModalContent').classList.add('scale-100', 'opacity-100');
            }, 10);
        }

        // Close Renew Modal
        function closeRenewModal() {
            const modal = document.getElementById('editModalContent');
            modal.classList.remove('scale-100', 'opacity-100');
            modal.classList.add('scale-95', 'opacity-0');
            
            setTimeout(() => {
                document.getElementById("renewMemberModal").classList.add("hidden");

            }, 300);
        }

       

</script>


@endsection