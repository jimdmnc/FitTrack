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
</style>
<div class="p-6">
    <div class="mb-6">
        <h2 class="text-2xl font-bold pb-1 md:text-4xl font-bold bg-clip-text text-transparent bg-gradient-to-r from-red-600 to-orange-600">Member Approval Dashboard</h2>
        <p class="text-gray-500 text-md ml-1">Review and process new membership requests</p>
    </div>
    

    @if(session('success'))
        <div class="bg-green-100 text-green-700 p-4 rounded-md mb-4 flex items-center">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
            </svg>
            {{ session('success') }}
        </div>
    @elseif(session('error'))
        <div class="bg-red-100 text-red-700 p-4 rounded-md mb-4 flex items-center">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
            </svg>
            {{ session('error') }}
        </div>
    @endif

    <div class="p-4" >
        <div class="flex justify-between items-center mb-4">
            <div class="flex gap-2">
                <select id="filterOptions" class="bg-[#212121] rounded-md px-3 py-2 text-sm text-gray-200 focus:ring-[#ff5722] focus:border-[#ff5722]">
                    <option value="all">All Pending</option>
                    <option value="today">Today's Requests</option>
                    <option value="week">This Week</option>
                </select>
                <button id="refreshBtn" class="bg-[#212121] text-gray-200 border border-[#ff5722] hover:translate-y-[-2px] hover:bg-[#ff5722] px-3 py-2 rounded-md text-sm transition-colors flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                    </svg>
                    Refresh
                </button>
            </div>
        </div>

        <div class="overflow-x-auto table-responsive">
            <table class="w-full border-collapse">
                <thead> 
                    <tr class="bg-gradient-to-br from-[#2c2c2c] to-[#1e1e1e] text-gray-200 text-sm border-b border-black">
                        <th class="border-gray-700 p-3 text-left">Full Name</th>
                        <th class="p-3 text-left">Gender</th>
                        <th class="p-3 text-left">Membeship Type</th>
                        <th class="p-3 text-left">Payment Method</th>
                        <th class="p-3 text-left">Registration Date</th>
                        <th class="p-3 text-center">Actions</th>
                    </tr>   
                </thead>

                <tbody class="divide-y divide-black">
                    @forelse($pendingUsers as $user)
                    <tr class="bg-gradient-to-br from-[#2c2c2c] to-[#1e1e1e] text-gray-200 text-sm border-b border-black">
                        <td class="p-3 font-medium text-gray-200">{{ $user->first_name }} {{ $user->last_name }}</td>
                        <td class="p-3 font-medium text-gray-200">{{ ucfirst($user->gender) }}</td>
                        <td class="p-3 font-medium text-gray-200">
                @if($user->membership_type == '7')
                    <span class="px-2 py-1 rounded-full text-xs font-semibold bg-green-900 text-green-200">
                        Weekly Membership
                    </span>
                @elseif($user->membership_type == '30')
                    <span class="px-2 py-1 rounded-full text-xs font-semibold bg-blue-900 text-blue-200">
                        Monthly Membership
                    </span>
                @elseif($user->membership_type == '365')
                    <span class="px-2 py-1 rounded-full text-xs font-semibold  bg-purple-900 text-purple-200">
                        Annual Membership
                    </span>
                @else
                    <span class="px-2 py-1 rounded-full text-xs font-semibold bg-green-900 text-green-200">
                        {{ $user->membership_type ?? 'N/A' }}
                    </span>
                @endif
                </td>
                <td class="p-3 font-medium text-gray-200">
                @if($user->payment && $user->payment->payment_method == 'gcash')
                <span class="px-2 py-1 rounded-full text-xs font-semibold bg-indigo-500 text-white flex items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 mr-1" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8zm.31-8.86c-1.77-.45-2.34-.94-2.34-1.67 0-.84.79-1.43 2.1-1.43 1.38 0 1.9.66 1.94 1.64h1.71c-.05-1.34-.87-2.57-2.49-2.97V5H10.9v1.69c-1.51.32-2.72 1.3-2.72 2.81 0 1.79 1.49 2.69 3.66 3.21 1.95.46 2.34 1.15 2.34 1.87 0 .53-.39 1.39-2.1 1.39-1.6 0-2.23-.72-2.32-1.64H8.04c.1 1.7 1.36 2.66 2.86 2.97V19h2.34v-1.67c1.52-.29 2.72-1.16 2.73-2.77-.01-2.2-1.9-2.96-3.66-3.42z"/>
                            </svg>
                            GCash
                        </span>
                        @elseif($user->payment && $user->payment->payment_method == 'cash')
                        <span class="px-2 py-1 rounded-full text-xs font-semibold bg-yellow-500 text-black flex items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 mr-1" viewBox="0 0 20 20" fill="currentColor">
                                <path d="M8.433 7.418c.155-.103.346-.196.567-.267v1.698a2.305 2.305 0 01-.567-.267C8.07 8.34 8 8.114 8 8c0-.114.07-.34.433-.582zM11 12.849v-1.698c.22.071.412.164.567.267.364.243.433.468.433.582 0 .114-.07.34-.433.582a2.305 2.305 0 01-.567.267z"/>
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-13a1 1 0 10-2 0v.092a4.535 4.535 0 00-1.676.662C6.602 6.234 6 7.009 6 8c0 .99.602 1.765 1.324 2.246.48.32 1.054.545 1.676.662v1.941c-.391-.127-.68-.317-.843-.504a1 1 0 10-1.51 1.31c.562.649 1.413 1.076 2.353 1.253V15a1 1 0 102 0v-.092a4.535 4.535 0 001.676-.662C13.398 13.766 14 12.991 14 12c0-.99-.602-1.765-1.324-2.246A4.535 4.535 0 0011 9.092V7.151c.391.127.68.317.843.504a1 1 0 101.511-1.31c-.563-.649-1.413-1.076-2.354-1.253V5z" clip-rule="evenodd"/>
                            </svg>
                            Cash
                        </span>
                    @else
                        <span class="px-2 py-1 rounded-full text-xs font-semibold bg-gray-500 text-white">
                            Unknown
                        </span>
                    @endif
                </td>
                        <td class="p-3 font-medium">
                            <span class="text-gray-200">{{ $user->updated_at->format('M d, Y') }}</span>
                            <span class="text-gray-400 text-sm">{{ $user->updated_at->format('h:i A') }}</span>
                        </td>
                        <td class="p-3 text-center">
                            <div class="flex justify-center gap-2">
                                <form action="{{route('staff.approveUser', $user->id)}}" method="POST" class="inline-block">
                                    @csrf
                                    @method('PUT')
                                    <button type="submit" class="bg-green-500 text-white px-3 py-2 rounded-md text-sm hover:translate-y-[-2px] hover:bg-green-600 transition-colors flex items-center">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                        </svg>
                                        Approve
                                    </button>
                                </form>

                                <button onclick="rejectUser({{ $user->id }})" class="bg-red-500 text-white px-3 py-2 rounded-md text-sm hover:translate-y-[-2px] hover:bg-red-600 transition-colors flex items-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                                    </svg>
                                    Reject
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="py-10 text-center">
                            <div class="flex flex-col items-center justify-center text-gray-400">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 mx-auto text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <h3 class="mt-3 text-lg font-medium text-gray-200">No Pending Approvals</h3>
                                <p class="text-gray-300 mt-1">All membership requests have been processed</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>

            </div>

        </table>
    </div>
</div>

<!-- Reject Confirmation Modal -->
<div id="rejectModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex justify-center items-center z-50">
    <div class="bg-[#121212] p-6 rounded-lg shadow-xl w-full max-w-md">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-bold text-gray-200">Reject Membership Request</h3>
            <button onclick="closeModal()" class="text-gray-400 hover:text-gray-200 hover:bg-[#ff5722] rounded-full p-1">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
        <p class="text-gray-400 mb-4">Please provide a reason for rejecting this membership request. This information may be shared with the applicant.</p>
        <form id="rejectForm" method="POST">
            @csrf
            @method('PUT')
            <input type="hidden" id="rejectUserId" name="user_id">
            <div class="mb-4">
                <label for="rejection_reason" class="block text-sm font-medium text-gray-200 mb-1">Rejection Reason</label>
                <textarea 
                    name="rejection_reason" 
                    id="rejection_reason"
                    class="w-full p-3 border border-gray-300 rounded-md focus:ring-[#ff5722] focus:border-[#ff5722] bg-[#212121] text-gray-200 placeholder-gray-400"
                    rows="4"
                    placeholder="Example: Incomplete information provided"></textarea>
            </div>
            <div class="flex justify-end gap-3">
                <button type="button" onclick="closeModal()" class="bg-gray-100 text-gray-700 px-4 py-2 rounded-md hover:bg-gray-200 transition-colors">
                    Cancel
                </button>
                <button type="submit" class="bg-red-500 text-white px-4 py-2 rounded-md hover:bg-red-600 transition-colors flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" viewBox="0 0 24 24" fill="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                    Reject
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function closeModal() {
        document.getElementById('rejectModal').classList.add('hidden');
        document.body.classList.remove('overflow-hidden');
    }

    let currentUserId = null;

    function rejectUser(userId) {
        document.getElementById('rejectUserId').value = userId;
        document.getElementById('rejectForm').action = '/staff/reject/' + userId;
        document.getElementById('rejectModal').classList.remove('hidden');
        document.body.classList.add('overflow-hidden');
    }

    // Filter options change
    document.getElementById('filterOptions').addEventListener('change', function() {
        // In a real implementation, this would filter the data
        console.log("Filter changed:", this.value);
        // Would typically trigger an AJAX request here
    });

    // Refresh button click
    document.getElementById('refreshBtn').addEventListener('click', function() {
        location.reload();
    });
</script>
@endsection