<?php

namespace App\Models;

use App\States\InstanceStatusState\InstanceStatus;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\ModelStates\HasStates;

class Instance extends Model
{
    use HasFactory,
        HasStates,
        HasUuids,
        SoftDeletes;

    protected $fillable = [
        'name',
        'description',
        'created_by',
    ];

    /** @return array<int, string> */
    public function casts(): array
    {
        return [
            'created_at' => 'datetime',
            'status' => InstanceStatus::class,
            'is_ready' => 'boolean',
        ];
    }

    /** @return array<int, string> */
    public function uniqueIds(): array
    {
        return ['id'];
    }

    public function instanceable(): MorphTo
    {
        return $this->morphTo();
    }

    public function created_by(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
