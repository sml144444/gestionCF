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

    // Find active days for personal view
    $activeDays = array_keys($allSessions);
    sort($activeDays);
    $totalSessions = array_sum(array_map('count', $allSessions));
@endphp
<style>
* { margin: 0; padding: 0; box-sizing: border-box; }

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
    padding: 10px 14px;
    margin-bottom: 10px;
}
.hdr-inner {
    display: table;
    width: 100%;
}
.hdr-left  { display: table-cell; vertical-align: middle; }
.hdr-right { display: table-cell; vertical-align: middle; text-align: right; width: 42%; }
.hdr-org   { font-size: 14px; font-weight: bold; letter-spacing: 0.5px; }
.hdr-sub   { font-size: 7.5px; opacity: 0.8; margin-top: 2px; }
.hdr-week  { font-size: 10px; font-weight: bold; }
.hdr-meta  { font-size: 7px; opacity: 0.75; margin-top: 3px; }
.hdr-badge {
    display: inline-block;
    background: rgba(255,255,255,0.2);
    border: 1px solid rgba(255,255,255,0.35);
    color: white;
    font-size: 7.5px;
    font-weight: bold;
    padding: 3px 10px;
    border-radius: 20px;
    margin-top: 6px;
    letter-spacing: 0.5px;
}
.hdr-year-pill {
    display: inline-block;
    background: rgba(255,255,255,0.15);
    color: white;
    font-size: 7px;
    font-weight: bold;
    padding: 2px 8px;
    border-radius: 20px;
    margin-right: 5px;
}

/* ── PERSONAL VIEW (stagiaire / formateur) ── */
.week-grid {
    display: table;
    width: 100%;
    border-collapse: separate;
    border-spacing: 4px;
}
.day-col {
    display: table-cell;
    vertical-align: top;
    width: {{ count($activeDays) > 0 ? round(100/max(count($activeDays),1)).'%' : '16.6%' }};
}
.day-head {
    background: {{ $roleColor['bg'] }};
    color: white;
    text-align: center;
    padding: 5px 4px;
    border-radius: 6px 6px 0 0;
    margin-bottom: 3px;
}
.day-name { font-size: 8.5px; font-weight: bold; text-transform: uppercase; letter-spacing: 0.5px; }
.day-date { font-size: 11px; font-weight: bold; margin-top: 1px; }
.day-mon  { font-size: 7px; opacity: 0.8; }

/* session card */
.sess-card {
    border-radius: 5px;
    padding: 6px 7px;
    margin-bottom: 4px;
    border-left: 3px solid {{ $roleColor['bg'] }};
    background: {{ $roleColor['light'] }};
    page-break-inside: avoid;
}
.sess-card.dist {
    border-left-color: #f59e0b;
    background: #fefce8;
}
.sess-time {
    font-size: 7px;
    font-weight: bold;
    color: {{ $roleColor['text'] }};
    margin-bottom: 3px;
}
.sess-card.dist .sess-time { color: #92400e; }
.sess-module {
    font-size: 8.5px;
    font-weight: bold;
    color: {{ $roleColor['text'] }};
    line-height: 1.3;
    margin-bottom: 3px;
}
.sess-card.dist .sess-module { color: #92400e; }
.sess-row {
    font-size: 7px;
    color: #475569;
    margin-top: 2px;
    display: block;
}
.dist-pill {
    display: inline-block;
    background: #fde68a;
    color: #92400e;
    font-size: 6px;
    font-weight: bold;
    padding: 1px 5px;
    border-radius: 3px;
    margin-bottom: 3px;
}
.no-sess {
    text-align: center;
    padding: 12px 4px;
    color: #cbd5e1;
    font-size: 7px;
    border: 1px dashed #e2e8f0;
    border-radius: 5px;
    margin-top: 3px;
}

/* ── STATS ROW ── */
.stats-row {
    display: table;
    width: 100%;
    border-collapse: separate;
    border-spacing: 6px;
    margin-bottom: 8px;
}
.stat-box {
    display: table-cell;
    text-align: center;
    background: {{ $roleColor['light'] }};
    border: 1px solid {{ $roleColor['border'] }};
    border-radius: 6px;
    padding: 6px 10px;
}
.stat-num   { font-size: 16px; font-weight: bold; color: {{ $roleColor['text'] }}; }
.stat-label { font-size: 6.5px; color: #64748b; margin-top: 2px; text-transform: uppercase; letter-spacing: 0.5px; }

/* ── ADMIN TABLE VIEW ── */
.section-head {
    background: {{ $roleColor['light'] }};
    border-left: 4px solid {{ $roleColor['bg'] }};
    padding: 5px 10px;
    margin-bottom: 4px;
    margin-top: 8px;
    border-radius: 0 4px 4px 0;
    page-break-after: avoid;
}
.section-title {
    font-size: 8.5px;
    font-weight: bold;
    color: {{ $roleColor['text'] }};
    text-transform: uppercase;
    letter-spacing: 1px;
}
.section-sub { font-size: 7px; color: #64748b; margin-top: 1px; }

.tt {
    width: 100%;
    border-collapse: collapse;
    table-layout: fixed;
    margin-bottom: 6px;
    page-break-inside: avoid;
}
.tt-head-day {
    background: {{ $roleColor['bg'] }};
    color: white;
    text-align: center;
    font-size: 7px;
    font-weight: bold;
    padding: 4px 2px;
    border: 1px solid rgba(0,0,0,0.1);
}
.tt-head-day.today { background: #059669; }
.tt-head-s {
    background: #f1f5f9;
    color: #475569;
    text-align: center;
    font-size: 6px;
    font-weight: bold;
    padding: 3px 1px;
    border: 1px solid #e2e8f0;
}
.tt-group-cell {
    padding: 4px 6px;
    border: 1px solid #e2e8f0;
    border-right: 2px solid #cbd5e1;
    background: white;
    min-width: 55px;
    max-width: 55px;
    width: 55px;
    vertical-align: middle;
}
.g-name { font-size: 7.5px; font-weight: bold; color: #1e293b; }
.g-sub  { font-size: 6px;   color: #94a3b8; margin-top: 1px; }

.tt-empty { border: 1px solid #f1f5f9; min-height: 36px; height: 36px; background: white; }
.tt-sess  { border: 1px solid #e2e8f0; vertical-align: top; padding: 2px 3px; }
.mini-card {
    border-left: 2px solid {{ $roleColor['bg'] }};
    background: {{ $roleColor['light'] }};
    padding: 2px 3px;
    border-radius: 0 3px 3px 0;
    min-height: 32px;
}
.mini-card.dist {
    border-left-color: #f59e0b;
    background: #fefce8;
}
.mini-time   { font-size: 5.5px; color: #64748b; font-weight: bold; margin-bottom: 1px; }
.mini-module { font-size: 6.5px; font-weight: bold; color: {{ $roleColor['text'] }}; line-height: 1.2; }
.mini-card.dist .mini-module { color: #92400e; }
.mini-row    { font-size: 5.5px; color: #475569; margin-top: 1px; }

/* ── LEGEND & FOOTER ── */
.legend {
    margin-top: 8px;
    padding: 5px 10px;
    border-top: 1px solid #e2e8f0;
    display: table;
    width: 100%;
}
.legend-l { display: table-cell; vertical-align: middle; }
.legend-r { display: table-cell; vertical-align: middle; text-align: right; }
.l-item   { display: inline-block; font-size: 6.5px; color: #475569; margin-right: 12px; }
.l-dot    {
    display: inline-block; width: 8px; height: 8px;
    border-radius: 2px; margin-right: 3px; vertical-align: middle;
}
.footer {
    text-align: center;
    font-size: 6px;
    color: #94a3b8;
    margin-top: 6px;
    padding-top: 5px;
    border-top: 1px solid #f1f5f9;
}



</style>
</head>
<body>

{{-- ════ HEADER (same for all roles) ════ --}}
<div class="hdr">
    <div class="hdr-inner">
        <div class="hdr-left">
            <div class="hdr-org">OFPPT — Emploi du temps</div>
            <div class="hdr-sub">Office de la Formation Professionnelle et de la Promotion du Travail</div>
            <div style="margin-top:5px;">
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
{{-- ════════════════════════════════════════════════
     PERSONAL VIEW — stagiaire / formateur
     A clean card-based weekly view, fits one page
     ════════════════════════════════════════════════ --}}

@php
    // Compute stats
    $totalH  = 0;
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

{{-- Stats row --}}
<div class="stats-row">
    <div class="stat-box">
        <div class="stat-num">{{ $totalSessions }}</div>
        <div class="stat-label">Séances</div>
    </div>
    <div class="stat-box">
        <div class="stat-num">{{ number_format($totalH, 1) }}h</div>
        <div class="stat-label">Total heures</div>
    </div>
    <div class="stat-box">
        <div class="stat-num">{{ $jours }}</div>
        <div class="stat-label">Jours actifs</div>
    </div>
    <div class="stat-box">
        <div class="stat-num">{{ $presCount }}</div>
        <div class="stat-label">Présentiel</div>
    </div>
    <div class="stat-box">
        <div class="stat-num">{{ $distCount }}</div>
        <div class="stat-label">À distance</div>
    </div>
</div>

@if($totalSessions === 0)
    {{-- Empty week --}}
    <div style="text-align:center; padding:40px 20px; color:#94a3b8;">
        <div style="font-size:14px; font-weight:bold; color:#cbd5e1; margin-bottom:6px;">Semaine libre</div>
        <div style="font-size:8px;">Aucune séance planifiée pour cette semaine.</div>
    </div>
@else
    {{-- Day columns — only show all 6 days to match the timetable look --}}
    <div class="week-grid">
        @foreach($dayDates as $dayNum => $date)
        @php
            $daySessions = $allSessions[$dayNum] ?? [];
            $isToday = $date->isToday();
        @endphp
        <div class="day-col">
            <div class="day-head" style="{{ $isToday ? 'background:#059669;' : '' }}">
                <div class="day-name">{{ strtoupper($date->translatedFormat('D')) }}</div>
                <div class="day-date">{{ $date->format('d') }}</div>
                <div class="day-mon">{{ $date->translatedFormat('M') }}{{ $isToday ? ' ★' : '' }}</div>
            </div>

            @forelse($daySessions as $sess)
                @php
                    $emploi  = $sess['emploi'];
                    $colspan = $sess['colspan'];
                    $sNum    = $sess['seance'];
                    $groupe  = $sess['groupe'];
                    $isRemote = ($emploi->mode ?? 'presentiel') === 'distance';
                    $spanLbl = EmploiDuTempsController::spanLabel($sNum, $colspan);
                    $totalHs = EmploiDuTempsController::totalHours($sNum, $colspan);
                @endphp
                <div class="sess-card {{ $isRemote ? 'dist' : '' }}">
                    @if($isRemote)
                        <div class="dist-pill">À DISTANCE</div>
                    @endif
                    <div class="sess-time">
                        {{ $emploi->date_debut->format('H:i') }} → {{ $emploi->date_fin->format('H:i') }}
                        &nbsp;·&nbsp;{{ $spanLbl }}&nbsp;·&nbsp;{{ $totalHs }}h
                    </div>
                    <div class="sess-module">{{ $emploi->module->name ?? 'Module' }}</div>
                    @if($user->role === 'stagiaire')
                        <span class="sess-row">{{ $emploi->gestionnaire->name ?? '—' }}</span>
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
{{-- ════════════════════════════════════════════════
     ADMIN / GESTIONNAIRE — full grid view
     ════════════════════════════════════════════════ --}}

@forelse($groupesByFiliere as $filiereId => $groupes)
    @php $filiere = $groupes->first()->filiere; @endphp

    {{-- Filière section header --}}
    <div class="section-head">
        <div class="section-title">
            {{ strtoupper($filiere->name ?? 'Filière') }}
           
        </div>
        <div class="section-sub">{{ $groupes->count() }} groupe{{ $groupes->count() > 1 ? 's' : '' }}</div>
    </div>

    <table class="tt">
        <colgroup>
            <col style="width:55px;">
            @foreach($dayDates as $dayNum => $date)
                @foreach(EmploiDuTempsController::SEANCES as $sNum => $s)
                    <col>
                @endforeach
            @endforeach
        </colgroup>

        {{-- Day header --}}
        <thead>
        <tr>
            <th style="background:#f8fafc; border:1px solid #e2e8f0; padding:3px;"></th>
            @foreach($dayDates as $dayNum => $date)
                @php $isToday = $date->isToday(); $isLast = $dayNum === 6; @endphp
                <th colspan="4" class="tt-head-day {{ $isToday ? 'today' : '' }}"
                    style="{{ !$isLast ? 'border-right:2px solid rgba(255,255,255,0.4);' : '' }}">
                    {{ strtoupper($date->translatedFormat('D')) }} {{ $date->format('d') }} {{ $date->translatedFormat('M') }}
                    @if($isToday) ★ @endif
                </th>
            @endforeach
        </tr>
        <tr>
            <th style="background:#f8fafc; border:1px solid #e2e8f0; font-size:6px; color:#94a3b8; padding:2px 4px; text-align:left;">Groupe</th>
            @foreach($dayDates as $dayNum => $date)
                @php $isLast = $dayNum === 6; @endphp
                @foreach(EmploiDuTempsController::SEANCES as $sNum => $seance)
                    @php $isLastS = $sNum === 4; @endphp
                    <th class="tt-head-s"
                        style="{{ $isLastS && !$isLast ? 'border-right:2px solid #94a3b8;' : '' }}">
                        {{ $seance['label'] }}<br>{{ $seance['start'] }}
                    </th>
                @endforeach
            @endforeach
        </tr>
        </thead>

        <tbody>
        @foreach($groupes as $groupe)
        <tr>
            <td class="tt-group-cell">
                <div class="g-name">{{ $groupe->name ?? 'G'.$groupe->id }}</div>
                <div class="g-sub">{{ $groupe->option->titre ?? $filiere->name ?? '' }}</div>
            </td>

            @foreach($dayDates as $dayNum => $date)
                @foreach(EmploiDuTempsController::SEANCES as $sNum => $seance)
                    @php
                        $cell      = $grid[$groupe->id][$dayNum][$sNum] ?? ['type' => 'empty'];
                        $isLastS   = $sNum === 4;
                        $isLastDay = $dayNum === 6;
                    @endphp

                    @if($cell['type'] === 'skip')
                        {{-- merged --}}

                    @elseif($cell['type'] === 'session')
                        @php
                            $emploi   = $cell['emploi'];
                            $colspan  = $cell['colspan'];
                            $isRemote = ($emploi->mode ?? 'presentiel') === 'distance';
                            $spanLbl  = EmploiDuTempsController::spanLabel($sNum, $colspan);
                            $lastS    = $sNum + $colspan - 1;
                            $borderR  = ($lastS % 4 === 0 && !$isLastDay) ? 'border-right:2px solid #94a3b8;' : '';
                        @endphp
                        <td class="tt-sess" colspan="{{ $colspan }}" style="{{ $borderR }}">
                            <div class="mini-card {{ $isRemote ? 'dist' : '' }}">
                                <div class="mini-time">
                                    {{ $emploi->date_debut->format('H:i') }}–{{ $emploi->date_fin->format('H:i') }}
                                    &nbsp;{{ $spanLbl }}
                                </div>
                                <div class="mini-module">{{ $emploi->module->name ?? 'Module' }}</div>
                                <div class="mini-row">{{ $emploi->gestionnaire->name ?? '—' }}</div>
                                @if(!$isRemote)
                                    <div class="mini-row">{{ $emploi->salle->name ?? '—' }}</div>
                                @else
                                    <div class="mini-row" style="color:#b45309;">⬡ Distance</div>
                                @endif
                            </div>
                        </td>

                    @else
                        <td class="tt-empty"
                            style="{{ ($isLastS && !$isLastDay) ? 'border-right:2px solid #e9edf2;' : '' }}">
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
        <span style="font-size:6.5px; font-weight:bold; color:{{ $roleColor['text'] }}; text-transform:uppercase; letter-spacing:1px; margin-right:10px;">
            Créneaux :
        </span>
        @foreach(EmploiDuTempsController::SEANCES as $sNum => $s)
            <span class="l-item">
                <strong>{{ $s['label'] }}</strong> {{ $s['start'] }}–{{ $s['end'] }} ({{ $s['hours'] }}h)
            </span>
        @endforeach
    </div>
    <div class="legend-r">
        <span class="l-item">
            <span class="l-dot" style="background:{{ $roleColor['light'] }}; border:1.5px solid {{ $roleColor['bg'] }};"></span>Présentiel
        </span>
        <span class="l-item">
            <span class="l-dot" style="background:#fef3c7; border:1.5px solid #f59e0b;"></span>À distance
        </span>
    </div>
</div>

<div class="footer">
    OFPPT — Emploi du temps {{ $yearLabel }} — Semaine du {{ $weekStart->format('d/m/Y') }} — {{ ucfirst($user->role) }} : {{ $user->name }} — Document généré automatiquement
</div>

</body>
</html>