@extends('layouts.app')
@section('title', 'Gestion des utilisateurs')
@section('page-title', 'Gestion des utilisateurs')

@section('content')
<div style="font-family:'Segoe UI',system-ui,sans-serif;">

{{-- ════ FLASH ════ --}}
@if(session('success'))
    <div style="margin-bottom:16px; padding:12px 16px; border-radius:12px; font-size:13px;
                display:flex; align-items:center; gap:8px;
                background:#f0fdf4; border:1px solid #bbf7d0; color:#15803d;">
        <svg width="16" height="16" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
        </svg>
        {{ session('success') }}
    </div>
@endif

{{-- ════ HEADER ════ --}}
<div style="display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:12px; margin-bottom:20px;">
    <div>
        <h1 style="font-size:20px; font-weight:800; color:#0f172a; margin:0;">Utilisateurs</h1>
        <p style="font-size:12px; color:#64748b; margin:4px 0 0;">
            Gérez les rôles de vos formateurs et gestionnaires
        </p>
    </div>
    {{-- Stats --}}
    <div style="display:flex; gap:10px; flex-wrap:wrap;">
        @php
            $counts = [
                'admin'        => ['count' => $users->total(), 'label' => 'Total', 'color' => '#0a6640', 'bg' => '#e8f5ee'],
                'gestionnaire' => ['count' => \App\Models\User::where('role','gestionnaire')->count(), 'label' => 'Gestionnaires', 'color' => '#2563eb', 'bg' => '#eff6ff'],
                'formateur'    => ['count' => \App\Models\User::where('role','formateur')->count(), 'label' => 'Formateurs', 'color' => '#9333ea', 'bg' => '#fdf4ff'],
            ];
        @endphp
        @foreach([['#0a6640','#e8f5ee',\App\Models\User::whereIn('role',['admin','gestionnaire','formateur'])->count(),'Total'],['#2563eb','#eff6ff',\App\Models\User::where('role','gestionnaire')->count(),'Gestionnaires'],['#9333ea','#fdf4ff',\App\Models\User::where('role','formateur')->count(),'Formateurs']] as [$col,$bg,$cnt,$lbl])
        <div style="padding:8px 14px; border-radius:12px; background:{{ $bg }}; border:1px solid {{ $col }}20; text-align:center;">
            <div style="font-size:18px; font-weight:800; color:{{ $col }};">{{ $cnt }}</div>
            <div style="font-size:9px; font-weight:700; color:{{ $col }}80; text-transform:uppercase; letter-spacing:0.5px;">{{ $lbl }}</div>
        </div>
        @endforeach
    </div>
</div>

{{-- ════ FILTERS ════ --}}
<form method="GET" action="{{ route('users.management.index') }}"
      style="display:flex; gap:10px; margin-bottom:16px; flex-wrap:wrap;">
    <input type="text" name="search" value="{{ $search }}"
           placeholder="Rechercher par nom ou email..."
           style="flex:1; min-width:200px; height:40px; padding:0 14px; border-radius:10px;
                  border:1.5px solid #e2e8f0; background:#f8fafc; font-size:13px;
                  color:#1e293b; outline:none; box-sizing:border-box;"
           onfocus="this.style.borderColor='#0a6640'; this.style.background='white';"
           onblur="this.style.borderColor='#e2e8f0'; this.style.background='#f8fafc';">

    <select name="role"
            style="height:40px; padding:0 12px; border-radius:10px; border:1.5px solid #e2e8f0;
                   background:#f8fafc; font-size:13px; color:#475569; outline:none; cursor:pointer;">
        <option value="">Tous les rôles</option>
        <option value="admin"        {{ $filterRole==='admin'        ? 'selected':'' }}>Admin</option>
        <option value="gestionnaire" {{ $filterRole==='gestionnaire' ? 'selected':'' }}>Gestionnaire</option>
        <option value="formateur"    {{ $filterRole==='formateur'    ? 'selected':'' }}>Formateur</option>
    </select>

    <button type="submit"
            style="height:40px; padding:0 16px; border-radius:10px; border:none;
                   background:#0a6640; color:white; font-size:13px; font-weight:600; cursor:pointer;">
        Filtrer
    </button>
    @if($search || $filterRole)
    <a href="{{ route('users.management.index') }}"
       style="height:40px; padding:0 14px; border-radius:10px; border:1.5px solid #e2e8f0;
              background:white; color:#64748b; font-size:13px; font-weight:600;
              text-decoration:none; display:flex; align-items:center;">
        Réinitialiser
    </a>
    @endif
</form>

{{-- ════ TABLE ════ --}}
<div style="background:white; border-radius:16px; border:1px solid #e2e8f0;
            box-shadow:0 2px 12px rgba(0,0,0,0.06); overflow:hidden;">

    {{-- Table header --}}
    <div style="display:grid; grid-template-columns:1fr 200px 160px 160px 120px;
                padding:10px 18px; background:#f8fafc; border-bottom:1px solid #e2e8f0;">
        <span style="font-size:9px; font-weight:800; color:#94a3b8; letter-spacing:1.5px; text-transform:uppercase;">Utilisateur</span>
        <span style="font-size:9px; font-weight:800; color:#94a3b8; letter-spacing:1.5px; text-transform:uppercase;">Rôle système</span>
        <span style="font-size:9px; font-weight:800; color:#94a3b8; letter-spacing:1.5px; text-transform:uppercase;">Rôle Spatie</span>
        <span style="font-size:9px; font-weight:800; color:#94a3b8; letter-spacing:1.5px; text-transform:uppercase;">Permissions</span>
        <span style="font-size:9px; font-weight:800; color:#94a3b8; letter-spacing:1.5px; text-transform:uppercase;">Actions</span>
    </div>

    {{-- Rows --}}
    @forelse($users as $user)
        @php
            $roleColors = [
                'admin'        => ['bg'=>'#e8f5ee','text'=>'#0a6640','border'=>'#0a664030'],
                'gestionnaire' => ['bg'=>'#eff6ff','text'=>'#1e40af','border'=>'#2563eb30'],
                'formateur'    => ['bg'=>'#fdf4ff','text'=>'#6b21a8','border'=>'#9333ea30'],
            ];
            $rc = $roleColors[$user->role] ?? ['bg'=>'#f8fafc','text'=>'#334155','border'=>'#e2e8f0'];
            $spatieRoleNames = $user->roles->pluck('name')->implode(', ');
            $permCount = $user->getAllPermissions()->count();
            $initials = strtoupper(substr($user->name, 0, 1)) . strtoupper(substr(explode(' ', $user->name)[1] ?? '', 0, 1));
        @endphp
        <div style="display:grid; grid-template-columns:1fr 200px 160px 160px 120px;
                    padding:12px 18px; border-bottom:1px solid #f1f5f9; align-items:center;
                    transition:background 0.12s;"
             onmouseover="this.style.background='#fafbfe'"
             onmouseout="this.style.background='white'">

            {{-- User info --}}
            <div style="display:flex; align-items:center; gap:10px;">
                <div style="width:36px; height:36px; border-radius:10px; flex-shrink:0;
                            background:{{ $rc['bg'] }}; border:1px solid {{ $rc['border'] }};
                            display:flex; align-items:center; justify-content:center;
                            font-size:12px; font-weight:800; color:{{ $rc['text'] }};">
                    {{ $initials }}
                </div>
                <div>
                    <div style="font-size:13px; font-weight:700; color:#0f172a;">{{ $user->name }}</div>
                    <div style="font-size:11px; color:#64748b; margin-top:1px;">{{ $user->email }}</div>
                </div>
            </div>

            {{-- Rôle enum (système) --}}
            <div>
                <span style="font-size:11px; font-weight:700; padding:4px 10px; border-radius:8px;
                             background:{{ $rc['bg'] }}; color:{{ $rc['text'] }}; border:1px solid {{ $rc['border'] }};
                             text-transform:capitalize;">
                    {{ $user->role }}
                </span>
            </div>

            {{-- Rôle Spatie --}}
            <div>
                @if($spatieRoleNames)
                    <span style="font-size:11px; font-weight:600; padding:4px 10px; border-radius:8px;
                                 background:#f0fdf4; color:#15803d; border:1px solid #bbf7d0;">
                        ✓ {{ $spatieRoleNames }}
                    </span>
                @else
                    <span style="font-size:11px; color:#94a3b8; font-style:italic;">Aucun rôle</span>
                @endif
            </div>

            {{-- Nb permissions --}}
            <div>
                <span style="font-size:11px; font-weight:700; color:#475569;">
                    {{ $permCount }} permission{{ $permCount > 1 ? 's' : '' }}
                </span>
            </div>

            {{-- Actions --}}
            <div style="display:flex; gap:6px;">
                <button onclick="openRoleModal({{ $user->id }}, '{{ addslashes($user->name) }}', '{{ $spatieRoleNames }}')"
                        style="font-size:11px; font-weight:600; color:#1e40af; padding:5px 10px;
                               border-radius:8px; background:#eff6ff; border:1px solid #bfdbfe;
                               cursor:pointer; white-space:nowrap;">
                    ✎ Rôle
                </button>
            </div>
        </div>
    @empty
        <div style="padding:48px; text-align:center;">
            <p style="font-size:13px; color:#94a3b8;">Aucun utilisateur trouvé.</p>
        </div>
    @endforelse
</div>

{{-- Pagination --}}
@if($users->hasPages())
    <div style="margin-top:16px; display:flex; justify-content:center;">
        {{ $users->links() }}
    </div>
@endif

{{-- ════ MODAL MODIFIER RÔLE ════ --}}
<div id="role-modal" style="display:none; position:fixed; inset:0; z-index:60;
     background:rgba(15,23,42,0.5); backdrop-filter:blur(4px);
     align-items:center; justify-content:center;"
     onclick="if(event.target===this)closeRoleModal()">
    <div style="background:white; border-radius:20px; width:100%; max-width:420px;
                margin:16px; padding:24px; box-shadow:0 24px 60px rgba(0,0,0,0.18);">

        {{-- Header --}}
        <div style="display:flex; align-items:center; justify-content:space-between;
                    margin-bottom:14px; padding-bottom:14px; border-bottom:2px solid #0a6640;">
            <div style="display:flex; align-items:center; gap:10px;">
                <div style="width:40px; height:40px; border-radius:12px; background:#e8f5ee;
                            display:flex; align-items:center; justify-content:center; flex-shrink:0;">
                    <svg width="18" height="18" fill="none" stroke="#0a6640" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                    </svg>
                </div>
                <div>
                    <div style="font-size:14px; font-weight:800; color:#1e293b;">Modifier le rôle</div>
                    <div id="modal-user-name" style="font-size:10px; color:#64748b; margin-top:1px;"></div>
                </div>
            </div>
            <button onclick="closeRoleModal()"
                    style="width:28px;height:28px;border-radius:8px;border:none;background:#f1f5f9;
                           color:#64748b;font-size:16px;cursor:pointer;display:flex;align-items:center;justify-content:center;">
                ×
            </button>
        </div>

        {{-- Current role info --}}
        <div style="padding:10px 14px; border-radius:10px; background:#f8fafc;
                    border:1px solid #e2e8f0; margin-bottom:16px; font-size:12px; color:#475569;">
            Rôle Spatie actuel :
            <span id="modal-current-role"
                  style="font-weight:700; color:#0a6640;"></span>
        </div>

        {{-- Role selection --}}
        <form id="role-form" method="POST" style="display:flex; flex-direction:column; gap:14px;">
            @csrf @method('PATCH')
            <input type="hidden" name="search" value="{{ $search }}">
            <input type="hidden" name="role" value="{{ $filterRole }}">

            <div>
                <label style="display:block; font-size:9px; font-weight:800; color:#94a3b8;
                              letter-spacing:1.5px; text-transform:uppercase; margin-bottom:8px;">
                    Nouveau rôle Spatie
                </label>

                {{-- Role cards --}}
                <div style="display:flex; flex-direction:column; gap:8px;" id="role-options">
                    @foreach($spatieRoles as $sRole)
                        @php
                            $srColors = [
                                'admin'        => ['bg'=>'#e8f5ee','border'=>'#0a6640','text'=>'#065f38'],
                                'gestionnaire' => ['bg'=>'#eff6ff','border'=>'#2563eb','text'=>'#1e40af'],
                                'formateur'    => ['bg'=>'#fdf4ff','border'=>'#9333ea','text'=>'#6b21a8'],
                                'stagiaire'    => ['bg'=>'#fff7ed','border'=>'#ea580c','text'=>'#9a3412'],
                            ];
                            $src = $srColors[$sRole->name] ?? ['bg'=>'#f8fafc','border'=>'#64748b','text'=>'#334155'];
                            $permNames = $sRole->permissions->pluck('name')->take(4)->implode(', ');
                            $more = max(0, $sRole->permissions->count() - 4);
                        @endphp
                        <label style="display:flex; align-items:center; gap:10px; cursor:pointer;
                                      padding:10px 14px; border-radius:12px; border:1.5px solid #e2e8f0;
                                      background:white; transition:all 0.15s; user-select:none;"
                               class="role-option-label"
                               data-color="{{ $src['border'] }}"
                               data-bg="{{ $src['bg'] }}">
                            <input type="radio" name="spatie_role" value="{{ $sRole->name }}"
                                   class="role-radio"
                                   style="display:none;"
                                   onchange="styleRoleLabel(this)">
                            <div class="role-radio-dot"
                                 style="width:18px; height:18px; border-radius:50%; flex-shrink:0;
                                        border:2px solid #cbd5e1; background:white; transition:all 0.15s;
                                        display:flex; align-items:center; justify-content:center;">
                            </div>
                            <div style="flex:1; min-width:0;">
                                <div style="font-size:13px; font-weight:700; color:#1e293b; text-transform:capitalize;">
                                    {{ $sRole->name }}
                                </div>
                                @if($sRole->permissions->count())
                                <div style="font-size:10px; color:#64748b; margin-top:2px; overflow:hidden;
                                            text-overflow:ellipsis; white-space:nowrap;">
                                    {{ $permNames }}{{ $more > 0 ? ' +' . $more : '' }}
                                </div>
                                @else
                                <div style="font-size:10px; color:#94a3b8; margin-top:2px; font-style:italic;">
                                    Aucune permission
                                </div>
                                @endif
                            </div>
                            <span class="role-perm-count"
                                  style="font-size:9px; font-weight:700; padding:2px 8px; border-radius:99px;
                                         background:{{ $src['bg'] }}; color:{{ $src['text'] }};">
                                {{ $sRole->permissions->count() }}p
                            </span>
                        </label>
                    @endforeach
                </div>
            </div>

            <div style="display:flex; gap:10px; margin-top:4px;">
                <button type="button" onclick="closeRoleModal()"
                        style="flex:1; height:44px; border-radius:12px; border:1.5px solid #e2e8f0;
                               background:white; font-size:13px; font-weight:600; color:#64748b; cursor:pointer;"
                        onmouseover="this.style.background='#f8fafc'"
                        onmouseout="this.style.background='white'">
                    Annuler
                </button>
                <button type="submit"
                        style="flex:1; height:44px; border-radius:12px; border:none;
                               background:#0a6640; font-size:13px; font-weight:700; color:white;
                               cursor:pointer; box-shadow:0 4px 12px rgba(10,102,64,0.3);">
                    Enregistrer
                </button>
            </div>
        </form>
    </div>
</div>

</div>

<script>
function openRoleModal(userId, userName, currentRole) {
    document.getElementById('modal-user-name').textContent = userName;
    document.getElementById('modal-current-role').textContent = currentRole || 'Aucun';
    document.getElementById('role-form').action = '/users/' + userId + '/role';

    // Reset all labels
    document.querySelectorAll('.role-option-label').forEach(lbl => {
        lbl.style.background   = 'white';
        lbl.style.borderColor  = '#e2e8f0';
        const dot = lbl.querySelector('.role-radio-dot');
        dot.style.borderColor  = '#cbd5e1';
        dot.style.background   = 'white';
        dot.innerHTML          = '';
    });

    // Pre-select current role if matches
    document.querySelectorAll('.role-radio').forEach(r => {
        r.checked = false;
        if (currentRole && r.value === currentRole.trim()) {
            r.checked = true;
            styleRoleLabel(r);
        }
    });

    document.getElementById('role-modal').style.display = 'flex';
}

function closeRoleModal() {
    document.getElementById('role-modal').style.display = 'none';
}

function styleRoleLabel(radio) {
    // Reset all
    document.querySelectorAll('.role-option-label').forEach(lbl => {
        lbl.style.background  = 'white';
        lbl.style.borderColor = '#e2e8f0';
        const dot = lbl.querySelector('.role-radio-dot');
        dot.style.borderColor = '#cbd5e1';
        dot.style.background  = 'white';
        dot.innerHTML         = '';
    });

    // Style selected
    const label = radio.closest('label');
    const color = label.dataset.color;
    const bg    = label.dataset.bg;
    label.style.background  = bg;
    label.style.borderColor = color;
    const dot = label.querySelector('.role-radio-dot');
    dot.style.borderColor   = color;
    dot.style.background    = color;
    dot.innerHTML = '<svg width="8" height="8" fill="none" stroke="white" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>';
}
</script>
@endsection