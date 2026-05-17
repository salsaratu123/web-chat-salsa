<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>SalsaChat</title>
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased bg-gray-900 text-gray-100 min-h-screen flex flex-col items-center justify-center relative overflow-hidden">
    
    <!-- Background Accents -->
    <div class="absolute top-[-10%] left-[-10%] w-96 h-96 bg-red-600 rounded-full mix-blend-multiply filter blur-3xl opacity-20 animate-blob"></div>
    <div class="absolute top-[20%] right-[-10%] w-96 h-96 bg-orange-500 rounded-full mix-blend-multiply filter blur-3xl opacity-20 animate-blob animation-delay-2000"></div>
    <div class="absolute bottom-[-20%] left-[20%] w-96 h-96 bg-yellow-500 rounded-full mix-blend-multiply filter blur-3xl opacity-20 animate-blob animation-delay-4000"></div>

    <div class="relative z-10 text-center px-4">
        <h1 class="text-6xl md:text-8xl font-extrabold mb-6 tracking-tight">
            <span class="bg-clip-text text-transparent bg-gradient-to-r from-red-500 via-orange-500 to-yellow-500">
                SalsaChat
            </span>
        </h1>
        <p class="text-xl md:text-2xl text-gray-300 mb-12 max-w-2xl mx-auto font-light">
            Platform komunikasi real-time yang simpel, cepat, dan aman. Tanpa ribet tambah nomor, langsung terhubung dengan semua temanmu.
        </p>

        @if (Route::has('login'))
            <div class="flex flex-col sm:flex-row gap-4 justify-center items-center">
                @auth
                    <a href="{{ url('/dashboard') }}" class="px-8 py-4 bg-gradient-to-r from-red-600 to-orange-500 hover:from-red-500 hover:to-orange-400 text-white rounded-full font-bold text-lg shadow-lg shadow-red-900/30 transition-all transform hover:scale-105">
                        Lanjut ke Chat
                    </a>
                @else
                    <a href="{{ route('login') }}" class="px-8 py-4 bg-gray-800 hover:bg-gray-700 text-white border border-gray-700 rounded-full font-bold text-lg shadow-lg transition-all transform hover:scale-105">
                        Masuk
                    </a>

                    @if (Route::has('register'))
                        <a href="{{ route('register') }}" class="px-8 py-4 bg-gradient-to-r from-red-600 to-orange-500 hover:from-red-500 hover:to-orange-400 text-white rounded-full font-bold text-lg shadow-lg shadow-red-900/30 transition-all transform hover:scale-105">
                            Daftar Sekarang
                        </a>
                    @endif
                @endauth
            </div>
        @endif
    </div>

    <div class="absolute bottom-6 text-gray-500 text-sm">
        &copy; {{ date('Y') }} SalsaChat. All rights reserved.
    </div>
</body>
</html>
