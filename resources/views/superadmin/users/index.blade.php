@extends('layouts.superadmin')

@section('title', 'User Management')

@section('content')
<div class="space-y-6">

    <!-- Flash Message Notification -->
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

    <!-- Header Section & Add User Button -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center bg-[#0d1527] p-6 rounded-3xl text-white shadow-lg gap-4">
        <div>
            <h1 class="text-xl font-bold text-white">User Management</h1>
            <p class="text-xs text-slate-400 mt-1">Manage system access, roles, and departmental assignments for all users.</p>
        </div>
        <a href="{{ route('superadmin.users.create') }}" 
           class="px-4 py-2.5 bg-orange-500 hover:bg-orange-600 text-white rounded-xl text-xs font-bold transition shadow-md flex items-center gap-2">
            <i class="fa-solid fa-user-plus"></i>
            <span>Add New User</span>
        </a>
    </div>

    <!-- MAIN SINGLE CARD CONTAINER -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200/80 overflow-hidden divide-y divide-slate-100">
        
        <!-- 1. Top Section: Filter & Search Bar -->
        <div class="p-5 bg-white">
            <form action="{{ route('superadmin.users.index') }}" method="GET" class="space-y-4">
                
                <!-- Search & Action Buttons Row -->
                <div class="flex flex-col sm:flex-row items-center gap-3">
                    <div class="relative flex-1 w-full">
                        <i class="fa-solid fa-magnifying-glass absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Search..." 
                               class="w-full pl-11 pr-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-800 outline-none focus:ring-2 focus:ring-orange-500 transition focus:bg-white">
                    </div>

                    <div class="flex items-center gap-2 w-full sm:w-auto">
                        <button type="submit"
                                class="flex-1 sm:flex-initial px-6 py-2.5 bg-orange-500 hover:bg-orange-600 text-white font-bold text-xs rounded-xl shadow-md transition flex items-center justify-center gap-2 cursor-pointer">
                            <i class="fa-solid fa-filter"></i> Filter
                        </button>

                        <a href="{{ route('superadmin.users.export', request()->all()) }}"
                           class="px-4 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs rounded-xl shadow-md transition flex items-center justify-center gap-2 cursor-pointer">
                            <i class="fa-solid fa-file-excel"></i>
                            <span>Export Excel</span>
                        </a>

                        @if(request()->hasAny(['search', 'department_id', 'role', 'status']))
                            <a href="{{ route('superadmin.users.index') }}" 
                               class="px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-600 rounded-xl text-xs font-bold transition flex items-center justify-center gap-1.5"
                               title="Reset Filters">
                                <i class="fa-solid fa-rotate-right text-[11px]"></i>
                                <span>Reset</span>
                            </a>
                        @endif
                    </div>
                </div>

                <!-- Dropdown Filters Row -->
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 pt-1">
                    <select name="department_id" class="px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-bold text-slate-700 outline-none focus:ring-2 focus:ring-orange-500 transition cursor-pointer">
                        <option value="">All Departments</option>
                        @foreach($departments ?? [] as $dept)
                            @php
                                $deptId = $dept->department_id ?? $dept->id;
                                $deptName = $dept->department_name ?? $dept->name;
                            @endphp
                            <option value="{{ $deptId }}" {{ request('department_id') == $deptId ? 'selected' : '' }}>
                                {{ $deptName }}
                            </option>
                        @endforeach
                    </select>

                    <select name="role" class="px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-bold text-slate-700 outline-none focus:ring-2 focus:ring-orange-500 transition cursor-pointer">
                        <option value="">All Roles</option>
                        <option value="Authentication User" {{ request('role') == 'Authentication User' ? 'selected' : '' }}>User</option>
                        <option value="Admin" {{ request('role') == 'Admin' ? 'selected' : '' }}>Admin</option>
                        <option value="Super Admin" {{ request('role') == 'Super Admin' ? 'selected' : '' }}>Super Admin</option>
                    </select>

                    <select name="status" class="px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-bold text-slate-700 outline-none focus:ring-2 focus:ring-orange-500 transition cursor-pointer">
                        <option value="">All Statuses</option>
                        <option value="Active" {{ request('status') == 'Active' ? 'selected' : '' }}>Active</option>
                        <option value="Inactive" {{ request('status') == 'Inactive' ? 'selected' : '' }}>Inactive</option>
                    </select>
                </div>

            </form>
        </div>

        <!-- 2. User List Table -->
        <div class="p-6">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="border-b border-slate-200 bg-slate-50 text-slate-700 uppercase text-[11px] font-bold tracking-wider">
                            <th class="p-3 text-center">No</th>
                            <th class="p-3 text-center">Name</th>
                            <th class="p-3 text-center">ID</th>
                            <th class="p-3 text-center">Email</th>
                            <th class="p-3 text-center">Department</th>
                            <th class="p-3 text-center">Role</th>
                            <th class="p-3 text-center">Status</th>
                            <th class="p-3 text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200 text-sm">
                        @forelse ($users as $index => $user)
                            @php
                                $userId = $user->user_id ?? $user->id;
                            @endphp
                            <tr class="hover:bg-slate-50 transition">
                                <!-- 1. No -->
                                <td class="p-3 text-center font-bold text-xs text-slate-500">
                                    {{ method_exists($users, 'firstItem') ? ($users->firstItem() + $index) : ($index + 1) }}
                                </td>

                                <!-- 2. Name -->
                                <td class="p-3">
                                    <span class="font-bold text-slate-800 text-xs block">
                                        {{ $user->full_name ?? $user->name }}
                                    </span>
                                </td>

                                <!-- 3. User ID -->
                                <td class="p-3 text-center text-xs font-bold text-slate-500">
                                    {{ $user->nmuc_id }}
                                </td>

                                <!-- 4. Email -->
                                <td class="p-3 text-xs text-slate-600 font-medium">
                                    {{ $user->email }}
                                </td>

                                <!-- 5. Department -->
                                <td class="p-3 text-xs font-bold text-slate-700 text-center">
                                    {{ $user->department->department_name ?? '-' }}
                                </td>

                                <!-- 6. Role -->
                                <td class="p-3 text-center">
                                    <span class="px-2.5 py-1 text-xs font-bold rounded-md border inline-block
                                        @if($user->role == 'Super Admin') bg-purple-100 text-purple-700 border-purple-200
                                        @elseif($user->role == 'Admin') bg-blue-100 text-blue-700 border-blue-200
                                        @else bg-slate-100 text-slate-700 border-slate-200 @endif">
                                        {{ $user->role }}
                                    </span>
                                </td>

                                <!-- 7. Status Badge -->
                                <td class="p-2 text-xs text-slate-500 font-medium text-center">
                                    <span class="px-2.5 py-1 text-[10px] font-bold rounded-full inline-block uppercase tracking-wider border
                                        {{ $user->user_status == 'Active' ? 'bg-emerald-50 text-emerald-700 border-emerald-200' : 'bg-slate-100 text-slate-600 border-slate-200' }}">
                                        {{ $user->user_status }}
                                    </span>
                                </td>

                                <!-- 8. Action -->
                                <td class="p-3 text-center">
                                    <div class="flex items-center justify-center gap-2">
                                        <a href="{{ route('superadmin.users.edit', $userId) }}" 
                                           title="Edit User"
                                           class="px-2.5 py-1.5 bg-slate-100 hover:bg-orange-500 text-slate-600 hover:text-white rounded-lg text-xs font-semibold transition border border-slate-200 hover:border-orange-500 flex items-center gap-1 cursor-pointer">
                                            <i class="fa-solid fa-pen-to-square"></i>
                                        </a>

                                        <form action="{{ route('superadmin.users.destroy', $userId) }}" 
                                              method="POST" 
                                              onsubmit="return confirm('Are you sure you want to delete this user?');" 
                                              class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" 
                                                    title="Delete User"
                                                    class="px-2.5 py-1.5 bg-red-50 hover:bg-red-500 text-red-600 hover:text-white rounded-lg text-xs font-bold transition border border-red-200 cursor-pointer flex items-center gap-1">
                                                <i class="fa-solid fa-trash-can"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="p-8 text-center text-slate-400 text-xs">
                                    <i class="fa-regular fa-id-badge text-3xl mb-2 text-slate-300 block"></i>
                                    No user accounts found.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if(method_exists($users, 'hasPages') && $users->hasPages())
                <div class="mt-4">
                    {{ $users->links() }}
                </div>
            @endif
        </div>
    </div>

</div>
@endsection