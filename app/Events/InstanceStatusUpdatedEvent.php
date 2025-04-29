<?php

namespace App\Events;

use App\Data\InstanceData;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class InstanceStatusUpdatedEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public int $nextStep,
        public InstanceData $instance,
    ) {}

    public function broadcastOn(): PrivateChannel
    {
        return new PrivateChannel('instances.'.$this->instance->id);
    }
}
