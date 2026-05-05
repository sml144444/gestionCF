<?php
// app/Events/NotificationUpdated.php
// Fired when an existing unread notification is incremented
// instead of creating a new one.

namespace App\Events;

use App\Models\UserNotification;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class NotificationUpdated implements ShouldBroadcastNow
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
        return 'NotificationUpdated';
    }

    public function broadcastWith(): array
    {
        $cfg = UserNotification::TYPES[$this->notification->type]
             ?? UserNotification::TYPES['default'];

        return [
            'id'         => $this->notification->id,
            'type'       => $this->notification->type,
            'message'    => $this->notification->message,   // already updated with new count
            'url'        => $this->notification->url,
            'icon'       => $cfg['icon'],
            'color'      => $cfg['color'],
            'bg'         => $cfg['bg'],
            'count'      => $this->notification->count,
            'created_at' => $this->notification->updated_at->diffForHumans(),
            'data'       => $this->notification->data ?? [],
        ];
    }
}