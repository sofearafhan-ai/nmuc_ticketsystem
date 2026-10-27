<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Ticket;
use App\Models\Inventory;
use App\Models\Department;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class SuperAdminController extends Controller
{
    public function dashboard()
    {
        $ticketStats = [
            'new'        => Ticket::where('status', 'New')->count(),
            'processing' => Ticket::where('status', 'Processing')->count(),
            'pending'    => Ticket::where('status', 'Pending')->count(),
            'close'      => Ticket::whereIn('status', ['Close', 'Closed'])->count(),
            'total'      => Ticket::count(),
        ];

        $inventoryStats = [
            'active'   => Inventory::where('item_status', 'Active')->count(),
            'inactive' => Inventory::where('item_status', 'Inactive')->count(),
            'total'    => Inventory::count(),
        ];

        $userStats = [
            'total'       => User::count(),
            'admins'      => User::whereIn('role', ['Admin', 'Super Admin'])->count(),
            'staff'       => User::where('role', 'Staff')->count(),
            'departments' => Department::count(),
        ];

        $recentTickets = Ticket::with(['user', 'picAdmin'])
            ->latest()
            ->take(5)
            ->get();

        return view('superadmin.dashboard', compact(
            'ticketStats',
            'inventoryStats',
            'userStats',
            'recentTickets'
        ));
    }

    // ==========================================
    // USERS MANAGEMENT
    // ==========================================
    public function usersIndex(Request $request)
    {
        $query = User::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('full_name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('department', 'like', "%{$search}%");
            });
        }

        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        $users = $query->latest()->paginate(10)->withQueryString();

        return view('superadmin.users.index', compact('users'));
    }

    public function usersCreate()
    {
        $departments = Department::pluck('department_name')->toArray();
        if (empty($departments)) {
            $departments = ['IT', 'Facility', 'Management', 'HR', 'Finance'];
        }
        $roles = ['Staff', 'Admin', 'Super Admin'];

        return view('superadmin.users.create', compact('departments', 'roles'));
    }

    public function usersStore(Request $request)
    {
        $request->validate([
            'full_name'  => 'required|string|max:255',
            'email'      => 'required|email|unique:users,email',
            'password'   => 'required|string|min:6',
            'role'       => 'required|string',
            'department' => 'required|string',
        ]);

        User::create([
            'name'       => $request->full_name,
            'full_name'  => $request->full_name,
            'email'      => $request->email,
            'password'   => Hash::make($request->password),
            'role'       => $request->role,
            'department' => $request->department,
        ]);

        return redirect()->route('superadmin.users.index')
            ->with('success', 'User account registered successfully!');
    }

    public function usersDestroy($id)
    {
        $user = User::where('user_id', $id)->firstOrFail();
        $user->delete();

        return redirect()->route('superadmin.users.index')
            ->with('success', 'User account deleted successfully!');
    }

    // ==========================================
    // DEPARTMENT MANAGEMENT
    // ==========================================
    public function departmentsIndex()
    {
        $departments = Department::latest()->get();
        return view('superadmin.departments.index', compact('departments'));
    }

    public function departmentsStore(Request $request)
    {
        $request->validate([
            'department_name' => 'required|string|max:255|unique:departments,department_name',
            'code'            => 'nullable|string|max:50',
        ]);

        Department::create([
            'department_name' => $request->department_name,
            'code'            => strtoupper($request->code),
        ]);

        return redirect()->route('superadmin.departments.index')
            ->with('success', 'Department added successfully!');
    }

    public function departmentsDestroy($id)
    {
        $dept = Department::findOrFail($id);
        $dept->delete();

        return redirect()->route('superadmin.departments.index')
            ->with('success', 'Department deleted successfully!');
    }

    // ==========================================
    // TICKETS MODULE (SUPER ADMIN)
    // ==========================================
    public function ticketsIndex(Request $request)
    {
        $adminDept = Auth::user()->department ?? 'ALL DEPARTMENTS';
        
        $query = Ticket::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('user_name', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $tickets = $query->latest()->paginate(10)->withQueryString();

        return view('superadmin.tickets.index', compact('tickets', 'adminDept'));
    }

    public function ticketsCreate()
    {
        $departments = Department::pluck('department_name')->toArray();
        $types       = ['Incident', 'Request', 'Hardware', 'Software', 'Network', 'Facility', 'Others'];
        $categories  = ['Hardware', 'Software', 'Network', 'Facility', 'Others'];
        $priorities  = ['Low', 'Medium', 'High', 'Urgent'];

        return view('superadmin.tickets.create', compact('departments', 'types', 'categories', 'priorities'));
    }

    public function ticketsStore(Request $request)
    {
        $request->validate([
            'title'       => 'required|string|max:255',
            'description' => 'required|string',
            'user_name'   => 'required|string|max:255',
            'user_email'  => 'nullable|email|max:255',
            'type'        => 'required|string',
            'category'    => 'required|string',
            'priority'    => 'required|string',
            'location'    => 'required|string',
            'image'       => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('tickets', 'public');
        }

        Ticket::create([
            'ticket_id'   => 'TK-' . strtoupper(uniqid()),
            'user_id'     => Auth::id(),
            'user_name'   => $request->user_name,
            'user_email'  => $request->user_email,
            'title'       => $request->title,
            'description' => $request->description,
            'type'        => $request->type,
            'category'    => $request->category,
            'priority'    => $request->priority,
            'location'    => $request->location,
            'status'      => 'New',
            'image'       => $imagePath,
        ]);

        return redirect()->route('superadmin.tickets.index')
            ->with('success', 'New ticket created successfully!');
    }

    public function ticketsDestroy($id)
    {
        $ticket = Ticket::where('ticket_id', $id)->orWhere('id', $id)->firstOrFail();
        
        if ($ticket->image && Storage::disk('public')->exists($ticket->image)) {
            Storage::disk('public')->delete($ticket->image);
        }

        $ticket->delete();

        return redirect()->route('superadmin.tickets.index')
            ->with('success', 'Ticket deleted successfully!');
    }

    public function messagesIndex(Request $request)
{
    $query = Ticket::with(['user', 'replies']);

    if ($request->filled('search')) {
        $search = $request->search;
        $query->where(function($q) use ($search) {
            $q->where('title', 'like', "%{$search}%")
              ->orWhere('ticket_id', 'like', "%{$search}%")
              ->orWhere('user_name', 'like', "%{$search}%");
        });
    }

    if ($request->filled('status')) {
        if ($request->status == 'Open') {
            $query->whereIn('status', ['New', 'Processing', 'Pending']);
        } elseif ($request->status == 'Closed') {
            $query->whereIn('status', ['Close', 'Closed']);
        }
    }

    $conversations = $query->latest()->get();

    $selectedId = $request->ticket_id ?? ($conversations->first()->ticket_id ?? null);
    $activeTicket = $selectedId 
        ? Ticket::with(['user', 'replies'])->where('ticket_id', $selectedId)->first() 
        : null;

    return view('superadmin.messages.index', compact('conversations', 'activeTicket'));
}

    public function messagesReply(Request $request, $ticketId)
{
    $request->validate([
        'message'    => 'required|string',
        'attachment' => 'nullable|file|mimes:jpeg,png,jpg,gif,pdf,doc,docx|max:2048',
    ]);

    $ticket = Ticket::where('ticket_id', $ticketId)->orWhere('id', $ticketId)->firstOrFail();

    $attachmentPath = null;
    if ($request->hasFile('attachment')) {
        $attachmentPath = $request->file('attachment')->store('replies', 'public');
    }

    $ticket->replies()->create([
        'user_id'     => Auth::id(),
        'sender_name' => Auth::user()->full_name ?? Auth::user()->name ?? 'Super Admin',
        'sender_role' => 'Super Admin',
        'message'     => $request->message,
        'attachment'  => $attachmentPath,
    ]);

    return redirect()->route('superadmin.messages.index', ['ticket_id' => $ticketId])
        ->with('success', 'Reply sent successfully!');
}

public function ticketsEdit($id)
{
    $ticket = Ticket::with(['user', 'replies'])->where('ticket_id', $id)->firstOrFail();

    $categories = ['Hardware', 'Software', 'Network', 'Facility', 'Others'];
    $types      = ['Incident', 'Request', 'Hardware', 'Software', 'Network', 'Facility', 'Others'];
    $statuses   = ['New', 'Processing', 'Pending', 'Closed'];
    $priorities = ['Low', 'Medium', 'High', 'Urgent'];

    $adminsInDept = User::whereIn('role', ['Admin', 'Super Admin'])->get();

    return view('superadmin.tickets.edit', compact(
        'ticket', 
        'categories', 
        'types', 
        'statuses', 
        'priorities', 
        'adminsInDept'
    ));
}

public function ticketsUpdate(Request $request, $id)
{
    $request->validate([
        'title'       => 'required|string|max:255',
        'user_name'   => 'required|string|max:255',
        'type'        => 'required|string',
        'category'    => 'required|string',
        'priority'    => 'required|string',
        'status'      => 'required|string',
        'location'    => 'required|string',
        'description' => 'required|string',
        'image'       => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
    ]);

    $ticket = Ticket::where('ticket_id', $id)->firstOrFail();

    $dataToUpdate = [
        'title'       => $request->title,
        'user_name'   => $request->user_name,
        'user_email'  => $request->user_email,
        'type'        => $request->type,
        'category'    => $request->category,
        'priority'    => $request->priority,
        'status'      => $request->status,
        'location'    => $request->location,
        'description' => $request->description,
    ];

    if ($request->hasFile('image')) {
        if ($ticket->image && Storage::disk('public')->exists($ticket->image)) {
            Storage::disk('public')->delete($ticket->image);
        }
        $dataToUpdate['image'] = $request->file('image')->store('tickets', 'public');
    }

    $ticket->update($dataToUpdate);

    return redirect()->route('superadmin.tickets.index')
        ->with('success', 'Ticket updated successfully!');
}

public function ticketsDeleteAttachment($id)
{
    $ticket = Ticket::where('ticket_id', $id)->firstOrFail();

    if ($ticket->image && Storage::disk('public')->exists($ticket->image)) {
        Storage::disk('public')->delete($ticket->image);
    }

    $ticket->update(['image' => null]);

    return redirect()->back()->with('success', 'Ticket image deleted successfully!');
}

    // ==========================================
    // INVENTORY MODULE (SUPER ADMIN)
    // ==========================================
    public function inventoryIndex(Request $request)
    {
        $query = Inventory::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('item_name', 'like', "%{$search}%")
                  ->orWhere('item_model', 'like', "%{$search}%")
                  ->orWhere('item_category', 'like', "%{$search}%")
                  ->orWhere('item_location', 'like', "%{$search}%");
            });
        }

        if ($request->filled('category')) {
            $query->where('item_category', $request->category);
        }

        if ($request->filled('status')) {
            $query->where('item_status', $request->status);
        }

        $inventories = $query->latest('item_id')->paginate(10)->withQueryString();

        return view('superadmin.inventory.index', compact('inventories'));
    }

    public function inventoryCreate()
    {
        $departments = Department::pluck('department_name')->toArray();
        return view('superadmin.inventory.create', compact('departments'));
    }

    public function inventoryStore(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'items'                   => 'required|array|min:1',
            'items.*.item_name'       => 'required|string|max:255',
            'items.*.item_category'   => 'required|string|max:255',
            'items.*.item_model'      => 'required|string|max:255',
            'items.*.item_status'     => 'required|in:Active,Inactive',
            'items.*.item_location'   => 'required|string|max:255',
            'items.*.item_attachment' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $createdCount = 0;

        foreach ($request->items as $index => $itemData) {
            $attachmentPath = null;

            if ($request->hasFile("items.{$index}.item_attachment")) {
                $attachmentPath = $request->file("items.{$index}.item_attachment")->store('inventories', 'public');
            }

            Inventory::create([
                'item_name'       => $itemData['item_name'],
                'item_category'   => $itemData['item_category'],
                'item_model'      => $itemData['item_model'],
                'item_status'     => $itemData['item_status'],
                'item_location'   => $itemData['item_location'],
                'item_attachment' => $attachmentPath,
                'user_id'         => $user->user_id ?? $user->id,
            ]);

            $createdCount++;
        }

        return redirect()->route('superadmin.inventory.index')
            ->with('success', "A total of {$createdCount} new inventory assets registered successfully!");
    }

    public function inventoryDestroy($id)
    {
        $item = Inventory::where('item_id', $id)->orWhere('id', $id)->firstOrFail();

        if ($item->item_attachment && Storage::disk('public')->exists($item->item_attachment)) {
            Storage::disk('public')->delete($item->item_attachment);
        }

        $item->delete();

        return redirect()->route('superadmin.inventory.index')
            ->with('success', 'Inventory asset deleted successfully!');
    }
}