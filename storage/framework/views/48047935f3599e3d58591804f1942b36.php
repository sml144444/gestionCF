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

    $todayStr = \Carbon\Carbon::today()->toDateString();
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

/* ─── ADMIN DAY PANEL ─── */
.day-panel        { background:white; border-radius:18px; border:1px solid #e2e8f0;
                    margin-bottom:20px; overflow:hidden; }
.day-panel-head   { display:flex; align-items:center; justify-content:space-between; gap:12px;
                    padding:14px 20px; border-bottom:1px solid #f1f5f9; flex-wrap:wrap; }
.day-nav          { display:flex; align-items:center; gap:8px; flex-wrap:wrap; }
.day-nav-btn      { display:inline-flex; align-items:center; gap:4px; padding:6px 14px;
                    border-radius:10px; border:1.5px solid #e2e8f0; background:white;
                    font-size:11px; font-weight:700; color:#475569; text-decoration:none;
                    transition:all .15s; cursor:pointer; }
.day-nav-btn:hover { border-color:#cbd5e1; background:#f8fafc; }
.day-nav-btn.today-btn { background:var(--accent-gr); color:white; border-color:transparent; }
.day-nav-btn.today-btn:hover { opacity:.88; }
.day-nav-btn.disabled { opacity:.35; pointer-events:none; }
.day-date-display { font-size:15px; font-weight:800; color:#1e293b; }
.day-date-sub     { font-size:10px; color:#94a3b8; margin-top:1px; }

.day-date-input   { font-size:11px; font-weight:600; padding:6px 10px; border:1.5px solid #e2e8f0;
                    border-radius:10px; background:#f8fafc; color:#1e293b; outline:none;
                    cursor:pointer; transition:border-color .15s; }
.day-date-input:focus { border-color:var(--accent); }

.day-absent-count { display:inline-flex; align-items:center; gap:6px; padding:5px 13px;
                    border-radius:99px; background:#fee2e2; color:#dc2626;
                    font-size:12px; font-weight:800; border:1px solid #fca5a5; }

/* Day navigator pills */
.day-pills        { display:flex; gap:6px; overflow-x:auto; padding:10px 20px;
                    border-bottom:1px solid #f1f5f9; scrollbar-width:none; }
.day-pills::-webkit-scrollbar { display:none; }
.day-pill         { flex-shrink:0; padding:5px 12px; border-radius:99px; border:1.5px solid #e2e8f0;
                    background:white; font-size:10px; font-weight:700; color:#64748b;
                    text-decoration:none; transition:all .15s; white-space:nowrap; }
.day-pill:hover   { border-color:var(--accent-bd); color:var(--accent-tx); }
.day-pill.active  { background:var(--accent-gr); color:white; border-color:transparent; }
.day-pill.today   { border-color:var(--accent); color:var(--accent-tx); }

/* Day absent table */
table.day-table   { width:100%; border-collapse:collapse; }
table.day-table thead th { padding:9px 14px; font-size:9px; font-weight:800; color:#94a3b8;
                            text-transform:uppercase; letter-spacing:.8px; background:#f8fafc;
                            border-bottom:1px solid #e2e8f0; text-align:left; white-space:nowrap; }
table.day-table tbody tr { border-bottom:1px solid #f1f5f9; transition:background .12s; }
table.day-table tbody tr:last-child { border-bottom:none; }
table.day-table tbody tr:hover { background:#fafbfd; }
table.day-table tbody td { padding:11px 14px; font-size:12px; color:#374151; vertical-align:middle; }
table.day-table tbody tr.row-pending { background:rgba(251,191,36,0.05); }

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

.row-pending { background:rgba(251,191,36,0.04); }

/* ─── DAY-VIEW: hours pill ─── */
.hours-pill {
    display:inline-flex; align-items:center; justify-content:center;
    min-width:52px; padding:5px 12px;
    border-radius:99px;
    background:#fee2e2; color:#dc2626;
    font-size:16px; font-weight:900; line-height:1;
    border:1.5px solid #fca5a5;
}

/* ─── DAY-VIEW: date block ─── */
.date-block-day  { font-size:22px; font-weight:900; color:#1e293b; line-height:1; }
.date-block-rest { font-size:10px; color:#94a3b8; margin-top:2px; }

/* ─── BADGES ─── */
.badge            { display:inline-flex; align-items:center; gap:4px; padding:3px 9px; border-radius:8px; font-size:10px; font-weight:800; }
.badge-justifie   { background:#d1fae5; color:#059669; border:1px solid #a7f3d0; }
.badge-injustifie { background:#fce7f3; color:#be185d; border:1px solid #fbcfe8; }
.badge-pending    { background:#fef3c7; color:#92400e; border:1px solid #fde68a; }

/* NEW: Admin validation badges and buttons */
.btn-admin-allow {
    font-size:10px; font-weight:700; padding:4px 10px; border-radius:8px;
    background:#fef9c3; color:#713f12;
    border:1px solid #fde047;
    cursor:pointer;
    display:inline-flex; align-items:center; gap:4px;
    transition:all .15s;
    white-space:nowrap;
}
.btn-admin-allow:hover { background:#fef08a; }

.btn-admin-revert {
    font-size:10px; font-weight:700; padding:4px 10px; border-radius:8px;
    background:#f1f5f9; color:#64748b;
    border:1px solid #cbd5e1;
    cursor:pointer;
    display:inline-flex; align-items:center; gap:4px;
    transition:all .15s;
    white-space:nowrap;
}
.btn-admin-revert:hover { background:#e2e8f0; }

.badge-admin-allowed {
    display:inline-flex; align-items:center; gap:4px;
    padding:3px 9px; border-radius:8px; font-size:10px; font-weight:800;
    background:#fef9c3; color:#713f12;
    border:1px solid #fde047;
}

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
.btn-toggle { font-size:10px; font-weight:700; padding:4px 10px; border-radius:8px;
              background:#eff6ff; color:#1d4ed8; border:1px solid #bfdbfe;
              cursor:pointer; display:inline-flex; align-items:center; gap:3px; transition:all .15s; }
.btn-toggle:hover { background:#dbeafe; }
.btn-upload-label { display:inline-flex; align-items:center; gap:4px; cursor:pointer;
                    font-size:10px; font-weight:600; color:var(--accent);
                    padding:4px 9px; border-radius:8px;
                    border:1.5px dashed var(--accent-bd);
                    background:var(--accent-ltr); white-space:nowrap; transition:all .15s; }
.btn-upload-label:hover { background:var(--accent-lt); }

/* ─── Day panel actions cell ─── */
.day-action-row { display:flex; flex-direction:column; gap:5px; }
.day-action-part { display:flex; align-items:center; gap:5px; flex-wrap:wrap;
                   padding:5px 8px; border-radius:10px; background:#f8fafc;
                   border:1px solid #f1f5f9; }
.day-action-part:hover { background:#f1f5f9; }

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
            Cliquez sur <strong>📎 Joindre un justificatif</strong> en face de la journée concernée et envoyez votre document (PDF, image…).
            Un seul fichier couvre toutes les demi-séances de la journée.
            Le statut passera en <strong>🕐 En attente</strong> jusqu'à validation par l'administration.
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
                <strong style="color:white"><?php echo e($stats['total']); ?></strong> absence(s) ·
                <strong style="color:white"><?php echo e($stats['total_heures_abs']); ?>h</strong> cumulées
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


<div class="stats-grid" id="abs-stats-grid">
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


<div id="abs-day-panel-wrap">
<?php if($canViewAll && $selectedDay): ?>
<?php
    $selDayStr    = $selectedDay->toDateString();
    $isToday      = $selDayStr === $todayStr;
    $dayLabel     = $isToday ? 'Aujourd\'hui' : $selectedDay->translatedFormat('l d M Y');
?>
<div class="day-panel">

    
    <div class="day-panel-head">
        <div style="display:flex;align-items:center;gap:14px;flex-wrap:wrap;">
            <div style="width:40px;height:40px;border-radius:12px;background:var(--accent-lt);
                        display:flex;align-items:center;justify-content:center;font-size:20px;flex-shrink:0;">
                📅
            </div>
            <div>
                <div class="day-date-display"><?php echo e($dayLabel); ?></div>
                <?php if(!$isToday): ?>
                    <div class="day-date-sub"><?php echo e($selectedDay->format('d/m/Y')); ?></div>
                <?php endif; ?>
            </div>
            <?php if($dayAbsents->count() > 0): ?>
                <span class="day-absent-count">
                    ❌ <?php echo e($dayAbsents->count()); ?> absent(s)
                </span>
            <?php else: ?>
                <span style="display:inline-flex;align-items:center;gap:5px;padding:5px 13px;
                             border-radius:99px;background:#d1fae5;color:#059669;
                             font-size:12px;font-weight:800;border:1px solid #a7f3d0;">
                    ✅ Aucune absence
                </span>
            <?php endif; ?>
        </div>

        
        <div class="day-nav">
            <?php if($prevDay): ?>
                <a href="<?php echo e(request()->fullUrlWithQuery(['day' => $prevDay])); ?>"
                   class="day-nav-btn" title="Jour précédent avec absences">
                    ← <?php echo e(\Carbon\Carbon::parse($prevDay)->format('d/m')); ?>

                </a>
            <?php else: ?>
                <span class="day-nav-btn disabled">← Préc.</span>
            <?php endif; ?>

            <?php if(!$isToday): ?>
                <a href="<?php echo e(request()->fullUrlWithQuery(['day' => $todayStr])); ?>"
                   class="day-nav-btn today-btn">
                    📅 Aujourd'hui
                </a>
            <?php endif; ?>

            <form method="GET" action="<?php echo e(route('absences.index')); ?>" style="display:inline-flex;align-items:center;gap:6px;">
                <?php $__currentLoopData = request()->except('day'); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $k => $v): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <input type="hidden" name="<?php echo e($k); ?>" value="<?php echo e($v); ?>">
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                <input type="date"
                       name="day"
                       value="<?php echo e($selDayStr); ?>"
                       class="day-date-input"
                       onchange="this.form.submit()">
            </form>

            <?php if($nextDay): ?>
                <a href="<?php echo e(request()->fullUrlWithQuery(['day' => $nextDay])); ?>"
                   class="day-nav-btn" title="Jour suivant avec absences">
                    <?php echo e(\Carbon\Carbon::parse($nextDay)->format('d/m')); ?> →
                </a>
            <?php else: ?>
                <span class="day-nav-btn disabled">Suiv. →</span>
            <?php endif; ?>
        </div>
    </div>

    
    <?php if($availableDays->count() > 0): ?>
    <div class="day-pills">
        <?php $__currentLoopData = $availableDays->sortByDesc('day'); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $dRow): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <?php
                $dStr   = $dRow->day;
                $dCarb  = \Carbon\Carbon::parse($dStr);
                $isAct  = $dStr === $selDayStr;
                $isTod  = $dStr === $todayStr;
                $cls    = $isAct ? 'active' : ($isTod ? 'today' : '');
            ?>
            <a href="<?php echo e(request()->fullUrlWithQuery(['day' => $dStr])); ?>"
               class="day-pill <?php echo e($cls); ?>"
               title="<?php echo e($dRow->cnt); ?> absence(s)">
                <?php if($isTod && !$isAct): ?>🔴 <?php endif; ?>
                <?php echo e($dCarb->format('d/m')); ?>

                <span style="font-size:9px;opacity:.7;">(<?php echo e($dRow->cnt); ?>)</span>
            </a>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>
    <?php endif; ?>

    
    <?php if($dayAbsents->isEmpty()): ?>
        <div style="padding:36px 20px;text-align:center;color:#94a3b8;font-size:13px;">
            🎉 Aucune absence enregistrée pour cette journée.
        </div>
    <?php else: ?>
        <div style="overflow-x:auto;">
        <table class="day-table">
            <thead>
                <tr>
                    <th>Stagiaire</th>
                    <th>Groupe</th>
                    <th>Module(s)</th>
                    <th>Formateur(s)</th>
                    <th>Demi-séances</th>
                    <th>Total heures</th>
                    <th>Statut</th>
                    <?php if($canJustify): ?>
                    <th>Justificatif & Actions</th>
                    <?php endif; ?>
                </tr>
            </thead>
            <tbody>
            <?php $__currentLoopData = $dayAbsents; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $da): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php
                    $initials = strtoupper(
                        mb_substr($da->stagiaire?->name ?? '?', 0, 1) .
                        mb_substr(explode(' ', $da->stagiaire?->name ?? '')[1] ?? '', 0, 1)
                    );
                    $rowClass = $da->is_pending && !$da->is_justified ? 'row-pending' : '';
                ?>
                <tr class="<?php echo e($rowClass); ?>">

                    
                    <td>
                        <div style="display:flex;align-items:center;gap:8px;">
                            <div class="avatar"><?php echo e($initials); ?></div>
                            <div>
                                <div style="font-weight:700;color:#1e293b;font-size:12px;">
                                    <?php echo e($da->stagiaire?->name ?? '—'); ?>

                                </div>
                                <div style="font-size:10px;color:#94a3b8;">
                                    <?php echo e($da->stagiaire?->email ?? ''); ?>

                                </div>
                            </div>
                        </div>
                    </td>

                    
                    <td>
                        <span style="font-size:11px;font-weight:600;color:#475569;">
                            <?php echo e($da->stagiaire?->groupe?->name ?? '—'); ?>

                        </span>
                    </td>

                    
                    <td>
                        <div style="display:flex;flex-direction:column;gap:2px;">
                            <?php $__empty_1 = true; $__currentLoopData = $da->modules; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $mod): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                <span style="font-size:11px;font-weight:600;color:#1e293b;">
                                    <?php echo e($mod->name); ?>

                                </span>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                <span style="color:#94a3b8;">—</span>
                            <?php endif; ?>
                        </div>
                    </td>

                    
                    <td>
                        <div style="display:flex;flex-direction:column;gap:2px;">
                            <?php $__empty_1 = true; $__currentLoopData = $da->formateurs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $form): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                <span style="font-size:11px;color:#475569;"><?php echo e($form->name); ?></span>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                <span style="color:#94a3b8;">—</span>
                            <?php endif; ?>
                        </div>
                    </td>

                    
                    <td>
                        <div style="display:flex;flex-wrap:wrap;gap:3px;">
                            <?php $__currentLoopData = $da->parts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $part): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <?php $pc = $partConfig[$part] ?? $partConfig['s1']; ?>
                                <span style="display:inline-flex;align-items:center;padding:2px 8px;
                                             border-radius:6px;font-size:10px;font-weight:800;
                                             background:<?php echo e($pc['bg']); ?>;color:<?php echo e($pc['color']); ?>;
                                             border:1px solid <?php echo e($pc['border']); ?>;">
                                    <?php echo e(strtoupper($part)); ?>

                                </span>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                    </td>

                    
                    <td>
                        <span class="hours-pill" style="font-size:14px;min-width:44px;">
                            <?php echo e($da->total_duree); ?>h
                        </span>
                    </td>

                    
                    <td>
                        <?php if($da->is_justified): ?>
                            <span class="badge badge-justifie">✅ Justifiée(s)</span>
                        <?php elseif($da->is_pending): ?>
                            <span class="badge badge-pending">🕐 En attente</span>
                        <?php else: ?>
                            <span class="badge badge-injustifie">⚠️ Non justifiée(s)</span>
                        <?php endif; ?>
                    </td>

                    
                    <?php if($canJustify): ?>
                    <td style="min-width:260px;">
                        <?php
                            $allAbsIds  = $da->absences->pluck('id');
                            $allJust    = $da->absences->every(fn($a) => $a->justifie);
                            $anyPending = $da->absences->contains(
                                fn($a) => !$a->justifie && !empty($a->file_justification)
                            );
                            $sharedFile = $da->absences->first(fn($a) => $a->file_justification)?->file_justification;
                        ?>

                        
                        <?php if($da->is_admin_validated): ?>
                            <div style="display:flex;flex-direction:column;gap:6px;">
                                <span class="badge-admin-allowed">✔ Autorisé sans justificatif</span>
                                <form method="POST" action="<?php echo e(route('absences.admin.annuler')); ?>" style="display:inline;">
                                    <?php echo csrf_field(); ?>
                                    <?php $__currentLoopData = $allAbsIds; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $id): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <input type="hidden" name="absence_ids[]" value="<?php echo e($id); ?>">
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    <button type="submit" class="btn-admin-revert" title="Rétablir le signalement formateur">
                                        ↩ Annuler l'autorisation
                                    </button>
                                </form>
                            </div>

                        
                        <?php elseif($allJust): ?>
                            <?php if($sharedFile): ?>
                                <a href="<?php echo e(Storage::url($sharedFile)); ?>" target="_blank"
                                   style="font-size:11px;font-weight:700;color:var(--accent);
                                          text-decoration:none;display:inline-flex;align-items:center;gap:4px;margin-bottom:8px;">
                                    📎 Voir le justificatif
                                </a><br>
                            <?php else: ?>
                                <span class="badge badge-justifie" style="margin-bottom:8px;display:inline-flex;">✅ Toutes justifiées</span><br>
                            <?php endif; ?>

                            
                            <form method="POST" action="<?php echo e(route('absences.admin.bulk.unjustify')); ?>"
                                  onsubmit="return confirm('Annuler la justification pour toutes les demi-séances de cette journée ?')">
                                <?php echo csrf_field(); ?>
                                <?php $__currentLoopData = $allAbsIds; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $id): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <input type="hidden" name="absence_ids[]" value="<?php echo e($id); ?>">
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                <button type="submit" class="btn-toggle">↩ Annuler toutes</button>
                            </form>

                        
                        <?php elseif($anyPending): ?>
                            <?php if($sharedFile): ?>
                                <a href="<?php echo e(Storage::url($sharedFile)); ?>" target="_blank"
                                   style="font-size:11px;font-weight:700;color:#92400e;
                                          text-decoration:none;display:inline-flex;align-items:center;gap:4px;margin-bottom:6px;">
                                    📎 Voir le justificatif
                                </a><br>
                            <?php endif; ?>
                            
                            <div style="display:flex;flex-direction:column;gap:4px;margin-top:4px;">
                                <?php $__currentLoopData = $da->absences->where('justifie', false)->filter(fn($a) => !empty($a->file_justification)); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $abs): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <?php $pc = $partConfig[$abs->session_part ?? 's1'] ?? $partConfig['s1']; ?>
                                    <div style="display:flex;align-items:center;gap:5px;flex-wrap:wrap;">
                                        <span style="font-size:9px;font-weight:800;padding:1px 6px;border-radius:5px;
                                                     background:<?php echo e($pc['bg']); ?>;color:<?php echo e($pc['color']); ?>;
                                                     border:1px solid <?php echo e($pc['border']); ?>;">
                                            <?php echo e(strtoupper($abs->session_part)); ?>

                                        </span>
                                        <form method="POST" action="<?php echo e(route('absences.accept', $abs)); ?>" style="display:inline;">
                                            <?php echo csrf_field(); ?> <?php echo method_field('PATCH'); ?>
                                            <button type="submit" class="btn-accept" style="font-size:9px;padding:2px 7px;">
                                                ✓ Accepter
                                            </button>
                                        </form>
                                        <form method="POST" action="<?php echo e(route('absences.reject', $abs)); ?>"
                                              onsubmit="return confirm('Rejeter ce justificatif ?')" style="display:inline;">
                                            <?php echo csrf_field(); ?> <?php echo method_field('PATCH'); ?>
                                            <button type="submit" class="btn-reject" style="font-size:9px;padding:2px 7px;">
                                                ✕ Rejeter
                                            </button>
                                        </form>
                                    </div>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </div>

                        
                        <?php else: ?>
                            
                            <form method="POST"
                                  action="<?php echo e(route('absences.admin.fichier.jour')); ?>"
                                  enctype="multipart/form-data"
                                  style="margin-bottom:8px;">
                                <?php echo csrf_field(); ?>
                                <?php $__currentLoopData = $allAbsIds; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $id): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <input type="hidden" name="absence_ids[]" value="<?php echo e($id); ?>">
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                <label class="btn-upload-label">
                                    <svg width="10" height="10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                                    </svg>
                                    📎 Joindre un justificatif
                                    <input type="file" name="file_justification"
                                           accept=".pdf,.doc,.docx,.jpg,.jpeg,.png"
                                           style="display:none;"
                                           onchange="this.closest('form').submit()">
                                </label>
                                <div style="font-size:9px;color:#94a3b8;margin-top:2px;margin-bottom:8px;">
                                    Couvre les <?php echo e($allAbsIds->count()); ?> demi-séance(s) du jour
                                </div>
                            </form>

                            
                            <form method="POST" action="<?php echo e(route('absences.admin.valider')); ?>"
                                  style="margin-bottom:8px;"
                                  onsubmit="return confirm('⚠️ Autoriser cette absence sans justificatif ?\n\nLe signalement formateur sera supprimé mais l\'absence restera non-justifiée.')">
                                <?php echo csrf_field(); ?>
                                <?php $__currentLoopData = $allAbsIds; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $id): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <input type="hidden" name="absence_ids[]" value="<?php echo e($id); ?>">
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                <button type="submit" class="btn-admin-allow"
                                        title="L'absence reste non-justifiée mais le signalement formateur disparaît">
                                    🔓 Autoriser sans justificatif
                                </button>
                            </form>

                            
                            <form method="POST" action="<?php echo e(route('absences.admin.bulk.justify')); ?>" style="margin-top:6px;">
                                <?php echo csrf_field(); ?>
                                <?php $__currentLoopData = $allAbsIds; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $id): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <input type="hidden" name="absence_ids[]" value="<?php echo e($id); ?>">
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                <button type="submit" class="btn-accept">✓ Justifier toutes</button>
                            </form>
                        <?php endif; ?>
                    </td>
                    <?php endif; ?>
                    

                </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </tbody>
        </table>
        </div>
        <div style="padding:10px 20px;border-top:1px solid #f1f5f9;font-size:11px;color:#94a3b8;
                    display:flex;align-items:center;gap:8px;flex-wrap:wrap;">
            📋 <?php echo e($dayAbsents->count()); ?> stagiaire(s) absent(s) ·
            <strong style="color:#dc2626;">
                <?php echo e(round($dayAbsents->sum('total_duree'), 1)); ?>h
            </strong> total du jour
            <?php if($dayAbsents->where('is_pending', true)->count() > 0): ?>
                · <span style="color:#92400e;font-weight:700;">
                    🕐 <?php echo e($dayAbsents->where('is_pending', true)->count()); ?> justificatif(s) en attente
                </span>
            <?php endif; ?>
        </div>
    <?php endif; ?>
</div>
<?php endif; ?>
</div>


<form id="abs-filter-form" method="GET" action="<?php echo e(route('absences.index')); ?>" class="filter-bar">

    <?php if($canViewAll && request()->filled('day')): ?>
        <input type="hidden" name="day" id="abs-day-hidden" value="<?php echo e(request('day')); ?>">
    <?php endif; ?>

    <?php if($canViewAll): ?>
    <div class="filter-group">
        <label class="filter-label">Demi-séance</label>
        <select name="session_part" class="filter-select abs-auto-filter">
            <option value="">Toutes (S1–S4)</option>
            <option value="s1" <?php if(request('session_part') === 's1'): echo 'selected'; endif; ?>>S1</option>
            <option value="s2" <?php if(request('session_part') === 's2'): echo 'selected'; endif; ?>>S2</option>
            <option value="s3" <?php if(request('session_part') === 's3'): echo 'selected'; endif; ?>>S3</option>
            <option value="s4" <?php if(request('session_part') === 's4'): echo 'selected'; endif; ?>>S4</option>
        </select>
    </div>

    <div class="filter-group">
        <label class="filter-label">Filière</label>
        <select name="filiere_id" class="filter-select abs-auto-filter">
            <option value="">Toutes les filières</option>
            <?php $__currentLoopData = $filieres; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $filiere): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <option value="<?php echo e($filiere->id); ?>" <?php if(request('filiere_id') == $filiere->id): echo 'selected'; endif; ?>>
                    <?php echo e($filiere->name); ?>

                </option>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </select>
    </div>

    <div class="filter-group">
        <label class="filter-label">Groupe</label>
        <select name="groupe_id" class="filter-select abs-auto-filter">
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
        <select name="stagiaire_id" class="filter-select abs-auto-filter">
            <option value="">Tous</option>
            <?php $__currentLoopData = $stagiaires; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $s): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <option value="<?php echo e($s->id); ?>" <?php if(request('stagiaire_id') == $s->id): echo 'selected'; endif; ?>>
                    <?php echo e($s->name); ?>

                </option>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </select>
    </div>
    <?php endif; ?>

    <div class="filter-group">
        <label class="filter-label">Statut</label>
        <select name="justifie" class="filter-select abs-auto-filter">
            <option value="">Tous les statuts</option>
            <option value="1"       <?php if(request('justifie') === '1'): echo 'selected'; endif; ?>>✅ Justifiée</option>
            <option value="pending" <?php if(request('justifie') === 'pending'): echo 'selected'; endif; ?>>🕐 En attente</option>
            <option value="0"       <?php if(request('justifie') === '0'): echo 'selected'; endif; ?>>⚠️ Non justifiée</option>
        </select>
    </div>

    <button type="submit" class="btn-filter" id="abs-filter-btn">
        <span id="abs-filter-spinner" style="display:none;">⏳</span>
        🔍 Filtrer
    </button>

    <a href="<?php echo e(route('absences.index', request()->only(['day']))); ?>"
       id="abs-reset-btn"
       class="btn-reset"
       style="<?php echo e(request()->hasAny(['justifie','groupe_id','stagiaire_id','session_part','filiere_id']) ? '' : 'display:none;'); ?>">
        <svg width="11" height="11" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/>
        </svg>
        Réinitialiser
    </a>
</form>


<div class="abs-table-wrap" id="abs-table-wrap">
    <div class="abs-table-head">
        <div>
            <div style="font-size:10px;font-weight:800;color:#94a3b8;letter-spacing:1.5px;text-transform:uppercase;">
                📋 Historique des absences
            </div>
            <div style="font-size:12px;color:#64748b;margin-top:2px;">
                <?php if(!$canViewAll): ?>
                    <?php echo e($absencesByDay->total()); ?> jour(s) d'absence — page <?php echo e($absencesByDay->currentPage()); ?>/<?php echo e($absencesByDay->lastPage()); ?>

                <?php else: ?>
                    <?php echo e($absencesGrouped->total()); ?> résultat(s) — page <?php echo e($absencesGrouped->currentPage()); ?>/<?php echo e($absencesGrouped->lastPage()); ?>

                <?php endif; ?>
            </div>
        </div>
        <?php if($canJustify && $stats['en_attente'] > 0): ?>
        <div style="display:flex;align-items:center;gap:6px;font-size:11px;color:#92400e;
                    background:#fef3c7;border:1px solid #fde68a;padding:6px 12px;border-radius:10px;">
            🕐 <strong><?php echo e($stats['en_attente']); ?></strong> justificatif(s) en attente de validation
        </div>
        <?php endif; ?>
    </div>

    
    <?php if(!$canViewAll): ?>

        <?php if($absencesByDay->isEmpty()): ?>
            <div class="empty-state">
                <div class="empty-state-icon">🎉</div>
                <p style="font-size:15px;font-weight:700;color:#1e293b;margin:0 0 6px;">Aucune absence enregistrée</p>
                <p style="font-size:12px;color:#94a3b8;margin:0;">
                    <?php echo e(request()->hasAny(['justifie','session_part'])
                        ? 'Aucun résultat pour ces filtres.'
                        : 'Parfait ! Aucune absence pour le moment.'); ?>

                </p>
            </div>
        <?php else: ?>
            <div style="overflow-x:auto;">
            <table class="abs-table">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Module(s) / Séance(s)</th>
                        <th>Formateur(s)</th>
                        <th>Demi-séances absentes</th>
                        <th>Total heures</th>
                        <th>Statut</th>
                        <th>Justificatif</th>
                    </tr>
                </thead>
                <tbody>
                <?php $__currentLoopData = $absencesByDay; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $day): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php
                        $rowClass = $day->is_pending && !$day->is_justified ? 'row-pending' : '';
                    ?>
                    <tr class="<?php echo e($rowClass); ?>">

                        
                        <td>
                            <div class="date-block-day"><?php echo e($day->date?->format('d')); ?></div>
                            <div class="date-block-rest"><?php echo e($day->date?->translatedFormat('M Y')); ?></div>
                            <div class="date-block-rest"><?php echo e($day->date?->translatedFormat('l')); ?></div>
                        </td>

                        
                        <td>
                            <?php $__currentLoopData = $day->emplois; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $emp): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <div style="margin-bottom:4px;padding-bottom:4px;
                                            <?php echo e(!$loop->last ? 'border-bottom:1px dashed #f1f5f9;' : ''); ?>">
                                    <div style="font-weight:600;color:#1e293b;font-size:12px;">
                                        <?php echo e($emp->module?->name ?? '—'); ?>

                                    </div>
                                    <div style="font-size:10px;color:#94a3b8;margin-top:1px;">
                                        <?php echo e($emp->date_debut->format('H:i')); ?> – <?php echo e($emp->date_fin->format('H:i')); ?>

                                        <?php if($emp->salle): ?>
                                            · 🏫 <?php echo e($emp->salle->name); ?>

                                        <?php endif; ?>
                                    </div>
                                </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </td>

                        
                        <td>
                            <?php $__currentLoopData = $day->formateurs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $form): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <div style="font-size:11px;color:#475569;font-weight:500;">
                                    <?php echo e($form->name); ?>

                                </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            <?php if($day->formateurs->isEmpty()): ?>
                                <span style="color:#94a3b8;">—</span>
                            <?php endif; ?>
                        </td>

                        
                        <td>
                            <div style="display:flex;flex-wrap:wrap;gap:4px;">
                                <?php $__currentLoopData = $day->parts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $part): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <?php $pc = $partConfig[$part] ?? $partConfig['s1']; ?>
                                    <span style="display:inline-flex;align-items:center;padding:3px 9px;
                                                 border-radius:8px;font-size:11px;font-weight:800;
                                                 background:<?php echo e($pc['bg']); ?>;color:<?php echo e($pc['color']); ?>;
                                                 border:1px solid <?php echo e($pc['border']); ?>;">
                                        <?php echo e(strtoupper($part)); ?>

                                    </span>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </div>
                            <div style="font-size:9px;color:#94a3b8;margin-top:4px;">
                                <?php echo e(count($day->parts)); ?> × 2.5h
                            </div>
                        </td>

                        
                        <td>
                            <span class="hours-pill"><?php echo e($day->total_duree); ?>h</span>
                        </td>

                        
                        <td>
                            <?php if($day->is_justified): ?>
                                <span class="badge badge-justifie">✅ Justifiée(s)</span>
                            <?php elseif($day->is_pending): ?>
                                <span class="badge badge-pending">🕐 En attente</span>
                                <div style="font-size:10px;color:#92400e;margin-top:4px;">En cours d'examen</div>
                            <?php else: ?>
                                <span class="badge badge-injustifie">⚠️ Non justifiée(s)</span>
                            <?php endif; ?>
                        </td>

                        
                        <td>
                        <?php
                            $absIds       = $day->absences->pluck('id');
                            $allJustified = $day->absences->every(fn($a) => $a->justifie);
                            $anyPending   = $day->absences->contains(
                                fn($a) => !$a->justifie && !empty($a->file_justification)
                            );
                            $sharedFile   = $day->absences->first(
                                fn($a) => $a->file_justification
                            )?->file_justification;
                        ?>

                        <?php if($allJustified): ?>
                            <?php if($sharedFile): ?>
                                <a href="<?php echo e(Storage::url($sharedFile)); ?>" target="_blank"
                                   style="font-size:11px;font-weight:600;color:var(--accent);text-decoration:none;
                                          display:inline-flex;align-items:center;gap:4px;">
                                    📎 Voir le justificatif
                                </a>
                            <?php else: ?>
                                <span class="badge badge-justifie">✅ OK</span>
                            <?php endif; ?>

                        <?php elseif($anyPending): ?>
                            <div style="display:flex;flex-direction:column;gap:6px;">
                                <?php if($sharedFile): ?>
                                    <a href="<?php echo e(Storage::url($sharedFile)); ?>" target="_blank"
                                       style="font-size:11px;font-weight:600;color:#92400e;text-decoration:none;
                                              display:inline-flex;align-items:center;gap:4px;">
                                        📎 Voir le justificatif
                                    </a>
                                <?php endif; ?>
                                <form method="POST"
                                      action="<?php echo e(route('absences.stagiaire.fichier.jour.delete')); ?>"
                                      onsubmit="return confirm('Retirer le justificatif pour toute la journée ?')">
                                    <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                                    <?php $__currentLoopData = $absIds; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $id): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <input type="hidden" name="absence_ids[]" value="<?php echo e($id); ?>">
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    <button type="submit"
                                            style="font-size:10px;color:#64748b;background:none;border:none;
                                                   cursor:pointer;padding:0;display:inline-flex;align-items:center;gap:3px;">
                                        ✕ Retirer
                                    </button>
                                </form>
                            </div>

                        <?php else: ?>
                            <form method="POST"
                                  action="<?php echo e(route('absences.stagiaire.fichier.jour')); ?>"
                                  enctype="multipart/form-data">
                                <?php echo csrf_field(); ?>
                                <?php $__currentLoopData = $absIds; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $id): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <input type="hidden" name="absence_ids[]" value="<?php echo e($id); ?>">
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                <label class="btn-upload-label">
                                    <svg width="10" height="10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
                                <div style="font-size:9px;color:#94a3b8;margin-top:4px;">
                                    Valable pour les <?php echo e($absIds->count()); ?> demi-séance(s) du jour
                                </div>
                            </form>
                        <?php endif; ?>
                        </td>

                    </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tbody>
            </table>
            </div>

            
            <div style="padding:10px 20px;border-top:1px solid #f1f5f9;
                        font-size:11px;color:#94a3b8;display:flex;align-items:center;
                        justify-content:space-between;flex-wrap:wrap;gap:8px;">

                <span>
                    📅 <strong style="color:#475569;"><?php echo e($absencesByDay->total()); ?></strong> jour(s) au total
                    <?php if($stats['total_heures_abs'] > 0): ?>
                        &nbsp;·&nbsp; <strong style="color:#dc2626;"><?php echo e($stats['total_heures_abs']); ?>h</strong> cumulées
                    <?php endif; ?>
                </span>

                <span style="font-size:10px;color:#94a3b8;">
                    Page <?php echo e($absencesByDay->currentPage()); ?> / <?php echo e($absencesByDay->lastPage()); ?>

                </span>
            </div>

            <?php if($absencesByDay->hasPages()): ?>
            <div class="pagination-wrap">
                <span style="font-size:11px;color:#94a3b8;">
                    <?php echo e($absencesByDay->firstItem()); ?>–<?php echo e($absencesByDay->lastItem()); ?>

                    sur <?php echo e($absencesByDay->total()); ?> jour(s)
                </span>

                <div style="display:flex;gap:6px;flex-wrap:wrap;">

                    
                    <?php if($absencesByDay->onFirstPage()): ?>
                        <span style="padding:6px 12px;border-radius:8px;background:#f1f5f9;
                                     color:#cbd5e1;font-size:12px;font-weight:600;cursor:default;">←</span>
                    <?php else: ?>
                        <a href="<?php echo e($absencesByDay->previousPageUrl()); ?>"
                           style="padding:6px 12px;border-radius:8px;background:white;
                                  border:1.5px solid #e2e8f0;color:#475569;font-size:12px;
                                  font-weight:600;text-decoration:none;transition:all .15s;"
                           onmouseover="this.style.borderColor='var(--accent-bd)';this.style.color='var(--accent-tx)';"
                           onmouseout="this.style.borderColor='#e2e8f0';this.style.color='#475569';">←</a>
                    <?php endif; ?>

                    
                    <?php $__currentLoopData = $absencesByDay->getUrlRange(
                        max(1, $absencesByDay->currentPage() - 2),
                        min($absencesByDay->lastPage(), $absencesByDay->currentPage() + 2)
                    ); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $page => $url): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php if($page == $absencesByDay->currentPage()): ?>
                            <span style="padding:6px 12px;border-radius:8px;
                                         background:var(--accent-gr);color:white;
                                         font-size:12px;font-weight:700;border:none;"><?php echo e($page); ?></span>
                        <?php else: ?>
                            <a href="<?php echo e($url); ?>"
                               style="padding:6px 12px;border-radius:8px;background:white;
                                      border:1.5px solid #e2e8f0;color:#475569;font-size:12px;
                                      font-weight:600;text-decoration:none;transition:all .15s;"
                               onmouseover="this.style.borderColor='var(--accent-bd)';this.style.color='var(--accent-tx)';"
                               onmouseout="this.style.borderColor='#e2e8f0';this.style.color='#475569';"><?php echo e($page); ?></a>
                        <?php endif; ?>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                    
                    <?php if($absencesByDay->hasMorePages()): ?>
                        <a href="<?php echo e($absencesByDay->nextPageUrl()); ?>"
                           style="padding:6px 12px;border-radius:8px;background:white;
                                  border:1.5px solid #e2e8f0;color:#475569;font-size:12px;
                                  font-weight:600;text-decoration:none;transition:all .15s;"
                           onmouseover="this.style.borderColor='var(--accent-bd)';this.style.color='var(--accent-tx)';"
                           onmouseout="this.style.borderColor='#e2e8f0';this.style.color='#475569';">→</a>
                    <?php else: ?>
                        <span style="padding:6px 12px;border-radius:8px;background:#f1f5f9;
                                     color:#cbd5e1;font-size:12px;font-weight:600;cursor:default;">→</span>
                    <?php endif; ?>

                </div>
            </div>
            <?php endif; ?>
        <?php endif; ?>

    
    <?php else: ?>

        <?php if($absencesGrouped->isEmpty()): ?>
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
                        <th>Date</th>
                        <th>Stagiaire</th>
                        <th>Groupe</th>
                        <th>Module(s) du jour</th>
                        <th>Formateur(s)</th>
                        <th>Demi-séances</th>
                        <th>Total heures</th>
                        <th>Statut</th>
                        <th>Justificatif & Actions</th>
                    </tr>
                </thead>
                <tbody>
                <?php $__currentLoopData = $absencesGrouped; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php
                        $initials = strtoupper(
                            mb_substr($row->stagiaire?->name ?? '?', 0, 1) .
                            mb_substr(explode(' ', $row->stagiaire?->name ?? '')[1] ?? '', 0, 1)
                        );
                        $rowBg = $row->is_pending ? 'row-pending' : '';
                    ?>
                    <tr class="<?php echo e($rowBg); ?>">

                        
                        <td style="min-width:90px;">
                            <div style="font-size:20px;font-weight:900;color:#1e293b;line-height:1;">
                                <?php echo e($row->date?->format('d')); ?>

                            </div>
                            <div style="font-size:10px;color:#94a3b8;margin-top:2px;">
                                <?php echo e($row->date?->translatedFormat('M Y')); ?>

                            </div>
                            <div style="font-size:10px;color:#94a3b8;">
                                <?php echo e($row->date?->translatedFormat('l')); ?>

                            </div>
                        </td>

                        
                        <td>
                            <div style="display:flex;align-items:center;gap:8px;">
                                <div class="avatar"><?php echo e($initials); ?></div>
                                <div>
                                    <div style="font-weight:700;color:#1e293b;font-size:12px;">
                                        <?php echo e($row->stagiaire?->name ?? '—'); ?>

                                    </div>
                                    <div style="font-size:10px;color:#94a3b8;">
                                        <?php echo e($row->stagiaire?->email ?? ''); ?>

                                    </div>
                                </div>
                            </div>
                        </td>

                        
                        <td>
                            <span style="font-size:11px;font-weight:600;color:#475569;">
                                <?php echo e($row->groupe?->name ?? $row->stagiaire?->groupe?->name ?? '—'); ?>

                            </span>
                        </td>

                        
                        <td style="min-width:160px;">
                            <?php $__currentLoopData = $row->emplois; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $emp): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <div style="margin-bottom:4px;padding-bottom:4px;
                                            <?php echo e(!$loop->last ? 'border-bottom:1px dashed #f1f5f9;' : ''); ?>">
                                    <div style="font-weight:600;color:#1e293b;font-size:12px;">
                                        <?php echo e($emp->module?->name ?? '—'); ?>

                                    </div>
                                    <div style="font-size:9px;color:#94a3b8;margin-top:1px;">
                                        <?php echo e($emp->date_debut->format('H:i')); ?>–<?php echo e($emp->date_fin->format('H:i')); ?>

                                        <?php if($emp->salle): ?> · 🏫 <?php echo e($emp->salle->name); ?> <?php endif; ?>
                                    </div>
                                </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            <?php if($row->emplois->isEmpty()): ?>
                                <span style="color:#94a3b8;">—</span>
                            <?php endif; ?>
                        </td>

                        
                        <td>
                            <?php $__currentLoopData = $row->formateurs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $form): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <div style="font-size:11px;color:#475569;font-weight:500;">
                                    <?php echo e($form->name); ?>

                                </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            <?php if($row->formateurs->isEmpty()): ?>
                                <span style="color:#94a3b8;">—</span>
                            <?php endif; ?>
                        </td>

                        
                        <td>
                            <div style="display:flex;flex-wrap:wrap;gap:3px;">
                                <?php $__currentLoopData = $row->parts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $part): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <?php $pc = $partConfig[$part] ?? $partConfig['s1']; ?>
                                    <span style="display:inline-flex;align-items:center;padding:2px 8px;
                                                 border-radius:7px;font-size:10px;font-weight:800;
                                                 background:<?php echo e($pc['bg']); ?>;color:<?php echo e($pc['color']); ?>;
                                                 border:1px solid <?php echo e($pc['border']); ?>;">
                                        <?php echo e(strtoupper($part)); ?>

                                    </span>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </div>
                            <div style="font-size:9px;color:#94a3b8;margin-top:3px;">
                                <?php echo e(count($row->parts)); ?> × 2.5h
                            </div>
                        </td>

                        
                        <td>
                            <span class="hours-pill"><?php echo e($row->total_duree); ?>h</span>
                        </td>

                        
                        <td>
                            <?php if($row->is_justified): ?>
                                <span class="badge badge-justifie">✅ Justifiée(s)</span>
                            <?php elseif($row->is_pending): ?>
                                <span class="badge badge-pending">🕐 En attente</span>
                                <?php if(!$canJustify): ?>
                                    <div style="font-size:10px;color:#92400e;margin-top:4px;">En cours d'examen</div>
                                <?php endif; ?>
                            <?php else: ?>
                                <span class="badge badge-injustifie">⚠️ Non justifiée(s)</span>
                            <?php endif; ?>
                        </td>

                        
                        <td style="min-width:260px;">
                            <?php
                                $allRowAbsIds  = $row->absences->pluck('id');
                                $allRowJust    = $row->absences->every(fn($a) => $a->justifie);
                                $anyRowPending = $row->absences->contains(
                                    fn($a) => !$a->justifie && !empty($a->file_justification)
                                );
                                $rowSharedFile = $row->absences->first(fn($a) => $a->file_justification)?->file_justification;
                            ?>

                            
                            <?php if($row->is_admin_validated): ?>
                                <div style="display:flex;flex-direction:column;gap:6px;">
                                    <span class="badge-admin-allowed">✔ Autorisé sans justificatif</span>
                                    <form method="POST" action="<?php echo e(route('absences.admin.annuler')); ?>" style="display:inline;">
                                        <?php echo csrf_field(); ?>
                                        <?php $__currentLoopData = $allRowAbsIds; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $id): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <input type="hidden" name="absence_ids[]" value="<?php echo e($id); ?>">
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        <button type="submit" class="btn-admin-revert" title="Rétablir le signalement formateur">
                                            ↩ Annuler l'autorisation
                                        </button>
                                    </form>
                                </div>

                            
                            <?php elseif($allRowJust): ?>
                                <?php if($rowSharedFile): ?>
                                    <a href="<?php echo e(Storage::url($rowSharedFile)); ?>" target="_blank"
                                       style="font-size:11px;font-weight:700;color:var(--accent);
                                              text-decoration:none;display:inline-flex;align-items:center;gap:4px;margin-bottom:8px;">
                                        📎 Voir le justificatif
                                    </a><br>
                                <?php else: ?>
                                    <span class="badge badge-justifie" style="margin-bottom:8px;display:inline-flex;">✅ Toutes justifiées</span><br>
                                <?php endif; ?>

                                <?php if($canJustify): ?>
                                <form method="POST" action="<?php echo e(route('absences.admin.bulk.unjustify')); ?>"
                                      onsubmit="return confirm('Annuler la justification pour toutes les demi-séances ?')">
                                    <?php echo csrf_field(); ?>
                                    <?php $__currentLoopData = $allRowAbsIds; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $id): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <input type="hidden" name="absence_ids[]" value="<?php echo e($id); ?>">
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    <button type="submit" class="btn-toggle">↩ Annuler toutes</button>
                                </form>
                                <?php endif; ?>

                            
                            <?php elseif($anyRowPending): ?>
                                <?php if($rowSharedFile): ?>
                                    <a href="<?php echo e(Storage::url($rowSharedFile)); ?>" target="_blank"
                                       style="font-size:11px;font-weight:700;color:#92400e;
                                              text-decoration:none;display:inline-flex;align-items:center;gap:4px;margin-bottom:6px;">
                                        📎 Voir le justificatif
                                    </a><br>
                                <?php endif; ?>
                                <?php if($canJustify): ?>
                                <div style="display:flex;flex-direction:column;gap:4px;margin-top:4px;">
                                    <?php $__currentLoopData = $row->absences->where('justifie', false)->filter(fn($a) => !empty($a->file_justification)); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $abs): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <?php $pc = $partConfig[$abs->session_part ?? 's1'] ?? $partConfig['s1']; ?>
                                        <div style="display:flex;align-items:center;gap:5px;flex-wrap:wrap;">
                                            <span style="font-size:9px;font-weight:800;padding:1px 6px;border-radius:5px;
                                                         background:<?php echo e($pc['bg']); ?>;color:<?php echo e($pc['color']); ?>;
                                                         border:1px solid <?php echo e($pc['border']); ?>;">
                                                <?php echo e(strtoupper($abs->session_part)); ?>

                                            </span>
                                            <form method="POST" action="<?php echo e(route('absences.accept', $abs)); ?>" style="display:inline;">
                                                <?php echo csrf_field(); ?> <?php echo method_field('PATCH'); ?>
                                                <button type="submit" class="btn-accept" style="font-size:9px;padding:2px 7px;">
                                                    ✓ Accepter
                                                </button>
                                            </form>
                                            <form method="POST" action="<?php echo e(route('absences.reject', $abs)); ?>"
                                                  onsubmit="return confirm('Rejeter ce justificatif ?')" style="display:inline;">
                                                <?php echo csrf_field(); ?> <?php echo method_field('PATCH'); ?>
                                                <button type="submit" class="btn-reject" style="font-size:9px;padding:2px 7px;">
                                                    ✕ Rejeter
                                                </button>
                                            </form>
                                        </div>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </div>
                                <?php else: ?>
                                    <div style="font-size:10px;color:#92400e;margin-top:4px;">En cours d'examen</div>
                                <?php endif; ?>

                            
                            <?php else: ?>
                                <?php if($canJustify): ?>
                                    
                                    <form method="POST"
                                          action="<?php echo e(route('absences.admin.fichier.jour')); ?>"
                                          enctype="multipart/form-data"
                                          style="margin-bottom:8px;">
                                        <?php echo csrf_field(); ?>
                                        <?php $__currentLoopData = $allRowAbsIds; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $id): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <input type="hidden" name="absence_ids[]" value="<?php echo e($id); ?>">
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        <label class="btn-upload-label">
                                            <svg width="10" height="10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                      d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                                            </svg>
                                            📎 Joindre un justificatif
                                            <input type="file" name="file_justification"
                                                   accept=".pdf,.doc,.docx,.jpg,.jpeg,.png"
                                                   style="display:none;"
                                                   onchange="this.closest('form').submit()">
                                        </label>
                                        <div style="font-size:9px;color:#94a3b8;margin-top:2px;margin-bottom:8px;">
                                            Couvre les <?php echo e($allRowAbsIds->count()); ?> demi-séance(s) du jour
                                        </div>
                                    </form>

                                    
                                    <form method="POST" action="<?php echo e(route('absences.admin.valider')); ?>"
                                          style="margin-bottom:8px;"
                                          onsubmit="return confirm('⚠️ Autoriser cette absence sans justificatif ?\n\nLe signalement formateur sera supprimé mais l\'absence restera non-justifiée.')">
                                        <?php echo csrf_field(); ?>
                                        <?php $__currentLoopData = $allRowAbsIds; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $id): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <input type="hidden" name="absence_ids[]" value="<?php echo e($id); ?>">
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        <button type="submit" class="btn-admin-allow"
                                                title="L'absence reste non-justifiée mais le signalement formateur disparaît">
                                            🔓 Autoriser sans justificatif
                                        </button>
                                    </form>

                                    
                                    <form method="POST" action="<?php echo e(route('absences.admin.bulk.justify')); ?>" style="margin-top:6px;">
                                        <?php echo csrf_field(); ?>
                                        <?php $__currentLoopData = $allRowAbsIds; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $id): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <input type="hidden" name="absence_ids[]" value="<?php echo e($id); ?>">
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        <button type="submit" class="btn-accept">✓ Justifier toutes</button>
                                    </form>
                                <?php else: ?>
                                    <span style="font-size:10px;color:#94a3b8;">—</span>
                                <?php endif; ?>
                            <?php endif; ?>
                        </td>
                        

                    </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tbody>
            </table>
            </div>

            
            <?php if($absencesGrouped->hasPages()): ?>
            <div class="pagination-wrap">
                <span style="font-size:11px;color:#94a3b8;">
                    <?php echo e($absencesGrouped->firstItem()); ?>–<?php echo e($absencesGrouped->lastItem()); ?>

                    sur <?php echo e($absencesGrouped->total()); ?> jour(s)/stagiaire(s)
                </span>
                <div style="display:flex;gap:6px;">
                    <?php if($absencesGrouped->onFirstPage()): ?>
                        <span style="padding:6px 12px;border-radius:8px;background:#f1f5f9;color:#cbd5e1;font-size:12px;font-weight:600;">←</span>
                    <?php else: ?>
                        <a href="<?php echo e($absencesGrouped->previousPageUrl()); ?>"
                           style="padding:6px 12px;border-radius:8px;background:white;border:1.5px solid #e2e8f0;color:#475569;font-size:12px;font-weight:600;text-decoration:none;">←</a>
                    <?php endif; ?>

                    <?php $__currentLoopData = $absencesGrouped->getUrlRange(
                        max(1,$absencesGrouped->currentPage()-2),
                        min($absencesGrouped->lastPage(),$absencesGrouped->currentPage()+2)
                    ); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $page => $url): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php if($page == $absencesGrouped->currentPage()): ?>
                            <span style="padding:6px 12px;border-radius:8px;background:var(--accent-gr);color:white;font-size:12px;font-weight:700;"><?php echo e($page); ?></span>
                        <?php else: ?>
                            <a href="<?php echo e($url); ?>"
                               style="padding:6px 12px;border-radius:8px;background:white;border:1.5px solid #e2e8f0;color:#475569;font-size:12px;font-weight:600;text-decoration:none;"><?php echo e($page); ?></a>
                        <?php endif; ?>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                    <?php if($absencesGrouped->hasMorePages()): ?>
                        <a href="<?php echo e($absencesGrouped->nextPageUrl()); ?>"
                           style="padding:6px 12px;border-radius:8px;background:white;border:1.5px solid #e2e8f0;color:#475569;font-size:12px;font-weight:600;text-decoration:none;">→</a>
                    <?php else: ?>
                        <span style="padding:6px 12px;border-radius:8px;background:#f1f5f9;color:#cbd5e1;font-size:12px;font-weight:600;">→</span>
                    <?php endif; ?>
                </div>
            </div>
            <?php endif; ?>
        <?php endif; ?>

    <?php endif; ?> 
</div>

</div>


<style>
/* Loading overlay */
.abs-loading-overlay {
    position:fixed; inset:0; z-index:9999;
    background:rgba(255,255,255,0.55);
    backdrop-filter:blur(2px);
    display:none; align-items:center; justify-content:center;
    transition:opacity .2s;
}
.abs-loading-overlay.active { display:flex; }
.abs-spinner {
    width:44px; height:44px; border-radius:50%;
    border:4px solid var(--accent-bd);
    border-top-color:var(--accent);
    animation:abs-spin .7s linear infinite;
}
@keyframes abs-spin { to { transform:rotate(360deg); } }

/* Fade-swap animation */
.abs-swap-out { opacity:0; transform:translateY(4px); transition:all .18s ease; }
.abs-swap-in  { animation:abs-fade-in .25s ease forwards; }
@keyframes abs-fade-in {
    from { opacity:0; transform:translateY(4px); }
    to   { opacity:1; transform:translateY(0); }
}
</style>

<div class="abs-loading-overlay" id="abs-loading-overlay">
    <div style="background:white;border-radius:16px;padding:24px 32px;
                box-shadow:0 20px 60px rgba(0,0,0,0.12);
                display:flex;align-items:center;gap:14px;">
        <div class="abs-spinner"></div>
        <span style="font-size:13px;font-weight:700;color:#475569;">Chargement…</span>
    </div>
</div>

<script>
(function () {
    // ── IDs of sections we swap on every filter/nav request ──
    const SWAP_IDS = [
        'abs-stats-grid',
        'abs-day-panel-wrap',
        'abs-table-wrap',
    ];

    // ── tiny debounce ──
    let _timer = null;
    function debounce(fn, ms) {
        clearTimeout(_timer);
        _timer = setTimeout(fn, ms);
    }

    // ── show / hide loading overlay ──
    const overlay = document.getElementById('abs-loading-overlay');
    function showLoading()  { overlay.classList.add('active'); }
    function hideLoading()  { overlay.classList.remove('active'); }

    // ── core fetch + swap ──
    function absAjax(url) {
        showLoading();

        // Fade out swappable zones
        SWAP_IDS.forEach(id => {
            const el = document.getElementById(id);
            if (el) el.classList.add('abs-swap-out');
        });

        fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
            .then(r => r.text())
            .then(html => {
                const parser  = new DOMParser();
                const newDoc  = parser.parseFromString(html, 'text/html');

                // Swap each section
                SWAP_IDS.forEach(id => {
                    const current = document.getElementById(id);
                    const fresh   = newDoc.getElementById(id);
                    if (current && fresh) {
                        current.classList.remove('abs-swap-out');
                        current.outerHTML = fresh.outerHTML;
                        // Re-find (outerHTML replaces the node)
                        const replaced = document.getElementById(id);
                        if (replaced) replaced.classList.add('abs-swap-in');
                    }
                });

                // Update filter form selects to reflect new state
                const newForm = newDoc.getElementById('abs-filter-form');
                const curForm = document.getElementById('abs-filter-form');
                if (newForm && curForm) {
                    newForm.querySelectorAll('select').forEach(newSel => {
                        const curSel = curForm.querySelector(`[name="${newSel.name}"]`);
                        if (curSel) curSel.value = newSel.value;
                    });
                }

                // Update reset button visibility
                const newReset = newDoc.getElementById('abs-reset-btn');
                const curReset = document.getElementById('abs-reset-btn');
                if (curReset && newReset) {
                    curReset.href  = newReset.href;
                    curReset.style.display = newReset.style.display;
                }

                // Update hidden day input if present
                const newDayHid = newDoc.getElementById('abs-day-hidden');
                const curDayHid = document.getElementById('abs-day-hidden');
                if (curDayHid && newDayHid) {
                    curDayHid.value = newDayHid.value;
                } else if (!curDayHid && newDayHid) {
                    // insert it
                    const f = document.getElementById('abs-filter-form');
                    if (f) { const inp = document.createElement('input'); inp.type='hidden'; inp.name='day'; inp.id='abs-day-hidden'; inp.value=newDayHid.value; f.prepend(inp); }
                } else if (curDayHid && !newDayHid) {
                    curDayHid.remove();
                }

                // Update browser URL without reload
                window.history.pushState({ absUrl: url }, '', url);
            })
            .catch(() => {
                // Fallback: normal reload on error
                window.location.href = url;
            })
            .finally(() => {
                hideLoading();
                // Re-bind events on new DOM nodes
                bindDayNav();
                bindPagination();
            });
    }

    // ── Build URL from filter form ──
    function filterUrl() {
        const form   = document.getElementById('abs-filter-form');
        if (!form) return window.location.href;
        const data   = new FormData(form);
        const params = new URLSearchParams();
        for (const [k, v] of data.entries()) {
            if (v !== '') params.set(k, v);
        }
        return form.action + (params.toString() ? '?' + params.toString() : '');
    }

    // ── Filter form: intercept submit + auto-change on selects ──
    document.addEventListener('submit', function (e) {
        const form = e.target.closest('#abs-filter-form');
        if (!form) return;
        e.preventDefault();
        absAjax(filterUrl());
    });

    document.addEventListener('change', function (e) {
        if (!e.target.closest('#abs-filter-form')) return;
        if (!e.target.classList.contains('abs-auto-filter')) return;
        debounce(() => absAjax(filterUrl()), 120);
    });

    // ── Day nav links + pills: intercept clicks ──
    function bindDayNav() {
        document.querySelectorAll('.day-nav-btn[href], .day-pill[href]').forEach(el => {
            if (el.dataset.ajaxBound) return;
            el.dataset.ajaxBound = '1';
            el.addEventListener('click', function (e) {
                e.preventDefault();
                absAjax(this.href);
            });
        });

        // Day date <input type="date"> form inside day nav
        document.querySelectorAll('.day-date-input').forEach(inp => {
            if (inp.dataset.ajaxBound) return;
            inp.dataset.ajaxBound = '1';
            inp.addEventListener('change', function () {
                const form   = this.closest('form');
                if (!form) return;
                const params = new URLSearchParams(new FormData(form));
                // Remove empty values
                const clean  = new URLSearchParams();
                for (const [k,v] of params) if (v) clean.set(k,v);
                absAjax(form.action + '?' + clean.toString());
            });
        });
    }

    // ── Pagination links: intercept clicks ──
    function bindPagination() {
        document.querySelectorAll('.pagination-wrap a').forEach(el => {
            if (el.dataset.ajaxBound) return;
            el.dataset.ajaxBound = '1';
            el.addEventListener('click', function (e) {
                e.preventDefault();
                absAjax(this.href);
            });
        });
    }

    // ── Handle browser back/forward ──
    window.addEventListener('popstate', function (e) {
        absAjax(window.location.href);
    });

    // ── Reset button: intercept ──
    document.addEventListener('click', function (e) {
        const btn = e.target.closest('#abs-reset-btn');
        if (!btn) return;
        e.preventDefault();
        absAjax(btn.href);
    });

    // ── Initial bind ──
    bindDayNav();
    bindPagination();
})();
</script>

<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\Project\gestion-CF\resources\views/absences/index.blade.php ENDPATH**/ ?>