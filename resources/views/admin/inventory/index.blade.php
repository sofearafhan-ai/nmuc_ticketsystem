@extends('layouts.app')

@section('title', 'Admin - ' . $adminDept . ' Inventory')

@section('content')
<div class="space-y-6">

    <!-- Header Section -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center bg-slate-900 p-6 rounded-xl text-white shadow-md gap-4">
        <div>
            <div class="flex items-center gap-2">
                <span class="bg-orange-500 text-white text-[10px] font-extrabold px-2 py-0.5 rounded tracking-wide uppercase">{{ $adminDept }} Department</span>
                <h1 class="text-2xl font-bold">Asset Inventory</h1>
            </div>
            <p class="text-xs text-slate-400 mt-1">Managing the list of devices and assets under the {{ $adminDept }} Department's.</p>
        </div>

        <button onclick="document.getElementById('addModal').classList.remove('hidden')" class="px-4 py-2.5 bg-orange-500 hover:bg-orange-600 text-white font-semibold text-xs rounded-lg transition shadow-md flex items-center gap-2 cursor-pointer">
            <i class="fa-solid fa-plus"></i> Add Asset
        </button>
    </div>

    @if (session('success'))
        <div class="bg-emerald-900/90 border-l-4 border-emerald-400 text-emerald-100 px-4 py-3 rounded-lg shadow-sm text-sm flex items-center gap-2">
            <i class="fa-solid fa-circle-check text-emerald-300"></i>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    <!-- Inventory Table Card -->
    <div class="bg-white rounded-xl shadow-md border border-orange-200 overflow-hidden">
        <div class="p-6">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="border-b border-orange-200 bg-orange-100/60 text-orange-950 uppercase text-[11px] font-bold tracking-wider">
                            <th class="p-3">Item ID</th>
                            <th class="p-3">Item Name</th>
                            <th class="p-3">Category</th>
                            <th class="p-3">Model / Specs</th>
                            <th class="p-3">Location</th>
                            <th class="p-3">Status</th>
                            <th class="p-3">Registered By</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200 text-sm">
                        @forelse ($inventories as $item)
                            <tr class="hover:bg-orange-50/50 transition">
                                <td class="p-3 font-bold text-slate-900">#{{ $item->item_id }}</td>
                                <td class="p-3 font-semibold text-slate-800">{{ $item->item_name }}</td>
                                <td class="p-3">
                                    <span class="text-xs bg-slate-100 text-slate-700 px-2 py-1 rounded border border-slate-200 font-medium">
                                        {{ $item->item_category }}
                                    </span>
                                </td>
                                <td class="p-3 text-xs text-slate-700 font-medium">{{ $item->item_model }}</td>
                                <td class="p-3 text-xs text-slate-600 font-medium">
                                    <i class="fa-solid fa-location-dot text-orange-500 mr-1"></i> {{ $item->item_location }}
                                </td>
                                <td class="p-3">
                                    <span class="px-2.5 py-1 text-xs font-bold rounded text-white
                                        {{ $item->item_status == 'Active' ? 'bg-emerald-600' : 'bg-slate-500' }}">
                                        {{ $item->item_status }}
                                    </span>
                                </td>
                                <td class="p-3 text-xs text-slate-500 font-medium">
                                    {{ $item->user->full_name ?? $item->user->name ?? 'Admin' }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="p-8 text-center text-slate-500 font-medium">
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

<!-- ADD ASSET MODULE -->
<div id="addModal" class="hidden fixed inset-0 bg-slate-950/60 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl max-w-md w-full p-6 space-y-4 shadow-2xl border border-orange-200">
        <div class="flex justify-between items-center border-b pb-3">
            <h3 class="font-bold text-slate-800 text-sm flex items-center gap-2">
                <i class="fa-solid fa-laptop-code text-orange-500"></i> Add New ({{ $adminDept }}) Inventory
            </h3>
            <button onclick="document.getElementById('addModal').classList.add('hidden')" class="text-slate-400 hover:text-slate-600">
                <i class="fa-solid fa-xmark text-lg"></i>
            </button>
        </div>

        <form action="{{ route('admin.inventory.store') }}" method="POST" class="space-y-3 text-xs">
            @csrf
            <div>
                <label class="block font-bold text-slate-700 mb-1">Item Name</label>
                <input type="text" name="item_name" required placeholder="e.g. Dell Latitude 3420" class="w-full p-2.5 bg-slate-50 border rounded-lg">
            </div>

            <div class="grid grid-cols-2 gap-2">
                <div>
                    <label class="block font-bold text-slate-700 mb-1">Category</label>
                    <select name="item_category" required class="w-full p-2.5 bg-slate-50 border rounded-lg">
                        <option value="Hardware">Hardware</option>
                        <option value="Software">Software</option>
                        <option value="Network">Network</option>
                        <option value="Facility">Facility</option>
                        <option value="Others">Others</option>
                    </select>
                </div>
                <div>
                    <label class="block font-bold text-slate-700 mb-1">Status</label>
                    <select name="item_status" required class="w-full p-2.5 bg-slate-50 border rounded-lg">
                        <option value="Active">Active</option>
                        <option value="Inactive">Inactive</option>
                    </select>
                </div>
            </div>

            <div>
                <label class="block font-bold text-slate-700 mb-1">Model / Specs</label>
                <input type="text" name="item_model" required placeholder="e.g. Core i5 11th Gen / 16GB RAM" class="w-full p-2.5 bg-slate-50 border rounded-lg">
            </div>

            <div>
                <label class="block font-bold text-slate-700 mb-1">Location</label>
                <input type="text" name="item_location" required placeholder="e.g. Server Room / Bilik IT" class="w-full p-2.5 bg-slate-50 border rounded-lg">
            </div>

            <div class="flex justify-end gap-2 pt-2">
                <button type="button" onclick="document.getElementById('addModal').classList.add('hidden')" class="px-4 py-2 bg-slate-200 text-slate-700 font-bold rounded-lg">Cancel</button>
                <button type="submit" class="px-4 py-2 bg-orange-500 text-white font-bold rounded-lg shadow">Save</button>
            </div>
        </form>
    </div>
</div>
@endsection