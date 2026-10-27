<?php 

namespace App\Exports;

use App\Models\Ticket; 
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Illuminate\Http\Request;

class TicketsExport implements FromQuery, WithHeadings, WithMapping
{
    protected $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function query()
    {
        $query = Ticket::query(); 

        if ($this->request->filled('search')) {
            $query->where('title', 'like', '%' . $this->request->search . '%');
        }

        if ($this->request->filled('category')) {
            $query->where('category', $this->request->category);
        }

        if ($this->request->filled('type')) {
            $query->where('type', $this->request->type);
        }

        if ($this->request->filled('priority')) {
            $query->where('priority', $this->request->priority);
        }

        if ($this->request->filled('status')) {
            $query->where('status', $this->request->status);
        }

        if ($this->request->filled('month')) {
            $query->whereMonth('created_at', $this->request->month);
        }

        if ($this->request->filled('year')) {
            $query->whereYear('created_at', $this->request->year);
        }

        return $query->latest();
    }

    public function headings(): array
    {
        return [
            'Ticket ID',
            'Title',
            'Submitted By',
            'Category',
            'Priority',
            'Status',
            'PiC',
            'Date Created',
        ];
    }

    public function map($ticket): array
    {
        return [
            $ticket->ticket_id,
            $ticket->title,
            $ticket->user_name ?? ($ticket->user->name ?? 'User'),
            $ticket->category,
            $ticket->priority,
            $ticket->status,
            $ticket->picAdmin->full_name ?? $ticket->picAdmin->name ?? $ticket->pic ?? '-',
            !empty($ticket->created_at) ? \Carbon\Carbon::parse($ticket->created_at)->format('d M Y') : '-',
        ];
    }
}