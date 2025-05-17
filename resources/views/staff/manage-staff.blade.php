@extends('layouts.app')

@section('content')
<style>
    [x-cloak] { display: none !important; }
    .table-responsive {
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
    }
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
        <h2 class="text-2xl font-bold pb-1 md:text-4xl font-bold bg-clip-text text-transparent bg-gradient-to-r from-red-600 to-orange-600">Staff Management Dashboard</h2>
        <p class="text-gray-500 text-md ml-1">Manage staff accounts and roles</p>
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
                    <option value="all">All Staff</option>
                    <option value="admin">Admins</option>
                    <option value="super_admin">Super Admins</option>
                    <option value="approved">Approved</option>
                    <option value="rejected">Rejected</option>
                </select>
                <button id="refreshBtn" class="bg-[#212121] text-gray-200 border border-[#ff5722] hover:translate-y-[-2px] hover:bg-[#ff5722] px-3 py-2 rounded-md text-sm transition-colors flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                    </svg>
                    Refresh
                </button>
            </div>
            <a href="{{ route('staff.createStaff') }}" class="bg-[#ff5722] text-white px-4 py-2 rounded-md hover:bg-[#e64a19] transition-colors flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Add New Staff
            </a>
        </div>

        <div class="overflow-x-auto table-responsive">
            <table class="w-full border-collapse">
                <thead> 
                    <tr class="bg-gradient-to-br from-[#2c2c2c] to-[#1e1e1e] text-gray-200 text-sm border-b border-black">
                        <th class="border-gray-700 p-3 text-left">Full Name</th>
                        <th class="p-3 text-left">Email</th>
                        <th class="p-3 text-left">Role</th>
                        <th class="p-3 text-left">Status</th>
                        <th class="p-3 text-left">Created At</th>
                        <th class="p-3 text-center">Actions</th>
                    </tr>   
                </thead>

                <tbody class="divide-y divide-black">
                    @forelse($staffs as $staff)
                    <tr class="bg-gradient-to-br from-[#2c2c2c] to-[#1e1e1e] text-gray-200 text-sm border-b border-black">
                        <td class="p-3 font-medium text-gray-200">{{ $staff->first_name }} {{ $staff->last_name }}</td>
                        <td class="p-3 font-medium text-gray-200">{{ $staff->email }}</td>
                        <td class="p-3 font-medium text-gray-200">
                            @if($staff->role == 'super_admin')
                                <span class="px-2 py-1 rounded-full text-xs font-semibold bg-purple-900 text-purple-200">
                                    Super Admin
                                </span>
                            @elseif($staff->role == 'admin')
                                <span class="px-2 py-1 rounded-full text-xs font-semibold bg-blue-900 text-blue-200">
                                    Admin
                                </span>
                            @else
                                <span class="px-2 py-1 rounded-full text-xs font-semibold bg-gray-500 text-white">
                                    {{ $staff->role }}
                                </span>
                            @endif
                        </td>
                        <td class="p-3 font-medium text-gray-200">
                            @if($staff->session_status == 'approved')
                                <span class="px-2 py-1 rounded-full text-xs font-semibold bg-green-900 text-green-200">
                                    Approved
                                </span>
                            @elseif($staff->session_status == 'rejected')
                                <span class="px-2 py-1 rounded-full text-xs font-semibold bg-red-900 text-red-200">
                                    Rejected
                                </span>
                            @else
                                <span class="px-2 py-1 rounded-full text-xs font-semibold bg-yellow-900 text-yellow-200">
                                    Pending
                                </span>
                            @endif
                        </td>
                        <td class="p-3 font-medium">
                            <span class="text-gray-200">{{ $staff->created_at->format('M d, Y') }}</span>
                            <span class="text-gray-400 text-sm">{{ $staff->created_at->format('h:i A') }}</span>
                        </td>
                        <td class="p-3 text-center">
                            <div class="flex justify-center gap-2">
                                <a href="{{ route('staff.editStaff', $staff->id) }}" class="bg-blue-100 text-blue-700 px-3 py-2 font-bold rounded-md text-md hover:translate-y-[-2px] hover:bg-blue-400 transition-colors flex items-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                    </svg>
                                    Edit
                                </a>
                                <button onclick="deleteStaff({{ $staff->id }})" class="bg-red-100 text-red-700 px-3 py-2 font-bold rounded-md text-md hover:translate-y-[-2px] hover:bg-red-400 transition-colors flex items-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
                                    </svg>
                                    Delete
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="py-10 text-center">
                            <div class="flex flex-col items-center justify-center text-gray-400">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 mx-auto text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                                </svg>
                                <h3 class="mt-3 text-lg font-medium text-gray-200">No Staff Found</h3>
                                <p class="text-gray-300 mt-1">Add new staff members to manage your team</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<div id="deleteModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex justify-center items-center z-50">
    <div class="bg-[#121212] p-6 rounded-lg shadow-xl w-full max-w-md">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-bold text-gray-200">Delete Staff Account</h3>
            <button type="button" onclick="closeModal()" class="text-gray-400 hover:text-gray-200 hover:bg-[#ff5722] rounded-full p-1">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
        <p class="text-gray-400 mb-4">Are you sure you want to delete this staff account? This action cannot be undone.</p>
        
        <form action="{{ route('staff.deleteStaff', ['id' => 0]) }}" method="POST" id="deleteForm">
            @csrf
            @method('DELETE')
            <div class="flex justify-end gap-3 mt-3">
                <button type="button" onclick="closeModal()" class="bg-gray-100 text-gray-700 px-4 py-2 rounded-md hover:bg-gray-200 transition-colors">
                    Cancel
                </button>
                <button type="submit" class="bg-red-500 text-white px-4 py-2 rounded-md hover:bg-red-600 transition-colors flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
                    </svg>
                    Delete
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function closeModal() {
        document.getElementById('deleteModal').classList.add('hidden');
        document.body.classList.remove('overflow-hidden');
    }

    function deleteStaff(id) {
        const form = document.getElementById('deleteForm');
        let actionUrl = form.getAttribute('action');
        actionUrl = actionUrl.replace('/0', '/' + id);
        form.setAttribute('action', actionUrl);
        document.getElementById('deleteModal').classList.remove('hidden');
        document.body.classList.add('overflow-hidden');
    }

    document.addEventListener('DOMContentLoaded', function() {
        const filterOptions = document.getElementById('filterOptions');
        if (filterOptions) {
            filterOptions.addEventListener('change', function() {
                const filter = this.value;
                fetch('{{ route('staff.manageStaffs') }}?filter=' + filter, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json',
                    },
                })
                .then(response => response.json())
                .then(data => {
                    const tbody = document.querySelector('tbody');
                    tbody.innerHTML = '';
                    if (data.staffs.length === 0) {
                        tbody.innerHTML = `
                            <tr>
                                <td colspan="6" class="py-10 text-center">
                                    <div class="flex flex-col items-center justify-center text-gray-400">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 mx-auto text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                                        </svg>
                                        <h3 class="mt-3 text-lg font-medium text-gray-200">No Staff Found</h3>
                                        <p class="text-gray-300 mt-1">No staff members match the selected filter</p>
                                    </div>
                                </td>
                            </tr>`;
                    } else {
                        data.staffs.forEach(staff => {
                            const roleBadge = staff.role === 'super_admin' 
                                ? '<span class="px-2 py-1 rounded-full text-xs font-semibold bg-purple-900 text-purple-200">Super Admin</span>'
                                : '<span class="px-2 py-1 rounded-full text-xs font-semibold bg-blue-900 text-blue-200">Admin</span>';
                            const statusBadge = staff.session_status === 'approved'
                                ? '<span class="px-2 py-1 rounded-full text-xs font-semibold bg-green-900 text-green-200">Approved</span>'
                                : staff.session_status === 'rejected'
                                ? '<span class="px-2 py-1 rounded-full text-xs font-semibold bg-red-900 text-red-200">Rejected</span>'
                                : '<span class="px-2 py-1 rounded-full text-xs font-semibold bg-yellow-900 text-yellow-200">Pending</span>';
                            tbody.innerHTML += `
                                <tr class="bg-gradient-to-br from-[#2c2c2c] to-[#1e1e1e] text-gray-200 text-sm border-b border-black">
                                    <td class="p-3 font-medium text-gray-200">${staff.first_name} ${staff.last_name}</td>
                                    <td class="p-3 font-medium text-gray-200">${staff.email}</td>
                                    <td class="p-3 font-medium text-gray-200">${roleBadge}</td>
                                    <td class="p-3 font-medium text-gray-200">${statusBadge}</td>
                                    <td class="p-3 font-medium">
                                        <span class="text-gray-200">${new Date(staff.created_at).toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' })}</span>
                                        <span class="text-gray-400 text-sm">${new Date(staff.created_at).toLocaleTimeString('en-US', { hour: 'numeric', minute: 'numeric', hour12: true })}</span>
                                    </td>
                                    <td class="p-3 text-center">
                                        <div class="flex justify-center gap-2">
                                            <a href="/staff/edit-staff/${staff.id}" class="bg-blue-100 text-blue-700 px-3 py-2 font-bold rounded-md text-md hover:translate-y-[-2px] hover:bg-blue-400 transition-colors flex items-center">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                </svg>
                                                Edit
                                            </a>
                                            <button onclick="deleteStaff(${staff.id})" class="bg-red-100 text-red-700 px-3 py-2 font-bold rounded-md text-md hover:translate-y-[-2px] hover:bg-red-400 transition-colors flex items-center">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" viewBox="0 0 20 20" fill="currentColor">
                                                    <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
                                                </svg>
                                                Delete
                                            </button>
                                        </div>
                                    </td>
                                </tr>`;
                        });
                    }
                })
                .catch(error => console.error('Error filtering staff:', error));
            });
        }

        const refreshBtn = document.getElementById('refreshBtn');
        if (refreshBtn) {
            refreshBtn.addEventListener('click', function() {
                location.reload();
            });
        }
    });
</script>
@endsection