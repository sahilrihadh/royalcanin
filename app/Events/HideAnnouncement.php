<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class HideAnnouncement implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $announcementId;

    public function __construct($announcementId)
    {
        $this->announcementId = $announcementId;
    }

    public function broadcastOn()
    {
        return new Channel('announcements');
    }

    public function broadcastAs()
    {
        return 'hide-announcement';
    }

    public function broadcastWith()
    {
        return [
            'announcement_id' => $this->announcementId
        ];
    }
}