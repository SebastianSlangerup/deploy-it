<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class PortNumber extends Model
{
    use HasUuids, SoftDeletes;

    protected $fillable = [
        'port',
        'is_active',
        'container_id',
        'allocated_on',
    ];

    public function container(): BelongsTo
    {
        return $this->belongsTo(Container::class);
    }

    public function allocatedOn(): BelongsTo
    {
        return $this->belongsTo(Server::class, 'allocated_on');
    }

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }
}
