<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\PreviousSession;
use App\Models\Poll;
use App\Models\Admin;

class DashboardController extends Controller
{
    public function index()
    {
        $totalUsers = User::count();
        $totalWebinars = PreviousSession::count();
        $totalPolls = Poll::count();
        $totalCertificates = PreviousSession::where('certificate_status', 1)->count();

        $recentUsers = User::orderBy('created_at', 'desc')->limit(5)->get();

        // Fix: Remove the 'user' relationship since it doesn't exist
        $recentActivities = PreviousSession::orderBy('watched_on', 'desc')
            ->limit(5)
            ->get();

        return view('admin.dashboard', compact(
            'totalUsers',
            'totalWebinars',
            'totalPolls',
            'totalCertificates',
            'recentUsers',
            'recentActivities'
        ));
    }
}
