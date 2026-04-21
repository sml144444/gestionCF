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
        ->whereNotNull('type');

        if (!$canViewAll) {
            $query->where('id_user', $user->id);
        }

        if ($request->filled('type') && in_array($request->type, ['absence', 'retard'])) {
            $query->where('type', $request->type);
        }

        if ($request->filled('justifie') && in_array($request->justifie, ['0', '1'])) {
            $query->where('justifie', (bool) $request->justifie);
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

        $query->orderByDesc('date_event');
        $absences = $query->paginate(20)->withQueryString();

        // ── Stats ──────────────────────────────────────────────
        $statsQuery = AbsenceRetard::whereNotNull('type');
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
            'total'       => (clone $statsQuery)->count(),
            'absences'    => (clone $statsQuery)->where('type', 'absence')->count(),
            'retards'     => (clone $statsQuery)->where('type', 'retard')->count(),
            'justifies'   => (clone $statsQuery)->where('justifie', true)->count(),
            'injustifies' => (clone $statsQuery)->where('justifie', false)->count(),
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

    // ── TOGGLE JUSTIFICATION ──────────────────────────────────
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

    // ── UPLOAD FICHIER JUSTIFICATION ──────────────────────────
    public function uploadFichier(Request $request, AbsenceRetard $absence)
    {
        if (!Auth::user()->can('absence-justify')) {
            abort(403);
        }

        $request->validate([
            'file_justification' => 'required|file|max:10240|mimes:pdf,doc,docx,jpg,jpeg,png',
        ]);

        // Delete old file if exists
        if ($absence->file_justification) {
            Storage::disk('public')->delete($absence->file_justification);
        }

        $path = $request->file('file_justification')->store('justifications', 'public');

        $absence->update([
            'file_justification' => $path,
            'justifie'           => true,
        ]);

        return back()->with('success', 'Fichier de justification uploadé avec succès.');
    }

    // ── DELETE FICHIER JUSTIFICATION ──────────────────────────
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