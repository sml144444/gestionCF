<?php
namespace App\Events;

use App\Models\Reportation;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;

class ReportationCreated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets;

    public function __construct(public Reportation $reportation) {}

    public function broadcastOn(): Channel
    {
        return new Channel('reportations');
    }

    public function broadcastWith(): array
    {
        $r = $this->reportation->load([
            'emploiDuTemps.groupe.filiere',
            'emploiDuTemps.module',
            'emploiDuTemps.salle',
            'formateur',
        ]);

        return [
            'id'            => $r->id,
            'formateur'     => $r->formateur?->name ?? 'Inconnu',
            'formateur_id'  => $r->id_user,
            'module'        => $r->emploiDuTemps?->module?->name ?? '—',
            'groupe'        => $r->emploiDuTemps?->groupe?->name ?? '—',
            'filiere'       => $r->emploiDuTemps?->groupe?->filiere?->name ?? '—',
            'date_debut'    => $r->emploiDuTemps?->date_debut?->format('Y-m-d'),
            'heure_debut'   => $r->emploiDuTemps?->date_debut?->format('H:i'),
            'heure_fin'     => $r->emploiDuTemps?->date_fin?->format('H:i'),
            'raison'        => $r->raison,
            'status'        => $r->status,
            'created_at'    => $r->created_at->format('d M Y à H:i'),
        ];
    }
}