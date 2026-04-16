<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

/**
 * Seeds the 4 salle-* permissions and assigns them to admin & gestionnaire.
 *
 * Run with:
 *   php artisan db:seed --class=SallePermissionSeeder
 *
 * Or call from DatabaseSeeder:
 *   $this->call(SallePermissionSeeder::class);
 */
class SallePermissionSeeder extends Seeder
{
    public function run(): void
    {
        // ── 1. Create permissions (idempotent) ────────────────────────
        $permissions = [
            'salle-list',
            'salle-create',
            'salle-edit',
            'salle-delete',
        ];

        foreach ($permissions as $perm) {
            Permission::firstOrCreate(
                ['name' => $perm],
                ['guard_name' => 'web']
            );
        }

        // ── 2. Assign to system roles ─────────────────────────────────
        $admin = Role::findByName('admin');
        $admin->givePermissionTo($permissions);

        $gestionnaire = Role::findByName('gestionnaire');
        $gestionnaire->givePermissionTo($permissions);

        // Formateur & stagiaire get salle-list only (read-only for EDT)
        foreach (['formateur', 'stagiaire'] as $roleName) {
            $role = Role::findByName($roleName);
            $role->givePermissionTo('salle-list');
        }

        $this->command->info('✅ Salle permissions seeded and assigned.');
        $this->command->table(
            ['Permission', 'admin', 'gestionnaire', 'formateur', 'stagiaire'],
            [
                ['salle-list',   '✓', '✓', '✓', '✓'],
                ['salle-create', '✓', '✓', '—', '—'],
                ['salle-edit',   '✓', '✓', '—', '—'],
                ['salle-delete', '✓', '✓', '—', '—'],
            ]
        );
    }
}