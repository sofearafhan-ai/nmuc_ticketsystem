@extends('layouts.admin')

@section('title', 'Admin - Message')

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

<div class="h-[82vh] flex flex-col">

    <!-- MAIN CHAT CONTAINER (2 Columns Layout) -->
    <div class="flex-1 bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden flex flex-col md:flex-row h-full">

        <!-- LEFT PANEL: Conversation List (Width 1/3) -->
        <div class="w-full md:w-80 lg:w-96 border-b md:border-b-0 md:border-r border-slate-200 bg-white flex flex-col shrink-0 h-full">
            
            <!-- Top Header & Filter Bar (Fixed Header) -->
            <div class="p-4 border-b border-slate-100 space-y-3 shrink-0">
                <!-- Total Tickets & Sort Dropdown -->
                <div class="flex items-center justify-between">
                    <h3 class="font-bold text-slate-800 text-sm">
                        Total ticket <span class="text-slate-500 font-semibold ml-1">{{ count($conversations ?? []) }}</span>
                    </h3>

                    <!-- Dropdown Sort -->
                    <div class="relative">
                        <select onchange="location = this.value;" class="text-xs font-semibold text-slate-600 bg-slate-50 border border-slate-200 rounded-xl px-2.5 py-1.5 outline-none cursor-pointer hover:bg-slate-100 transition">
                            <option value="{{ route('admin.messages.index', array_merge(request()->query(), ['sort' => 'newest'])) }}" {{ request('sort') != 'oldest' ? 'selected' : '' }}>Newest</option>
                            <option value="{{ route('admin.messages.index', array_merge(request()->query(), ['sort' => 'oldest'])) }}" {{ request('sort') == 'oldest' ? 'selected' : '' }}>Oldest</option>
                        </select>
                    </div>
                </div>

                <!-- Search Bar -->
                <form action="{{ route('admin.messages.index') }}" method="GET" class="relative">
                    @if(request('status'))
                        <input type="hidden" name="status" value="{{ request('status') }}">
                    @endif
                    <i class="fa-solid fa-magnifying-glass absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                    <input type="text" id="chatSearchInput" name="search" value="{{ request('search') }}" placeholder="Search..." 
                        class="w-full pl-9 pr-4 py-2 bg-slate-100/80 rounded-xl text-xs text-slate-800 placeholder-slate-400 border border-transparent focus:border-orange-500 focus:bg-white outline-none transition">
                </form>
                
                <!-- Filter Status Buttons -->
                <div class="flex items-center gap-1.5 text-xs pt-1">
                    <a href="{{ route('admin.messages.index', request()->only('search')) }}" 
                    class="px-3 py-1.5 rounded-xl font-bold cursor-pointer transition {{ !request('status') ? 'bg-orange-500 text-white shadow-2xs' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                    All
                    </a>
                    <a href="{{ route('admin.messages.index', array_merge(request()->only('search'), ['status' => 'Open'])) }}" 
                    class="px-3 py-1.5 rounded-xl font-semibold cursor-pointer transition {{ request('status') == 'Open' ? 'bg-orange-500 text-white shadow-2xs' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                    Open Ticket
                    </a>
                    <a href="{{ route('admin.messages.index', array_merge(request()->only('search'), ['status' => 'Closed'])) }}" 
                    class="px-3 py-1.5 rounded-xl font-semibold cursor-pointer transition {{ request('status') == 'Closed' ? 'bg-orange-500 text-white shadow-2xs' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                    Closed Ticket
                    </a>
                </div>
            </div>

            <!-- Conversations Scrollable List dengan SCROLLBAR -->
            <div class="flex-1 overflow-y-auto custom-scrollbar divide-y divide-slate-100" id="chatListContainer">
                @php
                    $chatList = $conversations ?? $tickets ?? [];
                    $currentSelectedId = request('ticket_id') ?? ($activeTicket->ticket_id ?? $activeTicket->id ?? null);
                @endphp

                @forelse ($chatList as $index => $conv)
                    @php 
                        $convId = $conv->ticket_id ?? $conv->id;
                        
                        if (request('ticket_id')) {
                            $isActive = ((string)$currentSelectedId === (string)$convId);
                        } else {
                            $isActive = ($index === 0) || ((string)$currentSelectedId === (string)$convId);
                        }
                        
                        $userName = $conv->user_name ?? $conv->user->name ?? 'User';
                        $ticketTitle = $conv->title ?? $conv->subject ?? 'Ticket';
                        $ticketStatus = $conv->status ?? 'New';
                        
                        $displayIndex = (count($chatList) - $index);
                        $formattedNo = '#TC-' . str_pad($displayIndex, 4, '0', STR_PAD_LEFT);

                        if (!empty($conv->created_at)) {
                            $formattedTime = is_string($conv->created_at) 
                                ? \Carbon\Carbon::parse($conv->created_at)->format('h:i a') 
                                : $conv->created_at->format('h:i a');
                        } else {
                            $formattedTime = $conv->date ?? '';
                        }

                        // Warna Lencana Status
                        $statusClasses = [
                            'New'        => 'bg-emerald-100 text-emerald-700 border-emerald-200',
                            'Processing' => 'bg-blue-100 text-blue-700 border-blue-200',
                            'Pending'    => 'bg-amber-100 text-amber-700 border-amber-200',
                            'Close'      => 'bg-red-100 text-red-700 border-red-200',
                            'Closed'     => 'bg-red-100 text-red-700 border-red-200',
                        ];
                        $badgeStyle = $statusClasses[$ticketStatus] ?? 'bg-slate-100 text-slate-700 border-slate-200';
                    @endphp

                    <a href="{{ route('admin.messages.index', array_merge(request()->only(['search', 'status', 'sort']), ['ticket_id' => $convId])) }}" 
                    class="chat-item relative flex items-start gap-2.5 p-3.5 pl-0 hover:bg-slate-50 transition cursor-pointer {{ $isActive ? 'bg-slate-100/90' : '' }}"
                    data-search="{{ strtolower($userName . ' ' . $ticketTitle . ' ' . $convId) }}">
                        
                        <!-- GARISAN HITAM TEPI -->
                        <div class="w-1.5 self-stretch shrink-0 {{ $isActive ? 'bg-slate-900 rounded-r-md' : 'bg-transparent' }}"></div>

                        <!-- Avatar -->
                        <div class="relative shrink-0 mt-0.5">
                            <div class="w-9 h-9 rounded-full bg-slate-400 text-white font-bold flex items-center justify-center text-xs shadow-2xs">
                                <i class="fa-solid fa-diamond text-[10px]"></i>
                            </div>
                        </div>

                        <!-- Chat Details Layout -->
                        <div class="flex-1 min-w-0 space-y-1 pr-2">
                            <!-- Baris 1: ID Tiket & Masa -->
                            <div class="flex items-center justify-between">
                                <span class="text-xs font-extrabold text-slate-900 tracking-tight">
                                    {{ $formattedNo }}
                                </span>
                                <span class="text-[10px] text-slate-400 font-medium">
                                    {{ $formattedTime }}
                                </span>
                            </div>

                            <!-- Baris 2: Nama Pengguna -->
                            <div class="flex items-center justify-between">
                                <h4 class="text-xs font-bold text-slate-700 truncate">
                                    {{ $userName }}
                                </h4>
                            </div>

                            <!-- Baris 3: Tajuk Tiket & Lencana Status di Kanan Bawah -->
                            <div class="flex items-center justify-between gap-2 pt-0.5">
                                <p class="text-xs text-slate-400 truncate font-normal flex-1">
                                    {{ $ticketTitle }}
                                </p>

                                <!-- Lencana Status Tiket (Kanan Bawah) -->
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

        <!-- RIGHT PANEL: Active Chat Area (Width 2/3) -->
        <div class="flex-1 bg-white flex flex-col justify-between min-w-0 h-full">
            
            @if($activeTicket)
                @php
                    $activeId = $activeTicket->ticket_id ?? $activeTicket->id;
                    $activeUserName = $activeTicket->user_name ?? $activeTicket->user->name ?? 'User';
                    $ticketMainAttachment = $activeTicket->image ?? $activeTicket->image_path ?? null;

                    $activeIndex = 0;
                    foreach($chatList as $idx => $c) {
                        if(($c->ticket_id ?? $c->id) == $activeId) {
                            $activeIndex = $idx;
                            break;
                        }
                    }
                    $activeDisplayIndex = (count($chatList) - $activeIndex);
                    $activeDisplayNo = '#TC-' . str_pad($activeDisplayIndex, 4, '0', STR_PAD_LEFT);
                @endphp

                <!-- Active Chat Header (Fixed Header) -->
                <div class="px-6 py-4 bg-white border-b border-slate-200 flex items-center justify-between shadow-2xs shrink-0">
                    <div class="flex items-center gap-3 min-w-0">
                        <div class="w-10 h-10 rounded-full bg-emerald-500 text-white font-bold flex items-center justify-center text-sm shrink-0 shadow-xs">
                            {{ strtoupper(substr($activeUserName, 0, 1)) }}
                        </div>
                        
                        <div class="flex items-center gap-2 min-w-0">
                            <h6 class="text-base font-bold text-slate-800 truncate">
                                {{ $activeDisplayNo }}
                                <br>
                                {{ $activeTicket->title }}
                            </h6>
                            <span class="text-slate-400">•</span>
                            <span class="px-2.5 py-1 rounded-full font-bold text-xs
                                @if(($activeTicket->status ?? '') == 'New') bg-emerald-100 text-emerald-700
                                @elseif(($activeTicket->status ?? '') == 'Processing') bg-blue-100 text-blue-700
                                @elseif(($activeTicket->status ?? '') == 'Pending') bg-amber-100 text-amber-700
                                @elseif(($activeTicket->status ?? '') == 'Close' || ($activeTicket->status ?? '') == 'Closed') bg-red-100 text-red-700
                                @else bg-slate-100 text-slate-700 @endif">
                                {{ $activeTicket->status }}
                            </span>
                        </div>
                    </div>

                    <div class="flex items-center gap-3">
                        <a href="{{ route('admin.tickets.edit', $activeId) }}" 
                           class="inline-flex items-center gap-1.5 px-3.5 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold rounded-xl transition">
                            <span>View Detail</span>
                            <i class="fa-solid fa-arrow-up-right-from-square text-[10px]"></i>
                        </a>
                    </div>
                </div>

                <!-- Chat Messages Scroll Area dengan SCROLLBAR -->
                <div class="flex-1 p-5 md:p-6 overflow-y-auto custom-scrollbar space-y-4 bg-white" id="messageScrollContainer">
                    
                    <!-- 1. First Ticket Message (Submission oleh User) -->
                    <div class="flex items-end gap-2.5 max-w-[85%] md:max-w-[70%]">
                        <div class="w-8 h-8 rounded-full bg-emerald-500 text-white text-xs font-bold flex items-center justify-center shrink-0 shadow-xs mb-1">
                            {{ strtoupper(substr($activeUserName, 0, 1)) }}
                        </div>

                        <div class="bg-slate-100 text-slate-800 p-3.5 px-4 rounded-2xl rounded-bl-xs space-y-1">
                            <div class="text-xs font-bold text-slate-900">
                                {{ $activeUserName }}
                            </div>
                            <div class="text-xs md:text-sm leading-relaxed">
                                {!! $activeTicket->description !!}
                            </div>

                            @if(!empty($ticketMainAttachment) && $ticketMainAttachment !== 'no-image.png')
                                @php
                                    $mainExt = strtolower(pathinfo($ticketMainAttachment, PATHINFO_EXTENSION));
                                    $isMainImg = in_array($mainExt, ['jpg', 'jpeg', 'png', 'gif', 'webp']);
                                @endphp
                                <div class="mt-2 pt-2 border-t border-slate-200/60">
                                    @if($isMainImg)
                                        <a href="{{ asset('storage/' . $ticketMainAttachment) }}" target="_blank" class="block group">
                                            <img src="{{ asset('storage/' . $ticketMainAttachment) }}" alt="Ticket Attachment" class="max-w-xs max-h-56 rounded-xl border border-slate-200 object-cover shadow-2xs group-hover:opacity-95 transition">
                                        </a>
                                    @else
                                        <a href="{{ asset('storage/' . $ticketMainAttachment) }}" target="_blank" class="text-xs text-sky-600 hover:underline flex items-center gap-1.5 font-medium">
                                            <i class="fa-solid fa-paperclip"></i> View Attachment
                                        </a>
                                    @endif
                                </div>
                            @endif

                            <div class="text-[10px] text-slate-400 font-medium pt-1">
                                {{ $activeTicket->date ?? ($activeTicket->created_at ? $activeTicket->created_at->format('h:i A') : '') }}
                            </div>
                        </div>
                    </div>

                    <!-- 2. Replies Loop & System Event Logs -->
                    @if(isset($activeTicket->replies) && $activeTicket->replies->count() > 0)
                        @foreach($activeTicket->replies as $reply)
                            @php
                                $replyName = $reply->sender_name ?? $reply->user_name ?? ($reply->user->name ?? 'User');
                                $isAdmin = ($reply->sender_role ?? '') == 'Admin' || str_contains(strtolower($replyName), 'admin') || ($reply->user_id ?? null) == Auth::id();
                                
                                $isSystemStatus = str_starts_with($reply->message, 'STATUS_CHANGE:');
                                $isSystemPic    = str_starts_with($reply->message, 'PIC_CHANGE:');

                                $attachment = $reply->attachment ?? null;
                                $ext = $attachment ? strtolower(pathinfo($attachment, PATHINFO_EXTENSION)) : '';
                                $isImage = in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp']);
                            @endphp

                            @if($isSystemPic)
                                @php
                                    $picAssignedName = str_replace('PIC_CHANGE:', '', $reply->message);
                                @endphp
                                <div class="flex items-center justify-center gap-2 py-2 my-2 text-xs text-slate-400 font-medium">
                                    <div class="w-6 h-6 rounded-full bg-slate-600 text-white flex items-center justify-center text-[10px] shrink-0 shadow-2xs">
                                        <i class="fa-solid fa-user-check"></i>
                                    </div>
                                    <span><strong class="text-slate-700 font-bold">{{ $picAssignedName }}</strong> has connected to take over ticket</span>
                                    <span class="text-[10px] text-slate-400">
                                        {{ $reply->created_at ? \Carbon\Carbon::parse($reply->created_at)->format('h:i A') : '' }}
                                    </span>
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
                                <div class="flex items-center justify-center gap-2 py-2 my-2 text-xs text-slate-400 font-medium">
                                    <div class="w-6 h-6 rounded-full bg-blue-500 text-white flex items-center justify-center text-[10px] shrink-0 shadow-2xs">
                                        <i class="fa-solid fa-sliders"></i>
                                    </div>
                                    <span>Ticket changed status to</span>
                                    <span class="px-2.5 py-0.5 rounded-full font-bold text-[11px] {{ $badgeClass }}">
                                        {{ $statusValue }}
                                    </span>
                                    <span class="text-[10px] text-slate-400">
                                        {{ $reply->created_at ? \Carbon\Carbon::parse($reply->created_at)->format('h:i A') : '' }}
                                    </span>
                                </div>

                            @elseif($isAdmin)
                                <div class="flex items-end justify-end gap-2.5 max-w-[85%] md:max-w-[70%] ml-auto">
                                    <div class="bg-blue-50/90 text-slate-800 p-3.5 px-4 rounded-2xl rounded-br-xs space-y-1">
                                        <div class="text-xs font-bold text-blue-900">
                                            {{ $replyName }}
                                        </div>
                                        <div class="text-xs md:text-sm leading-relaxed text-slate-700">
                                            {!! $reply->message !!}
                                        </div>

                                        @if(!empty($attachment))
                                            <div class="mt-2 pt-2 border-t border-blue-200/60">
                                                @if($isImage)
                                                    <a href="{{ asset('storage/' . $attachment) }}" target="_blank" class="block group">
                                                        <img src="{{ asset('storage/' . $attachment) }}" alt="Attachment" class="max-w-xs max-h-56 rounded-xl border border-blue-200 object-cover shadow-2xs group-hover:opacity-90 transition">
                                                    </a>
                                                @else
                                                    <a href="{{ asset('storage/' . $attachment) }}" target="_blank" class="text-xs text-blue-600 hover:underline flex items-center gap-1.5">
                                                        <i class="fa-solid fa-paperclip"></i> Download File
                                                    </a>
                                                @endif
                                            </div>
                                        @endif

                                        <div class="text-[10px] text-slate-400 font-medium pt-1">
                                            {{ $reply->created_at ? \Carbon\Carbon::parse($reply->created_at)->format('h:i A') : '' }}
                                        </div>
                                    </div>

                                    <div class="w-8 h-8 rounded-full bg-slate-900 text-white text-xs font-bold flex items-center justify-center shrink-0 shadow-xs mb-1">
                                        {{ strtoupper(substr($replyName, 0, 2)) }}
                                    </div>
                                </div>

                            @else
                                <div class="flex items-end gap-2.5 max-w-[85%] md:max-w-[70%]">
                                    <div class="w-8 h-8 rounded-full bg-emerald-500 text-white text-xs font-bold flex items-center justify-center shrink-0 shadow-xs mb-1">
                                        {{ strtoupper(substr($replyName, 0, 1)) }}
                                    </div>

                                    <div class="bg-slate-100 text-slate-800 p-3.5 px-4 rounded-2xl rounded-bl-xs space-y-1">
                                        <div class="text-xs font-bold text-slate-900">
                                            {{ $replyName }}
                                        </div>
                                        <div class="text-xs md:text-sm leading-relaxed text-slate-700">
                                            {!! $reply->message !!}
                                        </div>

                                        @if(!empty($attachment))
                                            <div class="mt-2 pt-2 border-t border-slate-200/60">
                                                @if($isImage)
                                                    <a href="{{ asset('storage/' . $attachment) }}" target="_blank" class="block group">
                                                        <img src="{{ asset('storage/' . $attachment) }}" alt="Attachment" class="max-w-xs max-h-56 rounded-xl border border-slate-200 object-cover shadow-2xs group-hover:opacity-90 transition">
                                                    </a>
                                                @else
                                                    <a href="{{ asset('storage/' . $attachment) }}" target="_blank" class="text-xs text-sky-600 hover:underline flex items-center gap-1.5">
                                                        <i class="fa-solid fa-paperclip"></i> Download File
                                                    </a>
                                                @endif
                                            </div>
                                        @endif

                                        <div class="text-[10px] text-slate-400 font-medium pt-1">
                                            {{ $reply->created_at ? \Carbon\Carbon::parse($reply->created_at)->format('h:i A') : '' }}
                                        </div>
                                    </div>
                                </div>
                            @endif
                        @endforeach
                    @endif
                </div>

                <!-- Bottom Chat Input Bar (Fixed Bottom) -->
                <div class="p-4 bg-white border-t border-slate-200 shrink-0">
                    <form action="{{ route('admin.messages.reply', $activeId) }}" method="POST" enctype="multipart/form-data" class="flex flex-col gap-2">
                        @csrf
                        
                        <div id="filePreviewContainer" class="hidden items-center justify-between bg-orange-50 px-3 py-1.5 rounded-lg border border-orange-200 text-xs text-orange-700">
                            <span class="truncate flex items-center gap-1.5">
                                <i class="fa-solid fa-image"></i>
                                <span id="fileNameDisplay">Selected file</span>
                            </span>
                            <button type="button" id="removeFileBtn" class="text-orange-500 hover:text-red-500 ml-2 font-bold">&times;</button>
                        </div>

                        <div class="flex items-center gap-2">
                            <label class="p-2.5 text-slate-400 hover:text-orange-500 hover:bg-slate-100 rounded-xl transition cursor-pointer" title="Attach file or image">
                                <i class="fa-solid fa-paperclip text-lg"></i>
                                <input type="file" id="attachmentInput" name="attachment" accept="image/*,.pdf,.doc,.docx" class="hidden">
                            </label>

                            <input type="text" name="message" required placeholder="Type a message..." 
                                   class="flex-1 bg-slate-100 border border-transparent focus:border-orange-500 focus:bg-white rounded-xl px-4 py-2.5 text-xs md:text-sm text-slate-800 outline-none transition">

                            <button type="submit" class="bg-orange-500 hover:bg-orange-600 text-white px-5 py-2.5 rounded-xl text-xs font-bold transition shadow-md flex items-center gap-2 cursor-pointer shrink-0">
                                <span>Send</span>
                                <i class="fa-solid fa-paper-plane text-[11px]"></i>
                            </button>
                        </div>
                    </form>
                </div>

            @else
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

<!-- Client-side Script -->
<script>
document.addEventListener('DOMContentLoaded', function () {
    // 1. Auto Scroll Down Panel Pesan Kanan ke Paling Bawah
    const msgContainer = document.getElementById('messageScrollContainer');
    if (msgContainer) {
        msgContainer.scrollTop = msgContainer.scrollHeight;
    }

    // 2. Live Filter Search Bar
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

    // 3. File Attachment Preview Indicator
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