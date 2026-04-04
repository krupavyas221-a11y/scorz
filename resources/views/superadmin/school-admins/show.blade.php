@extends('superadmin.layouts.app')

@section('title', $school->name)

@section('content')
<div class="page-header">
    <div class="page-header-left">
        <h1>{{ $school->name }}</h1>
        <p>
            View and update school admin details.
            @if($school->is_active)
                <span class="badge badge-success" style="margin-left:.4rem">Active</span>
            @else
                <span class="badge badge-danger" style="margin-left:.4rem">Inactive</span>
            @endif
        </p>
    </div>
    <div style="display:flex;gap:.6rem">
        <form method="POST" action="{{ route('superadmin.school-admins.toggle-status', $school) }}">
            @csrf @method('PATCH')
            <button type="submit"
                    class="btn {{ $school->is_active ? 'btn-danger' : 'btn-success' }}"
                    onclick="return confirm('{{ $school->is_active ? 'Deactivate' : 'Activate' }} this school admin?')">
                {{ $school->is_active ? 'Deactivate' : 'Activate' }}
            </button>
        </form>
        <a href="{{ route('superadmin.school-admins.index') }}" class="btn btn-outline">&larr; Back</a>
    </div>
</div>

<form method="POST" action="{{ route('superadmin.school-admins.update', $school) }}">
    @csrf @method('PUT')

    @if($errors->any())
        <div class="alert alert-error">
            <strong>Please fix the errors below:</strong>
            <ul style="margin:.4rem 0 0 1rem">
                @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
            </ul>
        </div>
    @endif

    {{-- School Details --}}
    <div class="card" style="margin-bottom:1.25rem">
        <div class="card-header"><h2>School Details</h2></div>
        <div class="card-body">

            <div class="grid-2">
                <div class="form-group">
                    <label>School Name <span style="color:#f87171">*</span></label>
                    <input type="text" name="school_name" class="form-control"
                           value="{{ old('school_name', $school->name) }}" required>
                    @error('school_name')<div class="field-error">{{ $message }}</div>@enderror
                </div>
                <div class="form-group">
                    <label>School Type <span style="color:#f87171">*</span></label>
                    <select name="school_type" class="form-control" required>
                        <option value="">Select type…</option>
                        @foreach(['primary','secondary','sixth_form','grammar','independent','special'] as $t)
                            <option value="{{ $t }}"
                                {{ old('school_type', $school->school_type) === $t ? 'selected' : '' }}>
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
                           value="{{ old('region', $school->region) }}">
                    @error('region')<div class="field-error">{{ $message }}</div>@enderror
                </div>
                <div class="form-group">
                    <label>Gender</label>
                    <select name="gender" class="form-control">
                        <option value="">Select…</option>
                        @foreach(['girls','boys','mixed'] as $g)
                            <option value="{{ $g }}"
                                {{ old('gender', $school->gender) === $g ? 'selected' : '' }}>
                                {{ ucfirst($g) }}
                            </option>
                        @endforeach
                    </select>
                    @error('gender')<div class="field-error">{{ $message }}</div>@enderror
                </div>
            </div>

            <div class="form-group">
                <label>Address</label>
                <textarea name="address" class="form-control" rows="2">{{ old('address', $school->address) }}</textarea>
                @error('address')<div class="field-error">{{ $message }}</div>@enderror
            </div>

            <div class="grid-3">
                <div class="form-group">
                    <label>Phone Number</label>
                    <input type="text" name="phone" class="form-control" value="{{ old('phone', $school->phone) }}">
                    @error('phone')<div class="field-error">{{ $message }}</div>@enderror
                </div>
                <div class="form-group">
                    <label>Fax</label>
                    <input type="text" name="fax" class="form-control" value="{{ old('fax', $school->fax) }}">
                    @error('fax')<div class="field-error">{{ $message }}</div>@enderror
                </div>
                <div class="form-group">
                    <label>Principal Name</label>
                    <input type="text" name="principal_name" class="form-control"
                           value="{{ old('principal_name', $school->principal_name) }}">
                    @error('principal_name')<div class="field-error">{{ $message }}</div>@enderror
                </div>
            </div>

            <div class="grid-3">
                <div class="form-group">
                    <label>School Email</label>
                    <input type="email" name="school_email" class="form-control"
                           value="{{ old('school_email', $school->email) }}">
                    @error('school_email')<div class="field-error">{{ $message }}</div>@enderror
                </div>
                <div class="form-group">
                    <label>School Website</label>
                    <input type="url" name="website" class="form-control"
                           value="{{ old('website', $school->website) }}">
                    @error('website')<div class="field-error">{{ $message }}</div>@enderror
                </div>
                <div class="form-group">
                    <label>Teacher Council Number</label>
                    <input type="text" name="teacher_council_number" class="form-control"
                           value="{{ old('teacher_council_number', $school->teacher_council_number) }}">
                    @error('teacher_council_number')<div class="field-error">{{ $message }}</div>@enderror
                </div>
            </div>

            <div class="form-group">
                <label>Assigned School Years</label>
                <div id="years-container" style="display:flex;flex-wrap:wrap;gap:.5rem;align-items:center">
                    @foreach(old('school_years', $school->school_years ?? []) as $yr)
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
            </div>

        </div>
    </div>

    {{-- Admin Account --}}
    @php
        $adminRole = $school->userSchoolRoles->first();
        $admin = $adminRole?->user;
    @endphp
    <div class="card" style="margin-bottom:1.25rem">
        <div class="card-header"><h2>School Admin Account</h2></div>
        <div class="card-body">
            <div class="grid-2">
                <div class="form-group">
                    <label>Admin Full Name <span style="color:#f87171">*</span></label>
                    <input type="text" name="admin_name" class="form-control"
                           value="{{ old('admin_name', $admin?->name) }}" required>
                    @error('admin_name')<div class="field-error">{{ $message }}</div>@enderror
                </div>
                <div class="form-group">
                    <label>Admin Email Address</label>
                    <input type="email" class="form-control" value="{{ $admin?->email }}" disabled
                           style="opacity:.6;cursor:not-allowed">
                    <div style="font-size:.75rem;color:var(--muted);margin-top:.3rem">
                        Email cannot be changed here. Contact the developer if needed.
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div style="display:flex;gap:.75rem;justify-content:flex-end">
        <a href="{{ route('superadmin.school-admins.index') }}" class="btn btn-outline">Cancel</a>
        <button type="submit" class="btn btn-primary">Save Changes</button>
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
function removeYear(btn) { btn.closest('.year-tag').remove(); }
document.getElementById('year-input').addEventListener('keydown', function(e) {
    if (e.key === 'Enter') { e.preventDefault(); addYear(); }
});
</script>
@endpush
