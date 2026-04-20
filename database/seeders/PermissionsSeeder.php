<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PermissionsSeeder extends Seeder
{
    public function run(): void
    {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $permissions = [
            // Emploi du temps
            'emploi-view',
            'emploi-view-all-groups',
            'emploi-create',
            'emploi-edit',
            'emploi-delete',
            'emploi-lien',
            'emploi-change-module',

            // Utilisateurs
            'user-list',
            'user-create',
            'user-edit',
            'user-delete',
            'stagiaire-list',
            'stagiaire-create',
            'stagiaire-edit',
            'stagiaire-delete',

            // Groupes & Filières
            'groupe-list',
            'groupe-create',
            'groupe-edit',
            'groupe-delete',

            // EDU Import
            'edu-view',
            'edu-import',

            // Rôles & Permissions
            'role-list',
            'role-create',
            'role-edit',
            'role-delete',

            // Reportations
            'reportation-create',
            'reportation-manage',

            // ── Réclamations ──────────────────────────────────
            'reclamation-create',   // Stagiaire : soumettre une réclamation
            'reclamation-list',     // Stagiaire : voir ses propres réclamations
            'reclamation-manage',   // Admin/Gestionnaire : voir toutes + changer statut
        ];

        foreach ($permissions as $perm) {
            Permission::firstOrCreate(
                ['name' => $perm, 'guard_name' => 'web']
            );
        }

        // ── Admin — tout ──────────────────────────────────────
        $adminRole = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        $adminRole->syncPermissions(Permission::all());

        // ── Gestionnaire — gestion complète sauf rôles ────────
        $gestionnaireRole = Role::firstOrCreate(['name' => 'gestionnaire', 'guard_name' => 'web']);
        $gestionnaireRole->syncPermissions([
            'emploi-view',
            'emploi-view-all-groups',
            'emploi-create',
            'emploi-edit',
            'emploi-delete',
            'emploi-change-module',
            'user-list',
            'user-create',
            'user-edit',
            'groupe-list',
            'groupe-create',
            'groupe-edit',
            'stagiaire-list',
            'stagiaire-create',
            'stagiaire-edit',
            'stagiaire-delete',
            'edu-view',
            'edu-import',
            'reportation-manage',
            'reclamation-manage',   // ← voir toutes les réclamations + changer statut
        ]);

        // ── Formateur — ses séances + lien + module + report ──
        $formateurRole = Role::firstOrCreate(['name' => 'formateur', 'guard_name' => 'web']);
        $formateurRole->syncPermissions([
            'emploi-view',
            'emploi-lien',
            'user-list',
            'groupe-list',
            'reportation-create',
            // Pas de permission réclamation pour le formateur
        ]);

        // ── Stagiaire — son emploi du temps + réclamations ────
        $stagiaireRole = Role::firstOrCreate(['name' => 'stagiaire', 'guard_name' => 'web']);
        $stagiaireRole->syncPermissions([
            'emploi-view',
            'reclamation-create',   // ← soumettre une réclamation
            'reclamation-list',     // ← voir ses propres réclamations
        ]);

        // ── Assignation des rôles Spatie aux users existants ──
        User::all()->each(function (User $user) {
            $user->syncRoles([$user->role]);
        });
    }
}