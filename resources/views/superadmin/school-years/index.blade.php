@extends('superadmin.layouts.app')

@section('title', 'School Years Management')

@section('content')
<div class="page-header">
    <div class="page-header-left">
        <h1>School Years Management</h1>
        <p>Create and manage academic years and their school assignments.</p>
    </div>
    <a href="{{ route('superadmin.school-years.create') }}" class="btn btn-primary">
        <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/>
        </svg>
        Add School Year
    </a>
</div>

{{-- Search & Filter --}}
<form method="GET" action="{{ route('superadmin.school-years.index') }}">
    <div class="toolbar">
        <div class="toolbar-search">
            <svg class="icon" width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z"/>
            </svg>
            <input type="text" name="search" class="form-control" style="width:220px;padding-left:2.1rem"
                   placeholder="Search year…" value="{{ request('search') }}">
        </div>
        <select name="status" class="form-control" style="width:160px">
            <option value="">All Status</option>
            <option value="active"   {{ request('status') === 'active'   ? 'selected' : '' }}>Active</option>
            <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
        </select>
        <button type="submit" class="btn btn-primary">Filter</button>
        @if(request('search') || request('status'))
            <a href="{{ route('superadmin.school-years.index') }}" class="btn btn-outline">Clear</a>
        @endif
    </div>
</form>

<div class="card">
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Academic Year</th>
                    <th>Schools Assigned</th>
                    <th>Status</th>
                    <th>Created</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($schoolYears as $sy)
                    <tr>
                        <td style="color:var(--muted)">{{ $schoolYears->firstItem() + $loop->index }}</td>
                        <td>
                            <span style="font-weight:700;color:#f1f5f9;font-size:.95rem">{{ $sy->year }}</span>
                        </td>
                        <td>
                            @if($sy->schools_count > 0)
                                <span class="badge badge-blue">{{ $sy->schools_count }} {{ Str::plural('school', $sy->schools_count) }}</span>
                            @else
                                <span style="color:var(--muted);font-size:.8rem">None assigned</span>
                            @endif
                        </td>
                        <td>
                            @if($sy->is_active)
                                <span class="badge badge-success">Active</span>
                            @else
                                <span class="badge badge-danger">Inactive</span>
                            @endif
                        </td>
                        <td style="color:var(--muted);font-size:.8rem">{{ $sy->created_at->format('d M Y') }}</td>
                        <td>
                            <div style="display:flex;gap:.4rem;align-items:center;flex-wrap:wrap">
                                <a href="{{ route('superadmin.school-years.edit', $sy) }}"
                                   class="btn btn-outline btn-sm">Edit</a>

                                <form method="POST"
                                      action="{{ route('superadmin.school-years.toggle-status', $sy) }}"
                                      style="display:inline">
                                    @csrf @method('PATCH')
                                    <button type="submit"
                                            class="btn btn-sm {{ $sy->is_active ? 'btn-danger' : 'btn-success' }}"
                                            onclick="return confirm('{{ $sy->is_active ? 'Deactivate' : 'Activate' }} school year {{ $sy->year }}?')">
                                        {{ $sy->is_active ? 'Deactivate' : 'Activate' }}
                                    </button>
                                </form>

                                <form method="POST"
                                      action="{{ route('superadmin.school-years.destroy', $sy) }}"
                                      style="display:inline">
                                    @csrf @method('DELETE')
                                    <button type="submit"
                                            class="btn btn-danger btn-sm"
                                            onclick="return confirm('Delete school year {{ $sy->year }}? This cannot be undone.')">
                                        Delete
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" style="text-align:center;padding:2.5rem;color:var(--muted)">
                            No school years found.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($schoolYears->hasPages())
        <div style="padding:.75rem 1.25rem;border-top:1px solid var(--border)">
            {{ $schoolYears->links('pagination::simple-tailwind') }}
        </div>
    @endif
</div>
@endsection
