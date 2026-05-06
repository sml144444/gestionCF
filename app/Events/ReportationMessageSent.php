<?php
namespace App\Events;

use App\Models\ReportationMessage;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;

class ReportationMessageSent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets;

    public function __construct(public ReportationMessage $reportationMessage) {}

    public function broadcastOn(): Channel
    {
        return new Channel('reportation.' . $this->reportationMessage->reportation_id);
    }

    public function broadcastAs(): string
    {
        return 'message.sent';
    }

public function broadcastWith(): array
{
    return [
        'id'              => $this->reportationMessage->id,
        'message'         => $this->reportationMessage->message,
        'user_id'         => $this->reportationMessage->user_id,
        'user_name'       => $this->reportationMessage->user->name,
        'created_at'      => $this->reportationMessage->created_at->format('H:i'),
        'attachment_name' => $this->reportationMessage->attachment_name,
        'attachment_type' => $this->reportationMessage->attachment_type,
        'attachment_url'  => $this->reportationMessage->attachment_path
            ? route('reportations.attachment', $this->reportationMessage->id)
            : null,
    ];
}
}