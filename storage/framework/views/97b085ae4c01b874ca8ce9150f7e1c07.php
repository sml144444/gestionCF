
<?php $__env->startSection('title', 'Modules'); ?>
<?php $__env->startSection('page-title', 'Modules'); ?>

<?php $__env->startSection('content'); ?>

<?php
    $canCreate = Auth::user()->can('groupe-create');
    $canEdit   = Auth::user()->can('groupe-edit');
    $canDelete = Auth::user()->can('groupe-delete');

    $palettes = [
        'admin'        => ['primary' => '#0a6640', 'light' => '#e8f5ee', 'medium' => '#1a8c56', 'text' => '#065f38', 'shadow' => 'rgba(10,102,64,0.2)'],
        'gestionnaire' => ['primary' => '#1e293b', 'light' => '#f1f5f9', 'medium' => '#334155', 'text' => '#1e293b', 'shadow' => 'rgba(30,41,59,0.2)'],
    ];
    $p      = $palettes[Auth::user()->role] ?? $palettes['gestionnaire'];
    $accent = $p['primary'];
    $light  = $p['light'];
    $text   = $p['text'];
    $shadow = $p['shadow'];
?>

<style>
.mod-wrap { font-family: 'Segoe UI', system-ui, sans-serif; }
.mod-stat { background:white; border-radius:14px; border:1px solid #e2e8f0; padding:16px 20px; display:flex; align-items:center; gap:14px; transition:box-shadow .15s; }
.mod-stat:hover { box-shadow:0 4px 16px rgba(0,0,0,0.07); }
.mod-stat-icon { width:42px; height:42px; border-radius:12px; flex-shrink:0; display:flex; align-items:center; justify-content:center; }
.mod-stat-val { font-size:22px; font-weight:800; color:#1e293b; line-height:1; }
.mod-stat-lbl { font-size:10px; font-weight:600; color:#94a3b8; text-transform:uppercase; letter-spacing:.6px; margin-top:3px; }
.mod-filter-bar { display:flex; gap:10px; flex-wrap:wrap; align-items:center; padding:14px 16px; background:white; border-radius:14px; border:1px solid #e2e8f0; margin-bottom:20px; }
.mod-filter-input { height:38px; padding:0 12px; border-radius:9px; border:1.5px solid #e2e8f0; background:#f8fafc; font-size:12px; color:#1e293b; outline:none; transition:border-color .15s; }
.mod-filter-input:focus { border-color:<?php echo e($accent); ?>; background:white; }
.mod-filiere-head { display:flex; align-items:center; justify-content:space-between; padding:10px 16px; background:<?php echo e($light); ?>; border-left:4px solid <?php echo e($accent); ?>; border-radius:10px; margin-bottom:10px; margin-top:20px; }
.mod-card { background:white; border-radius:14px; border:1px solid #e2e8f0; padding:14px 16px; transition:box-shadow .15s,transform .15s; position:relative; }
.mod-card:hover { box-shadow:0 4px 18px rgba(0,0,0,0.07); transform:translateY(-1px); }
.mod-badge { display:inline-flex; align-items:center; font-size:9px; font-weight:800; padding:2px 8px; border-radius:99px; text-transform:uppercase; letter-spacing:.5px; }
.mod-badge-regional { background:#eff6ff; color:#1e40af; border:1px solid #bfdbfe; }
.mod-badge-local    { background:#fdf4ff; color:#7e22ce; border:1px solid #e9d5ff; }
.mod-badge-annee1   { background:#eff6ff; color:#1e40af; border:1px solid #bfdbfe; }
.mod-badge-annee2   { background:#fdf4ff; color:#7e22ce; border:1px solid #e9d5ff; }
.mod-badge-annee3   { background:#fff7ed; color:#c2410c; border:1px solid #fed7aa; }

/* Progress bar */
.mod-progress-track { height:6px; background:#f1f5f9; border-radius:99px; overflow:hidden; }
.mod-progress-fill  { height:100%; border-radius:99px; transition:width .5s; }

.mod-btn { display:inline-flex; align-items:center; gap:6px; padding:8px 14px; font-size:12px; font-weight:700; border-radius:10px; border:none; cursor:pointer; transition:opacity .15s; text-decoration:none; }
.mod-btn:hover { opacity:.85; }
.mod-btn-primary { background:<?php echo e($accent); ?>; color:white; box-shadow:0 4px 12px <?php echo e($shadow); ?>; }
.mod-btn-ghost   { background:white; border:1.5px solid #e2e8f0; color:#475569; }
.mod-btn-yellow  { background:#fef3c7; color:#92400e; border:1px solid #fde68a; }
.mod-btn-red     { background:#fee2e2; color:#dc2626; border:1px solid #fecaca; }
.mod-label { display:block; font-size:9px; font-weight:800; color:#94a3b8; letter-spacing:1.5px; text-transform:uppercase; margin-bottom:6px; }
.mod-input { width:100%; height:42px; padding:0 12px; border-radius:10px; border:1.5px solid #e2e8f0; background:#f8fafc; font-size:13px; color:#1e293b; outline:none; transition:border-color .15s; box-sizing:border-box; }
.mod-input:focus { border-color:<?php echo e($accent); ?>; background:white; }
.mod-overlay { display:none; position:fixed; inset:0; z-index:60; background:rgba(15,23,42,0.5); backdrop-filter:blur(4px); align-items:center; justify-content:center; }
.mod-overlay.open { display:flex; }
.mod-modal { background:white; border-radius:20px; width:100%; max-width:500px; margin:16px; padding:24px; box-shadow:0 24px 60px rgba(0,0,0,0.18); max-height:92vh; overflow-y:auto; }
.type-toggle { display:flex; border-radius:10px; overflow:hidden; border:1.5px solid #e2e8f0; }
.type-btn { flex:1; padding:10px; font-size:12px; font-weight:600; border:none; background:white; cursor:pointer; transition:all .15s; display:flex; align-items:center; justify-content:center; gap:6px; color:#64748b; }
.type-btn.active-regional { background:#2563eb; color:white; }
.type-btn.active-local    { background:#7e22ce; color:white; }

/* Annee tabs */
.annee-tab { padding:7px 14px; border-radius:99px; font-size:12px; font-weight:600; text-decoration:none; border:1.5px solid #e2e8f0; background:white; color:#64748b; transition:all .15s; display:inline-flex; align-items:center; gap:5px; }
.annee-tab:hover { border-color:<?php echo e($accent); ?>; color:<?php echo e($text); ?>; background:<?php echo e($light); ?>; }
.annee-tab.active { background:<?php echo e($accent); ?>; border-color:<?php echo e($accent); ?>; color:white; }

/* Remplacant badge */
.remplacant-pill {
    display:inline-flex; align-items:center; gap:3px;
    font-size:8px; font-weight:800; padding:1px 6px; border-radius:99px;
    background:#f5f3ff; color:#7c3aed; border:1px solid #ddd6fe;
    text-transform:uppercase; letter-spacing:.3px;
}
</style>

<div class="mod-wrap">


<?php if(session('success')): ?>
    <div style="margin-bottom:16px;padding:12px 16px;border-radius:12px;font-size:13px;display:flex;align-items:center;gap:8px;background:#f0fdf4;border:1px solid #bbf7d0;color:#15803d;">
        ✓ <?php echo e(session('success')); ?>

    </div>
<?php endif; ?>
<?php if(session('error')): ?>
    <div style="margin-bottom:16px;padding:12px 16px;border-radius:12px;font-size:13px;display:flex;align-items:center;gap:8px;background:#fff1f2;border:1px solid #fecdd3;color:#be123c;">
        ✕ <?php echo e(session('error')); ?>

    </div>
<?php endif; ?>
<?php if($errors->any()): ?>
    <div style="margin-bottom:16px;padding:12px 16px;border-radius:12px;font-size:12px;background:#fff1f2;border:1px solid #fecdd3;color:#be123c;">
        <ul style="margin:0;padding-left:16px;"><?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $e): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><li><?php echo e($e); ?></li><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?></ul>
    </div>
<?php endif; ?>


<div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:12px;margin-bottom:20px;">
    <div>
        <h1 style="font-size:20px;font-weight:800;color:#0f172a;margin:0;">Modules de formation</h1>
        <p style="font-size:12px;color:#64748b;margin:4px 0 0;">
            <?php echo e($totalModules); ?> module<?php echo e($totalModules !== 1 ? 's' : ''); ?> ·
            <?php echo e($totalHeures); ?>h total ·
            <a href="<?php echo e(route('filieres.index')); ?>" style="color:<?php echo e($accent); ?>;font-weight:600;text-decoration:none;">Filières →</a>
        </p>
    </div>
    <?php if($canCreate): ?>
    <button onclick="openCreateModal()" class="mod-btn mod-btn-primary">
        <svg width="13" height="13" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
        Nouveau module
    </button>
    <?php endif; ?>
</div>


<div style="display:grid;grid-template-columns:repeat(4,1fr);gap:12px;margin-bottom:20px;">
    <div class="mod-stat">
        <div class="mod-stat-icon" style="background:<?php echo e($light); ?>;"><svg width="20" height="20" fill="none" stroke="<?php echo e($accent); ?>" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg></div>
        <div><div class="mod-stat-val"><?php echo e($totalModules); ?></div><div class="mod-stat-lbl">Total</div></div>
    </div>
    <div class="mod-stat">
        <div class="mod-stat-icon" style="background:#eff6ff;"><svg width="20" height="20" fill="none" stroke="#2563eb" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg></div>
        <div><div class="mod-stat-val" style="color:#1e40af;"><?php echo e($totalHeures); ?></div><div class="mod-stat-lbl">Heures</div></div>
    </div>
    <div class="mod-stat">
        <div class="mod-stat-icon" style="background:#eff6ff;"><svg width="20" height="20" fill="none" stroke="#2563eb" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064"/></svg></div>
        <div><div class="mod-stat-val" style="color:#1e40af;"><?php echo e($totalRegional); ?></div><div class="mod-stat-lbl">Régionaux</div></div>
    </div>
    <div class="mod-stat">
        <div class="mod-stat-icon" style="background:#fdf4ff;"><svg width="20" height="20" fill="none" stroke="#7e22ce" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/></svg></div>
        <div><div class="mod-stat-val" style="color:#7e22ce;"><?php echo e($totalLocal); ?></div><div class="mod-stat-lbl">Locaux</div></div>
    </div>
</div>


<div style="display:flex;gap:8px;flex-wrap:wrap;margin-bottom:16px;align-items:center;">
    <span style="font-size:9px;font-weight:800;color:<?php echo e($text); ?>;text-transform:uppercase;letter-spacing:1.5px;">Année :</span>
    <a href="<?php echo e(route('modules.index', array_merge(request()->except('annee','page'), []))); ?>"
       class="annee-tab <?php echo e(!$anneeFilter ? 'active' : ''); ?>">Toutes</a>
    <a href="<?php echo e(route('modules.index', array_merge(request()->except('annee','page'), ['annee'=>1]))); ?>"
       class="annee-tab <?php echo e($anneeFilter == 1 ? 'active' : ''); ?>">1ère année</a>
    <a href="<?php echo e(route('modules.index', array_merge(request()->except('annee','page'), ['annee'=>2]))); ?>"
       class="annee-tab <?php echo e($anneeFilter == 2 ? 'active' : ''); ?>">2ème année</a>
    <a href="<?php echo e(route('modules.index', array_merge(request()->except('annee','page'), ['annee'=>3]))); ?>"
       class="annee-tab <?php echo e($anneeFilter == 3 ? 'active' : ''); ?>">3ème année</a>
</div>


<form method="GET" action="<?php echo e(route('modules.index')); ?>" class="mod-filter-bar">
    <?php if($anneeFilter): ?>
        <input type="hidden" name="annee" value="<?php echo e($anneeFilter); ?>">
    <?php endif; ?>
    <input type="text" name="search" value="<?php echo e($search); ?>" placeholder="Rechercher un module…"
           class="mod-filter-input" style="flex:1;min-width:160px;">
    <select name="filiere" class="mod-filter-input" style="min-width:160px;">
        <option value="">Toutes les filières</option>
        <?php $__currentLoopData = $filieres; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $f): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <option value="<?php echo e($f->id); ?>" <?php echo e(request('filiere') == $f->id ? 'selected' : ''); ?>><?php echo e($f->name); ?></option>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </select>
    <select name="type" class="mod-filter-input">
        <option value="">Tous les types</option>
        <option value="regional" <?php echo e($typeFilter === 'regional' ? 'selected' : ''); ?>>Régional</option>
        <option value="local"    <?php echo e($typeFilter === 'local'    ? 'selected' : ''); ?>>Local</option>
    </select>
    <button type="submit" class="mod-btn mod-btn-primary" style="padding:8px 16px;">
        <svg width="13" height="13" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
        Filtrer
    </button>
    <?php if($search || request('filiere') || $typeFilter): ?>
    <a href="<?php echo e(route('modules.index', $anneeFilter ? ['annee'=>$anneeFilter] : [])); ?>" class="mod-btn mod-btn-ghost" style="padding:8px 14px;">Réinitialiser</a>
    <?php endif; ?>
</form>


<?php $__empty_1 = true; $__currentLoopData = $modules; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $filiereId => $filiereModules): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
    <?php $filiere = $filiereModules->first()->filiere; ?>

    <div class="mod-filiere-head">
        <div style="display:flex;align-items:center;gap:10px;">
            <div style="width:8px;height:8px;border-radius:50%;background:<?php echo e($accent); ?>;flex-shrink:0;"></div>
            <span style="font-size:11px;font-weight:800;letter-spacing:1.5px;text-transform:uppercase;color:<?php echo e($text); ?>;"><?php echo e($filiere->name ?? 'Filière'); ?></span>
            <span style="font-size:9px;background:<?php echo e($accent); ?>15;color:<?php echo e($text); ?>;padding:2px 8px;border-radius:99px;font-weight:700;">
                <?php echo e($filiereModules->count()); ?> module<?php echo e($filiereModules->count() > 1 ? 's' : ''); ?> · <?php echo e($filiereModules->sum('nbr_heure')); ?>h
            </span>
        </div>
        <?php if($canCreate): ?>
        <button onclick="openCreateModal('<?php echo e($filiereId); ?>')"
                class="mod-btn" style="padding:5px 10px;font-size:10px;background:<?php echo e($accent); ?>;color:white;font-weight:700;">
            + Ajouter
        </button>
        <?php endif; ?>
    </div>

    <?php
        $byAnnee    = $filiereModules->groupBy(fn($m) => $m->annee ?? 3);
        $anneeOrder = [1, 2, 3];
    ?>

    <?php $__currentLoopData = $anneeOrder; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $anneeKey): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <?php if(!$byAnnee->has($anneeKey)): ?> <?php continue; ?> <?php endif; ?>
        <?php $anneeModules = $byAnnee[$anneeKey]; ?>

        <?php if($byAnnee->count() > 1): ?>
        <div style="font-size:9px;font-weight:800;color:#94a3b8;text-transform:uppercase;letter-spacing:1.5px;margin:10px 0 8px;padding-left:4px;">
            <?php if($anneeKey === 1): ?> 1ère Année
            <?php elseif($anneeKey === 2): ?> 2ème Année
            <?php else: ?> 3ème Année
            <?php endif; ?>
        </div>
        <?php endif; ?>

        <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(280px,1fr));gap:12px;margin-bottom:8px;">
        <?php $__currentLoopData = $anneeModules; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $module): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>

            <?php
                $groupRows   = $moduleProgressMap[$module->id] ?? collect();
                $moduleAnnee = $module->annee ?? 3;
                $anneeRows   = $groupRows->filter(fn($r) => (int)$r->groupe_annee === (int)$moduleAnnee)->values();
                $totalH      = $module->nbr_heure;
                $doneHours   = $anneeRows->isNotEmpty()
                    ? round($anneeRows->avg(fn($r) => $r->total_minutes / 60), 1)
                    : 0;
                $pct      = $totalH > 0 ? min(100, round(($doneHours / $totalH) * 100)) : 0;
                $pctColor = $pct >= 100 ? '#22c55e' : ($pct >= 75 ? '#f59e0b' : $accent);
            ?>

            <div class="mod-card">
                
                <div style="position:absolute;top:12px;right:12px;display:flex;flex-direction:column;gap:3px;align-items:flex-end;">
                    <span class="mod-badge mod-badge-<?php echo e($module->type); ?>">
                        <?php echo e($module->type === 'regional' ? '🌍 Régional' : '📍 Local'); ?>

                    </span>
                    <?php $displayAnnee = $module->annee ?? 3; ?>
                    <span class="mod-badge mod-badge-annee<?php echo e($displayAnnee); ?>" style="margin-top:2px;">
                        <?php if($displayAnnee == 1): ?> 1ère An.
                        <?php elseif($displayAnnee == 2): ?> 2ème An.
                        <?php else: ?> 3ème An.
                        <?php endif; ?>
                    </span>
                </div>

                
                <div style="font-size:13px;font-weight:800;color:#1e293b;padding-right:80px;line-height:1.3;margin-bottom:10px;">
                    <?php echo e($module->name); ?>

                </div>

                
                <div style="display:flex;gap:16px;margin-bottom:8px;">
                    <div>
                        <div style="font-size:16px;font-weight:800;color:<?php echo e($accent); ?>;"><?php echo e($module->nbr_heure); ?>h</div>
                        <div style="font-size:9px;color:#94a3b8;text-transform:uppercase;letter-spacing:.4px;">Total</div>
                    </div>
                    <div>
                        <div style="font-size:16px;font-weight:800;color:<?php echo e($pctColor); ?>;"><?php echo e($doneHours); ?>h</div>
                        <div style="font-size:9px;color:#94a3b8;text-transform:uppercase;letter-spacing:.4px;">Fait</div>
                    </div>
                    <div>
                        <div style="font-size:16px;font-weight:800;color:#475569;"><?php echo e($module->coefficience); ?></div>
                        <div style="font-size:9px;color:#94a3b8;text-transform:uppercase;letter-spacing:.4px;">Coeff.</div>
                    </div>
                    <div>
                        <div style="font-size:16px;font-weight:800;color:#475569;"><?php echo e($module->emplois_du_temps_count); ?></div>
                        <div style="font-size:9px;color:#94a3b8;text-transform:uppercase;letter-spacing:.4px;">Séances</div>
                    </div>
                </div>

                
                <div style="margin-bottom:10px;">
                    <div style="display:flex;justify-content:space-between;font-size:9px;color:#64748b;margin-bottom:4px;">
                        <span>Progression par groupe</span>
                        <?php if($anneeRows->count() > 1): ?>
                            <span style="color:#94a3b8;">moy. <?php echo e($pct); ?>%</span>
                        <?php endif; ?>
                    </div>
                    <?php if($anneeRows->isEmpty()): ?>
                        <div class="mod-progress-track">
                            <div class="mod-progress-fill" style="width:0%; background:#e2e8f0;"></div>
                        </div>
                        <div style="font-size:8px;color:#94a3b8;margin-top:3px;">Aucune séance effectuée</div>
                    <?php else: ?>
                        <?php $__currentLoopData = $anneeRows; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php
                                $gDoneH = round($row->total_minutes / 60, 1);
                                $gPct   = $totalH > 0 ? min(100, round(($gDoneH / $totalH) * 100)) : 0;
                                $gColor = $gPct >= 100 ? '#22c55e' : ($gPct >= 75 ? '#f59e0b' : $accent);
                            ?>
                            <div style="margin-bottom:5px;">
                                <div style="display:flex;justify-content:space-between;font-size:8px;color:#475569;margin-bottom:2px;">
                                    <span style="font-weight:600;"><?php echo e($row->groupe_name); ?></span>
                                    <span style="font-weight:800;color:<?php echo e($gColor); ?>;">
                                        <?php echo e($gDoneH); ?>h / <?php echo e($totalH); ?>h &nbsp;·&nbsp; <?php echo e($gPct); ?>%
                                        <?php if($gPct >= 100): ?> ✓ <?php endif; ?>
                                    </span>
                                </div>
                                <div class="mod-progress-track">
                                    <div class="mod-progress-fill" style="width:<?php echo e($gPct); ?>%; background:<?php echo e($gColor); ?>;"></div>
                                </div>
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    <?php endif; ?>
                </div>

                
                <div style="display:flex;align-items:center;gap:6px;margin-bottom:4px;">
                    <div style="width:20px;height:20px;border-radius:50%;background:<?php echo e($light); ?>;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                        <svg width="11" height="11" fill="none" stroke="<?php echo e($accent); ?>" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                    </div>
                    <span style="font-size:10px;color:#64748b;font-weight:500;"><?php echo e($module->formateur->name ?? '—'); ?></span>
                </div>

                
                <?php if($module->remplacant): ?>
                <div style="display:flex;align-items:center;gap:6px;margin-bottom:8px;">
                    <div style="width:20px;height:20px;border-radius:50%;background:#f5f3ff;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                        <svg width="11" height="11" fill="none" stroke="#7c3aed" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                    </div>
                    <span style="font-size:10px;color:#7c3aed;font-weight:600;"><?php echo e($module->remplacant->name); ?></span>
                    <span class="remplacant-pill">Remplaçant</span>
                </div>
                <?php else: ?>
                <div style="margin-bottom:8px;"></div>
                <?php endif; ?>

                
                <?php if($canEdit || $canDelete): ?>
                <div style="display:flex;gap:6px;padding-top:8px;border-top:1px solid #f1f5f9;">
                    <?php if($canEdit): ?>
                    <button class="mod-btn mod-btn-yellow"
                            style="flex:1;justify-content:center;padding:6px 10px;font-size:10px;"
                            onclick="openEditModal(
                                <?php echo e($module->id); ?>,
                                '<?php echo e(addslashes($module->name)); ?>',
                                <?php echo e($module->nbr_heure); ?>,
                                <?php echo e($module->coefficience); ?>,
                                <?php echo e($module->nbr_controles ?? 1); ?>,
                                '<?php echo e($module->id_user ?? ''); ?>',
                                '<?php echo e($module->id_user_remplacant ?? ''); ?>',
                                '<?php echo e($module->type); ?>',
                                '<?php echo e($module->annee ?? 3); ?>'
                            )">✎ Modifier</button>
                    <?php endif; ?>
                    <?php if($canDelete): ?>
                    <button class="mod-btn mod-btn-red"
                            style="padding:6px 10px;font-size:10px;"
                            onclick="openDeleteModal(
                                '<?php echo e(route('modules.destroy', $module)); ?>',
                                '<?php echo e(addslashes($module->name)); ?>',
                                <?php echo e($module->emplois_du_temps_count); ?>

                            )">✕</button>
                    <?php endif; ?>
                </div>
                <?php endif; ?>
            </div>

        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>

    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
    <div style="padding:64px 32px;text-align:center;background:white;border-radius:16px;border:1px solid #e2e8f0;">
        <div style="width:60px;height:60px;border-radius:18px;background:<?php echo e($light); ?>;display:flex;align-items:center;justify-content:center;margin:0 auto 14px;">
            <svg width="28" height="28" fill="none" stroke="<?php echo e($accent); ?>" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
        </div>
        <p style="font-size:15px;font-weight:700;color:#1e293b;margin:0 0 6px;">
            <?php echo e($search || request('filiere') || $typeFilter ? 'Aucun résultat' : 'Aucun module créé'); ?>

        </p>
        <?php if($canCreate && !$search && !request('filiere') && !$typeFilter): ?>
        <button onclick="openCreateModal()" class="mod-btn mod-btn-primary">Créer le premier module</button>
        <?php endif; ?>
    </div>
<?php endif; ?>

</div>


<?php if($canCreate): ?>
<div id="modal-create" class="mod-overlay" onclick="if(event.target===this)this.classList.remove('open')">
    <div class="mod-modal">
        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:18px;padding-bottom:14px;border-bottom:2px solid <?php echo e($accent); ?>;">
            <div>
                <h3 style="font-size:15px;font-weight:800;color:#1e293b;margin:0;">Nouveau module</h3>
                <p style="font-size:10px;color:#64748b;margin:3px 0 0;">Ajouter un module de formation</p>
            </div>
            <button onclick="document.getElementById('modal-create').classList.remove('open')"
                    style="width:28px;height:28px;border-radius:8px;border:none;background:#f1f5f9;color:#64748b;font-size:16px;cursor:pointer;display:flex;align-items:center;justify-content:center;">×</button>
        </div>
        <form method="POST" action="<?php echo e(route('modules.store')); ?>" style="display:flex;flex-direction:column;gap:14px;">
            <?php echo csrf_field(); ?>

            
            <div>
                <label class="mod-label">Filière <span style="color:#ef4444;">*</span></label>
                <select name="id_filiere" id="create-id-filiere" class="mod-input" required>
                    <option value="">— Sélectionner —</option>
                    <?php $__currentLoopData = $filieres; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $f): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><option value="<?php echo e($f->id); ?>"><?php echo e($f->name); ?></option><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>

            
            <div>
                <label class="mod-label">Nom du module <span style="color:#ef4444;">*</span></label>
                <input type="text" name="name" class="mod-input" required placeholder="Ex : PHP & Laravel…" value="<?php echo e(old('name')); ?>">
            </div>

            
            <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:12px;">
                <div>
                    <label class="mod-label">Heures totales <span style="color:#ef4444;">*</span></label>
                    <input type="number" name="nbr_heure" class="mod-input" required min="1" max="500" placeholder="75" value="<?php echo e(old('nbr_heure')); ?>">
                </div>
                <div>
                    <label class="mod-label">Coefficient <span style="color:#ef4444;">*</span></label>
                    <input type="number" name="coefficience" class="mod-input" required min="0.5" max="10" step="0.5" placeholder="3" value="<?php echo e(old('coefficience')); ?>">
                </div>
                <div>
                    <label class="mod-label">
                        Nbre contrôles
                        <span style="font-size:8px;color:#94a3b8;font-weight:400;text-transform:none;letter-spacing:0;margin-left:3px;">(EFM auto)</span>
                    </label>
                    <input type="number" name="nbr_controles" class="mod-input"
                           min="0" max="10" step="1" placeholder="1"
                           value="<?php echo e(old('nbr_controles', 1)); ?>"
                           title="0 = EFM uniquement · L'EFM est toujours ajouté automatiquement">
                </div>
            </div>

            
            <div>
                <label class="mod-label">Formateur responsable <span style="color:#ef4444;">*</span></label>
                <select name="id_user" id="create-id-user" class="mod-input" required
                        onchange="filterRemplacantCreate(this.value)">
                    <option value="">— Sélectionner —</option>
                    <?php $__currentLoopData = $formateurs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $f): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($f->id); ?>"><?php echo e($f->name); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>

            
            <div>
                <label class="mod-label">
                    Formateur remplaçant
                    <span style="font-size:8px;color:#94a3b8;font-weight:400;text-transform:none;letter-spacing:0;margin-left:4px;">(optionnel)</span>
                </label>
                <select name="id_user_remplacant" id="create-remplacant" class="mod-input"
                        style="border-color:#ddd6fe;">
                    <option value="">— Aucun —</option>
                    <?php $__currentLoopData = $formateurs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $f): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($f->id); ?>"><?php echo e($f->name); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
                <p style="font-size:9px;color:#94a3b8;margin-top:4px;display:flex;align-items:center;gap:4px;">
                    <svg width="10" height="10" fill="none" stroke="#7c3aed" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    En cas d'absence du formateur principal (maladie, empêchement…)
                </p>
            </div>

            
            <div>
                <label class="mod-label">Année concernée <span style="color:#ef4444;">*</span></label>
                <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:8px;">
                    <?php $__currentLoopData = [[1,'1ère An.'],[2,'2ème An.'],[3,'3ème An.']]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as [$val,$lbl]): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <label style="display:flex;align-items:center;gap:8px;cursor:pointer;padding:9px 12px;border-radius:10px;border:1.5px solid #e2e8f0;background:white;transition:all .15s;font-size:12px;font-weight:600;color:#475569;">
                        <input type="radio" name="annee" value="<?php echo e($val); ?>"
                               <?php echo e((old('annee', '1') === (string)$val) ? 'checked' : ''); ?>

                               style="accent-color:<?php echo e($accent); ?>;">
                        <?php echo e($lbl); ?>

                    </label>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
                <p style="font-size:9px;color:#94a3b8;margin-top:4px;">
                    Détermine dans quelle année ce module apparaît dans l'emploi du temps
                </p>
            </div>

            
            <div>
                <label class="mod-label">Type <span style="color:#ef4444;">*</span></label>
                <div class="type-toggle">
                    <button type="button" id="create-btn-regional" class="type-btn active-regional" onclick="setCreateType('regional')">🌍 Régional</button>
                    <button type="button" id="create-btn-local" class="type-btn" style="border-left:1px solid #e2e8f0;" onclick="setCreateType('local')">📍 Local</button>
                </div>
                <input type="hidden" name="type" id="create-type" value="regional">
            </div>

            <div style="display:flex;gap:10px;margin-top:4px;">
                <button type="button" onclick="document.getElementById('modal-create').classList.remove('open')"
                        class="mod-btn mod-btn-ghost" style="flex:1;height:44px;justify-content:center;">Annuler</button>
                <button type="submit" class="mod-btn mod-btn-primary" style="flex:1;height:44px;justify-content:center;">
                    <svg width="13" height="13" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                    Créer
                </button>
            </div>
        </form>
    </div>
</div>
<?php endif; ?>


<?php if($canEdit): ?>
<div id="modal-edit" class="mod-overlay" onclick="if(event.target===this)this.classList.remove('open')">
    <div class="mod-modal">
        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:18px;padding-bottom:14px;border-bottom:2px solid #f59e0b;">
            <div>
                <h3 style="font-size:15px;font-weight:800;color:#1e293b;margin:0;">Modifier le module</h3>
                <p id="edit-subtitle" style="font-size:10px;color:#64748b;margin:3px 0 0;"></p>
            </div>
            <button onclick="document.getElementById('modal-edit').classList.remove('open')"
                    style="width:28px;height:28px;border-radius:8px;border:none;background:#f1f5f9;color:#64748b;font-size:16px;cursor:pointer;display:flex;align-items:center;justify-content:center;">×</button>
        </div>
        <form id="edit-form" method="POST" style="display:flex;flex-direction:column;gap:14px;">
            <?php echo csrf_field(); ?> <?php echo method_field('PATCH'); ?>

            
            <div>
                <label class="mod-label">Nom <span style="color:#ef4444;">*</span></label>
                <input type="text" name="name" id="edit-name" class="mod-input" required>
            </div>

            
            <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:12px;">
                <div>
                    <label class="mod-label">Heures totales</label>
                    <input type="number" name="nbr_heure" id="edit-heure" class="mod-input" required min="1" max="500">
                </div>
                <div>
                    <label class="mod-label">Coefficient</label>
                    <input type="number" name="coefficience" id="edit-coeff" class="mod-input" required min="0.5" max="10" step="0.5">
                </div>
                <div>
                    <label class="mod-label">
                        Nbre contrôles
                        <span style="font-size:8px;color:#94a3b8;font-weight:400;text-transform:none;letter-spacing:0;margin-left:3px;">(EFM auto)</span>
                    </label>
                    <input type="number" name="nbr_controles" id="edit-nbr-controles" class="mod-input"
                           min="0" max="10" step="1" placeholder="1"
                           title="0 = EFM uniquement">
                </div>
            </div>

            
            <div>
                <label class="mod-label">Formateur <span style="color:#ef4444;">*</span></label>
                <select name="id_user" id="edit-user" class="mod-input" required
                        onchange="filterRemplacantEdit(this.value)">
                    <option value="">— Sélectionner —</option>
                    <?php $__currentLoopData = $formateurs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $f): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($f->id); ?>"><?php echo e($f->name); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>

            
            <div>
                <label class="mod-label">
                    Formateur remplaçant
                    <span style="font-size:8px;color:#94a3b8;font-weight:400;text-transform:none;letter-spacing:0;margin-left:4px;">(optionnel)</span>
                </label>
                <select name="id_user_remplacant" id="edit-remplacant" class="mod-input"
                        style="border-color:#ddd6fe;"
                        onfocus="this.style.borderColor='#7c3aed'"
                        onblur="this.style.borderColor='#ddd6fe'">
                    <option value="">— Aucun —</option>
                    <?php $__currentLoopData = $formateurs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $f): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($f->id); ?>"><?php echo e($f->name); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
                <p style="font-size:9px;color:#94a3b8;margin-top:4px;display:flex;align-items:center;gap:4px;">
                    <svg width="10" height="10" fill="none" stroke="#7c3aed" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    En cas d'absence du formateur principal
                </p>
            </div>

            
            <div>
                <label class="mod-label">Année concernée <span style="color:#ef4444;">*</span></label>
                <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:8px;">
                    <?php $__currentLoopData = [[1,'1ère An.'],[2,'2ème An.'],[3,'3ème An.']]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as [$val,$lbl]): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <label style="display:flex;align-items:center;gap:8px;cursor:pointer;padding:9px 12px;border-radius:10px;border:1.5px solid #e2e8f0;background:white;font-size:12px;font-weight:600;color:#475569;">
                        <input type="radio" name="annee" value="<?php echo e($val); ?>" id="edit-annee-<?php echo e($val); ?>" style="accent-color:#f59e0b;">
                        <?php echo e($lbl); ?>

                    </label>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            </div>

            
            <div>
                <label class="mod-label">Type</label>
                <div class="type-toggle">
                    <button type="button" id="edit-btn-regional" class="type-btn" onclick="setEditType('regional')">🌍 Régional</button>
                    <button type="button" id="edit-btn-local"    class="type-btn" style="border-left:1px solid #e2e8f0;" onclick="setEditType('local')">📍 Local</button>
                </div>
                <input type="hidden" name="type" id="edit-type">
            </div>

            <div style="display:flex;gap:10px;margin-top:4px;">
                <button type="button" onclick="document.getElementById('modal-edit').classList.remove('open')"
                        class="mod-btn mod-btn-ghost" style="flex:1;height:44px;justify-content:center;">Annuler</button>
                <button type="submit" class="mod-btn" style="flex:1;height:44px;justify-content:center;background:#f59e0b;color:white;box-shadow:0 4px 12px rgba(245,158,11,0.3);">
                    <svg width="13" height="13" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                    Enregistrer
                </button>
            </div>
        </form>
    </div>
</div>
<?php endif; ?>


<?php if($canDelete): ?>
<div id="modal-delete" class="mod-overlay" onclick="if(event.target===this)this.classList.remove('open')">
    <div class="mod-modal" style="max-width:400px;">
        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:14px;padding-bottom:14px;border-bottom:2px solid #dc2626;">
            <div style="display:flex;align-items:center;gap:10px;">
                <div style="width:40px;height:40px;border-radius:12px;background:#fee2e2;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                    <svg width="18" height="18" fill="none" stroke="#dc2626" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                </div>
                <div>
                    <div style="font-size:14px;font-weight:800;color:#1e293b;">Supprimer le module ?</div>
                    <div id="delete-name" style="font-size:10px;color:#64748b;margin-top:1px;"></div>
                </div>
            </div>
            <button onclick="document.getElementById('modal-delete').classList.remove('open')"
                    style="width:28px;height:28px;border-radius:8px;border:none;background:#f1f5f9;color:#64748b;font-size:16px;cursor:pointer;display:flex;align-items:center;justify-content:center;">×</button>
        </div>
        <div id="delete-warning" style="font-size:12px;line-height:1.6;margin-bottom:16px;padding:12px 14px;border-radius:12px;background:#fff1f2;border:1px solid #fecdd3;color:#9f1239;"></div>
        <div style="display:flex;gap:10px;">
            <button onclick="document.getElementById('modal-delete').classList.remove('open')"
                    class="mod-btn mod-btn-ghost" style="flex:1;height:44px;justify-content:center;">Annuler</button>
            <form id="delete-form" method="POST" style="flex:1;">
                <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                <button type="submit" id="delete-btn" class="mod-btn"
                        style="width:100%;height:44px;justify-content:center;background:#dc2626;color:white;box-shadow:0 4px 12px rgba(220,38,38,0.3);">
                    Supprimer
                </button>
            </form>
        </div>
    </div>
</div>
<?php endif; ?>

<script>
// ── Créer modal ───────────────────────────────────────────────
function openCreateModal(filiereId) {
    if (filiereId) document.getElementById('create-id-filiere').value = filiereId;
    document.getElementById('modal-create').classList.add('open');
}
function setCreateType(type) {
    document.getElementById('create-type').value = type;
    document.getElementById('create-btn-regional').className = 'type-btn' + (type === 'regional' ? ' active-regional' : '');
    document.getElementById('create-btn-local').className    = 'type-btn' + (type === 'local'    ? ' active-local'    : '');
}

// Empêcher de choisir le même formateur comme remplaçant (créer)
function filterRemplacantCreate(userId) {
    const sel = document.getElementById('create-remplacant');
    for (const opt of sel.options) {
        opt.disabled = (opt.value !== '' && opt.value === userId);
        if (opt.disabled && sel.value === opt.value) sel.value = '';
    }
}

// ── Éditer modal ──────────────────────────────────────────────
function openEditModal(id, name, heure, coeff, nbrControles, userId, remplacantId, type, annee) {
    document.getElementById('edit-form').action          = '/modules/' + id;
    document.getElementById('edit-name').value           = name;
    document.getElementById('edit-heure').value          = heure;
    document.getElementById('edit-coeff').value          = coeff;
    document.getElementById('edit-nbr-controles').value  = nbrControles;
    document.getElementById('edit-user').value           = userId;
    document.getElementById('edit-remplacant').value     = remplacantId || '';
    document.getElementById('edit-subtitle').textContent = 'Modification : ' + name;
    setEditType(type);
    // Désactiver le formateur principal dans le select remplaçant
    filterRemplacantEdit(userId);
    // Remettre la valeur après le filtre
    document.getElementById('edit-remplacant').value = remplacantId || '';
    // Set annee radio
    ['1','2','3'].forEach(v => {
        const r = document.getElementById('edit-annee-' + v);
        if (r) r.checked = (String(annee) === v);
    });
    document.getElementById('modal-edit').classList.add('open');
}
function setEditType(type) {
    document.getElementById('edit-type').value = type;
    document.getElementById('edit-btn-regional').className = 'type-btn' + (type === 'regional' ? ' active-regional' : '');
    document.getElementById('edit-btn-local').className    = 'type-btn' + (type === 'local'    ? ' active-local'    : '');
}

// Empêcher de choisir le même formateur comme remplaçant (éditer)
function filterRemplacantEdit(userId) {
    const sel = document.getElementById('edit-remplacant');
    for (const opt of sel.options) {
        opt.disabled = (opt.value !== '' && opt.value === userId);
        if (opt.disabled && sel.value === opt.value) sel.value = '';
    }
}

// ── Supprimer modal ───────────────────────────────────────────
function openDeleteModal(action, name, emploisCount) {
    document.getElementById('delete-form').action      = action;
    document.getElementById('delete-name').textContent = name;
    const btn  = document.getElementById('delete-btn');
    const warn = document.getElementById('delete-warning');
    if (emploisCount > 0) {
        warn.innerHTML  = '⚠️ Impossible : ce module est utilisé dans <strong>' + emploisCount + '</strong> séance(s).';
        btn.disabled    = true;
        btn.style.opacity = '.4';
        btn.style.cursor  = 'not-allowed';
    } else {
        warn.textContent  = 'Cette action est irréversible.';
        btn.disabled      = false;
        btn.style.opacity = '1';
        btn.style.cursor  = 'pointer';
    }
    document.getElementById('modal-delete').classList.add('open');
}
</script>

<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\Project\gestion-CF\resources\views/modules/index.blade.php ENDPATH**/ ?>