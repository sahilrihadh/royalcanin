<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Carbon\Carbon;

class UserController extends Controller
{
    public function index(Request $request)
    {
        // Get online users (last 5 minutes activity)
        $onlineThreshold = Carbon::now()->subMinutes(5);
        $onlineUsers = User::where('last_seen_at', '>=', $onlineThreshold)->count();

        // Get total users
        $totalUsers = User::count();

        // Get paginated users
        $users = User::orderBy('created_at', 'desc')
            ->paginate(20);

        if ($request->ajax()) {
            return response()->json($users);
        }

        return view('admin.users.index', compact('users', 'onlineUsers', 'totalUsers'));
    }

    public function destroy($id)
    {
        try {
            $user = User::findOrFail($id);

            // Check if user is online
            $onlineThreshold = Carbon::now()->subMinutes(5);
            if ($user->last_seen_at && $user->last_seen_at >= $onlineThreshold) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot delete online user'
                ], 400);
            }

            $user->delete();

            return response()->json([
                'success' => true,
                'message' => 'User deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete user'
            ], 500);
        }
    }
}
