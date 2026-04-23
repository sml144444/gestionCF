
<?php $__env->startSection('title', 'Mes absences'); ?>
<?php $__env->startSection('page-title', 'Absences'); ?>

<?php $__env->startSection('content'); ?>
<?php
    $user     = Auth::user();
    $userRole = $user->role;

    $palettes = [
        'admin'        => ['primary'=>'#0a6640','medium'=>'#1a8c56','light'=>'#e8f5ee','lighter'=>'#f0fdf4','text'=>'#065f38','border'=>'#bbf7d0','shadow'=>'rgba(10,102,64,0.15)','gradient'=>'linear-gradient(135deg,#0a6640 0%,#1a8c56 100%)'],
        'gestionnaire' => ['primary'=>'#1e293b','medium'=>'#334155','light'=>'#f1f5f9','lighter'=>'#f8fafc','text'=>'#1e293b','border'=>'#cbd5e1','shadow'=>'rgba(30,41,59,0.15)','gradient'=>'linear-gradient(135deg,#1e293b 0%,#334155 100%)'],
        'formateur'    => ['primary'=>'#1a4f8a','medium'=>'#2563eb','light'=>'#eff6ff','lighter'=>'#f0f7ff','text'=>'#1e40af','border'=>'#bfdbfe','shadow'=>'rgba(26,79,138,0.15)','gradient'=>'linear-gradient(135deg,#1a4f8a 0%,#2563eb 100%)'],
        'stagiaire'    => ['primary'=>'#1a4f8a','medium'=>'#2563eb','light'=>'#eff6ff','lighter'=>'#f0f7ff','text'=>'#1e40af','border'=>'#bfdbfe','shadow'=>'rgba(26,79,138,0.15)','gradient'=>'linear-gradient(135deg,#1a4f8a 0%,#2563eb 100%)'],
    ];
    $p = $palettes[$userRole] ?? $palettes['gestionnaire'];

    $heroTitle = $canViewAll
        ? ($filterStagiaire ? 'Absences de '.$filterStagiaire->name : 'Toutes les absences')
        : 'Mes absences';

    $canJustify = $user->can('absence-justify');

    $partConfig = [
        's1' => ['color'=>'#6d28d9','bg'=>'#f5f3ff','border'=>'#ddd6fe','label'=>'S1'],
        's2' => ['color'=>'#1d4ed8','bg'=>'#eff6ff','border'=>'#bfdbfe','label'=>'S2'],
        's3' => ['color'=>'#0369a1','bg'=>'#f0f9ff','border'=>'#bae6fd','label'=>'S3'],
        's4' => ['color'=>'#0f766e','bg'=>'#f0fdfa','border'=>'#99f6e4','label'=>'S4'],
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
* { box-sizing:border-box; }
.abs-wrap { font-family:'Segoe UI',system-ui,sans-serif; max-width:1280px; margin:0 auto; }

/* ─── HERO ─── */
.abs-hero        { background:var(--accent-gr); border-radius:20px; padding:28px 32px; margin-bottom:24px;
                   display:flex; align-items:center; justify-content:space-between; gap:16px; flex-wrap:wrap;
                   position:relative; overflow:hidden; }
.abs-hero::after { content:''; position:absolute; right:-40px; top:-40px; width:200px; height:200px;
                   border-radius:50%; background:rgba(255,255,255,0.06); pointer-events:none; }
.abs-hero-icon   { width:52px; height:52px; border-radius:16px; background:rgba(255,255,255,0.15);
                   display:flex; align-items:center; justify-content:center; flex-shrink:0; }
.abs-hero-title  { font-size:20px; font-weight:800; color:white; margin:0; }
.abs-hero-sub    { font-size:12px; color:rgba(255,255,255,0.75); margin-top:3px; }

/* ─── STATS ─── */
.stats-grid      { display:grid; grid-template-columns:repeat(auto-fill,minmax(140px,1fr)); gap:12px; margin-bottom:20px; }
.stat-card       { background:white; border-radius:16px; border:1px solid #e2e8f0; padding:14px 16px;
                   display:flex; align-items:center; gap:11px; transition:all .2s; }
.stat-card:hover { transform:translateY(-2px); box-shadow:0 8px 20px rgba(0,0,0,0.08); }
.stat-icon       { width:38px; height:38px; border-radius:12px; display:flex; align-items:center;
                   justify-content:center; flex-shrink:0; font-size:17px; }
.stat-val        { font-size:22px; font-weight:900; line-height:1; }
.stat-lbl        { font-size:9px; font-weight:700; color:#94a3b8; margin-top:2px;
                   text-transform:uppercase; letter-spacing:.6px; }

/* ─── FILTER BAR ─── */
.filter-bar      { background:white; border-radius:16px; border:1px solid #e2e8f0;
                   padding:14px 18px; margin-bottom:18px; display:flex; flex-wrap:wrap;
                   gap:10px; align-items:flex-end; }
.filter-group    { display:flex; flex-direction:column; gap:4px; min-width:130px; }
.filter-label    { font-size:9px; font-weight:800; color:#94a3b8; text-transform:uppercase; letter-spacing:.8px; }
.filter-select   { font-size:12px; font-weight:500; padding:7px 10px; border:1.5px solid #e2e8f0;
                   border-radius:10px; background:white; color:#1e293b; outline:none; cursor:pointer;
                   transition:border-color .15s; }
.filter-select:focus { border-color:var(--accent); }
.btn-filter  { font-size:12px; font-weight:700; padding:8px 18px; border-radius:10px;
               background:var(--accent-gr); color:white; border:none; cursor:pointer;
               transition:opacity .15s; white-space:nowrap; }
.btn-filter:hover { opacity:.88; }
.btn-reset   { font-size:11px; font-weight:600; padding:8px 13px; border-radius:10px;
               background:white; color:#64748b; border:1.5px solid #e2e8f0;
               cursor:pointer; text-decoration:none; display:inline-flex; align-items:center; gap:5px; }
.btn-reset:hover { border-color:#cbd5e1; background:#f8fafc; }

/* ─── TABLE ─── */
.abs-table-wrap { background:white; border-radius:18px; border:1px solid #e2e8f0; overflow:hidden; }
.abs-table-head { padding:14px 20px; border-bottom:1px solid #f1f5f9; display:flex;
                  align-items:center; justify-content:space-between; flex-wrap:wrap; gap:10px; }
table.abs-table { width:100%; border-collapse:collapse; }
table.abs-table thead th { padding:10px 14px; font-size:9px; font-weight:800; color:#94a3b8;
                            text-transform:uppercase; letter-spacing:.8px; background:#f8fafc;
                            border-bottom:1px solid #e2e8f0; text-align:left; white-space:nowrap; }
table.abs-table tbody tr { border-bottom:1px solid #f1f5f9; transition:background .12s; }
table.abs-table tbody tr:last-child { border-bottom:none; }
table.abs-table tbody tr:hover { background:#fafbfd; }
table.abs-table tbody td { padding:12px 14px; font-size:12px; color:#374151; vertical-align:middle; }

/* Row highlight for pending */
.row-pending { background:rgba(251,191,36,0.04); }

/* ─── BADGES ─── */
.badge            { display:inline-flex; align-items:center; gap:4px; padding:3px 9px; border-radius:8px; font-size:10px; font-weight:800; }
.badge-justifie   { background:#d1fae5; color:#059669; border:1px solid #a7f3d0; }
.badge-injustifie { background:#fce7f3; color:#be185d; border:1px solid #fbcfe8; }
.badge-pending    { background:#fef3c7; color:#92400e; border:1px solid #fde68a; }

.avatar { width:30px; height:30px; border-radius:9px; background:var(--accent-lt);
          display:inline-flex; align-items:center; justify-content:center;
          font-size:10px; font-weight:800; color:var(--accent-tx); flex-shrink:0; }

/* ─── ACTION BUTTONS ─── */
.btn-accept { font-size:10px; font-weight:700; padding:4px 10px; border-radius:8px;
              background:#d1fae5; color:#059669; border:1px solid #6ee7b7;
              cursor:pointer; display:inline-flex; align-items:center; gap:3px; transition:all .15s; }
.btn-accept:hover { background:#a7f3d0; }
.btn-reject { font-size:10px; font-weight:700; padding:4px 10px; border-radius:8px;
              background:#fee2e2; color:#dc2626; border:1px solid #fca5a5;
              cursor:pointer; display:inline-flex; align-items:center; gap:3px; transition:all .15s; }
.btn-reject:hover { background:#fecaca; }
.btn-upload-label { display:inline-flex; align-items:center; gap:4px; cursor:pointer;
                    font-size:10px; font-weight:600; color:var(--accent);
                    padding:4px 9px; border-radius:8px;
                    border:1.5px dashed var(--accent-bd);
                    background:var(--accent-ltr); white-space:nowrap; transition:all .15s; }
.btn-upload-label:hover { background:var(--accent-lt); }

/* ─── FLASH ─── */
.flash-ok  { display:flex; align-items:center; gap:12px; padding:14px 18px; border-radius:14px;
             margin-bottom:18px; background:var(--accent-ltr); border:1px solid var(--accent-bd);
             animation:fadeIn .3s ease; }
.flash-ok-icon { width:36px; height:36px; border-radius:50%; background:var(--accent-gr);
                 display:flex; align-items:center; justify-content:center; flex-shrink:0; }
@keyframes fadeIn { from{opacity:0;transform:translateY(-6px)} to{opacity:1;transform:translateY(0)} }

/* ─── INFO BOX for stagiaire ─── */
.info-box { padding:14px 18px; border-radius:14px; margin-bottom:16px;
            background:#eff6ff; border:1px solid #bfdbfe;
            display:flex; align-items:flex-start; gap:10px; }

.empty-state      { padding:60px 20px; text-align:center; }
.empty-state-icon { width:64px; height:64px; border-radius:20px; background:var(--accent-lt);
                    margin:0 auto 16px; display:flex; align-items:center; justify-content:center; font-size:28px; }
.pagination-wrap  { padding:12px 18px; border-top:1px solid #f1f5f9; display:flex;
                    align-items:center; justify-content:space-between; flex-wrap:wrap; gap:8px; }

@media(max-width:768px) {
    .abs-hero { padding:18px; }
    table.abs-table thead th:nth-child(n+5),
    table.abs-table tbody td:nth-child(n+5) { display:none; }
}
</style>

<div class="abs-wrap">


<?php if(session('success')): ?>
<div class="flash-ok">
    <div class="flash-ok-icon">
        <svg width="16" height="16" fill="none" stroke="white" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
        </svg>
    </div>
    <p style="font-size:13px;font-weight:600;color:var(--accent-tx);margin:0;"><?php echo e(session('success')); ?></p>
</div>
<?php endif; ?>
<?php if(session('error')): ?>
<div style="padding:14px 18px;background:#fff1f2;border:1px solid #fecdd3;border-radius:14px;margin-bottom:16px;">
    <p style="font-size:12px;color:#be123c;margin:0;">✕ <?php echo e(session('error')); ?></p>
</div>
<?php endif; ?>


<?php if($userRole === 'stagiaire'): ?>
<div class="info-box">
    <svg width="18" height="18" fill="none" stroke="#1d4ed8" viewBox="0 0 24 24" style="flex-shrink:0;margin-top:1px;">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
    </svg>
    <div>
        <p style="font-size:12px;font-weight:700;color:#1e40af;margin:0 0 2px;">Comment justifier une absence ?</p>
        <p style="font-size:11px;color:#3b82f6;margin:0;">
            Cliquez sur <strong>📎 Joindre un justificatif</strong> en face de votre absence, puis envoyez votre document (PDF, image…).
            Votre justificatif sera examiné par l'administration. Le statut passera en <strong>🕐 En attente</strong> jusqu'à validation.
        </p>
    </div>
</div>
<?php endif; ?>


<div class="abs-hero">
    <div style="display:flex;align-items:center;gap:16px;">
        <div class="abs-hero-icon">
            <svg width="26" height="26" fill="none" stroke="white" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
        </div>
        <div>
            <h1 class="abs-hero-title"><?php echo e($heroTitle); ?></h1>
            <p class="abs-hero-sub">
                <strong style="color:white;"><?php echo e($stats['total']); ?></strong> absence(s) ·
                <strong style="color:white;"><?php echo e($stats['total_heures_abs']); ?>h</strong> cumulées
                <?php if($stats['en_attente'] > 0): ?>
                    · <strong style="color:#fef08a;"><?php echo e($stats['en_attente']); ?> en attente</strong>
                <?php endif; ?>
            </p>
        </div>
    </div>
    <span style="background:rgba(255,255,255,0.15);border:1px solid rgba(255,255,255,0.25);
                 color:white;font-size:11px;font-weight:700;padding:6px 14px;border-radius:99px;">
        <?php echo e(ucfirst($userRole)); ?>

    </span>
</div>


<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-icon" style="background:#fee2e2;">❌</div>
        <div><div class="stat-val" style="color:#dc2626;"><?php echo e($stats['total']); ?></div><div class="stat-lbl">Total</div></div>
    </div>
    <div class="stat-card">
        <div class="stat-icon" style="background:#fff7ed;">🕐</div>
        <div><div class="stat-val" style="color:#c2410c;"><?php echo e($stats['total_heures_abs']); ?>h</div><div class="stat-lbl">Heures abs.</div></div>
    </div>
    <div class="stat-card">
        <div class="stat-icon" style="background:#d1fae5;">✅</div>
        <div><div class="stat-val" style="color:#059669;"><?php echo e($stats['justifies']); ?></div><div class="stat-lbl">Justifiées</div></div>
    </div>
    <div class="stat-card">
        <div class="stat-icon" style="background:#fef3c7;">🕐</div>
        <div><div class="stat-val" style="color:#92400e;"><?php echo e($stats['en_attente']); ?></div><div class="stat-lbl">En attente</div></div>
    </div>
    <div class="stat-card">
        <div class="stat-icon" style="background:#fce7f3;">⚠️</div>
        <div><div class="stat-val" style="color:#be185d;"><?php echo e($stats['injustifies']); ?></div><div class="stat-lbl">Non justifiées</div></div>
    </div>
    <?php $__currentLoopData = ['s1','s2','s3','s4']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sp): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
    <div class="stat-card">
        <div class="stat-icon"
             style="background:<?php echo e(['s1'=>'#f5f3ff','s2'=>'#eff6ff','s3'=>'#f0f9ff','s4'=>'#f0fdfa'][$sp]); ?>;">
            <?php echo e(strtoupper($sp)); ?>

        </div>
        <div>
            <div class="stat-val"
                 style="color:<?php echo e(['s1'=>'#6d28d9','s2'=>'#1d4ed8','s3'=>'#0369a1','s4'=>'#0f766e'][$sp]); ?>;">
                <?php echo e($stats[$sp]); ?>

            </div>
            <div class="stat-lbl">Abs. <?php echo e(strtoupper($sp)); ?></div>
        </div>
    </div>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
</div>


<form method="GET" action="<?php echo e(route('absences.index')); ?>" class="filter-bar">

    <div class="filter-group">
        <label class="filter-label">Demi-séance</label>
        <select name="session_part" class="filter-select">
            <option value="">Toutes (S1–S4)</option>
            <option value="s1" <?php if(request('session_part') === 's1'): echo 'selected'; endif; ?>>S1</option>
            <option value="s2" <?php if(request('session_part') === 's2'): echo 'selected'; endif; ?>>S2</option>
            <option value="s3" <?php if(request('session_part') === 's3'): echo 'selected'; endif; ?>>S3</option>
            <option value="s4" <?php if(request('session_part') === 's4'): echo 'selected'; endif; ?>>S4</option>
        </select>
    </div>

    <div class="filter-group">
        <label class="filter-label">Statut</label>
        <select name="justifie" class="filter-select">
            <option value="">Tous les statuts</option>
            <option value="1"       <?php if(request('justifie') === '1'): echo 'selected'; endif; ?>>✅ Justifiée</option>
            <option value="pending" <?php if(request('justifie') === 'pending'): echo 'selected'; endif; ?>>🕐 En attente</option>
            <option value="0"       <?php if(request('justifie') === '0'): echo 'selected'; endif; ?>>⚠️ Non justifiée</option>
        </select>
    </div>

    <?php if($canViewAll): ?>
    <div class="filter-group">
        <label class="filter-label">Groupe</label>
        <select name="groupe_id" class="filter-select" onchange="this.form.submit()">
            <option value="">Tous les groupes</option>
            <?php $__currentLoopData = $groupes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $groupe): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <option value="<?php echo e($groupe->id); ?>" <?php if(request('groupe_id') == $groupe->id): echo 'selected'; endif; ?>>
                    <?php echo e($groupe->name); ?>

                </option>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </select>
    </div>

    <div class="filter-group">
        <label class="filter-label">Stagiaire</label>
        <select name="stagiaire_id" class="filter-select">
            <option value="">Tous</option>
            <?php $__currentLoopData = $stagiaires; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $s): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <option value="<?php echo e($s->id); ?>" <?php if(request('stagiaire_id') == $s->id): echo 'selected'; endif; ?>>
                    <?php echo e($s->name); ?>

                </option>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </select>
    </div>
    <?php endif; ?>

    <button type="submit" class="btn-filter">🔍 Filtrer</button>

    <?php if(request()->hasAny(['justifie','groupe_id','stagiaire_id','session_part'])): ?>
        <a href="<?php echo e(route('absences.index')); ?>" class="btn-reset">
            <svg width="11" height="11" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/>
            </svg>
            Réinitialiser
        </a>
    <?php endif; ?>
</form>


<div class="abs-table-wrap">
    <div class="abs-table-head">
        <div>
            <div style="font-size:10px;font-weight:800;color:#94a3b8;letter-spacing:1.5px;text-transform:uppercase;">
                📋 Historique des absences
            </div>
            <div style="font-size:12px;color:#64748b;margin-top:2px;">
                <?php echo e($absences->total()); ?> résultat(s) — page <?php echo e($absences->currentPage()); ?>/<?php echo e($absences->lastPage()); ?>

            </div>
        </div>
        
        <?php if($canJustify && $stats['en_attente'] > 0): ?>
        <div style="display:flex;align-items:center;gap:6px;font-size:11px;color:#92400e;
                    background:#fef3c7;border:1px solid #fde68a;padding:6px 12px;border-radius:10px;">
            🕐 <strong><?php echo e($stats['en_attente']); ?></strong> justificatif(s) en attente de validation
        </div>
        <?php endif; ?>
    </div>

    <?php if($absences->isEmpty()): ?>
        <div class="empty-state">
            <div class="empty-state-icon">🎉</div>
            <p style="font-size:15px;font-weight:700;color:#1e293b;margin:0 0 6px;">Aucune absence enregistrée</p>
            <p style="font-size:12px;color:#94a3b8;margin:0;">
                <?php echo e(request()->hasAny(['justifie','groupe_id','stagiaire_id','session_part'])
                    ? 'Aucun résultat pour ces filtres.'
                    : 'Parfait ! Aucune absence pour le moment.'); ?>

            </p>
        </div>
    <?php else: ?>
        <div style="overflow-x:auto;">
        <table class="abs-table">
            <thead>
                <tr>
                    <th>Date & Horaire</th>
                    <?php if($canViewAll): ?>
                        <th>Stagiaire</th>
                        <th>Groupe</th>
                    <?php endif; ?>
                    <th>Module</th>
                    <th>Formateur</th>
                    <th>Demi-séance</th>
                    <th>Durée</th>
                    <th>Statut</th>
                    <th>Justificatif</th>
                </tr>
            </thead>
            <tbody>
            <?php $__currentLoopData = $absences; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $abs): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php
                    $emploi   = $abs->cours?->emploiDuTemps;
                    $module   = $emploi?->module;
                    $groupe   = $emploi?->groupe;
                    $form     = $emploi?->gestionnaire;
                    $initials = strtoupper(
                        mb_substr($abs->stagiaire?->name ?? '?', 0, 1) .
                        mb_substr(explode(' ', $abs->stagiaire?->name ?? '')[1] ?? '', 0, 1)
                    );
                    $part   = $abs->session_part ?? 's1';
                    $pc     = $partConfig[$part] ?? $partConfig['s1'];

                    // Determine the 3-state status
                    $isPending  = !$abs->justifie && !empty($abs->file_justification);
                    $isJustifie = $abs->justifie;
                    // isRejected / plain unjustified = !$abs->justifie && empty($abs->file_justification)

                    $isOwner = Auth::id() === $abs->id_user;
                ?>
                <tr class="<?php echo e($isPending ? 'row-pending' : ''); ?>">

                    
                    <td>
                        <div style="font-weight:700;color:#1e293b;">
                            <?php echo e($abs->date_event ? $abs->date_event->format('d/m/Y') : '—'); ?>

                        </div>
                        <div style="font-size:10px;color:#94a3b8;">
                            <?php echo e($abs->date_event ? $abs->date_event->translatedFormat('l') : ''); ?>

                        </div>
                        <?php if($emploi): ?>
                            <div style="font-size:10px;color:#94a3b8;">
                                <?php echo e(\Carbon\Carbon::parse($emploi->heure_debut ?? $emploi->date_debut)->format('H:i')); ?>

                                – <?php echo e(\Carbon\Carbon::parse($emploi->heure_fin ?? $emploi->date_fin)->format('H:i')); ?>

                            </div>
                        <?php endif; ?>
                    </td>

                    
                    <?php if($canViewAll): ?>
                    <td>
                        <div style="display:flex;align-items:center;gap:8px;">
                            <div class="avatar"><?php echo e($initials); ?></div>
                            <div>
                                <div style="font-weight:600;color:#1e293b;"><?php echo e($abs->stagiaire?->name ?? '—'); ?></div>
                                <div style="font-size:10px;color:#94a3b8;"><?php echo e($abs->stagiaire?->email ?? ''); ?></div>
                            </div>
                        </div>
                    </td>
                    <td>
                        <span style="font-size:11px;font-weight:600;color:#475569;">
                            <?php echo e($groupe?->name ?? '—'); ?>

                        </span>
                    </td>
                    <?php endif; ?>

                    
                    <td>
                        <div style="font-weight:600;color:#1e293b;"><?php echo e($module?->name ?? '—'); ?></div>
                        <?php if($emploi?->salle): ?>
                            <div style="font-size:10px;color:#94a3b8;">🏫 <?php echo e($emploi->salle->name); ?></div>
                        <?php endif; ?>
                    </td>

                    
                    <td>
                        <span style="font-size:11px;color:#475569;font-weight:500;">
                            <?php echo e($form?->name ?? '—'); ?>

                        </span>
                    </td>

                    
                    <td>
                        <span style="display:inline-flex;align-items:center;gap:4px;padding:3px 10px;
                                     border-radius:8px;font-size:11px;font-weight:800;
                                     background:<?php echo e($pc['bg']); ?>;color:<?php echo e($pc['color']); ?>;
                                     border:1px solid <?php echo e($pc['border']); ?>;">
                            <?php echo e(strtoupper($part)); ?>

                        </span>
                        <div style="font-size:9px;color:#94a3b8;margin-top:3px;">2.5h</div>
                    </td>

                    
                    <td>
                        <span style="font-size:12px;font-weight:700;color:#dc2626;">
                            <?php echo e($abs->duree ? $abs->duree . ' h' : '2.5 h'); ?>

                        </span>
                    </td>

                    
                    <td>
                        <?php if($isJustifie): ?>
                            
                            <?php if($canJustify): ?>
                                <form method="POST" action="<?php echo e(route('absences.justify', $abs)); ?>" style="display:inline;">
                                    <?php echo csrf_field(); ?> <?php echo method_field('PATCH'); ?>
                                    <button type="submit" title="Cliquer pour marquer non justifiée"
                                            style="background:none;border:none;cursor:pointer;padding:0;">
                                        <span class="badge badge-justifie" style="cursor:pointer;">
                                            ✅ Justifiée
                                            <svg width="9" height="9" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="margin-left:2px;">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15.232 5.232l3.536 3.536M9 13l6.5-6.5M3 21h4l11-11a2.828 2.828 0 00-4-4L3 17v4z"/>
                                            </svg>
                                        </span>
                                    </button>
                                </form>
                            <?php else: ?>
                                <span class="badge badge-justifie">✅ Justifiée</span>
                            <?php endif; ?>

                        <?php elseif($isPending): ?>
                            
                            <div>
                                <span class="badge badge-pending">🕐 En attente</span>
                                <?php if($canJustify): ?>
                                    <div style="display:flex;gap:5px;margin-top:6px;flex-wrap:wrap;">
                                        
                                        <form method="POST" action="<?php echo e(route('absences.accept', $abs)); ?>">
                                            <?php echo csrf_field(); ?> <?php echo method_field('PATCH'); ?>
                                            <button type="submit" class="btn-accept">
                                                <svg width="10" height="10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                                                </svg>
                                                Accepter
                                            </button>
                                        </form>
                                        
                                        <form method="POST" action="<?php echo e(route('absences.reject', $abs)); ?>"
                                              onsubmit="return confirm('Rejeter ce justificatif ? Le stagiaire pourra en soumettre un nouveau.')">
                                            <?php echo csrf_field(); ?> <?php echo method_field('PATCH'); ?>
                                            <button type="submit" class="btn-reject">
                                                <svg width="10" height="10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/>
                                                </svg>
                                                Rejeter
                                            </button>
                                        </form>
                                    </div>
                                <?php else: ?>
                                    <div style="font-size:10px;color:#92400e;margin-top:4px;">
                                        En cours d'examen par l'administration
                                    </div>
                                <?php endif; ?>
                            </div>

                        <?php else: ?>
                            
                            <?php if($canJustify): ?>
                                <form method="POST" action="<?php echo e(route('absences.justify', $abs)); ?>" style="display:inline;">
                                    <?php echo csrf_field(); ?> <?php echo method_field('PATCH'); ?>
                                    <button type="submit" title="Marquer comme justifiée directement"
                                            style="background:none;border:none;cursor:pointer;padding:0;">
                                        <span class="badge badge-injustifie" style="cursor:pointer;">
                                            ⚠️ Non justifiée
                                            <svg width="9" height="9" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="margin-left:2px;">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15.232 5.232l3.536 3.536M9 13l6.5-6.5M3 21h4l11-11a2.828 2.828 0 00-4-4L3 17v4z"/>
                                            </svg>
                                        </span>
                                    </button>
                                </form>
                            <?php else: ?>
                                <span class="badge badge-injustifie">⚠️ Non justifiée</span>
                            <?php endif; ?>
                        <?php endif; ?>
                    </td>

                    
                    <td>
                        <?php if($isJustifie && $abs->file_justification): ?>
                            
                            <div style="display:flex;flex-direction:column;gap:5px;">
                                <a href="<?php echo e(Storage::url($abs->file_justification)); ?>"
                                   target="_blank"
                                   style="font-size:11px;font-weight:600;color:var(--accent);text-decoration:none;display:inline-flex;align-items:center;gap:4px;">
                                    📎 Voir fichier
                                </a>
                                <?php if($canJustify): ?>
                                    <form method="POST" action="<?php echo e(route('absences.fichier.delete', $abs)); ?>"
                                          onsubmit="return confirm('Supprimer ce fichier ?')">
                                        <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                                        <button type="submit"
                                                style="font-size:10px;font-weight:600;color:#dc2626;background:none;border:none;cursor:pointer;padding:0;display:inline-flex;align-items:center;gap:3px;">
                                            🗑 Supprimer
                                        </button>
                                    </form>
                                <?php endif; ?>
                            </div>

                        <?php elseif($isPending && $abs->file_justification): ?>
                            
                            <div style="display:flex;flex-direction:column;gap:5px;">
                                <a href="<?php echo e(Storage::url($abs->file_justification)); ?>"
                                   target="_blank"
                                   style="font-size:11px;font-weight:600;color:#92400e;text-decoration:none;display:inline-flex;align-items:center;gap:4px;">
                                    📎 Voir fichier
                                    <span style="font-size:9px;background:#fef3c7;color:#92400e;padding:1px 5px;border-radius:4px;border:1px solid #fde68a;">en attente</span>
                                </a>
                                <?php if($isOwner && !$canJustify): ?>
                                    
                                    <form method="POST" action="<?php echo e(route('absences.stagiaire.fichier.delete', $abs)); ?>"
                                          onsubmit="return confirm('Retirer ce justificatif ?')">
                                        <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                                        <button type="submit"
                                                style="font-size:10px;color:#64748b;background:none;border:none;cursor:pointer;padding:0;display:inline-flex;align-items:center;gap:3px;">
                                            ✕ Retirer
                                        </button>
                                    </form>
                                <?php endif; ?>
                            </div>

                        <?php elseif(!$abs->file_justification && !$isJustifie): ?>
                            
                            <?php if($isOwner && !$canJustify): ?>
                                
                                <form method="POST"
                                      action="<?php echo e(route('absences.stagiaire.fichier', $abs)); ?>"
                                      enctype="multipart/form-data">
                                    <?php echo csrf_field(); ?>
                                    <label class="btn-upload-label">
                                        <svg width="11" height="11" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                                        </svg>
                                        📎 Joindre un justificatif
                                        <input type="file"
                                               name="file_justification"
                                               accept=".pdf,.doc,.docx,.jpg,.jpeg,.png"
                                               style="display:none;"
                                               onchange="this.closest('form').submit()">
                                    </label>
                                </form>
                            <?php elseif($canJustify): ?>
                                
                                <form method="POST"
                                      action="<?php echo e(route('absences.fichier', $abs)); ?>"
                                      enctype="multipart/form-data">
                                    <?php echo csrf_field(); ?>
                                    <label class="btn-upload-label">
                                        <svg width="11" height="11" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                                        </svg>
                                        Joindre
                                        <input type="file"
                                               name="file_justification"
                                               accept=".pdf,.doc,.docx,.jpg,.jpeg,.png"
                                               style="display:none;"
                                               onchange="this.closest('form').submit()">
                                    </label>
                                </form>
                            <?php else: ?>
                                <span style="font-size:11px;color:#cbd5e1;">—</span>
                            <?php endif; ?>
                        <?php else: ?>
                            <span style="font-size:11px;color:#cbd5e1;">—</span>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </tbody>
        </table>
        </div>

        
        <?php if($absences->hasPages()): ?>
        <div class="pagination-wrap">
            <span style="font-size:11px;color:#94a3b8;">
                <?php echo e($absences->firstItem()); ?>–<?php echo e($absences->lastItem()); ?> sur <?php echo e($absences->total()); ?>

            </span>
            <div style="display:flex;gap:6px;">
                <?php if($absences->onFirstPage()): ?>
                    <span style="padding:6px 12px;border-radius:8px;background:#f1f5f9;color:#cbd5e1;font-size:12px;font-weight:600;">←</span>
                <?php else: ?>
                    <a href="<?php echo e($absences->previousPageUrl()); ?>"
                       style="padding:6px 12px;border-radius:8px;background:white;border:1.5px solid #e2e8f0;color:#475569;font-size:12px;font-weight:600;text-decoration:none;">←</a>
                <?php endif; ?>

                <?php $__currentLoopData = $absences->getUrlRange(max(1,$absences->currentPage()-2), min($absences->lastPage(),$absences->currentPage()+2)); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $page => $url): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php if($page == $absences->currentPage()): ?>
                        <span style="padding:6px 12px;border-radius:8px;background:var(--accent-gr);color:white;font-size:12px;font-weight:700;"><?php echo e($page); ?></span>
                    <?php else: ?>
                        <a href="<?php echo e($url); ?>"
                           style="padding:6px 12px;border-radius:8px;background:white;border:1.5px solid #e2e8f0;color:#475569;font-size:12px;font-weight:600;text-decoration:none;"><?php echo e($page); ?></a>
                    <?php endif; ?>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                <?php if($absences->hasMorePages()): ?>
                    <a href="<?php echo e($absences->nextPageUrl()); ?>"
                       style="padding:6px 12px;border-radius:8px;background:white;border:1.5px solid #e2e8f0;color:#475569;font-size:12px;font-weight:600;text-decoration:none;">→</a>
                <?php else: ?>
                    <span style="padding:6px 12px;border-radius:8px;background:#f1f5f9;color:#cbd5e1;font-size:12px;font-weight:600;">→</span>
                <?php endif; ?>
            </div>
        </div>
        <?php endif; ?>
    <?php endif; ?>
</div>

</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\Project\gestion-CF\resources\views/absences/index.blade.php ENDPATH**/ ?>