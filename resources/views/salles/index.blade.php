{{-- resources/views/salles/index.blade.php --}}
@extends('layouts.app')

@section('title', 'Gestion des Salles')
@section('page-title', 'Gestion des Salles')

@section('content')

{{-- ── FLASH MESSAGES ─────────────────────────────────────────────── --}}
@if(session('success'))
    <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)"
         x-transition:leave="transition ease-in duration-300"
         x-transition:leave-start="opacity-100 translate-y-0"
         x-transition:leave-end="opacity-0 -translate-y-2"
         class="mb-4 flex items-center gap-3 bg-emerald-50 border border-emerald-200 text-emerald-800
                text-sm px-4 py-3 rounded-xl shadow-sm">
        <svg class="w-4 h-4 text-emerald-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
        </svg>
        {{ session('success') }}
    </div>
@endif

@if(session('error'))
    <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)"
         class="mb-4 flex items-center gap-3 bg-red-50 border border-red-200 text-red-800
                text-sm px-4 py-3 rounded-xl shadow-sm">
        <svg class="w-4 h-4 text-red-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        {{ session('error') }}
    </div>
@endif

@if($errors->any())
    <div class="mb-4 bg-red-50 border border-red-200 text-red-800 text-sm px-4 py-3 rounded-xl shadow-sm">
        <ul class="list-disc list-inside space-y-1">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

{{-- ── PAGE HEADER ─────────────────────────────────────────────────── --}}
<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
    <div>
        <h2 class="text-xl font-bold text-slate-800">Salles</h2>
        <p class="text-sm text-slate-500 mt-0.5">
            {{ $salles->count() }} salle{{ $salles->count() > 1 ? 's' : '' }} enregistrée{{ $salles->count() > 1 ? 's' : '' }}
        </p>
    </div>

    @can('salle-create')
    <button onclick="document.getElementById('modal-create').classList.remove('hidden')"
            class="inline-flex items-center gap-2 bg-[#1a4f8a] hover:bg-[#163f70] text-white
                   text-sm font-medium px-4 py-2.5 rounded-xl shadow-sm transition-colors">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
        </svg>
        Nouvelle salle
    </button>
    @endcan
</div>

{{-- ── STAT CARDS ───────────────────────────────────────────────────── --}}
<div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-6">

    {{-- Total salles --}}
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm px-5 py-4">
        <p class="text-xs text-slate-500 font-medium uppercase tracking-wider mb-1">Total</p>
        <p class="text-2xl font-bold text-slate-800">{{ $salles->count() }}</p>
        <p class="text-xs text-slate-400 mt-0.5">salles</p>
    </div>

    {{-- Capacité totale --}}
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm px-5 py-4">
        <p class="text-xs text-slate-500 font-medium uppercase tracking-wider mb-1">Capacité totale</p>
        <p class="text-2xl font-bold text-[#1a4f8a]">{{ $salles->sum('capacity') }}</p>
        <p class="text-xs text-slate-400 mt-0.5">places</p>
    </div>

    {{-- Capacité moyenne --}}
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm px-5 py-4">
        <p class="text-xs text-slate-500 font-medium uppercase tracking-wider mb-1">Moy. capacité</p>
        <p class="text-2xl font-bold text-slate-800">
            {{ $salles->count() ? round($salles->avg('capacity')) : 0 }}
        </p>
        <p class="text-xs text-slate-400 mt-0.5">places / salle</p>
    </div>

    {{-- Utilisées --}}
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm px-5 py-4">
        <p class="text-xs text-slate-500 font-medium uppercase tracking-wider mb-1">Utilisées</p>
        <p class="text-2xl font-bold text-emerald-600">
            {{ $salles->where('sessions_count', '>', 0)->count() }}
        </p>
        <p class="text-xs text-slate-400 mt-0.5">dans un EDT</p>
    </div>
</div>

{{-- ── TABLE ────────────────────────────────────────────────────────── --}}
<div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">

    {{-- Search bar --}}
    <div class="px-5 py-4 border-b border-slate-100 flex items-center gap-3">
        <div class="relative flex-1 max-w-xs">
            <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400"
                 fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
            </svg>
            <input type="text" id="searchInput" placeholder="Rechercher une salle…"
                   class="w-full pl-9 pr-4 py-2 text-sm border border-slate-200 rounded-xl
                          focus:outline-none focus:ring-2 focus:ring-[#1a4f8a]/30 focus:border-[#1a4f8a]
                          bg-slate-50 text-slate-700 placeholder:text-slate-400"
                   oninput="filterTable(this.value)">
        </div>
    </div>

    {{-- Table --}}
    <div class="overflow-x-auto">
        <table class="w-full text-sm" id="sallesTable">
            <thead>
                <tr class="bg-slate-50 border-b border-slate-200 text-left">
                    <th class="px-5 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wider">#</th>
                    <th class="px-5 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wider">Nom</th>
                    <th class="px-5 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wider">Capacité</th>
                    <th class="px-5 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wider">Statut EDT</th>
                    <th class="px-5 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wider text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100" id="sallesBody">
                @forelse($salles as $i => $salle)
                <tr class="hover:bg-slate-50/70 transition-colors salle-row"
                    data-name="{{ strtolower($salle->name) }}">

                    {{-- Index --}}
                    <td class="px-5 py-3.5 text-slate-400 font-mono text-xs">
                        {{ str_pad($i + 1, 2, '0', STR_PAD_LEFT) }}
                    </td>

                    {{-- Name --}}
                    <td class="px-5 py-3.5">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-lg bg-[#1a4f8a]/10 flex items-center justify-center flex-shrink-0">
                                <svg class="w-4 h-4 text-[#1a4f8a]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                </svg>
                            </div>
                            <span class="font-semibold text-slate-800">{{ $salle->name }}</span>
                        </div>
                    </td>

                    {{-- Capacity --}}
                    <td class="px-5 py-3.5">
                        <div class="flex items-center gap-2">
                            <span class="font-semibold text-slate-800">{{ $salle->capacity }}</span>
                            <span class="text-slate-400 text-xs">places</span>

                            {{-- Mini capacity bar --}}
                            @php
                                $maxCap = $salles->max('capacity') ?: 1;
                                $pct    = round(($salle->capacity / $maxCap) * 100);
                            @endphp
                            <div class="w-16 h-1.5 bg-slate-100 rounded-full overflow-hidden">
                                <div class="h-full bg-[#1a4f8a]/60 rounded-full"
                                     style="width: {{ $pct }}%"></div>
                            </div>
                        </div>
                    </td>

                    {{-- EDT status --}}
                    <td class="px-5 py-3.5">
                        @if($salle->sessions_count > 0)
                            <span class="inline-flex items-center gap-1.5 text-xs font-medium
                                         bg-emerald-50 text-emerald-700 border border-emerald-200
                                         px-2.5 py-1 rounded-full">
                                <span class="w-1.5 h-1.5 bg-emerald-500 rounded-full"></span>
                                {{ $salle->sessions_count }} session{{ $salle->sessions_count > 1 ? 's' : '' }}
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1.5 text-xs font-medium
                                         bg-slate-100 text-slate-500 border border-slate-200
                                         px-2.5 py-1 rounded-full">
                                <span class="w-1.5 h-1.5 bg-slate-400 rounded-full"></span>
                                Libre
                            </span>
                        @endif
                    </td>

                    {{-- Actions --}}
                    <td class="px-5 py-3.5 text-right">
                        <div class="flex items-center justify-end gap-2">

                            @can('salle-edit')
                            {{-- Edit button → opens pre-filled modal --}}
                            <button
                                onclick="openEditModal({{ $salle->id }}, '{{ addslashes($salle->name) }}', {{ $salle->capacity }})"
                                class="w-8 h-8 rounded-lg bg-amber-50 border border-amber-200 text-amber-600
                                       hover:bg-amber-100 flex items-center justify-center transition-colors"
                                title="Modifier">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5
                                             m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                </svg>
                            </button>
                            @endcan

                            @can('salle-delete')
                            @if($salle->sessions_count === 0)
                            <form method="POST" action="{{ route('salles.destroy', $salle) }}"
                                  onsubmit="return confirm('Supprimer la salle « {{ addslashes($salle->name) }} » ?')">
                                @csrf @method('DELETE')
                                <button type="submit"
                                        class="w-8 h-8 rounded-lg bg-red-50 border border-red-200 text-red-500
                                               hover:bg-red-100 flex items-center justify-center transition-colors"
                                        title="Supprimer">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858
                                                 L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                    </svg>
                                </button>
                            </form>
                            @else
                            <div class="w-8 h-8 rounded-lg bg-slate-50 border border-slate-200 text-slate-300
                                        flex items-center justify-center cursor-not-allowed"
                                 title="Impossible — salle utilisée dans un EDT">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858
                                             L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                </svg>
                            </div>
                            @endif
                            @endcan
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-5 py-16 text-center">
                        <div class="flex flex-col items-center gap-3 text-slate-400">
                            <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                      d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5
                                         M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                            </svg>
                            <p class="text-sm font-medium">Aucune salle enregistrée</p>
                            <p class="text-xs">Cliquez sur « Nouvelle salle » pour commencer.</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- ════════════════════════════════════════════════════════
     MODAL — CREATE
════════════════════════════════════════════════════════ --}}
<div id="modal-create"
     class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-sm p-4">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md overflow-hidden"
         onclick="event.stopPropagation()">

        {{-- Header --}}
        <div class="flex items-center justify-between px-6 py-4 border-b border-slate-100">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-xl bg-[#1a4f8a]/10 flex items-center justify-center">
                    <svg class="w-5 h-5 text-[#1a4f8a]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M12 4v16m8-8H4"/>
                    </svg>
                </div>
                <h3 class="font-semibold text-slate-800">Nouvelle salle</h3>
            </div>
            <button onclick="document.getElementById('modal-create').classList.add('hidden')"
                    class="w-8 h-8 rounded-lg text-slate-400 hover:text-slate-600 hover:bg-slate-100
                           flex items-center justify-center transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        {{-- Body --}}
        <form method="POST" action="{{ route('salles.store') }}" class="px-6 py-5 space-y-4">
            @csrf

            <div>
                <label class="block text-xs font-semibold text-slate-600 mb-1.5 uppercase tracking-wider">
                    Nom de la salle <span class="text-red-500">*</span>
                </label>
                <input type="text" name="name" value="{{ old('name') }}"
                       placeholder="ex: Salle 101, Labo Info…"
                       class="w-full px-4 py-2.5 text-sm border border-slate-200 rounded-xl
                              focus:outline-none focus:ring-2 focus:ring-[#1a4f8a]/30 focus:border-[#1a4f8a]
                              bg-slate-50 text-slate-800 placeholder:text-slate-400"
                       required>
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-600 mb-1.5 uppercase tracking-wider">
                    Capacité (places) <span class="text-red-500">*</span>
                </label>
                <input type="number" name="capacity" value="{{ old('capacity') }}"
                       placeholder="ex: 25" min="1" max="1000"
                       class="w-full px-4 py-2.5 text-sm border border-slate-200 rounded-xl
                              focus:outline-none focus:ring-2 focus:ring-[#1a4f8a]/30 focus:border-[#1a4f8a]
                              bg-slate-50 text-slate-800 placeholder:text-slate-400"
                       required>
            </div>

            <div class="flex items-center justify-end gap-3 pt-2">
                <button type="button"
                        onclick="document.getElementById('modal-create').classList.add('hidden')"
                        class="px-4 py-2 text-sm font-medium text-slate-600 bg-slate-100 hover:bg-slate-200
                               rounded-xl transition-colors">
                    Annuler
                </button>
                <button type="submit"
                        class="px-5 py-2 text-sm font-semibold text-white bg-[#1a4f8a] hover:bg-[#163f70]
                               rounded-xl transition-colors shadow-sm">
                    Créer la salle
                </button>
            </div>
        </form>
    </div>
</div>

{{-- ════════════════════════════════════════════════════════
     MODAL — EDIT
════════════════════════════════════════════════════════ --}}
<div id="modal-edit"
     class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-sm p-4">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md overflow-hidden"
         onclick="event.stopPropagation()">

        {{-- Header --}}
        <div class="flex items-center justify-between px-6 py-4 border-b border-slate-100">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-xl bg-amber-50 flex items-center justify-center">
                    <svg class="w-5 h-5 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5
                                 m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                    </svg>
                </div>
                <h3 class="font-semibold text-slate-800">Modifier la salle</h3>
            </div>
            <button onclick="document.getElementById('modal-edit').classList.add('hidden')"
                    class="w-8 h-8 rounded-lg text-slate-400 hover:text-slate-600 hover:bg-slate-100
                           flex items-center justify-center transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        {{-- Body --}}
        <form method="POST" id="edit-form" action="" class="px-6 py-5 space-y-4">
            @csrf @method('PATCH')

            <div>
                <label class="block text-xs font-semibold text-slate-600 mb-1.5 uppercase tracking-wider">
                    Nom de la salle <span class="text-red-500">*</span>
                </label>
                <input type="text" name="name" id="edit-name"
                       class="w-full px-4 py-2.5 text-sm border border-slate-200 rounded-xl
                              focus:outline-none focus:ring-2 focus:ring-amber-300 focus:border-amber-400
                              bg-slate-50 text-slate-800"
                       required>
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-600 mb-1.5 uppercase tracking-wider">
                    Capacité (places) <span class="text-red-500">*</span>
                </label>
                <input type="number" name="capacity" id="edit-capacity"
                       min="1" max="1000"
                       class="w-full px-4 py-2.5 text-sm border border-slate-200 rounded-xl
                              focus:outline-none focus:ring-2 focus:ring-amber-300 focus:border-amber-400
                              bg-slate-50 text-slate-800"
                       required>
            </div>

            <div class="flex items-center justify-end gap-3 pt-2">
                <button type="button"
                        onclick="document.getElementById('modal-edit').classList.add('hidden')"
                        class="px-4 py-2 text-sm font-medium text-slate-600 bg-slate-100 hover:bg-slate-200
                               rounded-xl transition-colors">
                    Annuler
                </button>
                <button type="submit"
                        class="px-5 py-2 text-sm font-semibold text-white bg-amber-500 hover:bg-amber-600
                               rounded-xl transition-colors shadow-sm">
                    Enregistrer
                </button>
            </div>
        </form>
    </div>
</div>

{{-- Close modals on backdrop click --}}
<script>
    // Open edit modal and pre-fill fields
    function openEditModal(id, name, capacity) {
        document.getElementById('edit-form').action = '/salles/' + id;
        document.getElementById('edit-name').value     = name;
        document.getElementById('edit-capacity').value = capacity;
        document.getElementById('modal-edit').classList.remove('hidden');
    }

    // Close modals on backdrop click
    ['modal-create', 'modal-edit'].forEach(id => {
        document.getElementById(id).addEventListener('click', function(e) {
            if (e.target === this) this.classList.add('hidden');
        });
    });

    // Live search / filter
    function filterTable(query) {
        const q = query.toLowerCase().trim();
        document.querySelectorAll('.salle-row').forEach(row => {
            const name = row.dataset.name || '';
            row.style.display = name.includes(q) ? '' : 'none';
        });
    }
</script>

{{-- Auto-open create modal if validation failed on store --}}
@if($errors->any() && old('_method') === null)
<script>
    document.addEventListener('DOMContentLoaded', () => {
        document.getElementById('modal-create').classList.remove('hidden');
    });
</script>
@endif

@endsection