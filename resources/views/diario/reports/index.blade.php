<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-row justify-between items-center gap-4">
            <div class="flex items-center gap-4">
                <a href="{{ route('dashboard') }}" class="p-2 bg-white/5 hover:bg-white/10 rounded-xl text-slate-400 hover:text-white transition-all border border-white/5 shadow-lg">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                </a>
                <div>
                    <h2 class="font-black text-xl sm:text-2xl text-white leading-tight tracking-tight uppercase">
                        Diários de Obra
                    </h2>
                    <p class="text-[10px] font-bold text-cyan-500 uppercase tracking-widest">{{ $obra->nome }}</p>
                </div>
            </div>
        </div>
    </x-slot>

    <div class="space-y-6" x-data="{ showFilters: false }">
        
        <!-- Search and Filter Bar -->
        <div class="bg-white/5 backdrop-blur-xl border border-white/10 rounded-2xl p-4 shadow-xl">
            <form action="{{ route('diario-reports.index') }}" method="GET" class="flex flex-col gap-4">
                <div class="flex gap-2">
                    <div class="relative flex-1">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="w-5 h-5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                        </div>
                        <input type="text" name="busca" value="{{ request('busca') }}" placeholder="Buscar por observações, ocorrências ou serviços..." 
                               class="w-full pl-10 pr-4 py-3 bg-slate-900/50 border border-white/10 rounded-xl text-white text-sm focus:border-cyan-500 focus:ring-1 focus:ring-cyan-500 transition-all placeholder-slate-600">
                    </div>
                    <button type="button" @click="showFilters = !showFilters" class="px-4 py-3 bg-white/5 hover:bg-white/10 border border-white/10 rounded-xl text-slate-300 transition-all flex items-center justify-center" :class="showFilters ? 'bg-cyan-500/20 text-cyan-500 border-cyan-500/50' : ''">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path>
                        </svg>
                    </button>
                    <button type="submit" class="px-5 py-3 bg-cyan-600 hover:bg-cyan-500 text-white font-black uppercase tracking-widest text-xs rounded-xl transition-all shadow-lg shadow-cyan-600/20">
                        Buscar
                    </button>
                </div>

                <!-- Advanced Filters -->
                <div x-show="showFilters" x-transition class="pt-4 border-t border-white/10 grid grid-cols-1 sm:grid-cols-3 gap-4" style="display: none;">
                    <div>
                        <label class="text-[10px] font-bold text-slate-500 uppercase tracking-widest mb-1 block">Status</label>
                        <select name="status" class="w-full bg-slate-900/50 border border-white/10 rounded-xl text-white text-sm focus:border-cyan-500 focus:ring-1 focus:ring-cyan-500">
                            <option value="">Todos</option>
                            <option value="trabalhado" {{ request('status') == 'trabalhado' ? 'selected' : '' }}>Trabalhado</option>
                            <option value="meio_expediente" {{ request('status') == 'meio_expediente' ? 'selected' : '' }}>Meio Expediente</option>
                            <option value="nao_trabalhado" {{ request('status') == 'nao_trabalhado' ? 'selected' : '' }}>Não Trabalhado</option>
                        </select>
                    </div>
                    <div>
                        <label class="text-[10px] font-bold text-slate-500 uppercase tracking-widest mb-1 block">Data Inicial</label>
                        <input type="date" name="data_inicio" value="{{ request('data_inicio') }}" class="w-full bg-slate-900/50 border border-white/10 rounded-xl text-white text-sm focus:border-cyan-500 focus:ring-1 focus:ring-cyan-500 [color-scheme:dark]">
                    </div>
                    <div>
                        <label class="text-[10px] font-bold text-slate-500 uppercase tracking-widest mb-1 block">Data Final</label>
                        <input type="date" name="data_fim" value="{{ request('data_fim') }}" class="w-full bg-slate-900/50 border border-white/10 rounded-xl text-white text-sm focus:border-cyan-500 focus:ring-1 focus:ring-cyan-500 [color-scheme:dark]">
                    </div>
                    @if(request()->hasAny(['busca', 'status', 'data_inicio', 'data_fim']))
                        <div class="sm:col-span-3 text-right">
                            <a href="{{ route('diario-reports.index') }}" class="text-[10px] text-slate-400 hover:text-white uppercase font-bold tracking-widest transition-colors">Limpar Filtros</a>
                        </div>
                    @endif
                </div>
            </form>
        </div>

        <!-- Reports List -->
        <div class="space-y-4">
            @forelse($reports as $report)
                <div class="bg-white/5 backdrop-blur-xl border border-white/10 rounded-2xl p-5 shadow-xl transition-all hover:border-cyan-500/30">
                    <div class="flex flex-col sm:flex-row gap-4 justify-between items-start">
                        
                        <!-- Header / Date info -->
                        <div class="flex items-start gap-4">
                            <div class="w-12 h-12 bg-white/5 rounded-xl border border-white/10 flex flex-col items-center justify-center text-center">
                                <span class="text-[10px] text-cyan-500 font-bold uppercase">{{ $report->data_relatorio->translatedFormat('M') }}</span>
                                <span class="text-lg text-white font-black leading-none">{{ $report->data_relatorio->format('d') }}</span>
                            </div>
                            
                            <div>
                                <h3 class="text-white font-bold text-base flex items-center gap-2">
                                    {{ $report->data_relatorio->translatedFormat('l') }}
                                </h3>
                                <div class="flex items-center gap-2 mt-1 flex-wrap">
                                    <span class="text-[9px] font-black uppercase px-2 py-0.5 rounded
                                        @if($report->status_dia == 'trabalhado') bg-emerald-500/10 text-emerald-500 border border-emerald-500/20
                                        @elseif($report->status_dia == 'meio_expediente') bg-blue-500/10 text-blue-500 border border-blue-500/20
                                        @else bg-rose-500/10 text-rose-500 border border-rose-500/20 @endif">
                                        {{ str_replace('_', ' ', $report->status_dia) }}
                                    </span>
                                    
                                    @if($report->dia_improdutivo)
                                        <span class="text-[9px] font-black uppercase px-2 py-0.5 rounded bg-amber-500/10 text-amber-500 border border-amber-500/20">
                                            Improdutivo
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- Content Snippet -->
                        <div class="flex-1 w-full sm:w-auto mt-2 sm:mt-0 sm:ml-4 sm:pl-4 sm:border-l border-white/10">
                            @if($report->servicos_execucao || $report->ocorrencias || $report->observacoes)
                                @php
                                    $snippet = $report->servicos_execucao ?: ($report->ocorrencias ?: $report->observacoes);
                                @endphp
                                <p class="text-sm text-slate-400 line-clamp-2">{{ Str::limit($snippet, 120) }}</p>
                            @else
                                <p class="text-sm text-slate-600 italic">Nenhum detalhe principal registrado.</p>
                            @endif
                        </div>

                        <!-- Actions -->
                        <div class="flex items-center gap-2 w-full sm:w-auto mt-4 sm:mt-0 pt-4 sm:pt-0 border-t border-white/5 sm:border-t-0 justify-end">
                            <a href="{{ route('diario-reports.show', $report) }}" class="px-4 py-2 bg-white/5 hover:bg-white/10 rounded-lg text-xs font-bold text-white uppercase tracking-widest transition-colors flex items-center justify-center flex-1 sm:flex-none">
                                Ver Detalhes
                            </a>
                            
                            @if(Auth::user()->role === 'chefe')
                            <a href="{{ route('diario-reports.edit', $report) }}" class="p-2 bg-amber-500/10 hover:bg-amber-500/20 rounded-lg text-amber-500 transition-colors" title="Editar">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                </svg>
                            </a>
                            @endif
                        </div>
                    </div>
                </div>
            @empty
                <div class="bg-white/5 backdrop-blur-xl border border-white/10 rounded-2xl p-12 text-center shadow-xl">
                    <div class="w-20 h-20 bg-white/5 rounded-full flex items-center justify-center mx-auto mb-6 border border-white/10">
                        <svg class="w-10 h-10 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                        </svg>
                    </div>
                    <h3 class="text-lg font-black text-white uppercase tracking-widest mb-2">Nenhum Diário Encontrado</h3>
                    <p class="text-sm text-slate-400 max-w-md mx-auto">
                        @if(request()->hasAny(['busca', 'status', 'data_inicio', 'data_fim']))
                            Não encontramos nenhum registro correspondente aos filtros aplicados.
                        @else
                            Nenhum relatório diário foi emitido para esta obra ainda.
                        @endif
                    </p>
                    
                    @if(request()->hasAny(['busca', 'status', 'data_inicio', 'data_fim']))
                        <a href="{{ route('diario-reports.index') }}" class="inline-block mt-6 px-6 py-3 bg-cyan-600/20 text-cyan-500 font-bold uppercase tracking-widest text-xs rounded-xl hover:bg-cyan-600/30 transition-colors">
                            Limpar Filtros
                        </a>
                    @endif
                </div>
            @endforelse
        </div>

        <!-- Pagination -->
        @if($reports->hasPages())
            <div class="mt-6">
                {{ $reports->links() }}
            </div>
        @endif
        
    </div>
</x-app-layout>
