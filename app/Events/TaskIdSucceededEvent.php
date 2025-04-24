<?php

namespace App\Events;

use App\Models\Instance;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class TaskIdSucceededEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public Instance $instance,
    ) {}

    public function broadcastOn(): PrivateChannel
    {
        return new PrivateChannel('instances.'.$this->instance->id);
    }
}
