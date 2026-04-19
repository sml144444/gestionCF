<?php

namespace App\Http\Controllers;

use App\Models\Module;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Spatie\Permission\Models\Role;

class UserManagementController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:admin']);
    }

    // ── INDEX ─────────────────────────────────────────────────────────────────
    public function index(Request $request)
    {
        $search     = $request->get('search', '');
        $filterRole = $request->get('role', '');

        $users = User::with(['roles', 'modules'])
            ->when($search, fn($q) => $q->where(fn($q) =>
                $q->where('name',  'like', "%$search%")
                  ->orWhere('email', 'like', "%$search%")
                  ->orWhere('cin',   'like', "%$search%")
            ))
            ->when($filterRole, fn($q) => $q->where('role', $filterRole))
            ->whereIn('role', ['formateur', 'gestionnaire'])
            ->orderBy('role')->orderBy('name')
            ->paginate(15)
            ->withQueryString();

$spatieRoles = \Spatie\Permission\Models\Role::with('permissions')
    ->whereNotIn('name', ['admin', 'stagiaire'])
    ->get();

        $stats = [
            'total'        => User::whereIn('role', ['formateur', 'gestionnaire'])->count(),
            'gestionnaire' => User::where('role', 'gestionnaire')->count(),
            'formateur'    => User::where('role', 'formateur')->count(),
        ];

        return view('users.index', compact('users', 'spatieRoles', 'search', 'filterRole', 'stats'));
    }

    // ── CREATE ────────────────────────────────────────────────────────────────
    public function create(Request $request)
    {
        $role    = $request->get('role', 'formateur');
        $modules = Module::orderBy('name')->get();

        return view('users.create', compact('role', 'modules'));
    }

    // ── STORE ─────────────────────────────────────────────────────────────────
    public function store(Request $request): RedirectResponse
    {
        $role = $request->input('role', 'formateur');

        $validated = $request->validate([
            'name'           => ['required', 'string', 'max:100'],
            'email'          => ['required', 'email', 'unique:users,email'],
            'password'       => ['required', 'string', 'min:8', 'confirmed'],
            'role'           => ['required', Rule::in(['formateur', 'gestionnaire'])],
            'cin'            => ['nullable', 'string', 'max:20'],
            'phone'          => ['nullable', 'string', 'max:20'],
            'date_naissance' => ['nullable', 'date'],
            'photo'          => ['nullable', 'image', 'max:2048'],
            'matricule_formateur' => ['nullable', 'string', 'max:50'],
            'date_embauche'       => ['nullable', 'date'],
            'nbr_heure_limit'     => ['nullable', 'integer', 'min:0'],
            'modules'             => ['nullable', 'array'],
            'modules.*'           => ['integer', 'exists:modules,id'],
        ]);

        if ($request->hasFile('photo')) {
            $validated['photo'] = $request->file('photo')->store('photos/users', 'public');
        }

        $validated['password']   = Hash::make($validated['password']);
        $validated['specialite'] = null;

        $user = User::create($validated);

        if (Role::where('name', $role)->exists()) {
            $user->syncRoles([$role]);
        }

        if ($role === 'formateur' && !empty($validated['modules'])) {
            Module::whereIn('id', $validated['modules'])
                  ->update(['id_user' => $user->id]);
        }

        return redirect()
            ->route('users.management.index')
            ->with('success', "Utilisateur « {$user->name} » créé avec succès.");
    }

    // ── EDIT ──────────────────────────────────────────────────────────────────
    public function edit(User $user)
    {
        abort_unless(in_array($user->role, ['formateur', 'gestionnaire']), 403);

        $modules        = Module::orderBy('name')->get();
        $assignedModIds = $user->modules->pluck('id')->toArray();

        return view('users.edit', compact('user', 'modules', 'assignedModIds'));
    }

    // ── UPDATE ────────────────────────────────────────────────────────────────
    public function update(Request $request, User $user): RedirectResponse
    {
        abort_unless(in_array($user->role, ['formateur', 'gestionnaire']), 403);

        $role = $request->input('role', $user->role);

        $rules = [
            'name'           => ['required', 'string', 'max:100'],
            'email'          => ['required', 'email', Rule::unique('users', 'email')->ignore($user->id)],
            'role'           => ['required', Rule::in(['formateur', 'gestionnaire'])],
            'cin'            => ['nullable', 'string', 'max:20'],
            'phone'          => ['nullable', 'string', 'max:20'],
            'date_naissance' => ['nullable', 'date'],
            'photo'          => ['nullable', 'image', 'max:2048'],
            'matricule_formateur' => ['nullable', 'string', 'max:50'],
            'date_embauche'       => ['nullable', 'date'],
            'nbr_heure_limit'     => ['nullable', 'integer', 'min:0'],
            'modules'             => ['nullable', 'array'],
            'modules.*'           => ['integer', 'exists:modules,id'],
        ];

        if ($request->filled('password')) {
            $rules['password'] = ['string', 'min:8', 'confirmed'];
        }

        $validated = $request->validate($rules);

        if ($request->hasFile('photo')) {
            $validated['photo'] = $request->file('photo')->store('photos/users', 'public');
        }

        if ($request->filled('password')) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }

        $validated['specialite'] = null;
        $user->update($validated);

        if (Role::where('name', $role)->exists()) {
            $user->syncRoles([$role]);
        }

        Module::where('id_user', $user->id)->update(['id_user' => null]);
        if ($role === 'formateur' && !empty($validated['modules'])) {
            Module::whereIn('id', $validated['modules'])
                  ->update(['id_user' => $user->id]);
        }

        return redirect()
            ->route('users.management.index')
            ->with('success', "Utilisateur « {$user->name} » mis à jour.");
    }

    // ── DESTROY ───────────────────────────────────────────────────────────────
    public function destroy(User $user): RedirectResponse
    {
        abort_unless(in_array($user->role, ['formateur', 'gestionnaire']), 403);

        Module::where('id_user', $user->id)->update(['id_user' => null]);

        $name = $user->name;
        $user->delete();

        return redirect()
            ->route('users.management.index')
            ->with('success', "Utilisateur « {$name} » supprimé.");
    }

    // ── UPDATE SPATIE ROLE (inline modal) ─────────────────────────────────────
    public function updateRole(Request $request, User $user): RedirectResponse
    {
$allowedRoles = \Spatie\Permission\Models\Role::whereNotIn('name', ['admin', 'stagiaire'])
    ->pluck('name')
    ->implode(',');

$request->validate([
    'spatie_role' => 'required|in:' . $allowedRoles,
]);

        $user->syncRoles([$request->spatie_role]);

        return redirect()
            ->route('users.management.index', $request->only(['search', 'role']))
            ->with('success', "Rôle de « {$user->name} » mis à jour → {$request->spatie_role}");
    }
}