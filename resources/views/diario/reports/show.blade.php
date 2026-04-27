<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center print:hidden">
            <div>
                <p class="text-[10px] font-black text-amber-500 uppercase tracking-[0.2em] mb-1">Relatório Oficial</p>
                <h2 class="font-black text-xl text-white leading-tight uppercase tracking-tight">
                    Diário de Obra #{{ $diarioReport->id }}
                    @if($diarioReport->foiEditado())
                        <span class="ml-2 text-[10px] font-bold text-rose-400 uppercase tracking-widest align-middle">[Editado]</span>
                    @endif
                </h2>
            </div>
            <div class="flex items-center gap-3">
                @if(auth()->user()->role === 'chefe')
                    <a href="{{ route('diario-reports.edit', $diarioReport) }}" class="px-4 py-3 bg-white/10 hover:bg-white/20 text-slate-300 rounded-full text-[10px] font-black uppercase tracking-widest transition-all flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                        </svg>
                        Editar
                    </a>
                @endif
                <a href="{{ route('diario-reports.pdf', $diarioReport) }}" class="px-6 py-3 bg-amber-500 text-slate-900 rounded-full text-[10px] font-black uppercase tracking-widest transition-all hover:bg-amber-400 active:scale-95 shadow-lg shadow-amber-500/20 flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    Salvar Relatório PDF
                </a>
                <a href="{{ route('feed.index') }}" class="text-slate-500 hover:text-white">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </a>
            </div>
        </div>
    </x-slot>

    <div id="printable-report" class="max-w-5xl mx-auto pb-24 print:pb-0 print:m-0 print:max-w-none">
        <!-- Relatório Principal -->
        <div class="bg-white text-slate-900 shadow-xl print:shadow-none print:border-0 overflow-hidden font-sans border border-slate-200">
            
            <!-- CABEÇALHO TABULAR -->
            <div class="border-b-2 border-slate-900">
                <table class="w-full border-collapse">
                    <tr>
                        <td class="p-6 bg-black text-white w-2/3">
                            <h1 class="text-3xl font-black uppercase tracking-tighter leading-none mb-1">Diário de Obra</h1>
                            <p class="text-[10px] font-bold text-amber-500 uppercase tracking-[0.3em]">Registro de Atividades Diárias</p>
                        </td>
                        <td class="p-6 border-l-2 border-slate-900 text-right">
                            <p class="text-[10px] font-bold text-slate-400 uppercase mb-1">Data do Relatório</p>
                            <p class="text-3xl font-black text-slate-900">{{ $diarioReport->data_relatorio->format('d/m/Y') }}</p>
                        </td>
                    </tr>
                </table>
            </div>

            <!-- INFO CONTRATO -->
            <div class="grid grid-cols-2 border-b-2 border-slate-200">
                <div class="p-6 border-r-2 border-slate-100">
                    <div class="space-y-4">
                        <div>
                            <p class="text-[9px] font-black text-slate-400 uppercase">Contratante</p>
                            <p class="text-sm font-black uppercase">{{ $obra->contratante ?: '---' }}</p>
                            @if($obra->cnpj_contratante)<p class="text-[10px] text-slate-500 font-bold">CNPJ: {{ $obra->cnpj_contratante }}</p>@endif
                        </div>
                        <div>
                            <p class="text-[9px] font-black text-slate-400 uppercase">Empresa Contratada</p>
                            <p class="text-sm font-black uppercase">{{ $obra->empresa_contratada ?: '---' }}</p>
                            @if($obra->cnpj_empresa_contratada)<p class="text-[10px] text-slate-500 font-bold">CNPJ: {{ $obra->cnpj_empresa_contratada }}</p>@endif
                        </div>
                    </div>
                </div>
                <div class="p-6">
                    <div class="space-y-4">
                        <div>
                            <p class="text-[9px] font-black text-slate-400 uppercase">Engenheiro Responsável</p>
                            <p class="text-sm font-black uppercase">{{ $obra->engenheiro_responsavel ?: '---' }}</p>
                        </div>
                        <div>
                            <p class="text-[9px] font-black text-slate-400 uppercase">Nome da Obra</p>
                            <p class="text-sm font-black uppercase text-amber-600">{{ $obra->nome }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- MÉTRICAS -->
            <div class="grid grid-cols-6 border-b-2 border-slate-900">
                <div class="p-4 border-r border-slate-200 text-center bg-slate-50/50">
                    <p class="text-[8px] font-black text-slate-400 uppercase">Início</p>
                    <p class="text-xs font-black">{{ $obra->data_inicio ? $obra->data_inicio->format('d/m/y') : '-' }}</p>
                </div>
                <div class="p-4 border-r border-slate-200 text-center bg-slate-50/50">
                    <p class="text-[8px] font-black text-slate-400 uppercase">Prazo (Dias)</p>
                    <p class="text-xs font-black">{{ $obra->prazo_dias ?: '-' }}</p>
                </div>
                <div class="p-4 border-r border-slate-200 text-center bg-slate-50/50">
                    <p class="text-[8px] font-black text-slate-400 uppercase">Término Prev.</p>
                    <p class="text-xs font-black">{{ $obra->data_fim_prevista ? $obra->data_fim_prevista->format('d/m/y') : '-' }}</p>
                </div>
                <div class="p-4 border-r border-slate-200 text-center">
                    <p class="text-[8px] font-black text-slate-400 uppercase">Dias Corridos</p>
                    <p class="text-xs font-black text-blue-600">{{ $diasCorridos }}</p>
                </div>
                <div class="p-4 border-r border-slate-200 text-center">
                    <p class="text-[8px] font-black text-slate-400 uppercase">Dias Improd.</p>
                    <p class="text-xs font-black text-rose-600">{{ $diasImprodutivos }}</p>
                </div>
                <div class="p-4 text-center">
                    <p class="text-[8px] font-black text-slate-400 uppercase">Dias Restantes</p>
                    <p class="text-xs font-black text-green-600">{{ $diasRestantes }}</p>
                </div>
            </div>

            <!-- CLIMA -->
            <div class="p-6 border-b-2 border-slate-200">
                <h3 class="text-[10px] font-black uppercase mb-4 text-amber-600">Condições do Tempo</h3>
                <div class="grid grid-cols-14 border border-slate-200">
                    @foreach($diarioReport->clima_horario as $hora => $condicao)
                        <div class="p-2 text-center border-r last:border-r-0 border-slate-100">
                            <p class="text-[7px] font-black text-slate-400 leading-none">{{ $hora }}</p>
                            <p class="text-[9px] font-black uppercase {{ $condicao == '-' ? 'text-slate-200' : 'text-slate-900' }}">{{ $condicao }}</p>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- RECURSOS -->
            <div class="grid grid-cols-2 border-b-2 border-slate-200">
                <div class="p-6 border-r-2 border-slate-100">
                    <h3 class="text-[10px] font-black text-blue-600 uppercase mb-4">Mão de Obra</h3>
                    <table class="w-full text-xs">
                        @forelse($diarioReport->mao_de_obra as $item)
                            <tr class="border-b border-slate-100 last:border-0">
                                <td class="py-2 font-medium uppercase">{{ $item['funcao'] }}</td>
                                <td class="py-2 text-right font-black">{{ $item['quantidade'] }}</td>
                            </tr>
                        @empty
                            <tr><td class="py-2 text-slate-400 italic">Nenhum dado.</td></tr>
                        @endforelse
                    </table>
                </div>
                <div class="p-6">
                    <h3 class="text-[10px] font-black text-purple-600 uppercase mb-4">Equipamentos</h3>
                    <table class="w-full text-xs">
                        @forelse($diarioReport->maquinario as $item)
                            <tr class="border-b border-slate-100 last:border-0">
                                <td class="py-2 font-medium uppercase">{{ $item['item'] }}</td>
                                <td class="py-2 text-right font-black">{{ $item['quantidade'] }}</td>
                            </tr>
                        @empty
                            <tr><td class="py-2 text-slate-400 italic">Nenhum dado.</td></tr>
                        @endforelse
                    </table>
                </div>
            </div>

            <!-- RELATO -->
            <div class="p-6 border-b-2 border-slate-200 bg-slate-50/20">
                <h3 class="text-[10px] font-black text-green-700 uppercase mb-4">Relato das Atividades</h3>
                <div class="text-xs leading-relaxed text-slate-800 whitespace-pre-line min-h-[80px]">
                    {{ $diarioReport->servicos_execucao ?: 'Sem atividades registradas hoje.' }}
                </div>
            </div>

            <!-- OCORRÊNCIAS -->
            <div class="grid grid-cols-2 border-b-2 border-slate-200">
                <div class="p-6 border-r-2 border-slate-100">
                    <h3 class="text-[10px] font-black text-rose-600 uppercase mb-2">Ocorrências</h3>
                    <p class="text-xs text-slate-700">{{ $diarioReport->ocorrencias ?: 'Nenhuma registrada.' }}</p>
                </div>
                <div class="p-6">
                    <h3 class="text-[10px] font-black text-slate-500 uppercase mb-2">Materiais</h3>
                    <p class="text-xs text-slate-700">{{ $diarioReport->materiais_recebidos ?: 'Nenhum registrado.' }}</p>
                </div>
            </div>

            <!-- FOTOS -->
            <div class="p-6 border-b-2 border-slate-900 bg-slate-50/30">
                <h3 class="text-[10px] font-black text-slate-900 uppercase mb-6">Registro Fotográfico</h3>
                <div class="grid grid-cols-2 md:grid-cols-3 gap-6">
                    @foreach($postsComFoto as $post)
                        <div class="break-inside-avoid">
                            <div class="border border-slate-900 p-1 bg-white mb-1">
                                <img src="{{ asset('storage/' . $post->foto_path) }}" class="w-full aspect-[4/3] object-cover">
                            </div>
                            <p class="text-[8px] font-bold text-slate-600 uppercase text-center leading-tight">{{ $post->texto }}</p>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- ASSINATURAS -->
            <div class="p-10 grid grid-cols-2 gap-20">
                <div class="text-center">
                    <div class="border-t border-slate-900 mb-2"></div>
                    <p class="text-[9px] font-black uppercase">{{ $obra->engenheiro_responsavel ?: $diarioReport->user->name }}</p>
                    <p class="text-[8px] font-bold uppercase text-slate-400">Responsável Técnico</p>
                </div>
                <div class="text-center">
                    <div class="border-t border-slate-900 mb-2"></div>
                    <p class="text-[9px] font-black uppercase">Fiscalização / Cliente</p>
                    <p class="text-[8px] font-bold uppercase text-slate-400">Visto da Fiscalização</p>
                </div>
            </div>
        </div>
    </div>

    <style>
        @media print {
            @page {
                size: A4 portrait;
                margin: 0 !important; /* Forces removal of browser headers/footers in many cases */
            }

            /* Optimization for performance (remove shadows and heavy effects) */
            * {
                box-shadow: none !important;
                text-shadow: none !important;
                transition: none !important;
                backdrop-filter: none !important;
            }

            html, body {
                background: white !important;
                color: black !important;
                margin: 0 !important;
                padding: 0 !important;
                width: 100% !important;
                height: auto !important;
                overflow: visible !important;
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }

            /* UI HIDING - STRICT */
            nav, header, .pb-24, .sm\:hidden, .fixed, .absolute, .sticky, button {
                display: none !important;
                visibility: hidden !important;
                opacity: 0 !important;
            }

            main {
                display: block !important;
                position: static !important;
                margin: 0 !important;
                padding: 0 !important;
                width: 100% !important;
            }

            #printable-report {
                display: block !important;
                width: 100% !important;
                margin: 0 !important;
                padding: 0 !important;
                background: white !important;
                position: static !important;
            }

            .bg-white {
                margin: 0 !important;
                padding: 10mm !important; /* Internal margins for the report content */
                width: 100% !important;
                border: 0 !important;
            }

            .break-inside-avoid { break-inside: avoid; }
            .bg-black, .bg-slate-900 { background-color: #000 !important; color: #fff !important; }
            .border-slate-900 { border-color: #000 !important; }
            .bg-slate-50 { background-color: #f8fafc !important; }
        }
    </style>
</x-app-layout>
