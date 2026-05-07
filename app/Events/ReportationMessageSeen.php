<?php

namespace App\Events;

use App\Models\Reportation;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;

class ReportationMessageSeen implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets;

    public array $seenMessageIds;
    public int   $seenByUserId;

    public function __construct(Reportation $reportation, array $seenMessageIds, int $seenByUserId)
    {
        $this->seenMessageIds = $seenMessageIds;
        $this->seenByUserId   = $seenByUserId;
        $this->reportationId  = $reportation->id;
    }

    public int $reportationId;

    public function broadcastOn(): Channel
    {
        return new Channel('reportation.' . $this->reportationId);
    }

    public function broadcastAs(): string
    {
        return 'messages.seen';
    }

    public function broadcastWith(): array
    {
        return [
            'message_ids'    => $this->seenMessageIds,
            'seen_by_user_id' => $this->seenByUserId,
            'seen_at'        => now()->toISOString(),
        ];
    }
}