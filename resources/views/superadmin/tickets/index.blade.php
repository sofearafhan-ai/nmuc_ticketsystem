@extends('layouts.superadmin')

@section('title', 'Tickets')

@section('content')
<div class="space-y-6">

    <!-- Header Section -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center bg-[#0d1527] p-6 rounded-2xl text-white shadow-md gap-4">
        <div>
            <div class="flex items-center gap-2">
                <h1 class="text-2xl font-bold">Manage Support Tickets</h1>
            </div>
            <p class="text-xs text-slate-400 mt-1">Review, filter, and manage support tickets.</p>
        </div>
    </div>

    @if (session('success'))
        <div class="bg-emerald-900/90 border-l-4 border-emerald-400 text-emerald-100 px-4 py-3 rounded-xl shadow-sm text-sm flex items-center gap-2">
            <i class="fa-solid fa-circle-check text-emerald-300"></i>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    <!-- FILTER CARD SECTION -->
    <div class="bg-white rounded-2xl p-5 shadow-sm border border-slate-200/80">
        <form action="{{ route('superadmin.tickets.index') }}" method="GET" class="space-y-4">
            
            <!-- Top Search Input & Filter / Reset Buttons -->
            <div class="flex flex-col sm:flex-row items-center gap-3">
                <div class="relative flex-1 w-full">
                    <i class="fa-solid fa-magnifying-glass absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 text-sm"></i>
                    <input type="text" name="search" value="{{ request('search') }}" 
                           placeholder="Search..." 
                           class="w-full pl-11 pr-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-800 focus:ring-2 focus:ring-orange-500 focus:bg-white outline-none transition">
                </div>

                <div class="flex items-center gap-2 w-full sm:w-auto">
                    <button type="submit" 
                            class="flex-1 sm:flex-initial px-6 py-2.5 bg-orange-500 hover:bg-orange-600 text-white font-bold text-xs rounded-xl shadow-md transition flex items-center justify-center gap-2 cursor-pointer">
                        <i class="fa-solid fa-filter"></i> Filter
                    </button>

                     @if(request()->hasAny(['search', 'category', 'type', 'priority', 'status', 'department_id', 'month', 'year']))
                        <a href="{{ route('superadmin.tickets.index') }}" 
                           class="px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-600 font-bold text-xs rounded-xl transition flex items-center gap-1.5"
                           title="Reset Filters">
                            <i class="fa-solid fa-rotate-right"></i> Reset
                        </a>
                    @endif
                </div>
            </div>

            <hr class="border-slate-100">

            <!-- Bottom 6 Dropdown Filters Grid -->
            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-3 pt-2">
                
                <!-- 1. CATEGORY -->
                <div>
                    <label class="block text-[11px] font-extrabold uppercase tracking-wider text-slate-500 mb-1.5">Category</label>
                    <select name="category" onchange="this.form.submit()" 
                            class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-700 font-medium focus:ring-2 focus:ring-orange-500 outline-none transition cursor-pointer">
                        <option value="">All Categories</option>
                        @foreach(($categories ?? ['Hardware', 'Software', 'Network', 'Facility', 'Others']) as $cat)
                            <option value="{{ $cat }}" {{ request('category') == $cat ? 'selected' : '' }}>{{ $cat }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- 2. TYPE -->
                <div>
                    <label class="block text-[11px] font-extrabold uppercase tracking-wider text-slate-500 mb-1.5">Type</label>
                    <select name="type" onchange="this.form.submit()" 
                            class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-700 font-medium focus:ring-2 focus:ring-orange-500 outline-none transition cursor-pointer">
                        <option value="">All Types</option>
                        @foreach(($types ?? ['Incident', 'Request']) as $tp)
                            <option value="{{ $tp }}" {{ request('type') == $tp ? 'selected' : '' }}>{{ $tp }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- 3. PRIORITY -->
                <div>
                    <label class="block text-[11px] font-extrabold uppercase tracking-wider text-slate-500 mb-1.5">Priority</label>
                    <select name="priority" onchange="this.form.submit()" 
                            class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-700 font-medium focus:ring-2 focus:ring-orange-500 outline-none transition cursor-pointer">
                        <option value="">All Priorities</option>
                        @foreach(($priorities ?? ['Low', 'Medium', 'High', 'Urgent']) as $prio)
                            <option value="{{ $prio }}" {{ request('priority') == $prio ? 'selected' : '' }}>{{ $prio }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- 4. STATUS -->
                <div>
                    <label class="block text-[11px] font-extrabold uppercase tracking-wider text-slate-500 mb-1.5">Status</label>
                    <select name="status" onchange="this.form.submit()" 
                            class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-700 font-medium focus:ring-2 focus:ring-orange-500 outline-none transition cursor-pointer">
                        <option value="">All Statuses</option>
                        <option value="New" {{ request('status') == 'New' ? 'selected' : '' }}>New</option>
                        <option value="Processing" {{ request('status') == 'Processing' ? 'selected' : '' }}>Processing</option>
                        <option value="Pending" {{ request('status') == 'Pending' ? 'selected' : '' }}>Pending</option>
                        <option value="Close" {{ request('status') == 'Close' || request('status') == 'Closed' ? 'selected' : '' }}>Closed</option>
                    </select>
                </div>

                <!-- 5. DEPARTMENT -->
                <div>
                    <label class="block text-[11px] font-extrabold uppercase tracking-wider text-slate-500 mb-1.5">Department</label>
                    <select name="department_id" onchange="this.form.submit()" 
                            class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-700 font-medium focus:ring-2 focus:ring-orange-500 outline-none transition cursor-pointer">
                        <option value="">All Departments</option>
                        @if(isset($departments))
                            @foreach($departments as $dept)
                                @php $deptVal = $dept->department_id ?? $dept->id; @endphp
                                <option value="{{ $deptVal }}" {{ request('department_id') == $deptVal ? 'selected' : '' }}>
                                    {{ $dept->name ?? $dept->department_name }}
                                </option>
                            @endforeach
                        @endif
                    </select>
                </div>

                <!-- 6. MONTH -->
                <div>
                    <label class="block text-[11px] font-extrabold uppercase tracking-wider text-slate-500 mb-1.5">Month</label>
                    <select name="month" onchange="this.form.submit()" 
                            class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-700 font-medium focus:ring-2 focus:ring-orange-500 outline-none transition cursor-pointer">
                        <option value="">All Months</option>
                        @foreach([1=>'January', 2=>'February', 3=>'March', 4=>'April', 5=>'May', 6=>'June', 7=>'July', 8=>'August', 9=>'September', 10=>'October', 11=>'November', 12=>'December'] as $num => $monthName)
                            <option value="{{ $num }}" {{ request('month') == $num ? 'selected' : '' }}>{{ $monthName }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- 7. YEAR -->
                <div>
                    <label class="block text-[11px] font-extrabold uppercase tracking-wider text-slate-500 mb-1.5">Year</label>
                    <select name="year" onchange="this.form.submit()" 
                            class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-700 font-medium focus:ring-2 focus:ring-orange-500 outline-none transition cursor-pointer">
                        <option value="">All Years</option>
                        @foreach (range(date('Y'), date('Y') - 4) as $year)
                            <option value="{{ $year }}" {{ request('year') == $year ? 'selected' : '' }}>
                                {{ $year }}
                            </option>
                        @endforeach
                    </select>
                </div>

            </div>

        </form>
    </div>

    <!-- Tickets Table Card -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="p-6">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="border-b border-slate-200 bg-slate-50 text-slate-700 uppercase text-[11px] font-bold tracking-wider">
                            <th class="p-2 text-center">Ticket ID</th>
                            <th class="p-2 text-center">Title</th>
                            <th class="p-2 text-center">Submitted By</th>
                            <th class="p-2 text-center">Category</th>
                            <th class="p-2 text-center">Priority</th>
                            <th class="p-2 text-center">Status</th>
                            <th class="p-2 text-center">PiC</th>
                            <th class="p-2 text-center">Department</th>
                            <th class="p-2 text-center">Date</th>
                            <th class="p-2 text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200 text-sm">
                        @forelse ($tickets as $index => $ticket)
                            @php
                                $ticketId = $ticket->ticket_id ?? $ticket->id;
                                $formattedTicketNo = '#TC-' . $ticketId;
                            @endphp
                            <tr class="hover:bg-orange-50/40 transition">
                                <td class="p-2 text-xs font-bold text-slate-900 text-center">{{ $formattedTicketNo }}</td>
                                <td class="p-2 text-xs font-medium text-slate-900">
                                    <a href="{{ route('superadmin.messages.index', ['ticket_id' => $ticketId]) }}" class="hover:text-orange-600 hover:underline">
                                        {{ $ticket->title }}
                                    </a>
                                </td>
                                <td class="p-2 text-xs text-slate-600 font-medium text-center">{{ $ticket->user_name ?? ($ticket->user->name ?? 'User') }}</td>
                                <td class="p-2 text-xs text-slate-600 font-medium text-center">{{ $ticket->category }}</td>
                                <td class="p-2 text-center">
                                    <span class="px-2.5 py-1 rounded-full font-bold text-[10px]
                                        @if($ticket->priority == 'Urgent') bg-red-100 text-red-800
                                        @elseif($ticket->priority == 'High') bg-orange-200 text-orange-900
                                        @elseif($ticket->priority == 'Medium') bg-yellow-100 text-yellow-800
                                        @else bg-slate-100 text-slate-800 @endif">
                                        {{ $ticket->priority }}
                                    </span>
                                </td>
                                <td class="p-2 text-center">
                                    <span class="px-2.5 py-1 rounded-full font-bold text-[10px]
                                        @if($ticket->status == 'New') bg-emerald-100 text-emerald-700
                                        @elseif($ticket->status == 'Processing') bg-blue-100 text-blue-700
                                        @elseif($ticket->status == 'Pending') bg-amber-100 text-amber-700
                                        @else bg-red-100 text-red-700 @endif">
                                        {{ $ticket->status }}
                                    </span>
                                </td>
                                <td class="p-2 text-xs text-slate-500 font-medium text-center">
                                    {{ $ticket->picAdmin->full_name ?? $ticket->picAdmin->name ?? $ticket->pic ?? '-' }}
                                </td>
                                <td class="p-2 text-xs text-slate-500 font-medium text-center">{{ $ticket->department->department_name ?? $ticket->department ?? '-' }}</td>
                                <td class="p-2 text-xs text-slate-500 font-medium text-center">
                                    @if(!empty($ticket->created_at))
                                        {{ is_string($ticket->created_at) ? \Carbon\Carbon::parse($ticket->created_at)->format('d M Y') : $ticket->created_at->format('d M Y') }}
                                    @else
                                        {{ $ticket->date ? \Carbon\Carbon::parse($ticket->date)->format('d M Y') : '-' }}
                                    @endif
                                </td>
                                <td class="p-2 text-center">
                                    <div class="flex items-center justify-center gap-2">
                                        <a href="{{ route('superadmin.tickets.edit', $ticketId) }}" 
                                           class="px-2.5 py-1.5 bg-slate-100 hover:bg-orange-500 text-slate-600 hover:text-white rounded-lg text-xs font-semibold transition border border-slate-200 hover:border-orange-500 flex items-center gap-1 cursor-pointer">
                                            <i class="fa-solid fa-pen-to-square"></i>
                                        </a>

                                        <form action="{{ route('superadmin.tickets.destroy', $ticketId) }}" method="POST" 
                                              onsubmit="return confirm('Are you sure you want to delete this ticket?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" 
                                                    class="px-2.5 py-1.5 bg-red-50 hover:bg-red-600 text-red-600 hover:text-white rounded-lg text-xs font-semibold transition border border-red-200 hover:border-red-600 flex items-center gap-1 cursor-pointer">
                                                <i class="fa-solid fa-trash-can"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="10" class="p-8 text-center text-slate-500 font-medium">
                                    <i class="fa-solid fa-inbox text-2xl mb-2 text-slate-300 block"></i>
                                    No ticket complaints found matching your search and filter criteria.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if (method_exists($tickets, 'hasPages') && $tickets->hasPages())
                <div class="mt-6">
                    {{ $tickets->links() }}
                </div>
            @endif
        </div>
    </div>

</div>
@endsection