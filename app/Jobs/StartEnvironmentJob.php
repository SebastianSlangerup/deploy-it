<?php

namespace App\Jobs;

use App\Http\Controllers\EnvironmentController;
use App\Models\Environment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class StartEnvironmentJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(private readonly Environment $environment)
    {
    }

    public function handle(): void
    {
        (new EnvironmentController)->control($this->environment, 'start');
    }
}
