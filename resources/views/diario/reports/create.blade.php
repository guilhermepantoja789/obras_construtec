<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <p class="text-[10px] font-black text-amber-500 uppercase tracking-[0.2em] mb-1">
                    {{ $date->isToday() ? 'Finalização do Dia' : 'Lançamento Retroativo' }}
                </p>
                <h2 class="font-black text-xl text-white leading-tight uppercase tracking-tight">
                    Relatório Diário - {{ $date->translatedFormat('d \d\e F') }}
                </h2>
            </div>
            <a href="{{ route('feed.index') }}" class="text-slate-500 hover:text-white transition-colors">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </a>
        </div>
    </x-slot>

    <div class="max-w-4xl mx-auto pb-24 px-4 sm:px-6" x-data="{ 
        labor: [{ funcao: '', quantidade: 1 }],
        equipment: [{ item: '', quantidade: 1 }],
        addLabor() { this.labor.push({ funcao: '', quantidade: 1 }) },
        removeLabor(index) { this.labor.splice(index, 1) },
        addEquipment() { this.equipment.push({ item: '', quantidade: 1 }) },
        removeEquipment(index) { this.equipment.splice(index, 1) }
    }">
        <form action="{{ route('diario-reports.store') }}" method="POST" class="space-y-8">
            @csrf
            <input type="hidden" name="data_relatorio" value="{{ $date->format('Y-m-d') }}">
            
            <!-- Sessão 1: Auditoria de Tempo (07:00 as 20:00) -->
            <div class="bg-white/5 backdrop-blur-xl border border-white/10 rounded-3xl p-6 shadow-2xl">
                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
                    <h3 class="text-sm font-black text-white uppercase tracking-widest flex items-center gap-2">
                        <span class="w-2 h-2 bg-amber-500 rounded-full"></span>
                        Auditoria de Tempo (Hora a Hora)
                    </h3>
                    
                    <div class="flex flex-wrap items-center gap-4 bg-slate-900/50 p-1.5 rounded-2xl border border-white/5">
                        <div class="flex items-center gap-1">
                            <div>
                                <input type="radio" name="status_dia" id="status_trabalhado" value="trabalhado" checked class="hidden peer">
                                <label for="status_trabalhado" class="px-3 py-1.5 rounded-xl text-[9px] font-black uppercase tracking-widest cursor-pointer transition-all peer-checked:bg-amber-500 peer-checked:text-slate-900 text-slate-500 hover:text-white block">Trabalhado</label>
                            </div>
                            
                            <div>
                                <input type="radio" name="status_dia" id="status_meio" value="meio_expediente" class="hidden peer">
                                <label for="status_meio" class="px-3 py-1.5 rounded-xl text-[9px] font-black uppercase tracking-widest cursor-pointer transition-all peer-checked:bg-blue-500 peer-checked:text-white text-slate-500 hover:text-white block">Meio Exp.</label>
                            </div>
                            
                            <div>
                                <input type="radio" name="status_dia" id="status_nao" value="nao_trabalhado" class="hidden peer">
                                <label for="status_nao" class="px-3 py-1.5 rounded-xl text-[9px] font-black uppercase tracking-widest cursor-pointer transition-all peer-checked:bg-rose-500 peer-checked:text-white text-slate-500 hover:text-white block">Não Trab.</label>
                            </div>
                        </div>
                        <div class="w-px h-4 bg-white/10 mx-1"></div>
                        <label class="flex items-center gap-2 cursor-pointer group">
                            <input type="checkbox" name="dia_improdutivo" value="1" class="rounded border-white/10 bg-slate-900 text-amber-500 focus:ring-amber-500">
                            <span class="text-[9px] font-bold text-slate-400 group-hover:text-amber-500 transition-colors uppercase tracking-widest">Improdutivo</span>
                        </label>
                    </div>
                </div>
                
                <div class="grid grid-cols-2 sm:grid-cols-4 md:grid-cols-7 gap-3">
                    @foreach(['07:00', '08:00', '09:00', '10:00', '11:00', '12:00', '13:00', '14:00', '15:00', '16:00', '17:00', '18:00', '19:00', '20:00'] as $hora)
                        <div class="space-y-1">
                            <label class="text-[9px] font-black text-slate-500 uppercase block text-center">{{ $hora }}</label>
                            <select name="clima_horario[{{ $hora }}]" class="w-full bg-slate-900/50 border-white/10 rounded-lg text-[10px] text-white focus:border-amber-500 focus:ring-amber-500 px-2 py-1">
                                <option value="-">-</option>
                                <option value="bom">Bom</option>
                                <option value="nublado">Nublado</option>
                                <option value="chuva_l">Chuva L.</option>
                                <option value="chuva_f">Chuva F.</option>
                                <option value="paralisado">Paral.</option>
                            </select>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Sessão 2: Mão de Obra Detalhada -->
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

            <!-- Sessão 3: Maquinário Detalhado -->
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

            <!-- Sessão 4: Atividades (Pre-filled) -->
            <div class="bg-white/5 backdrop-blur-xl border border-white/10 rounded-3xl p-6 shadow-2xl">
                <h3 class="text-sm font-black text-white uppercase tracking-widest mb-6 flex items-center gap-2">
                    <span class="w-2 h-2 bg-green-500 rounded-full"></span>
                    Relato de Atividades
                </h3>
                <div class="space-y-6">
                    <div class="space-y-2">
                        <label class="text-[10px] font-bold text-slate-500 uppercase">Serviços Executados (do Feed)</label>
                        <textarea name="servicos_execucao" rows="6" class="w-full bg-slate-900/50 border-white/10 rounded-2xl text-white text-sm focus:border-amber-500">{{ $todayPosts }}</textarea>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-2">
                            <label class="text-[10px] font-bold text-slate-500 uppercase">Iniciados</label>
                            <textarea name="servicos_iniciados" rows="3" class="w-full bg-slate-900/50 border-white/10 rounded-2xl text-white text-sm focus:border-amber-500"></textarea>
                        </div>
                        <div class="space-y-2">
                            <label class="text-[10px] font-bold text-slate-500 uppercase">Concluídos</label>
                            <textarea name="servicos_concluidos" rows="3" class="w-full bg-slate-900/50 border-white/10 rounded-2xl text-white text-sm focus:border-amber-500"></textarea>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sessão 5: Ocorrências -->
            <div class="bg-white/5 backdrop-blur-xl border border-white/10 rounded-3xl p-6 shadow-2xl">
                <h3 class="text-sm font-black text-white uppercase tracking-widest mb-6 flex items-center gap-2">
                    <span class="w-2 h-2 bg-rose-500 rounded-full"></span>
                    Observações e Ocorrências
                </h3>
                <div class="space-y-6">
                    <div class="space-y-2">
                        <label class="text-[10px] font-bold text-slate-500 uppercase">Materiais Recebidos</label>
                        <textarea name="materiais_recebidos" rows="2" class="w-full bg-slate-900/50 border-white/10 rounded-2xl text-white text-sm focus:border-amber-500" placeholder="Ex: 50 sacos de cimento, 2m³ de areia..."></textarea>
                    </div>
                    <div class="space-y-2">
                        <label class="text-[10px] font-bold text-slate-500 uppercase">Ocorrências / Imprevistos</label>
                        <textarea name="ocorrencias" rows="3" class="w-full bg-slate-900/50 border-white/10 rounded-2xl text-white text-sm focus:border-amber-500"></textarea>
                    </div>
                    <div class="space-y-2">
                        <label class="text-[10px] font-bold text-slate-500 uppercase">Anotações Gerais</label>
                        <textarea name="observacoes" rows="3" class="w-full bg-slate-900/50 border-white/10 rounded-2xl text-white text-sm focus:border-amber-500"></textarea>
                    </div>
                </div>
            </div>

            <button type="submit" class="w-full py-5 bg-amber-500 hover:bg-amber-400 text-slate-900 font-black rounded-[2rem] transition-all shadow-xl shadow-amber-500/20 uppercase tracking-[0.3em] text-sm active:scale-[0.98]">
                Emitir Diário de Obra Oficial
            </button>
        </form>
    </div>
</x-app-layout>
