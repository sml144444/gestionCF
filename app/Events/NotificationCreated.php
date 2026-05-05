<?php
// app/Events/NotificationCreated.php
// CHANGE: broadcastWith() now includes the `data` field so the
// frontend can read e.data.reclamation_id for suppression logic.

namespace App\Events;

use App\Models\UserNotification;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class NotificationCreated implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(public UserNotification $notification)
    {}

    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('user.' . $this->notification->user_id),
        ];
    }

    public function broadcastAs(): string
    {
        return 'NotificationCreated';
    }

    public function broadcastWith(): array
    {
        $cfg = UserNotification::TYPES[$this->notification->type]
             ?? UserNotification::TYPES['default'];

        return [
            'id'         => $this->notification->id,
            'type'       => $this->notification->type,
            'message'    => $this->notification->message,
            'url'        => $this->notification->url,
            'icon'       => $cfg['icon'],
            'color'      => $cfg['color'],
            'bg'         => $cfg['bg'],
            'created_at' => $this->notification->created_at->diffForHumans(),
            'data'       => $this->notification->data ?? [],   // ← ADDED: includes reclamation_id
        ];
    }
}