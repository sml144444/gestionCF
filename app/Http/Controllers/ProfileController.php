<?php

namespace App\Http\Controllers;

use App\Mail\PasswordChangedMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    // ─────────────────────────────────────────────────────────────────────────
    // SHOW
    // ─────────────────────────────────────────────────────────────────────────

    public function show()
    {
        $user = Auth::user()->load(['filiere', 'groupe', 'modules']);
        return view('profile.show', compact('user'));
    }

    // ─────────────────────────────────────────────────────────────────────────
    // UPDATE PROFILE INFO
    // ─────────────────────────────────────────────────────────────────────────

    public function update(Request $request)
    {
        $user = Auth::user();

        $rules = [
            'name'           => 'required|string|max:255',
            'email'          => 'required|email|unique:users,email,' . $user->id,
            'phone'          => 'nullable|string|max:30',
            'cin'            => 'nullable|string|max:50',
            'date_naissance' => 'nullable|date',
        ];

        // Formateur-specific fields
        if ($user->isFormateur()) {
            $rules['matricule_formateur'] = 'nullable|string|max:50';
            $rules['date_embauche']       = 'nullable|date';
        }

        $data = $request->validate($rules);
        $user->update($data);

        return back()->with('success', 'Profil mis à jour avec succès.');
    }

    // ─────────────────────────────────────────────────────────────────────────
    // CHANGE PASSWORD
    // ─────────────────────────────────────────────────────────────────────────

    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password'      => 'required|string',
            'password'              => 'required|string|min:8|confirmed',
        ]);

        $user = Auth::user();

        if (! Hash::check($request->current_password, $user->password)) {
            return back()
                ->withErrors(['current_password' => 'Le mot de passe actuel est incorrect.'])
                ->with('open_password_modal', true);
        }

        $user->update(['password' => Hash::make($request->password)]);

        // Send security notification email (queued)
        Mail::to($user->email)->queue(
            new PasswordChangedMail($user, $request->ip() ?? 'inconnue')
        );

        return back()->with('success', 'Mot de passe changé avec succès. Un e-mail de confirmation vous a été envoyé.');
    }

    // ─────────────────────────────────────────────────────────────────────────
    // CHANGE PHOTO
    // ─────────────────────────────────────────────────────────────────────────

    public function updatePhoto(Request $request)
    {
        $request->validate([
            'photo' => 'required|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        $user = Auth::user();

        // Delete old photo if exists
        if ($user->photo && Storage::disk('public')->exists($user->photo)) {
            Storage::disk('public')->delete($user->photo);
        }

        $path = $request->file('photo')->store('photos/users', 'public');
        $user->update(['photo' => $path]);

        return back()->with('success', 'Photo de profil mise à jour.');
    }
}