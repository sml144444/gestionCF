
<?php $__env->startSection('title', 'Espace Gestionnaire'); ?>
<?php $__env->startSection('page-title', 'Dashboard Gestionnaire'); ?>

<?php $__env->startSection('content'); ?>


<div class="relative overflow-hidden rounded-2xl mb-6 bg-[#1e293b]">
    <div class="absolute inset-0"
         style="background: linear-gradient(135deg, transparent 40%, rgba(255,255,255,0.04) 100%);"></div>
    <div class="absolute top-0 right-0 w-64 h-64 rounded-full opacity-5"
         style="background:white; transform: translate(30%, -30%);"></div>
    <div class="relative px-6 py-6 flex items-center justify-between">
        <div>
            <p class="text-slate-400 text-xs font-semibold uppercase tracking-widest mb-1">Gestionnaire</p>
            <h2 class="text-2xl font-bold text-white">Bonjour, <?php echo e(Auth::user()->name); ?> 👋</h2>
            <p class="text-slate-400 text-sm mt-1"><?php echo e(now()->isoFormat('dddd D MMMM YYYY')); ?></p>
        </div>
        <?php if(($stats['seances_brouillon'] ?? 0) > 0): ?>
        <a href="<?php echo e(route('emplois.index')); ?>"
           class="flex items-center gap-2 bg-amber-400 text-amber-900 rounded-xl px-4 py-2.5 text-xs font-bold hover:bg-amber-300 transition-colors shadow-lg">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
            </svg>
            <?php echo e($stats['seances_brouillon']); ?> à publier
        </a>
        <?php endif; ?>
    </div>
</div>


<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">

    <?php $__currentLoopData = [
        ['Stagiaires',          $stats['stagiaires']        ?? '—', '#1a4f8a', '#eff6ff', 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z'],
        ['Groupes',             $stats['groupes']           ?? '—', '#334155', '#f1f5f9', 'M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10'],
        ['Séances semaine',     $stats['seances_semaine']   ?? '—', '#0d766a', '#f0fdfa', 'M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z'],
        ['EDU disponibles',     $stats['edu_pending']       ?? '—', '#b45309', '#fffbeb', 'M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12'],
    ]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as [$label, $val, $color, $bg, $icon]): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
    <div class="bg-white rounded-2xl border border-slate-100 p-5 shadow-sm hover:shadow-md transition-all hover:-translate-y-0.5">
        <div class="flex items-start justify-between mb-4">
            <div class="w-10 h-10 rounded-xl flex items-center justify-center" style="background:<?php echo e($bg); ?>;">
                <svg class="w-5 h-5" fill="none" stroke="<?php echo e($color); ?>" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="<?php echo e($icon); ?>"/>
                </svg>
            </div>
        </div>
        <p class="text-3xl font-black text-slate-800 leading-none"><?php echo e($val); ?></p>
        <p class="text-[11px] text-slate-400 mt-1.5 uppercase tracking-widest font-medium"><?php echo e($label); ?></p>
    </div>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

</div>


<div class="grid grid-cols-1 lg:grid-cols-3 gap-5">

    
    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
        <div class="px-5 py-4 border-b border-slate-50 flex items-center gap-3">
            <div class="w-7 h-7 rounded-lg bg-slate-100 flex items-center justify-center">
                <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                </svg>
            </div>
            <h3 class="text-sm font-bold text-slate-700">Navigation rapide</h3>
        </div>
        <div class="p-4 space-y-2">
            <?php $__currentLoopData = [
                ['Emploi du temps', route('emplois.index'),    'M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z',   '#0d766a', '#f0fdfa'],
                ['Import EDU',      route('edu-import.index'), 'M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12',                              '#b45309', '#fffbeb'],
                ['Stagiaires',      route('stagiaire.index'),  'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z', '#1a4f8a', '#eff6ff'],
                ['Groupes',         route('groupes.index'),    'M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10', '#334155', '#f1f5f9'],
                ['Modules',         route('modules.index'),    'M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253', '#7c3aed', '#f5f3ff'],
                ['Salles',          route('salles.index'),     'M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4', '#334155', '#f8fafc'],
            ]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as [$label, $route, $icon, $color, $bg]): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <a href="<?php echo e($route); ?>"
               class="group flex items-center gap-3 rounded-xl px-4 py-2.5 transition-all hover:shadow-sm"
               style="background:<?php echo e($bg); ?>; color:<?php echo e($color); ?>;">
                <div class="w-8 h-8 rounded-lg bg-white shadow-sm flex items-center justify-center flex-shrink-0 group-hover:scale-110 transition-transform">
                    <svg class="w-4 h-4" fill="none" stroke="<?php echo e($color); ?>" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="<?php echo e($icon); ?>"/>
                    </svg>
                </div>
                <span class="text-xs font-bold"><?php echo e($label); ?></span>
                <svg class="w-3.5 h-3.5 ml-auto opacity-40" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
            </a>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    </div>

    
    <div class="lg:col-span-2 grid gap-5">

        <?php $__currentLoopData = [
            ['Réclamations en attente', $stats['reclamations_open'] ?? 0, route('reclamations.index'), '#dc2626', '#fff1f2', '#fecdd3', 'M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z', 'Consultez la liste pour répondre aux stagiaires'],
            ['Reportations en attente', $stats['reportations_open'] ?? 0, route('reportations.index'), '#1a4f8a', '#eff6ff',  '#bfdbfe', 'M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15', 'Demandes de report de séance des formateurs'],
        ]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as [$title, $count, $route, $color, $bg, $border, $icon, $desc]): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
            <div class="px-5 py-4 border-b border-slate-50 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-7 h-7 rounded-lg flex items-center justify-center" style="background:<?php echo e($bg); ?>;">
                        <svg class="w-4 h-4" fill="none" stroke="<?php echo e($color); ?>" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="<?php echo e($icon); ?>"/>
                        </svg>
                    </div>
                    <h3 class="text-sm font-bold text-slate-700"><?php echo e($title); ?></h3>
                </div>
                <a href="<?php echo e($route); ?>" class="text-xs text-slate-400 hover:text-slate-600 flex items-center gap-1">
                    Voir tout <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                </a>
            </div>
            <div class="p-5 flex items-center gap-5">
                <div class="w-16 h-16 rounded-2xl flex items-center justify-center flex-shrink-0 font-black text-2xl"
                     style="background:<?php echo e($bg); ?>; color:<?php echo e($color); ?>; border:2px solid <?php echo e($border); ?>;">
                    <?php echo e($count); ?>

                </div>
                <div class="flex-1">
                    <p class="text-sm font-bold text-slate-700">
                        <?php echo e($count === 0 ? 'Aucune en attente ✓' : $count.' à traiter'); ?>

                    </p>
                    <p class="text-xs text-slate-400 mt-0.5"><?php echo e($desc); ?></p>
                </div>
                <a href="<?php echo e($route); ?>"
                   class="flex-shrink-0 text-white text-xs font-bold px-5 py-2.5 rounded-xl transition-opacity hover:opacity-90"
                   style="background:<?php echo e($color); ?>;">
                    Traiter
                </a>
            </div>
        </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

    </div>
</div>

<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\Project\gestion-CF\resources\views/gestionnaire/dashboard.blade.php ENDPATH**/ ?>