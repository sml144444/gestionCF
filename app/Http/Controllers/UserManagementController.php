<?php

namespace App\Http\Controllers;

use App\Mail\WelcomeMail;
use App\Models\EmploiDuTemps;
use App\Models\Module;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rule;
use Spatie\Permission\Models\Role;

class UserManagementController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth']);
        $this->middleware(function ($request, $next) {
            $user = auth()->user();
            if (!$user->canAny(['user-manage-formateur', 'user-manage-gestionnaire'])) {
                abort(403);
            }
            return $next($request);
        })->only(['index', 'create', 'store', 'edit', 'update', 'updateRole']);
        // NOTE: 'show' is intentionally excluded here — it handles its own
        // authorization internally to also allow formateurs to view stagiaire profiles.

        $this->middleware(function ($request, $next) {
            $user = auth()->user();
            if (!$user->canAny(['user-manage-formateur', 'user-manage-gestionnaire'])) {
                abort(403);
            }
            return $next($request);
        })->only(['destroy']);
    }

    // ── HELPER — roles the current user may manage ────────────────────────────
    private function allowedRoles(): array
    {
        $roles = [];
        if (auth()->user()->can('user-manage-formateur'))    $roles[] = 'formateur';
        if (auth()->user()->can('user-manage-gestionnaire')) $roles[] = 'gestionnaire';
        return $roles;
    }

    // ── INDEX ─────────────────────────────────────────────────────────────────
    public function index(Request $request)
    {
        $allowed = $this->allowedRoles();
        if (empty($allowed)) abort(403);

        $search     = $request->get('search', '');
        $filterRole = $request->get('role', '');

        if ($filterRole && !in_array($filterRole, $allowed)) {
            $filterRole = '';
        }

        $users = User::with(['roles', 'modules'])
            ->when($search, fn($q) => $q->where(fn($q) =>
                $q->where('name',  'like', "%$search%")
                  ->orWhere('email', 'like', "%$search%")
                  ->orWhere('cin',   'like', "%$search%")
            ))
            ->when($filterRole, fn($q) => $q->where('role', $filterRole))
            ->whereIn('role', $allowed)
            ->orderByDesc('created_at')
            ->paginate(15)
            ->withQueryString();

        $spatieRoles = \Spatie\Permission\Models\Role::with('permissions')
            ->whereNotIn('name', ['admin', 'stagiaire'])
            ->get();

        $canManageFormateur    = in_array('formateur',    $allowed);
        $canManageGestionnaire = in_array('gestionnaire', $allowed);

        $stats = [
            'total'        => User::whereIn('role', $allowed)->count(),
            'gestionnaire' => $canManageGestionnaire
                ? User::where('role', 'gestionnaire')->count() : null,
            'formateur'    => $canManageFormateur
                ? User::where('role', 'formateur')->count() : null,
        ];

        return view('users.index', compact(
            'users', 'spatieRoles', 'search', 'filterRole', 'stats',
            'canManageFormateur', 'canManageGestionnaire', 'allowed'
        ));
    }

    // ── CREATE ────────────────────────────────────────────────────────────────
    public function create(Request $request)
    {
        $allowed = $this->allowedRoles();
        if (empty($allowed)) abort(403);

        $canManageFormateur    = in_array('formateur',    $allowed);
        $canManageGestionnaire = in_array('gestionnaire', $allowed);

        $requestedRole = $request->get('role', $allowed[0]);
        $role = in_array($requestedRole, $allowed) ? $requestedRole : $allowed[0];

        $modules = Module::orderBy('name')->get();

        return view('users.create', compact(
            'role', 'modules', 'canManageFormateur', 'canManageGestionnaire'
        ));
    }

    // ── STORE ─────────────────────────────────────────────────────────────────
    public function store(Request $request): RedirectResponse
    {
        $allowed = $this->allowedRoles();
        $role    = $request->input('role', $allowed[0] ?? 'formateur');

        if (!in_array($role, $allowed)) {
            abort(403, "Vous n'avez pas la permission de créer un compte « {$role} ».");
        }

        $validated = $request->validate([
            'name'            => ['required', 'string', 'max:100'],
            'email'           => ['required', 'email', 'unique:users,email'],
            'role'            => ['required', Rule::in($allowed)],
            'cin'             => ['nullable', 'string', 'max:20'],
            'phone'           => ['nullable', 'string', 'max:20', 'regex:/^[\+]?[0-9\s\-\(\)\.]{6,20}$/'],
            'date_naissance'  => ['nullable', 'date'],
            'photo'           => ['nullable', 'image', 'max:2048'],
            'date_embauche'   => ['nullable', 'date'],
            'nbr_heure_limit' => ['nullable', 'integer', 'min:0'],
            'modules'         => ['nullable', 'array'],
            'modules.*'       => ['integer', 'exists:modules,id'],
        ]);

        if ($request->hasFile('photo')) {
            $validated['photo'] = $request->file('photo')->store('photos/users', 'public');
        }

        $plainPassword = $this->generateSecurePassword();

        $validated['password']        = Hash::make($plainPassword);
        $validated['specialite']      = null;
        $validated['nbr_heure_limit'] = $validated['nbr_heure_limit'] ?? 30;

        $user = User::create($validated);

        $prefix    = strtoupper(substr($role, 0, 1));
        $paddedId  = str_pad($user->id, 4, '0', STR_PAD_LEFT);
        $timestamp = now()->format('YmdHis');
        $user->update(['matricule_formateur' => "{$prefix}{$paddedId}{$timestamp}"]);

        if (Role::where('name', $role)->exists()) {
            $user->syncRoles([$role]);
        }

        if ($role === 'formateur' && !empty($validated['modules'])) {
            Module::whereIn('id', $validated['modules'])
                  ->update(['id_user' => $user->id]);
        }

        Mail::to($user->email)->queue(new WelcomeMail($user, $plainPassword));

        return redirect()
            ->route('users.management.index')
            ->with('success', "Utilisateur « {$user->name} » créé. Ses accès ont été envoyés par e-mail.");
    }

    // ── SHOW — unified for stagiaire + formateur + gestionnaire ──────────────
    public function show(User $user)
    {
        // Staff roles this auth user can manage
        $staffAllowed = $this->allowedRoles();              // ['formateur','gestionnaire']

        // Allow staff profiles AND stagiaire profiles through the same method.
        // Formateurs reach here via stagiaire.show (which has its own middleware),
        // so we only gate staff profiles against allowedRoles().
        if ($user->role === 'stagiaire') {
            // Any authenticated user who can reach this route may view a stagiaire
            // (route-level middleware already checked can:stagiaire-list)
        } else {
            abort_unless(in_array($user->role, $staffAllowed), 403);
        }

        // Eager-load everything the unified view may need —
        // unused relations are just empty collections, no cost.
        $user->load(['modules', 'roles', 'filiere', 'groupe', 'absences']);

        return view('users.show', compact('user'));
    }

    // ── EDIT ──────────────────────────────────────────────────────────────────
    public function edit(User $user)
    {
        $allowed = $this->allowedRoles();
        abort_unless(in_array($user->role, $allowed), 403);

        $canManageFormateur    = in_array('formateur',    $allowed);
        $canManageGestionnaire = in_array('gestionnaire', $allowed);

        $modules        = Module::orderBy('name')->get();
        $assignedModIds = $user->modules->pluck('id')->toArray();

        return view('users.edit', compact(
            'user', 'modules', 'assignedModIds',
            'canManageFormateur', 'canManageGestionnaire', 'allowed'
        ));
    }

    // ── UPDATE ────────────────────────────────────────────────────────────────
    public function update(Request $request, User $user): RedirectResponse
    {
        $allowed = $this->allowedRoles();
        abort_unless(in_array($user->role, $allowed), 403);

        $role = $request->input('role', $user->role);

        if (!in_array($role, $allowed)) {
            abort(403, "Vous n'avez pas la permission d'assigner le rôle « {$role} ».");
        }

        $rules = [
            'name'            => ['required', 'string', 'max:100'],
            'email'           => ['required', 'email', Rule::unique('users', 'email')->ignore($user->id)],
            'role'            => ['required', Rule::in($allowed)],
            'cin'             => ['nullable', 'string', 'max:20'],
            'phone'           => ['nullable', 'string', 'max:20', 'regex:/^[\+]?[0-9\s\-\(\)\.]{6,20}$/'],
            'date_naissance'  => ['nullable', 'date'],
            'photo'           => ['nullable', 'image', 'max:2048'],
            'date_embauche'   => ['nullable', 'date'],
            'nbr_heure_limit' => ['nullable', 'integer', 'min:0'],
            'modules'         => ['nullable', 'array'],
            'modules.*'       => ['integer', 'exists:modules,id'],
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
        $allowed = $this->allowedRoles();
        abort_unless(in_array($user->role, $allowed), 403);

        Module::where('id_user', $user->id)->update(['id_user' => null]);
        $name = $user->name;
        $user->delete();

        return redirect()
            ->route('users.management.index')
            ->with('success', "Utilisateur « {$name} » supprimé.");
    }

    // ── UPDATE SPATIE ROLE ────────────────────────────────────────────────────
    public function updateRole(Request $request, User $user): RedirectResponse
    {
        $allowed = $this->allowedRoles();
        abort_unless(in_array($user->role, $allowed), 403);

        $allowedRoleNames = \Spatie\Permission\Models\Role::whereNotIn('name', ['admin', 'stagiaire'])
            ->pluck('name')->implode(',');

        $request->validate(['spatie_role' => 'required|in:' . $allowedRoleNames]);
        $user->syncRoles([$request->spatie_role]);

        return redirect()
            ->route('users.management.index', $request->only(['search', 'role']))
            ->with('success', "Rôle de « {$user->name} » mis à jour → {$request->spatie_role}");
    }

    // ── UNIVERSAL SEARCH ──────────────────────────────────────────────────────
    public function searchAll(Request $request)
    {
        $q = trim($request->get('q', ''));

        if (strlen($q) < 2) {
            return response()->json([]);
        }

        $authUser = Auth::user();

        $query = User::where(fn($sq) =>
                $sq->where('name',  'like', "%{$q}%")
                   ->orWhere('email', 'like', "%{$q}%")
                   ->orWhere('cin',   'like', "%{$q}%")
            )
            ->with(['groupe', 'filiere']);

        // ── Formateur: only their own stagiaires ──────────────────────────────
        if ($authUser->role === 'formateur') {
            $moduleIds = Module::where('id_user', $authUser->id)
                ->orWhere('id_user_remplacant', $authUser->id)
                ->pluck('id');

            $groupeIds = EmploiDuTemps::whereIn('id_module', $moduleIds)
                ->pluck('id_groupe')
                ->unique();

            $query->where('role', 'stagiaire')
                  ->whereIn('id_groupe', $groupeIds);

        // ── Admin / Gestionnaire: everyone except themselves ──────────────────
        } else {
            $query->whereIn('role', ['stagiaire', 'formateur', 'gestionnaire'])
                  ->where('id', '!=', $authUser->id);
        }

        $results = $query->orderBy('name')->limit(10)->get()
            ->map(fn($u) => [
                'id'     => $u->id,
                'name'   => $u->name,
                'role'   => $u->role,
                'filiere'=> $u->filiere?->name
                            ?? ($u->role !== 'stagiaire' ? ucfirst($u->role) : '—'),
                'groupe' => $u->groupe?->name ?? '—',
                'promo'  => $u->groupe?->promo_label
                            ?? ($u->role !== 'stagiaire' ? $u->email : '—'),
                // All roles now resolve to the same show route
                'url'    => match ($u->role) {
                    'stagiaire' => route('stagiaire.show', $u->id),
                    default     => route('users.management.show', $u->id),
                },
            ]);

        return response()->json($results);
    }

    // ── PRIVATE ───────────────────────────────────────────────────────────────

    private function generateSecurePassword(int $length = 12): string
    {
        $upper   = 'ABCDEFGHJKLMNPQRSTUVWXYZ';
        $lower   = 'abcdefghjkmnpqrstuvwxyz';
        $digits  = '23456789';
        $special = '@#$%!&*';

        $password  = substr(str_shuffle($upper),   0, 2);
        $password .= substr(str_shuffle($lower),   0, 3);
        $password .= substr(str_shuffle($digits),  0, 3);
        $password .= substr(str_shuffle($special), 0, 2);

        $all      = $upper . $lower . $digits . $special;
        $password .= substr(str_shuffle(str_repeat($all, 3)), 0, $length - 10);

        return str_shuffle($password);
    }
}