<?php

namespace App\Events;

use App\Models\Question;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class QuestionAnswered implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $question;

    /**
     * Create a new event instance.
     */
    public function __construct(Question $question)
    {
        $this->question = $question->load('user');
    }

    /**
     * Get the channels the event should broadcast on.
     */
    public function broadcastOn(): Channel
    {
        return new Channel('question-channel');
    }

    /**
     * The event's broadcast name.
     */
    public function broadcastAs(): string
    {
        return 'question-answered';
    }

    /**
     * Get the data to broadcast.
     */
    public function broadcastWith(): array
    {
        return [
            'id' => $this->question->id,
            'user_name' => $this->question->user ? ($this->question->user->full_name ?? $this->question->user->name ?? 'Anonymous') : 'Anonymous',
            'question_text' => $this->question->question_text ?? $this->question->question_input,
            'answer_text' => $this->question->answer_text,
            'is_answered' => $this->question->is_answered,
            'answered_at' => $this->question->answered_at ? $this->question->answered_at->diffForHumans() : now()->diffForHumans(),
            'created_at' => $this->question->created_at->diffForHumans()
        ];
    }
}