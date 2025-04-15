<?php

namespace App\Data;

use App\Data\Casts\InstanceStatusStateCast;
use Illuminate\Support\Carbon;
use Spatie\LaravelData\Attributes\WithCast;
use Spatie\LaravelData\Data;

class InstanceData extends Data
{
    public function __construct(
        public string $id,
        public string $name,
        public string $description,
        public UserData $created_by,
        public bool $is_ready,
        #[WithCast(InstanceStatusStateCast::class)]
        public InstanceStatusData $status,
        public ?Carbon $started_at,
        public ?Carbon $stopped_at,
        public ?Carbon $suspended_at,
        public Carbon $created_at,
        public Carbon $updated_at,
    ) {}
}
