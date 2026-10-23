@extends('layouts.admin')

@section('title', 'Admin Dashboard - ' . $adminDept)

@section('content')
<!-- Chart.js Script -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<div class="space-y-6">

    <!-- Quick Stats Cards (Assigned Tickets) -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <div class="bg-white p-4 rounded-xl shadow-sm border border-orange-200 flex items-center justify-between">
            <div>
                <p class="text-xs font-medium text-slate-500">New Ticket</p>
                <h3 class="text-2xl font-bold text-blue-600 mt-0.5">{{ $ticketStats['new'] }}</h3>
            </div>
            <div class="w-10 h-10 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center text-base">
                <i class="fa-solid fa-envelope-open"></i>
            </div>
        </div>

        <div class="bg-white p-4 rounded-xl shadow-sm border border-orange-200 flex items-center justify-between">
            <div>
                <p class="text-xs font-medium text-slate-500">In Process</p>
                <h3 class="text-2xl font-bold text-amber-500 mt-0.5">{{ $ticketStats['processing'] }}</h3>
            </div>
            <div class="w-10 h-10 rounded-lg bg-amber-50 text-amber-500 flex items-center justify-center text-base">
                <i class="fa-solid fa-spinner"></i>
            </div>
        </div>

        <div class="bg-white p-4 rounded-xl shadow-sm border border-orange-200 flex items-center justify-between">
            <div>
                <p class="text-xs font-medium text-slate-500">Pending</p>
                <h3 class="text-2xl font-bold text-purple-600 mt-0.5">{{ $ticketStats['pending'] }}</h3>
            </div>
            <div class="w-10 h-10 rounded-lg bg-purple-50 text-purple-600 flex items-center justify-center text-base">
                <i class="fa-solid fa-clock-rotate-left"></i>
            </div>
        </div>

        <div class="bg-white p-4 rounded-xl shadow-sm border border-orange-200 flex items-center justify-between">
            <div>
                <p class="text-xs font-medium text-slate-500">Closed</p>
                <h3 class="text-2xl font-bold text-emerald-600 mt-0.5">{{ $ticketStats['close'] }}</h3>
            </div>
            <div class="w-10 h-10 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center text-base">
                <i class="fa-solid fa-circle-check"></i>
            </div>
        </div>
    </div>

    <!-- Charts Section -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        
        <!-- Ticket Status Chart -->
        <div class="bg-white p-5 rounded-xl shadow-md border border-orange-200 space-y-4">
            <div class="flex justify-between items-center border-b pb-3">
                <h3 class="font-bold text-slate-800 text-sm flex items-center gap-2">
                    <i class="fa-solid fa-chart-pie text-orange-500"></i> Assigned To ({{ $adminDept }})
                </h3>
                <span class="text-xs bg-orange-100 text-orange-800 font-bold px-2.5 py-1 rounded-full">
                    Total: {{ $ticketStats['total'] }} Ticket
                </span>
            </div>
            <div class="h-64 flex justify-center items-center">
                <canvas id="ticketChart"></canvas>
            </div>
        </div>

        <!-- Inventory Status Chart (Entire Department) -->
        <div class="bg-white p-5 rounded-xl shadow-md border border-orange-200 space-y-4">
            <div class="flex justify-between items-center border-b pb-3">
                <h3 class="font-bold text-slate-800 text-sm flex items-center gap-2">
                    <i class="fa-solid fa-chart-bar text-orange-500"></i> ({{ $adminDept }}) Department Inventory Status
                </h3>
                <span class="text-xs bg-slate-100 text-slate-600 px-2.5 py-1 rounded-full font-bold">
                    Total: {{ $inventoryStats['total'] }} Asset
                </span>
            </div>
            <div class="h-64 flex justify-center items-center">
                <canvas id="inventoryChart"></canvas>
            </div>
        </div>

    </div>

    <!-- Recent Tickets List Section -->
    <div class="bg-white p-5 rounded-xl shadow-md border border-orange-200 space-y-4">
        <div class="flex justify-between items-center border-b pb-3">
            <h3 class="font-bold text-slate-800 text-sm flex items-center gap-2">
                <i class="fa-solid fa-ticket text-orange-500"></i> Recent Tickets
            </h3>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left text-slate-600">
                <thead class="text-xs text-slate-500 uppercase bg-slate-50 rounded-lg">
                    <tr>
                        <th scope="col" class="px-4 py-3">Ticket ID</th>
                        <th scope="col" class="px-4 py-3">Subject</th>
                        <th scope="col" class="px-4 py-3">User</th>
                        <th scope="col" class="px-4 py-3">Status</th>
                        <th scope="col" class="px-4 py-3">Date</th>
                        <th scope="col" class="px-4 py-3 text-center">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($recentTickets ?? [] as $ticket)
                        <tr class="hover:bg-slate-50/50 transition">
                            <td class="px-4 py-3 font-semibold text-slate-800">
                                #{{ $loop->iteration }}
                            </td>
                            <td class="px-4 py-3 font-medium text-slate-700">
                                <a href="{{ route('admin.messages.show', $ticket->ticket_id) }}" class="hover:text-orange-600 hover:underline">
                                {{ Str::limit($ticket->subject ?? $ticket->title, 35) }}
                                    </a>
                            </td>
                            <td class="px-4 py-3">
                                {{ $ticket->user_name }}
                            </td>
                            <td class="px-4 py-3">
                                @php
                                    $statusClasses = [
                                        'new' => 'bg-emerald-100 text-emerald-700',
                                        'processing' => 'bg-blue-100 text-blue-700',
                                        'pending' => 'bg-amber-100 text-amber-700',
                                        'close' => 'bg-red-100 text-red-700',
                                    ];
                                    $status = strtolower($ticket->status ?? 'new');
                                    $badgeClass = $statusClasses[$status] ?? 'bg-slate-100 text-slate-700';
                                @endphp
                                <span class="px-2.5 py-1 text-xs font-bold rounded text-white inline-block capitalize {{ $badgeClass }}">
                                    {{ $ticket->status }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-xs text-slate-500">
                                {{ $ticket->created_at ? \Carbon\Carbon::parse($ticket->created_at)->format('d M Y') : '-' }}
                            </td>
                            <td class="p-2 text-center">
                                    <div class="flex items-center justify-center gap-2">
                                        <a href="{{ route('admin.tickets.edit', $ticket->ticket_id) }}" 
                                           class="px-2.5 py-1.5 bg-slate-100 hover:bg-orange-500 text-slate-600 hover:text-white rounded-lg text-xs font-semibold transition border border-slate-200 hover:border-orange-500 flex items-center gap-1 cursor-pointer">
                                            <i class="fa-solid fa-pen-to-square"></i>
                                            <span>Edit</span>
                                        </a>

                                        <form action="{{ route('admin.tickets.destroy', $ticket->ticket_id) }}" method="POST" 
                                              onsubmit="return confirm('Are you sure you want to delete this ticket?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" 
                                                    class="px-2.5 py-1.5 bg-red-50 hover:bg-red-600 text-red-600 hover:text-white rounded-lg text-xs font-semibold transition border border-red-200 hover:border-red-600 flex items-center gap-1 cursor-pointer">
                                                <i class="fa-solid fa-trash-can"></i>
                                                <span>Delete</span>
                                            </button>
                                        </form>
                                    </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-6 text-center text-slate-400">
                                No recent tickets found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>

<!-- Chart.js Script Initialization -->
<script>
    document.addEventListener("DOMContentLoaded", function () {
        // 1. TICKET CHART (Doughnut - Assigned Only)
        const ctxTicket = document.getElementById('ticketChart').getContext('2d');
        new Chart(ctxTicket, {
            type: 'doughnut',
            data: {
                labels: ['New', 'Processing', 'Pending', 'Close'],
                datasets: [{
                    data: [
                        {{ $ticketStats['new'] ?? 0 }},
                        {{ $ticketStats['processing'] ?? 0 }},
                        {{ $ticketStats['pending'] ?? 0 }},
                        {{ $ticketStats['close'] ?? $ticketStats['closed'] ?? 0 }}
                    ],
                    backgroundColor: ['#2563eb', '#f59e0b', '#9333ea', '#10b981'],
                    borderWidth: 2,
                    borderColor: '#ffffff'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'bottom' }
                }
            }
        });

        // 2. INVENTORY CHART (Bar - Entire Department)
        const ctxInventory = document.getElementById('inventoryChart').getContext('2d');
        new Chart(ctxInventory, {
            type: 'bar',
            data: {
                labels: ['Active', 'Inactive'],
                datasets: [{
                    label: 'Asset Total',
                    data: [
                        {{ $inventoryStats['active'] }},
                        {{ $inventoryStats['inactive'] }}
                    ],
                    backgroundColor: ['#10b981', '#64748b'],
                    borderRadius: 8
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: { beginAtZero: true, ticks: { stepSize: 1 } }
                },
                plugins: {
                    legend: { display: false }
                }
            }
        });
    });
</script>
@endsection