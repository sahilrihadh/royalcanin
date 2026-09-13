<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\OtpMail;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class PasswordController extends Controller
{
    private const OTP_TTL_MINUTES = 10;
    private const MAX_OTP_ATTEMPTS = 5;

    /**
     * Display the create-password view.
     */
    public function create(Request $request): View
    {
        return view('auth.create-password', [
            'email' => $request->query('email'),
        ]);
    }

    /**
     * Generate an OTP and email it to the given account's address.
     */
    public function sendOtp(Request $request): JsonResponse
    {
        $request->validate([
            'email_id' => ['required', 'string', 'email', 'exists:users,email_id'],
        ]);

        $email = strtolower($request->email_id);
        $otp = str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        Cache::put("password_otp:{$email}", [
            'otp' => Hash::make($otp),
            'attempts' => 0,
        ], now()->addMinutes(self::OTP_TTL_MINUTES));

        try {
            Mail::to($email)->send(new OtpMail($otp));
        } catch (\Exception $e) {
            \Log::error('OTP email sending failed: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Could not send OTP right now. Please try again in a moment.',
            ], 500);
        }

        return response()->json([
            'success' => true,
            'message' => 'An OTP has been sent to your email address.',
        ]);
    }

    /**
     * Verify the OTP entered for the given email.
     */
    public function verifyOtp(Request $request): JsonResponse
    {
        $request->validate([
            'email_id' => ['required', 'string', 'email'],
            'otp' => ['required', 'digits:6'],
        ]);

        $email = strtolower($request->email_id);
        $record = Cache::get("password_otp:{$email}");

        if (!$record) {
            return response()->json([
                'success' => false,
                'message' => 'This OTP has expired. Please request a new one.',
            ], 422);
        }

        if ($record['attempts'] >= self::MAX_OTP_ATTEMPTS) {
            Cache::forget("password_otp:{$email}");

            return response()->json([
                'success' => false,
                'message' => 'Too many incorrect attempts. Please request a new OTP.',
            ], 422);
        }

        if (!Hash::check($request->otp, $record['otp'])) {
            $record['attempts']++;
            Cache::put("password_otp:{$email}", $record, now()->addMinutes(self::OTP_TTL_MINUTES));

            return response()->json([
                'success' => false,
                'message' => 'The OTP you entered is incorrect.',
            ], 422);
        }

        Cache::forget("password_otp:{$email}");
        Cache::put("password_otp_verified:{$email}", true, now()->addMinutes(self::OTP_TTL_MINUTES));

        return response()->json([
            'success' => true,
            'message' => 'OTP verified successfully.',
        ]);
    }

    /**
     * Set the account's password once its OTP has been verified.
     */
    public function setPassword(Request $request): JsonResponse
    {
        $request->validate([
            'email_id' => ['required', 'string', 'email', 'exists:users,email_id'],
            'password' => ['required', 'confirmed', Password::min(8)->mixedCase()->numbers()->symbols()],
        ]);

        $email = strtolower($request->email_id);

        if (!Cache::get("password_otp_verified:{$email}")) {
            return response()->json([
                'success' => false,
                'message' => 'Please verify the OTP sent to your email before setting a password.',
            ], 422);
        }

        $user = User::where('email_id', $email)->first();
        $user->password = Hash::make($request->password);
        $user->save();

        Cache::forget("password_otp_verified:{$email}");

        return response()->json([
            'success' => true,
            'message' => 'Password created successfully!',
            'redirect_url' => route('login'),
        ]);
    }
}
