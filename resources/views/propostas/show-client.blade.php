<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-3">
            <div class="flex items-center gap-3 min-w-0">
                <a href="{{ route('dashboard') }}" class="p-2 bg-white/5 rounded-xl text-slate-400 border border-white/5">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                </a>
                <div class="min-w-0">
                    <p class="text-[9px] font-black text-amber-500 uppercase tracking-widest">Proposta do Cliente</p>
                    <h2 class="font-black text-lg text-white uppercase tracking-tight truncate">{{ $proposta->titulo }}</h2>
                </div>
            </div>
            <span class="text-[9px] font-black uppercase px-2 py-1 rounded
                @if($proposta->status === 'aceita') bg-green-500 text-slate-900
                @elseif($proposta->status === 'enviada') bg-blue-500 text-white
                @else bg-slate-600 text-white @endif">
                {{ $proposta->status }}
            </span>
        </div>
    </x-slot>

    <div class="max-w-5xl mx-auto space-y-4 pb-24 px-4 mt-4">
        <div class="bg-white/5 backdrop-blur-xl border border-white/10 rounded-3xl p-6 shadow-2xl">
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <p class="text-[9px] font-black text-slate-500 uppercase tracking-[0.2em]">Data da proposta</p>
                    <p class="text-sm font-bold text-white mt-1">{{ $proposta->data_proposta->format('d/m/Y') }}</p>
                </div>
                <div>
                    <p class="text-[9px] font-black text-slate-500 uppercase tracking-[0.2em]">Valor total aprovado</p>
                    <p class="text-lg font-black text-white mt-1">R$ {{ number_format($proposta->valor_total, 2, ',', '.') }}</p>
                </div>
                <div>
                    <p class="text-[9px] font-black text-slate-500 uppercase tracking-[0.2em]">Obra</p>
                    <p class="text-sm font-bold text-white mt-1">{{ $proposta->obra->nome ?? 'N/A' }}</p>
                </div>
            </div>

            <div class="pt-5 mt-5 border-t border-white/10">
                <p class="text-[9px] font-black text-slate-500 uppercase tracking-[0.2em] mb-2">Escopo</p>
                <p class="text-xs text-slate-300 leading-relaxed italic break-words">
                    {{ $proposta->escopo ?: 'Sem descrição de escopo.' }}
                </p>
            </div>
        </div>

        <div class="bg-white/5 backdrop-blur-xl border border-white/10 rounded-3xl p-6 shadow-2xl">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-[10px] font-black text-slate-500 uppercase tracking-widest">Itens e etapas</h3>
                <span class="text-[9px] text-slate-500 uppercase font-bold">{{ $items->count() }} itens</span>
            </div>

            <div class="space-y-3">
                @foreach($grupos as $grupo)
                    @php $etapa = $grupo['etapa'] ?? null; @endphp
                    <div class="bg-slate-900/50 border border-white/5 rounded-2xl overflow-hidden">
                        @if($etapa)
                            <div class="px-4 py-3 border-b border-white/5 bg-white/[0.03]">
                                <div class="flex items-center gap-2">
                                    <span class="text-[10px] font-black text-amber-500">{{ $etapa->ordem }}</span>
                                    <span class="text-xs font-black text-white uppercase">{{ $etapa->descricao }}</span>
                                </div>
                            </div>
                        @endif

                        <div class="divide-y divide-white/5">
                            @foreach($grupo['items'] as $item)
                                <div class="px-4 py-3 pl-8 flex items-start gap-2">
                                    <span class="text-[9px] font-black text-slate-600 mt-0.5">{{ $item->ordem }}</span>
                                    <span class="text-xs text-white">{{ $item->descricao }}</span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>

            <p class="text-[9px] text-slate-600 font-bold uppercase mt-5">
                Visualização do cliente: detalhamentos internos de composição e precificação unitária não são exibidos.
            </p>
        </div>
    </div>
</x-app-layout>
