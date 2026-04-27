

<?php $__env->startSection('title', 'Contrôles & Notes'); ?>
<?php $__env->startSection('page-title', 'Contrôles & Notes'); ?>

<?php $__env->startSection('content'); ?>

<?php
    $palettes = [
        'admin'        => ['primary' => '#0a6640', 'light' => '#e8f5ee', 'text' => '#065f38', 'shadow' => 'rgba(10,102,64,0.2)'],
        'gestionnaire' => ['primary' => '#1e293b', 'light' => '#f1f5f9', 'text' => '#1e293b', 'shadow' => 'rgba(30,41,59,0.2)'],
    ];
    $p      = $palettes[Auth::user()->role] ?? $palettes['gestionnaire'];
    $accent = $p['primary'];
    $light  = $p['light'];
    $text   = $p['text'];
    $shadow = $p['shadow'];
?>

<style>
.cn-wrap { font-family:'Segoe UI',system-ui,sans-serif; }

/* ── Stats ── */
.cn-stat { background:white;border-radius:14px;border:1px solid #e2e8f0;padding:16px 20px;display:flex;align-items:center;gap:14px;transition:box-shadow .15s; }
.cn-stat:hover { box-shadow:0 4px 16px rgba(0,0,0,.07); }
.cn-stat-icon { width:42px;height:42px;border-radius:12px;flex-shrink:0;display:flex;align-items:center;justify-content:center; }
.cn-stat-val  { font-size:22px;font-weight:800;color:#1e293b;line-height:1; }
.cn-stat-lbl  { font-size:10px;font-weight:600;color:#94a3b8;text-transform:uppercase;letter-spacing:.6px;margin-top:3px; }

/* ── Filters ── */
.cn-filter { display:flex;gap:10px;flex-wrap:wrap;align-items:center;padding:14px 16px;background:white;border-radius:14px;border:1px solid #e2e8f0;margin-bottom:20px; }
.cn-input  { height:38px;padding:0 12px;border-radius:9px;border:1.5px solid #e2e8f0;background:#f8fafc;font-size:12px;color:#1e293b;outline:none;transition:border-color .15s; }
.cn-input:focus { border-color:<?php echo e($accent); ?>;background:white; }

/* ── Filière header ── */
.cn-fh { display:flex;align-items:center;justify-content:space-between;padding:10px 16px;background:<?php echo e($light); ?>;border-left:4px solid <?php echo e($accent); ?>;border-radius:10px;margin-bottom:10px;margin-top:20px; }

/* ── Table ── */
.cn-table-wrap { background:white;border-radius:14px;border:1px solid #e2e8f0;overflow:hidden;margin-bottom:8px; }
.cn-table { width:100%;border-collapse:collapse;font-size:12px; }
.cn-table th { padding:10px 14px;text-align:left;font-size:9px;font-weight:800;color:#94a3b8;text-transform:uppercase;letter-spacing:1px;background:#f8fafc;border-bottom:1px solid #f1f5f9; }
.cn-table td { padding:11px 14px;border-bottom:1px solid #f8fafc;color:#1e293b;vertical-align:middle; }
.cn-table tr:last-child td { border-bottom:none; }
.cn-table tr:hover td { background:#fafbff; }

/* ── Badges ── */
.cn-badge { display:inline-flex;align-items:center;font-size:9px;font-weight:800;padding:2px 8px;border-radius:99px;text-transform:uppercase;letter-spacing:.5px; }
.cn-badge-regional { background:#eff6ff;color:#1e40af;border:1px solid #bfdbfe; }
.cn-badge-local    { background:#fdf4ff;color:#7e22ce;border:1px solid #e9d5ff; }
.cn-badge-an1 { background:#eff6ff;color:#1e40af;border:1px solid #bfdbfe; }
.cn-badge-an2 { background:#fdf4ff;color:#7e22ce;border:1px solid #e9d5ff; }
.cn-badge-an3 { background:#fff7ed;color:#c2410c;border:1px solid #fed7aa; }

/* ── Buttons ── */
.cn-btn { display:inline-flex;align-items:center;gap:5px;padding:7px 14px;font-size:11px;font-weight:700;border-radius:9px;border:none;cursor:pointer;transition:opacity .15s;text-decoration:none; }
.cn-btn:hover { opacity:.85; }
.cn-btn-primary { background:<?php echo e($accent); ?>;color:white;box-shadow:0 3px 10px <?php echo e($shadow); ?>; }
.cn-btn-ghost   { background:white;border:1.5px solid #e2e8f0;color:#475569; }

/* ── Année tabs ── */
.an-tab { padding:6px 13px;border-radius:99px;font-size:11px;font-weight:600;text-decoration:none;border:1.5px solid #e2e8f0;background:white;color:#64748b;transition:all .15s; }
.an-tab:hover { border-color:<?php echo e($accent); ?>;color:<?php echo e($text); ?>;background:<?php echo e($light); ?>; }
.an-tab.active { background:<?php echo e($accent); ?>;border-color:<?php echo e($accent); ?>;color:white; }

/* ── Controles count pill ── */
.ctrl-pill { display:inline-flex;align-items:center;gap:4px;padding:2px 8px;border-radius:99px;font-size:9px;font-weight:800;background:#f0fdf4;color:#166534;border:1px solid #bbf7d0; }
</style>

<div class="cn-wrap">


<?php if(session('success')): ?>
<div style="margin-bottom:16px;padding:12px 16px;border-radius:12px;font-size:13px;display:flex;align-items:center;gap:8px;background:#f0fdf4;border:1px solid #bbf7d0;color:#15803d;">
    ✓ <?php echo e(session('success')); ?>

</div>
<?php endif; ?>


<div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:12px;margin-bottom:20px;">
    <div>
        <h1 style="font-size:20px;font-weight:800;color:#0f172a;margin:0;">Contrôles & Notes</h1>
        <p style="font-size:12px;color:#64748b;margin:4px 0 0;">
            Saisie et gestion des notes par module et groupe
        </p>
    </div>
</div>


<div style="display:grid;grid-template-columns:repeat(3,1fr);gap:12px;margin-bottom:20px;">
    <div class="cn-stat">
        <div class="cn-stat-icon" style="background:<?php echo e($light); ?>;">
            <svg width="20" height="20" fill="none" stroke="<?php echo e($accent); ?>" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
        </div>
        <div><div class="cn-stat-val"><?php echo e($totalModules); ?></div><div class="cn-stat-lbl">Modules</div></div>
    </div>
    <div class="cn-stat">
        <div class="cn-stat-icon" style="background:#eff6ff;">
            <svg width="20" height="20" fill="none" stroke="#2563eb" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        </div>
        <div><div class="cn-stat-val" style="color:#1e40af;"><?php echo e($totalHeures); ?>h</div><div class="cn-stat-lbl">Volume total</div></div>
    </div>
    <div class="cn-stat">
        <div class="cn-stat-icon" style="background:#f0fdf4;">
            <svg width="20" height="20" fill="none" stroke="#16a34a" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        </div>
        <div>
            <div class="cn-stat-val" style="color:#16a34a;">
                <?php echo e(\App\Models\Note::count()); ?>

            </div>
            <div class="cn-stat-lbl">Notes saisies</div>
        </div>
    </div>
</div>


<div style="display:flex;gap:8px;flex-wrap:wrap;margin-bottom:14px;align-items:center;">
    <span style="font-size:9px;font-weight:800;color:<?php echo e($text); ?>;text-transform:uppercase;letter-spacing:1.5px;">Année :</span>
    <a href="<?php echo e(route('controles.index', array_merge(request()->except('annee','page'), []))); ?>"
       class="an-tab <?php echo e(!$anneeFilter ? 'active' : ''); ?>">Toutes</a>
    <a href="<?php echo e(route('controles.index', array_merge(request()->except('annee','page'), ['annee'=>1]))); ?>"
       class="an-tab <?php echo e($anneeFilter == 1 ? 'active' : ''); ?>">1ère année</a>
    <a href="<?php echo e(route('controles.index', array_merge(request()->except('annee','page'), ['annee'=>2]))); ?>"
       class="an-tab <?php echo e($anneeFilter == 2 ? 'active' : ''); ?>">2ème année</a>
    <a href="<?php echo e(route('controles.index', array_merge(request()->except('annee','page'), ['annee'=>3]))); ?>"
       class="an-tab <?php echo e($anneeFilter == 3 ? 'active' : ''); ?>">3ème année</a>
</div>


<form method="GET" action="<?php echo e(route('controles.index')); ?>" class="cn-filter">
    <?php if($anneeFilter): ?><input type="hidden" name="annee" value="<?php echo e($anneeFilter); ?>"><?php endif; ?>
    <input type="text" name="search" value="<?php echo e($search); ?>" placeholder="Rechercher un module…"
           class="cn-input" style="flex:1;min-width:160px;">
    <select name="filiere" class="cn-input" style="min-width:160px;">
        <option value="">Toutes les filières</option>
        <?php $__currentLoopData = $filieres; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $f): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <option value="<?php echo e($f->id); ?>" <?php echo e($filiereId == $f->id ? 'selected' : ''); ?>><?php echo e($f->name); ?></option>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </select>
    <select name="type" class="cn-input">
        <option value="">Tous les types</option>
        <option value="regional" <?php echo e($typeFilter === 'regional' ? 'selected' : ''); ?>>Régional</option>
        <option value="local"    <?php echo e($typeFilter === 'local'    ? 'selected' : ''); ?>>Local</option>
    </select>
    <button type="submit" class="cn-btn cn-btn-primary">
        <svg width="12" height="12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
        Filtrer
    </button>
    <?php if($search || $filiereId || $typeFilter): ?>
    <a href="<?php echo e(route('controles.index', $anneeFilter ? ['annee'=>$anneeFilter] : [])); ?>" class="cn-btn cn-btn-ghost">Réinitialiser</a>
    <?php endif; ?>
</form>


<?php $__empty_1 = true; $__currentLoopData = $modules; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $fId => $filiereModules): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
    <?php $filiere = $filiereModules->first()->filiere; ?>

    <div class="cn-fh">
        <div style="display:flex;align-items:center;gap:10px;">
            <div style="width:8px;height:8px;border-radius:50%;background:<?php echo e($accent); ?>;flex-shrink:0;"></div>
            <span style="font-size:11px;font-weight:800;letter-spacing:1.5px;text-transform:uppercase;color:<?php echo e($text); ?>;">
                <?php echo e($filiere->name ?? 'Filière'); ?>

            </span>
            <span style="font-size:9px;background:<?php echo e($accent); ?>20;color:<?php echo e($text); ?>;padding:2px 8px;border-radius:99px;font-weight:700;">
                <?php echo e($filiereModules->count()); ?> module<?php echo e($filiereModules->count() > 1 ? 's' : ''); ?>

            </span>
        </div>
    </div>

    <div class="cn-table-wrap">
        <table class="cn-table">
            <thead>
                <tr>
                    <th>Module</th>
                    <th>Année</th>
                    <th>Type</th>
                    <th>Formateur</th>
                    <th>Heures</th>
                    <th>Contrôles</th>
                    <th style="text-align:right;">Action</th>
                </tr>
            </thead>
            <tbody>
            <?php $__currentLoopData = $filiereModules; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $module): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php $displayAnnee = $module->annee ?? 1; ?>
                <tr>
                    
                    <td>
                        <div style="font-weight:700;color:#1e293b;"><?php echo e($module->name); ?></div>
                        <div style="font-size:10px;color:#94a3b8;margin-top:1px;">Coeff. <?php echo e($module->coefficience); ?></div>
                    </td>

                    
                    <td>
                        <span class="cn-badge cn-badge-an<?php echo e($displayAnnee); ?>">
                            <?php echo e($displayAnnee); ?>ème An.
                        </span>
                    </td>

                    
                    <td>
                        <span class="cn-badge cn-badge-<?php echo e($module->type); ?>">
                            <?php echo e($module->type === 'regional' ? '🌍 Régional' : '📍 Local'); ?>

                        </span>
                    </td>

                    
                    <td style="color:#475569;font-size:11px;">
                        <?php echo e($module->formateur->name ?? '—'); ?>

                    </td>

                    
                    <td style="font-weight:700;color:<?php echo e($accent); ?>;"><?php echo e($module->nbr_heure); ?>h</td>

                    
                    <td>
                        <span class="ctrl-pill">
                            <svg width="9" height="9" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7 21H3v-4l9-9 4 4-9 9zm9-18l4 4-1 1-4-4 1-1z"/></svg>
                            <?php echo e($module->nbr_controles ?? 1); ?> contrôle<?php echo e(($module->nbr_controles ?? 1) > 1 ? 's' : ''); ?> + EFM
                        </span>
                    </td>

                    
                    <td style="text-align:right;">
                        <a href="<?php echo e(route('controles.notes', $module->id)); ?>"
                           class="cn-btn cn-btn-primary" style="font-size:11px;">
                            <svg width="12" height="12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                            Entrer notes
                        </a>
                    </td>
                </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </tbody>
        </table>
    </div>

<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
    <div style="padding:64px 32px;text-align:center;background:white;border-radius:16px;border:1px solid #e2e8f0;">
        <p style="font-size:15px;font-weight:700;color:#1e293b;margin:0 0 6px;">Aucun module trouvé</p>
        <p style="font-size:12px;color:#94a3b8;">Essayez de modifier vos filtres</p>
    </div>
<?php endif; ?>

</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\Project\gestion-CF\resources\views/controles/index.blade.php ENDPATH**/ ?>