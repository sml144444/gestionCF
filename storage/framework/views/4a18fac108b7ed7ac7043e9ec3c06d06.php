
<?php $__env->startSection('title', 'Gestion des rôles'); ?>
<?php $__env->startSection('page-title', 'Gestion des rôles'); ?>

<?php $__env->startSection('content'); ?>
<?php
    $user = Auth::user();
    $role = $user->role;
    $palettes = [
        'admin'        => ['primary'=>'#0a6640','medium'=>'#1a8c56','light'=>'#e8f5ee','lighter'=>'#f0fdf4','text'=>'#065f38','border'=>'#bbf7d0','shadow'=>'rgba(10,102,64,0.15)','gradient'=>'linear-gradient(135deg,#0a6640 0%,#1a8c56 100%)'],
        'gestionnaire' => ['primary'=>'#1e293b','medium'=>'#334155','light'=>'#f1f5f9','lighter'=>'#f8fafc','text'=>'#1e293b','border'=>'#cbd5e1','shadow'=>'rgba(30,41,59,0.15)','gradient'=>'linear-gradient(135deg,#1e293b 0%,#334155 100%)'],
        'formateur'    => ['primary'=>'#1a4f8a','medium'=>'#2563eb','light'=>'#eff6ff','lighter'=>'#f0f7ff','text'=>'#1e40af','border'=>'#bfdbfe','shadow'=>'rgba(26,79,138,0.15)','gradient'=>'linear-gradient(135deg,#1a4f8a 0%,#2563eb 100%)'],
    ];
    $p = $palettes[$role] ?? $palettes['gestionnaire'];
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
.role-wrap { font-family:'Segoe UI',system-ui,sans-serif; max-width:1200px; margin:0 auto; }
.role-hero { background:var(--accent-gr); border-radius:20px; padding:28px 32px; margin-bottom:24px; display:flex; align-items:center; justify-content:space-between; gap:16px; flex-wrap:wrap; position:relative; overflow:hidden; }
.role-hero::after { content:''; position:absolute; right:-40px; top:-40px; width:200px; height:200px; border-radius:50%; background:rgba(255,255,255,0.06); pointer-events:none; }
.role-hero-icon { width:52px; height:52px; border-radius:16px; background:rgba(255,255,255,0.15); display:flex; align-items:center; justify-content:center; flex-shrink:0; }
.role-hero-title { font-size:20px; font-weight:800; color:white; margin:0; }
.role-hero-sub { font-size:12px; color:rgba(255,255,255,0.75); margin-top:3px; }
.role-hero-badge { background:rgba(255,255,255,0.15); border:1px solid rgba(255,255,255,0.25); color:white; font-size:11px; font-weight:700; padding:6px 14px; border-radius:99px; }
.flash-ok { display:flex; align-items:center; gap:12px; padding:14px 18px; border-radius:14px; margin-bottom:18px; background:var(--accent-ltr); border:1px solid var(--accent-bd); animation:fadeIn .3s ease; }
.flash-ok-icon { width:38px; height:38px; border-radius:50%; background:var(--accent-gr); display:flex; align-items:center; justify-content:center; flex-shrink:0; }
@keyframes fadeIn { from{opacity:0;transform:translateY(-6px)} to{opacity:1;transform:translateY(0)} }
.role-grid { display:grid; grid-template-columns:repeat(auto-fill, minmax(340px, 1fr)); gap:18px; }
.role-card { background:white; border-radius:18px; border:1px solid #e2e8f0; overflow:hidden; transition:all .2s; }
.role-card:hover { transform:translateY(-2px); box-shadow:0 12px 28px rgba(0,0,0,0.1); }
.role-card-header { padding:18px 20px; display:flex; align-items:center; justify-content:space-between; border-bottom:1px solid #f1f5f9; }
.role-card-icon { width:44px; height:44px; border-radius:14px; display:flex; align-items:center; justify-content:center; flex-shrink:0; }
.role-card-name { font-size:16px; font-weight:800; margin:0; }
.role-card-badge { font-size:9px; font-weight:700; padding:4px 12px; border-radius:99px; background:rgba(0,0,0,0.05); }
.role-card-badge.system { background:#fef3c7; color:#92400e; border:1px solid #fde68a; }
.role-card-body { padding:16px 20px; min-height:100px; }
.role-perms-list { display:flex; flex-wrap:wrap; gap:6px; }
.role-perm-tag { font-size:9px; font-weight:700; padding:4px 10px; border-radius:8px; }
.role-card-footer { padding:14px 20px; border-top:1px solid #f1f5f9; display:flex; gap:8px; }
.btn-sm { font-size:11px; font-weight:600; padding:7px 14px; border-radius:10px; text-decoration:none; display:inline-flex; align-items:center; gap:5px; cursor:pointer; transition:all .15s; border:none; }
.btn-sm-outline { background:white; border:1.5px solid #e2e8f0; color:#64748b; }
.btn-sm-outline:hover { border-color:var(--accent-bd); background:var(--accent-lt); color:var(--accent-tx); }
.btn-sm-primary { background:var(--accent-gr); color:white; box-shadow:0 2px 8px var(--accent-sh); }
.btn-sm-primary:hover { opacity:.88; }
.btn-sm-danger { background:#fee2e2; color:#dc2626; border:1px solid #fecaca; }
.btn-sm-danger:hover { background:#fecaca; }
.role-stats { display:flex; gap:12px; margin-top:12px; font-size:10px; color:#94a3b8; }
</style>

<div class="role-wrap">


<?php if(session('success')): ?>
    <div class="flash-ok">
        <div class="flash-ok-icon"><svg width="18" height="18" fill="none" stroke="white" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg></div>
        <p style="font-size:13px;font-weight:600;color:var(--accent-tx);margin:0;"><?php echo e(session('success')); ?></p>
    </div>
<?php endif; ?>
<?php if(session('error')): ?>
    <div style="padding:14px 18px;background:#fff1f2;border:1px solid #fecdd3;border-radius:14px;margin-bottom:16px;">
        <p style="font-size:12px;color:#be123c;margin:0;">✕ <?php echo e(session('error')); ?></p>
    </div>
<?php endif; ?>


<div class="role-hero">
    <div style="display:flex;align-items:center;gap:16px;">
        <div class="role-hero-icon">
            <svg width="26" height="26" fill="none" stroke="white" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
            </svg>
        </div>
        <div>
            <h1 class="role-hero-title">Rôles & Permissions</h1>
            <p class="role-hero-sub">
                <strong style="color:white;"><?php echo e($roles->count()); ?></strong> rôles configurés
            </p>
        </div>
    </div>
    <span class="role-hero-badge"><?php echo e(ucfirst($role)); ?></span>
</div>


<?php if(Auth::user()->role === 'admin'): ?>
<div style="margin-bottom:24px; display:flex; justify-content:flex-end;">
    <a href="<?php echo e(route('roles.create')); ?>" class="btn-sm btn-sm-primary" style="padding:10px 20px;">
        <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
        Nouveau rôle
    </a>
</div>
<?php endif; ?>


<div class="role-grid">
<?php $__empty_1 = true; $__currentLoopData = $roles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $roleItem): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
    <?php
        $isSystem = in_array($roleItem->name, ['admin','gestionnaire','formateur','stagiaire']);
        $roleColors = [
            'admin'        => ['bg'=>'#0a6640','light'=>'#e8f5ee','text'=>'#065f38'],
            'gestionnaire' => ['bg'=>'#1e293b','light'=>'#f1f5f9','text'=>'#1e293b'],
            'formateur'    => ['bg'=>'#1a4f8a','light'=>'#eff6ff','text'=>'#1e40af'],
            'stagiaire'    => ['bg'=>'#ea580c','light'=>'#fff7ed','text'=>'#9a3412'],
        ];
        $rc = $roleColors[$roleItem->name] ?? ['bg'=>'#64748b','light'=>'#f8fafc','text'=>'#334155'];
        
        $permGroups = $roleItem->permissions->groupBy(fn($p) => explode('-', $p->name)[0]);
        $permColors = [
            'emploi'      => ['bg'=>'#eff6ff','text'=>'#1e40af'],
            'user'        => ['bg'=>'#f0fdf4','text'=>'#166534'],
            'stagiaire'   => ['bg'=>'#ecfeff','text'=>'#0e7490'],
            'groupe'      => ['bg'=>'#fdf4ff','text'=>'#6b21a8'],
            'role'        => ['bg'=>'#fff1f2','text'=>'#9f1239'],
            'edu'         => ['bg'=>'#fff7ed','text'=>'#9a3412'],
            'reportation' => ['bg'=>'#f5f3ff','text'=>'#5b21b6'],
            'salle'       => ['bg'=>'#f0fdfa','text'=>'#0f766e'],
            'reclamation' => ['bg'=>'#f0fdfa','text'=>'#0f766e'],
            'news'        => ['bg'=>'#fefce8','text'=>'#b45309'],
            'absence'     => ['bg'=>'#fdf2f8','text'=>'#be185d'],
        ];
    ?>
    <div class="role-card">
        <div class="role-card-header">
            <div style="display:flex; align-items:center; gap:12px;">
                <div class="role-card-icon" style="background:<?php echo e($rc['light']); ?>;">
                    <svg width="22" height="22" fill="none" stroke="<?php echo e($rc['bg']); ?>" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                    </svg>
                </div>
                <div>
                    <div class="role-card-name" style="color:<?php echo e($rc['text']); ?>; text-transform:capitalize;">
                        <?php echo e($roleItem->name); ?>

                    </div>
                    <div class="role-stats">
                        <span>📋 <?php echo e($roleItem->permissions->count()); ?> permission(s)</span>
                    </div>
                </div>
            </div>
            <?php if($isSystem): ?>
                <span class="role-card-badge system">🔒 Système</span>
            <?php endif; ?>
        </div>

        <div class="role-card-body">
            <?php if($roleItem->permissions->isEmpty()): ?>
                <p style="font-size:11px; color:#94a3b8; font-style:italic; margin:0;">Aucune permission assignée</p>
            <?php else: ?>
                <div class="role-perms-list">
                    <?php $__currentLoopData = $roleItem->permissions->sortBy('name')->take(6); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $perm): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php $prefix = explode('-', $perm->name)[0]; $pc = $permColors[$prefix] ?? ['bg'=>'#f8fafc','text'=>'#334155']; ?>
                        <span class="role-perm-tag" style="background:<?php echo e($pc['bg']); ?>; color:<?php echo e($pc['text']); ?>;">
                            <?php echo e(str_replace('-view', '', $perm->name)); ?>

                        </span>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    <?php if($roleItem->permissions->count() > 6): ?>
                        <span class="role-perm-tag" style="background:#f1f5f9; color:#64748b;">
                            +<?php echo e($roleItem->permissions->count() - 6); ?> autres
                        </span>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>

        <div class="role-card-footer">
            <?php if(Auth::user()->role === 'admin'): ?>
                <a href="<?php echo e(route('roles.show', $roleItem)); ?>" class="btn-sm btn-sm-outline">📋 Détails</a>
                <a href="<?php echo e(route('roles.edit', $roleItem)); ?>" class="btn-sm btn-sm-primary">✎ Modifier</a>
                <?php if(!$isSystem): ?>
                    <button onclick="openDeleteRoleModal('<?php echo e(route('roles.destroy', $roleItem)); ?>', '<?php echo e($roleItem->name); ?>')" class="btn-sm btn-sm-danger" style="margin-left:auto;">🗑️ Supprimer</button>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    </div>
<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
    <div style="grid-column:1/-1; padding:60px; text-align:center; background:white; border-radius:20px; border:1px solid #e2e8f0;">
        <div style="width:64px;height:64px;border-radius:20px;background:var(--accent-lt);margin:0 auto 16px;display:flex;align-items:center;justify-content:center;">
            <svg width="28" height="28" fill="none" stroke="var(--accent)" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
        </div>
        <p style="font-size:14px;font-weight:700;color:#1e293b;margin:0 0 4px;">Aucun rôle trouvé</p>
        <p style="font-size:12px;color:#94a3b8;margin:0;">Créez un nouveau rôle pour commencer.</p>
    </div>
<?php endif; ?>
</div>


<div id="delete-role-modal" style="display:none; position:fixed; inset:0; z-index:60;
     background:rgba(15,23,42,0.5); backdrop-filter:blur(4px);
     align-items:center; justify-content:center;"
     onclick="if(event.target===this)closeDeleteRoleModal()">
    <div style="background:white; border-radius:20px; width:100%; max-width:400px; margin:16px; padding:24px;">
        <div style="display:flex; align-items:center; gap:12px; margin-bottom:16px; padding-bottom:14px; border-bottom:2px solid #dc2626;">
            <div style="width:44px;height:44px;border-radius:12px;background:#fee2e2;display:flex;align-items:center;justify-content:center;">
                <svg width="20" height="20" fill="none" stroke="#dc2626" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
            </div>
            <div>
                <div style="font-size:15px;font-weight:800;color:#1e293b;">Supprimer le rôle ?</div>
                <div style="font-size:11px;color:#64748b;" id="delete-role-name"></div>
            </div>
            <button onclick="closeDeleteRoleModal()" style="margin-left:auto;width:28px;height:28px;border-radius:8px;border:none;background:#f1f5f9;cursor:pointer;">×</button>
        </div>
        <div style="padding:12px 14px; border-radius:12px; background:#fff1f2; border:1px solid #fecdd3; font-size:12px; color:#9f1239; margin-bottom:20px;">
            Les utilisateurs ayant ce rôle perdront leurs permissions associées.
        </div>
        <div style="display:flex; gap:10px;">
            <button onclick="closeDeleteRoleModal()" class="btn-sm btn-sm-outline" style="flex:1; justify-content:center;">Annuler</button>
            <form id="delete-role-form" method="POST" style="flex:1;">
                <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                <button type="submit" class="btn-sm btn-sm-danger" style="width:100%; justify-content:center;">🗑️ Supprimer</button>
            </form>
        </div>
    </div>
</div>

</div>

<script>
function openDeleteRoleModal(action, name) {
    document.getElementById('delete-role-form').action = action;
    document.getElementById('delete-role-name').textContent = 'Rôle : ' + name;
    document.getElementById('delete-role-modal').style.display = 'flex';
}
function closeDeleteRoleModal() {
    document.getElementById('delete-role-modal').style.display = 'none';
}
</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\Project\gestion-CF\resources\views/roles/index.blade.php ENDPATH**/ ?>