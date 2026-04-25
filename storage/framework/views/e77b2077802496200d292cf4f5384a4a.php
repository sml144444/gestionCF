

<?php $__env->startSection('title', 'Réclamations assignées'); ?>
<?php $__env->startSection('page-title', 'Mes réclamations assignées'); ?>

<?php $__env->startSection('content'); ?>
<?php
    $user         = Auth::user();
    $gradient     = $user->role === 'formateur'
        ? 'linear-gradient(135deg,#1a4f8a 0%,#2563eb 100%)'
        : 'linear-gradient(135deg,#1e293b 0%,#334155 100%)';
    $statusConfig = \App\Models\Reclamation::STATUSES;
    $typeConfig   = \App\Models\Reclamation::TYPES;
?>

<style>
* { box-sizing:border-box; }
.asgn-wrap { font-family:'Segoe UI',system-ui,sans-serif; max-width:1100px; margin:0 auto; }
.hero { background:<?php echo e($gradient); ?>; border-radius:20px; padding:26px 30px; margin-bottom:22px;
        display:flex; align-items:center; justify-content:space-between; gap:14px;
        flex-wrap:wrap; position:relative; overflow:hidden; }
.hero::after { content:''; position:absolute; right:-40px; top:-40px; width:200px; height:200px;
               border-radius:50%; background:rgba(255,255,255,0.06); pointer-events:none; }
.stat-pill { background:rgba(255,255,255,0.15); border:1px solid rgba(255,255,255,0.2);
             border-radius:12px; padding:9px 16px; text-align:center; }
.stat-pill-val { font-size:20px; font-weight:900; color:white; }
.stat-pill-lbl { font-size:10px; color:rgba(255,255,255,0.72); }
.flash-ok { display:flex; align-items:center; gap:12px; padding:14px 18px; border-radius:14px;
            margin-bottom:18px; background:#f0f7ff; border:1px solid #bfdbfe; animation:fi .3s; }
@keyframes fi { from{opacity:0;transform:translateY(-6px)} to{opacity:1;transform:translateY(0)} }
.filter-bar { background:white; border-radius:14px; border:1px solid #e2e8f0; padding:12px 16px;
              margin-bottom:18px; display:flex; gap:10px; align-items:center; flex-wrap:wrap; }
.f-select { border:1.5px solid #e2e8f0; border-radius:10px; padding:7px 12px; font-size:12px;
            color:#1e293b; background:white; cursor:pointer; outline:none; }
.rc-table-wrap { background:white; border-radius:18px; border:1px solid #e2e8f0; overflow:hidden; }
.rc-table { width:100%; border-collapse:collapse; }
.rc-table th { padding:11px 14px; background:#f8fafc; font-size:9px; font-weight:800;
               text-transform:uppercase; letter-spacing:.7px; color:#64748b;
               border-bottom:1px solid #e2e8f0; text-align:left; white-space:nowrap; }
.rc-table td { padding:13px 14px; border-bottom:1px solid #f1f5f9; font-size:12px;
               color:#1e293b; vertical-align:middle; }
.rc-table tr:last-child td { border-bottom:none; }
.rc-table tr:hover td { background:#fafbfd; }
.badge { font-size:9px; font-weight:700; padding:3px 9px; border-radius:7px; white-space:nowrap; }
.avatar-sm { width:28px; height:28px; border-radius:8px; background:#f5f3ff;
             display:inline-flex; align-items:center; justify-content:center;
             font-size:9px; font-weight:800; color:#6d28d9; flex-shrink:0; }
.btn-view { font-size:11px; font-weight:700; padding:6px 14px; border-radius:9px;
            background:<?php echo e($gradient); ?>; color:white; text-decoration:none;
            display:inline-flex; align-items:center; gap:4px; transition:opacity .15s; }
.btn-view:hover { opacity:.85; }
.new-border td:first-child { border-left:3px solid #2563eb; }
</style>

<div class="asgn-wrap">

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
            <h1 style="font-size:20px;font-weight:800;color:white;margin:0;">Réclamations assignées</h1>
            <p style="font-size:12px;color:rgba(255,255,255,0.72);margin:3px 0 0;">
                Réclamations qui vous ont été assignées
            </p>
        </div>
    </div>
    <div style="display:flex;gap:10px;flex-wrap:wrap;">
        <?php $__currentLoopData = ['total'=>'Total','en_attente'=>'En attente','en_cours'=>'En cours','traite'=>'Traités']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $k => $l): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <div class="stat-pill">
            <div class="stat-pill-val"><?php echo e($stats[$k]); ?></div>
            <div class="stat-pill-lbl"><?php echo e($l); ?></div>
        </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>
</div>


<div class="filter-bar">
    <form method="GET" action="<?php echo e(route('reclamations.index')); ?>"
          style="display:flex;gap:10px;flex-wrap:wrap;align-items:center;width:100%;">
        <select name="status" class="f-select" onchange="this.form.submit()">
            <option value="">Tous les statuts</option>
            <?php $__currentLoopData = $statusConfig; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $k => $cfg): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <option value="<?php echo e($k); ?>" <?php echo e(request('status') === $k ? 'selected' : ''); ?>>
                    <?php echo e($cfg['icon']); ?> <?php echo e($cfg['label']); ?>

                </option>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </select>
        <?php if(request('status')): ?>
            <a href="<?php echo e(route('reclamations.index')); ?>"
               style="font-size:11px;font-weight:600;padding:7px 12px;border-radius:10px;
                      background:white;color:#64748b;border:1.5px solid #e2e8f0;text-decoration:none;">
                ✕ Réinitialiser
            </a>
        <?php endif; ?>
    </form>
</div>


<div class="rc-table-wrap">
    <?php if($reclamations->isEmpty()): ?>
        <div style="text-align:center;padding:60px 20px;">
            <div style="font-size:48px;margin-bottom:12px;">✅</div>
            <p style="font-size:14px;font-weight:700;color:#1e293b;margin:0 0 4px;">
                Aucune réclamation assignée
            </p>
            <p style="font-size:12px;color:#94a3b8;margin:0;">
                Aucune réclamation ne vous a été assignée pour le moment.
            </p>
        </div>
    <?php else: ?>
        <div style="overflow-x:auto;">
        <table class="rc-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Stagiaire</th>
                    <th>Type</th>
                    <th>Aperçu</th>
                    <th>Messages</th>
                    <th>Statut</th>
                    <th>Ouvert le</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
            <?php $__currentLoopData = $reclamations; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $rec): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php
                    $sc     = $statusConfig[$rec->status] ?? $statusConfig['en_attente'];
                    $tc     = $typeConfig[$rec->type]     ?? $typeConfig['autre'];
                    $sName  = $rec->stagiaire?->name ?? '—';
                    $initials = strtoupper(mb_substr($sName, 0, 1) . mb_substr(explode(' ', $sName)[1] ?? '', 0, 1));
                ?>
                <tr class="<?php echo e($rec->status === 'en_attente' ? 'new-border' : ''); ?>">
                    <td><span style="font-size:11px;font-weight:700;color:#94a3b8;">#<?php echo e($rec->id); ?></span></td>
                    <td>
                        <div style="display:flex;align-items:center;gap:8px;">
                            <div class="avatar-sm"><?php echo e($initials); ?></div>
                            <div>
                                <div style="font-weight:700;font-size:12px;"><?php echo e($sName); ?></div>
                                <div style="font-size:10px;color:#94a3b8;"><?php echo e($rec->stagiaire?->email ?? ''); ?></div>
                            </div>
                        </div>
                    </td>
                    <td>
                        <span class="badge" style="background:#eff6ff;color:#1e40af;">
                            <?php echo e($tc['icon']); ?> <?php echo e($tc['label']); ?>

                        </span>
                    </td>
                    <td style="max-width:220px;">
                        <p style="margin:0;font-size:12px;color:#475569;
                                  overflow:hidden;display:-webkit-box;
                                  -webkit-line-clamp:2;-webkit-box-orient:vertical;">
                            <?php echo e($rec->description); ?>

                        </p>
                    </td>
                    <td>
                        <span style="font-size:11px;font-weight:700;color:<?php echo e($rec->messages_count > 0 ? '#2563eb' : '#94a3b8'); ?>;">
                            💬 <?php echo e($rec->messages_count); ?>

                        </span>
                    </td>
                    <td>
                        <span class="badge" style="background:<?php echo e($sc['bg']); ?>;color:<?php echo e($sc['color']); ?>;border:1px solid <?php echo e($sc['border']); ?>;">
                            <?php echo e($sc['icon']); ?> <?php echo e($sc['label']); ?>

                        </span>
                    </td>
                    <td>
                        <div style="font-size:11px;color:#64748b;"><?php echo e($rec->created_at->format('d/m/Y')); ?></div>
                        <div style="font-size:10px;color:#94a3b8;"><?php echo e($rec->updated_at->diffForHumans()); ?></div>
                    </td>
                    <td>
                        <a href="<?php echo e(route('reclamations.show', $rec)); ?>" class="btn-view">
                            💬 Ouvrir
                        </a>
                    </td>
                </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </tbody>
        </table>
        </div>

        <?php if($reclamations->hasPages()): ?>
        <div style="padding:12px 18px;border-top:1px solid #f1f5f9;">
            <?php echo e($reclamations->links()); ?>

        </div>
        <?php endif; ?>
    <?php endif; ?>
</div>

</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\Project\gestion-CF\resources\views/reclamations/assigned.blade.php ENDPATH**/ ?>