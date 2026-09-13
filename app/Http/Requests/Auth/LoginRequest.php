<?php

namespace App\Http\Requests\Auth;

use App\Models\User;
use Illuminate\Auth\Events\Lockout;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class LoginRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'email_id' => ['required', 'string', 'email'],
            'password' => ['nullable', 'string'],
            'city' => ['nullable', 'string', 'max:255'],
        ];
    }

    /**
     * Attempt to authenticate the request's credentials.
     *
     * Returns 'password_not_set' when the account exists but has never
     * created a password yet, so the controller can send them to the
     * create-password flow instead of a generic "invalid credentials" error.
     */
    public function authenticate(): array
    {
        $this->ensureIsNotRateLimited();

        $user = User::where('email_id', $this->email_id)->first();

        if (!$user) {
            RateLimiter::hit($this->throttleKey());
            throw ValidationException::withMessages([
                'email_id' => 'No account found with this email address.',
            ]);
        }

        if (empty($user->password)) {
            return ['status' => 'password_not_set', 'user' => $user];
        }

        if (!Auth::attempt(['email_id' => $this->email_id, 'password' => $this->password], true)) {
            RateLimiter::hit($this->throttleKey());
            throw ValidationException::withMessages([
                'password' => 'The provided credentials do not match our records.',
            ]);
        }

        RateLimiter::clear($this->throttleKey());
        $this->session()->regenerate();

        return ['status' => 'authenticated', 'user' => $user];
    }

    public function ensureIsNotRateLimited(): void
    {
        if (!RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
            return;
        }

        event(new Lockout($this));
        $seconds = RateLimiter::availableIn($this->throttleKey());

        throw ValidationException::withMessages([
            'email_id' => trans('auth.throttle', [
                'seconds' => $seconds,
                'minutes' => ceil($seconds / 60),
            ]),
        ]);
    }

    public function throttleKey(): string
    {
        return Str::transliterate(Str::lower($this->string('email_id')) . '|' . $this->ip());
    }
}
