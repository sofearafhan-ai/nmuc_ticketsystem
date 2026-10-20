@extends('layouts.app')

@section('title', 'Admin - ' . $adminDept . ' Tickets')

@section('content')
<div class="space-y-6">

    <!-- Header Section -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center bg-slate-900 p-6 rounded-xl text-white shadow-md gap-4">
        <div>
            <div class="flex items-center gap-2">
                <span class="bg-orange-500 text-white text-[10px] font-extrabold px-2 py-0.5 rounded tracking-wide uppercase">{{ $adminDept }} Department</span>
                <h1 class="text-2xl font-bold">Manage Support Tickets</h1>
            </div>
            <p class="text-xs text-slate-400 mt-1">Senarai tiket aduan yang dihantar untuk tindakan Jabatan {{ $adminDept }}.</p>
        </div>
    </div>

    @if (session('success'))
        <div class="bg-emerald-900/90 border-l-4 border-emerald-400 text-emerald-100 px-4 py-3 rounded-lg shadow-sm text-sm flex items-center gap-2">
            <i class="fa-solid fa-circle-check text-emerald-300"></i>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    <!-- Search & Filter Bar -->
    <div class="bg-white p-5 rounded-lg shadow-md border border-orange-200">
        <form method="GET" action="{{ route('admin.tickets.index') }}" class="flex flex-col md:flex-row gap-3 items-center">
            <div class="relative flex-1 w-full">
                <i class="fa-solid fa-magnifying-glass absolute left-3.5 top-3 text-slate-400 text-sm"></i>
                <input type="text" name="search" value="{{ request('search') }}" 
                       placeholder="Cari ID tiket, tajuk, atau nama pengadu..." 
                       class="w-full pl-10 pr-4 py-2 bg-slate-50 border border-slate-300 rounded-lg text-sm text-slate-800 focus:ring-2 focus:ring-orange-500 outline-none transition">
            </div>
            
            <div class="w-full md:w-48">
                <select name="status" onchange="this.form.submit()" class="w-full p-2 bg-slate-50 border border-slate-300 rounded-lg text-sm font-medium text-slate-700 outline-none">
                    <option value="">Semua Status</option>
                    <option value="New" {{ request('status') == 'New' ? 'selected' : '' }}>New</option>
                    <option value="Processing" {{ request('status') == 'Processing' ? 'selected' : '' }}>Processing</option>
                    <option value="Pending" {{ request('status') == 'Pending' ? 'selected' : '' }}>Pending</option>
                    <option value="Solved" {{ request('status') == 'Solved' ? 'selected' : '' }}>Solved</option>
                    <option value="Closed" {{ request('status') == 'Closed' ? 'selected' : '' }}>Closed</option>
                </select>
            </div>

            <button type="submit" class="px-4 py-2 bg-orange-500 hover:bg-orange-600 text-white text-sm font-semibold rounded-lg transition shadow-sm cursor-pointer">
                <i class="fa-solid fa-filter text-xs"></i> Tapis
            </button>
        </form>
    </div>

    <!-- Tickets Table Card -->
    <div class="bg-white rounded-xl shadow-md border border-orange-200 overflow-hidden">
        <div class="p-6">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="border-b border-orange-200 bg-orange-100/60 text-orange-950 uppercase text-[11px] font-bold tracking-wider">
                            <th class="p-3">Ticket ID</th>
                            <th class="p-3">Title</th>
                            <th class="p-3">Submitted By</th>
                            <th class="p-3">Category</th>
                            <th class="p-3">Priority</th>
                            <th class="p-3">Status</th>
                            <th class="p-3">Date</th>
                            <th class="p-3 text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200 text-sm">
                        @forelse ($tickets as $ticket)
                            <tr class="hover:bg-orange-50/50 transition">
                                <td class="p-3 font-bold text-slate-900">
                                    <a href="{{ route('tickets.show', $ticket->ticket_id) }}" class="text-orange-600 hover:underline">
                                        #{{ $ticket->ticket_id }}
                                    </a>
                                </td>
                                <td class="p-3 font-semibold text-slate-800">{{ $ticket->title }}</td>
                                <td class="p-3 text-xs text-slate-600 font-medium">{{ $ticket->user_name }}</td>
                                <td class="p-3 font-medium text-slate-700">{{ $ticket->category }}</td>
                                <td class="p-3">
                                    <span class="px-2.5 py-1 text-xs font-bold rounded 
                                        @if($ticket->priority == 'Urgent') bg-red-100 text-red-800
                                        @elseif($ticket->priority == 'High') bg-orange-200 text-orange-900
                                        @elseif($ticket->priority == 'Medium') bg-yellow-100 text-yellow-800
                                        @else bg-slate-100 text-slate-800 @endif">
                                        {{ $ticket->priority }}
                                    </span>
                                </td>
                                <td class="p-3">
                                    <span class="px-2.5 py-1 text-xs font-bold rounded text-white inline-block
                                        @if($ticket->status == 'New') bg-blue-600
                                        @elseif($ticket->status == 'Processing') bg-amber-500
                                        @elseif($ticket->status == 'Pending') bg-purple-600
                                        @elseif($ticket->status == 'Closed') bg-slate-600
                                        @else bg-emerald-600 @endif">
                                        {{ $ticket->status }}
                                    </span>
                                </td>
                                <td class="p-3 text-xs text-slate-500 font-medium">{{ $ticket->date }}</td>
                                <td class="p-3 text-center">
                                    <a href="{{ route('tickets.show', $ticket->ticket_id) }}" class="px-3 py-1 bg-slate-900 hover:bg-slate-800 text-white text-xs font-semibold rounded-md shadow transition">
                                        <i class="fa-solid fa-eye text-[10px]"></i> View
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="p-8 text-center text-slate-500 font-medium">
                                    <i class="fa-solid fa-inbox text-2xl mb-2 text-slate-300 block"></i>
                                    Tiada aduan tiket dijumpai untuk Jabatan {{ $adminDept }}.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Custom Pagination Navigation -->
            @if ($tickets->hasPages())
                <div class="mt-6">
                    {{ $tickets->links() }}
                </div>
            @endif
        </div>
    </div>

</div>
@endsection