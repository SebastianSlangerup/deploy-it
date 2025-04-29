<?php

namespace App\Enums;

enum InstanceActionsEnum: string
{
    case Start = 'start';
    case Stop = 'stop';
    case Shutdown = 'shutdown';
    case Reset = 'reset';
    case Reboot = 'reboot';
    case Suspend = 'suspend';
    case Resume = 'resume';
}
