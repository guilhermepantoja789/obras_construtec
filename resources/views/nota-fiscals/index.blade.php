<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-4">
            <div class="p-2 bg-indigo-500/10 rounded-xl text-indigo-500 border border-indigo-500/20 shrink-0">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
            </div>
            <div>
                <p class="text-[10px] font-black text-indigo-500 uppercase tracking-[0.2em] mb-0.5">Gestão de Documentos</p>
                <h2 class="font-black text-xl text-white leading-tight uppercase tracking-tight">
                    Notas Fiscais
                </h2>
                <p class="text-[10px] font-bold text-slate-500 uppercase tracking-widest mt-0.5">{{ $obra->nome }}</p>
            </div>
        </div>
    </x-slot>

    <div class="max-w-2xl mx-auto space-y-4 pb-24 px-4" x-data="{ showCustom: {{ $periodoAtivo === 'custom' ? 'true' : 'false' }} }">

        {{-- Hero: resumo do período --}}
        <div class="bg-white/5 backdrop-blur-xl border border-white/10 rounded-3xl p-6 shadow-2xl overflow-hidden relative">
            <div class="absolute -right-8 -top-8 w-32 h-32 bg-indigo-500/5 rounded-full blur-3xl"></div>

            <div class="relative z-10">
                <p class="text-[10px] font-black text-slate-500 uppercase tracking-[0.2em] mb-1">Período</p>
                <p class="text-sm font-bold text-slate-400 mb-4">
                    {{ $dataInicio->format('d/m') }} – {{ $dataFim->format('d/m/Y') }}
                </p>

                <p class="text-[10px] font-black text-slate-500 uppercase tracking-[0.2em] mb-1">Valor Total</p>
                <h3 class="text-4xl font-black text-white leading-none mb-4">
                    R$ {{ number_format($totalValor, 2, ',', '.') }}
                </h3>

                <div class="inline-flex items-center gap-2 bg-indigo-500/10 border border-indigo-500/20 rounded-xl px-3 py-2">
                    <svg class="w-4 h-4 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                    <span class="text-xs font-black text-indigo-400 uppercase tracking-widest">
                        {{ $totalCount }} {{ $totalCount === 1 ? 'nota fiscal' : 'notas fiscais' }}
                    </span>
                </div>
            </div>
        </div>

        {{-- Filtros por preset --}}
        <div class="bg-white/5 backdrop-blur-xl border border-white/10 rounded-2xl p-4 shadow-xl space-y-4">
            <p class="text-[10px] font-black text-slate-500 uppercase tracking-widest px-1">Filtrar período</p>

            @php
                $presets = [
                    'este_mes' => 'Este mês',
                    'mes_anterior' => 'Mês anterior',
                    'ultimos_30' => 'Últimos 30 dias',
                    'custom' => 'Personalizado',
                ];
            @endphp

            <div class="grid grid-cols-2 gap-2">
                @foreach($presets as $key => $label)
                    @if($key === 'custom')
                        <button
                            type="button"
                            @click="showCustom = !showCustom"
                            class="w-full px-4 py-4 rounded-2xl text-left transition-all active:scale-[0.98] border
                                {{ $periodoAtivo === 'custom' ? 'bg-indigo-500/15 text-indigo-300 border-indigo-500/40 ring-1 ring-indigo-500/30' : 'bg-slate-900/40 text-slate-400 border-white/5' }}"
                        >
                            <span class="block text-xs font-black uppercase tracking-wide leading-tight">{{ $label }}</span>
                            @if($periodoAtivo === 'custom')
                                <span class="block text-[9px] font-bold text-indigo-400/80 mt-1 normal-case tracking-normal">Intervalo ativo</span>
                            @endif
                        </button>
                    @else
                        <a
                            href="{{ route('nota-fiscals.index', ['periodo' => $key]) }}"
                            class="w-full px-4 py-4 rounded-2xl text-left transition-all active:scale-[0.98] border
                                {{ $periodoAtivo === $key ? 'bg-indigo-500/15 text-indigo-300 border-indigo-500/40 ring-1 ring-indigo-500/30' : 'bg-slate-900/40 text-slate-400 border-white/5' }}"
                        >
                            <span class="block text-xs font-black uppercase tracking-wide leading-tight">{{ $label }}</span>
                        </a>
                    @endif
                @endforeach
            </div>

            {{-- Intervalo customizado --}}
            <div x-show="showCustom" x-transition class="pt-4 border-t border-white/10 space-y-4" style="display: none;">
                <form action="{{ route('nota-fiscals.index') }}" method="GET" class="space-y-4">
                    <input type="hidden" name="periodo" value="custom">

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="text-[10px] font-bold text-slate-500 uppercase tracking-widest mb-1 block">Data Inicial</label>
                            <input
                                type="date"
                                name="data_inicio"
                                value="{{ $periodoAtivo === 'custom' ? $dataInicio->format('Y-m-d') : '' }}"
                                required
                                class="w-full bg-slate-900/50 border border-white/10 rounded-xl text-white text-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 py-3 px-3 [color-scheme:dark]"
                            >
                        </div>
                        <div>
                            <label class="text-[10px] font-bold text-slate-500 uppercase tracking-widest mb-1 block">Data Final</label>
                            <input
                                type="date"
                                name="data_fim"
                                value="{{ $periodoAtivo === 'custom' ? $dataFim->format('Y-m-d') : '' }}"
                                required
                                class="w-full bg-slate-900/50 border border-white/10 rounded-xl text-white text-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 py-3 px-3 [color-scheme:dark]"
                            >
                        </div>
                    </div>

                    <div class="flex gap-2">
                        <button type="submit" class="flex-1 px-4 py-3 bg-indigo-500 text-white font-black uppercase tracking-widest text-[10px] rounded-xl transition-all active:scale-95 shadow-lg shadow-indigo-500/20">
                            Aplicar
                        </button>
                        @if($periodoAtivo !== 'este_mes')
                            <a href="{{ route('nota-fiscals.index', ['periodo' => 'este_mes']) }}" class="px-4 py-3 bg-white/5 text-slate-400 font-black uppercase tracking-widest text-[10px] rounded-xl transition-all active:scale-95 border border-white/10">
                                Limpar
                            </a>
                        @endif
                    </div>
                </form>
            </div>
        </div>

        {{-- Lista de notas --}}
        @if($notas->isEmpty())
            <div class="flex flex-col items-center justify-center py-16 bg-white/5 border border-white/10 rounded-3xl border-dashed px-6">
                <div class="w-14 h-14 bg-slate-800 rounded-full flex items-center justify-center text-slate-600 mb-4">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                </div>

                @if($totalNotasObra === 0)
                    <h3 class="text-white font-bold mb-1 text-center">Nenhuma nota fiscal</h3>
                    <p class="text-slate-500 text-xs text-center leading-relaxed">
                        Registre suas notas fiscais pelo botão <span class="text-indigo-400 font-bold">+</span> no menu inferior.
                    </p>
                @else
                    <h3 class="text-white font-bold mb-1 text-center">Nenhuma nota neste período</h3>
                    <p class="text-slate-500 text-xs text-center leading-relaxed">
                        Tente outro período ou ajuste o intervalo personalizado.
                    </p>
                @endif
            </div>
        @else
            <div class="space-y-3">
                <h3 class="text-[10px] font-black text-slate-500 uppercase tracking-widest flex items-center gap-2 px-1">
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"></path></svg>
                    Notas do Período
                </h3>

                @foreach($notas as $nota)
                    <div class="bg-white/5 backdrop-blur-xl border border-white/10 rounded-2xl p-4 shadow-xl active:scale-[0.99] transition-transform">
                        <div class="flex items-start gap-3">
                            {{-- Badge de data --}}
                            <div class="w-12 h-12 bg-white/5 rounded-xl border border-white/10 flex flex-col items-center justify-center text-center shrink-0">
                                <span class="text-[9px] text-indigo-400 font-bold uppercase">{{ $nota->data_recebimento->translatedFormat('M') }}</span>
                                <span class="text-lg text-white font-black leading-none">{{ $nota->data_recebimento->format('d') }}</span>
                            </div>

                            <div class="flex-1 min-w-0">
                                <div class="flex justify-between items-start gap-2">
                                    <div class="min-w-0">
                                        <p class="text-[9px] font-black text-indigo-400 uppercase tracking-widest mb-0.5">Nº {{ $nota->numero_nota }}</p>
                                        <h4 class="text-white font-bold text-sm leading-tight truncate">{{ $nota->descricao }}</h4>
                                        <p class="text-[10px] text-slate-500 font-bold uppercase tracking-tighter mt-1">{{ $nota->quem_recebeu }}</p>
                                    </div>
                                    <p class="text-sm font-black text-white shrink-0">
                                        R$ {{ number_format($nota->valor, 2, ',', '.') }}
                                    </p>
                                </div>

                                <div class="flex items-center gap-2 mt-3 pt-3 border-t border-white/5">
                                    @if($nota->arquivo_path)
                                        <a
                                            href="{{ asset('storage/' . $nota->arquivo_path) }}"
                                            target="_blank"
                                            class="flex items-center gap-1.5 px-3 py-2 bg-indigo-500/10 text-indigo-400 rounded-xl text-[10px] font-black uppercase tracking-widest active:scale-95 transition-transform"
                                        >
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
                                            Ver PDF
                                        </a>
                                    @endif

                                    <form action="{{ route('nota-fiscals.destroy', $nota) }}" method="POST" class="ml-auto" onsubmit="return confirm('Tem certeza que deseja excluir esta nota?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="flex items-center gap-1.5 px-3 py-2 bg-rose-500/10 text-rose-500 rounded-xl text-[10px] font-black uppercase tracking-widest active:scale-95 transition-transform">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                            Excluir
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</x-app-layout>
