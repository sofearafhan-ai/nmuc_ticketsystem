@extends('layouts.app')

@section('title', 'NMUC - New Ticket')

@section('content')
    <div class="w-full max-w-6xl mx-auto">

        <!-- Ralat Validasi -->
        @if ($errors->any())
            <div class="mb-4 bg-red-950/80 border-l-4 border-red-500 p-4 rounded shadow-sm text-red-200 w-full">
                <div class="flex items-center gap-2 text-red-400 font-bold mb-1 text-sm">
                    <i class="fa-solid fa-triangle-exclamation"></i> Sila semak ralat berikut:
                </div>
                <ul class="list-disc pl-5 text-xs text-red-300 space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Container Borang (GLPI Style 2 Ruangan) -->
        <form action="{{ route('tickets.store') }}" method="POST" enctype="multipart/form-data" class="w-full bg-white border border-orange-200 rounded-lg shadow-xl overflow-hidden grid grid-cols-1 lg:grid-cols-12 min-h-[650px] text-slate-700">
            @csrf

            <!-- RUANGAN KIRI: Borang Utama (8 Columns) -->
            <div class="lg:col-span-8 p-4 md:p-6 border-b lg:border-b-0 lg:border-r border-slate-200 bg-slate-50/50 flex flex-col justify-between">
                
                <div class="space-y-5">
                    <div class="flex items-start gap-4">
                        <div class="w-12 h-12 rounded-lg bg-orange-500 text-white font-bold flex items-center justify-center text-lg shadow-sm shrink-0">
                            {{ strtoupper(substr(Auth::user()->name ?? 'U', 0, 2)) }}
                        </div>

                        <div class="flex-1 bg-orange-50/60 border border-slate-200 rounded-lg p-4 md:p-5 relative">
                            <div class="hidden md:block absolute -left-2 top-4 w-0 h-0 border-t-8 border-t-transparent border-r-8 border-r-orange-100 border-b-8 border-b-transparent"></div>

                            <div class="text-sm font-bold text-slate-800 mb-3 flex items-center justify-between">
                                <span>{{ Auth::user()->name ?? 'User' }}</span>
                                <span class="text-xs text-slate-400 font-normal">Create New Ticket</span>
                            </div>

                            <div class="bg-white border-l-4 border-sky-500 border-slate-200 rounded p-2.5 mb-5 text-xs text-slate-600 flex items-center gap-2 shadow-sm">
                                <span>This ticket will be added to the entity:</span>
                                <span class="bg-slate-100 text-slate-700 font-medium px-2 py-0.5 rounded border border-slate-200 text-xs">Root Entity</span>
                            </div>

                            <!-- Title Input -->
                            <div class="mb-5">
                                <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Title <span class="text-red-500">*</span></label>
                                <input type="text" name="title" required value="{{ old('title') }}" placeholder="Cth: Komputer Makmal 2 Tidak Boleh Dihidupkan"
                                    class="w-full bg-white border border-slate-300 rounded p-2.5 text-sm text-slate-800 focus:ring-2 focus:ring-orange-500 focus:border-orange-500 outline-none transition">
                            </div>

                            <!-- Description Input -->
                            <div class="mb-2">
                                <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Description <span class="text-red-500">*</span></label>
                                
                                <div class="bg-white border border-slate-300 rounded-t-md p-2 flex flex-wrap items-center gap-2 text-xs text-slate-600 border-b-0">
                                    <span class="px-2 py-1 bg-slate-100 rounded text-slate-700 border border-slate-200">Paragraph <i class="fa-solid fa-chevron-down text-[10px] ml-1"></i></span>
                                    <div class="h-4 w-px bg-slate-200 mx-1"></div>
                                    <button type="button" class="p-1 hover:bg-slate-100 rounded font-bold px-2">B</button>
                                    <button type="button" class="p-1 hover:bg-slate-100 rounded italic px-2">I</button>
                                    <div class="h-4 w-px bg-slate-200 mx-1"></div>
                                    <button type="button" class="p-1 hover:bg-slate-100 rounded"><i class="fa-solid fa-underline"></i></button>
                                    <button type="button" class="p-1 hover:bg-slate-100 rounded"><i class="fa-solid fa-highlighter"></i></button>
                                    <div class="h-4 w-px bg-slate-200 mx-1"></div>
                                    <button type="button" class="p-1 hover:bg-slate-100 rounded"><i class="fa-solid fa-list-ul"></i></button>
                                    <button type="button" class="p-1 hover:bg-slate-100 rounded"><i class="fa-solid fa-ellipsis"></i></button>
                                </div>

                                <textarea name="description" rows="6" required placeholder="Provide detailed information regarding the incident or the request..." 
                                    class="w-full bg-white border border-slate-300 rounded-b-md p-3 text-sm text-slate-800 focus:ring-2 focus:ring-orange-500 focus:border-orange-500 outline-none transition resize-y">{{ old('description') }}</textarea>
                            </div>

                            <!-- Image Upload -->
                            <div class="mt-4 bg-white p-3 rounded border border-slate-200">
                                <label class="block text-xs font-semibold text-slate-600 mb-1">Attachments / Images (Optional)</label>
                                <input type="file" name="image" accept="image/*" class="w-full text-xs text-slate-500 file:mr-3 file:py-1.5 file:px-3 file:rounded file:border-0 file:text-xs file:font-semibold file:bg-orange-50 file:text-orange-700 hover:file:bg-orange-100 cursor-pointer">
                            </div>

                        </div>
                    </div>
                </div>

            </div>

            <!-- RUANGAN KANAN: Metadata Sidebar (4 Columns) -->
            <div class="lg:col-span-4 p-4 md:p-6 bg-white flex flex-col justify-between">
                <div class="space-y-4">
                    
                    <div class="flex items-center justify-between border-b border-slate-200 pb-3">
                        <span class="font-bold text-slate-700 text-sm flex items-center gap-2">
                            <i class="fa-regular fa-circle-question text-slate-400"></i> Ticket Details
                        </span>
                        <i class="fa-solid fa-chevron-up text-xs text-slate-400"></i>
                    </div>

                    <!-- Opening Date -->
                    <div>
                        <label class="block text-xs font-semibold text-slate-500 mb-1">Opening date</label>
                        <div class="relative">
                            <input type="text" name="opening_date" value="{{ date('Y-m-d H:i') }}" readonly 
                                class="w-full bg-slate-50 border border-slate-300 rounded p-2 text-sm text-slate-700 font-medium pr-8">
                            <i class="fa-regular fa-calendar absolute right-3 top-2.5 text-slate-400 text-xs"></i>
                        </div>
                    </div>

                    <!-- Name -->
                    <div>
                        <label class="block text-xs font-semibold text-slate-500 mb-1">Name</label>
                        <input type="text" name="user_name" required value="{{ old('user_name', Auth::user()->name ?? '') }}" placeholder="Nama Pengguna"
                            class="w-full border border-slate-300 rounded p-2 text-sm text-slate-700 focus:ring-2 focus:ring-orange-500 focus:border-orange-500 outline-none">
                    </div>

                    <!-- Email -->
                    <div>
                        <label class="block text-xs font-semibold text-slate-500 mb-1">Email</label>
                        <input type="email" name="user_email" required value="{{ old('user_email', Auth::user()->email ?? '') }}" placeholder="email@nmuc.edu.my"
                            class="w-full border border-slate-300 rounded p-2 text-sm text-slate-700 focus:ring-2 focus:ring-orange-500 focus:border-orange-500 outline-none">
                    </div>

                    <!-- Type -->
                    <div>
                        <label class="block text-xs font-semibold text-slate-500 mb-1">Type</label>
                        <select name="type" required class="w-full border border-slate-300 rounded p-2 text-sm bg-white text-slate-700 focus:ring-2 focus:ring-orange-500 focus:border-orange-500 outline-none">
                            @foreach ($types as $type)
                                <option value="{{ $type }}" {{ old('type') == $type ? 'selected' : '' }}>
                                    {{ $type }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Category -->
                    <div>
                        <label class="block text-xs font-semibold text-slate-500 mb-1">Category</label>
                        <div class="flex items-center">
                            <select name="category" required class="w-full border border-slate-300 rounded-l p-2 text-sm bg-white text-slate-700 focus:ring-2 focus:ring-orange-500 focus:border-orange-500 outline-none">
                                <option value="">-----</option>
                                @foreach ($categories as $category)
                                    <option value="{{ $category }}" {{ old('category') == $category ? 'selected' : '' }}>
                                        {{ $category }}
                                    </option>
                                @endforeach
                            </select>
                            <button type="button" class="border border-l-0 border-slate-300 bg-slate-50 px-2.5 py-2 text-slate-500 hover:bg-slate-100 text-xs" title="Maklumat Kategori">i</button>
                            <button type="button" class="border border-l-0 border-slate-300 bg-slate-50 px-2.5 py-2 text-slate-500 hover:bg-slate-100 rounded-r text-xs" title="Tambah Kategori Baru">+</button>
                        </div>
                    </div>

                    <!-- Priority -->
                    <div>
                        <label class="block text-xs font-semibold text-slate-500 mb-1">Priority</label>
                        <select name="priority" required class="w-full border border-slate-300 rounded p-2 text-sm bg-white text-slate-700 focus:ring-2 focus:ring-orange-500 focus:border-orange-500 outline-none">
                            @foreach ($priorities as $priority)
                                <option value="{{ $priority }}" {{ old('priority', 'Medium') == $priority ? 'selected' : '' }}>
                                    {{ $priority }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Location -->
                    <div>
                        <label class="block text-xs font-semibold text-slate-500 mb-1">Location</label>
                        <input type="text" name="location" placeholder="Cth: Makmal 2, Blok B" required value="{{ old('location') }}"
                            class="w-full border border-slate-300 rounded p-2 text-sm text-slate-700 focus:ring-2 focus:ring-orange-500 focus:border-orange-500 outline-none">
                    </div>

                    <!-- Status -->
                    <div>
                        <label class="block text-xs font-semibold text-slate-500 mb-1">Status</label>
                        <div class="w-full border border-slate-300 rounded p-2 bg-slate-50 text-sm font-medium text-slate-700 flex items-center gap-2">
                            <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 inline-block"></span> New
                        </div>
                    </div>

                </div>

                <!-- Action Buttons -->
                <div class="pt-6 mt-6 border-t border-slate-100 flex items-center justify-between">
                    <div class="flex gap-1 text-slate-400 text-xs">
                        <button type="button" class="p-1 hover:text-slate-600"><i class="fa-solid fa-caret-left"></i></button>
                        <button type="button" class="p-1 hover:text-slate-600"><i class="fa-solid fa-caret-right"></i></button>
                    </div>
                    
                    <div class="flex gap-2">
                        <a href="{{ route('tickets.index') }}" class="px-3 py-2 border border-slate-300 text-slate-600 hover:bg-slate-50 rounded text-xs font-semibold transition">
                            Cancel
                        </a>
                        <button type="submit" class="bg-orange-500 hover:bg-orange-600 text-white font-bold px-4 py-2 rounded text-sm transition flex items-center gap-1 shadow-sm cursor-pointer">
                            <i class="fa-solid fa-plus text-xs"></i> Add
                        </button>
                    </div>
                </div>

            </div>

        </form>
    </div>
@endsection