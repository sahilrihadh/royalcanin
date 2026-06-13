<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Carbon\Carbon;

class RegistrationConfirmation extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $sessions;
    public $nextSession;

    public function __construct(User $user)
    {
        $this->user = $user;

        $today = Carbon::today();

        $this->sessions = [
            ['date' => '27 May 2026', 'day' => 'Wednesday', 'topic' => 'When Angry Pancreas throws a tantrum', 'time' => '1 PM - 2 PM', 'timestamp' => Carbon::parse('2026-05-27')],
            ['date' => '26 June 2026', 'day' => 'Friday', 'topic' => 'Hungry, hungry doggo - The EPI edition', 'time' => '7 PM - 8 PM', 'timestamp' => Carbon::parse('2026-06-26')],
            ['date' => '22 Jul 2026', 'day' => 'Wednesday', 'topic' => 'Serial poopers - Loose stools, long tales', 'time' => '1 PM - 2 PM', 'timestamp' => Carbon::parse('2026-07-22')],
            ['date' => '19 Aug 2026', 'day' => 'Wednesday', 'topic' => 'Acute diarrhoea - New tricks, Same mess', 'time' => '1 PM - 2 PM', 'timestamp' => Carbon::parse('2026-08-19')],
            ['date' => '23 Sep 2026', 'day' => 'Wednesday', 'topic' => 'Liver under pressure - Let liver Live. Part 1', 'time' => '1 PM - 2 PM', 'timestamp' => Carbon::parse('2026-09-23')],
            ['date' => '21 Oct 2026', 'day' => 'Wednesday', 'topic' => 'Liver under pressure - Let liver Live. Part 2', 'time' => '1 PM - 2 PM', 'timestamp' => Carbon::parse('2026-10-21')]
        ];

        // Find next upcoming session
        $this->nextSession = collect($this->sessions)->first(function ($session) use ($today) {
            return $session['timestamp']->gte($today);
        });
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "Registration Confirmed: GI Horizons Webinar Series | Royal Canin",
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.registration-confirmation',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
