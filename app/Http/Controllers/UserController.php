<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use App\Models\Department;
use App\Exports\User\TicketsExport;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    // ==========================================
    // 1. EXPORT FUNCTION
    // ==========================================
    public function export(Request $request)
    {
        $fileName = 'my_tickets_report_' . date('Y_m_d_His') . '.xlsx';
        return Excel::download(new TicketsExport($request), $fileName);
    }

    public function exportTickets(Request $request)
    {
        return $this->export($request);
    }

    // ==========================================
    // 2. MESSAGES PAGE
    // ==========================================
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

        if ($request->status == 'Open') {
            $query->whereIn('status', ['New', 'Processing', 'Pending']);
        } elseif ($request->status == 'Closed') {
            $query->whereIn('status', ['Close', 'Closed']);
        }

        if ($request->sort == 'oldest') {
            $query->oldest();
        } else {
            $query->latest();
        }

        $conversations = $query->get();

        $selectedId = $request->ticket_id ?? ($conversations->first()->ticket_id ?? null);
        $activeTicket = $selectedId
            ? Ticket::with(['replies'])->where('ticket_id', $selectedId)->where('user_id', Auth::id())->first()
            : null;

        $replyName = Auth::user()->name;
        $adminDept = Auth::user()->department ?? null;

        $userTicketQuery = Ticket::where('user_id', Auth::id());

        $userTicketStats = [
            'new'        => (clone $userTicketQuery)->where('status', 'New')->count(),
            'processing' => (clone $userTicketQuery)->where('status', 'Processing')->count(),
            'pending'    => (clone $userTicketQuery)->where('status', 'Pending')->count(),
            'close'      => (clone $userTicketQuery)->whereIn('status', ['Close', 'Closed'])->count(),
            'total'      => (clone $userTicketQuery)->count(),
        ];

        return view('user.messages.index', compact('conversations', 'activeTicket', 'replyName', 'adminDept', 'userTicketStats' ));
    }

    public function messagesReply(Request $request, $ticketId)
    {
        $ticket = Ticket::where('ticket_id', $ticketId)
            ->where('user_id', Auth::id())
            ->firstOrFail();


        if (in_array($ticket->status, ['Close', 'Closed'])) {
            return redirect()->back()->with('error', 'This ticket is closed and cannot receive replies.');
        }

        $request->validate([
            'message'    => 'required|string',
            'attachment' => 'nullable|file|mimes:jpeg,png,jpg,pdf|max:2048',
        ]);

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
            ->with('success', 'Message sent successfully!');
    }



    // ==========================================
    // 3. TICKET LIST PAGE
    // ==========================================
    public function ticketsIndex(Request $request)
    {
        $query = Ticket::with('department')->where('user_id', Auth::id());

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('ticket_id', 'like', "%{$search}%");
            });
        }

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('priority')) {
            $query->where('priority', $request->priority);
        }

        if ($request->filled('department')) {
            $query->where('department_id', $request->department);
        }

        if ($request->filled('location')) {
            $query->where('location', $request->location);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('month')) {
            $query->whereMonth('created_at', $request->month);
        }

        if ($request->filled('year')) {
            $query->whereYear('created_at', $request->year);
        }

        $tickets = $query->latest()->paginate(10)->withQueryString();

        $categories = ['Hardware', 'Software', 'Network', 'Facility', 'Others'];
        $types      = ['Incident', 'Request'];
        $priorities = ['Low', 'Medium', 'High', 'Urgent'];
        $statuses   = ['New', 'Processing', 'Pending', 'Close'];
        $departments = Department::all();

        $locations = Ticket::whereNotNull('location')->distinct()->pluck('location');

        return view('user.tickets.index', compact('tickets', 'categories', 'types', 'priorities', 'statuses', 'departments', 'locations'));
    }

    public function destroyTicket($ticketId)
    {
        $ticket = Ticket::where('ticket_id', $ticketId)->firstOrFail();
        $ticket->delete();

        return redirect()->back()->with('success', 'Ticket successfully deleted!');
    }

    // ==========================================
    // 4. NEW TICKET PAGE
    // ==========================================
    public function ticketsCreate()
    {
        $departments = Department::all();
        $categories  = ['Hardware', 'Software', 'Network', 'Facility', 'Others'];
        $types       = ['Incident', 'Request']; 
        $priorities  = ['Low', 'Medium', 'High', 'Urgent'];

        return view('user.tickets.create', compact('departments', 'categories', 'types', 'priorities'));
    }

    public function ticketsStore(Request $request)
    {
        $request->validate([
            'title'         => 'required|string|max:255',
            'category'      => 'required|string',
            'type'          => 'nullable|string', 
            'priority'      => 'required|string',
            'department_id' => 'required',
            'location'      => 'required|string',
            'description'   => 'required|string',
            'image'         => 'nullable|file|mimes:jpeg,png,jpg,pdf|max:2048', 
        ]);

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('tickets', 'public');
        }

        $ticket = Ticket::create([
            'ticket_id'     => '#TC-' . strtoupper(uniqid()),
            'user_id'       => Auth::id(),
            'user_name'     => Auth::user()->full_name ?? Auth::user()->name,
            'user_email'    => Auth::user()->email,
            'title'         => $request->title,
            'category'      => $request->category,
            'type'          => $request->type ?? 'Incident', 
            'priority'      => $request->priority,
            'department_id' => $request->department_id,
            'location'      => $request->location,
            'description'   => $request->description,
            'status'        => 'New',
            'image'         => $imagePath,
            'date'          => now(),
        ]);

        return redirect()->route('messages.index', ['ticket_id' => $ticket->ticket_id])
            ->with('success', 'New ticket successfully created!');
    }
    
}