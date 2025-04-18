<?php

namespace App\Data;

use App\Data\Casts\InstanceStatusStateCast;
use App\Enums\InstanceTypeEnum;
use Illuminate\Support\Carbon;
use Spatie\LaravelData\Attributes\WithCast;
use Spatie\LaravelData\Data;

class InstanceData extends Data
{
    public function __construct(
        public string $id,
        public string $name,
        public string $tecnical_name,
        public string $node,
        public ?int $vm_id = null,
        public ?string $vm_username = null,
        public ?string $vm_password = null,
        public string $description,
        public UserData $created_by,
        public bool $is_ready,
        public InstanceTypeEnum $type,
        #[WithCast(InstanceStatusStateCast::class)]
        public InstanceStatusData $status,
        public ?Carbon $started_at,
        public ?Carbon $stopped_at,
        public ?Carbon $suspended_at,
        public Carbon $created_at,
        public Carbon $updated_at,
    ) {}
}
