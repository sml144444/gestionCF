@extends('layouts.app')
@section('title', 'Mes absences')
@section('page-title', 'Absences')

@section('content')
@php
    $user     = Auth::user();
    $userRole = $user->role;

    $palettes = [
        'admin'        => ['primary'=>'#0a6640','medium'=>'#1a8c56','light'=>'#e8f5ee','lighter'=>'#f0fdf4','text'=>'#065f38','border'=>'#bbf7d0','shadow'=>'rgba(10,102,64,0.15)','gradient'=>'linear-gradient(135deg,#0a6640 0%,#1a8c56 100%)'],
        'gestionnaire' => ['primary'=>'#1e293b','medium'=>'#334155','light'=>'#f1f5f9','lighter'=>'#f8fafc','text'=>'#1e293b','border'=>'#cbd5e1','shadow'=>'rgba(30,41,59,0.15)','gradient'=>'linear-gradient(135deg,#1e293b 0%,#334155 100%)'],
        'formateur'    => ['primary'=>'#1a4f8a','medium'=>'#2563eb','light'=>'#eff6ff','lighter'=>'#f0f7ff','text'=>'#1e40af','border'=>'#bfdbfe','shadow'=>'rgba(26,79,138,0.15)','gradient'=>'linear-gradient(135deg,#1a4f8a 0%,#2563eb 100%)'],
        'stagiaire'    => ['primary'=>'#1a4f8a','medium'=>'#2563eb','light'=>'#eff6ff','lighter'=>'#f0f7ff','text'=>'#1e40af','border'=>'#bfdbfe','shadow'=>'rgba(26,79,138,0.15)','gradient'=>'linear-gradient(135deg,#1a4f8a 0%,#2563eb 100%)'],
    ];
    $p = $palettes[$userRole] ?? $palettes['gestionnaire'];

    $heroTitle = $canViewAll
        ? ($filterStagiaire ? 'Absences de '.$filterStagiaire->name : 'Toutes les absences')
        : 'Mes absences';

    $canJustify = $user->can('absence-justify');

    $partConfig = [
        's1' => ['color'=>'#6d28d9','bg'=>'#f5f3ff','border'=>'#ddd6fe','label'=>'S1'],
        's2' => ['color'=>'#1d4ed8','bg'=>'#eff6ff','border'=>'#bfdbfe','label'=>'S2'],
        's3' => ['color'=>'#0369a1','bg'=>'#f0f9ff','border'=>'#bae6fd','label'=>'S3'],
        's4' => ['color'=>'#0f766e','bg'=>'#f0fdfa','border'=>'#99f6e4','label'=>'S4'],
    ];

    $todayStr = \Carbon\Carbon::today()->toDateString();
@endphp

<style>
:root {
    --accent:    {{ $p['primary'] }};
    --accent-md: {{ $p['medium'] }};
    --accent-lt: {{ $p['light'] }};
    --accent-ltr:{{ $p['lighter'] }};
    --accent-tx: {{ $p['text'] }};
    --accent-bd: {{ $p['border'] }};
    --accent-sh: {{ $p['shadow'] }};
    --accent-gr: {{ $p['gradient'] }};
}
* { box-sizing:border-box; }
.abs-wrap { font-family:'Segoe UI',system-ui,sans-serif; max-width:1280px; margin:0 auto; }

/* ─── HERO ─── */
.abs-hero        { background:var(--accent-gr); border-radius:20px; padding:28px 32px; margin-bottom:24px;
                   display:flex; align-items:center; justify-content:space-between; gap:16px; flex-wrap:wrap;
                   position:relative; overflow:hidden; }
.abs-hero::after { content:''; position:absolute; right:-40px; top:-40px; width:200px; height:200px;
                   border-radius:50%; background:rgba(255,255,255,0.06); pointer-events:none; }
.abs-hero-icon   { width:52px; height:52px; border-radius:16px; background:rgba(255,255,255,0.15);
                   display:flex; align-items:center; justify-content:center; flex-shrink:0; }
.abs-hero-title  { font-size:20px; font-weight:800; color:white; margin:0; }
.abs-hero-sub    { font-size:12px; color:rgba(255,255,255,0.75); margin-top:3px; }

/* ─── STATS ─── */
.stats-grid      { display:grid; grid-template-columns:repeat(auto-fill,minmax(140px,1fr)); gap:12px; margin-bottom:20px; }
.stat-card       { background:white; border-radius:16px; border:1px solid #e2e8f0; padding:14px 16px;
                   display:flex; align-items:center; gap:11px; transition:all .2s; }
.stat-card:hover { transform:translateY(-2px); box-shadow:0 8px 20px rgba(0,0,0,0.08); }
.stat-icon       { width:38px; height:38px; border-radius:12px; display:flex; align-items:center;
                   justify-content:center; flex-shrink:0; font-size:17px; }
.stat-val        { font-size:22px; font-weight:900; line-height:1; }
.stat-lbl        { font-size:9px; font-weight:700; color:#94a3b8; margin-top:2px;
                   text-transform:uppercase; letter-spacing:.6px; }

/* ─── ADMIN DAY PANEL ─── */
.day-panel        { background:white; border-radius:18px; border:1px solid #e2e8f0;
                    margin-bottom:20px; overflow:hidden; }
.day-panel-head   { display:flex; align-items:center; justify-content:space-between; gap:12px;
                    padding:14px 20px; border-bottom:1px solid #f1f5f9; flex-wrap:wrap; }
.day-nav          { display:flex; align-items:center; gap:8px; flex-wrap:wrap; }
.day-nav-btn      { display:inline-flex; align-items:center; gap:4px; padding:6px 14px;
                    border-radius:10px; border:1.5px solid #e2e8f0; background:white;
                    font-size:11px; font-weight:700; color:#475569; text-decoration:none;
                    transition:all .15s; cursor:pointer; }
.day-nav-btn:hover { border-color:#cbd5e1; background:#f8fafc; }
.day-nav-btn.today-btn { background:var(--accent-gr); color:white; border-color:transparent; }
.day-nav-btn.today-btn:hover { opacity:.88; }
.day-nav-btn.disabled { opacity:.35; pointer-events:none; }
.day-date-display { font-size:15px; font-weight:800; color:#1e293b; }
.day-date-sub     { font-size:10px; color:#94a3b8; margin-top:1px; }

.day-date-input   { font-size:11px; font-weight:600; padding:6px 10px; border:1.5px solid #e2e8f0;
                    border-radius:10px; background:#f8fafc; color:#1e293b; outline:none;
                    cursor:pointer; transition:border-color .15s; }
.day-date-input:focus { border-color:var(--accent); }

.day-absent-count { display:inline-flex; align-items:center; gap:6px; padding:5px 13px;
                    border-radius:99px; background:#fee2e2; color:#dc2626;
                    font-size:12px; font-weight:800; border:1px solid #fca5a5; }

/* Day navigator pills */
.day-pills        { display:flex; gap:6px; overflow-x:auto; padding:10px 20px;
                    border-bottom:1px solid #f1f5f9; scrollbar-width:none; }
.day-pills::-webkit-scrollbar { display:none; }
.day-pill         { flex-shrink:0; padding:5px 12px; border-radius:99px; border:1.5px solid #e2e8f0;
                    background:white; font-size:10px; font-weight:700; color:#64748b;
                    text-decoration:none; transition:all .15s; white-space:nowrap; }
.day-pill:hover   { border-color:var(--accent-bd); color:var(--accent-tx); }
.day-pill.active  { background:var(--accent-gr); color:white; border-color:transparent; }
.day-pill.today   { border-color:var(--accent); color:var(--accent-tx); }

/* Day absent table */
table.day-table   { width:100%; border-collapse:collapse; }
table.day-table thead th { padding:9px 14px; font-size:9px; font-weight:800; color:#94a3b8;
                            text-transform:uppercase; letter-spacing:.8px; background:#f8fafc;
                            border-bottom:1px solid #e2e8f0; text-align:left; white-space:nowrap; }
table.day-table tbody tr { border-bottom:1px solid #f1f5f9; transition:background .12s; }
table.day-table tbody tr:last-child { border-bottom:none; }
table.day-table tbody tr:hover { background:#fafbfd; }
table.day-table tbody td { padding:11px 14px; font-size:12px; color:#374151; vertical-align:middle; }
table.day-table tbody tr.row-pending { background:rgba(251,191,36,0.05); }

/* ─── FILTER BAR ─── */
.filter-bar      { background:white; border-radius:16px; border:1px solid #e2e8f0;
                   padding:14px 18px; margin-bottom:18px; display:flex; flex-wrap:wrap;
                   gap:10px; align-items:flex-end; }
.filter-group    { display:flex; flex-direction:column; gap:4px; min-width:130px; }
.filter-label    { font-size:9px; font-weight:800; color:#94a3b8; text-transform:uppercase; letter-spacing:.8px; }
.filter-select   { font-size:12px; font-weight:500; padding:7px 10px; border:1.5px solid #e2e8f0;
                   border-radius:10px; background:white; color:#1e293b; outline:none; cursor:pointer;
                   transition:border-color .15s; }
.filter-select:focus { border-color:var(--accent); }
.btn-filter  { font-size:12px; font-weight:700; padding:8px 18px; border-radius:10px;
               background:var(--accent-gr); color:white; border:none; cursor:pointer;
               transition:opacity .15s; white-space:nowrap; }
.btn-filter:hover { opacity:.88; }
.btn-reset   { font-size:11px; font-weight:600; padding:8px 13px; border-radius:10px;
               background:white; color:#64748b; border:1.5px solid #e2e8f0;
               cursor:pointer; text-decoration:none; display:inline-flex; align-items:center; gap:5px; }
.btn-reset:hover { border-color:#cbd5e1; background:#f8fafc; }

/* ─── TABLE ─── */
.abs-table-wrap { background:white; border-radius:18px; border:1px solid #e2e8f0; overflow:hidden; }
.abs-table-head { padding:14px 20px; border-bottom:1px solid #f1f5f9; display:flex;
                  align-items:center; justify-content:space-between; flex-wrap:wrap; gap:10px; }
table.abs-table { width:100%; border-collapse:collapse; }
table.abs-table thead th { padding:10px 14px; font-size:9px; font-weight:800; color:#94a3b8;
                            text-transform:uppercase; letter-spacing:.8px; background:#f8fafc;
                            border-bottom:1px solid #e2e8f0; text-align:left; white-space:nowrap; }
table.abs-table tbody tr { border-bottom:1px solid #f1f5f9; transition:background .12s; }
table.abs-table tbody tr:last-child { border-bottom:none; }
table.abs-table tbody tr:hover { background:#fafbfd; }
table.abs-table tbody td { padding:12px 14px; font-size:12px; color:#374151; vertical-align:middle; }

.row-pending { background:rgba(251,191,36,0.04); }

/* ─── DAY-VIEW: hours pill ─── */
.hours-pill {
    display:inline-flex; align-items:center; justify-content:center;
    min-width:52px; padding:5px 12px;
    border-radius:99px;
    background:#fee2e2; color:#dc2626;
    font-size:16px; font-weight:900; line-height:1;
    border:1.5px solid #fca5a5;
}

/* ─── DAY-VIEW: date block ─── */
.date-block-day  { font-size:22px; font-weight:900; color:#1e293b; line-height:1; }
.date-block-rest { font-size:10px; color:#94a3b8; margin-top:2px; }

/* ─── BADGES ─── */
.badge            { display:inline-flex; align-items:center; gap:4px; padding:3px 9px; border-radius:8px; font-size:10px; font-weight:800; }
.badge-justifie   { background:#d1fae5; color:#059669; border:1px solid #a7f3d0; }
.badge-injustifie { background:#fce7f3; color:#be185d; border:1px solid #fbcfe8; }
.badge-pending    { background:#fef3c7; color:#92400e; border:1px solid #fde68a; }

/* Admin validation badges and buttons */
.btn-admin-allow {
    font-size:10px; font-weight:700; padding:4px 10px; border-radius:8px;
    background:#fef9c3; color:#713f12;
    border:1px solid #fde047;
    cursor:pointer;
    display:inline-flex; align-items:center; gap:4px;
    transition:all .15s;
    white-space:nowrap;
}
.btn-admin-allow:hover { background:#fef08a; }

.btn-admin-revert {
    font-size:10px; font-weight:700; padding:4px 10px; border-radius:8px;
    background:#f1f5f9; color:#64748b;
    border:1px solid #cbd5e1;
    cursor:pointer;
    display:inline-flex; align-items:center; gap:4px;
    transition:all .15s;
    white-space:nowrap;
}
.btn-admin-revert:hover { background:#e2e8f0; }

.badge-admin-allowed {
    display:inline-flex; align-items:center; gap:4px;
    padding:3px 9px; border-radius:8px; font-size:10px; font-weight:800;
    background:#fef9c3; color:#713f12;
    border:1px solid #fde047;
}

.avatar { width:30px; height:30px; border-radius:9px; background:var(--accent-lt);
          display:inline-flex; align-items:center; justify-content:center;
          font-size:10px; font-weight:800; color:var(--accent-tx); flex-shrink:0; }

/* ─── ACTION BUTTONS ─── */
.btn-accept { font-size:10px; font-weight:700; padding:4px 10px; border-radius:8px;
              background:#d1fae5; color:#059669; border:1px solid #6ee7b7;
              cursor:pointer; display:inline-flex; align-items:center; gap:3px; transition:all .15s; }
.btn-accept:hover { background:#a7f3d0; }
.btn-reject { font-size:10px; font-weight:700; padding:4px 10px; border-radius:8px;
              background:#fee2e2; color:#dc2626; border:1px solid #fca5a5;
              cursor:pointer; display:inline-flex; align-items:center; gap:3px; transition:all .15s; }
.btn-reject:hover { background:#fecaca; }
.btn-toggle { font-size:10px; font-weight:700; padding:4px 10px; border-radius:8px;
              background:#eff6ff; color:#1d4ed8; border:1px solid #bfdbfe;
              cursor:pointer; display:inline-flex; align-items:center; gap:3px; transition:all .15s; }
.btn-toggle:hover { background:#dbeafe; }
.btn-upload-label { display:inline-flex; align-items:center; gap:4px; cursor:pointer;
                    font-size:10px; font-weight:600; color:var(--accent);
                    padding:4px 9px; border-radius:8px;
                    border:1.5px dashed var(--accent-bd);
                    background:var(--accent-ltr); white-space:nowrap; transition:all .15s; }
.btn-upload-label:hover { background:var(--accent-lt); }

/* ─── FLASH ─── */
.flash-ok  { display:flex; align-items:center; gap:12px; padding:14px 18px; border-radius:14px;
             margin-bottom:18px; background:var(--accent-ltr); border:1px solid var(--accent-bd);
             animation:fadeIn .3s ease; }
.flash-ok-icon { width:36px; height:36px; border-radius:50%; background:var(--accent-gr);
                 display:flex; align-items:center; justify-content:center; flex-shrink:0; }
@keyframes fadeIn { from{opacity:0;transform:translateY(-6px)} to{opacity:1;transform:translateY(0)} }

/* ─── INFO BOX for stagiaire ─── */
.info-box { padding:14px 18px; border-radius:14px; margin-bottom:16px;
            background:#eff6ff; border:1px solid #bfdbfe;
            display:flex; align-items:flex-start; gap:10px; }

.empty-state      { padding:60px 20px; text-align:center; }
.empty-state-icon { width:64px; height:64px; border-radius:20px; background:var(--accent-lt);
                    margin:0 auto 16px; display:flex; align-items:center; justify-content:center; font-size:28px; }
.pagination-wrap  { padding:12px 18px; border-top:1px solid #f1f5f9; display:flex;
                    align-items:center; justify-content:space-between; flex-wrap:wrap; gap:8px; }

@media(max-width:768px) {
    .abs-hero { padding:18px; }
    table.abs-table thead th:nth-child(n+5),
    table.abs-table tbody td:nth-child(n+5) { display:none; }
}
</style>

<div class="abs-wrap">

{{-- Flash --}}
@if(session('success'))
<div class="flash-ok">
    <div class="flash-ok-icon">
        <svg width="16" height="16" fill="none" stroke="white" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
        </svg>
    </div>
    <p style="font-size:13px;font-weight:600;color:var(--accent-tx);margin:0;">{{ session('success') }}</p>
</div>
@endif
@if(session('error'))
<div style="padding:14px 18px;background:#fff1f2;border:1px solid #fecdd3;border-radius:14px;margin-bottom:16px;">
    <p style="font-size:12px;color:#be123c;margin:0;">✕ {{ session('error') }}</p>
</div>
@endif

{{-- Info box for stagiaire --}}
@if($userRole === 'stagiaire')
<div class="info-box">
    <svg width="18" height="18" fill="none" stroke="#1d4ed8" viewBox="0 0 24 24" style="flex-shrink:0;margin-top:1px;">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
    </svg>
    <div>
        <p style="font-size:12px;font-weight:700;color:#1e40af;margin:0 0 2px;">Comment justifier une absence ?</p>
        <p style="font-size:11px;color:#3b82f6;margin:0;">
            Cliquez sur <strong>📎 Joindre un justificatif</strong> en face de la journée concernée et envoyez votre document (PDF, image…).
            Un seul fichier couvre toutes les demi-séances de la journée.
            Le statut passera en <strong>🕐 En attente</strong> jusqu'à validation par l'administration.
        </p>
    </div>
</div>
@endif

{{-- ─── HERO ─── --}}
<div class="abs-hero">
    <div style="display:flex;align-items:center;gap:16px;">
        <div class="abs-hero-icon">
            <svg width="26" height="26" fill="none" stroke="white" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
        </div>
        <div>
            <h1 class="abs-hero-title">{{ $heroTitle }}</h1>
            <p class="abs-hero-sub">
                <strong style="color:white">{{ $stats['total'] }}</strong> absence(s) ·
                <strong style="color:white">{{ $stats['total_heures_abs'] }}h</strong> cumulées
                @if($stats['en_attente'] > 0)
                    · <strong style="color:#fef08a;">{{ $stats['en_attente'] }} en attente</strong>
                @endif
            </p>
        </div>
    </div>
    <span style="background:rgba(255,255,255,0.15);border:1px solid rgba(255,255,255,0.25);
                 color:white;font-size:11px;font-weight:700;padding:6px 14px;border-radius:99px;">
        {{ ucfirst($userRole) }}
    </span>
</div>

{{-- ─── STATS (4 cards only — no S1/S2/S3/S4) ─── --}}
<div class="stats-grid" id="abs-stats-grid">
    <div class="stat-card">
        <div class="stat-icon" style="background:#fee2e2;">❌</div>
        <div><div class="stat-val" style="color:#dc2626;">{{ $stats['total'] }}</div><div class="stat-lbl">Total jours</div></div>
    </div>
    <div class="stat-card">
        <div class="stat-icon" style="background:#fff7ed;">🕐</div>
        <div><div class="stat-val" style="color:#c2410c;">{{ $stats['total_heures_abs'] }}h</div><div class="stat-lbl">Heures abs.</div></div>
    </div>
    <div class="stat-card">
        <div class="stat-icon" style="background:#d1fae5;">✅</div>
        <div><div class="stat-val" style="color:#059669;">{{ $stats['justifies'] }}</div><div class="stat-lbl">Justifiées</div></div>
    </div>
    <div class="stat-card">
        <div class="stat-icon" style="background:#fef3c7;">🕐</div>
        <div><div class="stat-val" style="color:#92400e;">{{ $stats['en_attente'] }}</div><div class="stat-lbl">En attente</div></div>
    </div>
    <div class="stat-card">
        <div class="stat-icon" style="background:#fce7f3;">⚠️</div>
        <div><div class="stat-val" style="color:#be185d;">{{ $stats['injustifies'] }}</div><div class="stat-lbl">Non justifiées</div></div>
    </div>
</div>

{{-- ════════════════════════════════════════════════════════════
     ADMIN : "ABSENTS DU JOUR" PANEL
     ════════════════════════════════════════════════════════════ --}}
<div id="abs-day-panel-wrap">
@if($canViewAll && $selectedDay)
@php
    $selDayStr    = $selectedDay->toDateString();
    $isToday      = $selDayStr === $todayStr;
    $dayLabel     = $isToday ? 'Aujourd\'hui' : $selectedDay->translatedFormat('l d M Y');
@endphp
<div class="day-panel">

    {{-- ── Header ── --}}
    <div class="day-panel-head">
        <div style="display:flex;align-items:center;gap:14px;flex-wrap:wrap;">
            <div style="width:40px;height:40px;border-radius:12px;background:var(--accent-lt);
                        display:flex;align-items:center;justify-content:center;font-size:20px;flex-shrink:0;">
                📅
            </div>
            <div>
                <div class="day-date-display">{{ $dayLabel }}</div>
                @if(!$isToday)
                    <div class="day-date-sub">{{ $selectedDay->format('d/m/Y') }}</div>
                @endif
            </div>
            @if($dayAbsents->count() > 0)
                <span class="day-absent-count">
                    ❌ {{ $dayAbsents->count() }} absent(s)
                </span>
            @else
                <span style="display:inline-flex;align-items:center;gap:5px;padding:5px 13px;
                             border-radius:99px;background:#d1fae5;color:#059669;
                             font-size:12px;font-weight:800;border:1px solid #a7f3d0;">
                    ✅ Aucune absence
                </span>
            @endif
        </div>

        {{-- Day navigation --}}
        <div class="day-nav">
            @if($prevDay)
                <a href="{{ request()->fullUrlWithQuery(['day' => $prevDay]) }}"
                   class="day-nav-btn" title="Jour précédent avec absences">
                    ← {{ \Carbon\Carbon::parse($prevDay)->format('d/m') }}
                </a>
            @else
                <span class="day-nav-btn disabled">← Préc.</span>
            @endif

            @if(!$isToday)
                <a href="{{ request()->fullUrlWithQuery(['day' => $todayStr]) }}"
                   class="day-nav-btn today-btn">
                    📅 Aujourd'hui
                </a>
            @endif

            <form method="GET" action="{{ route('absences.index') }}" style="display:inline-flex;align-items:center;gap:6px;">
                @foreach(request()->except('day') as $k => $v)
                    <input type="hidden" name="{{ $k }}" value="{{ $v }}">
                @endforeach
                <input type="date"
                       name="day"
                       value="{{ $selDayStr }}"
                       class="day-date-input"
                       onchange="this.form.submit()">
            </form>

            @if($nextDay)
                <a href="{{ request()->fullUrlWithQuery(['day' => $nextDay]) }}"
                   class="day-nav-btn" title="Jour suivant avec absences">
                    {{ \Carbon\Carbon::parse($nextDay)->format('d/m') }} →
                </a>
            @else
                <span class="day-nav-btn disabled">Suiv. →</span>
            @endif
        </div>
    </div>

    {{-- ── Day pills : last 60 days with absences ── --}}
    @if($availableDays->count() > 0)
    <div class="day-pills">
        @foreach($availableDays->sortByDesc('day') as $dRow)
            @php
                $dStr   = $dRow->day;
                $dCarb  = \Carbon\Carbon::parse($dStr);
                $isAct  = $dStr === $selDayStr;
                $isTod  = $dStr === $todayStr;
                $cls    = $isAct ? 'active' : ($isTod ? 'today' : '');
            @endphp
            <a href="{{ request()->fullUrlWithQuery(['day' => $dStr]) }}"
               class="day-pill {{ $cls }}"
               title="{{ $dRow->cnt }} absence(s)">
                @if($isTod && !$isAct)🔴 @endif
                {{ $dCarb->format('d/m') }}
                <span style="font-size:9px;opacity:.7;">({{ $dRow->cnt }})</span>
            </a>
        @endforeach
    </div>
    @endif

    {{-- ══ Absent stagiaires table ══ --}}
    @if($dayAbsents->isEmpty())
        <div style="padding:36px 20px;text-align:center;color:#94a3b8;font-size:13px;">
            🎉 Aucune absence enregistrée pour cette journée.
        </div>
    @else
        <div style="overflow-x:auto;">
        <table class="day-table">
            <thead>
                <tr>
                    <th>Stagiaire</th>
                    <th>Groupe</th>
                    <th>Module(s)</th>
                    <th>Formateur(s)</th>
                    <th>Demi-séances</th>
                    <th>Total heures</th>
                    <th>Statut</th>
                    @if($canJustify)
                    <th>Justificatif & Actions</th>
                    @endif
                </tr>
            </thead>
            <tbody>
            @foreach($dayAbsents as $da)
                @php
                    $initials = strtoupper(
                        mb_substr($da->stagiaire?->name ?? '?', 0, 1) .
                        mb_substr(explode(' ', $da->stagiaire?->name ?? '')[1] ?? '', 0, 1)
                    );
                    $rowClass = $da->is_pending && !$da->is_justified ? 'row-pending' : '';
                @endphp
                <tr class="{{ $rowClass }}">

                    {{-- Stagiaire --}}
                    <td>
                        <div style="display:flex;align-items:center;gap:8px;">
                            <div class="avatar">{{ $initials }}</div>
                            <div>
                                <div style="font-weight:700;color:#1e293b;font-size:12px;">
                                    {{ $da->stagiaire?->name ?? '—' }}
                                </div>
                                <div style="font-size:10px;color:#94a3b8;">
                                    {{ $da->stagiaire?->email ?? '' }}
                                </div>
                            </div>
                        </div>
                    </td>

                    {{-- Groupe --}}
                    <td>
                        <span style="font-size:11px;font-weight:600;color:#475569;">
                            {{ $da->stagiaire?->groupe?->name ?? '—' }}
                        </span>
                    </td>

                    {{-- Modules --}}
                    <td>
                        <div style="display:flex;flex-direction:column;gap:2px;">
                            @forelse($da->modules as $mod)
                                <span style="font-size:11px;font-weight:600;color:#1e293b;">
                                    {{ $mod->name }}
                                </span>
                            @empty
                                <span style="color:#94a3b8;">—</span>
                            @endforelse
                        </div>
                    </td>

                    {{-- Formateurs --}}
                    <td>
                        <div style="display:flex;flex-direction:column;gap:2px;">
                            @forelse($da->formateurs as $form)
                                <span style="font-size:11px;color:#475569;">{{ $form->name }}</span>
                            @empty
                                <span style="color:#94a3b8;">—</span>
                            @endforelse
                        </div>
                    </td>

                    {{-- Demi-séances --}}
                    <td>
                        <div style="display:flex;flex-wrap:wrap;gap:3px;">
                            @foreach($da->parts as $part)
                                @php $pc = $partConfig[$part] ?? $partConfig['s1']; @endphp
                                <span style="display:inline-flex;align-items:center;padding:2px 8px;
                                             border-radius:6px;font-size:10px;font-weight:800;
                                             background:{{ $pc['bg'] }};color:{{ $pc['color'] }};
                                             border:1px solid {{ $pc['border'] }};">
                                    {{ strtoupper($part) }}
                                </span>
                            @endforeach
                        </div>
                    </td>

                    {{-- Total heures --}}
                    <td>
                        <span class="hours-pill" style="font-size:14px;min-width:44px;">
                            {{ $da->total_duree }}h
                        </span>
                    </td>

                    {{-- Statut global --}}
                    <td>
                        @if($da->is_justified)
                            <span class="badge badge-justifie">✅ Justifiée(s)</span>
                        @elseif($da->is_pending)
                            <span class="badge badge-pending">🕐 En attente</span>
                        @else
                            <span class="badge badge-injustifie">⚠️ Non justifiée(s)</span>
                        @endif
                    </td>

                    @if($canJustify)
                    <td style="min-width:260px;">
                        @php
                            $allAbsIds  = $da->absences->pluck('id');
                            $allJust    = $da->absences->every(fn($a) => $a->justifie);
                            $anyPending = $da->absences->contains(
                                fn($a) => !$a->justifie && !empty($a->file_justification)
                            );
                            $sharedFile = $da->absences->first(fn($a) => $a->file_justification)?->file_justification;
                        @endphp

                        @if($da->is_admin_validated)
                            <div style="display:flex;flex-direction:column;gap:6px;">
                                <span class="badge-admin-allowed">✔ Autorisé sans justificatif</span>
                                <form method="POST" action="{{ route('absences.admin.annuler') }}" style="display:inline;">
                                    @csrf
                                    @foreach($allAbsIds as $id)
                                        <input type="hidden" name="absence_ids[]" value="{{ $id }}">
                                    @endforeach
                                    <button type="submit" class="btn-admin-revert">
                                        ↩ Annuler l'autorisation
                                    </button>
                                </form>
                            </div>

                        @elseif($allJust)
                            @if($sharedFile)
                                <a href="{{ Storage::url($sharedFile) }}" target="_blank"
                                   style="font-size:11px;font-weight:700;color:var(--accent);
                                          text-decoration:none;display:inline-flex;align-items:center;gap:4px;margin-bottom:8px;">
                                    📎 Voir le justificatif
                                </a><br>
                            @else
                                <span class="badge badge-justifie" style="margin-bottom:8px;display:inline-flex;">✅ Toutes justifiées</span><br>
                            @endif
                            <form method="POST" action="{{ route('absences.admin.bulk.unjustify') }}"
                                  onsubmit="return confirm('Annuler la justification pour toutes les demi-séances de cette journée ?')">
                                @csrf
                                @foreach($allAbsIds as $id)
                                    <input type="hidden" name="absence_ids[]" value="{{ $id }}">
                                @endforeach
                                <button type="submit" class="btn-toggle">↩ Annuler toutes</button>
                            </form>

                        @elseif($anyPending)
                            @if($sharedFile)
                                <a href="{{ Storage::url($sharedFile) }}" target="_blank"
                                   style="font-size:11px;font-weight:700;color:#92400e;
                                          text-decoration:none;display:inline-flex;align-items:center;gap:4px;margin-bottom:6px;">
                                    📎 Voir le justificatif
                                </a><br>
                            @endif
                            <div style="display:flex;flex-direction:column;gap:4px;margin-top:4px;">
                                @foreach($da->absences->where('justifie', false)->filter(fn($a) => !empty($a->file_justification)) as $abs)
                                    @php $pc = $partConfig[$abs->session_part ?? 's1'] ?? $partConfig['s1']; @endphp
                                    <div style="display:flex;align-items:center;gap:5px;flex-wrap:wrap;">
                                        <span style="font-size:9px;font-weight:800;padding:1px 6px;border-radius:5px;
                                                     background:{{ $pc['bg'] }};color:{{ $pc['color'] }};
                                                     border:1px solid {{ $pc['border'] }};">
                                            {{ strtoupper($abs->session_part) }}
                                        </span>
                                        <form method="POST" action="{{ route('absences.accept', $abs) }}" style="display:inline;">
                                            @csrf @method('PATCH')
                                            <button type="submit" class="btn-accept" style="font-size:9px;padding:2px 7px;">✓ Accepter</button>
                                        </form>
                                        <form method="POST" action="{{ route('absences.reject', $abs) }}"
                                              onsubmit="return confirm('Rejeter ce justificatif ?')" style="display:inline;">
                                            @csrf @method('PATCH')
                                            <button type="submit" class="btn-reject" style="font-size:9px;padding:2px 7px;">✕ Rejeter</button>
                                        </form>
                                    </div>
                                @endforeach
                            </div>

                        @else
                            <form method="POST"
                                  action="{{ route('absences.admin.fichier.jour') }}"
                                  enctype="multipart/form-data"
                                  style="margin-bottom:8px;">
                                @csrf
                                @foreach($allAbsIds as $id)
                                    <input type="hidden" name="absence_ids[]" value="{{ $id }}">
                                @endforeach
                                <label class="btn-upload-label">
                                    <svg width="10" height="10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                                    </svg>
                                    📎 Joindre un justificatif
                                    <input type="file" name="file_justification"
                                           accept=".pdf,.doc,.docx,.jpg,.jpeg,.png"
                                           style="display:none;"
                                           onchange="this.closest('form').submit()">
                                </label>
                                <div style="font-size:9px;color:#94a3b8;margin-top:2px;margin-bottom:8px;">
                                    Couvre les {{ $allAbsIds->count() }} demi-séance(s) du jour
                                </div>
                            </form>
                            <form method="POST" action="{{ route('absences.admin.valider') }}"
                                  style="margin-bottom:8px;"
                                  onsubmit="return confirm('⚠️ Autoriser cette absence sans justificatif ?\n\nLe signalement formateur sera supprimé mais l\'absence restera non-justifiée.')">
                                @csrf
                                @foreach($allAbsIds as $id)
                                    <input type="hidden" name="absence_ids[]" value="{{ $id }}">
                                @endforeach
                                <button type="submit" class="btn-admin-allow">
                                    🔓 Autoriser sans justificatif
                                </button>
                            </form>
                            <form method="POST" action="{{ route('absences.admin.bulk.justify') }}" style="margin-top:6px;">
                                @csrf
                                @foreach($allAbsIds as $id)
                                    <input type="hidden" name="absence_ids[]" value="{{ $id }}">
                                @endforeach
                                <button type="submit" class="btn-accept">✓ Justifier toutes</button>
                            </form>
                        @endif
                    </td>
                    @endif

                </tr>
            @endforeach
            </tbody>
        </table>
        </div>
        <div style="padding:10px 20px;border-top:1px solid #f1f5f9;font-size:11px;color:#94a3b8;
                    display:flex;align-items:center;gap:8px;flex-wrap:wrap;">
            📋 {{ $dayAbsents->count() }} stagiaire(s) absent(s) ·
            <strong style="color:#dc2626;">
                {{ round($dayAbsents->sum('total_duree'), 1) }}h
            </strong> total du jour
            @if($dayAbsents->where('is_pending', true)->count() > 0)
                · <span style="color:#92400e;font-weight:700;">
                    🕐 {{ $dayAbsents->where('is_pending', true)->count() }} justificatif(s) en attente
                </span>
            @endif
        </div>
    @endif
</div>
@endif
</div>{{-- #abs-day-panel-wrap --}}

{{-- ─── FILTER BAR ─── --}}
<form id="abs-filter-form" method="GET" action="{{ route('absences.index') }}" class="filter-bar">

    @if($canViewAll && request()->filled('day'))
        <input type="hidden" name="day" id="abs-day-hidden" value="{{ request('day') }}">
    @endif

    @if($canViewAll)
    <div class="filter-group">
        <label class="filter-label">Demi-séance</label>
        <select name="session_part" class="filter-select abs-auto-filter">
            <option value="">Toutes (S1–S4)</option>
            <option value="s1" @selected(request('session_part') === 's1')>S1</option>
            <option value="s2" @selected(request('session_part') === 's2')>S2</option>
            <option value="s3" @selected(request('session_part') === 's3')>S3</option>
            <option value="s4" @selected(request('session_part') === 's4')>S4</option>
        </select>
    </div>

    <div class="filter-group">
        <label class="filter-label">Filière</label>
        <select name="filiere_id" class="filter-select abs-auto-filter">
            <option value="">Toutes les filières</option>
            @foreach($filieres as $filiere)
                <option value="{{ $filiere->id }}" @selected(request('filiere_id') == $filiere->id)>
                    {{ $filiere->name }}
                </option>
            @endforeach
        </select>
    </div>

    <div class="filter-group">
        <label class="filter-label">Groupe</label>
        <select name="groupe_id" class="filter-select abs-auto-filter">
            <option value="">Tous les groupes</option>
            @foreach($groupes as $groupe)
                <option value="{{ $groupe->id }}" @selected(request('groupe_id') == $groupe->id)>
                    {{ $groupe->name }}
                </option>
            @endforeach
        </select>
    </div>

    <div class="filter-group">
        <label class="filter-label">Stagiaire</label>
        <select name="stagiaire_id" class="filter-select abs-auto-filter">
            <option value="">Tous</option>
            @foreach($stagiaires as $s)
                <option value="{{ $s->id }}" @selected(request('stagiaire_id') == $s->id)>
                    {{ $s->name }}
                </option>
            @endforeach
        </select>
    </div>
    @endif

    <div class="filter-group">
        <label class="filter-label">Statut</label>
        <select name="justifie" class="filter-select abs-auto-filter">
            <option value="">Tous les statuts</option>
            <option value="1"       @selected(request('justifie') === '1')>✅ Justifiée</option>
            <option value="pending" @selected(request('justifie') === 'pending')>🕐 En attente</option>
            <option value="0"       @selected(request('justifie') === '0')>⚠️ Non justifiée</option>
        </select>
    </div>

    <button type="submit" class="btn-filter" id="abs-filter-btn">
        <span id="abs-filter-spinner" style="display:none;">⏳</span>
        🔍 Filtrer
    </button>

    <a href="{{ route('absences.index', request()->only(['day'])) }}"
       id="abs-reset-btn"
       class="btn-reset"
       style="{{ request()->hasAny(['justifie','groupe_id','stagiaire_id','session_part','filiere_id']) ? '' : 'display:none;' }}">
        <svg width="11" height="11" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/>
        </svg>
        Réinitialiser
    </a>
</form>

{{-- ─── TABLE ─── --}}
<div class="abs-table-wrap" id="abs-table-wrap">
    <div class="abs-table-head">
        <div>
            <div style="font-size:10px;font-weight:800;color:#94a3b8;letter-spacing:1.5px;text-transform:uppercase;">
                📋 Historique des absences
            </div>
            <div style="font-size:12px;color:#64748b;margin-top:2px;">
                @if(!$canViewAll)
                    {{ $absencesByDay->total() }} jour(s) d'absence — page {{ $absencesByDay->currentPage() }}/{{ $absencesByDay->lastPage() }}
                @else
                    {{ $absencesGrouped->total() }} résultat(s) — page {{ $absencesGrouped->currentPage() }}/{{ $absencesGrouped->lastPage() }}
                @endif
            </div>
        </div>
        @if($canJustify && $stats['en_attente'] > 0)
        <div style="display:flex;align-items:center;gap:6px;font-size:11px;color:#92400e;
                    background:#fef3c7;border:1px solid #fde68a;padding:6px 12px;border-radius:10px;">
            🕐 <strong>{{ $stats['en_attente'] }}</strong> justificatif(s) en attente de validation
        </div>
        @endif
    </div>

    {{-- ════ STAGIAIRE — DAY-GROUPED VIEW ════ --}}
    @if(!$canViewAll)

        @if($absencesByDay->isEmpty())
            <div class="empty-state">
                <div class="empty-state-icon">🎉</div>
                <p style="font-size:15px;font-weight:700;color:#1e293b;margin:0 0 6px;">Aucune absence enregistrée</p>
                <p style="font-size:12px;color:#94a3b8;margin:0;">
                    {{ request()->hasAny(['justifie','session_part'])
                        ? 'Aucun résultat pour ces filtres.'
                        : 'Parfait ! Aucune absence pour le moment.' }}
                </p>
            </div>
        @else
            <div style="overflow-x:auto;">
            <table class="abs-table">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Module(s) / Séance(s)</th>
                        <th>Formateur(s)</th>
                        <th>Demi-séances absentes</th>
                        <th>Total heures</th>
                        <th>Statut</th>
                        <th>Justificatif</th>
                    </tr>
                </thead>
                <tbody>
                @foreach($absencesByDay as $day)
                    @php
                        $rowClass = $day->is_pending && !$day->is_justified ? 'row-pending' : '';
                    @endphp
                    <tr class="{{ $rowClass }}">

                        <td>
                            <div class="date-block-day">{{ $day->date?->format('d') }}</div>
                            <div class="date-block-rest">{{ $day->date?->translatedFormat('M Y') }}</div>
                            <div class="date-block-rest">{{ $day->date?->translatedFormat('l') }}</div>
                        </td>

                        <td>
                            @foreach($day->emplois as $emp)
                                <div style="margin-bottom:4px;padding-bottom:4px;
                                            {{ !$loop->last ? 'border-bottom:1px dashed #f1f5f9;' : '' }}">
                                    <div style="font-weight:600;color:#1e293b;font-size:12px;">
                                        {{ $emp->module?->name ?? '—' }}
                                    </div>
                                    <div style="font-size:10px;color:#94a3b8;margin-top:1px;">
                                        {{ $emp->date_debut->format('H:i') }} – {{ $emp->date_fin->format('H:i') }}
                                        @if($emp->salle)
                                            · 🏫 {{ $emp->salle->name }}
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </td>

                        <td>
                            @foreach($day->formateurs as $form)
                                <div style="font-size:11px;color:#475569;font-weight:500;">{{ $form->name }}</div>
                            @endforeach
                            @if($day->formateurs->isEmpty())
                                <span style="color:#94a3b8;">—</span>
                            @endif
                        </td>

                        <td>
                            <div style="display:flex;flex-wrap:wrap;gap:4px;">
                                @foreach($day->parts as $part)
                                    @php $pc = $partConfig[$part] ?? $partConfig['s1']; @endphp
                                    <span style="display:inline-flex;align-items:center;padding:3px 9px;
                                                 border-radius:8px;font-size:11px;font-weight:800;
                                                 background:{{ $pc['bg'] }};color:{{ $pc['color'] }};
                                                 border:1px solid {{ $pc['border'] }};">
                                        {{ strtoupper($part) }}
                                    </span>
                                @endforeach
                            </div>
                            <div style="font-size:9px;color:#94a3b8;margin-top:4px;">
                                {{ count($day->parts) }} × 2.5h
                            </div>
                        </td>

                        <td>
                            <span class="hours-pill">{{ $day->total_duree }}h</span>
                        </td>

                        <td>
                            @if($day->is_justified)
                                <span class="badge badge-justifie">✅ Justifiée(s)</span>
                            @elseif($day->is_pending)
                                <span class="badge badge-pending">🕐 En attente</span>
                                <div style="font-size:10px;color:#92400e;margin-top:4px;">En cours d'examen</div>
                            @else
                                <span class="badge badge-injustifie">⚠️ Non justifiée(s)</span>
                            @endif
                        </td>

                        <td>
                        @php
                            $absIds       = $day->absences->pluck('id');
                            $allJustified = $day->absences->every(fn($a) => $a->justifie);
                            $anyPending   = $day->absences->contains(
                                fn($a) => !$a->justifie && !empty($a->file_justification)
                            );
                            $sharedFile   = $day->absences->first(
                                fn($a) => $a->file_justification
                            )?->file_justification;
                        @endphp

                        @if($allJustified)
                            @if($sharedFile)
                                <a href="{{ Storage::url($sharedFile) }}" target="_blank"
                                   style="font-size:11px;font-weight:600;color:var(--accent);text-decoration:none;
                                          display:inline-flex;align-items:center;gap:4px;">
                                    📎 Voir le justificatif
                                </a>
                            @else
                                <span class="badge badge-justifie">✅ OK</span>
                            @endif

                        @elseif($anyPending)
                            <div style="display:flex;flex-direction:column;gap:6px;">
                                @if($sharedFile)
                                    <a href="{{ Storage::url($sharedFile) }}" target="_blank"
                                       style="font-size:11px;font-weight:600;color:#92400e;text-decoration:none;
                                              display:inline-flex;align-items:center;gap:4px;">
                                        📎 Voir le justificatif
                                    </a>
                                @endif
                                <form method="POST"
                                      action="{{ route('absences.stagiaire.fichier.jour.delete') }}"
                                      onsubmit="return confirm('Retirer le justificatif pour toute la journée ?')">
                                    @csrf @method('DELETE')
                                    @foreach($absIds as $id)
                                        <input type="hidden" name="absence_ids[]" value="{{ $id }}">
                                    @endforeach
                                    <button type="submit"
                                            style="font-size:10px;color:#64748b;background:none;border:none;
                                                   cursor:pointer;padding:0;display:inline-flex;align-items:center;gap:3px;">
                                        ✕ Retirer
                                    </button>
                                </form>
                            </div>

                        @else
                            <form method="POST"
                                  action="{{ route('absences.stagiaire.fichier.jour') }}"
                                  enctype="multipart/form-data">
                                @csrf
                                @foreach($absIds as $id)
                                    <input type="hidden" name="absence_ids[]" value="{{ $id }}">
                                @endforeach
                                <label class="btn-upload-label">
                                    <svg width="10" height="10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                                    </svg>
                                    📎 Joindre un justificatif
                                    <input type="file"
                                           name="file_justification"
                                           accept=".pdf,.doc,.docx,.jpg,.jpeg,.png"
                                           style="display:none;"
                                           onchange="this.closest('form').submit()">
                                </label>
                                <div style="font-size:9px;color:#94a3b8;margin-top:4px;">
                                    Valable pour les {{ $absIds->count() }} demi-séance(s) du jour
                                </div>
                            </form>
                        @endif
                        </td>

                    </tr>
                @endforeach
                </tbody>
            </table>
            </div>

            <div style="padding:10px 20px;border-top:1px solid #f1f5f9;
                        font-size:11px;color:#94a3b8;display:flex;align-items:center;
                        justify-content:space-between;flex-wrap:wrap;gap:8px;">
                <span>
                    📅 <strong style="color:#475569;">{{ $absencesByDay->total() }}</strong> jour(s) au total
                    @if($stats['total_heures_abs'] > 0)
                        &nbsp;·&nbsp; <strong style="color:#dc2626;">{{ $stats['total_heures_abs'] }}h</strong> cumulées
                    @endif
                </span>
                <span style="font-size:10px;color:#94a3b8;">
                    Page {{ $absencesByDay->currentPage() }} / {{ $absencesByDay->lastPage() }}
                </span>
            </div>

            @if($absencesByDay->hasPages())
            <div class="pagination-wrap">
                <span style="font-size:11px;color:#94a3b8;">
                    {{ $absencesByDay->firstItem() }}–{{ $absencesByDay->lastItem() }}
                    sur {{ $absencesByDay->total() }} jour(s)
                </span>
                <div style="display:flex;gap:6px;flex-wrap:wrap;">
                    @if($absencesByDay->onFirstPage())
                        <span style="padding:6px 12px;border-radius:8px;background:#f1f5f9;color:#cbd5e1;font-size:12px;font-weight:600;cursor:default;">←</span>
                    @else
                        <a href="{{ $absencesByDay->previousPageUrl() }}"
                           style="padding:6px 12px;border-radius:8px;background:white;border:1.5px solid #e2e8f0;color:#475569;font-size:12px;font-weight:600;text-decoration:none;"
                           onmouseover="this.style.borderColor='var(--accent-bd)';this.style.color='var(--accent-tx)';"
                           onmouseout="this.style.borderColor='#e2e8f0';this.style.color='#475569';">←</a>
                    @endif

                    @foreach($absencesByDay->getUrlRange(
                        max(1, $absencesByDay->currentPage() - 2),
                        min($absencesByDay->lastPage(), $absencesByDay->currentPage() + 2)
                    ) as $page => $url)
                        @if($page == $absencesByDay->currentPage())
                            <span style="padding:6px 12px;border-radius:8px;background:var(--accent-gr);color:white;font-size:12px;font-weight:700;">{{ $page }}</span>
                        @else
                            <a href="{{ $url }}"
                               style="padding:6px 12px;border-radius:8px;background:white;border:1.5px solid #e2e8f0;color:#475569;font-size:12px;font-weight:600;text-decoration:none;"
                               onmouseover="this.style.borderColor='var(--accent-bd)';this.style.color='var(--accent-tx)';"
                               onmouseout="this.style.borderColor='#e2e8f0';this.style.color='#475569';">{{ $page }}</a>
                        @endif
                    @endforeach

                    @if($absencesByDay->hasMorePages())
                        <a href="{{ $absencesByDay->nextPageUrl() }}"
                           style="padding:6px 12px;border-radius:8px;background:white;border:1.5px solid #e2e8f0;color:#475569;font-size:12px;font-weight:600;text-decoration:none;"
                           onmouseover="this.style.borderColor='var(--accent-bd)';this.style.color='var(--accent-tx)';"
                           onmouseout="this.style.borderColor='#e2e8f0';this.style.color='#475569';">→</a>
                    @else
                        <span style="padding:6px 12px;border-radius:8px;background:#f1f5f9;color:#cbd5e1;font-size:12px;font-weight:600;cursor:default;">→</span>
                    @endif
                </div>
            </div>
            @endif
        @endif

    {{-- ════ ADMIN — GROUPED VIEW ════ --}}
    @else

        @if($absencesGrouped->isEmpty())
            <div class="empty-state">
                <div class="empty-state-icon">🎉</div>
                <p style="font-size:15px;font-weight:700;color:#1e293b;margin:0 0 6px;">Aucune absence enregistrée</p>
                <p style="font-size:12px;color:#94a3b8;margin:0;">
                    {{ request()->hasAny(['justifie','groupe_id','stagiaire_id','session_part'])
                        ? 'Aucun résultat pour ces filtres.'
                        : 'Parfait ! Aucune absence pour le moment.' }}
                </p>
            </div>
        @else
            <div style="overflow-x:auto;">
            <table class="abs-table">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Stagiaire</th>
                        <th>Groupe</th>
                        <th>Module(s) du jour</th>
                        <th>Formateur(s)</th>
                        <th>Demi-séances</th>
                        <th>Total heures</th>
                        <th>Statut</th>
                        <th>Justificatif & Actions</th>
                    </tr>
                </thead>
                <tbody>
                @foreach($absencesGrouped as $row)
                    @php
                        $initials = strtoupper(
                            mb_substr($row->stagiaire?->name ?? '?', 0, 1) .
                            mb_substr(explode(' ', $row->stagiaire?->name ?? '')[1] ?? '', 0, 1)
                        );
                        $rowBg = $row->is_pending ? 'row-pending' : '';
                    @endphp
                    <tr class="{{ $rowBg }}">

                        <td style="min-width:90px;">
                            <div style="font-size:20px;font-weight:900;color:#1e293b;line-height:1;">
                                {{ $row->date?->format('d') }}
                            </div>
                            <div style="font-size:10px;color:#94a3b8;margin-top:2px;">
                                {{ $row->date?->translatedFormat('M Y') }}
                            </div>
                            <div style="font-size:10px;color:#94a3b8;">
                                {{ $row->date?->translatedFormat('l') }}
                            </div>
                        </td>

                        <td>
                            <div style="display:flex;align-items:center;gap:8px;">
                                <div class="avatar">{{ $initials }}</div>
                                <div>
                                    <div style="font-weight:700;color:#1e293b;font-size:12px;">
                                        {{ $row->stagiaire?->name ?? '—' }}
                                    </div>
                                    <div style="font-size:10px;color:#94a3b8;">
                                        {{ $row->stagiaire?->email ?? '' }}
                                    </div>
                                </div>
                            </div>
                        </td>

                        <td>
                            <span style="font-size:11px;font-weight:600;color:#475569;">
                                {{ $row->groupe?->name ?? $row->stagiaire?->groupe?->name ?? '—' }}
                            </span>
                        </td>

                        <td style="min-width:160px;">
                            @foreach($row->emplois as $emp)
                                <div style="margin-bottom:4px;padding-bottom:4px;
                                            {{ !$loop->last ? 'border-bottom:1px dashed #f1f5f9;' : '' }}">
                                    <div style="font-weight:600;color:#1e293b;font-size:12px;">
                                        {{ $emp->module?->name ?? '—' }}
                                    </div>
                                    <div style="font-size:9px;color:#94a3b8;margin-top:1px;">
                                        {{ $emp->date_debut->format('H:i') }}–{{ $emp->date_fin->format('H:i') }}
                                        @if($emp->salle) · 🏫 {{ $emp->salle->name }} @endif
                                    </div>
                                </div>
                            @endforeach
                            @if($row->emplois->isEmpty())
                                <span style="color:#94a3b8;">—</span>
                            @endif
                        </td>

                        <td>
                            @foreach($row->formateurs as $form)
                                <div style="font-size:11px;color:#475569;font-weight:500;">{{ $form->name }}</div>
                            @endforeach
                            @if($row->formateurs->isEmpty())
                                <span style="color:#94a3b8;">—</span>
                            @endif
                        </td>

                        <td>
                            <div style="display:flex;flex-wrap:wrap;gap:3px;">
                                @foreach($row->parts as $part)
                                    @php $pc = $partConfig[$part] ?? $partConfig['s1']; @endphp
                                    <span style="display:inline-flex;align-items:center;padding:2px 8px;
                                                 border-radius:7px;font-size:10px;font-weight:800;
                                                 background:{{ $pc['bg'] }};color:{{ $pc['color'] }};
                                                 border:1px solid {{ $pc['border'] }};">
                                        {{ strtoupper($part) }}
                                    </span>
                                @endforeach
                            </div>
                            <div style="font-size:9px;color:#94a3b8;margin-top:3px;">
                                {{ count($row->parts) }} × 2.5h
                            </div>
                        </td>

                        <td>
                            <span class="hours-pill">{{ $row->total_duree }}h</span>
                        </td>

                        <td>
                            @if($row->is_justified)
                                <span class="badge badge-justifie">✅ Justifiée(s)</span>
                            @elseif($row->is_pending)
                                <span class="badge badge-pending">🕐 En attente</span>
                                @if(!$canJustify)
                                    <div style="font-size:10px;color:#92400e;margin-top:4px;">En cours d'examen</div>
                                @endif
                            @else
                                <span class="badge badge-injustifie">⚠️ Non justifiée(s)</span>
                            @endif
                        </td>

                        <td style="min-width:260px;">
                            @php
                                $allRowAbsIds  = $row->absences->pluck('id');
                                $allRowJust    = $row->absences->every(fn($a) => $a->justifie);
                                $anyRowPending = $row->absences->contains(
                                    fn($a) => !$a->justifie && !empty($a->file_justification)
                                );
                                $rowSharedFile = $row->absences->first(fn($a) => $a->file_justification)?->file_justification;
                            @endphp

                            @if($row->is_admin_validated)
                                <div style="display:flex;flex-direction:column;gap:6px;">
                                    <span class="badge-admin-allowed">✔ Autorisé sans justificatif</span>
                                    <form method="POST" action="{{ route('absences.admin.annuler') }}" style="display:inline;">
                                        @csrf
                                        @foreach($allRowAbsIds as $id)
                                            <input type="hidden" name="absence_ids[]" value="{{ $id }}">
                                        @endforeach
                                        <button type="submit" class="btn-admin-revert">
                                            ↩ Annuler l'autorisation
                                        </button>
                                    </form>
                                </div>

                            @elseif($allRowJust)
                                @if($rowSharedFile)
                                    <a href="{{ Storage::url($rowSharedFile) }}" target="_blank"
                                       style="font-size:11px;font-weight:700;color:var(--accent);
                                              text-decoration:none;display:inline-flex;align-items:center;gap:4px;margin-bottom:8px;">
                                        📎 Voir le justificatif
                                    </a><br>
                                @else
                                    <span class="badge badge-justifie" style="margin-bottom:8px;display:inline-flex;">✅ Toutes justifiées</span><br>
                                @endif
                                @if($canJustify)
                                <form method="POST" action="{{ route('absences.admin.bulk.unjustify') }}"
                                      onsubmit="return confirm('Annuler la justification pour toutes les demi-séances ?')">
                                    @csrf
                                    @foreach($allRowAbsIds as $id)
                                        <input type="hidden" name="absence_ids[]" value="{{ $id }}">
                                    @endforeach
                                    <button type="submit" class="btn-toggle">↩ Annuler toutes</button>
                                </form>
                                @endif

                            @elseif($anyRowPending)
                                @if($rowSharedFile)
                                    <a href="{{ Storage::url($rowSharedFile) }}" target="_blank"
                                       style="font-size:11px;font-weight:700;color:#92400e;
                                              text-decoration:none;display:inline-flex;align-items:center;gap:4px;margin-bottom:6px;">
                                        📎 Voir le justificatif
                                    </a><br>
                                @endif
                                @if($canJustify)
                                <div style="display:flex;flex-direction:column;gap:4px;margin-top:4px;">
                                    @foreach($row->absences->where('justifie', false)->filter(fn($a) => !empty($a->file_justification)) as $abs)
                                        @php $pc = $partConfig[$abs->session_part ?? 's1'] ?? $partConfig['s1']; @endphp
                                        <div style="display:flex;align-items:center;gap:5px;flex-wrap:wrap;">
                                            <span style="font-size:9px;font-weight:800;padding:1px 6px;border-radius:5px;
                                                         background:{{ $pc['bg'] }};color:{{ $pc['color'] }};
                                                         border:1px solid {{ $pc['border'] }};">
                                                {{ strtoupper($abs->session_part) }}
                                            </span>
                                            <form method="POST" action="{{ route('absences.accept', $abs) }}" style="display:inline;">
                                                @csrf @method('PATCH')
                                                <button type="submit" class="btn-accept" style="font-size:9px;padding:2px 7px;">✓ Accepter</button>
                                            </form>
                                            <form method="POST" action="{{ route('absences.reject', $abs) }}"
                                                  onsubmit="return confirm('Rejeter ce justificatif ?')" style="display:inline;">
                                                @csrf @method('PATCH')
                                                <button type="submit" class="btn-reject" style="font-size:9px;padding:2px 7px;">✕ Rejeter</button>
                                            </form>
                                        </div>
                                    @endforeach
                                </div>
                                @else
                                    <div style="font-size:10px;color:#92400e;margin-top:4px;">En cours d'examen</div>
                                @endif

                            @else
                                @if($canJustify)
                                    <form method="POST"
                                          action="{{ route('absences.admin.fichier.jour') }}"
                                          enctype="multipart/form-data"
                                          style="margin-bottom:8px;">
                                        @csrf
                                        @foreach($allRowAbsIds as $id)
                                            <input type="hidden" name="absence_ids[]" value="{{ $id }}">
                                        @endforeach
                                        <label class="btn-upload-label">
                                            <svg width="10" height="10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                      d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                                            </svg>
                                            📎 Joindre un justificatif
                                            <input type="file" name="file_justification"
                                                   accept=".pdf,.doc,.docx,.jpg,.jpeg,.png"
                                                   style="display:none;"
                                                   onchange="this.closest('form').submit()">
                                        </label>
                                        <div style="font-size:9px;color:#94a3b8;margin-top:2px;margin-bottom:8px;">
                                            Couvre les {{ $allRowAbsIds->count() }} demi-séance(s) du jour
                                        </div>
                                    </form>
                                    <form method="POST" action="{{ route('absences.admin.valider') }}"
                                          style="margin-bottom:8px;"
                                          onsubmit="return confirm('⚠️ Autoriser cette absence sans justificatif ?\n\nLe signalement formateur sera supprimé mais l\'absence restera non-justifiée.')">
                                        @csrf
                                        @foreach($allRowAbsIds as $id)
                                            <input type="hidden" name="absence_ids[]" value="{{ $id }}">
                                        @endforeach
                                        <button type="submit" class="btn-admin-allow">
                                            🔓 Autoriser sans justificatif
                                        </button>
                                    </form>
                                    <form method="POST" action="{{ route('absences.admin.bulk.justify') }}" style="margin-top:6px;">
                                        @csrf
                                        @foreach($allRowAbsIds as $id)
                                            <input type="hidden" name="absence_ids[]" value="{{ $id }}">
                                        @endforeach
                                        <button type="submit" class="btn-accept">✓ Justifier toutes</button>
                                    </form>
                                @else
                                    <span style="font-size:10px;color:#94a3b8;">—</span>
                                @endif
                            @endif
                        </td>

                    </tr>
                @endforeach
                </tbody>
            </table>
            </div>

            @if($absencesGrouped->hasPages())
            <div class="pagination-wrap">
                <span style="font-size:11px;color:#94a3b8;">
                    {{ $absencesGrouped->firstItem() }}–{{ $absencesGrouped->lastItem() }}
                    sur {{ $absencesGrouped->total() }} jour(s)/stagiaire(s)
                </span>
                <div style="display:flex;gap:6px;">
                    @if($absencesGrouped->onFirstPage())
                        <span style="padding:6px 12px;border-radius:8px;background:#f1f5f9;color:#cbd5e1;font-size:12px;font-weight:600;">←</span>
                    @else
                        <a href="{{ $absencesGrouped->previousPageUrl() }}"
                           style="padding:6px 12px;border-radius:8px;background:white;border:1.5px solid #e2e8f0;color:#475569;font-size:12px;font-weight:600;text-decoration:none;">←</a>
                    @endif

                    @foreach($absencesGrouped->getUrlRange(
                        max(1,$absencesGrouped->currentPage()-2),
                        min($absencesGrouped->lastPage(),$absencesGrouped->currentPage()+2)
                    ) as $page => $url)
                        @if($page == $absencesGrouped->currentPage())
                            <span style="padding:6px 12px;border-radius:8px;background:var(--accent-gr);color:white;font-size:12px;font-weight:700;">{{ $page }}</span>
                        @else
                            <a href="{{ $url }}"
                               style="padding:6px 12px;border-radius:8px;background:white;border:1.5px solid #e2e8f0;color:#475569;font-size:12px;font-weight:600;text-decoration:none;">{{ $page }}</a>
                        @endif
                    @endforeach

                    @if($absencesGrouped->hasMorePages())
                        <a href="{{ $absencesGrouped->nextPageUrl() }}"
                           style="padding:6px 12px;border-radius:8px;background:white;border:1.5px solid #e2e8f0;color:#475569;font-size:12px;font-weight:600;text-decoration:none;">→</a>
                    @else
                        <span style="padding:6px 12px;border-radius:8px;background:#f1f5f9;color:#cbd5e1;font-size:12px;font-weight:600;">→</span>
                    @endif
                </div>
            </div>
            @endif
        @endif

    @endif {{-- end canViewAll --}}
</div>

</div>

{{-- ══ AJAX FILTER ENGINE ══ --}}
<style>
.abs-loading-overlay {
    position:fixed; inset:0; z-index:9999;
    background:rgba(255,255,255,0.55);
    backdrop-filter:blur(2px);
    display:none; align-items:center; justify-content:center;
}
.abs-loading-overlay.active { display:flex; }
.abs-spinner {
    width:44px; height:44px; border-radius:50%;
    border:4px solid var(--accent-bd);
    border-top-color:var(--accent);
    animation:abs-spin .7s linear infinite;
}
@keyframes abs-spin { to { transform:rotate(360deg); } }
.abs-swap-out { opacity:0; transform:translateY(4px); transition:all .18s ease; }
.abs-swap-in  { animation:abs-fade-in .25s ease forwards; }
@keyframes abs-fade-in {
    from { opacity:0; transform:translateY(4px); }
    to   { opacity:1; transform:translateY(0); }
}
</style>

<div class="abs-loading-overlay" id="abs-loading-overlay">
    <div style="background:white;border-radius:16px;padding:24px 32px;
                box-shadow:0 20px 60px rgba(0,0,0,0.12);
                display:flex;align-items:center;gap:14px;">
        <div class="abs-spinner"></div>
        <span style="font-size:13px;font-weight:700;color:#475569;">Chargement…</span>
    </div>
</div>

<script>
(function () {
    const SWAP_IDS = ['abs-stats-grid','abs-day-panel-wrap','abs-table-wrap'];
    let _timer = null;
    function debounce(fn, ms) { clearTimeout(_timer); _timer = setTimeout(fn, ms); }
    const overlay = document.getElementById('abs-loading-overlay');
    function showLoading() { overlay.classList.add('active'); }
    function hideLoading() { overlay.classList.remove('active'); }

    function absAjax(url) {
        showLoading();
        SWAP_IDS.forEach(id => { const el = document.getElementById(id); if (el) el.classList.add('abs-swap-out'); });
        fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
            .then(r => r.text())
            .then(html => {
                const parser = new DOMParser();
                const newDoc = parser.parseFromString(html, 'text/html');
                SWAP_IDS.forEach(id => {
                    const current = document.getElementById(id);
                    const fresh   = newDoc.getElementById(id);
                    if (current && fresh) {
                        current.classList.remove('abs-swap-out');
                        current.outerHTML = fresh.outerHTML;
                        const replaced = document.getElementById(id);
                        if (replaced) replaced.classList.add('abs-swap-in');
                    }
                });
                const newForm = newDoc.getElementById('abs-filter-form');
                const curForm = document.getElementById('abs-filter-form');
                if (newForm && curForm) {
                    newForm.querySelectorAll('select').forEach(newSel => {
                        const curSel = curForm.querySelector(`[name="${newSel.name}"]`);
                        if (curSel) curSel.value = newSel.value;
                    });
                }
                const newReset = newDoc.getElementById('abs-reset-btn');
                const curReset = document.getElementById('abs-reset-btn');
                if (curReset && newReset) { curReset.href = newReset.href; curReset.style.display = newReset.style.display; }
                const newDayHid = newDoc.getElementById('abs-day-hidden');
                const curDayHid = document.getElementById('abs-day-hidden');
                if (curDayHid && newDayHid) { curDayHid.value = newDayHid.value; }
                else if (!curDayHid && newDayHid) { const f = document.getElementById('abs-filter-form'); if (f) { const inp = document.createElement('input'); inp.type='hidden'; inp.name='day'; inp.id='abs-day-hidden'; inp.value=newDayHid.value; f.prepend(inp); } }
                else if (curDayHid && !newDayHid) { curDayHid.remove(); }
                window.history.pushState({ absUrl: url }, '', url);
            })
            .catch(() => { window.location.href = url; })
            .finally(() => { hideLoading(); bindDayNav(); bindPagination(); });
    }

    function filterUrl() {
        const form = document.getElementById('abs-filter-form');
        if (!form) return window.location.href;
        const data = new FormData(form);
        const params = new URLSearchParams();
        for (const [k, v] of data.entries()) { if (v !== '') params.set(k, v); }
        return form.action + (params.toString() ? '?' + params.toString() : '');
    }

    document.addEventListener('submit', function (e) {
        const form = e.target.closest('#abs-filter-form');
        if (!form) return;
        e.preventDefault();
        absAjax(filterUrl());
    });

    document.addEventListener('change', function (e) {
        if (!e.target.closest('#abs-filter-form')) return;
        if (!e.target.classList.contains('abs-auto-filter')) return;
        debounce(() => absAjax(filterUrl()), 120);
    });

    function bindDayNav() {
        document.querySelectorAll('.day-nav-btn[href], .day-pill[href]').forEach(el => {
            if (el.dataset.ajaxBound) return;
            el.dataset.ajaxBound = '1';
            el.addEventListener('click', function (e) { e.preventDefault(); absAjax(this.href); });
        });
        document.querySelectorAll('.day-date-input').forEach(inp => {
            if (inp.dataset.ajaxBound) return;
            inp.dataset.ajaxBound = '1';
            inp.addEventListener('change', function () {
                const form = this.closest('form');
                if (!form) return;
                const params = new URLSearchParams(new FormData(form));
                const clean = new URLSearchParams();
                for (const [k,v] of params) if (v) clean.set(k,v);
                absAjax(form.action + '?' + clean.toString());
            });
        });
    }

    function bindPagination() {
        document.querySelectorAll('.pagination-wrap a').forEach(el => {
            if (el.dataset.ajaxBound) return;
            el.dataset.ajaxBound = '1';
            el.addEventListener('click', function (e) { e.preventDefault(); absAjax(this.href); });
        });
    }

    window.addEventListener('popstate', function () { absAjax(window.location.href); });

    document.addEventListener('click', function (e) {
        const btn = e.target.closest('#abs-reset-btn');
        if (!btn) return;
        e.preventDefault();
        absAjax(btn.href);
    });

    bindDayNav();
    bindPagination();
})();
</script>

@endsection