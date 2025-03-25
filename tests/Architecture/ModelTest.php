<?php

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

arch('models has uniqueIds')
    ->expect('App\Models')
    ->toHaveMethod('uniqueIds')
    ->ignoring('App\Models\Concerns');

arch('models extends base model')
    ->expect('App\Models')
    ->toExtend(Model::class)
    ->ignoring('App\Models\Concerns');

arch('models use HasUuids')
    ->expect('App\Models')
    ->toUse([HasUuids::class]);
