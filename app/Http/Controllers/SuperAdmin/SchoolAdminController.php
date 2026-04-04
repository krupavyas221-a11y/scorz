<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Mail\SchoolAdminCredentials;
use App\Models\Role;
use App\Models\School;
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

class SchoolAdminController extends Controller
{
    // ------------------------------------------------------------------ INDEX
    public function index(Request $request): View
    {
        $query = School::query()->with([
            'userSchoolRoles' => fn ($q) => $q
                ->where('role_id', Role::where('slug', 'school_admin')->value('id'))
                ->with('user'),
        ]);

        // Search by school name, admin name or admin email
        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('schools.name', 'like', "%{$search}%")
                  ->orWhereHas('userSchoolRoles.user', function ($q) use ($search) {
                      $q->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                  });
            });
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('is_active', $request->input('status') === 'active');
        }

        $schools = $query->latest()->paginate(15)->withQueryString();

        return view('superadmin.school-admins.index', compact('schools'));
    }

    // ------------------------------------------------------------------ CREATE
    public function create(): View
    {
        return view('superadmin.school-admins.create');
    }

    // ------------------------------------------------------------------ STORE
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            // School fields
            'school_name'             => ['required', 'string', 'max:255'],
            'school_type'             => ['required', 'string', 'max:50'],
            'address'                 => ['nullable', 'string'],
            'region'                  => ['nullable', 'string', 'max:100'],
            'gender'                  => ['nullable', 'in:girls,boys,mixed'],
            'phone'                   => ['nullable', 'string', 'max:30'],
            'fax'                     => ['nullable', 'string', 'max:30'],
            'principal_name'          => ['nullable', 'string', 'max:255'],
            'school_email'            => ['nullable', 'email', 'max:191'],
            'website'                 => ['nullable', 'url', 'max:255'],
            'teacher_council_number'  => ['nullable', 'string', 'max:50'],
            'school_years'            => ['nullable', 'array'],
            'school_years.*'          => ['string', 'max:30'],
            // Admin user fields
            'admin_name'              => ['required', 'string', 'max:255'],
            'admin_email'             => ['required', 'email', 'max:191', 'unique:users,email'],
        ]);

        DB::transaction(function () use ($request) {
            // 1. Create the school
            $school = School::create([
                'name'                   => $request->school_name,
                'address'                => $request->address,
                'school_type'            => $request->school_type,
                'region'                 => $request->region,
                'gender'                 => $request->gender,
                'phone'                  => $request->phone,
                'fax'                    => $request->fax,
                'principal_name'         => $request->principal_name,
                'email'                  => $request->school_email,
                'website'                => $request->website,
                'teacher_council_number' => $request->teacher_council_number,
                'school_years'           => $request->school_years ?? [],
                'is_active'              => true,
            ]);

            // 2. Generate credentials
            $plainPassword = Str::password(10, symbols: false);
            $plainPin      = str_pad(random_int(0, 99999), 5, '0', STR_PAD_LEFT);

            // 3. Create the admin user
            $user = User::create([
                'name'      => $request->admin_name,
                'email'     => $request->admin_email,
                'password'  => Hash::make($plainPassword),
                'is_active' => true,
            ]);

            // 4. Assign PIN
            UserPin::create([
                'user_id' => $user->id,
                'pin'     => Hash::make($plainPin),
            ]);

            // 5. Assign school_admin role at this school
            $roleId = Role::where('slug', 'school_admin')->value('id');
            UserSchoolRole::create([
                'user_id'     => $user->id,
                'school_id'   => $school->id,
                'role_id'     => $roleId,
                'is_active'   => true,
                'assigned_at' => now(),
            ]);

            // 6. Send credentials email
            Mail::to($user->email)->send(
                new SchoolAdminCredentials($user, $school, $plainPassword, $plainPin)
            );
        });

        return redirect()
            ->route('superadmin.school-admins.index')
            ->with('success', 'School admin account created. Credentials have been emailed.');
    }

    // ------------------------------------------------------------------ SHOW
    public function show(School $schoolAdmin): View
    {
        $schoolAdmin->load([
            'userSchoolRoles' => fn ($q) => $q
                ->where('role_id', Role::where('slug', 'school_admin')->value('id'))
                ->with('user'),
        ]);

        return view('superadmin.school-admins.show', ['school' => $schoolAdmin]);
    }

    // ------------------------------------------------------------------ UPDATE
    public function update(Request $request, School $schoolAdmin): RedirectResponse
    {
        $school = $schoolAdmin;

        $request->validate([
            'school_name'            => ['required', 'string', 'max:255'],
            'school_type'            => ['required', 'string', 'max:50'],
            'address'                => ['nullable', 'string'],
            'region'                 => ['nullable', 'string', 'max:100'],
            'gender'                 => ['nullable', 'in:girls,boys,mixed'],
            'phone'                  => ['nullable', 'string', 'max:30'],
            'fax'                    => ['nullable', 'string', 'max:30'],
            'principal_name'         => ['nullable', 'string', 'max:255'],
            'school_email'           => ['nullable', 'email', 'max:191'],
            'website'                => ['nullable', 'url', 'max:255'],
            'teacher_council_number' => ['nullable', 'string', 'max:50'],
            'school_years'           => ['nullable', 'array'],
            'school_years.*'         => ['string', 'max:30'],
            'admin_name'             => ['required', 'string', 'max:255'],
        ]);

        DB::transaction(function () use ($request, $school) {
            $school->update([
                'name'                   => $request->school_name,
                'address'                => $request->address,
                'school_type'            => $request->school_type,
                'region'                 => $request->region,
                'gender'                 => $request->gender,
                'phone'                  => $request->phone,
                'fax'                    => $request->fax,
                'principal_name'         => $request->principal_name,
                'email'                  => $request->school_email,
                'website'                => $request->website,
                'teacher_council_number' => $request->teacher_council_number,
                'school_years'           => $request->school_years ?? [],
            ]);

            // Update admin user name
            $roleId = Role::where('slug', 'school_admin')->value('id');
            $adminUser = UserSchoolRole::where('school_id', $school->id)
                                       ->where('role_id', $roleId)
                                       ->first()?->user;
            if ($adminUser) {
                $adminUser->update(['name' => $request->admin_name]);
            }
        });

        return back()->with('success', 'School admin details updated successfully.');
    }

    // ------------------------------------------------------------------ TOGGLE STATUS
    public function toggleStatus(School $schoolAdmin): RedirectResponse
    {
        $school = $schoolAdmin;
        $school->update(['is_active' => ! $school->is_active]);

        // Also toggle the admin user's is_active flag
        $roleId = Role::where('slug', 'school_admin')->value('id');
        UserSchoolRole::where('school_id', $school->id)
                      ->where('role_id', $roleId)
                      ->update(['is_active' => $school->is_active]);

        $status = $school->is_active ? 'activated' : 'deactivated';

        return back()->with('success', "School admin account {$status}.");
    }
}
