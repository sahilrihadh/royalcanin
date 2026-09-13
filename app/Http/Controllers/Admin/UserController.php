<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Exports\UsersExport;
use Maatwebsite\Excel\Facades\Excel;

class UserController extends Controller
{
    public function index(Request $request)
    {
        // Get online users (last 5 minutes activity)
        $onlineThreshold = Carbon::now()->subMinutes(5);
        $onlineUsers = User::where('last_seen_at', '>=', $onlineThreshold)->count();

        // Get total users
        $totalUsers = User::count();

        $search = trim((string) $request->query('search', ''));
        $status = $request->query('status', 'all');
        $sort = $request->query('sort', 'newest');
        $perPage = (int) $request->query('per_page', 10);
        if (!in_array($perPage, [10, 20, 50], true)) {
            $perPage = 10;
        }

        $query = User::query();

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('full_name', 'like', "%{$search}%")
                    ->orWhere('email_id', 'like', "%{$search}%")
                    ->orWhere('mobile_number', 'like', "%{$search}%")
                    ->orWhere('clinic_name', 'like', "%{$search}%");
            });
        }

        if ($status === 'online') {
            $query->where('last_seen_at', '>=', $onlineThreshold);
        } elseif ($status === 'offline') {
            $query->where(function ($q) use ($onlineThreshold) {
                $q->whereNull('last_seen_at')->orWhere('last_seen_at', '<', $onlineThreshold);
            });
        }

        switch ($sort) {
            case 'oldest':
                $query->orderBy('created_at', 'asc');
                break;
            case 'name_asc':
                $query->orderBy('full_name', 'asc');
                break;
            case 'name_desc':
                $query->orderBy('full_name', 'desc');
                break;
            default:
                $query->orderBy('created_at', 'desc');
        }

        $users = $query->paginate($perPage)->withQueryString();

        if ($request->ajax()) {
            return response()->json($users);
        }

        return view('admin.users.index', compact('users', 'onlineUsers', 'totalUsers', 'search', 'status', 'sort', 'perPage'));
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

    public function export()
{
    $users = User::all(); // Or use your filtered query
    return Excel::download(new UsersExport($users), 'users_export_' . date('Y-m-d_H-i') . '.xlsx');
}
}
