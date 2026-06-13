<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PollStatusChanged implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $status;
    public $pollHtml;
    public $pollData;

    /**
     * Create a new event instance.
     */
    public function __construct($status, $pollHtml = null, $pollData = null)
    {
        $this->status = $status;
        $this->pollHtml = $pollHtml;
        $this->pollData = $pollData;
    }

    /**
     * Get the channels the event should broadcast on.
     */
    public function broadcastOn(): Channel
    {
        return new Channel('poll-channel');
    }

    /**
     * The event's broadcast name.
     */
    public function broadcastAs(): string
    {
        return 'poll-status-changed';
    }

    /**
     * Get the data to broadcast.
     */
    public function broadcastWith(): array
    {
        return [
            'status' => $this->status,
            'poll_html' => $this->pollHtml,
            'poll_data' => $this->pollData,
            'timestamp' => now()->toDateTimeString()
        ];
    }
}