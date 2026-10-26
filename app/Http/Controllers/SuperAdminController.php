<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Ticket;
use App\Models\Inventory;
use App\Models\Department;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class SuperAdminController extends Controller
{
    // 1. DASHBOARD SUPER ADMIN
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

    // 2. USER MANAGEMENT - SENARAI USER (usersIndex)
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

    // 3. USER MANAGEMENT - FORM TAMBAH USER BAHARU (usersCreate)
    public function usersCreate()
    {
        $departments = Department::pluck('department_name')->toArray();
        if (empty($departments)) {
            $departments = ['IT', 'Facility', 'Management', 'HR', 'Finance'];
        }
        $roles = ['Staff', 'Admin', 'Super Admin'];

        return view('superadmin.users.create', compact('departments', 'roles'));
    }

    // 4. USER MANAGEMENT - SIMPAN USER BAHARU (usersStore)
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

  // 5. USER MANAGEMENT - PADAM USER (usersDestroy)
    public function usersDestroy($id)
    {
        $user = User::where('user_id', $id)->orWhere('id', $id)->firstOrFail();
        $user->delete();

        return redirect()->route('superadmin.users.index')
            ->with('success', 'User account deleted successfully!');
    }

    // 6. DEPARTMENT MANAGEMENT - SENARAI & FORM (departmentsIndex)
    public function departmentsIndex()
    {
        $departments = Department::latest()->get();
        return view('superadmin.departments.index', compact('departments'));
    }

    // 7. DEPARTMENT MANAGEMENT - SIMPAN JABATAN (departmentsStore)
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

    // 8. DEPARTMENT MANAGEMENT - PADAM JABATAN (departmentsDestroy)
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
        return view('superadmin.tickets.create', compact('departments'));
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
                  ->orWhere('serial_number', 'like', "%{$search}%")
                  ->orWhere('brand', 'like', "%{$search}%");
            });
        }

        if ($request->filled('department')) {
            $query->where('department', $request->department);
        }

        $inventories = $query->latest()->paginate(10)->withQueryString();

        return view('superadmin.inventory.index', compact('inventories'));
    }

    public function inventoryCreate()
    {
        $departments = Department::pluck('department_name')->toArray();
        return view('superadmin.inventory.create', compact('departments'));
    }
}