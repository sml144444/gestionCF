<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use App\Models\User;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
   UsersSeeder::class,         // users أولاً
   PermissionsSeeder::class,   // roles after users
   EduSeeder::class,           
   ModulesSeeder::class,       // kay7taj formateur + filieres
   StagiaireDevdigSeeder::class
]);
        // ── 1. FILIERES (with code) ───────────────────────────
        // DB::table('filieres')->insert([
        //     ['name' => 'Développement Digital',       'code' => 'DEVDIG', 'duree' => 2, 'created_at' => now(), 'updated_at' => now()],
        //     ['name' => 'Génie Informatique',           'code' => 'GI',     'duree' => 2, 'created_at' => now(), 'updated_at' => now()],
        //     ['name' => 'Technicien Spécialisé Réseau', 'code' => 'TSRES',  'duree' => 2, 'created_at' => now(), 'updated_at' => now()],
        // ]);

        // $ddId   = DB::table('filieres')->where('code', 'DEVDIG')->value('id');
        // $giId   = DB::table('filieres')->where('code', 'GI')->value('id');
        // $tsId   = DB::table('filieres')->where('code', 'TSRES')->value('id');

        // // ── 2. SALLES ─────────────────────────────────────────
        // DB::table('salles')->insert([
        //     ['name' => 'Salle 101',   'capacity' => 30, 'created_at' => now(), 'updated_at' => now()],
        //     ['name' => 'Salle 102',   'capacity' => 30, 'created_at' => now(), 'updated_at' => now()],
        //     ['name' => 'Salle 201',   'capacity' => 25, 'created_at' => now(), 'updated_at' => now()],
        //     ['name' => 'Salle 202',   'capacity' => 25, 'created_at' => now(), 'updated_at' => now()],
        //     ['name' => 'Salle 301',   'capacity' => 20, 'created_at' => now(), 'updated_at' => now()],
        //     ['name' => 'Labo Info',   'capacity' => 20, 'created_at' => now(), 'updated_at' => now()],
        //     ['name' => 'Labo Réseau', 'capacity' => 15, 'created_at' => now(), 'updated_at' => now()],
        //     ['name' => 'Amphi A',     'capacity' => 80, 'created_at' => now(), 'updated_at' => now()],
        // ]);

        // // ── 3. GROUPES (with code) ────────────────────────────
        // DB::table('groupes')->insert([
        //     // Année 1
        //     ['id_filiere' => $ddId, 'id_option' => null, 'nbr_limit' => 25, 'annee' => 1, 'name' => 'G1A', 'code' => 'DD-G1A', 'created_at' => now(), 'updated_at' => now()],
        //     ['id_filiere' => $ddId, 'id_option' => null, 'nbr_limit' => 25, 'annee' => 1, 'name' => 'G1B', 'code' => 'DD-G1B', 'created_at' => now(), 'updated_at' => now()],
        //     ['id_filiere' => $giId, 'id_option' => null, 'nbr_limit' => 25, 'annee' => 1, 'name' => 'G1C', 'code' => 'GI-G1C', 'created_at' => now(), 'updated_at' => now()],
        //     // Année 2
        //     ['id_filiere' => $ddId, 'id_option' => null, 'nbr_limit' => 25, 'annee' => 2, 'name' => 'G2A', 'code' => 'DD-G2A', 'created_at' => now(), 'updated_at' => now()],
        //     ['id_filiere' => $giId, 'id_option' => null, 'nbr_limit' => 25, 'annee' => 2, 'name' => 'G2B', 'code' => 'GI-G2B', 'created_at' => now(), 'updated_at' => now()],
        //     ['id_filiere' => $tsId, 'id_option' => null, 'nbr_limit' => 20, 'annee' => 2, 'name' => 'G2X', 'code' => 'TS-G2X', 'created_at' => now(), 'updated_at' => now()],
        // ]);

        // // ── 4. USERS (admin, gestionnaire, formateurs) ────────
        // User::create(['name' => 'Admin OFPPT',        'email' => 'admin@ofppt.ma',        'password' => Hash::make('admin123'),        'role' => 'admin']);
        // User::create(['name' => 'Gestionnaire OFPPT', 'email' => 'gestionnaire@ofppt.ma', 'password' => Hash::make('gestionnaire123'), 'role' => 'gestionnaire']);
        // User::create(['name' => 'M. Benali',          'email' => 'benali@ofppt.ma',       'password' => Hash::make('formateur123'),    'role' => 'formateur', 'specialite' => 'Développement Web',   'matricule_formateur' => 'F001']);
        // User::create(['name' => 'Mme. Lahlou',        'email' => 'lahlou@ofppt.ma',       'password' => Hash::make('formateur123'),    'role' => 'formateur', 'specialite' => 'Réseaux & Systèmes', 'matricule_formateur' => 'F002']);
        // User::create(['name' => 'M. Amrani',          'email' => 'amrani@ofppt.ma',       'password' => Hash::make('formateur123'),    'role' => 'formateur', 'specialite' => 'Base de données',    'matricule_formateur' => 'F003']);

        // // ── 5. EDU entries (new format: nom, prenom, filiere_code, groupe_code) ──
        // // These are the pre-registered students imported by the gestionnaire
        // // Password in EDU is the one they will use to register
        // DB::table('edu')->insert([
        //     [
        //         'edu_email'    => 'ahmed.ali@ofppt.ma',
        //         'password'     => Hash::make('pass1234'),
        //         'nom'          => 'Ali',
        //         'prenom'       => 'Ahmed',
        //         'filiere_code' => 'DEVDIG',
        //         'groupe_code'  => 'DD-G1A',
        //         'used'         => false,
        //         'created_at'   => now(),
        //         'updated_at'   => now(),
        //     ],
        //     [
        //         'edu_email'    => 'fatima.zahra@ofppt.ma',
        //         'password'     => Hash::make('pass1234'),
        //         'nom'          => 'Zahra',
        //         'prenom'       => 'Fatima',
        //         'filiere_code' => 'DEVDIG',
        //         'groupe_code'  => 'DD-G1B',
        //         'used'         => false,
        //         'created_at'   => now(),
        //         'updated_at'   => now(),
        //     ],
        //     [
        //         'edu_email'    => 'youssef.malik@ofppt.ma',
        //         'password'     => Hash::make('pass1234'),
        //         'nom'          => 'Malik',
        //         'prenom'       => 'Youssef',
        //         'filiere_code' => 'GI',
        //         'groupe_code'  => 'GI-G1C',
        //         'used'         => false,
        //         'created_at'   => now(),
        //         'updated_at'   => now(),
        //     ],
        //     [
        //         'edu_email'    => 'sara.idrissi@ofppt.ma',
        //         'password'     => Hash::make('pass1234'),
        //         'nom'          => 'Idrissi',
        //         'prenom'       => 'Sara',
        //         'filiere_code' => 'DEVDIG',
        //         'groupe_code'  => 'DD-G2A',
        //         'used'         => false,
        //         'created_at'   => now(),
        //         'updated_at'   => now(),
        //     ],
        // ]);

        // $this->command->info('✅ Seeder complet !');
        // $this->command->table(
        //     ['Rôle', 'Email', 'Mot de passe'],
        //     [
        //         ['Admin',        'admin@ofppt.ma',          'admin123'],
        //         ['Gestionnaire', 'gestionnaire@ofppt.ma',   'gestionnaire123'],
        //         ['Formateur',    'benali@ofppt.ma',          'formateur123'],
        //         ['Stagiaire EDU','ahmed.ali@ofppt.ma',       'pass1234  → /register'],
        //         ['Stagiaire EDU','fatima.zahra@ofppt.ma',    'pass1234  → /register'],
        //         ['Stagiaire EDU','youssef.malik@ofppt.ma',   'pass1234  → /register'],
        //         ['8 salles',     '—',                        '—'],
        //         ['6 groupes',    'DD-G1A, DD-G1B, ...',      '—'],
        //     ]
        // );
    }
}