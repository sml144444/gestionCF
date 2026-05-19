
<?php $__env->startSection('title', $user->name); ?>
<?php $__env->startSection('page-title', match($user->role) {
    'formateur'    => 'Profil formateur',
    'gestionnaire' => 'Profil gestionnaire',
    default        => 'Profil stagiaire',
}); ?>

<?php $__env->startSection('content'); ?>
<?php
    $isStagiaire = $user->role === 'stagiaire';
    $isFormateur = $user->role === 'formateur';

    $rc = match($user->role) {
        'formateur' => [
            'hex'    => '#1a4f8a',
            'bg'     => 'bg-[#1a4f8a]',
            'light'  => 'bg-blue-50',
            'text'   => 'text-[#1a4f8a]',
            'border' => 'border-blue-100',
            'badge'  => 'bg-blue-100 text-[#1a4f8a]',
            'ring'   => 'ring-[#1a4f8a]',
            'label'  => 'Formateur',
            'icon'   => '🎓',
            'shape'  => 'rounded-full',
            'grad'   => 'from-[#1a4f8a] via-blue-500 to-sky-400',
        ],
        'gestionnaire' => [
            'hex'    => '#334155',
            'bg'     => 'bg-slate-700',
            'light'  => 'bg-slate-100',
            'text'   => 'text-slate-700',
            'border' => 'border-slate-200',
            'badge'  => 'bg-slate-100 text-slate-700',
            'ring'   => 'ring-slate-500',
            'label'  => 'Gestionnaire',
            'icon'   => '🏢',
            'shape'  => 'rounded-2xl',
            'grad'   => 'from-slate-700 via-slate-500 to-slate-400',
        ],
        default => [
            'hex'    => '#1a4f8a',
            'bg'     => 'bg-[#1a4f8a]',
            'light'  => 'bg-blue-50',
            'text'   => 'text-[#1a4f8a]',
            'border' => 'border-blue-100',
            'badge'  => 'bg-blue-100 text-[#1a4f8a]',
            'ring'   => 'ring-[#1a4f8a]',
            'label'  => 'Stagiaire',
            'icon'   => '🎒',
            'shape'  => 'rounded-full',
            'grad'   => 'from-[#1a4f8a] via-blue-500 to-sky-400',
        ],
    };

    $isCircle = $rc['shape'] === 'rounded-full';

    // Stats — ✅ FIXED: was 'justifiee' (typo), now 'justifie'
    $absCount     = $isStagiaire ? $user->absences->count() : null;
    $absJust      = $isStagiaire ? $user->absences->where('justifie', true)->count()  : null;
    $absUnjust    = $isStagiaire ? $user->absences->where('justifie', false)->count() : null;
    $modCount     = $isFormateur ? $user->modules->count() : null;
    $daysMember   = $user->created_at->diffInDays(now());
?>


<div x-data="{ photoOpen: false }" @keydown.escape.window="photoOpen = false">


<div class="max-w-5xl mx-auto mb-4">
    <a href="<?php echo e(url()->previous()); ?>"
       class="inline-flex items-center gap-1.5 text-xs font-semibold text-slate-500
              hover:text-slate-800 transition-colors group">
        <svg class="w-3.5 h-3.5 transition-transform group-hover:-translate-x-0.5"
             fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"/>
        </svg>
        Retour
    </a>
</div>

<div class="max-w-5xl mx-auto space-y-4">

    
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">

        
        <div class="h-28 relative overflow-hidden <?php echo e($rc['bg']); ?>">
            
            <svg class="absolute inset-0 w-full h-full opacity-10" xmlns="http://www.w3.org/2000/svg">
                <defs>
                    <pattern id="heroPattern" x="0" y="0" width="40" height="40" patternUnits="userSpaceOnUse">
                        <circle cx="20" cy="20" r="1.5" fill="white"/>
                        <path d="M0 0 L40 40 M40 0 L0 40" stroke="white" stroke-width="0.5"/>
                    </pattern>
                </defs>
                <rect width="100%" height="100%" fill="url(#heroPattern)"/>
            </svg>
            
            <div class="absolute -top-8 -right-8 w-40 h-40 rounded-full bg-white/10"></div>
            <div class="absolute -bottom-10 -left-10 w-32 h-32 rounded-full bg-white/5"></div>
        </div>

        <div class="px-6 pb-5">
            
            <div class="flex -mt-10 mb-4">

                
                <?php if($user->photo): ?>
                <button type="button" @click="photoOpen = true"
                        class="relative group focus:outline-none flex-shrink-0"
                        title="Voir la photo en grand">
                    
                    <span class="absolute inset-0 bg-gradient-to-tr <?php echo e($rc['grad']); ?>

                                 opacity-90 group-hover:opacity-100 group-hover:scale-105
                                 transition-all duration-300 -m-[2.5px]
                                 <?php echo e($isCircle ? 'rounded-full' : 'rounded-[20px]'); ?>"></span>
                    
                    <span class="absolute inset-0 bg-white -m-[1px] z-[1]
                                 <?php echo e($isCircle ? 'rounded-full' : 'rounded-[18px]'); ?>"></span>
                    
                    <span class="relative z-[2] block w-20 h-20 <?php echo e($rc['shape']); ?> overflow-hidden shadow-md">
                        <img src="<?php echo e(asset('storage/' . $user->photo)); ?>" alt="<?php echo e($user->name); ?>"
                             class="w-full h-full object-cover transition-transform duration-300
                                    group-hover:scale-110">
                    </span>
                    
                    <span class="absolute inset-0 z-[3] <?php echo e($isCircle ? 'rounded-full' : 'rounded-2xl'); ?>

                                 bg-black/30 opacity-0 group-hover:opacity-100
                                 flex items-center justify-center transition-opacity duration-200">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v3m0 0v3m0-3h3m-3 0H7"/>
                        </svg>
                    </span>
                </button>
                <?php else: ?>
                <div class="w-20 h-20 <?php echo e($rc['shape']); ?> <?php echo e($rc['bg']); ?> flex items-center justify-center
                            text-white text-2xl font-bold flex-shrink-0 shadow-md
                            border-[3px] border-white z-10 relative">
                    <?php echo e(strtoupper(substr($user->name,0,1))); ?><?php echo e(strtoupper(substr(explode(' ',$user->name)[1]??'',0,1))); ?>

                </div>
                <?php endif; ?>

            </div>

            
            <div class="mb-4">
                <h2 class="text-xl font-bold text-slate-900 leading-tight"><?php echo e($user->name); ?></h2>
                <div class="flex flex-wrap items-center gap-2 mt-2">
                    <span class="inline-flex items-center gap-1 text-xs font-semibold px-2.5 py-1
                                 rounded-full border <?php echo e($rc['badge']); ?> <?php echo e($rc['border']); ?>">
                        <?php echo e($rc['icon']); ?> <?php echo e($rc['label']); ?>

                    </span>
                    <?php if($isStagiaire && $user->filiere): ?>
                    <span class="text-xs font-medium px-2.5 py-1 rounded-full
                                 bg-blue-50 text-blue-700 border border-blue-100">
                        <?php echo e($user->filiere->name); ?>

                    </span>
                    <?php endif; ?>
                    <?php if($isStagiaire && $user->groupe): ?>
                    <span class="text-xs font-medium px-2.5 py-1 rounded-full
                                 bg-slate-100 text-slate-600 border border-slate-200">
                        <?php echo e($user->groupe->name); ?>

                    </span>
                    <span class="text-xs font-medium px-2.5 py-1 rounded-full
                                 bg-emerald-50 text-emerald-700 border border-emerald-100">
                        Promo <?php echo e($user->groupe->promo_label ?? '—'); ?>

                    </span>
                    <?php endif; ?>
                    <?php if(!$isStagiaire && $user->matricule_formateur): ?>
                    <span class="text-xs font-mono font-medium px-2.5 py-1 rounded-full
                                 bg-slate-100 text-slate-500 border border-slate-200">
                        <?php echo e($user->matricule_formateur); ?>

                    </span>
                    <?php endif; ?>
                </div>
                <p class="text-sm text-slate-400 mt-1.5 flex items-center gap-1.5">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                    </svg>
                    <?php echo e($user->email); ?>

                </p>
            </div>

            
            <div class="grid grid-cols-3 gap-3 pt-3 border-t border-slate-100">
                
                <div class="text-center">
                    <p class="text-lg font-bold text-slate-800"><?php echo e($daysMember); ?></p>
                    <p class="text-[10px] font-semibold text-slate-400 uppercase tracking-wider mt-0.5">Jours membre</p>
                </div>

                <?php if($isStagiaire): ?>
                
                <div class="text-center border-x border-slate-100">
                    <p class="text-lg font-bold <?php echo e($absCount > 0 ? 'text-red-500' : 'text-emerald-500'); ?>">
                        <?php echo e($absCount); ?>

                    </p>
                    <p class="text-[10px] font-semibold text-slate-400 uppercase tracking-wider mt-0.5">Absences</p>
                </div>
                
                <div class="text-center">
                    <p class="text-lg font-bold <?php echo e($absUnjust > 0 ? 'text-amber-500' : 'text-emerald-500'); ?>">
                        <?php echo e($absUnjust); ?>

                    </p>
                    <p class="text-[10px] font-semibold text-slate-400 uppercase tracking-wider mt-0.5">Non justifiées</p>
                </div>
                <?php elseif($isFormateur): ?>
                
                <div class="text-center border-x border-slate-100">
                    <p class="text-lg font-bold <?php echo e($rc['text']); ?>"><?php echo e($modCount); ?></p>
                    <p class="text-[10px] font-semibold text-slate-400 uppercase tracking-wider mt-0.5">Modules</p>
                </div>
                
                <div class="text-center">
                    <p class="text-lg font-bold text-slate-800">
                        <?php echo e($user->nbr_heure_limit ? $user->nbr_heure_limit . 'h' : '—'); ?>

                    </p>
                    <p class="text-[10px] font-semibold text-slate-400 uppercase tracking-wider mt-0.5">Limite h/sem</p>
                </div>
                <?php else: ?>
                <div class="text-center border-x border-slate-100">
                    <p class="text-lg font-bold text-slate-800">—</p>
                    <p class="text-[10px] font-semibold text-slate-400 uppercase tracking-wider mt-0.5">Rôle</p>
                </div>
                <div class="text-center">
                    <p class="text-lg font-bold text-slate-800">
                        <?php echo e($user->created_at->format('Y')); ?>

                    </p>
                    <p class="text-[10px] font-semibold text-slate-400 uppercase tracking-wider mt-0.5">Depuis</p>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

        
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
            
            <div class="px-5 py-3.5 border-b border-slate-100 flex items-center gap-2.5">
                <span class="w-7 h-7 rounded-lg <?php echo e($rc['light']); ?> <?php echo e($rc['text']); ?>

                             flex items-center justify-center flex-shrink-0">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                </span>
                <h3 class="text-sm font-bold text-slate-700">Informations personnelles</h3>
            </div>
            
            <div class="divide-y divide-slate-50">
                <?php
                    $fields = [
                        ['label' => 'Nom complet',       'value' => $user->name,                                  'icon' => 'M5.121 17.804A13.937 13.937 0 0112 16c2.5 0 4.847.655 6.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0z'],
                        ['label' => 'Email',              'value' => $user->email,                                 'icon' => 'M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z'],
                        ['label' => 'Téléphone',          'value' => $user->phone ?? '—',                          'icon' => 'M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z'],
                        ['label' => 'CIN',                'value' => $user->cin ?? '—',                            'icon' => 'M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2'],
                        ['label' => 'Date de naissance',  'value' => $user->date_naissance?->format('d/m/Y') ?? '—','icon' => 'M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z'],
                        ['label' => 'Membre depuis',      'value' => $user->created_at->format('d/m/Y'),           'icon' => 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z'],
                    ];
                ?>
                <?php $__currentLoopData = $fields; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $field): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="flex items-center gap-3 px-5 py-3">
                    <svg class="w-4 h-4 flex-shrink-0 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="<?php echo e($field['icon']); ?>"/>
                    </svg>
                    <div class="flex-1 flex items-center justify-between gap-4 min-w-0">
                        <span class="text-xs text-slate-400 font-medium flex-shrink-0"><?php echo e($field['label']); ?></span>
                        <span class="text-sm font-semibold text-slate-700 truncate text-right"><?php echo e($field['value']); ?></span>
                    </div>
                </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        </div>

        
        <?php if($isStagiaire): ?>
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
            <div class="px-5 py-3.5 border-b border-slate-100 flex items-center gap-2.5">
                <span class="w-7 h-7 rounded-lg <?php echo e($rc['light']); ?> <?php echo e($rc['text']); ?>

                             flex items-center justify-center flex-shrink-0">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M12 14l9-5-9-5-9 5 9 5z M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952
                                 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"/>
                    </svg>
                </span>
                <h3 class="text-sm font-bold text-slate-700">Scolarité</h3>
            </div>
            <div class="divide-y divide-slate-50">
                <?php
                    $scolFields = [
                        ['label' => 'Filière',   'value' => $user->filiere?->name ?? '—'],
                        ['label' => 'Groupe',    'value' => $user->groupe?->name ?? '—'],
                        ['label' => 'Année',     'value' => $user->groupe?->annee ? 'Année ' . $user->groupe->annee : '—'],
                        ['label' => 'Promotion', 'value' => $user->groupe?->promo_label ?? '—'],
                    ];
                    if($user->groupe?->nbr_limit) {
                        $scolFields[] = ['label' => 'Capacité', 'value' => $user->groupe->stagiaires()->count() . ' / ' . $user->groupe->nbr_limit];
                    }
                ?>
                <?php $__currentLoopData = $scolFields; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $f): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="flex items-center justify-between px-5 py-3">
                    <span class="text-xs text-slate-400 font-medium"><?php echo e($f['label']); ?></span>
                    <span class="text-sm font-semibold text-slate-700"><?php echo e($f['value']); ?></span>
                </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        </div>

        <?php elseif(!$isStagiaire): ?>
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
            <div class="px-5 py-3.5 border-b border-slate-100 flex items-center gap-2.5">
                <span class="w-7 h-7 rounded-lg <?php echo e($rc['light']); ?> <?php echo e($rc['text']); ?>

                             flex items-center justify-center flex-shrink-0">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                    </svg>
                </span>
                <h3 class="text-sm font-bold text-slate-700">Informations professionnelles</h3>
            </div>
            <div class="divide-y divide-slate-50">
                <div class="flex items-center justify-between px-5 py-3">
                    <span class="text-xs text-slate-400 font-medium">Matricule</span>
                    <span class="text-sm font-mono font-semibold text-slate-700"><?php echo e($user->matricule_formateur ?? '—'); ?></span>
                </div>
                <div class="flex items-center justify-between px-5 py-3">
                    <span class="text-xs text-slate-400 font-medium">Date d'embauche</span>
                    <span class="text-sm font-semibold text-slate-700"><?php echo e($user->date_embauche?->format('d/m/Y') ?? '—'); ?></span>
                </div>
                <?php if($isFormateur): ?>
                <div class="flex items-center justify-between px-5 py-3">
                    <span class="text-xs text-slate-400 font-medium">Limite heures</span>
                    <span class="text-sm font-semibold <?php echo e($rc['text']); ?>">
                        <?php echo e($user->nbr_heure_limit ? $user->nbr_heure_limit . ' h / semaine' : '—'); ?>

                    </span>
                </div>
                <div class="flex items-center justify-between px-5 py-3">
                    <span class="text-xs text-slate-400 font-medium">Modules assignés</span>
                    <span class="text-sm font-bold <?php echo e($rc['text']); ?>"><?php echo e($user->modules->count()); ?> module(s)</span>
                </div>
                <?php endif; ?>
                <div class="flex items-center justify-between px-5 py-3">
                    <span class="text-xs text-slate-400 font-medium">Membre depuis</span>
                    <span class="text-sm font-semibold text-slate-700"><?php echo e($user->created_at->format('d/m/Y')); ?></span>
                </div>
            </div>
        </div>
        <?php endif; ?>

    </div>


    
    <?php if($isFormateur && $user->modules->isNotEmpty()): ?>
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="px-5 py-3.5 border-b border-slate-100 flex items-center justify-between">
            <div class="flex items-center gap-2.5">
                <span class="w-7 h-7 rounded-lg <?php echo e($rc['light']); ?> <?php echo e($rc['text']); ?>

                             flex items-center justify-center flex-shrink-0">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13
                                 C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477
                                 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247
                                 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                    </svg>
                </span>
                <h3 class="text-sm font-bold text-slate-700">Modules enseignés</h3>
            </div>
            <span class="text-xs font-semibold px-2 py-0.5 rounded-full <?php echo e($rc['light']); ?> <?php echo e($rc['text']); ?>">
                <?php echo e($user->modules->count()); ?>

            </span>
        </div>
        <div class="p-5">
            <div class="flex flex-wrap gap-2">
                <?php $__currentLoopData = $user->modules->sortBy('name'); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $module): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="flex items-center gap-2 px-3 py-1.5 rounded-xl
                            <?php echo e($rc['light']); ?> border <?php echo e($rc['border']); ?>">
                    <span class="text-xs font-semibold <?php echo e($rc['text']); ?>"><?php echo e($module->name); ?></span>
                    <?php if($module->nbr_heure): ?>
                    <span class="text-[10px] font-bold px-1.5 py-0.5 rounded-md bg-white/70 <?php echo e($rc['text']); ?> opacity-70">
                        <?php echo e($module->nbr_heure); ?>h
                    </span>
                    <?php endif; ?>
                </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        </div>
    </div>
    <?php endif; ?>


    
    <?php if($isStagiaire): ?>
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="px-5 py-3.5 border-b border-slate-100 flex items-center justify-between">
            <div class="flex items-center gap-2.5">
                <span class="w-7 h-7 rounded-lg bg-red-50 text-red-500
                             flex items-center justify-center flex-shrink-0">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                </span>
                <h3 class="text-sm font-bold text-slate-700">Absences</h3>
            </div>
            <?php if($absCount > 0): ?>
            <span class="text-xs font-semibold px-2 py-0.5 rounded-full bg-red-50 text-red-500">
                <?php echo e($absCount); ?> au total
            </span>
            <?php else: ?>
            <span class="text-xs font-semibold px-2 py-0.5 rounded-full bg-emerald-50 text-emerald-600">
                Aucune absence ✓
            </span>
            <?php endif; ?>
        </div>

        <div class="p-5">
            <?php if($absCount > 0): ?>
            
            <div class="grid grid-cols-3 gap-3 mb-4">
                <div class="rounded-xl bg-slate-50 border border-slate-100 p-3 text-center">
                    <p class="text-xl font-bold text-slate-800"><?php echo e($absCount); ?></p>
                    <p class="text-[10px] text-slate-400 font-semibold uppercase tracking-wide mt-0.5">Total</p>
                </div>
                <div class="rounded-xl bg-emerald-50 border border-emerald-100 p-3 text-center">
                    <p class="text-xl font-bold text-emerald-600"><?php echo e($absJust); ?></p>
                    <p class="text-[10px] text-emerald-400 font-semibold uppercase tracking-wide mt-0.5">Justifiées</p>
                </div>
                <div class="rounded-xl bg-amber-50 border border-amber-100 p-3 text-center">
                    <p class="text-xl font-bold text-amber-600"><?php echo e($absUnjust); ?></p>
                    <p class="text-[10px] text-amber-400 font-semibold uppercase tracking-wide mt-0.5">Non just.</p>
                </div>
            </div>

            
            <?php $justRate = round(($absJust / $absCount) * 100); ?>
            <div>
                <div class="flex items-center justify-between mb-1.5">
                    <span class="text-xs text-slate-400 font-medium">Taux de justification</span>
                    <span class="text-xs font-bold <?php echo e($justRate >= 50 ? 'text-emerald-600' : 'text-amber-600'); ?>">
                        <?php echo e($justRate); ?>%
                    </span>
                </div>
                <div class="h-2 bg-slate-100 rounded-full overflow-hidden">
                    <div class="h-full rounded-full transition-all duration-700
                                <?php echo e($justRate >= 50 ? 'bg-emerald-400' : 'bg-amber-400'); ?>"
                         style="width: <?php echo e($justRate); ?>%"></div>
                </div>
            </div>

            <?php else: ?>
            <div class="flex flex-col items-center justify-center py-6 text-center">
                <div class="w-12 h-12 rounded-full bg-emerald-50 flex items-center justify-center mb-3">
                    <svg class="w-6 h-6 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                </div>
                <p class="text-sm font-semibold text-slate-700">Aucune absence enregistrée</p>
                <p class="text-xs text-slate-400 mt-1">Présence parfaite 🎉</p>
            </div>
            <?php endif; ?>
        </div>
    </div>
    <?php endif; ?>

</div>



<?php if($user->photo): ?>
<div
    x-show="photoOpen"
    x-cloak
    x-transition:enter="transition ease-out duration-300"
    x-transition:enter-start="opacity-0"
    x-transition:enter-end="opacity-100"
    x-transition:leave="transition ease-in duration-200"
    x-transition:leave-end="opacity-0"
    @click.self="photoOpen = false"
    class="fixed inset-0 z-[999] flex items-center justify-center p-6 bg-black/85 backdrop-blur-md"
    style="display:none"
>
    <div
        x-show="photoOpen"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 scale-75 translate-y-4"
        x-transition:enter-end="opacity-100 scale-100 translate-y-0"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-end="opacity-0 scale-75 translate-y-4"
        class="relative flex flex-col items-center gap-5 select-none"
    >
        <button @click="photoOpen = false"
                class="absolute -top-3 -right-3 z-10 w-9 h-9 rounded-full
                       bg-white/10 hover:bg-white/25 border border-white/20
                       flex items-center justify-center text-white
                       hover:scale-110 transition-all duration-150 backdrop-blur-sm">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/>
            </svg>
        </button>

        
        <div class="p-[3px] shadow-2xl <?php echo e($isCircle ? 'rounded-full' : 'rounded-[28px]'); ?>

                    bg-gradient-to-tr <?php echo e($rc['grad']); ?>">
            <div class="p-[3px] bg-black <?php echo e($isCircle ? 'rounded-full' : 'rounded-[26px]'); ?>">
                <img src="<?php echo e(asset('storage/' . $user->photo)); ?>" alt="<?php echo e($user->name); ?>"
                     class="block object-cover shadow-xl
                            <?php echo e($isCircle ? 'rounded-full w-64 h-64 sm:w-80 sm:h-80' : 'rounded-[24px] w-64 sm:w-80'); ?>"
                     style="max-height:70vh">
            </div>
        </div>

        <div class="text-center space-y-1">
            <p class="text-white font-bold text-lg tracking-wide drop-shadow-lg"><?php echo e($user->name); ?></p>
            <p class="text-white/50 text-xs uppercase tracking-widest font-semibold">
                <?php echo e($rc['icon']); ?> <?php echo e($rc['label']); ?>

            </p>
        </div>
    </div>
</div>
<?php endif; ?>

</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\Project\gestion-CF\resources\views/users/show.blade.php ENDPATH**/ ?>