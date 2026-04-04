@extends('superadmin.layouts.app')

@section('title', 'Add Teacher')

@section('content')
<div class="page-header">
    <div class="page-header-left">
        <h1>Add Teacher</h1>
        <p>Create a teacher account. Login credentials and PIN will be sent via email.</p>
    </div>
    <a href="{{ route('superadmin.teachers.index') }}" class="btn btn-outline">&larr; Back</a>
</div>

@if($errors->any())
    <div class="alert alert-error">
        <strong>Please fix the errors below:</strong>
        <ul style="margin:.4rem 0 0 1rem">
            @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
        </ul>
    </div>
@endif

{{-- Pass school years data for JS --}}
<script>
    const schoolsData = @json($schools->keyBy('id')->map(fn($s) => $s->school_years ?? []));
</script>

<form method="POST" action="{{ route('superadmin.teachers.store') }}">
    @csrf

    <div class="card" style="margin-bottom:1.25rem">
        <div class="card-header"><h2>Teacher Details</h2></div>
        <div class="card-body">

            <div class="grid-2">
                <div class="form-group">
                    <label>Full Name <span style="color:#f87171">*</span></label>
                    <input type="text" name="name" class="form-control"
                           value="{{ old('name') }}" placeholder="Teacher full name" required>
                    @error('name')<div class="field-error">{{ $message }}</div>@enderror
                </div>
                <div class="form-group">
                    <label>Email Address <span style="color:#f87171">*</span></label>
                    <input type="email" name="email" class="form-control"
                           value="{{ old('email') }}" placeholder="teacher@school.edu" required>
                    @error('email')<div class="field-error">{{ $message }}</div>@enderror
                </div>
            </div>

            <div class="grid-2">
                <div class="form-group">
                    <label style="display:flex;align-items:center;gap:.5rem;cursor:pointer">
                        <input type="hidden"   name="scorz_admin" value="0">
                        <input type="checkbox" name="scorz_admin" value="1"
                               {{ old('scorz_admin') ? 'checked' : '' }}
                               style="width:auto;accent-color:var(--accent)">
                        Scorz Admin
                    </label>
                    <div style="font-size:.75rem;color:var(--muted);margin-top:.3rem">
                        Grant this teacher admin-level access in Scorz.
                    </div>
                </div>
                <div class="form-group">
                    <label style="display:flex;align-items:center;gap:.5rem;cursor:pointer">
                        <input type="hidden"   name="scorz_access" value="0">
                        <input type="checkbox" name="scorz_access" value="1"
                               {{ old('scorz_access', '1') ? 'checked' : '' }}
                               style="width:auto;accent-color:var(--accent)">
                        Scorz Access
                    </label>
                    <div style="font-size:.75rem;color:var(--muted);margin-top:.3rem">
                        Allow this teacher to log in to the Scorz platform.
                    </div>
                </div>
            </div>

        </div>
    </div>

    <div class="card" style="margin-bottom:1.25rem">
        <div class="card-header"><h2>School Assignment</h2></div>
        <div class="card-body">

            <div class="grid-3">
                <div class="form-group">
                    <label>School Admin / School <span style="color:#f87171">*</span></label>
                    <select name="school_id" id="school-select" class="form-control"
                            onchange="loadSchoolYears()" required>
                        <option value="">Select school…</option>
                        @foreach($schools as $school)
                            <option value="{{ $school->id }}"
                                {{ old('school_id') == $school->id ? 'selected' : '' }}>
                                {{ $school->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('school_id')<div class="field-error">{{ $message }}</div>@enderror
                </div>

                <div class="form-group">
                    <label>School Year <span style="color:#f87171">*</span></label>
                    <select name="school_year" id="year-select" class="form-control" required>
                        <option value="">Select school first…</option>
                        @if(old('school_id') && old('school_year'))
                            <option value="{{ old('school_year') }}" selected>{{ old('school_year') }}</option>
                        @endif
                    </select>
                    @error('school_year')<div class="field-error">{{ $message }}</div>@enderror
                </div>

                <div class="form-group">
                    <label>Class <span style="color:#f87171">*</span></label>
                    <input type="text" name="class_name" class="form-control"
                           value="{{ old('class_name') }}" placeholder="e.g. 7A" required>
                    @error('class_name')<div class="field-error">{{ $message }}</div>@enderror
                </div>
            </div>

        </div>
    </div>

    <div style="display:flex;gap:.75rem;justify-content:flex-end">
        <a href="{{ route('superadmin.teachers.index') }}" class="btn btn-outline">Cancel</a>
        <button type="submit" class="btn btn-primary">Create Teacher</button>
    </div>
</form>
@endsection

@push('scripts')
<script>
function loadSchoolYears() {
    const schoolId = document.getElementById('school-select').value;
    const yearSel  = document.getElementById('year-select');
    yearSel.innerHTML = '<option value="">Select year…</option>';

    if (!schoolId || !schoolsData[schoolId]) return;

    schoolsData[schoolId].forEach(yr => {
        const opt = document.createElement('option');
        opt.value = yr;
        opt.textContent = yr;
        yearSel.appendChild(opt);
    });
}

// Re-populate on page load if old('school_id') is set
window.addEventListener('DOMContentLoaded', () => {
    const sel = document.getElementById('school-select');
    if (sel.value) loadSchoolYears();
});
</script>
@endpush
