<?php

namespace App\States\InstanceStatusState\Transitions;

use App\Models\Instance;
use App\States\InstanceStatusState\Stopped;
use Illuminate\Support\Facades\Log;
use Spatie\ModelStates\Transition;

class ToStopped extends Transition
{
    private Instance $instance;
    public function __construct(Instance $instance)
    {
        $this->instance = $instance;
    }

    public function handle(): Instance
    {
        $this->instance->started_at = null;
        $this->instance->stopped_at = now();
        $this->instance->suspended_at = null;

        $this->instance->status = new Stopped($this->instance);
        $this->instance->save();

        Log::info('{instance}: Instance stopped', [
            'instance' => "[ID: {$this->instance->id}]",
        ]);

        return $this->instance;
    }
}
