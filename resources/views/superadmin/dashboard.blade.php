@extends('layouts.superadmin')

@section('title', 'Super Admin Dashboard')

@section('content')
<div class="space-y-6">

    <!-- Header Section -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center bg-[#0d1527] p-6 rounded-2xl text-white shadow-md gap-4">
        <div>
            <div class="flex items-center gap-2">
                <span class="bg-purple-600 text-white text-[10px] font-extrabold px-3 py-1 rounded-full uppercase tracking-wider shadow-sm flex items-center gap-1">
                    <i class="fa-solid fa-crown text-[9px]"></i> OVERALL SYSTEM OVERVIEW
                </span>
                <h1 class="text-2xl font-bold">Super Admin Dashboard</h1>
            </div>
            <p class="text-xs text-slate-400 mt-1">Cross-departmental management & global system performance statistics.</p>
        </div>
    </div>

    <!-- TOP 4 CARDS: GLOBAL QUICK STATS -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        
        <!-- 1. Total Tickets -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm flex items-center justify-between">
            <div class="space-y-1">
                <span class="text-xs font-bold uppercase text-slate-400 tracking-wider">Total Tickets</span>
                <h3 class="text-2xl font-extrabold text-slate-800">{{ $ticketStats['total'] }}</h3>
                <p class="text-[11px] text-emerald-600 font-semibold flex items-center gap-1">
                    <i class="fa-solid fa-layer-group"></i> All Departments
                </p>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-orange-100 text-orange-500 flex items-center justify-center text-xl shadow-inner">
                <i class="fa-solid fa-ticket"></i>
            </div>
        </div>

        <!-- 2. Total Registered Users -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm flex items-center justify-between">
            <div class="space-y-1">
                <span class="text-xs font-bold uppercase text-slate-400 tracking-wider">Total Users</span>
                <h3 class="text-2xl font-extrabold text-slate-800">{{ $userStats['total'] }}</h3>
                <p class="text-[11px] text-purple-600 font-semibold flex items-center gap-1">
                    <i class="fa-solid fa-user-shield"></i> {{ $userStats['admins'] }} Admins / {{ $userStats['staff'] }} Staff
                </p>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-purple-100 text-purple-600 flex items-center justify-center text-xl shadow-inner">
                <i class="fa-solid fa-users"></i>
            </div>
        </div>

        <!-- 3. Global Assets / Inventory -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm flex items-center justify-between">
            <div class="space-y-1">
                <span class="text-xs font-bold uppercase text-slate-400 tracking-wider">Total Assets</span>
                <h3 class="text-2xl font-extrabold text-slate-800">{{ $inventoryStats['total'] }}</h3>
                <p class="text-[11px] text-blue-600 font-semibold flex items-center gap-1">
                    <i class="fa-solid fa-box"></i> {{ $inventoryStats['active'] }} Active Items
                </p>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-blue-100 text-blue-500 flex items-center justify-center text-xl shadow-inner">
                <i class="fa-solid fa-boxes-stacked"></i>
            </div>
        </div>

        <!-- 4. Active Departments -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm flex items-center justify-between">
            <div class="space-y-1">
                <span class="text-xs font-bold uppercase text-slate-400 tracking-wider">Departments</span>
                <h3 class="text-2xl font-extrabold text-slate-800">{{ $userStats['departments'] }}</h3>
                <p class="text-[11px] text-amber-600 font-semibold flex items-center gap-1">
                    <i class="fa-solid fa-sitemap"></i> System Managed
                </p>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-amber-100 text-amber-500 flex items-center justify-center text-xl shadow-inner">
                <i class="fa-solid fa-building"></i>
            </div>
        </div>

    </div>

    <!-- TICKET STATUS BREAKDOWN -->
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
        <div class="bg-emerald-50 border border-emerald-200 p-4 rounded-2xl text-center">
            <span class="text-xs font-bold text-emerald-700 uppercase">New Tickets</span>
            <div class="text-xl font-extrabold text-emerald-900 mt-1">{{ $ticketStats['new'] }}</div>
        </div>
        <div class="bg-blue-50 border border-blue-200 p-4 rounded-2xl text-center">
            <span class="text-xs font-bold text-blue-700 uppercase">Processing</span>
            <div class="text-xl font-extrabold text-blue-900 mt-1">{{ $ticketStats['processing'] }}</div>
        </div>
        <div class="bg-amber-50 border border-amber-200 p-4 rounded-2xl text-center">
            <span class="text-xs font-bold text-amber-700 uppercase">Pending</span>
            <div class="text-xl font-extrabold text-amber-900 mt-1">{{ $ticketStats['pending'] }}</div>
        </div>
        <div class="bg-red-50 border border-red-200 p-4 rounded-2xl text-center">
            <span class="text-xs font-bold text-red-700 uppercase">Closed Tickets</span>
            <div class="text-xl font-extrabold text-red-900 mt-1">{{ $ticketStats['close'] }}</div>
        </div>
    </div>

    <!-- RECENT TICKETS ACROSS ALL DEPARTMENTS TABLE -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="p-5 border-b border-slate-100 flex items-center justify-between">
            <div class="flex items-center gap-2">
                <i class="fa-solid fa-clock-rotate-left text-orange-500"></i>
                <h3 class="font-bold text-slate-800 text-sm">Recent Cross-Department Support Tickets</h3>
            </div>
            <a href="{{ route('admin.tickets.index') }}" class="text-xs font-bold text-orange-600 hover:underline flex items-center gap-1">
                <span>View All Tickets</span>
                <i class="fa-solid fa-arrow-right text-[10px]"></i>
            </a>
        </div>

        <div class="p-6">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="border-b border-slate-200 bg-slate-50 text-slate-700 uppercase text-[11px] font-bold tracking-wider">
                            <th class="p-3">Ticket ID</th>
                            <th class="p-3">Title</th>
                            <th class="p-3">Submitted By</th>
                            <th class="p-3">Category</th>
                            <th class="p-3">Status</th>
                            <th class="p-3">Assigned PIC</th>
                            <th class="p-3 text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200 text-sm">
                        @forelse ($recentTickets as $ticket)
                            @php
                                $formattedTicketNo = '#TC-' . str_pad($ticket->ticket_id ?? $ticket->id, 4, '0', STR_PAD_LEFT);
                            @endphp
                            <tr class="hover:bg-slate-50 transition">
                                <td class="p-3 font-bold text-slate-900">{{ $formattedTicketNo }}</td>
                                <td class="p-3 font-medium text-slate-800 truncate max-w-xs">{{ $ticket->title }}</td>
                                <td class="p-3 text-xs text-slate-600 font-medium">{{ $ticket->user_name ?? ($ticket->user->name ?? 'User') }}</td>
                                <td class="p-3 font-medium text-slate-700">
                                    <span class="px-2 py-0.5 rounded bg-slate-100 border text-slate-700 text-xs font-semibold">
                                        {{ $ticket->category }}
                                    </span>
                                </td>
                                <td class="p-3">
                                    <span class="px-2.5 py-1 text-xs font-bold rounded text-white inline-block
                                        @if($ticket->status == 'New') bg-green-600
                                        @elseif($ticket->status == 'Processing') bg-blue-500
                                        @elseif($ticket->status == 'Pending') bg-amber-600
                                        @elseif($ticket->status == 'Close' || $ticket->status == 'Closed') bg-red-600
                                        @else bg-emerald-600 @endif">
                                        {{ $ticket->status }}
                                    </span>
                                </td>
                                <td class="p-3 text-xs text-slate-500 font-medium">
                                    {{ $ticket->picAdmin->full_name ?? $ticket->picAdmin->name ?? $ticket->pic ?? 'Unassigned' }}
                                </td>
                                <td class="p-3 text-center">
                                    <a href="{{ route('admin.tickets.edit', $ticket->ticket_id ?? $ticket->id) }}" 
                                       class="px-3 py-1.5 bg-slate-100 hover:bg-orange-500 hover:text-white text-slate-700 rounded-lg text-xs font-semibold transition border inline-flex items-center gap-1">
                                        <i class="fa-solid fa-pen-to-square"></i> Edit
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="p-8 text-center text-slate-400 text-xs">
                                    <i class="fa-regular fa-folder-open text-3xl mb-2 text-slate-300 block"></i>
                                    No tickets found in the system.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>
@endsection