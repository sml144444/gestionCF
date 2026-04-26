<?php
// app/Events/ReclamationCreated.php

namespace App\Events;

use App\Models\Reclamation;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\Channel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ReclamationCreated implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(public Reclamation $reclamation)
    {}

    public function broadcastOn(): array
    {
        return [
            // Channel pour tous les admins/gestionnaires
            new Channel('reclamations.admin'),
        ];
    }

    public function broadcastAs(): string
    {
        return 'ReclamationCreated';
    }

    public function broadcastWith(): array
    {
        $tc = Reclamation::TYPES[$this->reclamation->type];

        return [
            'id'          => $this->reclamation->id,
            'type_icon'   => $tc['icon'],
            'type_label'  => $tc['label'],
            'description' => mb_substr($this->reclamation->description, 0, 80) . '...',
            'stagiaire'   => $this->reclamation->stagiaire?->name,
            'url'         => route('reclamations.show', $this->reclamation),
        ];
    }
}