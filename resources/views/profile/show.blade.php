{{-- resources/views/profile/show.blade.php --}}
@extends('layouts.app')

@section('title', 'Mon Profil')
@section('page-title', 'Mon Profil')

@section('content')


@php
    $roleColors = [
        'admin'        => [
            'bg'     => 'bg-emerald-600',
            'light'  => 'bg-emerald-50',
            'text'   => 'text-emerald-700',
            'border' => 'border-emerald-200',
            'badge'  => 'bg-emerald-100 text-emerald-800',
        ],
        'gestionnaire' => [
            'bg'     => 'bg-slate-700',
            'light'  => 'bg-slate-100',
            'text'   => 'text-slate-700',
            'border' => 'border-slate-300',
            'badge'  => 'bg-slate-100 text-slate-700',
        ],
        'formateur'    => [
            'bg'     => 'bg-[#1a4f8a]',
            'light'  => 'bg-blue-50',
            'text'   => 'text-[#1a4f8a]',
            'border' => 'border-blue-200',
            'badge'  => 'bg-blue-100 text-[#1a4f8a]',
        ],
        'stagiaire'    => [
            'bg'     => 'bg-[#1a4f8a]',
            'light'  => 'bg-blue-50',
            'text'   => 'text-[#1a4f8a]',
            'border' => 'border-blue-200',
            'badge'  => 'bg-blue-100 text-[#1a4f8a]',
        ],
    ];
    $rc = $roleColors[$user->role] ?? $roleColors['stagiaire'];
@endphp

{{-- ── Flash messages ─────────────────────────────────────────────────────── --}}
@if(session('success'))
<div x-data="{ show: true }" x-show="show"
     x-transition:enter="transition ease-out duration-200"
     x-transition:enter-start="opacity-0 -translate-y-1"
     x-transition:enter-end="opacity-100 translate-y-0"
     x-transition:leave="transition ease-in duration-150"
     x-transition:leave-end="opacity-0"
     x-init="setTimeout(() => show = false, 4000)"
     class="mb-4 flex items-center gap-3 bg-emerald-50 border border-emerald-200
            text-emerald-800 rounded-xl px-4 py-3 text-sm font-medium shadow-sm">
    <svg class="w-4 h-4 flex-shrink-0 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
    </svg>
    {{ session('success') }}
</div>
@endif

{{-- ─────────────────────────────────────────────────────────────────────────
     ROOT Alpine scope
───────────────────────────────────────────────────────────────────────── --}}
<div x-data="{
    editModal:     {{ $errors->has('name') || $errors->has('email') || $errors->has('phone') || $errors->has('cin') || $errors->has('date_naissance') ? 'true' : 'false' }},
    passwordModal: {{ $errors->has('current_password') || $errors->has('password') || session('open_password_modal') ? 'true' : 'false' }},
    emailModal:    {{ $errors->hasAny(['edu_email','edu_password','new_email']) || session('open_email_modal') ? 'true' : 'false' }},
    photoModal:    false,
    preview:       null,
    openPhoto()  { this.preview = null; this.photoModal = true; },
    closePhoto() { this.preview = null; this.photoModal = false; }
}" class="max-w-4xl mx-auto space-y-5">

    {{-- ══════════════════════════════════════════════════════════════════════
         HERO CARD
    ══════════════════════════════════════════════════════════════════════ --}}
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">

        {{-- Cover bar --}}
        <div class="h-24 {{ $rc['bg'] }} relative overflow-hidden">
            <div class="absolute inset-0 opacity-10"
                 style="background-image:repeating-linear-gradient(45deg,transparent,transparent 10px,rgba(255,255,255,.2) 10px,rgba(255,255,255,.2) 20px)">
            </div>
        </div>

        <div class="px-6 pb-5 relative">
            <div class="flex items-end justify-between -mt-10 mb-4">

                {{-- Avatar with camera overlay --}}
                <div class="relative group">
                    <div class="w-20 h-20 rounded-2xl {{ $rc['bg'] }} border-4 border-white shadow-md
                                flex items-center justify-center text-white font-bold text-2xl overflow-hidden">
                        @if($user->photo)
                            <img src="{{ asset('storage/' . $user->photo) }}" alt="{{ $user->name }}"
                                 class="w-full h-full object-cover">
                        @else
                            {{ strtoupper(substr($user->name, 0, 1)) }}{{ strtoupper(substr(explode(' ', $user->name)[1] ?? '', 0, 1)) }}
                        @endif
                    </div>
                    <button @click="openPhoto()"
                            class="absolute inset-0 rounded-2xl bg-black/50 flex items-center justify-center
                                   opacity-0 group-hover:opacity-100 transition-opacity duration-200 cursor-pointer">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0"/>
                        </svg>
                    </button>
                </div>

                {{-- Action buttons --}}
                <div class="flex items-center gap-2 pt-10">
                    <button @click="passwordModal = true"
                            class="flex items-center gap-1.5 text-xs font-semibold px-3 py-2 rounded-xl
                                   border border-slate-200 bg-white text-slate-600
                                   hover:border-slate-300 hover:bg-slate-50 transition-all shadow-sm">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                        </svg>
                        Mot de passe
                    </button>
                    @if(Auth::user()->isStagiaire())
                    <button @click="emailModal = true"
                            class="flex items-center gap-1.5 text-xs font-semibold px-3 py-2 rounded-xl
                                   border border-slate-200 bg-white text-slate-600
                                   hover:border-slate-300 hover:bg-slate-50 transition-all shadow-sm">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                        </svg>
                        Changer email
                    </button>
                    @endif
                    <button @click="editModal = true"
                            class="flex items-center gap-1.5 text-xs font-semibold px-3 py-2 rounded-xl
                                   {{ $rc['bg'] }} text-white hover:opacity-90 transition-all shadow-sm">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>
                        Modifier profil
                    </button>
                </div>
            </div>

            {{-- Name + role badge --}}
            <div>
                <h2 class="text-xl font-bold text-slate-800">{{ $user->name }}</h2>
                <div class="flex items-center gap-2 mt-1">
                    <span class="text-xs font-semibold px-2.5 py-1 rounded-full {{ $rc['badge'] }}">
                        {{ ucfirst($user->role) }}
                    </span>
                    <span class="text-xs text-slate-400">{{ $user->email }}</span>
                </div>
            </div>
        </div>
    </div>

    {{-- ══════════════════════════════════════════════════════════════════════
         INFO GRID
    ══════════════════════════════════════════════════════════════════════ --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">

        {{-- ── Informations personnelles ──────────────────────────────── --}}
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5">
            <h3 class="text-sm font-bold text-slate-700 mb-4 flex items-center gap-2">
                <span class="w-6 h-6 rounded-lg {{ $rc['light'] }} {{ $rc['text'] }} flex items-center justify-center flex-shrink-0">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                </span>
                Informations personnelles
            </h3>
            <div class="space-y-3">
                <x-profile-row label="Nom complet"    :value="$user->name" />
                <x-profile-row label="Email"          :value="$user->email" />
                <x-profile-row label="Téléphone"      :value="$user->phone ?? '—'" />
                <x-profile-row label="CIN"            :value="$user->cin ?? '—'" />
                <x-profile-row label="Date naissance" :value="$user->date_naissance?->format('d/m/Y') ?? '—'" />
                <x-profile-row label="Membre depuis"  :value="$user->created_at->format('d/m/Y')" />
            </div>
        </div>

        {{-- ── Scolarité (stagiaire) ───────────────────────────────────── --}}
        @if($user->isStagiaire())
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5">
            <h3 class="text-sm font-bold text-slate-700 mb-4 flex items-center gap-2">
                <span class="w-6 h-6 rounded-lg {{ $rc['light'] }} {{ $rc['text'] }} flex items-center justify-center flex-shrink-0">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M12 14l9-5-9-5-9 5 9 5z M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"/>
                    </svg>
                </span>
                Scolarité
            </h3>
            <div class="space-y-3">
                <x-profile-row label="Filière"         :value="$user->filiere?->name ?? '—'" />
                <x-profile-row label="Groupe"          :value="$user->groupe?->name ?? '—'" />
                @if($user->groupe?->annee)
                <x-profile-row label="Année"           :value="'Année ' . $user->groupe->annee" />
                @endif
                @if($user->groupe?->promo)
                <x-profile-row label="Promotion"       :value="$user->groupe->promo" />
                @endif
                @if($user->groupe?->nbr_limit)
                <x-profile-row label="Capacité groupe" :value="$user->groupe->stagiaires()->count() . ' / ' . $user->groupe->nbr_limit" />
                @endif
            </div>
        </div>
        @endif

        {{-- ── Informations formateur ──────────────────────────────────── --}}
        @if($user->isFormateur())
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5">
            <h3 class="text-sm font-bold text-slate-700 mb-4 flex items-center gap-2">
                <span class="w-6 h-6 rounded-lg {{ $rc['light'] }} {{ $rc['text'] }} flex items-center justify-center flex-shrink-0">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                    </svg>
                </span>
                Informations formateur
            </h3>
            <div class="space-y-3">
                <x-profile-row label="Matricule"        :value="$user->matricule_formateur ?? '—'" />
                <x-profile-row label="Date embauche"    :value="$user->date_embauche?->format('d/m/Y') ?? '—'" />
                <x-profile-row label="Limite heures"    :value="$user->nbr_heure_limit ? $user->nbr_heure_limit . ' h' : '—'" />
                <x-profile-row label="Modules assignés" :value="$user->modules->count() . ' module(s)'" />
            </div>
        </div>
        @endif

        {{-- ── Modules (formateur) ────────────────────────────────────── --}}
        @if($user->isFormateur() && $user->modules->isNotEmpty())
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5">
            <h3 class="text-sm font-bold text-slate-700 mb-4 flex items-center gap-2">
                <span class="w-6 h-6 rounded-lg {{ $rc['light'] }} {{ $rc['text'] }} flex items-center justify-center flex-shrink-0">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253"/>
                    </svg>
                </span>
                Mes modules
            </h3>
            <div class="flex flex-wrap gap-2">
                @foreach($user->modules as $module)
                <span class="text-xs font-medium px-2.5 py-1 rounded-full {{ $rc['light'] }} {{ $rc['text'] }} border {{ $rc['border'] }}">
                    {{ $module->name }}
                </span>
                @endforeach
            </div>
        </div>
        @endif

        {{-- ── Permissions (admin / gestionnaire) ────────────────────── --}}
        @if($user->isAdmin() || $user->isGestionnaire())
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5 md:col-span-2">
            <h3 class="text-sm font-bold text-slate-700 mb-4 flex items-center gap-2">
                <span class="w-6 h-6 rounded-lg {{ $rc['light'] }} {{ $rc['text'] }} flex items-center justify-center flex-shrink-0">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                    </svg>
                </span>
                Permissions accordées
            </h3>
            <div class="flex flex-wrap gap-1.5">
                @foreach($user->getAllPermissions()->sortBy('name') as $perm)
                <span class="text-[10px] font-semibold px-2 py-0.5 rounded-full bg-slate-100 text-slate-600 border border-slate-200">
                    {{ $perm->name }}
                </span>
                @endforeach
            </div>
        </div>
        @endif

    </div>{{-- /grid --}}


    {{-- ══════════════════════════════════════════════════════════════════════
         MODAL — Edit Profile
    ══════════════════════════════════════════════════════════════════════ --}}
    <div x-show="editModal" x-cloak
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-end="opacity-0"
         @keydown.escape.window="editModal = false"
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/40 backdrop-blur-sm">

        <div x-show="editModal"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 scale-95 translate-y-2"
             x-transition:enter-end="opacity-100 scale-100 translate-y-0"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-end="opacity-0 scale-95"
             @click.outside="editModal = false"
             class="w-full max-w-lg bg-white rounded-2xl shadow-2xl border border-slate-200 overflow-hidden">

            <div class="flex items-center justify-between px-6 py-4 border-b border-slate-100">
                <h3 class="text-base font-bold text-slate-800">Modifier mon profil</h3>
                <button @click="editModal = false"
                        class="w-8 h-8 rounded-lg hover:bg-slate-100 flex items-center justify-center text-slate-400 hover:text-slate-600 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <form method="POST" action="{{ route('profile.update') }}" class="px-6 py-5 space-y-4" data-submit-once>
                @csrf @method('PUT')
                <div class="grid grid-cols-2 gap-4">
                    <div class="col-span-2">
@if($user->isStagiaire())
    {{-- Hidden input so name is always submitted --}}
    <input type="hidden" name="name" value="{{ $user->name }}">
    
    <label class="block text-xs font-semibold text-slate-500 uppercase tracking-widest mb-1.5">
        Nom complet
    </label>
    <div class="w-full px-3 py-2.5 text-sm border border-slate-200 rounded-xl bg-slate-100 text-slate-500 cursor-not-allowed select-none">
        {{ $user->name }}
    </div>
    <p class="text-[10px] text-slate-400 mt-1">Le nom ne peut pas être modifié. Contactez l'administration.</p>
@else
    <x-form-field name="name" label="Nom complet" :value="old('name', $user->name)" required />
@endif
                    </div>
                    <div>

{{-- AFTER --}}
<div>
    <label class="block text-xs font-semibold text-slate-500 uppercase tracking-widest mb-1.5">
        Téléphone
    </label>
    <input type="tel" name="phone"
           value="{{ old('phone', $user->phone) }}"
           inputmode="numeric"
           pattern="[0-9]*"
           maxlength="15"
           placeholder="ex: 0612345678"
           @input="$event.target.value = $event.target.value.replace(/[^0-9]/g, '')"
           class="w-full px-3 py-2.5 text-sm border rounded-xl transition-colors
                  focus:outline-none focus:ring-2 focus:ring-blue-500/30 focus:border-blue-400
                  @error('phone') border-red-400 bg-red-50 @else border-slate-200 @enderror">
    @error('phone')
        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
    @enderror
</div>
                    </div>


                    <div>
                        <x-form-field name="cin" label="CIN" :value="old('cin', $user->cin)" />
                    </div>
                    <div>
                        <x-form-field name="date_naissance" label="Date de naissance" type="date"
                                      :value="old('date_naissance', $user->date_naissance?->format('Y-m-d'))" />
                    </div>
                    @if($user->isFormateur())
                    <div>
                        <x-form-field name="matricule_formateur" label="Matricule"
                                      :value="old('matricule_formateur', $user->matricule_formateur)" />
                    </div>
                    <div class="col-span-2">
                        <x-form-field name="date_embauche" label="Date embauche" type="date"
                                      :value="old('date_embauche', $user->date_embauche?->format('Y-m-d'))" />
                    </div>
                    @endif
                </div>

                <div class="flex gap-3 pt-1">
                    <button type="button" @click="editModal = false"
                            class="flex-1 px-4 py-2.5 rounded-xl border border-slate-200 text-sm font-semibold text-slate-600 hover:bg-slate-50 transition-colors">
                        Annuler
                    </button>
                    <button type="submit"
                            class="flex-1 px-4 py-2.5 rounded-xl {{ $rc['bg'] }} text-white text-sm font-semibold hover:opacity-90 transition-opacity shadow-sm">
                        Enregistrer
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- ══════════════════════════════════════════════════════════════════════
         MODAL — Change Password
    ══════════════════════════════════════════════════════════════════════ --}}
    <div x-show="passwordModal" x-cloak
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-end="opacity-0"
         @keydown.escape.window="passwordModal = false"
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/40 backdrop-blur-sm">

        <div x-show="passwordModal"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 scale-95 translate-y-2"
             x-transition:enter-end="opacity-100 scale-100 translate-y-0"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-end="opacity-0 scale-95"
             @click.outside="passwordModal = false"
             class="w-full max-w-md bg-white rounded-2xl shadow-2xl border border-slate-200 overflow-hidden">

            <div class="flex items-center justify-between px-6 py-4 border-b border-slate-100">
                <h3 class="text-base font-bold text-slate-800">Changer le mot de passe</h3>
                <button @click="passwordModal = false"
                        class="w-8 h-8 rounded-lg hover:bg-slate-100 flex items-center justify-center text-slate-400 hover:text-slate-600 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <form method="POST" action="{{ route('profile.password') }}" class="px-6 py-5 space-y-4" data-submit-once>
                @csrf @method('PUT')

                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1.5">Mot de passe actuel</label>
                    <input type="password" name="current_password" autocomplete="current-password"
                           class="w-full px-3 py-2.5 text-sm border rounded-xl transition-colors
                                  focus:outline-none focus:ring-2 focus:ring-blue-500/30 focus:border-blue-400
                                  @error('current_password') border-red-400 bg-red-50 @else border-slate-200 @enderror">
                    @error('current_password')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1.5">Nouveau mot de passe</label>
                    <input type="password" name="password" autocomplete="new-password"
                           class="w-full px-3 py-2.5 text-sm border rounded-xl transition-colors
                                  focus:outline-none focus:ring-2 focus:ring-blue-500/30 focus:border-blue-400
                                  @error('password') border-red-400 bg-red-50 @else border-slate-200 @enderror">
                    @error('password')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1.5">Confirmer le mot de passe</label>
                    <input type="password" name="password_confirmation" autocomplete="new-password"
                           class="w-full px-3 py-2.5 text-sm border border-slate-200 rounded-xl transition-colors
                                  focus:outline-none focus:ring-2 focus:ring-blue-500/30 focus:border-blue-400">
                </div>

                <div class="flex gap-3 pt-1">
                    <button type="button" @click="passwordModal = false"
                            class="flex-1 px-4 py-2.5 rounded-xl border border-slate-200 text-sm font-semibold text-slate-600 hover:bg-slate-50 transition-colors">
                        Annuler
                    </button>
                    <button type="submit"
                            class="flex-1 px-4 py-2.5 rounded-xl bg-slate-800 text-white text-sm font-semibold hover:bg-slate-700 transition-colors shadow-sm">
                        Changer
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- ══════════════════════════════════════════════════════════════════════
         MODAL — Change Email (stagiaire only)
    ══════════════════════════════════════════════════════════════════════ --}}
    @if(Auth::user()->isStagiaire())
    <div x-show="emailModal" x-cloak
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-end="opacity-0"
         @keydown.escape.window="emailModal = false"
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/40 backdrop-blur-sm">

        <div x-show="emailModal"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 scale-95 translate-y-2"
             x-transition:enter-end="opacity-100 scale-100 translate-y-0"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-end="opacity-0 scale-95"
             @click.outside="emailModal = false"
             class="w-full max-w-md bg-white rounded-2xl shadow-2xl border border-slate-200 overflow-hidden">

            <div class="flex items-center justify-between px-6 py-4 border-b border-slate-100">
                <h3 class="text-base font-bold text-slate-800">Changer l'email personnel</h3>
                <button @click="emailModal = false"
                        class="w-8 h-8 rounded-lg hover:bg-slate-100 flex items-center justify-center text-slate-400 hover:text-slate-600 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <form method="POST" action="{{ route('profile.email') }}" class="px-6 py-5 space-y-4" data-submit-once>
                @csrf @method('PUT')

                <div class="bg-blue-50 border border-blue-100 rounded-xl px-4 py-3 flex items-start gap-3">
                    <svg class="w-4 h-4 text-blue-400 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                    </svg>
                    <p class="text-xs text-blue-600 leading-relaxed">
                        Vérification EDU requise avant de changer votre email personnel.
                    </p>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-500 uppercase tracking-widest mb-1.5">Email EDU</label>
                    <input type="email" name="edu_email" value="{{ old('edu_email') }}"
                           placeholder="votre.email@ofppt.ma"
                           class="w-full px-3 py-2.5 text-sm border rounded-xl transition-colors
                                  focus:outline-none focus:ring-2 focus:ring-blue-500/30 focus:border-blue-400
                                  @error('edu_email') border-red-400 bg-red-50 @else border-slate-200 @enderror">
                    @error('edu_email')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-500 uppercase tracking-widest mb-1.5">Mot de passe EDU</label>
                    <input type="password" name="edu_password" placeholder="••••••••"
                           class="w-full px-3 py-2.5 text-sm border rounded-xl transition-colors
                                  focus:outline-none focus:ring-2 focus:ring-blue-500/30 focus:border-blue-400
                                  @error('edu_password') border-red-400 bg-red-50 @else border-slate-200 @enderror">
                    @error('edu_password')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-500 uppercase tracking-widest mb-1.5">Nouvel email personnel</label>
                    <input type="email" name="new_email" value="{{ old('new_email') }}"
                           placeholder="nouveau@gmail.com"
                           class="w-full px-3 py-2.5 text-sm border rounded-xl transition-colors
                                  focus:outline-none focus:ring-2 focus:ring-blue-500/30 focus:border-blue-400
                                  @error('new_email') border-red-400 bg-red-50 @else border-slate-200 @enderror">
                    @error('new_email')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex gap-3 pt-1">
                    <button type="button" @click="emailModal = false"
                            class="flex-1 px-4 py-2.5 rounded-xl border border-slate-200 text-sm font-semibold text-slate-600 hover:bg-slate-50 transition-colors">
                        Annuler
                    </button>
                    <button type="submit"
                            class="flex-1 px-4 py-2.5 rounded-xl {{ $rc['bg'] }} text-white text-sm font-semibold hover:opacity-90 transition-opacity shadow-sm">
                        Mettre à jour
                    </button>
                </div>
            </form>
        </div>
    </div>
    @endif

    {{-- ══════════════════════════════════════════════════════════════════════
         MODAL — Change Photo
    ══════════════════════════════════════════════════════════════════════ --}}
    <div x-show="photoModal" x-cloak
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-end="opacity-0"
         @keydown.escape.window="closePhoto()"
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/40 backdrop-blur-sm">

        <div x-show="photoModal"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 scale-95 translate-y-2"
             x-transition:enter-end="opacity-100 scale-100 translate-y-0"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-end="opacity-0 scale-95"
             @click.outside="closePhoto()"
             class="w-full max-w-sm bg-white rounded-2xl shadow-2xl border border-slate-200 overflow-hidden">

            <div class="flex items-center justify-between px-6 py-4 border-b border-slate-100">
                <h3 class="text-base font-bold text-slate-800">Changer la photo</h3>
                <button @click="closePhoto()"
                        class="w-8 h-8 rounded-lg hover:bg-slate-100 flex items-center justify-center text-slate-400 hover:text-slate-600 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <form method="POST" action="{{ route('profile.photo') }}" enctype="multipart/form-data"
                  class="px-6 py-5 space-y-4" data-submit-once>
                @csrf

                <div class="flex flex-col items-center gap-3">
                    <div class="relative w-24 h-24 rounded-2xl {{ $rc['bg'] }} border-4 border-slate-100 shadow-md overflow-hidden
                                flex items-center justify-center text-white font-bold text-2xl select-none">
                        <img x-show="preview" :src="preview"
                             class="absolute inset-0 w-full h-full object-cover" alt="">
                        @if($user->photo)
                            <img x-show="!preview"
                                 src="{{ asset('storage/' . $user->photo) }}"
                                 class="absolute inset-0 w-full h-full object-cover" alt="">
                        @else
                            <span x-show="!preview">
                                {{ strtoupper(substr($user->name, 0, 1)) }}{{ strtoupper(substr(explode(' ', $user->name)[1] ?? '', 0, 1)) }}
                            </span>
                        @endif
                    </div>

                    <label class="cursor-pointer flex items-center gap-2 text-sm font-semibold px-4 py-2 rounded-xl
                                  border-2 border-dashed border-slate-300 hover:border-[#1a4f8a]
                                  text-slate-600 hover:text-[#1a4f8a] transition-all">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                        </svg>
                        Choisir une image
                        <input type="file" name="photo" accept="image/*" class="hidden"
                               @change="preview = URL.createObjectURL($event.target.files[0])">
                    </label>
                    <p class="text-[10px] text-slate-400">JPEG, PNG, WEBP — max 2 Mo</p>
                </div>

                <div class="flex gap-3">
                    <button type="button" @click="closePhoto()"
                            class="flex-1 px-4 py-2.5 rounded-xl border border-slate-200 text-sm font-semibold text-slate-600 hover:bg-slate-50 transition-colors">
                        Annuler
                    </button>
                    <button type="submit"
                            class="flex-1 px-4 py-2.5 rounded-xl {{ $rc['bg'] }} text-white text-sm font-semibold hover:opacity-90 transition-opacity shadow-sm">
                        Enregistrer
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>{{-- /root x-data --}}

<script>
document.querySelectorAll('form[data-submit-once]').forEach(function (form) {
    form.addEventListener('submit', function () {
        form.querySelectorAll('button[type="submit"]').forEach(function (btn) {
            btn.disabled = true;
            btn.style.opacity = '0.6';
            btn.style.cursor  = 'not-allowed';
            btn.innerHTML =
                '<svg class="animate-spin w-4 h-4 inline mr-1.5" fill="none" viewBox="0 0 24 24">'
                + '<circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>'
                + '<path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"/>'
                + '</svg>'
                + 'Traitement…';
        });
    });
});
</script>

@endsection