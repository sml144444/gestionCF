{{-- resources/views/reclamations/my.blade.php --}}
@extends('layouts.app')
@section('title', 'Mes réclamations')
@section('page-title', 'Mes réclamations')

{{-- ✅ Echo CDN --}}
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
    $user         = Auth::user();
    $gradient     = 'linear-gradient(135deg,#1a4f8a 0%,#2563eb 100%)';
    $statusConfig = \App\Models\Reclamation::STATUSES;
    $typeConfig   = \App\Models\Reclamation::TYPES;
@endphp

<style>
* { box-sizing:border-box; }
.my-rc { font-family:'Segoe UI',system-ui,sans-serif; max-width:900px; margin:0 auto; }

/* Hero */
.hero { background:{{ $gradient }}; border-radius:20px; padding:26px 30px; margin-bottom:22px;
        display:flex; align-items:center; justify-content:space-between;
        gap:14px; flex-wrap:wrap; position:relative; overflow:hidden; }
.hero::after { content:''; position:absolute; right:-40px; top:-40px; width:200px; height:200px;
               border-radius:50%; background:rgba(255,255,255,0.06); pointer-events:none; }
.btn-new { background:rgba(255,255,255,0.15); border:1.5px solid rgba(255,255,255,0.3);
           color:white; font-size:12px; font-weight:700; padding:9px 18px; border-radius:99px;
           text-decoration:none; display:inline-flex; align-items:center; gap:6px;
           transition:background .15s; white-space:nowrap; }
.btn-new:hover { background:rgba(255,255,255,0.25); }

/* Flash */
.flash-ok { display:flex; align-items:center; gap:12px; padding:14px 18px; border-radius:14px;
            margin-bottom:18px; background:#f0f7ff; border:1px solid #bfdbfe; animation:fi .3s; }
@keyframes fi { from{opacity:0;transform:translateY(-6px)} to{opacity:1;transform:translateY(0)} }

/* Ticket card */
.ticket-card { background:white; border-radius:18px; border:1px solid #e2e8f0;
               margin-bottom:12px; transition:all .2s; overflow:hidden;
               display:flex; flex-direction:column; }
.ticket-card:hover { transform:translateY(-2px); box-shadow:0 8px 24px rgba(0,0,0,0.08); }
.ticket-card.new-reply { border-left:4px solid #2563eb; }
.ticket-head { padding:16px 20px; display:flex; align-items:flex-start;
               justify-content:space-between; gap:12px; }
.ticket-body { padding:0 20px 14px; font-size:13px; color:#475569; line-height:1.6;
               overflow:hidden; display:-webkit-box; -webkit-line-clamp:2; -webkit-box-orient:vertical;
               word-break:break-all; overflow-wrap:break-word; }
.ticket-footer { padding:12px 20px; border-top:1px solid #f1f5f9; background:#fafbfd;
                 display:flex; align-items:center; justify-content:space-between; gap:10px; }
.badge { font-size:9px; font-weight:700; padding:3px 9px; border-radius:7px; white-space:nowrap; }
.msg-count { display:inline-flex; align-items:center; gap:5px; font-size:11px;
             font-weight:700; color:#64748b; padding:4px 10px; border-radius:8px;
             background:#f1f5f9; }
.msg-count.has-msgs { color:#2563eb; background:#eff6ff; }
.btn-open { font-size:11px; font-weight:700; padding:7px 18px; border-radius:10px;
            background:{{ $gradient }}; color:white; text-decoration:none;
            display:inline-flex; align-items:center; gap:5px; transition:opacity .15s; }
.btn-open:hover { opacity:.85; }

/* RT: deleted card fade */
@keyframes cardFadeOut { to{opacity:0;transform:scale(.96);max-height:0;margin:0;padding:0;} }
.rt-deleting { animation:cardFadeOut .4s ease forwards; overflow:hidden; }

/* RT: status badge flash */
@keyframes flashIn { from{transform:scale(.9);opacity:0} to{transform:scale(1);opacity:1} }
.status-flash { animation:flashIn .3s ease; }

/* RT toast */
.rt-toast { position:fixed; top:20px; right:20px; z-index:9999;
            background:white; border:1px solid #bfdbfe; border-radius:16px;
            padding:14px 18px; box-shadow:0 8px 32px rgba(0,0,0,0.12);
            display:flex; align-items:center; gap:12px; min-width:260px;
            animation:toastIn .3s ease; }
@keyframes toastIn { from{opacity:0;transform:translateX(30px)} to{opacity:1;transform:translateX(0)} }
.rt-toast-hide { animation:toastOut .3s ease forwards; }
@keyframes toastOut { to{opacity:0;transform:translateX(30px)} }
</style>

<div class="my-rc">

@if(session('success'))
<div class="flash-ok">
    <svg width="16" height="16" fill="none" stroke="#1e40af" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
    </svg>
    <span style="font-size:13px;font-weight:600;color:#1e40af;">{{ session('success') }}</span>
</div>
@endif

{{-- Hero --}}
<div class="hero">
    <div style="display:flex;align-items:center;gap:16px;">
        <div style="width:52px;height:52px;border-radius:16px;background:rgba(255,255,255,0.15);
                    display:flex;align-items:center;justify-content:center;">
            <svg width="26" height="26" fill="none" stroke="white" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/>
            </svg>
        </div>
        <div>
            <h1 style="font-size:20px;font-weight:800;color:white;margin:0;">Mes réclamations</h1>
            <p style="font-size:12px;color:rgba(255,255,255,0.72);margin:3px 0 0;" id="total-label">
                {{ $reclamations->total() }} réclamation(s) soumise(s)
            </p>
        </div>
    </div>
    <a href="{{ route('reclamations.create') }}" class="btn-new">
        <svg width="13" height="13" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/>
        </svg>
        Nouvelle réclamation
    </a>
</div>

{{-- Info box --}}
<div style="padding:13px 16px;background:#eff6ff;border:1px solid #bfdbfe;border-radius:14px;
            margin-bottom:18px;display:flex;align-items:flex-start;gap:10px;">
    <svg width="16" height="16" fill="none" stroke="#1d4ed8" viewBox="0 0 24 24" style="flex-shrink:0;margin-top:1px;">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
              d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
    </svg>
    <p style="font-size:12px;color:#1e40af;margin:0;line-height:1.5;">
        Cliquez sur <strong>Voir la conversation</strong> pour suivre l'avancement de votre réclamation
        et répondre aux messages de l'équipe.
    </p>
</div>

{{-- List --}}
<div id="tickets-list">
@forelse($reclamations as $rec)
    @php
        $sc     = $statusConfig[$rec->status] ?? $statusConfig['en_attente'];
        $tc     = $typeConfig[$rec->type]     ?? $typeConfig['autre'];
        $hasNew = $rec->messages_count > 0;
        $newReply = $rec->status === 'en_cours' && $hasNew;
    @endphp
    <div id="rec-row-{{ $rec->id }}" class="ticket-card {{ $newReply ? 'new-reply' : '' }}">
        <div class="ticket-head">
            <div style="display:flex;align-items:center;gap:10px;flex-wrap:wrap;">
                <span style="font-size:10px;color:#94a3b8;font-weight:600;">#{{ $rec->id }}</span>
                <span class="badge" style="background:#eff6ff;color:#1e40af;">
                    {{ $tc['icon'] }} {{ $tc['label'] }}
                </span>
                @if($newReply)
                    <span class="badge" style="background:#dbeafe;color:#1e40af;animation:pulse 2s infinite;">
                        🔔 Nouvelle réponse
                    </span>
                @endif
            </div>
            <span id="status-badge-{{ $rec->id }}" class="badge"
                  style="background:{{ $sc['bg'] }};color:{{ $sc['color'] }};border:1px solid {{ $sc['border'] }};">
                {{ $sc['icon'] }} {{ $sc['label'] }}
            </span>
        </div>

        <div class="ticket-body">{{ $rec->description }}</div>

        <div class="ticket-footer">
            <div style="display:flex;align-items:center;gap:10px;">
                <span class="msg-count {{ $hasNew ? 'has-msgs' : '' }}" id="msg-count-{{ $rec->id }}">
                    💬 {{ $rec->messages_count }} message(s)
                </span>
                <span style="font-size:10px;color:#94a3b8;">
                    {{ $rec->created_at->format('d/m/Y') }} · mis à jour {{ $rec->updated_at->diffForHumans() }}
                </span>
            </div>
            <a href="{{ route('reclamations.show', $rec) }}" class="btn-open">
                Voir la conversation →
            </a>
        </div>
    </div>
@empty
    <div id="empty-state" style="text-align:center;padding:60px 20px;background:white;border-radius:20px;border:1px solid #e2e8f0;">
        <div style="font-size:48px;margin-bottom:12px;">💬</div>
        <p style="font-size:15px;font-weight:700;color:#1e293b;margin:0 0 6px;">Aucune réclamation</p>
        <p style="font-size:12px;color:#94a3b8;margin:0 0 22px;">
            Vous n'avez pas encore soumis de réclamation.
        </p>
        <a href="{{ route('reclamations.create') }}" class="btn-open">
            <svg width="13" height="13" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/>
            </svg>
            Soumettre une réclamation
        </a>
    </div>
@endforelse
</div>

@if($reclamations->hasPages())
<div style="margin-top:16px;">{{ $reclamations->links() }}</div>
@endif

</div>

<style>
@keyframes pulse { 0%, 100% { opacity:1; } 50% { opacity:.6; } }
</style>

<script>
document.addEventListener('DOMContentLoaded', function () {
    if (typeof window.Echo === 'undefined') {
        console.error('❌ Echo not loaded on my.blade.php!');
        return;
    }

    const CURRENT_USER_ID = {{ $user->id }};

    // ── Private user channel ─────────────────────────────────
    window.Echo.private('user.' + CURRENT_USER_ID)

        // ✅ Admin deleted this stagiaire's reclamation
        .listen('.ReclamationDeleted', (e) => {
            console.log('🗑️ ReclamationDeleted:', e);
            const card = document.getElementById('rec-row-' + e.reclamation_id);
            if (!card) return;
            card.classList.add('rt-deleting');
            setTimeout(() => {
                card.remove();
                // Update total label
                const lbl = document.getElementById('total-label');
                if (lbl) {
                    const match = lbl.textContent.match(/\d+/);
                    if (match) {
                        const n = Math.max(0, parseInt(match[0]) - 1);
                        lbl.textContent = n + ' réclamation(s) soumise(s)';
                    }
                }
                // Show empty state if no cards left
                const remaining = document.querySelectorAll('#tickets-list .ticket-card');
                if (remaining.length === 0) {
                    showDeletedNotice();
                }
            }, 450);
            showToast('🗑️', 'Réclamation supprimée',
                'La réclamation #' + e.reclamation_id + ' a été supprimée par l\'administration.');
        })

        // ✅ Admin assigned this stagiaire's reclamation (status may change soon)
        .listen('.ReclamationAssigned', (e) => {
            console.log('👤 ReclamationAssigned:', e);
            showToast('👤', 'Réclamation prise en charge',
                'Votre réclamation #' + e.reclamation_id + ' a été assignée à ' + (e.stagiaire || 'un responsable') + '.');
        });

    console.log('✅ Stagiaire real-time listeners active on user.' + CURRENT_USER_ID);

    // ── Helpers ─────────────────────────────────────────────
    function escHtml(s) {
        return String(s||'').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;');
    }

    function showDeletedNotice() {
        const list = document.getElementById('tickets-list');
        if (!list) return;
        list.innerHTML = `
            <div style="text-align:center;padding:60px 20px;background:white;border-radius:20px;border:1px solid #e2e8f0;">
                <div style="font-size:48px;margin-bottom:12px;">💬</div>
                <p style="font-size:15px;font-weight:700;color:#1e293b;margin:0 0 6px;">Aucune réclamation</p>
                <p style="font-size:12px;color:#94a3b8;margin:0 0 22px;">
                    Vous n'avez pas encore soumis de réclamation.
                </p>
                <a href="{{ route('reclamations.create') }}" class="btn-open">
                    Soumettre une réclamation
                </a>
            </div>
        `;
    }

    let _toastTimer = null;
    function showToast(icon, title, body) {
        const old = document.getElementById('rt-toast');
        if (old) old.remove();
        if (_toastTimer) clearTimeout(_toastTimer);

        const toast = document.createElement('div');
        toast.id = 'rt-toast';
        toast.className = 'rt-toast';
        toast.innerHTML = `
            <div style="font-size:22px;">${icon}</div>
            <div>
                <div style="font-size:12px;font-weight:800;color:#1e293b;">${escHtml(title)}</div>
                <div style="font-size:11px;color:#64748b;margin-top:2px;">${escHtml(body)}</div>
            </div>
            <button onclick="this.parentElement.remove()"
                style="margin-left:auto;background:none;border:none;cursor:pointer;color:#94a3b8;font-size:16px;">✕</button>
        `;
        document.body.appendChild(toast);
        _toastTimer = setTimeout(() => {
            toast.classList.add('rt-toast-hide');
            setTimeout(() => toast.remove(), 300);
        }, 6000);
    }
});
</script>
@endsection