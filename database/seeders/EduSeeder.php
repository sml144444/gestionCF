<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class EduSeeder extends Seeder
{
    public function run(): void
    {
        // ── 1. Vérifier ou créer la Filière ──
        $filiere = DB::table('filieres')->where('code', 'DD')->first();
        
        if (!$filiere) {
            $filiereId = DB::table('filieres')->insertGetId([
                'name'       => 'Développement Digital',
                'code'       => 'DD',
                'duree'      => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        } else {
            $filiereId = $filiere->id;
        }

        // ── 2. Vérifier ou créer les Groupes ──
        $groupes = ['G1A', 'G1B', 'G2A'];
        $groupeIds = [];
        
        foreach ($groupes as $groupeCode) {
            $groupe = DB::table('groupes')
                ->where('code', $groupeCode)
                ->where('id_filiere', $filiereId)
                ->first();
            
            if (!$groupe) {
                $annee = str_starts_with($groupeCode, 'G2') ? 2 : 1;
                $groupeIds[$groupeCode] = DB::table('groupes')->insertGetId([
                    'id_filiere' => $filiereId,
                    'id_option'  => null,
                    'nbr_limit'  => 25,
                    'annee'      => $annee,
                    'name'       => $groupeCode,
                    'code'       => $groupeCode,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            } else {
                $groupeIds[$groupeCode] = $groupe->id;
            }
        }

        // ── 3. Supprimer les anciens enregistrements EDU (optionnel) ──
        DB::table('edu')->truncate(); // Ou DB::table('edu')->delete();

        // ── 4. Créer les comptes EDU ──
        DB::table('edu')->insert([
            [
                'edu_email'    => 'stagiaire1@ofppt.ma',
                'password'     => Hash::make('password123'),
                'nom'          => 'Nom1',
                'prenom'       => 'Prenom1',
                'filiere_code' => 'DD',
                'groupe_code'  => 'G1A',
                'used'         => false,
                'created_at'   => now(),
                'updated_at'   => now(),
            ],
            [
                'edu_email'    => 'stagiaire2@ofppt.ma',
                'password'     => Hash::make('password123'),
                'nom'          => 'Nom2',
                'prenom'       => 'Prenom2',
                'filiere_code' => 'DD',
                'groupe_code'  => 'G1B',
                'used'         => false,
                'created_at'   => now(),
                'updated_at'   => now(),
            ],
            [
                'edu_email'    => 'stagiaire3@ofppt.ma',
                'password'     => Hash::make('motdepasse456'),
                'nom'          => 'Nom3',
                'prenom'       => 'Prenom3',
                'filiere_code' => 'DD',
                'groupe_code'  => 'G2A',
                'used'         => false,
                'created_at'   => now(),
                'updated_at'   => now(),
            ],
        ]);

        $this->command->info('✅ EDU Seeder terminé !');
    }
}