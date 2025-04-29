<?php

namespace App\Enums;

enum NotificationTypeEnum: string
{
    case Success = 'success';
    case Info = 'info';
    case Warning = 'warning';
    case Error = 'error';
}
