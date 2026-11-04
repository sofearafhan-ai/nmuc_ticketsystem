<?php

namespace App\Exports\User;

use App\Models\Ticket;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Illuminate\Support\Facades\Auth;

class TicketsExport implements FromQuery, WithHeadings, WithMapping
{
    protected $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function query()
    {
        $query = Ticket::where('user_id', Auth::id());

        if ($this->request->filled('search')) {
            $search = $this->request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('ticket_id', 'like', "%{$search}%");
            });
        }

        if ($this->request->filled('category')) {
            $query->where('category', $this->request->category);
        }

        if ($this->request->filled('status')) {
            $query->where('status', $this->request->status);
        }

        return $query->latest();
    }

    public function headings(): array
    {
        return [
            'Ticket ID',
            'Title',
            'Category',
            'Priority',
            'Department',
            'Location',
            'Status',
            'Created At',
        ];
    }

    public function map($ticket): array
    {
        return [
            $ticket->ticket_id,
            $ticket->title,
            $ticket->category,
            $ticket->priority,
            $ticket->department,
            $ticket->location,
            $ticket->status,
            !empty($ticket->created_at) ? \Carbon\Carbon::parse($ticket->created_at)->format('d M Y') : '-',
        ];
    }
}