@extends('superadmin.layouts.app')

@section('title', 'Edit School Year — ' . $schoolYear->year)

@section('content')
<div class="page-header">
    <div class="page-header-left">
        <h1>{{ $schoolYear->year }}</h1>
        <p>
            Edit academic year details and manage school assignments.
            @if($schoolYear->is_active)
                <span class="badge badge-success" style="margin-left:.4rem">Active</span>
            @else
                <span class="badge badge-danger" style="margin-left:.4rem">Inactive</span>
            @endif
        </p>
    </div>
    <div style="display:flex;gap:.6rem">
        <form method="POST" action="{{ route('superadmin.school-years.toggle-status', $schoolYear) }}">
            @csrf @method('PATCH')
            <button type="submit"
                    class="btn {{ $schoolYear->is_active ? 'btn-danger' : 'btn-success' }}"
                    onclick="return confirm('{{ $schoolYear->is_active ? 'Deactivate' : 'Activate' }} this school year?')">
                {{ $schoolYear->is_active ? 'Deactivate' : 'Activate' }}
            </button>
        </form>
        <a href="{{ route('superadmin.school-years.index') }}" class="btn btn-outline">
            <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18"/>
            </svg>
            Back
        </a>
    </div>
</div>

<div class="grid-2" style="align-items:start">

    {{-- Edit Year --}}
    <div class="card">
        <div class="card-header"><h2>Academic Year</h2></div>
        <div class="card-body">
            <form method="POST" action="{{ route('superadmin.school-years.update', $schoolYear) }}">
                @csrf @method('PUT')

                <div class="form-group">
                    <label for="year">Year <span style="color:var(--danger)">*</span></label>
                    <input type="text"
                           id="year"
                           name="year"
                           class="form-control"
                           placeholder="e.g., 2023–2024"
                           value="{{ old('year', $schoolYear->year) }}"
                           maxlength="20"
                           autocomplete="off">
                    @error('year')
                        <div class="field-error">{{ $message }}</div>
                    @enderror
                    <div style="color:var(--muted);font-size:.76rem;margin-top:.3rem">
                        Format: YYYY–YYYY (e.g., 2023–2024 or 2023-2024)
                    </div>
                </div>

                <button type="submit" class="btn btn-primary" style="margin-top:.5rem">Save Changes</button>
            </form>
        </div>
    </div>

    {{-- Assign Schools --}}
    <div class="card">
        <div class="card-header">
            <h2>Assigned Schools</h2>
            <span style="color:var(--muted);font-size:.8rem">{{ $schoolYear->schools->count() }} assigned</span>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('superadmin.school-years.assign-schools', $schoolYear) }}">
                @csrf @method('PATCH')

                <div class="form-group" style="margin-bottom:0">
                    <label style="margin-bottom:.5rem;display:block">Select Schools</label>
                    <div id="schools-list" style="max-height:320px;overflow-y:auto;border:1px solid var(--border);border-radius:7px;padding:.5rem">
                        @forelse(\App\Models\School::orderBy('name')->get() as $school)
                            <label style="display:flex;align-items:center;gap:.6rem;padding:.4rem .5rem;border-radius:5px;cursor:pointer;transition:background .1s"
                                   onmouseover="this.style.background='rgba(99,102,241,.07)'"
                                   onmouseout="this.style.background='transparent'">
                                <input type="checkbox"
                                       name="school_ids[]"
                                       value="{{ $school->id }}"
                                       {{ $schoolYear->schools->contains($school->id) ? 'checked' : '' }}
                                       style="accent-color:var(--accent);width:15px;height:15px;flex-shrink:0">
                                <span style="font-size:.84rem;color:var(--text)">{{ $school->name }}</span>
                                @if(!$school->is_active)
                                    <span class="badge badge-gray" style="margin-left:auto;font-size:.67rem">Inactive</span>
                                @endif
                            </label>
                        @empty
                            <p style="color:var(--muted);font-size:.82rem;padding:.5rem">No schools available.</p>
                        @endforelse
                    </div>
                </div>

                <button type="submit" class="btn btn-primary" style="margin-top:1rem">Update Assignments</button>
            </form>
        </div>
    </div>

</div>

{{-- Schools Table --}}
@if($schoolYear->schools->count())
<div class="card" style="margin-top:1.5rem">
    <div class="card-header"><h2>Schools in this Year</h2></div>
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>School Name</th>
                    <th>Type</th>
                    <th>Region</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach($schoolYear->schools as $school)
                    <tr>
                        <td style="color:var(--muted)">{{ $loop->iteration }}</td>
                        <td style="font-weight:600;color:#f1f5f9">{{ $school->name }}</td>
                        <td>
                            @if($school->school_type)
                                <span class="badge badge-blue">{{ ucfirst($school->school_type) }}</span>
                            @else
                                <span style="color:var(--muted)">—</span>
                            @endif
                        </td>
                        <td>{{ $school->region ?: '—' }}</td>
                        <td>
                            @if($school->is_active)
                                <span class="badge badge-success">Active</span>
                            @else
                                <span class="badge badge-danger">Inactive</span>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endif

@endsection
