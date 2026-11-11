@extends('layouts.superadmin')

@section('title', 'Add New User')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">

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

    <!-- Main Single Form Card -->
    <div class="bg-white p-6 md:p-8 rounded-3xl border border-slate-200/80 shadow-sm space-y-6">
        
        <!-- Form Header with Integrated Back Button -->
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center pb-5 border-b border-slate-100 gap-4">
            <div>
                <h1 class="text-xl font-bold text-slate-800">Register New User Account</h1>
                <p class="text-xs text-slate-400 mt-1">Fill in the user details and assign their access role.</p>
            </div>
            <a href="{{ route('superadmin.users.index') }}" 
               class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold rounded-xl transition flex items-center gap-2 shrink-0">
                <i class="fa-solid fa-arrow-left text-slate-500"></i>
                <span>Back to User List</span>
            </a>
        </div>

        <form action="{{ route('superadmin.users.store') }}" method="POST" class="space-y-6">
            @csrf

            <!-- SECTION 1: Account Information -->
            <div class="space-y-4">
                <h3 class="text-xs font-extrabold uppercase tracking-wider text-slate-400 border-b border-slate-100 pb-2 flex items-center gap-2">
                    Basic Information
                </h3>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <!-- Full Name -->
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1.5">Full Name <span class="text-red-500">*</span></label>
                        <input type="text" name="full_name" value="{{ old('full_name') }}"
                               class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-medium text-slate-800 focus:ring-2 focus:ring-orange-500 focus:bg-white outline-none transition">
                    </div>

                    <!-- Email Address -->
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1.5">Email Address <span class="text-red-500">*</span></label>
                        <input type="email" name="email" value="{{ old('email') }}"
                               class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-medium text-slate-800 focus:ring-2 focus:ring-orange-500 focus:bg-white outline-none transition">
                    </div>

                    <!-- User ID -->
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1.5">ID <span class="text-red-500">*</span></label>
                        <input type="text" name="nmuc_id" value="{{ old('nmuc_id') }}"
                               class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-medium text-slate-800 focus:ring-2 focus:ring-orange-500 focus:bg-white outline-none transition">
                    </div>
                    
                </div>
            </div>

            <!-- SECTION 2: Role & Department Assignment -->
            <div class="space-y-4 pt-2">

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- User Role -->
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1.5">Role <span class="text-red-500">*</span></label>
                        <select name="role" required class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-bold text-slate-700 focus:ring-2 focus:ring-orange-500 outline-none transition cursor-pointer">
                            <option value="">-- Select Role --</option>
                            @foreach($roles as $r)
                                <option value="{{ $r }}" {{ old('role') == $r ? 'selected' : '' }}>{{ $r }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Department -->
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1.5">Department</label>
                        <select name="department_id" id="department_id" class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-bold text-slate-700 focus:ring-2 focus:ring-orange-500 outline-none transition cursor-pointer">
                            <option value="">-- Select Department --</option>
                            @foreach($departments as $dept)
                                @php
                                    $deptId = $dept->department_id ?? $dept->id;
                                    $deptName = $dept->department_name ?? $dept->name;
                                @endphp
                                <option value="{{ $deptId }}" {{ old('department_id') == $deptId ? 'selected' : '' }}>{{ $deptName }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            <!-- SECTION 3: Security -->
            <div class="space-y-4 pt-2">
                <h3 class="text-xs font-extrabold uppercase tracking-wider text-slate-400 border-b border-slate-100 pb-2 flex items-center gap-2">
                    Security Credentials
                </h3>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1.5">Password <span class="text-red-500">*</span></label>
                    <input type="password" name="password" required minlength="6" placeholder="Minimum 6 characters"
                           class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-medium text-slate-800 focus:ring-2 focus:ring-orange-500 focus:bg-white outline-none transition">
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
                    <i class="fa-solid fa-user-plus"></i>
                    <span>Create User Account</span>
                </button>
            </div>

        </form>
    </div>

</div>
@endsection