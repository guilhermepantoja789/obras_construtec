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
                        <p class="text-[9px] font-black text-slate-500 uppercase tracking-[0.2em] mb-0.5">Valor Total</p>
                        <p class="text-2xl font-black text-white">R$ {{ number_format($proposta->valor_total, 2, ',', '.') }}</p>
                    </div>
                    <p class="text-[9px] text-slate-600 font-bold uppercase">EMITIDA EM {{ $proposta->data_proposta->format('d/m/Y') }}</p>
                </div>
            </div>
        </div>

        <!-- Itens (Mobile Friendly) -->
        <div class="space-y-3">
            <h3 class="text-[10px] font-black text-slate-500 uppercase tracking-widest px-2">Planilha de Itens</h3>
            
            <div class="hidden sm:block overflow-hidden bg-white/5 border border-white/10 rounded-3xl">
                <table class="w-full text-left">
                    <thead>
                        <tr class="bg-white/5 border-b border-white/10">
                            <th class="px-6 py-4 text-[9px] font-black text-slate-500 uppercase tracking-widest">Item</th>
                            <th class="px-6 py-4 text-[9px] font-black text-slate-500 uppercase tracking-widest text-center">Unid.</th>
                            <th class="px-6 py-4 text-[9px] font-black text-slate-500 uppercase tracking-widest text-center">Qtd.</th>
                            <th class="px-6 py-4 text-[9px] font-black text-slate-500 uppercase tracking-widest text-right">Unitário</th>
                            <th class="px-6 py-4 text-[9px] font-black text-slate-500 uppercase tracking-widest text-right">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/5">
                        @foreach($proposta->items as $item)
                            <tr class="hover:bg-white/5 transition-colors">
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-2">
                                        @if($item->is_etapa)
                                            <span class="w-1.5 h-1.5 bg-blue-500 rounded-full"></span>
                                        @endif
                                        <span class="text-xs font-bold text-white">{{ $item->descricao }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-[10px] text-slate-400 text-center">{{ $item->unidade ?: '-' }}</td>
                                <td class="px-6 py-4 text-xs text-white text-center font-bold">{{ number_format($item->quantidade, 2, ',', '.') }}</td>
                                <td class="px-6 py-4 text-[10px] text-slate-400 text-right">R$ {{ number_format($item->valor_unitario, 2, ',', '.') }}</td>
                                <td class="px-6 py-4 text-xs font-black text-white text-right">R$ {{ number_format($item->subtotal, 2, ',', '.') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Mobile List -->
            <div class="sm:hidden space-y-2">
                @foreach($proposta->items as $item)
                    <div class="bg-white/5 border border-white/10 rounded-2xl p-4">
                        <div class="flex items-start justify-between gap-4 mb-2">
                            <div class="flex items-center gap-2 min-w-0">
                                @if($item->is_etapa)
                                    <span class="w-1.5 h-1.5 bg-blue-500 rounded-full shrink-0"></span>
                                @endif
                                <p class="text-xs font-bold text-white truncate">{{ $item->descricao }}</p>
                            </div>
                            <span class="text-xs font-black text-white shrink-0">R$ {{ number_format($item->subtotal, 2, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between items-center text-[9px] font-bold text-slate-500 uppercase tracking-tighter">
                            <span>{{ number_format($item->quantidade, 2, ',', '.') }} {{ $item->unidade ?: 'un' }}</span>
                            <span>R$ {{ number_format($item->valor_unitario, 2, ',', '.') }} / {{ $item->unidade ?: 'un' }}</span>
                        </div>
                    </div>
                @endforeach
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
