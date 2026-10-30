@extends('layouts.superadmin')

@section('title', 'Create New Ticket')

@section('content')
<div class="space-y-6">

    <!-- Validation Error Alert -->
    @if ($errors->any())
        <div class="bg-red-950/80 border-l-4 border-red-500 p-4 rounded-xl shadow-sm text-red-200">
            <div class="flex items-center gap-2 text-red-400 font-bold mb-1 text-sm">
                <i class="fa-solid fa-triangle-exclamation"></i> Please check the following errors:
            </div>
            <ul class="list-disc pl-5 text-xs text-red-300 space-y-1">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Form Container -->
    <form action="{{ route('superadmin.tickets.store') }}" method="POST" enctype="multipart/form-data" 
          class="w-full bg-white border border-slate-200/80 rounded-2xl shadow-sm overflow-hidden grid grid-cols-1 lg:grid-cols-12 min-h-[650px] text-slate-700">
        @csrf

        <!-- LEFT SECTION: Main Form (8 Columns) -->
        <div class="lg:col-span-8 p-4 md:p-6 border-b lg:border-b-0 lg:border-r border-slate-200 bg-slate-50/50 flex flex-col justify-between">
            
            <div class="space-y-5">
                <div class="flex items-start gap-4">
                    <div class="w-12 h-12 rounded-xl bg-orange-500 text-white font-bold flex items-center justify-center text-lg shadow-sm shrink-0">
                        {{ strtoupper(substr(Auth::user()->full_name ?? Auth::user()->name ?? 'SA', 0, 2)) }}
                    </div>

                    <div class="flex-1 bg-orange-50/60 border border-orange-200 rounded-2xl p-4 md:p-5 relative">
                        <div class="hidden md:block absolute -left-2 top-4 w-0 h-0 border-t-8 border-t-transparent border-r-8 border-r-orange-100 border-b-8 border-b-transparent"></div>

                        <div class="text-sm font-bold text-slate-800 mb-3 flex items-center justify-between">
                            <span>{{ Auth::user()->full_name ?? Auth::user()->name ?? 'Super Admin' }}</span>
                            <span class="text-xs text-slate-400 font-normal">Create New Ticket</span>
                        </div>

                        <div class="bg-white border-l-4 border-sky-500 border-slate-200 rounded-lg p-2.5 mb-5 text-xs text-slate-600 flex items-center gap-2 shadow-sm">
                            <span>This ticket will be created under Super Admin privileges.</span>
                        </div>

                        <!-- Title Input -->
                        <div class="mb-5">
                            <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Title <span class="text-red-500">*</span></label>
                            <input type="text" name="title" required value="{{ old('title') }}" placeholder="Briefly describe the ticket subject..."
                                class="w-full bg-white border border-slate-300 rounded-xl p-2.5 text-sm text-slate-800 focus:ring-2 focus:ring-orange-500 focus:border-orange-500 outline-none transition">
                        </div>

                        <!-- Description Input (Visual WYSIWYG Editor) -->
                        <div class="mb-2">
                            <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Description <span class="text-red-500">*</span></label>
                            
                            <!-- Editor Toolbar -->
                            <div class="bg-white border border-slate-300 rounded-t-xl p-2 flex flex-wrap items-center gap-2 text-xs text-slate-600 border-b-0">
                                <!-- Paragraph / Heading Dropdown -->
                                <select onchange="execCmdArg('formatBlock', this.value)" class="px-2 py-1 bg-slate-100 rounded text-slate-700 border border-slate-200 outline-none text-xs cursor-pointer font-medium">
                                    <option value="p">Paragraph</option>
                                    <option value="h1">Heading 1</option>
                                    <option value="h2">Heading 2</option>
                                    <option value="h3">Heading 3</option>
                                </select>

                                <div class="h-4 w-px bg-slate-200 mx-1"></div>

                                <!-- Bold -->
                                <button type="button" onclick="execCmd('bold')" class="p-1 hover:bg-slate-100 rounded font-bold px-2 text-slate-700" title="Bold">B</button>
                                
                                <!-- Italic -->
                                <button type="button" onclick="execCmd('italic')" class="p-1 hover:bg-slate-100 rounded italic px-2 text-slate-700" title="Italic">I</button>
                                
                                <div class="h-4 w-px bg-slate-200 mx-1"></div>

                                <!-- Underline -->
                                <button type="button" onclick="execCmd('underline')" class="p-1 hover:bg-slate-100 rounded text-slate-700" title="Underline">
                                    <i class="fa-solid fa-underline"></i>
                                </button>

                                <!-- Highlight -->
                                <button type="button" onclick="execCmdArg('hiliteColor', '#fef08a')" class="p-1 hover:bg-slate-100 rounded text-slate-700" title="Highlight">
                                    <i class="fa-solid fa-highlighter"></i>
                                </button>

                                <div class="h-4 w-px bg-slate-200 mx-1"></div>

                                <!-- Bullet List -->
                                <button type="button" onclick="execCmd('insertUnorderedList')" class="p-1 hover:bg-slate-100 rounded text-slate-700" title="Bullet List">
                                    <i class="fa-solid fa-list-ul"></i>
                                </button>

                                <!-- Clear Formatting -->
                                <button type="button" onclick="execCmd('removeFormat')" class="p-1 hover:bg-slate-100 rounded text-slate-700" title="Clear Formatting">
                                    <i class="fa-solid fa-eraser"></i>
                                </button>
                            </div>

                            <!-- Visual Rich Text Box -->
                            <div id="visual-editor" contenteditable="true" 
                                 class="w-full min-h-[180px] max-h-[300px] overflow-y-auto bg-white border border-slate-300 rounded-b-xl p-3 text-sm text-slate-800 focus:outline-none focus:ring-2 focus:ring-orange-500 transition border-t-0">
                                {!! old('description') !!}
                            </div>

                            <input type="hidden" name="description" id="hidden-description" value="{{ old('description') }}" required>
                        </div>

                        <!-- Image Upload -->
                        <div class="mt-4 bg-white p-3 rounded-xl border border-slate-200">
                            <label class="block text-xs font-semibold text-slate-600 mb-1">Attachments / Images (Optional)</label>
                            <input type="file" name="image" accept="image/*" class="w-full text-xs text-slate-500 file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-orange-50 file:text-orange-700 hover:file:bg-orange-100 cursor-pointer">
                        </div>

                    </div>
                </div>
            </div>

        </div>

        <!-- RIGHT SECTION: Metadata Sidebar (4 Columns) -->
        <div class="lg:col-span-4 p-4 md:p-6 bg-white flex flex-col justify-between">
            <div class="space-y-4">
                
                <div class="flex items-center justify-between border-b border-slate-200 pb-3">
                    <span class="font-bold text-slate-700 text-sm flex items-center gap-2"> Ticket Details </span>
                </div>

                <!-- Opening Date -->
                <div>
                    <label class="block text-xs font-semibold text-slate-500 mb-1">Opening date</label>
                    <div class="relative">
                        <input type="text" name="opening_date" value="{{ date('Y-m-d H:i') }}" readonly 
                            class="w-full bg-slate-50 border border-slate-300 rounded-xl p-2.5 text-sm text-slate-700 font-medium pr-8 outline-none">
                        <i class="fa-regular fa-calendar absolute right-3 top-3 text-slate-400 text-xs"></i>
                    </div>
                </div>

                <!-- Name -->
                <div>
                    <label class="block text-xs font-semibold text-slate-500 mb-1">Submitted By Name</label>
                    <input type="text" name="user_name" required value="{{ old('user_name', Auth::user()->name ?? Auth::user()->full_name ?? '') }}" 
                        class="w-full border border-slate-300 rounded-xl p-2.5 text-sm text-slate-700 focus:ring-2 focus:ring-orange-500 focus:border-orange-500 outline-none">
                </div>

                <!-- Email -->
                <div>
                    <label class="block text-xs font-semibold text-slate-500 mb-1">Submitted By Email</label>
                    <input type="email" name="user_email" value="{{ old('user_email', Auth::user()->email ?? '') }}"
                        class="w-full border border-slate-300 rounded-xl p-2.5 text-sm text-slate-700 focus:ring-2 focus:ring-orange-500 focus:border-orange-500 outline-none">
                </div>

                <!-- Department -->
                 <div>
                    <label for="department_id" class="block text-xs font-extrabold uppercase tracking-wider text-slate-400 mb-1.5">Submitted To</label>
                    <select name="department_id" id="department_select" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs font-bold text-slate-700 focus:ring-2 focus:ring-orange-500 outline-none transition">
                        <option value="">-- Select Department --</option>
                        @foreach($departments as $id => $name)
                            <option value="{{ $id }}">{{ $name }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Type -->
                <div>
                    <label class="block text-xs font-semibold text-slate-500 mb-1">Type</label>
                    <select name="type" required class="w-full border border-slate-300 rounded-xl p-2.5 text-sm bg-white text-slate-700 focus:ring-2 focus:ring-orange-500 focus:border-orange-500 outline-none cursor-pointer">
                        @php
                            $typeList = $types ?? ['Incident', 'Request', 'Hardware', 'Software', 'Network', 'Facility', 'Others'];
                        @endphp
                        @foreach ($typeList as $type)
                            <option value="{{ $type }}" {{ old('type') == $type ? 'selected' : '' }}>
                                {{ $type }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Category -->
                <div>
                    <label class="block text-xs font-semibold text-slate-500 mb-1">Category</label>
                    <select name="category" required class="w-full border border-slate-300 rounded-xl p-2.5 text-sm bg-white text-slate-700 focus:ring-2 focus:ring-orange-500 focus:border-orange-500 outline-none cursor-pointer">
                        <option value="">Choose Category...</option>
                        @php
                            $categoryList = $categories ?? ['Hardware', 'Software', 'Network', 'Facility', 'Others'];
                        @endphp
                        @foreach ($categoryList as $category)
                            <option value="{{ $category }}" {{ old('category') == $category ? 'selected' : '' }}>
                                {{ $category }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Priority -->
                <div>
                    <label class="block text-xs font-semibold text-slate-500 mb-1">Priority</label>
                    <select name="priority" required class="w-full border border-slate-300 rounded-xl p-2.5 text-sm bg-white text-slate-700 focus:ring-2 focus:ring-orange-500 focus:border-orange-500 outline-none cursor-pointer">
                        @php
                            $priorityList = $priorities ?? ['Low', 'Medium', 'High', 'Urgent'];
                        @endphp
                        @foreach ($priorityList as $priority)
                            <option value="{{ $priority }}" {{ old('priority', 'Medium') == $priority ? 'selected' : '' }}>
                                {{ $priority }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Location -->
                <div>
                    <label class="block text-xs font-semibold text-slate-500 mb-1">Location</label>
                    <input type="text" name="location" required value="{{ old('location') }}" placeholder="e.g. Level 9 Lab"
                        class="w-full border border-slate-300 rounded-xl p-2.5 text-sm text-slate-700 focus:ring-2 focus:ring-orange-500 focus:border-orange-500 outline-none">
                </div>

                <!-- Status -->
                <div>
                    <label class="block text-xs font-semibold text-slate-500 mb-1">Status</label>
                    <div class="w-full border border-slate-300 rounded-xl p-2.5 bg-slate-50 text-sm font-medium text-slate-700 flex items-center gap-2">
                        <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 inline-block"></span> New
                    </div>
                </div>

            </div>

            <!-- Action Buttons -->
            <div class="pt-6 mt-6 border-t border-slate-100 flex items-center justify-end gap-2">
                <a href="{{ route('superadmin.tickets.index') }}" class="px-4 py-2 border border-slate-300 text-slate-600 hover:bg-slate-50 rounded-xl text-xs font-semibold transition">
                    Cancel
                </a>
                <button type="submit" class="bg-orange-500 hover:bg-orange-600 text-white font-bold px-5 py-2 rounded-xl text-sm transition flex items-center gap-1.5 shadow-sm cursor-pointer">
                    <i class="fa-solid fa-plus text-xs"></i> Add Ticket
                </button>
            </div>

        </div>

    </form>
</div>

<!-- JavaScript Visual Command Sync -->
<script>

    function execCmd(command) {
        document.execCommand(command, false, null);
        syncEditorContent();
    }

    function execCmdArg(command, arg) {
        document.execCommand(command, false, arg);
        syncEditorContent();
    }

    const editor = document.getElementById('visual-editor');
    const hiddenInput = document.getElementById('hidden-description');

    function syncEditorContent() {
        hiddenInput.value = editor.innerHTML.trim();
    }

    editor.addEventListener('input', syncEditorContent);
    editor.addEventListener('keyup', syncEditorContent);
    editor.addEventListener('blur', syncEditorContent);

    document.querySelector('form').addEventListener('submit', function() {
        syncEditorContent();
    });
</script>
@endsection