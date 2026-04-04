@extends('superadmin.layouts.app')

@section('title', 'Edit Teacher — '.$teacher->name)

@section('content')
<div class="page-header">
    <div class="page-header-left">
        <h1>Edit Teacher</h1>
        <p>
            {{ $teacher->name }}
            @if($teacher->is_active)
                <span class="badge badge-success" style="margin-left:.4rem">Active</span>
            @else
                <span class="badge badge-danger" style="margin-left:.4rem">Inactive</span>
            @endif
        </p>
    </div>
    <div style="display:flex;gap:.6rem">
        <form method="POST" action="{{ route('superadmin.teachers.toggle-status', $teacher) }}">
            @csrf @method('PATCH')
            <button type="submit"
                    class="btn {{ $teacher->is_active ? 'btn-danger' : 'btn-success' }}"
                    onclick="return confirm('{{ $teacher->is_active ? 'Deactivate' : 'Activate' }} this teacher?')">
                {{ $teacher->is_active ? 'Deactivate' : 'Activate' }}
            </button>
        </form>

        <form method="POST" action="{{ route('superadmin.teachers.destroy', $teacher) }}">
            @csrf @method('DELETE')
            <button type="submit" class="btn btn-danger"
                    onclick="return confirm('Permanently delete this teacher? This cannot be undone.')">
                Delete
            </button>
        </form>

        <a href="{{ route('superadmin.teachers.index') }}" class="btn btn-outline">&larr; Back</a>
    </div>
</div>

@if($errors->any())
    <div class="alert alert-error">
        <strong>Please fix the errors below:</strong>
        <ul style="margin:.4rem 0 0 1rem">
            @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
        </ul>
    </div>
@endif

@php $assignment = $teacher->teacherAssignments->first(); @endphp

<script>
    const schoolsData = @json($schools->keyBy('id')->map(fn($s) => $s->school_years ?? []));
</script>

<form method="POST" action="{{ route('superadmin.teachers.update', $teacher) }}">
    @csrf @method('PUT')

    <div class="card" style="margin-bottom:1.25rem">
        <div class="card-header"><h2>Teacher Details</h2></div>
        <div class="card-body">

            <div class="grid-2">
                <div class="form-group">
                    <label>Full Name <span style="color:#f87171">*</span></label>
                    <input type="text" name="name" class="form-control"
                           value="{{ old('name', $teacher->name) }}" required>
                    @error('name')<div class="field-error">{{ $message }}</div>@enderror
                </div>
                <div class="form-group">
                    <label>Email Address</label>
                    <input type="email" class="form-control" value="{{ $teacher->email }}"
                           disabled style="opacity:.6;cursor:not-allowed">
                    <div style="font-size:.75rem;color:var(--muted);margin-top:.3rem">
                        Email cannot be changed here.
                    </div>
                </div>
            </div>

            <div class="grid-2">
                <div class="form-group">
                    <label style="display:flex;align-items:center;gap:.5rem;cursor:pointer">
                        <input type="hidden"   name="scorz_admin" value="0">
                        <input type="checkbox" name="scorz_admin" value="1"
                               {{ old('scorz_admin', $teacher->scorz_admin) ? 'checked' : '' }}
                               style="width:auto;accent-color:var(--accent)">
                        Scorz Admin
                    </label>
                </div>
                <div class="form-group">
                    <label style="display:flex;align-items:center;gap:.5rem;cursor:pointer">
                        <input type="hidden"   name="scorz_access" value="0">
                        <input type="checkbox" name="scorz_access" value="1"
                               {{ old('scorz_access', $teacher->scorz_access) ? 'checked' : '' }}
                               style="width:auto;accent-color:var(--accent)">
                        Scorz Access
                    </label>
                </div>
            </div>

        </div>
    </div>

    <div class="card" style="margin-bottom:1.25rem">
        <div class="card-header"><h2>School Assignment</h2></div>
        <div class="card-body">
            <div class="grid-3">
                <div class="form-group">
                    <label>School <span style="color:#f87171">*</span></label>
                    <select name="school_id" id="school-select" class="form-control"
                            onchange="loadSchoolYears()" required>
                        <option value="">Select school…</option>
                        @foreach($schools as $school)
                            <option value="{{ $school->id }}"
                                {{ old('school_id', $assignment?->school_id) == $school->id ? 'selected' : '' }}>
                                {{ $school->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('school_id')<div class="field-error">{{ $message }}</div>@enderror
                </div>

                <div class="form-group">
                    <label>School Year <span style="color:#f87171">*</span></label>
                    <select name="school_year" id="year-select" class="form-control" required>
                        <option value="">Select year…</option>
                    </select>
                    @error('school_year')<div class="field-error">{{ $message }}</div>@enderror
                </div>

                <div class="form-group">
                    <label>Class <span style="color:#f87171">*</span></label>
                    <select name="class_id" id="class-select" class="form-control" required>
                        <option value="">Select class…</option>
                        @foreach(\App\Models\SchoolClass::where('is_active', true)->orderBy('name')->get() as $cls)
                            <option value="{{ $cls->id }}"
                                {{ old('class_id', $assignment?->class_id) == $cls->id ? 'selected' : '' }}>
                                {{ $cls->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('class_id')<div class="field-error">{{ $message }}</div>@enderror
                </div>
            </div>
        </div>
    </div>

    <div style="display:flex;gap:.75rem;justify-content:flex-end">
        <a href="{{ route('superadmin.teachers.index') }}" class="btn btn-outline">Cancel</a>
        <button type="submit" class="btn btn-primary">Save Changes</button>
    </div>
</form>
@endsection

@push('scripts')
<script>
const currentYear = @json(old('school_year', $assignment?->school_year));

function loadSchoolYears(selectCurrent = false) {
    const schoolId = document.getElementById('school-select').value;
    const yearSel  = document.getElementById('year-select');
    yearSel.innerHTML = '<option value="">Select year…</option>';

    if (!schoolId || !schoolsData[schoolId]) return;

    schoolsData[schoolId].forEach(yr => {
        const opt = document.createElement('option');
        opt.value = yr;
        opt.textContent = yr;
        if (selectCurrent && yr === currentYear) opt.selected = true;
        yearSel.appendChild(opt);
    });
}

window.addEventListener('DOMContentLoaded', () => {
    const sel = document.getElementById('school-select');
    if (sel.value) loadSchoolYears(true);
});
</script>
@endpush
