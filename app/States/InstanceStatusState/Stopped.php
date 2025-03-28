<?php

namespace App\States\InstanceStatusState;

use App\Enums\InstanceStatusEnum;

class Stopped extends InstanceStatus
{
    public function color(): string
    {
        return 'error';
    }

    public function label(): string
    {
        return 'Stopped';
    }

    public function status(): InstanceStatusEnum
    {
        return InstanceStatusEnum::Stopped;
    }
}
