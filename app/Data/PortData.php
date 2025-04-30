<?php

namespace App\Data;

use Spatie\LaravelData\Data;

class PortData extends Data
{
    public function __construct(
        public string $id,
        public int $port,
        public bool $is_active,
        public ContainerData $container_id,
        public ServerData $allocated_on,
    ) {}
}
