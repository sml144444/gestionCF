@extends('layouts.app')
@section('title', $user->name)
@section('page-title', 'Profil stagiaire')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">

    {{-- Back button --}}
    <a href="{{ route('stagiaire.index') }}"
       class="inline-flex items-center gap-2 text-xs font-semibold text-slate-500
              hover:text-slate-700 bg-white border border-slate-200 px-3 py-2 rounded-lg transition">
        ← Retour à la liste
    </a>

    {{-- Header card --}}
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 flex items-center gap-5">
        <div class="w-16 h-16 rounded-full bg-[#1a4f8a] flex items-center justify-center
                    text-white text-2xl font-bold flex-shrink-0">
            {{ strtoupper(substr($user->name, 0, 1)) }}
        </div>
        <div>
            <h2 class="text-xl font-bold text-slate-800">{{ $user->name }}</h2>
            <p class="text-sm text-slate-500">{{ $user->email }}</p>
            <div class="flex gap-2 mt-2 flex-wrap">
                <span class="text-xs px-2 py-1 rounded-full bg-blue-50 text-blue-700 border border-blue-100 font-semibold">
                    {{ $user->filiere?->name ?? '—' }}
                </span>
                <span class="text-xs px-2 py-1 rounded-full bg-slate-100 text-slate-600 border border-slate-200 font-semibold">
                    {{ $user->groupe?->name ?? '—' }}
                </span>
                <span class="text-xs px-2 py-1 rounded-full bg-emerald-50 text-emerald-700 border border-emerald-100 font-semibold">
                    Promo {{ $user->groupe?->promo_label ?? '—' }}
                </span>
            </div>
        </div>
    </div>

    {{-- Info card --}}
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
        <h3 class="text-sm font-bold text-slate-700 mb-4">Informations personnelles</h3>
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
                <dd class="mt-1 font-medium text-slate-800">
                    {{ $user->date_naissance?->format('d/m/Y') ?? '—' }}
                </dd>
            </div>
            <div>
                <dt class="text-xs text-slate-400 uppercase font-semibold tracking-wider">Absences</dt>
                <dd class="mt-1 font-medium text-slate-800">
                    {{ $user->absences->count() }} absence(s)
                </dd>
            </div>
        </dl>
    </div>

</div>
@endsection