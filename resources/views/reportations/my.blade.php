{{-- resources/views/reportations/my.blade.php --}}
@extends('layouts.app')
@section('title', 'Mes reportations')
@section('page-title', 'Mes reportations')

@section('content')
<style>
.rp-wrap { font-family:'Segoe UI',system-ui,sans-serif; }
.rp-card { background:white; border-radius:16px; border:1px solid #e2e8f0; box-shadow:0 2px 10px rgba(0,0,0,0.05); overflow:hidden; margin-bottom:14px; }
.status-pill { display:inline-flex; align-items:center; gap:4px; padding:4px 12px; border-radius:99px; font-size:10px; font-weight:700; }
.status-pill.attente { background:#fff7ed; color:#92400e; border:1px solid #fde68a; }
.status-pill.valide  { background:#f0fdf4; color:#15803d; border:1px solid #bbf7d0; }
.status-pill.refuse  { background:#fff1f2; color:#dc2626; border:1px solid #fecdd3; }
.tab-pill { padding:7px 14px; border-radius:99px; font-size:12px; font-weight:600; text-decoration:none; border:1.5px solid #e2e8f0; background:white; color:#64748b; transition:all .15s; display:inline-flex; align-items:center; gap:6px; }
.tab-pill:hover { border-color:#1a4f8a; color:#1e40af; background:#eff6ff; }
.tab-pill.active { background:#1a4f8a; border-color:#1a4f8a; color:white; }
.tab-pill .badge { font-size:9px; padding:1px 7px; border-radius:99px; font-weight:800; }
.tab-pill.active .badge { background:rgba(255,255,255,0.25); color:white; }
.tab-pill:not(.active) .badge { background:#eff6ff; color:#1e40af; }
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

{{-- HEADER --}}
<div style="display:flex;align-items:flex-start;justify-content:space-between;flex-wrap:wrap;gap:12px;margin-bottom:20px;">
    <div>
        <h1 style="font-size:20px;font-weight:800;color:#0f172a;margin:0;">Mes reportations</h1>
        <p style="font-size:12px;color:#64748b;margin:4px 0 0;">Suivi de vos demandes de report de séances</p>
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
<div style="display:flex;gap:8px;flex-wrap:wrap;margin-bottom:18px;">
    @foreach([['','📋','Toutes'],['en_attente','⏳','En attente'],['valide','✓','Acceptées'],['refuse','✕','Refusées']] as [$val,$icon,$label])
    <a href="{{ route('reportations.my', ['status' => $val]) }}"
       class="tab-pill {{ $status === $val ? 'active' : '' }}">
        {{ $icon }} {{ $label }}
        <span class="badge">{{ $val === '' ? array_sum($counts) : ($counts[$val] ?? 0) }}</span>
    </a>
    @endforeach
</div>

{{-- CARDS --}}
@forelse($reportations as $rp)
@php $emploi = $rp->emploiDuTemps; @endphp

<div class="rp-card">
    {{-- Header --}}
    <div style="padding:14px 20px;border-bottom:1px solid #f1f5f9;display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:8px;">
        <div style="font-size:11px;color:#64748b;">
            Demande envoyée le <strong style="color:#334155;">{{ $rp->created_at->translatedFormat('d M Y à H:i') }}</strong>
        </div>
        <div style="display:flex;align-items:center;gap:8px;">
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

    <div style="padding:16px 20px;display:grid;grid-template-columns:1fr 1fr;gap:16px;">

        {{-- Session info --}}
        <div>
            <div style="font-size:9px;font-weight:800;color:#94a3b8;text-transform:uppercase;letter-spacing:1.5px;margin-bottom:8px;">Séance concernée</div>
            @if($emploi)
            <div style="padding:12px 14px;border-radius:10px;background:#f8fafc;border:1px solid #e2e8f0;">
                <div style="font-size:12px;font-weight:700;color:#1e293b;margin-bottom:5px;">{{ $emploi->module?->name ?? '— Module non défini' }}</div>
                <div style="font-size:10px;color:#475569;margin-bottom:3px;">👥 {{ $emploi->groupe?->name ?? '—' }} · {{ $emploi->groupe?->filiere?->name ?? '' }}</div>
                <div style="font-size:10px;color:#475569;margin-bottom:3px;">📅 {{ $emploi->date_debut->translatedFormat('l d M Y') }}</div>
                <div style="font-size:10px;color:#475569;">🕐 {{ $emploi->date_debut->format('H:i') }} → {{ $emploi->date_fin->format('H:i') }}</div>
            </div>
            @else
                <div style="font-size:11px;color:#94a3b8;font-style:italic;padding:12px 14px;background:#f8fafc;border-radius:10px;border:1px solid #e2e8f0;">Séance supprimée par l'administration.</div>
            @endif
        </div>

        {{-- Reason + result --}}
        <div>
            <div style="font-size:9px;font-weight:800;color:#94a3b8;text-transform:uppercase;letter-spacing:1.5px;margin-bottom:8px;">Votre raison</div>
            <div style="padding:12px 14px;border-radius:10px;background:#f8fafc;border:1px solid #e2e8f0;border-left:3px solid #7c3aed;font-size:11px;color:#334155;line-height:1.6;">
                {{ $rp->raison }}
            </div>

            @if($rp->status === 'valide' && $rp->nouvelle_date_debut)
            <div style="margin-top:10px;padding:12px 14px;border-radius:10px;background:#f0fdf4;border:1px solid #bbf7d0;">
                <div style="font-size:9px;font-weight:800;color:#15803d;text-transform:uppercase;letter-spacing:1px;margin-bottom:4px;">✓ Nouvelle date fixée par l'admin</div>
                <div style="font-size:13px;font-weight:700;color:#15803d;">
                    {{ \Carbon\Carbon::parse($rp->nouvelle_date_debut)->translatedFormat('l d M Y') }}
                </div>
                <div style="font-size:11px;color:#15803d;">
                    🕐 {{ \Carbon\Carbon::parse($rp->nouvelle_date_debut)->format('H:i') }}
                    → {{ \Carbon\Carbon::parse($rp->nouvelle_date_fin)->format('H:i') }}
                </div>
            </div>
            @elseif($rp->status === 'refuse')
            <div style="margin-top:10px;padding:12px 14px;border-radius:10px;background:#fff1f2;border:1px solid #fecdd3;font-size:11px;color:#dc2626;">
                ✕ Votre demande a été refusée. La séance reste à la date initiale.
            </div>
            @elseif($rp->status === 'en_attente')
            <div style="margin-top:10px;padding:12px 14px;border-radius:10px;background:#fff7ed;border:1px solid #fde68a;font-size:11px;color:#92400e;display:flex;align-items:center;gap:8px;">
                <svg width="13" height="13" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                En attente de décision — l'admin choisira la nouvelle date si accepté.
            </div>
            @endif
        </div>
    </div>

    @if($emploi)
    <div style="padding:10px 20px;border-top:1px solid #f1f5f9;background:#fafafa;">
        <a href="{{ route('emplois.index', ['week' => $emploi->date_debut->toDateString(), 'year' => $emploi->groupe->annee ?? 1]) }}"
           style="font-size:11px;color:#1e40af;text-decoration:none;font-weight:600;display:inline-flex;align-items:center;gap:5px;">
            <svg width="11" height="11" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
            Voir la semaine de cette séance
        </a>
    </div>
    @endif
</div>
@empty
<div style="padding:64px;text-align:center;background:white;border-radius:16px;border:1px solid #e2e8f0;">
    <div style="font-size:36px;margin-bottom:12px;">📋</div>
    <p style="font-size:14px;font-weight:700;color:#334155;margin:0 0 4px;">Aucune demande</p>
    <p style="font-size:12px;color:#94a3b8;margin:0;">Vos demandes de report apparaîtront ici. Utilisez le bouton <strong>📋 Reporter</strong> sur une séance.</p>
</div>
@endforelse

@if($reportations->hasPages())
    <div style="margin-top:16px;display:flex;justify-content:center;">{{ $reportations->links() }}</div>
@endif

</div>
@endsection