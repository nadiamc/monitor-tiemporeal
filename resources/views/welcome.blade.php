<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Monitor de Servidor en Tiempo Real</title>
    <style>
        :root {
            --bg: #0b1026;
            --card: rgba(255, 255, 255, .08);
            --line: rgba(255, 255, 255, .16);
            --text: #f8fafc;
            --muted: #b9c4e6;
            --accent: #67e8f9;
        }

        * { box-sizing: border-box; }

        body {
            min-height: 100vh;
            margin: 0;
            display: grid;
            place-items: center;
            padding: 24px;
            color: var(--text);
            font-family: ui-sans-serif, system-ui, -apple-system, "Segoe UI", sans-serif;
            background:
                radial-gradient(60vw 60vw at 8% -10%, rgba(124, 58, 237, .45), transparent 60%),
                radial-gradient(50vw 50vw at 95% 5%, rgba(14, 165, 233, .35), transparent 55%),
                linear-gradient(140deg, #0b1026 0%, #171340 45%, #0b1a3a 100%);
        }

        main {
            width: 100%;
            max-width: 880px;
        }

        .eyebrow {
            margin: 0 0 12px;
            color: var(--accent);
            font-size: 12px;
            font-weight: 700;
            letter-spacing: .18em;
            text-transform: uppercase;
        }

        h1 {
            margin: 0;
            font-size: clamp(2rem, 6vw, 3.4rem);
            line-height: 1.05;
            letter-spacing: -.03em;
            background: linear-gradient(90deg, #ffffff, #c7d2fe 55%, #67e8f9);
            -webkit-background-clip: text;
            background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .lead {
            max-width: 620px;
            margin: 18px 0 32px;
            color: var(--muted);
            font-size: 17px;
            line-height: 1.6;
        }

        .cards {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
            gap: 16px;
        }

        .card {
            display: block;
            padding: 22px;
            color: inherit;
            text-decoration: none;
            background: var(--card);
            border: 1px solid var(--line);
            border-radius: 18px;
            box-shadow: inset 0 1px 0 rgba(255, 255, 255, .12);
            transition: transform .2s ease, border-color .2s ease, background .2s ease;
        }

        .card:hover {
            transform: translateY(-3px);
            border-color: rgba(103, 232, 249, .5);
            background: rgba(255, 255, 255, .12);
        }

        .card strong {
            display: block;
            margin-bottom: 6px;
            font-size: 18px;
        }

        .card span {
            color: var(--muted);
            font-size: 14px;
            line-height: 1.5;
        }

        .tags {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            margin-top: 28px;
        }

        .tag {
            padding: 5px 10px;
            border: 1px solid var(--line);
            border-radius: 999px;
            color: var(--muted);
            font-size: 12px;
        }

        footer {
            margin-top: 34px;
            color: rgba(185, 196, 230, .6);
            font-size: 13px;
        }
    </style>
</head>
<body>
    <main>
        <p class="eyebrow">Programación IV · TP 2</p>
        <h1>Monitor de Servidor<br>en Tiempo Real</h1>
        <p class="lead">
            Las métricas del servidor viajan por WebSocket con Laravel Reverb y se
            actualizan en todos los navegadores al mismo tiempo. También podés abrir
            programas en el servidor y ver la actividad en vivo.
        </p>

        <div class="cards">
            <a class="card" href="/servidor">
                <strong>Monitor en vivo →</strong>
                <span>CPU, memoria, disco, uptime y actividad de los botones.</span>
            </a>
            <a class="card" href="/graficos">
                <strong>Gráficos en tiempo real →</strong>
                <span>Historial de CPU y memoria, más el medidor de disco.</span>
            </a>
        </div>

        <div class="tags">
            <span class="tag">Laravel</span>
            <span class="tag">Reverb</span>
            <span class="tag">Laravel Echo</span>
            <span class="tag">Chart.js</span>
            <span class="tag">PowerShell</span>
        </div>

        <footer>Las métricas se emiten desde el scheduler de Laravel y se distribuyen por el canal <code>server.metrics</code>.</footer>
    </main>
</body>
</html>
