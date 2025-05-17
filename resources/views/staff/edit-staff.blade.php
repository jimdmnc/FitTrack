@extends('layouts.app')

@section('content')
<div class="p-6">
    <div class="mb-6">
        <h2 class="text-2xl font-bold pb-1 md:text-4xl font-bold bg-clip-text text-transparent bg-gradient-to-r from-red-600 to-orange-600">Create New Staff</h2>
        <p class="text-gray-500 text-md ml-1">Add a new staff member to the system</p>
    </div>

    @if ($errors->any())
        <div class="bg-red-100 text-red-700 p-4 rounded-md mb-4 flex items-center">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
            </svg>
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('staff.storeStaff') }}" method="POST" class="bg-[#212121] p-6 rounded-lg">
        @csrf
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label for="first_name" class="block text-sm font-medium text-gray-200">First Name</label>
                <input type="text" name="first_name" id="first_name" value="{{ old('first_name') }}" class="mt-1 block w-full bg-[#2c2c2c] text-gray-200 border border-gray-700 rounded-md p-2 focus:ring-[#ff5722] focus:border-[#ff5722]" required>
            </div>
            <div>
                <label for="last_name" class="block text-sm font-medium text-gray-200">Last Name</label>
                <input type="text" name="last_name" id="last_name" value="{{ old('last_name') }}" class="mt-1 block w-full bg-[#2c2c2c] text-gray-200 border border-gray-700 rounded-md p-2 focus:ring-[#ff5722] focus:border-[#ff5722]" required>
            </div>
            <div>
                <label for="gender" class="block text-sm font-medium text-gray-200">Gender</label>
                <select name="gender" id="gender" class="mt-1 block w-full bg-[#2c2c2c] text-gray-200 border border-gray-700 rounded-md p-2 focus:ring-[#ff5722] focus:border-[#ff5722]" required>
                    <option value="male" {{ old('gender') == 'male' ? 'selected' : '' }}>Male</option>
                    <option value="female" {{ old('gender') == 'female' ? 'selected' : '' }}>Female</option>
                    <option value="other" {{ old('gender') == 'other' ? 'selected' : '' }}>Other</option>
                </select>
            </div>
            <div>
                <label for="phone_number" class="block text-sm font-medium text-gray-200">Phone Number</label>
                <input type="text" name="phone_number" id="phone_number" value="{{ old('phone_number') }}" class="mt-1 block w-full bg-[#2c2c2c] text-gray-200 border border-gray-700 rounded-md p-2 focus:ring-[#ff5722] focus:border-[#ff5722]" required>
            </div>
            <div>
                <label for="email" class="block text-sm font-medium text-gray-200">Email</label>
                <input type="email" name="email" id="email" value="{{ old('email') }}" class="mt-1 block w-full bg-[#2c2c2c] text-gray-200 border border-gray-700 rounded-md p-2 focus:ring-[#ff5722] focus:border-[#ff5722]" required>
            </div>
            <div>
                <label for="rfid_uid" class="block text-sm font-medium text-gray-200">RFID UID</label>
                <input type="text" name="rfid_uid" id="rfid_uid" value="{{ old('rfid_uid') }}" class="mt-1 block w-full bg-[#2c2c2c] text-gray-200 border border-gray-700 rounded-md p-2 focus:ring-[#ff5722] focus:border-[#ff5722]" required>
            </div>
            <div>
                <label for="role" class="block text-sm font-medium text-gray-200">Role</label>
                <select name="role" id="role" class="mt-1 block w-full bg-[#2c2c2c] text-gray-200 border border-gray-700 rounded-md p-2 focus:ring-[#ff5722] focus:border-[#ff5722]" required>
                    <option value="admin" {{ old('role') == 'admin' ? 'selected' : '' }}>Admin</option>
                    <option value="super_admin" {{ old('role') == 'super_admin' ? 'selected' : '' }}>Super Admin</option>
                </select>
            </div>
            <div>
                <label for="session_status" class="block text-sm font-medium text-gray-200">Status</label>
                <select name="session_status" id="session_status" class="mt-1 block w-full bg-[#2c2c2c] text-gray-200 border border-gray-700 rounded-md p-2 focus:ring-[#ff5722] focus:border-[#ff5722]" required>
                    <option value="approved" {{ old('session_status') == 'approved' ? 'selected' : '' }}>Approved</option>
                    <option value="rejected" {{ old('session_status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
                </select>
            </div>
            <div>
                <label for="password" class="block text-sm font-medium text-gray-200">Password</label>
                <input type="password" name="password" id="password" class="mt-1 block w-full bg-[#2c2c2c] text-gray-200 border border-gray-700 rounded-md p-2 focus:ring-[#ff5722] focus:border-[#ff5722]" required>
            </div>
            <div>
                <label for="password_confirmation" class="block text-sm font-medium text-gray-200">Confirm Password</label>
                <input type="password" name="password_confirmation" id="password_confirmation" class="mt-1 block w-full bg-[#2c2c2c] text-gray-200 border border-gray-700 rounded-md p-2 focus:ring-[#ff5722] focus:border-[#ff5722]" required>
            </div>
        </div>
        <div class="mt-6 flex justify-end gap-3">
            <a href="{{ route('staff.manageStaffs') }}" class="bg-gray-100 text-gray-700 px-4 py-2 rounded-md hover:bg-gray-200 transition-colors">Cancel</a>
            <button type="submit" class="bg-[#ff5722] text-white px-4 py-2 rounded-md hover:bg-[#e64a19] transition-colors flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Create Staff
            </button>
        </div>
    </form>
</div>
@endsection