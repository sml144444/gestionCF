

<?php $__env->startSection('title', 'Réclamation #' . $reclamation->id); ?>
<?php $__env->startSection('page-title', 'Conversation'); ?>

<?php $__env->startPush('scripts'); ?>
<script src="https://js.pusher.com/8.4/pusher.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/laravel-echo@1.16.1/dist/echo.iife.js"></script>
<script>
window.Pusher = Pusher;
window.Echo = new Echo({
    broadcaster: 'reverb',
    key: '<?php echo e(env("REVERB_APP_KEY")); ?>',
    wsHost: '<?php echo e(env("REVERB_HOST", "localhost")); ?>',
    wsPort: <?php echo e(env("REVERB_PORT", 8080)); ?>,
    wssPort: <?php echo e(env("REVERB_PORT", 8080)); ?>,
    forceTLS: false,
    enabledTransports: ['ws', 'wss'],
});
</script>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
<?php
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
?>

<style>
* { box-sizing: border-box; margin: 0; padding: 0; }

/* ── Full-viewport shell ── */
.conv-shell {
    font-family: 'Segoe UI', system-ui, sans-serif;
    display: flex;
    flex-direction: column;
    height: calc(100vh - 64px);
    max-width: 860px;
    margin: 0 auto;
    gap: 12px;
    padding-bottom: 12px;
}

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
    background: <?php echo e($gradient); ?>;
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

/* ── Chat card ── */
.chat-card {
    flex: 1;
    min-height: 0;
    background: white;
    border-radius: 20px;
    border: 1px solid #e2e8f0;
    display: flex;
    flex-direction: column;
    overflow: hidden;
}

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
    min-height: 0;
    overflow-y: auto;
    padding: 20px;
    display: flex;
    flex-direction: column;
    gap: 14px;
    scroll-behavior: smooth;
}
.messages-area::-webkit-scrollbar { width: 5px; }
.messages-area::-webkit-scrollbar-track { background: transparent; }
.messages-area::-webkit-scrollbar-thumb { background: #e2e8f0; border-radius: 10px; }
.messages-area::-webkit-scrollbar-thumb:hover { background: #cbd5e1; }

/* ── Bubbles ── */
.msg-bubble {
    max-width: 72%;
    display: flex;
    flex-direction: column;
    gap: 4px;
    position: relative;
}
.msg-bubble.mine   { align-self: flex-end;   align-items: flex-end; }
.msg-bubble.theirs { align-self: flex-start; align-items: flex-start; }

.msg-content {
    padding: 11px 15px;
    border-radius: 18px;
    font-size: 13px;
    line-height: 1.6;
    word-break: break-word;
}
.msg-bubble.mine   .msg-content { background: <?php echo e($gradient); ?>; color: white; border-bottom-right-radius: 4px; }
.msg-bubble.theirs .msg-content { background: #f1f5f9; color: #1e293b; border-bottom-left-radius: 4px; }

.msg-meta   { font-size: 10px; color: #94a3b8; padding: 0 4px; }
.msg-sender { font-size: 10px; font-weight: 700; color: #64748b; padding: 0 4px; }

/* ── Edit / Delete actions ── */
.msg-actions {
    display: none;
    position: absolute;
    bottom: calc(100% + 4px);
    gap: 4px;
    z-index: 10;
}
.msg-bubble.mine   .msg-actions { right: 0; }
.msg-bubble.theirs .msg-actions { left: 0; }
.msg-bubble:hover  .msg-actions { display: flex; }

.msg-action-btn {
    border: none;
    border-radius: 8px;
    padding: 4px 9px;
    font-size: 10px;
    font-weight: 700;
    cursor: pointer;
    transition: opacity .15s;
    white-space: nowrap;
    box-shadow: 0 2px 8px rgba(0,0,0,0.10);
}
.btn-edit-msg   { background: #e0f2fe; color: #0369a1; }
.btn-delete-msg { background: #fee2e2; color: #be123c; }
.msg-action-btn:hover { opacity: .8; }

/* ── Seen / Edited indicators ── */
.msg-edited { font-size: 9px; color: rgba(255,255,255,0.55); margin-top: 1px; }
.msg-bubble.theirs .msg-edited { color: #94a3b8; }
.msg-seen { font-size: 9px; color: rgba(255,255,255,0.60); display: flex; align-items: center; gap: 3px; }

/* ── Inline edit box ── */
.edit-box { display: flex; flex-direction: column; gap: 6px; min-width: 200px; }
.edit-textarea {
    border: 1.5px solid #bfdbfe;
    border-radius: 10px;
    padding: 8px 10px;
    font-size: 12px;
    font-family: inherit;
    resize: none;
    outline: none;
    min-height: 60px;
    background: white;
    color: #1e293b;
}
.edit-actions { display: flex; gap: 6px; justify-content: flex-end; }
.btn-save-edit   { background: <?php echo e($gradient); ?>; color: white; border: none; border-radius: 8px; padding: 5px 12px; font-size: 11px; font-weight: 700; cursor: pointer; }
.btn-cancel-edit { background: #f1f5f9; color: #64748b; border: none; border-radius: 8px; padding: 5px 12px; font-size: 11px; font-weight: 700; cursor: pointer; }

/* ── Date divider ── */
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

/* ── Reply area ── */
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
    border-color: <?php echo e($accent); ?>;
    box-shadow: 0 0 0 3px <?php echo e($ring); ?>;
}
.btn-send {
    background: <?php echo e($gradient); ?>;
    color: white;
    border: none;
    border-radius: 12px;
    width: 44px; height: 44px;
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

/* ── Traite / closed notice ── */
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
.rt-dot { width: 6px; height: 6px; border-radius: 50%; background: #16a34a; animation: rtpulse 2s infinite; }
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
.f-select:focus { border-color: <?php echo e($accent); ?>; box-shadow: 0 0 0 3px <?php echo e($ring); ?>; }
.btn-action {
    font-size: 12px; font-weight: 700;
    padding: 8px 18px; border-radius: 10px;
    background: <?php echo e($gradient); ?>;
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

    
    <?php if(session('success')): ?>
    <div class="flash-notice" style="background:<?php echo e($succBg); ?>;border:1px solid <?php echo e($succBd); ?>;color:<?php echo e($succTx); ?>;">
        ✓ <?php echo e(session('success')); ?>

    </div>
    <?php endif; ?>

    
    <div class="hero">
        <div style="display:flex;align-items:center;gap:14px;">
            <a href="<?php echo e(route('reclamations.index')); ?>"
               style="width:36px;height:36px;border-radius:12px;background:rgba(255,255,255,0.15);
                      display:flex;align-items:center;justify-content:center;
                      text-decoration:none;color:white;font-size:16px;flex-shrink:0;">←</a>
            <div>
                <div style="font-size:11px;color:rgba(255,255,255,.65);font-weight:600;">
                    <?php echo e($tc['icon']); ?> <?php echo e($tc['label']); ?> · #<?php echo e($reclamation->id); ?>

                </div>
                <div style="font-size:15px;font-weight:800;color:white;margin-top:1px;">Conversation</div>
            </div>
        </div>
        <div style="display:flex;align-items:center;gap:10px;flex-wrap:wrap;">
            <span id="status-badge" class="badge"
                  style="background:<?php echo e($sc['bg']); ?>;color:<?php echo e($sc['color']); ?>;border:1px solid <?php echo e($sc['border']); ?>;">
                <?php echo e($sc['icon']); ?> <?php echo e($sc['label']); ?>

            </span>
            <span class="rt-badge"><span class="rt-dot"></span> Temps réel</span>
        </div>
    </div>

    
    <div class="chat-card">

        
        <div class="chat-header">
            <div>
                <?php if($isStagiaire): ?>
                    <div style="font-size:12px;font-weight:700;color:#1e293b;">Votre réclamation</div>
                    <div style="font-size:11px;color:#94a3b8;margin-top:1px;">
                        Ouverte le <?php echo e($reclamation->created_at->format('d/m/Y à H:i')); ?>

                    </div>
                <?php else: ?>
                    <div style="display:flex;align-items:center;gap:8px;">
                        <div style="width:34px;height:34px;border-radius:10px;
                                    background:<?php echo e($avatarBg); ?>;flex-shrink:0;
                                    display:flex;align-items:center;justify-content:center;
                                    font-size:11px;font-weight:800;color:<?php echo e($avatarTx); ?>;">
                            <?php echo e(strtoupper(mb_substr($reclamation->stagiaire?->name ?? '?', 0, 2))); ?>

                        </div>
                        <div>
                            <div style="font-size:12px;font-weight:700;color:#1e293b;"><?php echo e($reclamation->stagiaire?->name); ?></div>
                            <div style="font-size:10px;color:#94a3b8;"><?php echo e($reclamation->stagiaire?->email); ?></div>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
            <div style="font-size:11px;color:#94a3b8;background:#f8fafc;border:1px solid #e2e8f0;
                        border-radius:8px;padding:4px 10px;">
                <?php echo e($reclamation->messages->count()); ?> msg
            </div>
        </div>

        
        <div class="messages-area" id="messages-area">

            <div class="date-divider"><?php echo e($reclamation->created_at->format('d/m/Y')); ?></div>

            
            <div class="msg-bubble <?php echo e($isStagiaire ? 'mine' : 'theirs'); ?>">
                <?php if (! ($isStagiaire)): ?>
                    <div class="msg-sender"><?php echo e($reclamation->stagiaire?->name); ?></div>
                <?php endif; ?>
                <div class="msg-content"
                     style="<?php echo e(!$isStagiaire ? "background:{$msgBg};border:1px solid {$msgBorder};color:#1e293b;" : ''); ?>">
                    <div style="font-size:10px;font-weight:700;margin-bottom:6px;opacity:.6;letter-spacing:.3px;">
                        📝 RÉCLAMATION INITIALE
                    </div>
                    <?php echo e($reclamation->description); ?>

                </div>
                <div class="msg-meta"><?php echo e($reclamation->created_at->format('H:i')); ?></div>
            </div>

            
            <?php $__currentLoopData = $reclamation->messages; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $msg): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php
                    $isMe   = $msg->sender_id === $user->id;
                    $canAct = $isMe && is_null($msg->seen_at);
                ?>
                <div class="msg-bubble <?php echo e($isMe ? 'mine' : 'theirs'); ?>"
                     id="msg-<?php echo e($msg->id); ?>"
                     data-seen="<?php echo e($msg->seen_at ? '1' : '0'); ?>"
                     data-mine="<?php echo e($isMe ? '1' : '0'); ?>">

                    
                    <?php if($canAct): ?>
                    <div class="msg-actions">
                        <button class="msg-action-btn btn-edit-msg"
                                onclick="startEdit(<?php echo e($msg->id); ?>)">✏️ Modifier</button>
                        <button class="msg-action-btn btn-delete-msg"
                                onclick="deleteMsg(<?php echo e($msg->id); ?>)">🗑 Supprimer</button>
                    </div>
                    <?php endif; ?>

                    <?php if (! ($isMe)): ?>
                        <div class="msg-sender">
                            <?php echo e($msg->sender?->name); ?>

                            <span style="font-weight:400;color:#cbd5e1;">· <?php echo e(ucfirst($msg->sender?->role)); ?></span>
                        </div>
                    <?php endif; ?>

                    <div class="msg-content" id="msg-content-<?php echo e($msg->id); ?>"><?php echo e($msg->message); ?></div>

                    <div style="display:flex;align-items:center;gap:6px;flex-wrap:wrap;">
                        <div class="msg-meta"><?php echo e($msg->created_at->format('H:i')); ?></div>

                        <?php if($msg->edited_at): ?>
                            <div class="msg-edited <?php echo e($isMe ? '' : 'theirs'); ?>">
                                · modifié <?php echo e($msg->edited_at->format('H:i')); ?>

                            </div>
                        <?php endif; ?>

                        <?php if($isMe): ?>
                            <?php if($msg->seen_at): ?>
                                <div class="msg-seen" title="Vu">✓✓</div>
                            <?php else: ?>
                                <div class="msg-seen" style="opacity:.4;" title="Envoyé">✓</div>
                            <?php endif; ?>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

            
            <div class="typing-indicator" id="typing-indicator">
                <div class="typing-dots"><span></span><span></span><span></span></div>
            </div>
        </div>

        
        <?php if($reclamation->status === 'traite'): ?>
            <div class="traite-notice">✅ Réclamation traitée — les réponses sont désactivées.</div>
        <?php elseif($canReply): ?>
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
        <?php endif; ?>
    </div>

    
    <?php if($isStaff): ?>
    <div class="admin-panel">
        <h3>⚙️ Gestion</h3>
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
            <div>
                <div style="font-size:11px;font-weight:700;color:#64748b;margin-bottom:6px;">Assigner à</div>
                <form action="<?php echo e(route('reclamations.assign', $reclamation)); ?>" method="POST">
                    <?php echo csrf_field(); ?> <?php echo method_field('PATCH'); ?>
                    <select name="assigned_to" class="f-select" onchange="this.form.submit()">
                        <option value="">— Non assignée —</option>
                        <?php $__currentLoopData = $assignableUsers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $u): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($u->id); ?>" <?php echo e($reclamation->assigned_to === $u->id ? 'selected' : ''); ?>>
                                <?php echo e($u->name); ?> (<?php echo e(ucfirst($u->role)); ?>)
                            </option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </form>
            </div>
            <div>
                <div style="font-size:11px;font-weight:700;color:#64748b;margin-bottom:6px;">Statut</div>
                <form action="<?php echo e(route('reclamations.status', $reclamation)); ?>" method="POST">
                    <?php echo csrf_field(); ?> <?php echo method_field('PATCH'); ?>
                    <select name="status" class="f-select" style="margin-bottom:8px;">
                        <?php $__currentLoopData = \App\Models\Reclamation::STATUSES; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $k => $cfg): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($k); ?>" <?php echo e($reclamation->status === $k ? 'selected' : ''); ?>>
                                <?php echo e($cfg['icon']); ?> <?php echo e($cfg['label']); ?>

                            </option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                    <button type="submit" class="btn-action">Mettre à jour</button>
                </form>
            </div>
        </div>
        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('reclamation-manage')): ?>
        <form action="<?php echo e(route('reclamations.destroy', $reclamation)); ?>" method="POST"
              onsubmit="return confirm('Supprimer ?');" style="margin-top:12px;text-align:right;">
            <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
            <button type="submit"
                    style="font-size:11px;font-weight:700;padding:7px 16px;border-radius:10px;
                           background:white;border:1.5px solid #fecdd3;color:#be123c;cursor:pointer;">
                🗑 Supprimer
            </button>
        </form>
        <?php endif; ?>
    </div>
    <?php endif; ?>

    
    <?php if($isAssigned && !$isStaff): ?>
    <div class="admin-panel" id="assigned-status-panel">
        <h3>📋 Mettre à jour le statut</h3>
        <div style="display:flex;align-items:flex-end;gap:10px;">
            <div style="flex:1;">
                <div style="font-size:11px;font-weight:700;color:#64748b;margin-bottom:6px;">
                    Statut actuel :
                    <span id="asgn-status-badge" class="badge"
                          style="background:<?php echo e($sc['bg']); ?>;color:<?php echo e($sc['color']); ?>;
                                 border:1px solid <?php echo e($sc['border']); ?>;margin-left:4px;">
                        <?php echo e($sc['icon']); ?> <?php echo e($sc['label']); ?>

                    </span>
                </div>
                <select id="asgn-status-select" class="f-select">
                    <?php $__currentLoopData = \App\Models\Reclamation::STATUSES; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $k => $cfg): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php if(in_array($k, ['en_cours', 'traite'])): ?>
                            <option value="<?php echo e($k); ?>"
                                <?php echo e($reclamation->status === $k ? 'selected' : ''); ?>>
                                <?php echo e($cfg['icon']); ?> <?php echo e($cfg['label']); ?>

                            </option>
                        <?php endif; ?>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>
            <button id="asgn-status-btn" class="btn-action"
                    style="width:auto;padding:8px 20px;white-space:nowrap;"
                    onclick="updateAssignedStatus()">
                ✅ Mettre à jour
            </button>
        </div>
        <div id="asgn-status-msg" style="font-size:11px;font-weight:600;
             margin-top:10px;display:none;padding:8px 12px;border-radius:10px;"></div>
    </div>
    <?php endif; ?>

</div>
<script>
document.addEventListener('DOMContentLoaded', function () {

    const CURRENT_USER_ID = <?php echo e($user->id); ?>;
    const RECLAMATION_ID  = <?php echo e($reclamation->id); ?>;
    const SEND_URL        = "<?php echo e(route('reclamations.message', $reclamation)); ?>";
    const MARK_SEEN_URL   = "<?php echo e(route('reclamations.seen', $reclamation)); ?>";
    const MSG_BASE_URL    = "/reclamations/<?php echo e($reclamation->id); ?>/message/";
    const CSRF_TOKEN      = "<?php echo e(csrf_token()); ?>";

    const area     = document.getElementById('messages-area');
    const input    = document.getElementById('reply-input');
    const sendBtn  = document.getElementById('send-btn');
    const typingEl = document.getElementById('typing-indicator');

    // ── Mark messages as seen on load ────────────────────────
    fetch(MARK_SEEN_URL, {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': CSRF_TOKEN, 'Accept': 'application/json' }
    });

    function scrollBottom() { if (area) area.scrollTop = area.scrollHeight; }
    scrollBottom();

    function escHtml(str) {
        return String(str)
            .replace(/&/g,'&amp;').replace(/</g,'&lt;')
            .replace(/>/g,'&gt;').replace(/"/g,'&quot;');
    }
    function capitalize(str) { return str ? str.charAt(0).toUpperCase() + str.slice(1) : ''; }

    // ── Render new bubble ─────────────────────────────────────
    function renderBubble(data, mine) {
        const div = document.createElement('div');
        div.className = 'msg-bubble ' + (mine ? 'mine' : 'theirs');
        div.id = 'msg-' + data.id;
        div.dataset.seen = '0';
        div.dataset.mine = mine ? '1' : '0';

        let html = '';

        if (mine) {
            html += `
            <div class="msg-actions">
                <button class="msg-action-btn btn-edit-msg"   onclick="startEdit(${data.id})">✏️ Modifier</button>
                <button class="msg-action-btn btn-delete-msg" onclick="deleteMsg(${data.id})">🗑 Supprimer</button>
            </div>`;
        }

        if (!mine) {
            const role = data.sender?.role
                ? ` <span style="font-weight:400;color:#cbd5e1;">· ${capitalize(data.sender.role)}</span>`
                : '';
            html += `<div class="msg-sender">${escHtml(data.sender?.name ?? '')}${role}</div>`;
        }

        html += `<div class="msg-content" id="msg-content-${data.id}">${escHtml(data.message)}</div>`;
        html += `<div style="display:flex;align-items:center;gap:6px;">
                    <div class="msg-meta">${data.created_at}</div>
                    ${mine ? '<div class="msg-seen" style="opacity:.4;" title="Envoyé">✓</div>' : ''}
                 </div>`;

        div.innerHTML = html;
        area.insertBefore(div, typingEl);
        scrollBottom();
    }

    // ── Remove bubble with animation ──────────────────────────
    function removeBubble(msgId) {
        const el = document.getElementById('msg-' + msgId);
        if (el) {
            el.style.transition = 'opacity .2s, transform .2s';
            el.style.opacity    = '0';
            el.style.transform  = 'scale(.95)';
            setTimeout(() => el.remove(), 220);
        }
    }

    // ── Apply edit to bubble ──────────────────────────────────
    function applyEdit(msgId, newText, editedAt) {
        const contentEl = document.getElementById('msg-content-' + msgId);
        if (contentEl) contentEl.textContent = newText;

        const bubble  = document.getElementById('msg-' + msgId);
        const actions = bubble?.querySelector('.msg-actions');
        if (actions) actions.style.display = '';

        // Add/update "modifié" label
        const metaRow = bubble?.querySelector('[style*="display:flex"]');
        if (metaRow) {
            let editedEl = metaRow.querySelector('.msg-edited');
            if (!editedEl) {
                editedEl = document.createElement('div');
                editedEl.className = 'msg-edited';
                metaRow.appendChild(editedEl);
            }
            editedEl.textContent = '· modifié ' + editedAt;
        }
    }

    // ── Mark my own bubbles as seen (✓✓) ─────────────────────
    function markBubbleSeen(msgId) {
        const bubble  = document.getElementById('msg-' + msgId);
        if (!bubble || bubble.dataset.mine !== '1') return;

        bubble.dataset.seen = '1';

        // Remove action buttons — can no longer edit/delete
        const actions = bubble.querySelector('.msg-actions');
        if (actions) actions.remove();

        // Update tick to double
        const seenEl = bubble.querySelector('.msg-seen');
        if (seenEl) {
            seenEl.style.opacity = '1';
            seenEl.title         = 'Vu';
            seenEl.textContent   = '✓✓';
        }
    }

    // ── Send message ──────────────────────────────────────────
    async function sendMessage() {
        if (!input) return;
        const text = input.value.trim();
        if (!text || sendBtn.disabled) return;
        sendBtn.disabled = true;

        const now = new Date().toLocaleTimeString('fr-FR', { hour: '2-digit', minute: '2-digit' });
        renderBubble({
            id: 'tmp-' + Date.now(),
            message: text,
            created_at: now,
            sender: { id: CURRENT_USER_ID }
        }, true);
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

    // ── Delete message ────────────────────────────────────────
    window.deleteMsg = async function(msgId) {
        if (!confirm('Supprimer ce message ?')) return;

        const res = await fetch(MSG_BASE_URL + msgId, {
            method: 'DELETE',
            headers: { 'X-CSRF-TOKEN': CSRF_TOKEN, 'Accept': 'application/json' }
        });

        if (res.ok) {
            removeBubble(msgId);
        } else {
            const err = await res.json();
            alert(err.error ?? 'Impossible de supprimer.');
        }
    };

    // ── Edit message ──────────────────────────────────────────
    window.startEdit = function(msgId) {
        const contentEl = document.getElementById('msg-content-' + msgId);
        if (!contentEl) return;
        const originalText = contentEl.textContent.trim();

        contentEl.innerHTML = `
            <div class="edit-box">
                <textarea class="edit-textarea" id="edit-ta-${msgId}">${escHtml(originalText)}</textarea>
                <div class="edit-actions">
                    <button class="btn-cancel-edit" onclick="cancelEdit(${msgId}, \`${escHtml(originalText)}\`)">Annuler</button>
                    <button class="btn-save-edit"   onclick="saveEdit(${msgId})">Enregistrer</button>
                </div>
            </div>`;

        const ta = document.getElementById('edit-ta-' + msgId);
        if (ta) { ta.focus(); ta.setSelectionRange(ta.value.length, ta.value.length); }

        // Hide action buttons while editing
        const bubble  = document.getElementById('msg-' + msgId);
        const actions = bubble?.querySelector('.msg-actions');
        if (actions) actions.style.display = 'none';
    };

    window.cancelEdit = function(msgId, originalText) {
        const contentEl = document.getElementById('msg-content-' + msgId);
        if (contentEl) contentEl.textContent = originalText;

        const bubble  = document.getElementById('msg-' + msgId);
        const actions = bubble?.querySelector('.msg-actions');
        if (actions) actions.style.display = '';
    };

    window.saveEdit = async function(msgId) {
        const ta = document.getElementById('edit-ta-' + msgId);
        if (!ta) return;
        const newText = ta.value.trim();
        if (!newText) return;

        const res = await fetch(MSG_BASE_URL + msgId, {
            method: 'PATCH',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': CSRF_TOKEN,
                'Accept': 'application/json'
            },
            body: JSON.stringify({ message: newText }),
        });

        if (res.ok) {
            const data = await res.json();
            applyEdit(msgId, data.message, data.edited_at);
        } else {
            const err = await res.json();
            alert(err.error ?? 'Impossible de modifier.');
        }
    };

    // ── Input events ──────────────────────────────────────────
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

    // ── Echo real-time ────────────────────────────────────────
    if (typeof window.Echo !== 'undefined') {
        window.Echo.private('reclamation.' + RECLAMATION_ID)

            .listen('.ReclamationMessageSent', (e) => {
                if (typingEl) typingEl.classList.remove('visible');
                if (e.sender?.id !== CURRENT_USER_ID) {
                    renderBubble(e, false);
                    // We're in the conversation — mark as seen immediately
                    fetch(MARK_SEEN_URL, {
                        method: 'POST',
                        headers: { 'X-CSRF-TOKEN': CSRF_TOKEN }
                    });
                }
            })

            .listen('.ReclamationMessageDeleted', (e) => {
                removeBubble(e.message_id);
            })

            .listen('.ReclamationMessageUpdated', (e) => {
                applyEdit(e.message_id, e.message, e.edited_at);
            })

            .listen('.ReclamationMessageSeen', (e) => {
                // The other user opened the chat — update our sent bubbles to ✓✓
                if (Array.isArray(e.message_ids)) {
                    e.message_ids.forEach(id => markBubbleSeen(id));
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
            })

            .listen('.ReclamationDeleted', (e) => {
                // Show an overlay so the user knows what happened, then redirect
                const overlay = document.createElement('div');
                overlay.style.cssText = `
                    position:fixed;inset:0;z-index:9999;
                    display:flex;flex-direction:column;align-items:center;justify-content:center;
                    background:rgba(15,23,42,0.55);backdrop-filter:blur(4px);
                `;
                overlay.innerHTML = `
                    <div style="background:white;border-radius:20px;padding:32px 40px;
                                text-align:center;box-shadow:0 20px 60px rgba(0,0,0,0.2);max-width:340px;">
                        <div style="font-size:40px;margin-bottom:12px;">🗑️</div>
                        <div style="font-size:15px;font-weight:800;color:#1e293b;margin-bottom:6px;">
                            Réclamation supprimée
                        </div>
                        <div style="font-size:12px;color:#64748b;margin-bottom:22px;">
                            Cette réclamation a été supprimée par l'administration.
                            Vous allez être redirigé…
                        </div>
                        <div style="font-size:11px;color:#94a3b8;">Redirection dans <span id="cd">3</span>s</div>
                    </div>
                `;
                document.body.appendChild(overlay);

                let n = 3;
                const iv = setInterval(() => {
                    n--;
                    const el = document.getElementById('cd');
                    if (el) el.textContent = n;
                    if (n <= 0) {
                        clearInterval(iv);
                        window.location.href = '<?php echo e(route('reclamations.index')); ?>';
                    }
                }, 1000);
            })

            .listenForWhisper('typing', () => {
                if (typingEl) {
                    typingEl.classList.add('visible');
                    scrollBottom();
                    setTimeout(() => typingEl.classList.remove('visible'), 3000);
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

    // ── Assigned user status update (AJAX) ────────────────────
    window.updateAssignedStatus = async function () {
        const sel    = document.getElementById('asgn-status-select');
        const btn    = document.getElementById('asgn-status-btn');
        const msgBox = document.getElementById('asgn-status-msg');
        if (!sel || !btn) return;

        const status = sel.value;
        btn.disabled  = true;
        btn.textContent = '…';

        try {
            const res = await fetch('<?php echo e(route('reclamations.status', $reclamation)); ?>', {
                method : 'POST',
                headers: {
                    'Content-Type' : 'application/json',
                    'X-CSRF-TOKEN' : CSRF_TOKEN,
                    'Accept'       : 'application/json',
                },
                body: JSON.stringify({ status, _method: 'PATCH' }),
            });

            const data = await res.json();

            if (res.ok && data.ok) {
                // Update the hero badge
                const heroBadge = document.getElementById('status-badge');
                const asgnBadge = document.getElementById('asgn-status-badge');
                if (heroBadge && data.badge) {
                    heroBadge.style.background = data.badge.bg;
                    heroBadge.style.color      = data.badge.color;
                    heroBadge.style.border     = '1px solid ' + data.badge.border;
                    heroBadge.textContent      = data.badge.icon + ' ' + data.badge.label;
                    heroBadge.classList.add('status-updated');
                    setTimeout(() => heroBadge.classList.remove('status-updated'), 400);
                }
                if (asgnBadge && data.badge) {
                    asgnBadge.style.background = data.badge.bg;
                    asgnBadge.style.color      = data.badge.color;
                    asgnBadge.style.border     = '1px solid ' + data.badge.border;
                    asgnBadge.textContent      = data.badge.icon + ' ' + data.badge.label;
                }
                // If marked as traité, close the reply area
                if (status === 'traite') {
                    const replyArea = document.querySelector('.reply-area');
                    if (replyArea) {
                        replyArea.innerHTML = '<div class="traite-notice">✅ Réclamation traitée — conversation fermée.</div>';
                    }
                }
                showAsgnMsg('✅ Statut mis à jour avec succès.', '#dcfce7', '#166534');
            } else {
                showAsgnMsg('❌ ' + (data.message ?? 'Erreur.'), '#fee2e2', '#be123c');
            }
        } catch (err) {
            showAsgnMsg('❌ Erreur réseau.', '#fee2e2', '#be123c');
        } finally {
            btn.disabled    = false;
            btn.textContent = '✅ Mettre à jour';
        }
    };

    function showAsgnMsg(text, bg, color) {
        const el = document.getElementById('asgn-status-msg');
        if (!el) return;
        el.textContent       = text;
        el.style.background  = bg;
        el.style.color       = color;
        el.style.display     = 'block';
        setTimeout(() => { el.style.display = 'none'; }, 4000);
    }

});
</script>
<?php $__env->startPush('scripts'); ?>
<script>
// Tell the global Echo listener which reclamation is open.
// Any incoming notification for THIS reclamation will be
// suppressed in the bell and auto-marked as read.
window.__currentReclamationId = <?php echo e($reclamation->id); ?>;
 
// On page load → clear all existing unread notifications for this reclamation.
document.addEventListener('DOMContentLoaded', function () {
    fetch('/notifications/reclamation/<?php echo e($reclamation->id); ?>/read', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept':       'application/json',
        },
    })
    .then(r => r.json())
    .then(data => {
        // Sync the bell badge with the real server count
        if (typeof setUnreadCount === 'function') {
            setUnreadCount(data.unread_count ?? 0);
        }
    })
    .catch(() => {}); // silent fail — non-critical
});
</script>
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\Project\gestion-CF\resources\views/reclamations/show.blade.php ENDPATH**/ ?>