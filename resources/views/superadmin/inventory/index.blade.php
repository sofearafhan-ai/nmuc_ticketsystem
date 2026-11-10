@extends('layouts.superadmin')

@section('title', 'Inventory Management')

@section('content')
<div class="space-y-6">

    <!-- PAGE HEADER & STATS -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center bg-[#0d1527] p-6 rounded-3xl text-white shadow-lg gap-4">
        <div>
            <div class="flex items-center gap-2 mb-1">
                <h1 class="text-2xl font-bold">Inventory Management</h1>
            </div>
            <p class="text-xs text-slate-400">Monitor, filter, and audit departmental hardware and global asset records.</p>
        </div>

        <!-- Quick Summary Stats -->
        @if(isset($inventories) && request('department'))
            <div class="flex items-center gap-3 bg-slate-800/60 p-2 px-4 rounded-2xl border border-slate-700/50">
                <div class="text-center px-2">
                    <span class="block text-xs font-bold text-slate-400 uppercase">Total Items</span>
                    <span class="text-lg font-extrabold text-white">{{ $inventories->total() ?? count($inventories) }}</span>
                </div>
                <div class="h-6 w-px bg-slate-700"></div>
                <div class="text-center px-2">
                    <span class="block text-xs font-bold text-slate-400 uppercase">Dept</span>
                    <span class="text-xs font-bold text-orange-400 truncate max-w-[100px] block">{{ request('department') }}</span>
                </div>
            </div>
        @endif
    </div>

    @if(!request('department'))

        <!-- DEPARTMENT SELECTION SECTION -->
        <div class="bg-white rounded-3xl p-6 md:p-8 shadow-sm border border-slate-200/80 space-y-6">
            
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 border-b border-slate-100 pb-5">
                <div>
                    <h2 class="text-base font-bold text-slate-800 flex items-center gap-2">
                        <i class="fa-solid fa-building-circle-check text-orange-500"></i>
                        Select a Department
                    </h2>
                    <p class="text-xs text-slate-400 mt-0.5">Choose a departmental vault to access its itemized inventory catalogue.</p>
                </div>

                <!-- Global Search Box -->
                <form action="{{ route('superadmin.inventory.index') }}" method="GET" class="w-full md:w-80">
                    <div class="relative">
                        <i class="fa-solid fa-magnifying-glass absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                        <input type="text" name="search" value="{{ request('search') }}" 
                               placeholder="Search department..." 
                               class="w-full pl-10 pr-4 py-2.5 bg-slate-50 border border-slate-200 rounded-2xl text-xs text-slate-800 focus:ring-2 focus:ring-orange-500 focus:bg-white outline-none transition">
                    </div>
                </form>
            </div>
            
            <!-- Departments Grid -->
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-5">
                @forelse($departments as $dept)
                    @php
                        $deptName = is_object($dept) ? ($dept->department_name ?? $dept->name) : $dept;
                        $deptCode = is_object($dept) ? ($dept->code ?? 'DEPT') : 'N/A';
                    @endphp
                    <div class="group bg-white hover:bg-orange-50/20 p-5 rounded-2xl border border-slate-200/90 hover:border-orange-300 shadow-xs hover:shadow-md transition-all duration-300 flex flex-col justify-between relative overflow-hidden">
                        
                        <div class="space-y-3">
                            <div class="flex items-center justify-between">
                                <div class="w-12 h-12 rounded-2xl bg-orange-100/70 text-orange-600 flex items-center justify-center text-xl group-hover:scale-110 transition-transform duration-300">
                                    <i class="fa-solid fa-building"></i>
                                </div>
                                <span class="bg-slate-100 group-hover:bg-orange-100 text-slate-500 group-hover:text-orange-700 text-[10px] font-extrabold px-2.5 py-1 rounded-lg uppercase tracking-wider transition">
                                    {{ $deptCode }}
                                </span>
                            </div>

                            <div>
                                <h3 class="font-bold text-slate-800 text-sm group-hover:text-orange-600 transition truncate" title="{{ $deptName }}">
                                    {{ $deptName }}
                                </h3>
                            </div>
                        </div>
                        
                        <a href="{{ route('superadmin.inventory.index', ['department' => $deptName]) }}" 
                           class="mt-5 w-full py-2.5 bg-slate-900 group-hover:bg-orange-500 text-white text-xs font-bold rounded-xl shadow-xs transition-all duration-300 text-center flex items-center justify-center gap-2 group-hover:shadow-md">
                            <span>View Inventory</span>
                            <i class="fa-solid fa-arrow-right text-[10px] group-hover:translate-x-1 transition-transform"></i>
                        </a>
                    </div>
                @empty
                    <div class="col-span-full text-center py-12 bg-slate-50 rounded-2xl border border-dashed border-slate-200">
                        <i class="fa-solid fa-building-circle-exclamation text-4xl text-slate-300 block mb-3"></i>
                        <h4 class="text-sm font-bold text-slate-700">No Departments Found</h4>
                        <p class="text-xs text-slate-400 mt-1">There are no active departments registered in the system right now.</p>
                    </div>
                @endforelse
            </div>
        </div>

    @else

        <!-- FILTER & TOOLBAR CARD -->
        <div class="bg-white rounded-3xl p-5 md:p-6 shadow-sm border border-slate-200/80 space-y-4">
            
            <!-- Breadcrumb Navigation -->
            <div class="flex items-center justify-between border-b border-slate-100 pb-4">
                <div class="flex items-center gap-2 flex-wrap text-xs">
                    <a href="{{ route('superadmin.inventory.index') }}" 
                       class="px-3 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-600 rounded-xl font-bold transition flex items-center gap-2">
                        <i class="fa-solid fa-arrow-left text-[11px]"></i> All Departments
                    </a>
                    <span class="text-slate-300">/</span>
                    <span class="px-3 py-1.5 bg-orange-50 text-orange-600 font-extrabold rounded-xl border border-orange-200 uppercase tracking-wider">
                        <i class="fa-solid fa-building mr-1.5 text-[10px]"></i> {{ request('department') }}
                    </span>
                </div>

                @if(request()->hasAny(['search', 'type', 'status']))
                    <a href="{{ route('superadmin.inventory.index', ['department' => request('department')]) }}" 
                       class="text-xs text-slate-400 hover:text-red-500 font-semibold flex items-center gap-1 transition"
                       title="Reset Filters">
                        <i class="fa-solid fa-rotate-right"></i> Reset All Filters
                    </a>
                @endif
            </div>

            <!-- Filter Form -->
            <form action="{{ route('superadmin.inventory.index') }}" method="GET" class="space-y-4">
                <input type="hidden" name="department" value="{{ request('department') }}">
                
                <div class="grid grid-cols-1 md:grid-cols-12 gap-3">
                    
                    <!-- Search Bar (Span 6) -->
                    <div class="md:col-span-6 relative">
                        <i class="fa-solid fa-magnifying-glass absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                        <input type="text" name="search" value="{{ request('search') }}" 
                               placeholder="Search item name, model, serial no, or location..." 
                               class="w-full pl-10 pr-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-800 focus:ring-2 focus:ring-orange-500 focus:bg-white outline-none transition">
                    </div>

                    <!-- Status Dropdown (Span 3) -->
                    <div class="md:col-span-3">
                        <select name="status" onchange="this.form.submit()" 
                                class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-700 font-medium focus:ring-2 focus:ring-orange-500 outline-none transition cursor-pointer">
                            <option value="">All Statuses</option>
                            <option value="Active" {{ request('status') == 'Active' ? 'selected' : '' }}>Active</option>
                            <option value="Inactive" {{ request('status') == 'Inactive' ? 'selected' : '' }}>Inactive</option>
                        </select>
                    </div>

                </div>

                <!-- Action Toolbar Buttons -->
                <div class="flex items-center justify-end gap-2 pt-2 border-t border-slate-100">
                    <button type="submit" 
                            class="px-5 py-2 bg-orange-500 hover:bg-orange-600 text-white font-bold text-xs rounded-xl shadow-sm transition flex items-center gap-2 cursor-pointer">
                        <i class="fa-solid fa-filter text-[11px]"></i> Apply Filter
                    </button>

                    <a href="{{ route('admin.inventory.export', request()->all()) }}"
                       class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs rounded-xl shadow-sm transition flex items-center gap-2 cursor-pointer"
                       title="Export to Excel">
                       <i class="fa-solid fa-file-excel text-[11px]"></i> Export Excel
                    </a>
                </div>

            </form>
        </div>

        <!-- INVENTORY TABLE CARD -->
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="p-6">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="border-b border-slate-100 bg-slate-50/80 text-slate-400 uppercase text-[10px] font-extrabold tracking-wider">
                            <th class="py-2 px-2 text-center">NO</th>
                            <th class="py-2 px-2 text-center">NAME</th>
                            <th class="py-2 px-2 text-center">TYPE</th>
                            <th class="py-2 px-2 text-center">MANUFACTURER</th>
                            <th class="py-2 px-2 text-center">MODEL</th>
                            <th class="py-2 px-2 text-center">SERIAL NO.</th>
                            <th class="py-2 px-2 text-center">LOCATION</th>
                            <th class="py-2 px-2 text-center">STATUS</th>
                            <th class="py-2 px-2 text-center">REGISTERED BY</th>
                            <th class="py-2 px-2 text-center">ATTACHMENT</th>
                            <th class="py-2 px-2 text-center">ACTIONS</th>
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
                                                <div class="relative bg-white p-4 rounded-3xl shadow-2xl max-w-lg w-full z-10 space-y-3 border border-slate-100 text-left">
                                                    <div class="flex justify-between items-center px-1">
                                                        <span class="text-xs font-bold text-slate-800 uppercase tracking-wider flex items-center gap-2">
                                                            <i class="fa-solid fa-paperclip text-orange-500"></i> Attachment Preview
                                                        </span>
                                                        <button @click="open = false" type="button" class="w-7 h-7 rounded-full bg-slate-100 hover:bg-red-100 text-slate-400 hover:text-red-500 transition flex items-center justify-center text-xs">
                                                            <i class="fa-solid fa-xmark"></i>
                                                        </button>
                                                    </div>
                                                    <div class="overflow-hidden rounded-2xl bg-slate-900 border border-slate-200/80 max-h-[70vh] flex items-center justify-center p-2">
                                                        <img src="{{ asset('storage/' . $item->item_attachment) }}" 
                                                             alt="Attachment" 
                                                             class="max-w-full max-h-[65vh] object-contain rounded-xl">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @else
                                        <span class="text-[11px] text-slate-400 italic block text-center">-</span>
                                    @endif
                                </td>

                                <!-- Actions -->
                                <td class="py-3.5 px-4 text-center">
                                    <div class="flex items-center justify-center gap-1.5">
                                        <form action="{{ route('superadmin.inventory.destroy', $item->item_id ?? $item->id) }}" method="POST" 
                                              onsubmit="return confirm('Are you sure you want to delete this asset record?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" 
                                                    title="Delete Item"
                                                    class="p-2 bg-red-50 hover:bg-red-600 text-red-600 hover:text-white rounded-xl text-xs font-semibold transition border border-red-200 hover:border-red-600 flex items-center justify-center cursor-pointer">
                                                <i class="fa-solid fa-trash-can text-[11px]"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>

                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="p-10 text-center text-slate-400">
                                    <i class="fa-solid fa-boxes-stacked text-3xl mb-2 text-slate-300 block"></i>
                                    <p class="text-xs font-bold text-slate-600">No inventory records found</p>
                                    <p class="text-[11px] text-slate-400 mt-0.5">There are no items registered under {{ request('department') }} Department yet.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination Container -->
            @if (method_exists($inventories, 'hasPages') && $inventories->hasPages())
                <div class="p-4 border-t border-slate-100 bg-slate-50/50">
                    {{ $inventories->links() }}
                </div>
            @endif
        </div>

    @endif

</div>
@endsection