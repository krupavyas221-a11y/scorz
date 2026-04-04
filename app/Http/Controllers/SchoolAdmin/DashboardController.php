<?php

namespace App\Http\Controllers\SchoolAdmin;

use App\Http\Controllers\Controller;
use App\Models\UserSchoolRole;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(Request $request): View
    {
        $user = Auth::guard('school')->user();

        // Collect every school where this user is an active admin
        $schools = UserSchoolRole::with('school', 'role')
            ->where('user_id', $user->id)
            ->where('is_active', true)
            ->whereHas('role', fn ($q) => $q->where('slug', 'school_admin'))
            ->get()
            ->pluck('school');

        return view('school-admin.dashboard', compact('user', 'schools'));
    }
}
