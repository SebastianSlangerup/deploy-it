<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Server extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $fillable = [
        'instance_id',
        'configuration_id',
    ];

    protected $with = ['configuration'];

    /** @return array<int, string> */
    public function uniqueIds(): array
    {
        return ['id'];
    }

    public function configuration(): BelongsTo
    {
        return $this->belongsTo(Configuration::class);
    }

    public function instance(): MorphOne
    {
        return $this->morphOne(Instance::class, 'instanceable')->chaperone();
    }

    public function containers(): HasMany
    {
        return $this->hasMany(Container::class);
    }
}
