<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\TeacherAssignment;
use App\Models\UserSchoolRole;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(Request $request): View
    {
        $user = Auth::guard('school')->user();

        // Collect every school where this user is an active teacher
        $schools = UserSchoolRole::with('school', 'role')
            ->where('user_id', $user->id)
            ->where('is_active', true)
            ->whereHas('role', fn ($q) => $q->where('slug', 'teacher'))
            ->get()
            ->pluck('school');

        // All class assignments
        $assignments = TeacherAssignment::with('school')
            ->where('user_id', $user->id)
            ->get();

        return view('teacher.dashboard', compact('user', 'schools', 'assignments'));
    }
}
