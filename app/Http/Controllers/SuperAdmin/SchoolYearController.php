<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\SchoolYear;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class SchoolYearController extends Controller
{
    // ------------------------------------------------------------------ INDEX
    public function index(Request $request): View
    {
        $query = SchoolYear::withCount('schools');

        if ($search = $request->input('search')) {
            $query->where('year', 'like', "%{$search}%");
        }

        if ($request->filled('status')) {
            $query->where('is_active', $request->input('status') === 'active');
        }

        $schoolYears = $query->latest()->paginate(15)->withQueryString();

        return view('superadmin.school-years.index', compact('schoolYears'));
    }

    // ------------------------------------------------------------------ CREATE
    public function create(): View
    {
        return view('superadmin.school-years.create');
    }

    // ------------------------------------------------------------------ STORE
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'year' => ['required', 'string', 'max:20', 'unique:school_years,year',
                       'regex:/^\d{4}[–\-]\d{4}$/'],
        ], [
            'year.regex' => 'The year format must be YYYY–YYYY (e.g., 2023–2024).',
        ]);

        SchoolYear::create([
            'year'      => $request->year,
            'is_active' => true,
        ]);

        return redirect()
            ->route('superadmin.school-years.index')
            ->with('success', "School year \"{$request->year}\" created successfully.");
    }

    // ------------------------------------------------------------------ EDIT
    public function edit(SchoolYear $schoolYear): View
    {
        $schoolYear->load('schools');

        return view('superadmin.school-years.edit', compact('schoolYear'));
    }

    // ------------------------------------------------------------------ UPDATE
    public function update(Request $request, SchoolYear $schoolYear): RedirectResponse
    {
        $request->validate([
            'year' => ['required', 'string', 'max:20',
                       Rule::unique('school_years', 'year')->ignore($schoolYear->id),
                       'regex:/^\d{4}[–\-]\d{4}$/'],
        ], [
            'year.regex' => 'The year format must be YYYY–YYYY (e.g., 2023–2024).',
        ]);

        $schoolYear->update(['year' => $request->year]);

        return back()->with('success', 'School year updated successfully.');
    }

    // ------------------------------------------------------------------ DESTROY
    public function destroy(SchoolYear $schoolYear): RedirectResponse
    {
        if ($schoolYear->schools()->exists()) {
            return back()->with('error', 'Cannot delete a school year that is assigned to schools.');
        }

        $schoolYear->delete();

        return redirect()
            ->route('superadmin.school-years.index')
            ->with('success', 'School year deleted successfully.');
    }

    // ------------------------------------------------------------------ TOGGLE STATUS
    public function toggleStatus(SchoolYear $schoolYear): RedirectResponse
    {
        $schoolYear->update(['is_active' => ! $schoolYear->is_active]);

        $status = $schoolYear->is_active ? 'activated' : 'deactivated';

        return back()->with('success', "School year \"{$schoolYear->year}\" {$status}.");
    }

    // ------------------------------------------------------------------ ASSIGN SCHOOLS
    public function assignSchools(Request $request, SchoolYear $schoolYear): RedirectResponse
    {
        $request->validate([
            'school_ids'   => ['nullable', 'array'],
            'school_ids.*' => ['integer', 'exists:schools,id'],
        ]);

        $schoolYear->schools()->sync($request->input('school_ids', []));

        return back()->with('success', 'Schools assigned to school year updated.');
    }
}
