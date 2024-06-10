<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Environment extends Model
{
    const ERROR_CONNECTION_FAILED = 'Failed to connect to API';

    protected $fillable = [
        'name',
        'description',
        'vm_id',
        'node_id',
        'user_id',
        'cores',
        'memory',
        'ip',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function node(): BelongsTo
    {
        return $this->belongsTo(Node::class);
    }
}
