<?php

namespace App\Jobs;

use App\Models\Task;
use App\Models\User;
use App\Services\WhatsAppService;
use Carbon\Carbon;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;
use RuntimeException;

class SendTaskAssignedWhatsAppJob implements ShouldQueue
{
    use Queueable;

    public int $tries = 3;

    public int $timeout = 60;

    public array $backoff = [
        30,
        120,
        300,
    ];

    public function __construct(
        public int $taskId,
        public int $userId
    ) {}

    public function handle(
        WhatsAppService $whatsApp
    ): void {

        $task = Task::find(
            $this->taskId
        );

        if (! $task) {

            Log::warning(
                'Task Assigned WhatsApp skipped: task missing.',
                [
                    'task_id' => $this->taskId,
                ]
            );

            return;
        }

        $user = User::find(
            $this->userId
        );

        if (! $user) {

            Log::warning(
                'Task Assigned WhatsApp skipped: user missing.',
                [
                    'user_id' => $this->userId,
                ]
            );

            return;
        }

        /*
        |--------------------------------------------------------------------------
        | PHONE NUMBER
        |--------------------------------------------------------------------------
        */

        $phoneNumber =
            $user->phone_number
            ?? $user->mobile_number
            ?? null;

        if (blank($phoneNumber)) {

            Log::warning(
                'Task Assigned WhatsApp skipped: user phone missing.',
                [
                    'task_id' => $task->id,

                    'user_id' => $user->id,
                ]
            );

            return;
        }

        /*
        |--------------------------------------------------------------------------
        | USER NAME
        |--------------------------------------------------------------------------
        */

        $userName = trim(
            ($user->first_name ?? '')
                . ' '
                . ($user->last_name ?? '')
        );

        if (blank($userName)) {

            $userName =
                $user->name
                ?? 'User';
        }

        /*
        |--------------------------------------------------------------------------
        | TASK DATA
        |--------------------------------------------------------------------------
        */

        $taskTitle =
            filled($task->title)
            ? $task->title
            : 'Task';

        $priority =
            ucfirst(
                (string) (
                    $task->priority
                    ?? 'Normal'
                )
            );

        $dueDate =
            filled($task->due_date)
            ? Carbon::parse(
                $task->due_date
            )->format(
                'd/m/Y h:i A'
            )
            : 'Not Set';

        $status =
            ucfirst(
                str_replace(
                    '_',
                    ' ',
                    (string) (
                        $task->status
                        ?? 'Pending'
                    )
                )
            );

        Log::info(
            'Task Assigned WhatsApp Job Started',
            [
                'task_id' => $task->id,

                'user_id' => $user->id,

                'template' => config(
                    'services.whatsapp.task_assigned_template'
                ),
            ]
        );

        /*
        |--------------------------------------------------------------------------
        | SEND MESSAGE
        |--------------------------------------------------------------------------
        */

        $result =
            $whatsApp->sendTaskAssigned(
                $phoneNumber,
                $userName,
                $taskTitle,
                $priority,
                $dueDate,
                $status
            );

        /*
        |--------------------------------------------------------------------------
        | FAILED
        |--------------------------------------------------------------------------
        */

        if (! (
            $result['success']
            ?? false
        )) {

            $errorCode =
                $result['error_code']
                ?? null;

            $message =
                $result['message']
                ?? 'Unknown Meta WhatsApp Error';

            Log::error(
                'Task Assigned WhatsApp Job Failed',
                [
                    'task_id' => $task->id,

                    'user_id' => $user->id,

                    'error_code' => $errorCode,

                    'message' => $message,
                ]
            );

            throw new RuntimeException(
                'Task Assigned WhatsApp failed: '
                    . $message
            );
        }

        /*
        |--------------------------------------------------------------------------
        | SUCCESS
        |--------------------------------------------------------------------------
        */

        Log::info(
            'Task Assigned WhatsApp Sent',
            [
                'task_id' => $task->id,

                'user_id' => $user->id,

                'message_id' =>
                $result['message_id']
                    ?? null,
            ]
        );
    }

    public function failed(
        \Throwable $exception
    ): void {

        Log::error(
            'Task Assigned WhatsApp Job Permanently Failed',
            [
                'task_id' => $this->taskId,

                'user_id' => $this->userId,

                'error' =>
                $exception->getMessage(),
            ]
        );
    }
}
