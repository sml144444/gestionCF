
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>OFPPT – Connexion</title>
    <?php echo app('Illuminate\Foundation\Vite')(['resources/css/app.css', 'resources/js/app.js']); ?>
</head>
<body class="h-screen w-screen overflow-hidden flex">

    
    <div class="hidden lg:flex w-[45%] h-full flex-col items-center justify-around py-20 px-12
                bg-gradient-to-br from-[#1a5fa8] via-[#0d4a85] to-[#0a7a5e] relative overflow-hidden">

        
        <div class="absolute -top-24 -left-24 w-80 h-80 rounded-full bg-white/5"></div>
        <div class="absolute -bottom-32 -right-20 w-96 h-96 rounded-full bg-white/5"></div>
        <div class="absolute top-1/2 -left-16 w-48 h-48 rounded-full bg-white/5"></div>

        
        <div class="relative z-10 flex flex-col items-center">
            <div class="w-28 h-28 rounded-full overflow-hidden bg-white shadow-xl flex items-center justify-center mb-6">
                <img  alt="OFPPT" class="w-full h-full object-cover" src="<?php echo e(asset('images/ofppt-logo.webp')); ?> "
                     alt="OFPPT" class="w-20 h-20 object-contain">
            </div>
            <p class="text-white/90 text-2xl italic">La voie de l'avenir</p>
        </div>

        
        <div class="relative z-10 text-center px-6 max-w-md">
            <p class="text-white/80 text-2xl font-semibold leading-snug mb-4">
                Plateforme de gestion de votre centre de formation
            </p>
            <p class="text-white/70 text-sm leading-relaxed">
                Accédez à vos cours, notes, emplois du temps et bien plus depuis un seul espace.
            </p>
        </div>

        
        <div class="relative z-10 flex flex-col items-center gap-3 w-full">
            <p class="text-white/50 text-xs uppercase tracking-widest">Accès pour tous </p>
            <div class="flex flex-wrap gap-2 justify-center">
                <span class="text-xs bg-white/15 text-white px-4 py-1.5 rounded-full border border-white/25 backdrop-blur-sm">Gestionnaire</span>
                <span class="text-xs bg-white/15 text-white px-4 py-1.5 rounded-full border border-white/25 backdrop-blur-sm">Formateur</span>
                <span class="text-xs bg-white/15 text-white px-4 py-1.5 rounded-full border border-white/25 backdrop-blur-sm">Stagiaire</span>
            </div>
        </div>
    </div>

    
    <div class="flex-1 h-full flex flex-col justify-center items-center bg-white px-6 py-10 overflow-y-auto">

        
        <div class="lg:hidden flex items-center gap-3 mb-10">
            <div class="w-12 h-12 rounded-full bg-[#1a5fa8] flex items-center justify-center shadow">
                <span class="text-white font-bold text-base">OF</span>
            </div>
            <div>
                <p class="font-bold text-[#1a5fa8] text-lg leading-none tracking-widest">OFPPT</p>
                <p class="text-xs text-gray-400 italic">La voie de l'avenir</p>
            </div>
        </div>

        <div class="w-full max-w-lg">

            <h2 class="text-3xl text-center font-semibold text-slate-800 mb-4">Connexion</h2>
            <p class="text-center text-sm text-slate-500 mb-8">Accédez à votre espace personnel</p>

            <?php if(session('status')): ?>
                <div class="mb-5 text-sm text-green-700 bg-green-50 border border-green-200 rounded-xl px-4 py-3">
                    <?php echo e(session('status')); ?>

                </div>
            <?php endif; ?>

            <form method="POST" action="<?php echo e(route('login')); ?>" class="space-y-5">
                <?php echo csrf_field(); ?>

                
                <div>
                    <label for="email"
                           class="block text-xs font-semibold text-slate-500 uppercase tracking-widest mb-2">
                        Adresse email
                    </label>
                    <input id="email" name="email" type="email"
                           value="<?php echo e(old('email')); ?>"
                           required autofocus autocomplete="username"
                           placeholder="votre@email.com"
                           class="w-full h-12 px-4 rounded-xl border border-slate-200 bg-slate-50
                                  text-sm text-slate-800 placeholder-slate-400
                                  focus:outline-none focus:border-[#1a5fa8] focus:bg-white focus:ring-2 focus:ring-[#1a5fa8]/20
                                  transition-all" />
                    <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                        <p class="text-xs text-red-500 mt-1.5 flex items-center gap-1">
                            <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                            <?php echo e($message); ?>

                        </p>
                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>

                
                <div>
                    <label for="password"
                           class="block text-xs font-semibold text-slate-500 uppercase tracking-widest mb-2">
                        Mot de passe
                    </label>
                    <div class="relative">
                        <input id="password" name="password" type="password"
                               required autocomplete="current-password"
                               placeholder="••••••••"
                               class="w-full h-12 px-4 pr-12 rounded-xl border border-slate-200 bg-slate-50
                                      text-sm text-slate-800 placeholder-slate-400
                                      focus:outline-none focus:border-[#1a5fa8] focus:bg-white focus:ring-2 focus:ring-[#1a5fa8]/20
                                      transition-all" />
                        <button type="button" onclick="togglePwd('password', this)"
                                class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                        </button>
                    </div>
                    <?php $__errorArgs = ['password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                        <p class="text-xs text-red-500 mt-1.5"><?php echo e($message); ?></p>
                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>


<div class="flex items-center justify-between">
    <label class="flex items-center gap-2 text-sm text-slate-500 cursor-pointer select-none">
        <input type="checkbox" name="remember"
               class="w-4 h-4 rounded border-slate-300 text-[#1a5fa8] focus:ring-[#1a5fa8]" />
        Se souvenir de moi
    </label>

    <a href="<?php echo e(route('password.request')); ?>"
       class="text-sm text-[#1a5fa8] font-semibold hover:underline">
        Mot de passe oublié ?
    </a>
</div>

                
                <button type="submit"
                        class="w-full h-12 bg-[#1a5fa8] hover:bg-[#0d4a85] active:scale-[0.98]
                               text-white text-sm font-semibold rounded-xl transition-all shadow-md shadow-[#1a5fa8]/30">
                    Se connecter
                </button>

                
                <div class="relative flex items-center gap-3 py-1">
                    <div class="flex-1 h-px bg-slate-200"></div>
                    <span class="text-xs text-slate-400">ou</span>
                    <div class="flex-1 h-px bg-slate-200"></div>
                </div>

                <p class="text-center text-sm text-slate-500">
                    Stagiaire ?
                    <a href="<?php echo e(route('register')); ?>"
                       class="text-[#1a5fa8] font-semibold hover:underline">
                        Créer un compte →
                    </a>
                </p>
            </form>
        </div>


    </div>

</body>
<script>
function togglePwd(id, btn) {
    const input = document.getElementById(id);
    input.type = input.type === 'password' ? 'text' : 'password';
}
</script>
</html><?php /**PATH C:\Project\gestion-CF\resources\views/auth/login.blade.php ENDPATH**/ ?>