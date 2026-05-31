<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-start gap-4">
            <div class="min-w-0">
                <p class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] mb-1">Gestão Financeira</p>
                <h2 class="font-black text-xl text-white leading-tight uppercase tracking-tight truncate">
                    Contas da Obra
                </h2>
                <p class="text-[10px] font-bold text-slate-500 uppercase tracking-widest mt-1 truncate">{{ $obra->nome }}</p>
            </div>

            <div class="relative shrink-0" x-data="{ open: false }">
                <button
                    type="button"
                    @click="open = !open"
                    class="w-10 h-10 flex items-center justify-center bg-white/5 hover:bg-white/10 border border-white/10 rounded-2xl text-slate-300 transition-all active:scale-95"
                    aria-label="Novo lançamento"
                >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                </button>

                    <div
                        x-show="open"
                        x-transition
                        @click.outside="open = false"
                        class="absolute right-0 top-14 w-52 bg-slate-800/95 backdrop-blur-xl border border-white/10 rounded-2xl shadow-2xl overflow-hidden z-50"
                        style="display: none;"
                    >
                        @if($proposta)
                        <button
                            type="button"
                            @click="$dispatch('open-recebida-modal'); open = false"
                            class="w-full flex items-center gap-3 px-4 py-3.5 text-left text-xs font-bold text-slate-200 hover:bg-white/5 transition-colors border-b border-white/5"
                        >
                            <span class="w-8 h-8 rounded-lg bg-white/5 flex items-center justify-center text-slate-400">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 11l5-5m0 0l5 5m-5-5v12"></path></svg>
                            </span>
                            Conta recebida
                        </button>
                        @else
                        <p class="px-4 py-3 text-[10px] text-slate-500 border-b border-white/5">Proposta necessária para recebimentos</p>
                        @endif
                        <button
                            type="button"
                            @click="$dispatch('open-despesa-modal'); open = false"
                            class="w-full flex items-center gap-3 px-4 py-3.5 text-left text-xs font-bold text-slate-200 hover:bg-white/5 transition-colors"
                        >
                            <span class="w-8 h-8 rounded-lg bg-white/5 flex items-center justify-center text-slate-400">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 13l-5 5m0 0l-5-5m5 5V6"></path></svg>
                            </span>
                            Conta paga
                        </button>
                    </div>
            </div>
        </div>
    </x-slot>

    @php
        $openRecebida = $errors->hasAny(['valor_pago', 'data_pagamento', 'proposta_id', 'observacao', 'comprovante', 'forma_pagamento']);
        $openDespesa = $errors->hasAny(['valor', 'data', 'descricao', 'fornecedor', 'categoria', 'status', 'forma_pagamento', 'empreiteira_id', 'observacao', 'comprovante', 'comprovantes']);
    @endphp

    <div
        class="max-w-5xl mx-auto space-y-4 pb-24 px-0 sm:px-4"
        x-data="{
            showRecebidaModal: {{ $openRecebida ? 'true' : 'false' }},
            showDespesaModal: {{ $openDespesa ? 'true' : 'false' }},
            showKpisModal: false
        }"
        @open-recebida-modal.window="showRecebidaModal = true"
        @open-despesa-modal.window="showDespesaModal = true"
        @open-kpis-modal.window="showKpisModal = true"
    >
        @if(session('success'))
            <div class="bg-green-500/10 border border-green-500/20 rounded-2xl px-4 py-3 text-green-400 text-xs font-bold uppercase tracking-widest">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="bg-rose-500/10 border border-rose-500/20 rounded-2xl px-4 py-3 text-rose-400 text-xs font-bold uppercase tracking-widest">
                {{ session('error') }}
            </div>
        @endif

        @if($errors->any())
            <div class="bg-rose-500/10 border border-rose-500/20 rounded-2xl px-4 py-3 text-rose-400 text-xs space-y-1">
                @foreach($errors->all() as $error)
                    <p>{{ $error }}</p>
                @endforeach
            </div>
        @endif

        @include('financeiro._filtros')

        @include('financeiro._saldo-resumo')

        <!-- Lista -->
        <div class="space-y-3">
            <div class="flex items-center justify-between gap-2 px-1">
                <h3 class="text-[10px] font-black text-slate-500 uppercase tracking-widest">
                    Lançamentos
                    @if($kpis['qtd_lancamentos'] > 0)
                        <span class="text-slate-600 font-bold">· {{ $kpis['qtd_lancamentos'] }}</span>
                    @endif
                </h3>
            </div>

            <div class="grid grid-cols-1 gap-2.5">
                @forelse($lancamentos as $lancamento)
                    @include('financeiro._lancamento-card', ['lancamento' => $lancamento])
                @empty
                    <div class="py-16 text-center">
                        <div class="w-12 h-12 mx-auto mb-4 rounded-2xl bg-white/[0.03] border border-white/[0.06] flex items-center justify-center text-slate-600">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
                        </div>
                        <p class="text-slate-500 text-[10px] font-black uppercase tracking-widest">
                            @if($kpis['filtro_ativo'])
                                Nenhum resultado
                            @else
                                Nenhum lançamento
                            @endif
                        </p>
                        <p class="text-slate-600 text-[10px] mt-2">
                            @if($kpis['filtro_ativo'])
                                <a href="{{ route('financeiro.index') }}" class="text-slate-400 underline">Limpar filtros</a>
                            @else
                                Toque no <span class="text-slate-400">+</span> para registrar
                            @endif
                        </p>
                    </div>
                @endforelse
            </div>
        </div>

        @if(!$proposta)
            <div class="bg-amber-500/5 border border-amber-500/10 rounded-2xl px-4 py-3 text-xs">
                <p class="font-bold text-amber-500/80 text-[10px] uppercase tracking-widest mb-0.5">Sem proposta</p>
                <p class="text-slate-500 text-[10px]">Recebimentos exigem proposta. Despesas podem ser registradas normalmente.</p>
            </div>
        @endif

        @include('financeiro._modal-kpis')

        @if($proposta)
            @include('financeiro._modal-recebida')
        @endif
        @include('financeiro._modal-despesa')
    </div>
</x-app-layout>
