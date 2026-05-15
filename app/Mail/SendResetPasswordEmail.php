<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class SendResetPasswordEmail extends Mailable
{
    use Queueable, SerializesModels;

    protected $signature = 'emails:reset_password';

    public $user;
    public $token;

    /**
     * Create a new message instance.
     *
     * @param $user
     * @param $token
     * @return void
     */
    public function __construct($user, $token)
    {
        $this->user = $user;
        $this->token = $token;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(subject: 'Send Reset Password Email');
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.reset_password',

            with: [

                'user' => $this->user,

                'resetLink' => route(
                    'password.reset',
                    [
                        'token' => $this->token,
                        'email' => $this->user->email,
                    ]
                ),

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
