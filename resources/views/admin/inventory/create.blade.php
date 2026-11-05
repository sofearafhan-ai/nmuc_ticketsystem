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

        <div class="bg-white rounded-2xl shadow-sm border border-slate-200/80 p-6 md:p-8 space-y-8">

            <!-- Dynamic Multi-Item Loop -->
            <template x-for="(item, index) in items" :key="index">
                <div class="space-y-6 pb-8" :class="{ 'border-b border-slate-200': index !== items.length - 1 }">
                    
                    <!-- Item Header & Delete Button -->
                    <div class="flex justify-between items-center bg-slate-50 p-4 rounded-xl border border-slate-100">
                        <div>
                            <h2 class="text-base font-bold text-slate-800 flex items-center gap-2">
                                Item #<span x-text="index + 1"></span>
                            </h2>
                            <p class="text-xs text-slate-400">Fill in the details below to register asset #<span x-text="index + 1"></span>.</p>
                        </div>

                        <button type="button" @click="removeItem(index)" x-show="items.length > 1" 
                                class="text-xs text-red-500 hover:text-red-700 font-bold flex items-center gap-1.5 px-3 py-1.5 bg-red-50 hover:bg-red-100 rounded-lg transition cursor-pointer">
                            <i class="fa-solid fa-trash-can"></i> Delete Item
                        </button>
                    </div>

                    <!-- 01 — Basic Information -->
                    <div class="space-y-4 pt-2">
                        <h3 class="text-sm font-extrabold text-slate-800 tracking-tight flex items-center gap-2">
                            <span class="text-orange-500 font-black">01 —</span> Basic Information
                        </h3>

                        <!-- Item Name (Full Width) -->
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1.5">Item Name <span class="text-red-500">*</span></label>
                            <input type="text" :name="`items[${index}][item_name]`" x-model="item.item_name" required 
                                   placeholder="e.g. Dell Latitude Laptop"
                                   class="w-full px-4 py-2.5 bg-slate-50/60 border border-slate-200 rounded-xl text-xs text-slate-800 focus:ring-2 focus:ring-orange-500 focus:bg-white outline-none transition">
                        </div>

                        <!-- Category & Model Grid -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1.5">Category <span class="text-red-500">*</span></label>
                                <select :name="`items[${index}][item_category]`" x-model="item.item_category" required 
                                        class="w-full px-4 py-2.5 bg-slate-50/60 border border-slate-200 rounded-xl text-xs text-slate-800 focus:ring-2 focus:ring-orange-500 focus:bg-white outline-none transition cursor-pointer">
                                    <option value="Hardware">Hardware</option>
                                    <option value="Software">Software</option>
                                    <option value="Network">Network</option>
                                    <option value="Facility">Facility</option>
                                    <option value="Others">Others</option>
                                </select>
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1.5">Model <span class="text-red-500">*</span></label>
                                <input type="text" :name="`items[${index}][item_model]`" x-model="item.item_model" required 
                                       placeholder="e.g. Latitude 5440"
                                       class="w-full px-4 py-2.5 bg-slate-50/60 border border-slate-200 rounded-xl text-xs text-slate-800 focus:ring-2 focus:ring-orange-500 focus:bg-white outline-none transition">
                            </div>
                        </div>
                    </div>

                    <!-- 02 — Location & Status -->
                    <div class="space-y-4 pt-2">
                        <h3 class="text-sm font-extrabold text-slate-800 tracking-tight flex items-center gap-2">
                            <span class="text-orange-500 font-black">02 —</span> Location & Status
                        </h3>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1.5">Location <span class="text-red-500">*</span></label>
                                <input type="text" :name="`items[${index}][item_location]`" x-model="item.item_location" required 
                                       placeholder="e.g. IT Department"
                                       class="w-full px-4 py-2.5 bg-slate-50/60 border border-slate-200 rounded-xl text-xs text-slate-800 focus:ring-2 focus:ring-orange-500 focus:bg-white outline-none transition">
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1.5">Status <span class="text-red-500">*</span></label>
                                <select :name="`items[${index}][item_status]`" x-model="item.item_status" required 
                                        class="w-full px-4 py-2.5 bg-slate-50/60 border border-slate-200 rounded-xl text-xs text-slate-700 font-medium focus:ring-2 focus:ring-orange-500 focus:bg-white outline-none transition cursor-pointer">
                                    <option value="Active">Active</option>
                                    <option value="Inactive">Inactive</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- 03 — Supporting Document (Dropzone Upload) -->
                    <div class="space-y-4 pt-2">
                        <h3 class="text-sm font-extrabold text-slate-800 tracking-tight flex items-center gap-2">
                            <span class="text-orange-500 font-black">03 —</span> Supporting Document
                        </h3>

                        <div x-show="!item.filePreview && !item.fileName" 
                            class="relative border-2 border-dashed border-orange-300 hover:border-orange-500 bg-orange-50/30 hover:bg-orange-50/60 rounded-2xl p-6 text-center transition group cursor-pointer">
                            
                            <input type="file" 
                                :name="`items[${index}][item_attachment]`" 
                                :id="`file-input-${index}`"
                                accept=".pdf,.jpg,.jpeg,.png,image/jpeg,image/png,application/pdf"
                                @change="handleFileChange($event, item)"
                                class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10">
                            
                            <div class="space-y-2 flex flex-col items-center justify-center pointer-events-none">
                                <div class="w-10 h-10 rounded-full bg-orange-100 text-orange-500 flex items-center justify-center text-lg group-hover:scale-110 transition">
                                    <i class="fa-solid fa-cloud-arrow-up"></i>
                                </div>
                                
                                <div>
                                    <p class="text-xs font-bold text-slate-800">Upload Attachment</p>
                                    <p class="text-[11px] text-slate-400 mt-0.5">Click to browse or drag and drop your file</p>
                                    <p class="text-[10px] text-slate-400 font-medium">PDF, JPG, PNG (Optional)</p>
                                </div>
                            </div>
                        </div>

                        <div x-show="item.filePreview || item.fileName" 
                            class="p-4 bg-slate-50 border border-slate-200 rounded-2xl flex items-center justify-between gap-4 shadow-sm"
                            x-cloak>
                            
                            <div class="flex items-center gap-3 overflow-hidden">

                                <template x-if="item.isImage">
                                    <img :src="item.filePreview" 
                                        class="w-14 h-14 object-cover rounded-xl border border-slate-200 shrink-0 shadow-sm">
                                </template>

                                <template x-if="!item.isImage">
                                    <div class="w-14 h-14 bg-red-100 text-red-500 rounded-xl flex items-center justify-center text-2xl shrink-0 font-bold">
                                        <i class="fa-solid fa-file-pdf"></i>
                                    </div>
                                </template>

                                <div class="truncate">
                                    <p class="text-xs font-extrabold text-slate-800 truncate" x-text="item.fileName"></p>
                                    <p class="text-[10px] text-slate-400 mt-0.5" x-text="item.isImage ? 'Image file selected' : 'PDF document selected'"></p>
                                </div>
                            </div>

                            <button type="button" 
                                    @click="removeFile(item, index)"
                                    class="px-3 py-1.5 bg-red-50 hover:bg-red-500 text-red-600 hover:text-white border border-red-200 rounded-xl text-xs font-bold transition flex items-center gap-1.5 shrink-0 cursor-pointer">
                                <i class="fa-solid fa-trash-can"></i>
                                <span>Remove</span>
                            </button>
                        </div>
                    </div>
                    </div>

                </div>
            </template>

            <!-- Bottom Control Buttons -->
            <div class="pt-4 border-t border-slate-100 flex flex-col sm:flex-row items-center justify-between gap-4">
                <!-- Add Item Button (Left) -->
                <button type="button" @click="addItem()" 
                        class="w-full sm:w-auto px-5 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-800 font-bold text-xs rounded-xl transition flex items-center justify-center gap-2 cursor-pointer border border-slate-200">
                    <i class="fa-solid fa-plus text-slate-600"></i> Add Another Item
                </button>

                <!-- Cancel & Save Buttons (Right) -->
                <div class="flex items-center gap-3 w-full sm:w-auto justify-end">
                    <a href="{{ route('admin.inventory.index') }}" 
                       class="px-6 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-600 font-bold text-xs rounded-xl transition flex items-center justify-center">
                        Cancel
                    </a>
                    <button type="submit" 
                            class="px-7 py-2.5 bg-orange-500 hover:bg-orange-600 text-white font-bold text-xs rounded-xl shadow-md transition flex items-center justify-center gap-2 cursor-pointer">
                        <i class="fa-solid fa-floppy-disk"></i> Save Inventory
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
            { 
                item_name: '', 
                item_category: 'Hardware', 
                item_model: '', 
                item_location: '', 
                item_status: 'Active',
                fileName: '',
                filePreview: null,
                isImage: false
            }
        ],
        addItem() {
            this.items.push({
                item_name: '',
                item_category: 'Hardware',
                item_model: '',
                item_location: '',
                item_status: 'Active',
                fileName: '',
                filePreview: null,
                isImage: false
            });
        },
        removeItem(index) {
            if (this.items.length > 1) {
                this.items.splice(index, 1);
            }
        },
        handleFileChange(event, item) {
            const file = event.target.files[0];
            if (file) {
                item.fileName = file.name;
                item.isImage = file.type.startsWith('image/');

                if (item.isImage) {
                    const reader = new FileReader();
                    reader.onload = (e) => {
                        item.filePreview = e.target.result;
                    };
                    reader.readAsDataURL(file);
                } else {
                    item.filePreview = null;
                }
            }
        },
        removeFile(item, index) {
            item.fileName = '';
            item.filePreview = null;
            item.isImage = false;

            const input = document.getElementById(`file-input-${index}`);
            if (input) {
                input.value = '';
            }
        }
    }
}
</script>
@endsection