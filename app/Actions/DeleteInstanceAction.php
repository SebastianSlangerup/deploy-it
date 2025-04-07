<?php

namespace App\Actions;

use App\Models\Instance;
use Illuminate\Support\Facades\Http;

class DeleteInstanceAction
{
    public function execute(Instance $instance): void
    {
        Http::proxmox()->delete('v1/vm/delete_vm', [
            'node' => 'node1',
            'vmid' => $instance->vm_id,
        ]);

        $instance->delete();
    }
}
