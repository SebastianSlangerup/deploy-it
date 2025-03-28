<?php

namespace App\States\InstanceStatusState\Transitions;

use App\Models\Instance;
use App\States\InstanceStatusState\Started;
use Illuminate\Support\Facades\Log;
use Spatie\ModelStates\Transition;

class ToStarted extends Transition
{
    private Instance $instance;
    public function __construct(Instance $instance)
    {
        $this->instance = $instance;
    }

    public function handle(): Instance
    {
        $this->instance->started_at = now();
        $this->instance->stopped_at = null;
        $this->instance->suspended_at = null;

        $this->instance->status = new Started($this->instance);
        $this->instance->save();

        Log::info("Instance [ID: {$this->instance->id}] started");

        return $this->instance;
    }
}
