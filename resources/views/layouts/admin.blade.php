<!DOCTYPE html>
<html lang="ms">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'NMUC Admin Workspace')</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

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

    <!-- Custom Scrollbar Styling -->
    <style>
        .custom-scrollbar::-webkit-scrollbar {
            width: 6px;
        }
        .custom-scrollbar::-webkit-scrollbar-track {
            background: transparent;
        }
        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 9999px;
        }
        .custom-scrollbar::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }
    </style>
</head>
<body x-data="{ sidebarOpen: true }" class="bg-[#faf6f0] font-sans text-slate-800 h-screen flex flex-col overflow-hidden">

    <!-- TOP HEADER BAR (Static / Fixed) -->
    <header class="w-full bg-[#0d1527] text-white h-16 px-6 flex items-center justify-between sticky top-0 z-50 shrink-0 shadow-md">
        <div class="flex items-center gap-4">
            <button @click="sidebarOpen = !sidebarOpen" class="text-white hover:text-orange-400 p-1 text-lg transition focus:outline-none cursor-pointer">
                <i class="fa-solid fa-bars"></i>
            </button>

            <!-- NMUC Logo -->
            <img src="{{ asset('img/nmuc_logo_2.png') }}" alt="Netherlands Maritime University College" class="h-9 w-auto shrink-0">

            <!-- Admin Badge -->
            <span class="bg-orange-600 text-white font-extrabold text-[11px] px-3 py-1 rounded-full uppercase tracking-wider ml-2 shadow-sm">
                ADMIN PANEL ({{ Auth::user()->department->department_code ?? Auth::user()->department->code ?? 'ALL DEPARTMENTS' }})
            </span>
        </div>

        <div class="flex items-center gap-5">
            @php
                $currentUserId = Auth::user()->user_id ?? Auth::id();
                $userNotifs = \App\Models\Notification::where('user_id', $currentUserId)
                    ->orderBy('created_at', 'desc')
                    ->take(10)
                    ->get();
                $unreadCount = \App\Models\Notification::where('user_id', $currentUserId)
                    ->where('is_read', 0)
                    ->count();
            @endphp

            <!-- Notification Bell Dropdown -->
            <div class="relative" x-data="{ notifOpen: false }" @click.away="notifOpen = false">
                <button type="button" @click="notifOpen = !notifOpen"
                        class="text-white/80 hover:text-white text-base transition relative p-1.5 rounded-xl hover:bg-white/10 focus:outline-none cursor-pointer"
                        title="Notifications">
                    <i class="fa-regular fa-bell text-lg"></i>
                    @if($unreadCount > 0)
                        <span class="absolute -top-0.5 -right-0.5 min-w-[16px] h-4 px-1 bg-orange-500 text-white text-[10px] font-bold rounded-full flex items-center justify-center shadow-xs">
                            {{ $unreadCount > 9 ? '9+' : $unreadCount }}
                        </span>
                    @endif
                </button>

                <!-- Dropdown Menu -->
                <div x-show="notifOpen"
                     x-transition
                     style="display: none;"
                     class="absolute right-0 mt-2.5 w-80 bg-white text-slate-800 rounded-2xl border border-slate-200 shadow-xl z-50 overflow-hidden">
                    <div class="px-4 py-3 border-b border-slate-100 flex items-center justify-between bg-slate-50">
                        <span class="text-xs font-bold text-slate-800">Notifications ({{ $unreadCount }})</span>
                        @if($unreadCount > 0)
                            <form action="{{ route('notifications.readAll') }}" method="POST">
                                @csrf
                                <button type="submit" class="text-[11px] font-semibold text-orange-600 hover:underline cursor-pointer">
                                    Mark all read
                                </button>
                            </form>
                        @endif
                    </div>

                    <div class="max-h-80 overflow-y-auto custom-scrollbar divide-y divide-slate-100">
                        @forelse($userNotifs as $notif)
                            <a href="{{ route('notifications.read', $notif->notification_id) }}"
                               class="block px-4 py-3 hover:bg-slate-50 transition {{ $notif->is_read == 0 ? 'bg-orange-50/50' : '' }}">
                                <div class="flex items-start justify-between gap-2">
                                    <p class="text-xs font-bold text-slate-800">{{ $notif->notification_title }}</p>
                                    @if($notif->is_read == 0)
                                        <span class="w-2 h-2 rounded-full bg-orange-500 shrink-0 mt-1"></span>
                                    @endif
                                </div>
                                <p class="text-[11px] text-slate-500 mt-0.5 line-clamp-2">{{ $notif->notification_message }}</p>
                                <span class="text-[10px] text-slate-400 mt-1 block">
                                    {{ \Carbon\Carbon::parse($notif->created_at)->diffForHumans() }}
                                </span>
                            </a>
                        @empty
                            <div class="p-6 text-center text-xs text-slate-400">
                                No notifications yet.
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>

            <!-- User Avatar Circle -->
            <div class="flex items-center gap-2 pl-2">
                <div class="w-9 h-9 rounded-full bg-orange-600 text-white font-bold flex items-center justify-center text-xs shadow-md">
                    {{ strtoupper(substr(Auth::user()->full_name ?? Auth::user()->name ?? 'SY', 0, 2)) }}
                </div>
            </div>
        </div>
        </div>
    </header>

    <!-- MAIN CONTAINER (SIDEBAR + CONTENT) -->
    <div class="flex flex-1 h-[calc(100vh-4rem)] w-full overflow-hidden relative">

        <!-- MOBILE BACKDROP -->
        <div x-show="sidebarOpen" 
             @click="sidebarOpen = false"
             x-transition:enter="transition-opacity ease-linear duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition-opacity ease-linear duration-300"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="fixed inset-0 bg-slate-950/60 z-40 md:hidden"></div>

        <!-- LEFT SIDEBAR (Static / Fixed Height dengan Scroll Sendiri jika menu panjang) -->
        <aside :class="{
                   'translate-x-0 w-64': sidebarOpen,
                   '-translate-x-full md:translate-x-0 md:w-16': !sidebarOpen
               }" 
               class="fixed md:static inset-y-0 left-0 top-16 md:top-auto h-full bg-[#0d1527] flex flex-col justify-between shrink-0 text-slate-100 transition-all duration-300 z-50 md:z-30 shadow-2xl md:shadow-none overflow-hidden"
               x-data="{ 
                    openTickets: {{ request()->routeIs('admin.tickets.*') || request()->routeIs('admin.messages.*') ? 'true' : 'false' }},
                    openInventory: {{ request()->routeIs('admin.inventory.*') ? 'true' : 'false' }}
                }">
            
            <div class="w-64 md:w-auto flex-1 flex flex-col overflow-y-auto custom-scrollbar">
                <!-- Sidebar Title Section -->
                <div class="p-5 overflow-hidden shrink-0" x-show="sidebarOpen">
                    <h1 class="font-bold text-white text-base leading-tight">NMUC <span class="text-orange-500">Helpdesk</span></h1>
                    <p class="text-xs text-slate-400 mt-0.5">Admin Workspace</p>
                </div>

                <!-- Navigation List -->
                <nav class="px-3 py-2 space-y-2 text-sm font-medium flex-1">

                    <!-- Dashboard Link -->
                    <a href="{{ route('admin.dashboard') }}" 
                       class="flex items-center gap-3 px-4 py-3 rounded-xl transition cursor-pointer
                              {{ request()->routeIs('admin.dashboard') ? 'bg-[#152037] text-white font-bold' : 'text-slate-300 hover:text-white hover:bg-[#152037]/60' }}">
                        <i class="fa-solid fa-chart-line text-orange-500"></i>
                        <span x-show="sidebarOpen" class="whitespace-nowrap">Dashboard</span>
                    </a>

                    <!-- Tickets Accordion -->
                    <div class="space-y-1">
                        <button type="button" @click="openTickets = !openTickets" 
                                class="w-full flex items-center justify-between px-4 py-3 rounded-xl bg-[#152037]/80 hover:bg-[#152037] text-slate-200 transition cursor-pointer">
                            <div class="flex items-center gap-3">
                                <i class="fa-solid fa-ticket text-orange-500"></i>
                                <span x-show="sidebarOpen" class="font-bold text-sm whitespace-nowrap">Tickets</span>
                            </div>
                            <i x-show="sidebarOpen" class="fa-solid text-xs transition-transform duration-200 text-slate-400" 
                               :class="openTickets ? 'fa-chevron-down rotate-180' : 'fa-chevron-down'"></i>
                        </button>

                        <div x-show="openTickets && sidebarOpen" x-transition class="pl-3 pr-1 space-y-1 pt-1">
                            <a href="{{ route('admin.tickets.index') }}" 
                               class="flex items-center gap-3 px-4 py-2.5 rounded-xl text-xs font-semibold transition cursor-pointer
                                      {{ request()->routeIs('admin.tickets.index') ? 'bg-[#080d19] text-white' : 'text-slate-400 hover:text-white hover:bg-[#152037]/60' }}">
                                <i class="fa-solid fa-list-check text-[11px] text-orange-500"></i>
                                <span class="whitespace-nowrap">Ticket List</span>
                            </a>
                            <a href="{{ route('admin.tickets.create') }}" 
                               class="flex items-center gap-3 px-4 py-2.5 rounded-xl text-xs font-semibold transition cursor-pointer
                                      {{ request()->routeIs('admin.tickets.create') ? 'bg-[#080d19] text-white' : 'text-slate-400 hover:text-white hover:bg-[#152037]/60' }}">
                                <i class="fa-solid fa-plus text-[11px] text-orange-500"></i>
                                <span class="whitespace-nowrap">New Ticket</span>
                            </a>
                            <a href="{{ route('admin.messages.index') }}" 
                                class="flex items-center gap-3 px-4 py-2.5 rounded-xl text-xs font-semibold transition cursor-pointer
                                    {{ request()->routeIs('admin.messages.*') ? 'bg-[#080d19] text-white' : 'text-slate-400 hover:text-white hover:bg-[#152037]/60' }}">
                                <i class="fa-solid fa-comments text-[11px] text-orange-500"></i>
                                <span class="whitespace-nowrap">Message</span>
                            </a>
                        </div>
                    </div>

                    <!-- Inventory Accordion -->
                    <div class="space-y-1">
                        <button type="button" @click="openInventory = !openInventory" 
                                class="w-full flex items-center justify-between px-4 py-3 rounded-xl bg-[#152037]/80 hover:bg-[#152037] text-slate-200 transition cursor-pointer">
                            <div class="flex items-center gap-3">
                                <i class="fa-solid fa-boxes-stacked text-orange-500"></i>
                                <span x-show="sidebarOpen" class="font-bold text-sm whitespace-nowrap">Inventory</span>
                            </div>
                            <i x-show="sidebarOpen" class="fa-solid text-xs transition-transform duration-200 text-slate-400" 
                               :class="openInventory ? 'fa-chevron-down rotate-180' : 'fa-chevron-down'"></i>
                        </button>

                        <div x-show="openInventory && sidebarOpen" x-transition class="pl-3 pr-1 space-y-1 pt-1">
                            <a href="{{ route('admin.inventory.index') }}" 
                               class="flex items-center gap-3 px-4 py-2.5 rounded-xl text-xs font-semibold transition cursor-pointer
                                      {{ request()->routeIs('admin.inventory.index') ? 'bg-[#080d19] text-white font-bold' : 'text-slate-400 hover:text-white hover:bg-[#152037]/60' }}">
                                <i class="fa-solid fa-box text-[11px] text-orange-500"></i>
                                <span class="whitespace-nowrap">Inventory List</span>
                            </a>
                            <a href="{{ route('admin.inventory.create') }}" 
                               class="flex items-center gap-3 px-4 py-2.5 rounded-xl text-xs font-semibold transition cursor-pointer
                                      {{ request()->routeIs('admin.inventory.create') ? 'bg-[#080d19] text-white font-bold' : 'text-slate-400 hover:text-white hover:bg-[#152037]/60' }}">
                                <i class="fa-solid fa-plus text-[11px] text-orange-500"></i>
                                <span class="whitespace-nowrap">New Inventory</span>
                            </a>
                        </div>
                    </div>

                </nav>
            </div>

            <!-- Logout Button (Fixed Bottom of Sidebar) -->
            <div class="p-4 shrink-0 bg-[#0d1527] border-t border-slate-800/60">
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" 
                            class="w-full flex items-center gap-3 px-4 py-3 rounded-xl bg-red-950/40 hover:bg-red-700 text-red-400 hover:text-white border border-red-800/40 font-semibold text-xs transition cursor-pointer">
                        <i class="fa-solid fa-right-from-bracket text-sm"></i>
                        <span x-show="sidebarOpen" class="whitespace-nowrap">Log Out</span>
                    </button>
                </form>
            </div>

        </aside>

        <main class="flex-1 min-w-0 h-full overflow-y-auto custom-scrollbar p-6 md:p-8">
            <div class="max-w-7xl mx-auto space-y-6">
                @yield('content')
            </div>
        </main>

    </div>

</body>
</html>