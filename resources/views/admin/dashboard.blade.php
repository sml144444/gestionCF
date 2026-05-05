@extends('layouts.app')
@section('title', 'Espace Admin')
@section('page-title', 'Dashboard Admin')

@section('content')

{{-- ══ Welcome Banner ══ --}}
<div class="relative overflow-hidden rounded-2xl mb-6"
     style="background: linear-gradient(135deg, #065f46 0%, #0a6640 60%, #059669 100%);">
    <div class="absolute inset-0"
         style="background-image: radial-gradient(circle at 85% 15%, rgba(255,255,255,0.10) 0%, transparent 50%);"></div>
    <div class="relative px-6 py-6 flex items-center justify-between">
        <div>
            <p class="text-emerald-300 text-xs font-semibold uppercase tracking-widest mb-1">Administrateur</p>
            <h2 class="text-2xl font-bold text-white">Bonjour, {{ Auth::user()->name }} 👋</h2>
            <p class="text-emerald-200 text-sm mt-1">Accès complet à la plateforme OFPPT</p>
        </div>
        <div class="hidden sm:flex items-center gap-2 bg-white/15 hover:bg-white/25 rounded-xl px-4 py-2.5 text-sm text-white font-semibold transition-colors backdrop-blur-sm">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
            </svg>
            {{ now()->isoFormat('dddd D MMMM YYYY') }}
        </div>
    </div>
</div>

{{-- ══ Primary Stats ══ --}}
<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-4">

    @foreach([
        ['Utilisateurs',  $stats['total_users']  ?? '—', '#065f46', '#ecfdf5', 'M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z', 'Total'],
        ['Stagiaires',    $stats['stagiaires']   ?? '—', '#1a4f8a', '#eff6ff', 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z', null],
        ['Formateurs',    $stats['formateurs']   ?? '—', '#0d766a', '#f0fdfa', 'M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z', null],
        ['Comptes EDU',   $stats['edu_pending']  ?? '—', '#b45309', '#fffbeb', 'M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12', ($stats['edu_pending'] ?? 0) > 0 ? 'En attente' : null],
    ] as [$label, $val, $color, $bg, $icon, $badge])
    <div class="bg-white rounded-2xl border border-slate-100 p-5 shadow-sm hover:shadow-md transition-all hover:-translate-y-0.5">
        <div class="flex items-start justify-between mb-4">
            <div class="w-10 h-10 rounded-xl flex items-center justify-center" style="background:{{ $bg }};">
                <svg class="w-5 h-5" fill="none" stroke="{{ $color }}" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $icon }}"/>
                </svg>
            </div>
            @if($badge)
                <span class="text-[9px] font-bold uppercase tracking-wide px-2 py-1 rounded-full"
                      style="background:{{ $bg }}; color:{{ $color }};">{{ $badge }}</span>
            @endif
        </div>
        <p class="text-3xl font-black text-slate-800 leading-none">{{ $val }}</p>
        <p class="text-[11px] text-slate-400 mt-1.5 uppercase tracking-widest font-medium">{{ $label }}</p>
    </div>
    @endforeach

</div>

{{-- ══ Secondary Stats ══ --}}
<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
    @foreach([
        ['Filières',      $stats['filieres']         ?? '—', '#065f46', '#ecfdf5', 'M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z'],
        ['Groupes',       $stats['groupes']          ?? '—', '#1a4f8a', '#eff6ff', 'M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10'],
        ['Gestionnaires', $stats['gestionnaires']    ?? '—', '#b45309', '#fffbeb', 'M5.121 17.804A13.937 13.937 0 0112 16c2.5 0 4.847.655 6.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0zm6 2a9 9 0 11-18 0 9 9 0 0118 0z'],
        ['Réclamations',  $stats['reclamations_open']?? '—', '#dc2626', '#fff1f2', 'M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z'],
    ] as [$label, $val, $color, $bg, $icon])
    <div class="bg-white rounded-2xl border border-slate-100 p-5 shadow-sm hover:shadow-md transition-all hover:-translate-y-0.5">
        <div class="flex items-start justify-between mb-4">
            <div class="w-10 h-10 rounded-xl flex items-center justify-center" style="background:{{ $bg }};">
                <svg class="w-5 h-5" fill="none" stroke="{{ $color }}" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $icon }}"/>
                </svg>
            </div>
        </div>
        <p class="text-3xl font-black text-slate-800 leading-none">{{ $val }}</p>
        <p class="text-[11px] text-slate-400 mt-1.5 uppercase tracking-widest font-medium">{{ $label }}</p>
    </div>
    @endforeach
</div>

{{-- ══ Quick Actions + System ══ --}}
<div class="grid grid-cols-1 lg:grid-cols-3 gap-5">

    {{-- Quick Actions --}}
    <div class="lg:col-span-2 bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
        <div class="px-5 py-4 border-b border-slate-50 flex items-center gap-3">
            <div class="w-7 h-7 rounded-lg flex items-center justify-center" style="background:#ecfdf5;">
                <svg class="w-4 h-4" fill="none" stroke="#065f46" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M13 10V3L4 14h7v7l9-11h-7z"/>
                </svg>
            </div>
            <h3 class="text-sm font-bold text-slate-700">Actions rapides</h3>
        </div>
        <div class="p-4 grid grid-cols-2 sm:grid-cols-3 gap-3">
            @foreach([
                ['Gestion utilisateurs', route('users.management.index'),   'M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z', '#065f46', '#ecfdf5'],
                ['Import EDU',           route('edu-import.index'),         'M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12',                                                  '#1a4f8a', '#eff6ff'],
                ['Emploi du temps',      route('emplois.index'),            'M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z',                        '#0d766a', '#f0fdfa'],
                ['Filières',             route('filieres.index'),           'M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z', '#7c3aed', '#f5f3ff'],
                ['Groupes',              route('groupes.index'),            'M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10', '#b45309', '#fffbeb'],
                ['Rôles & Permissions',  route('roles.index'),             'M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z', '#475569', '#f8fafc'],
            ] as [$label, $route, $icon, $color, $bg])
            <a href="{{ $route }}"
               class="group flex flex-col items-center gap-3 rounded-xl p-4 text-center transition-all hover:shadow-sm hover:-translate-y-0.5"
               style="background:{{ $bg }};">
                <div class="w-10 h-10 rounded-xl bg-white shadow-sm flex items-center justify-center
                            group-hover:scale-110 transition-transform flex-shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="{{ $color }}" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $icon }}"/>
                    </svg>
                </div>
                <span class="text-xs font-bold leading-tight" style="color:{{ $color }};">{{ $label }}</span>
            </a>
            @endforeach
        </div>
    </div>

    {{-- System Info + Profile --}}
    <div class="flex flex-col gap-5">

        {{-- System Info --}}
        <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
            <div class="px-5 py-4 border-b border-slate-50 flex items-center gap-3">
                <div class="w-7 h-7 rounded-lg flex items-center justify-center" style="background:#ecfdf5;">
                    <svg class="w-4 h-4" fill="none" stroke="#065f46" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M9 3H5a2 2 0 00-2 2v4m6-6h10a2 2 0 012 2v4M9 3v18m0 0h10a2 2 0 002-2V9M9 21H5a2 2 0 01-2-2V9m0 0h18"/>
                    </svg>
                </div>
                <h3 class="text-sm font-bold text-slate-700">Système</h3>
            </div>
            <div class="px-5 py-4 space-y-3">
                @foreach([
                    ['Laravel',        app()->version()],
                    ['PHP',            PHP_VERSION],
                    ['Environnement',  ucfirst(app()->environment())],
                    ['Connexion',      'Maintenant'],
                ] as [$k, $v])
                <div class="flex items-center justify-between">
                    <span class="text-[11px] text-slate-400 uppercase tracking-widest font-medium">{{ $k }}</span>
                    <span class="text-xs font-bold
                        {{ $k === 'Environnement' && app()->isProduction() ? 'text-emerald-600' : '' }}
                        {{ $k === 'Environnement' && !app()->isProduction() ? 'text-amber-600' : '' }}
                        {{ !in_array($k, ['Environnement']) ? 'text-slate-700' : '' }}">
                        {{ $v }}
                    </span>
                </div>
                @endforeach
            </div>
        </div>

        {{-- Admin Profile Card --}}
        <div class="rounded-2xl p-5 text-white" style="background: linear-gradient(135deg, #065f46, #059669);">
            <div class="flex items-center gap-3 mb-3">
                <div class="w-10 h-10 rounded-xl bg-white/20 flex items-center justify-center text-sm font-black">
                    {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                </div>
                <div class="min-w-0">
                    <p class="text-xs font-bold text-white truncate">{{ Auth::user()->name }}</p>
                    <p class="text-[10px] text-emerald-200 truncate">{{ Auth::user()->email }}</p>
                </div>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('profile.show') }}"
                   class="flex-1 flex items-center justify-center gap-1.5 bg-white/15 hover:bg-white/25 rounded-xl py-2 text-xs font-bold text-white transition-colors">
                    Mon profil
                </a>
                <a href="{{ route('news.index') }}"
                   class="flex-1 flex items-center justify-center gap-1.5 bg-white/15 hover:bg-white/25 rounded-xl py-2 text-xs font-bold text-white transition-colors">
                    News
                </a>
            </div>
        </div>

    </div>
</div>

@endsection