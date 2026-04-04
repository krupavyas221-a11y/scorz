@extends('superadmin.layouts.app')

@section('title', $config['label'] . ' — Master Data')

@push('styles')
<style>
    /* ── Tab bar ── */
    .md-tabs { display:flex; gap:2px; flex-wrap:wrap; margin-bottom:1.5rem;
               background:var(--surface); border:1px solid var(--border); border-radius:10px; padding:4px; }
    .md-tab  { padding:.38rem .9rem; border-radius:7px; font-size:.8rem; font-weight:600;
               color:var(--muted); text-decoration:none; transition:all .15s; white-space:nowrap; }
    .md-tab:hover  { color:var(--text); background:rgba(99,102,241,.08); }
    .md-tab.active { background:var(--accent); color:#fff; }

    /* ── Inline form card ── */
    .form-card { background:var(--surface); border:1px solid var(--border); border-radius:10px;
                 margin-bottom:1.25rem; overflow:hidden; }
    .form-card-header { padding:.75rem 1.1rem; border-bottom:1px solid var(--border);
                        display:flex; align-items:center; justify-content:space-between; }
    .form-card-header h3 { font-size:.88rem; font-weight:700; color:#f1f5f9; }
    .form-card-body { padding:1rem 1.1rem; display:flex; gap:.75rem; align-items:flex-end; flex-wrap:wrap; }
    .form-card-body .form-group { margin:0; flex:1; min-width:220px; }
    .form-card-body .actions { display:flex; gap:.5rem; }

    /* ── Toggle collapse ── */
    .form-collapsible { display:none; }
    .form-collapsible.open { display:block; }
</style>
@endpush

@section('content')

{{-- Page header --}}
<div class="page-header">
    <div class="page-header-left">
        <h1>Master Data</h1>
        <p>Define core system structures used across tests and assessments.</p>
    </div>
    <div style="display:flex;gap:.5rem">
        <a href="{{ route($config['route_prefix'] . '.export-csv') }}"
           class="btn btn-outline" title="Download CSV (opens in Excel)">
            <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round"
                      d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3"/>
            </svg>
            Export Excel
        </a>
        <a href="{{ route($config['route_prefix'] . '.export-pdf') }}"
           target="_blank" class="btn btn-outline">
            <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round"
                      d="M6.72 13.829c-.24.03-.48.062-.72.096m.72-.096a42.415 42.415 0 0110.56 0m-10.56 0L6.34 18m10.94-4.171c.24.03.48.062.72.096m-.72-.096L17.66 18m0 0l.229 2.523a1.125 1.125 0 01-1.12 1.227H7.231c-.662 0-1.18-.568-1.12-1.227L6.34 18m11.318 0h1.091A2.25 2.25 0 0021 15.75V9.456c0-1.081-.768-2.015-1.837-2.175a48.055 48.055 0 00-1.913-.247M6.34 18H5.25A2.25 2.25 0 013 15.75V9.456c0-1.081.768-2.015 1.837-2.175a48.041 48.041 0 011.913-.247m10.5 0a48.536 48.536 0 00-10.5 0m10.5 0V3.375c0-.621-.504-1.125-1.125-1.125h-8.25c-.621 0-1.125.504-1.125 1.125v3.659M18 10.5h.008v.008H18V10.5zm-3 0h.008v.008H15V10.5z"/>
            </svg>
            Export PDF
        </a>
        <button onclick="toggleForm()" id="add-btn" class="btn btn-primary">
            <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/>
            </svg>
            Add {{ $config['singular'] }}
        </button>
    </div>
</div>

{{-- Type tabs --}}
<div class="md-tabs">
    @foreach($allTypes as $tab)
        <a href="{{ route($tab['route']) }}"
           class="md-tab {{ request()->routeIs($tab['route']) ? 'active' : '' }}">
            {{ $tab['label'] }}
        </a>
    @endforeach
</div>

{{-- Add / Edit form --}}
@php $isEditing = !is_null($editing); @endphp

<div class="form-card form-collapsible {{ $isEditing ? 'open' : '' }}" id="entry-form-card">
    <div class="form-card-header">
        <h3 id="form-title">{{ $isEditing ? 'Edit '.$config['singular'] : 'Add '.$config['singular'] }}</h3>
        <button type="button" onclick="toggleForm()"
                style="background:none;border:none;color:var(--muted);cursor:pointer;font-size:1.1rem;line-height:1">✕</button>
    </div>
    <div class="form-card-body">
        @if($isEditing)
            <form method="POST"
                  action="{{ route($config['route_prefix'].'.update', $editing->id) }}"
                  style="display:contents">
                @csrf @method('PUT')
                <div class="form-group">
                    <label>{{ $config['singular'] }} Name <span style="color:var(--danger)">*</span></label>
                    <input type="text" name="name" class="form-control"
                           value="{{ old('name', $editing->name) }}"
                           placeholder="Enter name…" maxlength="100" autofocus>
                    @error('name')<div class="field-error">{{ $message }}</div>@enderror
                </div>
                <div class="actions">
                    <button type="submit" class="btn btn-primary">Save Changes</button>
                    <a href="{{ route($config['route_prefix'].'.index') }}" class="btn btn-outline">Cancel</a>
                </div>
            </form>
        @else
            <form method="POST"
                  action="{{ route($config['route_prefix'].'.store') }}"
                  style="display:contents">
                @csrf
                <div class="form-group">
                    <label>{{ $config['singular'] }} Name <span style="color:var(--danger)">*</span></label>
                    <input type="text" name="name" class="form-control"
                           value="{{ old('name') }}"
                           placeholder="Enter name…" maxlength="100"
                           id="add-name-input">
                    @error('name')<div class="field-error">{{ $message }}</div>@enderror
                </div>
                <div class="actions">
                    <button type="submit" class="btn btn-primary">Add</button>
                    <button type="button" onclick="toggleForm()" class="btn btn-outline">Cancel</button>
                </div>
            </form>
        @endif
    </div>
</div>

{{-- Search & filter --}}
<form method="GET" action="{{ route($config['route_prefix'].'.index') }}">
    <div class="toolbar">
        <div class="toolbar-search">
            <svg class="icon" width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round"
                      d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z"/>
            </svg>
            <input type="text" name="search" class="form-control" style="width:240px;padding-left:2.1rem"
                   placeholder="Search {{ strtolower($config['label']) }}…" value="{{ request('search') }}">
        </div>
        <select name="status" class="form-control" style="width:150px">
            <option value="">All Status</option>
            <option value="active"   {{ request('status') === 'active'   ? 'selected' : '' }}>Active</option>
            <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
        </select>
        <button type="submit" class="btn btn-primary">Filter</button>
        @if(request('search') || request('status'))
            <a href="{{ route($config['route_prefix'].'.index') }}" class="btn btn-outline">Clear</a>
        @endif
        <span style="margin-left:auto;color:var(--muted);font-size:.8rem">
            {{ $entries->total() }} {{ Str::plural(strtolower($config['singular']), $entries->total()) }}
        </span>
    </div>
</form>

{{-- Table --}}
<div class="card">
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th style="width:48px">#</th>
                    <th>Name</th>
                    <th style="width:110px">Status</th>
                    <th style="width:120px">Added</th>
                    <th style="width:230px">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($entries as $entry)
                    <tr>
                        <td style="color:var(--muted)">{{ $entries->firstItem() + $loop->index }}</td>
                        <td><span style="font-weight:600;color:#f1f5f9">{{ $entry->name }}</span></td>
                        <td>
                            @if($entry->is_active)
                                <span class="badge badge-success">Active</span>
                            @else
                                <span class="badge badge-danger">Inactive</span>
                            @endif
                        </td>
                        <td style="color:var(--muted);font-size:.79rem">
                            {{ $entry->created_at->format('d M Y') }}
                        </td>
                        <td>
                            <div style="display:flex;gap:.35rem;align-items:center">
                                {{-- Edit --}}
                                <a href="{{ route($config['route_prefix'].'.index') }}?edit={{ $entry->id }}"
                                   class="btn btn-outline btn-sm">Edit</a>

                                {{-- Toggle status --}}
                                <form method="POST"
                                      action="{{ route($config['route_prefix'].'.toggle-status', $entry->id) }}"
                                      style="display:inline">
                                    @csrf @method('PATCH')
                                    <button type="submit"
                                            class="btn btn-sm {{ $entry->is_active ? 'btn-danger' : 'btn-success' }}"
                                            onclick="return confirm('{{ $entry->is_active ? 'Deactivate' : 'Activate' }} {{ addslashes($entry->name) }}?')">
                                        {{ $entry->is_active ? 'Deactivate' : 'Activate' }}
                                    </button>
                                </form>

                                {{-- Delete --}}
                                <form method="POST"
                                      action="{{ route($config['route_prefix'].'.destroy', $entry->id) }}"
                                      style="display:inline">
                                    @csrf @method('DELETE')
                                    <button type="submit"
                                            class="btn btn-danger btn-sm"
                                            onclick="return confirm('Delete \'{{ addslashes($entry->name) }}\'? This cannot be undone.')">
                                        Delete
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" style="text-align:center;padding:2.5rem;color:var(--muted)">
                            No {{ strtolower($config['label']) }} found.
                            <a href="#" onclick="toggleForm();return false"
                               style="color:var(--accent);text-decoration:none;margin-left:.4rem">
                                Add the first one.
                            </a>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($entries->hasPages())
        <div style="padding:.75rem 1.25rem;border-top:1px solid var(--border)">
            {{ $entries->links('pagination::simple-tailwind') }}
        </div>
    @endif
</div>

@endsection

@push('scripts')
<script>
const formCard  = document.getElementById('entry-form-card');
const isEditing = @json(!is_null($editing));

function toggleForm() {
    const open = formCard.classList.toggle('open');
    if (open && !isEditing) document.getElementById('add-name-input')?.focus();
}

@if($errors->any() && !$isEditing)
    document.addEventListener('DOMContentLoaded', () => formCard.classList.add('open'));
@endif

@if($isEditing)
    document.addEventListener('DOMContentLoaded', () => {
        formCard.classList.add('open');
        formCard.scrollIntoView({ behavior: 'smooth', block: 'start' });
    });
@endif
</script>
@endpush
