<?php

namespace Database\Seeders;

use App\Models\Filiere;
use App\Models\Groupe;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // ════════════════════════════════════════════════════════════
        // 1. USERS
        // ════════════════════════════════════════════════════════════
        $users = [
            [
                'name'     => 'Admin OFPPT',
                'email'    => 'admin@ofppt.ma',
                'password' => Hash::make('admin123'),
                'role'     => 'admin',
            ],
            [
                'name'     => 'Karim Benjelloun',
                'email'    => 'gestionnaire@ofppt.ma',
                'password' => Hash::make('gestionnaire123'),
                'role'     => 'gestionnaire',
            ],
            [
                'name'                => 'Mohammed Benali',
                'email'               => 'm.benali@ofppt.ma',
                'password'            => Hash::make('formateur123'),
                'role'                => 'formateur',
                'specialite'          => 'Développement Web & Mobile',
                'matricule_formateur' => 'F001',
            ],
            [
                'name'                => 'Fatima Lahlou',
                'email'               => 'f.lahlou@ofppt.ma',
                'password'            => Hash::make('formateur123'),
                'role'                => 'formateur',
                'specialite'          => 'Réseaux & Systèmes',
                'matricule_formateur' => 'F002',
            ],
            [
                'name'                => 'Rachid Amrani',
                'email'               => 'r.amrani@ofppt.ma',
                'password'            => Hash::make('formateur123'),
                'role'                => 'formateur',
                'specialite'          => 'Base de données & Cloud',
                'matricule_formateur' => 'F003',
            ],
        ];

        foreach ($users as $data) {
            User::firstOrCreate(['email' => $data['email']], $data);
        }

        $this->command->info('✅ Users créés');

        // ════════════════════════════════════════════════════════════
        // 2. PERMISSIONS & RÔLES
        // ════════════════════════════════════════════════════════════
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $permissions = [
            'emploi-view', 'emploi-view-all-groups', 'emploi-create',
            'emploi-edit', 'emploi-delete', 'emploi-lien', 'emploi-change-module',
            'user-list', 'user-create', 'user-edit', 'user-delete',
            'user-manage-formateur', 'user-manage-gestionnaire',
            'stagiaire-list', 'stagiaire-create', 'stagiaire-edit', 'stagiaire-delete',
            'groupe-list', 'groupe-create', 'groupe-edit', 'groupe-delete',
            'salle-list', 'salle-create', 'salle-edit', 'salle-delete',
            'edu-view', 'edu-import',
            'role-list', 'role-create', 'role-edit', 'role-delete',
            'reportation-create', 'reportation-manage', 'reportation-view-assigned',
            'reclamation-create', 'reclamation-list',
            'reclamation-manage', 'reclamation-view-assigned',
            'news-list', 'news-create', 'news-edit', 'news-delete',
            'news-comment', 'news-like',
            'absence-view', 'absence-view-all', 'absence-justify',
            'controle-view',
            'controle-save',
            'mes-notes-view',
            'bulletin-view',
            'search-stagiaires', // ← NEW
        ];

        foreach ($permissions as $perm) {
            Permission::firstOrCreate(['name' => $perm, 'guard_name' => 'web']);
        }

        // ── ADMIN — gets ALL permissions automatically ────────────
        $adminRole = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        $adminRole->syncPermissions(Permission::all());

        // ── GESTIONNAIRE ──────────────────────────────────────────
        $gestionnaireRole = Role::firstOrCreate(['name' => 'gestionnaire', 'guard_name' => 'web']);
        $gestionnaireRole->syncPermissions([
            'emploi-view', 'emploi-view-all-groups', 'emploi-create',
            'emploi-edit', 'emploi-delete', 'emploi-change-module',
            'user-list', 'user-create', 'user-edit', 'user-manage-formateur',
            'groupe-list', 'groupe-create', 'groupe-edit',
            'stagiaire-list', 'stagiaire-create', 'stagiaire-edit', 'stagiaire-delete',
            'salle-list', 'salle-create', 'salle-edit', 'salle-delete',
            'edu-view', 'edu-import',
            'reportation-view-assigned',
            'reclamation-manage',
            'news-list', 'news-create', 'news-edit', 'news-delete', 'news-comment', 'news-like',
            'absence-view', 'absence-view-all', 'absence-justify',
            'controle-view',
            'controle-save',
            'bulletin-view',
            'search-stagiaires', // ← NEW
        ]);

        // ── FORMATEUR ─────────────────────────────────────────────
        $formateurRole = Role::firstOrCreate(['name' => 'formateur', 'guard_name' => 'web']);
        $formateurRole->syncPermissions([
            'emploi-view', 'emploi-lien',
            'user-list', 'groupe-list', 'salle-list',
            'reportation-create',
            'stagiaire-list',
            'news-list', 'news-comment', 'news-like',
            'absence-view', 'absence-view-all',
            'reclamation-view-assigned',
            'controle-view',
            'controle-save',
            'search-stagiaires', // ← NEW
        ]);

        // ── STAGIAIRE ─────────────────────────────────────────────
        // Stagiaires cannot search other stagiaires — permission NOT added here
        $stagiaireRole = Role::firstOrCreate(['name' => 'stagiaire', 'guard_name' => 'web']);
        $stagiaireRole->syncPermissions([
            'emploi-view',
            'reclamation-create', 'reclamation-list',
            'news-list', 'news-comment', 'news-like',
            'absence-view',
            'mes-notes-view',
        ]);

        User::all()->each(fn(User $u) => $u->syncRoles([$u->role]));

        $this->command->info('✅ Permissions & rôles créés');

        // ════════════════════════════════════════════════════════════
        // 3. SALLES
        // ════════════════════════════════════════════════════════════
        $salles = [
            ['name' => 'Salle A101',      'capacity' => 30],
            ['name' => 'Salle A102',      'capacity' => 30],
            ['name' => 'Salle B201',      'capacity' => 28],
            ['name' => 'Salle B202',      'capacity' => 28],
            ['name' => 'Labo Info 1',     'capacity' => 25],
            ['name' => 'Labo Info 2',     'capacity' => 25],
            ['name' => 'Labo Réseau',     'capacity' => 20],
            ['name' => 'Amphi Principal', 'capacity' => 100],
        ];

        foreach ($salles as $salle) {
            DB::table('salles')->insertOrIgnore(array_merge($salle, [
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }

        $this->command->info('✅ Salles créées');

        // ════════════════════════════════════════════════════════════
        // 4. FILIERES
        // ════════════════════════════════════════════════════════════
        $filiereDev = Filiere::firstOrCreate(
            ['code' => 'DEVDIG'],
            ['name' => 'Développement Digital', 'duree' => 2]
        );

        $filiereGI = Filiere::firstOrCreate(
            ['code' => 'GI'],
            ['name' => 'Génie Informatique', 'duree' => 2]
        );

        $this->command->info('✅ Filières créées');

        // ════════════════════════════════════════════════════════════
        // 5. GROUPES
        // ════════════════════════════════════════════════════════════
        $groupes = [
            // Développement Digital — Année 1
            ['filiere' => $filiereDev, 'name' => 'TDEV-101', 'code' => 'TDEV-101-26', 'annee' => 1, 'nbr_limit' => 25, 'promo' => 2026],
            ['filiere' => $filiereDev, 'name' => 'TDEV-102', 'code' => 'TDEV-102-26', 'annee' => 1, 'nbr_limit' => 25, 'promo' => 2026],
            // Développement Digital — Année 2
            ['filiere' => $filiereDev, 'name' => 'TDEV-201', 'code' => 'TDEV-201-26', 'annee' => 2, 'nbr_limit' => 25, 'promo' => 2026],
            ['filiere' => $filiereDev, 'name' => 'TDEV-202', 'code' => 'TDEV-202-26', 'annee' => 2, 'nbr_limit' => 25, 'promo' => 2026],
            // Génie Informatique — Année 1
            ['filiere' => $filiereGI,  'name' => 'TGI-101',  'code' => 'TGI-101-26',  'annee' => 1, 'nbr_limit' => 25, 'promo' => 2026],
            // Génie Informatique — Année 2
            ['filiere' => $filiereGI,  'name' => 'TGI-201',  'code' => 'TGI-201-26',  'annee' => 2, 'nbr_limit' => 25, 'promo' => 2026],
        ];

        foreach ($groupes as $g) {
            Groupe::firstOrCreate(
                ['code' => $g['code']],
                [
                    'name'       => $g['name'],
                    'id_filiere' => $g['filiere']->id,
                    'annee'      => $g['annee'],
                    'nbr_limit'  => $g['nbr_limit'],
                    'promo'      => $g['promo'],
                    'id_option'  => null,
                ]
            );
        }

        $this->command->info('✅ Groupes créés');

        // ════════════════════════════════════════════════════════════
        // 6. MODULES
        // ════════════════════════════════════════════════════════════
        $formateur = User::where('role', 'formateur')->first();

        $modulesDev = [
            // Année 1
            ['name' => 'Algorithmique & Programmation', 'nbr_heure' => 60, 'coefficience' => 3, 'type' => 'regional', 'annee' => 1],
            ['name' => 'HTML & CSS',                    'nbr_heure' => 60, 'coefficience' => 2, 'type' => 'regional', 'annee' => 1],
            ['name' => 'JavaScript & TypeScript',       'nbr_heure' => 80, 'coefficience' => 3, 'type' => 'local',    'annee' => 1],
            ['name' => 'Base de données SQL',           'nbr_heure' => 75, 'coefficience' => 3, 'type' => 'regional', 'annee' => 1],
            ['name' => 'Git & DevOps',                  'nbr_heure' => 40, 'coefficience' => 2, 'type' => 'regional', 'annee' => 1],
            ['name' => 'Communication & Soft Skills',   'nbr_heure' => 30, 'coefficience' => 1, 'type' => 'regional', 'annee' => 1],
            ['name' => 'Anglais technique',             'nbr_heure' => 40, 'coefficience' => 2, 'type' => 'regional', 'annee' => 1],
            // Année 2
            ['name' => 'PHP & Laravel',                 'nbr_heure' => 90, 'coefficience' => 3, 'type' => 'local',    'annee' => 2],
            ['name' => 'React.js',                      'nbr_heure' => 70, 'coefficience' => 3, 'type' => 'local',    'annee' => 2],
            ['name' => 'APIs REST & GraphQL',           'nbr_heure' => 60, 'coefficience' => 3, 'type' => 'local',    'annee' => 2],
            ['name' => 'Python',                        'nbr_heure' => 50, 'coefficience' => 2, 'type' => 'regional', 'annee' => 2],
            ['name' => 'Node.js & Express',             'nbr_heure' => 60, 'coefficience' => 2, 'type' => 'local',    'annee' => 2],
            ['name' => 'Gestion de projet Agile',       'nbr_heure' => 30, 'coefficience' => 1, 'type' => 'regional', 'annee' => 2],
        ];

        foreach ($modulesDev as $m) {
            DB::table('modules')->insertOrIgnore([
                'id_filiere'   => $filiereDev->id,
                'id_option'    => null,
                'name'         => $m['name'],
                'coefficience' => $m['coefficience'],
                'nbr_heure'    => $m['nbr_heure'],
                'id_user'      => $formateur?->id,
                'type'         => $m['type'],
                'annee'        => $m['annee'],
                'created_at'   => now(),
                'updated_at'   => now(),
            ]);
        }

        $modulesGI = [
            // Année 1
            ['name' => "Systèmes d'exploitation",  'nbr_heure' => 70, 'coefficience' => 3, 'type' => 'regional', 'annee' => 1],
            ['name' => 'Réseaux & Administration',  'nbr_heure' => 80, 'coefficience' => 3, 'type' => 'regional', 'annee' => 1],
            ['name' => 'Linux & Shell',             'nbr_heure' => 50, 'coefficience' => 2, 'type' => 'local',    'annee' => 1],
            ['name' => 'Base de données SQL',       'nbr_heure' => 60, 'coefficience' => 2, 'type' => 'regional', 'annee' => 1],
            ['name' => 'Anglais technique',         'nbr_heure' => 40, 'coefficience' => 2, 'type' => 'regional', 'annee' => 1],
            // Année 2
            ['name' => 'Sécurité informatique',     'nbr_heure' => 60, 'coefficience' => 3, 'type' => 'regional', 'annee' => 2],
            ['name' => 'Virtualisation & Cloud',    'nbr_heure' => 50, 'coefficience' => 2, 'type' => 'local',    'annee' => 2],
            ['name' => 'Scripting Bash & Python',   'nbr_heure' => 40, 'coefficience' => 2, 'type' => 'local',    'annee' => 2],
        ];

        foreach ($modulesGI as $m) {
            DB::table('modules')->insertOrIgnore([
                'id_filiere'   => $filiereGI->id,
                'id_option'    => null,
                'name'         => $m['name'],
                'coefficience' => $m['coefficience'],
                'nbr_heure'    => $m['nbr_heure'],
                'id_user'      => $formateur?->id,
                'type'         => $m['type'],
                'annee'        => $m['annee'],
                'created_at'   => now(),
                'updated_at'   => now(),
            ]);
        }

        $this->command->info('✅ Modules créés');

        // ════════════════════════════════════════════════════════════
        // 7. EDU
        // ════════════════════════════════════════════════════════════
        DB::table('edu')->truncate();

        $eduStudents = [
            // TDEV-101-26
            ['edu_email' => 'youssef.aitali@ofppt.ma',   'nom' => 'Ait Ali',  'prenom' => 'Youssef',  'filiere_code' => 'DEVDIG', 'groupe_code' => 'TDEV-101-26'],
            ['edu_email' => 'sara.idrissi@ofppt.ma',     'nom' => 'Idrissi',  'prenom' => 'Sara',     'filiere_code' => 'DEVDIG', 'groupe_code' => 'TDEV-101-26'],
            ['edu_email' => 'hamza.benali@ofppt.ma',     'nom' => 'Benali',   'prenom' => 'Hamza',    'filiere_code' => 'DEVDIG', 'groupe_code' => 'TDEV-101-26'],
            ['edu_email' => 'imane.tahiri@ofppt.ma',     'nom' => 'Tahiri',   'prenom' => 'Imane',    'filiere_code' => 'DEVDIG', 'groupe_code' => 'TDEV-101-26'],
            ['edu_email' => 'omar.belhaj@ofppt.ma',      'nom' => 'Belhaj',   'prenom' => 'Omar',     'filiere_code' => 'DEVDIG', 'groupe_code' => 'TDEV-101-26'],
            // TDEV-102-26
            ['edu_email' => 'anas.moufid@ofppt.ma',      'nom' => 'Moufid',   'prenom' => 'Anas',     'filiere_code' => 'DEVDIG', 'groupe_code' => 'TDEV-102-26'],
            ['edu_email' => 'soukayna.belkadi@ofppt.ma', 'nom' => 'Belkadi',  'prenom' => 'Soukayna', 'filiere_code' => 'DEVDIG', 'groupe_code' => 'TDEV-102-26'],
            ['edu_email' => 'zakaria.naciri@ofppt.ma',   'nom' => 'Naciri',   'prenom' => 'Zakaria',  'filiere_code' => 'DEVDIG', 'groupe_code' => 'TDEV-102-26'],
            ['edu_email' => 'nadia.chraibi@ofppt.ma',    'nom' => 'Chraibi',  'prenom' => 'Nadia',    'filiere_code' => 'DEVDIG', 'groupe_code' => 'TDEV-102-26'],
            // TDEV-201-26
            ['edu_email' => 'bilal.amrani@ofppt.ma',     'nom' => 'Amrani',   'prenom' => 'Bilal',    'filiere_code' => 'DEVDIG', 'groupe_code' => 'TDEV-201-26'],
            ['edu_email' => 'meriem.bensaid@ofppt.ma',   'nom' => 'Bensaid',  'prenom' => 'Meriem',   'filiere_code' => 'DEVDIG', 'groupe_code' => 'TDEV-201-26'],
            ['edu_email' => 'ilyas.mouhib@ofppt.ma',     'nom' => 'Mouhib',   'prenom' => 'Ilyas',    'filiere_code' => 'DEVDIG', 'groupe_code' => 'TDEV-201-26'],
            // TDEV-202-26
            ['edu_email' => 'kawthar.ziani@ofppt.ma',    'nom' => 'Ziani',    'prenom' => 'Kawthar',  'filiere_code' => 'DEVDIG', 'groupe_code' => 'TDEV-202-26'],
            ['edu_email' => 'hicham.rachidi@ofppt.ma',   'nom' => 'Rachidi',  'prenom' => 'Hicham',   'filiere_code' => 'DEVDIG', 'groupe_code' => 'TDEV-202-26'],
            // TGI-101-26
            ['edu_email' => 'adam.tazi@ofppt.ma',        'nom' => 'Tazi',     'prenom' => 'Adam',     'filiere_code' => 'GI',     'groupe_code' => 'TGI-101-26'],
            ['edu_email' => 'hajar.alaoui@ofppt.ma',     'nom' => 'Alaoui',   'prenom' => 'Hajar',    'filiere_code' => 'GI',     'groupe_code' => 'TGI-101-26'],
            ['edu_email' => 'tariq.bennani@ofppt.ma',    'nom' => 'Bennani',  'prenom' => 'Tariq',    'filiere_code' => 'GI',     'groupe_code' => 'TGI-101-26'],
            // TGI-201-26
            ['edu_email' => 'samira.filali@ofppt.ma',    'nom' => 'Filali',   'prenom' => 'Samira',   'filiere_code' => 'GI',     'groupe_code' => 'TGI-201-26'],
            ['edu_email' => 'amine.berrada@ofppt.ma',    'nom' => 'Berrada',  'prenom' => 'Amine',    'filiere_code' => 'GI',     'groupe_code' => 'TGI-201-26'],
        ];

        foreach ($eduStudents as $s) {
            DB::table('edu')->insert([
                'edu_email'    => $s['edu_email'],
                'password'     => Hash::make('ofppt2024'),
                'nom'          => $s['nom'],
                'prenom'       => $s['prenom'],
                'filiere_code' => $s['filiere_code'],
                'groupe_code'  => $s['groupe_code'],
                'used'         => false,
                'created_at'   => now(),
                'updated_at'   => now(),
            ]);
        }

        $this->command->info('✅ EDU comptes créés');

        // ════════════════════════════════════════════════════════════
        // 7b. STAGIAIRES TDEV-101-26 — comptes de test (used = true)
        // ════════════════════════════════════════════════════════════
        $groupeTdev101 = Groupe::where('code', 'TDEV-101-26')->first();
        $filiereDev    = Filiere::where('code', 'DEVDIG')->first();

        $testStagiaires = [
            ['nom' => 'Ait Ali',  'prenom' => 'Youssef',  'edu_email' => 'youssef.aitali@ofppt.ma'],
            ['nom' => 'Idrissi',  'prenom' => 'Sara',      'edu_email' => 'sara.idrissi@ofppt.ma'],
            ['nom' => 'Benali',   'prenom' => 'Hamza',     'edu_email' => 'hamza.benali@ofppt.ma'],
            ['nom' => 'Tahiri',   'prenom' => 'Imane',     'edu_email' => 'imane.tahiri@ofppt.ma'],
            ['nom' => 'Belhaj',   'prenom' => 'Omar',      'edu_email' => 'omar.belhaj@ofppt.ma'],
        ];

        foreach ($testStagiaires as $s) {
            // 1. Create the User account
            $user = User::firstOrCreate(
                ['email' => $s['edu_email']],
                [
                    'name'       => $s['prenom'] . ' ' . $s['nom'],
                    'password'   => Hash::make('ofppt2024'),
                    'role'       => 'stagiaire',
                    'id_filiere' => $filiereDev?->id,
                    'id_groupe'  => $groupeTdev101?->id,
                ]
            );

            // 2. Assign stagiaire role (Spatie)
            $user->syncRoles(['stagiaire']);

            // 3. Mark EDU row as used
            DB::table('edu')
                ->where('edu_email', $s['edu_email'])
                ->update(['used' => true]);
        }

        $this->command->info('✅ Stagiaires TDEV-101-26 créés (comptes de test)');

        // ════════════════════════════════════════════════════════════
        // 8. RÉSUMÉ
        // ════════════════════════════════════════════════════════════
        $this->command->newLine();
        $this->command->info('🎉 Base de données seedée avec succès !');
        $this->command->newLine();

        $this->command->table(
            ['Rôle', 'Email', 'Mot de passe'],
            [
                ['Admin',        'admin@ofppt.ma',        'admin123'],
                ['Gestionnaire', 'gestionnaire@ofppt.ma', 'gestionnaire123'],
                ['Formateur',    'm.benali@ofppt.ma',     'formateur123'],
                ['Formateur',    'f.lahlou@ofppt.ma',     'formateur123'],
                ['Formateur',    'r.amrani@ofppt.ma',     'formateur123'],
                ['Stagiaires',   '*.@ofppt.ma',           'ofppt2024 → /register'],
            ]
        );

        $this->command->table(
            ['Filière', 'Groupes', 'Stagiaires EDU', 'Modules An.1', 'Modules An.2', 'Promo'],
            [
                ['Développement Digital', 'TDEV-101-26, TDEV-102-26, TDEV-201-26, TDEV-202-26', '14', '7', '6', '2025–2026'],
                ['Génie Informatique',    'TGI-101-26, TGI-201-26',                              '5',  '5', '3', '2025–2026'],
            ]
        );
    }
}