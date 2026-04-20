@extends('layouts.app')
@section('title', 'Groupes')
@section('page-title', 'Groupes')

@section('content')

@php
    $canCreate = Auth::user()->can('groupe-create');
    $canEdit   = Auth::user()->can('groupe-edit');
    $canDelete = Auth::user()->can('groupe-delete');
    $accent = Auth::user()->role === 'admin' ? '#0a6640' : '#1e293b';
    $light  = Auth::user()->role === 'admin' ? '#e8f5ee'  : '#f1f5f9';
    $text   = Auth::user()->role === 'admin' ? '#065f38'  : '#1e293b';
    $totalGroupes = $totalStagiaires = 0;
    foreach ($groupes as $grps) {
        $totalGroupes    += $grps->count();
        $totalStagiaires += $grps->sum('stagiaires_count');
    }
@endphp

<style>
:root {
    --accent: {{ $accent }}; --light: {{ $light }}; --atext: {{ $text }};
    --warning:#f59e0b; --danger:#dc2626; --danger-light:#fee2e2;
    --success-light:#f0fdf4; --success:#15803d;
    --gray-50:#f8fafc; --gray-100:#f1f5f9; --gray-200:#e2e8f0;
    --gray-400:#94a3b8; --gray-500:#64748b; --gray-800:#1e293b; --gray-900:#0f172a;
}
.grp-wrap { font-family:'Segoe UI',system-ui,sans-serif; }
.grp-card { background:white; border-radius:14px; border:1px solid var(--gray-200); overflow:hidden; transition:box-shadow .15s; display:flex; flex-direction:column; }
.grp-card:hover { box-shadow:0 4px 20px rgba(0,0,0,.07); }
.code-mono { font-family:'Courier New',monospace; font-size:9px; font-weight:800; letter-spacing:.5px;
             background:var(--gray-100); color:var(--gray-800); padding:2px 6px; border-radius:5px;
             border:1px solid var(--gray-200); }
.occ-wrap { height:5px; background:var(--gray-200); border-radius:99px; overflow:hidden; margin-top:5px; }
.occ-fill { height:100%; border-radius:99px; transition:width .4s; }
.chip { display:inline-flex; align-items:center; gap:5px; font-size:10px; font-weight:600; padding:4px 10px;
        border-radius:8px; background:var(--light); color:var(--atext); border:1px solid rgba(0,0,0,.06); }
.btn-g { display:inline-flex; align-items:center; gap:6px; padding:7px 13px; font-size:12px; font-weight:700;
         border-radius:10px; border:none; cursor:pointer; transition:opacity .15s; text-decoration:none; }
.btn-g:hover { opacity:.88; }
.grp-overlay { display:none; position:fixed; inset:0; z-index:1000; background:rgba(15,23,42,.5); backdrop-filter:blur(4px); align-items:center; justify-content:center; }
.grp-overlay.open { display:flex; animation:fIn .18s ease; }
@keyframes fIn { from{opacity:0}to{opacity:1} }
.grp-modal { background:white; border-radius:20px; width:100%; max-width:500px; margin:16px; box-shadow:0 24px 60px rgba(0,0,0,.18); max-height:90vh; overflow-y:auto; animation:sUp .2s ease; }
@keyframes sUp { from{opacity:0;transform:translateY(14px)}to{opacity:1;transform:none} }
.m-hd { padding:18px 24px; display:flex; align-items:center; justify-content:space-between; border-bottom:2px solid var(--accent); }
.m-hd.edit   { border-bottom-color:var(--warning); }
.m-hd.delete { border-bottom-color:var(--danger);  }
.m-hd h3 { font-size:15px; font-weight:800; color:var(--gray-900); margin:0; }
.m-hd p  { font-size:10px; color:var(--gray-500); margin:3px 0 0; }
.m-close { width:30px; height:30px; border-radius:8px; border:none; background:var(--gray-100); color:var(--gray-500); font-size:18px; cursor:pointer; display:flex; align-items:center; justify-content:center; }
.m-close:hover { background:var(--gray-200); }
.m-bd { padding:20px 24px; }
.m-ft { padding:14px 24px; border-top:1px solid var(--gray-100); display:flex; gap:10px; }
.f-label { display:block; font-size:9px; font-weight:800; color:var(--gray-400); letter-spacing:1.5px; text-transform:uppercase; margin-bottom:7px; }
.f-input { width:100%; height:42px; padding:0 12px; border-radius:10px; border:1.5px solid var(--gray-200); background:var(--gray-50); font-size:13px; color:var(--gray-800); outline:none; transition:all .15s; box-sizing:border-box; }
.f-input:focus { border-color:var(--accent); background:white; box-shadow:0 0 0 3px color-mix(in srgb,var(--accent) 8%,transparent); }
.f-input.warn:focus { border-color:var(--warning); box-shadow:0 0 0 3px rgba(245,158,11,.1); }
.f-hint { font-size:9px; color:var(--gray-400); margin-top:5px; line-height:1.5; }
.f-code-hint { margin-top:6px; padding:7px 10px; border-radius:8px; background:#fffbeb; border:1px solid #fde68a; font-size:9px; color:#92400e; line-height:1.6; }
.f-grid { display:grid; gap:12px; }
.f-row  { margin-bottom:14px; }
.flash { margin-bottom:16px; padding:12px 16px; border-radius:12px; font-size:13px; display:flex; align-items:center; gap:8px; }
.flash-ok  { background:var(--success-light); border:1px solid #bbf7d0; color:var(--success); }
.flash-err { background:var(--danger-light);  border:1px solid #fecdd3; color:#be123c; }
.radio-card { display:flex; align-items:center; gap:10px; cursor:pointer; padding:10px 14px; border-radius:10px; border:1.5px solid var(--gray-200); background:white; transition:all .15s; }

/* ── NEW: card footer link ── */
.grp-card-footer {
    padding:10px 16px;
    border-top:1px solid var(--gray-100);
    background:#fafbfc;
    display:flex;
    align-items:center;
    justify-content:space-between;
    margin-top:auto;
}
.grp-card-body { padding:14px 16px; flex:1; }
</style>

<div class="grp-wrap">

@if(session('success'))
    <div class="flash flash-ok">
        <svg width="16" height="16" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
        {{ session('success') }}
    </div>
@endif
@if(session('error'))
    <div class="flash flash-err">
        <svg width="16" height="16" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm-1-5h2v2h-2zm0-8h2v6h-2z" clip-rule="evenodd"/></svg>
        {{ session('error') }}
    </div>
@endif
@if($errors->any())
    <div class="flash flash-err"><ul style="margin:0;padding-left:16px;">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>
@endif

{{-- HEADER --}}
<div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:12px;margin-bottom:20px;">
    <div>
        <h1 style="font-size:22px;font-weight:800;color:var(--gray-900);margin:0;">Groupes</h1>
        <p style="font-size:12px;color:var(--gray-500);margin:4px 0 0;">
            {{ $totalGroupes }} groupe{{ $totalGroupes>1?'s':'' }} · {{ $totalStagiaires }} stagiaire{{ $totalStagiaires>1?'s':'' }} —
            <a href="{{ route('filieres.index') }}" style="color:var(--accent);font-weight:600;text-decoration:none;">← Filières</a>
        </p>
    </div>
    <div style="display:flex;gap:8px;align-items:center;flex-wrap:wrap;">
        <form method="GET" action="{{ route('groupes.index') }}" style="display:flex;align-items:center;gap:6px;">
            <select name="filiere" class="f-input" style="width:auto;font-size:12px;height:38px;cursor:pointer;" onchange="this.form.submit()">
                <option value="">Toutes les filières</option>
                @foreach($filieres as $f)
                    <option value="{{ $f->id }}" {{ request('filiere')==$f->id?'selected':'' }}>{{ $f->name }}</option>
                @endforeach
            </select>
            <select name="promo" onchange="this.form.submit()" class="f-input" style="width:auto;font-size:12px;height:38px;cursor:pointer;">
                <option value="">— Toutes les promos —</option>
                @foreach ($promos as $p)
                    <option value="{{ $p }}" {{ request('promo') == $p ? 'selected' : '' }}>Promo {{ $p }}</option>
                @endforeach
            </select>
        </form>
        @if($canCreate)
        <button onclick="@if($selectedFiliere) openCreateForFiliere({{ $selectedFiliere->id }}, '{{ addslashes($selectedFiliere->name) }}', '{{ addslashes($selectedFiliere->code ?? '') }}') @else openGrpModal('create') @endif"
                class="btn-g" style="background:var(--accent);color:white;box-shadow:0 4px 12px color-mix(in srgb,var(--accent) 30%,transparent);">
            <svg width="13" height="13" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
            Nouveau groupe
        </button>
        @endif
    </div>
</div>

@if($selectedFiliere)
<div style="margin-bottom:16px;padding:10px 16px;border-radius:12px;font-size:12px;display:flex;align-items:center;gap:8px;background:var(--light);border:1px solid color-mix(in srgb,var(--accent) 30%,transparent);color:var(--atext);">
    <strong>{{ $selectedFiliere->name }}</strong>
    @if($selectedFiliere->code)<span class="code-mono">{{ $selectedFiliere->code }}</span>@endif
    — {{ $groupes->flatten()->count() }} groupe(s)
    <a href="{{ route('groupes.index') }}" style="margin-left:auto;font-size:10px;color:var(--atext);font-weight:700;">Voir tout ×</a>
</div>
@endif

{{-- GROUPES PAR FILIÈRE --}}
@forelse($groupes as $filiereId => $grpList)
    @php $filiere = $grpList->first()->filiere; @endphp
    <div style="margin-bottom:28px;">
        <div style="display:flex;align-items:center;gap:10px;margin-bottom:12px;">
            <div style="width:4px;height:24px;border-radius:99px;background:var(--accent);"></div>
            <div>
                <span style="font-size:13px;font-weight:800;color:var(--gray-900);">{{ $filiere->name ?? 'Filière' }}</span>
                @if($filiere->code ?? null)
                    <span class="code-mono" style="margin-left:6px;">{{ $filiere->code }}</span>
                @endif
                <span style="font-size:10px;color:var(--gray-500);margin-left:8px;">{{ $grpList->count() }} groupe(s) · {{ $grpList->sum('stagiaires_count') }} stagiaires</span>
            </div>
            <div style="margin-left:auto;display:flex;gap:8px;align-items:center;">
                {{-- ✅ NEW: link to all stagiaires of this filiere --}}
                <a href="{{ route('stagiaire.index', ['filiere_id' => $filiereId]) }}"
                   style="font-size:10px;font-weight:700;color:var(--accent);background:var(--light);border:1px solid color-mix(in srgb,var(--accent) 30%,transparent);padding:4px 10px;border-radius:8px;text-decoration:none;display:inline-flex;align-items:center;gap:4px;">
                    <svg width="10" height="10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0"/></svg>
                    Tous les stagiaires
                </a>
                @if($canCreate)
                <button onclick="openCreateForFiliere({{ $filiereId }}, '{{ addslashes($filiere->name ?? '') }}', '{{ addslashes($filiere->code ?? '') }}')"
                        style="font-size:10px;font-weight:700;color:var(--accent);background:var(--light);border:1px solid color-mix(in srgb,var(--accent) 30%,transparent);padding:4px 10px;border-radius:8px;cursor:pointer;">
                    + Ajouter groupe
                </button>
                @endif
            </div>
        </div>

        <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(280px,1fr));gap:12px;">
            @foreach($grpList->sortBy('annee')->sortBy('name') as $groupe)
                @php
                    $occ        = $groupe->nbr_limit > 0 ? min(100, round(($groupe->stagiaires_count / $groupe->nbr_limit) * 100)) : 0;
                    $isFull     = $groupe->stagiaires_count >= $groupe->nbr_limit;
                    $occColor   = $isFull ? '#dc2626' : ($occ >= 70 ? '#f59e0b' : '#16a34a');
                    $anneeColor = $groupe->annee==1 ? '#1e40af' : ($groupe->annee==2 ? '#6b21a8' : '#c2410c');
                    $anneeBg    = $groupe->annee==1 ? '#eff6ff' : ($groupe->annee==2 ? '#fdf4ff' : '#fff7ed');
                    $anneeLabel = $groupe->annee==1 ? '1ère année' : ($groupe->annee==2 ? '2ème année' : '3ème année');
                @endphp
                <div class="grp-card" style="{{ $isFull ? 'border-color:#fca5a5;' : '' }}">
                    <div class="grp-card-body">
                        <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:8px;margin-bottom:10px;">
                            <div style="flex:1;">
                                <div style="display:flex;align-items:center;gap:6px;flex-wrap:wrap;">
                                    <span style="font-size:16px;font-weight:800;color:var(--gray-900);">{{ $groupe->name ?? 'G'.$groupe->id }}</span>
                                    @if($groupe->code)
                                        <span class="code-mono" title="Code import EDU">{{ $groupe->code }}</span>
                                    @else
                                        <span style="font-size:9px;color:var(--gray-400);font-style:italic;">sans code</span>
                                    @endif
                                </div>
                                <div style="display:flex;gap:6px;flex-wrap:wrap;margin-top:4px;">
                                    <div style="font-size:9px;font-weight:700;background:{{ $anneeBg }};color:{{ $anneeColor }};padding:2px 8px;border-radius:99px;display:inline-block;">
                                        {{ $anneeLabel }}
                                    </div>
                                    <div style="font-size:9px;font-weight:700;background:var(--light);color:var(--atext);padding:2px 8px;border-radius:99px;display:inline-block;">
                                        {{ $groupe->promo_label }}
                                    </div>
                                    {{-- ✅ COMPLET badge --}}
                                    @if($isFull)
                                    <div style="font-size:9px;font-weight:800;background:#fee2e2;color:#dc2626;padding:2px 8px;border-radius:99px;display:inline-block;border:1px solid #fca5a5;">
                                        ⛔ COMPLET
                                    </div>
                                    @endif
                                </div>
                            </div>
                            <div style="display:flex;gap:5px;">
                                @if($canEdit)
                                <button class="btn-g" style="padding:5px 8px;background:#fef3c7;color:#92400e;font-size:11px;"
                                        onclick="openEditGroupe({{ $groupe->id }},'{{ addslashes($groupe->name??'') }}','{{ addslashes($groupe->code??'') }}',{{ $groupe->annee }},{{ $groupe->nbr_limit }},{{ $groupe->id_filiere }},'{{ addslashes($filiere->name??'') }}',{{ $groupe->promo ?? date('Y') }})">✎</button>
                                @endif
                                @if($canDelete)
                                <button class="btn-g" style="padding:5px 8px;background:#fee2e2;color:#dc2626;font-size:11px;"
                                        onclick="openDeleteGroupe('{{ route('groupes.destroy',$groupe) }}','{{ addslashes($groupe->name??'') }}',{{ $groupe->stagiaires_count }})">✕</button>
                                @endif
                            </div>
                        </div>

                        <div style="display:flex;justify-content:space-between;align-items:center;font-size:10px;color:var(--gray-500);">
                            <span>{{ $groupe->stagiaires_count }} stagiaire{{ $groupe->stagiaires_count>1?'s':'' }}</span>
                            <span style="font-weight:700;color:{{ $occColor }};">{{ $occ }}%</span>
                            <span>/ {{ $groupe->nbr_limit }} places</span>
                        </div>
                        <div class="occ-wrap"><div class="occ-fill" style="width:{{ $occ }}%;background:{{ $occColor }};"></div></div>

                        <div style="display:flex;gap:6px;flex-wrap:wrap;margin-top:10px;">
                            <span class="chip">
                                <svg width="9" height="9" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                {{ $groupe->emploisDuTemps()->count() }} séances
                            </span>
                        </div>
                    </div>

                    {{-- ✅ NEW: card footer with link to group's stagiaires --}}
                    <div class="grp-card-footer">
                        <span style="font-size:10px;color:var(--gray-500);">
                            {{ $groupe->nbr_limit - $groupe->stagiaires_count }} place(s) libre(s)
                        </span>
                        <a href="{{ route('stagiaire.index', ['filiere_id' => $groupe->id_filiere, 'groupe_id' => $groupe->id]) }}"
                           style="font-size:11px;font-weight:700;color:var(--accent);text-decoration:none;display:inline-flex;align-items:center;gap:4px;">
                            Voir les stagiaires
                            <svg width="10" height="10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
                        </a>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
@empty
    <div style="padding:64px;text-align:center;background:white;border-radius:16px;border:1px solid var(--gray-200);">
        <div style="font-size:40px;margin-bottom:12px;">👥</div>
        <p style="font-size:14px;color:var(--gray-500);margin:0 0 16px;">
            @if($selectedFiliere) Aucun groupe dans cette filière.
            @else Aucun groupe créé. <a href="{{ route('filieres.index') }}" style="color:var(--accent);font-weight:600;">Créez d'abord une filière.</a>
            @endif
        </p>
        @if($canCreate && $filieres->isNotEmpty())
        <button onclick="openGrpModal('create')" class="btn-g" style="background:var(--accent);color:white;">Créer le premier groupe</button>
        @endif
    </div>
@endforelse

</div>

{{-- ═══ MODAL CRÉER ═══ --}}
@if($canCreate)
<div id="modal-create-grp" class="grp-overlay" onclick="if(event.target===this)closeGrpModal('create')">
    <div class="grp-modal">
        <div class="m-hd">
            <div><h3>Nouveau groupe</h3><p id="create-grp-sub">Créez un groupe de stagiaires</p></div>
            <button class="m-close" onclick="closeGrpModal('create')">×</button>
        </div>
        <form method="POST" action="{{ route('groupes.store') }}">
            @csrf
            <div class="m-bd">
                <div class="f-row">
                    <label class="f-label">Filière <span style="color:#ef4444;">*</span></label>
                    <select name="id_filiere" id="create-grp-filiere" class="f-input" required style="cursor:pointer;" onchange="onFiliereChange(this)">
                        <option value="">— Sélectionner —</option>
                        @foreach($filieres as $f)
                            <option value="{{ $f->id }}" data-code="{{ $f->code ?? '' }}">{{ $f->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="f-grid f-row" style="grid-template-columns:1fr 1fr;">
                    <div>
                        <label class="f-label">Nom du groupe <span style="color:#ef4444;">*</span></label>
                        <input type="text" name="name" id="create-grp-name" class="f-input" required
                               placeholder="G1A, G2B…" value="{{ old('name') }}" oninput="autoGroupeCode(this)">
                        <div class="f-hint">Court et unique dans la filière</div>
                    </div>
                    <div>
                        <label class="f-label">Code groupe <span style="color:#ef4444;">*</span></label>
                        <input type="text" name="code" id="create-grp-code" class="f-input" required
                               placeholder="DD-G1A" maxlength="30" value="{{ old('code') }}"
                               style="font-family:monospace;font-weight:700;letter-spacing:.5px;text-transform:uppercase;"
                               oninput="this.value=this.value.toUpperCase().replace(/[^A-Z0-9\-_]/g,'')">
                        <div class="f-code-hint">⚠️ Unique, immuable après import. Ex : <strong>DD-G1A</strong></div>
                    </div>
                </div>

                <div class="f-row">
                    <label class="f-label">Capacité maximale <span style="color:#ef4444;">*</span></label>
                    <input type="number" name="nbr_limit" class="f-input" required min="1" max="500" placeholder="25" value="{{ old('nbr_limit',25) }}">
                </div>

                <div class="f-row">
                    <label class="f-label">Année de formation <span style="color:#ef4444;">*</span></label>
                    <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:8px;">
                        <label class="radio-card" id="lbl-create-a1" onclick="styleRadio('create',1)">
                            <input type="radio" name="annee" value="1" {{ old('annee',1)==1?'checked':'' }} style="accent-color:var(--accent);">
                            <div><div style="font-size:12px;font-weight:700;color:var(--gray-900);">1ère année</div><div style="font-size:9px;color:var(--gray-500);">Onglet An. 1</div></div>
                        </label>
                        <label class="radio-card" id="lbl-create-a2" onclick="styleRadio('create',2)">
                            <input type="radio" name="annee" value="2" {{ old('annee')==2?'checked':'' }} style="accent-color:var(--accent);">
                            <div><div style="font-size:12px;font-weight:700;color:var(--gray-900);">2ème année</div><div style="font-size:9px;color:var(--gray-500);">Onglet An. 2</div></div>
                        </label>
                        <label class="radio-card" id="lbl-create-a3" onclick="styleRadio('create',3)">
                            <input type="radio" name="annee" value="3" {{ old('annee')==3?'checked':'' }} style="accent-color:var(--accent);">
                            <div><div style="font-size:12px;font-weight:700;color:var(--gray-900);">3ème année</div><div style="font-size:9px;color:var(--gray-500);">Onglet An. 3</div></div>
                        </label>
                    </div>
                </div>

                <div class="f-row">
                    <label class="f-label">Année de promotion (début)</label>
                    <input type="number" name="promo" value="{{ old('promo', date('Y')) }}" min="2000" max="2099" placeholder="ex: 2024" class="f-input" />
                    <small class="f-hint">Année de début (ex: 2024 → 2024–2026)</small>
                </div>
            </div>
            <div class="m-ft">
                <button type="button" onclick="closeGrpModal('create')" class="btn-g" style="flex:1;justify-content:center;background:white;border:1.5px solid var(--gray-200);color:var(--gray-500);">Annuler</button>
                <button type="submit" class="btn-g" style="flex:1;justify-content:center;background:var(--accent);color:white;box-shadow:0 4px 12px color-mix(in srgb,var(--accent) 35%,transparent);">
                    <svg width="13" height="13" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                    Créer
                </button>
            </div>
        </form>
    </div>
</div>
@endif

{{-- ═══ MODAL ÉDITER ═══ --}}
@if($canEdit)
<div id="modal-edit-grp" class="grp-overlay" onclick="if(event.target===this)closeGrpModal('edit')">
    <div class="grp-modal">
        <div class="m-hd edit">
            <div><h3>Modifier le groupe</h3><p id="edit-grp-sub"></p></div>
            <button class="m-close" onclick="closeGrpModal('edit')">×</button>
        </div>
        <form id="edit-grp-form" method="POST">
            @csrf @method('PATCH')
            <div class="m-bd">
                <div style="padding:10px 14px;border-radius:10px;background:#fef3c7;border:1px solid #fde68a;font-size:11px;color:#92400e;margin-bottom:14px;display:flex;align-items:center;gap:6px;">
                    <svg width="13" height="13" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span id="edit-grp-filiere-info">Filière :</span>
                </div>

                <div class="f-grid f-row" style="grid-template-columns:1fr 1fr;">
                    <div>
                        <label class="f-label">Nom du groupe <span style="color:#ef4444;">*</span></label>
                        <input type="text" name="name" id="edit-grp-name" class="f-input warn" required>
                    </div>
                    <div>
                        <label class="f-label">Code groupe <span style="color:#ef4444;">*</span></label>
                        <input type="text" name="code" id="edit-grp-code" class="f-input warn" required maxlength="30"
                               style="font-family:monospace;font-weight:700;letter-spacing:.5px;text-transform:uppercase;"
                               oninput="this.value=this.value.toUpperCase().replace(/[^A-Z0-9\-_]/g,'')">
                        <div class="f-code-hint">⚠️ Modifier le code casse la liaison avec les imports existants.</div>
                    </div>
                </div>

                <div class="f-row">
                    <label class="f-label">Capacité maximale <span style="color:#ef4444;">*</span></label>
                    <input type="number" name="nbr_limit" id="edit-grp-limit" class="f-input warn" required min="1" max="500">
                </div>

                <div class="f-row">
                    <label class="f-label">Année de formation <span style="color:#ef4444;">*</span></label>
                    <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:8px;">
                        <label class="radio-card" id="lbl-edit-a1" onclick="styleRadio('edit',1)">
                            <input type="radio" name="annee" value="1" id="edit-r1" style="accent-color:#f59e0b;">
                            <div><div style="font-size:12px;font-weight:700;color:var(--gray-900);">1ère année</div><div style="font-size:9px;color:var(--gray-500);">Onglet An. 1</div></div>
                        </label>
                        <label class="radio-card" id="lbl-edit-a2" onclick="styleRadio('edit',2)">
                            <input type="radio" name="annee" value="2" id="edit-r2" style="accent-color:#f59e0b;">
                            <div><div style="font-size:12px;font-weight:700;color:var(--gray-900);">2ème année</div><div style="font-size:9px;color:var(--gray-500);">Onglet An. 2</div></div>
                        </label>
                        <label class="radio-card" id="lbl-edit-a3" onclick="styleRadio('edit',3)">
                            <input type="radio" name="annee" value="3" id="edit-r3" style="accent-color:#f59e0b;">
                            <div><div style="font-size:12px;font-weight:700;color:var(--gray-900);">3ème année</div><div style="font-size:9px;color:var(--gray-500);">Onglet An. 3</div></div>
                        </label>
                    </div>
                </div>

                <div class="f-row">
                    <label class="f-label">Année de promotion (début)</label>
                    <input type="number" name="promo" id="edit-grp-promo" min="2000" max="2099" placeholder="ex: 2024" class="f-input warn" />
                    <small class="f-hint">Année de début (ex: 2024 → 2024–2026)</small>
                </div>
            </div>
            <div class="m-ft">
                <button type="button" onclick="closeGrpModal('edit')" class="btn-g" style="flex:1;justify-content:center;background:white;border:1.5px solid var(--gray-200);color:var(--gray-500);">Annuler</button>
                <button type="submit" class="btn-g" style="flex:1;justify-content:center;background:#f59e0b;color:white;box-shadow:0 4px 12px rgba(245,158,11,.3);">
                    <svg width="13" height="13" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                    Enregistrer
                </button>
            </div>
        </form>
    </div>
</div>
@endif

{{-- ═══ MODAL SUPPRIMER ═══ --}}
@if($canDelete)
<div id="modal-delete-grp" class="grp-overlay" onclick="if(event.target===this)closeGrpModal('delete')">
    <div class="grp-modal" style="max-width:400px;">
        <div class="m-hd delete">
            <div style="display:flex;align-items:center;gap:10px;">
                <div style="width:40px;height:40px;border-radius:12px;background:#fee2e2;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                    <svg width="18" height="18" fill="none" stroke="#dc2626" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                </div>
                <div><h3>Supprimer le groupe ?</h3><p id="delete-grp-name"></p></div>
            </div>
            <button class="m-close" onclick="closeGrpModal('delete')">×</button>
        </div>
        <div class="m-bd">
            <div id="delete-grp-warning" style="padding:12px 14px;border-radius:12px;background:#fff1f2;border:1px solid #fecdd3;color:#9f1239;font-size:12px;line-height:1.6;"></div>
        </div>
        <div class="m-ft">
            <button type="button" onclick="closeGrpModal('delete')" class="btn-g" style="flex:1;justify-content:center;background:white;border:1.5px solid var(--gray-200);color:var(--gray-500);">Annuler</button>
            <form id="delete-grp-form" method="POST" style="flex:1;">
                @csrf @method('DELETE')
                <button type="submit" id="delete-grp-btn" class="btn-g" style="width:100%;justify-content:center;background:#dc2626;color:white;box-shadow:0 4px 12px rgba(220,38,38,.3);">Supprimer</button>
            </form>
        </div>
    </div>
</div>
@endif

<script>
const ACCENT = '{{ $accent }}';
const LIGHT  = '{{ $light }}';

function openGrpModal(t)  { document.getElementById('modal-' + (t==='create'?'create-grp':t+'-grp')).classList.add('open');    }
function closeGrpModal(t) { document.getElementById('modal-' + (t==='create'?'create-grp':t+'-grp')).classList.remove('open'); }

function openCreateForFiliere(filiereId, filiereName, filiereCode) {
    const sel = document.getElementById('create-grp-filiere');
    if (sel) sel.value = filiereId;
    const sub = document.getElementById('create-grp-sub');
    if (sub) sub.textContent = 'Filière : ' + filiereName;
    const codeInput = document.getElementById('create-grp-code');
    if (codeInput && !codeInput.dataset.touched && filiereCode) {
        codeInput.value = filiereCode + '-';
    }
    document.getElementById('modal-create-grp').classList.add('open');
}

function onFiliereChange(sel) {
    const opt       = sel.options[sel.selectedIndex];
    const fCode     = opt.dataset.code || '';
    const nameInput = document.getElementById('create-grp-name');
    const codeInput = document.getElementById('create-grp-code');
    if (codeInput && !codeInput.dataset.touched && fCode) {
        const gName = nameInput?.value.toUpperCase().replace(/[^A-Z0-9]/g,'') || '';
        codeInput.value = fCode + (gName ? '-' + gName : '-');
    }
}

function autoGroupeCode(nameInput) {
    const codeInput = document.getElementById('create-grp-code');
    if (!codeInput || codeInput.dataset.touched) return;
    const sel   = document.getElementById('create-grp-filiere');
    const fCode = sel?.options[sel.selectedIndex]?.dataset.code || '';
    const gCode = nameInput.value.toUpperCase().replace(/[^A-Z0-9]/g,'').substring(0, 10);
    codeInput.value = (fCode ? fCode + '-' : '') + gCode;
}

function openEditGroupe(id, name, code, annee, limit, filiereId, filiereName, promo) {
    document.getElementById('edit-grp-form').action         = '/groupes/' + id;
    document.getElementById('edit-grp-name').value          = name;
    document.getElementById('edit-grp-code').value          = code;
    document.getElementById('edit-grp-limit').value         = limit;
    document.getElementById('edit-grp-promo').value         = promo || new Date().getFullYear();
    document.getElementById('edit-grp-sub').textContent     = name + ' — ' + filiereName;
    document.getElementById('edit-grp-filiere-info').textContent = 'Filière : ' + filiereName + ' (non modifiable)';
    document.getElementById('edit-r1').checked = annee == 1;
    document.getElementById('edit-r2').checked = annee == 2;
    document.getElementById('edit-r3').checked = annee == 3;
    styleRadio('edit', annee);
    document.getElementById('modal-edit-grp').classList.add('open');
}

function openDeleteGroupe(action, name, stagiaireCount) {
    document.getElementById('delete-grp-form').action      = action;
    document.getElementById('delete-grp-name').textContent = 'Groupe : ' + name;
    const btn = document.getElementById('delete-grp-btn');
    const w   = document.getElementById('delete-grp-warning');
    if (stagiaireCount > 0) {
        w.innerHTML = '⚠️ Impossible de supprimer : ce groupe contient <strong>' + stagiaireCount + ' stagiaire(s)</strong>. Désaffectez-les d\'abord.';
        btn.disabled=true; btn.style.opacity='.45'; btn.style.cursor='not-allowed';
    } else {
        w.textContent = 'Cette action est irréversible. L\'emploi du temps associé sera également supprimé.';
        btn.disabled=false; btn.style.opacity='1'; btn.style.cursor='pointer';
    }
    document.getElementById('modal-delete-grp').classList.add('open');
}

function styleRadio(prefix, selected) {
    [1, 2, 3].forEach(v => {
        const lbl = document.getElementById('lbl-' + prefix + '-a' + v);
        if (!lbl) return;
        const isSelected = v == selected;
        const color = prefix === 'edit' ? '#f59e0b' : ACCENT;
        const bg    = prefix === 'edit' ? '#fef3c7' : LIGHT;
        lbl.style.background  = isSelected ? bg    : 'white';
        lbl.style.borderColor = isSelected ? color : '#e2e8f0';
    });
}

document.addEventListener('DOMContentLoaded', () => {
    ['create-grp-code','edit-grp-code'].forEach(id => {
        const el = document.getElementById(id);
        if (el) el.addEventListener('input', () => { el.dataset.touched = '1'; });
    });
    styleRadio('create', {{ old('annee', 1) }});

    @if($selectedFiliere)
    const sel = document.getElementById('create-grp-filiere');
    if (sel) {
        sel.value = {{ $selectedFiliere->id }};
        const codeInput = document.getElementById('create-grp-code');
        if (codeInput && !codeInput.dataset.touched) {
            codeInput.value = '{{ addslashes($selectedFiliere->code ?? '') }}-';
        }
    }
    @endif
});

document.addEventListener('keydown', e => {
    if (e.key === 'Escape') document.querySelectorAll('.grp-overlay.open').forEach(m => m.classList.remove('open'));
});
</script>

@endsection