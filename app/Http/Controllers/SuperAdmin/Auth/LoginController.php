<?php

namespace App\Http\Controllers\SuperAdmin\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class LoginController extends Controller
{
    public function show(): View
    {
        return view('superadmin.auth.login');
    }

    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email'    => ['required', 'email'],
            'password' => ['required'],
        ]);

        // Attempt login via superadmin guard
        if (! Auth::guard('superadmin')->attempt($credentials, $request->boolean('remember'))) {
            return back()
                ->withInput($request->only('email'))
                ->withErrors(['email' => 'These credentials do not match our records.']);
        }

        $user = Auth::guard('superadmin')->user();

        // Verify the user actually has the super_admin role
        if (! $user->isSuperAdmin()) {
            Auth::guard('superadmin')->logout();
            return back()
                ->withInput($request->only('email'))
                ->withErrors(['email' => 'You do not have Super Admin access.']);
        }

        $request->session()->regenerate();

        return redirect()->route('superadmin.dashboard');
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::guard('superadmin')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('superadmin.login');
    }
}
