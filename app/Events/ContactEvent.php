<?php

namespace App\Events;

use App\Models\User;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ContactEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $type;
    public $userId;
    public $isContact;

    public function __construct(string $type, int $userId, bool $isContact)
    {
        $this->type = $type;
        $this->userId = $userId;
        $this->isContact = $isContact;
    }

    public function broadcastOn(): array
    {
        return [
            new Channel('user.' . $this->userId),
        ];
    }

    public function broadcastAs(): string
    {
        return 'contact_status';
    }

    public function broadcastWith(): array
    {
        return [
            'type' => $this->type,
            'userId' => $this->userId,
            'isContact' => $this->isContact
        ];
    }
} 