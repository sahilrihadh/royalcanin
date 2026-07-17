<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LoginDetails;
use App\Models\User;
use App\Exports\LoginDetailsExport;
use App\Exports\AttendanceExport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Pagination\LengthAwarePaginator;

class LoginDetailController extends Controller
{
    /**
     * Raw per-session login log.
     */
    public function index(Request $request)
    {
        $startDate = $request->get('start_date', now()->format('Y-m-d'));
        $endDate = $request->get('end_date', now()->format('Y-m-d'));

        $loginDetails = LoginDetails::with('user')
            ->whereDate('login_time', '>=', $startDate)
            ->whereDate('login_time', '<=', $endDate)
            ->orderBy('login_time', 'desc')
            ->paginate(20);

        return view('admin.login-details.index', compact(
            'loginDetails',
            'startDate',
            'endDate'
        ));
    }

    /**
     * Daily attendance summary: one row per user per day, sessions merged
     * and durations summed — for checking who showed up on a given day.
     */
    public function attendance(Request $request)
    {
        $startDate = $request->get('start_date', now()->format('Y-m-d'));
        $endDate = $request->get('end_date', now()->format('Y-m-d'));
        $perPage = 20;

        $grouped = $this->groupSessionsByUserAndDay(
            $this->fetchSessions($startDate, $endDate)
        );

        $page = LengthAwarePaginator::resolveCurrentPage('page');
        $items = $grouped->slice(($page - 1) * $perPage, $perPage)->values();

        $attendance = new LengthAwarePaginator(
            $items,
            $grouped->count(),
            $perPage,
            $page,
            [
                'path' => $request->url(),
                'query' => $request->query(),
            ]
        );

        return view('admin.login-details.attendance', compact('attendance', 'startDate', 'endDate'));
    }

    /**
     * Export the raw per-session log.
     */
    public function export(Request $request)
    {
        $startDate = $request->get('start_date', now()->format('Y-m-d'));
        $endDate = $request->get('end_date', now()->format('Y-m-d'));

        $loginDetails = LoginDetails::with('user')
            ->whereDate('login_time', '>=', $startDate)
            ->whereDate('login_time', '<=', $endDate)
            ->orderBy('login_time', 'desc')
            ->get();

        if ($loginDetails->isEmpty()) {
            return back()->with('error', 'No login records found for the selected date range.');
        }

        return Excel::download(
            new LoginDetailsExport($loginDetails, $startDate, $endDate),
            'login_details_' . $startDate . '_to_' . $endDate . '.xlsx'
        );
    }

    /**
     * Export the grouped daily-attendance summary (unpaginated — the full
     * date range, matching what exportAttendance's caller filtered for).
     */
    public function exportAttendance(Request $request)
    {
        $startDate = $request->get('start_date', now()->format('Y-m-d'));
        $endDate = $request->get('end_date', now()->format('Y-m-d'));

        $sessions = $this->fetchSessions($startDate, $endDate);

        if ($sessions->isEmpty()) {
            return back()->with('error', 'No login records found for the selected date range.');
        }

        $grouped = $this->groupSessionsByUserAndDay($sessions);

        return Excel::download(
            new AttendanceExport($grouped, $startDate, $endDate),
            'attendance_' . $startDate . '_to_' . $endDate . '.xlsx'
        );
    }

    /**
     * Shared query used by both the attendance view and its export.
     */
    private function fetchSessions($startDate, $endDate)
    {
        return LoginDetails::with('user')
            ->whereDate('login_time', '>=', $startDate)
            ->whereDate('login_time', '<=', $endDate)
            ->whereNotNull('login_time')
            ->orderBy('login_time')
            ->get();
    }

    /**
     * Collapse raw login sessions into one summary row per user per calendar day:
     * total sessions, first login, last activity, and summed duration
     * (open sessions count as running until now()).
     */
    private function groupSessionsByUserAndDay($sessions)
    {
        return $sessions
            ->groupBy(function ($session) {
                return $session->user_id . '|' . $session->login_time->format('Y-m-d');
            })
            ->map(function ($daySessions) {
                $first = $daySessions->first();
                $isActive = $daySessions->contains(fn($session) => is_null($session->logout_time));

                $totalMinutes = $daySessions->sum(function ($session) {
                    $end = $session->logout_time ?? now();
                    return $session->login_time->diffInMinutes($end);
                });

                $lastActivity = $daySessions
                    ->pluck('logout_time')
                    ->filter()
                    ->max();

                return (object) [
                    'user' => $first->user,
                    'date' => $first->login_time->format('Y-m-d'),
                    'session_count' => $daySessions->count(),
                    'first_login' => $daySessions->min('login_time'),
                    'last_activity' => $lastActivity,
                    'total_minutes' => $totalMinutes,
                    'total_duration' => $this->formatDuration($totalMinutes),
                    'is_active' => $isActive,
                ];
            })
            ->sortByDesc('date')
            ->values();
    }

    /**
     * Format duration in minutes to human readable format
     */
    private function formatDuration($minutes)
    {
        if ($minutes < 1) {
            return '0 min';
        }

        $hours = floor($minutes / 60);
        $mins = $minutes % 60;

        if ($hours > 0) {
            return $hours . 'h ' . $mins . 'm';
        }

        return $mins . ' min';
    }

    public function destroy($id)
    {
        try {
            $loginDetail = LoginDetails::findOrFail($id);
            $loginDetail->delete();

            return response()->json([
                'success' => true,
                'message' => 'Login record deleted successfully!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete record: ' . $e->getMessage()
            ], 500);
        }
    }

    public function clearOldRecords(Request $request)
    {
        try {
            $days = $request->get('days', 30);
            $deleted = LoginDetails::where('login_time', '<', now()->subDays($days))->delete();

            return response()->json([
                'success' => true,
                'message' => $deleted . ' records older than ' . $days . ' days deleted successfully!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to clear records: ' . $e->getMessage()
            ], 500);
        }
    }
}