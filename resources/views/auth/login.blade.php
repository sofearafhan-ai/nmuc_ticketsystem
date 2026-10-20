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
<body class="bg-slate-100 flex items-center justify-center min-h-screen p-4">

    <div class="w-full max-w-md bg-white rounded-2xl shadow-xl border border-orange-200 p-8 space-y-6">

        <!-- Header -->
        <div class="text-center space-y-2">
            <div class="inline-flex p-3 rounded-full bg-orange-100 text-orange-600 mb-2">
                <i class="fa-solid fa-headset text-3xl"></i>
            </div>
            <h1 class="text-2xl font-bold text-slate-800">NMUC Helpdesk</h1>
            <p class="text-xs text-slate-500">Please enter your details to log in</p>
        </div>

        @if (session('success'))
            <div class="p-3 bg-emerald-100 border border-emerald-300 text-emerald-800 rounded-lg text-xs flex items-center gap-2">
                <i class="fa-solid fa-circle-check"></i>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        <!-- Log In Form -->
        <form action="{{ route('login.post') }}" method="POST" class="space-y-4">
            @csrf

            <!-- Email -->
            <div class="space-y-1">
                <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Email</label>
                <div class="relative flex items-center">
                    <i class="fa-solid fa-envelope absolute left-3 top-3 text-slate-400 text-sm"></i>
                    <input type="email" name="email" value="{{ old('email') }}" required placeholder="name@nmuc.edu.my"
                        class="w-full pl-10 pr-4 py-2.5 bg-slate-50 border border-slate-300 rounded-lg text-sm text-slate-800 focus:ring-2 focus:ring-orange-500 focus:border-orange-500 outline-none transition">
                </div>
                @error('email')
                    <p class="text-red-500 text-xs mt-1 font-medium">{{ $message }}</p>
                @enderror
            </div>

                <!-- Password -->
                <div class="space-y-1">
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Password</label>
                    <div class="relative flex items-center">
                        <i class="fa-solid fa-lock absolute left-3 top-3 text-slate-400 text-sm"></i>
                        <input type="password" name="password"
                            class="w-full pl-10 pr-4 py-2.5 bg-slate-50 border border-slate-300 rounded-lg text-sm text-slate-800 focus:ring-2 focus:ring-orange-500 focus:border-orange-500 outline-none transition">
                    </div>
                </div>

                <!-- Send Button -->
                <button type="submit" class="w-full py-3 bg-orange-500 hover:bg-orange-600 text-white font-bold text-sm rounded-lg shadow-md hover:shadow-lg transition cursor-pointer flex items-center justify-center gap-2">
                    <i class="fa-solid fa-right-to-bracket"></i> Log In
                </button>
        </form>

        <div class="text-center pt-2 border-t border-slate-100">
            <p class="text-xs text-slate-400">&copy; {{ date('Y') }} NMUC Helpdesk System</p>
        </div>

    </div>

</body>
</html>