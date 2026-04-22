<?php

namespace App\Http\Controllers;

use App\Mail\WelcomeMail;
use App\Models\Edu;
use App\Models\EmploiDuTemps;
use App\Models\Filiere;
use App\Models\Groupe;
use App\Models\Module;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Schema;
use Spatie\Permission\Models\Role;

class StagiaireController extends Controller
{
    // ─────────────────────────────────────────────────────────────────────────
    // LIST
    // ─────────────────────────────────────────────────────────────────────────

    public function index(Request $request)
    {
        $user        = Auth::user();
        $isFormateur = $user->role === 'formateur';

        // ── Formateur scope: resolve which groups they teach ──────────────────
        $formateurGroupeIds  = collect();
        $formateurFiliereIds = collect();

        if ($isFormateur) {
            $moduleIds = Module::where('id_user', $user->id)
                ->orWhere('id_user_remplacant', $user->id)
                ->pluck('id');

            $formateurGroupeIds = EmploiDuTemps::whereIn('id_module', $moduleIds)
                ->pluck('id_groupe')
                ->unique()
                ->values();

            $formateurFiliereIds = Groupe::whereIn('id', $formateurGroupeIds)
                ->pluck('id_filiere')
                ->unique()
                ->values();
        }

        $filiereId     = $request->get('filiere_id');
        $search        = $request->get('search', '');
        $groupeId      = $request->get('groupe_id');
        $annee         = $request->get('annee');
        $promo         = $request->get('promo');
        $anneeScolaire = $request->get('annee_scolaire', '');

        $hasAnneeScolaireColumn = Schema::hasColumn('groupes', 'annee_scolaire');

        // Filieres visible to this user
        $filieresQuery = Filiere::withCount('stagiaires')
            ->with(['groupes' => fn ($q) => $q->withCount('stagiaires')]);

        if ($isFormateur) {
            $filieresQuery->whereIn('id', $formateurFiliereIds);
        }

        $filieres = $filieresQuery->get();

        $totalStagiaires = $isFormateur
            ? User::where('role', 'stagiaire')->whereIn('id_groupe', $formateurGroupeIds)->count()
            : User::where('role', 'stagiaire')->count();

        // ── MODE A — no filière selected ──────────────────────────────────────
        if (! $filiereId) {
            return view('stagiaire.index', [
                'filiereId'              => null,
                'selectedFiliere'        => null,
                'filieres'               => $filieres,
                'totalStagiaires'        => $totalStagiaires,
                'hasAnneeScolaireColumn' => $hasAnneeScolaireColumn,
                'stagiaires'             => collect(),
                'groupes'                => collect(),
                'allGroupes'             => collect(),
                'search'                 => '',
                'groupeId'               => null,
                'annee'                  => null,
                'promo'                  => null,
                'anneeScolaire'          => '',
                'hasFilters'             => false,
                'anneesScolaires'        => collect(),
                'promos'                 => collect(),
                'isFormateur'            => $isFormateur,
            ]);
        }

        // ── MODE B — filière selected ─────────────────────────────────────────
        $selectedFiliere = Filiere::findOrFail($filiereId);

        if ($isFormateur && ! $formateurFiliereIds->contains($filiereId)) {
            abort(403, 'Vous n\'enseignez pas dans cette filière.');
        }

        $groupes = Groupe::where('id_filiere', $filiereId)
            ->when($isFormateur, fn ($q) => $q->whereIn('id', $formateurGroupeIds))
            ->withCount('stagiaires')
            ->orderBy('annee')
            ->orderBy('name')
            ->get();

        $allGroupes      = $groupes;
        $promos          = $groupes->pluck('promo')->filter()->unique()->sort()->values();
        $anneesScolaires = collect();

        if ($hasAnneeScolaireColumn) {
            $anneesScolaires = Groupe::where('id_filiere', $filiereId)
                ->when($isFormateur, fn ($q) => $q->whereIn('id', $formateurGroupeIds))
                ->whereNotNull('annee_scolaire')
                ->pluck('annee_scolaire')
                ->unique()->sort()->values();
        }

        $query = User::where('role', 'stagiaire')
            ->where('id_filiere', $filiereId)
            ->with('groupe')
            ->orderBy('name');

        if ($isFormateur) {
            $query->whereIn('id_groupe', $formateurGroupeIds);
        }

        if ($search) {
            $query->where(fn ($q) => $q
                ->where('name', 'like', "%{$search}%")
                ->orWhere('email', 'like', "%{$search}%")
                ->orWhere('cin', 'like', "%{$search}%")
            );
        }

        if ($groupeId)      { $query->where('id_groupe', $groupeId); }
        if ($annee)         { $query->whereHas('groupe', fn ($q) => $q->where('annee', $annee)); }
        if ($promo)         { $query->whereHas('groupe', fn ($q) => $q->where('promo', $promo)); }
        if ($anneeScolaire && $hasAnneeScolaireColumn) {
            $query->whereHas('groupe', fn ($q) => $q->where('annee_scolaire', $anneeScolaire));
        }

        $hasFilters = (bool) ($search || $groupeId || $annee || $promo || $anneeScolaire);
        $stagiaires = $query->paginate(20)->withQueryString();

        return view('stagiaire.index', compact(
            'filiereId', 'selectedFiliere', 'filieres',
            'stagiaires', 'groupes', 'allGroupes',
            'search', 'groupeId', 'annee', 'promo', 'anneeScolaire',
            'hasFilters', 'hasAnneeScolaireColumn', 'anneesScolaires',
            'totalStagiaires', 'promos', 'isFormateur'
        ));
    }

    // ─────────────────────────────────────────────────────────────────────────
    // CREATE
    // ─────────────────────────────────────────────────────────────────────────

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'           => 'required|string|max:255',
            'email'          => 'required|email|unique:users,email',
            'cin'            => 'nullable|string|max:50',
            'phone'          => ['nullable', 'string', 'max:20', 'regex:/^[\+]?[0-9\s\-\(\)\.]{6,20}$/'],
            'date_naissance' => 'nullable|date',
            'id_groupe'      => 'nullable|exists:groupes,id',
            'id_filiere'     => 'required|exists:filieres,id',
        ]);

        if (! empty($data['id_groupe'])) {
            $groupe = Groupe::withCount('stagiaires')->findOrFail($data['id_groupe']);

            if ($groupe->stagiaires_count >= $groupe->nbr_limit) {
                return back()->withInput()->withErrors([
                    'id_groupe' => "Le groupe \"{$groupe->name}\" est complet ({$groupe->stagiaires_count}/{$groupe->nbr_limit} places).",
                ]);
            }

            $data['id_filiere'] = $groupe->id_filiere;
        }

        $plainPassword    = $this->generateSecurePassword();
        $data['password'] = Hash::make($plainPassword);
        $data['role']     = 'stagiaire';

        $stagiaire = User::create($data);

        if (Role::where('name', 'stagiaire')->exists()) {
            $stagiaire->syncRoles(['stagiaire']);
        }

        Mail::to($stagiaire->email)->queue(new WelcomeMail($stagiaire, $plainPassword));

        return back()->with('success', "Stagiaire \"{$stagiaire->name}\" créé. Ses identifiants ont été envoyés par e-mail.");
    }

    // ─────────────────────────────────────────────────────────────────────────
    // UPDATE
    // ─────────────────────────────────────────────────────────────────────────

    public function update(Request $request, User $stagiaire)
    {
        $data = $request->validate([
            'name'           => 'required|string|max:255',
            'email'          => 'required|email|unique:users,email,' . $stagiaire->id,
            'password'       => 'nullable|string|min:6',
            'cin'            => 'nullable|string|max:50',
            'phone'          => ['nullable', 'string', 'max:20', 'regex:/^[\+]?[0-9\s\-\(\)\.]{6,20}$/'],
            'date_naissance' => 'nullable|date',
            'id_groupe'      => 'nullable|exists:groupes,id',
            'id_filiere'     => 'required|exists:filieres,id',
        ]);

        if (! empty($data['id_groupe'])) {
            $groupe = Groupe::withCount('stagiaires')->findOrFail($data['id_groupe']);

            $isChangingGroup = (string) $stagiaire->id_groupe !== (string) $data['id_groupe'];

            if ($isChangingGroup && $groupe->stagiaires_count >= $groupe->nbr_limit) {
                return back()->withInput()->withErrors([
                    'id_groupe' => "Le groupe \"{$groupe->name}\" est complet ({$groupe->stagiaires_count}/{$groupe->nbr_limit} places).",
                ]);
            }

            $data['id_filiere'] = $groupe->id_filiere;
        } else {
            $data['id_groupe'] = null;
        }

        if (! empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }

        $stagiaire->update($data);

        if (Role::where('name', 'stagiaire')->exists() && ! $stagiaire->hasRole('stagiaire')) {
            $stagiaire->syncRoles(['stagiaire']);
        }

        return back()->with('success', 'Stagiaire "' . $stagiaire->name . '" mis à jour.');
    }

    // ─────────────────────────────────────────────────────────────────────────
    // DELETE — ✅ remet le compte EDU en "en attente" si existe
    // ─────────────────────────────────────────────────────────────────────────

    public function destroy(User $stagiaire)
    {
        $name  = $stagiaire->name;
        $email = $stagiaire->email;

        // ✅ Chercher un compte EDU lié au même email
        //    et le remettre en "en attente" (used = false)
        $edu = Edu::where('edu_email', $email)->first();
        if ($edu && $edu->used) {
            $edu->used = false;
            $edu->save();
        }

        $stagiaire->delete();

        $message = "Stagiaire \"{$name}\" supprimé.";
        if ($edu && $edu->wasChanged('used')) {
            $message .= " Le compte EDU associé a été remis en attente.";
        }

        return back()->with('success', $message);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // PRIVATE HELPERS
    // ─────────────────────────────────────────────────────────────────────────

    private function generateSecurePassword(int $length = 12): string
    {
        $upper   = 'ABCDEFGHJKLMNPQRSTUVWXYZ';
        $lower   = 'abcdefghjkmnpqrstuvwxyz';
        $digits  = '23456789';
        $special = '@#$%!&*';

        $password  = substr(str_shuffle($upper),   0, 2);
        $password .= substr(str_shuffle($lower),   0, 3);
        $password .= substr(str_shuffle($digits),  0, 3);
        $password .= substr(str_shuffle($special), 0, 2);

        $all      = $upper . $lower . $digits . $special;
        $password .= substr(str_shuffle(str_repeat($all, 3)), 0, $length - 10);

        return str_shuffle($password);
    }
}