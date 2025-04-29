<?php

namespace App\Enums;

enum InstanceStatusEnum: string
{
    case Started = 'started';
    case Stopped = 'stopped';
    case Suspended = 'suspended';
    case Configuring = 'configuring';
}
