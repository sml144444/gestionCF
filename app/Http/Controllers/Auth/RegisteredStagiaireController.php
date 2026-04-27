<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\WelcomeStagiaireMail;
use App\Models\Edu;
use App\Models\Filiere;
use App\Models\Groupe;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;

class RegisteredStagiaireController extends Controller
{
    public function create(): View
    {
        return view('auth.register');
    }

    public function store(Request $request): RedirectResponse
    {
        // 1. Validate
        $request->validate([
            'email'          => ['required', 'string', 'email'],
            'personal_email' => ['required', 'string', 'email', 'different:email'],
            'password'       => ['required', 'string', 'min:6'],
        ]);

        // 2. Find EDU entry
        $edu = Edu::where('edu_email', $request->email)->first();

        if (! $edu) {
            return back()->withInput($request->only('email', 'personal_email'))
                ->withErrors(['email' => 'Email EDU introuvable.']);
        }

        if ($edu->used) {
            return back()->withInput($request->only('email', 'personal_email'))
                ->withErrors(['email' => 'Ce compte est déjà activé. Veuillez vous connecter.']);
        }

        if (! Hash::check($request->password, $edu->password)) {
            return back()->withInput($request->only('email', 'personal_email'))
                ->withErrors(['password' => 'Mot de passe EDU incorrect.']);
        }

        if (User::where('email', $request->personal_email)->exists()) {
            return back()->withInput($request->only('email', 'personal_email'))
                ->withErrors(['personal_email' => 'Un compte existe déjà avec cet email personnel.']);
        }

        // 3. Resolve filière
        $filiere = Filiere::where('code', $edu->filiere_code)->first();
        if (! $filiere) {
            return back()->withInput($request->only('email', 'personal_email'))
                ->withErrors(['email' => 'Filière introuvable. Contactez l\'administration.']);
        }

        // 4. Resolve groupe
        $groupe = Groupe::where('code', $edu->groupe_code)->first();
        if (! $groupe) {
            return back()->withInput($request->only('email', 'personal_email'))
                ->withErrors(['email' => 'Groupe introuvable. Contactez l\'administration.']);
        }

        // 5. Create user with personal_email
        $plainPassword = $request->password;

        $user = User::create([
            'name'       => trim($edu->prenom . ' ' . $edu->nom),
            'email'      => $request->personal_email,   // ← personal email
            'password'   => Hash::make($plainPassword),
            'role'       => 'stagiaire',
            'id_filiere' => $filiere->id,
            'id_groupe'  => $groupe->id,
        ]);

        // 6. Assign Spatie role
        $user->syncRoles(['stagiaire']);

        // 7. Mark EDU as used
        $edu->update(['used' => true]);

        // 8. Fire Registered event
        event(new Registered($user));

        // 9. Send welcome email with password to personal_email
        try {
            Mail::to($user->email)->send(new WelcomeStagiaireMail($user, $plainPassword));
        } catch (\Throwable $e) {
            logger()->warning('Welcome email failed for ' . $user->email . ': ' . $e->getMessage());
        }

        // 10. Redirect to login (NOT auto-login)
        return redirect()->route('login')
            ->with('status', 'Compte créé ! Connectez-vous avec votre email personnel et votre mot de passe EDU.');
    }
}