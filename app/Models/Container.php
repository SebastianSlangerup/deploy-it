<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Container extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $fillable = [
        'server_id',
        'docker_image',
    ];

    protected $with = ['server'];

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

    public function getNextAvailablePort(Server $server, int $min = 1000, int $max = 5000): int
    {
        $usedPorts = PortNumber::query()
            ->where('allocated_on', '=', $server)
            ->get();

        // Exclude the ports already in use
        return collect(range($min, $max))->diff($usedPorts)->first();
    }

    public function port(): HasOne
    {
        return $this->hasOne(PortNumber::class);
    }
}
