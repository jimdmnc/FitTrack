@extends('layouts.app')

@section('content')
<style>
    [x-cloak] { display: none !important; }
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

    <div class="p-4">
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
                        <th class="p-3 text-left">Membership Type</th>
                        <th class="p-3 text-left">Payment Method</th>
                        <th class="p-3 text-left">Registration Date</th>
                        <th class="p-3 text-center">Actions</th>
                    </tr>
                </thead>
                <tbody id="pending-users-table" class="divide-y divide-black">
                    <!-- Populated by JavaScript -->
                </tbody>
            </table>
        </div>

        <div id="no-users-message" class="hidden py-10 text-center">
            <div class="flex flex-col items-center justify-center text-gray-400">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 mx-auto text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <h3 class="mt-3 text-lg font-medium text-gray-200">No Pending Approvals</h3>
                <p class="text-gray-300 mt-1">All membership requests have been processed</p>
            </div>
        </div>
    </div>
</div>


<!-- Add Approval Confirmation Modal after the Reject Confirmation Modal -->
<div id="approveModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex justify-center items-center z-50">
    <div class="bg-[#121212] p-6 rounded-lg shadow-xl w-full max-w-md">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-bold text-gray-200">Approve Membership Request</h3>
            <button type="button" onclick="closeApproveModal()" class="text-gray-400 hover:text-gray-200 hover:bg-[#ff5722] rounded-full p-1">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
        <p class="text-gray-400 mb-4">Are you sure you want to approve this membership request?</p>
        <form id="approveForm" action="{{ route('staff.approveUser', ['id' => 0]) }}" method="POST">
            @csrf
            <input type="hidden" name="_method" value="PUT">
            <div class="flex justify-end gap-3 mt-3">
                <button type="button" onclick="closeApproveModal()" class="bg-gray-100 text-gray-700 px-4 py-2 rounded-md hover:bg-gray-200 transition-colors">
                    Cancel
                </button>
                <button type="submit" class="bg-green-500 text-white px-4 py-2 rounded-md hover:bg-green-600 transition-colors flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                    </svg>
                    Approve
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Reject Confirmation Modal -->
<div id="rejectModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex justify-center items-center z-50">
    <div class="bg-[#121212] p-6 rounded-lg shadow-xl w-full max-w-md">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-bold text-gray-200">Reject Membership Request</h3>
            <button type="button" onclick="closeModal()" class="text-gray-400 hover:text-gray-200 hover:bg-[#ff5722] rounded-full p-1">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
        <p class="text-gray-400 mb-4">Please provide a reason for rejecting this membership request. This information may be shared with the applicant.</p>
        <form action="{{ route('staff.rejectUser', ['id' => 0]) }}" method="POST" id="rejectForm">
            @csrf
            <textarea name="rejection_reason" id="rejection_reason" class="w-full p-3 border border-gray-300 rounded-md focus:ring-[#ff5722] focus:border-[#ff5722] bg-[#212121] text-gray-200 placeholder-gray-400" rows="4" placeholder="Example: Incomplete information provided"></textarea>
            <div class="flex justify-end gap-3 mt-3">
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

<!-- Modal for Viewing Payment Screenshot -->
<div id="screenshotModal" class="fixed inset-0 flex items-center justify-center hidden z-50 bg-black bg-opacity-70">
    <div class="bg-[#1e1e1e] rounded-lg p-4 max-w-5xl w-auto mx-4 relative">
        <div class="flex justify-between items-center mb-3">
            <h3 class="text-sm font-semibold text-gray-200">Payment Screenshot</h3>
            <button onclick="closeScreenshotModal()" class="text-gray-400 hover:text-gray-200">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
        <div class="flex justify-center">
            <img id="screenshotImage" src="" alt="Payment Screenshot"
                class="max-w-[500vw] max-h-[80vh] object-contain rounded-md shadow-lg" />
        </div>
        <div class="mt-3 flex justify-center">
            <button onclick="closeScreenshotModal()" class="bg-gray-600 text-gray-200 px-3 py-1 rounded-md hover:bg-gray-500 text-sm">
                Close
            </button>
        </div>
    </div>
</div>


<script>
    function approveUser(id) {
        const form = document.getElementById('approveForm');
        let actionUrl = form.getAttribute('action');
        actionUrl = actionUrl.replace('/0', '/' + id);
        form.setAttribute('action', actionUrl);
        document.getElementById('approveModal').classList.remove('hidden');
        document.body.classList.add('overflow-hidden');
    }

    function openScreenshotModal(imageUrl) {
        const modal = document.getElementById('screenshotModal');
        const image = document.getElementById('screenshotImage');
        image.src = imageUrl;
        modal.classList.remove('hidden');
    }

    function closeScreenshotModal() {
        const modal = document.getElementById('screenshotModal');
        modal.classList.add('hidden');
        document.getElementById('screenshotImage').src = '';
    }

    function closeModal() {
        document.getElementById('rejectModal').classList.add('hidden');
        document.body.classList.remove('overflow-hidden');
    }

    function rejectUser(id) {
        const form = document.getElementById('rejectForm');
        let actionUrl = form.getAttribute('action');
        actionUrl = actionUrl.replace('/0', '/' + id);
        form.setAttribute('action', actionUrl);
        document.getElementById('rejectModal').classList.remove('hidden');
        document.body.classList.add('overflow-hidden');
    }

    function closeApproveModal() {
        document.getElementById('approveModal').classList.add('hidden');
        document.body.classList.remove('overflow-hidden');
    }

    // Update the table row in initPendingUsersTable to use approveUser
function initPendingUsersTable() {
    const tableBody = document.getElementById('pending-users-table');
    const noUsersMessage = document.getElementById('no-users-message');
    const filterOptions = document.getElementById('filterOptions');
    const refreshBtn = document.getElementById('refreshBtn');

    async function fetchPendingUsers(filter = 'all') {
        try {
            const response = await fetch('{{ route('staff.pendingUsers') }}?filter=' + filter, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            });
            if (!response.ok) throw new Error('Network response was not ok');
            const data = await response.json();

            if (data.success) {
                tableBody.innerHTML = '';
                noUsersMessage.classList.add('hidden');

                if (data.users.length === 0) {
                    noUsersMessage.classList.remove('hidden');
                    return;
                }

                data.users.forEach(user => {
                    const membershipLabels = {
                        '7': { text: 'Weekly', class: 'bg-green-900 text-green-200' },
                        '1': { text: 'Session', class: 'bg-yellow-900 text-yellow-200' },
                        '30': { text: 'Monthly', class: 'bg-blue-900 text-blue-200' },
                        '365': { text: 'Annual', class: 'bg-purple-900 text-purple-200' }
                    };
                    const membership = membershipLabels[user.membership_type] || { 
                        text: user.membership_type || 'N/A', 
                        class: 'bg-green-900 text-green-200' 
                    };

                    const paymentMethod = user.payment_method === 'gcash' 
                        ? `<button onclick="openScreenshotModal('${user.payment_screenshot}')" class="px-2 py-1 rounded-full text-xs font-semibold bg-green-900 text-green-200 hover:bg-green-700 transition-colors">GCASH</button>`
                        : user.payment_method === 'cash'
                        ? `<span class="px-2 py-1 rounded-full text-xs font-semibold bg-yellow-900 text-yellow-200">CASH</span>`
                        : `<span class="px-2 py-1 rounded-full text-xs font-semibold bg-gray-500 text-white">Unknown</span>`;

                    const row = `
                        <tr class="bg-gradient-to-br from-[#2c2c2c] to-[#1e1e1e] text-gray-200 text-sm border-b border-black">
                            <td class="p-3 font-medium text-gray-200">${user.first_name} ${user.last_name}</td>
                            <td class="p-3 font-medium text-gray-200">${user.gender}</td>
                            <td class="p-3 font-medium text-gray-200">
                                <span class="px-2 py-1 rounded-full text-xs font-semibold ${membership.class}">
                                    ${membership.text}
                                </span>
                            </td>
                            <td class="p-3 font-medium text-gray-200">${paymentMethod}</td>
                            <td class="p-3 font-medium">
                                <span class="text-gray-200">${user.updated_at.date}</span>
                                <span class="text-gray-400 text-sm">${user.updated_at.time}</span>
                            </td>
                            <td class="p-3 text-center">
                                <div class="flex justify-center gap-2">
                                    <button onclick="approveUser(${user.id})" class="bg-green-600 text-gray-200 px-3 py-2 font-bold rounded-md text-md hover:translate-y-[-2px] hover:bg-green-400 transition-colors flex items-center">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                        </svg>
                                        Approve
                                    </button>
                                    <button onclick="rejectUser(${user.id})" class="bg-red-600 text-gray-200 px-3 py-2 font-bold rounded-md text-md hover:translate-y-[-2px] hover:bg-red-400 transition-colors flex items-center">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                                        </svg>
                                        Reject
                                    </button>
                                </div>
                            </td>
                        </tr>
                    `;
                    tableBody.insertAdjacentHTML('beforeend', row);
                });
            } else {
                console.error('Failed to fetch pending users:', data.message);
                showNotification('Error', data.message || 'Failed to load pending users.', 'error');
            }
        } catch (error) {
            console.error('Error fetching pending users:', error);
            showNotification('Error', 'Failed to load pending users.', 'error');
        }
    }

    // Fetch on page load
    fetchPendingUsers(filterOptions.value);

    // Filter change
    filterOptions.addEventListener('change', function() {
        fetchPendingUsers(this.value);
    });

    // Refresh button
    refreshBtn.addEventListener('click', function() {
        fetchPendingUsers(filterOptions.value);
    });

    // Poll every 30 seconds
    setInterval(() => fetchPendingUsers(filterOptions.value), 30000);
}

    document.addEventListener('DOMContentLoaded', function() {
        initPendingUsersTable();
    });
</script>
@endsection