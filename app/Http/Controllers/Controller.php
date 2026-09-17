<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Log;

abstract class Controller
{
    /**
     * Broadcast a real-time event without letting a broadcasting failure
     * (e.g. the Reverb server being down) block the database change that
     * already happened. Real-time push is a nice-to-have layered on top of
     * the actual admin action, not a precondition for it succeeding.
     */
    protected function broadcastSafely(object $event): void
    {
        try {
            broadcast($event)->toOthers();
        } catch (\Throwable $e) {
            Log::warning('Broadcast failed, continuing without real-time update: ' . $e->getMessage(), [
                'event' => get_class($event),
            ]);
        }
    }
}
