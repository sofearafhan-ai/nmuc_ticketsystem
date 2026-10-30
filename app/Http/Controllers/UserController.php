<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use App\Models\Department;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{

    public function messagesIndex(Request $request)
    {
        $query = Ticket::with(['replies'])
            ->where('user_id', Auth::id());

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('ticket_id', 'like', "%{$search}%");
        });
    }

        $conversations = $query->latest()->get();

        $selectedId = $request->ticket_id ?? ($conversations->first()->ticket_id ?? null);
        $activeTicket = $selectedId
            ? Ticket::with(['replies'])->where('ticket_id', $selectedId)->where('user_id', Auth::id())->first()
            : null;

        return view('user.messages.index', compact('conversations', 'activeTicket'));
    }

    public function messagesReply(Request $request, $ticketId)
    {
        $request->validate([
            'message' => 'required|string',
            'attachment' => 'nullable|file|mimes:jpeg,png,jpg|max:2048',
        ]);

        $ticket = Ticket::where('ticket_id', $ticketId)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        $attachmentPath = null;
        if ($request->hasFile('attachment')) {
            $attachmentPath = $request->file('attachment')->store('replies', 'public');
        }
        $ticket->replies()->create([
            'user_id'     => Auth::id(),
            'sender_name' => Auth::user()->full_name ?? Auth::user()->name,
            'sender_role' => 'Staff',
            'message'     => $request->message,
            'attachment'  => $attachmentPath,
        ]);

        return redirect()->route('messages.index', ['ticket_id' => $ticketId])
            ->with('success', 'Pesan berhasil dikirim!');
    }

    // ==========================================
    // 2. TICKET LIST PAGE
    // ==========================================
    public function ticketsIndex(Request $request)
    {
        $query = Ticket::where('user_id', Auth::id());

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('ticket_id', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $tickets = $query->latest()->paginate(10)->withQueryString();

        return view('user.tickets.index', compact('tickets'));
    }

    // ==========================================
    // 3. NEW TICKET PAGE
    // ==========================================
    public function ticketsCreate()
    {
        $departments = Department::all();
        $categories  = ['Hardware', 'Software', 'Network', 'Facility', 'Others'];
        $priorities  = ['Low', 'Medium', 'High', 'Urgent'];

        return view('user.tickets.create', compact('departments', 'categories', 'priorities'));
    }

    // Simpan Tiket Baru
    public function ticketsStore(Request $request)
    {
        $request->validate([
            'title'         => 'required|string|max:255',
            'category'      => 'required|string',
            'priority'      => 'required|string',
            'department_id' => 'required|exists:departments,department_id',
            'location'      => 'required|string',
            'description'   => 'required|string',
            'image'         => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('tickets', 'public');
        }

        $ticket = Ticket::create([
            'ticket_id'     => 'TK-' . strtoupper(uniqid()),
            'user_id'       => Auth::id(),
            'user_name'     => Auth::user()->full_name ?? Auth::user()->name,
            'user_email'    => Auth::user()->email,
            'title'         => $request->title,
            'category'      => $request->category,
            'priority'      => $request->priority,
            'department_id' => $request->department_id,
            'location'      => $request->location,
            'description'   => $request->description,
            'status'        => 'New',
            'image'         => $imagePath,
        ]);

        return redirect()->route('messages.index', ['ticket_id' => $ticket->ticket_id])
            ->with('success', 'Tiket baru berhasil dibuat!');
    }
}