<?php

namespace App\Data;

use App\Enums\NotificationTypeEnum;
use Spatie\LaravelData\Data;

class NotificationData extends Data
{
    public function __construct(
        public string $title,
        public string $description,
        public NotificationTypeEnum $notificationType,
    ) {}
}
