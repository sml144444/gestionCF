
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <title>OFPPT – <?php echo $__env->yieldContent('title', 'Dashboard'); ?></title>
    <style>
    #sidebar-nav::-webkit-scrollbar { width: 4px; }
    #sidebar-nav::-webkit-scrollbar-track { background: transparent; }
    #sidebar-nav::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.15); border-radius: 99px; }
    #sidebar-nav::-webkit-scrollbar-thumb:hover { background: rgba(255,255,255,0.3); }
    #sidebar-nav { scrollbar-width: thin; scrollbar-color: rgba(255,255,255,0.15) transparent; }
    </style>
    <?php echo app('Illuminate\Foundation\Vite')(['resources/css/app.css', 'resources/js/app.js']); ?>
</head>
<body class="h-screen w-screen overflow-hidden flex bg-slate-100" x-data="{ sidebarOpen: true }">

<?php
    $sidebarColors = [
        'admin'        => 'bg-[#0a6640]',
        'gestionnaire' => 'bg-[#1e293b]',
        'formateur'    => 'bg-[#1a4f8a]',
        'stagiaire'    => 'bg-[#1a4f8a]',
    ];
    $avatarColors = [
        'admin'        => 'bg-[#0a6640]',
        'gestionnaire' => 'bg-slate-500',
        'formateur'    => 'bg-[#1a4f8a]',
        'stagiaire'    => 'bg-[#1a4f8a]',
    ];
    $badgeStyles = [
        'admin'        => 'bg-emerald-100 text-emerald-800',
        'gestionnaire' => 'bg-slate-200 text-slate-700',
        'formateur'    => 'bg-blue-100 text-blue-800',
        'stagiaire'    => 'bg-blue-100 text-blue-800',
    ];
    $sidebarColor = $sidebarColors[Auth::user()->role] ?? 'bg-[#1a5fa8]';
    $avatarColor  = $avatarColors[Auth::user()->role]  ?? 'bg-[#1a5fa8]';
    $badgeStyle   = $badgeStyles[Auth::user()->role]   ?? 'bg-blue-100 text-blue-700';
?>

    
    <aside id="sidebar"
           class="h-full flex flex-col flex-shrink-0 transition-all duration-300 ease-in-out <?php echo e($sidebarColor); ?>"
           :class="sidebarOpen ? 'w-60' : 'w-16'">

        
        <div class="flex items-center gap-3 px-4 py-4 border-b border-white/10 flex-shrink-0 min-h-[60px]">
            <div class="w-9 h-9 rounded-full bg-white flex items-center justify-center flex-shrink-0 shadow overflow-hidden">
                <img src="<?php echo e(asset('images/ofppt-logo.webp')); ?>" alt="OFPPT"
                     class="w-full h-full object-cover"
                     onerror="this.style.display='none'">
            </div>
            <span class="text-white font-bold text-base tracking-widest whitespace-nowrap overflow-hidden transition-all duration-300"
                  :class="sidebarOpen ? 'opacity-100 w-auto' : 'opacity-0 w-0'">
                OFPPT
            </span>
        </div>

        
        
        <nav class="flex-1 overflow-y-auto py-3 px-2 space-y-0.5" id="sidebar-nav">
            <?php echo $__env->make('partials.sidebar.sidebar', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
        </nav>

        
        <div class="border-t border-white/10 p-2 flex-shrink-0">
            <button @click="sidebarOpen = !sidebarOpen"
                    class="w-full flex items-center justify-center gap-2 px-3 py-2 rounded-lg
                           text-white/60 hover:text-white hover:bg-white/10 transition-all text-xs">
                <svg class="w-4 h-4 flex-shrink-0 transition-transform duration-300"
                     :class="sidebarOpen ? '' : 'rotate-180'"
                     fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M11 19l-7-7 7-7m8 14l-7-7 7-7"/>
                </svg>
                <span class="whitespace-nowrap overflow-hidden transition-all duration-300"
                      :class="sidebarOpen ? 'opacity-100 w-auto' : 'opacity-0 w-0'">
                    Réduire
                </span>
            </button>
        </div>
    </aside>

    
    <div class="flex-1 flex flex-col overflow-hidden">

        
        <header class="h-[60px] bg-white border-b border-slate-200 flex items-center
                       justify-between px-5 flex-shrink-0 shadow-sm">
            <div class="flex items-center gap-3">
                <button @click="sidebarOpen = !sidebarOpen"
                        class="lg:hidden w-8 h-8 rounded-lg hover:bg-slate-100 flex items-center justify-center">
                    <svg class="w-5 h-5 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                    </svg>
                </button>
                <h1 class="text-sm font-semibold text-slate-700"><?php echo $__env->yieldContent('page-title', 'Dashboard'); ?></h1>
            </div>

            <div class="flex items-center gap-3">
                <div class="relative">
                    <button class="w-9 h-9 rounded-xl bg-slate-50 border border-slate-200
                                   flex items-center justify-center hover:bg-slate-100 transition-colors">
                        <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                        </svg>
                    </button>
                    <span class="absolute top-1.5 right-1.5 w-2 h-2 bg-red-500 rounded-full border-2 border-white"></span>
                </div>

                <div class="flex items-center gap-2 bg-slate-50 border border-slate-200 rounded-xl px-3 py-1.5">
                    <div class="w-7 h-7 rounded-full flex items-center justify-center text-xs font-bold text-white <?php echo e($avatarColor); ?>">
                        <?php echo e(strtoupper(substr(Auth::user()->name, 0, 1))); ?><?php echo e(strtoupper(substr(explode(' ', Auth::user()->name)[1] ?? '', 0, 1))); ?>

                    </div>
                    <div class="hidden sm:block">
                        <p class="text-xs font-semibold text-slate-700 leading-none"><?php echo e(Auth::user()->name); ?></p>
                        <p class="text-[10px] text-slate-400 mt-0.5"><?php echo e(ucfirst(Auth::user()->role)); ?></p>
                    </div>
                    <span class="hidden md:inline-flex text-[10px] font-semibold px-2 py-0.5 rounded-full <?php echo e($badgeStyle); ?>">
                        <?php echo e(ucfirst(Auth::user()->role)); ?>

                    </span>
                </div>

                <form method="POST" action="<?php echo e(route('logout')); ?>">
                    <?php echo csrf_field(); ?>
                    <button type="submit"
                            class="flex items-center gap-1.5 text-xs font-medium text-slate-500
                                   hover:text-red-600 bg-slate-50 hover:bg-red-50 border border-slate-200
                                   hover:border-red-200 px-3 py-2 rounded-xl transition-all">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                        </svg>
                        <span class="hidden sm:inline">Déconnexion</span>
                    </button>
                </form>
            </div>
        </header>

        <main class="flex-1 overflow-y-auto p-5">
            <?php echo $__env->yieldContent('content'); ?>
        </main>
    </div>

</body>
</html><?php /**PATH C:\Project\gestion-CF\resources\views/layouts/app.blade.php ENDPATH**/ ?>