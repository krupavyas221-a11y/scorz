@extends('superadmin.layouts.app')

@section('title', 'Add School Admin')

@section('content')
<div class="page-header">
    <div class="page-header-left">
        <h1>Add School Admin</h1>
        <p>Create a new school and assign an administrator. Credentials will be emailed automatically.</p>
    </div>
    <a href="{{ route('superadmin.school-admins.index') }}" class="btn btn-outline">
        &larr; Back
    </a>
</div>

@if($errors->any())
    <div class="alert alert-error">
        <strong>Please fix the errors below:</strong>
        <ul style="margin:.4rem 0 0 1rem">
            @foreach($errors->all() as $e)
                <li>{{ $e }}</li>
            @endforeach
        </ul>
    </div>
@endif

<form method="POST" action="{{ route('superadmin.school-admins.store') }}">
    @csrf

    {{-- School Information --}}
    <div class="card" style="margin-bottom:1.25rem">
        <div class="card-header"><h2>School Information</h2></div>
        <div class="card-body">

            <div class="grid-2">
                <div class="form-group">
                    <label>School Name <span style="color:#f87171">*</span></label>
                    <input type="text" name="school_name" class="form-control"
                           value="{{ old('school_name') }}" placeholder="e.g. Greenwood Academy" required>
                    @error('school_name')<div class="field-error">{{ $message }}</div>@enderror
                </div>
                <div class="form-group">
                    <label>School Type <span style="color:#f87171">*</span></label>
                    <select name="school_type" class="form-control" required>
                        <option value="">Select type…</option>
                        @foreach(['primary','secondary','sixth_form','grammar','independent','special'] as $t)
                            <option value="{{ $t }}" {{ old('school_type') === $t ? 'selected' : '' }}>
                                {{ ucfirst(str_replace('_',' ',$t)) }}
                            </option>
                        @endforeach
                    </select>
                    @error('school_type')<div class="field-error">{{ $message }}</div>@enderror
                </div>
            </div>

            <div class="grid-2">
                <div class="form-group">
                    <label>Region</label>
                    <input type="text" name="region" class="form-control"
                           value="{{ old('region') }}" placeholder="e.g. South East England">
                    @error('region')<div class="field-error">{{ $message }}</div>@enderror
                </div>
                <div class="form-group">
                    <label>Gender</label>
                    <select name="gender" class="form-control">
                        <option value="">Select…</option>
                        <option value="girls" {{ old('gender') === 'girls' ? 'selected' : '' }}>Girls</option>
                        <option value="boys"  {{ old('gender') === 'boys'  ? 'selected' : '' }}>Boys</option>
                        <option value="mixed" {{ old('gender') === 'mixed' ? 'selected' : '' }}>Mixed</option>
                    </select>
                    @error('gender')<div class="field-error">{{ $message }}</div>@enderror
                </div>
            </div>

            <div class="form-group">
                <label>Address</label>
                <textarea name="address" class="form-control" rows="2"
                          placeholder="Full school address">{{ old('address') }}</textarea>
                @error('address')<div class="field-error">{{ $message }}</div>@enderror
            </div>

            <div class="grid-3">
                <div class="form-group">
                    <label>Phone Number</label>
                    <input type="text" name="phone" class="form-control"
                           value="{{ old('phone') }}" placeholder="+44 1234 567890">
                    @error('phone')<div class="field-error">{{ $message }}</div>@enderror
                </div>
                <div class="form-group">
                    <label>Fax</label>
                    <input type="text" name="fax" class="form-control"
                           value="{{ old('fax') }}" placeholder="+44 1234 567891">
                    @error('fax')<div class="field-error">{{ $message }}</div>@enderror
                </div>
                <div class="form-group">
                    <label>Principal Name</label>
                    <input type="text" name="principal_name" class="form-control"
                           value="{{ old('principal_name') }}" placeholder="Full name">
                    @error('principal_name')<div class="field-error">{{ $message }}</div>@enderror
                </div>
            </div>

            <div class="grid-3">
                <div class="form-group">
                    <label>School Email</label>
                    <input type="email" name="school_email" class="form-control"
                           value="{{ old('school_email') }}" placeholder="info@school.edu">
                    @error('school_email')<div class="field-error">{{ $message }}</div>@enderror
                </div>
                <div class="form-group">
                    <label>School Website</label>
                    <input type="url" name="website" class="form-control"
                           value="{{ old('website') }}" placeholder="https://school.edu">
                    @error('website')<div class="field-error">{{ $message }}</div>@enderror
                </div>
                <div class="form-group">
                    <label>Teacher Council Number</label>
                    <input type="text" name="teacher_council_number" class="form-control"
                           value="{{ old('teacher_council_number') }}" placeholder="e.g. TC-12345">
                    @error('teacher_council_number')<div class="field-error">{{ $message }}</div>@enderror
                </div>
            </div>

            <div class="form-group">
                <label>Assigned School Years</label>
                <div id="years-container" style="display:flex;flex-wrap:wrap;gap:.5rem;align-items:center">
                    @foreach(old('school_years', []) as $yr)
                        <div class="year-tag">
                            <input type="hidden" name="school_years[]" value="{{ $yr }}">
                            <span class="badge badge-blue" style="font-size:.78rem;padding:.3rem .7rem;gap:.4rem">
                                {{ $yr }}
                                <button type="button" onclick="removeYear(this)"
                                        style="background:none;border:none;cursor:pointer;color:#818cf8;font-size:.9rem;line-height:1;padding:0">&times;</button>
                            </span>
                        </div>
                    @endforeach
                    <div style="display:flex;gap:.4rem;align-items:center">
                        <input type="text" id="year-input" class="form-control"
                               style="width:130px" placeholder="e.g. Year 7">
                        <button type="button" class="btn btn-outline btn-sm" onclick="addYear()">Add</button>
                    </div>
                </div>
                @error('school_years')<div class="field-error">{{ $message }}</div>@enderror
            </div>

        </div>
    </div>

    {{-- Admin Account --}}
    <div class="card" style="margin-bottom:1.25rem">
        <div class="card-header"><h2>School Admin Account</h2></div>
        <div class="card-body">
            <p style="font-size:.82rem;color:var(--muted);margin-bottom:1rem">
                A password and 5-digit PIN will be auto-generated and sent to the admin's email address.
            </p>
            <div class="grid-2">
                <div class="form-group">
                    <label>Admin Full Name <span style="color:#f87171">*</span></label>
                    <input type="text" name="admin_name" class="form-control"
                           value="{{ old('admin_name') }}" placeholder="Full name" required>
                    @error('admin_name')<div class="field-error">{{ $message }}</div>@enderror
                </div>
                <div class="form-group">
                    <label>Admin Email Address <span style="color:#f87171">*</span></label>
                    <input type="email" name="admin_email" class="form-control"
                           value="{{ old('admin_email') }}" placeholder="admin@school.edu" required>
                    @error('admin_email')<div class="field-error">{{ $message }}</div>@enderror
                </div>
            </div>
        </div>
    </div>

    <div style="display:flex;gap:.75rem;justify-content:flex-end">
        <a href="{{ route('superadmin.school-admins.index') }}" class="btn btn-outline">Cancel</a>
        <button type="submit" class="btn btn-primary">Create School Admin</button>
    </div>
</form>
@endsection

@push('scripts')
<script>
function addYear() {
    const input = document.getElementById('year-input');
    const val = input.value.trim();
    if (!val) return;

    const container = document.getElementById('years-container');
    const div = document.createElement('div');
    div.className = 'year-tag';
    div.innerHTML = `
        <input type="hidden" name="school_years[]" value="${val}">
        <span class="badge badge-blue" style="font-size:.78rem;padding:.3rem .7rem;gap:.4rem">
            ${val}
            <button type="button" onclick="removeYear(this)"
                    style="background:none;border:none;cursor:pointer;color:#818cf8;font-size:.9rem;line-height:1;padding:0">&times;</button>
        </span>`;
    container.insertBefore(div, container.lastElementChild);
    input.value = '';
    input.focus();
}
function removeYear(btn) {
    btn.closest('.year-tag').remove();
}
document.getElementById('year-input').addEventListener('keydown', function(e) {
    if (e.key === 'Enter') { e.preventDefault(); addYear(); }
});
</script>
@endpush
