<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Você está offline - Diário de Obras</title>
    <style>
        :root {
            --bg-color: #0f172a;
            --card-bg: rgba(255, 255, 255, 0.1);
            --text-main: #f8fafc;
            --text-dim: #94a3b8;
            --accent: #f59e0b;
            --accent-hover: #d97706;
        }
        body {
            margin: 0;
            padding: 0;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
            background-color: var(--bg-color);
            background-image: radial-gradient(circle at top right, #1e293b, #0f172a);
            color: var(--text-main);
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            -webkit-font-smoothing: antialiased;
        }
        .card {
            width: 90%;
            max-width: 400px;
            padding: 40px 30px;
            background: var(--card-bg);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 24px;
            text-align: center;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
        }
        .icon-container {
            width: 80px;
            height: 80px;
            background: rgba(15, 23, 42, 0.6);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 24px;
            border: 1px solid rgba(255, 255, 255, 0.1);
        }
        .icon-container svg {
            width: 40px;
            height: 40px;
            color: var(--accent);
        }
        h2 {
            margin: 0 0 12px;
            font-size: 24px;
            font-weight: 700;
        }
        p {
            margin: 0 0 32px;
            color: var(--text-dim);
            line-height: 1.6;
        }
        button {
            width: 100%;
            padding: 14px;
            background: var(--accent);
            color: #0f172a;
            border: none;
            border-radius: 12px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s;
        }
        button:hover {
            background: var(--accent-hover);
            transform: translateY(-1px);
        }
        button:active {
            transform: translateY(0);
        }
    </style>
</head>
<body>
    <div class="card">
        <div class="icon-container">
            <svg fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 15a4 4 0 004 4h9a5 5 0 10-.1-9.999 5.002 5.002 0 10-9.78 2.096A4.001 4.001 0 003 15z" />
            </svg>
        </div>
        <h2>Sem conexão</h2>
        <p>Parece que você está offline. O <strong>Diário de Obras</strong> está aguardando o sinal voltar para continuar.</p>
        <button onclick="window.location.reload()">Tentar Novamente</button>
    </div>
</body>
</html>
