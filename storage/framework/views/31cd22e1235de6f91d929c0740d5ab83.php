

<?php $__env->startSection('title', 'Bulletin — ' . $stagiaire->name); ?>
<?php $__env->startSection('page-title', 'Bulletin de notes'); ?>

<?php $__env->startSection('content'); ?>
<?php
    $palettes = [
        'admin'        => ['primary' => '#0a6640', 'light' => '#e8f5ee', 'text' => '#065f38', 'shadow' => 'rgba(10,102,64,0.2)'],
        'gestionnaire' => ['primary' => '#1e293b', 'light' => '#f1f5f9', 'text' => '#1e293b', 'shadow' => 'rgba(30,41,59,0.2)'],
        'formateur'    => ['primary' => '#1d4ed8', 'light' => '#eff6ff', 'text' => '#1e40af', 'shadow' => 'rgba(29,78,216,0.2)'],
    ];
    $p      = $palettes[Auth::user()->role] ?? $palettes['gestionnaire'];
    $accent = $p['primary'];
    $light  = $p['light'];
    $text   = $p['text'];
    $shadow = $p['shadow'];

    // General average: only show if EVERY module has a moduleGrade
    // (discipline is always calculated so it never blocks the average)
    $allGraded = $modulesWithNotes->isNotEmpty()
        && $modulesWithNotes->every(fn($m) => $m['moduleGrade'] !== null);
?>

<style>
.bls-wrap { font-family:'Segoe UI',system-ui,sans-serif; }

.breadcrumb { display:flex;align-items:center;gap:8px;font-size:12px;color:#94a3b8;margin-bottom:18px;flex-wrap:wrap; }
.breadcrumb a { color:<?php echo e($accent); ?>;font-weight:600;text-decoration:none; }
.breadcrumb a:hover { text-decoration:underline; }

.profile-card { background:white;border-radius:16px;border:1px solid #e2e8f0;padding:20px 24px;margin-bottom:20px;display:flex;align-items:center;gap:16px;flex-wrap:wrap; }
.profile-avatar { width:52px;height:52px;border-radius:50%;background:<?php echo e($light); ?>;display:flex;align-items:center;justify-content:center;font-size:20px;font-weight:800;color:<?php echo e($text); ?>;flex-shrink:0; }

/* ── Main table ── */
.bl-table-wrap { background:white;border-radius:16px;border:1px solid #e2e8f0;overflow:hidden;margin-bottom:20px; }
.bl-table { width:100%;border-collapse:collapse;font-size:12px; }
.bl-table thead tr:first-child th { padding:11px 14px;font-size:9px;font-weight:800;color:#94a3b8;text-transform:uppercase;letter-spacing:.9px;background:#f8fafc;border-bottom:1px solid #f1f5f9;white-space:nowrap;text-align:center; }
.bl-table thead tr:first-child th.col-module { text-align:left;min-width:200px;position:sticky;left:0;z-index:2;background:#f8fafc; }
.bl-table tbody td { padding:12px 14px;border-bottom:1px solid #f8fafc;vertical-align:middle;text-align:center; }
.bl-table tbody td.col-module-cell { text-align:left;position:sticky;left:0;z-index:1;background:white;border-right:1px solid #f1f5f9; }
.bl-table tbody tr:last-child td { border-bottom:none; }
.bl-table tbody tr:hover td { background:#fafbff; }
.bl-table tbody tr:hover td.col-module-cell { background:#fafbff; }

/* Discipline row highlight */
.discipline-row td { background:#fefce8 !important; border-top:2px solid #fde047 !important; }
.discipline-row:hover td { background:#fef9c3 !important; }
.discipline-row td.col-module-cell { background:#fefce8 !important; }

/* Pills */
.note-pill { display:inline-flex;align-items:center;justify-content:center;min-width:50px;height:28px;border-radius:8px;font-size:12px;font-weight:800;padding:0 6px; }
.note-high  { background:#f0fdf4;color:#16a34a;border:1.5px solid #bbf7d0; }
.note-mid   { background:#fffbeb;color:#d97706;border:1.5px solid #fde68a; }
.note-low   { background:#fff1f2;color:#dc2626;border:1.5px solid #fecdd3; }
.note-empty { background:#f8fafc;color:#cbd5e1;border:1.5px solid #e2e8f0; }

.cc-pill  { display:inline-flex;align-items:center;justify-content:center;min-width:50px;height:28px;border-radius:8px;font-size:12px;font-weight:800;padding:0 6px; }
.cc-high  { background:#fdf4ff;color:#7e22ce;border:1.5px solid #e9d5ff; }
.cc-mid   { background:#fffbeb;color:#d97706;border:1.5px solid #fde68a; }
.cc-low   { background:#fff1f2;color:#dc2626;border:1.5px solid #fecdd3; }
.cc-empty { background:#f8fafc;color:#cbd5e1;border:1.5px solid #e2e8f0; }

.moy-badge { display:inline-flex;align-items:center;justify-content:center;padding:4px 12px;border-radius:99px;font-size:12px;font-weight:800; }
.moy-high  { background:#f0fdf4;color:#16a34a;border:1.5px solid #bbf7d0; }
.moy-mid   { background:#fffbeb;color:#d97706;border:1.5px solid #fde68a; }
.moy-low   { background:#fff1f2;color:#dc2626;border:1.5px solid #fecdd3; }
.moy-none  { background:#f8fafc;color:#94a3b8;border:1.5px solid #e2e8f0; }

/* Discipline note badge */
.disc-badge { display:inline-flex;align-items:center;justify-content:center;padding:4px 12px;border-radius:99px;font-size:12px;font-weight:800; }
.disc-high  { background:#fefce8;color:#713f12;border:1.5px solid #fde047; }
.disc-mid   { background:#fff7ed;color:#c2410c;border:1.5px solid #fed7aa; }
.disc-low   { background:#fff1f2;color:#dc2626;border:1.5px solid #fecdd3; }

.type-badge { display:inline-flex;font-size:9px;font-weight:800;padding:2px 7px;border-radius:99px;text-transform:uppercase;letter-spacing:.5px; }
.type-regional { background:#eff6ff;color:#1e40af;border:1px solid #bfdbfe; }
.type-local    { background:#fdf4ff;color:#7e22ce;border:1px solid #e9d5ff; }
.info-chip { display:inline-flex;align-items:center;gap:3px;padding:2px 7px;border-radius:99px;font-size:9px;font-weight:600;background:#f1f5f9;color:#475569;border:1px solid #e2e8f0; }

/* Stat cards */
.stat-card { background:white;border-radius:14px;border:1px solid #e2e8f0;padding:16px 20px;display:flex;align-items:center;gap:14px; }
.stat-icon { width:42px;height:42px;border-radius:12px;flex-shrink:0;display:flex;align-items:center;justify-content:center; }
.stat-val  { font-size:22px;font-weight:800;color:#1e293b;line-height:1; }
.stat-lbl  { font-size:10px;font-weight:600;color:#94a3b8;text-transform:uppercase;letter-spacing:.6px;margin-top:3px; }

/* General average banner */
.ga-banner { border-radius:16px;padding:24px 28px;margin-top:4px;display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:16px; }

/* Pending notice */
.pending-notice { border-radius:14px;padding:14px 18px;display:flex;align-items:center;gap:10px;background:#fffbeb;border:1.5px solid #fde68a;color:#92400e;font-size:12px;font-weight:600;margin-top:4px; }
</style>

<div class="bls-wrap">


<?php
    $backParams = array_filter([
        'groupe_id'  => $groupeId,
        'filiere_id' => $filiereFilter ?? null,
        'promo'      => $promoFilter ?? null,
    ]);
?>
<div class="breadcrumb">
    <a href="<?php echo e(route('bulletin.index')); ?>">Bulletins</a>
    <span style="color:#cbd5e1;">›</span>
    <?php if($groupeId): ?>
    <a href="<?php echo e(route('bulletin.index', $backParams)); ?>"><?php echo e($groupe?->name ?? 'Groupe'); ?></a>
    <span style="color:#cbd5e1;">›</span>
    <?php endif; ?>
    <span style="color:#1e293b;font-weight:600;"><?php echo e($stagiaire->name); ?></span>
</div>


<div class="profile-card">
    <div class="profile-avatar"><?php echo e(strtoupper(substr($stagiaire->name, 0, 1))); ?></div>
    <div style="flex:1;">
        <div style="font-size:16px;font-weight:800;color:#0f172a;"><?php echo e($stagiaire->name); ?></div>
        <div style="font-size:12px;color:#64748b;margin-top:2px;">
            <?php echo e($stagiaire->email); ?>

            <?php if($groupe): ?>
                · Groupe <strong><?php echo e($groupe->name); ?></strong>
                <?php if($groupe->filiere): ?> · <?php echo e($groupe->filiere->name); ?> <?php endif; ?>
            <?php endif; ?>
        </div>
    </div>
    <?php if($groupeId): ?>
    <a href="<?php echo e(route('bulletin.index', $backParams)); ?>"
       style="display:inline-flex;align-items:center;gap:6px;padding:8px 14px;font-size:11px;font-weight:700;border-radius:10px;background:white;border:1.5px solid #e2e8f0;color:#475569;text-decoration:none;">
        ← Retour au groupe
    </a>
    <?php endif; ?>
</div>

<?php if(! $groupe): ?>
    <div style="padding:64px;text-align:center;background:white;border-radius:16px;border:1px solid #e2e8f0;color:#94a3b8;">
        Ce stagiaire n'est affecté à aucun groupe.
    </div>

<?php elseif($modulesWithNotes->isEmpty()): ?>
    <div style="padding:64px;text-align:center;background:white;border-radius:16px;border:1px solid #e2e8f0;color:#94a3b8;">
        Aucun module trouvé pour ce groupe.
    </div>

<?php else: ?>


<?php
    $totalModules = $modulesWithNotes->count();
    $notedModules = $modulesWithNotes->filter(fn($m) => $m['moduleGrade'] !== null)->count();
    $gaColor = $generalAverage === null ? '#94a3b8' : ($generalAverage >= 10 ? '#16a34a' : '#dc2626');
    $gaBg    = $generalAverage === null ? '#f8fafc'  : ($generalAverage >= 10 ? '#f0fdf4'  : '#fff1f2');

    // Discipline stats
    $discClass = $disciplineNote === null ? 'disc-high'
        : ($disciplineNote >= 15 ? 'disc-high' : ($disciplineNote >= 10 ? 'disc-mid' : 'disc-low'));
?>

<div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(160px,1fr));gap:12px;margin-bottom:22px;">
    <div class="stat-card">
        <div class="stat-icon" style="background:<?php echo e($light); ?>;">
            <svg width="20" height="20" fill="none" stroke="<?php echo e($accent); ?>" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
        </div>
        <div><div class="stat-val"><?php echo e($totalModules); ?></div><div class="stat-lbl">Modules</div></div>
    </div>
    <div class="stat-card">
        <div class="stat-icon" style="background:#eff6ff;">
            <svg width="20" height="20" fill="none" stroke="#1d4ed8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
        </div>
        <div><div class="stat-val"><?php echo e($notedModules); ?>/<?php echo e($totalModules); ?></div><div class="stat-lbl">Complétés</div></div>
    </div>
    <div class="stat-card" style="background:<?php echo e($gaBg); ?>;border-color:<?php echo e($generalAverage !== null && $generalAverage >= 10 ? '#bbf7d0' : ($generalAverage !== null ? '#fecdd3' : '#e2e8f0')); ?>;">
        <div class="stat-icon" style="background:white;">
            <svg width="20" height="20" fill="none" stroke="<?php echo e($gaColor); ?>" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/></svg>
        </div>
        <div>
            <div class="stat-val" style="color:<?php echo e($gaColor); ?>;">
                <?php echo e($generalAverage !== null ? number_format($generalAverage, 2) : '—'); ?>

            </div>
            <div class="stat-lbl">Moy. générale</div>
        </div>
    </div>

    
    <?php if($disciplineNote !== null): ?>
    <div class="stat-card" style="background:#fefce8;border-color:#fde047;">
        <div class="stat-icon" style="background:white;font-size:18px;">🎓</div>
        <div>
            <div class="stat-val" style="color:#713f12;">
                <?php echo e(number_format($disciplineNote, 2)); ?>

            </div>
            <div class="stat-lbl">Discipline</div>
        </div>
    </div>
    <?php endif; ?>
</div>


<div class="bl-table-wrap">

    
    <div style="padding:14px 20px;border-bottom:1px solid #f1f5f9;display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:8px;background:#fafafa;">
        <div style="font-size:13px;font-weight:800;color:#0f172a;">Relevé de notes</div>
        <div style="display:flex;align-items:center;gap:12px;font-size:10px;color:#64748b;">
            <span style="display:flex;align-items:center;gap:4px;"><span style="width:8px;height:8px;border-radius:50%;background:#16a34a;display:inline-block;"></span>≥ 15</span>
            <span style="display:flex;align-items:center;gap:4px;"><span style="width:8px;height:8px;border-radius:50%;background:#d97706;display:inline-block;"></span>10 – 14</span>
            <span style="display:flex;align-items:center;gap:4px;"><span style="width:8px;height:8px;border-radius:50%;background:#dc2626;display:inline-block;"></span>< 10</span>
            <span style="display:flex;align-items:center;gap:4px;"><span style="width:8px;height:8px;border-radius:50%;background:#eab308;display:inline-block;"></span>Discipline</span>
        </div>
    </div>

    <div style="overflow-x:auto;">
        <table class="bl-table">
            <thead>
                <tr>
                    <th class="col-module">Module</th>
                    <th>Type</th>
                    <th>Coeff.</th>

                    
<?php
    $maxControles = $modulesWithNotes->max(fn($m) => (int) ($m['module']->nbr_controles ?? 1));
?>
                    <?php for($i = 1; $i <= $maxControles; $i++): ?>
                        <th>C<?php echo e($i); ?><br><span style="font-size:8px;color:#cbd5e1;">/ 20</span></th>
                    <?php endfor; ?>

                    <th style="color:#7e22ce;">CC<br><span style="font-size:8px;color:#cbd5e1;">/ 20</span></th>
                    <th style="color:#dc2626;">⚑ EFM<br><span style="font-size:8px;color:#fca5a5;">/ 20</span></th>
                    <th style="color:#7c3aed;">Note module</th>
                </tr>
            </thead>
            <tbody>

            
            <?php $__currentLoopData = $modulesWithNotes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <?php
                $module      = $item['module'];
                $controles   = $item['controles'];
                $efm         = $item['efm'];
                $notes       = $item['notes'];
                $cc          = $item['cc'];
                $efmDisplay  = $item['efmDisplay'];
                $moduleGrade = $item['moduleGrade'];

                $mgClass = $moduleGrade === null ? 'moy-none'
                    : ($moduleGrade >= 15 ? 'moy-high' : ($moduleGrade >= 10 ? 'moy-mid' : 'moy-low'));
                $ccClass = $cc === null ? 'cc-empty'
                    : ($cc >= 15 ? 'cc-high' : ($cc >= 10 ? 'cc-mid' : 'cc-low'));
            ?>
            <tr>
                
                <td class="col-module-cell">
                    <div style="font-weight:700;color:#1e293b;font-size:12px;"><?php echo e($module->name); ?></div>
                    <?php if($module->formateur): ?>
                    <div style="font-size:10px;color:#94a3b8;margin-top:1px;"><?php echo e($module->formateur->name); ?></div>
                    <?php endif; ?>
                </td>

                
                <td>
                    <span class="type-badge type-<?php echo e($module->type); ?>">
                        <?php echo e($module->type === 'regional' ? 'Rég.' : 'Loc.'); ?>

                    </span>
                </td>

                
                <td>
                    <span class="info-chip" style="font-size:11px;font-weight:800;"><?php echo e($module->coefficience); ?></span>
                </td>

                
                <?php for($i = 1; $i <= $maxControles; $i++): ?>
                <?php
                    $ctrl = $controles->get($i - 1);
                    $val  = $ctrl ? ($notes[$ctrl->id] ?? null) : null;
                    $cls  = $val !== null
                        ? ($val >= 15 ? 'note-high' : ($val >= 10 ? 'note-mid' : 'note-low'))
                        : 'note-empty';
                ?>
                <td>
                    <?php if($ctrl): ?>
                        <span class="note-pill <?php echo e($cls); ?>"><?php echo e($val !== null ? number_format((float)$val, 2) : '—'); ?></span>
                    <?php else: ?>
                        <span style="color:#e2e8f0;font-size:11px;">—</span>
                    <?php endif; ?>
                </td>
                <?php endfor; ?>

                
                <td>
                    <span class="cc-pill <?php echo e($ccClass); ?>"><?php echo e($cc !== null ? number_format($cc, 2) : '—'); ?></span>
                </td>

                
                <?php
                    $eCls = $efmDisplay !== null
                        ? ($efmDisplay >= 15 ? 'note-high' : ($efmDisplay >= 10 ? 'note-mid' : 'note-low'))
                        : 'note-empty';
                ?>
                <td>
                    <span class="note-pill <?php echo e($eCls); ?>"><?php echo e($efmDisplay !== null ? number_format($efmDisplay, 2) : '—'); ?></span>
                </td>

                
                <td>
                    <span class="moy-badge <?php echo e($mgClass); ?>">
                        <?php echo e($moduleGrade !== null ? number_format($moduleGrade, 2) : '—'); ?>

                    </span>
                </td>
            </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

            
            <?php if($disciplineNote !== null): ?>
            <?php
                $discBadgeClass = $disciplineNote >= 15 ? 'disc-high'
                    : ($disciplineNote >= 10 ? 'disc-mid' : 'disc-low');
            ?>
            <tr class="discipline-row">

                
                <td class="col-module-cell">
                    <div style="display:flex;align-items:center;gap:8px;">
                        <span style="font-size:16px;">🎓</span>
                        <div>
                            <div style="font-weight:800;color:#713f12;font-size:12px;">Discipline</div>
                            <div style="font-size:10px;color:#92400e;margin-top:1px;">
                                Absences non justifiées · pénalité 1pt / 5h
                            </div>
                        </div>
                    </div>
                </td>

                
                <td>
                    <span style="font-size:10px;font-weight:700;padding:2px 7px;border-radius:99px;
                                 background:#fef9c3;color:#713f12;border:1px solid #fde047;">
                        Conduite
                    </span>
                </td>

                
                <td>
                    <span class="info-chip" style="font-size:11px;font-weight:800;background:#fef9c3;color:#713f12;border-color:#fde047;">1</span>
                </td>

                
                <?php for($i = 1; $i <= $maxControles; $i++): ?>
                <td><span style="color:#e2e8f0;font-size:11px;">—</span></td>
                <?php endfor; ?>

                
                <td><span style="color:#e2e8f0;font-size:11px;">—</span></td>

                
                <td><span style="color:#e2e8f0;font-size:11px;">—</span></td>

                
                <td>
                    <span class="disc-badge <?php echo e($discBadgeClass); ?>">
                        <?php echo e(number_format($disciplineNote, 2)); ?>

                    </span>
                </td>
            </tr>
            <?php endif; ?>
            

            </tbody>
        </table>
    </div>
</div>


<?php if($allGraded): ?>
<?php
    $bannerBg  = $generalAverage >= 10 ? '#f0fdf4' : '#fff1f2';
    $bannerBdr = $generalAverage >= 10 ? '#bbf7d0' : '#fecdd3';
    $bannerClr = $generalAverage >= 10 ? '#15803d' : '#be123c';
?>
<div class="ga-banner" style="background:<?php echo e($bannerBg); ?>;border:2px solid <?php echo e($bannerBdr); ?>;">
    <div>
        <div style="font-size:14px;font-weight:800;color:#0f172a;">Moyenne générale</div>
        <div style="font-size:11px;color:#64748b;margin-top:3px;">
            <?php echo e($totalModules); ?> module<?php echo e($totalModules > 1 ? 's' : ''); ?> + discipline · pondérée par coefficient
            <?php if($groupe): ?> · <?php echo e($groupe->name); ?> <?php endif; ?>
        </div>
        <?php if($disciplineNote !== null): ?>
        <div style="font-size:10px;color:#92400e;margin-top:4px;
                    display:inline-flex;align-items:center;gap:4px;
                    background:#fef9c3;padding:2px 8px;border-radius:6px;border:1px solid #fde047;">
            🎓 Discipline : <?php echo e(number_format($disciplineNote, 2)); ?> / 20 (coeff. 1)
        </div>
        <?php endif; ?>
    </div>
    <div style="font-size:38px;font-weight:800;color:<?php echo e($bannerClr); ?>;">
        <?php echo e(number_format($generalAverage, 2)); ?> <span style="font-size:18px;font-weight:600;opacity:.6;">/ 20</span>
    </div>
</div>

<?php else: ?>

<div class="pending-notice">
    <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
    Moyenne générale non disponible —
    <?php echo e($totalModules - $notedModules); ?> module<?php echo e(($totalModules - $notedModules) > 1 ? 's' : ''); ?>

    en attente d'EFM
    (<?php echo e($notedModules); ?>/<?php echo e($totalModules); ?> complets)
</div>
<?php endif; ?>

<?php endif; ?>

</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\Project\gestion-CF\resources\views/bulletin/show.blade.php ENDPATH**/ ?>