{{-- resources/views/reclamations/index.blade.php --}}
{{-- Vue Admin / Gestionnaire : toutes les réclamations --}}
@extends('layouts.app')
@section('title', 'Réclamations')
@section('page-title', 'Gestion des réclamations')

@section('content')
@php
    $user = Auth::user();
    $role = $user->role;
    $palettes = [
        'admin'        => ['primary'=>'#0a6640','medium'=>'#1a8c56','light'=>'#e8f5ee','lighter'=>'#f0fdf4','text'=>'#065f38','border'=>'#bbf7d0','shadow'=>'rgba(10,102,64,0.15)','gradient'=>'linear-gradient(135deg,#0a6640 0%,#1a8c56 100%)'],
        'gestionnaire' => ['primary'=>'#1e293b','medium'=>'#334155','light'=>'#f1f5f9','lighter'=>'#f8fafc','text'=>'#1e293b','border'=>'#cbd5e1','shadow'=>'rgba(30,41,59,0.15)','gradient'=>'linear-gradient(135deg,#1e293b 0%,#334155 100%)'],
    ];
    $p = $palettes[$role] ?? $palettes['gestionnaire'];
@endphp

<style>
:root {
    --accent:    {{ $p['primary'] }};
    --accent-md: {{ $p['medium'] }};
    --accent-lt: {{ $p['light'] }};
    --accent-ltr:{{ $p['lighter'] }};
    --accent-tx: {{ $p['text'] }};
    --accent-bd: {{ $p['border'] }};
    --accent-sh: {{ $p['shadow'] }};
    --accent-gr: {{ $p['gradient'] }};
}
.rc-wrap { font-family:'Segoe UI',system-ui,sans-serif; max-width:1200px; margin:0 auto; }
.rc-hero { background:var(--accent-gr); border-radius:20px; padding:28px 32px; margin-bottom:24px; display:flex; align-items:center; justify-content:space-between; gap:16px; flex-wrap:wrap; position:relative; overflow:hidden; }
.rc-hero::after { content:''; position:absolute; right:-40px; top:-40px; width:200px; height:200px; border-radius:50%; background:rgba(255,255,255,0.06); pointer-events:none; }
.rc-hero-icon { width:52px; height:52px; border-radius:16px; background:rgba(255,255,255,0.15); display:flex; align-items:center; justify-content:center; flex-shrink:0; }
.rc-hero-title { font-size:20px; font-weight:800; color:white; margin:0; }
.rc-hero-sub { font-size:12px; color:rgba(255,255,255,0.75); margin-top:3px; }
.rc-stats { display:flex; gap:12px; flex-wrap:wrap; }
.rc-stat { background:rgba(255,255,255,0.15); border:1px solid rgba(255,255,255,0.2); border-radius:12px; padding:8px 16px; text-align:center; }
.rc-stat-val { font-size:20px; font-weight:800; color:white; }
.rc-stat-lbl { font-size:10px; color:rgba(255,255,255,0.75); }
.flash-ok { display:flex; align-items:center; gap:12px; padding:14px 18px; border-radius:14px; margin-bottom:18px; background:var(--accent-ltr); border:1px solid var(--accent-bd); animation:fadeIn .3s ease; }
.flash-ok-icon { width:38px; height:38px; border-radius:50%; background:var(--accent-gr); display:flex; align-items:center; justify-content:center; flex-shrink:0; }
@keyframes fadeIn { from{opacity:0;transform:translateY(-6px)} to{opacity:1;transform:translateY(0)} }
.rc-filters { background:white; border-radius:16px; border:1px solid #e2e8f0; padding:16px 20px; margin-bottom:20px; display:flex; gap:12px; flex-wrap:wrap; align-items:center; }
.rc-filter-select { border:1.5px solid #e2e8f0; border-radius:10px; padding:8px 12px; font-size:12px; color:#1e293b; background:white; cursor:pointer; outline:none; }
.rc-filter-select:focus { border-color:var(--accent-bd); }
.rc-table-wrap { background:white; border-radius:18px; border:1px solid #e2e8f0; overflow:hidden; }
.rc-table { width:100%; border-collapse:collapse; }
.rc-table th { padding:12px 16px; background:#f8fafc; font-size:10px; font-weight:700; text-transform:uppercase; letter-spacing:.5px; color:#64748b; border-bottom:1px solid #e2e8f0; text-align:left; }
.rc-table td { padding:14px 16px; border-bottom:1px solid #f1f5f9; font-size:13px; color:#1e293b; vertical-align:middle; }
.rc-table tr:last-child td { border-bottom:none; }
.rc-table tr:hover td { background:#f8fafc; }
.badge { font-size:9px; font-weight:700; padding:4px 10px; border-radius:8px; white-space:nowrap; }
.badge-attente { background:#fef3c7; color:#92400e; border:1px solid #fde68a; }
.badge-traitee { background:#dcfce7; color:#166534; border:1px solid #bbf7d0; }
.type-badge { font-size:9px; font-weight:700; padding:3px 9px; border-radius:6px; }
.type-note      { background:#eff6ff; color:#1e40af; }
.type-absence   { background:#fff7ed; color:#9a3412; }
.type-emploi    { background:#f0fdf4; color:#166534; }
.type-formateur { background:#fdf4ff; color:#6b21a8; }
.type-autre     { background:#f1f5f9; color:#334155; }
.btn-sm { font-size:11px; font-weight:600; padding:6px 12px; border-radius:9px; text-decoration:none; display:inline-flex; align-items:center; gap:4px; cursor:pointer; transition:all .15s; border:none; }
.btn-sm-success { background:#dcfce7; color:#166534; border:1px solid #bbf7d0; }
.btn-sm-success:hover { background:#bbf7d0; }
.btn-sm-danger { background:#fee2e2; color:#dc2626; border:1px solid #fecaca; }
.btn-sm-danger:hover { background:#fecaca; }
</style>

<div class="rc-wrap">

@if(session('success'))
    <div class="flash-ok">
        <div class="flash-ok-icon"><svg width="18" height="18" fill="none" stroke="white" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg></div>
        <p style="font-size:13px;font-weight:600;color:var(--accent-tx);margin:0;">{{ session('success') }}</p>
    </div>
@endif

{{-- HERO --}}
<div class="rc-hero">
    <div style="display:flex;align-items:center;gap:16px;">
        <div class="rc-hero-icon">
            <svg width="26" height="26" fill="none" stroke="white" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/>
            </svg>
        </div>
        <div>
            <h1 class="rc-hero-title">Réclamations</h1>
            <p class="rc-hero-sub">Gestion de toutes les réclamations des stagiaires</p>
        </div>
    </div>
    <div class="rc-stats">
        <div class="rc-stat">
            <div class="rc-stat-val">{{ $stats['total'] }}</div>
            <div class="rc-stat-lbl">Total</div>
        </div>
        <div class="rc-stat">
            <div class="rc-stat-val">{{ $stats['en_attente'] }}</div>
            <div class="rc-stat-lbl">En attente</div>
        </div>
        <div class="rc-stat">
            <div class="rc-stat-val">{{ $stats['traitee'] }}</div>
            <div class="rc-stat-lbl">Traitées</div>
        </div>
    </div>
</div>

{{-- FILTRES --}}
<div class="rc-filters">
    <form method="GET" action="{{ route('reclamations.index') }}" style="display:flex;gap:10px;flex-wrap:wrap;align-items:center;width:100%;">
        <select name="status" class="rc-filter-select" onchange="this.form.submit()">
            <option value="">Tous les statuts</option>
            <option value="en_attente" {{ request('status') === 'en_attente' ? 'selected' : '' }}>⏳ En attente</option>
            <option value="traitee"    {{ request('status') === 'traitee'    ? 'selected' : '' }}>✅ Traitées</option>
        </select>
        <select name="type" class="rc-filter-select" onchange="this.form.submit()">
            <option value="">Tous les types</option>
            <option value="note"      {{ request('type') === 'note'      ? 'selected' : '' }}>📝 Note</option>
            <option value="absence"   {{ request('type') === 'absence'   ? 'selected' : '' }}>📅 Absence</option>
            <option value="emploi"    {{ request('type') === 'emploi'    ? 'selected' : '' }}>🗓️ Emploi</option>
            <option value="formateur" {{ request('type') === 'formateur' ? 'selected' : '' }}>👨‍🏫 Formateur</option>
            <option value="autre"     {{ request('type') === 'autre'     ? 'selected' : '' }}>📌 Autre</option>
        </select>
        @if(request()->hasAny(['status','type']))
            <a href="{{ route('reclamations.index') }}" class="btn-sm btn-sm-danger">✕ Réinitialiser</a>
        @endif
    </form>
</div>

{{-- TABLE --}}
<div class="rc-table-wrap">
    @forelse($reclamations as $rec)
        @if($loop->first)
        <table class="rc-table">
            <thead>
                <tr>
                    <th>Stagiaire</th>
                    <th>Type</th>
                    <th>Description</th>
                    <th>Date</th>
                    <th>Statut</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
        @endif

        <tr>
            <td>
                <div style="font-weight:700;font-size:13px;">{{ $rec->stagiaire->name ?? '—' }}</div>
                <div style="font-size:11px;color:#94a3b8;">{{ $rec->stagiaire->email ?? '' }}</div>
            </td>
            <td>
                <span class="type-badge type-{{ $rec->type }}">
                    {{ ['note'=>'📝 Note','absence'=>'📅 Absence','emploi'=>'🗓️ Emploi','formateur'=>'👨‍🏫 Formateur','autre'=>'📌 Autre'][$rec->type] ?? $rec->type }}
                </span>
            </td>
            <td style="max-width:360px;">
                <p style="margin:0;font-size:12px;color:#475569;overflow:hidden;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;">
                    {{ $rec->description }}
                </p>
            </td>
            <td style="font-size:11px;color:#64748b;white-space:nowrap;">
                {{ $rec->created_at->format('d/m/Y') }}<br>
                <span style="color:#94a3b8;">{{ $rec->created_at->format('H:i') }}</span>
            </td>
            <td>
                @if($rec->status === 'en_attente')
                    <span class="badge badge-attente">⏳ En attente</span>
                @else
                    <span class="badge badge-traitee">✅ Traitée</span>
                @endif
            </td>
            <td>
                <div style="display:flex;gap:6px;flex-wrap:wrap;">
                    @if($rec->status === 'en_attente')
                        <form method="POST" action="{{ route('reclamations.status', $rec) }}">
                            @csrf @method('PATCH')
                            <input type="hidden" name="status" value="traitee">
                            <button type="submit" class="btn-sm btn-sm-success">✅ Marquer traitée</button>
                        </form>
                    @else
                        <form method="POST" action="{{ route('reclamations.status', $rec) }}">
                            @csrf @method('PATCH')
                            <input type="hidden" name="status" value="en_attente">
                            <button type="submit" class="btn-sm" style="background:#f1f5f9;color:#475569;border:1px solid #e2e8f0;">↩ Réouvrir</button>
                        </form>
                    @endif
                    @if(Auth::user()->role === 'admin')
                        <button onclick="openDeleteModal('{{ route('reclamations.destroy', $rec) }}')" class="btn-sm btn-sm-danger">🗑️</button>
                    @endif
                </div>
            </td>
        </tr>

        @if($loop->last)
            </tbody>
        </table>
        @endif
    @empty
        <div style="text-align:center;padding:60px 20px;">
            <div style="width:64px;height:64px;border-radius:20px;background:var(--accent-lt);margin:0 auto 16px;display:flex;align-items:center;justify-content:center;">
                <svg width="28" height="28" fill="none" stroke="var(--accent)" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/></svg>
            </div>
            <p style="font-size:14px;font-weight:700;color:#1e293b;margin:0 0 4px;">Aucune réclamation</p>
            <p style="font-size:12px;color:#94a3b8;margin:0;">Aucune réclamation trouvée pour ces filtres.</p>
        </div>
    @endforelse
</div>

@if($reclamations->hasPages())
<div style="margin-top:20px;">{{ $reclamations->links() }}</div>
@endif

{{-- DELETE MODAL --}}
<div id="delete-modal" style="display:none;position:fixed;inset:0;z-index:60;background:rgba(15,23,42,0.5);backdrop-filter:blur(4px);align-items:center;justify-content:center;" onclick="if(event.target===this)closeDeleteModal()">
    <div style="background:white;border-radius:20px;width:100%;max-width:380px;margin:16px;padding:24px;">
        <div style="display:flex;align-items:center;gap:12px;margin-bottom:16px;padding-bottom:14px;border-bottom:2px solid #dc2626;">
            <div style="width:44px;height:44px;border-radius:12px;background:#fee2e2;display:flex;align-items:center;justify-content:center;">
                <svg width="20" height="20" fill="none" stroke="#dc2626" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
            </div>
            <div style="flex:1;">
                <div style="font-size:15px;font-weight:800;color:#1e293b;">Supprimer la réclamation ?</div>
                <div style="font-size:11px;color:#64748b;">Cette action est irréversible.</div>
            </div>
            <button onclick="closeDeleteModal()" style="width:28px;height:28px;border-radius:8px;border:none;background:#f1f5f9;cursor:pointer;">×</button>
        </div>
        <div style="display:flex;gap:10px;">
            <button onclick="closeDeleteModal()" class="btn-sm" style="flex:1;justify-content:center;background:white;border:1.5px solid #e2e8f0;color:#64748b;">Annuler</button>
            <form id="delete-form" method="POST" style="flex:1;">
                @csrf @method('DELETE')
                <button type="submit" class="btn-sm btn-sm-danger" style="width:100%;justify-content:center;">🗑️ Supprimer</button>
            </form>
        </div>
    </div>
</div>

</div>

<script>
function openDeleteModal(action) {
    document.getElementById('delete-form').action = action;
    document.getElementById('delete-modal').style.display = 'flex';
}
function closeDeleteModal() {
    document.getElementById('delete-modal').style.display = 'none';
}
</script>
@endsection