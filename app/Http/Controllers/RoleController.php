<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    // ── INDEX ─────────────────────────────────────────────────
    public function index()
    {
        $roles = Role::with('permissions')->orderBy('name')->get();
        return view('roles.index', compact('roles'));
    }

    // ── CREATE ────────────────────────────────────────────────
    public function create()
    {
        $roles = Role::orderBy('name')->get();

        // No longer needed by the blade (groupLabels is the source of truth)
        // but kept for backwards compatibility
        $permissions = Permission::orderBy('name')->get()->groupBy(
            fn($p) => explode('-', $p->name)[0]
        );

        return view('roles.create', compact('permissions', 'roles'));
    }

    // ── STORE ─────────────────────────────────────────────────
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name'         => 'required|string|unique:roles,name|max:100',
            'permission'   => 'nullable|array',
            'permission.*' => 'string|max:100',
        ]);

        $role = Role::create(['name' => $request->name, 'guard_name' => 'web']);

        // ✅ firstOrCreate — auto-creates any permission not yet in DB
        $this->syncPermissionsFromRequest($role, $request->input('permission', []));

        // ✅ Also clear Spatie's permission cache
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        return redirect()->route('roles.index')
            ->with('success', 'Rôle « ' . $role->name . ' » créé avec succès.');
    }

    // ── SHOW ──────────────────────────────────────────────────
    public function show(Role $role)
    {
        $rolePermissions = $role->permissions()->orderBy('name')->get();
        return view('roles.show', compact('role', 'rolePermissions'));
    }

    // ── EDIT ──────────────────────────────────────────────────
    public function edit(Role $role)
    {
        $roles           = Role::orderBy('name')->get();
        $rolePermissions = $role->permissions->pluck('name')->toArray();

        // No longer needed by the blade but kept for backwards compatibility
        $permissions = Permission::orderBy('name')->get()->groupBy(
            fn($p) => explode('-', $p->name)[0]
        );

        return view('roles.edit', compact('role', 'permissions', 'rolePermissions', 'roles'));
    }

    // ── UPDATE ────────────────────────────────────────────────
    public function update(Request $request, Role $role): RedirectResponse
    {
        $request->validate([
            'name'         => 'required|string|unique:roles,name,' . $role->id . '|max:100',
            'permission'   => 'nullable|array',
            'permission.*' => 'string|max:100',
        ]);

        // System role names are locked (readonly in the blade, but double-check here)
        $isSystem = in_array($role->name, ['admin', 'gestionnaire', 'formateur', 'stagiaire']);
        if (! $isSystem) {
            $role->update(['name' => $request->name]);
        }

        // ✅ firstOrCreate — auto-creates any permission not yet in DB
        $this->syncPermissionsFromRequest($role, $request->input('permission', []));

        // ✅ Clear Spatie's permission cache so changes take effect immediately
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        return redirect()->route('roles.index')
            ->with('success', 'Rôle « ' . $role->name . ' » mis à jour.');
    }

    // ── DESTROY ───────────────────────────────────────────────
    public function destroy(Role $role): RedirectResponse
    {
        if (in_array($role->name, ['admin', 'gestionnaire', 'formateur', 'stagiaire'])) {
            return redirect()->route('roles.index')
                ->with('error', 'Les rôles système ne peuvent pas être supprimés.');
        }

        $role->delete();

        return redirect()->route('roles.index')
            ->with('success', 'Rôle « ' . $role->name . ' » supprimé.');
    }

    // ── PRIVATE HELPER ────────────────────────────────────────
    /**
     * Ensures every submitted permission name exists in the DB
     * before calling syncPermissions — prevents silent drops.
     */
    private function syncPermissionsFromRequest(Role $role, array $permissionNames): void
    {
        $permissions = collect($permissionNames)
            ->filter()                          // remove empty strings
            ->unique()
            ->map(fn($name) => Permission::firstOrCreate(
                ['name' => $name, 'guard_name' => 'web']
            ));

        $role->syncPermissions($permissions);
    }
}