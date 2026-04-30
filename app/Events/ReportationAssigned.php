<?php
// app/Events/ReportationAssigned.php
namespace App\Events;

use App\Models\Reportation;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;

class ReportationAssigned implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets;

    public function __construct(public Reportation $reportation) {}

    public function broadcastOn(): Channel
    {
        // Channel scoped to the assigned gestionnaire
        return new Channel('gestionnaire.' . $this->reportation->assigned_to);
    }

    public function broadcastAs(): string
    {
        return 'reportation.assigned';
    }

    public function broadcastWith(): array
    {
        $rp     = $this->reportation->load(['formateur', 'emploiDuTemps.module', 'emploiDuTemps.groupe.filiere', 'emploiDuTemps.salle']);
        $emploi = $rp->emploiDuTemps;

        return [
            'id'          => $rp->id,
            'formateur'   => $rp->formateur?->name ?? 'Inconnu',
            'raison'      => $rp->raison,
            'status'      => $rp->status,
            'created_at'  => $rp->created_at->translatedFormat('l d M Y à H:i'),
            'module'      => $emploi?->module?->name ?? '—',
            'groupe'      => $emploi?->groupe?->name ?? '—',
            'filiere'     => $emploi?->groupe?->filiere?->name ?? '—',
            'date_debut'  => $emploi?->date_debut?->translatedFormat('l d M Y') ?? '—',
            'heure_debut' => $emploi?->date_debut?->format('H:i') ?? '—',
            'heure_fin'   => $emploi?->date_fin?->format('H:i') ?? '—',
            'salle'       => $emploi?->salle?->name ?? null,
        ];
    }
}