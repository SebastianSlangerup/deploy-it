<?php

use App\Jobs\FetchConfigurationsJob;
use Illuminate\Support\Facades\Schedule;

Schedule::job(new FetchConfigurationsJob)->daily();
