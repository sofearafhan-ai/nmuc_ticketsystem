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
            // Tapis mengikut ID Admin atau Jabatan
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

    // 2. SENARAI TIKET ADMIN
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

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $tickets = $query->latest()->paginate(10)->withQueryString();

        return view('admin.tickets.index', compact('tickets', 'adminDept'));
    }

    // 2. DEPARTMENT ADMIN INVENTORY LIST (dynamic)
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

        if ($request->filled('status')) {
            $query->where('item_status', $request->status);
        }

        $inventories = $query->latest('item_id')->paginate(10)->withQueryString();

        return view('admin.inventory.index', compact('inventories', 'adminDept'));
    }

    // 3. NEW INVENTORY SAVE
    public function storeInventory(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'item_name'     => 'required|string',
            'item_category' => 'required|string',
            'item_model'    => 'required|string',
            'item_status'   => 'required|in:Active,Inactive',
            'item_location' => 'required|string',
        ]);

        Inventory::create([
            'item_name'     => $request->item_name,
            'item_category' => $request->item_category,
            'item_model'    => $request->item_model,
            'item_status'   => $request->item_status,
            'item_location' => $request->item_location,
            'user_id'       => $user->user_id ?? $user->id, 
        ]);

        return redirect()->back()->with('success', 'Aset inventori berjaya ditambah!');
    }
}