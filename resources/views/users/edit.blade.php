@extends('layouts.app')
@section('title', 'Modifier l\'utilisateur')
@section('page-title', 'Modifier l\'utilisateur')

@section('content')
<div style="font-family:'Segoe UI',system-ui,sans-serif; max-width:800px;">

{{-- Back --}}
<a href="{{ route('users.management.index') }}"
   style="display:inline-flex; align-items:center; gap:5px; font-size:12px; font-weight:600;
          color:#64748b; text-decoration:none; margin-bottom:20px;"
   onmouseover="this.style.color='#0a6640'" onmouseout="this.style.color='#64748b'">
    ← Retour à la liste
</a>

{{-- Header avec identité --}}
@php
    $rc = $user->role === 'formateur'
        ? ['bg'=>'#fdf4ff','text'=>'#9333ea','border'=>'#e9d5ff']
        : ['bg'=>'#eff6ff','text'=>'#2563eb','border'=>'#bfdbfe'];
    $initials = strtoupper(substr($user->name,0,1))
              . strtoupper(substr(explode(' ',$user->name)[1] ?? '',0,1));
@endphp
<div style="display:flex; align-items:center; gap:14px; margin-bottom:24px;">
    <div style="width:50px; height:50px; border-radius:14px; background:{{ $rc['bg'] }};
                border:2px solid {{ $rc['border'] }}; display:flex; align-items:center;
                justify-content:center; font-size:17px; font-weight:800;
                color:{{ $rc['text'] }}; flex-shrink:0;">{{ $initials }}</div>
    <div>
        <h1 style="font-size:20px; font-weight:800; color:#0f172a; margin:0;">{{ $user->name }}</h1>
        <p style="font-size:12px; color:#64748b; margin:3px 0 0;">
            {{ $user->email }}
            &nbsp;·&nbsp;
            <span style="font-weight:700; color:{{ $rc['text'] }}; text-transform:capitalize;">
                {{ $user->role }}
            </span>
            @if($user->modules->count())
            &nbsp;·&nbsp;
            <span style="color:#9333ea;">{{ $user->modules->count() }} module(s)</span>
            @endif
        </p>
    </div>
</div>

{{-- Errors --}}
@if($errors->any())
<div style="margin-bottom:16px; padding:12px 16px; border-radius:12px;
            background:#fef2f2; border:1px solid #fecaca; color:#dc2626; font-size:12px;">
    <div style="font-weight:700; margin-bottom:6px;">Veuillez corriger les erreurs :</div>
    <ul style="margin:0; padding-left:16px;">
        @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
    </ul>
</div>
@endif

{{-- ══════════════════════════════════════════════════════
     UPDATE FORM — no other <form> tag inside this one
     ══════════════════════════════════════════════════════ --}}
<form method="POST" action="{{ route('users.management.update', $user) }}" enctype="multipart/form-data">
@csrf
@method('PUT')

{{-- ── ROLE TABS ── --}}
<div style="display:flex; gap:8px; margin-bottom:24px;">
    @foreach([
        'formateur'    => ['#9333ea','#fdf4ff','🎓'],
        'gestionnaire' => ['#2563eb','#eff6ff','🏢'],
    ] as $r => [$col,$bg,$icon])
    @php $active = old('role', $user->role) === $r; @endphp
    <label style="flex:1; cursor:pointer;">
        <input type="radio" name="role" value="{{ $r }}"
               {{ $active ? 'checked' : '' }}
               onchange="switchRole(this.value)"
               style="display:none;">
        <div id="tab-{{ $r }}"
             style="padding:14px 18px; border-radius:14px; text-align:center; transition:all 0.15s;
                    border:2px solid {{ $active ? $col : '#e2e8f0' }};
                    background:{{ $active ? $bg : 'white' }};">
            <div style="font-size:15px; margin-bottom:4px;">{{ $icon }}</div>
            <div id="tab-label-{{ $r }}"
                 style="font-size:13px; font-weight:700; text-transform:capitalize;
                        color:{{ $active ? $col : '#64748b' }};">{{ ucfirst($r) }}</div>
        </div>
    </label>
    @endforeach
</div>

{{-- ── CARD ── --}}
<div style="background:white; border-radius:16px; border:1px solid #e2e8f0;
            box-shadow:0 2px 12px rgba(0,0,0,0.06); overflow:hidden;">

    {{-- Infos générales --}}
    <div style="padding:22px 24px; border-bottom:1px solid #f1f5f9;">
        <div style="font-size:9px; font-weight:800; color:#94a3b8; letter-spacing:1.5px;
                    text-transform:uppercase; margin-bottom:16px;">Informations générales</div>

        <div style="display:grid; grid-template-columns:1fr 1fr; gap:14px;">
            <div style="grid-column:1/-1;">
                @include('users._field',['label'=>'Nom complet','name'=>'name','type'=>'text',
                    'value'=>old('name',$user->name),'required'=>true])
            </div>
            <div>
                @include('users._field',['label'=>'Adresse e-mail','name'=>'email','type'=>'email',
                    'value'=>old('email',$user->email),'required'=>true])
            </div>
            @if($user->role !== 'stagiaire')
            <div>
                @include('users._field',['label'=>'CIN','name'=>'cin','type'=>'text',
                    'value'=>old('cin',$user->cin)])
            </div>
            @endif
            <div>
                @include('users._field',['label'=>'Téléphone','name'=>'phone','type'=>'tel','value'=>old('phone',$user->phone)])
            </div>
            <div>
                @include('users._field',['label'=>'Date de naissance','name'=>'date_naissance','type'=>'date',
                    'value'=>old('date_naissance',$user->date_naissance?->format('Y-m-d'))])
            </div>
        </div>

        {{-- Change password --}}
        <div style="margin-top:16px; padding:14px 16px; border-radius:12px;
                    background:#f8fafc; border:1px solid #e2e8f0;">
            <div style="font-size:10px; font-weight:700; color:#94a3b8; text-transform:uppercase;
                        letter-spacing:1px; margin-bottom:10px;">
                Changer le mot de passe
                <span style="font-weight:400; text-transform:none; color:#94a3b8;">(laisser vide pour ne pas modifier)</span>
            </div>
            <div style="display:grid; grid-template-columns:1fr 1fr; gap:14px;">
                <div>
                    @include('users._field',['label'=>'Nouveau mot de passe','name'=>'password',
                        'type'=>'password','placeholder'=>'Min. 8 caractères'])
                </div>
                <div>
                    @include('users._field',['label'=>'Confirmation','name'=>'password_confirmation',
                        'type'=>'password'])
                </div>
            </div>
        </div>

        {{-- Photo --}}
        <div style="margin-top:14px;">
            <label style="font-size:11px; font-weight:700; color:#475569; display:block; margin-bottom:6px;">
                Photo
            </label>
            @if($user->photo)
            <div style="display:flex; align-items:center; gap:10px; margin-bottom:8px;">
                <img src="{{ asset('storage/'.$user->photo) }}" alt="photo"
                     style="width:44px; height:44px; border-radius:10px; object-fit:cover; border:2px solid #e2e8f0;">
                <span style="font-size:11px; color:#64748b;">Photo actuelle — remplacer :</span>
            </div>
            @endif
            <input type="file" name="photo" accept="image/*"
                   style="font-size:12px; color:#475569; cursor:pointer;">
        </div>
    </div>

    {{-- Section Formateur --}}
    <div id="formateur-section"
         style="padding:22px 24px; border-bottom:1px solid #f1f5f9;
                display:{{ old('role',$user->role) === 'formateur' ? 'block' : 'none' }};">

        <div style="font-size:9px; font-weight:800; color:#9333ea; letter-spacing:1.5px;
                    text-transform:uppercase; margin-bottom:16px;">Infos Formateur</div>

        <div style="display:grid; grid-template-columns:1fr 1fr; gap:14px; margin-bottom:20px;">
            {{-- Matricule: read-only, auto-generated at creation --}}
            <div style="grid-column:1/-1;">
                <label style="display:block; font-size:11px; font-weight:700; color:#475569; margin-bottom:5px;">
                    Matricule formateur
                </label>
                <div style="display:flex; align-items:center; gap:10px; height:40px; padding:0 14px;
                            border-radius:10px; background:#f8fafc; border:1.5px solid #e2e8f0;">
                    <span style="font-size:15px;">🪪</span>
                    <span style="font-size:13px; font-weight:700; color:#334155;
                                 font-family:'Courier New',monospace; letter-spacing:1px;">
                        {{ $user->matricule_formateur ?? '—' }}
                    </span>
                    <span style="margin-left:auto; font-size:10px; color:#94a3b8; font-style:italic;">
                        Non modifiable
                    </span>
                </div>
            </div>
            <div>
                @include('users._field',['label'=>"Date d'embauche",'name'=>'date_embauche','type'=>'date',
                    'value'=>old('date_embauche',$user->date_embauche?->format('Y-m-d'))])
            </div>
            <div>
                @include('users._field',['label'=>"Limite d'heures / semaine",'name'=>'nbr_heure_limit',
                    'type'=>'number','value'=>old('nbr_heure_limit',$user->nbr_heure_limit ?? 30)])
            </div>
        </div>

        {{-- Modules --}}
        <div>
            <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:10px;">
                <div>
                    <span style="font-size:11px; font-weight:700; color:#475569;">Modules enseignés</span>
                    <span style="font-size:10px; color:#94a3b8; margin-left:4px;">(spécialisation)</span>
                </div>
                <span id="mod-counter"
                      style="font-size:10px; font-weight:700; padding:2px 10px; border-radius:99px;
                             background:#fdf4ff; color:#9333ea; border:1px solid #e9d5ff;">
                    {{ count($assignedModIds) }} sélectionné(s)
                </span>
            </div>

            <input type="text" id="mod-search" placeholder="Rechercher un module..."
                   oninput="filterMods()"
                   style="width:100%; height:36px; padding:0 12px; border-radius:10px; box-sizing:border-box;
                          border:1.5px solid #e2e8f0; background:#f8fafc; font-size:12px;
                          outline:none; margin-bottom:10px;"
                   onfocus="this.style.borderColor='#9333ea'; this.style.background='white';"
                   onblur="this.style.borderColor='#e2e8f0'; this.style.background='#f8fafc';">

            <div id="mods-grid"
                 style="display:grid; grid-template-columns:repeat(auto-fill,minmax(190px,1fr));
                        gap:8px; max-height:260px; overflow-y:auto; padding:2px;">
                @forelse($modules as $mod)
                @php $chk = in_array($mod->id, old('modules', $assignedModIds)); @endphp
                <label class="mod-card" data-name="{{ strtolower($mod->name) }}"
                       style="display:flex; align-items:center; gap:8px; padding:10px 12px;
                              border-radius:10px; cursor:pointer; transition:all 0.12s; user-select:none;
                              border:1.5px solid {{ $chk ? '#9333ea' : '#e2e8f0' }};
                              background:{{ $chk ? '#fdf4ff' : 'white' }};">
                    <input type="checkbox" name="modules[]" value="{{ $mod->id }}"
                           class="mod-cb" {{ $chk ? 'checked' : '' }}
                           onchange="syncModCard(this); updateModCount()"
                           style="display:none;">
                    <div class="mod-dot"
                         style="width:16px; height:16px; border-radius:5px; flex-shrink:0;
                                transition:all 0.12s; display:flex; align-items:center; justify-content:center;
                                border:2px solid {{ $chk ? '#9333ea' : '#cbd5e1' }};
                                background:{{ $chk ? '#9333ea' : 'white' }};">
                        @if($chk)
                        <svg width="8" height="8" fill="none" stroke="white" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/>
                        </svg>
                        @endif
                    </div>
                    <span style="font-size:11px; font-weight:600; color:#334155; line-height:1.3;">
                        {{ $mod->name }}
                    </span>
                </label>
                @empty
                <p style="font-size:12px; color:#94a3b8; font-style:italic; grid-column:1/-1;">
                    Aucun module disponible.
                </p>
                @endforelse
            </div>
        </div>
    </div>

    {{-- Footer — delete button triggers external form via JS, no nested <form> here --}}
    <div style="padding:16px 24px; background:#f8fafc;
                display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:10px;">

        <button type="button"
                onclick="confirmDelete()"
                style="height:42px; padding:0 16px; border-radius:12px;
                       border:1.5px solid #fecaca; background:white;
                       font-size:12px; font-weight:600; color:#dc2626; cursor:pointer;"
                onmouseover="this.style.background='#fef2f2'"
                onmouseout="this.style.background='white'">
            🗑 Supprimer cet utilisateur
        </button>

        <div style="display:flex; gap:10px;">
            <a href="{{ route('users.management.index') }}"
               style="height:42px; padding:0 18px; border-radius:12px; border:1.5px solid #e2e8f0;
                      background:white; font-size:13px; font-weight:600; color:#64748b;
                      text-decoration:none; display:inline-flex; align-items:center;">
                Annuler
            </a>
            <button type="submit"
                    style="height:42px; padding:0 24px; border-radius:12px; border:none;
                           background:#0a6640; color:white; font-size:13px; font-weight:700;
                           cursor:pointer; box-shadow:0 4px 12px rgba(10,102,64,0.25);"
                    onmouseover="this.style.background='#065f38'"
                    onmouseout="this.style.background='#0a6640'">
                ✓ Enregistrer les modifications
            </button>
        </div>
    </div>

</div>
</form>
{{-- ══════════════════════════════════════════════════════
     DELETE FORM — lives OUTSIDE the update form
     ══════════════════════════════════════════════════════ --}}
<form id="delete-user-form"
      method="POST"
      action="{{ route('users.management.destroy', $user) }}"
      style="display:none;">
    @csrf
    @method('DELETE')
</form>

</div>

<script>
const TAB_COLORS = {
    formateur:    { col:'#9333ea', bg:'#fdf4ff' },
    gestionnaire: { col:'#2563eb', bg:'#eff6ff' },
};

function switchRole(role) {
    document.getElementById('formateur-section').style.display =
        role === 'formateur' ? 'block' : 'none';
    Object.keys(TAB_COLORS).forEach(r => {
        const active = r === role;
        const tab = document.getElementById('tab-' + r);
        const lbl = document.getElementById('tab-label-' + r);
        tab.style.borderColor = active ? TAB_COLORS[r].col : '#e2e8f0';
        tab.style.background  = active ? TAB_COLORS[r].bg  : 'white';
        lbl.style.color       = active ? TAB_COLORS[r].col : '#64748b';
    });
}

function confirmDelete() {
    if (confirm('Supprimer définitivement « {{ addslashes($user->name) }} » ?')) {
        document.getElementById('delete-user-form').submit();
    }
}

function syncModCard(cb) {
    const label = cb.closest('label');
    const dot   = label.querySelector('.mod-dot');
    if (cb.checked) {
        label.style.borderColor = '#9333ea'; label.style.background = '#fdf4ff';
        dot.style.borderColor   = '#9333ea'; dot.style.background   = '#9333ea';
        dot.innerHTML = '<svg width="8" height="8" fill="none" stroke="white" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>';
    } else {
        label.style.borderColor = '#e2e8f0'; label.style.background = 'white';
        dot.style.borderColor   = '#cbd5e1'; dot.style.background   = 'white';
        dot.innerHTML           = '';
    }
}

function updateModCount() {
    const n = document.querySelectorAll('.mod-cb:checked').length;
    document.getElementById('mod-counter').textContent = n + ' sélectionné(s)';
}

function filterMods() {
    const q = document.getElementById('mod-search').value.toLowerCase();
    document.querySelectorAll('.mod-card').forEach(c =>
        c.style.display = c.dataset.name.includes(q) ? '' : 'none'
    );
}
</script>
@endsection