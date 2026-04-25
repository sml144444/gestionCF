

<?php $__env->startSection('title', 'Mes réclamations'); ?>
<?php $__env->startSection('page-title', 'Mes réclamations'); ?>

<?php $__env->startSection('content'); ?>
<?php
    $gradient     = 'linear-gradient(135deg,#1a4f8a 0%,#2563eb 100%)';
    $statusConfig = \App\Models\Reclamation::STATUSES;
    $typeConfig   = \App\Models\Reclamation::TYPES;
?>

<style>
* { box-sizing:border-box; }
.my-rc { font-family:'Segoe UI',system-ui,sans-serif; max-width:900px; margin:0 auto; }

/* Hero */
.hero { background:<?php echo e($gradient); ?>; border-radius:20px; padding:26px 30px; margin-bottom:22px;
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
            background:<?php echo e($gradient); ?>; color:white; text-decoration:none;
            display:inline-flex; align-items:center; gap:5px; transition:opacity .15s; }
.btn-open:hover { opacity:.85; }
</style>

<div class="my-rc">

<?php if(session('success')): ?>
<div class="flash-ok">
    <svg width="16" height="16" fill="none" stroke="#1e40af" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
    </svg>
    <span style="font-size:13px;font-weight:600;color:#1e40af;"><?php echo e(session('success')); ?></span>
</div>
<?php endif; ?>


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
            <p style="font-size:12px;color:rgba(255,255,255,0.72);margin:3px 0 0;">
                <?php echo e($reclamations->total()); ?> réclamation(s) soumise(s)
            </p>
        </div>
    </div>
    <a href="<?php echo e(route('reclamations.create')); ?>" class="btn-new">
        <svg width="13" height="13" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/>
        </svg>
        Nouvelle réclamation
    </a>
</div>


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


<?php $__empty_1 = true; $__currentLoopData = $reclamations; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $rec): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
    <?php
        $sc     = $statusConfig[$rec->status] ?? $statusConfig['en_attente'];
        $tc     = $typeConfig[$rec->type]     ?? $typeConfig['autre'];
        $hasNew = $rec->messages_count > 0;
        $newReply = $rec->status === 'en_cours' && $hasNew;
    ?>
    <div class="ticket-card <?php echo e($newReply ? 'new-reply' : ''); ?>">
        <div class="ticket-head">
            <div style="display:flex;align-items:center;gap:10px;flex-wrap:wrap;">
                <span style="font-size:10px;color:#94a3b8;font-weight:600;">#<?php echo e($rec->id); ?></span>
                <span class="badge" style="background:#eff6ff;color:#1e40af;">
                    <?php echo e($tc['icon']); ?> <?php echo e($tc['label']); ?>

                </span>
                <?php if($newReply): ?>
                    <span class="badge" style="background:#dbeafe;color:#1e40af;animation:pulse 2s infinite;">
                        🔔 Nouvelle réponse
                    </span>
                <?php endif; ?>
            </div>
            <span class="badge" style="background:<?php echo e($sc['bg']); ?>;color:<?php echo e($sc['color']); ?>;border:1px solid <?php echo e($sc['border']); ?>;">
                <?php echo e($sc['icon']); ?> <?php echo e($sc['label']); ?>

            </span>
        </div>

        <div class="ticket-body"><?php echo e($rec->description); ?></div>

        <div class="ticket-footer">
            <div style="display:flex;align-items:center;gap:10px;">
                <span class="msg-count <?php echo e($hasNew ? 'has-msgs' : ''); ?>">
                    💬 <?php echo e($rec->messages_count); ?> message(s)
                </span>
                <span style="font-size:10px;color:#94a3b8;">
                    <?php echo e($rec->created_at->format('d/m/Y')); ?> · mis à jour <?php echo e($rec->updated_at->diffForHumans()); ?>

                </span>
            </div>
            <a href="<?php echo e(route('reclamations.show', $rec)); ?>" class="btn-open">
                Voir la conversation →
            </a>
        </div>
    </div>
<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
    <div style="text-align:center;padding:60px 20px;background:white;border-radius:20px;border:1px solid #e2e8f0;">
        <div style="font-size:48px;margin-bottom:12px;">💬</div>
        <p style="font-size:15px;font-weight:700;color:#1e293b;margin:0 0 6px;">Aucune réclamation</p>
        <p style="font-size:12px;color:#94a3b8;margin:0 0 22px;">
            Vous n'avez pas encore soumis de réclamation.
        </p>
        <a href="<?php echo e(route('reclamations.create')); ?>" class="btn-open">
            <svg width="13" height="13" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/>
            </svg>
            Soumettre une réclamation
        </a>
    </div>
<?php endif; ?>

<?php if($reclamations->hasPages()): ?>
<div style="margin-top:16px;"><?php echo e($reclamations->links()); ?></div>
<?php endif; ?>

</div>

<style>
@keyframes pulse {
    0%, 100% { opacity:1; }
    50% { opacity:.6; }
}
</style>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\Project\gestion-CF\resources\views/reclamations/my.blade.php ENDPATH**/ ?>