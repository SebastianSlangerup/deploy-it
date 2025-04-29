<?php

namespace App\Actions;

use App\Data\NotificationData;
use App\Enums\NotificationTypeEnum;
use App\Events\NotifyUserEvent;
use App\Models\Instance;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class DeleteInstanceAction
{
    /**
     * @throws ConnectionException
     */
    public function execute(Request $request, Instance $instance): void
    {
        $response = Http::proxmox()->delete('/delete_vm', [
            'node' => 'node1',
            'vmid' => $instance->vm_id,
        ]);

        if (! $response->successful()) {
            Log::error('Failed to delete instance: {instance}. Error message: {message}', [
                'instance' => "[ID: {$instance->id}]",
                'message' => $response->body(),
            ]);

            $notification = NotificationData::from([
                'title' => 'Failed to delete instance',
                'description' => 'Could not delete the instance',
                'notificationType' => NotificationTypeEnum::Error,
            ]);

            NotifyUserEvent::dispatch($request->user(), $notification);

            return;
        }

        $instance->delete();
    }
}
