<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <p class="text-[10px] font-black text-blue-500 uppercase tracking-[0.2em] mb-1">Acompanhamento de Obra</p>
                <h2 class="font-black text-xl text-white leading-tight uppercase tracking-tight">
                    Cronograma de Etapas
                </h2>
            </div>

            @if(Auth::user()->isChefe())
            <div class="flex flex-wrap items-center gap-2">
                @if($propostaAceita)
                <form action="{{ route('etapa-obras.regenerar') }}" method="POST" onsubmit="return confirm('Regenerar etapas a partir da proposta aceita? Etapas vindas da proposta serão recriadas e o progresso delas será resetado.')">
                    @csrf
                    <button type="submit" class="flex items-center gap-2 px-4 py-2.5 bg-amber-500/10 hover:bg-amber-500/20 text-amber-500 border border-amber-500/20 rounded-xl text-[10px] font-black uppercase tracking-widest transition-all active:scale-95">
                        Regenerar da Proposta
                    </button>
                </form>
                @endif
                <button @click="$dispatch('open-add-etapa-modal')" class="flex items-center gap-2 px-5 py-2.5 bg-blue-600 hover:bg-blue-500 text-white rounded-xl transition-all shadow-lg shadow-blue-600/20 text-[10px] font-black uppercase tracking-widest active:scale-95">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                    Nova Etapa
                </button>
            </div>
            @endif
        </div>
    </x-slot>

    <div class="max-w-5xl mx-auto space-y-6 pb-24 px-4"
        x-data="{
            showAddEtapaModal: {{ ($errors->any() && old('_method') !== 'PUT') ? 'true' : 'false' }},
            showEditEtapaModal: {{ ($errors->any() && old('_method') === 'PUT') ? 'true' : 'false' }},
            selectedEtapa: null,
            collapsedGroups: {},
            viewGrouped: true,
            dragId: null,
            etapaOrder: @json($etapas->pluck('id')),
            openEditModal(etapa) {
                this.selectedEtapa = {
                    ...etapa,
                    data_inicio_prevista: etapa.data_inicio_prevista ? String(etapa.data_inicio_prevista).substring(0, 10) : '',
                    data_fim_prevista: etapa.data_fim_prevista ? String(etapa.data_fim_prevista).substring(0, 10) : '',
                    data_inicio_real: etapa.data_inicio_real ? String(etapa.data_inicio_real).substring(0, 10) : '',
                    data_fim_real: etapa.data_fim_real ? String(etapa.data_fim_real).substring(0, 10) : '',
                };
                this.showEditEtapaModal = true;
            },
            toggleGroup(ordem) {
                this.collapsedGroups[ordem] = !this.collapsedGroups[ordem];
            },
            isCollapsed(ordem) {
                return !!this.collapsedGroups[ordem];
            },
            async submitReorder() {
                const form = document.getElementById('etapa-reorder-form');
                const container = document.getElementById('etapa-order-inputs');
                container.innerHTML = '';
                this.etapaOrder.forEach(id => {
                    const input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = 'order[]';
                    input.value = id;
                    container.appendChild(input);
                });
                form.submit();
            },
            handleDrop(targetId) {
                if (!this.dragId || this.dragId === targetId) return;
                const from = this.etapaOrder.indexOf(this.dragId);
                const to = this.etapaOrder.indexOf(targetId);
                if (from === -1 || to === -1) return;
                this.etapaOrder.splice(from, 1);
                this.etapaOrder.splice(to, 0, this.dragId);
                this.dragId = null;
                this.submitReorder();
            }
        }"
        @open-add-etapa-modal.window="showAddEtapaModal = true"
        @etapa-drag-start.window="dragId = $event.detail"
        @etapa-drop.window="handleDrop($event.detail)"
    >
        <!-- Resumo de Progresso -->
        <div class="bg-white/5 backdrop-blur-xl border border-white/10 rounded-3xl p-6 shadow-2xl">
            <div class="flex justify-between items-end mb-4">
                <div>
                    <p class="text-[10px] font-black text-slate-500 uppercase tracking-[0.2em] mb-1">Progresso Geral (ponderado)</p>
                    <h3 class="text-2xl font-black text-white leading-none">{{ $progressoGeral }}%</h3>
                </div>
                <div class="text-right">
                    <p class="text-[10px] font-black text-slate-500 uppercase tracking-[0.2em] mb-1">Fases Concluídas</p>
                    <p class="text-sm font-bold text-white">{{ $etapas->where('status', 'concluida')->count() }} de {{ $etapas->count() }}</p>
                </div>
            </div>
            <div class="w-full h-3 bg-slate-900/50 rounded-full overflow-hidden border border-white/5">
                <div class="h-full bg-gradient-to-r from-blue-600 to-cyan-400 transition-all duration-1000 shadow-[0_0_15px_rgba(37,99,235,0.3)]" style="width: {{ $progressoGeral }}%"></div>
            </div>
            @if(Auth::user()->isChefe())
            <div class="flex items-center justify-between mt-4 pt-4 border-t border-white/5">
                <p class="text-[9px] text-slate-600 uppercase font-bold">Arraste os cards para reordenar (numeração 1, 2, 3…)</p>
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" x-model="viewGrouped" class="rounded bg-slate-900 border-white/10 text-blue-500 focus:ring-blue-500">
                    <span class="text-[9px] font-black text-slate-500 uppercase tracking-widest">Agrupar por fase</span>
                </label>
            </div>
            @endif
        </div>

        <form id="etapa-reorder-form" action="{{ route('etapa-obras.reorder') }}" method="POST" class="hidden">
            @csrf
            <div id="etapa-order-inputs"></div>
        </form>

        <!-- Lista de Etapas -->
        @if($etapas->isEmpty())
            <div class="p-12 text-center border-2 border-dashed border-white/5 rounded-3xl">
                <p class="text-slate-500 text-sm font-bold uppercase tracking-widest">Organize sua obra por etapas para acompanhar o progresso</p>
                @if($propostaAceita)
                    <p class="text-slate-600 text-xs mt-2">Aceite uma proposta com itens marcados como etapa ou use «Regenerar da Proposta».</p>
                @endif
            </div>
        @else
            <!-- Vista agrupada -->
            <div x-show="viewGrouped" class="space-y-4">
                @foreach($grupos as $grupo)
                    @php $parent = $grupo['etapa']; $ordemKey = (string) ($parent->ordem ?? $loop->iteration); @endphp
                    <div class="space-y-2">
                        @if(count($grupo['children']) > 0)
                        <button type="button" @click="toggleGroup('{{ $ordemKey }}')" class="flex items-center gap-2 text-[10px] font-black text-slate-500 uppercase tracking-widest px-2 hover:text-white transition-colors">
                            <svg class="w-3 h-3 transition-transform" :class="isCollapsed('{{ $ordemKey }}') ? '' : 'rotate-90'" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                            Fase {{ $ordemKey }} · {{ count($grupo['children']) }} sub-etapa(s)
                        </button>
                        @endif
                        @include('etapa-obras._etapa-card', ['etapa' => $parent, 'depth' => $parent->ordemDepth()])
                        <div x-show="!isCollapsed('{{ $ordemKey }}')" class="space-y-2">
                            @foreach($grupo['children'] as $child)
                                @include('etapa-obras._etapa-card', ['etapa' => $child, 'isChild' => true])
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Vista plana (para drag-drop) -->
            <div x-show="!viewGrouped" class="grid grid-cols-1 gap-4" x-cloak>
                @foreach($etapas as $etapa)
                    @include('etapa-obras._etapa-card', ['etapa' => $etapa])
                @endforeach
            </div>
        @endif

        <!-- MODAL CRIAR -->
        <div x-show="showAddEtapaModal" class="fixed inset-0 z-[100] flex items-center justify-center p-4" style="display:none;">
            <div class="fixed inset-0 bg-slate-950/80 backdrop-blur-md" @click="showAddEtapaModal = false"></div>
            <div class="relative w-full max-w-lg bg-slate-900 border border-white/10 rounded-3xl shadow-2xl p-6 max-h-[90vh] overflow-y-auto" x-transition>
                <h3 class="text-white font-black uppercase tracking-widest text-sm mb-6">Cadastrar Nova Etapa</h3>
                <form action="{{ route('etapa-obras.store') }}" method="POST" class="space-y-4">
                    @csrf
                    <div>
                        <label class="text-[10px] font-bold text-slate-500 uppercase mb-1 block">Nome da Etapa</label>
                        <input type="text" name="nome" required placeholder="Ex: Fundação, Alvenaria..." value="{{ old('nome') }}" class="w-full bg-slate-800 border-white/10 rounded-xl text-white text-sm focus:border-blue-500">
                    </div>
                    <input type="hidden" name="valor" value="0">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="text-[10px] font-bold text-slate-500 uppercase mb-1 block">Ordem</label>
                            <input type="text" name="ordem" placeholder="Ex: 1, 1.1, 1.1.2" value="{{ old('ordem') }}" class="w-full bg-slate-800 border-white/10 rounded-xl text-white text-sm">
                        </div>
                        <div>
                            <label class="text-[10px] font-bold text-slate-500 uppercase mb-1 block">Início Previsto</label>
                            <input type="date" name="data_inicio_prevista" value="{{ old('data_inicio_prevista') }}" class="w-full bg-slate-800 border-white/10 rounded-xl text-white text-sm">
                        </div>
                        <div class="col-span-2">
                            <label class="text-[10px] font-bold text-slate-500 uppercase mb-1 block">Fim Previsto</label>
                            <input type="date" name="data_fim_prevista" value="{{ old('data_fim_prevista') }}" class="w-full bg-slate-800 border-white/10 rounded-xl text-white text-sm">
                        </div>
                    </div>
                    <p class="text-[9px] text-slate-600 uppercase font-bold leading-relaxed">
                        Use ordem hierárquica (1, 1.1, 1.2) para espelhar a planilha da proposta.
                    </p>
                    <button type="submit" class="w-full py-4 bg-blue-600 hover:bg-blue-500 text-white font-black rounded-xl transition-all shadow-lg shadow-blue-600/20 uppercase tracking-widest text-xs mt-2">
                        Criar Etapa
                    </button>
                </form>
            </div>
        </div>

        <!-- MODAL EDITAR -->
        <div x-show="showEditEtapaModal" class="fixed inset-0 z-[100] flex items-center justify-center p-4" style="display:none;">
            <div class="fixed inset-0 bg-slate-950/80 backdrop-blur-md" @click="showEditEtapaModal = false"></div>
            <div class="relative w-full max-w-lg bg-slate-900 border border-white/10 rounded-3xl shadow-2xl p-6 max-h-[90vh] overflow-y-auto" x-transition>
                <h3 class="text-white font-black uppercase tracking-widest text-sm mb-6">Atualizar Etapa</h3>
                <form :action="'{{ url('etapa-obras') }}/' + selectedEtapa?.id" method="POST" class="space-y-4">
                    @csrf @method('PUT')
                    <input type="hidden" name="valor" :value="selectedEtapa?.valor || 0">
                    <div>
                        <label class="text-[10px] font-bold text-slate-500 uppercase mb-1 block">Nome</label>
                        <input type="text" name="nome" x-model="selectedEtapa.nome" required class="w-full bg-slate-800 border-white/10 rounded-xl text-white text-sm">
                    </div>
                    <div>
                        <label class="text-[10px] font-bold text-slate-500 uppercase mb-1 block">Ordem</label>
                        <input type="text" name="ordem" x-model="selectedEtapa.ordem" placeholder="Ex: 1.1.2" class="w-full bg-slate-800 border-white/10 rounded-xl text-white text-sm">
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="text-[10px] font-bold text-slate-500 uppercase mb-1 block">Status</label>
                            <select name="status" x-model="selectedEtapa.status" class="w-full bg-slate-800 border-white/10 rounded-xl text-white text-xs">
                                <option value="pendente">Pendente</option>
                                <option value="em_progresso">Em Progresso</option>
                                <option value="concluida">Concluída</option>
                                <option value="atrasada">Atrasada</option>
                            </select>
                        </div>
                        <div>
                            <label class="text-[10px] font-bold text-slate-500 uppercase mb-1 block">Progresso (%)</label>
                            <input type="number" name="percentual_concluido" x-model="selectedEtapa.percentual_concluido" min="0" max="100" class="w-full bg-slate-800 border-white/10 rounded-xl text-white text-sm">
                        </div>
                        <div>
                            <label class="text-[10px] font-bold text-slate-500 uppercase mb-1 block">Início Previsto</label>
                            <input type="date" name="data_inicio_prevista" x-model="selectedEtapa.data_inicio_prevista" class="w-full bg-slate-800 border-white/10 rounded-xl text-white text-sm">
                        </div>
                        <div>
                            <label class="text-[10px] font-bold text-slate-500 uppercase mb-1 block">Fim Previsto</label>
                            <input type="date" name="data_fim_prevista" x-model="selectedEtapa.data_fim_prevista" class="w-full bg-slate-800 border-white/10 rounded-xl text-white text-sm">
                        </div>
                        <div>
                            <label class="text-[10px] font-bold text-slate-500 uppercase mb-1 block">Início Real</label>
                            <input type="date" name="data_inicio_real" x-model="selectedEtapa.data_inicio_real" class="w-full bg-slate-800 border-white/10 rounded-xl text-white text-sm">
                        </div>
                        <div>
                            <label class="text-[10px] font-bold text-slate-500 uppercase mb-1 block">Fim Real</label>
                            <input type="date" name="data_fim_real" x-model="selectedEtapa.data_fim_real" class="w-full bg-slate-800 border-white/10 rounded-xl text-white text-sm">
                        </div>
                    </div>
                    <button type="submit" class="w-full py-4 bg-blue-600 hover:bg-blue-500 text-white font-black rounded-xl transition-all shadow-lg shadow-blue-600/20 uppercase tracking-widest text-xs">
                        Salvar Alterações
                    </button>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
