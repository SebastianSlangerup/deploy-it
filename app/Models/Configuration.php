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
        'cores',
        'memory',
        'disk_space',
    ];

    /** @return array<int, string> */
    public function uniqueIds(): array
    {
        return ['id'];
    }

    public function instances(): HasMany
    {
        return $this->hasMany(Instance::class);
    }

}
