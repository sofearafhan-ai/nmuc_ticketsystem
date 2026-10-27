@extends('layouts.admin')

@section('title', 'Admin - Edit Inventory')

@section('content')
<div class="max-w-5xl mx-auto space-y-6">

    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center bg-[#0d1527] p-6 rounded-2xl text-white shadow-md gap-4">
        <div class="flex items-center gap-3">
            <span class="bg-orange-600 text-white text-[10px] font-extrabold px-3 py-1 rounded-full uppercase tracking-wider shadow-sm">
                {{ $adminDept }} DEPARTMENT
            </span>
            <h1 class="text-xl font-bold">Edit Asset #{{ $rowNum }}</h1>
        </div>

        <a href="{{ route('admin.inventory.index') }}" class="px-4 py-2.5 bg-orange-500 hover:bg-orange-600 text-white font-semibold text-xs rounded-xl transition shadow-md flex items-center gap-2 cursor-pointer">
            <i class="fa-solid fa-arrow-left"></i> Back To List
        </a>
    </div>

    <!-- Edit Form Card -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6">

        <form action="{{ route('admin.inventory.update', $item->item_id) }}" method="POST" enctype="multipart/form-data" class="space-y-4">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Item Name <span class="text-red-500">*</span></label>
                    <input type="text" name="item_name" value="{{ old('item_name', $item->item_name) }}" required 
                           class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-800 focus:ring-2 focus:ring-orange-500 outline-none transition">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Category <span class="text-red-500">*</span></label>
                    <select name="item_category" required 
                            class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-800 focus:ring-2 focus:ring-orange-500 outline-none transition">
                        <option value="Hardware" {{ $item->item_category == 'Hardware' ? 'selected' : '' }}>Hardware</option>
                        <option value="Software" {{ $item->item_category == 'Software' ? 'selected' : '' }}>Software</option>
                        <option value="Network" {{ $item->item_category == 'Network' ? 'selected' : '' }}>Network</option>
                        <option value="Facility" {{ $item->item_category == 'Facility' ? 'selected' : '' }}>Facility</option>
                        <option value="Others" {{ $item->item_category == 'Others' ? 'selected' : '' }}>Others</option>
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Model <span class="text-red-500">*</span></label>
                    <input type="text" name="item_model" value="{{ old('item_model', $item->item_model) }}" required 
                           class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-800 focus:ring-2 focus:ring-orange-500 outline-none transition">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Location <span class="text-red-500">*</span></label>
                    <input type="text" name="item_location" value="{{ old('item_location', $item->item_location) }}" required 
                           class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-800 focus:ring-2 focus:ring-orange-500 outline-none transition">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Status <span class="text-red-500">*</span></label>
                    <select name="item_status" required 
                            class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-800 focus:ring-2 focus:ring-orange-500 outline-none transition">
                        <option value="Active" {{ $item->item_status == 'Active' ? 'selected' : '' }}>Active</option>
                        <option value="Inactive" {{ $item->item_status == 'Inactive' ? 'selected' : '' }}>Inactive</option>
                    </select>
                </div>
            </div>

            <!-- ATTACHMENT -->
            <div class="pt-2">
                <label class="block text-xs font-bold text-slate-700 mb-1">Attachment (Optional)</label>

                @if ($item->item_attachment)
                    <div class="mb-3 p-3 bg-slate-50 rounded-xl border border-slate-200 flex items-center justify-between gap-3">
                        <div class="flex items-center gap-3">
                            <img src="{{ asset('storage/' . $item->item_attachment) }}" 
                                 alt="Attachment" 
                                 class="w-12 h-12 object-cover rounded-lg border border-slate-200 shadow-sm">
                            <div>
                                <p class="text-xs font-bold text-slate-700">Current Attachment</p>
                                <p class="text-[11px] text-slate-400">Upload a new file below if you wish to replace it.</p>
                            </div>
                        </div>

                        <div class="flex items-center gap-3">
                            <a href="{{ asset('storage/' . $item->item_attachment) }}" target="_blank" 
                               class="px-3 py-1.5 bg-slate-200 hover:bg-orange-500 hover:text-white text-slate-700 text-xs font-semibold rounded-lg transition flex items-center gap-1.5">
                                <i class="fa-solid fa-eye"></i> View
                            </a>

                            <button type="submit" 
                                    form="delete-attachment-form" 
                                    onclick="return confirm('Are you sure you want to delete this attachment?');"
                                    class="px-3 py-1.5 bg-red-50 hover:bg-red-500 text-red-600 hover:text-white border border-red-200 text-xs font-semibold rounded-lg transition flex items-center gap-1.5 cursor-pointer">
                                <i class="fa-solid fa-trash-can"></i> Delete
                            </button>
                        </div>
                    </div>
                @endif

                <input type="file" name="item_attachment" 
                       class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-800 focus:ring-2 focus:ring-orange-500 outline-none transition">
            </div>

            <div class="pt-4 border-t border-slate-100 flex justify-end gap-3">
                <a href="{{ route('admin.inventory.index') }}" class="px-5 py-2.5 bg-slate-200 hover:bg-slate-300 text-slate-700 font-bold text-xs rounded-xl transition">
                    Cancel
                </a>
                <button type="submit" class="px-6 py-2.5 bg-orange-500 hover:bg-orange-600 text-white font-bold text-xs rounded-xl shadow-md transition flex items-center gap-2 cursor-pointer">
                    <i class="fa-solid fa-floppy-disk"></i> Update Asset
                </button>
            </div>
        </form>
    </div>

<form id="delete-attachment-form" action="{{ route('admin.inventory.delete-attachment', $item->item_id) }}" method="POST" class="hidden">
    @csrf
    @method('DELETE')
</form>

</div>
@endsection