<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-row justify-between items-center gap-4">
            <div class="flex items-center gap-4">
                <a href="{{ route('obras.index') }}" class="p-2 bg-white/5 hover:bg-white/10 rounded-xl text-slate-400 hover:text-white transition-all border border-white/5 shadow-lg">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                </a>
                <h2 class="font-black text-xl sm:text-2xl text-white leading-tight tracking-tight uppercase">
                    {{ $obra->nome }}
                </h2>
            </div>
            <div class="flex items-center gap-2">
                @if(isset($activeObra) && $activeObra->id != $obra->id)
                    <form action="{{ route('context.switch') }}" method="POST">
                        @csrf
                        <input type="hidden" name="obra_id" value="{{ $obra->id }}">
                        <button type="submit" class="inline-flex items-center px-4 py-2 bg-white/5 border border-white/10 rounded-xl font-bold text-[10px] text-white uppercase tracking-widest hover:bg-white/10 transition shadow-lg">
                            Ativar Contexto
                        </button>
                    </form>
                @endif
                @if(Auth::user()->isChefe())
                <a href="{{ route('obras.edit', $obra) }}" class="inline-flex items-center px-4 py-2 bg-amber-500 rounded-xl font-bold text-[10px] text-slate-900 uppercase tracking-widest hover:bg-amber-400 transition shadow-lg shadow-amber-500/20">
                    <svg class="w-3.5 h-3.5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                    </svg>
                    Editar
                </a>
                @endif
            </div>
        </div>
    </x-slot>

    <div class="space-y-6">
        <!-- Cards de Status Rápido -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <div class="bg-white/5 backdrop-blur-xl border border-white/10 p-4 rounded-2xl shadow-xl">
                <p class="text-[10px] font-bold text-slate-500 uppercase tracking-widest mb-1">Status</p>
                <div class="flex items-center gap-2">
                    <div class="w-2 h-2 rounded-full @if($obra->status == 'em_andamento') bg-blue-500 @elseif($obra->status == 'concluida') bg-green-500 @else bg-slate-500 @endif animate-pulse"></div>
                    <span class="text-sm font-bold text-white uppercase">{{ str_replace('_', ' ', $obra->status) }}</span>
                </div>
            </div>
            <div class="bg-white/5 backdrop-blur-xl border border-white/10 p-4 rounded-2xl shadow-xl">
                <p class="text-[10px] font-bold text-slate-500 uppercase tracking-widest mb-1">Equipe</p>
                <p class="text-lg font-black text-white leading-none">{{ $obra->users->where('role', 'operador')->count() + $chefes->count() }}</p>
            </div>
            <div class="bg-white/5 backdrop-blur-xl border border-white/10 p-4 rounded-2xl shadow-xl">
                <p class="text-[10px] font-bold text-slate-500 uppercase tracking-widest mb-1">Clientes</p>
                <p class="text-lg font-black text-white leading-none">{{ $obra->users->where('role', 'cliente')->count() }}</p>
            </div>
            <div class="bg-white/5 backdrop-blur-xl border border-white/10 p-4 rounded-2xl shadow-xl">
                <p class="text-[10px] font-bold text-slate-500 uppercase tracking-widest mb-1">Início</p>
                <p class="text-sm font-bold text-white leading-none">{{ $obra->data_inicio ? \Carbon\Carbon::parse($obra->data_inicio)->format('d/m/Y') : 'N/A' }}</p>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Coluna Principal -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Endereço e Detalhes -->
                <div class="bg-white/5 backdrop-blur-xl border border-white/10 rounded-2xl shadow-2xl overflow-hidden">
                    <div class="p-6">
                        <div class="flex items-center gap-2 mb-6">
                            <div class="p-2 bg-amber-500/10 rounded-lg">
                                <svg class="w-5 h-5 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                </svg>
                            </div>
                            <h3 class="text-sm font-bold text-white uppercase tracking-widest">Localização da Obra</h3>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="space-y-4">
                                <div>
                                    <p class="text-[10px] font-bold text-slate-500 uppercase tracking-widest">Endereço</p>
                                    <p class="text-sm text-white font-medium">
                                        {{ $obra->logradouro ?: 'Logradouro não informado' }}<br>
                                        {{ $obra->bairro }} - {{ $obra->cidade }}/{{ $obra->estado }}
                                    </p>
                                </div>
                                <div>
                                    <p class="text-[10px] font-bold text-slate-500 uppercase tracking-widest">CEP</p>
                                    <p class="text-sm text-white font-medium">{{ $obra->cep ?: 'N/A' }}</p>
                                </div>
                            </div>
                            <div class="space-y-4">
                                <div>
                                    <p class="text-[10px] font-bold text-slate-500 uppercase tracking-widest">Complemento</p>
                                    <p class="text-sm text-white font-medium">{{ $obra->localizacao ?: 'N/A' }}</p>
                                </div>
                                <div>
                                    <p class="text-[10px] font-bold text-slate-500 uppercase tracking-widest">Previsão de Entrega</p>
                                    <p class="text-sm text-white font-medium">{{ $obra->data_fim_prevista ? \Carbon\Carbon::parse($obra->data_fim_prevista)->format('d/m/Y') : 'Não definida' }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Propostas Comerciais -->
                <div class="bg-white/5 backdrop-blur-xl border border-white/10 rounded-2xl shadow-2xl overflow-hidden">
                    <div class="p-6">
                        <div class="flex justify-between items-center mb-6">
                            <div class="flex items-center gap-2">
                                <div class="p-2 bg-green-500/10 rounded-lg">
                                    <svg class="w-5 h-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                    </svg>
                                </div>
                                <h3 class="text-sm font-bold text-white uppercase tracking-widest">Propostas Comerciais</h3>
                            </div>
                            @if(Auth::user()->isChefe())
                            <a href="{{ route('propostas.create') }}" class="text-[10px] font-black text-amber-500 uppercase tracking-widest hover:text-amber-400 transition-colors">
                                + Nova Proposta
                            </a>
                            @endif
                        </div>

                        <div class="space-y-3">
                            @php
                                $propostasVisiveis = Auth::user()->isClient()
                                    ? $obra->propostas->where('status', 'aceita')
                                    : $obra->propostas;
                            @endphp
                            @forelse($propostasVisiveis as $proposta)
                                <div class="flex items-center justify-between p-4 bg-slate-900/50 rounded-2xl border border-white/5 hover:border-white/20 transition-all group">
                                    <div class="flex items-center gap-4">
                                        <div class="w-10 h-10 rounded-xl bg-white/5 flex items-center justify-center text-xs font-black text-slate-400 group-hover:text-amber-500 transition-colors">
                                            #{{ $proposta->id }}
                                        </div>
                                        <div>
                                            <p class="text-xs font-bold text-white">{{ $proposta->titulo }}</p>
                                            <div class="flex items-center gap-2 mt-1">
                                                <span class="text-[9px] font-black uppercase px-1.5 py-0.5 rounded
                                                    @if($proposta->status == 'aceita') bg-green-500/10 text-green-500
                                                    @elseif($proposta->status == 'recusada') bg-rose-500/10 text-rose-500
                                                    @else bg-blue-500/10 text-blue-500 @endif">
                                                    {{ $proposta->status }}
                                                </span>
                                                @if(Auth::user()->isChefe())
                                                <span class="text-[9px] text-slate-500 font-bold uppercase tracking-tighter">R$ {{ number_format($proposta->valor_total, 2, ',', '.') }}</span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                    <a href="{{ Auth::user()->isClient() ? route('propostas.cliente.show', $proposta) : route('propostas.show', $proposta) }}" class="p-2 text-slate-600 hover:text-white transition-colors">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                                    </a>
                                </div>
                            @empty
                                <div class="p-8 text-center border border-dashed border-white/5 rounded-2xl">
                                    <p class="text-[10px] text-slate-600 font-black uppercase tracking-widest">Nenhuma proposta elaborada</p>
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>

                <!-- Histórico de Diários -->
                <div class="bg-white/5 backdrop-blur-xl border border-white/10 rounded-2xl shadow-2xl overflow-hidden">
                    <div class="p-6">
                        <div class="flex items-center gap-2 mb-6">
                            <div class="p-2 bg-blue-500/10 rounded-lg">
                                <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <h3 class="text-sm font-bold text-white uppercase tracking-widest">Histórico de Diários</h3>
                        </div>
                        <a href="{{ route('diario-reports.calendar') }}" class="text-[10px] font-black text-blue-500 uppercase tracking-widest hover:text-blue-400 transition-colors mb-4 block">
                            Ver Calendário →
                        </a>

                        <div class="space-y-3">
                            @forelse($obra->diarioReports as $report)
                                <a href="{{ route('diario-reports.show', $report) }}" class="flex items-center justify-between p-4 bg-slate-900/50 rounded-2xl border border-white/5 hover:border-blue-500/50 hover:bg-blue-500/5 transition-all group">
                                    <div class="flex items-center gap-4">
                                        <div class="w-10 h-10 rounded-xl bg-white/5 flex items-center justify-center text-xs font-black text-slate-400 group-hover:text-blue-500 transition-colors">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                            </svg>
                                        </div>
                                        <div>
                                            <p class="text-xs font-bold text-white">{{ $report->data_relatorio->format('d/m/Y') }}</p>
                                            <div class="flex items-center gap-2 mt-1">
                                                <span class="text-[9px] font-black uppercase px-1.5 py-0.5 rounded
                                                    @if($report->status_dia == 'trabalhado') bg-emerald-500/10 text-emerald-500
                                                    @elseif($report->status_dia == 'meio_expediente') bg-blue-500/10 text-blue-500
                                                    @else bg-rose-500/10 text-rose-500 @endif">
                                                    {{ str_replace('_', ' ', $report->status_dia) }}
                                                </span>
                                                @if($report->dia_improdutivo)
                                                    <span class="text-[9px] font-black uppercase px-1.5 py-0.5 rounded bg-amber-500/10 text-amber-500">
                                                        Improdutivo
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                    <div class="p-2 text-slate-600 group-hover:text-blue-500 transition-colors">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                                    </div>
                                </a>
                            @empty
                                <div class="p-8 text-center border border-dashed border-white/5 rounded-2xl">
                                    <div class="w-12 h-12 bg-white/5 rounded-full flex items-center justify-center mx-auto mb-3 border border-white/10">
                                        <svg class="w-6 h-6 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                        </svg>
                                    </div>
                                    <p class="text-[10px] text-slate-600 font-black uppercase tracking-widest mb-1">Diário de Obra vazio</p>
                                    <p class="text-[10px] text-slate-600">Nenhum relatório diário foi emitido ainda.</p>
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>

            <!-- Coluna Lateral -->
            <div class="space-y-6">
                <!-- Card de Equipe -->
                <div class="bg-white/5 backdrop-blur-xl border border-white/10 rounded-2xl shadow-2xl overflow-hidden">
                    <div class="p-5 border-b border-white/5 flex justify-between items-center">
                        <h3 class="text-xs font-bold text-white uppercase tracking-widest">Pessoas Vinculadas</h3>
                        <span class="text-[10px] bg-amber-500 text-slate-900 px-2 py-0.5 rounded-full font-black">{{ $obra->users->count() + $chefes->count() }}</span>
                    </div>
                    <div class="p-2">
                        <!-- Chefes (Sempre presentes) -->
                        @foreach($chefes as $chefe)
                            <div class="flex items-center gap-3 p-3 hover:bg-white/5 rounded-xl transition-all group">
                                <div class="w-8 h-8 rounded-full bg-amber-500/20 flex items-center justify-center text-[10px] font-bold text-amber-500 border border-amber-500/20">
                                    {{ substr($chefe->name, 0, 1) }}
                                </div>
                                <div class="flex-1">
                                    <p class="text-xs font-bold text-white group-hover:text-amber-500 transition-colors">{{ $chefe->name }}</p>
                                    <p class="text-[9px] text-amber-500 uppercase tracking-tighter">Chefe de Obra</p>
                                </div>
                                <span class="text-[8px] bg-amber-500/10 text-amber-500 px-1.5 py-0.5 rounded border border-amber-500/20 font-bold uppercase">Admin</span>
                            </div>
                        @endforeach

                        <!-- Outros Usuários -->
                        @foreach($obra->users as $user)
                            <div class="flex items-center gap-3 p-3 hover:bg-white/5 rounded-xl transition-all group">
                                <div class="w-8 h-8 rounded-full bg-slate-800 flex items-center justify-center text-[10px] font-bold text-slate-300 border border-white/10">
                                    {{ substr($user->name, 0, 1) }}
                                </div>
                                <div class="flex-1">
                                    <p class="text-xs font-bold text-white group-hover:text-amber-500 transition-colors">{{ $user->name }}</p>
                                    <p class="text-[9px] text-slate-500 uppercase tracking-tighter">{{ $user->role }}</p>
                                </div>
                            </div>
                        @endforeach

                        @if($chefes->isEmpty() && $obra->users->isEmpty())
                            <div class="p-6 text-center">
                                <p class="text-xs text-slate-600 italic">Ninguém vinculado</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
