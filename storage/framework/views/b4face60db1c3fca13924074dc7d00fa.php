


<?php $__env->startSection('title', 'Mon Profil'); ?>
<?php $__env->startSection('page-title', 'Mon Profil'); ?>

<?php $__env->startSection('content'); ?>


<style>[x-cloak]{display:none!important}</style>

<?php
    $roleColors = [
        'admin'        => [
            'bg'     => 'bg-emerald-600',
            'light'  => 'bg-emerald-50',
            'text'   => 'text-emerald-700',
            'border' => 'border-emerald-200',
            'badge'  => 'bg-emerald-100 text-emerald-800',
            'ring'   => 'ring-emerald-500',
            'gradient' => 'from-emerald-600 to-teal-500',
        ],
        'gestionnaire' => [
            'bg'     => 'bg-slate-700',
            'light'  => 'bg-slate-100',
            'text'   => 'text-slate-700',
            'border' => 'border-slate-300',
            'badge'  => 'bg-slate-100 text-slate-700',
            'ring'   => 'ring-slate-500',
            'gradient' => 'from-slate-700 to-slate-600',
        ],
        'formateur'    => [
            'bg'     => 'bg-[#1a4f8a]',
            'light'  => 'bg-blue-50',
            'text'   => 'text-[#1a4f8a]',
            'border' => 'border-blue-200',
            'badge'  => 'bg-blue-100 text-[#1a4f8a]',
            'ring'   => 'ring-[#1a4f8a]',
            'gradient' => 'from-[#1a4f8a] to-[#2c6eb0]',
        ],
        'stagiaire'    => [
            'bg'     => 'bg-[#1a4f8a]',
            'light'  => 'bg-blue-50',
            'text'   => 'text-[#1a4f8a]',
            'border' => 'border-blue-200',
            'badge'  => 'bg-blue-100 text-[#1a4f8a]',
            'ring'   => 'ring-[#1a4f8a]',
            'gradient' => 'from-[#1a4f8a] to-[#2c6eb0]',
        ],
    ];
    $rc = $roleColors[$user->role] ?? $roleColors['stagiaire'];
?>


<?php if(session('success')): ?>
<div x-data="{ show: true }" x-show="show"
     x-transition:enter="transition ease-out duration-200"
     x-transition:enter-start="opacity-0 -translate-y-1"
     x-transition:enter-end="opacity-100 translate-y-0"
     x-transition:leave="transition ease-in duration-150"
     x-transition:leave-end="opacity-0"
     x-init="setTimeout(() => show = false, 4000)"
     class="mb-4 flex items-center gap-3 bg-emerald-50 border border-emerald-200
            text-emerald-800 rounded-xl px-4 py-3 text-sm font-medium shadow-sm">
    <svg class="w-4 h-4 flex-shrink-0 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
    </svg>
    <?php echo e(session('success')); ?>

</div>
<?php endif; ?>


<div x-data="{
    editModal:     <?php echo e($errors->has('name') || $errors->has('email') || $errors->has('phone') || $errors->has('cin') || $errors->has('date_naissance') ? 'true' : 'false'); ?>,
    passwordModal: <?php echo e($errors->has('current_password') || $errors->has('password') || session('open_password_modal') ? 'true' : 'false'); ?>,
    emailModal:    <?php echo e($errors->hasAny(['edu_email','edu_password','new_email']) || session('open_email_modal') ? 'true' : 'false'); ?>,
    photoModal:    false,
    preview:       null,
    openPhoto()  { this.preview = null; this.photoModal = true; },
    closePhoto() { this.preview = null; this.photoModal = false; }
}" class="max-w-5xl mx-auto space-y-6 pb-8">

    
    <div class="relative overflow-hidden rounded-2xl shadow-xl">
        
        <div class="relative h-32 lg:h-40 bg-gradient-to-r <?php echo e($rc['gradient']); ?>">
            <div class="absolute inset-0 opacity-10"
                 style="background-image: url('data:image/svg+xml,%3Csvg width=\"60\" height=\"60\" viewBox=\"0 0 60 60\" xmlns=\"http://www.w3.org/2000/svg\"%3E%3Cg fill=\"none\" fill-rule=\"evenodd\"%3E%3Cg fill=\"%23ffffff\" fill-opacity=\"0.4\"%3E%3Cpath d=\"M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z\"/%3E%3C/g%3E%3C/g%3E%3C/svg%3E')">
            </div>
            
            <div class="absolute bottom-0 right-0 p-4 flex items-center gap-2">
                <button @click="passwordModal = true"
                        class="backdrop-blur-md bg-white/20 hover:bg-white/30 text-white text-xs font-medium px-3 py-1.5 rounded-full flex items-center gap-1.5 transition-all">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                    </svg>
                    Sécurité
                </button>
                <?php if(Auth::user()->isStagiaire()): ?>
                <button @click="emailModal = true"
                        class="backdrop-blur-md bg-white/20 hover:bg-white/30 text-white text-xs font-medium px-3 py-1.5 rounded-full flex items-center gap-1.5 transition-all">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                    </svg>
                    Changer email
                </button>
                <?php endif; ?>
            </div>
        </div>

        
        <div class="relative px-6 pb-6 -mt-12">
            <div class="flex flex-col md:flex-row md:items-end justify-between gap-4">
                
                <div class="relative group">
                    <div class="relative">
                        <div class="w-24 h-24 md:w-28 md:h-28 rounded-2xl shadow-xl ring-4 ring-white overflow-hidden
                                    flex items-center justify-center text-white font-bold text-3xl
                                    bg-gradient-to-br <?php echo e($rc['gradient']); ?>">
                            <?php if($user->photo): ?>
                                <img src="<?php echo e(asset('storage/' . $user->photo)); ?>" alt="<?php echo e($user->name); ?>"
                                     class="w-full h-full object-cover">
                            <?php else: ?>
                                <?php echo e(strtoupper(substr($user->name, 0, 1))); ?><?php echo e(strtoupper(substr(explode(' ', $user->name)[1] ?? '', 0, 1))); ?>

                            <?php endif; ?>
                        </div>
                        <button @click="openPhoto()"
                                class="absolute inset-0 rounded-2xl bg-black/60 flex items-center justify-center
                                       opacity-0 group-hover:opacity-100 transition-all duration-200 cursor-pointer
                                       hover:bg-black/70">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0"/>
                            </svg>
                        </button>
                    </div>
                </div>

                
                <div class="flex-1">
                    <h1 class="text-2xl md:text-3xl font-bold text-slate-800"><?php echo e($user->name); ?></h1>
                    <div class="flex flex-wrap items-center gap-2 mt-1.5">
                        <span class="inline-flex items-center gap-1.5 text-xs font-semibold px-2.5 py-1 rounded-full <?php echo e($rc['badge']); ?>">
                            <span class="w-1.5 h-1.5 rounded-full <?php echo e($rc['bg']); ?>"></span>
                            <?php echo e(ucfirst($user->role)); ?>

                        </span>
                        <span class="text-sm text-slate-400 flex items-center gap-1">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                            </svg>
                            <?php echo e($user->email); ?>

                        </span>
                        <span class="text-sm text-slate-400 flex items-center gap-1">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                            Membre depuis <?php echo e($user->created_at->format('d/m/Y')); ?>

                        </span>
                    </div>
                </div>

                
                <div>
                    <button @click="editModal = true"
                            class="w-full md:w-auto flex items-center justify-center gap-2 px-5 py-2.5 rounded-xl
                                   <?php echo e($rc['bg']); ?> text-white font-medium text-sm
                                   hover:shadow-lg hover:opacity-90 transition-all duration-200">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>
                        Modifier mon profil
                    </button>
                </div>
            </div>
        </div>
    </div>

    
    <?php if($user->isAdmin() || $user->isGestionnaire()): ?>
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white rounded-xl border border-slate-200 p-4 shadow-sm">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-lg <?php echo e($rc['light']); ?> flex items-center justify-center">
                    <svg class="w-5 h-5 <?php echo e($rc['text']); ?>" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                    </svg>
                </div>
                <div>
                    <p class="text-xs text-slate-400">Rôle</p>
                    <p class="font-semibold text-slate-800 capitalize"><?php echo e($user->role); ?></p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl border border-slate-200 p-4 shadow-sm">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-lg <?php echo e($rc['light']); ?> flex items-center justify-center">
                    <svg class="w-5 h-5 <?php echo e($rc['text']); ?>" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                    </svg>
                </div>
                <div>
                    <p class="text-xs text-slate-400">Permissions</p>
                    <p class="font-semibold text-slate-800"><?php echo e($user->getAllPermissions()->count()); ?></p>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>

    
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

        
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
            <div class="border-b border-slate-100 px-5 py-4">
                <h3 class="text-sm font-bold text-slate-700 flex items-center gap-2">
                    <span class="w-6 h-6 rounded-lg <?php echo e($rc['light']); ?> <?php echo e($rc['text']); ?> flex items-center justify-center">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                    </span>
                    Informations personnelles
                </h3>
            </div>
            <div class="p-5 space-y-4">
                <div class="grid grid-cols-[100px,1fr] gap-3 text-sm">
                    <span class="text-slate-400 font-medium">Nom complet</span>
                    <span class="text-slate-700 font-medium"><?php echo e($user->name); ?></span>

                    <span class="text-slate-400">Email</span>
                    <span class="text-slate-600 break-all"><?php echo e($user->email); ?></span>

                    <span class="text-slate-400">Téléphone</span>
                    <span class="text-slate-600"><?php echo e($user->phone ?? 'Non renseigné'); ?></span>

                    <span class="text-slate-400">CIN</span>
                    <span class="text-slate-600"><?php echo e($user->cin ?? 'Non renseigné'); ?></span>

                    <span class="text-slate-400">Date naissance</span>
                    <span class="text-slate-600"><?php echo e($user->date_naissance?->format('d/m/Y') ?? 'Non renseignée'); ?></span>
                </div>
            </div>
        </div>

        
        <?php if($user->isStagiaire()): ?>
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
            <div class="border-b border-slate-100 px-5 py-4">
                <h3 class="text-sm font-bold text-slate-700 flex items-center gap-2">
                    <span class="w-6 h-6 rounded-lg <?php echo e($rc['light']); ?> <?php echo e($rc['text']); ?> flex items-center justify-center">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"/>
                        </svg>
                    </span>
                    Parcours scolaire
                </h3>
            </div>
            <div class="p-5 space-y-4">
                <div class="grid grid-cols-[100px,1fr] gap-3 text-sm">
                    <span class="text-slate-400 font-medium">Filière</span>
                    <span class="text-slate-700 font-medium"><?php echo e($user->filiere?->name ?? 'Non affecté'); ?></span>

                    <span class="text-slate-400">Groupe</span>
                    <span class="text-slate-600"><?php echo e($user->groupe?->name ?? 'Non affecté'); ?></span>

                    <?php if($user->groupe?->annee): ?>
                    <span class="text-slate-400">Année</span>
                    <span class="text-slate-600">Année <?php echo e($user->groupe->annee); ?></span>
                    <?php endif; ?>

                    <?php if($user->groupe?->promo): ?>
                    <span class="text-slate-400">Promotion</span>
                    <span class="text-slate-600"><?php echo e($user->groupe->promo); ?></span>
                    <?php endif; ?>

                    <?php if($user->groupe?->nbr_limit): ?>
                    <span class="text-slate-400">Placement</span>
                    <span class="text-slate-600">
                        <span class="inline-flex items-center gap-1">
                            <span class="w-16 h-1.5 bg-slate-100 rounded-full overflow-hidden">
                                <span class="block h-full <?php echo e($rc['bg']); ?> rounded-full"
                                      style="width: <?php echo e(($user->groupe->stagiaires()->count() / $user->groupe->nbr_limit) * 100); ?>%"></span>
                            </span>
                            <?php echo e($user->groupe->stagiaires()->count()); ?>/<?php echo e($user->groupe->nbr_limit); ?>

                        </span>
                    </span>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <?php endif; ?>

        
        <?php if($user->isFormateur()): ?>
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
            <div class="border-b border-slate-100 px-5 py-4">
                <h3 class="text-sm font-bold text-slate-700 flex items-center gap-2">
                    <span class="w-6 h-6 rounded-lg <?php echo e($rc['light']); ?> <?php echo e($rc['text']); ?> flex items-center justify-center">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                        </svg>
                    </span>
                    Informations professionnelles
                </h3>
            </div>
            <div class="p-5 space-y-4">
                <div class="grid grid-cols-[120px,1fr] gap-3 text-sm">
                    <span class="text-slate-400 font-medium">Matricule</span>
                    <span class="text-slate-700 font-medium"><?php echo e($user->matricule_formateur ?? 'Non renseigné'); ?></span>

                    <span class="text-slate-400">Date embauche</span>
                    <span class="text-slate-600"><?php echo e($user->date_embauche?->format('d/m/Y') ?? 'Non renseignée'); ?></span>

                    <span class="text-slate-400">Limite heures</span>
                    <span class="text-slate-600"><?php echo e($user->nbr_heure_limit ? $user->nbr_heure_limit . ' heures' : 'Non définie'); ?></span>

                    <span class="text-slate-400">Modules</span>
                    <span class="text-slate-600"><?php echo e($user->modules->count()); ?> module(s) assigné(s)</span>
                </div>
            </div>
        </div>

        
        <?php if($user->modules->isNotEmpty()): ?>
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
            <div class="border-b border-slate-100 px-5 py-4">
                <h3 class="text-sm font-bold text-slate-700 flex items-center gap-2">
                    <span class="w-6 h-6 rounded-lg <?php echo e($rc['light']); ?> <?php echo e($rc['text']); ?> flex items-center justify-center">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253M12 6.253C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                        </svg>
                    </span>
                    Modules enseignés
                </h3>
            </div>
            <div class="p-5">
                <div class="flex flex-wrap gap-2">
                    <?php $__currentLoopData = $user->modules; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $module): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <span class="text-xs font-medium px-3 py-1.5 rounded-full <?php echo e($rc['light']); ?> <?php echo e($rc['text']); ?> border <?php echo e($rc['border']); ?>">
                        <?php echo e($module->name); ?>

                    </span>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            </div>
        </div>
        <?php endif; ?>
        <?php endif; ?>

        
        <?php if($user->isAdmin() || $user->isGestionnaire()): ?>
        <div class="lg:col-span-2 bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
            <div class="border-b border-slate-100 px-5 py-4">
                <h3 class="text-sm font-bold text-slate-700 flex items-center gap-2">
                    <span class="w-6 h-6 rounded-lg <?php echo e($rc['light']); ?> <?php echo e($rc['text']); ?> flex items-center justify-center">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                        </svg>
                    </span>
                    Permissions système
                </h3>
            </div>
            <div class="p-5">
                <div class="flex flex-wrap gap-1.5">
                    <?php $__currentLoopData = $user->getAllPermissions()->sortBy('name'); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $perm): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <span class="text-[10px] font-semibold px-2 py-0.5 rounded-full bg-slate-100 text-slate-600 border border-slate-200">
                        <?php echo e($perm->name); ?>

                    </span>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            </div>
        </div>
        <?php endif; ?>

    </div>


    
    <div x-show="editModal" x-cloak
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-end="opacity-0"
         @keydown.escape.window="editModal = false"
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm">

        <div x-show="editModal"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 scale-95 translate-y-2"
             x-transition:enter-end="opacity-100 scale-100 translate-y-0"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-end="opacity-0 scale-95"
             @click.outside="editModal = false"
             class="w-full max-w-2xl bg-white rounded-2xl shadow-2xl overflow-hidden">

            <div class="bg-gradient-to-r <?php echo e($rc['gradient']); ?> px-6 py-4">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-lg font-bold text-white">Modifier mon profil</h3>
                        <p class="text-white/80 text-xs mt-0.5">Mettez à jour vos informations personnelles</p>
                    </div>
                    <button @click="editModal = false"
                            class="w-8 h-8 rounded-lg bg-white/20 hover:bg-white/30 flex items-center justify-center text-white transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
            </div>

            <form method="POST" action="<?php echo e(route('profile.update')); ?>" class="p-6 space-y-5" data-submit-once>
                <?php echo csrf_field(); ?> <?php echo method_field('PUT'); ?>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="md:col-span-2">
                        <?php if($user->isStagiaire()): ?>
                            <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1.5">
                                Nom complet
                            </label>
                            <div class="w-full px-4 py-2.5 text-sm border border-slate-200 rounded-xl bg-slate-50 text-slate-500 cursor-not-allowed select-none">
                                <?php echo e($user->name); ?>

                            </div>
                            <p class="text-[10px] text-amber-600 mt-1 flex items-center gap-1">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                Le nom ne peut pas être modifié. Contactez l'administration.
                            </p>
                        <?php else: ?>
                            <?php if (isset($component)) { $__componentOriginalf4c8ecf26ef77d4de25edf56eae3a34d = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalf4c8ecf26ef77d4de25edf56eae3a34d = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.form-field','data' => ['name' => 'name','label' => 'Nom complet','value' => old('name', $user->name),'required' => true]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('form-field'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(Illuminate\View\AnonymousComponent::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'name','label' => 'Nom complet','value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(old('name', $user->name)),'required' => true]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalf4c8ecf26ef77d4de25edf56eae3a34d)): ?>
<?php $attributes = $__attributesOriginalf4c8ecf26ef77d4de25edf56eae3a34d; ?>
<?php unset($__attributesOriginalf4c8ecf26ef77d4de25edf56eae3a34d); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalf4c8ecf26ef77d4de25edf56eae3a34d)): ?>
<?php $component = $__componentOriginalf4c8ecf26ef77d4de25edf56eae3a34d; ?>
<?php unset($__componentOriginalf4c8ecf26ef77d4de25edf56eae3a34d); ?>
<?php endif; ?>
                        <?php endif; ?>
                    </div>
                    <div>
                        <?php if (isset($component)) { $__componentOriginalf4c8ecf26ef77d4de25edf56eae3a34d = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalf4c8ecf26ef77d4de25edf56eae3a34d = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.form-field','data' => ['name' => 'phone','label' => 'Téléphone','value' => old('phone', $user->phone)]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('form-field'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(Illuminate\View\AnonymousComponent::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'phone','label' => 'Téléphone','value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(old('phone', $user->phone))]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalf4c8ecf26ef77d4de25edf56eae3a34d)): ?>
<?php $attributes = $__attributesOriginalf4c8ecf26ef77d4de25edf56eae3a34d; ?>
<?php unset($__attributesOriginalf4c8ecf26ef77d4de25edf56eae3a34d); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalf4c8ecf26ef77d4de25edf56eae3a34d)): ?>
<?php $component = $__componentOriginalf4c8ecf26ef77d4de25edf56eae3a34d; ?>
<?php unset($__componentOriginalf4c8ecf26ef77d4de25edf56eae3a34d); ?>
<?php endif; ?>
                    </div>
                    <div>
                        <?php if (isset($component)) { $__componentOriginalf4c8ecf26ef77d4de25edf56eae3a34d = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalf4c8ecf26ef77d4de25edf56eae3a34d = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.form-field','data' => ['name' => 'cin','label' => 'CIN','value' => old('cin', $user->cin)]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('form-field'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(Illuminate\View\AnonymousComponent::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'cin','label' => 'CIN','value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(old('cin', $user->cin))]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalf4c8ecf26ef77d4de25edf56eae3a34d)): ?>
<?php $attributes = $__attributesOriginalf4c8ecf26ef77d4de25edf56eae3a34d; ?>
<?php unset($__attributesOriginalf4c8ecf26ef77d4de25edf56eae3a34d); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalf4c8ecf26ef77d4de25edf56eae3a34d)): ?>
<?php $component = $__componentOriginalf4c8ecf26ef77d4de25edf56eae3a34d; ?>
<?php unset($__componentOriginalf4c8ecf26ef77d4de25edf56eae3a34d); ?>
<?php endif; ?>
                    </div>
                    <div>
                        <?php if (isset($component)) { $__componentOriginalf4c8ecf26ef77d4de25edf56eae3a34d = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalf4c8ecf26ef77d4de25edf56eae3a34d = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.form-field','data' => ['name' => 'date_naissance','label' => 'Date de naissance','type' => 'date','value' => old('date_naissance', $user->date_naissance?->format('Y-m-d'))]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('form-field'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(Illuminate\View\AnonymousComponent::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'date_naissance','label' => 'Date de naissance','type' => 'date','value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(old('date_naissance', $user->date_naissance?->format('Y-m-d')))]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalf4c8ecf26ef77d4de25edf56eae3a34d)): ?>
<?php $attributes = $__attributesOriginalf4c8ecf26ef77d4de25edf56eae3a34d; ?>
<?php unset($__attributesOriginalf4c8ecf26ef77d4de25edf56eae3a34d); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalf4c8ecf26ef77d4de25edf56eae3a34d)): ?>
<?php $component = $__componentOriginalf4c8ecf26ef77d4de25edf56eae3a34d; ?>
<?php unset($__componentOriginalf4c8ecf26ef77d4de25edf56eae3a34d); ?>
<?php endif; ?>
                    </div>
                    <?php if($user->isFormateur()): ?>
                    <div>
                        <?php if (isset($component)) { $__componentOriginalf4c8ecf26ef77d4de25edf56eae3a34d = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalf4c8ecf26ef77d4de25edf56eae3a34d = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.form-field','data' => ['name' => 'matricule_formateur','label' => 'Matricule','value' => old('matricule_formateur', $user->matricule_formateur)]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('form-field'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(Illuminate\View\AnonymousComponent::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'matricule_formateur','label' => 'Matricule','value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(old('matricule_formateur', $user->matricule_formateur))]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalf4c8ecf26ef77d4de25edf56eae3a34d)): ?>
<?php $attributes = $__attributesOriginalf4c8ecf26ef77d4de25edf56eae3a34d; ?>
<?php unset($__attributesOriginalf4c8ecf26ef77d4de25edf56eae3a34d); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalf4c8ecf26ef77d4de25edf56eae3a34d)): ?>
<?php $component = $__componentOriginalf4c8ecf26ef77d4de25edf56eae3a34d; ?>
<?php unset($__componentOriginalf4c8ecf26ef77d4de25edf56eae3a34d); ?>
<?php endif; ?>
                    </div>
                    <div>
                        <?php if (isset($component)) { $__componentOriginalf4c8ecf26ef77d4de25edf56eae3a34d = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalf4c8ecf26ef77d4de25edf56eae3a34d = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.form-field','data' => ['name' => 'date_embauche','label' => 'Date embauche','type' => 'date','value' => old('date_embauche', $user->date_embauche?->format('Y-m-d'))]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('form-field'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(Illuminate\View\AnonymousComponent::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'date_embauche','label' => 'Date embauche','type' => 'date','value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(old('date_embauche', $user->date_embauche?->format('Y-m-d')))]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalf4c8ecf26ef77d4de25edf56eae3a34d)): ?>
<?php $attributes = $__attributesOriginalf4c8ecf26ef77d4de25edf56eae3a34d; ?>
<?php unset($__attributesOriginalf4c8ecf26ef77d4de25edf56eae3a34d); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalf4c8ecf26ef77d4de25edf56eae3a34d)): ?>
<?php $component = $__componentOriginalf4c8ecf26ef77d4de25edf56eae3a34d; ?>
<?php unset($__componentOriginalf4c8ecf26ef77d4de25edf56eae3a34d); ?>
<?php endif; ?>
                    </div>
                    <?php endif; ?>
                </div>

                <div class="flex gap-3 pt-2">
                    <button type="button" @click="editModal = false"
                            class="flex-1 px-4 py-2.5 rounded-xl border border-slate-200 text-sm font-semibold text-slate-600 hover:bg-slate-50 transition-colors">
                        Annuler
                    </button>
                    <button type="submit"
                            class="flex-1 px-4 py-2.5 rounded-xl <?php echo e($rc['bg']); ?> text-white text-sm font-semibold hover:opacity-90 transition-all shadow-sm">
                        Enregistrer les modifications
                    </button>
                </div>
            </form>
        </div>
    </div>

    
    <div x-show="passwordModal" x-cloak
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-end="opacity-0"
         @keydown.escape.window="passwordModal = false"
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm">

        <div x-show="passwordModal"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 scale-95 translate-y-2"
             x-transition:enter-end="opacity-100 scale-100 translate-y-0"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-end="opacity-0 scale-95"
             @click.outside="passwordModal = false"
             class="w-full max-w-md bg-white rounded-2xl shadow-2xl overflow-hidden">

            <div class="bg-gradient-to-r from-slate-700 to-slate-800 px-6 py-4">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-lg font-bold text-white">Changer le mot de passe</h3>
                        <p class="text-white/80 text-xs mt-0.5">Utilisez un mot de passe sécurisé</p>
                    </div>
                    <button @click="passwordModal = false"
                            class="w-8 h-8 rounded-lg bg-white/20 hover:bg-white/30 flex items-center justify-center text-white transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
            </div>

            <form method="POST" action="<?php echo e(route('profile.password')); ?>" class="p-6 space-y-4" data-submit-once>
                <?php echo csrf_field(); ?> <?php echo method_field('PUT'); ?>

                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1.5">Mot de passe actuel</label>
                    <input type="password" name="current_password" autocomplete="current-password"
                           class="w-full px-3 py-2.5 text-sm border rounded-xl transition-colors
                                  focus:outline-none focus:ring-2 focus:ring-slate-500/30 focus:border-slate-400
                                  <?php $__errorArgs = ['current_password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-400 bg-red-50 <?php else: ?> border-slate-200 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                    <?php $__errorArgs = ['current_password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                    <p class="mt-1 text-xs text-red-600"><?php echo e($message); ?></p>
                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1.5">Nouveau mot de passe</label>
                    <input type="password" name="password" autocomplete="new-password"
                           class="w-full px-3 py-2.5 text-sm border rounded-xl transition-colors
                                  focus:outline-none focus:ring-2 focus:ring-slate-500/30 focus:border-slate-400
                                  <?php $__errorArgs = ['password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-400 bg-red-50 <?php else: ?> border-slate-200 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                    <?php $__errorArgs = ['password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                    <p class="mt-1 text-xs text-red-600"><?php echo e($message); ?></p>
                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1.5">Confirmer le mot de passe</label>
                    <input type="password" name="password_confirmation" autocomplete="new-password"
                           class="w-full px-3 py-2.5 text-sm border border-slate-200 rounded-xl transition-colors
                                  focus:outline-none focus:ring-2 focus:ring-slate-500/30 focus:border-slate-400">
                </div>

                <div class="flex gap-3 pt-2">
                    <button type="button" @click="passwordModal = false"
                            class="flex-1 px-4 py-2.5 rounded-xl border border-slate-200 text-sm font-semibold text-slate-600 hover:bg-slate-50 transition-colors">
                        Annuler
                    </button>
                    <button type="submit"
                            class="flex-1 px-4 py-2.5 rounded-xl bg-slate-800 text-white text-sm font-semibold hover:bg-slate-700 transition-all shadow-sm">
                        Mettre à jour
                    </button>
                </div>
            </form>
        </div>
    </div>

    
    <?php if(Auth::user()->isStagiaire()): ?>
    <div x-show="emailModal" x-cloak
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-end="opacity-0"
         @keydown.escape.window="emailModal = false"
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm">

        <div x-show="emailModal"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 scale-95 translate-y-2"
             x-transition:enter-end="opacity-100 scale-100 translate-y-0"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-end="opacity-0 scale-95"
             @click.outside="emailModal = false"
             class="w-full max-w-md bg-white rounded-2xl shadow-2xl overflow-hidden">

            <div class="bg-gradient-to-r <?php echo e($rc['gradient']); ?> px-6 py-4">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-lg font-bold text-white">Changer l'email personnel</h3>
                        <p class="text-white/80 text-xs mt-0.5">Vérification EDU requise</p>
                    </div>
                    <button @click="emailModal = false"
                            class="w-8 h-8 rounded-lg bg-white/20 hover:bg-white/30 flex items-center justify-center text-white transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
            </div>

            <form method="POST" action="<?php echo e(route('profile.email')); ?>" class="p-6 space-y-4" data-submit-once>
                <?php echo csrf_field(); ?> <?php echo method_field('PUT'); ?>

                <div class="bg-blue-50 border border-blue-100 rounded-xl px-4 py-3 flex items-start gap-3">
                    <svg class="w-4 h-4 text-blue-500 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                    </svg>
                    <p class="text-xs text-blue-700 leading-relaxed">
                        Vérification via votre compte EDU (@ofppt.ma) obligatoire avant de modifier votre email personnel.
                    </p>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1.5">
                        Email EDU
                    </label>
                    <input type="email" name="edu_email" value="<?php echo e(old('edu_email')); ?>"
                           placeholder="prenom.nom@ofppt.ma"
                           class="w-full px-3 py-2.5 text-sm border rounded-xl transition-colors
                                  focus:outline-none focus:ring-2 focus:ring-blue-500/30 focus:border-blue-400
                                  <?php $__errorArgs = ['edu_email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-400 bg-red-50 <?php else: ?> border-slate-200 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                    <?php $__errorArgs = ['edu_email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                    <p class="mt-1 text-xs text-red-600"><?php echo e($message); ?></p>
                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1.5">
                        Mot de passe EDU
                    </label>
                    <input type="password" name="edu_password"
                           placeholder="••••••••"
                           class="w-full px-3 py-2.5 text-sm border rounded-xl transition-colors
                                  focus:outline-none focus:ring-2 focus:ring-blue-500/30 focus:border-blue-400
                                  <?php $__errorArgs = ['edu_password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-400 bg-red-50 <?php else: ?> border-slate-200 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                    <?php $__errorArgs = ['edu_password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                    <p class="mt-1 text-xs text-red-600"><?php echo e($message); ?></p>
                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1.5">
                        Nouvel email personnel
                    </label>
                    <input type="email" name="new_email" value="<?php echo e(old('new_email')); ?>"
                           placeholder="nouveau.email@gmail.com"
                           class="w-full px-3 py-2.5 text-sm border rounded-xl transition-colors
                                  focus:outline-none focus:ring-2 focus:ring-blue-500/30 focus:border-blue-400
                                  <?php $__errorArgs = ['new_email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-400 bg-red-50 <?php else: ?> border-slate-200 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                    <?php $__errorArgs = ['new_email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                    <p class="mt-1 text-xs text-red-600"><?php echo e($message); ?></p>
                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>

                <div class="flex gap-3 pt-2">
                    <button type="button" @click="emailModal = false"
                            class="flex-1 px-4 py-2.5 rounded-xl border border-slate-200 text-sm font-semibold text-slate-600 hover:bg-slate-50 transition-colors">
                        Annuler
                    </button>
                    <button type="submit"
                            class="flex-1 px-4 py-2.5 rounded-xl <?php echo e($rc['bg']); ?> text-white text-sm font-semibold hover:opacity-90 transition-all shadow-sm">
                        Vérifier & mettre à jour
                    </button>
                </div>
            </form>
        </div>
    </div>
    <?php endif; ?>

    
    <div x-show="photoModal" x-cloak
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-end="opacity-0"
         @keydown.escape.window="closePhoto()"
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm">

        <div x-show="photoModal"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 scale-95 translate-y-2"
             x-transition:enter-end="opacity-100 scale-100 translate-y-0"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-end="opacity-0 scale-95"
             @click.outside="closePhoto()"
             class="w-full max-w-sm bg-white rounded-2xl shadow-2xl overflow-hidden">

            <div class="bg-gradient-to-r <?php echo e($rc['gradient']); ?> px-6 py-4">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-bold text-white">Photo de profil</h3>
                    <button @click="closePhoto()"
                            class="w-8 h-8 rounded-lg bg-white/20 hover:bg-white/30 flex items-center justify-center text-white transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
            </div>

            <form method="POST" action="<?php echo e(route('profile.photo')); ?>" enctype="multipart/form-data"
                  class="p-6 space-y-5" data-submit-once>
                <?php echo csrf_field(); ?>

                <div class="flex flex-col items-center gap-4">
                    <div class="relative w-28 h-28 rounded-2xl shadow-lg ring-4 ring-white overflow-hidden
                                flex items-center justify-center text-white font-bold text-3xl
                                bg-gradient-to-br <?php echo e($rc['gradient']); ?>">

                        <img x-show="preview" :src="preview"
                             class="absolute inset-0 w-full h-full object-cover" alt="">

                        <?php if($user->photo): ?>
                            <img x-show="!preview"
                                 src="<?php echo e(asset('storage/' . $user->photo)); ?>"
                                 class="absolute inset-0 w-full h-full object-cover" alt="">
                        <?php else: ?>
                            <span x-show="!preview">
                                <?php echo e(strtoupper(substr($user->name, 0, 1))); ?><?php echo e(strtoupper(substr(explode(' ', $user->name)[1] ?? '', 0, 1))); ?>

                            </span>
                        <?php endif; ?>
                    </div>

                    <label class="cursor-pointer flex items-center gap-2 text-sm font-semibold px-5 py-2.5 rounded-xl
                                  border-2 border-dashed border-slate-300 hover:border-[<?php echo e($rc['text']); ?>]
                                  <?php echo e($rc['text']); ?> transition-all">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                        </svg>
                        Choisir une image
                        <input type="file" name="photo" accept="image/*" class="hidden"
                               @change="preview = URL.createObjectURL($event.target.files[0])">
                    </label>
                    <p class="text-[10px] text-slate-400">JPEG, PNG, WEBP — Max 2 Mo</p>
                </div>

                <div class="flex gap-3">
                    <button type="button" @click="closePhoto()"
                            class="flex-1 px-4 py-2.5 rounded-xl border border-slate-200 text-sm font-semibold text-slate-600 hover:bg-slate-50 transition-colors">
                        Annuler
                    </button>
                    <button type="submit"
                            class="flex-1 px-4 py-2.5 rounded-xl <?php echo e($rc['bg']); ?> text-white text-sm font-semibold hover:opacity-90 transition-all shadow-sm">
                        Enregistrer
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>


<script>
document.querySelectorAll('form[data-submit-once]').forEach(function (form) {
    form.addEventListener('submit', function () {
        form.querySelectorAll('button[type="submit"]').forEach(function (btn) {
            btn.disabled = true;
            btn.style.opacity = '0.6';
            btn.style.cursor  = 'not-allowed';
            btn.innerHTML =
                '<svg class="animate-spin w-4 h-4 inline mr-1.5" fill="none" viewBox="0 0 24 24">'
                + '<circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>'
                + '<path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"/>'
                + '</svg>'
                + 'Traitement…';
        });
    });
});
</script>

<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\Project\gestion-CF\resources\views/profile/show.blade.php ENDPATH**/ ?>