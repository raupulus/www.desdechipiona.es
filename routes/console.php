<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Muestra una cita inspiradora');

// Programar la regeneración diaria del sitemap XML a las 03:00
Schedule::command('sitemap:generate')->dailyAt('03:00')->withoutOverlapping();
