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
            $ticketQuery->where('pic', $currentAdminId);
        }

        $ticketStats = [
            'new'        => (clone $ticketQuery)->where('status', 'New')->count(),
            'processing' => (clone $ticketQuery)->where('status', 'Processing')->count(),
            'pending'    => (clone $ticketQuery)->where('status', 'Pending')->count(),
            'close'      => (clone $ticketQuery)->whereIn('status', ['Close', 'Closed'])->count(),
            'total'      => (clone $ticketQuery)->count(),
        ];

        $inventoryQuery = Inventory::query();
        $inventoryStats = [
            'active'   => (clone $inventoryQuery)->where('item_status', 'Active')->count(),
            'inactive' => (clone $inventoryQuery)->where('item_status', 'Inactive')->count(),
            'total'    => (clone $inventoryQuery)->count(),
        ];

        // --- KIRA DATA 12 BULAN (REAL-TIME AGGREGATION) ---
        $currentYear = date('Y');
        $activeMonthly = [];
        $inactiveMonthly = [];
        $ticketsMonthly = [];

        for ($month = 1; $month <= 12; $month++) {
            $activeCount = (clone $inventoryQuery)
                ->where('item_status', 'Active')
                ->whereYear('created_at', $currentYear)
                ->whereMonth('created_at', $month)
                ->count();

            $inactiveCount = (clone $inventoryQuery)
                ->where('item_status', 'Inactive')
                ->whereYear('created_at', $currentYear)
                ->whereMonth('created_at', $month)
                ->count();

            $ticketCount = (clone $ticketQuery)
                ->whereYear('created_at', $currentYear)
                ->whereMonth('created_at', $month)
                ->count();

            $activeMonthly[] = $activeCount > 0 ? $activeCount : null;
            $inactiveMonthly[] = $inactiveCount > 0 ? $inactiveCount : null;
            $ticketsMonthly[] = $ticketCount > 0 ? $ticketCount : null;
        }

        $recentTickets = (clone $ticketQuery)
            ->latest()
            ->take(5) 
            ->get();

        return view('admin.dashboard', compact(
            'ticketStats', 
            'inventoryStats', 
            'adminDept', 
            'recentTickets',
            'activeMonthly',
            'inactiveMonthly',
            'ticketsMonthly'
        ));
    }

    // 2. ADMIN TICKET LIST (FILTER DISAMAKAN DENGAN MESSAGE)
    public function tickets(Request $request)
    {
        $user = Auth::user();
        $adminDept = $user->department ?? 'IT';
        $currentAdminId = $user->user_id ?? $user->id;

        $query = Ticket::with(['user', 'picAdmin']);

        // Filter PIC: Mengambil tiket yang di-assign kepada Admin yang login (Sama seperti Message)
        if ($user->role !== 'Super Admin') {
            $query->where('pic', $currentAdminId);
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
            $statusVal = $request->status;
            if ($statusVal == 'Closed') {
                $query->whereIn('status', ['Close', 'Closed']);
            } else {
                $query->where('status', $statusVal);
            }
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

    // Update Ticket & Record Status Change Log
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

        $oldStatus = $ticket->status;
        $newStatus = $request->status;
        $oldPic    = $ticket->pic;
        $newPic    = $request->pic;

        // 1. Log jika PIC BERTUKAR / DIASSIGN
        if ($oldPic != $newPic && !empty($newPic)) {
            $assignedUser = User::where('user_id', $newPic)->first();
            $picName = $assignedUser ? ($assignedUser->full_name ?? $assignedUser->name) : 'Admin';

            TicketReply::create([
                'ticket_id'   => $ticket->ticket_id,
                'user_id'     => Auth::id(),
                'sender_name' => 'System',
                'sender_role' => 'System',
                'message'     => 'PIC_CHANGE:' . $picName,
            ]);
        }

        // 2. Log jika STATUS BERTUKAR
        if ($oldStatus !== $newStatus) {
            TicketReply::create([
                'ticket_id'   => $ticket->ticket_id,
                'user_id'     => Auth::id(),
                'sender_name' => 'System',
                'sender_role' => 'System',
                'message'     => 'STATUS_CHANGE:' . $newStatus,
            ]);
        }

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

    // Store Ticket Baru
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

        $ticket = Ticket::create([
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

        TicketReply::create([
            'ticket_id'   => $ticket->ticket_id ?? $ticket->id,
            'user_id'     => $user->user_id ?? $user->id,
            'sender_name' => 'System',
            'sender_role' => 'System',
            'message'     => 'STATUS_CHANGE:New',
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
        $sort = $request->query('sort', 'newest');

        $query = Ticket::with(['user', 'replies', 'picAdmin']);

        if ($user->role !== 'Super Admin') {
            $query->where('pic', $currentAdminId);
        }

        if ($filter == 'Open') {
            $query->whereIn('status', ['New', 'Processing', 'Pending']);
        } elseif ($filter == 'Closed') {
            $query->whereIn('status', ['Close', 'Closed']);
        }

        if ($sort == 'oldest') {
            $query->oldest();
        } else {
            $query->latest();
        }

        $conversations = $query->get();

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