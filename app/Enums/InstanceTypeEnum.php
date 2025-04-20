<?php

namespace App\Enums;

use App\Models\Container;
use App\Models\Server;

enum InstanceTypeEnum: string
{
    case Server = 'server';
    case Container = 'container';

    public static function getType(string $className): self
    {
        return match ($className) {
            Server::class => self::Server,
            Container::class => self::Container,
        };
    }
}
