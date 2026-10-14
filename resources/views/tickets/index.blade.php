<!DOCTYPE html>
<html lang="ms">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NMUC - Helpdesk</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Poppins', 'sans-serif'],
                    },
                }
            }
        }
    </script>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="bg-orange-50 font-sans text-slate-800 min-h-screen flex">

        <aside class="w-64 bg-slate-900 border-r border-slate-800 flex flex-col justify-between shrink-0 min-h-screen sticky top-0 h-screen text-slate-100" x-data="{ openTicket: true }">
            <div>
                <!-- Header Logo -->
                <div class="p-5 border-b border-slate-800 flex items-center gap-3">
                    <span class="bg-orange-500 text-white p-2 rounded-lg text-sm shadow-sm">
                        <i class="fa-solid fa-ticket"></i>
                    </span>
                    <div>
                        <h1 class="font-bold text-white tracking-wide text-base leading-none">NMUC <span class="text-orange-500">Helpdesk</span></h1>
                        <span class="text-[10px] text-slate-400 font-medium">Ticketing System</span>
                    </div>
                </div>

                <nav class="px-4 py-2 space-y-1 mt-2 text-sm font-medium">

                    <!-- MAIN MENU: Ticket Header -->
                    <div class="space-y-2">
                        <button @click="openTicket = !openTicket" 
                                class="w-full flex items-center justify-between px-4 py-3 rounded-xl bg-slate-800/80 hover:bg-slate-800 border border-slate-700/60 text-slate-200 transition shadow-sm">
                            <div class="flex items-center gap-3">
                                <i class="fa-solid fa-ticket-simple text-orange-500"></i>
                                <span class="font-semibold text-sm">Ticket Menu</span>
                            </div>
                            <i class="fa-solid text-xs transition-transform duration-200 text-slate-400" 
                            :class="openTicket ? 'fa-chevron-down rotate-180' : 'fa-chevron-down'"></i>
                        </button>

                        <!-- SUB-MENU ITEM CARDS (Gaya Gambar Anda) -->
                        <div x-show="openTicket" x-collapse class="pl-6 pr-1 space-y-1 mt-1.5">
                    
                            <!-- Sub 1: Ticket List -->
                            <a href="{{ route('tickets.index') }}" 
                            class="flex items-center gap-3 px-4 py-2.5 rounded-xl text-xs font-semibold transition shadow-sm
                                    {{ request()->routeIs('tickets.index') 
                                        ? 'bg-slate-950 text-white border border-slate-800' 
                                        : 'text-slate-400 hover:text-white hover:bg-slate-800/60' }}">
                                <i class="fa-solid fa-list-check text-[11px] {{ request()->routeIs('tickets.index') ? 'text-orange-500' : 'text-slate-400' }}"></i>
                                <span>Ticket List</span>
                            </a>

                            <!-- Sub 2: New Ticket -->
                            <a href="{{ route('tickets.create') }}" 
                            class="flex items-center gap-3 px-4 py-2.5 rounded-xl text-xs font-semibold transition shadow-sm
                                    {{ request()->routeIs('tickets.create') 
                                        ? 'bg-slate-950 text-white border border-slate-800' 
                                        : 'text-slate-400 hover:text-white hover:bg-slate-800/60' }}">
                                <i class="fa-solid fa-plus text-[11px] {{ request()->routeIs('tickets.create') ? 'text-orange-500' : 'text-slate-400' }}"></i>
                                <span>New Ticket</span>
                            </a>
                        </div>
                    </div>

                </nav>
            </div>

            <!-- User Profile (Bawah Sidebar) -->
            <div class="p-4 border-t border-slate-800 bg-slate-900/50">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-full bg-orange-500 text-white font-bold flex items-center justify-center text-xs shrink-0 shadow-sm">
                        {{ strtoupper(substr(Auth::user()->name ?? 'P', 0, 2)) }}
                    </div>
                    
                    <div class="overflow-hidden flex-1">
                        <span class="block text-xs font-bold text-slate-100 truncate">
                            {{ Auth::user()->name ?? 'Pengguna NMUC' }}
                        </span>
                        <span class="block text-[10px] text-slate-400 truncate">
                            {{ Auth::user()->email ?? 'user@nmuc.edu.my' }}
                        </span>
                    </div>

                    @auth
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" title="Log Keluar" class="text-slate-400 hover:text-red-400 transition p-1">
                            <i class="fa-solid fa-right-from-bracket text-xs"></i>
                        </button>
                    </form>
                    @endauth
                </div>
            </div>
        </aside>

    <!-- ==================== MAIN CONTENT AREA ==================== -->
    <main class="flex-1 p-6 overflow-y-auto">
        
        <div class="max-w-7xl mx-auto space-y-6">
            
            <!-- Header Ringkasan & Butang -->
            <div class="flex justify-between items-center bg-orange-600 p-6 rounded-lg border border-orange-700 shadow-md text-white">
                <div>
                    <h1 class="text-2xl font-bold text-white">Ticket List</h1>
                    <p class="text-xs text-orange-100 mt-1">Menguruskan senarai masalah, insiden, dan permohonan servis anda.</p>
                </div>
                <a href="{{ route('tickets.create') }}" class="px-4 py-2.5 bg-slate-950 hover:bg-slate-900 text-white text-sm font-semibold rounded-lg transition flex items-center gap-2 shadow-md">
                    <i class="fa-solid fa-plus text-xs text-orange-400"></i> New Ticket
                </a>
            </div>

            <!-- Mesej Kejayaan -->
            @if (session('success'))
                <div class="bg-emerald-900/90 border-l-4 border-emerald-400 text-emerald-100 px-4 py-3 rounded-lg shadow-sm text-sm flex items-center gap-2">
                    <i class="fa-solid fa-circle-check text-emerald-300"></i>
                    <span>{{ session('success') }}</span>
                </div>
            @endif

            <!-- Jadual Tiket -->
            <div class="bg-white rounded-lg shadow-xl overflow-hidden border border-orange-200 text-slate-800">
                <div class="p-6">
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="border-b border-orange-200 bg-orange-100/60 text-orange-950 uppercase text-[11px] font-bold tracking-wider">
                                    <th class="p-3">Ticket ID</th>
                                    <th class="p-3">Title</th>
                                    <th class="p-3">Type</th>
                                    <th class="p-3">Category</th>
                                    <th class="p-3">Priority</th>
                                    <th class="p-3">Status</th>
                                    <th class="p-3">Date</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-200 text-sm">
                                @forelse ($tickets as $ticket)
                                    <tr class="hover:bg-orange-50/50 transition">
                                        <td class="p-3 font-semibold text-slate-950">#{{ $ticket->ticket_id }}</td>
                                        <td class="p-3 font-medium text-slate-900">{{ $ticket->title }}</td>
                                        <td class="p-3">
                                            <span class="text-xs bg-orange-100 text-orange-900 px-2 py-1 rounded font-medium border border-orange-200">
                                                {{ $ticket->type }}
                                            </span>
                                        </td>
                                        <td class="p-3 font-medium text-slate-700">{{ $ticket->category }}</td>
                                        <td class="p-3">
                                            <span class="px-2.5 py-1 text-xs font-bold rounded 
                                                @if($ticket->priority == 'Urgent') bg-red-100 text-red-800
                                                @elseif($ticket->priority == 'High') bg-orange-200 text-orange-900
                                                @elseif($ticket->priority == 'Medium') bg-yellow-100 text-yellow-800
                                                @else bg-slate-100 text-slate-800 @endif">
                                                {{ $ticket->priority }}
                                            </span>
                                        </td>
                                        <td class="p-3">
                                            <span class="px-2.5 py-1 text-xs font-medium rounded text-white inline-block
                                                @if($ticket->status == 'New') bg-blue-600
                                                @elseif($ticket->status == 'Processing') bg-amber-500
                                                @elseif($ticket->status == 'Pending') bg-purple-600
                                                @else bg-emerald-600 @endif">
                                                {{ $ticket->status }}
                                            </span>
                                        </td>
                                        <td class="p-3 text-xs text-slate-500 font-medium">{{ $ticket->date }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="p-8 text-center text-slate-500 font-medium">
                                            No complaint tickets found. Click "+ New Ticket" to get started.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>

    </main>

</body>
</html>