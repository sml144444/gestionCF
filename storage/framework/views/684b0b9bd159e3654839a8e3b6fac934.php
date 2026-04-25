
<?php
    $val   = $value       ?? old($name, '');
    $ph    = $placeholder ?? ($type === 'tel' ? '+212 6XX XXX XXX' : '');
    $req   = $required    ?? false;
    $hasErr = $errors->has($name);
    $borderColor = $hasErr ? '#fca5a5' : '#e2e8f0';
    $bgColor     = $hasErr ? '#fef2f2' : '#f8fafc';
    $isPhone = $type === 'tel';
?>
<div>
    <label for="<?php echo e($name); ?>"
           style="display:block; font-size:11px; font-weight:700; color:#475569; margin-bottom:5px;">
        <?php echo e($label); ?><?php if($req): ?><span style="color:#ef4444; margin-left:2px;">*</span><?php endif; ?>
    </label>
    <input
        type="<?php echo e($type); ?>"
        id="<?php echo e($name); ?>"
        name="<?php echo e($name); ?>"
        value="<?php echo e($type !== 'password' ? $val : ''); ?>"
        placeholder="<?php echo e($ph); ?>"
        <?php echo e($req ? 'required' : ''); ?>

        <?php if($isPhone): ?>
            inputmode="tel"
            pattern="[\+0-9\s\-\(\)\.]{6,20}"
            title="Numéro de téléphone valide uniquement (ex: +212 6XX XXX XXX)"
            maxlength="20"
            oninput="this.value=this.value.replace(/[^+0-9\s\-\(\)\.]/g,'')"
        <?php endif; ?>
        style="width:100%; height:40px; padding:0 12px; border-radius:10px; box-sizing:border-box;
               border:1.5px solid <?php echo e($borderColor); ?>; background:<?php echo e($bgColor); ?>;
               font-size:13px; color:#1e293b; outline:none;"
        onfocus="this.style.borderColor='#0a6640'; this.style.background='white';"
        onblur="this.style.borderColor='<?php echo e($borderColor); ?>'; this.style.background='<?php echo e($bgColor); ?>';">
    <?php if($isPhone): ?>
        <div style="font-size:9px; color:#94a3b8; margin-top:3px;">
            Chiffres, espaces, +, -, ( ) uniquement
        </div>
    <?php endif; ?>
    <?php $__errorArgs = [$name];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
        <div style="font-size:10px; color:#ef4444; margin-top:4px;"><?php echo e($message); ?></div>
    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
</div><?php /**PATH C:\Project\gestion-CF\resources\views/users/_field.blade.php ENDPATH**/ ?>