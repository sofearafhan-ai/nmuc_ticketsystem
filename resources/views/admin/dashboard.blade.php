@extends('layouts.admin')

@section('title', 'Admin Dashboard - ' . $adminDept)

@section('content')
<!-- Chart.js Script -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<div class="space-y-6">

    <!-- 1. HEADER / QUICK OVERVIEW CARDS -->
    <div class="bg-gradient-to-r from-orange-100 via-indigo-50 to-blue-100 p-6 rounded-3xl shadow-sm">
        <div class="mb-4">
            <h2 class="text-xl font-bold text-slate-800">Quick Overview</h2>
            <p class="text-xs text-slate-500">Department-wide overview statistics for {{ $adminDept }} department</p>
        </div>

        <div class="grid grid-cols-2 md:grid-cols-6 gap-3">
            <!-- Card 1: Total Tickets -->
            <div class="bg-white/70 backdrop-blur-md p-4 rounded-2xl border border-white/60 shadow-sm flex flex-col justify-between">
                <div>
                    <h3 class="text-2xl font-black text-slate-800">{{ $deptTicketStats['total'] ?? 0 }}</h3>
                    <p class="text-xs font-semibold text-slate-500 mt-1">Total Tickets</p>
                </div>
            </div>

            <!-- Card 2: New Tickets -->
            <div class="bg-white/70 backdrop-blur-md p-4 rounded-2xl border border-white/60 shadow-sm flex flex-col justify-between">
                <div>
                    <h3 class="text-2xl font-black text-slate-800">{{ $deptTicketStats['new'] ?? 0 }}</h3>
                    <p class="text-xs font-semibold text-slate-500 mt-1">New Tickets</p>
                </div>
            </div>

            <!-- Card 3: Processing Tickets -->
            <div class="bg-white/70 backdrop-blur-md p-4 rounded-2xl border border-white/60 shadow-sm flex flex-col justify-between">
                <div>
                    <h3 class="text-2xl font-black text-slate-800">{{ $deptTicketStats['processing'] ?? 0 }}</h3>
                    <p class="text-xs font-semibold text-slate-500 mt-1">In Process</p>
                </div>
            </div>

            <!-- Card 4: Pending Tickets -->
            <div class="bg-white/70 backdrop-blur-md p-4 rounded-2xl border border-white/60 shadow-sm flex flex-col justify-between">
                <div>
                    <h3 class="text-2xl font-black text-slate-800">{{ $deptTicketStats['pending'] ?? 0 }}</h3>
                    <p class="text-xs font-semibold text-slate-500 mt-1">Pending Tickets</p>
                </div>
            </div>

            <!-- Card 5: Close Tickets -->
            <div class="bg-white/70 backdrop-blur-md p-4 rounded-2xl border border-white/60 shadow-sm flex flex-col justify-between">
                <div>
                    <h3 class="text-2xl font-black text-slate-800">{{ $deptTicketStats['close'] ?? 0 }}</h3>
                    <p class="text-xs font-semibold text-slate-500 mt-1">Close Tickets</p>
                </div>
            </div>

            <!-- Card 6: Inventory Assets -->
            <div class="bg-white/70 backdrop-blur-md p-4 rounded-2xl border border-white/60 shadow-sm flex flex-col justify-between">
                <div>
                    <h3 class="text-2xl font-black text-slate-800">{{ $inventoryStats['total'] ?? 0 }}</h3>
                    <p class="text-xs font-semibold text-slate-500 mt-1">Total Assets</p>
                </div>
            </div>
        </div>
    </div>

    <!-- 2. BOTTOM SECTION -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <!-- Inventory & Ticket Trends -->
        <div class="lg:col-span-2 bg-white p-6 rounded-3xl shadow-sm border border-slate-100 flex flex-col justify-between">
            <div>
                <h3 class="font-bold text-slate-800 text-lg mb-1">Inventory & Ticket Trends</h3>
                <p class="text-xs text-slate-400 mb-4">Department-wide monthly trends</p>
            </div>

            <div class="h-64 relative">
                <canvas id="stackedBarChart"></canvas>
            </div>

            <!-- Metric Legend Bottom -->
            <div class="flex items-center gap-8 pt-4 border-t border-slate-100 mt-2">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-full bg-emerald-100 text-emerald-600 flex items-center justify-center text-xs">
                        <i class="fa-solid fa-circle-check"></i>
                    </div>
                    <div>
                        <h4 class="font-extrabold text-slate-800 text-sm">{{ $inventoryStats['active'] ?? 0 }}</h4>
                        <p class="text-xs text-slate-400">Active Assets</p>
                    </div>
                </div>

                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-full bg-slate-100 text-slate-600 flex items-center justify-center text-xs">
                        <i class="fa-solid fa-clock"></i>
                    </div>
                    <div>
                        <h4 class="font-extrabold text-slate-800 text-sm">{{ $inventoryStats['inactive'] ?? 0 }}</h4>
                        <p class="text-xs text-slate-400">Inactive Assets</p>
                    </div>
                </div>

                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-full bg-orange-100 text-orange-500 flex items-center justify-center text-xs">
                        <i class="fa-solid fa-box"></i>
                    </div>
                    <div>
                        <h4 class="font-extrabold text-slate-800 text-sm">{{ $deptTicketStats['total'] ?? 0 }}</h4>
                        <p class="text-xs text-slate-400">Total Dept Tickets</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Doughnut Graph -->
        <div class="bg-white p-6 rounded-3xl shadow-sm border border-slate-100 flex flex-col justify-between">
            <div>
                <h3 class="font-bold text-slate-800 text-lg mb-1">My Tickets Overview</h3>
                <p class="text-xs text-slate-400 mb-2">Assigned directly to you</p>
            </div>

            <div class="relative h-56 flex justify-center items-center">
                <canvas id="normalDoughnutChart"></canvas>
                <div class="absolute inset-0 flex flex-col items-center justify-center pointer-events-none">
                    <span class="text-4xl font-black text-slate-800">{{ $myTicketStats['total'] ?? 0 }}</span>
                    <span class="text-xs text-slate-400 font-medium">My Total Tickets</span>
                </div>
            </div>

            <div class="space-y-2.5 text-xs font-semibold border-t border-slate-100 pt-4">
                <div class="flex justify-between items-center">
                    <div class="flex items-center gap-2">
                        <span class="w-2.5 h-2.5 rounded-full bg-emerald-500"></span>
                        <span class="text-emerald-600">New Tickets</span>
                    </div>
                    <span class="text-emerald-500 font-bold">{{ $myTicketStats['new'] ?? 0 }}</span>
                </div>

                <div class="flex justify-between items-center">
                    <div class="flex items-center gap-2">
                        <span class="w-2.5 h-2.5 rounded-full bg-blue-500"></span>
                        <span class="text-blue-600">In Processing</span>
                    </div>
                    <span class="text-blue-500 font-bold">{{ $myTicketStats['processing'] ?? 0 }}</span>
                </div>

                <div class="flex justify-between items-center">
                    <div class="flex items-center gap-2">
                        <span class="w-2.5 h-2.5 rounded-full bg-amber-500"></span>
                        <span class="text-amber-600">Pending Tickets</span>
                    </div>
                    <span class="text-amber-500 font-bold">{{ $myTicketStats['pending'] ?? 0 }}</span>
                </div>

                <div class="flex justify-between items-center">
                    <div class="flex items-center gap-2">
                        <span class="w-2.5 h-2.5 rounded-full bg-red-500"></span>
                        <span class="text-red-600">Closed Tickets</span>
                    </div>
                    <span class="text-red-500 font-bold">{{ $myTicketStats['close'] ?? 0 }}</span>
                </div>
            </div>
        </div>

    </div>

    <!-- 4. RECENT UNASSIGNED TICKETS TABLE -->
    <div class="bg-white p-6 rounded-3xl shadow-sm border border-slate-100 space-y-4">
        <div class="flex justify-between items-center border-b pb-3">
            <div>
                <h3 class="font-bold text-slate-800 text-base flex items-center gap-2">
                    <i class="fa-solid fa-ticket text-orange-500"></i> New Unassigned Tickets
                </h3>
                <p class="text-xs text-slate-400">Tickets awaiting PIC assignment for {{ $adminDept }} department</p>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left text-slate-600">
                <thead class="text-xs text-slate-500 uppercase bg-slate-50 rounded-lg">
                    <tr>
                        <th scope="col" class="px-4 py-3 text-center">Ticket ID</th>
                        <th scope="col" class="px-4 py-3 text-center">Title</th>
                        <th scope="col" class="px-4 py-3 text-center">Submitted by</th>
                        <th scope="col" class="px-4 py-3 text-center">Category</th>
                        <th scope="col" class="px-4 py-3 text-center">Priority</th>
                        <th scope="col" class="px-4 py-3 text-center">Status</th>
                        <th scope="col" class="px-4 py-3 text-center">Date</th>
                        <th scope="col" class="px-4 py-3 text-center">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($recentTickets as $ticket)
                    @php
                        $tId = $ticket->ticket_id ?? $ticket->id;
                    @endphp
                    <tr class="border-b border-slate-100 hover:bg-slate-50 transition">
                        <td class="py-3 px-4 text-xs font-extrabold text-slate-900 text-center">
                            #TC-{{ $tId }}
                        </td>
                        <td class="py-3 px-4 text-xs text-slate-700 font-medium">
                            {{ $ticket->title }}
                        </td>
                        <td class="py-3 px-4 text-xs text-slate-700 font-medium text-center">
                            {{ $ticket->user_name ?? ($ticket->user->name ?? 'User') }}
                        </td>
                        <td class="py-3 px-4 text-xs text-slate-700 font-medium text-center">
                                {{ $ticket->category }}
                            </span>
                        </td>
                        <td class="py-3 px-4 text-center">
                            <span class="px-2 py-0.5 font-bold rounded-full text-[10px]
                                @if($ticket->priority == 'Urgent') bg-red-100 text-red-800
                                @elseif($ticket->priority == 'High') bg-orange-200 text-orange-900
                                @elseif($ticket->priority == 'Medium') bg-yellow-100 text-yellow-800
                                @else bg-slate-100 text-slate-800 @endif">
                                {{ $ticket->priority }}
                            </span>
                        </td>
                        <td class="py-3 px-4 text-center">
                            <span class="px-2.5 py-1 rounded-full font-bold text-xs bg-emerald-100 text-emerald-700">
                                {{ $ticket->status }}
                            </span>
                        </td>
                        <td class="py-3 px-4 text-xs text-slate-500 text-center">
                            @if(!empty($ticket->created_at))
                                {{ is_string($ticket->created_at) ? \Carbon\Carbon::parse($ticket->created_at)->format('d M Y') : $ticket->created_at->format('d M Y') }}
                            @else
                                {{ $ticket->date ? \Carbon\Carbon::parse($ticket->date)->format('d M Y') : '-' }}
                            @endif
                        </td>
                        <td class="py-3 px-4 text-center">
                            <div class="flex items-center justify-center gap-2">
                                <a href="{{ route('admin.tickets.edit', $tId) }}" 
                                   class="px-3 py-1.5 bg-orange-500 hover:bg-orange-600 text-white rounded-lg text-xs font-bold transition flex items-center gap-1">
                                    <i class="fa-solid fa-user-plus"></i> Assign PIC
                                </a>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-4 py-6 text-center text-slate-400 text-xs font-medium">
                            No unassigned tickets available right now.
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>

<!-- CHART INITIALIZATION SCRIPT -->
<script>
    document.addEventListener("DOMContentLoaded", function () {
        
        // 1. Stacked Bar Chart (Inventory & Ticket Trends)
        const ctxBar = document.getElementById('stackedBarChart').getContext('2d');
        new Chart(ctxBar, {
            type: 'bar',
            data: {
                labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'June', 'July', 'Aug', 'Sept', 'Oct', 'Nov', 'Dec'],
                datasets: [
                    {
                        label: 'Active Assets',
                        data: {!! json_encode($activeMonthly ?? [null, null, null, null, null, null, null, null, null, 0, null, null]) !!},
                        backgroundColor: '#10b981',
                        borderRadius: 8,
                        borderSkipped: false,
                        barThickness: 16
                    },
                    {
                        label: 'Inactive Assets',
                        data: {!! json_encode($inactiveMonthly ?? [null, null, null, null, null, null, null, null, null, 0, null, null]) !!},
                        backgroundColor: '#94a3b8',
                        borderRadius: 8,
                        borderSkipped: false,
                        barThickness: 16
                    },
                    {
                        label: 'Total Tickets',
                        data: {!! json_encode($ticketsMonthly ?? [null, null, null, null, null, null, null, null, null, 0, null, null]) !!},
                        backgroundColor: '#f97316',
                        borderRadius: 8,
                        borderSkipped: false,
                        barThickness: 16
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    x: {
                        stacked: true,
                        grid: { display: false },
                        border: { display: false }
                    },
                    y: {
                        stacked: true,
                        beginAtZero: true,
                        grid: { color: '#f8fafc' },
                        border: { display: false },
                        ticks: { stepSize: 1 }
                    }
                },
                plugins: {
                    legend: { display: false }
                }
            }
        });

        // 2. Doughnut Chart (Personal Tickets Status)
        const ctxNormal = document.getElementById('normalDoughnutChart').getContext('2d');
        
        const countNew = {{ $myTicketStats['new'] ?? 0 }};
        const countProc = {{ $myTicketStats['processing'] ?? 0 }};
        const countPending = {{ $myTicketStats['pending'] ?? 0 }};
        const countClose = {{ $myTicketStats['close'] ?? 0 }};

        new Chart(ctxNormal, {
            type: 'doughnut',
            data: {
                labels: ['New', 'In Processing', 'Pending', 'Closed'],
                datasets: [{
                    data: [countNew, countProc, countPending, countClose],
                    backgroundColor: ['#10b981', '#3b82f6', '#dfaa41', '#e82a2a'],
                    borderWidth: 3,
                    borderColor: '#ffffff',
                    hoverOffset: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '75%',
                plugins: {
                    legend: { display: false }
                }
            }
        });

    });
</script>
@endsection