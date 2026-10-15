@extends('layouts.app')

@section('title', 'NMUC - Ticket List')

@section('content')
    <!-- Header Ringkasan & Butang -->
    <div class="flex justify-between items-center bg-orange-600 p-6 rounded-lg border border-orange-700 shadow-md text-white">
        <div>
            <h1 class="text-2xl font-bold text-white">Ticket List</h1>
            <p class="text-xs text-orange-100 mt-1">Menguruskan senarai masalah, insiden, dan permohonan servis anda.</p>
        </div>
        <a href="{{ route('tickets.create') }}" class="px-4 py-2.5 bg-slate-950 hover:bg-slate-900 text-white text-sm font-semibold rounded-lg transition flex items-center gap-2 shadow-md cursor-pointer">
            <i class="fa-solid fa-plus text-xs text-orange-400"></i> New Ticket
        </a>
    </div>

    <!-- Mesej Kejayaan -->
    @if (session('success'))
        <div class="bg-emerald-900/90 border-l-4 border-emerald-400 text-emerald-100 px-4 py-3 rounded-lg shadow-sm text-sm flex items-center gap-2">
            <i class="fa-solid fa-circle-check text-emerald-300"></i>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    <!-- SECTION PENAPIS (FILTER) & CARIAN -->
    <div class="bg-white p-5 rounded-lg shadow-md border border-orange-200">
        <form method="GET" action="{{ route('tickets.index') }}" class="space-y-4">
            
            <!-- Bar Carian Utama -->
            <div class="flex flex-col md:flex-row gap-3 items-center">
                <div class="relative flex-1 w-full">
                    <i class="fa-solid fa-magnifying-glass absolute left-3.5 top-3 text-slate-400 text-sm"></i>
                    <input type="text" name="search" value="{{ request('search') }}" 
                           placeholder="Search..." 
                           class="w-full pl-10 pr-4 py-2 bg-slate-50 border border-slate-300 rounded-lg text-sm text-slate-800 focus:ring-2 focus:ring-orange-500 focus:border-orange-500 outline-none transition">
                </div>
                <div class="flex items-center gap-2 w-full md:w-auto justify-end">
                    <button type="submit" class="px-4 py-2 bg-orange-500 hover:bg-orange-600 text-white text-sm font-semibold rounded-lg transition flex items-center gap-2 shadow-sm cursor-pointer">
                        <i class="fa-solid fa-filter text-xs"></i> Filter
                    </button>
                    @if(request()->hasAny(['search', 'category', 'type', 'priority', 'status', 'month', 'year']))
                        <a href="{{ route('tickets.index') }}" class="px-3 py-2 bg-slate-200 hover:bg-slate-300 text-slate-700 text-sm font-medium rounded-lg transition flex items-center gap-1">
                            <i class="fa-solid fa-rotate-left text-xs"></i> Reset
                        </a>
                    @endif
                </div>
            </div>

            <!-- Dropdown Penapis Kategori & Tarikh -->
            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-6 gap-3 pt-2 border-t border-slate-100">
                
                <!-- Category -->
                <div>
                    <label class="block text-[11px] font-bold text-slate-500 uppercase mb-1">Category</label>
                    <select name="category" onchange="this.form.submit()" class="w-full p-2 bg-slate-50 border border-slate-300 rounded-md text-xs font-medium text-slate-700 focus:ring-1 focus:ring-orange-500 outline-none">
                        <option value="">All Categories</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat }}" {{ request('category') == $cat ? 'selected' : '' }}>{{ $cat }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Type -->
                <div>
                    <label class="block text-[11px] font-bold text-slate-500 uppercase mb-1">Type</label>
                    <select name="type" onchange="this.form.submit()" class="w-full p-2 bg-slate-50 border border-slate-300 rounded-md text-xs font-medium text-slate-700 focus:ring-1 focus:ring-orange-500 outline-none">
                        <option value="">All Types</option>
                        @foreach($types as $type)
                            <option value="{{ $type }}" {{ request('type') == $type ? 'selected' : '' }}>{{ $type }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Priority -->
                <div>
                    <label class="block text-[11px] font-bold text-slate-500 uppercase mb-1">Priority</label>
                    <select name="priority" onchange="this.form.submit()" class="w-full p-2 bg-slate-50 border border-slate-300 rounded-md text-xs font-medium text-slate-700 focus:ring-1 focus:ring-orange-500 outline-none">
                        <option value="">All Priorities</option>
                        @foreach($priorities as $prio)
                            <option value="{{ $prio }}" {{ request('priority') == $prio ? 'selected' : '' }}>{{ $prio }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Status -->
                <div>
                    <label class="block text-[11px] font-bold text-slate-500 uppercase mb-1">Status</label>
                    <select name="status" onchange="this.form.submit()" class="w-full p-2 bg-slate-50 border border-slate-300 rounded-md text-xs font-medium text-slate-700 focus:ring-1 focus:ring-orange-500 outline-none">
                        <option value="">All Statuses</option>
                        @foreach($statuses as $stat)
                            <option value="{{ $stat }}" {{ request('status') == $stat ? 'selected' : '' }}>{{ $stat }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Month -->
                <div>
                    <label class="block text-[11px] font-bold text-slate-500 uppercase mb-1">Month</label>
                    <select name="month" onchange="this.form.submit()" class="w-full p-2 bg-slate-50 border border-slate-300 rounded-md text-xs font-medium text-slate-700 focus:ring-1 focus:ring-orange-500 outline-none">
                        <option value="">All Months</option>
                        @for($m=1; $m<=12; $m++)
                            <option value="{{ sprintf('%02d', $m) }}" {{ request('month') == sprintf('%02d', $m) ? 'selected' : '' }}>
                                {{ date('F', mktime(0, 0, 0, $m, 1)) }}
                            </option>
                        @endfor
                    </select>
                </div>

                <!-- Year -->
                <div>
                    <label class="block text-[11px] font-bold text-slate-500 uppercase mb-1">Year</label>
                    <select name="year" onchange="this.form.submit()" class="w-full p-2 bg-slate-50 border border-slate-300 rounded-md text-xs font-medium text-slate-700 focus:ring-1 focus:ring-orange-500 outline-none">
                        <option value="">All Years</option>
                        @for($y=date('Y'); $y>=2023; $y--)
                            <option value="{{ $y }}" {{ request('year') == $y ? 'selected' : '' }}>{{ $y }}</option>
                        @endfor
                    </select>
                </div>

            </div>

        </form>
    </div>

    <!-- Jadual Tiket -->
    <div class="bg-white rounded-lg shadow-xl overflow-hidden border border-orange-200 text-slate-800">
        <div class="p-6">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="border-b border-orange-200 bg-orange-100/60 text-orange-950 uppercase text-[11px] font-bold tracking-wider">
                            <th class="p-3">Ticket ID</th>
                            <th class="p-3">Title</th>
                            <th class="p-3">Type</th>
                            <th class="p-3">Category</th>
                            <th class="p-3">Priority</th>
                            <th class="p-3">Status</th>
                            <th class="p-3">Date</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200 text-sm">
                        @forelse ($tickets as $ticket)
                            <tr class="hover:bg-orange-50/50 transition">
                                <td class="p-3 font-semibold text-slate-950">
                                    <a href="{{ route('tickets.show', $ticket->ticket_id) }}" class="text-orange-600 hover:text-orange-800 hover:underline font-bold">
                                        #{{ $ticket->ticket_id }}
                                    </a>
                                </td>
                                <td class="p-3 font-medium text-slate-900">
                                    <a href="{{ route('tickets.show', $ticket->ticket_id) }}" class="hover:text-orange-600 hover:underline">
                                        {{ $ticket->title }}
                                    </a>
                                </td>
                                <td class="p-3">
                                    <span class="text-xs bg-orange-100 text-orange-900 px-2 py-1 rounded font-medium border border-orange-200">
                                        {{ $ticket->type }}
                                    </span>
                                </td>
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
                                    <span class="px-2.5 py-1 text-xs font-medium rounded text-white inline-block
                                        @if($ticket->status == 'New') bg-blue-600
                                        @elseif($ticket->status == 'Processing') bg-amber-500
                                        @elseif($ticket->status == 'Pending') bg-purple-600
                                        @else bg-emerald-600 @endif">
                                        {{ $ticket->status }}
                                    </span>
                                </td>
                                <td class="p-3 text-xs text-slate-500 font-medium">{{ $ticket->date }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="p-8 text-center text-slate-500 font-medium">
                                    <i class="fa-solid fa-inbox text-2xl mb-2 text-slate-300 block"></i>
                                    Tiada tiket dijumpai mengikut penapis carian ini.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection