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
    /* Adjust table and column styles for responsiveness */
    @media (max-width: 768px) {
        .table-responsive table {
            min-width: 600px; /* Ensure table is scrollable with minimum width */
        }
        .table-responsive th,
        .table-responsive td {
            padding: 0.5rem;
            font-size: 0.875rem; /* Reduce font size on mobile */
        }
        .table-responsive .text-center {
            padding-left: 0.5rem;
            padding-right: 0.5rem;
        }
    }
    /* Adjust modal padding and width on smaller screens */
    @media (max-width: 640px) {
        .modal-content {
            width: 95%;
            padding: 1rem;
        }
        .modal-content form {
            padding: 1rem;
        }
        .modal-content .grid {
            grid-template-columns: 1fr; /* Stack form fields on mobile */
        }
    }
    /* Ensure buttons are touch-friendly */
    .btn-touch {
        min-width: 44px;
        min-height: 44px;
        padding: 0.75rem 1rem;
    }
</style>

<div class="p-4 sm:p-6">
    <div class="mb-6">
        <h2 class="text-xl sm:text-2xl md:text-4xl font-bold bg-clip-text text-transparent bg-gradient-to-r from-red-600 to-orange-600">Staff Management Dashboard</h2>
        <p class="text-gray-500 text-sm sm:text-md ml-1">Manage staff accounts and roles</p>
    </div>

    <div id="notification" class="hidden p-4 rounded-md mb-4 flex items-center"></div>

    <div class="p-4">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-4 space-y-3 sm:space-y-0 sm:space-x-3">
            <div class="flex flex-wrap gap-2 w-full sm:w-auto">
                <select id="filterOptions" class="bg-[#212121] rounded-md px-3 py-2 text-sm text-gray-200 focus:ring-[#ff5722] focus:border-[#ff5722] w-full sm:w-auto">
                    <option value="all">All Staff</option>
                    <option value="admin">Admins</option>
                    <option value="super_admin">Super Admins</option>
                </select>
                <button id="refreshBtn" class="bg-[#212121] text-gray-200 border border-[#ff5722] hover:translate-y-[-2px] hover:bg-[#ff5722] px-4 py-2 rounded-md text-sm transition-colors flex items-center btn-touch w-full sm:w-auto">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                    </svg>
                    Refresh
                </button>
            </div>
            <button onclick="openCreateModal()" class="bg-[#ff5722] text-white px-4 py-2 rounded-md hover:bg-[#e64a19] transition-colors flex items-center btn-touch w-full sm:w-auto">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Add New Staff
            </button>
        </div>

        <div class="overflow-x-auto table-responsive">
            <table class="w-full border-collapse">
                <thead>
                    <tr class="bg-gradient-to-br from-[#2c2c2c] to-[#1e1e1e] text-gray-200 text-sm border-b border-black">
                        <th class="border-gray-700 p-3 text-left min-w-[150px]">Full Name</th>
                        <th class="p-3 text-left min-w-[200px]">Email</th>
                        <th class="p-3 text-left min-w-[120px]">Role</th>
                        <th class="p-3 text-left min-w-[150px]">Created At</th>
                        <th class="p-3 text-center min-w-[200px]">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-black" id="staffTableBody">
                    @forelse($staffs as $staff)
                    <tr class="bg-gradient-to-br from-[#2c2c2c] to-[#1e1e1e] text-gray-200 text-sm border-b border-black" data-staff-id="{{ $staff->id }}">
                        <td class="p-3 font-medium text-gray-200">{{ $staff->first_name }} {{ $staff->last_name }}</td>
                        <td class="p-3 font-medium text-gray-200">{{ $staff->email }}</td>
                        <td class="p-3 font-medium text-gray-200">
                            @if($staff->role == 'super_admin')
                                <span class="px-2 py-1 rounded-full text-xs font-semibold bg-purple-900 text-purple-200">Super Admin</span>
                            @elseif($staff->role == 'admin')
                                <span class="px-2 py-1 rounded-full text-xs font-semibold bg-blue-900 text-blue-200">Admin</span>
                            @endif
                        </td>
                        <td class="p-3 font-medium">
                            <span class="text-gray-200">{{ $staff->created_at->format('M d, Y') }}</span>
                            <span class="text-gray-400 text-sm">{{ $staff->created_at->format('h:i A') }}</span>
                        </td>
                        <td class="p-3 text-center">
                            <div class="flex justify-center gap-2 flex-wrap">
                                <button onclick="openEditModal({{ $staff->id }})" class="bg-blue-600 text-gray-200 px-3 py-2 font-bold rounded-md text-sm hover:translate-y-[-2px] hover:bg-blue-400 transition-colors flex items-center btn-touch">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                    </svg>
                                    Edit
                                </button>
                                <button onclick="openDeleteModal({{ $staff->id }})" class="bg-red-600 text-gray-200 px-3 py-2 font-bold rounded-md text-sm hover:translate-y-[-2px] hover:bg-red-400 transition-colors flex items-center btn-touch">
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
                        <td colspan="5" class="py-10 text-center">
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

<!-- Create Staff Modal -->
<div id="createModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex justify-center items-center z-50">
    <div class="bg-[#121212] p-4 sm:p-6 rounded-lg shadow-xl w-full max-w-2xl max-h-[80vh] overflow-y-auto modal-content">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-bold text-gray-200">Create New Staff</h3>
            <button type="button" onclick="closeCreateModal()" class="text-gray-400 hover:text-gray-200 hover:bg-[#ff5722] rounded-full p-1 btn-touch">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
        <form id="createStaffForm" method="POST" class="bg-[#212121] p-4 sm:p-6 rounded-lg">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="create_first_name" class="block text-sm font-medium text-gray-200">First Name</label>
                    <input type="text" name="first_name" id="create_first_name" class="mt-1 block w-full bg-[#2c2c2c] text-gray-200 border border-gray-700 rounded-md p-2 focus:ring-[#ff5722] focus:border-[#ff5722]" required>
                </div>
                <div>
                    <label for="create_last_name" class="block text-sm font-medium text-gray-200">Last Name</label>
                    <input type="text" name="last_name" id="create_last_name" class="mt-1 block w-full bg-[#2c2c2c] text-gray-200 border border-gray-700 rounded-md p-2 focus:ring-[#ff5722] focus:border-[#ff5722]" required>
                </div>
                <div>
                    <label for="create_gender" class="block text-sm font-medium text-gray-200">Gender</label>
                    <select name="gender" id="create_gender" class="mt-1 block w-full bg-[#2c2c2c] text-gray-200 border border-gray-700 rounded-md p-2 focus:ring-[#ff5722] focus:border-[#ff5722]" required>
                        <option value="male">Male</option>
                        <option value="female">Female</option>
                        <option value="other">Other</option>
                    </select>
                </div>
                <div>
                    <label for="create_phone_number" class="block text-sm font-medium text-gray-200">Phone Number</label>
                    <input type="tel" 
                        name="phone_number" 
                        id="create_phone_number" 
                        class="mt-1 block w-full bg-[#2c2c2c] text-gray-200 border border-gray-700 rounded-md p-2 focus:ring-[#ff5722] focus:border-[#ff5722]" 
                        required
                        pattern="[0-9]{11}"
                        title="Please enter exactly 11 digits (numbers only)"
                        maxlength="11"
                        oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 11)">
                </div>
                <div>
                    <label for="create_email" class="block text-sm font-medium text-gray-200">Email</label>
                    <input type="email" name="email" id="create_email" class="mt-1 block w-full bg-[#2c2c2c] text-gray-200 border border-gray-700 rounded-md p-2 focus:ring-[#ff5722] focus:border-[#ff5722]" required>
                </div>
                <div>
                    <label for="create_role" class="block text-sm font-medium text-gray-200">Role</label>
                    <select name="role" id="create_role" class="mt-1 block w-full bg-[#2c2c2c] text-gray-200 border border-gray-700 rounded-md p-2 focus:ring-[#ff5722] focus:border-[#ff5722]" required>
                        <option value="admin">Admin</option>
                        <option value="super_admin">Super Admin</option>
                    </select>
                </div>
                <div class="relative">
                    <label for="create_password" class="block text-sm font-medium text-gray-200">Password</label>
                    <div class="relative">
                        <input type="password" name="password" id="create_password" 
                               class="mt-1 block w-full bg-[#2c2c2c] text-gray-200 border border-gray-700 rounded-md p-2 focus:ring-[#ff5722] focus:border-[#ff5722] pr-10" 
                               required
                               pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}"
                               title="Must contain at least one number, one uppercase letter, one lowercase letter, and at least 8 or more characters">
                        <button type="button" onclick="togglePasswordVisibility('create_password', 'create_password_toggle')" 
                                class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-200 mt-1" 
                                id="create_password_toggle">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>
                        </button>
                    </div>
                    <div id="password-strength-meter" class="h-1 mt-2 rounded overflow-hidden">
                        <div id="password-strength-bar" class="h-full bg-gray-600 w-0"></div>
                    </div>
                    <div id="password-requirements" class="text-xs text-gray-400 mt-2">
                        <p>Password must contain:</p>
                        <ul class="list-disc list-inside">
                            <li id="req-length" class="text-gray-400">At least 8 characters</li>
                            <li id="req-uppercase" class="text-gray-400">One uppercase letter</li>
                            <li id="req-lowercase" class="text-gray-400">One lowercase letter</li>
                            <li id="req-number" class="text-gray-400">One number</li>
                        </ul>
                    </div>
                </div>
                <div class="relative">
                    <label for="create_password_confirmation" class="block text-sm font-medium text-gray-200">Confirm Password</label>
                    <div class="relative">
                        <input type="password" name="password_confirmation" id="create_password_confirmation" 
                               class="mt-1 block w-full bg-[#2c2c2c] text-gray-200 border border-gray-700 rounded-md p-2 focus:ring-[#ff5722] focus:border-[#ff5722] pr-10" 
                               required>
                        <button type="button" onclick="togglePasswordVisibility('create_password_confirmation', 'create_password_confirmation_toggle')" 
                                class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-200 mt-1" 
                                id="create_password_confirmation_toggle">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>
                        </button>
                    </div>
                    <div id="password-match" class="text-xs mt-2 hidden">
                        <span id="match-icon" class="inline-block mr-1"></span>
                        <span id="match-text"></span>
                    </div>
                </div>
            </div>
            <div id="create_error_container" class="mt-4 hidden"></div>
            <div class="mt-6 flex justify-end gap-3 flex-wrap">
                <button type="button" onclick="closeCreateModal()" class="bg-gray-100 text-gray-700 px-4 py-2 rounded-md hover:bg-gray-200 transition-colors btn-touch">Cancel</button>
                <button type="submit" id="createSubmitBtn" class="bg-[#ff5722] text-white px-4 py-2 rounded-md hover:bg-[#e64a19] transition-colors flex items-center btn-touch">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Create Staff
                </button>
            </div>
        </form>
    </div>
</div>



<!-- Edit Staff Modal -->
<div id="editModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex justify-center items-center z-50">
    <div class="bg-[#121212] p-4 sm:p-6 rounded-lg shadow-xl w-full max-w-2xl max-h-[80vh] overflow-y-auto modal-content">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-bold text-gray-200">Edit Staff</h3>
            <button type="button" onclick="closeEditModal()" class="text-gray-400 hover:text-gray-200 hover:bg-[#ff5722] rounded-full p-1 btn-touch">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
        <form id="editStaffForm" method="POST" class="bg-[#212121] p-4 sm:p-6 rounded-lg">
            @csrf
            @method('PUT')
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="edit_first_name" class="block text-sm font-medium text-gray-200">First Name</label>
                    <input type="text" name="first_name" id="edit_first_name" class="mt-1 block w-full bg-[#2c2c2c] text-gray-200 border border-gray-700 rounded-md p-2 focus:ring-[#ff5722] focus:border-[#ff5722]" required>
                </div>
                <div>
                    <label for="edit_last_name" class="block text-sm font-medium text-gray-200">Last Name</label>
                    <input type="text" name="last_name" id="edit_last_name" class="mt-1 block w-full bg-[#2c2c2c] text-gray-200 border border-gray-700 rounded-md p-2 focus:ring-[#ff5722] focus:border-[#ff5722]" required>
                </div>
                <div>
                    <label for="edit_gender" class="block text-sm font-medium text-gray-200">Gender</label>
                    <select name="gender" id="edit_gender" class="mt-1 block w-full bg-[#2c2c2c] text-gray-200 border border-gray-700 rounded-md p-2 focus:ring-[#ff5722] focus:border-[#ff5722]" required>
                        <option value="male">Male</option>
                        <option value="female">Female</option>
                        <option value="other">Other</option>
                    </select>
                </div>
                <div>
                    <label for="edit_phone_number" class="block text-sm font-medium text-gray-200">Phone Number</label>
                    <input type="tel" 
                        name="phone_number" 
                        id="edit_phone_number" 
                        class="mt-1 block w-full bg-[#2c2c2c] text-gray-200 border border-gray-700 rounded-md p-2 focus:ring-[#ff5722] focus:border-[#ff5722]" 
                        required
                        pattern="[0-9]{11}"
                        title="Please enter exactly 11 digits (numbers only)"
                        maxlength="11"
                        oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 11)">
                </div>
                <div>
                    <label for="edit_email" class="block text-sm font-medium text-gray-200">Email</label>
                    <input type="email" name="email" id="edit_email" class="mt-1 block w-full bg-[#2c2c2c] text-gray-200 border border-gray-700 rounded-md p-2 focus:ring-[#ff5722] focus:border-[#ff5722]" required>
                </div>
                <div>
                    <label for="edit_role" class="block text-sm font-medium text-gray-200">Role</label>
                    <select name="role" id="edit_role" class="mt-1 block w-full bg-[#2c2c2c] text-gray-200 border border-gray-700 rounded-md p-2 focus:ring-[#ff5722] focus:border-[#ff5722]" required>
                        <option value="admin">Admin</option>
                        <option value="super_admin">Super Admin</option>
                    </select>
                </div>
                <div class="relative">
                    <label for="edit_password" class="block text-sm font-medium text-gray-200">Password (Leave blank to keep current)</label>
                    <div class="relative">
                        <input type="password" 
                               name="password" 
                               id="edit_password" 
                               class="mt-1 block w-full bg-[#2c2c2c] text-gray-200 border border-gray-700 rounded-md p-2 focus:ring-[#ff5722] focus:border-[#ff5722] pr-10"
                               pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}"
                               title="Must contain at least one number, one uppercase letter, one lowercase letter, and at least 8 or more characters">
                        <button type="button" 
                                onclick="togglePasswordVisibility('edit_password', 'edit_password_toggle')" 
                                class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-200 mt-1" 
                                id="edit_password_toggle">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>
                        </button>
                    </div>
                    <div id="edit-password-strength-meter" class="h-1 mt-2 rounded overflow-hidden">
                        <div id="edit-password-strength-bar" class="h-full bg-gray-600 w-0"></div>
                    </div>
                    <div id="edit-password-requirements" class="text-xs text-gray-400 mt-2">
                        <p>Password must contain:</p>
                        <ul class="list-disc list-inside">
                            <li id="edit-req-length" class="text-gray-400">At least 8 characters</li>
                            <li id="edit-req-uppercase" class="text-gray-400">One uppercase letter</li>
                            <li id="edit-req-lowercase" class="text-gray-400">One lowercase letter</li>
                            <li id="edit-req-number" class="text-gray-400">One number</li>
                        </ul>
                    </div>
                </div>
                <div class="relative">
                    <label for="edit_password_confirmation" class="block text-sm font-medium text-gray-200">Confirm Password</label>
                    <div class="relative">
                        <input type="password" 
                               name="password_confirmation" 
                               id="edit_password_confirmation" 
                               class="mt-1 block w-full bg-[#2c2c2c] text-gray-200 border border-gray-700 rounded-md p-2 focus:ring-[#ff5722] focus:border-[#ff5722] pr-10">
                        <button type="button" 
                                onclick="togglePasswordVisibility('edit_password_confirmation', 'edit_password_confirmation_toggle')" 
                                class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-200 mt-1" 
                                id="edit_password_confirmation_toggle">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>
                        </button>
                    </div>
                    <div id="edit-password-match" class="text-xs mt-2 hidden">
                        <span id="edit-match-icon" class="inline-block mr-1"></span>
                        <span id="edit-match-text"></span>
                    </div>
                </div>
            </div>
            <div id="edit_error_container" class="mt-4 hidden"></div>
            <div class="mt-6 flex justify-end gap-3 flex-wrap">
                <button type="button" onclick="closeEditModal()" class="bg-gray-100 text-gray-700 px-4 py-2 rounded-md hover:bg-gray-200 transition-colors btn-touch">Cancel</button>
                <button type="submit" id="editSubmitBtn" class="bg-[#ff5722] text-white px-4 py-2 rounded-md hover:bg-[#e64a19] transition-colors flex items-center btn-touch">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    Update Staff
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    // Add these event listeners for the edit modal
    document.getElementById('edit_password').addEventListener('input', function() {
        checkEditPasswordStrength(this.value);
        checkEditPasswordMatch();
    });

    document.getElementById('edit_password_confirmation').addEventListener('input', checkEditPasswordMatch);

    function checkEditPasswordStrength(password) {
        const strengthBar = document.getElementById('edit-password-strength-bar');
        const requirements = {
            length: password.length >= 8,
            uppercase: /[A-Z]/.test(password),
            lowercase: /[a-z]/.test(password),
            number: /[0-9]/.test(password)
        };

        // Update requirement indicators
        document.getElementById('edit-req-length').className = requirements.length ? 'text-green-400' : 'text-gray-400';
        document.getElementById('edit-req-uppercase').className = requirements.uppercase ? 'text-green-400' : 'text-gray-400';
        document.getElementById('edit-req-lowercase').className = requirements.lowercase ? 'text-green-400' : 'text-gray-400';
        document.getElementById('edit-req-number').className = requirements.number ? 'text-green-400' : 'text-gray-400';

        // Calculate strength score
        let strength = 0;
        if (requirements.length) strength += 25;
        if (requirements.uppercase) strength += 25;
        if (requirements.lowercase) strength += 25;
        if (requirements.number) strength += 25;

        // Update strength bar
        strengthBar.style.width = strength + '%';
        if (strength < 50) {
            strengthBar.className = 'h-full bg-red-500';
        } else if (strength < 75) {
            strengthBar.className = 'h-full bg-yellow-500';
        } else {
            strengthBar.className = 'h-full bg-green-500';
        }
    }

    function checkEditPasswordMatch() {
        const password = document.getElementById('edit_password').value;
        const confirmPassword = document.getElementById('edit_password_confirmation').value;
        const matchDiv = document.getElementById('edit-password-match');
        const matchIcon = document.getElementById('edit-match-icon');
        const matchText = document.getElementById('edit-match-text');

        if (password === '' || confirmPassword === '') {
            matchDiv.classList.add('hidden');
            return;
        }

        matchDiv.classList.remove('hidden');
        
        if (password === confirmPassword) {
            matchDiv.className = 'text-xs mt-2 text-green-400';
            matchIcon.innerHTML = `
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
            `;
            matchText.textContent = 'Passwords match';
        } else {
            matchDiv.className = 'text-xs mt-2 text-red-400';
            matchIcon.innerHTML = `
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            `;
            matchText.textContent = 'Passwords do not match';
        }
    }
</script>

<!-- Delete Staff Modal -->
<div id="deleteModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex justify-center items-center z-50">
    <div class="bg-[#121212] p-4 sm:p-6 rounded-lg shadow-xl w-full max-w-md modal-content">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-bold text-gray-200">Delete Staff Account</h3>
            <button type="button" onclick="closeDeleteModal()" class="text-gray-400 hover:text-gray-200 hover:bg-[#ff5722] rounded-full p-1 btn-touch">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
        <p class="text-gray-400 mb-4">Are you sure you want to delete this staff account? This action cannot be undone.</p>
        <form id="deleteForm" method="POST">
            @csrf
            @method('DELETE')
            <div class="flex justify-end gap-3 mt-3 flex-wrap">
                <button type="button" onclick="closeDeleteModal()" class="bg-gray-100 text-gray-700 px-4 py-2 rounded-md hover:bg-gray-200 transition-colors btn-touch">Cancel</button>
                <button type="submit" id="deleteSubmitBtn" class="bg-red-500 text-white px-4 py-2 rounded-md hover:bg-red-600 transition-colors flex items-center btn-touch">
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
    function togglePasswordVisibility(inputId, toggleId) {
        const input = document.getElementById(inputId);
        const toggle = document.getElementById(toggleId);
        
        if (input.type === 'password') {
            input.type = 'text';
            toggle.innerHTML = `
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                </svg>
            `;
        } else {
            input.type = 'password';
            toggle.innerHTML = `
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                </svg>
            `;
        }
    }

    document.getElementById('create_password').addEventListener('input', function() {
        checkPasswordStrength(this.value);
        checkPasswordMatch();
    });

    document.getElementById('create_password_confirmation').addEventListener('input', checkPasswordMatch);

    function checkPasswordStrength(password) {
        const strengthBar = document.getElementById('password-strength-bar');
        const requirements = {
            length: password.length >= 8,
            uppercase: /[A-Z]/.test(password),
            lowercase: /[a-z]/.test(password),
            number: /[0-9]/.test(password)
        };

        // Update requirement indicators
        document.getElementById('req-length').className = requirements.length ? 'text-green-400' : 'text-gray-400';
        document.getElementById('req-uppercase').className = requirements.uppercase ? 'text-green-400' : 'text-gray-400';
        document.getElementById('req-lowercase').className = requirements.lowercase ? 'text-green-400' : 'text-gray-400';
        document.getElementById('req-number').className = requirements.number ? 'text-green-400' : 'text-gray-400';

        // Calculate strength score
        let strength = 0;
        if (requirements.length) strength += 25;
        if (requirements.uppercase) strength += 25;
        if (requirements.lowercase) strength += 25;
        if (requirements.number) strength += 25;

        // Update strength bar
        strengthBar.style.width = strength + '%';
        if (strength < 50) {
            strengthBar.className = 'h-full bg-red-500';
        } else if (strength < 75) {
            strengthBar.className = 'h-full bg-yellow-500';
        } else {
            strengthBar.className = 'h-full bg-green-500';
        }
    }

    function checkPasswordMatch() {
        const password = document.getElementById('create_password').value;
        const confirmPassword = document.getElementById('create_password_confirmation').value;
        const matchDiv = document.getElementById('password-match');
        const matchIcon = document.getElementById('match-icon');
        const matchText = document.getElementById('match-text');

        if (password === '' || confirmPassword === '') {
            matchDiv.classList.add('hidden');
            return;
        }

        matchDiv.classList.remove('hidden');
        
        if (password === confirmPassword) {
            matchDiv.className = 'text-xs mt-2 text-green-400';
            matchIcon.innerHTML = `
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
            `;
            matchText.textContent = 'Passwords match';
        } else {
            matchDiv.className = 'text-xs mt-2 text-red-400';
            matchIcon.innerHTML = `
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            `;
            matchText.textContent = 'Passwords do not match';
        }
    }
</script>


<script>
    let isSubmitting = false;

    function showNotification(message, type) {
        const notification = document.getElementById('notification');
        if (!notification) {
            console.error('Notification element not found');
            return;
        }
        notification.className = `p-4 rounded-md mb-4 flex items-center ${type === 'success' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700'}`;
        notification.innerHTML = `
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                ${type === 'success' ? '<path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />' : '<path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />'}
            </svg>
            ${message}
        `;
        notification.classList.remove('hidden');
        setTimeout(() => {
            if (notification) notification.classList.add('hidden');
        }, 5000);
    }

    function displayFormErrors(formId, errors) {
        const errorContainer = document.getElementById(`${formId}_error_container`);
        if (!errorContainer) {
            console.error(`Error container for ${formId} not found`, { errors });
            showNotification('Cannot display errors: Form error container missing', 'error');
            return;
        }
        errorContainer.innerHTML = '';
        errorContainer.classList.remove('hidden');
        if (!errors || typeof errors !== 'object') {
            console.error(`Invalid errors format for ${formId}`, { errors });
            showNotification('Cannot display errors: Invalid error format', 'error');
            return;
        }
        for (const [field, messages] of Object.entries(errors)) {
            if (Array.isArray(messages) && messages.length > 0) {
                const errorDiv = document.createElement('div');
                errorDiv.className = 'text-red-500 text-sm';
                errorDiv.textContent = messages[0];
                errorContainer.appendChild(errorDiv);
                const input = document.querySelector(`#${formId} [name="${field}"]`);
                if (input) {
                    input.classList.add('border-red-500');
                    input.addEventListener('input', () => input.classList.remove('border-red-500'), { once: true });
                }
            }
        }
        if (errorContainer.innerHTML === '') {
            errorContainer.classList.add('hidden');
        }
    }

    function updateStaffTable(staffs) {
        const tbody = document.getElementById('staffTableBody');
        if (!tbody) {
            console.error('Staff table body not found');
            showNotification('Cannot update table: Table body missing', 'error');
            return;
        }
        tbody.innerHTML = '';
        if (!staffs || !Array.isArray(staffs) || staffs.length === 0) {
            tbody.innerHTML = `
                <tr>
                    <td colspan="5" class="py-10 text-center">
                        <div class="flex flex-col items-center justify-center text-gray-400">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 mx-auto text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                            </svg>
                            <h3 class="mt-3 text-lg font-medium text-gray-200">No Staff Found</h3>
                            <p class="text-gray-300 mt-1">No staff members match the selected filter</p>
                        </div>
                    </td>
                </tr>`;
            return;
        }
        staffs.forEach(staff => updateStaffTableRow(staff, true));
    }

    function updateStaffTableRow(staff, isNew = false) {
        const tbody = document.getElementById('staffTableBody');
        if (!tbody) {
            console.error('Staff table body not found');
            showNotification('Cannot update table row: Table body missing', 'error');
            return;
        }
        if (!staff || !staff.id) {
            console.error('Invalid staff data:', staff);
            showNotification('Cannot update table row: Invalid staff data', 'error');
            return;
        }
        const existingRow = document.querySelector(`tr[data-staff-id="${staff.id}"]`);
        const roleBadge = staff.role === 'super_admin'
            ? '<span class="px-2 py-1 rounded-full text-xs font-semibold bg-purple-900 text-purple-200">Super Admin</span>'
            : '<span class="px-2 py-1 rounded-full text-xs font-semibold bg-blue-900 text-blue-200">Admin</span>';
        const rowHtml = `
            <tr class="bg-gradient-to-br from-[#2c2c2c] to-[#1e1e1e] text-gray-200 text-sm border-b border-black" data-staff-id="${staff.id}">
                <td class="p-3 font-medium text-gray-200">${staff.first_name || ''} ${staff.last_name || ''}</td>
                <td class="p-3 font-medium text-gray-200">${staff.email || ''}</td>
                <td class="p-3 font-medium text-gray-200">${roleBadge}</td>
                <td class="p-3 font-medium">
                    <span class="text-gray-200">${staff.created_at ? new Date(staff.created_at).toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' }) : ''}</span>
                    <span class="text-gray-400 text-sm">${staff.created_at ? new Date(staff.created_at).toLocaleTimeString('en-US', { hour: 'numeric', minute: 'numeric', hour12: true }) : ''}</span>
                </td>
                <td class="p-3 text-center">
                    <div class="flex justify-center gap-2 flex-wrap">
                        <button onclick="openEditModal(${staff.id})" class="bg-blue-100 text-blue-700 px-3 py-2 font-bold rounded-md text-sm hover:translate-y-[-2px] hover:bg-blue-400 transition-colors flex items-center btn-touch">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                            </svg>
                            Edit
                        </button>
                        <button onclick="openDeleteModal(${staff.id})" class="bg-red-100 text-red-700 px-3 py-2 font-bold rounded-md text-sm hover:translate-y-[-2px] hover:bg-red-400 transition-colors flex items-center btn-touch">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
                            </svg>
                            Delete
                        </button>
                    </div>
                </td>
            </tr>`;
        if (existingRow && !isNew) {
            existingRow.outerHTML = rowHtml;
        } else {
            tbody.insertAdjacentHTML('afterbegin', rowHtml);
        }
    }

    function openCreateModal() {
        fetch('{{ route('staff.createStaff') }}', {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            },
        })
        .then(response => response.json())
        .then(data => {
            if (!data.form) {
                console.error('Invalid create form data:', data);
                showNotification('Error loading create form: Invalid data', 'error');
                return;
            }
            document.getElementById('create_first_name').value = data.form.first_name || '';
            document.getElementById('create_last_name').value = data.form.last_name || '';
            document.getElementById('create_gender').value = data.form.gender || '';
            document.getElementById('create_phone_number').value = data.form.phone_number || '';
            document.getElementById('create_email').value = data.form.email || '';
            document.getElementById('create_role').value = data.form.role || 'admin';
            document.getElementById('create_password').value = '';
            document.getElementById('create_password_confirmation').value = '';
            document.getElementById('create_error_container').classList.add('hidden');
            document.getElementById('createModal').classList.remove('hidden');
            document.body.classList.add('overflow-hidden');
        })
        .catch(error => {
            console.error('Fetch error:', error);
            showNotification('Error loading create form: ' + error.message, 'error');
        });
    }

    function closeCreateModal() {
        const modal = document.getElementById('createModal');
        if (modal) {
            modal.classList.add('hidden');
            document.body.classList.remove('overflow-hidden');
        }
        const form = document.getElementById('createStaffForm');
        if (form) form.reset();
        const errorContainer = document.getElementById('create_error_container');
        if (errorContainer) errorContainer.classList.add('hidden');
        document.querySelectorAll('#createStaffForm .border-red-500').forEach(el => el.classList.remove('border-red-500'));
    }

    function openEditModal(id) {
        fetch('{{ route('staff.editStaff', ':id') }}'.replace(':id', id), {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            },
        })
        .then(response => response.json())
        .then(data => {
            if (!data.staff) {
                console.error('Invalid edit staff data:', data);
                showNotification('Error loading edit form: Invalid data', 'error');
                return;
            }
            const form = document.getElementById('editStaffForm');
            if (form) form.dataset.staffId = id;
            document.getElementById('edit_first_name').value = data.staff.first_name || '';
            document.getElementById('edit_last_name').value = data.staff.last_name || '';
            document.getElementById('edit_gender').value = data.staff.gender || '';
            document.getElementById('edit_phone_number').value = data.staff.phone_number || '';
            document.getElementById('edit_email').value = data.staff.email || '';
            document.getElementById('edit_role').value = data.staff.role || 'admin';
            document.getElementById('edit_password').value = '';
            document.getElementById('edit_password_confirmation').value = '';
            const errorContainer = document.getElementById('edit_error_container');
            if (errorContainer) errorContainer.classList.add('hidden');
            document.getElementById('editModal').classList.remove('hidden');
            document.body.classList.add('overflow-hidden');
        })
        .catch(error => {
            console.error('Fetch error:', error);
            showNotification('Error loading edit form: ' + error.message, 'error');
        });
    }

    function closeEditModal() {
        const modal = document.getElementById('editModal');
        if (modal) {
            modal.classList.add('hidden');
            document.body.classList.remove('overflow-hidden');
        }
        const form = document.getElementById('editStaffForm');
        if (form) form.reset();
        const errorContainer = document.getElementById('edit_error_container');
        if (errorContainer) errorContainer.classList.add('hidden');
        document.querySelectorAll('#editStaffForm .border-red-500').forEach(el => el.classList.remove('border-red-500'));
    }

    function openDeleteModal(id) {
        const form = document.getElementById('deleteForm');
        if (form) {
            form.dataset.staffId = id;
            document.getElementById('deleteModal').classList.remove('hidden');
            document.body.classList.add('overflow-hidden');
        } else {
            console.error('Delete form not found');
            showNotification('Cannot open delete modal: Form missing', 'error');
        }
    }

    function closeDeleteModal() {
        const modal = document.getElementById('deleteModal');
        if (modal) {
            modal.classList.add('hidden');
            document.body.classList.remove('overflow-hidden');
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
        if (!csrfToken) {
            console.error('CSRF token not found');
            showNotification('Error: CSRF token missing', 'error');
            return;
        }

        const filterOptions = document.getElementById('filterOptions');
        if (filterOptions) {
            filterOptions.addEventListener('change', function() {
                if (isSubmitting) return;
                const filter = this.value;
                fetch('{{ route('staff.manageStaffs') }}?filter=' + filter, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                    },
                })
                .then(response => response.json())
                .then(data => {
                    if (data.staffs) {
                        updateStaffTable(data.staffs);
                    } else {
                        console.error('Invalid filter response:', data);
                        showNotification('Error filtering staff: Invalid data', 'error');
                    }
                })
                .catch(error => {
                    console.error('Fetch error:', error);
                    showNotification('Error filtering staff: ' + error.message, 'error');
                });
            });
        }

        const refreshBtn = document.getElementById('refreshBtn');
        if (refreshBtn) {
            refreshBtn.addEventListener('click', function() {
                if (isSubmitting) return;
                location.reload();
            });
        }

        const createForm = document.getElementById('createStaffForm');
        if (createForm) {
            createForm.addEventListener('submit', function(e) {
                e.preventDefault();
                if (isSubmitting) return;
                isSubmitting = true;
                const submitButton = document.getElementById('createSubmitBtn');
                if (submitButton) {
                    submitButton.disabled = true;
                    submitButton.innerHTML = '<svg class="animate-spin h-4 w-4 mr-1" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" fill="none" /></svg> Creating...';
                }
                const formData = new FormData(createForm);
                fetch('{{ route('staff.storeStaff') }}', {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                    },
                })
                .then(response => response.json())
                .then(data => {
                    console.log('Create staff response:', data);
                    if (data.success && data.staff) {
                        closeCreateModal();
                        showNotification(data.message, 'success');
                        updateStaffTableRow(data.staff, true);
                    } else {
                        showNotification(data.message || 'Validation errors occurred.', 'error');
                        if (data.errors) {
                            displayFormErrors('createStaffForm', data.errors);
                        }
                    }
                })
                .catch(error => {
                    console.error('Fetch error:', error);
                    showNotification('Error creating staff: ' + error.message, 'error');
                })
                .finally(() => {
                    isSubmitting = false;
                    if (submitButton) {
                        submitButton.disabled = false;
                        submitButton.innerHTML = `
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                            </svg>
                            Create Staff
                        `;
                    }
                });
            });
        }

        const editForm = document.getElementById('editStaffForm');
        if (editForm) {
            editForm.addEventListener('submit', function(e) {
                e.preventDefault();
                if (isSubmitting) return;
                isSubmitting = true;
                const submitButton = document.getElementById('editSubmitBtn');
                if (submitButton) {
                    submitButton.disabled = true;
                    submitButton.innerHTML = '<svg class="animate-spin h-4 w-4 mr-1" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" fill="none" /></svg> Updating...';
                }
                const formData = new FormData(editForm);
                const staffId = editForm.dataset.staffId;
                fetch('{{ route('staff.updateStaff', ':id') }}'.replace(':id', staffId), {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                    },
                })
                .then(response => {
                    if (!response.ok) {
                        throw new Error(`HTTP error! Status: ${response.status}`);
                    }
                    return response.json();
                })
                .then(data => {
                    console.log('Update staff response:', data);
                    if (data.success && data.staff) {
                        closeEditModal();
                        showNotification(data.message, 'success');
                        updateStaffTableRow(data.staff, false);
                    } else {
                        showNotification(data.message || 'Validation errors occurred.', 'error');
                        if (data.errors) {
                            displayFormErrors('editStaffForm', data.errors);
                        } else {
                            console.warn('No errors provided in response:', data);
                        }
                    }
                })
                .catch(error => {
                    console.error('Update staff error:', {
                        message: error.message,
                        stack: error.stack,
                        response: error.response || 'No response data'
                    });
                    showNotification('Error updating staff: ' + error.message, 'error');
                })
                .finally(() => {
                    isSubmitting = false;
                    if (submitButton) {
                        submitButton.disabled = false;
                        submitButton.innerHTML = `
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            Update Staff
                        `;
                    }
                });
            });
        }

        const deleteForm = document.getElementById('deleteForm');
        if (deleteForm) {
            deleteForm.addEventListener('submit', function(e) {
                e.preventDefault();
                if (isSubmitting) return;
                isSubmitting = true;
                const submitButton = document.getElementById('deleteSubmitBtn');
                if (submitButton) {
                    submitButton.disabled = true;
                    submitButton.innerHTML = '<svg class="animate-spin h-4 w-4 mr-1" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" fill="none" /></svg> Deleting...';
                }
                const staffId = deleteForm.dataset.staffId;
                fetch('{{ route('staff.deleteStaff', ':id') }}'.replace(':id', staffId), {
                    method: 'DELETE',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                    },
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        closeDeleteModal();
                        showNotification(data.message, 'success');
                        const row = document.querySelector(`tr[data-staff-id="${staffId}"]`);
                        if (row) row.remove();
                        else console.warn(`Row with staff-id ${staffId} not found`);
                    } else {
                        showNotification(data.message || 'Error deleting staff', 'error');
                    }
                })
                .catch(error => {
                    console.error('Fetch error:', error);
                    showNotification('Error deleting staff: ' + error.message, 'error');
                })
                .finally(() => {
                    isSubmitting = false;
                    if (submitButton) {
                        submitButton.disabled = false;
                        submitButton.innerHTML = `
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
                            </svg>
                            Delete
                        `;
                    }
                });
            });
        }
    });
</script>
@endsection