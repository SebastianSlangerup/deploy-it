<?php

use App\Jobs\FetchConfigurationsJob;
use App\Jobs\UpdateInstancesInformationJob;
use Illuminate\Support\Facades\Schedule;

Schedule::job(new FetchConfigurationsJob)->daily();

collect(['node1', 'node2', 'node3'])->each(function ($node) {
    Schedule::job(new UpdateInstancesInformationJob($node))->everyThirtySeconds()->withoutOverlapping();
});
