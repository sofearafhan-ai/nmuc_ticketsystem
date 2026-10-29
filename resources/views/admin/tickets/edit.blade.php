@extends('layouts.admin')

@section('title', 'Ticket Details #' . ($ticket->ticket_id ?? $ticket->id))

@section('content')
<!-- Styling Custom Scrollbar -->
<style>
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

<div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 space-y-6">

    <!-- Flash Message Notification -->
    @if(session('success'))
        <div class="p-4 bg-emerald-100 border border-emerald-200 text-emerald-800 rounded-xl text-xs font-bold flex items-center gap-2">
            <i class="fa-solid fa-circle-check text-emerald-600"></i>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    <!-- Back to Ticket List Button -->
    <div class="mb-2">
        <a href="{{ route('admin.tickets.index') }}" 
        class="inline-flex items-center gap-2 px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold rounded-xl transition duration-150">
            <i class="fa-solid fa-arrow-left"></i>
            <span>Back to Ticket List</span>
        </a>
    </div>

    <!-- GRID LAYOUT 2 RUANG (Kiri 2/3, Kanan 1/3) -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 items-start">

        <!-- RUANG KIRI: Conversation / Chat Area (Width 2/3) -->
        <div class="lg:col-span-2 bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden flex flex-col h-[650px]">
            
            @php
                $activeId = $ticket->ticket_id ?? $ticket->id;
                $activeUserName = $ticket->user_name ?? ($ticket->user->name ?? 'User');
                $ticketMainAttachment = $ticket->image ?? $ticket->image_path ?? null;
                $formattedTicketNo = '#TC-' . str_pad($activeId, 4, '0', STR_PAD_LEFT);
            @endphp

            <!-- Active Chat Header -->
            <div class="px-6 py-4 bg-white border-b border-slate-200 flex items-center justify-between shadow-2xs shrink-0">
                <div class="flex items-center gap-3 min-w-0">
                    <!-- Avatar Initial -->
                    <div class="w-10 h-10 rounded-full bg-emerald-500 text-white font-bold flex items-center justify-center text-sm shrink-0 shadow-xs">
                        {{ strtoupper(substr($activeUserName, 0, 1)) }}
                    </div>
                    
                    <!-- Header Title & Status -->
                    <div class="flex items-center gap-2 min-w-0">
                        <h2 class="text-base font-bold text-slate-800 truncate">
                            Ticket {{ $formattedTicketNo }} - {{ $ticket->title }}
                        </h2>
                        <span class="text-slate-400">•</span>
                        <span class="px-2.5 py-1 rounded-full font-bold text-xs
                            @if(($ticket->status ?? '') == 'New') bg-emerald-100 text-emerald-700
                            @elseif(($ticket->status ?? '') == 'Processing') bg-blue-100 text-blue-700
                            @elseif(($ticket->status ?? '') == 'Pending') bg-amber-100 text-amber-700
                            @elseif(($ticket->status ?? '') == 'Close' || ($ticket->status ?? '') == 'Closed') bg-red-100 text-red-700
                            @else bg-slate-100 text-slate-700 @endif">
                            {{ $ticket->status }}
                        </span>
                    </div>
                </div>
            </div>

            <!-- Chat Messages Scroll Area -->
            <div class="flex-1 p-5 md:p-6 overflow-y-auto custom-scrollbar space-y-4 bg-white" id="editMessageScrollContainer">
                
                <div class="flex items-end gap-2.5 max-w-[85%] md:max-w-[70%]">
                    <div class="w-8 h-8 rounded-full bg-emerald-500 text-white text-xs font-bold flex items-center justify-center shrink-0 shadow-xs mb-1">
                        {{ strtoupper(substr($activeUserName, 0, 1)) }}
                    </div>

                    <div class="bg-slate-100 text-slate-800 p-3.5 px-4 rounded-2xl rounded-bl-xs space-y-1">
                        <div class="text-xs font-bold text-slate-900">
                            {{ $activeUserName }}
                        </div>
                        <div class="text-xs md:text-sm leading-relaxed">
                            {!! $ticket->description !!}
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
                            {{ $ticket->date ?? ($ticket->created_at ? $ticket->created_at->format('h:i A') : '') }}
                        </div>
                    </div>
                </div>

                @if(isset($ticket->replies) && $ticket->replies->count() > 0)
                    @foreach($ticket->replies as $reply)
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
                            <!-- LOG SYSTEM: PIC ASSIGNED / TAKEOVER -->
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
                            <!-- LOG SYSTEM: STATUS CHANGE -->
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

            <!-- Bottom Chat Input Bar -->
            <div class="p-4 bg-white border-t border-slate-200 shrink-0">
                <form action="{{ route('admin.messages.reply', $activeId) }}" method="POST" enctype="multipart/form-data" class="flex flex-col gap-2">
                    @csrf
                    
                    <!-- File Preview Name Indicator -->
                    <div id="filePreviewContainerEdit" class="hidden items-center justify-between bg-orange-50 px-3 py-1.5 rounded-lg border border-orange-200 text-xs text-orange-700">
                        <span class="truncate flex items-center gap-1.5">
                            <i class="fa-solid fa-image"></i>
                            <span id="fileNameDisplayEdit">Selected file</span>
                        </span>
                        <button type="button" id="removeFileBtnEdit" class="text-orange-500 hover:text-red-500 ml-2 font-bold">&times;</button>
                    </div>

                    <div class="flex items-center gap-2">
                        <!-- Attachment Button -->
                        <label class="p-2.5 text-slate-400 hover:text-orange-500 hover:bg-slate-100 rounded-xl transition cursor-pointer" title="Attach file or image">
                            <i class="fa-solid fa-paperclip text-lg"></i>
                            <input type="file" id="attachmentInputEdit" name="attachment" accept="image/*,.pdf,.doc,.docx" class="hidden">
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
            <form action="{{ route('admin.tickets.update', $activeId) }}" method="POST" class="space-y-4">
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
                    <button type="submit" class="w-full py-2.5 bg-orange-500 hover:bg-orange-600 text-white font-bold rounded-xl text-xs shadow-md transition cursor-pointer">
                        Update Information
                    </button>
                </div>
            </form>

        </div>

    </div>

</div>

<!-- Client-side Script for Auto Scroll & File Preview -->
<script>
document.addEventListener('DOMContentLoaded', function () {
    
    const scrollContainer = document.getElementById('editMessageScrollContainer');
    if (scrollContainer) {
        scrollContainer.scrollTop = scrollContainer.scrollHeight;
    }
    
    const fileInput = document.getElementById('attachmentInputEdit');
    const filePreviewContainer = document.getElementById('filePreviewContainerEdit');
    const fileNameDisplay = document.getElementById('fileNameDisplayEdit');
    const removeFileBtn = document.getElementById('removeFileBtnEdit');

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