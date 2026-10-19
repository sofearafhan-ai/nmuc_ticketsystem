@extends('layouts.app')

@section('title', 'NMUC - Ticket #' . $ticket->ticket_id)

@section('content')
<div class="max-w-6xl mx-auto space-y-6">

    <!-- Title & Status Header -->
    <div class="bg-white p-5 rounded-xl border border-orange-200 shadow-sm flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-3">
                <a href="{{ route('tickets.index') }}" class="text-slate-400 hover:text-slate-700 transition" title="Back">
                    <i class="fa-solid fa-arrow-left text-lg"></i>
                </a>
                <h1 class="text-xl font-bold text-slate-900">#{{ $ticket->ticket_id }} - {{ $ticket->title }}</h1>
            </div>
            <p class="text-xs text-slate-500 mt-1 pl-7">Sent by <span class="font-semibold text-slate-700">{{ $ticket->user_name }}</span> ({{ $ticket->user_email }})</p>
        </div>

        <div class="flex items-center gap-2 pl-7 md:pl-0">
            <span class="px-3 py-1 text-xs font-bold rounded-full text-white 
                @if($ticket->status == 'New') bg-blue-600
                @elseif($ticket->status == 'Processing') bg-amber-500
                @elseif($ticket->status == 'Pending') bg-purple-600
                @elseif($ticket->status == 'Closed') bg-slate-600
                @else bg-emerald-600 @endif">
                {{ $ticket->status }}
            </span>
            <span class="px-3 py-1 text-xs font-bold rounded border border-slate-300 bg-slate-100 text-slate-700">
                Priority: {{ $ticket->priority }}
            </span>
        </div>
    </div>

    @if (session('success'))
        <div class="bg-emerald-900/90 border-l-4 border-emerald-400 text-emerald-100 px-4 py-3 rounded-lg shadow-sm text-sm flex items-center gap-2">
            <i class="fa-solid fa-circle-check text-emerald-300"></i>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">

        <!-- LEFT SECTION -->
        <div class="lg:col-span-8 space-y-6">
            
            <!-- Main Chat Container -->
            <div class="bg-white p-6 rounded-xl shadow-md border border-orange-200">
                
                <h3 class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-4 flex items-center gap-2 border-b border-slate-100 pb-2">
                    <i class="fa-solid fa-comments text-orange-500"></i> Conversation History
                </h3>

                <!-- CONTAINER CONVERSATION HISTORY -->
                <div id="chatContainer" class="max-h-[450px] overflow-y-auto pr-3 space-y-6 scroll-smooth">

                    <!-- USER -->
                    <div class="flex justify-end items-start gap-3 relative">
                        <div class="flex-1 max-w-2xl bg-orange-50/60 border border-orange-300 rounded-xl p-4 shadow-sm relative text-slate-800">
                            <div class="absolute -right-2 top-3 w-0 h-0 border-t-8 border-t-transparent border-l-8 border-l-orange-300 border-b-8 border-b-transparent"></div>
                            <div class="absolute -right-[7px] top-3 w-0 h-0 border-t-8 border-t-transparent border-l-8 border-l-orange-100 border-b-8 border-b-transparent"></div>

                            <div class="flex flex-wrap items-center gap-2 text-[11px] mb-3">
                                <span class="bg-slate-200 text-slate-700 px-2.5 py-0.5 rounded-full font-medium flex items-center gap-1 border border-slate-300">
                                    Created: <i class="fa-regular fa-clock text-[10px]"></i> {{ $ticket->date }}
                                </span>
                                <span class="bg-slate-200 text-slate-700 px-2.5 py-0.5 rounded-full font-medium flex items-center gap-1 border border-slate-300">
                                    by <i class="fa-regular fa-user text-[10px]"></i> {{ $ticket->user_name }}
                                </span>
                            </div>

                            <div class="bg-white p-3 rounded-lg border border-slate-200 text-sm leading-relaxed whitespace-pre-line text-slate-800">
                                {{ $ticket->description }}
                            </div>

                            @if($ticket->image && $ticket->image != 'no-image.png')
                                <div class="mt-3 pt-2 border-t border-slate-200">
                                    @php
                                        $imagePath = Str::startsWith($ticket->image, 'tickets/') ? $ticket->image : 'tickets/' . $ticket->image;
                                    @endphp
                                    <a href="{{ asset('storage/' . $imagePath) }}" target="_blank" class="inline-block border border-slate-300 rounded-lg overflow-hidden max-w-xs hover:opacity-95 transition shadow-sm bg-white p-1">
                                        <img src="{{ asset('storage/' . $imagePath) }}" alt="Ticket Attachment" class="w-full h-auto object-cover max-h-56 rounded">
                                    </a>
                                </div>
                            @endif
                        </div>

                        <!-- Avatar User -->
                        <div class="w-10 h-10 rounded-lg bg-orange-400 text-white font-bold flex items-center justify-center text-xs shadow shrink-0">
                            {{ strtoupper(substr($ticket->user_name ?? 'U', 0, 2)) }}
                        </div>
                    </div>

                    <!-- 2. REPLIES THREAD -->
                    @foreach($ticket->replies as $reply)
                        @if($reply->sender_role == 'Admin')
                            <!-- ADMIN PIC  -->
                            <div class="flex justify-start items-start gap-3 relative">
                                <div class="w-10 h-10 rounded-lg bg-blue-500 text-white font-bold flex items-center justify-center text-xs shadow shrink-0">
                                    {{ strtoupper(substr($reply->sender_name ?? 'AD', 0, 2)) }}
                                </div>

                                <div class="flex-1 max-w-2xl bg-blue-50/80 border border-blue-300 rounded-xl p-4 shadow-sm relative text-slate-800">
                                    <div class="absolute -left-2 top-3 w-0 h-0 border-t-8 border-t-transparent border-r-8 border-r-blue-300 border-b-8 border-b-transparent"></div>
                                    <div class="absolute -left-[7px] top-3 w-0 h-0 border-t-8 border-t-transparent border-r-8 border-r-blue-50/80 border-b-8 border-b-transparent"></div>

                                    <div class="flex flex-wrap items-center gap-2 text-[11px] mb-3">
                                        <span class="bg-slate-100 text-slate-700 px-2.5 py-0.5 rounded-full font-semibold flex items-center gap-1 border border-slate-200">
                                            Created: <i class="fa-regular fa-clock text-[10px]"></i> {{ $reply->created_at->format('Y-m-d H:i') }}
                                        </span>
                                        <span class="bg-slate-100 text-slate-700 px-2.5 py-0.5 rounded-full font-semibold flex items-center gap-1 border border-slate-200">
                                            by <i class="fa-regular fa-user text-[10px]"></i> {{ $reply->sender_name }} (Admin)
                                        </span>
                                    </div>

                                    <div class="bg-white p-3 rounded-lg border border-slate-200 text-sm leading-relaxed whitespace-pre-line text-slate-800 shadow-2xs">
                                        {{ $reply->message }}
                                    </div>

                                    @if($reply->attachment)
                                        <div class="mt-3 pt-2 border-t border-slate-200">
                                            <a href="{{ asset('storage/' . $reply->attachment) }}" target="_blank" class="text-xs text-blue-600 hover:underline flex items-center gap-1 font-semibold">
                                                <i class="fa-solid fa-paperclip"></i> See Attachment
                                            </a>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @else
                            <!-- USER -->
                            <div class="flex justify-end items-start gap-3 relative">
                                <div class="flex-1 max-w-2xl bg-orange-50/60 border border-orange-300 rounded-xl p-4 shadow-sm relative text-slate-800">
                                    <div class="absolute -right-2 top-3 w-0 h-0 border-t-8 border-t-transparent border-l-8 border-l-orange-300 border-b-8 border-b-transparent"></div>
                                    <div class="absolute -right-[7px] top-3 w-0 h-0 border-t-8 border-t-transparent border-l-8 border-l-orange-100 border-b-8 border-b-transparent"></div>

                                    <div class="flex flex-wrap items-center gap-2 text-[11px] mb-3">
                                        <span class="bg-slate-200 text-slate-700 px-2.5 py-0.5 rounded-full font-medium flex items-center gap-1 border border-slate-300">
                                            Created: <i class="fa-regular fa-clock text-[10px]"></i> {{ $reply->created_at->format('Y-m-d H:i') }}
                                        </span>
                                        <span class="bg-slate-200 text-slate-700 px-2.5 py-0.5 rounded-full font-medium flex items-center gap-1 border border-slate-300">
                                            by <i class="fa-regular fa-user text-[10px]"></i> {{ $reply->sender_name }}
                                        </span>
                                    </div>

                                    <div class="bg-white p-3 rounded-lg border border-slate-200 text-sm leading-relaxed whitespace-pre-line text-slate-800">
                                        {{ $reply->message }}
                                    </div>

                                    @if($reply->attachment)
                                        <div class="mt-3 pt-2 border-t border-slate-200">
                                            <a href="{{ asset('storage/' . $reply->attachment) }}" target="_blank" class="text-xs text-orange-600 hover:underline flex items-center gap-1 font-medium">
                                                <i class="fa-solid fa-paperclip"></i> See Attachment
                                            </a>
                                        </div>
                                    @endif
                                </div>

                                <div class="w-10 h-10 rounded-lg bg-orange-400 text-white font-bold flex items-center justify-center text-xs shadow shrink-0">
                                    {{ strtoupper(substr($reply->sender_name ?? 'U', 0, 2)) }}
                                </div>
                            </div>
                        @endif
                    @endforeach

                </div>

            </div>

            <!-- SEMAKAN STATUS TIKET: HANYA PAPAR BORANG JIKA BUKAN CLOSED -->
            @if(in_array($ticket->status, ['Closed', 'Resolved']))
                <!-- TICKET CLOSED NOTICE -->
                <div class="bg-slate-100 border border-slate-300 text-slate-600 rounded-xl p-4 text-center shadow-sm flex items-center justify-center gap-3">
                    <i class="fa-solid fa-lock text-slate-400 text-lg"></i>
                    <p class="text-xs font-semibold">
                        This ticket is marked as <span class="uppercase font-bold text-slate-700">{{ $ticket->status }}</span>. Replies are disabled for closed conversations.
                    </p>
                </div>
            @else
                <!-- USER RESPONSE FORM (ACTIVE) -->
                <div class="bg-white rounded-xl border border-orange-200 shadow-md p-5">
                    <h3 class="text-sm font-bold text-slate-800 mb-3 flex items-center gap-2">
                        <i class="fa-solid fa-reply text-orange-500"></i> Send Reply
                    </h3>

                    <form action="{{ route('tickets.reply', $ticket->ticket_id) }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                        @csrf

                        <div>
                            <textarea name="message" rows="3" required placeholder="Type your response or additional details here..." 
                                class="w-full bg-slate-50 border border-slate-300 rounded-lg p-3 text-sm text-slate-800 focus:ring-2 focus:ring-orange-500 focus:border-orange-500 outline-none transition"></textarea>
                        </div>

                        <div class="flex flex-col sm:flex-row justify-between items-center gap-4">
                            <div class="w-full sm:w-auto">
                                <label class="block text-xs font-semibold text-slate-600 mb-1">Attachment (Optional)</label>
                                <input type="file" name="attachment" class="w-full text-xs text-slate-500 file:mr-2 file:py-1.5 file:px-3 file:rounded file:border-0 file:text-xs file:font-semibold file:bg-orange-50 file:text-orange-700 hover:file:bg-orange-100 cursor-pointer">
                            </div>

                            <div class="w-full sm:w-auto flex justify-end">
                                <button type="submit" class="w-full sm:w-auto px-5 py-2.5 bg-orange-500 hover:bg-orange-600 text-white font-bold text-xs rounded-lg transition shadow-sm flex items-center justify-center gap-2 cursor-pointer">
                                    <i class="fa-solid fa-paper-plane"></i> Send
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            @endif

        </div>

        <!-- RIGHT SECTION: TICKET INFORMATION (4 Columns) -->
        <div class="lg:col-span-4 space-y-4">
            <div class="bg-white rounded-xl border border-orange-200 shadow-md p-5 space-y-4 sticky top-20">
                
                <!-- Header -->
                <div class="border-b border-slate-200 pb-3 flex items-center justify-between gap-2">
                    <h3 class="font-bold text-slate-800 text-xs uppercase tracking-wider flex items-center gap-2">
                        <i class="fa-solid fa-sliders text-orange-500"></i> Ticket Information
                    </h3>
                    
                    <!-- Ticket Open -->
                    <div class="text-right shrink-0">
                        <div class="text-[11px] font-bold text-slate-700 flex items-center justify-end gap-1">
                            <i class="fa-regular fa-calendar-days text-orange-500 text-[10px]"></i>
                            <span>{{ \Carbon\Carbon::parse($ticket->created_at ?? $ticket->date)->format('d M Y') }}</span>
                        </div>
                        <div class="text-[10px] text-slate-400 font-semibold flex items-center justify-end gap-1 mt-0.5">
                            <i class="fa-regular fa-clock text-[9px]"></i>
                            <span>{{ \Carbon\Carbon::parse($ticket->created_at ?? $ticket->date)->format('h:i A') }}</span>
                        </div>
                    </div>
                </div>

                <!-- Body Information -->
                <div class="space-y-3.5 text-xs">
                    <!-- Category -->
                    <div>
                        <span class="block text-slate-400 font-semibold mb-1">Category</span>
                        <span class="font-semibold text-slate-700 bg-slate-100 px-2.5 py-1 rounded inline-block border border-slate-200">
                            {{ $ticket->category }}
                        </span>
                    </div>

                    <!-- Type -->
                    <div>
                        <span class="block text-slate-400 font-semibold mb-1">Type</span>
                        <span class="font-semibold text-slate-700">{{ $ticket->type }}</span>
                    </div>

                    <!-- Incident Location -->
                    <div>
                        <span class="block text-slate-400 font-semibold mb-1">Incident Location</span>
                        <span class="font-semibold text-slate-700 flex items-center gap-1">
                            <i class="fa-solid fa-location-dot text-orange-500"></i> {{ $ticket->location }}
                        </span>
                    </div>

                    <!-- Person In Charge (PIC) -->
                    <div>
                        <span class="block text-slate-400 font-semibold mb-1">Person In Charge (PIC)</span>
                        @php
                            $picName = $ticket->assigned_to ?? $ticket->user->full_name ?? $ticket->user->name ?? null;
                        @endphp

                        @if(!empty($picName))
                            <span class="font-semibold text-slate-800 bg-emerald-50 text-emerald-800 px-2.5 py-1 rounded inline-flex items-center gap-1.5 border border-emerald-200">
                                <i class="fa-solid fa-user-check text-emerald-600"></i> {{ $picName }}
                            </span>
                        @else
                            <span class="font-medium text-slate-500 italic bg-slate-50 px-2.5 py-1 rounded inline-flex items-center gap-1 border border-slate-200">
                                <i class="fa-regular fa-clock text-slate-400"></i> Not Assigned Yet
                            </span>
                        @endif
                    </div>

                </div>
            </div>
        </div>

    </div>
</div>

<!-- AUTO SCROLL TO BOTTOM SCRIPT -->
<script>
    document.addEventListener("DOMContentLoaded", function () {
        const chatContainer = document.getElementById("chatContainer");
        if (chatContainer) {
            chatContainer.scrollTop = chatContainer.scrollHeight;
        }
    });
</script>
@endsection