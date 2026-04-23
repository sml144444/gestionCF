


<?php $__env->startSection('title', 'Espace Admin'); ?>
<?php $__env->startSection('page-title', 'Dashboard Admin'); ?>

<?php $__env->startSection('content'); ?>


<div class="bg-gradient-to-r from-emerald-700 to-emerald-500 rounded-2xl px-6 py-5 mb-6 shadow-sm flex items-center justify-between">
    <div>
        <h2 class="text-lg font-semibold text-white">Bonjour, <?php echo e(Auth::user()->name); ?> 👋</h2>
        <p class="text-sm text-emerald-100 mt-0.5">Accès complet à la plateforme OFPPT</p>
    </div>
    <div class="hidden sm:flex items-center gap-2 bg-white/15 rounded-xl px-4 py-2">
        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
        </svg>
        <span class="text-sm text-white font-medium"><?php echo e(now()->isoFormat('dddd D MMMM YYYY')); ?></span>
    </div>
</div>


<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">

    
    <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-sm hover:shadow-md transition-shadow">
        <div class="flex items-center justify-between mb-3">
            <div class="w-9 h-9 rounded-xl bg-emerald-50 flex items-center justify-center">
                <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                </svg>
            </div>
            <span class="text-[10px] font-semibold text-emerald-600 bg-emerald-50 px-2 py-0.5 rounded-full">Total</span>
        </div>
        <p class="text-2xl font-bold text-slate-800"><?php echo e($stats['total_users'] ?? '—'); ?></p>
        <p class="text-xs text-slate-400 mt-0.5 uppercase tracking-wide">Utilisateurs</p>
    </div>

    
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
            <div class="w-9 h-9 rounded-xl bg-teal-50 flex items-center justify-center">
                <svg class="w-5 h-5 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                </svg>
            </div>
        </div>
        <p class="text-2xl font-bold text-slate-800"><?php echo e($stats['formateurs'] ?? '—'); ?></p>
        <p class="text-xs text-slate-400 mt-0.5 uppercase tracking-wide">Formateurs</p>
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
                <span class="text-[10px] font-semibold text-amber-700 bg-amber-100 px-2 py-0.5 rounded-full">En attente</span>
            <?php endif; ?>
        </div>
        <p class="text-2xl font-bold text-slate-800"><?php echo e($stats['edu_pending'] ?? '—'); ?></p>
        <p class="text-xs text-slate-400 mt-0.5 uppercase tracking-wide">Comptes EDU inutilisés</p>
    </div>

</div>


<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
    <?php $__currentLoopData = [
        ['Filières',     $stats['filieres']     ?? '—', 'text-emerald-700', 'bg-emerald-50'],
        ['Groupes',      $stats['groupes']      ?? '—', 'text-slate-700',   'bg-slate-50'],
        ['Gestionnaires',$stats['gestionnaires']?? '—', 'text-amber-700',   'bg-amber-50'],
        ['Réclamations', $stats['reclamations_open'] ?? '—', 'text-red-600', 'bg-red-50'],
    ]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as [$label, $val, $textColor, $bg]): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
    <div class="<?php echo e($bg); ?> rounded-2xl border border-slate-200 p-4 shadow-sm flex items-center justify-between">
        <div>
            <p class="text-xs text-slate-400 uppercase tracking-wide mb-1"><?php echo e($label); ?></p>
            <p class="text-xl font-bold <?php echo e($textColor); ?>"><?php echo e($val); ?></p>
        </div>
    </div>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
</div>


<div class="grid grid-cols-1 lg:grid-cols-3 gap-5">

    
    <div class="lg:col-span-2 bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="px-5 py-4 border-b border-slate-100">
            <h3 class="text-sm font-semibold text-slate-700">Actions rapides</h3>
        </div>
        <div class="p-5 grid grid-cols-2 sm:grid-cols-3 gap-3">
            <?php $__currentLoopData = [
                ['Gestion utilisateurs', route('users.management.index'),    'M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z',   'hover:border-emerald-300 hover:bg-emerald-50 hover:text-emerald-700'],
                ['Import EDU',           route('edu-import.index'),          'M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12',                                                  'hover:border-blue-300   hover:bg-blue-50   hover:text-blue-700'],
                ['Emploi du temps',      route('emplois.index'),             'M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z',                        'hover:border-teal-300   hover:bg-teal-50   hover:text-teal-700'],
                ['Filières',             route('filieres.index'),            'M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z', 'hover:border-violet-300 hover:bg-violet-50 hover:text-violet-700'],
                ['Groupes',              route('groupes.index'),             'M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10', 'hover:border-amber-300 hover:bg-amber-50 hover:text-amber-700'],
                ['Rôles & Permissions',  route('roles.index'),              'M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z', 'hover:border-slate-300 hover:bg-slate-50 hover:text-slate-700'],
            ]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as [$label, $route, $icon, $hoverClass]): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <a href="<?php echo e($route); ?>"
               class="flex flex-col items-center gap-2 rounded-xl border border-slate-200 p-4 text-center text-slate-500 transition-all <?php echo e($hoverClass); ?> group">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="<?php echo e($icon); ?>"/>
                </svg>
                <span class="text-xs font-medium leading-tight"><?php echo e($label); ?></span>
            </a>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    </div>

    
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="px-5 py-4 border-b border-slate-100">
            <h3 class="text-sm font-semibold text-slate-700">Système</h3>
        </div>
        <div class="p-5 space-y-3">
            <div class="flex items-center justify-between py-2 border-b border-slate-50">
                <span class="text-xs text-slate-400">Version Laravel</span>
                <span class="text-xs font-semibold text-slate-600"><?php echo e(app()->version()); ?></span>
            </div>
            <div class="flex items-center justify-between py-2 border-b border-slate-50">
                <span class="text-xs text-slate-400">Environnement</span>
                <span class="text-xs font-semibold <?php echo e(app()->isProduction() ? 'text-emerald-600' : 'text-amber-600'); ?>">
                    <?php echo e(ucfirst(app()->environment())); ?>

                </span>
            </div>
            <div class="flex items-center justify-between py-2 border-b border-slate-50">
                <span class="text-xs text-slate-400">PHP</span>
                <span class="text-xs font-semibold text-slate-600"><?php echo e(PHP_VERSION); ?></span>
            </div>
            <div class="flex items-center justify-between py-2">
                <span class="text-xs text-slate-400">Dernière connexion</span>
                <span class="text-xs font-semibold text-slate-600">Maintenant</span>
            </div>

            <a href="<?php echo e(route('news.index')); ?>"
               class="mt-2 flex items-center justify-center gap-2 w-full rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-semibold px-4 py-2.5 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"/>
                </svg>
                Gérer les news
            </a>
        </div>
    </div>

</div>

<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\Project\gestion-CF\resources\views/admin/dashboard.blade.php ENDPATH**/ ?>