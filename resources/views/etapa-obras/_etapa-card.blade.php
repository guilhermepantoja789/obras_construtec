@props(['etapa', 'depth' => 0, 'isChild' => false])

@php
    $depth = $depth ?: $etapa->ordemDepth();
    $indent = max(0, $depth - 1) * 1.25;
@endphp

<div class="bg-white/5 backdrop-blur-xl border border-white/10 rounded-2xl p-5 shadow-xl transition-all hover:border-white/20 group relative overflow-hidden {{ $isChild ? 'ml-4 sm:ml-6 border-l-2 border-l-blue-500/30' : '' }}"
    style="{{ $indent > 0 ? 'margin-left: ' . $indent . 'rem' : '' }}"
    data-etapa-id="{{ $etapa->id }}"
>
    <div class="flex items-start gap-4 relative z-10">
        @if(Auth::user()->isChefe())
        <div class="shrink-0 mt-2 cursor-grab active:cursor-grabbing text-slate-600 hover:text-slate-400" title="Arrastar para reordenar"
            draggable="true"
            @dragstart.stop="$dispatch('etapa-drag-start', {{ $etapa->id }})"
            @dragover.prevent
            @drop.prevent.stop="$dispatch('etapa-drop', {{ $etapa->id }})"
        >
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8h16M4 16h16"></path></svg>
        </div>
        @endif
        <div class="shrink-0 mt-1">
            <div class="w-10 h-10 rounded-xl flex items-center justify-center border transition-all
                @if($etapa->status == 'concluida') bg-green-500/10 border-green-500/20 text-green-500
                @elseif($etapa->status == 'em_progresso') bg-blue-500/10 border-blue-500/20 text-blue-500
                @elseif($etapa->status == 'atrasada') bg-rose-500/10 border-rose-500/20 text-rose-500
                @else bg-slate-800 border-white/10 text-slate-500 @endif">
                <span class="text-[10px] font-black">{{ $etapa->ordem ?: '—' }}</span>
            </div>
        </div>
        <div class="flex-1 min-w-0">
            <div class="flex justify-between items-start mb-2 gap-2">
                <div class="min-w-0">
                    <div class="flex items-center gap-2 flex-wrap">
                        <h4 class="text-white font-bold truncate">{{ $etapa->nome }}</h4>
                        @if($etapa->isFromProposta())
                            <span class="text-[8px] font-black uppercase tracking-widest px-1.5 py-0.5 rounded bg-amber-500/10 text-amber-500 border border-amber-500/20">Proposta</span>
                        @endif
                    </div>
                    <p class="text-[9px] text-slate-500 uppercase font-black tracking-widest mt-0.5">
                        @if($etapa->data_inicio_prevista)
                            {{ $etapa->data_inicio_prevista->format('d/m/Y') }}
                            @if($etapa->data_fim_prevista)
                                → {{ $etapa->data_fim_prevista->format('d/m/Y') }}
                            @endif
                        @else
                            Sem data prevista
                        @endif
                        @if($etapa->valor > 0)
                            · R$ {{ number_format($etapa->valor, 2, ',', '.') }}
                        @endif
                    </p>
                </div>
                @if(Auth::user()->isChefe())
                <div class="flex items-center gap-1 shrink-0">
                    <button type="button" @click="openEditModal({{ json_encode($etapa) }})" class="p-2 bg-white/5 hover:bg-white/10 rounded-lg text-slate-400 transition-colors active:scale-90" title="Editar">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                    </button>
                    <form action="{{ route('etapa-obras.destroy', $etapa) }}" method="POST" onsubmit="return confirm('Excluir a etapa «{{ addslashes($etapa->nome) }}»?')">
                        @csrf @method('DELETE')
                        <button type="submit" class="p-2 bg-white/5 hover:bg-rose-500/20 rounded-lg text-slate-400 hover:text-rose-500 transition-colors active:scale-90" title="Excluir">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                        </button>
                    </form>
                </div>
                @endif
            </div>
            <div class="flex justify-between items-center text-[10px] font-black uppercase tracking-tighter mb-1.5 mt-3">
                <span class="text-slate-500">Andamento · {{ str_replace('_', ' ', $etapa->status) }}</span>
                <span class="text-white">{{ $etapa->percentual_concluido }}%</span>
            </div>
            <div class="w-full h-1.5 bg-slate-900 rounded-full overflow-hidden border border-white/5">
                <div class="h-full bg-blue-500 shadow-[0_0_10px_rgba(59,130,246,0.5)]" style="width: {{ $etapa->percentual_concluido }}%"></div>
            </div>
        </div>
    </div>
</div>
