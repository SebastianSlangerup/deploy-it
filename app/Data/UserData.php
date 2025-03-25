<?php

namespace App\Data;

use App\Enums\RolesEnum;
use Spatie\LaravelData\Data;

class UserData extends Data
{
    public function __construct(
        public string $id,
        public string $name,
        public string $email,
        public RolesEnum $role,
    ) {}
}
