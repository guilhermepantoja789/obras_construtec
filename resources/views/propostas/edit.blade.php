<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3 min-w-0">
            <a href="{{ route('propostas.show', $proposta) }}" class="p-2.5 bg-white/5 rounded-xl text-slate-400 border border-white/5 shrink-0 active:scale-95">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            </a>
            <div class="min-w-0">
                <p class="text-[10px] font-black text-amber-500 uppercase tracking-widest">Editar #{{ $proposta->id }}</p>
                <h2 class="font-black text-lg text-white uppercase tracking-tight truncate">{{ $proposta->titulo }}</h2>
            </div>
        </div>
    </x-slot>

    @include('propostas._form-alpine-script')

    <div class="max-w-lg lg:max-w-7xl mx-auto px-3 pt-2"
        x-data="propostaForm(@js([
            'items' => $proposta->items->values()->map(fn ($item) => [
                'descricao' => $item->descricao,
                'unidade' => $item->unidade,
                'quantidade' => (float) $item->quantidade,
                'valor_unitario' => (float) $item->valor_unitario,
                'is_etapa' => (bool) $item->is_etapa,
                'ordem' => $item->ordem,
            ])->all(),
            'encargos' => $encargosIniciais,
            'importUrl' => route('propostas.import'),
            'csrfToken' => csrf_token(),
        ]))"
        x-init="initPropostaForm()">

        @include('propostas._form', [
            'formAction' => route('propostas.update', $proposta),
            'formMethod' => 'PUT',
            'proposta' => $proposta,
            'submitLabel' => 'Atualizar Proposta',
        ])
    </div>
</x-app-layout>
