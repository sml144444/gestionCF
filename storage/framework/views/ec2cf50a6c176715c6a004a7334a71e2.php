

<?php $__env->startSection('title', 'Reportations'); ?>
<?php $__env->startSection('page-title', 'Reportations'); ?>

<?php $__env->startSection('content'); ?>
<?php
    $user    = Auth::user();
    $isAdmin = $user->role === 'admin';
    $accent  = $isAdmin ? '#0a6640' : '#1e293b';
    $light   = $isAdmin ? '#e8f5ee' : '#f1f5f9';
    $text    = $isAdmin ? '#065f38' : '#1e293b';
?>

<style>
.rp-wrap { font-family:'Segoe UI',system-ui,sans-serif; }
.rp-card { background:white; border-radius:16px; border:1px solid #e2e8f0; box-shadow:0 2px 10px rgba(0,0,0,0.05); overflow:hidden; margin-bottom:14px; transition:box-shadow .2s; }
.rp-card:hover { box-shadow:0 4px 20px rgba(0,0,0,0.08); }
.rp-input { height:40px; padding:0 12px; border-radius:10px; border:1.5px solid #e2e8f0; background:#f8fafc; font-size:13px; color:#1e293b; outline:none; transition:all .15s; box-sizing:border-box; }
.rp-input:focus { border-color:<?php echo e($accent); ?>; background:white; }
.status-pill { display:inline-flex; align-items:center; gap:4px; padding:4px 12px; border-radius:99px; font-size:10px; font-weight:700; }
.status-pill.attente { background:#fff7ed; color:#92400e; border:1px solid #fde68a; }
.status-pill.valide  { background:#f0fdf4; color:#15803d; border:1px solid #bbf7d0; }
.status-pill.refuse  { background:#fff1f2; color:#dc2626; border:1px solid #fecdd3; }
.rp-btn { height:36px; padding:0 14px; border-radius:9px; font-size:12px; font-weight:700; border:none; cursor:pointer; display:inline-flex; align-items:center; gap:5px; transition:opacity .15s; text-decoration:none; }
.rp-btn:hover { opacity:.85; }
.rp-btn.green  { background:#16a34a; color:white; }
.rp-btn.red    { background:#dc2626; color:white; }
.rp-btn.orange { background:#f59e0b; color:white; }
.rp-btn.ghost  { background:#f1f5f9; color:#475569; border:1px solid #e2e8f0; }
.tab-pill { padding:8px 16px; border-radius:99px; font-size:12px; font-weight:600; text-decoration:none; border:1.5px solid #e2e8f0; background:white; color:#64748b; transition:all .15s; display:inline-flex; align-items:center; gap:6px; }
.tab-pill:hover { border-color:<?php echo e($accent); ?>; color:<?php echo e($text); ?>; background:<?php echo e($light); ?>; }
.tab-pill.active { background:<?php echo e($accent); ?>; border-color:<?php echo e($accent); ?>; color:white; }
.tab-pill .badge { font-size:9px; padding:1px 7px; border-radius:99px; font-weight:800; }
.tab-pill.active .badge { background:rgba(255,255,255,0.25); color:white; }
.tab-pill:not(.active) .badge { background:<?php echo e($light); ?>; color:<?php echo e($text); ?>; }
.rp-modal-overlay { position:fixed; inset:0; z-index:60; background:rgba(15,23,42,0.5); backdrop-filter:blur(4px); display:none; align-items:center; justify-content:center; }
.rp-modal-overlay.open { display:flex; }
.rp-modal-box { background:white; border-radius:20px; width:100%; max-width:460px; margin:16px; padding:24px; box-shadow:0 24px 60px rgba(0,0,0,0.18); }
.rp-modal-input { width:100%; height:42px; padding:0 12px; border-radius:10px; border:1.5px solid #e2e8f0; background:#f8fafc; font-size:13px; color:#1e293b; outline:none; transition:all .15s; box-sizing:border-box; }
.rp-modal-input:focus { border-color:#16a34a; background:white; }
.rp-label { display:block; font-size:9px; font-weight:800; color:#94a3b8; letter-spacing:1.5px; text-transform:uppercase; margin-bottom:6px; }
@keyframes slideIn { from { opacity:0; transform:translateY(-12px); } to { opacity:1; transform:translateY(0); } }
</style>

<div class="rp-wrap">


<?php if(session('success')): ?>
    <div style="margin-bottom:16px;padding:12px 16px;border-radius:12px;font-size:13px;display:flex;align-items:center;gap:8px;background:#f0fdf4;border:1px solid #bbf7d0;color:#15803d;">
        ✓ <?php echo e(session('success')); ?>

    </div>
<?php endif; ?>
<?php if(session('error')): ?>
    <div style="margin-bottom:16px;padding:12px 16px;border-radius:12px;font-size:13px;background:#fff1f2;border:1px solid #fecdd3;color:#dc2626;">
        ✕ <?php echo e(session('error')); ?>

    </div>
<?php endif; ?>
<?php if($errors->any()): ?>
    <div style="margin-bottom:16px;padding:12px 16px;border-radius:12px;font-size:13px;background:#fff1f2;border:1px solid #fecdd3;color:#dc2626;">
        <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $e): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><p style="margin:2px 0;">✕ <?php echo e($e); ?></p><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>
<?php endif; ?>


<div style="display:flex;align-items:flex-start;justify-content:space-between;flex-wrap:wrap;gap:12px;margin-bottom:20px;">
    <div>
        <h1 style="font-size:20px;font-weight:800;color:#0f172a;margin:0;">Reportations</h1>
        <p style="font-size:12px;color:#64748b;margin:4px 0 0;">Demandes de report reçues des formateurs — vous choisissez la nouvelle date</p>
    </div>
    <div style="display:flex;gap:10px;flex-wrap:wrap;">
        <div style="padding:10px 16px;border-radius:12px;text-align:center;background:#fff7ed;border:1px solid #fde68a;">
            <div data-count="en_attente" style="font-size:22px;font-weight:800;color:#92400e;"><?php echo e($counts['en_attente']); ?></div>
            <div style="font-size:9px;font-weight:700;color:#92400e;text-transform:uppercase;letter-spacing:.5px;">En attente</div>
        </div>
        <div style="padding:10px 16px;border-radius:12px;text-align:center;background:#f0fdf4;border:1px solid #bbf7d0;">
            <div data-count="valide" style="font-size:22px;font-weight:800;color:#15803d;"><?php echo e($counts['valide']); ?></div>
            <div style="font-size:9px;font-weight:700;color:#15803d;text-transform:uppercase;letter-spacing:.5px;">Acceptées</div>
        </div>
        <div style="padding:10px 16px;border-radius:12px;text-align:center;background:#fff1f2;border:1px solid #fecdd3;">
            <div data-count="refuse" style="font-size:22px;font-weight:800;color:#dc2626;"><?php echo e($counts['refuse']); ?></div>
            <div style="font-size:9px;font-weight:700;color:#dc2626;text-transform:uppercase;letter-spacing:.5px;">Refusées</div>
        </div>
    </div>
</div>


<div style="display:flex;gap:8px;flex-wrap:wrap;margin-bottom:18px;align-items:center;">
    <?php $__currentLoopData = [['en_attente','⏳','En attente'],['valide','✓','Acceptées'],['refuse','✕','Refusées'],['','📋','Toutes']]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as [$val,$icon,$label]): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
    <a href="<?php echo e(route('reportations.index', array_merge(request()->except('status','page'), ['status'=>$val]))); ?>"
       class="tab-pill <?php echo e($status === $val ? 'active' : ''); ?>">
        <?php echo e($icon); ?> <?php echo e($label); ?>

        <span class="badge" <?php if($val !== ''): ?> data-tab="<?php echo e($val); ?>" <?php endif; ?>>
            <?php echo e($val === '' ? array_sum($counts) : ($counts[$val] ?? 0)); ?>

        </span>
    </a>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

    <form method="GET" action="<?php echo e(route('reportations.index')); ?>" style="margin-left:auto;display:flex;gap:8px;">
        <input type="hidden" name="status" value="<?php echo e($status); ?>">
        <input type="text" name="search" value="<?php echo e($search); ?>" placeholder="Rechercher formateur…" class="rp-input" style="width:200px;">
        <button type="submit" style="height:40px;padding:0 14px;border-radius:10px;border:none;background:<?php echo e($accent); ?>;color:white;font-size:13px;font-weight:600;cursor:pointer;">🔍</button>
    </form>
</div>


<?php $__empty_1 = true; $__currentLoopData = $reportations; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $rp): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
<?php $emploi = $rp->emploiDuTemps; ?>

<div class="rp-card" data-rp-id="<?php echo e($rp->id); ?>">
    
    <div style="padding:14px 20px;border-bottom:1px solid #f1f5f9;display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:10px;">
        <div style="display:flex;align-items:center;gap:12px;">
            <?php
                $name     = $rp->formateur?->name ?? 'Inconnu';
                $initials = strtoupper(substr($name,0,1)) . strtoupper(substr(explode(' ',$name.' ')[1]??'',0,1));
            ?>
            <div style="width:38px;height:38px;border-radius:10px;background:<?php echo e($light); ?>;border:1px solid <?php echo e($accent); ?>30;display:flex;align-items:center;justify-content:center;font-size:12px;font-weight:800;color:<?php echo e($text); ?>;flex-shrink:0;">
                <?php echo e($initials); ?>

            </div>
            <div>
                <div style="font-size:13px;font-weight:700;color:#0f172a;"><?php echo e($name); ?></div>
                <div style="font-size:10px;color:#64748b;"><?php echo e($rp->created_at->translatedFormat('l d M Y à H:i')); ?></div>
            </div>
        </div>
        <div style="display:flex;align-items:center;gap:8px;flex-wrap:wrap;">
            <?php if($rp->status === 'en_attente'): ?>
                <span class="status-pill attente">⏳ En attente de décision</span>
            <?php elseif($rp->status === 'valide'): ?>
                <span class="status-pill valide">✓ Acceptée</span>
            <?php else: ?>
                <span class="status-pill refuse">✕ Refusée</span>
            <?php endif; ?>
            <?php if($rp->validePar): ?>
                <span style="font-size:10px;color:#64748b;">par <strong><?php echo e($rp->validePar->name); ?></strong></span>
            <?php endif; ?>
        </div>
    </div>

    
    <div style="padding:16px 20px;display:grid;grid-template-columns:1fr 1fr;gap:16px;">

        
        <div>
            <div style="font-size:9px;font-weight:800;color:#94a3b8;text-transform:uppercase;letter-spacing:1.5px;margin-bottom:8px;">Séance concernée</div>
            <?php if($emploi): ?>
            <div style="padding:12px 14px;border-radius:10px;background:#f8fafc;border:1px solid #e2e8f0;">
                <div style="font-size:12px;font-weight:700;color:#1e293b;margin-bottom:5px;">
                    <?php echo e($emploi->module?->name ?? '— Module non défini'); ?>

                </div>
                <div style="font-size:10px;color:#475569;margin-bottom:3px;display:flex;align-items:center;gap:5px;">
                    👥 <?php echo e($emploi->groupe?->name ?? '—'); ?> · <?php echo e($emploi->groupe?->filiere?->name ?? ''); ?>

                </div>
                <div style="font-size:10px;color:#475569;margin-bottom:3px;display:flex;align-items:center;gap:5px;">
                    📅 <?php echo e($emploi->date_debut->translatedFormat('l d M Y')); ?>

                </div>
                <div style="font-size:10px;color:#475569;display:flex;align-items:center;gap:5px;">
                    🕐 <?php echo e($emploi->date_debut->format('H:i')); ?> → <?php echo e($emploi->date_fin->format('H:i')); ?>

                    <?php if($emploi->salle): ?>
                        · 🏛 <?php echo e($emploi->salle->name); ?>

                    <?php elseif($emploi->mode === 'distance'): ?>
                        · 📹 À distance
                    <?php endif; ?>
                </div>
            </div>
            <?php else: ?>
                <div style="font-size:11px;color:#94a3b8;font-style:italic;">Séance supprimée</div>
            <?php endif; ?>
        </div>

        
        <div>
            <div style="font-size:9px;font-weight:800;color:#94a3b8;text-transform:uppercase;letter-spacing:1.5px;margin-bottom:8px;">Raison du formateur</div>
            <div style="padding:12px 14px;border-radius:10px;background:#f8fafc;border:1px solid #e2e8f0;border-left:3px solid #7c3aed;font-size:11px;color:#334155;line-height:1.6;">
                <?php echo e($rp->raison); ?>

            </div>

            <?php if($rp->status === 'valide' && $rp->nouvelle_date_debut): ?>
            <div style="margin-top:10px;padding:10px 14px;border-radius:10px;background:#f0fdf4;border:1px solid #bbf7d0;">
                <div style="font-size:9px;font-weight:800;color:#15803d;text-transform:uppercase;letter-spacing:1px;margin-bottom:4px;">Déplacée au</div>
                <div style="font-size:12px;font-weight:700;color:#15803d;">
                    <?php echo e(Carbon\Carbon::parse($rp->nouvelle_date_debut)->translatedFormat('l d M Y')); ?>

                </div>
                <div style="font-size:10px;color:#15803d;">
                    <?php echo e(Carbon\Carbon::parse($rp->nouvelle_date_debut)->format('H:i')); ?>

                    → <?php echo e(Carbon\Carbon::parse($rp->nouvelle_date_fin)->format('H:i')); ?>

                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>

    
    <div style="padding:12px 20px;border-top:1px solid #f1f5f9;background:#fafafa;">
        <div style="display:flex;align-items:center;gap:12px;flex-wrap:wrap;">
            
            <form method="POST" action="<?php echo e(route('reportations.assign', $rp)); ?>" style="display:flex;gap:6px;align-items:center;">
                <?php echo csrf_field(); ?>
                <?php $gestionnaires = \App\Models\User::role('gestionnaire')->get(); ?>
                <select name="assigned_to" class="rp-input" style="width:200px;">
                    <option value="">— Assigner un gestionnaire —</option>
                    <?php $__currentLoopData = $gestionnaires; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $g): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($g->id); ?>" <?php echo e($rp->assigned_to == $g->id ? 'selected' : ''); ?>><?php echo e($g->name); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
                <button type="submit" class="rp-btn ghost">✔ Assigner</button>
            </form>

            
            <button class="rp-btn ghost"
                    onclick="openChat(<?php echo e($rp->id); ?>, '<?php echo e(addslashes($rp->formateur?->name ?? 'Conversation')); ?>')">
                💬 Chat
                <span id="chat-count-<?php echo e($rp->id); ?>" style="background:#e2e8f0;border-radius:99px;padding:1px 7px;font-size:10px;">
                    <?php echo e($rp->messages?->count() ?? 0); ?>

                </span>
            </button>
        </div>
    </div>

    
    <?php if($rp->status === 'en_attente' && $emploi): ?>
    <div style="padding:14px 20px;border-top:1px solid #f1f5f9;display:flex;align-items:center;gap:10px;flex-wrap:wrap;background:#fafafa;">
        <span style="font-size:11px;color:#64748b;flex:1;min-width:160px;">
            Choisissez la nouvelle date ou refusez :
        </span>

        <button class="rp-btn green"
                onclick="openAcceptModal(
                    <?php echo e($rp->id); ?>,
                    '<?php echo e(addslashes($rp->formateur?->name ?? '')); ?>',
                    '<?php echo e(addslashes($emploi->module?->name ?? 'Module')); ?>',
                    '<?php echo e($emploi->date_debut->format('Y-m-d')); ?>',
                    '<?php echo e($emploi->date_debut->format('H:i')); ?>',
                    '<?php echo e($emploi->date_fin->format('H:i')); ?>'
                )">
            <svg width="12" height="12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
            Accepter & Choisir la date
        </button>

        <form method="POST" action="<?php echo e(route('reportations.delete-session', $rp)); ?>"
              onsubmit="return confirm('Supprimer définitivement la séance ?')">
            <?php echo csrf_field(); ?>
            <button type="submit" class="rp-btn orange">
                <svg width="12" height="12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                Supprimer la séance
            </button>
        </form>

        <form method="POST" action="<?php echo e(route('reportations.refuse', $rp)); ?>"
              onsubmit="return confirm('Refuser cette demande ?')">
            <?php echo csrf_field(); ?>
            <button type="submit" class="rp-btn red">
                <svg width="12" height="12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
                Refuser
            </button>
        </form>

        <a href="<?php echo e(route('emplois.index', ['week' => $emploi->date_debut->toDateString(), 'year' => $emploi->groupe->annee ?? 1])); ?>"
           class="rp-btn ghost">
            📅 Voir la semaine
        </a>
    </div>
    <?php endif; ?>
</div>
<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
<div style="padding:64px;text-align:center;background:white;border-radius:16px;border:1px solid #e2e8f0;">
    <div style="font-size:36px;margin-bottom:12px;">📋</div>
    <p style="font-size:14px;font-weight:700;color:#334155;margin:0 0 4px;">Aucune demande</p>
    <p style="font-size:12px;color:#94a3b8;margin:0;">Les demandes des formateurs apparaîtront ici.</p>
</div>
<?php endif; ?>

<?php if($reportations->hasPages()): ?>
    <div style="margin-top:16px;display:flex;justify-content:center;"><?php echo e($reportations->links()); ?></div>
<?php endif; ?>


<div id="accept-modal" class="rp-modal-overlay" onclick="if(event.target===this)closeAcceptModal()">
    <div class="rp-modal-box">
        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:16px;padding-bottom:14px;border-bottom:2px solid #16a34a;">
            <div style="display:flex;align-items:center;gap:10px;">
                <div style="width:42px;height:42px;border-radius:12px;background:#f0fdf4;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                    <svg width="20" height="20" fill="none" stroke="#16a34a" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                </div>
                <div>
                    <div style="font-size:14px;font-weight:800;color:#1e293b;">Choisir la nouvelle date</div>
                    <div id="accept-session-label" style="font-size:10px;color:#64748b;margin-top:1px;"></div>
                </div>
            </div>
            <button onclick="closeAcceptModal()" style="width:28px;height:28px;border-radius:8px;border:none;background:#f1f5f9;color:#64748b;font-size:16px;cursor:pointer;display:flex;align-items:center;justify-content:center;">×</button>
        </div>
        <div id="accept-current-info" style="padding:10px 14px;border-radius:10px;background:#fff7ed;border:1px solid #fde68a;margin-bottom:16px;font-size:11px;color:#92400e;display:flex;align-items:center;gap:8px;">
            <svg width="13" height="13" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
            <span>Séance actuelle : <strong id="accept-current-date"></strong></span>
        </div>
        <form id="accept-form" method="POST" style="display:flex;flex-direction:column;gap:14px;">
            <?php echo csrf_field(); ?>
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
                <div>
                    <label class="rp-label">Nouvelle date et heure de début</label>
                    <input type="datetime-local" name="nouvelle_date_debut" id="accept-debut" required class="rp-modal-input">
                </div>
                <div>
                    <label class="rp-label">Heure de fin</label>
                    <input type="datetime-local" name="nouvelle_date_fin" id="accept-fin" required class="rp-modal-input">
                </div>
            </div>
            <div style="padding:10px 14px;border-radius:10px;background:#f0fdf4;border:1px solid #bbf7d0;font-size:11px;color:#15803d;display:flex;align-items:flex-start;gap:8px;">
                <svg width="13" height="13" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="flex-shrink:0;margin-top:1px;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                Le système vérifiera automatiquement les conflits de groupe, formateur et salle sur ce créneau.
            </div>
            <div style="display:flex;gap:10px;margin-top:4px;">
                <button type="button" onclick="closeAcceptModal()"
                        style="flex:1;height:44px;border-radius:12px;border:1.5px solid #e2e8f0;background:white;font-size:13px;font-weight:600;color:#64748b;cursor:pointer;">
                    Annuler
                </button>
                <button type="submit"
                        style="flex:2;height:44px;border-radius:12px;border:none;background:#16a34a;font-size:13px;font-weight:700;color:white;cursor:pointer;display:flex;align-items:center;justify-content:center;gap:6px;">
                    <svg width="13" height="13" fill="none" stroke="white" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                    Confirmer le déplacement
                </button>
            </div>
        </form>
    </div>
</div>


<div id="chat-modal" style="position:fixed;inset:0;z-index:70;background:rgba(15,23,42,0.5);backdrop-filter:blur(4px);display:none;align-items:center;justify-content:center;" onclick="if(event.target===this)closeChat()">
    <div style="background:white;border-radius:20px;width:100%;max-width:480px;margin:16px;display:flex;flex-direction:column;height:520px;box-shadow:0 24px 60px rgba(0,0,0,0.18);">
        <div style="padding:16px 20px;border-bottom:1px solid #e2e8f0;display:flex;align-items:center;justify-content:space-between;">
            <div style="font-size:14px;font-weight:800;color:#1e293b;" id="chat-title">💬 Conversation</div>
            <button onclick="closeChat()" style="border:none;background:#f1f5f9;border-radius:8px;width:28px;height:28px;cursor:pointer;font-size:16px;">×</button>
        </div>

        <div id="chat-messages" style="flex:1;overflow-y:auto;padding:16px;display:flex;flex-direction:column;gap:10px;"></div>

        
        <div id="file-preview-bar" style="display:none;padding:8px 16px;border-top:1px solid #e2e8f0;background:#f8fafc;align-items:center;gap:8px;">
            <span id="file-preview-name" style="font-size:11px;color:#475569;font-weight:600;flex:1;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;"></span>
            <button onclick="clearAttachment()" style="border:none;background:#fecdd3;color:#dc2626;border-radius:6px;width:22px;height:22px;cursor:pointer;font-size:14px;line-height:1;">×</button>
        </div>

        <div style="padding:12px 16px;border-top:1px solid #e2e8f0;display:flex;gap:8px;align-items:center;">
            
            <input type="file" id="chat-file-input"
                   accept="image/*,.pdf,.doc,.docx,.xls,.xlsx,.txt,.zip"
                   style="display:none;" onchange="onFileSelected(this)">

            
            <button onclick="document.getElementById('chat-file-input').click()"
                    title="Joindre un fichier"
                    style="width:40px;height:40px;border-radius:10px;border:1.5px solid #e2e8f0;background:#f8fafc;cursor:pointer;font-size:18px;display:flex;align-items:center;justify-content:center;flex-shrink:0;transition:border-color .15s;"
                    onmouseover="this.style.borderColor='<?php echo e($accent); ?>'" onmouseout="this.style.borderColor='#e2e8f0'">
                📎
            </button>

            <input id="chat-input" type="text" placeholder="Votre message…" maxlength="1000"
                   style="flex:1;height:40px;padding:0 12px;border-radius:10px;border:1.5px solid #e2e8f0;font-size:13px;outline:none;"
                   onkeydown="if(event.key==='Enter')sendChatMsg()">

            <button onclick="sendChatMsg()"
                    style="height:40px;padding:0 16px;border-radius:10px;border:none;background:#16a34a;color:white;font-weight:700;font-size:13px;cursor:pointer;flex-shrink:0;">
                Envoyer
            </button>
        </div>
    </div>
</div>

</div>

<script>
let currentReportationId = null;

// ══════════════════════════════════════════════
// REAL-TIME: new reportations
// ══════════════════════════════════════════════
if (window.Echo) {
    window.Echo.channel('reportations')
        .listen('ReportationCreated', (e) => {
            injectNewCard(e);
            updateCountBadge('en_attente', +1);
        });
}

function updateCountBadge(status, delta) {
    const box = document.querySelector(`[data-count="${status}"]`);
    if (box) box.textContent = parseInt(box.textContent) + delta;
    const tab = document.querySelector(`[data-tab="${status}"]`);
    if (tab) tab.textContent = parseInt(tab.textContent) + delta;
}

function injectNewCard(e) {
    const currentStatus = '<?php echo e($status); ?>';
    if (currentStatus !== 'en_attente' && currentStatus !== '') return;

    const initials = (e.formateur || 'IN').split(' ').slice(0,2).map(w => w[0]?.toUpperCase() || '').join('');
    const card = document.createElement('div');
    card.className = 'rp-card';
    card.style.animation = 'slideIn .3s ease';
    card.setAttribute('data-id', e.id);
    card.innerHTML = `
        <div style="padding:14px 20px;border-bottom:1px solid #f1f5f9;display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:10px;">
            <div style="display:flex;align-items:center;gap:12px;">
                <div style="width:38px;height:38px;border-radius:10px;background:<?php echo e($light); ?>;display:flex;align-items:center;justify-content:center;font-size:12px;font-weight:800;color:<?php echo e($text); ?>;">${escapeHtml(initials)}</div>
                <div>
                    <div style="font-size:13px;font-weight:700;color:#0f172a;">${escapeHtml(e.formateur)}</div>
                    <div style="font-size:10px;color:#64748b;">${escapeHtml(e.created_at)}</div>
                </div>
            </div>
            <div style="display:flex;align-items:center;gap:8px;">
                <span class="status-pill attente">⏳ En attente</span>
                <span style="font-size:10px;background:#dbeafe;color:#1e40af;padding:3px 10px;border-radius:99px;font-weight:700;">🔴 Nouveau</span>
            </div>
        </div>
        <div style="padding:16px 20px;display:grid;grid-template-columns:1fr 1fr;gap:16px;">
            <div>
                <div style="font-size:9px;font-weight:800;color:#94a3b8;text-transform:uppercase;letter-spacing:1.5px;margin-bottom:8px;">Séance concernée</div>
                <div style="padding:12px 14px;border-radius:10px;background:#f8fafc;border:1px solid #e2e8f0;">
                    <div style="font-size:12px;font-weight:700;color:#1e293b;margin-bottom:5px;">${escapeHtml(e.module)}</div>
                    <div style="font-size:10px;color:#475569;margin-bottom:3px;">👥 ${escapeHtml(e.groupe)} · ${escapeHtml(e.filiere)}</div>
                    <div style="font-size:10px;color:#475569;margin-bottom:3px;">📅 ${escapeHtml(e.date_debut)}</div>
                    <div style="font-size:10px;color:#475569;">🕐 ${escapeHtml(e.heure_debut)} → ${escapeHtml(e.heure_fin)}</div>
                </div>
            </div>
            <div>
                <div style="font-size:9px;font-weight:800;color:#94a3b8;text-transform:uppercase;letter-spacing:1.5px;margin-bottom:8px;">Raison</div>
                <div style="padding:12px 14px;border-radius:10px;background:#f8fafc;border:1px solid #e2e8f0;border-left:3px solid #7c3aed;font-size:11px;color:#334155;line-height:1.6;">${escapeHtml(e.raison)}</div>
            </div>
        </div>
        <div style="padding:10px 16px;border-top:1px solid #f1f5f9;background:#fffbeb;">
            <div style="font-size:11px;color:#92400e;font-weight:600;">⚡ Nouvelle demande — rechargez la page pour les actions complètes.</div>
        </div>`;

    const firstCard = document.querySelector('.rp-card');
    const wrap = document.querySelector('.rp-wrap');
    if (firstCard) {
        wrap.insertBefore(card, firstCard);
    } else {
        const empty = wrap.querySelector('div[style*="text-align:center"]');
        if (empty) empty.remove();
        wrap.appendChild(card);
    }
    showToast('📋 Nouvelle demande de ' + e.formateur);
}

// ══════════════════════════════════════════════
// CHAT FUNCTIONS
// ══════════════════════════════════════════════
function openChat(id, name) {
    currentReportationId = id;
    document.getElementById('chat-title').textContent = '💬 ' + name;
    document.getElementById('chat-messages').innerHTML =
        '<div style="text-align:center;font-size:12px;color:#94a3b8;">Chargement…</div>';
    document.getElementById('chat-modal').style.display = 'flex';

    fetch(`/reportations/${id}/messages`)
        .then(r => r.json())
        .then(msgs => {
            const box = document.getElementById('chat-messages');
            box.innerHTML = msgs.length === 0
                ? '<div style="text-align:center;font-size:12px;color:#94a3b8;">Aucun message pour l\'instant.</div>'
                : '';
            msgs.forEach(appendMsg);
            box.scrollTop = box.scrollHeight;
        });
}

function closeChat() {
    document.getElementById('chat-modal').style.display = 'none';
    currentReportationId = null;
    clearAttachment(); // Reset attachment when closing
}

function onFileSelected(input) {
    const file = input.files[0];
    if (!file) return;
    document.getElementById('file-preview-name').textContent = '📎 ' + file.name;
    document.getElementById('file-preview-bar').style.display = 'flex';
}

function clearAttachment() {
    document.getElementById('chat-file-input').value = '';
    document.getElementById('file-preview-bar').style.display = 'none';
    document.getElementById('file-preview-name').textContent = '';
}

function appendMsg(msg) {
    const me = <?php echo e(auth()->id()); ?>;
    const isMe = msg.user_id == me;
    const box = document.getElementById('chat-messages');
    const div = document.createElement('div');
    div.style.cssText = `display:flex;flex-direction:column;align-items:${isMe ? 'flex-end' : 'flex-start'};gap:2px;`;

    let attachmentHtml = '';
    if (msg.attachment_url) {
        if (msg.attachment_type === 'image') {
            attachmentHtml = `
                <a href="${msg.attachment_url}" target="_blank" style="display:block;max-width:200px;margin-top:4px;">
                    <img src="${msg.attachment_url}" alt="${escapeHtml(msg.attachment_name)}"
                         style="max-width:200px;max-height:160px;border-radius:8px;border:1px solid #e2e8f0;display:block;">
                </a>`;
        } else {
            attachmentHtml = `
                <a href="${msg.attachment_url}" target="_blank"
                   style="display:inline-flex;align-items:center;gap:6px;margin-top:4px;padding:7px 12px;border-radius:8px;background:${isMe ? 'rgba(255,255,255,0.15)' : '#e2e8f0'};color:${isMe ? 'white' : '#1e293b'};font-size:11px;font-weight:600;text-decoration:none;max-width:200px;overflow:hidden;">
                    📄 <span style="overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">${escapeHtml(msg.attachment_name)}</span>
                </a>`;
        }
    }

    const msgHtml = msg.message
        ? `<div style="max-width:75%;padding:8px 12px;border-radius:${isMe ? '12px 12px 2px 12px' : '12px 12px 12px 2px'};background:${isMe ? '#16a34a' : '#f1f5f9'};color:${isMe ? 'white' : '#1e293b'};font-size:12px;line-height:1.5;">${escapeHtml(msg.message)}</div>`
        : '';

    div.innerHTML = `
        <div style="font-size:9px;color:#94a3b8;">${escapeHtml(msg.user_name)} · ${escapeHtml(msg.created_at)}</div>
        ${msgHtml}
        ${attachmentHtml}`;
    box.appendChild(div);
}

function sendChatMsg() {
    const input     = document.getElementById('chat-input');
    const fileInput = document.getElementById('chat-file-input');
    const msg       = input.value.trim();
    const file      = fileInput.files[0] ?? null;

    if (!msg && !file) return;
    if (!currentReportationId) return;

    const rpId = currentReportationId;
    input.value = '';

    const formData = new FormData();
    if (msg)  formData.append('message', msg);
    if (file) formData.append('attachment', file);
    formData.append('_token', '<?php echo e(csrf_token()); ?>');

    const socketId = window.Echo?.socketId() ?? null;
    const headers  = {
        'Accept': 'application/json',   // ← FIX : force Laravel à répondre en JSON
    };
    if (socketId) headers['X-Socket-ID'] = socketId;

    clearAttachment();

    fetch(`/reportations/${rpId}/message`, {
        method: 'POST',
        headers,
        body: formData
    })
    .then(r => {
        if (!r.ok) {                    // ← FIX : vérifier le statut HTTP
            return r.json().then(err => {
                const msg = err?.message || err?.error
                    || Object.values(err?.errors ?? {})[0]?.[0]
                    || 'Erreur lors de l\'envoi.';
                showSendError(msg);
                throw new Error(msg);
            });
        }
        return r.json();
    })
    .then(data => {
        if (currentReportationId === rpId) {
            const box   = document.getElementById('chat-messages');
            const empty = box.querySelector('div[style*="text-align:center"]');
            if (empty) empty.remove();
            appendMsg(data);
            box.scrollTop = 99999;
        }
        const badge = document.getElementById('chat-count-' + rpId);
        if (badge) badge.textContent = parseInt(badge.textContent || '0') + 1;
    })
    .catch(err => console.error('Erreur envoi:', err));
}

// Ajouter cette fonction helper dans les 3 blades
function showSendError(message) {
    const box = document.getElementById('chat-messages');
    const div = document.createElement('div');
    div.style.cssText = 'text-align:center;padding:6px 12px;font-size:11px;color:#dc2626;background:#fff1f2;border-radius:8px;border:1px solid #fecdd3;';
    div.textContent = '⚠ ' + message;
    box.appendChild(div);
    box.scrollTop = 99999;
    setTimeout(() => div.remove(), 5000); // disparaît après 5s
}
// ══════════════════════════════════════════════
// ACCEPT MODAL
// ══════════════════════════════════════════════
function openAcceptModal(reportationId, formateurName, moduleName, currentDate, heureDebut, heureFin) {
    document.getElementById('accept-session-label').textContent = formateurName + ' — ' + moduleName;
    document.getElementById('accept-current-date').textContent  = currentDate + ' · ' + heureDebut + ' → ' + heureFin;
    const [y, m, d] = currentDate.split('-').map(Number);
    const base = new Date(y, m - 1, d + 7);
    const pad  = n => String(n).padStart(2, '0');
    const fmt  = (dt, h, mi) => `${dt.getFullYear()}-${pad(dt.getMonth()+1)}-${pad(dt.getDate())}T${pad(h)}:${pad(mi)}`;
    const [dh, dm] = heureDebut.split(':').map(Number);
    const [fh, fm] = heureFin.split(':').map(Number);
    document.getElementById('accept-debut').value = fmt(base, dh, dm);
    document.getElementById('accept-fin').value   = fmt(base, fh, fm);
    document.getElementById('accept-form').action = `/reportations/${reportationId}/accept`;
    document.getElementById('accept-modal').classList.add('open');
}

function closeAcceptModal() {
    document.getElementById('accept-modal').classList.remove('open');
}

document.getElementById('accept-debut').addEventListener('change', function() {
    const debut = new Date(this.value);
    if (!debut || isNaN(debut)) return;
    const fin = document.getElementById('accept-fin');
    const oldFin = new Date(fin.value);
    if (!oldFin || isNaN(oldFin)) return;
    const durMs = oldFin - new Date(this._prevValue || this.value);
    this._prevValue = this.value;
    if (durMs > 0) {
        const newFin = new Date(debut.getTime() + durMs);
        const pad = n => String(n).padStart(2,'0');
        fin.value = `${newFin.getFullYear()}-${pad(newFin.getMonth()+1)}-${pad(newFin.getDate())}T${pad(newFin.getHours())}:${pad(newFin.getMinutes())}`;
    }
});

// ══════════════════════════════════════════════
// GLOBAL ECHO — wait for Echo to be ready then subscribe
// ══════════════════════════════════════════════
function subscribeAll() {
    if (!window.Echo) {
        setTimeout(subscribeAll, 300);
        return;
    }
    <?php $__currentLoopData = $reportations; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $rp): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
    window.Echo.channel('reportation.<?php echo e($rp->id); ?>')
        .listen('.message.sent', function(e) {
            const rpId = <?php echo e($rp->id); ?>;
            if (currentReportationId === rpId) {
                const box = document.getElementById('chat-messages');
                const empty = box.querySelector('div[style*="text-align:center"]');
                if (empty) empty.remove();
                appendMsg(e);
                box.scrollTop = 99999;
            }
            const badge = document.getElementById('chat-count-' + rpId);
            if (badge) badge.textContent = parseInt(badge.textContent || '0') + 1;
        });
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
}
subscribeAll();

// ══════════════════════════════════════════════
// UTILS
// ══════════════════════════════════════════════
function showToast(msg) {
    const t = document.createElement('div');
    t.style.cssText = 'position:fixed;bottom:24px;right:24px;z-index:99;padding:14px 20px;background:#1e293b;color:white;border-radius:14px;font-size:13px;font-weight:600;box-shadow:0 8px 30px rgba(0,0,0,0.2);transition:opacity .3s;';
    t.textContent = msg;
    document.body.appendChild(t);
    setTimeout(() => { t.style.opacity = '0'; setTimeout(() => t.remove(), 300); }, 4000);
}

function escapeHtml(text) {
    if (text === null || text === undefined) return '';
    const div = document.createElement('div');
    div.textContent = String(text);
    return div.innerHTML;
}
</script>

<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\Project\gestion-CF\resources\views/reportations/index.blade.php ENDPATH**/ ?>