<?php

use App\Models\Instance;
use App\Models\User;
use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('instances.{instanceId}', function (User $user, string $instanceId) {
    return $user->id === Instance::findOrFail($instanceId)->created_by;
});

Broadcast::channel('notifications.{userId}', function (User $user, string $userId) {
    return $user->id === $userId;
});
