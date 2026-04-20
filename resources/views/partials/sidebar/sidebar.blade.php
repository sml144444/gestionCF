{{-- resources/views/partials/sidebar/sidebar.blade.php --}}
{{-- Une seule sidebar — affiche les liens selon les permissions Spatie de l'utilisateur --}}

@php
    $user = Auth::user();
    $role = $user->role;

    // Dashboard route par rôle
    $dashRoute = match($role) {
        'admin'        => route('admin.dashboard'),
        'gestionnaire' => route('gestionnaire.dashboard'),
        'formateur'    => route('formateur.dashboard'),
        'stagiaire'    => route('stagiaire.dashboard'),
        default        => '/',
    };
@endphp

{{-- ════ PRINCIPAL ════ --}}
<x-nav-section label="Principal" />

<x-nav-item
    route="{{ $dashRoute }}"
    label="Dashboard"
    icon="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />

{{-- Emploi du temps — visible à tous ceux qui ont emploi-view --}}
@can('emploi-view')
<x-nav-item
    route="{{ route('emplois.index') }}"
    label="Emploi du temps"
    icon="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
@endcan

{{-- Mes cours — formateur et stagiaire --}}
@if(in_array($role, ['formateur', 'stagiaire']))
<x-nav-item
    route="#"
    label="Mes cours"
    icon="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
@endif


{{-- ════ ADMINISTRATION ════ --}}
{{-- Section visible uniquement si l'utilisateur a au moins une permission admin --}}
@if($user->can('user-list') || $user->can('edu-import') || $user->can('role-list') || $user->can('groupe-list'))
<x-nav-section label="Administration" />
@endif

{{-- Gestion utilisateurs --}}
@can('user-list')
<x-nav-item
    route="{{ route('users.management.index') }}"
    label="Gestion utilisateurs"
    icon="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
@endcan

{{-- Rôles & Permissions --}}
@can('role-list')
<x-nav-item
    route="{{ route('roles.index') }}"
    label="Rôles & Permissions"
    icon="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
@endcan

{{-- Import EDU --}}
@can('edu-import')
<x-nav-item
    route="{{ route('edu-import.index') }}"
    label="Import EDU"
    icon="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
@endcan


{{-- ════ GESTION ════ --}}
{{-- Section visible pour gestionnaire et admin --}}
@can('groupe-list')
<x-nav-section label="Gestion" />

@can('stagiaire-list')
<x-nav-item
    route="{{ route('stagiaire.index') }}"
    label="Stagiaires"
    icon="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
@endcan

<x-nav-item route="{{ route('modules.index') }}" label="Modules"
    icon="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />

<x-nav-item
    route="{{ route('salles.index') }}"
    label="Salles"
    icon="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />

<x-nav-item
    route="{{ route('filieres.index') }}"
    label="Filières"
    icon="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />

<x-nav-item
    route="{{ route('groupes.index') }}"
    label="Groupes"
    icon="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
@endcan


{{-- ════ FORMATEUR — Saisie ════ --}}
@if($role === 'formateur')
<x-nav-section label="Saisie" />
<x-nav-item route="#" label="Saisir notes"
    icon="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
<x-nav-item route="#" label="Saisir absences"
    icon="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
<x-nav-item route="#" label="Contrôles"
    icon="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
@endif


{{-- ════ STAGIAIRE — Évaluation ════ --}}
@if($role === 'stagiaire')
<x-nav-section label="Évaluation" />
<x-nav-item route="#" label="Mes notes"
    icon="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
<x-nav-item route="#" label="Absences"
    icon="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
<x-nav-item route="#" label="Bulletin"
    icon="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
<x-nav-item route="#" label="Choix option"
    icon="M9 5l7 7-7 7" />
@endif


{{-- ════ TRAITEMENT ════ --}}
<x-nav-section label="Traitement" />

{{-- Réclamations : admin/gestionnaire → toutes ; stagiaire → les siennes --}}
@can('reclamation-manage')
<x-nav-item
    route="{{ route('reclamations.index') }}"
    label="Réclamations"
    icon="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z" />
@endcan

@can('reclamation-list')
@cannot('reclamation-manage')
<x-nav-item
    route="{{ route('reclamations.index') }}"
    label="Mes réclamations"
    icon="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z" />
@endcannot
@endcan

{{-- Reportations --}}
@can('reportation-manage')
<x-nav-item
    route="{{ route('reportations.index') }}"
    label="Reportations"
    icon="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
@endcan

@can('reportation-create')
<x-nav-item
    route="{{ route('reportations.my') }}"
    label="Mes reportations"
    icon="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
@endcan

<x-nav-item route="#" label="News / Événements"
    icon="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z" />