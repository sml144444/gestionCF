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
    $role        = $user->role ?? 'stagiaire';

    // ── Role-based design tokens ─────────────────────────────
    $roleConfig = [
        'admin' => [
            'primary'     => '#0a6640',
            'primary_dark'=> '#065f46',
            'primary_mid' => '#059669',
            'gradient'    => 'linear-gradient(135deg, #065f46 0%, #0a6640 60%, #059669 100%)',
            'light'       => '#ecfdf5',
            'lighter'     => '#f0fdf4',
            'border'      => '#a7f3d0',
            'text'        => '#064e3b',
            'badge_bg'    => '#d1fae5',
            'badge_text'  => '#065f46',
            'avatar_bg'   => '#d1fae5',
            'avatar_text' => '#065f46',
            'ring'        => 'rgba(10,102,64,0.15)',
            'msg_mine_bg' => 'linear-gradient(135deg, #065f46, #0a6640)',
            'msg_their_bg'=> '#f0fdf4',
            'msg_their_border' => '#a7f3d0',
            'msg_their_text'   => '#064e3b',
        ],
        'gestionnaire' => [
            'primary'     => '#1e293b',
            'primary_dark'=> '#0f172a',
            'primary_mid' => '#334155',
            'gradient'    => 'linear-gradient(135deg, #0f172a 0%, #1e293b 60%, #334155 100%)',
            'light'       => '#f1f5f9',
            'lighter'     => '#f8fafc',
            'border'      => '#cbd5e1',
            'text'        => '#0f172a',
            'badge_bg'    => '#e2e8f0',
            'badge_text'  => '#1e293b',
            'avatar_bg'   => '#e2e8f0',
            'avatar_text' => '#334155',
            'ring'        => 'rgba(30,41,59,0.15)',
            'msg_mine_bg' => 'linear-gradient(135deg, #0f172a, #1e293b)',
            'msg_their_bg'=> '#f1f5f9',
            'msg_their_border' => '#cbd5e1',
            'msg_their_text'   => '#1e293b',
        ],
        'formateur' => [
            'primary'     => '#1a4f8a',
            'primary_dark'=> '#1e3a5f',
            'primary_mid' => '#2563eb',
            'gradient'    => 'linear-gradient(135deg, #1e3a5f 0%, #1a4f8a 60%, #2563eb 100%)',
            'light'       => '#eff6ff',
            'lighter'     => '#f0f7ff',
            'border'      => '#bfdbfe',
            'text'        => '#1e3a5f',
            'badge_bg'    => '#dbeafe',
            'badge_text'  => '#1e40af',
            'avatar_bg'   => '#dbeafe',
            'avatar_text' => '#1e40af',
            'ring'        => 'rgba(26,79,138,0.12)',
            'msg_mine_bg' => 'linear-gradient(135deg, #1e3a5f, #1a4f8a)',
            'msg_their_bg'=> '#eff6ff',
            'msg_their_border' => '#bfdbfe',
            'msg_their_text'   => '#1e3a5f',
        ],
        'stagiaire' => [
            'primary'     => '#1a4f8a',
            'primary_dark'=> '#1e3a5f',
            'primary_mid' => '#2563eb',
            'gradient'    => 'linear-gradient(135deg, #1e3a5f 0%, #1a4f8a 60%, #2563eb 100%)',
            'light'       => '#eff6ff',
            'lighter'     => '#f0f7ff',
            'border'      => '#bfdbfe',
            'text'        => '#1e3a5f',
            'badge_bg'    => '#dbeafe',
            'badge_text'  => '#1e40af',
            'avatar_bg'   => '#dbeafe',
            'avatar_text' => '#1e40af',
            'ring'        => 'rgba(26,79,138,0.12)',
            'msg_mine_bg' => 'linear-gradient(135deg, #1e3a5f, #1a4f8a)',
            'msg_their_bg'=> '#eff6ff',
            'msg_their_border' => '#bfdbfe',
            'msg_their_text'   => '#1e3a5f',
        ],
    ];

    $c = $roleConfig[$role] ?? $roleConfig['stagiaire'];
@endphp

<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@300;400;500;600;700;800&family=DM+Mono:wght@400;500&display=swap" rel="stylesheet">

<style>
:root {
    --primary:      {{ $c['primary'] }};
    --primary-dark: {{ $c['primary_dark'] }};
    --primary-mid:  {{ $c['primary_mid'] }};
    --gradient:     {{ $c['gradient'] }};
    --light:        {{ $c['light'] }};
    --lighter:      {{ $c['lighter'] }};
    --border-col:   {{ $c['border'] }};
    --text-col:     {{ $c['text'] }};
    --badge-bg:     {{ $c['badge_bg'] }};
    --badge-text:   {{ $c['badge_text'] }};
    --avatar-bg:    {{ $c['avatar_bg'] }};
    --avatar-text:  {{ $c['avatar_text'] }};
    --ring:         {{ $c['ring'] }};
    --mine-bg:      {{ $c['msg_mine_bg'] }};
    --their-bg:     {{ $c['msg_their_bg'] }};
    --their-border: {{ $c['msg_their_border'] }};
    --their-text:   {{ $c['msg_their_text'] }};
}

*, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

body, .rc-page { font-family: 'DM Sans', system-ui, sans-serif; }

/* ── PAGE WRAPPER ─────────────────────────────────────────── */
.rc-page {
    display: flex;
    flex-direction: column;
    height: calc(100vh - 64px);
    max-width: 1400px;
    margin: 0 auto;
    padding: 12px 16px 0;
    gap: 12px;
}

/* ── FLASH ────────────────────────────────────────────────── */
.rc-flash {
    flex-shrink: 0;
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 12px 16px;
    background: var(--lighter);
    border: 1px solid var(--border-col);
    border-left: 4px solid var(--primary);
    border-radius: 12px;
    font-size: 13px;
    font-weight: 600;
    color: var(--text-col);
    animation: slideDown .3s ease;
}
@keyframes slideDown { from{transform:translateY(-8px);opacity:0} to{transform:translateY(0);opacity:1} }

/* ── HEADER BAR ───────────────────────────────────────────── */
.rc-header {
    flex-shrink: 0;
    background: var(--gradient);
    border-radius: 18px;
    padding: 14px 20px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 12px;
    flex-wrap: wrap;
    position: relative;
    overflow: hidden;
    box-shadow: 0 4px 24px rgba(0,0,0,0.18);
}
.rc-header::before {
    content: '';
    position: absolute;
    top: -60px; right: -60px;
    width: 200px; height: 200px;
    border-radius: 50%;
    background: rgba(255,255,255,0.07);
    pointer-events: none;
}
.rc-header::after {
    content: '';
    position: absolute;
    bottom: -40px; left: 30%;
    width: 120px; height: 120px;
    border-radius: 50%;
    background: rgba(255,255,255,0.05);
    pointer-events: none;
}
.rc-back-btn {
    width: 38px; height: 38px;
    border-radius: 12px;
    background: rgba(255,255,255,0.15);
    border: 1px solid rgba(255,255,255,0.2);
    display: flex; align-items: center; justify-content: center;
    text-decoration: none; color: white;
    font-size: 16px; flex-shrink: 0;
    transition: background .15s;
    backdrop-filter: blur(4px);
}
.rc-back-btn:hover { background: rgba(255,255,255,0.25); }
.rc-header-left { display: flex; align-items: center; gap: 12px; }
.rc-header-sub  { font-size: 11px; color: rgba(255,255,255,.65); font-weight: 500; letter-spacing: .3px; }
.rc-header-title{ font-size: 16px; font-weight: 800; color: white; margin-top: 2px; letter-spacing: -.2px; }
.rc-header-right { display: flex; align-items: center; gap: 8px; flex-wrap: wrap; }
.rc-status-badge {
    font-size: 11px; font-weight: 700;
    padding: 5px 12px; border-radius: 20px;
    display: inline-flex; align-items: center; gap: 5px;
    backdrop-filter: blur(4px);
}
.rc-live-pill {
    display: inline-flex; align-items: center; gap: 5px;
    font-size: 10px; font-weight: 700;
    color: rgba(255,255,255,.9);
    background: rgba(255,255,255,0.12);
    border: 1px solid rgba(255,255,255,.2);
    border-radius: 20px; padding: 5px 10px;
    backdrop-filter: blur(4px);
}
.live-dot {
    width: 6px; height: 6px; border-radius: 50%;
    background: #4ade80;
    box-shadow: 0 0 0 0 rgba(74,222,128,0.5);
    animation: livePulse 2s infinite;
}
@keyframes livePulse {
    0%   { box-shadow: 0 0 0 0 rgba(74,222,128,0.5); }
    70%  { box-shadow: 0 0 0 6px rgba(74,222,128,0); }
    100% { box-shadow: 0 0 0 0 rgba(74,222,128,0); }
}

/* ── MAIN LAYOUT: chat + optional sidebar ─────────────────── */
.rc-body {
    flex: 1;
    min-height: 0;
    display: flex;
    gap: 12px;
}

/* ── CHAT CARD ────────────────────────────────────────────── */
.rc-chat {
    flex: 1;
    min-width: 0;
    background: white;
    border-radius: 20px;
    border: 1px solid #e8edf3;
    display: flex;
    flex-direction: column;
    overflow: hidden;
    box-shadow: 0 2px 20px rgba(0,0,0,0.06);
}

/* ── CHAT SUB-HEADER ─────────────────────────────────────── */
.rc-chat-head {
    flex-shrink: 0;
    padding: 14px 20px;
    border-bottom: 1px solid #f0f4f8;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 10px;
    flex-wrap: wrap;
    background: white;
}
.rc-peer-avatar {
    width: 38px; height: 38px;
    border-radius: 12px;
    background: var(--avatar-bg);
    color: var(--avatar-text);
    font-size: 12px; font-weight: 800;
    display: flex; align-items: center; justify-content: center;
    flex-shrink: 0;
}
.rc-peer-name  { font-size: 13px; font-weight: 700; color: #1e293b; }
.rc-peer-email { font-size: 11px; color: #94a3b8; }
.rc-msg-count  {
    font-size: 11px; font-weight: 600; color: #94a3b8;
    background: #f8fafc; border: 1px solid #e8edf3;
    border-radius: 8px; padding: 4px 10px;
}

/* ── MESSAGES AREA ────────────────────────────────────────── */
.rc-messages {
    flex: 1; min-height: 0;
    overflow-y: auto;
    padding: 20px;
    display: flex;
    flex-direction: column;
    gap: 6px;
    scroll-behavior: smooth;
}
.rc-messages::-webkit-scrollbar { width: 4px; }
.rc-messages::-webkit-scrollbar-track { background: transparent; }
.rc-messages::-webkit-scrollbar-thumb { background: #dde3ea; border-radius: 10px; }
.rc-messages::-webkit-scrollbar-thumb:hover { background: #c4cdd7; }

/* ── DATE DIVIDER ────────────────────────────────────────── */
.date-divider {
    align-self: center;
    font-size: 10px; font-weight: 700; color: #a0aec0;
    background: #f8fafc; border: 1px solid #e8edf3;
    border-radius: 20px; padding: 4px 14px;
    margin: 10px 0 6px; letter-spacing: .4px;
}

/* ── MSG BUBBLE ───────────────────────────────────────────── */
.msg-bubble {
    display: flex;
    flex-direction: column;
    gap: 3px;
    max-width: 68%;
    position: relative;
    animation: bubbleIn .2s ease;
}
@keyframes bubbleIn { from{transform:translateY(10px);opacity:0} to{transform:translateY(0);opacity:1} }
.msg-bubble.mine   { align-self: flex-end;   align-items: flex-end; }
.msg-bubble.theirs { align-self: flex-start; align-items: flex-start; }

.msg-content {
    padding: 10px 15px;
    border-radius: 18px;
    font-size: 13.5px;
    line-height: 1.6;
    word-break: break-word;
    font-weight: 400;
}
.msg-bubble.mine .msg-content {
    background: var(--mine-bg);
    color: white;
    border-bottom-right-radius: 5px;
    box-shadow: 0 2px 12px rgba(0,0,0,0.15);
}
.msg-bubble.theirs .msg-content {
    background: var(--their-bg);
    color: var(--their-text);
    border: 1px solid var(--their-border);
    border-bottom-left-radius: 5px;
}

.msg-sender {
    font-size: 10px; font-weight: 700; color: #64748b;
    padding: 0 4px; margin-bottom: 2px;
    display: flex; align-items: center; gap: 4px;
}
.msg-sender-role { font-weight: 400; color: #94a3b8; }

.msg-footer {
    display: flex; align-items: center; gap: 6px;
    padding: 0 4px; flex-wrap: wrap;
}
.msg-time   { font-size: 10px; color: #94a3b8; font-weight: 500; }
.msg-edited { font-size: 9px; color: #94a3b8; }
.msg-bubble.mine .msg-edited { color: rgba(255,255,255,0.5); }
.msg-tick   { font-size: 9px; color: rgba(255,255,255,0.6); }
.msg-tick.seen { color: rgba(255,255,255,0.9); }

/* ── 3-DOT MENU ──────────────────────────────────────────── */
.msg-dots-btn {
    display: none;
    position: absolute;
    top: 50%; transform: translateY(-50%);
    left: -34px;
    width: 28px; height: 28px;
    border-radius: 8px;
    border: 1px solid #e8edf3;
    background: white;
    align-items: center; justify-content: center;
    cursor: pointer; font-size: 14px; font-weight: 900;
    color: #64748b; z-index: 20;
    box-shadow: 0 2px 10px rgba(0,0,0,0.10);
    transition: all .12s;
}
.msg-dots-btn.visible { display: flex; }
.msg-dots-btn:hover { background: #f0f4f8; color: #1e293b; transform: translateY(-50%) scale(1.05); }

.msg-actions {
    position: absolute;
    top: 50%; transform: translateY(-50%);
    left: -180px;
    background: white;
    border: 1px solid #e8edf3;
    border-radius: 14px;
    box-shadow: 0 8px 32px rgba(0,0,0,0.14);
    z-index: 100;
    min-width: 155px;
    overflow: hidden;
    display: none; flex-direction: column;
}
.msg-actions.open { display: flex; }
.msg-action-btn {
    border: none; border-radius: 0;
    padding: 10px 14px; font-size: 12px; font-weight: 600;
    cursor: pointer; text-align: left; background: white;
    display: flex; align-items: center; gap: 8px;
    transition: background .1s; white-space: nowrap; width: 100%;
    font-family: 'DM Sans', system-ui, sans-serif;
}
.btn-edit-msg   { color: #0369a1; }
.btn-delete-msg { color: #be123c; border-top: 1px solid #f0f4f8; }
.msg-action-btn:hover { background: #f8fafc; }

/* ── EDIT BOX ─────────────────────────────────────────────── */
.edit-box { display: flex; flex-direction: column; gap: 6px; min-width: 200px; }
.edit-textarea {
    border: 1.5px solid var(--border-col);
    border-radius: 10px; padding: 8px 10px;
    font-size: 13px; font-family: 'DM Sans', system-ui, sans-serif;
    resize: none; outline: none; min-height: 60px;
    background: white; color: #1e293b;
}
.edit-textarea:focus { border-color: var(--primary); box-shadow: 0 0 0 3px var(--ring); }
.edit-actions { display: flex; gap: 6px; justify-content: flex-end; }
.btn-save-edit   { background: var(--gradient); color: white; border: none; border-radius: 8px; padding: 6px 14px; font-size: 11px; font-weight: 700; cursor: pointer; font-family: 'DM Sans', system-ui, sans-serif; }
.btn-cancel-edit { background: #f1f5f9; color: #64748b; border: none; border-radius: 8px; padding: 6px 14px; font-size: 11px; font-weight: 700; cursor: pointer; font-family: 'DM Sans', system-ui, sans-serif; }

/* ── ATTACHMENTS ─────────────────────────────────────────── */
.attachment-container { margin-top: 8px; }
.attachment-image {
    max-width: 240px; max-height: 190px;
    border-radius: 12px; display: block; cursor: zoom-in;
    border: 1px solid rgba(255,255,255,0.25);
}
.msg-bubble.theirs .attachment-image { border-color: var(--their-border); }
.attachment-file {
    display: inline-flex; align-items: center; gap: 8px;
    padding: 8px 14px;
    background: rgba(255,255,255,0.15);
    border: 1px solid rgba(255,255,255,0.25);
    border-radius: 10px; font-size: 12px; font-weight: 600;
    color: white; text-decoration: none;
    transition: opacity .15s;
}
.msg-bubble.theirs .attachment-file {
    background: var(--lighter); border-color: var(--border-col); color: var(--text-col);
}
.attachment-file:hover { opacity: .82; }

/* ── TYPING ──────────────────────────────────────────────── */
.typing-indicator { display: none; align-self: flex-start; margin-top: 4px; }
.typing-indicator.visible { display: flex; }
.typing-dots {
    background: var(--their-bg);
    border: 1px solid var(--their-border);
    border-radius: 18px; border-bottom-left-radius: 5px;
    padding: 12px 16px; display: flex; gap: 4px; align-items: center;
}
.typing-dots span {
    width: 6px; height: 6px; border-radius: 50%;
    background: var(--primary-mid);
    animation: tdBounce 1.2s infinite;
}
.typing-dots span:nth-child(2) { animation-delay: .2s; }
.typing-dots span:nth-child(3) { animation-delay: .4s; }
@keyframes tdBounce { 0%,60%,100%{transform:translateY(0)} 30%{transform:translateY(-7px)} }

/* ── SEEN INDICATOR ──────────────────────────────────────── */
.seen-indicator { display: flex; align-items: center; gap: 5px; margin-top: 3px; justify-content: flex-end; }
.seen-avatar {
    width: 18px; height: 18px; border-radius: 50%;
    background: var(--avatar-bg); color: var(--avatar-text);
    font-size: 7px; font-weight: 800;
    display: flex; align-items: center; justify-content: center;
    border: 1.5px solid white;
    box-shadow: 0 1px 4px rgba(0,0,0,0.14);
}
.seen-text { font-size: 9px; color: rgba(255,255,255,.55); font-weight: 600; }
.msg-bubble.theirs .seen-text { color: #94a3b8; }
@keyframes seenPop { 0%{transform:scale(0);opacity:0} 70%{transform:scale(1.2);opacity:1} 100%{transform:scale(1);opacity:1} }
.seen-avatar.pop { animation: seenPop .3s ease forwards; }

/* ── ATTACH PREVIEW ──────────────────────────────────────── */
.attach-preview {
    padding: 10px 14px; background: var(--lighter);
    border: 1px solid var(--border-col);
    border-radius: 12px; margin-bottom: 8px;
    display: none; align-items: center; gap: 10px;
}
.attach-preview.visible { display: flex; }
.attach-name { font-size: 12px; font-weight: 600; color: var(--primary); flex: 1; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
.clear-attachment { background: none; border: none; cursor: pointer; color: #94a3b8; font-size: 20px; line-height: 1; transition: color .12s; }
.clear-attachment:hover { color: #ef4444; }

/* ── REPLY AREA ──────────────────────────────────────────── */
.rc-reply {
    flex-shrink: 0;
    padding: 12px 16px 14px;
    background: #fafbfc;
    border-top: 1px solid #f0f4f8;
}
.reply-box { display: flex; gap: 8px; align-items: flex-end; }
.reply-input {
    flex: 1;
    border: 1.5px solid #e2e8f0;
    border-radius: 16px;
    padding: 11px 16px;
    font-size: 13.5px; font-family: 'DM Sans', system-ui, sans-serif;
    resize: none; outline: none;
    min-height: 46px; max-height: 120px; line-height: 1.5;
    background: white; color: #1e293b;
    transition: border-color .15s, box-shadow .15s;
}
.reply-input:focus { border-color: var(--primary); box-shadow: 0 0 0 3px var(--ring); }
.reply-input::placeholder { color: #c4cdd7; }
.icon-btn {
    width: 42px; height: 42px; border-radius: 14px;
    border: 1.5px solid #e2e8f0;
    background: white; cursor: pointer;
    display: flex; align-items: center; justify-content: center;
    color: #64748b; flex-shrink: 0;
    transition: all .15s;
}
.icon-btn:hover { border-color: var(--primary); color: var(--primary); background: var(--lighter); }
.send-btn {
    background: var(--gradient);
    color: white; border: none;
    border-radius: 14px;
    width: 46px; height: 46px;
    cursor: pointer;
    display: flex; align-items: center; justify-content: center;
    flex-shrink: 0; box-shadow: 0 4px 14px rgba(0,0,0,0.18);
    transition: opacity .15s, transform .1s, box-shadow .15s;
}
.send-btn:hover  { opacity: .88; transform: scale(1.04); box-shadow: 0 6px 18px rgba(0,0,0,0.22); }
.send-btn:active { transform: scale(.97); }
.send-btn:disabled { opacity: .4; cursor: not-allowed; transform: none; box-shadow: none; }
.reply-hint { font-size: 10px; color: #c4cdd7; margin-top: 6px; text-align: right; font-weight: 500; }

/* ── TRAITE NOTICE ───────────────────────────────────────── */
.traite-notice {
    flex-shrink: 0;
    padding: 14px 20px;
    background: #f0fdf4; border-top: 1px solid #bbf7d0;
    text-align: center; font-size: 12px; font-weight: 700; color: #166534;
    display: flex; align-items: center; justify-content: center; gap: 8px;
}

/* ── SIDEBAR ─────────────────────────────────────────────── */
.rc-sidebar {
    width: 320px;
    flex-shrink: 0;
    display: flex;
    flex-direction: column;
    gap: 12px;
}
.sidebar-card {
    background: white;
    border-radius: 18px;
    border: 1px solid #e8edf3;
    padding: 18px;
    box-shadow: 0 2px 16px rgba(0,0,0,0.05);
}
.sidebar-card-title {
    font-size: 10px; font-weight: 800; color: #94a3b8;
    text-transform: uppercase; letter-spacing: .8px;
    margin-bottom: 14px; display: flex; align-items: center; gap: 6px;
}
.f-label { font-size: 11px; font-weight: 700; color: #64748b; margin-bottom: 6px; display: block; }
.f-select {
    width: 100%;
    border: 1.5px solid #e2e8f0; border-radius: 10px;
    padding: 9px 12px; font-size: 12px; font-family: 'DM Sans', system-ui, sans-serif;
    color: #1e293b; background: white; cursor: pointer; outline: none;
    transition: border-color .15s;
    appearance: none;
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='%2394a3b8'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M19 9l-7 7-7-7'/%3E%3C/svg%3E");
    background-repeat: no-repeat; background-position: right 10px center; background-size: 14px;
    padding-right: 32px;
}
.f-select:focus { border-color: var(--primary); box-shadow: 0 0 0 3px var(--ring); }
.btn-primary {
    width: 100%; padding: 10px; border-radius: 11px;
    background: var(--gradient); color: white; border: none;
    font-size: 12px; font-weight: 700; cursor: pointer;
    font-family: 'DM Sans', system-ui, sans-serif;
    transition: opacity .15s; margin-top: 8px;
}
.btn-primary:hover { opacity: .88; }
.btn-danger {
    width: 100%; padding: 9px; border-radius: 11px;
    background: white; color: #be123c;
    border: 1.5px solid #fecdd3;
    font-size: 11px; font-weight: 700; cursor: pointer;
    font-family: 'DM Sans', system-ui, sans-serif;
    transition: all .15s; margin-top: 8px;
}
.btn-danger:hover { background: #fff1f2; border-color: #fda4af; }

/* info pill row */
.info-pill {
    display: flex; align-items: flex-start; gap: 8px;
    padding: 10px 0; border-bottom: 1px solid #f0f4f8;
}
.info-pill:last-child { border-bottom: none; padding-bottom: 0; }
.info-pill-icon { font-size: 16px; flex-shrink: 0; margin-top: 1px; }
.info-pill-label { font-size: 10px; color: #94a3b8; font-weight: 600; }
.info-pill-val   { font-size: 12px; color: #1e293b; font-weight: 700; margin-top: 1px; }

/* assigned status panel */
.asgn-msg { font-size: 11px; font-weight: 600; margin-top: 10px; padding: 8px 12px; border-radius: 10px; display: none; }

/* ── MODALS ──────────────────────────────────────────────── */
.rc-overlay {
    position: fixed; inset: 0; z-index: 9998;
    background: rgba(15,23,42,0.5);
    backdrop-filter: blur(6px);
    display: flex; align-items: center; justify-content: center;
    animation: fadeO .2s ease;
}
@keyframes fadeO { from{opacity:0} to{opacity:1} }
.rc-modal {
    background: white; border-radius: 22px;
    padding: 30px 28px 24px;
    box-shadow: 0 24px 60px rgba(0,0,0,0.20);
    max-width: 320px; width: 90%;
    animation: popM .22s ease;
}
@keyframes popM { from{transform:scale(.92);opacity:0} to{transform:scale(1);opacity:1} }
.rc-modal-icon  { font-size: 40px; margin-bottom: 12px; }
.rc-modal-title { font-size: 16px; font-weight: 800; color: #1e293b; margin-bottom: 6px; }
.rc-modal-body  { font-size: 13px; color: #64748b; line-height: 1.6; margin-bottom: 22px; }
.rc-modal-btns  { display: flex; gap: 8px; justify-content: flex-end; }
.rc-btn { font-size: 12px; font-weight: 700; padding: 10px 20px; border-radius: 11px; border: none; cursor: pointer; transition: opacity .15s; font-family: 'DM Sans', system-ui, sans-serif; }
.rc-btn:hover { opacity: .85; }
.rc-btn-cancel { background: #f1f5f9; color: #64748b; }
.rc-btn-danger { background: linear-gradient(135deg,#be123c,#e11d48); color: white; }
.rc-btn-ok     { color: white; background: var(--gradient); }

/* ── STATUS BADGE ANIMATION ──────────────────────────────── */
@keyframes flashIn { from{transform:scale(.88);opacity:0} to{transform:scale(1);opacity:1} }
.status-updated { animation: flashIn .3s ease; }

/* ── INITIAL MSG LABEL ───────────────────────────────────── */
.init-label {
    font-size: 9px; font-weight: 800; letter-spacing: .6px;
    opacity: .65; margin-bottom: 5px; text-transform: uppercase;
}

/* ── RESPONSIVE ──────────────────────────────────────────── */
@media (max-width: 768px) {
    .rc-page { padding: 8px 10px 0; gap: 8px; height: auto; min-height: calc(100vh - 64px); }
    .rc-body { flex-direction: column; }
    .rc-sidebar { width: 100%; }
    .rc-chat { min-height: 0; flex: none; height: calc(100vh - 260px); }
    .rc-header { padding: 12px 14px; border-radius: 14px; }
    .rc-header-title { font-size: 14px; }
    .rc-live-pill { display: none; }
    .msg-bubble { max-width: 85%; }
    .sidebar-card { padding: 14px; }
    .f-select { font-size: 14px; }
    .reply-input { font-size: 14px; }
    .rc-back-btn { width: 34px; height: 34px; }
    .rc-body { height: auto; flex: none; }
    .rc-page { height: auto; }
}
@media (max-width: 480px) {
    .rc-header-right { gap: 6px; }
    .rc-status-badge { font-size: 10px; padding: 4px 9px; }
    .rc-messages { padding: 14px; }
    .msg-bubble { max-width: 90%; }
}
</style>

<div class="rc-page">

    {{-- Flash --}}
    @if(session('success'))
    <div class="rc-flash">
        <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
        </svg>
        {{ session('success') }}
    </div>
    @endif

    {{-- Header --}}
    <div class="rc-header">
        <div class="rc-header-left">
            <a href="{{ route('reclamations.index') }}" class="rc-back-btn">
                <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"/>
                </svg>
            </a>
            <div>
                <div class="rc-header-sub">{{ $tc['icon'] }} {{ $tc['label'] }} &nbsp;·&nbsp; #{{ $reclamation->id }}</div>
                <div class="rc-header-title">Conversation</div>
            </div>
        </div>
        <div class="rc-header-right">
            <span id="status-badge" class="rc-status-badge"
                  style="background:{{ $sc['bg'] }};color:{{ $sc['color'] }};border:1px solid {{ $sc['border'] }};">
                {{ $sc['icon'] }} {{ $sc['label'] }}
            </span>
            <span class="rc-live-pill"><span class="live-dot"></span> Temps réel</span>
        </div>
    </div>

    {{-- Body --}}
    <div class="rc-body">

        {{-- Chat --}}
        <div class="rc-chat">

            {{-- Chat sub-header --}}
            <div class="rc-chat-head">
                @if($isStagiaire)
                    <div>
                        <div class="rc-peer-name">Votre réclamation</div>
                        <div class="rc-peer-email">Ouverte le {{ $reclamation->created_at->format('d/m/Y à H:i') }}</div>
                    </div>
                @else
                    <div style="display:flex;align-items:center;gap:10px;">
                        <div class="rc-peer-avatar">
                            {{ strtoupper(mb_substr($reclamation->stagiaire?->name ?? '?', 0, 2)) }}
                        </div>
                        <div>
                            <div class="rc-peer-name">{{ $reclamation->stagiaire?->name }}</div>
                            <div class="rc-peer-email">{{ $reclamation->stagiaire?->email }}</div>
                        </div>
                    </div>
                @endif
                <div class="rc-msg-count">{{ $reclamation->messages->count() }} msgs</div>
            </div>

            {{-- Messages --}}
            <div class="rc-messages" id="messages-area">

                <div class="date-divider">{{ $reclamation->created_at->format('d M Y') }}</div>

                {{-- Initial description bubble --}}
                <div class="msg-bubble {{ $isStagiaire ? 'mine' : 'theirs' }}">
                    @unless($isStagiaire)
                        <div class="msg-sender">{{ $reclamation->stagiaire?->name }}</div>
                    @endunless
                    <div class="msg-content">
                        <div class="init-label">📝 Réclamation initiale</div>
                        {{ $reclamation->description }}
                    </div>
                    <div class="msg-footer">
                        <div class="msg-time">{{ $reclamation->created_at->format('H:i') }}</div>
                    </div>
                </div>

                {{-- Messages --}}
                @foreach($reclamation->messages as $msg)
                @php
                    $isMe    = $msg->sender_id === $user->id;
                    $canAct  = $isMe && is_null($msg->seen_at);
                    $canEdit = $canAct && is_null($msg->attachment_path);
                @endphp
                <div class="msg-bubble {{ $isMe ? 'mine' : 'theirs' }}"
                     id="msg-{{ $msg->id }}"
                     data-seen="{{ $msg->seen_at ? '1' : '0' }}"
                     data-mine="{{ $isMe ? '1' : '0' }}">

                    @if($canAct)
                    <button class="msg-dots-btn" onclick="toggleMenu(event,{{ $msg->id }})" title="Options">⋯</button>
                    <div class="msg-actions" id="msg-menu-{{ $msg->id }}">
                        @if($canEdit)
                        <button class="msg-action-btn btn-edit-msg"
                                onclick="startEdit({{ $msg->id }});closeAllMenus()">✏️ Modifier</button>
                        @endif
                        <button class="msg-action-btn btn-delete-msg"
                                onclick="deleteMsg({{ $msg->id }});closeAllMenus()">🗑 Supprimer</button>
                    </div>
                    @endif

                    @unless($isMe)
                        <div class="msg-sender">
                            {{ $msg->sender?->name }}
                            <span class="msg-sender-role">· {{ ucfirst($msg->sender?->role) }}</span>
                        </div>
                    @endunless

                    @if($msg->message)
                    <div class="msg-content" id="msg-content-{{ $msg->id }}">
                        {{ $msg->message }}
                    </div>
                    @else
                    <div id="msg-content-{{ $msg->id }}" style="display:none;"></div>
                    @endif

                    @if($msg->attachment_path)
                        <div class="attachment-container">
                            @if($msg->is_image)
                                <a href="{{ asset('storage/'.$msg->attachment_path) }}" target="_blank">
                                    <img src="{{ asset('storage/'.$msg->attachment_path) }}"
                                         alt="{{ $msg->attachment_name }}"
                                         class="attachment-image">
                                </a>
                            @else
                                <a href="{{ asset('storage/'.$msg->attachment_path) }}"
                                   target="_blank" download="{{ $msg->attachment_name }}"
                                   class="attachment-file">
                                    📎 {{ $msg->attachment_name }}
                                    <svg width="12" height="12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                                    </svg>
                                </a>
                            @endif
                        </div>
                    @endif

                    <div class="msg-footer">
                        <div class="msg-time">{{ $msg->created_at->format('H:i') }}</div>
                        @if($msg->edited_at)
                            <div class="msg-edited">· modifié {{ $msg->edited_at->format('H:i') }}</div>
                        @endif
                        @if($isMe)
                            <div class="msg-tick {{ $msg->seen_at ? 'seen' : '' }}" title="{{ $msg->seen_at ? 'Vu' : 'Envoyé' }}">
                                {{ $msg->seen_at ? '✓✓' : '✓' }}
                            </div>
                        @endif
                    </div>
                </div>
                @endforeach

                {{-- Typing indicator --}}
                <div class="typing-indicator" id="typing-indicator">
                    <div class="typing-dots"><span></span><span></span><span></span></div>
                </div>
            </div>

            {{-- Traite notice --}}
            <div id="traite-notice" class="traite-notice"
                 style="{{ $reclamation->status !== 'traite' ? 'display:none;' : '' }}">
                ✅ Réclamation traitée — les réponses sont désactivées.
            </div>

            {{-- Reply area --}}
            @if($canReply)
            <div id="reply-wrapper" class="rc-reply"
                 style="{{ $reclamation->status === 'traite' ? 'display:none;' : '' }}">
                <form id="msg-form"
                      action="{{ route('reclamations.message', $reclamation) }}"
                      method="POST"
                      enctype="multipart/form-data">
                    @csrf
                    <div id="attach-preview" class="attach-preview">
                        <span id="attach-icon" style="font-size:18px;">📎</span>
                        <span id="attach-name" class="attach-name"></span>
                        <button type="button" class="clear-attachment" onclick="clearAttachment()">×</button>
                    </div>
                    <div class="reply-box">
                        <label for="attachment-input" class="icon-btn" title="Joindre un fichier">
                            <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/>
                            </svg>
                        </label>
                        <input type="file" id="attachment-input" name="attachment"
                               accept="image/*,.pdf,.doc,.docx,.xls,.xlsx,.txt,.zip"
                               style="display:none;" onchange="handleFileSelect(this)">

                        <textarea name="message" id="reply-input" class="reply-input"
                                  placeholder="Écrire un message…" rows="1"></textarea>

                        <button type="submit" class="send-btn" id="send-btn" title="Ctrl+Entrée">
                            <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                            </svg>
                        </button>
                    </div>
                    <div class="reply-hint">Ctrl + Entrée pour envoyer</div>
                </form>
            </div>
            @endif
        </div>{{-- /rc-chat --}}

        {{-- Sidebar — visible to all --}}
        <div class="rc-sidebar">

            {{-- Info card — visible to all roles --}}
            <div class="sidebar-card">
                <div class="sidebar-card-title">
                    <svg width="12" height="12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    Détails
                </div>

                <div class="info-pill">
                    <div class="info-pill-icon">🏷️</div>
                    <div>
                        <div class="info-pill-label">Type</div>
                        <div class="info-pill-val">{{ $tc['icon'] }} {{ $tc['label'] }}</div>
                    </div>
                </div>

                <div class="info-pill">
                    <div class="info-pill-icon">📋</div>
                    <div>
                        <div class="info-pill-label">Statut</div>
                        <div class="info-pill-val">
                            <span id="detail-status-badge"
                                  style="display:inline-flex;align-items:center;gap:4px;font-size:11px;font-weight:700;
                                         padding:3px 9px;border-radius:20px;
                                         background:{{ $sc['bg'] }};color:{{ $sc['color'] }};border:1px solid {{ $sc['border'] }};">
                                {{ $sc['icon'] }} {{ $sc['label'] }}
                            </span>
                        </div>
                    </div>
                </div>

                <div class="info-pill">
                    <div class="info-pill-icon">📅</div>
                    <div>
                        <div class="info-pill-label">Date d'ouverture</div>
                        <div class="info-pill-val">{{ $reclamation->created_at->format('d/m/Y à H:i') }}</div>
                    </div>
                </div>

                @unless($isStagiaire)
                <div class="info-pill">
                    <div class="info-pill-icon">👤</div>
                    <div>
                        <div class="info-pill-label">Stagiaire</div>
                        <div class="info-pill-val">{{ $reclamation->stagiaire?->name ?? '—' }}</div>
                    </div>
                </div>
                @endunless

                @if($reclamation->assignedUser)
                <div class="info-pill">
                    <div class="info-pill-icon">🎯</div>
                    <div>
                        <div class="info-pill-label">Assignée à</div>
                        <div class="info-pill-val">{{ $reclamation->assignedUser->name }}</div>
                    </div>
                </div>
                @else
                <div class="info-pill">
                    <div class="info-pill-icon">🎯</div>
                    <div>
                        <div class="info-pill-label">Assignée à</div>
                        <div class="info-pill-val" style="color:#94a3b8;font-weight:500;">Non assignée</div>
                    </div>
                </div>
                @endif

                <div class="info-pill">
                    <div class="info-pill-icon">💬</div>
                    <div>
                        <div class="info-pill-label">Messages</div>
                        <div class="info-pill-val">{{ $reclamation->messages->count() }}</div>
                    </div>
                </div>
            </div>

            {{-- Staff management card --}}
            @if($isStaff)
            <div class="sidebar-card">
                <div class="sidebar-card-title">
                    <svg width="12" height="12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                    Gestion
                </div>

                {{-- Assign --}}
                <div style="margin-bottom:14px;">
                    <label class="f-label">Assigner à</label>
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

                {{-- Status --}}
                <div>
                    <label class="f-label">Statut</label>
                    <form action="{{ route('reclamations.status', $reclamation) }}" method="POST">
                        @csrf @method('PATCH')
                        <select name="status" class="f-select">
                            @foreach(\App\Models\Reclamation::STATUSES as $k => $cfg)
                                <option value="{{ $k }}" {{ $reclamation->status === $k ? 'selected' : '' }}>
                                    {{ $cfg['icon'] }} {{ $cfg['label'] }}
                                </option>
                            @endforeach
                        </select>
                        <button type="submit" class="btn-primary">Mettre à jour le statut</button>
                    </form>
                </div>

                @can('reclamation-manage')
                <form action="{{ route('reclamations.destroy', $reclamation) }}" method="POST"
                      onsubmit="return confirm('Supprimer définitivement cette réclamation ?');">
                    @csrf @method('DELETE')
                    <button type="submit" class="btn-danger">🗑 Supprimer la réclamation</button>
                </form>
                @endcan
            </div>
            @endif

            {{-- Assigned user status update card --}}
            @if($isAssigned && !$isStaff)
            <div class="sidebar-card" id="assigned-status-panel">
                <div class="sidebar-card-title">
                    <svg width="12" height="12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    Statut de la réclamation
                </div>
                <div style="margin-bottom:6px;">
                    <label class="f-label">Statut actuel</label>
                    <span id="asgn-status-badge"
                          style="display:inline-flex;align-items:center;gap:5px;font-size:11px;font-weight:700;
                                 padding:5px 11px;border-radius:20px;
                                 background:{{ $sc['bg'] }};color:{{ $sc['color'] }};border:1px solid {{ $sc['border'] }};">
                        {{ $sc['icon'] }} {{ $sc['label'] }}
                    </span>
                </div>
                <label class="f-label" style="margin-top:10px;">Changer vers</label>
                <select id="asgn-status-select" class="f-select">
                    @foreach(\App\Models\Reclamation::STATUSES as $k => $cfg)
                        @if(in_array($k, ['en_cours', 'traite']))
                            <option value="{{ $k }}" {{ $reclamation->status === $k ? 'selected' : '' }}>
                                {{ $cfg['icon'] }} {{ $cfg['label'] }}
                            </option>
                        @endif
                    @endforeach
                </select>
                <button id="asgn-status-btn" class="btn-primary" onclick="updateAssignedStatus()">
                    ✅ Mettre à jour
                </button>
                <div id="asgn-status-msg" class="asgn-msg"></div>
            </div>
            @endif

        </div>{{-- /rc-sidebar --}}

    </div>{{-- /rc-body --}}
</div>{{-- /rc-page --}}

<script>
// ── Menu helpers ─────────────────────────────────────────────
function closeAllMenus() {
    document.querySelectorAll('.msg-actions.open').forEach(m => {
        m.classList.remove('open');
        m.closest('.msg-bubble')?.querySelector('.msg-dots-btn')?.classList.remove('visible');
    });
}
function toggleMenu(e, msgId) {
    e.stopPropagation();
    const menu = document.getElementById('msg-menu-' + msgId);
    if (!menu) return;
    const isOpen = menu.classList.contains('open');
    closeAllMenus();
    if (!isOpen) menu.classList.add('open');
}
document.addEventListener('click', closeAllMenus);

function attachBubbleHover(bubble) {
    const btn = bubble.querySelector('.msg-dots-btn');
    if (!btn) return;
    const msgId = bubble.id.replace('msg-', '');
    let hideTimer = null;
    const showBtn = () => { clearTimeout(hideTimer); btn.classList.add('visible'); };
    const scheduleHide = () => {
        hideTimer = setTimeout(() => {
            const menu = document.getElementById('msg-menu-' + msgId);
            if (menu?.classList.contains('open')) return;
            btn.classList.remove('visible');
            menu?.classList.remove('open');
        }, 300);
    };
    [bubble, btn].forEach(el => {
        el.addEventListener('mouseenter', showBtn);
        el.addEventListener('mouseleave', scheduleHide);
    });
    bubble.addEventListener('mouseenter', () => {
        const menu = document.getElementById('msg-menu-' + msgId);
        if (menu) {
            menu.addEventListener('mouseenter', showBtn);
            menu.addEventListener('mouseleave', scheduleHide);
        }
    });
}

// ── Attachment preview ────────────────────────────────────────
function handleFileSelect(input) {
    const file = input.files[0];
    if (!file) return;
    document.getElementById('attach-name').textContent = file.name;
    document.getElementById('attach-icon').textContent = file.type.startsWith('image/') ? '🖼️' : '📎';
    document.getElementById('attach-preview').classList.add('visible');
}
function clearAttachment() {
    const input = document.getElementById('attachment-input');
    if (input) input.value = '';
    document.getElementById('attach-preview').classList.remove('visible');
}

// ── Custom modals ─────────────────────────────────────────────
function rcConfirm(title, body, onConfirm) {
    const overlay = document.createElement('div');
    overlay.className = 'rc-overlay';
    overlay.innerHTML = `
        <div class="rc-modal">
            <div class="rc-modal-icon">🗑️</div>
            <div class="rc-modal-title">${title}</div>
            <div class="rc-modal-body">${body}</div>
            <div class="rc-modal-btns">
                <button class="rc-btn rc-btn-cancel" id="rc-cancel">Annuler</button>
                <button class="rc-btn rc-btn-danger" id="rc-confirm">Supprimer</button>
            </div>
        </div>`;
    document.body.appendChild(overlay);
    overlay.querySelector('#rc-cancel').onclick  = () => overlay.remove();
    overlay.querySelector('#rc-confirm').onclick = () => { overlay.remove(); onConfirm(); };
    overlay.addEventListener('click', e => { if (e.target === overlay) overlay.remove(); });
}
function rcAlert(icon, title, body) {
    const overlay = document.createElement('div');
    overlay.className = 'rc-overlay';
    overlay.innerHTML = `
        <div class="rc-modal">
            <div class="rc-modal-icon">${icon}</div>
            <div class="rc-modal-title">${title}</div>
            <div class="rc-modal-body">${body}</div>
            <div class="rc-modal-btns">
                <button class="rc-btn rc-btn-ok" id="rc-ok">OK</button>
            </div>
        </div>`;
    document.body.appendChild(overlay);
    overlay.querySelector('#rc-ok').onclick = () => overlay.remove();
    overlay.addEventListener('click', e => { if (e.target === overlay) overlay.remove(); });
}

document.addEventListener('DOMContentLoaded', function () {

    const CURRENT_USER_ID = {{ $user->id }};
    const RECLAMATION_ID  = {{ $reclamation->id }};
    const MARK_SEEN_URL   = "{{ route('reclamations.seen', $reclamation) }}";
    const MSG_BASE_URL    = "/reclamations/{{ $reclamation->id }}/message/";
    const CSRF_TOKEN      = "{{ csrf_token() }}";

    const area     = document.getElementById('messages-area');
    const form     = document.getElementById('msg-form');
    const input    = document.getElementById('reply-input');
    const sendBtn  = document.getElementById('send-btn');
    const typingEl = document.getElementById('typing-indicator');

    // Mark seen on load
    fetch(MARK_SEEN_URL, { method:'POST', headers:{'X-CSRF-TOKEN':CSRF_TOKEN,'Accept':'application/json'} });

    function scrollBottom() { if (area) area.scrollTop = area.scrollHeight; }
    scrollBottom();

    document.querySelectorAll('.msg-bubble').forEach(attachBubbleHover);

    function escHtml(str) {
        return String(str ?? '')
            .replace(/&/g,'&amp;').replace(/</g,'&lt;')
            .replace(/>/g,'&gt;').replace(/"/g,'&quot;');
    }
    function capitalize(str) { return str ? str.charAt(0).toUpperCase() + str.slice(1) : ''; }

    function buildAttachmentHtml(data, isMine) {
        if (!data.attachment_path) return '';
        const isImage = data.attachment_mime?.startsWith('image/');
        if (isImage) {
            return `<div class="attachment-container">
                        <a href="${data.attachment_path}" target="_blank">
                            <img src="${data.attachment_path}" alt="${escHtml(data.attachment_name??'image')}" class="attachment-image">
                        </a>
                    </div>`;
        }
        return `<div class="attachment-container">
                    <a href="${data.attachment_path}" target="_blank"
                       download="${escHtml(data.attachment_name??'fichier')}" class="attachment-file">
                        📎 ${escHtml(data.attachment_name??'Fichier')}
                        <svg width="12" height="12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                        </svg>
                    </a>
                </div>`;
    }

    function renderBubble(data, isMine) {
        const div = document.createElement('div');
        div.className  = 'msg-bubble ' + (isMine ? 'mine' : 'theirs');
        div.id         = 'msg-' + data.id;
        div.dataset.seen = '0';
        div.dataset.mine = isMine ? '1' : '0';

        let html = '';
        const hasAttachment = !!data.attachment_path;

        if (isMine) {
            html += `<button class="msg-dots-btn" onclick="toggleMenu(event,${data.id})" title="Options">⋯</button>
                     <div class="msg-actions" id="msg-menu-${data.id}">
                        ${!hasAttachment ? `<button class="msg-action-btn btn-edit-msg" onclick="startEdit(${data.id});closeAllMenus()">✏️ Modifier</button>` : ''}
                        <button class="msg-action-btn btn-delete-msg" onclick="deleteMsg(${data.id});closeAllMenus()">🗑 Supprimer</button>
                     </div>`;
        }
        if (!isMine) {
            const role = data.sender?.role ? ` <span class="msg-sender-role">· ${capitalize(data.sender.role)}</span>` : '';
            html += `<div class="msg-sender">${escHtml(data.sender?.name??'')}${role}</div>`;
        }
        if (data.message) {
            html += `<div class="msg-content" id="msg-content-${data.id}">${escHtml(data.message)}</div>`;
        } else {
            html += `<div id="msg-content-${data.id}" style="display:none;"></div>`;
        }
        html += buildAttachmentHtml(data, isMine);
        html += `<div class="msg-footer">
                    <div class="msg-time">${escHtml(data.created_at)}</div>
                    ${isMine ? '<div class="msg-tick" title="Envoyé">✓</div>' : ''}
                 </div>`;

        div.innerHTML = html;
        area.insertBefore(div, typingEl);
        attachBubbleHover(div);
        scrollBottom();
    }

    function removeBubble(msgId) {
        const el = document.getElementById('msg-' + msgId);
        if (!el) return;
        el.style.transition = 'opacity .2s, transform .2s';
        el.style.opacity = '0';
        el.style.transform = 'scale(.95)';
        setTimeout(() => el.remove(), 220);
    }

    function applyEdit(msgId, newText, editedAt) {
        const contentEl = document.getElementById('msg-content-' + msgId);
        if (contentEl) contentEl.textContent = newText ?? '';
        const bubble  = document.getElementById('msg-' + msgId);
        const dotsBtn = bubble?.querySelector('.msg-dots-btn');
        if (dotsBtn) dotsBtn.style.display = '';
        const footer = bubble?.querySelector('.msg-footer');
        if (footer) {
            let editedEl = footer.querySelector('.msg-edited');
            if (!editedEl) {
                editedEl = document.createElement('div');
                editedEl.className = 'msg-edited';
                footer.appendChild(editedEl);
            }
            editedEl.textContent = '· modifié ' + editedAt;
        }
    }

    let _lastSeenBubbleId = null;
    function markBubbleSeen(msgId, initials, seenByName) {
        const bubble = document.getElementById('msg-' + msgId);
        if (!bubble || bubble.dataset.mine !== '1') return;
        bubble.dataset.seen = '1';
        bubble.querySelector('.msg-dots-btn')?.remove();
        document.getElementById('msg-menu-' + msgId)?.remove();
        const tick = bubble.querySelector('.msg-tick');
        if (tick) { tick.classList.add('seen'); tick.title = 'Vu'; tick.textContent = '✓✓'; }
        if (_lastSeenBubbleId && _lastSeenBubbleId !== msgId) {
            document.getElementById('msg-' + _lastSeenBubbleId)?.querySelector('.seen-indicator')?.remove();
        }
        bubble.querySelector('.seen-indicator')?.remove();
        const ind = document.createElement('div');
        ind.className = 'seen-indicator';
        ind.innerHTML = `<span class="seen-text">Vu</span>
                         <div class="seen-avatar pop" title="${escHtml(seenByName??'Lu')}">${escHtml(initials??'?')}</div>`;
        bubble.appendChild(ind);
        _lastSeenBubbleId = msgId;
    }

    window.deleteMsg = async function(msgId) {
        rcConfirm('Supprimer ce message ?', 'Cette action est irréversible.', async () => {
            const res = await fetch(MSG_BASE_URL + msgId, {
                method:'DELETE', headers:{'X-CSRF-TOKEN':CSRF_TOKEN,'Accept':'application/json'}
            });
            if (res.ok) removeBubble(msgId);
            else { const err = await res.json(); rcAlert('❌','Impossible de supprimer', err.error??'Une erreur est survenue.'); }
        });
    };

    window.startEdit = function(msgId) {
        const contentEl = document.getElementById('msg-content-' + msgId);
        if (!contentEl) return;
        const originalText = contentEl.textContent.trim();
        contentEl.innerHTML = `
            <div class="edit-box">
                <textarea class="edit-textarea" id="edit-ta-${msgId}">${escHtml(originalText)}</textarea>
                <div class="edit-actions">
                    <button class="btn-cancel-edit" onclick="cancelEdit(${msgId},\`${escHtml(originalText)}\`)">Annuler</button>
                    <button class="btn-save-edit" onclick="saveEdit(${msgId})">Enregistrer</button>
                </div>
            </div>`;
        const ta = document.getElementById('edit-ta-' + msgId);
        if (ta) { ta.focus(); ta.setSelectionRange(ta.value.length, ta.value.length); }
        const dotsBtn = document.getElementById('msg-' + msgId)?.querySelector('.msg-dots-btn');
        if (dotsBtn) dotsBtn.style.display = 'none';
    };
    window.cancelEdit = function(msgId, originalText) {
        const contentEl = document.getElementById('msg-content-' + msgId);
        if (contentEl) contentEl.textContent = originalText;
        const dotsBtn = document.getElementById('msg-' + msgId)?.querySelector('.msg-dots-btn');
        if (dotsBtn) dotsBtn.style.display = '';
    };
    window.saveEdit = async function(msgId) {
        const ta = document.getElementById('edit-ta-' + msgId);
        if (!ta) return;
        const newText = ta.value.trim();
        if (!newText) return;
        const res = await fetch(MSG_BASE_URL + msgId, {
            method:'PATCH',
            headers:{'Content-Type':'application/json','X-CSRF-TOKEN':CSRF_TOKEN,'Accept':'application/json'},
            body: JSON.stringify({ message: newText }),
        });
        if (res.ok) { const data = await res.json(); applyEdit(msgId, data.message, data.edited_at); }
        else { const err = await res.json(); rcAlert('❌','Impossible de modifier', err.error??'Une erreur est survenue.'); }
    };

    if (form) {
        form.addEventListener('submit', async function(e) {
            e.preventDefault();
            if (sendBtn.disabled) return;
            const message   = input?.value.trim();
            const fileInput = document.getElementById('attachment-input');
            const file      = fileInput?.files[0];
            if (!message && !file) return;
            sendBtn.disabled = true;
            const formData = new FormData();
            if (message) formData.append('message', message);
            if (file)    formData.append('attachment', file);
            formData.append('_token', CSRF_TOKEN);
            try {
                const res  = await fetch(form.action, { method:'POST', headers:{'Accept':'application/json'}, body:formData });
                const data = await res.json();
                if (!res.ok) { rcAlert('❌','Erreur', data.error||'Erreur lors de l\'envoi.'); return; }
                renderBubble(data, true);
                if (input)     input.value = '';
                if (fileInput) fileInput.value = '';
                clearAttachment();
                input?.focus();
            } catch (err) {
                console.error(err);
                rcAlert('❌','Erreur réseau','Impossible d\'envoyer le message.');
            } finally {
                sendBtn.disabled = false;
            }
        });
    }

    if (input) {
        input.addEventListener('input', () => {
            input.style.height = 'auto';
            input.style.height = Math.min(input.scrollHeight, 120) + 'px';
        });
        input.addEventListener('keydown', e => {
            if (e.key === 'Enter' && e.ctrlKey) { e.preventDefault(); form.dispatchEvent(new Event('submit')); }
        });
    }

    if (typeof window.Echo !== 'undefined') {
        window.Echo.private('reclamation.' + RECLAMATION_ID)
            .listen('.ReclamationMessageSent', (e) => {
                if (typingEl) typingEl.classList.remove('visible');
                if (e.sender?.id !== CURRENT_USER_ID) {
                    renderBubble(e, false);
                    fetch(MARK_SEEN_URL, { method:'POST', headers:{'X-CSRF-TOKEN':CSRF_TOKEN} });
                }
            })
            .listen('.ReclamationMessageDeleted', (e) => { removeBubble(e.message_id); })
            .listen('.ReclamationMessageUpdated', (e) => { applyEdit(e.id, e.message, e.edited_at); })
            .listen('.ReclamationMessageSeen', (e) => {
                if (!Array.isArray(e.message_ids) || !e.message_ids.length) return;
                const lastId = e.message_ids[e.message_ids.length - 1];
                e.message_ids.forEach(id => markBubbleSeen(id, e.initials, e.seen_by_name));
                e.message_ids.forEach(id => {
                    if (id !== lastId) document.getElementById('msg-'+id)?.querySelector('.seen-indicator')?.remove();
                });
            })
            .listen('.ReclamationStatusUpdated', (e) => {
                ['status-badge', 'detail-status-badge'].forEach(id => {
                    const badge = document.getElementById(id);
                    if (badge) {
                        badge.style.background = e.bg;
                        badge.style.color      = e.color;
                        badge.style.border     = '1px solid ' + e.border;
                        badge.textContent      = e.icon + ' ' + e.label;
                        badge.classList.add('status-updated');
                        setTimeout(() => badge.classList.remove('status-updated'), 400);
                    }
                });
                const traiteNotice = document.getElementById('traite-notice');
                const replyWrapper = document.getElementById('reply-wrapper');
                if (e.status === 'traite') {
                    if (traiteNotice) traiteNotice.style.display = 'flex';
                    if (replyWrapper) replyWrapper.style.display = 'none';
                } else {
                    if (traiteNotice) traiteNotice.style.display = 'none';
                    if (replyWrapper) replyWrapper.style.display = 'block';
                }
            })
            .listen('.ReclamationDeleted', () => {
                const overlay = document.createElement('div');
                overlay.className = 'rc-overlay';
                overlay.innerHTML = `
                    <div class="rc-modal" style="text-align:center;">
                        <div class="rc-modal-icon">🗑️</div>
                        <div class="rc-modal-title">Réclamation supprimée</div>
                        <div class="rc-modal-body">Cette réclamation a été supprimée. Redirection en cours… (<span id="cd">3</span>s)</div>
                    </div>`;
                document.body.appendChild(overlay);
                let n = 3;
                const iv = setInterval(() => {
                    const el = document.getElementById('cd');
                    if (el) el.textContent = --n;
                    if (n <= 0) { clearInterval(iv); window.location.href = '{{ route("reclamations.index") }}'; }
                }, 1000);
            })
            .listenForWhisper('typing', () => {
                if (typingEl) { typingEl.classList.add('visible'); scrollBottom(); setTimeout(() => typingEl.classList.remove('visible'), 3000); }
            });

        if (input) {
            input.addEventListener('input', () => {
                window.Echo.private('reclamation.' + RECLAMATION_ID).whisper('typing', { user: CURRENT_USER_ID });
            });
        }
    }

    window.updateAssignedStatus = async function () {
        const sel    = document.getElementById('asgn-status-select');
        const btn    = document.getElementById('asgn-status-btn');
        const msgBox = document.getElementById('asgn-status-msg');
        if (!sel || !btn) return;
        const status = sel.value;
        btn.disabled = true; btn.textContent = '…';
        try {
            const res  = await fetch('{{ route("reclamations.status", $reclamation) }}', {
                method:'POST',
                headers:{'Content-Type':'application/json','X-CSRF-TOKEN':CSRF_TOKEN,'Accept':'application/json'},
                body: JSON.stringify({ status, _method:'PATCH' }),
            });
            const data = await res.json();
            if (res.ok && data.ok) {
                ['status-badge','asgn-status-badge','detail-status-badge'].forEach(id => {
                    const el = document.getElementById(id);
                    if (el && data.badge) {
                        el.style.background = data.badge.bg;
                        el.style.color      = data.badge.color;
                        el.style.border     = '1px solid ' + data.badge.border;
                        el.textContent      = data.badge.icon + ' ' + data.badge.label;
                        el.classList.add('status-updated');
                        setTimeout(() => el.classList.remove('status-updated'), 400);
                    }
                });
                const traiteNotice = document.getElementById('traite-notice');
                const replyWrapper = document.getElementById('reply-wrapper');
                if (status === 'traite') {
                    if (traiteNotice) traiteNotice.style.display = 'flex';
                    if (replyWrapper) replyWrapper.style.display = 'none';
                } else {
                    if (traiteNotice) traiteNotice.style.display = 'none';
                    if (replyWrapper) replyWrapper.style.display = 'block';
                }
                showAsgnMsg('✅ Statut mis à jour.', '#dcfce7', '#166534');
            } else {
                showAsgnMsg('❌ ' + (data.message ?? 'Erreur.'), '#fee2e2', '#be123c');
            }
        } catch { showAsgnMsg('❌ Erreur réseau.', '#fee2e2', '#be123c'); }
        finally { btn.disabled = false; btn.textContent = '✅ Mettre à jour'; }
    };

    function showAsgnMsg(text, bg, color) {
        const el = document.getElementById('asgn-status-msg');
        if (!el) return;
        el.textContent = text;
        el.style.cssText = `background:${bg};color:${color};display:block;font-size:11px;font-weight:600;margin-top:10px;padding:8px 12px;border-radius:10px;`;
        setTimeout(() => el.style.display = 'none', 4000);
    }
});
</script>

@push('scripts')
<script>
window.__currentReclamationId = {{ $reclamation->id }};
document.addEventListener('DOMContentLoaded', function () {
    fetch('/notifications/reclamation/{{ $reclamation->id }}/read', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '{{ csrf_token() }}',
            'Accept': 'application/json',
        },
    })
    .then(r => r.json())
    .then(data => { if (typeof setUnreadCount === 'function') setUnreadCount(data.unread_count ?? 0); })
    .catch(() => {});
});
</script>
@endpush
@endsection