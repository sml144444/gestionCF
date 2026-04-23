<?php

namespace App\Http\Controllers;

use App\Models\AbsenceRetard;
use App\Models\User;
use App\Models\Groupe;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class AbsenceController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware(function ($request, $next) {
            if (!Auth::user()->can('absence-view')) {
                abort(403);
            }
            return $next($request);
        });
    }

    // ── INDEX ─────────────────────────────────────────────────
    public function index(Request $request)
    {
        $user       = Auth::user();
        $canViewAll = $user->can('absence-view-all')
                   || in_array($user->role, ['admin', 'gestionnaire', 'formateur']);

        $query = AbsenceRetard::with([
            'stagiaire',
            'cours.emploiDuTemps.module',
            'cours.emploiDuTemps.groupe',
            'cours.emploiDuTemps.salle',
            'cours.emploiDuTemps.gestionnaire',
        ])
        ->where('type', 'absence');

        if (!$canViewAll) {
            $query->where('id_user', $user->id);
        }

        if ($request->filled('justifie') && in_array($request->justifie, ['0', '1', 'pending'])) {
            if ($request->justifie === 'pending') {
                // File uploaded but not yet validated
                $query->where('justifie', false)->whereNotNull('file_justification');
            } else {
                $query->where('justifie', (bool) $request->justifie);
            }
        }

        if ($canViewAll) {
            if ($request->filled('stagiaire_id')) {
                $query->where('id_user', $request->stagiaire_id);
            }
            if ($request->filled('groupe_id')) {
                $query->whereHas('stagiaire', fn($q) =>
                    $q->where('id_groupe', $request->groupe_id)
                );
            }
        }

        if ($request->filled('session_part')
            && in_array($request->session_part, ['s1', 's2', 's3', 's4'])) {
            $query->where('session_part', $request->session_part);
        }

        $query->orderByDesc('date_event');
        $absences = $query->paginate(20)->withQueryString();

        // ── Stats ──────────────────────────────────────────────
        $statsQuery = AbsenceRetard::where('type', 'absence');
        if (!$canViewAll) {
            $statsQuery->where('id_user', $user->id);
        }
        if ($canViewAll && $request->filled('stagiaire_id')) {
            $statsQuery->where('id_user', $request->stagiaire_id);
        }
        if ($canViewAll && $request->filled('groupe_id')) {
            $statsQuery->whereHas('stagiaire', fn($q) =>
                $q->where('id_groupe', $request->groupe_id)
            );
        }

        $stats = [
            'total'            => (clone $statsQuery)->count(),
            'justifies'        => (clone $statsQuery)->where('justifie', true)->count(),
            'injustifies'      => (clone $statsQuery)->where('justifie', false)->whereNull('file_justification')->count(),
            'en_attente'       => (clone $statsQuery)->where('justifie', false)->whereNotNull('file_justification')->count(),
            'total_heures_abs' => round((clone $statsQuery)->sum('duree'), 1),
            's1' => (clone $statsQuery)->where('session_part', 's1')->count(),
            's2' => (clone $statsQuery)->where('session_part', 's2')->count(),
            's3' => (clone $statsQuery)->where('session_part', 's3')->count(),
            's4' => (clone $statsQuery)->where('session_part', 's4')->count(),
        ];

        // ── Dropdowns ─────────────────────────────────────────
        $groupes    = $canViewAll ? Groupe::orderBy('name')->get() : collect();
        $stagiaires = collect();
        if ($canViewAll) {
            $gId        = $request->filled('groupe_id') ? $request->groupe_id : null;
            $stagiaires = User::where('role', 'stagiaire')
                ->when($gId, fn($q) => $q->where('id_groupe', $gId))
                ->orderBy('name')
                ->get();
        }

        $filterStagiaire = ($canViewAll && $request->filled('stagiaire_id'))
            ? User::find($request->stagiaire_id)
            : ($canViewAll ? null : $user);

        return view('absences.index', compact(
            'absences', 'stats', 'canViewAll',
            'groupes', 'stagiaires', 'filterStagiaire'
        ));
    }

    // ── TOGGLE JUSTIFICATION (admin/gestionnaire) ─────────────
    public function toggleJustification(Request $request, AbsenceRetard $absence)
    {
        if (!Auth::user()->can('absence-justify')) {
            abort(403);
        }

        $newStatus = !$absence->justifie;
        $absence->update(['justifie' => $newStatus]);

        return back()->with('success',
            'Absence marquée comme ' . ($newStatus ? 'justifiée' : 'non justifiée') . '.'
        );
    }

    // ── ACCEPT JUSTIFICATION (admin/gestionnaire) ─────────────
    // Validates the file the stagiaire uploaded → justifie = true
    public function acceptJustification(AbsenceRetard $absence)
    {
        if (!Auth::user()->can('absence-justify')) {
            abort(403);
        }

        $absence->update(['justifie' => true]);

        return back()->with('success', 'Justificatif accepté — absence marquée comme justifiée.');
    }

    // ── REJECT JUSTIFICATION (admin/gestionnaire) ─────────────
    // Deletes the file and keeps justifie = false so stagiaire can re-upload
    public function rejectJustification(AbsenceRetard $absence)
    {
        if (!Auth::user()->can('absence-justify')) {
            abort(403);
        }

        if ($absence->file_justification) {
            Storage::disk('public')->delete($absence->file_justification);
            $absence->update([
                'file_justification' => null,
                'justifie'           => false,
            ]);
        }

        return back()->with('success', 'Justificatif rejeté. Le stagiaire peut en soumettre un nouveau.');
    }

    // ── STAGIAIRE UPLOAD (self-service, pending validation) ───
    // The stagiaire uploads their own justification file.
    // It is NOT automatically validated — admin must accept/reject.
    public function stagiaireUploadFichier(Request $request, AbsenceRetard $absence)
    {
        // Only the owner of this absence record may upload
        if (Auth::id() !== $absence->id_user) {
            abort(403);
        }

        // Must not already be validated
        if ($absence->justifie) {
            return back()->with('error', 'Cette absence est déjà justifiée.');
        }

        $request->validate([
            'file_justification' => 'required|file|max:10240|mimes:pdf,doc,docx,jpg,jpeg,png',
        ]);

        // Replace any existing (previously rejected) file
        if ($absence->file_justification) {
            Storage::disk('public')->delete($absence->file_justification);
        }

        $path = $request->file('file_justification')->store('justifications', 'public');

        $absence->update([
            'file_justification' => $path,
            'justifie'           => false, // stays false until admin validates
        ]);

        return back()->with('success', 'Justificatif envoyé avec succès. En attente de validation par l\'administration.');
    }

    // ── STAGIAIRE DELETE OWN FILE (only while still pending) ──
    public function stagiaireDeleteFichier(AbsenceRetard $absence)
    {
        if (Auth::id() !== $absence->id_user) {
            abort(403);
        }

        // Cannot delete if already validated
        if ($absence->justifie) {
            return back()->with('error', 'Vous ne pouvez pas supprimer un justificatif déjà accepté.');
        }

        if ($absence->file_justification) {
            Storage::disk('public')->delete($absence->file_justification);
            $absence->update(['file_justification' => null]);
        }

        return back()->with('success', 'Fichier supprimé.');
    }

    // ── ADMIN UPLOAD FICHIER (direct validation) ──────────────
    // Admin/gestionnaire uploads a file AND directly validates.
    public function uploadFichier(Request $request, AbsenceRetard $absence)
    {
        if (!Auth::user()->can('absence-justify')) {
            abort(403);
        }

        $request->validate([
            'file_justification' => 'required|file|max:10240|mimes:pdf,doc,docx,jpg,jpeg,png',
        ]);

        if ($absence->file_justification) {
            Storage::disk('public')->delete($absence->file_justification);
        }

        $path = $request->file('file_justification')->store('justifications', 'public');

        $absence->update([
            'file_justification' => $path,
            'justifie'           => true, // admin upload is immediately validated
        ]);

        return back()->with('success', 'Fichier de justification uploadé et validé.');
    }

    // ── ADMIN DELETE FICHIER ───────────────────────────────────
    public function deleteFichier(AbsenceRetard $absence)
    {
        if (!Auth::user()->can('absence-justify')) {
            abort(403);
        }

        if ($absence->file_justification) {
            Storage::disk('public')->delete($absence->file_justification);
            $absence->update([
                'file_justification' => null,
                'justifie'           => false,
            ]);
        }

        return back()->with('success', 'Fichier supprimé.');
    }
}