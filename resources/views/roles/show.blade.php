@extends('layouts.app')
@section('title', 'Détails du rôle')
@section('page-title', 'Détails du rôle')

@section('content')
<div style="font-family:'Segoe UI',system-ui,sans-serif; max-width:600px;">

<div style="display:flex; align-items:center; gap:12px; margin-bottom:24px;">
    <a href="{{ route('roles.index') }}"
       style="display:inline-flex; align-items:center; gap:5px; padding:7px 12px;
              border-radius:10px; border:1.5px solid #e2e8f0; background:white;
              color:#475569; font-size:12px; font-weight:600; text-decoration:none;">
        <svg width="12" height="12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"/>
        </svg>
        Retour
    </a>
</div>

<div style="background:white; border-radius:20px; border:1px solid #e2e8f0;
            box-shadow:0 2px 12px rgba(0,0,0,0.06); overflow:hidden;">

    {{-- Header --}}
    <div style="padding:20px 24px; background:#e8f5ee; border-bottom:2px solid #0a6640;
                display:flex; align-items:center; justify-content:space-between;">
        <div style="display:flex; align-items:center; gap:12px;">
            <div style="width:44px; height:44px; border-radius:12px; background:#0a6640;
                        display:flex; align-items:center; justify-content:center;">
                <svg width="20" height="20" fill="none" stroke="white" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                </svg>
            </div>
            <div>
                <div style="font-size:18px; font-weight:800; color:#065f38; text-transform:capitalize;">
                    {{ $role->name }}
                </div>
                <div style="font-size:11px; color:#065f3880; margin-top:2px;">
                    {{ $rolePermissions->count() }} permission{{ $rolePermissions->count() > 1 ? 's' : '' }} assignée{{ $rolePermissions->count() > 1 ? 's' : '' }}
                </div>
            </div>
        </div>
        @can('role-edit')
        <a href="{{ route('roles.edit', $role) }}"
           style="display:inline-flex; align-items:center; gap:5px; padding:8px 14px;
                  border-radius:10px; background:#0a6640; color:white; font-size:12px;
                  font-weight:700; text-decoration:none;">
            ✎ Modifier
        </a>
        @endcan
    </div>

    {{-- Permissions --}}
    <div style="padding:22px 24px;">
        <div style="font-size:9px; font-weight:800; color:#94a3b8; letter-spacing:1.5px;
                    text-transform:uppercase; margin-bottom:14px;">
            Permissions assignées
        </div>

        @if($rolePermissions->isEmpty())
            <div style="padding:24px; text-align:center; background:#f8fafc; border-radius:12px;
                        border:1px dashed #e2e8f0;">
                <p style="font-size:13px; color:#94a3b8; margin:0;">Aucune permission assignée à ce rôle.</p>
            </div>
        @else
            @php
                $grouped = $rolePermissions->groupBy(fn($p) => explode('-', $p->name)[0]);
                $groupLabels = [
                    'emploi'  => ['label'=>'Emploi du temps',   'color'=>'#2563eb','bg'=>'#eff6ff'],
                    'user'    => ['label'=>'Utilisateurs',       'color'=>'#16a34a','bg'=>'#f0fdf4'],
                    'groupe'  => ['label'=>'Groupes & Filières', 'color'=>'#9333ea','bg'=>'#fdf4ff'],
                    'edu'     => ['label'=>'Import EDU',          'color'=>'#ea580c','bg'=>'#fff7ed'],
                    'role'    => ['label'=>'Rôles & Permissions', 'color'=>'#dc2626','bg'=>'#fff1f2'],
                ];
                $actionLabels = ['list'=>'Voir','create'=>'Créer','edit'=>'Modifier','delete'=>'Supprimer','import'=>'Importer','view'=>'Consulter'];
            @endphp
            <div style="display:flex; flex-direction:column; gap:10px;">
                @foreach($grouped as $group => $perms)
                    @php $g = $groupLabels[$group] ?? ['label'=>ucfirst($group),'color'=>'#64748b','bg'=>'#f8fafc']; @endphp
                    <div style="border-radius:12px; overflow:hidden; border:1px solid {{ $g['color'] }}20;">
                        <div style="padding:8px 14px; background:{{ $g['bg'] }}; font-size:11px;
                                    font-weight:700; color:{{ $g['color'] }};">
                            {{ $g['label'] }}
                        </div>
                        <div style="padding:10px 14px; display:flex; flex-wrap:wrap; gap:6px; background:white;">
                            @foreach($perms as $perm)
                                @php $action = explode('-', $perm->name)[1] ?? $perm->name; @endphp
                                <span style="font-size:11px; font-weight:700; padding:4px 12px;
                                             border-radius:8px; background:{{ $g['bg'] }}; color:{{ $g['color'] }};">
                                    ✓ {{ $actionLabels[$action] ?? ucfirst($action) }}
                                </span>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</div>

</div>
@endsection