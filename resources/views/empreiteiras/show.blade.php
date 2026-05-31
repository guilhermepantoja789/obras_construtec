<x-app-layout>
    <x-slot name="header">
        <div class="flex items-start gap-3">
            <a href="{{ route('empreiteiras.index') }}" class="shrink-0 p-2 text-slate-400 hover:text-white bg-white/5 rounded-xl mt-0.5">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
            </a>
            <div class="min-w-0 flex-1">
                <p class="text-[10px] font-black text-orange-500 uppercase tracking-[0.2em] mb-1">Progresso de pagamento</p>
                <h2 class="font-black text-xl text-white leading-tight uppercase tracking-tight truncate">
                    {{ $empreiteira->nome }}
                </h2>
                @if($empreiteira->servico)
                    <p class="text-[10px] font-bold text-slate-500 uppercase tracking-widest mt-1 truncate">{{ $empreiteira->servico }}</p>
                @endif
            </div>
            <button
                type="button"
                @click="$dispatch('open-modal', 'edit-empreiteira-modal')"
                class="shrink-0 p-2 text-slate-400 hover:text-white bg-white/5 rounded-xl"
                aria-label="Editar empreiteira"
            >
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
            </button>
        </div>
    </x-slot>

    @php
        $valorPago = $empreiteira->valor_pago;
        $valorPendente = $empreiteira->valor_pendente;
        $valorAcordado = (float) $empreiteira->valor_acordado;
        $percentual = $empreiteira->percentual_pago;
        $saldo = $empreiteira->saldo_restante;
        $concluido = $empreiteira->status_pagamento === 'concluido';
    @endphp

    <div class="max-w-7xl mx-auto px-4 pb-28 sm:pb-8 mt-4 space-y-4">
        {{-- Card principal de progresso --}}
        <div class="bg-gradient-to-br from-orange-500/10 to-amber-500/5 border border-orange-500/20 rounded-3xl p-6">
            <div class="flex flex-col sm:flex-row sm:items-center gap-6">
                <div class="relative w-32 h-32 mx-auto sm:mx-0 shrink-0">
                    <svg class="w-32 h-32 -rotate-90" viewBox="0 0 120 120">
                        <circle cx="60" cy="60" r="52" fill="none" stroke="currentColor" stroke-width="8" class="text-slate-800"/>
                        <circle
                            cx="60" cy="60" r="52" fill="none"
                            stroke="currentColor" stroke-width="8"
                            stroke-linecap="round"
                            class="{{ $concluido ? 'text-green-500' : 'text-orange-500' }}"
                            stroke-dasharray="{{ 2 * 3.14159 * 52 }}"
                            stroke-dashoffset="{{ 2 * 3.14159 * 52 * (1 - $percentual / 100) }}"
                        />
                    </svg>
                    <div class="absolute inset-0 flex flex-col items-center justify-center">
                        <span class="text-2xl font-black text-white">{{ $percentual }}%</span>
                        <span class="text-[8px] font-bold text-slate-500 uppercase tracking-widest">pago</span>
                    </div>
                </div>

                <div class="flex-1 space-y-4">
                    <div class="grid grid-cols-2 gap-3">
                        <div class="bg-slate-900/50 rounded-2xl p-4 border border-white/5">
                            <p class="text-[8px] font-bold text-slate-500 uppercase tracking-widest mb-1">Pago</p>
                            <p class="text-white font-black text-lg">R$ {{ number_format($valorPago, 2, ',', '.') }}</p>
                        </div>
                        <div class="bg-slate-900/50 rounded-2xl p-4 border border-white/5">
                            <p class="text-[8px] font-bold text-slate-500 uppercase tracking-widest mb-1">Acordado</p>
                            <p class="text-slate-300 font-black text-lg">R$ {{ number_format($valorAcordado, 2, ',', '.') }}</p>
                        </div>
                        <div class="bg-slate-900/50 rounded-2xl p-4 border border-white/5">
                            <p class="text-[8px] font-bold text-slate-500 uppercase tracking-widest mb-1">Saldo</p>
                            <p class="font-black text-lg {{ $concluido ? 'text-green-400' : 'text-orange-400' }}">
                                R$ {{ number_format($saldo, 2, ',', '.') }}
                            </p>
                        </div>
                        <div class="bg-slate-900/50 rounded-2xl p-4 border border-white/5">
                            <p class="text-[8px] font-bold text-slate-500 uppercase tracking-widest mb-1">Pendente</p>
                            <p class="text-amber-400 font-black text-lg">R$ {{ number_format($valorPendente, 2, ',', '.') }}</p>
                        </div>
                    </div>

                    @if($concluido)
                        <div class="flex items-center gap-2 px-4 py-3 bg-green-500/10 border border-green-500/20 rounded-2xl">
                            <svg class="w-5 h-5 text-green-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                            <p class="text-[10px] font-bold text-green-400 uppercase tracking-widest">Valor acordado quitado</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Detalhes --}}
        @if($empreiteira->telefone || $empreiteira->observacao)
            <div class="bg-white/5 border border-white/10 rounded-3xl p-5 space-y-3">
                @if($empreiteira->telefone)
                    <div>
                        <p class="text-[8px] font-bold text-slate-500 uppercase tracking-widest mb-1">Telefone</p>
                        <p class="text-white text-sm font-bold">{{ $empreiteira->telefone }}</p>
                    </div>
                @endif
                @if($empreiteira->observacao)
                    <div>
                        <p class="text-[8px] font-bold text-slate-500 uppercase tracking-widest mb-1">Observações</p>
                        <p class="text-slate-300 text-sm">{{ $empreiteira->observacao }}</p>
                    </div>
                @endif
            </div>
        @endif

        {{-- Lista de pagamentos --}}
        <div>
            <div class="flex items-center justify-between mb-3 px-1">
                <h3 class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">Pagamentos vinculados</h3>
                <a href="{{ route('financeiro.index') }}" class="text-[9px] font-bold text-orange-400 uppercase tracking-widest hover:text-orange-300">
                    + Nova despesa
                </a>
            </div>

            @if($empreiteira->despesas->isEmpty())
                <div class="bg-white/5 border border-white/10 border-dashed rounded-3xl p-8 text-center">
                    <p class="text-slate-500 text-xs">Nenhum pagamento vinculado ainda.</p>
                    <p class="text-slate-600 text-[10px] mt-2">Ao registrar uma despesa no financeiro, selecione esta empreiteira.</p>
                </div>
            @else
                <div class="space-y-2">
                    @foreach($empreiteira->despesas as $despesa)
                        <div class="bg-white/5 border border-white/10 rounded-2xl p-4 flex items-center gap-4">
                            <div class="w-10 h-10 rounded-xl flex items-center justify-center shrink-0 {{ $despesa->status === 'pago' ? 'bg-green-500/10 text-green-400' : 'bg-amber-500/10 text-amber-400' }}">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 13l-5 5m0 0l-5-5m5 5V6"></path></svg>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-white font-bold text-sm truncate">{{ $despesa->descricao }}</p>
                                <p class="text-[9px] text-slate-500 uppercase tracking-widest mt-0.5">
                                    {{ $despesa->data->format('d/m/Y') }}
                                    · {{ $despesa->status === 'pago' ? 'Pago' : 'Pendente' }}
                                </p>
                            </div>
                            <div class="text-right shrink-0">
                                <p class="text-white font-black text-sm">R$ {{ number_format($despesa->valor, 2, ',', '.') }}</p>
                                @if($despesa->comprovante_path)
                                    <a href="{{ route('despesas.comprovante', $despesa) }}" class="text-[8px] font-bold text-orange-400 uppercase tracking-widest hover:text-orange-300">Comprovante</a>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        <form action="{{ route('empreiteiras.destroy', $empreiteira) }}" method="POST" onsubmit="return confirm('Remover esta empreiteira? Os pagamentos vinculados serão mantidos, mas sem vínculo.')">
            @csrf
            @method('DELETE')
            <button type="submit" class="w-full py-3 text-rose-500/80 hover:text-rose-400 text-[10px] font-black uppercase tracking-widest transition-colors">
                Remover empreiteira
            </button>
        </form>
    </div>

    <x-slot name="modals">
        <x-modal name="edit-empreiteira-modal">
            <div class="bg-slate-900 min-h-[300px]">
                <div class="p-6 sm:p-8">
                    <div class="flex items-start justify-between mb-6 gap-4">
                        <div>
                            <h2 class="text-lg font-black text-white uppercase tracking-tight">Editar Empreiteira</h2>
                            <p class="text-[9px] font-bold text-slate-500 uppercase tracking-widest mt-0.5">{{ $empreiteira->nome }}</p>
                        </div>
                        <button @click="$dispatch('close-modal', 'edit-empreiteira-modal')" class="p-2 text-slate-500 hover:text-white bg-white/5 rounded-xl shrink-0">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                        </button>
                    </div>

                    <form action="{{ route('empreiteiras.update', $empreiteira) }}" method="POST" class="space-y-4 pb-2">
                        @csrf
                        @method('PUT')

                        <div>
                            <label class="text-[10px] font-bold text-slate-500 uppercase mb-1 block">Nome</label>
                            <input type="text" name="nome" required value="{{ old('nome', $empreiteira->nome) }}" class="w-full bg-slate-800 border-white/10 rounded-xl text-white text-sm py-3.5 px-4">
                        </div>

                        <div>
                            <label class="text-[10px] font-bold text-slate-500 uppercase mb-1 block">Valor acordado (R$)</label>
                            <input type="number" step="0.01" min="0.01" name="valor_acordado" required value="{{ old('valor_acordado', $empreiteira->valor_acordado) }}" class="w-full bg-slate-800 border-white/10 rounded-xl text-white text-sm py-3.5 px-4">
                        </div>

                        <div>
                            <label class="text-[10px] font-bold text-slate-500 uppercase mb-1 block">Serviço / Escopo</label>
                            <input type="text" name="servico" value="{{ old('servico', $empreiteira->servico) }}" class="w-full bg-slate-800 border-white/10 rounded-xl text-white text-sm py-3.5 px-4">
                        </div>

                        <div>
                            <label class="text-[10px] font-bold text-slate-500 uppercase mb-1 block">Telefone</label>
                            <input type="text" name="telefone" value="{{ old('telefone', $empreiteira->telefone) }}" class="w-full bg-slate-800 border-white/10 rounded-xl text-white text-sm py-3.5 px-4">
                        </div>

                        <div>
                            <label class="text-[10px] font-bold text-slate-500 uppercase mb-1 block">Observações</label>
                            <textarea name="observacao" rows="2" class="w-full bg-slate-800 border-white/10 rounded-xl text-white text-sm py-3 px-4">{{ old('observacao', $empreiteira->observacao) }}</textarea>
                        </div>

                        <button type="submit" class="w-full py-4 bg-orange-500 hover:bg-orange-400 text-white font-black rounded-xl transition-all uppercase tracking-widest text-xs">
                            Salvar alterações
                        </button>
                    </form>
                </div>
            </div>
        </x-modal>
    </x-slot>
</x-app-layout>
