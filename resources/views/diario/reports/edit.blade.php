<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <p class="text-[10px] font-black text-rose-500 uppercase tracking-[0.2em] mb-1">Edição de Relatório</p>
                <h2 class="font-black text-xl text-white leading-tight uppercase tracking-tight">
                    {{ $diarioReport->data_relatorio->format('d/m/Y') }} — {{ $obra->nome }}
                </h2>
            </div>
            <a href="{{ route('diario-reports.show', $diarioReport) }}" class="text-slate-500 hover:text-white transition-colors">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </a>
        </div>
    </x-slot>

    <div class="max-w-4xl mx-auto pb-24 px-4 sm:px-6" x-data="{
        labor: {{ json_encode($diarioReport->mao_de_obra ?: [['funcao' => '', 'quantidade' => 1]]) }},
        equipment: {{ json_encode($diarioReport->maquinario ?: [['item' => '', 'quantidade' => 1]]) }},
        addLabor() { this.labor.push({ funcao: '', quantidade: 1 }) },
        removeLabor(index) { this.labor.splice(index, 1) },
        addEquipment() { this.equipment.push({ item: '', quantidade: 1 }) },
        removeEquipment(index) { this.equipment.splice(index, 1) }
    }">

        <!-- Audit Warning Banner -->
        <div class="mb-8 flex items-center gap-4 p-4 bg-rose-500/10 border border-rose-500/20 rounded-2xl text-rose-400">
            <svg class="w-8 h-8 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
            </svg>
            <div>
                <p class="text-sm font-black uppercase">Atenção: Relatório já emitido</p>
                <p class="text-[11px] text-rose-400/70 mt-0.5">Toda edição ficará registrada com seu nome e horário no documento oficial. As fotos do feed não podem ser alteradas.</p>
            </div>
        </div>

        <form action="{{ route('diario-reports.update', $diarioReport) }}" method="POST" class="space-y-8">
            @csrf
            @method('PUT')

            <!-- Seção 1: Auditoria de Tempo -->
            <div class="bg-white/5 backdrop-blur-xl border border-white/10 rounded-3xl p-6 shadow-2xl">
                <div class="flex justify-between items-center mb-6">
                    <h3 class="text-sm font-black text-white uppercase tracking-widest flex items-center gap-2">
                        <span class="w-2 h-2 bg-amber-500 rounded-full"></span>
                        Auditoria de Tempo (Hora a Hora)
                    </h3>
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="dia_improdutivo" value="1"
                            {{ $diarioReport->dia_improdutivo ? 'checked' : '' }}
                            class="rounded border-white/10 bg-slate-900 text-amber-500 focus:ring-amber-500">
                        <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Dia Improdutivo</span>
                    </label>
                </div>
                <div class="grid grid-cols-2 sm:grid-cols-4 md:grid-cols-7 gap-3">
                    @foreach(['07:00', '08:00', '09:00', '10:00', '11:00', '12:00', '13:00', '14:00', '15:00', '16:00', '17:00', '18:00', '19:00', '20:00'] as $hora)
                        <div class="space-y-1">
                            <label class="text-[9px] font-black text-slate-500 uppercase block text-center">{{ $hora }}</label>
                            <select name="clima_horario[{{ $hora }}]" class="w-full bg-slate-900/50 border-white/10 rounded-lg text-[10px] text-white focus:border-amber-500 focus:ring-amber-500 px-2 py-1">
                                @foreach(['-', 'bom', 'nublado', 'chuva_l', 'chuva_f', 'paralisado'] as $opt)
                                    <option value="{{ $opt }}" {{ ($diarioReport->clima_horario[$hora] ?? '-') === $opt ? 'selected' : '' }}>
                                        {{ $opt === '-' ? '-' : ucfirst(str_replace('_', ' ', $opt)) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Seção 2: Mão de Obra -->
            <div class="bg-white/5 backdrop-blur-xl border border-white/10 rounded-3xl p-6 shadow-2xl">
                <div class="flex justify-between items-center mb-6">
                    <h3 class="text-sm font-black text-white uppercase tracking-widest flex items-center gap-2">
                        <span class="w-2 h-2 bg-blue-500 rounded-full"></span>
                        Mão de Obra Detalhada
                    </h3>
                    <button type="button" @click="addLabor()" class="text-[10px] font-black text-blue-500 uppercase hover:text-blue-400 transition-colors">+ Adicionar</button>
                </div>
                <div class="space-y-3">
                    <template x-for="(person, index) in labor" :key="index">
                        <div class="flex items-center gap-3">
                            <input type="text" :name="'mao_de_obra['+index+'][funcao]'" x-model="person.funcao" placeholder="Função (Ex: Pedreiro)" class="flex-1 bg-slate-900/50 border-white/10 rounded-xl text-white text-sm focus:border-amber-500">
                            <input type="number" :name="'mao_de_obra['+index+'][quantidade]'" x-model="person.quantidade" class="w-20 bg-slate-900/50 border-white/10 rounded-xl text-white text-sm focus:border-amber-500">
                            <button type="button" @click="removeLabor(index)" class="p-2 text-slate-600 hover:text-red-500" x-show="labor.length > 1">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                            </button>
                        </div>
                    </template>
                </div>
            </div>

            <!-- Seção 3: Maquinário -->
            <div class="bg-white/5 backdrop-blur-xl border border-white/10 rounded-3xl p-6 shadow-2xl">
                <div class="flex justify-between items-center mb-6">
                    <h3 class="text-sm font-black text-white uppercase tracking-widest flex items-center gap-2">
                        <span class="w-2 h-2 bg-purple-500 rounded-full"></span>
                        Equipamentos / Maquinário
                    </h3>
                    <button type="button" @click="addEquipment()" class="text-[10px] font-black text-purple-500 uppercase hover:text-purple-400 transition-colors">+ Adicionar</button>
                </div>
                <div class="space-y-3">
                    <template x-for="(eq, index) in equipment" :key="index">
                        <div class="flex items-center gap-3">
                            <input type="text" :name="'maquinario['+index+'][item]'" x-model="eq.item" placeholder="Equipamento (Ex: Betoneira)" class="flex-1 bg-slate-900/50 border-white/10 rounded-xl text-white text-sm focus:border-amber-500">
                            <input type="number" :name="'maquinario['+index+'][quantidade]'" x-model="eq.quantidade" class="w-20 bg-slate-900/50 border-white/10 rounded-xl text-white text-sm focus:border-amber-500">
                            <button type="button" @click="removeEquipment(index)" class="p-2 text-slate-600 hover:text-red-500" x-show="equipment.length > 1">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                            </button>
                        </div>
                    </template>
                </div>
            </div>

            <!-- Seção 4: Atividades -->
            <div class="bg-white/5 backdrop-blur-xl border border-white/10 rounded-3xl p-6 shadow-2xl">
                <h3 class="text-sm font-black text-white uppercase tracking-widest mb-6 flex items-center gap-2">
                    <span class="w-2 h-2 bg-green-500 rounded-full"></span>
                    Relato de Atividades
                </h3>
                <div class="space-y-6">
                    <div class="space-y-2">
                        <label class="text-[10px] font-bold text-slate-500 uppercase">Serviços Executados</label>
                        <textarea name="servicos_execucao" rows="6" class="w-full bg-slate-900/50 border-white/10 rounded-2xl text-white text-sm focus:border-amber-500">{{ $diarioReport->servicos_execucao }}</textarea>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-2">
                            <label class="text-[10px] font-bold text-slate-500 uppercase">Iniciados</label>
                            <textarea name="servicos_iniciados" rows="3" class="w-full bg-slate-900/50 border-white/10 rounded-2xl text-white text-sm focus:border-amber-500">{{ $diarioReport->servicos_iniciados }}</textarea>
                        </div>
                        <div class="space-y-2">
                            <label class="text-[10px] font-bold text-slate-500 uppercase">Concluídos</label>
                            <textarea name="servicos_concluidos" rows="3" class="w-full bg-slate-900/50 border-white/10 rounded-2xl text-white text-sm focus:border-amber-500">{{ $diarioReport->servicos_concluidos }}</textarea>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Seção 5: Ocorrências -->
            <div class="bg-white/5 backdrop-blur-xl border border-white/10 rounded-3xl p-6 shadow-2xl">
                <h3 class="text-sm font-black text-white uppercase tracking-widest mb-6 flex items-center gap-2">
                    <span class="w-2 h-2 bg-rose-500 rounded-full"></span>
                    Observações e Ocorrências
                </h3>
                <div class="space-y-6">
                    <div class="space-y-2">
                        <label class="text-[10px] font-bold text-slate-500 uppercase">Materiais Recebidos</label>
                        <textarea name="materiais_recebidos" rows="2" class="w-full bg-slate-900/50 border-white/10 rounded-2xl text-white text-sm focus:border-amber-500">{{ $diarioReport->materiais_recebidos }}</textarea>
                    </div>
                    <div class="space-y-2">
                        <label class="text-[10px] font-bold text-slate-500 uppercase">Ocorrências / Imprevistos</label>
                        <textarea name="ocorrencias" rows="3" class="w-full bg-slate-900/50 border-white/10 rounded-2xl text-white text-sm focus:border-amber-500">{{ $diarioReport->ocorrencias }}</textarea>
                    </div>
                    <div class="space-y-2">
                        <label class="text-[10px] font-bold text-slate-500 uppercase">Anotações Gerais</label>
                        <textarea name="observacoes" rows="3" class="w-full bg-slate-900/50 border-white/10 rounded-2xl text-white text-sm focus:border-amber-500">{{ $diarioReport->observacoes }}</textarea>
                    </div>
                </div>
            </div>

            <div class="flex items-center gap-4">
                <a href="{{ route('diario-reports.show', $diarioReport) }}" class="px-6 py-4 bg-white/5 hover:bg-white/10 text-slate-400 font-black rounded-2xl transition-all uppercase tracking-widest text-xs border border-white/10">
                    Cancelar
                </a>
                <button type="submit" class="flex-1 py-4 bg-green-600 hover:bg-green-500 text-white font-black rounded-2xl transition-all shadow-lg shadow-green-500/20 uppercase tracking-widest text-xs active:scale-[0.98] flex items-center justify-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    Salvar Alterações
                </button>
            </div>
        </form>
    </div>
</x-app-layout>
