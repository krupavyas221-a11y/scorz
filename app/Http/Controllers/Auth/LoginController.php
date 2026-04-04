<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\UserSchoolRole;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class LoginController extends Controller
{
    // ── Step 1: Role selection ────────────────────────────────────────────────

    public function showRoleSelect(): View
    {
        return view('auth.login.role');
    }

    public function selectRole(Request $request): RedirectResponse
    {
        $request->validate([
            'role' => ['required', 'in:school_admin,teacher'],
        ]);

        $request->session()->put('login.role', $request->role);

        return redirect()->route('login.credentials');
    }

    // ── Step 2: Email + Password ──────────────────────────────────────────────

    public function showCredentials(Request $request): View|RedirectResponse
    {
        if (! $request->session()->has('login.role')) {
            return redirect()->route('login');
        }

        return view('auth.login.credentials', [
            'role' => $request->session()->get('login.role'),
        ]);
    }

    public function submitCredentials(Request $request): RedirectResponse
    {
        if (! $request->session()->has('login.role')) {
            return redirect()->route('login');
        }

        $request->validate([
            'email'    => ['required', 'email'],
            'password' => ['required'],
        ]);

        $role = $request->session()->get('login.role');

        // Find user by email
        $user = User::where('email', $request->email)
                    ->where('is_active', true)
                    ->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            return back()
                ->withInput($request->only('email'))
                ->withErrors(['email' => 'These credentials do not match our records.']);
        }

        // Verify the user actually holds the selected role at an active school
        $hasRole = UserSchoolRole::where('user_id', $user->id)
                                 ->where('is_active', true)
                                 ->whereHas('role', fn ($q) => $q->where('slug', $role))
                                 ->exists();

        if (! $hasRole) {
            $roleLabel = $role === 'school_admin' ? 'School Admin' : 'Teacher';
            return back()
                ->withInput($request->only('email'))
                ->withErrors(['email' => "You do not have {$roleLabel} access."]);
        }

        // Credentials valid — store pending auth state and move to PIN step
        $request->session()->put('login.user_id', $user->id);

        return redirect()->route('login.pin');
    }

    // ── Step 3: PIN verification ──────────────────────────────────────────────

    public function showPin(Request $request): View|RedirectResponse
    {
        if (! $request->session()->has('login.user_id')) {
            return redirect()->route('login');
        }

        return view('auth.login.pin');
    }

    public function submitPin(Request $request): RedirectResponse
    {
        if (! $request->session()->has('login.user_id')) {
            return redirect()->route('login');
        }

        $request->validate([
            'pin' => ['required', 'digits:6'],
        ]);

        $userId = $request->session()->get('login.user_id');
        $role   = $request->session()->get('login.role');

        $user = User::find($userId);

        if (! $user || ! $user->pin || ! Hash::check($request->pin, $user->pin->pin)) {
            return back()->withErrors(['pin' => 'The PIN you entered is incorrect.']);
        }

        // Clear the multi-step session keys
        $request->session()->forget(['login.user_id', 'login.role']);

        // Complete authentication via the school guard
        Auth::guard('school')->loginUsingId($userId);
        $request->session()->regenerate();

        // Store the active role in session for dashboard context
        $request->session()->put('auth.role', $role);

        return $role === 'school_admin'
            ? redirect()->route('school-admin.dashboard')
            : redirect()->route('teacher.dashboard');
    }

    // ── Logout ────────────────────────────────────────────────────────────────

    public function logout(Request $request): RedirectResponse
    {
        Auth::guard('school')->logout();
        $request->session()->forget('auth.role');
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
