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
/* Accept modal */
.rp-modal-overlay { position:fixed; inset:0; z-index:60; background:rgba(15,23,42,0.5); backdrop-filter:blur(4px); display:none; align-items:center; justify-content:center; }
.rp-modal-overlay.open { display:flex; }
.rp-modal-box { background:white; border-radius:20px; width:100%; max-width:460px; margin:16px; padding:24px; box-shadow:0 24px 60px rgba(0,0,0,0.18); }
.rp-modal-input { width:100%; height:42px; padding:0 12px; border-radius:10px; border:1.5px solid #e2e8f0; background:#f8fafc; font-size:13px; color:#1e293b; outline:none; transition:all .15s; box-sizing:border-box; }
.rp-modal-input:focus { border-color:#16a34a; background:white; }
.rp-label { display:block; font-size:9px; font-weight:800; color:#94a3b8; letter-spacing:1.5px; text-transform:uppercase; margin-bottom:6px; }
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
            <div style="font-size:22px;font-weight:800;color:#92400e;">{{ $counts['en_attente'] }}</div>
            <div style="font-size:9px;font-weight:700;color:#92400e;text-transform:uppercase;letter-spacing:.5px;">En attente</div>
        </div>
        <div style="padding:10px 16px;border-radius:12px;text-align:center;background:#f0fdf4;border:1px solid #bbf7d0;">
            <div style="font-size:22px;font-weight:800;color:#15803d;">{{ $counts['valide'] }}</div>
            <div style="font-size:9px;font-weight:700;color:#15803d;text-transform:uppercase;letter-spacing:.5px;">Acceptées</div>
        </div>
        <div style="padding:10px 16px;border-radius:12px;text-align:center;background:#fff1f2;border:1px solid #fecdd3;">
            <div style="font-size:22px;font-weight:800;color:#dc2626;">{{ $counts['refuse'] }}</div>
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
        <span class="badge">{{ $val === '' ? array_sum($counts) : ($counts[$val] ?? 0) }}</span>
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

<div class="rp-card">
    {{-- Header --}}
    <div style="padding:14px 20px;border-bottom:1px solid #f1f5f9;display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:10px;">
        <div style="display:flex;align-items:center;gap:12px;">
            @php
                $name = $rp->formateur?->name ?? 'Inconnu';
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

        {{-- Reason + accepted date (if validated) --}}
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

    {{-- Actions — only for pending --}}
    @if($rp->status === 'en_attente' && $emploi)
    <div style="padding:14px 20px;border-top:1px solid #f1f5f9;display:flex;align-items:center;gap:10px;flex-wrap:wrap;background:#fafafa;">
        <span style="font-size:11px;color:#64748b;flex:1;min-width:160px;">
            Choisissez la nouvelle date ou refusez :
        </span>

        {{-- Accept — opens modal for admin to pick date --}}
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

        {{-- Delete session --}}
        <form method="POST" action="{{ route('reportations.delete-session', $rp) }}"
              onsubmit="return confirm('Supprimer définitivement la séance ?')">
            @csrf
            <button type="submit" class="rp-btn orange">
                <svg width="12" height="12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                Supprimer la séance
            </button>
        </form>

        {{-- Refuse --}}
        <form method="POST" action="{{ route('reportations.refuse', $rp) }}"
              onsubmit="return confirm('Refuser cette demande ?')">
            @csrf
            <button type="submit" class="rp-btn red">
                <svg width="12" height="12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
                Refuser
            </button>
        </form>

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
    <p style="font-size:14px;font-weight:700;color:#334155;margin:0 0 4px;">Aucune demande {{ $status === 'en_attente' ? 'en attente' : ($status === 'valide' ? 'acceptée' : ($status === 'refuse' ? 'refusée' : '')) }}</p>
    <p style="font-size:12px;color:#94a3b8;margin:0;">Les demandes des formateurs apparaîtront ici.</p>
</div>
@endforelse

@if($reportations->hasPages())
    <div style="margin-top:16px;display:flex;justify-content:center;">{{ $reportations->links() }}</div>
@endif

{{-- ════ MODAL ACCEPT — admin picks new date ════ --}}
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

        {{-- Current session reference --}}
        <div id="accept-current-info"
             style="padding:10px 14px;border-radius:10px;background:#fff7ed;border:1px solid #fde68a;margin-bottom:16px;font-size:11px;color:#92400e;display:flex;align-items:center;gap:8px;">
            <svg width="13" height="13" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
            <span>Séance actuelle : <strong id="accept-current-date"></strong></span>
        </div>

        <form id="accept-form" method="POST" style="display:flex;flex-direction:column;gap:14px;">
            @csrf
            <input type="hidden" name="_method" value="POST">

            <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
                <div>
                    <label class="rp-label">Nouvelle date et heure de début</label>
                    <input type="datetime-local" name="nouvelle_date_debut" id="accept-debut" required class="rp-modal-input">
                </div>
                <div>
                    <label class="rp-label">Heure de fin</label>
                    <input type="datetime-local" name="nouvelle_date_fin" id="accept-fin" required class="rp-modal-input">
                </div>
            </div>

            <div style="padding:10px 14px;border-radius:10px;background:#f0fdf4;border:1px solid #bbf7d0;font-size:11px;color:#15803d;display:flex;align-items:flex-start;gap:8px;">
                <svg width="13" height="13" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="flex-shrink:0;margin-top:1px;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                Le système vérifiera automatiquement les conflits de groupe, formateur et salle sur ce créneau.
            </div>

            <div style="display:flex;gap:10px;margin-top:4px;">
                <button type="button" onclick="closeAcceptModal()"
                        style="flex:1;height:44px;border-radius:12px;border:1.5px solid #e2e8f0;background:white;font-size:13px;font-weight:600;color:#64748b;cursor:pointer;"
                        onmouseover="this.style.background='#f8fafc'"
                        onmouseout="this.style.background='white'">
                    Annuler
                </button>
                <button type="submit"
                        style="flex:2;height:44px;border-radius:12px;border:none;background:#16a34a;font-size:13px;font-weight:700;color:white;cursor:pointer;box-shadow:0 4px 12px rgba(22,163,74,0.3);display:flex;align-items:center;justify-content:center;gap:6px;"
                        onmouseover="this.style.opacity='.9'"
                        onmouseout="this.style.opacity='1'">
                    <svg width="13" height="13" fill="none" stroke="white" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                    Confirmer le déplacement
                </button>
            </div>
        </form>
    </div>
</div>

</div>

<script>
function openAcceptModal(reportationId, formateurName, moduleName, currentDate, heureDebut, heureFin) {
    document.getElementById('accept-session-label').textContent = formateurName + ' — ' + moduleName;
    document.getElementById('accept-current-date').textContent  =
        currentDate + ' · ' + heureDebut + ' → ' + heureFin;

    // Pre-fill: same time next week
    const [y, m, d] = currentDate.split('-').map(Number);
    const base = new Date(y, m - 1, d + 7);
    const pad  = n => String(n).padStart(2, '0');
    const fmt  = (dt, h, mi) => `${dt.getFullYear()}-${pad(dt.getMonth()+1)}-${pad(dt.getDate())}T${pad(h)}:${pad(mi)}`;

    const [dh, dm] = heureDebut.split(':').map(Number);
    const [fh, fm] = heureFin.split(':').map(Number);

    document.getElementById('accept-debut').value = fmt(base, dh, dm);
    document.getElementById('accept-fin').value   = fmt(base, fh, fm);

    document.getElementById('accept-form').action = `/reportations/${reportationId}/accept`;

    document.getElementById('accept-modal').classList.add('open');
}

function closeAcceptModal() {
    document.getElementById('accept-modal').classList.remove('open');
}

// Auto-update fin when debut changes (keep same duration)
document.getElementById('accept-debut').addEventListener('change', function() {
    const debut = new Date(this.value);
    if (!debut || isNaN(debut)) return;
    const fin = document.getElementById('accept-fin');
    const oldFin = new Date(fin.value);
    if (!oldFin || isNaN(oldFin)) return;
    // Keep same duration
    const durMs = oldFin - new Date(this._prevValue || this.value);
    this._prevValue = this.value;
    if (durMs > 0) {
        const newFin = new Date(debut.getTime() + durMs);
        const pad = n => String(n).padStart(2,'0');
        fin.value = `${newFin.getFullYear()}-${pad(newFin.getMonth()+1)}-${pad(newFin.getDate())}T${pad(newFin.getHours())}:${pad(newFin.getMinutes())}`;
    }
});
</script>

@endsection