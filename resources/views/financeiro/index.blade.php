<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <p class="text-[10px] font-black text-green-500 uppercase tracking-[0.2em] mb-1">Gestão Financeira</p>
                <h2 class="font-black text-xl text-white leading-tight uppercase tracking-tight">
                    Fluxo de Caixa da Obra
                </h2>
            </div>
            
            @if($proposta)
                <button @click="$dispatch('open-add-pagamento-modal')" class="flex items-center gap-2 px-5 py-2.5 bg-green-600 hover:bg-green-500 text-white rounded-xl transition-all shadow-lg shadow-green-600/20 text-[10px] font-black uppercase tracking-widest active:scale-95">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                    Novo Pagamento
                </button>
            @endif
        </div>
    </x-slot>

    <div class="max-w-5xl mx-auto space-y-6 pb-24 px-4" 
        x-data="{ showAddPagamentoModal: {{ $errors->hasAny(['valor_pago', 'data_pagamento', 'proposta_id', 'observacao']) ? 'true' : 'false' }} }"
        @open-add-pagamento-modal.window="showAddPagamentoModal = true"
    >

        @if(!$proposta)
            <div class="bg-rose-500/10 border border-rose-500/20 rounded-3xl p-12 text-center">
                <div class="w-16 h-16 bg-rose-500/10 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8 text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                </div>
                <p class="text-rose-500 font-black uppercase text-sm mb-2">Proposta não encontrada</p>
                <p class="text-slate-500 text-xs max-w-xs mx-auto leading-relaxed">
                    Você precisa ter uma proposta cadastrada e aceita para esta obra para gerenciar os pagamentos.
                </p>
            </div>
        @else
            <!-- Equilíbrio Financeiro -->
            <div class="bg-white/5 backdrop-blur-xl border border-white/10 rounded-3xl p-8 shadow-2xl overflow-hidden relative group">
                <div class="absolute -right-12 -top-12 w-48 h-48 bg-green-500/5 rounded-full blur-3xl group-hover:bg-green-500/10 transition-all"></div>
                
                @php
                    $somaPagamentos = $pagamentos->sum('valor_pago');
                    $totalProposta = $proposta->valor_total;
                    $restante = $totalProposta - $somaPagamentos;
                    $percentualFinanceiro = $totalProposta > 0 ? round(($somaPagamentos / $totalProposta) * 100) : 0;
                @endphp
                
                <div class="flex flex-col md:flex-row justify-between items-start md:items-end gap-6 relative z-10">
                    <div>
                        <p class="text-[10px] font-black text-slate-500 uppercase tracking-[0.2em] mb-1">Total Recebido</p>
                        <h3 class="text-4xl font-black text-white leading-none">
                            R$ {{ number_format($somaPagamentos, 2, ',', '.') }}
                        </h3>
                    </div>
                    <div class="text-left md:text-right">
                        <p class="text-[10px] font-black text-slate-500 uppercase tracking-[0.2em] mb-1">Valor Total Contratado</p>
                        <p class="text-xl font-bold text-slate-400">R$ {{ number_format($totalProposta, 2, ',', '.') }}</p>
                    </div>
                </div>
                
                <div class="mt-8 space-y-3 relative z-10">
                    <div class="flex justify-between items-end text-[10px] font-black uppercase tracking-widest">
                        <span class="text-slate-500">Progresso Financeiro</span>
                        <span class="text-green-500">{{ $percentualFinanceiro }}%</span>
                    </div>
                    <div class="w-full h-4 bg-slate-900/50 rounded-full overflow-hidden border border-white/5 p-0.5">
                        <div class="h-full bg-gradient-to-r from-green-600 to-emerald-400 rounded-full transition-all duration-1000 shadow-[0_0_20px_rgba(16,185,129,0.3)]" style="width: {{ $percentualFinanceiro }}%"></div>
                    </div>
                    <div class="flex justify-between items-center bg-slate-900/50 p-3 rounded-2xl border border-white/5">
                        <span class="text-[10px] font-black text-slate-500 uppercase tracking-widest">Saldo em Aberto</span>
                        <span class="text-xs font-black {{ $restante > 0 ? 'text-amber-500' : 'text-green-500' }}">
                            R$ {{ number_format($restante, 2, ',', '.') }}
                        </span>
                    </div>
                </div>
            </div>

            <!-- Histórico de Pagamentos -->
            <div class="space-y-4">
                <h3 class="text-[10px] font-black text-slate-500 uppercase tracking-widest flex items-center gap-2 px-2">
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    Histórico de Recebimentos
                </h3>
                
                <div class="grid grid-cols-1 gap-3">
                    @forelse($pagamentos as $pagto)
                        <div class="bg-white/5 border border-white/10 rounded-2xl p-4 flex justify-between items-center group hover:bg-white/10 transition-all">
                            <div class="flex items-center gap-4">
                                <div class="w-10 h-10 bg-green-500/10 rounded-xl flex items-center justify-center text-green-500 border border-green-500/20">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                </div>
                                <div>
                                    <p class="text-xs font-black text-white leading-tight mb-0.5">{{ $pagto->observacao ?: 'Recebimento' }}</p>
                                    <p class="text-[10px] text-slate-500 font-bold uppercase tracking-tighter">{{ $pagto->data_pagamento->format('d/m/Y') }}</p>
                                </div>
                            </div>
                            <div class="flex items-center gap-4">
                                <span class="text-sm font-black text-green-500">R$ {{ number_format($pagto->valor_pago, 2, ',', '.') }}</span>
                                <form action="{{ route('pagamentos.destroy', $pagto) }}" method="POST" onsubmit="return confirm('Excluir este registro?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="p-2 text-slate-600 hover:text-rose-500 transition-colors opacity-0 group-hover:opacity-100">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                    </button>
                                </form>
                            </div>
                        </div>
                    @empty
                        <div class="p-12 text-center border-2 border-dashed border-white/5 rounded-3xl">
                            <p class="text-slate-600 text-[10px] font-black uppercase tracking-widest">Nenhum pagamento registrado ainda</p>
                        </div>
                    @endforelse
                </div>
            </div>
        @endif

        <!-- MODAL DE PAGAMENTO -->
        <div x-show="showAddPagamentoModal" class="fixed inset-0 z-[100] flex items-center justify-center p-4" style="display:none;">
            <div class="fixed inset-0 bg-slate-950/80 backdrop-blur-md" @click="showAddPagamentoModal = false"></div>
            <div class="relative w-full max-w-lg bg-slate-900 border border-white/10 rounded-3xl shadow-2xl p-6" x-transition>
                <h3 class="text-white font-black uppercase tracking-widest text-sm mb-6">Registrar Novo Pagamento</h3>
                <form action="{{ route('pagamentos.store') }}" method="POST" class="space-y-4">
                    @csrf
                    <input type="hidden" name="proposta_id" value="{{ $proposta->id ?? '' }}">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="text-[10px] font-bold text-slate-500 uppercase mb-1 block">Valor Pago (R$)</label>
                            <input type="number" step="0.01" name="valor_pago" required value="{{ old('valor_pago') }}" class="w-full bg-slate-800 border-white/10 rounded-xl text-white text-sm focus:border-green-500">
                        </div>
                        <div>
                            <label class="text-[10px] font-bold text-slate-500 uppercase mb-1 block">Data</label>
                            <input type="date" name="data_pagamento" required value="{{ old('data_pagamento', date('Y-m-d')) }}" class="w-full bg-slate-800 border-white/10 rounded-xl text-white text-sm">
                        </div>
                    </div>
                    <div>
                        <label class="text-[10px] font-bold text-slate-500 uppercase mb-1 block">Descrição / Observação</label>
                        <input type="text" name="observacao" placeholder="Ex: Adiantamento, Parcela 01..." value="{{ old('observacao') }}" class="w-full bg-slate-800 border-white/10 rounded-xl text-white text-sm">
                    </div>
                    <button type="submit" class="w-full py-4 bg-green-600 hover:bg-green-500 text-white font-black rounded-xl transition-all shadow-lg shadow-green-600/20 uppercase tracking-widest text-xs mt-2">
                        Confirmar Recebimento
                    </button>
                </form>
            </div>
        </div>

    </div>
</x-app-layout>
