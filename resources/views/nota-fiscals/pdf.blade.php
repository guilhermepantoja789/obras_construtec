<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Notas Fiscais - {{ $obra->nome }} - {{ $dataInicio->format('d/m/Y') }} a {{ $dataFim->format('d/m/Y') }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 10pt;
            color: #1e293b;
            background: white;
        }

        .header-table { width: 100%; border-collapse: collapse; margin-bottom: 0; }
        .header-black { background-color: #000; color: #fff; padding: 16px 20px; width: 65%; }
        .header-black h1 { font-size: 22pt; font-weight: 900; text-transform: uppercase; letter-spacing: -1px; line-height: 1; margin-bottom: 4px; }
        .header-black p { font-size: 7pt; color: #f59e0b; font-weight: 700; text-transform: uppercase; letter-spacing: 3px; }
        .header-date { padding: 16px 20px; text-align: right; border-left: 2px solid #000; vertical-align: middle; }
        .header-date .label { font-size: 7pt; font-weight: 700; color: #94a3b8; text-transform: uppercase; margin-bottom: 4px; }
        .header-date .date { font-size: 14pt; font-weight: 900; color: #000; }
        .header-date .sub { font-size: 7pt; font-weight: 700; color: #64748b; text-transform: uppercase; margin-top: 6px; }

        .section-label { font-size: 7pt; font-weight: 900; color: #94a3b8; text-transform: uppercase; letter-spacing: 2px; margin-bottom: 3px; }
        .section-value { font-size: 10pt; font-weight: 900; text-transform: uppercase; color: #000; }
        .section-sub { font-size: 8pt; color: #64748b; font-weight: 700; }

        .contract-table { width: 100%; border-collapse: collapse; border-top: 2px solid #e2e8f0; }
        .contract-table td { padding: 12px 20px; vertical-align: top; }
        .contract-table .left { border-right: 1px solid #e2e8f0; width: 50%; }
        .amber { color: #d97706; }

        .metrics-table { width: 100%; border-collapse: collapse; border-top: 2px solid #000; border-bottom: 2px solid #000; }
        .metrics-table td { padding: 10px 12px; text-align: center; border-right: 1px solid #e2e8f0; }
        .metrics-table td:last-child { border-right: 0; }
        .metrics-table .metric-label { font-size: 7pt; font-weight: 900; color: #94a3b8; text-transform: uppercase; display: block; }
        .metrics-table .metric-value { font-size: 12pt; font-weight: 900; }
        .bg-gray { background-color: #f8fafc; }
        .indigo { color: #4f46e5; }

        .notas-section { border-top: 2px solid #000; padding: 12px 20px; }
        .notas-title { font-size: 7pt; font-weight: 900; text-transform: uppercase; letter-spacing: 2px; margin-bottom: 10px; color: #4f46e5; }

        .notas-table { width: 100%; border-collapse: collapse; border: 1px solid #e2e8f0; }
        .notas-table th {
            background-color: #f8fafc;
            font-size: 7pt;
            font-weight: 900;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #64748b;
            padding: 8px 6px;
            border-bottom: 2px solid #e2e8f0;
            text-align: left;
        }
        .notas-table th.valor { text-align: right; }
        .notas-table td {
            font-size: 8pt;
            padding: 8px 6px;
            border-bottom: 1px solid #f1f5f9;
            vertical-align: top;
        }
        .notas-table td.valor { text-align: right; font-weight: 900; white-space: nowrap; }
        .notas-table td.numero { font-weight: 900; color: #4f46e5; }
        .notas-table tr.obs td {
            font-size: 7pt;
            color: #64748b;
            font-style: italic;
            padding-top: 0;
            border-bottom: 1px solid #f1f5f9;
        }
        .notas-table tr.total td {
            background-color: #f8fafc;
            font-weight: 900;
            font-size: 9pt;
            border-top: 2px solid #000;
            border-bottom: 0;
        }
        .notas-table tr.total td.valor { font-size: 11pt; }

        .empty-state {
            border: 1px dashed #e2e8f0;
            padding: 24px;
            text-align: center;
            color: #94a3b8;
            font-size: 9pt;
            font-style: italic;
        }

        .footer-note {
            border-top: 1px solid #e2e8f0;
            padding: 10px 20px;
            font-size: 7pt;
            color: #94a3b8;
            text-align: center;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
    </style>
</head>
<body>

    <table class="header-table">
        <tr>
            <td class="header-black">
                <h1>Notas Fiscais</h1>
                <p>Relatório de Documentos Fiscais</p>
            </td>
            <td class="header-date">
                <div class="label">Período</div>
                <div class="date">{{ $dataInicio->format('d/m/Y') }} – {{ $dataFim->format('d/m/Y') }}</div>
                <div class="sub">Emitido em {{ $emitidoEm->format('d/m/Y \à\s H:i') }}</div>
            </td>
        </tr>
    </table>

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

    <table class="metrics-table">
        <tr>
            <td class="bg-gray" style="width: 40%;">
                <span class="metric-label">Valor Total</span>
                <span class="metric-value indigo">R$ {{ number_format($totalValor, 2, ',', '.') }}</span>
            </td>
            <td class="bg-gray" style="width: 30%;">
                <span class="metric-label">Quantidade de Notas</span>
                <span class="metric-value">{{ $totalCount }}</span>
            </td>
            <td style="width: 30%;">
                <span class="metric-label">Filtro Aplicado</span>
                <span class="metric-value" style="font-size: 9pt;">{{ $periodoLabel }}</span>
            </td>
        </tr>
    </table>

    <div class="notas-section">
        <div class="notas-title">Notas do Período</div>

        @if($notas->isEmpty())
            <div class="empty-state">Nenhuma nota fiscal neste período.</div>
        @else
            <table class="notas-table">
                <thead>
                    <tr>
                        <th style="width: 12%;">Data</th>
                        <th style="width: 12%;">Nº Nota</th>
                        <th style="width: 30%;">Descrição</th>
                        <th style="width: 28%;">Recebedor</th>
                        <th class="valor" style="width: 18%;">Valor</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($notas as $nota)
                        <tr>
                            <td>{{ $nota->data_recebimento->format('d/m/Y') }}</td>
                            <td class="numero">{{ $nota->numero_nota }}</td>
                            <td>{{ $nota->descricao }}</td>
                            <td>{{ $nota->quem_recebeu }}</td>
                            <td class="valor">R$ {{ number_format($nota->valor, 2, ',', '.') }}</td>
                        </tr>
                        @if($nota->observacao)
                            <tr class="obs">
                                <td colspan="5">Obs: {{ $nota->observacao }}</td>
                            </tr>
                        @endif
                    @endforeach
                    <tr class="total">
                        <td colspan="4">Total ({{ $totalCount }} {{ $totalCount === 1 ? 'nota' : 'notas' }})</td>
                        <td class="valor">R$ {{ number_format($totalValor, 2, ',', '.') }}</td>
                    </tr>
                </tbody>
            </table>
        @endif
    </div>

    <div class="footer-note">
        Documento gerado pelo sistema de gestão Diário de Obras
    </div>

</body>
</html>
