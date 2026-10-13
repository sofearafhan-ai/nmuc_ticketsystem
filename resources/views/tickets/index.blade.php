<!DOCTYPE html>
<html lang="ms">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Senarai Tiket Aduan</title>
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 font-sans">

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        
        <!-- Header -->
        <div class="flex justify-between items-center mb-6 bg-white p-6 rounded-lg shadow-sm">
            <h1 class="text-2xl font-bold text-gray-800">Senarai Tiket Aduan</h1>
            <a href="{{ route('tickets.create') }}" class="px-4 py-2 bg-indigo-600 text-white font-medium rounded-md hover:bg-indigo-700 transition">
                + Aduan Baru
            </a>
        </div>

        <!-- Mesej Kejayaan -->
        @if (session('success'))
            <div class="mb-6 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg shadow-sm">
                {{ session('success') }}
            </div>
        @endif

        <!-- Jadual Tiket -->
        <div class="bg-white rounded-lg shadow-sm overflow-hidden">
            <div class="p-6">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="border-b bg-gray-50 text-gray-700 uppercase text-xs">
                            <th class="p-3">ID Tiket</th>
                            <th class="p-3">Tajuk</th>
                            <th class="p-3">Jenis / Kategori</th>
                            <th class="p-3">Keutamaan</th>
                            <th class="p-3">Status</th>
                            <th class="p-3">Tarikh</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse ($tickets as $ticket)
                            <tr class="hover:bg-gray-50">
                                <td class="p-3 font-semibold text-indigo-600">#{{ $ticket->ticket_id }}</td>
                                <td class="p-3 font-medium text-gray-900">{{ $ticket->title }}</td>
                                <td class="p-3"><span class="text-xs bg-gray-100 px-2 py-1 rounded text-gray-600">{{ $ticket->type }}</span> {{ $ticket->category }}</td>
                                <td class="p-3">
                                    <span class="px-2 py-1 text-xs font-bold rounded 
                                        @if($ticket->priority == 'Urgent') bg-red-100 text-red-800
                                        @elseif($ticket->priority == 'High') bg-orange-100 text-orange-800
                                        @elseif($ticket->priority == 'Medium') bg-yellow-100 text-yellow-800
                                        @else bg-gray-100 text-gray-800 @endif">
                                        {{ $ticket->priority }}
                                    </span>
                                </td>
                                <td class="p-3">
                                    <span class="px-2 py-1 text-xs font-medium rounded text-white
                                        @if($ticket->status == 'New') bg-blue-500
                                        @elseif($ticket->status == 'Processing') bg-amber-500
                                        @elseif($ticket->status == 'Pending') bg-purple-500
                                        @else bg-green-600 @endif">
                                        {{ $ticket->status }}
                                    </span>
                                </td>
                                <td class="p-3 text-sm text-gray-500">{{ $ticket->date }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="p-8 text-center text-gray-500">Tiada tiket aduan dijumpai. Klik "+ Aduan Baru" untuk bermula.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>

</body>
</html>