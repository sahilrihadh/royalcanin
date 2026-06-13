<?php

namespace App\Events;

use App\Models\Announcement;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ShowAnnouncement implements ShouldBroadcastNow    
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $announcement;

    public function __construct(Announcement $announcement)
    {
        $this->announcement = $announcement;
    }

    public function broadcastOn()
    {
        return new Channel('announcements');
    }

    public function broadcastAs()
    {
        return 'show-announcement';
    }

    public function broadcastWith()
    {
        return [
            'id' => $this->announcement->id,
            'title' => $this->announcement->title,
            'description' => $this->announcement->description,
            'created_at' => $this->announcement->created_at->toDateTimeString()
        ];
    }
}