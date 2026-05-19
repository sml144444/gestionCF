{{-- resources/views/emplois/index.blade.php --}}
@extends('layouts.app')
@section('title', 'Emploi du temps')
@section('page-title', 'Emploi du temps')

@section('content')

@php
    use App\Http\Controllers\EmploiDuTempsController;

    $canCreate       = Auth::user()->hasPermissionTo('emploi-create');
    $canEdit         = Auth::user()->hasPermissionTo('emploi-edit');
    $canDelete       = Auth::user()->hasPermissionTo('emploi-delete');
    $canLien         = Auth::user()->hasPermissionTo('emploi-lien');
    $canSelectModule = Auth::user()->hasPermissionTo('emploi-view-all-groups');
    $isGestionnaire  = Auth::user()->hasPermissionTo('emploi-view-all-groups');
    $canReport       = Auth::user()->hasPermissionTo('reportation-create');

    $pendingReportIds = $canReport
        ? \App\Models\Reportation::where('id_user', Auth::id())
            ->where('status', 'en_attente')
            ->pluck('id_emplois_du_temps')
            ->toArray()
        : [];

    $isStagiaire = Auth::user()->role === 'stagiaire';
    $restrictNextWeek = $isStagiaire || Auth::user()->role === 'formateur';

    $stagiaireYear = null;
    if ($isStagiaire && Auth::user()->id_groupe) {
        $stagiaireYear = Auth::user()->groupe?->annee ?? 1;
    }

    $palettes = [
        'admin'        => ['primary' => '#0a6640', 'light' => '#e8f5ee', 'medium' => '#1a8c56', 'text' => '#065f38'],
        'gestionnaire' => ['primary' => '#1e293b', 'light' => '#f1f5f9', 'medium' => '#334155', 'text' => '#1e293b'],
        'formateur'    => ['primary' => '#1a4f8a', 'light' => '#eff6ff', 'medium' => '#2563eb', 'text' => '#1e40af'],
        'stagiaire'    => ['primary' => '#1a4f8a', 'light' => '#eff6ff', 'medium' => '#2563eb', 'text' => '#1e40af'],
    ];

    $p           = $palettes[Auth::user()->role] ?? $palettes['stagiaire'];
    $accentYear1 = $p['primary'];
    $accentYear2 = $p['medium'];
    $accentColor = $accentYear1;

    $cardPalette = Auth::user()->role === 'gestionnaire'
        ? ['light' => '#eff6ff', 'medium' => '#2563eb', 'text' => '#1e40af']
        : $p;

    $progressFlat = [];
    foreach ($moduleProgress as $gId => $modules) {
        foreach ($modules as $mId => $hours) {
            $progressFlat[$gId . '_' . $mId] = round($hours, 2);
        }
    }

    $today = \Carbon\Carbon::today();

    $availablePromos = \App\Models\Groupe::where('annee', $year)
        ->whereNotNull('promo')
        ->distinct()
        ->orderBy('promo', 'desc')
        ->pluck('promo');
@endphp

<style>
/* ═══════════════════════════════════════
   BASE STYLES
═══════════════════════════════════════ */
.tt-wrap {
    font-family: 'Segoe UI', system-ui, sans-serif;
    min-width: 0;
    width: 100%;
    max-width: 100%;
    overflow: hidden;
}

.tt-scroll {
    overflow-x: auto; overflow-y: visible;
    border-radius: 16px; border: 1px solid #e2e8f0;
    background: #f8fafc; box-shadow: 0 2px 12px rgba(0,0,0,0.06);
    scrollbar-width: thin; scrollbar-color: {{ $accentColor }}40 transparent;
    width: 100%;
    max-width: 100%;
    box-sizing: border-box;
}
.tt-scroll::-webkit-scrollbar { height: 4px; }
.tt-scroll::-webkit-scrollbar-track { background: transparent; }
.tt-scroll::-webkit-scrollbar-thumb { background: {{ $accentColor }}40; border-radius: 99px; }
.tt-scroll::-webkit-scrollbar-thumb:hover { background: {{ $accentColor }}80; }

.tt-table { width: 100%; min-width: 860px; border-collapse: separate; border-spacing: 0; }

.tt-sticky-head { position: sticky; left: 0; z-index: 20; background: #f8fafc; }
.tt-sticky-cell { position: sticky; left: 0; z-index: 10; background: white; box-shadow: 3px 0 8px rgba(0,0,0,0.06); }
tr:hover .tt-sticky-cell { background: #fafbfc; }

.tt-filiere-row td { color: {{ $p['text'] }}; background: {{ $p['light'] }}; }
.tt-filiere-sticky-cell {
    position: sticky; left: 0; z-index: 15;
    background: {{ $p['light'] }};
    border-left: 4px solid {{ $accentColor }};
    padding: 8px 16px; white-space: nowrap;
    box-shadow: 3px 0 8px rgba(0,0,0,0.06);
    border-top: 1px solid {{ $accentColor }}30;
    border-bottom: 1px solid {{ $accentColor }}30;
}

.tt-day-head { padding: 10px 4px 8px; text-align: center; background: white; border-bottom: 2px solid #f1f5f9; }
.tt-day-head.today { background: {{ $accentColor }}; border-bottom-color: {{ $accentColor }}; }
.tt-day-name { font-size: 11px; font-weight: 700; letter-spacing: 0.5px; color: #334155; text-transform: uppercase; }
.tt-day-date { font-size: 18px; font-weight: 800; color: #1e293b; line-height: 1; margin-top: 2px; }
.tt-day-head.today .tt-day-name,
.tt-day-head.today .tt-day-date { color: white; }
.tt-today-pill {
    display: inline-block; font-size: 8px; font-weight: 700;
    background: rgba(255,255,255,0.25); color: white;
    padding: 2px 6px; border-radius: 99px; margin-top: 3px; letter-spacing: 0.5px;
}

.tt-seance-head { padding: 5px 2px 4px; text-align: center; background: #f8fafc; border-bottom: 1px solid #e9edf2; min-width: 52px; }
.tt-seance-label { font-size: 9px; font-weight: 800; color: #475569; letter-spacing: 1px; text-transform: uppercase; }
.tt-seance-time  { font-size: 8px; color: #64748b; margin-top: 1px; }

.tt-body-row { transition: background 0.15s; }
.tt-body-row:hover { background: #fafbfe; }
.tt-body-row td { border-bottom: 1px solid #f1f5f9; vertical-align: top; }
.tt-group-cell { padding: 10px 12px; min-width: 110px; }
.tt-group-name { font-size: 11px; font-weight: 700; color: #1e293b; }
.tt-group-sub  { font-size: 9px; color: #64748b; margin-top: 2px; }

.tt-session-td { padding: 5px; }

/* ════ CARD STYLES ════ */
.tt-card {
    border-radius: 14px;
    background: white;
    border: 1px solid #e2e8f0;
    border-top: 3px solid {{ $accentColor }};
    position: relative;
    transition: transform 0.15s, box-shadow 0.15s;
    overflow: hidden;
    min-height: 0;
}
.tt-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(0,0,0,0.09);
}
.tt-card-body { padding: 11px 12px 9px; }
.tt-card-header {
    display: flex; align-items: flex-start;
    justify-content: space-between; gap: 6px; margin-bottom: 8px;
}
.tt-card-module {
    font-size: 11.5px; font-weight: 700; color: #1e293b;
    line-height: 1.3; flex: 1; min-width: 0; overflow: hidden;
    text-overflow: ellipsis; display: -webkit-box;
    -webkit-line-clamp: 2; -webkit-box-orient: vertical;
}
.tt-card-time {
    font-size: 9px; font-weight: 700; padding: 3px 8px; border-radius: 8px;
    background: {{ $p['light'] }}; color: {{ $p['text'] }}; white-space: nowrap; flex-shrink: 0;
}
.tt-card-meta { display: flex; flex-direction: column; gap: 4px; }
.tt-card-row  { display: flex; align-items: center; gap: 5px; font-size: 10px; color: #475569; }
.tt-card-icon {
    width: 15px; height: 15px; border-radius: 4px;
    background: {{ $p['light'] }}; flex-shrink: 0;
    display: flex; align-items: center; justify-content: center;
}
.tt-card-icon svg { width: 9px; height: 9px; }
.tt-card-dot { width: 5px; height: 5px; border-radius: 50%; flex-shrink: 0; }

.card-distance { border-top-color: #f59e0b; background: #fffbeb; }
.card-distance .tt-card-module { color: #92400e; }
.card-distance .tt-card-time   { background: #fef3c7; color: #b45309; }

.card-brouillon { opacity: 0.7; border-style: dashed; border-top-style: dashed; border-top-color: #94a3b8; background: #f8fafc; }
.card-brouillon .tt-card-module { color: #475569; }
.card-brouillon .tt-card-time   { background: #f1f5f9; color: #64748b; }
.draft-badge {
    display: inline-block; font-size: 8px; font-weight: 800; letter-spacing: .5px;
    background: #f1f5f9; color: #64748b; padding: 2px 7px; border-radius: 6px;
    border: 1px solid #cbd5e1; text-transform: uppercase; margin-bottom: 5px;
}

.card-role-1 { border-top-color: {{ $cardPalette['medium'] }}; }
.card-role-2 { border-top-color: {{ $cardPalette['text'] }}; }
.card-role-3 { border-top-color: {{ $cardPalette['text'] }}80; }
.card-role-4 { border-top-color: {{ $cardPalette['medium'] }}b0; }

.card-progress { margin-top: 6px; }
.card-progress-track { height: 3px; background: #e2e8f0; border-radius: 99px; overflow: hidden; }
.card-progress-fill  { height: 100%; border-radius: 99px; transition: width 0.3s; }
.card-progress-meta  { display: flex; justify-content: space-between; font-size: 8px; color: #94a3b8; margin-top: 2px; }

.remplacant-badge {
    display: inline-flex; align-items: center; gap: 3px;
    font-size: 7px; font-weight: 800; padding: 1px 5px; border-radius: 99px;
    background: #f5f3ff; color: #7c3aed; border: 1px solid #ddd6fe;
    text-transform: uppercase; letter-spacing: .3px;
}
.remplacant-badge-module {
    display: inline-flex; align-items: center; gap: 3px;
    font-size: 7px; font-weight: 800; padding: 1px 5px; border-radius: 99px;
    background: #fff7ed; color: #c2410c; border: 1px solid #fed7aa;
    text-transform: uppercase; letter-spacing: .3px;
}

/* ════════════════════════════════════════
   ACTION BUTTONS — icon-only, hover overlay
════════════════════════════════════════ */
.tt-actions {
    position: absolute;
    top: 7px; right: 7px;
    display: none;
    align-items: center;
    gap: 2px;
    background: rgba(255,255,255,0.96);
    border: 1px solid #e2e8f0;
    border-radius: 10px;
    padding: 3px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.10);
    backdrop-filter: blur(4px);
}
.tt-card:hover .tt-actions { display: flex; }

/* Thin separator between action groups */
.tt-actions-sep {
    width: 1px; height: 14px;
    background: #e2e8f0;
    margin: 0 1px;
    border-radius: 99px;
    flex-shrink: 0;
}

/* Base icon button */
.tt-action-btn {
    width: 26px; height: 26px;
    border-radius: 7px;
    border: none;
    background: transparent;
    cursor: pointer;
    display: flex; align-items: center; justify-content: center;
    transition: background 0.12s, color 0.12s, transform 0.1s;
    color: #94a3b8;
    padding: 0;
    flex-shrink: 0;
}
.tt-action-btn svg { width: 13px; height: 13px; pointer-events: none; }
.tt-action-btn:hover { transform: scale(1.08); }

/* Variants */
.tt-action-btn.btn-edit:hover   { background: {{ $p['light'] }};  color: {{ $p['text'] }}; }
.tt-action-btn.btn-del:hover    { background: #fee2e2; color: #dc2626; }
.tt-action-btn.btn-report:hover { background: #f5f3ff; color: #7c3aed; }
.tt-action-btn.btn-lien:hover   { background: #fef3c7; color: #d97706; }

/* Pending report — locked state */
.tt-action-btn.btn-pending {
    opacity: 0.45;
    cursor: not-allowed;
}
.tt-action-btn.btn-pending:hover { background: transparent; transform: none; }

/* ════════════════════════════════════════
   CARD FOOTER — icon-only tabs
════════════════════════════════════════ */
.tt-card-footer {
    display: flex;
    border-top: 1px solid #f1f5f9;
}
.tt-foot-btn {
    flex: 1;
    padding: 8px 4px;
    border: none;
    cursor: pointer;
    background: transparent;
    color: #cbd5e1;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: background 0.12s, color 0.12s;
    text-decoration: none;
}
.tt-foot-btn svg { width: 14px; height: 14px; }
.tt-foot-btn:not(:last-child) { border-right: 1px solid #f1f5f9; }
.tt-foot-btn:hover             { background: #f8fafc; color: #475569; }
.tt-foot-btn-pres:hover        { background: #f0fdf4; color: #16a34a; }
.tt-foot-btn-cls:hover         { background: {{ $p['light'] }}; color: {{ $p['text'] }}; }
.card-distance .tt-card-footer { background: #fef9ee; }

/* ── Empty cell ── */
.tt-empty-td { padding: 5px; }
.tt-add-btn {
    width: 100%; min-height: 72px;
    border: 1.5px dashed #e2e8f0; border-radius: 10px; background: transparent;
    display: flex; align-items: center; justify-content: center;
    font-size: 16px; color: #e2e8f0; cursor: pointer; transition: all 0.15s;
}
.tt-add-btn:hover { border-color: {{ $accentColor }}; color: {{ $accentColor }}; background: {{ $p['light'] }}; }

/* ── Past day locked cell ── */
.tt-past-cell {
    width: 100%; min-height: 72px; border-radius: 10px;
    background: repeating-linear-gradient(135deg, transparent, transparent 4px, #f1f5f9 4px, #f1f5f9 8px);
    display: flex; align-items: center; justify-content: center; cursor: not-allowed;
}

/* ── Navigation ── */
.tt-nav-btn { display: inline-flex; align-items: center; gap: 5px; padding: 7px 14px; font-size: 12px; font-weight: 600; border-radius: 10px; border: 1.5px solid #e2e8f0; background: white; color: #475569; text-decoration: none; transition: all 0.15s; cursor: pointer; }
.tt-nav-btn:hover { border-color: #cbd5e1; background: #f8fafc; color: #1e293b; }
.tt-nav-btn.primary { background: {{ $accentColor }}; border-color: {{ $accentColor }}; color: white; }
.tt-nav-btn.primary:hover { opacity: 0.9; }
.tt-nav-btn.today-btn { color: {{ $accentColor }}; border-color: {{ $accentColor }}30; background: {{ $p['light'] }}; }

/* ── Tabs ── */
.tt-tab { display: inline-flex; align-items: center; gap: 8px; padding: 9px 18px; font-size: 12px; font-weight: 600; transition: all 0.15s; text-decoration: none; }
.tt-tab.active { color: white; }
.tt-tab.inactive { color: #64748b; }
.tt-tab.inactive:hover { background: #f8fafc; color: #1e293b; }
.tt-tab.disabled { color: #cbd5e1; cursor: not-allowed; opacity: 0.55; background: #f8fafc; pointer-events: none; }

/* ── Promo Selector ── */
.promo-selector {
    display: inline-flex; align-items: center; gap: 8px;
    background: #f8fafc; border: 1.5px solid #e2e8f0; border-radius: 12px; padding: 4px 8px 4px 14px;
}
.promo-label { font-size: 10px; font-weight: 700; color: #64748b; letter-spacing: 0.5px; }
.promo-select {
    height: 34px; padding: 0 24px 0 10px; border-radius: 8px; border: 1.5px solid #e2e8f0;
    background: white; font-size: 12px; font-weight: 600; color: {{ $accentColor }};
    cursor: pointer; outline: none;
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='%2364748b'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M19 9l-7 7-7-7'%3E%3C/path%3E%3C/svg%3E");
    background-repeat: no-repeat; background-position: right 8px center; background-size: 14px; appearance: none;
}
.promo-select:focus { border-color: {{ $accentColor }}; }
.promo-select option { color: #1e293b; }

/* ── Modal / Form ── */
.mode-toggle { display: flex; border-radius: 10px; overflow: hidden; border: 1.5px solid #e2e8f0; }
.mode-btn { flex: 1; padding: 9px; font-size: 12px; font-weight: 600; border: none; background: white; cursor: pointer; transition: all 0.15s; display: flex; align-items: center; justify-content: center; gap: 6px; }
.mode-btn.active-pres { background: {{ $accentColor }}; color: white; }
.mode-btn.active-dist { background: #f59e0b; color: white; }
.mode-btn:not(.active-pres):not(.active-dist) { color: #64748b; }

.tt-modal-overlay { position: fixed; inset: 0; z-index: 50; background: rgba(15,23,42,0.5); backdrop-filter: blur(4px); display: none; align-items: center; justify-content: center; }
.tt-modal-overlay.open { display: flex; }
.tt-modal-box { background: white; border-radius: 20px; width: 100%; max-width: 440px; margin: 16px; padding: 24px; max-height: 90vh; overflow-y: auto; box-shadow: 0 24px 60px rgba(0,0,0,0.18); }
.tt-modal-label { display: block; font-size: 9px; font-weight: 800; color: #94a3b8; letter-spacing: 1.5px; text-transform: uppercase; margin-bottom: 6px; }
.tt-modal-input { width: 100%; height: 42px; padding: 0 12px; border-radius: 10px; border: 1.5px solid #e2e8f0; background: #f8fafc; font-size: 13px; color: #1e293b; outline: none; transition: border-color 0.15s; box-sizing: border-box; }
.tt-modal-input:focus { border-color: {{ $accentColor }}; background: white; }

@keyframes pulse {
    0%, 100% { opacity: 1; }
    50%       { opacity: 0.6; }
}

/* ══════════════════════════════════════════════
   MOBILE STYLES
══════════════════════════════════════════════ */
@media (max-width: 900px) {
    .tt-header-wrap {
        flex-direction: column !important;
        align-items: stretch !important;
        gap: 10px !important;
    }
    .tt-header-left  { width: 100%; justify-content: space-between; flex-wrap: wrap; }
    .tt-header-right { width: 100%; justify-content: space-between; flex-wrap: wrap; }
}

@media (max-width: 640px) {
    .tt-tab { padding: 8px 10px; font-size: 11px; }
    .tt-tab-badge { display: none; }
    .promo-label { display: none; }
    .tt-nav-btn { padding: 7px 9px; font-size: 11px; }
    .tt-nav-date-label { display: none; }
}

.tt-mobile-nav  { display: none; }
.tt-mobile-grid { display: none; }

@media (max-width: 768px) {
    .tt-scroll      { display: none; }
    .tt-mobile-nav  { display: flex; }
    .tt-mobile-grid { display: block; }
    .tt-legend      { display: none; }
}

.tt-mobile-nav {
    overflow-x: auto;
    gap: 6px;
    padding: 0 2px 8px;
    scrollbar-width: none;
    -webkit-overflow-scrolling: touch;
    margin-bottom: 12px;
}
.tt-mobile-nav::-webkit-scrollbar { display: none; }

.tt-day-pill {
    flex-shrink: 0;
    padding: 7px 16px;
    border-radius: 20px;
    font-size: 11px;
    font-weight: 700;
    border: 1.5px solid #e2e8f0;
    background: white;
    color: #475569;
    cursor: pointer;
    transition: all 0.15s;
    white-space: nowrap;
    text-align: center;
    line-height: 1.4;
}
.tt-day-pill.today-pill { border-color: {{ $accentColor }}; color: {{ $accentColor }}; }
.tt-day-pill.active     { background: {{ $accentColor }}; border-color: {{ $accentColor }}; color: white; }

.tt-mobile-filiere {
    padding: 6px 12px;
    border-radius: 8px;
    font-size: 9px;
    font-weight: 800;
    letter-spacing: 1.5px;
    text-transform: uppercase;
    background: {{ $p['light'] }};
    color: {{ $p['text'] }};
    border-left: 3px solid {{ $accentColor }};
    margin-bottom: 8px;
    margin-top: 14px;
}

.tt-mobile-group { margin-bottom: 12px; }

.tt-mobile-group-header {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 8px 12px;
    border-radius: 10px;
    margin-bottom: 6px;
    font-size: 11px;
    font-weight: 800;
    background: {{ $p['light'] }};
    color: {{ $p['text'] }};
}

.tt-mobile-session {
    border-radius: 14px;
    background: white;
    border: 1px solid #e2e8f0;
    border-top: 3px solid {{ $accentColor }};
    margin-bottom: 8px;
    overflow: hidden;
}
.tt-mobile-session-body { padding: 12px 14px 10px; }
.tt-mobile-session-header {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    gap: 8px;
    margin-bottom: 10px;
}
.tt-mobile-session-module {
    font-size: 13px;
    font-weight: 700;
    color: #1e293b;
    flex: 1;
    line-height: 1.3;
}
.tt-mobile-session-badge {
    font-size: 10px;
    font-weight: 700;
    padding: 3px 10px;
    border-radius: 8px;
    background: {{ $p['light'] }};
    color: {{ $p['text'] }};
    white-space: nowrap;
    flex-shrink: 0;
}
.tt-mobile-session-meta { display: flex; flex-direction: column; gap: 6px; }
.tt-mobile-session-row  { display: flex; align-items: center; gap: 7px; font-size: 11px; color: #475569; }

.tt-mobile-empty {
    text-align: center;
    padding: 24px 16px;
    color: #94a3b8;
    font-size: 12px;
    border: 1.5px dashed #e2e8f0;
    border-radius: 12px;
    margin-bottom: 8px;
}

/* ── Mobile footer — icon-only ── */
.tt-mobile-session-footer {
    display: flex;
    border-top: 1px solid #f1f5f9;
}
.tt-mobile-foot-btn {
    flex: 1;
    padding: 11px 4px;
    border: none;
    background: transparent;
    color: #cbd5e1;
    display: flex;
    align-items: center;
    justify-content: center;
    text-decoration: none;
    cursor: pointer;
    transition: background 0.12s, color 0.12s;
}
.tt-mobile-foot-btn svg { width: 16px; height: 16px; }
.tt-mobile-foot-btn:not(:last-child) { border-right: 1px solid #f1f5f9; }
.tt-mobile-foot-btn:hover            { background: #f8fafc; color: #475569; }
.tt-mobile-foot-btn.pres:hover       { background: #f0fdf4; color: #16a34a; }
.tt-mobile-foot-btn.cls:hover        { background: {{ $p['light'] }}; color: {{ $p['text'] }}; }

@media (max-width: 480px) {
    .tt-modal-overlay { align-items: flex-end !important; }
    .tt-modal-box {
        max-width: 100% !important;
        margin: 0 !important;
        border-radius: 20px 20px 0 0 !important;
        max-height: 95vh !important;
    }
    #delete-modal,
    #lien-modal,
    #remplacant-modal,
    #report-modal,
    #publish-modal {
        align-items: flex-end !important;
    }
    #delete-modal > div,
    #lien-modal > div,
    #remplacant-modal > div,
    #report-modal > div,
    #publish-modal > div {
        max-width: 100% !important;
        margin: 0 !important;
        border-radius: 20px 20px 0 0 !important;
        max-height: 92vh !important;
        overflow-y: auto;
    }
}
</style>

{{-- ════ FLASH ════ --}}
@if(session('success'))
    <div class="mb-4 px-4 py-3 rounded-xl text-sm flex items-center gap-2"
         style="background:#f0fdf4; border:1px solid #bbf7d0; color:#15803d;">
        <svg class="w-4 h-4 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
        </svg>
        {{ session('success') }}
    </div>
@endif
@if($errors->any())
    <div class="mb-4 px-4 py-3 rounded-xl text-sm"
         style="background:#fff1f2; border:1px solid #fecdd3; color:#be123c;">
        <ul class="list-disc list-inside space-y-0.5">
            @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
        </ul>
    </div>
@endif

{{-- ════ STAGIAIRE : RESTRICTED ACCESS BLOCK ════ --}}
@if($isStagiaire)
@php
    $joursAvance = 1;
    $prochainLundi = \Carbon\Carbon::now()->startOfWeek(\Carbon\Carbon::MONDAY)->addWeek();
    $visibleDepuis = $prochainLundi->copy()->subDays($joursAvance);

    $estSemaineActuelle  = $weekStart->eq(\Carbon\Carbon::now()->startOfWeek(\Carbon\Carbon::MONDAY));
    $estSemaineProchaine = $weekStart->eq($prochainLundi);

    $peutVoirSemaineProchaine = \Carbon\Carbon::now()->gte($visibleDepuis)
                             && ($nextWeekHasSessions ?? false);

    $semaineAutorisee = $weekStart->lte(\Carbon\Carbon::now()->startOfWeek(\Carbon\Carbon::MONDAY))
                     || ($estSemaineProchaine && $peutVoirSemaineProchaine);
@endphp

@if(!$semaineAutorisee)
    @php
        $blockedBecauseNoSessions = \Carbon\Carbon::now()->gte($visibleDepuis)
                                 && !($nextWeekHasSessions ?? false);
    @endphp

    @if($blockedBecauseNoSessions)
        <div style="margin-bottom:16px; padding:12px 16px; border-radius:12px; font-size:13px;
                    display:flex; align-items:center; gap:8px;
                    background:#f0fdf4; border:1px solid #bbf7d0; color:#15803d;">
            <svg width="16" height="16" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
            </svg>
            Aucune séance planifiée la semaine prochaine — profitez de vos vacances ! 🎉
        </div>
    @else
        <div style="margin-bottom:16px; padding:12px 16px; border-radius:12px; font-size:13px;
                    display:flex; align-items:center; gap:8px;
                    background:#fff7ed; border:1px solid #fed7aa; color:#9a3412;">
            <svg width="16" height="16" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
            </svg>
            L'emploi du temps de cette semaine sera disponible
            <strong>le {{ $visibleDepuis->translatedFormat('l d M') }}</strong>
            — vous recevrez un email dès sa publication.
        </div>
    @endif

@elseif($estSemaineProchaine && $peutVoirSemaineProchaine)
    <div style="margin-bottom:16px; padding:12px 16px; border-radius:12px; font-size:13px;
                display:flex; align-items:center; gap:8px;
                background:#eff6ff; border:1px solid #bfdbfe; color:#1e40af;">
        <svg width="16" height="16" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
        </svg>
        Aperçu de la semaine prochaine — planning provisoire.
    </div>
@endif
@endif

<div class="tt-wrap">

{{-- ════ HEADER ════ --}}
<div class="tt-header-wrap" style="display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:12px; margin-bottom:20px;">

    {{-- LEFT : Year tabs + Promo --}}
    <div class="tt-header-left" style="display:flex; align-items:center; gap:10px; flex-wrap:wrap;">
        <div style="display:inline-flex; border-radius:12px; overflow:hidden; border:1.5px solid #e2e8f0; background:white;">
            @php
                $tab1Disabled = $isStagiaire && $stagiaireYear !== null && $stagiaireYear !== 1;
                $tab2Disabled = $isStagiaire && $stagiaireYear !== null && $stagiaireYear !== 2;
                $tab3Disabled = $isStagiaire && $stagiaireYear !== null && $stagiaireYear !== 3;
            @endphp

            @if($tab1Disabled)
                <span class="tt-tab disabled" title="Vous êtes inscrit en {{ $stagiaireYear }}ème année">Année 1 &nbsp;🔒</span>
            @else
                <a href="{{ route('emplois.index', ['year' => 1, 'week' => $weekStart->toDateString(), 'promo' => $promo]) }}"
                   class="tt-tab {{ $year === 1 ? 'active' : 'inactive' }}"
                   style="{{ $year === 1 ? 'background:'.$accentYear1.';' : '' }}">
                    Année 1
                    <span class="tt-tab-badge" style="font-size:9px; padding:2px 7px; border-radius:99px; font-weight:700;
                         {{ $year === 1 ? 'background:rgba(255,255,255,0.2); color:white;'
                                       : 'background:'.$p['light'].'; color:'.$p['text'].';' }}">1ère</span>
                </a>
            @endif

            @if($tab2Disabled)
                <span class="tt-tab disabled" style="border-left:1.5px solid #e2e8f0;" title="Vous êtes inscrit en {{ $stagiaireYear }}ème année">Année 2 &nbsp;🔒</span>
            @else
                <a href="{{ route('emplois.index', ['year' => 2, 'week' => $weekStart->toDateString(), 'promo' => $promo]) }}"
                   class="tt-tab {{ $year === 2 ? 'active' : 'inactive' }}"
                   style="{{ $year === 2 ? 'background:'.$accentYear1.';' : '' }} border-left:1.5px solid #e2e8f0;">
                    Année 2
                    <span class="tt-tab-badge" style="font-size:9px; padding:2px 7px; border-radius:99px; font-weight:700;
                         {{ $year === 2 ? 'background:rgba(255,255,255,0.2); color:white;'
                                       : 'background:'.$p['light'].'; color:'.$p['text'].';' }}">2ème</span>
                </a>
            @endif

            @if($tab3Disabled)
                <span class="tt-tab disabled" style="border-left:1.5px solid #e2e8f0;" title="Vous êtes inscrit en {{ $stagiaireYear }}ème année">Année 3 &nbsp;🔒</span>
            @else
                <a href="{{ route('emplois.index', ['year' => 3, 'week' => $weekStart->toDateString(), 'promo' => $promo]) }}"
                   class="tt-tab {{ $year === 3 ? 'active' : 'inactive' }}"
                   style="{{ $year === 3 ? 'background:'.$accentYear1.';' : '' }} border-left:1.5px solid #e2e8f0;">
                    Année 3
                    <span class="tt-tab-badge" style="font-size:9px; padding:2px 7px; border-radius:99px; font-weight:700;
                         {{ $year === 3 ? 'background:rgba(255,255,255,0.2); color:white;'
                                       : 'background:'.$p['light'].'; color:'.$p['text'].';' }}">3ème</span>
                </a>
            @endif
        </div>

        <div class="promo-selector">
            <span class="promo-label">Promotion</span>
            <select id="promo-select" class="promo-select" onchange="changePromo(this.value)">
                @foreach($availablePromos as $pVal)
                    <option value="{{ $pVal }}" {{ $promo == $pVal ? 'selected' : '' }}>Promo {{ $pVal }}</option>
                @endforeach
            </select>
        </div>
    </div>

    {{-- RIGHT : Week nav --}}
    <div class="tt-header-right" style="display:flex; align-items:center; gap:8px; flex-wrap:wrap;">

        <div class="tt-nav-date-label" style="font-size:11px; color:#64748b;">
            <strong style="color:#334155;">{{ $weekStart->translatedFormat('d M') }}</strong>
            &nbsp;–&nbsp;
            <strong style="color:#334155;">{{ $weekEnd->translatedFormat('d M Y') }}</strong>
        </div>

        <a href="{{ route('emplois.index', ['year' => $year, 'week' => $weekStart->copy()->subWeek()->toDateString(), 'promo' => $promo]) }}" class="tt-nav-btn">
            <svg style="width:12px;height:12px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"/></svg>
        </a>

        <a href="{{ route('emplois.index', ['year' => $year, 'promo' => $promo]) }}" class="tt-nav-btn today-btn">Aujourd'hui</a>

        @php
            $semaineSuivante   = $weekStart->copy()->addWeek();
            $prochainLundiNav  = \Carbon\Carbon::now()->startOfWeek(\Carbon\Carbon::MONDAY)->addWeek();
            $visibleDepuisNav  = $prochainLundiNav->copy()->subDays(1);
            $peutNaviguerSuivante = !$restrictNextWeek
                || $semaineSuivante->lte(\Carbon\Carbon::now()->startOfWeek(\Carbon\Carbon::MONDAY))
                || ($semaineSuivante->eq($prochainLundiNav) && \Carbon\Carbon::now()->gte($visibleDepuisNav));
        @endphp

        @if($peutNaviguerSuivante)
            <a href="{{ route('emplois.index', ['year' => $year, 'week' => $weekStart->copy()->addWeek()->toDateString(), 'promo' => $promo]) }}" class="tt-nav-btn">
                <svg style="width:12px;height:12px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
            </a>
        @else
            <span class="tt-nav-btn" style="opacity:0.35; cursor:not-allowed;">
                <svg style="width:12px;height:12px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
            </span>
        @endif

        <a href="{{ route('emplois.pdf', ['year' => $year, 'week' => $weekStart->toDateString(), 'promo' => $promo]) }}"
           class="tt-nav-btn" title="Télécharger PDF"
           style="color:#dc2626; border-color:#fecdd3; background:#fff1f2;">
            <svg style="width:12px;height:12px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414A1 1 0 0119 9.414V19a2 2 0 01-2 2z"/>
            </svg>
            PDF
        </a>

        @if($canSeeDraft && $canCreate)
        <form id="publish-form" method="POST"
              action="{{ route('emplois.publish', ['year' => $year, 'week' => $weekStart->toDateString(), 'promo' => $promo]) }}"
              style="display:inline;">
            @csrf
            <button type="button"
                    onclick="{{ $draftCount > 0 ? 'openPublishModal()' : '' }}"
                    class="tt-nav-btn {{ $draftCount > 0 ? 'primary' : '' }}"
                    {{ $draftCount === 0 ? 'disabled' : '' }}
                    style="{{ $draftCount > 0
                        ? 'background:#16a34a; border-color:#16a34a; color:white; box-shadow:0 4px 12px rgba(22,163,74,0.3);'
                        : 'opacity:.45; cursor:not-allowed;' }}">
                <svg style="width:12px;height:12px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                </svg>
                Publier
                @if($draftCount > 0)
                    <span style="background:rgba(255,255,255,0.25); font-size:9px; font-weight:800; padding:1px 6px; border-radius:99px;">{{ $draftCount }}</span>
                @endif
            </button>
        </form>
        @endif

    </div>
</div>

{{-- ════ MOBILE DAY NAVIGATOR ════ --}}
<div class="tt-mobile-nav" id="tt-mobile-day-nav">
    @foreach($dayDates as $dayNum => $date)
        <button
            class="tt-day-pill {{ $date->isToday() ? 'today-pill' : '' }} {{ $loop->first ? 'active' : '' }}"
            data-day="{{ $dayNum }}"
            onclick="switchMobileDay({{ $dayNum }})">
            <div>{{ $date->translatedFormat('D') }}</div>
            <div style="font-size:14px; font-weight:800;">{{ $date->format('d') }}</div>
        </button>
    @endforeach
</div>

{{-- ════ MOBILE CARD GRID ════ --}}
<div class="tt-mobile-grid" id="tt-mobile-grid">
    @foreach($dayDates as $dayNum => $date)
        <div class="tt-mobile-day-block" data-day="{{ $dayNum }}" style="{{ $loop->first ? '' : 'display:none;' }}">

            @forelse($groupesByFiliere as $filiereId => $groupes)
                @php $filiere = $groupes->first()->filiere; @endphp
                <div class="tt-mobile-filiere">{{ $filiere->name ?? 'Filière' }}</div>

                @foreach($groupes as $groupe)
                    <div class="tt-mobile-group">
                        <div class="tt-mobile-group-header">
                            <div style="width:3px; height:18px; border-radius:99px; background:{{ $accentColor }}; flex-shrink:0;"></div>
                            <span>{{ $groupe->name ?? 'G'.$groupe->id }}</span>
                            <span style="font-size:9px; color:#94a3b8; font-weight:500; margin-left:auto;">{{ $groupe->option->titre ?? $filiere->name ?? '' }}</span>
                        </div>

                        @php $hasSessions = false; @endphp
                        @foreach(EmploiDuTempsController::SEANCES as $sNum => $seance)
                            @php
                                $cell = $grid[$groupe->id][$dayNum][$sNum] ?? ['type' => 'empty'];
                                if ($cell['type'] === 'session') $hasSessions = true;
                            @endphp
                        @endforeach

                        @if(!$hasSessions)
                            <div class="tt-mobile-empty">Aucune séance ce jour</div>
                        @else
                            @foreach(EmploiDuTempsController::SEANCES as $sNum => $seance)
                                @php
                                    $cell = $grid[$groupe->id][$dayNum][$sNum] ?? ['type' => 'empty'];
                                @endphp
                                @if($cell['type'] === 'session')
                                    @php
                                        $emploi   = $cell['emploi'];
                                        $colspan  = $cell['colspan'];
                                        $spanLbl  = EmploiDuTempsController::spanLabel($sNum, $colspan);
                                        $totalH   = EmploiDuTempsController::totalHours($sNum, $colspan);
                                        $isRemote = ($emploi->mode ?? 'presentiel') === 'distance';
                                        $isDraft  = $emploi->statut === 'brouillon';
                                        $topColor = $isDraft ? '#94a3b8' : ($isRemote ? '#f59e0b' : $accentColor);

                                        $sessionRemplacant = $emploi->id_user_remplacant ? $emploi->remplacant : null;
                                        $isFuture          = $emploi->date_debut->isFuture();
                                        $moduleRemplacant  = (!$sessionRemplacant && $isFuture && $emploi->module?->id_user_remplacant)
                                                            ? $emploi->module->remplacant : null;
                                        $hasRemplacant    = $sessionRemplacant || $moduleRemplacant;
                                        $activeRemplacant = $sessionRemplacant ?? $moduleRemplacant;
                                    @endphp

                                    <div class="tt-mobile-session" style="border-top-color:{{ $topColor }}; {{ $isDraft ? 'opacity:.75; border-style:dashed; border-top-style:dashed; background:#f8fafc;' : ($isRemote ? 'background:#fffbeb;' : '') }}">
                                        <div class="tt-mobile-session-body">
                                            @if($isDraft)
                                                <div style="display:inline-block; font-size:8px; font-weight:800; background:#f1f5f9; color:#64748b; padding:2px 7px; border-radius:6px; border:1px solid #cbd5e1; text-transform:uppercase; margin-bottom:6px;">Brouillon</div>
                                            @endif

                                            <div class="tt-mobile-session-header">
                                                <div class="tt-mobile-session-module"
                                                     style="{{ $isDraft ? 'color:#475569;' : ($isRemote ? 'color:#92400e;' : '') }}">
                                                    {{ $emploi->module->name ?? 'Module' }}
                                                </div>
                                                <div class="tt-mobile-session-badge"
                                                     style="{{ $isRemote ? 'background:#fef3c7; color:#b45309;' : '' }}">
                                                    {{ $spanLbl }} · {{ $totalH }}h
                                                </div>
                                            </div>

                                            <div class="tt-mobile-session-meta">
                                                @if($hasRemplacant)
                                                    <div class="tt-mobile-session-row" style="flex-direction:column; align-items:flex-start; gap:3px;">
                                                        <div style="display:flex; align-items:center; gap:6px;">
                                                            <div style="width:5px; height:5px; border-radius:50%; background:#94a3b8; flex-shrink:0;"></div>
                                                            <span style="font-size:10px; color:#94a3b8; text-decoration:line-through;">{{ $emploi->gestionnaire->name ?? '—' }}</span>
                                                        </div>
                                                        <div style="display:flex; align-items:center; gap:6px; flex-wrap:wrap;">
                                                            <div style="width:5px; height:5px; border-radius:50%; background:#7c3aed; flex-shrink:0;"></div>
                                                            <span style="font-size:11px; color:#5b21b6; font-weight:700;">{{ $activeRemplacant->name }}</span>
                                                            @if($moduleRemplacant && !$sessionRemplacant)
                                                                <span style="font-size:7px; font-weight:800; padding:1px 5px; border-radius:99px; background:#fff7ed; color:#c2410c; border:1px solid #fed7aa; text-transform:uppercase;">Module</span>
                                                            @else
                                                                <span style="font-size:7px; font-weight:800; padding:1px 5px; border-radius:99px; background:#f5f3ff; color:#7c3aed; border:1px solid #ddd6fe; text-transform:uppercase;">Remplaçant</span>
                                                            @endif
                                                        </div>
                                                    </div>
                                                @else
                                                    <div class="tt-mobile-session-row">
                                                        <svg width="13" height="13" fill="none" stroke="{{ $accentColor }}" viewBox="0 0 24 24">
                                                            <circle cx="12" cy="7" r="4" stroke-width="2"/>
                                                            <path stroke-width="2" d="M4 21v-1a8 8 0 0116 0v1"/>
                                                        </svg>
                                                        {{ $emploi->gestionnaire->name ?? '—' }}
                                                    </div>
                                                @endif

                                                @if($isRemote)
                                                    <div class="tt-mobile-session-row" style="color:#b45309;">
                                                        <svg width="13" height="13" fill="none" stroke="#f59e0b" viewBox="0 0 24 24">
                                                            <path stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.723v6.554a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                                                        </svg>
                                                        À distance
                                                        @if($emploi->lien_distance)
                                                            &nbsp;·&nbsp;
                                                            <a href="{{ $emploi->lien_distance }}" target="_blank"
                                                               style="color:#b45309; font-weight:700; text-decoration:underline;">Rejoindre</a>
                                                        @endif
                                                    </div>
                                                @else
                                                    <div class="tt-mobile-session-row">
                                                        <svg width="13" height="13" fill="none" stroke="#64748b" viewBox="0 0 24 24">
                                                            <rect x="3" y="3" width="18" height="18" rx="2" stroke-width="2"/>
                                                            <path d="M3 9h18M9 21V9" stroke-width="2"/>
                                                        </svg>
                                                        {{ $emploi->salle->name ?? '—' }}
                                                    </div>
                                                @endif

                                                <div class="tt-mobile-session-row" style="color:#94a3b8; font-size:10px;">
                                                    <svg width="12" height="12" fill="none" stroke="#94a3b8" viewBox="0 0 24 24">
                                                        <circle cx="12" cy="12" r="9" stroke-width="2"/>
                                                        <path stroke-width="2" stroke-linecap="round" d="M12 7v5l3 3"/>
                                                    </svg>
                                                    {{ $emploi->date_debut->format('H:i') }} → {{ $emploi->date_fin->format('H:i') }}
                                                </div>
                                            </div>
                                        </div>

                                        {{-- ── MOBILE FOOTER — icon-only ── --}}
                                        <div class="tt-mobile-session-footer">
                                            {{-- Voir --}}
                                            <a href="{{ route('seances.show', $emploi) }}"
                                               class="tt-mobile-foot-btn"
                                               title="Voir le détail">
                                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                    <path stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                                </svg>
                                            </a>

                                            @if(in_array(Auth::user()->role, ['admin','gestionnaire','formateur']))
                                                @if(!$isDraft)
                                                    {{-- Présence --}}
                                                    <a href="{{ route('seances.show', $emploi) }}#presence"
                                                       class="tt-mobile-foot-btn pres"
                                                       title="Feuille de présence">
                                                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                                                  d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                                                        </svg>
                                                    </a>
                                                @else
                                                    <span class="tt-mobile-foot-btn" style="opacity:.3; cursor:not-allowed;" title="Disponible après publication">
                                                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                                                  d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                                        </svg>
                                                    </span>
                                                @endif
                                            @endif

                                            {{-- Cours --}}
                                            <a href="{{ route('seances.show', $emploi) }}#classroom"
                                               class="tt-mobile-foot-btn cls"
                                               title="Ressources du cours">
                                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-width="2" stroke-linecap="round"
                                                          d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                                                </svg>
                                            </a>
                                        </div>
                                    </div>
                                @endif
                            @endforeach
                        @endif
                    </div>
                @endforeach

            @empty
                <div class="tt-mobile-empty">Aucun groupe pour cette année et cette promotion.</div>
            @endforelse
        </div>
    @endforeach
</div>

{{-- ════ DESKTOP GRID TABLE ════ --}}
<div class="tt-scroll" id="tt-scroll-container">
<table class="tt-table">
    <thead>
    <tr>
        <th class="tt-sticky-head"
            style="padding:12px 14px; border-right:1px solid #cbd5e1; border-bottom:1px solid #e9edf2;
                   text-align:left; min-width:120px; background:#f8fafc;">
            <span style="font-size:9px; font-weight:800; color:#94a3b8; letter-spacing:2px; text-transform:uppercase;">Groupe</span>
        </th>
        @foreach($dayDates as $dayNum => $date)
            @php $isToday = $date->isToday(); $isLastDay = $dayNum === 6; @endphp
            <th colspan="4" class="tt-day-head {{ $isToday ? 'today' : '' }}"
                style="border-right:{{ $isLastDay ? 'none' : '3px solid #cbd5e1' }};
                       border-bottom:{{ $isToday ? '2px solid '.$accentColor : '1px solid #e9edf2' }};">
                <div class="tt-day-name">{{ $date->translatedFormat('D') }}</div>
                <div class="tt-day-date">{{ $date->format('d') }}</div>
                <div style="font-size:9px; {{ $isToday ? 'color:rgba(255,255,255,0.85)' : 'color:#64748b' }};">{{ $date->translatedFormat('M') }}</div>
                @if($isToday)<div class="tt-today-pill">AUJOURD'HUI</div>@endif
            </th>
        @endforeach
    </tr>
    <tr>
        <th class="tt-sticky-head" style="border-right:1px solid #cbd5e1; border-bottom:2px solid #e2e8f0; background:#f8fafc;"></th>
        @foreach($dayDates as $dayNum => $date)
            @php $isLastDay = $dayNum === 6; @endphp
            @foreach(EmploiDuTempsController::SEANCES as $sNum => $seance)
                @php $isLastS = $sNum === 4; @endphp
                <th class="tt-seance-head"
                    style="border-right:{{ $isLastS ? ($isLastDay ? 'none' : '3px solid #cbd5e1') : '1px solid #e9edf2' }}; border-bottom:2px solid #e2e8f0;"
                    title="{{ $seance['start'] }}–{{ $seance['end'] }}">
                    <div class="tt-seance-label" style="color:{{ $accentColor }};">{{ $seance['label'] }}</div>
                    <div class="tt-seance-time">{{ $seance['start'] }}</div>
                </th>
            @endforeach
        @endforeach
    </tr>
    </thead>

    <tbody>
    @forelse($groupesByFiliere as $filiereId => $groupes)
        @php $filiere = $groupes->first()->filiere; @endphp

        <tr class="tt-filiere-row">
            <td class="tt-filiere-sticky-cell">
                <div style="display:flex; align-items:center; gap:8px;">
                    <div style="width:6px; height:6px; border-radius:50%; background:{{ $accentColor }}; flex-shrink:0;"></div>
                    <span style="font-size:10px; font-weight:800; letter-spacing:1.5px; text-transform:uppercase; color:{{ $p['text'] }};">
                        {{ $filiere->name ?? 'Filière' }}
                    </span>
                </div>
            </td>
            <td colspan="24" style="background:{{ $p['light'] }}; padding:0; border-top:1px solid {{ $accentColor }}30; border-bottom:1px solid {{ $accentColor }}30;"></td>
        </tr>

        @foreach($groupes as $groupe)
        <tr class="tt-body-row">
            <td class="tt-sticky-cell tt-group-cell" style="border-right:1px solid #e2e8f0;">
                <div style="display:flex; align-items:center; gap:8px;">
                    <div style="width:3px; height:32px; border-radius:99px; background:{{ $accentColor }}; flex-shrink:0; opacity:0.6;"></div>
                    <div>
                        <div class="tt-group-name">{{ $groupe->name ?? 'G'.$groupe->id }}</div>
                        <div class="tt-group-sub">{{ $groupe->option->titre ?? $filiere->name ?? '' }}</div>
                    </div>
                </div>
            </td>

            @foreach($dayDates as $dayNum => $date)
                @php $isPastDay = $date->copy()->startOfDay()->lt($today); @endphp
                @foreach(EmploiDuTempsController::SEANCES as $sNum => $seance)
                    @php
                        $cell      = $grid[$groupe->id][$dayNum][$sNum] ?? ['type' => 'empty'];
                        $isLastS   = $sNum === 4;
                        $isLastDay = $dayNum === 6;
                        $cellBorder = $isLastS ? ($isLastDay ? '' : 'border-right:3px solid #cbd5e1;') : 'border-right:1px solid #e2e8f0;';
                    @endphp

                    @if($cell['type'] === 'skip')
                        {{-- merged --}}
                    @elseif($cell['type'] === 'session')
                        @php
                            $emploi   = $cell['emploi'];
                            $colspan  = $cell['colspan'];
                            $spanLbl  = EmploiDuTempsController::spanLabel($sNum, $colspan);
                            $totalH   = EmploiDuTempsController::totalHours($sNum, $colspan);
                            $isRemote = ($emploi->mode ?? 'presentiel') === 'distance';
                            $isDraft  = $emploi->statut === 'brouillon';

                            $sessionRemplacant = $emploi->id_user_remplacant ? $emploi->remplacant : null;
                            $isFuture          = $emploi->date_debut->isFuture();
                            $moduleRemplacant  = (!$sessionRemplacant && $isFuture && $emploi->module?->id_user_remplacant)
                                                ? $emploi->module->remplacant : null;

                            $hasRemplacant    = $sessionRemplacant || $moduleRemplacant;
                            $activeRemplacant = $sessionRemplacant ?? $moduleRemplacant;

                            if ($isDraft) {
                                $cardClass = 'card-brouillon';
                            } elseif ($isRemote) {
                                $cardClass = 'card-distance';
                            } else {
                                $rawColor = EmploiDuTempsController::cardColor($emploi->id_module);
                                $colorIdx = match(true) {
                                    in_array($rawColor, ['blue', 'violet']) => 1,
                                    in_array($rawColor, ['green', 'teal'])  => 2,
                                    in_array($rawColor, ['amber', 'red'])   => 3,
                                    default                                 => 4,
                                };
                                $cardClass = 'card-role-'.$colorIdx;
                            }

                            $lastOfSpan    = $sNum + $colspan - 1;
                            $isLastSOfSpan = ($lastOfSpan % 4 === 0);
                            $spanBorder    = $isLastSOfSpan ? ($isLastDay ? '' : 'border-right:3px solid #94a3b8;') : 'border-right:1px solid #e2e8f0;';

                            $progDone  = 0;
                            $progTotal = 0;
                            $progPct   = 0;
                            if ($emploi->id_module && $emploi->module && $emploi->module->nbr_heure > 0) {
                                $progDone  = $moduleProgress[$emploi->id_groupe][$emploi->id_module] ?? 0;
                                $progTotal = $emploi->module->nbr_heure;
                                $progPct   = min(100, round(($progDone / $progTotal) * 100));
                            }
                        @endphp
                        <td class="tt-session-td" colspan="{{ $colspan }}" style="{{ $spanBorder }}">
                            <div class="tt-card {{ $cardClass }}">

                                {{-- ════ ACTION BUTTONS — icon-only, appear on hover ════ --}}
                                @if($canEdit || $canDelete || $canLien || $isGestionnaire || $canReport)
                                <div class="tt-actions">

                                    {{-- Edit --}}
                                    @if($canEdit)
                                    <button type="button"
                                            class="tt-action-btn btn-edit"
                                            onclick="openEditModal({{ $emploi->id }})"
                                            title="Modifier la séance">
                                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                        </svg>
                                    </button>
                                    @endif

                                    {{-- Report --}}
                                    @if($canReport && $emploi->id_user === Auth::user()->id)
                                        @php $alreadyPending = in_array($emploi->id, $pendingReportIds); @endphp
                                        @if($alreadyPending)
                                            <span class="tt-action-btn btn-pending" title="Demande de report déjà en attente">
                                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <circle cx="12" cy="12" r="9" stroke-width="2"/>
                                                    <path stroke-width="2" stroke-linecap="round" d="M12 7v5l3 3"/>
                                                </svg>
                                            </span>
                                        @else
                                            <button type="button"
                                                    class="tt-action-btn btn-report"
                                                    title="Demander un report"
                                                    onclick="openReportModal(
                                                        {{ $emploi->id }},
                                                        '{{ addslashes(($emploi->groupe->name ?? 'Groupe').' — '.($emploi->module->name ?? 'Module')) }}',
                                                        '{{ $emploi->date_debut->translatedFormat('l d M Y') }}',
                                                        '{{ $emploi->date_debut->format('Y-m-d') }}',
                                                        '{{ EmploiDuTempsController::spanLabel($sNum, $colspan) }}',
                                                        '{{ $emploi->date_debut->format('H:i') }}',
                                                        '{{ $emploi->date_fin->format('H:i') }}'
                                                    )">
                                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                          d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                                                </svg>
                                            </button>
                                        @endif
                                    @endif

                                    {{-- Lien distance --}}
                                    @if($canLien && $emploi->mode === 'distance' && (Auth::user()->role === 'formateur' ? $emploi->id_user === Auth::user()->id : true))
                                    <button type="button"
                                            class="tt-action-btn btn-lien"
                                            title="Mettre à jour le lien de réunion"
                                            onclick="openLienModal(
                                                {{ $emploi->id }},
                                                '{{ addslashes($emploi->lien_distance ?? '') }}',
                                                '{{ addslashes(($emploi->groupe->name ?? 'Groupe').' — '.($emploi->module->name ?? 'Module')) }}',
                                                '{{ $emploi->date_debut->translatedFormat('l d M') }} · {{ EmploiDuTempsController::spanLabel($sNum, $colspan) }} · {{ $emploi->date_debut->format('H:i') }} → {{ $emploi->date_fin->format('H:i') }}'
                                            )">
                                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M15 10l4.553-2.276A1 1 0 0121 8.723v6.554a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                                        </svg>
                                    </button>
                                    @endif

                                    {{-- Separator before delete --}}
                                    @if($canDelete && ($canEdit || $canReport || $canLien))
                                        <div class="tt-actions-sep"></div>
                                    @endif

                                    {{-- Delete --}}
                                    @if($canDelete)
                                    <button type="button"
                                            class="tt-action-btn btn-del"
                                            title="Supprimer la séance"
                                            onclick="openDeleteModal(
                                                '{{ route('emplois.destroy', $emploi) }}',
                                                '{{ addslashes($emploi->groupe->name ?? 'Groupe') }}',
                                                '{{ addslashes($emploi->module->name ?? 'Module') }}',
                                                '{{ $emploi->date_debut->translatedFormat('l d M') }}',
                                                '{{ EmploiDuTempsController::spanLabel($sNum, $colspan) }}',
                                                '{{ $emploi->date_debut->format('H:i') }}',
                                                '{{ $emploi->date_fin->format('H:i') }}',
                                                '{{ addslashes($emploi->salle->name ?? ($isRemote ? 'À distance' : '—')) }}'
                                            )">
                                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                        </svg>
                                    </button>
                                    @endif

                                </div>
                                @endif

                                {{-- ════ CARD BODY ════ --}}
                                <div class="tt-card-body">
                                    @if($isDraft)<div class="draft-badge">Brouillon</div>@endif
                                    <div class="tt-card-header">
                                        <div class="tt-card-module">{{ $emploi->module->name ?? 'Module' }}</div>
                                        <div class="tt-card-time">{{ $spanLbl }} · {{ $totalH }}h</div>
                                    </div>
                                    <div class="tt-card-meta">
                                        @if($hasRemplacant)
                                            <div class="tt-card-row" style="flex-direction:column; align-items:flex-start; gap:2px;">
                                                <div style="display:flex;align-items:center;gap:5px;">
                                                    <div class="tt-card-dot" style="background:#94a3b8;"></div>
                                                    <span style="font-size:9px; color:#94a3b8; text-decoration:line-through;">{{ $emploi->gestionnaire->name ?? '—' }}</span>
                                                </div>
                                                <div style="display:flex;align-items:center;gap:5px;flex-wrap:wrap;">
                                                    <div class="tt-card-dot" style="background:#7c3aed;"></div>
                                                    <span style="font-size:10px; color:#5b21b6; font-weight:700;">{{ $activeRemplacant->name }}</span>
                                                    @if($moduleRemplacant && !$sessionRemplacant)
                                                        <span class="remplacant-badge-module">Module</span>
                                                    @else
                                                        <span class="remplacant-badge">Remplaçant</span>
                                                    @endif
                                                </div>
                                            </div>
                                        @else
                                            <div class="tt-card-row">
                                                <div class="tt-card-icon" style="{{ $isRemote ? 'background:#fef3c7;' : '' }}">
                                                    <svg fill="none" stroke="{{ $isRemote ? '#b45309' : $accentColor }}" viewBox="0 0 24 24">
                                                        <circle cx="12" cy="7" r="4" stroke-width="2"/>
                                                        <path stroke-width="2" d="M4 21v-1a8 8 0 0116 0v1"/>
                                                    </svg>
                                                </div>
                                                <span style="color:{{ $isRemote ? '#92400e' : '' }}">{{ $emploi->gestionnaire->name ?? '—' }}</span>
                                            </div>
                                        @endif

                                        @if($isRemote && $emploi->lien_distance)
                                            <div class="tt-card-row">
                                                <div class="tt-card-icon" style="background:#fef3c7;">
                                                    <svg fill="none" stroke="#f59e0b" viewBox="0 0 24 24">
                                                        <path stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.723v6.554a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                                                    </svg>
                                                </div>
                                                <a href="{{ $emploi->lien_distance }}" target="_blank"
                                                   style="font-size:9px; color:#b45309; text-decoration:underline; font-weight:600; overflow:hidden; text-overflow:ellipsis; white-space:nowrap; max-width:100%;">
                                                    Rejoindre la réunion
                                                </a>
                                            </div>
                                        @elseif(!$isRemote)
                                            <div class="tt-card-row">
                                                <div class="tt-card-icon">
                                                    <svg fill="none" stroke="#64748b" viewBox="0 0 24 24">
                                                        <rect x="3" y="3" width="18" height="18" rx="2" stroke-width="2"/>
                                                        <path d="M3 9h18M9 21V9" stroke-width="2"/>
                                                    </svg>
                                                </div>
                                                {{ $emploi->salle->name ?? '—' }}
                                            </div>
                                        @endif

                                        <div class="tt-card-row" style="color:#94a3b8; font-size:9px;">
                                            {{ $emploi->date_debut->format('H:i') }} → {{ $emploi->date_fin->format('H:i') }}
                                        </div>

                                        @if($progTotal > 0 && $emploi->date_debut->isPast())
                                            @php $isOngoing = $emploi->date_debut->isPast() && $emploi->date_fin->isFuture(); @endphp
                                            <div class="card-progress">
                                                <div class="card-progress-track">
                                                    <div class="card-progress-fill"
                                                         style="width:{{ $progPct }}%;
                                                                background:{{ $progPct >= 100 ? '#22c55e' : ($isRemote ? '#f59e0b' : $accentColor) }};
                                                                {{ $isOngoing ? 'animation: pulse 1.5s ease-in-out infinite;' : '' }}">
                                                    </div>
                                                </div>
                                                <div class="card-progress-meta">
                                                    <span style="{{ $isOngoing ? 'color:#f59e0b; font-weight:700;' : '' }}">
                                                        {{ $isOngoing ? '⏳ En cours' : number_format($progDone, 1).'h / '.$progTotal.'h' }}
                                                    </span>
                                                    <span style="{{ $progPct >= 100 ? 'color:#22c55e; font-weight:700;' : '' }}">{{ $progPct }}%</span>
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                </div>

                                {{-- ════ CARD FOOTER — icon-only tabs ════ --}}
                                <div class="tt-card-footer">

                                    {{-- Voir --}}
                                    <a href="{{ route('seances.show', $emploi) }}"
                                       class="tt-foot-btn"
                                       title="Voir le détail">
                                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                                  d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            <path stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                                  d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                        </svg>
                                    </a>

                                    {{-- Présence --}}
                                    @if(in_array(Auth::user()->role, ['admin','gestionnaire','formateur']))
                                        @if(!$isDraft)
                                            <a href="{{ route('seances.show', $emploi) }}#presence"
                                               class="tt-foot-btn tt-foot-btn-pres"
                                               title="Feuille de présence">
                                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                                          d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                                                </svg>
                                            </a>
                                        @else
                                            <span class="tt-foot-btn" style="opacity:.3; cursor:not-allowed;" title="Disponible après publication">
                                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                                          d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                                </svg>
                                            </span>
                                        @endif
                                    @else
                                        <span></span>
                                    @endif

                                    {{-- Cours --}}
                                    <a href="{{ route('seances.show', $emploi) }}#classroom"
                                       class="tt-foot-btn tt-foot-btn-cls"
                                       title="Ressources du cours">
                                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-width="2" stroke-linecap="round"
                                                  d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                                        </svg>
                                    </a>

                                </div>

                            </div>
                        </td>

                    @else
                        <td class="tt-empty-td" style="{{ $cellBorder }}">
                            @if($canCreate && !$isPastDay)
                                <button class="tt-add-btn"
                                        onclick="openModalWithSlot({{ $dayNum }}, {{ $sNum }}, '{{ $date->toDateString() }}', {{ $groupe->id }})">
                                    +
                                </button>
                            @elseif($canCreate && $isPastDay)
                                <div class="tt-past-cell" title="Impossible de créer une séance sur une date passée">
                                    <svg width="14" height="14" fill="none" stroke="#cbd5e1" viewBox="0 0 24 24">
                                        <rect x="3" y="11" width="18" height="11" rx="2" stroke-width="2"/>
                                        <path stroke-width="2" d="M7 11V7a5 5 0 0110 0v4"/>
                                    </svg>
                                </div>
                            @else
                                <div style="min-height:72px;"></div>
                            @endif
                        </td>
                    @endif

                @endforeach
            @endforeach
        </tr>
        @endforeach

    @empty
        <tr>
            <td colspan="25" style="padding:48px; text-align:center; font-size:13px; color:#64748b;">
                Aucun groupe pour cette année et cette promotion.
            </td>
        </tr>
    @endforelse
    </tbody>
</table>
</div>{{-- .tt-scroll --}}

{{-- ════ LEGEND ════ --}}
<div class="tt-legend" style="display:flex; flex-wrap:wrap; gap:12px; align-items:center; margin-top:12px;
            padding:10px 14px; background:white; border-radius:12px; border:1px solid #e2e8f0;">
    <span style="font-size:9px; font-weight:800; color:{{ $accentColor }}; letter-spacing:2px; text-transform:uppercase;">Créneaux</span>
    @foreach(EmploiDuTempsController::SEANCES as $sNum => $s)
        <span style="display:flex; align-items:center; gap:5px; font-size:10px; color:#475569;">
            <span style="font-size:9px; font-weight:800; padding:2px 8px; border-radius:6px;
                         background:{{ $p['light'] }}; color:{{ $p['text'] }};">{{ $s['label'] }}</span>
            {{ $s['start'] }}–{{ $s['end'] }}
            <span style="color:#94a3b8;">({{ $s['hours'] }}h)</span>
        </span>
    @endforeach
    <span style="margin-left:auto; display:flex; align-items:center; gap:10px; font-size:9px;">
        <span style="display:flex; align-items:center; gap:4px; color:#475569;">
            <span style="width:10px; height:10px; border-radius:2px; background:{{ $p['light'] }}; border:1.5px solid {{ $p['medium'] }}; display:inline-block;"></span>
            Présentiel
        </span>
        <span style="display:flex; align-items:center; gap:4px; color:#92400e;">
            <span style="width:10px; height:10px; border-radius:2px; background:#fef3c7; border:1.5px solid #f59e0b; display:inline-block;"></span>
            À distance
        </span>
        <span style="display:flex; align-items:center; gap:4px; color:#5b21b6;">
            <span style="width:10px; height:10px; border-radius:2px; background:#f5f3ff; border:1.5px solid #7c3aed; display:inline-block;"></span>
            Remplaçant séance
        </span>
        <span style="display:flex; align-items:center; gap:4px; color:#c2410c;">
            <span style="width:10px; height:10px; border-radius:2px; background:#fff7ed; border:1.5px solid #fb923c; display:inline-block;"></span>
            Remplaçant module
        </span>
    </span>
</div>

</div>{{-- .tt-wrap --}}

{{-- ════════════════════════════════════════════════════════════
     MODALS  (unchanged)
════════════════════════════════════════════════════════════ --}}

{{-- ── DELETE ── --}}
<div id="delete-modal" style="display:none; position:fixed; inset:0; z-index:60;
     background:rgba(15,23,42,0.5); backdrop-filter:blur(4px);
     align-items:center; justify-content:center;"
     onclick="if(event.target===this)closeDeleteModal()">
    <div style="background:white; border-radius:20px; width:100%; max-width:420px;
                margin:16px; padding:24px; box-shadow:0 24px 60px rgba(0,0,0,0.18);">
        <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:14px; padding-bottom:14px; border-bottom:2px solid #dc2626;">
            <div style="display:flex; align-items:center; gap:10px;">
                <div style="width:40px; height:40px; border-radius:12px; background:#fee2e2; display:flex; align-items:center; justify-content:center; flex-shrink:0;">
                    <svg width="18" height="18" fill="none" stroke="#dc2626" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                    </svg>
                </div>
                <div>
                    <div style="font-size:14px; font-weight:800; color:#1e293b;">Supprimer la séance ?</div>
                    <div style="font-size:10px; color:#64748b; margin-top:1px;">Cette action est irréversible</div>
                </div>
            </div>
            <button onclick="closeDeleteModal()" style="width:28px;height:28px;border-radius:8px;border:none;background:#f1f5f9;color:#64748b;font-size:16px;cursor:pointer;display:flex;align-items:center;justify-content:center;">×</button>
        </div>
        <div id="delete-session-info" style="display:flex; align-items:center; gap:10px; padding:10px 12px; border-radius:10px; background:#f8fafc; border:1px solid #e2e8f0; margin-bottom:14px;">
            <div style="width:8px;height:8px;border-radius:50%;background:#dc2626;flex-shrink:0;"></div>
            <div>
                <div id="delete-session-label" style="font-size:11px; font-weight:700; color:#1e293b;"></div>
                <div id="delete-session-meta"  style="font-size:9px; color:#64748b; margin-top:1px;"></div>
            </div>
        </div>
        <div style="font-size:12px; color:#9f1239; line-height:1.6; margin-bottom:18px; padding:12px 14px; border-radius:12px; background:#fff1f2; border:1px solid #fecdd3;">
            La suppression retirera définitivement ce créneau de l'emploi du temps.
        </div>
        <div style="display:flex; gap:10px;">
            <button onclick="closeDeleteModal()" style="flex:1; height:44px; border-radius:12px; border:1.5px solid #e2e8f0; background:white; font-size:13px; font-weight:600; color:#64748b; cursor:pointer;">Annuler</button>
            <form id="delete-form" method="POST" style="flex:1;">
                @csrf @method('DELETE')
                <button type="submit" style="width:100%; height:44px; border-radius:12px; border:none; background:#dc2626; font-size:13px; font-weight:700; color:white; cursor:pointer; box-shadow:0 4px 12px rgba(220,38,38,0.3);">
                    Supprimer
                </button>
            </form>
        </div>
    </div>
</div>

{{-- ── LIEN DISTANCE ── --}}
<div id="lien-modal" style="display:none; position:fixed; inset:0; z-index:60;
     background:rgba(15,23,42,0.5); backdrop-filter:blur(4px);
     align-items:center; justify-content:center;"
     onclick="if(event.target===this)closeLienModal()">
    <div style="background:white; border-radius:20px; width:100%; max-width:420px; margin:16px; padding:24px; box-shadow:0 24px 60px rgba(0,0,0,0.18);">
        <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:14px; padding-bottom:14px; border-bottom:2px solid #f59e0b;">
            <div style="display:flex; align-items:center; gap:10px;">
                <div style="width:40px; height:40px; border-radius:12px; background:#fef3c7; display:flex; align-items:center; justify-content:center; flex-shrink:0;">
                    <svg width="18" height="18" fill="none" stroke="#f59e0b" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.723v6.554a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                </div>
                <div>
                    <div style="font-size:14px; font-weight:800; color:#1e293b;">Mettre à jour le lien</div>
                    <div style="font-size:10px; color:#64748b; margin-top:1px;">Séance à distance</div>
                </div>
            </div>
            <button onclick="closeLienModal()" style="width:28px;height:28px;border-radius:8px;border:none;background:#f1f5f9;color:#64748b;font-size:16px;cursor:pointer;display:flex;align-items:center;justify-content:center;">×</button>
        </div>
        <div style="display:flex; align-items:center; gap:10px; padding:10px 12px; border-radius:10px; background:#f8fafc; border:1px solid #e2e8f0; margin-bottom:14px;">
            <div style="width:8px;height:8px;border-radius:50%;background:#f59e0b;flex-shrink:0;"></div>
            <div>
                <span id="lien-session-label" style="font-size:11px; font-weight:700; color:#1e293b;"></span>
                <div id="lien-session-meta" style="font-size:9px; color:#64748b; margin-top:1px;"></div>
            </div>
        </div>
        <div style="margin-bottom:18px;">
            <label style="display:block; font-size:9px; font-weight:800; color:#94a3b8; letter-spacing:1.5px; text-transform:uppercase; margin-bottom:6px;">Lien Teams / Zoom…</label>
            <input type="text" id="lien-input" placeholder="https://teams.microsoft.com/..."
                   style="width:100%; height:42px; padding:0 12px; border-radius:10px; border:1.5px solid #e2e8f0; background:#f8fafc; font-size:13px; color:#1e293b; outline:none; box-sizing:border-box;"
                   onfocus="this.style.borderColor='#f59e0b'; this.style.background='white';"
                   onblur="this.style.borderColor='#e2e8f0'; this.style.background='#f8fafc';">
        </div>
        <form id="lien-form" method="POST" style="display:contents;">
            @csrf @method('PATCH')
            <input type="hidden" name="lien_distance" id="lien-hidden">
            <div style="display:flex; gap:10px;">
                <button type="button" onclick="closeLienModal()" style="flex:1; height:44px; border-radius:12px; border:1.5px solid #e2e8f0; background:white; font-size:13px; font-weight:600; color:#64748b; cursor:pointer;">Annuler</button>
                <button type="button" onclick="submitLien()" style="flex:1; height:44px; border-radius:12px; border:none; background:#f59e0b; font-size:13px; font-weight:700; color:white; cursor:pointer; box-shadow:0 4px 12px rgba(245,158,11,0.35);">
                    Enregistrer
                </button>
            </div>
        </form>
    </div>
</div>

{{-- ── REPORT ── --}}
@if($canReport)
<div id="report-modal" style="display:none; position:fixed; inset:0; z-index:60;
     background:rgba(15,23,42,0.5); backdrop-filter:blur(4px);
     align-items:center; justify-content:center;"
     onclick="if(event.target===this)closeReportModal()">
    <div style="background:white; border-radius:20px; width:100%; max-width:440px;
                margin:16px; padding:24px; box-shadow:0 24px 60px rgba(0,0,0,0.18);">
        <div style="display:flex; align-items:center; justify-content:space-between;
                    margin-bottom:16px; padding-bottom:14px; border-bottom:2px solid #7c3aed;">
            <div style="display:flex; align-items:center; gap:10px;">
                <div style="width:42px; height:42px; border-radius:12px; background:#f5f3ff;
                            display:flex; align-items:center; justify-content:center; flex-shrink:0;">
                    <svg width="20" height="20" fill="none" stroke="#7c3aed" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                    </svg>
                </div>
                <div>
                    <div style="font-size:14px; font-weight:800; color:#1e293b;">Demander un report</div>
                    <div id="report-session-label" style="font-size:10px; color:#64748b; margin-top:1px;"></div>
                </div>
            </div>
            <button onclick="closeReportModal()"
                    style="width:28px;height:28px;border-radius:8px;border:none;background:#f1f5f9;
                           color:#64748b;font-size:16px;cursor:pointer;display:flex;align-items:center;justify-content:center;">×</button>
        </div>
        <div style="padding:10px 14px; border-radius:10px; background:#fef3c7;
                    border:1px solid #fde68a; margin-bottom:16px;
                    display:flex; align-items:center; gap:8px; font-size:11px; color:#92400e;">
            📅 <span>Séance actuelle : <strong id="report-current-date"></strong> · <span id="report-current-time"></span></span>
        </div>
        <form id="report-form" method="POST" action="{{ route('reportations.store') }}"
              style="display:flex; flex-direction:column; gap:14px;">
            @csrf
            <input type="hidden" name="id_emplois_du_temps" id="report-emploi-id">
            <div>
                <label style="display:block; font-size:9px; font-weight:800; color:#94a3b8;
                              letter-spacing:1.5px; text-transform:uppercase; margin-bottom:6px;">
                    Raison du report <span style="color:#ef4444;">*</span>
                </label>
                <textarea name="raison" id="report-raison" required minlength="10" maxlength="1000" rows="4"
                          placeholder="Expliquez pourquoi cette séance doit être reportée…"
                          style="width:100%; padding:10px 12px; border-radius:10px; border:1.5px solid #e2e8f0;
                                 background:#f8fafc; font-size:13px; color:#1e293b; outline:none;
                                 resize:vertical; box-sizing:border-box; font-family:inherit; line-height:1.5;"
                          onfocus="this.style.borderColor='#7c3aed';this.style.background='white';"
                          onblur="this.style.borderColor='#e2e8f0';this.style.background='#f8fafc';"></textarea>
                <div style="display:flex; justify-content:space-between; margin-top:3px;">
                    <p style="font-size:9px; color:#94a3b8; margin:0;">Minimum 10 caractères</p>
                    <span id="report-raison-count" style="font-size:9px; color:#94a3b8;">0 / 1000</span>
                </div>
            </div>
            <div style="padding:10px 14px; border-radius:10px; background:#f5f3ff; border:1px solid #ddd6fe;
                        font-size:11px; color:#5b21b6; display:flex; align-items:flex-start; gap:8px;">
                <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="flex-shrink:0;margin-top:1px;">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0116 0z"/>
                </svg>
                L'administration choisira la nouvelle date après validation de votre demande.
            </div>
            <div style="display:flex; gap:10px; margin-top:4px;">
                <button type="button" onclick="closeReportModal()"
                        style="flex:1; height:44px; border-radius:12px; border:1.5px solid #e2e8f0;
                               background:white; font-size:13px; font-weight:600; color:#64748b; cursor:pointer;">
                    Annuler
                </button>
                <button type="submit"
                        style="flex:2; height:44px; border-radius:12px; border:none;
                               background:#7c3aed; font-size:13px; font-weight:700; color:white; cursor:pointer;
                               box-shadow:0 4px 12px rgba(124,58,237,0.35);
                               display:flex; align-items:center; justify-content:center; gap:6px;">
                    <svg width="13" height="13" fill="none" stroke="white" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                    </svg>
                    Envoyer la demande
                </button>
            </div>
        </form>
    </div>
</div>
@endif

{{-- ── CREATE / EDIT ── --}}
@if($canCreate || $canEdit)
<div id="emploi-modal" class="tt-modal-overlay" onclick="if(event.target===this)closeModal()">
    <div class="tt-modal-box">
        <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:18px; padding-bottom:14px; border-bottom:2px solid {{ $accentColor }};">
            <div>
                <h3 id="modal-title" style="font-size:14px; font-weight:800; color:#1e293b; margin:0;">Nouvelle séance</h3>
                <div id="modal-slot-info" style="font-size:10px; color:#64748b; margin-top:2px;"></div>
            </div>
            <button onclick="closeModal()" style="width:28px;height:28px;border-radius:8px;border:none;background:#f1f5f9;color:#64748b;font-size:16px;cursor:pointer;display:flex;align-items:center;justify-content:center;">×</button>
        </div>

        <form id="emploi-form" method="POST" action="{{ route('emplois.store') }}"
              style="display:flex; flex-direction:column; gap:14px;">
            @csrf
            <input type="hidden" name="_method"    id="form-method"   value="POST">
            <input type="hidden" name="id_groupe"  id="m-groupe-hidden">
            <input type="hidden" name="date_debut" id="m-debut">
            <input type="hidden" name="date_fin"   id="m-fin">

            <div id="m-groupe-row" style="display:none;">
                <label class="tt-modal-label">Groupe</label>
                <select id="m-groupe-select" class="tt-modal-input"
                        onchange="document.getElementById('m-groupe-hidden').value=this.value; loadAvailable();">
                    <option value="">— Sélectionner —</option>
                    @foreach($allGroupes as $g)
                        <option value="{{ $g->id }}" data-filiere="{{ $g->id_filiere }}">{{ $g->name ?? 'G'.$g->id }} – {{ $g->filiere->name ?? '' }}</option>
                    @endforeach
                </select>
            </div>

            @if($canCreate || $canEdit)
            <div>
                <label class="tt-modal-label">Mode de séance</label>
                <div class="mode-toggle">
                    <button type="button" id="btn-pres" class="mode-btn active-pres" onclick="setMode('presentiel')">
                        <svg style="width:13px;height:13px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                        Présentiel
                    </button>
                    <button type="button" id="btn-dist" class="mode-btn" onclick="setMode('distance')" style="border-left:1px solid #e2e8f0;">
                        <svg style="width:13px;height:13px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.723v6.554a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                        À distance
                    </button>
                </div>
                <input type="hidden" name="mode" id="m-mode" value="presentiel">
            </div>

            <div>
                <label class="tt-modal-label">Durée</label>
                <select id="m-dur" onchange="onDurationChange()" class="tt-modal-input">
                    <option value="1">1 séance (2.5h)</option>
                    <option value="2">2 séances (5h)</option>
                    <option value="3">3 séances (7.5h)</option>
                    <option value="4">Journée (10h)</option>
                </select>
            </div>

            <div style="border-radius:12px; padding:12px; background:{{ $p['light'] }}; border:1px solid {{ $accentColor }}20;">
                <div style="display:flex; justify-content:space-between; font-size:9px; color:#475569; margin-bottom:8px;">
                    <span id="prev-start" style="font-weight:700;">08:30</span>
                    <span id="prev-label" style="font-weight:800; color:{{ $accentColor }};">2.5h · 1 séance</span>
                    <span id="prev-end" style="font-weight:700;">11:00</span>
                </div>
                <div style="display:flex; gap:4px;">
                    @foreach(EmploiDuTempsController::SEANCES as $sNum => $s)
                        <div id="prev-bar-{{ $sNum }}" style="flex:1; height:8px; border-radius:4px; background:#e2e8f0; transition:background 0.2s;"></div>
                    @endforeach
                </div>
                <div style="display:flex; gap:4px; margin-top:4px;">
                    @foreach(EmploiDuTempsController::SEANCES as $sNum => $s)
                        <div style="flex:1; text-align:center; font-size:8px; color:#64748b; font-weight:700;">{{ $s['label'] }}</div>
                    @endforeach
                </div>
            </div>

            @if($canSelectModule)
            <div id="module-row">
                <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:6px;">
                    <label class="tt-modal-label" style="margin:0;">
                        Module
                        @if($isGestionnaire)
                            <span style="color:#ef4444; font-size:10px;">*</span>
                        @else
                            <span style="font-size:8px; color:#94a3b8; font-weight:400; text-transform:none; letter-spacing:0;">(optionnel)</span>
                        @endif
                    </label>
                    <span id="avail-loading-module" style="font-size:9px; color:#64748b; display:none;">chargement…</span>
                </div>
                <select name="id_module" id="m-module"
                        {{ $isGestionnaire ? 'required' : '' }}
                        onchange="onModuleChange()"
                        class="tt-modal-input">
                    <option value="">— Sélectionner un module —</option>
                </select>
                <div id="module-progress-wrap" style="display:none; margin-top:8px; padding:8px 10px; background:#f8fafc; border-radius:8px; border:1px solid #e2e8f0;">
                    <div style="display:flex; justify-content:space-between; font-size:9px; color:#475569; margin-bottom:5px;">
                        <span style="font-weight:700;">Progression du module</span>
                        <span id="module-progress-pct" style="font-weight:800; color:{{ $accentColor }};">0%</span>
                    </div>
                    <div style="height:5px; background:#e2e8f0; border-radius:99px; overflow:hidden;">
                        <div id="module-progress-bar" style="height:100%; width:0%; background:{{ $accentColor }}; border-radius:99px; transition:width 0.4s;"></div>
                    </div>
                    <div style="font-size:8px; color:#64748b; margin-top:4px; display:flex; justify-content:space-between;">
                        <span id="module-progress-label">0h planifiées</span>
                        <span id="module-progress-total">0h total</span>
                    </div>
                </div>
            </div>
            @endif

            <div>
                <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:6px;">
                    <label class="tt-modal-label" style="margin:0;">Formateur</label>
                    <span id="avail-loading-user"  style="font-size:9px; color:#64748b; display:none;">chargement…</span>
                    <span id="avail-count-user"    style="font-size:9px; color:{{ $accentColor }}; font-weight:700; display:none;"></span>
                </div>
                <select name="id_user" id="m-user" required class="tt-modal-input">
                    <option value="">— Sélectionner —</option>
                    @foreach($formateurs as $f)
                        <option value="{{ $f->id }}">{{ $f->name }}</option>
                    @endforeach
                </select>
            </div>

            <div id="salle-row">
                <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:6px;">
                    <label class="tt-modal-label" style="margin:0;">Salle</label>
                    <span id="avail-loading-salle" style="font-size:9px; color:#64748b; display:none;">chargement…</span>
                    <span id="avail-count-salle"   style="font-size:9px; color:{{ $accentColor }}; font-weight:700; display:none;"></span>
                </div>
                <select name="id_salle" id="m-salle" class="tt-modal-input">
                    <option value="">— Sélectionner —</option>
                    @foreach($salles as $s)
                        <option value="{{ $s->id }}">{{ $s->name }} (cap. {{ $s->capacity }})</option>
                    @endforeach
                </select>
            </div>

            <div id="lien-row" style="display:none;">
                <label class="tt-modal-label">Lien de réunion (Teams / Zoom…)</label>
                <input type="text" name="lien_distance" id="m-lien" placeholder="https://teams.microsoft.com/..." class="tt-modal-input" style="height:42px;">
            </div>
            @endif

            <div style="font-size:9px; color:#64748b; background:#f8fafc; border-radius:8px; padding:8px 12px; line-height:1.8;">
                S1 08:30→11:00 &nbsp;·&nbsp; S2 11:00→13:30 &nbsp;·&nbsp; S3 13:30→16:00 &nbsp;·&nbsp; S4 16:00→18:30
            </div>

            <div style="display:flex; gap:10px; margin-top:4px;">
                <button type="button" onclick="closeModal()" style="flex:1; height:44px; border-radius:12px; border:1.5px solid #e2e8f0; background:white; font-size:13px; font-weight:600; color:#64748b; cursor:pointer;">Annuler</button>
                <button type="submit" id="btn-submit" style="flex:1; height:44px; border-radius:12px; border:none; background:{{ $accentColor }}; font-size:13px; font-weight:700; color:white; cursor:pointer; box-shadow:0 4px 12px {{ $accentColor }}40; transition:background 0.2s;">
                    Enregistrer
                </button>
            </div>
        </form>
    </div>
</div>
@endif

{{-- ── PUBLISH CONFIRM MODAL ── --}}
@if($canSeeDraft && $canCreate)
<div id="publish-modal" style="display:none; position:fixed; inset:0; z-index:60;
     background:rgba(15,23,42,0.5); backdrop-filter:blur(4px);
     align-items:center; justify-content:center;"
     onclick="if(event.target===this)closePublishModal()">
    <div style="background:white; border-radius:20px; width:100%; max-width:420px;
                margin:16px; padding:24px; box-shadow:0 24px 60px rgba(0,0,0,0.18);">
        <div style="display:flex; align-items:center; justify-content:space-between;
                    margin-bottom:14px; padding-bottom:14px; border-bottom:2px solid #16a34a;">
            <div style="display:flex; align-items:center; gap:10px;">
                <div style="width:42px; height:42px; border-radius:12px; background:#f0fdf4;
                            display:flex; align-items:center; justify-content:center; flex-shrink:0;">
                    <svg width="20" height="20" fill="none" stroke="#16a34a" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div>
                    <div style="font-size:14px; font-weight:800; color:#1e293b;">Publier les séances ?</div>
                    <div style="font-size:10px; color:#64748b; margin-top:1px;">Cette action rendra les séances visibles aux stagiaires</div>
                </div>
            </div>
            <button onclick="closePublishModal()"
                    style="width:28px;height:28px;border-radius:8px;border:none;background:#f1f5f9;
                           color:#64748b;font-size:16px;cursor:pointer;display:flex;align-items:center;justify-content:center;">×</button>
        </div>
        <div style="display:flex; align-items:center; gap:12px; padding:12px 14px;
                    border-radius:12px; background:#f0fdf4; border:1px solid #bbf7d0; margin-bottom:14px;">
            <div style="width:36px; height:36px; border-radius:10px; background:#dcfce7;
                        display:flex; align-items:center; justify-content:center; flex-shrink:0;">
                <svg width="16" height="16" fill="none" stroke="#16a34a" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
            </div>
            <div>
                <div style="font-size:12px; font-weight:700; color:#166534;">
                    Semaine du {{ $weekStart->translatedFormat('d M') }} au {{ $weekEnd->translatedFormat('d M Y') }}
                </div>
                <div style="font-size:10px; color:#15803d; margin-top:2px;">
                    <span style="font-weight:800;">{{ $draftCount }}</span> séance{{ $draftCount > 1 ? 's' : '' }} en brouillon à publier
                </div>
            </div>
        </div>
        <div style="font-size:11px; color:#92400e; line-height:1.6; margin-bottom:18px;
                    padding:10px 14px; border-radius:10px; background:#fffbeb; border:1px solid #fde68a;
                    display:flex; align-items:flex-start; gap:8px;">
            <svg width="14" height="14" fill="none" stroke="#f59e0b" viewBox="0 0 24 24" style="flex-shrink:0;margin-top:1px;">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
            </svg>
            Les séances publiées seront immédiatement visibles par les stagiaires et formateurs concernés.
        </div>
        <div style="display:flex; gap:10px;">
            <button type="button" onclick="closePublishModal()"
                    style="flex:1; height:44px; border-radius:12px; border:1.5px solid #e2e8f0;
                           background:white; font-size:13px; font-weight:600; color:#64748b; cursor:pointer;">
                Annuler
            </button>
            <button type="button" onclick="submitPublish()"
                    style="flex:2; height:44px; border-radius:12px; border:none;
                           background:#16a34a; font-size:13px; font-weight:700; color:white; cursor:pointer;
                           box-shadow:0 4px 12px rgba(22,163,74,0.35);
                           display:flex; align-items:center; justify-content:center; gap:7px;">
                <svg width="14" height="14" fill="none" stroke="white" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                </svg>
                Oui, publier {{ $draftCount }} séance{{ $draftCount > 1 ? 's' : '' }}
            </button>
        </div>
    </div>
</div>
@endif

{{-- ════ JAVASCRIPT (unchanged) ════ --}}
<script>
const SEANCE_STARTS = ['08:30','11:00','13:30','16:00'];
const SEANCE_ENDS   = ['11:00','13:30','16:00','18:30'];
const SEANCE_HOURS  = [2.5, 2.5, 2.5, 2.5];
const SEANCE_LABELS = ['S1','S2','S3','S4'];
const ACCENT        = '{{ $accentColor }}';
const AVAILABLE_URL = '{{ route('emplois.available') }}';
const emploisData   = @json($emploisJson);
const CAN_SELECT_MODULE = {{ $canSelectModule ? 'true' : 'false' }};
const IS_GESTIONNAIRE   = {{ $isGestionnaire ? 'true' : 'false' }};

@php
    $progressFlat2 = [];
    foreach ($moduleProgress as $gId => $mods) {
        foreach ($mods as $mId => $h) {
            $progressFlat2[$gId.'_'.$mId] = round($h, 2);
        }
    }
@endphp
const moduleProgressData = @json($progressFlat2);

let _slotGroupeId  = null;
let _slotDate      = null;
let _slotSeance    = null;
let _editExcludeId = null;
let _fetchTimer    = null;
let _currentMode   = 'presentiel';

let _allFormateurs = @json($formateurs->map(fn($f) => ['id'=>$f->id,'name'=>$f->name]));
let _allSalles     = @json($salles->map(fn($s) => ['id'=>$s->id,'name'=>$s->name,'capacity'=>$s->capacity]));

// ── Sidebar reflow fix ────────────────────────────────────────────
(function () {
    const sidebar = document.getElementById('sidebar');
    const scroll  = document.getElementById('tt-scroll-container');
    if (!sidebar) return;
    sidebar.addEventListener('transitionend', function (e) {
        if (e.propertyName !== 'width') return;
        if (scroll) {
            scroll.style.maxWidth = 'none';
            void scroll.offsetWidth;
            scroll.style.maxWidth = '100%';
        }
        window.dispatchEvent(new Event('resize'));
    });
    const observer = new MutationObserver(function () {
        const hasDuration = parseFloat(getComputedStyle(sidebar).transitionDuration) > 0;
        if (!hasDuration) setTimeout(() => window.dispatchEvent(new Event('resize')), 0);
    });
    observer.observe(sidebar, { attributes: true, attributeFilter: ['class', 'style'] });
})();

// ── Mobile day switcher ───────────────────────────────────────────
function switchMobileDay(dayNum) {
    document.querySelectorAll('.tt-day-pill').forEach(p => {
        p.classList.toggle('active', parseInt(p.dataset.day) === dayNum);
    });
    document.querySelectorAll('.tt-mobile-day-block').forEach(b => {
        b.style.display = parseInt(b.dataset.day) === dayNum ? 'block' : 'none';
    });
}
(function () {
    const pills = document.querySelectorAll('.tt-day-pill');
    let todayPill = null;
    pills.forEach(p => { if (p.classList.contains('today-pill')) todayPill = p; });
    if (todayPill && window.innerWidth <= 768) {
        switchMobileDay(parseInt(todayPill.dataset.day));
        setTimeout(() => todayPill.scrollIntoView({ behavior: 'smooth', inline: 'center', block: 'nearest' }), 150);
    }
})();

// ── Publish modal ─────────────────────────────────────────────────
function openPublishModal()  { document.getElementById('publish-modal').style.display = 'flex'; }
function closePublishModal() { document.getElementById('publish-modal').style.display = 'none'; }
function submitPublish()     { document.getElementById('publish-form').submit(); }

// ── Promo change ──────────────────────────────────────────────────
function changePromo(promoValue) {
    const url = new URL(window.location.href);
    url.searchParams.set('promo', promoValue);
    window.location.href = url.toString();
}

// ── Mode toggle ───────────────────────────────────────────────────
function setMode(mode) {
    _currentMode = mode;
    document.getElementById('m-mode').value = mode;
    const btnPres  = document.getElementById('btn-pres');
    const btnDist  = document.getElementById('btn-dist');
    const salleRow = document.getElementById('salle-row');
    const lienRow  = document.getElementById('lien-row');
    const submit   = document.getElementById('btn-submit');
    if (mode === 'distance') {
        btnPres.className = 'mode-btn';
        btnDist.className = 'mode-btn active-dist';
        if (salleRow) salleRow.style.display = 'none';
        if (lienRow)  lienRow.style.display  = 'block';
        if (submit) { submit.style.background = '#f59e0b'; submit.style.boxShadow = '0 4px 12px rgba(245,158,11,0.4)'; }
        const salleEl = document.getElementById('m-salle');
        if (salleEl) salleEl.required = false;
    } else {
        btnPres.className = 'mode-btn active-pres';
        btnDist.className = 'mode-btn';
        if (salleRow) salleRow.style.display = 'block';
        if (lienRow)  lienRow.style.display  = 'none';
        if (submit) { submit.style.background = ACCENT; submit.style.boxShadow = '0 4px 12px ' + ACCENT + '40'; }
        const salleEl = document.getElementById('m-salle');
        if (salleEl) salleEl.required = true;
    }
    loadAvailable();
}

// ── Preview bar ───────────────────────────────────────────────────
function updatePreview(seanceIdx, duration) {
    let totalH = 0;
    for (let i = 1; i <= 4; i++) {
        const bar    = document.getElementById('prev-bar-' + i);
        if (!bar) continue;
        const filled = i > seanceIdx && i <= seanceIdx + duration;
        const color  = _currentMode === 'distance' ? '#f59e0b' : ACCENT;
        bar.style.background = filled ? color : '#e2e8f0';
        if (filled) totalH += SEANCE_HOURS[i - 1];
    }
    const endIdx  = Math.min(seanceIdx + duration, 4);
    const startEl = document.getElementById('prev-start');
    const endEl   = document.getElementById('prev-end');
    const lblEl   = document.getElementById('prev-label');
    if (startEl) startEl.textContent = SEANCE_STARTS[seanceIdx];
    if (endEl)   endEl.textContent   = SEANCE_ENDS[endIdx - 1] || '18:30';
    if (lblEl)   lblEl.textContent   = totalH + 'h · ' + duration + ' séance' + (duration > 1 ? 's' : '');
}

// ── Availability fetch ────────────────────────────────────────────
function populateSelect(selectId, items, labelFn, countSpanId, loadingSpanId) {
    const sel = document.getElementById(selectId);
    if (!sel) return;
    const curVal    = sel.value;
    const loadingEl = document.getElementById(loadingSpanId);
    if (loadingEl) loadingEl.style.display = 'none';
    const available = items.filter(i => i.available);
    const busy      = items.filter(i => !i.available);
    sel.innerHTML = '<option value="">— Sélectionner —</option>';
    if (available.length) {
        const grp = document.createElement('optgroup');
        grp.label = '✓ Disponibles (' + available.length + ')';
        available.forEach(i => { const o = document.createElement('option'); o.value = i.id; o.textContent = labelFn(i); grp.appendChild(o); });
        sel.appendChild(grp);
    }
    if (busy.length) {
        const grp = document.createElement('optgroup');
        grp.label = '✗ Occupés (' + busy.length + ')';
        busy.forEach(i => { const o = document.createElement('option'); o.value = i.id; o.textContent = '✗ ' + labelFn(i); o.disabled = true; o.style.color = '#cbd5e1'; grp.appendChild(o); });
        sel.appendChild(grp);
    }
    if (curVal) sel.value = curVal;
    const countEl = document.getElementById(countSpanId);
    if (countEl) { countEl.textContent = available.length + ' dispo.'; countEl.style.display = available.length < items.length ? 'inline' : 'none'; }
}

function populateModuleSelect(modules) {
    const sel = document.getElementById('m-module');
    if (!sel) return;
    const curVal    = sel.value;
    const loadingEl = document.getElementById('avail-loading-module');
    if (loadingEl) loadingEl.style.display = 'none';
    const available = modules.filter(m => !m.nbr_heure || (m.done_hours || 0) < m.nbr_heure);
    const completed = modules.filter(m =>  m.nbr_heure > 0 && (m.done_hours || 0) >= m.nbr_heure);
    sel.innerHTML = '<option value="">— Sélectionner un module —</option>';
    if (available.length) {
        const grp = document.createElement('optgroup');
        grp.label = '✓ Disponibles (' + available.length + ')';
        available.forEach(m => {
            const pct = m.nbr_heure > 0 ? Math.min(99, Math.round(((m.done_hours || 0) / m.nbr_heure) * 100)) : 0;
            const o = document.createElement('option');
            o.value = m.id; o.textContent = m.name + ' (' + m.nbr_heure + 'h — ' + pct + '%)';
            o.dataset.nbrHeure = m.nbr_heure; o.dataset.doneHours = m.done_hours || 0;
            grp.appendChild(o);
        });
        sel.appendChild(grp);
    }
    if (completed.length) {
        const grp = document.createElement('optgroup');
        grp.label = '✗ Terminés 100% — ' + completed.length + ' module(s)';
        completed.forEach(m => {
            const o = document.createElement('option');
            o.value = m.id; o.textContent = '✗ ' + m.name + ' (' + m.nbr_heure + 'h — complet)';
            o.disabled = true; o.style.color = '#94a3b8';
            grp.appendChild(o);
        });
        sel.appendChild(grp);
    }
    if (curVal) sel.value = curVal;
    updateModuleProgress();
}

function updateModuleProgress() {
    const sel  = document.getElementById('m-module');
    const wrap = document.getElementById('module-progress-wrap');
    if (!sel || !wrap) return;
    const moduleId = parseInt(sel.value);
    if (!moduleId || !_slotGroupeId) { wrap.style.display = 'none'; return; }
    const opt    = sel.options[sel.selectedIndex];
    const totalH = parseFloat(opt.dataset.nbrHeure || 0);
    const key    = _slotGroupeId + '_' + moduleId;
    const doneH  = moduleProgressData[key] || 0;
    const pct    = totalH > 0 ? Math.min(100, Math.round((doneH / totalH) * 100)) : 0;
    wrap.style.display = 'block';
    const bar = document.getElementById('module-progress-bar');
    if (bar) { bar.style.width = pct + '%'; bar.style.background = pct >= 100 ? '#22c55e' : ACCENT; }
    const pctEl = document.getElementById('module-progress-pct');
    const lblEl = document.getElementById('module-progress-label');
    const totEl = document.getElementById('module-progress-total');
    if (pctEl) { pctEl.textContent = pct + '%'; pctEl.style.color = pct >= 100 ? '#22c55e' : ACCENT; }
    if (lblEl) lblEl.textContent = doneH.toFixed(1) + 'h planifiées';
    if (totEl) totEl.textContent = totalH + 'h total';
}

function onModuleChange() { updateModuleProgress(); loadAvailable(); }

function loadAvailable() {
    if (!_slotGroupeId || !_slotDate || !_slotSeance) return;
    const durEl      = document.getElementById('m-dur');
    const duration   = parseInt(durEl ? durEl.value : 1);
    const seanceIdx0 = _slotSeance - 1;
    const endIdx     = Math.min(seanceIdx0 + duration, 4);
    const debutEl    = document.getElementById('m-debut');
    const finEl      = document.getElementById('m-fin');
    if (debutEl) debutEl.value = _slotDate + 'T' + SEANCE_STARTS[seanceIdx0];
    if (finEl)   finEl.value   = _slotDate + 'T' + SEANCE_ENDS[endIdx - 1];
    if (document.getElementById('prev-bar-1')) updatePreview(seanceIdx0, duration);
    const loadingUser  = document.getElementById('avail-loading-user');
    const loadingSalle = document.getElementById('avail-loading-salle');
    const loadingMod   = document.getElementById('avail-loading-module');
    if (loadingUser)  loadingUser.style.display  = 'inline';
    if (loadingSalle && _currentMode === 'presentiel') loadingSalle.style.display = 'inline';
    if (loadingMod)   loadingMod.style.display   = 'inline';
    clearTimeout(_fetchTimer);
    _fetchTimer = setTimeout(async () => {
        const moduleId = document.getElementById('m-module')?.value || '';
        const params = new URLSearchParams({
            groupe_id: _slotGroupeId, date: _slotDate, seance_start: _slotSeance,
            duration: duration, mode: _currentMode, module_id: moduleId,
        });
        if (_editExcludeId) params.set('exclude_id', _editExcludeId);
        try {
            const res  = await fetch(AVAILABLE_URL + '?' + params, { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
            const data = await res.json();
            if (document.getElementById('m-user')) populateSelect('m-user', data.formateurs, f => f.name, 'avail-count-user', 'avail-loading-user');
            if (_currentMode === 'presentiel' && document.getElementById('m-salle'))
                populateSelect('m-salle', data.salles, s => s.name + ' (cap. ' + s.capacity + ')', 'avail-count-salle', 'avail-loading-salle');
            else if (loadingSalle) loadingSalle.style.display = 'none';
            if (CAN_SELECT_MODULE && data.modules && moduleId === '') populateModuleSelect(data.modules);
            else if (loadingMod) loadingMod.style.display = 'none';
        } catch (e) {
            console.error('loadAvailable error:', e);
            if (loadingUser)  loadingUser.style.display  = 'none';
            if (loadingSalle) loadingSalle.style.display = 'none';
            if (loadingMod)   loadingMod.style.display   = 'none';
        }
    }, 250);
}

function onDurationChange() { loadAvailable(); }

// ── Modal open / close ────────────────────────────────────────────
function openModalWithSlot(dayNum, seanceNum, dateStr, groupeId) {
    _slotGroupeId  = groupeId; _slotDate = dateStr; _slotSeance = seanceNum; _editExcludeId = null;
    const titleEl = document.getElementById('modal-title');
    const formEl  = document.getElementById('emploi-form');
    const methodEl = document.getElementById('form-method');
    const groupeHEl = document.getElementById('m-groupe-hidden');
    const groupeRow = document.getElementById('m-groupe-row');
    const slotInfoEl = document.getElementById('modal-slot-info');
    const durEl = document.getElementById('m-dur');
    const modWrap = document.getElementById('module-progress-wrap');
    const modSel  = document.getElementById('m-module');
    if (titleEl)    titleEl.textContent    = 'Nouvelle séance';
    if (formEl)     formEl.action          = '{{ route('emplois.store') }}';
    if (methodEl)   methodEl.value         = 'POST';
    if (groupeHEl)  groupeHEl.value        = groupeId;
    if (groupeRow)  groupeRow.style.display = 'none';
    if (durEl)      durEl.value            = '1';
    if (slotInfoEl) slotInfoEl.textContent = dateStr + ' · ' + (SEANCE_LABELS[seanceNum - 1] || 'S' + seanceNum) + ' · ' + SEANCE_STARTS[seanceNum - 1];
    if (modWrap) modWrap.style.display = 'none';
    if (modSel)  modSel.innerHTML = '<option value="">— Sélectionner un module —</option>';
    if (document.getElementById('btn-pres')) setMode('presentiel');
    loadAvailable();
    showModal();
}

function openEditModal(id) {
    const e = emploisData.find(x => x.id === id);
    if (!e) return;
    const dateStr   = e.date_debut.slice(0, 10);
    const timeStr   = e.date_debut.slice(11, 16);
    const si        = SEANCE_STARTS.indexOf(timeStr);
    const seanceNum = si >= 0 ? si + 1 : 1;
    const ei        = SEANCE_STARTS.indexOf(e.date_fin.slice(11, 16));
    const duration  = ei > si ? ei - si : 1;
    _slotGroupeId  = e.id_groupe; _slotDate = dateStr; _slotSeance = seanceNum; _editExcludeId = id;
    const titleEl   = document.getElementById('modal-title');
    const formEl    = document.getElementById('emploi-form');
    const methodEl  = document.getElementById('form-method');
    const groupeHEl = document.getElementById('m-groupe-hidden');
    const groupeRow = document.getElementById('m-groupe-row');
    const slotInfoEl = document.getElementById('modal-slot-info');
    const debutEl   = document.getElementById('m-debut');
    const finEl     = document.getElementById('m-fin');
    const durEl     = document.getElementById('m-dur');
    const lienEl    = document.getElementById('m-lien');
    if (titleEl)    titleEl.textContent    = 'Modifier la séance';
    if (formEl)     formEl.action          = `/emplois/${id}`;
    if (methodEl)   methodEl.value         = 'PUT';
    if (groupeHEl)  groupeHEl.value        = e.id_groupe;
    if (groupeRow)  groupeRow.style.display = 'none';
    if (slotInfoEl) slotInfoEl.textContent  = dateStr + ' · ' + (SEANCE_LABELS[seanceNum - 1] || 'S' + seanceNum);
    if (debutEl)    debutEl.value           = e.date_debut;
    if (finEl)      finEl.value             = e.date_fin;
    if (durEl)      durEl.value             = String(duration);
    if (document.getElementById('btn-pres')) {
        setMode(e.mode || 'presentiel');
        if (lienEl) lienEl.value = e.lien_distance || '';
        if (document.getElementById('prev-bar-1')) updatePreview(seanceNum - 1, duration);
    }
    loadAvailable();
    showModal();
    const prevUser = e.id_user, prevSalle = e.id_salle, prevModule = e.id_module;
    setTimeout(() => {
        const userEl   = document.getElementById('m-user');
        const salleEl  = document.getElementById('m-salle');
        const moduleEl = document.getElementById('m-module');
        if (prevUser   && userEl)   userEl.value   = prevUser;
        if (prevSalle  && salleEl)  salleEl.value  = prevSalle;
        if (prevModule && moduleEl) { moduleEl.value = prevModule; updateModuleProgress(); }
    }, 450);
}

function showModal()  { document.getElementById('emploi-modal').classList.add('open'); }
function closeModal() { document.getElementById('emploi-modal').classList.remove('open'); }

// ── Delete modal ──────────────────────────────────────────────────
function openDeleteModal(action, groupe, module, date, span, heureDebut, heureFin, salle) {
    document.getElementById('delete-form').action               = action;
    document.getElementById('delete-session-label').textContent = groupe + ' — ' + module;
    document.getElementById('delete-session-meta').textContent  = date + ' · ' + span + ' · ' + heureDebut + ' → ' + heureFin + ' · ' + salle;
    document.getElementById('delete-modal').style.display = 'flex';
}
function closeDeleteModal() { document.getElementById('delete-modal').style.display = 'none'; }

// ── Lien modal ────────────────────────────────────────────────────
function openLienModal(id, currentLien, groupeLabel, dateMeta) {
    document.getElementById('lien-form').action               = '/emplois/' + id + '/lien';
    document.getElementById('lien-input').value               = currentLien || '';
    document.getElementById('lien-session-label').textContent = groupeLabel || 'Séance';
    document.getElementById('lien-session-meta').textContent  = dateMeta   || '';
    document.getElementById('lien-modal').style.display = 'flex';
}
function closeLienModal() { document.getElementById('lien-modal').style.display = 'none'; }
function submitLien() {
    document.getElementById('lien-hidden').value = document.getElementById('lien-input').value;
    document.getElementById('lien-form').submit();
}

// ── Report modal ──────────────────────────────────────────────────
function openReportModal(emploiId, sessionLabel, dateLabel, dateStr, spanLabel, heureDebut, heureFin) {
    document.getElementById('report-emploi-id').value           = emploiId;
    document.getElementById('report-session-label').textContent = sessionLabel;
    document.getElementById('report-current-date').textContent  = dateLabel;
    document.getElementById('report-current-time').textContent  = spanLabel + ' · ' + heureDebut + ' → ' + heureFin;
    const ta      = document.getElementById('report-raison');
    const counter = document.getElementById('report-raison-count');
    if (ta)      ta.value = '';
    if (counter) counter.textContent = '0 / 1000';
    document.getElementById('report-modal').style.display = 'flex';
}
function closeReportModal() { document.getElementById('report-modal').style.display = 'none'; }

const reportRaison = document.getElementById('report-raison');
if (reportRaison) {
    reportRaison.addEventListener('input', function () {
        const counter = document.getElementById('report-raison-count');
        if (counter) counter.textContent = this.value.length + ' / 1000';
    });
}
</script>

@endsection