<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Show the login page.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle login and redirect by role.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        // Authenticate using Breeze's LoginRequest (handles throttle, etc.)
        $request->authenticate();

        $request->session()->regenerate();

        // Redirect based on role
        return $this->redirectByRole(Auth::user()->role);
    }

    /**
     * Logout.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }

    /**
     * Returns a redirect response depending on user role.
     */
    private function redirectByRole(string $role): RedirectResponse
    {
        return match ($role) {
            'admin'        => redirect()->route('admin.dashboard'),
            'gestionnaire' => redirect()->route('gestionnaire.dashboard'),
            'formateur'    => redirect()->route('formateur.dashboard'),
            'stagiaire'    => redirect()->route('stagiaire.dashboard'),
            default        => redirect('/'),
        };
    }
}