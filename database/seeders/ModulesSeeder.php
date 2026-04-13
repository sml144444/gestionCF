<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\User;

class ModulesSeeder extends Seeder
{
    public function run(): void
    {
        // ── Récupérer les filières et formateurs existants ──
        $filiereDev = DB::table('filieres')->where('name', 'LIKE', '%Digital%')->first();
        $filiereGI  = DB::table('filieres')->where('name', 'LIKE', '%Informatique%')->first();

        // Formateur par défaut (le premier formateur trouvé)
        $formateur = User::where('role', 'formateur')->first();

        if (! $formateur) {
            $this->command->warn('⚠️ Aucun formateur trouvé — crée un formateur d\'abord.');
            return;
        }

        $formateurId = $formateur->id;
        $now = now();

        // ════════════════════════════════════════════════════
        // FILIÈRE : Développement Digital
        // ════════════════════════════════════════════════════
        if ($filiereDev) {
            $fId = $filiereDev->id;

            $modulesDev = [
                // ── Modules techniques ──
                ['name' => 'PHP & Laravel',              'nbr_heure' => 90,  'coefficience' => 3, 'type' => 'local'],
                ['name' => 'JavaScript & TypeScript',    'nbr_heure' => 80,  'coefficience' => 3, 'type' => 'local'],
                ['name' => 'HTML & CSS',                 'nbr_heure' => 60,  'coefficience' => 2, 'type' => 'regional'],
                ['name' => 'React.js',                   'nbr_heure' => 70,  'coefficience' => 3, 'type' => 'local'],
                ['name' => 'Vue.js',                     'nbr_heure' => 60,  'coefficience' => 2, 'type' => 'local'],
                ['name' => 'Base de données SQL',        'nbr_heure' => 75,  'coefficience' => 3, 'type' => 'regional'],
                ['name' => 'MySQL & PostgreSQL',         'nbr_heure' => 50,  'coefficience' => 2, 'type' => 'local'],
                ['name' => 'APIs REST & GraphQL',        'nbr_heure' => 60,  'coefficience' => 3, 'type' => 'local'],
                ['name' => 'Git & DevOps',               'nbr_heure' => 40,  'coefficience' => 2, 'type' => 'regional'],
                ['name' => 'Python',                     'nbr_heure' => 50,  'coefficience' => 2, 'type' => 'regional'],
                ['name' => 'Node.js & Express',          'nbr_heure' => 60,  'coefficience' => 2, 'type' => 'local'],
                ['name' => 'Algorithmique',              'nbr_heure' => 60,  'coefficience' => 3, 'type' => 'regional'],

                // ── Modules transversaux ──
                ['name' => 'Communication & Soft Skills','nbr_heure' => 30,  'coefficience' => 1, 'type' => 'regional'],
                ['name' => 'Gestion de projet',          'nbr_heure' => 30,  'coefficience' => 1, 'type' => 'regional'],
                ['name' => 'Anglais technique',          'nbr_heure' => 40,  'coefficience' => 2, 'type' => 'regional'],
            ];

            foreach ($modulesDev as $m) {
                DB::table('modules')->insertOrIgnore([
                    'id_filiere'   => $fId,
                    'id_option'    => null,
                    'name'         => $m['name'],
                    'coefficience' => $m['coefficience'],
                    'nbr_heure'    => $m['nbr_heure'],
                    'id_user'      => $formateurId,
                    'type'         => $m['type'],
                    'created_at'   => $now,
                    'updated_at'   => $now,
                ]);
            }

            $this->command->info('✅ ' . count($modulesDev) . ' modules créés pour Développement Digital');
        } else {
            $this->command->warn('⚠️ Filière "Développement Digital" non trouvée — lance EduSeeder d\'abord.');
        }

        // ════════════════════════════════════════════════════
        // FILIÈRE : Gestion Informatique (si elle existe)
        // ════════════════════════════════════════════════════
        if ($filiereGI) {
            $fId = $filiereGI->id;

            $modulesGI = [
                ['name' => 'Systèmes d\'exploitation',  'nbr_heure' => 70,  'coefficience' => 3, 'type' => 'regional'],
                ['name' => 'Réseaux & Administration',  'nbr_heure' => 80,  'coefficience' => 3, 'type' => 'regional'],
                ['name' => 'Sécurité informatique',     'nbr_heure' => 60,  'coefficience' => 3, 'type' => 'regional'],
                ['name' => 'Linux',                     'nbr_heure' => 50,  'coefficience' => 2, 'type' => 'local'],
                ['name' => 'Virtualisation & Cloud',    'nbr_heure' => 50,  'coefficience' => 2, 'type' => 'local'],
                ['name' => 'Base de données SQL',       'nbr_heure' => 60,  'coefficience' => 2, 'type' => 'regional'],
                ['name' => 'Scripting Bash & Python',   'nbr_heure' => 40,  'coefficience' => 2, 'type' => 'local'],
                ['name' => 'Anglais technique',         'nbr_heure' => 40,  'coefficience' => 2, 'type' => 'regional'],
            ];

            foreach ($modulesGI as $m) {
                DB::table('modules')->insertOrIgnore([
                    'id_filiere'   => $fId,
                    'id_option'    => null,
                    'name'         => $m['name'],
                    'coefficience' => $m['coefficience'],
                    'nbr_heure'    => $m['nbr_heure'],
                    'id_user'      => $formateurId,
                    'type'         => $m['type'],
                    'created_at'   => $now,
                    'updated_at'   => $now,
                ]);
            }

            $this->command->info('✅ ' . count($modulesGI) . ' modules créés pour Gestion Informatique');
        }

        // ── Résumé ───────────────────────────────────────────
        $total = DB::table('modules')->count();
        $this->command->info("📚 Total modules en base : {$total}");

        $this->command->table(
            ['Filière', 'Module', 'Heures', 'Coeff', 'Type'],
            DB::table('modules')
                ->join('filieres', 'modules.id_filiere', '=', 'filieres.id')
                ->select('filieres.name as filiere', 'modules.name', 'modules.nbr_heure', 'modules.coefficience', 'modules.type')
                ->orderBy('filieres.name')->orderBy('modules.name')
                ->get()
                ->map(fn($r) => [$r->filiere, $r->name, $r->nbr_heure . 'h', $r->coefficience, $r->type])
                ->toArray()
        );
    }
}