@extends('layouts.app')

@section('title', 'Ticket List - Helpdesk')

@section('content')
<div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 space-y-5">
    
    <!-- Title Bar & Filter -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 pb-4 border-b border-slate-100">
        <div>
            <h2 class="text-base font-bold text-slate-800">My Ticket List</h2>
            <p class="text-xs text-slate-400">A list of the complaints and requests for assistance that you have submitted.</p>
        </div>
        
        <a href="{{ route('tickets.create') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-orange-500 hover:bg-orange-600 text-white rounded-xl text-xs font-bold transition shadow-md w-fit">
            <i class="fa-solid fa-plus"></i>
            <span>New Ticket</span>
        </a>
    </div>

    <!-- Table List -->
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-slate-50 text-[11px] font-extrabold uppercase text-slate-400 tracking-wider border-b border-slate-100">
                    <th class="p-3 pl-4">Ticket ID</th>
                    <th class="p-3">Title</th>
                    <th class="p-3">Category</th>
                    <th class="p-3">Priority</th>
                    <th class="p-3">Status</th>
                    <th class="p-3 pr-4 text-right">Action</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 text-xs font-medium text-slate-700">
                @forelse($tickets as $ticket)
                    <tr class="hover:bg-slate-50/80 transition">
                        <td class="p-3 pl-4 font-bold text-slate-900">#{{ $ticket->ticket_id }}</td>
                        <td class="p-3 font-semibold text-slate-800">{{ $ticket->title }}</td>
                        <td class="p-3">{{ $ticket->category }}</td>
                        <td class="p-3">
                            <span class="px-2 py-0.5 rounded-full font-bold text-[10px]
                                @if($ticket->priority == 'Urgent') bg-red-100 text-red-700
                                @elseif($ticket->priority == 'High') bg-orange-100 text-orange-700
                                @elseif($ticket->priority == 'Medium') bg-amber-100 text-amber-700
                                @else bg-slate-100 text-slate-600 @endif">
                                {{ $ticket->priority }}
                            </span>
                        </td>
                        <td class="p-3">
                            <span class="px-2 py-0.5 rounded-full font-bold text-[10px]
                                @if($ticket->status == 'New') bg-emerald-100 text-emerald-700
                                @elseif($ticket->status == 'Processing') bg-blue-100 text-blue-700
                                @elseif($ticket->status == 'Pending') bg-amber-100 text-amber-700
                                @else bg-red-100 text-red-700 @endif">
                                {{ $ticket->status }}
                            </span>
                        </td>
                        <td class="p-3 pr-4 text-right">
                            <a href="{{ route('messages.index', ['ticket_id' => $ticket->ticket_id]) }}" 
                               class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-lg text-xs font-bold transition">
                                <i class="fa-solid fa-comments text-orange-500"></i>
                                <span>Message</span>
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="p-6 text-center text-slate-400">Tiada tiket ditemui.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div class="pt-2">
        {{ $tickets->links() }}
    </div>

</div>
@endsection