<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TicketController extends Controller
{
    public function index()
    {
        // Ambil tiket pengguna jika ada sesi login, atau ambil semua jika tiada
        $tickets = Auth::check() 
            ? Ticket::where('user_id', Auth::id())->latest()->get() 
            : Ticket::latest()->get();

        return view('tickets.index', compact('tickets'));
    }

    public function create()
    {
        // Mengambil nilai ENUM daripada pangkalan data secara dinamik
        $types = $this->getEnumValues('tickets', 'type');
        $priorities = $this->getEnumValues('tickets', 'priority');
        
        // Senarai kategori (boleh ditambah/diubah mengikut keperluan)
        $categories = ['Hardware', 'Software', 'Rangkaian', 'Fasiliti', 'Others'];

        return view('tickets.create', compact('types', 'priorities', 'categories'));
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
            'title' => 'required|string',
            'type' => 'required|in:Incident,Request',
            'category' => 'required|string',
            'priority' => 'required|in:Low,Medium,High,Urgent', // Dibetulkan: Medium
            'location' => 'required|string',
            'description' => 'required|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('tickets', 'public');
        }

        $user = Auth::user();

        Ticket::create([
            'user_name' => $user->full_name ?? $user->name ?? 'Pengguna',
            'user_email' => $user->email ?? 'user@nmuc.edu.my',
            'title' => $request->title,
            'description' => $request->description,
            'image' => $imagePath ?? 'no-image.png',
            'type' => $request->type,
            'category' => $request->category,
            'date' => now()->toDateString(),
            'status' => 'New',
            'priority' => $request->priority,
            'user_id' => $user->user_id ?? 1,
            'location' => $request->location,
        ]);

        return redirect()->route('tickets.index')->with('success', 'Tiket aduan berjaya dihantar!');
    }
}
