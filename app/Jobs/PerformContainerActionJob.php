<?php

namespace App\Jobs;

use App\Enums\InstanceActionsEnum;
use App\Models\Instance;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;

class PerformContainerActionJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        public User $user,
        public Instance $instance,
        public string $action,
    ) {}

    public function handle(): void
    {
        try {
            $response = Http::proxmox()->withQueryParameters([
                'node' => $this->instance->node,
            ]);
        } catch (ConnectionException $exception) {

        }
    }
}
