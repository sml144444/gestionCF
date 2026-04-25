<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Filiere;
use App\Models\Groupe;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class StagiaireDevdigSeeder extends Seeder
{
    public function run(): void
    {
        // ── Filiere (create if missing) ──
        $filiere = Filiere::firstOrCreate(
            ['code' => 'DD'],
            [
                'name' => 'Développement Digital',
                'duree' => 2,
            ]
        );

        // ── Groupes (create if missing) ──
        $groupe101 = Groupe::firstOrCreate(
            ['code' => 'DD-101'],
            [
                'name' => 'DD-101',
                'id_filiere' => $filiere->id,
                'annee' => 1,
                'nbr_limit' => 25
            ]
        );

        $groupe201 = Groupe::firstOrCreate(
            ['code' => 'DD-201'],
            [
                'name' => 'DD-201',
                'id_filiere' => $filiere->id,
                'annee' => 2,
                'nbr_limit' => 25
            ]
        );

        $groupe301 = Groupe::firstOrCreate(
            ['code' => 'DD-301'],
            [
                'name' => 'DD-301',
                'id_filiere' => $filiere->id,
                'annee' => 3,
                'nbr_limit' => 25
            ]
        );

        $students = [

            // 1ère année
            ['Youssef Ait Ali','youssef.aitali@ofppt.ma','Youssef2024',$groupe101->id],
            ['Sara Idrissi','sara.idrissi@ofppt.ma','Sara2024',$groupe101->id],
            ['Hamza Benali','hamza.benali@ofppt.ma','Hamza2024',$groupe101->id],

            // 2ème année
            ['Anas Moufid','anas.moufid@ofppt.ma','Anas2024',$groupe201->id],
            ['Soukayna Belkadi','soukayna.belkadi@ofppt.ma','Souk2024',$groupe201->id],
            ['Zakaria Naciri','zakaria.naciri@ofppt.ma','Zak2024',$groupe201->id],

            // 3ème année
            ['Bilal Amrani','bilal.amrani@ofppt.ma','Bilal2024',$groupe301->id],
            ['Meriem Bensaid','meriem.bensaid@ofppt.ma','Mer2024',$groupe301->id],
            ['Ilyas Mouhib','ilyas.mouhib@ofppt.ma','Ilyas2024',$groupe301->id],
        ];

        foreach($students as [$name,$email,$pass,$groupeId]){

            User::firstOrCreate(
                ['email'=>$email],
                [
                    'name'=>$name,
                    'password'=>Hash::make($pass),
                    'role'=>'stagiaire',
                    'id_filiere'=>$filiere->id,
                    'id_groupe'=>$groupeId,
                ]
            );
        }

        $this->command->info('✅ Stagiaires seeded successfully');
    }
}