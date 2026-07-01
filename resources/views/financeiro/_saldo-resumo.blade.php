{{-- Resumo mínimo na tela principal --}}
<div class="flex items-center justify-between gap-3 px-1 py-1">
    <div class="min-w-0">
        <p class="text-[9px] font-bold text-slate-600 uppercase tracking-widest mb-0.5">Saldo operacional</p>
        <p class="text-base font-black {{ $kpis['saldo_operacional'] >= 0 ? 'text-emerald-400' : 'text-rose-400' }} truncate">
            R$ {{ number_format($kpis['saldo_operacional'], 2, ',', '.') }}
        </p>
    </div>
    <button
        type="button"
        @click="showKpisModal = true"
        class="shrink-0 flex flex-col items-center gap-1 group active:scale-95 transition-transform"
        aria-label="Ver métricas financeiras"
    >
        <span class="w-10 h-10 rounded-2xl bg-white/[0.03] border border-white/[0.06] flex items-center justify-center text-slate-600 group-hover:text-slate-400 group-hover:border-white/10 transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
        </span>
        <span class="text-[8px] font-bold text-slate-600 uppercase tracking-widest group-hover:text-slate-500">Métricas</span>
    </button>
</div>
