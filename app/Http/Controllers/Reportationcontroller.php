<?php

namespace App\Http\Controllers;

use App\Models\EmploiDuTemps;
use App\Models\Reportation;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ReportationController extends Controller
{
    // ── ADMIN/GESTIONNAIRE — see ALL requests ──────────────
    public function index(Request $request)
    {
        if (! auth()->user()->hasPermissionTo('reportation-manage')) {
            abort(403);
        }

        $status = trim($request->input('status', 'en_attente'));
        $search = trim($request->input('search', ''));

        $reportations = Reportation::with([
                'emploiDuTemps.groupe.filiere',
                'emploiDuTemps.module',
                'emploiDuTemps.salle',
                'formateur',
                'validePar',
            ])
            ->when($status !== '', fn($q) => $q->where('status', $status))
            ->when($search !== '', fn($q) => $q->whereHas('formateur', fn($u) =>
                $u->where('name', 'like', "%{$search}%")
            ))
            ->latest()
            ->paginate(20)
            ->withQueryString();

        $counts = [
            'en_attente' => Reportation::where('status', 'en_attente')->count(),
            'valide'     => Reportation::where('status', 'valide')->count(),
            'refuse'     => Reportation::where('status', 'refuse')->count(),
        ];

        return view('reportations.index', compact('reportations', 'status', 'search', 'counts'));
    }

    // ── FORMATEUR — see ONLY their own requests ────────────
    public function myIndex(Request $request)
    {
        if (! auth()->user()->hasPermissionTo('reportation-create')) {
            abort(403);
        }

        $status = trim($request->input('status', ''));

        $reportations = Reportation::with([
                'emploiDuTemps.groupe.filiere',
                'emploiDuTemps.module',
                'emploiDuTemps.salle',
                'validePar',
            ])
            ->where('id_user', auth()->id())
            ->when($status !== '', fn($q) => $q->where('status', $status))
            ->latest()
            ->paginate(20)
            ->withQueryString();

        $counts = [
            'en_attente' => Reportation::where('id_user', auth()->id())->where('status', 'en_attente')->count(),
            'valide'     => Reportation::where('id_user', auth()->id())->where('status', 'valide')->count(),
            'refuse'     => Reportation::where('id_user', auth()->id())->where('status', 'refuse')->count(),
        ];

        return view('reportations.my', compact('reportations', 'status', 'counts'));
    }

    // ── FORMATEUR SUBMITS — reason only, NO date ───────────
    public function store(Request $request): RedirectResponse
    {
        if (! auth()->user()->hasPermissionTo('reportation-create')) {
            abort(403);
        }

        $data = $request->validate([
            'id_emplois_du_temps' => 'required|exists:emplois_du_temps,id',
            'raison'              => 'required|string|min:10|max:1000',
        ]);

        $emploi = EmploiDuTemps::findOrFail($data['id_emplois_du_temps']);

        // Formateur can only report their own sessions
        if ($emploi->id_user !== auth()->id()) {
            abort(403, 'Vous ne pouvez reporter que vos propres séances.');
        }

        // Prevent duplicate pending request for same session
        if (Reportation::where('id_emplois_du_temps', $emploi->id)->where('status', 'en_attente')->exists()) {
            return back()->with('error', 'Une demande de report est déjà en attente pour cette séance.');
        }

        Reportation::create([
            'id_emplois_du_temps' => $emploi->id,
            'id_user'             => auth()->id(),
            'raison'              => $data['raison'],
            // No date yet — admin will choose
            'nouvelle_date_debut' => null,
            'nouvelle_date_fin'   => null,
            'status'              => 'en_attente',
        ]);

        $week = Carbon::parse($emploi->date_debut)->toDateString();
        $year = $emploi->groupe->annee ?? 1;

        return redirect()
            ->route('emplois.index', ['week' => $week, 'year' => $year])
            ->with('success', 'Demande de report envoyée. L\'administration choisira la nouvelle date.');
    }

    // ── ADMIN ACCEPTS — picks new date ─────────────────────
    public function accept(Request $request, Reportation $reportation): RedirectResponse
    {
        if (! auth()->user()->hasPermissionTo('reportation-manage')) {
            abort(403);
        }

        if ($reportation->status !== 'en_attente') {
            return back()->with('error', 'Cette demande a déjà été traitée.');
        }

        $data = $request->validate([
            'nouvelle_date_debut' => 'required|date|after:now',
            'nouvelle_date_fin'   => 'required|date|after:nouvelle_date_debut',
        ]);

        $emploi      = $reportation->emploiDuTemps;
        $newDebut    = Carbon::parse($data['nouvelle_date_debut']);
        $newFin      = Carbon::parse($data['nouvelle_date_fin']);

        // Check for conflicts on the new slot
        $overlap = EmploiDuTemps::whereIn('statut', ['actif', 'brouillon'])
            ->where('id', '!=', $emploi->id)
            ->where('date_debut', '<', $newFin)
            ->where('date_fin',   '>', $newDebut)
            ->where(fn($q) =>
                $q->where('id_groupe', $emploi->id_groupe)
                  ->orWhere('id_user', $emploi->id_user)
                  ->orWhere(fn($q2) =>
                      $q2->where('id_salle', $emploi->id_salle)
                         ->whereNotNull('id_salle')
                  )
            )
            ->exists();

        if ($overlap) {
            return back()->with('error', 'Conflit détecté sur ce créneau (groupe, formateur ou salle déjà occupé). Choisissez une autre date.');
        }

        // Apply new dates
        $emploi->update([
            'date_debut' => $newDebut,
            'date_fin'   => $newFin,
            'jour'       => EmploiDuTempsController::DAYS[$newDebut->dayOfWeekIso] ?? null,
        ]);

        $reportation->update([
            'nouvelle_date_debut' => $newDebut,
            'nouvelle_date_fin'   => $newFin,
            'status'              => 'valide',
            'valide_by'           => auth()->id(),
        ]);

        return redirect()
            ->route('reportations.index', ['status' => 'valide'])
            ->with('success', 'Report accepté — séance déplacée au ' . $newDebut->translatedFormat('l d M Y') . ' à ' . $newDebut->format('H:i') . '.');
    }

    // ── REFUSE ─────────────────────────────────────────────
    public function refuse(Reportation $reportation): RedirectResponse
    {
        if (! auth()->user()->hasPermissionTo('reportation-manage')) {
            abort(403);
        }

        if ($reportation->status !== 'en_attente') {
            return back()->with('error', 'Cette demande a déjà été traitée.');
        }

        $reportation->update([
            'status'    => 'refuse',
            'valide_by' => auth()->id(),
        ]);

        return redirect()
            ->route('reportations.index', ['status' => 'refuse'])
            ->with('success', 'Demande refusée.');
    }

    // ── DELETE the original session ────────────────────────
    public function deleteSession(Reportation $reportation): RedirectResponse
    {
        if (! auth()->user()->hasPermissionTo('reportation-manage')) {
            abort(403);
        }

        $emploi = $reportation->emploiDuTemps;

        $reportation->update([
            'status'    => 'valide',
            'valide_by' => auth()->id(),
        ]);

        $emploi?->delete();

        return redirect()
            ->route('reportations.index')
            ->with('success', 'Séance supprimée suite à la demande de report.');
    }
}