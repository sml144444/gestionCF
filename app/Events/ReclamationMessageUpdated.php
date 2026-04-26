<?php
// app/Events/ReclamationMessageUpdated.php

namespace App\Events;

use App\Models\ReclamationMessage;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ReclamationMessageUpdated implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(public ReclamationMessage $message) {}

    public function broadcastOn(): array
    {
        return [new PrivateChannel('reclamation.' . $this->message->reclamation_id)];
    }

    public function broadcastAs(): string { return 'ReclamationMessageUpdated'; }

    public function broadcastWith(): array
    {
        return [
            'message_id' => $this->message->id,
            'message'    => $this->message->message,
            'edited_at'  => $this->message->edited_at?->format('H:i'),
        ];
    }
}