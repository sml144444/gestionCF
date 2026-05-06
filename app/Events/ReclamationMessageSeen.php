<?php

namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ReclamationMessageSeen implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public int $reclamationId,
        public array $messageIds,
        public string $seenByName,
        public string $seenByRole
    ) {}

    public function broadcastOn(): array
    {
        return [new PrivateChannel('reclamation.' . $this->reclamationId)];
    }

    public function broadcastAs(): string
    {
        return 'ReclamationMessageSeen';
    }

    public function broadcastWith(): array
    {
        return [
            'message_ids'   => $this->messageIds,
            'seen_by_name'  => $this->seenByName,
            'seen_by_role'  => $this->seenByRole,
            'initials'      => strtoupper(
                mb_substr($this->seenByName, 0, 1) .
                mb_substr(explode(' ', $this->seenByName)[1] ?? '', 0, 1)
            ),
        ];
    }
}