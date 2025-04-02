@extends('layouts.app')

@section('content')
<div class="p-6">
    <h2 class="text-2xl font-bold mb-4">Member Approval Dashboard</h2>

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

    <div class="bg-white p-6 shadow-md rounded-lg">
        <div class="flex justify-between items-center mb-4">
            <div>
                <h3 class="text-lg font-semibold">Pending Approvals</h3>
                <p class="text-gray-500 text-sm">Review and process new membership requests</p>
            </div>
            <div class="flex gap-2">
                <select id="filterOptions" class="border rounded-md px-3 py-2 text-sm">
                    <option value="all">All Pending</option>
                    <option value="today">Today's Requests</option>
                    <option value="week">This Week</option>
                </select>
                <button id="refreshBtn" class="bg-blue-100 text-blue-700 px-3 py-2 rounded-md text-sm hover:bg-blue-200 transition-colors flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                    </svg>
                    Refresh
                </button>
            </div>
        </div>

        @if(count($pendingUsers) > 0)
            <div class="overflow-x-auto">
                <table class="w-full border-collapse">
                    <thead>
                        <tr class="bg-gray-50 text-gray-600 text-sm">
                            <th class="border-b p-3 text-left">Full Name</th>
                            <th class="border-b p-3 text-left">Gender</th>
                            <th class="border-b p-3 text-left">Registration Date</th>
                            <th class="border-b p-3 text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($pendingUsers as $user)
                            <tr class="hover:bg-gray-50 border-b border-gray-100">
                                <td class="p-3 font-medium">{{ $user->first_name }} {{ $user->last_name }}</td>
                                <td class="p-3">{{ ucfirst($user->gender) }}</td>
                                <td class="p-3 text-gray-600">
                                    <span class="whitespace-nowrap">{{ $user->created_at->format('M d, Y') }}</span>
                                    <span class="text-gray-400 text-sm">{{ $user->created_at->format('h:i A') }}</span>
                                </td>
                                <td class="p-3 text-center">
                                    <div class="flex justify-center gap-2">
                                        <form action="{{ route('staff.approveUser', $user->id) }}" method="POST" class="inline-block">
                                            @csrf
                                            @method('PUT')
                                            <button type="submit" class="bg-green-500 text-white px-3 py-2 rounded-md text-sm hover:bg-green-600 transition-colors flex items-center">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" viewBox="0 0 20 20" fill="currentColor">
                                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                                </svg>
                                                Approve
                                            </button>
                                        </form>

                                        <!-- <button onclick="openUserDetails({{ $user->id }})" class="bg-blue-500 text-white px-3 py-2 rounded-md text-sm hover:bg-blue-600 transition-colors flex items-center">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                            </svg>
                                            View
                                        </button> -->

                                        <button onclick="rejectUser({{ $user->id }})" class="bg-red-500 text-white px-3 py-2 rounded-md text-sm hover:bg-red-600 transition-colors flex items-center">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" viewBox="0 0 20 20" fill="currentColor">
                                                <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                                            </svg>
                                            Reject
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="mt-4 flex justify-between items-center text-sm text-gray-500">
                <span>Showing {{ count($pendingUsers) }} pending requests</span>
                <div class="flex items-center">
                    <button class="px-3 py-1 border rounded-md mr-2 hover:bg-gray-50">Previous</button>
                    <span class="px-3 py-1 bg-blue-50 border border-blue-200 rounded-md">1</span>
                    <button class="px-3 py-1 border rounded-md ml-2 hover:bg-gray-50">Next</button>
                </div>
            </div>
        @else
            <div class="text-center p-8">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 mx-auto text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <h3 class="mt-3 text-lg font-medium text-gray-600">No Pending Approvals</h3>
                <p class="text-gray-400 mt-1">All membership requests have been processed</p>
            </div>
        @endif
    </div>
</div>

<!-- Reject Confirmation Modal -->
<div id="rejectModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex justify-center items-center z-50">
    <div class="bg-white p-6 rounded-lg shadow-xl w-full max-w-md">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-bold text-gray-800">Reject Membership Request</h3>
            <button onclick="closeModal()" class="text-gray-400 hover:text-gray-600">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
        <p class="text-gray-600 mb-4">Please provide a reason for rejecting this membership request. This information may be shared with the applicant.</p>
        <form id="rejectForm" method="POST">
            @csrf
            @method('PUT')
            <input type="hidden" id="rejectUserId" name="user_id">
            <div class="mb-4">
                <label for="rejection_reason" class="block text-sm font-medium text-gray-700 mb-1">Rejection Reason</label>
                <textarea 
                    name="rejection_reason" 
                    id="rejection_reason"
                    class="w-full p-3 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500"
                    rows="4"
                    placeholder="Example: Incomplete information provided"></textarea>
            </div>
            <div class="flex justify-end gap-3">
                <button type="button" onclick="closeModal()" class="bg-gray-100 text-gray-700 px-4 py-2 rounded-md hover:bg-gray-200 transition-colors">
                    Cancel
                </button>
                <button type="submit" class="bg-red-500 text-white px-4 py-2 rounded-md hover:bg-red-600 transition-colors flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                    </svg>
                    Confirm Rejection
                </button>
            </div>
        </form>
    </div>
</div>


<script>
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