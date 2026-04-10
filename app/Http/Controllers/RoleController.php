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
// ── CREATE ────────────────────────────────────────────────
public function create()
{
    $permissions = Permission::orderBy('name')->get()->groupBy(function ($p) {
        return explode('-', $p->name)[0];
    });

    // ← AJOUTER cette ligne :
    $roles = Role::orderBy('name')->get();

    return view('roles.create', compact('permissions', 'roles'));
}

// ── STORE ─────────────────────────────────────────────────
public function store(Request $request): RedirectResponse
{
    $request->validate([
        'name'       => 'required|string|unique:roles,name|max:100',
        'permission' => 'nullable|array',
    ]);

    $role = Role::create(['name' => $request->name]);
    $role->syncPermissions($request->input('permission', []));

    return redirect()->route('roles.index')
        ->with('success', 'Rôle « ' . $role->name . ' » créé avec succès.');
}

    // ── SHOW ──────────────────────────────────────────────────
    public function show(Role $role)
    {
        $rolePermissions = $role->permissions()->orderBy('name')->get();
        return view('roles.show', compact('role', 'rolePermissions'));
    }

public function edit(Role $role)
{
    $permissions = Permission::orderBy('name')->get()->groupBy(function ($p) {
        return explode('-', $p->name)[0];
    });
    $rolePermissions = $role->permissions->pluck('name')->toArray();

    // ← AJOUTER cette ligne :
    $roles = Role::orderBy('name')->get();

    return view('roles.edit', compact('role', 'permissions', 'rolePermissions', 'roles'));
}
    // ── UPDATE ────────────────────────────────────────────────
    public function update(Request $request, Role $role): RedirectResponse
    {
        $request->validate([
            'name'       => 'required|string|unique:roles,name,' . $role->id . '|max:100',
            'permission' => 'nullable|array',
        ]);

        $role->update(['name' => $request->name]);
        $role->syncPermissions($request->input('permission', []));

        return redirect()->route('roles.index')
            ->with('success', 'Rôle « ' . $role->name . ' » mis à jour.');
    }

    // ── DESTROY ───────────────────────────────────────────────
    public function destroy(Role $role): RedirectResponse
    {
        // Prevent deleting system roles
        if (in_array($role->name, ['admin', 'gestionnaire', 'formateur', 'stagiaire'])) {
            return redirect()->route('roles.index')
                ->with('error', 'Les rôles système ne peuvent pas être supprimés.');
        }

        $role->delete();
        return redirect()->route('roles.index')
            ->with('success', 'Rôle supprimé.');
    }
}