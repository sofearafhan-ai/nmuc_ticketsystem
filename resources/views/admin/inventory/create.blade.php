@extends('layouts.admin')

@section('title', 'Admin - Add New Inventories')

@section('content')
<div class="max-w-5xl mx-auto space-y-6" x-data="inventoryForm()">

    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center bg-[#0d1527] p-6 rounded-2xl text-white shadow-md gap-4">
        <div class="flex items-center gap-3">
            <span class="bg-orange-600 text-white text-[10px] font-extrabold px-3 py-1 rounded-full uppercase tracking-wider shadow-sm">
                {{ $adminDept }} DEPARTMENT
            </span>
            <h1 class="text-xl font-bold">New Inventories Register</h1>
        </div>

        <a href="{{ route('admin.inventory.index') }}" class="px-4 py-2 bg-orange-500 hover:bg-orange-600 text-white font-semibold text-xs rounded-xl transition shadow-md flex items-center gap-2 cursor-pointer">
            <i class="fa-solid fa-arrow-left"></i> Back To List
        </a>
    </div>

    <!-- Form Container -->
    <form action="{{ route('admin.inventory.store') }}" method="POST" class="space-y-6" enctype="multipart/form-data">
        @csrf

        @if ($errors->any())
            <div class="p-4 bg-red-50 border-l-4 border-red-500 rounded-r-xl text-xs text-red-700">
                <p class="font-bold">Please review the form information. There are mandatory fields that have not been filled in correctly.</p>
            </div>
        @endif

        <div class="bg-white rounded-2xl shadow-sm border border-slate-200/80 p-6 space-y-6">

            <template x-for="(item, index) in items" :key="index">
                <div class="space-y-4 pb-6" :class="{ 'border-b border-slate-100': index !== items.length - 1 }">
                    
                    <div class="flex justify-between items-center">
                        <span class="text-xs font-extrabold text-orange-600 bg-orange-50 px-3 py-1 rounded-full border border-orange-200/60 flex items-center gap-1.5">
                            <i class="fa-solid fa-box text-orange-500"></i> Item #<span x-text="index + 1"></span>
                        </span>

                        <button type="button" @click="removeItem(index)" x-show="items.length > 1" 
                                class="text-xs text-red-500 hover:text-red-700 font-bold flex items-center gap-1.5 px-3 py-1 bg-red-50 hover:bg-red-100 rounded-lg transition cursor-pointer">
                            <i class="fa-solid fa-trash-can"></i> Delete
                        </button>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Item Name <span class="text-red-500">*</span></label>
                            <input type="text" :name="`items[${index}][item_name]`" x-model="item.item_name" required 
                                   class="w-full px-3.5 py-2.5 bg-slate-50/60 border border-slate-200 rounded-xl text-xs text-slate-800 focus:ring-2 focus:ring-orange-500 focus:bg-white outline-none transition">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Category <span class="text-red-500">*</span></label>
                            <select :name="`items[${index}][item_category]`" x-model="item.item_category" required 
                                    class="w-full px-3.5 py-2.5 bg-slate-50/60 border border-slate-200 rounded-xl text-xs text-slate-800 focus:ring-2 focus:ring-orange-500 focus:bg-white outline-none transition">
                                <option value="Hardware">Hardware</option>
                                <option value="Software">Software</option>
                                <option value="Network">Network</option>
                                <option value="Facility">Facility</option>
                                <option value="Others">Others</option>
                            </select>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Model <span class="text-red-500">*</span></label>
                            <input type="text" :name="`items[${index}][item_model]`" x-model="item.item_model" required 
                                   class="w-full px-3.5 py-2.5 bg-slate-50/60 border border-slate-200 rounded-xl text-xs text-slate-800 focus:ring-2 focus:ring-orange-500 focus:bg-white outline-none transition">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Location <span class="text-red-500">*</span></label>
                            <input type="text" :name="`items[${index}][item_location]`" x-model="item.item_location" required 
                                   class="w-full px-3.5 py-2.5 bg-slate-50/60 border border-slate-200 rounded-xl text-xs text-slate-800 focus:ring-2 focus:ring-orange-500 focus:bg-white outline-none transition">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Attachment (Optional)</label>
                            <input type="file" :name="`items[${index}][item_attachment]`" 
                                class="w-full px-3.5 py-2.5 bg-slate-50/60 border border-slate-200 rounded-xl text-xs text-slate-800 focus:ring-2 focus:ring-orange-500 focus:bg-white outline-none transition">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Status <span class="text-red-500">*</span></label>
                            <select :name="`items[${index}][item_status]`" x-model="item.item_status" required 
                                    class="w-full px-3.5 py-2.5 bg-slate-50/60 border border-slate-200 rounded-xl text-xs text-slate-800 focus:ring-2 focus:ring-orange-500 focus:bg-white outline-none transition">
                                <option value="Active">Active</option>
                                <option value="Inactive">Inactive</option>
                            </select>
                        </div>
                    </div>

                </div>
            </template>

            <div class="pt-4 border-t border-slate-100 flex flex-col sm:flex-row items-center justify-between gap-4">
                <!-- Add Item Button (Left) -->
                <button type="button" @click="addItem()" 
                        class="w-full sm:w-auto px-5 py-2.5 bg-[#0d1527] hover:bg-slate-800 text-white font-bold text-xs rounded-xl shadow transition flex items-center justify-center gap-2 cursor-pointer">
                    <i class="fa-solid fa-plus text-orange-500"></i> Add Item
                </button>

                <!-- Cancel & Save Buttons (Right) -->
                <div class="flex items-center gap-3 w-full sm:w-auto justify-end">
                    <a href="{{ route('admin.inventory.index') }}" 
                       class="px-6 py-2.5 bg-slate-200 hover:bg-slate-300 text-slate-700 font-bold text-xs rounded-xl transition flex items-center justify-center">
                        Cancel
                    </a>
                    <button type="submit" 
                            class="px-7 py-2.5 bg-orange-500 hover:bg-orange-600 text-white font-bold text-xs rounded-xl shadow-md transition flex items-center justify-center gap-2 cursor-pointer">
                        <i class="fa-solid fa-floppy-disk"></i> Save
                    </button>
                </div>
            </div>

        </div>

    </form>
</div>

<script>
    function inventoryForm() {
        return {
            items: [
                { item_name: '', item_category: 'Hardware', item_model: '', item_location: '', item_status: 'Active' }
            ],
            addItem() {
                this.items.push({
                    item_name: '',
                    item_category: 'Hardware',
                    item_model: '',
                    item_location: '',
                    item_status: 'Active'
                });
            },
            removeItem(index) {
                if (this.items.length > 1) {
                    this.items.splice(index, 1);
                }
            }
        }
    }
</script>
@endsection