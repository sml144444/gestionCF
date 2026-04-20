{{-- resources/views/auth/forgot-password.blade.php --}}
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>OFPPT – Mot de passe oublié</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-screen w-screen overflow-hidden flex">

    {{-- ── LEFT PANEL ── --}}
    <div class="hidden lg:flex w-[45%] h-full flex-col items-center justify-around py-20 px-12
                bg-gradient-to-br from-[#1a5fa8] via-[#0d4a85] to-[#0a7a5e] relative overflow-hidden">

        <div class="absolute -top-24 -left-24 w-80 h-80 rounded-full bg-white/5"></div>
        <div class="absolute -bottom-32 -right-20 w-96 h-96 rounded-full bg-white/5"></div>

        <div class="relative z-10 flex flex-col items-center">
            <div class="w-28 h-28 rounded-full overflow-hidden bg-white shadow-xl flex items-center justify-center mb-6">
                <img src="{{ asset('images/ofppt-logo.webp') }}" alt="OFPPT" class="w-full h-full object-cover">
            </div>
            <p class="text-white/90 text-2xl italic">La voie de l'avenir</p>
        </div>

        <div class="relative z-10 text-center px-6 max-w-md">
            <p class="text-white/80 text-2xl font-semibold leading-snug mb-4">
                Récupération de compte
            </p>
            <p class="text-white/70 text-sm leading-relaxed">
                Entrez votre adresse email et nous vous enverrons un lien pour
                réinitialiser votre mot de passe en toute sécurité.
            </p>
        </div>

        <div class="relative z-10 bg-white/10 border border-white/20 rounded-2xl px-5 py-4 w-full">
            <div class="space-y-3">
                @foreach([
                    ['📧', 'Entrez votre email', 'L\'adresse associée à votre compte OFPPT'],
                    ['🔗', 'Recevez le lien', 'Un lien sécurisé valable 60 minutes'],
                    ['🔑', 'Nouveau mot de passe', 'Choisissez un mot de passe solide'],
                ] as [$icon, $title, $desc])
                <div class="flex items-center gap-3">
                    <span class="text-xl">{{ $icon }}</span>
                    <div>
                        <p class="text-white text-sm font-semibold">{{ $title }}</p>
                        <p class="text-white/60 text-xs">{{ $desc }}</p>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- ── RIGHT PANEL ── --}}
    <div class="flex-1 h-full flex flex-col justify-center items-center bg-white px-6 overflow-y-auto">

        <div class="lg:hidden flex items-center gap-3 mb-10">
            <div class="w-12 h-12 rounded-full bg-[#1a5fa8] flex items-center justify-center shadow">
                <span class="text-white font-bold text-base">OF</span>
            </div>
            <div>
                <p class="font-bold text-[#1a5fa8] text-lg leading-none tracking-widest">OFPPT</p>
                <p class="text-xs text-gray-400 italic">La voie de l'avenir</p>
            </div>
        </div>

        <div class="w-full max-w-md">

            {{-- Icon --}}
            <div class="flex justify-center mb-6">
                <div class="w-16 h-16 rounded-full bg-blue-50 border border-blue-100 flex items-center justify-center">
                    <svg class="w-8 h-8 text-[#1a5fa8]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                              d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/>
                    </svg>
                </div>
            </div>

            <h2 class="text-2xl font-semibold text-center text-slate-800 mb-2">Mot de passe oublié ?</h2>
            <p class="text-sm text-slate-500 text-center mb-8 leading-relaxed">
                Pas de problème. Entrez votre adresse email et nous vous enverrons
                un lien de réinitialisation.
            </p>

            {{-- Success --}}
            @if (session('status'))
                <div class="mb-6 flex items-start gap-3 bg-emerald-50 border border-emerald-200
                            rounded-xl px-4 py-3 text-sm text-emerald-700">
                    <svg class="w-5 h-5 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                    <span>{{ session('status') }}</span>
                </div>
            @endif

            <form method="POST" action="{{ route('password.email') }}" class="space-y-5">
                @csrf

                <div>
                    <label for="email"
                           class="block text-xs font-semibold text-slate-500 uppercase tracking-widest mb-2">
                        Adresse email
                    </label>
                    <input id="email" name="email" type="email"
                           value="{{ old('email') }}"
                           required autofocus autocomplete="username"
                           placeholder="votre@email.com"
                           class="w-full h-12 px-4 rounded-xl border bg-slate-50 text-sm
                                  text-slate-800 placeholder-slate-400 transition-all
                                  focus:outline-none focus:bg-white focus:ring-2 focus:ring-[#1a5fa8]/20
                                  {{ $errors->has('email') ? 'border-red-400 bg-red-50' : 'border-slate-200 focus:border-[#1a5fa8]' }}" />
                    @error('email')
                        <p class="text-xs text-red-500 mt-1.5 flex items-center gap-1">
                            <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                            </svg>
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                <button type="submit"
                        class="w-full h-12 bg-[#1a5fa8] hover:bg-[#0d4a85] active:scale-[0.98]
                               text-white text-sm font-semibold rounded-xl transition-all shadow-md shadow-[#1a5fa8]/30">
                    Envoyer le lien de réinitialisation
                </button>

                <p class="text-center text-sm text-slate-500">
                    <a href="{{ route('login') }}"
                       class="text-[#1a5fa8] font-semibold hover:underline inline-flex items-center gap-1">
                        ← Retour à la connexion
                    </a>
                </p>
            </form>
        </div>
    </div>

</body>
</html>