<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class AdminUserController extends Controller
{
    /**
     * Display a listing of admin users.
     */
    public function index()
    {
        $admins = Admin::orderBy('created_at', 'desc')->get();
        return view('admin.admins.index', compact('admins'));
    }

    /**
     * Show the form for creating a new admin user.
     */
    public function create()
    {
        return view('admin.admins.create');
    }

    /**
     * Store a newly created admin user.
     */
    public function store(Request $request)
    {
        $request->validate([
            'username' => 'required|string|max:255|unique:admins,username',
            'full_name' => 'required|string|max:255',
            'password' => 'required|string|min:6|confirmed',
            'user_role' => 'required|in:admin,editor,viewer',
            'is_active' => 'nullable|boolean'
        ]);

        Admin::create([
            'username' => $request->username,
            'full_name' => $request->full_name,
            'password' => Hash::make($request->password),
            'user_role' => $request->user_role,
            'is_active' => $request->has('is_active') ? 1 : 0
        ]);

        if ($request->ajax()) {
            return response()->json(['success' => true, 'message' => 'Admin user created successfully!']);
        }

        return redirect()->route('admin.admins.index')
            ->with('success', 'Admin user created successfully!');
    }

    /**
     * Show the form for editing the specified admin user.
     */
    public function edit($id)
    {
        $admin = Admin::findOrFail($id);
        return view('admin.admins.edit', compact('admin'));
    }

    /**
     * Update the specified admin user.
     */
    public function update(Request $request, $id)
    {
        $admin = Admin::findOrFail($id);

        $request->validate([
            'username' => 'required|string|max:255|unique:admins,username,' . $id,
            'full_name' => 'required|string|max:255',
            'password' => 'nullable|string|min:6|confirmed',
            'user_role' => 'required|in:admin,editor,viewer',
            'is_active' => 'nullable|boolean'
        ]);

        $data = [
            'username' => $request->username,
            'full_name' => $request->full_name,
            'user_role' => $request->user_role,
            'is_active' => $request->has('is_active') ? 1 : 0
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $admin->update($data);

        if ($request->ajax()) {
            return response()->json(['success' => true, 'message' => 'Admin user updated successfully!']);
        }

        return redirect()->route('admin.admins.index')
            ->with('success', 'Admin user updated successfully!');
    }

    /**
     * Remove the specified admin user.
     */
    public function destroy($id)
    {
        $admin = Admin::findOrFail($id);
        
        // Prevent deleting your own account
        if (auth()->guard('admin')->id() == $admin->id) {
            if (request()->ajax()) {
                return response()->json(['success' => false, 'message' => 'You cannot delete your own account!'], 403);
            }
            return redirect()->route('admin.admins.index')
                ->with('error', 'You cannot delete your own account!');
        }

        $admin->delete();

        if (request()->ajax()) {
            return response()->json(['success' => true, 'message' => 'Admin user deleted successfully!']);
        }

        return redirect()->route('admin.admins.index')
            ->with('success', 'Admin user deleted successfully!');
    }

    /**
     * Toggle user status (activate/deactivate)
     */
    public function toggleStatus($id)
    {
        $admin = Admin::findOrFail($id);
        
        // Prevent toggling your own status
        if (auth()->guard('admin')->id() == $admin->id) {
            return redirect()->route('admin.admins.index')
                ->with('error', 'You cannot change your own status!');
        }
        
        $admin->is_active = !$admin->is_active;
        $admin->save();

        $message = $admin->is_active ? 'activated' : 'deactivated';
        
        return redirect()->route('admin.admins.index')
            ->with('success', "Admin user {$message} successfully!");
    }
}