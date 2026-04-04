@extends('superadmin.layouts.app')

@section('title', 'Classes Management')

@section('content')
<div class="page-header">
    <div class="page-header-left">
        <h1>Classes Management</h1>
        <p>Define classes and view their assigned teachers.</p>
    </div>
    <a href="{{ route('superadmin.classes.create') }}" class="btn btn-primary">
        <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/>
        </svg>
        Add Class
    </a>
</div>

{{-- Search & Filter --}}
<form method="GET" action="{{ route('superadmin.classes.index') }}">
    <div class="toolbar">
        <div class="toolbar-search">
            <svg class="icon" width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z"/>
            </svg>
            <input type="text" name="search" class="form-control" style="width:240px;padding-left:2.1rem"
                   placeholder="Search by class name…" value="{{ request('search') }}">
        </div>
        <select name="status" class="form-control" style="width:160px">
            <option value="">All Status</option>
            <option value="active"   {{ request('status') === 'active'   ? 'selected' : '' }}>Active</option>
            <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
        </select>
        <button type="submit" class="btn btn-primary">Filter</button>
        @if(request('search') || request('status'))
            <a href="{{ route('superadmin.classes.index') }}" class="btn btn-outline">Clear</a>
        @endif
    </div>
</form>

<div class="card">
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Class Name</th>
                    <th>Teachers Assigned</th>
                    <th>Active Teachers</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($classes as $class)
                    <tr>
                        <td style="color:var(--muted)">{{ $classes->firstItem() + $loop->index }}</td>
                        <td>
                            <span style="font-weight:600;color:#f1f5f9">{{ $class->name }}</span>
                        </td>
                        <td>
                            @if($class->teacher_assignments_count > 0)
                                <span class="badge badge-blue">
                                    {{ $class->teacher_assignments_count }}
                                    {{ Str::plural('teacher', $class->teacher_assignments_count) }}
                                </span>
                            @else
                                <span style="color:var(--muted);font-size:.8rem">None</span>
                            @endif
                        </td>
                        <td>
                            @if($class->active_teachers_count > 0)
                                <span class="badge badge-success">{{ $class->active_teachers_count }} active</span>
                            @else
                                <span style="color:var(--muted);font-size:.8rem">—</span>
                            @endif
                        </td>
                        <td>
                            @if($class->is_active)
                                <span class="badge badge-success">Active</span>
                            @else
                                <span class="badge badge-danger">Inactive</span>
                            @endif
                        </td>
                        <td>
                            <div style="display:flex;gap:.4rem;align-items:center;flex-wrap:wrap">
                                <a href="{{ route('superadmin.classes.edit', $class) }}"
                                   class="btn btn-outline btn-sm">Edit</a>

                                <form method="POST"
                                      action="{{ route('superadmin.classes.toggle-status', $class) }}"
                                      style="display:inline">
                                    @csrf @method('PATCH')
                                    <button type="submit"
                                            class="btn btn-sm {{ $class->is_active ? 'btn-danger' : 'btn-success' }}"
                                            onclick="return confirm('{{ $class->is_active ? 'Deactivate' : 'Activate' }} class {{ $class->name }}?')">
                                        {{ $class->is_active ? 'Deactivate' : 'Activate' }}
                                    </button>
                                </form>

                                <form method="POST"
                                      action="{{ route('superadmin.classes.destroy', $class) }}"
                                      style="display:inline">
                                    @csrf @method('DELETE')
                                    <button type="submit"
                                            class="btn btn-danger btn-sm"
                                            onclick="return confirm('Delete class {{ $class->name }}? This cannot be undone.')">
                                        Delete
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" style="text-align:center;padding:2.5rem;color:var(--muted)">
                            No classes found.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($classes->hasPages())
        <div style="padding:.75rem 1.25rem;border-top:1px solid var(--border)">
            {{ $classes->links('pagination::simple-tailwind') }}
        </div>
    @endif
</div>
@endsection
