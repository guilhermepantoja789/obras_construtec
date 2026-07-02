<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-4">
            <div class="p-2 bg-indigo-500/10 rounded-xl text-indigo-500 border border-indigo-500/20 shrink-0">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
            </div>
            <div>
                <p class="text-[10px] font-black text-indigo-500 uppercase tracking-[0.2em] mb-0.5">Gestão de Documentos</p>
                <h2 class="font-black text-xl text-white leading-tight uppercase tracking-tight">
                    Notas Fiscais
                </h2>
                <p class="text-[10px] font-bold text-slate-500 uppercase tracking-widest mt-0.5">{{ $obra->nome }}</p>
            </div>
        </div>
    </x-slot>

    <div class="max-w-2xl mx-auto space-y-4 pb-24 px-4" x-data="{ showCustom: {{ $periodoAtivo === 'custom' ? 'true' : 'false' }} }">

        {{-- Hero: resumo do período --}}
        <div class="bg-white/5 backdrop-blur-xl border border-white/10 rounded-3xl p-6 shadow-2xl overflow-hidden relative" x-data="{
            isGenerating: false,
            async downloadPdf() {
                if (this.isGenerating) return;
                this.isGenerating = true;

                try {
                    const response = await fetch('{{ route('nota-fiscals.pdf', request()->query()) }}');
                    if (!response.ok) throw new Error('Falha ao gerar PDF');

                    const blob = await response.blob();
                    const url = window.URL.createObjectURL(blob);
                    const fileName = 'notas-fiscais-obra-{{ $obra->id }}-{{ $dataInicio->format('Y-m-d') }}-{{ $dataFim->format('Y-m-d') }}.pdf';

                    if (navigator.share && navigator.canShare && navigator.canShare({ files: [new File([blob], fileName, { type: 'application/pdf' })] })) {
                        const file = new File([blob], fileName, { type: 'application/pdf' });
                        await navigator.share({
                            files: [file],
                            title: 'Notas Fiscais',
                            text: 'Relatório de notas fiscais da obra {{ $obra->nome }}'
                        });
                    } else {
                        const a = document.createElement('a');
                        a.href = url;
                        a.download = fileName;
                        document.body.appendChild(a);
                        a.click();
                        window.URL.revokeObjectURL(url);
                        document.body.removeChild(a);
                    }
                } catch (error) {
                    alert('Erro ao gerar PDF. Tente novamente.');
                } finally {
                    this.isGenerating = false;
                }
            }
        }">
            <div class="absolute -right-8 -top-8 w-32 h-32 bg-indigo-500/5 rounded-full blur-3xl"></div>

            <div class="relative z-10">
                <div class="flex items-start justify-between gap-3 mb-4">
                    <div>
                        <p class="text-[10px] font-black text-slate-500 uppercase tracking-[0.2em] mb-1">Período</p>
                        <p class="text-sm font-bold text-slate-400">
                            {{ $dataInicio->format('d/m') }} – {{ $dataFim->format('d/m/Y') }}
                        </p>
                    </div>
                    <button
                        type="button"
                        @click="downloadPdf()"
                        :disabled="isGenerating"
                        class="shrink-0 flex items-center gap-2 px-4 py-2.5 bg-indigo-500/15 hover:bg-indigo-500/25 border border-indigo-500/30 rounded-xl text-[10px] font-black uppercase tracking-widest text-indigo-300 transition-all active:scale-95 disabled:opacity-50"
                    >
                        <template x-if="!isGenerating">
                            <div class="flex items-center gap-2">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                <span>Exportar PDF</span>
                            </div>
                        </template>
                        <template x-if="isGenerating">
                            <div class="flex items-center gap-2">
                                <svg class="w-3.5 h-3.5 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
                                <span>Gerando...</span>
                            </div>
                        </template>
                    </button>
                </div>

                <p class="text-[10px] font-black text-slate-500 uppercase tracking-[0.2em] mb-1">Valor Total</p>
                <h3 class="text-4xl font-black text-white leading-none mb-4">
                    R$ {{ number_format($totalValor, 2, ',', '.') }}
                </h3>

                <div class="inline-flex items-center gap-2 bg-indigo-500/10 border border-indigo-500/20 rounded-xl px-3 py-2">
                    <svg class="w-4 h-4 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                    <span class="text-xs font-black text-indigo-400 uppercase tracking-widest">
                        {{ $totalCount }} {{ $totalCount === 1 ? 'nota fiscal' : 'notas fiscais' }}
                    </span>
                </div>
            </div>
        </div>

        {{-- Filtros por preset --}}
        <div class="bg-white/5 backdrop-blur-xl border border-white/10 rounded-2xl p-4 shadow-xl space-y-4">
            <p class="text-[10px] font-black text-slate-500 uppercase tracking-widest px-1">Filtrar período</p>

            @php
                $presets = [
                    'este_mes' => 'Este mês',
                    'mes_anterior' => 'Mês anterior',
                    'ultimos_30' => 'Últimos 30 dias',
                    'custom' => 'Personalizado',
                ];
            @endphp

            <div class="grid grid-cols-2 gap-2">
                @foreach($presets as $key => $label)
                    @if($key === 'custom')
                        <button
                            type="button"
                            @click="showCustom = !showCustom"
                            class="w-full px-4 py-4 rounded-2xl text-left transition-all active:scale-[0.98] border
                                {{ $periodoAtivo === 'custom' ? 'bg-indigo-500/15 text-indigo-300 border-indigo-500/40 ring-1 ring-indigo-500/30' : 'bg-slate-900/40 text-slate-400 border-white/5' }}"
                        >
                            <span class="block text-xs font-black uppercase tracking-wide leading-tight">{{ $label }}</span>
                            @if($periodoAtivo === 'custom')
                                <span class="block text-[9px] font-bold text-indigo-400/80 mt-1 normal-case tracking-normal">Intervalo ativo</span>
                            @endif
                        </button>
                    @else
                        <a
                            href="{{ route('nota-fiscals.index', ['periodo' => $key]) }}"
                            class="w-full px-4 py-4 rounded-2xl text-left transition-all active:scale-[0.98] border
                                {{ $periodoAtivo === $key ? 'bg-indigo-500/15 text-indigo-300 border-indigo-500/40 ring-1 ring-indigo-500/30' : 'bg-slate-900/40 text-slate-400 border-white/5' }}"
                        >
                            <span class="block text-xs font-black uppercase tracking-wide leading-tight">{{ $label }}</span>
                        </a>
                    @endif
                @endforeach
            </div>

            {{-- Intervalo customizado --}}
            <div x-show="showCustom" x-transition class="pt-4 border-t border-white/10 space-y-4" style="display: none;">
                <form action="{{ route('nota-fiscals.index') }}" method="GET" class="space-y-4">
                    <input type="hidden" name="periodo" value="custom">

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="text-[10px] font-bold text-slate-500 uppercase tracking-widest mb-1 block">Data Inicial</label>
                            <input
                                type="date"
                                name="data_inicio"
                                value="{{ $periodoAtivo === 'custom' ? $dataInicio->format('Y-m-d') : '' }}"
                                required
                                class="w-full bg-slate-900/50 border border-white/10 rounded-xl text-white text-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 py-3 px-3 [color-scheme:dark]"
                            >
                        </div>
                        <div>
                            <label class="text-[10px] font-bold text-slate-500 uppercase tracking-widest mb-1 block">Data Final</label>
                            <input
                                type="date"
                                name="data_fim"
                                value="{{ $periodoAtivo === 'custom' ? $dataFim->format('Y-m-d') : '' }}"
                                required
                                class="w-full bg-slate-900/50 border border-white/10 rounded-xl text-white text-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 py-3 px-3 [color-scheme:dark]"
                            >
                        </div>
                    </div>

                    <div class="flex gap-2">
                        <button type="submit" class="flex-1 px-4 py-3 bg-indigo-500 text-white font-black uppercase tracking-widest text-[10px] rounded-xl transition-all active:scale-95 shadow-lg shadow-indigo-500/20">
                            Aplicar
                        </button>
                        @if($periodoAtivo !== 'este_mes')
                            <a href="{{ route('nota-fiscals.index', ['periodo' => 'este_mes']) }}" class="px-4 py-3 bg-white/5 text-slate-400 font-black uppercase tracking-widest text-[10px] rounded-xl transition-all active:scale-95 border border-white/10">
                                Limpar
                            </a>
                        @endif
                    </div>
                </form>
            </div>
        </div>

        {{-- Lista de notas --}}
        @if($notas->isEmpty())
            <div class="flex flex-col items-center justify-center py-16 bg-white/5 border border-white/10 rounded-3xl border-dashed px-6">
                <div class="w-14 h-14 bg-slate-800 rounded-full flex items-center justify-center text-slate-600 mb-4">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                </div>

                @if($totalNotasObra === 0)
                    <h3 class="text-white font-bold mb-1 text-center">Nenhuma nota fiscal</h3>
                    <p class="text-slate-500 text-xs text-center leading-relaxed">
                        Registre suas notas fiscais pelo botão <span class="text-indigo-400 font-bold">+</span> no menu inferior.
                    </p>
                @else
                    <h3 class="text-white font-bold mb-1 text-center">Nenhuma nota neste período</h3>
                    <p class="text-slate-500 text-xs text-center leading-relaxed">
                        Tente outro período ou ajuste o intervalo personalizado.
                    </p>
                @endif
            </div>
        @else
            <div class="space-y-3">
                <h3 class="text-[10px] font-black text-slate-500 uppercase tracking-widest flex items-center gap-2 px-1">
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"></path></svg>
                    Notas do Período
                </h3>

                @foreach($notas as $nota)
                    <div class="bg-white/5 backdrop-blur-xl border border-white/10 rounded-2xl p-4 shadow-xl active:scale-[0.99] transition-transform">
                        <div class="flex items-start gap-3">
                            {{-- Badge de data --}}
                            <div class="w-12 h-12 bg-white/5 rounded-xl border border-white/10 flex flex-col items-center justify-center text-center shrink-0">
                                <span class="text-[9px] text-indigo-400 font-bold uppercase">{{ $nota->data_recebimento->translatedFormat('M') }}</span>
                                <span class="text-lg text-white font-black leading-none">{{ $nota->data_recebimento->format('d') }}</span>
                            </div>

                            <div class="flex-1 min-w-0">
                                <div class="flex justify-between items-start gap-2">
                                    <div class="min-w-0">
                                        <p class="text-[9px] font-black text-indigo-400 uppercase tracking-widest mb-0.5">Nº {{ $nota->numero_nota }}</p>
                                        <h4 class="text-white font-bold text-sm leading-tight truncate">{{ $nota->descricao }}</h4>
                                        <p class="text-[10px] text-slate-500 font-bold uppercase tracking-tighter mt-1">{{ $nota->quem_recebeu }}</p>
                                    </div>
                                    <p class="text-sm font-black text-white shrink-0">
                                        R$ {{ number_format($nota->valor, 2, ',', '.') }}
                                    </p>
                                </div>

                                <div class="flex items-center gap-2 mt-3 pt-3 border-t border-white/5">
                                    @if($nota->arquivo_path)
                                        <a
                                            href="{{ asset('storage/' . $nota->arquivo_path) }}"
                                            target="_blank"
                                            class="flex items-center gap-1.5 px-3 py-2 bg-indigo-500/10 text-indigo-400 rounded-xl text-[10px] font-black uppercase tracking-widest active:scale-95 transition-transform"
                                        >
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
                                            Ver PDF
                                        </a>
                                    @endif

                                    <button
                                        type="button"
                                        @click="$dispatch('edit-nota-date', {
                                            id: {{ $nota->id }},
                                            numero: @js($nota->numero_nota),
                                            descricao: @js($nota->descricao),
                                            data: @js($nota->data_recebimento->format('Y-m-d'))
                                        }); $dispatch('open-modal', 'edit-nota-date-modal')"
                                        class="flex items-center gap-1.5 px-3 py-2 bg-white/5 text-slate-300 rounded-xl text-[10px] font-black uppercase tracking-widest active:scale-95 transition-transform"
                                    >
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                        Alterar data
                                    </button>

                                    <form action="{{ route('nota-fiscals.destroy', $nota) }}" method="POST" class="ml-auto" onsubmit="return confirm('Tem certeza que deseja excluir esta nota?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="flex items-center gap-1.5 px-3 py-2 bg-rose-500/10 text-rose-500 rounded-xl text-[10px] font-black uppercase tracking-widest active:scale-95 transition-transform">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    <x-slot name="modals">
        @php
            $pendingEditNota = old('nota_id') ? $notas->firstWhere('id', (int) old('nota_id')) : null;
        @endphp

        <!-- Modal Alterar Data -->
        <x-modal name="edit-nota-date-modal" :show="$errors->has('data_recebimento') && old('nota_id')">
            <div
                class="bg-slate-900 min-h-[200px]"
                x-data="{
                    editingNota: @js($pendingEditNota ? [
                        'id' => $pendingEditNota->id,
                        'numero' => $pendingEditNota->numero_nota,
                        'descricao' => $pendingEditNota->descricao,
                        'data' => old('data_recebimento', $pendingEditNota->data_recebimento->format('Y-m-d')),
                    ] : null)
                }"
                x-on:edit-nota-date.window="editingNota = $event.detail"
            >
                <div class="p-6 sm:p-10">
                    <div class="flex items-start justify-between mb-8 gap-4">
                        <div class="flex items-center gap-3 sm:gap-4">
                            <div class="w-10 h-10 sm:w-12 sm:h-12 bg-indigo-500/10 rounded-2xl flex items-center justify-center text-indigo-500 border border-indigo-500/20 shrink-0">
                                <svg class="w-5 h-5 sm:w-6 sm:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                            </div>
                            <div>
                                <h2 class="text-lg sm:text-xl font-black text-white uppercase tracking-tight leading-tight">Alterar Data</h2>
                                <p class="text-[9px] sm:text-[10px] font-bold text-slate-500 uppercase tracking-widest mt-0.5" x-show="editingNota" x-text="'Nº ' + (editingNota?.numero ?? '')"></p>
                            </div>
                        </div>
                        <button @click="$dispatch('close-modal', 'edit-nota-date-modal')" class="p-2 text-slate-500 hover:text-white transition-colors bg-white/5 rounded-xl shrink-0">
                            <svg class="w-5 h-5 sm:w-6 sm:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                        </button>
                    </div>

                    <template x-if="editingNota">
                        <form :action="`{{ url('nota-fiscals') }}/${editingNota.id}`" method="POST" class="space-y-6 pb-4">
                            @csrf
                            @method('PUT')
                            <input type="hidden" name="nota_id" :value="editingNota.id">

                            <div class="space-y-2">
                                <label class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] ml-1">Nota</label>
                                <p class="text-sm font-bold text-white px-1" x-text="editingNota.descricao"></p>
                            </div>

                            <div class="space-y-2">
                                <label class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] ml-1">Data de Recebimento</label>
                                <input
                                    type="date"
                                    name="data_recebimento"
                                    required
                                    x-model="editingNota.data"
                                    class="w-full bg-slate-800/50 border-white/10 rounded-2xl text-white text-sm focus:border-indigo-500 focus:ring-0 py-3.5 sm:py-4 px-5 [color-scheme:dark]"
                                >
                                @error('data_recebimento')
                                    <p class="text-xs text-rose-400 mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="flex flex-col sm:flex-row gap-4 pt-6 sm:pt-8 mt-4 border-t border-white/5">
                                <button type="button" @click="$dispatch('close-modal', 'edit-nota-date-modal')" class="flex-1 px-8 py-3.5 sm:py-4 bg-white/5 hover:bg-white/10 text-slate-400 rounded-2xl text-[11px] font-black uppercase tracking-widest transition-all">
                                    Cancelar
                                </button>
                                <button type="submit" class="flex-[2] px-8 py-3.5 sm:py-4 bg-indigo-500 hover:bg-indigo-400 text-white rounded-2xl text-[11px] font-black uppercase tracking-widest transition-all shadow-xl shadow-indigo-500/20">
                                    Salvar Data
                                </button>
                            </div>
                        </form>
                    </template>
                </div>
            </div>
        </x-modal>

        <!-- Modal Nova Nota (Clean & Reliable) -->
        <x-modal name="add-nota-modal" :show="$errors->hasAny(['numero_nota', 'descricao', 'valor', 'quem_recebeu', 'arquivo', 'observacao']) || ($errors->has('data_recebimento') && !old('nota_id'))">
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
                                <input type="text" name="numero_nota" required value="{{ old('numero_nota') }}" class="w-full bg-slate-800/50 border-white/10 rounded-2xl text-white text-sm focus:border-indigo-500 focus:ring-0 py-3.5 sm:py-4 px-5">
                            </div>
     
                            <div class="space-y-2">
                                <label class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] ml-1">Valor Total (R$)</label>
                                <input type="number" step="0.01" name="valor" required value="{{ old('valor') }}" class="w-full bg-slate-800/50 border-white/10 rounded-2xl text-white text-sm focus:border-indigo-500 focus:ring-0 py-3.5 sm:py-4 px-5">
                            </div>
     
                            <div class="sm:col-span-2 space-y-2">
                                <label class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] ml-1">Descrição</label>
                                <input type="text" name="descricao" required value="{{ old('descricao') }}" class="w-full bg-slate-800/50 border-white/10 rounded-2xl text-white text-sm focus:border-indigo-500 focus:ring-0 py-3.5 sm:py-4 px-5">
                            </div>
     
                            <div class="space-y-2">
                                <label class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] ml-1">Data</label>
                                <input type="date" name="data_recebimento" required value="{{ old('data_recebimento', date('Y-m-d')) }}" class="w-full bg-slate-800/50 border-white/10 rounded-2xl text-white text-sm focus:border-indigo-500 focus:ring-0 py-3.5 sm:py-4 px-5">
                            </div>
     
                            <div class="space-y-2">
                                <label class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] ml-1">Recebedor</label>
                                <input type="text" name="quem_recebeu" required value="{{ old('quem_recebeu') }}" class="w-full bg-slate-800/50 border-white/10 rounded-2xl text-white text-sm focus:border-indigo-500 focus:ring-0 py-3.5 sm:py-4 px-5">
                            </div>

                            <div class="sm:col-span-2 space-y-2">
                                <label class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] ml-1">PDF da Nota</label>
                                <div class="bg-slate-800/50 border-white/10 rounded-2xl p-2">
                                    <input type="file" name="arquivo" accept="application/pdf" class="w-full text-white text-[11px] file:bg-indigo-500 file:border-none file:text-white file:text-[10px] file:font-black file:uppercase file:px-6 file:py-3 file:rounded-xl file:mr-4 file:cursor-pointer">
                                </div>
                            </div>

                            <div class="sm:col-span-2 space-y-2">
                                <label class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] ml-1">Observações</label>
                                <textarea name="observacao" rows="3" class="w-full bg-slate-800/50 border-white/10 rounded-2xl text-white text-sm focus:border-indigo-500 focus:ring-0 p-5">{{ old('observacao') }}</textarea>
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
