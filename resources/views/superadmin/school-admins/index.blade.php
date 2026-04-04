@extends('superadmin.layouts.app')

@section('title', 'School Admin Management')

@section('content')
<div class="page-header">
    <div class="page-header-left">
        <h1>School Admin Management</h1>
        <p>Manage all school administrator accounts and their schools.</p>
    </div>
    <a href="{{ route('superadmin.school-admins.create') }}" class="btn btn-primary">
        <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/>
        </svg>
        Add School Admin
    </a>
</div>

{{-- Search & Filter --}}
<form method="GET" action="{{ route('superadmin.school-admins.index') }}">
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
            <a href="{{ route('superadmin.school-admins.index') }}" class="btn btn-outline">Clear</a>
        @endif
    </div>
</form>

<div class="card">
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>School Name</th>
                    <th>Type</th>
                    <th>Region</th>
                    <th>Gender</th>
                    <th>Admin Name</th>
                    <th>Admin Email</th>
                    <th>Phone</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($schools as $school)
                    @php
                        $adminRole = $school->userSchoolRoles->first();
                        $admin = $adminRole?->user;
                    @endphp
                    <tr>
                        <td style="color:var(--muted)">{{ $schools->firstItem() + $loop->index }}</td>
                        <td>
                            <div style="font-weight:600;color:#f1f5f9">{{ $school->name }}</div>
                            @if($school->school_years && count($school->school_years))
                                <div style="font-size:.72rem;color:var(--muted);margin-top:2px">
                                    {{ implode(', ', $school->school_years) }}
                                </div>
                            @endif
                        </td>
                        <td>
                            @if($school->school_type)
                                <span class="badge badge-blue">{{ ucfirst($school->school_type) }}</span>
                            @else
                                <span style="color:var(--muted)">—</span>
                            @endif
                        </td>
                        <td>{{ $school->region ?: '—' }}</td>
                        <td>{{ $school->gender ? ucfirst($school->gender) : '—' }}</td>
                        <td>{{ $admin?->name ?: '—' }}</td>
                        <td style="font-size:.8rem">{{ $admin?->email ?: '—' }}</td>
                        <td>{{ $school->phone ?: '—' }}</td>
                        <td>
                            @if($school->is_active)
                                <span class="badge badge-success">Active</span>
                            @else
                                <span class="badge badge-danger">Inactive</span>
                            @endif
                        </td>
                        <td>
                            <div style="display:flex;gap:.4rem;align-items:center">
                                <a href="{{ route('superadmin.school-admins.show', $school) }}"
                                   class="btn btn-outline btn-sm">View</a>

                                <form method="POST"
                                      action="{{ route('superadmin.school-admins.toggle-status', $school) }}"
                                      style="display:inline">
                                    @csrf @method('PATCH')
                                    <button type="submit"
                                            class="btn btn-sm {{ $school->is_active ? 'btn-danger' : 'btn-success' }}"
                                            onclick="return confirm('{{ $school->is_active ? 'Deactivate' : 'Activate' }} this school admin?')">
                                        {{ $school->is_active ? 'Deactivate' : 'Activate' }}
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="10" style="text-align:center;padding:2.5rem;color:var(--muted)">
                            No school admins found.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($schools->hasPages())
        <div style="padding:.75rem 1.25rem;border-top:1px solid var(--border)">
            {{ $schools->links('pagination::simple-tailwind') }}
        </div>
    @endif
</div>
@endsection
