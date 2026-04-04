@extends('superadmin.layouts.app')

@section('title', 'Add Pupil')

@section('content')
<div class="page-header">
    <div class="page-header-left">
        <h1>Add Pupil</h1>
        <p>A unique Pupil ID and 6-digit PIN will be auto-generated and sent to the school admin and teacher.</p>
    </div>
    <a href="{{ route('superadmin.pupils.index') }}" class="btn btn-outline">&larr; Back</a>
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
    const schoolsData  = @json($schoolYears);
    const teachersData = @json($teachersJson);
</script>

<form method="POST" action="{{ route('superadmin.pupils.store') }}">
    @csrf

    <div class="card" style="margin-bottom:1.25rem">
        <div class="card-header"><h2>Personal Information</h2></div>
        <div class="card-body">
            <div class="grid-3">
                <div class="form-group">
                    <label>First Name <span style="color:#f87171">*</span></label>
                    <input type="text" name="first_name" class="form-control"
                           value="{{ old('first_name') }}" placeholder="First name" required>
                    @error('first_name')<div class="field-error">{{ $message }}</div>@enderror
                </div>
                <div class="form-group">
                    <label>Last Name <span style="color:#f87171">*</span></label>
                    <input type="text" name="last_name" class="form-control"
                           value="{{ old('last_name') }}" placeholder="Last name" required>
                    @error('last_name')<div class="field-error">{{ $message }}</div>@enderror
                </div>
                <div class="form-group">
                    <label>Date of Birth <span style="color:#f87171">*</span></label>
                    <input type="date" name="date_of_birth" class="form-control"
                           value="{{ old('date_of_birth') }}" required>
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
                            <option value="{{ $s->id }}" {{ old('school_id') == $s->id ? 'selected' : '' }}>
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
                        <option value="">Select school first…</option>
                    </select>
                    @error('year_group')<div class="field-error">{{ $message }}</div>@enderror
                </div>
            </div>
            <div class="grid-2">
                <div class="form-group">
                    <label>Teacher</label>
                    <select name="teacher_id" id="teacher-select" class="form-control">
                        <option value="">Select year first…</option>
                    </select>
                    @error('teacher_id')<div class="field-error">{{ $message }}</div>@enderror
                </div>
                <div class="form-group">
                    <label>Class <span style="color:#f87171">*</span></label>
                    <input type="text" name="class_name" id="class-input" class="form-control"
                           value="{{ old('class_name') }}" placeholder="e.g. 7A" required>
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
                        <option value="1" {{ old('include_in_averages', '1') == '1' ? 'selected' : '' }}>Yes</option>
                        <option value="0" {{ old('include_in_averages') === '0' ? 'selected' : '' }}>No</option>
                    </select>
                    @error('include_in_averages')<div class="field-error">{{ $message }}</div>@enderror
                </div>
                <div class="form-group">
                    <label>SEN — Special Educational Needs</label>
                    <select name="sen" class="form-control">
                        <option value="none"        {{ old('sen','none') === 'none'        ? 'selected':'' }}>None</option>
                        <option value="sen_support" {{ old('sen') === 'sen_support' ? 'selected':'' }}>SEN Support</option>
                        <option value="ehc_plan"    {{ old('sen') === 'ehc_plan'    ? 'selected':'' }}>EHC Plan</option>
                    </select>
                    @error('sen')<div class="field-error">{{ $message }}</div>@enderror
                </div>
                <div class="form-group">
                    <label>Status</label>
                    <select name="is_active" class="form-control">
                        <option value="1" {{ old('is_active','1') == '1' ? 'selected' : '' }}>Active</option>
                        <option value="0" {{ old('is_active') === '0'    ? 'selected' : '' }}>Inactive</option>
                    </select>
                </div>
            </div>
        </div>
    </div>

    <div style="display:flex;gap:.75rem;justify-content:flex-end">
        <a href="{{ route('superadmin.pupils.index') }}" class="btn btn-outline">Cancel</a>
        <button type="submit" class="btn btn-primary">Create Pupil</button>
    </div>
</form>
@endsection

@push('scripts')
<script>
function onSchoolChange() {
    const schoolId  = document.getElementById('school-select').value;
    const yearSel   = document.getElementById('year-select');
    const teachSel  = document.getElementById('teacher-select');

    yearSel.innerHTML  = '<option value="">Select year…</option>';
    teachSel.innerHTML = '<option value="">Select year first…</option>';
    document.getElementById('class-input').value = '';

    if (!schoolId || !schoolsData[schoolId]) return;
    schoolsData[schoolId].forEach(yr => {
        yearSel.add(new Option(yr, yr));
    });
}

function onYearChange() {
    const schoolId  = document.getElementById('school-select').value;
    const year      = document.getElementById('year-select').value;
    const teachSel  = document.getElementById('teacher-select');

    teachSel.innerHTML = '<option value="">— None —</option>';
    document.getElementById('class-input').value = '';

    if (!schoolId || !year) return;

    teachersData.forEach(t => {
        const match = t.assignments.find(a => String(a.school_id) === String(schoolId) && a.school_year === year);
        if (match) {
            const opt = new Option(t.name + ' (' + match.class_name + ')', t.id);
            opt.dataset.class = match.class_name;
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
    if (document.getElementById('school-select').value) {
        onSchoolChange();
    }
});
</script>
@endpush
