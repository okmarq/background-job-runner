<?php

namespace App\Mail;

use App\Models\Assignment;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class SubmissionConfirmed extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */
    public function __construct(protected Assignment $assignment)
    {
        //
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            from: new Address(env('MAIL_FROM_ADDRESS', 'okmarq@gail.com'), env('MAIL_FROM_NAME', config('app.name'))),
            replyTo: [
                new Address(env('MAIL_FROM_ADDRESS', 'okmarq@gail.com'), env('MAIL_FROM_NAME', config('app.name'))),
            ],
            subject: 'Submission Confirmed',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            markdown: 'mail.submission.confirmed',
            with: [
                'assignment' => $this->assignment,
            ]
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
