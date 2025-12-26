<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SyncronTelegramEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $userId;
    public $success;
    public $message;

    /**
     * Create a new event instance.
     */
    public function __construct($userId, $success = true, $message = null)
    {
        $this->userId = $userId;
        $this->success = $success;
        $this->message = $message;
        
        Log::info('SyncronTelegramEvent dibuat', [
            'userId' => $this->userId,
            'success' => $this->success,
            'message' => $this->message,
        ]);
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('user.' . $this->userId),
        ];
    }

    public function broadcastAs(): string
    {
        return 'syncron-telegram';
    }

    public function broadcastWith(): array
    {
        $data = [
            'success' => $this->success,
            'message' => $this->message ?? ($this->success ? 'Sinkronisasi berhasil' : 'Sinkronisasi gagal'),
        ];

        Log::info('SyncronTelegramEvent broadcastWith data', $data);

        return $data;
    }
}
