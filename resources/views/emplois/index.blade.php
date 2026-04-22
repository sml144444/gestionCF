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
    $canChangeModule = Auth::user()->hasPermissionTo('emploi-change-module');
    $canSelectModule = Auth::user()->hasPermissionTo('emploi-view-all-groups') || $canChangeModule;
    $isGestionnaire  = Auth::user()->hasPermissionTo('emploi-view-all-groups');
    $canReport       = Auth::user()->hasPermissionTo('reportation-create');

    $pendingReportIds = $canReport
        ? \App\Models\Reportation::where('id_user', Auth::id())
            ->where('status', 'en_attente')
            ->pluck('id_emplois_du_temps')
            ->toArray()
        : [];

    $isStagiaire = Auth::user()->role === 'stagiaire';

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
@endphp

<style>
.tt-wrap { font-family: 'Segoe UI', system-ui, sans-serif; }

.tt-scroll {
    overflow-x: auto; overflow-y: visible;
    border-radius: 16px; border: 1px solid #e2e8f0;
    background: #f8fafc; box-shadow: 0 2px 12px rgba(0,0,0,0.06);
    scrollbar-width: thin; scrollbar-color: {{ $accentColor }}40 transparent;
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
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    gap: 6px;
    margin-bottom: 8px;
}
.tt-card-module {
    font-size: 11.5px;
    font-weight: 700;
    color: #1e293b;
    line-height: 1.3;
    flex: 1;
    min-width: 0;
    overflow: hidden;
    text-overflow: ellipsis;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
}
.tt-card-time {
    font-size: 9px;
    font-weight: 700;
    padding: 3px 8px;
    border-radius: 8px;
    background: {{ $p['light'] }};
    color: {{ $p['text'] }};
    white-space: nowrap;
    flex-shrink: 0;
}

.tt-card-meta { display: flex; flex-direction: column; gap: 4px; }
.tt-card-row  { display: flex; align-items: center; gap: 5px; font-size: 10px; color: #475569; }
.tt-card-icon {
    width: 15px; height: 15px; border-radius: 4px;
    background: {{ $p['light'] }};
    flex-shrink: 0;
    display: flex; align-items: center; justify-content: center;
}
.tt-card-icon svg { width: 9px; height: 9px; }
.tt-card-dot { width: 5px; height: 5px; border-radius: 50%; flex-shrink: 0; }

.card-distance { border-top-color: #f59e0b; background: #fffbeb; }
.card-distance .tt-card-module { color: #92400e; }
.card-distance .tt-card-time   { background: #fef3c7; color: #b45309; }

.card-brouillon {
    opacity: 0.7;
    border-style: dashed;
    border-top-style: dashed;
    border-top-color: #94a3b8;
    background: #f8fafc;
}
.card-brouillon .tt-card-module { color: #475569; }
.card-brouillon .tt-card-time   { background: #f1f5f9; color: #64748b; }
.draft-badge {
    display: inline-block;
    font-size: 8px; font-weight: 800; letter-spacing: .5px;
    background: #f1f5f9; color: #64748b;
    padding: 2px 7px; border-radius: 6px; border: 1px solid #cbd5e1;
    text-transform: uppercase;
    margin-bottom: 5px;
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

.tt-card-footer {
    display: grid;
    grid-template-columns: 1fr 1fr 1fr;
    border-top: 1px solid #f1f5f9;
}
.tt-foot-btn {
    padding: 7px 4px;
    font-size: 10px; font-weight: 700;
    border: none; cursor: pointer; background: transparent;
    color: #94a3b8;
    display: flex; align-items: center; justify-content: center; gap: 4px;
    transition: background 0.12s, color 0.12s;
    text-decoration: none;
    white-space: nowrap;
}
.tt-foot-btn:not(:last-child) { border-right: 1px solid #f1f5f9; }
.tt-foot-btn:hover             { background: #f8fafc; color: #1e293b; }
.tt-foot-btn-pres:hover        { background: #f0fdf4; color: #16a34a; }
.tt-foot-btn-cls:hover         { background: {{ $p['light'] }}; color: {{ $p['text'] }}; }
.card-distance .tt-card-footer { background: #fef9ee; }

.tt-actions { position: absolute; top: 7px; right: 7px; display: none; gap: 3px; }
.tt-card:hover .tt-actions { display: flex; }
.tt-btn-edit { font-size: 9px; font-weight: 700; padding: 2px 7px; border-radius: 6px; border: none; cursor: pointer; transition: opacity 0.15s; }
.tt-btn-del  { font-size: 9px; font-weight: 700; background: #fee2e2; color: #dc2626; padding: 2px 7px; border-radius: 6px; border: none; cursor: pointer; transition: opacity 0.15s; }
.tt-btn-edit:hover, .tt-btn-del:hover { opacity: 0.8; }

/* ── Empty cell ── */
.tt-empty-td { padding: 5px; }
.tt-add-btn {
    width: 100%; min-height: 72px;
    border: 1.5px dashed #e2e8f0; border-radius: 10px;
    background: transparent;
    display: flex; align-items: center; justify-content: center;
    font-size: 16px; color: #e2e8f0;
    cursor: pointer; transition: all 0.15s;
}
.tt-add-btn:hover { border-color: {{ $accentColor }}; color: {{ $accentColor }}; background: {{ $p['light'] }}; }

/* ── Past day locked cell ── */
.tt-past-cell {
    width: 100%; min-height: 72px;
    border-radius: 10px;
    background: repeating-linear-gradient(
        135deg,
        transparent,
        transparent 4px,
        #f1f5f9 4px,
        #f1f5f9 8px
    );
    display: flex; align-items: center; justify-content: center;
    cursor: not-allowed;
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

{{-- ════ STAGIAIRE : avertissement ════ --}}
@if($isStagiaire)
@php
    $joursAvance = 2;
    $prochainLundi = \Carbon\Carbon::now()->startOfWeek(\Carbon\Carbon::MONDAY)->addWeek();
    $visibleDepuis = $prochainLundi->copy()->subDays($joursAvance);
    $estSemaineActuelle = $weekStart->eq(\Carbon\Carbon::now()->startOfWeek(\Carbon\Carbon::MONDAY));
    $estSemaineProchaine = $weekStart->eq($prochainLundi);
    $peutVoirSemaineProchaine = \Carbon\Carbon::now()->gte($visibleDepuis);
    $semaineAutorisee = $weekStart->lte(\Carbon\Carbon::now()->startOfWeek(\Carbon\Carbon::MONDAY))
                     || ($estSemaineProchaine && $peutVoirSemaineProchaine);
@endphp
@if(!$semaineAutorisee)
<div style="margin-bottom:16px; padding:12px 16px; border-radius:12px; font-size:13px;
            display:flex; align-items:center; gap:8px;
            background:#fff7ed; border:1px solid #fed7aa; color:#9a3412;">
    <svg width="16" height="16" fill="currentColor" viewBox="0 0 20 20">
        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
    </svg>
    L'emploi du temps de cette semaine sera disponible
    <strong>le {{ $visibleDepuis->translatedFormat('l d M') }}</strong>.
</div>
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
<div style="display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:12px; margin-bottom:20px;">

{{-- Year tabs --}}
<div style="display:inline-flex; border-radius:12px; overflow:hidden; border:1.5px solid #e2e8f0; background:white;">
    @php
        $tab1Disabled = $isStagiaire && $stagiaireYear !== null && $stagiaireYear !== 1;
        $tab2Disabled = $isStagiaire && $stagiaireYear !== null && $stagiaireYear !== 2;
        $tab3Disabled = $isStagiaire && $stagiaireYear !== null && $stagiaireYear !== 3;
    @endphp

    @if($tab1Disabled)
        <span class="tt-tab disabled" title="Vous êtes inscrit en {{ $stagiaireYear }}ème année">Année 1 &nbsp;🔒</span>
    @else
        <a href="{{ route('emplois.index', ['year' => 1, 'week' => $weekStart->toDateString()]) }}"
           class="tt-tab {{ $year === 1 ? 'active' : 'inactive' }}"
           style="{{ $year === 1 ? 'background:'.$accentYear1.';' : '' }}">
            Année 1
            <span style="font-size:9px; padding:2px 7px; border-radius:99px; font-weight:700;
                         {{ $year === 1 ? 'background:rgba(255,255,255,0.2); color:white;'
                                       : 'background:'.$p['light'].'; color:'.$p['text'].';' }}">1ère</span>
        </a>
    @endif

    @if($tab2Disabled)
        <span class="tt-tab disabled" style="border-left:1.5px solid #e2e8f0;" title="Vous êtes inscrit en {{ $stagiaireYear }}ème année">Année 2 / 2.5 &nbsp;🔒</span>
    @else
        <a href="{{ route('emplois.index', ['year' => 2, 'week' => $weekStart->toDateString()]) }}"
           class="tt-tab {{ $year === 2 ? 'active' : 'inactive' }}"
           style="{{ $year === 2 ? 'background:'.$accentYear1.';' : '' }} border-left:1.5px solid #e2e8f0;">
            Année 2
            <span style="font-size:9px; padding:2px 7px; border-radius:99px; font-weight:700;
                         {{ $year === 2 ? 'background:rgba(255,255,255,0.2); color:white;'
                                       : 'background:'.$p['light'].'; color:'.$p['text'].';' }}">2ème</span>
        </a>
    @endif

    @if($tab3Disabled)
        <span class="tt-tab disabled" style="border-left:1.5px solid #e2e8f0;" title="Vous êtes inscrit en {{ $stagiaireYear }}ème année">Année 3 &nbsp;🔒</span>
    @else
        <a href="{{ route('emplois.index', ['year' => 3, 'week' => $weekStart->toDateString()]) }}"
           class="tt-tab {{ $year === 3 ? 'active' : 'inactive' }}"
           style="{{ $year === 3 ? 'background:'.$accentYear1.';' : '' }} border-left:1.5px solid #e2e8f0;">
            Année 3
            <span style="font-size:9px; padding:2px 7px; border-radius:99px; font-weight:700;
                         {{ $year === 3 ? 'background:rgba(255,255,255,0.2); color:white;'
                                       : 'background:'.$p['light'].'; color:'.$p['text'].';' }}">3ème</span>
        </a>
    @endif
</div>

    {{-- Week nav --}}
    <div style="display:flex; align-items:center; gap:8px; flex-wrap:wrap;">
        <div style="font-size:11px; color:#64748b;">
            <strong style="color:#334155;">{{ $weekStart->translatedFormat('d M') }}</strong>
            &nbsp;–&nbsp;
            <strong style="color:#334155;">{{ $weekEnd->translatedFormat('d M Y') }}</strong>
        </div>
        <a href="{{ route('emplois.index', ['year' => $year, 'week' => $weekStart->copy()->subWeek()->toDateString()]) }}" class="tt-nav-btn">
            <svg style="width:12px;height:12px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"/></svg>
        </a>
        <a href="{{ route('emplois.index', ['year' => $year]) }}" class="tt-nav-btn today-btn">Aujourd'hui</a>

        @php
            $semaineSuivante = $weekStart->copy()->addWeek();
            $prochainLundiNav = \Carbon\Carbon::now()->startOfWeek(\Carbon\Carbon::MONDAY)->addWeek();
            $visibleDepuisNav = $prochainLundiNav->copy()->subDays(2);
            $peutNaviguerSuivante = !$isStagiaire || $semaineSuivante->lte(\Carbon\Carbon::now()->startOfWeek(\Carbon\Carbon::MONDAY))
                || ($semaineSuivante->eq($prochainLundiNav) && \Carbon\Carbon::now()->gte($visibleDepuisNav));
        @endphp
        @if($peutNaviguerSuivante)
            <a href="{{ route('emplois.index', ['year' => $year, 'week' => $weekStart->copy()->addWeek()->toDateString()]) }}" class="tt-nav-btn">
                <svg style="width:12px;height:12px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
            </a>
        @else
            <span class="tt-nav-btn" style="opacity:0.35; cursor:not-allowed;">
                <svg style="width:12px;height:12px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
            </span>
        @endif

        <a href="{{ route('emplois.pdf', ['year' => $year, 'week' => $weekStart->toDateString()]) }}"
           class="tt-nav-btn" title="Télécharger PDF"
           style="color:#dc2626; border-color:#fecdd3; background:#fff1f2;">
            <svg style="width:12px;height:12px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414A1 1 0 0119 9.414V19a2 2 0 01-2 2z"/>
            </svg>
            PDF
        </a>

        @if($canSeeDraft && $canCreate)
        <form method="POST"
              action="{{ route('emplois.publish', ['year' => $year, 'week' => $weekStart->toDateString()]) }}"
              style="display:inline;"
              onsubmit="return confirm('Publier toutes les séances en brouillon de cette semaine ?')">
            @csrf
            <button type="submit"
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

{{-- ════ GRID ════ --}}
<div class="tt-scroll">
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
                                                ? $emploi->module->remplacant
                                                : null;

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

                                @if($canEdit || $canDelete || $canLien || $canChangeModule || $isGestionnaire || $canReport)
                                <div class="tt-actions">
                                    @if($canEdit)
                                        <button class="tt-btn-edit"
                                                style="background:{{ $isRemote ? '#fef3c7' : $p['light'] }}; color:{{ $isRemote ? '#92400e' : $p['text'] }};"
                                                onclick="openEditModal({{ $emploi->id }})">✎</button>
                                    @endif

                                    @if($isGestionnaire && !$isDraft)
                                        <button class="tt-btn-edit"
                                                style="background:#f5f3ff; color:#6d28d9;"
                                                onclick="openRemplacantModal(
                                                    {{ $emploi->id }},
                                                    '{{ addslashes(($emploi->groupe->name ?? 'Groupe').' — '.($emploi->module->name ?? 'Module')) }}',
                                                    '{{ $emploi->date_debut->translatedFormat('l d M') }} · {{ EmploiDuTempsController::spanLabel($sNum, $colspan) }}',
                                                    {{ $emploi->id_user_remplacant ?? 'null' }}
                                                )"
                                                title="{{ $hasRemplacant ? 'Modifier le remplaçant' : 'Assigner un remplaçant' }}">
                                            {{ $hasRemplacant ? '🔄' : '👤' }}
                                        </button>
                                    @endif

                                    @if($canReport && $emploi->id_user === Auth::user()->id)
                                        @php $alreadyPending = in_array($emploi->id, $pendingReportIds); @endphp
                                        @if($alreadyPending)
                                            <span class="tt-btn-edit" style="background:#fef3c7; color:#92400e; opacity:.6; cursor:not-allowed;" title="Demande déjà en attente">⏳</span>
                                        @else
                                            <button class="tt-btn-edit"
                                                    style="background:#f5f3ff; color:#6d28d9;"
                                                    onclick="openReportModal(
                                                        {{ $emploi->id }},
                                                        '{{ addslashes(($emploi->groupe->name ?? 'Groupe').' — '.($emploi->module->name ?? 'Module')) }}',
                                                        '{{ $emploi->date_debut->translatedFormat('l d M Y') }}',
                                                        '{{ $emploi->date_debut->format('Y-m-d') }}',
                                                        '{{ EmploiDuTempsController::spanLabel($sNum, $colspan) }}',
                                                        '{{ $emploi->date_debut->format('H:i') }}',
                                                        '{{ $emploi->date_fin->format('H:i') }}'
                                                    )"
                                                    title="Demander un report">📋</button>
                                        @endif
                                    @endif

                                    @if($canDelete)
                                        <button type="button" class="tt-btn-del"
                                                onclick="openDeleteModal(
                                                    '{{ route('emplois.destroy', $emploi) }}',
                                                    '{{ addslashes($emploi->groupe->name ?? 'Groupe') }}',
                                                    '{{ addslashes($emploi->module->name ?? 'Module') }}',
                                                    '{{ $emploi->date_debut->translatedFormat('l d M') }}',
                                                    '{{ EmploiDuTempsController::spanLabel($sNum, $colspan) }}',
                                                    '{{ $emploi->date_debut->format('H:i') }}',
                                                    '{{ $emploi->date_fin->format('H:i') }}',
                                                    '{{ addslashes($emploi->salle->name ?? ($isRemote ? 'À distance' : '—')) }}'
                                                )">✕</button>
                                    @endif

                                    @if($canLien && $emploi->mode === 'distance' && (Auth::user()->role === 'formateur' ? $emploi->id_user === Auth::user()->id : true))
                                        <button class="tt-btn-edit"
                                                style="background:#fef3c7; color:#92400e;"
                                                onclick="openLienModal(
                                                    {{ $emploi->id }},
                                                    '{{ addslashes($emploi->lien_distance ?? '') }}',
                                                    '{{ addslashes(($emploi->groupe->name ?? 'Groupe').' — '.($emploi->module->name ?? 'Module')) }}',
                                                    '{{ $emploi->date_debut->translatedFormat('l d M') }} · {{ EmploiDuTempsController::spanLabel($sNum, $colspan) }} · {{ $emploi->date_debut->format('H:i') }} → {{ $emploi->date_fin->format('H:i') }}'
                                                )">🔗</button>
                                    @endif

                                    @if($canChangeModule && !$canEdit && $emploi->id_user === Auth::user()->id)
                                        <button class="tt-btn-edit"
                                                style="background:{{ $p['light'] }}; color:{{ $p['text'] }};"
                                                onclick="openEditModal({{ $emploi->id }})">📚</button>
                                    @endif
                                </div>
                                @endif

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

                                <div class="tt-card-footer">
                                    <a href="{{ route('seances.show', $emploi) }}"
                                       class="tt-foot-btn"
                                       title="Voir le détail">
                                        <svg width="11" height="11" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <circle cx="11" cy="11" r="8" stroke-width="2"/>
                                            <path d="M21 21l-4.35-4.35" stroke-width="2" stroke-linecap="round"/>
                                        </svg>
                                        Voir
                                    </a>

                                    @if(in_array(Auth::user()->role, ['admin','gestionnaire','formateur']))
                                        @if(!$isDraft)
                                            <a href="{{ route('seances.show', $emploi) }}#presence"
                                               class="tt-foot-btn tt-foot-btn-pres"
                                               title="Saisir la présence">
                                                <svg width="11" height="11" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                                          d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                                                </svg>
                                                Présence
                                            </a>
                                        @else
                                            <span class="tt-foot-btn" style="opacity:.35; cursor:not-allowed;" title="Disponible après publication">
                                                Présence
                                            </span>
                                        @endif
                                    @else
                                        <span></span>
                                    @endif

                                    <a href="{{ route('seances.show', $emploi) }}#classroom"
                                       class="tt-foot-btn tt-foot-btn-cls"
                                       title="Ressources du cours">
                                        <svg width="11" height="11" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-width="2" stroke-linecap="round"
                                                  d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                                        </svg>
                                        Cours
                                    </a>
                                </div>

                            </div>
                        </td>

                    @else
                        {{-- ── EMPTY CELL ── --}}
                        <td class="tt-empty-td" style="{{ $cellBorder }}">
                            @if($canCreate && !$isPastDay)
                                {{-- Future or today: show + button --}}
                                <button class="tt-add-btn"
                                        onclick="openModalWithSlot({{ $dayNum }}, {{ $sNum }}, '{{ $date->toDateString() }}', {{ $groupe->id }})">
                                    +
                                </button>
                            @elseif($canCreate && $isPastDay)
                                {{-- Past day: locked cell --}}
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
                Aucun groupe pour cette année.
            </td>
        </tr>
    @endforelse
    </tbody>
</table>
</div>

{{-- ════ LEGEND ════ --}}
<div style="display:flex; flex-wrap:wrap; gap:12px; align-items:center; margin-top:12px;
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
     MODALS
     ════════════════════════════════════════════════════════════ --}}

{{-- ── DELETE ──────────────────────────────────────────────── --}}
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

{{-- ── LIEN DISTANCE ───────────────────────────────────────── --}}
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

{{-- ── REMPLACEMENT ─────────────────────────────────────────── --}}
@if($isGestionnaire)
<div id="remplacant-modal" style="display:none; position:fixed; inset:0; z-index:60;
     background:rgba(15,23,42,0.5); backdrop-filter:blur(4px);
     align-items:center; justify-content:center;"
     onclick="if(event.target===this)closeRemplacantModal()">
    <div style="background:white; border-radius:20px; width:100%; max-width:430px;
                margin:16px; padding:24px; box-shadow:0 24px 60px rgba(0,0,0,0.18);">
        <div style="display:flex; align-items:center; justify-content:space-between;
                    margin-bottom:14px; padding-bottom:14px; border-bottom:2px solid #7c3aed;">
            <div style="display:flex; align-items:center; gap:10px;">
                <div style="width:40px; height:40px; border-radius:12px; background:#f5f3ff;
                            display:flex; align-items:center; justify-content:center; flex-shrink:0;">
                    <svg width="18" height="18" fill="none" stroke="#7c3aed" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                </div>
                <div>
                    <div style="font-size:14px; font-weight:800; color:#1e293b;">Assigner un remplaçant</div>
                    <div id="remplacant-session-meta" style="font-size:10px; color:#64748b; margin-top:1px;"></div>
                </div>
            </div>
            <button onclick="closeRemplacantModal()"
                    style="width:28px;height:28px;border-radius:8px;border:none;background:#f1f5f9;color:#64748b;font-size:16px;cursor:pointer;display:flex;align-items:center;justify-content:center;">×</button>
        </div>

        <div style="padding:10px 12px; border-radius:10px; background:#f8fafc;
                    border:1px solid #e2e8f0; margin-bottom:14px; font-size:11px; color:#1e293b; font-weight:600;">
            📅 <span id="remplacant-session-label"></span>
        </div>

        <form id="remplacant-form" method="POST" style="display:flex; flex-direction:column; gap:14px;">
            @csrf
            <div>
                <label style="display:block; font-size:9px; font-weight:800; color:#94a3b8;
                              letter-spacing:1.5px; text-transform:uppercase; margin-bottom:6px;">
                    Formateur remplaçant pour cette séance
                </label>
                <select name="id_user_remplacant" id="remplacant-select"
                        style="width:100%; height:42px; padding:0 12px; border-radius:10px;
                               border:1.5px solid #e2e8f0; background:#f8fafc; font-size:13px;
                               color:#1e293b; outline:none; box-sizing:border-box; transition:border-color .15s;"
                        onfocus="this.style.borderColor='#7c3aed';this.style.background='white';"
                        onblur="this.style.borderColor='#e2e8f0';this.style.background='#f8fafc';">
                    <option value="">— Aucun (annuler le remplacement) —</option>
                    @foreach($formateurs as $f)
                        <option value="{{ $f->id }}">{{ $f->name }}</option>
                    @endforeach
                </select>
            </div>

            <div style="padding:10px 14px; border-radius:10px; background:#f5f3ff; border:1px solid #ddd6fe;
                        font-size:11px; color:#5b21b6; display:flex; align-items:flex-start; gap:8px;">
                <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="flex-shrink:0;margin-top:1px;">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0116 0z"/>
                </svg>
                Le remplacement de séance est prioritaire sur le remplaçant du module.
                Choisir « Aucun » pour revenir au remplaçant du module si défini.
            </div>

            <div style="display:flex; gap:10px; margin-top:4px;">
                <button type="button" onclick="closeRemplacantModal()"
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
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                    </svg>
                    Enregistrer
                </button>
            </div>
        </form>
    </div>
</div>
@endif

{{-- ── REPORT ───────────────────────────────────────────────── --}}
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
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
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

{{-- ── CREATE / EDIT ────────────────────────────────────────── --}}
@if($canCreate || $canEdit || $canChangeModule)
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
                        <svg style="width:13px;height:13px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5"/></svg>
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

{{-- ════ JAVASCRIPT ════ --}}
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
        salleRow.style.display = 'none';
        lienRow.style.display  = 'block';
        submit.style.background = '#f59e0b';
        submit.style.boxShadow  = '0 4px 12px rgba(245,158,11,0.4)';
        document.getElementById('m-salle').required = false;
    } else {
        btnPres.className = 'mode-btn active-pres';
        btnDist.className = 'mode-btn';
        salleRow.style.display = 'block';
        lienRow.style.display  = 'none';
        submit.style.background = ACCENT;
        submit.style.boxShadow  = '0 4px 12px ' + ACCENT + '40';
        document.getElementById('m-salle').required = true;
    }
    loadAvailable();
}

function updatePreview(seanceIdx, duration) {
    let totalH = 0;
    for (let i = 1; i <= 4; i++) {
        const bar    = document.getElementById('prev-bar-' + i);
        const filled = i > seanceIdx && i <= seanceIdx + duration;
        const color  = _currentMode === 'distance' ? '#f59e0b' : ACCENT;
        bar.style.background = filled ? color : '#e2e8f0';
        if (filled) totalH += SEANCE_HOURS[i - 1];
    }
    const endIdx = Math.min(seanceIdx + duration, 4);
    document.getElementById('prev-start').textContent = SEANCE_STARTS[seanceIdx];
    document.getElementById('prev-end').textContent   = SEANCE_ENDS[endIdx - 1] || '18:30';
    document.getElementById('prev-label').textContent = totalH + 'h · ' + duration + ' séance' + (duration > 1 ? 's' : '');
}

function populateSelect(selectId, items, labelFn, countSpanId, loadingSpanId) {
    const sel = document.getElementById(selectId);
    if (!sel) return;
    const curVal    = sel.value;
    document.getElementById(loadingSpanId).style.display = 'none';
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
    const curVal  = sel.value;
    const loading = document.getElementById('avail-loading-module');
    if (loading) loading.style.display = 'none';
    sel.innerHTML = '<option value="">— Sélectionner un module —</option>';
    modules.forEach(m => {
        const o = document.createElement('option');
        o.value = m.id;
        o.textContent = m.name + ' (' + m.nbr_heure + 'h)';
        o.dataset.nbrHeure = m.nbr_heure;
        sel.appendChild(o);
    });
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
    bar.style.width      = pct + '%';
    bar.style.background = pct >= 100 ? '#22c55e' : ACCENT;
    document.getElementById('module-progress-pct').textContent   = pct + '%';
    document.getElementById('module-progress-pct').style.color   = pct >= 100 ? '#22c55e' : ACCENT;
    document.getElementById('module-progress-label').textContent = doneH.toFixed(1) + 'h planifiées';
    document.getElementById('module-progress-total').textContent = totalH + 'h total';
}

function onModuleChange() {
    updateModuleProgress();
    loadAvailable();
}

function loadAvailable() {
    if (!_slotGroupeId || !_slotDate || !_slotSeance) return;
    const duration   = parseInt(document.getElementById('m-dur') ? document.getElementById('m-dur').value : 1);
    const seanceIdx0 = _slotSeance - 1;
    const endIdx     = Math.min(seanceIdx0 + duration, 4);

    const debutEl = document.getElementById('m-debut');
    const finEl   = document.getElementById('m-fin');
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
            groupe_id:    _slotGroupeId,
            date:         _slotDate,
            seance_start: _slotSeance,
            duration:     duration,
            mode:         _currentMode,
            module_id:    moduleId,
        });
        if (_editExcludeId) params.set('exclude_id', _editExcludeId);

        try {
            const res  = await fetch(AVAILABLE_URL + '?' + params, { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
            const data = await res.json();

            if (document.getElementById('m-user')) {
                populateSelect('m-user', data.formateurs, f => f.name, 'avail-count-user', 'avail-loading-user');
            }
            if (_currentMode === 'presentiel' && document.getElementById('m-salle')) {
                populateSelect('m-salle', data.salles, s => s.name + ' (cap. ' + s.capacity + ')', 'avail-count-salle', 'avail-loading-salle');
            }
            if (CAN_SELECT_MODULE && data.modules && moduleId === '') {
                populateModuleSelect(data.modules);
            } else {
                if (loadingMod) loadingMod.style.display = 'none';
            }
        } catch {
            if (loadingUser)  loadingUser.style.display  = 'none';
            if (loadingSalle) loadingSalle.style.display = 'none';
            if (loadingMod)   loadingMod.style.display   = 'none';
        }
    }, 250);
}

function onDurationChange() { loadAvailable(); }

function openModalWithSlot(dayNum, seanceNum, dateStr, groupeId) {
    _slotGroupeId  = groupeId;
    _slotDate      = dateStr;
    _slotSeance    = seanceNum;
    _editExcludeId = null;

    document.getElementById('modal-title').textContent    = 'Nouvelle séance';
    document.getElementById('emploi-form').action         = '{{ route('emplois.store') }}';
    document.getElementById('form-method').value          = 'POST';
    document.getElementById('m-groupe-hidden').value      = groupeId;
    document.getElementById('m-groupe-row').style.display = 'none';
    if (document.getElementById('m-dur')) document.getElementById('m-dur').value = '1';
    document.getElementById('modal-slot-info').textContent =
        dateStr + ' · ' + (SEANCE_LABELS[seanceNum-1]||'S'+seanceNum) + ' · ' + SEANCE_STARTS[seanceNum-1];

    const modWrap = document.getElementById('module-progress-wrap');
    if (modWrap) modWrap.style.display = 'none';
    const modSel = document.getElementById('m-module');
    if (modSel) modSel.innerHTML = '<option value="">— Sélectionner un module —</option>';

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

    _slotGroupeId  = e.id_groupe;
    _slotDate      = dateStr;
    _slotSeance    = seanceNum;
    _editExcludeId = id;

    document.getElementById('modal-title').textContent    = 'Modifier la séance';
    document.getElementById('emploi-form').action         = `/emplois/${id}`;
    document.getElementById('form-method').value          = 'PUT';
    document.getElementById('m-groupe-hidden').value      = e.id_groupe;
    document.getElementById('m-groupe-row').style.display = 'none';
    document.getElementById('modal-slot-info').textContent = dateStr + ' · ' + (SEANCE_LABELS[seanceNum-1]||'S'+seanceNum);

    if (document.getElementById('m-debut')) {
        document.getElementById('m-debut').value = e.date_debut;
        document.getElementById('m-fin').value   = e.date_fin;
    }
    if (document.getElementById('m-dur')) document.getElementById('m-dur').value = String(duration);

    if (document.getElementById('btn-pres')) {
        setMode(e.mode || 'presentiel');
        if (document.getElementById('m-lien')) document.getElementById('m-lien').value = e.lien_distance || '';
        if (document.getElementById('prev-bar-1')) updatePreview(seanceNum - 1, duration);
    }

    loadAvailable();
    showModal();

    const prevUser   = e.id_user;
    const prevSalle  = e.id_salle;
    const prevModule = e.id_module;
    setTimeout(() => {
        if (prevUser   && document.getElementById('m-user'))   document.getElementById('m-user').value   = prevUser;
        if (prevSalle  && document.getElementById('m-salle'))  document.getElementById('m-salle').value  = prevSalle;
        if (prevModule && document.getElementById('m-module')) {
            document.getElementById('m-module').value = prevModule;
            updateModuleProgress();
        }
    }, 450);
}

function showModal()  { document.getElementById('emploi-modal').classList.add('open');    }
function closeModal() { document.getElementById('emploi-modal').classList.remove('open'); }

function openDeleteModal(action, groupe, module, date, span, heureDebut, heureFin, salle) {
    document.getElementById('delete-form').action = action;
    document.getElementById('delete-session-label').textContent = groupe + ' — ' + module;
    document.getElementById('delete-session-meta').textContent  = date + ' · ' + span + ' · ' + heureDebut + ' → ' + heureFin + ' · ' + salle;
    document.getElementById('delete-modal').style.display = 'flex';
}
function closeDeleteModal() { document.getElementById('delete-modal').style.display = 'none'; }

function openLienModal(id, currentLien, groupeLabel, dateMeta) {
    document.getElementById('lien-form').action  = '/emplois/' + id + '/lien';
    document.getElementById('lien-input').value  = currentLien || '';
    document.getElementById('lien-session-label').textContent = groupeLabel || 'Séance';
    document.getElementById('lien-session-meta').textContent  = dateMeta   || '';
    document.getElementById('lien-modal').style.display = 'flex';
}
function closeLienModal() { document.getElementById('lien-modal').style.display = 'none'; }
function submitLien() {
    document.getElementById('lien-hidden').value = document.getElementById('lien-input').value;
    document.getElementById('lien-form').submit();
}

function openRemplacantModal(emploiId, sessionLabel, sessionMeta, currentRemplacantId) {
    document.getElementById('remplacant-form').action = '/emplois/' + emploiId + '/remplacant';
    document.getElementById('remplacant-session-label').textContent = sessionLabel;
    document.getElementById('remplacant-session-meta').textContent  = sessionMeta;
    const sel = document.getElementById('remplacant-select');
    sel.value = currentRemplacantId ? String(currentRemplacantId) : '';
    document.getElementById('remplacant-modal').style.display = 'flex';
}
function closeRemplacantModal() {
    document.getElementById('remplacant-modal').style.display = 'none';
}

function openReportModal(emploiId, sessionLabel, dateLabel, dateStr, spanLabel, heureDebut, heureFin) {
    document.getElementById('report-emploi-id').value           = emploiId;
    document.getElementById('report-session-label').textContent = sessionLabel;
    document.getElementById('report-current-date').textContent  = dateLabel;
    document.getElementById('report-current-time').textContent  = spanLabel + ' · ' + heureDebut + ' → ' + heureFin;
    const ta = document.getElementById('report-raison');
    if (ta) { ta.value = ''; document.getElementById('report-raison-count').textContent = '0 / 1000'; }
    document.getElementById('report-modal').style.display = 'flex';
}
function closeReportModal() {
    document.getElementById('report-modal').style.display = 'none';
}

const reportRaison = document.getElementById('report-raison');
if (reportRaison) {
    reportRaison.addEventListener('input', function() {
        document.getElementById('report-raison-count').textContent = this.value.length + ' / 1000';
    });
}
</script>

@endsection