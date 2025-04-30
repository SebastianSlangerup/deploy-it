<?php

namespace App\Data;

use Spatie\LaravelData\Data;

class ContainerData extends Data
{
    public function __construct(
        public string $id,
        public ServerData $server,
        public string $docker_image,
        public PortData $port,
    ) {}
}
