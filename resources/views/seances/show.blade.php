{{-- resources/views/seances/show.blade.php --}}
@extends('layouts.app')
@section('title', ($emploi->module->name ?? 'Séance') . ' — Détail')
@section('page-title', 'Détail de la séance')

@section('content')
@php
    $palettes = [
        'admin'        => ['primary' => '#0a6640', 'light' => '#e8f5ee', 'medium' => '#1a8c56', 'text' => '#065f38', 'border' => '#bbf7d0'],
        'gestionnaire' => ['primary' => '#1e293b', 'light' => '#f1f5f9', 'medium' => '#334155', 'text' => '#1e293b', 'border' => '#cbd5e1'],
        'formateur'    => ['primary' => '#1a4f8a', 'light' => '#eff6ff', 'medium' => '#2563eb', 'text' => '#1e40af', 'border' => '#bfdbfe'],
        'stagiaire'    => ['primary' => '#1a4f8a', 'light' => '#eff6ff', 'medium' => '#2563eb', 'text' => '#1e40af', 'border' => '#bfdbfe'],
    ];
    $p           = $palettes[Auth::user()->role] ?? $palettes['stagiaire'];
    $accent      = $p['primary'];
    $isRemote    = $emploi->mode === 'distance';
    $isCancelled = $emploi->statut === 'annule';

    $presentCount = $stagiaires->count() - $presences->count();
    $absCount     = $presences->where('type', 'absence')->count();
    $retardCount  = $presences->where('type', 'retard')->count();
@endphp

<style>
.sc-wrap { font-family:'Segoe UI',system-ui,sans-serif; max-width:1060px; margin:0 auto; }

/* ── Hero ── */
.sc-hero {
    border-radius: 20px; padding: 26px 30px; margin-bottom: 22px;
    position: relative; overflow: hidden;
    background: linear-gradient(135deg, {{ $accent }} 0%, {{ $p['medium'] }} 100%);
    box-shadow: 0 8px 32px {{ $accent }}40;
}
.sc-hero::before {
    content: ''; position: absolute; right: -60px; top: -60px;
    width: 220px; height: 220px; border-radius: 50%;
    background: rgba(255,255,255,0.07); pointer-events: none;
}
.sc-hero::after {
    content: ''; position: absolute; left: -30px; bottom: -50px;
    width: 160px; height: 160px; border-radius: 50%;
    background: rgba(255,255,255,0.04); pointer-events: none;
}

/* ── Section card ── */
.sc-card {
    background: white; border-radius: 18px; border: 1px solid #e2e8f0;
    overflow: hidden; margin-bottom: 20px;
    box-shadow: 0 2px 12px rgba(0,0,0,0.04);
}
.sc-card-head {
    padding: 18px 24px; border-bottom: 1px solid #f1f5f9;
    display: flex; align-items: center; justify-content: space-between; gap: 12px;
    background: #fafbfc;
}
.sc-card-icon {
    width: 42px; height: 42px; border-radius: 12px; flex-shrink: 0;
    display: flex; align-items: center; justify-content: center;
    background: {{ $p['light'] }};
}
.sc-card-title { font-size: 14px; font-weight: 800; color: #1e293b; }
.sc-card-sub   { font-size: 10px; color: #64748b; margin-top: 2px; }

/* ── Presence table ── */
.pres-table { width: 100%; border-collapse: separate; border-spacing: 0; }
.pres-table th {
    padding: 10px 14px; font-size: 9px; font-weight: 800; color: #94a3b8;
    letter-spacing: 1.5px; text-transform: uppercase;
    background: #f8fafc; border-bottom: 2px solid #e2e8f0; text-align: left;
}
.pres-table td { padding: 10px 14px; border-bottom: 1px solid #f1f5f9; vertical-align: middle; }
.pres-table tr:last-child td { border-bottom: none; }
.pres-table tbody tr:hover td { background: #fafbfe; }

/* ── Status toggle ── */
.status-tog { display: inline-flex; border: 1.5px solid #e2e8f0; border-radius: 8px; overflow: hidden; }
.status-btn {
    padding: 5px 11px; font-size: 10px; font-weight: 700;
    border: none; cursor: pointer; background: white; color: #94a3b8; transition: all .15s;
    white-space: nowrap;
}
.s-present { background: #dcfce7 !important; color: #16a34a !important; }
.s-retard  { background: #fef3c7 !important; color: #d97706 !important; }
.s-absent  { background: #fee2e2 !important; color: #dc2626 !important; }

/* ── Classroom ── */
.res-item {
    display: flex; align-items: center; gap: 14px;
    padding: 14px 16px; border: 1px solid #e2e8f0; border-radius: 12px;
    margin-bottom: 10px; background: white; transition: all .15s;
}
.res-item:hover { border-color: {{ $accent }}30; background: {{ $p['light'] }}; transform: translateX(2px); }
.res-item:last-child { margin-bottom: 0; }
.res-icon {
    width: 44px; height: 44px; border-radius: 12px; flex-shrink: 0;
    display: flex; align-items: center; justify-content: center; font-size: 20px;
}

/* ── Form ── */
.sc-input {
    width: 100%; height: 40px; padding: 0 12px; border-radius: 10px;
    border: 1.5px solid #e2e8f0; background: #f8fafc; font-size: 13px;
    color: #1e293b; outline: none; transition: border-color .15s; box-sizing: border-box;
    font-family: inherit;
}
.sc-input:focus { border-color: {{ $accent }}; background: white; }
.sc-label {
    display: block; font-size: 9px; font-weight: 800; color: #94a3b8;
    letter-spacing: 1.5px; text-transform: uppercase; margin-bottom: 6px;
}
.sc-btn {
    display: inline-flex; align-items: center; gap: 6px;
    padding: 9px 18px; border-radius: 10px; font-size: 12px; font-weight: 700;
    border: none; cursor: pointer; transition: all .15s; text-decoration: none;
    white-space: nowrap;
}
.sc-btn-primary { background: {{ $accent }}; color: white; box-shadow: 0 3px 10px {{ $accent }}40; }
.sc-btn-primary:hover { opacity: .88; transform: translateY(-1px); }
.sc-btn-outline { background: white; border: 1.5px solid #e2e8f0; color: #475569; }
.sc-btn-outline:hover { border-color: {{ $accent }}30; color: #1e293b; }
.sc-btn-danger { background: #fee2e2; color: #dc2626; border: 1px solid #fecaca; }
.sc-btn-danger:hover { background: #fecaca; }
.sc-btn-sm { padding: 5px 12px; font-size: 11px; border-radius: 8px; }

/* ── Stat pill ── */
.stat-pill {
    display: inline-flex; align-items: center; gap: 5px;
    font-size: 10px; font-weight: 700; padding: 4px 12px; border-radius: 99px;
}
</style>

<div class="sc-wrap">

{{-- ── Flash ── --}}
@if(session('success'))
<div style="margin-bottom:16px;padding:12px 16px;border-radius:12px;font-size:13px;display:flex;align-items:center;gap:10px;background:{{ $p['light'] }};border:1px solid {{ $p['border'] }};color:{{ $p['text'] }};">
    <svg width="16" height="16" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
    <span style="font-weight:600;">{{ session('success') }}</span>
</div>
@endif
@if(session('error'))
<div style="margin-bottom:16px;padding:12px 16px;border-radius:12px;font-size:13px;display:flex;align-items:center;gap:10px;background:#fff1f2;border:1px solid #fecdd3;color:#be123c;">
    ⚠ <span style="font-weight:600;">{{ session('error') }}</span>
</div>
@endif

{{-- ════ HERO ════ --}}
<div class="sc-hero">
    <div style="position:relative;z-index:1;">
        <div style="display:flex;align-items:flex-start;justify-content:space-between;flex-wrap:wrap;gap:14px;margin-bottom:16px;">
            <div>
                <div style="font-size:9px;font-weight:800;letter-spacing:2px;color:rgba(255,255,255,0.65);text-transform:uppercase;margin-bottom:5px;">
                    {{ $emploi->date_debut->translatedFormat('l d MMMM Y') }}
                </div>
                <h1 style="font-size:20px;font-weight:800;color:white;margin:0 0 10px;line-height:1.2;">
                    {{ $emploi->module->name ?? 'Module' }}
                </h1>
                <div style="display:flex;flex-wrap:wrap;gap:8px;">
                    <span style="background:rgba(255,255,255,0.15);color:white;font-size:10px;font-weight:600;padding:4px 11px;border-radius:99px;display:inline-flex;align-items:center;gap:4px;">
                        👥 {{ $emploi->groupe->name ?? 'Groupe' }}
                    </span>
                    <span style="background:rgba(255,255,255,0.15);color:white;font-size:10px;font-weight:600;padding:4px 11px;border-radius:99px;display:inline-flex;align-items:center;gap:4px;">
                        👤 {{ ($emploi->remplacant ?? $emploi->gestionnaire)?->name ?? '—' }}
                        @if($emploi->remplacant)
                            <span style="opacity:.7;font-size:8px;">(remplaçant)</span>
                        @endif
                    </span>
                    @if(!$isRemote)
                    <span style="background:rgba(255,255,255,0.15);color:white;font-size:10px;font-weight:600;padding:4px 11px;border-radius:99px;display:inline-flex;align-items:center;gap:4px;">
                        🏫 {{ $emploi->salle->name ?? '—' }}
                    </span>
                    @else
                    <span style="background:rgba(245,158,11,0.3);color:#fef3c7;font-size:10px;font-weight:600;padding:4px 11px;border-radius:99px;border:1px solid rgba(245,158,11,0.35);display:inline-flex;align-items:center;gap:4px;">
                        🎥 À distance
                        @if($emploi->lien_distance)
                            · <a href="{{ $emploi->lien_distance }}" target="_blank" style="color:#fde68a;text-decoration:underline;font-size:9px;">Rejoindre</a>
                        @endif
                    </span>
                    @endif
                    <span style="background:rgba(255,255,255,0.15);color:white;font-size:10px;font-weight:600;padding:4px 11px;border-radius:99px;display:inline-flex;align-items:center;gap:4px;">
                        ⏰ {{ $emploi->date_debut->format('H:i') }} → {{ $emploi->date_fin->format('H:i') }}
                    </span>
                    <span style="font-size:10px;font-weight:700;padding:4px 11px;border-radius:99px;display:inline-flex;align-items:center;gap:4px;
                        @if($emploi->statut === 'actif') background:rgba(34,197,94,0.25);color:#bbf7d0;
                        @elseif($emploi->statut === 'brouillon') background:rgba(148,163,184,0.2);color:#e2e8f0;
                        @else background:rgba(239,68,68,0.25);color:#fecaca; @endif">
                        @if($emploi->statut === 'actif') ✓ Actif
                        @elseif($emploi->statut === 'brouillon') ✎ Brouillon
                        @else ✕ Annulé @endif
                    </span>
                </div>
            </div>
            <a href="{{ url()->previous() }}" class="sc-btn sc-btn-sm"
               style="background:rgba(255,255,255,0.15);border:1.5px solid rgba(255,255,255,0.25);color:white;flex-shrink:0;">
                ← Retour
            </a>
        </div>

        {{-- Quick-jump tabs --}}
        <div style="display:flex;gap:8px;flex-wrap:wrap;">
            @if($canPresence)
            <a href="#presence" style="background:rgba(255,255,255,0.15);color:white;text-decoration:none;font-size:10px;font-weight:700;padding:5px 14px;border-radius:99px;border:1px solid rgba(255,255,255,0.2);transition:all .15s;"
               onmouseover="this.style.background='rgba(255,255,255,0.25)'" onmouseout="this.style.background='rgba(255,255,255,0.15)'">
                ✅ Présence
            </a>
            @endif
            <a href="#classroom" style="background:rgba(255,255,255,0.15);color:white;text-decoration:none;font-size:10px;font-weight:700;padding:5px 14px;border-radius:99px;border:1px solid rgba(255,255,255,0.2);transition:all .15s;"
               onmouseover="this.style.background='rgba(255,255,255,0.25)'" onmouseout="this.style.background='rgba(255,255,255,0.15)'">
                📚 Classroom
            </a>
        </div>
    </div>
</div>

{{-- ════════════════════════════════════════
     SECTION 1 — PRÉSENCE
     ════════════════════════════════════════ --}}
@if($canPresence)
<div class="sc-card" id="presence">

    {{-- Head --}}
    <div class="sc-card-head">
        <div style="display:flex;align-items:center;gap:12px;">
            <div class="sc-card-icon">
                <svg width="20" height="20" fill="none" stroke="{{ $accent }}" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                </svg>
            </div>
            <div>
                <div class="sc-card-title">Liste de présence</div>
                <div class="sc-card-sub">Groupe {{ $emploi->groupe->name ?? '' }} · {{ $stagiaires->count() }} stagiaire(s)</div>
            </div>
        </div>
        <div style="display:flex;gap:8px;flex-wrap:wrap;">
            <span class="stat-pill" style="background:#dcfce7;color:#16a34a;">✓ {{ $presentCount }} présent(s)</span>
            @if($retardCount > 0)
            <span class="stat-pill" style="background:#fef3c7;color:#d97706;">⏳ {{ $retardCount }} retard(s)</span>
            @endif
            @if($absCount > 0)
            <span class="stat-pill" style="background:#fee2e2;color:#dc2626;">✕ {{ $absCount }} absent(s)</span>
            @endif
        </div>
    </div>

    {{-- Cancelled warning --}}
    @if($isCancelled)
    <div style="padding:12px 24px;background:#fff1f2;border-bottom:1px solid #fecdd3;font-size:12px;color:#9f1239;display:flex;align-items:center;gap:8px;">
        <svg width="14" height="14" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
        Séance annulée — la saisie de présence est désactivée.
    </div>
    @endif

    {{-- Table --}}
    <form action="{{ route('seances.presence', $emploi) }}" method="POST">
        @csrf
        <div style="overflow-x:auto;">
            <table class="pres-table">
                <thead>
                    <tr>
                        <th style="padding-left:24px;min-width:220px;">Stagiaire</th>
                        <th style="text-align:center;min-width:220px;">Statut</th>
                        <th style="min-width:60px;text-align:center;">Code</th>
                    </tr>
                </thead>
                <tbody>
                @forelse($stagiaires as $index => $stagiaire)
                    @php
                        $rec    = $presences[$stagiaire->id] ?? null;
                        $status = $rec ? $rec->type : 'present';
                    @endphp
                    <tr>
                        <td style="padding-left:24px;">
                            <div style="display:flex;align-items:center;gap:10px;">
                                <div style="width:36px;height:36px;border-radius:10px;background:{{ $p['light'] }};display:flex;align-items:center;justify-content:center;font-size:12px;font-weight:800;color:{{ $accent }};flex-shrink:0;">
                                    {{ strtoupper(substr($stagiaire->name, 0, 1)) }}
                                </div>
                                <div>
                                    <div style="font-size:12px;font-weight:700;color:#1e293b;">{{ $stagiaire->name }}</div>
                                    <div style="font-size:9px;color:#94a3b8;">{{ $stagiaire->email }}</div>
                                </div>
                            </div>
                            <input type="hidden" name="presences[{{ $index }}][stagiaire_id]" value="{{ $stagiaire->id }}">
                        </td>
                        <td style="text-align:center;">
                            <div class="status-tog" id="tog-{{ $stagiaire->id }}"
                                 style="{{ $isCancelled ? 'opacity:.45;pointer-events:none;' : '' }}">
                                <button type="button"
                                        onclick="setStatus({{ $stagiaire->id }}, 'present', this)"
                                        class="status-btn {{ $status === 'present' ? 's-present' : '' }}"
                                        title="Présent">
                                    ✓ Présent
                                </button>
                                <button type="button"
                                        onclick="setStatus({{ $stagiaire->id }}, 'retard', this)"
                                        class="status-btn {{ $status === 'retard' ? 's-retard' : '' }}"
                                        title="Retard"
                                        style="border-left:1px solid #e2e8f0;">
                                    ⏳ Retard
                                </button>
                                <button type="button"
                                        onclick="setStatus({{ $stagiaire->id }}, 'absence', this)"
                                        class="status-btn {{ $status === 'absence' ? 's-absent' : '' }}"
                                        title="Absent"
                                        style="border-left:1px solid #e2e8f0;">
                                    ✕ Absent
                                </button>
                            </div>
                            <input type="hidden"
                                   name="presences[{{ $index }}][status]"
                                   id="stat-{{ $stagiaire->id }}"
                                   value="{{ $status }}">
                        </td>
                        <td style="text-align:center;">
                            <span id="badge-{{ $stagiaire->id }}" class="stat-pill"
                                  style="font-size:9px;padding:3px 9px;
                                  @if($status === 'present') background:#dcfce7;color:#16a34a;
                                  @elseif($status === 'retard') background:#fef3c7;color:#d97706;
                                  @else background:#fee2e2;color:#dc2626; @endif">
                                @if($status === 'present') P @elseif($status === 'retard') R @else A @endif
                            </span>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" style="padding:40px;text-align:center;color:#94a3b8;font-size:12px;">
                            Aucun stagiaire inscrit dans ce groupe.
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>

        @if($stagiaires->isNotEmpty() && !$isCancelled)
        <div style="padding:16px 24px;border-top:1px solid #f1f5f9;display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:10px;background:#fafbfc;">
            <p style="font-size:11px;color:#94a3b8;margin:0;">
                Par défaut tous les stagiaires sont marqués présents.
            </p>
            <button type="submit" class="sc-btn sc-btn-primary">
                <svg width="13" height="13" fill="none" stroke="white" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                Enregistrer la présence
            </button>
        </div>
        @endif
    </form>
</div>
@endif

{{-- ════════════════════════════════════════
     SECTION 2 — CLASSROOM
     ════════════════════════════════════════ --}}
<div class="sc-card" id="classroom">

    {{-- Head --}}
    <div class="sc-card-head">
        <div style="display:flex;align-items:center;gap:12px;">
            <div class="sc-card-icon">
                <svg width="20" height="20" fill="none" stroke="{{ $accent }}" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                </svg>
            </div>
            <div>
                <div class="sc-card-title">Classroom de la séance</div>
                <div class="sc-card-sub">{{ $coursItems->count() }} ressource(s) disponible(s)</div>
            </div>
        </div>
        @if(!$canEditClassroom)
        <span class="stat-pill" style="background:{{ $p['light'] }};color:{{ $p['text'] }};font-size:9px;">
            👁 Lecture seule
        </span>
        @endif
    </div>

    {{-- ── Add resource form (admin / formateur only) ── --}}
    @if($canEditClassroom)
    <div style="padding:20px 24px;border-bottom:2px solid #f1f5f9;background:#fafbfc;">
        <div style="font-size:11px;font-weight:800;color:{{ $accent }};letter-spacing:.5px;margin-bottom:16px;display:flex;align-items:center;gap:6px;">
            <span style="display:inline-flex;width:20px;height:20px;border-radius:6px;background:{{ $p['light'] }};align-items:center;justify-content:center;font-size:12px;">+</span>
            Ajouter une ressource
        </div>
        <form action="{{ route('seances.ressource.store', $emploi) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @if($errors->any())
            <div style="margin-bottom:12px;padding:10px 14px;background:#fff1f2;border:1px solid #fecdd3;border-radius:10px;font-size:11px;color:#be123c;">
                @foreach($errors->all() as $e)<div>· {{ $e }}</div>@endforeach
            </div>
            @endif

            <div style="display:grid;grid-template-columns:1fr 200px;gap:14px;margin-bottom:14px;">
                <div>
                    <label class="sc-label">Titre <span style="color:#ef4444;">*</span></label>
                    <input type="text" name="titre" required value="{{ old('titre') }}"
                           class="sc-input" placeholder="Ex: TP Controllers, Cours AJAX, Lien GitHub…">
                </div>
                <div>
                    <label class="sc-label">Type <span style="color:#ef4444;">*</span></label>
                    <select name="type" id="res-type" onchange="onTypeChange()" required
                            class="sc-input" style="cursor:pointer;">
                        <option value="pdf"   {{ old('type') === 'pdf'   ? 'selected' : '' }}>📄 PDF / Fichier</option>
                        <option value="lien"  {{ old('type') === 'lien'  ? 'selected' : '' }}>🔗 Lien externe</option>
                        <option value="texte" {{ old('type') === 'texte' ? 'selected' : '' }}>📝 Contenu texte</option>
                    </select>
                </div>
            </div>

            {{-- File --}}
            <div id="row-pdf" style="margin-bottom:14px;">
                <label class="sc-label">Fichier (PDF, DOC, ZIP, image…) · max 20 Mo</label>
                <input type="file" name="fichier" accept=".pdf,.doc,.docx,.zip,.png,.jpg,.jpeg,.txt,.pptx,.xlsx"
                       class="sc-input" style="height:auto;padding:8px 12px;">
            </div>

            {{-- Link --}}
            <div id="row-lien" style="display:none;margin-bottom:14px;">
                <label class="sc-label">URL</label>
                <input type="url" name="lien" value="{{ old('lien') }}"
                       class="sc-input" placeholder="https://github.com/…">
            </div>

            {{-- Text --}}
            <div id="row-texte" style="display:none;margin-bottom:14px;">
                <label class="sc-label">Contenu</label>
                <textarea name="description" rows="4"
                          class="sc-input" style="height:auto;padding:10px 12px;resize:vertical;line-height:1.6;"
                          placeholder="Saisissez les consignes, notes de cours…">{{ old('description') }}</textarea>
            </div>

            <div style="display:flex;justify-content:flex-end;">
                <button type="submit" class="sc-btn sc-btn-primary">
                    <svg width="13" height="13" fill="none" stroke="white" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
                    Ajouter la ressource
                </button>
            </div>
        </form>
    </div>
    @endif

    {{-- ── Resources list ── --}}
    <div style="padding:20px 24px;">
        @forelse($coursItems as $item)
        @php
            $hasFichier = $item->fichier && count($item->fichier) > 0;
            $hasLien    = (bool) $item->lien;
            $hasTexte   = !$hasFichier && !$hasLien;

            if ($hasFichier) {
                $typeIcon = '📄'; $typeBg = '#fee2e2'; $typeColor = '#9f1239'; $typeLabel = 'PDF';
            } elseif ($hasLien) {
                $typeIcon = '🔗'; $typeBg = '#dbeafe'; $typeColor = '#1e40af'; $typeLabel = 'Lien';
            } else {
                $typeIcon = '📝'; $typeBg = '#dcfce7'; $typeColor = '#166534'; $typeLabel = 'Texte';
            }
        @endphp
        <div class="res-item">
            <div class="res-icon" style="background:{{ $typeBg }};">{{ $typeIcon }}</div>
            <div style="flex:1;min-width:0;">
                <div style="font-size:13px;font-weight:700;color:#1e293b;margin-bottom:3px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">
                    {{ $item->titre }}
                </div>
                <div style="display:flex;align-items:center;gap:8px;flex-wrap:wrap;margin-bottom:3px;">
                    <span style="font-size:9px;font-weight:700;padding:2px 8px;border-radius:6px;background:{{ $typeBg }};color:{{ $typeColor }};">
                        {{ $typeLabel }}
                    </span>
                    <span style="font-size:10px;color:#64748b;">{{ $item->formateur->name ?? '—' }}</span>
                    <span style="font-size:9px;color:#94a3b8;">{{ $item->created_at->diffForHumans() }}</span>
                </div>
                @if($hasTexte && $item->description)
                    <div style="font-size:11px;color:#475569;line-height:1.5;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;max-width:400px;">
                        {{ $item->description }}
                    </div>
                @endif
            </div>
            <div style="display:flex;align-items:center;gap:6px;flex-shrink:0;">
                @if($hasLien)
                    <a href="{{ $item->lien }}" target="_blank" class="sc-btn sc-btn-outline sc-btn-sm">
                        🔗 Ouvrir
                    </a>
                @elseif($hasFichier)
                    <a href="{{ Storage::url($item->fichier[0]) }}" download
                       class="sc-btn sc-btn-outline sc-btn-sm">
                        ⬇ Télécharger
                    </a>
                @else
                    <button onclick="openTextModal('{{ addslashes($item->titre) }}', '{{ addslashes($item->description ?? '') }}')"
                            class="sc-btn sc-btn-outline sc-btn-sm">
                        👁 Lire
                    </button>
                @endif

                @if($canEditClassroom)
                <form action="{{ route('seances.ressource.destroy', [$emploi, $item]) }}" method="POST"
                      onsubmit="return confirm('Supprimer la ressource « {{ addslashes($item->titre) }} » ?')">
                    @csrf @method('DELETE')
                    <button type="submit" class="sc-btn sc-btn-danger sc-btn-sm">🗑</button>
                </form>
                @endif
            </div>
        </div>
        @empty
        <div style="text-align:center;padding:44px 20px;">
            <div style="font-size:42px;margin-bottom:10px;">📚</div>
            <div style="font-size:13px;font-weight:700;color:#475569;margin-bottom:4px;">Aucune ressource pour le moment</div>
            @if($canEditClassroom)
                <div style="font-size:11px;color:#94a3b8;">Utilisez le formulaire ci-dessus pour ajouter fichiers, liens ou contenus.</div>
            @else
                <div style="font-size:11px;color:#94a3b8;">Le formateur n'a pas encore ajouté de ressources.</div>
            @endif
        </div>
        @endforelse
    </div>
</div>

</div>{{-- .sc-wrap --}}

{{-- Text content modal --}}
<div id="txt-modal" style="display:none;position:fixed;inset:0;z-index:60;background:rgba(15,23,42,0.5);backdrop-filter:blur(4px);align-items:center;justify-content:center;"
     onclick="if(event.target===this)closeTxtModal()">
    <div style="background:white;border-radius:20px;width:100%;max-width:560px;margin:16px;padding:24px;max-height:80vh;overflow-y:auto;box-shadow:0 24px 60px rgba(0,0,0,0.18);">
        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:16px;padding-bottom:14px;border-bottom:2px solid {{ $accent }};">
            <div id="txt-title" style="font-size:15px;font-weight:800;color:#1e293b;"></div>
            <button onclick="closeTxtModal()" style="width:28px;height:28px;border-radius:8px;border:none;background:#f1f5f9;color:#64748b;font-size:16px;cursor:pointer;display:flex;align-items:center;justify-content:center;">×</button>
        </div>
        <div id="txt-body" style="font-size:13px;color:#334155;line-height:1.75;white-space:pre-wrap;"></div>
    </div>
</div>

<script>
// ── Presence status toggle ──────────────────────────────────
function setStatus(id, status, btn) {
    // Clear all buttons in the toggle
    document.querySelectorAll('#tog-' + id + ' .status-btn').forEach(b => {
        b.className = 'status-btn';
    });

    // Activate clicked button
    const map = { present: 's-present', retard: 's-retard', absence: 's-absent' };
    btn.className = 'status-btn ' + (map[status] || '');

    // Update hidden field
    document.getElementById('stat-' + id).value = status;

    // Update badge
    const badge = document.getElementById('badge-' + id);
    const styles = {
        present: 'background:#dcfce7;color:#16a34a;',
        retard:  'background:#fef3c7;color:#d97706;',
        absence: 'background:#fee2e2;color:#dc2626;',
    };
    const labels = { present: 'P', retard: 'R', absence: 'A' };
    badge.style.cssText = 'font-size:9px;padding:3px 9px;display:inline-flex;align-items:center;gap:5px;font-weight:700;border-radius:99px;' + styles[status];
    badge.textContent = labels[status];
}

// ── Resource type toggle ────────────────────────────────────
function onTypeChange() {
    const t = document.getElementById('res-type').value;
    document.getElementById('row-pdf').style.display   = t === 'pdf'   ? 'block' : 'none';
    document.getElementById('row-lien').style.display  = t === 'lien'  ? 'block' : 'none';
    document.getElementById('row-texte').style.display = t === 'texte' ? 'block' : 'none';
}

// ── Text modal ──────────────────────────────────────────────
function openTextModal(titre, content) {
    document.getElementById('txt-title').textContent = titre;
    document.getElementById('txt-body').textContent  = content;
    document.getElementById('txt-modal').style.display = 'flex';
}
function closeTxtModal() {
    document.getElementById('txt-modal').style.display = 'none';
}

// Init type display
onTypeChange();
</script>
@endsection