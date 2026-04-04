<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Mail\PupilCreated;
use App\Models\Pupil;
use App\Models\Role;
use App\Models\School;
use App\Models\User;
use App\Models\UserSchoolRole;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;

class PupilController extends Controller
{
    // ------------------------------------------------------------------ INDEX
    public function index(Request $request): View
    {
        $query = Pupil::with(['school', 'teacher']);

        if ($search = $request->input('search')) {
            $query->where(fn ($q) => $q
                ->where('first_name', 'like', "%{$search}%")
                ->orWhere('last_name',  'like', "%{$search}%")
                ->orWhere('pupil_id',   'like', "%{$search}%")
            );
        }

        if ($request->filled('status')) {
            $query->where('is_active', $request->input('status') === 'active');
        }

        if ($request->filled('school_id')) {
            $query->where('school_id', $request->input('school_id'));
        }

        $pupils  = $query->orderBy('last_name')->orderBy('first_name')->paginate(20)->withQueryString();
        $schools = School::where('is_active', true)->select('id', 'name')->get();

        return view('superadmin.pupils.index', compact('pupils', 'schools'));
    }

    // ------------------------------------------------------------------ CREATE
    public function create(): View
    {
        $schools  = School::where('is_active', true)->select('id', 'name', 'school_years')->get();
        $teachers = $this->getTeachersData();

        [$schoolYears, $teachersJson] = $this->buildDropdownData($schools, $teachers);

        return view('superadmin.pupils.create', compact('schools', 'teachers', 'schoolYears', 'teachersJson'));
    }

    // ------------------------------------------------------------------ STORE
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'first_name'          => ['required', 'string', 'max:100'],
            'last_name'           => ['required', 'string', 'max:100'],
            'date_of_birth'       => ['required', 'date', 'before:today'],
            'school_id'           => ['required', 'exists:schools,id'],
            'year_group'          => ['required', 'string', 'max:30'],
            'class_name'          => ['required', 'string', 'max:30'],
            'teacher_id'          => ['nullable', 'exists:users,id'],
            'include_in_averages' => ['nullable', 'boolean'],
            'sen'                 => ['nullable', 'in:none,sen_support,ehc_plan'],
            'is_active'           => ['nullable', 'boolean'],
        ]);

        DB::transaction(function () use ($request) {
            $plainPin = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

            $pupil = Pupil::create([
                'school_id'           => $request->school_id,
                'first_name'          => $request->first_name,
                'last_name'           => $request->last_name,
                'date_of_birth'       => $request->date_of_birth,
                'year_group'          => $request->year_group,
                'class_name'          => $request->class_name,
                'teacher_id'          => $request->teacher_id,
                'include_in_averages' => $request->boolean('include_in_averages', true),
                'sen'                 => $request->input('sen', 'none'),
                'is_active'           => $request->boolean('is_active', true),
                'pin'                 => Hash::make($plainPin),
            ]);

            // Notify school admin and/or teacher
            $this->sendPupilCredentials($pupil, $plainPin, $request->school_id, $request->teacher_id);
        });

        return redirect()
            ->route('superadmin.pupils.index')
            ->with('success', 'Pupil created. Credentials sent to the school admin / teacher.');
    }

    // ------------------------------------------------------------------ EDIT
    public function edit(Pupil $pupil): View
    {
        $pupil->load(['school', 'teacher']);
        $schools  = School::where('is_active', true)->select('id', 'name', 'school_years')->get();
        $teachers = $this->getTeachersData();

        [$schoolYears, $teachersJson] = $this->buildDropdownData($schools, $teachers);

        return view('superadmin.pupils.edit', compact('pupil', 'schools', 'teachers', 'schoolYears', 'teachersJson'));
    }

    // ------------------------------------------------------------------ UPDATE
    public function update(Request $request, Pupil $pupil): RedirectResponse
    {
        $request->validate([
            'first_name'          => ['required', 'string', 'max:100'],
            'last_name'           => ['required', 'string', 'max:100'],
            'date_of_birth'       => ['required', 'date', 'before:today'],
            'school_id'           => ['required', 'exists:schools,id'],
            'year_group'          => ['required', 'string', 'max:30'],
            'class_name'          => ['required', 'string', 'max:30'],
            'teacher_id'          => ['nullable', 'exists:users,id'],
            'include_in_averages' => ['nullable', 'boolean'],
            'sen'                 => ['nullable', 'in:none,sen_support,ehc_plan'],
            'is_active'           => ['nullable', 'boolean'],
        ]);

        $pupil->update([
            'first_name'          => $request->first_name,
            'last_name'           => $request->last_name,
            'date_of_birth'       => $request->date_of_birth,
            'school_id'           => $request->school_id,
            'year_group'          => $request->year_group,
            'class_name'          => $request->class_name,
            'teacher_id'          => $request->teacher_id,
            'include_in_averages' => $request->boolean('include_in_averages', true),
            'sen'                 => $request->input('sen', 'none'),
            'is_active'           => $request->boolean('is_active', true),
        ]);

        return back()->with('success', 'Pupil updated successfully.');
    }

    // ------------------------------------------------------------------ TOGGLE STATUS
    public function toggleStatus(Pupil $pupil): RedirectResponse
    {
        $pupil->update(['is_active' => ! $pupil->is_active]);
        $status = $pupil->is_active ? 'activated' : 'deactivated';
        return back()->with('success', "Pupil {$status}.");
    }

    // ------------------------------------------------------------------ DESTROY
    public function destroy(Pupil $pupil): RedirectResponse
    {
        $pupil->delete();
        return redirect()
            ->route('superadmin.pupils.index')
            ->with('success', 'Pupil record deleted.');
    }

    // ------------------------------------------------------------------ EXPORT CSV
    public function exportCsv(Request $request): StreamedResponse
    {
        $pupils = Pupil::with(['school', 'teacher'])
            ->when($request->filled('school_id'), fn ($q) => $q->where('school_id', $request->school_id))
            ->orderBy('last_name')->orderBy('first_name')
            ->get();

        $headers = [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => 'attachment; filename="pupils-' . now()->format('Ymd') . '.csv"',
        ];

        $rows = collect([['Pupil ID', 'Last Name', 'First Name', 'Date of Birth', 'Age',
                          'School', 'Year', 'Class', 'Teacher', 'SEN', 'Incl. Averages', 'Status']]);

        $rows = $rows->merge($pupils->map(fn ($p) => [
            $p->pupil_id,
            $p->last_name,
            $p->first_name,
            $p->date_of_birth?->format('d/m/Y'),
            $p->age,
            $p->school?->name,
            $p->year_group,
            $p->class_name,
            $p->teacher?->name,
            $p->sen_label,
            $p->include_in_averages ? 'Yes' : 'No',
            $p->is_active ? 'Active' : 'Inactive',
        ]));

        $callback = function () use ($rows) {
            $fh = fopen('php://output', 'w');
            foreach ($rows as $row) {
                fputcsv($fh, $row);
            }
            fclose($fh);
        };

        return response()->stream($callback, 200, $headers);
    }

    // ------------------------------------------------------------------ PRINT / PDF VIEW
    public function print(Request $request): View
    {
        $pupils = Pupil::with(['school', 'teacher'])
            ->when($request->filled('school_id'), fn ($q) => $q->where('school_id', $request->school_id))
            ->orderBy('last_name')->orderBy('first_name')
            ->get();

        $school = $request->filled('school_id')
            ? School::find($request->school_id)
            : null;

        return view('superadmin.pupils.print', compact('pupils', 'school'));
    }

    // ------------------------------------------------------------------ Helpers
    private function buildDropdownData($schools, $teachers): array
    {
        $schoolYears = $schools->keyBy('id')->map(fn ($s) => $s->school_years ?? []);

        $teachersJson = $teachers->map(fn ($t) => [
            'id'          => $t->id,
            'name'        => $t->name,
            'assignments' => $t->teacherAssignments->map(fn ($a) => [
                'school_id'   => $a->school_id,
                'school_year' => $a->school_year,
                'class_name'  => $a->class_name,
            ])->values(),
        ])->values();

        return [$schoolYears, $teachersJson];
    }

    private function getTeachersData(): \Illuminate\Database\Eloquent\Collection
    {
        $teacherRoleId = Role::where('slug', 'teacher')->value('id');

        return User::whereHas('schools', fn ($q) => $q->where('role_id', $teacherRoleId))
                   ->with(['teacherAssignments'])
                   ->select('id', 'name')
                   ->get();
    }

    private function sendPupilCredentials(Pupil $pupil, string $plainPin, int $schoolId, ?int $teacherId): void
    {
        $schoolAdminRoleId = Role::where('slug', 'school_admin')->value('id');

        // Get school admin
        $schoolAdmin = UserSchoolRole::where('school_id', $schoolId)
                                     ->where('role_id', $schoolAdminRoleId)
                                     ->where('is_active', true)
                                     ->first()?->user;

        // Get assigned teacher
        $teacher = $teacherId ? User::find($teacherId) : null;

        $recipients = collect([$schoolAdmin, $teacher])->filter()->unique('id');

        foreach ($recipients as $recipient) {
            Mail::to($recipient->email)->send(new PupilCreated($pupil, $plainPin, $recipient));
        }
    }
}
