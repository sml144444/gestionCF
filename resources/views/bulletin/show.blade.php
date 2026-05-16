{{-- resources/views/bulletin/show.blade.php --}}
@extends('layouts.app')
@section('title', 'Bulletin — ' . $stagiaire->name)
@section('page-title', 'Bulletin de notes')

@section('content')
@php
    $palettes = [
        'admin'        => ['primary' => '#0a6640', 'light' => '#e8f5ee', 'text' => '#065f38', 'shadow' => 'rgba(10,102,64,0.2)'],
        'gestionnaire' => ['primary' => '#1e293b', 'light' => '#f1f5f9', 'text' => '#1e293b', 'shadow' => 'rgba(30,41,59,0.2)'],
        'formateur'    => ['primary' => '#1d4ed8', 'light' => '#eff6ff', 'text' => '#1e40af', 'shadow' => 'rgba(29,78,216,0.2)'],
        'stagiaire'    => ['primary' => '#0a6640', 'light' => '#e8f5ee', 'text' => '#065f38', 'shadow' => 'rgba(10,102,64,0.2)'],
    ];
    $p      = $palettes[Auth::user()->role] ?? $palettes['gestionnaire'];
    $accent = $p['primary'];
    $light  = $p['light'];
    $text   = $p['text'];
    $shadow = $p['shadow'];

    $allGraded = $modulesWithNotes->isNotEmpty()
        && $modulesWithNotes->every(fn($m) => $m['moduleGrade'] !== null);

    $totalModules = $modulesWithNotes->count();
    $notedModules = $modulesWithNotes->filter(fn($m) => $m['moduleGrade'] !== null)->count();

    // EFF /100 → /20 conversion for formula display
    $effNoteConverted = $effNote !== null ? round($effNote / 5, 2) : null;

    $progressPct = $totalModules > 0 ? round(($notedModules / $totalModules) * 100) : 0;
@endphp

<style>
.bls-wrap { font-family:'Segoe UI',system-ui,sans-serif; }

.breadcrumb { display:flex;align-items:center;gap:8px;font-size:12px;color:#94a3b8;margin-bottom:18px;flex-wrap:wrap; }
.breadcrumb a { color:{{ $accent }};font-weight:600;text-decoration:none; }
.breadcrumb a:hover { text-decoration:underline; }

.profile-card { background:white;border-radius:16px;border:1px solid #e2e8f0;padding:20px 24px;margin-bottom:20px;display:flex;align-items:center;gap:16px;flex-wrap:wrap; }
.profile-avatar { width:52px;height:52px;border-radius:50%;background:{{ $light }};display:flex;align-items:center;justify-content:center;font-size:20px;font-weight:800;color:{{ $text }};flex-shrink:0; }

/* ── Main table ── */
.bl-table-wrap { background:white;border-radius:16px;border:1px solid #e2e8f0;overflow:hidden;margin-bottom:20px; }
.bl-table { width:100%;border-collapse:collapse;font-size:12px; }
.bl-table thead tr:first-child th { padding:11px 14px;font-size:9px;font-weight:800;color:#94a3b8;text-transform:uppercase;letter-spacing:.9px;background:#f8fafc;border-bottom:1px solid #f1f5f9;white-space:nowrap;text-align:center; }
.bl-table thead tr:first-child th.col-module { text-align:left;min-width:200px;position:sticky;left:0;z-index:2;background:#f8fafc; }
.bl-table tbody td { padding:12px 14px;border-bottom:1px solid #f8fafc;vertical-align:middle;text-align:center; }
.bl-table tbody td.col-module-cell { text-align:left;position:sticky;left:0;z-index:1;background:white;border-right:1px solid #f1f5f9; }
.bl-table tbody tr:last-child td { border-bottom:none; }
.bl-table tbody tr:hover td { background:#fafbff; }
.bl-table tbody tr:hover td.col-module-cell { background:#fafbff; }

/* Discipline row */
.discipline-row td { background:#fefce8 !important;border-top:2px solid #fde047 !important; }
.discipline-row:hover td { background:#fef9c3 !important; }
.discipline-row td.col-module-cell { background:#fefce8 !important; }

/* Pills */
.note-pill { display:inline-flex;align-items:center;justify-content:center;min-width:50px;height:28px;border-radius:8px;font-size:12px;font-weight:800;padding:0 6px; }
.note-high  { background:#f0fdf4;color:#16a34a;border:1.5px solid #bbf7d0; }
.note-mid   { background:#fffbeb;color:#d97706;border:1.5px solid #fde68a; }
.note-low   { background:#fff1f2;color:#dc2626;border:1.5px solid #fecdd3; }
.note-empty { background:#f8fafc;color:#cbd5e1;border:1.5px solid #e2e8f0; }

.cc-pill  { display:inline-flex;align-items:center;justify-content:center;min-width:50px;height:28px;border-radius:8px;font-size:12px;font-weight:800;padding:0 6px; }
.cc-high  { background:#fdf4ff;color:#7e22ce;border:1.5px solid #e9d5ff; }
.cc-mid   { background:#fffbeb;color:#d97706;border:1.5px solid #fde68a; }
.cc-low   { background:#fff1f2;color:#dc2626;border:1.5px solid #fecdd3; }
.cc-empty { background:#f8fafc;color:#cbd5e1;border:1.5px solid #e2e8f0; }

.moy-badge { display:inline-flex;align-items:center;justify-content:center;padding:4px 12px;border-radius:99px;font-size:12px;font-weight:800; }
.moy-high  { background:#f0fdf4;color:#16a34a;border:1.5px solid #bbf7d0; }
.moy-mid   { background:#fffbeb;color:#d97706;border:1.5px solid #fde68a; }
.moy-low   { background:#fff1f2;color:#dc2626;border:1.5px solid #fecdd3; }
.moy-none  { background:#f8fafc;color:#94a3b8;border:1.5px solid #e2e8f0; }

.disc-badge { display:inline-flex;align-items:center;justify-content:center;padding:4px 12px;border-radius:99px;font-size:12px;font-weight:800; }
.disc-high  { background:#fefce8;color:#713f12;border:1.5px solid #fde047; }
.disc-mid   { background:#fff7ed;color:#c2410c;border:1.5px solid #fed7aa; }
.disc-low   { background:#fff1f2;color:#dc2626;border:1.5px solid #fecdd3; }

.type-badge { display:inline-flex;font-size:9px;font-weight:800;padding:2px 7px;border-radius:99px;text-transform:uppercase;letter-spacing:.5px; }
.type-regional { background:#eff6ff;color:#1e40af;border:1px solid #bfdbfe; }
.type-local    { background:#fdf4ff;color:#7e22ce;border:1px solid #e9d5ff; }
.info-chip { display:inline-flex;align-items:center;gap:3px;padding:2px 7px;border-radius:99px;font-size:9px;font-weight:600;background:#f1f5f9;color:#475569;border:1px solid #e2e8f0; }

/* Stat cards */
.stat-card { background:white;border-radius:14px;border:1px solid #e2e8f0;padding:16px 20px;display:flex;align-items:center;gap:14px; }
.stat-icon { width:42px;height:42px;border-radius:12px;flex-shrink:0;display:flex;align-items:center;justify-content:center; }
.stat-val  { font-size:22px;font-weight:800;color:#1e293b;line-height:1; }
.stat-lbl  { font-size:10px;font-weight:600;color:#94a3b8;text-transform:uppercase;letter-spacing:.6px;margin-top:3px; }

/* General average banner */
.ga-banner { border-radius:16px;padding:24px 28px;margin-top:4px;display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:16px; }

/* Pending notice */
.pending-notice { border-radius:14px;padding:14px 18px;display:flex;align-items:center;gap:10px;background:#fffbeb;border:1.5px solid #fde68a;color:#92400e;font-size:12px;font-weight:600;margin-top:4px; }

/* ── EFF lock styles ── */
.eff-lock-notice {
    display:inline-flex;
    align-items:center;
    gap:4px;
    font-size:10px;
    color:#92400e;
    margin-top:6px;
    background:#fef3c7;
    padding:3px 9px;
    border-radius:6px;
    border:1px solid #fde68a;
    font-weight:600;
}
.eff-progress-bar-wrap {
    height:5px;
    border-radius:99px;
    background:#e2e8f0;
    overflow:hidden;
    width:180px;
}
.eff-progress-bar-fill {
    height:100%;
    border-radius:99px;
    background: {{ $allGraded ? '#16a34a' : '#f59e0b' }};
    width: {{ $progressPct }}%;
    transition: width .4s ease;
}
</style>

<div class="bls-wrap">

{{-- Breadcrumb --}}
@php
    $backParams = array_filter([
        'groupe_id'  => $groupeId,
        'filiere_id' => $filiereFilter ?? null,
        'promo'      => $promoFilter ?? null,
    ]);
    $isStagiaire = Auth::user()->role === 'stagiaire';
@endphp

<div class="breadcrumb">
    @if($isStagiaire)
        <span style="color:#1e293b;font-weight:600;">Mon Bulletin</span>
    @else
        <a href="{{ route('bulletin.index') }}">Bulletins</a>
        <span style="color:#cbd5e1;">›</span>
        @if($groupeId)
        <a href="{{ route('bulletin.index', $backParams) }}">{{ $groupe?->name ?? 'Groupe' }}</a>
        <span style="color:#cbd5e1;">›</span>
        @endif
        <span style="color:#1e293b;font-weight:600;">{{ $stagiaire->name }}</span>
    @endif
</div>

{{-- Profile --}}
<div class="profile-card">
    <div class="profile-avatar">{{ strtoupper(substr($stagiaire->name, 0, 1)) }}</div>
    <div style="flex:1;">
        <div style="font-size:16px;font-weight:800;color:#0f172a;">{{ $stagiaire->name }}</div>
        <div style="font-size:12px;color:#64748b;margin-top:2px;">
            {{ $stagiaire->email }}
            @if($groupe)
                · Groupe <strong>{{ $groupe->name }}</strong>
                @if($groupe->filiere) · {{ $groupe->filiere->name }} @endif
            @endif
        </div>
    </div>
    @if(!$isStagiaire && $groupeId)
    <a href="{{ route('bulletin.index', $backParams) }}"
       style="display:inline-flex;align-items:center;gap:6px;padding:8px 14px;font-size:11px;font-weight:700;border-radius:10px;background:white;border:1.5px solid #e2e8f0;color:#475569;text-decoration:none;">
        ← Retour au groupe
    </a>
    @endif
</div>

@if(! $groupe)
    <div style="padding:64px;text-align:center;background:white;border-radius:16px;border:1px solid #e2e8f0;color:#94a3b8;">
        Ce stagiaire n'est affecté à aucun groupe.
    </div>

@elseif($modulesWithNotes->isEmpty())
    <div style="padding:64px;text-align:center;background:white;border-radius:16px;border:1px solid #e2e8f0;color:#94a3b8;">
        Aucun module trouvé pour ce groupe.
    </div>

@else

{{-- Stats --}}
<div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(160px,1fr));gap:12px;margin-bottom:22px;">
    <div class="stat-card">
        <div class="stat-icon" style="background:{{ $light }};">
            <svg width="20" height="20" fill="none" stroke="{{ $accent }}" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
        </div>
        <div><div class="stat-val">{{ $totalModules }}</div><div class="stat-lbl">Modules</div></div>
    </div>
    <div class="stat-card">
        <div class="stat-icon" style="background:#eff6ff;">
            <svg width="20" height="20" fill="none" stroke="#1d4ed8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
        </div>
        <div><div class="stat-val">{{ $notedModules }}/{{ $totalModules }}</div><div class="stat-lbl">Complétés</div></div>
    </div>

    @if($disciplineNote !== null)
    <div class="stat-card" style="background:#fefce8;border-color:#fde047;">
        <div class="stat-icon" style="background:white;font-size:18px;">🎓</div>
        <div>
            <div class="stat-val" style="color:#713f12;">{{ number_format($disciplineNote, 2) }}</div>
            <div class="stat-lbl">Discipline</div>
        </div>
    </div>
    @endif
</div>

{{-- ── MAIN TABLE ── --}}
<div class="bl-table-wrap">
    <div style="padding:14px 20px;border-bottom:1px solid #f1f5f9;display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:8px;background:#fafafa;">
        <div style="font-size:13px;font-weight:800;color:#0f172a;">Relevé de notes</div>
        <div style="display:flex;align-items:center;gap:12px;font-size:10px;color:#64748b;">
            <span style="display:flex;align-items:center;gap:4px;"><span style="width:8px;height:8px;border-radius:50%;background:#16a34a;display:inline-block;"></span>≥ 15</span>
            <span style="display:flex;align-items:center;gap:4px;"><span style="width:8px;height:8px;border-radius:50%;background:#d97706;display:inline-block;"></span>10 – 14</span>
            <span style="display:flex;align-items:center;gap:4px;"><span style="width:8px;height:8px;border-radius:50%;background:#dc2626;display:inline-block;"></span>< 10</span>
            <span style="display:flex;align-items:center;gap:4px;"><span style="width:8px;height:8px;border-radius:50%;background:#eab308;display:inline-block;"></span>Discipline</span>
        </div>
    </div>

    <div style="overflow-x:auto;">
        <table class="bl-table">
            <thead>
                <tr>
                    <th class="col-module">Module</th>
                    <th>Type</th>
                    <th>Coeff.</th>
@php
    $maxControles = $modulesWithNotes->max(fn($m) => (int) ($m['module']->nbr_controles ?? 1));
@endphp
                    @for($i = 1; $i <= $maxControles; $i++)
                        <th>C{{ $i }}<br><span style="font-size:8px;color:#cbd5e1;">/ 20</span></th>
                    @endfor
                    <th style="color:#7e22ce;">CC<br><span style="font-size:8px;color:#cbd5e1;">/ 20</span></th>
                    <th style="color:#dc2626;">⚑ EFM<br><span style="font-size:8px;color:#fca5a5;">/ 20</span></th>
                    <th style="color:#7c3aed;">Note module</th>
                </tr>
            </thead>
            <tbody>

            @foreach($modulesWithNotes as $item)
            @php
                $module      = $item['module'];
                $controles   = $item['controles'];
                $efm         = $item['efm'];
                $notes       = $item['notes'];
                $cc          = $item['cc'];
                $efmDisplay  = $item['efmDisplay'];
                $moduleGrade = $item['moduleGrade'];

                $mgClass = $moduleGrade === null ? 'moy-none'
                    : ($moduleGrade >= 15 ? 'moy-high' : ($moduleGrade >= 10 ? 'moy-mid' : 'moy-low'));
                $ccClass = $cc === null ? 'cc-empty'
                    : ($cc >= 15 ? 'cc-high' : ($cc >= 10 ? 'cc-mid' : 'cc-low'));
            @endphp
            <tr>
                <td class="col-module-cell">
                    <div style="font-weight:700;color:#1e293b;font-size:12px;">{{ $module->name }}</div>
                    @if($module->formateur)
                    <div style="font-size:10px;color:#94a3b8;margin-top:1px;">{{ $module->formateur->name }}</div>
                    @endif
                </td>
                <td>
                    <span class="type-badge type-{{ $module->type }}">
                        {{ $module->type === 'regional' ? 'Rég.' : 'Loc.' }}
                    </span>
                </td>
                <td>
                    <span class="info-chip" style="font-size:11px;font-weight:800;">{{ $module->coefficience }}</span>
                </td>

                @for($i = 1; $i <= $maxControles; $i++)
                @php
                    $ctrl = $controles->get($i - 1);
                    $val  = $ctrl ? ($notes[$ctrl->id] ?? null) : null;
                    $cls  = $val !== null
                        ? ($val >= 15 ? 'note-high' : ($val >= 10 ? 'note-mid' : 'note-low'))
                        : 'note-empty';
                @endphp
                <td>
                    @if($ctrl)
                        <span class="note-pill {{ $cls }}">{{ $val !== null ? number_format((float)$val, 2) : '—' }}</span>
                    @else
                        <span style="color:#e2e8f0;font-size:11px;">—</span>
                    @endif
                </td>
                @endfor

                <td>
                    <span class="cc-pill {{ $ccClass }}">{{ $cc !== null ? number_format($cc, 2) : '—' }}</span>
                </td>

                @php
                    $eCls = $efmDisplay !== null
                        ? ($efmDisplay >= 15 ? 'note-high' : ($efmDisplay >= 10 ? 'note-mid' : 'note-low'))
                        : 'note-empty';
                @endphp
                <td>
                    <span class="note-pill {{ $eCls }}">{{ $efmDisplay !== null ? number_format($efmDisplay, 2) : '—' }}</span>
                </td>

                <td>
                    <span class="moy-badge {{ $mgClass }}">
                        {{ $moduleGrade !== null ? number_format($moduleGrade, 2) : '—' }}
                    </span>
                </td>
            </tr>
            @endforeach

            {{-- ── DISCIPLINE ROW ── --}}
            @if($disciplineNote !== null)
            @php
                $discBadgeClass = $disciplineNote >= 15 ? 'disc-high'
                    : ($disciplineNote >= 10 ? 'disc-mid' : 'disc-low');
            @endphp
            <tr class="discipline-row">
                <td class="col-module-cell">
                    <div style="display:flex;align-items:center;gap:8px;">
                        <span style="font-size:16px;">🎓</span>
                        <div>
                            <div style="font-weight:800;color:#713f12;font-size:12px;">Discipline</div>
                            <div style="font-size:10px;color:#92400e;margin-top:1px;">
                                Absences non justifiées · pénalité 1pt / 5h
                            </div>
                        </div>
                    </div>
                </td>
                <td>
                    <span style="font-size:10px;font-weight:700;padding:2px 7px;border-radius:99px;
                                 background:#fef9c3;color:#713f12;border:1px solid #fde047;">
                        Conduite
                    </span>
                </td>
                <td>
                    <span class="info-chip" style="font-size:11px;font-weight:800;background:#fef9c3;color:#713f12;border-color:#fde047;">1</span>
                </td>
                @for($i = 1; $i <= $maxControles; $i++)
                <td><span style="color:#e2e8f0;font-size:11px;">—</span></td>
                @endfor
                <td><span style="color:#e2e8f0;font-size:11px;">—</span></td>
                <td><span style="color:#e2e8f0;font-size:11px;">—</span></td>
                <td>
                    <span class="disc-badge {{ $discBadgeClass }}">
                        {{ number_format($disciplineNote, 2) }}
                    </span>
                </td>
            </tr>
            @endif

            </tbody>
        </table>
    </div>
</div>

{{-- ══════════════════════════════════════════════════════════════
     BANNERS
     ══════════════════════════════════════════════════════════════ --}}

{{-- ── Moyenne Générale ── --}}
@if($allGraded)
@php
    $bannerBg  = $generalAverage >= 10 ? '#f0fdf4' : '#fff1f2';
    $bannerBdr = $generalAverage >= 10 ? '#bbf7d0' : '#fecdd3';
    $bannerClr = $generalAverage >= 10 ? '#15803d' : '#be123c';
@endphp
<div class="ga-banner" style="background:{{ $bannerBg }};border:2px solid {{ $bannerBdr }};margin-bottom:10px;">
    <div>
        <div style="font-size:14px;font-weight:800;color:#0f172a;">Moyenne générale</div>
        <div style="font-size:11px;color:#64748b;margin-top:3px;">
            {{ $totalModules }} module{{ $totalModules > 1 ? 's' : '' }} + discipline · pondérée par coefficient
            @if($groupe) · {{ $groupe->name }} @endif
        </div>
        @if($disciplineNote !== null)
        <div style="font-size:10px;color:#92400e;margin-top:4px;
                    display:inline-flex;align-items:center;gap:4px;
                    background:#fef9c3;padding:2px 8px;border-radius:6px;border:1px solid #fde047;">
            🎓 Discipline : {{ number_format($disciplineNote, 2) }} / 20 (coeff. 1)
        </div>
        @endif
    </div>
    <div style="font-size:38px;font-weight:800;color:{{ $bannerClr }};">
        {{ number_format($generalAverage, 2) }}
        <span style="font-size:18px;font-weight:600;opacity:.6;">/ 20</span>
    </div>
</div>

@else
{{-- Pending notice --}}
<div class="pending-notice" style="margin-bottom:10px;">
    <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
              d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
    </svg>
    Moyenne générale non disponible —
    {{ $totalModules - $notedModules }} module{{ ($totalModules - $notedModules) > 1 ? 's' : '' }}
    en attente d'EFM
    ({{ $notedModules }}/{{ $totalModules }} complets)
</div>
@endif

{{-- ══════════════════════════════════════════════════════════════
     EFF + NOTE FINALE
     EFF stocké en /100, converti ÷5 → /20 pour le calcul
     ══════════════════════════════════════════════════════════════ --}}
@if($isFinalYear)
@php
    $fgColor = $finalGrade === null ? '#94a3b8'
        : ($finalGrade >= 10 ? '#15803d' : '#be123c');
    $fgBg    = $finalGrade === null ? '#f8fafc'
        : ($finalGrade >= 10 ? '#f0fdf4' : '#fff1f2');
    $fgBdr   = $finalGrade === null ? '#e2e8f0'
        : ($finalGrade >= 10 ? '#bbf7d0' : '#fecdd3');

    // EFF card colors driven by lock state
    $effCardBg  = $allGraded ? '#eff6ff' : '#f8fafc';
    $effCardBdr = $allGraded ? '#bfdbfe' : '#e2e8f0';
    $effTitleCl = $allGraded ? '#1e40af' : '#94a3b8';
    $effSubCl   = $allGraded ? '#3b82f6' : '#cbd5e1';
    $effValCl   = $allGraded ? '#1d4ed8' : '#94a3b8';
@endphp

<div style="display:grid;grid-template-columns:1fr 1fr;gap:10px;margin-bottom:4px;">

    {{-- ── EFF card ── --}}
    <div style="border-radius:16px;padding:20px 24px;
                background:{{ $effCardBg }};
                border:2px solid {{ $effCardBdr }};
                display:flex;align-items:center;justify-content:space-between;
                gap:12px;flex-wrap:wrap;
                transition:background .3s,border-color .3s;">
        <div>

            {{-- Title + lock/unlock badge --}}
            <div style="font-size:13px;font-weight:800;color:{{ $effTitleCl }};">
                ⚑ EFF
                @if(!$allGraded)
                    <span style="margin-left:6px;font-size:10px;font-weight:700;
                                 padding:2px 7px;border-radius:99px;
                                 background:#fef3c7;color:#92400e;border:1px solid #fde68a;
                                 vertical-align:middle;">
                        🔒 Verrouillé
                    </span>
                @else
                    <span style="margin-left:6px;font-size:10px;font-weight:700;
                                 padding:2px 7px;border-radius:99px;
                                 background:#dcfce7;color:#15803d;border:1px solid #bbf7d0;
                                 vertical-align:middle;">
                        ✓ Disponible
                    </span>
                @endif
            </div>

            <div style="font-size:11px;color:{{ $effSubCl }};margin-top:3px;">
                Examen Final de Formation · année terminale ·
                <strong style="color:{{ $effTitleCl }};">Note sur 100</strong>
            </div>

            {{-- Progress bar (admin/gestionnaire only) --}}
            @if(in_array(Auth::user()->role, ['admin','gestionnaire']))
            <div style="margin-top:8px;">
                <div style="font-size:10px;color:#64748b;margin-bottom:4px;font-weight:600;">
                    Modules complétés : {{ $notedModules }}/{{ $totalModules }}
                </div>
                <div class="eff-progress-bar-wrap">
                    <div class="eff-progress-bar-fill"></div>
                </div>
            </div>
            @endif

            {{-- Admin / gestionnaire : form --}}
            @if(in_array(Auth::user()->role, ['admin','gestionnaire']))
            <form method="POST"
                  action="{{ route('bulletin.eff.store', $stagiaire) }}"
                  style="display:flex;align-items:center;gap:8px;margin-top:10px;">
                @csrf

                {{-- Input /100 --}}
                <div style="position:relative;display:inline-flex;align-items:center;">
                    <input type="number"
                           name="eff_note"
                           value="{{ $effNote !== null ? number_format($effNote, 2, '.', '') : '' }}"
                           min="0" max="100" step="0.01"
                           placeholder="0 – 100"
                           {{ !$allGraded ? 'disabled' : '' }}
                           style="width:110px;height:34px;padding:0 40px 0 10px;border-radius:8px;
                                  border:1.5px solid {{ $allGraded ? '#bfdbfe' : '#e2e8f0' }};
                                  font-size:13px;font-weight:700;
                                  color:{{ $allGraded ? '#1e40af' : '#94a3b8' }};
                                  outline:none;
                                  background:{{ $allGraded ? 'white' : '#f1f5f9' }};
                                  cursor:{{ $allGraded ? 'text' : 'not-allowed' }};
                                  transition:all .2s;">
                    {{-- /100 suffix inside input --}}
                    <span style="position:absolute;right:8px;font-size:10px;font-weight:800;
                                 color:{{ $allGraded ? '#93c5fd' : '#cbd5e1' }};
                                 pointer-events:none;user-select:none;">
                        /100
                    </span>
                </div>

                <button type="submit"
                        {{ !$allGraded ? 'disabled' : '' }}
                        style="padding:7px 14px;border-radius:8px;
                               background:{{ $allGraded ? '#1d4ed8' : '#cbd5e1' }};
                               color:white;font-size:11px;font-weight:700;border:none;
                               cursor:{{ $allGraded ? 'pointer' : 'not-allowed' }};
                               transition:opacity .15s,background .2s;"
                        @if($allGraded)
                        onmouseover="this.style.opacity='.85'"
                        onmouseout="this.style.opacity='1'"
                        @endif>
                    {{ $effNote !== null ? '✏️ Modifier' : '+ Saisir' }}
                </button>
            </form>

            {{-- Lock / unlock status message --}}
            @if(!$allGraded)
            <div class="eff-lock-notice">
                🔒 Complétez tous les modules avant de saisir l'EFF
                — {{ $totalModules - $notedModules }} restant{{ ($totalModules - $notedModules) > 1 ? 's' : '' }}
            </div>
            @else
            <div style="font-size:10px;color:#16a34a;margin-top:6px;
                        display:inline-flex;align-items:center;gap:4px;
                        background:#dcfce7;padding:2px 8px;border-radius:6px;
                        border:1px solid #bbf7d0;font-weight:600;">
                ✓ Tous les modules sont notés — saisie EFF disponible
            </div>
            @endif

            {{-- Conversion hint when a value is saved --}}
            @if($effNote !== null)
            <div style="font-size:10px;color:#64748b;margin-top:5px;
                        display:inline-flex;align-items:center;gap:4px;
                        background:#f1f5f9;padding:2px 8px;border-radius:6px;">
                ↳ Équivalent /20 :
                <strong style="color:#1e40af;">{{ number_format($effNoteConverted, 2) }}/20</strong>
                &nbsp;({{ number_format($effNote, 2) }} ÷ 5)
            </div>
            @endif

            @else
            {{-- Stagiaire / formateur : lecture seule --}}
            <div style="font-size:10px;color:#3b82f6;margin-top:8px;
                        display:inline-flex;align-items:center;gap:4px;
                        background:#dbeafe;padding:3px 8px;border-radius:6px;">
                📋 Note saisie par l'administration
            </div>
            @endif

        </div>

        {{-- Big number display: raw /100 + converted /20 below --}}
        <div style="text-align:right;">
            <div style="font-size:38px;font-weight:800;color:{{ $effValCl }};line-height:1;">
                {{ $effNote !== null ? number_format($effNote, 2) : '—' }}
                <span style="font-size:18px;font-weight:600;opacity:.6;">/ 100</span>
            </div>
            @if($effNote !== null)
            <div style="font-size:11px;color:#64748b;margin-top:4px;">
                = <strong style="color:#1e40af;">{{ number_format($effNoteConverted, 2) }}</strong>
                <span style="opacity:.6;">/ 20</span>
            </div>
            @endif
        </div>
    </div>

    {{-- ── Note Finale card ── --}}
    <div style="border-radius:16px;padding:20px 24px;background:{{ $fgBg }};
                border:2px solid {{ $fgBdr }};
                display:flex;align-items:center;justify-content:space-between;
                gap:12px;flex-wrap:wrap;">
        <div>
            <div style="font-size:13px;font-weight:800;color:#0f172a;">🏆 Note Finale</div>
            <div style="font-size:11px;color:#64748b;margin-top:3px;">
                (EFF ÷ 5) × 60% + Moy. Générale × 40%
            </div>

            @if($effNote === null && !$allGraded)
            <div style="font-size:10px;color:#f59e0b;margin-top:5px;
                        display:inline-flex;align-items:center;gap:4px;
                        background:#fef3c7;padding:2px 8px;border-radius:6px;border:1px solid #fde68a;">
                ⚠️ En attente des notes et de la note EFF
            </div>

            @elseif($effNote === null)
            <div style="font-size:10px;color:#f59e0b;margin-top:5px;
                        display:inline-flex;align-items:center;gap:4px;
                        background:#fef3c7;padding:2px 8px;border-radius:6px;border:1px solid #fde68a;">
                ⚠️ En attente de la note EFF
            </div>

            @elseif(!$allGraded)
            <div style="font-size:10px;color:#f59e0b;margin-top:5px;
                        display:inline-flex;align-items:center;gap:4px;
                        background:#fef3c7;padding:2px 8px;border-radius:6px;border:1px solid #fde68a;">
                ⚠️ En attente des notes de modules
            </div>

            @else
            {{-- Full formula with /100→/20 conversion shown transparently --}}
            <div style="font-size:10px;color:#64748b;margin-top:5px;">
                ({{ number_format($effNote, 2) }}/100 → {{ number_format($effNoteConverted, 2) }}/20 × 0.6)
                + ({{ number_format($generalAverage, 2) }} × 0.4)
            </div>
            @endif
        </div>

        <div style="font-size:38px;font-weight:800;color:{{ $fgColor }};">
            {{ $finalGrade !== null ? number_format($finalGrade, 2) : '—' }}
            <span style="font-size:18px;font-weight:600;opacity:.6;">/ 20</span>
        </div>
    </div>

</div>

@elseif($allGraded)
{{-- Pas année terminale + tous notés → Note Finale = Moyenne Générale --}}
@php $bannerClr = $generalAverage >= 10 ? '#15803d' : '#be123c'; @endphp
<div style="border-radius:14px;padding:14px 20px;background:#f8fafc;
            border:1.5px solid #e2e8f0;
            display:flex;align-items:center;gap:10px;font-size:11px;color:#64748b;">
    🏆 <strong style="color:#1e293b;">Note Finale :</strong>
    <span style="font-size:15px;font-weight:800;color:{{ $bannerClr }};">
        {{ number_format($generalAverage, 2) }} / 20
    </span>
    <span style="font-size:10px;color:#94a3b8;">(= Moyenne Générale — pas en année terminale)</span>
</div>
@endif

@endif

</div>
@endsection