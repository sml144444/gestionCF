{{-- resources/views/formateur/dashboard.blade.php --}}
{{--
    Controller should pass:
    $stats = [
        'seances_semaine'      => count of emplois this week for this formateur,
        'modules_count'        => Module::where('id_user', auth()->id())->count(),
        'groupes_count'        => distinct groupes count from modules,
        'reportations_pending' => Reportation::where('id_user', auth()->id())->where('statut','en_attente')->count(),
    ]
    $prochaines_seances = EmploiDuTemps upcoming (limit 5) for this formateur
--}}
@extends('layouts.app')
@section('title', 'Espace Formateur')
@section('page-title', 'Dashboard Formateur')

@section('content')

{{-- ══ Welcome Banner ══ --}}
<div class="bg-gradient-to-r from-[#1a4f8a] to-[#1a6fa8] rounded-2xl px-6 py-5 mb-6 shadow-sm flex items-center justify-between">
    <div>
        <h2 class="text-lg font-semibold text-white">Bonjour, {{ Auth::user()->name }} 👋</h2>
        <p class="text-sm text-blue-100 mt-0.5">
            Spécialité :
            <span class="font-semibold text-white">{{ Auth::user()->specialite ?? '—' }}</span>
        </p>
    </div>
    <a href="{{ route('emplois.index') }}"
       class="hidden sm:flex items-center gap-2 bg-white/15 hover:bg-white/25 rounded-xl px-4 py-2 text-sm text-white font-medium transition-colors">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
        </svg>
        Mon emploi du temps
    </a>
</div>

{{-- ══ Stats ══ --}}
<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">

    <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-sm hover:shadow-md transition-shadow">
        <div class="flex items-center justify-between mb-3">
            <div class="w-9 h-9 rounded-xl bg-blue-50 flex items-center justify-center">
                <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
            </div>
            <span class="text-[10px] font-semibold text-blue-600 bg-blue-50 px-2 py-0.5 rounded-full">Semaine</span>
        </div>
        <p class="text-2xl font-bold text-slate-800">{{ $stats['seances_semaine'] ?? '—' }}</p>
        <p class="text-xs text-slate-400 mt-0.5 uppercase tracking-wide">Séances</p>
    </div>

    <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-sm hover:shadow-md transition-shadow">
        <div class="flex items-center justify-between mb-3">
            <div class="w-9 h-9 rounded-xl bg-teal-50 flex items-center justify-center">
                <svg class="w-5 h-5 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                </svg>
            </div>
        </div>
        <p class="text-2xl font-bold text-slate-800">{{ $stats['modules_count'] ?? '—' }}</p>
        <p class="text-xs text-slate-400 mt-0.5 uppercase tracking-wide">Modules assignés</p>
    </div>

    <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-sm hover:shadow-md transition-shadow">
        <div class="flex items-center justify-between mb-3">
            <div class="w-9 h-9 rounded-xl bg-violet-50 flex items-center justify-center">
                <svg class="w-5 h-5 text-violet-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                </svg>
            </div>
        </div>
        <p class="text-2xl font-bold text-slate-800">{{ $stats['groupes_count'] ?? '—' }}</p>
        <p class="text-xs text-slate-400 mt-0.5 uppercase tracking-wide">Groupes</p>
    </div>

    <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-sm hover:shadow-md transition-shadow">
        <div class="flex items-center justify-between mb-3">
            <div class="w-9 h-9 rounded-xl bg-orange-50 flex items-center justify-center">
                <svg class="w-5 h-5 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                </svg>
            </div>
            @if(($stats['reportations_pending'] ?? 0) > 0)
                <span class="text-[10px] font-semibold text-orange-600 bg-orange-100 px-2 py-0.5 rounded-full">En attente</span>
            @endif
        </div>
        <p class="text-2xl font-bold text-slate-800">{{ $stats['reportations_pending'] ?? '—' }}</p>
        <p class="text-xs text-slate-400 mt-0.5 uppercase tracking-wide">Reportations</p>
    </div>

</div>

{{-- ══ Upcoming Seances + Quick Actions ══ --}}
<div class="grid grid-cols-1 lg:grid-cols-3 gap-5">

    {{-- Prochaines séances --}}
    <div class="lg:col-span-2 bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="px-5 py-4 border-b border-slate-100 flex items-center justify-between">
            <h3 class="text-sm font-semibold text-slate-700">Prochaines séances</h3>
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
                <a href="{{ route('seances.show', $seance) }}"
                   class="flex items-center gap-4 px-5 py-3.5 hover:bg-slate-50 transition-colors">
                    {{-- Day badge --}}
                    <div class="w-10 text-center flex-shrink-0">
                        <p class="text-[10px] text-slate-400 uppercase">{{ $seance->date_debut->isoFormat('ddd') }}</p>
                        <p class="text-lg font-bold text-blue-700 leading-none">{{ $seance->date_debut->format('d') }}</p>
                    </div>
                    {{-- Info --}}
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-semibold text-slate-800 truncate">
                            {{ $seance->module->name ?? 'Module non défini' }}
                        </p>
                        <p class="text-xs text-slate-400 truncate">
                            {{ $seance->groupe->name ?? '' }}
                            @if($seance->salle) · {{ $seance->salle->name }} @endif
                        </p>
                    </div>
                    {{-- Hours --}}
                    <div class="text-right flex-shrink-0">
                        <p class="text-xs font-semibold text-slate-600">{{ $seance->date_debut->format('H:i') }}</p>
                        <p class="text-[10px] text-slate-400">{{ $seance->date_fin->format('H:i') }}</p>
                    </div>
                    {{-- Status --}}
                    <span class="text-[10px] font-semibold px-2 py-0.5 rounded-full flex-shrink-0
                        {{ $seance->statut === 'actif' ? 'bg-emerald-100 text-emerald-700' : 'bg-amber-100 text-amber-700' }}">
                        {{ $seance->statut === 'actif' ? 'Actif' : 'Brouillon' }}
                    </span>
                </a>
                @endforeach
            </div>
        @else
            <div class="py-12 text-center">
                <svg class="w-10 h-10 text-slate-200 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                          d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
                <p class="text-xs text-slate-400">Aucune séance à venir cette semaine</p>
            </div>
        @endif
    </div>

    {{-- Quick Actions --}}
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="px-5 py-4 border-b border-slate-100">
            <h3 class="text-sm font-semibold text-slate-700">Actions rapides</h3>
        </div>
        <div class="p-4 space-y-2">
            @foreach([
                ['Emploi du temps',  route('emplois.index'),         'M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z',   'text-blue-600 bg-blue-50 hover:bg-blue-100'],
                ['Mes reportations', route('reportations.my'),       'M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15', 'text-orange-600 bg-orange-50 hover:bg-orange-100'],
                ['Absences & Retards',route('absences.index'),       'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z',                                                'text-red-500 bg-red-50 hover:bg-red-100'],
                ['News / Événements', route('news.index'),           'M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z', 'text-slate-600 bg-slate-50 hover:bg-slate-100'],
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

        {{-- Profile snippet --}}
        <div class="mx-4 mb-4 mt-1 rounded-xl bg-blue-50 border border-blue-100 p-4">
            <p class="text-[10px] text-blue-400 uppercase tracking-wide mb-1">Mon profil</p>
            <p class="text-xs font-semibold text-blue-800">{{ Auth::user()->name }}</p>
            <p class="text-[11px] text-blue-500">{{ Auth::user()->email }}</p>
            @if(Auth::user()->specialite)
            <p class="text-[11px] text-blue-500 mt-0.5">{{ Auth::user()->specialite }}</p>
            @endif
        </div>
    </div>

</div>

@endsection