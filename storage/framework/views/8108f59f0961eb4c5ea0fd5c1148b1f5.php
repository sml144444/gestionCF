
<?php $__env->startSection('title', 'Filières'); ?>
<?php $__env->startSection('page-title', 'Filières'); ?>

<?php $__env->startSection('content'); ?>

<?php
    $user     = Auth::user();
    $userRole = $user->role;

    $palettes = [
        'admin'        => [
            'primary'   => '#0a6640',
            'dark'      => '#065f38',
            'light'     => '#e8f5ee',
            'border'    => 'rgba(10,102,64,.18)',
            'shadow'    => 'rgba(10,102,64,.3)',
            'pill_bg'   => '#e8f5ee',
            'pill_text' => '#065f38',
            'pill_cnt'  => '#0a6640',
            'yr2_bg'    => '#fdf4ff', 'yr2_text' => '#6b21a8', 'yr2_cnt' => '#7e22ce', 'yr2_lbl' => '#6b21a8',
            'yr3_bg'    => '#fff7ed', 'yr3_text' => '#c2410c', 'yr3_cnt' => '#c2410c', 'yr3_lbl' => '#c2410c',
        ],
        'gestionnaire' => [
            'primary'   => '#1e293b',
            'dark'      => '#0f172a',
            'light'     => '#f1f5f9',
            'border'    => 'rgba(30,41,59,.18)',
            'shadow'    => 'rgba(30,41,59,.3)',
            'pill_bg'   => '#f1f5f9',
            'pill_text' => '#1e293b',
            'pill_cnt'  => '#334155',
            'yr2_bg'    => '#fdf4ff', 'yr2_text' => '#6b21a8', 'yr2_cnt' => '#7e22ce', 'yr2_lbl' => '#6b21a8',
            'yr3_bg'    => '#fff7ed', 'yr3_text' => '#c2410c', 'yr3_cnt' => '#c2410c', 'yr3_lbl' => '#c2410c',
        ],
        'formateur'    => [
            'primary'   => '#1a4f8a',
            'dark'      => '#1e40af',
            'light'     => '#eff6ff',
            'border'    => 'rgba(26,79,138,.18)',
            'shadow'    => 'rgba(26,79,138,.3)',
            'pill_bg'   => '#eff6ff',
            'pill_text' => '#1e40af',
            'pill_cnt'  => '#1a4f8a',
            'yr2_bg'    => '#fdf4ff', 'yr2_text' => '#6b21a8', 'yr2_cnt' => '#7e22ce', 'yr2_lbl' => '#6b21a8',
            'yr3_bg'    => '#fff7ed', 'yr3_text' => '#c2410c', 'yr3_cnt' => '#c2410c', 'yr3_lbl' => '#c2410c',
        ],
        'stagiaire'    => [
            'primary'   => '#ea580c',
            'dark'      => '#9a3412',
            'light'     => '#fff7ed',
            'border'    => 'rgba(234,88,12,.18)',
            'shadow'    => 'rgba(234,88,12,.3)',
            'pill_bg'   => '#fff7ed',
            'pill_text' => '#9a3412',
            'pill_cnt'  => '#ea580c',
            'yr2_bg'    => '#fdf4ff', 'yr2_text' => '#6b21a8', 'yr2_cnt' => '#7e22ce', 'yr2_lbl' => '#6b21a8',
            'yr3_bg'    => '#f0fdfa', 'yr3_text' => '#0f766e', 'yr3_cnt' => '#0f766e', 'yr3_lbl' => '#0f766e',
        ],
    ];

    $p = $palettes[$userRole] ?? $palettes['gestionnaire'];

    $canCreate = $user->can('groupe-create');
    $canEdit   = $user->can('groupe-edit');
    $canDelete = $user->can('groupe-delete');
?>

<style>
:root {
    --primary:       <?php echo e($p['primary']); ?>;
    --primary-dark:  <?php echo e($p['dark']); ?>;
    --primary-light: <?php echo e($p['light']); ?>;
    --primary-border:<?php echo e($p['border']); ?>;
    --primary-shadow:<?php echo e($p['shadow']); ?>;
    --warning: #f59e0b; --warning-light: #fef3c7;
    --danger:  #dc2626; --danger-light:  #fee2e2;
    --success: #15803d; --success-light: #f0fdf4;
    --gray-50: #f8fafc; --gray-100: #f1f5f9; --gray-200: #e2e8f0;
    --gray-400: #94a3b8; --gray-500: #64748b; --gray-800: #1e293b; --gray-900: #0f172a;
}
.fil-wrap { font-family: 'Segoe UI', system-ui, sans-serif; }
.filiere-card {
    background: white; border-radius: 16px; border: 1px solid var(--gray-200);
    overflow: hidden; margin-bottom: 20px; transition: all 0.2s;
}
.filiere-card:hover { box-shadow: 0 4px 20px rgba(0,0,0,.08); transform: translateY(-1px); }
.filiere-header {
    padding: 18px 24px; background: var(--primary-light);
    border-bottom: 3px solid var(--primary);
    display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 16px;
}
.stat-block { display: flex; border-radius: 12px; overflow: hidden; border: 1px solid var(--gray-200); background: white; }
.stat-item  { display: flex; flex-direction: column; align-items: center; padding: 10px 18px; min-width: 72px; }
.stat-item + .stat-item { border-left: 1px solid var(--gray-200); }
.stat-val { font-size: 20px; font-weight: 800; color: var(--gray-900); line-height: 1; }
.stat-lbl { font-size: 9px; font-weight: 700; color: var(--gray-400); text-transform: uppercase; letter-spacing: .8px; margin-top: 3px; }
.code-badge {
    display: inline-flex; align-items: center; gap: 4px;
    font-size: 10px; font-weight: 800; letter-spacing: .5px;
    font-family: 'Courier New', monospace;
    padding: 3px 9px; border-radius: 6px;
    background: var(--gray-100); color: var(--gray-800); border: 1px solid var(--gray-200);
}
.code-badge.empty { color: var(--gray-400); font-style: italic; font-weight: 400; font-family: inherit; }
.btn-act {
    display: inline-flex; align-items: center; gap: 6px; padding: 7px 14px;
    font-size: 12px; font-weight: 700; border-radius: 10px; border: none;
    cursor: pointer; transition: opacity .15s; text-decoration: none;
}
.btn-act:hover { opacity: .88; }
.btn-primary { background: var(--primary); color: white; box-shadow: 0 4px 12px var(--primary-shadow); }
.btn-warning { background: var(--warning-light); color: #92400e; border: 1px solid #fde68a; }
.btn-danger  { background: var(--danger-light);  color: var(--danger); border: 1px solid #fecaca; }
.btn-ghost   { background: white; color: var(--gray-500); border: 1.5px solid var(--gray-200); }
.modal-overlay { display:none; position:fixed; inset:0; z-index:1000; background:rgba(15,23,42,.5); backdrop-filter:blur(4px); align-items:center; justify-content:center; }
.modal-overlay.active { display:flex; animation: fIn .18s ease; }
@keyframes fIn { from{opacity:0}to{opacity:1} }
.modal-box { background:white; border-radius:20px; width:100%; max-width:520px; margin:20px; box-shadow:0 24px 60px rgba(0,0,0,.18); animation:sUp .2s ease; }
@keyframes sUp { from{opacity:0;transform:translateY(16px)}to{opacity:1;transform:none} }
.modal-hd { padding:18px 24px; display:flex; align-items:center; justify-content:space-between; border-bottom:2px solid var(--primary); }
.modal-hd.edit   { border-bottom-color:var(--warning); }
.modal-hd.delete { border-bottom-color:var(--danger);  }
.modal-hd h3 { font-size:15px; font-weight:800; color:var(--gray-900); margin:0; }
.modal-hd p  { font-size:10px; color:var(--gray-500); margin:3px 0 0; }
.modal-close { width:30px; height:30px; border-radius:8px; border:none; background:var(--gray-100); color:var(--gray-500); font-size:18px; cursor:pointer; display:flex; align-items:center; justify-content:center; }
.modal-close:hover { background:var(--gray-200); }
.modal-bd { padding:20px 24px; }
.modal-ft { padding:14px 24px; border-top:1px solid var(--gray-100); display:flex; gap:10px; }
.f-label { display:block; font-size:9px; font-weight:800; color:var(--gray-400); letter-spacing:1.5px; text-transform:uppercase; margin-bottom:7px; }
.f-input { width:100%; height:42px; padding:0 12px; border-radius:10px; border:1.5px solid var(--gray-200); background:var(--gray-50); font-size:13px; color:var(--gray-800); outline:none; transition:all .15s; box-sizing:border-box; }
.f-input:focus { border-color:var(--primary); background:white; box-shadow:0 0 0 3px color-mix(in srgb, var(--primary) 10%, transparent); }
.f-input.edit-focus:focus { border-color:var(--warning); box-shadow:0 0 0 3px rgba(245,158,11,.1); }
.f-hint { font-size:9px; color:var(--gray-400); margin-top:5px; line-height:1.5; }
.f-code-hint { margin-top:6px; padding:7px 10px; border-radius:8px; background:#fffbeb; border:1px solid #fde68a; font-size:9px; color:#92400e; line-height:1.6; }
.f-grid { display:grid; grid-template-columns:1fr 1fr; gap:12px; }
.f-row  { margin-bottom:14px; }
.flash { margin-bottom:16px; padding:12px 16px; border-radius:12px; font-size:13px; display:flex; align-items:center; gap:8px; }
.flash-ok  { background:var(--success-light); border:1px solid #bbf7d0; color:var(--success); }
.flash-err { background:var(--danger-light);  border:1px solid #fecdd3; color:#be123c; }
</style>

<div class="fil-wrap">

<?php if(session('success')): ?>
    <div class="flash flash-ok">
        <svg width="16" height="16" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
        <?php echo e(session('success')); ?>

    </div>
<?php endif; ?>
<?php if(session('error')): ?>
    <div class="flash flash-err">
        <svg width="16" height="16" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm-1-5h2v2h-2zm0-8h2v6h-2z" clip-rule="evenodd"/></svg>
        <?php echo e(session('error')); ?>

    </div>
<?php endif; ?>
<?php if($errors->any()): ?>
    <div class="flash flash-err"><ul style="margin:0;padding-left:16px;"><?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $e): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><li><?php echo e($e); ?></li><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?></ul></div>
<?php endif; ?>

<div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:16px;margin-bottom:24px;">
    <div>
        <h1 style="font-size:22px;font-weight:800;color:var(--gray-900);margin:0;">Filières</h1>
        <p style="font-size:12px;color:var(--gray-500);margin:4px 0 0;">
            <?php echo e($filieres->count()); ?> filière<?php echo e($filieres->count()>1?'s':''); ?> —
            <a href="<?php echo e(route('groupes.index')); ?>" style="color:var(--primary);font-weight:600;text-decoration:none;">Gérer les groupes →</a>
        </p>
    </div>
    <?php if($canCreate): ?>
        <button onclick="openModal('create')" class="btn-act btn-primary">
            <svg width="13" height="13" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
            Nouvelle filière
        </button>
    <?php endif; ?>
</div>

<?php $__empty_1 = true; $__currentLoopData = $filieres; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $filiere): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
    <?php
        $groupes1        = $filiere->groupes->where('annee', 1);
        $groupes2        = $filiere->groupes->where('annee', 2);
        $groupes3        = $filiere->groupes->where('annee', 3);
        $totalStagiaires = $filiere->stagiaires_count ?? 0;
        $totalPlaces     = $filiere->groupes->sum('nbr_limit');
    ?>
    <div class="filiere-card">
        <div class="filiere-header">
            <div style="display:flex;align-items:center;gap:14px;">
                <div style="width:46px;height:46px;border-radius:12px;background:var(--primary);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                    <svg width="22" height="22" fill="none" stroke="white" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/></svg>
                </div>
                <div>
                    <div style="display:flex;align-items:center;gap:8px;flex-wrap:wrap;">
                        <span style="font-size:17px;font-weight:800;color:var(--primary-dark);"><?php echo e($filiere->name); ?></span>
                        <?php if($filiere->code): ?>
                            <span class="code-badge" title="Code import EDU">
                                <svg width="9" height="9" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"/></svg>
                                <?php echo e($filiere->code); ?>

                            </span>
                        <?php else: ?>
                            <span class="code-badge empty">sans code</span>
                        <?php endif; ?>
                    </div>
                    <div style="font-size:11px;color:var(--primary-dark);opacity:.75;margin-top:3px;">
                        Formation de <?php echo e($filiere->duree); ?> an<?php echo e($filiere->duree>1?'s':''); ?>

                    </div>
                </div>
            </div>

            <div class="stat-block">
                <div class="stat-item"><div class="stat-val"><?php echo e($filiere->groupes_count); ?></div><div class="stat-lbl">Groupes</div></div>
                <div class="stat-item"><div class="stat-val"><?php echo e($totalStagiaires); ?></div><div class="stat-lbl">Stagiaires</div></div>
                <div class="stat-item"><div class="stat-val"><?php echo e($filiere->modules_count); ?></div><div class="stat-lbl">Modules</div></div>
            </div>

            <?php if($canEdit || $canDelete): ?>
            <div style="display:flex;gap:8px;">
                <?php if($canEdit): ?>
                <button class="btn-act btn-warning"
                        onclick="openEditModal(<?php echo e($filiere->id); ?>, '<?php echo e(addslashes($filiere->name)); ?>', '<?php echo e(addslashes($filiere->code ?? '')); ?>', <?php echo e($filiere->duree); ?>)">
                    ✎ Modifier
                </button>
                <?php endif; ?>
                <?php if($canDelete): ?>
                <button class="btn-act btn-danger"
                        onclick="openDeleteModal('<?php echo e(route('filieres.destroy', $filiere)); ?>', '<?php echo e(addslashes($filiere->name)); ?>', <?php echo e($filiere->groupes_count); ?>)">
                    ✕
                </button>
                <?php endif; ?>
            </div>
            <?php endif; ?>
        </div>

        <div style="padding:18px 24px;">
            <?php if($filiere->groupes->isEmpty()): ?>
                <div style="padding:20px;text-align:center;background:var(--gray-50);border-radius:10px;border:1.5px dashed var(--gray-200);">
                    <p style="font-size:12px;color:var(--gray-400);margin:0;">Aucun groupe — <a href="<?php echo e(route('groupes.index', ['filiere'=>$filiere->id])); ?>" style="color:var(--primary);font-weight:600;">en créer un</a></p>
                </div>
            <?php else: ?>
                
                <?php if($groupes1->isNotEmpty()): ?>
                    <div style="font-size:9px;font-weight:800;color:var(--primary-dark);letter-spacing:1.5px;text-transform:uppercase;margin-bottom:8px;">1ère année</div>
                    <div style="display:flex;flex-wrap:wrap;gap:8px;margin-bottom:14px;">
                        <?php $__currentLoopData = $groupes1; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $g): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <div style="display:inline-flex;align-items:center;gap:6px;font-size:11px;font-weight:600;padding:5px 11px;border-radius:8px;background:<?php echo e($p['pill_bg']); ?>;color:<?php echo e($p['pill_text']); ?>;border:1px solid var(--primary-border);">
                                <?php if($g->code): ?><span style="font-family:monospace;font-size:9px;font-weight:800;background:<?php echo e($p['pill_cnt']); ?>;color:white;padding:1px 5px;border-radius:4px;"><?php echo e($g->code); ?></span><?php endif; ?>
                                <?php echo e($g->name); ?>

                                <span style="font-size:9px;background:<?php echo e($p['pill_cnt']); ?>;color:white;padding:1px 6px;border-radius:99px;font-weight:700;"><?php echo e($g->stagiaires_count); ?>/<?php echo e($g->nbr_limit); ?></span>
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                <?php endif; ?>

                
                <?php if($groupes2->isNotEmpty()): ?>
                    <div style="font-size:9px;font-weight:800;color:<?php echo e($p['yr2_lbl']); ?>;letter-spacing:1.5px;text-transform:uppercase;margin-bottom:8px;">2ème année</div>
                    <div style="display:flex;flex-wrap:wrap;gap:8px;margin-bottom:14px;">
                        <?php $__currentLoopData = $groupes2; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $g): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <div style="display:inline-flex;align-items:center;gap:6px;font-size:11px;font-weight:600;padding:5px 11px;border-radius:8px;background:<?php echo e($p['yr2_bg']); ?>;color:<?php echo e($p['yr2_text']); ?>;border:1px solid rgba(107,33,168,.18);">
                                <?php if($g->code): ?><span style="font-family:monospace;font-size:9px;font-weight:800;background:<?php echo e($p['yr2_cnt']); ?>;color:white;padding:1px 5px;border-radius:4px;"><?php echo e($g->code); ?></span><?php endif; ?>
                                <?php echo e($g->name); ?>

                                <span style="font-size:9px;background:<?php echo e($p['yr2_cnt']); ?>;color:white;padding:1px 6px;border-radius:99px;font-weight:700;"><?php echo e($g->stagiaires_count); ?>/<?php echo e($g->nbr_limit); ?></span>
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                <?php endif; ?>

                
                <?php if($groupes3->isNotEmpty()): ?>
                    <div style="font-size:9px;font-weight:800;color:<?php echo e($p['yr3_lbl']); ?>;letter-spacing:1.5px;text-transform:uppercase;margin-bottom:8px;">3ème année</div>
                    <div style="display:flex;flex-wrap:wrap;gap:8px;">
                        <?php $__currentLoopData = $groupes3; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $g): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <div style="display:inline-flex;align-items:center;gap:6px;font-size:11px;font-weight:600;padding:5px 11px;border-radius:8px;background:<?php echo e($p['yr3_bg']); ?>;color:<?php echo e($p['yr3_text']); ?>;border:1px solid rgba(194,65,12,.18);">
                                <?php if($g->code): ?><span style="font-family:monospace;font-size:9px;font-weight:800;background:<?php echo e($p['yr3_cnt']); ?>;color:white;padding:1px 5px;border-radius:4px;"><?php echo e($g->code); ?></span><?php endif; ?>
                                <?php echo e($g->name); ?>

                                <span style="font-size:9px;background:<?php echo e($p['yr3_cnt']); ?>;color:white;padding:1px 6px;border-radius:99px;font-weight:700;"><?php echo e($g->stagiaires_count); ?>/<?php echo e($g->nbr_limit); ?></span>
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                <?php endif; ?>
            <?php endif; ?>
        </div>

        <div style="padding:11px 24px;border-top:1px solid var(--gray-100);background:#fafbfc;display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:10px;">
            <span style="font-size:10px;color:var(--gray-500);">Capacité totale : <?php echo e($totalPlaces); ?> places</span>
            <a href="<?php echo e(route('groupes.index', ['filiere'=>$filiere->id])); ?>"
               style="font-size:11px;font-weight:600;color:var(--primary);text-decoration:none;display:inline-flex;align-items:center;gap:4px;">
                Gérer les groupes
                <svg width="10" height="10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
            </a>
        </div>
    </div>
<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
    <div style="padding:64px;text-align:center;background:white;border-radius:16px;border:1px solid var(--gray-200);">
        <div style="font-size:48px;margin-bottom:12px;">🏫</div>
        <p style="font-size:14px;color:var(--gray-500);margin:0 0 16px;">Aucune filière créée.</p>
        <?php if($canCreate): ?><button onclick="openModal('create')" class="btn-act btn-primary">Créer la première filière</button><?php endif; ?>
    </div>
<?php endif; ?>

</div>


<?php if($canCreate): ?>
<div id="modal-create" class="modal-overlay" onclick="if(event.target===this)closeModal('create')">
    <div class="modal-box">
        <div class="modal-hd">
            <div><h3>Nouvelle filière</h3><p>Renseignez le nom, le code et la durée</p></div>
            <button class="modal-close" onclick="closeModal('create')">×</button>
        </div>
        <form method="POST" action="<?php echo e(route('filieres.store')); ?>">
            <?php echo csrf_field(); ?>
            <div class="modal-bd">
                <div class="f-grid f-row">
                    <div>
                        <label class="f-label">Nom <span style="color:#ef4444;">*</span></label>
                        <input type="text" name="name" class="f-input" required placeholder="Développement Digital…"
                               value="<?php echo e(old('name')); ?>" oninput="autoCode(this)">
                    </div>
                    <div>
                        <label class="f-label">
                            Code filière <span style="color:#ef4444;">*</span>
                            <span style="font-size:8px;font-weight:400;text-transform:none;letter-spacing:0;color:var(--gray-500);margin-left:2px;">(EDU import)</span>
                        </label>
                        <input type="text" name="code" id="create-fil-code" class="f-input" required
                               placeholder="DEVDIG" maxlength="20" value="<?php echo e(old('code')); ?>"
                               style="font-family:monospace;font-weight:700;letter-spacing:.5px;text-transform:uppercase;"
                               oninput="this.value=this.value.toUpperCase().replace(/[^A-Z0-9\-_]/g,'')">
                        <div class="f-code-hint">
                            ⚠️ Utilisé dans l'import EDU — unique et immuable après import.<br>
                            Lettres majuscules, chiffres, tirets. Ex : <strong>DEVDIG</strong>, <strong>GI</strong>
                        </div>
                    </div>
                </div>
                <div class="f-row" style="margin-bottom:0;">
                    <label class="f-label">Durée <span style="color:#ef4444;">*</span></label>
                    <select name="duree" class="f-input" required style="cursor:pointer;">
                        <option value="">— Sélectionner —</option>
                        <option value="1" <?php echo e(old('duree')==1?'selected':''); ?>>1 an</option>
                        <option value="2" <?php echo e(old('duree',2)==2?'selected':''); ?>>2 ans</option>
                        <option value="3" <?php echo e(old('duree')==3?'selected':''); ?>>3 ans</option>
                    </select>
                </div>
            </div>
            <div class="modal-ft">
                <button type="button" onclick="closeModal('create')" class="btn-act btn-ghost" style="flex:1;justify-content:center;">Annuler</button>
                <button type="submit" class="btn-act btn-primary" style="flex:1;justify-content:center;">
                    <svg width="13" height="13" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                    Créer
                </button>
            </div>
        </form>
    </div>
</div>
<?php endif; ?>


<?php if($canEdit): ?>
<div id="modal-edit" class="modal-overlay" onclick="if(event.target===this)closeModal('edit')">
    <div class="modal-box">
        <div class="modal-hd edit">
            <div><h3>Modifier la filière</h3><p id="edit-subtitle"></p></div>
            <button class="modal-close" onclick="closeModal('edit')">×</button>
        </div>
        <form id="edit-form" method="POST">
            <?php echo csrf_field(); ?> <?php echo method_field('PATCH'); ?>
            <div class="modal-bd">
                <div class="f-grid f-row">
                    <div>
                        <label class="f-label">Nom <span style="color:#ef4444;">*</span></label>
                        <input type="text" name="name" id="edit-name" class="f-input edit-focus" required>
                    </div>
                    <div>
                        <label class="f-label">Code filière <span style="color:#ef4444;">*</span></label>
                        <input type="text" name="code" id="edit-code" class="f-input edit-focus" required maxlength="20"
                               style="font-family:monospace;font-weight:700;letter-spacing:.5px;text-transform:uppercase;"
                               oninput="this.value=this.value.toUpperCase().replace(/[^A-Z0-9\-_]/g,'')">
                        <div class="f-code-hint">
                            ⚠️ Modifier le code casse la liaison avec les imports EDU existants.
                        </div>
                    </div>
                </div>
                <div class="f-row" style="margin-bottom:0;">
                    <label class="f-label">Durée <span style="color:#ef4444;">*</span></label>
                    <select name="duree" id="edit-duree" class="f-input edit-focus" required style="cursor:pointer;">
                        <option value="1">1 an</option>
                        <option value="2">2 ans</option>
                        <option value="3">3 ans</option>
                    </select>
                </div>
            </div>
            <div class="modal-ft">
                <button type="button" onclick="closeModal('edit')" class="btn-act btn-ghost" style="flex:1;justify-content:center;">Annuler</button>
                <button type="submit" class="btn-act" style="flex:1;justify-content:center;background:var(--warning);color:white;box-shadow:0 4px 12px rgba(245,158,11,.3);">Enregistrer</button>
            </div>
        </form>
    </div>
</div>
<?php endif; ?>


<?php if($canDelete): ?>
<div id="modal-delete" class="modal-overlay" onclick="if(event.target===this)closeModal('delete')">
    <div class="modal-box" style="max-width:420px;">
        <div class="modal-hd delete">
            <div style="display:flex;align-items:center;gap:10px;">
                <div style="width:40px;height:40px;border-radius:12px;background:var(--danger-light);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                    <svg width="18" height="18" fill="none" stroke="var(--danger)" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                </div>
                <div><h3>Supprimer la filière ?</h3><p id="delete-subtitle"></p></div>
            </div>
            <button class="modal-close" onclick="closeModal('delete')">×</button>
        </div>
        <div class="modal-bd">
            <div id="delete-warning" style="padding:12px 14px;border-radius:12px;background:var(--danger-light);border:1px solid #fecdd3;color:#9f1239;font-size:12px;line-height:1.6;"></div>
        </div>
        <div class="modal-ft">
            <button type="button" onclick="closeModal('delete')" class="btn-act btn-ghost" style="flex:1;justify-content:center;">Annuler</button>
            <form id="delete-form" method="POST" style="flex:1;">
                <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                <button type="submit" id="delete-btn" class="btn-act btn-danger" style="width:100%;justify-content:center;">Supprimer</button>
            </form>
        </div>
    </div>
</div>
<?php endif; ?>

<script>
function openModal(t)  { document.getElementById('modal-'+t).classList.add('active');    }
function closeModal(t) { document.getElementById('modal-'+t).classList.remove('active'); }

function openEditModal(id, name, code, duree) {
    document.getElementById('edit-form').action = '/filieres/' + id;
    document.getElementById('edit-name').value  = name;
    document.getElementById('edit-code').value  = code;
    document.getElementById('edit-duree').value = duree;
    document.getElementById('edit-subtitle').textContent = 'Modifiez : ' + name;
    openModal('edit');
}

function openDeleteModal(action, name, groupeCount) {
    document.getElementById('delete-form').action = action;
    document.getElementById('delete-subtitle').textContent = name;
    const btn = document.getElementById('delete-btn');
    const w   = document.getElementById('delete-warning');
    if (groupeCount > 0) {
        w.innerHTML = '⚠️ Impossible de supprimer "<strong>' + name + '</strong>" — '
            + groupeCount + ' groupe(s) existants. Supprimez-les d\'abord.';
        btn.disabled=true; btn.style.opacity='.45'; btn.style.cursor='not-allowed';
    } else {
        w.textContent = 'Cette action est irréversible. Tous les modules associés seront également supprimés.';
        btn.disabled=false; btn.style.opacity='1'; btn.style.cursor='pointer';
    }
    openModal('delete');
}

function autoCode(nameInput) {
    const codeInput = document.getElementById('create-fil-code');
    if (codeInput && !codeInput.dataset.touched) {
        const raw  = nameInput.value.trim().split(/\s+/);
        const code = raw.map(w => w.substring(0,3).toUpperCase().replace(/[^A-Z0-9]/g,'')).join('').substring(0,20);
        codeInput.value = code;
    }
}

document.addEventListener('DOMContentLoaded', () => {
    const c = document.getElementById('create-fil-code');
    if (c) c.addEventListener('input', () => { c.dataset.touched = '1'; });
});

document.addEventListener('keydown', e => {
    if (e.key === 'Escape') document.querySelectorAll('.modal-overlay.active').forEach(m => m.classList.remove('active'));
});
</script>

<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\Project\gestion-CF\resources\views/filieres/index.blade.php ENDPATH**/ ?>