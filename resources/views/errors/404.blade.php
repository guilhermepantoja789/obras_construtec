<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Página não encontrada</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-slate-950 text-white flex items-center justify-center px-4">
    <div class="w-full max-w-md bg-white/5 border border-white/10 rounded-3xl p-8 text-center">
        <p class="text-xs font-black text-amber-500 uppercase tracking-widest mb-2">Erro 404</p>
        <h1 class="text-2xl font-black mb-3">Página não encontrada</h1>
        <p class="text-sm text-slate-300 mb-6">O endereço acessado não existe ou foi movido.</p>

        <a href="{{ route('dashboard') }}" class="inline-flex w-full items-center justify-center py-3 rounded-xl bg-amber-500 text-slate-900 font-black text-xs uppercase tracking-widest">
            Ir para o painel
        </a>
    </div>
</body>
</html>
