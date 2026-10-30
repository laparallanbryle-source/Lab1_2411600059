<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// I-schedule ang reorder check araw-araw ng 6:00 AM
Schedule::command('inventory:check-reorder')->dailyAt('06:00');