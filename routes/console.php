<?php

use App\Jobs\GetEnvironmentsJob;
use Illuminate\Support\Facades\Schedule;

Schedule::job(new GetEnvironmentsJob)->everyFifteenSeconds();
