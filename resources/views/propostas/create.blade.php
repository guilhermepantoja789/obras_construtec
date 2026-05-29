<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div class="flex items-center gap-4">
                <a href="{{ route('propostas.index') }}" class="p-2 bg-white/5 hover:bg-white/10 rounded-xl text-slate-400 transition-all border border-white/5">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                </a>
                <div>
                    <p class="text-[10px] font-black text-amber-500 uppercase tracking-[0.2em] mb-0.5">Elaboração de Orçamento</p>
                    <h2 class="font-black text-xl text-white leading-tight uppercase tracking-tight">
                        Nova Proposta
                    </h2>
                </div>
            </div>
        </div>
    </x-slot>

    <div class="max-w-7xl mx-auto px-4 pb-24" x-data="{
        items: [
            { descricao: '', unidade: 'un', quantidade: 1, valor_unitario: 0, is_etapa: false, ordem: 1 }
        ],
        addItem() {
            this.items.push({ descricao: '', unidade: 'un', quantidade: 1, valor_unitario: 0, is_etapa: false, ordem: this.items.length + 1 });
        },
        removeItem(index) {
            this.items.splice(index, 1);
        },
        getTotal() {
            return this.items.reduce((sum, item) => sum + (parseFloat(item.quantidade) * parseFloat(item.valor_unitario) || 0), 0);
        },
        isLoading: false,
        async importExcel(event) {
            const file = event.target.files[0];
            if (!file) return;

            const formData = new FormData();
            formData.append('file', file);
            formData.append('_token', '{{ csrf_token() }}');

            this.isLoading = true;
            try {
                const response = await fetch('{{ route('propostas.import') }}', {
                    method: 'POST',
                    body: formData
                });

                if (!response.ok) throw new Error('Falha na importação');

                const data = await response.json();
                
                if (!response.ok) {
                    throw new Error(data.error || 'Falha na importação');
                }
                
                // Confirmar se deseja sobrescrever ou adicionar
                if (this.items.length > 0 && this.items[0].descricao !== '') {
                    if (confirm('Deseja substituir os itens atuais pelos itens do Excel?')) {
                        this.items = data.items;
                    } else {
                        // Adicionar ao final
                        const lastOrder = this.items.length > 0 ? Math.max(...this.items.map(i => i.ordem || 0)) : 0;
                        data.items.forEach((item, idx) => {
                            item.ordem = lastOrder + idx + 1;
                            this.items.push(item);
                        });
                    }
                } else {
                    this.items = data.items;
                }

                alert('Importação concluída com sucesso! ' + data.items.length + ' itens carregados.');
            } catch (error) {
                console.error(error);
                alert('Erro: ' + error.message);
            } finally {
                this.isLoading = false;
                event.target.value = ''; // Reset input
            }
        }
    }">


        <form action="{{ route('propostas.store') }}" method="POST" class="space-y-6 mt-4">
            @csrf
            
            @if($errors->any())
                <div class="bg-rose-500/10 border border-rose-500/20 rounded-2xl p-4 mb-6">
                    <div class="flex items-center gap-3 mb-2">
                        <svg class="w-5 h-5 text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        <h4 class="text-xs font-black text-rose-500 uppercase tracking-widest">Erros de Validação</h4>
                    </div>
                    <ul class="list-disc list-inside text-xs text-rose-400/80 space-y-1">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            
            <!-- Cabeçalho -->
            <div class="bg-white/5 backdrop-blur-xl border border-white/10 rounded-3xl p-6 md:p-8 shadow-2xl">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="md:col-span-2">
                        <label class="text-[10px] font-bold text-slate-500 uppercase mb-2 block">Título da Proposta</label>
                        <input type="text" name="titulo" required placeholder="Ex: Reforma Geral - Fase 01" class="w-full bg-slate-900/50 border-white/10 rounded-2xl text-white text-sm focus:border-amber-500 py-3">
                    </div>
                    <div>
                        <label class="text-[10px] font-bold text-slate-500 uppercase mb-2 block">Data</label>
                        <input type="date" name="data_proposta" required value="{{ date('Y-m-d') }}" class="w-full bg-slate-900/50 border-white/10 rounded-2xl text-white text-sm focus:border-amber-500 py-3">
                    </div>
                    <div class="md:col-span-2">
                        <label class="text-[10px] font-bold text-slate-500 uppercase mb-2 block">Escopo / Descrição</label>
                        <textarea name="escopo" rows="3" placeholder="Descreva o escopo principal do serviço..." class="w-full bg-slate-900/50 border-white/10 rounded-2xl text-white text-sm focus:border-amber-500"></textarea>
                    </div>
                    <div>
                        <label class="text-[10px] font-bold text-slate-500 uppercase mb-2 block">Status Inicial</label>
                        <select name="status" class="w-full bg-slate-900/50 border-white/10 rounded-2xl text-white text-sm focus:border-amber-500 py-3">
                            <option value="rascunho">Rascunho</option>
                            <option value="enviada">Enviada ao Cliente</option>
                            <option value="aceita">Aceita (Gera Etapas)</option>
                            <option value="recusada">Recusada</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Planilha de Itens -->
            <div class="space-y-4">
                <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 px-2">
                    <h3 class="text-xs font-black text-white uppercase tracking-widest flex items-center gap-2">
                        <span class="w-2 h-2 bg-amber-500 rounded-full"></span>
                        Itens da Proposta (<span x-text="items.length"></span>)
                    </h3>
                    
                    <div class="flex flex-wrap items-center gap-2 w-full md:w-auto">
                        <input type="file" x-ref="fileInput" @change="importExcel($event)" class="hidden" accept=".xlsx,.xls,.csv">
                        <button type="button" @click="$refs.fileInput.click()" :disabled="isLoading" class="flex-1 md:flex-none px-4 py-2 bg-emerald-500/10 hover:bg-emerald-500/20 text-emerald-500 rounded-xl border border-emerald-500/20 text-[10px] font-black uppercase tracking-widest transition-all active:scale-95 disabled:opacity-50">
                            <span x-show="!isLoading">Importar Excel</span>
                            <span x-show="isLoading">Processando...</span>
                        </button>
                        <button type="button" @click="addItem()" class="flex-1 md:flex-none px-4 py-2 bg-amber-500/10 hover:bg-amber-500/20 text-amber-500 rounded-xl border border-amber-500/20 text-[10px] font-black uppercase tracking-widest transition-all active:scale-95">
                            + Novo Item
                        </button>
                        <button type="submit" class="flex-1 md:flex-none px-4 py-2 bg-amber-500 text-slate-900 rounded-xl font-black text-[10px] uppercase tracking-widest transition-all active:scale-95 shadow-lg shadow-amber-500/20">
                            Salvar Proposta
                        </button>
                    </div>
                </div>

                <!-- Layout Desktop (Tabela) -->
                <div class="hidden md:block bg-white/5 border border-white/10 rounded-3xl overflow-hidden relative">
                    <div class="overflow-y-auto max-h-[70vh] scrollbar-thin scrollbar-thumb-white/10 scrollbar-track-transparent">
                        <table class="w-full text-left border-collapse">
                            <thead class="sticky top-0 z-10 bg-slate-900 border-b border-white/10 shadow-xl">
                                <tr class="text-[10px] font-black text-slate-500 uppercase tracking-widest">
                                    <th class="px-4 py-4 w-12 text-center">Etapa?</th>
                                    <th class="px-4 py-4 w-16 text-center">Nº</th>
                                    <th class="px-4 py-4">Descrição</th>
                                    <th class="px-4 py-4 w-20">Unid.</th>
                                    <th class="px-4 py-4 w-24">Qtd.</th>
                                    <th class="px-4 py-4 w-32">Unitário</th>
                                    <th class="px-4 py-4 w-32 text-right">Subtotal</th>
                                    <th class="px-4 py-4 w-10"></th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-white/5">
                                <template x-for="(item, index) in items" :key="index">
                                    <tr class="hover:bg-white/[0.02] group transition-colors" :class="item.is_etapa ? 'bg-white/[0.03]' : ''">
                                        <td class="px-4 py-3 text-center">
                                            <input type="checkbox" :name="'items['+index+'][is_etapa]'" value="1" x-model="item.is_etapa" class="rounded bg-slate-900 border-white/10 text-amber-500 focus:ring-amber-500">
                                        </td>
                                        <td class="px-4 py-3">
                                            <input type="text" :name="'items['+index+'][ordem]'" x-model="item.ordem" class="w-full bg-slate-900/50 border-white/10 rounded-lg text-white text-[10px] font-bold text-center py-1">
                                        </td>
                                        <td class="px-4 py-3">
                                            <input type="text" :name="'items['+index+'][descricao]'" x-model="item.descricao" required class="w-full bg-transparent border-none text-white text-sm focus:ring-0 p-0 placeholder-slate-700" :class="item.is_etapa ? 'font-black uppercase tracking-tight text-amber-500/90' : ''" placeholder="Descrição do item...">
                                        </td>
                                        <td class="px-4 py-3">
                                            <input type="text" :name="'items['+index+'][unidade]'" x-model="item.unidade" class="w-full bg-transparent border-none text-slate-400 text-sm focus:ring-0 p-0 text-center" placeholder="un">
                                        </td>
                                        <td class="px-4 py-3">
                                            <input type="number" step="0.001" :name="'items['+index+'][quantidade]'" x-model="item.quantidade" class="w-full bg-transparent border-none text-white text-sm focus:ring-0 p-0 text-center font-medium">
                                        </td>
                                        <td class="px-4 py-3">
                                            <input type="number" step="0.01" :name="'items['+index+'][valor_unitario]'" x-model="item.valor_unitario" class="w-full bg-transparent border-none text-white text-sm focus:ring-0 p-0 text-center font-medium">
                                        </td>
                                        <td class="px-4 py-3 text-right text-xs font-black" :class="item.is_etapa ? 'text-amber-500' : 'text-white'">
                                            R$ <span x-text="(item.quantidade * item.valor_unitario).toLocaleString('pt-BR', {minimumFractionDigits: 2})"></span>
                                        </td>
                                        <td class="px-4 py-3 text-right">
                                            <button type="button" @click="removeItem(index)" class="p-1.5 text-slate-700 hover:text-rose-500 transition-colors">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                            </button>
                                        </td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>
                    </div>
                </div>


                <!-- Layout Mobile (Cards) -->
                <div class="md:hidden space-y-4">
                    <template x-for="(item, index) in items" :key="index">
                        <div class="bg-white/5 border border-white/10 rounded-3xl p-5 space-y-4 shadow-xl">
                            <div class="flex justify-between items-center pb-3 border-b border-white/5">
                                <div class="flex items-center gap-3">
                                    <label class="flex items-center gap-2">
                                        <input type="checkbox" :name="'items['+index+'][is_etapa]'" value="1" x-model="item.is_etapa" class="rounded bg-slate-900 border-white/10 text-amber-500 focus:ring-amber-500">
                                        <span class="text-[9px] font-black text-slate-500 uppercase tracking-widest">É uma Etapa?</span>
                                    </label>
                                    <div x-show="item.is_etapa" class="flex items-center gap-1">
                                        <span class="text-[9px] font-black text-slate-500 uppercase">Ord:</span>
                                        <input type="text" :name="'items['+index+'][ordem]'" x-model="item.ordem" class="w-12 bg-slate-900/50 border-white/10 rounded-lg text-white text-[10px] text-center p-1">

                                    </div>
                                </div>
                                <button type="button" @click="removeItem(index)" class="p-2 text-rose-500/50 hover:text-rose-500 transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                </button>
                            </div>

                            <div class="space-y-2">
                                <label class="text-[9px] font-black text-slate-600 uppercase tracking-widest block">Descrição do Item</label>
                                <input type="text" :name="'items['+index+'][descricao]'" x-model="item.descricao" required class="w-full bg-slate-900/30 border-white/10 rounded-xl text-white text-sm focus:border-amber-500 py-3 px-4" placeholder="Ex: Piso porcelanato...">
                            </div>

                            <div class="grid grid-cols-3 gap-3">
                                <div class="space-y-1">
                                    <label class="text-[8px] font-black text-slate-600 uppercase block">Unidade</label>
                                    <input type="text" :name="'items['+index+'][unidade]'" x-model="item.unidade" class="w-full bg-slate-900/30 border-white/10 rounded-xl text-white text-xs text-center py-2" placeholder="un">
                                </div>
                                <div class="space-y-1">
                                    <label class="text-[8px] font-black text-slate-600 uppercase block">Quantidade</label>
                                    <input type="number" step="0.001" :name="'items['+index+'][quantidade]'" x-model="item.quantidade" class="w-full bg-slate-900/30 border-white/10 rounded-xl text-white text-xs text-center py-2">
                                </div>
                                <div class="space-y-1">
                                    <label class="text-[8px] font-black text-slate-600 uppercase block">V. Unitário</label>
                                    <input type="number" step="0.01" :name="'items['+index+'][valor_unitario]'" x-model="item.valor_unitario" class="w-full bg-slate-900/30 border-white/10 rounded-xl text-white text-xs text-center py-2">
                                </div>
                            </div>

                            <div class="flex justify-between items-center pt-3 border-t border-white/5">
                                <span class="text-[9px] font-black text-slate-600 uppercase">Subtotal</span>
                                <span class="text-sm font-black text-white">R$ <span x-text="(item.quantidade * item.valor_unitario).toLocaleString('pt-BR', {minimumFractionDigits: 2})"></span></span>
                            </div>
                        </div>
                    </template>
                </div>

                <!-- Footer Total -->
                <div class="bg-slate-900/50 border border-white/10 rounded-3xl p-6 flex justify-between items-center shadow-xl">
                    <span class="text-[10px] font-black text-slate-500 uppercase tracking-[0.2em]">Total Geral</span>
                    <span class="text-2xl font-black text-white" x-text="'R$ ' + getTotal().toLocaleString('pt-BR', {minimumFractionDigits: 2})"></span>
                </div>

                <div class="flex justify-center pt-4">
                    <button type="submit" class="w-full md:w-auto px-12 py-5 bg-amber-500 hover:bg-amber-400 text-slate-900 font-black rounded-2xl transition-all shadow-lg shadow-amber-500/20 uppercase tracking-widest text-xs flex items-center justify-center gap-2 active:scale-95">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                        Finalizar e Salvar Proposta
                    </button>
                </div>
            </div>
        </form>
    </div>
</x-app-layout>
