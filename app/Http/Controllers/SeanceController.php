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

    // ── HELPER : determine start index based on session start time ──
    // S1 : 08:30 – 11:00  (before 11:00)
    // S2 : 11:00 – 13:30  (11:00 to 13:29)
    // S3 : 13:30 – 16:00  (13:30 to 15:59)
    // S4 : 16:00 – 18:30  (16:00 and after)
    private function getStartPartIndex(EmploiDuTemps $emploi): int
    {
        $time = (int)$emploi->date_debut->format('H') * 60
              + (int)$emploi->date_debut->format('i');

        if ($time < 11 * 60)           return 0; // s1
        if ($time < 13 * 60 + 30)      return 1; // s2
        if ($time < 16 * 60)           return 2; // s3
        return 3;                                 // s4
    }

    // ── HELPER : derive active session parts from real start time + duration ──
    private function getActiveParts(EmploiDuTemps $emploi): array
    {
        $minutes    = $emploi->date_debut->diffInMinutes($emploi->date_fin);
        $numParts   = min(4, max(1, (int) floor($minutes / (self::HALF_DUREE * 60))));
        $startIndex = $this->getStartPartIndex($emploi);

        // Make sure we don't go past s4
        $numParts = min($numParts, 4 - $startIndex);

        return array_slice(self::SESSION_PARTS, $startIndex, $numParts);
    }
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
 
        // ── ✅ UPDATED: Last absence warning ───────────────────
        // Show warning ONLY when the last record is:
        //   - type      = 'absence'
        //   - justifie  = false
        //   - admin_validated = false   ← NEW condition
        //
        // If admin has clicked "Autoriser sans justificatif" (admin_validated = true),
        // the warning is suppressed even though justifie stays false.
        $stagiaireIds = $stagiaires->pluck('id');
 
        $lastAbsences = AbsenceRetard::whereIn('id_user', $stagiaireIds)
            ->where('date_event', '<', $emploi->date_debut)
            ->orderBy('date_event', 'desc')
            ->get()
            ->groupBy('id_user')
            ->map(fn($records) => $records->first());
 
        $lastAbsenceWarnings = $lastAbsences
            ->filter(fn($rec) =>
                $rec->type           === 'absence' &&
                $rec->justifie       == false       &&
                $rec->admin_validated == false       // ← NEW: suppress if admin validated
            )
            ->keys()
            ->flip()
            ->map(fn() => true);
 
        return view('seances.show', compact(
            'emploi', 'stagiaires', 'presences', 'coursItems',
            'canPresence', 'canEditClassroom',
            'activeParts', 'lastAbsenceWarnings'
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

        // Only iterate parts that are actually active for this session's duration + start time
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