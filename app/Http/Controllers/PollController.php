<?php

namespace App\Http\Controllers;

use App\Events\PollStatusChanged;
use App\Models\Poll;
use App\Models\PollOption;
use App\Models\PollVote;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class PollController extends Controller
{
    /**
     * Check if there's an active poll
     */
    public function checkPoll(Request $request)
    {
        try {
            $activePoll = Poll::where('is_active', true)
                ->where('status', 'published')
                ->where('start_date', '<=', now())
                ->where(function ($q) {
                    $q->whereNull('end_date')
                        ->orWhere('end_date', '>=', now());
                })
                ->with('options')
                ->first();

            if (!$activePoll) {
                return response()->json('NO_POLL_ACTIVE');
            }

            // Get user's existing vote
            $userVote = PollVote::where('poll_id', $activePoll->id)
                ->where('user_id', Auth::id())
                ->first();

            // Generate poll HTML
            $html = $this->generatePollHtml($activePoll, $userVote);

            return response()->json($html);
        } catch (\Exception $e) {
            Log::error('Check poll error: ' . $e->getMessage());
            return response()->json('NO_POLL_ACTIVE');
        }
    }

    /**
     * Submit vote for a poll
     */
    public function submitVote(Request $request)
    {
        try {
            $request->validate([
                'poll_id' => 'required|exists:polls,id',
                'option_id' => 'required|exists:poll_options,id'
            ]);

            $userId = Auth::id();

            // Check if user already voted
            $existingVote = PollVote::where('poll_id', $request->poll_id)
                ->where('user_id', $userId)
                ->first();

            if ($existingVote) {
                return response()->json(['success' => false, 'message' => 'Already voted'], 400);
            }

            // Save vote
            PollVote::create([
                'poll_id' => $request->poll_id,
                'option_id' => $request->option_id,
                'user_id' => $userId,
                'voted_at' => now()
            ]);

            // Increment vote count on option
            PollOption::where('id', $request->option_id)->increment('votes');

            // Get updated poll data for real-time update
            $activePoll = Poll::with('options')->find($request->poll_id);

            // Broadcast updated results to all users
            broadcast(new PollStatusChanged($activePoll))->toOthers();

            return response()->json([
                'success' => true,
                'message' => 'Vote submitted successfully'
            ]);
        } catch (\Exception $e) {
            Log::error('Submit vote error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to submit vote'
            ], 500);
        }
    }

    /**
     * Get poll results (for display after voting)
     */
    public function getPollResults($pollId)
    {
        try {
            $poll = Poll::with('options')->findOrFail($pollId);
            $totalVotes = $poll->options->sum('votes');

            $results = [];
            foreach ($poll->options as $option) {
                $percentage = $totalVotes > 0 ? round(($option->votes / $totalVotes) * 100, 1) : 0;
                $results[] = [
                    'id' => $option->id,
                    'text' => $option->option_text,
                    'votes' => $option->votes,
                    'percentage' => $percentage
                ];
            }

            return response()->json([
                'success' => true,
                'poll_title' => $poll->title,
                'total_votes' => $totalVotes,
                'results' => $results
            ]);
        } catch (\Exception $e) {
            Log::error('Get poll results error: ' . $e->getMessage());
            return response()->json(['success' => false], 500);
        }
    }

    /**
     * Admin: Create a new poll
     */
    public function createPoll(Request $request)
    {
        try {
            $request->validate([
                'title' => 'required|string|max:255',
                'description' => 'nullable|string',
                'options' => 'required|array|min:2',
                'options.*' => 'required|string|max:255',
                'start_date' => 'nullable|date',
                'end_date' => 'nullable|date|after:start_date'
            ]);

            $poll = Poll::create([
                'title' => $request->title,
                'description' => $request->description,
                'status' => 'draft',
                'start_date' => $request->start_date ?? now(),
                'end_date' => $request->end_date,
                'created_by' => Auth::id()
            ]);

            foreach ($request->options as $option) {
                PollOption::create([
                    'poll_id' => $poll->id,
                    'option_text' => $option,
                    'votes' => 0
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Poll created successfully',
                'poll_id' => $poll->id
            ]);
        } catch (\Exception $e) {
            Log::error('Create poll error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Failed to create poll'], 500);
        }
    }

    /**
     * Admin: Activate/publish a poll
     */
    public function activatePoll(Request $request)
    {
        try {
            $request->validate([
                'poll_id' => 'required|exists:polls,id'
            ]);

            // Deactivate all other active polls first
            Poll::where('is_active', true)->update(['is_active' => false]);

            // Activate the selected poll
            $poll = Poll::find($request->poll_id);
            $poll->update([
                'is_active' => true,
                'status' => 'published',
                'published_at' => now()
            ]);

            // Load poll with options
            $poll->load('options');

            // Generate HTML for the poll
            $pollHtml = $this->generatePollHtml($poll, null);

            // Broadcast to all connected clients
            broadcast(new PollStatusChanged($pollHtml));

            return response()->json([
                'success' => true,
                'message' => 'Poll activated successfully',
                'poll_html' => $pollHtml
            ]);
        } catch (\Exception $e) {
            Log::error('Activate poll error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Failed to activate poll'], 500);
        }
    }

    /**
     * Admin: Deactivate poll
     */
    public function deactivatePoll(Request $request)
    {
        try {
            $request->validate([
                'poll_id' => 'required|exists:polls,id'
            ]);

            $poll = Poll::find($request->poll_id);
            $poll->update(['is_active' => false]);

            // Broadcast to all connected clients that poll is closed
            broadcast(new PollStatusChanged(null));

            return response()->json([
                'success' => true,
                'message' => 'Poll deactivated successfully'
            ]);
        } catch (\Exception $e) {
            Log::error('Deactivate poll error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Failed to deactivate poll'], 500);
        }
    }

    /**
     * Admin: Get all polls
     */
    public function getAllPolls()
    {
        try {
            $polls = Poll::with('options')
                ->orderBy('created_at', 'desc')
                ->get();

            return response()->json([
                'success' => true,
                'polls' => $polls
            ]);
        } catch (\Exception $e) {
            Log::error('Get polls error: ' . $e->getMessage());
            return response()->json(['success' => false], 500);
        }
    }

    /**
     * Generate HTML for poll display
     */
    private function generatePollHtml($poll, $userVote = null)
    {
        $html = '<div class="poll-container">';
        $html .= '<h4>' . e($poll->title) . '</h4>';

        if ($poll->description) {
            $html .= '<p>' . e($poll->description) . '</p>';
        }

        if ($userVote) {
            // Show results if user already voted
            $totalVotes = $poll->options->sum('votes');
            $html .= '<div class="poll-results">';
            foreach ($poll->options as $option) {
                $percentage = $totalVotes > 0 ? round(($option->votes / $totalVotes) * 100, 1) : 0;
                $html .= '<div class="poll-option-result mb-3">';
                $html .= '<div class="d-flex justify-content-between mb-1">';
                $html .= '<span>' . e($option->option_text) . '</span>';
                $html .= '<span>' . $percentage . '% (' . $option->votes . ' votes)</span>';
                $html .= '</div>';
                $html .= '<div class="progress">';
                $html .= '<div class="progress-bar bg-danger" style="width: ' . $percentage . '%"></div>';
                $html .= '</div>';
                $html .= '</div>';
            }
            $html .= '<p class="text-muted mt-2">Total votes: ' . $totalVotes . '</p>';
            $html .= '<p class="text-success">✓ You have already voted</p>';
            $html .= '</div>';
        } else {
            // Show voting options
            $html .= '<form id="poll-form">';
            $html .= '<input type="hidden" name="poll_id" value="' . $poll->id . '">';
            foreach ($poll->options as $option) {
                $html .= '<div class="form-check mb-2">';
                $html .= '<input class="form-check-input" type="radio" name="poll_option" value="' . $option->id . '" id="option_' . $option->id . '">';
                $html .= '<label class="form-check-label" for="option_' . $option->id . '">';
                $html .= e($option->option_text);
                $html .= '</label>';
                $html .= '</div>';
            }
            $html .= '<button type="submit" id="but_vote" class="btn btn-danger mt-3">Submit Vote</button>';
            $html .= '</form>';
        }

        $html .= '</div>';

        return $html;
    }
}
