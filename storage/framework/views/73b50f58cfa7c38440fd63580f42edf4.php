

<?php $__env->startSection('title', 'Nouvelle réclamation'); ?>
<?php $__env->startSection('page-title', 'Nouvelle réclamation'); ?>

<?php $__env->startSection('content'); ?>
<?php
    $p = [
        'primary'  => '#1a4f8a',
        'medium'   => '#2563eb',
        'light'    => '#eff6ff',
        'lighter'  => '#f0f7ff',
        'text'     => '#1e40af',
        'border'   => '#bfdbfe',
        'shadow'   => 'rgba(26,79,138,0.15)',
        'gradient' => 'linear-gradient(135deg,#1a4f8a 0%,#2563eb 100%)',
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
.form-wrap { font-family:'Segoe UI',system-ui,sans-serif; max-width:1200px; margin:0 auto; }
.form-card { background:white; border-radius:20px; border:1px solid #e2e8f0; overflow:hidden; }
.form-header { background:var(--accent-gr); padding:28px 32px; position:relative; overflow:hidden; }
.form-header::after { content:''; position:absolute; right:-40px; top:-40px; width:200px; height:200px; border-radius:50%; background:rgba(255,255,255,0.06); pointer-events:none; }
.form-header-icon { width:52px; height:52px; border-radius:16px; background:rgba(255,255,255,0.15); display:flex; align-items:center; justify-content:center; margin-bottom:14px; }
.form-header h1 { font-size:20px; font-weight:800; color:white; margin:0 0 4px; }
.form-header p { font-size:12px; color:rgba(255,255,255,0.75); margin:0; }
.form-body { padding:32px; }
.form-group { margin-bottom:22px; }
.form-label { display:block; font-size:12px; font-weight:700; color:#374151; margin-bottom:8px; letter-spacing:.3px; }
.form-label span { color:#ef4444; }
.form-select, .form-textarea {
    width:100%; border:1.5px solid #e2e8f0; border-radius:12px;
    padding:12px 14px; font-size:13px; color:#1e293b;
    font-family:inherit; outline:none; transition:border-color .15s, box-shadow .15s;
    background:white; box-sizing:border-box;
}
.form-select:focus, .form-textarea:focus {
    border-color:var(--accent-bd);
    box-shadow:0 0 0 3px rgba(26,79,138,0.08);
}
.form-textarea { resize:vertical; min-height:140px; line-height:1.6; }
.form-hint { font-size:11px; color:#94a3b8; margin-top:6px; }
.type-option-grid { display:grid; grid-template-columns:repeat(auto-fill, minmax(160px,1fr)); gap:10px; }
.type-option { position:relative; }
.type-option input[type=radio] { position:absolute; opacity:0; width:0; height:0; }
.type-option label {
    display:flex; flex-direction:column; align-items:center; justify-content:center;
    gap:8px; padding:16px 10px; border-radius:14px;
    border:1.5px solid #e2e8f0; cursor:pointer; transition:all .15s;
    font-size:11px; font-weight:700; color:#64748b; text-align:center;
}
.type-option input[type=radio]:checked + label {
    border-color:var(--accent); background:var(--accent-lt);
    color:var(--accent-tx); box-shadow:0 0 0 3px rgba(26,79,138,0.1);
}
.type-option label:hover { border-color:var(--accent-bd); background:var(--accent-ltr); }
.type-option-icon { font-size:24px; }
.char-count { font-size:11px; color:#94a3b8; text-align:right; margin-top:6px; }
.char-count.warn { color:#f59e0b; }
.char-count.danger { color:#ef4444; }
.form-footer { display:flex; gap:12px; align-items:center; padding-top:8px; border-top:1px solid #f1f5f9; }
.btn-submit { font-size:13px; font-weight:700; padding:12px 28px; border-radius:12px; background:var(--accent-gr); color:white; border:none; cursor:pointer; display:inline-flex; align-items:center; gap:8px; box-shadow:0 2px 8px var(--accent-sh); transition:opacity .15s; }
.btn-submit:hover { opacity:.88; }
.btn-cancel { font-size:12px; font-weight:600; padding:12px 20px; border-radius:12px; background:white; border:1.5px solid #e2e8f0; color:#64748b; text-decoration:none; display:inline-flex; align-items:center; gap:6px; transition:all .15s; }
.btn-cancel:hover { border-color:#cbd5e1; background:#f8fafc; }
.error-msg { font-size:11px; color:#ef4444; margin-top:5px; }
</style>

<div class="form-wrap">

<?php if($errors->any()): ?>
<div style="padding:14px 18px;background:#fff1f2;border:1px solid #fecdd3;border-radius:14px;margin-bottom:16px;">
    <div style="font-size:12px;font-weight:700;color:#be123c;margin-bottom:6px;">Veuillez corriger les erreurs :</div>
    <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $err): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <div style="font-size:11px;color:#9f1239;margin-top:2px;">• <?php echo e($err); ?></div>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
</div>
<?php endif; ?>

<div class="form-card">
    <div class="form-header">
        <div class="form-header-icon">
            <svg width="26" height="26" fill="none" stroke="white" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/>
            </svg>
        </div>
        <h1>Nouvelle réclamation</h1>
        <p>Décrivez votre réclamation. Elle sera transmise à l'équipe pédagogique.</p>
    </div>

    <form action="<?php echo e(route('reclamations.store')); ?>" method="POST" class="form-body">
        <?php echo csrf_field(); ?>

        
        <div class="form-group">
            <label class="form-label">Type de réclamation <span>*</span></label>
            <div class="type-option-grid">
                <?php $__currentLoopData = [
                    'note'      => ['icon'=>'📝', 'label'=>'Note'],
                    'absence'   => ['icon'=>'📅', 'label'=>'Absence'],
                    'emploi'    => ['icon'=>'🗓️', 'label'=>'Emploi du temps'],
                    'formateur' => ['icon'=>'👨‍🏫', 'label'=>'Formateur'],
                    'autre'     => ['icon'=>'📌', 'label'=>'Autre'],
                ]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $val => $opt): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="type-option">
                        <input type="radio" name="type" id="type_<?php echo e($val); ?>" value="<?php echo e($val); ?>"
                            <?php echo e(old('type') === $val ? 'checked' : ''); ?> required>
                        <label for="type_<?php echo e($val); ?>">
                            <span class="type-option-icon"><?php echo e($opt['icon']); ?></span>
                            <?php echo e($opt['label']); ?>

                        </label>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
            <?php $__errorArgs = ['type'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="error-msg"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
        </div>

        
        <div class="form-group">
            <label class="form-label" for="description">Description <span>*</span></label>
            <textarea
                id="description"
                name="description"
                class="form-textarea"
                placeholder="Décrivez votre réclamation de façon précise et détaillée…"
                maxlength="1000"
                oninput="updateCharCount(this)"
            ><?php echo e(old('description')); ?></textarea>
            <div class="char-count" id="char-count">0 / 1000 caractères</div>
            <div class="form-hint">Minimum 10 caractères. Soyez précis pour faciliter le traitement.</div>
            <?php $__errorArgs = ['description'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="error-msg"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
        </div>

        <div class="form-footer">
            <button type="submit" class="btn-submit">
                <svg width="15" height="15" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg>
                Soumettre la réclamation
            </button>
            <a href="<?php echo e(route('reclamations.index')); ?>" class="btn-cancel">← Retour</a>
        </div>
    </form>
</div>

</div>

<script>
function updateCharCount(el) {
    const count = el.value.length;
    const counter = document.getElementById('char-count');
    counter.textContent = count + ' / 1000 caractères';
    counter.className = 'char-count' + (count > 900 ? ' danger' : count > 700 ? ' warn' : '');

    // ← ZID HAD PARTIE
    const words = el.value.split(/\s+/);
    const hasLongWord = words.some(w => w.length > 50);
    if (hasLongWord) {
        counter.textContent += ' ⚠️ Mot trop long détecté';
        counter.className = 'char-count danger';
    }
}
document.addEventListener('DOMContentLoaded', () => {
    const ta = document.getElementById('description');
    if (ta && ta.value) updateCharCount(ta);
});
</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\Project\gestion-CF\resources\views/reclamations/create.blade.php ENDPATH**/ ?>