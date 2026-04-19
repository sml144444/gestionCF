@extends('layouts.app')
@section('title', 'Modifier le rôle')
@section('page-title', 'Modifier le rôle')

@section('content')
@php
    $user = Auth::user();
    $userRole = $user->role;
    
    $palettes = [
        'admin'        => ['primary'=>'#0a6640','medium'=>'#1a8c56','light'=>'#e8f5ee','lighter'=>'#f0fdf4','text'=>'#065f38','border'=>'#bbf7d0','shadow'=>'rgba(10,102,64,0.15)','gradient'=>'linear-gradient(135deg,#0a6640 0%,#1a8c56 100%)'],
        'gestionnaire' => ['primary'=>'#1e293b','medium'=>'#334155','light'=>'#f1f5f9','lighter'=>'#f8fafc','text'=>'#1e293b','border'=>'#cbd5e1','shadow'=>'rgba(30,41,59,0.15)','gradient'=>'linear-gradient(135deg,#1e293b 0%,#334155 100%)'],
        'formateur'    => ['primary'=>'#1a4f8a','medium'=>'#2563eb','light'=>'#eff6ff','lighter'=>'#f0f7ff','text'=>'#1e40af','border'=>'#bfdbfe','shadow'=>'rgba(26,79,138,0.15)','gradient'=>'linear-gradient(135deg,#1a4f8a 0%,#2563eb 100%)'],
        'stagiaire'    => ['primary'=>'#ea580c','medium'=>'#f97316','light'=>'#fff7ed','lighter'=>'#fffbeb','text'=>'#9a3412','border'=>'#fed7aa','shadow'=>'rgba(234,88,12,0.15)','gradient'=>'linear-gradient(135deg,#ea580c 0%,#f97316 100%)'],
    ];
    $p = $palettes[$userRole] ?? $palettes['gestionnaire'];
    
    $groupLabels = [
        'emploi' => [
            'label' => 'Emploi du temps',
            'icon'  => 'M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z',
            'color' => '#2563eb', 'bg' => '#eff6ff',
            'perms' => [
                'emploi-view' => '📅 Consulter son planning',
                'emploi-view-all-groups' => '👑 Consulter tous les groupes',
                'emploi-create' => '➕ Créer une séance',
                'emploi-edit' => '✏️ Modifier une séance',
                'emploi-delete' => '🗑️ Supprimer une séance',
                'emploi-lien' => '🔗 Gérer les liens de réunion',
                'emploi-change-module' => '📚 Changer le module d\'une séance',
            ]
        ],
        'user' => [
            'label' => 'Utilisateurs',
            'icon'  => 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v2h5M7 20v-2a3 3 0 015.356-1.857M15 7a3 3 0 11-6 0 3 3 0 016 0z',
            'color' => '#16a34a', 'bg' => '#f0fdf4',
            'perms' => [
                'user-list' => '📋 Voir la liste des utilisateurs',
                'user-create' => '➕ Créer un utilisateur',
                'user-edit' => '✏️ Modifier un utilisateur',
                'user-delete' => '🗑️ Supprimer un utilisateur',
            ]
        ],
        'stagiaire' => [
            'label' => 'Stagiaires',
            'icon'  => 'M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z',
            'color' => '#0891b2', 'bg' => '#ecfeff',
            'perms' => [
                'stagiaire-list' => '👥 Voir la liste des stagiaires',
                'stagiaire-create' => '➕ Créer un stagiaire',      
                'stagiaire-edit'   => '✏️ Modifier un stagiaire', 
                'stagiaire-delete' => '🗑️ Supprimer un stagiaire', 

            ]
        ],
        'groupe' => [
            'label' => 'Groupes & Filières',
            'icon'  => 'M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10',
            'color' => '#9333ea', 'bg' => '#fdf4ff',
            'perms' => [
                'groupe-list' => '📋 Voir les groupes',
                'groupe-create' => '➕ Créer un groupe',
                'groupe-edit' => '✏️ Modifier un groupe',
                'groupe-delete' => '🗑️ Supprimer un groupe',
            ]
        ],
        'edu' => [
            'label' => 'Import EDU',
            'icon'  => 'M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12',
            'color' => '#ea580c', 'bg' => '#fff7ed',
            'perms' => [
                'edu-import' => '📥 Importer des données EDU',
                'edu-view' => '📋 Voir les imports',
            ]
        ],
        'role' => [
            'label' => 'Rôles & Permissions',
            'icon'  => 'M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z',
            'color' => '#dc2626', 'bg' => '#fff1f2',
            'perms' => [
                'role-list' => '📋 Voir les rôles',
                'role-create' => '➕ Créer un rôle',
                'role-edit' => '✏️ Modifier un rôle',
                'role-delete' => '🗑️ Supprimer un rôle',
            ]
        ],
        'reportation' => [
            'label' => 'Reportations',
            'icon'  => 'M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15',
            'color' => '#7c3aed', 'bg' => '#f5f3ff',
            'perms' => [
                'reportation-create' => '📋 Demander un report de séance',
                'reportation-manage' => '✅ Gérer les reportations (accepter/refuser)',
            ]
        ],
   // 👇 AJOUTEZ CE BLOC ICI
'salle' => [
    'label' => 'Salles',
    'icon'  => 'M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4',
    'color' => '#0d9488', 'bg' => '#f0fdfa',
    'perms' => [
        'salle-list'   => '🏫 Voir la liste des salles',
        'salle-create' => '➕ Ajouter une salle',
        'salle-edit'   => '✏️ Modifier une salle',
        'salle-delete' => '🗑️ Supprimer une salle',
    ]
],
// Fin du tableau
];
    
    $rolePermissionsArray = $rolePermissions ?? [];
@endphp

<style>
:root {
    --accent: {{ $p['primary'] }};
    --accent-gr: {{ $p['gradient'] }};
    --accent-sh: {{ $p['shadow'] }};
}
.role-edit-wrap { font-family:'Segoe UI',system-ui,sans-serif; max-width:900px; margin:0 auto; }
.role-hero { background:var(--accent-gr); border-radius:20px; padding:28px 32px; margin-bottom:24px; display:flex; align-items:center; justify-content:space-between; gap:16px; flex-wrap:wrap; position:relative; overflow:hidden; }
.role-hero::after { content:''; position:absolute; right:-40px; top:-40px; width:200px; height:200px; border-radius:50%; background:rgba(255,255,255,0.06); pointer-events:none; }
.role-hero-icon { width:52px; height:52px; border-radius:16px; background:rgba(255,255,255,0.15); display:flex; align-items:center; justify-content:center; flex-shrink:0; }
.role-hero-title { font-size:20px; font-weight:800; color:white; margin:0; text-transform:capitalize; }
.role-hero-sub { font-size:12px; color:rgba(255,255,255,0.75); margin-top:3px; }
.role-hero-badge { background:rgba(255,255,255,0.15); border:1px solid rgba(255,255,255,0.25); color:white; font-size:11px; font-weight:700; padding:6px 14px; border-radius:99px; }
.flash-error { padding:14px 18px; background:#fff1f2; border:1px solid #fecdd3; border-radius:14px; margin-bottom:16px; }
.flash-error li { font-size:12px; color:#be123c; margin:2px 0; }
.role-form-card { background:white; border-radius:20px; border:1px solid #e2e8f0; overflow:hidden; box-shadow:0 2px 12px rgba(0,0,0,0.05); }
.role-form-section { padding:22px 28px; border-bottom:1px solid #f1f5f9; }
.role-form-label { display:block; font-size:9px; font-weight:800; color:#94a3b8; letter-spacing:1.5px; text-transform:uppercase; margin-bottom:8px; }
.role-input { width:100%; height:44px; padding:0 14px; border-radius:12px; border:1.5px solid #e2e8f0; background:#f8fafc; font-size:14px; font-weight:600; color:#1e293b; outline:none; box-sizing:border-box; transition:all .15s; }
.role-input:focus { border-color:var(--accent); background:white; }
.role-input::placeholder { font-weight:400; color:#cbd5e1; }
.perms-header { display:flex; align-items:center; justify-content:space-between; margin-bottom:18px; flex-wrap:wrap; gap:12px; }
.perms-actions { display:flex; gap:8px; }
.perms-action-btn { font-size:10px; font-weight:600; padding:5px 12px; border-radius:8px; border:none; cursor:pointer; transition:all .15s; }
.perms-action-btn.select-all { background:#e8f5ee; color:#0a6640; }
.perms-action-btn.select-all:hover { background:#d1fae5; }
.perms-action-btn.deselect-all { background:#f1f5f9; color:#64748b; }
.perms-action-btn.deselect-all:hover { background:#e2e8f0; }
.perms-group { border:1px solid #e2e8f0; border-radius:16px; overflow:hidden; margin-bottom:14px; transition:all .2s; }
.perms-group:hover { border-color:var(--accent); box-shadow:0 2px 8px rgba(0,0,0,0.05); }
.perms-group-header { padding:14px 20px; display:flex; align-items:center; gap:12px; cursor:pointer; background:white; }
.perms-group-icon { width:36px; height:36px; border-radius:12px; display:flex; align-items:center; justify-content:center; flex-shrink:0; }
.perms-group-title { font-size:14px; font-weight:700; }
.perms-group-count { margin-left:auto; font-size:10px; font-weight:800; padding:3px 10px; border-radius:99px; }
.perms-group-toggle { transition:transform 0.2s; }
.perms-group-body { padding:14px 20px; display:flex; flex-wrap:wrap; gap:8px; background:#fafbfc; border-top:1px solid #f1f5f9; }
.permission-check { display:inline-flex; align-items:center; gap:8px; cursor:pointer; padding:8px 14px; border-radius:12px; border:1.5px solid #e2e8f0; background:white; transition:all .15s; user-select:none; }
.permission-check:hover { border-color:var(--accent); transform:translateY(-1px); }
.permission-tick { width:18px; height:18px; border-radius:6px; background:white; border:2px solid #cbd5e1; display:flex; align-items:center; justify-content:center; transition:all .15s; flex-shrink:0; }
.permission-check.selected .permission-tick { background:var(--accent); border-color:var(--accent); }
.permission-check.selected .permission-tick svg { display:block; }
.permission-tick svg { display:none; width:10px; height:10px; stroke:white; stroke-width:3; }
.permission-label { font-size:11px; font-weight:600; color:#475569; }
.permission-check.selected .permission-label { color:var(--accent); }
.btn-back { display:inline-flex; align-items:center; gap:8px; padding:8px 18px; border-radius:12px; background:white; color:#475569; font-size:12px; font-weight:600; text-decoration:none; border:1.5px solid #e2e8f0; margin-bottom:20px; transition:all .2s; }
.btn-back:hover { border-color:var(--accent); background:var(--accent); color:white; }
.btn-back:hover svg { stroke:white; }
.btn-submit { flex:1; height:48px; border-radius:12px; border:none; background:var(--accent-gr); color:white; font-size:13px; font-weight:700; cursor:pointer; box-shadow:0 4px 12px var(--accent-sh); transition:opacity .15s; display:inline-flex; align-items:center; justify-content:center; gap:8px; }
.btn-submit:hover { opacity:.88; }
.btn-cancel { flex:1; height:48px; border-radius:12px; border:1.5px solid #e2e8f0; background:white; color:#64748b; font-size:13px; font-weight:600; cursor:pointer; text-decoration:none; display:inline-flex; align-items:center; justify-content:center; gap:8px; transition:all .15s; }
.btn-cancel:hover { border-color:var(--accent); background:var(--accent-lt); color:var(--accent-tx); }
.search-perms { margin-bottom:20px; position:relative; }
.search-perms input { width:100%; height:42px; padding:0 14px 0 38px; border-radius:12px; border:1.5px solid #e2e8f0; background:#f8fafc; font-size:13px; outline:none; }
.search-perms input:focus { border-color:var(--accent); background:white; }
.search-perms svg { position:absolute; left:12px; top:50%; transform:translateY(-50%); stroke:#94a3b8; }
.stats-badge { display:inline-flex; align-items:center; gap:6px; padding:4px 12px; border-radius:99px; font-size:10px; font-weight:700; background:#f1f5f9; color:#64748b; }
.system-warning { background:#fef3c7; border:1px solid #fde68a; border-radius:12px; padding:12px 16px; margin-bottom:16px; display:flex; align-items:center; gap:10px; font-size:12px; color:#92400e; }
</style>

<div class="role-edit-wrap">

{{-- Back button --}}
<a href="{{ route('roles.index') }}" class="btn-back">
    <svg width="12" height="12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"/>
    </svg>
    Retour aux rôles
</a>

{{-- Hero section --}}
<div class="role-hero">
    <div style="display:flex; align-items:center; gap:16px;">
        <div class="role-hero-icon">
            <svg width="26" height="26" fill="none" stroke="white" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
            </svg>
        </div>
        <div>
            <h1 class="role-hero-title">{{ $role->name }}</h1>
            <p class="role-hero-sub">Modifiez le nom et les permissions du rôle</p>
        </div>
    </div>
    <span class="role-hero-badge">{{ ucfirst($userRole) }}</span>
</div>

{{-- System role warning --}}
@if(in_array($role->name, ['admin', 'gestionnaire', 'formateur', 'stagiaire']))
<div class="system-warning">
    <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
    </svg>
    <span>Ce rôle est un rôle système. La modification de ses permissions peut affecter le fonctionnement de l'application.</span>
</div>
@endif

{{-- Errors --}}
@if($errors->any())
    <div class="flash-error">
        <ul style="margin:0; padding-left:16px;">
            @foreach($errors->all() as $e)<li>✕ {{ $e }}</li>@endforeach
        </ul>
    </div>
@endif

{{-- Form --}}
<div class="role-form-card">
    <form method="POST" action="{{ route('roles.update', $role) }}" id="role-form">
        @csrf
        @method('PATCH')

        {{-- Role name --}}
        <div class="role-form-section">
            <label class="role-form-label">
                <span style="color:#ef4444;">*</span> Nom du rôle
            </label>
            <input type="text" name="name" value="{{ old('name', $role->name) }}"
                   placeholder="ex: coordinateur, responsable pédagogique, assistant..."
                   class="role-input" required>
            <div style="font-size:10px; color:#94a3b8; margin-top:6px;">
                ⚡ Utilisez des lettres minuscules et des tirets (ex: responsable-pedagogique)
            </div>
        </div>

        {{-- Permissions section --}}
        <div class="role-form-section" style="border-bottom:none;">
            <div class="perms-header">
                <div>
                    <div class="role-form-label" style="margin-bottom:0;">Permissions</div>
                    <div style="font-size:11px; color:#64748b; margin-top:4px;">
                        Sélectionnez les droits à attribuer à ce rôle
                    </div>
                </div>
                <div class="perms-actions">
                    <button type="button" class="perms-action-btn select-all" onclick="toggleAllPermissions(true)">
                        ✅ Tout sélectionner
                    </button>
                    <button type="button" class="perms-action-btn deselect-all" onclick="toggleAllPermissions(false)">
                        ❌ Tout désélectionner
                    </button>
                </div>
            </div>

            {{-- Search filter --}}
            <div class="search-perms">
                <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
                <input type="text" id="search-perms" placeholder="Filtrer les permissions..." onkeyup="filterPermissions()">
            </div>

            <div id="permissions-container">
                @foreach($groupLabels as $groupKey => $group)
                    @php 
                        $g = $group;
                        $oldPermissions = old('permission', $rolePermissionsArray);
                    @endphp
                    <div class="perms-group" data-group="{{ $groupKey }}">
                        <div class="perms-group-header" onclick="toggleGroup(this)">
                            <div class="perms-group-icon" style="background:{{ $g['color'] }}20;">
                                <svg width="18" height="18" fill="none" stroke="{{ $g['color'] }}" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="{{ $g['icon'] }}"/>
                                </svg>
                            </div>
                            <span class="perms-group-title" style="color:{{ $g['color'] }};">{{ $g['label'] }}</span>
                            <span class="perms-group-count" style="background:{{ $g['color'] }}10; color:{{ $g['color'] }};" id="count-{{ $groupKey }}">
                                {{ count($g['perms']) }} permission(s)
                            </span>
                            <svg class="perms-group-toggle" width="14" height="14" fill="none" stroke="{{ $g['color'] }}" viewBox="0 0 24 24" style="transition:transform 0.2s;">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </div>
                        <div class="perms-group-body">
                            @foreach($g['perms'] as $permName => $permLabel)
                                <label class="permission-check" data-perm="{{ $permName }}" onclick="togglePermission(this, event)">
                                    <span class="permission-tick">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="4" d="M5 13l4 4L19 7"/>
                                        </svg>
                                    </span>
                                    <span class="permission-label">{{ $permLabel }}</span>
                                    <input type="checkbox" name="permission[]" value="{{ $permName }}"
                                           {{ in_array($permName, $oldPermissions) ? 'checked' : '' }}
                                           style="display:none;">
                                </label>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- Selected count --}}
            <div style="margin-top:20px; padding:12px 16px; background:#f8fafc; border-radius:12px; display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:10px;">
                <div class="stats-badge">
                    <span>📋</span>
                    <span id="selected-count">0</span>
                    <span>permission(s) sélectionnée(s)</span>
                </div>
            </div>
        </div>

        {{-- Submit buttons --}}
        <div style="padding:20px 28px; border-top:1px solid #f1f5f9; display:flex; gap:12px;">
            <a href="{{ route('roles.index') }}" class="btn-cancel">
                <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
                Annuler
            </a>
            <button type="submit" class="btn-submit">
                <svg width="14" height="14" fill="none" stroke="white" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                </svg>
                Enregistrer les modifications
            </button>
        </div>
    </form>
</div>

</div>

<script>
// Initialize all checkboxes style
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.permission-check').forEach(function(label) {
        const cb = label.querySelector('input');
        if (cb && cb.checked) {
            label.classList.add('selected');
        }
    });
    updateSelectedCount();
    
    // Open first group by default
    const firstGroup = document.querySelector('.perms-group');
    if (firstGroup) {
        const body = firstGroup.querySelector('.perms-group-body');
        const icon = firstGroup.querySelector('.perms-group-toggle');
        if (body && icon) {
            body.style.display = 'flex';
            icon.style.transform = 'rotate(0deg)';
        }
    }
});

function togglePermission(label, event) {
    event.stopPropagation();
    const cb = label.querySelector('input');
    cb.checked = !cb.checked;
    
    if (cb.checked) {
        label.classList.add('selected');
    } else {
        label.classList.remove('selected');
    }
    
    updateSelectedCount();
}

function toggleAllPermissions(checked) {
    document.querySelectorAll('.permission-check').forEach(function(label) {
        const cb = label.querySelector('input');
        cb.checked = checked;
        if (checked) {
            label.classList.add('selected');
        } else {
            label.classList.remove('selected');
        }
    });
    updateSelectedCount();
}

function updateSelectedCount() {
    const count = document.querySelectorAll('.permission-check input:checked').length;
    document.getElementById('selected-count').textContent = count;
}

function toggleGroup(header) {
    const body = header.nextElementSibling;
    const icon = header.querySelector('.perms-group-toggle');
    if (body.style.display === 'none' || getComputedStyle(body).display === 'none') {
        body.style.display = 'flex';
        icon.style.transform = 'rotate(0deg)';
    } else {
        body.style.display = 'none';
        icon.style.transform = 'rotate(-90deg)';
    }
}

function filterPermissions() {
    const searchTerm = document.getElementById('search-perms').value.toLowerCase();
    const groups = document.querySelectorAll('.perms-group');
    
    groups.forEach(group => {
        const labels = group.querySelectorAll('.permission-check');
        let hasVisible = false;
        
        labels.forEach(label => {
            const text = label.querySelector('.permission-label').textContent.toLowerCase();
            if (searchTerm === '' || text.includes(searchTerm)) {
                label.style.display = 'inline-flex';
                hasVisible = true;
            } else {
                label.style.display = 'none';
            }
        });
        
        if (searchTerm !== '') {
            const header = group.querySelector('.perms-group-header');
            const body = group.querySelector('.perms-group-body');
            if (hasVisible) {
                group.style.display = 'block';
                body.style.display = 'flex';
                if (header) {
                    const icon = header.querySelector('.perms-group-toggle');
                    if (icon) icon.style.transform = 'rotate(0deg)';
                }
            } else {
                group.style.display = 'none';
            }
        } else {
            group.style.display = 'block';
        }
    });
}
</script>

@endsection