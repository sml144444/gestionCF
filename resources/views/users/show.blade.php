@extends('layouts.app')
@section('title', $user->name)
@section('page-title', match($user->role) {
    'formateur'    => 'Profil formateur',
    'gestionnaire' => 'Profil gestionnaire',
    default        => 'Profil stagiaire',
})

@section('content')
@php
    $isStagiaire = $user->role === 'stagiaire';
    $isFormateur = $user->role === 'formateur';

    $rc = match($user->role) {
        'formateur' => [
            'bg' => 'bg-[#1a4f8a]', 'light' => 'bg-blue-50', 'text' => 'text-[#1a4f8a]',
            'border' => 'border-blue-100', 'badge' => 'bg-blue-100 text-[#1a4f8a]',
            'label' => 'Formateur', 'icon' => '🎓', 'shape' => 'rounded-2xl',
        ],
        'gestionnaire' => [
            'bg' => 'bg-slate-700', 'light' => 'bg-slate-100', 'text' => 'text-slate-700',
            'border' => 'border-slate-200', 'badge' => 'bg-slate-100 text-slate-700',
            'label' => 'Gestionnaire', 'icon' => '🏢', 'shape' => 'rounded-2xl',
        ],
        default => [
            'bg' => 'bg-[#1a4f8a]', 'light' => 'bg-blue-50', 'text' => 'text-[#1a4f8a]',
            'border' => 'border-blue-100', 'badge' => 'bg-blue-100 text-[#1a4f8a]',
            'label' => 'Stagiaire', 'icon' => '🎒', 'shape' => 'rounded-full',
        ],
    };
@endphp

<div class="max-w-3xl mx-auto space-y-6">

    {{-- Back --}}
    <a href="{{ url()->previous() }}"
       class="inline-flex items-center gap-2 text-xs font-semibold text-slate-500
              hover:text-slate-700 bg-white border border-slate-200 px-3 py-2 rounded-lg transition">
        ← Retour
    </a>

    {{-- ── Header ─────────────────────────────────────────────────────────── --}}
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 flex items-center gap-5">
        <div class="w-16 h-16 {{ $rc['shape'] }} {{ $rc['bg'] }} flex items-center justify-center
                    text-white text-2xl font-bold flex-shrink-0 overflow-hidden shadow-md">
            @if(!$isStagiaire && $user->photo)
                <img src="{{ asset('storage/' . $user->photo) }}" alt="{{ $user->name }}"
                     class="w-full h-full object-cover">
            @else
                {{ strtoupper(substr($user->name,0,1)) }}{{ strtoupper(substr(explode(' ',$user->name)[1]??'',0,1)) }}
            @endif
        </div>

        <div class="flex-1 min-w-0">
            <h2 class="text-xl font-bold text-slate-800">{{ $user->name }}</h2>
            <p class="text-sm text-slate-500 truncate">{{ $user->email }}</p>
            <div class="flex gap-2 mt-2 flex-wrap">

                {{-- Role badge --}}
                <span class="text-xs px-2.5 py-1 rounded-full font-semibold border {{ $rc['badge'] }} {{ $rc['border'] }}">
                    {{ $rc['icon'] }} {{ $rc['label'] }}
                </span>

                @if($isStagiaire)
                    @if($user->filiere)
                    <span class="text-xs px-2.5 py-1 rounded-full font-semibold
                                 bg-blue-50 text-blue-700 border border-blue-100">
                        {{ $user->filiere->name }}
                    </span>
                    @endif
                    @if($user->groupe)
                    <span class="text-xs px-2.5 py-1 rounded-full font-semibold
                                 bg-slate-100 text-slate-600 border border-slate-200">
                        {{ $user->groupe->name }}
                    </span>
                    <span class="text-xs px-2.5 py-1 rounded-full font-semibold
                                 bg-emerald-50 text-emerald-700 border border-emerald-100">
                        Promo {{ $user->groupe->promo_label ?? '—' }}
                    </span>
                    @endif
                @else
                    @if($user->matricule_formateur)
                    <span class="text-xs px-2.5 py-1 rounded-full font-semibold
                                 bg-slate-100 text-slate-600 border border-slate-200">
                        {{ $user->matricule_formateur }}
                    </span>
                    @endif
                    @can('user-manage-formateur')
                    @can('user-manage-gestionnaire')
                    <a href="{{ route('users.management.edit', $user) }}"
                       class="text-xs px-2.5 py-1 rounded-full font-semibold bg-amber-50 text-amber-700
                              border border-amber-100 hover:bg-amber-100 transition">
                        ✏️ Modifier
                    </a>
                    @endcan
                    @endcan
                @endif
            </div>
        </div>
    </div>

    {{-- ── Info grid ───────────────────────────────────────────────────────── --}}
    <div class="grid grid-cols-1 {{ !$isStagiaire ? 'md:grid-cols-2' : '' }} gap-5">

        {{-- Informations personnelles --}}
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
            <h3 class="text-sm font-bold text-slate-700 mb-4 flex items-center gap-2">
                <span class="w-6 h-6 rounded-lg {{ $rc['light'] }} {{ $rc['text'] }} flex items-center justify-center">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                </span>
                Informations personnelles
            </h3>

            @if($isStagiaire)
            {{-- Stagiaire: 2-col grid --}}
            <dl class="grid grid-cols-2 gap-4 text-sm">
                <div>
                    <dt class="text-xs text-slate-400 uppercase font-semibold tracking-wider">CIN</dt>
                    <dd class="mt-1 font-medium text-slate-800">{{ $user->cin ?? '—' }}</dd>
                </div>
                <div>
                    <dt class="text-xs text-slate-400 uppercase font-semibold tracking-wider">Téléphone</dt>
                    <dd class="mt-1 font-medium text-slate-800">{{ $user->phone ?? '—' }}</dd>
                </div>
                <div>
                    <dt class="text-xs text-slate-400 uppercase font-semibold tracking-wider">Date de naissance</dt>
                    <dd class="mt-1 font-medium text-slate-800">{{ $user->date_naissance?->format('d/m/Y') ?? '—' }}</dd>
                </div>
                <div>
                    <dt class="text-xs text-slate-400 uppercase font-semibold tracking-wider">Absences</dt>
                    <dd class="mt-1 font-semibold text-slate-800">{{ $user->absences->count() }} absence(s)</dd>
                </div>
            </dl>
            @else
            {{-- Staff: stacked list --}}
            <dl class="space-y-3 text-sm">
                <div class="flex justify-between items-center py-2 border-b border-slate-50">
                    <dt class="text-xs text-slate-400 uppercase font-semibold tracking-wider">Nom complet</dt>
                    <dd class="font-semibold text-slate-800">{{ $user->name }}</dd>
                </div>
                <div class="flex justify-between items-center py-2 border-b border-slate-50">
                    <dt class="text-xs text-slate-400 uppercase font-semibold tracking-wider">Email</dt>
                    <dd class="font-medium text-slate-700 truncate max-w-[200px]">{{ $user->email }}</dd>
                </div>
                <div class="flex justify-between items-center py-2 border-b border-slate-50">
                    <dt class="text-xs text-slate-400 uppercase font-semibold tracking-wider">Téléphone</dt>
                    <dd class="font-medium text-slate-800">{{ $user->phone ?? '—' }}</dd>
                </div>
                <div class="flex justify-between items-center py-2 border-b border-slate-50">
                    <dt class="text-xs text-slate-400 uppercase font-semibold tracking-wider">CIN</dt>
                    <dd class="font-medium text-slate-800">{{ $user->cin ?? '—' }}</dd>
                </div>
                <div class="flex justify-between items-center py-2">
                    <dt class="text-xs text-slate-400 uppercase font-semibold tracking-wider">Date de naissance</dt>
                    <dd class="font-medium text-slate-800">{{ $user->date_naissance?->format('d/m/Y') ?? '—' }}</dd>
                </div>
            </dl>
            @endif
        </div>

        {{-- Informations professionnelles (staff only) --}}
        @if(!$isStagiaire)
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
            <h3 class="text-sm font-bold text-slate-700 mb-4 flex items-center gap-2">
                <span class="w-6 h-6 rounded-lg {{ $rc['light'] }} {{ $rc['text'] }} flex items-center justify-center">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                    </svg>
                </span>
                Informations professionnelles
            </h3>
            <dl class="space-y-3 text-sm">
                <div class="flex justify-between items-center py-2 border-b border-slate-50">
                    <dt class="text-xs text-slate-400 uppercase font-semibold tracking-wider">Matricule</dt>
                    <dd class="font-medium text-slate-800">{{ $user->matricule_formateur ?? '—' }}</dd>
                </div>
                <div class="flex justify-between items-center py-2 border-b border-slate-50">
                    <dt class="text-xs text-slate-400 uppercase font-semibold tracking-wider">Date d'embauche</dt>
                    <dd class="font-medium text-slate-800">{{ $user->date_embauche?->format('d/m/Y') ?? '—' }}</dd>
                </div>
                @if($isFormateur)
                <div class="flex justify-between items-center py-2 border-b border-slate-50">
                    <dt class="text-xs text-slate-400 uppercase font-semibold tracking-wider">Limite heures</dt>
                    <dd class="font-medium text-slate-800">
                        {{ $user->nbr_heure_limit ? $user->nbr_heure_limit . ' h' : '—' }}
                    </dd>
                </div>
                <div class="flex justify-between items-center py-2 border-b border-slate-50">
                    <dt class="text-xs text-slate-400 uppercase font-semibold tracking-wider">Modules assignés</dt>
                    <dd class="font-semibold text-slate-800">{{ $user->modules->count() }} module(s)</dd>
                </div>
                @endif
                <div class="flex justify-between items-center py-2">
                    <dt class="text-xs text-slate-400 uppercase font-semibold tracking-wider">Membre depuis</dt>
                    <dd class="font-medium text-slate-800">{{ $user->created_at->format('d/m/Y') }}</dd>
                </div>
            </dl>
        </div>
        @endif
    </div>

    {{-- ── Modules (formateur only) ─────────────────────────────────────────── --}}
    @if($isFormateur && $user->modules->isNotEmpty())
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
        <h3 class="text-sm font-bold text-slate-700 mb-4 flex items-center gap-2">
            <span class="w-6 h-6 rounded-lg {{ $rc['light'] }} {{ $rc['text'] }} flex items-center justify-center">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477
                             5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0
                             3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                </svg>
            </span>
            Modules enseignés
            <span class="ml-auto text-xs font-normal text-slate-400">{{ $user->modules->count() }} module(s)</span>
        </h3>
        <div class="flex flex-wrap gap-2">
            @foreach($user->modules->sortBy('name') as $module)
            <span class="text-xs font-semibold px-3 py-1.5 rounded-full
                         {{ $rc['light'] }} {{ $rc['text'] }} border {{ $rc['border'] }}">
                {{ $module->name }}
                <span class="text-[10px] opacity-60 ml-1">{{ $module->nbr_heure }}h</span>
            </span>
            @endforeach
        </div>
    </div>
    @endif

    {{-- ── Absences (stagiaire only) ───────────────────────────────────────── --}}
    @if($isStagiaire && $user->absences->isNotEmpty())
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
        <h3 class="text-sm font-bold text-slate-700 mb-4 flex items-center gap-2">
            <span class="w-6 h-6 rounded-lg bg-red-50 text-red-600 flex items-center justify-center">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
            </span>
            Absences
            <span class="ml-auto text-xs font-normal text-slate-400">
                {{ $user->absences->count() }} total
                · {{ $user->absences->where('justifiee', true)->count() }} justifiée(s)
            </span>
        </h3>
        <div class="flex gap-4 text-center">
            <div class="flex-1 bg-red-50 rounded-xl p-3">
                <p class="text-2xl font-bold text-red-600">{{ $user->absences->count() }}</p>
                <p class="text-xs text-red-400 mt-1">Total</p>
            </div>
            <div class="flex-1 bg-emerald-50 rounded-xl p-3">
                <p class="text-2xl font-bold text-emerald-600">{{ $user->absences->where('justifiee', true)->count() }}</p>
                <p class="text-xs text-emerald-400 mt-1">Justifiées</p>
            </div>
            <div class="flex-1 bg-amber-50 rounded-xl p-3">
                <p class="text-2xl font-bold text-amber-600">{{ $user->absences->where('justifiee', false)->count() }}</p>
                <p class="text-xs text-amber-400 mt-1">Non justifiées</p>
            </div>
        </div>
    </div>
    @endif

</div>
@endsection