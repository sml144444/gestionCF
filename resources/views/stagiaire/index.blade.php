{{-- resources/views/stagiaires/index.blade.php --}}
@extends('layouts.app')
@section('title', 'Stagiaires')
@section('page-title', 'Stagiaires')

@section('content')
@php
    $user = Auth::user();
    $isAdmin = $user->role === 'admin';

    $accent  = $isAdmin ? '#0a6640' : '#1a4f8a';
    $light   = $isAdmin ? '#e8f5ee' : '#eff6ff';
    $text    = $isAdmin ? '#065f38' : '#1e40af';
    $border  = $isAdmin ? '#0a664030' : '#2563eb30';
@endphp

<style>
.sg-wrap { font-family: 'Segoe UI', system-ui, sans-serif; }

/* ── Filter bar ── */
.sg-filter-input {
    height: 40px; padding: 0 12px; border-radius: 10px;
    border: 1.5px solid #e2e8f0; background: #f8fafc;
    font-size: 13px; color: #1e293b; outline: none;
    transition: border-color 0.15s, background 0.15s;
    box-sizing: border-box;
}
.sg-filter-input:focus { border-color: {{ $accent }}; background: white; }

/* ── Stat card ── */
.sg-stat {
    padding: 10px 16px; border-radius: 12px; text-align: center;
    border: 1px solid transparent;
}

/* ── Table ── */
.sg-table-wrap {
    background: white; border-radius: 16px; border: 1px solid #e2e8f0;
    box-shadow: 0 2px 12px rgba(0,0,0,0.06); overflow: hidden;
}
.sg-th {
    font-size: 9px; font-weight: 800; color: #94a3b8;
    letter-spacing: 1.5px; text-transform: uppercase;
}
.sg-row {
    display: grid;
    grid-template-columns: 2fr 1fr 1fr 1fr 1fr 100px;
    padding: 12px 18px; border-bottom: 1px solid #f1f5f9;
    align-items: center; transition: background 0.12s;
}
.sg-row:hover { background: #fafbfe; }
.sg-badge {
    display: inline-block; font-size: 10px; font-weight: 700;
    padding: 3px 9px; border-radius: 8px;
}

/* ── Tag filters (filière pills) ── */
.sg-pill {
    display: inline-flex; align-items: center; gap: 5px;
    padding: 5px 12px; border-radius: 99px; font-size: 11px; font-weight: 600;
    border: 1.5px solid #e2e8f0; background: white; color: #64748b;
    cursor: pointer; text-decoration: none; transition: all 0.15s;
    white-space: nowrap;
}
.sg-pill:hover  { border-color: {{ $accent }}; color: {{ $text }}; background: {{ $light }}; }
.sg-pill.active { border-color: {{ $accent }}; color: white; background: {{ $accent }}; }

/* ── Avatar ── */
.sg-avatar {
    width: 36px; height: 36px; border-radius: 10px; flex-shrink: 0;
    display: flex; align-items: center; justify-content: center;
    font-size: 12px; font-weight: 800;
    background: {{ $light }}; color: {{ $text }};
    border: 1px solid {{ $border }};
}

/* ── Empty ── */
.sg-empty { padding: 64px; text-align: center; }
</style>

<div class="sg-wrap">

{{-- ════ FLASH ════ --}}
@if(session('success'))
    <div style="margin-bottom:16px; padding:12px 16px; border-radius:12px; font-size:13px;
                display:flex; align-items:center; gap:8px;
                background:#f0fdf4; border:1px solid #bbf7d0; color:#15803d;">
        <svg width="16" height="16" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
        </svg>
        {{ session('success') }}
    </div>
@endif

{{-- ════ HEADER ════ --}}
<div style="display:flex; align-items:flex-start; justify-content:space-between;
            flex-wrap:wrap; gap:12px; margin-bottom:20px;">
    <div>
        <h1 style="font-size:20px; font-weight:800; color:#0f172a; margin:0;">Stagiaires</h1>
        <p style="font-size:12px; color:#64748b; margin:4px 0 0;">
            Vue complète par filière, groupe et option
        </p>
    </div>

    {{-- Stats globaux --}}
    <div style="display:flex; gap:10px; flex-wrap:wrap;">
        <div class="sg-stat" style="background:{{ $light }}; border-color:{{ $border }};">
            <div style="font-size:22px; font-weight:800; color:{{ $accent }};">{{ $totalStagiaires }}</div>
            <div style="font-size:9px; font-weight:700; color:{{ $text }}; text-transform:uppercase; letter-spacing:0.5px;">Total</div>
        </div>
        @foreach($statsByFiliere as $fil)
            @if($fil->stagiaires_count > 0)
            <div class="sg-stat" style="background:#f8fafc; border-color:#e2e8f0;">
                <div style="font-size:22px; font-weight:800; color:#334155;">{{ $fil->stagiaires_count }}</div>
                <div style="font-size:9px; font-weight:700; color:#64748b; text-transform:uppercase; letter-spacing:0.5px; max-width:80px; overflow:hidden; text-overflow:ellipsis; white-space:nowrap;">
                    {{ $fil->name }}
                </div>
            </div>
            @endif
        @endforeach
    </div>
</div>

{{-- ════ FILIÈRE PILLS ════ --}}
<div style="display:flex; gap:8px; flex-wrap:wrap; margin-bottom:16px;">
    <a href="{{ route('stagiaire.index', array_merge(request()->except('filiere_id','groupe_id','option_id'), [])) }}"
       class="sg-pill {{ !$filiereId ? 'active' : '' }}">
        <svg width="11" height="11" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 6h16M4 12h16M4 18h16"/>
        </svg>
        Toutes les filières
        <span style="font-size:9px; padding:1px 6px; border-radius:99px;
                     {{ !$filiereId ? 'background:rgba(255,255,255,0.25); color:white;' : 'background:'.$light.'; color:'.$text.';' }}
                     font-weight:800;">
            {{ $totalStagiaires }}
        </span>
    </a>
    @foreach($statsByFiliere as $fil)
        @if($fil->stagiaires_count > 0)
        <a href="{{ route('stagiaire.index', array_merge(request()->except('filiere_id','groupe_id','option_id','page'), ['filiere_id' => $fil->id])) }}"
           class="sg-pill {{ $filiereId == $fil->id ? 'active' : '' }}">
            {{ $fil->name }}
            <span style="font-size:9px; padding:1px 6px; border-radius:99px; font-weight:800;
                         {{ $filiereId == $fil->id ? 'background:rgba(255,255,255,0.25); color:white;' : 'background:'.$light.'; color:'.$text.';' }}">
                {{ $fil->stagiaires_count }}
            </span>
        </a>
        @endif
    @endforeach
</div>

{{-- ════ FILTERS ════ --}}
<form method="GET" action="{{ route('stagiaire.index') }}"
      style="display:flex; gap:10px; margin-bottom:16px; flex-wrap:wrap;">

    @if($filiereId)
        <input type="hidden" name="filiere_id" value="{{ $filiereId }}">
    @endif

    {{-- Search --}}
    <input type="text" name="search" value="{{ $search }}"
           placeholder="Nom, email ou CIN..."
           class="sg-filter-input" style="flex:1; min-width:200px;">

    {{-- Groupe --}}
    <select name="groupe_id" class="sg-filter-input" style="min-width:140px;">
        <option value="">Tous les groupes</option>
        @foreach($groupes as $g)
            <option value="{{ $g->id }}" {{ $groupeId == $g->id ? 'selected' : '' }}>
                {{ $g->name ?? 'G'.$g->id }}
                ({{ $g->annee == 1 ? 'An.1' : 'An.2' }})
            </option>
        @endforeach
    </select>

    {{-- Option --}}
    @if($options->count())
    <select name="option_id" class="sg-filter-input" style="min-width:140px;">
        <option value="">Toutes les options</option>
        @foreach($options as $opt)
            <option value="{{ $opt->id }}" {{ $optionId == $opt->id ? 'selected' : '' }}>
                {{ $opt->titre }}
            </option>
        @endforeach
    </select>
    @endif

    {{-- Année --}}
    <select name="annee" class="sg-filter-input" style="min-width:120px;">
        <option value="">Toutes les années</option>
        <option value="1" {{ $annee == 1 ? 'selected' : '' }}>1ère année</option>
        <option value="2" {{ $annee == 2 ? 'selected' : '' }}>2ème / 2.5 ans</option>
    </select>

    <button type="submit"
            style="height:40px; padding:0 16px; border-radius:10px; border:none;
                   background:{{ $accent }}; color:white; font-size:13px;
                   font-weight:600; cursor:pointer; white-space:nowrap;">
        Filtrer
    </button>

    @if($search || $groupeId || $optionId || $annee)
    <a href="{{ route('stagiaire.index', $filiereId ? ['filiere_id' => $filiereId] : []) }}"
       style="height:40px; padding:0 14px; border-radius:10px; border:1.5px solid #e2e8f0;
              background:white; color:#64748b; font-size:13px; font-weight:600;
              text-decoration:none; display:flex; align-items:center; white-space:nowrap;">
        Réinitialiser
    </a>
    @endif
</form>

{{-- ════ RESULT COUNT ════ --}}
<div style="display:flex; align-items:center; justify-content:space-between;
            margin-bottom:10px; flex-wrap:wrap; gap:8px;">
    <div style="font-size:12px; color:#64748b;">
        <strong style="color:#334155;">{{ $stagiaires->total() }}</strong>
        stagiaire{{ $stagiaires->total() > 1 ? 's' : '' }} trouvé{{ $stagiaires->total() > 1 ? 's' : '' }}
        @if($filiereId)
            &nbsp;·&nbsp;
            <span style="color:{{ $accent }}; font-weight:600;">
                {{ $filieres->firstWhere('id', $filiereId)?->name ?? '' }}
            </span>
        @endif
        @if($groupeId)
            &nbsp;›&nbsp;
            <span style="font-weight:600; color:#334155;">
                {{ $groupes->firstWhere('id', $groupeId)?->name ?? 'Groupe' }}
            </span>
        @endif
    </div>
</div>

{{-- ════ TABLE ════ --}}
<div class="sg-table-wrap">

    {{-- Header --}}
    <div class="sg-row" style="background:#f8fafc; border-bottom:2px solid #e2e8f0; padding:10px 18px;">
        <span class="sg-th">Stagiaire</span>
        <span class="sg-th">Filière</span>
        <span class="sg-th">Groupe</span>
        <span class="sg-th">Option</span>
        <span class="sg-th">Année</span>
        <span class="sg-th">Infos</span>
    </div>

    {{-- Rows --}}
    @forelse($stagiaires as $stagiaire)
        @php
            $initials = strtoupper(substr($stagiaire->name, 0, 1))
                      . strtoupper(substr(explode(' ', $stagiaire->name)[1] ?? '', 0, 1));
            $anneeLabel = ($stagiaire->groupe?->annee == 2) ? '2ème' : '1ère';
        @endphp

        <div class="sg-row">
            {{-- Identity --}}
            <div style="display:flex; align-items:center; gap:10px; min-width:0;">
                <div class="sg-avatar">{{ $initials }}</div>
                <div style="min-width:0;">
                    <div style="font-size:13px; font-weight:700; color:#0f172a;
                                overflow:hidden; text-overflow:ellipsis; white-space:nowrap;">
                        {{ $stagiaire->name }}
                    </div>
                    <div style="font-size:11px; color:#64748b; margin-top:1px;
                                overflow:hidden; text-overflow:ellipsis; white-space:nowrap;">
                        {{ $stagiaire->email }}
                    </div>
                    @if($stagiaire->cin)
                    <div style="font-size:10px; color:#94a3b8;">CIN: {{ $stagiaire->cin }}</div>
                    @endif
                </div>
            </div>

            {{-- Filière --}}
            <div>
                @if($stagiaire->filiere)
                    <span class="sg-badge"
                          style="background:{{ $light }}; color:{{ $text }}; border:1px solid {{ $border }};">
                        {{ $stagiaire->filiere->name }}
                    </span>
                @else
                    <span style="font-size:11px; color:#94a3b8; font-style:italic;">—</span>
                @endif
            </div>

            {{-- Groupe --}}
            <div>
                @if($stagiaire->groupe)
                    <span class="sg-badge"
                          style="background:#f1f5f9; color:#334155; border:1px solid #e2e8f0;">
                        {{ $stagiaire->groupe->name ?? 'G'.$stagiaire->groupe->id }}
                    </span>
                @else
                    <span style="font-size:11px; color:#94a3b8; font-style:italic;">Non assigné</span>
                @endif
            </div>

            {{-- Option --}}
            <div>
                @if($stagiaire->option)
                    <span class="sg-badge"
                          style="background:#fdf4ff; color:#6b21a8; border:1px solid #e9d5ff;
                                 font-size:10px; max-width:110px; overflow:hidden;
                                 text-overflow:ellipsis; white-space:nowrap; display:inline-block;">
                        {{ $stagiaire->option->titre }}
                    </span>
                @else
                    <span style="font-size:11px; color:#94a3b8; font-style:italic;">—</span>
                @endif
            </div>

            {{-- Année --}}
            <div>
                <span class="sg-badge"
                      style="{{ $stagiaire->groupe?->annee == 2
                          ? 'background:#fff7ed; color:#9a3412; border:1px solid #fed7aa;'
                          : 'background:#eff6ff; color:#1e40af; border:1px solid #bfdbfe;' }}">
                    {{ $anneeLabel }}
                </span>
            </div>

            {{-- Infos / actions --}}
            <div style="display:flex; gap:5px; flex-wrap:wrap;">
                @if($stagiaire->phone)
                    <span title="{{ $stagiaire->phone }}"
                          style="font-size:10px; color:#64748b; background:#f8fafc;
                                 border:1px solid #e2e8f0; padding:3px 7px; border-radius:6px;">
                        📞
                    </span>
                @endif
                @if($stagiaire->date_naissance)
                    <span title="Né(e) le {{ $stagiaire->date_naissance->format('d/m/Y') }}"
                          style="font-size:10px; color:#64748b; background:#f8fafc;
                                 border:1px solid #e2e8f0; padding:3px 7px; border-radius:6px;">
                        🎂
                    </span>
                @endif
            </div>
        </div>
    @empty
        <div class="sg-empty">
            <div style="font-size:32px; margin-bottom:12px;">👥</div>
            <p style="font-size:14px; font-weight:700; color:#334155; margin:0 0 4px;">
                Aucun stagiaire trouvé
            </p>
            <p style="font-size:12px; color:#94a3b8; margin:0;">
                Essayez de modifier les filtres ou sélectionnez une autre filière.
            </p>
        </div>
    @endforelse
</div>

{{-- ════ PAGINATION ════ --}}
@if($stagiaires->hasPages())
    <div style="margin-top:16px; display:flex; justify-content:center;">
        {{ $stagiaires->links() }}
    </div>
@endif

</div>
@endsection