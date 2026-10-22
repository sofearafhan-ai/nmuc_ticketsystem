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
        
        <!-- Ticket Status Chart (Assigned To Admin Only) -->
        <div class="bg-white p-5 rounded-xl shadow-md border border-orange-200 space-y-4">
            <div class="flex justify-between items-center border-b pb-3">
                <h3 class="font-bold text-slate-800 text-sm flex items-center gap-2">
                    <i class="fa-solid fa-chart-pie text-orange-500"></i> Assigned To Me
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
                    label: 'Bilangan Aset',
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