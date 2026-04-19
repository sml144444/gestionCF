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
        // ── Récupérer la filière et le groupe ──────────────────────────────
        $filiere = Filiere::where('code', 'DEVDIG')->firstOrFail();
        $groupe  = Groupe::where('code', 'DD-101')->firstOrFail();

        // ── Liste des stagiaires [name, email, password] ───────────────────
        $stagiaires = [
            ['Youssef Ait Ali',    'youssef.aitali@ofppt.ma',   'Youssef2024'],
            ['Sara Idrissi',       'sara.idrissi@ofppt.ma',     'Sara2024'],
            ['Hamza Benali',       'hamza.benali@ofppt.ma',     'Hamza2024'],
            ['Fatima Ouali',       'fatima.ouali@ofppt.ma',     'Fatima2024'],
            ['Khalid Tazi',        'khalid.tazi@ofppt.ma',      'Khalid2024'],
            ['Imane Bouhali',      'imane.bouhali@ofppt.ma',    'Imane2024'],
            ['Mehdi Zaoui',        'mehdi.zaoui@ofppt.ma',      'Mehdi2024'],
            ['Nadia Rachidi',      'nadia.rachidi@ofppt.ma',    'Nadia2024'],
            ['Omar Filali',        'omar.filali@ofppt.ma',      'Omar2024'],
            ['Hiba Errachidi',     'hiba.errachidi@ofppt.ma',   'Hiba2024'],
            ['Anas Moufid',        'anas.moufid@ofppt.ma',      'Anas2024'],
            ['Soukayna Belkadi',   'soukayna.belkadi@ofppt.ma', 'Souk2024'],
            ['Zakaria Naciri',     'zakaria.naciri@ofppt.ma',   'Zak2024'],
            ['Houda El Hakim',     'houda.elhakim@ofppt.ma',    'Houda2024'],
            ['Ayoub Sabiri',       'ayoub.sabiri@ofppt.ma',     'Ayoub2024'],
            ['Rania Hajji',        'rania.hajji@ofppt.ma',      'Rania2024'],
            ['Tariq Benmoussa',    'tariq.benmoussa@ofppt.ma',  'Tariq2024'],
            ['Lamia Cherif',       'lamia.cherif@ofppt.ma',     'Lamia2024'],
            ['Adil Oumarou',       'adil.oumarou@ofppt.ma',     'Adil2024'],
            ['Chaimae Kettani',    'chaimae.kettani@ofppt.ma',  'Chaim2024'],
            ['Bilal Amrani',       'bilal.amrani@ofppt.ma',     'Bilal2024'],
            ['Meriem Bensaid',     'meriem.bensaid@ofppt.ma',   'Mer2024'],
            ['Ilyas Mouhib',       'ilyas.mouhib@ofppt.ma',     'Ilyas2024'],
            ['Yasmine Lahlou',     'yasmine.lahlou@ofppt.ma',   'Yas2024'],
            ['Othmane Fassi',      'othmane.fassi@ofppt.ma',    'Oth2024'],
        ];

        foreach ($stagiaires as [$name, $email, $password]) {
            User::firstOrCreate(
                ['email' => $email],
                [
                    'name'       => $name,
                    'password'   => Hash::make($password),
                    'role'       => 'stagiaire',
                    'id_filiere' => $filiere->id,
                    'id_groupe'  => $groupe->id,
                ]
            );
        }

        $this->command->info('✅ ' . count($stagiaires) . ' stagiaires DEVDIG / DD-101 seedés avec succès.');
    }
}