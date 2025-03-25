<?php

use App\Http\Controllers\Controller;

beforeEach(function () {
    $this->arch()->ignore([
        Controller::class,
    ]);
});

arch('controllers are invokable classes in the App\Http\Controllers namespace and have the suffix Controller')
    ->expect('App\Http\Controllers')
    ->toBeClasses()
    ->toHaveSuffix('Controller');


