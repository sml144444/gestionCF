

<?php $__env->startSection('title', 'Mes reportations'); ?>
<?php $__env->startSection('page-title', 'Mes reportations'); ?>

<?php $__env->startSection('content'); ?>
<style>
.rp-wrap { font-family:'Segoe UI',system-ui,sans-serif; }
.rp-card { background:white; border-radius:16px; border:1px solid #e2e8f0; box-shadow:0 2px 10px rgba(0,0,0,0.05); overflow:hidden; margin-bottom:14px; }
.status-pill { display:inline-flex; align-items:center; gap:4px; padding:4px 12px; border-radius:99px; font-size:10px; font-weight:700; }
.status-pill.attente { background:#fff7ed; color:#92400e; border:1px solid #fde68a; }
.status-pill.valide  { background:#f0fdf4; color:#15803d; border:1px solid #bbf7d0; }
.status-pill.refuse  { background:#fff1f2; color:#dc2626; border:1px solid #fecdd3; }
.tab-pill { padding:7px 14px; border-radius:99px; font-size:12px; font-weight:600; text-decoration:none; border:1.5px solid #e2e8f0; background:white; color:#64748b; transition:all .15s; display:inline-flex; align-items:center; gap:6px; }
.tab-pill:hover { border-color:#1a4f8a; color:#1e40af; background:#eff6ff; }
.tab-pill.active { background:#1a4f8a; border-color:#1a4f8a; color:white; }
.tab-pill .badge { font-size:9px; padding:1px 7px; border-radius:99px; font-weight:800; }
.tab-pill.active .badge { background:rgba(255,255,255,0.25); color:white; }
.tab-pill:not(.active) .badge { background:#eff6ff; color:#1e40af; }
.rp-btn { height:36px; padding:0 14px; border-radius:9px; font-size:12px; font-weight:700; border:none; cursor:pointer; display:inline-flex; align-items:center; gap:5px; transition:opacity .15s; text-decoration:none; }
.rp-btn:hover { opacity:.85; }
.rp-btn.ghost { background:#f1f5f9; color:#475569; border:1px solid #e2e8f0; }
@keyframes slideIn { from { opacity:0; transform:translateY(-12px); } to { opacity:1; transform:translateY(0); } }
</style>

<div class="rp-wrap">


<?php if(session('success')): ?>
    <div style="margin-bottom:16px;padding:12px 16px;border-radius:12px;font-size:13px;display:flex;align-items:center;gap:8px;background:#f0fdf4;border:1px solid #bbf7d0;color:#15803d;">
        ✓ <?php echo e(session('success')); ?>

    </div>
<?php endif; ?>
<?php if(session('error')): ?>
    <div style="margin-bottom:16px;padding:12px 16px;border-radius:12px;font-size:13px;background:#fff1f2;border:1px solid #fecdd3;color:#dc2626;">
        ✕ <?php echo e(session('error')); ?>

    </div>
<?php endif; ?>


<div style="display:flex;align-items:flex-start;justify-content:space-between;flex-wrap:wrap;gap:12px;margin-bottom:20px;">
    <div>
        <h1 style="font-size:20px;font-weight:800;color:#0f172a;margin:0;">Mes reportations</h1>
        <p style="font-size:12px;color:#64748b;margin:4px 0 0;">Suivi de vos demandes de report de séances</p>
    </div>
    <div style="display:flex;gap:10px;flex-wrap:wrap;">
        <div style="padding:10px 16px;border-radius:12px;text-align:center;background:#fff7ed;border:1px solid #fde68a;">
            <div style="font-size:22px;font-weight:800;color:#92400e;"><?php echo e($counts['en_attente']); ?></div>
            <div style="font-size:9px;font-weight:700;color:#92400e;text-transform:uppercase;letter-spacing:.5px;">En attente</div>
        </div>
        <div style="padding:10px 16px;border-radius:12px;text-align:center;background:#f0fdf4;border:1px solid #bbf7d0;">
            <div style="font-size:22px;font-weight:800;color:#15803d;"><?php echo e($counts['valide']); ?></div>
            <div style="font-size:9px;font-weight:700;color:#15803d;text-transform:uppercase;letter-spacing:.5px;">Acceptées</div>
        </div>
        <div style="padding:10px 16px;border-radius:12px;text-align:center;background:#fff1f2;border:1px solid #fecdd3;">
            <div style="font-size:22px;font-weight:800;color:#dc2626;"><?php echo e($counts['refuse']); ?></div>
            <div style="font-size:9px;font-weight:700;color:#dc2626;text-transform:uppercase;letter-spacing:.5px;">Refusées</div>
        </div>
    </div>
</div>


<div style="display:flex;gap:8px;flex-wrap:wrap;margin-bottom:18px;">
    <?php $__currentLoopData = [['','📋','Toutes'],['en_attente','⏳','En attente'],['valide','✓','Acceptées'],['refuse','✕','Refusées']]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as [$val,$icon,$label]): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
    <a href="<?php echo e(route('reportations.my', ['status' => $val])); ?>"
       class="tab-pill <?php echo e($status === $val ? 'active' : ''); ?>">
        <?php echo e($icon); ?> <?php echo e($label); ?>

        <span class="badge"><?php echo e($val === '' ? array_sum($counts) : ($counts[$val] ?? 0)); ?></span>
    </a>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
</div>


<?php $__empty_1 = true; $__currentLoopData = $reportations; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $rp): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
<?php $emploi = $rp->emploiDuTemps; ?>

<div class="rp-card" data-rp-id="<?php echo e($rp->id); ?>">
    
    <div style="padding:14px 20px;border-bottom:1px solid #f1f5f9;display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:8px;">
        <div style="font-size:11px;color:#64748b;">
            Demande envoyée le <strong style="color:#334155;"><?php echo e($rp->created_at->translatedFormat('d M Y à H:i')); ?></strong>
        </div>
        <div style="display:flex;align-items:center;gap:8px;">
            <?php if($rp->status === 'en_attente'): ?>
                <span class="status-pill attente">⏳ En attente de décision</span>
            <?php elseif($rp->status === 'valide'): ?>
                <span class="status-pill valide">✓ Acceptée</span>
            <?php else: ?>
                <span class="status-pill refuse">✕ Refusée</span>
            <?php endif; ?>
            <?php if($rp->validePar): ?>
                <span style="font-size:10px;color:#64748b;">par <strong><?php echo e($rp->validePar->name); ?></strong></span>
            <?php endif; ?>
        </div>
    </div>

    <div style="padding:16px 20px;display:grid;grid-template-columns:1fr 1fr;gap:16px;">

        
        <div>
            <div style="font-size:9px;font-weight:800;color:#94a3b8;text-transform:uppercase;letter-spacing:1.5px;margin-bottom:8px;">Séance concernée</div>
            <?php if($emploi): ?>
            <div style="padding:12px 14px;border-radius:10px;background:#f8fafc;border:1px solid #e2e8f0;">
                <div style="font-size:12px;font-weight:700;color:#1e293b;margin-bottom:5px;"><?php echo e($emploi->module?->name ?? '— Module non défini'); ?></div>
                <div style="font-size:10px;color:#475569;margin-bottom:3px;">👥 <?php echo e($emploi->groupe?->name ?? '—'); ?> · <?php echo e($emploi->groupe?->filiere?->name ?? ''); ?></div>
                <div style="font-size:10px;color:#475569;margin-bottom:3px;">📅 <?php echo e($emploi->date_debut->translatedFormat('l d M Y')); ?></div>
                <div style="font-size:10px;color:#475569;">🕐 <?php echo e($emploi->date_debut->format('H:i')); ?> → <?php echo e($emploi->date_fin->format('H:i')); ?></div>
            </div>
            <?php else: ?>
                <div style="font-size:11px;color:#94a3b8;font-style:italic;padding:12px 14px;background:#f8fafc;border-radius:10px;border:1px solid #e2e8f0;">
                    Séance supprimée par l'administration.
                </div>
            <?php endif; ?>
        </div>

        
        <div>
            <div style="font-size:9px;font-weight:800;color:#94a3b8;text-transform:uppercase;letter-spacing:1.5px;margin-bottom:8px;">Votre raison</div>
            <div style="padding:12px 14px;border-radius:10px;background:#f8fafc;border:1px solid #e2e8f0;border-left:3px solid #7c3aed;font-size:11px;color:#334155;line-height:1.6;">
                <?php echo e($rp->raison); ?>

            </div>

            <?php if($rp->status === 'valide' && $rp->nouvelle_date_debut): ?>
            <div style="margin-top:10px;padding:12px 14px;border-radius:10px;background:#f0fdf4;border:1px solid #bbf7d0;">
                <div style="font-size:9px;font-weight:800;color:#15803d;text-transform:uppercase;letter-spacing:1px;margin-bottom:4px;">✓ Nouvelle date fixée par l'admin</div>
                <div style="font-size:13px;font-weight:700;color:#15803d;">
                    <?php echo e(\Carbon\Carbon::parse($rp->nouvelle_date_debut)->translatedFormat('l d M Y')); ?>

                </div>
                <div style="font-size:11px;color:#15803d;">
                    🕐 <?php echo e(\Carbon\Carbon::parse($rp->nouvelle_date_debut)->format('H:i')); ?>

                    → <?php echo e(\Carbon\Carbon::parse($rp->nouvelle_date_fin)->format('H:i')); ?>

                </div>
            </div>
            <?php elseif($rp->status === 'refuse'): ?>
            <div style="margin-top:10px;padding:12px 14px;border-radius:10px;background:#fff1f2;border:1px solid #fecdd3;font-size:11px;color:#dc2626;">
                ✕ Votre demande a été refusée. La séance reste à la date initiale.
            </div>
            <?php elseif($rp->status === 'en_attente'): ?>
            <div style="margin-top:10px;padding:12px 14px;border-radius:10px;background:#fff7ed;border:1px solid #fde68a;font-size:11px;color:#92400e;display:flex;align-items:center;gap:8px;">
                <svg width="13" height="13" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                En attente de décision — l'admin choisira la nouvelle date si accepté.
            </div>
            <?php endif; ?>
        </div>
    </div>

    
    <div style="padding:10px 20px;border-top:1px solid #f1f5f9;background:#fafafa;display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:8px;">
        
        <button class="rp-btn ghost"
                onclick="openChat(<?php echo e($rp->id); ?>, '<?php echo e(addslashes($rp->emploiDuTemps?->module?->name ?? 'Support')); ?>')">
            💬 Chat
            <span id="chat-count-<?php echo e($rp->id); ?>" style="background:#e2e8f0;border-radius:99px;padding:1px 7px;font-size:10px;">
                <?php echo e($rp->messages?->count() ?? 0); ?>

            </span>
        </button>

        <?php if($emploi): ?>
        <a href="<?php echo e(route('emplois.index', ['week' => $emploi->date_debut->toDateString(), 'year' => $emploi->groupe->annee ?? 1])); ?>"
           style="font-size:11px;color:#1e40af;text-decoration:none;font-weight:600;display:inline-flex;align-items:center;gap:5px;">
            <svg width="11" height="11" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
            Voir la semaine de cette séance
        </a>
        <?php endif; ?>
    </div>
</div>
<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
<div style="padding:64px;text-align:center;background:white;border-radius:16px;border:1px solid #e2e8f0;">
    <div style="font-size:36px;margin-bottom:12px;">📋</div>
    <p style="font-size:14px;font-weight:700;color:#334155;margin:0 0 4px;">Aucune demande</p>
    <p style="font-size:12px;color:#94a3b8;margin:0;">Vos demandes de report apparaîtront ici. Utilisez le bouton <strong>📋 Reporter</strong> sur une séance.</p>
</div>
<?php endif; ?>

<?php if($reportations->hasPages()): ?>
    <div style="margin-top:16px;display:flex;justify-content:center;"><?php echo e($reportations->links()); ?></div>
<?php endif; ?>

</div>


<div id="chat-modal" style="position:fixed;inset:0;z-index:70;background:rgba(15,23,42,0.5);backdrop-filter:blur(4px);display:none;align-items:center;justify-content:center;" onclick="if(event.target===this)closeChat()">
    <div style="background:white;border-radius:20px;width:100%;max-width:480px;margin:16px;display:flex;flex-direction:column;height:520px;box-shadow:0 24px 60px rgba(0,0,0,0.18);">
        <div style="padding:16px 20px;border-bottom:1px solid #e2e8f0;display:flex;align-items:center;justify-content:space-between;">
            <div style="font-size:14px;font-weight:800;color:#1e293b;" id="chat-title">💬 Conversation</div>
            <button onclick="closeChat()" style="border:none;background:#f1f5f9;border-radius:8px;width:28px;height:28px;cursor:pointer;font-size:16px;">×</button>
        </div>

        <div id="chat-messages" style="flex:1;overflow-y:auto;padding:16px;display:flex;flex-direction:column;gap:10px;"></div>

        
        <div id="file-preview-bar" style="display:none;padding:8px 16px;border-top:1px solid #e2e8f0;background:#f8fafc;align-items:center;gap:8px;">
            <span id="file-preview-name" style="font-size:11px;color:#475569;font-weight:600;flex:1;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;"></span>
            <button onclick="clearAttachment()" style="border:none;background:#fecdd3;color:#dc2626;border-radius:6px;width:22px;height:22px;cursor:pointer;font-size:14px;line-height:1;">×</button>
        </div>

        <div style="padding:12px 16px;border-top:1px solid #e2e8f0;display:flex;gap:8px;align-items:center;">
            
            <input type="file" id="chat-file-input"
                   accept="image/*,.pdf,.doc,.docx,.xls,.xlsx,.txt,.zip"
                   style="display:none;" onchange="onFileSelected(this)">

            
            <button onclick="document.getElementById('chat-file-input').click()"
                    title="Joindre un fichier"
                    style="width:40px;height:40px;border-radius:10px;border:1.5px solid #e2e8f0;background:#f8fafc;cursor:pointer;font-size:18px;display:flex;align-items:center;justify-content:center;flex-shrink:0;transition:border-color .15s;"
                    onmouseover="this.style.borderColor='#1a4f8a'" onmouseout="this.style.borderColor='#e2e8f0'">
                📎
            </button>

            <input id="chat-input" type="text" placeholder="Votre message…" maxlength="1000"
                   style="flex:1;height:40px;padding:0 12px;border-radius:10px;border:1.5px solid #e2e8f0;font-size:13px;outline:none;"
                   onkeydown="if(event.key==='Enter')sendChatMsg()">

            <button onclick="sendChatMsg()"
                    style="height:40px;padding:0 16px;border-radius:10px;border:none;background:#1a4f8a;color:white;font-weight:700;font-size:13px;cursor:pointer;flex-shrink:0;">
                Envoyer
            </button>
        </div>
    </div>
</div>

<script>
let currentReportationId = null;

function openChat(id, label) {
    currentReportationId = id;
    document.getElementById('chat-title').textContent = '💬 ' + label;
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
    const me = <?php echo e(auth()->id()); ?>;
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
        ? `<div style="max-width:75%;padding:8px 12px;border-radius:${isMe ? '12px 12px 2px 12px' : '12px 12px 12px 2px'};background:${isMe ? '#1a4f8a' : '#f1f5f9'};color:${isMe ? 'white' : '#1e293b'};font-size:12px;line-height:1.5;">${escapeHtml(msg.message)}</div>`
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
    formData.append('_token', '<?php echo e(csrf_token()); ?>');

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
// ══════════════════════════════════════════════
// GLOBAL ECHO — wait for Echo to be ready then subscribe
// ══════════════════════════════════════════════
function subscribeAll() {
    if (!window.Echo) {
        setTimeout(subscribeAll, 300);
        return;
    }
    <?php $__currentLoopData = $reportations; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $rp): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
    window.Echo.channel('reportation.<?php echo e($rp->id); ?>')
        .listen('.message.sent', function(e) {
            const rpId = <?php echo e($rp->id); ?>;
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
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
}
subscribeAll();

function escapeHtml(text) {
    if (text === null || text === undefined) return '';
    const div = document.createElement('div');
    div.textContent = String(text);
    return div.innerHTML;
}
</script>

<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\Project\gestion-CF\resources\views/reportations/my.blade.php ENDPATH**/ ?>