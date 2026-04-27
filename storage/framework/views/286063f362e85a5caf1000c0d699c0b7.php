
<?php $__env->startSection('title', 'Modifier stagiaire'); ?>
<?php $__env->startSection('page-title', 'Modifier stagiaire'); ?>

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
    --accent-lt: <?php echo e($p['light']); ?>;
    --accent-tx: <?php echo e($p['text']); ?>;
    --accent-bd: <?php echo e($p['border']); ?>;
    --accent-sh: <?php echo e($p['shadow']); ?>;
    --accent-gr: <?php echo e($p['gradient']); ?>;
}
.edu-label { display:block; font-size:9px; font-weight:800; color:#94a3b8; text-transform:uppercase; letter-spacing:1.5px; margin-bottom:6px; }
.edu-input { width:100%; height:42px; padding:0 12px; border-radius:10px; border:1.5px solid #e2e8f0; background:#f8fafc; font-size:13px; color:#1e293b; outline:none; transition:all .15s; box-sizing:border-box; }
.edu-input:focus { border-color:var(--accent); background:white; }
.edu-select { appearance:none; cursor:pointer; }
</style>

<div style="font-family:'Segoe UI',system-ui,sans-serif; max-width:620px; margin:0 auto;">

    
    <div style="display:flex;align-items:center;gap:12px;margin-bottom:24px;">
        <a href="<?php echo e(route('edu-import.index', ['tab'=>'accounts'])); ?>"
           style="display:inline-flex;align-items:center;gap:5px;padding:7px 12px;border-radius:10px;
                  border:1.5px solid #e2e8f0;background:white;color:#475569;font-size:12px;
                  font-weight:600;text-decoration:none;">
            <svg width="12" height="12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"/>
            </svg>
            Retour
        </a>
        <div>
            <h1 style="font-size:18px;font-weight:800;color:#0f172a;margin:0;">Modifier le stagiaire</h1>
            <p style="font-size:11px;color:#64748b;margin:2px 0 0;"><?php echo e($edu->prenom); ?> <?php echo e($edu->nom); ?> — <?php echo e($edu->edu_email); ?></p>
        </div>
    </div>

    <?php if($errors->any()): ?>
        <div style="margin-bottom:16px;padding:12px 16px;border-radius:12px;background:#fff1f2;border:1px solid #fecdd3;">
            <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $e): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <p style="font-size:12px;color:#be123c;margin:2px 0;">✕ <?php echo e($e); ?></p>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    <?php endif; ?>

    <div style="background:white;border-radius:20px;border:1px solid #e2e8f0;box-shadow:0 2px 12px rgba(0,0,0,0.06);overflow:hidden;">

        
        <div style="padding:14px 24px;background:var(--accent-lt);border-bottom:1px solid var(--accent-bd);display:flex;align-items:center;justify-content:space-between;">
            <span style="font-size:11px;font-weight:700;color:var(--accent-tx);">ID #<?php echo e($edu->id); ?></span>
            <?php if($edu->used): ?>
                <span style="padding:3px 12px;border-radius:99px;font-size:10px;font-weight:700;background:#f0fdf4;color:#15803d;border:1px solid #bbf7d0;">
                    ✓ Compte déjà créé
                </span>
            <?php else: ?>
                <span style="padding:3px 12px;border-radius:99px;font-size:10px;font-weight:700;background:#fffbeb;color:#92400e;border:1px solid #fde68a;">
                    ⏳ En attente
                </span>
            <?php endif; ?>
        </div>

        <form method="POST" action="<?php echo e(route('edu-import.update', $edu->id)); ?>"
              style="padding:24px;display:flex;flex-direction:column;gap:16px;">
            <?php echo csrf_field(); ?>
            <?php echo method_field('PUT'); ?>

            <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
                <div>
                    <label class="edu-label">Nom</label>
                    <input type="text" name="nom" value="<?php echo e(old('nom', $edu->nom)); ?>" required class="edu-input" placeholder="Alami">
                </div>
                <div>
                    <label class="edu-label">Prénom</label>
                    <input type="text" name="prenom" value="<?php echo e(old('prenom', $edu->prenom)); ?>" required class="edu-input" placeholder="Mohammed">
                </div>
            </div>

            <div>
                <label class="edu-label">Email EDU</label>
                <input type="email" name="edu_email" value="<?php echo e(old('edu_email', $edu->edu_email)); ?>" required class="edu-input">
            </div>

            <div>
                <label class="edu-label">Nouveau mot de passe <span style="color:#94a3b8;font-weight:400;">(laisser vide pour ne pas changer)</span></label>
                <input type="password" name="password" class="edu-input" placeholder="Min. 6 caractères">
            </div>

            <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
                <div>
                    <label class="edu-label">Filière</label>
                    <select name="filiere_code" id="edit-filiere" required
                            onchange="filterGroupsEdit(this.value)"
                            class="edu-input edu-select">
                        <option value="">— Sélectionner —</option>
                        <?php $__currentLoopData = $filieres; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $f): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($f->code); ?>" <?php echo e(old('filiere_code', $edu->filiere_code) === $f->code ? 'selected' : ''); ?>>
                                <?php echo e($f->code); ?> — <?php echo e($f->name); ?>

                            </option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
                <div>
                    <label class="edu-label">Groupe</label>
                    <select name="groupe_code" id="edit-groupe" required class="edu-input edu-select">
                        <option value="">— Filière d'abord —</option>
                        <?php $__currentLoopData = $groupes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $g): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php if($g->filiere?->code === $edu->filiere_code): ?>
                                <option value="<?php echo e($g->code); ?>" <?php echo e(old('groupe_code', $edu->groupe_code) === $g->code ? 'selected' : ''); ?>>
                                    <?php echo e($g->code); ?> — <?php echo e($g->name); ?>

                                </option>
                            <?php endif; ?>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
            </div>

            <div style="display:flex;gap:10px;padding-top:4px;">
                <a href="<?php echo e(route('edu-import.index', ['tab'=>'accounts'])); ?>"
                   style="flex:1;height:44px;border-radius:12px;border:1.5px solid #e2e8f0;
                          background:white;font-size:13px;font-weight:600;color:#64748b;
                          text-decoration:none;display:flex;align-items:center;justify-content:center;">
                    Annuler
                </a>
                <button type="submit"
                        style="flex:2;height:44px;border-radius:12px;border:none;
                               background:var(--accent-gr);color:white;font-size:13px;
                               font-weight:700;cursor:pointer;box-shadow:0 4px 12px var(--accent-sh);
                               display:flex;align-items:center;justify-content:center;gap:8px;">
                    <svg width="14" height="14" fill="none" stroke="white" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                    </svg>
                    Enregistrer les modifications
                </button>
            </div>
        </form>
    </div>
</div>

<?php $groupesJs = $groupes->map(fn($g) => ['code'=>$g->code,'name'=>$g->name??'G'.$g->id,'filiere_code'=>$g->filiere?->code])->values(); ?>
<script>
const allGroupesEdit = <?php echo json_encode($groupesJs, 15, 512) ?>;
const currentGroupe  = "<?php echo e($edu->groupe_code); ?>";

function filterGroupsEdit(fc) {
    const sel = document.getElementById('edit-groupe');
    sel.innerHTML = '<option value="">— Sélectionner —</option>';
    allGroupesEdit.filter(g => g.filiere_code === fc).forEach(g => {
        const o = document.createElement('option');
        o.value = g.code;
        o.textContent = g.code + ' — ' + g.name;
        if (g.code === currentGroupe) o.selected = true;
        sel.appendChild(o);
    });
}

// Init on load
document.addEventListener('DOMContentLoaded', () => {
    const fc = document.getElementById('edit-filiere').value;
    if (fc) filterGroupsEdit(fc);
});
</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\Project\gestion-CF\resources\views/gestionnaire/edu-edit.blade.php ENDPATH**/ ?>