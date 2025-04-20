<?php

namespace App\Data;

use Illuminate\Support\Carbon;
use Spatie\LaravelData\Data;

class PackageData extends Data
{
    public function __construct(
        public string $id,
        public string $name,
        public string $command,
        public Carbon $created_at,
    ) {}
}
