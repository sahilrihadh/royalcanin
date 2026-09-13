<?php

namespace App\Mail;

use App\Models\User;
use App\Support\WebinarSchedule;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class WebinarReminder extends Mailable
{
    use Queueable, SerializesModels;

    public User $user;
    public array $sessions;
    public array $targetSession;

    public function __construct(User $user, array $targetSession)
    {
        $this->user = $user;
        $this->sessions = WebinarSchedule::sessions();
        $this->targetSession = $targetSession;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            from: new Address(config('mail.from.address'), 'Royal Canin India'),
            subject: 'GI Horizons – Webinar Reminder | ' . $this->targetSession['date'] . ' | Dr. K. G. Umesh',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.webinar-reminder',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
