{{-- resources/views/emplois/pdf.blade.php --}}
<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
@php
    use App\Http\Controllers\EmploiDuTempsController;

    $isPersonal = in_array($user->role, ['stagiaire', 'formateur']);

    $roleColor = match($user->role) {
        'admin'        => ['bg' => '#0a6640', 'light' => '#e8f5ee', 'text' => '#065f38', 'border' => '#6ee7b7'],
        'gestionnaire' => ['bg' => '#1e293b', 'light' => '#f1f5f9', 'text' => '#334155', 'border' => '#94a3b8'],
        'formateur'    => ['bg' => '#1d4ed8', 'light' => '#eff6ff', 'text' => '#1e40af', 'border' => '#93c5fd'],
        default        => ['bg' => '#1d4ed8', 'light' => '#eff6ff', 'text' => '#1e40af', 'border' => '#93c5fd'],
    };

    $days_fr = [1 => 'Lundi', 2 => 'Mardi', 3 => 'Mercredi', 4 => 'Jeudi', 5 => 'Vendredi', 6 => 'Samedi'];

    // Collect all sessions flat for personal view
    $allSessions = [];
    if ($isPersonal) {
        foreach ($groupesByFiliere as $filiereId => $groupes) {
            foreach ($groupes as $groupe) {
                for ($day = 1; $day <= 6; $day++) {
                    for ($s = 1; $s <= 4; $s++) {
                        $cell = $grid[$groupe->id][$day][$s] ?? ['type' => 'empty'];
                        if ($cell['type'] === 'session') {
                            $emploi  = $cell['emploi'];
                            $colspan = $cell['colspan'];

                            // ── FIX: For formateur, skip sessions where THEY have been replaced,
                            //         but keep sessions where THEY ARE the replacement ──
                            if ($user->role === 'formateur') {
                                $sessRempl   = $emploi->id_user_remplacant ? $emploi->remplacant : null;
                                $isFutureChk = $emploi->date_debut->isFuture();
                                $modRempl    = (!$sessRempl && $isFutureChk && $emploi->module?->id_user_remplacant)
                                               ? $emploi->module->remplacant : null;
                                $activeRempl = $sessRempl ?? $modRempl;

                                // Only hide if there IS a replacement AND it's not the current user
                                if ($activeRempl !== null && $activeRempl->id !== $user->id) {
                                    continue;
                                }
                            }

                            $allSessions[$day][] = [
                                'emploi'  => $emploi,
                                'colspan' => $colspan,
                                'seance'  => $s,
                                'groupe'  => $groupe,
                            ];
                        }
                    }
                }
            }
        }
    }

    $activeDays    = array_keys($allSessions);
    sort($activeDays);
    $totalSessions = array_sum(array_map('count', $allSessions));
@endphp
<style>
* { margin: 0; padding: 0; box-sizing: border-box; }

/* ── Landscape for admin/gestionnaire, portrait for personal ── */
@page {
    @if(!$isPersonal)
    size: A4 landscape;
    margin: 8mm 10mm;
    @else
    size: A4 portrait;
    margin: 10mm 12mm;
    @endif
}

body {
    font-family: DejaVu Sans, sans-serif;
    font-size: 8px;
    color: #1e293b;
    background: white;
    padding: 0;
}

/* ── HEADER ── */
.hdr {
    background: {{ $roleColor['bg'] }};
    color: white;
    padding: 8px 12px;
    margin-bottom: 8px;
}
.hdr-inner  { display: table; width: 100%; }
.hdr-left   { display: table-cell; vertical-align: middle; }
.hdr-right  { display: table-cell; vertical-align: middle; text-align: right; width: 42%; }
.hdr-org    { font-size: 12px; font-weight: bold; letter-spacing: 0.5px; }
.hdr-sub    { font-size: 7px; opacity: 0.8; margin-top: 2px; }
.hdr-week   { font-size: 9px; font-weight: bold; }
.hdr-meta   { font-size: 6.5px; opacity: 0.75; margin-top: 3px; }
.hdr-badge  {
    display: inline-block;
    background: rgba(255,255,255,0.2);
    border: 1px solid rgba(255,255,255,0.35);
    color: white;
    font-size: 7px;
    font-weight: bold;
    padding: 2px 8px;
    border-radius: 20px;
    margin-top: 5px;
    letter-spacing: 0.5px;
}
.hdr-year-pill {
    display: inline-block;
    background: rgba(255,255,255,0.15);
    color: white;
    font-size: 6.5px;
    font-weight: bold;
    padding: 2px 7px;
    border-radius: 20px;
    margin-right: 5px;
}

/* ════════════════════════════════════════════════
   PERSONAL VIEW — stagiaire / formateur
════════════════════════════════════════════════ */
.week-grid  { display: table; width: 100%; border-collapse: separate; border-spacing: 4px; }
.day-col    { display: table-cell; vertical-align: top; width: {{ count($activeDays) > 0 ? round(100/max(count($activeDays),1)).'%' : '16.6%' }}; }
.day-head   { background: {{ $roleColor['bg'] }}; color: white; text-align: center; padding: 5px 4px; border-radius: 6px 6px 0 0; margin-bottom: 3px; }
.day-name   { font-size: 8.5px; font-weight: bold; text-transform: uppercase; letter-spacing: 0.5px; }
.day-date   { font-size: 11px; font-weight: bold; margin-top: 1px; }
.day-mon    { font-size: 7px; opacity: 0.8; }

/* session card — personal */
.sess-card  { border-radius: 5px; padding: 6px 7px; margin-bottom: 4px; border-left: 3px solid {{ $roleColor['bg'] }}; background: {{ $roleColor['light'] }}; page-break-inside: avoid; }
.sess-card.dist { border-left-color: #f59e0b; background: #fefce8; }
.sess-time   { font-size: 7px; font-weight: bold; color: {{ $roleColor['text'] }}; margin-bottom: 3px; }
.sess-card.dist .sess-time { color: #92400e; }
.sess-module { font-size: 8.5px; font-weight: bold; color: {{ $roleColor['text'] }}; line-height: 1.3; margin-bottom: 3px; }
.sess-card.dist .sess-module { color: #92400e; }
.sess-row    { font-size: 7px; color: #475569; margin-top: 2px; display: block; }
.dist-pill   { display: inline-block; background: #fde68a; color: #92400e; font-size: 6px; font-weight: bold; padding: 1px 5px; border-radius: 3px; margin-bottom: 3px; }
.no-sess     { text-align: center; padding: 12px 4px; color: #cbd5e1; font-size: 7px; border: 1px dashed #e2e8f0; border-radius: 5px; margin-top: 3px; }

/* remplaçant — personal */
.rempl-old  { font-size: 7px; color: #94a3b8; text-decoration: line-through; display: block; margin-top: 2px; }
.rempl-new  { font-size: 7px; color: #7c3aed; font-weight: bold; display: block; margin-top: 1px; }
.rempl-pill { display: inline-block; background: #ede9fe; color: #7c3aed; font-size: 5.5px; font-weight: bold; padding: 1px 4px; border-radius: 3px; margin-left: 3px; }

/* ── STATS ROW ── */
.stats-row  { display: table; width: 100%; border-collapse: separate; border-spacing: 6px; margin-bottom: 8px; }
.stat-box   { display: table-cell; text-align: center; background: {{ $roleColor['light'] }}; border: 1px solid {{ $roleColor['border'] }}; border-radius: 6px; padding: 6px 10px; }
.stat-num   { font-size: 16px; font-weight: bold; color: {{ $roleColor['text'] }}; }
.stat-label { font-size: 6.5px; color: #64748b; margin-top: 2px; text-transform: uppercase; letter-spacing: 0.5px; }

/* ════════════════════════════════════════════════
   ADMIN / GESTIONNAIRE — COMPACT GRID TABLE
════════════════════════════════════════════════ */

.section-head {
    display: table;
    width: 100%;
    background: {{ $roleColor['bg'] }};
    padding: 5px 10px;
    margin-bottom: 0;
    margin-top: 8px;
    border-radius: 5px 5px 0 0;
}
.section-head-left  { display: table-cell; vertical-align: middle; }
.section-head-right { display: table-cell; vertical-align: middle; text-align: right; }
.section-title {
    font-size: 8px;
    font-weight: bold;
    color: white;
    text-transform: uppercase;
    letter-spacing: 1.5px;
}
.section-badge {
    display: inline-block;
    background: rgba(255,255,255,0.2);
    color: white;
    font-size: 6px;
    font-weight: bold;
    padding: 2px 7px;
    border-radius: 20px;
    letter-spacing: 0.5px;
}

/* ── Table ── */
.tt {
    width: 100%;
    border-collapse: collapse;
    table-layout: fixed;
    margin-bottom: 8px;
    border: 1px solid #e2e8f0;
    border-top: none;
    border-radius: 0 0 5px 5px;
    overflow: hidden;
    page-break-inside: avoid;
}

/* Day header row */
.tt-head-day {
    background: {{ $roleColor['light'] }};
    color: {{ $roleColor['text'] }};
    text-align: center;
    font-size: 6px;
    font-weight: bold;
    padding: 4px 2px;
    border-bottom: 2px solid {{ $roleColor['bg'] }};
    border-right: 1px solid #d1d5db;
    letter-spacing: 0.5px;
    text-transform: uppercase;
}
.tt-head-day.today {
    background: #dcfce7;
    color: #14532d;
    border-bottom-color: #059669;
}

/* Séance sub-header */
.tt-head-s {
    background: #f8fafc;
    color: #64748b;
    text-align: center;
    font-size: 5px;
    font-weight: bold;
    padding: 2px 1px;
    border-bottom: 1px solid #e2e8f0;
    border-right: 1px solid #edf0f4;
    text-transform: uppercase;
    letter-spacing: 0.3px;
}

/* Group label cell — narrower */
.tt-group-cell {
    padding: 0;
    border-right: 2px solid {{ $roleColor['bg'] }};
    border-bottom: 1px solid #e9edf2;
    background: {{ $roleColor['light'] }};
    min-width: 42px;
    max-width: 42px;
    width: 42px;
    vertical-align: middle;
    text-align: center;
}
.g-inner  { padding: 3px; }
.g-name   { font-size: 6.5px; font-weight: bold; color: {{ $roleColor['text'] }}; line-height: 1.2; }
.g-sub    { font-size: 4.5px; color: #94a3b8; margin-top: 1px; }

/* Empty cell */
.tt-empty {
    border-right: 1px solid #edf0f4;
    border-bottom: 1px solid #edf0f4;
    background: #fafbfc;
    height: 42px;
}
.tt-empty-dot {
    display: block;
    width: 3px;
    height: 3px;
    background: #e2e8f0;
    border-radius: 50%;
    margin: auto;
    margin-top: 19px;
}

/* Session cell */
.tt-sess {
    border-right: 1px solid #e2e8f0;
    border-bottom: 1px solid #e2e8f0;
    vertical-align: top;
    padding: 2px 3px;
    background: white;
}

/* ── Mini card — compact redesign ── */
.mini-card {
    border-radius: 3px;
    padding: 3px 4px;
    min-height: 38px;
    position: relative;
    background: white;
    border: 1px solid {{ $roleColor['border'] }};
    border-top: 2px solid {{ $roleColor['bg'] }};
}
.mini-card.dist {
    border-color: #fcd34d;
    border-top-color: #f59e0b;
    background: #fffbeb;
}
.mini-card.rempl {
    border-top-color: #7c3aed;
    border-color: #ddd6fe;
    background: #faf5ff;
}

/* Time badge inside card */
.mini-time-badge {
    display: inline-block;
    background: {{ $roleColor['bg'] }};
    color: white;
    font-size: 4px;
    font-weight: bold;
    padding: 1px 3px;
    border-radius: 2px;
    margin-bottom: 2px;
    letter-spacing: 0.2px;
}
.mini-card.dist .mini-time-badge { background: #f59e0b; }
.mini-card.rempl .mini-time-badge { background: #7c3aed; }

/* Module name */
.mini-module {
    font-size: 5.5px;
    font-weight: bold;
    color: #1e293b;
    line-height: 1.25;
    margin-bottom: 2px;
    overflow: hidden;
}

/* Divider line */
.mini-divider {
    border: none;
    border-top: 1px solid #e2e8f0;
    margin: 2px 0;
}
.mini-card.dist .mini-divider { border-top-color: #fde68a; }
.mini-card.rempl .mini-divider { border-top-color: #ddd6fe; }

/* Meta rows (formateur, salle) */
.mini-meta { display: table; width: 100%; }
.mini-meta-row { display: table-row; }
.mini-meta-icon {
    display: table-cell;
    width: 5px;
    font-size: 4px;
    color: #94a3b8;
    vertical-align: top;
    padding-top: 0.5px;
}
.mini-meta-text {
    display: table-cell;
    font-size: 4.5px;
    color: #475569;
    line-height: 1.4;
    vertical-align: top;
}

/* Remplaçant styles */
.mini-rempl-old { font-size: 4px; color: #94a3b8; text-decoration: line-through; }
.mini-rempl-new { font-size: 4px; color: #7c3aed; font-weight: bold; }

/* Distance badge */
.mini-dist-tag {
    display: inline-block;
    background: #fef3c7;
    color: #92400e;
    font-size: 4px;
    font-weight: bold;
    padding: 1px 3px;
    border-radius: 2px;
}

/* ── LEGEND & FOOTER ── */
.legend   { margin-top: 6px; padding: 4px 10px; border-top: 1px solid #e2e8f0; display: table; width: 100%; }
.legend-l { display: table-cell; vertical-align: middle; }
.legend-r { display: table-cell; vertical-align: middle; text-align: right; }
.l-item   { display: inline-block; font-size: 6px; color: #475569; margin-right: 10px; }
.l-dot    { display: inline-block; width: 7px; height: 7px; border-radius: 2px; margin-right: 3px; vertical-align: middle; }
.footer   { text-align: center; font-size: 5.5px; color: #94a3b8; margin-top: 5px; padding-top: 4px; border-top: 1px solid #f1f5f9; }
</style>
</head>
<body>

{{-- ════ HEADER ════ --}}
<div class="hdr">
    <div class="hdr-inner">
        <div class="hdr-left">
            <div class="hdr-org">OFPPT — Emploi du temps</div>
            <div class="hdr-sub">Office de la Formation Professionnelle et de la Promotion du Travail</div>
            <div style="margin-top:4px;">
                <span class="hdr-year-pill">{{ $yearLabel }}</span>
                <span class="hdr-badge">{{ ucfirst($user->role) }} : {{ $user->name }}</span>
            </div>
        </div>
        <div class="hdr-right">
            <div class="hdr-week">
                Semaine du {{ $weekStart->translatedFormat('d M') }}
                au {{ $weekEnd->translatedFormat('d M Y') }}
            </div>
            <div class="hdr-meta">Généré le {{ now()->translatedFormat('d M Y à H:i') }}</div>
            @if($isPersonal)
            <div class="hdr-meta" style="margin-top:4px; opacity:0.9;">
                {{ $totalSessions }} séance{{ $totalSessions > 1 ? 's' : '' }} cette semaine
            </div>
            @endif
        </div>
    </div>
</div>


@if($isPersonal)
{{-- ══════════════════════════════════════════════════
     PERSONAL VIEW — stagiaire / formateur
══════════════════════════════════════════════════ --}}
@php
    $totalH    = 0;
    $presCount = 0;
    $distCount = 0;
    foreach ($allSessions as $daySessions) {
        foreach ($daySessions as $sess) {
            $h = EmploiDuTempsController::totalHours($sess['seance'], $sess['colspan']);
            $totalH += $h;
            if (($sess['emploi']->mode ?? 'presentiel') === 'distance') $distCount++;
            else $presCount++;
        }
    }
    $jours = count($activeDays);
@endphp

<div class="stats-row">
    <div class="stat-box"><div class="stat-num">{{ $totalSessions }}</div><div class="stat-label">Séances</div></div>
    <div class="stat-box"><div class="stat-num">{{ number_format($totalH,1) }}h</div><div class="stat-label">Total heures</div></div>
    <div class="stat-box"><div class="stat-num">{{ $jours }}</div><div class="stat-label">Jours actifs</div></div>
    <div class="stat-box"><div class="stat-num">{{ $presCount }}</div><div class="stat-label">Présentiel</div></div>
    <div class="stat-box"><div class="stat-num">{{ $distCount }}</div><div class="stat-label">À distance</div></div>
</div>

@if($totalSessions === 0)
    <div style="text-align:center; padding:40px 20px; color:#94a3b8;">
        <div style="font-size:14px; font-weight:bold; color:#cbd5e1; margin-bottom:6px;">Semaine libre</div>
        <div style="font-size:8px;">Aucune séance planifiée pour cette semaine.</div>
    </div>
@else
    <div class="week-grid">
        @foreach($dayDates as $dayNum => $date)
        @php
            $daySessions = $allSessions[$dayNum] ?? [];
            $isToday     = $date->isToday();
        @endphp
        <div class="day-col">
            <div class="day-head" style="{{ $isToday ? 'background:#059669;' : '' }}">
                <div class="day-name">{{ strtoupper($date->translatedFormat('D')) }}</div>
                <div class="day-date">{{ $date->format('d') }}</div>
                <div class="day-mon">{{ $date->translatedFormat('M') }}{{ $isToday ? ' ★' : '' }}</div>
            </div>

            @forelse($daySessions as $sess)
                @php
                    $emploi   = $sess['emploi'];
                    $colspan  = $sess['colspan'];
                    $sNum     = $sess['seance'];
                    $groupe   = $sess['groupe'];
                    $isRemote = ($emploi->mode ?? 'presentiel') === 'distance';
                    $spanLbl  = EmploiDuTempsController::spanLabel($sNum, $colspan);
                    $totalHs  = EmploiDuTempsController::totalHours($sNum, $colspan);
                    $sessionRempl = $emploi->id_user_remplacant ? $emploi->remplacant : null;
                    $isFuture     = $emploi->date_debut->isFuture();
                    $moduleRempl  = (!$sessionRempl && $isFuture && $emploi->module?->id_user_remplacant)
                                    ? $emploi->module->remplacant : null;
                    $activeRempl  = $sessionRempl ?? $moduleRempl;
                    $hasRempl     = $activeRempl !== null;
                @endphp
                <div class="sess-card {{ $isRemote ? 'dist' : '' }}">
                    @if($isRemote)<div class="dist-pill">À DISTANCE</div>@endif
                    <div class="sess-time">
                        {{ $emploi->date_debut->format('H:i') }} → {{ $emploi->date_fin->format('H:i') }}
                        &nbsp;·&nbsp;{{ $spanLbl }}&nbsp;·&nbsp;{{ $totalHs }}h
                    </div>
                    <div class="sess-module">{{ $emploi->module->name ?? 'Module' }}</div>
                    @if($user->role === 'stagiaire')
                        @if($hasRempl)
                            <span class="rempl-old">{{ $emploi->gestionnaire->name ?? '—' }}</span>
                            <span class="rempl-new">
                                ⇄ {{ $activeRempl->name }}
                                <span class="rempl-pill">{{ $moduleRempl && !$sessionRempl ? 'MODULE' : 'SÉANCE' }}</span>
                            </span>
                        @else
                            <span class="sess-row">{{ $emploi->gestionnaire->name ?? '—' }}</span>
                        @endif
                    @else
                        <span class="sess-row">Gr. {{ $groupe->name ?? 'G'.$groupe->id }}</span>
                    @endif
                    @if(!$isRemote)
                        <span class="sess-row">{{ $emploi->salle->name ?? '—' }}</span>
                    @elseif($emploi->lien_distance)
                        <span class="sess-row" style="color:#b45309;">Lien disponible</span>
                    @endif
                </div>
            @empty
                <div class="no-sess">Libre</div>
            @endforelse
        </div>
        @endforeach
    </div>
@endif

@else
{{-- ══════════════════════════════════════════════════
     ADMIN / GESTIONNAIRE — COMPACT LANDSCAPE GRID
══════════════════════════════════════════════════ --}}

@forelse($groupesByFiliere as $filiereId => $groupes)
    @php $filiere = $groupes->first()->filiere; @endphp

    {{-- Section header --}}
    <div class="section-head">
        <div class="section-head-left">
            <div class="section-title">{{ strtoupper($filiere->name ?? 'Filière') }}</div>
        </div>
        <div class="section-head-right">
            <span class="section-badge">
                {{ $groupes->count() }} groupe{{ $groupes->count() > 1 ? 's' : '' }}
            </span>
        </div>
    </div>

    <table class="tt">
        <colgroup>
            <col style="width:42px;">
            @foreach($dayDates as $dayNum => $date)
                @foreach(EmploiDuTempsController::SEANCES as $sNum => $s)
                    <col>
                @endforeach
            @endforeach
        </colgroup>

        <thead>
        {{-- Row 1: Day names --}}
        <tr>
            <th style="background:{{ $roleColor['light'] }}; border:1px solid #e2e8f0; border-right:2px solid {{ $roleColor['bg'] }}; padding:3px;"></th>
            @foreach($dayDates as $dayNum => $date)
                @php
                    $isToday   = $date->isToday();
                    $isLastDay = $dayNum === array_key_last($dayDates);
                @endphp
                <th colspan="4"
                    class="tt-head-day {{ $isToday ? 'today' : '' }}"
                    style="{{ !$isLastDay ? 'border-right:2px solid '.($isToday ? '#059669' : $roleColor['bg']).';' : '' }}">
                    {{ strtoupper($date->translatedFormat('D')) }}
                    {{ $date->format('d') }}
                    {{ $date->translatedFormat('M') }}
                    @if($isToday)<span style="color:#059669; font-size:7px;"> ●</span>@endif
                </th>
            @endforeach
        </tr>

        {{-- Row 2: Séance slots --}}
        <tr>
            <th style="background:#f8fafc; border:1px solid #e2e8f0; border-right:2px solid {{ $roleColor['bg'] }}; font-size:5px; color:#94a3b8; padding:2px 4px; text-align:center;">
                GRP
            </th>
            @foreach($dayDates as $dayNum => $date)
                @php $isLastDay = $dayNum === array_key_last($dayDates); @endphp
                @foreach(EmploiDuTempsController::SEANCES as $sNum => $seance)
                    @php $isLastS = $sNum === 4; @endphp
                    <th class="tt-head-s"
                        style="{{ ($isLastS && !$isLastDay) ? 'border-right:2px solid #c8cdd6;' : '' }}">
                        {{ $seance['label'] }}<br>
                        <span style="font-weight:normal; font-size:4.5px; color:#94a3b8;">{{ $seance['start'] }}</span>
                    </th>
                @endforeach
            @endforeach
        </tr>
        </thead>

        <tbody>
        @foreach($groupes as $groupe)
        <tr>
            {{-- Group label --}}
            <td class="tt-group-cell">
                <div class="g-inner">
                    <div class="g-name">{{ $groupe->name ?? 'G'.$groupe->id }}</div>
                    <div class="g-sub">{{ Str::limit($groupe->option->titre ?? $filiere->name ?? '', 14) }}</div>
                </div>
            </td>

            @foreach($dayDates as $dayNum => $date)
                @foreach(EmploiDuTempsController::SEANCES as $sNum => $seance)
                    @php
                        $cell      = $grid[$groupe->id][$dayNum][$sNum] ?? ['type' => 'empty'];
                        $isLastS   = $sNum === 4;
                        $isLastDay = $dayNum === array_key_last($dayDates);
                    @endphp

                    @if($cell['type'] === 'skip')
                        {{-- Spanned cell, skip --}}

                    @elseif($cell['type'] === 'session')
                        @php
                            $emploi   = $cell['emploi'];
                            $colspan  = $cell['colspan'];
                            $isRemote = ($emploi->mode ?? 'presentiel') === 'distance';
                            $spanLbl  = EmploiDuTempsController::spanLabel($sNum, $colspan);
                            $lastS    = $sNum + $colspan - 1;
                            $borderR  = ($lastS % 4 === 0 && !$isLastDay)
                                        ? 'border-right:2px solid #c8cdd6;'
                                        : '';

                            $sessionRempl2 = $emploi->id_user_remplacant ? $emploi->remplacant : null;
                            $isFuture2     = $emploi->date_debut->isFuture();
                            $moduleRempl2  = (!$sessionRempl2 && $isFuture2 && $emploi->module?->id_user_remplacant)
                                             ? $emploi->module->remplacant : null;
                            $activeRempl2  = $sessionRempl2 ?? $moduleRempl2;
                            $hasRempl2     = $activeRempl2 !== null;

                            // Truncate long names for compact display
                            $moduleName = Str::limit($emploi->module->name ?? 'Module', 28);
                            $formName   = Str::limit($emploi->gestionnaire->name ?? '—', 18);
                            $salleName  = Str::limit($emploi->salle->name ?? '—', 10);
                        @endphp
                        <td class="tt-sess" colspan="{{ $colspan }}" style="{{ $borderR }}">
                            <div class="mini-card {{ $isRemote ? 'dist' : '' }} {{ $hasRempl2 ? 'rempl' : '' }}">

                                {{-- Time badge --}}
                                <div class="mini-time-badge">
                                    {{ $emploi->date_debut->format('H:i') }}–{{ $emploi->date_fin->format('H:i') }}
                                    {{ $spanLbl }}
                                </div>

                                {{-- Module name --}}
                                <div class="mini-module">{{ $moduleName }}</div>

                                <hr class="mini-divider">

                                {{-- Meta info --}}
                                <div class="mini-meta">
                                    {{-- Formateur row --}}
                                    <div class="mini-meta-row">
                                        <span class="mini-meta-icon">▸</span>
                                        <span class="mini-meta-text">
                                            @if($hasRempl2)
                                                <span class="mini-rempl-old">{{ $formName }}</span>
                                                <span class="mini-rempl-new">⇄ {{ Str::limit($activeRempl2->name, 16) }}</span>
                                            @else
                                                {{ $formName }}
                                            @endif
                                        </span>
                                    </div>

                                    {{-- Salle / Distance row --}}
                                    <div class="mini-meta-row">
                                        <span class="mini-meta-icon">@if($isRemote)⬡@else◉@endif</span>
                                        <span class="mini-meta-text">
                                            @if(!$isRemote)
                                                {{ $salleName }}
                                            @else
                                                <span class="mini-dist-tag">DIST.</span>
                                            @endif
                                        </span>
                                    </div>
                                </div>

                            </div>
                        </td>

                    @else
                        {{-- Empty --}}
                        <td class="tt-empty"
                            style="{{ ($isLastS && !$isLastDay) ? 'border-right:2px solid #e9edf2;' : '' }}">
                            <span class="tt-empty-dot"></span>
                        </td>
                    @endif

                @endforeach
            @endforeach
        </tr>
        @endforeach
        </tbody>
    </table>

@empty
    <div style="text-align:center; padding:32px; color:#94a3b8; font-size:10px;">
        Aucun groupe pour cette année.
    </div>
@endforelse

@endif

{{-- ════ LEGEND ════ --}}
<div class="legend">
    <div class="legend-l">
        <span style="font-size:6px; font-weight:bold; color:{{ $roleColor['text'] }}; text-transform:uppercase; letter-spacing:1px; margin-right:8px;">Créneaux :</span>
        @foreach(EmploiDuTempsController::SEANCES as $sNum => $s)
            <span class="l-item"><strong>{{ $s['label'] }}</strong> {{ $s['start'] }}–{{ $s['end'] }} ({{ $s['hours'] }}h)</span>
        @endforeach
    </div>
    <div class="legend-r">
        <span class="l-item">
            <span class="l-dot" style="background:white; border:2px solid {{ $roleColor['bg'] }};"></span>Présentiel
        </span>
        <span class="l-item">
            <span class="l-dot" style="background:#fffbeb; border:2px solid #f59e0b;"></span>À distance
        </span>
        <span class="l-item">
            <span class="l-dot" style="background:#faf5ff; border:2px solid #7c3aed;"></span>Remplaçant
        </span>
    </div>
</div>

<div class="footer">
    OFPPT — Emploi du temps {{ $yearLabel }} — Semaine du {{ $weekStart->format('d/m/Y') }} — {{ ucfirst($user->role) }} : {{ $user->name }} — Document généré automatiquement
</div>

</body>
</html>