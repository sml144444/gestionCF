{{-- resources/views/reportations/assigned.blade.php --}}
@extends('layouts.app')
@section('title', 'Reportations assignées')
@section('page-title', 'Reportations assignées')

@section('content')
<style>
.rp-wrap { font-family:'Segoe UI',system-ui,sans-serif; }
.rp-card { background:white; border-radius:16px; border:1px solid #e2e8f0; box-shadow:0 2px 10px rgba(0,0,0,0.05); overflow:hidden; margin-bottom:14px; transition:box-shadow .2s; }
.rp-card:hover { box-shadow:0 4px 20px rgba(0,0,0,0.08); }
.rp-input { height:40px; padding:0 12px; border-radius:10px; border:1.5px solid #e2e8f0; background:#f8fafc; font-size:13px; color:#1e293b; outline:none; transition:all .15s; box-sizing:border-box; }
.rp-input:focus { border-color:#1e293b; background:white; }
.status-pill { display:inline-flex; align-items:center; gap:4px; padding:4px 12px; border-radius:99px; font-size:10px; font-weight:700; }
.status-pill.attente { background:#fff7ed; color:#92400e; border:1px solid #fde68a; }
.status-pill.valide  { background:#f0fdf4; color:#15803d; border:1px solid #bbf7d0; }
.status-pill.refuse  { background:#fff1f2; color:#dc2626; border:1px solid #fecdd3; }
.rp-btn { height:36px; padding:0 14px; border-radius:9px; font-size:12px; font-weight:700; border:none; cursor:pointer; display:inline-flex; align-items:center; gap:5px; transition:opacity .15s; text-decoration:none; }
.rp-btn:hover { opacity:.85; }
.rp-btn.ghost { background:#f1f5f9; color:#475569; border:1px solid #e2e8f0; }
.tab-pill { padding:7px 14px; border-radius:99px; font-size:12px; font-weight:600; text-decoration:none; border:1.5px solid #e2e8f0; background:white; color:#64748b; transition:all .15s; display:inline-flex; align-items:center; gap:6px; }
.tab-pill:hover { border-color:#1e293b; color:#1e293b; background:#f1f5f9; }
.tab-pill.active { background:#1e293b; border-color:#1e293b; color:white; }
.tab-pill .badge { font-size:9px; padding:1px 7px; border-radius:99px; font-weight:800; }
.tab-pill.active .badge { background:rgba(255,255,255,0.25); color:white; }
.tab-pill:not(.active) .badge { background:#f1f5f9; color:#1e293b; }
</style>

<div class="rp-wrap">

{{-- FLASH --}}
@if(session('success'))
    <div style="margin-bottom:16px;padding:12px 16px;border-radius:12px;font-size:13px;display:flex;align-items:center;gap:8px;background:#f0fdf4;border:1px solid #bbf7d0;color:#15803d;">
        ✓ {{ session('success') }}
    </div>
@endif
@if(session('error'))
    <div style="margin-bottom:16px;padding:12px 16px;border-radius:12px;font-size:13px;background:#fff1f2;border:1px solid #fecdd3;color:#dc2626;">
        ✕ {{ session('error') }}
    </div>
@endif

{{-- HEADER --}}
<div style="display:flex;align-items:flex-start;justify-content:space-between;flex-wrap:wrap;gap:12px;margin-bottom:20px;">
    <div>
        <h1 style="font-size:20px;font-weight:800;color:#0f172a;margin:0;">Reportations assignées</h1>
        <p style="font-size:12px;color:#64748b;margin:4px 0 0;">Demandes de report qui vous ont été assignées</p>
    </div>
    <div style="display:flex;gap:10px;flex-wrap:wrap;">
        <div style="padding:10px 16px;border-radius:12px;text-align:center;background:#fff7ed;border:1px solid #fde68a;">
            <div style="font-size:22px;font-weight:800;color:#92400e;">{{ $counts['en_attente'] }}</div>
            <div style="font-size:9px;font-weight:700;color:#92400e;text-transform:uppercase;letter-spacing:.5px;">En attente</div>
        </div>
        <div style="padding:10px 16px;border-radius:12px;text-align:center;background:#f0fdf4;border:1px solid #bbf7d0;">
            <div style="font-size:22px;font-weight:800;color:#15803d;">{{ $counts['valide'] }}</div>
            <div style="font-size:9px;font-weight:700;color:#15803d;text-transform:uppercase;letter-spacing:.5px;">Acceptées</div>
        </div>
        <div style="padding:10px 16px;border-radius:12px;text-align:center;background:#fff1f2;border:1px solid #fecdd3;">
            <div style="font-size:22px;font-weight:800;color:#dc2626;">{{ $counts['refuse'] }}</div>
            <div style="font-size:9px;font-weight:700;color:#dc2626;text-transform:uppercase;letter-spacing:.5px;">Refusées</div>
        </div>
    </div>
</div>

{{-- STATUS TABS + SEARCH --}}
<div style="display:flex;gap:8px;flex-wrap:wrap;margin-bottom:18px;align-items:center;">
    @foreach([['en_attente','⏳','En attente'],['valide','✓','Acceptées'],['refuse','✕','Refusées'],['','📋','Toutes']] as [$val,$icon,$label])
    <a href="{{ route('reportations.assigned', array_merge(request()->except('status','page'), ['status'=>$val])) }}"
       class="tab-pill {{ $status === $val ? 'active' : '' }}">
        {{ $icon }} {{ $label }}
        <span class="badge">{{ $val === '' ? array_sum($counts) : ($counts[$val] ?? 0) }}</span>
    </a>
    @endforeach

    <form method="GET" action="{{ route('reportations.assigned') }}" style="margin-left:auto;display:flex;gap:8px;">
        <input type="hidden" name="status" value="{{ $status }}">
        <input type="text" name="search" value="{{ $search ?? '' }}" placeholder="Rechercher formateur…" class="rp-input" style="width:200px;">
        <button type="submit" style="height:40px;padding:0 14px;border-radius:10px;border:none;background:#1e293b;color:white;font-size:13px;font-weight:600;cursor:pointer;">🔍</button>
    </form>
</div>

{{-- CARDS --}}
@forelse($reportations as $rp)
@php $emploi = $rp->emploiDuTemps; @endphp

<div class="rp-card" data-rp-id="{{ $rp->id }}">
    {{-- Header --}}
    <div style="padding:14px 20px;border-bottom:1px solid #f1f5f9;display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:10px;">
        <div style="display:flex;align-items:center;gap:12px;">
            @php
                $name     = $rp->formateur?->name ?? 'Inconnu';
                $initials = strtoupper(substr($name,0,1)) . strtoupper(substr(explode(' ',$name.' ')[1]??'',0,1));
            @endphp
            <div style="width:38px;height:38px;border-radius:10px;background:#f1f5f9;border:1px solid #cbd5e1;display:flex;align-items:center;justify-content:center;font-size:12px;font-weight:800;color:#1e293b;flex-shrink:0;">
                {{ $initials }}
            </div>
            <div>
                <div style="font-size:13px;font-weight:700;color:#0f172a;">{{ $name }}</div>
                <div style="font-size:10px;color:#64748b;">{{ $rp->created_at->translatedFormat('l d M Y à H:i') }}</div>
            </div>
        </div>
        <div style="display:flex;align-items:center;gap:8px;flex-wrap:wrap;">
            @if($rp->status === 'en_attente')
                <span class="status-pill attente">⏳ En attente de décision</span>
            @elseif($rp->status === 'valide')
                <span class="status-pill valide">✓ Acceptée</span>
            @else
                <span class="status-pill refuse">✕ Refusée</span>
            @endif
            @if($rp->validePar)
                <span style="font-size:10px;color:#64748b;">par <strong>{{ $rp->validePar->name }}</strong></span>
            @endif
        </div>
    </div>

    {{-- Body --}}
    <div style="padding:16px 20px;display:grid;grid-template-columns:1fr 1fr;gap:16px;">

        {{-- Session info --}}
        <div>
            <div style="font-size:9px;font-weight:800;color:#94a3b8;text-transform:uppercase;letter-spacing:1.5px;margin-bottom:8px;">Séance concernée</div>
            @if($emploi)
            <div style="padding:12px 14px;border-radius:10px;background:#f8fafc;border:1px solid #e2e8f0;">
                <div style="font-size:12px;font-weight:700;color:#1e293b;margin-bottom:5px;">
                    {{ $emploi->module?->name ?? '— Module non défini' }}
                </div>
                <div style="font-size:10px;color:#475569;margin-bottom:3px;">👥 {{ $emploi->groupe?->name ?? '—' }} · {{ $emploi->groupe?->filiere?->name ?? '' }}</div>
                <div style="font-size:10px;color:#475569;margin-bottom:3px;">📅 {{ $emploi->date_debut->translatedFormat('l d M Y') }}</div>
                <div style="font-size:10px;color:#475569;">
                    🕐 {{ $emploi->date_debut->format('H:i') }} → {{ $emploi->date_fin->format('H:i') }}
                    @if($emploi->salle) · 🏛 {{ $emploi->salle->name }} @endif
                </div>
            </div>
            @else
                <div style="font-size:11px;color:#94a3b8;font-style:italic;padding:12px 14px;background:#f8fafc;border-radius:10px;border:1px solid #e2e8f0;">
                    Séance supprimée par l'administration.
                </div>
            @endif
        </div>

        {{-- Reason + result --}}
        <div>
            <div style="font-size:9px;font-weight:800;color:#94a3b8;text-transform:uppercase;letter-spacing:1.5px;margin-bottom:8px;">Raison du formateur</div>
            <div style="padding:12px 14px;border-radius:10px;background:#f8fafc;border:1px solid #e2e8f0;border-left:3px solid #7c3aed;font-size:11px;color:#334155;line-height:1.6;">
                {{ $rp->raison }}
            </div>

            @if($rp->status === 'valide' && $rp->nouvelle_date_debut)
            <div style="margin-top:10px;padding:12px 14px;border-radius:10px;background:#f0fdf4;border:1px solid #bbf7d0;">
                <div style="font-size:9px;font-weight:800;color:#15803d;text-transform:uppercase;letter-spacing:1px;margin-bottom:4px;">✓ Nouvelle date</div>
                <div style="font-size:13px;font-weight:700;color:#15803d;">
                    {{ \Carbon\Carbon::parse($rp->nouvelle_date_debut)->translatedFormat('l d M Y') }}
                </div>
                <div style="font-size:11px;color:#15803d;">
                    🕐 {{ \Carbon\Carbon::parse($rp->nouvelle_date_debut)->format('H:i') }}
                    → {{ \Carbon\Carbon::parse($rp->nouvelle_date_fin)->format('H:i') }}
                </div>
            </div>
            @elseif($rp->status === 'refuse')
            <div style="margin-top:10px;padding:12px 14px;border-radius:10px;background:#fff1f2;border:1px solid #fecdd3;font-size:11px;color:#dc2626;">
                ✕ Demande refusée. La séance reste à la date initiale.
            </div>
            @elseif($rp->status === 'en_attente')
            <div style="margin-top:10px;padding:12px 14px;border-radius:10px;background:#fff7ed;border:1px solid #fde68a;font-size:11px;color:#92400e;display:flex;align-items:center;gap:8px;">
                <svg width="13" height="13" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                En attente de décision par l'administrateur.
            </div>
            @endif
        </div>
    </div>

    {{-- Footer — Chat only --}}
    <div style="padding:10px 20px;border-top:1px solid #f1f5f9;background:#fafafa;display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:8px;">
        <button class="rp-btn ghost"
                onclick="openChat({{ $rp->id }}, '{{ addslashes($rp->formateur?->name ?? 'Conversation') }}')">
            💬 Chat
            <span id="chat-count-{{ $rp->id }}" style="background:#e2e8f0;border-radius:99px;padding:1px 7px;font-size:10px;">
                {{ $rp->messages?->count() ?? 0 }}
            </span>
        </button>

        @if($emploi)
        <a href="{{ route('emplois.index', ['week' => $emploi->date_debut->toDateString(), 'year' => $emploi->groupe->annee ?? 1]) }}"
           style="font-size:11px;color:#1e40af;text-decoration:none;font-weight:600;display:inline-flex;align-items:center;gap:5px;">
            <svg width="11" height="11" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
            Voir la semaine de cette séance
        </a>
        @endif
    </div>
</div>
@empty
<div style="padding:64px;text-align:center;background:white;border-radius:16px;border:1px solid #e2e8f0;">
    <div style="font-size:36px;margin-bottom:12px;">📋</div>
    <p style="font-size:14px;font-weight:700;color:#334155;margin:0 0 4px;">Aucune reportation assignée</p>
    <p style="font-size:12px;color:#94a3b8;margin:0;">Les demandes qui vous sont assignées par l'admin apparaîtront ici.</p>
</div>
@endforelse

@if($reportations->hasPages())
    <div style="margin-top:16px;display:flex;justify-content:center;">{{ $reportations->links() }}</div>
@endif

</div>

{{-- ════ CHAT MODAL ════ --}}
<div id="chat-modal" style="position:fixed;inset:0;z-index:70;background:rgba(15,23,42,0.5);backdrop-filter:blur(4px);display:none;align-items:center;justify-content:center;" onclick="if(event.target===this)closeChat()">
    <div style="background:white;border-radius:20px;width:100%;max-width:480px;margin:16px;display:flex;flex-direction:column;height:520px;box-shadow:0 24px 60px rgba(0,0,0,0.18);">
        <div style="padding:16px 20px;border-bottom:1px solid #e2e8f0;display:flex;align-items:center;justify-content:space-between;">
            <div style="font-size:14px;font-weight:800;color:#1e293b;" id="chat-title">💬 Conversation</div>
            <button onclick="closeChat()" style="border:none;background:#f1f5f9;border-radius:8px;width:28px;height:28px;cursor:pointer;font-size:16px;">×</button>
        </div>

        <div id="chat-messages" style="flex:1;overflow-y:auto;padding:16px;display:flex;flex-direction:column;gap:10px;"></div>

        {{-- File preview bar --}}
        <div id="file-preview-bar" style="display:none;padding:8px 16px;border-top:1px solid #e2e8f0;background:#f8fafc;align-items:center;gap:8px;">
            <span id="file-preview-name" style="font-size:11px;color:#475569;font-weight:600;flex:1;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;"></span>
            <button onclick="clearAttachment()" style="border:none;background:#fecdd3;color:#dc2626;border-radius:6px;width:22px;height:22px;cursor:pointer;font-size:14px;line-height:1;">×</button>
        </div>

        <div style="padding:12px 16px;border-top:1px solid #e2e8f0;display:flex;gap:8px;align-items:center;">
            {{-- Hidden file input --}}
            <input type="file" id="chat-file-input"
                   accept="image/*,.pdf,.doc,.docx,.xls,.xlsx,.txt,.zip"
                   style="display:none;" onchange="onFileSelected(this)">

            {{-- Attach button --}}
            <button onclick="document.getElementById('chat-file-input').click()"
                    title="Joindre un fichier"
                    style="width:40px;height:40px;border-radius:10px;border:1.5px solid #e2e8f0;background:#f8fafc;cursor:pointer;font-size:18px;display:flex;align-items:center;justify-content:center;flex-shrink:0;transition:border-color .15s;"
                    onmouseover="this.style.borderColor='#1e293b'" onmouseout="this.style.borderColor='#e2e8f0'">
                📎
            </button>

            <input id="chat-input" type="text" placeholder="Votre message…" maxlength="1000"
                   style="flex:1;height:40px;padding:0 12px;border-radius:10px;border:1.5px solid #e2e8f0;font-size:13px;outline:none;"
                   onkeydown="if(event.key==='Enter')sendChatMsg()">

            <button onclick="sendChatMsg()"
                    style="height:40px;padding:0 16px;border-radius:10px;border:none;background:#1e293b;color:white;font-weight:700;font-size:13px;cursor:pointer;flex-shrink:0;">
                Envoyer
            </button>
        </div>
    </div>
</div>

<script>
let currentReportationId = null;

function openChat(id, name) {
    currentReportationId = id;
    document.getElementById('chat-title').textContent = '💬 ' + name;
    document.getElementById('chat-messages').innerHTML =
        '<div style="text-align:center;font-size:12px;color:#94a3b8;">Chargement…</div>';
    document.getElementById('chat-modal').style.display = 'flex';

    fetch(`/reportations/${id}/messages`)
        .then(r => r.json())
        .then(msgs => {
            const box = document.getElementById('chat-messages');
            box.innerHTML = msgs.length === 0
                ? '<div style="text-align:center;font-size:12px;color:#94a3b8;">Aucun message pour l\'instant.</div>'
                : '';
            msgs.forEach(appendMsg);
            box.scrollTop = box.scrollHeight;
        });
}

function closeChat() {
    document.getElementById('chat-modal').style.display = 'none';
    currentReportationId = null;
    clearAttachment(); // Reset attachment when closing
}

function onFileSelected(input) {
    const file = input.files[0];
    if (!file) return;
    document.getElementById('file-preview-name').textContent = '📎 ' + file.name;
    document.getElementById('file-preview-bar').style.display = 'flex';
}

function clearAttachment() {
    document.getElementById('chat-file-input').value = '';
    document.getElementById('file-preview-bar').style.display = 'none';
    document.getElementById('file-preview-name').textContent = '';
}

function appendMsg(msg) {
    const me = {{ auth()->id() }};
    const isMe = msg.user_id == me;
    const box = document.getElementById('chat-messages');
    const div = document.createElement('div');
    div.style.cssText = `display:flex;flex-direction:column;align-items:${isMe ? 'flex-end' : 'flex-start'};gap:2px;`;

    let attachmentHtml = '';
    if (msg.attachment_url) {
        if (msg.attachment_type === 'image') {
            attachmentHtml = `
                <a href="${msg.attachment_url}" target="_blank" style="display:block;max-width:200px;margin-top:4px;">
                    <img src="${msg.attachment_url}" alt="${escapeHtml(msg.attachment_name)}"
                         style="max-width:200px;max-height:160px;border-radius:8px;border:1px solid #e2e8f0;display:block;">
                </a>`;
        } else {
            attachmentHtml = `
                <a href="${msg.attachment_url}" target="_blank"
                   style="display:inline-flex;align-items:center;gap:6px;margin-top:4px;padding:7px 12px;border-radius:8px;background:${isMe ? 'rgba(255,255,255,0.15)' : '#e2e8f0'};color:${isMe ? 'white' : '#1e293b'};font-size:11px;font-weight:600;text-decoration:none;max-width:200px;overflow:hidden;">
                    📄 <span style="overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">${escapeHtml(msg.attachment_name)}</span>
                </a>`;
        }
    }

    const msgHtml = msg.message
        ? `<div style="max-width:75%;padding:8px 12px;border-radius:${isMe ? '12px 12px 2px 12px' : '12px 12px 12px 2px'};background:${isMe ? '#1e293b' : '#f1f5f9'};color:${isMe ? 'white' : '#1e293b'};font-size:12px;line-height:1.5;">${escapeHtml(msg.message)}</div>`
        : '';

    div.innerHTML = `
        <div style="font-size:9px;color:#94a3b8;">${escapeHtml(msg.user_name)} · ${escapeHtml(msg.created_at)}</div>
        ${msgHtml}
        ${attachmentHtml}`;
    box.appendChild(div);
}
function sendChatMsg() {
    const input     = document.getElementById('chat-input');
    const fileInput = document.getElementById('chat-file-input');
    const msg       = input.value.trim();
    const file      = fileInput.files[0] ?? null;

    if (!msg && !file) return;
    if (!currentReportationId) return;

    const rpId = currentReportationId;
    input.value = '';

    const formData = new FormData();
    if (msg)  formData.append('message', msg);
    if (file) formData.append('attachment', file);
    formData.append('_token', '{{ csrf_token() }}');

    const socketId = window.Echo?.socketId() ?? null;
    const headers  = {
        'Accept': 'application/json',   // ← FIX : force Laravel à répondre en JSON
    };
    if (socketId) headers['X-Socket-ID'] = socketId;

    clearAttachment();

    fetch(`/reportations/${rpId}/message`, {
        method: 'POST',
        headers,
        body: formData
    })
    .then(r => {
        if (!r.ok) {                    // ← FIX : vérifier le statut HTTP
            return r.json().then(err => {
                const msg = err?.message || err?.error
                    || Object.values(err?.errors ?? {})[0]?.[0]
                    || 'Erreur lors de l\'envoi.';
                showSendError(msg);
                throw new Error(msg);
            });
        }
        return r.json();
    })
    .then(data => {
        if (currentReportationId === rpId) {
            const box   = document.getElementById('chat-messages');
            const empty = box.querySelector('div[style*="text-align:center"]');
            if (empty) empty.remove();
            appendMsg(data);
            box.scrollTop = 99999;
        }
        const badge = document.getElementById('chat-count-' + rpId);
        if (badge) badge.textContent = parseInt(badge.textContent || '0') + 1;
    })
    .catch(err => console.error('Erreur envoi:', err));
}

// Ajouter cette fonction helper dans les 3 blades
function showSendError(message) {
    const box = document.getElementById('chat-messages');
    const div = document.createElement('div');
    div.style.cssText = 'text-align:center;padding:6px 12px;font-size:11px;color:#dc2626;background:#fff1f2;border-radius:8px;border:1px solid #fecdd3;';
    div.textContent = '⚠ ' + message;
    box.appendChild(div);
    box.scrollTop = 99999;
    setTimeout(() => div.remove(), 5000); // disparaît après 5s
}
function injectAssignedCard(e) {
    // Don't duplicate if card already exists
    if (document.querySelector(`[data-rp-id="${e.id}"]`)) return;

    const initials = (e.formateur || 'IN').split(' ').slice(0,2).map(w => w[0]?.toUpperCase() || '').join('');

    const card = document.createElement('div');
    card.className = 'rp-card';
    card.setAttribute('data-rp-id', e.id);
    card.style.animation = 'slideIn .3s ease';
    card.innerHTML = `
        <div style="padding:14px 20px;border-bottom:1px solid #f1f5f9;display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:10px;">
            <div style="display:flex;align-items:center;gap:12px;">
                <div style="width:38px;height:38px;border-radius:10px;background:#f1f5f9;border:1px solid #cbd5e1;display:flex;align-items:center;justify-content:center;font-size:12px;font-weight:800;color:#1e293b;flex-shrink:0;">
                    ${escapeHtml(initials)}
                </div>
                <div>
                    <div style="font-size:13px;font-weight:700;color:#0f172a;">${escapeHtml(e.formateur)}</div>
                    <div style="font-size:10px;color:#64748b;">${escapeHtml(e.created_at)}</div>
                </div>
            </div>
            <div style="display:flex;align-items:center;gap:8px;">
                <span class="status-pill attente">⏳ En attente de décision</span>
                <span style="font-size:10px;background:#dbeafe;color:#1e40af;padding:3px 10px;border-radius:99px;font-weight:700;">🔴 Nouveau</span>
            </div>
        </div>
        <div style="padding:16px 20px;display:grid;grid-template-columns:1fr 1fr;gap:16px;">
            <div>
                <div style="font-size:9px;font-weight:800;color:#94a3b8;text-transform:uppercase;letter-spacing:1.5px;margin-bottom:8px;">Séance concernée</div>
                <div style="padding:12px 14px;border-radius:10px;background:#f8fafc;border:1px solid #e2e8f0;">
                    <div style="font-size:12px;font-weight:700;color:#1e293b;margin-bottom:5px;">${escapeHtml(e.module)}</div>
                    <div style="font-size:10px;color:#475569;margin-bottom:3px;">👥 ${escapeHtml(e.groupe)} · ${escapeHtml(e.filiere)}</div>
                    <div style="font-size:10px;color:#475569;margin-bottom:3px;">📅 ${escapeHtml(e.date_debut)}</div>
                    <div style="font-size:10px;color:#475569;">🕐 ${escapeHtml(e.heure_debut)} → ${escapeHtml(e.heure_fin)}${e.salle ? ' · 🏛 ' + escapeHtml(e.salle) : ''}</div>
                </div>
            </div>
            <div>
                <div style="font-size:9px;font-weight:800;color:#94a3b8;text-transform:uppercase;letter-spacing:1.5px;margin-bottom:8px;">Raison du formateur</div>
                <div style="padding:12px 14px;border-radius:10px;background:#f8fafc;border:1px solid #e2e8f0;border-left:3px solid #7c3aed;font-size:11px;color:#334155;line-height:1.6;">${escapeHtml(e.raison)}</div>
                <div style="margin-top:10px;padding:12px 14px;border-radius:10px;background:#fff7ed;border:1px solid #fde68a;font-size:11px;color:#92400e;display:flex;align-items:center;gap:8px;">
                    <svg width="13" height="13" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    En attente de décision par l'administrateur.
                </div>
            </div>
        </div>
        <div style="padding:10px 20px;border-top:1px solid #f1f5f9;background:#fafafa;display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:8px;">
            <button class="rp-btn ghost" onclick="openChat(${e.id}, '${escapeHtml(e.formateur)}')">
                💬 Chat
                <span id="chat-count-${e.id}" style="background:#e2e8f0;border-radius:99px;padding:1px 7px;font-size:10px;">0</span>
            </button>
        </div>`;

    // Subscribe to this new card's chat channel immediately
    if (window.Echo) {
        window.Echo.channel('reportation.' + e.id)
            .listen('.message.sent', function(msg) {
                if (currentReportationId === e.id) {
                    const box = document.getElementById('chat-messages');
                    const empty = box.querySelector('div[style*="text-align:center"]');
                    if (empty) empty.remove();
                    appendMsg(msg);
                    box.scrollTop = 99999;
                }
                const badge = document.getElementById('chat-count-' + e.id);
                if (badge) badge.textContent = parseInt(badge.textContent || '0') + 1;
            });
    }

    // Remove empty state if present
    const empty = document.querySelector('.rp-wrap > div[style*="text-align:center"]');
    if (empty) empty.remove();

    // Prepend to top
    const wrap = document.querySelector('.rp-wrap');
    const firstCard = wrap.querySelector('.rp-card');
    firstCard ? wrap.insertBefore(card, firstCard) : wrap.appendChild(card);
}

function showToast(msg) {
    const t = document.createElement('div');
    t.style.cssText = 'position:fixed;bottom:24px;right:24px;z-index:99;padding:14px 20px;background:#1e293b;color:white;border-radius:14px;font-size:13px;font-weight:600;box-shadow:0 8px 30px rgba(0,0,0,0.2);transition:opacity .3s;';
    t.textContent = msg;
    document.body.appendChild(t);
    setTimeout(() => { t.style.opacity = '0'; setTimeout(() => t.remove(), 300); }, 4000);
}

function escapeHtml(text) {
    if (text === null || text === undefined) return '';
    const div = document.createElement('div');
    div.textContent = String(text);
    return div.innerHTML;
}

function subscribeAll() {
    if (!window.Echo) { setTimeout(subscribeAll, 300); return; }

    // Listen for new assignments in real-time
    window.Echo.channel('gestionnaire.{{ auth()->id() }}')
        .listen('.reportation.assigned', function(e) {
            injectAssignedCard(e);
            showToast('📋 Nouvelle reportation assignée de ' + e.formateur);
        });

    // Existing message subscriptions for already-loaded cards
    @foreach($reportations as $rp)
    window.Echo.channel('reportation.{{ $rp->id }}')
        .listen('.message.sent', function(e) {
            const rpId = {{ $rp->id }};
            if (currentReportationId === rpId) {
                const box = document.getElementById('chat-messages');
                const empty = box.querySelector('div[style*="text-align:center"]');
                if (empty) empty.remove();
                appendMsg(e);
                box.scrollTop = 99999;
            }
            const badge = document.getElementById('chat-count-' + rpId);
            if (badge) badge.textContent = parseInt(badge.textContent || '0') + 1;
        });
    @endforeach
}

// Add animation keyframes
const style = document.createElement('style');
style.textContent = `
    @keyframes slideIn {
        from {
            opacity: 0;
            transform: translateY(-20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
`;
document.head.appendChild(style);

subscribeAll();
</script>

@endsection