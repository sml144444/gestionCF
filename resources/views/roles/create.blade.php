@extends('layouts.app')
@section('title', 'Nouveau rôle')
@section('page-title', 'Nouveau rôle')

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
                'emploi-view'            => '📅 Consulter son planning',
                'emploi-view-all-groups' => '👑 Consulter tous les groupes',
                'emploi-create'          => '➕ Créer une séance',
                'emploi-edit'            => '✏️ Modifier une séance',
                'emploi-delete'          => '🗑️ Supprimer une séance',
                'emploi-lien'            => '🔗 Gérer les liens de réunion',
                'emploi-change-module'   => '📚 Changer le module d\'une séance',
            ]
        ],
        'user' => [
            'label' => 'Utilisateurs',
            'icon'  => 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v2h5M7 20v-2a3 3 0 015.356-1.857M15 7a3 3 0 11-6 0 3 3 0 016 0z',
            'color' => '#16a34a', 'bg' => '#f0fdf4',
            'perms' => [
                'user-manage-formateur'    => '🎓 Gérer les comptes formateurs',
                'user-manage-gestionnaire' => '🏢 Gérer les comptes gestionnaires',
            ]
        ],
        'stagiaire' => [
            'label' => 'Stagiaires',
            'icon'  => 'M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z',
            'color' => '#0891b2', 'bg' => '#ecfeff',
            'perms' => [
                'stagiaire-list'   => '👥 Voir la liste des stagiaires',
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
                'groupe-list'   => '📋 Voir les groupes',
                'groupe-create' => '➕ Créer un groupe',
                'groupe-edit'   => '✏️ Modifier un groupe',
                'groupe-delete' => '🗑️ Supprimer un groupe',
            ]
        ],
        'salle' => [
            'label' => 'Salles',
            'icon'  => 'M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4',
            'color' => '#0f766e', 'bg' => '#f0fdfa',
            'perms' => [
                'salle-list'   => '📋 Voir les salles',
                'salle-create' => '➕ Créer une salle',
                'salle-edit'   => '✏️ Modifier une salle',
                'salle-delete' => '🗑️ Supprimer une salle',
            ]
        ],
        'edu' => [
            'label' => 'Import EDU',
            'icon'  => 'M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12',
            'color' => '#ea580c', 'bg' => '#fff7ed',
            'perms' => [
                'edu-import' => '📥 Importer des données EDU',
                'edu-view'   => '📋 Voir les imports',
            ]
        ],
        'role' => [
            'label' => 'Rôles & Permissions',
            'icon'  => 'M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z',
            'color' => '#dc2626', 'bg' => '#fff1f2',
            'perms' => [
                'role-list'   => '📋 Voir les rôles',
                'role-create' => '➕ Créer un rôle',
                'role-edit'   => '✏️ Modifier un rôle',
                'role-delete' => '🗑️ Supprimer un rôle',
            ]
        ],
        'reportation' => [
            'label' => 'Reportations',
            'icon'  => 'M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15',
            'color' => '#7c3aed', 'bg' => '#f5f3ff',
            'perms' => [
                'reportation-create' => '📝 Créer une reportation',
                'reportation-manage' => '⚙️ Gérer toutes les reportations',
            ]
        ],
        'reclamation' => [
            'label' => 'Réclamations',
            'icon'  => 'M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z',
            'color' => '#0f766e', 'bg' => '#f0fdfa',
            'perms' => [
                'reclamation-create' => '📝 Soumettre une réclamation',
                'reclamation-list'   => '📋 Voir ses réclamations',
                'reclamation-manage' => '⚙️ Gérer toutes les réclamations',
            ]
        ],
        'news' => [
            'label' => 'News & Événements',
            'icon'  => 'M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z',
            'color' => '#b45309', 'bg' => '#fefce8',
            'perms' => [
                'news-list'    => '📰 Voir les publications',
                'news-create'  => '✍️ Publier une actualité',
                'news-edit'    => '✏️ Modifier une publication',
                'news-delete'  => '🗑️ Supprimer une publication',
                'news-comment' => '💬 Commenter une publication',
                'news-like'    => '❤️ Liker une publication',
            ]
        ],
        'absence' => [
            'label' => 'Absences & Retards',
            'icon'  => 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z',
            'color' => '#be185d', 'bg' => '#fdf2f8',
            'perms' => [
                'absence-view'     => '👁️ Consulter ses absences',
                'absence-view-all' => '📋 Voir toutes les absences',
                'absence-justify'  => '✅ Justifier / modifier une absence',
            ]
        ],
    ];
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
.rc-wrap { font-family:'Segoe UI',system-ui,sans-serif; max-width:900px; margin:0 auto; }
.rc-back { display:inline-flex; align-items:center; gap:6px; font-size:12px; font-weight:600; color:#64748b; text-decoration:none; padding:8px 14px; border-radius:10px; background:white; border:1.5px solid #e2e8f0; margin-bottom:20px; transition:all .15s; }
.rc-back:hover { color:var(--accent-tx); border-color:var(--accent-bd); background:var(--accent-lt); }
.rc-hero { background:var(--accent-gr); border-radius:20px; padding:24px 28px; margin-bottom:24px; display:flex; align-items:center; gap:16px; position:relative; overflow:hidden; }
.rc-hero::after { content:''; position:absolute; right:-30px; top:-30px; width:160px; height:160px; border-radius:50%; background:rgba(255,255,255,0.06); pointer-events:none; }
.rc-hero-icon { width:48px; height:48px; border-radius:15px; background:rgba(255,255,255,0.15); display:flex; align-items:center; justify-content:center; flex-shrink:0; }
.rc-hero-title { font-size:18px; font-weight:800; color:white; margin:0; }
.rc-hero-sub { font-size:11px; color:rgba(255,255,255,0.75); margin-top:2px; }
.rc-card { background:white; border-radius:20px; border:1px solid #e2e8f0; overflow:hidden; }
.rc-name-field { padding:24px 24px 16px; border-bottom:1px solid #f1f5f9; }
.rc-label { display:block; font-size:12px; font-weight:700; color:#374151; margin-bottom:8px; }
.rc-input { width:100%; padding:10px 14px; border-radius:12px; border:1.5px solid #e2e8f0; font-size:13px; font-weight:500; color:#1e293b; outline:none; transition:border .15s; }
.rc-input:focus { border-color:var(--accent-bd); box-shadow:0 0 0 3px color-mix(in srgb, var(--accent) 10%, transparent); }
.rc-perm-group { border-bottom:1px solid #f1f5f9; }
.rc-perm-group:last-child { border-bottom:none; }
.rc-perm-group-header { padding:14px 24px; display:flex; align-items:center; justify-content:space-between; cursor:pointer; user-select:none; transition:background .1s; }
.rc-perm-group-header:hover { background:#fafafa; }
.rc-perm-group-label { display:flex; align-items:center; gap:10px; font-size:13px; font-weight:700; }
.rc-perm-group-icon { width:32px; height:32px; border-radius:10px; display:flex; align-items:center; justify-content:center; flex-shrink:0; }
.rc-perm-group-body { display:flex; flex-wrap:wrap; gap:8px; padding:4px 24px 16px; }
.rc-perm-item { display:flex; align-items:center; gap:8px; padding:8px 12px; border-radius:10px; border:1.5px solid #e2e8f0; cursor:pointer; transition:all .15s; background:white; }
.rc-perm-item:hover { border-color:var(--accent-bd); background:var(--accent-ltr); }
.rc-perm-item input[type=checkbox] { width:15px; height:15px; accent-color:var(--accent); cursor:pointer; }
.rc-perm-item label { font-size:12px; font-weight:600; color:#374151; cursor:pointer; }
.rc-footer { padding:20px 24px; border-top:1px solid #f1f5f9; display:flex; gap:12px; justify-content:flex-end; }
.btn-primary { display:inline-flex; align-items:center; gap:8px; padding:12px 24px; border-radius:12px; border:none; background:var(--accent-gr); color:white; font-size:14px; font-weight:700; cursor:pointer; transition:all .15s; box-shadow:0 4px 12px var(--accent-sh); }
.btn-primary:hover { opacity:.88; }
.btn-outline { display:inline-flex; align-items:center; gap:6px; padding:11px 20px; border-radius:12px; border:1.5px solid #e2e8f0; background:white; color:#64748b; font-size:13px; font-weight:600; cursor:pointer; transition:all .15s; text-decoration:none; }
.btn-outline:hover { border-color:var(--accent-bd); color:var(--accent-tx); background:var(--accent-lt); }
.rc-select-all { font-size:10px; font-weight:700; padding:4px 10px; border-radius:8px; border:1px solid #e2e8f0; background:white; color:#64748b; cursor:pointer; }
.rc-select-all:hover { background:#f1f5f9; }
.flash-err { padding:14px 18px; background:#fff1f2; border:1px solid #fecdd3; border-radius:14px; margin-bottom:16px; }
</style>

<div class="rc-wrap">
    <a href="{{ route('roles.index') }}" class="rc-back">
        <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        Retour aux rôles
    </a>

    @if($errors->any())
        <div class="flash-err">
            @foreach($errors->all() as $error)
                <p style="font-size:12px;color:#be123c;margin:0;">✕ {{ $error }}</p>
            @endforeach
        </div>
    @endif

    <div class="rc-hero">
        <div class="rc-hero-icon">
            <svg width="24" height="24" fill="none" stroke="white" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
        </div>
        <div>
            <h1 class="rc-hero-title">Créer un nouveau rôle</h1>
            <p class="rc-hero-sub">Définissez le nom et les permissions de ce rôle</p>
        </div>
    </div>

    <div class="rc-card">
        <form action="{{ route('roles.store') }}" method="POST">
            @csrf

            {{-- NOM --}}
            <div class="rc-name-field">
                <label class="rc-label" for="name">Nom du rôle <span style="color:#dc2626">*</span></label>
                <input
                    type="text"
                    id="name"
                    name="name"
                    class="rc-input"
                    value="{{ old('name') }}"
                    placeholder="ex: coordinateur, observateur..."
                    style="max-width:400px;"
                    required>
            </div>

            {{-- PERMISSIONS --}}
            <div style="padding:20px 24px 8px;">
                <div style="font-size:13px;font-weight:700;color:#1e293b;margin-bottom:4px;">Permissions</div>
                <div style="font-size:11px;color:#94a3b8;">Cochez les permissions à attribuer à ce rôle</div>
            </div>

            @foreach($groupLabels as $groupKey => $group)
                @php
                    $groupPerms = isset($permissions[$groupKey]) ? $permissions[$groupKey] : collect();
                @endphp
                @if($groupPerms->isNotEmpty() || !empty($group['perms']))
                <div class="rc-perm-group">
                    <div class="rc-perm-group-header" onclick="toggleGroup('{{ $groupKey }}')">
                        <div class="rc-perm-group-label">
                            <div class="rc-perm-group-icon" style="background:{{ $group['bg'] }};">
                                <svg width="18" height="18" fill="none" stroke="{{ $group['color'] }}" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="{{ $group['icon'] }}"/>
                                </svg>
                            </div>
                            <span style="color:{{ $group['color'] }};">{{ $group['label'] }}</span>
                        </div>
                        <div style="display:flex;align-items:center;gap:8px;">
                            <button type="button" class="rc-select-all" onclick="event.stopPropagation();selectAll('{{ $groupKey }}', true)">Tout</button>
                            <button type="button" class="rc-select-all" onclick="event.stopPropagation();selectAll('{{ $groupKey }}', false)">Aucun</button>
                            <svg id="chevron-{{ $groupKey }}" width="16" height="16" fill="none" stroke="#94a3b8" viewBox="0 0 24 24" style="transition:.2s;transform:rotate(180deg);">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </div>
                    </div>
                    <div id="group-{{ $groupKey }}" class="rc-perm-group-body">
                        @foreach($group['perms'] as $permName => $permLabel)
                            @php
                                $exists = $groupPerms->where('name', $permName)->isNotEmpty();
                            @endphp
                            @if($exists)
                                <div class="rc-perm-item" onclick="toggleCheck('cb-{{ str_replace(['-','.',' '], '_', $permName) }}')">
                                    <input type="checkbox"
                                        id="cb-{{ str_replace(['-','.',' '], '_', $permName) }}"
                                        name="permission[]"
                                        value="{{ $permName }}"
                                        data-group="{{ $groupKey }}"
                                        {{ in_array($permName, old('permission', [])) ? 'checked' : '' }}>
                                    <label for="cb-{{ str_replace(['-','.',' '], '_', $permName) }}">{{ $permLabel }}</label>
                                </div>
                            @endif
                        @endforeach
                    </div>
                </div>
                @endif
            @endforeach

            {{-- FOOTER --}}
            <div class="rc-footer">
                <a href="{{ route('roles.index') }}" class="btn-outline">Annuler</a>
                <button type="submit" class="btn-primary">
                    <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                    Créer le rôle
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function toggleGroup(key) {
    const body = document.getElementById('group-' + key);
    const chevron = document.getElementById('chevron-' + key);
    const isHidden = body.style.display === 'none';
    body.style.display = isHidden ? 'flex' : 'none';
    chevron.style.transform = isHidden ? 'rotate(180deg)' : 'rotate(0deg)';
}
function selectAll(key, checked) {
    document.querySelectorAll(`input[type=checkbox][data-group="${key}"]`).forEach(cb => cb.checked = checked);
}
function toggleCheck(id) {
    const cb = document.getElementById(id);
    if (cb) cb.checked = !cb.checked;
}
</script>
@endsection