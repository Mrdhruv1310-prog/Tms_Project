<?php

namespace App\Mail;

use App\Models\Task;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class TaskStatusUpdateMail extends Mailable
{
    use Queueable, SerializesModels;

    public Task $task;
    public User $user;
    public string $status;
    public string $remark;

    /**
     * Create a new message instance.
     */
    public function __construct(
        Task $task,
        User $user,
        string $status,
        string $remark
    ) {
        $this->task = $task;
        $this->user = $user;
        $this->status = $status;
        $this->remark = $remark;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Task Updated - ' . $this->task->title,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        Log::info('Task Update Mail View Loaded', [
            'task_id' => $this->task->id,
            'task_title' => $this->task->title,
            'user_email' => $this->user->email,
            'status' => $this->status,
        ]);

        return new Content(
            view: 'emails.task-status-update',
            with: [
                'task' => $this->task,
                'user' => $this->user,
                'status' => $this->status,
                'remark' => $this->remark,
            ],
        );
    }

    /**
     * Get the attachments for the message.
     */
    public function attachments(): array
    {
        return [];
    }
}
