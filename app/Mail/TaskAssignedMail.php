<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class TaskAssignedMail extends Mailable
{
    use Queueable, SerializesModels;

    public $task; // Task details
    public $user; // User details

    /**
     * Create a new message instance.
     *
     * @param mixed $task
     * @param mixed $user
     */
    public function __construct($task, $user)
    {
        $this->task = $task;
        $this->user = $user;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Task Assigned: ' . $this->task->title,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        // log the task and user details for debugging
        Log::info('Task Assigned Mail', [
            'task' => $this->task,
            'user' => $this->user,
        ]);
        return new Content(
            view: 'emails.task-assigned', // Path to the Blade view
            with: [
                'task' => $this->task,
                'user' => $this->user,
            ],
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
