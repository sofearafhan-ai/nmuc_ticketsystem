@extends('layouts.admin')

@section('title', 'Admin - Edit Inventory')

@section('content')
<div class="max-w-5xl mx-auto space-y-6" x-data="editInventoryForm()">

    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center bg-[#0d1527] p-6 rounded-2xl text-white shadow-md gap-4">
        <div>
            <div class="flex items-center gap-2 mb-1">
                <span class="bg-orange-600/90 text-white text-[10px] font-extrabold px-3 py-1 rounded-full uppercase tracking-wider shadow-sm">
                    {{ Auth::user()->department->department_code ?? Auth::user()->department->code ?? 'all' }} DEPARTMENT
                </span>
            </div>
            <h1 class="text-xl font-bold">Edit Asset #{{ $rowNum ?? $item->item_id }}</h1>
            <p class="text-xs text-slate-400 mt-0.5">Update the existing inventory details.</p>
        </div>

        <a href="{{ route('admin.inventory.index') }}" class="px-4 py-2.5 bg-orange-500 hover:bg-orange-600 text-white font-semibold text-xs rounded-xl transition shadow-md flex items-center gap-2 cursor-pointer">
            <i class="fa-solid fa-arrow-left"></i> Back to List
        </a>
    </div>

    <!-- Edit Form Card -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200/80 p-6 md:p-8">

        <form action="{{ route('admin.inventory.update', $item->item_id) }}" method="POST" enctype="multipart/form-data" class="space-y-8">
            @csrf
            @method('PUT')

            @if ($errors->any())
                <div class="p-4 bg-red-50 border-l-4 border-red-500 rounded-r-xl text-xs text-red-700 space-y-1">
                    <p class="font-bold">Please review the form information. There are mandatory fields that have not been filled in correctly:</p>
                    <ul class="list-disc pl-5">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- Section 1: Asset Information -->
            <div class="space-y-4">
                <h3 class="text-sm font-extrabold text-slate-800 tracking-tight">
                    Asset Information
                </h3>

                <!-- 1. Name & Type -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1.5">Item Name <span class="text-red-500">*</span></label>
                        <input type="text" name="item_name" value="{{ old('item_name', $item->item_name) }}" required 
                               class="w-full px-4 py-2.5 bg-slate-50/60 border border-slate-200 rounded-xl text-xs text-slate-800 focus:ring-2 focus:ring-orange-500 focus:bg-white outline-none transition">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1.5">Type <span class="text-red-500">*</span></label>
                        <input type="text" name="item_type" value="{{ old('item_type', $item->item_type) }}" required 
                               class="w-full px-4 py-2.5 bg-slate-50/60 border border-slate-200 rounded-xl text-xs text-slate-800 focus:ring-2 focus:ring-orange-500 focus:bg-white outline-none transition">
                    </div>
                </div>

                <!-- 2. Manufacturer & Model -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1.5">Manufacturer <span class="text-red-500">*</span></label>
                        <input type="text" name="item_manufacturer" value="{{ old('item_manufacturer', $item->item_manufacturer) }}" required 
                               class="w-full px-4 py-2.5 bg-slate-50/60 border border-slate-200 rounded-xl text-xs text-slate-800 focus:ring-2 focus:ring-orange-500 focus:bg-white outline-none transition">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1.5">Model <span class="text-red-500">*</span></label>
                        <input type="text" name="item_model" value="{{ old('item_model', $item->item_model) }}" required 
                               class="w-full px-4 py-2.5 bg-slate-50/60 border border-slate-200 rounded-xl text-xs text-slate-800 focus:ring-2 focus:ring-orange-500 focus:bg-white outline-none transition">
                    </div>
                </div>

                <!-- 3. Serial Number, Location & Status -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1.5">Serial Number <span class="text-red-500">*</span></label>
                        <input type="text" name="serial_number" value="{{ old('serial_number', $item->serial_number) }}" required 
                               class="w-full px-4 py-2.5 bg-slate-50/60 border border-slate-200 rounded-xl text-xs text-slate-800 focus:ring-2 focus:ring-orange-500 focus:bg-white outline-none transition">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1.5">Location <span class="text-red-500">*</span></label>
                        <input type="text" name="item_location" value="{{ old('item_location', $item->item_location) }}" required 
                               class="w-full px-4 py-2.5 bg-slate-50/60 border border-slate-200 rounded-xl text-xs text-slate-800 focus:ring-2 focus:ring-orange-500 focus:bg-white outline-none transition">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1.5">Status <span class="text-red-500">*</span></label>
                        <select name="item_status" required 
                                class="w-full px-4 py-2.5 bg-slate-50/60 border border-slate-200 rounded-xl text-xs text-slate-800 font-medium focus:ring-2 focus:ring-orange-500 focus:bg-white outline-none transition cursor-pointer">
                            <option value="Active" {{ old('item_status', $item->item_status) == 'Active' ? 'selected' : '' }}>Active</option>
                            <option value="Inactive" {{ old('item_status', $item->item_status) == 'Inactive' ? 'selected' : '' }}>Inactive</option>
                        </select>
                    </div>
                </div>
            </div>

            <hr class="border-slate-100">

            <!-- Section 2: Supporting Document (Attachment) -->
            <div class="space-y-4">
                <h3 class="text-sm font-extrabold text-slate-800 tracking-tight">
                    Supporting Document
                </h3>

                @if ($item->item_attachment)
                    <!-- Existing Attachment Display Box -->
                    <div class="p-4 bg-slate-50 rounded-2xl border border-slate-200/80 flex items-center justify-between gap-4">
                        <div class="flex items-center gap-3.5">
                            <div class="w-10 h-10 rounded-xl {{ str_ends_with(strtolower($item->item_attachment), '.pdf') ? 'bg-red-100 text-red-600' : 'bg-orange-100 text-orange-600' }} flex items-center justify-center text-lg shrink-0">
                                @if(str_ends_with(strtolower($item->item_attachment), '.pdf'))
                                    <i class="fa-solid fa-file-pdf"></i>
                                @else
                                    <i class="fa-solid fa-file-image"></i>
                                @endif
                            </div>
                            <div>
                                <h4 class="text-xs font-extrabold text-slate-800">Existing attachment</h4>
                                <p class="text-[11px] text-slate-400 mt-0.5">View or upload below to replace your file</p>
                            </div>
                        </div>

                        <div class="flex items-center gap-2">
                            <!-- View Modal Trigger Button -->
                            <button type="button" @click="openPreview = true" 
                                    class="p-2 text-slate-500 hover:text-orange-600 hover:bg-orange-50 rounded-xl transition cursor-pointer"
                                    title="View Attachment">
                                <i class="fa-regular fa-eye text-base"></i>
                            </button>

                            <!-- Delete Attachment Button -->
                            <button type="submit" 
                                    form="delete-attachment-form" 
                                    onclick="return confirm('Are you sure you want to delete this attachment?');"
                                    class="p-2 text-slate-400 hover:text-red-600 hover:bg-red-50 rounded-xl transition cursor-pointer"
                                    title="Delete Attachment">
                                <i class="fa-regular fa-trash-can text-base"></i>
                            </button>
                        </div>
                    </div>
                @endif

                <!-- Upload New File Dropzone Input -->
                <div x-show="!filePreview && !fileName"
                     class="relative border-2 border-dashed border-orange-300 hover:border-orange-500 bg-orange-50/30 hover:bg-orange-50/60 rounded-2xl p-6 text-center transition group cursor-pointer">
                    <input type="file" 
                           name="item_attachment" 
                           id="edit-file-input"
                           accept=".pdf,.jpg,.jpeg,.png,image/jpeg,image/png,application/pdf"
                           @change="handleFileChange($event)"
                           class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10">
                    
                    <div class="space-y-2 flex flex-col items-center justify-center pointer-events-none">
                        <div class="w-10 h-10 rounded-full bg-orange-100 text-orange-500 flex items-center justify-center text-lg group-hover:scale-110 transition">
                            <i class="fa-solid fa-cloud-arrow-up"></i>
                        </div>
                        <div>
                            <p class="text-xs font-bold text-slate-800">Click to upload or replace attachment</p>
                            <p class="text-[11px] text-slate-400 mt-0.5">Click to browse or drag and drop your file</p>
                            <p class="text-[10px] text-slate-400 font-medium">PDF, JPG, PNG (Optional)</p>
                        </div>
                    </div>
                </div>

                <!-- Selected New File Preview -->
                <div x-show="filePreview || fileName" 
                     class="p-4 bg-slate-50 border border-slate-200 rounded-2xl flex items-center justify-between gap-4 shadow-sm"
                     x-cloak>
                    
                    <div class="flex items-center gap-3 overflow-hidden">
                        <template x-if="isImage">
                            <img :src="filePreview" 
                                 class="w-14 h-14 object-cover rounded-xl border border-slate-200 shrink-0 shadow-sm">
                        </template>

                        <template x-if="!isImage">
                            <div class="w-14 h-14 bg-red-100 text-red-500 rounded-xl flex items-center justify-center text-2xl shrink-0 font-bold">
                                <i class="fa-solid fa-file-pdf"></i>
                            </div>
                        </template>

                        <div class="truncate">
                            <p class="text-xs font-extrabold text-slate-800 truncate" x-text="fileName"></p>
                            <p class="text-[10px] text-slate-400 mt-0.5" x-text="isImage ? 'New image file selected' : 'New PDF document selected'"></p>
                        </div>
                    </div>

                    <button type="button" 
                            @click="removeFile()"
                            class="px-3 py-1.5 bg-red-50 hover:bg-red-500 text-red-600 hover:text-white border border-red-200 rounded-xl text-xs font-bold transition flex items-center gap-1.5 shrink-0 cursor-pointer">
                        <i class="fa-solid fa-trash-can"></i>
                        <span>Remove</span>
                    </button>
                </div>
            </div>

            <!-- Form Action Buttons -->
            <div class="pt-4 border-t border-slate-100 flex items-center justify-end gap-3">
                <a href="{{ route('admin.inventory.index') }}" 
                   class="px-6 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-600 font-bold text-xs rounded-xl transition">
                    Cancel
                </a>
                <button type="submit" 
                        class="px-6 py-2.5 bg-orange-500 hover:bg-orange-600 text-white font-bold text-xs rounded-xl shadow-md transition flex items-center gap-2 cursor-pointer">
                    <i class="fa-solid fa-floppy-disk"></i> Update Asset
                </button>
            </div>
        </form>
    </div>

    <!-- Modal Preview Attachment -->
    @if ($item->item_attachment)
        @php
            $fileUrl = asset('storage/' . $item->item_attachment);
            $isPdf   = strtolower(pathinfo($item->item_attachment, PATHINFO_EXTENSION)) === 'pdf';
        @endphp

        <div x-show="openPreview" 
             x-cloak
             @keydown.escape.window="openPreview = false" 
             class="fixed inset-0 z-50 flex items-center justify-center bg-slate-950/70 backdrop-blur-xs p-4">
            
            <div class="fixed inset-0" @click="openPreview = false"></div>

            <div class="relative bg-white p-4 rounded-3xl shadow-2xl {{ $isPdf ? 'max-w-3xl' : 'max-w-lg' }} w-full z-10 space-y-3 border border-slate-100 text-left">
                <div class="flex justify-between items-center px-1">
                    <span class="text-xs font-bold text-slate-800 uppercase tracking-wider flex items-center gap-2">
                        <i class="fa-solid fa-paperclip text-orange-500"></i> Attachment Preview
                    </span>
                    <div class="flex items-center gap-2">
                        @if ($isPdf)
                            <a href="{{ $fileUrl }}" target="_blank"
                               class="px-3 py-1 rounded-full bg-orange-50 hover:bg-orange-500 text-orange-600 hover:text-white transition text-[11px] font-bold flex items-center gap-1">
                                <i class="fa-solid fa-up-right-from-square text-[10px]"></i> Open Tab
                            </a>
                        @endif
                        <button @click="openPreview = false" type="button" class="w-7 h-7 rounded-full bg-slate-100 hover:bg-red-100 text-slate-400 hover:text-red-500 transition flex items-center justify-center text-xs cursor-pointer">
                            <i class="fa-solid fa-xmark"></i>
                        </button>
                    </div>
                </div>

                <div class="overflow-hidden rounded-2xl bg-slate-900 border border-slate-200/80 max-h-[75vh] flex items-center justify-center p-2">
                    @if ($isPdf)
                        <iframe src="{{ $fileUrl }}" class="w-full h-[68vh] rounded-xl bg-white"></iframe>
                    @else
                        <img src="{{ $fileUrl }}" 
                             alt="Attachment Preview" 
                             class="max-w-full max-h-[65vh] object-contain rounded-xl">
                    @endif
                </div>
            </div>
        </div>
    @endif

    <!-- Hidden Form for Attachment Deletion -->
    <form id="delete-attachment-form" action="{{ route('admin.inventory.delete-attachment', $item->item_id) }}" method="POST" class="hidden">
        @csrf
        @method('DELETE')
    </form>

</div>

<script>
function editInventoryForm() {
    return {
        openPreview: false,
        fileName: '',
        filePreview: null,
        isImage: false,
        handleFileChange(event) {
            const file = event.target.files[0];
            if (file) {
                this.fileName = file.name;
                this.isImage = file.type.startsWith('image/');

                if (this.isImage) {
                    const reader = new FileReader();
                    reader.onload = (e) => {
                        this.filePreview = e.target.result;
                    };
                    reader.readAsDataURL(file);
                } else {
                    this.filePreview = null;
                }
            }
        },
        removeFile() {
            this.fileName = '';
            this.filePreview = null;
            this.isImage = false;

            const input = document.getElementById('edit-file-input');
            if (input) {
                input.value = '';
            }
        }
    }
}
</script>
@endsection