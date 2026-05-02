<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ReminderEmail extends Mailable
{
    use Queueable, SerializesModels;
    public $reminder;
    public $task;

    /**
     * Create a new message instance.
     */
    public function __construct($reminder, $task)
    {
        $this->reminder = $reminder;
        $this->task = $task;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Reminder Email',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        $assignedByUser = $this->task->assignedBy; // Task's assigned_by relationship
        $recipient = $this->reminder->recipient; // Reminder's recipient relationship

        return new Content(
            view: 'emails.reminder',
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
