@extends('superadmin.layouts.app')

@section('title', 'Edit Class — ' . $class->name)

@section('content')
<div class="page-header">
    <div class="page-header-left">
        <h1>{{ $class->name }}</h1>
        <p>
            Edit class details and view assigned teachers.
            @if($class->is_active)
                <span class="badge badge-success" style="margin-left:.4rem">Active</span>
            @else
                <span class="badge badge-danger" style="margin-left:.4rem">Inactive</span>
            @endif
        </p>
    </div>
    <div style="display:flex;gap:.6rem">
        <form method="POST" action="{{ route('superadmin.classes.toggle-status', $class) }}">
            @csrf @method('PATCH')
            <button type="submit"
                    class="btn {{ $class->is_active ? 'btn-danger' : 'btn-success' }}"
                    onclick="return confirm('{{ $class->is_active ? 'Deactivate' : 'Activate' }} this class?')">
                {{ $class->is_active ? 'Deactivate' : 'Activate' }}
            </button>
        </form>

        @if($class->teacherAssignments->isEmpty())
            <form method="POST" action="{{ route('superadmin.classes.destroy', $class) }}">
                @csrf @method('DELETE')
                <button type="submit" class="btn btn-danger"
                        onclick="return confirm('Permanently delete this class?')">
                    Delete
                </button>
            </form>
        @endif

        <a href="{{ route('superadmin.classes.index') }}" class="btn btn-outline">
            <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18"/>
            </svg>
            Back
        </a>
    </div>
</div>

<div class="grid-2" style="align-items:start">

    {{-- Edit Class Name --}}
    <div class="card">
        <div class="card-header"><h2>Class Details</h2></div>
        <div class="card-body">
            <form method="POST" action="{{ route('superadmin.classes.update', $class) }}">
                @csrf @method('PUT')

                <div class="form-group">
                    <label for="name">Class Name <span style="color:var(--danger)">*</span></label>
                    <input type="text"
                           id="name"
                           name="name"
                           class="form-control"
                           placeholder="e.g., Junior Infant"
                           value="{{ old('name', $class->name) }}"
                           maxlength="60"
                           autocomplete="off">
                    @error('name')
                        <div class="field-error">{{ $message }}</div>
                    @enderror
                </div>

                <div style="display:flex;gap:.5rem;align-items:center;margin-top:1rem;padding:.75rem;background:rgba(99,102,241,.06);border-radius:8px;border:1px solid var(--border)">
                    <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" style="color:var(--muted);flex-shrink:0">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0"/>
                    </svg>
                    <span style="font-size:.8rem;color:var(--muted)">
                        {{ $class->teacherAssignments->count() }}
                        {{ Str::plural('teacher', $class->teacherAssignments->count()) }} currently assigned
                    </span>
                </div>

                <button type="submit" class="btn btn-primary" style="margin-top:1rem">Save Changes</button>
            </form>
        </div>
    </div>

    {{-- Assigned Teachers --}}
    <div class="card">
        <div class="card-header">
            <h2>Assigned Teachers</h2>
            <span style="color:var(--muted);font-size:.8rem">
                {{ $class->teacherAssignments->count() }} total
            </span>
        </div>

        @if($class->teacherAssignments->isEmpty())
            <div class="card-body" style="text-align:center;padding:2rem">
                <svg width="36" height="36" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"
                     style="color:var(--muted);margin:0 auto 1rem">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="M4.26 10.147a60.438 60.438 0 00-.491 6.347A48.62 48.62 0 0112 20.904a48.62 48.62 0 018.232-4.41 60.46 60.46 0 00-.491-6.347m-15.482 0a50.636 50.636 0 00-2.658-.813A59.906 59.906 0 0112 3.493a59.903 59.903 0 0110.399 5.84c-.896.248-1.783.52-2.658.814m-15.482 0A50.717 50.717 0 0112 13.489"/>
                </svg>
                <p style="color:var(--muted);font-size:.84rem">No teachers assigned to this class yet.</p>
                <a href="{{ route('superadmin.teachers.create') }}" class="btn btn-outline btn-sm" style="margin-top:.75rem">
                    Assign via Teacher Management
                </a>
            </div>
        @else
            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>Teacher</th>
                            <th>School</th>
                            <th>Year Group</th>
                            <th>Status</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($class->teacherAssignments as $assignment)
                            <tr>
                                <td>
                                    <div style="font-weight:600;color:#f1f5f9">
                                        {{ $assignment->user?->name ?? '—' }}
                                    </div>
                                    <div style="font-size:.76rem;color:var(--muted)">
                                        {{ $assignment->user?->email }}
                                    </div>
                                </td>
                                <td style="font-size:.83rem">{{ $assignment->school?->name ?? '—' }}</td>
                                <td>
                                    @if($assignment->school_year)
                                        <span class="badge badge-blue" style="font-size:.72rem">
                                            {{ $assignment->school_year }}
                                        </span>
                                    @else
                                        <span style="color:var(--muted)">—</span>
                                    @endif
                                </td>
                                <td>
                                    @if($assignment->user?->is_active)
                                        <span class="badge badge-success">Active</span>
                                    @else
                                        <span class="badge badge-danger">Inactive</span>
                                    @endif
                                </td>
                                <td>
                                    @if($assignment->user)
                                        <a href="{{ route('superadmin.teachers.edit', $assignment->user) }}"
                                           class="btn btn-outline btn-sm">Edit</a>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

</div>
@endsection
