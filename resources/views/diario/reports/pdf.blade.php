<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Diário de Obra - {{ $obra->nome }} - {{ $diarioReport->data_relatorio->format('d/m/Y') }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 10pt;
            color: #1e293b;
            background: white;
        }

        /* CABEÇALHO */
        .header-table { width: 100%; border-collapse: collapse; margin-bottom: 0; }
        .header-black { background-color: #000; color: #fff; padding: 16px 20px; width: 65%; }
        .header-black h1 { font-size: 22pt; font-weight: 900; text-transform: uppercase; letter-spacing: -1px; line-height: 1; margin-bottom: 4px; }
        .header-black p { font-size: 7pt; color: #f59e0b; font-weight: 700; text-transform: uppercase; letter-spacing: 3px; }
        .header-date { padding: 16px 20px; text-align: right; border-left: 2px solid #000; vertical-align: middle; }
        .header-date .label { font-size: 7pt; font-weight: 700; color: #94a3b8; text-transform: uppercase; margin-bottom: 4px; }
        .header-date .date { font-size: 20pt; font-weight: 900; color: #000; }

        /* SEÇÃO GENÉRICA */
        .section { border-top: 2px solid #e2e8f0; padding: 12px 20px; }
        .section-label { font-size: 7pt; font-weight: 900; color: #94a3b8; text-transform: uppercase; letter-spacing: 2px; margin-bottom: 3px; }
        .section-value { font-size: 10pt; font-weight: 900; text-transform: uppercase; color: #000; }
        .section-sub { font-size: 8pt; color: #64748b; font-weight: 700; }

        /* GRID DE INFORMAÇÕES DO CONTRATO */
        .contract-table { width: 100%; border-collapse: collapse; border-top: 2px solid #e2e8f0; }
        .contract-table td { padding: 12px 20px; vertical-align: top; }
        .contract-table .left { border-right: 1px solid #e2e8f0; width: 50%; }

        /* MÉTRICAS */
        .metrics-table { width: 100%; border-collapse: collapse; border-top: 2px solid #000; border-bottom: 2px solid #000; }
        .metrics-table td { padding: 8px 12px; text-align: center; border-right: 1px solid #e2e8f0; }
        .metrics-table td:last-child { border-right: 0; }
        .metrics-table .metric-label { font-size: 7pt; font-weight: 900; color: #94a3b8; text-transform: uppercase; display: block; }
        .metrics-table .metric-value { font-size: 11pt; font-weight: 900; }
        .bg-gray { background-color: #f8fafc; }
        .blue { color: #2563eb; }
        .rose { color: #e11d48; }
        .green { color: #15803d; }
        .amber { color: #d97706; }

        /* CLIMA */
        .clima-section { border-top: 1px solid #e2e8f0; padding: 12px 20px; }
        .clima-title { font-size: 7pt; font-weight: 900; text-transform: uppercase; color: #d97706; letter-spacing: 2px; margin-bottom: 8px; }
        .clima-grid { width: 100%; border-collapse: collapse; border: 1px solid #e2e8f0; }
        .clima-grid td { padding: 6px 4px; text-align: center; border-right: 1px solid #e2e8f0; font-size: 7pt; }
        .clima-grid td:last-child { border-right: 0; }
        .clima-hour { font-weight: 900; color: #94a3b8; display: block; }
        .clima-val { font-weight: 900; text-transform: uppercase; }
        .clima-empty { color: #f1f5f9; }

        /* RECURSOS */
        .resources-table { width: 100%; border-collapse: collapse; border-top: 1px solid #e2e8f0; }
        .resources-table .col { padding: 12px 20px; vertical-align: top; }
        .resources-table .col-left { border-right: 1px solid #e2e8f0; width: 50%; }
        .resource-title { font-size: 7pt; font-weight: 900; text-transform: uppercase; letter-spacing: 2px; margin-bottom: 8px; }
        .resource-item { width: 100%; border-collapse: collapse; }
        .resource-item tr { border-bottom: 1px solid #f1f5f9; }
        .resource-item td { padding: 4px 0; font-size: 9pt; }
        .resource-item .name { font-weight: 700; text-transform: uppercase; }
        .resource-item .qty { text-align: right; font-weight: 900; }

        /* RELATO */
        .relato-section { border-top: 1px solid #e2e8f0; padding: 12px 20px; background-color: #f8fafc; }
        .relato-title { font-size: 7pt; font-weight: 900; text-transform: uppercase; color: #15803d; letter-spacing: 2px; margin-bottom: 8px; }
        .relato-text { font-size: 9pt; color: #334155; line-height: 1.5; }

        /* OCORRÊNCIAS */
        .occurrences-table { width: 100%; border-collapse: collapse; border-top: 1px solid #e2e8f0; }
        .occurrences-table td { padding: 12px 20px; vertical-align: top; }
        .occurrences-table .left { border-right: 1px solid #e2e8f0; width: 50%; }
        .occ-title { font-size: 7pt; font-weight: 900; text-transform: uppercase; letter-spacing: 2px; margin-bottom: 6px; }
        .occ-text { font-size: 9pt; color: #475569; line-height: 1.5; }

        /* FOTOS */
        .fotos-section { border-top: 2px solid #000; padding: 12px 20px; }
        .fotos-title { font-size: 7pt; font-weight: 900; text-transform: uppercase; letter-spacing: 2px; margin-bottom: 12px; }
        .fotos-grid { width: 100%; border-collapse: collapse; }
        .fotos-grid td { padding: 6px; width: 33.3%; vertical-align: top; }
        .foto-img { width: 100%; border: 1px solid #000; display: block; }
        .foto-caption { font-size: 7pt; font-weight: 700; text-transform: uppercase; text-align: center; color: #64748b; margin-top: 4px; }

        /* ASSINATURAS */
        .assinaturas-section { border-top: 1px solid #e2e8f0; padding: 24px 20px; }
        .assinaturas-table { width: 100%; border-collapse: collapse; }
        .assinaturas-table td { padding: 0 40px; text-align: center; width: 50%; }
        .assinatura-line { border-top: 1px solid #000; margin-bottom: 6px; }
        .assinatura-name { font-size: 8pt; font-weight: 900; text-transform: uppercase; }
        .assinatura-role { font-size: 7pt; font-weight: 700; text-transform: uppercase; color: #94a3b8; }

        /* PAGE BREAK */
        .page-break { page-break-after: always; }
    </style>
</head>
<body>

    <!-- CABEÇALHO -->
    <table class="header-table">
        <tr>
            <td class="header-black">
                <h1>Diário de Obra</h1>
                <p>Registro de Atividades Diárias</p>
            </td>
            <td class="header-date">
                <div class="label">Data do Relatório</div>
                <div class="date">{{ $diarioReport->data_relatorio->format('d/m/Y') }}</div>
                <div style="font-size: 7pt; font-weight: 900; text-transform: uppercase; margin-top: 4px; color: {{ $diarioReport->status_dia === 'trabalhado' ? '#059669' : ($diarioReport->status_dia === 'meio_expediente' ? '#2563eb' : '#e11d48') }}">
                    {{ str_replace('_', ' ', $diarioReport->status_dia) }}
                </div>
            </td>
        </tr>
    </table>

    <!-- INFORMAÇÕES DO CONTRATO -->
    <table class="contract-table">
        <tr>
            <td class="left">
                <div class="section-label">Contratante</div>
                <div class="section-value">{{ $obra->contratante ?: '---' }}</div>
                @if($obra->cnpj_contratante)<div class="section-sub">CNPJ: {{ $obra->cnpj_contratante }}</div>@endif
                <br>
                <div class="section-label">Empresa Contratada</div>
                <div class="section-value">{{ $obra->empresa_contratada ?: '---' }}</div>
                @if($obra->cnpj_empresa_contratada)<div class="section-sub">CNPJ: {{ $obra->cnpj_empresa_contratada }}</div>@endif
            </td>
            <td>
                <div class="section-label">Engenheiro Responsável</div>
                <div class="section-value">{{ $obra->engenheiro_responsavel ?: '---' }}</div>
                <br>
                <div class="section-label">Nome da Obra</div>
                <div class="section-value amber">{{ $obra->nome }}</div>
            </td>
        </tr>
    </table>

    <!-- MÉTRICAS -->
    <table class="metrics-table">
        <tr>
            <td class="bg-gray">
                <span class="metric-label">Início</span>
                <span class="metric-value">{{ $obra->data_inicio ? $obra->data_inicio->format('d/m/y') : '-' }}</span>
            </td>
            <td class="bg-gray">
                <span class="metric-label">Prazo (Dias)</span>
                <span class="metric-value">{{ $obra->prazo_dias ?: '-' }}</span>
            </td>
            <td class="bg-gray">
                <span class="metric-label">Término Prev.</span>
                <span class="metric-value">{{ $obra->data_fim_prevista ? $obra->data_fim_prevista->format('d/m/y') : '-' }}</span>
            </td>
            <td>
                <span class="metric-label">Dias Corridos</span>
                <span class="metric-value blue">{{ $diasCorridos }}</span>
            </td>
            <td>
                <span class="metric-label">Dias Improd.</span>
                <span class="metric-value rose">{{ $diasImprodutivos }}</span>
            </td>
            <td>
                <span class="metric-label">Dias Restantes</span>
                <span class="metric-value green">{{ $diasRestantes }}</span>
            </td>
        </tr>
    </table>

    <!-- AUDITORIA DE CLIMA -->
    <div class="clima-section">
        <div class="clima-title">⬤ Condições do Tempo (Auditadas por Hora)</div>
        <table class="clima-grid">
            <tr>
                @foreach($diarioReport->clima_horario as $hora => $condicao)
                    <td>
                        <span class="clima-hour">{{ $hora }}</span>
                        <span class="clima-val {{ $condicao == '-' ? 'clima-empty' : '' }}">{{ $condicao }}</span>
                    </td>
                @endforeach
            </tr>
        </table>
    </div>

    <!-- RECURSOS -->
    <table class="resources-table">
        <tr>
            <td class="col col-left">
                <div class="resource-title blue">Mão de Obra / Equipe</div>
                <table class="resource-item">
                    @forelse($diarioReport->mao_de_obra as $item)
                        <tr>
                            <td class="name">{{ $item['funcao'] }}</td>
                            <td class="qty">{{ $item['quantidade'] }}</td>
                        </tr>
                    @empty
                        <tr><td style="font-style:italic;color:#94a3b8">Nenhum dado informado.</td></tr>
                    @endforelse
                </table>
            </td>
            <td class="col">
                <div class="resource-title" style="color:#7c3aed">Equipamentos / Maquinário</div>
                <table class="resource-item">
                    @forelse($diarioReport->maquinario as $item)
                        <tr>
                            <td class="name">{{ $item['item'] }}</td>
                            <td class="qty">{{ $item['quantidade'] }}</td>
                        </tr>
                    @empty
                        <tr><td style="font-style:italic;color:#94a3b8">Nenhum dado informado.</td></tr>
                    @endforelse
                </table>
            </td>
        </tr>
    </table>

    <!-- RELATO -->
    <div class="relato-section">
        <div class="relato-title">Relato Detalhado das Atividades</div>
        <div class="relato-text">{{ $diarioReport->servicos_execucao ?: 'Sem atividades registradas hoje.' }}</div>
    </div>

    <!-- OCORRÊNCIAS -->
    <table class="occurrences-table">
        <tr>
            <td class="left">
                <div class="occ-title rose">Ocorrências / Imprevistos</div>
                <div class="occ-text">{{ $diarioReport->ocorrencias ?: 'Nenhuma ocorrência registrada.' }}</div>
            </td>
            <td>
                <div class="occ-title" style="color:#64748b">Materiais Recebidos</div>
                <div class="occ-text">{{ $diarioReport->materiais_recebidos ?: 'Nenhum material registrado.' }}</div>
            </td>
        </tr>
    </table>
    
    <!-- ANOTAÇÕES GERAIS -->
    <div style="border-top: 1px solid #e2e8f0; padding: 12px 20px;">
        <div class="section-label">Anotações Gerais</div>
        <div class="relato-text" style="min-height: 100px; padding-top: 5px;">
            {!! nl2br(e($diarioReport->observacoes)) ?: '<span style="color: #94a3b8; font-style: italic;">Nenhuma anotação adicional registrada.</span>' !!}
        </div>
    </div>

    <!-- FOTOS (se houver) -->
    @if($postsComFoto->where('foto_base64', '!=', null)->count() > 0)
        <div class="fotos-section">
            <div class="fotos-title">Registro Fotográfico de Campo</div>
            <table class="fotos-grid">
                @foreach($postsComFoto->where('foto_base64', '!=', null)->chunk(3) as $chunk)
                <tr>
                    @foreach($chunk as $post)
                    <td>
                        <img class="foto-img" src="{{ $post->foto_base64 }}">
                        <div class="foto-caption">{{ $post->texto }}</div>
                    </td>
                    @endforeach
                    @for($i = $chunk->count(); $i < 3; $i++)
                    <td></td>
                    @endfor
                </tr>
                @endforeach
            </table>
        </div>
    @endif

    <!-- NOTA DE AUDITORIA (se editado) -->
    @if($diarioReport->foiEditado())
    <div style="border-top: 1px solid #fca5a5; padding: 8px 20px; background-color: #fff1f2; margin: 0;">
        <p style="font-size: 7pt; font-weight: 900; color: #ef4444; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 2px;">⚠ Nota de Auditoria — Relatório Editado</p>
        <p style="font-size: 7pt; color: #64748b;">Este relatório foi editado após a emissão original por <strong>{{ $diarioReport->editor?->name ?? 'Responsável' }}</strong> em {{ $diarioReport->editado_em->format('d/m/Y \à\s H:i') }}. As fotos do registro de campo permanecem inalteradas.</p>
    </div>
    @endif

    <!-- ASSINATURAS -->
    <div class="assinaturas-section">
        <table class="assinaturas-table">
            <tr>
                <td>
                    <div class="assinatura-line"></div>
                    <div class="assinatura-name">{{ $obra->engenheiro_responsavel ?: $diarioReport->user->name }}</div>
                    <div class="assinatura-role">Responsável Técnico (Engenharia)</div>
                </td>
                <td>
                    <div class="assinatura-line"></div>
                    <div class="assinatura-name">Fiscalização / Cliente</div>
                    <div class="assinatura-role">Visto da Fiscalização</div>
                </td>
            </tr>
        </table>
    </div>

</body>
</html>
