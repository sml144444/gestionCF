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

/* ── Module card ── */
.mn-card { background:white;border-radius:16px;border:1px solid #e2e8f0;overflow:hidden;margin-bottom:18px;transition:box-shadow .15s; }
.mn-card:hover { box-shadow:0 4px 20px rgba(0,0,0,.07); }
.mn-card-head { padding:16px 20px;background:#f8fafc;border-bottom:1px solid #f1f5f9;display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:10px; }

/* ── Table ── */
.mn-table { width:100%;border-collapse:collapse;font-size:12px; }
.mn-table th { padding:10px 18px;text-align:left;font-size:9px;font-weight:800;color:#94a3b8;text-transform:uppercase;letter-spacing:.9px;background:#f8fafc;border-bottom:2px solid #f1f5f9; }
.mn-table th.center { text-align:center; }
.mn-table td { padding:13px 18px;border-bottom:1px solid #f8fafc;vertical-align:middle; }
.mn-table tr:last-child td { border-bottom:none; }

/* ── Note pill ── */
.note-pill { display:inline-flex;align-items:center;justify-content:center;width:54px;height:30px;border-radius:8px;font-size:13px;font-weight:800; }
.note-high  { background:#f0fdf4;color:#16a34a;border:1.5px solid #bbf7d0; }
.note-mid   { background:#fffbeb;color:#d97706;border:1.5px solid #fde68a; }
.note-low   { background:#fff1f2;color:#dc2626;border:1.5px solid #fecdd3; }
.note-empty { background:#f8fafc;color:#cbd5e1;border:1.5px solid #e2e8f0; }

/* ── Moyenne badge ── */
.moy-badge { display:inline-flex;align-items:center;justify-content:center;padding:5px 14px;border-radius:99px;font-size:13px;font-weight:800; }
.moy-high  { background:#f0fdf4;color:#16a34a;border:1.5px solid #bbf7d0; }
.moy-mid   { background:#fffbeb;color:#d97706;border:1.5px solid #fde68a; }
.moy-low   { background:#fff1f2;color:#dc2626;border:1.5px solid #fecdd3; }
.moy-none  { background:#f8fafc;color:#94a3b8;border:1.5px solid #e2e8f0; }

/* ── Type badge ── */
.type-badge { display:inline-flex;align-items:center;font-size:9px;font-weight:800;padding:2px 8px;border-radius:99px;text-transform:uppercase;letter-spacing:.5px; }
.type-regional { background:#eff6ff;color:#1e40af;border:1px solid #bfdbfe; }
.type-local    { background:#fdf4ff;color:#7e22ce;border:1px solid #e9d5ff; }

/* ── Info chip ── */
.info-chip { display:inline-flex;align-items:center;gap:4px;padding:3px 10px;border-radius:99px;font-size:10px;font-weight:600;background:#f1f5f9;color:#475569;border:1px solid #e2e8f0; }

/* ── Stat card ── */
.mn-stat { background:white;border-radius:14px;border:1px solid #e2e8f0;padding:16px 20px;display:flex;align-items:center;gap:14px; }
.mn-stat-icon { width:42px;height:42px;border-radius:12px;flex-shrink:0;display:flex;align-items:center;justify-content:center; }
.mn-stat-val  { font-size:22px;font-weight:800;color:#1e293b;line-height:1; }
.mn-stat-lbl  { font-size:10px;font-weight:600;color:#94a3b8;text-transform:uppercase;letter-spacing:.6px;margin-top:3px; }

/* ── EFM column ── */
.efm-th { color:#dc2626 !important; }
.efm-max { font-size:9px;color:#94a3b8;margin-top:2px; }
</style>

<div class="mn-wrap">

{{-- ── HEADER ── --}}
<div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:12px;margin-bottom:20px;">
    <div>
        <h1 style="font-size:20px;font-weight:800;color:#0f172a;margin:0;">Mes Notes</h1>
        <p style="font-size:12px;color:#64748b;margin:4px 0 0;">
            Résultats par module — {{ Auth::user()->name }}
            @if($groupe)
                · Groupe <strong>{{ $groupe->name }}</strong>
            @endif
        </p>
    </div>
</div>

@if(! $groupe)
    {{-- No groupe assigned --}}
    <div style="padding:64px 32px;text-align:center;background:white;border-radius:16px;border:1px solid #e2e8f0;">
        <div style="width:56px;height:56px;border-radius:16px;background:{{ $light }};display:flex;align-items:center;justify-content:center;margin:0 auto 14px;">
            <svg width="26" height="26" fill="none" stroke="{{ $accent }}" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
        </div>
        <p style="font-size:15px;font-weight:700;color:#1e293b;margin:0 0 6px;">Aucun groupe assigné</p>
        <p style="font-size:12px;color:#94a3b8;">Vous n'êtes pas encore affecté à un groupe. Contactez votre gestionnaire.</p>
    </div>

@elseif($modulesWithNotes->isEmpty())
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
    $totalModules  = $modulesWithNotes->count();
    $notedModules  = $modulesWithNotes->filter(fn($m) => $m['moyenne'] !== null)->count();
    $globalMoyenne = $modulesWithNotes->filter(fn($m) => $m['moyenne'] !== null)->avg('moyenne');
    $globalMoyenne = $globalMoyenne ? round($globalMoyenne, 2) : null;
@endphp

<div style="display:grid;grid-template-columns:repeat(3,1fr);gap:12px;margin-bottom:22px;">
    <div class="mn-stat">
        <div class="mn-stat-icon" style="background:{{ $light }};">
            <svg width="20" height="20" fill="none" stroke="{{ $accent }}" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
        </div>
        <div>
            <div class="mn-stat-val">{{ $totalModules }}</div>
            <div class="mn-stat-lbl">Modules</div>
        </div>
    </div>
    <div class="mn-stat">
        <div class="mn-stat-icon" style="background:#eff6ff;">
            <svg width="20" height="20" fill="none" stroke="#2563eb" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/></svg>
        </div>
        <div>
            <div class="mn-stat-val" style="color:#1e40af;">{{ $notedModules }}/{{ $totalModules }}</div>
            <div class="mn-stat-lbl">Notes saisies</div>
        </div>
    </div>
    <div class="mn-stat">
        <div class="mn-stat-icon" style="background:{{ $globalMoyenne === null ? '#f8fafc' : ($globalMoyenne >= 10 ? '#f0fdf4' : '#fff1f2') }};">
            <svg width="20" height="20" fill="none" stroke="{{ $globalMoyenne === null ? '#94a3b8' : ($globalMoyenne >= 10 ? '#16a34a' : '#dc2626') }}" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/></svg>
        </div>
        <div>
            <div class="mn-stat-val" style="color:{{ $globalMoyenne === null ? '#94a3b8' : ($globalMoyenne >= 10 ? '#16a34a' : '#dc2626') }};">
                {{ $globalMoyenne !== null ? $globalMoyenne . '/20' : '—' }}
            </div>
            <div class="mn-stat-lbl">Moyenne générale</div>
        </div>
    </div>
</div>

{{-- ── MODULE CARDS ── --}}
@foreach($modulesWithNotes as $item)
@php
    $module    = $item['module'];
    $controles = $item['controles'];
    $efm       = $item['efm'];
    $notes     = $item['notes'];
    $moyenne   = $item['moyenne'];

    $moyClass  = $moyenne === null ? 'moy-none' : ($moyenne >= 15 ? 'moy-high' : ($moyenne >= 10 ? 'moy-mid' : 'moy-low'));
    $hasAnyNote = $notes->isNotEmpty();
@endphp

<div class="mn-card">

    {{-- Card header --}}
    <div class="mn-card-head">
        <div style="display:flex;align-items:center;gap:10px;flex-wrap:wrap;">
            <div>
                <div style="font-size:14px;font-weight:800;color:#0f172a;">{{ $module->name }}</div>
                <div style="display:flex;gap:6px;margin-top:5px;flex-wrap:wrap;">
                    <span class="type-badge type-{{ $module->type }}">
                        {{ $module->type === 'regional' ? '🌍 Régional' : '📍 Local' }}
                    </span>
                    <span class="info-chip">Coeff. {{ $module->coefficience }}</span>
                    <span class="info-chip">{{ $module->nbr_heure }}h</span>
                    @if($module->formateur)
                    <span class="info-chip">
                        <svg width="10" height="10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                        {{ $module->formateur->name }}
                    </span>
                    @endif
                </div>
            </div>
        </div>

        {{-- Moyenne --}}
        <div style="text-align:right;">
            <div style="font-size:9px;font-weight:700;color:#94a3b8;text-transform:uppercase;letter-spacing:1px;margin-bottom:4px;">Moyenne</div>
            <span class="moy-badge {{ $moyClass }}">
                {{ $moyenne !== null ? $moyenne . ' / 20' : '—' }}
            </span>
        </div>
    </div>

    {{-- Notes table --}}
    @if(! $hasAnyNote && $controles->isEmpty() && ! $efm)
        <div style="padding:28px;text-align:center;color:#94a3b8;font-size:12px;">
            Aucune note saisie pour ce module.
        </div>
    @else
    <div style="overflow-x:auto;">
        <table class="mn-table">
            <thead>
                <tr>
                    @foreach($controles as $ctrl)
                    <th class="center">{{ $ctrl->titre }}<br><span style="font-size:8px;font-weight:600;color:#cbd5e1;">/ 20</span></th>
                    @endforeach
                    @if($efm)
                    <th class="center efm-th">⚑ EFM<br><span style="font-size:8px;font-weight:600;color:#fca5a5;">/ 40</span></th>
                    @endif
                    <th class="center" style="color:#7c3aed;">Moyenne</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    {{-- Contrôle notes --}}
                    @foreach($controles as $ctrl)
                    @php
                        $val = $notes[$ctrl->id] ?? null;
                        if ($val !== null) {
                            $noteClass = $val >= 15 ? 'note-high' : ($val >= 10 ? 'note-mid' : 'note-low');
                            $display   = number_format($val, 2);
                        } else {
                            $noteClass = 'note-empty';
                            $display   = '—';
                        }
                    @endphp
                    <td style="text-align:center;">
                        <span class="note-pill {{ $noteClass }}">{{ $display }}</span>
                    </td>
                    @endforeach

                    {{-- EFM note — stored /20, display /40 --}}
                    @if($efm)
                    @php
                        $efmRaw  = $notes[$efm->id] ?? null;
                        $efmDisp = $efmRaw !== null ? number_format($efmRaw * 2, 2) : null;
                        if ($efmDisp !== null) {
                            $efmPct   = ($efmRaw / 20);
                            $efmClass = $efmPct >= 0.75 ? 'note-high' : ($efmPct >= 0.50 ? 'note-mid' : 'note-low');
                        } else {
                            $efmClass = 'note-empty';
                            $efmDisp  = '—';
                        }
                    @endphp
                    <td style="text-align:center;">
                        <span class="note-pill {{ $efmClass }}" style="{{ $efmClass !== 'note-empty' ? 'width:64px;' : '' }}">{{ $efmDisp }}</span>
                    </td>
                    @endif

                    {{-- Moyenne --}}
                    <td style="text-align:center;">
                        <span class="moy-badge {{ $moyClass }}" style="font-size:12px;padding:4px 12px;">
                            {{ $moyenne !== null ? $moyenne : '—' }}
                        </span>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
    @endif

</div>
@endforeach

@endif

</div>
@endsection