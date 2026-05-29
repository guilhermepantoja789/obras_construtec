<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <p class="text-[10px] font-black text-amber-500 uppercase tracking-[0.2em] mb-1">Centro de Orçamentos</p>
                <h2 class="font-black text-xl text-white leading-tight uppercase tracking-tight">
                    Propostas Comerciais
                </h2>
            </div>
            <a href="{{ route('propostas.create') }}" class="flex items-center gap-2 px-4 py-2 bg-amber-500 hover:bg-amber-400 text-slate-900 rounded-xl transition-all shadow-lg shadow-amber-500/20 text-[10px] font-black uppercase tracking-widest active:scale-95 w-full sm:w-auto justify-center">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                Nova Proposta
            </a>
        </div>
    </x-slot>

    <div class="max-w-5xl mx-auto space-y-4 pb-24 px-4 mt-4">
        @forelse($propostas as $proposta)
            <div class="bg-white/5 backdrop-blur-xl border border-white/10 rounded-3xl p-5 shadow-2xl transition-all hover:border-white/20 group relative overflow-hidden">
                <div class="absolute -right-8 -top-8 w-32 h-32 bg-amber-500/5 rounded-full blur-3xl group-hover:bg-amber-500/10 transition-all"></div>
                
                <div class="flex flex-col gap-4 relative z-10">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 rounded-xl bg-amber-500/10 flex flex-col items-center justify-center border border-amber-500/20 shrink-0">
                            <span class="text-[8px] font-black text-amber-500/50 uppercase leading-none mb-0.5">#{{ $proposta->id }}</span>
                            <svg class="w-4 h-4 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                        </div>
                        <div class="min-w-0 flex-1">
                            <h3 class="text-sm font-black text-white group-hover:text-amber-500 transition-colors truncate">{{ $proposta->titulo }}</h3>
                            <div class="flex items-center gap-2 mt-1">
                                <span class="text-[8px] font-black uppercase px-1.5 py-0.5 rounded-full tracking-widest
                                    @if($proposta->status == 'aceita') bg-green-500/10 text-green-500
                                    @elseif($proposta->status == 'recusada') bg-rose-500/10 text-rose-500
                                    @else bg-blue-500/10 text-blue-500 @endif">
                                    {{ $proposta->status }}
                                </span>
                                <span class="text-[9px] text-slate-500 font-bold uppercase tracking-tighter">
                                    {{ $proposta->data_proposta->format('d/m/Y') }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center justify-between pt-4 border-t border-white/5">
                        <div class="text-left">
                            <p class="text-[8px] font-black text-slate-600 uppercase tracking-widest mb-0.5">Valor Total</p>
                            <p class="text-lg font-black text-white">R$ {{ number_format($proposta->valor_total, 2, ',', '.') }}</p>
                        </div>
                        <a href="{{ route('propostas.show', $proposta) }}" class="p-2.5 bg-white/5 hover:bg-white/10 rounded-xl text-slate-400 hover:text-white transition-all active:scale-90 border border-white/5">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                        </a>
                    </div>
                </div>
            </div>
        @empty
            <div class="p-12 text-center border-2 border-dashed border-white/5 rounded-[2.5rem] bg-white/[0.02]">
                <div class="w-16 h-16 bg-white/5 rounded-full flex items-center justify-center mx-auto mb-4 border border-white/5">
                    <svg class="w-8 h-8 text-slate-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                </div>
                <h4 class="text-white font-black text-base mb-1 uppercase tracking-tight">Nenhuma proposta</h4>
                <p class="text-slate-500 text-[10px] max-w-[200px] mx-auto leading-relaxed mb-6 uppercase font-bold tracking-widest">Inicie um orçamento detalhado para sua obra.</p>
                <a href="{{ route('propostas.create') }}" class="inline-flex px-6 py-3 bg-amber-500 hover:bg-amber-400 text-slate-900 font-black rounded-xl shadow-lg shadow-amber-500/20 uppercase tracking-widest text-[10px] transition-all active:scale-95">
                    Criar Proposta
                </a>
            </div>
        @endforelse
    </div>
</x-app-layout>
