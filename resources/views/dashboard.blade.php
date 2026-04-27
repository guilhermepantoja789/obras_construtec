<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-[10px] font-black text-amber-500 uppercase tracking-[0.2em] mb-1">Painel de Controle</p>
                <h2 class="font-black text-xl text-white uppercase tracking-tight leading-tight">
                    {{ $obra->nome }}
                </h2>
            </div>
            <div class="flex items-center gap-2 bg-amber-500/10 px-3 py-1.5 rounded-full border border-amber-500/20">
                <div class="w-2 h-2 rounded-full bg-amber-500 animate-pulse"></div>
                <span class="text-[10px] font-black text-amber-500 uppercase tracking-widest">{{ $obra->status }}</span>
            </div>
        </div>
    </x-slot>

    <div class="space-y-6 pb-12">
        <!-- Main Progress Card -->
        <div class="relative overflow-hidden bg-slate-800/40 backdrop-blur-xl border border-white/10 rounded-[2.5rem] p-8 shadow-2xl">
            <div class="absolute top-0 right-0 -mt-10 -mr-10 w-40 h-40 bg-amber-500/10 rounded-full blur-3xl"></div>
            
            <div class="relative z-10">
                <div class="flex items-end justify-between mb-4">
                    <div>
                        <h3 class="text-3xl font-black text-white tracking-tighter">{{ number_format($stats['progresso_geral'], 0) }}%</h3>
                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mt-1">Progresso Global da Obra</p>
                    </div>
                    <div class="text-right">
                        <p class="text-xs font-bold text-white mb-1">{{ $stats['etapas_concluidas'] }}/{{ $stats['total_etapas'] }}</p>
                        <p class="text-[10px] font-bold text-slate-500 uppercase tracking-widest">Etapas Concluídas</p>
                    </div>
                </div>
                
                <div class="h-4 bg-white/5 rounded-full overflow-hidden border border-white/5 p-1">
                    <div class="h-full bg-gradient-to-r from-amber-600 to-amber-400 rounded-full shadow-[0_0_15px_rgba(245,158,11,0.3)] transition-all duration-1000" style="width: {{ $stats['progresso_geral'] }}%"></div>
                </div>
            </div>
        </div>

        <!-- Quick Stats Grid -->
        <div class="grid grid-cols-2 gap-4">
            <!-- Posts Today -->
            <div class="bg-slate-800/40 backdrop-blur-xl border border-white/10 rounded-3xl p-5 flex flex-col items-center justify-center text-center group active:scale-95 transition-all">
                <div class="w-12 h-12 bg-blue-500/10 rounded-2xl flex items-center justify-center text-blue-500 border border-blue-500/20 mb-3">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                    </svg>
                </div>
                <p class="text-2xl font-black text-white leading-none">{{ $stats['today_posts'] }}</p>
                <p class="text-[9px] font-bold text-slate-500 uppercase tracking-widest mt-2">Posts Hoje</p>
            </div>

            <!-- Team Count -->
            <div class="bg-slate-800/40 backdrop-blur-xl border border-white/10 rounded-3xl p-5 flex flex-col items-center justify-center text-center group active:scale-95 transition-all">
                <div class="w-12 h-12 bg-rose-500/10 rounded-2xl flex items-center justify-center text-rose-500 border border-rose-500/20 mb-3">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                    </svg>
                </div>
                <p class="text-2xl font-black text-white leading-none">{{ $stats['equipe_count'] }}</p>
                <p class="text-[9px] font-bold text-slate-500 uppercase tracking-widest mt-2">Colaboradores</p>
            </div>
        </div>

        <!-- Financial Overview Card -->
        @if(Auth::user()->isChefe())
        <div class="bg-slate-800/40 backdrop-blur-xl border border-white/10 rounded-[2rem] overflow-hidden">
            <div class="p-6 border-b border-white/5">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-xs font-black text-white uppercase tracking-widest">Saúde Financeira</h3>
                    <a href="{{ route('financeiro.index') }}" class="text-[10px] font-bold text-amber-500 uppercase tracking-widest hover:underline">Detalhes</a>
                </div>
                
                <div class="flex items-center gap-4 mb-6">
                    <div class="flex-1">
                        <p class="text-[10px] font-bold text-slate-500 uppercase tracking-widest mb-1">Pago até agora</p>
                        <p class="text-lg font-black text-white">R$ {{ number_format($financeiro['valor_pago'], 2, ',', '.') }}</p>
                    </div>
                    <div class="w-px h-10 bg-white/10"></div>
                    <div class="flex-1 text-right">
                        <p class="text-[10px] font-bold text-slate-500 uppercase tracking-widest mb-1">Saldo Devedor</p>
                        <p class="text-lg font-black text-rose-500">R$ {{ number_format($financeiro['saldo_devedor'], 2, ',', '.') }}</p>
                    </div>
                </div>

                <div class="space-y-2">
                    <div class="flex justify-between items-end">
                        <p class="text-[9px] font-bold text-slate-400 uppercase tracking-widest">Percentual Liquidado</p>
                        <p class="text-xs font-black text-amber-500">{{ number_format($financeiro['percentual_pago'], 1) }}%</p>
                    </div>
                    <div class="h-1.5 bg-white/5 rounded-full overflow-hidden">
                        <div class="h-full bg-amber-500 rounded-full" style="width: {{ $financeiro['percentual_pago'] }}%"></div>
                    </div>
                </div>
            </div>
        </div>
        @endif

        <!-- Next Stages -->
        <div class="space-y-4">
            <div class="flex items-center justify-between px-2">
                <h3 class="text-xs font-black text-white uppercase tracking-widest">Próximas Etapas</h3>
                <a href="{{ route('etapa-obras.index') }}" class="text-[10px] font-bold text-slate-500 uppercase tracking-widest hover:text-white">Ver Cronograma</a>
            </div>
            
            <div class="space-y-3">
                @forelse($proximas_etapas as $etapa)
                    <div class="bg-white/5 border border-white/5 rounded-2xl p-4 flex items-center justify-between group">
                        <div class="flex items-center gap-4">
                            <div class="w-10 h-10 bg-slate-900 rounded-xl flex items-center justify-center text-xs font-black text-amber-500 border border-white/10 group-hover:border-amber-500/30 transition-all">
                                {{ str_pad($etapa->ordem, 2, '0', STR_PAD_LEFT) }}
                            </div>
                            <div>
                                <h4 class="text-sm font-bold text-white mb-0.5">{{ $etapa->nome }}</h4>
                                <div class="flex items-center gap-2">
                                    <span class="text-[9px] font-bold text-slate-500 uppercase tracking-widest">{{ $etapa->status }}</span>
                                    @if($etapa->data_fim_prevista)
                                        <span class="w-1 h-1 rounded-full bg-slate-700"></span>
                                        <span class="text-[9px] font-bold text-slate-500 uppercase tracking-widest">Até {{ $etapa->data_fim_prevista->format('d/m') }}</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="text-right">
                            <p class="text-[10px] font-black text-white">{{ $etapa->percentual_concluido }}%</p>
                        </div>
                    </div>
                @empty
                    <div class="p-8 text-center bg-white/5 rounded-3xl border border-dashed border-white/10">
                        <p class="text-xs text-slate-500 uppercase font-bold tracking-widest">Nenhuma etapa pendente</p>
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Recent Activity -->
        <div class="space-y-4">
            <div class="flex items-center justify-between px-2">
                <h3 class="text-xs font-black text-white uppercase tracking-widest">Atividades Recentes</h3>
                <a href="{{ route('feed.index') }}" class="text-[10px] font-bold text-slate-500 uppercase tracking-widest hover:text-white">Ver Diário</a>
            </div>

            <div class="space-y-4">
                @forelse($recent_posts as $post)
                    <div class="relative pl-6 before:content-[''] before:absolute before:left-0 before:top-2 before:bottom-0 before:w-px before:bg-white/10">
                        <div class="absolute left-[-4px] top-2 w-2 h-2 rounded-full bg-amber-500 border-4 border-slate-900 box-content"></div>
                        <div class="bg-white/5 rounded-2xl p-4">
                            <div class="flex items-center justify-between mb-2">
                                <span class="text-[9px] font-black text-amber-500 uppercase tracking-widest">{{ $post->user->name }}</span>
                                <span class="text-[9px] font-bold text-slate-500 uppercase tracking-widest">{{ $post->data_postagem->diffForHumans() }}</span>
                            </div>
                            <p class="text-xs text-slate-300 line-clamp-2 leading-relaxed">{{ $post->texto }}</p>
                            @if($post->foto_path)
                                <div class="mt-3 rounded-xl overflow-hidden aspect-video border border-white/5">
                                    <img src="{{ asset('storage/' . $post->foto_path) }}" class="w-full h-full object-cover grayscale-[0.3] hover:grayscale-0 transition-all duration-500">
                                </div>
                            @endif

                        </div>
                    </div>
                @empty
                    <p class="text-center py-4 text-xs text-slate-600 uppercase font-bold tracking-widest">Nenhuma atividade registrada</p>
                @endforelse
            </div>
        </div>
    </div>
</x-app-layout>
