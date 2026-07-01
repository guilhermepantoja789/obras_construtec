<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <a href="{{ route('propostas.index') }}" class="p-2.5 bg-white/5 rounded-xl text-slate-400 border border-white/5 active:scale-95">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            </a>
            <div>
                <p class="text-[10px] font-black text-amber-500 uppercase tracking-widest">Nova Proposta</p>
                <h2 class="font-black text-lg text-white uppercase tracking-tight">{{ $obra->nome }}</h2>
            </div>
        </div>
    </x-slot>

    @include('propostas._form-alpine-script')

    <div class="max-w-lg lg:max-w-7xl mx-auto px-3 pt-2"
        x-data="propostaForm(@js([
            'items' => [['descricao' => '', 'unidade' => 'un', 'quantidade' => 1, 'valor_unitario' => 0, 'is_etapa' => false, 'ordem' => '1']],
            'encargos' => $encargosIniciais,
            'importUrl' => route('propostas.import'),
            'csrfToken' => csrf_token(),
        ]))"
        x-init="initPropostaForm()">

        @include('propostas._form', [
            'formAction' => route('propostas.store'),
            'formMethod' => 'POST',
            'submitLabel' => 'Salvar Proposta',
        ])
    </div>
</x-app-layout>
