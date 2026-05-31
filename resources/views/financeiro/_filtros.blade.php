@php
    $queryParams = fn (array $overrides = []) => array_filter(
        array_merge($filters, $overrides),
        fn ($v) => $v !== null && $v !== '' && $v !== 'todos'
    );
@endphp

<div x-data="{ filtrosAbertos: {{ $kpis['filtro_ativo'] ? 'true' : 'false' }} }" class="space-y-3">
    <button
        type="button"
        @click="filtrosAbertos = !filtrosAbertos"
        class="w-full flex items-center justify-between gap-3 bg-white/5 border border-white/10 rounded-2xl px-4 py-3.5 active:scale-[0.99] transition-all"
    >
        <div class="flex items-center gap-3 min-w-0">
            <span class="w-9 h-9 shrink-0 rounded-xl bg-white/5 flex items-center justify-center text-slate-400">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path></svg>
            </span>
            <div class="text-left min-w-0">
                <p class="text-[10px] font-black text-white uppercase tracking-widest">Filtros</p>
                <p class="text-[9px] text-slate-500 truncate">
                    @if($kpis['filtro_ativo'])
                        Filtros ativos · toque para ajustar
                    @else
                        Período, tipo, categoria e busca
                    @endif
                </p>
            </div>
        </div>
        <svg class="w-4 h-4 text-slate-500 shrink-0 transition-transform" :class="filtrosAbertos && 'rotate-180'" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
    </button>

    <div x-show="filtrosAbertos" x-transition class="space-y-4" style="display: none;">
        <!-- Atalhos de período -->
        <div class="flex gap-2 overflow-x-auto pb-1 scrollbar-hide">
            @foreach([
                'mes_atual' => 'Este mês',
                'mes_anterior' => 'Mês passado',
                '30_dias' => '30 dias',
                'ano_atual' => 'Este ano',
            ] as $key => $label)
                <a
                    href="{{ route('financeiro.index', $queryParams(['periodo' => $key, 'data_inicio' => null, 'data_fim' => null])) }}"
                    class="shrink-0 px-3.5 py-2 rounded-xl border text-[10px] font-black uppercase tracking-widest transition-all active:scale-95 {{ ($filters['periodo'] ?? null) === $key ? 'bg-white/10 text-white border-white/20' : 'bg-transparent text-slate-500 border-white/5 hover:border-white/10' }}"
                >
                    {{ $label }}
                </a>
            @endforeach
        </div>

        <form method="GET" action="{{ route('financeiro.index') }}" class="bg-white/5 border border-white/10 rounded-2xl p-4 space-y-4">
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label for="data_inicio" class="text-[9px] font-bold text-slate-600 uppercase tracking-widest mb-1 block">De</label>
                    <input type="date" id="data_inicio" name="data_inicio" value="{{ $filters['data_inicio'] }}" class="w-full bg-slate-800/80 border-white/10 rounded-xl text-white text-sm py-3 px-3">
                </div>
                <div>
                    <label for="data_fim" class="text-[9px] font-bold text-slate-600 uppercase tracking-widest mb-1 block">Até</label>
                    <input type="date" id="data_fim" name="data_fim" value="{{ $filters['data_fim'] }}" class="w-full bg-slate-800/80 border-white/10 rounded-xl text-white text-sm py-3 px-3">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label for="tipo" class="text-[9px] font-bold text-slate-600 uppercase tracking-widest mb-1 block">Tipo</label>
                    <select id="tipo" name="tipo" class="w-full bg-slate-800/80 border-white/10 rounded-xl text-white text-sm py-3 px-3">
                        @foreach(['todos' => 'Todos', 'recebida' => 'Recebidas', 'paga' => 'Pagas', 'pendente' => 'Pendentes'] as $val => $label)
                            <option value="{{ $val }}" @selected($filters['tipo'] === $val)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="categoria" class="text-[9px] font-bold text-slate-600 uppercase tracking-widest mb-1 block">Categoria</label>
                    <select id="categoria" name="categoria" class="w-full bg-slate-800/80 border-white/10 rounded-xl text-white text-sm py-3 px-3">
                        <option value="">Todas</option>
                        @foreach($categorias as $val => $label)
                            <option value="{{ $val }}" @selected($filters['categoria'] === $val)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div>
                <label for="busca" class="text-[9px] font-bold text-slate-600 uppercase tracking-widest mb-1 block">Buscar</label>
                <input
                    type="search"
                    id="busca"
                    name="busca"
                    value="{{ $filters['busca'] }}"
                    placeholder="Descrição, fornecedor..."
                    class="w-full bg-slate-800/80 border-white/10 rounded-xl text-white text-sm py-3 px-4 placeholder:text-slate-600"
                >
            </div>

            <div class="flex gap-2">
                <button type="submit" class="flex-1 py-3 bg-white/10 hover:bg-white/15 text-white rounded-xl text-[10px] font-black uppercase tracking-widest transition-all active:scale-95">
                    Aplicar
                </button>
                @if($kpis['filtro_ativo'])
                    <a href="{{ route('financeiro.index') }}" class="px-4 py-3 bg-white/5 hover:bg-white/10 text-slate-400 rounded-xl text-[10px] font-black uppercase tracking-widest transition-all flex items-center">
                        Limpar
                    </a>
                @endif
            </div>
        </form>

        @if($kpis['filtro_ativo'])
            <div class="flex flex-wrap gap-2">
                @if($filters['periodo'])
                    <span class="px-2.5 py-1 rounded-lg bg-white/5 border border-white/10 text-[9px] font-bold text-slate-400 uppercase tracking-widest">
                        {{ match($filters['periodo']) { 'mes_atual' => 'Este mês', 'mes_anterior' => 'Mês passado', '30_dias' => 'Últimos 30 dias', 'ano_atual' => 'Este ano', default => $filters['periodo'] } }}
                    </span>
                @endif
                @if($filters['data_inicio'] || $filters['data_fim'])
                    <span class="px-2.5 py-1 rounded-lg bg-white/5 border border-white/10 text-[9px] font-bold text-slate-400 uppercase tracking-widest">
                        @if($filters['data_inicio'] && $filters['data_fim'])
                            {{ \Carbon\Carbon::parse($filters['data_inicio'])->format('d/m/Y') }} – {{ \Carbon\Carbon::parse($filters['data_fim'])->format('d/m/Y') }}
                        @elseif($filters['data_inicio'])
                            A partir de {{ \Carbon\Carbon::parse($filters['data_inicio'])->format('d/m/Y') }}
                        @else
                            Até {{ \Carbon\Carbon::parse($filters['data_fim'])->format('d/m/Y') }}
                        @endif
                    </span>
                @endif
                @if($filters['tipo'] !== 'todos')
                    <span class="px-2.5 py-1 rounded-lg bg-white/5 border border-white/10 text-[9px] font-bold text-slate-400 uppercase tracking-widest">{{ ucfirst($filters['tipo']) }}</span>
                @endif
                @if($filters['categoria'])
                    <span class="px-2.5 py-1 rounded-lg bg-white/5 border border-white/10 text-[9px] font-bold text-slate-400 uppercase tracking-widest">{{ $categorias[$filters['categoria']] ?? $filters['categoria'] }}</span>
                @endif
                @if($filters['busca'])
                    <span class="px-2.5 py-1 rounded-lg bg-white/5 border border-white/10 text-[9px] font-bold text-slate-400 uppercase tracking-widest">"{{ $filters['busca'] }}"</span>
                @endif
            </div>
        @endif
    </div>
</div>
