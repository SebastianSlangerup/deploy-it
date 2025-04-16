<?php

namespace App\Enums;

use App\Models\Container;
use App\Models\Server;

enum InstanceTypeEnum: string
{
    case Server = 'server';
    case Container = 'container';

    public static function getType(string $model): self
    {
        return match ($model) {
            Server::class => self::Server,
            Container::class => self::Container,
        };
    }
}
