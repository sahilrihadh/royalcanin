<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\WebinarReminder;
use App\Models\User;
use App\Support\WebinarSchedule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class ReminderController extends Controller
{
    private const BATCH_SIZE = 25;

    public function index()
    {
        $totalUsers = User::count();
        $sessions = WebinarSchedule::sessions();
        $nextSession = WebinarSchedule::nextSession();

        return view('admin.reminders.index', compact('totalUsers', 'sessions', 'nextSession'));
    }

    /**
     * Send one batch of reminder emails, synchronously, and report back what
     * happened so the browser can keep calling this until every registered
     * user has been processed. Keeps each HTTP request short regardless of
     * how many users are registered, without depending on a queue worker
     * actually running.
     */
    public function sendBatch(Request $request)
    {
        $validated = $request->validate([
            'offset' => ['required', 'integer', 'min:0'],
            'session_index' => ['required', 'integer', 'min:0'],
        ]);

        $sessions = WebinarSchedule::sessions();
        $sessionIndex = min($validated['session_index'], count($sessions) - 1);
        $targetSession = $sessions[$sessionIndex];

        $offset = $validated['offset'];
        $total = User::count();

        $users = User::orderBy('id')->skip($offset)->take(self::BATCH_SIZE)->get();

        $sent = [];
        $failed = [];

        foreach ($users as $user) {
            try {
                Mail::to($user->email_id)->send(new WebinarReminder($user, $targetSession));
                $sent[] = ['name' => $user->full_name, 'email' => $user->email_id];
            } catch (\Exception $e) {
                \Log::error('Webinar reminder email failed: ' . $e->getMessage(), ['user_id' => $user->id]);
                $failed[] = ['name' => $user->full_name, 'email' => $user->email_id];
            }
        }

        $nextOffset = $offset + self::BATCH_SIZE;

        return response()->json([
            'sent' => $sent,
            'failed' => $failed,
            'processed' => min($nextOffset, $total),
            'total' => $total,
            'next_offset' => $nextOffset,
            'done' => $nextOffset >= $total,
        ]);
    }
}
