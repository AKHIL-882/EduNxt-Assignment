<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

use Illuminate\Support\Facades\Schedule;

Schedule::command('system:maintain')
    ->dailyAt('00:00')
    ->withoutOverlapping()
    ->sendOutputTo(storage_path('logs/daily_maintenance.log'));