<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Picks up deployments queued from Admin > Deployments on hosts that block background processes.
Schedule::command('deploy:run-pending')->everyMinute()->withoutOverlapping();
