<?php

use Carbon\Carbon;
use PHPUnit\Framework\TestCase;
use App\Console\Commands\RepeatTaskCron;
use App\Models\Task;

class RepeatTaskCronTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        // Set the current date to 2025-02-28
        Carbon::setTestNow(Carbon::create(2025, 2, 28));
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        // Reset the mocked date
        Carbon::setTestNow();
    }

    public function testCalculateNextDueDateDaily()
    {
        $task = new Task(['recurrence' => 'daily']);
        $cron = new RepeatTaskCron();

        $nextDueDate = $cron->calculateNextDueDate($task);

        $this->assertEquals(Carbon::now()->addDay()->toDateString(), $nextDueDate->toDateString());
    }

    public function testCalculateNextDueDateWeekly()
    {
        $task = new Task(['recurrence' => 'weekly']);
        $cron = new RepeatTaskCron();

        $nextDueDate = $cron->calculateNextDueDate($task);

        $this->assertEquals(Carbon::now()->addWeek()->toDateString(), $nextDueDate->toDateString());
    }

    public function testCalculateNextDueDateMonthly()
    {
        $task = new Task(['recurrence' => 'monthly', 'due_date' => Carbon::now()->day(15)]);
        $cron = new RepeatTaskCron();

        $nextDueDate = $cron->calculateNextDueDate($task);

        $this->assertEquals(Carbon::now()->addMonth()->day(15)->toDateString(), $nextDueDate->toDateString());
    }

    public function testCalculateNextDueDateMonthlyEndOfMonth()
    {
        $task = new Task(['recurrence' => 'monthly', 'due_date' => Carbon::now()->endOfMonth()]);
        $cron = new RepeatTaskCron();

        $nextDueDate = $cron->calculateNextDueDate($task);

        $this->assertEquals(Carbon::now()->addMonth()->endOfMonth()->toDateString(), $nextDueDate->toDateString());
    }

    public function testCalculateNextDueDateMonthlyLeapYear()
    {
        $task = new Task(['recurrence' => 'monthly', 'due_date' => Carbon::create(2020, 2, 29)]);
        $cron = new RepeatTaskCron();

        $nextDueDate = $cron->calculateNextDueDate($task);

        $this->assertEquals(Carbon::create(2021, 2, 28)->toDateString(), $nextDueDate->toDateString());
    }
}