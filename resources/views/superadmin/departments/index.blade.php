@extends('layouts.superadmin')

@section('title', 'Department Management')

@section('content')
<div class="space-y-6">

    <!-- Flash Message Notification -->
    @if(session('success'))
        <div class="p-4 bg-emerald-100 border border-emerald-200 text-emerald-800 rounded-xl text-xs font-bold flex items-center gap-2">
            <i class="fa-solid fa-circle-check text-emerald-600"></i>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    <!-- Header Section -->
    <div class="bg-white p-6 rounded-2xl border border-slate-200/80 shadow-sm">
        <h1 class="text-xl font-bold text-slate-800">Department Management</h1>
        <p class="text-xs text-slate-400 mt-1">Add, update, or remove departments used across the ticketing & inventory systems.</p>
    </div>

    <!-- GRID LAYOUT 2 RUANG (Borang Tambah 1/3, Jadual Senarai 2/3) -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 items-start">

        <!-- RUANG KIRI: Form Tambah Department Baharu (1/3) -->
        <div class="bg-white p-6 rounded-2xl border border-slate-200/80 shadow-sm space-y-4">
            <div class="border-b border-slate-100 pb-3">
                <h3 class="font-bold text-slate-800 text-sm flex items-center gap-2">
                    <i class="fa-solid fa-plus-circle text-orange-500"></i>
                    <span>Add New Department</span>
                </h3>
            </div>

            <form action="{{ route('superadmin.departments.store') }}" method="POST" class="space-y-4">
                @csrf

                <!-- Department Name -->
                <div>
                    <label class="block text-xs font-extrabold uppercase tracking-wider text-slate-400 mb-1.5">Department Name</label>
                    <input type="text" name="department_name"
                           class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-medium text-slate-800 focus:ring-2 focus:ring-orange-500 outline-none transition">
                    @error('department_name')
                        <span class="text-[10px] text-red-500 font-semibold mt-1 block">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Department Code / Abbreviation -->
                <div>
                    <label class="block text-xs font-extrabold uppercase tracking-wider text-slate-400 mb-1.5">Department Code (Optional)</label>
                    <input type="text" name="code"
                           class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-medium text-slate-800 focus:ring-2 focus:ring-orange-500 outline-none transition">
                </div>

                <!-- Submit Button -->
                <div class="pt-2">
                    <button type="submit" class="w-full py-2.5 bg-orange-500 hover:bg-orange-600 text-white font-bold rounded-xl text-xs shadow-md transition cursor-pointer">
                        Save Department
                    </button>
                </div>
            </form>
        </div>

        <div class="lg:col-span-2 bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
            <div class="p-5 border-b border-slate-100 flex items-center justify-between">
                <h3 class="font-bold text-slate-800 text-sm flex items-center gap-2">
                    <span>Registered Departments</span>
                </h3>
                <span class="text-xs text-slate-400 font-medium">Total: {{ $departments->count() }}</span>
            </div>

            <div class="p-6">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="border-b border-slate-200 bg-slate-50 text-slate-700 uppercase text-[11px] font-bold tracking-wider">
                                <th class="p-3">#</th>
                                <th class="p-3">Department Name</th>
                                <th class="p-3">Code</th>
                                <th class="p-3 text-center">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200 text-sm">
                            @forelse ($departments as $index => $dept)
                                <tr class="hover:bg-slate-50 transition">
                                    <td class="p-3 text-xs text-slate-400 font-bold">{{ $index + 1 }}</td>
                                    <td class="p-3 font-bold text-slate-800 text-xs">
                                        {{ $dept->department_name }}
                                    </td>
                                    <td class="p-3">
                                        <span class="px-2.5 py-1 bg-slate-100 border text-slate-700 rounded-md text-xs font-bold">
                                            {{ $dept->code ?? 'N/A' }}
                                        </span>
                                    </td>
                                    <td class="p-3 text-center">
                                        <form action="{{ route('superadmin.departments.destroy', $dept->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this department?');" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="px-3 py-1.5 bg-red-50 hover:bg-red-500 hover:text-white text-red-600 rounded-lg text-xs font-bold transition border border-red-200">
                                                <i class="fa-solid fa-trash mr-1"></i> Delete
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="p-8 text-center text-slate-400 text-xs">
                                        <i class="fa-regular fa-building text-3xl mb-2 text-slate-300 block"></i>
                                        No departments found. Add your first department using the form.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>

</div>
@endsection