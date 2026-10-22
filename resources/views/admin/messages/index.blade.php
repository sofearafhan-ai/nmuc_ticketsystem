@extends('layouts.admin')

@section('title', 'Admin - Message')

@section('content')
<div class="h-[calc(100vh-140px)] flex flex-col space-y-4">

    <!-- MAIN CHAT CONTAINER (2 Columns Layout) -->
    <div class="flex-1 bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden flex flex-col md:flex-row">

        <!-- LEFT PANEL: Conversation List (Width 1/3) -->
        <div class="w-full md:w-80 lg:w-96 border-b md:border-b-0 md:border-r border-slate-200 bg-slate-50 flex flex-col shrink-0">
            
            <!-- Search & Filter Bar -->
            <div class="p-3.5 border-b border-slate-200 bg-white">
                <form action="{{ route('admin.messages.index') }}" method="GET" class="relative">
                    <i class="fa-solid fa-magnifying-glass absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Search..." 
                           class="w-full pl-9 pr-4 py-2 bg-slate-100 rounded-xl text-xs text-slate-800 placeholder-slate-400 border border-transparent focus:border-orange-500 focus:bg-white outline-none transition">
                </form>
                
                <!-- Quick Filter Badges -->
                <div class="flex items-center gap-1.5 mt-3 text-[11px]">
                    <a href="{{ route('admin.messages.index') }}" 
                       class="px-2.5 py-1 rounded-lg font-bold cursor-pointer transition {{ !request('status') ? 'bg-orange-500 text-white' : 'bg-slate-200 text-slate-600 hover:bg-slate-300' }}">
                       All
                    </a>
                    <a href="{{ route('admin.messages.index', ['status' => 'Open']) }}" 
                       class="px-2.5 py-1 rounded-lg font-medium cursor-pointer transition {{ request('status') == 'Open' ? 'bg-orange-500 text-white' : 'bg-slate-200 text-slate-600 hover:bg-slate-300' }}">
                       Open Tickets
                    </a>
                    <a href="{{ route('admin.messages.index', ['status' => 'Closed']) }}" 
                       class="px-2.5 py-1 rounded-lg font-medium cursor-pointer transition {{ request('status') == 'Closed' ? 'bg-orange-500 text-white' : 'bg-slate-200 text-slate-600 hover:bg-slate-300' }}">
                       Closed Tickets
                    </a>
                </div>
            </div>

            <!-- Conversations Scrollable List -->
            <div class="flex-1 overflow-y-auto divide-y divide-slate-100">
                @php
                    $chatList = $conversations ?? $tickets ?? [];
                @endphp

                @forelse ($chatList as $conv)
                    @php 
                        $convId = $conv->ticket_id ?? $conv->id;
                        $activeId = $activeTicket->ticket_id ?? $activeTicket->id ?? null;
                        $isActive = $activeTicket && $activeId == $convId;
                    @endphp
                    <a href="{{ route('admin.messages.index', ['ticket_id' => $convId]) }}" 
                       class="flex items-center gap-3 p-3.5 hover:bg-orange-50/60 transition cursor-pointer {{ $isActive ? 'bg-orange-50 border-l-4 border-orange-500' : '' }}">
                        
                        <!-- Avatar -->
                        <div class="relative shrink-0">
                            <div class="w-11 h-11 rounded-xl bg-slate-800 text-white font-bold flex items-center justify-center text-sm shadow-sm">
                                {{ strtoupper(substr($conv->user_name ?? $conv->user->name ?? 'U', 0, 2)) }}
                            </div>
                            <span class="absolute -bottom-0.5 -right-0.5 w-3.5 h-3.5 rounded-full border-2 border-white 
                                @if(($conv->status ?? '') == 'New') bg-emerald-500
                                @elseif(($conv->status ?? '') == 'Processing') bg-blue-500
                                @elseif(($conv->status ?? '') == 'Pending') bg-amber-500
                                @elseif(($conv->status ?? '') == 'Close') bg-red-500
                                @else bg-slate-500 @endif">
                            </span>
                        </div>

                        <!-- Chat Details -->
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center justify-between mb-0.5">
                                <h2 class="text-xs font-bold text-slate-800 truncate">{{ $conv->user_name ?? $conv->user->name ?? 'User' }}</h2>
                                <span class="text-[10px] text-slate-400 font-medium shrink-0">
                                    {{ $conv->date ?? ($conv->created_at ? $conv->created_at->format('Y-m-d') : '') }}
                                </span>
                            </div>
                            <p class="text-xs font-semibold text-orange-600 truncate mb-0.5">
                                #{{ $convId }} - {{ $conv->title ?? $conv->subject ?? 'Ticket' }}
                            </p>
                            <p class="text-[11px] text-slate-500 truncate">
                                {{ strip_tags($conv->description ?? '') }}
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
                @php
                    $activeId = $activeTicket->ticket_id ?? $activeTicket->id;
                @endphp
                <!-- Active Chat Header -->
                <div class="px-5 py-3.5 bg-white border-b border-slate-200 flex items-center justify-between shadow-xs">
                    <div class="flex items-center gap-3 min-w-0">
                        <!-- Avatar Initial -->
                        <div class="w-10 h-10 rounded-xl bg-orange-500 text-white font-bold flex items-center justify-center text-sm shrink-0">
                            {{ strtoupper(substr($activeTicket->user_name ?? $activeTicket->user->name ?? 'U', 0, 2)) }}
                        </div>
                        
                        <!-- Header Title & Status -->
                        <div class="flex items-center gap-2 min-w-0">
                            <h2 class="text-sm font-bold text-slate-800 truncate">
                                Ticket #{{ $activeId }} - {{ $activeTicket->title }}
                            </h2>
                            <span class="text-slate-400">•</span>
                            <span class="px-2 py-0.5 rounded font-bold text-[10px]
                                @if(($activeTicket->status ?? '') == 'New') bg-emerald-100 text-emerald-700
                                @elseif(($activeTicket->status ?? '') == 'Processing') bg-blue-100 text-blue-700
                                @elseif(($activeTicket->status ?? '') == 'Pending') bg-amber-100 text-amber-700
                                @elseif(($activeTicket->status ?? '') == 'Close') bg-red-100 text-red-700
                                @else bg-slate-100 text-slate-700 @endif">
                                {{ $activeTicket->status }}
                            </span>
                        </div>
                    </div>

                    <!-- View Detail / Edit Ticket Button -->
                    <div class="flex items-center gap-3">
                        <a href="{{ route('admin.tickets.edit', $activeId) }}" 
                           class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-orange-50 hover:bg-orange-100 text-orange-600 text-xs font-bold rounded-lg border border-orange-200 transition shadow-2xs">
                            <span>View Detail</span>
                            <i class="fa-solid fa-arrow-up-right-from-square text-[10px]"></i>
                        </a>
                    </div>
                </div>

                <!-- Chat Messages Scroll Area -->
                <div class="flex-1 p-4 md:p-6 overflow-y-auto space-y-4">
                    
                    <!-- Date Separator Badge -->
                    <div class="flex justify-center my-2">
                        <span class="px-3 py-1 bg-white border border-slate-200 text-slate-500 rounded-full text-[10px] font-semibold shadow-xs">
                            {{ $activeTicket->date ?? ($activeTicket->created_at ? $activeTicket->created_at->format('Y-m-d') : 'Today') }}
                        </span>
                    </div>

                    <!-- 1. First Ticket Message (User) -->
                    <div class="flex items-start gap-2.5 max-w-[85%] md:max-w-[70%]">
                        <div class="w-8 h-8 rounded-lg bg-slate-700 text-white text-xs font-bold flex items-center justify-center shrink-0 mt-1">
                            {{ strtoupper(substr($activeTicket->user_name ?? 'U', 0, 1)) }}
                        </div>
                        <div class="bg-white border border-slate-200/90 p-3.5 rounded-2xl rounded-tl-none shadow-xs text-slate-800 space-y-1">
                            <div class="text-[10px] font-bold text-orange-600 mb-1">
                                {{ $activeTicket->user_name ?? 'User' }} ({{ $activeTicket->category ?? 'General' }})
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
                                $replyName = $reply->sender_name ?? $reply->user_name ?? 'User';
                                $isAdmin = ($reply->sender_role ?? '') == 'Admin' || str_contains(strtolower($replyName), 'admin') || ($reply->user_id ?? null) == Auth::id();
                            @endphp

                            @if($isAdmin)
                                <!-- Admin Reply (Right Side) -->
                                <div class="flex items-start justify-end gap-2.5 max-w-[85%] md:max-w-[70%] ml-auto">
                                    <div class="bg-[#0d1527] text-white p-3.5 rounded-2xl rounded-tr-none shadow-xs space-y-1">
                                        <div class="text-[10px] font-bold text-orange-400 mb-1">
                                            {{ $replyName }}
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
                                        </div>
                                    </div>
                                    <div class="w-8 h-8 rounded-lg bg-orange-500 text-white text-xs font-bold flex items-center justify-center shrink-0 mt-1">
                                        {{ strtoupper(substr($replyName, 0, 1)) }}
                                    </div>
                                </div>
                            @else
                                <!-- User Reply (Left Side) -->
                                <div class="flex items-start gap-2.5 max-w-[85%] md:max-w-[70%]">
                                    <div class="w-8 h-8 rounded-lg bg-slate-700 text-white text-xs font-bold flex items-center justify-center shrink-0 mt-1">
                                        {{ strtoupper(substr($replyName, 0, 1)) }}
                                    </div>
                                    <div class="bg-white border border-slate-200/90 p-3.5 rounded-2xl rounded-tl-none shadow-xs text-slate-800 space-y-1">
                                        <div class="text-[10px] font-bold text-orange-600 mb-1">
                                            {{ $replyName }}
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
                    <form action="{{ route('admin.messages.reply', $activeId) }}" method="POST" enctype="multipart/form-data" class="flex items-center gap-2">
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