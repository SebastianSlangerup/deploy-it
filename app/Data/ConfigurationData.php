<?php

namespace App\Data;

use Illuminate\Support\Carbon;
use Spatie\LaravelData\Data;

class ConfigurationData extends Data
{
    public function __construct(
        public string $id,
        public string $name,
        public string $description,

        public int $cores,
        public int $memory,
        public int $disk_space,
        public int $proxmox_configuration_id,

        public Carbon $created_at,
        public Carbon $updated_at,
    ) {}
}
