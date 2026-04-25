{{-- resources/views/reclamations/index.blade.php --}}
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

    $statusConfig = \App\Models\Reclamation::STATUSES;
    $typeConfig   = \App\Models\Reclamation::TYPES;

    $roleLabels = [
        'admin'        => ['label'=>'Admin',        'bg'=>'#d1fae5','color'=>'#065f38'],
        'gestionnaire' => ['label'=>'Gestionnaire', 'bg'=>'#f1f5f9','color'=>'#1e293b'],
        'formateur'    => ['label'=>'Formateur',    'bg'=>'#eff6ff','color'=>'#1e40af'],
        'stagiaire'    => ['label'=>'Stagiaire',    'bg'=>'#f5f3ff','color'=>'#6d28d9'],
    ];
@endphp

<style>
:root {
    --accent:    {{ $p['primary'] }};
    --accent-gr: {{ $p['gradient'] }};
    --accent-lt: {{ $p['light'] }};
    --accent-ltr:{{ $p['lighter'] }};
    --accent-tx: {{ $p['text'] }};
    --accent-bd: {{ $p['border'] }};
}
.rc-wrap { font-family:'Segoe UI',system-ui,sans-serif; max-width:1200px; margin:0 auto; }

/* Hero */
.rc-hero { background:var(--accent-gr); border-radius:20px; padding:26px 30px; margin-bottom:22px;
           display:flex; align-items:center; justify-content:space-between; gap:14px;
           flex-wrap:wrap; position:relative; overflow:hidden; }
.rc-hero::after { content:''; position:absolute; right:-40px; top:-40px; width:200px; height:200px;
                  border-radius:50%; background:rgba(255,255,255,0.06); pointer-events:none; }
.stat-pill { background:rgba(255,255,255,0.15); border:1px solid rgba(255,255,255,0.2);
             border-radius:14px; padding:10px 18px; text-align:center; }
.stat-pill-val { font-size:22px; font-weight:900; color:white; }
.stat-pill-lbl { font-size:10px; color:rgba(255,255,255,0.72); }

/* Flash */
.flash-ok { display:flex; align-items:center; gap:12px; padding:14px 18px; border-radius:14px;
            margin-bottom:18px; background:var(--accent-ltr); border:1px solid var(--accent-bd);
            animation:fi .3s ease; }
@keyframes fi { from{opacity:0;transform:translateY(-6px)} to{opacity:1;transform:translateY(0)} }

/* Filters */
.filter-bar { background:white; border-radius:14px; border:1px solid #e2e8f0; padding:14px 18px;
              margin-bottom:18px; display:flex; gap:10px; flex-wrap:wrap; align-items:center; }
.f-select { border:1.5px solid #e2e8f0; border-radius:10px; padding:7px 12px; font-size:12px;
            color:#1e293b; background:white; cursor:pointer; outline:none; }
.f-select:focus { border-color:var(--accent-bd); }
.btn-reset { font-size:11px; font-weight:600; padding:7px 13px; border-radius:10px; background:white;
             color:#64748b; border:1.5px solid #e2e8f0; text-decoration:none;
             display:inline-flex; align-items:center; gap:4px; }
.btn-reset:hover { background:#f8fafc; }

/* Table */
.rc-table-wrap { background:white; border-radius:18px; border:1px solid #e2e8f0; overflow:hidden; }
.rc-table { width:100%; border-collapse:collapse; }
.rc-table th { padding:11px 14px; background:#f8fafc; font-size:9px; font-weight:800;
               text-transform:uppercase; letter-spacing:.7px; color:#64748b;
               border-bottom:1px solid #e2e8f0; text-align:left; white-space:nowrap; }
.rc-table td { padding:13px 14px; border-bottom:1px solid #f1f5f9; font-size:12px;
               color:#1e293b; vertical-align:middle; }
.rc-table tr:last-child td { border-bottom:none; }
.rc-table tr:hover td { background:#fafbfd; }
.badge { font-size:9px; font-weight:700; padding:3px 9px; border-radius:7px; white-space:nowrap; }
.avatar-sm { width:28px; height:28px; border-radius:8px; background:#eff6ff;
             display:inline-flex; align-items:center; justify-content:center;
             font-size:9px; font-weight:800; color:#1e40af; flex-shrink:0; }
.msg-dot { display:inline-flex; align-items:center; gap:4px; font-size:10px;
           font-weight:700; color:#64748b; }
.msg-dot.has-new { color:#2563eb; }
.btn-view { font-size:11px; font-weight:700; padding:6px 14px; border-radius:9px;
            background:var(--accent-gr); color:white; text-decoration:none;
            display:inline-flex; align-items:center; gap:4px; transition:opacity .15s; }
.btn-view:hover { opacity:.85; }
.unread-row td:first-child { border-left:3px solid #2563eb; }
</style>

<div class="rc-wrap">

@if(session('success'))
<div class="flash-ok">
    <svg width="16" height="16" fill="none" stroke="var(--accent)" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
    </svg>
    <span style="font-size:13px;font-weight:600;color:var(--accent-tx);">{{ session('success') }}</span>
</div>
@endif

{{-- Hero --}}
<div class="rc-hero">
    <div style="display:flex;align-items:center;gap:16px;">
        <div style="width:52px;height:52px;border-radius:16px;background:rgba(255,255,255,0.15);
                    display:flex;align-items:center;justify-content:center;">
            <svg width="26" height="26" fill="none" stroke="white" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/>
            </svg>
        </div>
        <div>
            <h1 style="font-size:20px;font-weight:800;color:white;margin:0;">Réclamations</h1>
            <p style="font-size:12px;color:rgba(255,255,255,0.72);margin:3px 0 0;">
                Gestion de toutes les réclamations des stagiaires
            </p>
        </div>
    </div>
    <div style="display:flex;gap:10px;flex-wrap:wrap;">
        @foreach(['total'=>'Total','en_attente'=>'En attente','en_cours'=>'En cours','traite'=>'Traités'] as $k => $l)
        <div class="stat-pill">
            <div class="stat-pill-val">{{ $stats[$k] }}</div>
            <div class="stat-pill-lbl">{{ $l }}</div>
        </div>
        @endforeach
    </div>
</div>

{{-- Filters --}}
<div class="filter-bar">
    <form method="GET" action="{{ route('reclamations.index') }}"
          style="display:flex;gap:10px;flex-wrap:wrap;align-items:center;width:100%;">

        <select name="status" class="f-select" onchange="this.form.submit()">
            <option value="">Tous les statuts</option>
            @foreach($statusConfig as $k => $cfg)
                <option value="{{ $k }}" {{ request('status') === $k ? 'selected' : '' }}>
                    {{ $cfg['icon'] }} {{ $cfg['label'] }}
                </option>
            @endforeach
        </select>

        <select name="type" class="f-select" onchange="this.form.submit()">
            <option value="">Tous les types</option>
            @foreach($typeConfig as $k => $cfg)
                <option value="{{ $k }}" {{ request('type') === $k ? 'selected' : '' }}>
                    {{ $cfg['icon'] }} {{ $cfg['label'] }}
                </option>
            @endforeach
        </select>

        @if(request()->hasAny(['status','type']))
            <a href="{{ route('reclamations.index') }}" class="btn-reset">✕ Réinitialiser</a>
        @endif
    </form>
</div>

{{-- Table --}}
<div class="rc-table-wrap">
    @if($reclamations->isEmpty())
        <div style="text-align:center;padding:60px 20px;">
            <div style="width:60px;height:60px;border-radius:18px;background:var(--accent-lt);
                        margin:0 auto 14px;display:flex;align-items:center;justify-content:center;font-size:26px;">
                💬
            </div>
            <p style="font-size:14px;font-weight:700;color:#1e293b;margin:0 0 4px;">Aucune réclamation</p>
            <p style="font-size:12px;color:#94a3b8;margin:0;">
                Aucune réclamation pour ces filtres.
            </p>
        </div>
    @else
        <div style="overflow-x:auto;">
        <table class="rc-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Stagiaire</th>
                    <th>Type</th>
                    <th>Aperçu</th>
                    <th>Messages</th>
                    <th>Assigné à</th>
                    <th>Statut</th>
                    <th>Date</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
            @foreach($reclamations as $rec)
                @php
                    $sc = $statusConfig[$rec->status] ?? $statusConfig['en_attente'];
                    $tc = $typeConfig[$rec->type]     ?? $typeConfig['autre'];
                    $sName    = $rec->stagiaire?->name ?? '—';
                    $initials = strtoupper(mb_substr($sName, 0, 1) . mb_substr(explode(' ', $sName)[1] ?? '', 0, 1));
                    $isNew    = $rec->status === 'en_attente';
                @endphp
                <tr class="{{ $isNew ? 'unread-row' : '' }}">
                    <td>
                        <span style="font-size:11px;font-weight:700;color:#94a3b8;">#{{ $rec->id }}</span>
                    </td>
                    <td>
                        <div style="display:flex;align-items:center;gap:8px;">
                            <div class="avatar-sm">{{ $initials }}</div>
                            <div>
                                <div style="font-weight:700;font-size:12px;">{{ $sName }}</div>
                                <div style="font-size:10px;color:#94a3b8;">{{ $rec->stagiaire?->email ?? '' }}</div>
                            </div>
                        </div>
                    </td>
                    <td>
                        <span class="badge" style="background:#eff6ff;color:#1e40af;">
                            {{ $tc['icon'] }} {{ $tc['label'] }}
                        </span>
                    </td>
                    <td style="max-width:220px;">
                        <p style="margin:0;font-size:12px;color:#475569;
                                  overflow:hidden;display:-webkit-box;
                                  -webkit-line-clamp:2;-webkit-box-orient:vertical;
                                  word-break:break-all;overflow-wrap:break-word;">
                            {{ $rec->description }}
                        </p>
                    </td>
                    <td>
                        <span class="msg-dot {{ $rec->messages_count > 0 ? 'has-new' : '' }}">
                            💬 {{ $rec->messages_count }}
                        </span>
                    </td>
                    <td>
                        @if($rec->assignee)
                            @php $ar = $roleLabels[$rec->assignee->role] ?? $roleLabels['formateur']; @endphp
                            <span style="font-size:11px;font-weight:600;color:#1e293b;">
                                {{ $rec->assignee->name }}
                            </span><br>
                            <span style="font-size:9px;font-weight:700;padding:1px 6px;border-radius:5px;
                                         background:{{ $ar['bg'] }};color:{{ $ar['color'] }};">
                                {{ $ar['label'] }}
                            </span>
                        @else
                            <span style="font-size:10px;color:#cbd5e1;">—</span>
                        @endif
                    </td>
                    <td>
                        <span class="badge" style="background:{{ $sc['bg'] }};color:{{ $sc['color'] }};border:1px solid {{ $sc['border'] }};">
                            {{ $sc['icon'] }} {{ $sc['label'] }}
                        </span>
                    </td>
                    <td style="white-space:nowrap;">
                        <div style="font-size:11px;color:#64748b;">{{ $rec->created_at->format('d/m/Y') }}</div>
                        <div style="font-size:10px;color:#94a3b8;">{{ $rec->created_at->format('H:i') }}</div>
                    </td>
                    <td style="white-space:nowrap;">
                        <a href="{{ route('reclamations.show', $rec) }}" class="btn-view">
                            💬 Ouvrir
                        </a>
                        
                        @can('reclamation-manage')
                        <button onclick="confirmDelete({{ $rec->id }})"
                            style="font-size:11px;font-weight:700;padding:6px 14px;border-radius:9px;
                                   background:#fff1f2;color:#be123c;border:1.5px solid #fecdd3;
                                   cursor:pointer;display:inline-flex;align-items:center;gap:4px;
                                   transition:all .15s;margin-left:4px;"
                            onmouseover="this.style.background='#ffe4e6'"
                            onmouseout="this.style.background='#fff1f2'">
                            🗑️ Supprimer
                        </button>

                        {{-- Hidden form for this row --}}
                        <form id="delete-form-{{ $rec->id }}"
                              action="{{ route('reclamations.destroy', $rec) }}"
                              method="POST" style="display:none;">
                            @csrf
                            @method('DELETE')
                        </form>
                        @endcan
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
        </div>

        @if($reclamations->hasPages())
        <div style="padding:12px 18px;border-top:1px solid #f1f5f9;">
            {{ $reclamations->links() }}
        </div>
        @endif
    @endif
</div>

</div>

{{-- ── MODAL CONFIRMATION ── --}}
<div id="delete-modal" style="display:none;position:fixed;inset:0;z-index:9999;
     align-items:center;justify-content:center;">
    {{-- Backdrop --}}
    <div onclick="cancelDelete()"
         style="position:absolute;inset:0;background:rgba(0,0,0,0.45);backdrop-filter:blur(3px);"></div>

    {{-- Card --}}
    <div style="position:relative;background:white;border-radius:20px;padding:32px;
                max-width:420px;width:90%;box-shadow:0 24px 48px rgba(0,0,0,0.18);
                animation:popIn .2s ease;">
        <div style="text-align:center;margin-bottom:20px;">
            <div style="width:60px;height:60px;border-radius:18px;background:#fff1f2;
                        margin:0 auto 14px;display:flex;align-items:center;justify-content:center;font-size:28px;">
                🗑️
            </div>
            <h3 style="font-size:17px;font-weight:800;color:#1e293b;margin:0 0 8px;">
                Supprimer la réclamation ?
            </h3>
            <p style="font-size:13px;color:#64748b;margin:0;line-height:1.5;">
                Cette action est <strong>irréversible</strong>.<br>
                Tous les messages liés seront supprimés.
            </p>
        </div>

        <div style="display:flex;gap:10px;">
            <button onclick="cancelDelete()"
                style="flex:1;padding:12px;border-radius:12px;border:1.5px solid #e2e8f0;
                       background:white;font-size:13px;font-weight:700;color:#64748b;cursor:pointer;">
                ✕ Annuler
            </button>
            <button onclick="submitDelete()"
                style="flex:1;padding:12px;border-radius:12px;border:none;
                       background:linear-gradient(135deg,#be123c,#ef4444);
                       font-size:13px;font-weight:700;color:white;cursor:pointer;">
                🗑️ Oui, supprimer
            </button>
        </div>
    </div>
</div>

<style>
@keyframes popIn {
    from { opacity:0; transform:scale(.92); }
    to   { opacity:1; transform:scale(1); }
}
</style>

<script>
let _deleteId = null;

function confirmDelete(id) {
    _deleteId = id;
    const modal = document.getElementById('delete-modal');
    modal.style.display = 'flex';
}

function cancelDelete() {
    _deleteId = null;
    document.getElementById('delete-modal').style.display = 'none';
}

function submitDelete() {
    if (_deleteId) {
        document.getElementById('delete-form-' + _deleteId).submit();
    }
}

// Close on ESC
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') cancelDelete();
});
</script>

@endsection