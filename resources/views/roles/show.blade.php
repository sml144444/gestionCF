@extends('layouts.app')
@section('title', 'Détails du rôle')
@section('page-title', 'Détails du rôle')

@section('content')
@php
    $user = Auth::user();
    $userRole = $user->role;
    $roleItem = $role; // le rôle passé par le contrôleur
    
    $palettes = [
        'admin'        => ['primary'=>'#0a6640','medium'=>'#1a8c56','light'=>'#e8f5ee','lighter'=>'#f0fdf4','text'=>'#065f38','border'=>'#bbf7d0','shadow'=>'rgba(10,102,64,0.15)','gradient'=>'linear-gradient(135deg,#0a6640 0%,#1a8c56 100%)'],
        'gestionnaire' => ['primary'=>'#1e293b','medium'=>'#334155','light'=>'#f1f5f9','lighter'=>'#f8fafc','text'=>'#1e293b','border'=>'#cbd5e1','shadow'=>'rgba(30,41,59,0.15)','gradient'=>'linear-gradient(135deg,#1e293b 0%,#334155 100%)'],
        'formateur'    => ['primary'=>'#1a4f8a','medium'=>'#2563eb','light'=>'#eff6ff','lighter'=>'#f0f7ff','text'=>'#1e40af','border'=>'#bfdbfe','shadow'=>'rgba(26,79,138,0.15)','gradient'=>'linear-gradient(135deg,#1a4f8a 0%,#2563eb 100%)'],
        'stagiaire'    => ['primary'=>'#ea580c','medium'=>'#f97316','light'=>'#fff7ed','lighter'=>'#fffbeb','text'=>'#9a3412','border'=>'#fed7aa','shadow'=>'rgba(234,88,12,0.15)','gradient'=>'linear-gradient(135deg,#ea580c 0%,#f97316 100%)'],
    ];
    $p = $palettes[$userRole] ?? $palettes['gestionnaire'];
    
    $roleColors = [
        'admin'        => ['bg'=>'#0a6640','light'=>'#e8f5ee','text'=>'#065f38','icon'=>'🛡️'],
        'gestionnaire' => ['bg'=>'#1e293b','light'=>'#f1f5f9','text'=>'#1e293b','icon'=>'📋'],
        'formateur'    => ['bg'=>'#1a4f8a','light'=>'#eff6ff','text'=>'#1e40af','icon'=>'👨‍🏫'],
        'stagiaire'    => ['bg'=>'#ea580c','light'=>'#fff7ed','text'=>'#9a3412','icon'=>'🎓'],
    ];
    $rc = $roleColors[$roleItem->name] ?? ['bg'=>'#64748b','light'=>'#f8fafc','text'=>'#334155','icon'=>'🔐'];
    
    $groupLabels = [
        'emploi'      => ['label' => 'Emploi du temps',     'color' => '#2563eb', 'bg' => '#eff6ff', 'icon' => 'M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z'],
        'user'        => ['label' => 'Utilisateurs',       'color' => '#16a34a', 'bg' => '#f0fdf4', 'icon' => 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v2h5M7 20v-2a3 3 0 015.356-1.857M15 7a3 3 0 11-6 0 3 3 0 016 0z'],
        'stagiaire'   => ['label' => 'Stagiaires',         'color' => '#0891b2', 'bg' => '#ecfeff', 'icon' => 'M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z'],
        'groupe'      => ['label' => 'Groupes & Filières', 'color' => '#9333ea', 'bg' => '#fdf4ff', 'icon' => 'M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10'],
        'edu'         => ['label' => 'Import EDU',         'color' => '#ea580c', 'bg' => '#fff7ed', 'icon' => 'M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12'],
        'role'        => ['label' => 'Rôles & Permissions','color' => '#dc2626', 'bg' => '#fff1f2', 'icon' => 'M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z'],
        'reportation' => ['label' => 'Reportations',       'color' => '#7c3aed', 'bg' => '#f5f3ff', 'icon' => 'M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15'],
    ];
    
    $actionLabels = [
        'list'    => '📋 Voir la liste',
        'create'  => '➕ Créer',
        'edit'    => '✏️ Modifier',
        'delete'  => '🗑️ Supprimer',
        'import'  => '📥 Importer',
        'view'    => '👁️ Consulter',
        'manage'  => '⚙️ Gérer',
        'lien'    => '🔗 Liens réunion',
        'change'  => '📚 Changer module',
        'view-all-groups' => '👑 Voir tous les groupes',
    ];
    
    $grouped = $roleItem->permissions->groupBy(fn($p) => explode('-', $p->name)[0]);
    $isSystem = in_array($roleItem->name, ['admin','gestionnaire','formateur','stagiaire']);
@endphp

<style>
:root {
    --accent: {{ $p['primary'] }};
    --accent-gr: {{ $p['gradient'] }};
    --accent-sh: {{ $p['shadow'] }};
}
.role-detail-wrap { font-family:'Segoe UI',system-ui,sans-serif; max-width:900px; margin:0 auto; }
.role-detail-hero { background:var(--accent-gr); border-radius:20px; padding:28px 32px; margin-bottom:24px; display:flex; align-items:center; justify-content:space-between; gap:16px; flex-wrap:wrap; position:relative; overflow:hidden; }
.role-detail-hero::after { content:''; position:absolute; right:-40px; top:-40px; width:200px; height:200px; border-radius:50%; background:rgba(255,255,255,0.06); pointer-events:none; }
.role-detail-icon { width:64px; height:64px; border-radius:20px; background:rgba(255,255,255,0.15); display:flex; align-items:center; justify-content:center; flex-shrink:0; font-size:32px; }
.role-detail-name { font-size:22px; font-weight:800; color:white; margin:0; text-transform:capitalize; }
.role-detail-sub { font-size:12px; color:rgba(255,255,255,0.75); margin-top:4px; }
.role-hero-badge { background:rgba(255,255,255,0.15); border:1px solid rgba(255,255,255,0.25); color:white; font-size:11px; font-weight:700; padding:6px 14px; border-radius:99px; }
.role-detail-card { background:white; border-radius:20px; border:1px solid #e2e8f0; overflow:hidden; box-shadow:0 2px 12px rgba(0,0,0,0.05); }
.role-perm-group { border:1px solid #e2e8f0; border-radius:16px; overflow:hidden; margin-bottom:14px; transition:all .2s; }
.role-perm-group:hover { border-color:var(--accent); box-shadow:0 2px 8px rgba(0,0,0,0.05); }
.role-perm-group-header { padding:14px 20px; display:flex; align-items:center; gap:12px; cursor:pointer; }
.role-perm-group-icon { width:36px; height:36px; border-radius:12px; display:flex; align-items:center; justify-content:center; flex-shrink:0; }
.role-perm-group-title { font-size:14px; font-weight:700; }
.role-perm-group-count { margin-left:auto; font-size:10px; font-weight:800; padding:3px 10px; border-radius:99px; }
.role-perm-group-body { padding:14px 20px; display:flex; flex-wrap:wrap; gap:8px; background:#fafbfc; border-top:1px solid #f1f5f9; }
.permission-badge { font-size:11px; font-weight:700; padding:7px 14px; border-radius:10px; transition:transform .1s; }
.permission-badge:hover { transform:translateY(-1px); }
.btn-back { display:inline-flex; align-items:center; gap:8px; padding:8px 18px; border-radius:12px; background:white; color:#475569; font-size:12px; font-weight:600; text-decoration:none; border:1.5px solid #e2e8f0; margin-bottom:20px; transition:all .2s; }
.btn-back:hover { border-color:var(--accent); background:var(--accent); color:white; }
.btn-back:hover svg { stroke:white; }
.btn-edit { display:inline-flex; align-items:center; gap:8px; padding:10px 20px; border-radius:12px; background:white; color:{{ $p['primary'] }}; font-size:13px; font-weight:700; text-decoration:none; border:1.5px solid {{ $p['primary'] }}; transition:all .2s; }
.btn-edit:hover { background:{{ $p['primary'] }}; color:white; }
.stats-row { display:flex; gap:12px; margin-top:16px; flex-wrap:wrap; }
.stat-chip { background:rgba(255,255,255,0.12); padding:6px 14px; border-radius:99px; font-size:11px; color:white; display:inline-flex; align-items:center; gap:6px; }
</style>

<div class="role-detail-wrap">

{{-- Back button --}}
<a href="{{ route('roles.index') }}" class="btn-back">
    <svg width="12" height="12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"/>
    </svg>
    Retour aux rôles
</a>

{{-- Hero section --}}
<div class="role-detail-hero">
    <div style="display:flex; align-items:center; gap:20px;">
        <div class="role-detail-icon">{{ $rc['icon'] }}</div>
        <div>
            <h1 class="role-detail-name">{{ $roleItem->name }}</h1>
            <p class="role-detail-sub">
                {{ $roleItem->permissions->count() }} permission(s) assignée(s)
            </p>
            <div class="stats-row">
                <span class="stat-chip">📋 Rôle {{ $isSystem ? 'système' : 'personnalisé' }}</span>
                <span class="stat-chip">🆔 ID #{{ $roleItem->id }}</span>
            </div>
        </div>
    </div>
    @can('role-edit')
    <a href="{{ route('roles.edit', $roleItem) }}" class="btn-edit">
        ✎ Modifier le rôle
    </a>
    @endcan
</div>

{{-- Permissions card --}}
<div class="role-detail-card">
    <div style="padding:20px 24px; border-bottom:1px solid #f1f5f9; background:white;">
        <div style="display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:10px;">
            <div>
                <div style="font-size:10px; font-weight:800; color:#94a3b8; letter-spacing:1.5px; text-transform:uppercase; margin-bottom:4px;">
                    📋 Permissions assignées
                </div>
                <div style="font-size:12px; color:#475569;">
                    Détail des droits accordés à ce rôle
                </div>
            </div>
            <div style="font-size:11px; font-weight:700; padding:5px 12px; border-radius:99px; background:var(--accent); color:white;">
                Total : {{ $roleItem->permissions->count() }}
            </div>
        </div>
    </div>

    <div style="padding:20px 24px;">
        @if($roleItem->permissions->isEmpty())
            <div style="padding:50px 20px; text-align:center; background:#f8fafc; border-radius:16px; border:1px dashed #e2e8f0;">
                <div style="font-size:40px; margin-bottom:12px;">🔒</div>
                <p style="font-size:14px; font-weight:600; color:#1e293b; margin:0;">Aucune permission assignée</p>
                <p style="font-size:11px; color:#94a3b8; margin-top:5px;">Ce rôle n'a aucun droit spécifique pour le moment.</p>
            </div>
        @else
            @foreach($grouped as $group => $perms)
                @php 
                    $g = $groupLabels[$group] ?? [
                        'label' => ucfirst($group), 
                        'color' => '#64748b', 
                        'bg' => '#f8fafc', 
                        'icon' => 'M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z'
                    ]; 
                @endphp
                <div class="role-perm-group">
                    <div class="role-perm-group-header" style="background:{{ $g['bg'] }};" onclick="toggleGroupBody(this)">
                        <div class="role-perm-group-icon" style="background:{{ $g['color'] }}20;">
                            <svg width="18" height="18" fill="none" stroke="{{ $g['color'] }}" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="{{ $g['icon'] }}"/>
                            </svg>
                        </div>
                        <span class="role-perm-group-title" style="color:{{ $g['color'] }};">{{ $g['label'] }}</span>
                        <span class="role-perm-group-count" style="background:{{ $g['color'] }}10; color:{{ $g['color'] }};">
                            {{ $perms->count() }} permission(s)
                        </span>
                        <svg class="toggle-icon" width="14" height="14" fill="none" stroke="{{ $g['color'] }}" viewBox="0 0 24 24" style="transition:transform 0.2s;">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </div>
                    <div class="role-perm-group-body" style="display:flex;">
                        @foreach($perms->sortBy('name') as $perm)
                            @php
                                $parts = explode('-', $perm->name);
                                $action = $parts[1] ?? $perm->name;
                                $suffix = $parts[2] ?? null;
                                $label = $actionLabels[$action] ?? ucfirst($action);
                                if ($suffix) $label .= ' ' . ucfirst(str_replace('_', ' ', $suffix));
                                if ($action === 'view-all-groups') $label = '👑 Voir tous les groupes';
                            @endphp
                            <span class="permission-badge" style="background:{{ $g['color'] }}10; color:{{ $g['color'] }}; border:1px solid {{ $g['color'] }}20;">
                                {{ $label }}
                            </span>
                        @endforeach
                    </div>
                </div>
            @endforeach
        @endif
    </div>
</div>

{{-- Info footer --}}
<div style="margin-top:20px; padding:16px 20px; background:#f8fafc; border-radius:16px; border:1px solid #e2e8f0; display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:10px;">
    <div style="display:flex; align-items:center; gap:10px;">
        <div style="width:32px; height:32px; border-radius:10px; background:{{ $p['light'] }}; display:flex; align-items:center; justify-content:center;">
            <svg width="14" height="14" fill="none" stroke="{{ $p['primary'] }}" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
        </div>
        <div>
            <div style="font-size:10px; font-weight:800; color:#94a3b8;">CRÉÉ LE</div>
            <div style="font-size:12px; font-weight:600; color:#1e293b;">{{ $roleItem->created_at->format('d M Y à H:i') }}</div>
        </div>
    </div>
    <div style="display:flex; align-items:center; gap:10px;">
        <div style="width:32px; height:32px; border-radius:10px; background:{{ $p['light'] }}; display:flex; align-items:center; justify-content:center;">
            <svg width="14" height="14" fill="none" stroke="{{ $p['primary'] }}" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
        </div>
        <div>
            <div style="font-size:10px; font-weight:800; color:#94a3b8;">DERNIÈRE MODIFICATION</div>
            <div style="font-size:12px; font-weight:600; color:#1e293b;">{{ $roleItem->updated_at->format('d M Y à H:i') }}</div>
        </div>
    </div>
    @if($isSystem)
    <div style="display:flex; align-items:center; gap:6px; background:#fef3c7; padding:6px 14px; border-radius:99px;">
        <span style="font-size:12px;">🔒</span>
        <span style="font-size:10px; font-weight:700; color:#92400e;">RÔLE SYSTÈME - NON SUPPRIMABLE</span>
    </div>
    @endif
</div>

</div>

<script>
function toggleGroupBody(header) {
    const body = header.nextElementSibling;
    const icon = header.querySelector('.toggle-icon');
    if (body.style.display === 'none') {
        body.style.display = 'flex';
        icon.style.transform = 'rotate(0deg)';
    } else {
        body.style.display = 'none';
        icon.style.transform = 'rotate(-90deg)';
    }
}
</script>

@endsection