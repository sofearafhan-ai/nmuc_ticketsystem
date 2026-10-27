<?php

namespace App\Exports;

use App\Models\Inventory;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Illuminate\Http\Request;

class InventoryExport implements FromQuery, WithHeadings, WithMapping
{
    protected $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function query()
    {
        $query = Inventory::query();

        // 1. Filter Search
        if ($this->request->filled('search')) {
            $search = $this->request->search;
            $query->where(function ($q) use ($search) {
                $q->where('item_name', 'like', "%{$search}%")
                  ->orWhere('item_model', 'like', "%{$search}%")
                  ->orWhere('item_category', 'like', "%{$search}%")
                  ->orWhere('item_location', 'like', "%{$search}%");
            });
        }

        // 2. Filter Category 
        if ($this->request->filled('category')) {
            $query->where('item_category', $this->request->category);
        }

        // 3. Filter Status
        if ($this->request->filled('status')) {
            $query->where('item_status', $this->request->status);
        }

        return $query->latest('item_id');
    }

    public function headings(): array
    {
        return [
            'ID',
            'Item Name',
            'Category',
            'Model',
            'Status',
            'Location',
            'Date Added',
        ]; 
    }

    public function map($item): array
    {
        return [
            $item->item_id ?? $item->id,
            $item-> name ?? $item->item_name,
            $item->item_category ?? $item->category ?? '-',
            $item->item_model ?? $item->model ?? '-',
            $item->item_status ?? $item->status ?? '-',
            $item->item_location ?? $item->location ?? '-',
            !empty($item->created_at) ? \Carbon\Carbon::parse($item->created_at)->format('d M Y') : '-',
        ];
    }
}
