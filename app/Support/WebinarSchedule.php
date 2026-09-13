<?php

namespace App\Support;

use Carbon\Carbon;

class WebinarSchedule
{
    /**
     * The GI Horizons series schedule. Matches the dates already hardcoded
     * in RegistrationConfirmation and its email view — kept here as the one
     * source new mailables (like WebinarReminder) build from.
     */
    public static function sessions(): array
    {
        return [
            ['date' => '27 May 2026', 'day' => 'Wednesday', 'topic' => 'When Angry Pancreas throws a tantrum', 'time' => '1 PM - 2 PM', 'timestamp' => Carbon::parse('2026-05-27')],
            ['date' => '26 June 2026', 'day' => 'Friday', 'topic' => 'Hungry, hungry doggo - The EPI edition', 'time' => '7 PM - 8 PM', 'timestamp' => Carbon::parse('2026-06-26')],
            ['date' => '22 Jul 2026', 'day' => 'Wednesday', 'topic' => 'Serial poopers - Loose stools, long tales', 'time' => '1 PM - 2 PM', 'timestamp' => Carbon::parse('2026-07-22')],
            ['date' => '19 Aug 2026', 'day' => 'Wednesday', 'topic' => 'Acute diarrhoea - New tricks, Same mess', 'time' => '1 PM - 2 PM', 'timestamp' => Carbon::parse('2026-08-19')],
            ['date' => '23 Sep 2026', 'day' => 'Wednesday', 'topic' => 'Liver under pressure - Let liver Live. Part 1', 'time' => '1 PM - 2 PM', 'timestamp' => Carbon::parse('2026-09-23')],
            ['date' => '21 Oct 2026', 'day' => 'Wednesday', 'topic' => 'Liver under pressure - Let liver Live. Part 2', 'time' => '1 PM - 2 PM', 'timestamp' => Carbon::parse('2026-10-21')],
        ];
    }

    /**
     * The first session whose date hasn't passed yet, or null once the
     * whole series is over.
     */
    public static function nextSession(): ?array
    {
        $today = Carbon::today();

        return collect(self::sessions())->first(fn ($session) => $session['timestamp']->gte($today));
    }
}
