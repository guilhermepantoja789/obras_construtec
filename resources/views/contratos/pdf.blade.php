<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Contrato - {{ $obra->nome }}</title>
    <style>
        @page {
            margin: 3cm 2cm 3cm 2cm;
        }
        body {
            font-family: 'Helvetica', sans-serif;
            font-size: 10pt;
            line-height: 1.6;
            color: #1e293b;
            text-align: justify;
        }
        .header {
            position: fixed;
            top: -2cm;
            left: 0;
            right: 0;
            height: 2cm;
            border-bottom: 2px solid #f59e0b;
            padding-bottom: 10px;
        }
        .header-title {
            font-size: 14pt;
            font-weight: bold;
            color: #0f172a;
            text-transform: uppercase;
            letter-spacing: 2px;
        }
        .footer {
            position: fixed;
            bottom: -2cm;
            left: 0;
            right: 0;
            height: 1.5cm;
            font-size: 8pt;
            color: #64748b;
            text-align: center;
            border-top: 1px solid #e2e8f0;
            padding-top: 5px;
        }
        .title {
            text-align: center;
            font-size: 16pt;
            font-weight: bold;
            margin-bottom: 30px;
            text-decoration: underline;
            color: #0f172a;
        }
        .content {
            white-space: pre-wrap;
        }
        .assinaturas {
            margin-top: 50px;
            width: 100%;
        }
        .assinatura-box {
            width: 45%;
            display: inline-block;
            text-align: center;
            vertical-align: top;
        }
        .linha {
            border-top: 1px solid #000;
            width: 80%;
            margin: 40px auto 5px;
        }
        .nome {
            font-weight: bold;
            font-size: 9pt;
        }
        .cargo {
            font-size: 8pt;
            color: #64748b;
        }
    </style>
</head>
<body>
    <div class="header">
        <table width="100%">
            <tr>
                <td><span class="header-title">{{ $obra->empresa_contratada }}</span></td>
                <td align="right" style="font-size: 8pt; color: #64748b;">
                    Obra: {{ $obra->nome }}<br>
                    CNPJ: {{ $obra->cnpj_empresa_contratada }}
                </td>
            </tr>
        </table>
    </div>

    <div class="footer">
        Este documento é parte integrante do sistema de gestão Diário de Obras - Página {PAGINA}
    </div>

    <div class="title">CONTRATO DE PRESTAÇÃO DE SERVIÇOS DE ENGENHARIA</div>

    <div class="content">
        {!! nl2br(e($contrato->conteudo)) !!}
    </div>

    <div class="assinaturas">
        <div class="assinatura-box">
            <div class="linha"></div>
            <div class="nome">{{ $obra->contratante }}</div>
            <div class="cargo">CONTRATANTE</div>
        </div>
        <div style="width: 8%; display: inline-block;"></div>
        <div class="assinatura-box">
            <div class="linha"></div>
            <div class="nome">{{ $obra->empresa_contratada }}</div>
            <div class="cargo">CONTRATADA</div>
        </div>
    </div>

    <div style="margin-top: 30px;">
        <div class="assinatura-box">
            <div class="linha"></div>
            <div class="nome">{{ $obra->engenheiro_responsavel }}</div>
            <div class="cargo">Responsável Técnico / Engenheiro</div>
        </div>
    </div>
</body>
</html>
