<?php

namespace App\Actions;

use App\Models\Instance;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class DeleteInstanceAction
{
    /**
     * @throws ConnectionException
     */
    public function execute(Instance $instance): void
    {
        $response = Http::proxmox()->delete('/delete_vm', [
            'node' => 'node1',
            'vmid' => $instance->vm_id,
        ]);

        if (! $response->successful()) {
            Log::error('Failed to delete instance: {instance}. Error message: {message}', [
                'instance' => "[ID: {$instance->id}]",
            ]);

            return;
        }

        $instance->delete();
    }
}
