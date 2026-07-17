<?php
// app/Http/Controllers/Admin/PreviousSessionController.php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PreviousSession;
use App\Exports\PreviousSessionsExport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Http\Request;

class PreviousSessionController extends Controller
{
    public function index(Request $request)
    {
        $sessionName = $request->get('session_name');

        $sessions = PreviousSession::when($sessionName, function ($query) use ($sessionName) {
                return $query->where('session_name', $sessionName);
            })
            ->orderBy('watched_on', 'desc')
            ->paginate(20);

        // Drives the webinar filter buttons — automatically picks up any new
        // session_name value as soon as a record with it exists, no code change needed.
        $webinars = PreviousSession::select('session_name')
            ->distinct()
            ->orderBy('session_name')
            ->pluck('session_name');

        return view('admin.previous-sessions.index', compact(
            'sessions',
            'webinars',
            'sessionName'
        ));
    }

    /**
     * No longer linked from the index page (View button removed). Left in
     * place in case admin.previous-sessions.show is still used elsewhere —
     * delete this method + its route + resources/views/.../show.blade.php
     * together if you want the feature fully gone.
     */
    public function show($id)
    {
        $session = PreviousSession::findOrFail($id);
        return view('admin.previous-sessions.show', compact('session'));
    }

    public function export(Request $request)
    {
        $sessionName = $request->get('session_name');

        $sessions = PreviousSession::when($sessionName, function ($query) use ($sessionName) {
                return $query->where('session_name', $sessionName);
            })
            ->orderBy('watched_on', 'desc')
            ->get();

        if ($sessions->isEmpty()) {
            return back()->with('error', 'No session records found.');
        }

        $filename = $sessionName
            ? 'sessions_' . $sessionName . '.xlsx'
            : 'sessions_all.xlsx';

        return Excel::download(
            new PreviousSessionsExport($sessions, $sessionName),
            $filename
        );
    }

    public function resendCertificate($id)
    {
        try {
            $session = PreviousSession::findOrFail($id);

            // Resend certificate logic here
            // Mail::to($session->email_id)->send(new CertificateMail(...));

            return response()->json([
                'success' => true,
                'message' => 'Certificate resent successfully!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to resend certificate'
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $session = PreviousSession::findOrFail($id);
            $session->delete();

            return response()->json([
                'success' => true,
                'message' => 'Session record deleted successfully!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete record: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Kept for backward compatibility with the existing route — no longer
     * triggered from the UI since bulk delete was removed from the page.
     */
    public function bulkDelete(Request $request)
    {
        try {
            $ids = $request->ids;

            if (empty($ids)) {
                return response()->json([
                    'success' => false,
                    'message' => 'No records selected'
                ], 400);
            }

            $deleted = PreviousSession::whereIn('id', $ids)->delete();

            return response()->json([
                'success' => true,
                'message' => $deleted . ' records deleted successfully!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete records: ' . $e->getMessage()
            ], 500);
        }
    }
}