
<?php $__env->startSection('title', 'Gestion des utilisateurs'); ?>
<?php $__env->startSection('page-title', 'Gestion des utilisateurs'); ?>

<?php $__env->startSection('content'); ?>
<style>
    .users-wrapper { font-family:'Segoe UI',system-ui,sans-serif; max-width:1100px; margin:0 auto; }

    /* Table grid */
    .table-head,
    .table-row {
        display:grid;
        grid-template-columns:2fr 120px 150px 130px 110px;
        padding:10px 20px;
        align-items:center;
        gap:8px;
    }
    .table-head { background:#f8fafc; border-bottom:1.5px solid #e2e8f0; }
    .table-row  { border-bottom:1px solid #f1f5f9; background:white; transition:background 0.1s; }
    .table-row:hover { background:#fafbff; }

    /* Hide cols on mobile */
    .col-spatie,
    .col-modules { display:block; }

    /* Action buttons */
    .action-btn {
        width:30px; height:30px; border-radius:8px;
        background:#f1f5f9; border:1px solid #e2e8f0;
        display:inline-flex; align-items:center; justify-content:center;
        font-size:13px; cursor:pointer; color:#475569;
        text-decoration:none; transition:all 0.12s;
    }
    .action-btn:hover { background:#e8f5ee; border-color:#0a664030; color:#0a6640; }
    .action-btn.blue:hover  { background:#eff6ff; border-color:#bfdbfe; color:#1d4ed8; }
    .action-btn.red:hover   { background:#fef2f2; border-color:#fecaca; color:#dc2626; }

    /* Mobile card layout */
    @media(max-width:700px) {
        .table-head { display:none; }
        .table-row {
            display:flex; flex-direction:column;
            padding:14px 16px; gap:10px;
            border-radius:12px; margin-bottom:8px;
            border:1px solid #e2e8f0 !important;
            box-shadow:0 1px 4px rgba(0,0,0,0.05);
        }
        .row-user   { width:100%; }
        .col-role   { width:fit-content; }
        .col-spatie,
        .col-modules { width:fit-content; }
        .col-actions { display:flex; gap:6px; width:100%; }
        .action-btn  { flex:1; width:auto; padding:0 10px; font-size:12px; gap:5px; }
    }

    @media(max-width:480px) {
        .users-wrapper { padding:0 4px; }
    }

    /* Filters */
    .filter-bar { display:flex; gap:8px; margin-bottom:16px; flex-wrap:wrap; }
    .filter-input {
        flex:1; min-width:160px; height:38px; padding:0 12px; border-radius:9px;
        border:1.5px solid #e2e8f0; background:white; font-size:12px;
        color:#1e293b; outline:none; box-sizing:border-box;
    }
    .filter-input:focus { border-color:#0a6640; }
    .filter-select {
        height:38px; padding:0 12px; border-radius:9px; border:1.5px solid #e2e8f0;
        background:white; font-size:12px; color:#475569; outline:none; cursor:pointer;
    }

    /* Header */
    .page-header {
        display:flex; align-items:flex-start; justify-content:space-between;
        flex-wrap:wrap; gap:12px; margin-bottom:20px;
    }
    .header-actions { display:flex; gap:8px; flex-wrap:wrap; }
</style>

<div class="users-wrapper">


<?php if(session('success')): ?>
<div style="margin-bottom:16px; padding:11px 16px; border-radius:10px; font-size:12px;
            display:flex; align-items:center; gap:8px;
            background:#f0fdf4; border:1px solid #bbf7d0; color:#15803d;">
    <svg width="14" height="14" fill="currentColor" viewBox="0 0 20 20">
        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
    </svg>
    <?php echo e(session('success')); ?>

</div>
<?php endif; ?>


<div class="page-header">
    <div>
        <h1 style="font-size:22px; font-weight:800; color:#0f172a; margin:0 0 4px;">Utilisateurs</h1>
        <div style="display:flex; align-items:center; gap:8px; flex-wrap:wrap;">
            <?php $__currentLoopData = [
                ['#0a6640', $stats['total'],        'total'],
                $canManageGestionnaire ? ['#2563eb', $stats['gestionnaire'], 'gestionnaires'] : null,
                $canManageFormateur    ? ['#9333ea', $stats['formateur'],    'formateurs']    : null,
            ]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $s): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <?php if($s): ?>
            <?php [$col,$cnt,$lbl] = $s; ?>
            <?php if($i > 0): ?><span style="color:#e2e8f0; font-size:12px;">·</span><?php endif; ?>
            <span style="font-size:12px; color:#64748b;">
                <span style="font-weight:800; color:<?php echo e($col); ?>;"><?php echo e($cnt); ?></span> <?php echo e($lbl); ?>

            </span>
            <?php endif; ?>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    </div>

    <div class="header-actions">
        <?php if($canManageFormateur): ?>
        <a href="<?php echo e(route('users.management.create', ['role'=>'formateur'])); ?>"
           style="display:inline-flex; align-items:center; gap:5px; height:38px; padding:0 14px;
                  border-radius:9px; background:#9333ea; color:white; font-size:12px;
                  font-weight:700; text-decoration:none; white-space:nowrap;"
           onmouseover="this.style.background='#7e22ce'"
           onmouseout="this.style.background='#9333ea'">
            + Formateur
        </a>
        <?php endif; ?>
        <?php if($canManageGestionnaire): ?>
        <a href="<?php echo e(route('users.management.create', ['role'=>'gestionnaire'])); ?>"
           style="display:inline-flex; align-items:center; gap:5px; height:38px; padding:0 14px;
                  border-radius:9px; background:#2563eb; color:white; font-size:12px;
                  font-weight:700; text-decoration:none; white-space:nowrap;"
           onmouseover="this.style.background='#1d4ed8'"
           onmouseout="this.style.background='#2563eb'">
            + Gestionnaire
        </a>
        <?php endif; ?>
    </div>
</div>


<form method="GET" action="<?php echo e(route('users.management.index')); ?>" class="filter-bar">
    <input type="text" name="search" value="<?php echo e($search); ?>"
           placeholder="🔍 Rechercher nom, email, CIN..."
           class="filter-input">

    <select name="role" class="filter-select">
        <option value="">Tous les rôles</option>
        <option value="gestionnaire" <?php echo e($filterRole==='gestionnaire'?'selected':''); ?>>Gestionnaire</option>
        <option value="formateur"    <?php echo e($filterRole==='formateur'   ?'selected':''); ?>>Formateur</option>
    </select>

    <button type="submit"
            style="height:38px; padding:0 16px; border-radius:9px; border:none;
                   background:#0a6640; color:white; font-size:12px; font-weight:600; cursor:pointer;"
            onmouseover="this.style.background='#065f38'"
            onmouseout="this.style.background='#0a6640'">
        Filtrer
    </button>

    <?php if($search || $filterRole): ?>
    <a href="<?php echo e(route('users.management.index')); ?>"
       style="height:38px; padding:0 12px; border-radius:9px; border:1.5px solid #e2e8f0;
              background:white; color:#94a3b8; font-size:12px; font-weight:500;
              text-decoration:none; display:flex; align-items:center;">
        ✕ Effacer
    </a>
    <?php endif; ?>
</form>


<div style="background:white; border-radius:14px; border:1px solid #e2e8f0;
            box-shadow:0 1px 8px rgba(0,0,0,0.05); overflow:hidden;">

    
    <div class="table-head">
        <?php $__currentLoopData = ['Utilisateur','Rôle','Accès Spatie','Modules','Actions']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $h): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <span style="font-size:10px; font-weight:700; color:#94a3b8;
                     letter-spacing:1.2px; text-transform:uppercase;"><?php echo e($h); ?></span>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>

    
    <?php $__empty_1 = true; $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
    <?php
        $rc = match($user->role) {
            'gestionnaire' => ['bg'=>'#eff6ff','text'=>'#1d4ed8','dot'=>'#2563eb'],
            'formateur'    => ['bg'=>'#fdf4ff','text'=>'#7e22ce','dot'=>'#9333ea'],
            default        => ['bg'=>'#f8fafc','text'=>'#475569','dot'=>'#94a3b8'],
        };
        $spatieRoleNames = $user->roles->pluck('name')->implode(', ');
        $initials = strtoupper(substr($user->name,0,1))
                  . strtoupper(substr(explode(' ',$user->name)[1] ?? '',0,1));
        $modCount = $user->modules->count();
    ?>

    <div class="table-row">

        
        <div class="row-user" style="display:flex; align-items:center; gap:10px; min-width:0;">
            <div style="width:34px; height:34px; border-radius:9px; flex-shrink:0;
                        background:<?php echo e($rc['bg']); ?>; display:flex; align-items:center;
                        justify-content:center; font-size:11px; font-weight:800;
                        color:<?php echo e($rc['text']); ?>;"><?php echo e($initials); ?></div>
            <div style="min-width:0;">
                <div style="font-size:13px; font-weight:700; color:#0f172a;
                            white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">
                    <?php echo e($user->name); ?>

                </div>
                <div style="font-size:11px; color:#94a3b8; margin-top:1px;
                            white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">
                    <?php echo e($user->email); ?>

                </div>
            </div>
        </div>

        
        <div class="col-role">
            <span style="display:inline-flex; align-items:center; gap:5px; font-size:11px;
                         font-weight:600; color:<?php echo e($rc['text']); ?>; padding:3px 10px;
                         border-radius:99px; background:<?php echo e($rc['bg']); ?>; white-space:nowrap;">
                <span style="width:6px; height:6px; border-radius:50%;
                             background:<?php echo e($rc['dot']); ?>; flex-shrink:0;"></span>
                <?php echo e(ucfirst($user->role)); ?>

            </span>
        </div>

        
        <div class="col-spatie">
            <?php if($spatieRoleNames): ?>
            <span style="font-size:11px; font-weight:600; color:#15803d; padding:3px 10px;
                         border-radius:99px; background:#f0fdf4; border:1px solid #bbf7d0;
                         white-space:nowrap;">
                ✓ <?php echo e($spatieRoleNames); ?>

            </span>
            <?php else: ?>
            <span style="font-size:11px; color:#cbd5e1;">—</span>
            <?php endif; ?>
        </div>

        
        <div class="col-modules">
            <?php if($user->role === 'formateur'): ?>
                <?php if($modCount > 0): ?>
                <span style="font-size:11px; font-weight:600; color:#7e22ce; padding:3px 10px;
                             border-radius:99px; background:#fdf4ff; white-space:nowrap;">
                    <?php echo e($modCount); ?> module<?php echo e($modCount > 1 ? 's' : ''); ?>

                </span>
                <?php else: ?>
                <span style="font-size:11px; color:#cbd5e1;">Aucun</span>
                <?php endif; ?>
            <?php else: ?>
            <span style="font-size:11px; color:#cbd5e1;">—</span>
            <?php endif; ?>
        </div>

        
        <div class="col-actions" style="display:flex; align-items:center; gap:5px;">
            <a href="<?php echo e(route('users.management.edit', $user)); ?>"
               class="action-btn" title="Éditer">✎</a>

            <button class="action-btn blue" title="Modifier le rôle"
                    onclick="openRoleModal(<?php echo e($user->id); ?>, '<?php echo e(addslashes($user->name)); ?>', '<?php echo e($spatieRoleNames); ?>')">
                🛡
            </button>

            <form method="POST" action="<?php echo e(route('users.management.destroy', $user)); ?>"
                  onsubmit="return confirm('Supprimer « <?php echo e(addslashes($user->name)); ?> » ?')"
                  style="display:contents;">
                <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                <button type="submit" class="action-btn red" title="Supprimer">🗑</button>
            </form>
        </div>

    </div>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
    <div style="padding:60px; text-align:center;">
        <div style="font-size:32px; margin-bottom:10px; opacity:0.3;">👤</div>
        <p style="font-size:13px; font-weight:600; color:#94a3b8; margin:0;">Aucun utilisateur trouvé</p>
    </div>
    <?php endif; ?>

    <?php if($users->total() > 0): ?>
    <div style="padding:10px 20px; background:#f8fafc; border-top:1px solid #f1f5f9;">
        <span style="font-size:11px; color:#94a3b8;">
            <?php echo e($users->firstItem()); ?>–<?php echo e($users->lastItem()); ?> sur <?php echo e($users->total()); ?> utilisateur(s)
        </span>
    </div>
    <?php endif; ?>
</div>


<?php if($users->hasPages()): ?>
<div style="margin-top:14px; display:flex; justify-content:center;">
    <?php echo e($users->links()); ?>

</div>
<?php endif; ?>


<div id="role-modal" style="display:none; position:fixed; inset:0; z-index:60;
     background:rgba(15,23,42,0.45); backdrop-filter:blur(4px);
     align-items:center; justify-content:center;"
     onclick="if(event.target===this)closeRoleModal()">
    <div style="background:white; border-radius:18px; width:100%; max-width:420px;
                margin:16px; padding:24px; box-shadow:0 24px 60px rgba(0,0,0,0.16);
                max-height:90vh; overflow-y:auto;">

        <div style="display:flex; align-items:center; justify-content:space-between;
                    margin-bottom:16px; padding-bottom:14px; border-bottom:1.5px solid #f1f5f9;">
            <div>
                <div style="font-size:14px; font-weight:800; color:#0f172a;">Modifier le rôle Spatie</div>
                <div id="modal-user-name" style="font-size:11px; color:#64748b; margin-top:2px;"></div>
            </div>
            <button onclick="closeRoleModal()"
                    style="width:28px; height:28px; border-radius:7px; border:1px solid #e2e8f0;
                           background:#f8fafc; color:#94a3b8; font-size:15px;
                           cursor:pointer; line-height:1; flex-shrink:0;">×</button>
        </div>

        <div style="padding:9px 13px; border-radius:9px; background:#f8fafc;
                    border:1px solid #e2e8f0; margin-bottom:14px; font-size:12px; color:#64748b;">
            Rôle actuel :
            <span id="modal-current-role" style="font-weight:700; color:#0a6640;"></span>
        </div>

        <form id="role-form" method="POST" style="display:flex; flex-direction:column; gap:12px;">
            <?php echo csrf_field(); ?> <?php echo method_field('PATCH'); ?>
            <input type="hidden" name="search" value="<?php echo e($search); ?>">
            <input type="hidden" name="role"   value="<?php echo e($filterRole); ?>">

            <div style="display:flex; flex-direction:column; gap:7px;">
                <?php $__currentLoopData = $spatieRoles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sRole): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php
                    $src = match($sRole->name) {
                        'gestionnaire' => ['bg'=>'#eff6ff','border'=>'#2563eb','text'=>'#1d4ed8'],
                        'formateur'    => ['bg'=>'#fdf4ff','border'=>'#9333ea','text'=>'#7e22ce'],
                        default        => ['bg'=>'#f8fafc','border'=>'#64748b','text'=>'#334155'],
                    };
                    $permNames = $sRole->permissions->pluck('name')->take(3)->implode(', ');
                    $more = max(0, $sRole->permissions->count() - 3);
                ?>
                <label class="role-option-label"
                       data-color="<?php echo e($src['border']); ?>" data-bg="<?php echo e($src['bg']); ?>"
                       style="display:flex; align-items:center; gap:10px; cursor:pointer;
                              padding:11px 14px; border-radius:11px; border:1.5px solid #e2e8f0;
                              background:white; transition:all 0.12s; user-select:none;">
                    <input type="radio" name="spatie_role" value="<?php echo e($sRole->name); ?>"
                           class="role-radio" style="display:none;"
                           onchange="styleRoleLabel(this)">
                    <div class="role-radio-dot"
                         style="width:16px; height:16px; border-radius:50%; flex-shrink:0;
                                border:2px solid #cbd5e1; background:white;
                                display:flex; align-items:center; justify-content:center;
                                transition:all 0.12s;"></div>
                    <div style="flex:1; min-width:0;">
                        <div style="font-size:12px; font-weight:700; color:#1e293b; text-transform:capitalize;">
                            <?php echo e($sRole->name); ?>

                        </div>
                        <div style="font-size:10px; color:#94a3b8; margin-top:1px;
                                    overflow:hidden; text-overflow:ellipsis; white-space:nowrap;">
                            <?php if($sRole->permissions->count()): ?>
                                <?php echo e($permNames); ?><?php echo e($more > 0 ? ' +'.$more : ''); ?>

                            <?php else: ?>
                                Aucune permission
                            <?php endif; ?>
                        </div>
                    </div>
                    <span style="font-size:9px; font-weight:700; padding:2px 7px; border-radius:99px;
                                 background:<?php echo e($src['bg']); ?>; color:<?php echo e($src['text']); ?>; white-space:nowrap;">
                        <?php echo e($sRole->permissions->count()); ?>p
                    </span>
                </label>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>

            <div style="display:flex; gap:8px; margin-top:4px;">
                <button type="button" onclick="closeRoleModal()"
                        style="flex:1; height:42px; border-radius:10px; border:1.5px solid #e2e8f0;
                               background:white; font-size:12px; font-weight:600;
                               color:#64748b; cursor:pointer;">
                    Annuler
                </button>
                <button type="submit"
                        style="flex:1; height:42px; border-radius:10px; border:none;
                               background:#0a6640; font-size:12px; font-weight:700;
                               color:white; cursor:pointer;">
                    Enregistrer
                </button>
            </div>
        </form>
    </div>
</div>

</div>

<script>
function openRoleModal(userId, userName, currentRole) {
    document.getElementById('modal-user-name').textContent = userName;
    document.getElementById('modal-current-role').textContent = currentRole || 'Aucun';
    document.getElementById('role-form').action = '/users/' + userId + '/role';

    document.querySelectorAll('.role-option-label').forEach(lbl => {
        lbl.style.background  = 'white';
        lbl.style.borderColor = '#e2e8f0';
        const dot = lbl.querySelector('.role-radio-dot');
        dot.style.borderColor = '#cbd5e1';
        dot.style.background  = 'white';
        dot.innerHTML         = '';
    });

    document.querySelectorAll('.role-radio').forEach(r => {
        r.checked = false;
        if (currentRole && r.value === currentRole.trim()) {
            r.checked = true;
            styleRoleLabel(r);
        }
    });

    document.getElementById('role-modal').style.display = 'flex';
}

function closeRoleModal() {
    document.getElementById('role-modal').style.display = 'none';
}

function styleRoleLabel(radio) {
    document.querySelectorAll('.role-option-label').forEach(lbl => {
        lbl.style.background  = 'white';
        lbl.style.borderColor = '#e2e8f0';
        const dot = lbl.querySelector('.role-radio-dot');
        dot.style.borderColor = '#cbd5e1';
        dot.style.background  = 'white';
        dot.innerHTML         = '';
    });
    const label = radio.closest('label');
    const color = label.dataset.color;
    const bg    = label.dataset.bg;
    label.style.background  = bg;
    label.style.borderColor = color;
    const dot = label.querySelector('.role-radio-dot');
    dot.style.borderColor = color;
    dot.style.background  = color;
    dot.innerHTML = '<svg width="7" height="7" fill="none" stroke="white" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>';
}
</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\Project\gestion-CF\resources\views/users/index.blade.php ENDPATH**/ ?>