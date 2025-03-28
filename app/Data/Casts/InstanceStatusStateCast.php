<?php

namespace App\Data\Casts;

use App\Data\InstanceStatusData;
use Spatie\LaravelData\Casts\Cast;
use Spatie\LaravelData\Support\Creation\CreationContext;
use Spatie\LaravelData\Support\DataProperty;

class InstanceStatusStateCast implements Cast
{
    public function cast(
        DataProperty $property,
        mixed $value,
        array $properties,
        CreationContext $context): InstanceStatusData
    {
        return $value->data();
    }
}
