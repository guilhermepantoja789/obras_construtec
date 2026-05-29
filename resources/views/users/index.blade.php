<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-row justify-between items-center gap-2">
            <h2 class="font-black text-lg sm:text-2xl text-white leading-tight tracking-tight uppercase">
                <span class="text-amber-500">Equipe</span> <span class="text-slate-400">&</span> Clientes
            </h2>
            <a href="{{ route('users.create') }}" class="inline-flex justify-center items-center px-3 py-1.5 bg-blue-600 rounded-lg font-bold text-[9px] sm:text-[10px] text-white uppercase tracking-widest hover:bg-blue-500 transition shadow-lg shadow-blue-500/20 whitespace-nowrap">
                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"></path>
                </svg>
                Novo
            </a>
        </div>
    </x-slot>

    <!-- Filtros e Busca -->
    <div class="mb-6">
        <form action="{{ route('users.index') }}" method="GET" class="relative group">
            <x-text-input name="search" :value="request('search')" placeholder="Buscar por nome ou e-mail..." class="pl-12 w-full" />
            <div class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-500 group-focus-within:text-amber-500 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                </svg>
            </div>
            @if(request('search'))
                <a href="{{ route('users.index') }}" class="absolute right-4 top-1/2 -translate-y-1/2 text-xs text-slate-500 hover:text-white uppercase font-bold">Limpar</a>
            @endif
        </form>
    </div>

    @if(session('success'))
        <div class="mb-6 p-4 bg-green-500/10 border border-green-500/50 rounded-2xl text-green-500 text-sm backdrop-blur-xl">
            {{ session('success') }}
        </div>
    @endif

    <!-- Versão Mobile: Cards -->
    <div class="grid grid-cols-1 gap-4 sm:hidden">
        @forelse($users as $user)
            <div class="bg-white/5 backdrop-blur-xl border border-white/10 p-5 rounded-2xl shadow-xl">
                <div class="flex items-start justify-between mb-4">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-full bg-slate-800 flex items-center justify-center text-sm font-bold text-slate-300 border border-white/10 shadow-inner">
                            {{ substr($user->name, 0, 1) }}
                        </div>
                        <div>
                            <h4 class="text-white font-bold">{{ $user->name }}</h4>
                            <p class="text-xs text-slate-500">{{ $user->email }}</p>
                        </div>
                    </div>
                    <span class="px-2 py-0.5 text-[9px] font-bold uppercase rounded-md tracking-wider
                        @if($user->role === 'chefe') bg-purple-500/20 text-purple-400 border border-purple-500/30
                        @elseif($user->role === 'operador') bg-blue-500/20 text-blue-400 border border-blue-500/30
                        @else bg-green-500/20 text-green-400 border border-green-500/30 @endif">
                        {{ $user->role }}
                    </span>
                </div>
                
                <div class="mb-6">
                    <p class="text-[10px] font-bold text-slate-500 uppercase tracking-widest mb-2">Obras Vinculadas</p>
                    <div class="flex flex-wrap gap-1">
                        @forelse($user->obras as $obra)
                            <span class="text-[10px] bg-white/5 text-slate-300 px-2 py-0.5 rounded border border-white/5">{{ $obra->nome }}</span>
                        @empty
                            <span class="text-[10px] text-slate-600 italic">Nenhum acesso</span>
                        @endforelse
                    </div>
                </div>

                <div class="flex gap-2 pt-4 border-t border-white/5">
                    <a href="{{ route('users.edit', $user) }}" class="flex-1 text-center py-2 bg-white/5 hover:bg-white/10 rounded-xl text-xs font-bold text-white border border-white/5 transition-all">Editar</a>
                    <form action="{{ route('users.destroy', $user) }}" method="POST" onsubmit="return confirm('Excluir este usuário?')" class="flex-1">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="w-full text-center py-2 bg-red-500/10 hover:bg-red-500/20 rounded-xl text-xs font-bold text-red-500 border border-red-500/20 transition-all">Excluir</button>
                    </form>
                </div>
            </div>
        @empty
            <p class="text-center text-slate-500 py-10">Nenhum usuário encontrado.</p>
        @endforelse
    </div>

    <!-- Versão Desktop: Tabela Otimizada -->
    <div class="hidden sm:block bg-white/5 backdrop-blur-xl border border-white/10 shadow-2xl rounded-2xl overflow-hidden">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-white/5 border-b border-white/10">
                    <th class="py-4 px-6 text-xs font-bold text-slate-400 uppercase tracking-wider">Usuário</th>
                    <th class="py-4 px-6 text-xs font-bold text-slate-400 uppercase tracking-wider">Perfil</th>
                    <th class="py-4 px-6 text-xs font-bold text-slate-400 uppercase tracking-wider">Acessos</th>
                    <th class="py-4 px-6 text-xs font-bold text-slate-400 uppercase tracking-wider text-right">Ações</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-white/5">
                @foreach($users as $user)
                    <tr class="hover:bg-white/[0.02] transition-colors group">
                        <td class="py-4 px-6">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-full bg-slate-800 flex items-center justify-center text-xs font-bold text-slate-300 border border-white/10 shadow-inner">
                                    {{ substr($user->name, 0, 1) }}
                                </div>
                                <div>
                                    <span class="text-sm font-bold text-white group-hover:text-amber-500 transition-colors">{{ $user->name }}</span>
                                    <p class="text-xs text-slate-500">{{ $user->email }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="py-4 px-6">
                            <span class="px-2 py-1 text-[10px] font-bold uppercase rounded-md tracking-wider
                                @if($user->role === 'chefe') bg-purple-500/20 text-purple-400 border border-purple-500/30
                                @elseif($user->role === 'operador') bg-blue-500/20 text-blue-400 border border-blue-500/30
                                @else bg-green-500/20 text-green-400 border border-green-500/30 @endif">
                                {{ $user->role }}
                            </span>
                        </td>
                        <td class="py-4 px-6">
                            <div class="flex flex-wrap gap-1 max-w-[300px]">
                                @forelse($user->obras as $obra)
                                    <span class="text-[10px] bg-slate-800/50 text-slate-300 px-2 py-0.5 rounded border border-white/5">{{ $obra->nome }}</span>
                                @empty
                                    <span class="text-[10px] text-slate-600 italic">Sem obras vinculadas</span>
                                @endforelse
                            </div>
                        </td>
                        <td class="py-4 px-6 text-right space-x-2">
                            <a href="{{ route('users.edit', $user) }}" class="inline-flex p-2 bg-white/5 hover:bg-amber-500/20 rounded-lg text-slate-400 hover:text-amber-500 transition-all border border-white/5">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2-2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                </svg>
                            </a>
                            <form action="{{ route('users.destroy', $user) }}" method="POST" class="inline" onsubmit="return confirm('Excluir este usuário?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="p-2 bg-white/5 hover:bg-red-500/20 rounded-lg text-slate-400 hover:text-red-500 transition-all border border-white/5">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                    </svg>
                                </button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- Paginação -->
    <div class="mt-6">
        {{ $users->links() }}
    </div>
</x-app-layout>
