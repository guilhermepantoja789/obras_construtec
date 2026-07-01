<div class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-3 px-2 mb-3">
    <div class="flex flex-wrap items-center gap-2 w-full lg:w-auto">
        <input type="search" x-model="searchQuery" placeholder="Buscar por nº ou descrição…"
            class="flex-1 min-w-[180px] bg-slate-900/50 border-white/10 rounded-xl text-white text-xs py-2 px-3 focus:border-amber-500">
        <label class="flex items-center gap-2 px-3 py-2 bg-white/5 rounded-xl border border-white/10 cursor-pointer">
            <input type="checkbox" x-model="showOnlyEtapas" class="rounded bg-slate-900 border-white/10 text-amber-500 focus:ring-amber-500">
            <span class="text-[9px] font-black text-slate-400 uppercase tracking-widest whitespace-nowrap">Só etapas</span>
        </label>
        <label class="flex items-center gap-2 px-3 py-2 bg-white/5 rounded-xl border border-white/10 cursor-pointer">
            <input type="checkbox" x-model="groupByEtapa" class="rounded bg-slate-900 border-white/10 text-amber-500 focus:ring-amber-500">
            <span class="text-[9px] font-black text-slate-400 uppercase tracking-widest whitespace-nowrap">Agrupar</span>
        </label>
    </div>
    <p class="text-[9px] text-slate-600 uppercase font-bold">
        <span x-text="filteredItems.length"></span> visíveis de <span x-text="items.length"></span>
    </p>
</div>
