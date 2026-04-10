<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Edu;
use App\Models\Filiere;
use App\Models\Groupe;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class RegisteredStagiaireController extends Controller
{
    /**
     * Show the minimal registration form.
     * Only: edu_email | password | password_confirmation
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle registration.
     * All user data (name, filiere, groupe) is pulled from the EDU table.
     */
    public function store(Request $request): RedirectResponse
    {
        // 1. Validate — only 3 fields needed
        $request->validate([
            'email'    => ['required', 'string', 'email'],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
        ]);

        // 2. Find EDU entry
        $edu = Edu::where('edu_email', $request->email)->first();

        // 3. If EDU not found
        if (! $edu) {
            return back()
                ->withInput($request->only('email'))
                ->withErrors([
                    'email' => 'Email EDU introuvable.',
                ]);
        }

        // 4. If already used
        if ($edu->used) {
            return back()
                ->withInput($request->only('email'))
                ->withErrors([
                    'email' => 'Ce compte est déjà activé. Veuillez vous connecter.',
                ]);
        }

        // 5. Check password against EDU password
        if (! Hash::check($request->password, $edu->password)) {
            return back()
                ->withInput($request->only('email'))
                ->withErrors([
                    'password' => 'Mot de passe incorrect.',
                ]);
        }

        // 6. Reject if a users account already exists with this email
        if (User::where('email', $request->email)->exists()) {
            return back()
                ->withInput($request->only('email'))
                ->withErrors([
                    'email' => 'Un compte existe déjà avec cet email.',
                ]);
        }

        // 7. Resolve filière from code
        $filiere = Filiere::where('code', $edu->filiere_code)->first();
        if (! $filiere) {
            return back()
                ->withInput($request->only('email'))
                ->withErrors([
                    'email' => 'Filière introuvable (code : ' . $edu->filiere_code . '). Contactez l\'administration.',
                ]);
        }

        // 8. Resolve groupe from code
        $groupe = Groupe::where('code', $edu->groupe_code)->first();
        if (! $groupe) {
            return back()
                ->withInput($request->only('email'))
                ->withErrors([
                    'email' => 'Groupe introuvable (code : ' . $edu->groupe_code . '). Contactez l\'administration.',
                ]);
        }

        // 9. Create user — name auto-filled from EDU (nom + prenom)
        $user = User::create([
            'name'       => trim($edu->nom . ' ' . $edu->prenom),
            'email'      => $edu->edu_email,
            'password'   => Hash::make($request->password),
            'role'       => 'stagiaire',
            'id_filiere' => $filiere->id,
            'id_groupe'  => $groupe->id,
        ]);

        // 10. ── CRITICAL ──────────────────────────────────────────
        //     Assign the Spatie 'stagiaire' role.
        //     Without this, hasPermissionTo('emploi-view') returns false
        //     and the timetable sidebar link and page are never shown.
        $user->syncRoles(['stagiaire']);

        // 11. Mark EDU row as used — prevents duplicate registration
        $edu->update(['used' => true]);

        // 12. Fire Registered event
        event(new Registered($user));

        // 13. Auto login
        Auth::login($user);

        // 14. Redirect to stagiaire dashboard
        return redirect()->route('stagiaire.dashboard');
    }
}