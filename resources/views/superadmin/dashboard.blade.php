@extends('layouts.superadmin')

@section('title', 'Dashboard')

@section('content')
<div class="space-y-6">

    <!-- Header Section -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center bg-[#0d1527] p-6 rounded-2xl text-white shadow-md gap-4">
        <div>
            <div class="flex items-center gap-2">
                <span class="bg-purple-600 text-white text-[10px] font-extrabold px-3 py-1 rounded-full uppercase tracking-wider shadow-sm flex items-center gap-1">
                    OVERALL SYSTEM OVERVIEW
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
                <h3 class="text-2xl font-extrabold text-slate-800">{{ $ticketStats['total'] ?? 0 }}</h3>
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
                <h3 class="text-2xl font-extrabold text-slate-800">{{ $userStats['total'] ?? 0 }}</h3>
                <p class="text-[11px] text-purple-600 font-semibold flex items-center gap-1">
                    <i class="fa-solid fa-user-shield"></i> {{ $userStats['admins'] ?? 0 }} Admins / {{ $userStats['staff'] ?? 0 }} Staff
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
                <h3 class="text-2xl font-extrabold text-slate-800">{{ $inventoryStats['total'] ?? 0 }}</h3>
                <p class="text-[11px] text-blue-600 font-semibold flex items-center gap-1">
                    <i class="fa-solid fa-box"></i> {{ $inventoryStats['active'] ?? 0 }} Active Items
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
                <h3 class="text-2xl font-extrabold text-slate-800">{{ $userStats['departments'] ?? 0 }}</h3>
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
            <div class="text-xl font-extrabold text-emerald-900 mt-1">{{ $ticketStats['new'] ?? 0 }}</div>
        </div>
        <div class="bg-blue-50 border border-blue-200 p-4 rounded-2xl text-center">
            <span class="text-xs font-bold text-blue-700 uppercase">Processing</span>
            <div class="text-xl font-extrabold text-blue-900 mt-1">{{ $ticketStats['processing'] ?? 0 }}</div>
        </div>
        <div class="bg-amber-50 border border-amber-200 p-4 rounded-2xl text-center">
            <span class="text-xs font-bold text-amber-700 uppercase">Pending</span>
            <div class="text-xl font-extrabold text-amber-900 mt-1">{{ $ticketStats['pending'] ?? 0 }}</div>
        </div>
        <div class="bg-red-50 border border-red-200 p-4 rounded-2xl text-center">
            <span class="text-xs font-bold text-red-700 uppercase">Closed Tickets</span>
            <div class="text-xl font-extrabold text-red-900 mt-1">{{ $ticketStats['close'] ?? 0 }}</div>
        </div>
    </div>

    <!-- RECENT UNASSIGNED TICKETS TABLE -->
    <div class="bg-white p-6 rounded-3xl shadow-sm border border-slate-100 space-y-4">
        <div class="flex justify-between items-start">
            <div>
                <h3 class="font-bold text-slate-800 text-base flex items-center gap-2">
                    <i class="fa-solid fa-ticket text-orange-500"></i> New Unassigned Tickets
                </h3>
                <p class="text-xs text-slate-400 mt-0.5">Tickets awaiting PIC assignment across all departments</p>
            </div>
            <a href="{{ route('superadmin.tickets.index') }}" class="text-xs font-bold text-orange-600 hover:underline flex items-center gap-1 mt-1">
                <span>View All Tickets</span>
                <i class="fa-solid fa-arrow-right text-[10px]"></i>
            </a>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50/80 text-slate-400 uppercase text-[11px] font-bold tracking-wider border-b border-slate-100">
                        <th class="py-3 px-4 text-center">TICKET ID</th>
                        <th class="py-3 px-4 text-center">TITLE</th>
                        <th class="py-3 px-4 text-center">SUBMITTED BY</th>
                        <th class="py-3 px-4 text-center">CATEGORY</th>
                        <th class="py-3 px-4 text-center">PRIORITY</th>
                        <th class="py-3 px-4 text-center">DEPARTMENT</th>
                        <th class="py-3 px-4 text-center">STATUS</th>
                        <th class="py-3 px-4 text-center">DATE</th>
                        <th class="py-3 px-4 text-center">ACTION</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-xs font-medium text-slate-700">
                    @forelse ($recentTickets as $ticket)
                        @php
                            $ticketId = $ticket->ticket_id ?? $ticket->id;
                            $formattedTicketNo = str_starts_with((string)$ticketId, '#') ? $ticketId : '#TC-' . $ticketId;
                            
                            if (!empty($ticket->created_at)) {
                                $formattedDate = is_string($ticket->created_at) 
                                    ? \Carbon\Carbon::parse($ticket->created_at)->format('d M Y') 
                                    : $ticket->created_at->format('d M Y');
                            } else {
                                $formattedDate = $ticket->date ? \Carbon\Carbon::parse($ticket->date)->format('d M Y') : '-';
                            }
                        @endphp
                        <tr class="hover:bg-slate-50/50 transition">
                            <td class="py-3 px-4 font-bold text-slate-900">{{ $formattedTicketNo }}</td>
                            <td class="py-3 px-4 font-semibold text-slate-800 truncate max-w-xs">{{ $ticket->title }}</td>
                            <td class="py-3 px-4 text-slate-600 font-semibold">{{ $ticket->user_name ?? ($ticket->user->name ?? 'User') }}</td>
                            <td class="py-3 px-4 text-xs text-slate-700 font-medium text-center">
                                    {{ $ticket->category }}
                                </span>
                            </td>
                            <td class="py-3 px-4 text-center">
                                <span class="px-2 py-0.5 font-bold rounded-full text-[10px]
                                    @if($ticket->priority == 'Urgent') bg-red-100 text-red-800
                                    @elseif($ticket->priority == 'High') bg-orange-200 text-orange-900
                                    @elseif($ticket->priority == 'Medium') bg-yellow-100 text-yellow-800
                                    @else bg-slate-100 text-slate-800 @endif">
                                    {{ $ticket->priority }}
                                </span>
                            </td>
                            <td class="py-3 px-4 text-center text-slate-500 font-medium">{{ $ticket->department_name }}</td>
                            <td class="py-3 px-4 text-center">
                                <span class="px-3 py-1 text-xs font-semibold rounded-full inline-block
                                    @if($ticket->status == 'New') bg-emerald-100 text-emerald-700
                                    @elseif($ticket->status == 'Processing') bg-blue-100 text-blue-700
                                    @elseif($ticket->status == 'Pending') bg-amber-100 text-amber-700
                                    @elseif($ticket->status == 'Close' || $ticket->status == 'Closed') bg-red-100 text-red-700
                                    @else bg-emerald-100 text-emerald-700 @endif">
                                    {{ $ticket->status }}
                                </span>
                            </td>
                            <td class="py-3.5 px-4 text-center text-slate-500 font-medium">{{ $formattedDate }}</td>
                            <td class="py-3.5 px-4 text-center">
                                <a href="{{ route('superadmin.tickets.edit', $ticketId) }}" 
                                   class="bg-orange-500 hover:bg-orange-600 text-white font-bold px-3.5 py-1.5 rounded-xl text-xs transition inline-flex items-center gap-1.5 shadow-sm">
                                    <i class="fa-solid fa-user-plus text-[11px]"></i>
                                    <span>Assign PIC</span>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="p-8 text-center text-slate-400 text-xs">
                                <i class="fa-regular fa-folder-open text-3xl mb-2 text-slate-300 block"></i>
                                No unassigned tickets available right now.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection