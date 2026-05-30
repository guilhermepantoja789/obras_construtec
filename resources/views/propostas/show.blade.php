<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div class="flex items-center gap-3 min-w-0">
                <a href="{{ route('propostas.index') }}" class="p-2 bg-white/5 hover:bg-white/10 rounded-xl text-slate-400 transition-all border border-white/5 shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                </a>
                <div class="min-w-0">
                    <p class="text-[9px] font-black text-amber-500 uppercase tracking-widest mb-0.5 truncate">Orçamento #{{ $proposta->id }}</p>
                    <h2 class="font-black text-sm sm:text-lg text-white leading-tight uppercase tracking-tight truncate">
                        {{ $proposta->titulo }}
                    </h2>
                </div>
            </div>
            
            <div class="flex items-center gap-2 w-full sm:w-auto justify-between sm:justify-end">
                <a href="{{ route('propostas.edit', $proposta) }}" class="flex items-center gap-2 px-3 py-1.5 bg-white/5 hover:bg-white/10 text-white rounded-lg border border-white/10 text-[8px] font-black uppercase tracking-widest transition-all active:scale-95">
                    <svg class="w-3 h-3 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                    Editar
                </a>
                <span class="text-[8px] font-black uppercase px-2 py-1 rounded-md tracking-widest shrink-0
                    @if($proposta->status == 'aceita') bg-green-500 text-slate-900
                    @elseif($proposta->status == 'recusada') bg-rose-500 text-white
                    @else bg-blue-500 text-white @endif">
                    {{ $proposta->status }}
                </span>
            </div>
        </div>
    </x-slot>

    <div class="max-w-5xl mx-auto space-y-4 pb-24 px-4 mt-4">
        <!-- Resumo -->
        <div class="bg-white/5 backdrop-blur-xl border border-white/10 rounded-3xl p-6 shadow-2xl">
            <div class="space-y-6">
                <div>
                    <h4 class="text-[9px] font-black text-slate-500 uppercase tracking-[0.2em] mb-2">Escopo do Projeto</h4>
                    <p class="text-xs text-slate-300 leading-relaxed italic break-words">
                        "{{ $proposta->escopo ?: 'Sem descrição de escopo.' }}"
                    </p>
                </div>
                <div class="pt-4 border-t border-white/5 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                    <div>
                        <p class="text-[9px] font-black text-slate-500 uppercase tracking-[0.2em] mb-0.5">Subtotal dos Itens</p>
                        <p class="text-lg font-black text-slate-300">R$ {{ number_format($encargosResumo['subtotal_itens'], 2, ',', '.') }}</p>
                    </div>
                    <div class="text-left sm:text-right">
                        <p class="text-[9px] font-black text-slate-500 uppercase tracking-[0.2em] mb-0.5">Valor Total</p>
                        <p class="text-2xl font-black text-white">R$ {{ number_format($proposta->valor_total, 2, ',', '.') }}</p>
                    </div>
                </div>
                @if(!empty($encargosResumo['encargos']))
                <div class="pt-3 space-y-1">
                    @foreach($encargosResumo['encargos'] as $enc)
                        <div class="flex justify-between text-[10px]">
                            <span class="text-slate-500">{{ $enc['label'] }} ({{ number_format($enc['percent'], 2, ',', '.') }}%)</span>
                            <span class="{{ !empty($enc['subtrai']) ? 'text-rose-400' : 'text-emerald-400' }} font-bold">
                                {{ !empty($enc['subtrai']) ? '−' : '+' }} R$ {{ number_format($enc['valor'], 2, ',', '.') }}
                            </span>
                        </div>
                    @endforeach
                </div>
                @endif
                <p class="text-[9px] text-slate-600 font-bold uppercase mt-2">EMITIDA EM {{ $proposta->data_proposta->format('d/m/Y') }}</p>
            </div>
        </div>

        @php
            $groupMeta = [];
            $hideChildSubtotalById = [];
            $etapaDisplaySubtotalById = [];

            foreach ($grupos as $idx => $grupo) {
                $etapa = $grupo['etapa'] ?? null;
                $childrenSubtotal = collect($grupo['items'] ?? [])->sum('subtotal');
                $hasOwnSubtotal = $etapa && ((float) $etapa->subtotal > 0.00001);
                $displaySubtotal = $etapa
                    ? ($hasOwnSubtotal ? (float) $etapa->subtotal : (float) $childrenSubtotal)
                    : (float) $childrenSubtotal;

                $groupMeta[$idx] = [
                    'displaySubtotal' => $displaySubtotal,
                    'hideChildrenSubtotal' => $hasOwnSubtotal,
                ];

                if ($etapa) {
                    $etapaDisplaySubtotalById[$etapa->id] = $displaySubtotal;
                }

                if ($hasOwnSubtotal) {
                    foreach (($grupo['items'] ?? []) as $child) {
                        $hideChildSubtotalById[$child->id] = true;
                    }
                }
            }
        @endphp

        <!-- Itens -->
        <div class="space-y-3" x-data="{ searchQuery: '', showOnlyEtapas: false, groupByEtapa: true, collapsed: {} }">
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3 px-2">
                <h3 class="text-[10px] font-black text-slate-500 uppercase tracking-widest">Planilha de Itens ({{ $items->count() }})</h3>
                <div class="flex flex-wrap items-center gap-2 w-full sm:w-auto">
                    <input type="search" x-model="searchQuery" placeholder="Buscar…" class="flex-1 sm:w-48 bg-slate-900/50 border-white/10 rounded-xl text-white text-xs py-2 px-3">
                    <label class="flex items-center gap-2 text-[9px] font-black text-slate-500 uppercase">
                        <input type="checkbox" x-model="showOnlyEtapas" class="rounded bg-slate-900 border-white/10 text-amber-500"> Só etapas
                    </label>
                    <label class="flex items-center gap-2 text-[9px] font-black text-slate-500 uppercase">
                        <input type="checkbox" x-model="groupByEtapa" class="rounded bg-slate-900 border-white/10 text-amber-500"> Agrupar
                    </label>
                </div>
            </div>

            @if($proposta->status === 'aceita')
            <div class="px-2">
                <p class="text-[9px] text-amber-500/80 uppercase font-bold">Proposta aceita — etapas marcadas alimentam o cronograma da obra.</p>
            </div>
            @endif

            <!-- Vista agrupada -->
            <div x-show="groupByEtapa" class="space-y-3">
                @foreach($grupos as $grupo)
                    @php
                        $etapa = $grupo['etapa'];
                        $ordemKey = $etapa ? (string) $etapa->ordem : 'sem-etapa-'.$loop->index;
                        $meta = $groupMeta[$loop->index] ?? ['displaySubtotal' => 0, 'hideChildrenSubtotal' => false];
                    @endphp
                    <div class="bg-white/5 border border-white/10 rounded-2xl overflow-hidden">
                        @if($etapa)
                        <button type="button" @click="collapsed['{{ $ordemKey }}'] = !collapsed['{{ $ordemKey }}']" class="w-full flex items-center justify-between px-4 py-3 bg-white/[0.03] border-b border-white/5 text-left">
                            <div class="flex items-center gap-2 min-w-0">
                                <span class="text-[10px] font-black text-amber-500 shrink-0">{{ $etapa->ordem }}</span>
                                <span class="text-xs font-black text-white uppercase truncate">{{ $etapa->descricao }}</span>
                                @if($etapa->is_etapa)
                                    <span class="text-[8px] font-black uppercase px-1.5 py-0.5 rounded bg-blue-500/10 text-blue-400 shrink-0">Etapa</span>
                                @endif
                            </div>
                            <span class="text-slate-500 text-xs shrink-0" x-text="collapsed['{{ $ordemKey }}'] ? '▶' : '▼'"></span>
                        </button>
                        @else
                        <div class="px-4 py-3 bg-white/[0.03] border-b border-white/5 text-[9px] font-black text-slate-500 uppercase">Itens avulsos</div>
                        @endif
                        <div x-show="!collapsed['{{ $ordemKey }}']" class="divide-y divide-white/5">
                            @if($etapa)
                                <div class="px-4 py-2 flex justify-between items-center"
                                    x-show="(!showOnlyEtapas || {{ $etapa->is_etapa ? 'true' : 'false' }}) && (searchQuery === '' || '{{ addslashes(strtolower($etapa->descricao.' '.$etapa->ordem)) }}'.includes(searchQuery.toLowerCase()))">
                                    <span class="text-xs font-bold {{ $etapa->is_etapa ? 'text-amber-500 uppercase' : 'text-white' }}">{{ $etapa->descricao }}</span>
                                    <span class="text-xs font-black text-white">
                                        <span class="text-[9px] text-slate-500 uppercase mr-1">Valor total</span>
                                        R$ {{ number_format($meta['displaySubtotal'], 2, ',', '.') }}
                                    </span>
                                </div>
                            @endif
                            @foreach($grupo['items'] as $item)
                                <div class="px-4 py-3 flex justify-between items-center pl-8"
                                    x-show="(!showOnlyEtapas || {{ $item->is_etapa ? 'true' : 'false' }}) && (searchQuery === '' || '{{ addslashes(strtolower($item->descricao.' '.$item->ordem)) }}'.includes(searchQuery.toLowerCase()))">
                                    <div class="flex items-center gap-2 min-w-0">
                                        <span class="text-[9px] font-black text-slate-600">{{ $item->ordem }}</span>
                                        <span class="text-xs text-white truncate">{{ $item->descricao }}</span>
                                    </div>
                                    <span class="text-xs font-black shrink-0 ml-2 {{ !empty($meta['hideChildrenSubtotal']) ? 'text-slate-600' : 'text-white' }}">
                                        @if(!empty($meta['hideChildrenSubtotal']))
                                            —
                                        @else
                                            R$ {{ number_format($item->subtotal, 2, ',', '.') }}
                                        @endif
                                    </span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Vista plana -->
            <div x-show="!groupByEtapa" class="hidden sm:block overflow-hidden bg-white/5 border border-white/10 rounded-3xl">
                <table class="w-full text-left">
                    <thead>
                        <tr class="bg-white/5 border-b border-white/10">
                            <th class="px-6 py-4 text-[9px] font-black text-slate-500 uppercase tracking-widest w-16">Nº</th>
                            <th class="px-6 py-4 text-[9px] font-black text-slate-500 uppercase tracking-widest">Descrição</th>
                            <th class="px-6 py-4 text-[9px] font-black text-slate-500 uppercase tracking-widest text-center">Unid.</th>
                            <th class="px-6 py-4 text-[9px] font-black text-slate-500 uppercase tracking-widest text-center">Qtd.</th>
                            <th class="px-6 py-4 text-[9px] font-black text-slate-500 uppercase tracking-widest text-right">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/5">
                        @foreach($items as $item)
                            @php
                                $hideSubtotal = !empty($hideChildSubtotalById[$item->id]);
                                $displaySubtotal = $etapaDisplaySubtotalById[$item->id] ?? $item->subtotal;
                            @endphp
                            <tr class="hover:bg-white/5 transition-colors {{ $item->is_etapa ? 'bg-white/[0.03]' : '' }}"
                                x-show="(!showOnlyEtapas || {{ $item->is_etapa ? 'true' : 'false' }}) && (searchQuery === '' || '{{ addslashes(strtolower($item->descricao.' '.$item->ordem)) }}'.includes(searchQuery.toLowerCase()))">
                                <td class="px-6 py-4 text-[10px] font-black text-slate-500">{{ $item->ordem }}</td>
                                <td class="px-6 py-4">
                                    <span class="text-xs font-bold {{ $item->is_etapa ? 'text-amber-500 uppercase' : 'text-white' }}">{{ $item->descricao }}</span>
                                </td>
                                <td class="px-6 py-4 text-[10px] text-slate-400 text-center">{{ $item->unidade ?: '-' }}</td>
                                <td class="px-6 py-4 text-xs text-white text-center font-bold">{{ number_format($item->quantidade, 2, ',', '.') }}</td>
                                <td class="px-6 py-4 text-xs font-black text-right {{ $hideSubtotal ? 'text-slate-600' : 'text-white' }}">
                                    @if($hideSubtotal)
                                        —
                                    @else
                                        R$ {{ number_format($displaySubtotal, 2, ',', '.') }}
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <div class="flex justify-center pt-8">
            <p class="text-[8px] text-slate-700 font-black uppercase tracking-widest flex items-center gap-3">
                <span class="hidden sm:block w-8 h-px bg-white/5"></span>
                Documento de Controle Interno
                <span class="hidden sm:block w-8 h-px bg-white/5"></span>
            </p>
        </div>
    </div>
</x-app-layout>
