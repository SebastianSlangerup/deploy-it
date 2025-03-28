<?php

namespace App\Data;

use App\Enums\InstanceStatusEnum;
use Spatie\LaravelData\Data;

class InstanceStatusData extends Data
{
    public function __construct(
        public InstanceStatusEnum $status,
        public string $label,
        public string $color,
    ) {}
}
