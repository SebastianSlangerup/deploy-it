<?php

namespace App\Data;

use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Spatie\LaravelData\Data;

class ServerData extends Data
{
    public function __construct(
        public string $id,
        public ?string $ip,
        public ConfigurationData $configuration,
        /** @var Collection<int, ContainerData> $containers */
        public Collection $containers,
        public Carbon $created_at,
        public Carbon $updated_at,
    ) {}
}
