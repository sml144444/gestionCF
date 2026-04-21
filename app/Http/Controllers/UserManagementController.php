<?php

namespace App\Http\Controllers;

use App\Mail\WelcomeMail;
use App\Models\Module;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Spatie\Permission\Models\Role;

class UserManagementController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth']);

        $this->middleware('can:user-list')->only(['index']);
        $this->middleware('can:user-create')->only(['create', 'store']);
        $this->middleware('can:user-edit')->only(['edit', 'update', 'updateRole']);
        $this->middleware('role:admin')->only(['destroy']);
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
            ->orderByDesc('created_at')
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
        // Gestionnaire can only create formateur — force role and hide the tab
        $canCreateGestionnaire = auth()->user()->role === 'admin';

        $role = $request->get('role', 'formateur');
        if (!$canCreateGestionnaire) {
            $role = 'formateur'; // hard-lock regardless of query string
        }

        $modules = Module::orderBy('name')->get();

        return view('users.create', compact('role', 'modules', 'canCreateGestionnaire'));
    }

    // ── STORE ─────────────────────────────────────────────────────────────────
    public function store(Request $request): RedirectResponse
    {
        $role = $request->input('role', 'formateur');

        // Security: gestionnaire is only allowed to create formateur accounts
        if (auth()->user()->role === 'gestionnaire') {
            $role = 'formateur';
        }

        $validated = $request->validate([
            'name'           => ['required', 'string', 'max:100'],
            'email'          => ['required', 'email', 'unique:users,email'],
            'role'           => ['required', Rule::in(['formateur', 'gestionnaire'])],
            'cin'            => ['nullable', 'string', 'max:20'],
            'phone'          => ['nullable', 'string', 'max:20'],
            'date_naissance' => ['nullable', 'date'],
            'photo'          => ['nullable', 'image', 'max:2048'],
            'date_embauche'       => ['nullable', 'date'],
            'nbr_heure_limit'     => ['nullable', 'integer', 'min:0'],
            'modules'             => ['nullable', 'array'],
            'modules.*'           => ['integer', 'exists:modules,id'],
        ]);

        if ($request->hasFile('photo')) {
            $validated['photo'] = $request->file('photo')->store('photos/users', 'public');
        }

        // ── 1. Auto-generate a secure password ────────────────────────────────
        // Format: Uppercase + lowercase + digits + special chars, 12 characters
        $plainPassword = $this->generateSecurePassword();

        $validated['password']        = Hash::make($plainPassword);
        $validated['specialite']      = null;
        $validated['nbr_heure_limit'] = $validated['nbr_heure_limit'] ?? 30;

        // ── 2. Create user (without matricule yet — we need the ID first) ──────
        $user = User::create($validated);

        // ── 3. Auto-generate matricule using prefix + ID + timestamp ──────────
        //       Format: F0042-20250421153045  (role prefix + zero-padded ID + datetime)
        $prefix     = strtoupper(substr($role, 0, 1));          // 'F' or 'G'
        $paddedId   = str_pad($user->id, 4, '0', STR_PAD_LEFT); // e.g. "0042"
        $timestamp  = now()->format('YmdHis');                   // e.g. "20250421153045"
        $matricule  = "{$prefix}{$paddedId}{$timestamp}";        // e.g. "F004220250421153045"

        $user->update(['matricule_formateur' => $matricule]);

        // ── 4. Sync Spatie role ───────────────────────────────────────────────
        if (Role::where('name', $role)->exists()) {
            $user->syncRoles([$role]);
        }

        // ── 5. Assign modules (formateur only) ───────────────────────────────
        if ($role === 'formateur' && !empty($validated['modules'])) {
            Module::whereIn('id', $validated['modules'])
                  ->update(['id_user' => $user->id]);
        }

        // ── 6. Send welcome email with credentials ────────────────────────────
        Mail::to($user->email)->queue(new WelcomeMail($user, $plainPassword));

        return redirect()
            ->route('users.management.index')
            ->with('success', "Utilisateur « {$user->name} » créé. Un e-mail avec ses accès a été envoyé.");
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

    // ── UPDATE SPATIE ROLE ────────────────────────────────────────────────────
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

    // ── PRIVATE HELPERS ───────────────────────────────────────────────────────

    /**
     * Generate a cryptographically secure password.
     * Guarantees: ≥2 uppercase, ≥2 lowercase, ≥2 digits, ≥2 special chars, 12 chars total.
     */
    private function generateSecurePassword(int $length = 12): string
    {
        $upper   = 'ABCDEFGHJKLMNPQRSTUVWXYZ';   // no I/O to avoid confusion
        $lower   = 'abcdefghjkmnpqrstuvwxyz';     // no i/l/o
        $digits  = '23456789';                     // no 0/1
        $special = '@#$%!&*';

        // Guarantee at least 2 of each category
        $password  = substr(str_shuffle($upper),   0, 2);
        $password .= substr(str_shuffle($lower),   0, 3);
        $password .= substr(str_shuffle($digits),  0, 3);
        $password .= substr(str_shuffle($special), 0, 2);

        // Fill remaining length with mixed chars
        $all      = $upper . $lower . $digits . $special;
        $password .= substr(str_shuffle(str_repeat($all, 3)), 0, $length - 10);

        // Final shuffle
        return str_shuffle($password);
    }
}