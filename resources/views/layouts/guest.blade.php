<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Diário de Obras') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <!-- PWA -->
        <link rel="manifest" href="{{ asset('manifest.json') }}">
        <meta name="mobile-web-app-capable" content="yes">
        <meta name="apple-mobile-web-app-capable" content="yes">
        <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
        <meta name="apple-mobile-web-app-title" content="Construtec">
        <meta name="theme-color" content="#f59e0b">
        <link rel="apple-touch-icon" href="{{ asset('apple-touch-icon.png') }}">
        <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('favicon-32x32.png') }}">
        <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('favicon-16x16.png') }}">
        <link rel="shortcut icon" href="{{ asset('favicon.ico') }}">

        <script>
            if ('serviceWorker' in navigator) {
                window.addEventListener('load', () => {
                    navigator.serviceWorker.register("{{ asset('sw.js') }}").then(registration => {
                        console.log('Service Worker registrado com escopo:', registration.scope);
                    }).catch(error => {
                        console.error('Falha ao registrar o Service Worker:', error);
                    });
                });
            }
        </script>
    </head>
    <body class="font-sans text-slate-200 antialiased bg-slate-900 bg-[url('https://www.transparenttextures.com/patterns/stardust.png')] relative">
        <div class="absolute inset-0 bg-gradient-to-br from-slate-900 via-slate-900/95 to-slate-800 -z-10"></div>
        <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0">
            <div>
                <a href="{{ url('/') }}" class="flex flex-col items-center gap-3 transition-transform hover:scale-105 duration-300">
                    <div class="w-20 h-20 bg-amber-500 rounded-2xl flex items-center justify-center shadow-lg shadow-amber-500/30 border border-amber-400">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 text-slate-900" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                        </svg>
                    </div>
                    <div class="text-center">
                        <h1 class="text-3xl font-bold text-white tracking-tight">Construtec Obras</h1>
                        <p class="text-amber-500 text-sm font-medium mt-1 tracking-widest uppercase">Sistema de Gestão</p>
                    </div>
                </a>
            </div>

            <div class="w-full sm:max-w-md mt-10 px-8 py-8 bg-white/10 backdrop-blur-xl border border-white/10 shadow-2xl overflow-hidden sm:rounded-2xl">
                {{ $slot }}
            </div>
            
            <p class="mt-8 text-sm text-slate-500">&copy; {{ date('Y') }} Construtec Obras. Todos os direitos reservados.</p>
        </div>
    </body>
</html>
