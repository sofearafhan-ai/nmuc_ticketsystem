@extends('layouts.admin')

@section('title', 'Admin - Message History')

@section('content')
<div class="h-[calc(100vh-140px)] flex flex-col space-y-4">

    <!-- MAIN CHAT CONTAINER (2 Columns Layout) -->
    <div class="flex-1 bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden flex flex-col md:flex-row">

        <!-- LEFT PANEL: Conversation List (Width 1/3) -->
        <div class="w-full md:w-80 lg:w-96 border-b md:border-b-0 md:border-r border-slate-200 bg-slate-50 flex flex-col shrink-0">
            
            <!-- Search & Filter Bar -->
            <div class="p-3.5 border-b border-slate-200 bg-white">
                <div class="relative">
                    <i class="fa-solid fa-magnifying-glass absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                    <input type="text" placeholder="Search..." 
                           class="w-full pl-9 pr-4 py-2 bg-slate-100 rounded-xl text-xs text-slate-800 placeholder-slate-400 border border-transparent focus:border-orange-500 focus:bg-white outline-none transition">
                </div>
                <!-- Quick Filter Badges -->
                <div class="flex items-center gap-1.5 mt-3 text-[11px]">
                    <span class="px-2.5 py-1 bg-orange-500 text-white font-bold rounded-lg cursor-pointer">All</span>
                    <span class="px-2.5 py-1 bg-slate-200 text-slate-600 font-medium rounded-lg hover:bg-slate-300 cursor-pointer transition">Unread</span>
                    <span class="px-2.5 py-1 bg-slate-200 text-slate-600 font-medium rounded-lg hover:bg-slate-300 cursor-pointer transition">Open Tickets</span>
                    <span class="px-2.5 py-1 bg-slate-200 text-slate-600 font-medium rounded-lg hover:bg-slate-300 cursor-pointer transition">Closed Tickets</span>
                </div>
            </div>

            <!-- Conversations Scrollable List -->
            <div class="flex-1 overflow-y-auto divide-y divide-slate-100">
                @forelse($conversations as $conv)
                    @php 
                        $isActive = $activeTicket && $activeTicket->ticket_id == $conv->ticket_id;
                    @endphp
                    <a href="{{ route('admin.messages.show', $conv->ticket_id) }}" 
                       class="flex items-center gap-3 p-3.5 hover:bg-orange-50/60 transition cursor-pointer {{ $isActive ? 'bg-orange-50 border-l-4 border-orange-500' : '' }}">
                        
                        <!-- Avatar -->
                        <div class="relative shrink-0">
                            <div class="w-11 h-11 rounded-xl bg-slate-800 text-white font-bold flex items-center justify-center text-sm shadow-sm">
                                {{ strtoupper(substr($conv->user_name ?? 'U', 0, 2)) }}
                            </div>
                            <span class="absolute -bottom-0.5 -right-0.5 w-3.5 h-3.5 rounded-full border-2 border-white 
                                @if($conv->status == 'New') bg-blue-500
                                @elseif($conv->status == 'Processing') bg-amber-500
                                @else bg-emerald-500 @endif">
                            </span>
                        </div>

                        <!-- Chat Details -->
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center justify-between mb-0.5">
                                <h2 class="text-xs font-bold text-slate-800 truncate">{{ $conv->user_name }}</h2>
                                <span class="text-[10px] text-slate-400 font-medium shrink-0">
                                    {{ $conv->date ?? '' }}
                                </span>
                            </div>
                            <p class="text-xs font-semibold text-orange-600 truncate mb-0.5">
                                #{{ $conv->ticket_id }} - {{ $conv->title }}
                            </p>
                            <p class="text-[11px] text-slate-500 truncate">
                                <i class="fa-solid fa-check-double text-sky-500 mr-1"></i> {{ strip_tags($conv->description) }}
                            </p>
                        </div>
                    </a>
                @empty
                    <div class="p-8 text-center text-slate-400 text-xs">
                        <i class="fa-regular fa-comments text-3xl mb-2 text-slate-300 block"></i>
                        No chat history found.
                    </div>
                @endforelse
            </div>

        </div>

        <!-- RIGHT PANEL: Active Chat Area (Width 2/3) -->
        <div class="flex-1 bg-[#f8fafc] flex flex-col justify-between min-w-0">
            
            @if($activeTicket)
                <!-- Active Chat Header -->
                <div class="px-5 py-3.5 bg-white border-b border-slate-200 flex items-center justify-between shadow-xs">
                    <div class="flex items-center gap-3 min-w-0">
                        <div class="w-10 h-10 rounded-xl bg-orange-500 text-white font-bold flex items-center justify-center text-sm shrink-0">
                            {{ strtoupper(substr($activeTicket->user_name ?? 'U', 0, 2)) }}
                        </div>
                        <div class="min-w-0">
                            <h2 class="text-sm font-bold text-slate-800 truncate">{{ $activeTicket->user_name }}</h2>
                            <div class="flex items-center gap-2 text-[11px] text-slate-500">
                                <span>Ticket #{{ $activeTicket->ticket_id }}</span>
                                <span>•</span>
                                <span class="px-2 py-0.5 rounded font-bold text-[10px]
                                    @if($activeTicket->status == 'New') bg-blue-100 text-blue-700
                                    @elseif($activeTicket->status == 'Processing') bg-amber-100 text-amber-700
                                    @else bg-emerald-100 text-emerald-700 @endif">
                                    {{ $activeTicket->status }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Action Icons Header -->
                    <div class="flex items-center gap-3 text-slate-500 text-sm">
                        <a href="{{ route('tickets.show', $activeTicket->ticket_id) }}" class="p-2 hover:bg-slate-100 rounded-lg transition" title="View Full Ticket Details">
                            <i class="fa-solid fa-arrow-up-right-from-square text-orange-600"></i>
                        </a>
                        <button class="p-2 hover:bg-slate-100 rounded-lg transition"><i class="fa-solid fa-ellipsis-vertical"></i></button>
                    </div>
                </div>

                <!-- Chat Messages Scroll Area -->
                <div class="flex-1 p-4 md:p-6 overflow-y-auto space-y-4">
                    
                    <!-- Date Separator Badge -->
                    <div class="flex justify-center my-2">
                        <span class="px-3 py-1 bg-white border border-slate-200 text-slate-500 rounded-full text-[10px] font-semibold shadow-xs">
                            {{ $activeTicket->date ?? 'Today' }}
                        </span>
                    </div>

                    <!-- 1. First Ticket Message (User) -->
                    <div class="flex items-start gap-2.5 max-w-[85%] md:max-w-[70%]">
                        <div class="w-8 h-8 rounded-lg bg-slate-700 text-white text-xs font-bold flex items-center justify-center shrink-0 mt-1">
                            {{ strtoupper(substr($activeTicket->user_name ?? 'U', 0, 1)) }}
                        </div>
                        <div class="bg-white border border-slate-200/90 p-3.5 rounded-2xl rounded-tl-none shadow-xs text-slate-800 space-y-1">
                            <div class="text-[10px] font-bold text-orange-600 mb-1">
                                {{ $activeTicket->user_name }} ({{ $activeTicket->category }})
                            </div>
                            <div class="text-xs leading-relaxed">
                                {!! $activeTicket->description !!}
                            </div>
                            <div class="text-[9px] text-slate-400 text-right pt-1">
                                {{ $activeTicket->date ?? '10:00 AM' }}
                            </div>
                        </div>
                    </div>

                    <!-- 2. Replies Loop -->
                    @if(isset($activeTicket->replies) && $activeTicket->replies->count() > 0)
                        @foreach($activeTicket->replies as $reply)
                            @php
                                $isAdmin = str_contains(strtolower($reply->user_name ?? ''), 'admin') || ($reply->user_id ?? null) == Auth::id();
                            @endphp

                            @if($isAdmin)
                                <!-- Admin Reply (Right Side) -->
                                <div class="flex items-start justify-end gap-2.5 max-w-[85%] md:max-w-[70%] ml-auto">
                                    <div class="bg-[#0d1527] text-white p-3.5 rounded-2xl rounded-tr-none shadow-xs space-y-1">
                                        <div class="text-[10px] font-bold text-orange-400 mb-1">
                                            {{ $reply->user_name }}
                                        </div>
                                        <div class="text-xs leading-relaxed text-slate-100">
                                            {!! $reply->message !!}
                                        </div>

                                        @if(!empty($reply->attachment))
                                            <div class="mt-2">
                                                <a href="{{ asset('storage/' . $reply->attachment) }}" target="_blank" class="text-[11px] text-orange-300 underline flex items-center gap-1">
                                                    <i class="fa-solid fa-paperclip"></i> View Attachment
                                                </a>
                                            </div>
                                        @endif

                                        <div class="text-[9px] text-slate-400 text-right pt-1 flex items-center justify-end gap-1">
                                            <span>{{ $reply->created_at ? \Carbon\Carbon::parse($reply->created_at)->format('H:i A') : '' }}</span>
                                            <i class="fa-solid fa-check-double text-sky-400"></i>
                                        </div>
                                    </div>
                                    <div class="w-8 h-8 rounded-lg bg-orange-500 text-white text-xs font-bold flex items-center justify-center shrink-0 mt-1">
                                        {{ strtoupper(substr($reply->user_name ?? 'A', 0, 1)) }}
                                    </div>
                                </div>
                            @else
                                <!-- User Reply (Left Side) -->
                                <div class="flex items-start gap-2.5 max-w-[85%] md:max-w-[70%]">
                                    <div class="w-8 h-8 rounded-lg bg-slate-700 text-white text-xs font-bold flex items-center justify-center shrink-0 mt-1">
                                        {{ strtoupper(substr($reply->user_name ?? 'U', 0, 1)) }}
                                    </div>
                                    <div class="bg-white border border-slate-200/90 p-3.5 rounded-2xl rounded-tl-none shadow-xs text-slate-800 space-y-1">
                                        <div class="text-[10px] font-bold text-orange-600 mb-1">
                                            {{ $reply->user_name }}
                                        </div>
                                        <div class="text-xs leading-relaxed">
                                            {!! $reply->message !!}
                                        </div>

                                        @if(!empty($reply->attachment))
                                            <div class="mt-2">
                                                <a href="{{ asset('storage/' . $reply->attachment) }}" target="_blank" class="text-[11px] text-sky-600 underline flex items-center gap-1">
                                                    <i class="fa-solid fa-paperclip"></i> View Attachment
                                                </a>
                                            </div>
                                        @endif

                                        <div class="text-[9px] text-slate-400 text-right pt-1">
                                            {{ $reply->created_at ? \Carbon\Carbon::parse($reply->created_at)->format('H:i A') : '' }}
                                        </div>
                                    </div>
                                </div>
                            @endif
                        @endforeach
                    @endif

                </div>

                <!-- Bottom Chat Input Bar -->
                <div class="p-3.5 bg-white border-t border-slate-200">
                    <form action="{{ route('admin.messages.reply', $activeTicket->ticket_id) }}" method="POST" enctype="multipart/form-data" class="flex items-center gap-2">
                        @csrf
                        
                        <!-- Attachment Button -->
                        <label class="p-2.5 text-slate-400 hover:text-orange-500 hover:bg-slate-100 rounded-xl transition cursor-pointer" title="Attach file">
                            <i class="fa-solid fa-paperclip text-base"></i>
                            <input type="file" name="attachment" class="hidden">
                        </label>

                        <!-- Input Message Field -->
                        <input type="text" name="message" required placeholder="Type a message..." 
                               class="flex-1 bg-slate-100 border border-transparent focus:border-orange-500 focus:bg-white rounded-xl px-4 py-2.5 text-xs text-slate-800 outline-none transition">

                        <!-- Send Button -->
                        <button type="submit" class="bg-orange-500 hover:bg-orange-600 text-white px-4 py-2.5 rounded-xl text-xs font-bold transition shadow-md flex items-center gap-1.5 cursor-pointer">
                            <span>Send</span>
                            <i class="fa-solid fa-paper-plane text-[10px]"></i>
                        </button>
                    </form>
                </div>

            @else
                <!-- Blank State if No Active Chat Selected -->
                <div class="flex-1 flex flex-col items-center justify-center p-8 text-center">
                    <div class="w-16 h-16 rounded-full bg-orange-100 text-orange-500 flex items-center justify-center text-2xl mb-3 shadow-inner">
                        <i class="fa-solid fa-comments"></i>
                    </div>
                    <h3 class="text-base font-bold text-slate-800 mb-1">Select a Conversation</h3>
                    <p class="text-xs text-slate-500 max-w-xs">Choose a ticket chat from the left panel to start communication.</p>
                </div>
            @endif

        </div>

    </div>

</div>
@endsection