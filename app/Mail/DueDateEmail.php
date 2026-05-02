<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class DueDateEmail extends Mailable
{
    use Queueable, SerializesModels;
    public $task;
    public $user;

    /**
     * Create a new message instance.
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
            subject: 'Due Date Email',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        $assignedByUser = $this->task->assignedBy; // Task's assigned_by relationship
        $recipient = $this->user; // Reminder's recipient relationship

        return new Content(
            view: 'emails.duedate',
            with: [
                'task' => $this->task,
                'assignedByUser' => $assignedByUser,
                'recipient' => $recipient,
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
