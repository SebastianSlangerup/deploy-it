<?php

namespace App\States\InstanceStatusState;

use App\Enums\InstanceStatusEnum;

class Started extends InstanceStatus
{
    public function color(): string
    {
        return 'success';
    }

    public function label(): string
    {
        return 'Started';
    }

    public function status(): InstanceStatusEnum
    {
        return InstanceStatusEnum::Started;
    }
}
