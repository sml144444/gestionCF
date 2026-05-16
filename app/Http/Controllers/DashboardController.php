<?php

namespace App\Http\Controllers;

use App\Models\AbsenceRetard;
use App\Models\Cours;
use App\Models\Edu;
use App\Models\EmploiDuTemps;
use App\Models\Filiere;
use App\Models\Groupe;
use App\Models\Module;
use App\Models\Reclamation;
use App\Models\Reportation;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    // ─── ADMIN ───────────────────────────────────────────
    public function admin()
    {
        $stats = [
            'total_users'       => User::count(),
            'stagiaires'        => User::where('role', 'stagiaire')->count(),
            'formateurs'        => User::where('role', 'formateur')->count(),
            'gestionnaires'     => User::where('role', 'gestionnaire')->count(),
            'filieres'          => Filiere::count(),
            'groupes'           => Groupe::count(),
            'edu_pending'       => Edu::where('used', false)->count(),
            'reclamations_open' => Reclamation::where('status', 'en_attente')->count(),
        ];

        return view('admin.dashboard', compact('stats'));
    }

    // ─── GESTIONNAIRE ────────────────────────────────────
    public function gestionnaire()
    {
        [$weekStart, $weekEnd] = $this->currentWeek();

        $stats = [
            'stagiaires'        => User::where('role', 'stagiaire')->count(),
            'groupes'           => Groupe::count(),
            'edu_pending'       => Edu::where('used', false)->count(),
            'reclamations_open' => Reclamation::where('status', 'en_attente')->count(),
            'reportations_open' => Reportation::where('status', 'en_attente')->count(),
            'seances_semaine'   => EmploiDuTemps::whereBetween('date_debut', [$weekStart, $weekEnd])
                                        ->where('statut', 'actif')->count(),
            'seances_brouillon' => EmploiDuTemps::where('statut', 'brouillon')->count(),
        ];

        return view('gestionnaire.dashboard', compact('stats'));
    }

    // ─── FORMATEUR ───────────────────────────────────────
    public function formateur()
    {
        $user = Auth::user();
        [$weekStart, $weekEnd] = $this->currentWeek();

        $visibleUntil = $this->visibleUntilFor('formateur');

        $stats = [
            'seances_semaine'      => EmploiDuTemps::where(function ($q) use ($user) {
                                            $q->where('id_user', $user->id)
                                              ->orWhere('id_user_remplacant', $user->id);
                                        })
                                        ->whereBetween('date_debut', [$weekStart, $weekEnd])
                                        ->where('statut', 'actif')
                                        ->count(),
            'modules_count'        => Module::where('id_user', $user->id)->count(),
            'groupes_count'        => EmploiDuTemps::where('id_user', $user->id)
                                            ->distinct('id_groupe')
                                            ->count('id_groupe'),
            'reportations_pending' => Reportation::where('id_user', $user->id)
                                            ->where('status', 'en_attente')
                                            ->count(),
        ];

        $current_seance = EmploiDuTemps::where(function ($q) use ($user) {
                                $q->where('id_user', $user->id)
                                  ->orWhere('id_user_remplacant', $user->id);
                            })
                            ->where('date_debut', '<=', now())
                            ->where('date_fin',   '>=', now())
                            ->whereIn('statut', ['actif', 'brouillon'])
                            ->with(['module', 'groupe', 'salle'])
                            ->first();

        $next_seance = EmploiDuTemps::where(function ($q) use ($user) {
                            $q->where('id_user', $user->id)
                              ->orWhere('id_user_remplacant', $user->id);
                        })
                        ->where('date_debut', '>', now())
                        ->where('date_debut', '<=', $visibleUntil)
                        ->where('statut', 'actif')
                        ->orderBy('date_debut')
                        ->with(['module', 'groupe', 'salle'])
                        ->first();

        $prochaines_seances = EmploiDuTemps::where(function ($q) use ($user) {
                                    $q->where('id_user', $user->id)
                                      ->orWhere('id_user_remplacant', $user->id);
                                })
                                ->where('date_debut', '>=', now())
                                ->where('date_debut', '<=', $visibleUntil)
                                ->where('statut', 'actif')
                                ->orderBy('date_debut')
                                ->with(['module', 'groupe', 'salle'])
                                ->limit(5)
                                ->get();

        return view('formateur.dashboard', compact(
            'stats', 'prochaines_seances', 'current_seance', 'next_seance'
        ));
    }

    // ─── STAGIAIRE ───────────────────────────────────────
    public function stagiaire()
    {
        $user = Auth::user();
        [$weekStart, $weekEnd] = $this->currentWeek();

        $idGroupe = $user->id_groupe;

        $visibleUntil = $this->visibleUntilFor('stagiaire');

        // ── Base query reused for absence stats ──────────────────────────────
        $absencesQuery = AbsenceRetard::where('id_user', $user->id)
                            ->where('type', 'absence');

        $stats = [
            // ✅ FIXED: count distinct days (date_event), not individual session rows
            // Previously counted every session row separately (S1, S2... = multiple rows per day)
            'absences_count'  => (clone $absencesQuery)
                                    ->distinct('date_event')
                                    ->count('date_event'),

            // ✅ FIXED: unjustified also counts distinct days
            'absences_injust' => (clone $absencesQuery)
                                    ->where('justifie', false)
                                    ->distinct('date_event')
                                    ->count('date_event'),

            // Retards: each late arrival is its own independent event — no change
            'retards_count'   => AbsenceRetard::where('id_user', $user->id)
                                    ->where('type', 'retard')
                                    ->count(),

            'cours_semaine'   => EmploiDuTemps::where('id_groupe', $idGroupe)
                                    ->whereBetween('date_debut', [$weekStart, $weekEnd])
                                    ->where('statut', 'actif')
                                    ->count(),
        ];

        // Current session in progress
        $current_seance = EmploiDuTemps::where('id_groupe', $idGroupe)
                            ->where('date_debut', '<=', now())
                            ->where('date_fin',   '>=', now())
                            ->where('statut', 'actif')
                            ->with(['module', 'salle', 'remplacant', 'gestionnaire'])
                            ->first();

        // Next upcoming session — capped to $visibleUntil
        $next_seance = EmploiDuTemps::where('id_groupe', $idGroupe)
                        ->where('date_debut', '>', now())
                        ->where('date_debut', '<=', $visibleUntil)
                        ->where('statut', 'actif')
                        ->orderBy('date_debut')
                        ->with(['module', 'salle', 'remplacant', 'gestionnaire'])
                        ->first();

        // Upcoming list — capped to $visibleUntil
        $prochaines_seances = EmploiDuTemps::where('id_groupe', $idGroupe)
                                ->where('date_debut', '>=', now())
                                ->where('date_debut', '<=', $visibleUntil)
                                ->where('statut', 'actif')
                                ->orderBy('date_debut')
                                ->with(['module', 'salle', 'remplacant', 'gestionnaire'])
                                ->limit(5)
                                ->get();

        $derniers_documents = Cours::whereHas('emploi', fn($q) => $q->where('id_groupe', $idGroupe))
                                ->latest()
                                ->limit(4)
                                ->get();

        return view('stagiaire.dashboard', compact(
            'stats', 'prochaines_seances', 'derniers_documents',
            'current_seance', 'next_seance'
        ));
    }

    // ─── Helpers ─────────────────────────────────────────

    private function currentWeek(): array
    {
        return [
            Carbon::now()->startOfWeek(Carbon::MONDAY),
            Carbon::now()->endOfWeek(Carbon::SUNDAY),
        ];
    }

    /**
     * Returns the latest datetime a stagiaire/formateur is allowed
     * to see sessions up to.
     *
     * Rule:
     *   - Before Sunday 00:00  → visible up to end of CURRENT week (Saturday 23:59)
     *   - From Sunday 00:00    → visible up to end of NEXT week (Saturday 23:59)
     */
    private function visibleUntilFor(string $role): Carbon
    {
        $joursAvance   = 1;
        $prochainLundi = Carbon::now()->startOfWeek(Carbon::MONDAY)->addWeek();
        $visibleDepuis = $prochainLundi->copy()->subDays($joursAvance)->startOfDay(); // Sunday 00:00

        $peutVoirSemaineProchaine = Carbon::now()->gte($visibleDepuis);

        if ($peutVoirSemaineProchaine) {
            // Sunday or later → show current week + next week
            return $prochainLundi->copy()->addDays(5)->endOfDay(); // next Saturday 23:59
        }

        // Before Sunday → show only current week
        return Carbon::now()->startOfWeek(Carbon::MONDAY)->addDays(5)->endOfDay(); // this Saturday 23:59
    }
}