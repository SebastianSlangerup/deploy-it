<?php

namespace App\Jobs;

use App\Models\Instance;
use App\Models\Package;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class InstallPackagesJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @param  Collection<int, Package>  $selectedPackages
     */
    public function __construct(
        public Instance $instance,
        public Collection $selectedPackages,
    ) {}

    public function handle(): void
    {
        $file = Storage::disk('local')->get('package-template.sh');

        // This is gonna turn our array into space-separated values
        $packageList = $this->selectedPackages->pluck('packageName')->implode(' ');

        // Replace the __REPLACE__ filler in our template with the actual packages to install
        Str::replace('__REPLACE__', $packageList, $file);

        // Time to send the instructions to our virtual machine!
        try {

        }
    }
}
