
<?php $__env->startSection('title', 'Séance — ' . ($emploi->module?->name ?? 'Module')); ?>
<?php $__env->startSection('page-title', 'Détail de la séance'); ?>

<?php $__env->startSection('content'); ?>
<?php
    $user     = Auth::user();
    $userRole = $user->role;

    $palettes = [
        'admin'        => ['primary'=>'#0a6640','medium'=>'#1a8c56','light'=>'#e8f5ee','lighter'=>'#f0fdf4','text'=>'#065f38','border'=>'#bbf7d0','shadow'=>'rgba(10,102,64,0.12)','gradient'=>'linear-gradient(135deg,#0a6640 0%,#1a8c56 100%)'],
        'gestionnaire' => ['primary'=>'#1e293b','medium'=>'#334155','light'=>'#f1f5f9','lighter'=>'#f8fafc','text'=>'#1e293b','border'=>'#cbd5e1','shadow'=>'rgba(30,41,59,0.12)','gradient'=>'linear-gradient(135deg,#1e293b 0%,#334155 100%)'],
        'formateur'    => ['primary'=>'#1a4f8a','medium'=>'#2563eb','light'=>'#eff6ff','lighter'=>'#f0f7ff','text'=>'#1e40af','border'=>'#bfdbfe','shadow'=>'rgba(26,79,138,0.12)','gradient'=>'linear-gradient(135deg,#1a4f8a 0%,#2563eb 100%)'],
        'stagiaire'    => ['primary'=>'#1a4f8a','medium'=>'#2563eb','light'=>'#eff6ff','lighter'=>'#f0f7ff','text'=>'#1e40af','border'=>'#bfdbfe','shadow'=>'rgba(26,79,138,0.12)','gradient'=>'linear-gradient(135deg,#1a4f8a 0%,#2563eb 100%)'],
    ];
    $p = $palettes[$userRole] ?? $palettes['gestionnaire'];

    $statutColors = [
        'actif'   => ['bg'=>'#d1fae5','color'=>'#065f46','border'=>'#6ee7b7','label'=>'Actif'],
        'annule'  => ['bg'=>'#fee2e2','color'=>'#991b1b','border'=>'#fca5a5','label'=>'Annulé'],
        'reporte' => ['bg'=>'#fef3c7','color'=>'#92400e','border'=>'#fde68a','label'=>'Reporté'],
        'prevue'  => ['bg'=>'#eff6ff','color'=>'#1e40af','border'=>'#bfdbfe','label'=>'Prévue'],
    ];
    $sc = $statutColors[$emploi->statut] ?? $statutColors['prevue'];

    $halfDuree  = 2.5;
    $totalDuree = count($activeParts) * $halfDuree;

    $partConfig = [
        's1' => ['label'=>'S1','color'=>'#6d28d9','bg'=>'#f5f3ff','border'=>'#ddd6fe','th'=>'#ede9fe'],
        's2' => ['label'=>'S2','color'=>'#1d4ed8','bg'=>'#eff6ff','border'=>'#bfdbfe','th'=>'#dbeafe'],
        's3' => ['label'=>'S3','color'=>'#0369a1','bg'=>'#f0f9ff','border'=>'#bae6fd','th'=>'#e0f2fe'],
        's4' => ['label'=>'S4','color'=>'#0f766e','bg'=>'#f0fdfa','border'=>'#99f6e4','th'=>'#ccfbf1'],
    ];

    $isStagiaire = $userRole === 'stagiaire';
?>

<style>
:root {
    --ac:    <?php echo e($p['primary']); ?>;
    --ac-md: <?php echo e($p['medium']); ?>;
    --ac-lt: <?php echo e($p['light']); ?>;
    --ac-ltr:<?php echo e($p['lighter']); ?>;
    --ac-tx: <?php echo e($p['text']); ?>;
    --ac-bd: <?php echo e($p['border']); ?>;
    --ac-sh: <?php echo e($p['shadow']); ?>;
    --ac-gr: <?php echo e($p['gradient']); ?>;
}

* { box-sizing: border-box; }
.sw { font-family:'Segoe UI',system-ui,sans-serif; max-width:1200px; margin:0 auto; }

/* ─── HERO ─── */
.hero { background:var(--ac-gr); border-radius:20px; padding:28px 32px; margin-bottom:22px;
        display:flex; align-items:center; justify-content:space-between; gap:16px; flex-wrap:wrap;
        position:relative; overflow:hidden; }
.hero::after { content:''; position:absolute; right:-50px; top:-50px; width:220px; height:220px;
               border-radius:50%; background:rgba(255,255,255,0.06); pointer-events:none; }
.hero-icon { width:52px; height:52px; border-radius:14px; background:rgba(255,255,255,0.15);
             display:flex; align-items:center; justify-content:center; flex-shrink:0; }
.hero-title { font-size:20px; font-weight:800; color:#fff; margin:0; }
.hero-sub   { font-size:12px; color:rgba(255,255,255,0.72); margin-top:4px; }
.hero-back  { background:rgba(255,255,255,0.15); border:1px solid rgba(255,255,255,0.25);
              color:#fff; font-size:11px; font-weight:700; padding:7px 16px; border-radius:99px;
              text-decoration:none; transition:background .15s; }
.hero-back:hover { background:rgba(255,255,255,0.22); }

/* ─── INFO CARDS ─── */
.info-grid { display:grid; grid-template-columns:repeat(auto-fill,minmax(180px,1fr)); gap:12px; margin-bottom:20px; }
.info-card { background:#fff; border:1px solid #e2e8f0; border-radius:14px; padding:14px 16px; }
.info-label { font-size:9px; font-weight:800; color:#94a3b8; text-transform:uppercase; letter-spacing:.9px; margin-bottom:5px; }
.info-val   { font-size:13px; font-weight:700; color:#1e293b; }
.info-sub   { font-size:10px; color:#94a3b8; margin-top:2px; }

/* ─── SECTION CARD ─── */
.section { background:#fff; border:1px solid #e2e8f0; border-radius:18px; margin-bottom:20px; overflow:hidden; }
.section-head { padding:16px 20px; border-bottom:1px solid #f1f5f9; display:flex; align-items:center; justify-content:space-between; gap:10px; flex-wrap:wrap; }
.section-title { font-size:13px; font-weight:800; color:#1e293b; display:flex; align-items:center; gap:8px; }
.section-sub   { font-size:11px; color:#94a3b8; margin-top:2px; }

/* ─── PRESENCE TABLE ─── */
table.ptbl { width:100%; border-collapse:collapse; }
table.ptbl thead th { padding:10px 12px; font-size:9px; font-weight:800;
                      text-transform:uppercase; letter-spacing:.8px;
                      border-bottom:2px solid #e2e8f0; white-space:nowrap; }
table.ptbl thead th.th-name { text-align:left; background:#f8fafc; color:#94a3b8; }
table.ptbl thead th.th-bilan { background:#f8fafc; color:#94a3b8; text-align:center; }
table.ptbl thead th.th-part { text-align:center; }

table.ptbl tbody tr { border-bottom:1px solid #f1f5f9; transition:background .1s; }
table.ptbl tbody tr:last-child { border-bottom:none; }
table.ptbl tbody tr:hover { background:#fafbfd; }
table.ptbl tbody td { padding:11px 12px; font-size:12px; color:#374151; vertical-align:middle; }
table.ptbl tbody td.td-name  { text-align:left; }
table.ptbl tbody td.td-part  { text-align:center; }
table.ptbl tbody td.td-bilan { text-align:center; }

/* ─── ABSENT CHECKBOX ─── */
.abs-checkbox-wrap { display:flex; flex-direction:column; align-items:center; gap:3px; }
.abs-cb {
    width:20px; height:20px; cursor:pointer; border-radius:5px;
    accent-color:#dc2626; flex-shrink:0;
}
.abs-cb:focus { outline:2px solid #dc2626; outline-offset:2px; }

/* Row highlight when any session is absent */
.row-has-absence { background:rgba(220,38,38,0.02); }

/* ─── STATUS BADGES ─── */
.badge { display:inline-flex; align-items:center; gap:4px; padding:3px 9px; border-radius:7px; font-size:10px; font-weight:800; }
.half-badge { font-size:9px; font-weight:800; padding:2px 8px; border-radius:6px; white-space:nowrap; display:inline-block; }

/* ─── RESOURCES ─── */
.res-item { display:flex; align-items:flex-start; gap:12px; padding:14px 20px; border-bottom:1px solid #f1f5f9; }
.res-item:last-child { border-bottom:none; }
.res-icon { width:38px; height:38px; border-radius:11px; display:flex; align-items:center; justify-content:center; flex-shrink:0; font-size:16px; }
.res-title { font-size:13px; font-weight:700; color:#1e293b; }
.res-desc  { font-size:11px; color:#94a3b8; margin-top:2px; }
.res-link  { font-size:11px; font-weight:600; color:var(--ac); text-decoration:none; }
.res-link:hover { text-decoration:underline; }

/* ─── ADD RESOURCE FORM ─── */
.add-form { padding:20px; background:#f8fafc; border-top:1px solid #e2e8f0; }
.form-grid { display:grid; grid-template-columns:1fr 1fr; gap:12px; }
.form-group { display:flex; flex-direction:column; gap:5px; }
.form-label { font-size:10px; font-weight:700; color:#64748b; text-transform:uppercase; letter-spacing:.7px; }
.form-input, .form-select, .form-textarea {
    font-size:12px; padding:9px 12px; border:1.5px solid #e2e8f0; border-radius:10px;
    background:#fff; color:#1e293b; outline:none; width:100%; transition:border-color .15s; }
.form-input:focus, .form-select:focus, .form-textarea:focus { border-color:var(--ac); }
.form-textarea { resize:vertical; min-height:70px; }
.btn-primary { font-size:12px; font-weight:700; padding:10px 22px; border-radius:10px;
               background:var(--ac-gr); color:#fff; border:none; cursor:pointer; transition:opacity .15s; }
.btn-primary:hover { opacity:.88; }
.btn-danger  { font-size:10px; font-weight:700; padding:5px 12px; border-radius:8px;
               background:#fff; color:#dc2626; border:1.5px solid #fecaca; cursor:pointer; transition:all .15s; }
.btn-danger:hover { background:#fee2e2; }

/* ─── STAGIAIRE SELF-VIEW BANNER ─── */
.self-view-banner {
    display:flex; align-items:center; gap:12px; padding:14px 20px;
    background:#eff6ff; border-bottom:1px solid #bfdbfe;
    font-size:12px; color:#1e40af; font-weight:600;
}

/* ─── FLASH ─── */
.flash-ok  { display:flex; align-items:center; gap:12px; padding:14px 18px; border-radius:14px;
             margin-bottom:18px; background:var(--ac-ltr); border:1px solid var(--ac-bd); animation:fi .3s ease; }
.flash-err { display:flex; align-items:center; gap:12px; padding:14px 18px; border-radius:14px;
             margin-bottom:18px; background:#fff1f2; border:1px solid #fecdd3; animation:fi .3s ease; }
.flash-icon { width:36px; height:36px; border-radius:50%; background:var(--ac-gr); display:flex; align-items:center; justify-content:center; flex-shrink:0; }
@keyframes fi { from{opacity:0;transform:translateY(-6px)} to{opacity:1;transform:translateY(0)} }

/* ─── AVATAR ─── */
.avatar { width:30px; height:30px; border-radius:9px; background:var(--ac-lt);
          display:inline-flex; align-items:center; justify-content:center;
          font-size:10px; font-weight:800; color:var(--ac-tx); flex-shrink:0; }

/* ─── LEGEND ─── */
.legend { display:flex; align-items:center; gap:14px; flex-wrap:wrap; padding:10px 16px;
          background:#f8fafc; border-top:1px solid #f1f5f9; font-size:10px; color:#64748b; }
.legend-item { display:flex; align-items:center; gap:5px; font-weight:600; }

/* ─── SELECT ALL ROW ─── */
.select-all-bar { padding:10px 16px; background:#fef9ee; border-top:1px solid #fef3c7;
                  display:flex; align-items:center; gap:16px; flex-wrap:wrap; }
.btn-select-all { font-size:10px; font-weight:700; padding:4px 11px; border-radius:7px; cursor:pointer;
                  border:1.5px solid currentColor; background:white; transition:all .15s; }

@media(max-width:768px) {
    .hero { padding:18px; }
    .info-grid { grid-template-columns:1fr 1fr; }
    .form-grid { grid-template-columns:1fr; }
}
</style>

<div class="sw">


<?php if(session('success')): ?>
<div class="flash-ok">
    <div class="flash-icon">
        <svg width="16" height="16" fill="none" stroke="#fff" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
        </svg>
    </div>
    <p style="font-size:13px;font-weight:600;color:var(--ac-tx);margin:0;"><?php echo e(session('success')); ?></p>
</div>
<?php endif; ?>
<?php if(session('error')): ?>
<div class="flash-err">
    <div style="width:36px;height:36px;border-radius:50%;background:#dc2626;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
        <svg width="16" height="16" fill="none" stroke="#fff" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/>
        </svg>
    </div>
    <p style="font-size:13px;font-weight:600;color:#be123c;margin:0;"><?php echo e(session('error')); ?></p>
</div>
<?php endif; ?>


<div class="hero">
    <div style="display:flex;align-items:center;gap:16px;">
        <div class="hero-icon">
            <svg width="26" height="26" fill="none" stroke="#fff" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
            </svg>
        </div>
        <div>
            <h1 class="hero-title"><?php echo e($emploi->module?->name ?? 'Séance'); ?></h1>
            <p class="hero-sub">
                <?php echo e($emploi->date_debut->translatedFormat('l d M Y')); ?>

                &nbsp;·&nbsp;
                <?php echo e($emploi->date_debut->format('H:i')); ?> – <?php echo e($emploi->date_fin->format('H:i')); ?>

                <?php if($emploi->groupe): ?>
                    &nbsp;·&nbsp; Groupe <?php echo e($emploi->groupe->name); ?>

                <?php endif; ?>
            </p>
        </div>
    </div>
    <div style="display:flex;align-items:center;gap:10px;flex-wrap:wrap;">
        <span style="background:<?php echo e($sc['bg']); ?>;color:<?php echo e($sc['color']); ?>;border:1px solid <?php echo e($sc['border']); ?>;
                     font-size:11px;font-weight:800;padding:5px 13px;border-radius:99px;">
            <?php echo e($sc['label']); ?>

        </span>
        <a href="<?php echo e(route('emplois.index')); ?>" class="hero-back">← Retour</a>
    </div>
</div>


<div class="info-grid">
    <div class="info-card">
        <div class="info-label">📅 Date</div>
        <div class="info-val"><?php echo e($emploi->date_debut->format('d/m/Y')); ?></div>
        <div class="info-sub"><?php echo e($emploi->date_debut->translatedFormat('l')); ?></div>
    </div>
    <div class="info-card">
        <div class="info-label">🕐 Horaire</div>
        <div class="info-val"><?php echo e($emploi->date_debut->format('H:i')); ?> – <?php echo e($emploi->date_fin->format('H:i')); ?></div>
        <div class="info-sub"><?php echo e(count($activeParts)); ?> demi-séance(s) × 2.5h = <?php echo e($totalDuree); ?>h</div>
    </div>
    <?php if($emploi->salle): ?>
    <div class="info-card">
        <div class="info-label">🏫 Salle</div>
        <div class="info-val"><?php echo e($emploi->salle->name); ?></div>
        <?php if($emploi->salle->capacite): ?>
            <div class="info-sub">Capacité: <?php echo e($emploi->salle->capacite); ?> pers.</div>
        <?php endif; ?>
    </div>
    <?php endif; ?>
    <?php if($emploi->gestionnaire): ?>
    <div class="info-card">
        <div class="info-label">👤 Formateur</div>
        <div class="info-val"><?php echo e($emploi->gestionnaire->name); ?></div>
        <div class="info-sub"><?php echo e($emploi->gestionnaire->email); ?></div>
    </div>
    <?php endif; ?>
    <?php if($emploi->remplacant): ?>
    <div class="info-card">
        <div class="info-label">🔄 Remplaçant</div>
        <div class="info-val"><?php echo e($emploi->remplacant->name); ?></div>
        <div class="info-sub"><?php echo e($emploi->remplacant->email); ?></div>
    </div>
    <?php endif; ?>
    <div class="info-card">
        <div class="info-label">👥 Stagiaires</div>
        <div class="info-val">
            <?php if($isStagiaire): ?>
                <?php echo e($emploi->groupe ? \App\Models\User::where('id_groupe', $emploi->id_groupe)->where('role','stagiaire')->count() : '—'); ?>

            <?php else: ?>
                <?php echo e($stagiaires->count()); ?>

            <?php endif; ?>
        </div>
        <div class="info-sub"><?php echo e($emploi->groupe?->name ?? '—'); ?></div>
    </div>
</div>


<div class="section">
    <div class="section-head">
        <div>
            <div class="section-title">
                <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <?php if($isStagiaire): ?>
                    Ma présence
                <?php else: ?>
                    Liste de présence
                <?php endif; ?>
            </div>
            <div class="section-sub">
                <?php if($isStagiaire): ?>
                    Votre statut de présence pour chaque demi-séance.
                <?php else: ?>
                    ☑ Cochez uniquement les <strong>absences</strong> — les cases vides = Présent.
                    Cette séance a <strong><?php echo e(count($activeParts)); ?> demi-séance(s)</strong>
                    (<?php echo e(implode(', ', array_map('strtoupper', $activeParts))); ?>) × 2.5h = <strong><?php echo e($totalDuree); ?>h</strong>.
                <?php endif; ?>
            </div>
        </div>
        <?php if($emploi->statut !== 'annule' && $canPresence && $stagiaires->isNotEmpty()): ?>
        <button form="presence-form" type="submit"
                style="font-size:12px;font-weight:700;padding:9px 22px;border-radius:10px;
                       background:var(--ac-gr);color:#fff;border:none;cursor:pointer;white-space:nowrap;">
            💾 Enregistrer la présence
        </button>
        <?php endif; ?>
    </div>

    
    <?php if($isStagiaire): ?>
    <div class="self-view-banner">
        <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="flex-shrink:0;">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        Vous ne voyez que votre propre présence.
    </div>
    <?php endif; ?>

    <?php if($stagiaires->isEmpty()): ?>
        <div style="padding:40px;text-align:center;color:#94a3b8;font-size:13px;">
            <?php if($isStagiaire): ?>
                Vous n'êtes pas inscrit dans le groupe de cette séance.
            <?php else: ?>
                Aucun stagiaire dans ce groupe.
            <?php endif; ?>
        </div>

    <?php elseif(!$canPresence): ?>
        
        <div style="overflow-x:auto;">
        <table class="ptbl">
            <thead>
                <tr>
                    <th class="th-name">
                        <?php if($isStagiaire): ?> Votre nom <?php else: ?> Stagiaire <?php endif; ?>
                    </th>
                    <?php $__currentLoopData = $activeParts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $part): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <th class="th-part"
                            style="background:<?php echo e($partConfig[$part]['bg']); ?>;
                                   color:<?php echo e($partConfig[$part]['color']); ?>;
                                   border-left:2px solid <?php echo e($partConfig[$part]['th']); ?>;">
                            <?php echo e(strtoupper($part)); ?><br>
                            <span style="font-size:8px;opacity:.7;">2.5h</span>
                        </th>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    <th class="th-bilan">Total abs.</th>
                </tr>
            </thead>
            <tbody>
            <?php $__currentLoopData = $stagiaires; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $stagiaire): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php
                    $statuses = [];
                    $absCount = 0;
                    foreach ($activeParts as $part) {
                        $rec = $presences->get($stagiaire->id . '_' . $part);
                        $isAbsent = $rec?->type === 'absence';
                        $statuses[$part] = $isAbsent;
                        if ($isAbsent) $absCount++;
                    }
                    $totalAbs = $absCount * $halfDuree;
                ?>
                <tr class="<?php echo e($absCount > 0 ? 'row-has-absence' : ''); ?>">
                    <td class="td-name">
                        <div style="display:flex;align-items:center;gap:8px;">
                            <div class="avatar">
                                <?php echo e(strtoupper(mb_substr($stagiaire->name, 0, 1) . mb_substr(explode(' ', $stagiaire->name)[1] ?? '', 0, 1))); ?>

                            </div>
                            <div>
                                <div style="font-weight:600;color:#1e293b;">
                                    <?php echo e($stagiaire->name); ?>

                                    <?php if($isStagiaire): ?>
                                        <span style="font-size:9px;font-weight:700;padding:1px 7px;border-radius:99px;background:#eff6ff;color:#1e40af;margin-left:4px;">Moi</span>
                                    <?php endif; ?>
                                </div>
                                <div style="font-size:10px;color:#94a3b8;"><?php echo e($stagiaire->email); ?></div>
                            </div>
                        </div>
                    </td>
                    <?php $__currentLoopData = $activeParts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $part): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <td class="td-part"
                        style="border-left:2px solid <?php echo e($partConfig[$part]['th']); ?>;">
                        <?php if($statuses[$part]): ?>
                            <span class="half-badge" style="background:#fee2e2;color:#dc2626;">❌ Absent</span>
                        <?php else: ?>
                            <span class="half-badge" style="background:#d1fae5;color:#059669;">✓ Présent</span>
                        <?php endif; ?>
                    </td>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    <td class="td-bilan">
                        <?php if($totalAbs > 0): ?>
                            <span style="font-size:12px;font-weight:800;color:#dc2626;"><?php echo e($totalAbs); ?>h</span>
                        <?php else: ?>
                            <span style="font-size:11px;color:#059669;font-weight:700;">✓ OK</span>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </tbody>
        </table>
        </div>

    <?php else: ?>
        
        <?php if($emploi->statut === 'annule'): ?>
            <div style="padding:14px 20px;background:#fff7ed;border-top:1px solid #fde68a;">
                <p style="font-size:12px;color:#b45309;margin:0;font-weight:600;">
                    ⚠️ Séance annulée — la saisie de présence est désactivée.
                </p>
            </div>
        <?php endif; ?>

        
        <div class="select-all-bar">
            <span style="font-size:10px;font-weight:700;color:#92400e;">Actions rapides :</span>
            <?php $__currentLoopData = $activeParts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $part): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <button type="button"
                        class="btn-select-all"
                        style="color:<?php echo e($partConfig[$part]['color']); ?>;border-color:<?php echo e($partConfig[$part]['border']); ?>;"
                        onclick="toggleAllPart('<?php echo e($part); ?>', true)">
                    Tous absents <?php echo e(strtoupper($part)); ?>

                </button>
                <button type="button"
                        class="btn-select-all"
                        style="color:#64748b;border-color:#e2e8f0;"
                        onclick="toggleAllPart('<?php echo e($part); ?>', false)">
                    Tous présents <?php echo e(strtoupper($part)); ?>

                </button>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>

        <form id="presence-form"
              method="POST"
              action="<?php echo e(route('seances.presence', $emploi)); ?>"
              style="<?php echo e($emploi->statut === 'annule' ? 'pointer-events:none;opacity:.55;' : ''); ?>">
        <?php echo csrf_field(); ?>

        <div style="overflow-x:auto;">
        <table class="ptbl" id="presence-table">
            <thead>
                <tr>
                    <th class="th-name" style="min-width:200px;">Stagiaire</th>
                    <?php $__currentLoopData = $activeParts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $part): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <th class="th-part"
                            style="background:<?php echo e($partConfig[$part]['bg']); ?>;
                                   color:<?php echo e($partConfig[$part]['color']); ?>;
                                   border-left:2px solid <?php echo e($partConfig[$part]['th']); ?>;
                                   min-width:80px;">
                            <?php echo e(strtoupper($part)); ?><br>
                            <span style="font-size:8px;font-weight:500;opacity:.7;">2.5h · ☑ = Absent</span>
                        </th>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    <th class="th-bilan" style="min-width:80px;">Bilan</th>
                </tr>
            </thead>
            <tbody>
            <?php $__currentLoopData = $stagiaires; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $stagiaire): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php
                    $isAbsent = [];
                    $absCount = 0;
                    foreach ($activeParts as $part) {
                        $rec = $presences->get($stagiaire->id . '_' . $part);
                        $isAbsent[$part] = $rec?->type === 'absence';
                        if ($isAbsent[$part]) $absCount++;
                    }
                    $initials = strtoupper(
                        mb_substr($stagiaire->name, 0, 1) .
                        mb_substr(explode(' ', $stagiaire->name)[1] ?? '', 0, 1)
                    );
                    $totalHistorical = \App\Models\AbsenceRetard::where('id_user', $stagiaire->id)
                        ->where('type', 'absence')
                        ->sum('duree');
                ?>
                <tr id="row-<?php echo e($i); ?>" class="<?php echo e($absCount > 0 ? 'row-has-absence' : ''); ?>">

                    
                    <td class="td-name">
                        <input type="hidden"
                               name="presences[<?php echo e($i); ?>][stagiaire_id]"
                               value="<?php echo e($stagiaire->id); ?>">

                        <div style="display:flex;align-items:center;gap:9px;">
                            <div class="avatar"><?php echo e($initials); ?></div>
                            <div>
                                <div style="font-weight:700;color:#1e293b;font-size:12px;"><?php echo e($stagiaire->name); ?></div>
                                <div style="font-size:10px;color:#94a3b8;"><?php echo e($stagiaire->email); ?></div>
                                <?php if($totalHistorical > 0): ?>
                                    <span style="display:inline-block;margin-top:2px;font-size:9px;font-weight:700;
                                                 padding:1px 7px;border-radius:99px;background:#fee2e2;color:#dc2626;">
                                        <?php echo e($totalHistorical); ?>h cumulées
                                    </span>
                                <?php endif; ?>
                            </div>
                        </div>
                    </td>

                    
                    <?php $__currentLoopData = $activeParts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $part): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <td class="td-part"
                        style="border-left:2px solid <?php echo e($partConfig[$part]['th']); ?>;">
                        <div class="abs-checkbox-wrap">
                            <input type="checkbox"
                                   class="abs-cb"
                                   id="cb_<?php echo e($i); ?>_<?php echo e($part); ?>"
                                   name="presences[<?php echo e($i); ?>][<?php echo e($part); ?>]"
                                   value="1"
                                   data-idx="<?php echo e($i); ?>"
                                   data-part="<?php echo e($part); ?>"
                                   onchange="updateBilan(<?php echo e($i); ?>)"
                                   <?php echo e($isAbsent[$part] ? 'checked' : ''); ?>>
                            <label for="cb_<?php echo e($i); ?>_<?php echo e($part); ?>"
                                   style="font-size:8px;color:#94a3b8;cursor:pointer;margin:0;">
                                absent
                            </label>
                        </div>
                    </td>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                    
                    <td class="td-bilan">
                        <div id="bilan_<?php echo e($i); ?>">
                            <?php if($absCount > 0): ?>
                                <span style="font-size:12px;font-weight:800;color:#dc2626;">
                                    <?php echo e($absCount * $halfDuree); ?>h
                                </span>
                                <div style="font-size:9px;color:#94a3b8;"><?php echo e($absCount); ?>/<?php echo e(count($activeParts)); ?> séance(s)</div>
                            <?php else: ?>
                                <span style="font-size:11px;color:#059669;font-weight:700;">✓ Présent</span>
                            <?php endif; ?>
                        </div>
                    </td>
                </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </tbody>
        </table>
        </div>

        
        <div class="legend">
            <span class="legend-item">
                <input type="checkbox" checked disabled style="accent-color:#dc2626;width:14px;height:14px;">
                = Absent (2.5h comptabilisées)
            </span>
            <span class="legend-item" style="color:#059669;">
                ☐ = Présent (défaut, aucune action)
            </span>
            <?php $__currentLoopData = $activeParts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $part): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <span class="legend-item" style="color:<?php echo e($partConfig[$part]['color']); ?>;">
                    <span style="width:10px;height:10px;border-radius:3px;background:<?php echo e($partConfig[$part]['bg']); ?>;border:1.5px solid <?php echo e($partConfig[$part]['color']); ?>;display:inline-block;"></span>
                    <?php echo e(strtoupper($part)); ?> = 2.5h
                </span>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>

        </form>
    <?php endif; ?>
</div>


<div class="section">
    <div class="section-head">
        <div>
            <div class="section-title">
                <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                Ressources de la séance
            </div>
            <div class="section-sub"><?php echo e($coursItems->count()); ?> document(s) partagé(s)</div>
        </div>
    </div>

    <?php $__empty_1 = true; $__currentLoopData = $coursItems; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $doc): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
        <?php
            $icon   = match(true) {
                str_ends_with(strtolower($doc->fichier[0] ?? ''), '.pdf') => '📄',
                !empty($doc->lien) => '🔗',
                default            => '📝',
            };
            $iconBg = !empty($doc->lien) ? '#eff6ff' : '#f0fdf4';
        ?>
        <div class="res-item">
            <div class="res-icon" style="background:<?php echo e($iconBg); ?>;"><?php echo e($icon); ?></div>
            <div style="flex:1;min-width:0;">
                <div class="res-title"><?php echo e($doc->titre); ?></div>
                <?php if($doc->description): ?>
                    <div class="res-desc"><?php echo e(Str::limit($doc->description, 120)); ?></div>
                <?php endif; ?>
                <?php if(!empty($doc->lien)): ?>
                    <a href="<?php echo e($doc->lien); ?>" target="_blank" class="res-link" style="margin-top:4px;display:inline-block;">
                        🔗 Ouvrir le lien
                    </a>
                <?php endif; ?>
                <?php if(!empty($doc->fichier[0])): ?>
                    <a href="<?php echo e(Storage::url($doc->fichier[0])); ?>" target="_blank" class="res-link" style="margin-top:4px;display:inline-block;">
                        ⬇ Télécharger
                    </a>
                <?php endif; ?>
                <div style="font-size:9px;color:#cbd5e1;margin-top:4px;">
                    Ajouté <?php echo e($doc->created_at?->diffForHumans()); ?>

                    <?php if($doc->formateur): ?> par <?php echo e($doc->formateur->name); ?> <?php endif; ?>
                </div>
            </div>
            <?php if($canEditClassroom): ?>
                <form method="POST"
                      action="<?php echo e(route('seances.ressource.destroy', [$emploi, $doc])); ?>"
                      onsubmit="return confirm('Supprimer « <?php echo e(addslashes($doc->titre)); ?> » ?')">
                    <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                    <button type="submit" class="btn-danger">Supprimer</button>
                </form>
            <?php endif; ?>
        </div>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
        <div style="padding:30px;text-align:center;color:#94a3b8;font-size:13px;">
            Aucune ressource partagée pour cette séance.
        </div>
    <?php endif; ?>

    
    <?php if($canEditClassroom): ?>
    <div class="add-form">
        <div style="font-size:11px;font-weight:800;color:#64748b;text-transform:uppercase;letter-spacing:.8px;margin-bottom:12px;">
            + Ajouter une ressource
        </div>
        <form method="POST"
              action="<?php echo e(route('seances.ressource.store', $emploi)); ?>"
              enctype="multipart/form-data">
        <?php echo csrf_field(); ?>
        <div class="form-grid">
            <div class="form-group">
                <label class="form-label">Titre *</label>
                <input type="text" name="titre" class="form-input"
                       placeholder="Ex: Cours chapitre 3" required maxlength="255">
            </div>
            <div class="form-group">
                <label class="form-label">Type *</label>
                <select name="type" class="form-select" id="res-type-select"
                        onchange="toggleResFields(this.value)">
                    <option value="pdf">📄 Fichier</option>
                    <option value="lien">🔗 Lien URL</option>
                    <option value="texte">📝 Texte</option>
                </select>
            </div>
            <div class="form-group" id="res-file-group">
                <label class="form-label">Fichier (PDF, Word, ZIP…)</label>
                <input type="file" name="fichier" class="form-input"
                       accept=".pdf,.doc,.docx,.zip,.png,.jpg,.jpeg,.txt,.pptx,.xlsx">
            </div>
            <div class="form-group" id="res-url-group" style="display:none;">
                <label class="form-label">URL</label>
                <input type="url" name="lien" class="form-input" placeholder="https://…">
            </div>
            <div class="form-group" style="grid-column:1/-1;">
                <label class="form-label">Description (optionnel)</label>
                <textarea name="description" class="form-textarea"
                          placeholder="Résumé, consignes…" maxlength="10000"></textarea>
            </div>
        </div>
        <div style="margin-top:12px;">
            <button type="submit" class="btn-primary">📤 Partager la ressource</button>
        </div>
        </form>
    </div>
    <?php endif; ?>
</div>

</div>

<script>
const HALF_DUREE   = <?php echo e($halfDuree); ?>;
const ACTIVE_PARTS = <?php echo json_encode($activeParts, 15, 512) ?>;

function updateBilan(idx) {
    let absCount = 0;
    ACTIVE_PARTS.forEach(part => {
        const cb = document.getElementById(`cb_${idx}_${part}`);
        if (cb && cb.checked) absCount++;
    });
    const row   = document.getElementById(`row-${idx}`);
    const bilan = document.getElementById(`bilan_${idx}`);
    row.classList.toggle('row-has-absence', absCount > 0);
    if (absCount === 0) {
        bilan.innerHTML = '<span style="font-size:11px;color:#059669;font-weight:700;">✓ Présent</span>';
    } else {
        bilan.innerHTML = `
            <span style="font-size:12px;font-weight:800;color:#dc2626;">${absCount * HALF_DUREE}h</span>
            <div style="font-size:9px;color:#94a3b8;">${absCount}/${ACTIVE_PARTS.length} séance(s)</div>
        `;
    }
}

function toggleAllPart(part, absent) {
    document.querySelectorAll(`.abs-cb[data-part="${part}"]`).forEach(cb => {
        cb.checked = absent;
        updateBilan(cb.dataset.idx);
    });
}

function toggleResFields(type) {
    document.getElementById('res-file-group').style.display = type === 'lien' ? 'none' : '';
    document.getElementById('res-url-group').style.display  = type === 'lien' ? '' : 'none';
}
</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\Project\gestion-CF\resources\views/seances/show.blade.php ENDPATH**/ ?>