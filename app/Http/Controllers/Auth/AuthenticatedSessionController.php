<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use App\Models\LoginDetails;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse|JsonResponse
    {
        $request->authenticate();
        $request->session()->regenerate();

        // Get user after authentication
        $user = Auth::user();

        // Track login
        LoginDetail::create([
            'user_id' => $user->id,
            'login_time' => now(),
            'logout_time' => null
        ]);

        // Check if request expects JSON response (AJAX request)
        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Login successful!',
                'redirect_url' => route('webcast'),
                'user' => Auth::user()->email_id
            ]);
        }

        // For regular form submissions
        return redirect()->intended(route('webcast'));
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        // Get user BEFORE logging out
        $user = Auth::user();

        if ($user) {
            // Update active session with logout time
            $activeSession = LoginDetail::where('user_id', $user->id)
                ->whereNull('logout_time')
                ->first();
                
            if ($activeSession) {
                $activeSession->update([
                    'logout_time' => now()
                ]);
            }
        }

        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}