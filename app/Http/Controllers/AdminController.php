<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use App\Models\Inventory;
use App\Models\TicketReply;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminController extends Controller
{
    // 1. DASHBOARD ADMIN
    public function dashboard()
    {
        $user = Auth::user();
        $adminDept = $user->department ?? 'IT';
        $currentAdminId = $user->user_id ?? $user->id;

        $ticketQuery = Ticket::query();
        
        if ($user->role !== 'Super Admin') {
            $ticketQuery->where(function ($q) use ($currentAdminId, $adminDept) {
                $q->where('user_id', $currentAdminId)
                  ->orWhere('category', $adminDept);
            });
        }

        $ticketStats = [
            'new'        => Ticket::where('status', 'New')->count(),
            'processing' => Ticket::where('status', 'Processing')->count(),
            'pending'    => Ticket::where('status', 'Pending')->count(),
            'close'      => Ticket::where('status', 'Close')->count(),
            'total'      => Ticket::count(),
        ];

        $inventoryQuery = Inventory::query();
        $inventoryStats = [
            'active'   => (clone $inventoryQuery)->where('item_status', 'Active')->count(),
            'inactive' => (clone $inventoryQuery)->where('item_status', 'Inactive')->count(),
            'total'    => (clone $inventoryQuery)->count(),
        ];

        $recentTickets = Ticket::where('department', $user->department_id) 
            ->latest()
            ->take(5) 
            ->get();

        return view('admin.dashboard', compact('ticketStats', 'inventoryStats', 'adminDept', 'recentTickets'));
    }

    // 2. ADMIN TICKET LIST
    public function tickets(Request $request)
    {
        $user = Auth::user();
        $adminDept = $user->department ?? 'IT';
        $currentAdminId = $user->user_id ?? $user->id;

        $query = Ticket::query();

        if ($user->role !== 'Super Admin') {
            $query->where(function ($q) use ($currentAdminId, $adminDept) {
                $q->where('user_id', $currentAdminId)
                  ->orWhere('category', $adminDept);
            });
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('ticket_id', 'like', "%{$search}%")
                  ->orWhere('user_name', 'like', "%{$search}%");
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

        if ($request->filled('month')) {
            $query->whereMonth('created_at', $request->month);
        }

        if ($request->filled('year')) {
            $query->whereYear('created_at', $request->year);
        }

        $tickets = $query->latest()->paginate(10)->withQueryString();

        return view('admin.tickets.index', compact('tickets', 'adminDept'));
    }

    public function destroyTicket($id)
    {
        $ticket = Ticket::where('ticket_id', $id)->firstOrFail();
        $ticket->delete();

        return redirect()->route('admin.tickets.index')
            ->with('success', 'Ticket successfully deleted!');
    }

    public function createTicket()
    {
        $user = Auth::user();
        $adminDept = $user->department ?? 'IT';

        $types = ['Incident', 'Request'];
        $categories = ['Hardware', 'Software', 'Network', 'Facility', 'Others'];
        $priorities = ['Low', 'Medium', 'High', 'Urgent'];

        return view('admin.tickets.create', compact('adminDept', 'types', 'categories', 'priorities'));
    }

    // Edit Ticket
    public function editTicket($id)
    {
        $user = Auth::user();
        $adminDept = $user->department ?? 'IT';

        $ticket = Ticket::with(['replies', 'user', 'picAdmin'])->where('ticket_id', $id)->firstOrFail();

        $adminsInDept = User::whereIn('role', ['Admin', 'Super Admin'])
                            ->where('department', $adminDept)
                            ->get();

        $categories = ['Hardware', 'Software', 'Network', 'Facility', 'Others'];
        $types = ['Incident', 'Request'];
        $statuses = ['New', 'Processing', 'Pending', 'Close'];
        $priorities = ['Low', 'Medium', 'High', 'Urgent'];

        return view('admin.tickets.edit', compact('ticket', 'adminsInDept', 'categories', 'types', 'statuses', 'priorities'));
    }

    public function updateTicket(Request $request, $id)
    {
        $request->validate([
            'category' => 'required|string',
            'type'     => 'required|string',
            'status'   => 'required|in:New,Processing,Pending,Close', 
            'priority' => 'required|string',
            'location' => 'required|string',
            'pic'      => 'nullable|exists:users,user_id',
        ]);

        $ticket = Ticket::with(['replies', 'user'])->where('ticket_id', $id)->firstOrFail();

        $ticket->update([
            'category' => $request->category,
            'type'     => $request->type,
            'status'   => $request->status,
            'priority' => $request->priority,
            'location' => $request->location,
            'pic'      => $request->pic, 
        ]);

        return redirect()->back()->with('success', 'Ticket Information updated successfully!');
    }

    public function showConversation($id)
    {
        return $this->messages(request(), $id);
    }

    public function storeTicket(Request $request)
    {
        $request->validate([
            'title'       => 'required|string|max:255',
            'description' => 'required|string',
            'user_name'   => 'required|string|max:255',
            'user_email'  => 'required|email|max:255',
            'type'        => 'required|string',
            'category'    => 'required|string',
            'priority'    => 'required|string',
            'location'    => 'required|string|max:255',
            'image'       => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $user = Auth::user();

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('tickets', 'public');
        }

        Ticket::create([
            'user_id'     => $user->user_id ?? $user->id,
            'user_name'   => $request->user_name,
            'user_email'  => $request->user_email,
            'title'       => $request->title,
            'description' => $request->description,
            'type'        => $request->type,
            'category'    => $request->category,
            'priority'    => $request->priority,
            'location'    => $request->location,
            'status'      => 'New',
            'date'        => now()->format('Y-m-d'),
            'image_path'  => $imagePath,
        ]);

        return redirect()->route('admin.tickets.index')
            ->with('success', 'New support ticket created successfully!');
    }

    // 3. MESSAGE HISTORY ADMIN 
    public function messages(Request $request, $ticketId = null)
    {
        $user = Auth::user();
        $currentAdminId = $user->user_id ?? $user->id;
        $adminDept = $user->department ?? 'IT';
        $filter = $request->query('status');

        $query = Ticket::with(['user', 'replies', 'picAdmin'])
                       ->where('pic', $currentAdminId);

        if ($filter == 'Open') {
            $query->whereIn('status', ['New', 'Processing', 'Pending']);
        } elseif ($filter == 'Closed') {
            $query->where('status', 'Close');
        }

        $conversations = $query->latest()->get();

        $selectedId = $ticketId ?? $request->query('ticket_id');

        $activeTicket = null;
        if ($selectedId) {
            $activeTicket = Ticket::with(['user', 'replies', 'picAdmin'])->where('ticket_id', $selectedId)->first();
        } else if ($conversations->isNotEmpty()) {
            $activeTicket = $conversations->first();
        }

        return view('admin.messages.index', compact('adminDept', 'conversations', 'activeTicket'))
            ->with('tickets', $conversations);
    }

   public function replyMessage(Request $request, $ticketId)
    {
        $request->validate([
            'message'    => 'required|string',
            'attachment' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        $user = Auth::user();

        $attachmentPath = null;
        if ($request->hasFile('attachment')) {
            $attachmentPath = $request->file('attachment')->store('chat_attachments', 'public');
        }

        TicketReply::create([
            'ticket_id'   => $ticketId,
            'user_id'     => $user->user_id ?? $user->id,
            'sender_name' => $user->full_name ?? $user->name ?? 'Admin Support',
            'sender_role' => $user->role ?? 'Admin',
            'message'     => $request->message,
            'attachment'  => $attachmentPath,
        ]);

        return redirect()->route('admin.messages.index', ['ticket_id' => $ticketId])
            ->with('success', 'Message sent successfully.');
    }

    // 4. DEPARTMENT ADMIN INVENTORY LIST
    public function inventory(Request $request)
    {
        $user = Auth::user();
        $adminDept = $user->department ?? 'IT';

        $query = Inventory::with('user');

        if ($user->role !== 'Super Admin') {
            $query->where('user_id', $user->user_id ?? $user->id);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
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

        return view('admin.inventory.index', compact('inventories', 'adminDept'));
    }

    public function createInventory()
    {
        $user = Auth::user();
        $adminDept = $user->department ?? 'IT';

        return view('admin.inventory.create', compact('adminDept'));
    }

    public function editInventory(Request $request, $id)
    {
        $user = Auth::user();
        $adminDept = $user->department ?? 'IT';

        $item = Inventory::where('item_id', $id)->firstOrFail();
        $rowNum = $request->query('row_num', $item->item_id);

        return view('admin.inventory.edit', compact('item', 'adminDept', 'rowNum'));
    }

    public function updateInventory(Request $request, $id)
    {
        $request->validate([
            'item_name'     => 'required|string|max:255',
            'item_category' => 'required|string|max:255',
            'item_model'    => 'required|string|max:255',
            'item_status'   => 'required|in:Active,Inactive',
            'item_location' => 'required|string|max:255',
        ]);

        $item = Inventory::where('item_id', $id)->firstOrFail();

        $item->update([
            'item_name'     => $request->item_name,
            'item_category' => $request->item_category,
            'item_model'    => $request->item_model,
            'item_status'   => $request->item_status,
            'item_location' => $request->item_location,
        ]);

        return redirect()->route('admin.inventory.index')
            ->with('success', 'Inventory assets have been successfully updated!');
    }

    public function destroyInventory($id)
    {
        $item = Inventory::findOrFail($id);
        $item->delete();

        return redirect()->route('admin.inventory.index')
            ->with('success', 'Inventory asset successfully deleted!');
    }

    public function storeInventory(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'items'                 => 'required|array|min:1',
            'items.*.item_name'     => 'required|string|max:255',
            'items.*.item_category' => 'required|string|max:255',
            'items.*.item_model'    => 'required|string|max:255',
            'items.*.item_status'   => 'required|in:Active,Inactive',
            'items.*.item_location' => 'required|string|max:255',
        ]);

        $createdCount = 0;

        foreach ($request->items as $itemData) {
            Inventory::create([
                'item_name'     => $itemData['item_name'],
                'item_category' => $itemData['item_category'],
                'item_model'    => $itemData['item_model'],
                'item_status'   => $itemData['item_status'],
                'item_location' => $itemData['item_location'],
                'user_id'       => $user->user_id ?? $user->id,
            ]);
            $createdCount++;
        }

        return redirect()->route('admin.inventory.index')
            ->with('success', "A total of {$createdCount} new inventory assets have been successfully registered!");
    }
}