<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Mail\TeacherCredentials;
use App\Models\Role;
use App\Models\School;
use App\Models\SchoolClass;
use App\Models\TeacherAssignment;
use App\Models\User;
use App\Models\UserPin;
use App\Models\UserSchoolRole;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\View\View;

class TeacherController extends Controller
{
    // ------------------------------------------------------------------ INDEX
    public function index(Request $request): View
    {
        $teacherRoleId = Role::where('slug', 'teacher')->value('id');

        $query = User::query()
            ->whereHas('schools', fn ($q) => $q->where('role_id', $teacherRoleId))
            ->with([
                'teacherAssignments.school',
                'schools' => fn ($q) => $q->wherePivot('role_id', $teacherRoleId),
            ]);

        if ($search = $request->input('search')) {
            $query->where(fn ($q) => $q
                ->where('name', 'like', "%{$search}%")
                ->orWhere('email', 'like', "%{$search}%")
            );
        }

        if ($request->filled('status')) {
            $query->where('is_active', $request->input('status') === 'active');
        }

        $teachers = $query->latest()->paginate(15)->withQueryString();

        return view('superadmin.teachers.index', compact('teachers'));
    }

    // ------------------------------------------------------------------ CREATE
    public function create(): View
    {
        $schools = School::where('is_active', true)
                         ->select('id', 'name', 'school_years')
                         ->get();

        return view('superadmin.teachers.create', compact('schools'));
    }

    // ------------------------------------------------------------------ STORE
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name'         => ['required', 'string', 'max:255'],
            'email'        => ['required', 'email', 'max:191', 'unique:users,email'],
            'scorz_admin'  => ['nullable', 'boolean'],
            'scorz_access' => ['nullable', 'boolean'],
            'school_id'   => ['required', 'exists:schools,id'],
            'school_year' => ['required', 'string', 'max:30'],
            'class_id'    => ['required', 'exists:classes,id'],
        ]);

        DB::transaction(function () use ($request) {
            $school        = School::findOrFail($request->school_id);
            $schoolClass   = SchoolClass::findOrFail($request->class_id);
            $plainPassword = Str::password(10, symbols: false);
            $plainPin      = str_pad(random_int(0, 99999), 5, '0', STR_PAD_LEFT);

            // 1. Create user
            $user = User::create([
                'name'         => $request->name,
                'email'        => $request->email,
                'password'     => Hash::make($plainPassword),
                'is_active'    => true,
                'scorz_admin'  => $request->boolean('scorz_admin'),
                'scorz_access' => $request->boolean('scorz_access', true),
            ]);

            // 2. PIN
            UserPin::create([
                'user_id' => $user->id,
                'pin'     => Hash::make($plainPin),
            ]);

            // 3. Assign teacher role at this school
            $roleId = Role::where('slug', 'teacher')->value('id');
            UserSchoolRole::create([
                'user_id'     => $user->id,
                'school_id'   => $school->id,
                'role_id'     => $roleId,
                'is_active'   => true,
                'assigned_at' => now(),
            ]);

            // 4. Teacher assignment (class / year)
            TeacherAssignment::create([
                'user_id'     => $user->id,
                'school_id'   => $school->id,
                'school_year' => $request->school_year,
                'class_name'  => $schoolClass->name,
                'class_id'    => $schoolClass->id,
            ]);

            // 5. Send credentials email
            $user->load('teacherAssignments');
            Mail::to($user->email)->send(
                new TeacherCredentials($user, $school, $plainPassword, $plainPin)
            );
        });

        return redirect()
            ->route('superadmin.teachers.index')
            ->with('success', 'Teacher account created. Credentials sent via email.');
    }

    // ------------------------------------------------------------------ EDIT
    public function edit(User $teacher): View
    {
        $teacherRoleId = Role::where('slug', 'teacher')->value('id');

        abort_unless(
            UserSchoolRole::where('user_id', $teacher->id)
                          ->where('role_id', $teacherRoleId)
                          ->exists(),
            404
        );

        $teacher->load(['teacherAssignments.school', 'schools']);
        $schools = School::where('is_active', true)->select('id', 'name', 'school_years')->get();

        return view('superadmin.teachers.edit', compact('teacher', 'schools'));
    }

    // ------------------------------------------------------------------ UPDATE
    public function update(Request $request, User $teacher): RedirectResponse
    {
        $request->validate([
            'name'         => ['required', 'string', 'max:255'],
            'scorz_admin'  => ['nullable', 'boolean'],
            'scorz_access' => ['nullable', 'boolean'],
            'school_id'   => ['required', 'exists:schools,id'],
            'school_year' => ['required', 'string', 'max:30'],
            'class_id'    => ['required', 'exists:classes,id'],
        ]);

        DB::transaction(function () use ($request, $teacher) {
            $schoolClass = SchoolClass::findOrFail($request->class_id);

            $teacher->update([
                'name'         => $request->name,
                'scorz_admin'  => $request->boolean('scorz_admin'),
                'scorz_access' => $request->boolean('scorz_access', true),
            ]);

            // Update teacher assignment (replace all with the new one)
            $teacher->teacherAssignments()->delete();
            TeacherAssignment::create([
                'user_id'     => $teacher->id,
                'school_id'   => $request->school_id,
                'school_year' => $request->school_year,
                'class_name'  => $schoolClass->name,
                'class_id'    => $schoolClass->id,
            ]);

            // Ensure school role is correct
            $roleId    = Role::where('slug', 'teacher')->value('id');
            $oldRoles  = UserSchoolRole::where('user_id', $teacher->id)
                                        ->where('role_id', $roleId);
            $oldRoles->delete();

            UserSchoolRole::create([
                'user_id'     => $teacher->id,
                'school_id'   => $request->school_id,
                'role_id'     => $roleId,
                'is_active'   => true,
                'assigned_at' => now(),
            ]);
        });

        return back()->with('success', 'Teacher updated successfully.');
    }

    // ------------------------------------------------------------------ TOGGLE STATUS
    public function toggleStatus(User $teacher): RedirectResponse
    {
        $teacher->update(['is_active' => ! $teacher->is_active]);

        $roleId = Role::where('slug', 'teacher')->value('id');
        UserSchoolRole::where('user_id', $teacher->id)
                      ->where('role_id', $roleId)
                      ->update(['is_active' => $teacher->is_active]);

        $status = $teacher->is_active ? 'activated' : 'deactivated';
        return back()->with('success', "Teacher account {$status}.");
    }

    // ------------------------------------------------------------------ DESTROY
    public function destroy(User $teacher): RedirectResponse
    {
        DB::transaction(function () use ($teacher) {
            $teacher->teacherAssignments()->delete();
            $teacher->pin()->delete();
            UserSchoolRole::where('user_id', $teacher->id)->delete();
            $teacher->delete();
        });

        return redirect()
            ->route('superadmin.teachers.index')
            ->with('success', 'Teacher account deleted.');
    }
}
