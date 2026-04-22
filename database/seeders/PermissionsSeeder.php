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

            // Utilisateurs — accès général
            'user-list',
            'user-create',
            'user-edit',
            'user-delete',

            // ── Nouvelles permissions granulaires ─────────────────
            // Chacune contrôle la visibilité ET le CRUD d'un type de compte
            'user-manage-formateur',     // voir, créer, modifier, supprimer des formateurs
            'user-manage-gestionnaire',  // voir, créer, modifier, supprimer des gestionnaires
            // ──────────────────────────────────────────────────────

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
            'absence-justify',
        ];

        foreach ($permissions as $perm) {
            Permission::firstOrCreate(
                ['name' => $perm, 'guard_name' => 'web']
            );
        }

        // ── Admin — tout ──────────────────────────────────────────
        $adminRole = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        $adminRole->syncPermissions(Permission::all());

        // ── Gestionnaire — gère uniquement les formateurs ─────────
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
            'user-manage-formateur',       // ← peut gérer les formateurs
            // pas user-manage-gestionnaire   ← ne peut PAS gérer les gestionnaires

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
            'absence-justify',
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
        ]);

        // ── Assignation des rôles Spatie aux users existants ──────
        User::all()->each(function (User $user) {
            $user->syncRoles([$user->role]);
        });
    }
}