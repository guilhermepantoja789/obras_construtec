<div
    x-show="showRecebidaModal"
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
    <div class="fixed inset-0 bg-slate-950/80 backdrop-blur-md" @click="showRecebidaModal = false"></div>
    <div class="relative w-full sm:max-w-lg bg-slate-900 border border-white/10 rounded-t-3xl sm:rounded-3xl shadow-2xl max-h-[92vh] overflow-y-auto safe-area-bottom">
        <div class="p-6">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h3 class="text-white font-black uppercase tracking-widest text-sm">Nova Conta Recebida</h3>
                    <p class="text-[10px] font-bold text-slate-500 uppercase tracking-widest mt-0.5">Recebimento do cliente</p>
                </div>
                <button type="button" @click="showRecebidaModal = false" class="p-2 text-slate-500 hover:text-white bg-white/5 rounded-xl">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>

            <form action="{{ route('pagamentos.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                @csrf
                <input type="hidden" name="proposta_id" value="{{ $proposta->id ?? '' }}">

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="text-[10px] font-bold text-slate-500 uppercase mb-1 block">Valor (R$)</label>
                        <input type="number" step="0.01" name="valor_pago" required value="{{ old('valor_pago') }}" class="w-full bg-slate-800 border-white/10 rounded-xl text-white text-sm focus:border-green-500 py-3.5 px-4">
                    </div>
                    <div>
                        <label class="text-[10px] font-bold text-slate-500 uppercase mb-1 block">Data</label>
                        <input type="date" name="data_pagamento" required value="{{ old('data_pagamento', date('Y-m-d')) }}" class="w-full bg-slate-800 border-white/10 rounded-xl text-white text-sm py-3.5 px-4">
                    </div>
                </div>

                <div>
                    <label class="text-[10px] font-bold text-slate-500 uppercase mb-1 block">Descrição</label>
                    <input type="text" name="observacao" placeholder="Ex: Adiantamento, Parcela 01..." value="{{ old('observacao') }}" class="w-full bg-slate-800 border-white/10 rounded-xl text-white text-sm py-3.5 px-4">
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

                <div x-data="{ preview: null }">
                    <label class="text-[10px] font-bold text-slate-500 uppercase mb-1 block">Comprovante</label>
                    <label class="relative flex flex-col items-center justify-center w-full min-h-[120px] border-2 border-dashed border-white/10 rounded-2xl cursor-pointer hover:border-green-500/40 transition-all overflow-hidden">
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
                    Confirmar Recebimento
                </button>
            </form>
        </div>
    </div>
</div>
