@extends('layouts.admin')

@section('title', 'Admin - Edit Inventory')

@section('content')
<div class="max-w-5xl mx-auto space-y-6" x-data="{ openPreview: false }">

    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center bg-[#0d1527] p-6 rounded-2xl text-white shadow-md gap-4">
        <div>
            <div class="flex items-center gap-2 mb-1">
                <span class="bg-orange-600/90 text-white text-[10px] font-extrabold px-3 py-1 rounded-full uppercase tracking-wider shadow-sm">
                    {{ $adminDept }} DEPARTMENT
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

            <!-- Section 1: Asset Information -->
            <div class="space-y-5">
                <h3 class="text-sm font-extrabold text-slate-800 tracking-tight">
                    Asset Information
                </h3>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <!-- Item Name -->
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1.5">Item Name <span class="text-red-500">*</span></label>
                        <input type="text" name="item_name" value="{{ old('item_name', $item->item_name) }}" required 
                               placeholder="e.g. Laptop"
                               class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-800 focus:ring-2 focus:ring-orange-500 focus:bg-white outline-none transition">
                    </div>

                    <!-- Category -->
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1.5">Category <span class="text-red-500">*</span></label>
                        <select name="item_category" required 
                                class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-800 focus:ring-2 focus:ring-orange-500 focus:bg-white outline-none transition cursor-pointer">
                            <option value="Hardware" {{ $item->item_category == 'Hardware' ? 'selected' : '' }}>Hardware</option>
                            <option value="Software" {{ $item->item_category == 'Software' ? 'selected' : '' }}>Software</option>
                            <option value="Network" {{ $item->item_category == 'Network' ? 'selected' : '' }}>Network</option>
                            <option value="Facility" {{ $item->item_category == 'Facility' ? 'selected' : '' }}>Facility</option>
                            <option value="Others" {{ $item->item_category == 'Others' ? 'selected' : '' }}>Others</option>
                        </select>
                    </div>

                    <!-- Model -->
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1.5">Model <span class="text-red-500">*</span></label>
                        <input type="text" name="item_model" value="{{ old('item_model', $item->item_model) }}" required 
                               placeholder="e.g. Acer"
                               class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-800 focus:ring-2 focus:ring-orange-500 focus:bg-white outline-none transition">
                    </div>

                    <!-- Location -->
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1.5">Location <span class="text-red-500">*</span></label>
                        <input type="text" name="item_location" value="{{ old('item_location', $item->item_location) }}" required 
                               placeholder="e.g. Level 14 Lab"
                               class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-800 focus:ring-2 focus:ring-orange-500 focus:bg-white outline-none transition">
                    </div>

                    <!-- Status (Full Width on Grid or Half Width) -->
                    <div class="md:col-span-2">
                        <label class="block text-xs font-bold text-slate-700 mb-1.5">Status <span class="text-red-500">*</span></label>
                        <select name="item_status" required 
                                class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-800 focus:ring-2 focus:ring-orange-500 focus:bg-white outline-none transition cursor-pointer">
                            <option value="Active" {{ $item->item_status == 'Active' ? 'selected' : '' }}>Active</option>
                            <option value="Inactive" {{ $item->item_status == 'Inactive' ? 'selected' : '' }}>Inactive</option>
                        </select>
                    </div>
                </div>
            </div>

            <hr class="border-slate-100">

            <!-- Section 2: Attachment -->
            <div class="space-y-4">
                <h3 class="text-sm font-extrabold text-slate-800 tracking-tight">
                    Attachment
                </h3>

                @if ($item->item_attachment)
                    <!-- Existing Attachment Display Box -->
                    <div class="p-4 bg-slate-50 rounded-2xl border border-slate-200/80 flex items-center justify-between gap-4">
                        <div class="flex items-center gap-3.5">
                            <div class="w-10 h-10 rounded-xl bg-red-100 text-red-600 flex items-center justify-center text-lg shrink-0">
                                @if(str_contains(strtolower($item->item_attachment), '.pdf'))
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
                <div class="relative border-2 border-dashed border-slate-200 hover:border-orange-400 bg-slate-50/50 hover:bg-orange-50/30 rounded-2xl p-5 text-center transition group cursor-pointer">
                    <input type="file" name="item_attachment" 
                           accept=".pdf,.jpg,.jpeg,.png,image/jpeg,image/png,application/pdf"
                           class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10">
                    
                    <div class="space-y-1.5 flex flex-col items-center justify-center">
                        <div class="w-9 h-9 rounded-full bg-orange-100/80 text-orange-500 flex items-center justify-center text-base group-hover:scale-110 transition">
                            <i class="fa-solid fa-cloud-arrow-up"></i>
                        </div>
                        <p class="text-xs font-bold text-slate-700">Click to upload or replace attachment</p>
                        <p class="text-[10px] text-slate-400">PDF, JPG, PNG up to 2MB (Optional)</p>
                    </div>
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
        <div x-show="openPreview" 
             x-cloak
             @keydown.escape.window="openPreview = false" 
             class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 backdrop-blur-sm p-4">
            
            <div class="fixed inset-0" @click="openPreview = false"></div>

            <div class="relative bg-white p-4 rounded-2xl shadow-2xl max-w-2xl w-full z-10 space-y-3 border border-slate-100 text-left">
                <div class="flex justify-between items-center px-1">
                    <span class="text-xs font-bold text-slate-700 uppercase tracking-wider">Attachment Preview</span>
                    <button @click="openPreview = false" type="button" class="text-slate-400 hover:text-red-500 transition text-sm">
                        <i class="fa-solid fa-xmark"></i>
                    </button>
                </div>

                <div class="overflow-hidden rounded-xl bg-slate-50 border border-slate-200 max-h-[70vh] flex items-center justify-center p-2">
                    @if(str_contains(strtolower($item->item_attachment), '.pdf'))
                        <iframe src="{{ asset('storage/' . $item->item_attachment) }}" class="w-full h-[60vh] rounded-lg"></iframe>
                    @else
                        <img src="{{ asset('storage/' . $item->item_attachment) }}" 
                             alt="Attachment Preview" 
                             class="max-w-full max-h-[60vh] object-contain rounded-lg">
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
@endsection