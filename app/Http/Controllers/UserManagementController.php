<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Spatie\Permission\Models\Role;

class UserManagementController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:admin']);
    }

    // ── INDEX — liste tous les users ──────────────────────────
    public function index(Request $request)
    {
        $search     = $request->get('search', '');
        $filterRole = $request->get('role', '');

        $users = User::with('roles')
            ->when($search, fn($q) => $q->where('name', 'like', "%$search%")
                                        ->orWhere('email', 'like', "%$search%"))
            ->when($filterRole, fn($q) => $q->where('role', $filterRole))
            ->where('role', '!=', 'stagiaire')   // stagiaires gérés ailleurs
            ->orderBy('role')->orderBy('name')
            ->paginate(15)
            ->withQueryString();

        $spatieRoles = Role::orderBy('name')->get();

        return view('users.index', compact('users', 'spatieRoles', 'search', 'filterRole'));
    }

    // ── UPDATE ROLE — change le rôle Spatie d'un user ─────────
    public function updateRole(Request $request, User $user): RedirectResponse
    {
        $request->validate([
            'spatie_role' => 'required|exists:roles,name',
        ]);

        // Sync = remplace tous les rôles Spatie existants par le nouveau
        $user->syncRoles([$request->spatie_role]);

        return redirect()
            ->route('users.management.index', $request->only(['search', 'role']))
            ->with('success', "Rôle de « {$user->name} » mis à jour → {$request->spatie_role}");
    }
}