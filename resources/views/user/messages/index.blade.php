@extends('layouts.app')

@section('title', 'Support Message')

@section('content')

<style>
    /* Styling Scrollbar */
    .custom-scrollbar::-webkit-scrollbar {
        width: 6px;
    }
    .custom-scrollbar::-webkit-scrollbar-track {
        background: transparent;
    }
    .custom-scrollbar::-webkit-scrollbar-thumb {
        background: #cbd5e1;
        border-radius: 9999px;
    }
    .custom-scrollbar::-webkit-scrollbar-thumb:hover {
        background: #94a3b8;
    }
</style>

<div class="h-[85vh] flex flex-col">

    <!-- MAIN CHAT CONTAINER (Responsive Layout) -->
    <div class="flex-1 bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden flex flex-col md:flex-row h-full">

        @php
            $chatList = $conversations ?? $tickets ?? [];
            $hasSelectedTicket = request()->has('ticket_id') || (isset($activeTicket) && $activeTicket);
            $currentSelectedId = request('ticket_id') ?? ($activeTicket->ticket_id ?? $activeTicket->id ?? null);
        @endphp

        <!-- LEFT PANEL: Conversation List -->
        <div class="w-full md:w-80 lg:w-96 border-b md:border-b-0 md:border-r border-slate-200 bg-white flex-col shrink-0 h-full {{ $hasSelectedTicket ? 'hidden md:flex' : 'flex' }}">
            
            <!-- Top Header & Filter Bar (Fixed Header) -->
            <div class="p-4 border-b border-slate-100 space-y-3 shrink-0">
                <!-- Total Tickets & Sort Dropdown -->
                <div class="flex items-center justify-between">
                    <h3 class="font-bold text-slate-800 text-sm">
                        Total ticket <span class="text-slate-500 font-semibold ml-1">{{ count($chatList) }}</span>
                    </h3>

                    <!-- Dropdown Sort -->
                    <div class="relative">
                        <select onchange="location = this.value;" class="text-xs font-semibold text-slate-600 bg-slate-50 border border-slate-200 rounded-xl px-2.5 py-1.5 outline-none cursor-pointer hover:bg-slate-100 transition">
                            <option value="{{ route('messages.index', array_merge(request()->query(), ['sort' => 'newest'])) }}" {{ request('sort') != 'oldest' ? 'selected' : '' }}>Newest</option>
                            <option value="{{ route('messages.index', array_merge(request()->query(), ['sort' => 'oldest'])) }}" {{ request('sort') == 'oldest' ? 'selected' : '' }}>Oldest</option>
                        </select>
                    </div>
                </div>

                <!-- Search Bar -->
                <form action="{{ route('messages.index') }}" method="GET" class="relative">
                    @if(request('status'))
                        <input type="hidden" name="status" value="{{ request('status') }}">
                    @endif
                    <i class="fa-solid fa-magnifying-glass absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                    <input type="text" id="chatSearchInput" name="search" value="{{ request('search') }}" placeholder="Search..." 
                        class="w-full pl-9 pr-4 py-2 bg-slate-100/80 rounded-xl text-xs text-slate-800 placeholder-slate-400 border border-transparent focus:border-orange-500 focus:bg-white outline-none transition">
                </form>
                
                <!-- Filter Status Buttons -->
                <div class="flex items-center gap-1.5 text-xs pt-1 overflow-x-auto">
                    <a href="{{ route('messages.index', request()->only('search')) }}" 
                    class="px-3 py-1.5 rounded-xl font-bold cursor-pointer transition whitespace-nowrap {{ !request('status') ? 'bg-orange-500 text-white shadow-2xs' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                    All
                    </a>
                    <a href="{{ route('messages.index', array_merge(request()->only('search'), ['status' => 'Open'])) }}" 
                    class="px-3 py-1.5 rounded-xl font-semibold cursor-pointer transition whitespace-nowrap {{ request('status') == 'Open' ? 'bg-orange-500 text-white shadow-2xs' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                    Open Ticket
                    </a>
                    <a href="{{ route('messages.index', array_merge(request()->only('search'), ['status' => 'Closed'])) }}" 
                    class="px-3 py-1.5 rounded-xl font-semibold cursor-pointer transition whitespace-nowrap {{ request('status') == 'Closed' ? 'bg-orange-500 text-white shadow-2xs' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                    Close Ticket
                    </a>
                </div>
            </div>

            <!-- Conversations Scrollable List -->
            <div class="flex-1 overflow-y-auto custom-scrollbar divide-y divide-slate-100" id="chatListContainer">
                @forelse ($chatList as $index => $conv)
                    @php 
                        $convId = $conv->ticket_id ?? $conv->id;
                        
                        if (request('ticket_id')) {
                            $isActive = ((string)$currentSelectedId === (string)$convId);
                        } else {
                            $isActive = ($index === 0) || ((string)$currentSelectedId === (string)$convId);
                        }
                        
                        $ticketTitle = $conv->title ?? $conv->subject ?? 'Ticket';
                        $ticketStatus = $conv->status ?? 'New';
                        
                        $formattedNo = '#TC-' . $convId;

                        if (!empty($conv->created_at)) {
                            $formattedTime = is_string($conv->created_at) 
                                ? \Carbon\Carbon::parse($conv->created_at)->format('d M, h:i a') 
                                : $conv->created_at->format('d M, h:i a');
                        } else {
                            $formattedTime = $conv->date ?? '';
                        }

                        $statusClasses = [
                            'New'        => 'bg-emerald-100 text-emerald-700 border-emerald-200',
                            'Processing' => 'bg-blue-100 text-blue-700 border-blue-200',
                            'Pending'    => 'bg-amber-100 text-amber-700 border-amber-200',
                            'Close'      => 'bg-red-100 text-red-700 border-red-200',
                            'Closed'     => 'bg-red-100 text-red-700 border-red-200',
                        ];
                        $badgeStyle = $statusClasses[$ticketStatus] ?? 'bg-slate-100 text-slate-700 border-slate-200';
                    @endphp

                    <a href="{{ route('messages.index', array_merge(request()->only(['search', 'status', 'sort']), ['ticket_id' => $convId])) }}" 
                    class="chat-item relative flex items-start gap-2.5 p-3.5 pl-0 hover:bg-slate-50 transition cursor-pointer {{ $isActive ? 'bg-slate-100/90' : '' }}"
                    data-search="{{ strtolower($ticketTitle . ' ' . $convId) }}">
                        
                        <div class="w-1.5 self-stretch shrink-0 {{ $isActive ? 'bg-orange-500 rounded-r-md' : 'bg-transparent' }}"></div>

                        <!-- Icon Avatar -->
                        <div class="relative shrink-0 mt-0.5">
                            <div class="w-9 h-9 rounded-full bg-slate-400 text-white font-bold flex items-center justify-center text-xs shadow-2xs">
                                <i class="fa-solid fa-diamond text-[10px]"></i>
                            </div>
                        </div>

                        <!-- Chat Details Layout -->
                        <div class="flex-1 min-w-0 space-y-1 pr-2">
                            <div class="flex items-center justify-between">
                                <span class="text-xs font-extrabold text-slate-900 tracking-tight">
                                    {{ $formattedNo }}
                                </span>
                                <span class="text-[10px] text-slate-400 font-medium">
                                    {{ $formattedTime }}
                                </span>
                            </div>

                            <div class="flex items-center justify-between gap-2 pt-0.5">
                                <p class="text-xs text-slate-400 truncate font-normal flex-1">
                                    {{ $ticketTitle }}
                                </p>

                                <span class="px-2 py-0.5 text-[10px] font-bold rounded-md border shrink-0 {{ $badgeStyle }}">
                                    {{ $ticketStatus }}
                                </span>
                            </div>
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

        <!-- RIGHT PANEL: Active Chat Area -->
        <div class="flex-1 bg-white flex-col justify-between min-w-0 h-full {{ $hasSelectedTicket ? 'flex' : 'hidden md:flex' }}">
            
            @if($activeTicket)
                @php
                    $activeId = $activeTicket->ticket_id ?? $activeTicket->id;
                    $ticketMainAttachment = $activeTicket->image ?? $activeTicket->image_path ?? null;

                    $activeDisplayNo = '#TC-' . $activeId;
                    $isTicketClosed = in_array($activeTicket->status ?? '', ['Close', 'Closed']);
                @endphp

                <!-- Active Chat Header -->
                <div class="px-4 md:px-6 py-3.5 bg-white border-b border-slate-200 flex items-center justify-between shadow-2xs shrink-0">
                    <div class="flex items-center gap-3 w-full min-w-0">
                        
                        <a href="{{ route('tickets.index', request()->only(['search', 'status', 'sort'])) }}" 
                           class="md:hidden p-2 -ml-1 text-slate-500 hover:text-orange-500 hover:bg-slate-100 rounded-xl transition"
                           title="Back to Ticket List">
                            <i class="fa-solid fa-arrow-left text-base"></i>
                        </a>

                        <div class="w-9 h-9 md:w-10 md:h-10 rounded-full bg-orange-500 text-white font-bold flex items-center justify-center text-xs md:text-sm shrink-0 shadow-xs">
                            <i class="fa-solid fa-headset"></i>
                        </div>
                        
                        <!-- Header Title & Status -->
                        <div class="flex-1 flex items-center justify-between gap-2 min-w-0">
                            <div class="min-w-0">
                                <h6 class="text-xs md:text-base font-bold text-slate-800 truncate">
                                    {{ $activeDisplayNo }}
                                </h6>
                                <p class="text-[11px] md:text-xs text-slate-500 font-medium truncate">
                                    {{ $activeTicket->title }}
                                </p>
                            </div>

                            <span class="px-2.5 py-0.5 md:px-3 md:py-1 rounded-full font-bold text-[10px] md:text-xs shrink-0 shadow-2xs
                                @if(($activeTicket->status ?? '') == 'New') bg-emerald-100 text-emerald-700 border border-emerald-200
                                @elseif(($activeTicket->status ?? '') == 'Processing') bg-blue-100 text-blue-700 border border-blue-200
                                @elseif(($activeTicket->status ?? '') == 'Pending') bg-amber-100 text-amber-700 border border-amber-200
                                @elseif($isTicketClosed) bg-red-100 text-red-700 border border-red-200
                                @else bg-slate-100 text-slate-700 border border-slate-200 @endif">
                                {{ $activeTicket->status }}
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Chat Messages Scroll Area -->
                <div class="flex-1 p-4 md:p-6 overflow-y-auto custom-scrollbar space-y-4 bg-white" id="messageScrollContainer">
                    
                    <div class="flex items-end justify-end gap-2.5 max-w-[90%] md:max-w-[70%] ml-auto">
                        <div class="bg-orange-500 text-white p-3 md:p-3.5 px-3.5 md:px-4 rounded-2xl rounded-br-xs space-y-1">
                            <div class="text-[11px] md:text-xs font-bold text-orange-100">
                                {{ Auth::user()->full_name ?? Auth::user()->name }} (You)
                            </div>
                            <div class="text-xs md:text-sm leading-relaxed">
                                {!! $activeTicket->description !!}
                            </div>

                            @if(!empty($ticketMainAttachment) && $ticketMainAttachment !== 'no-image.png')
                                @php
                                    $mainExt = strtolower(pathinfo($ticketMainAttachment, PATHINFO_EXTENSION));
                                    $isMainImg = in_array($mainExt, ['jpg', 'jpeg', 'png', 'pdf']);
                                @endphp
                                <div class="mt-2 pt-2 border-t border-orange-200/60">
                                    @if($isMainImg)
                                        <a href="{{ asset('storage/' . $ticketMainAttachment) }}" target="_blank" class="block group">
                                            <img src="{{ asset('storage/' . $ticketMainAttachment) }}" alt="Ticket Attachment" class="max-w-xs max-h-48 md:max-h-56 rounded-xl border border-orange-300 object-cover shadow-2xs group-hover:opacity-95 transition">
                                        </a>
                                    @else
                                        <a href="{{ asset('storage/' . $ticketMainAttachment) }}" target="_blank" class="text-xs text-white underline flex items-center gap-1.5 font-medium">
                                            <i class="fa-solid fa-paperclip"></i> View Attachment
                                        </a>
                                    @endif
                                </div>
                            @endif

                            <div class="text-[10px] text-orange-100 font-medium pt-1 text-right">
                                {{ $activeTicket->created_at 
                                    ? (is_string($activeTicket->created_at) 
                                        ? \Carbon\Carbon::parse($activeTicket->created_at)->format('d M Y, h:i A') 
                                        : $activeTicket->created_at->format('d M Y, h:i A')) 
                                    : '' }}
                            </div>
                        </div>

                        <div class="w-7 h-7 md:w-8 md:h-8 rounded-full bg-slate-900 text-white text-[10px] md:text-xs font-bold flex items-center justify-center shrink-0 shadow-xs mb-1">
                            {{ strtoupper(substr(Auth::user()->name ?? 'U', 0, 1)) }}
                        </div>
                    </div>

                    @if(isset($activeTicket->replies) && $activeTicket->replies->count() > 0)
                        @foreach($activeTicket->replies as $reply)
                            @php
                                $replyName = $reply->sender_name ?? $reply->user_name ?? ($reply->user->name ?? 'Admin');
                                $isMe = ($reply->user_id ?? null) == Auth::id();
                                
                                $isSystemStatus = str_starts_with($reply->message, 'STATUS_CHANGE:');
                                $isSystemPic    = str_starts_with($reply->message, 'PIC_CHANGE:');

                                $attachment = $reply->attachment ?? null;
                                $ext = $attachment ? strtolower(pathinfo($attachment, PATHINFO_EXTENSION)) : '';
                                $isImage = in_array($ext, ['jpg', 'jpeg', 'png', 'pdf']);
                            @endphp

                            @if($isSystemPic)
                                @php
                                    $picAssignedName = str_replace('PIC_CHANGE:', '', $reply->message);
                                @endphp
                                <div class="flex items-center justify-center gap-2 py-2 my-1 text-[11px] md:text-xs text-slate-400 font-medium text-center">
                                    <div class="w-5 h-5 md:w-6 md:h-6 rounded-full bg-slate-600 text-white flex items-center justify-center text-[9px] md:text-[10px] shrink-0 shadow-2xs">
                                        <i class="fa-solid fa-user-check"></i>
                                    </div>
                                    <span>Agent <strong class="text-slate-700 font-bold">{{ $picAssignedName }}</strong> has taken over</span>
                                </div>

                            @elseif($isSystemStatus)
                                @php
                                    $statusValue = str_replace('STATUS_CHANGE:', '', $reply->message);
                                    $statusClasses = [
                                        'New'        => 'bg-emerald-100 text-emerald-700',
                                        'Processing' => 'bg-blue-100 text-blue-700',
                                        'Pending'    => 'bg-amber-100 text-amber-700',
                                        'Close'      => 'bg-red-100 text-red-700',
                                        'Closed'     => 'bg-red-100 text-red-700',
                                    ];
                                    $badgeClass = $statusClasses[$statusValue] ?? 'bg-slate-100 text-slate-700';
                                @endphp
                                <div class="flex items-center justify-center gap-1.5 md:gap-2 py-2 my-1 text-[11px] md:text-xs text-slate-400 font-medium">
                                    <div class="w-5 h-5 md:w-6 md:h-6 rounded-full bg-blue-500 text-white flex items-center justify-center text-[9px] md:text-[10px] shrink-0 shadow-2xs">
                                        <i class="fa-solid fa-sliders"></i>
                                    </div>
                                    <span>Status updated to</span>
                                    <span class="px-2 py-0.5 rounded-full font-bold text-[10px] md:text-[11px] {{ $badgeClass }}">
                                        {{ $statusValue }}
                                    </span>
                                </div>

                            @elseif($isMe)
                                <div class="flex items-end justify-end gap-2.5 max-w-[90%] md:max-w-[70%] ml-auto">
                                    <div class="bg-orange-500 text-white p-3 md:p-3.5 px-3.5 md:px-4 rounded-2xl rounded-br-xs space-y-1">
                                        <div class="text-[11px] md:text-xs font-bold text-orange-100">
                                            You
                                        </div>
                                        <div class="text-xs md:text-sm leading-relaxed">
                                            {!! $reply->message !!}
                                        </div>

                                        @if(!empty($attachment))
                                            <div class="mt-2 pt-2 border-t border-orange-400/60">
                                                @if($isImage)
                                                    <a href="{{ asset('storage/' . $attachment) }}" target="_blank" class="block group">
                                                        <img src="{{ asset('storage/' . $attachment) }}" alt="Attachment" class="max-w-xs max-h-48 md:max-h-56 rounded-xl border border-orange-300 object-cover shadow-2xs group-hover:opacity-90 transition">
                                                    </a>
                                                @else
                                                    <a href="{{ asset('storage/' . $attachment) }}" target="_blank" class="text-xs text-white underline flex items-center gap-1.5">
                                                        <i class="fa-solid fa-paperclip"></i> Download File
                                                    </a>
                                                @endif
                                            </div>
                                        @endif

                                        <div class="text-[10px] text-orange-100 font-medium pt-1 text-right">
                                            {{ $reply->created_at ? \Carbon\Carbon::parse($reply->created_at)->format('d M Y, h:i A') : '' }}
                                        </div>
                                    </div>

                                    <div class="w-7 h-7 md:w-8 md:h-8 rounded-full bg-slate-900 text-white text-[10px] md:text-xs font-bold flex items-center justify-center shrink-0 shadow-xs mb-1">
                                        {{ strtoupper(substr(Auth::user()->name ?? 'U', 0, 1)) }}
                                    </div>
                                </div>

                            @else
                                <div class="flex items-end gap-2.5 max-w-[90%] md:max-w-[70%]">
                                    <div class="w-7 h-7 md:w-8 md:h-8 rounded-full bg-blue-600 text-white text-[10px] md:text-xs font-bold flex items-center justify-center shrink-0 shadow-xs mb-1">
                                        <i class="fa-solid fa-user-shield text-[10px]"></i>
                                    </div>

                                    <div class="bg-blue-50/90 text-slate-800 p-3 md:p-3.5 px-3.5 md:px-4 rounded-2xl rounded-bl-xs space-y-1">
                                        <div class="text-[11px] md:text-xs font-bold text-blue-900">
                                            {{ $replyName ?? '' }}{{ isset($adminDept) && $adminDept ? ' - ' . $adminDept : '' }}
                                        </div>
                                        <div class="text-xs md:text-sm leading-relaxed text-slate-700">
                                            {!! $reply->message !!}
                                        </div>

                                        @if(!empty($attachment))
                                            <div class="mt-2 pt-2 border-t border-blue-200/60">
                                                @if($isImage)
                                                    <a href="{{ asset('storage/' . $attachment) }}" target="_blank" class="block group">
                                                        <img src="{{ asset('storage/' . $attachment) }}" alt="Attachment" class="max-w-xs max-h-48 md:max-h-56 rounded-xl border border-blue-200 object-cover shadow-2xs group-hover:opacity-90 transition">
                                                    </a>
                                                @else
                                                    <a href="{{ asset('storage/' . $attachment) }}" target="_blank" class="text-xs text-blue-600 hover:underline flex items-center gap-1.5 font-medium">
                                                        <i class="fa-solid fa-paperclip"></i> Download File
                                                    </a>
                                                @endif
                                            </div>
                                        @endif

                                        <div class="text-[10px] text-slate-400 font-medium pt-1">
                                            {{ $reply->created_at ? \Carbon\Carbon::parse($reply->created_at)->format('d M Y, h:i A') : '' }}
                                        </div>
                                    </div>
                                </div>
                            @endif
                        @endforeach
                    @endif
                </div>

                <!-- Bottom Chat Input Bar -->
                <div class="p-3 md:p-4 bg-white border-t border-slate-200 shrink-0">
                    @if($isTicketClosed)
                        <div class="p-3 bg-slate-100 rounded-xl text-center text-xs font-semibold text-slate-500 flex items-center justify-center gap-2 border border-slate-200">
                            <i class="fa-solid fa-lock text-slate-400"></i>
                            <span>This ticket has been closed. Replies are disabled.</span>
                        </div>
                    @else
                        <form action="{{ route('messages.reply', $activeId) }}" method="POST" enctype="multipart/form-data" class="flex flex-col gap-2">
                            @csrf
                            
                            <!-- File Preview Container -->
                            <div id="filePreviewContainer" class="hidden items-center justify-between bg-orange-50 px-3 py-1.5 rounded-xl border border-orange-200 text-xs text-orange-700">
                                <div class="flex items-center gap-2 truncate pr-2">
                                    <i id="attachmentTypeIcon" class="fa-solid fa-paperclip text-orange-500"></i>
                                    <span id="fileNameDisplay" class="font-semibold truncate">Selected file</span>
                                </div>
                                <button type="button" id="removeFileBtn" class="px-2 py-0.5 bg-red-100 hover:bg-red-200 text-red-600 rounded-lg text-xs font-bold transition shrink-0 cursor-pointer">
                                    <i class="fa-solid fa-xmark mr-1"></i> Cancel
                                </button>
                            </div>

                            <div class="flex items-center gap-2">
                                <label class="p-2 md:p-2.5 text-slate-400 hover:text-orange-500 hover:bg-slate-100 rounded-xl transition cursor-pointer shrink-0" title="Attach image or PDF">
                                    <i class="fa-solid fa-paperclip text-base md:text-lg"></i>
                                    <input type="file" id="attachmentInput" name="attachment" accept=".jpg,.jpeg,.png,.pdf" class="hidden">
                                </label>

                                <input type="text" name="message" required placeholder="Type a message..." 
                                       class="flex-1 bg-slate-100 border border-transparent focus:border-orange-500 focus:bg-white rounded-xl px-3.5 py-2 md:px-4 md:py-2.5 text-xs md:text-sm text-slate-800 outline-none transition">

                                <button type="submit" class="bg-orange-500 hover:bg-orange-600 text-white px-4 md:px-5 py-2 md:py-2.5 rounded-xl text-xs font-bold transition shadow-md flex items-center gap-1.5 cursor-pointer shrink-0">
                                    <span>Send</span>
                                    <i class="fa-solid fa-paper-plane text-[10px] md:text-[11px]"></i>
                                </button>
                            </div>
                        </form>
                    @endif
                </div>

            @else
                <div class="flex-1 flex flex-col items-center justify-center p-8 text-center">
                    <div class="w-16 h-16 rounded-full bg-orange-100 text-orange-500 flex items-center justify-center text-2xl mb-3 shadow-inner">
                        <i class="fa-solid fa-comments"></i>
                    </div>
                    <h3 class="text-base font-bold text-slate-800 mb-1">Select a Support Ticket</h3>
                    <p class="text-xs text-slate-500 max-w-xs">Choose a ticket from list to communicate with support team.</p>
                </div>
            @endif

        </div>

    </div>

</div>

<!-- Client-side Script -->
<script>
document.addEventListener('DOMContentLoaded', function () {

    const msgContainer = document.getElementById('messageScrollContainer');
    if (msgContainer) {
        msgContainer.scrollTop = msgContainer.scrollHeight;
    }

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

    const fileInput = document.getElementById('attachmentInput');
    const filePreviewContainer = document.getElementById('filePreviewContainer');
    const fileNameDisplay = document.getElementById('fileNameDisplay');
    const attachmentTypeIcon = document.getElementById('attachmentTypeIcon');
    const removeFileBtn = document.getElementById('removeFileBtn');

    if (fileInput) {
        fileInput.addEventListener('change', function () {
            if (this.files && this.files[0]) {
                const file = this.files[0];
                const fileName = file.name;
                const ext = fileName.split('.').pop().toLowerCase();
                const allowedExtensions = ['jpg', 'jpeg', 'png', 'pdf'];

                if (!allowedExtensions.includes(ext)) {
                    alert('Format fail tidak dibenarkan! Sila pilih fail JPG, JPEG, PNG, atau PDF sahaja.');
                    this.value = '';
                    filePreviewContainer.classList.add('hidden');
                    filePreviewContainer.classList.remove('flex');
                    return;
                }

                if (ext === 'pdf') {
                    attachmentTypeIcon.className = 'fa-solid fa-file-pdf text-red-500';
                } else {
                    attachmentTypeIcon.className = 'fa-solid fa-image text-orange-500';
                }

                fileNameDisplay.textContent = fileName;
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