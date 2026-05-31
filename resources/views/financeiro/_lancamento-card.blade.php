@php
    $isRecebida = $lancamento['tipo'] === 'recebida';
    $isPendente = !$isRecebida && $lancamento['status'] === 'pendente';
    $categorias = [
        'material' => 'Material',
        'mao_de_obra' => 'Mão de obra',
        'equipamento' => 'Equipamento',
        'servico' => 'Serviço',
        'outros' => 'Outros',
    ];
    $formas = [
        'pix' => 'PIX',
        'boleto' => 'Boleto',
        'dinheiro' => 'Dinheiro',
        'transferencia' => 'Transferência',
        'cartao' => 'Cartão',
    ];
@endphp

<div class="bg-white/5 border border-white/10 rounded-2xl p-4 transition-all">
    <div class="flex items-start gap-3">
        <div class="w-10 h-10 shrink-0 rounded-xl flex items-center justify-center border {{ $isRecebida ? 'bg-green-500/10 text-green-500 border-green-500/20' : ($isPendente ? 'bg-amber-500/10 text-amber-500 border-amber-500/20' : 'bg-slate-500/10 text-slate-400 border-white/10') }}">
            @if($isRecebida)
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 11l5-5m0 0l5 5m-5-5v12"></path></svg>
            @else
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 13l-5 5m0 0l-5-5m5 5V6"></path></svg>
            @endif
        </div>

        <div class="flex-1 min-w-0">
            <div class="flex items-start justify-between gap-2 mb-1">
                <p class="text-xs font-black text-white leading-snug">{{ $lancamento['descricao'] }}</p>
                <span class="shrink-0 text-sm font-black {{ $isRecebida ? 'text-green-500' : ($isPendente ? 'text-amber-400' : 'text-slate-300') }}">
                    {{ $isRecebida ? '+' : '-' }} R$ {{ number_format($lancamento['valor'], 2, ',', '.') }}
                </span>
            </div>

            <div class="flex flex-wrap items-center gap-x-2 gap-y-1 mb-3">
                @if($isPendente)
                    <span class="text-[8px] font-black uppercase tracking-widest bg-amber-500/20 text-amber-400 px-2 py-0.5 rounded-full">A pagar</span>
                @elseif($isRecebida)
                    <span class="text-[8px] font-black uppercase tracking-widest bg-green-500/20 text-green-400 px-2 py-0.5 rounded-full">Recebida</span>
                @else
                    <span class="text-[8px] font-black uppercase tracking-widest bg-white/10 text-slate-400 px-2 py-0.5 rounded-full">Paga</span>
                @endif
                <p class="text-[10px] text-slate-500 font-bold">{{ $lancamento['data']->format('d/m/Y') }}</p>
                @if(!$isRecebida && !empty($lancamento['fornecedor']))
                    <span class="text-[10px] text-slate-600">· {{ $lancamento['fornecedor'] }}</span>
                @endif
                @if(!$isRecebida && !empty($lancamento['categoria']))
                    <span class="text-[10px] text-slate-600">· {{ $categorias[$lancamento['categoria']] ?? $lancamento['categoria'] }}</span>
                @endif
                @if(!empty($lancamento['forma_pagamento']))
                    <span class="text-[10px] text-slate-600">· {{ $formas[$lancamento['forma_pagamento']] ?? $lancamento['forma_pagamento'] }}</span>
                @endif
            </div>

            <div class="flex items-center gap-2">
                @if(!empty($lancamento['comprovante_url']))
                    <a href="{{ $lancamento['comprovante_url'] }}" target="_blank" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-white/5 text-slate-400 rounded-lg text-[10px] font-bold uppercase tracking-widest hover:text-white hover:bg-white/10 transition-all">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"></path></svg>
                        Comprovante
                    </a>
                @endif

                <form
                    action="{{ $isRecebida ? route('pagamentos.destroy', $lancamento['model']) : route('despesas.destroy', $lancamento['model']) }}"
                    method="POST"
                    onsubmit="return confirm('Excluir este registro?')"
                    class="ml-auto"
                >
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-white/5 text-slate-500 rounded-lg text-[10px] font-bold uppercase tracking-widest hover:bg-rose-500/20 hover:text-rose-400 transition-all">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                        Excluir
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
