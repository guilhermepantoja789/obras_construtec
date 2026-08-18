<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3 min-w-0">
            <a href="{{ route('obras.index') }}" class="sm:hidden flex-shrink-0 p-2 -ml-2 min-h-11 min-w-11 flex items-center justify-center rounded-xl text-slate-400 active:bg-white/10 active:text-white transition-colors" aria-label="Voltar">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                </svg>
            </a>
            <div class="min-w-0">
                <p class="text-[10px] font-black text-amber-500 uppercase tracking-[0.2em] sm:hidden">Edição</p>
                <h2 class="font-semibold text-lg sm:text-xl text-white leading-tight">
                    {{ __('Editar Obra') }}
                </h2>
                <p class="text-xs text-slate-400 truncate">{{ $obra->nome }}</p>
            </div>
        </div>
    </x-slot>

    @include('obras._form', ['obra' => $obra])
</x-app-layout>
