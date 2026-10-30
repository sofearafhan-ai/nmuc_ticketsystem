@extends('layouts.app')

@section('title', 'New Ticket - Helpdesk')

@section('content')
<div class="max-w-3xl mx-auto bg-white rounded-2xl border border-slate-200 shadow-sm p-6 space-y-6">
    
    <div class="border-b border-slate-100 pb-4">
        <h2 class="text-base font-bold text-slate-800">Submit New Ticket</h2>
        <p class="text-xs text-slate-400">Please provide details regarding the issue or the technical assistance you require.</p>
    </div>

    <form action="{{ route('tickets.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
        @csrf

        <!-- Ticket Title -->
        <div>
            <label class="block text-xs font-extrabold uppercase tracking-wider text-slate-500 mb-1">Title / Subject</label>
            <input type="text" name="title" required placeholder="Contoh: Kertas Pencetak Habis / Internet Terputus" 
                   class="w-full px-3.5 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs font-medium focus:ring-2 focus:ring-orange-500 outline-none transition">
        </div>

        <!-- Grid 2 Column -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <!-- Category -->
            <div>
                <label class="block text-xs font-extrabold uppercase tracking-wider text-slate-500 mb-1">Category</label>
                <select name="category" required class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs font-bold text-slate-700 focus:ring-2 focus:ring-orange-500 outline-none transition">
                    @foreach($categories as $cat)
                        <option value="{{ $cat }}">{{ $cat }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Priority -->
            <div>
                <label class="block text-xs font-extrabold uppercase tracking-wider text-slate-500 mb-1">Priority</label>
                <select name="priority" required class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs font-bold text-slate-700 focus:ring-2 focus:ring-orange-500 outline-none transition">
                    @foreach($priorities as $prio)
                        <option value="{{ $prio }}">{{ $prio }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <!-- Grid 2 Column -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <!-- Department -->
            <div>
                <label class="block text-xs font-extrabold uppercase tracking-wider text-slate-500 mb-1">Department</label>
                <select name="department_id" required class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs font-bold text-slate-700 focus:ring-2 focus:ring-orange-500 outline-none transition">
                    <option value="">-- Select Department --</option>
                    @foreach($departments as $dept)
                        <option value="{{ $dept->department_id }}">{{ $dept->department_name }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Incident Location -->
            <div>
                <label class="block text-xs font-extrabold uppercase tracking-wider text-slate-500 mb-1">Location</label>
                <input type="text" name="location" required placeholder="Contoh: Bilik Mesyuarat Level 3" 
                       class="w-full px-3.5 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs font-medium focus:ring-2 focus:ring-orange-500 outline-none transition">
            </div>
        </div>

        <!-- Description -->
        <div>
            <label class="block text-xs font-extrabold uppercase tracking-wider text-slate-500 mb-1">Description</label>
            <textarea name="description" rows="4" required placeholder="Jelaskan masalah anda dengan terperinci..." 
                      class="w-full px-3.5 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs font-medium focus:ring-2 focus:ring-orange-500 outline-none transition"></textarea>
        </div>

        <!-- Image Upload -->
        <div>
            <label class="block text-xs font-extrabold uppercase tracking-wider text-slate-500 mb-1">Attachment Image (Optional)</label>
            <input type="file" name="image" accept="image/*" class="w-full text-xs text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-orange-50 file:text-orange-700 hover:file:bg-orange-100 transition">
        </div>

        <!-- Submit Button -->
        <div class="pt-3">
            <button type="submit" class="w-full py-2.5 bg-orange-500 hover:bg-orange-600 text-white font-bold rounded-xl text-xs shadow-md transition cursor-pointer">
                Submit Ticket
            </button>
        </div>
    </form>

</div>
@endsection