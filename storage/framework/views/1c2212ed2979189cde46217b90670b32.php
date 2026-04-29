


<?php $__env->startSection('title', 'Espace Gestionnaire'); ?>
<?php $__env->startSection('page-title', 'Dashboard Gestionnaire'); ?>

<?php $__env->startSection('content'); ?>


<div class="bg-white rounded-2xl border-l-4 border-slate-500 px-6 py-5 mb-6 shadow-sm flex items-center justify-between">
    <div>
        <h2 class="text-lg font-semibold text-slate-800">Bonjour, <?php echo e(Auth::user()->name); ?> 👋</h2>
        <p class="text-sm text-slate-500 mt-0.5">Tableau de bord gestionnaire · <?php echo e(now()->isoFormat('dddd D MMMM')); ?></p>
    </div>
    <?php if(($stats['seances_brouillon'] ?? 0) > 0): ?>
    <a href="<?php echo e(route('emplois.index')); ?>"
       class="flex items-center gap-2 bg-amber-50 border border-amber-200 text-amber-700 rounded-xl px-4 py-2 text-xs font-semibold hover:bg-amber-100 transition-colors">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
        </svg>
        <?php echo e($stats['seances_brouillon']); ?> brouillon(s) à publier
    </a>
    <?php endif; ?>
</div>


<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">

    <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-sm hover:shadow-md transition-shadow">
        <div class="flex items-center justify-between mb-3">
            <div class="w-9 h-9 rounded-xl bg-blue-50 flex items-center justify-center">
                <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
            </div>
        </div>
        <p class="text-2xl font-bold text-slate-800"><?php echo e($stats['stagiaires'] ?? '—'); ?></p>
        <p class="text-xs text-slate-400 mt-0.5 uppercase tracking-wide">Stagiaires</p>
    </div>

    <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-sm hover:shadow-md transition-shadow">
        <div class="flex items-center justify-between mb-3">
            <div class="w-9 h-9 rounded-xl bg-slate-100 flex items-center justify-center">
                <svg class="w-5 h-5 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                </svg>
            </div>
        </div>
        <p class="text-2xl font-bold text-slate-800"><?php echo e($stats['groupes'] ?? '—'); ?></p>
        <p class="text-xs text-slate-400 mt-0.5 uppercase tracking-wide">Groupes</p>
    </div>

    <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-sm hover:shadow-md transition-shadow">
        <div class="flex items-center justify-between mb-3">
            <div class="w-9 h-9 rounded-xl bg-teal-50 flex items-center justify-center">
                <svg class="w-5 h-5 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
            </div>
        </div>
        <p class="text-2xl font-bold text-slate-800"><?php echo e($stats['seances_semaine'] ?? '—'); ?></p>
        <p class="text-xs text-slate-400 mt-0.5 uppercase tracking-wide">Séances cette semaine</p>
    </div>

    <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-sm hover:shadow-md transition-shadow">
        <div class="flex items-center justify-between mb-3">
            <div class="w-9 h-9 rounded-xl bg-amber-50 flex items-center justify-center">
                <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                </svg>
            </div>
            <?php if(($stats['edu_pending'] ?? 0) > 0): ?>
                <span class="text-[10px] font-semibold text-amber-700 bg-amber-100 px-2 py-0.5 rounded-full">Libre</span>
            <?php endif; ?>
        </div>
        <p class="text-2xl font-bold text-slate-800"><?php echo e($stats['edu_pending'] ?? '—'); ?></p>
        <p class="text-xs text-slate-400 mt-0.5 uppercase tracking-wide">Comptes EDU dispo</p>
    </div>

</div>


<div class="grid grid-cols-1 lg:grid-cols-3 gap-5">

    
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="px-5 py-4 border-b border-slate-100">
            <h3 class="text-sm font-semibold text-slate-700">Actions rapides</h3>
        </div>
        <div class="p-4 space-y-2">
            <?php $__currentLoopData = [
                ['Emploi du temps',    route('emplois.index'),    'M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z',   'text-teal-600 bg-teal-50 hover:bg-teal-100'],
                ['Import EDU',         route('edu-import.index'), 'M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12',                              'text-amber-600 bg-amber-50 hover:bg-amber-100'],
                ['Stagiaires',         route('stagiaire.index'),  'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z', 'text-blue-600 bg-blue-50 hover:bg-blue-100'],
                ['Groupes',            route('groupes.index'),    'M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10', 'text-slate-600 bg-slate-50 hover:bg-slate-100'],
                ['Modules',            route('modules.index'),    'M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253', 'text-violet-600 bg-violet-50 hover:bg-violet-100'],
                ['Salles',             route('salles.index'),     'M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4', 'text-slate-600 bg-slate-50 hover:bg-slate-100'],
            ]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as [$label, $route, $icon, $colorClass]): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <a href="<?php echo e($route); ?>"
               class="flex items-center gap-3 rounded-xl <?php echo e($colorClass); ?> px-4 py-2.5 transition-colors">
                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="<?php echo e($icon); ?>"/>
                </svg>
                <span class="text-xs font-semibold"><?php echo e($label); ?></span>
                <svg class="w-3.5 h-3.5 ml-auto opacity-40" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
            </a>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    </div>

    
    <div class="lg:col-span-2 grid grid-rows-2 gap-5">

        
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
            <div class="px-5 py-4 border-b border-slate-100 flex items-center justify-between">
                <h3 class="text-sm font-semibold text-slate-700">Réclamations en attente</h3>
                <a href="<?php echo e(route('reclamations.index')); ?>"
                   class="text-xs text-slate-400 hover:text-slate-600 flex items-center gap-1">
                    Voir tout
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </a>
            </div>
            <div class="p-5 flex items-center gap-5">
                <div class="w-14 h-14 rounded-2xl bg-orange-50 flex items-center justify-center flex-shrink-0">
                    <span class="text-2xl font-bold text-orange-600"><?php echo e($stats['reclamations_open'] ?? '—'); ?></span>
                </div>
                <div>
                    <p class="text-sm font-medium text-slate-700">
                        <?php echo e(($stats['reclamations_open'] ?? 0) === 0 ? 'Aucune réclamation en attente' : 'Réclamation(s) à traiter'); ?>

                    </p>
                    <p class="text-xs text-slate-400 mt-0.5">Consultez la liste pour répondre aux stagiaires</p>
                </div>
                <a href="<?php echo e(route('reclamations.index')); ?>"
                   class="ml-auto flex-shrink-0 bg-slate-800 hover:bg-slate-700 text-white text-xs font-semibold px-4 py-2 rounded-xl transition-colors">
                    Traiter
                </a>
            </div>
        </div>

        
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
            <div class="px-5 py-4 border-b border-slate-100 flex items-center justify-between">
                <h3 class="text-sm font-semibold text-slate-700">Reportations en attente</h3>
                <a href="<?php echo e(route('reportations.index')); ?>"
                   class="text-xs text-slate-400 hover:text-slate-600 flex items-center gap-1">
                    Voir tout
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </a>
            </div>
            <div class="p-5 flex items-center gap-5">
                <div class="w-14 h-14 rounded-2xl bg-blue-50 flex items-center justify-center flex-shrink-0">
                    <span class="text-2xl font-bold text-blue-600"><?php echo e($stats['reportations_open'] ?? '—'); ?></span>
                </div>
                <div>
                    <p class="text-sm font-medium text-slate-700">
                        <?php echo e(($stats['reportations_open'] ?? 0) === 0 ? 'Aucune reportation en attente' : 'Reportation(s) à valider'); ?>

                    </p>
                    <p class="text-xs text-slate-400 mt-0.5">Demandes de report de séance des formateurs</p>
                </div>
                <a href="<?php echo e(route('reportations.index')); ?>"
                   class="ml-auto flex-shrink-0 bg-slate-800 hover:bg-slate-700 text-white text-xs font-semibold px-4 py-2 rounded-xl transition-colors">
                    Traiter
                </a>
            </div>
        </div>

    </div>
</div>

<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\Project\gestion-CF\resources\views/gestionnaire/dashboard.blade.php ENDPATH**/ ?>