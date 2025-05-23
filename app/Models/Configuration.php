<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Configuration extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $fillable = [
        'name',
        'description',
        'proxmox_configuration_id',
        'cores',
        'memory',
        'disk_space',
        'disk',
    ];

    /** @return array<int, string> */
    public function uniqueIds(): array
    {
        return ['id'];
    }

    public function servers(): HasMany
    {
        return $this->hasMany(Server::class);
    }
}
