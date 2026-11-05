@extends('layouts.admin')

@section('title', 'New Ticket - Helpdesk')

@section('content')
<div class="space-y-6">

    <!-- Validation Error Alert -->
    @if($errors->any())
        <div class="bg-red-50 border-l-4 border-red-500 p-4 rounded-xl shadow-sm text-red-700">
            <div class="flex items-center gap-2 text-red-600 font-bold mb-1 text-sm">
                <i class="fa-solid fa-triangle-exclamation"></i> Please check the following errors:
            </div>
            <ul class="list-disc pl-5 text-xs text-red-600 space-y-1">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    
    <!-- Form Container -->
    <form action="{{ route('tickets.store') }}" method="POST" enctype="multipart/form-data"
          class="w-full bg-white border border-slate-200/80 rounded-2xl shadow-sm overflow-hidden grid grid-cols-1 lg:grid-cols-12 min-h-[650px] text-slate-700">
        @csrf

        <!-- LEFT SECTION: Main Form -->
        <div class="lg:col-span-8 p-4 md:p-6 border-b lg:border-b-0 lg:border-r border-slate-200 bg-slate-50/50 flex flex-col justify-between">

            <div class="space-y-5">
                <!-- Header User Info -->
                <div class="flex items-center gap-4 mb-4">
                    <div class="w-12 h-12 rounded-xl bg-orange-500 text-white font-bold flex items-center justify-center text-lg shadow-sm shrink-0">
                        <span>{{ strtoupper(substr(Auth::user()->name ?? 'U', 0, 1)) }}</span>
                    </div>
                    <div>
                        <h3 class="text-sm font-bold text-slate-800">{{ Auth::user()->full_name ?? Auth::user()->name ?? 'User' }}</h3>
                        <p class="text-xs text-slate-400 font-normal">Create New Ticket</p>
                    </div>
                </div>

                <div class="bg-sky-50 border-l-4 border-sky-500 rounded-lg p-3 text-xs text-sky-800 flex items-center gap-2 shadow-xs">
                    <i class="fa-solid fa-circle-info text-sky-500 text-sm"></i>
                    <span>This ticket will be submitted to your selected department for resolution.</span>
                </div>

                <!-- Title Input -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Title <span class="text-red-500">*</span></label>
                    <input type="text" name="title" required value="{{ old('title') }}" placeholder="Briefly describe the ticket subject..."
                        class="w-full bg-white border border-slate-300 rounded-xl p-2.5 text-sm text-slate-800 focus:ring-2 focus:ring-orange-500 focus:border-orange-500 outline-none transition">
                </div>

                <!-- Description Editor -->
                <div>
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
                            <i class="fa-solid fa-rotate-left"></i>
                        </button>
                    </div>

                    <!-- Visual Text Box -->
                    <div id="visual-editor" contenteditable="true" 
                        class="w-full min-h-[180px] max-h-[300px] overflow-y-auto bg-white border border-slate-300 rounded-b-xl p-3 text-sm text-slate-800 focus:outline-none focus:ring-2 focus:ring-orange-500 transition border-t-0">
                        {!! old('description') !!}
                    </div>

                    <input type="hidden" name="description" id="hidden-description" value="{{ old('description') }}" required>
                </div>

                <!-- Image Upload -->
                <div class="bg-white p-3.5 rounded-xl border border-slate-200 space-y-2">
                    <label class="block text-xs font-semibold text-slate-600">Attachments / Images (Optional)</label>
                    
                    <input type="file" id="ticketImageInput" name="image" accept=".jpg,.jpeg,.png,.pdf" 
                        class="w-full text-xs text-slate-500 file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-orange-50 file:text-orange-700 hover:file:bg-orange-100 cursor-pointer">

                    <!-- Container Preview & Cancel File -->
                    <div id="imagePreviewContainer" class="hidden items-center justify-between bg-orange-50/80 px-3 py-2 rounded-xl border border-orange-200 text-xs text-orange-800">
                        <div class="flex items-center gap-2 truncate pr-2">
                            <i id="fileTypeIcon" class="fa-solid fa-file text-orange-500 text-sm"></i>
                            <span id="fileNameDisplay" class="font-semibold truncate">Selected file name</span>
                        </div>
                        <button type="button" id="cancelImageBtn" class="px-2 py-1 bg-red-100 hover:bg-red-200 text-red-600 rounded-lg text-xs font-bold transition shrink-0 cursor-pointer" title="Cancel/Remove File">
                            <i class="fa-solid fa-xmark mr-1"></i> Cancel
                        </button>
                    </div>
                </div>

            </div>

        </div>

        <!-- RIGHT SECTION: Metadata Sidebar -->
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
                        <i class="fa-regular fa-calendar absolute right-3 top-3.5 text-slate-400 text-xs"></i>
                    </div>
                </div>

                <!-- Name -->
                <div>
                    <label class="block text-xs font-semibold text-slate-500 mb-1">Submitted By Name</label>
                    <input type="text" name="user_name" readonly value="{{ old('user_name', Auth::user()->full_name ?? Auth::user()->name ?? '') }}" 
                        class="w-full border border-slate-200 bg-slate-100/80 rounded-xl p-2.5 text-sm text-slate-500 cursor-not-allowed outline-none select-none">
                </div>

                <!-- Email -->
                <div>
                    <label class="block text-xs font-semibold text-slate-500 mb-1">Submitted By Email</label>
                    <input type="email" name="user_email" readonly value="{{ old('user_email', Auth::user()->email ?? '') }}" 
                        class="w-full border border-slate-200 bg-slate-100/80 rounded-xl p-2.5 text-sm text-slate-500 cursor-not-allowed outline-none select-none">
                </div>

                <!-- Department -->
                <div>
                    <label for="department_select" class="block text-xs font-semibold text-slate-500 mb-1">Submitted To (Department) <span class="text-red-500">*</span></label>
                    <select name="department_id" id="department_select" required class="w-full p-2.5 bg-white border border-slate-300 rounded-xl text-sm font-medium text-slate-700 focus:ring-2 focus:ring-orange-500 outline-none transition cursor-pointer">
                        <option value="">-- Select Department --</option>
                        @foreach($departments as $dept)
                            <option value="{{ is_object($dept) ? ($dept->department_id ?? $dept->id) : $loop->index }}" 
                                {{ old('department_id') == (is_object($dept) ? ($dept->department_id ?? $dept->id) : $loop->index) ? 'selected' : '' }}>
                                {{ is_object($dept) ? ($dept->name ?? $dept->department_name) : $dept }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Type -->
                <div>
                    <label class="block text-xs font-semibold text-slate-500 mb-1">Type <span class="text-red-500">*</span></label>
                    <select name="type" required class="w-full border border-slate-300 rounded-xl p-2.5 text-sm bg-white text-slate-700 focus:ring-2 focus:ring-orange-500 focus:border-orange-500 outline-none cursor-pointer">
                        @foreach ($types as $type)
                            <option value="{{ $type }}" {{ old('type') == $type ? 'selected' : '' }}>
                                {{ $type }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Category -->
                <div>
                    <label class="block text-xs font-semibold text-slate-500 mb-1">Category <span class="text-red-500">*</span></label>
                    <select name="category" required class="w-full border border-slate-300 rounded-xl p-2.5 text-sm bg-white text-slate-700 focus:ring-2 focus:ring-orange-500 focus:border-orange-500 outline-none cursor-pointer">
                        <option value="">Choose Category...</option>
                        @foreach ($categories as $category)
                            <option value="{{ $category }}" {{ old('category') == $category ? 'selected' : '' }}>
                                {{ $category }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Priority -->
                <div>
                    <label class="block text-xs font-semibold text-slate-500 mb-1">Priority <span class="text-red-500">*</span></label>
                    <select name="priority" required class="w-full border border-slate-300 rounded-xl p-2.5 text-sm bg-white text-slate-700 focus:ring-2 focus:ring-orange-500 focus:border-orange-500 outline-none cursor-pointer">
                        @foreach ($priorities as $priority)
                            <option value="{{ $priority }}" {{ old('priority', 'Medium') == $priority ? 'selected' : '' }}>
                                {{ $priority }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Location -->
                <div>
                    <label class="block text-xs font-semibold text-slate-500 mb-1">Location <span class="text-red-500">*</span></label>
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
                <a href="{{ route('tickets.index') }}" class="px-4 py-2 border border-slate-300 text-slate-600 hover:bg-slate-50 rounded-xl text-xs font-semibold transition">
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

    document.addEventListener('DOMContentLoaded', function () {
    const fileInput = document.getElementById('ticketImageInput');
    const previewContainer = document.getElementById('imagePreviewContainer');
    const fileNameDisplay = document.getElementById('fileNameDisplay');
    const fileTypeIcon = document.getElementById('fileTypeIcon');
    const cancelBtn = document.getElementById('cancelImageBtn');

    if (fileInput) {
        fileInput.addEventListener('change', function () {
            if (this.files && this.files[0]) {
                const file = this.files[0];
                const fileName = file.name;
                const ext = fileName.split('.').pop().toLowerCase();
                const allowedExtensions = ['jpg', 'jpeg', 'png', 'pdf'];

                if (!allowedExtensions.includes(ext)) {
                    alert('File format not allowed! Please select only JPG, JPEG, PNG, or PDF files.');
                    this.value = '';
                    previewContainer.classList.add('hidden');
                    previewContainer.classList.remove('flex');
                    return;
                }

                if (ext === 'pdf') {
                    fileTypeIcon.className = 'fa-solid fa-file-pdf text-red-500 text-sm';
                } else {
                    fileTypeIcon.className = 'fa-solid fa-image text-orange-500 text-sm';
                }

                fileNameDisplay.textContent = fileName;
                previewContainer.classList.remove('hidden');
                previewContainer.classList.add('flex');
            }
        });

        if (cancelBtn) {
            cancelBtn.addEventListener('click', function () {
                fileInput.value = ''; 
                previewContainer.classList.add('hidden');
                previewContainer.classList.remove('flex');
            });
        }
    }
});
</script>
@endsection