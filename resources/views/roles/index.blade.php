@extends('layouts.app')
@section('title', 'Gestion des rôles')
@section('page-title', 'Gestion des rôles')

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
@if(session('error'))
    <div style="margin-bottom:16px; padding:12px 16px; border-radius:12px; font-size:13px;
                display:flex; align-items:center; gap:8px;
                background:#fff1f2; border:1px solid #fecdd3; color:#be123c;">
        <svg width="16" height="16" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm-1-5h2v2h-2zm0-8h2v6h-2z" clip-rule="evenodd"/>
        </svg>
        {{ session('error') }}
    </div>
@endif

{{-- ════ HEADER ════ --}}
<div style="display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:12px; margin-bottom:24px;">
    <div>
        <h1 style="font-size:20px; font-weight:800; color:#0f172a; margin:0;">Rôles & Permissions</h1>
        <p style="font-size:12px; color:#64748b; margin:4px 0 0;">Gérez les rôles et leurs permissions associées</p>
    </div>
    {{-- APRÈS : Vérification par rôle admin --}}
    @if(Auth::user()->role === 'admin')
    <a href="{{ route('roles.create') }}"
       style="display:inline-flex; align-items:center; gap:6px; padding:9px 16px;
              border-radius:10px; background:#0a6640; color:white; font-size:12px;
              font-weight:700; text-decoration:none; border:none;
              box-shadow:0 4px 12px rgba(10,102,64,0.3);">
        <svg width="13" height="13" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/>
        </svg>
        Nouveau rôle
    </a>
    @endif
</div>

{{-- ════ ROLES GRID ════ --}}
<div style="display:grid; grid-template-columns:repeat(auto-fill, minmax(300px, 1fr)); gap:16px;">
    @forelse($roles as $role)
        @php
            $isSystem = in_array($role->name, ['admin','gestionnaire','formateur','stagiaire']);
            $colors = [
                'admin'        => ['bg'=>'#e8f5ee','border'=>'#0a6640','text'=>'#065f38','badge'=>'#0a6640'],
                'gestionnaire' => ['bg'=>'#eff6ff','border'=>'#2563eb','text'=>'#1e40af','badge'=>'#2563eb'],
                'formateur'    => ['bg'=>'#fdf4ff','border'=>'#9333ea','text'=>'#6b21a8','badge'=>'#9333ea'],
                'stagiaire'    => ['bg'=>'#fff7ed','border'=>'#ea580c','text'=>'#9a3412','badge'=>'#ea580c'],
            ];
            $c = $colors[$role->name] ?? ['bg'=>'#f8fafc','border'=>'#64748b','text'=>'#334155','badge'=>'#64748b'];
        @endphp
        <div style="background:white; border-radius:16px; border:1px solid #e2e8f0;
                    overflow:hidden; transition:box-shadow 0.15s;"
             onmouseover="this.style.boxShadow='0 4px 20px rgba(0,0,0,0.08)'"
             onmouseout="this.style.boxShadow='none'">

            {{-- Card header --}}
            <div style="padding:16px 18px; background:{{ $c['bg'] }}; border-bottom:1px solid {{ $c['border'] }}20;
                        display:flex; align-items:center; justify-content:space-between;">
                <div style="display:flex; align-items:center; gap:10px;">
                    <div style="width:36px; height:36px; border-radius:10px; background:{{ $c['border'] }};
                                display:flex; align-items:center; justify-content:center;">
                        <svg width="16" height="16" fill="none" stroke="white" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                        </svg>
                    </div>
                    <div>
                        <div style="font-size:14px; font-weight:800; color:{{ $c['text'] }}; text-transform:capitalize;">
                            {{ $role->name }}
                        </div>
                        <div style="font-size:10px; color:{{ $c['text'] }}80; margin-top:1px;">
                            {{ $role->permissions->count() }} permission{{ $role->permissions->count() > 1 ? 's' : '' }}
                        </div>
                    </div>
                </div>
                @if($isSystem)
                    <span style="font-size:9px; font-weight:700; background:{{ $c['border'] }}15;
                                 color:{{ $c['text'] }}; padding:3px 9px; border-radius:99px;
                                 border:1px solid {{ $c['border'] }}30;">
                        Système
                    </span>
                @endif
            </div>

            {{-- Permissions list --}}
            <div style="padding:14px 18px;">
                @if($role->permissions->isEmpty())
                    <p style="font-size:11px; color:#94a3b8; font-style:italic; margin:0;">
                        Aucune permission assignée
                    </p>
                @else
                    <div style="display:flex; flex-wrap:wrap; gap:5px;">
                        @foreach($role->permissions->sortBy('name') as $perm)
                            @php
                                $prefix = explode('-', $perm->name)[0];
                                $action = explode('-', $perm->name)[1] ?? '';
                                $permColors = [
                                    'emploi'    => ['bg'=>'#eff6ff','text'=>'#1e40af'],
                                    'user'      => ['bg'=>'#f0fdf4','text'=>'#166534'],
                                    'groupe'    => ['bg'=>'#fdf4ff','text'=>'#6b21a8'],
                                    'role'      => ['bg'=>'#fff1f2','text'=>'#9f1239'],
                                    'edu'       => ['bg'=>'#fff7ed','text'=>'#9a3412'],
                                ];
                                $pc = $permColors[$prefix] ?? ['bg'=>'#f8fafc','text'=>'#334155'];
                            @endphp
                            <span style="font-size:9px; font-weight:700; padding:3px 8px; border-radius:6px;
                                         background:{{ $pc['bg'] }}; color:{{ $pc['text'] }};">
                                {{ $perm->name }}
                            </span>
                        @endforeach
                    </div>
                @endif
            </div>

            {{-- APRÈS : Actions avec vérification par rôle admin --}}
            <div style="padding:12px 18px; border-top:1px solid #f1f5f9;
                        display:flex; align-items:center; gap:8px;">
                @if(Auth::user()->role === 'admin')
                    <a href="{{ route('roles.show', $role) }}"
                       style="font-size:11px; font-weight:600; color:#64748b; text-decoration:none;
                              padding:5px 10px; border-radius:8px; border:1px solid #e2e8f0; background:white;"
                       onmouseover="this.style.background='#f8fafc'"
                       onmouseout="this.style.background='white'">
                       Détails
                    </a>
                    <a href="{{ route('roles.edit', $role) }}"
                       style="font-size:11px; font-weight:600; color:#1e40af; text-decoration:none;
                              padding:5px 10px; border-radius:8px; background:#eff6ff; border:1px solid #bfdbfe;">
                       ✎ Modifier
                    </a>
                    @if(!$isSystem)
                        <button onclick="openDeleteRoleModal('{{ route('roles.destroy', $role) }}', '{{ $role->name }}')"
                                style="font-size:11px; font-weight:600; color:#dc2626; padding:5px 10px;
                                       border-radius:8px; background:#fee2e2; border:1px solid #fecaca;
                                       cursor:pointer; margin-left:auto;">
                            ✕ Supprimer
                        </button>
                    @endif
                @endif
            </div>
        </div>
    @empty
        <div style="grid-column:1/-1; padding:48px; text-align:center;
                    background:white; border-radius:16px; border:1px solid #e2e8f0;">
            <p style="font-size:14px; color:#64748b;">Aucun rôle trouvé.</p>
        </div>
    @endforelse
</div>

{{-- ════ DELETE MODAL ════ --}}
<div id="delete-role-modal" style="display:none; position:fixed; inset:0; z-index:60;
     background:rgba(15,23,42,0.5); backdrop-filter:blur(4px);
     align-items:center; justify-content:center;"
     onclick="if(event.target===this)closeDeleteRoleModal()">
    <div style="background:white; border-radius:20px; width:100%; max-width:400px;
                margin:16px; padding:24px; box-shadow:0 24px 60px rgba(0,0,0,0.18);">
        <div style="display:flex; align-items:center; justify-content:space-between;
                    margin-bottom:14px; padding-bottom:14px; border-bottom:2px solid #dc2626;">
            <div style="display:flex; align-items:center; gap:10px;">
                <div style="width:40px; height:40px; border-radius:12px; background:#fee2e2;
                            display:flex; align-items:center; justify-content:center;">
                    <svg width="18" height="18" fill="none" stroke="#dc2626" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                    </svg>
                </div>
                <div>
                    <div style="font-size:14px; font-weight:800; color:#1e293b;">Supprimer le rôle ?</div>
                    <div style="font-size:10px; color:#64748b; margin-top:1px;" id="delete-role-name"></div>
                </div>
            </div>
            <button onclick="closeDeleteRoleModal()"
                    style="width:28px;height:28px;border-radius:8px;border:none;background:#f1f5f9;
                           color:#64748b;font-size:16px;cursor:pointer;">×</button>
        </div>
        <div style="font-size:12px; color:#9f1239; line-height:1.6; margin-bottom:18px;
                    padding:12px 14px; border-radius:12px; background:#fff1f2; border:1px solid #fecdd3;">
            Les utilisateurs ayant ce rôle perdront leurs permissions associées.
        </div>
        <div style="display:flex; gap:10px;">
            <button onclick="closeDeleteRoleModal()"
                    style="flex:1; height:44px; border-radius:12px; border:1.5px solid #e2e8f0;
                           background:white; font-size:13px; font-weight:600; color:#64748b; cursor:pointer;">
                Annuler
            </button>
            <form id="delete-role-form" method="POST" style="flex:1;">
                @csrf @method('DELETE')
                <button type="submit"
                        style="width:100%; height:44px; border-radius:12px; border:none;
                               background:#dc2626; font-size:13px; font-weight:700; color:white;
                               cursor:pointer; box-shadow:0 4px 12px rgba(220,38,38,0.3);">
                    Supprimer
                </button>
            </form>
        </div>
    </div>
</div>

</div>

<script>
function openDeleteRoleModal(action, name) {
    document.getElementById('delete-role-form').action = action;
    document.getElementById('delete-role-name').textContent = 'Rôle : ' + name;
    document.getElementById('delete-role-modal').style.display = 'flex';
}
function closeDeleteRoleModal() {
    document.getElementById('delete-role-modal').style.display = 'none';
}
</script>
@endsection