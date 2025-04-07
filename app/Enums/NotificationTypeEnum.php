<?php

namespace App\Enums;

enum NotificationTypeEnum: string
{
    case Default = 'default';
    case Success = 'success';
    case Info = 'info';
    case Warning = 'warning';
    case Error = 'error';
}
