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

            // Salles
            'salle-list',
            'salle-create',
            'salle-edit',
            'salle-delete',

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

            // Réclamations
            'reclamation-create',
            'reclamation-list',
            'reclamation-manage',

            // News & Événements
            'news-list',
            'news-create',
            'news-edit',
            'news-delete',
            'news-comment',
            'news-like',

            // Absences & Retards
            'absence-view',
            'absence-view-all',
            'absence-justify',   // ← NEW
        ];

        foreach ($permissions as $perm) {
            Permission::firstOrCreate(
                ['name' => $perm, 'guard_name' => 'web']
            );
        }

        // ── Admin — tout ──────────────────────────────────────────
        $adminRole = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        $adminRole->syncPermissions(Permission::all());

        // ── Gestionnaire ──────────────────────────────────────────
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
            'salle-list',
            'salle-create',
            'salle-edit',
            'salle-delete',
            'edu-view',
            'edu-import',
            'reportation-manage',
            'reclamation-manage',
            'news-list',
            'news-create',
            'news-edit',
            'news-delete',
            'news-comment',
            'news-like',
            'absence-view',
            'absence-view-all',
            'absence-justify',   // ← NEW
        ]);

        // ── Formateur ─────────────────────────────────────────────
        $formateurRole = Role::firstOrCreate(['name' => 'formateur', 'guard_name' => 'web']);
        $formateurRole->syncPermissions([
            'emploi-view',
            'emploi-lien',
            'user-list',
            'groupe-list',
            'salle-list',
            'reportation-create',
            'news-list',
            'news-comment',
            'news-like',
            'absence-view',
            'absence-view-all',
            // no absence-justify
        ]);

        // ── Stagiaire ─────────────────────────────────────────────
        $stagiaireRole = Role::firstOrCreate(['name' => 'stagiaire', 'guard_name' => 'web']);
        $stagiaireRole->syncPermissions([
            'emploi-view',
            'reclamation-create',
            'reclamation-list',
            'news-list',
            'news-comment',
            'news-like',
            'absence-view',
            // no absence-justify
        ]);

        // ── Assignation des rôles Spatie aux users existants ──────
        User::all()->each(function (User $user) {
            $user->syncRoles([$user->role]);
        });
    }
}