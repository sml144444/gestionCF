@extends('layouts.app')
@section('title', 'Mes absences & retards')
@section('page-title', 'Absences & Retards')

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
        ? ($filterStagiaire ? 'Absences de '.$filterStagiaire->name : 'Toutes les absences & retards')
        : 'Mes absences & retards';
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

.abs-wrap        { font-family:'Segoe UI',system-ui,sans-serif; max-width:1200px; margin:0 auto; }
.abs-hero        { background:var(--accent-gr); border-radius:20px; padding:28px 32px; margin-bottom:24px; display:flex; align-items:center; justify-content:space-between; gap:16px; flex-wrap:wrap; position:relative; overflow:hidden; }
.abs-hero::after { content:''; position:absolute; right:-40px; top:-40px; width:200px; height:200px; border-radius:50%; background:rgba(255,255,255,0.06); pointer-events:none; }
.abs-hero-icon   { width:52px; height:52px; border-radius:16px; background:rgba(255,255,255,0.15); display:flex; align-items:center; justify-content:center; flex-shrink:0; }
.abs-hero-title  { font-size:20px; font-weight:800; color:white; margin:0; }
.abs-hero-sub    { font-size:12px; color:rgba(255,255,255,0.75); margin-top:3px; }

/* Stats row */
.stats-grid      { display:grid; grid-template-columns:repeat(auto-fill,minmax(160px,1fr)); gap:14px; margin-bottom:22px; }
.stat-card       { background:white; border-radius:16px; border:1px solid #e2e8f0; padding:16px 18px; display:flex; align-items:center; gap:12px; transition:all .2s; }
.stat-card:hover { transform:translateY(-2px); box-shadow:0 8px 20px rgba(0,0,0,0.08); }
.stat-icon       { width:40px; height:40px; border-radius:12px; display:flex; align-items:center; justify-content:center; flex-shrink:0; font-size:18px; }
.stat-val        { font-size:22px; font-weight:900; line-height:1; }
.stat-lbl        { font-size:10px; font-weight:600; color:#94a3b8; margin-top:2px; text-transform:uppercase; letter-spacing:.5px; }

/* Filters */
.filter-bar      { background:white; border-radius:16px; border:1px solid #e2e8f0; padding:16px 20px; margin-bottom:20px; display:flex; flex-wrap:wrap; gap:12px; align-items:flex-end; }
.filter-group    { display:flex; flex-direction:column; gap:5px; min-width:160px; }
.filter-label    { font-size:10px; font-weight:700; color:#94a3b8; text-transform:uppercase; letter-spacing:.8px; }
.filter-select, .filter-input {
    font-size:12px; font-weight:500; padding:8px 12px; border:1.5px solid #e2e8f0;
    border-radius:10px; background:white; color:#1e293b; outline:none; cursor:pointer;
    transition:border-color .15s;
}
.filter-select:focus, .filter-input:focus { border-color:var(--accent); }
.btn-filter      { font-size:12px; font-weight:700; padding:9px 20px; border-radius:10px; background:var(--accent-gr); color:white; border:none; cursor:pointer; transition:opacity .15s; white-space:nowrap; }
.btn-filter:hover { opacity:.88; }
.btn-reset       { font-size:11px; font-weight:600; padding:9px 14px; border-radius:10px; background:white; color:#64748b; border:1.5px solid #e2e8f0; cursor:pointer; text-decoration:none; display:inline-flex; align-items:center; gap:5px; }
.btn-reset:hover { border-color:#cbd5e1; background:#f8fafc; }

/* Table */
.abs-table-wrap  { background:white; border-radius:18px; border:1px solid #e2e8f0; overflow:hidden; }
.abs-table-head  { padding:16px 20px; border-bottom:1px solid #f1f5f9; display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:10px; }
table.abs-table  { width:100%; border-collapse:collapse; }
table.abs-table thead th {
    padding:11px 16px; font-size:10px; font-weight:800; color:#94a3b8;
    text-transform:uppercase; letter-spacing:.8px; background:#f8fafc;
    border-bottom:1px solid #e2e8f0; text-align:left; white-space:nowrap;
}
table.abs-table tbody tr { border-bottom:1px solid #f1f5f9; transition:background .12s; }
table.abs-table tbody tr:last-child { border-bottom:none; }
table.abs-table tbody tr:hover { background:#fafbfd; }
table.abs-table tbody td { padding:14px 16px; font-size:12px; color:#374151; vertical-align:middle; }

/* Badges */
.badge           { display:inline-flex; align-items:center; gap:5px; padding:4px 10px; border-radius:8px; font-size:10px; font-weight:800; }
.badge-absence   { background:#fee2e2; color:#dc2626; border:1px solid #fecaca; }
.badge-retard    { background:#fef3c7; color:#d97706; border:1px solid #fde68a; }
.badge-justifie  { background:#d1fae5; color:#059669; border:1px solid #a7f3d0; }
.badge-injustifie{ background:#fce7f3; color:#be185d; border:1px solid #fbcfe8; }

/* Avatar */
.avatar          { width:32px; height:32px; border-radius:10px; background:var(--accent-lt); display:inline-flex; align-items:center; justify-content:center; font-size:11px; font-weight:800; color:var(--accent-tx); flex-shrink:0; }

/* Flash */
.flash-ok        { display:flex; align-items:center; gap:12px; padding:14px 18px; border-radius:14px; margin-bottom:18px; background:var(--accent-ltr); border:1px solid var(--accent-bd); animation:fadeIn .3s ease; }
.flash-ok-icon   { width:38px; height:38px; border-radius:50%; background:var(--accent-gr); display:flex; align-items:center; justify-content:center; flex-shrink:0; }
@keyframes fadeIn { from{opacity:0;transform:translateY(-6px)} to{opacity:1;transform:translateY(0)} }

/* Empty state */
.empty-state     { padding:60px 20px; text-align:center; }
.empty-state-icon{ width:64px; height:64px; border-radius:20px; background:var(--accent-lt); margin:0 auto 16px; display:flex; align-items:center; justify-content:center; font-size:28px; }

/* Pagination */
.pagination-wrap { padding:14px 20px; border-top:1px solid #f1f5f9; display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:8px; }

@media(max-width:768px) {
    .abs-hero { padding:20px; }
    table.abs-table thead th:nth-child(4),
    table.abs-table tbody td:nth-child(4),
    table.abs-table thead th:nth-child(5),
    table.abs-table tbody td:nth-child(5) { display:none; }
}
</style>

<div class="abs-wrap">

{{-- Flash messages --}}
@if(session('success'))
<div class="flash-ok">
    <div class="flash-ok-icon">
        <svg width="18" height="18" fill="none" stroke="white" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
        </svg>
    </div>
    <p style="font-size:13px;font-weight:600;color:var(--accent-tx);margin:0;">{{ session('success') }}</p>
</div>
@endif

{{-- ═══ HERO ═══ --}}
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
                <strong style="color:white;">{{ $stats['total'] }}</strong> enregistrement(s) trouvé(s)
            </p>
        </div>
    </div>
    <span style="background:rgba(255,255,255,0.15);border:1px solid rgba(255,255,255,0.25);color:white;font-size:11px;font-weight:700;padding:6px 14px;border-radius:99px;">
        {{ ucfirst($userRole) }}
    </span>
</div>

{{-- ═══ STATS ═══ --}}
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-icon" style="background:#f1f5f9;">📋</div>
        <div>
            <div class="stat-val" style="color:#1e293b;">{{ $stats['total'] }}</div>
            <div class="stat-lbl">Total</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon" style="background:#fee2e2;">❌</div>
        <div>
            <div class="stat-val" style="color:#dc2626;">{{ $stats['absences'] }}</div>
            <div class="stat-lbl">Absences</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon" style="background:#fef3c7;">⏰</div>
        <div>
            <div class="stat-val" style="color:#d97706;">{{ $stats['retards'] }}</div>
            <div class="stat-lbl">Retards</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon" style="background:#d1fae5;">✅</div>
        <div>
            <div class="stat-val" style="color:#059669;">{{ $stats['justifies'] }}</div>
            <div class="stat-lbl">Justifiés</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon" style="background:#fce7f3;">⚠️</div>
        <div>
            <div class="stat-val" style="color:#be185d;">{{ $stats['injustifies'] }}</div>
            <div class="stat-lbl">Non justifiés</div>
        </div>
    </div>
</div>

{{-- ═══ FILTER BAR ═══ --}}
<form method="GET" action="{{ route('absences.index') }}" class="filter-bar">

    {{-- Type filter --}}
    <div class="filter-group">
        <label class="filter-label">Type</label>
        <select name="type" class="filter-select">
            <option value="">Tous les types</option>
            <option value="absence"  @selected(request('type') === 'absence')>Absence</option>
            <option value="retard"   @selected(request('type') === 'retard')>Retard</option>
        </select>
    </div>

    {{-- Justification filter --}}
    <div class="filter-group">
        <label class="filter-label">Justification</label>
        <select name="justifie" class="filter-select">
            <option value="">Tous</option>
            <option value="1" @selected(request('justifie') === '1')>Justifié</option>
            <option value="0" @selected(request('justifie') === '0')>Non justifié</option>
        </select>
    </div>

    @if($canViewAll)
    {{-- Groupe filter (staff only) --}}
    <div class="filter-group">
        <label class="filter-label">Groupe</label>
        <select name="groupe_id" class="filter-select" id="groupeSelect" onchange="this.form.submit()">
            <option value="">Tous les groupes</option>
            @foreach($groupes as $groupe)
                <option value="{{ $groupe->id }}" @selected(request('groupe_id') == $groupe->id)>
                    {{ $groupe->name }}
                </option>
            @endforeach
        </select>
    </div>

    {{-- Stagiaire filter (staff only) --}}
    <div class="filter-group">
        <label class="filter-label">Stagiaire</label>
        <select name="stagiaire_id" class="filter-select">
            <option value="">Tous les stagiaires</option>
            @foreach($stagiaires as $s)
                <option value="{{ $s->id }}" @selected(request('stagiaire_id') == $s->id)>
                    {{ $s->name }}
                </option>
            @endforeach
        </select>
    </div>
    @endif

    <button type="submit" class="btn-filter">
        🔍 Filtrer
    </button>

    @if(request()->hasAny(['type','justifie','groupe_id','stagiaire_id']))
        <a href="{{ route('absences.index') }}" class="btn-reset">
            <svg width="11" height="11" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/>
            </svg>
            Réinitialiser
        </a>
    @endif
</form>

{{-- ═══ TABLE ═══ --}}
<div class="abs-table-wrap">
    <div class="abs-table-head">
        <div>
            <div style="font-size:10px;font-weight:800;color:#94a3b8;letter-spacing:1.5px;text-transform:uppercase;">
                📋 Historique des absences & retards
            </div>
            <div style="font-size:12px;color:#64748b;margin-top:2px;">
                {{ $absences->total() }} résultat(s) — page {{ $absences->currentPage() }}/{{ $absences->lastPage() }}
            </div>
        </div>
    </div>

    @if($absences->isEmpty())
        <div class="empty-state">
            <div class="empty-state-icon">🎉</div>
            <p style="font-size:15px;font-weight:700;color:#1e293b;margin:0 0 6px;">Aucune absence enregistrée</p>
            <p style="font-size:12px;color:#94a3b8;margin:0;">
                {{ request()->hasAny(['type','justifie','groupe_id','stagiaire_id'])
                    ? 'Aucun résultat pour ces filtres. Essayez de les modifier.'
                    : 'Parfait ! Aucune absence ou retard pour le moment.' }}
            </p>
        </div>
    @else
        <div style="overflow-x:auto;">
        <table class="abs-table">
            <thead>
                <tr>
                    <th>Date</th>
                    @if($canViewAll)
                        <th>Stagiaire</th>
                        <th>Groupe</th>
                    @endif
                    <th>Module / Séance</th>
                    <th>Formateur</th>
                    <th>Type</th>
                    <th>Durée</th>
                    <th>Justification</th>
                    <th>Justificatif</th> 
                </tr>
            </thead>
            <tbody>
            @foreach($absences as $abs)
                    @php
                        $emploi  = $abs->cours?->emploiDuTemps;
                        $module  = $emploi?->module;
                        $groupe  = $emploi?->groupe;
                        $form    = $emploi?->gestionnaire;  // ← was $emploi?->formateur
                        $initials = strtoupper(mb_substr($abs->stagiaire?->name ?? '?', 0, 1)
                                . mb_substr(explode(' ', $abs->stagiaire?->name ?? '')[1] ?? '', 0, 1));
                    @endphp
                <tr>
                    {{-- Date --}}
                    <td>
                        <div style="font-weight:700;color:#1e293b;">
                            {{ $abs->date_event ? $abs->date_event->format('d/m/Y') : '—' }}
                        </div>
                        <div style="font-size:10px;color:#94a3b8;">
                            {{ $abs->date_event ? $abs->date_event->translatedFormat('l') : '' }}
                        </div>
                        @if($emploi)
                            <div style="font-size:10px;color:#94a3b8;">
                                {{ \Carbon\Carbon::parse($emploi->heure_debut)->format('H:i') }}
                                – {{ \Carbon\Carbon::parse($emploi->heure_fin)->format('H:i') }}
                            </div>
                        @endif
                    </td>

                    {{-- Stagiaire (staff only) --}}
                    @if($canViewAll)
                    <td>
                        <div style="display:flex;align-items:center;gap:8px;">
                            <div class="avatar">{{ $initials }}</div>
                            <div>
                                <div style="font-weight:600;color:#1e293b;">
                                    {{ $abs->stagiaire?->name ?? '—' }}
                                </div>
                                <div style="font-size:10px;color:#94a3b8;">
                                    {{ $abs->stagiaire?->email ?? '' }}
                                </div>
                            </div>
                        </div>
                    </td>
                    <td>
                        <span style="font-size:11px;font-weight:600;color:#475569;">
                            {{ $groupe?->nom ?? '—' }}
                        </span>
                    </td>
                    @endif

{{-- Module / Séance --}}
<td>
    <div style="font-weight:600;color:#1e293b;">
        {{ $module?->name ?? '—' }}  {{-- ← nom → name --}}
    </div>
    @if($emploi?->salle)
        <div style="font-size:10px;color:#94a3b8;">
            🏫 {{ $emploi->salle->name }}  {{-- ← nom → name --}}
        </div>
    @endif
</td>

                    {{-- Formateur --}}
                    <td>
                        <span style="font-size:11px;color:#475569;font-weight:500;">
                            {{ $form?->name ?? ($emploi?->formateur_nom ?? '—') }}
                        </span>
                    </td>

                    {{-- Type --}}
                    <td>
                        @if($abs->type === 'absence')
                            <span class="badge badge-absence">❌ Absence</span>
                        @else
                            <span class="badge badge-retard">⏰ Retard</span>
                        @endif
                    </td>

                    {{-- Durée --}}
                    <td>
                        <span style="font-size:12px;color:#475569;">
                            @php
                                $heures = $abs->duree ?? ($emploi ? $emploi->date_debut->diffInMinutes($emploi->date_fin) / 60 : null);
                            @endphp
                            {{ $heures ? round($heures, 1) . ' h' : '—' }}
                        </span>
                    </td>

{{-- Justification badge --}}
<td>
    @if($canViewAll)
        {{-- Admin/Gestionnaire: clickable toggle button --}}
        <form method="POST"
              action="{{ route('absences.justify', $abs) }}"
              style="display:inline;">
            @csrf
            @method('PATCH')
            <button type="submit"
                    title="Cliquer pour {{ $abs->justifie ? 'marquer non justifié' : 'marquer justifié' }}"
                    style="background:none; border:none; cursor:pointer; padding:0;">
                @if($abs->justifie)
                    <span class="badge badge-justifie" style="cursor:pointer;">
                        ✅ Justifié
                        <svg width="10" height="10" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="margin-left:3px;">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15.232 5.232l3.536 3.536M9 13l6.5-6.5M3 21h4l11-11a2.828 2.828 0 00-4-4L3 17v4z"/>
                        </svg>
                    </span>
                @else
                    <span class="badge badge-injustifie" style="cursor:pointer;">
                        ⚠️ Non justifié
                        <svg width="10" height="10" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="margin-left:3px;">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15.232 5.232l3.536 3.536M9 13l6.5-6.5M3 21h4l11-11a2.828 2.828 0 00-4-4L3 17v4z"/>
                        </svg>
                    </span>
                @endif
            </button>
        </form>
    @else
        {{-- Stagiaire: read-only badge --}}
        @if($abs->justifie)
            <span class="badge badge-justifie">✅ Justifié</span>
        @else
            <span class="badge badge-injustifie">⚠️ Non justifié</span>
        @endif
    @endif
</td>
{{-- Justificatif file --}}
<td>
    @if($canViewAll)
        {{-- Admin / Gestionnaire: upload + delete --}}
        @if($abs->file_justification)
            <div style="display:flex; flex-direction:column; gap:6px;">
                <a href="{{ Storage::url($abs->file_justification) }}"
                   target="_blank"
                   style="font-size:11px; font-weight:600; color:var(--accent);
                          text-decoration:none; display:inline-flex; align-items:center; gap:4px;">
                    <svg width="12" height="12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/>
                    </svg>
                    Voir le fichier
                </a>
                {{-- Delete button --}}
                <form method="POST" action="{{ route('absences.fichier.delete', $abs) }}"
                      onsubmit="return confirm('Supprimer ce fichier ?')"
                      style="display:inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit"
                            style="font-size:10px; font-weight:600; color:#dc2626; background:none;
                                   border:none; cursor:pointer; padding:0; display:inline-flex;
                                   align-items:center; gap:3px;">
                        <svg width="10" height="10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                        </svg>
                        Supprimer
                    </button>
                </form>
            </div>
        @else
            {{-- Upload form --}}
            <form method="POST"
                  action="{{ route('absences.fichier', $abs) }}"
                  enctype="multipart/form-data"
                  style="display:flex; align-items:center; gap:6px;">
                @csrf
                <label style="display:inline-flex; align-items:center; gap:4px; cursor:pointer;
                              font-size:10px; font-weight:600; color:var(--accent);
                              padding:4px 10px; border-radius:8px;
                              border:1.5px dashed var(--accent-bd);
                              background:var(--accent-ltr); white-space:nowrap;">
                    <svg width="11" height="11" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                    </svg>
                    Joindre fichier
                    <input type="file"
                           name="file_justification"
                           accept=".pdf,.doc,.docx,.jpg,.jpeg,.png"
                           style="display:none;"
                           onchange="this.closest('form').submit()">
                </label>
            </form>
        @endif
    @else
        {{-- Stagiaire: read-only --}}
        @if($abs->file_justification)
            <a href="{{ Storage::url($abs->file_justification) }}"
               target="_blank"
               style="font-size:11px; font-weight:600; color:var(--accent);
                      text-decoration:none; display:inline-flex; align-items:center; gap:4px;">
                <svg width="12" height="12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/>
                </svg>
                Voir le fichier
            </a>
        @else
            <span style="font-size:11px; color:#cbd5e1;">—</span>
        @endif
    @endif
</td>
                </tr>
            @endforeach
            </tbody>
        </table>
        </div>

        {{-- Pagination --}}
        @if($absences->hasPages())
        <div class="pagination-wrap">
            <span style="font-size:11px;color:#94a3b8;">
                Affichage {{ $absences->firstItem() }}–{{ $absences->lastItem() }}
                sur {{ $absences->total() }} résultat(s)
            </span>
            <div style="display:flex;gap:6px;">
                @if($absences->onFirstPage())
                    <span style="padding:6px 12px;border-radius:8px;background:#f1f5f9;color:#cbd5e1;font-size:12px;font-weight:600;">←</span>
                @else
                    <a href="{{ $absences->previousPageUrl() }}" style="padding:6px 12px;border-radius:8px;background:white;border:1.5px solid #e2e8f0;color:#475569;font-size:12px;font-weight:600;text-decoration:none;">←</a>
                @endif

                @foreach($absences->getUrlRange(max(1, $absences->currentPage()-2), min($absences->lastPage(), $absences->currentPage()+2)) as $page => $url)
                    @if($page == $absences->currentPage())
                        <span style="padding:6px 12px;border-radius:8px;background:var(--accent-gr);color:white;font-size:12px;font-weight:700;">{{ $page }}</span>
                    @else
                        <a href="{{ $url }}" style="padding:6px 12px;border-radius:8px;background:white;border:1.5px solid #e2e8f0;color:#475569;font-size:12px;font-weight:600;text-decoration:none;">{{ $page }}</a>
                    @endif
                @endforeach

                @if($absences->hasMorePages())
                    <a href="{{ $absences->nextPageUrl() }}" style="padding:6px 12px;border-radius:8px;background:white;border:1.5px solid #e2e8f0;color:#475569;font-size:12px;font-weight:600;text-decoration:none;">→</a>
                @else
                    <span style="padding:6px 12px;border-radius:8px;background:#f1f5f9;color:#cbd5e1;font-size:12px;font-weight:600;">→</span>
                @endif
            </div>
        </div>
        @endif
    @endif
</div>

</div>
@endsection

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