@extends('layouts.app') <!-- Assuming you have a main layout file -->

@section('content')
<style>
    /* Improved responsive styles */
    .table-responsive {
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
        width: 100%;
    }
    
    /* Better table styling for mobile */
    @media (max-width: 640px) {
        table {
            min-width: 600px;
        }
        
        .action-buttons {
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
        }
        
        .action-buttons button {
            width: 100%;
            margin-left: 0 !important;
        }
        
        /* Modal adjustments */
        .modal-content {
            width: 95% !important;
            margin: 0.5rem !important;
        }
        
        /* Filter and search layout */
        .filter-container {
            flex-direction: column;
            gap: 1rem;
        }
        
        .search-form, .status-filter {
            width: 100% !important;
        }
    }
    
    /* Custom scrollbar */
    .table-responsive::-webkit-scrollbar {
        height: 6px;
    }
    .table-responsive::-webkit-scrollbar-track {
        background: #2d2d2d;
    }
    .table-responsive::-webkit-scrollbar-thumb {
        background-color: #ff5722;
        border-radius: 20px;
    }
    
    /* Better modal transitions */
    .modal-transition {
        transition: all 0.3s ease;
    }
    
    /* Improved button spacing */
    .button-group {
        display: flex;
        flex-wrap: wrap;
        gap: 0.5rem;
    }
    
    /* Responsive table cell padding */
    @media (max-width: 768px) {
        td, th {
            padding: 0.5rem !important;
        }
    }

    /*
    th {
        position: relative;
        user-select: none;
    }

    th:hover {
        background-color: rgba(255, 87, 34, 0.1);
    }

    [id^="sort-icon-"] {
        display: inline-block;
        transition: transform 0.2s;
    }

    th:hover [id^="sort-icon-"] {
        opacity: 1;
    } */

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

</style>

<div class="py-8 sm:px-6 lg:px-4 h-screen">
    <div class="mb-6">
    <h1 class="text-3xl pb-1 md:text-4xl font-bold bg-clip-text text-transparent bg-gradient-to-r from-red-600 to-orange-600">
                    Gym Member 
                </h1>
        <p class="mt-1 ml-2 text-sm text-gray-300">Track members </p>
    </div>

<section class="mt-6 rounded-lg p-4 bg-transparent text-gray-200">
        <div class="flex flex-col sm:flex-row justify-between items-center gap-4 mb-6">
            

            <!-- Search Form -->
            <form method="GET" action="{{ route('staff.viewmembers') }}" class="w-full sm:w-64 md:w-80">
                <input type="hidden" name="page" value="1">
                <div class="relative flex items-center">
                    <!-- Search Input -->
                    <input 
                        type="text" 
                        name="search" 
                        value="{{ $query }}" 
                        placeholder="Search members" 
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
                    <a href="{{ route('staff.viewmembers', ['page' => 1]) }}" 
                    id="clearSearchBtn" class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-200 hover:text-[#ff5722] transition-colors hidden cursor-pointer"
                    aria-label="Clear search">
                        <svg class="h-4 w-4 text-[#ff5722]" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </a>
                </div>
            </form>

            <!-- Filter Dropdown -->
            <div class="w-full sm:w-auto">
                <form action="{{ route('staff.viewmembers') }}" method="GET" class="inline-block w-full sm:w-auto">
                    <input type="hidden" name="page" value="1"> <!-- Add this line -->
                    <select 
                        name="status" 
                        onchange="this.form.submit()" 
                        class="w-full sm:w-auto appearance-none bg-[#212121] border border-[#666666] hover:border-[#ff5722] px-4 py-2 pr-8 rounded-md text-sm text-gray-200 focus:outline-none focus:ring-1 focus:ring-[#ff5722] focus:border-[#ff5722]"
                        aria-label="Filter members by status"
                    >
                        <option value="all" {{ request('status') == 'all' ? 'selected' : '' }}>All Members</option>
                        <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active Members</option>
                        <option value="expired" {{ request('status') == 'expired' ? 'selected' : '' }}>Expired Members</option>
                        <option value="revoked" {{ request('status') == 'revoked' ? 'selected' : '' }}>Revoked Members</option>
                    </select>
                </form>
            </div>

        </div>

        <div class="glass-card mt-5 ">
            <div class="overflow-x-auto table-responsive">
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
                <table class="min-w-full divide-y divide-black">
                    <thead>
                        <tr class="bg-gradient-to-br from-[#2c2c2c] to-[#1e1e1e] rounded-lg">
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-200 uppercase tracking-wider">
                                # 
                            </th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-200 uppercase tracking-wider">
                                Name 
                                <!-- <span id="sort-icon-1" class="ml-1">↕</span> -->
                            </th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-200 uppercase tracking-wider">
                                Member ID 
                                <!-- <span id="sort-icon-2" class="ml-1">↕</span> -->
                            </th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-200 uppercase tracking-wider">
                                Membership Type 
                                <!-- <span id="sort-icon-3" class="ml-1">↕</span> -->
                            </th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-200 uppercase tracking-wider">
                                Registration Date 
                                <!-- <span id="sort-icon-4" class="ml-1">↑</span> -->
                            </th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-200 uppercase tracking-wider">
                                Status 
                                <!-- <span id="sort-icon-5" class="ml-1">↕</span> -->
                            </th>
                            <th class="px-4 py-3 text-center text-xs font-medium text-gray-200 uppercase">
                                Actions
                            </th>
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
                                    {{ $member->member_status == 'active' ? ' text-green-400' : 
                                    ($member->member_status == 'expired' ? ' text-red-400' : 
                                    ($member->member_status == 'revoked' ? ' text-gray-400' : '')) }}">
                                    {{ $member->member_status }}
                                </span>
                            </td>
                        
                            
                            <td class="px-4 py-4 text-center text-sm">
                                @if($member->member_status == 'active')
                                    <div class="flex flex-wrap gap-2 justify-center">
                                        <button 
                                            onclick="openViewModal('{{ $member->rfid_uid }}', '{{ $member->first_name }} {{ $member->last_name }}', '{{ $member->getMembershipType() }}', '{{ \Carbon\Carbon::parse($member->start_date)->format('M d, Y') }}', '{{ $member->member_status }}')"
                                            class="inline-flex items-center px-3 py-1.5 bg-transparent hover:bg-[#ff5722] hover:translate-y-[-2px] text-gray-200 rounded-lg transition-all duration-200 font-medium text-sm border border-[#ff5722] shadow-sm"
                                            aria-label="View details for {{ $member->first_name }} {{ $member->last_name }}"
                                            title="View member details"
                                        >
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                            </svg>
                                            View
                                        </button>
                                        <button 
                                            onclick="openRevokeModal('{{ $member->rfid_uid }}', '{{ $member->first_name }} {{ $member->last_name }}')"
                                            class="inline-flex items-center px-3 py-1.5 bg-transparent hover:bg-red-600 hover:translate-y-[-2px] text-gray-200 rounded-lg transition-all duration-200 font-medium text-sm border border-red-600 shadow-sm"
                                            aria-label="Revoke membership for {{ $member->first_name }} {{ $member->last_name }}"
                                            title="Revoke membership"
                                        >
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" />
                                            </svg>
                                            Revoke
                                        </button>
                                    </div>
                                @elseif($member->member_status == 'expired' && $member->getMembershipType() != 'Session')
                                    <div class="flex flex-wrap gap-2 justify-center">
                                        <button 
                                            onclick="openRenewModal('{{ $member->rfid_uid }}', '{{ $member->first_name }} {{ $member->last_name }}', '{{ $member->email }}', '{{ $member->phone_number }}', '{{ $member->end_date }}')" 
                                            class="inline-flex items-center px-3 py-1.5 bg-transparent hover:bg-[#ff5722] hover:translate-y-[-2px] text-gray-200 rounded-lg transition-all duration-200 font-medium text-sm border border-[#ff5722] shadow-sm"
                                            aria-label="Renew membership for {{ $member->first_name }} {{ $member->last_name }}"
                                            title="Renew expired membership"
                                        >
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                            </svg>
                                            Renew
                                        </button>
                                        <button 
                                            onclick="openRevokeModal('{{ $member->rfid_uid }}', '{{ $member->first_name }} {{ $member->last_name }}')"
                                            class="inline-flex items-center px-3 py-1.5 bg-transparent hover:bg-red-600 hover:translate-y-[-2px] text-gray-200 rounded-lg transition-all duration-200 font-medium text-sm border border-red-600 shadow-sm"
                                            aria-label="Revoke membership for {{ $member->first_name }} {{ $member->last_name }}"
                                            title="Revoke membership"
                                        >
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" />
                                            </svg>
                                            Revoke
                                        </button>
                                    </div>
                                @elseif($member->member_status == 'expired' && $member->getMembershipType() == 'Session')
                                    <div class="flex flex-wrap gap-2 justify-center">
                                        <button 
                                            onclick="openViewModal('{{ $member->rfid_uid }}', '{{ $member->first_name }} {{ $member->last_name }}', '{{ $member->getMembershipType() }}', '{{ \Carbon\Carbon::parse($member->start_date)->format('M d, Y') }}', '{{ $member->member_status }}')"
                                            class="inline-flex items-center px-3 py-1.5 bg-transparent hover:bg-[#ff5722] hover:translate-y-[-2px] text-gray-200 rounded-lg transition-all duration-200 font-medium text-sm border border-[#ff5722] shadow-sm"
                                            aria-label="View details for {{ $member->first_name }} {{ $member->last_name }}"
                                            title="View member details"
                                        >
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                            </svg>
                                            View
                                        </button>
                                        <button 
                                            onclick="openRevokeModal('{{ $member->rfid_uid }}', '{{ $member->first_name }} {{ $member->last_name }}')"
                                            class="inline-flex items-center px-3 py-1.5 bg-transparent hover:bg-red-600 hover:translate-y-[-2px] text-gray-200 rounded-lg transition-all duration-200 font-medium text-sm border border-red-600 shadow-sm"
                                            aria-label="Revoke membership for {{ $member->first_name }} {{ $member->last_name }}"
                                            title="Revoke membership"
                                        >
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" />
                                            </svg>
                                            Revoke
                                        </button>
                                    </div>
                                @elseif($member->member_status == 'revoked')
                                    <div class="flex flex-wrap gap-2 justify-center">
                                        <button 
                                            onclick="openRevokedReasonModal('{{ $member->id }}', '{{ $member->revoke_reason }}')"
                                            class="inline-flex items-center px-3 py-1.5 bg-transparent] hover:bg-[#ff5722] hover:translate-y-[-2px] text-gray-200 rounded-lg transition-all duration-200 font-medium text-sm border border-[#ff5722] shadow-sm"
                                            aria-label="View reason for revocation"
                                            title="View reason for revocation"
                                        >
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                            </svg>
                                            Reason
                                        </button>
                                        <button 
                                            onclick="openRestoreModal('{{ $member->rfid_uid }}', '{{ $member->first_name }} {{ $member->last_name }}')" 
                                            class="inline-flex items-center px-3 py-1.5 bg-transparent hover:bg-green-600 hover:translate-y-[-2px] text-gray-200 rounded-lg transition-all duration-200 font-medium text-sm border border-green-600 shadow-sm"
                                            aria-label="Restore membership for {{ $member->first_name }} {{ $member->last_name }}"
                                            title="Restore revoked membership"
                                        >
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                            </svg>
                                            Restore
                                        </button>
                                    </div>
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
            {{ $members->appends([
                'search' => request('search'),
                'status' => request('status'),
            ])->links('vendor.pagination.default') }}
            </div>

</section>


<!-- Renew Member Modal -->
<div id="renewMemberModal" class="fixed inset-0 bg-[#1e1e1e] bg-opacity-70 flex justify-center items-center hidden z-50 transition-opacity duration-300 p-4">
    <div class="bg-[#1e1e1e] rounded-xl shadow-2xl w-full max-w-2xl max-h-[90vh] overflow-y-auto transform transition-all duration-300 scale-95 opacity-0" id="editModalContent">
        <!-- Modal Header -->
        <div class="flex justify-between items-center p-3 sm:p-4 border-b border-gray-700 sticky top-0 bg-gradient-to-br from-[#2c2c2c] to-[#1e1e1e] z-10">
            <h2 class="text-base sm:text-lg font-bold text-gray-200 flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 sm:h-6 sm:w-6 mr-2 text-[#ff5722]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                </svg>
                <span class="truncate">Renew Membership</span>
            </h2>
            <button onclick="closeRenewModal()" class="text-gray-300 hover:text-gray-200 hover:bg-[#ff5722] rounded-full p-1 transition-colors duration-200 flex-shrink-0" aria-label="Close modal">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 sm:h-6 sm:w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        <!-- Renew Form -->
        <form id="renewalForm" action="{{ route('renew.membership') }}" method="POST" class="p-4 sm:p-6">
            @csrf
            <input type="hidden" name="user_id" id="editUserId">

            <div class="grid grid-cols-1 md:grid-cols-2 gap-3 sm:gap-4">
                <!-- Member ID -->
                <div class="w-full">
                    <label class="block text-xs sm:text-sm font-medium text-gray-300 mb-1" for="editMemberID">Member ID</label>
                    <input type="text" name="rfid_uid" id="editMemberID" class="w-full px-2 sm:px-3 py-1.5 sm:py-2 border border-gray-600 rounded-lg bg-[#2c2c2c] text-gray-200 text-xs sm:text-sm pointer-events-none" readonly>
                </div>
                
                <!-- Member Name -->
                <div class="w-full">
                    <label class="block text-xs sm:text-sm font-medium text-gray-300 mb-1" for="editMemberName">Name</label>
                    <input type="text" id="editMemberName" class="w-full px-2 sm:px-3 py-1.5 sm:py-2 border border-gray-600 rounded-lg bg-[#2c2c2c] text-gray-200 text-xs sm:text-sm pointer-events-none" readonly>
                </div>
                
                <!-- Membership Type -->
                <div class="w-full">
                    <label class="block text-xs sm:text-sm font-medium text-gray-300 mb-1" for="membershipType">Membership Type</label>
                    <select id="membershipType" name="membership_type" required class="w-full px-2 sm:px-3 py-1.5 sm:py-2 border border-gray-600 rounded-lg focus:ring-2 focus:ring-[#ff5722] focus:border-[#ff5722] transition-colors appearance-none bg-[#2c2c2c] text-gray-200 text-xs sm:text-sm">
                        <option value="" selected disabled>Select Membership Type</option>
                        <option value="1">Session (1 day)</option>
                        <option value="7">Weekly (7 days)</option>
                        <option value="30">Monthly (30 days)</option>
                        <option value="365">Annual (365 days)</option>
                    </select>
                </div>
                
                <!-- Renewal Date -->
                <div class="w-full">
                    <label class="block text-xs sm:text-sm font-medium text-gray-300 mb-1" for="startDate">Renewal Date</label>
                    <input type="date" id="startDate" name="start_date" required class="w-full px-2 sm:px-3 py-1.5 sm:py-2 border border-gray-600 rounded-lg focus:ring-2 focus:ring-[#ff5722] focus:border-[#ff5722] transition-colors bg-[#2c2c2c] text-gray-200 text-xs sm:text-sm">
                </div>
                
                <!-- Expiration Date -->
                <div class="w-full">
                    <label class="block text-xs sm:text-sm font-medium text-gray-300 mb-1" for="endDate">Expiration Date</label>
                    <input type="text" id="endDate" name="end_date" class="w-full px-2 sm:px-3 py-1.5 sm:py-2 border border-gray-600 rounded-lg bg-[#2c2c2c] text-gray-200 text-xs sm:text-sm pointer-events-none" readonly>
                </div>

                <!-- Membership Fee -->
                <div class="w-full">
                    <label class="block text-xs sm:text-sm font-medium text-gray-300 mb-1" for="membershipFee">Base Fee</label>
                    <input type="text" id="membershipFee" class="w-full px-2 sm:px-3 py-1.5 sm:py-2 border border-gray-600 rounded-lg bg-[#2c2c2c] text-gray-200 text-xs sm:text-sm pointer-events-none" readonly>
                </div>
            </div>
            
            <!-- Summary Box -->
            <div class="mt-4 bg-[#ff5722] bg-opacity-10 p-3 sm:p-4 rounded-lg flex items-start border border-[#ff5722] border-opacity-30">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 sm:h-5 sm:w-5 text-[#ff5722] mt-0.5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <div class="ml-2 sm:ml-3 text-xs sm:text-sm text-gray-300">
                    <span class="font-medium">Membership Summary:</span> <span id="membershipSummaryText">Select membership type to see details.</span>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex flex-col sm:flex-row justify-end sm:space-x-3 space-y-2 sm:space-y-0 mt-5 pt-4 border-t border-gray-700">
                <button type="button" onclick="closeRenewModal()" class="w-full sm:w-auto px-4 py-2 bg-[#444444] hover:bg-opacity-80 hover:translate-y-[-2px] text-gray-200 rounded-lg transition-colors duration-200 text-xs sm:text-sm">
                    Cancel
                </button>
                <button type="submit" class="w-full sm:w-auto px-4 py-2 bg-[#ff5722] hover:bg-opacity-80 hover:translate-y-[-2px] text-white rounded-lg transition-colors duration-200 font-medium flex items-center justify-center text-xs sm:text-sm">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 sm:h-5 sm:w-5 mr-1 sm:mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
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
<div id="viewMemberModal" class="fixed inset-0 bg-black bg-opacity-80 flex justify-center items-center hidden z-50 transition-opacity duration-300">
    <div class="bg-[#1e1e1e] rounded-2xl shadow-2xl w-full max-w-3xl p-4 sm:p-6 md:p-8 m-3 transform transition-all duration-300 scale-95 opacity-0 overflow-y-auto max-h-[90vh]" id="viewModalContent">
        <!-- Modal Header -->
        <div class="flex justify-between items-center mb-4 md:mb-6">
            <h2 class="text-xl sm:text-2xl font-bold text-white flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 sm:h-7 sm:w-7 mr-2 sm:mr-3 text-orange-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                </svg>
                <span class="truncate">Member Profile</span>
            </h2>
            <button onclick="closeViewModal()" class="text-gray-300 hover:text-gray-200 hover:bg-[#ff5722] hover:scale-95 rounded-full p-2 transition-colors duration-200">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        <!-- Modern Card Design -->
        <div class="bg-[#2c2c2c] rounded-xl overflow-hidden">
            <!-- Card Header -->
            <div class="bg-gradient-to-r from-[#2c2c2c] to-[#1e1e1e] py-3 px-4 sm:py-4 sm:px-6 rounded-t-xl shadow-lg">
                <div class="flex justify-between items-center">
                    <h3 class="font-bold text-white text-base sm:text-lg tracking-wider truncate">MEMBER IDENTIFICATION</h3>
                    <div class="px-2 sm:px-3 py-1 rounded-full">
                        <span id="viewStatus" class="text-xs sm:text-sm font-semibold text-gray-200">Active</span>
                    </div>
                </div>
            </div>
            
            <!-- Responsive Layout -->
            <div class="flex flex-col lg:flex-row">
                
                <!-- Avatar Section -->
                <div class="w-full lg:w-1/4 p-4 sm:p-6 flex flex-col items-center justify-center bg-[#2c2c2c] mx-auto lg:mx-4 border-transparent">
                    <div class="w-24 h-24 sm:w-28 sm:h-28 md:w-32 md:h-32 bg-[#444444] rounded-full flex items-center justify-center border-2 border-orange-500 shadow-lg">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 sm:h-14 sm:w-14 md:h-16 md:w-16 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                    </div>
                    <div class="w-full text-center mt-3 sm:mt-4">
                        <p class="text-xs text-gray-400">Profile Image</p>
                    </div>
                </div>
                
                <!-- Primary Info Section -->
                <div class="w-full lg:w-2/5 p-4 sm:p-6 bg-[#1e1e1e] flex flex-col justify-between border-t border-[#333333] lg:border-t-0 lg:border-l">
                    <!-- Name -->
                    <div class="mb-4 sm:mb-5">
                        <p class="text-xs text-gray-400 uppercase tracking-wider mb-1">Name</p>
                        <p class="font-bold text-white text-lg sm:text-xl" id="viewMemberName">John Doe</p>
                    </div>
                    
                    <!-- Membership Type -->
                    <div class="mb-4 sm:mb-5">
                        <p class="text-xs text-gray-400 uppercase tracking-wider mb-1">Membership Type</p>
                        <div class="bg-orange-600 text-gray-200 inline-block px-2 sm:px-3 py-1 rounded-lg text-xs sm:text-sm">
                            <p class="font-medium" id="viewMembershipType">Monthly</p>
                        </div>
                    </div>
                    
                    <!-- Registration Date -->
                    <div class="mb-4 sm:mb-5">
                        <p class="text-xs text-gray-400 uppercase tracking-wider">Issued Date</p>
                        <div class="flex items-center mt-2">
                            <div class="bg-orange-500 bg-opacity-20 p-1 sm:p-2 rounded-lg mr-2 sm:mr-3">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 sm:h-5 sm:w-5 text-orange-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                            </div>
                            <div>
                                <p class="font-medium text-gray-200 text-sm sm:text-base" id="viewStartDate">Jan 1, 2025</p>
                            </div>                           
                        </div>                      
                    </div>
                </div>
                
                <!-- RFID Card Section -->
                <div class="w-full lg:w-1/3 p-4 sm:p-6 bg-[#2c2c2c] flex flex-col justify-between border-t border-[#333333] lg:border-t-0 lg:border-l">
                    <!-- RFID Card Area -->
                    <div class="mb-4 sm:mb-5">
                        <p class="text-xs text-gray-400 uppercase tracking-wider mb-2">RFID Card</p>
                        <div class="bg-[#1e1e1e] rounded-lg p-2 sm:p-3 shadow-inner">
                            <div class="flex items-center mb-2">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 sm:h-6 sm:w-6 text-orange-400 mr-2" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                    <rect x="3" y="5" width="18" height="14" rx="2" ry="2" stroke-width="1.5" />
                                    <path d="M7 15a4 4 0 010-6" stroke-width="1.5" />
                                    <path d="M11 13a2 2 0 010-2" stroke-width="1.5" />
                                    <line x1="17" y1="9" x2="17" y2="9" stroke-width="2" stroke-linecap="round" />
                                    <line x1="17" y1="15" x2="17" y2="15" stroke-width="2" stroke-linecap="round" />
                                </svg>
                                <span class="text-xs sm:text-sm font-medium text-gray-300">RFID UID</span>
                            </div>
                            <div class="bg-[#121212] bg-opacity-50 p-2 rounded flex items-center justify-between">
                                <span id="viewRfid" class="text-xs sm:text-sm font-medium text-gray-300 truncate mr-1">ID: 123456789</span>
                                <div class="flex space-x-1">
                                    <div class="w-1 h-6 sm:h-8 bg-[#444444] rounded"></div>
                                    <div class="w-1 h-6 sm:h-8 bg-[#555555] rounded"></div>
                                    <div class="w-1 h-6 sm:h-8 bg-[#444444] rounded"></div>
                                    <div class="w-1 h-6 sm:h-8 bg-[#555555] rounded"></div>
                                    <div class="w-1 h-6 sm:h-8 bg-[#444444] rounded"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Expiration Date -->
                    <div class="mb-4 sm:mb-5">
                        <p class="text-xs text-gray-400 uppercase tracking-wider">Expiration Date</p>
                        <div class="flex items-center mt-2">                           
                            <div class="bg-orange-500 bg-opacity-20 p-1 sm:p-2 rounded-lg mr-2 sm:mr-3">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 sm:h-5 sm:w-5 text-orange-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                            </div>
                            <div>
                                <p class="font-medium text-white text-sm sm:text-base" id="viewEndDate">Jan 15, 2025</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Footer -->
            <div class="bg-gradient-to-r from-[#2c2c2c] to-[#1e1e1e] text-gray-400 border-t border-[#333333] py-2 sm:py-3 px-4 sm:px-6 flex justify-between items-center">
                <p class="text-xs text-gray-300 mx-auto">Valid only upon registration</p>
            </div>
        </div>
    </div>
</div>


<!-- Revoke Member Modal -->
<div id="revokeMemberModal" class="fixed inset-0 bg-[#1e1e1e] bg-opacity-70 flex justify-center items-center hidden z-50 transition-opacity duration-300 p-4">
    <div class="bg-[#1e1e1e] rounded-xl shadow-2xl w-full max-w-md max-h-[90vh] overflow-y-auto transform transition-all duration-300 scale-95 opacity-0" id="revokeModalContent">
        <!-- Modal Header -->
        <div class="flex justify-between items-center p-3 sm:p-4 border-b border-gray-700 sticky top-0 bg-gradient-to-br from-[#2c2c2c] to-[#1e1e1e] z-10">
            <h2 class="text-base sm:text-lg font-bold text-gray-200 flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 sm:h-6 sm:w-6 mr-2 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" />
                </svg>
                <span class="truncate">Revoke Membership</span>
            </h2>
            <button onclick="closeRevokeModal()" class="text-gray-300 hover:text-gray-200 hover:bg-red-600 rounded-full p-1 transition-colors duration-200 flex-shrink-0" aria-label="Close modal">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 sm:h-6 sm:w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        <!-- Main Form View -->
        <div id="revokeFormView" class="block">
            <form id="revokeForm" action="{{ route('revoke.membership') }}" method="POST" class="p-4 sm:p-6">
                @csrf
                <input type="hidden" name="rfid_uid" id="revokeMemberID">

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-300 mb-2" for="revokeConfirmName">Member Name</label>
                    <input type="text" id="revokeConfirmName" class="w-full px-3 py-2 border border-gray-600 rounded-lg bg-[#2c2c2c] text-gray-200 text-sm pointer-events-none" readonly>
                </div>
                
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-300 mb-2" for="revokeReason">Reason for Revocation (Optional)</label>
                    <textarea id="revokeReason" name="reason" rows="3" class="w-full px-3 py-2 border border-gray-600 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-colors bg-[#2c2c2c] text-gray-200 text-sm" placeholder="Enter reason for revoking membership..."></textarea>
                </div>

                <!-- Warning Box -->
                <div class="mb-4 bg-red-900 bg-opacity-20 p-4 rounded-lg flex items-start border border-red-600 border-opacity-30">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-red-500 mt-0.5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                    <div class="ml-3 text-sm text-gray-300">
                        <span class="font-medium text-red-400">Warning:</span> Revoking a membership will prevent the member from accessing the gym. This action can be reversed later if needed.
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex flex-col sm:flex-row justify-end sm:space-x-3 space-y-2 sm:space-y-0 mt-5 pt-4 border-t border-gray-700">
                    <button type="button" onclick="closeRevokeModal()" class="w-full sm:w-auto px-4 py-2 bg-[#444444] hover:bg-opacity-80 hover:translate-y-[-2px] text-gray-200 rounded-lg transition-colors duration-200 text-sm">
                        Cancel
                    </button>
                    <button type="button" onclick="showConfirmation()" class="w-full sm:w-auto px-4 py-2 bg-red-600 hover:bg-opacity-80 hover:translate-y-[-2px] text-white rounded-lg transition-colors duration-200 font-medium flex items-center justify-center text-sm">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" />
                        </svg>
                        Revoke Member
                    </button>
                </div>
            </form>
        </div>

        <!-- Confirmation View -->
        <div id="confirmationView" class="hidden p-4 sm:p-6">
            <!-- Warning Icon -->
            <div class="flex justify-center mb-4">
                <div class="bg-red-600 bg-opacity-20 p-3 rounded-full">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                </div>
            </div>
            
            <!-- Confirmation Message -->
            <div class="text-center mb-6">
                <h3 class="text-lg font-bold text-red-400 mb-2">Confirm Membership Revocation</h3>
                <p class="text-gray-300 mb-2">You are about to revoke membership for:</p>
                <p class="text-lg font-medium text-gray-200 mb-4" id="confirmMemberName">Member Name</p>
                <p class="text-sm text-gray-400">This action will immediately block gym access for this member.</p>
            </div>
            
            <!-- Confirmation Buttons -->
            <div class="flex flex-col sm:flex-row justify-center sm:space-x-4 space-y-3 sm:space-y-0">
                <button type="button" onclick="backToForm()" class="w-full sm:w-auto px-6 py-2 bg-[#444444] hover:bg-opacity-80 hover:translate-y-[-2px] text-gray-200 rounded-lg transition-colors duration-200 flex items-center justify-center text-sm">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    Go Back
                </button>
                <button type="button" onclick="confirmRevoke()" class="w-full sm:w-auto px-6 py-2 bg-red-600 hover:bg-opacity-80 hover:translate-y-[-2px] text-white rounded-lg transition-colors duration-200 font-medium flex items-center justify-center text-sm">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    Confirm Revocation
                </button>
            </div>
        </div>
    </div>
</div>
<!-- End Revoke Member Modal -->

<!-- View Reason Modal -->
<div id="viewReasonModal" class="fixed inset-0 bg-[#1e1e1e] bg-opacity-70 flex justify-center items-center hidden z-50 transition-opacity duration-300 p-4">
    <div class="bg-[#1e1e1e] rounded-xl shadow-2xl w-full max-w-md max-h-[90vh] overflow-y-auto transform transition-all duration-300 scale-95 opacity-0" id="viewReasonModalContent">
        <!-- Modal Header -->
        <div class="flex justify-between items-center p-3 sm:p-4 border-b border-gray-700 sticky top-0 bg-gradient-to-br from-[#2c2c2c] to-[#1e1e1e] z-10">
            <h2 class="text-base sm:text-lg font-bold text-gray-200 flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 sm:h-6 sm:w-6 mr-2 text-[#ff5722]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                </svg>
                <span class="truncate">Revocation Reason</span>
            </h2>
            <button onclick="closeReasonModal()" class="text-gray-300 hover:text-gray-200 hover:bg-[#ff5722] rounded-full p-1 transition-colors duration-200 flex-shrink-0" aria-label="Close modal">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 sm:h-6 sm:w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        <!-- Modal Content -->
        <div class="p-4 sm:p-6">
            <p class="text-sm text-gray-400">Reason for revocation:</p>
            <p id="revocationReason" class="text-lg text-white font-medium mt-2">This member has been revoked for violating gym rules.</p>
        </div>

        <!-- Action Buttons -->
        <div class="flex justify-end sm:space-x-3 space-y-2 sm:space-y-0 mt-5 pt-4 border-t border-gray-700">
            <button type="button" onclick="closeReasonModal()" class="w-full sm:w-auto px-4 py-2 bg-[#444444] hover:bg-opacity-80 hover:translate-y-[-2px] text-gray-200 rounded-lg transition-colors duration-200 text-xs sm:text-sm">
                Close
            </button>
        </div>
    </div>
</div>
<!-- End View Reason Modal -->

<!-- Restore Member Modal -->
<div id="restoreMemberModal" class="fixed inset-0 bg-[#1e1e1e] bg-opacity-70 flex justify-center items-center hidden z-50 transition-opacity duration-300 p-4">
    <div class="bg-[#1e1e1e] rounded-xl shadow-2xl w-full max-w-md max-h-[90vh] overflow-y-auto transform transition-all duration-300 scale-95 opacity-0" id="restoreModalContent">
        <!-- Modal Header -->
        <div class="flex justify-between items-center p-3 sm:p-4 border-b border-gray-700 sticky top-0 bg-gradient-to-br from-[#2c2c2c] to-[#1e1e1e] z-10">
            <h2 class="text-base sm:text-lg font-bold text-gray-200 flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 sm:h-6 sm:w-6 mr-2 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                </svg>
                <span class="truncate">Restore Membership</span>
            </h2>
            <button onclick="closeRestoreModal()" class="text-gray-300 hover:text-gray-200 hover:bg-green-600 rounded-full p-1 transition-colors duration-200 flex-shrink-0" aria-label="Close modal">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 sm:h-6 sm:w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        <!-- Restore Form -->
        <form id="restoreForm" action="{{ route('restore.membership') }}" method="POST" class="p-4 sm:p-6">
            @csrf
            <input type="hidden" name="rfid_uid" id="restoreMemberID">

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-300 mb-2" for="restoreConfirmName">Member Name</label>
                <input type="text" id="restoreConfirmName" class="w-full px-3 py-2 border border-gray-600 rounded-lg bg-[#2c2c2c] text-gray-200 text-sm pointer-events-none" readonly>
            </div>

            <!-- Info Box -->
            <div class="mb-4 bg-green-900 bg-opacity-20 p-4 rounded-lg flex items-start border border-green-600 border-opacity-30">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-green-500 mt-0.5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <div class="ml-3 text-sm text-gray-300">
                    <span class="font-medium text-green-400">Info:</span> Restoring this member will allow them to access the gym again. The member's status will be set to either 'active' or 'expired' based on their membership end date.
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex flex-col sm:flex-row justify-end sm:space-x-3 space-y-2 sm:space-y-0 mt-5 pt-4 border-t border-gray-700">
                <button type="button" onclick="closeRestoreModal()" class="w-full sm:w-auto px-4 py-2 bg-[#444444] hover:bg-opacity-80 hover:translate-y-[-2px] text-gray-200 rounded-lg transition-colors duration-200 text-sm">
                    Cancel
                </button>
                <button type="submit" class="w-full sm:w-auto px-4 py-2 bg-green-600 hover:bg-opacity-80 hover:translate-y-[-2px] text-white rounded-lg transition-colors duration-200 font-medium flex items-center justify-center text-sm">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                    </svg>
                    Restore Member
                </button>
            </div>
        </form>
    </div>
</div>
<!-- End Restore Member Modal -->

<script>
    document.addEventListener('DOMContentLoaded', function() {
    // ======== CONSTANTS & DOM ELEMENTS ========
    const ELEMENTS = {
        searchInput: document.querySelector('input[name="search"]'),
        statusSelect: document.querySelector('select[name="status"]'),
        tableContainer: document.querySelector('.glass-card .overflow-x-auto'),
        paginationContainer: document.querySelector('.pagination'),
        clearSearchButton: document.querySelector('[aria-label="Clear search"]'),
        membershipTypeSelect: document.getElementById('membershipType'),
        startDateInput: document.getElementById('startDate'),
        endDateInput: document.getElementById('endDate'),
        membershipFeeInput: document.getElementById('membershipFee'),
        summaryText: document.getElementById('membershipSummaryText')
    };

    // Get URL parameters
    const urlParams = new URLSearchParams(window.location.search);
    
    const today = new Date();
    const todayFormatted = formatDate(today);
    let searchTimeout;

    // Membership type data configuration
    const MEMBERSHIP_DATA = {
        '1': { fee: 60, name: 'Session (1 day)' },
        '7': { fee: 300, name: 'Weekly (7 days)' },
        '30': { fee: 850, name: 'Monthly (30 days)' },
        '365': { fee: 10000, name: 'Annual (365 days)' }
    };

    // Status badge style mapping
    const STATUS_STYLES = {
        active: "inline-block px-3 py-1 text-sm font-semibold rounded-full bg-green-900 text-green-200",
        revoked: "inline-block px-3 py-1 text-sm font-semibold rounded-full bg-red-900 text-red-200"
    }

    // ======== INITIALIZATION ========
    function initialize() {
        initializeEventListeners();
        initializeFormDefaults();
        toggleClearButtonVisibility();
        ensureRevokedStatusOption();
        
        // Make sure to re-attach pagination listeners when page loads
        attachPaginationListeners();
    }

    // Helper function to parse dates in various formats
    function parseDate(dateStr) {
        if (!dateStr) return null;
        
        // Try standard date parsing first
        let date = new Date(dateStr);
        if (!isNaN(date.getTime())) return date;
        
        // Try to handle common date formats
        const formats = [
            // MM/DD/YYYY
            /(\d{1,2})\/(\d{1,2})\/(\d{4})/,
            // DD/MM/YYYY
            /(\d{1,2})\/(\d{1,2})\/(\d{4})/,
            // YYYY-MM-DD
            /(\d{4})-(\d{1,2})-(\d{1,2})/,
            // Month DD, YYYY (e.g., January 1, 2023)
            /([A-Za-z]+)\s+(\d{1,2}),\s+(\d{4})/
        ];
        
        for (const format of formats) {
            const match = dateStr.match(format);
            if (match) {
                // Different handling based on format
                if (format === formats[0]) return new Date(match[3], match[1]-1, match[2]);
                if (format === formats[1]) return new Date(match[3], match[2]-1, match[1]);
                if (format === formats[2]) return new Date(match[1], match[2]-1, match[3]);
                if (format === formats[3]) {
                    const months = ["january","february","march","april","may","june","july",
                                   "august","september","october","november","december"];
                    const monthIndex = months.indexOf(match[1].toLowerCase());
                    if (monthIndex !== -1) return new Date(match[3], monthIndex, match[2]);
                }
            }
        }
        
        // Return null if no formats matched
        console.log(`Failed to parse date: ${dateStr}`); // Debug logging
        return null;
    }

    function initializeFormDefaults() {
        // Set today's date as default for start date
        if (ELEMENTS.startDateInput) {
            ELEMENTS.startDateInput.value = todayFormatted;
            ELEMENTS.startDateInput.min = todayFormatted; // Prevent past dates
        }
    }

    function ensureRevokedStatusOption() {
        if (!ELEMENTS.statusSelect) return;
        
        const currentOptions = Array.from(ELEMENTS.statusSelect.options).map(opt => opt.value);
        if (!currentOptions.includes('revoked')) {
            const option = document.createElement('option');
            option.value = 'revoked';
            option.textContent = 'Revoked Members';
            
            // Check URL parameters for preselection
            const urlParams = new URLSearchParams(window.location.search);
            option.selected = urlParams.get('status') === 'revoked';
            
            ELEMENTS.statusSelect.appendChild(option);
        }
    }

    function initializeEventListeners() {
        // Search input debounced event
        if (ELEMENTS.searchInput) {
            ELEMENTS.searchInput.addEventListener('input', () => {
                toggleClearButtonVisibility();
                debounce(fetchMembers, 500);
            });
        }

        // Clear search button
        if (ELEMENTS.clearSearchButton) {
            ELEMENTS.clearSearchButton.addEventListener('click', () => {
                ELEMENTS.searchInput.value = '';
                fetchMembers();
                toggleClearButtonVisibility();
            });
        }
        
        // Status filter change - FIX: Use event handler instead of direct onchange attribute
        if (ELEMENTS.statusSelect) {
            ELEMENTS.statusSelect.addEventListener('change', function(e) {
                e.preventDefault(); // Prevent default form submission
                
                // Create new URL with status parameter
                const currentUrl = new URL(window.location.href);
                currentUrl.searchParams.set('status', this.value);
                currentUrl.searchParams.set('page', '1'); // Reset to page 1 when filter changes
                
                // Update URL without reloading page
                window.history.pushState({}, '', currentUrl.toString());
                
                // Fetch members with new status filter
                fetchMembers();
            });
        }
        
        // Membership renewal events
        if (ELEMENTS.membershipTypeSelect) {
            ELEMENTS.membershipTypeSelect.addEventListener('change', updateAllDetails);
        }
        
        if (ELEMENTS.startDateInput) {
            ELEMENTS.startDateInput.addEventListener('change', function() {
                const selectedDate = new Date(this.value);
                const today = new Date();
                today.setHours(0, 0, 0, 0);
        
                if (selectedDate < today) {
                    this.value = todayFormatted;
                }
                updateAllDetails();
            });
        }
        
        // Then, attach pagination links handlers
        attachPaginationListeners();
    }

    // Separate function for pagination listeners
    function attachPaginationListeners() {
        // Use setTimeout to ensure the DOM is fully updated
        setTimeout(() => {
            const paginationLinks = document.querySelectorAll('.pagination a');
            
            paginationLinks.forEach(link => {
                // Remove old event listeners if they exist
                const oldLink = link.cloneNode(true);
                link.parentNode.replaceChild(oldLink, link);
                
                oldLink.addEventListener('click', function(e) {
                    e.preventDefault();
                    
                    // Get the base URL from the link
                    const url = new URL(this.href, window.location.origin);
                    
                    // Preserve search query if exists
                    if (ELEMENTS.searchInput && ELEMENTS.searchInput.value.trim()) {
                        url.searchParams.set('search', ELEMENTS.searchInput.value.trim());
                    }
                    
                    // Preserve status filter if not 'all'
                    if (ELEMENTS.statusSelect && ELEMENTS.statusSelect.value !== 'all') {
                        url.searchParams.set('status', ELEMENTS.statusSelect.value);
                    }
                    
                    // Update browser URL
                    window.history.pushState({}, '', url.toString());
                    
                    // Fetch members with the complete URL
                    fetchMembers(url.toString());
                });
            });
        }, 100);
    }

    // ======== HELPER FUNCTIONS ========
    function debounce(func, delay) {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(func, delay);
    }

    function formatDate(date) {
        return date.toISOString().split('T')[0];
    }
    
    function formatDisplayDate(date) {
        return date.toLocaleDateString('en-US', { 
            year: 'numeric', 
            month: 'short', 
            day: 'numeric' 
        });
    }

    function toggleClearButtonVisibility() {
        if (ELEMENTS.clearSearchButton) {
            ELEMENTS.clearSearchButton.style.display = 
                ELEMENTS.searchInput && ELEMENTS.searchInput.value.trim() !== '' ? 'flex' : 'none';
        }
    }

    // ======== DATA OPERATIONS ========
    function fetchMembers(url = null) {
        // Show loading indicator
        if (ELEMENTS.tableContainer) {
            ELEMENTS.tableContainer.innerHTML = '<div class="flex justify-center items-center h-32"><div class="animate-spin rounded-full h-12 w-12 border-t-2 border-b-2 border-orange-500"></div></div>';
        }
        
        // Construct URL with parameters if none provided
        if (!url) {
            const params = new URLSearchParams();
            
            // Add search filter if exists
            if (ELEMENTS.searchInput && ELEMENTS.searchInput.value.trim()) {
                params.append('search', ELEMENTS.searchInput.value.trim());
            }
            
            // Add status filter if not 'all'
            if (ELEMENTS.statusSelect && ELEMENTS.statusSelect.value !== 'all') {
                params.append('status', ELEMENTS.statusSelect.value);
            }
            
            // Get current page from URL or default to 1
            const urlObj = new URL(window.location.href);
            const currentPage = urlObj.searchParams.get('page') || 1;
            params.append('page', currentPage);
            
            url = `${window.location.pathname}?${params.toString()}`;
        }
        
        // Fetch data with AJAX
        fetch(url, {
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
            // Update table content
            if (ELEMENTS.tableContainer) {
                ELEMENTS.tableContainer.innerHTML = data.table;
            }
            
            // Update pagination
            if (ELEMENTS.paginationContainer && data.pagination) {
                ELEMENTS.paginationContainer.innerHTML = data.pagination;
                // Re-attach pagination listeners after content update
                attachPaginationListeners();
            } else if (ELEMENTS.paginationContainer) {
                ELEMENTS.paginationContainer.innerHTML = '';
            }
            
            // Update browser URL with all current parameters
            const newUrl = new URL(url, window.location.origin);
            window.history.replaceState({}, '', newUrl.toString());
            
            // Re-attach event listeners
            setTimeout(() => {
                attachTableEventListeners();
                if (typeof updateSortIcons === 'function') {
                    updateSortIcons();
                }
            }, 50);
        })
        .catch(error => {
            console.error('Error fetching members:', error);
            
            // Show error message in table container
            if (ELEMENTS.tableContainer) {
                ELEMENTS.tableContainer.innerHTML = `
                    <div class="flex flex-col items-center justify-center h-32 text-center">
                        <div class="text-red-500 mb-2">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <p class="text-gray-400">Failed to load members. Please try again.</p>
                    </div>
                `;
            }
        });
    }

    // Fix: Add missing attachTableEventListeners function
    function attachTableEventListeners() {
        // This function would attach any event listeners to table elements
        // For now it's empty since we don't have specific table interactions defined
        // But it's called in the code, so we need to define it
    }

    // ======== MEMBERSHIP MANAGEMENT FUNCTIONS ========
    function updateAllDetails() {
        updateMembershipFee();
        updateExpirationDate();
        updateSummaryText();
    }
    
    function updateMembershipFee() {
        if (!ELEMENTS.membershipTypeSelect || !ELEMENTS.membershipFeeInput) return;
        
        const selectedType = ELEMENTS.membershipTypeSelect.value;
        ELEMENTS.membershipFeeInput.value = selectedType ? 
            MEMBERSHIP_DATA[selectedType].fee.toFixed(2) : '0.00';
    }
    
    function updateExpirationDate() {
        if (!ELEMENTS.membershipTypeSelect || !ELEMENTS.startDateInput || !ELEMENTS.endDateInput) return;
        
        const selectedType = parseInt(ELEMENTS.membershipTypeSelect.value);
        const renewalDate = ELEMENTS.startDateInput.value;
    
        if (renewalDate && selectedType) {
            try {
                const renewal = new Date(renewalDate);
                if (isNaN(renewal.getTime())) throw new Error('Invalid date');
    
                renewal.setDate(renewal.getDate() + selectedType);
                ELEMENTS.endDateInput.value = formatDate(renewal);
            } catch (error) {
                console.error('Error calculating expiration date:', error);
                ELEMENTS.endDateInput.value = '';
            }
        } else {
            ELEMENTS.endDateInput.value = '';
        }
    }
    
    function updateSummaryText() {
        if (!ELEMENTS.membershipTypeSelect || !ELEMENTS.startDateInput || 
            !ELEMENTS.endDateInput || !ELEMENTS.summaryText) return;
        
        const selectedType = ELEMENTS.membershipTypeSelect.value;
        const renewalDate = ELEMENTS.startDateInput.value;
        const endDate = ELEMENTS.endDateInput.value;
    
        if (!selectedType) {
            ELEMENTS.summaryText.textContent = 'Select membership type to see details.';
            return;
        }
    
        const typeName = MEMBERSHIP_DATA[selectedType].name;
        const fee = MEMBERSHIP_DATA[selectedType].fee.toFixed(2);
    
        if (renewalDate && endDate) {
            const formattedStart = formatDisplayDate(new Date(renewalDate));
            const formattedEnd = formatDisplayDate(new Date(endDate));
            ELEMENTS.summaryText.textContent = 
                `${typeName} membership from ${formattedStart} to ${formattedEnd}. Total fee: ₱${fee}`;
        } else {
            ELEMENTS.summaryText.textContent = 
                `${typeName} membership. Total fee: ₱${fee}`;
        }
    }

    // ======== MODAL FUNCTIONS ========
    // View Member Modal
    function openViewModal(memberID, name, membershipType, startDate, status) {
        // Set modal data
        document.getElementById('viewMemberName').textContent = name;
        document.getElementById('viewRfid').textContent = 'ID: ' + memberID;
        document.getElementById('viewMembershipType').textContent = membershipType;
        document.getElementById('viewStartDate').textContent = formatDisplayDate(new Date(startDate));

        // Calculate and set expiration date based on membership type
        const start = new Date(startDate);
        let endDate = new Date(start);

        switch(membershipType.toLowerCase()) {
            case 'session':
                endDate.setDate(start.getDate() + 1);
                break;
            case 'week':
                endDate.setDate(start.getDate() + 7);
                break;
            case 'month':
                endDate.setMonth(start.getMonth() + 1);
                break;
            case 'annual':
                endDate.setFullYear(start.getFullYear() + 1);
                break;
            default:
                endDate = 'N/A';
        }

        document.getElementById('viewEndDate').textContent = 
            typeof endDate === 'object' ? formatDisplayDate(endDate) : endDate;

        // Change status color based on status
        const statusBadge = document.getElementById('viewStatus');
        statusBadge.textContent = status;
        statusBadge.className = STATUS_STYLES[status.toLowerCase()] || STATUS_STYLES.revoked;

        // Show modal with animation
        animateModalOpen('viewMemberModal', 'viewModalContent');
    }

    function closeViewModal() {
        animateModalClose('viewMemberModal', 'viewModalContent');
    }

    // Renew Member Modal
    function openRenewModal(memberID, name, email, phone, endDate) {
        // Set form values
        document.getElementById("editMemberID").value = memberID;
        document.getElementById("editMemberName").value = name;
        
        // If we have email and phone, set those too
        if (document.getElementById("editEmail") && email) {
            document.getElementById("editEmail").value = email;
        }
        
        if (document.getElementById("editPhone") && phone) {
            document.getElementById("editPhone").value = phone;
        }

        // Initialize date fields with current values if available
        if (document.getElementById("startDate")) {
            document.getElementById("startDate").value = todayFormatted;
        }

        // Show modal with animation
        animateModalOpen('renewMemberModal', 'editModalContent');
        
        // Update the summary and pricing
        if (ELEMENTS.membershipTypeSelect) {
            updateAllDetails();
        }
    }

    function closeRenewModal() {
        animateModalClose('renewMemberModal', 'editModalContent');
    }
    
    // Revoke Member Modal
    function openRevokeModal(memberID, memberName) {
        // Set form values
        document.getElementById("revokeMemberID").value = memberID;
        document.getElementById("revokeConfirmName").value = memberName;
        
        // Clear reason field
        document.getElementById("revokeReason").value = '';

        // Make sure we're showing the form view, not the confirmation view
        document.getElementById('revokeFormView').classList.remove('hidden');
        document.getElementById('confirmationView').classList.add('hidden');

        // Show modal with animation
        animateModalOpen('revokeMemberModal', 'revokeModalContent');
    }

    function closeRevokeModal() {
        animateModalClose('revokeMemberModal', 'revokeModalContent');
    }

    // Restore Member Modal
    function openRestoreModal(memberID, memberName) {
        // Set form values
        document.getElementById("restoreMemberID").value = memberID;
        document.getElementById("restoreConfirmName").value = memberName;

        // Show modal with animation
        animateModalOpen('restoreMemberModal', 'restoreModalContent');
    }

    function closeRestoreModal() {
        animateModalClose('restoreMemberModal', 'restoreModalContent');
    }

    // Revocation Reason Modal
    function openRevokedReasonModal(memberId, reason) {
        // Set the revocation reason inside the modal
        document.getElementById('revocationReason').textContent = reason || 'No reason provided';

        // Show modal with animation
        animateModalOpen('viewReasonModal', 'viewReasonModalContent');
    }

    function closeReasonModal() {
        animateModalClose('viewReasonModal', 'viewReasonModalContent');
    }

    // General modal animation helpers
    function animateModalOpen(modalId, contentId) {
        const modal = document.getElementById(modalId);
        const modalContent = document.getElementById(contentId);

        if (!modal || !modalContent) return;

        modal.classList.remove('hidden');
        setTimeout(() => {
            modalContent.classList.remove('scale-95', 'opacity-0');
            modalContent.classList.add('scale-100', 'opacity-100');
        }, 10);
    }

    function animateModalClose(modalId, contentId) {
        const modal = document.getElementById(modalId);
        const modalContent = document.getElementById(contentId);

        if (!modal || !modalContent) return;

        modalContent.classList.remove('scale-100', 'opacity-100');
        modalContent.classList.add('scale-95', 'opacity-0');
        
        setTimeout(() => {
            modal.classList.add('hidden');
        }, 300);
    }

    // ======== REVOCATION CONFIRMATION FUNCTIONS ========
    function showConfirmation() {
        // Get the member name and set it in the confirmation view
        const memberName = document.getElementById('revokeConfirmName').value;
        document.getElementById('confirmMemberName').textContent = memberName;
        
        // Hide the form view and show the confirmation view
        document.getElementById('revokeFormView').classList.add('hidden');
        document.getElementById('confirmationView').classList.remove('hidden');
    }
    
    function backToForm() {
        document.getElementById('confirmationView').classList.add('hidden');
        document.getElementById('revokeFormView').classList.remove('hidden');
    }

    function confirmRevoke() {
        // Submit the form
        document.getElementById('revokeForm').submit();
    }

    // Export functions to global scope for HTML onclick handlers
    window.openViewModal = openViewModal;
    window.closeViewModal = closeViewModal;
    window.openRenewModal = openRenewModal;
    window.closeRenewModal = closeRenewModal;
    window.openRevokeModal = openRevokeModal;
    window.closeRevokeModal = closeRevokeModal;
    window.openRestoreModal = openRestoreModal;
    window.closeRestoreModal = closeRestoreModal;
    window.openRevokedReasonModal = openRevokedReasonModal;
    window.closeReasonModal = closeReasonModal;
    window.showConfirmation = showConfirmation;
    window.backToForm = backToForm;
    window.confirmRevoke = confirmRevoke;

    // Handle browser back/forward navigation
    window.addEventListener('popstate', function() {
        fetchMembers();
    });

    // Initialize everything
    initialize();
});
</script>
@endsection