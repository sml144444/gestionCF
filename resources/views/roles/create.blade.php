@extends('layouts.app')
@section('title', 'Nouveau rôle')
@section('page-title', 'Nouveau rôle')

@section('content')
<div style="font-family:'Segoe UI',system-ui,sans-serif; max-width:680px;">

{{-- BACK + TITLE --}}
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
    <div>
        <h1 style="font-size:18px; font-weight:800; color:#0f172a; margin:0;">Créer un rôle</h1>
        <p style="font-size:11px; color:#64748b; margin:2px 0 0;">Définissez un nom et sélectionnez les permissions</p>
    </div>
</div>

@if($errors->any())
    <div style="margin-bottom:16px; padding:12px 16px; border-radius:12px; font-size:12px;
                background:#fff1f2; border:1px solid #fecdd3; color:#be123c;">
        <ul style="margin:0; padding-left:16px;">
            @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
        </ul>
    </div>
@endif

<div style="background:white; border-radius:20px; border:1px solid #e2e8f0;
            box-shadow:0 2px 12px rgba(0,0,0,0.06); overflow:hidden;">

    <form method="POST" action="{{ route('roles.store') }}">
        @csrf

        <div style="padding:22px 24px; border-bottom:1px solid #f1f5f9;">
            <label style="display:block; font-size:9px; font-weight:800; color:#94a3b8;
                          letter-spacing:1.5px; text-transform:uppercase; margin-bottom:8px;">
                Nom du rôle
            </label>
            <input type="text" name="name" value="{{ old('name') }}"
                   placeholder="ex: coordinateur"
                   style="width:100%; height:44px; padding:0 14px; border-radius:12px;
                          border:1.5px solid #e2e8f0; background:#f8fafc; font-size:14px;
                          font-weight:600; color:#1e293b; outline:none; box-sizing:border-box;"
                   onfocus="this.style.borderColor='#0a6640'; this.style.background='white';"
                   onblur="this.style.borderColor='#e2e8f0'; this.style.background='#f8fafc';"
                   required>
        </div>

        <div style="padding:22px 24px;">
            <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:16px;">
                <label style="font-size:9px; font-weight:800; color:#94a3b8; letter-spacing:1.5px; text-transform:uppercase;">
                    Permissions
                </label>
                <div style="display:flex; gap:8px;">
                    <button type="button" onclick="toggleAll(true)"
                            style="font-size:10px; font-weight:600; color:#0a6640; background:#e8f5ee;
                                   padding:4px 10px; border-radius:8px; border:none; cursor:pointer;">
                        Tout sélectionner
                    </button>
                    <button type="button" onclick="toggleAll(false)"
                            style="font-size:10px; font-weight:600; color:#64748b; background:#f1f5f9;
                                   padding:4px 10px; border-radius:8px; border:none; cursor:pointer;">
                        Tout désélectionner
                    </button>
                </div>
            </div>

            @php
                $groupLabels = [
                    'emploi' => [
                        'label' => 'Emploi du temps',
                        'icon'  => 'M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z',
                        'color' => '#2563eb', 'bg' => '#eff6ff',
                    ],
                    'user' => [
                        'label' => 'Utilisateurs',
                        'icon'  => 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v2h5M7 20v-2a3 3 0 015.356-1.857M15 7a3 3 0 11-6 0 3 3 0 016 0z',
                        'color' => '#16a34a', 'bg' => '#f0fdf4',
                    ],
                    'stagiaire' => [
                        'label' => 'Stagiaires',
                        'icon'  => 'M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z',
                        'color' => '#0891b2', 'bg' => '#ecfeff',
                    ],
                    'groupe' => [
                        'label' => 'Groupes & Filières',
                        'icon'  => 'M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10',
                        'color' => '#9333ea', 'bg' => '#fdf4ff',
                    ],
                    'edu' => [
                        'label' => 'Import EDU',
                        'icon'  => 'M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12',
                        'color' => '#ea580c', 'bg' => '#fff7ed',
                    ],
                    'role' => [
                        'label' => 'Rôles & Permissions',
                        'icon'  => 'M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z',
                        'color' => '#dc2626', 'bg' => '#fff1f2',
                    ],
                    'reportation' => [
                        'label' => 'Reportations',
                        'icon'  => 'M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15',
                        'color' => '#7c3aed', 'bg' => '#f5f3ff',
                    ],
                ];

                $customLabels = [
                    // Emploi du temps
                    'emploi-view'             => '📅 Consulter son planning',
                    'emploi-view-all-groups'  => '👑 Consulter tous les groupes',
                    'emploi-create'           => '➕ Créer une séance',
                    'emploi-edit'             => '✏️ Modifier une séance',
                    'emploi-delete'           => '🗑️ Supprimer une séance',
                    'emploi-lien'             => '🔗 Gérer les liens de réunion',
                    'emploi-change-module'    => '📚 Changer le module d\'une séance',
                    // Utilisateurs
                    'user-list'               => '📋 Voir la liste des utilisateurs',
                    'user-create'             => '➕ Créer un utilisateur',
                    'user-edit'               => '✏️ Modifier un utilisateur',
                    'user-delete'             => '🗑️ Supprimer un utilisateur',
                    // Stagiaires
                    'stagiaire-list'          => '👥 Voir la liste des stagiaires',
                    // Groupes & Filières
                    'groupe-list'             => '📋 Voir les groupes',
                    'groupe-create'           => '➕ Créer un groupe',
                    'groupe-edit'             => '✏️ Modifier un groupe',
                    'groupe-delete'           => '🗑️ Supprimer un groupe',
                    // Import EDU
                    'edu-import'              => '📥 Importer des données EDU',
                    'edu-view'                => '📋 Voir les imports',
                    // Rôles & Permissions
                    'role-list'               => '📋 Voir les rôles',
                    'role-create'             => '➕ Créer un rôle',
                    'role-edit'               => '✏️ Modifier un rôle',
                    'role-delete'             => '🗑️ Supprimer un rôle',
                    // Reportations
                    'reportation-create'      => '📋 Demander un report de séance',
                    'reportation-manage'      => '✅ Gérer les reportations (accepter/refuser)',
                ];
            @endphp

            <div style="display:flex; flex-direction:column; gap:12px;">
            @foreach($permissions as $group => $perms)
                @php $g = $groupLabels[$group] ?? ['label'=>ucfirst($group),'icon'=>'M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z','color'=>'#64748b','bg'=>'#f8fafc']; @endphp
                <div style="border:1px solid #e2e8f0; border-radius:14px; overflow:hidden;">
                    <div style="padding:10px 16px; background:{{ $g['bg'] }};
                                display:flex; align-items:center; justify-content:space-between;">
                        <div style="display:flex; align-items:center; gap:8px;">
                            <div style="width:28px; height:28px; border-radius:8px; background:{{ $g['color'] }};
                                        display:flex; align-items:center; justify-content:center; flex-shrink:0;">
                                <svg width="13" height="13" fill="none" stroke="white" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $g['icon'] }}"/>
                                </svg>
                            </div>
                            <span style="font-size:12px; font-weight:700; color:{{ $g['color'] }};">{{ $g['label'] }}</span>
                        </div>
                        <button type="button"
                                onclick="toggleGroup('{{ $group }}', '{{ $g['bg'] }}', '{{ $g['color'] }}')"
                                style="font-size:9px; font-weight:700; color:{{ $g['color'] }};
                                       background:white; padding:3px 9px; border-radius:6px;
                                       border:1px solid {{ $g['color'] }}30; cursor:pointer;">
                            Tout
                        </button>
                    </div>
                    <div style="padding:12px 16px; display:flex; flex-wrap:wrap; gap:8px; background:white;">
                        @foreach($perms as $perm)
                            @php
                                $aLabel    = $customLabels[$perm->name] ?? ucfirst(str_replace('-', ' ', $perm->name));
                                $isChecked = in_array($perm->name, old('permission', []));
                            @endphp
                            <label style="display:inline-flex; align-items:center; gap:7px; cursor:pointer;
                                          padding:7px 12px; border-radius:10px;
                                          border:1.5px solid {{ $isChecked ? $g['color'].'60' : '#e2e8f0' }};
                                          background:{{ $isChecked ? $g['bg'] : 'white' }};
                                          transition:all 0.15s; user-select:none;">
                                <input type="checkbox" name="permission[]" value="{{ $perm->name }}"
                                       class="perm-cb perm-cb-{{ $group }}"
                                       {{ $isChecked ? 'checked' : '' }}
                                       data-bg="{{ $g['bg'] }}" data-color="{{ $g['color'] }}"
                                       style="display:none;"
                                       onchange="stylePermLabel(this)">
                                <span class="perm-tick"
                                      style="width:16px; height:16px; border-radius:5px; flex-shrink:0;
                                             background:{{ $isChecked ? $g['color'] : 'white' }};
                                             border:1.5px solid {{ $isChecked ? $g['color'] : '#cbd5e1' }};
                                             display:flex; align-items:center; justify-content:center; transition:all 0.15s;">
                                    @if($isChecked)
                                    <svg width="9" height="9" fill="none" stroke="white" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3.5" d="M5 13l4 4L19 7"/>
                                    </svg>
                                    @endif
                                </span>
                                <span style="font-size:11px; font-weight:600; color:{{ $isChecked ? $g['color'] : '#475569' }};">
                                    {{ $aLabel }}
                                </span>
                            </label>
                        @endforeach
                    </div>
                </div>
            @endforeach
            </div>
        </div>

        <div style="padding:18px 24px; border-top:1px solid #f1f5f9; display:flex; gap:10px;">
            <a href="{{ route('roles.index') }}"
               style="flex:1; height:44px; border-radius:12px; border:1.5px solid #e2e8f0;
                      background:white; font-size:13px; font-weight:600; color:#64748b;
                      text-decoration:none; display:flex; align-items:center; justify-content:center;">
                Annuler
            </a>
            <button type="submit"
                    style="flex:1; height:44px; border-radius:12px; border:none;
                           background:#0a6640; font-size:13px; font-weight:700; color:white;
                           cursor:pointer; box-shadow:0 4px 12px rgba(10,102,64,0.3);">
                Créer le rôle
            </button>
        </div>
    </form>
</div>

</div>

<script>
function stylePermLabel(cb) {
    const bg    = cb.dataset.bg;
    const color = cb.dataset.color;
    const label = cb.closest('label');
    const tick  = label.querySelector('.perm-tick');
    const text  = label.querySelector('span:last-child');
    if (cb.checked) {
        label.style.background  = bg;
        label.style.borderColor = color + '60';
        tick.style.background   = color;
        tick.style.borderColor  = color;
        tick.innerHTML = '<svg width="9" height="9" fill="none" stroke="white" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3.5" d="M5 13l4 4L19 7"/></svg>';
        text.style.color = color;
    } else {
        label.style.background  = 'white';
        label.style.borderColor = '#e2e8f0';
        tick.style.background   = 'white';
        tick.style.borderColor  = '#cbd5e1';
        tick.innerHTML          = '';
        text.style.color        = '#475569';
    }
}

function toggleGroup(group, bg, color) {
    const cbs = document.querySelectorAll('.perm-cb-' + group);
    const anyUnchecked = [...cbs].some(cb => !cb.checked);
    cbs.forEach(cb => {
        cb.checked = anyUnchecked;
        cb.dataset.bg    = bg;
        cb.dataset.color = color;
        cb.dispatchEvent(new Event('change'));
    });
}

function toggleAll(check) {
    document.querySelectorAll('.perm-cb').forEach(cb => {
        cb.checked = check;
        cb.dispatchEvent(new Event('change'));
    });
}
</script>
@endsection