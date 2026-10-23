@extends('layouts.admin')

@section('title', 'Admin - Message')

@section('content')
<div class="min-h-[85vh] flex flex-col space-y-4">

    <!-- MAIN CHAT CONTAINER (2 Columns Layout) -->
    <div class="flex-1 bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden flex flex-col md:flex-row">

        <!-- LEFT PANEL: Conversation List (Width 1/3) -->
        <div class="w-full md:w-80 lg:w-96 border-b md:border-b-0 md:border-r border-slate-200 bg-slate-50 flex flex-col shrink-0">
            
            <!-- Search & Filter Bar -->
            <div class="p-4 border-b border-slate-200 bg-white">
                <form action="{{ route('admin.messages.index') }}" method="GET" class="relative">
                    @if(request('status'))
                        <input type="hidden" name="status" value="{{ request('status') }}">
                    @endif
                    <i class="fa-solid fa-magnifying-glass absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-sm"></i>
                    <input type="text" id="chatSearchInput" name="search" value="{{ request('search') }}" placeholder="Search..." 
                           class="w-full pl-9 pr-4 py-2.5 bg-slate-100 rounded-xl text-xs text-slate-800 placeholder-slate-400 border border-transparent focus:border-orange-500 focus:bg-white outline-none transition">
                </form>
                
                <!-- Quick Filter Badges -->
                <div class="flex items-center gap-1.5 mt-3 text-xs">
                    <a href="{{ route('admin.messages.index', request()->only('search')) }}" 
                       class="px-3 py-1.5 rounded-lg font-bold cursor-pointer transition {{ !request('status') ? 'bg-orange-500 text-white' : 'bg-slate-200 text-slate-600 hover:bg-slate-300' }}">
                       All
                    </a>
                    <a href="{{ route('admin.messages.index', array_merge(request()->only('search'), ['status' => 'Open'])) }}" 
                       class="px-3 py-1.5 rounded-lg font-medium cursor-pointer transition {{ request('status') == 'Open' ? 'bg-orange-500 text-white' : 'bg-slate-200 text-slate-600 hover:bg-slate-300' }}">
                       Open Ticket
                    </a>
                    <a href="{{ route('admin.messages.index', array_merge(request()->only('search'), ['status' => 'Closed'])) }}" 
                       class="px-3 py-1.5 rounded-lg font-medium cursor-pointer transition {{ request('status') == 'Closed' ? 'bg-orange-500 text-white' : 'bg-slate-200 text-slate-600 hover:bg-slate-300' }}">
                       Closed Ticket
                    </a>
                </div>
            </div>

            <!-- Conversations Scrollable List -->
            <div class="flex-1 overflow-y-auto divide-y divide-slate-100" id="chatListContainer">
                @php
                    $chatList = $conversations ?? $tickets ?? [];
                @endphp

                @forelse ($chatList as $conv)
                    @php 
                        $convId = $conv->ticket_id ?? $conv->id;
                        $activeId = $activeTicket->ticket_id ?? $activeTicket->id ?? null;
                        $isActive = $activeTicket && $activeId == $convId;
                        $userName = $conv->user_name ?? $conv->user->name ?? 'User';
                        $ticketTitle = $conv->title ?? $conv->subject ?? 'Ticket';
                    @endphp
                    <a href="{{ route('admin.messages.index', array_merge(request()->only(['search', 'status']), ['ticket_id' => $convId])) }}" 
                       class="chat-item flex items-center gap-3 p-4 hover:bg-orange-50/60 transition cursor-pointer {{ $isActive ? 'bg-orange-50 border-l-4 border-orange-500' : '' }}"
                       data-search="{{ strtolower($userName . ' ' . $ticketTitle . ' ' . $convId) }}">
                        
                        <!-- Avatar -->
                        <div class="relative shrink-0">
                            <div class="w-12 h-12 rounded-xl bg-slate-800 text-white font-bold flex items-center justify-center text-sm shadow-sm">
                                {{ strtoupper(substr($userName, 0, 2)) }}
                            </div>
                            <span class="absolute -bottom-0.5 -right-0.5 w-3.5 h-3.5 rounded-full border-2 border-white 
                                @if(($conv->status ?? '') == 'New') bg-emerald-500
                                @elseif(($conv->status ?? '') == 'Processing') bg-blue-500
                                @elseif(($conv->status ?? '') == 'Pending') bg-amber-500
                                @elseif(($conv->status ?? '') == 'Close' || ($conv->status ?? '') == 'Closed') bg-red-500
                                @else bg-slate-500 @endif">
                            </span>
                        </div>

                        <!-- Chat Details -->
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center justify-between mb-1">
                                <h2 class="text-xs font-bold text-slate-800 truncate">{{ $userName }}</h2>
                                <span class="text-[10px] text-slate-400 font-medium shrink-0">
                                    {{ $conv->date ?? ($conv->created_at ? $conv->created_at->format('Y-m-d') : '') }}
                                </span>
                            </div>
                            <p class="text-xs font-semibold text-orange-600 truncate mb-0.5">
                                #{{ $convId }} - {{ $ticketTitle }}
                            </p>
                            <p class="text-xs text-slate-500 truncate">
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
                    $activeUserName = $activeTicket->user_name ?? $activeTicket->user->name ?? 'User';
                    $ticketMainAttachment = $activeTicket->attachment ?? $activeTicket->file ?? null;
                @endphp
                <!-- Active Chat Header -->
                <div class="px-6 py-4 bg-white border-b border-slate-200 flex items-center justify-between shadow-xs">
                    <div class="flex items-center gap-3 min-w-0">
                        <!-- Avatar Initial -->
                        <div class="w-11 h-11 rounded-xl bg-orange-500 text-white font-bold flex items-center justify-center text-base shrink-0 shadow-sm">
                            {{ strtoupper(substr($activeUserName, 0, 2)) }}
                        </div>
                        
                        <!-- Header Title & Status -->
                        <div class="flex items-center gap-2 min-w-0">
                            <h2 class="text-base font-bold text-slate-800 truncate">
                                Ticket #{{ $activeId }} - {{ $activeTicket->title }}
                            </h2>
                            <span class="text-slate-400">•</span>
                            <span class="px-2.5 py-1 rounded font-bold text-xs
                                @if(($activeTicket->status ?? '') == 'New') bg-emerald-100 text-emerald-700
                                @elseif(($activeTicket->status ?? '') == 'Processing') bg-blue-100 text-blue-700
                                @elseif(($activeTicket->status ?? '') == 'Pending') bg-amber-100 text-amber-700
                                @elseif(($activeTicket->status ?? '') == 'Close' || ($activeTicket->status ?? '') == 'Closed') bg-red-100 text-red-700
                                @else bg-slate-100 text-slate-700 @endif">
                                {{ $activeTicket->status }}
                            </span>
                        </div>
                    </div>

                    <!-- View Detail / Edit Ticket Button -->
                    <div class="flex items-center gap-3">
                        <a href="{{ route('admin.tickets.edit', $activeId) }}" 
                           class="inline-flex items-center gap-1.5 px-3.5 py-2 bg-orange-50 hover:bg-orange-100 text-orange-600 text-xs font-bold rounded-lg border border-orange-200 transition shadow-2xs">
                            <span>View Detail</span>
                            <i class="fa-solid fa-arrow-up-right-from-square text-[10px]"></i>
                        </a>
                    </div>
                </div>

                <!-- Chat Messages Scroll Area -->
                <div class="flex-1 p-5 md:p-6 overflow-y-auto space-y-5">
                    
                    <!-- Date Separator Badge -->
                    <div class="flex justify-center my-2">
                        <span class="px-3.5 py-1 bg-white border border-slate-200 text-slate-500 rounded-full text-xs font-semibold shadow-xs">
                            {{ $activeTicket->date ?? ($activeTicket->created_at ? $activeTicket->created_at->format('Y-m-d') : 'Today') }}
                        </span>
                    </div>

                    <!-- 1. First Ticket Message (User Form Submission) -->
                    <div class="flex items-start gap-3 max-w-[85%] md:max-w-[75%]">
                        <div class="w-9 h-9 rounded-lg bg-slate-700 text-white text-xs font-bold flex items-center justify-center shrink-0 mt-1">
                            {{ strtoupper(substr($activeUserName, 0, 1)) }}
                        </div>
                        <div class="bg-white border border-slate-200/90 p-4 rounded-2xl rounded-tl-none shadow-xs text-slate-800 space-y-2">
                            <div class="text-xs font-bold text-orange-600">
                                {{ $activeUserName }} ({{ $activeTicket->category ?? 'General' }})
                            </div>
                            <div class="text-xs md:text-sm leading-relaxed">
                                {!! $activeTicket->description !!}
                            </div>

                            <!-- Gambar / Attachment yang disubmit dalam Form Utama -->
                            @if(!empty($ticketMainAttachment))
                                @php
                                    $mainExt = strtolower(pathinfo($ticketMainAttachment, PATHINFO_EXTENSION));
                                    $isMainImg = in_array($mainExt, ['jpg', 'jpeg', 'png', 'pdf']);
                                @endphp
                                <div class="mt-3 pt-2 border-t border-slate-100">
                                    @if($isMainImg)
                                        <a href="{{ asset('storage/' . $ticketMainAttachment) }}" target="_blank" class="block group">
                                            <img src="{{ asset('storage/' . $ticketMainAttachment) }}" alt="Ticket Attachment" class="max-w-xs max-h-60 rounded-xl border border-slate-200 object-cover shadow-sm group-hover:opacity-95 transition">
                                        </a>
                                    @else
                                        <a href="{{ asset('storage/' . $ticketMainAttachment) }}" target="_blank" class="text-xs text-sky-600 hover:underline flex items-center gap-1.5 font-medium">
                                            <i class="fa-solid fa-paperclip"></i> View Main Attachment
                                        </a>
                                    @endif
                                </div>
                            @endif

                            <div class="text-[10px] text-slate-400 text-right pt-1">
                                {{ $activeTicket->date ?? ($activeTicket->created_at ? $activeTicket->created_at->format('h:i A') : '') }}
                            </div>
                        </div>
                    </div>

                    <!-- 2. Replies Loop -->
                    @if(isset($activeTicket->replies) && $activeTicket->replies->count() > 0)
                        @foreach($activeTicket->replies as $reply)
                            @php
                                $replyName = $reply->sender_name ?? $reply->user_name ?? ($reply->user->name ?? 'User');
                                $isAdmin = ($reply->sender_role ?? '') == 'Admin' || str_contains(strtolower($replyName), 'admin') || ($reply->user_id ?? null) == Auth::id();
                                $attachment = $reply->attachment ?? null;
                                $ext = $attachment ? strtolower(pathinfo($attachment, PATHINFO_EXTENSION)) : '';
                                $isImage = in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp']);
                            @endphp

                            @if($isAdmin)
                                <!-- Admin Reply (Right Side) -->
                                <div class="flex items-start justify-end gap-3 max-w-[85%] md:max-w-[75%] ml-auto">
                                    <div class="bg-[#0d1527] text-white p-4 rounded-2xl rounded-tr-none shadow-xs space-y-2">
                                        <div class="text-xs font-bold text-orange-400">
                                            {{ $replyName }}
                                        </div>
                                        <div class="text-xs md:text-sm leading-relaxed text-slate-100">
                                            {!! $reply->message !!}
                                        </div>

                                        <!-- Attachment Balasan Admin -->
                                        @if(!empty($attachment))
                                            <div class="mt-2 pt-2 border-t border-slate-700/60">
                                                @if($isImage)
                                                    <a href="{{ asset('storage/' . $attachment) }}" target="_blank" class="block group">
                                                        <img src="{{ asset('storage/' . $attachment) }}" alt="Attachment" class="max-w-xs max-h-56 rounded-xl border border-slate-700 object-cover shadow-sm group-hover:opacity-90 transition">
                                                    </a>
                                                @else
                                                    <a href="{{ asset('storage/' . $attachment) }}" target="_blank" class="text-xs text-orange-300 hover:underline flex items-center gap-1.5">
                                                        <i class="fa-solid fa-paperclip"></i> Download File
                                                    </a>
                                                @endif
                                            </div>
                                        @endif

                                        <div class="text-[10px] text-slate-400 text-right pt-1">
                                            {{ $reply->created_at ? \Carbon\Carbon::parse($reply->created_at)->format('h:i A') : '' }}
                                        </div>
                                    </div>
                                    <div class="w-9 h-9 rounded-lg bg-orange-500 text-white text-xs font-bold flex items-center justify-center shrink-0 mt-1">
                                        {{ strtoupper(substr($replyName, 0, 1)) }}
                                    </div>
                                </div>
                            @else
                                <!-- User Reply (Left Side) -->
                                <div class="flex items-start gap-3 max-w-[85%] md:max-w-[75%]">
                                    <div class="w-9 h-9 rounded-lg bg-slate-700 text-white text-xs font-bold flex items-center justify-center shrink-0 mt-1">
                                        {{ strtoupper(substr($replyName, 0, 1)) }}
                                    </div>
                                    <div class="bg-white border border-slate-200/90 p-4 rounded-2xl rounded-tl-none shadow-xs text-slate-800 space-y-2">
                                        <div class="text-xs font-bold text-orange-600">
                                            {{ $replyName }}
                                        </div>
                                        <div class="text-xs md:text-sm leading-relaxed">
                                            {!! $reply->message !!}
                                        </div>

                                        <!-- Attachment Balasan Pengguna -->
                                        @if(!empty($attachment))
                                            <div class="mt-2 pt-2 border-t border-slate-100">
                                                @if($isImage)
                                                    <a href="{{ asset('storage/' . $attachment) }}" target="_blank" class="block group">
                                                        <img src="{{ asset('storage/' . $attachment) }}" alt="Attachment" class="max-w-xs max-h-56 rounded-xl border border-slate-200 object-cover shadow-sm group-hover:opacity-90 transition">
                                                    </a>
                                                @else
                                                    <a href="{{ asset('storage/' . $attachment) }}" target="_blank" class="text-xs text-sky-600 hover:underline flex items-center gap-1.5">
                                                        <i class="fa-solid fa-paperclip"></i> Download File
                                                    </a>
                                                @endif
                                            </div>
                                        @endif

                                        <div class="text-[10px] text-slate-400 text-right pt-1">
                                            {{ $reply->created_at ? \Carbon\Carbon::parse($reply->created_at)->format('h:i A') : '' }}
                                        </div>
                                    </div>
                                </div>
                            @endif
                        @endforeach
                    @endif

                </div>

                <!-- Bottom Chat Input Bar -->
                <div class="p-4 bg-white border-t border-slate-200">
                    <form action="{{ route('admin.messages.reply', $activeId) }}" method="POST" enctype="multipart/form-data" class="flex flex-col gap-2">
                        @csrf
                        
                        <!-- File Preview Name Indicator -->
                        <div id="filePreviewContainer" class="hidden items-center justify-between bg-orange-50 px-3 py-1.5 rounded-lg border border-orange-200 text-xs text-orange-700">
                            <span class="truncate flex items-center gap-1.5">
                                <i class="fa-solid fa-image"></i>
                                <span id="fileNameDisplay">Selected file</span>
                            </span>
                            <button type="button" id="removeFileBtn" class="text-orange-500 hover:text-red-500 ml-2 font-bold">&times;</button>
                        </div>

                        <div class="flex items-center gap-2">
                            <!-- Attachment Button -->
                            <label class="p-2.5 text-slate-400 hover:text-orange-500 hover:bg-slate-100 rounded-xl transition cursor-pointer" title="Attach file or image">
                                <i class="fa-solid fa-paperclip text-lg"></i>
                                <input type="file" id="attachmentInput" name="attachment" accept="image/*,.pdf,.doc,.docx" class="hidden">
                            </label>

                            <!-- Input Message Field -->
                            <input type="text" name="message" required placeholder="Type a message..." 
                                   class="flex-1 bg-slate-100 border border-transparent focus:border-orange-500 focus:bg-white rounded-xl px-4 py-2.5 text-xs md:text-sm text-slate-800 outline-none transition">

                            <!-- Send Button -->
                            <button type="submit" class="bg-orange-500 hover:bg-orange-600 text-white px-5 py-2.5 rounded-xl text-xs font-bold transition shadow-md flex items-center gap-2 cursor-pointer shrink-0">
                                <span>Send</span>
                                <i class="fa-solid fa-paper-plane text-[11px]"></i>
                            </button>
                        </div>
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

<!-- Client-side Script for Realtime Search & File Preview -->
<script>
document.addEventListener('DOMContentLoaded', function () {
    // 1. Live Filter Search Bar
    const searchInput = document.getElementById('chatSearchInput');
    const chatItems = document.querySelectorAll('.chat-item');

    if (searchInput) {
        searchInput.addEventListener('input', function (e) {
            const query = e.target.value.toLowerCase().trim();
            chatItems.forEach(item => {
                const searchData = item.getAttribute('data-search') || '';
                if (searchData.includes(query)) {
                    item.style.display = 'flex';
                } else {
                    item.style.display = 'none';
                }
            });
        });
    }

    // 2. File Attachment Preview Indicator
    const fileInput = document.getElementById('attachmentInput');
    const filePreviewContainer = document.getElementById('filePreviewContainer');
    const fileNameDisplay = document.getElementById('fileNameDisplay');
    const removeFileBtn = document.getElementById('removeFileBtn');

    if (fileInput) {
        fileInput.addEventListener('change', function () {
            if (this.files && this.files[0]) {
                fileNameDisplay.textContent = this.files[0].name;
                filePreviewContainer.classList.remove('hidden');
                filePreviewContainer.classList.add('flex');
            }
        });

        if (removeFileBtn) {
            removeFileBtn.addEventListener('click', function () {
                fileInput.value = '';
                filePreviewContainer.classList.add('hidden');
                filePreviewContainer.classList.remove('flex');
            });
        }
    }
});
</script>
@endsection