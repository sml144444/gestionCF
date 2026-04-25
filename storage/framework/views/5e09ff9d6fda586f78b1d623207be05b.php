
<?php $__env->startSection('title', 'Nouvel utilisateur'); ?>
<?php $__env->startSection('page-title', 'Nouvel utilisateur'); ?>

<?php $__env->startSection('content'); ?>
<div style="font-family:'Segoe UI',system-ui,sans-serif; max-width:800px;">


<a href="<?php echo e(route('users.management.index')); ?>"
   style="display:inline-flex; align-items:center; gap:5px; font-size:12px; font-weight:600;
          color:#64748b; text-decoration:none; margin-bottom:20px;"
   onmouseover="this.style.color='#0a6640'" onmouseout="this.style.color='#64748b'">
    ← Retour à la liste
</a>

<div style="margin-bottom:24px;">
    <h1 style="font-size:20px; font-weight:800; color:#0f172a; margin:0;">Créer un utilisateur</h1>
    <p style="font-size:12px; color:#64748b; margin:4px 0 0;">Formateur ou Gestionnaire</p>
</div>


<div style="margin-bottom:20px; padding:14px 18px; border-radius:14px;
            background:#f0fdf4; border:1.5px solid #bbf7d0; display:flex; align-items:flex-start; gap:12px;">
    <span style="font-size:20px; flex-shrink:0; margin-top:1px;">📧</span>
    <div>
        <div style="font-size:12px; font-weight:700; color:#15803d; margin-bottom:3px;">
            Mot de passe & Matricule générés automatiquement
        </div>
        <div style="font-size:11px; color:#166534; line-height:1.6;">
            Un mot de passe sécurisé (majuscules, chiffres, caractères spéciaux) et un matricule unique
            seront générés automatiquement. Les identifiants seront envoyés à l'adresse e-mail renseignée.
        </div>
    </div>
</div>


<?php if($errors->any()): ?>
<div style="margin-bottom:16px; padding:12px 16px; border-radius:12px;
            background:#fef2f2; border:1px solid #fecaca; color:#dc2626; font-size:12px;">
    <div style="font-weight:700; margin-bottom:6px;">Veuillez corriger les erreurs :</div>
    <ul style="margin:0; padding-left:16px;">
        <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $e): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><li><?php echo e($e); ?></li><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </ul>
</div>
<?php endif; ?>

<form method="POST" action="<?php echo e(route('users.management.store')); ?>" enctype="multipart/form-data">
<?php echo csrf_field(); ?>


<div style="display:flex; gap:8px; margin-bottom:24px;">
    <?php $__currentLoopData = [
        'formateur'    => ['#9333ea','#fdf4ff','🎓'],
        'gestionnaire' => ['#2563eb','#eff6ff','🏢'],
    ]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $r => [$col,$bg,$icon]): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
    
    <?php if($r === 'gestionnaire' && !$canManageGestionnaire): ?> <?php continue; ?> <?php endif; ?>
    <?php $active = old('role', $role) === $r; ?>
    <label style="flex:1; cursor:pointer;">
        <input type="radio" name="role" value="<?php echo e($r); ?>"
               <?php echo e($active ? 'checked' : ''); ?>

               onchange="switchRole(this.value)"
               style="display:none;">
        <div id="tab-<?php echo e($r); ?>"
             style="padding:14px 18px; border-radius:14px; text-align:center; transition:all 0.15s;
                    border:2px solid <?php echo e($active ? $col : '#e2e8f0'); ?>;
                    background:<?php echo e($active ? $bg : 'white'); ?>;">
            <div style="font-size:15px; margin-bottom:4px;"><?php echo e($icon); ?></div>
            <div id="tab-label-<?php echo e($r); ?>"
                 style="font-size:13px; font-weight:700; text-transform:capitalize;
                        color:<?php echo e($active ? $col : '#64748b'); ?>;"><?php echo e(ucfirst($r)); ?></div>
        </div>
    </label>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
</div>


<div style="background:white; border-radius:16px; border:1px solid #e2e8f0;
            box-shadow:0 2px 12px rgba(0,0,0,0.06); overflow:hidden;">

    
    <div style="padding:22px 24px; border-bottom:1px solid #f1f5f9;">
        <div style="font-size:9px; font-weight:800; color:#94a3b8; letter-spacing:1.5px;
                    text-transform:uppercase; margin-bottom:16px;">Informations générales</div>

        <div style="display:grid; grid-template-columns:1fr 1fr; gap:14px;">
            <div style="grid-column:1/-1;">
                <?php echo $__env->make('users._field',['label'=>'Nom complet','name'=>'name','type'=>'text',
                    'placeholder'=>'Ex : Amine Benali','required'=>true], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
            </div>
            <div>
                <?php echo $__env->make('users._field',['label'=>'Adresse e-mail','name'=>'email','type'=>'email',
                    'placeholder'=>'exemple@ofppt.ma','required'=>true], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
            </div>
            <div>
                <?php echo $__env->make('users._field',['label'=>'CIN','name'=>'cin','type'=>'text',
                    'placeholder'=>'AB123456'], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
            </div>
            <div>
                <?php echo $__env->make('users._field',['label'=>'Téléphone','name'=>'phone','type'=>'tel'], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
            </div>
            <div>
                <?php echo $__env->make('users._field',['label'=>'Date de naissance','name'=>'date_naissance','type'=>'date'], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
            </div>
        </div>

        <div style="margin-top:14px;">
            <label style="font-size:11px; font-weight:700; color:#475569; display:block; margin-bottom:5px;">
                Photo <span style="color:#94a3b8; font-weight:400;">(optionnel)</span>
            </label>
            <input type="file" name="photo" accept="image/*"
                   style="font-size:12px; color:#475569; cursor:pointer;">
        </div>
    </div>

    
    <div id="formateur-section"
         style="padding:22px 24px; border-bottom:1px solid #f1f5f9;
                display:<?php echo e(old('role',$role) === 'formateur' ? 'block' : 'none'); ?>;">

        <div style="font-size:9px; font-weight:800; color:#9333ea; letter-spacing:1.5px;
                    text-transform:uppercase; margin-bottom:16px;">Infos Formateur</div>

        <div style="display:grid; grid-template-columns:1fr 1fr; gap:14px; margin-bottom:20px;">
            
            <div style="grid-column:1/-1;">
                <div style="padding:12px 16px; border-radius:10px; background:#f8fafc;
                            border:1.5px dashed #cbd5e1; display:flex; align-items:center; gap:10px;">
                    <span style="font-size:18px;">🪪</span>
                    <div>
                        <div style="font-size:10px; font-weight:700; color:#9333ea;
                                    text-transform:uppercase; letter-spacing:1px;">Matricule formateur</div>
                        <div style="font-size:11px; color:#64748b; margin-top:2px;">
                            Généré automatiquement après création
                            <span style="color:#9333ea; font-weight:600;">(F + ID + horodatage)</span>
                        </div>
                    </div>
                </div>
            </div>
            <div>
                <?php echo $__env->make('users._field',['label'=>"Date d'embauche",'name'=>'date_embauche','type'=>'date'], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
            </div>
            <div>
                <?php echo $__env->make('users._field',['label'=>"Limite d'heures / semaine",'name'=>'nbr_heure_limit',
                    'type'=>'number','value'=>old('nbr_heure_limit', 30),'placeholder'=>'Ex : 30'], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
            </div>
        </div>

        
        <div>
            <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:10px;">
                <div>
                    <span style="font-size:11px; font-weight:700; color:#475569;">Modules enseignés</span>
                    <span style="font-size:10px; color:#94a3b8; margin-left:4px;">(spécialisation)</span>
                </div>
                <span id="mod-counter"
                      style="font-size:10px; font-weight:700; padding:2px 10px; border-radius:99px;
                             background:#fdf4ff; color:#9333ea; border:1px solid #e9d5ff;">
                    0 sélectionné(s)
                </span>
            </div>

            <input type="text" id="mod-search" placeholder="Rechercher un module..."
                   oninput="filterMods()"
                   style="width:100%; height:36px; padding:0 12px; border-radius:10px; box-sizing:border-box;
                          border:1.5px solid #e2e8f0; background:#f8fafc; font-size:12px;
                          outline:none; margin-bottom:10px;"
                   onfocus="this.style.borderColor='#9333ea'; this.style.background='white';"
                   onblur="this.style.borderColor='#e2e8f0'; this.style.background='#f8fafc';">

            <div id="mods-grid"
                 style="display:grid; grid-template-columns:repeat(auto-fill,minmax(190px,1fr));
                        gap:8px; max-height:260px; overflow-y:auto; padding:2px;">
                <?php $__empty_1 = true; $__currentLoopData = $modules; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $mod): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <?php $chk = in_array($mod->id, old('modules', [])); ?>
                <label class="mod-card" data-name="<?php echo e(strtolower($mod->name)); ?>"
                       style="display:flex; align-items:center; gap:8px; padding:10px 12px;
                              border-radius:10px; cursor:pointer; transition:all 0.12s; user-select:none;
                              border:1.5px solid <?php echo e($chk ? '#9333ea' : '#e2e8f0'); ?>;
                              background:<?php echo e($chk ? '#fdf4ff' : 'white'); ?>;">
                    <input type="checkbox" name="modules[]" value="<?php echo e($mod->id); ?>"
                           class="mod-cb" <?php echo e($chk ? 'checked' : ''); ?>

                           onchange="syncModCard(this); updateModCount()"
                           style="display:none;">
                    <div class="mod-dot"
                         style="width:16px; height:16px; border-radius:5px; flex-shrink:0;
                                transition:all 0.12s; display:flex; align-items:center; justify-content:center;
                                border:2px solid <?php echo e($chk ? '#9333ea' : '#cbd5e1'); ?>;
                                background:<?php echo e($chk ? '#9333ea' : 'white'); ?>;">
                        <?php if($chk): ?>
                        <svg width="8" height="8" fill="none" stroke="white" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/>
                        </svg>
                        <?php endif; ?>
                    </div>
                    <span style="font-size:11px; font-weight:600; color:#334155; line-height:1.3;">
                        <?php echo e($mod->name); ?>

                    </span>
                </label>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <p style="font-size:12px; color:#94a3b8; font-style:italic; grid-column:1/-1;">
                    Aucun module disponible.
                </p>
                <?php endif; ?>
            </div>
        </div>
    </div>

    
    <div style="padding:16px 24px; background:#f8fafc; display:flex; gap:10px; justify-content:flex-end;">
        <a href="<?php echo e(route('users.management.index')); ?>"
           style="height:42px; padding:0 18px; border-radius:12px; border:1.5px solid #e2e8f0;
                  background:white; font-size:13px; font-weight:600; color:#64748b;
                  text-decoration:none; display:inline-flex; align-items:center;">
            Annuler
        </a>
        <button type="submit"
                style="height:42px; padding:0 24px; border-radius:12px; border:none;
                       background:#0a6640; color:white; font-size:13px; font-weight:700;
                       cursor:pointer; box-shadow:0 4px 12px rgba(10,102,64,0.25);"
                onmouseover="this.style.background='#065f38'"
                onmouseout="this.style.background='#0a6640'">
            ✓ Créer l'utilisateur
        </button>
    </div>
</div>

</form>
</div>

<script>
const TAB_COLORS = {
    formateur:    { col:'#9333ea', bg:'#fdf4ff' },
    gestionnaire: { col:'#2563eb', bg:'#eff6ff' },
};

function switchRole(role) {
    document.getElementById('formateur-section').style.display =
        role === 'formateur' ? 'block' : 'none';

    Object.keys(TAB_COLORS).forEach(r => {
        const active = r === role;
        const tab    = document.getElementById('tab-' + r);
        const lbl    = document.getElementById('tab-label-' + r);
        tab.style.borderColor = active ? TAB_COLORS[r].col : '#e2e8f0';
        tab.style.background  = active ? TAB_COLORS[r].bg  : 'white';
        lbl.style.color       = active ? TAB_COLORS[r].col : '#64748b';
    });
}

function syncModCard(cb) {
    const label = cb.closest('label');
    const dot   = label.querySelector('.mod-dot');
    if (cb.checked) {
        label.style.borderColor = '#9333ea'; label.style.background = '#fdf4ff';
        dot.style.borderColor   = '#9333ea'; dot.style.background   = '#9333ea';
        dot.innerHTML = '<svg width="8" height="8" fill="none" stroke="white" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>';
    } else {
        label.style.borderColor = '#e2e8f0'; label.style.background = 'white';
        dot.style.borderColor   = '#cbd5e1'; dot.style.background   = 'white';
        dot.innerHTML           = '';
    }
}

function updateModCount() {
    const n = document.querySelectorAll('.mod-cb:checked').length;
    document.getElementById('mod-counter').textContent = n + ' sélectionné(s)';
}

function filterMods() {
    const q = document.getElementById('mod-search').value.toLowerCase();
    document.querySelectorAll('.mod-card').forEach(c =>
        c.style.display = c.dataset.name.includes(q) ? '' : 'none'
    );
}

document.querySelectorAll('.mod-cb:checked').forEach(cb => syncModCard(cb));
updateModCount();
</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\Project\gestion-CF\resources\views/users/create.blade.php ENDPATH**/ ?>