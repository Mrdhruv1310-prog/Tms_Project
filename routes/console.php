<?php

use Illuminate\Support\Facades\Schedule;


// Artisan::command('inspire', function () {
//     $quote = Inspiring::quote();
//     $this->comment($quote); // Display in console output
//     Log::info('Inspire command executed', ['quote' => $quote]); // Log it explicitly
// })->purpose('Display an inspiring quote')->everyTenSeconds();

Schedule::command('repeattask:cron')->everyMinute();
