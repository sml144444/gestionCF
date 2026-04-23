
<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag; ?>
<?php foreach($attributes->onlyProps(['label', 'value']) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
} ?>
<?php $attributes = $attributes->exceptProps(['label', 'value']); ?>
<?php foreach (array_filter((['label', 'value']), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
} ?>
<?php $__defined_vars = get_defined_vars(); ?>
<?php foreach ($attributes as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
} ?>
<?php unset($__defined_vars); ?>
 
<div class="flex items-start justify-between gap-3 py-2 border-b border-slate-50 last:border-0">
    <span class="text-xs font-semibold text-slate-400 whitespace-nowrap flex-shrink-0"><?php echo e($label); ?></span>
    <span class="text-xs text-slate-700 font-medium text-right break-all"><?php echo e($value); ?></span>
</div>
 <?php /**PATH C:\Project\gestion-CF\resources\views/components/profile-row.blade.php ENDPATH**/ ?>