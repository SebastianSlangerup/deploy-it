<?php

use Spatie\LaravelData\Data;

beforeEach(function () {
    $this->arch()->ignore([
        'App\Data\Casts',
    ]);
});

arch('data extends base data')
    ->expect('App\Data')
    ->toExtend(Data::class);

arch('data has a constructor')
    ->expect('App\Data')
    ->toHaveConstructor();
