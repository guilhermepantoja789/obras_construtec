<x-app-layout>
    <div class="min-h-[80vh] flex flex-col items-center justify-center p-8 text-center">
        <div class="w-24 h-24 bg-amber-500/10 rounded-full flex items-center justify-center mb-6 animate-pulse">
            <svg class="w-12 h-12 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
            </svg>
        </div>
        <h2 class="text-3xl font-black text-white uppercase tracking-tight mb-2">Módulo em Produção</h2>
        <p class="text-slate-500 max-w-sm leading-relaxed mb-8">
            Estamos finalizando os detalhes para a geração automática de contratos e gestão de documentos. Em breve disponível!
        </p>
        <a href="{{ route('dashboard') }}" class="px-8 py-3 bg-white/5 hover:bg-white/10 text-white font-black rounded-2xl border border-white/10 uppercase tracking-widest text-xs transition-all">
            Voltar ao Início
        </a>
    </div>
</x-app-layout>
