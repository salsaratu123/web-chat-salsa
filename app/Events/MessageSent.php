<?php

namespace App\Events;

use App\Models\Message;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MessageSent implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $message;

    /**
     * Create a new event instance.
     */
    public function __construct(Message $message)
    {
        // Eager load sender so the frontend has the user name
        $this->message = $message->load('sender');
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, Channel>
     */
    public function broadcastOn(): array
    {
        if ($this->message->group_id) {
            return [
                new PresenceChannel('chat.group.' . $this->message->group_id),
            ];
        }

        return [
            new PrivateChannel('chat.private.' . $this->message->receiver_id),
            // Optional: also broadcast to sender so they see it across multiple tabs
            new PrivateChannel('chat.private.' . $this->message->sender_id),
        ];
    }

    public function broadcastAs()
    {
        return 'message.sent';
    }
}
