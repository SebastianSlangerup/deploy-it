<?php

namespace App\States\InstanceStatusState;

use App\Enums\InstanceStatusEnum;

class Configuring extends InstanceStatus
{
    public function color(): string
    {
        return 'info';
    }

    public function label(): string
    {
        return 'Configuring';
    }

    public function status(): InstanceStatusEnum
    {
        return InstanceStatusEnum::Configuring;
    }
}
