<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Note: Cron job được setup trực tiếp trong crontab:
// 0 23 * * * cd /path/to/project && php artisan thuchi365:close-day >> /dev/null 2>&1
