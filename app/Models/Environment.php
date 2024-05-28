<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Environment extends Model
{
    const ERROR_CONNECTION_FAILED = "Failed to connect to API";

    protected $fillable = [
        'name',
        'vm_id',
        'node',
        'user_id',
        'cores',
        'memory',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
