<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\SchoolClass;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ClassController extends Controller
{
    // ------------------------------------------------------------------ INDEX
    public function index(Request $request): View
    {
        $query = SchoolClass::withCount([
            'teacherAssignments',
            'teacherAssignments as active_teachers_count' => fn ($q) =>
                $q->whereHas('user', fn ($u) => $u->where('is_active', true)),
        ]);

        if ($search = $request->input('search')) {
            $query->where('name', 'like', "%{$search}%");
        }

        if ($request->filled('status')) {
            $query->where('is_active', $request->input('status') === 'active');
        }

        $classes = $query->orderBy('name')->paginate(20)->withQueryString();

        return view('superadmin.classes.index', compact('classes'));
    }

    // ------------------------------------------------------------------ CREATE
    public function create(): View
    {
        return view('superadmin.classes.create');
    }

    // ------------------------------------------------------------------ STORE
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:60', 'unique:classes,name'],
        ]);

        SchoolClass::create([
            'name'      => $request->name,
            'is_active' => true,
        ]);

        return redirect()
            ->route('superadmin.classes.index')
            ->with('success', "Class \"{$request->name}\" created successfully.");
    }

    // ------------------------------------------------------------------ EDIT
    public function edit(SchoolClass $class): View
    {
        $class->load([
            'teacherAssignments' => fn ($q) => $q->with(['user', 'school'])
                                                   ->latest(),
        ]);

        return view('superadmin.classes.edit', compact('class'));
    }

    // ------------------------------------------------------------------ UPDATE
    public function update(Request $request, SchoolClass $class): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:60',
                       Rule::unique('classes', 'name')->ignore($class->id)],
        ]);

        $class->update(['name' => $request->name]);

        return back()->with('success', 'Class updated successfully.');
    }

    // ------------------------------------------------------------------ DESTROY
    public function destroy(SchoolClass $class): RedirectResponse
    {
        if ($class->teacherAssignments()->exists()) {
            return back()->with('error', 'Cannot delete a class that has teachers assigned to it.');
        }

        $class->delete();

        return redirect()
            ->route('superadmin.classes.index')
            ->with('success', 'Class deleted successfully.');
    }

    // ------------------------------------------------------------------ TOGGLE STATUS
    public function toggleStatus(SchoolClass $class): RedirectResponse
    {
        $class->update(['is_active' => ! $class->is_active]);

        $status = $class->is_active ? 'activated' : 'deactivated';

        return back()->with('success', "Class \"{$class->name}\" {$status}.");
    }
}
