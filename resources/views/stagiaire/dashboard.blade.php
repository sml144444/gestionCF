{{-- resources/views/stagiaire/dashboard.blade.php --}}
{{--
    Controller should pass:
    $stats = [
        'absences_count'     => AbsenceRetard::where('id_user', auth()->id())->where('type','absence')->count(),
        'retards_count'      => AbsenceRetard::where('id_user', auth()->id())->where('type','retard')->count(),
        'absences_injust'    => AbsenceRetard::where('id_user', auth()->id())->where('justifie', false)->count(),
        'cours_semaine'      => count of emplois this week for this stagiaire's groupe,
    ]
    $prochaines_seances = EmploiDuTemps upcoming this week for this stagiaire's groupe (limit 5)
    $derniers_documents  = Cours (ressources) latest for this stagiaire's groupe (limit 4)
--}}
@extends('layouts.app')
@section('title', 'Espace Stagiaire')
@section('page-title', 'Dashboard')

@section('content')

@php
    $user    = Auth::user();
    $groupe  = $user->groupe ?? null;
    $filiere = $groupe?->filiere ?? null;
@endphp

{{-- ══ Welcome Banner ══ --}}
<div class="bg-white rounded-2xl border border-slate-200 px-6 py-5 mb-6 shadow-sm">
    <div class="flex items-start justify-between">
        <div>
            <h2 class="text-lg font-semibold text-slate-800">Bonjour, {{ $user->name }} 👋</h2>
            <p class="text-sm text-slate-500 mt-1 flex flex-wrap items-center gap-x-3 gap-y-1">
                @if($filiere)
                    <span class="inline-flex items-center gap-1">
                        <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6z"/>
                        </svg>
                        <span class="font-medium text-slate-700">{{ $filiere->name }}</span>
                    </span>
                @endif
                @if($groupe)
                    <span class="inline-flex items-center gap-1">
                        <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857"/>
                        </svg>
                        <span class="font-medium text-slate-700">{{ $groupe->name }}</span>
                    </span>
                    <span class="text-[10px] font-semibold bg-blue-100 text-blue-700 px-2 py-0.5 rounded-full">
                        {{ $groupe->annee }}ème Année
                    </span>
                @endif
            </p>
        </div>
        <span class="text-xs text-slate-400 hidden sm:block">{{ now()->isoFormat('dddd D MMMM') }}</span>
    </div>
</div>

{{-- ══ Stats ══ --}}
<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">

    <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-sm hover:shadow-md transition-shadow">
        <div class="flex items-center justify-between mb-3">
            <div class="w-9 h-9 rounded-xl bg-blue-50 flex items-center justify-center">
                <svg class="w-5 h-5 text-[#1a5fa8]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
            </div>
            <span class="text-[10px] font-semibold text-blue-600 bg-blue-50 px-2 py-0.5 rounded-full">Semaine</span>
        </div>
        <p class="text-2xl font-bold text-slate-800">{{ $stats['cours_semaine'] ?? '—' }}</p>
        <p class="text-xs text-slate-400 mt-0.5 uppercase tracking-wide">Cours à venir</p>
    </div>

    <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-sm hover:shadow-md transition-shadow">
        <div class="flex items-center justify-between mb-3">
            <div class="w-9 h-9 rounded-xl bg-red-50 flex items-center justify-center">
                <svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            @if(($stats['absences_injust'] ?? 0) > 0)
                <span class="text-[10px] font-semibold text-red-600 bg-red-100 px-2 py-0.5 rounded-full">Non justif.</span>
            @endif
        </div>
        <p class="text-2xl font-bold text-slate-800">{{ $stats['absences_count'] ?? '—' }}</p>
        <p class="text-xs text-slate-400 mt-0.5 uppercase tracking-wide">Absences</p>
    </div>

    <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-sm hover:shadow-md transition-shadow">
        <div class="flex items-center justify-between mb-3">
            <div class="w-9 h-9 rounded-xl bg-amber-50 flex items-center justify-center">
                <svg class="w-5 h-5 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
        </div>
        <p class="text-2xl font-bold text-slate-800">{{ $stats['retards_count'] ?? '—' }}</p>
        <p class="text-xs text-slate-400 mt-0.5 uppercase tracking-wide">Retards</p>
    </div>

    <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-sm hover:shadow-md transition-shadow">
        <div class="flex items-center justify-between mb-3">
            <div class="w-9 h-9 rounded-xl bg-emerald-50 flex items-center justify-center">
                <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                </svg>
            </div>
        </div>
        <p class="text-2xl font-bold text-slate-800">—</p>
        <p class="text-xs text-slate-400 mt-0.5 uppercase tracking-wide">Notes disponibles</p>
    </div>

</div>

{{-- ══ Schedule + Documents ══ --}}
<div class="grid grid-cols-1 lg:grid-cols-3 gap-5">

    {{-- Programme de la semaine --}}
    <div class="lg:col-span-2 bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="px-5 py-4 border-b border-slate-100 flex items-center justify-between">
            <h3 class="text-sm font-semibold text-slate-700">Programme de la semaine</h3>
            <a href="{{ route('emplois.index') }}"
               class="text-xs text-slate-400 hover:text-slate-600 flex items-center gap-1">
                Voir tout
                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
            </a>
        </div>

        @if(isset($prochaines_seances) && $prochaines_seances->isNotEmpty())
            <div class="divide-y divide-slate-50">
                @foreach($prochaines_seances as $seance)
                @php
                    $isPast    = $seance->date_debut->isPast();
                    $isToday   = $seance->date_debut->isToday();
                @endphp
                <a href="{{ route('seances.show', $seance) }}"
                   class="flex items-center gap-4 px-5 py-3.5 hover:bg-slate-50 transition-colors {{ $isPast ? 'opacity-50' : '' }}">
                    {{-- Day badge --}}
                    <div class="w-10 text-center flex-shrink-0">
                        <p class="text-[10px] text-slate-400 uppercase">{{ $seance->date_debut->isoFormat('ddd') }}</p>
                        <p class="text-lg font-bold {{ $isToday ? 'text-[#1a5fa8]' : 'text-slate-700' }} leading-none">
                            {{ $seance->date_debut->format('d') }}
                        </p>
                        @if($isToday)
                            <div class="w-1 h-1 rounded-full bg-[#1a5fa8] mx-auto mt-0.5"></div>
                        @endif
                    </div>
                    {{-- Info --}}
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center gap-2">
                            <p class="text-sm font-semibold text-slate-800 truncate">
                                {{ $seance->module->name ?? 'Séance' }}
                            </p>
                            @if($isToday)
                                <span class="text-[10px] font-semibold bg-[#1a5fa8] text-white px-2 py-0.5 rounded-full flex-shrink-0">Aujourd'hui</span>
                            @endif
                        </div>
                        <p class="text-xs text-slate-400">
                            {{ $seance->date_debut->format('H:i') }} – {{ $seance->date_fin->format('H:i') }}
                            @if($seance->salle) · {{ $seance->salle->name }} @endif
                            @if($seance->mode === 'distance')
                                <span class="inline-flex items-center gap-1 text-blue-500">
                                    · 🌐 Distance
                                </span>
                            @endif
                        </p>
                    </div>
                    {{-- Formateur --}}
                    @php
                        $formateur = $seance->remplacant ?? $seance->gestionnaire;
                    @endphp
                    @if($formateur)
                    <div class="hidden sm:flex items-center gap-1.5 flex-shrink-0">
                        <div class="w-6 h-6 rounded-full bg-[#1a4f8a] flex items-center justify-center text-[9px] font-bold text-white">
                            {{ strtoupper(substr($formateur->name, 0, 1)) }}
                        </div>
                        <span class="text-[11px] text-slate-400 max-w-[80px] truncate">{{ $formateur->name }}</span>
                    </div>
                    @endif
                </a>
                @endforeach
            </div>
        @else
            <div class="py-12 text-center">
                <svg class="w-10 h-10 text-slate-200 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                          d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
                <p class="text-xs text-slate-400">Aucun cours programmé cette semaine</p>
                <p class="text-[11px] text-slate-300 mt-1">L'emploi du temps sera disponible prochainement</p>
            </div>
        @endif
    </div>

    {{-- Right column: quick links + recent documents --}}
    <div class="flex flex-col gap-5">

        {{-- Quick Links --}}
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
            <div class="px-5 py-4 border-b border-slate-100">
                <h3 class="text-sm font-semibold text-slate-700">Accès rapide</h3>
            </div>
            <div class="p-4 space-y-2">
                @foreach([
                    ['Emploi du temps',    route('emplois.index'),  'M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z',   'text-blue-600 bg-blue-50 hover:bg-blue-100'],
                    ['Mes absences',       route('absences.index'), 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z',                                                'text-red-500 bg-red-50 hover:bg-red-100'],
                    ['Mes réclamations',   route('reclamations.index'),'M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z', 'text-slate-600 bg-slate-50 hover:bg-slate-100'],
                    ['News / Événements',  route('news.index'),    'M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z', 'text-slate-600 bg-slate-50 hover:bg-slate-100'],
                ] as [$label, $route, $icon, $colorClass])
                <a href="{{ $route }}"
                   class="flex items-center gap-3 rounded-xl {{ $colorClass }} px-4 py-2.5 transition-colors">
                    <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $icon }}"/>
                    </svg>
                    <span class="text-xs font-semibold">{{ $label }}</span>
                    <svg class="w-3.5 h-3.5 ml-auto opacity-40" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </a>
                @endforeach
            </div>
        </div>

        {{-- Derniers documents --}}
        @if(isset($derniers_documents) && $derniers_documents->isNotEmpty())
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
            <div class="px-5 py-4 border-b border-slate-100">
                <h3 class="text-sm font-semibold text-slate-700">Documents récents</h3>
            </div>
            <div class="divide-y divide-slate-50">
                @foreach($derniers_documents as $doc)
                <div class="flex items-center gap-3 px-4 py-3">
                    <div class="w-8 h-8 rounded-lg flex items-center justify-center flex-shrink-0
                        {{ $doc->lien ? 'bg-blue-50' : 'bg-red-50' }}">
                        @if($doc->lien)
                            <svg class="w-4 h-4 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101"/>
                            </svg>
                        @else
                            <svg class="w-4 h-4 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                            </svg>
                        @endif
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-xs font-semibold text-slate-700 truncate">{{ $doc->titre }}</p>
                        <p class="text-[10px] text-slate-400">{{ $doc->created_at->diffForHumans() }}</p>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endif

    </div>
</div>

@endsection