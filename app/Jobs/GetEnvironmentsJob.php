<?php

namespace App\Jobs;

use App\Events\EnvironmentStatusEvent;
use App\Services\EnvironmentService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class GetEnvironmentsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct()
    {
    }

    public function handle(): array
    {
        $vms = (new EnvironmentService())->getEnvironments();

        // Dispatch our event to update the front-end with new information
        EnvironmentStatusEvent::dispatch($vms);

        return $vms;
    }
}
