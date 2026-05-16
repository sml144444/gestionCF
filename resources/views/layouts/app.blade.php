{{-- resources/views/layouts/app.blade.php --}}
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>OFPPT – @yield('title', 'Dashboard')</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:ital,opsz,wght@0,9..40,300;0,9..40,400;0,9..40,500;0,9..40,600;0,9..40,700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/dist/tabler-icons.min.css">

    <style>
    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
    body { font-family: 'DM Sans', system-ui, sans-serif; }

    /* ── Sidebar scrollbar ── */
    #sidebar-nav::-webkit-scrollbar { width: 3px; }
    #sidebar-nav::-webkit-scrollbar-track { background: transparent; }
    #sidebar-nav::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.18); border-radius: 99px; }
    #sidebar-nav { scrollbar-width: thin; scrollbar-color: rgba(255,255,255,0.18) transparent; }

    /* ── Sidebar ── */
    #sidebar { transform: translateX(-100%); transition: transform 0.26s cubic-bezier(.4,0,.2,1); }

    /* ── Navbar ── */
    #navbar {
        height: 56px;
        background: #ffffff;
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 0 16px;
        gap: 8px;
        position: relative;
        border-bottom: 0.5px solid #e8ecf0;
        flex-shrink: 0;
        z-index: 30;
    }

    /* Role-accent strip */
    #navbar::before {
        content: '';
        position: absolute;
        top: 0; left: 0; right: 0;
        height: 2px;
        background: linear-gradient(
            90deg,
            var(--role-color, #1a4f8a) 0%,
            color-mix(in srgb, var(--role-color, #1a4f8a) 40%, #93c5fd) 100%
        );
    }

    /* ── Hamburger ── */
    .ham-btn {
        width: 34px; height: 34px; border-radius: 8px;
        display: flex; align-items: center; justify-content: center;
        background: transparent; border: 0.5px solid transparent;
        cursor: pointer; color: #64748b;
        transition: background .15s, border-color .15s, color .15s;
        flex-shrink: 0;
    }
    .ham-btn:hover { background: #f8fafc; border-color: #e2e8f0; color: #1e293b; }
    .ham-btn:active { transform: scale(.93); }

    /* ── Page title ── */
    .nav-title {
        font-size: 12.5px; font-weight: 600; color: #334155;
        white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
        max-width: 180px;
    }
    .nav-title-dot {
        width: 4px; height: 4px; border-radius: 50%;
        background: var(--role-color, #1a4f8a);
        flex-shrink: 0;
    }

    /* ── Search bar (desktop) ── */
    .search-cmd {
        display: flex; align-items: center; gap: 8px;
        background: #f8fafc; border: 0.5px solid #e2e8f0;
        border-radius: 8px; padding: 0 12px;
        height: 34px;
        cursor: pointer; transition: background .18s, border-color .18s;
        width: 300px; user-select: none;
    }
    .search-cmd:hover { background: #f1f5f9; border-color: #cbd5e1; }
    .search-cmd-text { font-size: 12.5px; color: #94a3b8; flex: 1; font-weight: 400; }
    .search-kbd {
        background: white; border: 0.5px solid #e2e8f0;
        border-radius: 5px; padding: 2px 6px;
        font-size: 10px; font-weight: 600; color: #94a3b8;
        box-shadow: 0 1px 2px rgba(0,0,0,0.05);
        white-space: nowrap; font-family: 'DM Sans', sans-serif;
    }

    /* ── Icon btn (right side) ── */
    .icon-btn {
        width: 34px; height: 34px; border-radius: 8px;
        display: flex; align-items: center; justify-content: center;
        color: #64748b; background: transparent;
        border: 0.5px solid transparent;
        cursor: pointer;
        transition: background .15s, border-color .15s, color .15s, transform .1s;
        position: relative; flex-shrink: 0; text-decoration: none;
    }
    .icon-btn:hover { background: #f8fafc; border-color: #e2e8f0; color: #1e293b; }
    .icon-btn:active { transform: scale(.93); }
    .icon-btn.danger { color: #94a3b8; }
    .icon-btn.danger:hover { background: #fff1f2; border-color: #fecaca; color: #ef4444; }

    /* ── User pill ── */
    .user-pill {
        display: flex; align-items: center; gap: 8px;
        padding: 4px 4px 4px 10px; border-radius: 8px;
        border: 0.5px solid transparent; cursor: pointer;
        transition: background .15s, border-color .15s;
        text-decoration: none; flex-shrink: 0;
    }
    .user-pill:hover { background: #f8fafc; border-color: #e2e8f0; }
    .user-name { font-size: 12.5px; font-weight: 600; color: #1e293b; line-height: 1.2; white-space: nowrap; }
    .user-role { font-size: 10px; color: #94a3b8; text-transform: capitalize; margin-top: 1px; font-weight: 400; }
    .user-avatar {
        width: 30px; height: 30px; border-radius: 8px;
        display: flex; align-items: center; justify-content: center;
        font-size: 11px; font-weight: 700; color: white;
        flex-shrink: 0; letter-spacing: .02em;
    }

    /* ── Divider ── */
    .vdivider { width: 1px; height: 16px; background: #e8ecf0; flex-shrink: 0; }

    /* ════════════════════════════════════════════
       SEARCH MODAL
    ════════════════════════════════════════════ */
    #search-modal {
        position: fixed; inset: 0; z-index: 500;
        display: flex; align-items: flex-start; justify-content: center;
        padding-top: 72px; padding-left: 16px; padding-right: 16px;
        background: rgba(15,23,42,0.48);
        backdrop-filter: blur(10px);
        -webkit-backdrop-filter: blur(10px);
        opacity: 0; visibility: hidden;
        transition: opacity .18s ease, visibility .18s ease;
    }
    #search-modal.open { opacity: 1; visibility: visible; }

    #search-panel {
        width: 100%; max-width: 580px;
        background: #ffffff;
        border-radius: 16px;
        border: 0.5px solid #e2e8f0;
        box-shadow:
            0 0 0 1px rgba(0,0,0,0.04),
            0 8px 24px rgba(0,0,0,0.07),
            0 32px 80px rgba(0,0,0,0.14);
        overflow: hidden;
        transform: translateY(-16px) scale(0.97);
        transition: transform .22s cubic-bezier(.34,1.4,.64,1);
    }
    #search-modal.open #search-panel { transform: translateY(0) scale(1); }

    /* Input row */
    .sp-input-row {
        display: flex; align-items: center; gap: 10px;
        padding: 13px 16px;
        border-bottom: 0.5px solid #f1f5f9;
    }
    #sp-input {
        flex: 1; font-size: 14.5px; font-weight: 500;
        color: #1e293b; background: transparent;
        border: none; outline: none;
        font-family: 'DM Sans', sans-serif;
        letter-spacing: -0.01em;
    }
    #sp-input::placeholder { color: #94a3b8; font-weight: 400; }

    /* Close btn */
    .sp-close {
        background: #f1f5f9; border: 0.5px solid #e2e8f0;
        cursor: pointer;
        width: 26px; height: 26px; border-radius: 7px;
        display: flex; align-items: center; justify-content: center;
        color: #64748b; flex-shrink: 0; transition: all .12s;
    }
    .sp-close:hover { background: #e2e8f0; color: #1e293b; }

    /* Results */
    #sp-results {
        min-height: 80px; max-height: 380px; overflow-y: auto;
    }
    #sp-results::-webkit-scrollbar { width: 4px; }
    #sp-results::-webkit-scrollbar-thumb { background: #e2e8f0; border-radius: 99px; }

    .sp-empty {
        padding: 36px 24px; text-align: center;
    }
    .sp-empty-icon { font-size: 28px; margin-bottom: 10px; }
    .sp-empty-title { font-size: 13px; font-weight: 600; color: #475569; }
    .sp-empty-sub { font-size: 11.5px; color: #94a3b8; margin-top: 4px; line-height: 1.5; }

    /* Result item */
    .sp-item {
        display: flex; align-items: center; gap: 12px;
        padding: 10px 16px; text-decoration: none;
        border-bottom: 0.5px solid #f8fafc; transition: background .1s;
    }
    .sp-item:last-child { border-bottom: none; }
    .sp-item:hover { background: #f8fafc; }
    .sp-item:hover .sp-arrow { opacity: 1; transform: translateX(0); }

    .sp-avatar {
        width: 38px; height: 38px; border-radius: 10px;
        display: flex; align-items: center; justify-content: center;
        font-size: 14px; font-weight: 700; color: white;
        flex-shrink: 0;
    }
    .sp-name { font-size: 13px; font-weight: 600; color: #1e293b; letter-spacing: -0.01em; }
    .sp-meta { font-size: 11px; color: #94a3b8; margin-top: 2px; }
    .sp-promo {
        font-size: 10px; font-weight: 700;
        padding: 3px 9px; border-radius: 99px;
        flex-shrink: 0;
    }
    .sp-arrow {
        color: #cbd5e1; opacity: 0; transform: translateX(-6px);
        transition: all .15s; flex-shrink: 0;
    }

    /* Footer */
    .sp-footer {
        display: flex; align-items: center; justify-content: space-between;
        padding: 8px 16px; border-top: 0.5px solid #f1f5f9;
        background: #fafbfc;
    }
    .sp-hint { display: flex; align-items: center; gap: 4px; font-size: 11px; color: #94a3b8; }

    /* ── Highlight ── */
    mark { background: #fef9c3; color: #92400e; border-radius: 3px; padding: 0 2px; }

    /* ── Spinner ── */
    @keyframes _spin { to { transform: rotate(360deg); } }
    .spin { animation: _spin 1s linear infinite; }

    /* ── Toast ── */
    @keyframes _slide-in { from { transform: translateX(110%); opacity: 0; } to { transform: translateX(0); opacity: 1; } }
    @keyframes _fade-out { to { opacity: 0; transform: translateX(30px); } }

    [x-cloak] { display: none !important; }
    </style>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

@php
    $role = Auth::user()->role;
    $sidebarColors = [
        'admin'        => 'bg-[#0a6640]',
        'gestionnaire' => 'bg-[#1e293b]',
        'formateur'    => 'bg-[#1a4f8a]',
        'stagiaire'    => 'bg-[#1a4f8a]',
    ];
    $roleColors = [
        'admin'        => '#0a6640',
        'gestionnaire' => '#334155',
        'formateur'    => '#1a4f8a',
        'stagiaire'    => '#1a4f8a',
    ];
    $avatarGradients = [
        'admin'        => 'linear-gradient(135deg,#0a6640,#22c55e)',
        'gestionnaire' => 'linear-gradient(135deg,#334155,#64748b)',
        'formateur'    => 'linear-gradient(135deg,#1a4f8a,#60a5fa)',
        'stagiaire'    => 'linear-gradient(135deg,#1a4f8a,#60a5fa)',
    ];
    $sidebarColor   = $sidebarColors[$role]   ?? 'bg-[#1a5fa8]';
    $roleColor      = $roleColors[$role]       ?? '#1a4f8a';
    $avatarGradient = $avatarGradients[$role]  ?? 'linear-gradient(135deg,#1a4f8a,#60a5fa)';
    $userName       = Auth::user()->name;
    $nameParts      = explode(' ', $userName);
    $initials       = strtoupper(substr($nameParts[0] ?? '', 0, 1)) . strtoupper(substr($nameParts[1] ?? '', 0, 1));
@endphp

<body class="h-screen w-screen overflow-hidden bg-slate-100"
      style="--role-color: {{ $roleColor }};">

{{-- ════ BACKDROP ════ --}}
<div id="sidebar-backdrop" onclick="closeSidebar()"
     class="fixed inset-0 z-40 bg-black/40 backdrop-blur-sm"
     style="display:none;opacity:0;transition:opacity .26s ease;"></div>

{{-- ════ SIDEBAR ════ --}}
<aside id="sidebar" class="fixed top-0 left-0 h-full z-50 flex flex-col w-60 {{ $sidebarColor }}">
    <div class="flex items-center gap-3 px-4 py-4 border-b border-white/10 flex-shrink-0 min-h-[60px]">
        <div class="w-9 h-9 rounded-full bg-white flex items-center justify-center flex-shrink-0 shadow overflow-hidden">
            <img src="{{ asset('images/ofppt-logo.webp') }}" alt="OFPPT"
                 class="w-full h-full object-cover" onerror="this.style.display='none'">
        </div>
        <span class="text-white font-bold text-base tracking-widest whitespace-nowrap">OFPPT</span>
    </div>
    <nav class="flex-1 overflow-y-auto py-3 px-2 space-y-0.5" id="sidebar-nav">
        @include('partials.sidebar.sidebar')
    </nav>
    <div class="border-t border-white/10 p-2 flex-shrink-0">
        <button onclick="closeSidebar()"
                class="w-full flex items-center justify-center gap-2 px-3 py-2 rounded-lg
                       text-white/60 hover:text-white hover:bg-white/10 transition-all text-xs">
            <svg class="w-4 h-4 rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 19l-7-7 7-7m8 14l-7-7 7-7"/>
            </svg>
            <span>Fermer</span>
        </button>
    </div>
</aside>

{{-- ════ SEARCH MODAL ════ --}}
{{-- Visible to: admin, gestionnaire, formateur (anyone with search-users permission) --}}
@can('search-users')
<div id="search-modal" onclick="if(event.target===this)closeSearch()">
    <div id="search-panel">

        <div class="sp-input-row">
            <i class="ti ti-search" style="font-size:16px;color:#94a3b8;flex-shrink:0;" aria-hidden="true"></i>
            <input id="sp-input" type="text" placeholder="Rechercher un utilisateur…"
                   oninput="spSearch(this.value)" autocomplete="off" spellcheck="false">
            <div id="sp-spinner" style="display:none;flex-shrink:0;">
                <svg class="spin" style="width:15px;height:15px;color:#94a3b8;" fill="none" viewBox="0 0 24 24">
                    <circle style="opacity:.2;" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                    <path style="opacity:.8;" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"/>
                </svg>
            </div>
            <button class="sp-close" onclick="closeSearch()" aria-label="Fermer la recherche">
                <i class="ti ti-x" style="font-size:13px;" aria-hidden="true"></i>
            </button>
        </div>

        <div id="sp-results">
            <div class="sp-empty">
                <div class="sp-empty-icon">🔍</div>
                <p class="sp-empty-title">Rechercher un utilisateur</p>
                <p class="sp-empty-sub">Tapez au moins 2 caractères (nom, email, CIN…)</p>
            </div>
        </div>

        <div class="sp-footer">
            <div style="display:flex;gap:14px;">
                <span class="sp-hint"><span class="search-kbd">↵</span> Ouvrir</span>
                <span class="sp-hint"><span class="search-kbd">Esc</span> Fermer</span>
            </div>
            <span style="font-size:10px;font-weight:700;color:#d1d5db;letter-spacing:.06em;text-transform:uppercase;">
                OFPPT · Recherche
            </span>
        </div>
    </div>
</div>
@endcan

{{-- ════ LAYOUT ════ --}}
<div class="flex flex-col h-screen w-screen overflow-hidden">

    {{-- ───────────── NAVBAR ───────────── --}}
    <header id="navbar">

        {{-- LEFT: Hamburger + divider + title --}}
        <div style="display:flex;align-items:center;gap:10px;flex-shrink:0;min-width:0;">
            <button class="ham-btn" onclick="openSidebar()" title="Menu" aria-label="Ouvrir le menu">
                <i class="ti ti-menu-2" style="font-size:17px;" aria-hidden="true"></i>
            </button>

            <div class="vdivider"></div>

            <div style="display:flex;align-items:center;gap:6px;min-width:0;">
                <div class="nav-title-dot"></div>
                <span class="nav-title">@yield('page-title', 'Dashboard')</span>
            </div>
        </div>

        {{-- CENTER: Search bar (desktop) --}}
        @can('search-users')
        <div class="hidden md:flex" style="flex:1;justify-content:center;padding:0 24px;">
            <button class="search-cmd" onclick="openSearch()" aria-label="Rechercher un utilisateur">
                <i class="ti ti-search" style="font-size:14px;color:#94a3b8;flex-shrink:0;" aria-hidden="true"></i>
                <span class="search-cmd-text">Rechercher un utilisateur…</span>
                <span class="search-kbd">Ctrl K</span>
            </button>
        </div>
        @endcan

        {{-- RIGHT: Actions --}}
        <div style="display:flex;align-items:center;gap:2px;flex-shrink:0;">

            {{-- Mobile search icon --}}
            @can('search-users')
            <button class="icon-btn md:hidden" onclick="openSearch()" title="Rechercher" aria-label="Rechercher">
                <i class="ti ti-search" style="font-size:16px;" aria-hidden="true"></i>
            </button>
            @endcan

            {{-- Notifications --}}
            @include('partials.notifications.bell')

            <div class="vdivider" style="margin:0 6px;"></div>

            {{-- User pill → profile --}}
            <a href="{{ route('profile.show') }}" class="user-pill">
                <div class="hidden sm:block" style="text-align:right;">
                    <div class="user-name">{{ $userName }}</div>
                    <div class="user-role">{{ $role }}</div>
                </div>
                <div class="user-avatar" style="background:{{ $avatarGradient }};">
                    {{ $initials }}
                </div>
            </a>

            <div class="vdivider" style="margin:0 6px;"></div>

            {{-- Logout --}}
            <form method="POST" action="{{ route('logout') }}" style="display:contents;">
                @csrf
                <button type="submit" class="icon-btn danger" title="Déconnexion" aria-label="Déconnexion">
                    <i class="ti ti-logout" style="font-size:16px;" aria-hidden="true"></i>
                </button>
            </form>
        </div>
    </header>

    {{-- PAGE CONTENT --}}
    <main class="flex-1 overflow-y-auto p-4 sm:p-5">
        @yield('content')
    </main>
</div>

{{-- Toast --}}
<div id="toast-container"
     style="position:fixed;top:18px;right:18px;z-index:9999;display:flex;flex-direction:column;gap:8px;pointer-events:none;"></div>

{{-- ════ JS ════ --}}
<script>
/* ── Sidebar ── */
function openSidebar() {
    const s=document.getElementById('sidebar'), b=document.getElementById('sidebar-backdrop');
    if(s) s.style.transform='translateX(0)';
    if(b){ b.style.display='block'; requestAnimationFrame(()=>requestAnimationFrame(()=>b.style.opacity='1')); }
}
function closeSidebar() {
    const s=document.getElementById('sidebar'), b=document.getElementById('sidebar-backdrop');
    if(s) s.style.transform='translateX(-100%)';
    if(b){ b.style.opacity='0'; setTimeout(()=>b.style.display='none',260); }
}

/* ── Search ── */
let _spOpen=false, _spTimer=null;

function openSearch() {
    const m=document.getElementById('search-modal');
    if(!m) return;
    _spOpen=true; m.classList.add('open');
    document.body.style.overflow='hidden';
    setTimeout(()=>document.getElementById('sp-input')?.focus(), 90);
}
function closeSearch() {
    const m=document.getElementById('search-modal'), i=document.getElementById('sp-input');
    if(!m) return;
    _spOpen=false; m.classList.remove('open');
    document.body.style.overflow='';
    if(i) i.value='';
    document.getElementById('sp-results').innerHTML=`
        <div class="sp-empty">
            <div class="sp-empty-icon">🔍</div>
            <p class="sp-empty-title">Rechercher un utilisateur</p>
            <p class="sp-empty-sub">Tapez au moins 2 caractères (nom, email, CIN…)</p>
        </div>`;
}

/* ── Role colours ── */
const _roleColor = {
    stagiaire:    '#2563eb',
    formateur:    '#7c3aed',
    gestionnaire: '#0f766e',
    admin:        '#dc2626',
};
const _roleLabel = {
    stagiaire:    'Stagiaire',
    formateur:    'Formateur',
    gestionnaire: 'Gestionnaire',
    admin:        'Admin',
};

function spSearch(q) {
    clearTimeout(_spTimer);
    const box  = document.getElementById('sp-results');
    const spin = document.getElementById('sp-spinner');

    if (q.length < 2) {
        if (spin) spin.style.display = 'none';
        box.innerHTML = `
            <div class="sp-empty">
                <div class="sp-empty-icon">🔍</div>
                <p class="sp-empty-title">Rechercher un utilisateur</p>
                <p class="sp-empty-sub">Tapez au moins 2 caractères (nom, email, CIN…)</p>
            </div>`;
        return;
    }

    if (spin) spin.style.display = 'block';

    _spTimer = setTimeout(() => {
        fetch('/users/search?q=' + encodeURIComponent(q), {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(r => r.json())
        .then(data => {
            if (spin) spin.style.display = 'none';

            if (!data.length) {
                box.innerHTML = `
                    <div class="sp-empty">
                        <div class="sp-empty-icon">😕</div>
                        <p class="sp-empty-title">Aucun résultat</p>
                        <p class="sp-empty-sub">Aucun utilisateur ne correspond à "<strong>${escHtml(q)}</strong>"</p>
                    </div>`;
                return;
            }

            box.innerHTML = data.map(s => {
                const color  = _roleColor[s.role]  ?? '#64748b';
                const label  = _roleLabel[s.role]  ?? s.role;
                const avatar = `linear-gradient(135deg, ${color}, ${color}99)`;
                return `
                <a href="${s.url}" class="sp-item">
                    <div class="sp-avatar" style="background:${avatar};">
                        ${escHtml(s.name.charAt(0).toUpperCase())}
                    </div>
                    <div style="flex:1;min-width:0;">
                        <div class="sp-name">${hlMatch(s.name, q)}</div>
                        <div class="sp-meta">${escHtml(s.filiere)} · ${escHtml(s.groupe)}</div>
                    </div>
                    <span class="sp-promo"
                          style="background:${color}18;color:${color};border:0.5px solid ${color}33;">
                        ${escHtml(label)}
                    </span>
                    <i class="ti ti-chevron-right sp-arrow" style="font-size:14px;" aria-hidden="true"></i>
                </a>`;
            }).join('');
        })
        .catch(() => {
            if (spin) spin.style.display = 'none';
            box.innerHTML = `<div class="sp-empty"><p class="sp-empty-sub" style="color:#f87171;">Erreur — réessayez.</p></div>`;
        });
    }, 260);
}

function hlMatch(text, q) {
    const esc = q.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
    return escHtml(text).replace(new RegExp(`(${esc})`, 'gi'), '<mark>$1</mark>');
}
function escHtml(s) {
    return String(s)
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;');
}

/* ── Keyboard shortcuts ── */
document.addEventListener('keydown', e => {
    if ((e.ctrlKey || e.metaKey) && e.key === 'k') {
        e.preventDefault();
        _spOpen ? closeSearch() : openSearch();
    }
    if (e.key === 'Escape') {
        closeSidebar();
        if (_spOpen) closeSearch();
    }
});
</script>

{{-- ════ REVERB / ECHO ════ --}}
<script src="https://js.pusher.com/8.4/pusher.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/laravel-echo@1.16.1/dist/echo.iife.js"></script>
<script>
window.Pusher = Pusher;
window.Echo = new Echo({
    broadcaster: 'reverb',
    key:         '{{ env("REVERB_APP_KEY") }}',
    wsHost:      '{{ env("REVERB_HOST","localhost") }}',
    wsPort:      {{ env("REVERB_PORT",8080) }},
    wssPort:     {{ env("REVERB_PORT",8080) }},
    forceTLS:    false,
    enabledTransports: ['ws', 'wss'],
});

function showToast(icon, title, body, url) {
    const c = document.getElementById('toast-container');
    const t = document.createElement('div');
    t.style.cssText = `pointer-events:auto;background:white;border-radius:12px;border:0.5px solid #e2e8f0;
        padding:12px 14px;min-width:270px;max-width:340px;
        box-shadow:0 8px 24px rgba(0,0,0,0.09);display:flex;gap:10px;align-items:flex-start;
        animation:_slide-in .28s ease;cursor:${url ? 'pointer' : 'default'};font-family:'DM Sans',sans-serif;`;
    t.innerHTML = `
        <div style="font-size:18px;flex-shrink:0;margin-top:1px;">${icon}</div>
        <div style="flex:1;">
            <div style="font-size:12.5px;font-weight:700;color:#1e293b;margin-bottom:3px;">${title}</div>
            <div style="font-size:11.5px;color:#64748b;line-height:1.5;">${body}</div>
        </div>
        <button onclick="this.parentElement.remove()"
                style="background:none;border:none;cursor:pointer;color:#94a3b8;font-size:16px;padding:0;line-height:1;flex-shrink:0;">×</button>`;
    if (url) t.addEventListener('click', e => { if (e.target.tagName !== 'BUTTON') location.href = url; });
    c.appendChild(t);
    setTimeout(() => { t.style.animation = '_fade-out .3s ease forwards'; setTimeout(() => t.remove(), 300); }, 5700);
}

@auth
window.Echo.private('user.{{ Auth::id() }}')
    .listen('.ReclamationAssigned', e => showToast('📋', 'Réclamation assignée', `Réclamation #${e.reclamation_id} de ${e.stagiaire} vous a été assignée.`, e.url))
    .listen('.ReclamationDeleted', e => {
        showToast('🗑️', 'Réclamation supprimée', `La réclamation #${e.reclamation_id} a été supprimée.`, null);
        const row = document.getElementById('rec-row-' + e.reclamation_id);
        if (row) { row.style.animation = '_fade-out .4s ease forwards'; setTimeout(() => row.remove(), 400); }
        if (location.href.includes('/reclamations/' + e.reclamation_id)) setTimeout(() => location.href = '{{ route("reclamations.index") }}', 2000);
    })
    .listen('.NotificationCreated', e => {
        const cur = window.__currentReclamationId ?? null;
        if (cur && e.data?.reclamation_id && cur === e.data.reclamation_id) {
            fetch(`/notifications/${e.id}/read`, { method: 'POST', headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content, 'Accept': 'application/json' } })
                .then(r => r.json()).then(d => { if (typeof setUnreadCount === 'function') setUnreadCount(d.unread_count ?? 0); });
            return;
        }
        if (typeof prependNotification === 'function') prependNotification(e);
    })
    .listen('.NotificationUpdated', e => {
        const cur = window.__currentReclamationId ?? null;
        if (cur && e.data?.reclamation_id && cur === e.data.reclamation_id) {
            fetch(`/notifications/${e.id}/read`, { method: 'POST', headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content, 'Accept': 'application/json' } })
                .then(r => r.json()).then(d => { if (typeof setUnreadCount === 'function') setUnreadCount(d.unread_count ?? 0); });
            return;
        }
        if (typeof patchNotification === 'function') patchNotification(e);
    });

@can('reclamation-manage')
window.Echo.channel('reclamations.admin')
    .listen('.ReclamationCreated', e => {
        showToast(e.type_icon, 'Nouvelle réclamation', `${e.stagiaire} : ${e.description}`, e.url);
        const b = document.getElementById('reclamations-count');
        if (b) b.textContent = parseInt(b.textContent || 0) + 1;
    })
    .listen('.ReclamationDeleted', e => {
        showToast('🗑️', 'Réclamation supprimée', `Réclamation #${e.reclamation_id} supprimée.`, null);
        const row = document.getElementById('rec-row-' + e.reclamation_id);
        if (row) { row.style.animation = '_fade-out .4s ease forwards'; setTimeout(() => row.remove(), 400); }
    })
    .listen('.ReclamationStatusUpdated', e => {
        const row = document.getElementById('rec-row-' + e.reclamation_id);
        if (!row) return;
        const badge = row.querySelector('.status-badge');
        if (badge) { badge.textContent = e.icon + ' ' + e.label; badge.style.background = e.bg; badge.style.color = e.color; badge.style.border = '1px solid ' + e.border; }
        showToast(e.icon, 'Statut mis à jour', `Réclamation #${e.reclamation_id} → ${e.label}`, null);
    });
@endcan
@endauth
</script>

@stack('scripts')
</body>
</html>