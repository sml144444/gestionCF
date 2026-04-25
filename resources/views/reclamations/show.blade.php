{{-- resources/views/reclamations/show.blade.php --}}
@extends('layouts.app')
@section('title', 'Réclamation #' . $reclamation->id)
@section('page-title', 'Réclamation')

@section('content')
@php
    $user      = Auth::user();
    $role      = $user->role;
    $isAdmin   = $user->can('reclamation-manage');
    $isMine    = $reclamation->id_user === $user->id;
    $canReply  = $reclamation->canReply($user);
    $sc        = $reclamation->statusConfig;
    $tc        = $reclamation->typeConfig;

    $palettes = [
        'admin' => [
            'gradient' => 'linear-gradient(135deg,#0a6640 0%,#1a8c56 100%)',
            'primary'  => '#0a6640',
            'light'    => '#e8f5ee',
            'lighter'  => '#f0fdf4',
            'text'     => '#065f38',
            'border'   => '#bbf7d0',
            'shadow'   => 'rgba(10,102,64,0.15)',
            'btn_bg'   => 'linear-gradient(135deg,#0a6640,#1a8c56)',
        ],
        'gestionnaire' => [
            'gradient' => 'linear-gradient(135deg,#1e293b 0%,#334155 100%)',
            'primary'  => '#1e293b',
            'light'    => '#f1f5f9',
            'lighter'  => '#f8fafc',
            'text'     => '#1e293b',
            'border'   => '#cbd5e1',
            'shadow'   => 'rgba(30,41,59,0.15)',
            'btn_bg'   => 'linear-gradient(135deg,#1e293b,#334155)',
        ],
        'formateur' => [
            'gradient' => 'linear-gradient(135deg,#1a4f8a 0%,#2563eb 100%)',
            'primary'  => '#1a4f8a',
            'light'    => '#eff6ff',
            'lighter'  => '#f0f7ff',
            'text'     => '#1e40af',
            'border'   => '#bfdbfe',
            'shadow'   => 'rgba(26,79,138,0.15)',
            'btn_bg'   => 'linear-gradient(135deg,#1a4f8a,#2563eb)',
        ],
        'stagiaire' => [
            'gradient' => 'linear-gradient(135deg,#1a4f8a 0%,#2563eb 100%)',
            'primary'  => '#1a4f8a',
            'light'    => '#eff6ff',
            'lighter'  => '#f0f7ff',
            'text'     => '#1e40af',
            'border'   => '#bfdbfe',
            'shadow'   => 'rgba(26,79,138,0.15)',
            'btn_bg'   => 'linear-gradient(135deg,#1a4f8a,#2563eb)',
        ],
    ];

    $p = $palettes[$role] ?? $palettes['stagiaire'];

    $roleLabels = [
        'admin'        => ['label'=>'Admin',        'bg'=>'#d1fae5','color'=>'#065f38'],
        'gestionnaire' => ['label'=>'Gestionnaire', 'bg'=>'#f1f5f9','color'=>'#1e293b'],
        'formateur'    => ['label'=>'Formateur',    'bg'=>'#eff6ff','color'=>'#1e40af'],
        'stagiaire'    => ['label'=>'Stagiaire',    'bg'=>'#f5f3ff','color'=>'#6d28d9'],
    ];
@endphp

<style>
:root {
    --gr:  {{ $p['gradient'] }};
    --pr:  {{ $p['primary'] }};
    --lt:  {{ $p['light'] }};
    --ltr: {{ $p['lighter'] }};
    --tx:  {{ $p['text'] }};
    --bd:  {{ $p['border'] }};
    --sh:  {{ $p['shadow'] }};
}

* { box-sizing:border-box; }
.rc-show { font-family:'Segoe UI',system-ui,sans-serif; max-width:1200px; margin:0 auto; }

/* ── HERO ── */
.rc-hero { background:var(--gr); border-radius:20px; padding:24px 28px; margin-bottom:20px;
           display:flex; align-items:center; justify-content:space-between; gap:14px; flex-wrap:wrap;
           position:relative; overflow:hidden; }
.rc-hero::after { content:''; position:absolute; right:-40px; top:-40px; width:200px; height:200px;
                  border-radius:50%; background:rgba(255,255,255,0.06); pointer-events:none; }
.rc-hero-back { background:rgba(255,255,255,0.15); border:1px solid rgba(255,255,255,0.25);
                color:#fff; font-size:11px; font-weight:700; padding:7px 16px; border-radius:99px;
                text-decoration:none; transition:background .15s; white-space:nowrap; }
.rc-hero-back:hover { background:rgba(255,255,255,0.22); }

/* ── LAYOUT ── */
.rc-body { display:grid; grid-template-columns:1fr 320px; gap:18px; align-items:start; }
@media(max-width:860px) { .rc-body { grid-template-columns:1fr; } }

/* ── SIDEBAR ── */
.rc-sidebar { display:flex; flex-direction:column; gap:14px; }
.rc-card { background:white; border-radius:18px; border:1px solid #e2e8f0; overflow:hidden; }
.rc-card-head { padding:13px 16px; border-bottom:1px solid #f1f5f9;
                font-size:10px; font-weight:800; color:#94a3b8;
                text-transform:uppercase; letter-spacing:.9px; }
.rc-info-row { display:flex; justify-content:space-between; align-items:center;
               padding:10px 16px; border-bottom:1px solid #f8fafc; gap:8px; }
.rc-info-row:last-child { border-bottom:none; }
.rc-info-label { font-size:11px; color:#94a3b8; font-weight:600; flex-shrink:0; }
.rc-info-val   { font-size:11px; color:#1e293b; font-weight:700; text-align:right; }

/* Status buttons */
.btn-status { width:100%; margin:0; padding:9px; border-radius:10px; border:1.5px solid;
              font-size:11px; font-weight:700; cursor:pointer; transition:all .15s; background:white; }

/* Assign select */
.assign-select { width:100%; padding:9px 12px; border:1.5px solid #e2e8f0; border-radius:10px;
                 font-size:12px; color:#1e293b; background:white; outline:none; cursor:pointer;
                 transition:border-color .15s; }
.assign-select:focus { border-color:var(--bd); }
.btn-assign { width:100%; margin-top:8px; padding:9px; border-radius:10px;
              background:var(--gr); color:white; border:none; font-size:12px;
              font-weight:700; cursor:pointer; transition:opacity .15s; }
.btn-assign:hover { opacity:.88; }

/* ── CONVERSATION ── */
.rc-thread { background:white; border-radius:18px; border:1px solid #e2e8f0; overflow:hidden; }
.rc-thread-head { padding:14px 20px; border-bottom:1px solid #f1f5f9;
                  display:flex; align-items:center; justify-content:space-between; }

/* Original ticket */
.ticket-desc { margin:20px; padding:16px 20px; background:#f8fafc;
               border-radius:14px; border:1px solid #e2e8f0;
               border-left:4px solid var(--pr); }
.ticket-desc-header { display:flex; align-items:center; gap:10px; margin-bottom:10px; }
.ticket-desc-body   { font-size:13px; color:#374151; line-height:1.7; white-space:pre-wrap; }

/* Messages area */
.messages-area { padding:8px 20px 20px; display:flex; flex-direction:column; gap:16px; }

/* Bubble: theirs */
.bubble-left  { display:flex; gap:10px; align-items:flex-end; max-width:80%; }
.bubble-right { display:flex; justify-content:flex-end; }
.bubble-right-inner { max-width:80%; }

.bubble-body-left {
    background:white; border:1px solid #e2e8f0;
    border-radius:4px 16px 16px 16px;
    padding:12px 16px; font-size:13px; color:#1e293b;
    line-height:1.6; word-break:break-word;
    box-shadow:0 1px 4px rgba(0,0,0,0.05);
}
.bubble-body-right {
    background:var(--gr);
    border-radius:16px 4px 16px 16px;
    padding:12px 16px; font-size:13px; color:white;
    line-height:1.6; word-break:break-word;
    box-shadow:0 2px 8px var(--sh);
}
.bubble-meta  { font-size:10px; color:#94a3b8; margin-bottom:5px; display:flex; align-items:center; gap:6px; }
.bubble-meta-right { text-align:right; justify-content:flex-end; }
.avatar-sm    { width:28px; height:28px; border-radius:8px; background:#eff6ff;
                display:flex; align-items:center; justify-content:center;
                font-size:9px; font-weight:800; color:#1e40af; flex-shrink:0; }
.role-chip    { font-size:9px; font-weight:700; padding:1px 7px; border-radius:5px; white-space:nowrap; }

/* Divider */
.day-divider  { display:flex; align-items:center; gap:10px; margin:8px 0; }
.day-divider::before, .day-divider::after { content:''; flex:1; height:1px; background:#f1f5f9; }
.day-divider span { font-size:10px; color:#cbd5e1; font-weight:600; white-space:nowrap; }

/* Reply form */
.reply-area { border-top:1px solid #f1f5f9; padding:16px 20px; background:#fafbfd; }
.reply-textarea { width:100%; padding:12px 14px; border:1.5px solid #e2e8f0; border-radius:14px;
                  font-size:13px; font-family:inherit; color:#1e293b; background:white;
                  outline:none; resize:vertical; min-height:90px; line-height:1.6;
                  transition:border-color .15s, box-shadow .15s; }
.reply-textarea:focus { border-color:var(--bd); box-shadow:0 0 0 3px rgba(26,79,138,0.08); }
.reply-submit { margin-top:10px; display:flex; justify-content:flex-end; }
.btn-send { padding:10px 24px; border-radius:12px; background:var(--gr); color:white;
            border:none; font-size:13px; font-weight:700; cursor:pointer;
            display:inline-flex; align-items:center; gap:8px;
            box-shadow:0 2px 8px var(--sh); transition:opacity .15s; }
.btn-send:hover { opacity:.88; }
.reply-closed { padding:14px 20px; background:#fef3c7; border-top:1px solid #fde68a;
                font-size:12px; color:#92400e; font-weight:600;
                display:flex; align-items:center; gap:8px; }

/* Flash */
.flash-ok  { display:flex; align-items:center; gap:12px; padding:14px 18px; border-radius:14px;
             margin-bottom:18px; background:var(--ltr); border:1px solid var(--bd); animation:fi .3s; }
@keyframes fi { from{opacity:0;transform:translateY(-6px)} to{opacity:1;transform:translateY(0)} }
</style>

<div class="rc-show">

{{-- Flash --}}
@if(session('success'))
<div class="flash-ok">
    <svg width="16" height="16" fill="none" stroke="var(--tx)" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
    </svg>
    <span style="font-size:13px;font-weight:600;color:var(--tx);">{{ session('success') }}</span>
</div>
@endif

{{-- Hero --}}
<div class="rc-hero">
    <div style="display:flex;align-items:center;gap:14px;">
        <div style="width:48px;height:48px;border-radius:14px;background:rgba(255,255,255,0.15);
                    display:flex;align-items:center;justify-content:center;font-size:22px;flex-shrink:0;">
            {{ $tc['icon'] }}
        </div>
        <div>
            <div style="font-size:11px;color:rgba(255,255,255,0.65);font-weight:600;margin-bottom:3px;">
                Réclamation #{{ $reclamation->id }}
            </div>
            <h1 style="font-size:18px;font-weight:800;color:white;margin:0;">
                {{ $tc['icon'] }} {{ $tc['label'] }}
            </h1>
            <div style="font-size:11px;color:rgba(255,255,255,0.7);margin-top:3px;">
                par {{ $reclamation->stagiaire?->name ?? '—' }} ·
                {{ $reclamation->created_at->format('d/m/Y à H:i') }}
            </div>
        </div>
    </div>
    <div style="display:flex;align-items:center;gap:10px;flex-wrap:wrap;">
        <span style="background:{{ $sc['bg'] }};color:{{ $sc['color'] }};
                     border:1px solid {{ $sc['border'] }};font-size:11px;font-weight:800;
                     padding:5px 13px;border-radius:99px;">
            {{ $sc['icon'] }} {{ $sc['label'] }}
        </span>
        <a href="{{ route('reclamations.index') }}" class="rc-hero-back">← Retour</a>
    </div>
</div>

{{-- Body --}}
<div class="rc-body">

    {{-- ══════ LEFT : CONVERSATION ══════ --}}
    <div class="rc-thread">
        <div class="rc-thread-head">
            <div style="font-size:13px;font-weight:800;color:#1e293b;display:flex;align-items:center;gap:8px;">
                💬 Conversation
                <span style="font-size:10px;font-weight:600;color:#94a3b8;">
                    {{ $reclamation->messages->count() }} message(s)
                </span>
            </div>
            <span style="font-size:10px;color:#94a3b8;">
                Mis à jour {{ $reclamation->updated_at->diffForHumans() }}
            </span>
        </div>

        {{-- Original ticket description --}}
        <div class="ticket-desc">
            <div class="ticket-desc-header">
                @php
                    $stag = $reclamation->stagiaire;
                    $stagInitials = strtoupper(
                        mb_substr($stag?->name ?? '?', 0, 1) .
                        mb_substr(explode(' ', $stag?->name ?? '?')[1] ?? '', 0, 1)
                    );
                @endphp
                <div style="width:34px;height:34px;border-radius:10px;background:#eff6ff;
                            display:flex;align-items:center;justify-content:center;
                            font-size:11px;font-weight:800;color:#1e40af;flex-shrink:0;">
                    {{ $stagInitials }}
                </div>
                <div>
                    <div style="font-size:12px;font-weight:700;color:#1e293b;">
                        {{ $stag?->name ?? '—' }}
                        <span style="font-size:9px;font-weight:700;padding:1px 7px;border-radius:5px;
                                     background:#f5f3ff;color:#6d28d9;margin-left:4px;">Stagiaire</span>
                    </div>
                    <div style="font-size:10px;color:#94a3b8;">
                        Ticket ouvert le {{ $reclamation->created_at->format('d/m/Y à H:i') }}
                    </div>
                </div>
                <span style="margin-left:auto;font-size:10px;font-weight:700;padding:3px 10px;
                             border-radius:8px;background:#dbeafe;color:#1e40af;">
                    {{ $tc['icon'] }} {{ $tc['label'] }}
                </span>
            </div>
            <div class="ticket-desc-body">{{ $reclamation->description }}</div>
        </div>

        {{-- Messages --}}
        @if($reclamation->messages->isNotEmpty())
        <div class="messages-area" id="messages-area">
            @php $prevDate = null; @endphp
            @foreach($reclamation->messages as $msg)
                @php
                    $isMe       = $msg->sender_id === Auth::id();
                    $sender     = $msg->sender;
                    $role       = $sender?->role ?? 'stagiaire';
                    $rc         = $roleLabels[$role] ?? $roleLabels['stagiaire'];
                    $initials   = strtoupper(
                        mb_substr($sender?->name ?? '?', 0, 1) .
                        mb_substr(explode(' ', $sender?->name ?? '?')[1] ?? '', 0, 1)
                    );
                    $msgDate    = $msg->created_at->toDateString();
                @endphp

                {{-- Day divider --}}
                @if($msgDate !== $prevDate)
                    @php $prevDate = $msgDate; @endphp
                    <div class="day-divider">
                        <span>
                            {{ $msg->created_at->isToday() ? "Aujourd'hui" : ($msg->created_at->isYesterday() ? 'Hier' : $msg->created_at->format('d M Y')) }}
                        </span>
                    </div>
                @endif

                @if($isMe)
                {{-- MY bubble (right) --}}
                <div class="bubble-right">
                    <div class="bubble-right-inner">
                        <div class="bubble-meta bubble-meta-right">
                            <span>{{ $msg->created_at->format('H:i') }}</span>
                            <span style="font-weight:700;color:#475569;">Moi</span>
                        </div>
                        <div class="bubble-body-right">{{ $msg->message }}</div>
                    </div>
                </div>
                @else
                {{-- THEIR bubble (left) --}}
                <div class="bubble-left">
                    <div class="avatar-sm">{{ $initials }}</div>
                    <div style="flex:1;">
                        <div class="bubble-meta">
                            <span style="font-weight:700;color:#1e293b;">{{ $sender?->name ?? '—' }}</span>
                            <span class="role-chip" style="background:{{ $rc['bg'] }};color:{{ $rc['color'] }};">
                                {{ $rc['label'] }}
                            </span>
                            <span>· {{ $msg->created_at->format('H:i') }}</span>
                        </div>
                        <div class="bubble-body-left">{{ $msg->message }}</div>
                    </div>
                </div>
                @endif
            @endforeach
            <div id="messages-end"></div>
        </div>
        @else
        <div style="padding:32px 20px;text-align:center;color:#94a3b8;font-size:12px;">
            Aucun message pour l'instant. Soyez le premier à répondre.
        </div>
        @endif

        {{-- Reply form --}}
        @if($canReply)
        <div class="reply-area">
            <form method="POST" action="{{ route('reclamations.message', $reclamation) }}">
                @csrf
                @error('message')
                    <div style="font-size:11px;color:#ef4444;margin-bottom:8px;">{{ $message }}</div>
                @enderror
                <textarea
                    name="message"
                    class="reply-textarea"
                    placeholder="Écrivez votre message ici…"
                    maxlength="2000"
                    required
                    autofocus
                >{{ old('message') }}</textarea>
                <div class="reply-submit">
                    <button type="submit" class="btn-send">
                        <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                  d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                        </svg>
                        Envoyer
                    </button>
                </div>
            </form>
        </div>
        @else
        <div class="reply-closed">
            <svg width="16" height="16" fill="none" stroke="#92400e" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            @if($reclamation->status === 'traite')
                Cette réclamation est marquée comme traitée — la conversation est fermée.
            @else
                Vous ne pouvez pas répondre à cette réclamation.
            @endif
        </div>
        @endif
    </div>

    {{-- ══════ RIGHT : SIDEBAR ══════ --}}
    <div class="rc-sidebar">

        {{-- ── Ticket info ── --}}
        <div class="rc-card">
            <div class="rc-card-head">📋 Informations</div>

            <div class="rc-info-row">
                <span class="rc-info-label">Ticket</span>
                <span class="rc-info-val">#{{ $reclamation->id }}</span>
            </div>
            <div class="rc-info-row">
                <span class="rc-info-label">Type</span>
                <span style="font-size:11px;font-weight:700;padding:2px 9px;border-radius:6px;background:#eff6ff;color:#1e40af;">
                    {{ $tc['icon'] }} {{ $tc['label'] }}
                </span>
            </div>
            <div class="rc-info-row">
                <span class="rc-info-label">Statut</span>
                <span style="font-size:11px;font-weight:700;padding:2px 9px;border-radius:6px;
                             background:{{ $sc['bg'] }};color:{{ $sc['color'] }};border:1px solid {{ $sc['border'] }};">
                    {{ $sc['icon'] }} {{ $sc['label'] }}
                </span>
            </div>
            <div class="rc-info-row">
                <span class="rc-info-label">Stagiaire</span>
                <span class="rc-info-val">{{ $reclamation->stagiaire?->name ?? '—' }}</span>
            </div>
            @if($reclamation->assignee)
            <div class="rc-info-row">
                <span class="rc-info-label">Assigné à</span>
                <span class="rc-info-val" style="display:flex;align-items:center;gap:5px;justify-content:flex-end;">
                    {{ $reclamation->assignee->name }}
                    @php $ar = $roleLabels[$reclamation->assignee->role] ?? $roleLabels['formateur']; @endphp
                    <span class="role-chip" style="background:{{ $ar['bg'] }};color:{{ $ar['color'] }};">
                        {{ $ar['label'] }}
                    </span>
                </span>
            </div>
            @endif
            <div class="rc-info-row">
                <span class="rc-info-label">Ouvert le</span>
                <span class="rc-info-val">{{ $reclamation->created_at->format('d/m/Y') }}</span>
            </div>
            <div class="rc-info-row">
                <span class="rc-info-label">Dernière activité</span>
                <span class="rc-info-val">{{ $reclamation->updated_at->diffForHumans() }}</span>
            </div>
        </div>

        {{-- ── Status controls (admin only) ── --}}
        @if($isAdmin)
        <div class="rc-card">
            <div class="rc-card-head">🔄 Changer le statut</div>
            <div style="padding:14px;display:flex;flex-direction:column;gap:8px;">
                @foreach(\App\Models\Reclamation::STATUSES as $statusKey => $statusCfg)
                    <form method="POST" action="{{ route('reclamations.status', $reclamation) }}">
                        @csrf @method('PATCH')
                        <input type="hidden" name="status" value="{{ $statusKey }}">
                        <button type="submit" class="btn-status"
                            style="color:{{ $statusCfg['color'] }};border-color:{{ $statusCfg['border'] }};
                                   background:{{ $reclamation->status === $statusKey ? $statusCfg['bg'] : 'white' }};
                                   font-weight:{{ $reclamation->status === $statusKey ? '800' : '600' }};">
                            {{ $statusCfg['icon'] }} {{ $statusCfg['label'] }}
                            @if($reclamation->status === $statusKey) ← actuel @endif
                        </button>
                    </form>
                @endforeach
            </div>
        </div>

        {{-- ── Assign ── --}}
        <div class="rc-card">
            <div class="rc-card-head">👤 Assigner à</div>
            <div style="padding:14px;">
                <form method="POST" action="{{ route('reclamations.assign', $reclamation) }}">
                    @csrf @method('PATCH')
                    <select name="assigned_to" class="assign-select">
                        <option value="">— Non assigné —</option>
                        @foreach($assignableUsers as $assignable)
                            @php $ar = $roleLabels[$assignable->role] ?? $roleLabels['formateur']; @endphp
                            <option value="{{ $assignable->id }}"
                                {{ $reclamation->assigned_to == $assignable->id ? 'selected' : '' }}>
                                {{ $assignable->name }} ({{ $ar['label'] }})
                            </option>
                        @endforeach
                    </select>
                    <button type="submit" class="btn-assign">
                        Assigner
                    </button>
                </form>
            </div>
        </div>
        @endif

    </div>
    {{-- END SIDEBAR --}}
</div>

</div>

<script>
// Auto-scroll to bottom of messages on load
document.addEventListener('DOMContentLoaded', () => {
    const end = document.getElementById('messages-end');
    if (end) end.scrollIntoView({ behavior: 'smooth', block: 'end' });
});
</script>
@endsection