
<?php $__env->startSection('title', 'Espace Formateur'); ?>
<?php $__env->startSection('page-title', 'Dashboard Formateur'); ?>

<?php $__env->startSection('content'); ?>


<div class="relative overflow-hidden rounded-2xl mb-6"
     style="background: linear-gradient(135deg, #1a4f8a 0%, #1a6fa8 60%, #2563eb 100%);">
    <div class="absolute inset-0"
         style="background-image: radial-gradient(circle at 85% 15%, rgba(255,255,255,0.12) 0%, transparent 50%);"></div>
    <div class="relative px-6 py-6 flex items-center justify-between">
        <div>
            <p class="text-blue-300 text-xs font-semibold uppercase tracking-widest mb-1">Formateur</p>
            <h2 class="text-2xl font-bold text-white">Bonjour, <?php echo e(Auth::user()->name); ?> 👋</h2>
            <p class="text-blue-200 text-sm mt-1">
                Spécialité :
                <span class="font-semibold text-white"><?php echo e(Auth::user()->specialite ?? '—'); ?></span>
            </p>
        </div>
        <a href="<?php echo e(route('emplois.index')); ?>"
           class="hidden sm:flex items-center gap-2 bg-white/15 hover:bg-white/25 rounded-xl px-4 py-2.5 text-sm text-white font-semibold transition-colors backdrop-blur-sm">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
            </svg>
            Mon emploi du temps
        </a>
    </div>
</div>


<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">

    <?php $__currentLoopData = [
        ['Séances semaine',   $stats['seances_semaine']      ?? '—', '#1a4f8a', '#eff6ff', 'M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z', 'Semaine'],
        ['Modules assignés',  $stats['modules_count']        ?? '—', '#0d766a', '#f0fdfa', 'M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253', null],
        ['Groupes',           $stats['groupes_count']        ?? '—', '#7c3aed', '#f5f3ff', 'M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10', null],
        ['Reportations',      $stats['reportations_pending'] ?? '—', '#ea580c', '#fff7ed', 'M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15', ($stats['reportations_pending'] ?? 0) > 0 ? 'En attente' : null],
    ]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as [$label, $val, $color, $bg, $icon, $badge]): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
    <div class="bg-white rounded-2xl border border-slate-100 p-5 shadow-sm hover:shadow-md transition-all hover:-translate-y-0.5">
        <div class="flex items-start justify-between mb-4">
            <div class="w-10 h-10 rounded-xl flex items-center justify-center" style="background:<?php echo e($bg); ?>;">
                <svg class="w-5 h-5" fill="none" stroke="<?php echo e($color); ?>" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="<?php echo e($icon); ?>"/>
                </svg>
            </div>
            <?php if($badge): ?>
                <span class="text-[9px] font-bold uppercase tracking-wide px-2 py-1 rounded-full"
                      style="background:<?php echo e($bg); ?>; color:<?php echo e($color); ?>;"><?php echo e($badge); ?></span>
            <?php endif; ?>
        </div>
        <p class="text-3xl font-black text-slate-800 leading-none"><?php echo e($val); ?></p>
        <p class="text-[11px] text-slate-400 mt-1.5 uppercase tracking-widest font-medium"><?php echo e($label); ?></p>
    </div>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

</div>


<?php if($current_seance): ?>
<div class="mb-5 rounded-2xl px-5 py-4 shadow-lg flex items-center gap-4"
     style="background: linear-gradient(135deg, #059669, #10b981);">
    <div class="relative flex-shrink-0">
        <div class="w-11 h-11 rounded-full bg-white/20 flex items-center justify-center">
            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M15 10l4.553-2.069A1 1 0 0121 8.82v6.361a1 1 0 01-1.447.894L15 14M3 8a2 2 0 012-2h8a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2V8z"/>
            </svg>
        </div>
        <span class="absolute -top-0.5 -right-0.5 flex h-3 w-3">
            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-white opacity-75"></span>
            <span class="relative inline-flex rounded-full h-3 w-3 bg-white"></span>
        </span>
    </div>
    <div class="flex-1 min-w-0">
        <p class="text-[10px] font-bold text-emerald-100 uppercase tracking-widest">Séance en cours</p>
        <p class="text-sm font-bold text-white truncate">
            <?php echo e($current_seance->module->name ?? 'Séance'); ?>

            <?php if($current_seance->groupe): ?> · <?php echo e($current_seance->groupe->name); ?> <?php endif; ?>
        </p>
        <p class="text-xs text-emerald-200 mt-0.5">
            <?php echo e($current_seance->date_debut->format('H:i')); ?> – <?php echo e($current_seance->date_fin->format('H:i')); ?>

            <?php if($current_seance->salle): ?> · <?php echo e($current_seance->salle->name); ?> <?php endif; ?>
        </p>
    </div>
    <?php $remaining = now()->diffInMinutes($current_seance->date_fin); ?>
    <div class="flex-shrink-0 text-right hidden sm:block">
        <p class="text-xl font-black text-white leading-none"><?php echo e(intdiv($remaining,60) > 0 ? intdiv($remaining,60).'h '.($remaining%60).'min' : ($remaining%60).'min'); ?></p>
        <p class="text-[10px] text-emerald-300 uppercase tracking-wide">restantes</p>
    </div>
    <a href="<?php echo e(route('seances.show', $current_seance)); ?>"
       class="flex-shrink-0 bg-white text-emerald-700 text-xs font-bold px-4 py-2 rounded-xl hover:bg-emerald-50 transition-colors">
        Ouvrir
    </a>
</div>

<?php elseif($next_seance): ?>
<div class="mb-5 rounded-2xl bg-white border-2 border-slate-100 px-5 py-4 shadow-sm flex items-center gap-4">
    <div class="w-11 h-11 rounded-xl flex items-center justify-center flex-shrink-0" style="background:#eff6ff;">
        <svg class="w-5 h-5" fill="none" stroke="#1a4f8a" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
    </div>
    <div class="flex-1 min-w-0">
        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Prochaine séance</p>
        <p class="text-sm font-bold text-slate-800 truncate">
            <?php echo e($next_seance->module->name ?? 'Séance'); ?>

            <?php if($next_seance->groupe): ?> · <?php echo e($next_seance->groupe->name); ?> <?php endif; ?>
        </p>
        <p class="text-xs text-slate-400 mt-0.5">
            <?php echo e($next_seance->date_debut->isoFormat('ddd D MMM')); ?>

            · <?php echo e($next_seance->date_debut->format('H:i')); ?> – <?php echo e($next_seance->date_fin->format('H:i')); ?>

            <?php if($next_seance->salle): ?> · <?php echo e($next_seance->salle->name); ?> <?php endif; ?>
        </p>
    </div>
    <?php
        $diffMins  = now()->diffInMinutes($next_seance->date_debut);
        $diffHours = now()->diffInHours($next_seance->date_debut);
        $diffDays  = now()->diffInDays($next_seance->date_debut);
        [$cVal, $cUnit, $cColor] = $diffMins < 60
            ? [$diffMins, 'min', '#ea580c']
            : ($diffHours < 24 ? [$diffHours, 'h', '#1a4f8a'] : [$diffDays, $diffDays>1?'jours':'jour', '#475569']);
    ?>
    <div class="flex-shrink-0 text-right hidden sm:block">
        <p class="text-lg font-black leading-none" style="color:<?php echo e($cColor); ?>;">dans <?php echo e($cVal); ?><?php echo e($cUnit); ?></p>
        <p class="text-[10px] text-slate-400 uppercase tracking-wide"><?php echo e($next_seance->date_debut->format('H:i')); ?></p>
    </div>
    <a href="<?php echo e(route('seances.show', $next_seance)); ?>"
       class="flex-shrink-0 text-white text-xs font-bold px-4 py-2 rounded-xl hover:opacity-90 transition-opacity"
       style="background:#1a4f8a;">Détails</a>
</div>
<?php endif; ?>


<div class="grid grid-cols-1 lg:grid-cols-3 gap-5">

    <div class="lg:col-span-2 bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
        <div class="px-5 py-4 border-b border-slate-50 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-7 h-7 rounded-lg flex items-center justify-center" style="background:#eff6ff;">
                    <svg class="w-4 h-4" fill="none" stroke="#1a4f8a" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                </div>
                <h3 class="text-sm font-bold text-slate-700">Prochaines séances</h3>
            </div>
            <a href="<?php echo e(route('emplois.index')); ?>" class="text-xs text-slate-400 hover:text-slate-600 flex items-center gap-1">
                Voir tout <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            </a>
        </div>

        <?php if(isset($prochaines_seances) && $prochaines_seances->isNotEmpty()): ?>
        <div class="divide-y divide-slate-50">
            <?php $__currentLoopData = $prochaines_seances; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $seance): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <?php
                $isToday = $seance->date_debut->isToday();
                $isNow   = $seance->date_debut->lte(now()) && $seance->date_fin->gte(now());
            ?>
            <a href="<?php echo e(route('seances.show', $seance)); ?>"
               class="flex items-center gap-4 px-5 py-3.5 hover:bg-slate-50 transition-colors group">
                <div class="w-11 text-center flex-shrink-0">
                    <p class="text-[9px] text-slate-400 uppercase font-semibold"><?php echo e($seance->date_debut->isoFormat('ddd')); ?></p>
                    <p class="text-xl font-black leading-none mt-0.5" style="<?php echo e($isToday ? 'color:#1a4f8a;' : 'color:#1e293b;'); ?>">
                        <?php echo e($seance->date_debut->format('d')); ?>

                    </p>
                    <?php if($isToday): ?><div class="w-1 h-1 rounded-full mx-auto mt-1" style="background:#1a4f8a;"></div><?php endif; ?>
                </div>
                <div class="w-px h-10 bg-slate-100 flex-shrink-0"></div>
                <div class="flex-1 min-w-0">
                    <div class="flex items-center gap-2 flex-wrap">
                        <p class="text-sm font-bold text-slate-800 truncate"><?php echo e($seance->module->name ?? 'Module'); ?></p>
                        <?php if($isNow): ?>
                            <span class="text-[9px] font-bold bg-emerald-500 text-white px-2 py-0.5 rounded-full animate-pulse">EN COURS</span>
                        <?php elseif($isToday): ?>
                            <span class="text-[9px] font-bold text-white px-2 py-0.5 rounded-full" style="background:#1a4f8a;">AUJOURD'HUI</span>
                        <?php endif; ?>
                    </div>
                    <p class="text-xs text-slate-400 mt-0.5">
                        <?php echo e($seance->groupe->name ?? ''); ?>

                        <?php if($seance->salle): ?> · <?php echo e($seance->salle->name); ?> <?php endif; ?>
                        <?php if($seance->mode === 'distance'): ?> · 🌐 Distance <?php endif; ?>
                    </p>
                </div>
                <div class="text-right flex-shrink-0">
                    <p class="text-xs font-bold text-slate-700"><?php echo e($seance->date_debut->format('H:i')); ?></p>
                    <p class="text-[10px] text-slate-400"><?php echo e($seance->date_fin->format('H:i')); ?></p>
                </div>
                <?php if($isToday && !$isNow): ?>
                <?php $mins = now()->diffInMinutes($seance->date_debut); ?>
                <div class="flex-shrink-0 hidden sm:block">
                    <p class="text-xs font-bold" style="<?php echo e($mins < 30 ? 'color:#ea580c;' : 'color:#94a3b8;'); ?>">
                        <?php echo e(intdiv($mins,60) > 0 ? 'dans '.intdiv($mins,60).'h'.($mins%60) : 'dans '.$mins.'min'); ?>

                    </p>
                </div>
                <?php else: ?>
                <span class="text-[9px] font-bold px-2 py-0.5 rounded-full flex-shrink-0"
                      style="<?php echo e($seance->statut === 'actif' ? 'background:#dcfce7; color:#166534;' : 'background:#fef3c7; color:#92400e;'); ?>">
                    <?php echo e($seance->statut === 'actif' ? 'Actif' : 'Brouillon'); ?>

                </span>
                <?php endif; ?>
            </a>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
        <?php else: ?>
        <div class="py-14 text-center">
            <div class="w-14 h-14 rounded-2xl mx-auto mb-3 flex items-center justify-center bg-slate-50">
                <svg class="w-7 h-7 text-slate-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                          d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
            </div>
            <p class="text-xs font-semibold text-slate-400">Aucune séance à venir</p>
        </div>
        <?php endif; ?>
    </div>

    <div class="flex flex-col gap-5">
        
        <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
            <div class="px-5 py-4 border-b border-slate-50">
                <h3 class="text-sm font-bold text-slate-700">Actions rapides</h3>
            </div>
            <div class="p-4 space-y-2">
                <?php $__currentLoopData = [
                    ['Emploi du temps',  route('emplois.index'),     'M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z',   '#1a4f8a', '#eff6ff'],
                    ['Mes reportations', route('reportations.my'),   'M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15', '#ea580c', '#fff7ed'],
                    ['Absences',         route('absences.index'),    'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z',                                                '#dc2626', '#fff1f2'],
                    ['Contrôles',        route('controles.index'),   'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2', '#0d766a', '#f0fdfa'],
                    ['News',             route('news.index'),        'M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z', '#475569', '#f8fafc'],
                ]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as [$label, $route, $icon, $color, $bg]): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <a href="<?php echo e($route); ?>"
                   class="group flex items-center gap-3 rounded-xl px-4 py-2.5 transition-all hover:shadow-sm"
                   style="background:<?php echo e($bg); ?>; color:<?php echo e($color); ?>;">
                    <div class="w-7 h-7 rounded-lg bg-white shadow-sm flex items-center justify-center flex-shrink-0 group-hover:scale-110 transition-transform">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="<?php echo e($color); ?>" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="<?php echo e($icon); ?>"/>
                        </svg>
                    </div>
                    <span class="text-xs font-bold"><?php echo e($label); ?></span>
                    <svg class="w-3 h-3 ml-auto opacity-40" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </a>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        </div>

        
        <div class="rounded-2xl p-5 text-white" style="background: linear-gradient(135deg, #1a4f8a, #2563eb);">
            <div class="flex items-center gap-3 mb-3">
                <div class="w-10 h-10 rounded-xl bg-white/20 flex items-center justify-center text-sm font-black">
                    <?php echo e(strtoupper(substr(Auth::user()->name, 0, 1))); ?>

                </div>
                <div class="min-w-0">
                    <p class="text-xs font-bold text-white truncate"><?php echo e(Auth::user()->name); ?></p>
                    <p class="text-[10px] text-blue-200 truncate"><?php echo e(Auth::user()->email); ?></p>
                </div>
            </div>
            <?php if(Auth::user()->specialite): ?>
            <p class="text-xs text-blue-200 mb-3"><?php echo e(Auth::user()->specialite); ?></p>
            <?php endif; ?>
            <a href="<?php echo e(route('profile.show')); ?>"
               class="flex items-center justify-center gap-2 w-full bg-white/15 hover:bg-white/25 rounded-xl py-2 text-xs font-bold text-white transition-colors">
                Mon profil
                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
            </a>
        </div>
    </div>
</div>

<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\Project\gestion-CF\resources\views/formateur/dashboard.blade.php ENDPATH**/ ?>