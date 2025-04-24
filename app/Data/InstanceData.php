<?php

namespace App\Data;

use App\Data\Casts\InstanceStatusStateCast;
use App\Enums\InstanceTypeEnum;
use App\Models\Configuration;
use App\Models\Server;
use Illuminate\Support\Carbon;
use Spatie\LaravelData\Attributes\WithCast;
use Spatie\LaravelData\Data;

class InstanceData extends Data
{
    public function __construct(
        public string $id,
        public string $name,
        public string $hostname,
        public string $node,
        public ?int $vm_id,
        public ?string $vm_username,
        public ?string $vm_password,
        public string $description,
        public UserData $created_by,
        public bool $is_ready,
        public Server|Configuration $instanceable,
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
