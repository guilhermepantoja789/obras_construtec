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
        <div class="mb-8 flex items-center gap-4 p-4 bg-amber-500/10 border border-amber-500/20 rounded-2xl text-amber-500">
            <svg class="w-8 h-8 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <div>
                <p class="text-sm font-black uppercase">Modo de Edição Avançada</p>
                <p class="text-[11px] text-amber-500/70 mt-0.5">Como responsável, você tem acesso para alterar os dados e adicionar/remover fotos retroativamente. Toda edição ficará registrada.</p>
            </div>
        </div>

        <!-- Seção: Gerenciamento de Fotos -->
        <div class="bg-white/5 backdrop-blur-xl border border-white/10 rounded-3xl p-6 shadow-2xl mb-8">
            <h3 class="text-sm font-black text-white uppercase tracking-widest flex items-center gap-2 mb-6">
                <span class="w-2 h-2 bg-pink-500 rounded-full"></span>
                Gerenciamento de Fotos
            </h3>

            <!-- Grid de Fotos Existentes -->
            @if($postsComFoto->count() > 0)
                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-4 mb-6">
                    @foreach($postsComFoto as $post)
                        <div class="relative group aspect-square rounded-2xl overflow-hidden bg-slate-900 border border-white/10">
                            <img src="{{ asset('storage/' . $post->foto_path) }}" class="w-full h-full object-cover transition-transform group-hover:scale-110">
                            
                            <form action="{{ route('diario-posts.destroy', $post) }}" method="POST" class="absolute top-2 right-2 opacity-0 group-hover:opacity-100 transition-opacity" onsubmit="return confirm('Tem certeza que deseja excluir esta foto retroativamente?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="w-8 h-8 bg-rose-500 hover:bg-rose-600 text-white rounded-full flex items-center justify-center shadow-lg">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                </button>
                            </form>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-xs text-slate-500 uppercase tracking-widest font-bold mb-6">Nenhuma foto registrada neste dia.</p>
            @endif

            <!-- Adicionar Nova Foto -->
            <form action="{{ route('diario-reports.add-photo', $diarioReport) }}" method="POST" enctype="multipart/form-data" class="border-t border-white/10 pt-6" x-data="{ photoName: null, photoPreview: null, photoFile: null, submitting: false }" @submit="submitting = true">
                @csrf
                <p class="text-[10px] font-bold text-slate-500 uppercase tracking-widest mb-3">Adicionar Foto Retroativa</p>
                
                <div x-show="photoPreview" class="relative w-full max-w-sm aspect-video rounded-2xl overflow-hidden border border-white/10 mb-4" style="display: none;">
                    <img :src="photoPreview" class="w-full h-full object-cover">
                    <button type="button" @click="photoPreview = null; photoName = null; photoFile = null; $refs.fileInput.value = ''" class="absolute top-2 right-2 p-1.5 bg-rose-500 rounded-full text-white shadow-lg hover:bg-rose-600">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>

                <div class="flex flex-col sm:flex-row gap-4 items-start sm:items-center">
                    <div class="relative group w-full sm:flex-1" x-show="!photoPreview">
                        <input type="file" name="foto" accept="image/*" required x-ref="fileInput" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10"
                               @change="
                                    photoFile = $event.target.files[0];
                                    photoName = photoFile.name;
                                    const reader = new FileReader();
                                    reader.onload = (e) => { photoPreview = e.target.result; };
                                    reader.readAsDataURL(photoFile);
                               ">
                        <div class="px-4 py-3 border-2 border-dashed border-white/10 rounded-xl flex items-center justify-center gap-3 group-hover:border-pink-500/50 transition-all bg-white/[0.02]">
                            <svg class="w-5 h-5 text-slate-500 group-hover:text-pink-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                            <span class="text-xs font-bold text-slate-400 uppercase tracking-widest group-hover:text-pink-500 transition-colors">Selecionar Imagem</span>
                        </div>
                    </div>
                    
                    <button type="submit" x-show="photoPreview" class="w-full sm:w-auto px-6 py-3 bg-pink-600 hover:bg-pink-500 text-white font-black rounded-xl transition-all shadow-lg shadow-pink-500/20 uppercase tracking-widest text-xs" style="display: none;" :disabled="submitting">
                        <span x-show="!submitting">Fazer Upload</span>
                        <span x-show="submitting">Enviando...</span>
                    </button>
                </div>
            </form>
        </div>

        <form action="{{ route('diario-reports.update', $diarioReport) }}" method="POST" class="space-y-8">
            @csrf
            @method('PUT')

            <!-- Seção 1: Auditoria de Tempo -->
            <div class="bg-white/5 backdrop-blur-xl border border-white/10 rounded-3xl p-6 shadow-2xl">
                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
                    <h3 class="text-sm font-black text-white uppercase tracking-widest flex items-center gap-2">
                        <span class="w-2 h-2 bg-amber-500 rounded-full"></span>
                        Auditoria de Tempo (Hora a Hora)
                    </h3>
                    
                    <div class="flex flex-wrap items-center gap-4 bg-slate-900/50 p-1.5 rounded-2xl border border-white/5">
                        <div class="flex items-center gap-1">
                            <div>
                                <input type="radio" name="status_dia" id="status_trabalhado" value="trabalhado" {{ $diarioReport->status_dia === 'trabalhado' ? 'checked' : '' }} class="hidden peer">
                                <label for="status_trabalhado" class="px-3 py-1.5 rounded-xl text-[9px] font-black uppercase tracking-widest cursor-pointer transition-all peer-checked:bg-amber-500 peer-checked:text-slate-900 text-slate-500 hover:text-white block">Trabalhado</label>
                            </div>
                            
                            <div>
                                <input type="radio" name="status_dia" id="status_meio" value="meio_expediente" {{ $diarioReport->status_dia === 'meio_expediente' ? 'checked' : '' }} class="hidden peer">
                                <label for="status_meio" class="px-3 py-1.5 rounded-xl text-[9px] font-black uppercase tracking-widest cursor-pointer transition-all peer-checked:bg-blue-500 peer-checked:text-white text-slate-500 hover:text-white block">Meio Exp.</label>
                            </div>
                            
                            <div>
                                <input type="radio" name="status_dia" id="status_nao" value="nao_trabalhado" {{ $diarioReport->status_dia === 'nao_trabalhado' ? 'checked' : '' }} class="hidden peer">
                                <label for="status_nao" class="px-3 py-1.5 rounded-xl text-[9px] font-black uppercase tracking-widest cursor-pointer transition-all peer-checked:bg-rose-500 peer-checked:text-white text-slate-500 hover:text-white block">Não Trab.</label>
                            </div>
                        </div>
                        <div class="w-px h-4 bg-white/10 mx-1"></div>
                        <label class="flex items-center gap-2 cursor-pointer group">
                            <input type="checkbox" name="dia_improdutivo" value="1" {{ $diarioReport->dia_improdutivo ? 'checked' : '' }} class="rounded border-white/10 bg-slate-900 text-amber-500 focus:ring-amber-500">
                            <span class="text-[9px] font-bold text-slate-400 group-hover:text-amber-500 transition-colors uppercase tracking-widest">Improdutivo</span>
                        </label>
                    </div>
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
