<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <p class="text-[10px] font-black text-blue-500 uppercase tracking-[0.2em] mb-1">Acompanhamento de Obra</p>
                <h2 class="font-black text-xl text-white leading-tight uppercase tracking-tight">
                    Cronograma de Etapas
                </h2>
            </div>
            
            <button @click="$dispatch('open-add-etapa-modal')" class="flex items-center gap-2 px-5 py-2.5 bg-blue-600 hover:bg-blue-500 text-white rounded-xl transition-all shadow-lg shadow-blue-600/20 text-[10px] font-black uppercase tracking-widest active:scale-95">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                Nova Etapa
            </button>
        </div>
    </x-slot>

    <div class="max-w-5xl mx-auto space-y-6 pb-24 px-4" 
        x-data="{ 
            showAddEtapaModal: false, 
            showEditEtapaModal: false,
            selectedEtapa: null,
            openEditModal(etapa) {
                this.selectedEtapa = etapa;
                this.showEditEtapaModal = true;
            }
        }"
        @open-add-etapa-modal.window="showAddEtapaModal = true"
    >

        <!-- Resumo de Progresso -->
        <div class="bg-white/5 backdrop-blur-xl border border-white/10 rounded-3xl p-6 shadow-2xl">
            <div class="flex justify-between items-end mb-4">
                <div>
                    <p class="text-[10px] font-black text-slate-500 uppercase tracking-[0.2em] mb-1">Progresso Geral</p>
                    <h3 class="text-2xl font-black text-white leading-none">
                        @php
                            $progressoGeral = $etapas->count() > 0 ? round($etapas->avg('percentual_concluido')) : 0;
                        @endphp
                        {{ $progressoGeral }}%
                    </h3>
                </div>
                <div class="text-right">
                    <p class="text-[10px] font-black text-slate-500 uppercase tracking-[0.2em] mb-1">Fases Concluídas</p>
                    <p class="text-sm font-bold text-white">{{ $etapas->where('status', 'concluida')->count() }} de {{ $etapas->count() }}</p>
                </div>
            </div>
            <div class="w-full h-3 bg-slate-900/50 rounded-full overflow-hidden border border-white/5">
                <div class="h-full bg-gradient-to-r from-blue-600 to-cyan-400 transition-all duration-1000 shadow-[0_0_15px_rgba(37,99,235,0.3)]" style="width: {{ $progressoGeral }}%"></div>
            </div>
        </div>

        <!-- Lista de Etapas -->
        <div class="grid grid-cols-1 gap-4">
            @forelse($etapas as $etapa)
                <div class="bg-white/5 backdrop-blur-xl border border-white/10 rounded-2xl p-5 shadow-xl transition-all hover:border-white/20 group relative overflow-hidden">
                    <div class="flex items-start gap-4 relative z-10">
                        <div class="shrink-0 mt-1">
                            <div class="w-10 h-10 rounded-xl flex items-center justify-center border transition-all
                                @if($etapa->status == 'concluida') bg-green-500/10 border-green-500/20 text-green-500
                                @elseif($etapa->status == 'em_progresso') bg-blue-500/10 border-blue-500/20 text-blue-500
                                @elseif($etapa->status == 'atrasada') bg-rose-500/10 border-rose-500/20 text-rose-500
                                @else bg-slate-800 border-white/10 text-slate-500 @endif">
                                <span class="text-xs font-black">{{ $etapa->ordem ?: $loop->iteration }}</span>
                            </div>
                        </div>
                        <div class="flex-1">
                            <div class="flex justify-between items-start mb-2">
                                <div>
                                    <h4 class="text-white font-bold">{{ $etapa->nome }}</h4>
                                    <p class="text-[9px] text-slate-500 uppercase font-black tracking-widest">
                                        {{ $etapa->data_inicio_prevista ? $etapa->data_inicio_prevista->format('d/m/Y') : 'Sem data' }}
                                    </p>
                                </div>
                                <button @click="openEditModal({{ json_encode($etapa) }})" class="p-2 bg-white/5 hover:bg-white/10 rounded-lg text-slate-400 transition-colors active:scale-90">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                                </button>
                            </div>
                            <div class="flex justify-between items-center text-[10px] font-black uppercase tracking-tighter mb-1.5 mt-4">
                                <span class="text-slate-500">Andamento</span>
                                <span class="text-white">{{ $etapa->percentual_concluido }}%</span>
                            </div>
                            <div class="w-full h-1.5 bg-slate-900 rounded-full overflow-hidden border border-white/5">
                                <div class="h-full bg-blue-500 shadow-[0_0_10px_rgba(59,130,246,0.5)]" style="width: {{ $etapa->percentual_concluido }}%"></div>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="p-12 text-center border-2 border-dashed border-white/5 rounded-3xl">
                    <p class="text-slate-500 text-sm font-bold uppercase tracking-widest">Organize sua obra por etapas para acompanhar o progresso</p>
                </div>
            @endforelse
        </div>

        <!-- MODAIS -->
        <div x-show="showAddEtapaModal" class="fixed inset-0 z-[100] flex items-center justify-center p-4" style="display:none;">
            <div class="fixed inset-0 bg-slate-950/80 backdrop-blur-md" @click="showAddEtapaModal = false"></div>
            <div class="relative w-full max-w-lg bg-slate-900 border border-white/10 rounded-3xl shadow-2xl p-6" x-transition>
                <h3 class="text-white font-black uppercase tracking-widest text-sm mb-6">Cadastrar Nova Etapa</h3>
                <form action="{{ route('etapa-obras.store') }}" method="POST" class="space-y-4">
                    @csrf
                    <div>
                        <label class="text-[10px] font-bold text-slate-500 uppercase mb-1 block">Nome da Etapa</label>
                        <input type="text" name="nome" required placeholder="Ex: Fundação, Alvenaria..." class="w-full bg-slate-800 border-white/10 rounded-xl text-white text-sm focus:border-blue-500">
                    </div>
                    <input type="hidden" name="valor" value="0">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="text-[10px] font-bold text-slate-500 uppercase mb-1 block">Data Prevista</label>
                            <input type="date" name="data_inicio_prevista" class="w-full bg-slate-800 border-white/10 rounded-xl text-white text-sm">
                        </div>
                        <div>
                            <div class="flex items-center justify-between mb-1">
                                <label class="text-[10px] font-bold text-slate-500 uppercase block">Ordem</label>
                                <span class="text-[8px] text-slate-600 uppercase font-black">Posição na lista</span>
                            </div>
                            <input type="number" name="ordem" placeholder="1, 2, 3..." class="w-full bg-slate-800 border-white/10 rounded-xl text-white text-sm">
                        </div>
                    </div>
                    <p class="text-[9px] text-slate-600 uppercase font-bold leading-relaxed">
                        * A **ordem** define em qual posição a etapa aparecerá na lista. Etapas com ordem 1 aparecem no topo.
                    </p>
                    <button type="submit" class="w-full py-4 bg-blue-600 hover:bg-blue-500 text-white font-black rounded-xl transition-all shadow-lg shadow-blue-600/20 uppercase tracking-widest text-xs mt-2">
                        Criar Etapa
                    </button>
                </form>
            </div>
        </div>

        <div x-show="showEditEtapaModal" class="fixed inset-0 z-[100] flex items-center justify-center p-4" style="display:none;">
            <div class="fixed inset-0 bg-slate-950/80 backdrop-blur-md" @click="showEditEtapaModal = false"></div>
            <div class="relative w-full max-w-lg bg-slate-900 border border-white/10 rounded-3xl shadow-2xl p-6" x-transition>
                <h3 class="text-white font-black uppercase tracking-widest text-sm mb-6">Atualizar Andamento</h3>
                <form :action="'/etapa-obras/' + selectedEtapa?.id" method="POST" class="space-y-4">
                    @csrf @method('PUT')
                    <input type="hidden" name="valor" :value="selectedEtapa?.valor || 0">
                    <div>
                        <label class="text-[10px] font-bold text-slate-500 uppercase mb-1 block">Nome</label>
                        <input type="text" name="nome" :value="selectedEtapa?.nome" required class="w-full bg-slate-800 border-white/10 rounded-xl text-white text-sm">
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="text-[10px] font-bold text-slate-500 uppercase mb-1 block">Status</label>
                            <select name="status" :value="selectedEtapa?.status" class="w-full bg-slate-800 border-white/10 rounded-xl text-white text-xs">
                                <option value="pendente">Pendente</option>
                                <option value="em_progresso">Em Progresso</option>
                                <option value="concluida">Concluída</option>
                                <option value="atrasada">Atrasada</option>
                            </select>
                        </div>
                        <div>
                            <label class="text-[10px] font-bold text-slate-500 uppercase mb-1 block">Progresso (%)</label>
                            <input type="number" name="percentual_concluido" :value="selectedEtapa?.percentual_concluido" min="0" max="100" class="w-full bg-slate-800 border-white/10 rounded-xl text-white text-sm">
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
