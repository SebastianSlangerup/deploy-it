<?php

namespace App\States\InstanceStatusState\Transitions;

use App\Models\Instance;
use App\States\InstanceStatusState\Suspended;
use Illuminate\Support\Facades\Log;
use Spatie\ModelStates\Transition;

class ToSuspended extends Transition
{
    private Instance $instance;

    public function __construct(Instance $instance)
    {
        $this->instance = $instance;
    }

    public function handle(): Instance
    {
        $this->instance->started_at = null;
        $this->instance->stopped_at = null;
        $this->instance->suspended_at = now();

        $this->instance->status = new Suspended($this->instance);
        $this->instance->save();

        Log::info('{instance}: Instance suspended', [
            'instance' => "[ID: {$this->instance->id}]",
        ]);

        return $this->instance;
    }
}
