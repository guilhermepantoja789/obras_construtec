<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div class="flex items-center gap-4">
                <div class="p-2 bg-indigo-500/10 rounded-xl text-indigo-500 border border-indigo-500/20">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                </div>
                <div>
                    <p class="text-[10px] font-black text-indigo-500 uppercase tracking-[0.2em] mb-0.5">Gestão de Documentos</p>
                    <h2 class="font-black text-xl text-white leading-tight uppercase tracking-tight">
                        Notas Fiscais
                    </h2>
                </div>
            </div>
            <button @click="$dispatch('open-modal', 'add-nota-modal')" class="px-5 py-2.5 bg-indigo-500 hover:bg-indigo-400 text-white rounded-xl text-[10px] font-black uppercase tracking-widest transition-all shadow-lg shadow-indigo-500/20 active:scale-95 flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                Nova Nota
            </button>
        </div>
    </x-slot>

    <div class="max-w-7xl mx-auto px-4 pb-24 mt-6">
        @if($notas->isEmpty())
            <div class="flex flex-col items-center justify-center py-20 bg-white/5 border border-white/10 rounded-3xl border-dashed">
                <div class="w-16 h-16 bg-slate-800 rounded-full flex items-center justify-center text-slate-600 mb-4">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                </div>
                <h3 class="text-white font-bold mb-1">Nenhuma nota fiscal</h3>
                <p class="text-slate-500 text-xs text-center px-6">Registre suas notas fiscais para manter o controle documental da obra.</p>
            </div>
        @else
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach($notas as $nota)
                    <div class="bg-white/5 backdrop-blur-xl border border-white/10 rounded-3xl p-5 shadow-xl hover:bg-white/10 transition-all group">
                        <div class="flex justify-between items-start mb-4">
                            <div>
                                <p class="text-[9px] font-black text-indigo-500 uppercase tracking-widest mb-1">Nº {{ $nota->numero_nota }}</p>
                                <h4 class="text-white font-black text-sm uppercase leading-tight">{{ $nota->descricao }}</h4>
                            </div>
                            <div class="flex items-center gap-2">
                                @if($nota->arquivo_path)
                                    <a href="{{ asset('storage/' . $nota->arquivo_path) }}" target="_blank" class="p-2 bg-indigo-500/10 text-indigo-400 rounded-lg hover:bg-indigo-500 hover:text-white transition-all">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
                                    </a>
                                @endif
                                <form action="{{ route('nota-fiscals.destroy', $nota) }}" method="POST" onsubmit="return confirm('Tem certeza?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-2 bg-rose-500/10 text-rose-500 rounded-lg hover:bg-rose-500 hover:text-white transition-all">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                    </button>
                                </form>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-4 pt-4 border-t border-white/5">
                            <div>
                                <p class="text-[8px] font-bold text-slate-500 uppercase mb-1">Valor</p>
                                <p class="text-white font-black text-sm">R$ {{ number_format($nota->valor, 2, ',', '.') }}</p>
                            </div>
                            <div class="text-right">
                                <p class="text-[8px] font-bold text-slate-500 uppercase mb-1">Recebido por</p>
                                <p class="text-white font-bold text-[10px]">{{ $nota->quem_recebeu }}</p>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    <x-slot name="modals">
        <!-- Modal Nova Nota (Clean & Reliable) -->
        <x-modal name="add-nota-modal" :show="false">
            <div class="bg-slate-900 min-h-[300px]">
                <div class="p-6 sm:p-10">
                    <div class="flex items-start justify-between mb-8 gap-4">
                        <div class="flex items-center gap-3 sm:gap-4">
                            <div class="w-10 h-10 sm:w-12 sm:h-12 bg-indigo-500/10 rounded-2xl flex items-center justify-center text-indigo-500 border border-indigo-500/20 shrink-0">
                                <svg class="w-5 h-5 sm:w-6 sm:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                            </div>
                            <div>
                                <h2 class="text-lg sm:text-xl font-black text-white uppercase tracking-tight leading-tight">Nova Nota Fiscal</h2>
                                <p class="text-[9px] sm:text-[10px] font-bold text-slate-500 uppercase tracking-widest mt-0.5">Detalhes do documento</p>
                            </div>
                        </div>
                        <button @click="$dispatch('close-modal', 'add-nota-modal')" class="p-2 text-slate-500 hover:text-white transition-colors bg-white/5 rounded-xl shrink-0">
                            <svg class="w-5 h-5 sm:w-6 sm:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                        </button>
                    </div>

                    <form action="{{ route('nota-fiscals.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6 pb-4">
                        @csrf
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                            <div class="space-y-2">
                                <label class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] ml-1">Número da Nota</label>
                                <input type="text" name="numero_nota" required class="w-full bg-slate-800/50 border-white/10 rounded-2xl text-white text-sm focus:border-indigo-500 focus:ring-0 py-3.5 sm:py-4 px-5">
                            </div>
     
                            <div class="space-y-2">
                                <label class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] ml-1">Valor Total (R$)</label>
                                <input type="number" step="0.01" name="valor" required class="w-full bg-slate-800/50 border-white/10 rounded-2xl text-white text-sm focus:border-indigo-500 focus:ring-0 py-3.5 sm:py-4 px-5">
                            </div>
     
                            <div class="sm:col-span-2 space-y-2">
                                <label class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] ml-1">Descrição</label>
                                <input type="text" name="descricao" required class="w-full bg-slate-800/50 border-white/10 rounded-2xl text-white text-sm focus:border-indigo-500 focus:ring-0 py-3.5 sm:py-4 px-5">
                            </div>
     
                            <div class="space-y-2">
                                <label class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] ml-1">Data</label>
                                <input type="date" name="data_recebimento" required value="{{ date('Y-m-d') }}" class="w-full bg-slate-800/50 border-white/10 rounded-2xl text-white text-sm focus:border-indigo-500 focus:ring-0 py-3.5 sm:py-4 px-5">
                            </div>
     
                            <div class="space-y-2">
                                <label class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] ml-1">Recebedor</label>
                                <input type="text" name="quem_recebeu" required class="w-full bg-slate-800/50 border-white/10 rounded-2xl text-white text-sm focus:border-indigo-500 focus:ring-0 py-3.5 sm:py-4 px-5">
                            </div>

                            <div class="sm:col-span-2 space-y-2">
                                <label class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] ml-1">PDF da Nota</label>
                                <div class="bg-slate-800/50 border-white/10 rounded-2xl p-2">
                                    <input type="file" name="arquivo" accept="application/pdf" class="w-full text-white text-[11px] file:bg-indigo-500 file:border-none file:text-white file:text-[10px] file:font-black file:uppercase file:px-6 file:py-3 file:rounded-xl file:mr-4 file:cursor-pointer">
                                </div>
                            </div>

                            <div class="sm:col-span-2 space-y-2">
                                <label class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] ml-1">Observações</label>
                                <textarea name="observacao" rows="3" class="w-full bg-slate-800/50 border-white/10 rounded-2xl text-white text-sm focus:border-indigo-500 focus:ring-0 p-5"></textarea>
                            </div>
                        </div>

                        <div class="flex flex-col sm:flex-row gap-4 pt-6 sm:pt-8 mt-4 border-t border-white/5">
                            <button type="button" @click="$dispatch('close-modal', 'add-nota-modal')" class="flex-1 px-8 py-3.5 sm:py-4 bg-white/5 hover:bg-white/10 text-slate-400 rounded-2xl text-[11px] font-black uppercase tracking-widest transition-all">
                                Descartar
                            </button>
                            <button type="submit" class="flex-[2] px-8 py-3.5 sm:py-4 bg-indigo-500 hover:bg-indigo-400 text-white rounded-2xl text-[11px] font-black uppercase tracking-widest transition-all shadow-xl shadow-indigo-500/20">
                                Salvar Nota
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </x-modal>
    </x-slot>
</x-app-layout>
