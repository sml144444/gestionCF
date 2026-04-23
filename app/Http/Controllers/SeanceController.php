<?php

namespace App\Http\Controllers;

use App\Models\AbsenceRetard;
use App\Models\Cours;
use App\Models\EmploiDuTemps;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Mail\AbsenceMail;
use App\Mail\NouveauDocumentMail;
use Illuminate\Support\Facades\Mail;

class SeanceController extends Controller
{
    // Each half-session is exactly 2.5 hours
    const HALF_DUREE    = 2.5;
    const SESSION_PARTS = ['s1', 's2', 's3', 's4'];

    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware(function ($request, $next) {
            if (!Auth::user()->can('emploi-view')) {
                abort(403);
            }
            return $next($request);
        });
    }

    // ── HELPER : derive active session parts from real duration ──
    private function getActiveParts(EmploiDuTemps $emploi): array
    {
        $minutes  = $emploi->date_debut->diffInMinutes($emploi->date_fin);
        $numParts = min(4, max(1, (int) floor($minutes / (self::HALF_DUREE * 60))));
        return array_slice(self::SESSION_PARTS, 0, $numParts);
    }

    

    // ── SHOW ──────────────────────────────────────────────────
public function show(EmploiDuTemps $emploi)
{
    $emploi->load(['module', 'groupe', 'salle', 'gestionnaire', 'remplacant']);

    $user = Auth::user();

    // Stagiaire sees only themselves
    if ($user->role === 'stagiaire') {
        $stagiaires = User::where('id', $user->id)
            ->where('id_groupe', $emploi->id_groupe)
            ->get();
    } else {
        $stagiaires = User::where('id_groupe', $emploi->id_groupe)
            ->where('role', 'stagiaire')
            ->orderBy('name')
            ->get();
    }

    $presenceCours = Cours::where('id_emplois_du_temps', $emploi->id)
        ->where('titre', '__presence__')
        ->first();

    $presences = $presenceCours
        ? AbsenceRetard::where('id_cours', $presenceCours->id)
            ->get()
            ->keyBy(fn($a) => $a->id_user . '_' . $a->session_part)
        : collect();

    $coursItems = Cours::where('id_emplois_du_temps', $emploi->id)
        ->where('titre', '!=', '__presence__')
        ->with('formateur')
        ->latest()
        ->get();

    $canPresence = Auth::user()->can('emploi-view-all-groups')
                || in_array(Auth::user()->role, ['admin', 'gestionnaire', 'formateur']);

    $canEditClassroom = Auth::user()->can('emploi-view-all-groups')
                     || in_array(Auth::user()->role, ['admin', 'formateur']);

    $activeParts = $this->getActiveParts($emploi);

    return view('seances.show', compact(
        'emploi', 'stagiaires', 'presences', 'coursItems',
        'canPresence', 'canEditClassroom',
        'activeParts'
    ));
}

    // ── SAVE PRESENCE ─────────────────────────────────────────
    public function savePresence(Request $request, EmploiDuTemps $emploi): RedirectResponse
    {
        if (!in_array(Auth::user()->role, ['admin', 'gestionnaire', 'formateur'])
            && !Auth::user()->can('emploi-view-all-groups')) {
            abort(403);
        }

        if ($emploi->statut === 'annule') {
            return back()->with('error', 'Impossible de saisir la présence pour une séance annulée.');
        }

        $request->validate([
            'presences'                => 'nullable|array',
            'presences.*.stagiaire_id' => 'required|exists:users,id',
        ]);

        $presenceCours = Cours::firstOrCreate(
            ['id_emplois_du_temps' => $emploi->id, 'titre' => '__presence__'],
            ['statut' => 'faite', 'created_by' => Auth::id()]
        );

        // Only iterate parts that are actually active for this session's duration
        $activeParts   = $this->getActiveParts($emploi);
        $inactiveParts = array_diff(self::SESSION_PARTS, $activeParts);

        $absentStagiaireIds = [];

        foreach ($request->input('presences', []) as $entry) {
            $stagiaireId   = $entry['stagiaire_id'];
            $hasAnyAbsence = false;

            foreach ($activeParts as $part) {
                // Checkbox sends '1' when checked (absent)
                $isAbsent = !empty($entry[$part]);

                if (!$isAbsent) {
                    // Present → remove any existing absence record for this half
                    AbsenceRetard::where('id_cours', $presenceCours->id)
                        ->where('id_user', $stagiaireId)
                        ->where('session_part', $part)
                        ->delete();
                } else {
                    // Absent → upsert
                    AbsenceRetard::updateOrCreate(
                        [
                            'id_cours'     => $presenceCours->id,
                            'id_user'      => $stagiaireId,
                            'session_part' => $part,
                        ],
                        [
                            'type'       => 'absence',
                            'date_event' => $emploi->date_debut,
                            'duree'      => self::HALF_DUREE,
                            'justifie'   => false,
                        ]
                    );
                    $hasAnyAbsence = true;
                }
            }

            // Clean up stale records for parts no longer active
            // (protects against session duration being shortened after data was saved)
            foreach ($inactiveParts as $part) {
                AbsenceRetard::where('id_cours', $presenceCours->id)
                    ->where('id_user', $stagiaireId)
                    ->where('session_part', $part)
                    ->delete();
            }

            if ($hasAnyAbsence) {
                $absentStagiaireIds[$stagiaireId] = true;
            }
        }

        // ── Send one email per absent stagiaire ────────────────
        $enregistreePar = Auth::user();
        $sentCount      = 0;
        $failedEmails   = [];

        foreach (array_keys($absentStagiaireIds) as $stagiaireId) {
            $stagiaire = User::find($stagiaireId);
            if (!$stagiaire || !$stagiaire->email) continue;
            if (!filter_var($stagiaire->email, FILTER_VALIDATE_EMAIL)) {
                $failedEmails[] = $stagiaire->email;
                continue;
            }
            try {
                Mail::to($stagiaire->email)->queue(new AbsenceMail(
                    stagiaire:      $stagiaire,
                    emploi:         $emploi,
                    enregistreePar: $enregistreePar,
                    justified:      false,
                    justification:  null,
                ));
                $sentCount++;
            } catch (\Exception $e) {
                $failedEmails[] = $stagiaire->email;
                \Log::error("Erreur email absence {$stagiaire->email}: " . $e->getMessage());
            }
        }

        $msg = 'Liste de présence enregistrée.';
        if ($sentCount > 0)        $msg .= " Notification d'absence envoyée à {$sentCount} stagiaire(s).";
        if (!empty($failedEmails)) $msg .= ' (' . count($failedEmails) . ' email(s) invalide(s) ignoré(s))';

        return redirect()->route('seances.show', $emploi)->with('success', $msg);
    }

    // ── ADD CLASSROOM RESOURCE ────────────────────────────────
    public function addRessource(Request $request, EmploiDuTemps $emploi): RedirectResponse
    {
        if (!in_array(Auth::user()->role, ['admin', 'formateur'])
            && !Auth::user()->can('emploi-view-all-groups')) {
            abort(403);
        }

        $request->validate([
            'titre'       => 'required|string|max:255',
            'type'        => 'required|in:pdf,lien,texte',
            'fichier'     => 'nullable|file|max:20480|mimes:pdf,doc,docx,zip,png,jpg,jpeg,txt,pptx,xlsx',
            'lien'        => 'nullable|url|max:500',
            'description' => 'nullable|string|max:10000',
        ]);

        $fichierPath = null;
        if ($request->hasFile('fichier') && $request->file('fichier')->isValid()) {
            $fichierPath = $request->file('fichier')->store('classroom', 'public');
        }

        $cours = Cours::create([
            'id_emplois_du_temps' => $emploi->id,
            'titre'               => $request->titre,
            'description'         => $request->description,
            'fichier'             => $fichierPath ? [$fichierPath] : null,
            'lien'                => $request->type === 'lien' ? $request->lien : null,
            'statut'              => 'prevue',
            'created_by'          => Auth::id(),
        ]);

        $emploi->load(['module', 'groupe', 'gestionnaire']);

        $otherDocs = Cours::where('id_emplois_du_temps', $emploi->id)
            ->where('titre', '!=', '__presence__')
            ->where('id', '!=', $cours->id)
            ->latest()->take(3)->get();

        $stagiaires = User::where('id_groupe', $emploi->id_groupe)
            ->where('role', 'stagiaire')
            ->whereNotNull('email')
            ->get();

        $sharedBy     = Auth::user();
        $sentCount    = 0;
        $failedEmails = [];

        foreach ($stagiaires as $stagiaire) {
            if (!filter_var($stagiaire->email, FILTER_VALIDATE_EMAIL)) {
                $failedEmails[] = $stagiaire->email;
                continue;
            }
            try {
                Mail::to($stagiaire->email)->queue(new NouveauDocumentMail(
                    recipient: $stagiaire,
                    document:  $cours,
                    emploi:    $emploi,
                    sharedBy:  $sharedBy,
                    otherDocs: $otherDocs,
                ));
                $sentCount++;
            } catch (\Exception $e) {
                $failedEmails[] = $stagiaire->email;
                \Log::error("Erreur email ressource {$stagiaire->email}: " . $e->getMessage());
            }
        }

        $msg = "Ressource « {$request->titre} » ajoutée.";
        if ($sentCount > 0)        $msg .= " Notification envoyée à {$sentCount} stagiaire(s).";
        if (!empty($failedEmails)) $msg .= ' (' . count($failedEmails) . ' email(s) invalide(s) ignoré(s))';

        return redirect()->route('seances.show', $emploi)->with('success', $msg);
    }

    // ── DELETE CLASSROOM RESOURCE ─────────────────────────────
    public function deleteRessource(EmploiDuTemps $emploi, Cours $cours): RedirectResponse
    {
        if (!in_array(Auth::user()->role, ['admin', 'formateur'])
            && !Auth::user()->can('emploi-view-all-groups')) {
            abort(403);
        }

        if ($cours->id_emplois_du_temps !== $emploi->id || $cours->titre === '__presence__') {
            abort(404);
        }

        if ($cours->fichier) {
            foreach ($cours->fichier as $path) {
                Storage::disk('public')->delete($path);
            }
        }

        $titre = $cours->titre;
        $cours->delete();

        return redirect()->route('seances.show', $emploi)
            ->with('success', "Ressource « {$titre} » supprimée.");
    }
}