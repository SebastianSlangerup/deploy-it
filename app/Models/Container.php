<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Container extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $fillable = [
        'server_id',
        'docker_image',
    ];

    /** @return array<int, string> */
    public function uniqueIds(): array
    {
        return ['id'];
    }

    public function instances(): MorphOne
    {
        return $this->morphOne(Instance::class, 'instanceable')->chaperone();
    }

    public function server(): BelongsTo
    {
        return $this->belongsTo(Server::class);
    }
}
