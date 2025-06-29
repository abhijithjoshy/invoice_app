<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class InvoiceMailProgress implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $progressKey;
    public $completed;
    public $total;

    /**
     * Create a new event instance.
     */
    public function __construct($progressKey, $completed, $total)
    {
        $this->progressKey = $progressKey;
        $this->completed = $completed;
        $this->total = $total;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new \Illuminate\Broadcasting\Channel('mail-progress.' . $this->progressKey),
        ];
    }

    public function broadcastAs()
    {
        return 'InvoiceMailProgress';
    }
}
