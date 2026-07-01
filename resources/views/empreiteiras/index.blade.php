<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-start gap-4">
            <div class="min-w-0">
                <p class="text-[10px] font-black text-orange-500 uppercase tracking-[0.2em] mb-1">Subcontratos</p>
                <h2 class="font-black text-xl text-white leading-tight uppercase tracking-tight truncate">
                    Empreiteiras
                </h2>
                <p class="text-[10px] font-bold text-slate-500 uppercase tracking-widest mt-1 truncate">{{ $obra->nome }}</p>
            </div>

            <button
                type="button"
                @click="$dispatch('open-modal', 'add-empreiteira-modal')"
                class="shrink-0 w-10 h-10 flex items-center justify-center bg-orange-500 hover:bg-orange-400 text-white rounded-2xl transition-all active:scale-95 shadow-lg shadow-orange-500/20"
                aria-label="Nova empreiteira"
            >
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
            </button>
        </div>
    </x-slot>

    <div class="max-w-7xl mx-auto px-4 pb-28 sm:pb-8 mt-4">
        @if($empreiteiras->isEmpty())
            <div class="flex flex-col items-center justify-center py-20 bg-white/5 border border-white/10 rounded-3xl border-dashed">
                <div class="w-16 h-16 bg-orange-500/10 rounded-full flex items-center justify-center text-orange-500 mb-4 border border-orange-500/20">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                </div>
                <h3 class="text-white font-bold mb-1">Nenhuma empreiteira</h3>
                <p class="text-slate-500 text-xs text-center px-8 mb-6">Cadastre empreiteiras com valor acordado e acompanhe os pagamentos vinculados às despesas.</p>
                <button
                    type="button"
                    @click="$dispatch('open-modal', 'add-empreiteira-modal')"
                    class="px-5 py-3 bg-orange-500 hover:bg-orange-400 text-white rounded-xl text-[10px] font-black uppercase tracking-widest transition-all"
                >
                    Cadastrar empreiteira
                </button>
            </div>
        @else
            <div class="space-y-3">
                @foreach($empreiteiras as $empreiteira)
                    @php
                        $valorPago = (float) ($empreiteira->valor_pago_sum ?? 0);
                        $valorAcordado = (float) $empreiteira->valor_acordado;
                        $percentual = $valorAcordado > 0 ? min(100, round(($valorPago / $valorAcordado) * 100, 1)) : 0;
                        $saldo = max(0, $valorAcordado - $valorPago);
                        $concluido = $valorPago >= $valorAcordado;
                    @endphp
                    <a
                        href="{{ route('empreiteiras.show', $empreiteira) }}"
                        class="block bg-white/5 backdrop-blur-xl border border-white/10 rounded-3xl p-5 hover:bg-white/10 transition-all active:scale-[0.99]"
                    >
                        <div class="flex items-start justify-between gap-3 mb-4">
                            <div class="min-w-0">
                                <h3 class="text-white font-black text-sm uppercase leading-tight truncate">{{ $empreiteira->nome }}</h3>
                                @if($empreiteira->servico)
                                    <p class="text-[10px] text-slate-500 mt-1 truncate">{{ $empreiteira->servico }}</p>
                                @endif
                            </div>
                            <span class="shrink-0 px-2.5 py-1 rounded-lg text-[9px] font-black uppercase tracking-widest {{ $concluido ? 'bg-green-500/10 text-green-400 border border-green-500/20' : ($valorPago > 0 ? 'bg-orange-500/10 text-orange-400 border border-orange-500/20' : 'bg-slate-500/10 text-slate-400 border border-slate-500/20') }}">
                                {{ $concluido ? 'Quitado' : ($valorPago > 0 ? 'Em pagamento' : 'Não iniciado') }}
                            </span>
                        </div>

                        <div class="mb-3">
                            <div class="flex justify-between items-end mb-2">
                                <div>
                                    <p class="text-[8px] font-bold text-slate-500 uppercase tracking-widest">Pago</p>
                                    <p class="text-white font-black text-lg leading-none">R$ {{ number_format($valorPago, 2, ',', '.') }}</p>
                                </div>
                                <div class="text-right">
                                    <p class="text-[8px] font-bold text-slate-500 uppercase tracking-widest">Acordado</p>
                                    <p class="text-slate-300 font-bold text-sm">R$ {{ number_format($valorAcordado, 2, ',', '.') }}</p>
                                </div>
                            </div>
                            <div class="h-2.5 bg-slate-800 rounded-full overflow-hidden">
                                <div
                                    class="h-full rounded-full transition-all duration-500 {{ $concluido ? 'bg-green-500' : 'bg-gradient-to-r from-orange-500 to-amber-400' }}"
                                    style="width: {{ $percentual }}%"
                                ></div>
                            </div>
                            <div class="flex justify-between mt-2">
                                <span class="text-[9px] font-bold text-slate-500 uppercase">{{ $percentual }}% pago</span>
                                @if(!$concluido)
                                    <span class="text-[9px] font-bold text-orange-400 uppercase">Faltam R$ {{ number_format($saldo, 2, ',', '.') }}</span>
                                @endif
                            </div>
                        </div>

                        <div class="flex items-center justify-between pt-3 border-t border-white/5">
                            <span class="text-[9px] font-bold text-slate-500 uppercase tracking-widest">Ver pagamentos</span>
                            <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                        </div>
                    </a>
                @endforeach
            </div>
        @endif
    </div>

    <x-slot name="modals">
        <x-modal name="add-empreiteira-modal" :show="$errors->hasAny(['nome', 'valor_acordado', 'servico', 'telefone', 'observacao'])">
            <div class="bg-slate-900 min-h-[300px]">
                <div class="p-6 sm:p-8">
                    <div class="flex items-start justify-between mb-6 gap-4">
                        <div>
                            <h2 class="text-lg font-black text-white uppercase tracking-tight">Nova Empreiteira</h2>
                            <p class="text-[9px] font-bold text-slate-500 uppercase tracking-widest mt-0.5">Valor acordado para pagamento</p>
                        </div>
                        <button @click="$dispatch('close-modal', 'add-empreiteira-modal')" class="p-2 text-slate-500 hover:text-white bg-white/5 rounded-xl shrink-0">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                        </button>
                    </div>

                    <form action="{{ route('empreiteiras.store') }}" method="POST" class="space-y-4 pb-2">
                        @csrf

                        <div>
                            <label class="text-[10px] font-bold text-slate-500 uppercase mb-1 block">Nome</label>
                            <input type="text" name="nome" required value="{{ old('nome') }}" placeholder="Ex: João Pedreiro" class="w-full bg-slate-800 border-white/10 rounded-xl text-white text-sm py-3.5 px-4">
                        </div>

                        <div>
                            <label class="text-[10px] font-bold text-slate-500 uppercase mb-1 block">Valor acordado (R$)</label>
                            <input type="number" step="0.01" min="0.01" name="valor_acordado" required value="{{ old('valor_acordado') }}" class="w-full bg-slate-800 border-white/10 rounded-xl text-white text-sm py-3.5 px-4">
                        </div>

                        <div>
                            <label class="text-[10px] font-bold text-slate-500 uppercase mb-1 block">Serviço / Escopo</label>
                            <input type="text" name="servico" value="{{ old('servico') }}" placeholder="Ex: Alvenaria, reboco..." class="w-full bg-slate-800 border-white/10 rounded-xl text-white text-sm py-3.5 px-4">
                        </div>

                        <div>
                            <label class="text-[10px] font-bold text-slate-500 uppercase mb-1 block">Telefone</label>
                            <input type="text" name="telefone" value="{{ old('telefone') }}" placeholder="(00) 00000-0000" class="w-full bg-slate-800 border-white/10 rounded-xl text-white text-sm py-3.5 px-4">
                        </div>

                        <div>
                            <label class="text-[10px] font-bold text-slate-500 uppercase mb-1 block">Observações</label>
                            <textarea name="observacao" rows="2" class="w-full bg-slate-800 border-white/10 rounded-xl text-white text-sm py-3 px-4">{{ old('observacao') }}</textarea>
                        </div>

                        <button type="submit" class="w-full py-4 bg-orange-500 hover:bg-orange-400 text-white font-black rounded-xl transition-all uppercase tracking-widest text-xs">
                            Cadastrar
                        </button>
                    </form>
                </div>
            </div>
        </x-modal>
    </x-slot>
</x-app-layout>
