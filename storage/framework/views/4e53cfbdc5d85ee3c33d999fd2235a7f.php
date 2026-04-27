

<?php $__env->startSection('title', 'Notes — ' . $module->name); ?>
<?php $__env->startSection('page-title', 'Saisie des notes'); ?>

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
.notes-wrap { font-family:'Segoe UI',system-ui,sans-serif; }

/* ── Breadcrumb ── */
.breadcrumb { display:flex;align-items:center;gap:8px;font-size:12px;color:#94a3b8;margin-bottom:18px;flex-wrap:wrap; }
.breadcrumb a { color:<?php echo e($accent); ?>;font-weight:600;text-decoration:none; }
.breadcrumb a:hover { text-decoration:underline; }
.breadcrumb-sep { color:#cbd5e1; }

/* ── Module info card ── */
.mod-info { background:white;border-radius:16px;border:1px solid #e2e8f0;padding:20px 24px;margin-bottom:20px;display:flex;align-items:flex-start;gap:20px;flex-wrap:wrap; }
.mod-info-badge { display:inline-flex;align-items:center;font-size:9px;font-weight:800;padding:2px 8px;border-radius:99px;text-transform:uppercase;letter-spacing:.5px; }
.mod-info-badge-regional { background:#eff6ff;color:#1e40af;border:1px solid #bfdbfe; }
.mod-info-badge-local    { background:#fdf4ff;color:#7e22ce;border:1px solid #e9d5ff; }

/* ── Groupe selector ── */
.groupe-selector { background:white;border-radius:14px;border:1px solid #e2e8f0;padding:18px 20px;margin-bottom:20px; }
.grp-label { display:block;font-size:9px;font-weight:800;color:#94a3b8;letter-spacing:1.5px;text-transform:uppercase;margin-bottom:8px; }
.grp-select { height:44px;padding:0 14px;border-radius:10px;border:1.5px solid #e2e8f0;background:#f8fafc;font-size:13px;color:#1e293b;outline:none;transition:border-color .15s;width:100%;max-width:360px; }
.grp-select:focus { border-color:<?php echo e($accent); ?>;background:white; }

/* ── Buttons ── */
.n-btn { display:inline-flex;align-items:center;gap:6px;padding:9px 16px;font-size:12px;font-weight:700;border-radius:10px;border:none;cursor:pointer;transition:all .15s;text-decoration:none; }
.n-btn:hover { opacity:.87; }
.n-btn-primary { background:<?php echo e($accent); ?>;color:white;box-shadow:0 4px 14px <?php echo e($shadow); ?>; }
.n-btn-ghost   { background:white;border:1.5px solid #e2e8f0;color:#475569; }
.n-btn-green   { background:#16a34a;color:white;box-shadow:0 4px 14px rgba(22,163,74,0.25); }

/* ── Notes table container ── */
.notes-card { background:white;border-radius:16px;border:1px solid #e2e8f0;overflow:hidden;margin-bottom:24px; }
.notes-card-head { padding:14px 20px;border-bottom:1px solid #f1f5f9;display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:10px; }
.notes-table-wrap { overflow-x:auto; }

/* ── Notes table ── */
.notes-table { width:100%;border-collapse:collapse;font-size:12px;min-width:600px; }
.notes-table th {
    padding:11px 14px;text-align:left;font-size:9px;font-weight:800;
    color:#94a3b8;text-transform:uppercase;letter-spacing:.9px;
    background:#f8fafc;border-bottom:2px solid #f1f5f9;white-space:nowrap;
}
.notes-table th.col-name  { min-width:180px;position:sticky;left:0;z-index:2;background:#f8fafc; }
.notes-table th.col-ctrl  { min-width:110px;text-align:center; }
.notes-table th.col-efm   { min-width:110px;text-align:center;color:#dc2626; }
.notes-table th.col-moy   { min-width:100px;text-align:center;color:#7c3aed; }

.notes-table td {
    padding:10px 14px;
    border-bottom:1px solid #f8fafc;
    vertical-align:middle;
}
.notes-table tr:last-child td { border-bottom:none; }
.notes-table tr:hover td      { background:#fafbff; }

/* ── Sticky name column ── */
.notes-table td.col-name-cell {
    position:sticky;left:0;z-index:1;background:white;
    font-weight:600;color:#1e293b;border-right:1px solid #f1f5f9;
}
.notes-table tr:hover td.col-name-cell { background:#fafbff; }

/* ── Note input ── */
.note-input {
    width:84px;height:36px;text-align:center;
    border-radius:9px;border:1.5px solid #e2e8f0;
    background:#f8fafc;font-size:13px;font-weight:700;color:#1e293b;
    outline:none;transition:all .15s;
    display:block;margin:0 auto;
}
.note-input:focus {
    border-color:<?php echo e($accent); ?>;background:white;
    box-shadow:0 0 0 3px <?php echo e($accent); ?>20;
}
.note-input.efm-input:focus {
    border-color:#dc2626;box-shadow:0 0 0 3px rgba(220,38,38,.12);
}

/* ── Grade color coding (applied via JS) ── */
.grade-high   { color:#16a34a !important; }
.grade-mid    { color:#d97706 !important; }
.grade-low    { color:#dc2626 !important; }
.grade-none   { color:#94a3b8 !important; }

/* ── Moyenne cell ── */
.moy-cell { font-weight:800;font-size:13px;text-align:center; }

/* ── Empty state ── */
.empty-box { padding:52px 32px;text-align:center; }
.empty-icon { width:56px;height:56px;border-radius:16px;background:<?php echo e($light); ?>;display:flex;align-items:center;justify-content:center;margin:0 auto 14px; }

/* ── Flash ── */
.flash-ok   { padding:12px 16px;border-radius:12px;font-size:13px;display:flex;align-items:center;gap:8px;background:#f0fdf4;border:1px solid #bbf7d0;color:#15803d;margin-bottom:16px; }
.flash-err  { padding:12px 16px;border-radius:12px;font-size:13px;display:flex;align-items:center;gap:8px;background:#fff1f2;border:1px solid #fecdd3;color:#be123c;margin-bottom:16px; }

/* ── Coeff column header ── */
.col-coeff { min-width:80px;text-align:center; }

/* ── Info chips ── */
.info-chip { display:inline-flex;align-items:center;gap:4px;padding:3px 10px;border-radius:99px;font-size:10px;font-weight:600;background:#f1f5f9;color:#475569;border:1px solid #e2e8f0; }
</style>

<div class="notes-wrap">


<?php if(session('success')): ?>
<div class="flash-ok">✓ <?php echo e(session('success')); ?></div>
<?php endif; ?>
<?php if(session('error')): ?>
<div class="flash-err">✕ <?php echo e(session('error')); ?></div>
<?php endif; ?>


<div class="breadcrumb">
    <a href="<?php echo e(route('controles.index')); ?>">Contrôles & Notes</a>
    <span class="breadcrumb-sep">›</span>
    <span style="color:#1e293b;font-weight:600;"><?php echo e($module->name); ?></span>
    <?php if($selectedGroupe): ?>
        <span class="breadcrumb-sep">›</span>
        <span style="color:#64748b;"><?php echo e($selectedGroupe->name); ?></span>
    <?php endif; ?>
</div>


<div class="mod-info">
    <div style="flex:1;min-width:200px;">
        <div style="display:flex;align-items:center;gap:10px;flex-wrap:wrap;margin-bottom:8px;">
            <h2 style="font-size:17px;font-weight:800;color:#0f172a;margin:0;"><?php echo e($module->name); ?></h2>
            <span class="mod-info-badge mod-info-badge-<?php echo e($module->type); ?>">
                <?php echo e($module->type === 'regional' ? '🌍 Régional' : '📍 Local'); ?>

            </span>
        </div>
        <div style="display:flex;flex-wrap:wrap;gap:8px;">
            <span class="info-chip">
                <svg width="11" height="11" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                <?php echo e($module->formateur->name ?? '—'); ?>

            </span>
            <span class="info-chip">
                <svg width="11" height="11" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <?php echo e($module->nbr_heure); ?>h · Coeff. <?php echo e($module->coefficience); ?>

            </span>
            <span class="info-chip" style="background:#f0fdf4;color:#166534;border-color:#bbf7d0;">
                <svg width="11" height="11" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                <?php echo e($module->nbr_controles ?? 1); ?> contrôle<?php echo e(($module->nbr_controles ?? 1) > 1 ? 's' : ''); ?> + EFM
            </span>
            <?php if($module->annee): ?>
            <span class="info-chip">
                <?php echo e($module->annee); ?>ème Année
            </span>
            <?php endif; ?>
        </div>
    </div>

    
    <?php if(in_array(Auth::user()->role, ['admin', 'gestionnaire'])): ?>
    <form method="POST" action="<?php echo e(route('controles.update-nbr', $module->id)); ?>"
          style="display:flex;align-items:flex-end;gap:10px;flex-wrap:wrap;"
          title="Modifier le nombre de contrôles">
        <?php echo csrf_field(); ?> <?php echo method_field('PATCH'); ?>
        <?php if($selectedGroupe): ?>
            <input type="hidden" name="groupe_id" value="<?php echo e($selectedGroupe->id); ?>">
        <?php endif; ?>
        <div>
            <label style="display:block;font-size:9px;font-weight:800;color:#94a3b8;text-transform:uppercase;letter-spacing:1px;margin-bottom:5px;">
                Nbre contrôles
            </label>
            <input type="number" name="nbr_controles"
                   value="<?php echo e($module->nbr_controles ?? 1); ?>"
                   min="0" max="10"
                   style="width:70px;height:36px;text-align:center;border-radius:9px;border:1.5px solid #e2e8f0;background:#f8fafc;font-size:13px;font-weight:700;outline:none;padding:0 8px;"
                   title="0 = EFM uniquement">
        </div>
        <button type="submit" class="n-btn n-btn-ghost" style="height:36px;font-size:11px;">
            Mettre à jour
        </button>
    </form>
    <?php endif; ?>
</div>


<div class="groupe-selector">
    <form method="GET" action="<?php echo e(route('controles.notes', $module->id)); ?>"
          style="display:flex;align-items:flex-end;gap:12px;flex-wrap:wrap;">
        <div style="flex:1;">
            <label class="grp-label">Sélectionner un groupe</label>
            <select name="groupe_id" class="grp-select">
                <option value="">— Choisir un groupe —</option>
                <?php $__empty_1 = true; $__currentLoopData = $groupes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $g): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <option value="<?php echo e($g->id); ?>" <?php echo e(optional($selectedGroupe)->id == $g->id ? 'selected' : ''); ?>>
                        <?php echo e($g->name); ?>

                        (<?php echo e($g->stagiaires()->count()); ?> stagiaire<?php echo e($g->stagiaires()->count() > 1 ? 's' : ''); ?>)
                    </option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <option disabled>Aucun groupe pour cette filière / année</option>
                <?php endif; ?>
            </select>
        </div>
        <button type="submit" class="n-btn n-btn-primary">
            <svg width="13" height="13" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
            Afficher
        </button>
        <?php if($selectedGroupe): ?>
        <a href="<?php echo e(route('controles.notes', $module->id)); ?>" class="n-btn n-btn-ghost">
            Changer de groupe
        </a>
        <?php endif; ?>
    </form>
</div>


<?php if($selectedGroupe): ?>

    <?php
        $nbr       = $module->nbr_controles ?? 1;
        $totalCols = $controles->count() + 1;
    ?>

    <div class="notes-card">

        
        <div class="notes-card-head">
            <div>
                <div style="font-size:14px;font-weight:800;color:#0f172a;">
                    Notes — <?php echo e($selectedGroupe->name); ?>

                </div>
                <div style="font-size:11px;color:#64748b;margin-top:2px;">
                    <?php echo e($stagiaires->count()); ?> stagiaire<?php echo e($stagiaires->count() > 1 ? 's' : ''); ?> ·
                    <?php echo e($controles->count()); ?> contrôle<?php echo e($controles->count() > 1 ? 's' : ''); ?> + EFM
                    · notes sur 20
                </div>
            </div>
            <div style="display:flex;align-items:center;gap:8px;font-size:10px;color:#64748b;">
                <span style="display:flex;align-items:center;gap:4px;"><span style="width:8px;height:8px;border-radius:50%;background:#16a34a;display:inline-block;"></span>≥ 15</span>
                <span style="display:flex;align-items:center;gap:4px;"><span style="width:8px;height:8px;border-radius:50%;background:#d97706;display:inline-block;"></span>10–14</span>
                <span style="display:flex;align-items:center;gap:4px;"><span style="width:8px;height:8px;border-radius:50%;background:#dc2626;display:inline-block;"></span>< 10</span>
            </div>
        </div>

        
        <?php if($stagiaires->isEmpty()): ?>
            <div class="empty-box">
                <div class="empty-icon">
                    <svg width="24" height="24" fill="none" stroke="<?php echo e($accent); ?>" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                </div>
                <p style="font-size:14px;font-weight:700;color:#1e293b;margin:0 0 4px;">Aucun stagiaire dans ce groupe</p>
                <p style="font-size:12px;color:#94a3b8;">Ajoutez des stagiaires au groupe depuis la gestion des utilisateurs.</p>
            </div>
        <?php else: ?>

        <form method="POST" action="<?php echo e(route('controles.save', $module->id)); ?>" id="notes-form">
            <?php echo csrf_field(); ?>
            <input type="hidden" name="groupe_id" value="<?php echo e($selectedGroupe->id); ?>">

            <div class="notes-table-wrap">
                <table class="notes-table" id="notesTable">
                    <thead>
                        <tr>
                            <th class="col-name" style="background:#f8fafc;">#</th>
                            <th class="col-name" style="min-width:200px;position:sticky;left:40px;z-index:2;background:#f8fafc;">
                                Stagiaire
                            </th>
                            <?php $__currentLoopData = $controles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ctrl): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <th class="col-ctrl"><?php echo e($ctrl->titre); ?></th>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            <?php if($efm): ?>
                            <th class="col-efm">⚑ EFM</th>
                            <?php endif; ?>
                            <th class="col-moy">Moyenne</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php $__currentLoopData = $stagiaires; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $stagiaire): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <tr data-stagiaire="<?php echo e($stagiaire->id); ?>">

                            
                            <td style="color:#94a3b8;font-size:11px;font-weight:600;width:36px;text-align:center;background:#fafafa;">
                                <?php echo e($i + 1); ?>

                            </td>

                            
                            <td class="col-name-cell" style="left:40px;">
                                <div style="font-weight:700;"><?php echo e($stagiaire->name); ?></div>
                                <div style="font-size:10px;color:#94a3b8;margin-top:1px;"><?php echo e($stagiaire->email); ?></div>
                            </td>

                            
                            <?php $__currentLoopData = $controles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ctrl): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            
                            <?php $existingNote = $notesMap[(int)$stagiaire->id][(int)$ctrl->id] ?? null; ?>
                            <td style="text-align:center;">
                                <input
                                    type="number"
                                    name="notes[<?php echo e($stagiaire->id); ?>][<?php echo e($ctrl->id); ?>]"
                                    class="note-input ctrl-input"
                                    value="<?php echo e($existingNote !== null ? $existingNote : ''); ?>"
                                    min="0" max="20" step="0.25"
                                    placeholder="—"
                                    data-stagiaire="<?php echo e($stagiaire->id); ?>"
                                    data-max="20"
                                    oninput="colorize(this); updateMoy(<?php echo e($stagiaire->id); ?>)"
                                    onchange="colorize(this); updateMoy(<?php echo e($stagiaire->id); ?>)">
                            </td>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                            
                            <?php if($efm): ?>
                            <?php
                                $efmRaw  = $notesMap[(int)$stagiaire->id][(int)$efm->id] ?? null;
                                // Stored /20 → display /40 (multiply back by 2)
                                $efmNote = $efmRaw !== null ? round($efmRaw * 2, 2) : null;
                            ?>
                            <td style="text-align:center;">
                                <input
                                    type="number"
                                    name="notes[<?php echo e($stagiaire->id); ?>][<?php echo e($efm->id); ?>]"
                                    class="note-input efm-input"
                                    value="<?php echo e($efmNote !== null ? $efmNote : ''); ?>"
                                    min="0" max="40" step="0.25"
                                    placeholder="—"
                                    data-stagiaire="<?php echo e($stagiaire->id); ?>"
                                    data-max="40"
                                    style="border-color:#fecdd3;"
                                    oninput="colorize(this); updateMoy(<?php echo e($stagiaire->id); ?>)"
                                    onchange="colorize(this); updateMoy(<?php echo e($stagiaire->id); ?>)">
                                <div style="font-size:9px;color:#94a3b8;margin-top:2px;">/ 40</div>
                            </td>
                            <?php endif; ?>

                            
                            <td class="moy-cell">
                                <span id="moy-<?php echo e($stagiaire->id); ?>" style="color:#94a3b8;">—</span>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                </table>
            </div>

            
            <div style="padding:16px 20px;border-top:2px solid #f1f5f9;display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:12px;background:#fafafa;">
                <div style="font-size:11px;color:#64748b;">
                    <span id="filled-count" style="font-weight:800;color:<?php echo e($accent); ?>;">0</span>
                    note<?php echo e(''); ?> saisie<?php echo e(''); ?> sur
                    <span style="font-weight:700;"><?php echo e($stagiaires->count() * ($controles->count() + ($efm ? 1 : 0))); ?></span>
                </div>
                <div style="display:flex;gap:10px;">
                    <a href="<?php echo e(route('controles.index')); ?>" class="n-btn n-btn-ghost">
                        ← Retour aux modules
                    </a>
                    <button type="submit" class="n-btn n-btn-green">
                        <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                        Enregistrer les notes
                    </button>
                </div>
            </div>

        </form>
        <?php endif; ?>
    </div>

<?php else: ?>
    
    <div style="padding:64px 32px;text-align:center;background:white;border-radius:16px;border:1px solid #e2e8f0;">
        <div class="empty-icon">
            <svg width="26" height="26" fill="none" stroke="<?php echo e($accent); ?>" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
        </div>
        <p style="font-size:15px;font-weight:700;color:#1e293b;margin:0 0 6px;">Sélectionnez un groupe</p>
        <p style="font-size:12px;color:#94a3b8;">Choisissez un groupe dans le menu ci-dessus pour afficher le tableau de notes.</p>
    </div>
<?php endif; ?>

</div>

<script>
// ── Grade color coding ───────────────────────────────────────────
// Thresholds scale with data-max (20 for controles, 40 for EFM).
function colorize(input) {
    const val = parseFloat(input.value);
    input.style.color      = '';
    input.style.fontWeight = '';
    if (input.value === '' || isNaN(val)) return;
    const max = parseFloat(input.dataset.max || 20);
    const pct = val / max;
    if (pct >= 0.75)      { input.style.color = '#16a34a'; input.style.fontWeight = '800'; }
    else if (pct >= 0.50) { input.style.color = '#d97706'; input.style.fontWeight = '700'; }
    else                  { input.style.color = '#dc2626'; input.style.fontWeight = '700'; }
}

// ── Per-row moyenne ──────────────────────────────────────────────
function updateMoy(stagiaireId) {
    const row    = document.querySelector(`tr[data-stagiaire="${stagiaireId}"]`);
    if (!row) return;
    const inputs = row.querySelectorAll('.note-input');
    const values = [];
    inputs.forEach(i => {
        const v   = parseFloat(i.value);
        if (isNaN(v)) return;
        const max = parseFloat(i.dataset.max || 20);
        // Normalise to /20 before averaging (EFM entered /40 → divide by 2)
        values.push(v / max * 20);
    });
    const span = document.getElementById(`moy-${stagiaireId}`);
    if (!span) return;
    if (values.length === 0) {
        span.textContent = '—';
        span.style.color = '#94a3b8';
    } else {
        const avg = values.reduce((a, b) => a + b, 0) / values.length;
        span.textContent = avg.toFixed(2);
        span.style.color = avg >= 15 ? '#16a34a' : avg >= 10 ? '#d97706' : '#dc2626';
    }
    updateFilledCount();
}

// ── Filled count ────────────────────────────────────────────────
function updateFilledCount() {
    const all    = document.querySelectorAll('.note-input');
    const filled = [...all].filter(i => i.value !== '').length;
    const span   = document.getElementById('filled-count');
    if (span) span.textContent = filled;
}

// ── Init on load ─────────────────────────────────────────────────
document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.note-input').forEach(input => {
        colorize(input);
    });
    // Compute all moyennes on load
    <?php $__currentLoopData = $stagiaires; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $stagiaire): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
    updateMoy(<?php echo e($stagiaire->id); ?>);
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    updateFilledCount();

    // Tab / arrow navigation between cells
    document.querySelectorAll('.note-input').forEach((input, idx, arr) => {
        input.addEventListener('keydown', function(e) {
            if (e.key === 'Tab') return;
            if (e.key === 'Enter') {
                e.preventDefault();
                if (arr[idx + 1]) arr[idx + 1].focus();
            }
            if (e.key === 'ArrowRight' && arr[idx + 1]) {
                e.preventDefault(); arr[idx + 1].focus();
            }
            if (e.key === 'ArrowLeft' && arr[idx - 1]) {
                e.preventDefault(); arr[idx - 1].focus();
            }
        });
    });
});

// ── Confirm before leaving with unsaved changes ──────────────────
let changed = false;
document.querySelectorAll('.note-input').forEach(i => {
    i.addEventListener('input', () => changed = true);
});
window.addEventListener('beforeunload', function(e) {
    if (changed) {
        e.preventDefault();
        e.returnValue = '';
    }
});
document.getElementById('notes-form')?.addEventListener('submit', function() {
    changed = false;
});
</script>

<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\Project\gestion-CF\resources\views/controles/notes.blade.php ENDPATH**/ ?>