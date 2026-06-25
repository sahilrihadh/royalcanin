<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use App\Models\LoginDetails; // Note: Changed from LoginDetail to LoginDetails

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
        // Log the incoming request data for debugging
        \Log::info('Login request data:', $request->all());
        
        $request->authenticate();
        $request->session()->regenerate();

        // Get user after authentication
        $user = Auth::user();

        // Get city from request - try different ways
        $city = $request->input('city') ?? $request->get('city') ?? null;
        
        // Log the city value
        \Log::info('City value:', ['city' => $city]);

        // Track login with city
        $loginDetail = LoginDetails::create([
            'user_id' => $user->id,
            'login_time' => now(),
            'logout_time' => null,
            'city' => $city // This should now work
        ]);

        // Log the created record
        \Log::info('Login record created:', $loginDetail->toArray());

        // Check if request expects JSON response (AJAX request)
        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Login successful!',
                'redirect_url' => route('webcast'),
                'user' => Auth::user()->email_id,
                'city' => $city
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
            $activeSession = LoginDetails::where('user_id', $user->id)
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