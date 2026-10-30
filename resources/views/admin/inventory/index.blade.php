@extends('layouts.admin')

@section('title', 'Admin - ' . $adminDept . ' Inventory')

@section('content')
<div class="space-y-6">

    <!-- Header Section -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center bg-[#0d1527] p-6 rounded-2xl text-white shadow-md gap-4">
        <div>
            <div class="flex items-center gap-2">
                <span class="bg-orange-600 text-white text-[10px] font-extrabold px-3 py-1 rounded-full uppercase tracking-wider shadow-sm">{{ $adminDept }} DEPARTMENT</span>
                <h1 class="text-2xl font-bold">Asset Inventory</h1>
            </div>
            <p class="text-xs text-slate-400 mt-1">Managing the list of devices and assets under the {{ $adminDept }} Department.</p>
        </div>

        <!-- Link to New Inventory Form Page -->
        <a href="{{ route('admin.inventory.create') }}" class="px-4 py-2.5 bg-orange-500 hover:bg-orange-600 text-white font-semibold text-xs rounded-xl transition shadow-md flex items-center gap-2 cursor-pointer">
            <i class="fa-solid fa-plus"></i> Add Asset
        </a>
    </div>

    @if (session('success'))
        <div class="bg-emerald-900/90 border-l-4 border-emerald-400 text-emerald-100 px-4 py-3 rounded-xl shadow-sm text-sm flex items-center gap-2">
            <i class="fa-solid fa-circle-check text-emerald-300"></i>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    <!-- FILTER CARD SECTION -->
    <div class="bg-white rounded-2xl p-5 shadow-sm border border-slate-200/80">
        <form action="{{ route('admin.inventory.index') }}" method="GET" class="space-y-4">
            
            <!-- Top Search Input & Filter Button -->
            <div class="flex flex-col sm:flex-row items-center gap-3">
                <div class="relative flex-1 w-full">
                    <i class="fa-solid fa-magnifying-glass absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 text-sm"></i>
                    <input type="text" name="search" value="{{ request('search') }}" 
                           placeholder="Search by name, model, or location..." 
                           class="w-full pl-11 pr-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-800 focus:ring-2 focus:ring-orange-500 focus:bg-white outline-none transition">
                </div>

                <div class="flex items-center gap-2 w-full sm:w-auto">
                    <button type="submit" 
                            class="flex-1 sm:flex-initial px-6 py-2.5 bg-orange-500 hover:bg-orange-600 text-white font-bold text-xs rounded-xl shadow-md transition flex items-center justify-center gap-2 cursor-pointer">
                        <i class="fa-solid fa-filter"></i> Filter
                    </button>

                    <a href="{{ route('admin.inventory.export', request()->all()) }}"
                       class="px-4 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs rounded-xl shadow-md transition flex items-center gap-2 cursor-pointer"
                       title="Export to Excel">
                       <i class="fa-solid fa-file-excel"></i> Export Excel
                    </a>

                    @if(request()->hasAny(['search', 'category', 'status']))
                        <a href="{{ route('admin.inventory.index') }}" 
                           class="px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-600 font-bold text-xs rounded-xl transition flex items-center gap-1.5"
                           title="Reset Filters">
                            <i class="fa-solid fa-rotate-right"></i> Reset
                        </a>
                    @endif
                </div>
            </div>

            <hr class="border-slate-100">

            <!-- Bottom Dropdown Filters -->
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4 pt-1">
                <!-- Category Filter -->
                <div>
                    <label class="block text-[11px] font-extrabold uppercase tracking-wider text-slate-500 mb-1.5">Category</label>
                    <select name="category" onchange="this.form.submit()" 
                            class="w-full px-3.5 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-700 font-medium focus:ring-2 focus:ring-orange-500 outline-none transition cursor-pointer">
                        <option value="">All Categories</option>
                        <option value="Hardware" {{ request('category') == 'Hardware' ? 'selected' : '' }}>Hardware</option>
                        <option value="Software" {{ request('category') == 'Software' ? 'selected' : '' }}>Software</option>
                        <option value="Network" {{ request('category') == 'Network' ? 'selected' : '' }}>Network</option>
                        <option value="Facility" {{ request('category') == 'Facility' ? 'selected' : '' }}>Facility</option>
                        <option value="Others" {{ request('category') == 'Others' ? 'selected' : '' }}>Others</option>
                    </select>
                </div>

                <!-- Status Filter -->
                <div>
                    <label class="block text-[11px] font-extrabold uppercase tracking-wider text-slate-500 mb-1.5">Status</label>
                    <select name="status" onchange="this.form.submit()" 
                            class="w-full px-3.5 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-700 font-medium focus:ring-2 focus:ring-orange-500 outline-none transition cursor-pointer">
                        <option value="">All Statuses</option>
                        <option value="Active" {{ request('status') == 'Active' ? 'selected' : '' }}>Active</option>
                        <option value="Inactive" {{ request('status') == 'Inactive' ? 'selected' : '' }}>Inactive</option>
                    </select>
                </div>
            </div>

        </form>
    </div>

    <!-- Inventory Table Card -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="p-6">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="border-b border-slate-200 bg-slate-50 text-slate-700 uppercase text-[11px] font-bold tracking-wider">
                            <th class="p-3 text-center">Item ID</th>
                            <th class="p-3 text-center">Item Name</th>
                            <th class="p-3 text-center">Category</th>
                            <th class="p-3 text-center">Model / Specs</th>
                            <th class="p-3 text-center">Location</th>
                            <th class="p-3 text-center">Status</th>
                            <th class="p-3 text-center">Registered By</th>
                            <th class="p-3 text-center">Attachment</th>
                            <th class="p-3 text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200 text-sm">
                        @forelse ($inventories as $index => $item)
                            <tr class="hover:bg-orange-50/40 transition">
                                <td class="py-3 px-4 text-xs font-extrabold text-slate-900">#{{ $inventories->firstItem() + $index }}</td>
                                <td class="py-3 px-4 text-xs text-slate-900 text-center">{{ $item->item_name }}</td>
                                <td class="py-3 px-4 text-xs font-extrabold text-slate-900 text-center">
                                    <span class="text-xs bg-slate-100 text-slate-700 px-2.5 py-1 rounded-lg border border-slate-200 font-medium">
                                        {{ $item->item_category }}
                                    </span>
                                </td>
                                <td class="py-3 px-4 text-xs text-slate-900 text-center">{{ $item->item_model }}</td>
                                <td class="py-3 px-4 text-xs text-slate-900 text-center">{{ $item->item_location }}</td>
                                <td class="py-3 px-4 text-xs font-extrabold text-slate-900 text-center">
                                    <span class="px-2.5 py-1 text-[11px] font-bold rounded-lg text-white
                                        {{ $item->item_status == 'Active' ? 'bg-emerald-600' : 'bg-slate-500' }}">
                                        {{ $item->item_status }}
                                    </span>
                                </td>
                                <td class="py-3 px-4 text-xs text-slate-900 text-center">
                                    {{ $item->user->full_name ?? $item->user->name ?? 'Admin' }}
                                </td>
                                <td class="py-3 px-4 text-xs text-slate-900 text-center">
                                    @if ($item->item_attachment)
                                        <div x-data="{ open: false }" class="flex justify-center">
                                          
                                            <button @click="open = true" 
                                                    type="button" 
                                                    class="p-2 bg-slate-100 hover:bg-orange-500 text-slate-600 hover:text-white rounded-lg transition border border-slate-200 shadow-sm flex items-center justify-center cursor-pointer mx-auto"
                                                    title="View Image">
                                                <i class="fa-solid fa-eye text-xs"></i>
                                            </button>

                                            <div x-show="open" 
                                                x-cloak
                                                @keydown.escape.window="open = false" 
                                                class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 backdrop-blur-sm p-4">
                                                
                                                <div class="fixed inset-0" @click="open = false"></div>

                                                <div class="relative bg-white p-3 rounded-2xl shadow-2xl max-w-lg w-full z-10 space-y-3 border border-slate-100 text-left">
                                                    <div class="flex justify-between items-center px-1">
                                                        <span class="text-xs font-bold text-slate-700 uppercase tracking-wider">Attachment Preview</span>
                                                        <button @click="open = false" type="button" class="text-slate-400 hover:text-red-500 transition text-sm">
                                                            <i class="fa-solid fa-xmark"></i>
                                                        </button>
                                                    </div>

                                                    <div class="overflow-hidden rounded-xl bg-slate-50 border border-slate-200/80 max-h-[70vh] flex items-center justify-center">
                                                        <img src="{{ asset('storage/' . $item->item_attachment) }}" 
                                                            alt="Attachment" 
                                                            class="max-w-full max-h-[65vh] object-contain rounded-lg">
                                                    </div>
                                                </div>
                                            </div>

                                        </div>
                                    @else
                                    
                                        <span class="py-3 px-4 text-xs text-slate-400 italic block text-center">No Attachment</span>
                                    @endif
                                </td>
                                <td class="p-3">
                                    <div class="flex items-center justify-center gap-2">
                                        <!-- Edit Button -->
                                       <a href="{{ route('admin.inventory.edit', ['id' => $item->item_id, 'row_num' => $inventories->firstItem() + $index]) }}" 
                                          class="px-2.5 py-1.5 bg-slate-100 hover:bg-orange-500 text-slate-600 hover:text-white rounded-lg text-xs font-semibold transition border border-slate-200 hover:border-orange-500 flex items-center gap-1 cursor-pointer">
                                            <i class="fa-solid fa-pen-to-square"></i>
                                        </a>

                                        <!-- Delete Button -->
                                        <form action="{{ route('admin.inventory.destroy', $item->item_id) }}" method="POST" 
                                              onsubmit="return confirm('Adakah anda pasti ingin memadamkan aset ini?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" 
                                                    title="Delete Item"
                                                    class="px-2.5 py-1.5 bg-red-50 hover:bg-red-600 text-red-600 hover:text-white rounded-lg text-xs font-semibold transition border border-red-200 hover:border-red-600 flex items-center gap-1 cursor-pointer">
                                                <i class="fa-solid fa-trash-can"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="p-8 text-center text-slate-500 font-medium">
                                    <i class="fa-solid fa-boxes-stacked text-2xl mb-2 text-slate-300 block"></i>
                                    No inventory data registered for the {{ $adminDept }} Department.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($inventories->hasPages())
                <div class="mt-6">
                    {{ $inventories->links() }}
                </div>
            @endif
        </div>
    </div>

</div>
@endsection