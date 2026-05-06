{{-- resources/views/layouts/app.blade.php --}}
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>OFPPT – @yield('title', 'Dashboard')</title>
    <style>
    #sidebar-nav::-webkit-scrollbar { width: 4px; }
    #sidebar-nav::-webkit-scrollbar-track { background: transparent; }
    #sidebar-nav::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.15); border-radius: 99px; }
    #sidebar-nav::-webkit-scrollbar-thumb:hover { background: rgba(255,255,255,0.3); }
    #sidebar-nav { scrollbar-width: thin; scrollbar-color: rgba(255,255,255,0.15) transparent; }
    </style>
    <style>
    @keyframes fadeOut {
        from { opacity:1; transform:translateX(0); }
        to   { opacity:0; transform:translateX(30px); }
    }
    </style>
    <style>
        #sidebar {
            transform: translateX(-100%);
            transition: transform 0.3s ease-in-out;
        }
    </style>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-screen w-screen overflow-hidden bg-slate-100">

@php
    $sidebarColors = [
        'admin'        => 'bg-[#0a6640]',
        'gestionnaire' => 'bg-[#1e293b]',
        'formateur'    => 'bg-[#1a4f8a]',
        'stagiaire'    => 'bg-[#1a4f8a]',
    ];
    $avatarColors = [
        'admin'        => 'bg-[#0a6640]',
        'gestionnaire' => 'bg-slate-500',
        'formateur'    => 'bg-[#1a4f8a]',
        'stagiaire'    => 'bg-[#1a4f8a]',
    ];
    $badgeStyles = [
        'admin'        => 'bg-emerald-100 text-emerald-800',
        'gestionnaire' => 'bg-slate-200 text-slate-700',
        'formateur'    => 'bg-blue-100 text-blue-800',
        'stagiaire'    => 'bg-blue-100 text-blue-800',
    ];
    $sidebarColor = $sidebarColors[Auth::user()->role] ?? 'bg-[#1a5fa8]';
    $avatarColor  = $avatarColors[Auth::user()->role]  ?? 'bg-[#1a5fa8]';
    $badgeStyle   = $badgeStyles[Auth::user()->role]   ?? 'bg-blue-100 text-blue-700';
@endphp

{{-- ════ BACKDROP ════ --}}
<div id="sidebar-backdrop"
     onclick="closeSidebar()"
     class="fixed inset-0 z-40 bg-black/40 backdrop-blur-sm"
     style="display:none; opacity:0; transition:opacity 0.3s ease;">
</div>

{{-- ════ SIDEBAR ════ --}}
<aside id="sidebar"
       class="fixed top-0 left-0 h-full z-50 flex flex-col w-60 {{ $sidebarColor }}">

    {{-- Logo --}}
    <div class="flex items-center gap-3 px-4 py-4 border-b border-white/10 flex-shrink-0 min-h-[60px]">
        <div class="w-9 h-9 rounded-full bg-white flex items-center justify-center flex-shrink-0 shadow overflow-hidden">
            <img src="{{ asset('images/ofppt-logo.webp') }}" alt="OFPPT"
                 class="w-full h-full object-cover"
                 onerror="this.style.display='none'">
        </div>
        <span class="text-white font-bold text-base tracking-widest whitespace-nowrap">
            OFPPT
        </span>
    </div>

    {{-- ── NAVIGATION ── --}}
    <nav class="flex-1 overflow-y-auto py-3 px-2 space-y-0.5" id="sidebar-nav">
        @include('partials.sidebar.sidebar')
    </nav>

    {{-- Close button --}}
    <div class="border-t border-white/10 p-2 flex-shrink-0">
        <button onclick="closeSidebar()"
                class="w-full flex items-center justify-center gap-2 px-3 py-2 rounded-lg
                       text-white/60 hover:text-white hover:bg-white/10 transition-all text-xs">
            <svg class="w-4 h-4 flex-shrink-0 rotate-180"
                 fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M11 19l-7-7 7-7m8 14l-7-7 7-7"/>
            </svg>
            <span>Fermer</span>
        </button>
    </div>
</aside>

{{-- ════ MAIN ════ --}}
<div class="flex flex-col h-screen w-screen overflow-hidden">

    {{-- NAVBAR --}}
    <header class="h-[56px] bg-white border-b border-slate-100 flex items-center
                   justify-between px-5 flex-shrink-0 z-30 relative">

        {{-- LEFT: Hamburger + title --}}
        <div class="flex items-center gap-4">
            <button onclick="openSidebar()"
                    class="w-8 h-8 flex items-center justify-center rounded-lg
                           text-slate-400 hover:text-slate-700 hover:bg-slate-50 transition-all">
                <svg class="w-[18px] h-[18px]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M4 6h16M4 12h10M4 18h16"/>
                </svg>
            </button>
            <h1 class="text-sm font-semibold text-slate-600 tracking-tight">@yield('page-title', 'Dashboard')</h1>
        </div>

        {{-- RIGHT --}}
        <div class="flex items-center gap-1">

            {{-- ════ NOTIFICATION BELL (replaces old static bell) ════ --}}
            @include('partials.notifications.bell')

            {{-- Divider --}}
            <div class="w-px h-4 bg-slate-200 mx-2"></div>

            {{-- User --}}
            <div class="flex items-center gap-2.5">
                <div class="text-right hidden sm:block">
                    <p class="text-xs font-semibold text-slate-700 leading-none">{{ Auth::user()->name }}</p>
                    <p class="text-[10px] text-slate-400 mt-0.5">{{ ucfirst(Auth::user()->role) }}</p>
                </div>
                <div class="w-8 h-8 rounded-full flex items-center justify-center
                            text-[11px] font-bold text-white {{ $avatarColor }}">
                    {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}{{ strtoupper(substr(explode(' ', Auth::user()->name)[1] ?? '', 0, 1)) }}
                </div>
            </div>

            {{-- Divider --}}
            <div class="w-px h-4 bg-slate-200 mx-2"></div>

            {{-- Logout --}}
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit"
                        class="w-8 h-8 flex items-center justify-center rounded-lg
                               text-slate-400 hover:text-red-500 hover:bg-red-50 transition-all">
                    <svg class="w-[16px] h-[16px]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                              d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                    </svg>
                </button>
            </form>
        </div>
    </header>

    {{-- PAGE CONTENT --}}
    <main class="flex-1 overflow-y-auto p-5">
        @yield('content')
    </main>

</div>

{{-- Toast container --}}
<div id="toast-container" style="position:fixed;top:20px;right:20px;z-index:9999;display:flex;flex-direction:column;gap:10px;"></div>

{{-- ════ SIDEBAR JS ════ --}}
<script>
function openSidebar() {
    var sidebar  = document.getElementById('sidebar');
    var backdrop = document.getElementById('sidebar-backdrop');
    if (sidebar)  sidebar.style.transform = 'translateX(0)';
    if (backdrop) {
        backdrop.style.display = 'block';
        requestAnimationFrame(function () {
            requestAnimationFrame(function () {
                backdrop.style.opacity = '1';
            });
        });
    }
}

function closeSidebar() {
    var sidebar  = document.getElementById('sidebar');
    var backdrop = document.getElementById('sidebar-backdrop');
    if (sidebar)  sidebar.style.transform = 'translateX(-100%)';
    if (backdrop) {
        backdrop.style.opacity = '0';
        setTimeout(function () { backdrop.style.display = 'none'; }, 300);
    }
}

document.addEventListener('keydown', function (e) {
    if (e.key === 'Escape') closeSidebar();
});
</script>

{{-- ════ REVERB / ECHO ════ --}}
<script src="https://js.pusher.com/8.4/pusher.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/laravel-echo@1.16.1/dist/echo.iife.js"></script>
<script>
window.Pusher = Pusher;
window.Echo = new Echo({
    broadcaster:       'reverb',
    key:               '{{ env("REVERB_APP_KEY") }}',
    wsHost:            '{{ env("REVERB_HOST", "localhost") }}',
    wsPort:            {{ env("REVERB_PORT", 8080) }},
    wssPort:           {{ env("REVERB_PORT", 8080) }},
    forceTLS:          false,
    enabledTransports: ['ws', 'wss'],
});

// ── Toast helper (used by all listeners below) ─────────────
function showToast(icon, title, body, url) {
    const toast = document.createElement('div');
    toast.style.cssText = `
        background:white;border-radius:14px;border:1px solid #e2e8f0;
        padding:14px 16px;min-width:280px;max-width:340px;
        box-shadow:0 8px 24px rgba(0,0,0,0.12);
        display:flex;gap:12px;align-items:flex-start;
        animation:slideIn .3s ease;cursor:pointer;
    `;
    toast.innerHTML = `
        <div style="font-size:22px;flex-shrink:0;">${icon}</div>
        <div style="flex:1;">
            <div style="font-size:12px;font-weight:800;color:#1e293b;margin-bottom:3px;">${title}</div>
            <div style="font-size:11px;color:#64748b;line-height:1.4;">${body}</div>
        </div>
        <button onclick="this.parentElement.remove()"
                style="background:none;border:none;cursor:pointer;color:#94a3b8;font-size:16px;padding:0;flex-shrink:0;">×</button>
    `;
    if (url) toast.addEventListener('click', (e) => {
        if (e.target.tagName !== 'BUTTON') window.location.href = url;
    });
    document.getElementById('toast-container').appendChild(toast);
    setTimeout(() => toast.remove(), 6000);
}

const _toastStyle = document.createElement('style');
_toastStyle.textContent = `@keyframes slideIn { from{transform:translateX(100%);opacity:0} to{transform:translateX(0);opacity:1} }`;
document.head.appendChild(_toastStyle);

// ══════════════════════════════════════════════════════════════
// REAL-TIME LISTENERS
// ══════════════════════════════════════════════════════════════
@auth
window.Echo.private('user.{{ Auth::id() }}')
 
    .listen('.ReclamationAssigned', (e) => {
        showToast('📋', 'Réclamation assignée !',
            `Réclamation #${e.reclamation_id} de ${e.stagiaire} vous a été assignée.`, e.url);
    })
 
    .listen('.ReclamationDeleted', (e) => {
        showToast('🗑️', 'Réclamation supprimée',
            `La réclamation #${e.reclamation_id} a été supprimée.`, null);
        const row = document.getElementById('rec-row-' + e.reclamation_id);
        if (row) {
            row.style.animation = 'fadeOut .4s ease forwards';
            setTimeout(() => row.remove(), 400);
        }
        if (window.location.href.includes('/reclamations/' + e.reclamation_id)) {
            setTimeout(() => window.location.href = '{{ route("reclamations.index") }}', 2000);
        }
    })
 
    // ── New notification (first message) ──────────────────────────
    .listen('.NotificationCreated', (e) => {
        const currentId  = window.__currentReclamationId ?? null;
        const notifRecId = e.data?.reclamation_id ?? null;
 
        // Already viewing this reclamation → silently mark as read
        if (currentId && notifRecId && currentId === notifRecId) {
            fetch(`/notifications/${e.id}/read`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept':       'application/json',
                },
            })
            .then(r => r.json())
            .then(data => {
                if (typeof setUnreadCount === 'function') setUnreadCount(data.unread_count ?? 0);
            })
            .catch(() => {});
            return;
        }
 
        if (typeof prependNotification === 'function') prependNotification(e);
    })
 
    // ── Existing notification incremented (2nd, 3rd… message) ────
    .listen('.NotificationUpdated', (e) => {
        const currentId  = window.__currentReclamationId ?? null;
        const notifRecId = e.data?.reclamation_id ?? null;
 
        // Already viewing this reclamation → silently mark as read
        if (currentId && notifRecId && currentId === notifRecId) {
            fetch(`/notifications/${e.id}/read`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept':       'application/json',
                },
            })
            .then(r => r.json())
            .then(data => {
                if (typeof setUnreadCount === 'function') setUnreadCount(data.unread_count ?? 0);
            })
            .catch(() => {});
            return;
        }
 
        // Patch the existing bell item in-place
        if (typeof patchNotification === 'function') patchNotification(e);
    });
 
 
@can('reclamation-manage')
window.Echo.channel('reclamations.admin')
    .listen('.ReclamationCreated', (e) => {
        showToast(e.type_icon, 'Nouvelle réclamation !', `${e.stagiaire} : ${e.description}`, e.url);
        const badge = document.getElementById('reclamations-count');
        if (badge) badge.textContent = parseInt(badge.textContent || 0) + 1;
    })
    .listen('.ReclamationDeleted', (e) => {
        showToast('🗑️', 'Réclamation supprimée', `Réclamation #${e.reclamation_id} supprimée.`, null);
        const row = document.getElementById('rec-row-' + e.reclamation_id);
        if (row) {
            row.style.animation = 'fadeOut .4s ease forwards';
            setTimeout(() => row.remove(), 400);
        }
    })

    // ── ADD THIS: real-time status update on index page ────
    .listen('.ReclamationStatusUpdated', (e) => {
        const row = document.getElementById('rec-row-' + e.reclamation_id);
        if (!row) return;

        const badge = row.querySelector('.status-badge');
        if (badge) {
            badge.textContent        = e.icon + ' ' + e.label;
            badge.style.background   = e.bg;
            badge.style.color        = e.color;
            badge.style.border       = '1px solid ' + e.border;
        }

        showToast(e.icon, 'Statut mis à jour',
            `Réclamation #${e.reclamation_id} → ${e.label}`, null);
    });
@endcan
@endauth
</script>

@stack('scripts')
</body>
</html>