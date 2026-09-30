<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;


Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

/*
|--------------------------------------------------------------------------
| Scheduled Tasks
|--------------------------------------------------------------------------
*/

// Update status peminjaman terlambat — setiap hari jam 00:05
Schedule::command('loans:update-overdue')
    ->dailyAt('00:05')
    ->description('Update status peminjaman terlambat');