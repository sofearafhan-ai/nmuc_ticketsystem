<?php

namespace App\Exports\SuperAdmin;

use App\Models\Inventory;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class InventoryExport implements FromQuery, WithHeadings, WithMapping, ShouldAutoSize, WithStyles
{
    protected $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function query()
    {
        $query = Inventory::query();

        if ($this->request->filled('search')) {
            $search = $this->request->search;
            $query->where(function ($q) use ($search) {
                $q->where('item_name', 'like', "%{$search}%")
                  ->orWhere('item_type', 'like', "%{$search}%")
                  ->orWhere('item_manufacturer', 'like', "%{$search}%")
                  ->orWhere('item_model', 'like', "%{$search}%")
                  ->orWhere('serial_number', 'like', "%{$search}%")
                  ->orWhere('item_location', 'like', "%{$search}%")
                  ->orWhereHas('user', function ($userQuery) use ($search) {
                    $userQuery->where('full_name', 'like', "%{$search}%");
});;
            });
        }

        if ($this->request->filled('status')) {
            $query->where('item_status', $this->request->status);
        }

        return $query->latest('item_id');
    }

    public function headings(): array
    {
        return [
            'No',
            'Name',
            'Type',
            'Manufacturer',
            'Model',
            'Serial Number',
            'Location',
            'Status',
            'Registered By',
            'Date Created',
        ];
    }

    public function map($item): array
    {
        return [
            $item->item_id ?? $item->id,
            $item->name ?? $item->item_name,
            $item->item_type ?? $item->type ?? '-',
            $item->item_manufacturer ?? $item->manufacturer ?? '-',
            $item->item_model ?? $item->model ?? '-',
            $item->serial_number ?? '-',
            $item->item_location ?? $item->location ?? '-',
            $item->item_status ?? $item->status ?? '-',
            $item->user_id ?? '-',
            !empty($item->created_at) ? \Carbon\Carbon::parse($item->created_at)->format('d M Y') : '-',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}