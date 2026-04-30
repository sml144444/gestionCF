{{-- resources/views/controles/notes.blade.php --}}
@extends('layouts.app')
@section('title', 'Notes — ' . $module->name)
@section('page-title', 'Saisie des notes')

@section('content')

@php
    $palettes = [
        'admin'        => ['primary' => '#0a6640', 'light' => '#e8f5ee', 'text' => '#065f38', 'shadow' => 'rgba(10,102,64,0.2)'],
        'gestionnaire' => ['primary' => '#1e293b', 'light' => '#f1f5f9', 'text' => '#1e293b', 'shadow' => 'rgba(30,41,59,0.2)'],
    ];
    $p      = $palettes[Auth::user()->role] ?? $palettes['gestionnaire'];
    $accent = $p['primary'];
    $light  = $p['light'];
    $text   = $p['text'];
    $shadow = $p['shadow'];
@endphp

<style>
.notes-wrap { font-family:'Segoe UI',system-ui,sans-serif; }

/* ── Breadcrumb ── */
.breadcrumb { display:flex;align-items:center;gap:8px;font-size:12px;color:#94a3b8;margin-bottom:18px;flex-wrap:wrap; }
.breadcrumb a { color:{{ $accent }};font-weight:600;text-decoration:none; }
.breadcrumb a:hover { text-decoration:underline; }
.breadcrumb-sep { color:#cbd5e1; }

/* ── Module info card ── */
.mod-info { background:white;border-radius:16px;border:1px solid #e2e8f0;padding:20px 24px;margin-bottom:20px;display:flex;align-items:flex-start;gap:20px;flex-wrap:wrap; }
.mod-info-badge { display:inline-flex;align-items:center;font-size:9px;font-weight:800;padding:2px 8px;border-radius:99px;text-transform:uppercase;letter-spacing:.5px; }
.mod-info-badge-regional { background:#eff6ff;color:#1e40af;border:1px solid #bfdbfe; }
.mod-info-badge-local    { background:#fdf4ff;color:#7e22ce;border:1px solid #e9d5ff; }

/* ── Groupe selector ── */
.groupe-selector { background:white;border-radius:14px;border:1px solid #e2e8f0;padding:18px 20px;margin-bottom:20px; }
.grp-label { display:block;font-size:9px;font-weight:800;color:#94a3b8;letter-spacing:1.5px;text-transform:uppercase;margin-bottom:8px; }
.grp-select { height:44px;padding:0 14px;border-radius:10px;border:1.5px solid #e2e8f0;background:#f8fafc;font-size:13px;color:#1e293b;outline:none;transition:border-color .15s;width:100%;max-width:360px; }
.grp-select:focus { border-color:{{ $accent }};background:white; }

/* ── Buttons ── */
.n-btn { display:inline-flex;align-items:center;gap:6px;padding:9px 16px;font-size:12px;font-weight:700;border-radius:10px;border:none;cursor:pointer;transition:all .15s;text-decoration:none; }
.n-btn:hover { opacity:.87; }
.n-btn-primary { background:{{ $accent }};color:white;box-shadow:0 4px 14px {{ $shadow }}; }
.n-btn-ghost   { background:white;border:1.5px solid #e2e8f0;color:#475569; }
.n-btn-green   { background:#16a34a;color:white;box-shadow:0 4px 14px rgba(22,163,74,0.25); }

/* ── Notes table container ── */
.notes-card { background:white;border-radius:16px;border:1px solid #e2e8f0;overflow:hidden;margin-bottom:24px; }
.notes-card-head { padding:14px 20px;border-bottom:1px solid #f1f5f9;display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:10px; }
.notes-table-wrap { overflow-x:auto; }

/* ── Notes table ── */
.notes-table { width:100%;border-collapse:collapse;font-size:12px;min-width:600px; }
.notes-table th {
    padding:11px 14px;text-align:left;font-size:9px;font-weight:800;
    color:#94a3b8;text-transform:uppercase;letter-spacing:.9px;
    background:#f8fafc;border-bottom:2px solid #f1f5f9;white-space:nowrap;
}
.notes-table th.col-name  { min-width:180px;position:sticky;left:0;z-index:2;background:#f8fafc; }
.notes-table th.col-ctrl  { min-width:110px;text-align:center; }
.notes-table th.col-efm   { min-width:110px;text-align:center;color:#dc2626; }
.notes-table th.col-moy   { min-width:120px;text-align:center;color:#7c3aed; }

.notes-table td {
    padding:10px 14px;
    border-bottom:1px solid #f8fafc;
    vertical-align:middle;
}
.notes-table tr:last-child td { border-bottom:none; }
.notes-table tr:hover td      { background:#fafbff; }

/* ── Sticky name column ── */
.notes-table td.col-name-cell {
    position:sticky;left:0;z-index:1;background:white;
    font-weight:600;color:#1e293b;border-right:1px solid #f1f5f9;
}
.notes-table tr:hover td.col-name-cell { background:#fafbff; }

/* ── Note input ── */
.note-input {
    width:84px;height:36px;text-align:center;
    border-radius:9px;border:1.5px solid #e2e8f0;
    background:#f8fafc;font-size:13px;font-weight:700;color:#1e293b;
    outline:none;transition:all .15s;
    display:block;margin:0 auto;
}
.note-input:focus {
    border-color:{{ $accent }};background:white;
    box-shadow:0 0 0 3px {{ $accent }}20;
}
.note-input.efm-input:focus {
    border-color:#dc2626;box-shadow:0 0 0 3px rgba(220,38,38,.12);
}

/* ── Grade color coding (applied via JS) ── */
.grade-high   { color:#16a34a !important; }
.grade-mid    { color:#d97706 !important; }
.grade-low    { color:#dc2626 !important; }
.grade-none   { color:#94a3b8 !important; }

/* ── Moyenne cell ── */
.moy-cell { font-weight:800;font-size:13px;text-align:center; }

/* ── Moyenne badge (only shown when complete) ── */
.moy-badge {
    display:inline-flex;align-items:center;justify-content:center;
    padding:4px 12px;border-radius:99px;font-size:12px;font-weight:800;
    min-width:64px;
}
.moy-badge-high { background:#f0fdf4;color:#16a34a;border:1.5px solid #bbf7d0; }
.moy-badge-mid  { background:#fffbeb;color:#d97706;border:1.5px solid #fde68a; }
.moy-badge-low  { background:#fff1f2;color:#dc2626;border:1.5px solid #fecdd3; }
.moy-badge-none { background:#f8fafc;color:#94a3b8;border:1.5px solid #e2e8f0;font-size:11px;font-weight:600; }

/* ── Pending hint inside moyenne cell ── */
.moy-pending {
    display:inline-flex;align-items:center;gap:4px;
    font-size:10px;color:#94a3b8;font-weight:500;font-style:italic;
}

/* ── Empty state ── */
.empty-box { padding:52px 32px;text-align:center; }
.empty-icon { width:56px;height:56px;border-radius:16px;background:{{ $light }};display:flex;align-items:center;justify-content:center;margin:0 auto 14px; }

/* ── Flash ── */
.flash-ok   { padding:12px 16px;border-radius:12px;font-size:13px;display:flex;align-items:center;gap:8px;background:#f0fdf4;border:1px solid #bbf7d0;color:#15803d;margin-bottom:16px; }
.flash-err  { padding:12px 16px;border-radius:12px;font-size:13px;display:flex;align-items:center;gap:8px;background:#fff1f2;border:1px solid #fecdd3;color:#be123c;margin-bottom:16px; }

/* ── Info chips ── */
.info-chip { display:inline-flex;align-items:center;gap:4px;padding:3px 10px;border-radius:99px;font-size:10px;font-weight:600;background:#f1f5f9;color:#475569;border:1px solid #e2e8f0; }
</style>

<div class="notes-wrap">

{{-- ── FLASH ── --}}
@if(session('success'))
<div class="flash-ok">✓ {{ session('success') }}</div>
@endif
@if(session('error'))
<div class="flash-err">✕ {{ session('error') }}</div>
@endif

{{-- ── BREADCRUMB ── --}}
<div class="breadcrumb">
    <a href="{{ route('controles.index') }}">Contrôles & Notes</a>
    <span class="breadcrumb-sep">›</span>
    <span style="color:#1e293b;font-weight:600;">{{ $module->name }}</span>
    @if($selectedGroupe)
        <span class="breadcrumb-sep">›</span>
        <span style="color:#64748b;">{{ $selectedGroupe->name }}</span>
    @endif
</div>

{{-- ── MODULE INFO CARD ── --}}
<div class="mod-info">
    <div style="flex:1;min-width:200px;">
        <div style="display:flex;align-items:center;gap:10px;flex-wrap:wrap;margin-bottom:8px;">
            <h2 style="font-size:17px;font-weight:800;color:#0f172a;margin:0;">{{ $module->name }}</h2>
            <span class="mod-info-badge mod-info-badge-{{ $module->type }}">
                {{ $module->type === 'regional' ? '🌍 Régional' : '📍 Local' }}
            </span>
        </div>
        <div style="display:flex;flex-wrap:wrap;gap:8px;">
            <span class="info-chip">
                <svg width="11" height="11" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                {{ $module->formateur->name ?? '—' }}
            </span>
            <span class="info-chip">
                <svg width="11" height="11" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                {{ $module->nbr_heure }}h · Coeff. {{ $module->coefficience }}
            </span>
            <span class="info-chip" style="background:#f0fdf4;color:#166534;border-color:#bbf7d0;">
                <svg width="11" height="11" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                {{ $module->nbr_controles ?? 1 }} contrôle{{ ($module->nbr_controles ?? 1) > 1 ? 's' : '' }} + EFM
            </span>
            @if($module->annee)
            <span class="info-chip">
                {{ $module->annee }}ème Année
            </span>
            @endif
        </div>
    </div>

    {{-- Quick edit nbr_controles ── admin + gestionnaire only --}}
    @if(in_array(Auth::user()->role, ['admin', 'gestionnaire']))
    <form method="POST" action="{{ route('controles.update-nbr', $module->id) }}"
          style="display:flex;align-items:flex-end;gap:10px;flex-wrap:wrap;"
          title="Modifier le nombre de contrôles">
        @csrf @method('PATCH')
        @if($selectedGroupe)
            <input type="hidden" name="groupe_id" value="{{ $selectedGroupe->id }}">
        @endif
        @if($promoFilter)
            <input type="hidden" name="promo" value="{{ $promoFilter }}">
        @endif
        <div>
            <label style="display:block;font-size:9px;font-weight:800;color:#94a3b8;text-transform:uppercase;letter-spacing:1px;margin-bottom:5px;">
                Nbre contrôles
            </label>
            <input type="number" name="nbr_controles"
                   value="{{ $module->nbr_controles ?? 1 }}"
                   min="0" max="10"
                   style="width:70px;height:36px;text-align:center;border-radius:9px;border:1.5px solid #e2e8f0;background:#f8fafc;font-size:13px;font-weight:700;outline:none;padding:0 8px;"
                   title="0 = EFM uniquement">
        </div>
        <button type="submit" class="n-btn n-btn-ghost" style="height:36px;font-size:11px;">
            Mettre à jour
        </button>
    </form>
    @endif
</div>

{{-- ── GROUPE SELECTOR ── --}}
<div class="groupe-selector">
    <form method="GET" action="{{ route('controles.notes', $module->id) }}"
          style="display:flex;align-items:flex-end;gap:12px;flex-wrap:wrap;">

        {{-- ── PROMO FILTER (new) ── --}}
        <div>
            <label class="grp-label">Promotion</label>
            <select id="promo-select" name="promo" class="grp-select" style="max-width:180px;"
                    onchange="filterGroupesByPromo()">
                <option value="">🎓 Toutes promos</option>
                @foreach($promos as $pr)
                    <option value="{{ $pr }}" {{ $promoFilter == $pr ? 'selected' : '' }}>
                        Promo {{ $pr }}
                    </option>
                @endforeach
            </select>
        </div>

        <div style="flex:1;">
            <label class="grp-label">Sélectionner un groupe</label>
            <select name="groupe_id" id="groupe-select" class="grp-select">
                <option value="">— Choisir un groupe —</option>
                @forelse($groupes as $g)
                    <option value="{{ $g->id }}"
                            data-promo="{{ $g->promo }}"
                            {{ optional($selectedGroupe)->id == $g->id ? 'selected' : '' }}>
                        {{ $g->name }}
                        @if($g->promo) ({{ $g->promo }}) @endif
                        · {{ $g->stagiaires()->count() }} stagiaire{{ $g->stagiaires()->count() > 1 ? 's' : '' }}
                    </option>
                @empty
                    <option disabled>Aucun groupe pour cette filière / année</option>
                @endforelse
            </select>
        </div>

        <button type="submit" class="n-btn n-btn-primary">
            <svg width="13" height="13" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
            Afficher
        </button>
        @if($selectedGroupe)
        <a href="{{ route('controles.notes', $module->id) }}" class="n-btn n-btn-ghost">
            Changer de groupe
        </a>
        @endif
    </form>

    {{-- Promo filter active badge --}}
    @if($promoFilter)
    <div style="margin-top:10px;display:flex;align-items:center;gap:6px;font-size:11px;color:#0369a1;">
        <svg width="12" height="12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2a1 1 0 01-.293.707L13 13.414V19a1 1 0 01-.553.894l-4 2A1 1 0 017 21v-7.586L3.293 6.707A1 1 0 013 6V4z"/></svg>
        Groupes filtrés par promo <strong>{{ $promoFilter }}</strong>
        <a href="{{ route('controles.notes', $module->id) }}"
           style="margin-left:4px;color:#dc2626;font-weight:700;text-decoration:none;">
           × Réinitialiser
        </a>
    </div>
    @endif
</div>

{{-- ── NOTES TABLE ── --}}
@if($selectedGroupe)

    @php
        $nbr       = $module->nbr_controles ?? 1;
        $totalCols = $controles->count() + 1;
    @endphp

    <div class="notes-card">

        {{-- Card header --}}
        <div class="notes-card-head">
            <div>
                <div style="font-size:14px;font-weight:800;color:#0f172a;">
                    Notes — {{ $selectedGroupe->name }}
                </div>
                <div style="font-size:11px;color:#64748b;margin-top:2px;">
                    {{ $stagiaires->count() }} stagiaire{{ $stagiaires->count() > 1 ? 's' : '' }} ·
                    {{ $controles->count() }} contrôle{{ $controles->count() > 1 ? 's' : '' }} + EFM
                    · notes sur 20
                </div>
            </div>
            <div style="display:flex;align-items:center;gap:12px;flex-wrap:wrap;">
                {{-- Legend --}}
                <div style="display:flex;align-items:center;gap:8px;font-size:10px;color:#64748b;">
                    <span style="display:flex;align-items:center;gap:4px;"><span style="width:8px;height:8px;border-radius:50%;background:#16a34a;display:inline-block;"></span>≥ 15</span>
                    <span style="display:flex;align-items:center;gap:4px;"><span style="width:8px;height:8px;border-radius:50%;background:#d97706;display:inline-block;"></span>10–14</span>
                    <span style="display:flex;align-items:center;gap:4px;"><span style="width:8px;height:8px;border-radius:50%;background:#dc2626;display:inline-block;"></span>< 10</span>
                </div>
                {{-- Moyenne rule hint --}}
                <div style="display:flex;align-items:center;gap:5px;font-size:10px;color:#7c3aed;background:#fdf4ff;border:1px solid #e9d5ff;border-radius:8px;padding:4px 10px;">
                    <svg width="11" height="11" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    Moyenne calculée uniquement si tous les contrôles <strong style="margin:0 2px;">+</strong> EFM sont saisis
                </div>
            </div>
        </div>

        {{-- Table --}}
        @if($stagiaires->isEmpty())
            <div class="empty-box">
                <div class="empty-icon">
                    <svg width="24" height="24" fill="none" stroke="{{ $accent }}" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                </div>
                <p style="font-size:14px;font-weight:700;color:#1e293b;margin:0 0 4px;">Aucun stagiaire dans ce groupe</p>
                <p style="font-size:12px;color:#94a3b8;">Ajoutez des stagiaires au groupe depuis la gestion des utilisateurs.</p>
            </div>
        @else

        <form method="POST" action="{{ route('controles.save', $module->id) }}" id="notes-form">
            @csrf
            <input type="hidden" name="groupe_id" value="{{ $selectedGroupe->id }}">
            @if($promoFilter)
            <input type="hidden" name="promo" value="{{ $promoFilter }}">
            @endif

            {{-- Pass expected counts to JS via data attributes --}}
            <div id="js-config"
                 data-nbr-controles="{{ $controles->count() }}"
                 data-has-efm="{{ $efm ? '1' : '0' }}"
                 style="display:none;"></div>

            <div class="notes-table-wrap">
                <table class="notes-table" id="notesTable">
                    <thead>
                        <tr>
                            <th class="col-name" style="background:#f8fafc;">#</th>
                            <th class="col-name" style="min-width:200px;position:sticky;left:40px;z-index:2;background:#f8fafc;">
                                Stagiaire
                            </th>
                            @foreach($controles as $ctrl)
                            <th class="col-ctrl">{{ $ctrl->titre }}</th>
                            @endforeach
                            @if($efm)
                            <th class="col-efm">⚑ EFM</th>
                            @endif
                            <th class="col-moy">Moyenne</th>
                        </tr>
                    </thead>
                    <tbody>
                    @foreach($stagiaires as $i => $stagiaire)
                        <tr data-stagiaire="{{ $stagiaire->id }}">

                            {{-- Row number --}}
                            <td style="color:#94a3b8;font-size:11px;font-weight:600;width:36px;text-align:center;background:#fafafa;">
                                {{ $i + 1 }}
                            </td>

                            {{-- Name (sticky) --}}
                            <td class="col-name-cell" style="left:40px;">
                                <div style="font-weight:700;">{{ $stagiaire->name }}</div>
                                <div style="font-size:10px;color:#94a3b8;margin-top:1px;">{{ $stagiaire->email }}</div>
                            </td>

                            {{-- Contrôle columns --}}
                            @foreach($controles as $ctrl)
                            @php $existingNote = $notesMap[(int)$stagiaire->id][(int)$ctrl->id] ?? null; @endphp
                            <td style="text-align:center;">
                                <input
                                    type="number"
                                    name="notes[{{ $stagiaire->id }}][{{ $ctrl->id }}]"
                                    class="note-input ctrl-input"
                                    value="{{ $existingNote !== null ? $existingNote : '' }}"
                                    min="0" max="20" step="0.25"
                                    placeholder="—"
                                    data-stagiaire="{{ $stagiaire->id }}"
                                    data-max="20"
                                    oninput="colorize(this); updateMoy({{ $stagiaire->id }})"
                                    onchange="colorize(this); updateMoy({{ $stagiaire->id }})">
                            </td>
                            @endforeach

                            {{-- EFM column — max 40 --}}
                            @if($efm)
                            @php
                                $efmRaw  = $notesMap[(int)$stagiaire->id][(int)$efm->id] ?? null;
                                $efmNote = $efmRaw !== null ? round($efmRaw * 2, 2) : null;
                            @endphp
                            <td style="text-align:center;">
                                <input
                                    type="number"
                                    name="notes[{{ $stagiaire->id }}][{{ $efm->id }}]"
                                    class="note-input efm-input"
                                    value="{{ $efmNote !== null ? $efmNote : '' }}"
                                    min="0" max="40" step="0.25"
                                    placeholder="—"
                                    data-stagiaire="{{ $stagiaire->id }}"
                                    data-max="40"
                                    style="border-color:#fecdd3;"
                                    oninput="colorize(this); updateMoy({{ $stagiaire->id }})"
                                    onchange="colorize(this); updateMoy({{ $stagiaire->id }})">
                                <div style="font-size:9px;color:#94a3b8;margin-top:2px;">/ 40</div>
                            </td>
                            @endif

                            {{-- Moyenne — only shown when ALL inputs are filled --}}
                            <td class="moy-cell">
                                <span id="moy-{{ $stagiaire->id }}"></span>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>

            {{-- ── SAVE BAR ── --}}
            <div style="padding:16px 20px;border-top:2px solid #f1f5f9;display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:12px;background:#fafafa;">
                <div style="font-size:11px;color:#64748b;">
                    <span id="filled-count" style="font-weight:800;color:{{ $accent }};">0</span>
                    note{{ '' }} saisie{{ '' }} sur
                    <span style="font-weight:700;">{{ $stagiaires->count() * ($controles->count() + ($efm ? 1 : 0)) }}</span>
                </div>
                <div style="display:flex;gap:10px;">
                    <a href="{{ route('controles.index') }}" class="n-btn n-btn-ghost">
                        ← Retour aux modules
                    </a>
                    <button type="submit" class="n-btn n-btn-green">
                        <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                        Enregistrer les notes
                    </button>
                </div>
            </div>

        </form>
        @endif
    </div>

@else
    {{-- No groupe selected yet --}}
    <div style="padding:64px 32px;text-align:center;background:white;border-radius:16px;border:1px solid #e2e8f0;">
        <div class="empty-icon">
            <svg width="26" height="26" fill="none" stroke="{{ $accent }}" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
        </div>
        <p style="font-size:15px;font-weight:700;color:#1e293b;margin:0 0 6px;">Sélectionnez un groupe</p>
        <p style="font-size:12px;color:#94a3b8;">Choisissez un groupe dans le menu ci-dessus pour afficher le tableau de notes.</p>
    </div>
@endif

</div>{{-- .notes-wrap --}}

<script>
// ── Config (passed from Blade) ───────────────────────────────
const cfg        = document.getElementById('js-config');
const nbrCtrl    = cfg ? parseInt(cfg.dataset.nbrControles, 10) : 0;
const hasEfm     = cfg ? cfg.dataset.hasEfm === '1'            : false;
// Total inputs expected per row = controles + EFM (if exists)
const totalPerRow = nbrCtrl + (hasEfm ? 1 : 0);

// ── Grade color coding + real-time clamping ─────────────────
function colorize(input) {
    const max = parseFloat(input.dataset.max || 20);

    // ── Clamp: reject anything above max or below 0 ──────────
    if (input.value !== '') {
        let val = parseFloat(input.value);
        if (!isNaN(val)) {
            if (val > max) {
                input.value = max;
                // Flash red border to signal the correction
                input.style.borderColor = '#dc2626';
                input.style.boxShadow   = '0 0 0 3px rgba(220,38,38,.18)';
                clearTimeout(input._flashTimer);
                input._flashTimer = setTimeout(() => {
                    input.style.borderColor = '';
                    input.style.boxShadow   = '';
                }, 800);
            } else if (val < 0) {
                input.value = 0;
            }
        }
    }

    const val2 = parseFloat(input.value);
    input.style.color      = '';
    input.style.fontWeight = '';
    if (input.value === '' || isNaN(val2)) return;
    const pct = val2 / max;
    if (pct >= 0.75)      { input.style.color = '#16a34a'; input.style.fontWeight = '800'; }
    else if (pct >= 0.50) { input.style.color = '#d97706'; input.style.fontWeight = '700'; }
    else                  { input.style.color = '#dc2626'; input.style.fontWeight = '700'; }
}

// ── Per-row moyenne ──────────────────────────────────────────
// Only calculates and displays when ALL inputs in the row are filled.
function updateMoy(stagiaireId) {
    const row  = document.querySelector(`tr[data-stagiaire="${stagiaireId}"]`);
    const span = document.getElementById(`moy-${stagiaireId}`);
    if (!row || !span) return;

    const inputs = row.querySelectorAll('.note-input');

    // ── Check: every input must have a non-empty value ───────
    const allFilled = [...inputs].every(i => i.value !== '');

    if (!allFilled || totalPerRow === 0) {
        // Show a gentle pending hint instead of a partial average
        const missing = [...inputs].filter(i => i.value === '').length;
        span.innerHTML = `<span class="moy-pending">
            <svg width="10" height="10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            ${missing} manquant${missing > 1 ? 's' : ''}
        </span>`;
        updateFilledCount();
        return;
    }

    // ── All filled → compute moyenne (normalise each /20) ────
    let sum = 0;
    inputs.forEach(i => {
        const v   = parseFloat(i.value);
        const max = parseFloat(i.dataset.max || 20);
        sum += (v / max) * 20;
    });

    // Average of CC values (controles) and EFM, each already /20
    // Rule: moyenne = (CC + EFM) / 2
    // CC itself = average of all controle inputs
    // So: if 2 controles + EFM → we have 3 values /20
    //     CC = (C1+C2)/2  →  moyenne = (CC + EFM) / 2
    // Simplified: sum all /20 values, cc = sum_ctrl/n_ctrl, moy = (cc+efm)/2

    let ccSum = 0, ccCount = 0, efmVal = null;
    inputs.forEach(i => {
        const v   = parseFloat(i.value);
        const max = parseFloat(i.dataset.max || 20);
        const norm = (v / max) * 20;
        if (i.classList.contains('efm-input')) {
            efmVal = norm;
        } else {
            ccSum += norm;
            ccCount++;
        }
    });

    let avg;
    if (ccCount > 0 && efmVal !== null) {
        // Both CC and EFM present
        const cc = ccSum / ccCount;
        avg = (cc + efmVal) / 2;
    } else if (ccCount === 0 && efmVal !== null) {
        // EFM only (nbr_controles = 0)
        avg = efmVal;
    } else {
        // Fallback: plain average
        avg = sum / inputs.length;
    }

    avg = Math.round(avg * 100) / 100;

    const cls = avg >= 15 ? 'moy-badge-high'
              : avg >= 10 ? 'moy-badge-mid'
              :              'moy-badge-low';

    span.innerHTML = `<span class="moy-badge ${cls}">${avg.toFixed(2)}</span>`;
    updateFilledCount();
}

// ── Filled count ────────────────────────────────────────────
function updateFilledCount() {
    const all    = document.querySelectorAll('.note-input');
    const filled = [...all].filter(i => i.value !== '').length;
    const span   = document.getElementById('filled-count');
    if (span) span.textContent = filled;
}

// ── Init on load ─────────────────────────────────────────────
document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.note-input').forEach(input => colorize(input));

    @foreach($stagiaires as $stagiaire)
    updateMoy({{ $stagiaire->id }});
    @endforeach

    updateFilledCount();

    // Tab / arrow navigation
    const allInputs = [...document.querySelectorAll('.note-input')];
    allInputs.forEach((input, idx) => {
        input.addEventListener('keydown', function(e) {
            if (e.key === 'Tab') return;
            if (e.key === 'Enter') {
                e.preventDefault();
                if (allInputs[idx + 1]) allInputs[idx + 1].focus();
            }
            if (e.key === 'ArrowRight' && allInputs[idx + 1]) {
                e.preventDefault(); allInputs[idx + 1].focus();
            }
            if (e.key === 'ArrowLeft' && allInputs[idx - 1]) {
                e.preventDefault(); allInputs[idx - 1].focus();
            }
        });
    });
});

// ── Confirm before leaving with unsaved changes ──────────────
let changed = false;
document.querySelectorAll('.note-input').forEach(i => {
    i.addEventListener('input', () => changed = true);
});
window.addEventListener('beforeunload', function(e) {
    if (changed) { e.preventDefault(); e.returnValue = ''; }
});
// ── Promo filter for groupe dropdown ────────────────────────
function filterGroupesByPromo() {
    const promo  = document.getElementById('promo-select')?.value || '';
    const select = document.getElementById('groupe-select');
    if (!select) return;

    let visibleCount = 0;
    [...select.options].forEach(opt => {
        if (!opt.value) return; // keep the placeholder
        const match = !promo || opt.dataset.promo === promo;
        opt.style.display = match ? '' : 'none';
        opt.disabled      = !match;
        if (match) visibleCount++;
        // Deselect hidden options
        if (!match && opt.selected) {
            opt.selected  = false;
            select.value  = '';
        }
    });

    // Update placeholder text
    const placeholder = select.options[0];
    if (placeholder && !placeholder.value) {
        placeholder.text = visibleCount === 0
            ? '— Aucun groupe pour cette promo —'
            : '— Choisir un groupe —';
    }
}

// Run on page load to apply server-side promo filter visually
document.addEventListener('DOMContentLoaded', function () {
    filterGroupesByPromo();
});

document.getElementById('notes-form')?.addEventListener('submit', function() {
    changed = false;
});
</script>

@endsection