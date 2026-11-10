@extends('layouts.superadmin')

@section('title', 'Edit User Account')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">

    @if(session('success'))
        <div class="bg-emerald-50 border-l-4 border-emerald-500 p-4 rounded-xl shadow-sm text-emerald-800 flex items-start justify-between">
            <div class="flex items-center gap-3 text-sm font-medium">
                <i class="fa-solid fa-circle-check text-emerald-500 text-lg shrink-0"></i>
                <div>
                    <h4 class="font-bold text-emerald-900">Success!</h4>
                    <p class="text-xs text-emerald-700 mt-0.5">{{ session('success') }}</p>
                </div>
            </div>
            <button type="button" onclick="this.parentElement.remove()" class="text-emerald-500 hover:text-emerald-700 text-xs p-1">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>
    @endif

    <!-- Flash Error Notification -->
    @if ($errors->any())
        <div class="p-4 bg-red-100 border border-red-200 text-red-800 rounded-2xl text-xs font-semibold space-y-1">
            <div class="flex items-center gap-2 font-bold text-red-900">
                <i class="fa-solid fa-triangle-exclamation"></i>
                <span>Please fix the following errors:</span>
            </div>
            <ul class="list-disc pl-5 space-y-0.5">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Header & Back Button -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center bg-white p-6 rounded-3xl border border-slate-200/80 shadow-sm gap-4">
        <div>
            <div class="flex items-center gap-2">
                <a href="{{ route('superadmin.users.index') }}" class="text-slate-400 hover:text-slate-600 transition text-xs font-bold flex items-center gap-1">
                    <i class="fa-solid fa-arrow-left"></i> Users
                </a>
                <span class="text-slate-300">/</span>
                <span class="text-xs font-bold text-orange-600 uppercase tracking-wider">Edit User</span>
            </div>
            <h1 class="text-xl font-bold text-slate-800 mt-1">Edit User Account: {{ $user->full_name ?? $user->name }}</h1>
        </div>
    </div>

    <!-- Edit User Form Card -->
    <div class="bg-white rounded-3xl p-6 md:p-8 border border-slate-200/80 shadow-sm">
        @php
            $userId = $user->user_id ?? $user->id;
        @endphp

        <form action="{{ route('superadmin.users.update', $userId) }}" method="POST" class="space-y-6">
            @csrf
            @method('PUT')

            <!-- SECTION 1: Personal Information -->
            <div class="space-y-4">
                <h3 class="text-xs font-extrabold uppercase tracking-wider text-slate-400 border-b border-slate-100 pb-2 flex items-center gap-2">
                    <i class="fa-solid fa-user text-orange-500"></i> Personal Information
                </h3>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                    <!-- Full Name -->
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1.5">Full Name <span class="text-red-500">*</span></label>
                        <input type="text" name="full_name" value="{{ old('full_name', $user->full_name ?? $user->name) }}" required
                               placeholder="Enter user full name"
                               class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-800 font-medium focus:ring-2 focus:ring-orange-500 focus:bg-white outline-none transition">
                    </div>

                    <!-- Email Address -->
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1.5">Email Address <span class="text-red-500">*</span></label>
                        <input type="email" name="email" value="{{ old('email', $user->email) }}" required
                               placeholder="user@nmuc.edu.my"
                               class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-800 font-medium focus:ring-2 focus:ring-orange-500 focus:bg-white outline-none transition">
                    </div>

                    <!-- Input ID -->
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1.5">User ID</label>
                        <input type="text" name="nmuc_id" value="{{ old('nmuc_id', $user->nmuc_id ?? $user->user_id) }}" 
                            class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-800 font-medium focus:ring-2 focus:ring-orange-500 focus:bg-white outline-none transition">
                    </div>
                </div>
            </div>

            <!-- SECTION 2: Role, Department & Status -->
            <div class="space-y-4 pt-2">

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <!-- Department -->
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1.5">Department</label>
                        <select name="department_id" 
                                class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-700 font-medium focus:ring-2 focus:ring-orange-500 outline-none transition cursor-pointer">
                            <option value="">-- No Department --</option>
                            @foreach($departments as $dept)
                                @php
                                    $deptId = $dept->department_id ?? $dept->id;
                                    $deptName = $dept->department_name ?? $dept->name;
                                    $selectedDept = old('department_id', $user->department_id) == $deptId ? 'selected' : '';
                                @endphp
                                <option value="{{ $deptId }}" {{ $selectedDept }}>{{ $deptName }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Role -->
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1.5">User Role <span class="text-red-500">*</span></label>
                        <select name="role" required
                                class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-700 font-medium focus:ring-2 focus:ring-orange-500 outline-none transition cursor-pointer">
                            <option value="Authentication User" {{ request('role') == 'Authentication User' ? 'selected' : '' }}>User</option>
                            <option value="Admin" {{ old('role', $user->role) == 'Admin' ? 'selected' : '' }}>Admin</option>
                            <option value="Super Admin" {{ old('role', $user->role) == 'Super Admin' ? 'selected' : '' }}>Super Admin</option>
                        </select>
                    </div>

                    <!-- Account Status -->
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1.5">Account Status <span class="text-red-500">*</span></label>
                        <select name="user_status" required
                                class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-700 font-medium focus:ring-2 focus:ring-orange-500 outline-none transition cursor-pointer">
                            <option value="Active" {{ old('user_status', $user->user_status ?? 'Active') == 'Active' ? 'selected' : '' }}>Active</option>
                            <option value="Inactive" {{ old('user_status', $user->user_status) == 'Inactive' ? 'selected' : '' }}>Inactive</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- SECTION 3: Change Password (Optional) -->
            <div class="space-y-4 pt-2">
                <h3 class="text-xs font-extrabold uppercase tracking-wider text-slate-400 border-b border-slate-100 pb-2 flex items-center gap-2">
                    <i class="fa-solid fa-key text-orange-500"></i> Password Settings
                </h3>

                <div class="p-4 bg-amber-50/60 rounded-2xl border border-amber-200/60 text-xs text-amber-800 flex items-start gap-2.5">
                    <i class="fa-solid fa-circle-info text-amber-500 mt-0.5 text-sm shrink-0"></i>
                    <span>Leave the password fields empty if you do not want to change the user's current password.</span>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- New Password -->
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1.5">New Password</label>
                        <input type="password" name="password" minlength="6" placeholder="Enter new password (optional)"
                               class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-800 font-medium focus:ring-2 focus:ring-orange-500 focus:bg-white outline-none transition">
                    </div>

                    <!-- Confirm Password -->
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1.5">Confirm New Password</label>
                        <input type="password" name="password_confirmation" minlength="6" placeholder="Confirm new password"
                               class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-800 font-medium focus:ring-2 focus:ring-orange-500 focus:bg-white outline-none transition">
                    </div>
                </div>
            </div>

            <!-- Form Actions -->
            <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100">
                <a href="{{ route('superadmin.users.index') }}" 
                   class="px-5 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs rounded-xl transition">
                    Cancel
                </a>
                <button type="submit" 
                        class="px-6 py-2.5 bg-orange-500 hover:bg-orange-600 text-white font-bold text-xs rounded-xl shadow-md transition flex items-center gap-2 cursor-pointer">
                    <i class="fa-solid fa-floppy-disk"></i>
                    <span>Save Changes</span>
                </button>
            </div>

        </form>
    </div>

</div>
@endsection