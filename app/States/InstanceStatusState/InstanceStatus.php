<?php

namespace App\States\InstanceStatusState;

use App\Data\InstanceStatusData;
use App\Enums\InstanceStatusEnum;
use App\Models\Instance;
use App\States\InstanceStatusState\Transitions\ToStarted;
use App\States\InstanceStatusState\Transitions\ToStopped;
use App\States\InstanceStatusState\Transitions\ToSuspended;
use Spatie\ModelStates\State;
use Spatie\ModelStates\StateConfig;

/** @extends State<Instance> */
abstract class InstanceStatus extends State
{
    abstract public function color(): string;

    abstract public function label(): string;

    abstract public function status(): InstanceStatusEnum;

    public static function config(): StateConfig
    {
        return parent::config()
            ->default(Stopped::class)
            ->allowTransition(Stopped::class, Started::class, ToStarted::class)
            ->allowTransition(Suspended::class, Started::class, ToStarted::class)
            ->allowTransition(Started::class, Stopped::class, ToStopped::class)
            ->allowTransition(Suspended::class, Stopped::class, ToStopped::class)
            ->allowTransition(Stopped::class, Suspended::class, ToSuspended::class)
            ->allowTransition(Started::class, Suspended::class, ToSuspended::class);

    }

    public function data(): InstanceStatusData
    {
        return InstanceStatusData::from([
            'status' => $this->status(),
            'label' => $this->label(),
            'color' => $this->color(),
        ]);
    }
}


