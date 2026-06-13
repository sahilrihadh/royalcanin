<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class CertificateMail extends Mailable
{
    use Queueable, SerializesModels;

    public $fullName;
    public $eventDate;
    public $certificatePath;

    public function __construct($fullName, $eventDate, $certificatePath)
    {
        $this->fullName = $fullName;
        $this->eventDate = $eventDate;
        $this->certificatePath = $certificatePath;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'GI HORIZONS Webinar Series Participation Certificate',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.certificate',
        );
    }

    public function attachments(): array
    {
        // If you want to attach the file instead of link
        $fullPath = storage_path('app/public/' . $this->certificatePath);

        if (file_exists($fullPath)) {
            return [
                \Illuminate\Mail\Mailables\Attachment::fromPath($fullPath)
                    ->as('certificate.png')
                    ->withMime('image/png'),
            ];
        }

        return [];
    }
}
