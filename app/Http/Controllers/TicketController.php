<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use App\Models\User;
use App\Models\TicketReply;
use App\Models\Department;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TicketController extends Controller
{
    public function index(Request $request)
    {
        $query = Ticket::query();

        if (Auth::check()) {
            $userId = Auth::user()->user_id ?? Auth::id();
            $query->where('user_id', $userId);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('ticket_id', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
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

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // 7. Tapis mengikut Bulan & Tahun
        if ($request->filled('month')) {
            $query->whereMonth('date', $request->month);
        }
        if ($request->filled('year')) {
            $query->whereYear('date', $request->year);
        }

        $tickets = $query->latest()->paginate(10)->withQueryString();

    // Senarai pilihan untuk dropdown filter
        $categories = ['Hardware', 'Software', 'Network', 'Facility', 'Others'];
        $types      = ['Incident', 'Request'];
        $priorities = ['Low', 'Medium', 'High', 'Urgent'];
        $statuses   = ['New', 'Processing', 'Pending', 'Solved', 'Closed'];

        return view('tickets.index', compact('tickets', 'categories', 'types', 'priorities', 'statuses'));
    }

    public function create()
    {
        $departments = Department::pluck('department_name', 'department_id');

        $types = $this->getEnumValues('tickets', 'type');
        $priorities = $this->getEnumValues('tickets', 'priority');
        
        // Senarai kategori
        $categories = ['Hardware', 'Software', 'Network', 'Facility', 'Others'];

        return view('tickets.create', compact('types', 'priorities', 'categories', 'departments'));
    }

    // Fungsi khas untuk mengekstrak pilihan ENUM terus dari jadual MySQL
    private function getEnumValues($table, $column)
    {
        $type = DB::select("SHOW COLUMNS FROM {$table} WHERE Field = '{$column}'")[0]->Type;
        preg_match('/^enum\((.*)\)$/', $type, $matches);
        $enum = [];
        foreach (explode(',', $matches[1]) as $value) {
            $enum[] = trim($value, "'");
        }
        return $enum;
    }

    public function store(Request $request)
    {
        $request->validate([
            'title'       => 'required|string',
            'type'        => 'required|in:Incident,Request',
            'category'    => 'required|string',
            'priority'    => 'required|in:Low,Medium,High,Urgent',
            'location'    => 'required|string',
            'description' => 'required|string',
            'image'       => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('tickets', 'public');
        }

        $user = Auth::user();

        if ($user) {
            $validUserId = $user->user_id ?? $user->id;
        } else {
            $existingUser = User::first();
            if ($existingUser) {
                $validUserId = $existingUser->user_id ?? $existingUser->id;
            } else {

                $newUser = User::create([
                    'full_name'  => 'System Admin',
                    'email'      => 'admin@nmuc.edu.my',
                    'password'   => bcrypt('password'),
                    'role'       => 'Admin',
                    'department' => 'IT',
                ]);
                $validUserId = $newUser->user_id ?? $newUser->id;
            }
        }

        Ticket::create([
            'user_name'   => $user->full_name ?? $user->name ?? $request->user_name ?? 'User',
            'user_email'  => $user->email ?? $request->user_email ?? 'user@nmuc.edu.my',
            'title'       => $request->title,
            'description' => $request->description,
            'image'       => $imagePath ?? 'no-image.png',
            'type'        => $request->type,
            'category'    => $request->category,
            'date'        => now()->toDateString(),
            'status'      => 'New',
            'priority'    => $request->priority,
            'user_id'     => $validUserId, 
            'location'    => $request->location,
        ]);

        return redirect()->route('tickets.index')->with('success', 'Tiket aduan berjaya dihantar!');
    }

    public function show($id)
{
    // Contoh jika anda mahu susun mengikut lajur created_at atau lajur lain yang wujud:
    $ticket = Ticket::with(['replies' => function ($query) {
        $query->orderBy('created_at', 'asc'); // Tukar 'created_at' mengikut lajur yang wujud
    }])->findOrFail($id);
    $statuses = ['New', 'Processing', 'Pending', 'Closed'];

    return view('tickets.show', compact('ticket', 'statuses'));
}

// Simpan Mesej Balasan & Kemaskini Status
public function storeReply(Request $request, $id)
{
    $request->validate([
        'message' => 'required|string',
        'attachment' => 'nullable|file|mimes:jpeg,png,jpg,pdf,doc,docx|max:4096',
    ]);

    $ticket = Ticket::findOrFail($id);
    $user = Auth::user();

    $attachmentPath = null;
    if ($request->hasFile('attachment')) {
        $attachmentPath = $request->file('attachment')->store('ticket_replies', 'public');
    }

    $senderName = $user->full_name ?? $user->name ?? $ticket->user_name ?? 'User';
    $senderRole = 'User';

    TicketReply::create([
        'ticket_id'   => $ticket->ticket_id,
        'user_id'     => $user->user_id ?? $user->id ?? null,
        'sender_name' => $senderName,
        'sender_role' => $senderRole,
        'message'     => $request->message,
        'attachment'  => $attachmentPath,
    ]);

    return redirect()->back()->with('success', 'Reply message sent successfully!');
}

}