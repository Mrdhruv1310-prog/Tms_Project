<?php

namespace App\Mail;

use App\Models\Task;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class TimeWiseReminderMail extends Mailable
{
    use Queueable, SerializesModels;

    public Task $task;
    public User $user;
    public string $remark;

    public function __construct(Task $task, User $user, string $remark)
    {
        $this->task = $task;
        $this->user = $user;
        $this->remark = $remark;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Task Reminder - ' . $this->task->title,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.time-wise-reminder',
            with: [
                'task' => $this->task,
                'user' => $this->user,
                'remark' => $this->remark,
                'description' => $this->task->description,
                'due_date' => $this->task->due_date,
                'priority' => $this->task->priority,
                'task_url' => url('/tasks?task_view=tasks&task_id=' . $this->task->id),
            ],
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
