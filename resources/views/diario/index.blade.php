<x-app-layout>
    <div x-data="{ 
        showLightbox: false, 
        lightboxImage: '', 
        lightboxText: '',
        downloadImage() {
            const link = document.createElement('a');
            link.href = this.lightboxImage;
            link.download = 'obra-' + new Date().getTime() + '.jpg';
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
        }
    }">
        <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <p class="text-[10px] font-black text-amber-500 uppercase tracking-[0.2em] mb-1">Diário de Obra</p>
                <h2 class="font-black text-xl text-white leading-tight uppercase tracking-tight">
                    {{ $obra->nome }}
                </h2>
            </div>
            <div class="flex items-center gap-3">
                @if($report)
                    <a href="{{ route('diario-reports.show', $report) }}" class="flex items-center gap-2 px-4 py-2 bg-green-500/10 border border-green-500/20 rounded-full text-green-500 transition-all hover:bg-green-500/20 group">
                        <div class="w-2 h-2 rounded-full bg-green-500 animate-pulse"></div>
                        <span class="text-[10px] font-black uppercase tracking-widest">Relatório Emitido</span>
                        <svg class="w-3 h-3 transition-transform group-hover:translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
                        </svg>
                    </a>
                @else
                    <a href="{{ route('diario-reports.create') }}" class="flex items-center gap-2 px-4 py-2 bg-amber-500/10 border border-amber-500/20 rounded-full text-amber-500 transition-all hover:bg-amber-500/20 group">
                        <div class="w-2 h-2 rounded-full bg-amber-500 animate-pulse"></div>
                        <span class="text-[10px] font-black uppercase tracking-widest">Finalizar Dia</span>
                        <svg class="w-3 h-3 transition-transform group-hover:translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                        </svg>
                    </a>
                @endif
            </div>
        </div>
    </x-slot>

        @if($report)
        <!-- DIA ENCERRADO BANNER -->
        <div class="max-w-2xl mx-auto mb-6">
            <div class="flex items-center gap-4 p-4 bg-green-500/10 border border-green-500/20 rounded-2xl text-green-400">
                <div class="w-10 h-10 bg-green-500/20 rounded-full flex items-center justify-center flex-shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                    </svg>
                </div>
                <div>
                    <p class="text-sm font-black uppercase tracking-wide">Diário Encerrado</p>
                    <p class="text-[11px] text-green-500/80">O relatório oficial deste dia foi emitido. Novas publicações não são permitidas.</p>
                </div>
                <a href="{{ route('diario-reports.pdf', $report) }}" class="ml-auto flex-shrink-0 px-4 py-2 bg-green-500/20 hover:bg-green-500/30 rounded-xl text-[10px] font-black uppercase tracking-widest transition-all flex items-center gap-2">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    Baixar PDF
                </a>
            </div>
        </div>
        @endif

        <div class="max-w-2xl mx-auto space-y-8 pb-12">
            <!-- Timeline Loop -->
            @forelse($posts as $post)
                <div class="bg-white/5 backdrop-blur-xl border border-white/10 rounded-3xl overflow-hidden shadow-2xl transition-all hover:border-white/20">
                    <!-- Header do Post -->
                    <div class="p-4 flex items-center justify-between border-b border-white/5">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-full bg-slate-800 flex items-center justify-center border border-white/10 text-xs font-bold text-slate-300">
                                {{ substr($post->user->name, 0, 1) }}
                            </div>
                            <div>
                                <p class="text-sm font-bold text-white leading-none mb-1">{{ $post->user->name }}</p>
                                <div class="flex items-center gap-2">
                                    <p class="text-[10px] text-slate-500 uppercase tracking-widest">{{ $post->data_postagem->diffForHumans() }}</p>
                                </div>
                            </div>
                        </div>
                        
                        @if(!$report && (auth()->id() === $post->user_id || auth()->user()->role === 'chefe'))
                            <form action="{{ route('diario-posts.destroy', $post) }}" method="POST" onsubmit="return confirm('Excluir esta postagem?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="p-2 text-slate-600 hover:text-red-500 transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                    </svg>
                                </button>
                            </form>
                        @elseif($report)
                            <div class="p-2" title="Diário encerrado">
                                <svg class="w-4 h-4 text-green-600/50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                                </svg>
                            </div>
                        @endif
                    </div>

                    <!-- Imagem -->
                    @if($post->foto_path)
                        <div class="relative aspect-square sm:aspect-video bg-slate-900 overflow-hidden cursor-pointer group/img" 
                             @click="showLightbox = true; lightboxImage = '{{ asset('storage/' . $post->foto_path) }}'; lightboxText = '{{ addslashes(str_replace(["\r", "\n"], ' ', $post->texto)) }}'">
                            <img src="{{ asset('storage/' . $post->foto_path) }}" alt="Foto da obra" class="w-full h-full object-cover transition-transform duration-500 group-hover/img:scale-110">
                            <div class="absolute inset-0 bg-gradient-to-t from-slate-900/60 via-transparent to-transparent opacity-0 group-hover/img:opacity-100 transition-opacity flex items-end p-6">
                                <div class="flex items-center gap-2 text-white text-[10px] font-black uppercase tracking-widest">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                    </svg>
                                    Ver ampliado
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- Conteúdo -->
                    @if($post->texto)
                        <div class="p-6">
                            <p class="text-slate-200 text-sm leading-relaxed font-medium">
                                {!! nl2br(e($post->texto)) !!}
                            </p>
                        </div>
                    @endif

                    <!-- Footer (Ações/Infos) -->
                    <div class="px-6 py-4 bg-white/[0.02] border-t border-white/5 flex items-center justify-between">
                        <div class="flex items-center gap-1 text-slate-500">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <span class="text-[10px] font-bold uppercase tracking-widest">Publicado às {{ $post->data_postagem->format('H:i') }}</span>
                        </div>
                    </div>
                </div>
            @empty
                <div class="bg-white/5 backdrop-blur-xl border border-white/10 rounded-3xl p-16 text-center shadow-2xl">
                    <div class="w-20 h-20 bg-slate-800 rounded-full flex items-center justify-center mx-auto mb-6 border border-white/10">
                        <svg class="w-10 h-10 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-white mb-2">Nenhuma atualização hoje</h3>
                    <p class="text-slate-500 text-sm max-w-xs mx-auto">As postagens de hoje aparecerão aqui. Use o botão de "+" para registrar o progresso atual.</p>
                </div>
            @endforelse
        </div>

        <!-- Lightbox Modal -->
        <div x-show="showLightbox" 
             class="fixed inset-0 z-[110] flex items-center justify-center p-4 sm:p-12"
             style="display: none;"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0">
            
            <!-- Backdrop -->
            <div class="fixed inset-0 bg-slate-950/95 backdrop-blur-xl" @click="showLightbox = false"></div>

            <!-- Content -->
            <div class="relative w-full max-w-5xl max-h-full flex flex-col items-center gap-6" @click.stop>
                <div class="relative group w-full">
                    <img :src="lightboxImage" class="w-full max-h-[70vh] object-contain rounded-2xl shadow-2xl border border-white/10">
                    
                    <!-- Close Button -->
                    <button @click="showLightbox = false" class="absolute -top-4 -right-4 w-10 h-10 bg-white/10 hover:bg-white/20 backdrop-blur-xl rounded-full flex items-center justify-center text-white border border-white/20 transition-all shadow-xl">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>

                <!-- Description & Action -->
                <div class="w-full max-w-2xl bg-white/5 backdrop-blur-xl border border-white/10 p-6 rounded-3xl shadow-2xl flex flex-col sm:flex-row items-center gap-6">
                    <div class="flex-1 text-center sm:text-left">
                        <p class="text-[10px] font-black text-amber-500 uppercase tracking-widest mb-1">Descrição</p>
                        <p class="text-white text-sm font-medium leading-relaxed" x-text="lightboxText || 'Sem descrição.'"></p>
                    </div>
                    
                    <button @click="downloadImage()" class="shrink-0 flex items-center gap-2 px-6 py-3 bg-white/10 hover:bg-white/20 rounded-2xl text-white text-xs font-black uppercase tracking-widest border border-white/10 transition-all active:scale-95">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                        </svg>
                        Salvar
                    </button>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
