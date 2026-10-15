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
<body x-data="{ sidebarOpen: true }" class="bg-orange-50 font-sans text-slate-800 min-h-screen flex flex-col">

    <!-- ==================== TOP HEADER BAR ==================== -->
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
                    {{ strtoupper(substr(Auth::user()->name ?? 'P', 0, 2)) }}
                </div>
            </div>
        </div>
    </header>

    <!-- ==================== (SIDEBAR + MAIN CONTENT AREA) ==================== -->
    <div class="flex flex-1 min-h-[calc(100vh-3.5rem)]">

        <!-- LEFT SIDEBAR NAVIGATION -->
        <aside :class="sidebarOpen ? 'w-64' : 'w-16'" 
               class="bg-slate-900 border-r border-slate-800 flex flex-col justify-between shrink-0 sticky top-16 h-[calc(100vh-4rem)] text-slate-100 transition-all duration-300 z-40 overflow-visible" 
               x-data="{ openTicket: true, flyoutTicket: false, flyoutTimeout: null }">
            <div>
                <!-- Header Logo Text -->
                <div class="p-4 border-b border-slate-800 flex items-center gap-3 overflow-hidden" :class="sidebarOpen ? 'justify-start' : 'justify-center'">
                    <div x-show="sidebarOpen" x-transition.opacity class="whitespace-nowrap">
                        <h1 class="font-bold text-white tracking-wide text-base leading-none">NMUC <span class="text-orange-500">Helpdesk</span></h1>
                        <span class="text-[10px] text-slate-400 font-medium">Ticketing System</span>
                    </div>
                </div>

                <nav class="px-2 py-3 space-y-1 mt-1 text-sm font-medium">

                    <!-- MAIN MENU ITEM WITH STABLE HOVER FLYOUT -->
                    <div class="relative" 
                         @mouseenter="if(!sidebarOpen) { clearTimeout(flyoutTimeout); flyoutTicket = true; }" 
                         @mouseleave="if(!sidebarOpen) { flyoutTimeout = setTimeout(() => { flyoutTicket = false; }, 300); }">
                        
                        <!-- 1. BILA SIDEBAR DIBUKA (Expanded) -->
                        <div x-show="sidebarOpen" class="space-y-2 px-2">
                            <button type="button" @click="openTicket = !openTicket" 
                                    class="w-full flex items-center justify-between px-4 py-3 rounded-xl bg-slate-800/80 hover:bg-slate-800 border border-slate-700/60 text-slate-200 transition shadow-sm cursor-pointer">
                                <div class="flex items-center gap-3">
                                    <i class="fa-solid fa-ticket-simple text-orange-500"></i>
                                    <span class="font-semibold text-sm">Ticket Menu</span>
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
                                    <span>Ticket List</span>
                                </a>

                                <a href="{{ route('tickets.create') }}" 
                                   class="w-full flex items-center gap-3 px-4 py-2.5 rounded-xl text-xs font-semibold transition shadow-sm cursor-pointer block
                                          {{ request()->routeIs('tickets.create') 
                                              ? 'bg-slate-950 text-white border border-slate-800' 
                                              : 'text-slate-400 hover:text-white hover:bg-slate-800/60' }}">
                                    <i class="fa-solid fa-plus text-[11px] {{ request()->routeIs('tickets.create') ? 'text-orange-500' : 'text-slate-400' }}"></i>
                                    <span>New Ticket</span>
                                </a>
                            </div>
                        </div>

                        <!-- 2. BILA SIDEBAR COLLAPSED (Ikon Sahaja) -->
                        <div x-show="!sidebarOpen" class="flex justify-center py-1">
                            <button type="button" class="p-3 rounded-xl hover:bg-slate-800 text-slate-300 hover:text-white transition flex items-center justify-center cursor-pointer
                                           {{ request()->routeIs('tickets.*') ? 'bg-slate-800 text-orange-500' : '' }}">
                                <i class="fa-solid fa-ticket-simple text-base text-orange-500"></i>
                            </button>
                        </div>

                        <!-- 3. FLYOUT MENU BILA HOVER -->
                        <div x-show="!sidebarOpen && flyoutTicket" 
                             @mouseenter="clearTimeout(flyoutTimeout); flyoutTicket = true"
                             @mouseleave="flyoutTimeout = setTimeout(() => { flyoutTicket = false; }, 300)"
                             x-transition:enter="transition ease-out duration-150"
                             x-transition:enter-start="opacity-0 translate-x-1"
                             x-transition:enter-end="opacity-100 translate-x-0"
                             class="absolute left-14 -top-1 w-52 pl-3 z-50">
                            
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

                </nav>
            </div>
        </aside>

        <!-- MAIN CONTENT DYNAMIC AREA -->
        <main class="flex-1 p-6 overflow-y-auto">
            <div class="max-w-7xl mx-auto space-y-6">
                @yield('content')
            </div>
        </main>

    </div>

</body>
</html>