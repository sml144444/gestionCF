<?php

namespace App\Mail;

use App\Models\AbsenceRetard;
use App\Models\Cours;
use App\Models\EmploiDuTemps;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AbsenceMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public int   $totalAbsences;
    public float $totalHeuresAbsence;
    public int   $tauxPresence;
    public bool  $justified;
    public ?string $justification;

    public function __construct(
        public User           $stagiaire,
        public EmploiDuTemps  $emploi,
        public ?User          $enregistreePar = null,
        bool                  $justified      = false,
        ?string               $justification  = null,
    ) {
        $this->justified     = $justified;
        $this->justification = $justification;

        // ── Compute stats ─────────────────────────────────────
        $presenceCours = Cours::where('id_emplois_du_temps', $emploi->id)
            ->where('titre', '__presence__')
            ->first();

        // Total absences (all time, this stagiaire)
        $allAbsences = AbsenceRetard::where('id_user', $stagiaire->id)
            ->where('type', 'absence')
            ->count();

        // Total hours absent
        $absenceCoursIds = AbsenceRetard::where('id_user', $stagiaire->id)
            ->where('type', 'absence')
            ->pluck('id_cours');

        $totalMinutes = Cours::whereIn('id', $absenceCoursIds)
            ->with('emploi')
            ->get()
            ->sum(function ($cours) {
                $e = $cours->emploi;
                return $e ? $e->date_debut->diffInMinutes($e->date_fin) : 150;
            });

        // Presence rate for the groupe
        $totalSeances = EmploiDuTemps::where('id_groupe', $stagiaire->id_groupe)
            ->where('statut', 'actif')
            ->where('date_debut', '<=', now())
            ->count();

        $this->totalAbsences      = $allAbsences;
        $this->totalHeuresAbsence = round($totalMinutes / 60, 1);
        $this->tauxPresence       = $totalSeances > 0
            ? (int) round((($totalSeances - $allAbsences) / $totalSeances) * 100)
            : 100;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: '⚠️ Absence enregistrée – ' . $this->emploi->module?->name,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.absence',
        );
    }
}