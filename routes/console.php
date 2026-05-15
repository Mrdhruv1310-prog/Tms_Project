<?php

use Illuminate\Support\Facades\Schedule;


// Artisan::command('inspire', function () {
//     $quote = Inspiring::quote();
//     $this->comment($quote); // Display in console output
//     Log::info('Inspire command executed', ['quote' => $quote]); // Log it explicitly
// })->purpose('Display an inspiring quote')->everyTenSeconds();

Schedule::command('repeattask:cron')->everyMinute();
Schedule::command('emails:duedate')->everyMinute();
Schedule::command('emails:reminder')->everyMinute();
Schedule::command('emails:reset_password')->everyMinute();
Schedule::command('emails:task_assigned')->everyMinute();
Schedule::command('emails:task_status_update')->everyMinute();
