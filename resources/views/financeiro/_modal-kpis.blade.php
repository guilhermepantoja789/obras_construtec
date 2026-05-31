<div
    x-show="showKpisModal"
    x-cloak
    x-transition:enter="transition ease-out duration-200"
    x-transition:enter-start="opacity-0 translate-y-full sm:translate-y-4"
    x-transition:enter-end="opacity-100 translate-y-0"
    x-transition:leave="transition ease-in duration-150"
    x-transition:leave-start="opacity-100 translate-y-0"
    x-transition:leave-end="opacity-0 translate-y-full sm:translate-y-4"
    class="fixed inset-0 z-[200] flex items-end sm:items-center justify-center p-0 sm:p-4"
    style="display: none;"
>
    <div class="fixed inset-0 bg-slate-950/80 backdrop-blur-md" @click="showKpisModal = false"></div>

    <div class="relative w-full sm:max-w-lg bg-slate-900 border border-white/10 rounded-t-3xl sm:rounded-3xl shadow-2xl max-h-[90vh] overflow-hidden flex flex-col safe-area-bottom">
        {{-- Header --}}
        <div class="shrink-0 flex items-center justify-between gap-4 p-5 border-b border-white/5">
            <div>
                <h3 class="text-white font-black uppercase tracking-widest text-sm">Métricas Financeiras</h3>
                <p class="text-[9px] font-bold text-slate-500 uppercase tracking-widest mt-0.5">
                    @if($kpis['filtro_ativo'])
                        Baseado nos filtros aplicados
                    @else
                        Visão geral da obra
                    @endif
                </p>
            </div>
            <button type="button" @click="showKpisModal = false" class="p-2 text-slate-500 hover:text-white bg-white/5 rounded-xl shrink-0">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        </div>

        {{-- Body scrollável --}}
        <div class="overflow-y-auto p-5 space-y-6">
            {{-- Destaque principal --}}
            <div class="text-center py-2">
                <p class="text-[10px] font-black text-slate-500 uppercase tracking-[0.2em] mb-2">Saldo Operacional</p>
                <p class="text-3xl font-black {{ $kpis['saldo_operacional'] >= 0 ? 'text-emerald-400' : 'text-rose-400' }} tracking-tight">
                    R$ {{ number_format($kpis['saldo_operacional'], 2, ',', '.') }}
                </p>
                @if($kpis['total_recebido'] > 0)
                    <p class="text-[10px] text-slate-500 mt-1">Margem {{ number_format($kpis['margem_percentual'], 1, ',', '.') }}%</p>
                @endif
            </div>

            {{-- Fluxo --}}
            <div>
                <p class="text-[9px] font-black text-slate-600 uppercase tracking-widest mb-3">Fluxo de caixa</p>
                <div class="grid grid-cols-2 gap-3">
                    <div class="bg-green-500/10 border border-green-500/15 rounded-2xl p-4">
                        <p class="text-[9px] font-black text-green-500/80 uppercase tracking-widest mb-1">Recebido</p>
                        <p class="text-lg font-black text-white leading-none">R$ {{ number_format($kpis['total_recebido'], 2, ',', '.') }}</p>
                        <p class="text-[9px] text-slate-500 mt-1.5">{{ $kpis['qtd_recebidas'] }} lançamento(s)</p>
                    </div>
                    <div class="bg-rose-500/10 border border-rose-500/15 rounded-2xl p-4">
                        <p class="text-[9px] font-black text-rose-500/80 uppercase tracking-widest mb-1">Pago</p>
                        <p class="text-lg font-black text-white leading-none">R$ {{ number_format($kpis['total_pago'], 2, ',', '.') }}</p>
                        <p class="text-[9px] text-slate-500 mt-1.5">{{ $kpis['qtd_despesas_pagas'] }} despesa(s)</p>
                    </div>
                    @if($kpis['total_pendente'] > 0)
                    <div class="bg-amber-500/10 border border-amber-500/15 rounded-2xl p-4">
                        <p class="text-[9px] font-black text-amber-500/80 uppercase tracking-widest mb-1">A pagar</p>
                        <p class="text-lg font-black text-amber-400 leading-none">R$ {{ number_format($kpis['total_pendente'], 2, ',', '.') }}</p>
                        <p class="text-[9px] text-slate-500 mt-1.5">{{ $kpis['qtd_pendentes'] }} pendente(s)</p>
                    </div>
                    @endif
                    <div class="bg-white/[0.03] border border-white/[0.06] rounded-2xl p-4 {{ $kpis['total_pendente'] <= 0 ? 'col-span-2' : '' }}">
                        <p class="text-[9px] font-black text-slate-500 uppercase tracking-widest mb-1">Total movimentado</p>
                        <p class="text-lg font-black text-white leading-none">R$ {{ number_format($kpis['total_movimentado'], 2, ',', '.') }}</p>
                        <p class="text-[9px] text-slate-500 mt-1.5">{{ $kpis['qtd_lancamentos'] }} lançamento(s) no filtro</p>
                    </div>
                </div>
            </div>

            @if($kpis['qtd_lancamentos'] > 0)
            {{-- Análise --}}
            <div>
                <p class="text-[9px] font-black text-slate-600 uppercase tracking-widest mb-3">Análise</p>
                <div class="grid grid-cols-2 gap-2">
                    <div class="bg-slate-800/50 border border-white/5 rounded-xl p-3">
                        <p class="text-[8px] font-bold text-slate-500 uppercase tracking-widest mb-1">Média receita</p>
                        <p class="text-sm font-black text-white">R$ {{ number_format($kpis['media_recebimento'], 2, ',', '.') }}</p>
                    </div>
                    <div class="bg-slate-800/50 border border-white/5 rounded-xl p-3">
                        <p class="text-[8px] font-bold text-slate-500 uppercase tracking-widest mb-1">Média despesa</p>
                        <p class="text-sm font-black text-white">R$ {{ number_format($kpis['media_despesa'], 2, ',', '.') }}</p>
                    </div>
                    <div class="bg-slate-800/50 border border-white/5 rounded-xl p-3">
                        <p class="text-[8px] font-bold text-slate-500 uppercase tracking-widest mb-1">Maior receita</p>
                        <p class="text-sm font-black text-green-400">R$ {{ number_format($kpis['maior_recebimento'], 2, ',', '.') }}</p>
                    </div>
                    <div class="bg-slate-800/50 border border-white/5 rounded-xl p-3">
                        <p class="text-[8px] font-bold text-slate-500 uppercase tracking-widest mb-1">Maior despesa</p>
                        <p class="text-sm font-black text-rose-400">R$ {{ number_format($kpis['maior_despesa'], 2, ',', '.') }}</p>
                    </div>
                </div>
            </div>

            {{-- Composição --}}
            <div>
                <p class="text-[9px] font-black text-slate-600 uppercase tracking-widest mb-3">Composição</p>
                <div class="space-y-2">
                    @if($kpis['top_categoria_label'])
                    <div class="flex items-center justify-between gap-3 bg-slate-800/50 border border-white/5 rounded-xl px-4 py-3">
                        <div>
                            <p class="text-[8px] font-bold text-slate-500 uppercase tracking-widest">Top categoria</p>
                            <p class="text-xs font-black text-white mt-0.5">{{ $kpis['top_categoria_label'] }}</p>
                        </div>
                        <p class="text-sm font-black text-slate-300">R$ {{ number_format($kpis['top_categoria_valor'], 2, ',', '.') }}</p>
                    </div>
                    @endif
                    <div class="flex items-center justify-between gap-3 bg-slate-800/50 border border-white/5 rounded-xl px-4 py-3">
                        <div>
                            <p class="text-[8px] font-bold text-slate-500 uppercase tracking-widest">Comprovantes</p>
                            <p class="text-xs font-black text-white mt-0.5">{{ $kpis['com_comprovante'] }} com anexo</p>
                        </div>
                        @if($kpis['sem_comprovante'] > 0)
                            <p class="text-[10px] font-bold text-slate-500">{{ $kpis['sem_comprovante'] }} sem</p>
                        @else
                            <p class="text-[10px] font-bold text-emerald-500/70">Completo</p>
                        @endif
                    </div>
                    <div class="grid grid-cols-3 gap-2">
                        <div class="bg-slate-800/30 border border-white/5 rounded-xl p-3 text-center">
                            <p class="text-lg font-black text-green-400/90">{{ $kpis['qtd_recebidas'] }}</p>
                            <p class="text-[8px] font-bold text-slate-600 uppercase tracking-widest mt-0.5">Recebidas</p>
                        </div>
                        <div class="bg-slate-800/30 border border-white/5 rounded-xl p-3 text-center">
                            <p class="text-lg font-black text-rose-400/90">{{ $kpis['qtd_despesas_pagas'] }}</p>
                            <p class="text-[8px] font-bold text-slate-600 uppercase tracking-widest mt-0.5">Pagas</p>
                        </div>
                        <div class="bg-slate-800/30 border border-white/5 rounded-xl p-3 text-center">
                            <p class="text-lg font-black text-amber-400/90">{{ $kpis['qtd_pendentes'] }}</p>
                            <p class="text-[8px] font-bold text-slate-600 uppercase tracking-widest mt-0.5">Pendentes</p>
                        </div>
                    </div>
                </div>
            </div>
            @else
            <div class="text-center py-6 border border-dashed border-white/5 rounded-2xl">
                <p class="text-[10px] font-bold text-slate-600 uppercase tracking-widest">Sem lançamentos para analisar</p>
            </div>
            @endif

            @if($proposta)
            @php
                $totalProposta = $proposta->valor_total;
                $restanteCliente = $totalProposta - $totalRecebidoGeral;
                $percentualFinanceiro = $totalProposta > 0 ? round(($totalRecebidoGeral / $totalProposta) * 100) : 0;
            @endphp
            {{-- Contrato --}}
            <div>
                <p class="text-[9px] font-black text-slate-600 uppercase tracking-widest mb-3">Contrato cliente · geral</p>
                <div class="bg-white/[0.03] border border-white/[0.06] rounded-2xl p-4 space-y-4">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <p class="text-[8px] font-bold text-slate-500 uppercase tracking-widest mb-1">Contratado</p>
                            <p class="text-sm font-black text-white">R$ {{ number_format($totalProposta, 2, ',', '.') }}</p>
                        </div>
                        <div class="text-right">
                            <p class="text-[8px] font-bold text-slate-500 uppercase tracking-widest mb-1">Saldo devedor</p>
                            <p class="text-sm font-black {{ $restanteCliente > 0 ? 'text-amber-500' : 'text-green-500' }}">
                                R$ {{ number_format($restanteCliente, 2, ',', '.') }}
                            </p>
                        </div>
                    </div>
                    <div class="space-y-1.5">
                        <div class="flex justify-between text-[9px] font-black uppercase tracking-widest">
                            <span class="text-slate-500">Liquidado</span>
                            <span class="text-green-500">{{ $percentualFinanceiro }}%</span>
                        </div>
                        <div class="h-1.5 bg-slate-800 rounded-full overflow-hidden">
                            <div class="h-full bg-gradient-to-r from-green-600 to-emerald-400 rounded-full" style="width: {{ $percentualFinanceiro }}%"></div>
                        </div>
                    </div>
                </div>
            </div>
            @endif

            @if($kpis['filtro_ativo'])
            <div class="flex flex-wrap gap-1.5 pt-1">
                @if($filters['periodo'])
                    <span class="px-2 py-0.5 rounded-md bg-white/5 text-[8px] font-bold text-slate-500 uppercase">{{ match($filters['periodo']) { 'mes_atual' => 'Este mês', 'mes_anterior' => 'Mês passado', '30_dias' => '30 dias', 'ano_atual' => 'Este ano', default => $filters['periodo'] } }}</span>
                @endif
                @if($filters['tipo'] !== 'todos')
                    <span class="px-2 py-0.5 rounded-md bg-white/5 text-[8px] font-bold text-slate-500 uppercase">{{ $filters['tipo'] }}</span>
                @endif
                @if($filters['categoria'])
                    <span class="px-2 py-0.5 rounded-md bg-white/5 text-[8px] font-bold text-slate-500 uppercase">{{ $categorias[$filters['categoria']] ?? $filters['categoria'] }}</span>
                @endif
            </div>
            @endif
        </div>
    </div>
</div>
