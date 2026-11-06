<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class GuestTicketController extends Controller
{
    public function create()
    {
        return view('guest.tickets.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'title'        => 'required|string|max:255',
            'description'  => 'required|string',
            'user_name'    => 'required|string|max:255',
            'user_email'   => 'required|email|max:255',
            'nmuc_email'   => 'required|email|exists:users,email',
            'student_id'   => 'required|string|max:50',
            'type'         => 'required|in:Incident,Request',
            'category'     => 'required|string',
            'priority'     => 'required|in:Low,Medium,High,Urgent',
            'image'        => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
        ]);

        $nmucUser = User::where('email', $request->nmuc_email)->first();

        $itDepartmentId = 2; 

        $imageName = null;
        if ($request->hasFile('image')) {
            $imageName = $request->file('image')->store('tickets', 'public');
        }

        $fullDescription = "<p><strong>Title:</strong> " . e($request->title) . "</p>" .
                           "<p><strong>Student ID:</strong> " . e($request->student_id) . "</p>" .
                           "<p><strong>NMUC Email:</strong> " . e($request->nmuc_email) . "</p>" .
                           "<hr><br>" . $request->description;

        $ticket = Ticket::create([
            'user_name'     => $request->user_name,
            'user_email'    => $request->user_email,  
            'title'         => $request->title,
            'description'   => $fullDescription,
            'image'         => $imageName,
            'type'          => $request->type,
            'category'      => $request->category,
            'date'          => now()->format('Y-m-d'),
            'status'        => 'New',
            'priority'      => $request->priority,
            'user_id'       => $nmucUser->user_id,     
            'department_id' => 2,
            'location'      => $request->location   
        ]);

       return redirect()->back()->with('success', 'Your password reset ticket has been submitted successfully to IT Department.');
    }
}