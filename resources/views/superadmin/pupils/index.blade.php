@extends('superadmin.layouts.app')

@section('title', 'Pupils Management')

@section('content')
<div class="page-header">
    <div class="page-header-left">
        <h1>Pupils Management</h1>
        <p>Create and manage pupil records across all schools.</p>
    </div>
    <div style="display:flex;gap:.6rem">
        <a href="{{ route('superadmin.pupils.export-csv', request()->query()) }}"
           class="btn btn-outline" title="Export CSV">
            <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round"
                      d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3"/>
            </svg>
            CSV
        </a>
        <a href="{{ route('superadmin.pupils.print', request()->query()) }}"
           target="_blank" class="btn btn-outline" title="Print / PDF">
            <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round"
                      d="M6.72 13.829c-.24.03-.48.062-.72.096m.72-.096a42.415 42.415 0 0110.56 0m-10.56 0L6.34 18m10.94-4.171c.24.03.48.062.72.096m-.72-.096L17.66 18m0 0l.229 2.523a1.125 1.125 0 01-1.12 1.227H7.231c-.662 0-1.18-.568-1.12-1.227L6.34 18m11.318 0h1.091A2.25 2.25 0 0021 15.75V9.456c0-1.081-.768-2.015-1.837-2.175a48.055 48.055 0 00-1.913-.247M6.34 18H5.25A2.25 2.25 0 013 15.75V9.456c0-1.081.768-2.015 1.837-2.175a48.041 48.041 0 011.913-.247m10.5 0a48.536 48.536 0 00-10.5 0m10.5 0V3.375c0-.621-.504-1.125-1.125-1.125h-8.25c-.621 0-1.125.504-1.125 1.125v3.659M18 10.5h.008v.008H18V10.5zm-3 0h.008v.008H15V10.5z"/>
            </svg>
            PDF
        </a>
        <a href="{{ route('superadmin.pupils.create') }}" class="btn btn-primary">
            <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/>
            </svg>
            Add Pupil
        </a>
    </div>
</div>

{{-- Search & Filter --}}
<form method="GET" action="{{ route('superadmin.pupils.index') }}">
    <div class="toolbar">
        <div class="toolbar-search">
            <svg class="icon" width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z"/>
            </svg>
            <input type="text" name="search" class="form-control" style="width:240px;padding-left:2.1rem"
                   placeholder="Search name or pupil ID…" value="{{ request('search') }}">
        </div>
        <select name="school_id" class="form-control" style="width:200px">
            <option value="">All Schools</option>
            @foreach($schools as $s)
                <option value="{{ $s->id }}" {{ request('school_id') == $s->id ? 'selected' : '' }}>
                    {{ $s->name }}
                </option>
            @endforeach
        </select>
        <select name="status" class="form-control" style="width:150px">
            <option value="">All Status</option>
            <option value="active"   {{ request('status') === 'active'   ? 'selected' : '' }}>Active</option>
            <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
        </select>
        <button type="submit" class="btn btn-primary">Filter</button>
        @if(request()->hasAny(['search','school_id','status']))
            <a href="{{ route('superadmin.pupils.index') }}" class="btn btn-outline">Clear</a>
        @endif
    </div>
</form>

<div class="card">
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Pupil ID</th>
                    <th>Surname</th>
                    <th>First Name</th>
                    <th>Age</th>
                    <th>School</th>
                    <th>Year / Class</th>
                    <th>Teacher</th>
                    <th>SEN</th>
                    <th>Avg</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($pupils as $pupil)
                    <tr>
                        <td>
                            <span style="font-family:monospace;font-size:.82rem;color:#818cf8;font-weight:600">
                                {{ $pupil->pupil_id }}
                            </span>
                        </td>
                        <td style="font-weight:600;color:#f1f5f9">{{ $pupil->last_name }}</td>
                        <td>{{ $pupil->first_name }}</td>
                        <td style="color:var(--muted)">{{ $pupil->age ?? '—' }}</td>
                        <td style="font-size:.8rem">{{ $pupil->school?->name ?? '—' }}</td>
                        <td>
                            @if($pupil->year_group)
                                <span class="badge badge-blue">{{ $pupil->year_group }}</span>
                                @if($pupil->class_name)
                                    <span style="color:var(--muted);font-size:.8rem;margin-left:.25rem">{{ $pupil->class_name }}</span>
                                @endif
                            @else
                                <span style="color:var(--muted)">—</span>
                            @endif
                        </td>
                        <td style="font-size:.8rem">{{ $pupil->teacher?->name ?? '—' }}</td>
                        <td>
                            @if($pupil->sen !== 'none')
                                <span class="badge badge-blue" style="font-size:.7rem">{{ $pupil->sen_label }}</span>
                            @else
                                <span style="color:var(--muted);font-size:.78rem">None</span>
                            @endif
                        </td>
                        <td>
                            @if($pupil->include_in_averages)
                                <span class="badge badge-success" style="font-size:.7rem">Yes</span>
                            @else
                                <span class="badge badge-gray" style="font-size:.7rem">No</span>
                            @endif
                        </td>
                        <td>
                            @if($pupil->is_active)
                                <span class="badge badge-success">Active</span>
                            @else
                                <span class="badge badge-danger">Inactive</span>
                            @endif
                        </td>
                        <td>
                            <div style="display:flex;gap:.35rem;flex-wrap:wrap">
                                <a href="{{ route('superadmin.pupils.edit', $pupil) }}"
                                   class="btn btn-outline btn-sm">Edit</a>

                                <form method="POST"
                                      action="{{ route('superadmin.pupils.toggle-status', $pupil) }}">
                                    @csrf @method('PATCH')
                                    <button type="submit"
                                            class="btn btn-sm {{ $pupil->is_active ? 'btn-danger' : 'btn-success' }}"
                                            onclick="return confirm('{{ $pupil->is_active ? 'Deactivate' : 'Activate' }} this pupil?')">
                                        {{ $pupil->is_active ? 'Deactivate' : 'Activate' }}
                                    </button>
                                </form>

                                <form method="POST"
                                      action="{{ route('superadmin.pupils.destroy', $pupil) }}">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger"
                                            onclick="return confirm('Delete {{ addslashes($pupil->full_name) }}? This cannot be undone.')">
                                        Delete
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="11" style="text-align:center;padding:2.5rem;color:var(--muted)">
                            No pupils found.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($pupils->hasPages())
        <div style="padding:.75rem 1.25rem;border-top:1px solid var(--border)">
            {{ $pupils->links('pagination::simple-tailwind') }}
        </div>
    @endif
</div>
@endsection
