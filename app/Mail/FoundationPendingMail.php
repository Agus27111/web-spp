<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class FoundationPendingMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;
    public $foundation;

    /**
     * Create a new message instance.
     */
    public function __construct($foundation)
    {
        $this->foundation = $foundation;
        Log::channel('emails')->info('Creating FoundationPendingMail', ['email' => $foundation->email]);
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Pendaftaran Yayasan Sedang Diproses',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.foundation-pending',
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
