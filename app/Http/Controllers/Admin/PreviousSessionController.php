<?php
// app/Http/Controllers/Admin/PreviousSessionController.php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PreviousSession;
use Illuminate\Http\Request;

class PreviousSessionController extends Controller
{
    public function index(Request $request)
    {
        $sessions = PreviousSession::orderBy('watched_on', 'desc')->paginate(20);
        
        $totalSessions = PreviousSession::count();
        $certificatesSent = PreviousSession::where('certificate_status', 1)->count();
        $uniqueUsers = PreviousSession::distinct('email_id')->count('email_id');
        
        $webinarStats = PreviousSession::select('session_name', 
            \DB::raw('count(*) as total'),
            \DB::raw('count(CASE WHEN certificate_status = 1 THEN 1 END) as certificates_sent'))
            ->groupBy('session_name')
            ->get();
        
        return view('admin.previous-sessions.index', compact(
            'sessions', 
            'totalSessions', 
            'certificatesSent', 
            'uniqueUsers',
            'webinarStats'
        ));
    }
    
    public function show($id)
    {
        $session = PreviousSession::findOrFail($id);
        return view('admin.previous-sessions.show', compact('session'));
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
    
    public function clearOldRecords(Request $request)
    {
        try {
            $days = $request->get('days', 30);
            $deleted = PreviousSession::where('watched_on', '<', now()->subDays($days))->delete();
            
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