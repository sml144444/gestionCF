
<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag; ?>
<?php foreach($attributes->onlyProps(['label']) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
} ?>
<?php $attributes = $attributes->exceptProps(['label']); ?>
<?php foreach (array_filter((['label']), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
} ?>
<?php $__defined_vars = get_defined_vars(); ?>
<?php foreach ($attributes as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
} ?>
<?php unset($__defined_vars); ?>

<div class="px-3 pt-4 pb-1 overflow-hidden transition-all duration-300"
     x-bind:class="sidebarOpen ? 'opacity-100' : 'opacity-0 h-0 py-0'">
    <p class="text-[9px] font-bold uppercase tracking-widest text-white/35"><?php echo e($label); ?></p>
</div><?php /**PATH C:\Project\gestion-CF\resources\views/components/nav-section.blade.php ENDPATH**/ ?>