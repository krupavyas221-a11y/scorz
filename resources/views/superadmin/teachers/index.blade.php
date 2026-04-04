@extends('superadmin.layouts.app')

@section('title', 'Teacher Management')

@section('content')
<div class="page-header">
    <div class="page-header-left">
        <h1>Teacher Management</h1>
        <p>Create and manage teacher accounts linked to schools.</p>
    </div>
    <a href="{{ route('superadmin.teachers.create') }}" class="btn btn-primary">
        <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/>
        </svg>
        Add Teacher
    </a>
</div>

{{-- Search & Filter --}}
<form method="GET" action="{{ route('superadmin.teachers.index') }}">
    <div class="toolbar">
        <div class="toolbar-search">
            <svg class="icon" width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z"/>
            </svg>
            <input type="text" name="search" class="form-control" style="width:260px;padding-left:2.1rem"
                   placeholder="Search by name or email…" value="{{ request('search') }}">
        </div>
        <select name="status" class="form-control" style="width:160px">
            <option value="">All Status</option>
            <option value="active"   {{ request('status') === 'active'   ? 'selected' : '' }}>Active</option>
            <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
        </select>
        <button type="submit" class="btn btn-primary">Filter</button>
        @if(request('search') || request('status'))
            <a href="{{ route('superadmin.teachers.index') }}" class="btn btn-outline">Clear</a>
        @endif
    </div>
</form>

<div class="card">
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>School</th>
                    <th>Year / Class</th>
                    <th>Scorz Admin</th>
                    <th>Scorz Access</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($teachers as $teacher)
                    @php
                        $assignment = $teacher->teacherAssignments->first();
                        $school     = $assignment?->school ?? $teacher->schools->first();
                    @endphp
                    <tr>
                        <td style="color:var(--muted)">{{ $teachers->firstItem() + $loop->index }}</td>
                        <td style="font-weight:600;color:#f1f5f9">{{ $teacher->name }}</td>
                        <td style="font-size:.8rem">{{ $teacher->email }}</td>
                        <td>{{ $school?->name ?? '—' }}</td>
                        <td>
                            @if($assignment)
                                <span class="badge badge-blue">{{ $assignment->school_year }}</span>
                                <span style="color:var(--muted);font-size:.8rem;margin-left:.25rem">{{ $assignment->class_name }}</span>
                            @else
                                <span style="color:var(--muted)">—</span>
                            @endif
                        </td>
                        <td>
                            @if($teacher->scorz_admin)
                                <span class="badge badge-success">Yes</span>
                            @else
                                <span class="badge badge-gray">No</span>
                            @endif
                        </td>
                        <td>
                            @if($teacher->scorz_access)
                                <span class="badge badge-success">Yes</span>
                            @else
                                <span class="badge badge-danger">No</span>
                            @endif
                        </td>
                        <td>
                            @if($teacher->is_active)
                                <span class="badge badge-success">Active</span>
                            @else
                                <span class="badge badge-danger">Inactive</span>
                            @endif
                        </td>
                        <td>
                            <div style="display:flex;gap:.35rem;align-items:center">
                                <a href="{{ route('superadmin.teachers.edit', $teacher) }}"
                                   class="btn btn-outline btn-sm">Edit</a>

                                <form method="POST"
                                      action="{{ route('superadmin.teachers.toggle-status', $teacher) }}"
                                      style="display:inline">
                                    @csrf @method('PATCH')
                                    <button type="submit"
                                            class="btn btn-sm {{ $teacher->is_active ? 'btn-danger' : 'btn-success' }}"
                                            onclick="return confirm('{{ $teacher->is_active ? 'Deactivate' : 'Activate' }} this teacher?')">
                                        {{ $teacher->is_active ? 'Deactivate' : 'Activate' }}
                                    </button>
                                </form>

                                <form method="POST"
                                      action="{{ route('superadmin.teachers.destroy', $teacher) }}"
                                      style="display:inline">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger"
                                            onclick="return confirm('Permanently delete {{ addslashes($teacher->name) }}? This cannot be undone.')">
                                        Delete
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" style="text-align:center;padding:2.5rem;color:var(--muted)">
                            No teachers found.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($teachers->hasPages())
        <div style="padding:.75rem 1.25rem;border-top:1px solid var(--border)">
            {{ $teachers->links('pagination::simple-tailwind') }}
        </div>
    @endif
</div>
@endsection
