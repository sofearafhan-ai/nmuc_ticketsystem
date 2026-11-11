<?php

namespace App\Exports\SuperAdmin;

use App\Models\User;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class UserExport implements FromQuery, WithHeadings, WithMapping, ShouldAutoSize, WithStyles
{
    protected $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function query()
    {
        $query = User::with('department');

        if ($this->request->filled('search')) {
            $search = $this->request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('full_name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($this->request->filled('department_id')) {
            $query->where('department_id', $this->request->department_id);
        }

        if ($this->request->filled('role')) {
            $query->where('role', $this->request->role);
        }

        if ($this->request->filled('status')) {
            $query->where('user_status', $this->request->status);
        }

        return $query->latest();
    }

    public function headings(): array
    {
        return [
            'User ID',
            'Full Name',
            'Email Address',
            'Department',
            'Role',
            'Status',
            'Created At',
        ];
    }

    public function map($user): array
    {
        $userId = $user->user_id ?? $user->id;
        $fullName = $user->full_name ?? $user->name ?? '-';
        $departmentName = $user->department->department_name ?? 'No Department';

        return [
            $userId,
            $fullName,
            $user->email,
            $departmentName,
            $user->role,
            $user->user_status ?? 'Active',
            !empty($user->created_at) ? \Carbon\Carbon::parse($user->created_at)->format('d M Y, h:i A') : '-',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true, 'size' => 11]],
        ];
    }
}