@extends('layouts.superadmin')

@section('title', 'User Management')

@section('content')
<div class="space-y-6">

    <!-- Flash Message Notification -->
    @if(session('success'))
        <div class="p-4 bg-emerald-100 border border-emerald-200 text-emerald-800 rounded-xl text-xs font-bold flex items-center gap-2">
            <i class="fa-solid fa-circle-check text-emerald-600"></i>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    <!-- Header Section & Add User Button -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center bg-white p-6 rounded-2xl border border-slate-200/80 shadow-sm gap-4">
        <div>
            <h1 class="text-xl font-bold text-slate-800">User Management</h1>
            <p class="text-xs text-slate-400 mt-1">Manage system access, roles, and departmental assignments for all users.</p>
        </div>
        <a href="{{ route('superadmin.users.create') }}" 
           class="px-4 py-2.5 bg-orange-500 hover:bg-orange-600 text-white rounded-xl text-xs font-bold transition shadow-md flex items-center gap-2">
            <i class="fa-solid fa-user-plus"></i>
            <span>Add New User</span>
        </a>
    </div>

    <!-- Filter & Search Bar -->
    <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-sm">
        <form action="{{ route('superadmin.users.index') }}" method="GET" class="flex flex-col md:flex-row gap-3">
            <div class="flex-1 relative">
                <i class="fa-solid fa-magnifying-glass absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by name, email, or department..." 
                       class="w-full pl-9 pr-4 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs font-medium text-slate-700 outline-none focus:ring-2 focus:ring-orange-500 transition">
            </div>

            <select name="role" class="px-4 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs font-bold text-slate-700 outline-none focus:ring-2 focus:ring-orange-500 transition">
                <option value="">All Roles</option>
                <option value="Staff" {{ request('role') == 'Staff' ? 'selected' : '' }}>Staff</option>
                <option value="Admin" {{ request('role') == 'Admin' ? 'selected' : '' }}>Admin</option>
                <option value="Super Admin" {{ request('role') == 'Super Admin' ? 'selected' : '' }}>Super Admin</option>
            </select>

            <button type="submit" class="px-5 py-2 bg-slate-800 hover:bg-slate-900 text-white rounded-xl text-xs font-bold transition">
                Filter
            </button>
        </form>
    </div>

    <!-- User List Table -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="p-6">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="border-b border-slate-200 bg-slate-50 text-slate-700 uppercase text-[11px] font-bold tracking-wider">
                            <th class="p-3">User</th>
                            <th class="p-3">Email</th>
                            <th class="p-3">Department</th>
                            <th class="p-3">Role</th>
                            <th class="p-3 text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200 text-sm">
                        @forelse ($users as $userItem)
                            <tr class="hover:bg-slate-50 transition">
                                <td class="p-3">
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 rounded-full bg-slate-700 text-white font-bold flex items-center justify-center text-xs shrink-0">
                                            {{ strtoupper(substr($userItem->full_name ?? $userItem->name ?? 'U', 0, 1)) }}
                                        </div>
                                        <span class="font-bold text-slate-800 text-xs">
                                            {{ $userItem->full_name ?? $userItem->name }}
                                        </span>
                                    </div>
                                </td>
                                <td class="p-3 text-xs text-slate-600 font-medium">{{ $userItem->email }}</td>
                                <td class="p-3 text-xs font-bold text-slate-700">
                                    <span class="px-2.5 py-1 bg-slate-100 border text-slate-700 rounded-md">
                                        {{ $userItem->department ?? 'General' }}
                                    </span>
                                </td>
                                <td class="p-3">
                                    <span class="px-2.5 py-1 text-xs font-bold rounded-full border
                                        @if($userItem->role == 'Super Admin') bg-purple-100 text-purple-700 border-purple-200
                                        @elseif($userItem->role == 'Admin') bg-blue-100 text-blue-700 border-blue-200
                                        @else bg-slate-100 text-slate-700 border-slate-200 @endif">
                                        {{ $userItem->role }}
                                    </span>
                                </td>
                                <td class="p-3 text-center">
                                    <form action="{{ route('superadmin.users.destroy', $userItem->user_id ?? $userItem->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this user?');" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="px-3 py-1.5 bg-red-50 hover:bg-red-500 hover:text-white text-red-600 rounded-lg text-xs font-bold transition border border-red-200">
                                            <i class="fa-solid fa-trash mr-1"></i> Delete
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="p-8 text-center text-slate-400 text-xs">
                                    <i class="fa-regular fa-id-badge text-3xl mb-2 text-slate-300 block"></i>
                                    No user accounts found.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="mt-4">
                {{ $users->links() }}
            </div>
        </div>
    </div>

</div>
@endsection