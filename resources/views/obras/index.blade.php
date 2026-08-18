<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-white leading-tight">
                {{ __('Minhas Obras') }}
            </h2>
            @if(Auth::user()->isChefe())
            <a href="{{ route('obras.create') }}" class="inline-flex items-center flex-shrink-0 px-3 sm:px-4 py-2 min-h-11 bg-amber-500 rounded-xl font-bold text-[10px] sm:text-xs text-slate-900 uppercase tracking-widest hover:bg-amber-400 focus:outline-none focus:ring-2 focus:ring-amber-500 transition ease-in-out duration-150 shadow-lg shadow-amber-500/20">
                <svg class="w-4 h-4 mr-1.5 sm:mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                Nova Obra
            </a>
            @endif
        </div>
    </x-slot>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($obras as $obra)
            <div class="bg-white/5 backdrop-blur-xl border border-white/10 overflow-hidden shadow-2xl rounded-2xl hover:border-amber-500/30 transition-all duration-300 group">
                <div class="p-6">
                    <div class="flex justify-between items-start mb-4">
                        <div>
                            <h3 class="text-lg font-bold text-white group-hover:text-amber-500 transition-colors">{{ $obra->nome }}</h3>
                            <p class="text-xs text-slate-500 mt-1 flex items-center">
                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                </svg>
                                {{ $obra->localizacao_exibicao }}
                            </p>
                        </div>
                        <span class="px-2 py-1 text-[10px] font-bold uppercase rounded-md tracking-wider
                            @if($obra->status === 'concluida') bg-green-500/20 text-green-500 border border-green-500/30
                            @elseif($obra->status === 'paralisada') bg-red-500/20 text-red-500 border border-red-500/30
                            @else bg-amber-500/20 text-amber-500 border border-amber-500/30 @endif">
                            {{ str_replace('_', ' ', $obra->status) }}
                        </span>
                    </div>
                    
                    <div class="space-y-3 mb-6">
                        <div class="flex items-center justify-between text-xs text-slate-400">
                            <span class="flex items-center">
                                <svg class="w-4 h-4 mr-2 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                                Início
                            </span>
                            <span class="text-white font-medium">{{ $obra->data_inicio ? \Carbon\Carbon::parse($obra->data_inicio)->format('d/m/Y') : '--' }}</span>
                        </div>
                        <div class="flex items-center justify-between text-xs text-slate-400">
                            <span class="flex items-center">
                                <svg class="w-4 h-4 mr-2 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                                </svg>
                                Equipe
                            </span>
                            <span class="text-white font-medium">{{ $obra->users_count + $chefesCount }} membros</span>
                        </div>
                    </div>

                    <div class="flex gap-2 pt-4 border-t border-white/5">
                        <form action="{{ route('context.switch') }}" method="POST" class="flex-1">
                            @csrf
                            <input type="hidden" name="obra_id" value="{{ $obra->id }}">
                            <button type="submit" class="w-full text-center py-2 @if(isset($activeObra) && $activeObra->id == $obra->id) bg-amber-500 text-slate-900 @else bg-white/5 text-white @endif hover:bg-amber-400 hover:text-slate-900 rounded-xl text-xs font-bold transition-all border border-white/5 shadow-lg">
                                {{ (isset($activeObra) && $activeObra->id == $obra->id) ? 'Ativa' : 'Selecionar' }}
                            </button>
                        </form>
                        <a href="{{ route('obras.show', $obra) }}" aria-label="Ver obra {{ $obra->nome }}" class="inline-flex items-center justify-center min-h-11 min-w-11 p-2 bg-white/5 hover:bg-white/10 rounded-xl text-slate-400 hover:text-white transition-all border border-white/5">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                        </a>
                        @if(Auth::user()->isChefe())
                        <a href="{{ route('obras.edit', $obra) }}" aria-label="Editar obra {{ $obra->nome }}" class="inline-flex items-center justify-center min-h-11 min-w-11 p-2 bg-white/5 hover:bg-amber-500/20 rounded-xl text-slate-400 hover:text-amber-500 transition-all border border-white/5">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                            </svg>
                        </a>
                        @endif
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-full bg-white/5 backdrop-blur-xl border border-white/10 p-12 text-center rounded-2xl">
                <div class="w-20 h-20 bg-slate-800 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-10 h-10 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                    </svg>
                </div>
                <h3 class="text-xl font-bold text-white">Nenhuma obra encontrada</h3>
                <p class="text-slate-500 mt-2 max-w-xs mx-auto">Você ainda não possui obras cadastradas. Comece agora mesmo!</p>
                @if(Auth::user()->isChefe())
                <a href="{{ route('obras.create') }}" class="inline-flex mt-6 items-center px-6 py-3 bg-amber-500 rounded-xl font-bold text-slate-900 transition-transform hover:scale-105">
                    Cadastrar Primeira Obra
                </a>
                @endif
            </div>
        @endforelse
    </div>

    @if($obras instanceof \Illuminate\Contracts\Pagination\Paginator && $obras->hasPages())
        <div class="mt-8">
            {{ $obras->links() }}
        </div>
    @endif
</x-app-layout>
