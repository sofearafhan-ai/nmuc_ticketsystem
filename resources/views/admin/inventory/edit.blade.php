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
        <form action="{{ route('admin.inventory.update', $item->item_id) }}" method="POST" class="space-y-4">
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

</div>
@endsection