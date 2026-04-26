<?php
// app/Events/ReclamationAssigned.php

namespace App\Events;

use App\Models\Reclamation;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ReclamationAssigned implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(public Reclamation $reclamation)
    {}

    public function broadcastOn(): array
    {
        return [
            // Channel dyal l-assigné (formateur/gestionnaire)
            new PrivateChannel('user.' . $this->reclamation->assigned_to),
        ];
    }

    public function broadcastAs(): string
    {
        return 'ReclamationAssigned';
    }

    public function broadcastWith(): array
    {
        return [
            'reclamation_id' => $this->reclamation->id,
            'type'           => $this->reclamation->type,
            'description'    => mb_substr($this->reclamation->description, 0, 80) . '...',
            'stagiaire'      => $this->reclamation->stagiaire?->name,
            'url'            => route('reclamations.show', $this->reclamation),
        ];
    }
}