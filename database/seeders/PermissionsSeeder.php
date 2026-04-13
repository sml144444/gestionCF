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
            'emploi-view',            // 🔓 Accès de base (voir son propre emploi du temps)
            'emploi-view-all-groups', // 👑 Voir tous les groupes (admin/gestionnaire)
            'emploi-create',          // ➕ Ajouter une séance
            'emploi-edit',            // ✎  Modifier une séance
            'emploi-delete',          // ✕  Supprimer une séance
            'emploi-lien',            // 🔗 Modifier le lien de réunion (distance)
            'emploi-change-module',   // 📚 Modifier le module d'une séance (formateur optionnel)

            // Utilisateurs
            'user-list',
            'user-create',
            'user-edit',
            'user-delete',
            'stagiaire-list',         // 👥 Voir la liste des stagiaires par filière/groupe

            // Groupes & Filières
            'groupe-list',
            'groupe-create',
            'groupe-edit',
            'groupe-delete',

            // EDU Import
            'edu-view',               // 📄 Voir la page import + télécharger le modèle
            'edu-import',             // 📥 Uploader / prévisualiser / confirmer / ajout manuel

            // Rôles & Permissions
            'role-list',
            'role-create',
            'role-edit',
            'role-delete',
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
            'emploi-change-module',   // ← gestionnaire peut changer le module
            'user-list',
            'user-create',
            'user-edit',
            'groupe-list',
            'groupe-create',
            'groupe-edit',
            'stagiaire-list',
            'edu-view',
            'edu-import',
        ]);

        // ── Formateur — ses séances + lien + module (optionnel) ──
        $formateurRole = Role::firstOrCreate(['name' => 'formateur', 'guard_name' => 'web']);
        $formateurRole->syncPermissions([
            'emploi-view',            // ← voit seulement ses propres séances
            'emploi-lien',            // ← peut modifier ses liens de réunion
            // 'emploi-change-module' ← NON par défaut, à donner manuellement si besoin
            'user-list',
            'groupe-list',
        ]);

        // ── Stagiaire — son groupe seulement ──────────────────
        $stagiaireRole = Role::firstOrCreate(['name' => 'stagiaire', 'guard_name' => 'web']);
        $stagiaireRole->syncPermissions([
            'emploi-view',            // ← voit seulement son groupe
        ]);

        // ── Assignation des rôles Spatie aux users existants ──
        User::all()->each(function (User $user) {
            $user->syncRoles([$user->role]);
        });
    }
}