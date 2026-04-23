

<?php $__env->startSection('title', 'Mes réclamations'); ?>
<?php $__env->startSection('page-title', 'Mes réclamations'); ?>

<?php $__env->startSection('content'); ?>
<?php
    $p = [
        'primary'  => '#1a4f8a',
        'medium'   => '#2563eb',
        'light'    => '#eff6ff',
        'lighter'  => '#f0f7ff',
        'text'     => '#1e40af',
        'border'   => '#bfdbfe',
        'shadow'   => 'rgba(26,79,138,0.15)',
        'gradient' => 'linear-gradient(135deg,#1a4f8a 0%,#2563eb 100%)',
    ];
?>

<style>
:root {
    --accent:    <?php echo e($p['primary']); ?>;
    --accent-md: <?php echo e($p['medium']); ?>;
    --accent-lt: <?php echo e($p['light']); ?>;
    --accent-ltr:<?php echo e($p['lighter']); ?>;
    --accent-tx: <?php echo e($p['text']); ?>;
    --accent-bd: <?php echo e($p['border']); ?>;
    --accent-sh: <?php echo e($p['shadow']); ?>;
    --accent-gr: <?php echo e($p['gradient']); ?>;
}
.my-rc-wrap { font-family:'Segoe UI',system-ui,sans-serif; max-width:1200px; margin:0 auto; }
.rc-hero { background:var(--accent-gr); border-radius:20px; padding:28px 32px; margin-bottom:24px; display:flex; align-items:center; justify-content:space-between; gap:16px; flex-wrap:wrap; position:relative; overflow:hidden; }
.rc-hero::after { content:''; position:absolute; right:-40px; top:-40px; width:200px; height:200px; border-radius:50%; background:rgba(255,255,255,0.06); pointer-events:none; }
.rc-hero-icon { width:52px; height:52px; border-radius:16px; background:rgba(255,255,255,0.15); display:flex; align-items:center; justify-content:center; flex-shrink:0; }
.rc-hero-title { font-size:20px; font-weight:800; color:white; margin:0; }
.rc-hero-sub { font-size:12px; color:rgba(255,255,255,0.75); margin-top:3px; }
.flash-ok { display:flex; align-items:center; gap:12px; padding:14px 18px; border-radius:14px; margin-bottom:18px; background:var(--accent-ltr); border:1px solid var(--accent-bd); animation:fadeIn .3s ease; }
.flash-ok-icon { width:38px; height:38px; border-radius:50%; background:var(--accent-gr); display:flex; align-items:center; justify-content:center; flex-shrink:0; }
@keyframes fadeIn { from{opacity:0;transform:translateY(-6px)} to{opacity:1;transform:translateY(0)} }
.rc-card { background:white; border-radius:18px; border:1px solid #e2e8f0; padding:20px 24px; margin-bottom:14px; transition:all .2s; }
.rc-card:hover { transform:translateY(-1px); box-shadow:0 8px 24px rgba(0,0,0,0.08); }
.badge { font-size:9px; font-weight:700; padding:4px 10px; border-radius:8px; }
.badge-attente { background:#fef3c7; color:#92400e; border:1px solid #fde68a; }
.badge-traitee { background:#dcfce7; color:#166534; border:1px solid #bbf7d0; }
.type-badge { font-size:9px; font-weight:700; padding:3px 9px; border-radius:6px; }
.type-note      { background:#eff6ff; color:#1e40af; }
.type-absence   { background:#fff7ed; color:#9a3412; }
.type-emploi    { background:#f0fdf4; color:#166534; }
.type-formateur { background:#fdf4ff; color:#6b21a8; }
.type-autre     { background:#f1f5f9; color:#334155; }
.btn-primary { font-size:12px; font-weight:700; padding:10px 20px; border-radius:12px; background:var(--accent-gr); color:white; text-decoration:none; display:inline-flex; align-items:center; gap:6px; box-shadow:0 2px 8px var(--accent-sh); transition:opacity .15s; }
.btn-primary:hover { opacity:.88; }
</style>

<div class="my-rc-wrap">

<?php if(session('success')): ?>
    <div class="flash-ok">
        <div class="flash-ok-icon"><svg width="18" height="18" fill="none" stroke="white" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg></div>
        <p style="font-size:13px;font-weight:600;color:var(--accent-tx);margin:0;"><?php echo e(session('success')); ?></p>
    </div>
<?php endif; ?>

<div class="rc-hero">
    <div style="display:flex;align-items:center;gap:16px;">
        <div class="rc-hero-icon">
            <svg width="26" height="26" fill="none" stroke="white" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/>
            </svg>
        </div>
        <div>
            <h1 class="rc-hero-title">Mes réclamations</h1>
            <p class="rc-hero-sub"><?php echo e($reclamations->total()); ?> réclamation(s) soumise(s)</p>
        </div>
    </div>
    <a href="<?php echo e(route('reclamations.create')); ?>" class="btn-primary">
        <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
        Nouvelle réclamation
    </a>
</div>

<?php $__empty_1 = true; $__currentLoopData = $reclamations; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $rec): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
    <div class="rc-card">
        <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:12px;margin-bottom:12px;">
            <div style="display:flex;align-items:center;gap:10px;">
                <span class="type-badge type-<?php echo e($rec->type); ?>">
                    <?php echo e(['note'=>'📝 Note','absence'=>'📅 Absence','emploi'=>'🗓️ Emploi','formateur'=>'👨‍🏫 Formateur','autre'=>'📌 Autre'][$rec->type] ?? $rec->type); ?>

                </span>
                <span style="font-size:11px;color:#94a3b8;"><?php echo e($rec->created_at->format('d/m/Y à H:i')); ?></span>
            </div>
            <span class="badge <?php echo e($rec->status === 'traitee' ? 'badge-traitee' : 'badge-attente'); ?>">
                <?php echo e($rec->status === 'traitee' ? '✅ Traitée' : '⏳ En attente'); ?>

            </span>
        </div>
        <p style="font-size:13px;color:#475569;margin:0;line-height:1.6;"><?php echo e($rec->description); ?></p>
    </div>
<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
    <div style="text-align:center;padding:60px 20px;background:white;border-radius:20px;border:1px solid #e2e8f0;">
        <div style="width:64px;height:64px;border-radius:20px;background:var(--accent-lt);margin:0 auto 16px;display:flex;align-items:center;justify-content:center;">
            <svg width="28" height="28" fill="none" stroke="var(--accent)" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/></svg>
        </div>
        <p style="font-size:14px;font-weight:700;color:#1e293b;margin:0 0 8px;">Aucune réclamation</p>
        <p style="font-size:12px;color:#94a3b8;margin:0 0 20px;">Vous n'avez pas encore soumis de réclamation.</p>
        <a href="<?php echo e(route('reclamations.create')); ?>" class="btn-primary">
            <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
            Soumettre une réclamation
        </a>
    </div>
<?php endif; ?>

<?php if($reclamations->hasPages()): ?>
<div style="margin-top:20px;"><?php echo e($reclamations->links()); ?></div>
<?php endif; ?>

</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\Project\gestion-CF\resources\views/reclamations/my.blade.php ENDPATH**/ ?>