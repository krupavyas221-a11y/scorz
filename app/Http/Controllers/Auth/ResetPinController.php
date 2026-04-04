<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\UserPin;
use App\Notifications\PinResetNotification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\View\View;

class ResetPinController extends Controller
{
    // Token expiry in minutes
    private const EXPIRE_MINUTES = 60;

    // ── Show request form (enter email) ───────────────────────────────────────

    public function showRequest(): View
    {
        return view('auth.reset-pin.request');
    }

    // ── Send PIN reset link ───────────────────────────────────────────────────

    public function sendLink(Request $request): RedirectResponse
    {
        $request->validate([
            'email' => ['required', 'email'],
        ]);

        $user = User::where('email', $request->email)->where('is_active', true)->first();

        // Respond the same way whether or not the email exists (prevents enumeration)
        if ($user) {
            // Throttle: block if a token was issued less than 60 seconds ago
            $existing = DB::table('pin_reset_tokens')
                          ->where('email', $user->email)
                          ->first();

            $throttle = 60; // seconds
            if ($existing && now()->diffInSeconds($existing->created_at) < $throttle) {
                return back()
                    ->withInput($request->only('email'))
                    ->withErrors(['email' => 'Please wait before requesting another PIN reset link.']);
            }

            $token = Str::random(64);

            DB::table('pin_reset_tokens')->updateOrInsert(
                ['email' => $user->email],
                [
                    'token'      => Hash::make($token),
                    'created_at' => now(),
                ]
            );

            $user->notify(new PinResetNotification($token));
        }

        return back()->with('status', 'If that email is registered, a PIN reset link has been sent.');
    }

    // ── Show new PIN form ─────────────────────────────────────────────────────

    public function showForm(Request $request, string $token): View|RedirectResponse
    {
        $email = $request->query('email', '');

        if (! $email || ! $this->tokenIsValid($email, $token)) {
            return redirect()->route('pin.reset.request')
                             ->withErrors(['email' => 'This PIN reset link is invalid or has expired.']);
        }

        return view('auth.reset-pin.form', compact('token', 'email'));
    }

    // ── Save new PIN ──────────────────────────────────────────────────────────

    public function update(Request $request): RedirectResponse
    {
        $request->validate([
            'token' => ['required'],
            'email' => ['required', 'email'],
            'pin'   => ['required', 'digits:6', 'confirmed'],
        ]);

        if (! $this->tokenIsValid($request->email, $request->token)) {
            return back()
                ->withInput($request->only('email'))
                ->withErrors(['email' => 'This PIN reset link is invalid or has expired.']);
        }

        $user = User::where('email', $request->email)->where('is_active', true)->first();

        if (! $user) {
            return back()->withErrors(['email' => 'No account found for that email address.']);
        }

        // Update or create PIN record
        UserPin::updateOrCreate(
            ['user_id' => $user->id],
            ['pin'     => $request->pin]   // cast 'hashed' handles bcrypt
        );

        // Delete the used token
        DB::table('pin_reset_tokens')->where('email', $request->email)->delete();

        return redirect()->route('login')
                         ->with('status', 'Your PIN has been reset. Please log in.');
    }

    // ── Helper ────────────────────────────────────────────────────────────────

    private function tokenIsValid(string $email, string $token): bool
    {
        $record = DB::table('pin_reset_tokens')->where('email', $email)->first();

        if (! $record) {
            return false;
        }

        // Check expiry
        if (now()->diffInMinutes($record->created_at) > self::EXPIRE_MINUTES) {
            return false;
        }

        return Hash::check($token, $record->token);
    }
}
