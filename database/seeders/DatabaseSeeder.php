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
        // 1. USERS — Admin · Gestionnaire · Formateurs
        // ════════════════════════════════════════════════════════════
        $users = [
            [
                'name'                 => 'Admin OFPPT',
                'email'                => 'admin@ofppt.ma',
                'password'             => Hash::make('admin123'),
                'role'                 => 'admin',
            ],
            [
                'name'                 => 'Karim Benjelloun',
                'email'                => 'gestionnaire@ofppt.ma',
                'password'             => Hash::make('gestionnaire123'),
                'role'                 => 'gestionnaire',
            ],
            [
                'name'                 => 'Mohammed Benali',
                'email'                => 'm.benali@ofppt.ma',
                'password'             => Hash::make('formateur123'),
                'role'                 => 'formateur',
                'specialite'           => 'Développement Web & Mobile',
                'matricule_formateur'  => 'F001',
            ],
            [
                'name'                 => 'Fatima Lahlou',
                'email'                => 'f.lahlou@ofppt.ma',
                'password'             => Hash::make('formateur123'),
                'role'                 => 'formateur',
                'specialite'           => 'Réseaux & Systèmes',
                'matricule_formateur'  => 'F002',
            ],
            [
                'name'                 => 'Rachid Amrani',
                'email'                => 'r.amrani@ofppt.ma',
                'password'             => Hash::make('formateur123'),
                'role'                 => 'formateur',
                'specialite'           => 'Base de données & Cloud',
                'matricule_formateur'  => 'F003',
            ],
        ];

        foreach ($users as $data) {
            User::firstOrCreate(['email' => $data['email']], $data);
        }

        $this->command->info('✅ Users créés');

        // ════════════════════════════════════════════════════════════
        // 2. PERMISSIONS & RÔLES (Spatie)
        // ════════════════════════════════════════════════════════════
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $permissions = [
            // Emploi du temps
            'emploi-view', 'emploi-view-all-groups', 'emploi-create',
            'emploi-edit', 'emploi-delete', 'emploi-lien', 'emploi-change-module',
            // Users
            'user-list', 'user-create', 'user-edit', 'user-delete',
            'user-manage-formateur', 'user-manage-gestionnaire',
            // Stagiaires
            'stagiaire-list', 'stagiaire-create', 'stagiaire-edit', 'stagiaire-delete',
            // Groupes
            'groupe-list', 'groupe-create', 'groupe-edit', 'groupe-delete',
            // Salles
            'salle-list', 'salle-create', 'salle-edit', 'salle-delete',
            // EDU
            'edu-view', 'edu-import',
            // Rôles
            'role-list', 'role-create', 'role-edit', 'role-delete',
            // Reportations
            'reportation-create', 'reportation-manage',
            // Réclamations
            'reclamation-create', 'reclamation-list',
            'reclamation-manage', 'reclamation-view-assigned',
            // News
            'news-list', 'news-create', 'news-edit', 'news-delete',
            'news-comment', 'news-like',
            // Absences
            'absence-view', 'absence-view-all', 'absence-justify',
        ];

        foreach ($permissions as $perm) {
            Permission::firstOrCreate(['name' => $perm, 'guard_name' => 'web']);
        }

        // ── Admin — tout ──
        $adminRole = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        $adminRole->syncPermissions(Permission::all());

        // ── Gestionnaire ──
        $gestionnaireRole = Role::firstOrCreate(['name' => 'gestionnaire', 'guard_name' => 'web']);
        $gestionnaireRole->syncPermissions([
            'emploi-view', 'emploi-view-all-groups', 'emploi-create',
            'emploi-edit', 'emploi-delete', 'emploi-change-module',
            'user-list', 'user-create', 'user-edit', 'user-manage-formateur',
            'groupe-list', 'groupe-create', 'groupe-edit',
            'stagiaire-list', 'stagiaire-create', 'stagiaire-edit', 'stagiaire-delete',
            'salle-list', 'salle-create', 'salle-edit', 'salle-delete',
            'edu-view', 'edu-import',
            'reportation-manage', 'reclamation-manage',
            'news-list', 'news-create', 'news-edit', 'news-delete', 'news-comment', 'news-like',
            'absence-view', 'absence-view-all', 'absence-justify',
        ]);

        // ── Formateur ──
        $formateurRole = Role::firstOrCreate(['name' => 'formateur', 'guard_name' => 'web']);
        $formateurRole->syncPermissions([
            'emploi-view', 'emploi-lien',
            'user-list', 'groupe-list', 'salle-list',
            'reportation-create',
            'groupe-list',          // → Mes modules, Mes groupes (scoped in controller)
           'stagiaire-list',     
            'news-list', 'news-comment', 'news-like',
            'absence-view', 'absence-view-all',
            'reclamation-view-assigned',
        ]);

        // ── Stagiaire ──
        $stagiaireRole = Role::firstOrCreate(['name' => 'stagiaire', 'guard_name' => 'web']);
        $stagiaireRole->syncPermissions([
            'emploi-view',
            'reclamation-create', 'reclamation-list',
            'news-list', 'news-comment', 'news-like',
            'absence-view',
        ]);

        // ── Assign roles to existing users ──
        User::all()->each(fn(User $u) => $u->syncRoles([$u->role]));

        $this->command->info('✅ Permissions & rôles créés');

        // ════════════════════════════════════════════════════════════
        // 3. SALLES
        // ════════════════════════════════════════════════════════════
        $salles = [
            ['name' => 'Salle A101',    'capacity' => 30],
            ['name' => 'Salle A102',    'capacity' => 30],
            ['name' => 'Salle B201',    'capacity' => 28],
            ['name' => 'Salle B202',    'capacity' => 28],
            ['name' => 'Labo Info 1',   'capacity' => 25],
            ['name' => 'Labo Info 2',   'capacity' => 25],
            ['name' => 'Labo Réseau',   'capacity' => 20],
            ['name' => 'Amphi Principal','capacity' => 100],
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
            ['filiere' => $filiereDev, 'name' => 'TDEV-101', 'code' => 'TDEV-101', 'annee' => 1, 'nbr_limit' => 25],
            ['filiere' => $filiereDev, 'name' => 'TDEV-102', 'code' => 'TDEV-102', 'annee' => 1, 'nbr_limit' => 25],
            // Développement Digital — Année 2
            ['filiere' => $filiereDev, 'name' => 'TDEV-201', 'code' => 'TDEV-201', 'annee' => 2, 'nbr_limit' => 25],
            ['filiere' => $filiereDev, 'name' => 'TDEV-202', 'code' => 'TDEV-202', 'annee' => 2, 'nbr_limit' => 25],
            // Génie Informatique — Année 1
            ['filiere' => $filiereGI,  'name' => 'TGI-101',  'code' => 'TGI-101',  'annee' => 1, 'nbr_limit' => 25],
            // Génie Informatique — Année 2
            ['filiere' => $filiereGI,  'name' => 'TGI-201',  'code' => 'TGI-201',  'annee' => 2, 'nbr_limit' => 25],
        ];

        $groupeMap = [];
        foreach ($groupes as $g) {
            $groupe = Groupe::firstOrCreate(
                ['code' => $g['code']],
                [
                    'name'       => $g['name'],
                    'id_filiere' => $g['filiere']->id,
                    'annee'      => $g['annee'],
                    'nbr_limit'  => $g['nbr_limit'],
                    'id_option'  => null,
                ]
            );
            $groupeMap[$g['code']] = $groupe->id;
        }

        $this->command->info('✅ Groupes créés');

        // ════════════════════════════════════════════════════════════
        // 6. MODULES
        // ════════════════════════════════════════════════════════════
        $formateur = User::where('role', 'formateur')->first();

        $modulesDev = [
            ['name' => 'Algorithmique & Programmation', 'nbr_heure' => 60, 'coefficience' => 3, 'type' => 'regional'],
            ['name' => 'HTML & CSS',                    'nbr_heure' => 60, 'coefficience' => 2, 'type' => 'regional'],
            ['name' => 'JavaScript & TypeScript',       'nbr_heure' => 80, 'coefficience' => 3, 'type' => 'local'],
            ['name' => 'PHP & Laravel',                 'nbr_heure' => 90, 'coefficience' => 3, 'type' => 'local'],
            ['name' => 'React.js',                      'nbr_heure' => 70, 'coefficience' => 3, 'type' => 'local'],
            ['name' => 'Base de données SQL',           'nbr_heure' => 75, 'coefficience' => 3, 'type' => 'regional'],
            ['name' => 'APIs REST & GraphQL',           'nbr_heure' => 60, 'coefficience' => 3, 'type' => 'local'],
            ['name' => 'Git & DevOps',                  'nbr_heure' => 40, 'coefficience' => 2, 'type' => 'regional'],
            ['name' => 'Python',                        'nbr_heure' => 50, 'coefficience' => 2, 'type' => 'regional'],
            ['name' => 'Node.js & Express',             'nbr_heure' => 60, 'coefficience' => 2, 'type' => 'local'],
            ['name' => 'Communication & Soft Skills',   'nbr_heure' => 30, 'coefficience' => 1, 'type' => 'regional'],
            ['name' => 'Gestion de projet Agile',       'nbr_heure' => 30, 'coefficience' => 1, 'type' => 'regional'],
            ['name' => 'Anglais technique',             'nbr_heure' => 40, 'coefficience' => 2, 'type' => 'regional'],
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
                'created_at'   => now(),
                'updated_at'   => now(),
            ]);
        }

        $modulesGI = [
            ['name' => "Systèmes d'exploitation",   'nbr_heure' => 70, 'coefficience' => 3, 'type' => 'regional'],
            ['name' => 'Réseaux & Administration',  'nbr_heure' => 80, 'coefficience' => 3, 'type' => 'regional'],
            ['name' => 'Sécurité informatique',     'nbr_heure' => 60, 'coefficience' => 3, 'type' => 'regional'],
            ['name' => 'Linux & Shell',             'nbr_heure' => 50, 'coefficience' => 2, 'type' => 'local'],
            ['name' => 'Virtualisation & Cloud',    'nbr_heure' => 50, 'coefficience' => 2, 'type' => 'local'],
            ['name' => 'Base de données SQL',       'nbr_heure' => 60, 'coefficience' => 2, 'type' => 'regional'],
            ['name' => 'Scripting Bash & Python',   'nbr_heure' => 40, 'coefficience' => 2, 'type' => 'local'],
            ['name' => 'Anglais technique',         'nbr_heure' => 40, 'coefficience' => 2, 'type' => 'regional'],
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
                'created_at'   => now(),
                'updated_at'   => now(),
            ]);
        }

        $this->command->info('✅ Modules créés');

        // ════════════════════════════════════════════════════════════
        // 7. EDU — comptes stagiaires pré-enregistrés
        // ════════════════════════════════════════════════════════════
        DB::table('edu')->truncate();

        $eduStudents = [
            // TDEV-101
            ['edu_email' => 'youssef.aitali@ofppt.ma',     'nom' => 'Ait Ali',    'prenom' => 'Youssef',   'filiere_code' => 'DEVDIG', 'groupe_code' => 'TDEV-101'],
            ['edu_email' => 'sara.idrissi@ofppt.ma',       'nom' => 'Idrissi',    'prenom' => 'Sara',      'filiere_code' => 'DEVDIG', 'groupe_code' => 'TDEV-101'],
            ['edu_email' => 'hamza.benali@ofppt.ma',       'nom' => 'Benali',     'prenom' => 'Hamza',     'filiere_code' => 'DEVDIG', 'groupe_code' => 'TDEV-101'],
            ['edu_email' => 'imane.tahiri@ofppt.ma',       'nom' => 'Tahiri',     'prenom' => 'Imane',     'filiere_code' => 'DEVDIG', 'groupe_code' => 'TDEV-101'],
            ['edu_email' => 'omar.belhaj@ofppt.ma',        'nom' => 'Belhaj',     'prenom' => 'Omar',      'filiere_code' => 'DEVDIG', 'groupe_code' => 'TDEV-101'],
            // TDEV-102
            ['edu_email' => 'anas.moufid@ofppt.ma',        'nom' => 'Moufid',     'prenom' => 'Anas',      'filiere_code' => 'DEVDIG', 'groupe_code' => 'TDEV-102'],
            ['edu_email' => 'soukayna.belkadi@ofppt.ma',   'nom' => 'Belkadi',    'prenom' => 'Soukayna',  'filiere_code' => 'DEVDIG', 'groupe_code' => 'TDEV-102'],
            ['edu_email' => 'zakaria.naciri@ofppt.ma',     'nom' => 'Naciri',     'prenom' => 'Zakaria',   'filiere_code' => 'DEVDIG', 'groupe_code' => 'TDEV-102'],
            ['edu_email' => 'nadia.chraibi@ofppt.ma',      'nom' => 'Chraibi',    'prenom' => 'Nadia',     'filiere_code' => 'DEVDIG', 'groupe_code' => 'TDEV-102'],
            // TDEV-201
            ['edu_email' => 'bilal.amrani@ofppt.ma',       'nom' => 'Amrani',     'prenom' => 'Bilal',     'filiere_code' => 'DEVDIG', 'groupe_code' => 'TDEV-201'],
            ['edu_email' => 'meriem.bensaid@ofppt.ma',     'nom' => 'Bensaid',    'prenom' => 'Meriem',    'filiere_code' => 'DEVDIG', 'groupe_code' => 'TDEV-201'],
            ['edu_email' => 'ilyas.mouhib@ofppt.ma',       'nom' => 'Mouhib',     'prenom' => 'Ilyas',     'filiere_code' => 'DEVDIG', 'groupe_code' => 'TDEV-201'],
            // TDEV-202
            ['edu_email' => 'kawthar.ziani@ofppt.ma',      'nom' => 'Ziani',      'prenom' => 'Kawthar',   'filiere_code' => 'DEVDIG', 'groupe_code' => 'TDEV-202'],
            ['edu_email' => 'hicham.rachidi@ofppt.ma',     'nom' => 'Rachidi',    'prenom' => 'Hicham',    'filiere_code' => 'DEVDIG', 'groupe_code' => 'TDEV-202'],
            // TGI-101
            ['edu_email' => 'adam.tazi@ofppt.ma',          'nom' => 'Tazi',       'prenom' => 'Adam',      'filiere_code' => 'GI',     'groupe_code' => 'TGI-101'],
            ['edu_email' => 'hajar.alaoui@ofppt.ma',       'nom' => 'Alaoui',     'prenom' => 'Hajar',     'filiere_code' => 'GI',     'groupe_code' => 'TGI-101'],
            ['edu_email' => 'tariq.bennani@ofppt.ma',      'nom' => 'Bennani',    'prenom' => 'Tariq',     'filiere_code' => 'GI',     'groupe_code' => 'TGI-101'],
            // TGI-201
            ['edu_email' => 'samira.filali@ofppt.ma',      'nom' => 'Filali',     'prenom' => 'Samira',    'filiere_code' => 'GI',     'groupe_code' => 'TGI-201'],
            ['edu_email' => 'amine.berrada@ofppt.ma',      'nom' => 'Berrada',    'prenom' => 'Amine',     'filiere_code' => 'GI',     'groupe_code' => 'TGI-201'],
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
        // 8. RÉSUMÉ FINAL
        // ════════════════════════════════════════════════════════════
        $this->command->newLine();
        $this->command->info('🎉 Base de données seedée avec succès !');
        $this->command->newLine();

        $this->command->table(
            ['Rôle', 'Email', 'Mot de passe'],
            [
                ['Admin',          'admin@ofppt.ma',          'admin123'],
                ['Gestionnaire',   'gestionnaire@ofppt.ma',   'gestionnaire123'],
                ['Formateur',      'm.benali@ofppt.ma',       'formateur123'],
                ['Formateur',      'f.lahlou@ofppt.ma',       'formateur123'],
                ['Formateur',      'r.amrani@ofppt.ma',       'formateur123'],
                ['Stagiaires EDU', '*.@ofppt.ma',             'ofppt2024 → /register'],
            ]
        );

        $this->command->table(
            ['Filière', 'Groupes', 'Stagiaires EDU', 'Modules'],
            [
                ['Développement Digital', 'TDEV-101, TDEV-102, TDEV-201, TDEV-202', '14', count($modulesDev)],
                ['Génie Informatique',    'TGI-101, TGI-201',                        '5',  count($modulesGI)],
                ['Salles',                '8 salles',                                 '—',  '—'],
            ]
        );
    }
}