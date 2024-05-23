<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Environment extends Model
{
    protected $fillable = [
        'name',
        'vm_id',
        'user_id',
        'cores',
        'memory',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
