<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        // Check if already logged in as admin
        if (Auth::guard('admin')->check()) {
            return redirect()->route('admin.dashboard');
        }
        
        // Set admin session configuration before any session operations
        $this->setAdminSessionConfig();
        
        return view('admin.login');
    }

    public function login(Request $request)
    {
        // Set admin session configuration
        $this->setAdminSessionConfig();

        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string'
        ]);

        // First check if admin exists and is active
        $admin = \App\Models\Admin::where('username', $request->username)->first();
        
        if (!$admin) {
            return back()->withErrors(['username' => 'Invalid credentials.']);
        }
        
        // Check if admin is active
        if (!$admin->is_active) {
            return back()->withErrors(['username' => 'Your account is inactive. Please contact administrator.']);
        }

        $credentials = [
            'username' => $request->username,
            'password' => $request->password,
            'is_active' => true
        ];

        if (Auth::guard('admin')->attempt($credentials, $request->remember)) {
            $request->session()->regenerate();
            return redirect()->route('admin.dashboard');
        }

        return back()->withErrors(['username' => 'Invalid credentials or account inactive.']);
    }

    public function logout(Request $request)
    {
        // Set admin session configuration
        $this->setAdminSessionConfig();
        
        Auth::guard('admin')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('admin.login');
    }

    /**
     * Set admin-specific session configuration
     */
    private function setAdminSessionConfig()
    {
        // Temporarily change session config for admin
        Config::set('session.driver', config('session_admin.driver', 'file'));
        Config::set('session.cookie', config('session_admin.cookie', 'admin_session'));
        Config::set('session.files', config('session_admin.files', storage_path('framework/sessions/admin')));
        Config::set('session.path', config('session_admin.path', '/'));
        Config::set('session.domain', config('session_admin.domain', null));
        Config::set('session.secure', config('session_admin.secure', false));
        Config::set('session.http_only', config('session_admin.http_only', true));
        Config::set('session.same_site', config('session_admin.same_site', 'lax'));
    }
}