@extends('superadmin.layouts.app')

@section('title', 'Edit Pupil — '.$pupil->full_name)

@section('content')
<div class="page-header">
    <div class="page-header-left">
        <h1>Edit Pupil</h1>
        <p>
            <span style="font-family:monospace;color:#818cf8">{{ $pupil->pupil_id }}</span>
            &mdash; {{ $pupil->full_name }}
            @if($pupil->is_active)
                <span class="badge badge-success" style="margin-left:.4rem">Active</span>
            @else
                <span class="badge badge-danger" style="margin-left:.4rem">Inactive</span>
            @endif
        </p>
    </div>
    <div style="display:flex;gap:.6rem">
        <form method="POST" action="{{ route('superadmin.pupils.toggle-status', $pupil) }}">
            @csrf @method('PATCH')
            <button type="submit"
                    class="btn {{ $pupil->is_active ? 'btn-danger' : 'btn-success' }}"
                    onclick="return confirm('{{ $pupil->is_active ? 'Deactivate' : 'Activate' }} this pupil?')">
                {{ $pupil->is_active ? 'Deactivate' : 'Activate' }}
            </button>
        </form>
        <form method="POST" action="{{ route('superadmin.pupils.destroy', $pupil) }}">
            @csrf @method('DELETE')
            <button type="submit" class="btn btn-danger"
                    onclick="return confirm('Permanently delete this pupil? This cannot be undone.')">
                Delete
            </button>
        </form>
        <a href="{{ route('superadmin.pupils.index') }}" class="btn btn-outline">&larr; Back</a>
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

<script>
    const schoolsData    = @json($schoolYears);
    const teachersData   = @json($teachersJson);
    const currentYear    = @json(old('year_group', $pupil->year_group));
    const currentTeacher = @json(old('teacher_id', $pupil->teacher_id));
</script>

<form method="POST" action="{{ route('superadmin.pupils.update', $pupil) }}">
    @csrf @method('PUT')

    <div class="card" style="margin-bottom:1.25rem">
        <div class="card-header">
            <h2>Personal Information</h2>
            <span style="font-size:.78rem;color:var(--muted)">
                Pupil ID: <strong style="color:#818cf8;font-family:monospace">{{ $pupil->pupil_id }}</strong>
            </span>
        </div>
        <div class="card-body">
            <div class="grid-3">
                <div class="form-group">
                    <label>First Name <span style="color:#f87171">*</span></label>
                    <input type="text" name="first_name" class="form-control"
                           value="{{ old('first_name', $pupil->first_name) }}" required>
                    @error('first_name')<div class="field-error">{{ $message }}</div>@enderror
                </div>
                <div class="form-group">
                    <label>Last Name <span style="color:#f87171">*</span></label>
                    <input type="text" name="last_name" class="form-control"
                           value="{{ old('last_name', $pupil->last_name) }}" required>
                    @error('last_name')<div class="field-error">{{ $message }}</div>@enderror
                </div>
                <div class="form-group">
                    <label>Date of Birth <span style="color:#f87171">*</span></label>
                    <input type="date" name="date_of_birth" class="form-control"
                           value="{{ old('date_of_birth', $pupil->date_of_birth?->format('Y-m-d')) }}" required>
                    @error('date_of_birth')<div class="field-error">{{ $message }}</div>@enderror
                </div>
            </div>
        </div>
    </div>

    <div class="card" style="margin-bottom:1.25rem">
        <div class="card-header"><h2>School & Class Assignment</h2></div>
        <div class="card-body">
            <div class="grid-2">
                <div class="form-group">
                    <label>School <span style="color:#f87171">*</span></label>
                    <select name="school_id" id="school-select" class="form-control"
                            onchange="onSchoolChange()" required>
                        <option value="">Select school…</option>
                        @foreach($schools as $s)
                            <option value="{{ $s->id }}"
                                {{ old('school_id', $pupil->school_id) == $s->id ? 'selected' : '' }}>
                                {{ $s->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('school_id')<div class="field-error">{{ $message }}</div>@enderror
                </div>
                <div class="form-group">
                    <label>School Year <span style="color:#f87171">*</span></label>
                    <select name="year_group" id="year-select" class="form-control"
                            onchange="onYearChange()" required>
                        <option value="">Loading…</option>
                    </select>
                    @error('year_group')<div class="field-error">{{ $message }}</div>@enderror
                </div>
            </div>
            <div class="grid-2">
                <div class="form-group">
                    <label>Teacher</label>
                    <select name="teacher_id" id="teacher-select" class="form-control">
                        <option value="">— None —</option>
                    </select>
                    @error('teacher_id')<div class="field-error">{{ $message }}</div>@enderror
                </div>
                <div class="form-group">
                    <label>Class <span style="color:#f87171">*</span></label>
                    <input type="text" name="class_name" id="class-input" class="form-control"
                           value="{{ old('class_name', $pupil->class_name) }}" required>
                    @error('class_name')<div class="field-error">{{ $message }}</div>@enderror
                </div>
            </div>
        </div>
    </div>

    <div class="card" style="margin-bottom:1.25rem">
        <div class="card-header"><h2>Additional Details</h2></div>
        <div class="card-body">
            <div class="grid-3">
                <div class="form-group">
                    <label>Include in Averages</label>
                    <select name="include_in_averages" class="form-control">
                        <option value="1" {{ old('include_in_averages', $pupil->include_in_averages ? '1' : '0') == '1' ? 'selected' : '' }}>Yes</option>
                        <option value="0" {{ old('include_in_averages', $pupil->include_in_averages ? '1' : '0') == '0' ? 'selected' : '' }}>No</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>SEN — Special Educational Needs</label>
                    <select name="sen" class="form-control">
                        <option value="none"        {{ old('sen', $pupil->sen) === 'none'        ? 'selected':'' }}>None</option>
                        <option value="sen_support" {{ old('sen', $pupil->sen) === 'sen_support' ? 'selected':'' }}>SEN Support</option>
                        <option value="ehc_plan"    {{ old('sen', $pupil->sen) === 'ehc_plan'    ? 'selected':'' }}>EHC Plan</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Status</label>
                    <select name="is_active" class="form-control">
                        <option value="1" {{ old('is_active', $pupil->is_active ? '1' : '0') == '1' ? 'selected' : '' }}>Active</option>
                        <option value="0" {{ old('is_active', $pupil->is_active ? '1' : '0') == '0' ? 'selected' : '' }}>Inactive</option>
                    </select>
                </div>
            </div>
        </div>
    </div>

    <div style="display:flex;gap:.75rem;justify-content:flex-end">
        <a href="{{ route('superadmin.pupils.index') }}" class="btn btn-outline">Cancel</a>
        <button type="submit" class="btn btn-primary">Save Changes</button>
    </div>
</form>
@endsection

@push('scripts')
<script>
function onSchoolChange(preserveYear = false) {
    const schoolId = document.getElementById('school-select').value;
    const yearSel  = document.getElementById('year-select');
    yearSel.innerHTML = '<option value="">Select year…</option>';
    document.getElementById('teacher-select').innerHTML = '<option value="">— None —</option>';

    if (!schoolId || !schoolsData[schoolId]) return;
    schoolsData[schoolId].forEach(yr => {
        const opt = new Option(yr, yr);
        if (preserveYear && yr === currentYear) opt.selected = true;
        yearSel.add(opt);
    });
    if (preserveYear) onYearChange(true);
}

function onYearChange(preserveTeacher = false) {
    const schoolId = document.getElementById('school-select').value;
    const year     = document.getElementById('year-select').value;
    const teachSel = document.getElementById('teacher-select');
    teachSel.innerHTML = '<option value="">— None —</option>';

    if (!schoolId || !year) return;
    teachersData.forEach(t => {
        const match = t.assignments.find(a => String(a.school_id) === String(schoolId) && a.school_year === year);
        if (match) {
            const opt = new Option(t.name + ' (' + match.class_name + ')', t.id);
            opt.dataset.class = match.class_name;
            if (preserveTeacher && String(t.id) === String(currentTeacher)) opt.selected = true;
            teachSel.add(opt);
        }
    });
}

document.getElementById('teacher-select').addEventListener('change', function () {
    const opt = this.options[this.selectedIndex];
    if (opt && opt.dataset.class) {
        document.getElementById('class-input').value = opt.dataset.class;
    }
});

window.addEventListener('DOMContentLoaded', () => {
    const sel = document.getElementById('school-select');
    if (sel.value) onSchoolChange(true);
});
</script>
@endpush
