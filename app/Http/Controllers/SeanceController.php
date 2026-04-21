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

    // ── SHOW ──────────────────────────────────────────────────
    public function show(EmploiDuTemps $emploi)
    {
        $emploi->load(['module', 'groupe', 'salle', 'gestionnaire', 'remplacant']);

        // Stagiaires of this groupe (no assumption about Groupe->stagiaires())
        $stagiaires = User::where('id_groupe', $emploi->id_groupe)
            ->where('role', 'stagiaire')
            ->orderBy('name')
            ->get();

        // Find the hidden "presence" cours entry for this séance
        $presenceCours = Cours::where('id_emplois_du_temps', $emploi->id)
            ->where('titre', '__presence__')
            ->first();

        // Keyed by id_user for easy lookup in the blade
        $presences = $presenceCours
            ? AbsenceRetard::where('id_cours', $presenceCours->id)
                ->get()
                ->keyBy('id_user')
            : collect();

        // Classroom resources (all cours except the hidden presence entry)
        $coursItems = Cours::where('id_emplois_du_temps', $emploi->id)
            ->where('titre', '!=', '__presence__')
            ->with('formateur')
            ->latest()
            ->get();

        // Permission flags
        $canPresence = Auth::user()->can('emploi-view-all-groups')
                    || in_array(Auth::user()->role, ['admin', 'gestionnaire', 'formateur']);

        $canEditClassroom = Auth::user()->can('emploi-view-all-groups')
                         || in_array(Auth::user()->role, ['admin', 'formateur']);

        return view('seances.show', compact(
            'emploi', 'stagiaires', 'presences', 'coursItems',
            'canPresence', 'canEditClassroom'
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
            'presences'                    => 'nullable|array',
            'presences.*.stagiaire_id'     => 'required|exists:users,id',
            'presences.*.status'           => 'required|in:present,retard,absence',
        ]);

        // Get or create the hidden "presence" cours entry
        $presenceCours = Cours::firstOrCreate(
            [
                'id_emplois_du_temps' => $emploi->id,
                'titre'               => '__presence__',
            ],
            [
                'statut'     => 'faite',
                'created_by' => Auth::id(),
            ]
        );

        foreach ($request->input('presences', []) as $entry) {
            $stagiaireId = $entry['stagiaire_id'];
            $status      = $entry['status'];

            if ($status === 'present') {
                // Present = remove any existing absence/retard record
                AbsenceRetard::where('id_cours', $presenceCours->id)
                    ->where('id_user', $stagiaireId)
                    ->delete();
            } else {
                AbsenceRetard::updateOrCreate(
                    [
                        'id_cours' => $presenceCours->id,
                        'id_user'  => $stagiaireId,
                    ],
                    [
                        'type'       => $status,
                        'date_event' => $emploi->date_debut,
                        'duree'      => round($emploi->date_debut->diffInMinutes($emploi->date_fin) / 60, 1), // ← was null
                        'justifie'   => false,
                    ]
                );
            }
        }

        // ── Envoyer les emails d'absence ───────────────────────────
        $enregistreePar = Auth::user();
        $sentCount = 0;
        $failedEmails = [];

        foreach ($request->input('presences', []) as $entry) {
            if ($entry['status'] === 'absence') {
                $stagiaire = User::find($entry['stagiaire_id']);
                if ($stagiaire && $stagiaire->email) {
                    // Vérifier si l'email est valide
                    if (!filter_var($stagiaire->email, FILTER_VALIDATE_EMAIL)) {
                        $failedEmails[] = $stagiaire->email;
                        continue;
                    }
                    
                    try {
                        Mail::to($stagiaire->email)->queue(
                            new AbsenceMail(
                                stagiaire:    $stagiaire,
                                emploi:       $emploi,
                                enregistreePar: $enregistreePar,
                                justified:    false,
                                justification: null,
                            )
                        );
                        $sentCount++;
                    } catch (\Exception $e) {
                        $failedEmails[] = $stagiaire->email;
                        \Log::error("Erreur envoi email d'absence à {$stagiaire->email}: " . $e->getMessage());
                    }
                }
            }
        }

        $successMessage = 'Liste de présence enregistrée.';
        if ($sentCount > 0) {
            $successMessage .= " Notification d'absence envoyée à {$sentCount} stagiaire(s).";
        }
        if (!empty($failedEmails)) {
            $successMessage .= " (" . count($failedEmails) . " email(s) invalide(s) ignoré(s))";
        }

        return redirect()->route('seances.show', $emploi)
            ->with('success', $successMessage);
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

        // ── Notifier les stagiaires du groupe ───────────────────────
        $emploi->load(['module', 'groupe', 'gestionnaire']);

        $otherDocs = Cours::where('id_emplois_du_temps', $emploi->id)
            ->where('titre', '!=', '__presence__')
            ->where('id', '!=', $cours->id)
            ->latest()
            ->take(3)
            ->get();

        $stagiaires = User::where('id_groupe', $emploi->id_groupe)
            ->where('role', 'stagiaire')
            ->whereNotNull('email')
            ->get();

        $sharedBy = Auth::user();

        $sentCount = 0;
        $failedEmails = [];

        foreach ($stagiaires as $stagiaire) {
            // Vérifier si l'email est valide
            if (!filter_var($stagiaire->email, FILTER_VALIDATE_EMAIL)) {
                $failedEmails[] = $stagiaire->email;
                continue;
            }
            
            try {
                Mail::to($stagiaire->email)->queue(
                    new NouveauDocumentMail(
                        recipient: $stagiaire,
                        document:  $cours,
                        emploi:    $emploi,
                        sharedBy:  $sharedBy,
                        otherDocs: $otherDocs,
                    )
                );
                $sentCount++;
            } catch (\Exception $e) {
                $failedEmails[] = $stagiaire->email;
                \Log::error("Erreur envoi email à {$stagiaire->email}: " . $e->getMessage());
            }
        }

        $message = "Ressource « {$request->titre} » ajoutée.";
        if ($sentCount > 0) {
            $message .= " Notification envoyée à {$sentCount} stagiaire(s).";
        }
        if (!empty($failedEmails)) {
            $message .= " (" . count($failedEmails) . " email(s) invalide(s) ignoré(s))";
        }

        return redirect()->route('seances.show', $emploi)
            ->with('success', $message);
    }

    // ── DELETE CLASSROOM RESOURCE ─────────────────────────────
    public function deleteRessource(EmploiDuTemps $emploi, Cours $cours): RedirectResponse
    {
        if (!in_array(Auth::user()->role, ['admin', 'formateur'])
            && !Auth::user()->can('emploi-view-all-groups')) {
            abort(403);
        }

        // Ensure the cours belongs to this emploi and is not the hidden presence entry
        if ($cours->id_emplois_du_temps !== $emploi->id || $cours->titre === '__presence__') {
            abort(404);
        }

        // Delete stored files
        if ($cours->fichier) {
            foreach ($cours->fichier as $path) {
                Storage::disk('public')->delete($path);
            }
        }

        $titre = $cours->titre;
        $cours->delete();

        return redirect()->route('seances.show', $emploi)
            ->with('success', 'Ressource « ' . $titre . ' » supprimée.');
    }
}