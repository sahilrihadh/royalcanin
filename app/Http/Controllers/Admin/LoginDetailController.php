<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LoginDetails;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LoginDetailController extends Controller
{
    public function index(Request $request)
    {
        $startDate = $request->get('start_date', now()->format('Y-m-d'));
        $endDate = $request->get('end_date', now()->format('Y-m-d'));
        
        $loginDetails = LoginDetails::with('user')
            ->when($startDate, function($query) use ($startDate, $endDate) {
                return $query->whereDate('login_time', '>=', $startDate)
                             ->whereDate('login_time', '<=', $endDate);
            })
            ->orderBy('login_time', 'desc')
            ->paginate(20);
        
        // Summary statistics
        $totalLogins = LoginDetails::whereBetween('login_time', [$startDate, $endDate])->count();
        $uniqueUsers = LoginDetails::whereBetween('login_time', [$startDate, $endDate])
            ->distinct('user_id')
            ->count('user_id');
        
        $averageDuration = LoginDetails::whereBetween('login_time', [$startDate, $endDate])
            ->whereNotNull('logout_time')
            ->get()
            ->avg(function($login) {
                return $login->login_time->diffInMinutes($login->logout_time);
            });
        
        // Peak login hours
        $peakHours = LoginDetails::whereBetween('login_time', [$startDate, $endDate])
            ->select(DB::raw('HOUR(login_time) as hour'), DB::raw('count(*) as count'))
            ->groupBy('hour')
            ->orderBy('count', 'desc')
            ->first();
        
        return view('admin.login-details.index', compact(
            'loginDetails', 
            'startDate', 
            'endDate', 
            'totalLogins', 
            'uniqueUsers', 
            'averageDuration',
            'peakHours'
        ));
    }
    
    public function show($id)
    {
        $loginDetail = LoginDetails::with('user')->findOrFail($id);
        
        // Calculate additional stats
        $previousLogins = LoginDetails::where('user_id', $loginDetail->user_id)
            ->where('id', '!=', $id)
            ->orderBy('login_time', 'desc')
            ->limit(5)
            ->get();
        
        return view('admin.login-details.show', compact('loginDetail', 'previousLogins'));
    }
    
    public function export(Request $request)
    {
        $startDate = $request->get('start_date', now()->format('Y-m-d'));
        $endDate = $request->get('end_date', now()->format('Y-m-d'));
        
        $loginDetails = LoginDetails::with('user')
            ->whereBetween('login_time', [$startDate, $endDate])
            ->orderBy('login_time', 'desc')
            ->get();
        
        $filename = "login_details_{$startDate}_to_{$endDate}.csv";
        
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        
        $output = fopen('php://output', 'w');
        fputcsv($output, ['User', 'Email', 'Login Time', 'Logout Time', 'Duration (minutes)']);
        
        foreach ($loginDetails as $detail) {
            $duration = $detail->login_time && $detail->logout_time 
                ? $detail->login_time->diffInMinutes($detail->logout_time) 
                : 0;
            
            fputcsv($output, [
                $detail->user->full_name ?? 'Unknown',
                $detail->user->email_id ?? 'Unknown',
                $detail->login_time,
                $detail->logout_time ?? 'Still Active',
                $duration
            ]);
        }
        
        fclose($output);
        exit();
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
            
            $deleted = LoginDetails::whereIn('id', $ids)->delete();
            
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