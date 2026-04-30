{{-- resources/views/controles/my-notes.blade.php --}}
@extends('layouts.app')
@section('title', 'Mes Notes')
@section('page-title', 'Mes Notes')

@section('content')

@php
    $accent = '#0a6640';
    $light  = '#e8f5ee';
    $text   = '#065f38';
    $shadow = 'rgba(10,102,64,0.18)';
@endphp

<style>
.mn-wrap { font-family:'Segoe UI',system-ui,sans-serif; }

/* ── Stat cards ── */
.mn-stat { background:white;border-radius:14px;border:1px solid #e2e8f0;padding:16px 20px;display:flex;align-items:center;gap:14px; }
.mn-stat-icon { width:42px;height:42px;border-radius:12px;flex-shrink:0;display:flex;align-items:center;justify-content:center; }
.mn-stat-val  { font-size:22px;font-weight:800;color:#1e293b;line-height:1; }
.mn-stat-lbl  { font-size:10px;font-weight:600;color:#94a3b8;text-transform:uppercase;letter-spacing:.6px;margin-top:3px; }

/* ── Main table ── */
.mn-table-wrap { background:white;border-radius:16px;border:1px solid #e2e8f0;overflow:hidden; }
.mn-table { width:100%;border-collapse:collapse;font-size:12px; }
.mn-table th { padding:10px 16px;text-align:left;font-size:9px;font-weight:800;color:#94a3b8;text-transform:uppercase;letter-spacing:1px;background:#f8fafc;border-bottom:1px solid #f1f5f9; }
.mn-table th.center { text-align:center; }
.mn-table td { padding:13px 16px;border-bottom:1px solid #f8fafc;vertical-align:middle;color:#1e293b; }
.mn-table tr:last-child > td { border-bottom:none; }

/* ── Clickable module row ── */
.mn-module-row { cursor:pointer;transition:background .12s; }
.mn-module-row:hover td { background:#f0fdf4; }
.mn-module-row.open td { background:#f0fdf4; border-bottom:none; }

/* ── Expand row ── */
.mn-expand-row { display:none; }
.mn-expand-row.open { display:table-row; }
.mn-expand-cell { padding:0 !important;border-bottom:1px solid #f1f5f9 !important; }
.mn-expand-inner { padding:16px 24px;background:#fafffe;border-top:1px dashed #bbf7d0; }

/* ── Detail table (inside expand) ── */
.det-table { width:100%;border-collapse:collapse;font-size:11px; }
.det-table th { padding:8px 12px;text-align:center;font-size:9px;font-weight:800;color:#94a3b8;text-transform:uppercase;letter-spacing:.8px;background:#f8fafc;border-radius:8px; }
.det-table td { padding:10px 12px;text-align:center;vertical-align:middle; }

/* ── Badges ── */
.type-badge { display:inline-flex;align-items:center;font-size:9px;font-weight:800;padding:2px 8px;border-radius:99px;text-transform:uppercase;letter-spacing:.5px; }
.type-regional { background:#eff6ff;color:#1e40af;border:1px solid #bfdbfe; }
.type-local    { background:#fdf4ff;color:#7e22ce;border:1px solid #e9d5ff; }

/* ── Note pills ── */
.note-pill { display:inline-flex;align-items:center;justify-content:center;min-width:52px;height:28px;border-radius:8px;font-size:12px;font-weight:800;padding:0 8px; }
.note-high  { background:#f0fdf4;color:#16a34a;border:1.5px solid #bbf7d0; }
.note-mid   { background:#fffbeb;color:#d97706;border:1.5px solid #fde68a; }
.note-low   { background:#fff1f2;color:#dc2626;border:1.5px solid #fecdd3; }
.note-empty { background:#f8fafc;color:#cbd5e1;border:1.5px solid #e2e8f0; }

/* ── Module grade pill (larger) ── */
.moy-badge { display:inline-flex;align-items:center;justify-content:center;padding:4px 12px;border-radius:99px;font-size:12px;font-weight:800; }
.moy-high  { background:#f0fdf4;color:#16a34a;border:1.5px solid #bbf7d0; }
.moy-mid   { background:#fffbeb;color:#d97706;border:1.5px solid #fde68a; }
.moy-low   { background:#fff1f2;color:#dc2626;border:1.5px solid #fecdd3; }
.moy-none  { background:#f8fafc;color:#94a3b8;border:1.5px solid #e2e8f0; }

/* ── Chevron ── */
.chevron { transition:transform .2s; }
.open .chevron { transform:rotate(180deg); }

/* ── CC pill (purple) ── */
.cc-pill { display:inline-flex;align-items:center;justify-content:center;min-width:52px;height:28px;border-radius:8px;font-size:12px;font-weight:800;padding:0 8px; }
.cc-high  { background:#fdf4ff;color:#7e22ce;border:1.5px solid #e9d5ff; }
.cc-mid   { background:#fffbeb;color:#d97706;border:1.5px solid #fde68a; }
.cc-low   { background:#fff1f2;color:#dc2626;border:1.5px solid #fecdd3; }
.cc-empty { background:#f8fafc;color:#cbd5e1;border:1.5px solid #e2e8f0; }

/* ── Progress bar ── */
.prog-bar { height:4px;border-radius:99px;background:#e2e8f0;overflow:hidden;width:80px;display:inline-block;vertical-align:middle;margin-left:6px; }
.prog-fill { height:100%;border-radius:99px;transition:width .3s; }
</style>

<div class="mn-wrap">

{{-- ── HEADER ── --}}
<div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:12px;margin-bottom:20px;">
    <div>
        <h1 style="font-size:20px;font-weight:800;color:#0f172a;margin:0;">Mes Notes</h1>
        <p style="font-size:12px;color:#64748b;margin:4px 0 0;">
            {{ Auth::user()->name }}
            @if($groupe)
                · Groupe <strong>{{ $groupe->name }}</strong>
            @endif
        </p>
    </div>
</div>

@if(! $groupe)
    {{-- No group --}}
    <div style="padding:64px 32px;text-align:center;background:white;border-radius:16px;border:1px solid #e2e8f0;">
        <div style="width:56px;height:56px;border-radius:16px;background:{{ $light }};display:flex;align-items:center;justify-content:center;margin:0 auto 14px;">
            <svg width="26" height="26" fill="none" stroke="{{ $accent }}" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
        </div>
        <p style="font-size:15px;font-weight:700;color:#1e293b;margin:0 0 6px;">Aucun groupe assigné</p>
        <p style="font-size:12px;color:#94a3b8;">Vous n'êtes pas encore affecté à un groupe. Contactez votre gestionnaire.</p>
    </div>

@elseif($modulesWithNotes->isEmpty())
    {{-- No notes yet --}}
    <div style="padding:64px 32px;text-align:center;background:white;border-radius:16px;border:1px solid #e2e8f0;">
        <div style="width:56px;height:56px;border-radius:16px;background:#eff6ff;display:flex;align-items:center;justify-content:center;margin:0 auto 14px;">
            <svg width="26" height="26" fill="none" stroke="#2563eb" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        </div>
        <p style="font-size:15px;font-weight:700;color:#1e293b;margin:0 0 6px;">Aucune note disponible</p>
        <p style="font-size:12px;color:#94a3b8;">Les notes n'ont pas encore été saisies pour vos modules.</p>
    </div>

@else

{{-- ── STATS ── --}}
@php
    $totalModules = $modulesWithNotes->count();
    $notedModules = $modulesWithNotes->filter(fn($m) => $m['moduleGrade'] !== null)->count();
    $gaColor = $generalAverage === null ? '#94a3b8' : ($generalAverage >= 10 ? '#16a34a' : '#dc2626');
    $gaBg    = $generalAverage === null ? '#f8fafc'  : ($generalAverage >= 10 ? '#f0fdf4'  : '#fff1f2');
@endphp

<div style="display:grid;grid-template-columns:repeat(3,1fr);gap:12px;margin-bottom:22px;">
    <div class="mn-stat">
        <div class="mn-stat-icon" style="background:{{ $light }};">
            <svg width="20" height="20" fill="none" stroke="{{ $accent }}" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
        </div>
        <div><div class="mn-stat-val">{{ $totalModules }}</div><div class="mn-stat-lbl">Modules</div></div>
    </div>
    <div class="mn-stat">
        <div class="mn-stat-icon" style="background:#eff6ff;">
            <svg width="20" height="20" fill="none" stroke="#2563eb" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/></svg>
        </div>
        <div><div class="mn-stat-val" style="color:#1e40af;">{{ $notedModules }}/{{ $totalModules }}</div><div class="mn-stat-lbl">Modules notés</div></div>
    </div>
    <div class="mn-stat">
        <div class="mn-stat-icon" style="background:{{ $gaBg }};">
            <svg width="20" height="20" fill="none" stroke="{{ $gaColor }}" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/></svg>
        </div>
        <div>
            <div class="mn-stat-val" style="color:{{ $gaColor }};">
                {{ $generalAverage !== null ? $generalAverage . '/20' : '—' }}
            </div>
            <div class="mn-stat-lbl">Moyenne générale</div>
        </div>
    </div>
</div>

{{-- ── HINT ── --}}
<div style="display:flex;align-items:center;gap:6px;margin-bottom:12px;font-size:11px;color:#64748b;">
    <svg width="13" height="13" fill="none" stroke="#94a3b8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
    Cliquez sur le nom d'un module pour voir le détail des notes.
</div>

{{-- ── MODULE TABLE ── --}}
<div class="mn-table-wrap">
    <table class="mn-table">
        <thead>
            <tr>
                <th style="width:36px;"></th>
                <th>Module</th>
                <th>Type</th>
                <th class="center">Coeff.</th>
                <th class="center">Heures</th>
                <th class="center">CC /20</th>
                <th class="center">EFM /20</th>
                <th class="center">Note module</th>
            </tr>
        </thead>
        <tbody>
        @foreach($modulesWithNotes as $idx => $item)
        @php
            $module      = $item['module'];
            $controles   = $item['controles'];
            $efm         = $item['efm'];
            $notes       = $item['notes'];
            $cc          = $item['cc'];
            $efmDisplay  = $item['efmDisplay'];
            $moduleGrade = $item['moduleGrade'];

            $mgClass  = $moduleGrade === null ? 'moy-none'
                      : ($moduleGrade >= 15 ? 'moy-high' : ($moduleGrade >= 10 ? 'moy-mid' : 'moy-low'));
            $ccClass  = $cc === null ? 'cc-empty'
                      : ($cc >= 15 ? 'cc-high' : ($cc >= 10 ? 'cc-mid' : 'cc-low'));
            $efmClass = $efmDisplay === null ? 'note-empty'
                      : ($efmDisplay >= 15 ? 'note-high' : ($efmDisplay >= 10 ? 'note-mid' : 'note-low'));

            $rowId = 'mod-' . $idx;
        @endphp

        {{-- ── Summary row (clickable) ── --}}
        <tr class="mn-module-row" id="row-{{ $rowId }}" onclick="toggleModule('{{ $rowId }}')">

            {{-- Chevron --}}
            <td style="width:36px;text-align:center;padding:13px 8px;">
                <svg class="chevron" width="14" height="14" fill="none" stroke="#94a3b8" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"/>
                </svg>
            </td>

            {{-- Module name ── clickable look ── --}}
            <td>
                <div style="font-size:13px;font-weight:800;color:{{ $accent }};text-decoration:underline;text-underline-offset:3px;text-decoration-color:{{ $accent }}40;">
                    {{ $module->name }}
                </div>
                @if($module->formateur)
                <div style="font-size:10px;color:#94a3b8;margin-top:2px;">{{ $module->formateur->name }}</div>
                @endif
            </td>

            {{-- Type --}}
            <td>
                <span class="type-badge type-{{ $module->type }}">
                    {{ $module->type === 'regional' ? '🌍 Régional' : '📍 Local' }}
                </span>
            </td>

            {{-- Coeff --}}
            <td style="text-align:center;font-weight:700;color:#475569;">{{ $module->coefficience }}</td>

            {{-- Heures --}}
            <td style="text-align:center;font-weight:700;color:{{ $accent }};">{{ $module->nbr_heure }}h</td>

            {{-- CC --}}
            <td style="text-align:center;">
                <span class="cc-pill {{ $ccClass }}">
                    {{ $cc !== null ? number_format($cc, 2) : '—' }}
                </span>
            </td>

            {{-- EFM --}}
            <td style="text-align:center;">
                <span class="note-pill {{ $efmClass }}">
                    {{ $efmDisplay !== null ? number_format($efmDisplay, 2) : '—' }}
                </span>
            </td>

            {{-- Note module --}}
            <td style="text-align:center;">
                <span class="moy-badge {{ $mgClass }}">
                    {{ $moduleGrade !== null ? number_format($moduleGrade, 2) . ' / 20' : '—' }}
                </span>
                @if($moduleGrade !== null)
                <div class="prog-bar" style="display:block;margin:5px auto 0;">
                    <div class="prog-fill" style="width:{{ min(100, $moduleGrade / 20 * 100) }}%;background:{{ $moduleGrade >= 10 ? '#16a34a' : '#dc2626' }};"></div>
                </div>
                @endif
            </td>
        </tr>

        {{-- ── Detail expand row ── --}}
        <tr class="mn-expand-row" id="expand-{{ $rowId }}">
            <td colspan="8" class="mn-expand-cell">
                <div class="mn-expand-inner">

                    @if($controles->isEmpty() && ! $efm)
                        <p style="font-size:12px;color:#94a3b8;margin:0;">Aucune évaluation configurée pour ce module.</p>
                    @else
                        <div style="font-size:10px;font-weight:800;color:#94a3b8;text-transform:uppercase;letter-spacing:1.2px;margin-bottom:10px;">
                            Détail des évaluations — {{ $module->name }}
                        </div>
                        <div style="overflow-x:auto;">
                        <table class="det-table">
                            <thead>
                                <tr style="border-radius:8px;">
                                    @foreach($controles as $ctrl)
                                    <th>{{ $ctrl->titre }}<br><span style="font-size:8px;color:#cbd5e1;font-weight:600;">/ 20</span></th>
                                    @endforeach

                                    @if($controles->isNotEmpty())
                                    <th style="color:#7e22ce;">CC<br><span style="font-size:8px;color:#cbd5e1;font-weight:600;">/ 20</span></th>
                                    @endif

                                    @if($efm)
                                    <th style="color:#dc2626;">⚑ EFM<br><span style="font-size:8px;color:#fca5a5;font-weight:600;">/ 20</span></th>
                                    @endif

                                    <th style="color:#7c3aed;">Note module<br><span style="font-size:8px;color:#cbd5e1;font-weight:600;">/ 20</span></th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    @foreach($controles as $ctrl)
                                    @php
                                        $val = $notes[$ctrl->id] ?? null;
                                        $pc  = $val !== null ? ($val >= 15 ? 'note-high' : ($val >= 10 ? 'note-mid' : 'note-low')) : 'note-empty';
                                    @endphp
                                    <td>
                                        <span class="note-pill {{ $pc }}">
                                            {{ $val !== null ? number_format((float)$val, 2) : '—' }}
                                        </span>
                                    </td>
                                    @endforeach

                                    @if($controles->isNotEmpty())
                                    <td>
                                        <span class="cc-pill {{ $ccClass }}">
                                            {{ $cc !== null ? number_format($cc, 2) : '—' }}
                                        </span>
                                    </td>
                                    @endif

                                    @if($efm)
                                    <td>
                                        <span class="note-pill {{ $efmClass }}" style="min-width:60px;">
                                            {{ $efmDisplay !== null ? number_format($efmDisplay, 2) : '—' }}
                                        </span>
                                    </td>
                                    @endif

                                    <td>
                                        <span class="moy-badge {{ $mgClass }}" style="font-size:12px;">
                                            {{ $moduleGrade !== null ? number_format($moduleGrade, 2) : '—' }}
                                        </span>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                        </div>
                    @endif
                </div>
            </td>
        </tr>

        @endforeach
        </tbody>
    </table>
</div>

@endif

</div>

<script>
function toggleModule(id) {
    const row    = document.getElementById('row-'    + id);
    const expand = document.getElementById('expand-' + id);

    const isOpen = expand.classList.contains('open');

    // Close all
    document.querySelectorAll('.mn-expand-row').forEach(r => r.classList.remove('open'));
    document.querySelectorAll('.mn-module-row').forEach(r => r.classList.remove('open'));

    // Toggle current
    if (! isOpen) {
        expand.classList.add('open');
        row.classList.add('open');
    }
}
</script>

@endsection