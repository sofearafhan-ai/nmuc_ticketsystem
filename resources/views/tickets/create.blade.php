<!DOCTYPE html>
<html lang="ms">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hantar Aduan Baru</title>
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 font-sans">

    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        
        <div class="mb-6 flex justify-between items-center">
            <h1 class="text-2xl font-bold text-gray-800">Hantar Aduan Baru</h1>
            <a href="{{ route('tickets.index') }}" class="text-sm text-indigo-600 hover:text-indigo-800">
                &larr; Kembali ke Senarai
            </a>
        </div>

        <div class="bg-white p-6 rounded-lg shadow-sm">
            
            {{-- Papar Ralat Validasi --}}
            @if ($errors->any())
                <div class="mb-6 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg">
                    <ul class="list-disc pl-5">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('tickets.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <!-- Tajuk Aduan -->
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tajuk Aduan / Masalah</label>
                    <input type="text" name="title" required value="{{ old('title') }}" placeholder="Cth: Komputer tidak boleh dihidupkan"
                        class="w-full rounded-md border-gray-300 border p-2 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                </div>

                <!-- Jenis & Kategori (Dinamik daripada Database) -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Jenis Aduan (Type)</label>
                        <select name="type" required class="w-full rounded-md border-gray-300 border p-2 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="">-- Pilih Jenis --</option>
                            @foreach ($types as $type)
                                <option value="{{ $type }}" {{ old('type') == $type ? 'selected' : '' }}>
                                    {{ $type }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Kategori</label>
                        <select name="category" required class="w-full rounded-md border-gray-300 border p-2 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="">-- Pilih Kategori --</option>
                            @foreach ($categories as $category)
                                <option value="{{ $category }}" {{ old('category') == $category ? 'selected' : '' }}>
                                    {{ $category }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <!-- Keutamaan & Lokasi (Dinamik daripada Database) -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Keutamaan (Priority)</label>
                        <select name="priority" required class="w-full rounded-md border-gray-300 border p-2 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="">-- Pilih Keutamaan --</option>
                            @foreach ($priorities as $priority)
                                <option value="{{ $priority }}" {{ old('priority', 'Medium') == $priority ? 'selected' : '' }}>
                                    {{ $priority }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Lokasi Kejadian</label>
                        <input type="text" name="location" placeholder="Cth: Makmal 2, Blok B" required value="{{ old('location') }}"
                            class="w-full rounded-md border-gray-300 border p-2 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                    </div>
                </div>

                <!-- Penerangan Aduan -->
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Penerangan Lanjut (Description)</label>
                    <textarea name="description" rows="4" required placeholder="Jelaskan butiran kerosakan..." class="w-full rounded-md border-gray-300 border p-2 text-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('description') }}</textarea>
                </div>

                <!-- Muat Naik Gambar -->
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Gambar / Bukti (Pilihan)</label>
                    <input type="file" name="image" accept="image/*" class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                </div>

                <!-- Butang -->
                <div class="flex justify-end gap-3">
                    <a href="{{ route('tickets.index') }}" class="px-4 py-2 border border-gray-300 text-gray-700 rounded-md hover:bg-gray-50 text-sm font-medium">
                        Batal
                    </a>
                    <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 text-sm font-medium">
                        Hantar Tiket
                    </button>
                </div>
            </form>

        </div>
    </div>

</body>
</html>