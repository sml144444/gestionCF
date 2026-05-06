{{-- resources/views/reclamations/index.blade.php --}}
@extends('layouts.app')
@section('title', 'Réclamations')
@section('page-title', 'Gestion des réclamations')

@push('scripts')
<script src="https://js.pusher.com/8.4/pusher.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/laravel-echo@1.16.1/dist/echo.iife.js"></script>
<script>
window.Pusher = Pusher;
window.Echo = new Echo({
    broadcaster: 'reverb',
    key: '{{ env("REVERB_APP_KEY") }}',
    wsHost: '{{ env("REVERB_HOST", "localhost") }}',
    wsPort: {{ env("REVERB_PORT", 8080) }},
    wssPort: {{ env("REVERB_PORT", 8080) }},
    forceTLS: false,
    enabledTransports: ['ws', 'wss'],
});
</script>
@endpush

@section('content')
@php
    $user = Auth::user();
    $role = $user->role;
    $palettes = [
        'admin' => [
            'primary'   => '#0a6640',
            'medium'    => '#1a8c56',
            'light'     => '#ecfdf5',
            'lighter'   => '#f0fdf4',
            'text'      => '#064e3b',
            'border'    => '#a7f3d0',
            'shadow'    => 'rgba(10,102,64,0.15)',
            'gradient'  => 'linear-gradient(135deg,#065f46 0%,#0a6640 60%,#1a8c56 100%)',
            'ring'      => 'rgba(10,102,64,0.12)',
        ],
        'gestionnaire' => [
            'primary'   => '#1e293b',
            'medium'    => '#334155',
            'light'     => '#f1f5f9',
            'lighter'   => '#f8fafc',
            'text'      => '#0f172a',
            'border'    => '#cbd5e1',
            'shadow'    => 'rgba(30,41,59,0.15)',
            'gradient'  => 'linear-gradient(135deg,#0f172a 0%,#1e293b 60%,#334155 100%)',
            'ring'      => 'rgba(30,41,59,0.10)',
        ],
    ];
    $p = $palettes[$role] ?? $palettes['gestionnaire'];

    $statusConfig = \App\Models\Reclamation::STATUSES;
    $typeConfig   = \App\Models\Reclamation::TYPES;

    $roleLabels = [
        'admin'        => ['label' => 'Admin',        'bg' => '#d1fae5', 'color' => '#065f38'],
        'gestionnaire' => ['label' => 'Gestionnaire', 'bg' => '#f1f5f9', 'color' => '#1e293b'],
        'formateur'    => ['label' => 'Formateur',    'bg' => '#eff6ff', 'color' => '#1e40af'],
        'stagiaire'    => ['label' => 'Stagiaire',    'bg' => '#f5f3ff', 'color' => '#6d28d9'],
    ];
@endphp

<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@300;400;500;600;700;800&family=DM+Mono:wght@400;500&display=swap" rel="stylesheet">

<style>
:root {
    --primary:    {{ $p['primary'] }};
    --medium:     {{ $p['medium'] }};
    --light:      {{ $p['light'] }};
    --lighter:    {{ $p['lighter'] }};
    --text-col:   {{ $p['text'] }};
    --border-col: {{ $p['border'] }};
    --shadow:     {{ $p['shadow'] }};
    --gradient:   {{ $p['gradient'] }};
    --ring:       {{ $p['ring'] }};
}

*, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
body, .rc-page { font-family: 'DM Sans', system-ui, sans-serif; }

/* ── PAGE ──────────────────────────────────────────────── */
.rc-page {
    max-width: 1300px;
    margin: 0 auto;
    padding: 16px 18px 32px;
    display: flex;
    flex-direction: column;
    gap: 14px;
}

/* ── FLASH ─────────────────────────────────────────────── */
.rc-flash {
    display: flex; align-items: center; gap: 12px;
    padding: 13px 18px;
    background: var(--lighter);
    border: 1px solid var(--border-col);
    border-left: 4px solid var(--primary);
    border-radius: 14px;
    font-size: 13px; font-weight: 600; color: var(--text-col);
    animation: slideDown .3s ease;
}
@keyframes slideDown { from{transform:translateY(-8px);opacity:0} to{transform:translateY(0);opacity:1} }

/* ── HERO ──────────────────────────────────────────────── */
.rc-hero {
    background: var(--gradient);
    border-radius: 20px;
    padding: 20px 26px;
    display: flex; align-items: center; justify-content: space-between;
    gap: 14px; flex-wrap: wrap;
    position: relative; overflow: hidden;
    box-shadow: 0 6px 28px var(--shadow);
}
.rc-hero::before {
    content: '';
    position: absolute; top: -60px; right: -60px;
    width: 220px; height: 220px; border-radius: 50%;
    background: rgba(255,255,255,0.06); pointer-events: none;
}
.rc-hero::after {
    content: '';
    position: absolute; bottom: -40px; left: 35%;
    width: 130px; height: 130px; border-radius: 50%;
    background: rgba(255,255,255,0.04); pointer-events: none;
}
.rc-hero-icon {
    width: 52px; height: 52px; border-radius: 16px;
    background: rgba(255,255,255,0.15);
    border: 1px solid rgba(255,255,255,0.2);
    display: flex; align-items: center; justify-content: center;
    flex-shrink: 0;
}
.rc-hero-title { font-size: 20px; font-weight: 800; color: white; letter-spacing: -.3px; }
.rc-hero-sub   { font-size: 12px; color: rgba(255,255,255,.65); margin-top: 3px; font-weight: 500; }

/* Stats pills */
.stats-group { display: flex; gap: 8px; flex-wrap: wrap; align-items: center; }
.stat-pill {
    background: rgba(255,255,255,0.12);
    border: 1px solid rgba(255,255,255,0.18);
    border-radius: 14px; padding: 10px 16px; text-align: center;
    transition: transform .2s, background .2s; cursor: default;
    backdrop-filter: blur(4px);
}
.stat-pill:hover { background: rgba(255,255,255,0.2); transform: translateY(-1px); }
.stat-pill-val { font-size: 22px; font-weight: 900; color: white; line-height: 1; }
.stat-pill-lbl { font-size: 10px; color: rgba(255,255,255,.68); margin-top: 2px; font-weight: 600; letter-spacing: .3px; }

/* Live badge */
.rt-badge {
    display: inline-flex; align-items: center; gap: 6px;
    font-size: 10px; font-weight: 700;
    color: rgba(255,255,255,.9);
    background: rgba(255,255,255,0.12);
    border: 1px solid rgba(255,255,255,.2);
    border-radius: 20px; padding: 5px 11px;
    backdrop-filter: blur(4px);
}
.live-dot {
    width: 6px; height: 6px; border-radius: 50%;
    background: #4ade80;
    box-shadow: 0 0 0 0 rgba(74,222,128,.5);
    animation: livePulse 2s infinite;
}
@keyframes livePulse {
    0%   { box-shadow: 0 0 0 0   rgba(74,222,128,.5); }
    70%  { box-shadow: 0 0 0 7px rgba(74,222,128,0);  }
    100% { box-shadow: 0 0 0 0   rgba(74,222,128,0);  }
}

/* ── FILTER BAR ────────────────────────────────────────── */
.filter-bar {
    background: white;
    border-radius: 16px;
    border: 1px solid #e8edf3;
    padding: 12px 18px;
    display: flex; gap: 10px; flex-wrap: wrap; align-items: center;
    box-shadow: 0 2px 12px rgba(0,0,0,0.04);
}
.filter-bar-label {
    font-size: 10px; font-weight: 800; color: #94a3b8;
    text-transform: uppercase; letter-spacing: .7px;
    flex-shrink: 0; margin-right: 2px;
}
.f-select {
    border: 1.5px solid #e2e8f0; border-radius: 10px;
    padding: 8px 32px 8px 12px; font-size: 12px; font-weight: 500;
    font-family: 'DM Sans', system-ui, sans-serif;
    color: #1e293b; background: white; cursor: pointer; outline: none;
    appearance: none;
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='%2394a3b8'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M19 9l-7 7-7-7'/%3E%3C/svg%3E");
    background-repeat: no-repeat; background-position: right 10px center; background-size: 14px;
    transition: border-color .15s, box-shadow .15s;
}
.f-select:focus { border-color: var(--primary); box-shadow: 0 0 0 3px var(--ring); }
.btn-reset {
    font-size: 11px; font-weight: 700; padding: 8px 14px; border-radius: 10px;
    background: #fff1f2; color: #be123c; border: 1.5px solid #fecdd3;
    text-decoration: none; display: inline-flex; align-items: center; gap: 5px;
    transition: all .15s; cursor: pointer;
}
.btn-reset:hover { background: #ffe4e6; border-color: #fda4af; }
.filter-count-badge {
    font-size: 10px; font-weight: 700; padding: 3px 10px;
    border-radius: 20px; margin-left: auto;
    background: var(--light); color: var(--text-col);
    border: 1px solid var(--border-col);
}

/* ── TABLE CARD ────────────────────────────────────────── */
.rc-table-wrap {
    background: white;
    border-radius: 20px;
    border: 1px solid #e8edf3;
    overflow: hidden;
    box-shadow: 0 2px 20px rgba(0,0,0,0.06);
}
.rc-table { width: 100%; border-collapse: collapse; }
.rc-table th {
    padding: 12px 16px;
    background: #f8fafc;
    font-size: 9px; font-weight: 800;
    text-transform: uppercase; letter-spacing: .8px; color: #94a3b8;
    border-bottom: 1px solid #e8edf3;
    text-align: left; white-space: nowrap;
    font-family: 'DM Sans', system-ui, sans-serif;
}
.rc-table td {
    padding: 14px 16px;
    border-bottom: 1px solid #f1f5f9;
    font-size: 12.5px; color: #1e293b;
    vertical-align: middle;
    font-family: 'DM Sans', system-ui, sans-serif;
}
.rc-table tr:last-child td { border-bottom: none; }
.rc-table tbody tr { transition: background .12s; }
.rc-table tbody tr:hover td { background: #fafbfd; }

/* ── AVATAR ────────────────────────────────────────────── */
.avatar-sm {
    width: 34px; height: 34px; border-radius: 11px;
    background: #eff6ff; display: inline-flex;
    align-items: center; justify-content: center;
    font-size: 10px; font-weight: 800; color: #1e40af;
    flex-shrink: 0; letter-spacing: .5px;
}

/* ── BADGES ────────────────────────────────────────────── */
.badge {
    font-size: 10px; font-weight: 700; padding: 4px 10px;
    border-radius: 20px; white-space: nowrap;
    display: inline-flex; align-items: center; gap: 4px;
    letter-spacing: .2px;
}
.type-badge  { background: #eff6ff; color: #1e40af; border: 1px solid #bfdbfe; }
.role-badge  { font-size: 9px; font-weight: 700; padding: 2px 7px; border-radius: 6px; }

/* ── MSG COUNT ─────────────────────────────────────────── */
.msg-count {
    display: inline-flex; align-items: center; gap: 5px;
    font-size: 11px; font-weight: 700; color: #94a3b8;
    background: #f8fafc; border: 1px solid #e8edf3;
    border-radius: 8px; padding: 3px 10px;
}
.msg-count.has-new { color: #2563eb; background: #eff6ff; border-color: #bfdbfe; }

/* ── ROW ID ────────────────────────────────────────────── */
.row-id {
    font-size: 11px; font-weight: 700; color: #cbd5e1;
    font-family: 'DM Mono', monospace;
}

/* ── ACTION BUTTONS ────────────────────────────────────── */
.btn-open {
    font-size: 11px; font-weight: 700; padding: 7px 15px;
    border-radius: 10px; background: var(--gradient);
    color: white; text-decoration: none;
    display: inline-flex; align-items: center; gap: 5px;
    transition: opacity .15s, transform .1s;
    box-shadow: 0 2px 10px var(--shadow);
}
.btn-open:hover { opacity: .88; transform: translateY(-1px); }
.btn-open:active { transform: translateY(0); }

.btn-del {
    font-size: 11px; font-weight: 700; padding: 7px 13px;
    border-radius: 10px; background: #fff1f2; color: #be123c;
    border: 1.5px solid #fecdd3; cursor: pointer;
    display: inline-flex; align-items: center; gap: 4px;
    transition: all .15s; margin-left: 6px;
    font-family: 'DM Sans', system-ui, sans-serif;
}
.btn-del:hover { background: #ffe4e6; border-color: #fda4af; transform: translateY(-1px); }

/* ── EMPTY STATE ───────────────────────────────────────── */
.empty-state {
    text-align: center; padding: 72px 24px;
}
.empty-icon {
    width: 68px; height: 68px; border-radius: 20px;
    background: var(--light); margin: 0 auto 18px;
    display: flex; align-items: center; justify-content: center;
    font-size: 30px; border: 1px solid var(--border-col);
}
.empty-title { font-size: 15px; font-weight: 800; color: #1e293b; margin-bottom: 6px; }
.empty-sub   { font-size: 12px; color: #94a3b8; font-weight: 500; }

/* ── DATE / TIME ───────────────────────────────────────── */
.date-main { font-size: 12px; font-weight: 600; color: #475569; }
.date-time  { font-size: 10px; color: #94a3b8; margin-top: 2px; }

/* ── REAL-TIME ─────────────────────────────────────────── */
@keyframes rowSlideIn {
    from { opacity:0; transform:translateY(-12px); background:#eff6ff; }
    to   { opacity:1; transform:translateY(0);      background:transparent; }
}
.rt-new-row td { animation: rowSlideIn .45s ease forwards; }

@keyframes rowFadeOut { to{opacity:0;transform:scaleY(0);max-height:0;padding:0;} }
.rt-deleting td { animation: rowFadeOut .35s ease forwards; }

/* Toast */
.rt-toast {
    position: fixed; top: 22px; right: 22px; z-index: 9999;
    background: white; border: 1px solid #bfdbfe; border-radius: 18px;
    padding: 14px 18px; box-shadow: 0 10px 40px rgba(0,0,0,0.14);
    display: flex; align-items: center; gap: 13px; min-width: 290px;
    animation: toastIn .3s ease; font-family: 'DM Sans', system-ui, sans-serif;
}
@keyframes toastIn  { from{opacity:0;transform:translateX(32px)} to{opacity:1;transform:translateX(0)} }
.rt-toast-hide { animation: toastOut .3s ease forwards; }
@keyframes toastOut { to{opacity:0;transform:translateX(32px)} }
.rt-toast-icon {
    width: 38px; height: 38px; border-radius: 12px;
    display: flex; align-items: center; justify-content: center;
    font-size: 18px; flex-shrink: 0;
}
.rt-toast-title { font-size: 12px; font-weight: 800; color: #1e293b; }
.rt-toast-body  { font-size: 11px; color: #64748b; margin-top: 2px; }
.rt-toast-close {
    margin-left: auto; background: none; border: none; cursor: pointer;
    color: #94a3b8; font-size: 18px; padding: 0; line-height: 1;
    transition: color .12s;
}
.rt-toast-close:hover { color: #475569; }

/* Stat bump */
@keyframes statBump { 0%,100%{transform:scale(1)} 50%{transform:scale(1.18)} }
.stat-bump { animation: statBump .4s ease; }

/* ── DELETE MODAL ──────────────────────────────────────── */
.rc-overlay {
    position: fixed; inset: 0; z-index: 9998;
    background: rgba(15,23,42,0.5);
    backdrop-filter: blur(6px);
    display: flex; align-items: center; justify-content: center;
    animation: fadeO .2s ease;
}
@keyframes fadeO { from{opacity:0} to{opacity:1} }
.rc-modal {
    background: white; border-radius: 22px;
    padding: 30px 28px 24px;
    box-shadow: 0 24px 60px rgba(0,0,0,0.20);
    max-width: 400px; width: 90%;
    animation: popM .22s ease;
    font-family: 'DM Sans', system-ui, sans-serif;
}
@keyframes popM { from{transform:scale(.92);opacity:0} to{transform:scale(1);opacity:1} }
.rc-modal-icon  { font-size: 40px; margin-bottom: 14px; text-align: center; }
.rc-modal-title { font-size: 17px; font-weight: 800; color: #1e293b; margin-bottom: 8px; text-align: center; }
.rc-modal-body  { font-size: 13px; color: #64748b; line-height: 1.6; margin-bottom: 24px; text-align: center; }
.rc-modal-btns  { display: flex; gap: 10px; }
.rc-btn { font-size: 13px; font-weight: 700; padding: 12px 0; border-radius: 12px; border: none; cursor: pointer; flex: 1; transition: opacity .15s; font-family: 'DM Sans', system-ui, sans-serif; }
.rc-btn:hover { opacity: .86; }
.rc-btn-cancel { background: #f1f5f9; color: #64748b; border: 1.5px solid #e2e8f0; }
.rc-btn-danger { background: linear-gradient(135deg,#be123c,#e11d48); color: white; }

/* ── RESPONSIVE ────────────────────────────────────────── */
@media (max-width: 1024px) {
    .rc-page { padding: 12px 14px 24px; }
    .rc-table th:nth-child(4),
    .rc-table td:nth-child(4) { display: none; } /* hide description on medium */
}
@media (max-width: 768px) {
    .rc-hero { padding: 16px 18px; border-radius: 16px; }
    .rc-hero-title { font-size: 17px; }
    .stat-pill-val { font-size: 18px; }
    .stat-pill { padding: 8px 12px; }

    /* Stack table into card-rows on mobile */
    .rc-table-wrap { border-radius: 16px; }
    .rc-table, .rc-table thead, .rc-table tbody,
    .rc-table th, .rc-table td, .rc-table tr { display: block; }
    .rc-table thead { display: none; }
    .rc-table tbody tr {
        border: none;
        border-bottom: 1px solid #f1f5f9;
        padding: 14px 16px;
        position: relative;
    }
    .rc-table tbody tr:last-child { border-bottom: none; }
    .rc-table tbody tr:hover { background: #fafbfd; }
    .rc-table td {
        border: none; padding: 3px 0;
        display: flex; align-items: center; gap: 8px;
    }
    .rc-table td::before {
        content: attr(data-label);
        font-size: 9px; font-weight: 800; color: #94a3b8;
        text-transform: uppercase; letter-spacing: .6px;
        min-width: 76px; flex-shrink: 0;
    }
    .rc-table td:first-child { position: absolute; top: 14px; right: 16px; }
    .rc-table td:first-child::before { display: none; }
    .rc-table td:last-child { margin-top: 10px; padding-top: 10px; border-top: 1px solid #f1f5f9; }
    .rc-table td:last-child::before { display: none; }

    .filter-bar { padding: 10px 14px; gap: 8px; border-radius: 14px; }
    .f-select { font-size: 13px; }
    .filter-count-badge { display: none; }

    .rt-toast { min-width: 240px; right: 12px; top: 12px; border-radius: 14px; }
}
@media (max-width: 480px) {
    .rc-page { padding: 10px 10px 20px; gap: 10px; }
    .rc-hero { gap: 12px; }
    .stats-group { gap: 6px; }
    .stat-pill { padding: 7px 10px; }
    .stat-pill-val { font-size: 16px; }
    .rc-hero-icon { display: none; }
}
</style>

<div class="rc-page">

    {{-- Flash --}}
    @if(session('success'))
    <div class="rc-flash">
        <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
        </svg>
        {{ session('success') }}
    </div>
    @endif

    {{-- Hero --}}
    <div class="rc-hero">
        <div style="display:flex;align-items:center;gap:16px;">
            <div class="rc-hero-icon">
                <svg width="26" height="26" fill="none" stroke="white" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/>
                </svg>
            </div>
            <div>
                <div class="rc-hero-title">Réclamations</div>
                <div class="rc-hero-sub">Gestion de toutes les réclamations des stagiaires</div>
            </div>
        </div>
        <div class="stats-group">
            <span class="rt-badge"><span class="live-dot"></span>Temps réel</span>
            @foreach(['total'=>'Total','en_attente'=>'En attente','en_cours'=>'En cours','traite'=>'Traités'] as $k => $l)
            <div class="stat-pill" id="stat-{{ $k }}">
                <div class="stat-pill-val" id="stat-val-{{ $k }}">{{ $stats[$k] }}</div>
                <div class="stat-pill-lbl">{{ $l }}</div>
            </div>
            @endforeach
        </div>
    </div>

    {{-- Filter bar --}}
    <div class="filter-bar">
        <span class="filter-bar-label">Filtrer</span>
        <form method="GET" action="{{ route('reclamations.index') }}"
              style="display:flex;gap:10px;flex-wrap:wrap;align-items:center;flex:1;">
            <select name="status" class="f-select" onchange="this.form.submit()">
                <option value="">Tous les statuts</option>
                @foreach($statusConfig as $k => $cfg)
                    <option value="{{ $k }}" {{ request('status') === $k ? 'selected' : '' }}>
                        {{ $cfg['icon'] }} {{ $cfg['label'] }}
                    </option>
                @endforeach
            </select>
            <select name="type" class="f-select" onchange="this.form.submit()">
                <option value="">Tous les types</option>
                @foreach($typeConfig as $k => $cfg)
                    <option value="{{ $k }}" {{ request('type') === $k ? 'selected' : '' }}>
                        {{ $cfg['icon'] }} {{ $cfg['label'] }}
                    </option>
                @endforeach
            </select>
            @if(request()->hasAny(['status','type']))
                <a href="{{ route('reclamations.index') }}" class="btn-reset">✕ Réinitialiser</a>
            @endif
            @if(!$reclamations->isEmpty())
                <span class="filter-count-badge">{{ $reclamations->total() }} résultat(s)</span>
            @endif
        </form>
    </div>

    {{-- Table --}}
    <div class="rc-table-wrap">
        @if($reclamations->isEmpty())
            <div class="empty-state" id="empty-state">
                <div class="empty-icon">💬</div>
                <p class="empty-title">Aucune réclamation</p>
                <p class="empty-sub">Aucune réclamation ne correspond à ces filtres.</p>
            </div>
            {{-- Hidden table shown when first RT row arrives --}}
            <div id="table-container" style="display:none;overflow-x:auto;">
                <table class="rc-table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Stagiaire</th>
                            <th>Type</th>
                            <th>Description</th>
                            <th>Msgs</th>
                            <th>Assigné à</th>
                            <th>Statut</th>
                            <th>Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody id="reclamations-tbody"></tbody>
                </table>
            </div>
        @else
            <div style="overflow-x:auto;" id="table-container">
                <table class="rc-table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Stagiaire</th>
                            <th>Type</th>
                            <th>Description</th>
                            <th>Msgs</th>
                            <th>Assigné à</th>
                            <th>Statut</th>
                            <th>Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody id="reclamations-tbody">
                    @foreach($reclamations as $rec)
                        @php
                            $sc     = $statusConfig[$rec->status] ?? $statusConfig['en_attente'];
                            $tc     = $typeConfig[$rec->type]     ?? $typeConfig['autre'];
                            $sName  = $rec->stagiaire?->name ?? '—';
                            $parts  = explode(' ', $sName);
                            $initials = strtoupper(mb_substr($parts[0],0,1) . mb_substr($parts[1] ?? '',0,1));
                        @endphp
                        <tr id="rec-row-{{ $rec->id }}">
                            <td data-label="#">
                                <span class="row-id">#{{ $rec->id }}</span>
                            </td>
                            <td data-label="Stagiaire">
                                <div style="display:flex;align-items:center;gap:10px;">
                                    <div class="avatar-sm">{{ $initials }}</div>
                                    <div>
                                        <div style="font-weight:700;font-size:13px;color:#1e293b;">{{ $sName }}</div>
                                        <div style="font-size:10px;color:#94a3b8;margin-top:1px;">{{ $rec->stagiaire?->email ?? '' }}</div>
                                    </div>
                                </div>
                            </td>
                            <td data-label="Type">
                                <span class="badge type-badge">{{ $tc['icon'] }} {{ $tc['label'] }}</span>
                            </td>
                            <td data-label="Description" style="max-width:240px;">
                                <p style="margin:0;font-size:12px;color:#64748b;
                                          overflow:hidden;display:-webkit-box;
                                          -webkit-line-clamp:2;-webkit-box-orient:vertical;
                                          word-break:break-word;line-height:1.5;">
                                    {{ $rec->description }}
                                </p>
                            </td>
                            <td data-label="Messages">
                                <span class="msg-count {{ $rec->messages_count > 0 ? 'has-new' : '' }}">
                                    💬 {{ $rec->messages_count }}
                                </span>
                            </td>
                            <td data-label="Assigné à">
                                @if($rec->assignee)
                                    @php $ar = $roleLabels[$rec->assignee->role] ?? $roleLabels['formateur']; @endphp
                                    <div style="font-size:12px;font-weight:700;color:#1e293b;">{{ $rec->assignee->name }}</div>
                                    <span class="role-badge" style="background:{{ $ar['bg'] }};color:{{ $ar['color'] }};margin-top:3px;display:inline-block;">
                                        {{ $ar['label'] }}
                                    </span>
                                @else
                                    <span style="font-size:11px;color:#cbd5e1;font-weight:500;">—</span>
                                @endif
                            </td>
                            <td data-label="Statut">
                                <span class="badge status-badge"
                                      style="background:{{ $sc['bg'] }};color:{{ $sc['color'] }};border:1px solid {{ $sc['border'] }};">
                                    {{ $sc['icon'] }} {{ $sc['label'] }}
                                </span>
                            </td>
                            <td data-label="Date">
                                <div class="date-main">{{ $rec->created_at->format('d/m/Y') }}</div>
                                <div class="date-time">{{ $rec->created_at->format('H:i') }}</div>
                            </td>
                            <td data-label="Actions">
                                <div style="display:flex;align-items:center;flex-wrap:wrap;gap:4px;">
                                    <a href="{{ route('reclamations.show', $rec) }}" class="btn-open">
                                        <svg width="13" height="13" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                                  d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                                        </svg>
                                        Ouvrir
                                    </a>
                                    @can('reclamation-manage')
                                    <button onclick="confirmDelete({{ $rec->id }})" class="btn-del">
                                        🗑️ Sup.
                                    </button>
                                    <form id="delete-form-{{ $rec->id }}"
                                          action="{{ route('reclamations.destroy', $rec) }}"
                                          method="POST" style="display:none;">
                                        @csrf
                                        @method('DELETE')
                                    </form>
                                    @endcan
                                </div>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>

            @if($reclamations->hasPages())
            <div style="padding:14px 20px;border-top:1px solid #f1f5f9;">
                {{ $reclamations->links() }}
            </div>
            @endif
        @endif
    </div>

</div>{{-- /rc-page --}}

{{-- ── DELETE MODAL ── --}}
<div id="delete-modal" class="rc-overlay" style="display:none;">
    <div onclick="cancelDelete()" style="position:absolute;inset:0;"></div>
    <div class="rc-modal">
        <div class="rc-modal-icon">🗑️</div>
        <div class="rc-modal-title">Supprimer la réclamation ?</div>
        <div class="rc-modal-body">
            Cette action est <strong>irréversible</strong>.<br>
            Tous les messages liés seront supprimés.
        </div>
        <div class="rc-modal-btns">
            <button onclick="cancelDelete()" class="rc-btn rc-btn-cancel">✕ Annuler</button>
            <button onclick="submitDelete()" class="rc-btn rc-btn-danger">🗑️ Oui, supprimer</button>
        </div>
    </div>
</div>

<script>
// ── Delete modal ─────────────────────────────────────────────
let _deleteId = null;
function confirmDelete(id) {
    _deleteId = id;
    const modal = document.getElementById('delete-modal');
    modal.style.display = 'flex';
}
function cancelDelete() {
    _deleteId = null;
    document.getElementById('delete-modal').style.display = 'none';
}
function submitDelete() {
    if (_deleteId) document.getElementById('delete-form-' + _deleteId).submit();
}
document.addEventListener('keydown', e => { if (e.key === 'Escape') cancelDelete(); });

// ── Helpers ──────────────────────────────────────────────────
function escHtml(s) {
    return String(s || '').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;');
}

// Toast
let _toastTimer = null;
function showToast(icon, title, body, bg) {
    const old = document.getElementById('rt-toast');
    if (old) old.remove();
    if (_toastTimer) clearTimeout(_toastTimer);

    const t = document.createElement('div');
    t.id = 'rt-toast';
    t.className = 'rt-toast';
    t.innerHTML = `
        <div class="rt-toast-icon" style="background:${bg||'#eff6ff'};">${icon}</div>
        <div>
            <div class="rt-toast-title">${escHtml(title)}</div>
            <div class="rt-toast-body">${escHtml(body)}</div>
        </div>
        <button class="rt-toast-close" onclick="this.parentElement.remove()">✕</button>`;
    document.body.appendChild(t);
    _toastTimer = setTimeout(() => {
        t.classList.add('rt-toast-hide');
        setTimeout(() => t.remove(), 300);
    }, 5000);
}

// Bump stat
function bumpStat(key, delta) {
    const el = document.getElementById('stat-val-' + key);
    if (!el) return;
    el.textContent = Math.max(0, parseInt(el.textContent || '0') + delta);
    const pill = document.getElementById('stat-' + key);
    if (pill) {
        pill.classList.remove('stat-bump');
        void pill.offsetWidth;
        pill.classList.add('stat-bump');
    }
}

// Prepend new row
function prependRow(e) {
    const tbody = document.getElementById('reclamations-tbody');
    if (!tbody) return;

    // Show table, hide empty state
    const empty = document.getElementById('empty-state');
    const table = document.getElementById('table-container');
    if (empty) empty.style.display = 'none';
    if (table) table.style.display = 'block';

    const initials = (e.stagiaire || '?')
        .split(' ').slice(0,2).map(w => w.charAt(0).toUpperCase()).join('');

    const now     = new Date();
    const dateStr = now.toLocaleDateString('fr-FR');
    const timeStr = now.toLocaleTimeString('fr-FR', { hour:'2-digit', minute:'2-digit' });

    const tr = document.createElement('tr');
    tr.id = 'rec-row-' + e.id;
    tr.className = 'rt-new-row';
    tr.innerHTML = `
        <td data-label="#"><span class="row-id">#${escHtml(e.id)}</span></td>
        <td data-label="Stagiaire">
            <div style="display:flex;align-items:center;gap:10px;">
                <div class="avatar-sm">${escHtml(initials)}</div>
                <div>
                    <div style="font-weight:700;font-size:13px;color:#1e293b;">${escHtml(e.stagiaire || '—')}</div>
                </div>
            </div>
        </td>
        <td data-label="Type">
            <span class="badge type-badge">${escHtml(e.type_icon)} ${escHtml(e.type_label)}</span>
        </td>
        <td data-label="Description" style="max-width:240px;">
            <p style="margin:0;font-size:12px;color:#64748b;overflow:hidden;
                      display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;">
                ${escHtml(e.description)}
            </p>
        </td>
        <td data-label="Messages"><span class="msg-count">💬 0</span></td>
        <td data-label="Assigné à"><span style="font-size:11px;color:#cbd5e1;font-weight:500;">—</span></td>
        <td data-label="Statut">
            <span class="badge status-badge"
                  style="background:#fef9c3;color:#854d0e;border:1px solid #fde68a;">
                ⏳ En attente
            </span>
        </td>
        <td data-label="Date">
            <div class="date-main">${dateStr}</div>
            <div class="date-time">${timeStr}</div>
        </td>
        <td data-label="Actions">
            <div style="display:flex;align-items:center;gap:4px;">
                <a href="${escHtml(e.url)}" class="btn-open">
                    <svg width="13" height="13" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                              d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                    </svg>
                    Ouvrir
                </a>
                @can('reclamation-manage')
                <button onclick="confirmDelete(${e.id})" class="btn-del">🗑️ Sup.</button>
                <form id="delete-form-${e.id}" action="/reclamations/${e.id}" method="POST" style="display:none;">
                    @csrf
                    @method('DELETE')
                </form>
                @endcan
            </div>
        </td>`;

    tbody.insertBefore(tr, tbody.firstChild);
}

// Remove row
function removeRow(id) {
    const row = document.getElementById('rec-row-' + id);
    if (!row) return;
    row.classList.add('rt-deleting');
    setTimeout(() => row.remove(), 380);
}

// ── Echo listeners ────────────────────────────────────────────
document.addEventListener('DOMContentLoaded', function () {
    if (typeof window.Echo === 'undefined') {
        console.error('❌ Echo not loaded!');
        return;
    }

    window.Echo.channel('reclamations.admin')

        .listen('.ReclamationCreated', (e) => {
            console.log('🆕 ReclamationCreated:', e);
            prependRow(e);
            bumpStat('total', +1);
            bumpStat('en_attente', +1);
            showToast('📝', 'Nouvelle réclamation #' + e.id,
                (e.stagiaire || 'Stagiaire') + ' · ' + e.type_label, '#eff6ff');
        })

        .listen('.ReclamationDeleted', (e) => {
            console.log('🗑️ ReclamationDeleted:', e);
            removeRow(e.reclamation_id);
            bumpStat('total', -1);
        })

        .listen('.ReclamationStatusUpdated', (e) => {
            console.log('🔄 ReclamationStatusUpdated:', e);
            const row = document.getElementById('rec-row-' + e.reclamation_id);
            if (!row) return;
            const badge = row.querySelector('.status-badge');
            if (badge) {
                badge.textContent      = e.icon + ' ' + e.label;
                badge.style.background = e.bg;
                badge.style.color      = e.color;
                badge.style.border     = '1px solid ' + e.border;
            }
            showToast(e.icon, 'Statut mis à jour',
                'Réclamation #' + e.reclamation_id + ' → ' + e.label, e.bg);
        });

    console.log('✅ Real-time listeners active on reclamations.admin');
});
</script>

@endsection