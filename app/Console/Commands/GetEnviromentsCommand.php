<?php

namespace App\Console\Commands;

use App\Jobs\GetEnvironmentsJob;
use Illuminate\Console\Command;

class GetEnviromentsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'environments:get';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Hent alle VMer';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        GetEnvironmentsJob::dispatch();
    }
}
