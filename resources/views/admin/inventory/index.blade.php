@extends('layouts.admin')

@section('title', 'Admin - Inventory List')

@section('content')
<div class="space-y-6">

    <!-- Header Section -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center bg-[#0d1527] p-6 rounded-2xl text-white shadow-md gap-4">
        <div>
            <div class="flex items-center gap-2">
                <span class="bg-orange-600 text-white text-[10px] font-extrabold px-3 py-1 rounded-full uppercase tracking-wider shadow-sm">{{ Auth::user()->department->department_code ?? Auth::user()->department->code ?? 'all' }} DEPARTMENT</span>
                <h1 class="text-2xl font-bold">Asset Inventory</h1>
            </div>
            <p class="text-xs text-slate-400 mt-1">Managing the list of devices and assets under the {{ Auth::user()->department->department_code ?? Auth::user()->department->code ?? 'all' }} Department.</p>
        </div>

        <!-- Link to New Inventory Form Page -->
        <a href="{{ route('admin.inventory.create') }}" class="px-4 py-2.5 bg-orange-500 hover:bg-orange-600 text-white font-semibold text-xs rounded-xl transition shadow-md flex items-center gap-2 cursor-pointer">
            <i class="fa-solid fa-plus"></i> Add Asset
        </a>
    </div>

    @if(session('success'))
        <div class="bg-emerald-50 border-l-4 border-emerald-500 p-4 rounded-xl shadow-sm text-emerald-800 flex items-start justify-between">
            <div class="flex items-center gap-3 text-sm font-medium">
                <i class="fa-solid fa-circle-check text-emerald-500 text-lg shrink-0"></i>
                <div>
                    <h4 class="font-bold text-emerald-900">Success!</h4>
                    <p class="text-xs text-emerald-700 mt-0.5">{{ session('success') }}</p>
                </div>
            </div>
            <button type="button" onclick="this.parentElement.remove()" class="text-emerald-500 hover:text-emerald-700 text-xs p-1">
                <i class="fa-solid fa-xmark"></i>
            </button>
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
                           placeholder="Search..." 
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
            <div class="grid grid-cols-1 sm:grid-cols-1 gap-3 pt-1">
             
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
                        <tr class="border-b border-orange-100 bg-orange-50 text-orange-400 uppercase text-[11px] font-bold tracking-wider">
                            <th class="py-1 px-1 text-center">NO</th>
                            <th class="py-1 px-1 text-center">NAME</th>
                            <th class="py-1 px-1 text-center">TYPE</th>
                            <th class="py-1 px-1 text-center">MANUFACTURER</th>
                            <th class="py-1 px-1 text-center">MODEL</th>
                            <th class="py-1 px-1 text-center">SERIAL NO.</th>
                            <th class="py-1 px-1 text-center">LOCATION</th>
                            <th class="py-1 px-1 text-center">STATUS</th>
                            <th class="py-1 px-1 text-center">REGISTERED BY</th>
                            <th class="py-1 px-1 text-center">ATTACHMENT</th>
                            <th class="py-1 px-1 text-center">ACTIONS</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-xs font-medium text-slate-700">
                        @forelse ($inventories as $index => $item)
                            @php
                                $registeredUser = $item->user->full_name ?? $item->user->name ?? 'System Admin';
                            @endphp
                            <tr class="hover:bg-slate-50/60 transition duration-150">
                                
                                <!-- No -->
                                <td class="py-3.5 px-4 font-bold text-slate-900 text-center">
                                    #{{ method_exists($inventories, 'firstItem') ? ($inventories->firstItem() + $index) : ($index + 1) }}
                                </td>

                                <!-- Item Name -->
                                <td class="py-3.5 px-4 text-center">
                                    <div class="flex items-center gap-3">
                                        <div>
                                            <span class="font-bold text-slate-800 block text-xs">{{ $item->item_name }}</span>
                                        </div>
                                    </div>
                                </td>

                                <!-- Type -->
                                <td class="py-3.5 px-4 text-center">
                                    <div class="flex items-center justify-center gap-1.5">
                                        <span class="text-xs text-slate-700 font-semibold">{{ $item->item_type }}</span>
                                    </div>
                                </td>

                                <!-- Manufacturer -->
                                <td class="py-3.5 px-4 text-center">
                                    <span class="text-[10px] text-slate-400 block font-medium">{{ $item->item_manufacturer ?? '-' }}</span>
                                </td>

                                <!-- Model -->
                                <td class="py-3.5 px-4 text-center">
                                    <span class="font-bold text-slate-800 block">{{ $item->item_model ?? '-' }}</span>
                                </td>

                                 <!-- Serial Number -->
                                <td class="py-2 text-xs text-center font-medium text-slate-500">
                                    {{ $item->serial_number ?? 'N/A' }}
                                </td>

                                <!-- Location -->
                                <td class="p-2 text-xs text-slate-500 font-medium text-center">
                                        {{ $item->item_location ?? 'N/A' }}
                                </td>

                                <!-- Status Badge -->
                                <td class="p-2 text-xs text-slate-500 font-medium text-center">
                                    <span class="px-2.5 py-1 text-[10px] font-bold rounded-full inline-block uppercase tracking-wider border
                                        {{ $item->item_status == 'Active' ? 'bg-emerald-50 text-emerald-700 border-emerald-200' : 'bg-slate-100 text-slate-600 border-slate-200' }}">
                                        {{ $item->item_status }}
                                    </span>
                                </td>

                                <!-- Registered By -->
                                <td class="p-2 text-xs text-slate-500 font-medium text-center">
                                    {{ $registeredUser }}
                                </td>

                                <!-- Attachment Preview Button -->
                                <td class="py-3.5 px-4 text-center">
                                    @if ($item->item_attachment)
                                        @php
                                            $fileUrl = asset('storage/' . $item->item_attachment);
                                            $isPdf   = strtolower(pathinfo($item->item_attachment, PATHINFO_EXTENSION)) === 'pdf';
                                        @endphp

                                        <div x-data="{ open: false }" class="flex justify-center">
                                            <button @click="open = true" 
                                                    type="button" 
                                                    class="px-2.5 py-1.5 bg-slate-100 hover:bg-orange-500 text-slate-600 hover:text-white rounded-xl transition-all border border-slate-200 shadow-xs flex items-center justify-center gap-1.5 cursor-pointer text-xs font-semibold"
                                                    title="View Attachment">
                                                <i class="fa-solid fa-eye text-[11px]"></i>
                                            </button>

                                            <!-- Modal Backdrop & Window -->
                                            <div x-show="open" 
                                                x-cloak
                                                @keydown.escape.window="open = false" 
                                                class="fixed inset-0 z-50 flex items-center justify-center bg-slate-950/70 backdrop-blur-xs p-4">
                                                <div class="fixed inset-0" @click="open = false"></div>
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
                                                            <button @click="open = false" type="button" class="w-7 h-7 rounded-full bg-slate-100 hover:bg-red-100 text-slate-400 hover:text-red-500 transition flex items-center justify-center text-xs cursor-pointer">
                                                                <i class="fa-solid fa-xmark"></i>
                                                            </button>
                                                        </div>
                                                    </div>

                                                    <div class="overflow-hidden rounded-2xl bg-slate-900 border border-slate-200/80 max-h-[75vh] flex items-center justify-center p-2">
                                                        @if ($isPdf)
                                                            <iframe src="{{ $fileUrl }}" class="w-full h-[68vh] rounded-xl bg-white"></iframe>
                                                        @else
                                                            <img src="{{ $fileUrl }}" 
                                                                alt="Attachment" 
                                                                class="max-w-full max-h-[65vh] object-contain rounded-xl">
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @else
                                        <span class="text-[11px] text-slate-400 italic block text-center">-</span>
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
                                              onsubmit="return confirm('Are you sure you want to delete this asset?');">
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
                                <td colspan="9" class="p-8 text-center text-slate-500 font-medium">
                                    <i class="fa-solid fa-boxes-stacked text-2xl mb-2 text-slate-300 block"></i>
                                    No inventory data registered for the {{ Auth::user()->department->department_code ?? Auth::user()->department->code ?? 'all' }} Department.
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