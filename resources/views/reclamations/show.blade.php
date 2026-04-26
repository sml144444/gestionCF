{{-- resources/views/reclamations/show.blade.php --}}
@extends('layouts.app')
@section('title', 'Réclamation #' . $reclamation->id)
@section('page-title', 'Conversation')

@push('scripts')
<script src="https://js.pusher.com/8.4/pusher.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/laravel-echo@1.16.1/dist/echo.iife.js"></script>
<script>
window.Pusher = Pusher;
window.Echo = new Echo({
    broadcaster: 'reverb',
    key: '{{ env("REVERB_APP_KEY") }}',
    wsHost: '{{ env("REVERB_HOST", "localhost") }}',
    wsPort: {{ env("REVERB_PORT", 8080) }},
    wssPort: {{ env("REVERB_PORT", 8080) }},
    forceTLS: false,
    enabledTransports: ['ws', 'wss'],
});
</script>
@endpush

@section('content')
@php
    $user        = Auth::user();
    $isStaff     = $user->can('reclamation-manage');
    $isAssigned  = $reclamation->assigned_to === $user->id;
    $isStagiaire = $user->id === $reclamation->id_user;
    $sc          = $reclamation->statusConfig;
    $tc          = $reclamation->typeConfig;
    $canReply    = $reclamation->canReply($user);

    $roleGradients = [
        'admin'        => 'linear-gradient(135deg,#065f46 0%,#0a6640 100%)',
        'gestionnaire' => 'linear-gradient(135deg,#0f172a 0%,#1e293b 100%)',
        'formateur'    => 'linear-gradient(135deg,#1a4f8a 0%,#2563eb 100%)',
        'stagiaire'    => 'linear-gradient(135deg,#1a4f8a 0%,#2563eb 100%)',
    ];
    $roleAccents = [
        'admin'        => '#0a6640',
        'gestionnaire' => '#1e293b',
        'formateur'    => '#2563eb',
        'stagiaire'    => '#2563eb',
    ];
    $roleFocusRings = [
        'admin'        => 'rgba(10,102,64,0.12)',
        'gestionnaire' => 'rgba(30,41,59,0.12)',
        'formateur'    => 'rgba(26,79,138,0.10)',
        'stagiaire'    => 'rgba(26,79,138,0.10)',
    ];
    $roleAvatarBg = [
        'admin'        => '#dcfce7',
        'gestionnaire' => '#e2e8f0',
        'formateur'    => '#eff6ff',
        'stagiaire'    => '#eff6ff',
    ];
    $roleAvatarText = [
        'admin'        => '#065f46',
        'gestionnaire' => '#334155',
        'formateur'    => '#1e40af',
        'stagiaire'    => '#1e40af',
    ];
    $roleMsgBg = [
        'admin'        => '#f0fdf4',
        'gestionnaire' => '#f8fafc',
        'formateur'    => '#f0f7ff',
        'stagiaire'    => '#f0f7ff',
    ];
    $roleMsgBorder = [
        'admin'        => '#bbf7d0',
        'gestionnaire' => '#e2e8f0',
        'formateur'    => '#bfdbfe',
        'stagiaire'    => '#bfdbfe',
    ];
    $roleSuccessBg     = ['admin'=>'#f0fdf4','gestionnaire'=>'#f8fafc','formateur'=>'#f0f7ff','stagiaire'=>'#f0f7ff'];
    $roleSuccessBorder = ['admin'=>'#bbf7d0','gestionnaire'=>'#e2e8f0','formateur'=>'#bfdbfe','stagiaire'=>'#bfdbfe'];
    $roleSuccessText   = ['admin'=>'#065f46','gestionnaire'=>'#1e293b','formateur'=>'#1e40af','stagiaire'=>'#1e40af'];

    $role      = $user->role ?? 'stagiaire';
    $gradient  = $roleGradients[$role]     ?? $roleGradients['stagiaire'];
    $accent    = $roleAccents[$role]       ?? $roleAccents['stagiaire'];
    $ring      = $roleFocusRings[$role]    ?? $roleFocusRings['stagiaire'];
    $avatarBg  = $roleAvatarBg[$role]      ?? $roleAvatarBg['stagiaire'];
    $avatarTx  = $roleAvatarText[$role]    ?? $roleAvatarText['stagiaire'];
    $msgBg     = $roleMsgBg[$role]         ?? $roleMsgBg['stagiaire'];
    $msgBorder = $roleMsgBorder[$role]     ?? $roleMsgBorder['stagiaire'];
    $succBg    = $roleSuccessBg[$role]     ?? $roleSuccessBg['stagiaire'];
    $succBd    = $roleSuccessBorder[$role] ?? $roleSuccessBorder['stagiaire'];
    $succTx    = $roleSuccessText[$role]   ?? $roleSuccessText['stagiaire'];
@endphp

<style>
* { box-sizing: border-box; margin: 0; padding: 0; }

/* ── Full-viewport shell ── */
.conv-shell {
    font-family: 'Segoe UI', system-ui, sans-serif;
    display: flex;
    flex-direction: column;
    height: calc(100vh - 64px); /* adjust 64px to your navbar height */
    max-width: 860px;
    margin: 0 auto;
    gap: 12px;
    padding-bottom: 12px;
}

/* ── Flash notice ── */
.flash-notice {
    flex-shrink: 0;
    padding: 11px 16px;
    border-radius: 12px;
    font-size: 12px;
    font-weight: 600;
}

/* ── Hero ── */
.hero {
    flex-shrink: 0;
    background: {{ $gradient }};
    border-radius: 18px;
    padding: 16px 22px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 12px;
    flex-wrap: wrap;
    position: relative;
    overflow: hidden;
}
.hero::after {
    content: '';
    position: absolute;
    right: -40px; top: -40px;
    width: 160px; height: 160px;
    border-radius: 50%;
    background: rgba(255,255,255,0.06);
    pointer-events: none;
}

/* ── Chat card — FLEX COLUMN that fills remaining space ── */
.chat-card {
    flex: 1;               /* take all remaining vertical space */
    min-height: 0;         /* crucial — lets flex child shrink below content size */
    background: white;
    border-radius: 20px;
    border: 1px solid #e2e8f0;
    display: flex;
    flex-direction: column;
    overflow: hidden;
}

/* ── Chat header — fixed, never scrolls ── */
.chat-header {
    flex-shrink: 0;
    padding: 14px 20px;
    border-bottom: 1px solid #f1f5f9;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 10px;
    flex-wrap: wrap;
    background: white;
}

/* ── Messages area — ONLY this scrolls ── */
.messages-area {
    flex: 1;
    min-height: 0;         /* crucial */
    overflow-y: auto;
    padding: 20px;
    display: flex;
    flex-direction: column;
    gap: 14px;
    scroll-behavior: smooth;
}
/* Custom scrollbar */
.messages-area::-webkit-scrollbar { width: 5px; }
.messages-area::-webkit-scrollbar-track { background: transparent; }
.messages-area::-webkit-scrollbar-thumb { background: #e2e8f0; border-radius: 10px; }
.messages-area::-webkit-scrollbar-thumb:hover { background: #cbd5e1; }

/* ── Bubbles ── */
.msg-bubble { max-width: 72%; display: flex; flex-direction: column; gap: 4px; }
.msg-bubble.mine   { align-self: flex-end;   align-items: flex-end; }
.msg-bubble.theirs { align-self: flex-start; align-items: flex-start; }

.msg-content {
    padding: 11px 15px;
    border-radius: 18px;
    font-size: 13px;
    line-height: 1.6;
    word-break: break-word;
}
.msg-bubble.mine .msg-content {
    background: {{ $gradient }};
    color: white;
    border-bottom-right-radius: 4px;
}
.msg-bubble.theirs .msg-content {
    background: #f1f5f9;
    color: #1e293b;
    border-bottom-left-radius: 4px;
}
.msg-meta   { font-size: 10px; color: #94a3b8; padding: 0 4px; }
.msg-sender { font-size: 10px; font-weight: 700; color: #64748b; padding: 0 4px; }

/* Date divider */
.date-divider {
    align-self: center;
    font-size: 10px;
    font-weight: 700;
    color: #94a3b8;
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 20px;
    padding: 4px 14px;
    margin: 4px 0;
}

/* ── Typing indicator ── */
.typing-indicator { display: none; align-self: flex-start; }
.typing-indicator.visible { display: flex; }
.typing-dots {
    background: #f1f5f9;
    border-radius: 18px;
    border-bottom-left-radius: 4px;
    padding: 12px 16px;
    display: flex;
    gap: 4px;
    align-items: center;
}
.typing-dots span {
    width: 7px; height: 7px;
    border-radius: 50%;
    background: #94a3b8;
    animation: bounce 1.2s infinite;
}
.typing-dots span:nth-child(2) { animation-delay: .2s; }
.typing-dots span:nth-child(3) { animation-delay: .4s; }
@keyframes bounce { 0%,60%,100%{transform:translateY(0)} 30%{transform:translateY(-7px)} }

/* ── Reply area — fixed at card bottom ── */
.reply-area {
    flex-shrink: 0;
    padding: 14px 18px;
    border-top: 1px solid #f1f5f9;
    background: #fafbfd;
}
.reply-box { display: flex; gap: 10px; align-items: flex-end; }

.reply-input {
    flex: 1;
    border: 1.5px solid #e2e8f0;
    border-radius: 14px;
    padding: 11px 14px;
    font-size: 13px;
    font-family: inherit;
    resize: none;
    outline: none;
    min-height: 44px;
    max-height: 120px;
    line-height: 1.5;
    transition: border-color .15s, box-shadow .15s;
    background: white;
}
.reply-input:focus {
    border-color: {{ $accent }};
    box-shadow: 0 0 0 3px {{ $ring }};
}

.btn-send {
    background: {{ $gradient }};
    color: white;
    border: none;
    border-radius: 12px;
    width: 44px;
    height: 44px;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
    transition: opacity .15s, transform .1s;
}
.btn-send:hover    { opacity: .88; transform: scale(1.04); }
.btn-send:active   { transform: scale(.97); }
.btn-send:disabled { opacity: .4; cursor: not-allowed; transform: none; }

.reply-hint { font-size: 10px; color: #cbd5e1; margin-top: 7px; text-align: right; }

/* ── Traite notice ── */
.traite-notice {
    flex-shrink: 0;
    padding: 16px 20px;
    background: #f0fdf4;
    border-top: 1px solid #bbf7d0;
    text-align: center;
    font-size: 12px;
    font-weight: 700;
    color: #166534;
}

/* ── Badges ── */
.badge { font-size: 10px; font-weight: 700; padding: 4px 10px; border-radius: 8px; white-space: nowrap; }
.rt-badge {
    display: inline-flex; align-items: center; gap: 5px;
    font-size: 10px; font-weight: 700;
    color: #16a34a; background: #dcfce7;
    border: 1px solid #bbf7d0;
    border-radius: 8px; padding: 3px 9px;
}
.rt-dot {
    width: 6px; height: 6px;
    border-radius: 50%;
    background: #16a34a;
    animation: rtpulse 2s infinite;
}
@keyframes rtpulse { 0%,100%{opacity:1} 50%{opacity:.4} }

/* ── Admin panel ── */
.admin-panel {
    flex-shrink: 0;
    background: white;
    border-radius: 16px;
    border: 1px solid #e2e8f0;
    padding: 16px 20px;
}
.admin-panel h3 {
    font-size: 11px; font-weight: 800; color: #1e293b;
    margin: 0 0 12px; text-transform: uppercase; letter-spacing: .5px;
}
.f-select {
    border: 1.5px solid #e2e8f0;
    border-radius: 10px;
    padding: 8px 12px;
    font-size: 12px;
    color: #1e293b;
    background: white;
    cursor: pointer;
    outline: none;
    width: 100%;
    transition: border-color .15s;
}
.f-select:focus { border-color: {{ $accent }}; box-shadow: 0 0 0 3px {{ $ring }}; }
.btn-action {
    font-size: 12px; font-weight: 700;
    padding: 8px 18px; border-radius: 10px;
    background: {{ $gradient }};
    color: white; border: none; cursor: pointer;
    transition: opacity .15s; width: 100%;
}
.btn-action:hover { opacity: .88; }

/* ── Animations ── */
@keyframes slideUp { from{transform:translateY(12px);opacity:0} to{transform:translateY(0);opacity:1} }
.msg-bubble { animation: slideUp .2s ease; }
@keyframes flashIn { from{transform:scale(.9);opacity:0} to{transform:scale(1);opacity:1} }
.status-updated { animation: flashIn .3s ease; }
</style>

<div class="conv-shell">

    {{-- Flash --}}
    @if(session('success'))
    <div class="flash-notice" style="background:{{ $succBg }};border:1px solid {{ $succBd }};color:{{ $succTx }};">
        ✓ {{ session('success') }}
    </div>
    @endif

    {{-- Hero --}}
    <div class="hero">
        <div style="display:flex;align-items:center;gap:14px;">
          <a href="{{ route('reclamations.index') }}"
               style="width:36px;height:36px;border-radius:12px;background:rgba(255,255,255,0.15);
                      display:flex;align-items:center;justify-content:center;
                      text-decoration:none;color:white;font-size:16px;flex-shrink:0;">←</a>
            <div>
                <div style="font-size:11px;color:rgba(255,255,255,.65);font-weight:600;">
                    {{ $tc['icon'] }} {{ $tc['label'] }} · #{{ $reclamation->id }}
                </div>
                <div style="font-size:15px;font-weight:800;color:white;margin-top:1px;">Conversation</div>
            </div>
        </div>
        <div style="display:flex;align-items:center;gap:10px;flex-wrap:wrap;">
            <span id="status-badge" class="badge"
                  style="background:{{ $sc['bg'] }};color:{{ $sc['color'] }};border:1px solid {{ $sc['border'] }};">
                {{ $sc['icon'] }} {{ $sc['label'] }}
            </span>
            <span class="rt-badge"><span class="rt-dot"></span> Temps réel</span>
        </div>
    </div>

    {{-- Chat card --}}
    <div class="chat-card">

        {{-- Header --}}
        <div class="chat-header">
            <div>
                @if($isStagiaire)
                    <div style="font-size:12px;font-weight:700;color:#1e293b;">Votre réclamation</div>
                    <div style="font-size:11px;color:#94a3b8;margin-top:1px;">
                        Ouverte le {{ $reclamation->created_at->format('d/m/Y à H:i') }}
                    </div>
                @else
                    <div style="display:flex;align-items:center;gap:8px;">
                        <div style="width:34px;height:34px;border-radius:10px;
                                    background:{{ $avatarBg }};flex-shrink:0;
                                    display:flex;align-items:center;justify-content:center;
                                    font-size:11px;font-weight:800;color:{{ $avatarTx }};">
                            {{ strtoupper(mb_substr($reclamation->stagiaire?->name ?? '?', 0, 2)) }}
                        </div>
                        <div>
                            <div style="font-size:12px;font-weight:700;color:#1e293b;">
                                {{ $reclamation->stagiaire?->name }}
                            </div>
                            <div style="font-size:10px;color:#94a3b8;">{{ $reclamation->stagiaire?->email }}</div>
                        </div>
                    </div>
                @endif
            </div>
            <div style="font-size:11px;color:#94a3b8;background:#f8fafc;border:1px solid #e2e8f0;
                        border-radius:8px;padding:4px 10px;">
                {{ $reclamation->messages->count() }} msg
            </div>
        </div>

        {{-- Messages — ONLY this div scrolls --}}
        <div class="messages-area" id="messages-area">

            {{-- Date divider --}}
            <div class="date-divider">{{ $reclamation->created_at->format('d/m/Y') }}</div>

            {{-- Initial description --}}
            <div class="msg-bubble {{ $isStagiaire ? 'mine' : 'theirs' }}">
                @unless($isStagiaire)
                    <div class="msg-sender">{{ $reclamation->stagiaire?->name }}</div>
                @endunless
                <div class="msg-content"
                     style="{{ !$isStagiaire ? "background:{$msgBg};border:1px solid {$msgBorder};color:#1e293b;" : '' }}">
                    <div style="font-size:10px;font-weight:700;margin-bottom:6px;opacity:.6;letter-spacing:.3px;">
                        📝 RÉCLAMATION INITIALE
                    </div>
                    {{ $reclamation->description }}
                </div>
                <div class="msg-meta">{{ $reclamation->created_at->format('H:i') }}</div>
            </div>

            {{-- Messages --}}
            @foreach($reclamation->messages as $msg)
                @php $isMe = $msg->sender_id === $user->id; @endphp
                <div class="msg-bubble {{ $isMe ? 'mine' : 'theirs' }}" id="msg-{{ $msg->id }}">
                    @unless($isMe)
                        <div class="msg-sender">
                            {{ $msg->sender?->name }}
                            <span style="font-weight:400;color:#cbd5e1;">· {{ ucfirst($msg->sender?->role) }}</span>
                        </div>
                    @endunless
                    <div class="msg-content">{{ $msg->message }}</div>
                    <div class="msg-meta">{{ $msg->created_at->format('H:i') }}</div>
                </div>
            @endforeach

            {{-- Typing --}}
            <div class="typing-indicator" id="typing-indicator">
                <div class="typing-dots"><span></span><span></span><span></span></div>
            </div>
        </div>

        {{-- Reply / Closed notice --}}
        @if($reclamation->status === 'traite')
            <div class="traite-notice">✅ Réclamation traitée — les réponses sont désactivées.</div>
        @elseif($canReply)
            <div class="reply-area">
                <div class="reply-box">
                    <textarea id="reply-input" class="reply-input"
                              placeholder="Votre message…" rows="1"></textarea>
                    <button class="btn-send" id="send-btn" title="Envoyer (Ctrl+Entrée)">
                        <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                        </svg>
                    </button>
                </div>
                <div class="reply-hint">Ctrl + Entrée pour envoyer</div>
            </div>
        @endif
    </div>

    {{-- Admin panel --}}
    @if($isStaff)
    <div class="admin-panel">
        <h3>⚙️ Gestion</h3>
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
            <div>
                <div style="font-size:11px;font-weight:700;color:#64748b;margin-bottom:6px;">Assigner à</div>
                <form action="{{ route('reclamations.assign', $reclamation) }}" method="POST">
                    @csrf @method('PATCH')
                    <select name="assigned_to" class="f-select" onchange="this.form.submit()">
                        <option value="">— Non assignée —</option>
                        @foreach($assignableUsers as $u)
                            <option value="{{ $u->id }}" {{ $reclamation->assigned_to === $u->id ? 'selected' : '' }}>
                                {{ $u->name }} ({{ ucfirst($u->role) }})
                            </option>
                        @endforeach
                    </select>
                </form>
            </div>
            <div>
                <div style="font-size:11px;font-weight:700;color:#64748b;margin-bottom:6px;">Statut</div>
                <form action="{{ route('reclamations.status', $reclamation) }}" method="POST">
                    @csrf @method('PATCH')
                    <select name="status" class="f-select" style="margin-bottom:8px;">
                        @foreach(\App\Models\Reclamation::STATUSES as $k => $cfg)
                            <option value="{{ $k }}" {{ $reclamation->status === $k ? 'selected' : '' }}>
                                {{ $cfg['icon'] }} {{ $cfg['label'] }}
                            </option>
                        @endforeach
                    </select>
                    <button type="submit" class="btn-action">Mettre à jour</button>
                </form>
            </div>
        </div>
        @can('reclamation-manage')
        <form action="{{ route('reclamations.destroy', $reclamation) }}" method="POST"
              onsubmit="return confirm('Supprimer ?');" style="margin-top:12px;text-align:right;">
            @csrf @method('DELETE')
            <button type="submit"
                    style="font-size:11px;font-weight:700;padding:7px 16px;border-radius:10px;
                           background:white;border:1.5px solid #fecdd3;color:#be123c;cursor:pointer;">
                🗑 Supprimer
            </button>
        </form>
        @endcan
    </div>
    @endif

</div>{{-- .conv-shell --}}

<script>
document.addEventListener('DOMContentLoaded', function () {

    const CURRENT_USER_ID = {{ $user->id }};
    const RECLAMATION_ID  = {{ $reclamation->id }};
    const SEND_URL        = "{{ route('reclamations.message', $reclamation) }}";
    const CSRF_TOKEN      = "{{ csrf_token() }}";

    const area     = document.getElementById('messages-area');
    const input    = document.getElementById('reply-input');
    const sendBtn  = document.getElementById('send-btn');
    const typingEl = document.getElementById('typing-indicator');

    function scrollBottom() { if (area) area.scrollTop = area.scrollHeight; }
    scrollBottom();

    function escHtml(str) {
        return String(str)
            .replace(/&/g,'&amp;').replace(/</g,'&lt;')
            .replace(/>/g,'&gt;').replace(/"/g,'&quot;');
    }
    function capitalize(str) { return str ? str.charAt(0).toUpperCase() + str.slice(1) : ''; }

    function renderBubble(data, mine) {
        const div = document.createElement('div');
        div.className = 'msg-bubble ' + (mine ? 'mine' : 'theirs');
        div.id = 'msg-' + data.id;
        let html = '';
        if (!mine) {
            const role = data.sender?.role
                ? ` <span style="font-weight:400;color:#cbd5e1;">· ${capitalize(data.sender.role)}</span>`
                : '';
            html += `<div class="msg-sender">${escHtml(data.sender?.name ?? '')}${role}</div>`;
        }
        html += `<div class="msg-content">${escHtml(data.message)}</div>`;
        html += `<div class="msg-meta">${data.created_at}</div>`;
        div.innerHTML = html;
        area.insertBefore(div, typingEl);
        scrollBottom();
    }

    async function sendMessage() {
        if (!input) return;
        const text = input.value.trim();
        if (!text || sendBtn.disabled) return;
        sendBtn.disabled = true;

        const now = new Date().toLocaleTimeString('fr-FR', { hour: '2-digit', minute: '2-digit' });
        renderBubble({ id: 'tmp-' + Date.now(), message: text, created_at: now, sender: { id: CURRENT_USER_ID } }, true);
        input.value = '';
        input.style.height = 'auto';

        try {
            const res = await fetch(SEND_URL, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': CSRF_TOKEN,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ message: text }),
            });
            if (!res.ok) console.error('Erreur:', await res.json());
        } catch (e) {
            console.error('Erreur envoi:', e);
        } finally {
            sendBtn.disabled = false;
            if (input) input.focus();
        }
    }

    if (input) {
        input.addEventListener('input', () => {
            input.style.height = 'auto';
            input.style.height = Math.min(input.scrollHeight, 120) + 'px';
        });
        input.addEventListener('keydown', e => {
            if (e.key === 'Enter' && e.ctrlKey) { e.preventDefault(); sendMessage(); }
        });
    }
    if (sendBtn) sendBtn.addEventListener('click', sendMessage);

    if (typeof window.Echo !== 'undefined') {
        window.Echo.private('reclamation.' + RECLAMATION_ID)
            .listen('.ReclamationMessageSent', (e) => {
                if (typingEl) typingEl.classList.remove('visible');
                if (e.sender?.id !== CURRENT_USER_ID) renderBubble(e, false);
            })
            .listenForWhisper('typing', () => {
                if (typingEl) {
                    typingEl.classList.add('visible');
                    scrollBottom();
                    setTimeout(() => typingEl.classList.remove('visible'), 3000);
                }
            })
            .listen('.ReclamationStatusUpdated', (e) => {
                const badge = document.getElementById('status-badge');
                if (badge) {
                    badge.style.background = e.bg;
                    badge.style.color      = e.color;
                    badge.style.border     = '1px solid ' + e.border;
                    badge.textContent      = e.icon + ' ' + e.label;
                    badge.classList.add('status-updated');
                    setTimeout(() => badge.classList.remove('status-updated'), 400);
                }
                if (e.status === 'traite') {
                    const replyArea = document.querySelector('.reply-area');
                    if (replyArea) {
                        replyArea.innerHTML = '<div class="traite-notice">✅ Réclamation traitée — conversation fermée.</div>';
                    }
                }
            });

        if (input) {
            input.addEventListener('input', () => {
                window.Echo.private('reclamation.' + RECLAMATION_ID)
                    .whisper('typing', { user: CURRENT_USER_ID });
            });
        }
    } else {
        console.error('❌ Echo not loaded!');
    }
});
</script>
@endsection