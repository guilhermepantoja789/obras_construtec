<form action="{{ $formAction }}" method="POST" class="pb-28" @submit="if(items.length === 0) { alert('Adicione ao menos um item.'); $event.preventDefault(); }">
    @csrf
    @if(($formMethod ?? 'POST') !== 'POST')
        @method($formMethod)
    @endif

    @if($errors->any())
        <div class="bg-rose-500/10 border border-rose-500/20 rounded-2xl p-4 mb-4">
            <p class="text-xs font-black text-rose-500 uppercase mb-2">Corrija os erros abaixo</p>
            <ul class="text-xs text-rose-400/90 space-y-1 list-disc list-inside">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- TAB: DADOS --}}
    <div x-show="activeTab === 'dados'" class="space-y-4">
        <div class="bg-white/5 border border-white/10 rounded-2xl p-4 space-y-4">
            <div>
                <label class="text-[10px] font-black text-slate-500 uppercase tracking-widest mb-1.5 block">Título</label>
                <input type="text" name="titulo" required value="{{ old('titulo', $proposta->titulo ?? '') }}"
                    placeholder="Ex: Reforma Geral - Fase 01"
                    class="w-full bg-slate-900/60 border-white/10 rounded-xl text-white text-base py-3.5 px-4 focus:border-amber-500">
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="text-[10px] font-black text-slate-500 uppercase tracking-widest mb-1.5 block">Data</label>
                    <input type="date" name="data_proposta" required
                        value="{{ old('data_proposta', isset($proposta) ? $proposta->data_proposta->format('Y-m-d') : date('Y-m-d')) }}"
                        class="w-full bg-slate-900/60 border-white/10 rounded-xl text-white text-sm py-3.5 px-3 focus:border-amber-500">
                </div>
                <div>
                    <label class="text-[10px] font-black text-slate-500 uppercase tracking-widest mb-1.5 block">Status</label>
                    <select name="status" class="w-full bg-slate-900/60 border-white/10 rounded-xl text-white text-sm py-3.5 px-3 focus:border-amber-500">
                        @foreach(['rascunho' => 'Rascunho', 'enviada' => 'Enviada', 'aceita' => 'Aceita', 'recusada' => 'Recusada'] as $val => $label)
                            <option value="{{ $val }}" @selected(old('status', $proposta->status ?? 'rascunho') === $val)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div>
                <label class="text-[10px] font-black text-slate-500 uppercase tracking-widest mb-1.5 block">Escopo</label>
                <textarea name="escopo" rows="3" placeholder="Descrição do escopo..."
                    class="w-full bg-slate-900/60 border-white/10 rounded-xl text-white text-sm py-3 px-4 focus:border-amber-500">{{ old('escopo', $proposta->escopo ?? '') }}</textarea>
            </div>
            @if(isset($proposta) && $proposta->status === 'aceita')
                <p class="text-[10px] text-amber-500/80 font-bold uppercase">Proposta aceita — ao salvar, o cronograma será sincronizado automaticamente.</p>
            @endif
        </div>
    </div>

    {{-- TAB: ITENS --}}
    <div x-show="activeTab === 'itens'" class="space-y-3">
        <div class="flex gap-2 overflow-x-auto pb-1 -mx-1 px-1">
            <input type="search" x-model="searchQuery" placeholder="Buscar item..."
                class="flex-1 min-w-[140px] bg-slate-900/60 border-white/10 rounded-xl text-white text-sm py-2.5 px-3">
            <button type="button" @click="showOnlyEtapas = !showOnlyEtapas"
                class="shrink-0 px-3 py-2.5 rounded-xl text-[10px] font-black uppercase border transition-colors"
                :class="showOnlyEtapas ? 'bg-amber-500/20 border-amber-500/40 text-amber-400' : 'bg-white/5 border-white/10 text-slate-400'">
                Etapas
            </button>
            <button type="button" @click="groupByEtapa = !groupByEtapa"
                class="shrink-0 px-3 py-2.5 rounded-xl text-[10px] font-black uppercase border transition-colors"
                :class="groupByEtapa ? 'bg-blue-500/20 border-blue-500/40 text-blue-400' : 'bg-white/5 border-white/10 text-slate-400'">
                Agrupar
            </button>
            <button type="button" @click="$refs.fileInput.click()" :disabled="isLoading"
                class="shrink-0 px-3 py-2.5 rounded-xl text-[10px] font-black uppercase bg-emerald-500/10 border border-emerald-500/30 text-emerald-400">
                <span x-text="isLoading ? '…' : 'Excel'"></span>
            </button>
            <input type="file" x-ref="fileInput" @change="importExcel($event)" class="hidden" accept=".xlsx,.xls,.csv">
        </div>

        <div class="space-y-2">
            <template x-for="(item, index) in items" :key="'hidden-'+index">
                <div class="hidden">
                    <input type="hidden" :name="'items['+index+'][descricao]'" :value="item.descricao">
                    <input type="hidden" :name="'items['+index+'][unidade]'" :value="item.unidade || 'un'">
                    <input type="hidden" :name="'items['+index+'][quantidade]'" :value="item.quantidade">
                    <input type="hidden" :name="'items['+index+'][valor_unitario]'" :value="item.valor_unitario">
                    <input type="hidden" :name="'items['+index+'][ordem]'" :value="item.ordem">
                    <template x-if="item.is_etapa">
                        <input type="hidden" :name="'items['+index+'][is_etapa]'" value="1">
                    </template>
                </div>
            </template>

            <template x-for="(item, index) in items" :key="'row-'+index">
                <button type="button" x-show="itemVisible(item, index)" @click="openEditItem(index)"
                    class="w-full text-left bg-white/5 border border-white/10 rounded-2xl p-4 active:scale-[0.99] transition-transform"
                    :class="item.is_etapa ? 'border-l-4 border-l-amber-500/60' : ''"
                    :style="'margin-left:' + (groupByEtapa && !item.is_etapa ? Math.max(0, ordemDepth(item.ordem)-1) * 12 : 0) + 'px'">
                    <div class="flex items-start justify-between gap-3">
                        <div class="min-w-0 flex-1">
                            <div class="flex items-center gap-2 mb-1">
                                <span class="text-[10px] font-black text-slate-500" x-text="item.ordem || (index+1)"></span>
                                <span x-show="item.is_etapa" class="text-[8px] font-black uppercase px-1.5 py-0.5 rounded bg-amber-500/15 text-amber-400">Etapa</span>
                            </div>
                            <p class="text-sm font-bold text-white truncate" :class="item.is_etapa ? 'uppercase text-amber-500/90' : ''" x-text="item.descricao || 'Sem descrição'"></p>
                            <p class="text-[10px] text-slate-500 mt-1" x-text="item.quantidade + ' ' + (item.unidade||'un') + ' × R$ ' + formatMoney(item.valor_unitario)"></p>
                        </div>
                        <div class="text-right shrink-0">
                            <p class="text-sm font-black text-white">R$ <span x-text="formatMoney(itemSubtotal(item))"></span></p>
                            <p class="text-[9px] text-slate-600 uppercase mt-1">Toque p/ editar</p>
                        </div>
                    </div>
                </button>
            </template>

            <div x-show="filteredItems.length === 0" class="py-10 text-center border-2 border-dashed border-white/10 rounded-2xl">
                <p class="text-slate-500 text-xs font-bold uppercase">Nenhum item encontrado</p>
            </div>
        </div>

        <button type="button" @click="openNewItem()"
            class="w-full py-4 border-2 border-dashed border-amber-500/30 rounded-2xl text-amber-500 font-black text-xs uppercase tracking-widest active:scale-[0.99]">
            + Adicionar Item
        </button>
    </div>

    {{-- TAB: VALORES (encargos fixos) --}}
    <div x-show="activeTab === 'valores'" class="space-y-4">
        <div class="bg-white/5 border border-white/10 rounded-2xl p-4">
            <p class="text-[10px] font-black text-slate-500 uppercase tracking-widest mb-3">Subtotal dos itens</p>
            <p class="text-2xl font-black text-white">R$ <span x-text="formatMoney(getSubtotalItens())"></span></p>
        </div>

        <div class="space-y-2">
            <p class="text-[10px] font-black text-slate-500 uppercase tracking-widest px-1">Encargos padrão da obra</p>
            <template x-for="(encargo, idx) in encargos" :key="encargo.key">
                <div class="bg-white/5 border border-white/10 rounded-2xl p-4" :class="encargo.ativo ? 'border-amber-500/20' : ''">
                    <div class="flex items-center justify-between gap-3 mb-2">
                        <div class="min-w-0">
                            <p class="text-sm font-black text-white" x-text="encargo.label"></p>
                            <p class="text-[10px] text-slate-500 truncate" x-text="encargo.descricao"></p>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer shrink-0">
                            <input type="checkbox" :name="'encargos['+encargo.key+'][ativo]'" value="1" x-model="encargo.ativo" class="sr-only peer">
                            <div class="w-11 h-6 bg-slate-700 rounded-full peer peer-checked:bg-amber-500 transition-colors after:content-[''] after:absolute after:top-0.5 after:left-0.5 after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:after:translate-x-5"></div>
                        </label>
                    </div>
                    <div x-show="encargo.ativo" class="flex items-center gap-3">
                        <div class="flex-1">
                            <label class="text-[9px] font-black text-slate-600 uppercase">Percentual (%)</label>
                            <input type="number" step="0.01" min="0" max="100" :name="'encargos['+encargo.key+'][percent]'" x-model.number="encargo.percent"
                                class="w-full mt-1 bg-slate-900/60 border-white/10 rounded-xl text-white text-base py-3 px-3 text-center font-bold">
                        </div>
                        <div class="text-right shrink-0 pt-4">
                            <p class="text-[9px] text-slate-600 uppercase" x-text="encargo.subtrai ? 'Desconto' : 'Valor'"></p>
                            <p class="text-sm font-black" :class="encargo.subtrai ? 'text-rose-400' : 'text-emerald-400'">
                                <span x-text="encargo.subtrai ? '−' : '+'"></span>R$ <span x-text="formatMoney(getEncargoValor(encargo))"></span>
                            </p>
                        </div>
                    </div>
                </div>
            </template>
        </div>

        <div class="bg-amber-500/10 border border-amber-500/30 rounded-2xl p-5 flex justify-between items-center">
            <span class="text-[10px] font-black text-amber-500 uppercase tracking-widest">Total da Proposta</span>
            <span class="text-2xl font-black text-white">R$ <span x-text="formatMoney(getTotalComEncargos())"></span></span>
        </div>
    </div>

    {{-- Bottom sheet: editar item --}}
    <div x-show="showItemSheet" class="fixed inset-0 z-[120]" style="display:none;">
        <div class="absolute inset-0 bg-slate-950/80" @click="showItemSheet = false"></div>
        <div class="absolute bottom-0 left-0 right-0 bg-slate-900 border-t border-white/10 rounded-t-3xl p-5 pb-8 max-h-[85vh] overflow-y-auto" x-transition>
            <div class="w-10 h-1 bg-white/20 rounded-full mx-auto mb-4"></div>
            <h3 class="text-white font-black uppercase text-sm tracking-widest mb-4" x-text="editingIndex === null ? 'Novo Item' : 'Editar Item'"></h3>
            <template x-if="draftItem">
                <div class="space-y-4">
                    <label class="flex items-center gap-3 p-3 bg-white/5 rounded-xl">
                        <input type="checkbox" x-model="draftItem.is_etapa" class="rounded bg-slate-900 border-white/10 text-amber-500 w-5 h-5">
                        <span class="text-sm font-bold text-white">Marcar como etapa do cronograma</span>
                    </label>
                    <div class="grid grid-cols-3 gap-3">
                        <div class="col-span-1">
                            <label class="text-[9px] font-black text-slate-500 uppercase">Nº ordem</label>
                            <input type="text" x-model="draftItem.ordem" class="w-full mt-1 bg-slate-800 border-white/10 rounded-xl text-white text-sm py-3 px-2 text-center">
                        </div>
                        <div class="col-span-2">
                            <label class="text-[9px] font-black text-slate-500 uppercase">Unidade</label>
                            <input type="text" x-model="draftItem.unidade" class="w-full mt-1 bg-slate-800 border-white/10 rounded-xl text-white text-sm py-3 px-3 text-center">
                        </div>
                    </div>
                    <div>
                        <label class="text-[9px] font-black text-slate-500 uppercase">Descrição</label>
                        <input type="text" x-model="draftItem.descricao" required
                            class="w-full mt-1 bg-slate-800 border-white/10 rounded-xl text-white text-base py-3.5 px-4"
                            :class="draftItem.is_etapa ? 'uppercase font-bold' : ''" placeholder="Descrição do serviço...">
                    </div>
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="text-[9px] font-black text-slate-500 uppercase">Quantidade</label>
                            <input type="number" step="0.001" x-model="draftItem.quantidade" class="w-full mt-1 bg-slate-800 border-white/10 rounded-xl text-white text-lg py-3.5 px-3 text-center font-bold">
                        </div>
                        <div>
                            <label class="text-[9px] font-black text-slate-500 uppercase">Valor unit. (R$)</label>
                            <input type="number" step="0.01" x-model="draftItem.valor_unitario" class="w-full mt-1 bg-slate-800 border-white/10 rounded-xl text-white text-lg py-3.5 px-3 text-center font-bold">
                        </div>
                    </div>
                    <div class="flex justify-between items-center py-3 border-t border-white/10">
                        <span class="text-[10px] font-black text-slate-500 uppercase">Subtotal</span>
                        <span class="text-lg font-black text-white">R$ <span x-text="formatMoney(itemSubtotal(draftItem))"></span></span>
                    </div>
                    <div class="flex gap-3">
                        <button type="button" x-show="editingIndex !== null" @click="removeItemAt(editingIndex)"
                            class="px-4 py-4 rounded-xl bg-rose-500/10 text-rose-400 font-black text-xs uppercase">Excluir</button>
                        <button type="button" @click="saveDraftItem()"
                            class="flex-1 py-4 rounded-xl bg-amber-500 text-slate-900 font-black text-xs uppercase tracking-widest">Salvar Item</button>
                    </div>
                </div>
            </template>
        </div>
    </div>

    {{-- Barra inferior fixa --}}
    <div class="fixed bottom-0 left-0 right-0 z-[110] bg-slate-950/95 backdrop-blur-xl border-t border-white/10 px-3 pt-2 pb-[max(0.75rem,env(safe-area-inset-bottom))]">
        <div class="max-w-lg mx-auto flex gap-1 mb-2">
            <button type="button" @click="activeTab = 'dados'" class="flex-1 py-2 rounded-xl text-[10px] font-black uppercase tracking-wide transition-colors"
                :class="activeTab === 'dados' ? 'bg-white/10 text-white' : 'text-slate-500'">Dados</button>
            <button type="button" @click="activeTab = 'itens'" class="flex-1 py-2 rounded-xl text-[10px] font-black uppercase tracking-wide transition-colors"
                :class="activeTab === 'itens' ? 'bg-white/10 text-white' : 'text-slate-500'">
                Itens (<span x-text="items.length"></span>)
            </button>
            <button type="button" @click="activeTab = 'valores'" class="flex-1 py-2 rounded-xl text-[10px] font-black uppercase tracking-wide transition-colors"
                :class="activeTab === 'valores' ? 'bg-white/10 text-white' : 'text-slate-500'">Valores</button>
        </div>
        <button type="submit" class="w-full max-w-lg mx-auto flex py-4 bg-amber-500 hover:bg-amber-400 text-slate-900 font-black rounded-2xl uppercase tracking-widest text-xs justify-center items-center gap-2 active:scale-[0.99]">
            {{ $submitLabel }}
        </button>
    </div>

    {{-- Desktop: tabela completa (lg+) --}}
    <div class="hidden lg:block mt-8 border-t border-white/10 pt-8 pb-8">
        <p class="text-[10px] font-black text-slate-600 uppercase tracking-widest mb-4">Visão desktop — planilha completa</p>
        @include('propostas._toolbar')
        <div class="bg-white/5 border border-white/10 rounded-3xl overflow-hidden">
            <div class="overflow-y-auto max-h-[60vh]">
                <table class="w-full text-left">
                    <thead class="sticky top-0 bg-slate-900 border-b border-white/10 text-[10px] font-black text-slate-500 uppercase">
                        <tr>
                            <th class="px-4 py-3 w-12">Etapa</th>
                            <th class="px-4 py-3 w-16">Nº</th>
                            <th class="px-4 py-3">Descrição</th>
                            <th class="px-4 py-3 w-16">Un.</th>
                            <th class="px-4 py-3 w-20">Qtd</th>
                            <th class="px-4 py-3 w-24">Unit.</th>
                            <th class="px-4 py-3 w-24 text-right">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/5">
                        <template x-for="(item, index) in items" :key="'dt-'+index">
                            <tr x-show="itemVisible(item, index)" class="hover:bg-white/[0.02]" :class="item.is_etapa ? 'bg-white/[0.03]' : ''">
                                <td class="px-4 py-2 text-center"><input type="checkbox" x-model="item.is_etapa" class="rounded text-amber-500"></td>
                                <td class="px-4 py-2"><input type="text" x-model="item.ordem" class="w-full bg-slate-900/50 rounded text-white text-xs text-center py-1"></td>
                                <td class="px-4 py-2"><input type="text" x-model="item.descricao" class="w-full bg-transparent text-white text-sm" :class="item.is_etapa ? 'font-black uppercase text-amber-500/90' : ''"></td>
                                <td class="px-4 py-2"><input type="text" x-model="item.unidade" class="w-full bg-transparent text-slate-400 text-sm text-center"></td>
                                <td class="px-4 py-2"><input type="number" step="0.001" x-model="item.quantidade" class="w-full bg-transparent text-white text-sm text-center"></td>
                                <td class="px-4 py-2"><input type="number" step="0.01" x-model="item.valor_unitario" class="w-full bg-transparent text-white text-sm text-center"></td>
                                <td class="px-4 py-2 text-right text-xs font-black text-white">R$ <span x-text="formatMoney(itemSubtotal(item))"></span></td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</form>
