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
.fil-card-bar {
    height:5px; background:{{ $accent }};
    border-radius:0;
}

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

/* Groupe group pills */
.grp-chip { display:inline-flex; align-items:center; gap:4px; font-size:10px; font-weight:600; padding:3px 9px; border-radius:7px; background:{{ $light }}; color:{{ $text }}; border:1px solid {{ $accent }}20; margin:2px; }
</style>

<div class="sg-wrap">

{{-- FLASH --}}
@if(session('success'))
    <div style="margin-bottom:16px;padding:12px 16px;border-radius:12px;font-size:13px;display:flex;align-items:center;gap:8px;background:#f0fdf4;border:1px solid #bbf7d0;color:#15803d;">
        ✓ {{ session('success') }}
    </div>
@endif

{{-- ══════════════════════════════════════════════
     MODE A — NO FILIÈRE SELECTED: Filière cards
══════════════════════════════════════════════ --}}
@if(!$filiereId)

{{-- Header --}}
<div style="display:flex;align-items:flex-start;justify-content:space-between;flex-wrap:wrap;gap:12px;margin-bottom:24px;">
    <div>
        <h1 style="font-size:20px;font-weight:800;color:#0f172a;margin:0;">Stagiaires</h1>
        <p style="font-size:12px;color:#64748b;margin:4px 0 0;">
            Sélectionnez une filière pour voir ses stagiaires
        </p>
    </div>
    <div style="padding:10px 18px;border-radius:12px;background:{{ $light }};border:1px solid {{ $border }};text-align:center;">
        <div style="font-size:24px;font-weight:800;color:{{ $accent }};">{{ $totalStagiaires }}</div>
        <div style="font-size:9px;font-weight:700;color:{{ $text }};text-transform:uppercase;letter-spacing:.5px;">Stagiaires total</div>
    </div>
</div>

{{-- Filière cards grid --}}
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
        {{-- Top color bar --}}
        <div class="fil-card-bar"></div>

        <div style="padding:18px 20px;">
            {{-- Name + count --}}
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

            {{-- Occupancy --}}
            <div style="display:flex;justify-content:space-between;font-size:10px;color:#64748b;margin-bottom:3px;">
                <span>{{ $total }} / {{ $cap }} places</span>
                <span style="font-weight:700;color:{{ $occClr }};">{{ $occ }}%</span>
            </div>
            <div class="occ-bar"><div class="occ-fill" style="width:{{ $occ }}%;background:{{ $occClr }};"></div></div>

            {{-- Groups --}}
            @if($grps->isNotEmpty())
            <div style="margin-top:14px;">
                @if($grps1->isNotEmpty())
                <div style="font-size:9px;font-weight:800;color:#94a3b8;text-transform:uppercase;letter-spacing:1px;margin-bottom:5px;">1ère année</div>
                <div style="display:flex;flex-wrap:wrap;margin-bottom:8px;">
                    @foreach($grps1 as $g)
                    <span class="grp-chip">
                        {{ $g->name }}
                        <span style="font-size:8px;opacity:.7;">{{ $g->stagiaires_count }}</span>
                    </span>
                    @endforeach
                </div>
                @endif
                @if($grps2->isNotEmpty())
                <div style="font-size:9px;font-weight:800;color:#94a3b8;text-transform:uppercase;letter-spacing:1px;margin-bottom:5px;">2ème année</div>
                <div style="display:flex;flex-wrap:wrap;">
                    @foreach($grps2 as $g)
                    <span class="grp-chip">
                        {{ $g->name }}
                        <span style="font-size:8px;opacity:.7;">{{ $g->stagiaires_count }}</span>
                    </span>
                    @endforeach
                </div>
                @endif
            </div>
            @else
            <div style="margin-top:14px;font-size:11px;color:#94a3b8;font-style:italic;">Aucun groupe</div>
            @endif
        </div>

        {{-- Footer --}}
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
     MODE B — FILIÈRE SELECTED: Detail + filters
══════════════════════════════════════════════ --}}
@else

{{-- Back + header --}}
<div style="display:flex;align-items:center;gap:12px;margin-bottom:20px;flex-wrap:wrap;">
    <a href="{{ route('stagiaire.index') }}"
       style="display:inline-flex;align-items:center;gap:6px;padding:8px 14px;border-radius:10px;border:1.5px solid #e2e8f0;background:white;color:#475569;font-size:12px;font-weight:600;text-decoration:none;transition:all .15s;"
       onmouseover="this.style.borderColor='{{ $accent }}';this.style.color='{{ $text }}'"
       onmouseout="this.style.borderColor='#e2e8f0';this.style.color='#475569'">
        <svg width="12" height="12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"/></svg>
        Toutes les filières
    </a>

    {{-- Filière pills navigation --}}
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

{{-- Title + stats --}}
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
    <div style="display:flex;gap:10px;flex-wrap:wrap;">
        <div style="padding:8px 14px;border-radius:12px;background:{{ $light }};border:1px solid {{ $border }};text-align:center;">
            <div style="font-size:20px;font-weight:800;color:{{ $accent }};">{{ $stagiaires->total() }}</div>
            <div style="font-size:9px;font-weight:700;color:{{ $text }};text-transform:uppercase;letter-spacing:.5px;">Résultats</div>
        </div>
        @php
            $groupesCount = $groupes->count();
            $groupesStat = $groupes->groupBy('annee');
        @endphp
        @foreach($groupesStat as $yr => $grpList)
        <div style="padding:8px 14px;border-radius:12px;background:#f8fafc;border:1px solid #e2e8f0;text-align:center;">
            <div style="font-size:20px;font-weight:800;color:#334155;">{{ $grpList->count() }}</div>
            <div style="font-size:9px;font-weight:700;color:#64748b;text-transform:uppercase;letter-spacing:.5px;">Grp An.{{ $yr }}</div>
        </div>
        @endforeach
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
           class="annee-pill {{ $anneeScolaire === '' ? 'active' : '' }}">
            Toutes
        </a>
        @foreach($anneesScolaires as $as)
        <a href="{{ route('stagiaire.index', array_merge(request()->except('annee_scolaire','page'), ['filiere_id' => $filiereId, 'annee_scolaire' => $as])) }}"
           class="annee-pill {{ $anneeScolaire === $as ? 'active' : '' }}">
            📅 {{ $as }}
        </a>
        @endforeach
    </div>
</div>
@endif

{{-- ── FILTER FORM ── --}}
<form method="GET" action="{{ route('stagiaire.index') }}"
      style="display:flex;gap:10px;flex-wrap:wrap;align-items:flex-end;margin-bottom:18px;
             padding:14px 16px;background:white;border-radius:14px;border:1px solid #e2e8f0;">
    <input type="hidden" name="filiere_id" value="{{ $filiereId }}">
    @if($anneeScolaire !== '')
        <input type="hidden" name="annee_scolaire" value="{{ $anneeScolaire }}">
    @endif

    {{-- Search --}}
    <div style="flex:2;min-width:180px;">
        <label style="display:block;font-size:9px;font-weight:800;color:#94a3b8;text-transform:uppercase;letter-spacing:1.5px;margin-bottom:5px;">Recherche</label>
        <div style="position:relative;">
            <svg width="13" height="13" fill="none" stroke="#94a3b8" viewBox="0 0 24 24"
                 style="position:absolute;left:11px;top:50%;transform:translateY(-50%);">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
            </svg>
            <input type="text" name="search" value="{{ $search }}"
                   placeholder="Nom, email ou CIN…"
                   class="sg-input" style="padding-left:32px;width:100%;">
        </div>
    </div>

    {{-- Groupe --}}
    <div style="flex:1;min-width:140px;">
        <label style="display:block;font-size:9px;font-weight:800;color:#94a3b8;text-transform:uppercase;letter-spacing:1.5px;margin-bottom:5px;">Groupe</label>
        <select name="groupe_id" class="sg-input" style="width:100%;appearance:none;cursor:pointer;">
            <option value="">Tous</option>
            @foreach($groupes as $g)
            <option value="{{ $g->id }}" {{ $groupeId == $g->id ? 'selected' : '' }}>
                {{ $g->name }} (An.{{ $g->annee }})
            </option>
            @endforeach
        </select>
    </div>

    {{-- Année d'étude --}}
    <div style="flex:1;min-width:130px;">
        <label style="display:block;font-size:9px;font-weight:800;color:#94a3b8;text-transform:uppercase;letter-spacing:1.5px;margin-bottom:5px;">Année d'étude</label>
        <select name="annee" class="sg-input" style="width:100%;appearance:none;cursor:pointer;">
            <option value="">Toutes</option>
            <option value="1" {{ $annee == 1 ? 'selected' : '' }}>1ère année</option>
            <option value="2" {{ $annee == 2 ? 'selected' : '' }}>2ème / 2.5 ans</option>
        </select>
    </div>

    {{-- Option --}}
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

    {{-- Buttons --}}
    <div style="display:flex;gap:8px;align-items:flex-end;">
        <button type="submit"
                style="height:40px;padding:0 16px;border-radius:10px;border:none;
                       background:{{ $accent }};color:white;font-size:13px;font-weight:600;
                       cursor:pointer;display:inline-flex;align-items:center;gap:6px;">
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
                                <div style="font-size:13px;font-weight:700;color:#0f172a;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">
                                    {{ $stagiaire->name }}
                                </div>
                                <div style="font-size:11px;color:#64748b;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">
                                    {{ $stagiaire->email }}
                                </div>
                                @if($stagiaire->cin)
                                <div style="font-size:10px;color:#94a3b8;">{{ $stagiaire->cin }}</div>
                                @endif
                            </div>
                        </div>
                    </td>

                    {{-- Groupe --}}
                    <td>
                        @if($stagiaire->groupe)
                            <span class="sg-badge" style="background:#f1f5f9;color:#334155;border:1px solid #e2e8f0;">
                                {{ $stagiaire->groupe->name ?? 'G'.$stagiaire->groupe->id }}
                            </span>
                        @else
                            <span style="font-size:11px;color:#94a3b8;font-style:italic;">Non assigné</span>
                        @endif
                    </td>

                    {{-- Année --}}
                    <td>
                        <span class="sg-badge"
                              style="{{ $stagiaire->groupe?->annee == 2
                                  ? 'background:#fff7ed;color:#9a3412;border:1px solid #fed7aa;'
                                  : 'background:#eff6ff;color:#1e40af;border:1px solid #bfdbfe;' }}">
                            {{ $anneeLabel }}
                        </span>
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
                            <span class="sg-badge" style="background:#fdf4ff;color:#6b21a8;border:1px solid #e9d5ff;font-size:10px;max-width:120px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;display:inline-block;">
                                {{ $stagiaire->option->titre }}
                            </span>
                        @else
                            <span style="font-size:11px;color:#94a3b8;">—</span>
                        @endif
                    </td>

                    {{-- Contact --}}
                    <td>
                        <div style="display:flex;gap:5px;">
                            @if($stagiaire->phone)
                                <span title="{{ $stagiaire->phone }}"
                                      style="font-size:10px;color:#64748b;background:#f8fafc;border:1px solid #e2e8f0;padding:3px 7px;border-radius:6px;cursor:default;">
                                    📞 {{ $stagiaire->phone }}
                                </span>
                            @endif
                            @if($stagiaire->date_naissance)
                                <span title="Né(e) le {{ $stagiaire->date_naissance->format('d/m/Y') }}"
                                      style="font-size:10px;color:#64748b;background:#f8fafc;border:1px solid #e2e8f0;padding:3px 7px;border-radius:6px;cursor:default;">
                                    🎂 {{ $stagiaire->date_naissance->format('d/m/Y') }}
                                </span>
                            @endif
                            @if(!$stagiaire->phone && !$stagiaire->date_naissance)
                                <span style="font-size:11px;color:#94a3b8;">—</span>
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

{{-- Pagination --}}
@if($stagiaires && $stagiaires->hasPages())
    <div style="margin-top:16px;display:flex;justify-content:center;">
        {{ $stagiaires->links() }}
    </div>
@endif

@endif {{-- end mode B --}}

</div>
@endsection