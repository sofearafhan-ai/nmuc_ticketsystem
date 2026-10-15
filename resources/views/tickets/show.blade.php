@extends('layouts.app')

@section('title', 'NMUC - Ticket #' . $ticket->ticket_id)

@section('content')
<div class="max-w-6xl mx-auto space-y-6">

    <!-- Header Tajuk & Status -->
    <div class="bg-white p-5 rounded-xl border border-orange-200 shadow-sm flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-3">
                <a href="{{ route('tickets.index') }}" class="text-slate-400 hover:text-slate-700 transition" title="Kembali">
                    <i class="fa-solid fa-arrow-left text-lg"></i>
                </a>
                <h1 class="text-xl font-bold text-slate-900">#{{ $ticket->ticket_id }} - {{ $ticket->title }}</h1>
            </div>
            <p class="text-xs text-slate-500 mt-1 pl-7">Dihantar oleh <span class="font-semibold text-slate-700">{{ $ticket->user_name }}</span> ({{ $ticket->user_email }})</p>
        </div>

        <div class="flex items-center gap-2 pl-7 md:pl-0">
            <span class="px-3 py-1 text-xs font-bold rounded-full text-white 
                @if($ticket->status == 'New') bg-blue-600
                @elseif($ticket->status == 'Processing') bg-amber-500
                @elseif($ticket->status == 'Pending') bg-purple-600
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

        <!-- RUANGAN KIRI: PERBUALAN GAYA BUBBLE CHAT GLPI (8 Columns) -->
        <div class="lg:col-span-8 space-y-6">

            <!-- 1. ADUAN ASAL (DARI PENGGUNA - SEBELAH KANAN CHAT BUBBLE) -->
            <div class="flex justify-end items-start gap-3 relative my-4">
                
                <!-- Kad Bubble Aduan Asal -->
                <div class="flex-1 max-w-2xl bg-slate-100 border border-slate-300 rounded-xl p-4 shadow-sm relative text-slate-800">
                    <!-- Segi tiga ekor bubble kanan -->
                    <div class="absolute -right-2 top-3 w-0 h-0 border-t-8 border-t-transparent border-l-8 border-l-slate-300 border-b-8 border-b-transparent"></div>
                    <div class="absolute -right-[7px] top-3 w-0 h-0 border-t-8 border-t-transparent border-l-8 border-l-slate-100 border-b-8 border-b-transparent"></div>

                    <!-- Metadata Header -->
                    <div class="flex flex-wrap items-center gap-2 text-[11px] mb-3">
                        <span class="bg-slate-200 text-slate-700 px-2.5 py-0.5 rounded-full font-medium flex items-center gap-1 border border-slate-300">
                            Created: <i class="fa-regular fa-clock text-[10px]"></i> {{ $ticket->date }}
                        </span>
                        <span class="bg-slate-200 text-slate-700 px-2.5 py-0.5 rounded-full font-medium flex items-center gap-1 border border-slate-300">
                            by <i class="fa-regular fa-user text-[10px]"></i> {{ $ticket->user_name }}
                        </span>
                    </div>

                    <!-- Isi Kandungan Aduan -->
                    <div class="bg-white p-3 rounded-lg border border-slate-200 text-sm leading-relaxed whitespace-pre-line text-slate-800">
                        {{ $ticket->description }}
                    </div>

                    <!-- Lampiran Imej (Jika Ada) -->
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

                    <!-- Footer Badge -->
                    <div class="mt-3 flex items-center gap-2 text-[11px] text-slate-500 font-medium">
                        <span class="bg-slate-200 px-2 py-0.5 rounded border border-slate-300 flex items-center gap-1">
                            <i class="fa-solid fa-desktop text-[10px]"></i> Helpdesk
                        </span>
                    </div>
                </div>

                <!-- Avatar User (Merah / Oren) -->
                <div class="w-10 h-10 rounded-lg bg-red-400 text-white font-bold flex items-center justify-center text-xs shadow shrink-0">
                    {{ strtoupper(substr($ticket->user_name ?? 'U', 0, 2)) }}
                </div>
            </div>

            <!-- 2. SEJARAH BALASAN (THREAD CHAT GLPI) -->
            @foreach($ticket->replies as $reply)
                @if($reply->sender_role == 'Admin')
                    <!-- ADMIN PIC BALASAN (SEBELAH KIRI - BUBBLE HIJAU/SLATE) -->
                    <div class="flex justify-start items-start gap-3 relative my-4">
                        <!-- Avatar Admin (Hijau) -->
                        <div class="w-10 h-10 rounded-lg bg-emerald-500 text-white font-bold flex items-center justify-center text-xs shadow shrink-0">
                            {{ strtoupper(substr($reply->sender_name ?? 'AD', 0, 2)) }}
                        </div>

                        <!-- Kad Bubble Admin -->
                        <div class="flex-1 max-w-2xl bg-emerald-50/80 border border-emerald-300 rounded-xl p-4 shadow-sm relative text-slate-800">
                            <!-- Segi tiga ekor bubble kiri -->
                            <div class="absolute -left-2 top-3 w-0 h-0 border-t-8 border-t-transparent border-r-8 border-r-emerald-300 border-b-8 border-b-transparent"></div>
                            <div class="absolute -left-[7px] top-3 w-0 h-0 border-t-8 border-t-transparent border-r-8 border-r-emerald-50/80 border-b-8 border-b-transparent"></div>

                            <!-- Metadata Header -->
                            <div class="flex flex-wrap items-center gap-2 text-[11px] mb-3">
                                <span class="bg-emerald-100 text-emerald-800 px-2.5 py-0.5 rounded-full font-semibold flex items-center gap-1 border border-emerald-200">
                                    Created: <i class="fa-regular fa-clock text-[10px]"></i> {{ $reply->created_at->format('Y-m-d H:i') }}
                                </span>
                                <span class="bg-emerald-100 text-emerald-800 px-2.5 py-0.5 rounded-full font-semibold flex items-center gap-1 border border-emerald-200">
                                    by <i class="fa-regular fa-user text-[10px]"></i> {{ $reply->sender_name }} (Admin)
                                </span>
                            </div>

                            <!-- Mesej Balasan -->
                            <div class="bg-white p-3 rounded-lg border border-emerald-100 text-sm leading-relaxed whitespace-pre-line text-slate-800 shadow-2xs">
                                {{ $reply->message }}
                            </div>

                            @if($reply->attachment)
                                <div class="mt-3 pt-2 border-t border-emerald-200">
                                    <a href="{{ asset('storage/' . $reply->attachment) }}" target="_blank" class="text-xs text-emerald-700 hover:underline flex items-center gap-1 font-semibold">
                                        <i class="fa-solid fa-paperclip"></i> Lihat Lampiran Balasan
                                    </a>
                                </div>
                            @endif
                        </div>
                    </div>
                @else
                    <!-- USER BALASAN (SEBELAH KANAN - BUBBLE KELABU/OREN) -->
                    <div class="flex justify-end items-start gap-3 relative my-4">
                        <div class="flex-1 max-w-2xl bg-slate-100 border border-slate-300 rounded-xl p-4 shadow-sm relative text-slate-800">
                            <div class="absolute -right-2 top-3 w-0 h-0 border-t-8 border-t-transparent border-l-8 border-l-slate-300 border-b-8 border-b-transparent"></div>
                            <div class="absolute -right-[7px] top-3 w-0 h-0 border-t-8 border-t-transparent border-l-8 border-l-slate-100 border-b-8 border-b-transparent"></div>

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
                                        <i class="fa-solid fa-paperclip"></i> Lihat Lampiran
                                    </a>
                                </div>
                            @endif
                        </div>

                        <div class="w-10 h-10 rounded-lg bg-red-400 text-white font-bold flex items-center justify-center text-xs shadow shrink-0">
                            {{ strtoupper(substr($reply->sender_name ?? 'U', 0, 2)) }}
                        </div>
                    </div>
                @endif
            @endforeach

            <!-- 3. BORANG BALASAN CHAT PIC (ADMIN RESPONSE FORM) -->
            <div class="bg-white rounded-xl border border-orange-200 shadow-md p-5 mt-6">
                <h3 class="text-sm font-bold text-slate-800 mb-3 flex items-center gap-2">
                    <i class="fa-solid fa-reply text-orange-500"></i> Hantar Balasan (Admin PIC)
                </h3>

                <form action="{{ route('tickets.reply', $ticket->ticket_id) }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                    @csrf

                    <div>
                        <textarea name="message" rows="4" required placeholder="Tulis mesej balasan atau penyelesaian masalah di sini..." 
                            class="w-full bg-slate-50 border border-slate-300 rounded-lg p-3 text-sm text-slate-800 focus:ring-2 focus:ring-orange-500 focus:border-orange-500 outline-none transition"></textarea>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 items-center">
                        <!-- Tukar Status Tiket -->
                        <div>
                            <label class="block text-xs font-semibold text-slate-600 mb-1">Kemaskini Status Tiket</label>
                            <select name="status" class="w-full bg-slate-50 border border-slate-300 rounded-lg p-2 text-xs font-semibold text-slate-700 outline-none focus:ring-2 focus:ring-orange-500">
                                @foreach($statuses as $st)
                                    <option value="{{ $st }}" {{ $ticket->status == $st ? 'selected' : '' }}>
                                        Status: {{ $st }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Muat Naik Lampiran -->
                        <div>
                            <label class="block text-xs font-semibold text-slate-600 mb-1">Lampiran (Opsional)</label>
                            <input type="file" name="attachment" class="w-full text-xs text-slate-500 file:mr-2 file:py-1.5 file:px-3 file:rounded file:border-0 file:text-xs file:font-semibold file:bg-orange-50 file:text-orange-700 hover:file:bg-orange-100 cursor-pointer">
                        </div>
                    </div>

                    <div class="flex justify-end pt-2">
                        <button type="submit" class="px-5 py-2.5 bg-orange-500 hover:bg-orange-600 text-white font-bold text-xs rounded-lg transition shadow-sm flex items-center gap-2 cursor-pointer">
                            <i class="fa-solid fa-paper-plane"></i> Hantar Balasan
                        </button>
                    </div>
                </form>
            </div>

        </div>

        <!-- RUANGAN KANAN: MAKLUMAT TIKET SIDEBAR (4 Columns) -->
        <div class="lg:col-span-4 space-y-4">
            <div class="bg-white rounded-xl border border-orange-200 shadow-md p-5 space-y-4 sticky top-20">
                <h3 class="font-bold text-slate-800 text-xs uppercase tracking-wider border-b border-slate-200 pb-3 flex items-center gap-2">
                    <i class="fa-solid fa-sliders text-orange-500"></i> Maklumat Tiket
                </h3>

                <div class="space-y-3 text-xs">
                    <div>
                        <span class="block text-slate-400 font-semibold mb-0.5">Kategori</span>
                        <span class="font-semibold text-slate-700 bg-slate-100 px-2.5 py-1 rounded inline-block border border-slate-200">{{ $ticket->category }}</span>
                    </div>

                    <div>
                        <span class="block text-slate-400 font-semibold mb-0.5">Jenis Aduan</span>
                        <span class="font-semibold text-slate-700">{{ $ticket->type }}</span>
                    </div>

                    <div>
                        <span class="block text-slate-400 font-semibold mb-0.5">Lokasi Insiden</span>
                        <span class="font-semibold text-slate-700"><i class="fa-solid fa-location-dot text-orange-500 mr-1"></i> {{ $ticket->location }}</span>
                    </div>

                    <div>
                        <span class="block text-slate-400 font-semibold mb-0.5">Tarikh Dibuka</span>
                        <span class="font-semibold text-slate-700">{{ $ticket->date }}</span>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection