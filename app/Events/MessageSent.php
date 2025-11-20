<?php

namespace App\Events;

use App\Models\Chat;
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

    public $chat;
    public $sender;
    public $receiver;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(Chat $chat, $sender, $receiver)
    {
        $this->chat = $chat;
        $this->sender = $sender;
        $this->receiver = $receiver;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        // Broadcast to both sender and receiver's private channels
        return [
            new PrivateChannel('message-thread.' . $this->chat->message_thrade),
            new PrivateChannel('user.' . $this->chat->sender_id),
            new PrivateChannel('user.' . $this->chat->reciver_id),
        ];
    }

    /**
     * The event's broadcast name.
     *
     * @return string
     */
    public function broadcastAs()
    {
        return 'message.sent';
    }

    /**
     * Get the data to broadcast.
     *
     * @return array
     */
    public function broadcastWith()
    {
        return [
            'id' => $this->chat->id,
            'message_thrade' => $this->chat->message_thrade,
            'sender_id' => $this->chat->sender_id,
            'reciver_id' => $this->chat->reciver_id,
            'message' => $this->chat->message,
            'read_status' => $this->chat->read_status,
            'created_at' => $this->chat->created_at->toDateTimeString(),
            'sender' => [
                'id' => $this->sender->id,
                'name' => $this->sender->name,
                'user_information' => $this->sender->user_information,
            ],
        ];
    }
}

