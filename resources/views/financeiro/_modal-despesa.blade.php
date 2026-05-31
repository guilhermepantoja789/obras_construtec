<div
    x-show="showDespesaModal"
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
    <div class="fixed inset-0 bg-slate-950/80 backdrop-blur-md" @click="showDespesaModal = false"></div>
    <div class="relative w-full sm:max-w-lg bg-slate-900 border border-white/10 rounded-t-3xl sm:rounded-3xl shadow-2xl max-h-[92vh] overflow-y-auto safe-area-bottom">
        <div class="p-6">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h3 class="text-white font-black uppercase tracking-widest text-sm">Nova Conta Paga</h3>
                    <p class="text-[10px] font-bold text-slate-500 uppercase tracking-widest mt-0.5">Despesa da obra</p>
                </div>
                <button type="button" @click="showDespesaModal = false" class="p-2 text-slate-500 hover:text-white bg-white/5 rounded-xl">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>

            <form action="{{ route('despesas.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                @csrf

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="text-[10px] font-bold text-slate-500 uppercase mb-1 block">Valor (R$)</label>
                        <input type="number" step="0.01" name="valor" required value="{{ old('valor') }}" class="w-full bg-slate-800 border-white/10 rounded-xl text-white text-sm focus:border-rose-500 py-3.5 px-4">
                    </div>
                    <div>
                        <label class="text-[10px] font-bold text-slate-500 uppercase mb-1 block">Data</label>
                        <input type="date" name="data" required value="{{ old('data', date('Y-m-d')) }}" class="w-full bg-slate-800 border-white/10 rounded-xl text-white text-sm py-3.5 px-4">
                    </div>
                </div>

                <div>
                    <label class="text-[10px] font-bold text-slate-500 uppercase mb-1 block">Descrição</label>
                    <input type="text" name="descricao" required placeholder="Ex: Concreto, Pedreiro..." value="{{ old('descricao') }}" class="w-full bg-slate-800 border-white/10 rounded-xl text-white text-sm py-3.5 px-4">
                </div>

                <div>
                    <label class="text-[10px] font-bold text-slate-500 uppercase mb-1 block">Fornecedor / Beneficiário</label>
                    <input type="text" name="fornecedor" placeholder="Quem recebeu" value="{{ old('fornecedor') }}" class="w-full bg-slate-800 border-white/10 rounded-xl text-white text-sm py-3.5 px-4">
                </div>

                @if(isset($empreiteiras) && $empreiteiras->isNotEmpty())
                <div>
                    <label class="text-[10px] font-bold text-slate-500 uppercase mb-1 block">Empreiteira (opcional)</label>
                    <select name="empreiteira_id" class="w-full bg-slate-800 border-white/10 rounded-xl text-white text-sm py-3.5 px-4">
                        <option value="">Nenhuma — despesa avulsa</option>
                        @foreach($empreiteiras as $emp)
                            <option value="{{ $emp->id }}" @selected(old('empreiteira_id') == $emp->id)>
                                {{ $emp->nome }} — R$ {{ number_format($emp->valor_acordado, 2, ',', '.') }} acordado
                            </option>
                        @endforeach
                    </select>
                    <p class="text-[9px] text-slate-600 mt-1.5">Pagamentos vinculados somam no progresso da empreiteira.</p>
                </div>
                @endif

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="text-[10px] font-bold text-slate-500 uppercase mb-1 block">Categoria</label>
                        <select name="categoria" class="w-full bg-slate-800 border-white/10 rounded-xl text-white text-sm py-3.5 px-4">
                            <option value="">Selecione...</option>
                            <option value="material" @selected(old('categoria') === 'material')>Material</option>
                            <option value="mao_de_obra" @selected(old('categoria') === 'mao_de_obra')>Mão de obra</option>
                            <option value="equipamento" @selected(old('categoria') === 'equipamento')>Equipamento</option>
                            <option value="servico" @selected(old('categoria') === 'servico')>Serviço</option>
                            <option value="outros" @selected(old('categoria') === 'outros')>Outros</option>
                        </select>
                    </div>
                    <div>
                        <label class="text-[10px] font-bold text-slate-500 uppercase mb-1 block">Status</label>
                        <select name="status" required class="w-full bg-slate-800 border-white/10 rounded-xl text-white text-sm py-3.5 px-4">
                            <option value="pago" @selected(old('status', 'pago') === 'pago')>Pago</option>
                            <option value="pendente" @selected(old('status') === 'pendente')>Pendente</option>
                        </select>
                    </div>
                </div>

                <div>
                    <label class="text-[10px] font-bold text-slate-500 uppercase mb-1 block">Forma de Pagamento</label>
                    <select name="forma_pagamento" class="w-full bg-slate-800 border-white/10 rounded-xl text-white text-sm py-3.5 px-4">
                        <option value="">Selecione...</option>
                        <option value="pix" @selected(old('forma_pagamento') === 'pix')>PIX</option>
                        <option value="boleto" @selected(old('forma_pagamento') === 'boleto')>Boleto</option>
                        <option value="dinheiro" @selected(old('forma_pagamento') === 'dinheiro')>Dinheiro</option>
                        <option value="transferencia" @selected(old('forma_pagamento') === 'transferencia')>Transferência</option>
                        <option value="cartao" @selected(old('forma_pagamento') === 'cartao')>Cartão</option>
                    </select>
                </div>

                <div>
                    <label class="text-[10px] font-bold text-slate-500 uppercase mb-1 block">Observações</label>
                    <textarea name="observacao" rows="2" class="w-full bg-slate-800 border-white/10 rounded-xl text-white text-sm py-3 px-4">{{ old('observacao') }}</textarea>
                </div>

                <div x-data="{ preview: null }">
                    <label class="text-[10px] font-bold text-slate-500 uppercase mb-1 block">Comprovante</label>
                    <label class="relative flex flex-col items-center justify-center w-full min-h-[120px] border-2 border-dashed border-white/10 rounded-2xl cursor-pointer hover:border-rose-500/40 transition-all overflow-hidden">
                        <template x-if="preview">
                            <img :src="preview" class="absolute inset-0 w-full h-full object-cover opacity-60">
                        </template>
                        <div class="relative z-10 flex flex-col items-center py-4 px-4 text-center">
                            <svg class="w-8 h-8 text-slate-500 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Toque para anexar foto ou PDF</span>
                        </div>
                        <input type="file" name="comprovante" accept="image/*,application/pdf" class="absolute inset-0 opacity-0 cursor-pointer"
                            @change="const f = $event.target.files[0]; if (f && f.type.startsWith('image/')) { const r = new FileReader(); r.onload = e => preview = e.target.result; r.readAsDataURL(f); } else { preview = null; }">
                    </label>
                </div>

                <button type="submit" class="w-full py-4 bg-white/10 hover:bg-white/15 text-white font-black rounded-xl transition-all uppercase tracking-widest text-xs">
                    Registrar Despesa
                </button>
            </form>
        </div>
    </div>
</div>
