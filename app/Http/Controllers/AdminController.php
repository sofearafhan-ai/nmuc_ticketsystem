<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use App\Models\Inventory;
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
            // Filter follow Admin ID or Department
            $ticketQuery->where(function ($q) use ($currentAdminId, $adminDept) {
                $q->where('user_id', $currentAdminId)
                  ->orWhere('category', $adminDept);
            });
        }

        $ticketStats = [
            'new'        => (clone $ticketQuery)->where('status', 'New')->count(),
            'processing' => (clone $ticketQuery)->where('status', 'Processing')->count(),
            'pending'    => (clone $ticketQuery)->where('status', 'Pending')->count(),
            'closed'     => (clone $ticketQuery)->whereIn('status', ['Closed', 'Solved', 'Resolved'])->count(),
            'total'      => (clone $ticketQuery)->count(),
        ];

        $inventoryQuery = Inventory::query();
        $inventoryStats = [
            'active'   => (clone $inventoryQuery)->where('item_status', 'Active')->count(),
            'inactive' => (clone $inventoryQuery)->where('item_status', 'Inactive')->count(),
            'total'    => (clone $inventoryQuery)->count(),
        ];

        return view('admin.dashboard', compact('adminDept', 'ticketStats', 'inventoryStats'));
    }

    // 2. ADMIN TICKET LIST (With full filters)
    public function tickets(Request $request)
    {
        $user = Auth::user();
        $adminDept = $user->department ?? 'IT';
        $currentAdminId = $user->user_id ?? $user->id;

        $query = Ticket::query();

        // Kawalan capaian mengikut jabatan/user
        if ($user->role !== 'Super Admin') {
            $query->where(function ($q) use ($currentAdminId, $adminDept) {
                $q->where('user_id', $currentAdminId)
                  ->orWhere('category', $adminDept);
            });
        }

        // Carian teks (Title, Ticket ID, Submitted By)
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('ticket_id', 'like', "%{$search}%")
                  ->orWhere('user_name', 'like', "%{$search}%");
            });
        }

        // Penapis Dropdown
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

        // Penapis Bulan
        if ($request->filled('month')) {
            $query->whereMonth('created_at', $request->month);
        }

        // Penapis Tahun
        if ($request->filled('year')) {
            $query->whereYear('created_at', $request->year);
        }

        $tickets = $query->latest()->paginate(10)->withQueryString();

        return view('admin.tickets.index', compact('tickets', 'adminDept'));
    }

    public function destroyTicket($id)
    {
        $ticket = Ticket::where('ticket_id', $id)->orWhere('id', $id)->firstOrFail();
        
        $ticket->delete();

        return redirect()->route('admin.tickets.index')
            ->with('success', 'Ticket successfully deleted!');
    }

    // Tunjukkan borang penambahan tiket baru oleh Admin
    public function createTicket()
    {
        $user = Auth::user();
        $adminDept = $user->department ?? 'IT';

        // Pilihan dropdown untuk borang tiket
        $types = ['Incident', 'Request'];
        $categories = ['Hardware', 'Software', 'Network', 'Facility', 'Others'];
        $priorities = ['Low', 'Medium', 'High', 'Urgent'];

        return view('admin.tickets.create', compact('adminDept', 'types', 'categories', 'priorities'));
    }

    // Simpan tiket baru ke dalam pangkalan data
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

        // Mengendalikan muat naik imej lampiran
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
    public function messages($ticketId = null)
    {
        $user = Auth::user();
        $adminDept = $user->department ?? 'IT';

        // Tarik semua tiket beserta relasi balasannya
        $conversations = Ticket::with('replies')
            ->when($user->role !== 'Super Admin', function($q) use ($adminDept) {
                $q->where('category', $adminDept);
            })
            ->orderBy('ticket_id', 'desc')
            ->get();

        // Pilih tiket aktif
        $activeTicket = null;
        if ($ticketId) {
            $activeTicket = Ticket::with('replies')->where('ticket_id', $ticketId)->first();
        } else if ($conversations->isNotEmpty()) {
            $activeTicket = $conversations->first();
        }

        return view('admin.messages.index', compact('adminDept', 'conversations', 'activeTicket'));
    }

    // Simpan Balasan Mesej
    public function replyMessage(Request $request, $ticketId)
    {
        $request->validate([
            'message' => 'required|string',
            'attachment' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        $user = Auth::user();

        $attachmentPath = null;
        if ($request->hasFile('attachment')) {
            $attachmentPath = $request->file('attachment')->store('chat_attachments', 'public');
        }

        // Simpan balasan ke dalam jadual ticket_replies
        \App\Models\TicketReply::create([
            'ticket_id'  => $ticketId,
            'user_id'    => $user->user_id ?? $user->id,
            'user_name'  => $user->full_name ?? $user->name ?? 'Admin Support',
            'message'    => $request->message,
            'attachment' => $attachmentPath,
            'created_at' => now(),
        ]);

        return redirect()->route('admin.messages.show', $ticketId)
            ->with('success', 'Message sent successfully.');
    }

    // 4. DEPARTMENT ADMIN INVENTORY LIST
    public function inventory(Request $request)
    {
        $user = Auth::user();
        $adminDept = $user->department ?? 'IT';

        $query = Inventory::with('user');

        // Pengehadan mengikut user/jabatan jika bukan Super Admin
        if ($user->role !== 'Super Admin') {
            $query->where('user_id', $user->user_id ?? $user->id);
        }

        // Filter 1: Carian Kata Kunci
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('item_name', 'like', "%{$search}%")
                  ->orWhere('item_model', 'like', "%{$search}%")
                  ->orWhere('item_category', 'like', "%{$search}%")
                  ->orWhere('item_location', 'like', "%{$search}%");
            });
        }

        // Filter 2: Kategori
        if ($request->filled('category')) {
            $query->where('item_category', $request->category);
        }

        // Filter 3: Status
        if ($request->filled('status')) {
            $query->where('item_status', $request->status);
        }

        $inventories = $query->latest('item_id')->paginate(10)->withQueryString();

        return view('admin.inventory.index', compact('inventories', 'adminDept'));
    }

    // 5. NEW INVENTORY SAVE
    public function createInventory()
    {
        $user = Auth::user();
        $adminDept = $user->department ?? 'IT';

        return view('admin.inventory.create', compact('adminDept'));
    }

    // 6. EDIT INVENTORY
    public function editInventory(Request $request, $id)
    {
        $user = Auth::user();
        $adminDept = $user->department ?? 'IT';

        $item = Inventory::where('item_id', $id)->firstOrFail();

        $rowNum = $request->query('row_num', $item->item_id);

        return view('admin.inventory.edit', compact('item', 'adminDept', 'rowNum'));
    }

    // 7. UPDATE INVENTORY
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

    // 8. DELETE INVENTORY
    public function destroyInventory($id)
    {
        $item = Inventory::findOrFail($id);
        $item->delete();

        return redirect()->route('admin.inventory.index')
            ->with('success', 'Inventory asset successfully deleted!');
    }

    // 9. SAVE MULTIPLE ITEMS
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