@extends('layouts.superadmin')

@section('title', 'Add New User')

@section('content')
<div class="max-w-2xl mx-auto space-y-6">

    <!-- Back Button -->
    <div>
        <a href="{{ route('superadmin.users.index') }}" 
           class="inline-flex items-center gap-2 px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold rounded-xl transition">
            <i class="fa-solid fa-arrow-left"></i>
            <span>Back to User List</span>
        </a>
    </div>

    <!-- Form Box -->
    <div class="bg-white p-6 rounded-2xl border border-slate-200/80 shadow-sm space-y-5">
        <div class="border-b border-slate-100 pb-4">
            <h1 class="text-lg font-bold text-slate-800">Register New User Account</h1>
            <p class="text-xs text-slate-400 mt-0.5">Fill in the user details and assign their access role.</p>
        </div>

        <form action="{{ route('superadmin.users.store') }}" method="POST" class="space-y-4">
            @csrf

            <!-- Full Name -->
            <div>
                <label class="block text-xs font-extrabold uppercase tracking-wider text-slate-400 mb-1.5">Full Name</label>
                <input type="text" name="full_name" required value="{{ old('full_name') }}" placeholder="e.g. Ahmad Faliq"
                       class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-medium text-slate-800 focus:ring-2 focus:ring-orange-500 outline-none transition">
            </div>

            <!-- Email Address -->
            <div>
                <label class="block text-xs font-extrabold uppercase tracking-wider text-slate-400 mb-1.5">Email Address</label>
                <input type="email" name="email" required value="{{ old('email') }}" placeholder="e.g. ahmad@nmuc.edu.my"
                       class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-medium text-slate-800 focus:ring-2 focus:ring-orange-500 outline-none transition">
            </div>

            <!-- Password -->
            <div>
                <label class="block text-xs font-extrabold uppercase tracking-wider text-slate-400 mb-1.5">Password</label>
                <input type="password" name="password" required placeholder="Minimum 6 characters"
                       class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-medium text-slate-800 focus:ring-2 focus:ring-orange-500 outline-none transition">
            </div>

            <!-- Role & Department Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <!-- Role -->
                <div>
                    <label class="block text-xs font-extrabold uppercase tracking-wider text-slate-400 mb-1.5">User Role</label>
                    <select name="role" required class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-bold text-slate-800 focus:ring-2 focus:ring-orange-500 outline-none transition">
                        @foreach($roles as $r)
                            <option value="{{ $r }}">{{ $r }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Department -->
                <div>
                    <label class="block text-xs font-extrabold uppercase tracking-wider text-slate-400 mb-1.5">Department</label>
                    <select name="department" required class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-bold text-slate-800 focus:ring-2 focus:ring-orange-500 outline-none transition">
                        @foreach($departments as $dept)
                            <option value="{{ $dept }}">{{ $dept }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- Submit Button -->
            <div class="pt-3">
                <button type="submit" class="w-full py-3 bg-orange-500 hover:bg-orange-600 text-white font-bold rounded-xl text-xs shadow-md transition">
                    Create User Account
                </button>
            </div>
        </form>
    </div>

</div>
@endsection