{{-- resources/views/reportations/index.blade.php --}}
@extends('layouts.app')
@section('title', 'Reportations')
@section('page-title', 'Reportations')

@section('content')
@php
    $user    = Auth::user();
    $isAdmin = $user->role === 'admin';
    $accent  = $isAdmin ? '#0a6640' : '#1e293b';
    $light   = $isAdmin ? '#e8f5ee' : '#f1f5f9';
    $text    = $isAdmin ? '#065f38' : '#1e293b';
@endphp

<style>
.rp-wrap { font-family:'Segoe UI',system-ui,sans-serif; }
.rp-card { background:white; border-radius:16px; border:1px solid #e2e8f0; box-shadow:0 2px 10px rgba(0,0,0,0.05); overflow:hidden; margin-bottom:14px; transition:box-shadow .2s; }
.rp-card:hover { box-shadow:0 4px 20px rgba(0,0,0,0.08); }
.rp-input { height:40px; padding:0 12px; border-radius:10px; border:1.5px solid #e2e8f0; background:#f8fafc; font-size:13px; color:#1e293b; outline:none; transition:all .15s; box-sizing:border-box; }
.rp-input:focus { border-color:{{ $accent }}; background:white; }
.status-pill { display:inline-flex; align-items:center; gap:4px; padding:4px 12px; border-radius:99px; font-size:10px; font-weight:700; }
.status-pill.attente { background:#fff7ed; color:#92400e; border:1px solid #fde68a; }
.status-pill.valide  { background:#f0fdf4; color:#15803d; border:1px solid #bbf7d0; }
.status-pill.refuse  { background:#fff1f2; color:#dc2626; border:1px solid #fecdd3; }
.rp-btn { height:36px; padding:0 14px; border-radius:9px; font-size:12px; font-weight:700; border:none; cursor:pointer; display:inline-flex; align-items:center; gap:5px; transition:opacity .15s; text-decoration:none; }
.rp-btn:hover { opacity:.85; }
.rp-btn.green  { background:#16a34a; color:white; }
.rp-btn.red    { background:#dc2626; color:white; }
.rp-btn.orange { background:#f59e0b; color:white; }
.rp-btn.ghost  { background:#f1f5f9; color:#475569; border:1px solid #e2e8f0; }
.tab-pill { padding:8px 16px; border-radius:99px; font-size:12px; font-weight:600; text-decoration:none; border:1.5px solid #e2e8f0; background:white; color:#64748b; transition:all .15s; display:inline-flex; align-items:center; gap:6px; }
.tab-pill:hover { border-color:{{ $accent }}; color:{{ $text }}; background:{{ $light }}; }
.tab-pill.active { background:{{ $accent }}; border-color:{{ $accent }}; color:white; }
.tab-pill .badge { font-size:9px; padding:1px 7px; border-radius:99px; font-weight:800; }
.tab-pill.active .badge { background:rgba(255,255,255,0.25); color:white; }
.tab-pill:not(.active) .badge { background:{{ $light }}; color:{{ $text }}; }
.rp-modal-overlay { position:fixed; inset:0; z-index:60; background:rgba(15,23,42,0.5); backdrop-filter:blur(4px); display:none; align-items:center; justify-content:center; }
.rp-modal-overlay.open { display:flex; }
.rp-modal-box { background:white; border-radius:20px; width:100%; max-width:460px; margin:16px; padding:24px; box-shadow:0 24px 60px rgba(0,0,0,0.18); }
.rp-modal-input { width:100%; height:42px; padding:0 12px; border-radius:10px; border:1.5px solid #e2e8f0; background:#f8fafc; font-size:13px; color:#1e293b; outline:none; transition:all .15s; box-sizing:border-box; }
.rp-modal-input:focus { border-color:#16a34a; background:white; }
.rp-label { display:block; font-size:9px; font-weight:800; color:#94a3b8; letter-spacing:1.5px; text-transform:uppercase; margin-bottom:6px; }
@keyframes slideIn { from { opacity:0; transform:translateY(-12px); } to { opacity:1; transform:translateY(0); } }
</style>

<div class="rp-wrap">

{{-- FLASH --}}
@if(session('success'))
    <div style="margin-bottom:16px;padding:12px 16px;border-radius:12px;font-size:13px;display:flex;align-items:center;gap:8px;background:#f0fdf4;border:1px solid #bbf7d0;color:#15803d;">
        ✓ {{ session('success') }}
    </div>
@endif
@if(session('error'))
    <div style="margin-bottom:16px;padding:12px 16px;border-radius:12px;font-size:13px;background:#fff1f2;border:1px solid #fecdd3;color:#dc2626;">
        ✕ {{ session('error') }}
    </div>
@endif
@if($errors->any())
    <div style="margin-bottom:16px;padding:12px 16px;border-radius:12px;font-size:13px;background:#fff1f2;border:1px solid #fecdd3;color:#dc2626;">
        @foreach($errors->all() as $e)<p style="margin:2px 0;">✕ {{ $e }}</p>@endforeach
    </div>
@endif

{{-- HEADER --}}
<div style="display:flex;align-items:flex-start;justify-content:space-between;flex-wrap:wrap;gap:12px;margin-bottom:20px;">
    <div>
        <h1 style="font-size:20px;font-weight:800;color:#0f172a;margin:0;">Reportations</h1>
        <p style="font-size:12px;color:#64748b;margin:4px 0 0;">Demandes de report reçues des formateurs — vous choisissez la nouvelle date</p>
    </div>
    <div style="display:flex;gap:10px;flex-wrap:wrap;">
        <div style="padding:10px 16px;border-radius:12px;text-align:center;background:#fff7ed;border:1px solid #fde68a;">
            <div data-count="en_attente" style="font-size:22px;font-weight:800;color:#92400e;">{{ $counts['en_attente'] }}</div>
            <div style="font-size:9px;font-weight:700;color:#92400e;text-transform:uppercase;letter-spacing:.5px;">En attente</div>
        </div>
        <div style="padding:10px 16px;border-radius:12px;text-align:center;background:#f0fdf4;border:1px solid #bbf7d0;">
            <div data-count="valide" style="font-size:22px;font-weight:800;color:#15803d;">{{ $counts['valide'] }}</div>
            <div style="font-size:9px;font-weight:700;color:#15803d;text-transform:uppercase;letter-spacing:.5px;">Acceptées</div>
        </div>
        <div style="padding:10px 16px;border-radius:12px;text-align:center;background:#fff1f2;border:1px solid #fecdd3;">
            <div data-count="refuse" style="font-size:22px;font-weight:800;color:#dc2626;">{{ $counts['refuse'] }}</div>
            <div style="font-size:9px;font-weight:700;color:#dc2626;text-transform:uppercase;letter-spacing:.5px;">Refusées</div>
        </div>
    </div>
</div>

{{-- STATUS TABS --}}
<div style="display:flex;gap:8px;flex-wrap:wrap;margin-bottom:18px;align-items:center;">
    @foreach([['en_attente','⏳','En attente'],['valide','✓','Acceptées'],['refuse','✕','Refusées'],['','📋','Toutes']] as [$val,$icon,$label])
    <a href="{{ route('reportations.index', array_merge(request()->except('status','page'), ['status'=>$val])) }}"
       class="tab-pill {{ $status === $val ? 'active' : '' }}">
        {{ $icon }} {{ $label }}
        <span class="badge" @if($val !== '') data-tab="{{ $val }}" @endif>
            {{ $val === '' ? array_sum($counts) : ($counts[$val] ?? 0) }}
        </span>
    </a>
    @endforeach

    <form method="GET" action="{{ route('reportations.index') }}" style="margin-left:auto;display:flex;gap:8px;">
        <input type="hidden" name="status" value="{{ $status }}">
        <input type="text" name="search" value="{{ $search }}" placeholder="Rechercher formateur…" class="rp-input" style="width:200px;">
        <button type="submit" style="height:40px;padding:0 14px;border-radius:10px;border:none;background:{{ $accent }};color:white;font-size:13px;font-weight:600;cursor:pointer;">🔍</button>
    </form>
</div>

{{-- CARDS --}}
@forelse($reportations as $rp)
@php $emploi = $rp->emploiDuTemps; @endphp

<div class="rp-card"
     data-rp-id="{{ $rp->id }}"
     data-rp-status="{{ $rp->status }}"
     data-module="{{ addslashes($rp->emploiDuTemps?->module?->name ?? 'Support') }}">
    {{-- Header --}}
    <div style="padding:14px 20px;border-bottom:1px solid #f1f5f9;display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:10px;">
        <div style="display:flex;align-items:center;gap:12px;">
            @php
                $name     = $rp->formateur?->name ?? 'Inconnu';
                $initials = strtoupper(substr($name,0,1)) . strtoupper(substr(explode(' ',$name.' ')[1]??'',0,1));
            @endphp
            <div style="width:38px;height:38px;border-radius:10px;background:{{ $light }};border:1px solid {{ $accent }}30;display:flex;align-items:center;justify-content:center;font-size:12px;font-weight:800;color:{{ $text }};flex-shrink:0;">
                {{ $initials }}
            </div>
            <div>
                <div style="font-size:13px;font-weight:700;color:#0f172a;">{{ $name }}</div>
                <div style="font-size:10px;color:#64748b;">{{ $rp->created_at->translatedFormat('l d M Y à H:i') }}</div>
            </div>
        </div>
        <div style="display:flex;align-items:center;gap:8px;flex-wrap:wrap;">
            @if($rp->status === 'en_attente')
                <span class="status-pill attente">⏳ En attente de décision</span>
            @elseif($rp->status === 'valide')
                <span class="status-pill valide">✓ Acceptée</span>
            @else
                <span class="status-pill refuse">✕ Refusée</span>
            @endif
            @if($rp->validePar)
                <span style="font-size:10px;color:#64748b;">par <strong>{{ $rp->validePar->name }}</strong></span>
            @endif
        </div>
    </div>

    {{-- Body --}}
    <div style="padding:16px 20px;display:grid;grid-template-columns:1fr 1fr;gap:16px;">

        {{-- Current session --}}
        <div>
            <div style="font-size:9px;font-weight:800;color:#94a3b8;text-transform:uppercase;letter-spacing:1.5px;margin-bottom:8px;">Séance concernée</div>
            @if($emploi)
            <div style="padding:12px 14px;border-radius:10px;background:#f8fafc;border:1px solid #e2e8f0;">
                <div style="font-size:12px;font-weight:700;color:#1e293b;margin-bottom:5px;">
                    {{ $emploi->module?->name ?? '— Module non défini' }}
                </div>
                <div style="font-size:10px;color:#475569;margin-bottom:3px;display:flex;align-items:center;gap:5px;">
                    👥 {{ $emploi->groupe?->name ?? '—' }} · {{ $emploi->groupe?->filiere?->name ?? '' }}
                </div>
                <div style="font-size:10px;color:#475569;margin-bottom:3px;display:flex;align-items:center;gap:5px;">
                    📅 {{ $emploi->date_debut->translatedFormat('l d M Y') }}
                </div>
                <div style="font-size:10px;color:#475569;display:flex;align-items:center;gap:5px;">
                    🕐 {{ $emploi->date_debut->format('H:i') }} → {{ $emploi->date_fin->format('H:i') }}
                    @if($emploi->salle)
                        · 🏛 {{ $emploi->salle->name }}
                    @elseif($emploi->mode === 'distance')
                        · 📹 À distance
                    @endif
                </div>
            </div>
            @else
                <div style="font-size:11px;color:#94a3b8;font-style:italic;">Séance supprimée</div>
            @endif
        </div>

        {{-- Reason + accepted date --}}
        <div>
            <div style="font-size:9px;font-weight:800;color:#94a3b8;text-transform:uppercase;letter-spacing:1.5px;margin-bottom:8px;">Raison du formateur</div>
            <div style="padding:12px 14px;border-radius:10px;background:#f8fafc;border:1px solid #e2e8f0;border-left:3px solid #7c3aed;font-size:11px;color:#334155;line-height:1.6;">
                {{ $rp->raison }}
            </div>

            @if($rp->status === 'valide' && $rp->nouvelle_date_debut)
            <div style="margin-top:10px;padding:10px 14px;border-radius:10px;background:#f0fdf4;border:1px solid #bbf7d0;">
                <div style="font-size:9px;font-weight:800;color:#15803d;text-transform:uppercase;letter-spacing:1px;margin-bottom:4px;">Déplacée au</div>
                <div style="font-size:12px;font-weight:700;color:#15803d;">
                    {{ Carbon\Carbon::parse($rp->nouvelle_date_debut)->translatedFormat('l d M Y') }}
                </div>
                <div style="font-size:10px;color:#15803d;">
                    {{ Carbon\Carbon::parse($rp->nouvelle_date_debut)->format('H:i') }}
                    → {{ Carbon\Carbon::parse($rp->nouvelle_date_fin)->format('H:i') }}
                </div>
            </div>
            @endif
        </div>
    </div>

    {{-- Assign + Chat --}}
    <div style="padding:12px 20px;border-top:1px solid #f1f5f9;background:#fafafa;">
        <div style="display:flex;align-items:center;gap:12px;flex-wrap:wrap;">
            {{-- Assign gestionnaire — only while pending --}}
            @if($rp->status === 'en_attente')
            <form method="POST" action="{{ route('reportations.assign', $rp) }}"
                  style="display:flex;flex-direction:column;gap:4px;"
                  onsubmit="
                    var sel = this.querySelector('select[name=assigned_to]');
                    var err = this.querySelector('.assign-error');
                    if (!sel.value) {
                        err.style.display = 'block';
                        sel.style.borderColor = '#dc2626';
                        sel.focus();
                        return false;
                    }
                    err.style.display = 'none';
                    sel.style.borderColor = '';
                  ">
                @csrf
                <div style="display:flex;gap:6px;align-items:center;">
                    @php $gestionnaires = \App\Models\User::role('gestionnaire')->get(); @endphp
                    <select name="assigned_to" class="rp-input" style="width:200px;"
                            onchange="this.style.borderColor='';this.closest('form').querySelector('.assign-error').style.display='none'">
                        <option value="">— Assigner un gestionnaire —</option>
                        @foreach($gestionnaires as $g)
                            <option value="{{ $g->id }}" {{ $rp->assigned_to == $g->id ? 'selected' : '' }}>{{ $g->name }}</option>
                        @endforeach
                    </select>
                    <button type="submit" class="rp-btn ghost">✔ Assigner</button>
                </div>
                <span class="assign-error"
                      style="display:none;font-size:10px;font-weight:600;color:#dc2626;padding-left:2px;">
                    ⚠ Veuillez choisir un gestionnaire avant d'assigner.
                </span>
            </form>
            @endif

            {{-- Chat button --}}
            <button class="rp-btn ghost"
                    onclick="openChat({{ $rp->id }}, '{{ addslashes($rp->formateur?->name ?? 'Conversation') }}', '{{ $rp->status }}')">
                💬 Chat
                <span id="chat-count-{{ $rp->id }}" style="background:#e2e8f0;border-radius:99px;padding:1px 7px;font-size:10px;">
                    {{ $rp->messages?->count() ?? 0 }}
                </span>
            </button>
        </div>
    </div>

    {{-- Actions — only for pending --}}
    @if($rp->status === 'en_attente' && $emploi)
    <div style="padding:14px 20px;border-top:1px solid #f1f5f9;display:flex;align-items:center;gap:10px;flex-wrap:wrap;background:#fafafa;">
        <span style="font-size:11px;color:#64748b;flex:1;min-width:160px;">
            Choisissez la nouvelle date ou refusez :
        </span>

        <button class="rp-btn green"
                onclick="openAcceptModal(
                    {{ $rp->id }},
                    '{{ addslashes($rp->formateur?->name ?? '') }}',
                    '{{ addslashes($emploi->module?->name ?? 'Module') }}',
                    '{{ $emploi->date_debut->format('Y-m-d') }}',
                    '{{ $emploi->date_debut->format('H:i') }}',
                    '{{ $emploi->date_fin->format('H:i') }}'
                )">
            <svg width="12" height="12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
            Accepter & Choisir la date
        </button>

<button class="rp-btn orange"
        onclick="openDeleteModal(
            {{ $rp->id }},
            '{{ addslashes($rp->formateur?->name ?? '') }}',
            '{{ addslashes($emploi->module?->name ?? 'Module') }}',
            '{{ $emploi->date_debut->translatedFormat('l d M Y') }}',
            '{{ $emploi->date_debut->format('H:i') }}',
            '{{ $emploi->date_fin->format('H:i') }}'
        )">
    <svg width="12" height="12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
    Supprimer la séance
</button>

<button class="rp-btn red"
        onclick="openRefuseModal(
            {{ $rp->id }},
            '{{ addslashes($rp->formateur?->name ?? '') }}',
            '{{ addslashes($emploi->module?->name ?? 'Module') }}'
        )">
    <svg width="12" height="12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
    Refuser
</button>

        <a href="{{ route('emplois.index', ['week' => $emploi->date_debut->toDateString(), 'year' => $emploi->groupe->annee ?? 1]) }}"
           class="rp-btn ghost">
            📅 Voir la semaine
        </a>
    </div>
    @endif
</div>
@empty
<div style="padding:64px;text-align:center;background:white;border-radius:16px;border:1px solid #e2e8f0;">
    <div style="font-size:36px;margin-bottom:12px;">📋</div>
    <p style="font-size:14px;font-weight:700;color:#334155;margin:0 0 4px;">Aucune demande</p>
    <p style="font-size:12px;color:#94a3b8;margin:0;">Les demandes des formateurs apparaîtront ici.</p>
</div>
@endforelse

@if($reportations->hasPages())
    <div style="margin-top:16px;display:flex;justify-content:center;">{{ $reportations->links() }}</div>
@endif

{{-- ════ MODAL ACCEPT ════ --}}
<div id="accept-modal" class="rp-modal-overlay" onclick="if(event.target===this)closeAcceptModal()">
    <div class="rp-modal-box">
        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:16px;padding-bottom:14px;border-bottom:2px solid #16a34a;">
            <div style="display:flex;align-items:center;gap:10px;">
                <div style="width:42px;height:42px;border-radius:12px;background:#f0fdf4;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                    <svg width="20" height="20" fill="none" stroke="#16a34a" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                </div>
                <div>
                    <div style="font-size:14px;font-weight:800;color:#1e293b;">Choisir la nouvelle date</div>
                    <div id="accept-session-label" style="font-size:10px;color:#64748b;margin-top:1px;"></div>
                </div>
            </div>
            <button onclick="closeAcceptModal()" style="width:28px;height:28px;border-radius:8px;border:none;background:#f1f5f9;color:#64748b;font-size:16px;cursor:pointer;display:flex;align-items:center;justify-content:center;">×</button>
        </div>
        <div id="accept-current-info" style="padding:10px 14px;border-radius:10px;background:#fff7ed;border:1px solid #fde68a;margin-bottom:16px;font-size:11px;color:#92400e;display:flex;align-items:center;gap:8px;">
            <svg width="13" height="13" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
            <span>Séance actuelle : <strong id="accept-current-date"></strong></span>
        </div>
        <form id="accept-form" method="POST" style="display:flex;flex-direction:column;gap:14px;">
            @csrf
            {{-- Hidden inputs sent to controller --}}
            <input type="hidden" name="nouvelle_date_debut" id="accept-debut">
            <input type="hidden" name="nouvelle_date_fin"   id="accept-fin">

            {{-- Date picker --}}
            <div>
                <label class="rp-label">Nouvelle date</label>
                <input type="date" id="accept-date" required class="rp-modal-input"
                       style="width:100%;box-sizing:border-box;">
            </div>

            {{-- Session slot selector --}}
            <div>
                <label class="rp-label">Séance</label>
                <div style="display:grid;grid-template-columns:repeat(4,1fr);gap:8px;margin-top:4px;">
                    @foreach([
                        ['S1','08:30','11:00'],
                        ['S2','11:00','13:30'],
                        ['S3','13:30','16:00'],
                        ['S4','16:00','18:30'],
                    ] as $i => [$label,$debut,$fin])
                    <button type="button"
                            class="accept-slot-btn"
                            data-index="{{ $i }}"
                            data-debut="{{ $debut }}"
                            data-fin="{{ $fin }}"
                            onclick="toggleSlot(this)"
                            style="padding:8px 4px;border-radius:10px;border:1.5px solid #e2e8f0;background:white;
                                   font-size:12px;font-weight:600;color:#475569;cursor:pointer;
                                   display:flex;flex-direction:column;align-items:center;gap:2px;
                                   transition:all .15s;">
                        <span style="font-size:13px;font-weight:800;">{{ $label }}</span>
                        <span style="font-size:10px;color:#94a3b8;">{{ $debut }}</span>
                        <span style="font-size:10px;color:#94a3b8;">{{ $fin }}</span>
                    </button>
                    @endforeach
                </div>
                <div id="accept-slot-error" style="color:#dc2626;font-size:11px;margin-top:5px;display:none;">
                    Veuillez sélectionner une séance.
                </div>
                <div id="accept-range-label" style="display:none;margin-top:6px;padding:7px 12px;border-radius:8px;background:#f0fdf4;border:1px solid #bbf7d0;font-size:11px;font-weight:700;color:#15803d;text-align:center;"></div>
            </div>
            <div style="padding:10px 14px;border-radius:10px;background:#f0fdf4;border:1px solid #bbf7d0;font-size:11px;color:#15803d;display:flex;align-items:flex-start;gap:8px;">
                <svg width="13" height="13" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="flex-shrink:0;margin-top:1px;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                Le système vérifiera automatiquement les conflits de groupe, formateur et salle sur ce créneau.
            </div>
            <div style="display:flex;gap:10px;margin-top:4px;">
                <button type="button" onclick="closeAcceptModal()"
                        style="flex:1;height:44px;border-radius:12px;border:1.5px solid #e2e8f0;background:white;font-size:13px;font-weight:600;color:#64748b;cursor:pointer;">
                    Annuler
                </button>
                <button type="submit"
                        style="flex:2;height:44px;border-radius:12px;border:none;background:#16a34a;font-size:13px;font-weight:700;color:white;cursor:pointer;display:flex;align-items:center;justify-content:center;gap:6px;">
                    <svg width="13" height="13" fill="none" stroke="white" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                    Confirmer le déplacement
                </button>
            </div>
        </form>
    </div>
</div>

{{-- ════ MODAL DELETE SESSION ════ --}}
<div id="delete-modal" class="rp-modal-overlay" onclick="if(event.target===this)closeDeleteModal()">
    <div class="rp-modal-box">
        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:16px;padding-bottom:14px;border-bottom:2px solid #f59e0b;">
            <div style="display:flex;align-items:center;gap:10px;">
                <div style="width:42px;height:42px;border-radius:12px;background:#fffbeb;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                    <svg width="20" height="20" fill="none" stroke="#f59e0b" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                </div>
                <div>
                    <div style="font-size:14px;font-weight:800;color:#1e293b;">Supprimer la séance</div>
                    <div id="delete-session-label" style="font-size:10px;color:#64748b;margin-top:1px;"></div>
                </div>
            </div>
            <button onclick="closeDeleteModal()" style="width:28px;height:28px;border-radius:8px;border:none;background:#f1f5f9;color:#64748b;font-size:16px;cursor:pointer;display:flex;align-items:center;justify-content:center;">×</button>
        </div>

        {{-- Session info --}}
        <div id="delete-current-info" style="padding:10px 14px;border-radius:10px;background:#fffbeb;border:1px solid #fde68a;margin-bottom:16px;font-size:11px;color:#92400e;display:flex;align-items:center;gap:8px;">
            <svg width="13" height="13" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
            <span>Séance : <strong id="delete-current-date"></strong></span>
        </div>

        {{-- Warning --}}
        <div style="padding:12px 14px;border-radius:10px;background:#fff1f2;border:1px solid #fecdd3;margin-bottom:20px;display:flex;align-items:flex-start;gap:10px;">
            <svg width="16" height="16" fill="none" stroke="#dc2626" viewBox="0 0 24 24" style="flex-shrink:0;margin-top:1px;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/></svg>
            <div>
                <div style="font-size:12px;font-weight:700;color:#dc2626;margin-bottom:2px;">Action irréversible</div>
                <div style="font-size:11px;color:#dc2626;line-height:1.5;">Cette séance sera définitivement supprimée de l'emploi du temps. Le formateur sera notifié.</div>
            </div>
        </div>

        <form id="delete-form" method="POST" style="display:flex;gap:10px;">
            @csrf
            <button type="button" onclick="closeDeleteModal()"
                    style="flex:1;height:44px;border-radius:12px;border:1.5px solid #e2e8f0;background:white;font-size:13px;font-weight:600;color:#64748b;cursor:pointer;">
                Annuler
            </button>
            <button type="submit"
                    style="flex:2;height:44px;border-radius:12px;border:none;background:#f59e0b;font-size:13px;font-weight:700;color:white;cursor:pointer;display:flex;align-items:center;justify-content:center;gap:6px;">
                <svg width="13" height="13" fill="none" stroke="white" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                Confirmer la suppression
            </button>
        </form>
    </div>
</div>

{{-- ════ MODAL REFUSE ════ --}}
<div id="refuse-modal" class="rp-modal-overlay" onclick="if(event.target===this)closeRefuseModal()">
    <div class="rp-modal-box">
        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:16px;padding-bottom:14px;border-bottom:2px solid #dc2626;">
            <div style="display:flex;align-items:center;gap:10px;">
                <div style="width:42px;height:42px;border-radius:12px;background:#fff1f2;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                    <svg width="20" height="20" fill="none" stroke="#dc2626" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
                </div>
                <div>
                    <div style="font-size:14px;font-weight:800;color:#1e293b;">Refuser la demande</div>
                    <div id="refuse-session-label" style="font-size:10px;color:#64748b;margin-top:1px;"></div>
                </div>
            </div>
            <button onclick="closeRefuseModal()" style="width:28px;height:28px;border-radius:8px;border:none;background:#f1f5f9;color:#64748b;font-size:16px;cursor:pointer;display:flex;align-items:center;justify-content:center;">×</button>
        </div>

        {{-- Warning --}}
        <div style="padding:12px 14px;border-radius:10px;background:#fff1f2;border:1px solid #fecdd3;margin-bottom:20px;display:flex;align-items:flex-start;gap:10px;">
            <svg width="16" height="16" fill="none" stroke="#dc2626" viewBox="0 0 24 24" style="flex-shrink:0;margin-top:1px;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/></svg>
            <div>
                <div style="font-size:12px;font-weight:700;color:#dc2626;margin-bottom:2px;">Confirmer le refus</div>
                <div style="font-size:11px;color:#dc2626;line-height:1.5;">La séance restera dans l'emploi du temps. Le formateur sera notifié du refus.</div>
            </div>
        </div>

        <form id="refuse-form" method="POST" style="display:flex;gap:10px;">
            @csrf
            <button type="button" onclick="closeRefuseModal()"
                    style="flex:1;height:44px;border-radius:12px;border:1.5px solid #e2e8f0;background:white;font-size:13px;font-weight:600;color:#64748b;cursor:pointer;">
                Annuler
            </button>
            <button type="submit"
                    style="flex:2;height:44px;border-radius:12px;border:none;background:#dc2626;font-size:13px;font-weight:700;color:white;cursor:pointer;display:flex;align-items:center;justify-content:center;gap:6px;">
                <svg width="13" height="13" fill="none" stroke="white" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
                Confirmer le refus
            </button>
        </form>
    </div>
</div>

{{-- ════ CHAT MODAL ════ --}}
<div id="chat-modal" style="position:fixed;inset:0;z-index:70;background:rgba(15,23,42,0.5);backdrop-filter:blur(4px);display:none;align-items:center;justify-content:center;" onclick="if(event.target===this)closeChat()">
    <div style="background:white;border-radius:20px;width:100%;max-width:480px;margin:16px;display:flex;flex-direction:column;height:520px;box-shadow:0 24px 60px rgba(0,0,0,0.18);">
        <div style="padding:16px 20px;border-bottom:1px solid #e2e8f0;display:flex;align-items:center;justify-content:space-between;">
            <div style="font-size:14px;font-weight:800;color:#1e293b;" id="chat-title">💬 Conversation</div>
            <button onclick="closeChat()" style="border:none;background:#f1f5f9;border-radius:8px;width:28px;height:28px;cursor:pointer;font-size:16px;">×</button>
        </div>

        <div id="chat-messages" style="flex:1;overflow-y:auto;padding:16px;display:flex;flex-direction:column;gap:10px;"></div>

        {{-- File preview bar --}}
        <div id="file-preview-bar" style="display:none;padding:8px 16px;border-top:1px solid #e2e8f0;background:#f8fafc;align-items:center;gap:8px;">
            <span id="file-preview-name" style="font-size:11px;color:#475569;font-weight:600;flex:1;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;"></span>
            <button onclick="clearAttachment()" style="border:none;background:#fecdd3;color:#dc2626;border-radius:6px;width:22px;height:22px;cursor:pointer;font-size:14px;line-height:1;">×</button>
        </div>

        <div id="chat-input-row" style="padding:12px 16px;border-top:1px solid #e2e8f0;display:flex;gap:8px;align-items:center;">
            {{-- Hidden file input --}}
            <input type="file" id="chat-file-input"
                   accept="image/*,.pdf,.doc,.docx,.xls,.xlsx,.txt,.zip"
                   style="display:none;" onchange="onFileSelected(this)">

            {{-- Attach button --}}
            <button onclick="document.getElementById('chat-file-input').click()"
                    title="Joindre un fichier"
                    style="width:40px;height:40px;border-radius:10px;border:1.5px solid #e2e8f0;background:#f8fafc;cursor:pointer;font-size:18px;display:flex;align-items:center;justify-content:center;flex-shrink:0;transition:border-color .15s;"
                    onmouseover="this.style.borderColor='{{ $accent }}'" onmouseout="this.style.borderColor='#e2e8f0'">
                📎
            </button>

            <input id="chat-input" type="text" placeholder="Votre message…" maxlength="1000"
                   style="flex:1;height:40px;padding:0 12px;border-radius:10px;border:1.5px solid #e2e8f0;font-size:13px;outline:none;"
                   onkeydown="if(event.key==='Enter')sendChatMsg()">

            <button onclick="sendChatMsg()"
                    style="height:40px;padding:0 16px;border-radius:10px;border:none;background:#16a34a;color:white;font-weight:700;font-size:13px;cursor:pointer;flex-shrink:0;">
                Envoyer
            </button>
        </div>

        {{-- Closed notice (shown when valide / refuse) --}}
        <div id="chat-closed-notice" style="display:none;padding:12px 16px;border-top:1px solid #e2e8f0;background:#f8fafc;text-align:center;font-size:12px;font-weight:600;color:#94a3b8;">
            🔒 Cette demande est clôturée — la messagerie est désactivée.
        </div>
    </div>
</div>

</div>

<script>
let currentReportationId = null;

// ══════════════════════════════════════════════
// CHAT FUNCTIONS (UPDATED)
// ══════════════════════════════════════════════

function openChat(id, name, status) {
    currentReportationId = id;
    document.getElementById('chat-title').textContent = '💬 ' + name;
    document.getElementById('chat-messages').innerHTML =
        '<div style="text-align:center;font-size:12px;color:#94a3b8;">Chargement…</div>';
    document.getElementById('chat-modal').style.display = 'flex';

    // Show / hide input area based on status
    const isClosed     = status && status !== 'en_attente';
    const inputRow     = document.getElementById('chat-input-row');
    const closedNotice = document.getElementById('chat-closed-notice');
    if (inputRow)      inputRow.style.display     = isClosed ? 'none' : 'flex';
    if (closedNotice)  closedNotice.style.display  = isClosed ? 'block' : 'none';
    if (isClosed) {
        document.getElementById('file-preview-bar').style.display = 'none';
    }

    fetch(`/reportations/${id}/messages`, {
        headers: { 'Accept': 'application/json' }
    })
    .then(r => r.json())
    .then(msgs => {
        const box = document.getElementById('chat-messages');
        box.innerHTML = msgs.length === 0
            ? '<div style="text-align:center;font-size:12px;color:#94a3b8;">Aucun message pour l\'instant.</div>'
            : '';
        msgs.forEach(appendMsg);
        box.scrollTop = box.scrollHeight;

        // Mark received messages as seen
        markAllSeen(id);
    });
}

function closeChat() {
    document.getElementById('chat-modal').style.display = 'none';
    currentReportationId = null;
    clearAttachment();
}

// ── MARK SEEN ─────────────────────────────────────────
function markAllSeen(rpId) {
    fetch(`/reportations/${rpId}/seen`, {
        method: 'POST',
        headers: {
            'Accept': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        }
    });
}

// ── APPEND MESSAGE (avec édition/suppression/statut vu) ──
function appendMsg(msg) {
    const me    = {{ auth()->id() }};
    const isMe  = msg.user_id == me;
    const unseen = !msg.seen_at;
    const box   = document.getElementById('chat-messages');
    const div   = document.createElement('div');
    div.id      = 'msg-' + msg.id;
    div.style.cssText = `display:flex;flex-direction:column;align-items:${isMe ? 'flex-end' : 'flex-start'};gap:2px;`;

    // Attachment HTML
    let attachmentHtml = '';
    if (msg.attachment_url) {
        if (msg.attachment_type === 'image') {
            attachmentHtml = `
                <a href="${msg.attachment_url}" target="_blank" style="display:block;max-width:200px;margin-top:4px;">
                    <img src="${msg.attachment_url}" alt="${escapeHtml(msg.attachment_name)}"
                         style="max-width:200px;max-height:160px;border-radius:8px;border:1px solid #e2e8f0;display:block;">
                </a>`;
        } else {
            attachmentHtml = `
                <a href="${msg.attachment_url}" target="_blank"
                   style="display:inline-flex;align-items:center;gap:6px;margin-top:4px;padding:7px 12px;border-radius:8px;background:${isMe ? 'rgba(255,255,255,0.15)' : '#e2e8f0'};color:${isMe ? 'white' : '#1e293b'};font-size:11px;font-weight:600;text-decoration:none;max-width:200px;overflow:hidden;">
                    📄 <span style="overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">${escapeHtml(msg.attachment_name)}</span>
                </a>`;
        }
    }

    const msgHtml = msg.message
        ? `<div id="msg-text-${msg.id}" style="max-width:75%;padding:8px 12px;border-radius:${isMe ? '12px 12px 2px 12px' : '12px 12px 12px 2px'};background:${isMe ? '#16a34a' : '#f1f5f9'};color:${isMe ? 'white' : '#1e293b'};font-size:12px;line-height:1.5;">${escapeHtml(msg.message)}</div>`
        : '';

    // Action buttons — 3-dot kebab menu (only for MY messages not yet seen)
    let actionsHtml = '';
    if (isMe && unseen) {
        const hasAttachment = !!msg.attachment_url;
        actionsHtml = `
            <div id="msg-actions-${msg.id}" style="position:relative;display:flex;justify-content:flex-start;margin-top:2px;">
                <button onclick="toggleMsgMenu(${msg.id})"
                    id="msg-menu-btn-${msg.id}"
                    title="Options"
                    style="width:26px;height:26px;border-radius:8px;border:1px solid #e2e8f0;background:#f8fafc;color:#64748b;cursor:pointer;font-size:15px;display:flex;align-items:center;justify-content:center;font-weight:900;line-height:1;letter-spacing:1px;transition:background .15s;"
                    onmouseover="this.style.background='#e2e8f0'" onmouseout="this.style.background='#f8fafc'">
                    ···
                </button>
                <div id="msg-menu-${msg.id}"
                    style="display:none;position:absolute;bottom:30px;left:0;background:white;border:1px solid #e2e8f0;border-radius:10px;box-shadow:0 6px 20px rgba(0,0,0,0.1);min-width:130px;z-index:99;overflow:hidden;">
                    ${!hasAttachment ? `
                    <button onclick="closeMsgMenu(${msg.id});startEdit(${msg.id})"
                        style="width:100%;padding:8px 14px;border:none;background:transparent;color:#334155;font-size:12px;font-weight:600;cursor:pointer;text-align:left;display:flex;align-items:center;gap:8px;"
                        onmouseover="this.style.background='#f1f5f9'" onmouseout="this.style.background='transparent'">
                        ✏️ Modifier
                    </button>` : ''}
                    <button onclick="closeMsgMenu(${msg.id});deleteMsg(${msg.id})"
                        style="width:100%;padding:8px 14px;border:none;background:transparent;color:#dc2626;font-size:12px;font-weight:600;cursor:pointer;text-align:left;display:flex;align-items:center;gap:8px;"
                        onmouseover="this.style.background='#fff1f2'" onmouseout="this.style.background='transparent'">
                        🗑 Supprimer
                    </button>
                </div>
            </div>`;
    }

    // Seen indicator for my messages
    const seenHtml = isMe
        ? `<div id="msg-seen-${msg.id}" style="font-size:9px;color:${msg.seen_at ? '#22c55e' : '#94a3b8'};">
               ${msg.seen_at ? '✓✓ Vu' : '✓ Envoyé'}
           </div>`
        : '';

    div.innerHTML = `
        <div style="font-size:9px;color:#94a3b8;">${escapeHtml(msg.user_name)} · ${escapeHtml(msg.created_at)}</div>
        ${msgHtml}
        ${attachmentHtml}
        ${actionsHtml}
        ${seenHtml}`;

    box.appendChild(div);
}

// ── DELETE ────────────────────────────────────────────
function deleteMsg(msgId) {
    if (!confirm('Supprimer ce message ?')) return;

    fetch(`/reportations/messages/${msgId}`, {
        method: 'DELETE',
        headers: {
            'Accept': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        }
    })
    .then(r => r.json())
    .then(data => {
        if (data.ok) {
            const el = document.getElementById('msg-' + msgId);
            if (el) el.remove();
        } else {
            alert(data.error || 'Erreur lors de la suppression.');
        }
    });
}

// ── 3-DOT MENU ────────────────────────────────────────
function toggleMsgMenu(msgId) {
    const menu = document.getElementById('msg-menu-' + msgId);
    const isOpen = menu.style.display === 'block';
    // Close all other open menus first
    document.querySelectorAll('[id^="msg-menu-"]').forEach(m => m.style.display = 'none');
    menu.style.display = isOpen ? 'none' : 'block';
}

function closeMsgMenu(msgId) {
    const menu = document.getElementById('msg-menu-' + msgId);
    if (menu) menu.style.display = 'none';
}

// Close menu when clicking outside
document.addEventListener('click', function(e) {
    if (!e.target.closest('[id^="msg-menu-btn-"]') && !e.target.closest('[id^="msg-menu-"]')) {
        document.querySelectorAll('[id^="msg-menu-"]').forEach(m => m.style.display = 'none');
    }
});

// ── EDIT ──────────────────────────────────────────────
function startEdit(msgId) {
    const textEl    = document.getElementById('msg-text-' + msgId);
    const actionsEl = document.getElementById('msg-actions-' + msgId);
    const current   = textEl.textContent;

    textEl.innerHTML = `
        <div style="display:flex;gap:6px;align-items:center;">
            <input id="edit-input-${msgId}" type="text" value="${escapeHtml(current)}" maxlength="1000"
                style="flex:1;padding:4px 8px;border-radius:6px;border:1.5px solid #1e293b;font-size:12px;outline:none;color:#1e293b;background:white;"
                onkeydown="if(event.key==='Enter')confirmEdit(${msgId});if(event.key==='Escape')cancelEdit(${msgId}, \`${escapeHtml(current)}\`)">
            <button onclick="confirmEdit(${msgId})"
                style="padding:3px 8px;border-radius:6px;border:none;background:#22c55e;color:white;font-size:11px;font-weight:700;cursor:pointer;">✓</button>
            <button onclick="cancelEdit(${msgId}, \`${escapeHtml(current)}\`)"
                style="padding:3px 8px;border-radius:6px;border:none;background:#e2e8f0;color:#475569;font-size:11px;font-weight:700;cursor:pointer;">✕</button>
        </div>`;

    actionsEl.style.display = 'none';
    document.getElementById('edit-input-' + msgId).focus();
}

function cancelEdit(msgId, original) {
    const textEl    = document.getElementById('msg-text-' + msgId);
    const actionsEl = document.getElementById('msg-actions-' + msgId);
    textEl.textContent = original;
    actionsEl.style.display = 'flex';
}

function confirmEdit(msgId) {
    const input   = document.getElementById('edit-input-' + msgId);
    const newText = input.value.trim();
    if (!newText) return;

    fetch(`/reportations/messages/${msgId}`, {
        method: 'PATCH',
        headers: {
            'Accept': 'application/json',
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({ message: newText })
    })
    .then(r => r.json())
    .then(data => {
        if (data.ok) {
            const textEl    = document.getElementById('msg-text-' + msgId);
            const actionsEl = document.getElementById('msg-actions-' + msgId);
            textEl.textContent = data.message;
            actionsEl.style.display = 'flex';
        } else {
            alert(data.error || 'Erreur lors de la modification.');
            cancelEdit(msgId, input.value);
        }
    });
}

// ── FILE ATTACHMENT ───────────────────────────────────
function onFileSelected(input) {
    const file = input.files[0];
    if (!file) return;
    document.getElementById('file-preview-name').textContent = '📎 ' + file.name;
    document.getElementById('file-preview-bar').style.display = 'flex';
}

function clearAttachment() {
    document.getElementById('chat-file-input').value = '';
    document.getElementById('file-preview-bar').style.display = 'none';
    document.getElementById('file-preview-name').textContent = '';
}

// ── SEND MESSAGE ──────────────────────────────────────
function sendChatMsg() {
    const input     = document.getElementById('chat-input');
    const fileInput = document.getElementById('chat-file-input');
    const msg       = input.value.trim();
    const file      = fileInput.files[0] ?? null;

    if (!msg && !file) return;
    if (!currentReportationId) return;

    const rpId = currentReportationId;
    input.value = '';

    const formData = new FormData();
    if (msg)  formData.append('message', msg);
    if (file) formData.append('attachment', file);
    formData.append('_token', '{{ csrf_token() }}');

    const socketId = window.Echo?.socketId() ?? null;
    const headers  = {
        'Accept': 'application/json',
    };
    if (socketId) headers['X-Socket-ID'] = socketId;

    clearAttachment();

    fetch(`/reportations/${rpId}/message`, {
        method: 'POST',
        headers,
        body: formData
    })
    .then(r => {
        if (!r.ok) {
            return r.json().then(err => {
                const msg = err?.message || err?.error
                    || Object.values(err?.errors ?? {})[0]?.[0]
                    || 'Erreur lors de l\'envoi.';
                showSendError(msg);
                throw new Error(msg);
            });
        }
        return r.json();
    })
    .then(data => {
        if (currentReportationId === rpId) {
            const box   = document.getElementById('chat-messages');
            const empty = box.querySelector('div[style*="text-align:center"]');
            if (empty) empty.remove();
            appendMsg(data);
            box.scrollTop = 99999;
        }
        const badge = document.getElementById('chat-count-' + rpId);
        if (badge) badge.textContent = parseInt(badge.textContent || '0') + 1;
    })
    .catch(err => console.error('Erreur envoi:', err));
}

function showSendError(message) {
    const box = document.getElementById('chat-messages');
    const div = document.createElement('div');
    div.style.cssText = 'text-align:center;padding:6px 12px;font-size:11px;color:#dc2626;background:#fff1f2;border-radius:8px;border:1px solid #fecdd3;';
    div.textContent = '⚠ ' + message;
    box.appendChild(div);
    box.scrollTop = 99999;
    setTimeout(() => div.remove(), 5000);
}

// ══════════════════════════════════════════════
// REAL-TIME: new reportations
// ══════════════════════════════════════════════
if (window.Echo) {
    window.Echo.channel('reportations')
        .listen('ReportationCreated', (e) => {
            injectNewCard(e);
            updateCountBadge('en_attente', +1);
        });
}

function updateCountBadge(status, delta) {
    const box = document.querySelector(`[data-count="${status}"]`);
    if (box) box.textContent = parseInt(box.textContent) + delta;
    const tab = document.querySelector(`[data-tab="${status}"]`);
    if (tab) tab.textContent = parseInt(tab.textContent) + delta;
}

function injectNewCard(e) {
    const currentStatus = '{{ $status }}';
    if (currentStatus !== 'en_attente' && currentStatus !== '') return;

    const initials = (e.formateur || 'IN').split(' ').slice(0,2).map(w => w[0]?.toUpperCase() || '').join('');
    const card = document.createElement('div');
    card.className = 'rp-card';
    card.style.animation = 'slideIn .3s ease';
    card.setAttribute('data-id', e.id);
    card.innerHTML = `
        <div style="padding:14px 20px;border-bottom:1px solid #f1f5f9;display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:10px;">
            <div style="display:flex;align-items:center;gap:12px;">
                <div style="width:38px;height:38px;border-radius:10px;background:{{ $light }};display:flex;align-items:center;justify-content:center;font-size:12px;font-weight:800;color:{{ $text }};">${escapeHtml(initials)}</div>
                <div>
                    <div style="font-size:13px;font-weight:700;color:#0f172a;">${escapeHtml(e.formateur)}</div>
                    <div style="font-size:10px;color:#64748b;">${escapeHtml(e.created_at)}</div>
                </div>
            </div>
            <div style="display:flex;align-items:center;gap:8px;">
                <span class="status-pill attente">⏳ En attente</span>
                <span style="font-size:10px;background:#dbeafe;color:#1e40af;padding:3px 10px;border-radius:99px;font-weight:700;">🔴 Nouveau</span>
            </div>
        </div>
        <div style="padding:16px 20px;display:grid;grid-template-columns:1fr 1fr;gap:16px;">
            <div>
                <div style="font-size:9px;font-weight:800;color:#94a3b8;text-transform:uppercase;letter-spacing:1.5px;margin-bottom:8px;">Séance concernée</div>
                <div style="padding:12px 14px;border-radius:10px;background:#f8fafc;border:1px solid #e2e8f0;">
                    <div style="font-size:12px;font-weight:700;color:#1e293b;margin-bottom:5px;">${escapeHtml(e.module)}</div>
                    <div style="font-size:10px;color:#475569;margin-bottom:3px;">👥 ${escapeHtml(e.groupe)} · ${escapeHtml(e.filiere)}</div>
                    <div style="font-size:10px;color:#475569;margin-bottom:3px;">📅 ${escapeHtml(e.date_debut)}</div>
                    <div style="font-size:10px;color:#475569;">🕐 ${escapeHtml(e.heure_debut)} → ${escapeHtml(e.heure_fin)}</div>
                </div>
            </div>
            <div>
                <div style="font-size:9px;font-weight:800;color:#94a3b8;text-transform:uppercase;letter-spacing:1.5px;margin-bottom:8px;">Raison</div>
                <div style="padding:12px 14px;border-radius:10px;background:#f8fafc;border:1px solid #e2e8f0;border-left:3px solid #7c3aed;font-size:11px;color:#334155;line-height:1.6;">${escapeHtml(e.raison)}</div>
            </div>
        </div>
        <div style="padding:10px 16px;border-top:1px solid #f1f5f9;background:#fffbeb;">
            <div style="font-size:11px;color:#92400e;font-weight:600;">⚡ Nouvelle demande — rechargez la page pour les actions complètes.</div>
        </div>`;

    const firstCard = document.querySelector('.rp-card');
    const wrap = document.querySelector('.rp-wrap');
    if (firstCard) {
        wrap.insertBefore(card, firstCard);
    } else {
        const empty = wrap.querySelector('div[style*="text-align:center"]');
        if (empty) empty.remove();
        wrap.appendChild(card);
    }
    showToast('📋 Nouvelle demande de ' + e.formateur);
}

// ══════════════════════════════════════════════
// ACCEPT MODAL — contiguous multi-slot selection
// ══════════════════════════════════════════════
const SLOTS = [
    { debut: '08:30', fin: '11:00' },
    { debut: '11:00', fin: '13:30' },
    { debut: '13:30', fin: '16:00' },
    { debut: '16:00', fin: '18:30' },
];
let slotRange = { start: null, end: null }; // indices

function renderSlots() {
    const btns = document.querySelectorAll('.accept-slot-btn');
    btns.forEach(btn => {
        const i = parseInt(btn.dataset.index);
        const selected = slotRange.start !== null && i >= slotRange.start && i <= slotRange.end;
        if (selected) {
            btn.style.background  = '#f0fdf4';
            btn.style.borderColor = '#16a34a';
            btn.style.color       = '#16a34a';
            btn.querySelectorAll('span').forEach(s => s.style.color = '#16a34a');
        } else {
            btn.style.background  = 'white';
            btn.style.borderColor = '#e2e8f0';
            btn.style.color       = '#475569';
            btn.querySelectorAll('span').forEach(s => s.style.color = '');
        }
    });
    // Update label showing total range
    const lbl = document.getElementById('accept-range-label');
    if (lbl) {
        if (slotRange.start !== null) {
            const d = SLOTS[slotRange.start].debut;
            const f = SLOTS[slotRange.end].fin;
            const n = slotRange.end - slotRange.start + 1;
            lbl.textContent = n === 1
                ? `Séance : ${d} → ${f}`
                : `${n} séances : ${d} → ${f}`;
            lbl.style.display = 'block';
        } else {
            lbl.style.display = 'none';
        }
    }
    updateHiddenDatetimes();
}

function toggleSlot(btn) {
    const i = parseInt(btn.dataset.index);
    document.getElementById('accept-slot-error').style.display = 'none';

    if (slotRange.start === null) {
        // Nothing selected → start new
        slotRange = { start: i, end: i };
    } else if (i === slotRange.start && i === slotRange.end) {
        // Clicked the only selected → deselect
        slotRange = { start: null, end: null };
    } else if (i === slotRange.start - 1) {
        // Extend left
        slotRange.start = i;
    } else if (i === slotRange.end + 1) {
        // Extend right
        slotRange.end = i;
    } else if (i === slotRange.start && slotRange.start < slotRange.end) {
        // Shrink from left
        slotRange.start++;
    } else if (i === slotRange.end && slotRange.end > slotRange.start) {
        // Shrink from right
        slotRange.end--;
    } else {
        // Non-adjacent → reset to single
        slotRange = { start: i, end: i };
    }
    renderSlots();
}

function updateHiddenDatetimes() {
    const dateVal = document.getElementById('accept-date').value;
    if (!dateVal || slotRange.start === null) return;
    document.getElementById('accept-debut').value = dateVal + 'T' + SLOTS[slotRange.start].debut;
    document.getElementById('accept-fin').value   = dateVal + 'T' + SLOTS[slotRange.end].fin;
}

function openAcceptModal(reportationId, formateurName, moduleName, currentDate, heureDebut, heureFin) {
    document.getElementById('accept-session-label').textContent = formateurName + ' — ' + moduleName;
    document.getElementById('accept-current-date').textContent  = currentDate + ' · ' + heureDebut + ' → ' + heureFin;

    // Pre-fill date = current date + 7 days
    const [y, m, d] = currentDate.split('-').map(Number);
    const base = new Date(y, m - 1, d + 7);
    const pad  = n => String(n).padStart(2, '0');
    document.getElementById('accept-date').value =
        `${base.getFullYear()}-${pad(base.getMonth()+1)}-${pad(base.getDate())}`;

    // Auto-select matching slot
    const matchIdx = SLOTS.findIndex(s => s.debut === heureDebut);
    slotRange = matchIdx >= 0
        ? { start: matchIdx, end: matchIdx }
        : { start: null, end: null };
    renderSlots();

    document.getElementById('accept-form').action = `/reportations/${reportationId}/accept`;
    document.getElementById('accept-modal').classList.add('open');
}

function closeAcceptModal() {
    document.getElementById('accept-modal').classList.remove('open');
}

// ══════════════════════════════════════════════
// DELETE SESSION MODAL
// ══════════════════════════════════════════════
function openDeleteModal(reportationId, formateurName, moduleName, currentDate, heureDebut, heureFin) {
    document.getElementById('delete-session-label').textContent = formateurName + ' — ' + moduleName;
    document.getElementById('delete-current-date').textContent  = currentDate + ' · ' + heureDebut + ' → ' + heureFin;
    document.getElementById('delete-form').action = `/reportations/${reportationId}/delete-session`;
    document.getElementById('delete-modal').classList.add('open');
}

function closeDeleteModal() {
    document.getElementById('delete-modal').classList.remove('open');
}

// ══════════════════════════════════════════════
// REFUSE MODAL
// ══════════════════════════════════════════════
function openRefuseModal(reportationId, formateurName, moduleName) {
    document.getElementById('refuse-session-label').textContent = formateurName + ' — ' + moduleName;
    document.getElementById('refuse-form').action = `/reportations/${reportationId}/refuse`;
    document.getElementById('refuse-modal').classList.add('open');
}

function closeRefuseModal() {
    document.getElementById('refuse-modal').classList.remove('open');
}

document.getElementById('accept-date').addEventListener('change', updateHiddenDatetimes);

// Validate on submit
document.getElementById('accept-form').addEventListener('submit', function(e) {
    if (slotRange.start === null) {
        e.preventDefault();
        document.getElementById('accept-slot-error').style.display = 'block';
        return;
    }
    updateHiddenDatetimes();
    if (!document.getElementById('accept-debut').value || !document.getElementById('accept-fin').value) {
        e.preventDefault();
    }
});

// ── HANDLE SEEN EVENT (real-time ✓✓) ─────────────────
function handleSeenEvent(e) {
    (e.message_ids || []).forEach(function(id) {
        // Mettre à jour l'indicateur ✓✓ en temps réel
        const seenEl = document.getElementById('msg-seen-' + id);
        if (seenEl) {
            seenEl.style.color = '#22c55e';
            seenEl.textContent = '✓✓ Vu';
        }
        // Supprimer les boutons modifier/supprimer (message vu = plus modifiable)
        const actionsEl = document.getElementById('msg-actions-' + id);
        if (actionsEl) actionsEl.remove();
    });
}

// ══════════════════════════════════════════════
// GLOBAL ECHO — wait for Echo to be ready then subscribe
// ══════════════════════════════════════════════
function subscribeAll() {
    if (!window.Echo) {
        setTimeout(subscribeAll, 300);
        return;
    }
    @foreach($reportations as $rp)
    window.Echo.channel('reportation.{{ $rp->id }}')
        .listen('.message.sent', function(e) {
            const rpId = {{ $rp->id }};
            if (currentReportationId === rpId) {
                const box = document.getElementById('chat-messages');
                const empty = box.querySelector('div[style*="text-align:center"]');
                if (empty) empty.remove();
                appendMsg(e);
                box.scrollTop = 99999;
                // Marquer comme vu immédiatement si le chat est ouvert
                markAllSeen(rpId);
            }
            const badge = document.getElementById('chat-count-' + rpId);
            if (badge) badge.textContent = parseInt(badge.textContent || '0') + 1;
        })
        .listen('.messages.seen', function(e) {
            const rpId = {{ $rp->id }};
            if (currentReportationId === rpId) {
                handleSeenEvent(e);
            }
        });
    @endforeach
}
subscribeAll();

// ══════════════════════════════════════════════
// UTILS
// ══════════════════════════════════════════════
function showToast(msg) {
    const t = document.createElement('div');
    t.style.cssText = 'position:fixed;bottom:24px;right:24px;z-index:99;padding:14px 20px;background:#1e293b;color:white;border-radius:14px;font-size:13px;font-weight:600;box-shadow:0 8px 30px rgba(0,0,0,0.2);transition:opacity .3s;';
    t.textContent = msg;
    document.body.appendChild(t);
    setTimeout(() => { t.style.opacity = '0'; setTimeout(() => t.remove(), 300); }, 4000);
}

function escapeHtml(text) {
    if (text === null || text === undefined) return '';
    const div = document.createElement('div');
    div.textContent = String(text);
    return div.innerHTML;
}

// ── AUTO-OPEN CHAT FROM NOTIFICATION LINK ─────────────────────
(function () {
    const params = new URLSearchParams(window.location.search);
    const openId = parseInt(params.get('open_chat'));
    if (!openId) return;

    function tryOpen() {
        const card = document.querySelector('[data-rp-id="' + openId + '"]');
        if (!card) return;
        const label  = card.getAttribute('data-module') || 'Conversation';
        const status = card.dataset.rpStatus || 'en_attente';
        openChat(openId, label, status);
        const clean = new URL(window.location);
        clean.searchParams.delete('open_chat');
        window.history.replaceState({}, '', clean);
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', () => setTimeout(tryOpen, 200));
    } else {
        setTimeout(tryOpen, 200);
    }
})();
</script>

@endsection