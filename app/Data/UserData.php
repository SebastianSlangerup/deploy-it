<?php

namespace App\Data;

use App\Enums\RolesEnum;
use App\Models\Instance;
use Illuminate\Support\Collection;
use Spatie\LaravelData\Data;

class UserData extends Data
{
    public function __construct(
        public string $id,
        public string $name,
        public string $email,
        /** @var Collection<int, Instance> $instances */
        public ?Collection $instances,
        public RolesEnum $role,
    ) {}
}
