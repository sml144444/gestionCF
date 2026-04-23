
<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag; ?>
<?php foreach($attributes->onlyProps(['route' => '#', 'icon', 'label', 'active' => false]) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
} ?>
<?php $attributes = $attributes->exceptProps(['route' => '#', 'icon', 'label', 'active' => false]); ?>
<?php foreach (array_filter((['route' => '#', 'icon', 'label', 'active' => false]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
} ?>
<?php $__defined_vars = get_defined_vars(); ?>
<?php foreach ($attributes as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
} ?>
<?php unset($__defined_vars); ?>

<a href="<?php echo e($route); ?>"
   class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition-all duration-150 group
          <?php echo e($active || request()->routeIs(str_replace('/', '.', ltrim($route, '/')))
             ? 'bg-white/20 text-white'
             : 'text-white/65 hover:bg-white/10 hover:text-white'); ?>">

    
    <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="<?php echo e($icon); ?>"/>
    </svg>

    
    <span class="text-sm whitespace-nowrap overflow-hidden transition-all duration-300 leading-none"
          x-bind:class="sidebarOpen ? 'opacity-100 w-auto' : 'opacity-0 w-0'">
        <?php echo e($label); ?>

    </span>
</a><?php /**PATH C:\Project\gestion-CF\resources\views/components/nav-item.blade.php ENDPATH**/ ?>