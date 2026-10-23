@extends('layouts.admin')

@section('title', 'Ticket Details #' . ($ticket->ticket_id ?? $ticket->id))

@section('content')
<div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 space-y-6">

    <!-- Flash Message Notification -->
    @if(session('success'))
        <div class="p-4 bg-emerald-100 border border-emerald-200 text-emerald-800 rounded-xl text-xs font-bold">
            {{ session('success') }}
        </div>
    @endif

    <!-- Back to Ticket List Button -->
    <div class="mb-4">
        <a href="{{ route('admin.tickets.index') }}" 
        class="inline-flex items-center gap-2 px-4 py-2 bg-orange-500 hover:bg-orange-200 text-white text-xs font-bold rounded-xl transition duration-150">
            <i class="fa-solid fa-arrow-left"></i>
            <span>Back to Ticket List</span>
        </a>
    </div>

    <!-- GRID LAYOUT 2 RUANG (Kiri 2/3, Kanan 1/3) -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 items-start">

        <!-- RUANG KIRI: Conversation / Chat History (Width 2/3) -->
        <div class="lg:col-span-2 bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden flex flex-col h-[650px]">
            
            <!-- Chat Header -->
            <div class="px-5 py-3.5 bg-white border-b border-slate-200 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-orange-500 text-white font-bold flex items-center justify-center text-sm">
                        {{ strtoupper(substr($ticket->user_name ?? 'U', 0, 2)) }}
                    </div>
                    <div>
                        <h2 class="text-sm font-bold text-slate-800">
                            Ticket #{{ $ticket->ticket_id ?? $ticket->id }} - {{ $ticket->title }}
                        </h2>
                        <span class="px-2 py-0.5 rounded font-bold text-[10px] 
                            @if($ticket->status == 'New') bg-emerald-100 text-emerald-700
                            @elseif($ticket->status == 'Processing') bg-blue-100 text-blue-700
                            @elseif($ticket->status == 'Pending') bg-amber-100 text-amber-700
                            @elseif($ticket->status == 'Close') bg-red-100 text-red-700
                            @else bg-slate-100 text-slate-700 @endif">
                            {{ $ticket->status }}
                        </span>
                    </div>
                </div>
            </div>

            <!-- Chat Scroll Area -->
            <div class="flex-1 p-6 overflow-y-auto space-y-4 bg-slate-50/50">
                
                <!-- Tarikh -->
                <div class="flex justify-center my-2">
                    <span class="px-3 py-1 bg-white border text-slate-500 rounded-full text-[10px] font-semibold">
                        {{ $ticket->date ?? $ticket->created_at->format('Y-m-d') }}
                    </span>
                </div>

                <!-- Mesej Pertama dari User -->
                <div class="flex items-start gap-2.5 max-w-[80%]">
                    <div class="w-8 h-8 rounded-lg bg-slate-700 text-white text-xs font-bold flex items-center justify-center shrink-0">
                        {{ strtoupper(substr($ticket->user_name ?? 'U', 0, 1)) }}
                    </div>
                    <div class="bg-white border border-slate-200 p-3.5 rounded-2xl rounded-tl-none text-slate-800 text-xs shadow-xs space-y-1">
                        <div class="font-bold text-orange-600 text-[11px]">
                            {{ $ticket->user_name }} ({{ $ticket->category }})
                        </div>
                        <p class="leading-relaxed">{!! $ticket->description !!}</p>
                    </div>
                </div>

                <!-- Loop Balasan Mesej (Replies) -->
                @if($ticket->replies && $ticket->replies->count() > 0)
                    @foreach($ticket->replies as $reply)
                        @php
                            $isAdmin = ($reply->sender_role ?? '') == 'Admin' || str_contains(strtolower($reply->sender_name ?? ''), 'admin');
                        @endphp

                        @if($isAdmin)
                            <!-- Mesej Admin (Sebelah Kanan) -->
                            <div class="flex items-start justify-end gap-2.5 max-w-[80%] ml-auto">
                                <div class="bg-[#0d1527] text-white p-3.5 rounded-2xl rounded-tr-none text-xs shadow-xs space-y-1">
                                    <div class="font-bold text-orange-400 text-[10px]">{{ $reply->sender_name ?? 'Admin' }}</div>
                                    <p class="leading-relaxed">{!! $reply->message !!}</p>
                                    <div class="text-[9px] text-slate-400 text-right">{{ $reply->created_at ? $reply->created_at->format('H:i A') : '' }}</div>
                                </div>
                                <div class="w-8 h-8 rounded-lg bg-orange-500 text-white text-xs font-bold flex items-center justify-center shrink-0">
                                    {{ strtoupper(substr($reply->sender_name ?? 'A', 0, 1)) }}
                                </div>
                            </div>
                        @else
                            <!-- Mesej User (Sebelah Kiri) -->
                            <div class="flex items-start gap-2.5 max-w-[80%]">
                                <div class="w-8 h-8 rounded-lg bg-slate-700 text-white text-xs font-bold flex items-center justify-center shrink-0">
                                    {{ strtoupper(substr($reply->sender_name ?? 'U', 0, 1)) }}
                                </div>
                                <div class="bg-white border border-slate-200 p-3.5 rounded-2xl rounded-tl-none text-slate-800 text-xs shadow-xs space-y-1">
                                    <div class="font-bold text-orange-600 text-[10px]">{{ $reply->sender_name }}</div>
                                    <p class="leading-relaxed">{!! $reply->message !!}</p>
                                    <div class="text-[9px] text-slate-400 text-right">{{ $reply->created_at ? $reply->created_at->format('H:i A') : '' }}</div>
                                </div>
                            </div>
                        @endif
                    @endforeach
                @endif

            </div>

            <!-- Chat Input Box -->
            <div class="p-3.5 bg-white border-t border-slate-200">
                <form action="{{ route('admin.messages.reply', $ticket->ticket_id ?? $ticket->id) }}" method="POST" enctype="multipart/form-data" class="flex items-center gap-2">
                    @csrf
                    <label class="p-2.5 text-slate-400 hover:text-orange-500 rounded-xl transition cursor-pointer">
                        <i class="fa-solid fa-paperclip text-base"></i>
                        <input type="file" name="attachment" class="hidden">
                    </label>
                    <input type="text" name="message" required placeholder="Type a message..." 
                           class="flex-1 bg-slate-100 border border-transparent focus:border-orange-500 focus:bg-white rounded-xl px-4 py-2.5 text-xs outline-none transition">
                    <button type="submit" class="bg-orange-500 hover:bg-orange-600 text-white px-5 py-2.5 rounded-xl text-xs font-bold transition flex items-center gap-1.5">
                        <span>Send</span>
                        <i class="fa-solid fa-paper-plane text-[10px]"></i>
                    </button>
                </form>
            </div>

        </div>

        <!-- RIGHT SIDE: Editable Ticket Information Form (Width 1/3) -->
        <div class="bg-white p-6 rounded-2xl border border-slate-200/80 shadow-sm space-y-5">
            
            <div class="flex items-center justify-between border-b border-slate-100 pb-4">
                <div class="flex items-center gap-2 text-slate-800 font-bold text-sm tracking-wide uppercase">
                    <i class="fa-solid fa-sliders text-orange-500"></i>
                    <span>Ticket Information</span>
                </div>
                <div class="text-right text-[11px] text-slate-400 font-medium">
                    <div><i class="fa-regular fa-calendar mr-1"></i>{{ $ticket->date ?? (is_object($ticket->created_at) ? $ticket->created_at->format('Y-m-d') : \Carbon\Carbon::parse($ticket->created_at)->format('Y-m-d')) }}</div>
                    <div><i class="fa-regular fa-clock mr-1"></i>{{ is_object($ticket->created_at) ? $ticket->created_at->format('h:i A') : \Carbon\Carbon::parse($ticket->created_at)->format('h:i A') }}</div>
                </div>
            </div>

            <!-- Ticket Information Update Form -->
            <form action="{{ route('admin.tickets.update', $ticket->ticket_id ?? $ticket->id) }}" method="POST" class="space-y-4">
                @csrf
                @method('PUT')

                <!-- Category -->
                <div>
                    <label class="block text-xs font-extrabold uppercase tracking-wider text-slate-400 mb-1.5">Category</label>
                    <select name="category" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs font-bold text-slate-700 focus:ring-2 focus:ring-orange-500 outline-none transition">
                        @foreach($categories as $cat)
                            <option value="{{ $cat }}" {{ $ticket->category == $cat ? 'selected' : '' }}>{{ $cat }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Type -->
                <div>
                    <label class="block text-xs font-extrabold uppercase tracking-wider text-slate-400 mb-1.5">Type</label>
                    <select name="type" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs font-bold text-slate-700 focus:ring-2 focus:ring-orange-500 outline-none transition">
                        @foreach($types as $type)
                            <option value="{{ $type }}" {{ $ticket->type == $type ? 'selected' : '' }}>{{ $type }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Status -->
                <div>
                    <label class="block text-xs font-extrabold uppercase tracking-wider text-slate-400 mb-1.5">Status</label>
                    <select name="status" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs font-bold text-slate-700 focus:ring-2 focus:ring-orange-500 outline-none transition">
                        @foreach($statuses as $st)
                            <option value="{{ $st }}" {{ $ticket->status == $st ? 'selected' : '' }}>{{ $st }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Priority -->
                <div>
                    <label class="block text-xs font-extrabold uppercase tracking-wider text-slate-400 mb-1.5">Priority</label>
                    <select name="priority" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs font-bold text-slate-700 focus:ring-2 focus:ring-orange-500 outline-none transition">
                        @foreach($priorities as $prio)
                            <option value="{{ $prio }}" {{ $ticket->priority == $prio ? 'selected' : '' }}>{{ $prio }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Incident Location -->
                <div>
                    <label class="block text-xs font-extrabold uppercase tracking-wider text-slate-400 mb-1.5">Incident Location</label>
                    <div class="relative">
                        <i class="fa-solid fa-location-dot absolute left-3 top-1/2 -translate-y-1/2 text-orange-500 text-xs"></i>
                        <input type="text" name="location" value="{{ $ticket->location }}" 
                               class="w-full pl-8 pr-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs font-medium text-slate-700 focus:ring-2 focus:ring-orange-500 outline-none transition">
                    </div>
                </div>

                <!-- ASSIGN TO (PIC) DROPDOWN -->
                <div>
                    <label class="block text-xs font-extrabold uppercase tracking-wider text-slate-400 mb-1.5">Assign To (PIC)</label>
                    <select name="pic" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs font-bold text-slate-700 focus:ring-2 focus:ring-orange-500 outline-none transition">
                        <option value="">-- Unassigned --</option>
                        @foreach($adminsInDept as $adm)
                            <option value="{{ $adm->user_id }}" {{ $ticket->pic == $adm->user_id ? 'selected' : '' }}>
                                {{ $adm->full_name ?? $adm->name }} ({{ $adm->role }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Save Button -->
                <div class="pt-3">
                    <button type="submit" class="w-full py-2.5 bg-orange-500 hover:bg-orange-600 text-white font-bold rounded-xl text-xs shadow-md transition">
                        Update Information
                    </button>
                </div>
            </form>

        </div>

    </div>

</div>
@endsection