<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class EduSeeder extends Seeder
{
    public function run(): void
    {
        // ── 1. Filière
        $filiereId = DB::table('filieres')->insertGetId([
            'name'       => 'Développement Digital',
            'duree'      => 2,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // ── 2. Groupes  (annee=1 → onglet An.1 | annee=2 → onglet An.2/2.5)
        $groupe1AId = DB::table('groupes')->insertGetId([
            'id_filiere' => $filiereId,
            'id_option'  => null,
            'nbr_limit'  => 25,
            'annee'      => 1,       // première année
            'name'       => 'G1A',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $groupe1BId = DB::table('groupes')->insertGetId([
            'id_filiere' => $filiereId,
            'id_option'  => null,
            'nbr_limit'  => 25,
            'annee'      => 1,       // première année
            'name'       => 'G1B',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $groupe2AId = DB::table('groupes')->insertGetId([
            'id_filiere' => $filiereId,
            'id_option'  => null,
            'nbr_limit'  => 25,
            'annee'      => 2,       // deuxième année
            'name'       => 'G2A',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // ── 3. EDU pre-registrations
        DB::table('edu')->insert([
            [
                'edu_email'  => 'stagiaire1@ofppt.ma',
                'password'   => Hash::make('password123'),
                'id_filiere' => $filiereId,
                'id_groupe'  => $groupe1AId,
                'used'       => false,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'edu_email'  => 'stagiaire2@ofppt.ma',
                'password'   => Hash::make('password123'),
                'id_filiere' => $filiereId,
                'id_groupe'  => $groupe1BId,
                'used'       => false,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'edu_email'  => 'stagiaire3@ofppt.ma',
                'password'   => Hash::make('motdepasse456'),
                'id_filiere' => $filiereId,
                'id_groupe'  => $groupe2AId,
                'used'       => false,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        $this->command->info('✅ EDU Seeder terminé !');
        $this->command->table(
            ['Email', 'Mot de passe', 'Groupe', 'Année'],
            [
                ['stagiaire1@ofppt.ma', 'password123',   'G1A', '1ère année'],
                ['stagiaire2@ofppt.ma', 'password123',   'G1B', '1ère année'],
                ['stagiaire3@ofppt.ma', 'motdepasse456', 'G2A', '2ème année'],
            ]
        );
    }
}