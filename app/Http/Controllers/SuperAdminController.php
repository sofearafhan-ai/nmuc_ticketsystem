<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Ticket;
use App\Models\Inventory;
use App\Models\Department;
use App\Models\TicketReply;
use App\Exports\SuperAdmin\UserExport;
use App\Exports\SuperAdmin\TicketsExport;
use App\Exports\SuperAdmin\InventoryExport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class SuperAdminController extends Controller
{
    public function usersExport(Request $request)
    {
        $fileName = 'user_list_' . date('Y_m_d_His') . '.xlsx';
        return Excel::download(new UserExport($request), $fileName);
    }

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
            'user'       => User::where('role', 'Authentication User')->count(),
            'departments' => Department::count(),
        ];

       $recentTickets = Ticket::with(['department', 'user'])
        ->where(function($q) {
            $q->whereNull('pic')->orWhere('pic', '');
        })
        ->latest()
        ->take(10)
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
        $query = User::with('department');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nmuc_id', 'like', "%{$search}%")
                ->orWhere('full_name', 'like', "%{$search}%")
                ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($request->filled('department_id')) {
            $query->where('department_id', $request->department_id);
        }

        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        if ($request->filled('status')) {
            $query->where('user_status', $request->status);
        }

        $users = $query->latest()->paginate(10)->withQueryString();
        $departments = Department::all();

        return view('superadmin.users.index', compact('users', 'departments'));
    }

    public function usersCreate()
    {
        $departments = Department::all();
        $roles = ['Authentication User', 'Admin', 'Super Admin'];

        return view('superadmin.users.create', compact('departments', 'roles'));
    }

    public function usersStore(Request $request)
    {
        $request->validate([
            'nmuc_id'       => 'required|string|max:50|unique:users,nmuc_id',
            'full_name'     => 'required|string|max:255',
            'email'         => 'required|email|unique:users,email',
            'password'      => 'required|string|min:6',
            'role'          => 'required|string',
            'department_id' => 'nullable',
        ]);

        User::create([
            'nmuc_id'       => $request->nmuc_id,
            'full_name'     => $request->full_name,
            'email'         => $request->email,
            'password'      => Hash::make($request->password),
            'role'          => $request->role,
            'department_id' => $request->department_id,
        ]);

        return redirect()->route('superadmin.users.index')->with('success', 'User created successfully!');
    }

    public function usersDestroy($id)
    {
        $user = User::where('user_id', $id)
                    ->orWhere('nmuc_id', $id)
                    ->firstOrFail();

        $user->delete();

        return redirect()->route('superadmin.users.index')
            ->with('success', 'User account deleted successfully!');
    }

    public function usersEdit($id)
    {
        $user = User::where('user_id', $id)
                    ->orWhere('nmuc_id', $id)
                    ->firstOrFail();

        $departments = Department::all();

        return view('superadmin.users.edit', compact('user', 'departments'));
    }

   public function usersUpdate(Request $request, $id)
    {

        $user = User::where('user_id', $id)
                    ->orWhere('nmuc_id', $id)
                    ->firstOrFail();

        $primaryKey = $user->user_id;

        $request->validate([
            'full_name'     => 'required|string|max:255',
            'email'         => 'required|email|unique:users,email,' . $primaryKey . ',user_id',
            'role'          => 'required|string',
            'department_id' => 'nullable',
            'user_status'   => 'required|string',
            'password'      => 'nullable|string|min:6|confirmed',
        ]);

        $dataToUpdate = [
            'full_name'     => $request->full_name,
            'email'         => $request->email,
            'role'          => $request->role,
            'department_id' => $request->department_id,
            'user_status'   => $request->user_status,
        ];

        if ($request->filled('nmuc_id')) {
            $dataToUpdate['nmuc_id'] = $request->nmuc_id;
        }

        if ($request->filled('password')) {
            $dataToUpdate['password'] = \Illuminate\Support\Facades\Hash::make($request->password);
        }

        $user->update($dataToUpdate);

        return redirect()->route('superadmin.users.index')
            ->with('success', 'User account updated successfully!');
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

    public function ticketsExport(Request $request)
    {
        return Excel::download(new TicketsExport($request), 'tickets_list_' . date('Y_m_d_His') . '.xlsx');
    }
        
    public function ticketsIndex(Request $request)
    {
        $query = Ticket::with(['user', 'department', 'picAdmin']);

        if ($request->filled('search')) {
            $search = str_replace('#TC-', '', $request->search);
            $query->where(function ($q) use ($search) {
                $q->where('ticket_id', 'like', "%{$search}%")
                ->orWhere('title', 'like', "%{$search}%")
                ->orWhere('user_name', 'like', "%{$search}%")
                ->orWhereHas('user', function ($userQ) use ($search) {
                    $userQ->where('full_name', 'like', "%{$search}%");
                })
                ->orWhereHas('picAdmin', function ($picQ) use ($search) {
                    $picQ->where('full_name', 'like', "%{$search}%");
                });
            });
        }

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        if ($request->filled('priority')) {
            $query->where('priority', $request->priority);
        }

        if ($request->filled('status')) {
            if (in_array($request->status, ['Close', 'Closed'])) {
                $query->whereIn('status', ['Close', 'Closed']);
            } else {
                $query->where('status', $request->status);
            }
        }

        if ($request->filled('department_id')) {
            $query->where('department_id', $request->department_id);
        }

        if ($request->filled('month')) {
            $query->where(function ($q) use ($request) {
                $q->whereMonth('date', $request->month)
                ->orWhereMonth('created_at', $request->month);
            });
        }

        if ($request->filled('year')) {
            $query->where(function ($q) use ($request) {
                $q->whereYear('date', $request->year)
                ->orWhereYear('created_at', $request->year);
            });
        }

        $tickets = $query->latest('date')->paginate(10)->withQueryString();
        $departments = Department::all();

        return view('superadmin.tickets.index', compact('tickets', 'departments'));
    }

    public function ticketsCreate()
    {
        $departments = Department::pluck('department_name', 'department_id');
        $types       = ['Incident', 'Request'];
        $categories  = ['Hardware', 'Software', 'Network', 'Facility', 'Others'];
        $priorities  = ['Low', 'Medium', 'High', 'Urgent'];

        return view('superadmin.tickets.create', compact('departments', 'types', 'categories', 'priorities'));
    }

    public function ticketsStore(Request $request)
    {
        $request->validate([
            'title'         => 'required|string|max:255',
            'description'   => 'required|string',
            'user_name'     => 'required|string|max:255',
            'user_email'    => 'nullable|email|max:255',
            'department_id' => 'required',
            'type'          => 'required|string',
            'category'      => 'required|string',
            'priority'      => 'required|string',
            'location'      => 'required|string',
            'image'         => 'nullable|image|mimes:jpeg,png,jpg,pdf|max:2048',
        ]);

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('tickets', 'public');
        }

       Ticket::create([
            'ticket_id'     => 'TK-' . strtoupper(uniqid()),
            'user_id'       => Auth::id(),
            'user_name'     => $request->user_name,
            'user_email'    => $request->user_email,
            'department_id' => $request->department_id,
            'title'         => $request->title,
            'description'   => $request->description,
            'type'          => $request->type,
            'category'      => $request->category,
            'priority'      => $request->priority,
            'location'      => $request->location,
            'status'        => 'New',
            'date'          => now(),
            'image'         => $imagePath,
        ]);

        return redirect()->route('superadmin.tickets.index')
            ->with('success', 'New ticket created successfully!');
    }

    public function ticketsDestroy($id)
    {
        $ticket = Ticket::where('ticket_id', $id)->firstOrFail();
        
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

        if ($request->get('sort') === 'oldest') {
            $query->oldest();
        } else {
            $query->latest(); 
        }

        $conversations = $query->get();

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
            'attachment' => 'nullable|file|mimes:jpeg,png,jpg,pdf|max:2048',
        ]);

        $ticket = Ticket::where('ticket_id', $ticketId)->firstOrFail();

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
        $ticket = Ticket::with(['user', 'replies', 'department'])->where('ticket_id', $id)->firstOrFail();

        $categories = ['Hardware', 'Software', 'Network', 'Facility', 'Others'];
        $types      = ['Incident', 'Request'];
        $statuses   = ['New', 'Processing', 'Pending', 'Closed'];
        $priorities = ['Low', 'Medium', 'High', 'Urgent'];

        $departments = Department::all();
        $adminsInDept = User::whereIn('role', ['Admin', 'Super Admin'])->get();

        return view('superadmin.tickets.edit', compact(
            'ticket', 
            'categories', 
            'types', 
            'statuses', 
            'priorities', 
            'departments',
            'adminsInDept'
        ));
    }

    public function ticketsUpdate(Request $request, $id)
    {
        $ticket = Ticket::where('ticket_id', $id)->firstOrFail();

        $oldStatus = $ticket->status;
        $oldPic    = $ticket->pic;

        $ticket->update([
            'category'      => $request->category,
            'type'          => $request->type,
            'status'        => $request->status,
            'priority'      => $request->priority,
            'location'      => $request->location,
            'department_id' => $request->department_id,
            'pic'           => $request->pic,
        ]);

        if ($request->filled('pic') && $oldPic != $request->pic) {
            $picUser = User::where('user_id', $request->pic)->first();
            $picName = $picUser ? ($picUser->full_name ?? $picUser->name) : 'Admin';

            TicketReply::create([
                'ticket_id'   => $ticket->ticket_id,
                'user_id'     => Auth::id(),
                'sender_name' => Auth::user()->full_name ?? 'Super Admin',
                'sender_role' => Auth::user()->role ?? 'Super Admin',
                'message'     => 'PIC_CHANGE:' . $picName,
            ]);
        }

        if ($request->filled('status') && $oldStatus != $request->status) {
            TicketReply::create([
                'ticket_id'   => $ticket->ticket_id,
                'user_id'     => Auth::id(),
                'sender_name' => Auth::user()->full_name ?? 'Super Admin',
                'sender_role' => Auth::user()->role ?? 'Super Admin',
                'message'     => 'STATUS_CHANGE:' . $request->status,
            ]);
        }

        return redirect()->back()->with('success', 'Ticket information updated successfully!');
    }

    // ==========================================
    // INVENTORY MODULE (SUPER ADMIN)
    // ==========================================

    public function inventoryExport(Request $request)
    {
        return Excel::download(new InventoryExport($request), 'inventory_list_' . date('Y_m_d_His') . '.xlsx');
    }

    public function inventoryIndex(Request $request)
    {
        $deptQuery = Department::query();

        if ($request->filled('search') && !$request->filled('department')) {
            $deptQuery->where('department_name', 'like', '%' . $request->search . '%')
                    ->orWhere('code', 'like', '%' . $request->search . '%');
        }

        $departments = $deptQuery->get();

        $query = Inventory::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('item_name', 'like', "%{$search}%")
                ->orWhere('item_type', 'like', "%{$search}%")
                ->orWhere('item_manufacturer', 'like', "%{$search}%")
                ->orWhere('item_model', 'like', "%{$search}%")
                ->orWhere('serial_number', 'like', "%{$search}%")
                ->orWhere('item_location', 'like', "%{$search}%")
                ->orWhereHas('user', function ($userQuery) use ($search) {
                    $userQuery->where('full_name', 'like', "%{$search}%");
                });
            });
        }

        if ($request->filled('status')) {
            $query->where('item_status', $request->status);
        }

        $inventories = $query->latest('item_id')->paginate(10)->withQueryString();

        return view('superadmin.inventory.index', compact('inventories', 'departments'));
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
            'items'                     => 'required|array|min:1',
            'items.*.item_name'         => 'required|string|max:255',
            'items.*.serial_number'     => 'required|string|max:255',
            'items.*.item_manufacturer' => 'required|string|max:255',
            'items.*.item_type'         => 'required|string|max:255',
            'items.*.item_category'     => 'required|string|max:255',
            'items.*.item_model'        => 'required|string|max:255',
            'items.*.item_status'       => 'required|in:Active,Inactive',
            'items.*.item_location'     => 'required|string|max:255',
            'items.*.item_attachment'   => 'nullable|image|mimes:jpeg,png,jpg,pdf|max:2048',
        ]);

        $createdCount = 0;

        foreach ($request->items as $index => $itemData) {
            $attachmentPath = null;

            if ($request->hasFile("items.{$index}.item_attachment")) {
                $attachmentPath = $request->file("items.{$index}.item_attachment")->store('inventories', 'public');
            }

            Inventory::create([
                'item_name'         => $itemData['item_name'],
                'serial_number'     => $itemData['serial_number'],
                'item_manufacturer' => $itemData['item_manufacturer'],
                'item_type'         => $itemData['item-type'],
                'item_category'     => $itemData['item_category'],
                'item_model'        => $itemData['item_model'],
                'item_status'       => $itemData['item_status'],
                'item_location'     => $itemData['item_location'],
                'item_attachment'   => $attachmentPath,
                'user_id'           => $user->user_id ?? $user->id,
                'department_id'     => $user->department_id,
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

    public function inventoryEdit($id)
    {
        $item = Inventory::where('item_id', $id)->orWhere('id', $id)->firstOrFail();
        $departments = Department::all();

        return view('superadmin.inventory.edit', compact('item', 'departments'));
    }
}