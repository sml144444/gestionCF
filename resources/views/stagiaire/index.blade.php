{{-- resources/views/stagiaire/index.blade.php --}}
@extends('layouts.app')
@section('title', $filiereId ? 'Stagiaires — '.($selectedFiliere->name ?? '') : 'Stagiaires')
@section('page-title', 'Stagiaires')

@section('content')
@php
    $user    = Auth::user();
    $isAdmin = $user->role === 'admin';
    $accent  = $isAdmin ? '#0a6640' : '#1a4f8a';
    $light   = $isAdmin ? '#e8f5ee' : '#eff6ff';
    $text    = $isAdmin ? '#065f38' : '#1e40af';
    $border  = $isAdmin ? '#0a664030' : '#2563eb30';
    $shadow  = $isAdmin ? 'rgba(10,102,64,0.12)' : 'rgba(26,79,138,0.12)';
@endphp

<style>
.sg-wrap { font-family:'Segoe UI',system-ui,sans-serif; }

/* ── Filière cards ── */
.fil-card {
    background:white; border-radius:16px; border:1px solid #e2e8f0;
    overflow:hidden; cursor:pointer; text-decoration:none;
    transition:box-shadow .2s, transform .15s;
    display:block;
}
.fil-card:hover {
    box-shadow:0 8px 28px {{ $shadow }};
    transform:translateY(-2px);
    border-color:{{ $accent }}40;
}
.fil-card.active {
    border-color:{{ $accent }};
    box-shadow:0 6px 24px {{ $shadow }};
}
.fil-card-bar { height:5px; background:{{ $accent }}; }

/* ── Filter bar ── */
.sg-input {
    height:40px; padding:0 12px; border-radius:10px;
    border:1.5px solid #e2e8f0; background:#f8fafc;
    font-size:13px; color:#1e293b; outline:none;
    transition:border-color .15s,background .15s; box-sizing:border-box;
}
.sg-input:focus { border-color:{{ $accent }}; background:white; }

/* ── Table ── */
.sg-table { width:100%; border-collapse:collapse; }
.sg-table thead tr { background:{{ $light }}; border-bottom:2px solid {{ $accent }}30; }
.sg-table th { padding:10px 14px; font-size:9px; font-weight:800; color:{{ $text }}; text-transform:uppercase; letter-spacing:1.5px; text-align:left; white-space:nowrap; }
.sg-table tbody tr { border-bottom:1px solid #f1f5f9; transition:background .12s; }
.sg-table tbody tr:hover { background:{{ $light }}40; }
.sg-table td { padding:11px 14px; font-size:12px; color:#334155; }

.sg-badge { display:inline-block; font-size:10px; font-weight:700; padding:3px 9px; border-radius:8px; }
.sg-avatar { width:34px; height:34px; border-radius:9px; flex-shrink:0; display:flex; align-items:center; justify-content:center; font-size:11px; font-weight:800; background:{{ $light }}; color:{{ $text }}; border:1px solid {{ $border }}; }

/* ── Année pills ── */
.annee-pill { display:inline-flex; align-items:center; gap:5px; padding:6px 14px; border-radius:99px; font-size:11px; font-weight:700; border:1.5px solid #e2e8f0; background:white; color:#64748b; cursor:pointer; text-decoration:none; transition:all .15s; white-space:nowrap; }
.annee-pill:hover  { border-color:#8b5cf6; color:#6d28d9; background:#f5f3ff; }
.annee-pill.active { border-color:#8b5cf6; color:white; background:#7c3aed; }

/* ── Occupancy bar ── */
.occ-bar { height:4px; background:#e2e8f0; border-radius:99px; overflow:hidden; margin-top:6px; }
.occ-fill { height:100%; border-radius:99px; }

/* ── Group chips ── */
.grp-chip { display:inline-flex; align-items:center; gap:4px; font-size:10px; font-weight:600; padding:3px 9px; border-radius:7px; background:{{ $light }}; color:{{ $text }}; border:1px solid {{ $accent }}20; margin:2px; }

/* ── Action buttons ── */
.sg-btn-icon {
    width:30px; height:30px; border-radius:8px; border:none; cursor:pointer;
    display:inline-flex; align-items:center; justify-content:center;
    font-size:13px; transition:all .15s;
}
.sg-btn-edit   { background:#eff6ff; color:#2563eb; }
.sg-btn-edit:hover   { background:#2563eb; color:white; }
.sg-btn-delete { background:#fff1f2; color:#e11d48; }
.sg-btn-delete:hover { background:#e11d48; color:white; }

/* ── Modals ── */
.sg-overlay {
    position:fixed; inset:0; background:rgba(15,23,42,.5);
    display:flex; align-items:center; justify-content:center;
    z-index:999; padding:16px; backdrop-filter:blur(2px);
}
.sg-modal {
    background:white; border-radius:20px; padding:28px 28px 24px;
    width:100%; max-width:540px; max-height:90vh; overflow-y:auto;
    box-shadow:0 24px 72px rgba(0,0,0,.22); position:relative;
}
.sg-modal-title {
    font-size:16px; font-weight:800; color:#0f172a;
    margin:0 0 22px; padding-right:24px;
}
.sg-modal label {
    display:block; font-size:9px; font-weight:800; color:#94a3b8;
    text-transform:uppercase; letter-spacing:1.5px; margin-bottom:5px;
}
.sg-modal .sg-input  { width:100%; }
.sg-modal .field     { margin-bottom:14px; }
.sg-grid-2 { display:grid; grid-template-columns:1fr 1fr; gap:12px; }
.sg-modal .err { color:#e11d48; font-size:10px; margin-top:4px; display:block; }
.sg-modal-close {
    position:absolute; top:16px; right:16px; width:28px; height:28px;
    border-radius:8px; border:none; background:#f1f5f9; color:#64748b;
    cursor:pointer; font-size:14px; display:flex; align-items:center; justify-content:center;
}
.sg-modal-close:hover { background:#e2e8f0; color:#0f172a; }
.sg-modal-footer { display:flex; gap:8px; justify-content:flex-end; margin-top:22px; padding-top:16px; border-top:1px solid #f1f5f9; }
.sg-btn-primary {
    height:40px; padding:0 20px; border-radius:10px; border:none;
    background:{{ $accent }}; color:white; font-size:13px; font-weight:700;
    cursor:pointer; display:inline-flex; align-items:center; gap:6px; transition:opacity .15s;
}
.sg-btn-primary:hover { opacity:.88; }
.sg-btn-danger {
    height:40px; padding:0 20px; border-radius:10px; border:none;
    background:#e11d48; color:white; font-size:13px; font-weight:700;
    cursor:pointer; display:inline-flex; align-items:center; gap:6px; transition:opacity .15s;
}
.sg-btn-danger:hover { opacity:.88; }
.sg-btn-ghost {
    height:40px; padding:0 14px; border-radius:10px;
    border:1.5px solid #e2e8f0; background:white; color:#64748b;
    font-size:13px; font-weight:600; cursor:pointer; text-decoration:none;
    display:inline-flex; align-items:center; transition:all .15s;
}
.sg-btn-ghost:hover { border-color:#94a3b8; color:#334155; }

@media(max-width:500px) { .sg-grid-2 { grid-template-columns:1fr; } }
</style>

<div class="sg-wrap">

{{-- ── Flash messages ── --}}
@if(session('success'))
<div style="margin-bottom:16px;padding:12px 16px;border-radius:12px;font-size:13px;display:flex;align-items:center;gap:8px;background:#f0fdf4;border:1px solid #bbf7d0;color:#15803d;">
    <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
    {{ session('success') }}
</div>
@endif

@if($errors->any())
<div style="margin-bottom:16px;padding:12px 16px;border-radius:12px;font-size:13px;background:#fff1f2;border:1px solid #fecdd3;color:#be123c;">
    <strong style="font-weight:700;">Erreurs de validation :</strong>
    <ul style="margin:6px 0 0 16px;padding:0;">
        @foreach($errors->all() as $e)
        <li style="margin-bottom:3px;">{{ $e }}</li>
        @endforeach
    </ul>
</div>
@endif


{{-- ══════════════════════════════════════════════
     MODE A — NO FILIÈRE SELECTED: Filière cards
══════════════════════════════════════════════ --}}
@if(!$filiereId)

<div style="display:flex;align-items:flex-start;justify-content:space-between;flex-wrap:wrap;gap:12px;margin-bottom:24px;">
    <div>
        <h1 style="font-size:20px;font-weight:800;color:#0f172a;margin:0;">Stagiaires</h1>
        <p style="font-size:12px;color:#64748b;margin:4px 0 0;">Sélectionnez une filière pour voir ses stagiaires</p>
    </div>
    <div style="padding:10px 18px;border-radius:12px;background:{{ $light }};border:1px solid {{ $border }};text-align:center;">
        <div style="font-size:24px;font-weight:800;color:{{ $accent }};">{{ $totalStagiaires }}</div>
        <div style="font-size:9px;font-weight:700;color:{{ $text }};text-transform:uppercase;letter-spacing:.5px;">Stagiaires total</div>
    </div>
</div>

<div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(280px,1fr));gap:16px;">
    @forelse($filieres as $filiere)
    @php
        $grps   = $filiere->groupes;
        $total  = $filiere->stagiaires_count;
        $cap    = $grps->sum('nbr_limit');
        $occ    = $cap > 0 ? min(100, round(($total / $cap) * 100)) : 0;
        $occClr = $occ >= 90 ? '#dc2626' : ($occ >= 70 ? '#f59e0b' : '#16a34a');
        $grps1  = $grps->where('annee', 1);
        $grps2  = $grps->where('annee', 2);
    @endphp
    <a href="{{ route('stagiaire.index', ['filiere_id' => $filiere->id]) }}" class="fil-card">
        <div class="fil-card-bar"></div>
        <div style="padding:18px 20px;">
            <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:10px;margin-bottom:14px;">
                <div>
                    <div style="font-size:15px;font-weight:800;color:#0f172a;line-height:1.3;">{{ $filiere->name }}</div>
                    <div style="font-size:10px;color:#64748b;margin-top:2px;">{{ $filiere->duree }} an{{ $filiere->duree > 1 ? 's' : '' }} de formation</div>
                </div>
                <div style="min-width:48px;height:48px;border-radius:12px;background:{{ $light }};border:1px solid {{ $border }};display:flex;flex-direction:column;align-items:center;justify-content:center;flex-shrink:0;">
                    <div style="font-size:18px;font-weight:800;color:{{ $accent }};line-height:1;">{{ $total }}</div>
                    <div style="font-size:8px;color:{{ $text }};font-weight:700;text-transform:uppercase;">élèves</div>
                </div>
            </div>
            <div style="display:flex;justify-content:space-between;font-size:10px;color:#64748b;margin-bottom:3px;">
                <span>{{ $total }} / {{ $cap }} places</span>
                <span style="font-weight:700;color:{{ $occClr }};">{{ $occ }}%</span>
            </div>
            <div class="occ-bar"><div class="occ-fill" style="width:{{ $occ }}%;background:{{ $occClr }};"></div></div>
            @if($grps->isNotEmpty())
            <div style="margin-top:14px;">
                @if($grps1->isNotEmpty())
                <div style="font-size:9px;font-weight:800;color:#94a3b8;text-transform:uppercase;letter-spacing:1px;margin-bottom:5px;">1ère année</div>
                <div style="display:flex;flex-wrap:wrap;margin-bottom:8px;">
                    @foreach($grps1 as $g)
                    <span class="grp-chip">{{ $g->name }}<span style="font-size:8px;opacity:.7;">{{ $g->stagiaires_count }}</span></span>
                    @endforeach
                </div>
                @endif
                @if($grps2->isNotEmpty())
                <div style="font-size:9px;font-weight:800;color:#94a3b8;text-transform:uppercase;letter-spacing:1px;margin-bottom:5px;">2ème année</div>
                <div style="display:flex;flex-wrap:wrap;">
                    @foreach($grps2 as $g)
                    <span class="grp-chip">{{ $g->name }}<span style="font-size:8px;opacity:.7;">{{ $g->stagiaires_count }}</span></span>
                    @endforeach
                </div>
                @endif
            </div>
            @else
            <div style="margin-top:14px;font-size:11px;color:#94a3b8;font-style:italic;">Aucun groupe</div>
            @endif
        </div>
        <div style="padding:10px 20px;border-top:1px solid #f1f5f9;background:#fafbfc;display:flex;align-items:center;justify-content:space-between;">
            <span style="font-size:10px;color:#64748b;">{{ $grps->count() }} groupe(s)</span>
            <span style="font-size:11px;font-weight:700;color:{{ $accent }};display:flex;align-items:center;gap:4px;">
                Voir les stagiaires
                <svg width="10" height="10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
            </span>
        </div>
    </a>
    @empty
    <div style="grid-column:1/-1;padding:64px;text-align:center;background:white;border-radius:16px;border:1px solid #e2e8f0;">
        <div style="font-size:36px;margin-bottom:12px;">🏫</div>
        <p style="font-size:14px;font-weight:700;color:#334155;margin:0;">Aucune filière créée.</p>
    </div>
    @endforelse
</div>


{{-- ══════════════════════════════════════════════
     MODE B — FILIÈRE SELECTED: Detail + CRUD
══════════════════════════════════════════════ --}}
@else

{{-- Back + filière navigation --}}
<div style="display:flex;align-items:center;gap:12px;margin-bottom:20px;flex-wrap:wrap;">
    <a href="{{ route('stagiaire.index') }}"
       style="display:inline-flex;align-items:center;gap:6px;padding:8px 14px;border-radius:10px;border:1.5px solid #e2e8f0;background:white;color:#475569;font-size:12px;font-weight:600;text-decoration:none;transition:all .15s;"
       onmouseover="this.style.borderColor='{{ $accent }}';this.style.color='{{ $text }}'"
       onmouseout="this.style.borderColor='#e2e8f0';this.style.color='#475569'">
        <svg width="12" height="12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"/></svg>
        Toutes les filières
    </a>
    @foreach($filieres as $f)
    <a href="{{ route('stagiaire.index', ['filiere_id' => $f->id]) }}"
       style="display:inline-flex;align-items:center;gap:5px;padding:6px 12px;border-radius:99px;font-size:11px;font-weight:600;text-decoration:none;transition:all .15s;white-space:nowrap;
              {{ $f->id == $filiereId ? 'background:'.$accent.';color:white;border:1.5px solid '.$accent.';' : 'background:white;color:#64748b;border:1.5px solid #e2e8f0;' }}">
        {{ $f->name }}
        <span style="font-size:9px;padding:1px 5px;border-radius:99px;font-weight:800;{{ $f->id == $filiereId ? 'background:rgba(255,255,255,0.25);color:white;' : 'background:'.$light.';color:'.$text.';' }}">
            {{ $f->stagiaires_count }}
        </span>
    </a>
    @endforeach
</div>

{{-- Title + stats + create button --}}
<div style="display:flex;align-items:flex-start;justify-content:space-between;flex-wrap:wrap;gap:12px;margin-bottom:20px;">
    <div>
        <h1 style="font-size:20px;font-weight:800;color:#0f172a;margin:0;">
            {{ $selectedFiliere->name ?? 'Filière' }}
        </h1>
        <p style="font-size:12px;color:#64748b;margin:4px 0 0;">
            {{ $stagiaires->total() }} stagiaire{{ $stagiaires->total() > 1 ? 's' : '' }}
            @if($hasFilters) <span style="color:{{ $accent }};font-weight:600;">(filtrés)</span> @endif
        </p>
    </div>
    <div style="display:flex;gap:10px;flex-wrap:wrap;align-items:center;">
        {{-- Stats --}}
        <div style="padding:8px 14px;border-radius:12px;background:{{ $light }};border:1px solid {{ $border }};text-align:center;">
            <div style="font-size:20px;font-weight:800;color:{{ $accent }};">{{ $stagiaires->total() }}</div>
            <div style="font-size:9px;font-weight:700;color:{{ $text }};text-transform:uppercase;letter-spacing:.5px;">Résultats</div>
        </div>
        @php $groupesStat = $groupes->groupBy('annee'); @endphp
        @foreach($groupesStat as $yr => $grpList)
        <div style="padding:8px 14px;border-radius:12px;background:#f8fafc;border:1px solid #e2e8f0;text-align:center;">
            <div style="font-size:20px;font-weight:800;color:#334155;">{{ $grpList->count() }}</div>
            <div style="font-size:9px;font-weight:700;color:#64748b;text-transform:uppercase;letter-spacing:.5px;">Grp An.{{ $yr }}</div>
        </div>
        @endforeach

        {{-- Create button --}}
        @can('stagiaire-create')
        <button onclick="openCreateModal()" class="sg-btn-primary" style="height:42px;">
            <svg width="13" height="13" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
            Nouveau stagiaire
        </button>
        @endcan
    </div>
</div>

{{-- ── ANNÉE SCOLAIRE PILLS ── --}}
@if($hasAnneeScolaireColumn && $anneesScolaires->count())
<div style="margin-bottom:14px;">
    <div style="font-size:9px;font-weight:800;color:#7c3aed;text-transform:uppercase;letter-spacing:1.5px;margin-bottom:8px;display:flex;align-items:center;gap:5px;">
        <svg width="10" height="10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
        Année scolaire
    </div>
    <div style="display:flex;gap:8px;flex-wrap:wrap;">
        <a href="{{ route('stagiaire.index', array_merge(request()->except('annee_scolaire','page'), ['filiere_id' => $filiereId])) }}"
           class="annee-pill {{ $anneeScolaire === '' ? 'active' : '' }}">Toutes</a>
        @foreach($anneesScolaires as $as)
        <a href="{{ route('stagiaire.index', array_merge(request()->except('annee_scolaire','page'), ['filiere_id' => $filiereId, 'annee_scolaire' => $as])) }}"
           class="annee-pill {{ $anneeScolaire === $as ? 'active' : '' }}">📅 {{ $as }}</a>
        @endforeach
    </div>
</div>
@endif

{{-- ── FILTER FORM ── --}}
<form method="GET" action="{{ route('stagiaire.index') }}"
      style="display:flex;gap:10px;flex-wrap:wrap;align-items:flex-end;margin-bottom:18px;
             padding:14px 16px;background:white;border-radius:14px;border:1px solid #e2e8f0;">
    <input type="hidden" name="filiere_id" value="{{ $filiereId }}">
    @if($anneeScolaire !== '') <input type="hidden" name="annee_scolaire" value="{{ $anneeScolaire }}"> @endif

    <div style="flex:2;min-width:180px;">
        <label style="display:block;font-size:9px;font-weight:800;color:#94a3b8;text-transform:uppercase;letter-spacing:1.5px;margin-bottom:5px;">Recherche</label>
        <div style="position:relative;">
            <svg width="13" height="13" fill="none" stroke="#94a3b8" viewBox="0 0 24 24" style="position:absolute;left:11px;top:50%;transform:translateY(-50%);"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            <input type="text" name="search" value="{{ $search }}" placeholder="Nom, email ou CIN…" class="sg-input" style="padding-left:32px;width:100%;">
        </div>
    </div>

    <div style="flex:1;min-width:140px;">
        <label style="display:block;font-size:9px;font-weight:800;color:#94a3b8;text-transform:uppercase;letter-spacing:1.5px;margin-bottom:5px;">Groupe</label>
        <select name="groupe_id" class="sg-input" style="width:100%;appearance:none;cursor:pointer;">
            <option value="">Tous</option>
            @foreach($groupes as $g)
            <option value="{{ $g->id }}" {{ $groupeId == $g->id ? 'selected' : '' }}>{{ $g->name }} (An.{{ $g->annee }})</option>
            @endforeach
        </select>
    </div>

    <div style="flex:1;min-width:130px;">
        <label style="display:block;font-size:9px;font-weight:800;color:#94a3b8;text-transform:uppercase;letter-spacing:1.5px;margin-bottom:5px;">Année d'étude</label>
        <select name="annee" class="sg-input" style="width:100%;appearance:none;cursor:pointer;">
            <option value="">Toutes</option>
            <option value="1" {{ $annee == 1 ? 'selected' : '' }}>1ère année</option>
            <option value="2" {{ $annee == 2 ? 'selected' : '' }}>2ème / 2.5 ans</option>
        </select>
    </div>

    @if(isset($promos) && $promos->count())
    <div style="flex:1;min-width:130px;">
        <label style="display:block;font-size:9px;font-weight:800;color:#94a3b8;text-transform:uppercase;letter-spacing:1.5px;margin-bottom:5px;">Promotion</label>
        <select name="promo" class="sg-input" style="width:100%;appearance:none;cursor:pointer;">
            <option value="">Toutes</option>
            @foreach($promos as $p)
            <option value="{{ $p }}" {{ $promo == $p ? 'selected' : '' }}>Promo {{ $p }}</option>
            @endforeach
        </select>
    </div>
    @endif

    @if($hasAnneeScolaireColumn && $anneesScolaires->count())
    <div style="flex:1;min-width:140px;">
        <label style="display:block;font-size:9px;font-weight:800;color:#94a3b8;text-transform:uppercase;letter-spacing:1.5px;margin-bottom:5px;">Promo / Saison</label>
        <select name="annee_scolaire" class="sg-input" style="width:100%;appearance:none;cursor:pointer;">
            <option value="">Toutes les promos</option>
            @foreach($anneesScolaires as $as)
            <option value="{{ $as }}" {{ $anneeScolaire === $as ? 'selected' : '' }}>📅 {{ $as }}</option>
            @endforeach
        </select>
    </div>
    @endif

    @if($options->count())
    <div style="flex:1;min-width:130px;">
        <label style="display:block;font-size:9px;font-weight:800;color:#94a3b8;text-transform:uppercase;letter-spacing:1.5px;margin-bottom:5px;">Option</label>
        <select name="option_id" class="sg-input" style="width:100%;appearance:none;cursor:pointer;">
            <option value="">Toutes</option>
            @foreach($options as $opt)
            <option value="{{ $opt->id }}" {{ $optionId == $opt->id ? 'selected' : '' }}>{{ $opt->titre }}</option>
            @endforeach
        </select>
    </div>
    @endif

    <div style="display:flex;gap:8px;align-items:flex-end;">
        <button type="submit" style="height:40px;padding:0 16px;border-radius:10px;border:none;background:{{ $accent }};color:white;font-size:13px;font-weight:600;cursor:pointer;display:inline-flex;align-items:center;gap:6px;">
            <svg width="12" height="12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            Filtrer
        </button>
        @if($hasFilters)
        <a href="{{ route('stagiaire.index', ['filiere_id' => $filiereId] + ($anneeScolaire ? ['annee_scolaire' => $anneeScolaire] : [])) }}"
           style="height:40px;padding:0 12px;border-radius:10px;border:1.5px solid #e2e8f0;background:white;color:#64748b;font-size:13px;font-weight:600;text-decoration:none;display:inline-flex;align-items:center;">
            ✕ Reset
        </a>
        @endif
    </div>
</form>

{{-- ── TABLE ── --}}
<div style="background:white;border-radius:16px;border:1px solid #e2e8f0;box-shadow:0 2px 12px rgba(0,0,0,0.06);overflow:hidden;">
    @if($stagiaires->isEmpty())
    <div style="padding:64px;text-align:center;">
        <div style="font-size:32px;margin-bottom:12px;">👥</div>
        <p style="font-size:14px;font-weight:700;color:#334155;margin:0 0 4px;">Aucun stagiaire trouvé</p>
        <p style="font-size:12px;color:#94a3b8;margin:0;">Essayez de modifier les filtres.</p>
    </div>
    @else
    <div style="overflow-x:auto;">
        <table class="sg-table">
            <thead>
                <tr>
                    <th style="padding-left:18px;">Stagiaire</th>
                    <th>Groupe</th>
                    <th>Année</th>
                    @if($hasAnneeScolaireColumn) <th>Saison</th> @endif
                    <th>Option</th>
                    <th>Contact</th>
                    @canany(['stagiaire-edit','stagiaire-delete']) <th style="text-align:right;padding-right:18px;">Actions</th> @endcanany
                </tr>
            </thead>
            <tbody>
                @foreach($stagiaires as $stagiaire)
                @php
                    $initials   = strtoupper(substr($stagiaire->name,0,1))
                                . strtoupper(substr(explode(' ',$stagiaire->name.' ')[1]??'',0,1));
                    $anneeLabel = ($stagiaire->groupe?->annee == 2) ? '2ème' : '1ère';
                    $groupeAS   = $hasAnneeScolaireColumn ? ($stagiaire->groupe?->annee_scolaire ?? null) : null;
                @endphp
                <tr>
                    {{-- Identity --}}
                    <td style="padding-left:18px;">
                        <div style="display:flex;align-items:center;gap:10px;min-width:0;">
                            <div class="sg-avatar">{{ $initials }}</div>
                            <div style="min-width:0;">
                                <div style="font-size:13px;font-weight:700;color:#0f172a;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">{{ $stagiaire->name }}</div>
                                <div style="font-size:11px;color:#64748b;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">{{ $stagiaire->email }}</div>
                                @if($stagiaire->cin)
                                <div style="font-size:10px;color:#94a3b8;">{{ $stagiaire->cin }}</div>
                                @endif
                            </div>
                        </div>
                    </td>

                    {{-- Groupe --}}
                    <td>
                        @if($stagiaire->groupe)
                        <span class="sg-badge" style="background:#f1f5f9;color:#334155;border:1px solid #e2e8f0;">{{ $stagiaire->groupe->name ?? 'G'.$stagiaire->groupe->id }}</span>
                        @else
                        <span style="font-size:11px;color:#94a3b8;font-style:italic;">Non assigné</span>
                        @endif
                    </td>

                    {{-- Année --}}
                    <td>
                        <span class="sg-badge" style="{{ $stagiaire->groupe?->annee == 2 ? 'background:#fff7ed;color:#9a3412;border:1px solid #fed7aa;' : 'background:#eff6ff;color:#1e40af;border:1px solid #bfdbfe;' }}">{{ $anneeLabel }}</span>
                    </td>

                    {{-- Année scolaire --}}
                    @if($hasAnneeScolaireColumn)
                    <td>
                        @if($groupeAS)
                        <span style="font-size:10px;font-weight:600;color:#7c3aed;">📅 {{ $groupeAS }}</span>
                        @else
                        <span style="font-size:11px;color:#94a3b8;">—</span>
                        @endif
                    </td>
                    @endif

                    {{-- Option --}}
                    <td>
                        @if($stagiaire->option)
                        <span class="sg-badge" style="background:#fdf4ff;color:#6b21a8;border:1px solid #e9d5ff;font-size:10px;max-width:120px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;display:inline-block;">{{ $stagiaire->option->titre }}</span>
                        @else
                        <span style="font-size:11px;color:#94a3b8;">—</span>
                        @endif
                    </td>

                    {{-- Contact --}}
                    <td>
                        <div style="display:flex;gap:5px;flex-wrap:wrap;">
                            @if($stagiaire->phone)
                            <span style="font-size:10px;color:#64748b;background:#f8fafc;border:1px solid #e2e8f0;padding:3px 7px;border-radius:6px;">📞 {{ $stagiaire->phone }}</span>
                            @endif
                            @if($stagiaire->date_naissance)
                            <span style="font-size:10px;color:#64748b;background:#f8fafc;border:1px solid #e2e8f0;padding:3px 7px;border-radius:6px;">🎂 {{ $stagiaire->date_naissance->format('d/m/Y') }}</span>
                            @endif
                            @if(!$stagiaire->phone && !$stagiaire->date_naissance)
                            <span style="font-size:11px;color:#94a3b8;">—</span>
                            @endif
                        </div>
                    </td>

                    {{-- Actions --}}
                    @canany(['stagiaire-edit','stagiaire-delete'])
                    <td style="text-align:right;padding-right:18px;">
                        <div style="display:flex;gap:5px;justify-content:flex-end;">
                            @can('stagiaire-edit')
                            <button type="button" class="sg-btn-icon sg-btn-edit"
                                    title="Modifier"
                                    onclick="openEditModal(this)"
                                    data-id="{{ $stagiaire->id }}"
                                    data-name="{{ e($stagiaire->name) }}"
                                    data-email="{{ $stagiaire->email }}"
                                    data-cin="{{ $stagiaire->cin ?? '' }}"
                                    data-phone="{{ $stagiaire->phone ?? '' }}"
                                    data-dob="{{ $stagiaire->date_naissance?->format('Y-m-d') ?? '' }}"
                                    data-groupe="{{ $stagiaire->id_groupe ?? '' }}"
                                    data-option="{{ $stagiaire->id_option ?? '' }}">
                                <svg width="13" height="13" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                            </button>
                            @endcan
                            @can('stagiaire-delete')
                            <button type="button" class="sg-btn-icon sg-btn-delete"
                                    title="Supprimer"
                                    onclick="openDeleteModal({{ $stagiaire->id }}, '{{ addslashes($stagiaire->name) }}')">
                                <svg width="13" height="13" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                            </button>
                            @endcan
                        </div>
                    </td>
                    @endcanany
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif
</div>

{{-- Pagination --}}
@if($stagiaires && $stagiaires->hasPages())
<div style="margin-top:16px;display:flex;justify-content:center;">
    {{ $stagiaires->links() }}
</div>
@endif


{{-- ══════════════════════════════════════════════
     MODALS
══════════════════════════════════════════════ --}}

{{-- ── CREATE MODAL ── --}}
@can('stagiaire-create')
<div id="modal-create" class="sg-overlay" style="display:none;" onclick="if(event.target===this)closeModal('modal-create')">
    <div class="sg-modal">
        <button type="button" class="sg-modal-close" onclick="closeModal('modal-create')">✕</button>
        <h2 class="sg-modal-title">
            <span style="display:inline-flex;align-items:center;gap:8px;">
                <span style="width:28px;height:28px;border-radius:8px;background:{{ $light }};display:inline-flex;align-items:center;justify-content:center;font-size:14px;">👤</span>
                Nouveau stagiaire
            </span>
        </h2>

        <form method="POST" action="{{ route('stagiaire.store') }}">
            @csrf
            <input type="hidden" name="_modal" value="create">
            <input type="hidden" name="id_filiere" value="{{ $filiereId }}">

            {{-- Row 1: Nom + Email --}}
            <div class="sg-grid-2">
                <div class="field">
                    <label>Nom complet <span style="color:#e11d48;">*</span></label>
                    <input type="text" name="name" class="sg-input" value="{{ old('name') }}" placeholder="Ex: Youssef Ait Ali" required autofocus>
                    @error('name') <span class="err">{{ $message }}</span> @enderror
                </div>
                <div class="field">
                    <label>Email <span style="color:#e11d48;">*</span></label>
                    <input type="email" name="email" class="sg-input" value="{{ old('email') }}" placeholder="email@exemple.com" required>
                    @error('email') <span class="err">{{ $message }}</span> @enderror
                </div>
            </div>

            {{-- Row 2: Password + CIN --}}
            <div class="sg-grid-2">
                <div class="field">
                    <label>Mot de passe <span style="color:#e11d48;">*</span></label>
                    <input type="password" name="password" class="sg-input" placeholder="Min. 6 caractères" required>
                    @error('password') <span class="err">{{ $message }}</span> @enderror
                </div>
                <div class="field">
                    <label>CIN</label>
                    <input type="text" name="cin" class="sg-input" value="{{ old('cin') }}" placeholder="Ex: BE123456">
                    @error('cin') <span class="err">{{ $message }}</span> @enderror
                </div>
            </div>

            {{-- Row 3: Téléphone + Date naissance --}}
            <div class="sg-grid-2">
                <div class="field">
                    <label>Téléphone</label>
                    <input type="text" name="phone" class="sg-input" value="{{ old('phone') }}" placeholder="06 00 00 00 00">
                    @error('phone') <span class="err">{{ $message }}</span> @enderror
                </div>
                <div class="field">
                    <label>Date de naissance</label>
                    <input type="date" name="date_naissance" class="sg-input" value="{{ old('date_naissance') }}">
                    @error('date_naissance') <span class="err">{{ $message }}</span> @enderror
                </div>
            </div>

            {{-- Row 4: Groupe + Option --}}
            <div class="sg-grid-2">
                <div class="field">
                    <label>Groupe</label>
                    <select name="id_groupe" class="sg-input" style="appearance:none;cursor:pointer;">
                        <option value="">— Aucun —</option>
                        @foreach($allGroupes as $g)
                        <option value="{{ $g->id }}" {{ old('id_groupe') == $g->id ? 'selected' : '' }}>
                            {{ $g->name }} (An.{{ $g->annee }})
                        </option>
                        @endforeach
                    </select>
                    @error('id_groupe') <span class="err">{{ $message }}</span> @enderror
                </div>
                <div class="field">
                    <label>Option</label>
                    <select name="id_option" class="sg-input" style="appearance:none;cursor:pointer;">
                        <option value="">— Aucune —</option>
                        @foreach($options as $opt)
                        <option value="{{ $opt->id }}" {{ old('id_option') == $opt->id ? 'selected' : '' }}>{{ $opt->titre }}</option>
                        @endforeach
                    </select>
                    @error('id_option') <span class="err">{{ $message }}</span> @enderror
                </div>
            </div>

            <div class="sg-modal-footer">
                <button type="button" class="sg-btn-ghost" onclick="closeModal('modal-create')">Annuler</button>
                <button type="submit" class="sg-btn-primary">
                    <svg width="13" height="13" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
                    Créer le stagiaire
                </button>
            </div>
        </form>
    </div>
</div>
@endcan

{{-- ── EDIT MODAL ── --}}
@can('stagiaire-edit')
<div id="modal-edit" class="sg-overlay" style="display:none;" onclick="if(event.target===this)closeModal('modal-edit')">
    <div class="sg-modal">
        <button type="button" class="sg-modal-close" onclick="closeModal('modal-edit')">✕</button>
        <h2 class="sg-modal-title">
            <span style="display:inline-flex;align-items:center;gap:8px;">
                <span style="width:28px;height:28px;border-radius:8px;background:#eff6ff;display:inline-flex;align-items:center;justify-content:center;font-size:14px;">✏️</span>
                Modifier le stagiaire
            </span>
        </h2>

        <form id="form-edit" method="POST" action="#">
            @csrf
            @method('PUT')
            <input type="hidden" name="_modal" value="edit">
            <input type="hidden" name="_stagiaire_id" id="edit-sid" value="{{ old('_stagiaire_id') }}">
            <input type="hidden" name="id_filiere" value="{{ $filiereId }}">

            <div class="sg-grid-2">
                <div class="field">
                    <label>Nom complet <span style="color:#e11d48;">*</span></label>
                    <input type="text" name="name" id="edit-name" class="sg-input"
                           value="{{ old('name', '') }}" placeholder="Nom complet" required>
                    @error('name') <span class="err">{{ $message }}</span> @enderror
                </div>
                <div class="field">
                    <label>Email <span style="color:#e11d48;">*</span></label>
                    <input type="email" name="email" id="edit-email" class="sg-input"
                           value="{{ old('email', '') }}" placeholder="email@exemple.com" required>
                    @error('email') <span class="err">{{ $message }}</span> @enderror
                </div>
            </div>

            <div class="sg-grid-2">
                <div class="field">
                    <label>Nouveau mot de passe <span style="color:#94a3b8;font-weight:500;text-transform:none;letter-spacing:0;">(laisser vide = inchangé)</span></label>
                    <input type="password" name="password" class="sg-input" placeholder="Nouveau mot de passe…">
                    @error('password') <span class="err">{{ $message }}</span> @enderror
                </div>
                <div class="field">
                    <label>CIN</label>
                    <input type="text" name="cin" id="edit-cin" class="sg-input"
                           value="{{ old('cin', '') }}" placeholder="BE123456">
                    @error('cin') <span class="err">{{ $message }}</span> @enderror
                </div>
            </div>

            <div class="sg-grid-2">
                <div class="field">
                    <label>Téléphone</label>
                    <input type="text" name="phone" id="edit-phone" class="sg-input"
                           value="{{ old('phone', '') }}" placeholder="06 00 00 00 00">
                    @error('phone') <span class="err">{{ $message }}</span> @enderror
                </div>
                <div class="field">
                    <label>Date de naissance</label>
                    <input type="date" name="date_naissance" id="edit-dob" class="sg-input"
                           value="{{ old('date_naissance', '') }}">
                    @error('date_naissance') <span class="err">{{ $message }}</span> @enderror
                </div>
            </div>

            <div class="sg-grid-2">
                <div class="field">
                    <label>Groupe</label>
                    <select name="id_groupe" id="edit-groupe" class="sg-input" style="appearance:none;cursor:pointer;">
                        <option value="">— Aucun —</option>
                        @foreach($allGroupes as $g)
                        <option value="{{ $g->id }}">{{ $g->name }} (An.{{ $g->annee }})</option>
                        @endforeach
                    </select>
                    @error('id_groupe') <span class="err">{{ $message }}</span> @enderror
                </div>
                <div class="field">
                    <label>Option</label>
                    <select name="id_option" id="edit-option" class="sg-input" style="appearance:none;cursor:pointer;">
                        <option value="">— Aucune —</option>
                        @foreach($options as $opt)
                        <option value="{{ $opt->id }}">{{ $opt->titre }}</option>
                        @endforeach
                    </select>
                    @error('id_option') <span class="err">{{ $message }}</span> @enderror
                </div>
            </div>

            <div class="sg-modal-footer">
                <button type="button" class="sg-btn-ghost" onclick="closeModal('modal-edit')">Annuler</button>
                <button type="submit" class="sg-btn-primary">
                    <svg width="13" height="13" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                    Enregistrer les modifications
                </button>
            </div>
        </form>
    </div>
</div>
@endcan

{{-- ── DELETE MODAL ── --}}
@can('stagiaire-delete')
<div id="modal-delete" class="sg-overlay" style="display:none;" onclick="if(event.target===this)closeModal('modal-delete')">
    <div class="sg-modal" style="max-width:420px;">
        <button type="button" class="sg-modal-close" onclick="closeModal('modal-delete')">✕</button>

        <div style="text-align:center;padding:12px 0 20px;">
            <div style="width:56px;height:56px;border-radius:16px;background:#fff1f2;border:1px solid #fecdd3;display:flex;align-items:center;justify-content:center;font-size:24px;margin:0 auto 16px;">🗑️</div>
            <h2 style="font-size:16px;font-weight:800;color:#0f172a;margin:0 0 8px;">Supprimer le stagiaire ?</h2>
            <p style="font-size:13px;color:#64748b;margin:0;">
                Vous allez supprimer définitivement
                <strong id="delete-name" style="color:#0f172a;"></strong>.
                Cette action est irréversible.
            </p>
        </div>

        <form id="form-delete" method="POST" action="#">
            @csrf
            @method('DELETE')
            <div class="sg-modal-footer" style="justify-content:center;">
                <button type="button" class="sg-btn-ghost" onclick="closeModal('modal-delete')">Annuler</button>
                <button type="submit" class="sg-btn-danger">
                    <svg width="13" height="13" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                    Oui, supprimer
                </button>
            </div>
        </form>
    </div>
</div>
@endcan

@endif {{-- end mode B --}}

</div>{{-- .sg-wrap --}}


{{-- ── JAVASCRIPT ── --}}
@if($filiereId)
<script>
function openCreateModal() {
    document.getElementById('modal-create')?.style.setProperty('display','flex');
}

function openEditModal(btn) {
    var id      = btn.dataset.id;
    var form    = document.getElementById('form-edit');
    if (!form) return;

    // Set form action
    form.action = '/stagiaire/' + id;

    // Populate fields
    document.getElementById('edit-sid').value    = id;
    document.getElementById('edit-name').value   = btn.dataset.name;
    document.getElementById('edit-email').value  = btn.dataset.email;
    document.getElementById('edit-cin').value    = btn.dataset.cin;
    document.getElementById('edit-phone').value  = btn.dataset.phone;
    document.getElementById('edit-dob').value    = btn.dataset.dob;

    // Set selects
    setSelect('edit-groupe', btn.dataset.groupe);
    setSelect('edit-option', btn.dataset.option);

    // Clear password field
    form.querySelector('[name="password"]').value = '';

    document.getElementById('modal-edit').style.display = 'flex';
}

function openDeleteModal(id, name) {
    var form = document.getElementById('form-delete');
    if (!form) return;
    form.action = '/stagiaire/' + id;
    document.getElementById('delete-name').textContent = name;
    document.getElementById('modal-delete').style.display = 'flex';
}

function closeModal(id) {
    var m = document.getElementById(id);
    if (m) m.style.display = 'none';
}

function setSelect(selectId, value) {
    var sel = document.getElementById(selectId);
    if (!sel) return;
    for (var i = 0; i < sel.options.length; i++) {
        if (sel.options[i].value == value) {
            sel.selectedIndex = i;
            return;
        }
    }
    sel.selectedIndex = 0; // fallback to "— Aucun —"
}

// Close modals on Escape key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        ['modal-create','modal-edit','modal-delete'].forEach(closeModal);
    }
});

// Auto re-open modal after a validation error
@if(old('_modal') === 'create')
document.addEventListener('DOMContentLoaded', function() {
    document.getElementById('modal-create')?.style.setProperty('display','flex');
});
@elseif(old('_modal') === 'edit')
document.addEventListener('DOMContentLoaded', function() {
    var form = document.getElementById('form-edit');
    var sid  = '{{ old("_stagiaire_id") }}';
    if (form && sid) {
        form.action = '/stagiaire/' + sid;
        document.getElementById('modal-edit').style.display = 'flex';
    }
});
@endif
</script>
@endif

@endsection