
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>OFPPT – Inscription Stagiaire</title>
    <?php echo app('Illuminate\Foundation\Vite')(['resources/css/app.css', 'resources/js/app.js']); ?>
</head>
<body class="h-screen w-screen overflow-hidden flex">

    
    <div class="hidden lg:flex w-[45%] h-full flex-col items-center justify-around py-16 px-12
                bg-gradient-to-br from-[#0a7a5e] via-[#0d5c7a] to-[#1a5fa8] relative overflow-hidden">

        <div class="absolute -top-20 -right-20 w-72 h-72 rounded-full bg-white/5"></div>
        <div class="absolute -bottom-28 -left-16 w-80 h-80 rounded-full bg-white/5"></div>

        
        <div class="relative z-10 flex flex-col items-center">
            <div class="w-28 h-28 rounded-full overflow-hidden bg-white shadow-xl flex items-center justify-center mb-6">
                <img src="<?php echo e(asset('images/ofppt-logo.webp')); ?>" alt="OFPPT" class="w-full h-full object-cover">
            </div>
            <p class="text-white/90 text-2xl italic">La voie de l'avenir</p>
        </div>

        
        <div class="relative z-10 w-full">
            <p class="text-white/90 text-xs uppercase tracking-widest text-center mb-5">Comment ça marche ?</p>
            <div class="space-y-4">
                <?php $__currentLoopData = [
                    ['1', 'Email & mot de passe EDU', 'Utilisez les identifiants fournis par votre centre'],
                    ['2', 'Vérification automatique',  'Le système valide votre pré-inscription dans la base EDU'],
                    ['3', 'Compte créé instantanément','Nom, filière et groupe attribués automatiquement'],
                ]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as [$n, $title, $desc]): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="flex items-start gap-4">
                    <div class="w-8 h-8 rounded-full bg-white/20 border border-white/30
                                flex items-center justify-center flex-shrink-0 mt-0.5">
                        <span class="text-white text-sm font-bold"><?php echo e($n); ?></span>
                    </div>
                    <div>
                        <p class="text-white text-sm font-semibold"><?php echo e($title); ?></p>
                        <p class="text-white/60 text-xs leading-relaxed mt-0.5"><?php echo e($desc); ?></p>
                    </div>
                </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        </div>

        
        <div class="relative z-10 bg-white/10 border border-white/20 rounded-2xl px-5 py-4 w-full">
            <p class="text-white/80 text-xs leading-relaxed text-center">
                L'inscription est réservée aux stagiaires pré-enregistrés.<br>
                Pour toute question, contactez l'administration.
            </p>
        </div>
    </div>

    
    <div class="flex-1 h-full flex flex-col justify-center items-center bg-white px-6 py-8 overflow-y-auto">

        
        <div class="lg:hidden flex items-center gap-3 mb-8">
            <div class="w-12 h-12 rounded-full bg-[#1a5fa8] flex items-center justify-center shadow">
                <span class="text-white font-bold text-base">OF</span>
            </div>
            <div>
                <p class="font-bold text-[#1a5fa8] text-lg leading-none tracking-widest">OFPPT</p>
                <p class="text-xs text-gray-400 italic">La voie de l'avenir</p>
            </div>
        </div>

        <div class="w-full max-w-md">

            
            <div class="flex justify-center mb-6">
                <div class="inline-flex items-center gap-2 bg-emerald-50 border border-emerald-200
                            text-emerald-700 text-xs font-semibold px-4 py-1.5 rounded-full">
                    <div class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></div>
                    Inscription Stagiaire
                </div>
            </div>

            <h2 class="text-2xl font-semibold text-center text-slate-800 mb-2">Créer votre compte</h2>
            <p class="text-sm text-slate-500 text-center mb-8">
                Entrez vos identifiants EDU fournis par le centre
            </p>

            
            <?php if($errors->any()): ?>
                <div class="mb-5 px-4 py-3 bg-red-50 border border-red-200 rounded-xl
                            text-sm text-red-700 flex items-start gap-2">
                    <svg class="w-4 h-4 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                    </svg>
                    <span><?php echo e($errors->first()); ?></span>
                </div>
            <?php endif; ?>

            <form method="POST" action="<?php echo e(route('register')); ?>" class="space-y-5">
                <?php echo csrf_field(); ?>

                
                <div>
                    <label for="email"
                           class="block text-xs font-semibold text-slate-500 uppercase tracking-widest mb-2">
                        Email EDU
                    </label>
                    <input id="email" name="email" type="email"
                           value="<?php echo e(old('email')); ?>"
                           required autofocus autocomplete="username"
                           placeholder="votre.email@ofppt.ma"
                           class="w-full h-12 px-4 rounded-xl border bg-slate-50 text-sm
                                  text-slate-800 placeholder-slate-400 transition-all
                                  focus:outline-none focus:bg-white focus:ring-2 focus:ring-[#1a5fa8]/20
                                  <?php echo e($errors->has('email') ? 'border-red-400 bg-red-50' : 'border-slate-200 focus:border-[#1a5fa8]'); ?>" />
                </div>

                
                <div>
                    <label for="password"
                           class="block text-xs font-semibold text-slate-500 uppercase tracking-widest mb-2">
                        Mot de passe EDU
                    </label>
                    <div class="relative">
                        <input id="password" name="password" type="password"
                               required autocomplete="new-password"
                               placeholder="••••••••"
                               class="w-full h-12 px-4 pr-12 rounded-xl border border-slate-200
                                      bg-slate-50 text-sm text-slate-800 placeholder-slate-400
                                      transition-all focus:outline-none focus:border-[#1a5fa8]
                                      focus:bg-white focus:ring-2 focus:ring-[#1a5fa8]/20
                                      <?php echo e($errors->has('password') ? 'border-red-400' : ''); ?>" />
                        <button type="button" onclick="togglePwd('password')"
                                class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400
                                       hover:text-[#1a5fa8] transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M15 12a3 3 0 11-6 0 3 3 0 016 0zM2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
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

                
                <div>
                    <label for="password_confirmation"
                           class="block text-xs font-semibold text-slate-500 uppercase tracking-widest mb-2">
                        Confirmer le mot de passe
                    </label>
                    <div class="relative">
                        <input id="password_confirmation" name="password_confirmation"
                               type="password"
                               required autocomplete="new-password"
                               placeholder="••••••••"
                               class="w-full h-12 px-4 pr-12 rounded-xl border border-slate-200
                                      bg-slate-50 text-sm text-slate-800 placeholder-slate-400
                                      transition-all focus:outline-none focus:border-[#1a5fa8]
                                      focus:bg-white focus:ring-2 focus:ring-[#1a5fa8]/20" />
                        <button type="button" onclick="togglePwd('password_confirmation')"
                                class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400
                                       hover:text-[#1a5fa8] transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M15 12a3 3 0 11-6 0 3 3 0 016 0zM2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                        </button>
                    </div>
                </div>

                
                <div class="bg-blue-50 border border-blue-100 rounded-xl px-4 py-3
                            flex items-start gap-3">
                    <svg class="w-4 h-4 text-blue-400 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                    </svg>
                    <p class="text-xs text-blue-600 leading-relaxed">
                        Votre nom, filière et groupe seront attribués automatiquement depuis votre dossier EDU.
                    </p>
                </div>

                
                <button type="submit"
                        class="w-full h-12 bg-[#1a5fa8] hover:bg-[#0d4a85] active:scale-[0.98]
                               text-white text-sm font-semibold rounded-xl transition-all
                               shadow-lg shadow-[#1a5fa8]/30">
                    S'inscrire
                </button>

                <p class="text-center text-sm text-slate-500">
                    Déjà inscrit ?
                    <a href="<?php echo e(route('login')); ?>" class="text-[#1a5fa8] font-semibold hover:underline">
                        Se connecter →
                    </a>
                </p>
            </form>
        </div>
    </div>

</body>
<script>
function togglePwd(id) {
    const i = document.getElementById(id);
    i.type = i.type === 'password' ? 'text' : 'password';
}
</script>
</html><?php /**PATH C:\Project\gestion-CF\resources\views/auth/register.blade.php ENDPATH**/ ?>