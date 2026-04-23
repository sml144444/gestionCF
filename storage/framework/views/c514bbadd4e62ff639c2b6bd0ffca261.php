


<?php $__env->startSection('title', 'Espace Stagiaire'); ?>
<?php $__env->startSection('page-title', 'Dashboard'); ?>

<?php $__env->startSection('content'); ?>

<?php
    $user    = Auth::user();
    $groupe  = $user->groupe ?? null;
    $filiere = $groupe?->filiere ?? null;
?>


<div class="bg-white rounded-2xl border border-slate-200 px-6 py-5 mb-6 shadow-sm">
    <div class="flex items-start justify-between">
        <div>
            <h2 class="text-lg font-semibold text-slate-800">Bonjour, <?php echo e($user->name); ?> 👋</h2>
            <p class="text-sm text-slate-500 mt-1 flex flex-wrap items-center gap-x-3 gap-y-1">
                <?php if($filiere): ?>
                    <span class="inline-flex items-center gap-1">
                        <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6z"/>
                        </svg>
                        <span class="font-medium text-slate-700"><?php echo e($filiere->name); ?></span>
                    </span>
                <?php endif; ?>
                <?php if($groupe): ?>
                    <span class="inline-flex items-center gap-1">
                        <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857"/>
                        </svg>
                        <span class="font-medium text-slate-700"><?php echo e($groupe->name); ?></span>
                    </span>
                    <span class="text-[10px] font-semibold bg-blue-100 text-blue-700 px-2 py-0.5 rounded-full">
                        <?php echo e($groupe->annee); ?>ème Année
                    </span>
                <?php endif; ?>
            </p>
        </div>
        <span class="text-xs text-slate-400 hidden sm:block"><?php echo e(now()->isoFormat('dddd D MMMM')); ?></span>
    </div>
</div>


<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">

    <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-sm hover:shadow-md transition-shadow">
        <div class="flex items-center justify-between mb-3">
            <div class="w-9 h-9 rounded-xl bg-blue-50 flex items-center justify-center">
                <svg class="w-5 h-5 text-[#1a5fa8]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
            </div>
            <span class="text-[10px] font-semibold text-blue-600 bg-blue-50 px-2 py-0.5 rounded-full">Semaine</span>
        </div>
        <p class="text-2xl font-bold text-slate-800"><?php echo e($stats['cours_semaine'] ?? '—'); ?></p>
        <p class="text-xs text-slate-400 mt-0.5 uppercase tracking-wide">Cours à venir</p>
    </div>

    <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-sm hover:shadow-md transition-shadow">
        <div class="flex items-center justify-between mb-3">
            <div class="w-9 h-9 rounded-xl bg-red-50 flex items-center justify-center">
                <svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <?php if(($stats['absences_injust'] ?? 0) > 0): ?>
                <span class="text-[10px] font-semibold text-red-600 bg-red-100 px-2 py-0.5 rounded-full">Non justif.</span>
            <?php endif; ?>
        </div>
        <p class="text-2xl font-bold text-slate-800"><?php echo e($stats['absences_count'] ?? '—'); ?></p>
        <p class="text-xs text-slate-400 mt-0.5 uppercase tracking-wide">Absences</p>
    </div>

    <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-sm hover:shadow-md transition-shadow">
        <div class="flex items-center justify-between mb-3">
            <div class="w-9 h-9 rounded-xl bg-amber-50 flex items-center justify-center">
                <svg class="w-5 h-5 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
        </div>
        <p class="text-2xl font-bold text-slate-800"><?php echo e($stats['retards_count'] ?? '—'); ?></p>
        <p class="text-xs text-slate-400 mt-0.5 uppercase tracking-wide">Retards</p>
    </div>

    <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-sm hover:shadow-md transition-shadow">
        <div class="flex items-center justify-between mb-3">
            <div class="w-9 h-9 rounded-xl bg-emerald-50 flex items-center justify-center">
                <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                </svg>
            </div>
        </div>
        <p class="text-2xl font-bold text-slate-800">—</p>
        <p class="text-xs text-slate-400 mt-0.5 uppercase tracking-wide">Notes disponibles</p>
    </div>

</div>


<div class="grid grid-cols-1 lg:grid-cols-3 gap-5">

    
    <div class="lg:col-span-2 bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="px-5 py-4 border-b border-slate-100 flex items-center justify-between">
            <h3 class="text-sm font-semibold text-slate-700">Programme de la semaine</h3>
            <a href="<?php echo e(route('emplois.index')); ?>"
               class="text-xs text-slate-400 hover:text-slate-600 flex items-center gap-1">
                Voir tout
                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
            </a>
        </div>

        <?php if(isset($prochaines_seances) && $prochaines_seances->isNotEmpty()): ?>
            <div class="divide-y divide-slate-50">
                <?php $__currentLoopData = $prochaines_seances; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $seance): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php
                    $isPast    = $seance->date_debut->isPast();
                    $isToday   = $seance->date_debut->isToday();
                ?>
                <a href="<?php echo e(route('seances.show', $seance)); ?>"
                   class="flex items-center gap-4 px-5 py-3.5 hover:bg-slate-50 transition-colors <?php echo e($isPast ? 'opacity-50' : ''); ?>">
                    
                    <div class="w-10 text-center flex-shrink-0">
                        <p class="text-[10px] text-slate-400 uppercase"><?php echo e($seance->date_debut->isoFormat('ddd')); ?></p>
                        <p class="text-lg font-bold <?php echo e($isToday ? 'text-[#1a5fa8]' : 'text-slate-700'); ?> leading-none">
                            <?php echo e($seance->date_debut->format('d')); ?>

                        </p>
                        <?php if($isToday): ?>
                            <div class="w-1 h-1 rounded-full bg-[#1a5fa8] mx-auto mt-0.5"></div>
                        <?php endif; ?>
                    </div>
                    
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center gap-2">
                            <p class="text-sm font-semibold text-slate-800 truncate">
                                <?php echo e($seance->module->name ?? 'Séance'); ?>

                            </p>
                            <?php if($isToday): ?>
                                <span class="text-[10px] font-semibold bg-[#1a5fa8] text-white px-2 py-0.5 rounded-full flex-shrink-0">Aujourd'hui</span>
                            <?php endif; ?>
                        </div>
                        <p class="text-xs text-slate-400">
                            <?php echo e($seance->date_debut->format('H:i')); ?> – <?php echo e($seance->date_fin->format('H:i')); ?>

                            <?php if($seance->salle): ?> · <?php echo e($seance->salle->name); ?> <?php endif; ?>
                            <?php if($seance->mode === 'distance'): ?>
                                <span class="inline-flex items-center gap-1 text-blue-500">
                                    · 🌐 Distance
                                </span>
                            <?php endif; ?>
                        </p>
                    </div>
                    
                    <?php
                        $formateur = $seance->remplacant ?? $seance->gestionnaire;
                    ?>
                    <?php if($formateur): ?>
                    <div class="hidden sm:flex items-center gap-1.5 flex-shrink-0">
                        <div class="w-6 h-6 rounded-full bg-[#1a4f8a] flex items-center justify-center text-[9px] font-bold text-white">
                            <?php echo e(strtoupper(substr($formateur->name, 0, 1))); ?>

                        </div>
                        <span class="text-[11px] text-slate-400 max-w-[80px] truncate"><?php echo e($formateur->name); ?></span>
                    </div>
                    <?php endif; ?>
                </a>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        <?php else: ?>
            <div class="py-12 text-center">
                <svg class="w-10 h-10 text-slate-200 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                          d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
                <p class="text-xs text-slate-400">Aucun cours programmé cette semaine</p>
                <p class="text-[11px] text-slate-300 mt-1">L'emploi du temps sera disponible prochainement</p>
            </div>
        <?php endif; ?>
    </div>

    
    <div class="flex flex-col gap-5">

        
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
            <div class="px-5 py-4 border-b border-slate-100">
                <h3 class="text-sm font-semibold text-slate-700">Accès rapide</h3>
            </div>
            <div class="p-4 space-y-2">
                <?php $__currentLoopData = [
                    ['Emploi du temps',    route('emplois.index'),  'M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z',   'text-blue-600 bg-blue-50 hover:bg-blue-100'],
                    ['Mes absences',       route('absences.index'), 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z',                                                'text-red-500 bg-red-50 hover:bg-red-100'],
                    ['Mes réclamations',   route('reclamations.index'),'M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z', 'text-slate-600 bg-slate-50 hover:bg-slate-100'],
                    ['News / Événements',  route('news.index'),    'M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z', 'text-slate-600 bg-slate-50 hover:bg-slate-100'],
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

        
        <?php if(isset($derniers_documents) && $derniers_documents->isNotEmpty()): ?>
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
            <div class="px-5 py-4 border-b border-slate-100">
                <h3 class="text-sm font-semibold text-slate-700">Documents récents</h3>
            </div>
            <div class="divide-y divide-slate-50">
                <?php $__currentLoopData = $derniers_documents; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $doc): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="flex items-center gap-3 px-4 py-3">
                    <div class="w-8 h-8 rounded-lg flex items-center justify-center flex-shrink-0
                        <?php echo e($doc->lien ? 'bg-blue-50' : 'bg-red-50'); ?>">
                        <?php if($doc->lien): ?>
                            <svg class="w-4 h-4 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101"/>
                            </svg>
                        <?php else: ?>
                            <svg class="w-4 h-4 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                            </svg>
                        <?php endif; ?>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-xs font-semibold text-slate-700 truncate"><?php echo e($doc->titre); ?></p>
                        <p class="text-[10px] text-slate-400"><?php echo e($doc->created_at->diffForHumans()); ?></p>
                    </div>
                </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        </div>
        <?php endif; ?>

    </div>
</div>

<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\Project\gestion-CF\resources\views/stagiaire/dashboard.blade.php ENDPATH**/ ?>