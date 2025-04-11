<?php

namespace App\Events;

use App\Models\Chat;
use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Broadcasting\InteractsWithSockets;

class ChatSent implements ShouldBroadcast
{
    use InteractsWithSockets, SerializesModels;

    public Chat $chat;

    public function __construct(Chat $chat)
    {
        $this->chat = $chat->load('media');
    }

    public function broadcastOn(): Channel
    {
        return new PrivateChannel('chat.' . $this->chat->receiver_id);
    }

    public function broadcastWith(): array
    {
        return [
            'chat' => $this->chat->toArray(),
            'attachments' => $this->chat->getMedia('attachments')->map(function ($media) {
                return [
                    'url' => $media->getFullUrl(),
                    'name' => $media->name,
                    'type' => $media->mime_type,
                ];
            }),
        ];
    }

    public function broadcastAs(): string
    {
        return 'chat.sent';
    }
}
