<?php
// app/Events/ReclamationDeleted.php

namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\Channel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ReclamationDeleted implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public int $reclamationId,
        public int $stagiaireId
    ) {}

    public function broadcastOn(): array
    {
        return [
            new Channel('reclamations.admin'),                    // admin/gestionnaire
            new \Illuminate\Broadcasting\PrivateChannel('user.' . $this->stagiaireId), // stagiaire
        ];
    }

    public function broadcastAs(): string
    {
        return 'ReclamationDeleted';
    }

    public function broadcastWith(): array
    {
        return [
            'reclamation_id' => $this->reclamationId,
        ];
    }
}