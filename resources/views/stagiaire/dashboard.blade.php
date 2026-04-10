{{-- resources/views/stagiaire/dashboard.blade.php --}}
@extends('layouts.app')
@section('title', 'Espace Stagiaire')
@section('page-title', 'Dashboard')

@section('sidebar-nav')
    <x-nav-section label="Principal" />
    <x-nav-item route="{{ route('stagiaire.dashboard') }}" label="Dashboard"
        icon="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
    <x-nav-item route="#" label="Emploi du temps"
        icon="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
    <x-nav-item route="#" label="Mes cours"
        icon="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
    <x-nav-section label="Évaluation" />
    <x-nav-item route="#" label="Mes notes"
        icon="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
    <x-nav-item route="#" label="Absences"
        icon="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
    <x-nav-item route="#" label="Bulletin"
        icon="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
    <x-nav-section label="Autres" />
    <x-nav-item route="#" label="Réclamations"
        icon="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z" />
    <x-nav-item route="#" label="Choix option"
        icon="M9 5l7 7-7 7" />
    <x-nav-item route="#" label="News / Événements"
        icon="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z" />
@endsection

@section('content')
    <div class="bg-white rounded-2xl border border-slate-200 px-6 py-5 mb-5 shadow-sm">
        <h2 class="text-lg font-semibold text-slate-800">Bonjour, {{ Auth::user()->name }} 👋</h2>
        <p class="text-sm text-slate-500 mt-1">
            Filière : <strong>{{ Auth::user()->filiere->name ?? '—' }}</strong>
            &nbsp;·&nbsp; Groupe : <strong>{{ Auth::user()->id_groupe ?? '—' }}</strong>
        </p>
    </div>
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-5">
        @foreach([['Cours à venir','—'],['Notes en attente','—'],['Absences','—']] as [$l,$v])
        <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-sm">
            <p class="text-xs text-slate-400 uppercase tracking-widest mb-2">{{ $l }}</p>
            <p class="text-2xl font-semibold text-[#1a5fa8]">{{ $v }}</p>
        </div>
        @endforeach
    </div>
    <div class="bg-white rounded-2xl border border-slate-200 p-10 shadow-sm text-center">
        <p class="text-slate-400 text-sm">🚧 Contenu en cours de construction...</p>
    </div>
@endsection