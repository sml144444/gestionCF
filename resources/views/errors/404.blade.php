<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 — Page introuvable | OFPPT</title>
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        :root {
            --font-sans: 'Segoe UI', system-ui, -apple-system, BlinkMacSystemFont, sans-serif;
            --font-mono: 'Consolas', 'Courier New', monospace;
            --green-900: #033d22; --green-800: #0a6640; --green-700: #0d7a4c; --green-600: #16a85e;
            --green-100: #e8f5ee; --green-50: #f0fdf4;
            --red: #c8102e; --gold: #c9a84c;
            --slate-900: #0f172a; --slate-700: #1e293b; --slate-600: #475569; --slate-500: #64748b;
            --slate-300: #cbd5e1; --slate-100: #f1f5f9; --white: #ffffff;
        }
        html, body { height: 100%; font-family: var(--font-sans); background: var(--green-50); color: var(--slate-900); overflow: hidden; }
        .bg { position: fixed; inset: 0; z-index: 0; background: radial-gradient(ellipse 80% 60% at 10% 90%, rgba(10,102,64,0.15) 0%, transparent 60%), radial-gradient(ellipse 60% 50% at 90% 10%, rgba(200,16,46,0.05) 0%, transparent 55%), radial-gradient(ellipse 100% 80% at 50% 50%, var(--green-50) 0%, var(--white) 100%); }
        .bg::before { content: ''; position: absolute; inset: 0; background-image: linear-gradient(rgba(0,0,0,0.03) 1px, transparent 1px), linear-gradient(90deg, rgba(0,0,0,0.03) 1px, transparent 1px); background-size: 48px 48px; mask-image: radial-gradient(ellipse 70% 70% at 50% 50%, black 20%, transparent 80%); -webkit-mask-image: radial-gradient(ellipse 70% 70% at 50% 50%, black 20%, transparent 80%); }
        .orb { position: fixed; width: 480px; height: 480px; border-radius: 50%; background: radial-gradient(circle, rgba(10,102,64,0.15) 0%, transparent 70%); top: -120px; left: -80px; animation: pulse 6s ease-in-out infinite; z-index: 0; }
        .orb-2 { position: fixed; width: 320px; height: 320px; border-radius: 50%; background: radial-gradient(circle, rgba(200,16,46,0.08) 0%, transparent 70%); bottom: -80px; right: -60px; animation: pulse 8s ease-in-out infinite reverse; z-index: 0; }
        @keyframes pulse { 0%, 100% { transform: scale(1); opacity: 1; } 50% { transform: scale(1.1); opacity: 0.7; } }
        .wrapper { position: relative; z-index: 10; height: 100vh; display: flex; flex-direction: column; align-items: center; justify-content: center; padding: 24px; gap: 0; }
        .topbar { position: fixed; top: 0; left: 0; right: 0; z-index: 20; padding: 14px 32px; display: flex; align-items: center; justify-content: space-between; border-bottom: 1px solid rgba(0,0,0,0.05); background: rgba(255,255,255,0.92); backdrop-filter: blur(8px); -webkit-backdrop-filter: blur(8px); }
        .topbar-brand { display: flex; align-items: center; gap: 12px; text-decoration: none; }
        .topbar-logo { width: 40px; height: 40px; border-radius: 10px; background: linear-gradient(135deg, var(--green-800), var(--green-600)); display: flex; align-items: center; justify-content: center; flex-shrink: 0; box-shadow: 0 4px 12px rgba(10,102,64,0.25); }
        .topbar-name { font-size: 13px; font-weight: 700; color: var(--slate-900); letter-spacing: 0.5px; }
        .topbar-sub { font-size: 9px; color: var(--slate-500); letter-spacing: 1.5px; text-transform: uppercase; margin-top: 1px; }
        .topbar-status { display: flex; align-items: center; gap: 7px; font-size: 11px; color: var(--slate-600); font-family: var(--font-mono); font-weight: 600; }
        .status-dot { width: 6px; height: 6px; border-radius: 50%; background: var(--red); box-shadow: 0 0 8px rgba(200,16,46,0.6); animation: blink 2s step-end infinite; }
        @keyframes blink { 0%, 100% { opacity: 1; } 50% { opacity: 0.3; } }
        .card { background: var(--white); border: 1px solid rgba(0,0,0,0.06); border-radius: 28px; padding: 52px 56px 44px; max-width: 560px; width: 100%; text-align: center; box-shadow: 0 0 0 1px rgba(10,102,64,0.03), 0 24px 64px rgba(0,0,0,0.06), 0 8px 24px rgba(10,102,64,0.04); animation: slideUp 0.6s cubic-bezier(0.16,1,0.3,1) both; }
        @keyframes slideUp { from { opacity: 0; transform: translateY(32px); } to { opacity: 1; transform: translateY(0); } }
        .shield-wrap { position: relative; width: 96px; height: 96px; margin: 0 auto 28px; }
        .shield-ring { position: absolute; inset: -12px; border-radius: 50%; border: 1px solid rgba(200,16,46,0.15); animation: spin 12s linear infinite; }
        .shield-ring::before { content: ''; position: absolute; top: -2px; left: 50%; transform: translateX(-50%); width: 6px; height: 6px; background: var(--red); border-radius: 50%; box-shadow: 0 0 8px rgba(200,16,46,0.6); }
        @keyframes spin { to { transform: rotate(360deg); } }
        .shield-icon { width: 96px; height: 96px; border-radius: 50%; background: linear-gradient(135deg, rgba(200,16,46,0.08), rgba(200,16,46,0.02)); border: 1.5px solid rgba(200,16,46,0.15); display: flex; align-items: center; justify-content: center; box-shadow: 0 0 24px rgba(200,16,46,0.08); }
        .code-badge { display: inline-flex; align-items: center; gap: 8px; background: rgba(200,16,46,0.06); border: 1px solid rgba(200,16,46,0.15); border-radius: 99px; padding: 5px 14px 5px 10px; margin-bottom: 20px; }
        .code-badge-dot { width: 5px; height: 5px; border-radius: 50%; background: var(--red); }
        .code-badge-text { font-family: var(--font-mono); font-size: 10px; font-weight: 700; color: var(--red); letter-spacing: 2px; text-transform: uppercase; }
        .title-403 { font-size: 72px; font-weight: 800; line-height: 1; letter-spacing: -3px; background: linear-gradient(135deg, var(--green-900) 0%, var(--green-600) 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text; margin-bottom: 8px; }
        .title-main { font-size: 18px; font-weight: 700; color: var(--slate-900); margin-bottom: 12px; letter-spacing: -0.3px; }
        .desc { font-size: 13px; color: var(--slate-600); line-height: 1.7; max-width: 380px; margin: 0 auto 32px; }
        .desc strong { color: var(--slate-900); font-weight: 700; }
        .divider { width: 100%; height: 1px; background: linear-gradient(90deg, transparent, rgba(0,0,0,0.06), transparent); margin-bottom: 28px; }
        .info-row { display: flex; gap: 10px; justify-content: center; flex-wrap: wrap; margin-bottom: 28px; }
        .info-pill { display: inline-flex; align-items: center; gap: 6px; padding: 6px 12px; border-radius: 10px; background: rgba(0,0,0,0.03); border: 1px solid rgba(0,0,0,0.06); font-size: 10px; color: var(--slate-600); font-family: var(--font-mono); font-weight: 500; }
        .info-pill .pill-icon { width: 14px; height: 14px; opacity: 0.7; }
        .actions { display: flex; gap: 10px; justify-content: center; flex-wrap: wrap; }
        .btn-primary { display: inline-flex; align-items: center; gap: 8px; padding: 12px 24px; border-radius: 12px; background: linear-gradient(135deg, var(--green-800), var(--green-700)); color: var(--white); font-family: var(--font-sans); font-size: 13px; font-weight: 700; text-decoration: none; border: none; cursor: pointer; box-shadow: 0 4px 16px rgba(10,102,64,0.25); transition: all 0.2s ease; letter-spacing: 0.2px; }
        .btn-primary:hover { transform: translateY(-2px); box-shadow: 0 8px 24px rgba(10,102,64,0.35); background: linear-gradient(135deg, var(--green-700), var(--green-600)); }
        .btn-primary:active { transform: translateY(0); }
        .btn-ghost { display: inline-flex; align-items: center; gap: 8px; padding: 12px 20px; border-radius: 12px; background: rgba(0,0,0,0.03); color: var(--slate-700); font-family: var(--font-sans); font-size: 13px; font-weight: 600; text-decoration: none; border: 1px solid rgba(0,0,0,0.06); cursor: pointer; transition: all 0.2s ease; letter-spacing: 0.2px; }
        .btn-ghost:hover { background: rgba(0,0,0,0.06); color: var(--slate-900); border-color: rgba(0,0,0,0.1); }
        .footer { position: fixed; bottom: 0; left: 0; right: 0; z-index: 20; padding: 14px 32px; display: flex; align-items: center; justify-content: space-between; border-top: 1px solid rgba(0,0,0,0.05); background: rgba(255,255,255,0.6); backdrop-filter: blur(8px); -webkit-backdrop-filter: blur(8px); }
        .footer-left { font-size: 10px; color: var(--slate-500); letter-spacing: 0.5px; font-weight: 500; }
        .footer-right { display: flex; align-items: center; gap: 6px; }
        .footer-flag { display: flex; gap: 3px; }
        .flag-stripe { width: 16px; height: 10px; border-radius: 2px; }
        .footer-country { font-size: 10px; color: var(--slate-500); font-weight: 500; }
        .moroccan-star { position: fixed; bottom: 48px; right: 48px; opacity: 0.04; fill: var(--green-800); animation: slowSpin 30s linear infinite; }
        @keyframes slowSpin { to { transform: rotate(360deg); } }
        @media (max-width: 600px) { .card { padding: 36px 24px 32px; border-radius: 20px; } .title-403 { font-size: 56px; } .title-main { font-size: 16px; } .topbar { padding: 12px 16px; } .moroccan-star { display: none; } }
    </style>
</head>
<body>
<div class="bg"></div>
<div class="orb"></div>
<div class="orb-2"></div>

<header class="topbar">
    <a href="/" class="topbar-brand">
        <div class="topbar-logo">
            <svg width="22" height="22" fill="none" viewBox="0 0 24 24">
                <path stroke="white" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 3l1.68 3.405L17.5 7.07l-2.75 2.68.65 3.78L12 11.77l-3.4 1.76.65-3.78L6.5 7.07l3.82-.665L12 3z"/>
                <path stroke="white" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M5 20h14M8 20v-5M12 20v-8M16 20v-5"/>
            </svg>
        </div>
        <div>
            <div class="topbar-name">OFPPT</div>
            <div class="topbar-sub">Plateforme de formation</div>
        </div>
    </a>
    <div class="topbar-status">
        <span class="status-dot"></span>
        PAGE INTROUVABLE
    </div>
</header>

<svg class="moroccan-star" width="320" height="320" viewBox="0 0 100 100">
    <polygon points="50,5 61,35 95,35 68,57 79,91 50,70 21,91 32,57 5,35 39,35"/>
</svg>

<div class="wrapper">
    <div class="card">
        <div class="shield-wrap">
            <div class="shield-ring"></div>
            <div class="shield-icon">
                <svg width="42" height="42" fill="none" viewBox="0 0 24 24">
                    <circle cx="11" cy="11" r="6" stroke="rgba(200,16,46,0.9)" stroke-width="1.5"/>
                    <path stroke="rgba(200,16,46,0.9)" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 21l-3.5-3.5"/>
                    <path stroke="rgba(200,16,46,0.65)" stroke-linecap="round" stroke-width="2" d="M11 8.5v2.5M11 13.5h.01"/>
                </svg>
            </div>
        </div>

        <div class="code-badge">
            <span class="code-badge-dot"></span>
            <span class="code-badge-text">ERREUR 404</span>
        </div>

        <div class="title-403">404</div>
        <h1 class="title-main">Page introuvable</h1>
        <p class="desc">
            La page que vous recherchez <strong>n'existe pas</strong> ou a été déplacée.<br>
            Vérifiez l'URL ou retournez à votre <strong>tableau de bord</strong>.
        </p>

        <div class="divider"></div>

        <div class="info-row">
            <span class="info-pill">
                <svg class="pill-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.1-1.1m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/>
                </svg>
                {{ request()->path() }}
            </span>
            <span class="info-pill">
                <svg class="pill-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                {{ now()->format('d/m/Y H:i') }}
            </span>
        </div>

        <div class="actions">
            @auth
            <a href="{{ url()->previous() !== url()->current() ? url()->previous() : '/' }}" class="btn-ghost">
                <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"/>
                </svg>
                Retour
            </a>
            <a href="{{ route('redirect.by.role') }}" class="btn-primary">
                <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                </svg>
                Mon tableau de bord
            </a>
            @else
            <a href="{{ route('login') }}" class="btn-primary">
                <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/>
                </svg>
                Se connecter
            </a>
            @endauth
        </div>
    </div>
</div>

<footer class="footer">
    <span class="footer-left">© {{ date('Y') }} OFPPT — Office de la Formation Professionnelle et de la Promotion du Travail</span>
    <div class="footer-right">
        <div class="footer-flag">
            <div class="flag-stripe" style="background:#c8102e;"></div>
            <div class="flag-stripe" style="background:#c8102e;"></div>
        </div>
        <span class="footer-country">Maroc</span>
    </div>
</footer>
</body>
</html>