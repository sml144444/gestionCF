
<?php $__env->startSection('title', 'Nouvelle publication'); ?>
<?php $__env->startSection('page-title', 'News & Événements'); ?>

<?php $__env->startSection('content'); ?>
<?php
    $user = Auth::user();
    $role = $user->role;
    $palettes = [
        'admin'        => ['primary'=>'#0a6640','medium'=>'#1a8c56','light'=>'#e8f5ee','lighter'=>'#f0fdf4','text'=>'#065f38','border'=>'#bbf7d0','shadow'=>'rgba(10,102,64,0.15)','gradient'=>'linear-gradient(135deg,#0a6640 0%,#1a8c56 100%)'],
        'gestionnaire' => ['primary'=>'#1e293b','medium'=>'#334155','light'=>'#f1f5f9','lighter'=>'#f8fafc','text'=>'#1e293b','border'=>'#cbd5e1','shadow'=>'rgba(30,41,59,0.15)','gradient'=>'linear-gradient(135deg,#1e293b 0%,#334155 100%)'],
        'formateur'    => ['primary'=>'#1a4f8a','medium'=>'#2563eb','light'=>'#eff6ff','lighter'=>'#f0f7ff','text'=>'#1e40af','border'=>'#bfdbfe','shadow'=>'rgba(26,79,138,0.15)','gradient'=>'linear-gradient(135deg,#1a4f8a 0%,#2563eb 100%)'],
        'stagiaire'    => ['primary'=>'#1a4f8a','medium'=>'#2563eb','light'=>'#eff6ff','lighter'=>'#f0f7ff','text'=>'#1e40af','border'=>'#bfdbfe','shadow'=>'rgba(26,79,138,0.15)','gradient'=>'linear-gradient(135deg,#1a4f8a 0%,#2563eb 100%)'],
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
.nf-wrap { font-family:'Segoe UI',system-ui,sans-serif; max-width:760px; margin:0 auto; }
.nf-back { display:inline-flex; align-items:center; gap:6px; font-size:12px; font-weight:600; color:#64748b; text-decoration:none; padding:8px 14px; border-radius:10px; background:white; border:1.5px solid #e2e8f0; margin-bottom:20px; transition:all .15s; }
.nf-back:hover { color:var(--accent-tx); border-color:var(--accent-bd); background:var(--accent-lt); }
.nf-hero { background:var(--accent-gr); border-radius:20px; padding:24px 28px; margin-bottom:24px; display:flex; align-items:center; gap:16px; position:relative; overflow:hidden; }
.nf-hero::after { content:''; position:absolute; right:-30px; top:-30px; width:160px; height:160px; border-radius:50%; background:rgba(255,255,255,0.06); pointer-events:none; }
.nf-hero-icon { width:48px; height:48px; border-radius:15px; background:rgba(255,255,255,0.15); display:flex; align-items:center; justify-content:center; flex-shrink:0; }
.nf-hero-title { font-size:18px; font-weight:800; color:white; margin:0; }
.nf-hero-sub { font-size:11px; color:rgba(255,255,255,0.75); margin-top:2px; }
.nf-card { background:white; border-radius:20px; border:1px solid #e2e8f0; padding:28px 32px; }
.nf-label { display:block; font-size:12px; font-weight:700; color:#374151; margin-bottom:8px; }
.nf-label span { color:#dc2626; }
.nf-input { width:100%; padding:12px 16px; border-radius:12px; border:1.5px solid #e2e8f0; font-size:14px; font-family:inherit; outline:none; transition:all .15s; box-sizing:border-box; color:#1e293b; }
.nf-input:focus { border-color:var(--accent-bd); box-shadow:0 0 0 3px <?php echo e($p['shadow']); ?>; }
.nf-textarea { min-height:220px; resize:vertical; }
.nf-error { font-size:11px; color:#dc2626; margin-top:4px; display:block; }
.nf-field { margin-bottom:20px; }
.nf-upload-zone { border:2px dashed #e2e8f0; border-radius:16px; padding:32px; text-align:center; cursor:pointer; transition:all .2s; background:#fafafa; }
.nf-upload-zone:hover { border-color:var(--accent-bd); background:var(--accent-ltr); }
.nf-upload-zone.has-file { border-color:var(--accent-bd); background:var(--accent-ltr); }
.nf-preview { max-width:100%; border-radius:12px; margin-top:16px; display:none; max-height:300px; object-fit:cover; }
.btn-primary { display:inline-flex; align-items:center; gap:8px; padding:12px 24px; border-radius:12px; border:none; background:var(--accent-gr); color:white; font-size:14px; font-weight:700; cursor:pointer; transition:all .15s; box-shadow:0 4px 12px var(--accent-sh); }
.btn-primary:hover { opacity:.88; transform:translateY(-1px); }
.btn-outline { display:inline-flex; align-items:center; gap:6px; padding:11px 20px; border-radius:12px; border:1.5px solid #e2e8f0; background:white; color:#64748b; font-size:13px; font-weight:600; cursor:pointer; transition:all .15s; text-decoration:none; }
.btn-outline:hover { border-color:var(--accent-bd); color:var(--accent-tx); background:var(--accent-lt); }
</style>

<div class="nf-wrap">
    <a href="<?php echo e(route('news.index')); ?>" class="nf-back">
        <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        Retour
    </a>

    <div class="nf-hero">
        <div class="nf-hero-icon">
            <svg width="24" height="24" fill="none" stroke="white" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
        </div>
        <div>
            <h1 class="nf-hero-title">Nouvelle publication</h1>
            <p class="nf-hero-sub">Partagez une actualité ou un événement avec la communauté</p>
        </div>
    </div>

    <div class="nf-card">
        <form action="<?php echo e(route('news.store')); ?>" method="POST" enctype="multipart/form-data">
            <?php echo csrf_field(); ?>

            
            <div class="nf-field">
                <label class="nf-label" for="titre">Titre <span>*</span></label>
                <input
                    type="text"
                    id="titre"
                    name="titre"
                    class="nf-input"
                    value="<?php echo e(old('titre')); ?>"
                    placeholder="Titre de votre publication..."
                    required>
                <?php $__errorArgs = ['titre'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="nf-error"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>

            
            <div class="nf-field">
                <label class="nf-label" for="contenu">Contenu <span>*</span></label>
                <textarea
                    id="contenu"
                    name="contenu"
                    class="nf-input nf-textarea"
                    placeholder="Rédigez votre publication ici..."
                    required><?php echo e(old('contenu')); ?></textarea>
                <?php $__errorArgs = ['contenu'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="nf-error"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>

            
            <div class="nf-field">
                <label class="nf-label" for="image">Image (optionnelle)</label>
                <div class="nf-upload-zone" id="upload-zone" onclick="document.getElementById('image').click()">
                    <svg width="32" height="32" fill="none" stroke="#94a3b8" viewBox="0 0 24 24" style="margin:0 auto 10px;">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                    <p style="font-size:13px;font-weight:600;color:#64748b;margin:0;">Cliquez pour ajouter une image</p>
                    <p style="font-size:11px;color:#94a3b8;margin:4px 0 0;">JPEG, PNG, WebP — max 4 Mo</p>
                </div>
                <input type="file" id="image" name="image" accept="image/*" style="display:none;" onchange="previewImage(event)">
                <img id="preview" class="nf-preview" alt="Aperçu">
                <?php $__errorArgs = ['image'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="nf-error"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>

            
            <div style="display:flex; gap:12px; justify-content:flex-end; padding-top:16px; border-top:1px solid #f1f5f9;">
                <a href="<?php echo e(route('news.index')); ?>" class="btn-outline">Annuler</a>
                <button type="submit" class="btn-primary">
                    <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg>
                    Publier
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function previewImage(event) {
    const file = event.target.files[0];
    if (!file) return;
    const preview = document.getElementById('preview');
    const zone = document.getElementById('upload-zone');
    const reader = new FileReader();
    reader.onload = e => {
        preview.src = e.target.result;
        preview.style.display = 'block';
        zone.classList.add('has-file');
        zone.querySelector('p').textContent = file.name;
    };
    reader.readAsDataURL(file);
}
</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\Project\gestion-CF\resources\views/news/create.blade.php ENDPATH**/ ?>