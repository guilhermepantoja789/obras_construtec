<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-row justify-between items-center gap-4">
            <div class="flex items-center gap-4">
                <a href="{{ route('diario-reports.index') }}" class="p-2 bg-white/5 hover:bg-white/10 rounded-xl text-slate-400 hover:text-white transition-all border border-white/5 shadow-lg">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                </a>
                <div>
                    <h2 class="font-black text-xl sm:text-2xl text-white leading-tight tracking-tight uppercase">
                        Calendário de Diários
                    </h2>
                    <p class="text-[10px] font-bold text-cyan-500 uppercase tracking-widest">{{ $obra->nome }}</p>
                </div>
            </div>
            
            <div class="flex items-center gap-1 bg-white/5 p-1 rounded-xl border border-white/5 scale-90 sm:scale-100">
                <a href="{{ route('diario-reports.calendar', ['month' => $startDate->copy()->subMonth()->month, 'year' => $startDate->copy()->subMonth()->year]) }}" class="p-1.5 sm:p-2 hover:bg-white/10 rounded-lg text-slate-400 hover:text-white transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                </a>
                <span class="text-[10px] sm:text-xs font-black text-white uppercase px-1 sm:px-2 min-w-[100px] sm:min-w-[120px] text-center">
                    {{ $startDate->translatedFormat('F Y') }}
                </span>
                <a href="{{ route('diario-reports.calendar', ['month' => $startDate->copy()->addMonth()->month, 'year' => $startDate->copy()->addMonth()->year]) }}" class="p-1.5 sm:p-2 hover:bg-white/10 rounded-lg text-slate-400 hover:text-white transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                </a>
            </div>
        </div>
    </x-slot>

    <div class="space-y-4 sm:space-y-6">
        <!-- Legend -->
        <div class="flex flex-wrap gap-x-4 gap-y-2 px-2">
            <div class="flex items-center gap-1.5">
                <div class="w-2.5 h-2.5 rounded-full bg-emerald-500"></div>
                <span class="text-[9px] sm:text-[10px] font-bold text-slate-400 uppercase tracking-widest">Trabalhado</span>
            </div>
            <div class="flex items-center gap-1.5">
                <div class="w-2.5 h-2.5 rounded-full bg-blue-500"></div>
                <span class="text-[9px] sm:text-[10px] font-bold text-slate-400 uppercase tracking-widest">Meio</span>
            </div>
            <div class="flex items-center gap-1.5">
                <div class="w-2.5 h-2.5 rounded-full bg-rose-500"></div>
                <span class="text-[9px] sm:text-[10px] font-bold text-slate-400 uppercase tracking-widest">Não Trab.</span>
            </div>
        </div>

        <!-- Calendar Grid -->
        <div class="bg-white/5 backdrop-blur-xl border border-white/10 rounded-2xl sm:rounded-3xl overflow-hidden shadow-2xl">
            <div class="grid grid-cols-7 border-b border-white/10 bg-white/5">
                @foreach(['D', 'S', 'T', 'Q', 'Q', 'S', 'S'] as $day)
                    <div class="py-3 sm:py-4 text-center">
                        <span class="text-[10px] font-black text-slate-500 uppercase tracking-widest">{{ $day }}</span>
                    </div>
                @endforeach
            </div>

            <div class="grid grid-cols-7">
                @php
                    $daysInMonth = $startDate->daysInMonth;
                    $dayOfWeek = $startDate->dayOfWeek;
                    $currentDay = 1;
                    $today = \Carbon\Carbon::today()->format('Y-m-d');
                @endphp

                @for ($i = 0; $i < 42; $i++)
                    @if ($i < $dayOfWeek || $currentDay > $daysInMonth)
                        <div class="aspect-square p-1 border-b border-r border-white/5 bg-black/10"></div>
                    @else
                        @php
                            $dateStr = $startDate->copy()->day($currentDay)->format('Y-m-d');
                            $report = $reports->get($dateStr);
                            $dayPosts = $posts->get($dateStr);
                            $isToday = $dateStr === $today;
                        @endphp
                        
                        <div class="aspect-square p-1 sm:p-2 border-b border-r border-white/5 relative group transition-all hover:bg-white/5 
                            {{ $isToday ? 'bg-cyan-500/10' : '' }}
                            @if($report && !isset($isToday))
                                @if($report->status_dia == 'trabalhado') bg-emerald-500/[0.03]
                                @elseif($report->status_dia == 'meio_expediente') bg-blue-500/[0.03]
                                @else bg-rose-500/[0.03] @endif
                            @endif">
                            
                            <div class="flex justify-between items-start relative z-10">
                                <span class="text-[10px] sm:text-xs font-black 
                                    @if($report)
                                        @if($report->status_dia == 'trabalhado') text-emerald-500
                                        @elseif($report->status_dia == 'meio_expediente') text-blue-500
                                        @else text-rose-500 @endif
                                    @else
                                        {{ $isToday ? 'text-cyan-500' : 'text-slate-500' }}
                                    @endif">
                                    {{ $currentDay }}
                                </span>
                                
                                @if($dayPosts && !$report)
                                    <div class="flex gap-0.5">
                                        <span class="w-1.5 h-1.5 rounded-full bg-cyan-500 animate-pulse"></span>
                                    </div>
                                @endif
                            </div>

                            <div class="absolute inset-0 flex items-center justify-center pointer-events-none">
                                @if($report)
                                    <div class="opacity-20 sm:opacity-10 transition-opacity group-hover:opacity-40">
                                        @if($report->status_dia == 'trabalhado')
                                            <svg class="w-6 h-6 sm:w-8 sm:h-8 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                        @elseif($report->status_dia == 'meio_expediente')
                                            <svg class="w-6 h-6 sm:w-8 sm:h-8 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                        @else
                                            <svg class="w-6 h-6 sm:w-8 sm:h-8 text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                        @endif
                                    </div>
                                @endif
                            </div>

                            <div class="absolute inset-0 flex flex-col justify-end p-1 sm:p-2">
                                @if($report)
                                    <a href="{{ Auth::user()->role === 'chefe' ? route('diario-reports.edit', $report) : route('diario-reports.show', $report) }}" class="block w-full z-10 h-full">
                                        <!-- Desktop view -->
                                        <div class="hidden sm:block rounded-lg p-1 text-[8px] font-black uppercase tracking-tighter truncate
                                            @if($report->status_dia == 'trabalhado') bg-emerald-500/10 text-emerald-500 border border-emerald-500/20
                                            @elseif($report->status_dia == 'meio_expediente') bg-blue-500/10 text-blue-500 border border-blue-500/20
                                            @else bg-rose-500/10 text-rose-500 border border-rose-500/20 @endif">
                                            {{ str_replace('_', ' ', $report->status_dia) }}
                                        </div>
                                    </a>
                                @else
                                    @if(Auth::user()->role === 'chefe' || Auth::user()->role === 'operador')
                                        <a href="{{ route('diario-reports.create', ['date' => $dateStr]) }}" class="w-full h-full absolute inset-0 flex items-center justify-center sm:opacity-0 group-hover:opacity-100 transition-opacity">
                                            <div class="w-6 h-6 sm:w-auto sm:h-auto sm:px-2 sm:py-1 bg-cyan-500 text-white rounded-full sm:rounded-md flex items-center justify-center shadow-lg">
                                                <svg class="w-4 h-4 sm:hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                                                <span class="hidden sm:inline text-[9px] font-black uppercase">Lançar</span>
                                            </div>
                                        </a>
                                    @endif
                                @endif
                            </div>

                        </div>
                        @php $currentDay++; @endphp
                    @endif
                @endfor
            </div>
        </div>

        <!-- Mobile Info -->
        <div class="sm:hidden bg-white/5 border border-white/10 rounded-2xl p-4 shadow-lg">
            <h4 class="text-[10px] font-black text-white uppercase tracking-widest mb-3 flex items-center gap-2">
                <span class="w-1.5 h-1.5 bg-cyan-500 rounded-full animate-pulse"></span>
                Dicas de Navegação
            </h4>
            <ul class="space-y-2">
                <li class="flex items-start gap-2">
                    <div class="w-4 h-4 bg-cyan-500/20 rounded flex items-center justify-center mt-0.5">
                        <svg class="w-2.5 h-2.5 text-cyan-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                    </div>
                    <p class="text-[9px] text-slate-400 font-medium uppercase tracking-tight">Toque no "+" para lançar um novo diário</p>
                </li>
                <li class="flex items-start gap-2">
                    <div class="w-4 h-4 bg-emerald-500/20 rounded flex items-center justify-center mt-0.5">
                        <span class="text-[8px] font-black text-emerald-500">12</span>
                    </div>
                    <p class="text-[9px] text-slate-400 font-medium uppercase tracking-tight">Número colorido indica diário já emitido</p>
                </li>
            </ul>
        </div>
    </div>
</x-app-layout>
