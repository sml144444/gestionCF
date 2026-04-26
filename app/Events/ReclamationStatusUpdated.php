<?php

namespace App\Events;

use App\Models\Reclamation;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ReclamationStatusUpdated implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(public Reclamation $reclamation)
    {}

    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('reclamation.' . $this->reclamation->id),
        ];
    }

    public function broadcastAs(): string
    {
        return 'ReclamationStatusUpdated';
    }

    public function broadcastWith(): array
    {
        $sc = Reclamation::STATUSES[$this->reclamation->status];

        return [
            'status' => $this->reclamation->status,
            'label'  => $sc['label'],
            'icon'   => $sc['icon'],
            'bg'     => $sc['bg'],
            'color'  => $sc['color'],
            'border' => $sc['border'],
        ];
    }
}