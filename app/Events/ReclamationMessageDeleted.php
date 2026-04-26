<?php
// app/Events/ReclamationMessageDeleted.php

namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ReclamationMessageDeleted implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public int $reclamationId,
        public int $messageId
    ) {}

    public function broadcastOn(): array
    {
        return [new PrivateChannel('reclamation.' . $this->reclamationId)];
    }

    public function broadcastAs(): string { return 'ReclamationMessageDeleted'; }

    public function broadcastWith(): array
    {
        return ['message_id' => $this->messageId];
    }
}