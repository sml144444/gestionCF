<?php $__env->startSection('title', $filiereId ? 'Stagiaires — '.($selectedFiliere->name ?? '') : 'Stagiaires'); ?>
<?php $__env->startSection('page-title', 'Stagiaires'); ?>

<?php $__env->startSection('content'); ?>
<?php
    $user    = Auth::user();
    $isAdmin = $user->role === 'admin';
    $accent  = $isAdmin ? '#0a6640' : '#1a4f8a';
    $accentMd= $isAdmin ? '#1a8c56' : '#2563eb';
    $light   = $isAdmin ? '#e8f5ee' : '#eff6ff';
    $text    = $isAdmin ? '#065f38' : '#1e40af';
    $border  = $isAdmin ? 'rgba(10,102,64,0.18)' : 'rgba(37,99,235,0.18)';
    $shadow  = $isAdmin ? 'rgba(10,102,64,0.14)' : 'rgba(26,79,138,0.14)';
    $grad    = $isAdmin
        ? 'linear-gradient(135deg,#0a6640 0%,#1a8c56 100%)'
        : 'linear-gradient(135deg,#1a4f8a 0%,#2563eb 100%)';
?>

<style>
/* ── CSS Variables ── */
:root {
    --accent:   <?php echo e($accent); ?>;
    --accent-md:<?php echo e($accentMd); ?>;
    --light:    <?php echo e($light); ?>;
    --text:     <?php echo e($text); ?>;
    --border:   <?php echo e($border); ?>;
    --shadow:   <?php echo e($shadow); ?>;
    --grad:     <?php echo e($grad); ?>;
}

/* ── Base ── */
.sg-wrap { font-family:'Segoe UI',system-ui,sans-serif; max-width:1100px; margin:0 auto; }

/* ── Inputs ── */
.sg-input {
    height:40px; padding:0 12px; border-radius:10px;
    border:1.5px solid #e2e8f0; background:#f8fafc;
    font-size:13px; color:#1e293b; outline:none;
    transition:border-color .15s, background .15s;
    box-sizing:border-box; width:100%;
}
.sg-input:focus { border-color:var(--accent); background:white; }

/* ── Table ── */
.sg-table { width:100%; border-collapse:collapse; }
.sg-table thead tr { background:var(--light); border-bottom:2px solid var(--border); }
.sg-table th {
    padding:11px 16px; font-size:9px; font-weight:800;
    color:var(--text); text-transform:uppercase;
    letter-spacing:1.5px; text-align:left; white-space:nowrap;
}
.sg-table tbody tr { border-bottom:1px solid #f1f5f9; transition:background .12s; }
.sg-table tbody tr:hover { background:var(--light); }
.sg-table td { padding:12px 16px; font-size:13px; color:#334155; vertical-align:middle; }

.sg-badge { display:inline-block; font-size:10px; font-weight:700; padding:3px 10px; border-radius:8px; }

.sg-avatar {
    width:36px; height:36px; border-radius:10px; flex-shrink:0;
    display:flex; align-items:center; justify-content:center;
    font-size:12px; font-weight:800;
    background:var(--light); color:var(--text); border:1px solid var(--border);
}

/* ── Année pills ── */
.annee-pill {
    display:inline-flex; align-items:center; gap:5px;
    padding:6px 14px; border-radius:99px; font-size:11px; font-weight:700;
    border:1.5px solid #e2e8f0; background:white; color:#64748b;
    cursor:pointer; text-decoration:none; transition:all .15s; white-space:nowrap;
}
.annee-pill:hover  { border-color:#8b5cf6; color:#6d28d9; background:#f5f3ff; }
.annee-pill.active { border-color:#8b5cf6; color:white;   background:#7c3aed; }

/* ── Occupancy bar ── */
.occ-bar  { height:5px; background:#e2e8f0; border-radius:99px; overflow:hidden; margin-top:6px; }
.occ-fill { height:100%; border-radius:99px; transition:width .4s ease; }

/* ── Group chips ── */
.grp-chip {
    display:inline-flex; align-items:center; gap:4px;
    font-size:10px; font-weight:600; padding:3px 9px;
    border-radius:7px; background:var(--light); color:var(--text);
    border:1px solid var(--border); margin:2px;
}

/* ═══════════════════════════════════════════════
   FILIÈRE CARDS — redesigned, glitch-free
═══════════════════════════════════════════════ */
.fil-grid {
    display:grid;
    grid-template-columns:repeat(auto-fill, minmax(300px, 1fr));
    gap:18px;
}

.fil-card {
    background:white;
    border-radius:18px;
    border:1.5px solid #e8edf5;
    overflow:hidden;
    text-decoration:none;
    display:flex;
    flex-direction:column;
    position:relative;
    transition:transform .2s ease, box-shadow .2s ease, border-color .2s ease;
    box-shadow:0 2px 8px rgba(0,0,0,0.05);
}
.fil-card:hover {
    transform:translateY(-3px);
    box-shadow:0 12px 32px var(--shadow);
    border-color:var(--accent);
}

/* Top gradient accent bar */
.fil-card-accent {
    height:6px;
    background:var(--grad);
    flex-shrink:0;
}

/* Header section */
.fil-card-header {
    padding:18px 20px 14px;
    display:flex;
    align-items:flex-start;
    justify-content:space-between;
    gap:12px;
}

.fil-card-title {
    font-size:15px;
    font-weight:800;
    color:#0f172a;
    line-height:1.3;
    margin:0 0 4px;
}
.fil-card-subtitle {
    font-size:11px;
    color:#64748b;
    margin:0;
}

/* Count badge */
.fil-count-badge {
    min-width:52px;
    height:52px;
    border-radius:14px;
    background:var(--light);
    border:1.5px solid var(--border);
    display:flex;
    flex-direction:column;
    align-items:center;
    justify-content:center;
    flex-shrink:0;
    transition:background .2s, border-color .2s;
}
.fil-card:hover .fil-count-badge {
    background:var(--grad);
    border-color:transparent;
}
.fil-count-num {
    font-size:20px;
    font-weight:800;
    color:var(--accent);
    line-height:1;
    transition:color .2s;
}
.fil-card:hover .fil-count-num { color:white; }
.fil-count-lbl {
    font-size:8px;
    font-weight:700;
    color:var(--text);
    text-transform:uppercase;
    letter-spacing:.4px;
    transition:color .2s;
}
.fil-card:hover .fil-count-lbl { color:rgba(255,255,255,0.85); }

/* Occupancy row */
.fil-occ-row {
    padding:0 20px 14px;
}
.fil-occ-labels {
    display:flex;
    justify-content:space-between;
    font-size:10px;
    color:#94a3b8;
    margin-bottom:5px;
}
.fil-occ-labels strong { font-weight:700; }

/* Groups section */
.fil-groups-section {
    padding:0 20px 16px;
    flex:1;
}
.fil-annee-label {
    font-size:9px;
    font-weight:800;
    color:#94a3b8;
    text-transform:uppercase;
    letter-spacing:1px;
    margin-bottom:6px;
    margin-top:10px;
}
.fil-annee-label:first-child { margin-top:0; }

.fil-grp-chips { display:flex; flex-wrap:wrap; gap:4px; }

.fil-grp-chip {
    display:inline-flex;
    align-items:center;
    gap:5px;
    padding:4px 10px;
    border-radius:8px;
    background:#f8fafc;
    border:1px solid #e2e8f0;
    font-size:10px;
    font-weight:600;
    color:#334155;
    transition:background .15s, border-color .15s, color .15s;
}
.fil-card:hover .fil-grp-chip {
    background:var(--light);
    border-color:var(--border);
    color:var(--text);
}
.fil-grp-count {
    font-size:9px;
    font-weight:800;
    padding:1px 5px;
    border-radius:5px;
    background:#e2e8f0;
    color:#64748b;
    transition:background .15s, color .15s;
}
.fil-card:hover .fil-grp-count {
    background:var(--accent);
    color:white;
}

/* Footer */
.fil-card-footer {
    padding:12px 20px;
    border-top:1px solid #f1f5f9;
    background:#fafbfc;
    display:flex;
    align-items:center;
    justify-content:space-between;
    flex-shrink:0;
    transition:background .2s;
}
.fil-card:hover .fil-card-footer {
    background:var(--light);
    border-top-color:var(--border);
}
.fil-footer-text {
    font-size:10px;
    color:#94a3b8;
    font-weight:500;
}
.fil-footer-cta {
    font-size:11px;
    font-weight:700;
    color:var(--accent);
    display:flex;
    align-items:center;
    gap:5px;
}
.fil-footer-cta svg { transition:transform .2s; }
.fil-card:hover .fil-footer-cta svg { transform:translateX(3px); }

/* ── CRUD Action buttons ── */
.sg-btn-edit {
    display:inline-flex; align-items:center; gap:6px;
    height:34px; padding:0 14px; border-radius:9px; border:none;
    background:#eff6ff; color:#2563eb;
    font-size:12px; font-weight:700; cursor:pointer;
    transition:background .15s, color .15s, box-shadow .15s;
    white-space:nowrap;
}
.sg-btn-edit:hover { background:#2563eb; color:white; box-shadow:0 4px 12px rgba(37,99,235,.3); }

.sg-btn-delete {
    display:inline-flex; align-items:center; gap:6px;
    height:34px; padding:0 14px; border-radius:9px; border:none;
    background:#fff1f2; color:#e11d48;
    font-size:12px; font-weight:700; cursor:pointer;
    transition:background .15s, color .15s, box-shadow .15s;
    white-space:nowrap;
}
.sg-btn-delete:hover { background:#e11d48; color:white; box-shadow:0 4px 12px rgba(225,29,72,.3); }

/* ── Modals ── */
.sg-overlay {
    position:fixed; inset:0; background:rgba(15,23,42,.55);
    display:flex; align-items:center; justify-content:center;
    z-index:999; padding:16px; backdrop-filter:blur(3px);
}
.sg-modal {
    background:white; border-radius:20px; padding:28px 28px 24px;
    width:100%; max-width:540px; max-height:90vh; overflow-y:auto;
    box-shadow:0 24px 72px rgba(0,0,0,.22); position:relative;
    animation:modalIn .2s ease;
}
@keyframes modalIn {
    from { opacity:0; transform:translateY(12px) scale(.98); }
    to   { opacity:1; transform:none; }
}
.sg-modal-title { font-size:16px; font-weight:800; color:#0f172a; margin:0 0 22px; padding-right:24px; }
.sg-modal label { display:block; font-size:9px; font-weight:800; color:#94a3b8; text-transform:uppercase; letter-spacing:1.5px; margin-bottom:5px; }
.sg-modal .sg-input { width:100%; }
.sg-modal .field { margin-bottom:14px; }
.sg-grid-2 { display:grid; grid-template-columns:1fr 1fr; gap:12px; }
.sg-modal .err { color:#e11d48; font-size:10px; margin-top:4px; display:block; }
.sg-modal-close {
    position:absolute; top:16px; right:16px; width:28px; height:28px;
    border-radius:8px; border:none; background:#f1f5f9; color:#64748b;
    cursor:pointer; font-size:14px; display:flex; align-items:center; justify-content:center;
    transition:background .15s, color .15s;
}
.sg-modal-close:hover { background:#e2e8f0; color:#0f172a; }
.sg-modal-footer { display:flex; gap:8px; justify-content:flex-end; margin-top:22px; padding-top:16px; border-top:1px solid #f1f5f9; }

.sg-btn-primary {
    height:40px; padding:0 20px; border-radius:10px; border:none;
    background:var(--grad); color:white; font-size:13px; font-weight:700;
    cursor:pointer; display:inline-flex; align-items:center; gap:6px;
    transition:opacity .15s; box-shadow:0 4px 14px var(--shadow);
}
.sg-btn-primary:hover { opacity:.88; }

.sg-btn-danger-modal {
    height:40px; padding:0 20px; border-radius:10px; border:none;
    background:#e11d48; color:white; font-size:13px; font-weight:700;
    cursor:pointer; display:inline-flex; align-items:center; gap:6px; transition:opacity .15s;
}
.sg-btn-danger-modal:hover { opacity:.88; }

.sg-btn-ghost {
    height:40px; padding:0 14px; border-radius:10px;
    border:1.5px solid #e2e8f0; background:white; color:#64748b;
    font-size:13px; font-weight:600; cursor:pointer; text-decoration:none;
    display:inline-flex; align-items:center; transition:all .15s;
}
.sg-btn-ghost:hover { border-color:#94a3b8; color:#334155; }

/* Empty state */
.fil-empty {
    grid-column:1/-1;
    padding:64px;
    text-align:center;
    background:white;
    border-radius:18px;
    border:1.5px dashed #e2e8f0;
}

@media(max-width:600px) {
    .sg-grid-2 { grid-template-columns:1fr; }
    .fil-grid  { grid-template-columns:1fr; }
}
</style>

<div class="sg-wrap">


<?php if(session('success')): ?>
<div style="margin-bottom:16px;padding:12px 16px;border-radius:12px;font-size:13px;
            display:flex;align-items:center;gap:8px;
            background:#f0fdf4;border:1px solid #bbf7d0;color:#15803d;">
    <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
    </svg>
    <?php echo e(session('success')); ?>

</div>
<?php endif; ?>

<?php if($errors->any()): ?>
<div style="margin-bottom:16px;padding:12px 16px;border-radius:12px;font-size:13px;
            background:#fff1f2;border:1px solid #fecdd3;color:#be123c;">
    <strong style="font-weight:700;">Erreurs de validation :</strong>
    <ul style="margin:6px 0 0 16px;padding:0;">
        <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $e): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <li style="margin-bottom:3px;"><?php echo e($e); ?></li>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </ul>
</div>
<?php endif; ?>



<?php if(!$filiereId): ?>


<div style="display:flex;align-items:flex-start;justify-content:space-between;
            flex-wrap:wrap;gap:12px;margin-bottom:28px;">
    <div>
        <h1 style="font-size:22px;font-weight:800;color:#0f172a;margin:0;">Stagiaires</h1>
        <p style="font-size:12px;color:#64748b;margin:5px 0 0;">
            Sélectionnez une filière pour voir et gérer ses stagiaires
        </p>
    </div>
    <div style="padding:12px 20px;border-radius:14px;background:var(--grad);
                text-align:center;box-shadow:0 4px 16px var(--shadow);">
        <div style="font-size:28px;font-weight:800;color:white;line-height:1;">
            <?php echo e($totalStagiaires); ?>

        </div>
        <div style="font-size:9px;font-weight:700;color:rgba(255,255,255,0.8);
                    text-transform:uppercase;letter-spacing:.8px;margin-top:3px;">
            Stagiaires total
        </div>
    </div>
</div>


<div class="fil-grid">
    <?php $__empty_1 = true; $__currentLoopData = $filieres; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $filiere): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
    <?php
        $grps   = $filiere->groupes;
        $total  = $filiere->stagiaires_count;
        $cap    = $grps->sum('nbr_limit');
        $occ    = $cap > 0 ? min(100, round(($total / $cap) * 100)) : 0;
        $occClr = $occ >= 90 ? '#dc2626' : ($occ >= 70 ? '#f59e0b' : '#16a34a');
        $grps1  = $grps->where('annee', 1);
        $grps2  = $grps->where('annee', 2);
        $grps3  = $grps->where('annee', 3);
    ?>

    <a href="<?php echo e(route('stagiaire.index', ['filiere_id' => $filiere->id])); ?>"
       class="fil-card">

        
        <div class="fil-card-accent"></div>

        
        <div class="fil-card-header">
            <div style="flex:1;min-width:0;">
                <p class="fil-card-title"><?php echo e($filiere->name); ?></p>
                <p class="fil-card-subtitle">
                    <?php echo e($filiere->duree); ?> an<?php echo e($filiere->duree > 1 ? 's' : ''); ?> de formation
                    &nbsp;·&nbsp; <?php echo e($grps->count()); ?> groupe<?php echo e($grps->count() > 1 ? 's' : ''); ?>

                </p>
            </div>
            <div class="fil-count-badge">
                <span class="fil-count-num"><?php echo e($total); ?></span>
                <span class="fil-count-lbl">élèves</span>
            </div>
        </div>

        
        <div class="fil-occ-row">
            <div class="fil-occ-labels">
                <span><?php echo e($total); ?> / <?php echo e($cap); ?> places occupées</span>
                <strong style="color:<?php echo e($occClr); ?>;"><?php echo e($occ); ?>%</strong>
            </div>
            <div class="occ-bar">
                <div class="occ-fill" style="width:<?php echo e($occ); ?>%;background:<?php echo e($occClr); ?>;"></div>
            </div>
        </div>

        
        <?php if($grps->isNotEmpty()): ?>
        <div class="fil-groups-section">
            <?php if($grps1->isNotEmpty()): ?>
                <div class="fil-annee-label">1ère année</div>
                <div class="fil-grp-chips">
                    <?php $__currentLoopData = $grps1; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $g): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <span class="fil-grp-chip">
                        <?php echo e($g->name); ?>

                        <span class="fil-grp-count"><?php echo e($g->stagiaires_count); ?></span>
                    </span>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            <?php endif; ?>
            <?php if($grps2->isNotEmpty()): ?>
                <div class="fil-annee-label">2ème année</div>
                <div class="fil-grp-chips">
                    <?php $__currentLoopData = $grps2; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $g): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <span class="fil-grp-chip">
                        <?php echo e($g->name); ?>

                        <span class="fil-grp-count"><?php echo e($g->stagiaires_count); ?></span>
                    </span>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            <?php endif; ?>
            <?php if($grps3->isNotEmpty()): ?>
                <div class="fil-annee-label">3ème année</div>
                <div class="fil-grp-chips">
                    <?php $__currentLoopData = $grps3; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $g): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <span class="fil-grp-chip">
                        <?php echo e($g->name); ?>

                        <span class="fil-grp-count"><?php echo e($g->stagiaires_count); ?></span>
                    </span>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            <?php endif; ?>
        </div>
        <?php else: ?>
        <div class="fil-groups-section">
            <p style="font-size:11px;color:#94a3b8;font-style:italic;margin:0;">Aucun groupe créé</p>
        </div>
        <?php endif; ?>

        
        <div class="fil-card-footer">
            <span class="fil-footer-text">
                <?php echo e($cap - $total); ?> place<?php echo e(($cap - $total) > 1 ? 's' : ''); ?> libre<?php echo e(($cap - $total) > 1 ? 's' : ''); ?>

            </span>
            <span class="fil-footer-cta">
                Voir les stagiaires
                <svg width="12" height="12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/>
                </svg>
            </span>
        </div>
    </a>

    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
    <div class="fil-empty">
        <div style="font-size:40px;margin-bottom:14px;">🏫</div>
        <p style="font-size:15px;font-weight:700;color:#334155;margin:0 0 6px;">
            Aucune filière créée
        </p>
        <p style="font-size:12px;color:#94a3b8;margin:0;">
            Les filières apparaîtront ici une fois configurées.
        </p>
    </div>
    <?php endif; ?>
</div>



<?php else: ?>


<div style="display:flex;align-items:center;gap:10px;margin-bottom:20px;flex-wrap:wrap;">
    <a href="<?php echo e(route('stagiaire.index')); ?>"
       style="display:inline-flex;align-items:center;gap:6px;padding:8px 14px;
              border-radius:10px;border:1.5px solid #e2e8f0;background:white;
              color:#475569;font-size:12px;font-weight:600;text-decoration:none;
              transition:all .15s;"
       onmouseover="this.style.borderColor='<?php echo e($accent); ?>';this.style.color='<?php echo e($text); ?>'"
       onmouseout="this.style.borderColor='#e2e8f0';this.style.color='#475569'">
        <svg width="12" height="12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"/>
        </svg>
        Toutes les filières
    </a>

    <?php $__currentLoopData = $filieres; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $f): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
    <a href="<?php echo e(route('stagiaire.index', ['filiere_id' => $f->id])); ?>"
       style="display:inline-flex;align-items:center;gap:5px;padding:6px 12px;
              border-radius:99px;font-size:11px;font-weight:600;text-decoration:none;
              transition:all .15s;white-space:nowrap;
              <?php echo e($f->id == $filiereId
                    ? 'background:var(--grad);color:white;border:1.5px solid transparent;box-shadow:0 3px 10px var(--shadow);'
                    : 'background:white;color:#64748b;border:1.5px solid #e2e8f0;'); ?>">
        <?php echo e($f->name); ?>

        <span style="font-size:9px;padding:1px 5px;border-radius:99px;font-weight:800;
                     <?php echo e($f->id == $filiereId
                           ? 'background:rgba(255,255,255,0.25);color:white;'
                           : 'background:var(--light);color:var(--text);'); ?>">
            <?php echo e($f->stagiaires_count); ?>

        </span>
    </a>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
</div>


<div style="display:flex;align-items:flex-start;justify-content:space-between;
            flex-wrap:wrap;gap:12px;margin-bottom:20px;">
    <div>
        <h1 style="font-size:20px;font-weight:800;color:#0f172a;margin:0;">
            <?php echo e($selectedFiliere->name ?? 'Filière'); ?>

        </h1>
        <p style="font-size:12px;color:#64748b;margin:4px 0 0;">
            <?php echo e($stagiaires->total()); ?> stagiaire<?php echo e($stagiaires->total() > 1 ? 's' : ''); ?>

            <?php if($hasFilters): ?>
                <span style="color:var(--accent);font-weight:600;">(filtrés)</span>
            <?php endif; ?>
        </p>
    </div>
    <div style="display:flex;gap:10px;flex-wrap:wrap;align-items:center;">
        <div style="padding:8px 14px;border-radius:12px;background:var(--light);
                    border:1px solid var(--border);text-align:center;">
            <div style="font-size:20px;font-weight:800;color:var(--accent);">
                <?php echo e($stagiaires->total()); ?>

            </div>
            <div style="font-size:9px;font-weight:700;color:var(--text);
                        text-transform:uppercase;letter-spacing:.5px;">Résultats</div>
        </div>

        <?php $groupesStat = $groupes->groupBy('annee'); ?>
        <?php $__currentLoopData = $groupesStat; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $yr => $grpList): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <div style="padding:8px 14px;border-radius:12px;background:#f8fafc;
                    border:1px solid #e2e8f0;text-align:center;">
            <div style="font-size:20px;font-weight:800;color:#334155;">
                <?php echo e($grpList->count()); ?>

            </div>
            <div style="font-size:9px;font-weight:700;color:#64748b;
                        text-transform:uppercase;letter-spacing:.5px;">Grp An.<?php echo e($yr); ?></div>
        </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>
</div>


<?php if($hasAnneeScolaireColumn && $anneesScolaires->count()): ?>
<div style="margin-bottom:14px;">
    <div style="font-size:9px;font-weight:800;color:#7c3aed;text-transform:uppercase;
                letter-spacing:1.5px;margin-bottom:8px;display:flex;align-items:center;gap:5px;">
        <svg width="10" height="10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                  d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
        </svg>
        Année scolaire
    </div>
    <div style="display:flex;gap:8px;flex-wrap:wrap;">
        <a href="<?php echo e(route('stagiaire.index', array_merge(request()->except('annee_scolaire','page'), ['filiere_id' => $filiereId]))); ?>"
           class="annee-pill <?php echo e($anneeScolaire === '' ? 'active' : ''); ?>">Toutes</a>
        <?php $__currentLoopData = $anneesScolaires; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $as): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <a href="<?php echo e(route('stagiaire.index', array_merge(request()->except('annee_scolaire','page'), ['filiere_id' => $filiereId, 'annee_scolaire' => $as]))); ?>"
           class="annee-pill <?php echo e($anneeScolaire === $as ? 'active' : ''); ?>">📅 <?php echo e($as); ?></a>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>
</div>
<?php endif; ?>


<form method="GET" action="<?php echo e(route('stagiaire.index')); ?>"
      style="display:flex;gap:10px;flex-wrap:wrap;align-items:flex-end;margin-bottom:18px;
             padding:14px 16px;background:white;border-radius:14px;border:1px solid #e2e8f0;
             box-shadow:0 1px 4px rgba(0,0,0,0.04);">
    <input type="hidden" name="filiere_id" value="<?php echo e($filiereId); ?>">
    <?php if($anneeScolaire !== ''): ?>
    <input type="hidden" name="annee_scolaire" value="<?php echo e($anneeScolaire); ?>">
    <?php endif; ?>

    <div style="flex:2;min-width:180px;">
        <label style="display:block;font-size:9px;font-weight:800;color:#94a3b8;
                      text-transform:uppercase;letter-spacing:1.5px;margin-bottom:5px;">
            Recherche
        </label>
        <div style="position:relative;">
            <svg width="13" height="13" fill="none" stroke="#94a3b8" viewBox="0 0 24 24"
                 style="position:absolute;left:11px;top:50%;transform:translateY(-50%);">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
            </svg>
            <input type="text" name="search" value="<?php echo e($search); ?>"
                   placeholder="Nom, email ou CIN…"
                   class="sg-input" style="padding-left:32px;">
        </div>
    </div>

    <div style="flex:1;min-width:140px;">
        <label style="display:block;font-size:9px;font-weight:800;color:#94a3b8;
                      text-transform:uppercase;letter-spacing:1.5px;margin-bottom:5px;">Groupe</label>
        <select name="groupe_id" class="sg-input" style="appearance:none;cursor:pointer;">
            <option value="">Tous</option>
            <?php $__currentLoopData = $groupes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $g): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <option value="<?php echo e($g->id); ?>" <?php echo e($groupeId == $g->id ? 'selected' : ''); ?>>
                <?php echo e($g->name); ?> (An.<?php echo e($g->annee); ?>)
            </option>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </select>
    </div>

    <div style="flex:1;min-width:130px;">
        <label style="display:block;font-size:9px;font-weight:800;color:#94a3b8;
                      text-transform:uppercase;letter-spacing:1.5px;margin-bottom:5px;">
            Année d'étude
        </label>
        <select name="annee" class="sg-input" style="appearance:none;cursor:pointer;">
            <option value="">Toutes</option>
            <option value="1" <?php echo e($annee == 1 ? 'selected' : ''); ?>>1ère année</option>
            <option value="2" <?php echo e($annee == 2 ? 'selected' : ''); ?>>2ème année</option>
            <option value="3" <?php echo e($annee == 3 ? 'selected' : ''); ?>>3ème année</option>
        </select>
    </div>

    <?php if(isset($promos) && $promos->count()): ?>
    <div style="flex:1;min-width:130px;">
        <label style="display:block;font-size:9px;font-weight:800;color:#94a3b8;
                      text-transform:uppercase;letter-spacing:1.5px;margin-bottom:5px;">
            Promotion
        </label>
        <select name="promo" class="sg-input" style="appearance:none;cursor:pointer;">
            <option value="">Toutes</option>
            <?php $__currentLoopData = $promos; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <option value="<?php echo e($p); ?>" <?php echo e($promo == $p ? 'selected' : ''); ?>>Promo <?php echo e($p); ?></option>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </select>
    </div>
    <?php endif; ?>

    <?php if($hasAnneeScolaireColumn && $anneesScolaires->count()): ?>
    <div style="flex:1;min-width:140px;">
        <label style="display:block;font-size:9px;font-weight:800;color:#94a3b8;
                      text-transform:uppercase;letter-spacing:1.5px;margin-bottom:5px;">
            Promo / Saison
        </label>
        <select name="annee_scolaire" class="sg-input" style="appearance:none;cursor:pointer;">
            <option value="">Toutes les promos</option>
            <?php $__currentLoopData = $anneesScolaires; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $as): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <option value="<?php echo e($as); ?>" <?php echo e($anneeScolaire === $as ? 'selected' : ''); ?>>
                📅 <?php echo e($as); ?>

            </option>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </select>
    </div>
    <?php endif; ?>

    <div style="display:flex;gap:8px;align-items:flex-end;">
        <button type="submit"
                style="height:40px;padding:0 16px;border-radius:10px;border:none;
                       background:var(--grad);color:white;font-size:13px;font-weight:600;
                       cursor:pointer;display:inline-flex;align-items:center;gap:6px;
                       box-shadow:0 3px 10px var(--shadow);">
            <svg width="12" height="12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                      d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
            </svg>
            Filtrer
        </button>
        <?php if($hasFilters): ?>
        <a href="<?php echo e(route('stagiaire.index', ['filiere_id' => $filiereId] + ($anneeScolaire ? ['annee_scolaire' => $anneeScolaire] : []))); ?>"
           style="height:40px;padding:0 12px;border-radius:10px;border:1.5px solid #e2e8f0;
                  background:white;color:#64748b;font-size:13px;font-weight:600;
                  text-decoration:none;display:inline-flex;align-items:center;
                  transition:all .15s;">
            ✕ Reset
        </a>
        <?php endif; ?>
    </div>
</form>


<div style="background:white;border-radius:16px;border:1px solid #e2e8f0;
            box-shadow:0 2px 12px rgba(0,0,0,0.05);overflow:hidden;">
    <?php if($stagiaires->isEmpty()): ?>
    <div style="padding:64px;text-align:center;">
        <div style="font-size:32px;margin-bottom:12px;">👥</div>
        <p style="font-size:14px;font-weight:700;color:#334155;margin:0 0 4px;">
            Aucun stagiaire trouvé
        </p>
        <p style="font-size:12px;color:#94a3b8;margin:0;">
            Essayez de modifier les filtres.
        </p>
    </div>
    <?php else: ?>
    <div style="overflow-x:auto;">
        <table class="sg-table">
            <thead>
                <tr>
                    <th style="padding-left:20px;width:48px;">#</th>
                    <th>Stagiaire</th>
                    <th>Groupe</th>
                    <th>Année</th>
                    <?php if($hasAnneeScolaireColumn): ?> <th>Saison</th> <?php endif; ?>
                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->any(['stagiaire-edit','stagiaire-delete'])): ?>
                    <th style="text-align:center;">Actions</th>
                    <?php endif; ?>
                </tr>
            </thead>
            <tbody>
                <?php $__currentLoopData = $stagiaires; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $stagiaire): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php
                    $initials   = strtoupper(substr($stagiaire->name, 0, 1))
                                . strtoupper(substr(explode(' ', $stagiaire->name.' ')[1] ?? '', 0, 1));
                    $anneeValue = $stagiaire->groupe?->annee;
                    $anneeLabel = match($anneeValue) {
                        1 => '1ère année', 2 => '2ème année', 3 => '3ème année',
                        default => 'Non assigné'
                    };
                    $anneeStyle = match($anneeValue) {
                        1 => 'background:#eff6ff;color:#1e40af;border:1px solid #bfdbfe;',
                        2 => 'background:#fdf4ff;color:#6b21a8;border:1px solid #e9d5ff;',
                        3 => 'background:#fff7ed;color:#c2410c;border:1px solid #fed7aa;',
                        default => 'background:#f1f5f9;color:#64748b;border:1px solid #e2e8f0;'
                    };
                    $groupeAS = $hasAnneeScolaireColumn ? ($stagiaire->groupe?->annee_scolaire ?? null) : null;
                ?>
                <tr>
                    <td style="padding-left:20px;color:#94a3b8;font-size:11px;font-weight:700;">
                        <?php echo e($stagiaires->firstItem() + $i); ?>

                    </td>
                    <td>
                        <div style="display:flex;align-items:center;gap:10px;min-width:0;">
                            <div class="sg-avatar"><?php echo e($initials); ?></div>
                            <div style="min-width:0;">
                                <div style="font-size:13px;font-weight:700;color:#0f172a;
                                            white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">
                                    <?php echo e($stagiaire->name); ?>

                                </div>
                                <div style="font-size:11px;color:#64748b;
                                            white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">
                                    <?php echo e($stagiaire->email); ?>

                                </div>
                                <?php if($stagiaire->cin): ?>
                                <div style="font-size:10px;color:#94a3b8;">
                                    CIN : <?php echo e($stagiaire->cin); ?>

                                </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </td>
                    <td>
                        <?php if($stagiaire->groupe): ?>
                        <span class="sg-badge"
                              style="background:#f1f5f9;color:#334155;border:1px solid #e2e8f0;">
                            <?php echo e($stagiaire->groupe->name ?? 'G'.$stagiaire->groupe->id); ?>

                        </span>
                        <?php else: ?>
                        <span style="font-size:11px;color:#94a3b8;font-style:italic;">Non assigné</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <span class="sg-badge" style="<?php echo e($anneeStyle); ?>"><?php echo e($anneeLabel); ?></span>
                    </td>
                    <?php if($hasAnneeScolaireColumn): ?>
                    <td>
                        <?php if($groupeAS): ?>
                        <span style="font-size:10px;font-weight:600;color:#7c3aed;">
                            📅 <?php echo e($groupeAS); ?>

                        </span>
                        <?php else: ?>
                        <span style="font-size:11px;color:#94a3b8;">—</span>
                        <?php endif; ?>
                    </td>
                    <?php endif; ?>

                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->any(['stagiaire-edit','stagiaire-delete'])): ?>
                    <td>
                        <div style="display:flex;align-items:center;justify-content:center;gap:8px;">
                            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('stagiaire-edit')): ?>
                            <button type="button"
                                    class="sg-btn-edit"
                                    onclick="openEditModal(this)"
                                    data-id="<?php echo e($stagiaire->id); ?>"
                                    data-name="<?php echo e(e($stagiaire->name)); ?>"
                                    data-email="<?php echo e($stagiaire->email); ?>"
                                    data-cin="<?php echo e($stagiaire->cin ?? ''); ?>"
                                    data-phone="<?php echo e($stagiaire->phone ?? ''); ?>"
                                    data-dob="<?php echo e($stagiaire->date_naissance?->format('Y-m-d') ?? ''); ?>"
                                    data-groupe="<?php echo e($stagiaire->id_groupe ?? ''); ?>">
                                <svg width="13" height="13" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5
                                             m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                </svg>
                                Modifier
                            </button>
                            <?php endif; ?>

                            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('stagiaire-delete')): ?>
                            <button type="button"
                                    class="sg-btn-delete"
                                    onclick="openDeleteModal(<?php echo e($stagiaire->id); ?>, '<?php echo e(addslashes($stagiaire->name)); ?>')">
                                <svg width="13" height="13" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858
                                             L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                </svg>
                                Supprimer
                            </button>
                            <?php endif; ?>
                        </div>
                    </td>
                    <?php endif; ?>
                </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </tbody>
        </table>
    </div>
    <?php endif; ?>
</div>


<?php if($stagiaires && $stagiaires->hasPages()): ?>
<div style="margin-top:16px;display:flex;justify-content:center;">
    <?php echo e($stagiaires->links()); ?>

</div>
<?php endif; ?>





<?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('stagiaire-create')): ?>
<div id="modal-create" class="sg-overlay" style="display:none;"
     onclick="if(event.target===this)closeModal('modal-create')">
    <div class="sg-modal">
        <button type="button" class="sg-modal-close" onclick="closeModal('modal-create')">✕</button>
        <h2 class="sg-modal-title">
            <span style="display:inline-flex;align-items:center;gap:8px;">
                <span style="width:28px;height:28px;border-radius:8px;background:var(--light);
                             display:inline-flex;align-items:center;justify-content:center;
                             font-size:14px;">👤</span>
                Nouveau stagiaire
            </span>
        </h2>

        <div style="margin-bottom:18px;padding:12px 14px;border-radius:12px;
                    background:#f0fdf4;border:1.5px solid #bbf7d0;
                    display:flex;align-items:flex-start;gap:10px;">
            <span style="font-size:18px;flex-shrink:0;margin-top:1px;">📧</span>
            <div>
                <div style="font-size:11px;font-weight:700;color:#15803d;margin-bottom:3px;">
                    Mot de passe généré automatiquement
                </div>
                <div style="font-size:10px;color:#166534;line-height:1.5;">
                    Un mot de passe sécurisé sera généré et envoyé à l'adresse e-mail du stagiaire.
                </div>
            </div>
        </div>

        <form method="POST" action="<?php echo e(route('stagiaire.store')); ?>">
            <?php echo csrf_field(); ?>
            <input type="hidden" name="_modal" value="create">
            <input type="hidden" name="id_filiere" value="<?php echo e($filiereId); ?>">

            <div class="field" style="grid-column:1/-1;">
                <label>Nom complet <span style="color:#e11d48;">*</span></label>
                <input type="text" name="name" class="sg-input" value="<?php echo e(old('name')); ?>"
                       placeholder="Ex: Youssef Ait Ali" required autofocus>
                <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="err"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>

            <div class="field" style="margin-top:14px;">
                <label>Email <span style="color:#e11d48;">*</span></label>
                <input type="email" name="email" class="sg-input" value="<?php echo e(old('email')); ?>"
                       placeholder="email@exemple.com" required>
                <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="err"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>

            <div class="sg-grid-2" style="margin-top:14px;">
                <div class="field">
                    <label>CIN</label>
                    <input type="text" name="cin" class="sg-input" value="<?php echo e(old('cin')); ?>"
                           placeholder="Ex: BE123456">
                    <?php $__errorArgs = ['cin'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="err"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>
                <div class="field">
                    <label>Téléphone</label>
                    <input type="tel" name="phone" class="sg-input" value="<?php echo e(old('phone')); ?>"
                           placeholder="+212 6XX XXX XXX"
                           inputmode="tel" pattern="[\+0-9\s\-\(\)\.]{6,20}" maxlength="20"
                           oninput="this.value=this.value.replace(/[^+0-9\s\-\(\)\.]/g,'')">
                    <?php $__errorArgs = ['phone'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="err"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>
            </div>

            <div class="sg-grid-2" style="margin-top:0;">
                <div class="field">
                    <label>Date de naissance</label>
                    <input type="date" name="date_naissance" class="sg-input"
                           value="<?php echo e(old('date_naissance')); ?>">
                    <?php $__errorArgs = ['date_naissance'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="err"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>
                <div class="field">
                    <label>Groupe</label>
                    <select name="id_groupe" class="sg-input" style="appearance:none;cursor:pointer;">
                        <option value="">— Aucun —</option>
                        <?php $__currentLoopData = $allGroupes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $g): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php $full = $g->stagiaires_count >= $g->nbr_limit; ?>
                            <option value="<?php echo e($g->id); ?>"
                                    <?php echo e(old('id_groupe') == $g->id ? 'selected' : ''); ?>

                                    <?php echo e($full ? 'disabled' : ''); ?>>
                                <?php echo e($g->name); ?> (An.<?php echo e($g->annee); ?>) — <?php echo e($g->stagiaires_count); ?>/<?php echo e($g->nbr_limit); ?>

                                <?php echo e($full ? '⛔ COMPLET' : ($g->nbr_limit - $g->stagiaires_count).' libre(s)'); ?>

                            </option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                    <?php $__errorArgs = ['id_groupe'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="err"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>
            </div>

            <div class="sg-modal-footer">
                <button type="button" class="sg-btn-ghost" onclick="closeModal('modal-create')">
                    Annuler
                </button>
                <button type="submit" class="sg-btn-primary">
                    <svg width="13" height="13" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/>
                    </svg>
                    Créer le stagiaire
                </button>
            </div>
        </form>
    </div>
</div>
<?php endif; ?>


<?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('stagiaire-edit')): ?>
<div id="modal-edit" class="sg-overlay" style="display:none;"
     onclick="if(event.target===this)closeModal('modal-edit')">
    <div class="sg-modal">
        <button type="button" class="sg-modal-close" onclick="closeModal('modal-edit')">✕</button>
        <h2 class="sg-modal-title">
            <span style="display:inline-flex;align-items:center;gap:8px;">
                <span style="width:28px;height:28px;border-radius:8px;background:#eff6ff;
                             display:inline-flex;align-items:center;justify-content:center;
                             font-size:14px;">✏️</span>
                Modifier le stagiaire
            </span>
        </h2>
        <form id="form-edit" method="POST" action="#">
            <?php echo csrf_field(); ?>
            <?php echo method_field('PUT'); ?>
            <input type="hidden" name="_modal" value="edit">
            <input type="hidden" name="_stagiaire_id" id="edit-sid" value="<?php echo e(old('_stagiaire_id')); ?>">
            <input type="hidden" name="id_filiere" value="<?php echo e($filiereId); ?>">

            <div class="sg-grid-2">
                <div class="field">
                    <label>Nom complet <span style="color:#e11d48;">*</span></label>
                    <input type="text" name="name" id="edit-name" class="sg-input"
                           value="<?php echo e(old('name','')); ?>" placeholder="Nom complet" required>
                    <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="err"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>
                <div class="field">
                    <label>Email <span style="color:#e11d48;">*</span></label>
                    <input type="email" name="email" id="edit-email" class="sg-input"
                           value="<?php echo e(old('email','')); ?>" placeholder="email@exemple.com" required>
                    <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="err"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>
            </div>

            <div class="sg-grid-2">
                <div class="field">
                    <label>
                        Nouveau mot de passe
                        <span style="font-weight:400;text-transform:none;letter-spacing:0;color:#94a3b8;">
                            (vide = inchangé)
                        </span>
                    </label>
                    <input type="password" name="password" class="sg-input"
                           placeholder="Nouveau mot de passe…">
                    <?php $__errorArgs = ['password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="err"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>
                <div class="field">
                    <label>CIN</label>
                    <input type="text" name="cin" id="edit-cin" class="sg-input"
                           value="<?php echo e(old('cin','')); ?>" placeholder="BE123456">
                    <?php $__errorArgs = ['cin'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="err"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>
            </div>

            <div class="sg-grid-2">
                <div class="field">
                    <label>Téléphone</label>
                    <input type="tel" name="phone" id="edit-phone" class="sg-input"
                           value="<?php echo e(old('phone','')); ?>" placeholder="+212 6XX XXX XXX"
                           inputmode="tel" pattern="[\+0-9\s\-\(\)\.]{6,20}" maxlength="20"
                           oninput="this.value=this.value.replace(/[^+0-9\s\-\(\)\.]/g,'')">
                    <?php $__errorArgs = ['phone'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="err"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>
                <div class="field">
                    <label>Date de naissance</label>
                    <input type="date" name="date_naissance" id="edit-dob" class="sg-input"
                           value="<?php echo e(old('date_naissance','')); ?>">
                    <?php $__errorArgs = ['date_naissance'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="err"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>
            </div>

            <div class="field">
                <label>Groupe</label>
                <select name="id_groupe" id="edit-groupe" class="sg-input"
                        style="appearance:none;cursor:pointer;">
                    <option value="">— Aucun —</option>
                    <?php $__currentLoopData = $allGroupes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $g): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php
                            $full           = $g->stagiaires_count >= $g->nbr_limit;
                            $isCurrentGroup = old('id_groupe', '') == $g->id;
                        ?>
                        <option value="<?php echo e($g->id); ?>"
                                <?php echo e($isCurrentGroup ? 'selected' : ''); ?>

                                <?php echo e(($full && !$isCurrentGroup) ? 'disabled' : ''); ?>>
                            <?php echo e($g->name); ?> (An.<?php echo e($g->annee); ?>) — <?php echo e($g->stagiaires_count); ?>/<?php echo e($g->nbr_limit); ?>

                            <?php echo e($full ? '⛔ COMPLET' : ($g->nbr_limit - $g->stagiaires_count).' libre(s)'); ?>

                        </option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
                <?php $__errorArgs = ['id_groupe'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="err"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>

            <div class="sg-modal-footer">
                <button type="button" class="sg-btn-ghost" onclick="closeModal('modal-edit')">
                    Annuler
                </button>
                <button type="submit" class="sg-btn-primary">
                    <svg width="13" height="13" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                    </svg>
                    Enregistrer
                </button>
            </div>
        </form>
    </div>
</div>
<?php endif; ?>


<?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('stagiaire-delete')): ?>
<div id="modal-delete" class="sg-overlay" style="display:none;"
     onclick="if(event.target===this)closeModal('modal-delete')">
    <div class="sg-modal" style="max-width:420px;">
        <button type="button" class="sg-modal-close" onclick="closeModal('modal-delete')">✕</button>
        <div style="text-align:center;padding:12px 0 20px;">
            <div style="width:56px;height:56px;border-radius:16px;background:#fff1f2;
                        border:1px solid #fecdd3;display:flex;align-items:center;
                        justify-content:center;font-size:24px;margin:0 auto 16px;">🗑️</div>
            <h2 style="font-size:16px;font-weight:800;color:#0f172a;margin:0 0 8px;">
                Supprimer le stagiaire ?
            </h2>
            <p style="font-size:13px;color:#64748b;margin:0;">
                Vous allez supprimer définitivement
                <strong id="delete-name" style="color:#0f172a;"></strong>.
                <br>Cette action est <strong style="color:#e11d48;">irréversible</strong>.
            </p>
            <div style="margin-top:14px;padding:10px 14px;border-radius:10px;
                        background:#fffbeb;border:1px solid #fde68a;
                        font-size:11px;color:#92400e;text-align:left;
                        display:flex;align-items:flex-start;gap:8px;">
                <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                     style="flex-shrink:0;margin-top:1px;">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                Si ce stagiaire possède un compte EDU, celui-ci sera remis automatiquement
                <strong style="margin-left:3px;">en attente</strong>.
            </div>
        </div>
        <form id="form-delete" method="POST" action="#">
            <?php echo csrf_field(); ?>
            <?php echo method_field('DELETE'); ?>
            <div class="sg-modal-footer" style="justify-content:center;">
                <button type="button" class="sg-btn-ghost" onclick="closeModal('modal-delete')">
                    Annuler
                </button>
                <button type="submit" class="sg-btn-danger-modal">
                    <svg width="13" height="13" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858
                                 L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                    </svg>
                    Oui, supprimer
                </button>
            </div>
        </form>
    </div>
</div>
<?php endif; ?>

<?php endif; ?> 

</div>



<?php if($filiereId): ?>
<script>
function openCreateModal() {
    document.getElementById('modal-create')?.style.setProperty('display', 'flex');
}

function openEditModal(btn) {
    var id   = btn.dataset.id;
    var form = document.getElementById('form-edit');
    if (!form) return;

    form.action = '/stagiaire/' + id;
    document.getElementById('edit-sid').value   = id;
    document.getElementById('edit-name').value  = btn.dataset.name;
    document.getElementById('edit-email').value = btn.dataset.email;
    document.getElementById('edit-cin').value   = btn.dataset.cin;
    document.getElementById('edit-phone').value = btn.dataset.phone;
    document.getElementById('edit-dob').value   = btn.dataset.dob;
    setSelect('edit-groupe', btn.dataset.groupe);
    form.querySelector('[name="password"]').value = '';

    document.getElementById('modal-edit').style.display = 'flex';
}

function openDeleteModal(id, name) {
    var form = document.getElementById('form-delete');
    if (!form) return;
    form.action = '/stagiaire/' + id;
    document.getElementById('delete-name').textContent = name;
    document.getElementById('modal-delete').style.display = 'flex';
}

function closeModal(id) {
    var m = document.getElementById(id);
    if (m) m.style.display = 'none';
}

function setSelect(selectId, value) {
    var sel = document.getElementById(selectId);
    if (!sel) return;
    for (var i = 0; i < sel.options.length; i++) {
        if (sel.options[i].value == value) { sel.selectedIndex = i; return; }
    }
    sel.selectedIndex = 0;
}

document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        ['modal-create', 'modal-edit', 'modal-delete'].forEach(closeModal);
    }
});

<?php if(old('_modal') === 'create'): ?>
document.addEventListener('DOMContentLoaded', function() {
    document.getElementById('modal-create')?.style.setProperty('display', 'flex');
});
<?php elseif(old('_modal') === 'edit'): ?>
document.addEventListener('DOMContentLoaded', function() {
    var form = document.getElementById('form-edit');
    var sid  = '<?php echo e(old("_stagiaire_id")); ?>';
    if (form && sid) {
        form.action = '/stagiaire/' + sid;
        document.getElementById('modal-edit').style.display = 'flex';
    }
});
<?php endif; ?>
</script>
<?php endif; ?>

<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\Project\gestion-CF\resources\views/stagiaire/index.blade.php ENDPATH**/ ?>