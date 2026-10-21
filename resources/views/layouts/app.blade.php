<!DOCTYPE html>
<html lang="ms">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'NMUC - Helpdesk')</title>

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
<body x-data="{ sidebarOpen: true }" class="bg-orange-50 font-sans text-slate-800 min-h-screen flex flex-col overflow-x-hidden">

    <!-- TOP HEADER BAR -->
    <header class="w-full bg-slate-900 border-b border-slate-800 text-white h-16 px-6 flex items-center justify-between sticky top-0 z-50 shrink-0 shadow-sm">
        <div class="flex items-center gap-4">
            <button @click="sidebarOpen = !sidebarOpen" class="text-white hover:text-slate-100 p-1 text-lg transition focus:outline-none cursor-pointer">
                <i class="fa-solid fa-bars"></i>
            </button>
            <img src="{{ asset('img/nmuc_logo_2.png') }}" alt="NMUC Logo" class="h-9 w-auto shadow-sm rounded-sm shrink-0">
        </div>

        <div class="flex items-center gap-5">
            <button class="text-white/80 hover:text-white text-base transition relative" title="Notifikasi">
                <i class="fa-regular fa-bell"></i>
                <span class="absolute -top-1 -right-1 w-2 h-2 bg-amber-400 rounded-full"></span>
            </button>

            <div class="flex items-center gap-2 pl-2 border-l border-white/20">
                <div class="w-8 h-8 rounded-full bg-white text-slate-900 font-bold flex items-center justify-center text-xs shadow-sm ring-2 ring-white/40">
                    {{ strtoupper(substr(Auth::user()->full_name ?? Auth::user()->name ?? 'P', 0, 2)) }}
                </div>
            </div>
        </div>
    </header>

    <!-- SIDEBAR + MAIN CONTENT AREA -->
    <div class="flex flex-1 min-h-[calc(100vh-4rem)] w-full relative items-stretch">

        <!-- BACKDROP OVERLAY MOBILE -->
        <div x-show="sidebarOpen" 
             @click="sidebarOpen = false"
             x-transition:enter="transition-opacity ease-linear duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition-opacity ease-linear duration-300"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="fixed inset-0 bg-slate-950/60 z-40 md:hidden"></div>

        <!-- LEFT SIDEBAR NAVIGATION -->
        <aside :class="{
                   'translate-x-0 w-64': sidebarOpen,
                   '-translate-x-full md:translate-x-0 md:w-16': !sidebarOpen
               }" 
               class="fixed md:static inset-y-0 left-0 top-16 md:top-auto self-stretch bg-slate-900 border-r border-slate-800 flex flex-col justify-between shrink-0 text-slate-100 transition-all duration-300 z-50 md:z-30 overflow-hidden shadow-2xl md:shadow-none" 
               x-data="{ openTicket: true, flyoutTicket: false, flyoutTimeout: null }">
            
            <!-- UPPER SIDEBAR CONTENT -->
            <div class="w-64 md:w-auto">
                <div class="p-4 border-b border-slate-800 flex items-center justify-between overflow-hidden h-16 shrink-0">
                    <div x-show="sidebarOpen" x-transition.opacity class="whitespace-nowrap">
                        <h1 class="font-bold text-white tracking-wide text-base leading-none">NMUC <span class="text-orange-500">Helpdesk</span></h1>
                        <span class="text-[10px] text-slate-400 font-medium">Ticketing System</span>
                    </div>

                    <button x-show="sidebarOpen" @click="sidebarOpen = false" class="md:hidden text-slate-400 hover:text-white p-1 rounded-lg hover:bg-slate-800 transition">
                        <i class="fa-solid fa-xmark text-lg"></i>
                    </button>
                </div>

                <nav class="px-2 py-3 space-y-2 text-sm font-medium">

                    <!-- MAIN MENU ITEM WITH STABLE HOVER FLYOUT -->
                    <div class="relative" 
                         @mouseenter="if(!sidebarOpen) { clearTimeout(flyoutTimeout); flyoutTicket = true; }" 
                         @mouseleave="if(!sidebarOpen) { flyoutTimeout = setTimeout(() => { flyoutTicket = false; }, 300); }">
                        
                        <!-- 1. OPEN SIDEBAR (Expanded) -->
                        <div x-show="sidebarOpen" x-transition.opacity class="space-y-2 px-2">
                            <button type="button" @click="openTicket = !openTicket" 
                                    class="w-full flex items-center justify-between px-4 py-3 rounded-xl bg-slate-800/80 hover:bg-slate-800 border border-slate-700/60 text-slate-200 transition shadow-sm cursor-pointer">
                                <div class="flex items-center gap-3">
                                    <i class="fa-solid fa-ticket-simple text-orange-500"></i>
                                    <span class="font-semibold text-sm whitespace-nowrap">Ticket Menu</span>
                                </div>
                                <i class="fa-solid text-xs transition-transform duration-200 text-slate-400" 
                                   :class="openTicket ? 'fa-chevron-down rotate-180' : 'fa-chevron-down'"></i>
                            </button>

                            <!-- SUB-MENU ITEM CARDS -->
                            <div x-show="openTicket" class="pl-2 pr-1 space-y-1 mt-1.5">
                                <a href="{{ route('tickets.index') }}" 
                                   class="w-full flex items-center gap-3 px-4 py-2.5 rounded-xl text-xs font-semibold transition shadow-sm cursor-pointer block
                                          {{ request()->routeIs('tickets.index') 
                                              ? 'bg-slate-950 text-white border border-slate-800' 
                                              : 'text-slate-400 hover:text-white hover:bg-slate-800/60' }}">
                                    <i class="fa-solid fa-list-check text-[11px] {{ request()->routeIs('tickets.index') ? 'text-orange-500' : 'text-slate-400' }}"></i>
                                    <span class="whitespace-nowrap">Ticket List</span>
                                </a>

                                <a href="{{ route('tickets.create') }}" 
                                   class="w-full flex items-center gap-3 px-4 py-2.5 rounded-xl text-xs font-semibold transition shadow-sm cursor-pointer block
                                          {{ request()->routeIs('tickets.create') 
                                              ? 'bg-slate-950 text-white border border-slate-800' 
                                              : 'text-slate-400 hover:text-white hover:bg-slate-800/60' }}">
                                    <i class="fa-solid fa-plus text-[11px] {{ request()->routeIs('tickets.create') ? 'text-orange-500' : 'text-slate-400' }}"></i>
                                    <span class="whitespace-nowrap">New Ticket</span>
                                </a>
                            </div>
                        </div>

                        <!-- 2. COLLAPSED SIDEBAR (Desktop) -->
                        <div x-show="!sidebarOpen" class="hidden md:flex justify-center py-1">
                            <button type="button" class="p-3 rounded-xl hover:bg-slate-800 text-slate-300 hover:text-white transition flex items-center justify-center cursor-pointer
                                           {{ request()->routeIs('tickets.*') ? 'bg-slate-800 text-orange-500' : '' }}">
                                <i class="fa-solid fa-ticket-simple text-base text-orange-500"></i>
                            </button>
                        </div>

                        <!-- 3. FLYOUT MENU ON HOVER (Desktop) -->
                        <div x-show="!sidebarOpen && flyoutTicket" 
                             @mouseenter="clearTimeout(flyoutTimeout); flyoutTicket = true"
                             @mouseleave="flyoutTimeout = setTimeout(() => { flyoutTicket = false; }, 300)"
                             x-transition:enter="transition ease-out duration-150"
                             x-transition:enter-start="opacity-0 translate-x-1"
                             x-transition:enter-end="opacity-100 translate-x-0"
                             class="hidden md:block absolute left-14 -top-1 w-52 pl-3 z-50">
                            
                            <div class="bg-slate-900 border border-slate-700 rounded-xl overflow-hidden shadow-2xl">
                                <div class="px-4 py-3 bg-slate-800 border-b border-slate-700/80 font-bold text-xs text-white flex items-center gap-2">
                                    <i class="fa-solid fa-ticket-simple text-orange-500"></i>
                                    <span>Ticket Menu</span>
                                </div>

                                <div class="py-1.5 bg-slate-900 space-y-0.5">
                                    <a href="{{ route('tickets.index') }}" 
                                       class="flex items-center gap-3 px-4 py-2.5 text-xs text-slate-300 hover:text-white hover:bg-slate-800 transition cursor-pointer font-medium block">
                                        <i class="fa-solid fa-angles-right text-[10px] text-orange-500"></i>
                                        <span>Ticket List</span>
                                    </a>
                                    <a href="{{ route('tickets.create') }}" 
                                       class="flex items-center gap-3 px-4 py-2.5 text-xs text-slate-300 hover:text-white hover:bg-slate-800 transition cursor-pointer font-medium block">
                                        <i class="fa-solid fa-angles-right text-[10px] text-orange-500"></i>
                                        <span>New Ticket</span>
                                    </a>
                                </div>
                            </div>
                        </div>

                    </div>

                    <!-- ADMIN SECTION MENU -->
                    @if(Auth::check() && in_array(Auth::user()->role, ['Admin', 'Super Admin']))
                        <div class="pt-3 mt-3 border-t border-slate-800/80 px-2 space-y-1">
                            <span x-show="sidebarOpen" class="block text-[10px] font-extrabold text-orange-500 uppercase tracking-wider mb-2">
                                Admin Area ({{ Auth::user()->department ?? 'IT' }})
                            </span>

                            <a href="{{ route('admin.dashboard') }}" title="Dashboard"
                               class="w-full flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-xs font-semibold transition shadow-sm cursor-pointer block
                                      {{ request()->routeIs('admin.dashboard') 
                                          ? 'bg-slate-950 text-white border border-slate-800' 
                                          : 'text-slate-400 hover:text-white hover:bg-slate-800/60' }}">
                                <i class="fa-solid fa-chart-line text-xs {{ request()->routeIs('admin.dashboard') ? 'text-orange-500' : 'text-slate-400' }}"></i>
                                <span x-show="sidebarOpen" class="whitespace-nowrap">Dashboard</span>
                            </a>

                            <a href="{{ route('admin.tickets.index') }}" title="Admin Tickets"
                               class="w-full flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-xs font-semibold transition shadow-sm cursor-pointer block
                                      {{ request()->routeIs('admin.tickets.*') 
                                          ? 'bg-slate-950 text-white border border-slate-800' 
                                          : 'text-slate-400 hover:text-white hover:bg-slate-800/60' }}">
                                <i class="fa-solid fa-list-check text-xs {{ request()->routeIs('admin.tickets.*') ? 'text-orange-500' : 'text-slate-400' }}"></i>
                                <span x-show="sidebarOpen" class="whitespace-nowrap">Admin Tickets</span>
                            </a>

                            <a href="{{ route('admin.inventory.index') }}" title="Department Inventory"
                               class="w-full flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-xs font-semibold transition shadow-sm cursor-pointer block
                                      {{ request()->routeIs('admin.inventory.*') 
                                          ? 'bg-slate-950 text-white border border-slate-800' 
                                          : 'text-slate-400 hover:text-white hover:bg-slate-800/60' }}">
                                <i class="fa-solid fa-boxes-stacked text-xs {{ request()->routeIs('admin.inventory.*') ? 'text-orange-500' : 'text-slate-400' }}"></i>
                                <span x-show="sidebarOpen" class="whitespace-nowrap">Department Inventory</span>
                            </a>
                        </div>
                    @endif

                </nav>
            </div>

            <!-- LOWER SIDEBAR CONTENT: BUTANG LOGOUT -->
            <div class="p-3 border-t border-slate-800 shrink-0">
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button x-show="sidebarOpen" type="submit" 
                            class="w-full flex items-center gap-3 px-4 py-3 rounded-xl bg-red-600/10 hover:bg-red-600 text-red-400 hover:text-white border border-red-500/20 font-semibold text-xs transition shadow-sm cursor-pointer">
                        <i class="fa-solid fa-right-from-bracket text-sm"></i>
                        <span class="whitespace-nowrap">Log Out</span>
                    </button>

                    <button x-show="!sidebarOpen" type="submit" title="Log Keluar"
                            class="hidden md:flex w-full justify-center p-3 rounded-xl bg-red-600/10 hover:bg-red-600 text-red-400 hover:text-white border border-red-500/20 transition cursor-pointer">
                        <i class="fa-solid fa-right-from-bracket text-sm"></i>
                    </button>
                </form>
            </div>

        </aside>

        <!-- MAIN CONTENT DYNAMIC AREA -->
        <main class="flex-1 min-w-0 p-6 overflow-y-auto">
            <div class="max-w-7xl mx-auto space-y-6">
                @yield('content')
            </div>
        </main>

    </div>

</body>
</html>