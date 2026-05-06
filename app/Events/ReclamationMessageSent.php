<?php

namespace App\Events;

use App\Models\ReclamationMessage;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ReclamationMessageSent implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(public ReclamationMessage $reclamationMessage)
    {}

    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('reclamation.' . $this->reclamationMessage->reclamation_id),
        ];
    }

    public function broadcastAs(): string
    {
        return 'ReclamationMessageSent';
    }

public function broadcastWith(): array
{
    $msg = $this->reclamationMessage->load('sender');

    return [
        'id'              => $msg->id,
        'message'         => $msg->message,
        'created_at'      => $msg->created_at->format('H:i'),
        'attachment_path' => $msg->attachment_path
                                ? asset('storage/' . $msg->attachment_path)
                                : null,
        'attachment_name' => $msg->attachment_name,
        'attachment_mime' => $msg->attachment_mime,
        'sender'          => [
            'id'   => $msg->sender->id,
            'name' => $msg->sender->name,
            'role' => $msg->sender->role,
        ],
    ];
}
}