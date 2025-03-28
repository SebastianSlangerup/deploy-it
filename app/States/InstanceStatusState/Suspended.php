<?php

namespace App\States\InstanceStatusState;

use App\Enums\InstanceStatusEnum;

class Suspended extends InstanceStatus
{
    public function color(): string
    {
        return 'warning';
    }

    public function label(): string
    {
        return 'Suspended';
    }

    public function status(): InstanceStatusEnum
    {
        return InstanceStatusEnum::Suspended;
    }
}
